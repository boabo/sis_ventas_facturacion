CREATE OR REPLACE FUNCTION vef.ft_venta_facturacion_exportacion_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_venta_facturacion_exportacion_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tventa'
 AUTOR: 		 (ismael.valdivia)
 FECHA:	        22-04-2021 13:40:47
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

    /*Aumentando Variables para la inserccion*/
    v_id_cliente	integer;
    v_nombre_factura	varchar;
    v_id_periodo	integer;
    v_codigo_tabla	varchar;
    v_id_moneda_venta	integer;
    v_tipo_factura	varchar;
    v_tipo_base		varchar;
    v_excento		numeric;
    v_fecha			date;
    v_id_punto_venta	integer;
    v_num_ven			varchar;
    v_porcentaje_descuento numeric;
    v_id_sucursal	integer;
    v_a_cuenta		numeric;
    v_comision		numeric;
    v_fecha_estimada_entrega	date;
    v_hora_estimada_entrega	time;
    v_forma_pedido	varchar;
    v_id_gestion	integer;
    v_id_venta		integer;
    v_codigo_proceso	varchar;
    v_id_funcionario_inicio integer;

    v_num_tramite			varchar;
    v_id_proceso_wf			integer;
    v_id_estado_wf			integer;
    v_codigo_estado			varchar;
    v_tipo_cambio_venta		numeric;
    v_fecha_ini				varchar;
     v_fecha_fin			varchar;
     v_estado_periodo		varchar;
     v_formula				record;
    v_monto_venta			numeric;
    v_total_venta			numeric;
    v_id_venta_detalle		integer;
    v_cantidad					integer;
    v_cantidad_exe				integer;
    v_cantidad_ace				integer;
    v_precio				numeric;
    venta_total				numeric;
    v_codigo_tarjeta		varchar;
    v_codigo_fp				varchar;
    v_res					varchar;
    v_monto					numeric;
    v_acumulado_fp			numeric;
    v_id_venta_forma_pago	numeric;
    v_total_venta_exportacion	numeric;

    v_id_moneda_pais		integer;
    v_fecha_tc				date;
    v_tipo_cambio_expo		numeric;

    v_nombre_producto		varchar;
    v_id_producto			varchar;
    v_id_moneda_paquetes	varchar;
    v_desc_moneda			varchar;
    v_cliente				record;
    /****************************************/


