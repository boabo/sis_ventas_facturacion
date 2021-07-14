CREATE OR REPLACE FUNCTION vef.ft_emision_facturacion_exportacion_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_emision_facturacion_exportacion_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tventa'
 AUTOR: 		 (ismael.valdivia)
 FECHA:	        27-04-2021 13:40:47
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

    v_registros				record;
    v_id_dosificacion		integer;
    v_tiene_formula			varchar;

    v_valor_bruto			numeric;
    v_transporte_fob		numeric;
    v_seguros_fob			numeric;
    v_otros_fob				numeric;
    v_transporte_cif		numeric;
    v_seguros_cif			numeric;
    v_otros_cif				numeric;
    v_id_concepto_ingas		integer;
    v_descripcion			varchar;
    v_tmp					record;
    v_total					numeric;
    v_id_tipo_estado		integer;
    v_id_funcionario		integer;
    v_venta					record;
    v_estado_finalizado		integer;
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
    v_fecha_venta			date;
    v_id_actividad_economica	integer[];

    v_dosificacion_sucursal	record;
    v_dosificacion_concepto	record;
    v_dosificacion_por_concepto record;
    v_dosificacion			record;
    v_nro_factura			integer;
    v_monto_fp				numeric;
    v_suma_fp				numeric;
    v_suma_det				numeric;
    v_cantidad_fp			integer;
    v_venta_emi				record;
    vef_estados_validar_fp	varchar;

    v_id_moneda_base		integer;
    v_monto_total_base		numeric;
    v_tipo_cambio_local		numeric;

    v_host varchar;
    v_puerto varchar;
    v_dbname varchar;
    p_user varchar;
    v_password varchar;
    v_semilla	varchar;

    v_cuenta_usu	varchar;
    v_pass_usu		varchar;
    v_cadena_cnx	varchar;
    v_conexion 		varchar;
    v_id_factura	integer;
    v_tipo_pv		varchar;
    v_consulta		varchar;
    v_cajero		varchar;
    v_res_cone		varchar;

    v_id_unidad_medida	integer;
    /****************************************/


