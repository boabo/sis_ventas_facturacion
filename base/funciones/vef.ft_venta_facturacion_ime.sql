CREATE OR REPLACE FUNCTION vef.ft_venta_facturacion_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_venta_facturacion_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tventa'
 AUTOR: 		 (ivaldivia)
 FECHA:	        10-05-2019 19:08:47
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				10-05-2019 19:08:47								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tventa'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;


    /***********Aumento de variables**************/
	v_id_venta	integer;
    v_id_cliente			integer;
    v_nombre_factura		varchar;
    v_id_cliente_destino    integer;
    v_hora_estimada_entrega	time;
    v_tiene_formula			varchar;
    v_forma_pedido			varchar;
    v_id_periodo			integer;
    v_codigo_tabla			varchar;
    v_id_moneda_venta		integer;
    v_tipo_factura			varchar;
    v_tipo_base				varchar;
    v_excento				numeric;
    v_fecha					date;
    v_nro_factura			integer;
    v_id_dosificacion		integer;
    v_id_sucursal			integer;
    v_num_ven				varchar;
    v_porcentaje_descuento	integer;
    v_id_proceso_wf			integer;
    v_id_estado_wf			integer;
    v_id_vendedor_medico	varchar;
    v_id_punto_venta		integer;
    v_fecha_estimada_entrega date;
    v_codigo_estado			varchar;
    v_id_gestion			integer;
    v_id_funcionario_inicio	integer;
    v_codigo_proceso		varchar;
    v_transporte_fob		numeric;
    v_seguros_fob			numeric;
    v_otros_fob				numeric;
    v_transporte_cif		numeric;
    v_seguros_cif			numeric;
    v_otros_cif				numeric;
    v_valor_bruto			numeric;
    v_descripcion_bulto		varchar;
    v_tipo_cambio_venta		numeric;
    v_num_tramite			varchar;
    v_a_cuenta				numeric;
    v_comision				numeric;
    v_apertura				varchar;

    v_id_tipo_estado		integer;
    v_id_funcionario		integer;
    v_estado_finalizado		integer;
    v_venta					record;
    v_tabla					varchar;
    v_codigo_estado_siguiente varchar;
    v_es_fin				varchar;
    v_id_depto				integer;
    v_obs					text;
    v_acceso_directo		varchar;
    v_clase					varchar;
    v_parametros_ad			varchar;
    v_tipo_noti				varchar;
    v_titulo				varchar;
    v_id_estado_actual		integer;
    v_fecha_venta 			date;

    v_formula				record;
    v_registros				record;
    v_codigo_tarjeta		varchar;
    v_res					varchar;
    v_id_actividad_economica	integer[];
    v_dosificacion			record;
    v_tipo_punto_venta		varchar;
    v_total_venta			numeric;

    v_respaldo				record;
    v_tipo_usuario			varchar;

    v_informe				text;
    v_anulado				varchar;
    vef_estados_validar_fp	varchar;
    v_id_moneda_suc			integer;
    v_cantidad				integer;
    v_cantidad_fp			integer;
    v_acumulado_fp			numeric;
    v_monto_fp				numeric;
    v_suma_fp				numeric;
    v_suma_det				numeric;
    v_total_venta_ms		numeric;
    v_reg_tipo_desc			record;
    /********************************************/