BEGIN

    v_nombre_funcion = 'vef.ft_venta_facturacion_exportacion_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_FACTEXPOR_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		10-05-2019 19:08:47
	***********************************/

	if(p_transaccion='VF_FACTEXPOR_INS')then

        begin

        /*********************Inserccion de cliente si existe o no******************************/

         if (pxp.f_is_positive_integer(v_parametros.id_cliente)) THEN
          v_id_cliente = v_parametros.id_cliente::integer;

          IF(trim(v_parametros.nit) = '' or v_parametros.nit is null)then
          	raise exception 'El nit no puede ser vacio verifique los datos';
          end if;


          update vef.tcliente
          set nit = trim(v_parametros.nit),
              nombre_factura = UPPER(regexp_replace(trim(v_parametros.nombre_factura), '[^a-zA-ZñÑ ]+', '','g')),
              direccion = UPPER(trim(v_parametros.direccion_cliente))
          where id_cliente = v_id_cliente;

          select c.nombre_factura into v_nombre_factura
          from vef.tcliente c
          where c.id_cliente = v_id_cliente;

        else

          IF(trim(v_parametros.nit) = '' or trim(v_parametros.nit) is null)then
          	raise exception 'El nit no puede ser vacio verifique los datos';
          end if;

          INSERT INTO
            vef.tcliente
            (
              id_usuario_reg,
              fecha_reg,
              estado_reg,
              nombre_factura,
              nit,
              direccion
            )
          VALUES (
            p_id_usuario,
            now(),
            'activo',
            UPPER(regexp_replace(trim(v_parametros.nombre_factura), '[^a-zA-ZñÑ ]+', '','g')),
            trim(v_parametros.nit),
            UPPER(trim(v_parametros.direccion_cliente))
          ) returning id_cliente into v_id_cliente;

          v_nombre_factura = regexp_replace(trim(v_parametros.nombre_factura), '[^a-zA-ZñÑ ]+', '','g');

        end if;

       /***************************************************************************************/

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
          if (pxp.f_existe_parametro(p_tabla,'id_moneda_venta')) then
            	v_id_moneda_venta = v_parametros.id_moneda_venta;
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


         v_tipo_factura = 'factura_exportacion';


        SELECT tv.tipo_base into v_tipo_base
        from vef.ttipo_venta tv
        where tv.codigo = v_tipo_factura and tv.estado_reg = 'activo';


          if (v_tipo_base is null) then
            raise exception 'No existe un tipo de venta con el codigo: % consulte con el administrador del sistema',v_tipo_factura;
          end if;

        	v_excento = 0;

        	if (v_tipo_base = 'factura_exportacion') THEN
          		v_fecha = now()::date;
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
          v_fecha_estimada_entrega = now();
          /***********************************************************************************/

          /************************Verificamos si existe hora_estimada_entrega**************************************/
          v_hora_estimada_entrega = now()::time;
          /*********************************************************************************************************/

          /**********************Verificamos si existe forma_pedido************************************************/
          v_forma_pedido =NULL;
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
         v_tipo_cambio_venta = v_parametros.tipo_cambio;


          /*Aqui control para no Anular Facturas cuando el periodo este cerrado*/
          select
                 per.fecha_ini,
                 per.fecha_fin,
                 cp.estado
                 into
                 v_fecha_ini,
                 v_fecha_fin,
                 v_estado_periodo
          from param.tgestion ges
          inner join param.tperiodo per on per.id_gestion = ges.id_gestion
          inner join conta.tperiodo_compra_venta cp on cp.id_periodo = per.id_periodo
          where v_fecha between per.fecha_ini and per.fecha_fin
           and cp.id_depto = (select depo.id_depto
          from param.tdepto depo
          where depo.codigo = 'CON');
          /*********************************************************************/

          if (v_estado_periodo = 'cerrado') then
              raise exception 'No se puede registrar la factura debido a que el periodo %, %, se encuentra cerrado',v_fecha_ini,v_fecha_fin;
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
          id_formula


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
          NULL,
          v_porcentaje_descuento,
          v_comision,
          upper(v_parametros.observaciones),
          v_num_ven,
          v_tipo_factura,
          v_fecha,
          NULL,
          NULL,
          0,--v_excento,
          v_id_moneda_venta,
          0,
          0,
          0,
          0,
          0,
          0,
          COALESCE(v_tipo_cambio_venta,0),
          0,
          '',
          trim(v_parametros.nit),
          upper(regexp_replace(trim(v_nombre_factura), '[^a-zA-ZñÑ ]+', '','g')),
          NULL,
          v_hora_estimada_entrega,
          'no',
          v_forma_pedido,
          v_parametros.id_formula

        ) returning id_venta into v_id_venta;


        if ( v_parametros.id_formula is not null) then

          for v_formula  in	(select  form.id_concepto_ingas,
                                     ing.precio,
                                     ing.id_moneda
                                     from vef.tformula_detalle form
                                     left join param.tconcepto_ingas ing on ing.id_concepto_ingas = form.id_concepto_ingas
                              		 where form.id_formula = v_parametros.id_formula) LOOP




          if (v_id_moneda_venta != v_formula.id_moneda) then

          		v_monto_venta = param.f_convertir_moneda(v_formula.id_moneda::integer,v_id_moneda_venta::integer,v_formula.precio::numeric,now()::date,'CUS',2, NULL,'si');
          else
          		v_monto_venta = v_formula.precio;

          end if;

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
            v_monto_venta::numeric,
            1,
            v_monto_venta::numeric,
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
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Factura Computarizada almacenado(a) con exito');
           -- v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_id_venta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'VF_FACEXP_DET_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		23-04-2021 10:33:22
	***********************************/

	elsif(p_transaccion='VF_FACEXP_DET_INS')then

        BEGIN

        	--Sentencia de la insercion
        	insert into vef.tventa_detalle(
			id_venta,
			descripcion,
			cantidad,
			tipo,
			estado_reg,
			id_producto,
			precio,
            id_usuario_reg,
            fecha_reg
          	) values(
			v_parametros.id_venta,
			v_parametros.descripcion,
			v_parametros.cantidad_det,
			v_parametros.tipo,
			'activo',
            v_parametros.id_producto,
			--v_parametros.id_producto,
			v_parametros.precio,
            p_id_usuario,
            now()

			)RETURNING id_venta_detalle into v_id_venta_detalle;

            /*Verificamos si el concepto es contabilizable para no mezclar*/

       	select count(distinct inga.contabilizable),
          count(distinct inga.excento),
          count(distinct inga.id_actividad_economica)
         into v_cantidad,v_cantidad_exe, v_cantidad_ace
        from vef.tventa_detalle det
        inner join param.tconcepto_ingas inga on inga.id_concepto_ingas = det.id_producto
        where det.id_venta = v_parametros.id_venta;

        if (v_cantidad_exe > 1) then
          raise exception 'No puede utilizar conceptos con exentos si y exentos no, en la misma venta';
        end if;

        if(v_cantidad_ace > 1)then
          raise exception 'No puede utilizar conceptos de diferente actividad economica en la misma venta';
        end if;

        if (v_cantidad > 1) then
          raise exception 'No puede utilizar conceptos contabilizables y no contabilizables en la misma venta';
        else

          update vef.tventa set contabilizable =
           (
                          select distinct(sp.contabilizable)
                          from vef.tventa_detalle vd
                            inner join param.tconcepto_ingas sp on sp.id_concepto_ingas = vd.id_producto
                          where vd.id_venta = v_parametros.id_venta)

          where id_venta = v_parametros.id_venta;

        end if;

        /******************************************************************/

            select sum(ven.precio * ven.cantidad) into v_total_venta
              from vef.tventa_detalle ven
              where  ven.id_venta = v_parametros.id_venta;

            /*Aqui calculamos los datos de la venta de exportacion*/
            select (COALESCE(ven.valor_bruto,0) + COALESCE(ven.transporte_fob,0) + COALESCE(ven.seguros_fob,0) +
            		COALESCE(ven.otros_fob,0) + COALESCE(ven.transporte_cif,0) + COALESCE(ven.seguros_cif,0) + COALESCE(ven.otros_cif,0))
                    into
                    v_total_venta_exportacion
            from vef.tventa ven
            where  ven.id_venta = v_parametros.id_venta;



              update vef.tventa set
                total_venta = (v_total_venta + v_total_venta_exportacion),
                total_venta_msuc = (v_total_venta + v_total_venta_exportacion)
              where id_venta = v_parametros.id_venta;



			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','venta_detalle_facturacion almacenado(a) con exito (id_venta_detalle'||v_id_venta_detalle||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta_detalle',v_id_venta_detalle::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'VF_FACEXP_DET_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		23-04-2021 10:33:22
	***********************************/

	elsif(p_transaccion='VF_FACEXP_DET_MOD')then

		begin

            		v_precio = v_parametros.precio;

            select count(distinct inga.contabilizable),
                   count(distinct inga.excento),
                   count(distinct inga.id_actividad_economica)
             into v_cantidad, v_cantidad_exe, v_cantidad_ace
            from vef.tventa_detalle det
            inner join param.tconcepto_ingas inga on inga.id_concepto_ingas = det.id_producto
            where det.id_venta = v_parametros.id_venta;

            if (v_cantidad_exe > 1) then
              raise exception 'No puede utilizar conceptos con exentos si y exentos no, en la misma venta';
            end if;
            if(v_cantidad_ace > 1)then
              raise exception 'No puede utilizar conceptos de diferente actividad economica en la misma venta';
            end if;
            if (v_cantidad > 1) then
              raise exception 'No puede utilizar conceptos contabilizables y no contabilizables en la misma venta';
            end if;

			update vef.tventa_detalle set
            tipo = v_parametros.tipo,
            id_producto = v_parametros.id_producto,
            cantidad = v_parametros.cantidad_det,
            precio = v_precio
			where id_venta_detalle=v_parametros.id_venta_detalle;

            /*Obtenemos el total de la venta*/
            select sum((ven.precio * ven.cantidad)) into venta_total
            from vef.tventa_detalle ven
            where ven.id_venta = v_parametros.id_venta;

            /***Actualizamos el total de la venta***/
            update vef.tventa set
            total_venta = venta_total,
            total_venta_msuc = venta_total
            --excento = v_parametros.excento
            where id_venta = v_parametros.id_venta;
            /*****************************************/

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','venta_detalle_facturacion modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta_detalle',v_parametros.id_venta_detalle::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;


    /*********************************
 	#TRANSACCION:  'VF_FACEXP_FP_INS'
 	#DESCRIPCION:	Insercion de registros n formas de pago
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		23-04-2021 10:33:22
	***********************************/

	elsif(p_transaccion='VF_FACEXP_FP_INS')then

        begin
        	/*Aqui Hacemos las validaciones de las tarjetas y mco ingrseados*/

        	 if (v_parametros.id_medio_pago is not null and v_parametros.id_medio_pago != 0) then

                  select mp.mop_code, fp.fop_code into v_codigo_tarjeta, v_codigo_fp
                  from obingresos.tmedio_pago_pw mp
                  inner join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
                  where mp.id_medio_pago_pw = v_parametros.id_medio_pago;

                  v_codigo_tarjeta = (case when v_codigo_tarjeta is not null then
                                                  v_codigo_tarjeta
                                          else
                                                NULL
                                        end);

                  if (v_codigo_tarjeta is not null and v_codigo_fp = 'CC') then
                      if (substring(v_parametros.num_tarjeta::varchar from 1 for 1) != 'X') then
                          v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.num_tarjeta::varchar,v_codigo_tarjeta);
                      end if;
                  end if;
              end if;


              if (left (v_parametros.mco,3)  <> '930' and v_parametros.mco <> '')then
                  raise exception 'El numero del MCO tiene que empezar con 930';
              end if;

              if (char_length(v_parametros.mco::varchar) <> 15 and v_parametros.mco <> '' ) then
                  raise exception 'El numero del MCO debe tener 15 digitos obligatorios, 930000000012345';
              end if;
            /************************************************************************************************/

            v_monto = 0;
            v_acumulado_fp = 0;

            IF (v_parametros.id_moneda = 2) then
            	v_monto = v_parametros.monto_forma_pago;
            else
            	v_monto = v_parametros.monto_forma_pago;
            end if;



            --Sentencia de la insercion
        	insert into vef.tventa_forma_pago(
            usuario_ai,
            fecha_reg,
            id_usuario_reg,
            id_usuario_ai,
            estado_reg,
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
            nro_mco
            /****************************/
          )
          values(
            v_parametros._nombre_usuario_ai,
            now(),
            p_id_usuario,
            v_parametros._id_usuario_ai,
            'activo',
            v_parametros.id_venta,
            v_monto,
            0,
            0,
            0,
            v_parametros.num_tarjeta,
            replace(upper(v_parametros.codigo_tarjeta),' ',''),
            v_parametros.id_auxiliar,
            '',
            /*Aumentamos el id_instancia y el id_moneda*/
            v_parametros.id_medio_pago,
            v_parametros.id_moneda,
            v_parametros.mco
            /****************************/
          )RETURNING id_venta_forma_pago into v_id_venta_forma_pago;


          select sum((ven.precio * ven.cantidad)) into venta_total
            from vef.tventa_detalle ven
            where ven.id_venta = v_parametros.id_venta;

          v_total_venta_exportacion = (COALESCE(v_parametros.valor_bruto,0) + COALESCE(v_parametros.transporte_fob,0) + COALESCE(v_parametros.seguros_fob,0) + COALESCE(v_parametros.otros_fob,0)
          				   + COALESCE(v_parametros.transporte_cif,0) + COALESCE(v_parametros.seguros_cif,0) + COALESCE(v_parametros.otros_cif,0));

          /*Aqui Actualizamos los siguientes campos en ventas*/
          update vef.tventa set
          valor_bruto = v_parametros.valor_bruto,
          transporte_fob = v_parametros.transporte_fob,
          seguros_fob = v_parametros.seguros_fob,
          otros_fob = v_parametros.otros_fob,
          transporte_cif = v_parametros.transporte_cif,
          seguros_cif = v_parametros.seguros_cif,
          otros_cif = v_parametros.otros_cif,
          total_venta = (venta_total + v_total_venta_exportacion),
          total_venta_msuc = (venta_total + v_total_venta_exportacion)
          where id_venta = v_parametros.id_venta;


          /***************************************************/





			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de Pago almacenado(a) con exito (id_venta_forma_pago'||v_id_venta_forma_pago||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta_forma_pago',v_id_venta_forma_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;


    	/*********************************
        #TRANSACCION: 'VF_TC_EXPO_IME'
        #DESCRIPCION: RECUPERA EL TIPO DE CAMBIO
        #AUTOR: Ismael Valdivia
        #FECHA: 26-04-2021
        ***********************************/

        elsif (p_transaccion = 'VF_TC_EXPO_IME') then

        BEGIN

        	select
						tcpa.id_tipo_cambio_pais,
						tcpa.fecha,
						tcpa.oficial
                        into
                        v_id_moneda_pais,
                        v_fecha_tc,
                        v_tipo_cambio_expo
          from conta.ttipo_cambio_pais tcpa
          inner join segu.tusuario usu1 on usu1.id_usuario = tcpa.id_usuario_reg
          left join segu.tusuario usu2 on usu2.id_usuario = tcpa.id_usuario_mod
          where tcpa.id_moneda_pais = v_parametros.id_moneda_pais and tcpa.fecha = v_parametros.fecha_cambio::date;

          --Definition of the response
            v_resp = pxp.f_agrega_clave(v_resp, 'message ', 'Tipo Cambio Mon Sel');
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_cambio_pais',v_id_moneda_pais::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'fecha',v_fecha_tc::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'oficial',v_tipo_cambio_expo::varchar);

          --Returns the answer
            return v_resp;

        END;


        /*********************************
        #TRANSACCION: 'VF_TC_CON_EXPO_IME'
        #DESCRIPCION: RECUPERA EL TIPO DE CAMBIO DEL CONCEPTO
        #AUTOR: Ismael Valdivia
        #FECHA: 29-04-2021
        ***********************************/

        elsif (p_transaccion = 'VF_TC_CON_EXPO_IME') then

        BEGIN
        	select
						tcpa.id_tipo_cambio_pais,
						tcpa.fecha,
						tcpa.oficial
                        into
                        v_id_moneda_pais,
                        v_fecha_tc,
                        v_tipo_cambio_expo
          from conta.ttipo_cambio_pais tcpa
          inner join segu.tusuario usu1 on usu1.id_usuario = tcpa.id_usuario_reg
          left join segu.tusuario usu2 on usu2.id_usuario = tcpa.id_usuario_mod
          where tcpa.id_moneda_pais = ( select mon.id_moneda_pais
                                          from conta.tmoneda_pais mon
                                          where mon.id_lugar = 1
                                          and mon.filtrar_combo = 'si'
                                          and mon.id_moneda = v_parametros.id_moneda)  and tcpa.fecha = v_parametros.fecha_cambio::date;

          --Definition of the response
            v_resp = pxp.f_agrega_clave(v_resp, 'message ', 'Tipo Cambio Mon Sel');
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_cambio_pais',v_id_moneda_pais::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'fecha',v_fecha_tc::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'oficial_concepto',v_tipo_cambio_expo::varchar);

          --Returns the answer
            return v_resp;

        END;

    	/*********************************
        #TRANSACCION: 'VF_EXP_FORM_INS'
        #DESCRIPCION: RECUPERA LAS FORMULAS
        #AUTOR: Ismael Valdivia Aranibar
        #FECHA: 29-04-2021
        ***********************************/

        elsif (p_transaccion = 'VF_EXP_FORM_INS') then

        BEGIN

          select
                string_agg (ing.desc_ingas, ','),
                string_agg  (ing.id_concepto_ingas::varchar,','),
                string_agg (ing.precio::varchar, ','),
                string_agg (ing.id_moneda::varchar, ','),
                string_agg (mon.codigo_internacional::varchar, ',')
                into
                v_nombre_producto,
                v_id_producto,
                v_precio,
                v_id_moneda_paquetes,
                v_desc_moneda
          from vef.tformula_detalle form
          inner join param.tconcepto_ingas ing on ing.id_concepto_ingas = form.id_concepto_ingas
          left join param.tmoneda mon on mon.id_moneda = ing.id_moneda
          where form.id_formula = v_parametros.id_formula;

          --Definition of the response
            v_resp = pxp.f_agrega_clave(v_resp, 'message ', 'Contador');
            v_resp = pxp.f_agrega_clave(v_resp,'v_nombre_producto',v_nombre_producto::varchar);
           	v_resp = pxp.f_agrega_clave(v_resp,'v_id_producto',v_id_producto::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_precio',v_precio::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_id_moneda_paquetes',v_id_moneda_paquetes::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_desc_moneda',v_desc_moneda::varchar);

          --Returns the answer
            return v_resp;

        END;

        /*********************************
        #TRANSACCION:  'VEF_CLIEXPO_MOD'
        #DESCRIPCION:	Recuperacion del Cliente
        #AUTOR:		Ismael Valdivia
        #FECHA:		04-05-2021 08:00:00
        ***********************************/

        elsif(p_transaccion='VEF_CLIEXPO_MOD')then

        begin

           IF (trim(v_parametros.nit) != '') THEN
                select cli.id_cliente,
                       cli.nombre_factura,
                       cli.nit,
                       cli.direccion
                INTO
                       v_cliente
                from vef.tcliente cli
                where trim(cli.nit) = trim(v_parametros.nit)
                ORDER BY cli.id_cliente DESC
                limit 1;
           end if;

           IF (v_parametros.razon_social != '') THEN
                select cli.id_cliente,
                       cli.nombre_factura,
                       cli.nit,
                       cli.direccion
                into
                       v_cliente
                from vef.tcliente cli
                where cli.nombre_factura = UPPER(v_parametros.razon_social)
                ORDER BY cli.id_cliente DESC
                limit 1;
           end if;




            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Datos del deposito');
            v_resp = pxp.f_agrega_clave(v_resp,'id_cliente',v_cliente.id_cliente::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'nit',v_cliente.nit::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'razon',v_cliente.nombre_factura::varchar);
			v_resp = pxp.f_agrega_clave(v_resp,'direccion',v_cliente.direccion::varchar);
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

ALTER FUNCTION vef.ft_venta_facturacion_exportacion_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