BEGIN

    v_nombre_funcion = 'vef.ft_emision_facturacion_exportacion_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
     #TRANSACCION:  'VF_EXP_FOP_ELI'
     #DESCRIPCION:	Eliminacion de formas de pago relacionadas a una venta
     #AUTOR:		Ismael Valdivia
     #FECHA:		27-04-2021 12:33:00
    ***********************************/

    if(p_transaccion='VF_EXP_FOP_ELI')then

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
     #TRANSACCION:  'VF_EXP_DET_ELI'
     #DESCRIPCION:	Eliminacion de los detalles relacionados a una venta
     #AUTOR:		Ismael Valdivia
     #FECHA:		27-04-2021 12:33:00
    ***********************************/

    elsif(p_transaccion='VF_EXP_DET_ELI')then

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
     #TRANSACCION:  'VF_EXP_MOD'
     #DESCRIPCION:	Modificacion de registros
     #AUTOR:		Ismael Valdivia
     #FECHA:		27-04-2021 12:33:00
    ***********************************/

    elsif(p_transaccion='VF_EXP_MOD')then

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
        v_excento = v_parametros.excento;

        /*Obtenemos la moneda base dependiento de la sucursal*/
        v_id_moneda_venta = v_parametros.id_moneda_venta;

       /* if (pxp.f_existe_parametro(p_tabla,'id_moneda')) then
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
        end if;*/
        /*****************************************************************************/

        /*Verificamos si existe los siguiente parametros*/
       -- if (pxp.f_existe_parametro(p_tabla,'transporte_fob')) then
          v_transporte_fob = v_parametros.transporte_fob;
          v_seguros_fob = v_parametros.seguros_fob;
          v_otros_fob = v_parametros.otros_fob;
          v_transporte_cif = v_parametros.transporte_cif;
          v_seguros_cif = v_parametros.seguros_cif;
          v_otros_cif = v_parametros.otros_cif;
          v_valor_bruto = v_parametros.valor_bruto;

        --end if;

        if (pxp.f_existe_parametro(p_tabla,'tipo_cambio')) then
          v_tipo_cambio_venta = v_parametros.tipo_cambio;
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


        /*Verificamos si el id_cliente es entero para insertar al nuevo cliente*/
        if (pxp.f_is_positive_integer(v_parametros.id_cliente)) THEN
          v_id_cliente = v_parametros.id_cliente::integer;

          update vef.tcliente
          set nit = trim(v_parametros.nit),
              nombre_factura = regexp_replace(trim(v_parametros.nombre_factura), '[^a-zA-ZñÑ ]+', '','g'),
              direccion = upper(trim(v_parametros.direccion_cliente))
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
              nit,
              direccion
            )
          VALUES (
            p_id_usuario,
            now(),
            'activo',
            upper(regexp_replace(trim(v_parametros.nombre_factura), '[^a-zA-ZñÑ ]+', '','g')),
            trim(v_parametros.nit),
            upper(trim(v_parametros.direccion_cliente))
          ) returning id_cliente into v_id_cliente;

          v_nombre_factura = upper(regexp_replace(trim(v_parametros.nombre_factura), '[^a-zA-ZñÑ ]+', '','g'));
        end if;
       /*************************************************************************/

        --Sentencia de la modificacion

        update vef.tventa set
          id_cliente = v_id_cliente,
          id_sucursal = v_id_sucursal,
          --a_cuenta = v_a_cuenta,
          --fecha_estimada_entrega = v_fecha_estimada_entrega,
          --hora_estimada_entrega = v_hora_estimada_entrega,
          id_usuario_mod = p_id_usuario,
          fecha_mod = now(),
          id_usuario_ai = v_parametros._id_usuario_ai,
          usuario_ai = v_parametros._nombre_usuario_ai,
          id_punto_venta = v_id_punto_venta,
          --id_vendedor_medico = v_id_vendedor_medico,
         -- porcentaje_descuento = v_porcentaje_descuento,
          comision = v_comision,
          --tiene_formula = v_tiene_formula,
          observaciones = upper(v_parametros.observaciones),
          --forma_pedido = v_forma_pedido,
          /*fecha = (case when v_fecha is null then
            fecha
                   else
                     v_fecha
                   end),
          nro_factura = v_nro_factura,*/
          --id_dosificacion = v_id_dosificacion,
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
          --descripcion_bulto = COALESCE(v_descripcion_bulto,''),
          nit = trim(v_parametros.nit),
          nombre_factura = upper(regexp_replace(trim(v_nombre_factura), '[^a-zA-ZñÑ ]+', '','g'))
          --id_cliente_destino = v_id_cliente_destino
        where id_venta=v_parametros.id_venta;



        if (v_parametros.id_medio_pago != 0 ) then


        /*Comentamos para la validacion de tarjetas dscomentar a futuro*/

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


          delete from vef.tventa_forma_pago
          where id_venta = v_parametros.id_venta;

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
            /*Aumentando la instancia de pago*/
            id_medio_pago,
            id_moneda,
            /*********************************/
            nro_mco
          )
          values(
            v_parametros._nombre_usuario_ai,
            now(),
            p_id_usuario,
            v_parametros._id_usuario_ai,
            'activo',
            --v_parametros.id_forma_pago,
            v_parametros.id_venta,
            v_parametros.monto_forma_pago,
            0,
            0,
            0,
            v_parametros.numero_tarjeta,
            replace(upper(v_parametros.codigo_tarjeta),' ',''),
            v_parametros.id_auxiliar,
            v_parametros.tipo_tarjeta,
            /*Aumentando la instancia de pago*/
            v_parametros.id_medio_pago,
            v_parametros.id_moneda,
            /*********************************/
            v_parametros.mco
          );

             if (v_parametros.id_medio_pago_2 is not null) then
           /*******************************Control para la tarjeta 2******************************/

                  if (v_parametros.id_medio_pago_2 is not null and v_parametros.id_medio_pago_2 != 0) then

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
                end if;

            if (left (v_parametros.mco_2,3)  <> '930' and v_parametros.mco_2 <> '')then
                raise exception 'El numero del MCO tiene que empezar con 930';
            end if;

            if (char_length(v_parametros.mco_2::varchar) <> 15 and v_parametros.mco_2 <> '' ) then
                raise exception 'El numero del MCO debe tener 15 digitos obligatorios, 930000000012345';
            end if;


          /**************************************************************************************/


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
              id_medio_pago,
              id_moneda,
              nro_mco
            )
            values(
              v_parametros._nombre_usuario_ai,
              now(),
              p_id_usuario,
              v_parametros._id_usuario_ai,
              'activo',
              --v_parametros.id_forma_pago_2,
              v_parametros.id_venta,
              v_parametros.monto_forma_pago_2,
              0,
              0,
              0,
              v_parametros.numero_tarjeta_2,
              replace(upper(v_parametros.codigo_tarjeta_2),' ',''),
              v_parametros.id_auxiliar_2,
              v_parametros.tipo_tarjeta_2,
              v_parametros.id_medio_pago_2,
              v_parametros.id_moneda_2,
              v_parametros.mco_2
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
     #TRANSACCION:  'VF_EXP_VEN_INS'
     #DESCRIPCION:	Insercion de registros
     #AUTOR:		Ismael Valdivia
     #FECHA:		27-04-2021 12:33:00
    ***********************************/

    elsif(p_transaccion='VF_EXP_VEN_INS')then

      begin

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
		/***********************Comentando para un futuro***********************/
        if (pxp.f_existe_parametro(p_tabla,'id_moneda_venta')) then
          v_id_moneda_venta = v_parametros.id_moneda_venta;
        else
          v_id_moneda_venta = null;
        end if;


      	v_tipo_factura = 'factura_exportacion';


        SELECT tv.tipo_base into v_tipo_base
        from vef.ttipo_venta tv
        where tv.codigo = v_tipo_factura and tv.estado_reg = 'activo';


        if (v_tipo_base is null) then
          raise exception 'No existe un tipo de venta con el codigo: % consulte con el administrador del sistema',v_tipo_factura;
        end if;

        v_excento = 0;

        v_fecha = now()::date;


        if (pxp.f_existe_parametro(p_tabla,'id_punto_venta')) then
          v_id_punto_venta = v_parametros.id_punto_venta;
        else
          v_id_punto_venta = NULL;
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

        if (v_id_punto_venta is not null) then
          select id_sucursal into v_id_sucursal
          from vef.tpunto_venta
          where id_punto_venta = v_id_punto_venta;
        else
          v_id_sucursal = v_parametros.id_sucursal;
        end if;


          v_a_cuenta = 0;
          v_comision = 0;
          v_fecha_estimada_entrega = now();
          v_hora_estimada_entrega = now()::time;
       	  v_forma_pedido =NULL;


       if (pxp.f_is_positive_integer(v_parametros.id_cliente)) THEN
          v_id_cliente = v_parametros.id_cliente::integer;

          IF(trim(v_parametros.nit) = '' or trim(v_parametros.nit) is null)then
          	raise exception 'El nit no puede ser vacio verifique los datos';
          end if;

          update vef.tcliente
          set nit = trim(v_parametros.nit),
              nombre_factura = regexp_replace(trim(v_parametros.nombre_factura), '[^a-zA-ZñÑ ]+', '','g'),
              direccion = upper(trim(v_parametros.direccion_cliente))
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
            regexp_replace(trim(v_parametros.nombre_factura), '[^a-zA-ZñÑ ]+', '','g'),
            trim(v_parametros.nit),
            upper(trim(v_parametros.direccion_cliente))
          ) returning id_cliente into v_id_cliente;

          v_nombre_factura = regexp_replace(trim(v_parametros.nombre_factura), '[^a-zA-ZñÑ ]+', '','g');

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


          v_transporte_fob = v_parametros.transporte_fob;
          v_seguros_fob = v_parametros.seguros_fob;
          v_otros_fob = v_parametros.otros_fob;
          v_transporte_cif = v_parametros.transporte_cif;
          v_seguros_cif = v_parametros.seguros_cif;
          v_otros_cif = v_parametros.otros_cif;
          v_valor_bruto = v_parametros.valor_bruto;



        if (pxp.f_existe_parametro(p_tabla,'tipo_cambio')) then
          v_tipo_cambio_venta = v_parametros.tipo_cambio;
        end if;


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
          --nro_factura,
          --id_dosificacion,
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
          anulado,
          /*Aumentando para registrar nuevos campos*/
          formato_factura_emitida,
          enviar_correo,
          correo_electronico,
          /*****************************************/
          /*Aumentamos la direccion cliente*/
          direccion_cliente
        ) values(
          v_id_venta,
          v_id_cliente,
          v_id_sucursal,
          v_id_proceso_wf,
          v_id_estado_wf,
          'activo',
          v_num_tramite,
          0,
          v_fecha_estimada_entrega,
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
          v_comision,
          upper(v_parametros.observaciones),
          v_num_ven,
          'computarizada',
          v_fecha,
          --v_nro_factura,
         -- v_id_dosificacion,
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
          '',
          trim(v_parametros.nit),
          upper(regexp_replace(trim(v_nombre_factura), '[^a-zA-ZñÑ ]+', '','g')),
          NULL,
          v_hora_estimada_entrega,
          v_tiene_formula,
          'factura_exportacion',
          '',
          '',

          '',
          '',
          '',
          /*Aumentamos la direccion cliente*/
          upper(v_parametros.direccion_cliente)


        ) returning id_venta into v_id_venta;
		--raise exception 'Aqui llega el dato %',v_parametros.monto_forma_pago;

