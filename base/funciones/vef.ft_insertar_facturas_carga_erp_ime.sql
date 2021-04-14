CREATE OR REPLACE FUNCTION vef.ft_insertar_facturas_carga_erp_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ventas Facturación
 FUNCION: 		vef.ft_insertar_facturas_carga_erp_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tforma_pago'
 AUTOR: 		 (ivaldivia)
 FECHA:	        28-08-2019 15:00:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_movimiento_entidad	integer;
    v_existe				record;
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
    /********************/

BEGIN

    v_nombre_funcion = 'vef.ft_insertar_facturas_carga_erp_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VEF_INS_FACCARGA_ERP'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		28-08-2019 15:00:00
	***********************************/

	if(p_transaccion='VEF_INS_FACCARGA_ERP')then

        begin

        /*Inserccion de los datos recibidos por el servicio*/
        INSERT INTO vef.tdatos_carga_recibido
                (
                    id_sistema_origen,--1
                    fecha,--2
                    nro_factura,--3
                    nro_autorizacion,--4
                    nit,--5
                    razon_social,--6
                    importe_total,--7
                    codigo_control,--8
                    tipo_factura,--9
                    moneda,--10
                    codigo_punto_venta,--11
                    id_funcionario,--12
                    observaciones,--13
                    json_venta_forma_pago--14
                )
        VALUES (
                v_parametros.id_origen::integer,--1
                v_parametros.fecha,--2
                v_parametros.nro_factura,--3
                v_parametros.nro_autorizacion,--4
                v_parametros.nit,--5
                v_parametros.razon_social,--6
                v_parametros.importe_total::numeric,--7
                v_parametros.codigo_control,--8
                v_parametros.tipo_factura,--9
                v_parametros.moneda,--10
                v_parametros.codigo_punto_venta,--11
                v_parametros.id_funcionario::INTEGER,--12
                v_parametros.observaciones,--13
                '['||v_parametros.json_venta_forma_pago||']'--14
            );
        /***************************************************/

        /*Inserccion de facturas carga a tventa y tventa_forma_pago*/

        /*Verificamos si Existe el Cliente para obtener el ID*/
        select count (cli.id_cliente) into v_existencia_cliente
        from vef.tcliente cli
        where trim(cli.nit) = trim(v_parametros.nit)
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
        where fun.id_funcionario = v_parametros.id_funcionario::INTEGER;
        /***************************************************/

        if (v_cant_id_usuario > 1) then

        	select usu.id_usuario
            	   into
                   v_cant_id_usuario
            from orga.vfuncionario fun
            inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
            inner join segu.tusuario_rol usurol on usurol.id_usuario = usu.id_usuario
            inner join segu.trol rol on rol.id_rol = usurol.id_rol and rol.estado_reg = 'activo'
            where fun.id_funcionario = v_parametros.id_funcionario::INTEGER and rol.rol = 'VEF - Cajero';

            if (v_cant_id_usuario_rol > 1) then
            	v_observaciones = 'El funcionario tiene dos cuentas con el mismo rol (VEF - Cajero)';
            else

                select usu.id_usuario
                       into
                       v_id_usuario
                from orga.vfuncionario fun
                inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
                inner join segu.tusuario_rol usurol on usurol.id_usuario = usu.id_usuario
                inner join segu.trol rol on rol.id_rol = usurol.id_rol and rol.estado_reg = 'activo'
                where fun.id_funcionario = v_parametros.id_funcionario::INTEGER and rol.rol = 'VEF - Cajero';

            end if;


        else
        	select usu.id_usuario
                   into
                   v_id_usuario
            from orga.vfuncionario fun
            inner join segu.vusuario usu on usu.id_persona = fun.id_persona and usu.estado_reg = 'activo'
            where fun.id_funcionario = v_parametros.id_funcionario::INTEGER;
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
                  	v_parametros.razon_social,
                  	v_parametros.nit
                ) returning id_cliente into v_id_cliente;
        else
            select cli.id_cliente into v_id_cliente
            from vef.tcliente cli
            where trim(cli.nit) = trim(v_parametros.nit)
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
        where tc.id_moneda = 2 and tc.fecha = v_parametros.fecha::Date;
        /***********************************************************/

        /****Aqui la moneda base para la emision de la factura*******/
        select mon.id_moneda into v_id_moneda
        from param.tmoneda mon
        where mon.codigo_internacional = v_parametros.moneda;
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
            v_monto_total = param.f_convertir_moneda(v_id_moneda,v_moneda_base,v_parametros.importe_total,v_parametros.fecha::date,'O',2,NULL,'si');
        else
            v_monto_total = v_parametros.importe_total;
        end if;
        /***********************************************************/

        /**********Recuperamos el id punto de venta en base al codigo que nos envia el sistema de carga*********/
        select pv.id_punto_venta,
               pv.id_sucursal
        	   into
               v_id_punto_venta,
               v_id_sucursal
        from vef.tpunto_venta pv
        where pv.codigo = v_parametros.codigo_punto_venta and pv.tipo = 'carga';

        if (v_id_punto_venta is null) then
        	raise exception 'No existe el punto de venta favor verificar el codigo %', v_parametros.codigo_punto_venta;
        end if;
        /*******************************************************************************************************/

        select id_gestion into v_id_gestion
        from param.tgestion
        where gestion = extract(year from (v_parametros.fecha::date))::integer;



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
            v_parametros._id_usuario_ai,
            v_parametros._nombre_usuario_ai,
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
        v_parametros.codigo_punto_venta
        );

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
                      v_parametros.fecha::date,--9
                      v_parametros._nombre_usuario_ai,--10
                      now(),--11
                      v_id_usuario,--12
                      v_parametros._id_usuario_ai,--13
                      null,--14
                      null,--15
                      v_codigo_estado,--16
                      v_id_punto_venta,--17
                      null,--18
                      0,--19
                      0,--20
                      upper(v_parametros.observaciones),--21
                      v_num_ven,--22
                      'carga',--23
                      v_parametros.fecha::date,--24
                      v_parametros.nro_factura::integer,--25
                      null,--26
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
                      REPLACE(v_parametros.nit,' ',''),--38
                      upper(v_parametros.razon_social),--39
                      NULL,--40
                      v_hora_estimada,--41
                      'no',--42
                      'carga',--43
                      v_monto_total,--44
					  v_monto_total,--45
					  v_parametros.codigo_control,--46,
                      v_parametros.id_origen::integer,--47
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
                        v_parametros.id_funcionario::INTEGER,--2
                        'pendiente',--3
                        'El Funcionario no tiene una cuenta de usuario en el Sistema ERP'--4
                      );
          elsif (COALESCE (v_cant_id_usuario_rol,0) > 1) then
               insert into vef.tfacturas_carga_observadas(
                        id_venta,--1
                        id_funcionario,--2
                        estado,--3
                        observacion--4
                      ) values(
                        v_id_venta,--1
                        v_parametros.id_funcionario::INTEGER,--2
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
                                          ARRAY[	coalesce(v_parametros._nombre_usuario_ai,''),
                                          coalesce(v_parametros._id_usuario_ai::varchar,''),
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
                                                        v_parametros._id_usuario_ai,
                                                        v_parametros._nombre_usuario_ai,
                                                        v_id_depto,
                                                        v_obs,
                                                        v_acceso_directo ,
                                                        v_clase,
                                                        v_parametros_ad,
                                                        v_tipo_noti,
                                                        v_titulo);

           /*Verificar que hace*/
           IF  vef.f_fun_inicio_venta_wf(v_id_usuario,
                                        v_parametros._id_usuario_ai,
                                        v_parametros._nombre_usuario_ai,
                                        v_id_estado_actual,
                                        v_id_proceso_wf,
                                        v_codigo_estado_siguiente) THEN

          END IF;
          /************************************/


          /*Aqui Insertamos los Medios de Pago que nos enviara Carga*/
          v_acumulado_fp = 0;

          v_json_data = '['||v_parametros.json_venta_forma_pago||']';


          for v_medio_pago in (select *
                              from json_populate_recordset(null::vef.medio_pago_venta,v_json_data::json))loop

           /*Recuperamos el id moneda en base al codigo que nos manda el servicio*/
           select mon.id_moneda
                  into
                  v_id_moneda_fp
           from param.tmoneda mon
           where mon.codigo_internacional = v_medio_pago.moneda;
           /**********************************************************************/

           /*Recuperamos el id auxiliar en base al codigo que nos envia el servicio*/
           IF (v_medio_pago.cod_auxiliar is not null) then
             select aux.id_auxiliar into
                    v_id_auxiliar_fp
             from conta.tauxiliar aux
             where (aux.codigo_auxiliar = v_medio_pago.cod_auxiliar OR aux.cod_antiguo = v_medio_pago.cod_auxiliar);
           end if;
           /************************************************************************/

           /*Recuperamos el id_medio_pago en base al codigo que nos envia el servicio*/
           select mp.id_medio_pago_pw
           	      into
                  v_id_medio_pago
           from obingresos.tmedio_pago_pw mp
           where mp.mop_code = v_medio_pago.cod_medio_pago;
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
                                              v_parametros._nombre_usuario_ai,--1
                                              now(),--2
                                              v_id_usuario,--3
                                              v_parametros._id_usuario_ai,--4
                                              'activo',--5
                                              v_id_venta,--6
                                              v_medio_pago.importe,--7
                                              0,--8
                                              0,--9
                                              0,--10
                                              v_medio_pago.numero_tarjeta,--11
                                              v_medio_pago.codigo_tarjeta,--12
                                              v_id_auxiliar_fp,--13
                                              NULL,--14
                                              v_id_medio_pago,--15
                                              v_id_moneda_fp--16
                                            )returning id_venta_forma_pago into v_id_venta_forma_pago;

          /*********************************************************************************************/

          /*Aqui realizamos la conversion de la moneda en caso que llegue en dolar*/
          if (v_id_moneda != v_id_moneda_fp) then
                v_monto_fp = param.f_convertir_moneda(v_id_moneda_fp,v_id_moneda,v_medio_pago.importe,v_parametros.fecha::date,'O',2,NULL,'si');
          else
            	v_monto_fp = v_medio_pago.importe;
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
          end loop;
          /**********************************************************/


        /*Migrar los datos a la nueva base de datos db_facturas_2019*/
        /*Recuperamos el nombre del cajero que esta finalizando la factura*/
        SELECT per.nombre_completo2 into v_cajero
        from segu.tusuario usu
        inner join segu.vpersona2 per on per.id_persona = usu.id_persona
        where usu.id_usuario = v_id_usuario;
        /******************************************************************/

        /*Establecemos la conexion con la base de datos*/
          --v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();

          v_host=pxp.f_get_variable_global('sincroniza_ip_facturacion');
          v_puerto=pxp.f_get_variable_global('sincroniza_puerto_facturacion');
          v_dbname=pxp.f_get_variable_global('sincronizar_base_facturacion');


          select usu.cuenta,
                 usu.contrasena
                 into
                 v_cuenta_usu,
                 v_pass_usu
          from segu.tusuario usu
          where usu.id_usuario = v_id_usuario;

          p_user= 'dbkerp_'||v_cuenta_usu;


         -- v_password=pxp.f_get_variable_global('sincronizar_password_facturacion');



          v_semilla = pxp.f_get_variable_global('semilla_erp');


          select md5(v_semilla||v_pass_usu) into v_password;

          v_cadena_cnx = 'hostaddr='||v_host||' port='||v_puerto||' dbname='||v_dbname||' user='||p_user||' password='||v_password;


         -- raise exception 'v_cadena_cnx %',v_cadena_cnx;
          v_conexion = (SELECT dblink_connect(v_cadena_cnx));
        /*************************************************/

          select * FROM dblink(v_cadena_cnx,'select nextval(''sfe.tfactura_id_factura_seq'')',TRUE)AS t1(resp integer)
          into v_id_factura;

          v_consulta = 'INSERT INTO sfe.tfactura(
                            id_factura,
                            fecha_factura,
                            nro_factura,
                            nro_autorizacion,
                            estado,
                            nit_ci_cli,
                            razon_social_cli,
                            importe_total_venta,
                            importe_otros_no_suj_iva,
                            codigo_control ,
                            sistema_origen,
                            id_origen,
                            tipo_factura,
                            usuario_reg,
                            desc_ruta

                            )
          				values(
                            '||v_id_factura||',
                            '''||v_parametros.fecha::date||''',
                            '''||v_parametros.nro_factura::varchar||''',
                            '''||v_parametros.nro_autorizacion::varchar||''',
                            ''VÁLIDA'',
                            '''||v_parametros.nit::varchar||''',
                            '''||v_parametros.razon_social::varchar||''',
                            '||v_parametros.importe_total::numeric||',
                            0,
                            '''||v_parametros.codigo_control::varchar||''',--decomentar
                            ''CARGA'',
                            '||v_parametros.id_origen::integer||',
                            ''computarizada'',
                            '''||v_cajero::varchar||''',
                            ''CARGA NACIONAL COMPUTARIZADA''
                            );';

              IF(v_conexion!='OK') THEN
              		raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
              ELSE

              	perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);

              	v_res_cone=(select dblink_disconnect());

              END IF;

              /************************************/
        	  /*****************************************************************/

              --Definicion de la respuesta
              v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Factura Registrada con con exito');

              --Devuelve la respuesta
              return v_resp;


          end;

    /*********************************
 	#TRANSACCION:  'VEF_ANULAR_FCA_ERP'
 	#DESCRIPCION:	Actualizacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		01-10-2019 17:30:00
	***********************************/

	elsif(p_transaccion='VEF_ANULAR_FCA_ERP')then

		begin


		/*Aqui recuperaremos la Factura insertada en la tabla tventa*/
        select 	* into v_respaldo
        from vef.tventa ven
        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
        where ven.id_sistema_origen = v_parametros.id_origen::integer and ven.tipo_factura = 'carga';
		/************************************************************/

        /*Aqui recuperamos el nro de Autorizacion*/
        select cr.nro_autorizacion
        	   into
               v_nro_autorizacion
        from vef.tdatos_carga_recibido cr
        where cr.id_sistema_origen::integer = v_parametros.id_origen::integer;
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
                                                              v_nro_autorizacion--33
                                                      		);

          /*Obtenemos los campos de venta el id_entidad y el tipo_base*/
          select v.* into v_venta
          from vef.tventa v
          where v.id_sistema_origen = v_parametros.id_origen::integer;
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
            where id_sistema_origen = v_parametros.id_origen::integer;
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
          where ven.id_sistema_origen = v_parametros.id_origen::integer;


        /*Recuperamos el nombre del cajero que esta finalizando la factura*/
        SELECT per.nombre_completo2 into v_cajero
        from segu.tusuario usu
        inner join segu.vpersona2 per on per.id_persona = usu.id_persona
        where usu.id_usuario = v_venta.id_usuario_cajero;
        /******************************************************************/


        v_res = vef.f_anula_venta(p_administrador,v_respaldo.id_usuario_cajero,p_tabla, v_registros.id_proceso_wf,v_registros.id_estado_wf, v_respaldo.id_venta);



			--Sentencia de la modificacion
			v_host=pxp.f_get_variable_global('sincroniza_ip_facturacion');
          v_puerto=pxp.f_get_variable_global('sincroniza_puerto_facturacion');
          v_dbname=pxp.f_get_variable_global('sincronizar_base_facturacion');


          select usu.cuenta,
                 usu.contrasena
                 into
                 v_cuenta_usu,
                 v_pass_usu
          from segu.tusuario usu
          where usu.id_usuario = v_venta.id_usuario_cajero;

          p_user= 'dbkerp_'||v_cuenta_usu;


         -- v_password=pxp.f_get_variable_global('sincronizar_password_facturacion');



          v_semilla = pxp.f_get_variable_global('semilla_erp');


          select md5(v_semilla||v_pass_usu) into v_password;

          v_cadena_cnx = 'hostaddr='||v_host||' port='||v_puerto||' dbname='||v_dbname||' user='||p_user||' password='||v_password;



        /*Replicacion a la base de datos DB_FACTURAS 2019*/
   		/*Para migrar los datos a la nueva base de datos db_facturas_2019*/


          /*Establecemos la conexion con la base de datos*/
            --v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();
            v_conexion = (SELECT dblink_connect(v_cadena_cnx));

            /************************************************/
          		select * FROM dblink(v_cadena_cnx,'
                								  select f.id_factura,
                                                  		 f.fecha_factura,
                                                         f.nro_factura,
                                                         f.estado,
                                                         f.nit_ci_cli,
                                                         f.razon_social_cli,
                                                         f.importe_total_venta,
                                                         f.usuario_reg,
                                                         f.tipo_factura,
                                                         f.id_origen,
                                                         f.sistema_origen,
                                                         f.nro_autorizacion
                  								  from sfe.tfactura f
                                                  where f.id_origen = '||v_parametros.id_origen::INTEGER||' and f.sistema_origen = ''CARGA''
                                                  ',TRUE) AS datos_carga (
                                                  		id_factura INTEGER,
                                                        fecha_factura date,
                                                        nro_factura varchar,
                                                        estado varchar,
                                                        nit_ci_cli varchar,
                                                        razon_social_cli varchar,
                                                        importe_total_venta numeric,
                                                        usuario_reg varchar,
                                                        tipo_factura varchar,
                                                        id_origen INTEGER,
                                                        sistema_origen varchar,
                                                        nro_autorizacion varchar )
                                                  into v_datos_carga;

              v_consulta = 'update sfe.tfactura set
                            estado_reg = ''inactivo''
                            where id_origen = '||v_datos_carga.id_origen||' and sistema_origen = ''CARGA'' and estado <> ''ANULADA'';';



               select * FROM dblink(v_cadena_cnx,'select nextval(''sfe.tfactura_id_factura_seq'')',TRUE)AS t1(resp integer)
            	into v_id_factura;

              v_consulta_inser = '
                                INSERT INTO sfe.tfactura(
                                id_factura,
                                fecha_factura,
                                nro_factura,
                                estado,
                                nit_ci_cli,
                                razon_social_cli,
                                importe_total_venta,
                                usuario_reg,
                                tipo_factura,
                                id_origen,
                                sistema_origen,
                                nro_autorizacion
                                )
                                values(
                                '||v_id_factura||',
                                '''||v_datos_carga.fecha_factura||''',
                                '''||v_datos_carga.nro_factura::varchar||''',
                                ''ANULADA'',
                                ''0'',
                                ''ANULADA'',
                                0,
                                '''||v_cajero||''',
                                ''computarizada'',
                                '||v_datos_carga.id_origen||',
                                ''CARGA'',
                                '''||v_datos_carga.nro_autorizacion||'''
                                );';



              IF(v_conexion!='OK') THEN
              		raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
              ELSE
                       perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);

                       perform dblink_exec(v_cadena_cnx,v_consulta_inser,TRUE);

              	v_res_cone=(select dblink_disconnect());

              END IF;

          /*Establecemos la conexion con la base de datos*/
            --v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();
            v_conexion = (SELECT dblink_connect(v_cadena_cnx));
          /************************************************/



              IF(v_conexion!='OK') THEN
              		raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
              	ELSE
                   perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);

              	v_res_cone=(select dblink_disconnect());

              END IF;
              /************************************/
        /*****************************************************************/



			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Fáctura Anulada');

            --Devuelve la respuesta
            return v_resp;

		end;

    --Definicion de la respuesta
    v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Factura Registrada con con exito');
    v_resp = pxp.f_agrega_clave(v_resp,'tipo_mensaje','exito');

    --Devuelve la respuesta
    return v_resp;

     else

    	raise exception 'Transaccion inexistente: %',p_transaccion;

	end if;


EXCEPTION

	WHEN OTHERS THEN
		v_resp='';
		v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
		v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
		v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
		raise exception '%',v_resp;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION vef.ft_insertar_facturas_carga_erp_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
