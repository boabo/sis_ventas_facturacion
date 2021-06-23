CREATE OR REPLACE FUNCTION vef.ft_replicar_facturas_carga_pendientes (
  p_estado varchar,
  p_fecha_inicio date,
  p_fecha_fin date
)
RETURNS varchar AS
$body$
DECLARE
	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_movimiento_entidad	integer;
    v_existe				numeric;
    v_nombre_agencia		varchar;
    v_id_moneda				integer;

    /******Variables para ERP******/
    v_existencia_cliente	integer;
    v_hora_estimada			time;
    v_tipo_cambio			numeric;
    v_exento				numeric;
    v_monto_total			numeric;
    v_moneda_base			integer;
    v_id_punto_venta		integer;
    v_id_cliente			integer;
    v_id_venta				integer;
    v_codigo_proceso		varchar;
    v_num_ven				varchar;
    v_id_usuario			integer;
    v_id_periodo			integer;
    v_id_sucursal			integer;
    v_num_tramite			varchar;
    v_id_proceso_wf			integer;
    v_id_estado_wf			integer;
    v_codigo_estado			varchar;
    v_id_gestion			integer;
    v_id_tipo_estado_sig	integer;
    v_id_estado_wf_sig		integer;
    v_id_funcionario_sig	integer;
    v_venta					record;
    v_estado_finalizado		integer;
    v_tabla					varchar;
    v_codigo_estado_siguiente varchar;
    v_es_fin				varchar;
    v_acceso_directo 		varchar;
    v_clase 				varchar;
    v_parametros_ad 		varchar;
    v_tipo_noti 			varchar;
    v_titulo 				varchar;
    v_id_depto				integer;
    v_obs					text;
    v_id_estado_actual		integer;
    v_cant_id_usuario		numeric;
    v_cant_id_usuario_rol	numeric;
    v_medio_pago			record;
    v_id_moneda_fp			integer;
    v_id_auxiliar_fp		integer;
    v_id_medio_pago			integer;
    v_monto_fp				numeric;
    v_acumulado_fp			numeric;
    v_id_venta_forma_pago	integer;
    v_respaldo				record;
    v_res					varchar;
    v_registros				record;
    v_nro_autorizacion		varchar;
	/******************************/

    /*Variables conexion*/
    v_conexion varchar;
    v_cadena_cnx	varchar;
    v_sinc	varchar;
    v_consulta	varchar;
    v_id_factura	integer;
    v_res_cone	varchar;
    v_datos_carga	record;
    v_cajero	varchar;
    v_consulta_inser varchar;
    v_observaciones	varchar;
    v_host varchar;
    v_puerto varchar;
    v_dbname varchar;
    p_user varchar;
    v_password varchar;
    v_semilla	varchar;

    v_cuenta_usu	varchar;
    v_pass_usu		varchar;
    v_json_data		varchar;
    v_id_tipo_estado		 integer;
    v_venta_anu			 record;
    v_id_funcionario_inicio	 integer;
    v_desc_funcionario		varchar;
    v_usuario_nuevo		varchar;
    v_cant_id_usuario_rol_normal numeric;
    v_id_dosificacion	integer;
    /********************/

