CREATE OR REPLACE FUNCTION vef.ft_facturacion_externa_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_ft_facturacion_externa_ime
 DESCRIPCION:   Funcion para ir registrando los datos en la tabla de ventas y ventas detalle
 AUTOR: 		Ismael Valdivia
 FECHA:	        23-05-2020
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
	v_id_prioridad	integer;

    /*Aumentando estas variables*/
    v_num_tramite			varchar;
    v_id_proceso_wf			integer;
    v_id_estado_wf			integer;
    v_codigo_estado			varchar;
    v_id_gestion			integer;
    v_id_venta				integer;
    v_codigo_proceso		varchar;
    v_existencia_cliente	integer;
    v_id_cliente			integer;
    v_hora_estimada			time;
    v_id_sucursal			integer;
    v_id_dosificacion		integer;
    v_id_punto_venta		integer;
    v_codigo_tabla			varchar;
    v_num_ven				varchar;
	v_id_venta_detalle		integer;
    v_total_venta			numeric;
    v_registros				record;
    v_id_periodo			integer;
    v_id_tipo_estado_sig	integer;
    v_id_estado_wf_sig		integer;
    v_id_funcionario_sig	integer;
    v_venta					record;
    v_estado_finalizado		integer;
    v_tabla					varchar;
    v_id_tipo_estado		integer;
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
     v_fecha_venta			date;
    v_id_actividad_economica	integer[];
    v_dosificacion			record;
    v_nro_factura			integer;
    v_id_entidad			integer;
    v_mensaje_error_punto_venta	varchar;
    v_mensaje_general		varchar;
    v_tipo_cambio			numeric;
    v_id_moneda				integer;
    v_mensaje_error_moneda	varchar;
    v_respaldo				record;
    v_tipo_usuario			varchar;
    v_res					varchar;
    v_existe_tc				integer;
    v_exento				numeric;
    v_precio_unitario		numeric;
    v_precio_unitario_convertido numeric;
    v_mensaje_correo		varchar;
    v_parametros_correo		varchar;
    v_documento_adjunto		varchar;
    v_correos				varchar;
    v_codigo_control		varchar;
    v_numero_factura		varchar;
    v_precio_original		numeric;
    v_total_descuento		numeric;
    v_existencia_registro	integer;
    v_recuperados_factura	record;
    v_id_proceso_wf_venta	integer;
    v_cod_dosificacion		varchar;
    v_tc_erp				numeric;

     v_host varchar;
    v_puerto varchar;
    v_dbname varchar;
    p_user varchar;
    v_password varchar;
    v_semilla	varchar;

    v_cuenta_usu	varchar;
    v_pass_usu		varchar;
    v_cadena_cnx	varchar;
    v_conexion varchar;
    v_id_factura	integer;
    v_cajero		varchar;
    v_tipo_pv		VARCHAR;
    v_consulta	varchar;
    v_res_cone	varchar;
