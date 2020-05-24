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
                  upper(replace(v_parametros.razon_social,' ','')),
                  replace(v_parametros.nit_cliente,' ','')
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


        /*Aqui recuperamos el id de la sucursal*/

           select pt.id_punto_venta,
            	   pt.id_sucursal
            into v_id_punto_venta,
                 v_id_sucursal
            from vef.tpunto_venta pt
            where pt.nombre = v_parametros.punto_venta;


          if (v_id_punto_venta IS NOT NULL) then
            	select pv.codigo into v_codigo_tabla
            	from vef.tpunto_venta pv
            	where id_punto_venta = v_id_punto_venta;
          else
            	select pv.codigo into v_codigo_tabla
            	from vef.tsucursal pv
            	where id_sucursal = v_id_sucursal;
          end if;


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

        --fin obtener correlativo


        	--Sentencia de la insercion
        		--Sentencia de la insercion
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
          forma_pedido


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
          'REGISTRO FACTURA EXTERNA',
          v_num_ven,
          'computarizada',
          now(),
          NULL,
          null,
          0,--Excento por el momento 0
          1,
          0,
          0,
          0,
          0,
          0,
          0,
          0,
          0,
          '',
          v_parametros.nit_cliente,
          v_parametros.razon_social,
          NULL,
          v_hora_estimada,
          'no',
          'externa'

        );


        /*Aqui insertamos el detalle de la venta*/
            for v_registros in (select *
            					from json_populate_recordset(null::vef.detalle_venta,v_parametros.json_venta_detalle::json))loop


           insert into vef.tventa_detalle(
			id_venta,
			descripcion,
			cantidad,
			tipo,
			estado_reg,
			id_producto,
            id_item,
			--id_sucursal_producto,
			precio,
            id_usuario_reg,
            fecha_reg

          	) values(
			v_id_venta,
			'',
			1,
			'servicio',
			'activo',
            v_registros.id_concepto,
            v_registros.id_concepto,
			--v_parametros.id_producto,
			v_registros.precio,
            p_id_usuario,
            now()

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


                   IF v_venta.tipo_factura not in ('computarizadaexpo','computarizadaexpomin','computarizadamin') THEN

                      select d.* into v_dosificacion
                      from vef.tdosificacion d
                      where d.estado_reg = 'activo' and d.fecha_inicio_emi <= v_venta.fecha and
                            d.fecha_limite >= v_venta.fecha and d.tipo = 'F' and d.tipo_generacion = 'computarizada' and
                            d.id_sucursal = v_venta.id_sucursal and
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

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Venta registrada correctamente');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_id_venta::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_punto_venta',v_id_punto_venta::varchar);

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
