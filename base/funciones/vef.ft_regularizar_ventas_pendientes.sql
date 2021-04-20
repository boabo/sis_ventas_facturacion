CREATE OR REPLACE FUNCTION vef.ft_regularizar_ventas_pendientes (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_regularizar_ventas_pendientes
 DESCRIPCION:   Funcion que regularizara los datos eliminados por error
 AUTOR: 		 (ivaldivia)
 FECHA:	        26-02-2021 09:43:50
 COMENTARIOS:
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


    /*Variables para conexion*/
    v_conexion varchar;
    v_cadena_cnx	varchar;
    v_sinc	varchar;
    v_consulta	varchar;
    v_id_factura	integer;
    v_res_cone	varchar;
    v_fecha_factura	varchar;
    v_cajero		varchar;

    v_concepto_excento	integer;
    v_consulta_inser		varchar;
    v_codigo_fp		varchar;
    /*************************/
    /*Instancias de pago*/
    v_id_instancia_pago varchar;
    v_nombre_instancia varchar;
    v_codigo_tarjeta_instancia varchar;
    v_numero_tarjeta varchar;
    v_monto_transaccion varchar;
    v_id_moneda integer;
    /********************/


    v_monto_fp_1	numeric;
    v_monto_fp_2	numeric;
    v_cambio		numeric;
    v_datos_anteriores record;
    v_datos_anteriores_2 record;
    v_existencia_fp2	integer;
    v_id_apertura_cierre			integer;
    v_punto_venta				varchar;

    /*Recuperamos para cuenta bancaria*/
    v_nro_cuenta	varchar;
	v_denominacion	varchar;
    v_id_cuenta_bancaria integer;
    v_cantidad_deposito  integer;
    v_id_deposito		integer;
    v_nro_deposito		varchar;
    v_fecha_deposito	varchar;
    v_monto_deposito	numeric;
    v_formato_factura	varchar;
    v_enviar_correo		varchar;
    v_correo_electronico	varchar;
    v_cantidad_apertura	numeric;
    v_existe_dosificacion	varchar;
    v_fecha_limite_emision	varchar;
    v_año_actual			varchar;
    v_id_dosificacion_ro	integer;
    v_existencia			numeric;

    /*Aumentando para verificar dosificacion*/
    v_dosificacion_sucursal	record;
    v_dosificacion_concepto	record;
    v_dosificacion_por_concepto record;
    /****************************************/

    /*Aumentando para los recibos en n formas de pago (Ismael Valdivia 09/12/2020)*/
    v_id_moneda_venta_recibo integer;
    v_id_auxiliar_anticipo	integer;
    v_cliente				record;
    v_boleto_asociado		varchar;
    v_inicial_boleto		varchar;
    v_datos_boletos			record;
    v_id_boleto_asociado	integer;
    v_requiere_excento		varchar;
    v_tipo_pv				varchar;
    /******************************************************************************/

    /**Aumentado para liquidaciones breydi vasquez 20/01/2021*/
    v_resp_informix			varchar[];

    v_tipo_usu				varchar;
    v_liquidacion			varchar=null;
    v_tipo_interf			varchar=null;

    v_estacion				varchar;
    v_nro_autorizacion		varchar;

BEGIN

    v_nombre_funcion = 'vef.ft_regularizar_ventas_pendientes';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_FACVEN_INS_ISMAEL'
 	#DESCRIPCION:	Insercion de formas de pago
 	#AUTOR:		ismael.valdivia
 	#FECHA:		26-02-2021 09:45:47
	***********************************/

	if(p_transaccion='VF_VEN_INS_ISMAEL')then

        begin

             update vef.tventa set
             observaciones = upper(v_parametros.observaciones)
             where id_venta = v_parametros.id_venta;

             if pxp.f_existe_parametro(p_tabla,'comision') then
               update vef.tventa set
               comision = v_parametros.comision
               where id_venta = v_parametros.id_venta;
             end if;


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
                  id_moneda
                  /****************************/
                )
                values(
                  v_parametros._nombre_usuario_ai,
                  now(),
                  p_id_usuario,
                  v_parametros._id_usuario_ai,
                  'activo',
                  v_parametros.id_venta,
                  v_parametros.monto_forma_pago,
                  0,
                  0,
                  0,
                  v_parametros.numero_tarjeta,
                  replace(upper(v_parametros.codigo_tarjeta),' ',''),
                  v_parametros.id_auxiliar,
                  v_parametros.tipo_tarjeta,
                  /*Aumentamos el id_instancia y el id_moneda*/
                  v_parametros.id_medio_pago,
                  v_parametros.id_moneda
                  /****************************/
                 );

                 if (v_parametros.id_medio_pago_2 is not null and v_parametros.id_medio_pago_2 != 0 ) then

                  select mp.mop_code, fp.fop_code into v_codigo_tarjeta, v_codigo_fp
                  from obingresos.tmedio_pago_pw mp
                  inner join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
                  where mp.id_medio_pago_pw = v_parametros.id_medio_pago_2;


                  v_codigo_tarjeta = (case when v_codigo_tarjeta is not null then
                                      v_codigo_tarjeta
                              else
                                    NULL
                            end);

                  if (v_codigo_tarjeta is not null and v_codigo_fp = 'CC') then
                      if (substring(v_parametros.numero_tarjeta_2::varchar from 1 for 1) != 'X') then
                          v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta_2::varchar,v_codigo_tarjeta);
                      end if;
                  end if;


                  insert into vef.tventa_forma_pago(
                                                    usuario_ai,
                                                    fecha_reg,
                                                    id_usuario_reg,
                                                    id_usuario_ai,
                                                    estado_reg,
                                                    id_medio_pago,
                                                    id_moneda,
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
                                                    v_parametros.id_medio_pago_2,
                                                    v_parametros.id_moneda_2,
                                                    v_parametros.id_venta,
                                                    v_parametros.monto_forma_pago_2,
                                                    0,
                                                    0,
                                                    0,
                                                    v_parametros.numero_tarjeta_2,
                                                    replace(upper(v_parametros.codigo_tarjeta_2),' ',''),
                                                    v_parametros.id_auxiliar_2,
                                                    v_parametros.tipo_tarjeta
                                                  );
                 end if;


        /*Aqui la validacion para el cambio*/
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
        	IF(v_cantidad=0 and v_parametros.anulado = 'VALIDA')THEN
        		raise exception 'Debe tener al menos un concepto registrado para la venta';
        	END IF;

        else

        	IF(v_cantidad=0)THEN
        		raise exception 'Debe tener al menos un concepto registrado para la venta';
        	END IF;

        end if;

        --Validar que solo haya conceptos contabilizables o no contabilizables
        select count(distinct inga.contabilizable) into v_cantidad
        from vef.tventa_detalle det
        inner join param.tconcepto_ingas inga on inga.id_concepto_ingas = det.id_producto
        where det.id_venta = v_parametros.id_venta;


        if (v_cantidad > 1) then
          raise exception 'No puede utilizar conceptos contabilizables y no contabilizables en la misma venta';
        else
              -- bvp
              if v_parametros.tipo_factura = 'manual' then
                      	if v_parametros.anulado = 'ANULADA' then
                              update vef.tventa set contabilizable = 'no'
                              where id_venta = v_parametros.id_venta;
                          else

                            update vef.tventa set contabilizable =
                             (
                                            select distinct(sp.contabilizable)
                                            from vef.tventa_detalle vd
                                              inner join param.tconcepto_ingas sp on sp.id_concepto_ingas = vd.id_producto
                                            where vd.id_venta = v_parametros.id_venta)

                            where id_venta = v_parametros.id_venta;
                          end if;
                      else

                         update vef.tventa set contabilizable =
                         (
                                        select distinct(sp.contabilizable)
                                        from vef.tventa_detalle vd
                                          inner join param.tconcepto_ingas sp on sp.id_concepto_ingas = vd.id_producto
                                        where vd.id_venta = v_parametros.id_venta)

                        where id_venta = v_parametros.id_venta;
              end if;
        end if;



		/*****************************VERIFICAMOS SI EL CONCEPTO TIENE EXCENTO*******************************/
        select count(*) into v_cantidad
        from vef.tventa_detalle vd
        left join param.tconcepto_ingas ing on ing.id_concepto_ingas = vd.id_producto
        --left join vef.tsucursal_producto sp on sp.id_sucursal_producto = vd.id_sucursal_producto
        where vd.id_venta = v_parametros.id_venta and ing.excento= 'si';
		/***************************************************************************************************/


        --Validar que si hay un concepto con excento el importe excento no sea 0
        if (v_venta.tipo_factura <> 'recibo') then
          if (v_cantidad > 0 and v_venta.excento < 0) then
            raise exception 'Tiene un concepto que requiere un importe excento y el importe excento para esta venta es menor 0';
          end if;
        end if;
      	--raise exception 'lelga cant:%, tipo_fac:%, v_venta.excento:%',v_cantidad,v_venta.tipo_factura,v_venta.excento;
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
        --if ((v_codigo_estado) = ANY(string_to_array(vef_estados_validar_fp,',')))then
        		  --raise exception 'entra %', v_codigo_estado;
          select count(*) into v_cantidad_fp
          from vef.tventa_forma_pago
          where id_venta =   v_parametros.id_venta;

          --lo que ya se pago es igual a lo que se tenia a cuenta, suponiendo q esta en la moneda base
          v_acumulado_fp = v_venta.a_cuenta;

		  /*******************************Obtenemos la moneda para realizar la converision si es en dolar (IRVA)****************************************/
                      /*for v_registros in (select vfp.id_venta_forma_pago, fp.id_moneda,vfp.monto_transaccion
                                          from vef.tventa_forma_pago vfp
                                            inner join vef.tforma_pago fp on fp.id_forma_pago = vfp.id_forma_pago
                                          where vfp.id_venta = v_parametros.id_venta)loop */
          	 for v_registros in (select vfp.id_venta_forma_pago, vfp.id_moneda,vfp.monto_transaccion
                              from vef.tventa_forma_pago vfp
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
              cambio = (case when (v_monto_fp + v_acumulado_fp - v_venta.total_venta + v_venta.comision) > 0 then
                (v_monto_fp + v_acumulado_fp - v_venta.total_venta) + v_venta.comision
                        else
                          0
                        end),
              monto_mb_efectivo = (case when (v_monto_fp + v_acumulado_fp - v_venta.total_venta + v_venta.comision) > 0 then
                (v_monto_fp - (v_monto_fp + v_acumulado_fp - v_venta.total_venta)) - v_venta.comision
                                   else
                                     v_monto_fp
                                   end)
            where id_venta_forma_pago = v_registros.id_venta_forma_pago;
            v_acumulado_fp = v_acumulado_fp + v_monto_fp;
          end loop;
          /************************************************************************************************************************************************************/

          select sum(round(monto_mb_efectivo,2)) into v_suma_fp
          from vef.tventa_forma_pago
          where id_venta =   v_parametros.id_venta;

          select sum(round(cantidad*precio,2)) into v_suma_det
          from vef.tventa_detalle
          where id_venta =   v_parametros.id_venta;

          IF v_parametros.tipo_factura != 'computarizadaexpo' THEN
            v_suma_det = COALESCE(v_suma_det,0) + COALESCE(v_venta.transporte_fob ,0)  + COALESCE(v_venta.seguros_fob ,0)+ COALESCE(v_venta.otros_fob ,0) + COALESCE(v_venta.transporte_cif ,0) +  COALESCE(v_venta.seguros_cif ,0) + COALESCE(v_venta.otros_cif ,0);
          END IF;

          if (v_suma_fp < (v_venta.total_venta - coalesce(v_venta.comision,0))) then
            raise exception 'El importe recibido es menor al valor de la venta, falta %', v_venta.total_venta - v_suma_fp;
          end if;

          if (v_suma_fp > (v_venta.total_venta - coalesce(v_venta.comision,0))) then
            raise exception 'El total de la venta no coincide con la división por forma de pago%',v_suma_fp;
          end if;

          if (v_suma_det != v_venta.total_venta) then
            raise exception 'El total de la venta no coincide con la suma de los detalles (% = %) en id: %',v_suma_det ,v_venta.total_venta, v_parametros.id_venta;
          end if;
       -- end if;

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


			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Factura Computarizada almacenado(a) con exito (id_venta'||v_id_venta||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_id_venta::varchar);

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

ALTER FUNCTION vef.ft_regularizar_ventas_pendientes (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