BEGIN
	  v_nombre_funcion = 'vef.ft_replicar_facturas_carga_pendientes ';

      if (p_estado = 'validas') then

       FOR v_datos_carga in ( select fv.fecha,
                                     fv.nro_factura,
                                     fv.estado,
                                     fv.nit,
                                     fv.razon_social,
                                     fv.importe_total,
                                     fv.id_funcionario,
                                     fv.id_origen,
                                     fv.codigo_control,
                                     fv.nro_autorizacion,
                                     fv.moneda,
                                     fv.cod_medio_pago,
                                     fv.cod_auxiliar,
                                     fv.cod_punto_venta
                              from vef.tfacturas_pendientes_carga_validas fv
                              where fv.fecha between p_fecha_inicio and p_fecha_fin
                              and fv.id_origen not in (select ven.id_sistema_origen
                                                      from vef.tventa ven
                                                      where ven.tipo_factura = 'carga'
                                                      and ven.estado_reg = 'activo'
                                                      and ven.fecha between p_fecha_inicio and p_fecha_fin)
                              order by fv.id_origen ASC
                              limit 100
      	) LOOP

        /*Inserccion de facturas carga a tventa y tventa_forma_pago*/

        /*Verificamos si Existe el Cliente para obtener el ID*/
        select count (cli.id_cliente) into v_existencia_cliente
        from vef.tcliente cli
        where trim(cli.nit) = trim(v_datos_carga.nit)
        group by cli.id_cliente
        order by cli.id_cliente desc
        limit 1;
        /****************************************************/

        /*Recuperamos el id_usuario desde el id_funcionario que nos envian*/

        /*Verificaremos si el usuario solo tiene una cuenta*/
        select count(usu.id_usuario)
               into
               v_cant_id_usuario
        from orga.vfuncionario fun
        inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
        where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER;
        /***************************************************/

        /*Cambiando la condicion porq en Carga los tecnicos pueden emitir facturas por tanto puede que no exista
        un cajero de turno para emision de facturas*/

        if (v_cant_id_usuario = 1) then

        	select usu.id_usuario
            	   into
                   v_id_usuario
            from orga.vfuncionario fun
            inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
            where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER;

        elsif (v_cant_id_usuario > 1) then

        	/*Si el usuario tiene mas de dos cuentas tomamos el usuario con rol de cajero*/
                select count(usu.id_usuario)
                       into
                       v_cant_id_usuario_rol
                from orga.vfuncionario fun
                inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
                inner join segu.tusuario_rol usurol on usurol.id_usuario = usu.id_usuario
                inner join segu.trol rol on rol.id_rol = usurol.id_rol and rol.estado_reg = 'activo'
                where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER and rol.rol = 'VEF - Cajero' and usurol.estado_reg = 'activo';

                if (v_cant_id_usuario_rol = 1) then

                 	select usu.id_usuario
                           into
                           v_id_usuario
                    from orga.vfuncionario fun
                    inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
                    inner join segu.tusuario_rol usurol on usurol.id_usuario = usu.id_usuario
                    inner join segu.trol rol on rol.id_rol = usurol.id_rol and rol.estado_reg = 'activo'
                    where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER and rol.rol = 'VEF - Cajero' and usurol.estado_reg = 'activo';

                    v_usuario_nuevo = 'NO';

                elsif (v_cant_id_usuario_rol > 1) then

                	select usu.id_usuario
                           into
                           v_id_usuario
                    from orga.vfuncionario fun
                    inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
                    inner join segu.tusuario_rol usurol on usurol.id_usuario = usu.id_usuario
                    inner join segu.trol rol on rol.id_rol = usurol.id_rol and rol.estado_reg = 'activo'
                    where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER and rol.rol = 'VEF - Cajero' and usurol.estado_reg = 'activo'
                    order by usu.id_usuario desc
                    limit 1;

                    v_usuario_nuevo = 'NO';

                elsif (v_cant_id_usuario_rol = 0) then

                	select count(usu.id_usuario)
                       into
                       v_cant_id_usuario_rol_normal
                from orga.vfuncionario fun
                inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
                where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER;

                    if (v_cant_id_usuario_rol_normal = 1) then

                        select usu.id_usuario
                               into
                               v_id_usuario
                        from orga.vfuncionario fun
                        inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
                        where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER;

                        v_usuario_nuevo = 'NO';

                    elsif (v_cant_id_usuario_rol_normal > 1 ) then

                        select usu.id_usuario
                               into
                               v_id_usuario
                        from orga.vfuncionario fun
                        inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
                        where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER
                        order by usu.id_usuario desc
                        limit 1;

                        v_usuario_nuevo = 'NO';


                    end if;
                end if;

        	 elsif (v_cant_id_usuario = 0) then

             /*Comentando esta para porque se crearan todos los usuarios de los funcionarios*/
             select fun.desc_funcionario1
                   into
                   v_desc_funcionario
            from orga.vfuncionario fun
            where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER;

             raise exception 'EL funcionario % no tiene una cuenta de usuario en ERP. Favor coordinar con encargados del ERP para su registro correspondiente',v_desc_funcionario;

        end if;
        /******************************************************************/

        if (v_existencia_cliente = 0 or v_existencia_cliente is null) then
         	INSERT INTO vef.tcliente
            	(
                	id_usuario_reg,
                    fecha_reg,
                    estado_reg,
                    nombre_factura,
                    nit
                )
            VALUES (
                  	v_id_usuario,
                  	now(),
                  	'activo',
                  	v_datos_carga.razon_social,
                  	v_datos_carga.nit
                ) returning id_cliente into v_id_cliente;
        else
            select cli.id_cliente into v_id_cliente
            from vef.tcliente cli
            where trim(cli.nit) = trim(v_datos_carga.nit)
            order by cli.id_cliente desc
            limit 1;
        end if;
        /***********************************************************/

        /*Obtenemos la hora de emision*/
        select to_char(current_timestamp, 'HH12:MI:SS')
        into v_hora_estimada;
        /******************************/

        /*****Aqui obtenemos el tipo de cambio para esa fecha*******/
        select tc.oficial
         	   into
               v_tipo_cambio
        from param.ttipo_cambio tc
        where tc.id_moneda = 2 and tc.fecha = v_datos_carga.fecha::Date;
        /***********************************************************/

        /****Aqui la moneda base para la emision de la factura*******/
        select mon.id_moneda into v_id_moneda
        from param.tmoneda mon
        where mon.codigo_internacional = v_datos_carga.moneda;
        /************************************************************/

        /****Aqui recuperamos la moneda base para realizar la conversion*/
        select mon.id_moneda
               into
               v_moneda_base
        from param.tmoneda mon
        where mon.tipo_moneda = 'base';
        /****************************************************************/

        /***Si la moneda llega en dolar hacer la conversion*********/
        v_exento = 0; ---Ojo Carga no maneja monto Exento por el momento estara como 0

        if (v_id_moneda = 2) then
            v_monto_total = param.f_convertir_moneda(v_id_moneda,v_moneda_base,v_datos_carga.importe_total,v_datos_carga.fecha::date,'O',2,NULL,'si');
        else
            v_monto_total = v_datos_carga.importe_total;
        end if;
        /***********************************************************/

        /**********Recuperamos el id punto de venta en base al codigo que nos envia el sistema de carga*********/
        select pv.id_punto_venta,
               pv.id_sucursal
        	   into
               v_id_punto_venta,
               v_id_sucursal
        from vef.tpunto_venta pv
        where pv.codigo = v_datos_carga.cod_punto_venta and pv.tipo = 'carga';

        if (v_id_punto_venta is null) then
        	raise exception 'No existe el punto de venta favor verificar el codigo %', v_datos_carga.cod_punto_venta;
        end if;
        /*******************************************************************************************************/

        select id_gestion into v_id_gestion
        from param.tgestion
        where gestion = extract(year from (v_datos_carga.fecha::date))::integer;



        /*Registramos la factura en el wf*/
        select nextval('vef.tventa_id_venta_seq') into v_id_venta;
            v_codigo_proceso = 'VEN-' || v_id_venta;

        SELECT
          ps_num_tramite ,
          ps_id_proceso_wf ,
          ps_id_estado_wf ,
          ps_codigo_estado
        into
          v_num_tramite,
          v_id_proceso_wf,
          v_id_estado_wf,
          v_codigo_estado

        FROM wf.f_inicia_tramite(
            v_id_usuario,
            NULL,
            NULL,
            v_id_gestion,
            'VEN',
            NULL,
            NULL,
            NULL,
            v_codigo_proceso);
        /*********************************/


       	/*************************Obtenemso el correlativo de la venta***********************************/
        select id_periodo into v_id_periodo from
          param.tperiodo per
        where per.fecha_ini <= now()::date
              and per.fecha_fin >=  now()::date
        limit 1 offset 0;

        v_num_ven =   param.f_obtener_correlativo(
        'VEN',
        v_id_periodo,-- par_id,
        NULL, --id_uo
        NULL,    -- id_depto
        v_id_usuario,
        'VEF',
        NULL,
        0,
        0,
        'vef.tpunto_venta',
        v_id_punto_venta,
        v_datos_carga.cod_punto_venta
        );


        select dosi.id_dosificacion into v_id_dosificacion
        from vef.tdosificacion dosi
        where trim(dosi.nroaut) = trim(v_datos_carga.nro_autorizacion::varchar);

        /*Se procede a la inserccion del registro en la tabla vef.tventa*/
        insert into vef.tventa(
                      id_venta,--1
                      id_cliente,--2 /*Podemos poner aqui la condicion para ir insertando o no*/
                      id_sucursal,--3 /*Nos llegaria por el servicio*/
                      id_proceso_wf,--4 /*Se recupera para el nro de tramite*/
                      id_estado_wf,--5 /*Se recupera para el estado que se encuentra*/
                      estado_reg,--6
                      nro_tramite,--7 /*Recuperamos en la variable*/
                      a_cuenta,--8
                      fecha_estimada_entrega,--9
                      usuario_ai,--10
                      fecha_reg,--11
                      id_usuario_reg,--12
                      id_usuario_ai,--13
                      id_usuario_mod,--14
                      fecha_mod,--15
                      estado,--16
                      id_punto_venta,--17 /*Llegaria desde el servicio*/
                      id_vendedor_medico,--18
                      porcentaje_descuento,--19
                      comision,--20
                      observaciones,--21
                      correlativo_venta,--22
                      tipo_factura,--23
                      fecha,--24
                      nro_factura,--25
                      id_dosificacion,--26
                      excento,--27

                      id_moneda,--28
                      transporte_fob,--29
                      seguros_fob,--30
                      otros_fob,--31
                      transporte_cif,--32
                      seguros_cif,--33
                      otros_cif,--34
                      tipo_cambio_venta,--35
                      valor_bruto,--36
                      descripcion_bulto,--37
                      nit,--38
                      nombre_factura,--39
                      id_cliente_destino,--40
                      hora_estimada_entrega,--41
                      tiene_formula,--42
                      forma_pedido,--43
                      total_venta,--44
                      total_venta_msuc,--45
                      cod_control,--46
                      id_sistema_origen,--47
                      id_usuario_cajero--48
                    ) values(
                      v_id_venta,--1
                      v_id_cliente,--2
                      v_id_sucursal,--3
                      v_id_proceso_wf,--4
                      v_id_estado_wf,--5
                      'activo',--6
                      v_num_tramite,--7
                      0,--8
                      v_datos_carga.fecha::date,--9
                      NULL,--10
                      now(),--11
                      v_id_usuario,--12
                      NULL,--13
                      null,--14
                      null,--15
                      v_codigo_estado,--16
                      v_id_punto_venta,--17
                      null,--18
                      0,--19
                      0,--20
                      '',--21
                      v_num_ven,--22
                      'carga',--23
                      v_datos_carga.fecha::date,--24
                      v_datos_carga.nro_factura::integer,--25
                      v_id_dosificacion,--26
                      v_exento,--27--Excento por el momento 0
                      v_id_moneda,--28
                      0,--29
                      0,--30
                      0,--31
                      0,--32
                      0,--33
                      0,--34
                      0,--35
                      0,--36
                      '',--37
                      REPLACE(v_datos_carga.nit,' ',''),--38
                      upper(v_datos_carga.razon_social),--39
                      NULL,--40
                      v_hora_estimada,--41
                      'no',--42
                      'carga',--43
                      v_monto_total,--44
					  v_monto_total,--45
					  v_datos_carga.codigo_control,--46,
                      v_datos_carga.id_origen::integer,--47
                      v_id_usuario--48
                    );
        /****************************************************************/

        /*En caso que el id_usuario sea Null debemos almacenarlo en una tabla para posible regularizacion*/
         if (v_id_usuario is null) then
              insert into vef.tfacturas_carga_observadas(
                        id_venta,--1
                        id_funcionario,--2
                        estado,--3
                        observacion--4
                      ) values(
                        v_id_venta,--1
                        v_datos_carga.id_funcionario::INTEGER,--2
                        'pendiente',--3
                        'El Funcionario no tiene una cuenta de usuario en el Sistema ERP'--4
                      );
          elsif (COALESCE (v_cant_id_usuario_rol,0) > 1) then
               insert into vef.tfacturas_carga_pendiente(
                        id_venta,--1
                        id_funcionario,--2
                        estado,--3
                        observacion--4
                      ) values(
                        v_id_venta,--1
                        v_datos_carga.id_funcionario::INTEGER,--2
                        'pendiente',--3
                        v_observaciones--4
                      );
          end if;
        /*************************************************************************************************/

        /*Recuperamos el id_tipo_estado y el id_estado_wf*/
         select
            ew.id_tipo_estado ,
            ew.id_estado_wf,
            ew.id_funcionario
          into
            v_id_tipo_estado_sig,
            v_id_estado_wf_sig,
            v_id_funcionario_sig
          from wf.testado_wf ew
            inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
          where ew.id_estado_wf =  v_id_estado_wf;
         /********************************************************/

         /*Obtenemos los campos de venta el id_entidad y el tipo_base*/
          select v.*,s.id_entidad,tv.tipo_base into v_venta
          from vef.tventa v
            inner join vef.tsucursal s on s.id_sucursal = v.id_sucursal
            inner join vef.tcliente c on c.id_cliente = v.id_cliente
            inner join vef.ttipo_venta tv on tv.codigo = v.tipo_factura and tv.estado_reg = 'activo'
          where v.id_proceso_wf = v_id_proceso_wf;
          /***********************************************************/

          /*Obtenemos el id del estado finalizado*/
          v_estado_finalizado = (v_id_tipo_estado_sig+1);
          /****************************************/

          /*Obtenemnos el codigo finalizado*/
          select te.codigo into v_codigo_estado
          from wf.ttipo_estado te
          where te.id_tipo_estado=v_estado_finalizado;
          /******************************************/


          /*Obtenemnos el codigo finalizado*/
          select te.codigo into v_codigo_estado
          from wf.ttipo_estado te
          where te.id_tipo_estado=v_estado_finalizado;
          /******************************************/

          /*Creamos un nuevo parametro*/
          v_tabla = pxp.f_crear_parametro(ARRAY[	'_nombre_usuario_ai',
          '_id_usuario_ai',
          'id_venta',
          'tipo_factura',
          'codigo_estado'],
                                          ARRAY[	'',
                                          '',
                                          v_venta.id_venta::varchar,
                                          v_venta.tipo_factura,
                                          v_codigo_estado],
                                          ARRAY[	'varchar',
                                          'integer',
                                          'integer',
                                          'varchar',
                                          'varchar']
          );


        /*******************************************************************************************/

         /*Obtenemos el codigo finalizado y fin*/
          select
            te.codigo,te.fin
          into
            v_codigo_estado_siguiente,v_es_fin
          from wf.ttipo_estado te
          where te.id_tipo_estado = v_estado_finalizado;
          /*********************************************************************/

          --configurar acceso directo para la alarma
          v_acceso_directo = '';
          v_clase = '';
          v_parametros_ad = '';
          v_tipo_noti = 'notificacion';
          v_titulo  = 'Visto Bueno';
          v_obs = '----';

          -- hay que recuperar el supervidor que seria el estado inmediato,...
          v_id_estado_actual =  wf.f_registra_estado_wf(v_estado_finalizado /*tengpo*/,
                                                        v_id_funcionario_sig/*recuperar*/,
                                                        v_id_estado_wf /*tengo*/,
                                                        v_id_proceso_wf/*tengo*/,
                                                        v_id_usuario,
                                                        NULL,
                                                        NULL,
                                                        v_id_depto,
                                                        v_obs,
                                                        v_acceso_directo ,
                                                        v_clase,
                                                        v_parametros_ad,
                                                        v_tipo_noti,
                                                        v_titulo);

           /*Verificar que hace*/
           IF  vef.f_fun_inicio_venta_wf(v_id_usuario,
                                        NULL,
                                        NULL,
                                        v_id_estado_actual,
                                        v_id_proceso_wf,
                                        v_codigo_estado_siguiente) THEN

          END IF;
          /************************************/


          /*Aqui Insertamos los Medios de Pago que nos enviara Carga*/
          v_acumulado_fp = 0;

          --v_json_data = '['||v_parametros.json_venta_forma_pago||']';


          --for v_medio_pago in (select *
            --                  from json_populate_recordset(null::vef.medio_pago_venta,v_json_data::json))loop

           /*Recuperamos el id moneda en base al codigo que nos manda el servicio*/
           select mon.id_moneda
                  into
                  v_id_moneda_fp
           from param.tmoneda mon
           where mon.codigo_internacional = v_datos_carga.moneda;
           /**********************************************************************/

           /*Recuperamos el id auxiliar en base al codigo que nos envia el servicio*/
           IF (v_datos_carga.cod_auxiliar is not null) then
             select aux.id_auxiliar into
                    v_id_auxiliar_fp
             from conta.tauxiliar aux
             where (aux.codigo_auxiliar = v_datos_carga.cod_auxiliar OR aux.cod_antiguo = v_datos_carga.cod_auxiliar);
           else
           	v_id_auxiliar_fp = null;
           end if;
           /************************************************************************/

           /*Recuperamos el id_medio_pago en base al codigo que nos envia el servicio*/
           select mp.id_medio_pago_pw
           	      into
                  v_id_medio_pago
           from obingresos.tmedio_pago_pw mp
           where mp.mop_code = v_datos_carga.cod_medio_pago;
           /**************************************************************************/

           if (v_id_medio_pago is null) then
              insert into vef.tfacturas_carga_observadas(
                      id_venta,--1
                      id_funcionario,--2
                      estado,--3
                      observacion--4
                    ) values(
                      v_id_venta,--1
                      NULL,--2
                      'fp_error',--3
                      'El medio de pago no esta parametrizado en la tabla obingresos.tmedio_pago_pw'--4
                    );
           end if;

           /*Aqui realizamos la inserccion de los medios de pago*/
           insert into vef.tventa_forma_pago(
                                              usuario_ai,--1
                                              fecha_reg,--2
                                              id_usuario_reg,--3
                                              id_usuario_ai,--4
                                              estado_reg,--5
                                              id_venta,--6
                                              monto_transaccion,--7
                                              monto,--8
                                              cambio,--9
                                              monto_mb_efectivo,--10
                                              numero_tarjeta,--11
                                              codigo_tarjeta,--12
                                              id_auxiliar,--13
                                              tipo_tarjeta,--14
                                              id_medio_pago,--15
                                              id_moneda--16
                                            )
                                      values(
                                              NULL,--1
                                              now(),--2
                                              v_id_usuario,--3
                                              NULL,--4
                                              'activo',--5
                                              v_id_venta,--6
                                              v_datos_carga.importe_total,--7
                                              0,--8
                                              0,--9
                                              0,--10
                                              '',--11
                                              '',--12
                                              v_id_auxiliar_fp,--13
                                              NULL,--14
                                              v_id_medio_pago,--15
                                              v_id_moneda_fp--16
                                            )returning id_venta_forma_pago into v_id_venta_forma_pago;

          /*********************************************************************************************/

          /*Aqui realizamos la conversion de la moneda en caso que llegue en dolar*/
          if (v_id_moneda != v_id_moneda_fp) then
                v_monto_fp = param.f_convertir_moneda(v_id_moneda_fp,v_id_moneda,v_datos_carga.importe_total,v_datos_carga.fecha::date,'O',2,NULL,'si');
          else
            	v_monto_fp = v_datos_carga.importe_total;
          end if;
          /************************************************************************/

          /*Realizamos la Actualizacion de la forma de pago con la nueva conversion*/
          update vef.tventa_forma_pago set
              monto = v_monto_fp,
              cambio = (case when (v_monto_fp + v_acumulado_fp - v_monto_total::numeric) > 0 then
                (v_monto_fp + v_acumulado_fp - v_monto_total::numeric)
                        else
                          0
                        end),
              monto_mb_efectivo = (case when (v_monto_fp + v_acumulado_fp - v_monto_total::numeric) > 0 then
                (v_monto_fp - (v_monto_fp + v_acumulado_fp - v_monto_total::numeric))
                                   else
                                     v_monto_fp
                                   end)
            where id_venta_forma_pago = v_id_venta_forma_pago;
            v_acumulado_fp = v_acumulado_fp + v_monto_fp;
          /*************************************************************************/
         -- end loop;
          /**********************************************************/

        end loop;