BEGIN

    v_nombre_funcion = 'vef.ft_venta_facturacion_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_fact_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:08:47
	***********************************/

	if(p_transaccion='VF_fact_INS')then

        begin

        /*********************Inserccion de cliente si existe o no******************************/
         if (pxp.f_is_positive_integer(v_parametros.id_cliente)) THEN
          v_id_cliente = v_parametros.id_cliente::integer;

          update vef.tcliente
          set nit = v_parametros.nit
          where id_cliente = v_id_cliente;

          select c.nombre_factura into v_nombre_factura
          from vef.tcliente c
          where c.id_cliente = v_id_cliente;
        else
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
            upper(v_parametros.id_cliente),
            v_parametros.nit
          ) returning id_cliente into v_id_cliente;

          v_nombre_factura = v_parametros.id_cliente;

        end if;

        v_id_cliente_destino = null;
        /***************************************************************************************/

        /******************Obtenemos el id periodo**********************/
         v_tiene_formula = 'no';
        --obtener correlativo
        select id_periodo into v_id_periodo from
          param.tperiodo per
        where per.fecha_ini <= now()::date
              and per.fecha_fin >=  now()::date
        limit 1 offset 0;
		/**************************************************************/

          /*****************Recuperamos el codigo del punto de venta**********************/
          if (pxp.f_existe_parametro(p_tabla,'id_punto_venta')) then
            	select pv.codigo into v_codigo_tabla
            	from vef.tpunto_venta pv
            	where id_punto_venta = v_parametros.id_punto_venta;
          else
            	select pv.codigo into v_codigo_tabla
            	from vef.tsucursal pv
            	where id_sucursal = v_parametros.id_sucursal;
          end if;
          /******************************************************************************/

        /***********************Recuperamos el id_moneda*******************************/
          if (pxp.f_existe_parametro(p_tabla,'id_moneda')) then
            	v_id_moneda_venta = v_parametros.id_moneda;
          else
            	if (v_parametros.id_sucursal is not null ) then
              		select sm.id_moneda into v_id_moneda_venta
              		from vef.tsucursal_moneda sm
              		where sm.id_sucursal = v_parametros.id_sucursal
                    and sm.estado_reg = 'activo' and sm.tipo_moneda = 'moneda_base';
            	else
              		select sm.id_moneda into v_id_moneda_venta
              		from vef.tsucursal_moneda sm
                	inner join vef.tpunto_venta pv on pv.id_sucursal = sm.id_sucursal
              		where pv.id_punto_venta = v_parametros.id_punto_venta
                    and sm.estado_reg = 'activo' and sm.tipo_moneda = 'moneda_base';
            	end if;
          end if;
		/******************************************************************************/

         if (pxp.f_existe_parametro(p_tabla,'tipo_factura')) then
         	v_tipo_factura = v_parametros.tipo_factura;
         else
            v_tipo_factura = 'computarizada';
         end if;


          if(v_tipo_factura = '') then
            v_tipo_factura = 'recibo';
          end if;

          SELECT tv.tipo_base into v_tipo_base
          from vef.ttipo_venta tv
          where tv.codigo = v_tipo_factura and tv.estado_reg = 'activo';


          if (v_tipo_base is null) then
            raise exception 'No existe un tipo de venta con el codigo: % consulte con el administrador del sistema',v_tipo_factura;
          end if;

        	v_excento = 0;

        	if (v_tipo_base = 'computarizada') THEN
          	v_fecha = now()::date;

        	ELSIF(v_tipo_base = 'manual') then
          	v_fecha = v_parametros.fecha;
          	v_nro_factura = v_parametros.nro_factura;
          	v_excento = v_parametros.excento;
          	v_id_dosificacion = v_parametros.id_dosificacion;

            --validaciones de factura manual
            --validar que no exista el mismo nro para la dosificacion
            if (exists(	select 1
                         from vef.tventa ven
                         where ven.nro_factura = v_parametros.nro_factura::integer and ven.id_dosificacion = v_parametros.id_dosificacion)) then
              raise exception 'Ya existe el mismo numero de factura en otra venta y con la misma dosificacion. Por favor revise los datos';
            end if;

            --validar que el nro de factura no supere el maximo nro de factura de la dosificaiocn
            if (exists(	select 1
                         from vef.tdosificacion dos
                         where v_parametros.nro_factura::integer > dos.final and dos.id_dosificacion = v_parametros.id_dosificacion)) then
              raise exception 'El numero de factura supera el maximo permitido para esta dosificacion';
            end if;

            --validar que la fecha de factura no sea superior a la fecha limite de emision
            if (exists(	select 1
                         from vef.tdosificacion dos
                         where dos.fecha_limite < v_parametros.fecha and dos.id_dosificacion = v_parametros.id_dosificacion)) then
              raise exception 'La fecha de la factura supera la fecha limite de emision de la dosificacion';
            end if;

          ELSE
            IF   v_tipo_factura in ('computarizadaexpo','computarizadaexpomin','computarizadamin')  THEN
              -- la fecha es abierta
              v_fecha = v_parametros.fecha;

            ELSE
              v_fecha = now()::date;
              v_excento = v_parametros.excento;
            END IF;

          end if;

          /*----------------------------------------------*/
          if (pxp.f_existe_parametro(p_tabla,'id_punto_venta')) then
          	  v_id_punto_venta = v_parametros.id_punto_venta;
          else
          	  v_id_punto_venta = NULL;

        end if;

         /*************************Obtenemso el correlativo de la venta***********************************/
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
               v_parametros.id_sucursal
             end),
            v_codigo_tabla
        	);

        --fin obtener correlativo
          IF (v_num_ven is NULL or v_num_ven ='') THEN
            raise exception 'No se pudo obtener un numero correlativo para la venta consulte con el administrador';
          END IF;

        v_porcentaje_descuento = 0;

        /********************Verificar si existe porcentaje de descuento*****************************************/
          if (pxp.f_existe_parametro(p_tabla,'porcentaje_descuento')) then
            v_porcentaje_descuento = v_parametros.porcentaje_descuento;
          end if;

          /*******************Verificar si existe el id_vendedor_medico********************************************/
          v_id_vendedor_medico = NULL;
          if (pxp.f_existe_parametro(p_tabla,'id_vendedor_medico')) then
            v_id_vendedor_medico = v_parametros.id_vendedor_medico;
          end if;

          /******************Si el id_punto_venta es null tomamos el id_sucursal********************************/
          if (v_id_punto_venta is not null) then
            select id_sucursal into v_id_sucursal
            from vef.tpunto_venta
            where id_punto_venta = v_id_punto_venta;
          else
            v_id_sucursal = v_parametros.id_sucursal;
          end if;
          /******************************************************************************************************/

          /*****************Verificamos si existe el parametros a_cuenta******************************/
          if (pxp.f_existe_parametro(p_tabla,'a_cuenta')) then
            v_a_cuenta = v_parametros.a_cuenta;
          else
            v_a_cuenta = 0;
          end if;
          /*****************************************************************************************/

		  /*******************Verificamos si existe comision****************************************/
          if (pxp.f_existe_parametro(p_tabla,'comision')) then
            v_comision = v_parametros.comision;
          else
            v_comision = 0;
          end if;
          /***************************************************************************************/

          /*****************************Verificamos si existe fecha_estimada_entrega**************************************/
          if (pxp.f_existe_parametro(p_tabla,'fecha_estimada_entrega')) then
            v_fecha_estimada_entrega = v_parametros.fecha_estimada_entrega;
            if (v_fecha_estimada_entrega is not null) then
              v_tiene_formula = 'si';
            else
              v_fecha_estimada_entrega = now();
            end if;
          else
            v_fecha_estimada_entrega = now();
          end if;
          /***********************************************************************************/

          /************************Verificamos si existe hora_estimada_entrega**************************************/
          if (pxp.f_existe_parametro(p_tabla,'hora_estimada_entrega')) then

            if (v_parametros.hora_estimada_entrega is not null and v_parametros.hora_estimada_entrega != '') then
              v_hora_estimada_entrega = (v_parametros.hora_estimada_entrega || ':00')::time;
            else
              v_hora_estimada_entrega = NULL;
            end if;
          else
            v_hora_estimada_entrega = now()::time;
          end if;
          /*********************************************************************************************************/

          /**********************Verificamos si existe forma_pedido************************************************/
          if (pxp.f_existe_parametro(p_tabla,'forma_pedido')) then
            v_forma_pedido = v_parametros.forma_pedido;
          else
            v_forma_pedido =NULL;
          end if;
          /*******************************************************************************************************/

        /*************************Obtenemos la gestion apartir de la fecha actual***************************/
          select id_gestion into v_id_gestion
          from param.tgestion
          where gestion = extract(year from now())::integer;
        /***************************************************************************************************/

          select nextval('vef.tventa_id_venta_seq') into v_id_venta;

          v_codigo_proceso = 'VEN-' || v_id_venta;
       	  -- inciiar el tramite en el sistema de WF

          select f.id_funcionario into  v_id_funcionario_inicio
          from segu.tusuario u
            inner join orga.tfuncionario f on f.id_persona = u.id_persona
          where u.id_usuario = p_id_usuario;


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
              v_id_funcionario_inicio,
              NULL,
              NULL,
              v_codigo_proceso);
		/****************************************************************************************************************************/
          if (pxp.f_existe_parametro(p_tabla,'transporte_fob')) then
            v_transporte_fob = v_parametros.transporte_fob;
            v_seguros_fob = v_parametros.seguros_fob;
            v_otros_fob = v_parametros.otros_fob;
            v_transporte_cif = v_parametros.transporte_cif;
            v_seguros_cif = v_parametros.seguros_cif;
            v_otros_cif = v_parametros.otros_cif;
            v_valor_bruto = v_parametros.valor_bruto;
            v_descripcion_bulto = v_parametros.descripcion_bulto;
          end if;

          if (pxp.f_existe_parametro(p_tabla,'tipo_cambio_venta')) then
            v_tipo_cambio_venta = v_parametros.tipo_cambio_venta;
          end if;

        	--Sentencia de la insercion
        insert into vef.tventa(
          id_venta,
          id_cliente,
          id_sucursal,
          id_proceso_wf,
          id_estado_wf,
          estado_reg,
          nro_tramite,
          a_cuenta,
          fecha_estimada_entrega,
          usuario_ai,
          fecha_reg,
          id_usuario_reg,
          id_usuario_ai,
          id_usuario_mod,
          fecha_mod,
          estado,
          id_punto_venta,
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
          v_a_cuenta,
          v_fecha_estimada_entrega,
          v_parametros._nombre_usuario_ai,
          now(),
          p_id_usuario,
          v_parametros._id_usuario_ai,
          null,
          null,
          v_codigo_estado,
          v_id_punto_venta,
          v_id_vendedor_medico,
          v_porcentaje_descuento,
          v_comision,
          upper(v_parametros.observaciones),
          v_num_ven,
          v_tipo_factura,
          v_fecha,
          v_nro_factura,
          v_id_dosificacion,
          v_excento,
          v_id_moneda_venta,
          COALESCE(v_transporte_fob,0),
          COALESCE(v_seguros_fob,0),
          COALESCE(v_otros_fob,0),
          COALESCE(v_transporte_cif,0),
          COALESCE(v_seguros_cif,0),
          COALESCE(v_otros_cif,0),
          COALESCE(v_tipo_cambio_venta,0),
          COALESCE(v_valor_bruto,0),
          COALESCE(v_descripcion_bulto,''),
          v_parametros.nit,
          v_nombre_factura,
          v_id_cliente_destino,
          v_hora_estimada_entrega,
          v_tiene_formula,
          v_forma_pedido

        ) returning id_venta into v_id_venta;

        if ( v_parametros.id_formula is not null) then

          for v_formula  in	(select form.id_concepto_ingas
                              from vef.tformula_detalle form
                              where form.id_formula = v_parametros.id_formula) LOOP

          insert into vef.tventa_detalle
          ( id_usuario_reg,
            fecha_reg,
          	estado_reg,
            id_venta,
            id_formula,
            tipo,
            precio,
            cantidad,
            precio_sin_descuento,
            porcentaje_descuento,
            bruto,
            ley,
            kg_fino,
            estado,
            id_producto
          )VALUES(
            p_id_usuario,
            now(),
            'activo',
            v_id_venta,
            v_parametros.id_formula,
            'formula',
            1,
            1,
            1,
            0,
            0,
            0,
            0,
            'registrado',
            v_formula.id_concepto_ingas
          );

          end loop;

          select sum(ven.precio * ven.cantidad) into v_total_venta
          from vef.tventa_detalle ven
          where  ven.id_venta = v_id_venta;

          update vef.tventa set
          	total_venta = v_total_venta,
          	total_venta_msuc = v_total_venta
          where id_venta = v_id_venta;

        end if;





			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Factura Computarizada almacenado(a) con exito (id_venta'||v_id_venta||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_id_venta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VF_fact_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:08:47
	***********************************/

	elsif(p_transaccion='VF_fact_MOD')then

		begin

			--Sentencia de la modificacion
			update vef.tventa set
			id_cliente = v_parametros.id_cliente,
			observaciones = v_parametros.observaciones,
			nit = v_parametros.nit
			where id_venta=v_parametros.id_venta;

            if ( v_parametros.id_formula is not null) then

              delete from vef.tventa_detalle
              where id_venta =  v_parametros.id_venta;


              for v_formula  in	(select form.id_concepto_ingas
                                  from vef.tformula_detalle form
                                  where form.id_formula = v_parametros.id_formula) LOOP

                  insert into vef.tventa_detalle
                  ( id_usuario_reg,
                    fecha_reg,
                    estado_reg,
                    id_venta,
                    id_formula,
                    tipo,
                    precio,
                    cantidad,
                    precio_sin_descuento,
                    porcentaje_descuento,
                    bruto,
                    ley,
                    kg_fino,
                    estado,
                    id_producto
                  )VALUES(
                    p_id_usuario,
                    now(),
                    'activo',
                    v_parametros.id_venta,
                    v_parametros.id_formula,
                    'formula',
                    1,
                    1,
                    1,
                    0,
                    0,
                    0,
                    0,
                    'registrado',
                    v_formula.id_concepto_ingas
                  );

              end loop;

              select sum(ven.precio * ven.cantidad) into v_total_venta
              from vef.tventa_detalle ven
              where  ven.id_venta = v_parametros.id_venta;

              update vef.tventa set
                total_venta = v_total_venta,
                total_venta_msuc = v_total_venta
              where id_venta = v_parametros.id_venta;

            end if;



			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Factura Computarizada modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_parametros.id_venta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;


    /*********************************
 	#TRANSACCION:  'VF_APERCOUN_IME'
 	#DESCRIPCION:	Obtener Apertura
 	#AUTOR:		ivaldivia
 	#FECHA:		25-06-2019 15:40:57
	***********************************/

	elsif(p_transaccion='VF_APERCOUN_IME')then

		begin
			--Sentencia de la modificacion
			--raise exception 'llega aqui punto_venta:%. sucursal:%.',v_parametros.id_punto_venta,v_parametros.id_sucursal;
            v_fecha = now ()::date;

            if (v_parametros.id_punto_venta is not null) then

                select acc.estado into v_apertura
                from vef.tapertura_cierre_caja acc
                where acc.fecha_apertura_cierre = v_fecha and
                acc.estado_reg = 'activo' and
                acc.id_punto_venta = v_parametros.id_punto_venta::integer;

                	if (v_apertura is null or v_apertura = '') then
                    	v_apertura = 'SIN APERTURA DE CAJA';
                    else
                    	v_apertura = v_apertura;

                    end if;
            else
            	select acc.estado into v_apertura
                from vef.tapertura_cierre_caja acc
                where acc.fecha_apertura_cierre = v_fecha and
                acc.estado_reg = 'activo' and
                acc.id_sucursal = v_parametros.id_sucursal::integer;

                	if (v_apertura is null or v_apertura = '') then
                    	v_apertura = 'SIN APERTURA DE CAJA';
                    else
                    	v_apertura = v_apertura;

                    end if;


            end if;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Factura Computarizada modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'v_apertura',v_apertura::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'VF_APERTURA_IME'
 	#DESCRIPCION:	Obtener Apertura
 	#AUTOR:		ivaldivia
 	#FECHA:		25-06-2019 15:40:57
	***********************************/

	elsif(p_transaccion='VF_APERTURA_IME')then

		begin
			--Sentencia de la modificacion
			--raise exception 'llega aqui punto_venta:%. sucursal:%.',v_parametros.id_punto_venta,v_parametros.id_sucursal;
            v_fecha = now ()::date;

            if (v_parametros.id_punto_venta is not null) then

                select acc.estado into v_apertura
                from vef.tapertura_cierre_caja acc
                where acc.fecha_apertura_cierre = v_fecha and
                acc.estado_reg = 'activo' and
                acc.id_usuario_reg = p_id_usuario and
                acc.id_punto_venta = v_parametros.id_punto_venta::integer;

                	if (v_apertura is null or v_apertura = '') then
                    	v_apertura = 'SIN APERTURA DE CAJA';
                    else
                    	v_apertura = v_apertura;

                    end if;

            	select pven.tipo into v_tipo_punto_venta
                from vef.tpunto_venta pven
                where pven.id_punto_venta = v_parametros.id_punto_venta::integer;


            else
            	select acc.estado into v_apertura
                from vef.tapertura_cierre_caja acc
                where acc.fecha_apertura_cierre = v_fecha and
                acc.estado_reg = 'activo' and
                acc.id_usuario_reg = p_id_usuario and
                acc.id_sucursal = v_parametros.id_sucursal::integer;

                	if (v_apertura is null or v_apertura = '') then
                    	v_apertura = 'SIN APERTURA DE CAJA';
                    else
                    	v_apertura = v_apertura;

                    end if;

                select pven.tipo into v_tipo_punto_venta
                from vef.tpunto_venta pven
                where pven.id_sucursal = v_parametros.id_sucursal::integer;


            end if;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Factura Computarizada modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'v_apertura',v_apertura::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_tipo_punto_venta',v_tipo_punto_venta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;


    /*********************************
 	#TRANSACCION:  'VEF_ENVIARCAJA_IME'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		25-06-2019 18:15:00
	***********************************/

	elsif(p_transaccion='VEF_ENVIARCAJA_IME')then

		begin
			--Sentencia de la modificacion
        	/*Recuperamos el id_tipo_estado y el id_estado_wf*/
       select
          ew.id_tipo_estado ,
          ew.id_estado_wf,
          ew.id_funcionario
        into
          v_id_tipo_estado,
          v_id_estado_wf,
          v_id_funcionario
        from wf.testado_wf ew
          inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
        where ew.id_estado_wf =  v_parametros.id_estado_wf_act;
       /********************************************************/

       /*Obtenemos los campos de venta el id_entidad y el tipo_base*/
        select v.*,s.id_entidad,tv.tipo_base into v_venta
        from vef.tventa v
          inner join vef.tsucursal s on s.id_sucursal = v.id_sucursal
          inner join vef.tcliente c on c.id_cliente = v.id_cliente
          inner join vef.ttipo_venta tv on tv.codigo = v.tipo_factura and tv.estado_reg = 'activo'
        where v.id_proceso_wf = v_parametros.id_proceso_wf_act;
        /***********************************************************/

        /*Obtenemos el id del estado finalizado*/
        v_estado_finalizado = (v_id_tipo_estado+3);
        /****************************************/


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
        /*************************************************/

       	--v_resp = vef.ft_venta_ime(p_administrador,p_id_usuario,v_tabla,'VF_VENVALI_MOD');

        /*Obtenemos el codigo finalizado y fin*/
       	select
          te.codigo,te.fin
        into
          v_codigo_estado_siguiente,v_es_fin
        from wf.ttipo_estado te
        where te.id_tipo_estado = v_estado_finalizado;
        /*********************************************************************/


        /*Verificcar si seran necesario*/
      	IF  pxp.f_existe_parametro(p_tabla,'id_depto_wf') THEN

          v_id_depto = v_parametros.id_depto_wf;

        END IF;

        IF  pxp.f_existe_parametro(p_tabla,'obs') THEN
            v_obs=v_parametros.obs;
        ELSE
          v_obs='---';
        END IF;
       /***************************************************************/


        --configurar acceso directo para la alarma
        v_acceso_directo = '';
        v_clase = '';
        v_parametros_ad = '';
        v_tipo_noti = 'notificacion';
        v_titulo  = 'Visto Bueno';

        -- hay que recuperar el supervidor que seria el estado inmediato,...
        v_id_estado_actual =  wf.f_registra_estado_wf(v_estado_finalizado /*tengpo*/,
                                                      v_id_funcionario/*recuperar*/,
                                                      v_parametros.id_estado_wf_act /*tengo*/,
                                                      v_parametros.id_proceso_wf_act/*tengo*/,
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
                                      v_parametros.id_proceso_wf_act,
                                      v_codigo_estado_siguiente) THEN

        END IF;
		/************************************/





        -- si hay mas de un estado disponible  preguntamos al usuario
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado de la planilla)');
        v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');
        v_resp = pxp.f_agrega_clave(v_resp,'estado',v_codigo_estado_siguiente);

        -- Devuelve la respuesta
        return v_resp;





      end;

	/*********************************
 	#TRANSACCION:  'VF_fact_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:08:47
	***********************************/

	elsif(p_transaccion='VF_fact_ELI')then


		begin
			--Sentencia de la eliminacion

            delete from vef.tventa_forma_pago
            where id_venta=v_parametros.id_venta;

            delete from vef.tventa_detalle
            where id_venta=v_parametros.id_venta;

			delete from vef.tventa
            where id_venta=v_parametros.id_venta;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Factura Computarizada eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_parametros.id_venta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
     #TRANSACCION:  'VF_FACFORPA_ELI'
     #DESCRIPCION:	Eliminacion de formas de pago relacionadas a una venta
     #AUTOR:		admin
     #FECHA:		01-06-2015 05:58:00
    ***********************************/

    elsif(p_transaccion='VF_FACFORPA_ELI')then

      begin
        --Sentencia de la eliminacion
        delete from vef.tventa_forma_pago
        where id_venta=v_parametros.id_venta;

        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Ventas forma de pago eliminado(a)');
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_parametros.id_venta::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;

    /*********************************
     #TRANSACCION:  'VF_VENDET_ELI'
     #DESCRIPCION:	Eliminacion de los detalles relacionados a una venta
     #AUTOR:		admin
     #FECHA:		01-06-2015 05:58:00
    ***********************************/

    elsif(p_transaccion='VF_VENDET_ELI')then

      begin
        --Sentencia de la eliminacion
        delete from vef.tventa_detalle
        where id_venta=v_parametros.id_venta;

        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Ventas detalle eliminado(a)');
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_parametros.id_venta::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;

    /*********************************
     #TRANSACCION:  'VF_FACVEN_INS'
     #DESCRIPCION:	Insercion de registros
     #AUTOR:		admin
     #FECHA:		01-06-2015 05:58:00
    ***********************************/

    elsif(p_transaccion='VF_FACVEN_INS')then

      begin
        if (v_parametros.id_forma_pago is not null and v_parametros.id_forma_pago != 0) then

        select fp.codigo into v_codigo_tarjeta
                from obingresos.tforma_pago fp
                where fp.id_forma_pago = v_parametros.id_forma_pago;

        v_codigo_tarjeta = (case when v_codigo_tarjeta like 'CC%' or v_codigo_tarjeta like 'SF%' then
                                        substring(v_codigo_tarjeta from 3 for 2)
                                else
                                      NULL
                              end);

      	if (v_codigo_tarjeta is not null) then
                    if (substring(v_parametros.numero_tarjeta::varchar from 1 for 1) != 'X') then
                		v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta::varchar,v_codigo_tarjeta);
                	end if;
                end if;
        end if;
        --raise exception 'llekga el mco %',v_parametros.mco;
        if (left (v_parametros.mco,3)  <> '930' and v_parametros.mco <> '')then
            raise exception 'El numero del MCO tiene que empezar con 930';
            end if;

        if (char_length(v_parametros.mco::varchar) <> 15 and v_parametros.mco <> '' ) then
            raise exception 'El numero del MCO debe tener 15 digitos obligatorios, 930000000012345';
            end if;


        v_tiene_formula = 'no';
        --obtener correlativo
        select id_periodo into v_id_periodo from
          param.tperiodo per
        where per.fecha_ini <= now()::date
              and per.fecha_fin >=  now()::date
        limit 1 offset 0;

        if (pxp.f_existe_parametro(p_tabla,'id_punto_venta')) then
          select pv.codigo into v_codigo_tabla
          from vef.tpunto_venta pv
          where id_punto_venta = v_parametros.id_punto_venta;
        else
          select pv.codigo into v_codigo_tabla
          from vef.tsucursal pv
          where id_sucursal = v_parametros.id_sucursal;
        end if;

        if (pxp.f_existe_parametro(p_tabla,'id_moneda')) then
          v_id_moneda_venta = v_parametros.id_moneda;
        else
          if (v_parametros.id_sucursal is not null ) then
            select sm.id_moneda into v_id_moneda_venta
            from vef.tsucursal_moneda sm
            where sm.id_sucursal = v_parametros.id_sucursal
                  and sm.estado_reg = 'activo' and sm.tipo_moneda = 'moneda_base';
          else
            select sm.id_moneda into v_id_moneda_venta
            from vef.tsucursal_moneda sm
              inner join vef.tpunto_venta pv on pv.id_sucursal = sm.id_sucursal
            where pv.id_punto_venta = v_parametros.id_punto_venta
                  and sm.estado_reg = 'activo' and sm.tipo_moneda = 'moneda_base';
          end if;
        end if;

       if (pxp.f_existe_parametro(p_tabla,'tipo_factura')) then
          v_tipo_factura = v_parametros.tipo_factura;
        else
          v_tipo_factura = 'computarizada';
        end if;

      if(v_tipo_factura = '') then
      	v_tipo_factura = 'computarizada';
      end if;


      /*Aumentando condicion para verificar el estado y el informe en facturacion manual*/
      if(v_tipo_factura = 'manual') then
      	if (pxp.f_existe_parametro(p_tabla,'informe')) then
        	 v_informe = v_parametros.informe;
        end if;

        if (pxp.f_existe_parametro(p_tabla,'anulado')) then
        	 v_anulado = v_parametros.anulado;
        end if;

      else
        v_informe = 'NINGUNO';
        v_anulado = 'NO';
      end if;

      /**********************************************************************************/



        SELECT tv.tipo_base into v_tipo_base
        from vef.ttipo_venta tv
        where tv.codigo = v_tipo_factura and tv.estado_reg = 'activo';


        if (v_tipo_base is null) then
          raise exception 'No existe un tipo de venta con el codigo: % consulte con el administrador del sistema',v_tipo_factura;
        end if;

        v_excento = 0;
        if (v_tipo_base = 'recibo') THEN
          v_fecha = now()::date;
        ELSIF(v_tipo_base = 'manual') then
          v_fecha = v_parametros.fecha;
          v_nro_factura = v_parametros.nro_factura;
          --v_excento = v_parametros.excento;
          v_id_dosificacion = v_parametros.id_dosificacion;

          --validaciones de factura manual
          --validar que no exista el mismo nro para la dosificacion
          if (exists(	select 1
                       from vef.tventa ven
                       where ven.nro_factura = v_parametros.nro_factura::integer and ven.id_dosificacion = v_parametros.id_dosificacion)) then
            raise exception 'Ya existe el mismo numero de factura en otra venta y con la misma dosificacion. Por favor revise los datos';
          end if;

          --validar que el nro de factura no supere el maximo nro de factura de la dosificaiocn
          if (exists(	select 1
                       from vef.tdosificacion dos
                       where v_parametros.nro_factura::integer > dos.final and dos.id_dosificacion = v_parametros.id_dosificacion)) then
            raise exception 'El numero de factura supera el maximo permitido para esta dosificacion';
          end if;

          --validar que la fecha de factura no sea superior a la fecha limite de emision
          if (exists(	select 1
                       from vef.tdosificacion dos
                       where dos.fecha_limite < v_parametros.fecha and dos.id_dosificacion = v_parametros.id_dosificacion)) then
            raise exception 'La fecha de la factura supera la fecha limite de emision de la dosificacion';
          end if;

        ELSE
          IF   v_tipo_factura in ('computarizadaexpo','computarizadaexpomin','computarizadamin')  THEN
            -- la fecha es abierta
            v_fecha = v_parametros.fecha;

          ELSE
            v_fecha = now()::date;
           -- v_excento = v_parametros.excento;
          END IF;

        end if;
        if (pxp.f_existe_parametro(p_tabla,'id_punto_venta')) then
          v_id_punto_venta = v_parametros.id_punto_venta;

          if (exists(	select 1
                       from vef.tapertura_cierre_caja acc
                       where acc.fecha_apertura_cierre = v_fecha and
                             acc.estado_reg = 'activo' and acc.estado = 'cerrado' and
                             acc.id_punto_venta = v_parametros.id_punto_venta)) then
            raise exception 'La caja ya fue cerrada, el cajero necesita tener la caja abierta para poder registrar la venta';
          end if;


          if (not exists(	select 1
                           from vef.tapertura_cierre_caja acc
                           where acc.fecha_apertura_cierre = v_fecha and
                                 acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                                 acc.id_punto_venta = v_parametros.id_punto_venta)) then
            raise exception 'Antes de registrar una venta el cajero debe realizar una apertura de caja';
          end if;

        else
          v_id_punto_venta = NULL;

          if (exists(	select 1
                       from vef.tapertura_cierre_caja acc
                       where acc.fecha_apertura_cierre = v_fecha and
                             acc.estado_reg = 'activo' and acc.estado = 'cerrado' and
                             acc.id_sucursal = v_parametros.id_sucursal)) then
            raise exception 'La caja ya fue cerrada, el cajero necesita tener la caja abierta para poder registrar la venta';
          end if;


          if (not exists(	select 1
                           from vef.tapertura_cierre_caja acc
                           where acc.fecha_apertura_cierre = v_fecha and
                                 acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                                 acc.id_sucursal = v_parametros.id_sucursal)) then
            raise exception 'Antes de registrar una venta el cajero debe realizar una apertura de caja';
          end if;

        end if;

        -- obtener correlativo
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
               v_parametros.id_sucursal
             end),
            v_codigo_tabla
        );

        --fin obtener correlativo
        IF (v_num_ven is NULL or v_num_ven ='') THEN
          raise exception 'No se pudo obtener un numero correlativo para la venta consulte con el administrador';
        END IF;

        v_porcentaje_descuento = 0;

        --  verificar si existe porcentaje de descuento
        if (pxp.f_existe_parametro(p_tabla,'porcentaje_descuento')) then
          v_porcentaje_descuento = v_parametros.porcentaje_descuento;
        end if;

        v_id_vendedor_medico = NULL;
        if (pxp.f_existe_parametro(p_tabla,'id_vendedor_medico')) then
          v_id_vendedor_medico = v_parametros.id_vendedor_medico;
        end if;

        if (v_id_punto_venta is not null) then
          select id_sucursal into v_id_sucursal
          from vef.tpunto_venta
          where id_punto_venta = v_id_punto_venta;
        else
          v_id_sucursal = v_parametros.id_sucursal;

        end if;

        if (pxp.f_existe_parametro(p_tabla,'a_cuenta')) then
          v_a_cuenta = v_parametros.a_cuenta;
        else
          v_a_cuenta = 0;
        end if;

        if (pxp.f_existe_parametro(p_tabla,'comision')) then
          v_comision = v_parametros.comision;
        else
          v_comision = 0;
        end if;

        if (pxp.f_existe_parametro(p_tabla,'fecha_estimada_entrega')) then
          v_fecha_estimada_entrega = v_parametros.fecha_estimada_entrega;
          if (v_fecha_estimada_entrega is not null) then
            v_tiene_formula = 'si';
          else
            v_fecha_estimada_entrega = now();
          end if;
        else
          v_fecha_estimada_entrega = now();
        end if;

        if (pxp.f_existe_parametro(p_tabla,'hora_estimada_entrega')) then

          if (v_parametros.hora_estimada_entrega is not null and v_parametros.hora_estimada_entrega != '') then

            v_hora_estimada_entrega = (v_parametros.hora_estimada_entrega || ':00')::time;
          else
            v_hora_estimada_entrega = NULL;
          end if;
        else
          v_hora_estimada_entrega = now()::time;
        end if;

        if (pxp.f_existe_parametro(p_tabla,'forma_pedido')) then
          v_forma_pedido = v_parametros.forma_pedido;
        else
          v_forma_pedido =NULL;
        end if;


        if (pxp.f_is_positive_integer(v_parametros.id_cliente)) THEN
          v_id_cliente = v_parametros.id_cliente::integer;

          update vef.tcliente
          set nit = v_parametros.nit
          where id_cliente = v_id_cliente;

          select c.nombre_factura into v_nombre_factura
          from vef.tcliente c
          where c.id_cliente = v_id_cliente;
        else
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
            upper(v_parametros.id_cliente),
            v_parametros.nit
          ) returning id_cliente into v_id_cliente;

          v_nombre_factura = v_parametros.id_cliente;

        end if;

        v_id_cliente_destino = null;
        --si tenemos cliente destino
        if v_tipo_factura = 'pedido' then
                 if (pxp.f_is_positive_integer(v_parametros.id_cliente_destino)) THEN
                    v_id_cliente_destino = v_parametros.id_cliente_destino::integer;
                  else

                    INSERT INTO
                      vef.tcliente
                    (
                      id_usuario_reg,
                      fecha_reg,
                      estado_reg,
                      nombre_factura
                    )
                    VALUES (
                      p_id_usuario,
                      now(),
                      'activo',
                      v_parametros.id_cliente
                    ) returning id_cliente into v_id_cliente_destino;


                end if;
        end if;


        --obtener gestion a partir de la fecha actual
        select id_gestion into v_id_gestion
        from param.tgestion
        where gestion = extract(year from now())::integer;

        select nextval('vef.tventa_id_venta_seq') into v_id_venta;

        v_codigo_proceso = 'VEN-' || v_id_venta;
        -- inciiar el tramite en el sistema de WF

        select f.id_funcionario into  v_id_funcionario_inicio
        from segu.tusuario u
          inner join orga.tfuncionario f on f.id_persona = u.id_persona
        where u.id_usuario = p_id_usuario;

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
            v_id_funcionario_inicio,
            NULL,
            NULL,
            v_codigo_proceso);


        if (pxp.f_existe_parametro(p_tabla,'transporte_fob')) then
          v_transporte_fob = v_parametros.transporte_fob;
          v_seguros_fob = v_parametros.seguros_fob;
          v_otros_fob = v_parametros.otros_fob;
          v_transporte_cif = v_parametros.transporte_cif;
          v_seguros_cif = v_parametros.seguros_cif;
          v_otros_cif = v_parametros.otros_cif;
          v_valor_bruto = v_parametros.valor_bruto;
          v_descripcion_bulto = v_parametros.descripcion_bulto;
        end if;

        if (pxp.f_existe_parametro(p_tabla,'tipo_cambio_venta')) then
          v_tipo_cambio_venta = v_parametros.tipo_cambio_venta;
        end if;


   if (v_tipo_factura = 'recibo') then
     -- obtener correlativo
            v_nro_factura =   param.f_obtener_correlativo(
                'RECI', --codigo documento
                         NULL,-- par_id,
                        NULL, --id_uo
                        1, --depto
                        1, --usuario
                        'VEF', --codigo depto
                        NULL,--formato
                        1); --id_empresa

            --fin obtener correlativo