BEGIN

    v_nombre_funcion = 'vef.ft_facturacion_externa_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VEF_INS_FAC_EXT_IME'
 	#DESCRIPCION:	Insercion de registros
    #AUTOR: 		Ismael Valdivia
    #FECHA:	        23-05-2020
	***********************************/

	if(p_transaccion='VEF_INS_FAC_EXT_IME')then

        begin
        /*VALIDACION DE DUPLICIDAD A NIVEL DETALLE*/
           for v_registros in (select *
                            from json_populate_recordset(null::vef.detalle_venta,v_parametros.json_venta_detalle::json))
              loop
                     /*Aumentando este control para no emitir conceptos con monto 0*/
                     if(v_registros.precio_unitario = 0::numeric)then
                     	raise exception 'Debe seleccionar un concepto valido, el monto para el concepto no debe ser 0';
                     end if;
              		 /**************************************************************/

             /*bvp         select
                          count(*) into v_existencia_registro
                      from vef.tventa ven
                      inner join vef.tventa_detalle det on det.id_venta = ven.id_venta
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                      where det.llave_unica = v_registros.llave_unica and ven.fecha = now()::date and ven.estado = 'finalizado';

                      IF (v_existencia_registro > 0) then
                              select
                              pv.nombre,
                              ven.nro_factura
                              into
                              v_recuperados_factura
                          from vef.tventa ven
                          inner join vef.tventa_detalle det on det.id_venta = ven.id_venta
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where det.llave_unica = v_registros.llave_unica and ven.fecha = now()::date;


                          raise exception 'El concepto <b>%</b> ya ha tiene una factura emitida en fecha <b>%</b> en la sucursal <b>%</b> Nro. Factura: <b>%</b>',v_registros.descripcion,to_char(now()::date,'dd/mm/YYYY'),v_recuperados_factura.nombre,v_recuperados_factura.nro_factura;


                      end if;*/



              end loop;
		/**************************************************************************************************/
        /*Aqui pondremos un control para que se vaya registrando los clientes a los que se vende*/
          select count (cli.id_cliente) into v_existencia_cliente
          from vef.tcliente cli
          where cli.nit = replace(v_parametros.nit_cliente,' ','');

          if (v_existencia_cliente = 0) then
          		INSERT INTO
                  vef.tcliente
                  (
                    id_usuario_reg,
                    fecha_reg,
                    estado_reg,
                    nombre_factura,
                    nit
                  )
                VALUES (
                  p_id_usuario,
                  now(),
                  'activo',
                  v_parametros.razon_social,
                  v_parametros.nit_cliente
                ) returning id_cliente into v_id_cliente;
          else
          		select cli.id_cliente into v_id_cliente
                from vef.tcliente cli
                where cli.nit = replace(v_parametros.nit_cliente,' ','');
          end if;

        /****************************************************************************************/

        /*Obtenemos la hora de emision*/
        select to_char(current_timestamp, 'HH12:MI:SS') into v_hora_estimada;
        /******************************/

        	  /*Aqui obtenemos el tipo de cambio con sus decimales*/
              v_tipo_cambio = v_parametros.tipo_cambio;--(v_parametros.tipo_cambio/100);
              /****************************************************/

              select mon.id_moneda into v_id_moneda
              from param.tmoneda mon
              where mon.codigo_internacional = v_parametros.moneda;

              /*Aqui hacemos la conversion si los precios del servicio estan en dolares*/

              /*if (v_id_moneda = 2) then
              	  v_exento = (v_parametros.exento*v_tipo_cambio);
              else
                  v_exento = v_parametros.exento;
              end if;*/



        	 /* if (v_id_moneda is null) then
              	raise exception 'El codigo de moneda: % no existe favor consulte con el area de sistemas.',v_parametros.moneda;
             else*/

             /*Recuperamos el tipo de Cambio para hacer la inserccion*/
             /*Verificamos que el tipo de cambio no exista para la fecha actual*/
             /*select count(*) into v_existe_tc
             from param.ttipo_cambio cam
             where cam.fecha = now()::date and cam.id_moneda = 2;

             /*Si no se tiene registro del tipo de cambio entonces insertamos*/
             IF (v_existe_tc = 0) then
             insert into param.ttipo_cambio(
              estado_reg,
              fecha,
              observaciones,
              compra,
              venta,
              oficial,
              id_moneda,
              fecha_reg,
              id_usuario_reg,
              fecha_mod,
              id_usuario_mod
              ) values(
              'activo',
              now()::date,
              'Registro tipo de cambio desde servicio de facturacion',
              v_tipo_cambio,
              v_tipo_cambio,
              v_tipo_cambio,
              2,
              now(),
              p_id_usuario,
              null,
              null
              );
             end if;*/
             /*******************************************************/


            /***RECUPERAMOS LA ENTIDAD PARA VERIFICAR SUS PUNTOS DE VENTA***/
            select ent.id_entidad into v_id_entidad
            from param.tentidad ent
            where ent.nit = '154422029'; -- bvp
/*            where ent.nit = REPLACE(v_parametros.nit_entidad,' ',''); */
            /***************************************************************/

            IF (v_id_entidad is null) then
