CREATE OR REPLACE FUNCTION vef.ft_venta_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
  /**************************************************************************
   SISTEMA:		Sistema de Ventas
   FUNCION: 		vef.ft_venta_ime
   DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tventa'
   AUTOR: 		 (admin)
   FECHA:	        01-06-2015 05:58:00
   COMENTARIOS:
  ***************************************************************************
   HISTORIAL DE MODIFICACIONES:

   DESCRIPCION:
   AUTOR:
   FECHA:
  ***************************************************************************/

  DECLARE

    v_nro_requerimiento    	integer;
    v_res					varchar;
    v_parametros           	record;
    v_reg_tipo_desc			record;
    v_id_requerimiento     	integer;
    v_resp		            varchar;
    v_nombre_funcion        text;
    v_mensaje_error         text;
    v_id_venta				integer;
    v_num_tramite			varchar;
    v_id_proceso_wf			integer;
    v_id_estado_wf			integer;
    v_codigo_estado			varchar;
    v_id_gestion			integer;
    v_codigo_proceso		varchar;
    v_id_tipo_estado		integer;
    v_id_funcionario		integer;
    v_id_usuario_reg		integer;
    v_id_depto				integer;

    v_id_estado_wf_ant		integer;
    v_acceso_directo		varchar;
    v_clase					varchar;
    v_parametros_ad			varchar;
    v_tipo_noti				varchar;
    v_titulo				varchar;
    v_id_estado_actual		integer;
    v_codigo_estado_siguiente varchar;
    v_obs					text;
    v_id_cliente			integer;
    v_venta					record;
    v_suma_fp				numeric;
    v_suma_det				numeric;
    v_registros				record;
    v_id_sucursal			integer;
    v_cantidad_fp			integer;
    v_acumulado_fp			numeric;
    v_monto_fp				numeric;
    v_a_cuenta				numeric;
    v_fecha_estimada_entrega date;
    vef_estados_validar_fp	varchar;
    v_id_punto_venta			integer;
    v_porcentaje_descuento	integer;
    v_id_vendedor_medico	varchar;
    v_comision				numeric;
    v_id_funcionario_inicio	integer;
    v_codigo_tabla			varchar;
    v_num_ven				varchar;
    v_id_periodo			integer;
    v_tipo_factura			varchar;
    v_fecha					date;
    v_excento				numeric;
    v_id_dosificacion		integer;
    v_nro_factura			integer;
    v_id_actividad_economica	integer[];
    v_dosificacion			record;
    v_tipo_base				varchar;
    v_cantidad				integer;
    v_tipo_usuario			varchar;
    v_id_moneda_venta		integer;
    v_id_moneda_suc			integer;
    v_total_venta_ms		numeric;
    v_fecha_venta 			date;
    v_nombre_ae				varchar;
    v_id_activida_economica		integer;
    v_transporte_fob		numeric;
    v_seguros_fob			numeric;
    v_otros_fob				numeric;
    v_transporte_cif		numeric;
    v_seguros_cif			numeric;
    v_otros_cif				numeric;
    v_tipo_cambio_venta		numeric;
    v_es_fin				varchar;
    v_valor_bruto			numeric;
    v_descripcion_bulto		varchar;
    v_nombre_factura		varchar;
    v_id_cliente_destino    integer;

    v_tabla					varchar;
    v_ventas				varchar;


    v_hora_estimada_entrega	time;
    v_tiene_formula			varchar;
    v_forma_pedido			varchar;
    v_estado_finalizado		integer;
    v_nombre_producto		varchar;
    v_id_producto			varchar;
    v_id_formula			varchar;
    v_codigo_tarjeta		varchar;

    v_precio				varchar;
	v_requiere_excento		varchar;
    v_excento_req			varchar;
    v_codigo_fp				varchar;
    v_id_apertura_cierre_caja	integer;
    v_id_deposito			integer;
    v_monto_venta					numeric;
    v_suma_det_total		numeric;

    /*Variables Recibos Manuales*/
    v_anulado				varchar;
    v_moneda_base			integer;
    v_id_moneda_paquetes	varchar;
    v_desc_moneda			varchar;
    v_año_actual			varchar;
    v_id_dosificacion_ro	integer;
    v_fecha_limite_emision	varchar;
    v_id_medio_pago			integer;
    v_boleto_asociado		varchar;
    v_boletos_asociados		varchar;
    v_existencia			numeric;
    v_comision_paquete		varchar;
    v_comision_req			varchar;

    v_estado_periodo		varchar;
    v_fecha_ini				varchar;
    v_fecha_fin 			varchar;
    v_respaldo				record;
  BEGIN

    v_nombre_funcion = 'vef.ft_venta_ime';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
     #TRANSACCION:  'VF_VEN_INS'
     #DESCRIPCION:	Insercion de registros
     #AUTOR:		admin
     #FECHA:		01-06-2015 05:58:00
    ***********************************/

    if(p_transaccion='VF_VEN_INS')then

      begin
        /*Comentamos para la validacion de tarjetas dscomentar a futuro*/
      	--if (v_parametros.id_forma_pago is not null and v_parametros.id_forma_pago != 0) then

        --Comentamos para que ya no recupere de las formas de pago
        /*select fp.codigo into v_codigo_tarjeta
                from obingresos.tforma_pago fp
                where fp.id_forma_pago = v_parametros.id_forma_pago;*/
        /*v_codigo_tarjeta = (case when v_codigo_tarjeta like 'CC%' or v_codigo_tarjeta like 'SF%' then
                                        substring(v_codigo_tarjeta from 3 for 2)
                                else
                                      NULL
                              end);*/
        if (v_parametros.id_medio_pago is not null and v_parametros.id_medio_pago != 0) then


        select mp.mop_code, fp.fop_code into v_codigo_tarjeta, v_codigo_fp
        from obingresos.tmedio_pago_pw mp
        inner join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
        where mp.id_medio_pago_pw = v_parametros.id_medio_pago;


        /*select ip.codigo_medio_pago, ip.codigo_forma_pago into v_codigo_tarjeta, v_codigo_fp
        from obingresos.tinstancia_pago ip
        where ip.id_instancia_pago = v_parametros.id_medio_pago;*/

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

      /*Aqui verificamos el tipo de recibo Recibo o Recibo Manual*/

       if (pxp.f_existe_parametro(p_tabla,'tipo_factura')) then
          v_tipo_factura = v_parametros.tipo_factura;
        else
          v_tipo_factura = 'recibo';
        end if;

        if(v_tipo_factura = '') then
          v_tipo_factura = 'recibo';
        end if;

        /*Aumentando condicion para verificar el estado en Recibo manual*/
        if(v_tipo_factura = 'recibo_manual') then
          if (pxp.f_existe_parametro(p_tabla,'anulado')) then
               v_anulado = v_parametros.anulado;
          end if;
        else
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
        ELSIF(v_tipo_base = 'recibo_manual') then
          v_fecha = v_parametros.fecha;
          v_nro_factura = v_parametros.nro_factura;
          v_excento = 0;

          select d.* into v_dosificacion
          from vef.tdosificacion_ro d
          where d.estado_reg = 'activo' and d.fecha_inicio_emi <= v_fecha and
                d.fecha_limite >= v_fecha and d.tipo = 'Recibo' and d.tipo_generacion = 'manual' and
                d.id_sucursal = v_parametros.id_sucursal;

          if (v_dosificacion is null) then
                  raise exception 'No existe una dosificacion activa para emitir el Recibo';
          end if;

          v_id_dosificacion = v_dosificacion.id_dosificacion_ro;

          --validaciones de factura manual
          --validar que no exista el mismo nro para la dosificacion
          if (exists(	select 1
                       from vef.tventa ven
                       where ven.estado_reg = 'activo' and ven.nro_factura = v_parametros.nro_factura::integer and ven.id_dosificacion_ro = v_dosificacion.id_dosificacion_ro)) then
            raise exception 'Ya existe el mismo número de Recibo en otra venta. Por favor revise los datos';
          end if;

          --validar que el nro de factura no supere el maximo nro de factura de la dosificaiocn
          if (exists(	select 1
                       from vef.tdosificacion_ro dos
                       where v_parametros.nro_factura::integer < dos.inicial and dos.id_dosificacion_ro = v_dosificacion.id_dosificacion_ro)) then
            raise exception 'El número de Recibo es menor al número Inicial permitido para esta dosificacion';
          end if;

          --validar que el nro de factura no supere el maximo nro de factura de la dosificaiocn
          if (exists(	select 1
                       from vef.tdosificacion_ro dos
                       where v_parametros.nro_factura::integer > dos.final and dos.id_dosificacion_ro = v_dosificacion.id_dosificacion_ro)) then
            -- raise exception 'El numero de Recibo supera el maximo permitido para esta dosificacion';
          end if;

          --validar que la fecha de factura no sea superior a la fecha limite de emision
          if (exists(	select 1
                       from vef.tdosificacion_ro dos
                       where dos.fecha_limite < v_parametros.fecha and dos.id_dosificacion_ro = v_dosificacion.id_dosificacion_ro)) then
            raise exception 'La fecha de la Recibo supera la fecha limite de emision de la dosificacion';
          end if;

        ELSE
          IF   v_tipo_factura in ('computarizadaexpo','computarizadaexpomin','computarizadamin')  THEN
            -- la fecha es abierta
            v_fecha = v_parametros.fecha;

          ELSE
            v_fecha = now()::date;
            v_excento = 0;
          END IF;

        end if;
        if (pxp.f_existe_parametro(p_tabla,'id_punto_venta')) then
          v_id_punto_venta = v_parametros.id_punto_venta;

          if (exists(  select 1
                       from vef.tapertura_cierre_caja acc
                       where acc.fecha_apertura_cierre = v_fecha and
                             acc.estado_reg = 'activo' and acc.estado = 'cerrado' and
                             acc.id_punto_venta = v_parametros.id_punto_venta and acc.id_usuario_cajero = p_id_usuario)) then
            raise exception 'La caja ya fue cerrada, el cajero necesita tener la caja abierta para poder registrar la venta';
          end if;


          if (not exists(  select 1
                           from vef.tapertura_cierre_caja acc
                           where acc.fecha_apertura_cierre = v_fecha and
                                 acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                                 acc.id_punto_venta = v_parametros.id_punto_venta and acc.id_usuario_cajero = p_id_usuario)) then
            raise exception 'Antes de registrar una venta el cajero debe realizar una apertura de caja';
          end if;

        else
          v_id_punto_venta = NULL;

          if (exists(	select 1
                       from vef.tapertura_cierre_caja acc
                       where acc.fecha_apertura_cierre = v_fecha and
                             acc.estado_reg = 'activo' and acc.estado = 'cerrado' and
                             acc.id_sucursal = v_parametros.id_sucursal and acc.id_usuario_cajero = p_id_usuario)) then
            raise exception 'La caja ya fue cerrada, el cajero necesita tener la caja abierta para poder registrar la venta';
          end if;


          if (not exists(	select 1
                           from vef.tapertura_cierre_caja acc
                           where acc.fecha_apertura_cierre = v_fecha and
                                 acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                                 acc.id_sucursal = v_parametros.id_sucursal and acc.id_usuario_cajero = p_id_usuario)) then
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


        if (pxp.f_existe_parametro(p_tabla,'id_cliente')) THEN
        	if(pxp.f_is_positive_integer(v_parametros.id_cliente))then
              v_id_cliente = v_parametros.id_cliente::integer;

              update vef.tcliente
              set nit = v_parametros.nit,
              correo = v_parametros.correo_electronico
              where id_cliente = v_id_cliente;

              select c.nombre_factura into v_nombre_factura
              from vef.tcliente c
              where c.id_cliente = v_id_cliente;
            end if;
        -- bvp
        elsif(v_tipo_base = 'recibo' or v_tipo_base = 'recibo_manual')then

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
            upper(v_parametros.nombre_factura)
          ) returning id_cliente into v_id_cliente;

          v_nombre_factura = UPPER(v_parametros.nombre_factura);
        -- bvp
        else
          INSERT INTO
            vef.tcliente
            (
              id_usuario_reg,
              fecha_reg,
              estado_reg,
              nombre_factura,
              nit,
              correo
            )
          VALUES (
            p_id_usuario,
            now(),
            'activo',
            upper(v_parametros.id_cliente),
            v_parametros.nit,
            v_parametros.correo_electronico
          ) returning id_cliente into v_id_cliente;

          v_nombre_factura = UPPER (v_parametros.id_cliente);

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


   /*if (v_tipo_factura = 'recibo') then
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
	end if;*/
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
          id_dosificacion_ro,
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
          id_moneda_venta_recibo,
          id_auxiliar_anticipo,
          anulado,
          correo_electronico




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
          v_parametros.id_moneda_venta_recibo,
	      v_parametros.id_auxiliar_anticipo,
          v_anulado,
          v_parametros.correo_electronico

        ) returning id_venta into v_id_venta;


         /*Aqui aumentaremos la condcion para registrar los depositos*/
        if (pxp.f_existe_parametro(p_tabla,'nro_deposito')) then
            IF (v_parametros.nro_deposito != '') then
                 select acc.id_apertura_cierre_caja into v_id_apertura_cierre_caja
                 from vef.tapertura_cierre_caja acc
                 where acc.fecha_apertura_cierre = v_fecha and
                       acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                       acc.id_punto_venta = v_parametros.id_punto_venta and acc.id_usuario_cajero = p_id_usuario;
            	 insert into obingresos.tdeposito(
                              id_usuario_reg,
                              fecha_reg,
                              estado_reg,
                              nro_deposito,
                              monto_deposito,
                              fecha,
                              tipo,
                              monto_total,
                              estado,
                              id_moneda_deposito,
                              id_apertura_cierre_caja
                            ) values(
                              p_id_usuario,
                              now(),
                              'activo',
                              v_parametros.nro_deposito,
                              v_parametros.monto_deposito,
                              v_parametros.fecha_deposito::date,
                              'cuenta_corriente',
                              v_parametros.monto_deposito,
                              'borrador',
                              v_parametros.id_moneda,
                              v_id_apertura_cierre_caja
                            )returning id_deposito into v_id_deposito;

                update vef.tventa set
                id_deposito = v_id_deposito
                where id_venta = v_id_venta;


            end if;
            /********************************************************/

        end if;
        /************************************************************/

		/*Aumentamos la instancia de pago por el id_forma_pago*/
        --if (v_parametros.id_forma_pago != 0 ) then

        if (v_parametros.id_medio_pago = 0 ) then

        	select
                  mppw.id_medio_pago_pw
                  into
                  v_id_medio_pago
          from obingresos.tmedio_pago_pw mppw
          inner join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mppw.forma_pago_id
          where   mppw.mop_code = 'CASH';

        else
        		v_id_medio_pago = v_parametros.id_medio_pago;
        end if;


		if (v_parametros.id_medio_pago is not null) then

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
            nro_mco
          )
          values(
            v_parametros._nombre_usuario_ai,
            now(),
            p_id_usuario,
            v_parametros._id_usuario_ai,
            'activo',
            --v_parametros.id_forma_pago,
            v_id_medio_pago,--v_parametros.id_medio_pago,
            v_parametros.id_moneda,
            v_id_venta,
            v_parametros.monto_forma_pago,
            0,
            0,
            0,
            v_parametros.numero_tarjeta,
            replace(upper(v_parametros.codigo_tarjeta),' ',''),
            v_parametros.id_auxiliar,
            v_parametros.tipo_tarjeta,
            v_parametros.mco
          );
        end if;