end if;

        --Sentencia de la insercion
        insert into vef.tventa(
          id_venta,
          id_cliente,
          id_sucursal,
          id_proceso_wf,
          id_estado_wf,
          estado_reg,
          nro_tramite,
          a_cuenta,
          fecha_estimada_entrega,
          usuario_ai,
          fecha_reg,
          id_usuario_reg,
          id_usuario_ai,
          id_usuario_mod,
          fecha_mod,
          estado,
          id_punto_venta,
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
          informe,
          anulado


        ) values(
          v_id_venta,
          v_id_cliente,
          v_id_sucursal,
          v_id_proceso_wf,
          v_id_estado_wf,
          'activo',
          v_num_tramite,
          v_a_cuenta,
          v_fecha_estimada_entrega,
          v_parametros._nombre_usuario_ai,
          now(),
          p_id_usuario,
          v_parametros._id_usuario_ai,
          null,
          null,
          v_codigo_estado,
          v_id_punto_venta,
          v_id_vendedor_medico,
          v_porcentaje_descuento,
          v_comision,
          upper(v_parametros.observaciones),
          v_num_ven,
          v_tipo_factura,
          v_fecha,
          v_nro_factura,
          v_id_dosificacion,
          v_excento,


          v_id_moneda_venta,
          COALESCE(v_transporte_fob,0),
          COALESCE(v_seguros_fob,0),
          COALESCE(v_otros_fob,0),
          COALESCE(v_transporte_cif,0),
          COALESCE(v_seguros_cif,0),
          COALESCE(v_otros_cif,0),
          COALESCE(v_tipo_cambio_venta,0),
          COALESCE(v_valor_bruto,0),
          COALESCE(v_descripcion_bulto,''),
          v_parametros.nit,
          v_nombre_factura,
          v_id_cliente_destino,
          v_hora_estimada_entrega,
          v_tiene_formula,
          v_forma_pedido,
          v_informe,
          v_anulado


        ) returning id_venta into v_id_venta;

        if (v_parametros.id_forma_pago != 0 ) then


          insert into vef.tventa_forma_pago(
            usuario_ai,
            fecha_reg,
            id_usuario_reg,
            id_usuario_ai,
            estado_reg,
            id_forma_pago,
            id_venta,
            monto_transaccion,
            monto,
            cambio,
            monto_mb_efectivo,
            numero_tarjeta,
            codigo_tarjeta,
            id_auxiliar,
            tipo_tarjeta
          )
          values(
            v_parametros._nombre_usuario_ai,
            now(),
            p_id_usuario,
            v_parametros._id_usuario_ai,
            'activo',
            v_parametros.id_forma_pago,
            v_id_venta,
            v_parametros.monto_forma_pago,
            0,
            0,
            0,
            v_parametros.numero_tarjeta,
            v_parametros.codigo_tarjeta,
            v_parametros.id_auxiliar,
            v_parametros.tipo_tarjeta
          );
        end if;
        if (v_parametros.id_forma_pago_2 is not null and v_parametros.id_forma_pago_2 != 0 ) then
         /*******************************Control para la tarjeta 2******************************/

        select fp.codigo into v_codigo_tarjeta
                from obingresos.tforma_pago fp
                where fp.id_forma_pago = v_parametros.id_forma_pago_2;

        v_codigo_tarjeta = (case when v_codigo_tarjeta like 'CC%' or v_codigo_tarjeta like 'SF%' then
                                        substring(v_codigo_tarjeta from 3 for 2)
                                else
                                      NULL
                              end);

      	if (v_codigo_tarjeta is not null) then
                    if (substring(v_parametros.numero_tarjeta_2::varchar from 1 for 1) != 'X') then
                		v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta_2::varchar,v_codigo_tarjeta);
                	end if;
        end if;


        /**************************************************************************************/



         --raise exception 'llega aqui para la insercion %',v_parametros.id_forma_pago;
        insert into vef.tventa_forma_pago(
            usuario_ai,
            fecha_reg,
            id_usuario_reg,
            id_usuario_ai,
            estado_reg,
            id_forma_pago,
            id_venta,
            monto_transaccion,
            monto,
            cambio,
            monto_mb_efectivo,
            numero_tarjeta,
            codigo_tarjeta,
            id_auxiliar,
            tipo_tarjeta
          )

          values(
            v_parametros._nombre_usuario_ai,
            now(),
            p_id_usuario,
            v_parametros._id_usuario_ai,
            'activo',
            v_parametros.id_forma_pago_2,
            v_id_venta,
            v_parametros.monto_forma_pago_2,
            0,
            0,
            0,
            v_parametros.numero_tarjeta_2,
            v_parametros.codigo_tarjeta_2,
            v_parametros.id_auxiliar_2,
            v_parametros.tipo_tarjeta
          );
        end if;


        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Ventas almacenado(a) con exito (id_venta'||v_id_venta||')');
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_id_venta::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;



    /*********************************
     #TRANSACCION:  'VF_FACTU_MOD'
     #DESCRIPCION:	Modificacion de registros
     #AUTOR:		Ismael Valdivia
     #FECHA:		26-06-2019 12:15:00
    ***********************************/

    elsif(p_transaccion='VF_FACTU_MOD')then

      begin
        select
          v.*
        into
          v_registros
        from vef.tventa v
        where v.id_venta = v_parametros.id_venta;

        v_tiene_formula = 'no';
        /*Verificamos si existe el parametro id_punto_venta*/
        if (pxp.f_existe_parametro(p_tabla,'id_punto_venta')) then
          v_id_punto_venta = v_parametros.id_punto_venta;
        else
          v_id_punto_venta = NULL;
        end if;
		/******************************************************/

        /*Obtenemos los datos de la venta*/
        SELECT * into v_venta
        FROM vef.tventa v where v.id_venta = v_parametros.id_venta;
		/***********************************/

        /*Recuperamos el id Sucursal cuando el id_punto_venta existe*/
        if (v_id_punto_venta is not null) then
          select id_sucursal into v_id_sucursal
          from vef.tpunto_venta
          where id_punto_venta = v_id_punto_venta;
        else
          v_id_sucursal = v_parametros.id_sucursal;
        end if;
        /*************************************************************/

        v_tipo_factura = 'computarizada';
        v_excento = 0;

        /*Obtenemos la moneda base dependiento de la sucursal*/
        if (pxp.f_existe_parametro(p_tabla,'id_moneda')) then
          v_id_moneda_venta = v_parametros.id_moneda;
        else
          if (v_parametros.id_sucursal is not null ) then
            select sm.id_moneda into v_id_moneda_venta
            from vef.tsucursal_moneda sm
            where sm.id_sucursal = v_parametros.id_sucursal
                  and sm.estado_reg = 'activo' and sm.tipo_moneda = 'moneda_base';
          else
            select sm.id_moneda into v_id_moneda_venta
            from vef.tsucursal_moneda sm
              inner join vef.tpunto_venta pv on pv.id_sucursal = sm.id_sucursal
            where pv.id_punto_venta = v_parametros.id_punto_venta
                  and sm.estado_reg = 'activo' and sm.tipo_moneda = 'moneda_base';
          end if;
        end if;
        /*****************************************************************************/

        /*Verificamos si existe los siguiente parametros*/
        if (pxp.f_existe_parametro(p_tabla,'transporte_fob')) then
          v_transporte_fob = v_parametros.transporte_fob;
          v_seguros_fob = v_parametros.seguros_fob;
          v_otros_fob = v_parametros.otros_fob;
          v_transporte_cif = v_parametros.transporte_cif;
          v_seguros_cif = v_parametros.seguros_cif;
          v_otros_cif = v_parametros.otros_cif;
          v_descripcion_bulto = v_parametros.descripcion_bulto;
          v_valor_bruto = v_parametros.valor_bruto;

        end if;


        if (pxp.f_existe_parametro(p_tabla,'tipo_cambio_venta')) then
          v_tipo_cambio_venta = v_parametros.tipo_cambio_venta;
        end if;
		/*********************************************************************/

        /*Obtenemos y verificamos que el tipo de venta exista*/
        SELECT tv.tipo_base into v_tipo_base
        from vef.ttipo_venta tv
        where tv.codigo = v_tipo_factura and tv.estado_reg = 'activo';

        if (v_tipo_base is null) then
          raise exception 'No existe un tipo de venta con el codigo: % consulte con el administrador del sistema',v_tipo_factura;
        end if;
		/******************************************************/

       /*	if (v_tipo_base = 'computarizada')  then

          IF   v_tipo_factura in ('computarizadaexpo','computarizadaexpomin','computarizadamin')  THEN
            v_fecha = v_parametros.fecha;
            v_nro_factura = v_venta.nro_factura;
            v_id_dosificacion = v_venta.id_dosificacion;

          ELSE
            v_excento = v_parametros.excento;
          END IF;

        end if;*/

        /* Lanzar exception al tratar de modificar la fecha de una venta computarizada*/
        if (v_fecha is not null and v_fecha != v_registros.fecha and v_tipo_base = 'computarizada') then
          raise exception 'No es posible modificar la fecha de una venta computarizada';
        end if;
        /*****************************************************************************/

		/*Verificamos si existen los parametros*/
        if (pxp.f_existe_parametro(p_tabla,'a_cuenta')) then
          v_a_cuenta = v_parametros.a_cuenta;
        else
          v_a_cuenta = 0;
        end if;

        if (pxp.f_existe_parametro(p_tabla,'comision')) then
          v_comision = v_parametros.comision;
        else
          v_comision = 0;
        end if;

        v_porcentaje_descuento = 0;
        --verificar si existe porcentaje de descuento
        if (pxp.f_existe_parametro(p_tabla,'porcentaje_descuento')) then
          v_porcentaje_descuento = v_parametros.porcentaje_descuento;
        end if;

        v_id_vendedor_medico = NULL;
        if (pxp.f_existe_parametro(p_tabla,'id_vendedor_medico')) then
          v_id_vendedor_medico = v_parametros.id_vendedor_medico;
        end if;

        if (pxp.f_existe_parametro(p_tabla,'fecha_estimada_entrega')) then
          v_fecha_estimada_entrega = v_parametros.fecha_estimada_entrega;
          if (v_fecha_estimada_entrega is not null) then
            v_tiene_formula = 'si';
          else
            v_fecha_estimada_entrega = now();
          end if;
        else
          v_fecha_estimada_entrega = now();
        end if;

        if (pxp.f_existe_parametro(p_tabla,'hora_estimada_entrega')) then

          if (v_parametros.hora_estimada_entrega is not null and v_parametros.hora_estimada_entrega != '') then

            v_hora_estimada_entrega = (v_parametros.hora_estimada_entrega || ':00')::time;
          else
            v_hora_estimada_entrega = NULL;
          end if;
        else
          v_hora_estimada_entrega = now()::time;
        end if;

        if (pxp.f_existe_parametro(p_tabla,'forma_pedido')) then
          v_forma_pedido = v_parametros.forma_pedido;
        else
          v_forma_pedido =NULL;
        end if;
		/**************************************************************************************************/

        /*Verificamos si el id_cliente es entero para insertar al nuevo cliente*/
        if (pxp.f_is_positive_integer(v_parametros.id_cliente)) THEN
          v_id_cliente = v_parametros.id_cliente::integer;

          update vef.tcliente
          set nit = v_parametros.nit
          where id_cliente = v_id_cliente;

          select c.nombre_factura into v_nombre_factura
          from vef.tcliente c
          where c.id_cliente = v_id_cliente;
        else
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
            upper(v_parametros.id_cliente),
            v_parametros.nit
          ) returning id_cliente into v_id_cliente;

          v_nombre_factura = v_parametros.id_cliente;
        end if;
       /*************************************************************************/


        --Sentencia de la modificacion

        update vef.tventa set
          id_cliente = v_id_cliente,
          id_sucursal = v_id_sucursal,
          a_cuenta = v_a_cuenta,
          fecha_estimada_entrega = v_fecha_estimada_entrega,
          hora_estimada_entrega = v_hora_estimada_entrega,
          id_usuario_mod = p_id_usuario,
          fecha_mod = now(),
          id_usuario_ai = v_parametros._id_usuario_ai,
          usuario_ai = v_parametros._nombre_usuario_ai,
          id_punto_venta = v_id_punto_venta,
          id_vendedor_medico = v_id_vendedor_medico,
          porcentaje_descuento = v_porcentaje_descuento,
          comision = v_comision,
          tiene_formula = v_tiene_formula,
          observaciones = upper(v_parametros.observaciones),
          forma_pedido = v_forma_pedido,
          fecha = (case when v_fecha is null then
            fecha
                   else
                     v_fecha
                   end),
          nro_factura = v_nro_factura,
          id_dosificacion = v_id_dosificacion,
          excento = v_excento,

          id_moneda = v_id_moneda_venta,
          transporte_fob = COALESCE(v_transporte_fob,0),
          seguros_fob = COALESCE(v_seguros_fob,0),
          otros_fob = COALESCE(v_otros_fob,0),
          transporte_cif = COALESCE(v_transporte_cif,0),
          seguros_cif = COALESCE(v_seguros_cif,0),
          otros_cif = COALESCE(v_otros_cif,0),
          tipo_cambio_venta = COALESCE(v_tipo_cambio_venta,0),
          valor_bruto = COALESCE(v_valor_bruto,0),
          descripcion_bulto = COALESCE(v_descripcion_bulto,''),
          nit = v_parametros.nit,
              nombre_factura = v_nombre_factura ,
              id_cliente_destino = v_id_cliente_destino
        where id_venta=v_parametros.id_venta;



        if (v_parametros.id_forma_pago != 0 ) then

          delete from vef.tventa_forma_pago
          where id_venta = v_parametros.id_venta;



          insert into vef.tventa_forma_pago(
            usuario_ai,
            fecha_reg,
            id_usuario_reg,
            id_usuario_ai,
            estado_reg,
            id_forma_pago,
            id_venta,
            monto_transaccion,
            monto,
            cambio,
            monto_mb_efectivo,
            numero_tarjeta,
            codigo_tarjeta,
            id_auxiliar,
            tipo_tarjeta
          )
          values(
            v_parametros._nombre_usuario_ai,
            now(),
            p_id_usuario,
            v_parametros._id_usuario_ai,
            'activo',
            v_parametros.id_forma_pago,
            v_parametros.id_venta,
            v_parametros.monto_forma_pago,
            0,
            0,
            0,
            v_parametros.numero_tarjeta,
            v_parametros.codigo_tarjeta,
            v_parametros.id_auxiliar,
            v_parametros.tipo_tarjeta
          );
            --raise exception 'llega aqui para la insercion %',v_parametros.id_forma_pago_2;
             if (v_parametros.id_forma_pago_2 is not null and v_parametros.id_forma_pago_2 != 0 ) then
           /*******************************Control para la tarjeta 2******************************/

          select fp.codigo into v_codigo_tarjeta
                  from obingresos.tforma_pago fp
                  where fp.id_forma_pago = v_parametros.id_forma_pago_2;

          v_codigo_tarjeta = (case when v_codigo_tarjeta like 'CC%' or v_codigo_tarjeta like 'SF%' then
                                          substring(v_codigo_tarjeta from 3 for 2)
                                  else
                                        NULL
                                end);

          if (v_codigo_tarjeta is not null) then
                      if (substring(v_parametros.numero_tarjeta_2::varchar from 1 for 1) != 'X') then
                          v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta_2::varchar,v_codigo_tarjeta);
                      end if;
          end if;


          /**************************************************************************************/


         insert into vef.tventa_forma_pago(
            usuario_ai,
            fecha_reg,
            id_usuario_reg,
            id_usuario_ai,
            estado_reg,
            id_forma_pago,
            id_venta,
            monto_transaccion,
            monto,
            cambio,
            monto_mb_efectivo,
            numero_tarjeta,
            codigo_tarjeta,
            id_auxiliar,
            tipo_tarjeta
          )
          values(
            v_parametros._nombre_usuario_ai,
            now(),
            p_id_usuario,
            v_parametros._id_usuario_ai,
            'activo',
            v_parametros.id_forma_pago_2,
            v_parametros.id_venta,
            v_parametros.monto_forma_pago_2,
            0,
            0,
            0,
            v_parametros.numero_tarjeta_2,
            v_parametros.codigo_tarjeta_2,
            v_parametros.id_auxiliar_2,
            v_parametros.tipo_tarjeta_2
          );
          end if;
        end if;

        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Ventas modificado(a)');
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_parametros.id_venta::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;

    /*********************************
 	#TRANSACCION:  'VEF_FINALIZAR_IME'
 	#DESCRIPCION:	funcion que controla el cambio al Siguiente estado de las ventas, integrado  con el WF
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		08-04-2019 10:20:00
	***********************************/

    elseif(p_transaccion='VEF_FINALIZAR_IME')then
      begin
       /*Recuperamos el id_tipo_estado y el id_estado_wf*/
       select
          ew.id_tipo_estado ,
          ew.id_estado_wf,
          ew.id_funcionario
        into
          v_id_tipo_estado,
          v_id_estado_wf,
          v_id_funcionario
        from wf.testado_wf ew
          inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
        where ew.id_estado_wf =  v_parametros.id_estado_wf_act;
       /********************************************************/

       /*Obtenemos los campos de venta el id_entidad y el tipo_base*/
        select v.*,s.id_entidad,tv.tipo_base into v_venta
        from vef.tventa v
          inner join vef.tsucursal s on s.id_sucursal = v.id_sucursal
          inner join vef.tcliente c on c.id_cliente = v.id_cliente
          inner join vef.ttipo_venta tv on tv.codigo = v.tipo_factura and tv.estado_reg = 'activo'
        where v.id_proceso_wf = v_parametros.id_proceso_wf_act;
        /***********************************************************/

        /*Obtenemos el id del estado finalizado*/
        v_estado_finalizado = (v_id_tipo_estado-2);
        /****************************************/

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
        /*************************************************/

       	v_resp = vef.ft_venta_ime(p_administrador,p_id_usuario,v_tabla,'VF_VENVALI_MOD');

        /*Obtenemos el codigo finalizado y fin*/
       	select
          te.codigo,te.fin
        into
          v_codigo_estado_siguiente,v_es_fin
        from wf.ttipo_estado te
        where te.id_tipo_estado = v_estado_finalizado;
        /*********************************************************************/


        /*Verificcar si seran necesario*/
      	IF  pxp.f_existe_parametro(p_tabla,'id_depto_wf') THEN

          v_id_depto = v_parametros.id_depto_wf;

        END IF;

        IF  pxp.f_existe_parametro(p_tabla,'obs') THEN
            v_obs=v_parametros.obs;
        ELSE
          v_obs='---';
        END IF;
       /***************************************************************/


        --configurar acceso directo para la alarma
        v_acceso_directo = '';
        v_clase = '';
        v_parametros_ad = '';
        v_tipo_noti = 'notificacion';
        v_titulo  = 'Visto Bueno';

        -- hay que recuperar el supervidor que seria el estado inmediato,...
        v_id_estado_actual =  wf.f_registra_estado_wf(v_estado_finalizado /*tengpo*/,
                                                      v_id_funcionario/*recuperar*/,
                                                      v_parametros.id_estado_wf_act /*tengo*/,
                                                      v_parametros.id_proceso_wf_act/*tengo*/,
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
                                      v_parametros.id_proceso_wf_act,
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
          --no validamos la fecha en las facturas de exportacion
          --por que  valida al insertar la factura, donde se genera el nro de la factura
          END IF;

         select array_agg(distinct cig.id_actividad_economica) into v_id_actividad_economica
          from vef.tventa_detalle vd
            --inner join vef.tsucursal_producto sp on vd.id_sucursal_producto = sp.id_sucursal_producto
            --inner join param.tconcepto_ingas cig on  cig.id_concepto_ingas = sp.id_concepto_ingas
          	inner join param.tconcepto_ingas cig on cig.id_concepto_ingas = vd.id_producto
          where vd.id_venta = v_venta.id_venta and vd.estado_reg = 'activo';

          --genera el numero de factura
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
        /*****************************************/


         if (v_es_fin = 'si' and pxp.f_get_variable_global('vef_tiene_apertura_cierre') = 'si') then
          if (exists(	select 1
                       from vef.tapertura_cierre_caja acc
                       where acc.id_usuario_cajero = p_id_usuario and
                             acc.fecha_apertura_cierre = v_venta.fecha and
                             acc.estado_reg = 'activo' and acc.estado = 'cerrado' and
                             (acc.id_punto_venta = v_venta.id_punto_venta or
                              acc.id_sucursal = v_venta.id_sucursal))) then
            raise exception 'La caja ya fue cerrada, necesita tener la caja abierta para poder finalizar la venta';
          end if;


          if (not exists(	select 1
                           from vef.tapertura_cierre_caja acc
                           where acc.id_usuario_cajero = p_id_usuario and
                                 acc.fecha_apertura_cierre = v_venta.fecha and
                                 acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                                 (acc.id_punto_venta = v_venta.id_punto_venta or
                                  acc.id_sucursal = v_venta.id_sucursal))) then
            raise exception 'Antes de finalizar una venta debe realizar una apertura de caja';
          end if;

          update vef.tventa set id_usuario_cajero = p_id_usuario
          where id_venta = v_venta.id_venta;
        end if;

        --inserta o modifical el libro de ventas
        if (pxp.f_get_variable_global('vef_integracion_lcv') = 'si' and v_es_fin = 'si') then
          v_res = vef.f_inserta_lcv(p_administrador,p_id_usuario,p_tabla,'FIN',v_venta.id_venta);
        end if;


        -- si hay mas de un estado disponible  preguntamos al usuario
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado de la planilla)');
        v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');
        v_resp = pxp.f_agrega_clave(v_resp,'estado',v_codigo_estado_siguiente);



        -- Devuelve la respuesta
        return v_resp;






      end;

    /*********************************
 	#TRANSACCION:  'VEF_FINATO_IME'
 	#DESCRIPCION:	funcion que controla el cambio al Siguiente estado de las ventas, integrado  con el WF
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		08-04-2019 10:20:00
	***********************************/

    elseif(p_transaccion='VEF_FINATO_IME')then
      begin
       /*Recuperamos el id_tipo_estado y el id_estado_wf*/
       select
          ew.id_tipo_estado ,
          ew.id_estado_wf,
          ew.id_funcionario
        into
          v_id_tipo_estado,
          v_id_estado_wf,
          v_id_funcionario
        from wf.testado_wf ew
          inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
        where ew.id_estado_wf =  v_parametros.id_estado_wf_act;
       /********************************************************/

       /*Obtenemos los campos de venta el id_entidad y el tipo_base*/
        select v.*,s.id_entidad,tv.tipo_base into v_venta
        from vef.tventa v
          inner join vef.tsucursal s on s.id_sucursal = v.id_sucursal
          inner join vef.tcliente c on c.id_cliente = v.id_cliente
          inner join vef.ttipo_venta tv on tv.codigo = v.tipo_factura and tv.estado_reg = 'activo'
        where v.id_proceso_wf = v_parametros.id_proceso_wf_act;
        /***********************************************************/

        /*Obtenemos el id del estado finalizado*/
        v_estado_finalizado = (v_id_tipo_estado+1);
        /****************************************/


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
        /*************************************************/

       	v_resp = vef.ft_venta_ime(p_administrador,p_id_usuario,v_tabla,'VF_VENVALI_MOD');

        /*Obtenemos el codigo finalizado y fin*/
       	select
          te.codigo,te.fin
        into
          v_codigo_estado_siguiente,v_es_fin
        from wf.ttipo_estado te
        where te.id_tipo_estado = v_estado_finalizado;
        /*********************************************************************/


        /*Verificcar si seran necesario*/
      	IF  pxp.f_existe_parametro(p_tabla,'id_depto_wf') THEN

          v_id_depto = v_parametros.id_depto_wf;

        END IF;

        IF  pxp.f_existe_parametro(p_tabla,'obs') THEN
            v_obs=v_parametros.obs;
        ELSE
          v_obs='---';
        END IF;
       /***************************************************************/


        --configurar acceso directo para la alarma
        v_acceso_directo = '';
        v_clase = '';
        v_parametros_ad = '';
        v_tipo_noti = 'notificacion';
        v_titulo  = 'Visto Bueno';

        -- hay que recuperar el supervidor que seria el estado inmediato,...
        v_id_estado_actual =  wf.f_registra_estado_wf(v_estado_finalizado /*tengpo*/,
                                                      v_id_funcionario/*recuperar*/,
                                                      v_parametros.id_estado_wf_act /*tengo*/,
                                                      v_parametros.id_proceso_wf_act/*tengo*/,
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
                                      v_parametros.id_proceso_wf_act,
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
          --no validamos la fecha en las facturas de exportacion
          --por que  valida al insertar la factura, donde se genera el nro de la factura
          END IF;

         select array_agg(distinct cig.id_actividad_economica) into v_id_actividad_economica
          from vef.tventa_detalle vd
            --inner join vef.tsucursal_producto sp on vd.id_sucursal_producto = sp.id_sucursal_producto
            --inner join param.tconcepto_ingas cig on  cig.id_concepto_ingas = sp.id_concepto_ingas
          	inner join param.tconcepto_ingas cig on cig.id_concepto_ingas = vd.id_producto
          where vd.id_venta = v_venta.id_venta and vd.estado_reg = 'activo';

          --genera el numero de factura
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
        /*****************************************/


         if (v_es_fin = 'si' and pxp.f_get_variable_global('vef_tiene_apertura_cierre') = 'si') then
          if (exists(	select 1
                       from vef.tapertura_cierre_caja acc
                       where acc.id_usuario_cajero = p_id_usuario and
                             acc.fecha_apertura_cierre = v_venta.fecha and
                             acc.estado_reg = 'activo' and acc.estado = 'cerrado' and
                             (acc.id_punto_venta = v_venta.id_punto_venta or
                              acc.id_sucursal = v_venta.id_sucursal))) then
            raise exception 'La caja ya fue cerrada, necesita tener la caja abierta para poder finalizar la venta';
          end if;


          if (not exists(	select 1
                           from vef.tapertura_cierre_caja acc
                           where acc.id_usuario_cajero = p_id_usuario and
                                 acc.fecha_apertura_cierre = v_venta.fecha and
                                 acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                                 (acc.id_punto_venta = v_venta.id_punto_venta or
                                  acc.id_sucursal = v_venta.id_sucursal))) then
            raise exception 'Antes de finalizar una venta debe realizar una apertura de caja';
          end if;

          update vef.tventa set id_usuario_cajero = p_id_usuario
          where id_venta = v_venta.id_venta;
        end if;

        --inserta o modifical el libro de ventas
        if (pxp.f_get_variable_global('vef_integracion_lcv') = 'si' and v_es_fin = 'si') then
          v_res = vef.f_inserta_lcv(p_administrador,p_id_usuario,p_tabla,'FIN',v_venta.id_venta);
        end if;


        -- si hay mas de un estado disponible  preguntamos al usuario
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado de la planilla)');
        v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');
        v_resp = pxp.f_agrega_clave(v_resp,'estado',v_codigo_estado_siguiente);



        -- Devuelve la respuesta
        return v_resp;





      end;


     /*********************************
 	#TRANSACCION:  'VEF_FINMAN_IME'
 	#DESCRIPCION:	funcion que controla el cambio al Siguiente estado de las ventas, integrado  con el WF
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		03-09-2019 15:50:00
	***********************************/

    elseif(p_transaccion='VEF_FINMAN_IME')then
      begin

      /*Recuperamos el id_tipo_estado y el id_estado_wf*/
       select
          ew.id_tipo_estado ,
          ew.id_estado_wf,
          ew.id_funcionario
        into
          v_id_tipo_estado,
          v_id_estado_wf,
          v_id_funcionario
        from wf.testado_wf ew
          inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
        where ew.id_estado_wf =  v_parametros.id_estado_wf_act;
       /********************************************************/

       /*Obtenemos los campos de venta el id_entidad y el tipo_base*/
        select v.*,s.id_entidad,tv.tipo_base into v_venta
        from vef.tventa v
          inner join vef.tsucursal s on s.id_sucursal = v.id_sucursal
          inner join vef.tcliente c on c.id_cliente = v.id_cliente
          inner join vef.ttipo_venta tv on tv.codigo = v.tipo_factura and tv.estado_reg = 'activo'
        where v.id_proceso_wf = v_parametros.id_proceso_wf_act;
        /***********************************************************/

        if (v_venta.anulado = 'SI' ) then
        	v_estado_finalizado = 851;
        else
          /*Obtenemos el id del estado finalizado*/
          v_estado_finalizado = (v_id_tipo_estado+1);
          /****************************************/
        end if;
        /*Obtenemnos el codigo caja*/
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
        /*************************************************/
        --v_resp = vef.ft_venta_facturacion_ime(p_administrador,p_id_usuario,v_tabla,'VF_VENVALI_MOD');

        /*Obtenemos el codigo finalizado y fin*/
        select
          te.codigo,te.fin
        into
          v_codigo_estado_siguiente,v_es_fin
        from wf.ttipo_estado te
        where te.id_tipo_estado = v_estado_finalizado;
        /*********************************************************************/


        /*Verificcar si seran necesario*/
        IF  pxp.f_existe_parametro(p_tabla,'id_depto_wf') THEN
          v_id_depto = v_parametros.id_depto_wf;
        END IF;

        IF  pxp.f_existe_parametro(p_tabla,'obs') THEN
            v_obs=v_parametros.obs;
        ELSE
          v_obs='---';
        END IF;
       /***************************************************************/


        --configurar acceso directo para la alarma
        v_acceso_directo = '';
        v_clase = '';
        v_parametros_ad = '';
        v_tipo_noti = 'notificacion';
        v_titulo  = 'Visto Bueno';

        -- hay que recuperar el supervidor que seria el estado inmediato,...
        v_id_estado_actual =  wf.f_registra_estado_wf(v_estado_finalizado /*tengpo*/,
                                                      v_id_funcionario/*recuperar*/,
                                                      v_parametros.id_estado_wf_act /*tengo*/,
                                                      v_parametros.id_proceso_wf_act/*tengo*/,
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
                                      v_parametros.id_proceso_wf_act,
                                      v_codigo_estado_siguiente) THEN

        END IF;
        /************************************/

        /*Controla si hay recibos posteriores*/

        /*****************************************/

         if (v_es_fin = 'si' and pxp.f_get_variable_global('vef_tiene_apertura_cierre') = 'si') then
          if (exists(	select 1
                       from vef.tapertura_cierre_caja acc
                       where acc.id_usuario_cajero = p_id_usuario and
                             acc.fecha_apertura_cierre = v_venta.fecha and
                             acc.estado_reg = 'activo' and acc.estado = 'cerrado' and
                             (acc.id_punto_venta = v_venta.id_punto_venta or
                              acc.id_sucursal = v_venta.id_sucursal))) then
            raise exception 'La caja ya fue cerrada, necesita tener la caja abierta para poder finalizar la venta';
          end if;


          if (not exists(	select 1
                           from vef.tapertura_cierre_caja acc
                           where acc.id_usuario_cajero = p_id_usuario and
                                 acc.fecha_apertura_cierre = v_venta.fecha and
                                 acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                                 (acc.id_punto_venta = v_venta.id_punto_venta or
                                  acc.id_sucursal = v_venta.id_sucursal))) then
            raise exception 'Antes de finalizar una venta debe realizar una apertura de caja';
          end if;

          update vef.tventa set id_usuario_cajero = p_id_usuario
          where id_venta = v_venta.id_venta;
        end if;

        --inserta o modifical el libro de ventas
       /* if (pxp.f_get_variable_global('vef_integracion_lcv') = 'si' and v_es_fin = 'si') then
          v_res = vef.f_inserta_lcv(p_administrador,p_id_usuario,p_tabla,'FIN',v_venta.id_venta);
        end if;*/


        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado de la planilla)');
        v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');
        v_resp = pxp.f_agrega_clave(v_resp,'estado',v_codigo_estado_siguiente);
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_venta.id_venta::varchar);
        -- Devuelve la respuesta
        return v_resp;

      end;

      /*********************************
     #TRANSACCION:  'VF_FACVALI_MOD'
     #DESCRIPCION:	Validacion de montos en una venta
     #AUTOR:		admin
     #FECHA:		01-06-2015 05:58:00
    ***********************************/

    elsif(p_transaccion='VF_FACVALI_MOD')then

      begin
        vef_estados_validar_fp = pxp.f_get_variable_global('vef_estados_validar_fp');
        --obtener datos de la venta y la moneda base
        select
          v.* ,
          sm.id_moneda as id_moneda_base,
          m.codigo  as moneda ,
          v.id_dosificacion as id_dosificacion_venta
        into
          v_venta
        from vef.tventa v
          inner join vef.tsucursal suc on suc.id_sucursal = v.id_sucursal
          inner join vef.tsucursal_moneda sm on suc.id_sucursal = sm.id_sucursal and sm.tipo_moneda = 'moneda_base'
          inner join param.tmoneda m on m.id_moneda = sm.id_moneda
        where id_venta = v_parametros.id_venta;

        --si es venta de exportacion operamos con la moneda especificada por el usuario
        IF  v_venta.tipo_factura in ('computarizadaexpo','computarizadaexpomin') THEN
          v_id_moneda_venta = v_venta.id_moneda;
        ELSE
          v_id_moneda_venta = v_venta.id_moneda_base;
        END IF;

        v_id_moneda_suc = v_venta.id_moneda_base;


		select count(distinct vd.id_venta_detalle) into v_cantidad
        from vef.tventa_detalle vd
        where vd.id_venta = v_parametros.id_venta;

        if (v_parametros.tipo_factura = 'manual') THEN
        	IF(v_cantidad=0 and v_parametros.anulado = 'NO')THEN
        		raise exception 'Debe tener al menos un concepto registrado para la venta';
        	END IF;

        else

        	IF(v_cantidad=0)THEN
        		raise exception 'Debe tener al menos un concepto registrado para la venta';
        	END IF;

        end if;

        --Validar que solo haya conceptos contabilizables o no contabilizables
        select count(distinct sp.contabilizable) into v_cantidad
        from vef.tventa_detalle vd
          left join vef.tsucursal_producto sp on sp.id_sucursal_producto = vd.id_sucursal_producto
        where vd.id_venta = v_parametros.id_venta;

        if (v_cantidad > 1) then
          raise exception 'No puede utilizar conceptos contabilizables y no contabilizables en la misma venta';
        else
        	update vef.tventa set contabilizable = 'si'
          where id_venta = v_parametros.id_venta;

         /* update vef.tventa set contabilizable = (
            select distinct sp.contabilizable
            from vef.tventa_detalle vd
              left join vef.tsucursal_producto sp on sp.id_sucursal_producto = vd.id_sucursal_producto
            where vd.id_venta = v_parametros.id_venta)
          where id_venta = v_parametros.id_venta; */
        end if;


        select count(*) into v_cantidad
        from vef.tventa_detalle vd
          left join vef.tsucursal_producto sp on sp.id_sucursal_producto = vd.id_sucursal_producto
        where vd.id_venta = v_parametros.id_venta and sp.excento= 'si';


        --Validar que si hay un concepto con excento el importe excento no sea 0
        if (v_venta.tipo_factura <> 'recibo') then
          if (v_cantidad > 0 and v_venta.excento = 0) then
            raise exception 'Tiene un concepto que requiere un importe excento y el importe excento para esta venta es 0';
          end if;
        end if;

        --Validar que si el excento no es 0 que haya un concepto que tenga excento
        if (v_cantidad = 0 and v_venta.tipo_factura not in ('computarizadaexpo','computarizadaexpomin') and v_venta.excento > 0) then
          raise exception 'No tiene ningun concepto que requiera excento. El excento no puede ser mayor a 0 para esta venta';
        end if;

        --Validar que el excento no es mayor que el valor total de la venta

        if (v_venta.excento > v_venta.total_venta_msuc) then
          raise exception 'El importe excento no puede ser mayor al total de la venta%,%',v_venta.excento,v_venta.total_venta;
        end if;


        --raise exception 'v_codigo_estado %', v_venta.estado;

        if (pxp.f_existe_parametro(p_tabla,'codigo_estado'))then
        	v_codigo_estado = v_parametros.codigo_estado;
        else
          select sig.codigo into v_codigo_estado
          from vef.tventa v
          inner join wf.testado_wf e on e.id_estado_wf=v.id_estado_wf
          inner join wf.ttipo_estado te on te.id_tipo_estado=e.id_tipo_estado
          inner join wf.testructura_estado es on es.id_tipo_estado_padre=te.id_tipo_estado
          inner join wf.ttipo_estado sig on sig.id_tipo_estado=es.id_tipo_estado_hijo
          where id_venta=v_venta.id_venta;
          	--v_codigo_estado = v_venta.estado;
        end if;

        --si es un estado para validar la forma de pago
        if ((v_codigo_estado) = ANY(string_to_array(vef_estados_validar_fp,',')))then
        		  --raise exception 'entra %', v_codigo_estado;
          select count(*) into v_cantidad_fp
          from vef.tventa_forma_pago
          where id_venta =   v_parametros.id_venta;

          --lo que ya se pago es igual a lo que se tenia a cuenta, suponiendo q esta en la moneda base
          v_acumulado_fp = v_venta.a_cuenta;


          for v_registros in (select vfp.id_venta_forma_pago, fp.id_moneda,vfp.monto_transaccion
                              from vef.tventa_forma_pago vfp
                                inner join vef.tforma_pago fp on fp.id_forma_pago = vfp.id_forma_pago
                              where vfp.id_venta = v_parametros.id_venta)loop

            --si la moneda de la forma de pago es distinta a al moneda base de la sucursal convertimos a moneda base
            if (v_registros.id_moneda != v_id_moneda_venta) then
              IF  v_venta.tipo_cambio_venta is not null and v_venta.tipo_cambio_venta != 0 THEN
              	v_monto_fp = param.f_convertir_moneda(v_registros.id_moneda,v_id_moneda_venta,v_registros.monto_transaccion,v_venta.fecha::date,'CUS',2, v_venta.tipo_cambio_venta,'si');
              ELSE
                v_monto_fp = param.f_convertir_moneda(v_registros.id_moneda,v_id_moneda_venta,v_registros.monto_transaccion,v_venta.fecha::date,'O',2,NULL,'si');
              END IF;
            else
              v_monto_fp = v_registros.monto_transaccion;
            end if;



            --si el monto de una d elas formas de pago es mayor q el total de la venta y la cantidad de formas de pago es mayor a 1 lanzo excepcion
            if (v_monto_fp >= v_venta.total_venta and v_cantidad_fp > 1) then
              raise exception 'Se ha definido mas de una forma de pago, pero existe una que supera el valor de la venta(solo se requiere una forma de pago)';
            end if;

            update vef.tventa_forma_pago set
              monto = v_monto_fp,
              cambio = (case when (v_monto_fp + v_acumulado_fp - v_venta.total_venta) > 0 then
                (v_monto_fp + v_acumulado_fp - v_venta.total_venta)
                        else
                          0
                        end),
              monto_mb_efectivo = (case when (v_monto_fp + v_acumulado_fp - v_venta.total_venta) > 0 then
                v_monto_fp - (v_monto_fp + v_acumulado_fp - v_venta.total_venta)
                                   else
                                     v_monto_fp
                                   end)
            where id_venta_forma_pago = v_registros.id_venta_forma_pago;
            v_acumulado_fp = v_acumulado_fp + v_monto_fp;
          end loop;

          select sum(round(monto_mb_efectivo,2)) into v_suma_fp
          from vef.tventa_forma_pago
          where id_venta =   v_parametros.id_venta;

          select sum(round(cantidad*precio,2)) into v_suma_det
          from vef.tventa_detalle
          where id_venta =   v_parametros.id_venta;

          IF v_parametros.tipo_factura != 'computarizadaexpo' THEN
            v_suma_det = COALESCE(v_suma_det,0) + COALESCE(v_venta.transporte_fob ,0)  + COALESCE(v_venta.seguros_fob ,0)+ COALESCE(v_venta.otros_fob ,0) + COALESCE(v_venta.transporte_cif ,0) +  COALESCE(v_venta.seguros_cif ,0) + COALESCE(v_venta.otros_cif ,0);
          END IF;

          if (v_suma_fp < v_venta.total_venta) then
            raise exception 'El importe recibido es menor al valor de la venta, falta %', v_venta.total_venta - v_suma_fp;
          end if;

          if (v_suma_fp > v_venta.total_venta) then
            raise exception 'El total de la venta no coincide con la división por forma de pago%',v_suma_fp;
          end if;

          if (v_suma_det != v_venta.total_venta) then
            raise exception 'El total de la venta no coincide con la suma de los detalles (% = %) en id: %',v_suma_det ,v_venta.total_venta, v_parametros.id_venta;
          end if;
        end if;

        select sum(cambio) into v_suma_fp
        from vef.tventa_forma_pago
        where id_venta =   v_parametros.id_venta;

		/*Aumento de condicion para que se pueda registrar sin ningun detalle*/
        IF (v_parametros.tipo_factura = 'manual') then
          if (v_parametros.anulado = 'SI' and v_venta.tipo_cambio_venta is null) then
              v_total_venta_ms = param.f_convertir_moneda(0,0,0,now()::date,'O',2,NULL,'si');
          elsif (v_parametros.anulado = 'SI' and v_venta.tipo_cambio_venta is not null) then
              v_total_venta_ms = param.f_convertir_moneda(v_id_moneda_venta,v_id_moneda_suc,v_venta.total_venta,v_venta.fecha,'CUS',2, v_venta.tipo_cambio_venta,'si');
          end if;
        ELSE
          --calcula el total de la venta en moenda de la sucursal
          IF  v_venta.tipo_cambio_venta is not null THEN
            v_total_venta_ms = param.f_convertir_moneda(v_id_moneda_venta,v_id_moneda_suc,v_venta.total_venta,v_venta.fecha,'CUS',2, v_venta.tipo_cambio_venta,'si');
          ELSE
            v_total_venta_ms = param.f_convertir_moneda(v_id_moneda_venta,v_id_moneda_suc,v_registros.monto_transaccion,v_venta.fecha::date,'O',2,NULL,'si');
          END IF;
        end if;
		/*****************************************************************************/

        update vef.tventa v set
          total_venta_msuc = v_total_venta_ms
        where v.id_venta = v_parametros.id_venta;

        --si es factura comercial de exportacion generamos el numero de factura y validamos la fecha
        IF  v_venta.tipo_factura in ('computarizadaexpo','computarizadaexpomin','computarizadamin') THEN
          IF  v_venta.tipo_factura in ('computarizadaexpo','computarizadaexpomin') THEN
            update vef.tventa v set
              excento = total_venta_msuc
            where v.id_venta = v_parametros.id_venta;
          END IF;
          -- si es eidicion ya tendremos un numeor de factura que no debemos cambiar
          IF  v_venta.nro_factura is null THEN



            if (EXISTS(select 1
                       from vef.tventa v
                       where v.fecha > v_venta.fecha and v.tipo_factura = v_venta.tipo_factura
                             and v.estado != 'anulado'
                             and v.id_sucursal = v_venta.id_sucursal
                             and v.estado_reg = 'activo'))THEN
              raise exception 'Existen facturas emitidas con fechas posterior a la registrada (%). Por favor revise la fecha y hora del sistema (%..%)',v_fecha, v_venta.fecha, v_venta.tipo_factura;
            end if;



            select array_agg(distinct cig.id_actividad_economica) into v_id_actividad_economica
            from vef.tventa_detalle vd
              inner join vef.tsucursal_producto sp on vd.id_sucursal_producto = sp.id_sucursal_producto
              inner join param.tconcepto_ingas cig on  cig.id_concepto_ingas = sp.id_concepto_ingas
            where vd.id_venta = v_venta.id_venta and vd.estado_reg = 'activo';



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
                         where ven.nro_factura =  v_nro_factura and ven.id_dosificacion = v_dosificacion.id_dosificacion)) then
              raise exception 'El numero de factura ya existe para esta dosificacion. Por favor comuniquese con el administrador del sistema';
            end if;


            update vef.tventa v set
              nro_factura = v_nro_factura,
              id_dosificacion = v_dosificacion.id_dosificacion
            where v.id_venta = v_parametros.id_venta;

            update vef.tdosificacion
            set nro_siguiente = nro_siguiente + 1
            where id_dosificacion = v_dosificacion.id_dosificacion;



          ELSE
            --validar que la actividad economica no varie con respecto la insertada inicialmente que la fecha n



            select
              *
            into
              v_dosificacion
            from vef.tdosificacion dos
            where dos.id_dosificacion = v_venta.id_dosificacion_venta;



            IF exists(select 1
                      from vef.tventa_detalle vd
                        inner join vef.tsucursal_producto sp on vd.id_sucursal_producto = sp.id_sucursal_producto
                        inner join param.tconcepto_ingas cig on  cig.id_concepto_ingas = sp.id_concepto_ingas
                      where vd.id_venta = v_venta.id_venta and vd.estado_reg = 'activo'
                            AND  cig.id_actividad_economica != ANY(v_dosificacion.id_activida_economica)

            ) THEN


              raise exception 'El nro de facura fue generado para la actividad economica: no puede introducir otros conceptos pertenecientes a otra actividad';

            END IF;

          END IF;



          --si es factura de exportacion minera insertamos descripcion por defecto
          IF   v_venta.tipo_factura in ('computarizadaexpomin','computarizadamin') THEN



            FOR v_reg_tipo_desc in (select
                                      td.*
                                    from vef.ttipo_descripcion td
                                    where td.id_sucursal = v_venta.id_sucursal and td.estado_reg = 'activo') LOOP


              --si el valor no exite lo insertamos
              IF  not exists (select 1 from vef.tvalor_descripcion vd
              where vd.id_tipo_descripcion =   v_reg_tipo_desc.id_tipo_descripcion
                    and vd.id_venta  =  v_venta.id_venta)   THEN


                INSERT INTO  vef.tvalor_descripcion
                (
                  id_usuario_reg,
                  fecha_reg,
                  estado_reg,
                  id_venta,
                  id_tipo_descripcion
                )
                VALUES (
                  p_id_usuario,
                  now(),
                  'activo',
                  v_venta.id_venta,
                  v_reg_tipo_desc.id_tipo_descripcion
                );


              END IF;


            END LOOP;

          END IF;


        END IF;

        /*******************CONDICION PARA OBTENER EL ID_PROCESO y ESTADO WF *******************/
        Select ven.id_proceso_wf ,
        ven.id_estado_wf
        into    v_id_proceso_wf,
        		v_id_estado_wf
        from vef.tventa ven
        where ven.id_venta = v_parametros.id_venta;

        /***************************************************************************************/



        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Venta Validada');
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_parametros.id_venta::varchar);
		v_resp = pxp.f_agrega_clave(v_resp,'id_proceso_wf',v_id_proceso_wf::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'id_estado_wf',v_id_estado_wf::varchar);

        if (v_codigo_estado =ANY(string_to_array(vef_estados_validar_fp,',')) and v_suma_fp > 0)then
          v_resp = pxp.f_agrega_clave(v_resp,'cambio',(v_suma_fp::varchar || ' ' || v_venta.moneda)::varchar);
        end if;

        --Devuelve la respuesta
        return v_resp;

      end;

     /*********************************
 	#TRANSACCION:  'VEF_ANULAR_IME'
 	#DESCRIPCION:	funcion que controla el cambio al Siguiente estado de las ventas, integrado  con el WF
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		05-06-2019 11:33:00
	***********************************/

    elseif(p_transaccion='VEF_ANULAR_IME')then
      begin

     	for v_respaldo in ( select
        	  ven.id_venta,
              ven.nombre_factura,
              ven.nit,
              ven.cod_control,
              ven.nro_factura,
              ven.total_venta,
              ven.total_venta_msuc,
              ven.id_sucursal,
              ven.id_cliente,
              ven.id_punto_venta,
              ven.observaciones,
              ven.id_moneda,
              ven.excento,
              ven.fecha,
              vendet.id_sucursal_producto,
              vendet.id_formula,
              vendet.id_producto,
              vendet.cantidad,
              vendet.precio,
              vendet.tipo,
              vendet.descripcion,
              fp.id_forma_pago,
              fp.monto,
              fp.monto_transaccion,
              fp.monto_mb_efectivo,
              fp.numero_tarjeta,
              fp.codigo_tarjeta,
              fp.tipo_tarjeta,
              fp.id_auxiliar,
              dos.nroaut,
              ven.id_dosificacion
        from vef.tventa ven
        inner join vef.tventa_detalle vendet on vendet.id_venta = ven.id_venta
        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
        inner join vef.tdosificacion dos on dos.id_dosificacion = ven.id_dosificacion
        where ven.id_venta = v_parametros.id_venta ) LOOP

        		insert into vef.trespaldo_facturas_anuladas (
                id_venta,
                nombre_factura,
                nit,
                cod_control,
                num_factura,
                total_venta,
                total_venta_msuc,
                id_sucursal,
                id_cliente,
                id_punto_venta,
                observaciones,
                id_moneda,
                excento,
                fecha,
                id_sucursal_producto,
                id_formula,
                id_producto,
                cantidad,
                precio,
                tipo,
                descripcion,
                id_forma_pago,
                monto,
                monto_transaccion,
                monto_mb_efectivo,
                numero_tarjeta,
                codigo_tarjeta,
                tipo_tarjeta,
                id_auxiliar,
                fecha_reg,
                id_usuario_reg,
                id_dosificacion,
                nro_autorizacion
                )
                VALUES (
                v_respaldo.id_venta,
      			v_respaldo.nombre_factura,
                v_respaldo.nit,
                v_respaldo.cod_control,
                v_respaldo.nro_factura,
                v_respaldo.total_venta,
                v_respaldo.total_venta_msuc,
                v_respaldo.id_sucursal,
                v_respaldo.id_cliente,
                v_respaldo.id_punto_venta,
                v_respaldo.observaciones,
                v_respaldo.id_moneda,
                v_respaldo.excento,
                v_respaldo.fecha,
                v_respaldo.id_sucursal_producto,
                v_respaldo.id_formula,
                v_respaldo.id_producto,
                v_respaldo.cantidad,
                v_respaldo.precio,
                v_respaldo.tipo,
                v_respaldo.descripcion,
                v_respaldo.id_forma_pago,
                v_respaldo.monto,
                v_respaldo.monto_transaccion,
                v_respaldo.monto_mb_efectivo,
                v_respaldo.numero_tarjeta,
                v_respaldo.codigo_tarjeta,
                v_respaldo.tipo_tarjeta,
                v_respaldo.id_auxiliar,
                now(),
                p_id_usuario,
                v_respaldo.id_dosificacion,
                v_respaldo.nroaut
                );

        END LOOP;




        update vef.tventa_forma_pago set
        monto_transaccion = 0,
        monto = 0,
        cambio = 0,
        monto_mb_efectivo = 0
        where id_venta = v_parametros.id_venta;

        update vef.tventa_detalle set
        precio = 0,
        cantidad = 0
        where id_venta = v_parametros.id_venta;

        update vef.tventa set
        cod_control = Null,
        total_venta_msuc = 0,
        nombre_factura = 'ANULADO',
        nit = '0',
        total_venta = 0
        where id_venta = v_parametros.id_venta;



        select * into v_venta
        from vef.tventa v
        where v.id_venta = v_parametros.id_venta;

        v_tipo_usuario = 'vendedor';

        if (v_venta.id_punto_venta is null) then
          select  su.tipo_usuario into v_tipo_usuario
          from vef.tsucursal_usuario su
          where id_sucursal = v_venta.id_sucursal and su.id_usuario = p_id_usuario;
        else
          select  su.tipo_usuario into v_tipo_usuario
          from vef.tsucursal_usuario su
          where su.id_punto_venta = v_venta.id_punto_venta and su.id_usuario = p_id_usuario;
        end if;

        /*if ((v_tipo_usuario = 'vendedor' and v_venta.fecha != now()::date) or p_administrador != 1) then
          raise exception 'La venta solo puede ser anulada el mismo dia o por un administrador';
        end if;*/

        if ((v_tipo_usuario = 'vendedor' or v_tipo_usuario = 'cajero')) then
          if (v_venta.id_usuario_reg != p_id_usuario and v_venta.fecha != now()::date ) then
                  raise exception 'La venta solo puede ser anulada el mismo dia o por un administrador';
          end if;
        else
        	select 'administrador'::varchar as rol into v_tipo_usuario
            from segu.tusuario_rol usurol
            where usurol.id_usuario = p_id_usuario and usurol.estado_reg = 'activo' and (usurol.id_rol = 190 or usurol.id_rol = 1);

        	if ((v_tipo_usuario != 'administrador')) then
            	raise exception 'La venta solo puede ser anulada el mismo dia o por un administrador';
            end if;
        end if;

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
        where ven.id_venta = v_parametros.id_venta;


        v_res = vef.f_anula_venta(p_administrador,p_id_usuario,p_tabla, v_registros.id_proceso_wf,v_registros.id_estado_wf, v_parametros.id_venta);

        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','venta anulada');
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_parametros.id_venta::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;

       /*********************************
    #TRANSACCION: 'VF_TIPO_USUARIO_IME'
    #DESCRIPCION: RECUPERA EL TIPO DE USUARIO
    #AUTOR: ISMAEL VALDIVIA ARANIBAR
    #FECHA: 08/07/2019
    ***********************************/

	elsif (p_transaccion = 'VF_TIPO_USUARIO_IME') then

  	BEGIN

            select 'administrador'::varchar as rol into v_tipo_usuario
            from segu.tusuario_rol usurol
            where usurol.id_usuario = p_id_usuario and usurol.estado_reg = 'activo' and (usurol.id_rol = 190 or usurol.id_rol = 1);

        if (v_tipo_usuario is null) then

            if (v_parametros.vista = 'cajero') then

            select 'cajero'::varchar as rol into v_tipo_usuario
            from segu.tusuario_rol usurol
            where usurol.id_usuario = p_id_usuario and usurol.estado_reg = 'activo' and usurol.id_rol = 163;

            elsif (v_parametros.vista = 'counter') then

            select 'vendedor'::varchar as rol into v_tipo_usuario
            from segu.tusuario_rol usurol
            where usurol.id_usuario = p_id_usuario and usurol.estado_reg = 'activo' and usurol.id_rol = 189;

          end if;
        end if;

      --Definition of the response
      	v_resp = pxp.f_agrega_clave(v_resp, 'message ', 'Tipo Usuario');
        v_resp = pxp.f_agrega_clave(v_resp,'v_tipo_usuario',v_tipo_usuario::varchar);


      --Returns the answer
        return v_resp;

  	END;

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

ALTER FUNCTION vef.ft_venta_facturacion_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;