/*                Raise exception 'No se encuentra registrada la entidad con NIT: %',v_parametros.nit_entidad;*/
                Raise exception 'No se encuentra registrada la entidad con NIT: ';

            else
            /*Aqui recuperamos el id de la sucursal*/
               select pt.id_punto_venta,
                       pt.id_sucursal
                into v_id_punto_venta,
                     v_id_sucursal
                from vef.tpunto_venta pt
                inner join vef.tsucursal su on su.id_sucursal = pt.id_sucursal
                where trim(pt.nombre) = trim(upper(v_parametros.punto_venta)) and su.id_entidad = v_id_entidad;

                if (v_id_punto_venta IS NOT NULL) then
                      select pv.codigo into v_codigo_tabla
                      from vef.tpunto_venta pv
                      where id_punto_venta = v_id_punto_venta;
                else
                      select pv.codigo into v_codigo_tabla
                      from vef.tsucursal pv
                      where id_sucursal = v_id_sucursal;
              end if;

		/*Aqui ponemos controles para verficar si existe el punto de venta y la sucursal*/
            if (v_id_punto_venta is null and v_id_sucursal is null) then
                raise exception 'El punto de venta: % no se encuentra registrado.',v_parametros.punto_venta;
            else
          /*************************Obtenemos la gestion apartir de la fecha actual***************************/
            select id_gestion into v_id_gestion
            from param.tgestion
            where gestion = extract(year from now())::integer;
          /***************************************************************************************************/
            select nextval('vef.tventa_id_venta_seq') into v_id_venta;
            v_codigo_proceso = 'VEN-' || v_id_venta;
          /************************Obtenemos el id_proceso_wf, id_estado_wf y el codigo estado*************************************/

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
                p_id_usuario,
                v_parametros._id_usuario_ai,
                v_parametros._nombre_usuario_ai,
                v_id_gestion,
                'VEN',
                NULL,
                NULL,
                NULL,
                v_codigo_proceso);
          /****************************************************************************************************************************/


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
                      p_id_usuario,
                      'VEF',
                      NULL,
                      0,
                      0,
                      (case when v_id_punto_venta is not null then
                        'vef.tpunto_venta'
                       else
                         'vef.tsucursal'
                       end),
                      (case when v_id_punto_venta is not null then
                        v_id_punto_venta
                       else
                         v_id_sucursal
                       end),
                      v_codigo_tabla
                      );
                  end if;
            end if;
        --fin obtener correlativo
              if (v_mensaje_error is null and v_mensaje_error_punto_venta is null) then

                    insert into vef.tventa(
                      id_venta,
                      id_cliente, /*Podemos poner aqui la condicion para ir insertando o no*/
                      id_sucursal, /*Nos llegaria por el servicio*/
                      id_proceso_wf, /*Se recupera para el nro de tramite*/
                      id_estado_wf, /*Se recupera para el estado que se encuentra*/
                      estado_reg,
                      nro_tramite, /*Recuperamos en la variable*/
                      a_cuenta,
                      fecha_estimada_entrega,
                      usuario_ai,
                      fecha_reg,
                      id_usuario_reg,
                      id_usuario_ai,
                      id_usuario_mod,
                      fecha_mod,
                      estado,
                      id_punto_venta, /*Llegaria desde el servicio*/
                      id_vendedor_medico,
                      porcentaje_descuento,
                      comision,
                      observaciones,
                      correlativo_venta,
                      tipo_factura,
                      fecha,
                      nro_factura,
                      id_dosificacion,
                      excento,

                      id_moneda,
                      transporte_fob,
                      seguros_fob,
                      otros_fob,
                      transporte_cif,
                      seguros_cif,
                      otros_cif,
                      tipo_cambio_venta,
                      valor_bruto,
                      descripcion_bulto,
                      nit,
                      nombre_factura,
                      id_cliente_destino,
                      hora_estimada_entrega,
                      tiene_formula,
                      forma_pedido,
                      id_usuario_cajero
/*                      correo_electronico*/


                    ) values(
                      v_id_venta,
                      v_id_cliente,
                      v_id_sucursal,
                      v_id_proceso_wf,
                      v_id_estado_wf,
                      'activo',
                      v_num_tramite,
                      0,
                      now(),
                      v_parametros._nombre_usuario_ai,
                      now(),
                      p_id_usuario,
                      v_parametros._id_usuario_ai,
                      null,
                      null,
                      v_codigo_estado,
                      v_id_punto_venta,
                      null,
                      0,
                      0,
                      v_parametros.observaciones,
                      v_num_ven,
                      'computarizada',
                      now(),
                      NULL,
                      null,
                      v_parametros.exento,--Excento por el momento 0
                      v_id_moneda,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      v_parametros.tipo_cambio,
                      0,
                      '',
                      REPLACE(v_parametros.nit_cliente,' ',''),
                      v_parametros.razon_social,
                      NULL,
                      v_hora_estimada,
                      'no',
                      'externa',
                      p_id_usuario
/*                      v_parametros.correo_electronico*/

                    );


                    /*Aqui insertamos el detalle de la venta*/
                  for v_registros in (select *
                                      from json_populate_recordset(null::vef.detalle_venta,v_parametros.json_venta_detalle::json))loop


                  /*Aqui hacemos la conversion para que calcule con el tipo de cambio*/
                  v_precio_unitario_convertido = v_registros.precio_unitario; --(v_registros.precio_unitario/100);

                  --if (v_id_moneda = 2) then
                    --  v_precio_unitario = (v_precio_unitario_convertido*v_tipo_cambio);
                  --else
                      v_precio_unitario = v_precio_unitario_convertido;
                  --end if;
                  /*******************************************************************/


                  /*Verificamos si el concepto aplica a descuento*/
                  /*if ((upper(v_registros.aplica_descuento) = 'S') and ((v_registros.porcentaje_descuento/100) > 0)) then
                  		v_precio_original = ((v_precio_unitario * 100)/(100-(v_registros.porcentaje_descuento/100)));
                  else
                  		v_precio_original = v_precio_unitario;
                  end if;*/
                  /***********************************************/

                  /*Aqui calculamos el total que se desconto*/
                  	--v_total_descuento = ((v_registros.cantidad/100)*(v_precio_original - v_precio_unitario));
                  /******************************************/

                 insert into vef.tventa_detalle(
                  id_venta,
                  descripcion,
                  cantidad,
                  tipo,
                  estado_reg,
                  id_producto,
                  --id_item,
                  --id_sucursal_producto,
                  precio,
                  id_usuario_reg,
                  fecha_reg,
                  porcentaje_descuento,
                  precio_sin_descuento,
                  obs
                 -- monto_descuento
/*                  ,llave_unica*/

                  ) values(
                  v_id_venta,
                  '',--upper(v_registros.descripcion),
/*                  (v_registros.cantidad/100),*/
				   v_registros.cantidad,
                  'servicio',
                  'activo',
                  v_registros.id_concepto,
                  --v_registros.id_concepto,
                  --v_parametros.id_producto,
                  v_precio_unitario,
                  p_id_usuario,
                  now(),
                  0,
                  v_precio_unitario,
                  null
                 -- v_total_descuento
/*                  ,v_registros.llave_unica*/

                  )RETURNING id_venta_detalle into v_id_venta_detalle;

                  end loop;
                  /********************************/
                  select sum(ven.precio * ven.cantidad) into v_total_venta
                    from vef.tventa_detalle ven
                    where  ven.id_venta = v_id_venta;

                    update vef.tventa set
                      total_venta = v_total_venta,
                      total_venta_msuc = v_total_venta
                    where id_venta = v_id_venta;

                    /*Cuando se complete la informacion si todo va correctamente auqi obtenemos los demas datos*/
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
                                                                    p_id_usuario,
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
                       IF  vef.f_fun_inicio_venta_wf(p_id_usuario,
                                                    v_parametros._id_usuario_ai,
                                                    v_parametros._nombre_usuario_ai,
                                                    v_id_estado_actual,
                                                    v_id_proceso_wf,
                                                    v_codigo_estado_siguiente) THEN

                      END IF;
                      /************************************/


                      /*Controla si hay recibos posteriores*/
                          if (v_venta.tipo_base = 'computarizada' and v_es_fin = 'si') then
                            IF v_venta.tipo_factura not in ('computarizadaexpo','computarizadaexpomin','computarizadamin') THEN
                              v_fecha_venta = now()::date;
                              if (EXISTS(	select 1
                                           from vef.tventa v
                                           where v.fecha > v_fecha_venta and v.tipo_factura = 'computarizada' and
                                                 v.estado_reg = 'activo' and v.estado = 'finalizado'))THEN
                                raise exception 'Existen recibos emitidos con fechas posterior a la actual. Por favor revise la fecha y hora del sistema';
                              end if;
                            ELSE
                              v_fecha_venta = v_venta.fecha;
                            END IF;


                            select array_agg(distinct cig.id_actividad_economica) into v_id_actividad_economica
                            from vef.tventa_detalle vd
                              inner join param.tconcepto_ingas cig on cig.id_concepto_ingas = vd.id_producto
                            where vd.id_venta = v_venta.id_venta and vd.estado_reg = 'activo';

                                  if -1 = ANY(v_id_actividad_economica) is null then

                                      v_id_actividad_economica = array_agg(1);
                                  end if;
                                  /*Aumentando esta parte*/
                                  if (v_id_actividad_economica is null)then

                                      v_id_actividad_economica = array_agg(1);
                                  end if;


                                 IF v_venta.tipo_factura not in ('computarizadaexpo','computarizadaexpomin','computarizadamin') THEN