----------------------------------------------------------------AQUI LAS ANULADAS----------------------------------------------------------------------
      elsif (p_estado = 'anuladas') then

        for v_datos_carga in (	select fv.fecha,
                                       fv.nro_factura,
                                       fv.estado,
                                       fv.nit,
                                       fv.razon_social,
                                       fv.importe_total,
                                       fv.id_funcionario,
                                       fv.id_origen,
                                       fv.codigo_control,
                                       fv.nro_autorizacion,
                                       fv.moneda,
                                       fv.cod_medio_pago,
                                       fv.cod_auxiliar,
                                       fv.cod_punto_venta
                                from vef.tfacturas_pendientes_carga_anuladas fv
                                where fv.fecha between p_fecha_inicio and p_fecha_fin
                                and fv.id_origen not in (select ven.id_sistema_origen
                                                        from vef.tventa ven
                                                        where ven.tipo_factura = 'carga'
                                                        and ven.estado_reg = 'activo'
                                                        and ven.estado = 'anulado'
                                                        and ven.fecha between p_fecha_inicio and p_fecha_fin
                              )
                              order by fv.id_origen ASC
                              limit 100
        )
        LOOP


        select count(ven.id_venta)
        into v_existe
        from vef.tventa ven
        where ven.id_sistema_origen = v_datos_carga.id_origen and ven.estado_reg = 'activo' and ven.tipo_factura = 'carga';

        /*Aqui insertamos la factura a vef.tventa en caso que no exista para la anulacion*/
          if (v_existe = 0) then

          /*Inserccion de facturas carga a tventa y tventa_forma_pago*/

          /*Verificamos si Existe el Cliente para obtener el ID*/
          select count (cli.id_cliente) into v_existencia_cliente
          from vef.tcliente cli
          where trim(cli.nit) = trim(v_datos_carga.nit)
          group by cli.id_cliente
          order by cli.id_cliente desc
          limit 1;
          /****************************************************/

          /*Recuperamos el id_usuario desde el id_funcionario que nos envian*/

          /*Verificaremos si el usuario solo tiene una cuenta*/
          select count(usu.id_usuario)
                 into
                 v_cant_id_usuario
          from orga.vfuncionario fun
          inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
          where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER;
          /***************************************************/

          /*Cambiando la condicion porq en Carga los tecnicos pueden emitir facturas por tanto puede que no exista
        un cajero de turno para emision de facturas*/

        if (v_cant_id_usuario = 1) then

        	select usu.id_usuario
            	   into
                   v_id_usuario
            from orga.vfuncionario fun
            inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
            where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER;

        elsif (v_cant_id_usuario > 1) then

        	/*Si el usuario tiene mas de dos cuentas tomamos el usuario con rol de cajero*/
                select count(usu.id_usuario)
                       into
                       v_cant_id_usuario_rol
                from orga.vfuncionario fun
                inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
                inner join segu.tusuario_rol usurol on usurol.id_usuario = usu.id_usuario
                inner join segu.trol rol on rol.id_rol = usurol.id_rol and rol.estado_reg = 'activo'
                where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER and rol.rol = 'VEF - Cajero' and usurol.estado_reg = 'activo';

                if (v_cant_id_usuario_rol = 1) then

                 	select usu.id_usuario
                           into
                           v_id_usuario
                    from orga.vfuncionario fun
                    inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
                    inner join segu.tusuario_rol usurol on usurol.id_usuario = usu.id_usuario
                    inner join segu.trol rol on rol.id_rol = usurol.id_rol and rol.estado_reg = 'activo'
                    where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER and rol.rol = 'VEF - Cajero' and usurol.estado_reg = 'activo';

                    v_usuario_nuevo = 'NO';

                elsif (v_cant_id_usuario_rol > 1) then

                	select usu.id_usuario
                           into
                           v_id_usuario
                    from orga.vfuncionario fun
                    inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
                    inner join segu.tusuario_rol usurol on usurol.id_usuario = usu.id_usuario
                    inner join segu.trol rol on rol.id_rol = usurol.id_rol and rol.estado_reg = 'activo'
                    where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER and rol.rol = 'VEF - Cajero' and usurol.estado_reg = 'activo'
                    order by usu.id_usuario desc
                    limit 1;

                    v_usuario_nuevo = 'NO';

                elsif (v_cant_id_usuario_rol = 0) then

                	select count(usu.id_usuario)
                       into
                       v_cant_id_usuario_rol_normal
                from orga.vfuncionario fun
                inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
                where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER;

                    if (v_cant_id_usuario_rol_normal = 1) then

                        select usu.id_usuario
                               into
                               v_id_usuario
                        from orga.vfuncionario fun
                        inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
                        where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER;

                        v_usuario_nuevo = 'NO';

                    elsif (v_cant_id_usuario_rol_normal > 1 ) then

                        select usu.id_usuario
                               into
                               v_id_usuario
                        from orga.vfuncionario fun
                        inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
                        where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER
                        order by usu.id_usuario desc
                        limit 1;

                        v_usuario_nuevo = 'NO';


                    end if;
                end if;

        	 elsif (v_cant_id_usuario = 0) then

             /*Comentando esta para porque se crearan todos los usuarios de los funcionarios*/
             select fun.desc_funcionario1
                   into
                   v_desc_funcionario
            from orga.vfuncionario fun
            where fun.id_funcionario = v_datos_carga.id_funcionario::INTEGER;

             raise exception 'EL funcionario % no tiene una cuenta de usuario en ERP. Favor coordinar con encargados del ERP para su registro correspondiente',v_desc_funcionario;

        end if;
        /******************************************************************/

          if (v_existencia_cliente = 0 or v_existencia_cliente is null) then
              INSERT INTO vef.tcliente
                  (
                      id_usuario_reg,
                      fecha_reg,
                      estado_reg,
                      nombre_factura,
                      nit
                  )
              VALUES (
                      v_id_usuario,
                      now(),
                      'activo',
                      v_datos_carga.razon_social,
                      v_datos_carga.nit
                  ) returning id_cliente into v_id_cliente;
          else
              select cli.id_cliente into v_id_cliente
              from vef.tcliente cli
              where trim(cli.nit) = trim(v_datos_carga.nit)
              order by cli.id_cliente desc
              limit 1;
          end if;
          /***********************************************************/

          /*Obtenemos la hora de emision*/
          select to_char(current_timestamp, 'HH12:MI:SS')
          into v_hora_estimada;
          /******************************/

          /*****Aqui obtenemos el tipo de cambio para esa fecha*******/
          select tc.oficial
                 into
                 v_tipo_cambio
          from param.ttipo_cambio tc
          where tc.id_moneda = 2 and tc.fecha = v_datos_carga.fecha::Date;
          /***********************************************************/

          /****Aqui la moneda base para la emision de la factura*******/
          select mon.id_moneda into v_id_moneda
          from param.tmoneda mon
          where mon.codigo_internacional = v_datos_carga.moneda;
          /************************************************************/

          /****Aqui recuperamos la moneda base para realizar la conversion*/
          select mon.id_moneda
                 into
                 v_moneda_base
          from param.tmoneda mon
          where mon.tipo_moneda = 'base';
          /****************************************************************/

          /***Si la moneda llega en dolar hacer la conversion*********/
          v_exento = 0; ---Ojo Carga no maneja monto Exento por el momento estara como 0

          if (v_id_moneda = 2) then
              v_monto_total = param.f_convertir_moneda(v_id_moneda,v_moneda_base,v_datos_carga.importe_total,v_datos_carga.fecha::date,'O',2,NULL,'si');
          else
              v_monto_total = v_datos_carga.importe_total;
          end if;
          /***********************************************************/

          /**********Recuperamos el id punto de venta en base al codigo que nos envia el sistema de carga*********/
          select pv.id_punto_venta,
                 pv.id_sucursal
                 into
                 v_id_punto_venta,
                 v_id_sucursal
          from vef.tpunto_venta pv
          where pv.codigo = v_datos_carga.cod_punto_venta and pv.tipo = 'carga';

          if (v_id_punto_venta is null) then
              raise exception 'No existe el punto de venta favor verificar el codigo %', v_datos_carga.cod_punto_venta;
          end if;
          /*******************************************************************************************************/

          select id_gestion into v_id_gestion
          from param.tgestion
          where gestion = extract(year from (v_datos_carga.fecha::date))::integer;



          /*Registramos la factura en el wf*/
          select nextval('vef.tventa_id_venta_seq') into v_id_venta;
              v_codigo_proceso = 'VEN-' || v_id_venta;

          SELECT
            ps_num_tramite ,
            ps_id_proceso_wf ,
            ps_id_estado_wf ,
            ps_codigo_estado
          into
            v_num_tramite,
            v_id_proceso_wf,
            v_id_estado_wf,
            v_codigo_estado

          FROM wf.f_inicia_tramite(
              v_id_usuario,
              NULL,
              NULL,
              v_id_gestion,
              'VEN',
              NULL,
              NULL,
              NULL,
              v_codigo_proceso);
          /*********************************/


          /*************************Obtenemso el correlativo de la venta***********************************/
          select id_periodo into v_id_periodo from
            param.tperiodo per
          where per.fecha_ini <= now()::date
                and per.fecha_fin >=  now()::date
          limit 1 offset 0;

          v_num_ven =   param.f_obtener_correlativo(
          'VEN',
          v_id_periodo,-- par_id,
          NULL, --id_uo
          NULL,    -- id_depto
          v_id_usuario,
          'VEF',
          NULL,
          0,
          0,
          'vef.tpunto_venta',
          v_id_punto_venta,
          v_datos_carga.cod_punto_venta
          );


          select dosi.id_dosificacion into v_id_dosificacion
        from vef.tdosificacion dosi
        where trim(dosi.nroaut) = trim(v_datos_carga.nro_autorizacion::varchar) ;


          /*Se procede a la inserccion del registro en la tabla vef.tventa*/
          insert into vef.tventa(
                        id_venta,--1
                        id_cliente,--2 /*Podemos poner aqui la condicion para ir insertando o no*/
                        id_sucursal,--3 /*Nos llegaria por el servicio*/
                        id_proceso_wf,--4 /*Se recupera para el nro de tramite*/
                        id_estado_wf,--5 /*Se recupera para el estado que se encuentra*/
                        estado_reg,--6
                        nro_tramite,--7 /*Recuperamos en la variable*/
                        a_cuenta,--8
                        fecha_estimada_entrega,--9
                        usuario_ai,--10
                        fecha_reg,--11
                        id_usuario_reg,--12
                        id_usuario_ai,--13
                        id_usuario_mod,--14
                        fecha_mod,--15
                        estado,--16
                        id_punto_venta,--17 /*Llegaria desde el servicio*/
                        id_vendedor_medico,--18
                        porcentaje_descuento,--19
                        comision,--20
                        observaciones,--21
                        correlativo_venta,--22
                        tipo_factura,--23
                        fecha,--24
                        nro_factura,--25
                        id_dosificacion,--26
                        excento,--27

                        id_moneda,--28
                        transporte_fob,--29
                        seguros_fob,--30
                        otros_fob,--31
                        transporte_cif,--32
                        seguros_cif,--33
                        otros_cif,--34
                        tipo_cambio_venta,--35
                        valor_bruto,--36
                        descripcion_bulto,--37
                        nit,--38
                        nombre_factura,--39
                        id_cliente_destino,--40
                        hora_estimada_entrega,--41
                        tiene_formula,--42
                        forma_pedido,--43
                        total_venta,--44
                        total_venta_msuc,--45
                        cod_control,--46
                        id_sistema_origen,--47
                        id_usuario_cajero--48
                      ) values(
                        v_id_venta,--1
                        v_id_cliente,--2
                        v_id_sucursal,--3
                        v_id_proceso_wf,--4
                        v_id_estado_wf,--5
                        'activo',--6
                        v_num_tramite,--7
                        0,--8
                        v_datos_carga.fecha::date,--9
                        NULL,--10
                        now(),--11
                        v_id_usuario,--12
                        NULL,--13
                        null,--14
                        null,--15
                        v_codigo_estado,--16
                        v_id_punto_venta,--17
                        null,--18
                        0,--19
                        0,--20
                        '',--21
                        v_num_ven,--22
                        'carga',--23
                        v_datos_carga.fecha::date,--24
                        v_datos_carga.nro_factura::integer,--25
                        v_id_dosificacion,--26
                        v_exento,--27--Excento por el momento 0
                        v_id_moneda,--28
                        0,--29
                        0,--30
                        0,--31
                        0,--32
                        0,--33
                        0,--34
                        0,--35
                        0,--36
                        '',--37
                        REPLACE(v_datos_carga.nit,' ',''),--38
                        upper(v_datos_carga.razon_social),--39
                        NULL,--40
                        v_hora_estimada,--41
                        'no',--42
                        'carga',--43
                        v_monto_total,--44
                        v_monto_total,--45
                        v_datos_carga.codigo_control,--46,
                        v_datos_carga.id_origen::integer,--47
                        v_id_usuario--48
                      );
          /****************************************************************/

          /*En caso que el id_usuario sea Null debemos almacenarlo en una tabla para posible regularizacion*/
           if (v_id_usuario is null) then
                insert into vef.tfacturas_carga_observadas(
                          id_venta,--1
                          id_funcionario,--2
                          estado,--3
                          observacion--4
                        ) values(
                          v_id_venta,--1
                          v_datos_carga.id_funcionario::INTEGER,--2
                          'pendiente',--3
                          'El Funcionario no tiene una cuenta de usuario en el Sistema ERP'--4
                        );
            elsif (COALESCE (v_cant_id_usuario_rol,0) > 1) then
                 insert into vef.tfacturas_carga_pendiente(
                          id_venta,--1
                          id_funcionario,--2
                          estado,--3
                          observacion--4
                        ) values(
                          v_id_venta,--1
                          v_datos_carga.id_funcionario::INTEGER,--2
                          'pendiente',--3
                          v_observaciones--4
                        );
                end if;
            /*************************************************************************************************/

            /*Recuperamos el id_tipo_estado y el id_estado_wf*/
             select
                ew.id_tipo_estado ,
                ew.id_estado_wf,
                ew.id_funcionario
              into
                v_id_tipo_estado_sig,
                v_id_estado_wf_sig,
                v_id_funcionario_sig
              from wf.testado_wf ew
                inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
              where ew.id_estado_wf =  v_id_estado_wf;
             /********************************************************/

             /*Obtenemos los campos de venta el id_entidad y el tipo_base*/
              select v.*,s.id_entidad,tv.tipo_base into v_venta
              from vef.tventa v
                inner join vef.tsucursal s on s.id_sucursal = v.id_sucursal
                inner join vef.tcliente c on c.id_cliente = v.id_cliente
                inner join vef.ttipo_venta tv on tv.codigo = v.tipo_factura and tv.estado_reg = 'activo'
              where v.id_proceso_wf = v_id_proceso_wf;
              /***********************************************************/

              /*Obtenemos el id del estado finalizado*/
              v_estado_finalizado = (v_id_tipo_estado_sig+1);
              /****************************************/

              /*Obtenemnos el codigo finalizado*/
              select te.codigo into v_codigo_estado
              from wf.ttipo_estado te
              where te.id_tipo_estado=v_estado_finalizado;
              /******************************************/


              /*Obtenemnos el codigo finalizado*/
              select te.codigo into v_codigo_estado
              from wf.ttipo_estado te
              where te.id_tipo_estado=v_estado_finalizado;
              /******************************************/

              /*Creamos un nuevo parametro*/
              v_tabla = pxp.f_crear_parametro(ARRAY[	'_nombre_usuario_ai',
              '_id_usuario_ai',
              'id_venta',
              'tipo_factura',
              'codigo_estado'],
                                              ARRAY[	'',
                                              '',
                                              v_venta.id_venta::varchar,
                                              v_venta.tipo_factura,
                                              v_codigo_estado],
                                              ARRAY[	'varchar',
                                              'integer',
                                              'integer',
                                              'varchar',
                                              'varchar']
              );


            /*******************************************************************************************/

             /*Obtenemos el codigo finalizado y fin*/
              select
                te.codigo,te.fin
              into
                v_codigo_estado_siguiente,v_es_fin
              from wf.ttipo_estado te
              where te.id_tipo_estado = v_estado_finalizado;
              /*********************************************************************/

              --configurar acceso directo para la alarma
              v_acceso_directo = '';
              v_clase = '';
              v_parametros_ad = '';
              v_tipo_noti = 'notificacion';
              v_titulo  = 'Visto Bueno';
              v_obs = '----';

              -- hay que recuperar el supervidor que seria el estado inmediato,...
              v_id_estado_actual =  wf.f_registra_estado_wf(v_estado_finalizado /*tengpo*/,
                                                            v_id_funcionario_sig/*recuperar*/,
                                                            v_id_estado_wf /*tengo*/,
                                                            v_id_proceso_wf/*tengo*/,
                                                            v_id_usuario,
                                                            NULL,
                                                            NULL,
                                                            v_id_depto,
                                                            v_obs,
                                                            v_acceso_directo ,
                                                            v_clase,
                                                            v_parametros_ad,
                                                            v_tipo_noti,
                                                            v_titulo);

               /*Verificar que hace*/
               IF  vef.f_fun_inicio_venta_wf(v_id_usuario,
                                            NULL,
                                            NULL,
                                            v_id_estado_actual,
                                            v_id_proceso_wf,
                                            v_codigo_estado_siguiente) THEN

              END IF;
              /************************************/


              /*Aqui Insertamos los Medios de Pago que nos enviara Carga*/
              v_acumulado_fp = 0;

              --v_json_data = '['||v_parametros.json_venta_forma_pago||']';


              --for v_medio_pago in (select *
                --                  from json_populate_recordset(null::vef.medio_pago_venta,v_json_data::json))loop

               /*Recuperamos el id moneda en base al codigo que nos manda el servicio*/
               select mon.id_moneda
                      into
                      v_id_moneda_fp
               from param.tmoneda mon
               where mon.codigo_internacional = v_datos_carga.moneda;
               /**********************************************************************/

               /*Recuperamos el id auxiliar en base al codigo que nos envia el servicio*/
               IF (v_datos_carga.cod_auxiliar is not null) then
                 select aux.id_auxiliar into
                        v_id_auxiliar_fp
                 from conta.tauxiliar aux
                 where (aux.codigo_auxiliar = v_datos_carga.cod_auxiliar OR aux.cod_antiguo = v_datos_carga.cod_auxiliar);
               end if;
               /************************************************************************/

               /*Recuperamos el id_medio_pago en base al codigo que nos envia el servicio*/
               select mp.id_medio_pago_pw
                      into
                      v_id_medio_pago
               from obingresos.tmedio_pago_pw mp
               where mp.mop_code = v_datos_carga.cod_medio_pago;
               /**************************************************************************/

               if (v_id_medio_pago is null) then
                  insert into vef.tfacturas_carga_observadas(
                          id_venta,--1
                          id_funcionario,--2
                          estado,--3
                          observacion--4
                        ) values(
                          v_id_venta,--1
                          NULL,--2
                          'fp_error',--3
                          'El medio de pago no esta parametrizado en la tabla obingresos.tmedio_pago_pw'--4
                        );
               end if;

               /*Aqui realizamos la inserccion de los medios de pago*/
               insert into vef.tventa_forma_pago(
                                                  usuario_ai,--1
                                                  fecha_reg,--2
                                                  id_usuario_reg,--3
                                                  id_usuario_ai,--4
                                                  estado_reg,--5
                                                  id_venta,--6
                                                  monto_transaccion,--7
                                                  monto,--8
                                                  cambio,--9
                                                  monto_mb_efectivo,--10
                                                  numero_tarjeta,--11
                                                  codigo_tarjeta,--12
                                                  id_auxiliar,--13
                                                  tipo_tarjeta,--14
                                                  id_medio_pago,--15
                                                  id_moneda--16
                                                )
                                          values(
                                                  NULL,--1
                                                  now(),--2
                                                  v_id_usuario,--3
                                                  NULL,--4
                                                  'activo',--5
                                                  v_id_venta,--6
                                                  v_datos_carga.importe_total,--7
                                                  0,--8
                                                  0,--9
                                                  0,--10
                                                  '',--11
                                                  '',--12
                                                  v_id_auxiliar_fp,--13
                                                  NULL,--14
                                                  v_id_medio_pago,--15
                                                  v_id_moneda_fp--16
                                                )returning id_venta_forma_pago into v_id_venta_forma_pago;

              /*********************************************************************************************/

              /*Aqui realizamos la conversion de la moneda en caso que llegue en dolar*/
              if (v_id_moneda != v_id_moneda_fp) then
                    v_monto_fp = param.f_convertir_moneda(v_id_moneda_fp,v_id_moneda,v_datos_carga.importe_total,v_datos_carga.fecha::date,'O',2,NULL,'si');
              else
                    v_monto_fp = v_datos_carga.importe_total;
              end if;
              /************************************************************************/

              /*Realizamos la Actualizacion de la forma de pago con la nueva conversion*/
              update vef.tventa_forma_pago set
                  monto = v_monto_fp,
                  cambio = (case when (v_monto_fp + v_acumulado_fp - v_monto_total::numeric) > 0 then
                    (v_monto_fp + v_acumulado_fp - v_monto_total::numeric)
                            else
                              0
                            end),
                  monto_mb_efectivo = (case when (v_monto_fp + v_acumulado_fp - v_monto_total::numeric) > 0 then
                    (v_monto_fp - (v_monto_fp + v_acumulado_fp - v_monto_total::numeric))
                                       else
                                         v_monto_fp
                                       end)
                where id_venta_forma_pago = v_id_venta_forma_pago;
                v_acumulado_fp = v_acumulado_fp + v_monto_fp;
              /*************************************************************************/
             -- end loop;
              /**********************************************************/

            end if;
            /*********************************************************************************/




        /*Aqui recuperaremos la Factura insertada en la tabla tventa*/
        select 	* into v_respaldo
        from vef.tventa ven
        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
        where ven.id_sistema_origen = v_datos_carga.id_origen::integer and ven.tipo_factura = 'carga';
		/************************************************************/

        /*Aqui recuperamos el nro de Autorizacion*/
        /*select cr.nro_autorizacion
        	   into
               v_nro_autorizacion
        from vef.tdatos_carga_recibido cr
        where cr.id_sistema_origen::integer = v_datos_carga.id_origen::integer;*/
        /*****************************************/

        		insert into vef.trespaldo_facturas_anuladas (
                                                              id_venta,--1
                                                              nombre_factura,--2
                                                              nit,--3
                                                              cod_control,--4
                                                              num_factura,--5
                                                              total_venta,--6
                                                              total_venta_msuc,--7
                                                              id_sucursal,--8
                                                              id_cliente,--9
                                                              id_punto_venta,--10
                                                              observaciones,--11
                                                              id_moneda,--12
                                                              excento,--13
                                                              fecha,--14

                                                              /*id_sucursal_producto,--15
                                                              id_formula,--16
                                                              id_producto,--17
                                                              cantidad,--18
                                                              precio,--19
                                                              tipo,--20
                                                              descripcion,--21*/

                                                              id_medio_pago,--22
                                                              monto,--23
                                                              monto_transaccion,--24
                                                              monto_mb_efectivo,--25
                                                              numero_tarjeta,--26
                                                              codigo_tarjeta,--27
                                                              tipo_tarjeta,--28
                                                              id_auxiliar,--29
                                                              fecha_reg,--30
                                                              id_usuario_reg,--31
                                                              --id_dosificacion,--32
                                                              nro_autorizacion--33
                                                              )
                                                      VALUES (
                                                              v_respaldo.id_venta,--1
                                                              v_respaldo.nombre_factura,--2
                                                              v_respaldo.nit,--3
                                                              v_respaldo.cod_control,--4
                                                              v_respaldo.nro_factura,--5
                                                              v_respaldo.total_venta,--6
                                                              v_respaldo.total_venta_msuc,--7
                                                              v_respaldo.id_sucursal,--8
                                                              v_respaldo.id_cliente,--9
                                                              v_respaldo.id_punto_venta,--10
                                                              v_respaldo.observaciones,--11
                                                              v_respaldo.id_moneda,--12
                                                              v_respaldo.excento,--13
                                                              v_respaldo.fecha,--14
                                                              ---Aqui recupera el detalle
                                                             /* v_respaldo.id_sucursal_producto,--15
                                                              v_respaldo.id_formula,--16
                                                              v_respaldo.id_producto,--17
                                                              v_respaldo.cantidad,--18
                                                              v_respaldo.precio,--19
                                                              v_respaldo.tipo,--20
                                                              v_respaldo.descripcion,--21*/
                                                              ----------------------------
                                                              --Aqui el medio de pago----
                                                              v_respaldo.id_medio_pago,--22
                                                              v_respaldo.monto,--23
                                                              v_respaldo.monto_transaccion,--24
                                                              v_respaldo.monto_mb_efectivo,--25
                                                              v_respaldo.numero_tarjeta,--26
                                                              v_respaldo.codigo_tarjeta,--27
                                                              v_respaldo.tipo_tarjeta,--28
                                                              v_respaldo.id_auxiliar,--29
                                                              now(),--30
                                                              v_respaldo.id_usuario_cajero,--31
                                                              --v_respaldo.id_dosificacion,--32
                                                              v_datos_carga.nro_autorizacion--33
                                                      		);

          /*Obtenemos los campos de venta el id_entidad y el tipo_base*/
          select v.* into v_venta
          from vef.tventa v
          where v.id_sistema_origen = v_datos_carga.id_origen::integer;
          /***********************************************************/

       	   /***Actualizamos la forma de Pago a todo 0***/
            update vef.tventa_forma_pago set
            monto_transaccion = 0,
            monto = 0,
            cambio = 0,
            monto_mb_efectivo = 0
            where id_venta = v_venta.id_venta;
          /*********************************************/

          /*Cambiamos La factura a Razon Social Anulada*/
            update vef.tventa set
            cod_control = Null,
            total_venta_msuc = 0,
            nombre_factura = 'ANULADO',
            nit = '0',
            total_venta = 0
            where id_sistema_origen = v_datos_carga.id_origen::integer;
	 	  /************************************************/


          --obtenemos datos basicos
          select
            ven.id_estado_wf,
            ven.id_proceso_wf,
            ven.estado,
            ven.id_venta,
            ven.nro_tramite
          into
            v_registros
          from vef.tventa ven
          where ven.id_sistema_origen = v_datos_carga.id_origen::integer;


         v_tabla = '';

        	--raise exception 'aqui llega data %',v_respaldo.id_usuario_cajero;
         --v_res = vef.f_anula_venta(1,v_respaldo.id_usuario_cajero,v_tabla, v_registros.id_proceso_wf,v_registros.id_estado_wf, v_venta.id_venta);


         select
          te.id_tipo_estado
         into
          v_id_tipo_estado
         from wf.tproceso_wf pw
         inner join wf.ttipo_proceso tp on pw.id_tipo_proceso = tp.id_tipo_proceso
         inner join wf.ttipo_estado te on te.id_tipo_proceso = tp.id_tipo_proceso and te.codigo = 'anulado'
         where pw.id_proceso_wf = v_registros.id_proceso_wf;

         select * into v_venta_anu
         from vef.tventa
         where id_venta = v_venta.id_venta;


         IF v_id_tipo_estado is NULL  THEN
            raise exception 'No se parametrizo el estado "anulado" para la venta';
         END IF;

           select f.id_funcionario into  v_id_funcionario_inicio
          from segu.tusuario u
          inner join orga.tfuncionario f on f.id_persona = u.id_persona
          where u.id_usuario = v_respaldo.id_usuario_cajero;

           -- pasamos la solicitud  al siguiente anulado

           v_id_estado_actual =  wf.f_registra_estado_wf(v_id_tipo_estado,
                                                       v_id_funcionario_inicio,
                                                       v_registros.id_estado_wf,
                                                       v_registros.id_proceso_wf,
                                                       v_respaldo.id_usuario_cajero,
                                                       NULL,
                                                       NULL,
                                                       NULL,
                                                       'Anulacion de venta');


             -- actualiza estado en la solicitud

             update vef.tventa  set
               id_estado_wf =  v_id_estado_actual,
               estado = 'anulado',
               id_usuario_mod=v_respaldo.id_usuario_cajero,
               fecha_mod=now()
             where id_venta  = v_venta.id_venta;





        END LOOP;

        /*v_tabla = pxp.f_crear_parametro(ARRAY[	'id_origen'	],
                                        ARRAY[	v_datos_carga.id_origen],
                                        ARRAY[	'integer']
        );*/

      end if;


    return 'Registro exitoso';

EXCEPTION
	WHEN OTHERS THEN
			--update a la tabla informix.migracion

            return SQLERRM;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION vef.ft_replicar_facturas_carga_pendientes (p_estado varchar, p_fecha_inicio date, p_fecha_fin date)
  OWNER TO postgres;