--raise 'resp %',v_parametros.id_venta_recibo_2;

		if (v_parametros.id_medio_pago != 0) then
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
            /****************************/
            /*Aumentando campo para mco*/
            nro_mco,
            /*Aumentado campo para pago con recibo*/
            id_venta_recibo
          )
          values(
            v_parametros._nombre_usuario_ai,
            now(),
            p_id_usuario,
            v_parametros._id_usuario_ai,
            'activo',
            --v_parametros.id_forma_pago,
            v_id_venta,
            v_parametros.monto_forma_pago,
            0,
            0,
            0,
            v_parametros.numero_tarjeta,
            replace(upper(v_parametros.codigo_tarjeta),' ',''),
            v_parametros.id_auxiliar,
            '',
            /*Aumentamos el id_instancia y el id_moneda*/
            v_parametros.id_medio_pago,
            v_parametros.id_moneda,
            /****************************/
            /*Aumentando campo para mco*/
            v_parametros.mco,
            null
          );
        end if;

        /*Comentando para aumentar instancia de pago*/
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
         /*******************************Control para la tarjeta 2******************************/

          if (left (v_parametros.mco_2,3)  <> '930' and v_parametros.mco_2 <> '')then
              raise exception 'El numero del MCO tiene que empezar con 930';
          end if;

          if (char_length(v_parametros.mco_2::varchar) <> 15 and v_parametros.mco_2 <> '' ) then
              raise exception 'El numero del MCO debe tener 15 digitos obligatorios, 930000000012345';
          end if;
        /**************************************************************************************/

         --raise exception 'llega aqui para la insercion %',v_parametros.id_forma_pago;
        insert into vef.tventa_forma_pago(
            usuario_ai,
            fecha_reg,
            id_usuario_reg,
            id_usuario_ai,
            estado_reg,
            --id_forma_pago,
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
            tipo_tarjeta,
            nro_mco,
            id_venta_recibo
          )

          values(
            v_parametros._nombre_usuario_ai,
            now(),
            p_id_usuario,
            v_parametros._id_usuario_ai,
            'activo',
            --v_parametros.id_forma_pago_2,
            v_parametros.id_medio_pago_2,
            v_parametros.id_moneda_2,
            v_id_venta,
            v_parametros.monto_forma_pago_2,
            0,
            0,
            0,
            v_parametros.numero_tarjeta_2,
            replace(upper(v_parametros.codigo_tarjeta_2),' ',''),
            v_parametros.id_auxiliar_2,
            '',
            v_parametros.mco_2,
            null
          );
        end if;

        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Ventas almacenado(a) con exito (id_venta'||v_id_venta||')');
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_id_venta::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'id_proceso_wf',v_id_proceso_wf::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'id_estado_wf',v_id_estado_wf::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;

      /*********************************
     #TRANSACCION:  'VF_EXP_DET_INS'
     #DESCRIPCION:	Insercion de registros
     #AUTOR:		Ismael Valdivia
     #FECHA:		27-04-2021 12:33:00
    ***********************************/

    elsif(p_transaccion='VF_EXP_DET_INS')then

      begin

      	v_id_concepto_ingas = v_parametros.id_producto;
        v_porcentaje_descuento = 0;


        select ing.id_unidad_medida into v_id_unidad_medida
        from param.tconcepto_ingas ing
        where ing.id_concepto_ingas = v_id_concepto_ingas;



        if (pxp.f_existe_parametro(p_tabla,'descripcion')) then
          v_descripcion =  v_parametros.descripcion;
        else
          v_descripcion = '';
        end if;

        --Sentencia de la insercion
        insert into vef.tventa_detalle(
          id_venta,
          id_item,
          id_formula,
          tipo,
          estado_reg,
          cantidad,
          precio,
          fecha_reg,
          id_usuario_reg,
          id_usuario_mod,
          fecha_mod,
          precio_sin_descuento,
          porcentaje_descuento,
          id_vendedor,
          id_medico,
          descripcion,
          bruto,
          ley,
          kg_fino,
          id_unidad_medida,
          id_producto
        ) values(
          v_parametros.id_venta,
          v_id_concepto_ingas,
          --v_id_sucursal_producto,
          null,
          '',
          'activo',
          v_parametros.cantidad_det,
          round(v_parametros.precio,2),
          now(),
          p_id_usuario,
          null,
          null,
          v_parametros.precio,
          v_porcentaje_descuento,
          null,
          NULL,
          v_descripcion,
          0,
          0,
          0,
          v_id_unidad_medida,
          v_id_concepto_ingas
        )RETURNING id_venta_detalle into v_id_venta_detalle;

        --recupera datos de la venta
        select
          *
        into
          v_registros
        from vef.tventa v
        where v.id_venta = v_parametros.id_venta;


        select precio, cantidad into  v_tmp
        from vef.tventa_detalle
        where id_venta = v_parametros.id_venta;

        --IF v_parametros.tipo_factura = 'factura_exportacion' THEN
          v_total = COALESCE(v_registros.valor_bruto ,0) + COALESCE(v_registros.transporte_fob ,0)  + COALESCE(v_registros.seguros_fob ,0)+ COALESCE(v_registros.otros_fob ,0) + COALESCE(v_registros.transporte_cif ,0) +  COALESCE(v_registros.seguros_cif ,0) + COALESCE(v_registros.otros_cif ,0);
       -- ELSE
          --v_total = 0; --en la factura comun de exportacion el detalle ya incluye los precios fob y cif
       -- END IF;

        update vef.tventa
        set total_venta = round(v_total,2)
        where id_venta = v_parametros.id_venta;


        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle de Venta almacenado(a) con exito (id_venta_detalle'||v_id_venta_detalle||')');
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta_detalle',v_id_venta_detalle::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;

    /*********************************
 	#TRANSACCION:  'VEF_FIN_EXP_IME'
 	#DESCRIPCION:	funcion que controla el cambio al Siguiente estado de las ventas, integrado  con el WF
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		08-04-2019 10:20:00
	***********************************/

    elseif(p_transaccion='VEF_FIN_EXP_IME')then
      begin
       /*Recuperamos el id_tipo_estado y el id_estado_wf*/

       select
          ew.id_tipo_estado,
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

       	--v_resp = vef.ft_venta_facturacion_ime(p_administrador,p_id_usuario,v_tabla,'VF_FACVALI_MOD');

        -------------------------------------------VALIDCACIONES-------------------------------------------------------
        /*********************Aqui controles para la emision de la factura******************************/
        select
          v.* ,
          sm.id_moneda as id_moneda_base,
          m.codigo  as moneda ,
          v.id_dosificacion as id_dosificacion_venta
        into
          v_venta_emi
        from vef.tventa v
          inner join vef.tsucursal suc on suc.id_sucursal = v.id_sucursal
          inner join vef.tsucursal_moneda sm on suc.id_sucursal = sm.id_sucursal and sm.tipo_moneda = 'moneda_base'
          inner join param.tmoneda m on m.id_moneda = sm.id_moneda
        where id_venta = v_venta.id_venta;

        --si es venta de exportacion operamos con la moneda especificada por el usuario
        v_id_moneda_venta = v_venta_emi.id_moneda;

        --v_id_moneda_suc = v_venta_emi.id_moneda_base;
		select count(distinct vd.id_venta_detalle) into v_cantidad
        from vef.tventa_detalle vd
        where vd.id_venta = v_venta.id_venta;


        IF(v_cantidad=0)THEN
            raise exception 'Debe tener al menos un concepto registrado para la venta';
        END IF;


        --Validar que solo haya conceptos contabilizables o no contabilizables
        select count(distinct inga.contabilizable) into v_cantidad
        from vef.tventa_detalle det
        inner join param.tconcepto_ingas inga on inga.id_concepto_ingas = det.id_producto
        where det.id_venta = v_venta.id_venta;


        if (v_cantidad > 1) then
          raise exception 'No puede utilizar conceptos contabilizables y no contabilizables en la misma venta';
        end if;




          select sig.codigo into v_codigo_estado
          from vef.tventa v
          inner join wf.testado_wf e on e.id_estado_wf=v.id_estado_wf
          inner join wf.ttipo_estado te on te.id_tipo_estado=e.id_tipo_estado
          inner join wf.testructura_estado es on es.id_tipo_estado_padre=te.id_tipo_estado
          inner join wf.ttipo_estado sig on sig.id_tipo_estado=es.id_tipo_estado_hijo
          where id_venta=v_venta_emi.id_venta;


        --raise exception 'entra %', v_codigo_estado;
          select count(*) into v_cantidad_fp
          from vef.tventa_forma_pago
          where id_venta =   v_venta.id_venta;

          v_acumulado_fp = 0;

          	 for v_registros in (select vfp.id_venta_forma_pago, vfp.id_moneda,vfp.monto_transaccion
                              from vef.tventa_forma_pago vfp
                              where vfp.id_venta = v_venta.id_venta)loop


            /*if (v_registros.id_moneda != v_id_moneda_venta) then
              IF  v_venta_emi.tipo_cambio_venta is not null and v_venta_emi.tipo_cambio_venta != 0 THEN
              	v_monto_fp = param.f_convertir_moneda(v_registros.id_moneda,v_id_moneda_venta,v_registros.monto_transaccion,v_venta_emi.fecha::date,'CUS',2, v_venta_emi.tipo_cambio_venta,'si');
              ELSE
                v_monto_fp = param.f_convertir_moneda(v_registros.id_moneda,v_id_moneda_venta,v_registros.monto_transaccion,v_venta_emi.fecha::date,'O',2,NULL,'si');
              END IF;
            else
              v_monto_fp = v_registros.monto_transaccion;
            end if;*/

            select mon.id_moneda
            INTO
            v_id_moneda_base
            from param.tmoneda mon
            where mon.tipo_moneda = 'base';


            /*Aqui condicionales para el tipo de cambio y tener la moneda en dolar como en bs*/
            if (v_registros.id_moneda = 2 and v_id_moneda_venta = 2) then

                v_monto_fp = param.f_convertir_moneda(v_registros.id_moneda,v_id_moneda_base,v_registros.monto_transaccion,v_venta_emi.fecha::date,'CUS',2, v_venta_emi.tipo_cambio_venta,'si');
            	v_monto_total_base = param.f_convertir_moneda(v_id_moneda_venta,v_id_moneda_base,v_venta_emi.total_venta,v_venta_emi.fecha::date,'CUS',2, v_venta_emi.tipo_cambio_venta,'si');

            elsif (v_registros.id_moneda != 2 and v_id_moneda_venta = 2) then

            	v_monto_fp = v_registros.monto_transaccion;
            	v_monto_total_base = param.f_convertir_moneda(v_id_moneda_venta,v_id_moneda_base,v_venta_emi.total_venta,v_venta_emi.fecha::date,'CUS',2, v_venta_emi.tipo_cambio_venta,'si');

            elsif (v_registros.id_moneda = 2 and v_id_moneda_venta != 2) then

                v_monto_fp = param.f_convertir_moneda(v_registros.id_moneda,v_id_moneda_base,v_registros.monto_transaccion,v_venta_emi.fecha::date,'CUS',2, NULL,'si');
            	v_monto_total_base = v_venta_emi.total_venta;

            elsif (v_registros.id_moneda != 2 and v_id_moneda_venta != 2) then

            	v_monto_fp = v_registros.monto_transaccion;
            	v_monto_total_base = v_venta_emi.total_venta;

            end if;


            /*********************************************************************************/

            --si el monto de una d elas formas de pago es mayor q el total de la venta y la cantidad de formas de pago es mayor a 1 lanzo excepcion
            if (v_monto_fp >= v_monto_total_base and v_cantidad_fp > 1) then
              raise exception 'Se ha definido mas de una forma de pago, pero existe una que supera el valor de la venta(solo se requiere una forma de pago)';
            end if;

            update vef.tventa_forma_pago set
              monto = v_monto_fp,
              cambio = (case when (v_monto_fp + COALESCE(v_acumulado_fp,0) - v_monto_total_base) > 0 then
                (v_monto_fp + COALESCE(v_acumulado_fp,0) - v_monto_total_base)
                        else
                          0
                        end),
              monto_mb_efectivo = (case when (v_monto_fp + COALESCE(v_acumulado_fp,0) - v_monto_total_base ) > 0 then
                (v_monto_fp - (v_monto_fp + COALESCE(v_acumulado_fp,0) - v_monto_total_base))
                                   else
                                     v_monto_fp
                                   end)
            where id_venta_forma_pago = v_registros.id_venta_forma_pago;

            select tc.oficial
            		into
                    v_tipo_cambio_local
            from param.ttipo_cambio tc
            where tc.id_moneda = 2 and tc.fecha = v_venta_emi.fecha;


            update vef.tventa_forma_pago set
              monto_dolar_efectivo = round((monto_mb_efectivo / v_tipo_cambio_local),2)
            where id_venta_forma_pago = v_registros.id_venta_forma_pago;




            v_acumulado_fp = v_acumulado_fp + v_monto_fp;
          end loop;

          /************************************************************************************************************************************************************/

          select sum(round(monto_mb_efectivo,2)) into v_suma_fp
          from vef.tventa_forma_pago
          where id_venta =   v_venta.id_venta;

          select sum(round(cantidad*precio,2)) into v_suma_det
          from vef.tventa_detalle
          where id_venta =   v_venta.id_venta;

          --IF v_venta_emi.tipo_factura != 'computarizadaexpo' THEN
          --  v_suma_det = COALESCE(v_suma_det,0) + COALESCE(v_venta_emi.transporte_fob ,0)  + COALESCE(v_venta_emi.seguros_fob ,0)+ COALESCE(v_venta_emi.otros_fob ,0) + COALESCE(v_venta_emi.transporte_cif ,0) +  COALESCE(v_venta_emi.seguros_cif ,0) + COALESCE(v_venta_emi.otros_cif ,0);
          --END IF;

          if (v_suma_fp < v_monto_total_base) then
            raise exception 'El importe recibido es menor al valor de la venta, falta %', v_monto_total_base - v_suma_fp;
          end if;

          if (v_suma_fp > v_monto_total_base) then

          	if ((v_suma_fp - v_monto_total_base) > 0.05) then
            	raise exception 'El total de la venta no coincide con la división por forma de pago %',v_suma_fp;
            end if;
          end if;

         -- if (v_suma_det != v_monto_total_base) then
          --  raise exception 'El total de la venta no coincide con la suma de los detalles (% = %) en id: %',v_suma_det ,v_monto_total_base, v_venta.id_venta;
          --end if;


        select sum(cambio) into v_suma_fp
        from vef.tventa_forma_pago
        where id_venta =   v_venta.id_venta;



        --si es factura comercial de exportacion generamos el numero de factura y validamos la fecha

            select
              *
            into
              v_dosificacion
            from vef.tdosificacion dos
            where dos.id_dosificacion = v_venta_emi.id_dosificacion_venta;



            IF exists(select 1
                      from vef.tventa_detalle vd
                        inner join vef.tsucursal_producto sp on vd.id_sucursal_producto = sp.id_sucursal_producto
                        inner join param.tconcepto_ingas cig on  cig.id_concepto_ingas = sp.id_concepto_ingas
                      where vd.id_venta = v_venta_emi.id_venta and vd.estado_reg = 'activo'
                            AND  cig.id_actividad_economica != ANY(v_dosificacion.id_activida_economica)

						) THEN


              raise exception 'El nro de facura fue generado para la actividad economica: no puede introducir otros conceptos pertenecientes a otra actividad';

            END IF;

        ---------------------------------------------------------------------------------------------------------------

        /*Obtenemos el codigo finalizado y fin*/
       	select
          te.codigo,te.fin
        into
          v_codigo_estado_siguiente,v_es_fin
        from wf.ttipo_estado te
        where te.id_tipo_estado = v_estado_finalizado;
        /*********************************************************************/


        /*Verificcar si seran necesario*/
      	/*IF  pxp.f_existe_parametro(p_tabla,'id_depto_wf') THEN

          v_id_depto = v_parametros.id_depto_wf;

        END IF;*/
        v_id_depto = null;

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
                         where v.fecha > v_fecha_venta and v.tipo_factura = 'factura_exportacion' and
                               v.estado_reg = 'activo' and v.estado = 'finalizado'))THEN
              raise exception 'Existen facturas emitidas con fechas posterior a la actual. Por favor revise la fecha y hora del sistema';
            end if;
          END IF;

         select array_agg(distinct cig.id_actividad_economica) into v_id_actividad_economica
          from vef.tventa_detalle vd
            --inner join vef.tsucursal_producto sp on vd.id_sucursal_producto = sp.id_sucursal_producto
            --inner join param.tconcepto_ingas cig on  cig.id_concepto_ingas = sp.id_concepto_ingas
          	inner join param.tconcepto_ingas cig on cig.id_concepto_ingas = vd.id_producto
          where vd.id_venta = v_venta.id_venta and vd.estado_reg = 'activo';

          --genera el numero de factura
          IF v_venta.tipo_factura not in ('computarizadaexpo','computarizadaexpomin','computarizadamin') THEN


              /*Aqui separamos para veirificar la dosificacion por la sucursal y por el concepto*/

              select d.* into v_dosificacion_sucursal
              from vef.tdosificacion d
              where d.estado_reg = 'activo' and d.fecha_inicio_emi <= v_venta.fecha and
                    d.fecha_limite >= v_venta.fecha and d.tipo = 'F' and d.tipo_generacion = 'computarizada' and
                    d.nombre_sistema = 'SISTEMAFACTURACIONBOA' and
                    /*Aqui para tomar en cuenta la dosificacion diferenciando por el titulo*/
                    d.titulo = 'FACTURA COMERCIAL DE EXPORTACION' AND
                    /***********************************************************************/
                    d.id_sucursal = v_venta.id_sucursal;

              if (v_dosificacion_sucursal is null ) then
                  raise exception 'No existe una dosificacion registrada para la sucursal. Favor contactarse con personal de Contabilidad.';
              end if;

              for v_dosificacion_concepto in (
                                              select '{'||cig.id_actividad_economica||'}' as id_actividad,
                                                     cig.desc_ingas
                                              from vef.tventa_detalle vd
                                                inner join param.tconcepto_ingas cig on cig.id_concepto_ingas = vd.id_producto
                                              where vd.id_venta = v_venta.id_venta and vd.estado_reg = 'activo'
              )loop

              select d.* into v_dosificacion_por_concepto
              from vef.tdosificacion d
              where d.estado_reg = 'activo' and d.fecha_inicio_emi <= v_venta.fecha and
                    d.fecha_limite >= v_venta.fecha and d.tipo = 'F' and d.tipo_generacion = 'computarizada' and
                    d.id_sucursal = v_venta.id_sucursal and
                    d.nombre_sistema = 'SISTEMAFACTURACIONBOA' and
                    /*Aqui para tomar en cuenta la dosificacion diferenciando por el titulo*/
                    d.titulo = 'FACTURA COMERCIAL DE EXPORTACION' AND
                    /***********************************************************************/
                    d.id_activida_economica @> v_dosificacion_concepto.id_actividad::integer[];

                if (v_dosificacion_por_concepto is null) then
                    raise exception 'No existe parametrizada una dosificación para el concepto <b>%</b>. Favor Contactarse con personal de Contabilidad.',v_dosificacion_concepto.desc_ingas;
                end if;


              end loop;

              /**********************************************************************************/

            select d.* into v_dosificacion
            from vef.tdosificacion d
            where d.estado_reg = 'activo' and d.fecha_inicio_emi <= v_venta.fecha and
                  d.fecha_limite >= v_venta.fecha and d.tipo = 'F' and d.tipo_generacion = 'computarizada' and
                  d.id_sucursal = v_venta.id_sucursal and
                  /*Aqui para tomar en cuenta la dosificacion diferenciando por el titulo*/
                  d.titulo = 'FACTURA COMERCIAL DE EXPORTACION' AND
                  /***********************************************************************/
                  d.nombre_sistema = 'SISTEMAFACTURACIONBOA' AND
                  d.id_activida_economica @> v_id_actividad_economica FOR UPDATE;

            v_nro_factura = v_dosificacion.nro_siguiente;

            if (v_dosificacion is null) then
              raise exception 'No existe una dosificacion activa para emitir la factura';
            end if;


            --validar que el nro de factura no supere el maximo nro de factura de la dosificaiocn
            if (exists(	select 1
                         from vef.tventa ven
                         where ven.estado_reg = 'activo' and ven.nro_factura =  v_dosificacion.nro_siguiente and ven.id_dosificacion = v_dosificacion.id_dosificacion)) then
              raise exception 'El numero de factura ya existe para esta dosificacion. Por favor comuniquese con el administrador del sistema';
            end if;

            --la factura de exportacion no altera la fecha

            if(trim(v_venta.nit) = '' or v_venta.nit is null) then
             raise exception 'La factura no puede tener nit vacio';
            end if;


            update vef.tventa  set
              id_dosificacion = v_dosificacion.id_dosificacion,
              nro_factura = v_nro_factura,
              fecha = v_fecha_venta,
              cod_control = pxp.f_gen_cod_control(v_dosificacion.llave,
                                                  v_dosificacion.nroaut,
                                                  v_nro_factura::varchar,
                                                  v_venta.nit,
                                                  to_char(v_fecha_venta,'YYYYMMDD')::varchar,
                                                  round(v_venta.total_venta*v_venta.tipo_cambio_venta,0))
            where id_venta = v_venta.id_venta;


            update vef.tdosificacion
            set nro_siguiente = nro_siguiente + 1
            where id_dosificacion = v_dosificacion.id_dosificacion;

          END IF;



        end if;
        /*****************************************/


         if (v_es_fin = 'si' and pxp.f_get_variable_global('vef_tiene_apertura_cierre') = 'si') then
          update vef.tventa set id_usuario_cajero = p_id_usuario
          where id_venta = v_venta.id_venta;
         end if;



        /*Replicacion a la base de datos DB_FACTURAS 2019*/
   		/*Para migrar los datos a la nueva base de datos db_facturas_2019*/
		IF(pxp.f_get_variable_global('migrar_facturas') ='true')THEN
          /*Establecemos la conexion con la base de datos*/
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

            v_tipo_pv= 'FAC.EXPORTACION.COMPUT.CONTABLE';


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
                            exportacion_excentas,
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
                            '||(v_venta.total_venta*v_venta.tipo_cambio_venta)::numeric||',
                            '||(v_venta.total_venta*v_venta.tipo_cambio_venta)::numeric||',
                            0,
                            '''||pxp.f_gen_cod_control(v_dosificacion.llave,
                                                  v_dosificacion.nroaut,
                                                  v_nro_factura::varchar,
                                                  trim(v_venta.nit),
                                                  to_char(v_fecha_venta,'YYYYMMDD')::varchar,
                                                  round(v_venta.total_venta*v_venta.tipo_cambio_venta,0))||''',
                            '''||v_cajero||''',
                            ''computarizada'',
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
              /************************************/


        /*************************************************/

        -- si hay mas de un estado disponible  preguntamos al usuario
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado de la planilla)');
        v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');
        v_resp = pxp.f_agrega_clave(v_resp,'estado',v_codigo_estado_siguiente);
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_venta.id_venta::varchar);



        -- Devuelve la respuesta
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

ALTER FUNCTION vef.ft_emision_facturacion_exportacion_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