--        if (v_parametros.id_forma_pago_2 is not null and v_parametros.id_forma_pago_2 != 0 ) then
		if (v_parametros.id_medio_pago_2 is not null ) then
         /*******************************Control para la tarjeta 2******************************/

         select mp.mop_code, fp.fop_code into v_codigo_tarjeta, v_codigo_fp
        from obingresos.tmedio_pago_pw mp
        inner join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
        where mp.id_medio_pago_pw = v_parametros.id_medio_pago_2;


        /*select ip.codigo_medio_pago, ip.codigo_forma_pago into v_codigo_tarjeta, v_codigo_fp
        from obingresos.tinstancia_pago ip
        where ip.id_instancia_pago = v_parametros.id_medio_pago;*/

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

        /*select fp.codigo into v_codigo_tarjeta
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
		*/

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
            nro_mco
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
            v_parametros.tipo_tarjeta,
            v_parametros.mco_2
          );
        end if;


        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Ventas almacenado(a) con exito (id_venta'||v_id_venta||')');
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_id_venta::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;

    /*********************************
     #TRANSACCION:  'VF_VEN_MOD'
     #DESCRIPCION:	Modificacion de registros
     #AUTOR:		admin
     #FECHA:		01-06-2015 05:58:00
    ***********************************/

    elsif(p_transaccion='VF_VEN_MOD')then

      begin
        select
          v.*
        into
          v_registros
        from vef.tventa v
        where v.id_venta = v_parametros.id_venta;

        v_tiene_formula = 'no';
        if (pxp.f_existe_parametro(p_tabla,'id_punto_venta')) then
          v_id_punto_venta = v_parametros.id_punto_venta;
        else
          v_id_punto_venta = NULL;
        end if;

        SELECT * into v_venta FROM vef.tventa v where v.id_venta = v_parametros.id_venta;

        if (v_id_punto_venta is not null) then
          select id_sucursal into v_id_sucursal
          from vef.tpunto_venta
          where id_punto_venta = v_id_punto_venta;
        else
          v_id_sucursal = v_parametros.id_sucursal;
        end if;

        if (pxp.f_existe_parametro(p_tabla,'tipo_factura')) then
          v_tipo_factura = v_parametros.tipo_factura;
        else
          v_tipo_factura = 'recibo';
        end if;

        /*SELECT tv.tipo_base into v_tipo_base
        from vef.ttipo_venta tv
        where tv.codigo = v_tipo_factura and tv.estado_reg = 'activo';

        if (v_tipo_base is null) then
          raise exception 'No existe un tipo de venta con el codigo: % consulte con el administrador del sistema',v_tipo_factura;
        end if;*/

        v_excento = 0;

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


        IF(v_tipo_base = 'manual') then
          v_fecha = v_parametros.fecha;
          v_nro_factura = v_parametros.nro_factura;
          v_excento = v_parametros.excento;
          v_id_dosificacion = v_parametros.id_dosificacion;
        elsif (v_tipo_base = 'computarizada')  then

          IF   v_tipo_factura in ('computarizadaexpo','computarizadaexpomin','computarizadamin')  THEN
            v_fecha = v_parametros.fecha;
            v_nro_factura = v_venta.nro_factura;
            v_id_dosificacion = v_venta.id_dosificacion;

          ELSE
            v_excento = v_parametros.excento;
          END IF;

        end if;

        /* Lanzar exception al tratar de modificar la fecha de una venta computarizada*/
        if (v_fecha is not null and v_fecha != v_registros.fecha and v_tipo_base = 'computarizada') then
          raise exception 'No es posible modificar la fecha de una venta computarizada';
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
                      upper(v_parametros.id_cliente)
                    ) returning id_cliente into v_id_cliente_destino;


                end if;
           end if;



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
          tipo_cambio_venta = COALESCE(v_tipo_cambio_venta,1),
          valor_bruto = COALESCE(v_valor_bruto,0),
          descripcion_bulto = COALESCE(v_descripcion_bulto,''),
          nit = v_parametros.nit,
              nombre_factura = v_nombre_factura ,
              id_cliente_destino = v_id_cliente_destino
        where id_venta=v_parametros.id_venta;


		/*Comentamos y aumentamos instancia de pago*/
       -- if (v_parametros.id_forma_pago != 0 ) then
	      if (v_parametros.id_medio_pago != 0 ) then

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
            /*Aumentando instancia y moneda*/
            id_medio_pago,
            id_moneda,
            nro_mco
            /*******************************/
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
            /*Aumentando instancia de pago y id_moneda*/
            v_parametros.id_medio_pago,
            v_parametros.id_moneda,
            v_parametros.mco
            /*****************************************/
          );

           --raise exception 'llega aqui para la insercion %',v_parametros.id_forma_pago_2;
          	/*Comentando y aumentado la instancia de pago*/
            -- if (v_parametros.id_forma_pago_2 is not null and v_parametros.id_forma_pago_2 != 0 ) then
           	if (v_parametros.id_medio_pago_2 is not null and v_parametros.id_medio_pago_2 != 0 ) then

            select mp.mop_code, fp.fop_code into v_codigo_tarjeta, v_codigo_fp
                  from obingresos.tmedio_pago_pw mp
                  inner join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
                  where mp.id_medio_pago_pw = v_parametros.id_medio_pago_2;


                  /*select ip.codigo_medio_pago, ip.codigo_forma_pago into v_codigo_tarjeta, v_codigo_fp
                  from obingresos.tinstancia_pago ip
                  where ip.id_instancia_pago = v_parametros.id_medio_pago;*/

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


                     if (left (v_parametros.mco_2,3)  <> '930' and v_parametros.mco_2 <> '')then
                          raise exception 'El numero del MCO tiene que empezar con 930';
                          end if;

                      if (char_length(v_parametros.mco_2::varchar) <> 15 and v_parametros.mco_2 <> '' ) then
                          raise exception 'El numero del MCO debe tener 15 digitos obligatorios, 930000000012345';
                      end if;


           /*******************************Control para la tarjeta 2******************************/

         /* select fp.codigo into v_codigo_tarjeta
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
			*/

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
            /*Aumentando la instancia de pago y el id_moneda*/
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
            /*Aumentando la instancia de pago y el id_moneda*/
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
     #TRANSACCION:  'VF_VEN_ELI'
     #DESCRIPCION:	Eliminacion de registros
     #AUTOR:		admin
     #FECHA:		01-06-2015 05:58:00
    ***********************************/

    elsif(p_transaccion='VF_VEN_ELI')then

      begin


        select
          v.*
        into
          v_registros  from vef.tventa v
        where v.id_venta = v_parametros.id_venta;



        IF  v_registros.tipo_factura not in  ('computarizadaexpo','computarizadaexpomin','computarizadamin') THEN



          --Sentencia de la eliminacion
         /* delete from vef.tventa_forma_pago
          where id_venta=v_parametros.id_venta;

          delete from vef.tventa_detalle
          where id_venta=v_parametros.id_venta;*/

          update vef.tventa_forma_pago
          set estado_reg = 'inactivo'
          where id_venta=v_parametros.id_venta;

          update vef.tventa_detalle
          set estado_reg = 'inactivo'
          where id_venta=v_parametros.id_venta;

          update vef.tventa
          set estado_reg = 'inactivo'
          where id_venta=v_parametros.id_venta;
        ELSE
          v_res = vef.f_anula_venta(p_administrador,p_id_usuario,p_tabla, v_registros.id_proceso_wf,v_registros.id_estado_wf, v_parametros.id_venta);

        END IF;

        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Ventas eliminado(a)');
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_parametros.id_venta::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;

    /*********************************
     #TRANSACCION:  'VF_VEALLFORPA_ELI'
     #DESCRIPCION:	Eliminacion de formas de pago relacionadas a una venta
     #AUTOR:		admin
     #FECHA:		01-06-2015 05:58:00
    ***********************************/

    elsif(p_transaccion='VF_VEALLFORPA_ELI')then

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
     #TRANSACCION:  'VF_VEALLDET_ELI'
     #DESCRIPCION:	Eliminacion de los detalles relacionados a una venta
     #AUTOR:		admin
     #FECHA:		01-06-2015 05:58:00
    ***********************************/

    elsif(p_transaccion='VF_VEALLDET_ELI')then

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
     #TRANSACCION:  'VF_VENVALI_MOD'
     #DESCRIPCION:	Validacion de montos en una venta
     #AUTOR:		admin
     #FECHA:		01-06-2015 05:58:00
    ***********************************/

    elsif(p_transaccion='VF_VENVALI_MOD')then

      begin
        vef_estados_validar_fp = pxp.f_get_variable_global('vef_estados_validar_fp');
        --obtener datos de la venta y la moneda base

        v_moneda_base = param.f_get_moneda_base();

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
        IF  v_venta.tipo_factura in ('computarizadaexpomin') THEN
          v_id_moneda_venta = v_venta.id_moneda;
        ELSE
          v_id_moneda_venta = v_venta.id_moneda_base;
        END IF;

        /*Aqui recuperamos el monto de la venta convertido a la moneda local*/
        /*Aumentamos la condicion para la moneda base*/

          if (v_venta.id_moneda_venta_recibo = 2 and v_moneda_base != 2) then
              v_monto_venta = param.f_convertir_moneda(v_venta.id_moneda_venta_recibo,v_id_moneda_venta,v_venta.total_venta,v_venta.fecha::date,'CUS',2, NULL,'si');
          else
              v_monto_venta = v_venta.total_venta;
          end if;

        /********************************************************************/


        v_id_moneda_suc = v_venta.id_moneda_base;

		select count(distinct vd.id_venta_detalle) into v_cantidad
        from vef.tventa_detalle vd
        where vd.id_venta = v_parametros.id_venta;

        /*Aqui poniendo condicion del detalle para recibos manuales*/
        if (v_parametros.tipo_factura = 'recibo_manual') THEN
        	IF(v_cantidad=0 and v_parametros.anulado = 'VALIDA')THEN
        		raise exception 'Debe tener al menos un concepto registrado para la venta';
        	END IF;

        else

        	IF(v_cantidad=0)THEN
        		raise exception 'Debe tener al menos un concepto registrado para la venta';
        	END IF;

        end if;

        /***********************************************************/

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

           /*******************************Obtenemos la moneda para realizar la converision si es en dolar (IRVA)****************************************/
                      /*for v_registros in (select vfp.id_venta_forma_pago, fp.id_moneda,vfp.monto_transaccion
                                          from vef.tventa_forma_pago vfp
                                            inner join vef.tforma_pago fp on fp.id_forma_pago = vfp.id_forma_pago
                                          where vfp.id_venta = v_parametros.id_venta)loop */


          	 for v_registros in (select vfp.id_venta_forma_pago, vfp.id_moneda,vfp.monto_transaccion
                              from vef.tventa_forma_pago vfp
                              where vfp.id_venta = v_parametros.id_venta)loop
            --si la moneda de la forma de pago es distinta a al moneda base de la sucursal convertimos a moneda base

            /*Aqui aumentamos para hacer el tipo de cambio de las formas de pago*/
              if (v_registros.id_moneda = 2 and v_moneda_base != 2) then
               /*Convertimos a Bs*/
               v_monto_fp = param.f_convertir_moneda(v_registros.id_moneda,v_id_moneda_venta,v_registros.monto_transaccion,v_venta.fecha::date,'CUS',2, NULL,'si');
              else
               /*si es Bs mantenemos a Bs*/
               v_monto_fp = v_registros.monto_transaccion;
              end if;

            /**********************************************/

            /*Comentamos esta parte para probar*/
            /* if (v_registros.id_moneda != v_id_moneda_venta) then
              IF  v_venta.tipo_cambio_venta is not null and v_venta.tipo_cambio_venta != 0 THEN
              	v_monto_fp = param.f_convertir_moneda(v_registros.id_moneda,v_id_moneda_venta,v_registros.monto_transaccion,v_venta.fecha::date,'CUS',2, v_venta.tipo_cambio_venta,'si');
              ELSE
                v_monto_fp = param.f_convertir_moneda(v_registros.id_moneda,v_id_moneda_venta,v_registros.monto_transaccion,v_venta.fecha::date,'O',2,NULL,'si');
              END IF;
            else
              v_monto_fp = v_registros.monto_transaccion;
            end if;*/


            --si el monto de una d elas formas de pago es mayor q el total de la venta y la cantidad de formas de pago es mayor a 1 lanzo excepcion
            if (v_monto_fp >= v_monto_venta/*v_venta.total_venta*/ and v_cantidad_fp > 1) then
              raise exception 'Se ha definido mas de una forma de pago, pero existe una que supera el valor de la venta(solo se requiere una forma de pago)';
            end if;

            update vef.tventa_forma_pago set
              monto = v_monto_fp,
              cambio = (case when (v_monto_fp + v_acumulado_fp - v_monto_venta/*v_venta.total_venta*/) > 0 then
                (v_monto_fp + v_acumulado_fp - v_monto_venta/*v_venta.total_venta*/)
                        else
                          0
                        end),
              monto_mb_efectivo = (case when (v_monto_fp + v_acumulado_fp - v_monto_venta/*v_venta.total_venta*/) > 0 then
                v_monto_fp - (v_monto_fp + v_acumulado_fp - v_monto_venta/*v_venta.total_venta*/)
                                   else
                                     v_monto_fp
                                   end)
            where id_venta_forma_pago = v_registros.id_venta_forma_pago;
            v_acumulado_fp = v_acumulado_fp + v_monto_fp;
          end loop;

          select sum(round(monto_mb_efectivo,2)) into v_suma_fp
          from vef.tventa_forma_pago
          where id_venta =   v_parametros.id_venta;

          /*Aqui pondremos la condicion para hacer la conversion*/
          select sum(round(cantidad*precio,2)) into v_suma_det
          from vef.tventa_detalle
          where id_venta =   v_parametros.id_venta;

          if (v_venta.id_moneda_venta_recibo = 2 and v_moneda_base != 2) then
              v_suma_det_total = param.f_convertir_moneda(v_venta.id_moneda_venta_recibo,v_id_moneda_venta,v_suma_det,v_venta.fecha::date,'CUS',2, NULL,'si');
          else
              v_suma_det_total = v_suma_det;
          end if;
          /******************************************************/


          IF v_parametros.tipo_factura != 'computarizadaexpo' THEN
            v_suma_det = COALESCE(v_suma_det,0) + COALESCE(v_venta.transporte_fob ,0)  + COALESCE(v_venta.seguros_fob ,0)+ COALESCE(v_venta.otros_fob ,0) + COALESCE(v_venta.transporte_cif ,0) +  COALESCE(v_venta.seguros_cif ,0) + COALESCE(v_venta.otros_cif ,0);
          END IF;

          if (v_suma_fp < v_monto_venta/*v_venta.total_venta*/) then
            raise exception 'El importe recibido es menor al valor de la venta, falta %', v_monto_venta - v_suma_fp;
          end if;

          if (v_suma_fp > v_monto_venta/*v_venta.total_venta*/) then
            raise exception 'El total de la venta no coincide con la división por forma de pago%',v_suma_fp;
          end if;

          if (/*v_suma_det*/v_suma_det_total != v_monto_venta/*v_venta.total_venta*/) then
            raise exception 'El total de la venta no coincide con la suma de los detalles (% = %) en id: %',/*v_suma_det*/v_suma_det_total ,v_monto_venta/*v_venta.total_venta*/, v_parametros.id_venta;
          end if;
        end if;

        select sum(cambio) into v_suma_fp
        from vef.tventa_forma_pago
        where id_venta =   v_parametros.id_venta;



        --calcula el total de la venta en moenda de la sucursal

        IF  v_venta.tipo_cambio_venta is not null THEN
          v_total_venta_ms = param.f_convertir_moneda(v_id_moneda_venta,v_id_moneda_suc,v_venta.total_venta,v_venta.fecha,'CUS',2, v_venta.tipo_cambio_venta,'si');
        ELSE
          v_total_venta_ms = param.f_convertir_moneda(v_id_moneda_venta,v_id_moneda_suc,v_registros.monto_transaccion,v_venta.fecha::date,'O',2,NULL,'si');
        END IF;

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
 	#TRANSACCION:  'VEF_ANTEVE_IME'
 	#DESCRIPCION:	Transaccion utilizada  pasar a  estados anterior en la venta
                    segun la operacion definida
 	#AUTOR:		JRR
 	#FECHA:		17-10-2014 12:12:51
	***********************************/

    elseif(p_transaccion='VEF_ANTEVE_IME')then
      begin

        --------------------------------------------------
        --Retrocede al estado inmediatamente anterior
        -------------------------------------------------
       --recuperaq estado anterior segun Log del WF
        SELECT
          ps_id_tipo_estado,
          ps_id_funcionario,
          ps_id_usuario_reg,
          ps_id_depto,
          ps_codigo_estado,
          ps_id_estado_wf_ant
        into
          v_id_tipo_estado,
          v_id_funcionario,
          v_id_usuario_reg,
          v_id_depto,
          v_codigo_estado,
          v_id_estado_wf_ant
        FROM wf.f_obtener_estado_ant_log_wf(v_parametros.id_estado_wf);
        --
        select
          ew.id_proceso_wf
        into
          v_id_proceso_wf
        from wf.testado_wf ew
        where ew.id_estado_wf= v_id_estado_wf_ant;

        --configurar acceso directo para la alarma
        v_acceso_directo = '';
        v_clase = '';
        v_parametros_ad = '';
        v_tipo_noti = 'notificacion';
        v_titulo  = 'Notificacion';

        -- registra nuevo estado
        v_id_estado_actual = wf.f_registra_estado_wf(
            v_id_tipo_estado,
            v_id_funcionario,
            v_parametros.id_estado_wf,
            v_id_proceso_wf,
            p_id_usuario,
            v_parametros._id_usuario_ai,
            v_parametros._nombre_usuario_ai,
            v_id_depto,
            '[RETROCESO] '|| v_parametros.obs,
            v_acceso_directo,
            v_clase,
            v_parametros_ad,
            v_tipo_noti,
            v_titulo);

        IF  vef.f_fun_regreso_venta_wf(p_id_usuario,
                                       v_parametros._id_usuario_ai,
                                       v_parametros._nombre_usuario_ai,
                                       v_id_estado_actual,
                                       v_id_proceso_wf,
                                       v_codigo_estado) THEN

        END IF;

        -- si hay mas de un estado disponible  preguntamos al usuario
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado)');
        v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');

        --Devuelve la respuesta
        return v_resp;
      end;
    /*********************************
 	#TRANSACCION:  'VEF_SIGEVE_IME'
 	#DESCRIPCION:	funcion que controla el cambio al Siguiente estado de las ventas, integrado  con el WF
 	#AUTOR:		JRR
 	#FECHA:		17-10-2014 12:12:51
	***********************************/

    elseif(p_transaccion='VEF_SIGEVE_IME')then
      begin

        /*   PARAMETROS

       $this->setParametro('id_proceso_wf_act','id_proceso_wf_act','int4');
       $this->setParametro('id_tipo_estado','id_tipo_estado','int4');
       $this->setParametro('id_funcionario_wf','id_funcionario_wf','int4');
       $this->setParametro('id_depto_wf','id_depto_wf','int4');
       $this->setParametro('obs','obs','text');
       $this->setParametro('json_procesos','json_procesos','text');
       */

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

        select v.*,s.id_entidad,tv.tipo_base into v_venta
        from vef.tventa v
          inner join vef.tsucursal s on s.id_sucursal = v.id_sucursal
          inner join vef.tcliente c on c.id_cliente = v.id_cliente
          inner join vef.ttipo_venta tv on tv.codigo = v.tipo_factura and tv.estado_reg = 'activo'
        where v.id_proceso_wf = v_parametros.id_proceso_wf_act;

        select te.codigo into v_codigo_estado
        from wf.ttipo_estado te
        where te.id_tipo_estado=v_parametros.id_tipo_estado;

        v_tabla = pxp.f_crear_parametro(ARRAY[	'_nombre_usuario_ai',
        '_id_usuario_ai',
        'id_venta',
        'tipo_factura',
        'codigo_estado',
        'anulado'],
                                        ARRAY[	coalesce(v_parametros._nombre_usuario_ai,''),
                                        coalesce(v_parametros._id_usuario_ai::varchar,''),
                                        v_venta.id_venta::varchar,
                                        v_venta.tipo_factura,
                                        v_codigo_estado,
	                                    v_venta.anulado ],
                                        ARRAY[	'varchar',
                                        'integer',
                                        'integer',
                                        'varchar',
                                        'varchar',
                                        'varchar']
        );

		v_resp = vef.ft_venta_ime(p_administrador,p_id_usuario,v_tabla,'VF_VENVALI_MOD');

        -- obtener datos tipo estado

        select
          te.codigo,te.fin
        into
          v_codigo_estado_siguiente,v_es_fin
        from wf.ttipo_estado te
        where te.id_tipo_estado = v_parametros.id_tipo_estado;

        IF  pxp.f_existe_parametro(p_tabla,'id_depto_wf') THEN

          v_id_depto = v_parametros.id_depto_wf;

        END IF;

        IF  pxp.f_existe_parametro(p_tabla,'obs') THEN
            v_obs=v_parametros.obs;
        ELSE
          v_obs='---';
        END IF;

        --configurar acceso directo para la alarma
        v_acceso_directo = '';
        v_clase = '';
        v_parametros_ad = '';
        v_tipo_noti = 'notificacion';
        v_titulo  = 'Visto Bueno';

        -- hay que recuperar el supervidor que seria el estado inmediato,...
        v_id_estado_actual =  wf.f_registra_estado_wf(v_parametros.id_tipo_estado,
                                                      v_parametros.id_funcionario_wf,
                                                      v_parametros.id_estado_wf_act,
                                                      v_parametros.id_proceso_wf_act,
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


        IF  vef.f_fun_inicio_venta_wf(p_id_usuario,
                                      v_parametros._id_usuario_ai,
                                      v_parametros._nombre_usuario_ai,
                                      v_id_estado_actual,
                                      v_parametros.id_proceso_wf_act,
                                      v_codigo_estado_siguiente) THEN

        END IF;

        if (v_venta.tipo_base = 'computarizada' and v_es_fin = 'si') then

          IF v_venta.tipo_factura not in ('computarizadaexpo','computarizadaexpomin','computarizadamin') THEN
            v_fecha_venta = now()::date;
            if (EXISTS(	select 1
                         from vef.tventa v
                         where v.fecha > v_fecha_venta and v.tipo_factura = 'computarizada' and
                               v.estado_reg = 'activo' and v.estado = 'finalizado'))THEN
              raise exception 'Existen facturas emitidas con fechas posterior a la actual. Por favor revise la fecha y hora del sistema';
            end if;
          ELSE
            v_fecha_venta = v_venta.fecha;
          --no validamos la fecha en las facturas de exportacion
          --por que  valida al insertar la factura, donde se genera el nro de la factura
          END IF;

          select array_agg(distinct cig.id_actividad_economica) into v_id_actividad_economica
          from vef.tventa_detalle vd
            inner join vef.tsucursal_producto sp on vd.id_sucursal_producto = sp.id_sucursal_producto
            inner join param.tconcepto_ingas cig on  cig.id_concepto_ingas = sp.id_concepto_ingas
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

        if (pxp.f_get_variable_global('sw_ventas_migrar_informix') = 'si' and pxp.f_get_variable_global('tipo_factura_migrar_ventas') = 'computarizada') then
	      v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_venta.id_venta::varchar);
        end if;


        -- Devuelve la respuesta
        return v_resp;

      end;

    /*********************************
 	#TRANSACCION:  'VEF_SIGRECI_IME'
 	#DESCRIPCION:	funcion que controla el cambio al Siguiente estado de las ventas, integrado  con el WF
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		08-04-2019 10:20:00
	***********************************/

    elseif(p_transaccion='VEF_SIGRECI_IME')then
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

        if (v_venta.anulado = 'ANULADA' ) then
        	v_estado_finalizado = 851;
        else
          /*Obtenemos el id del estado finalizado*/
          v_estado_finalizado = (v_id_tipo_estado+1);
          /****************************************/
        end if;

        /*Obtenemos el id del estado finalizado*/
       -- v_estado_finalizado = (v_id_tipo_estado+1);
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
        'codigo_estado',
        'anulado'],
                                        ARRAY[	coalesce(v_parametros._nombre_usuario_ai,''),
                                        coalesce(v_parametros._id_usuario_ai::varchar,''),
                                        v_venta.id_venta::varchar,
                                        v_venta.tipo_factura,
                                        v_codigo_estado,
                                        v_venta.anulado],
                                        ARRAY[	'varchar',
                                        'integer',
                                        'integer',
                                        'varchar',
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

        if (v_venta.tipo_base = 'recibo' and v_es_fin = 'si') then
          IF v_venta.tipo_factura not in ('computarizadaexpo','computarizadaexpomin','computarizadamin') THEN
            v_fecha_venta = now()::date;
            if (EXISTS(	select 1
                         from vef.tventa v
                         where v.fecha > v_fecha_venta and v.tipo_factura = 'recibo' and
                               v.estado_reg = 'activo' and v.estado = 'finalizado'))THEN
              raise exception 'Existen recibos emitidos con fechas posterior a la actual. Por favor revise la fecha y hora del sistema';
            end if;
          ELSE
            v_fecha_venta = v_venta.fecha;
          END IF;

          /*Aqui ponemos la nueva dosificacion para los recibos*/
             IF v_venta.tipo_factura not in ('computarizadaexpo','computarizadaexpomin','computarizadamin') THEN

                select d.* into v_dosificacion
                from vef.tdosificacion_ro d
                where d.estado_reg = 'activo' and d.fecha_inicio_emi <= v_venta.fecha and
                      d.fecha_limite >= v_venta.fecha and d.tipo = 'Recibo' and d.tipo_generacion = 'computarizada' and
                      d.id_sucursal = v_venta.id_sucursal;
                v_nro_factura = v_dosificacion.nro_siguiente;

                SELECT EXTRACT(YEAR FROM CAST( now()as date))
                into v_año_actual ;

                v_fecha_limite_emision = '31/12/'||v_año_actual;
				--raise exception 'Aqi data %',v_fecha_limite_emision;
                if (v_dosificacion is null) then
                	/*Aqui aumentamos para crear la dosificacion para los RO Computarizados*/
                    --Esta dosificacion se creara anualmente
                    insert into vef.tdosificacion_ro(
                                                      id_sucursal,
                                                      final,
                                                      tipo,
                                                      fecha_dosificacion,
                                                      nro_siguiente,
                                                      fecha_inicio_emi,
                                                      fecha_limite,
                                                      tipo_generacion,
                                                      inicial,
                                                      estado_reg,
                                                      id_usuario_ai,
                                                      fecha_reg,
                                                      usuario_ai,
                                                      id_usuario_reg,
                                                      fecha_mod,
                                                      id_usuario_mod
                                                    ) values(
                                                      v_venta.id_sucursal,
                                                      null,
                                                      'Recibo',
                                                      now()::date,
                                                      1,
                                                      now()::date,
                                                      v_fecha_limite_emision::date,
                                                      'computarizada',
                                                      1,
                                                      'activo',
                                                      v_parametros._id_usuario_ai,
                                                      now(),
                                                      v_parametros._nombre_usuario_ai,
                                                      p_id_usuario,
                                                      null,
                                                      null

                                                    )RETURNING id_dosificacion_ro into v_id_dosificacion_ro ;
                	v_nro_factura = 1;
                else
                	 v_id_dosificacion_ro = v_dosificacion.id_dosificacion_ro;
                    /***********************************************************************/
                  --raise exception 'No existe una dosificacion activa para emitir el Recibo';
                end if;


                --validar que el nro de factura no supere el maximo nro de factura de la dosificaiocn
                if (exists(	select 1
                             from vef.tventa ven
                             where ven.nro_factura =  v_dosificacion.nro_siguiente and ven.id_dosificacion_ro = v_id_dosificacion_ro)) then
                  raise exception 'El numero de recibo ya existe para esta dosificacion. Por favor comuniquese con el administrador del sistema';
                end if;

                --la factura de exportacion no altera la fecha
                update vef.tventa  set
                  id_dosificacion_ro = v_id_dosificacion_ro,
                  nro_factura = v_nro_factura,
                  fecha = v_fecha_venta
                where id_venta = v_venta.id_venta;

                update vef.tdosificacion_ro
                set nro_siguiente = nro_siguiente + 1
                where id_dosificacion_ro = v_id_dosificacion_ro;
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

        if (pxp.f_get_variable_global('sw_ventas_migrar_informix') = 'si' and pxp.f_get_variable_global('tipo_factura_migrar_ventas') = 'computarizada') then
	      v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_venta.id_venta::varchar);
        end if;

        -- Devuelve la respuesta
        return v_resp;






      end;


    /*********************************
 	#TRANSACCION:  'VF_VENANU_MOD'
 	#DESCRIPCION:	Anulacion de Venta
 	#AUTOR:		RAC
 	#FECHA:		19-02-2013 12:12:51
	***********************************/

    elsif(p_transaccion='VF_VENANU_MOD')then

      begin

        --obtener el tipo de usuario, la fecha de venta, etc

        select id_venta,fecha,id_sucursal,id_punto_venta into v_venta
        from vef.tventa v
        where v.id_venta = v_parametros.id_venta;

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
        where v_venta.fecha between per.fecha_ini and per.fecha_fin
         and cp.id_depto = (select depo.id_depto
        from param.tdepto depo
        where depo.codigo = 'CON');
        /*********************************************************************/

        if (v_estado_periodo = 'cerrado') then
        	raise exception 'No se puede Anular la factura debido a que el periodo %, %, se  			encuentra cerrado',v_fecha_ini,v_fecha_fin;
        end if;




        v_tipo_usuario = 'cajero';

        if (v_venta.id_punto_venta is null) then
          select  su.tipo_usuario into v_tipo_usuario
          from vef.tsucursal_usuario su
          where id_sucursal = v_venta.id_sucursal and su.id_usuario = p_id_usuario;
        else
          select  su.tipo_usuario into v_tipo_usuario
          from vef.tsucursal_usuario su
          where su.id_punto_venta = v_venta.id_punto_venta and su.id_usuario = p_id_usuario;
        end if;

        if (p_administrador != 1) then

        		select  count (permiso.id_autorizacion) into v_existencia
              from vef.tpermiso_sucursales permiso
              where permiso.id_funcionario = (select fun.id_funcionario
                                              from segu.tusuario usu
                                              inner join orga.vfuncionario funcio on funcio.id_persona = usu.id_persona
                                              inner join orga.vfuncionario_ultimo_cargo fun on fun.id_funcionario = funcio.id_funcionario
                                              where usu.id_usuario = p_id_usuario);
              if (v_existencia = 0) then
                if (v_tipo_usuario = 'cajero' and v_venta.fecha != now()::date) then
                  raise exception 'El recibo solo puede ser anulado el mismo dia de emision o por un administrador.';
                end if;
          	end if;
        end if;

        --obtenemos datos basicos
        select
          ven.id_estado_wf,
          ven.id_proceso_wf,
          ven.estado,
          ven.id_venta,
          ven.nro_tramite,
          ven.id_deposito
        into
          v_registros
        from vef.tventa ven
        where ven.id_venta = v_parametros.id_venta;


         if (v_registros.id_deposito is not null) then
            delete from obingresos.tdeposito
                where id_deposito = v_registros.id_deposito;
        end if;

        for v_respaldo in  (select *
                          from vef.tventa ven
                          inner join vef.tventa_detalle vendet on vendet.id_venta = ven.id_venta
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          left join vef.tdosificacion dos on dos.id_dosificacion = ven.id_dosificacion
                          where ven.id_venta = v_parametros.id_venta) loop

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
                id_medio_pago,
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
                nro_autorizacion,
                nro_mco
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
                v_respaldo.id_medio_pago,
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
                v_respaldo.nroaut,
                v_respaldo.nro_mco
                );

       END LOOP;


        v_res = vef.f_anula_venta(p_administrador,p_id_usuario,p_tabla, v_registros.id_proceso_wf,v_registros.id_estado_wf, v_parametros.id_venta);


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



        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','venta anulada');
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_parametros.id_venta::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;
    /*********************************
 	#TRANSACCION:  'VF_VENCONTA_MOD'
 	#DESCRIPCION:	Vuelve contabilizable una venta no contabilizable
 	#AUTOR:		JRR
 	#FECHA:		19-02-2013 12:12:51
	***********************************/

    elsif(p_transaccion='VF_VENCONTA_MOD')then

      begin

        --obtener el tipo de usuario, la fecha de venta, etc

        update vef.tventa set contabilizable = 'si'
        where id_venta = v_parametros.id_venta;
        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','venta anulada');
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_parametros.id_venta::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;
    /*********************************
 	#TRANSACCION:  'VF_VENVERELA_MOD'
 	#DESCRIPCION:	Vuelve contabilizable una venta no contabilizable
 	#AUTOR:		JRR
 	#FECHA:		19-02-2013 12:12:51
	***********************************/

    elsif(p_transaccion='VF_VENVERELA_MOD')then

      begin

        --obtener el tipo de usuario, la fecha de venta, etc

        select pxp.list_unique(v.correlativo_venta || '<br>') into v_ventas
        from vef.tventa_detalle vd
          inner join vef.tventa v on v.id_venta = vd.id_venta
        where vd.descripcion is not null and vd.id_boleto is null
        		and pxp.f_is_positive_integer(vd.descripcion) and
              v.estado = 'finalizado' and vd.descripcion != ''  and vd.estado_reg = 'activo' and v.id_punto_venta = v_parametros.id_punto_venta
              and v.tipo_factura = v_parametros.tipo_factura;
        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Relacion verificada');

        v_resp = pxp.f_agrega_clave(v_resp,'ventas',v_ventas);
        --Devuelve la respuesta
        return v_resp;

      end;
        /*********************************
        #TRANSACCION: 'VF_VENFOR_INS'
        #DESCRIPCION: RECUPERA LAS FORMULAS
        #AUTOR: Ismael Valdivia Aranibar
        #FECHA: 9-4-2019
        ***********************************/

        elsif (p_transaccion = 'VF_VENFOR_INS') then

        BEGIN

        							/*SELECT string_agg (ing.desc_ingas, ',')
                                    into v_contador_formula
                                    from vef.tformula_detalle form
                                    inner join param.tconcepto_ingas ing on ing.id_concepto_ingas = form.id_concepto_ingas
                                    where form.id_formula = v_parametros.id_formula;*/
          --raise exception 'llega aqui el id formula %, el id_sucursal %',v_parametros.id_formula,v_parametros.id_sucursal;
      	/*  select    string_agg (ing.desc_ingas, ','),
		  			string_agg  (pro.id_sucursal_producto::VARCHAR,',')
                    into v_nombre_producto, v_id_producto
          from vef.tformula_detalle form
          inner join param.tconcepto_ingas ing on ing.id_concepto_ingas = form.id_concepto_ingas
          inner join vef.tsucursal_producto pro on pro.id_concepto_ingas = form.id_concepto_ingas
		  where form.id_formula = v_parametros.id_formula and pro.id_sucursal = v_parametros.id_sucursal;*/


          select
          string_agg (ing.desc_ingas, ','),
          string_agg  (form.id_formula::VARCHAR,','),
          string_agg  (ing.id_concepto_ingas::varchar,','),
          string_agg (ing.precio::varchar, ','),
          string_agg (ing.excento::varchar, ','),
          string_agg (ing.id_moneda::varchar, ','),
          string_agg (mon.codigo_internacional::varchar, ','),
          string_agg (ing.boleto_asociado::varchar, ','),
          string_agg (ing.comision::varchar, ',')
          into v_nombre_producto, v_id_formula,v_id_producto, v_precio, v_excento_req, v_id_moneda_paquetes, v_desc_moneda, v_boletos_asociados, v_comision_req
          from vef.tformula_detalle form
          inner join param.tconcepto_ingas ing on ing.id_concepto_ingas = form.id_concepto_ingas
          left join param.tmoneda mon on mon.id_moneda = ing.id_moneda
          --inner join vef.tsucursal_producto pro on pro.id_concepto_ingas = form.id_concepto_ingas
          where form.id_formula = v_parametros.id_formula; --and pro.id_sucursal = 16


          select distinct (ing.excento) into v_requiere_excento
          from vef.tformula_detalle form
          inner join param.tconcepto_ingas ing on ing.id_concepto_ingas = form.id_concepto_ingas
          where form.id_formula = v_parametros.id_formula and ing.excento = 'si';

          select distinct (ing.boleto_asociado) into v_boleto_asociado
          from vef.tformula_detalle form
          inner join param.tconcepto_ingas ing on ing.id_concepto_ingas = form.id_concepto_ingas
          where form.id_formula = v_parametros.id_formula and ing.boleto_asociado = 'si';



          if (v_requiere_excento is null) then
          	v_requiere_excento = 'no';
          end if;

          if (v_boleto_asociado is null) then
          	v_boleto_asociado = 'no';
          end if;



          --raise exception 'LLEGA AQUI EL contador %',v_parametros.id_formula;
          --Definition of the response
            v_resp = pxp.f_agrega_clave(v_resp, 'message ', 'Contador');
            v_resp = pxp.f_agrega_clave(v_resp,'v_nombre_producto',v_nombre_producto::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_id_formula',v_id_formula::varchar);
           	v_resp = pxp.f_agrega_clave(v_resp,'v_id_producto',v_id_producto::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_precio',v_precio::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_excento_req',v_excento_req::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_requiere_excento',v_requiere_excento::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_id_moneda_paquetes',v_id_moneda_paquetes::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_desc_moneda',v_desc_moneda::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_boleto_asociado',v_boleto_asociado::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_boletos_asociados',v_boletos_asociados::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_comision_paquete',v_comision_paquete::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_comision_req',v_comision_req::varchar);

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

ALTER FUNCTION vef.ft_venta_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