/*							 raise 'fecha_venta : % , id_sucursal: %, id_actividad_economica: %',v_venta.fecha,v_venta.id_sucursal, v_id_actividad_economica; */
                                    select d.* into v_dosificacion
                                    from vef.tdosificacion d
                                    where d.estado_reg = 'activo' and d.fecha_inicio_emi <= v_venta.fecha and
                                          d.fecha_limite >= v_venta.fecha and d.tipo = 'F' and d.tipo_generacion = 'computarizada' and
                                          d.id_sucursal = v_venta.id_sucursal and
                                          d.nombre_sistema = 'SISTEMAFACTURACIONBOA' and
                                          /*Aqui para tomar en cuenta la dosificacion diferenciando por el titulo*/
                                          d.titulo = 'FACTURA' AND
                                          /***********************************************************************/
                                          d.id_activida_economica @> v_id_actividad_economica FOR UPDATE;

                                    v_nro_factura = v_dosificacion.nro_siguiente;

                                    if (v_dosificacion is null) then
                                      raise exception 'No existe una dosificacion activa para emitir la factura';
                                    end if;


                                    --validar que el nro de factura no supere el maximo nro de factura de la dosificaiocn
                                    if (exists(	select 1
                                                 from vef.tventa ven
                                                 where ven.nro_factura =  v_dosificacion.nro_siguiente and ven.id_dosificacion = v_dosificacion.id_dosificacion)) then
                                      raise exception 'El numero de factura ya existe para esta dosificacion. Por favor comuniquese con el administrador del sistema';
                                    end if;

                                    --la factura de exportacion no altera la fecha
                                    update vef.tventa  set
                                      id_dosificacion = v_dosificacion.id_dosificacion,
                                      nro_factura = v_nro_factura,
                                      fecha = v_fecha_venta,
                                      cod_control = pxp.f_gen_cod_control(v_dosificacion.llave,
                                                                          v_dosificacion.nroaut,
                                                                          v_nro_factura::varchar,
                                                                          v_venta.nit,
                                                                          to_char(v_fecha_venta,'YYYYMMDD')::varchar,
                                                                          round(v_venta.total_venta,0))
                                    where id_venta = v_venta.id_venta;


                                    update vef.tdosificacion
                                    set nro_siguiente = nro_siguiente + 1
                                    where id_dosificacion = v_dosificacion.id_dosificacion;


                                  ELSE
                                    -- en las facturas de exportacion y minera  el numero se genera al inserta
                                    v_nro_factura =  v_venta.nro_factura;

                                    select
                                      *
                                    into  v_dosificacion
                                    from  vef.tdosificacion d where d.id_dosificacion = v_venta.id_dosificacion;


                                    --la factura de exportacion no altera la fecha
                                    update vef.tventa  set
                                      cod_control = pxp.f_gen_cod_control(v_dosificacion.llave,
                                                                          v_dosificacion.nroaut,
                                                                          v_nro_factura::varchar,
                                                                          v_venta.nit,
                                                                          to_char(v_fecha_venta,'YYYYMMDD')::varchar,
                                                                          round(v_venta.total_venta_msuc,0))
                                    where id_venta = v_venta.id_venta;


                                  END IF;

                            end if;
                      end if;

                      /*Recuperamos el codigo de control*/
                      select ven.cod_control,
                      		 ven.nro_factura ,
                             ven.id_proceso_wf
                      into v_codigo_control,
                      	   v_numero_factura,
                           v_id_proceso_wf
                      from vef.tventa ven
                      where ven.id_venta = v_venta.id_venta;


                      /*Aqui para la conversion a dolar del total*/
                      select tc.oficial
                             into
                             v_tc_erp
                      from param.ttipo_cambio tc
                      where tc.fecha = now()::date and tc.id_moneda = 2;
                      /*******************************************/


                      insert into vef.tventa_forma_pago(
                                                        usuario_ai,
                                                        fecha_reg,
                                                        id_usuario_reg,
                                                        id_usuario_ai,
                                                        estado_reg,
                                                        --id_forma_pago,
                                                        id_venta,
                                                        monto_transaccion,
                                                        monto,
                                                        cambio,
                                                        monto_mb_efectivo,
                                                        numero_tarjeta,
                                                        codigo_tarjeta,
                                                        id_auxiliar,
                                                        tipo_tarjeta,
                                                        /*Aumentamos el id_instancia*/
                                                        id_medio_pago,
                                                        id_moneda,
                                                        monto_dolar_efectivo
                                                        /****************************/
                                                      )
                                                      values(
                                                        v_parametros._nombre_usuario_ai,
                                                        now(),
                                                        p_id_usuario,
                                                        v_parametros._id_usuario_ai,
                                                        'activo',
                                                        --v_parametros.id_forma_pago,
                                                        v_venta.id_venta,
                                                        v_total_venta,
                                                        0,
                                                        0,
                                                        v_total_venta,
                                                        NULL,
                                                        NULL,
                                                        NULL,
                                                        NULL,
                                                        /*Aumentamos el id_instancia y el id_moneda*/
                                                        20,
                                                        1,
                                                        round((v_total_venta/v_tc_erp),2)
                                                        /****************************/
                                                      );


				 IF(pxp.f_get_variable_global('migrar_facturas') ='true')THEN
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
                        where usu.id_usuario = p_id_usuario;

                        p_user= 'dbkerp_'||v_cuenta_usu;


                       -- v_password=pxp.f_get_variable_global('sincronizar_password_facturacion');



                        v_semilla = pxp.f_get_variable_global('semilla_erp');


                        select md5(v_semilla||v_pass_usu) into v_password;

                        v_cadena_cnx = 'hostaddr='||v_host||' port='||v_puerto||' dbname='||v_dbname||' user='||p_user||' password='||v_password;







                    v_conexion = (SELECT dblink_connect(v_cadena_cnx));
                  /*************************************************/

                    select * FROM dblink(v_cadena_cnx,'select nextval(''sfe.tfactura_id_factura_seq'')',TRUE)AS t1(resp integer)
                    into v_id_factura;

                    /*Recuperamos el nombre del cajero que esta finalizando la factura*/
                    SELECT per.nombre_completo2 into v_cajero
                    from segu.tusuario usu
                    inner join segu.vpersona2 per on per.id_persona = usu.id_persona
                    where usu.id_usuario = p_id_usuario;
                    /******************************************************************/

                   -- IF  pxp.f_existe_parametro(p_tabla,'tipo_pv') THEN
                        v_tipo_pv= 'FAC.BOL.COMPUT.CONTABLE.DEVOLUCIONES';
                    -- ELSE
                   --     v_tipo_pv='';
                   --  END IF;

                        v_consulta = '
                                    INSERT INTO sfe.tfactura(
                                    id_factura,
                                    fecha_factura,
                                    nro_factura,
                                    nro_autorizacion,
                                    estado,
                                    nit_ci_cli,
                                    razon_social_cli,
                                    importe_total_venta,
                                    importe_otros_no_suj_iva,
                                    codigo_control,
                                    usuario_reg,
                                    tipo_factura,
                                    id_origen,
                                    sistema_origen,
                                    desc_ruta
                                    )
                                    values(
                                    '||v_id_factura||',
                                    '''||v_venta.fecha||''',
                                    '''||v_nro_factura::varchar||''',
                                    '''||v_dosificacion.nroaut::varchar||''',
                                    ''VÁLIDA'',
                                    '''||trim(v_venta.nit)::varchar||''',
                                    '''||regexp_replace(trim(v_venta.nombre_factura), '[^a-zA-ZñÑ ]+', '','g')::varchar||''',
                                    '||v_venta.total_venta::numeric||',
                                    '||v_venta.excento||',
                                    '''||pxp.f_gen_cod_control(v_dosificacion.llave,
                                                          v_dosificacion.nroaut,
                                                          v_nro_factura::varchar,
                                                          trim(v_venta.nit),
                                                          to_char(v_fecha_venta,'YYYYMMDD')::varchar,
                                                          round(v_venta.total_venta_msuc,0))||''',
                                    '''||v_cajero||''',
                                    '''||v_venta.tipo_factura||''',
                                    '||v_venta.id_venta||',
                                    ''ERP'',
                                    '''||v_tipo_pv::varchar||'''
                                    );';


                        IF(v_conexion!='OK') THEN
                              raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
                        ELSE

                          perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);

                          v_res_cone=(select dblink_disconnect());

                        END IF;
                  end if;


                /*Aqui insertamos en la alarma para que nos salga la notificacion*/
                /*if(v_parametros.enviar_correo = 'si') then
                    v_mensaje_correo = '<p>Estimado usuario su factura se encuentra adjuntada</p>';
                    v_parametros_correo = '{filtro_directo:{campo:"vef.tventa",valor:"'||v_venta.id_venta||'"}
                    										{campo:"vef.tventa",valor:"'||v_venta.id_venta||'"}}';
                    v_documento_adjunto = 'sis_ventas_facturacion/control/Cajero/reporteFacturaCarta|'||v_venta.id_venta||'|Factura.pdf';
                    v_correos = v_parametros.correo_electronico;
				 INSERT INTO param.talarma (descripcion,
                 							acceso_directo,
                                            fecha,
                                            --id_funcionario,
                                            tipo,
                                            titulo,
                                            --id_usuario,
                                            titulo_correo,
                                            correos,
                                            documentos,
                                            estado_envio,
                                            estado_comunicado,
                                            pendiente,
                                            estado_notificacion,
                                            id_usuario_reg,
                                            parametros
                                            )
                							values
                						   (v_mensaje_correo,
                                           NULL,
                                           now()::date,
                                           --null,
                                           'notificacion',
                                           '<center><h1>Factura Emi</h1></center>',
                                           p_id_usuario,
                                           'prueba envio de correo',
                                           v_correos,
                                           v_documento_adjunto,
                                           'exito',
                                           'borrador',
                                           'no',
                                           NULL,
                                           --p_id_usuario,
                                           v_parametros_correo
                                           );
                 end if;*/


           -- end if;

            if (pxp.f_existe_parametro(p_tabla,'id_liquidacion')) then
            	if (v_parametros.id_liquidacion is not null) then
                    update decr.tliquidacion
                    set id_proceso_wf_factura = v_id_proceso_wf
                    where id_liquidacion = v_parametros.id_liquidacion::integer;
                end if;
            end if;


            	--Definicion de la respuesta
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Venta registrada correctamente');
                v_resp = pxp.f_agrega_clave(v_resp,'id_proceso_wf',v_id_proceso_wf::varchar);
                v_resp = pxp.f_agrega_clave(v_resp,'cod_control',v_codigo_control::varchar);
				v_resp = pxp.f_agrega_clave(v_resp,'num_factura',v_numero_factura::varchar);
/*				v_resp = pxp.f_agrega_clave(v_resp,'cod_dosificacion',v_cod_dosificacion::varchar);*/


            --Devuelve la respuesta
            return v_resp;

		end;

        /*********************************
        #TRANSACCION:  'VEF_ANU_FAC_EXT'
        #DESCRIPCION:	Anulacion de Facturas
        #AUTOR: 		Ismael Valdivia
        #FECHA:	        23-05-2020
        ***********************************/

        elsif(p_transaccion='VEF_ANU_FAC_EXT')then

            begin

            	select ven.id_proceso_wf into v_id_proceso_wf_venta
                from vef.tventa ven
                where ven.id_venta = v_parametros.id_venta;

            	update vef.tventa SET
                estado = 'anulado'
                where id_venta = v_parametros.id_venta;

                update param.talarma set
                descripcion = '<p class="MsoNormal" style="color: rgb(0, 0, 0); font-size: 12px;"><font face="verdana" style="" color="#333399"><b style=""><span style="font-size: 9pt; line-height: 107%; background: white;">Estimado Alumno</span><span style="font-size: 9pt; line-height: 107%;"><br style="font-variant-ligatures: normal;font-variant-caps: normal;orphans: 2;
                              text-align:start;widows: 2;-webkit-text-stroke-width: 0px;text-decoration-style: initial;
                              text-decoration-color: initial;word-spacing:0px">
                              <br style="font-variant-ligatures: normal;font-variant-caps: normal;orphans: 2;
                              text-align:start;widows: 2;-webkit-text-stroke-width: 0px;text-decoration-style: initial;
                              text-decoration-color: initial;word-spacing:0px"><span style="background: white;"><span style="font-variant-ligatures: normal;
                              font-variant-caps: normal;orphans: 2;text-align:start;widows: 2;-webkit-text-stroke-width: 0px;
                              text-decoration-style: initial;text-decoration-color: initial;float:none;
                              word-spacing:0px">Se adjunta la factura anulada del pago realizado.&nbsp;</span></span><br style="font-variant-ligatures: normal;font-variant-caps: normal;orphans: 2;
                              text-align:start;widows: 2;-webkit-text-stroke-width: 0px;text-decoration-style: initial;
                              text-decoration-color: initial;word-spacing:0px">
                              <br style="font-variant-ligatures: normal;font-variant-caps: normal;orphans: 2;
                              text-align:start;widows: 2;-webkit-text-stroke-width: 0px;text-decoration-style: initial;
                              text-decoration-color: initial;word-spacing:0px">
                              <span style="background: white;"><span style="font-variant-ligatures: normal; font-variant-caps: normal; orphans: 2; text-align: start; widows: 2; -webkit-text-stroke-width: 0px; text-decoration-style: initial; text-decoration-color: initial; float: none; word-spacing: 0px;">Atentamente</span></span></span></b></font></p><span style="color: rgb(0, 0, 0); font-size: 12px;"><img src="data:image/png;base64,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" alt=""></span><p class="MsoNormal" style="color: rgb(0, 0, 0); font-size: 12px; font-family: tahoma, arial, helvetica, sans-serif;"><!--[endif]--><span style="font-size:9.0pt;line-height:107%;font-family:&quot;Tahoma&quot;,sans-serif;
                              color:black;background:white"><o:p></o:p></span></p>',
                titulo_correo = 'Factura Anulada EMI',
                sw_correo = 0
                where id_proceso_wf = v_id_proceso_wf_venta;




                --Definicion de la respuesta
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Venta anulada(a)');
                v_resp = pxp.f_agrega_clave(v_resp,'Anulacion','Anulacion correctamente');

                --Devuelve la respuesta
                return v_resp;

            end;

	/*********************************
 	#TRANSACCION:  'VEF_INS_FAC_EXT_MOD'
 	#DESCRIPCION:	Insercion de registros
    #AUTOR: 		Ismael Valdivia
    #FECHA:	        23-05-2020
	***********************************/

	elsif(p_transaccion='VEF_INS_FAC_EXT_MOD')then

		begin
			--Sentencia de la modificacion
			update cola.tprioridad set
			nombre = v_parametros.nombre,
   			sigla = v_parametros.sigla,
			descripcion = v_parametros.descripcion,
			estado = v_parametros.estado,
			peso = v_parametros.peso,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_prioridad=v_parametros.id_prioridad;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Prioridad modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_prioridad',v_parametros.id_prioridad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VEF_INS_FAC_EXT_ELI'
 	#DESCRIPCION:	Insercion de registros
    #AUTOR: 		Ismael Valdivia
    #FECHA:	        23-05-2020
	***********************************/

	elsif(p_transaccion='VEF_INS_FAC_EXT_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from cola.tprioridad
            where id_prioridad=v_parametros.id_prioridad;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Prioridad eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_prioridad',v_parametros.id_prioridad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

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

ALTER FUNCTION vef.ft_facturacion_externa_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
