CREATE OR REPLACE FUNCTION vef.ft_apertura_cierre_caja_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_apertura_cierre_caja_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tapertura_cierre_caja'
 AUTOR: 		 (jrivera)
 FECHA:	        07-07-2016 14:16:20
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
	v_id_apertura_cierre_caja	integer;
    v_id_moneda				integer;
    v_cod_moneda			varchar;
    v_registro				record;
    v_total_ventas			numeric;
    v_total_boletos			numeric;
    v_id_moneda_usd			integer;
    v_tipo_usuario			varchar;
	v_m1 numeric;
    v_me numeric;
    v_tipo_cambio			numeric;
    v_codigo_moneda			varchar;
    v_tolerancia			varchar;

    --AUMENTO VARIABLES TIPO CAMBIO
    v_id_sucursal			integer;
    v_id_moneda_moneda_sucursal	integer;
    v_id_moneda_tri			integer;
    v_tipo_cambio_actual		numeric;
    v_tiene_dos_monedas		varchar;
    v_moneda_desc			varchar;
    v_fecha_emision			varchar;
    v_id_apertura_cierre_caja_auxiliar	varchar;
    v_datos_auxiliar		record;
    v_mensaje_apertura		varchar;
    v_existencia_abiertos	integer;
    v_id_apertura_cierre_caja_admin integer;
    v_estado_cajero_admin	varchar;
	v_cajero_admin			varchar;

BEGIN

    v_nombre_funcion = 'vef.ft_apertura_cierre_caja_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_APCIE_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera
 	#FECHA:		07-07-2016 14:16:20
	***********************************/

	if(p_transaccion='VF_APCIE_INS')then

        begin

        /*************************************************CONTROL PARA EL TIPO DE CAMBIO*********************************************************************/
        /*****Recuperamos el id_sucursal para obtener la moneda***/
        select venta.id_sucursal into v_id_sucursal
        from vef.tpunto_venta venta
        where venta.id_punto_venta = v_parametros.id_punto_venta;
		/*********************************************************/

        /*Recuperamos el id_moneda de la sucursal obtenida*/
        select sm.id_moneda into v_id_moneda_moneda_sucursal
        from vef.tsucursal s
        inner join vef.tsucursal_moneda sm on s.id_sucursal = sm.id_sucursal
        where s.id_sucursal = v_id_sucursal and sm.tipo_moneda = 'moneda_base';
        /**************************************************/

    	/*Recuperamos el id_moneda de triangulacion*/
        select m.id_moneda,m.codigo_internacional,m.moneda || ' (' || m.codigo_internacional || ')' into v_id_moneda_tri
        from param.tmoneda m
        where m.estado_reg = 'activo' and m.triangulacion = 'si';
        /*****************************************************************************************************************************************/

        v_tiene_dos_monedas = 'no';
        v_tipo_cambio_actual = 1;

		if (v_id_moneda_tri != v_id_moneda_moneda_sucursal) then
            	v_tiene_dos_monedas = 'si';
                v_tipo_cambio_actual = param.f_get_tipo_cambio_v2(v_id_moneda_moneda_sucursal, v_id_moneda_tri,v_parametros.fecha_apertura_cierre::date,'O');
        end if;
           /*********************VERIFICAMOS SI EXISTE EL TIPO DE CAMBIO*************************/
           IF (v_tipo_cambio_actual is null) then

            	select  mon.codigo into v_moneda_desc
                from param.tmoneda mon
                where mon.id_moneda = v_id_moneda_tri;

                SELECT to_char(v_parametros.fecha_apertura_cierre,'DD/MM/YYYY') into v_fecha_emision;

            	raise exception 'No se pudo recuperar el tipo de cambio para la moneda: % en fecha: %, comuniquese con personal de Contabilidad Ingresos.',v_moneda_desc,v_fecha_emision;
            end if;
/*******************************************************************************************************************************************************************************************************/

        	if (exists (select 1
            			from vef.tapertura_cierre_caja acc
                        where id_usuario_cajero = p_id_usuario and
                        (id_punto_venta =v_parametros.id_punto_venta or id_sucursal =v_parametros.id_sucursal)
        				and fecha_apertura_cierre = v_parametros.fecha_apertura_cierre::date and estado_reg = 'activo')) then
            	raise exception 'Ya existe una caja registrada en fecha % para el usuario. Por favor revise los datos', v_parametros.fecha_apertura_cierre;
            end if;

            if (exists (select 1
            			from vef.tapertura_cierre_caja acc
                        where id_usuario_cajero = p_id_usuario and
        				estado = 'abierto')) then
            	raise exception 'El usuario ya tiene una caja abierta. Debe cerrarla para poder abrir otra';
            end if;


            if (v_parametros.id_sucursal is not null) then
            	select sm.id_moneda into v_id_moneda
                from vef.tsucursal s
                inner join vef.tsucursal_moneda sm on s.id_sucursal = sm.id_sucursal
                where s.id_sucursal = v_parametros.id_sucursal and sm.tipo_moneda = 'moneda_base';

            	if ( not exists (select 1
                                from vef.tsucursal_usuario su
                                where su.id_sucursal = v_parametros.id_sucursal and su.estado_reg = 'activo' and
                                su.tipo_usuario = 'cajero')) then
                	if (p_administrador = 0) then
                    	raise exception 'El usuario no esta registrado como cajero de la sucursal';
                    end if;
                end if;
            end if;

            if (v_parametros.id_punto_venta is not null) then
            	select sm.id_moneda into v_id_moneda
                from vef.tpunto_venta s
                inner join vef.tsucursal_moneda sm on s.id_sucursal = sm.id_sucursal
                where s.id_punto_venta = v_parametros.id_punto_venta and sm.tipo_moneda = 'moneda_base';

            	if ( not exists (select 1
                                from vef.tsucursal_usuario su
                                where su.id_punto_venta = v_parametros.id_punto_venta and su.estado_reg = 'activo' and
                                su.tipo_usuario = 'cajero')) then
                	if (p_administrador = 0) then
                    	raise exception 'El usuario no esta registrado como cajero del punto de venta';
                    end if;
                end if;
            end if;

			IF(v_parametros.monto_inicial=0)THEN
            	raise exception 'La caja no puede aperturarse con monto inicial 0';
            END IF;

            --Sentencia de la insercion
        	insert into vef.tapertura_cierre_caja(
			id_sucursal,
			id_punto_venta,
			id_usuario_cajero,
			id_moneda,
			monto_inicial,
			obs_apertura,
			monto_inicial_moneda_extranjera,
            id_usuario_reg,
            fecha_apertura_cierre,
            estado,
            --18-01-2021 (may)
            id_apertura_cierre_admin

          	) values(
			v_parametros.id_sucursal,
			v_parametros.id_punto_venta,
			p_id_usuario,
			v_id_moneda,
			v_parametros.monto_inicial,
			v_parametros.obs_apertura,
			v_parametros.monto_inicial_moneda_extranjera,
            p_id_usuario,
            v_parametros.fecha_apertura_cierre,
            'abierto',
			--18-01-2021 (may)
            v_parametros.id_apertura_cierre_admin

			)RETURNING id_apertura_cierre_caja into v_id_apertura_cierre_caja;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Apertura de Caja almacenado(a) con exito (id_apertura_cierre_caja'||v_id_apertura_cierre_caja||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_apertura_cierre_caja',v_id_apertura_cierre_caja::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VF_APCIE_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		07-07-2016 14:16:20
	***********************************/

	elsif(p_transaccion='VF_APCIE_MOD')then

		begin

        v_mensaje_apertura = '';
        /*Aqui verificamos si el cajero tiene un cajero auxiliar*/
        select list (acc.id_apertura_cierre_caja::varchar)
        	   into
               v_id_apertura_cierre_caja_auxiliar
        from vef.tapertura_cierre_caja acc
        where acc.id_apertura_cierre_admin = v_parametros.id_apertura_cierre_caja;


        if (v_id_apertura_cierre_caja_auxiliar is not null) then

         	select count (aper.id_usuario_cajero)
               	   into
                   v_existencia_abiertos
          from vef.tapertura_cierre_caja aper
          inner join segu.vusuario u on u.id_usuario = aper.id_usuario_cajero
          where aper.id_apertura_cierre_caja in (select distinct (acc.id_apertura_cierre_caja)
                                                  from vef.tapertura_cierre_caja acc
                                                  where acc.id_apertura_cierre_admin = v_parametros.id_apertura_cierre_caja)
          and aper.estado = 'abierto';

          if (v_existencia_abiertos > 0) then


                FOR v_datos_auxiliar in (select aper.id_usuario_cajero,
                                                 aper.estado,
                                                 u.desc_persona
                                          from vef.tapertura_cierre_caja aper
                                          inner join segu.vusuario u on u.id_usuario = aper.id_usuario_cajero
                                          where aper.id_apertura_cierre_caja in (select distinct (acc.id_apertura_cierre_caja)
                                                                                  from vef.tapertura_cierre_caja acc
                                                                                  where acc.id_apertura_cierre_admin = v_parametros.id_apertura_cierre_caja)
                                          and aper.estado = 'abierto') loop

                v_mensaje_apertura = v_mensaje_apertura||'<p><b><font color="#005DFF">Cajero: </font>'||v_datos_auxiliar.desc_persona||'.</b></p>';
                v_mensaje_apertura = v_mensaje_apertura||'<p><b><font color="#005DFF">Estado Caja: </font>'||upper(v_datos_auxiliar.estado)||'.</b></p>';


                end loop;

                 if (v_existencia_abiertos > 1) then
                	  raise exception 'Los siguientes Cajeros Auxiliares:<br><br> % <br>Aún no cerraron su caja Favor contactarse con los mismos para coordinar cierre de Caja Saludos.',v_mensaje_apertura;
                 else
               	     raise exception 'El Cajero <br><br> % <br> Aún tiene su caja abierta Favor contactarse con el mismo para coordinar cierre de Caja Saludos.',v_mensaje_apertura;
                 end if;
            end if;

        end if;


        /********************************************************/

        	if (exists (select 1
            			from vef.tapertura_cierre_caja acc
                        where id_usuario_cajero = p_id_usuario and
                        (id_punto_venta =v_parametros.id_punto_venta or id_sucursal =v_parametros.id_sucursal)
        				and acc.estado='cerrado' and estado_reg = 'activo' and
                        acc.id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja )) then
            	raise exception 'La caja ya esta cerrada para el usuario. Por favor revise los datos';
            end if;

            if (exists (select 1
            			from vef.tapertura_cierre_caja acc
                        where id_usuario_cajero = p_id_usuario and
                        (id_punto_venta =v_parametros.id_punto_venta or id_sucursal =v_parametros.id_sucursal)
        				and fecha_apertura_cierre = v_parametros.fecha_apertura_cierre::date and estado_reg = 'activo')) then
            	IF v_parametros.accion!='cerrar' THEN
	            	raise exception 'Ya existe una caja registrada en fecha % para el usuario. Por favor revise los datos', v_parametros.fecha_apertura_cierre;
                END IF;
            end if;

			--Sentencia de la modificacion
			update vef.tapertura_cierre_caja set
			id_sucursal = v_parametros.id_sucursal,
			id_punto_venta = v_parametros.id_punto_venta,
			obs_cierre = v_parametros.obs_cierre,
			monto_inicial = v_parametros.monto_inicial,
			obs_apertura = v_parametros.obs_apertura,
			monto_inicial_moneda_extranjera = v_parametros.monto_inicial_moneda_extranjera,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            estado = (case when v_parametros.accion = 'cerrar' then
            			'cerrado'
            		  else
                      	'abierto'
                      end),
            arqueo_moneda_local = v_parametros.arqueo_moneda_local,
            arqueo_moneda_extranjera = v_parametros.arqueo_moneda_extranjera--,
            --18-01-2021 (may)
            --id_apertura_cierre_admin = v_parametros.id_apertura_cierre_admin

			where id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja;


     /*  if ((select modificado
            from vef.tapertura_cierre_caja
            where id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja) = 'no')then */
         delete from vef.tdetalle_apertura_cc fo
		where fo.id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja;

         INSERT INTO vef.tdetalle_apertura_cc ( id_usuario_reg,
                                                id_usuario_mod,
                                                fecha_reg,
                                                fecha_mod,
                                                estado_reg,
                                                id_apertura_cierre_caja,
                                                tipo_apertura,
                                                monto_ca_boleto_bs,
                                                monto_cc_boleto_bs,
                                                monto_cte_boleto_bs,
                                                monto_mco_boleto_bs,

                                                monto_ca_boleto_usd,
                                                monto_cc_boleto_usd,
                                                monto_cte_boleto_usd,
                                                monto_mco_boleto_usd,

                                                monto_ca_recibo_ml,
                                                monto_ca_recibo_me,
                                                monto_cc_recibo_ml,
                                                monto_cc_recibo_me,

                                                monto_ca_facturacion_bs,
                                                monto_cc_facturacion_bs,
                                                monto_cte_facturacion_bs,
                                                monto_mco_facturacion_bs,

                                                monto_ca_facturacion_usd,
                                                monto_cc_facturacion_usd,
                                                monto_cte_facturacion_usd,
                                                monto_mco_facturacion_usd,
                                                arqueo_moneda_local,
            									arqueo_moneda_extranjera,
                                                comisiones_ml,
                                                comisiones_me
                                              )
                                              VALUES (
                                                p_id_usuario,
                                                null,
                                                now(),
                                                null,
                                                'activo',
                                                v_parametros.id_apertura_cierre_caja,
                                                v_parametros.tipo,
                                                --local
                                                v_parametros.monto_ca_boleto_bs,
                                                v_parametros.monto_cc_boleto_bs,
                                                v_parametros.monto_cte_boleto_bs,
                                                v_parametros.monto_mco_boleto_bs,
                                                --Internacional
                                                v_parametros.monto_ca_boleto_usd,
                                                v_parametros.monto_cc_boleto_usd,
                                                v_parametros.monto_cte_boleto_usd,
                                                v_parametros.monto_mco_boleto_usd,

                                                v_parametros.monto_ca_recibo_ml,
                                                v_parametros.monto_ca_recibo_me,
                                                v_parametros.monto_cc_recibo_ml,
                                                v_parametros.monto_cc_recibo_me,

                                                v_parametros.monto_ca_facturacion_bs,
                                                v_parametros.monto_cc_facturacion_bs,
                                                v_parametros.monto_cte_facturacion_bs,
                                                v_parametros.monto_mco_facturacion_bs,

                                                v_parametros.monto_ca_facturacion_usd,
                                                v_parametros.monto_cc_facturacion_usd,
                                                v_parametros.monto_cte_facturacion_usd,
                                                v_parametros.monto_mco_facturacion_usd,

                                                v_parametros.arqueo_moneda_local,
            									v_parametros.arqueo_moneda_extranjera,
                                                v_parametros.comisiones_ml,
                                                 v_parametros.comisiones_me);
		--else

     /*
       v_m1 = v_parametros.comisiones_ml;

       v_me = v_parametros.comisiones_me;

        update vef.tdetalle_apertura_cc set
        id_usuario_mod = p_id_usuario,
        fecha_mod = now(),
        monto_ca_boleto_bs = v_parametros.monto_ca_boleto_bs,
        monto_cc_boleto_bs = v_parametros.monto_cc_boleto_bs,
        monto_cte_boleto_bs = v_parametros.monto_cte_boleto_bs,
        monto_mco_boleto_bs = v_parametros.monto_mco_boleto_bs,
        monto_ca_boleto_usd = v_parametros.monto_ca_boleto_usd,
        monto_cc_boleto_usd = v_parametros.monto_cc_boleto_usd,
        monto_cte_boleto_usd = v_parametros.monto_cte_boleto_usd,
        monto_mco_boleto_usd = v_parametros.monto_mco_boleto_usd,
        monto_ca_recibo_ml = v_parametros.monto_ca_recibo_ml,
        monto_ca_recibo_me = v_parametros.monto_ca_recibo_me,
        monto_cc_recibo_ml = v_parametros.monto_cc_recibo_ml,
        monto_cc_recibo_me = v_parametros.monto_cc_recibo_me,
        monto_ca_facturacion_bs = v_parametros.monto_ca_facturacion_bs,
        monto_cc_facturacion_bs = v_parametros.monto_cc_facturacion_bs,
        monto_cte_facturacion_bs = v_parametros.monto_cte_facturacion_bs,
        monto_mco_facturacion_bs = v_parametros.monto_mco_facturacion_bs,
        monto_ca_facturacion_usd = v_parametros.monto_ca_facturacion_usd,
        monto_cc_facturacion_usd = v_parametros.monto_cc_facturacion_usd,
        monto_cte_facturacion_usd = v_parametros.monto_cte_facturacion_usd,
        monto_mco_facturacion_usd = v_parametros.monto_mco_facturacion_usd,
        arqueo_moneda_local = v_parametros.arqueo_moneda_local,
        arqueo_moneda_extranjera = v_parametros.arqueo_moneda_extranjera,
        comisiones_ml = v_m1,
        comisiones_me = v_me
        where id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja;
		end if;
            */

            if (pxp.f_existe_parametro(p_tabla,'monto_ca_recibo_ml') = TRUE) then
            	UPDATE vef.tapertura_cierre_caja SET
                monto_ca_recibo_ml = v_parametros.monto_ca_recibo_ml
                WHERE id_apertura_cierre_caja=v_parametros.id_apertura_cierre_caja;
            end if;

            if (pxp.f_existe_parametro(p_tabla,'monto_cc_recibo_ml') = TRUE) then
            	UPDATE vef.tapertura_cierre_caja SET
                monto_cc_recibo_ml = v_parametros.monto_cc_recibo_ml
                WHERE id_apertura_cierre_caja=v_parametros.id_apertura_cierre_caja;
            end if;

            if (pxp.f_existe_parametro(p_tabla,'fecha_apertura_cierre') = TRUE) then
            	UPDATE vef.tapertura_cierre_caja SET
                fecha_apertura_cierre = v_parametros.fecha_apertura_cierre
                WHERE id_apertura_cierre_caja=v_parametros.id_apertura_cierre_caja;
            end if;

            select * into v_registro
            from vef.tapertura_cierre_caja
            where id_apertura_cierre_caja=v_parametros.id_apertura_cierre_caja;

            --obtener codigo de moneda e id_moneda de la sucursal
            if (v_parametros.id_sucursal is not null) then
            	select m.codigo_internacional,m.id_moneda into v_cod_moneda,v_id_moneda
                from vef.tpunto_venta s
                inner join vef.tsucursal_moneda sm on s.id_sucursal = sm.id_sucursal
                inner join param.tmoneda m on m.id_moneda = sm.id_moneda
                where s.id_sucursal = v_parametros.id_sucursal and sm.tipo_moneda = 'moneda_base';

            end if;
            --obtener codigo de moneda e id_moneda del putno de venta
            if (v_parametros.id_punto_venta is not null) then
            	select m.codigo_internacional,m.id_moneda into v_cod_moneda,v_id_moneda
                from vef.tpunto_venta s
                inner join vef.tsucursal_moneda sm on s.id_sucursal = sm.id_sucursal
                inner join param.tmoneda m on m.id_moneda = sm.id_moneda
                where s.id_punto_venta = v_parametros.id_punto_venta and sm.tipo_moneda = 'moneda_base';

            end if;

            --Si la moneda de la sucursal es USD quiere decir q no debe haber moneda extranjera
            if (v_cod_moneda = 'USD' and v_parametros.accion = 'cerrar' and coalesce(v_parametros.arqueo_moneda_extranjera,0) > 0 ) then
            	raise exception 'No se maneja importes en moneda extranjera en esta sucursal';
            end if;

            --Si la accion es cerrar
            if (v_parametros.accion = 'cerrar') then
            	--obtener el ototal de ventas ya sea por sucursal o por punto de venta
            	if (v_parametros.id_punto_venta is not null) then
                    select coalesce(sum (vfp.monto_mb_efectivo),0) into v_total_ventas
                    from vef.tventa v
                    inner join vef.tventa_forma_pago vfp on vfp.id_venta = v.id_venta
                    inner join vef.tforma_pago fp on fp.id_forma_pago = vfp.id_forma_pago
                    inner join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                    where v.estado = 'finalizado'  and v.fecha_reg::date = v_registro.fecha_apertura_cierre and
                        v_parametros.id_punto_venta = v.id_punto_venta and v.id_usuario_cajero = p_id_usuario and
                        fp.codigo like 'CA';
            	else
                	select coalesce(sum (vfp.monto_mb_efectivo),0)  into v_total_ventas
                    from vef.tventa v
                    inner join vef.tventa_forma_pago vfp on vfp.id_venta = v.id_venta
                    inner join vef.tforma_pago fp on fp.id_forma_pago = vfp.id_forma_pago
                    inner join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                    where v.estado = 'finalizado'  and v.fecha_reg::date = v_registro.fecha_apertura_cierre and
                        v_parametros.id_sucursal = v.id_sucursal and v.id_usuario_cajero = p_id_usuario and
                        fp.codigo like 'CA';
                end if;

                select m.id_moneda into v_id_moneda_usd
                from param.tmoneda m
                where m.codigo_internacional = 'USD';

                --si el sistema de ventas se integra con ingresos de boa obtener el total de bolesto
                if (pxp.f_get_variable_global('vef_integracion_obingresos') = 'true') then

                    select sum((case when mon.codigo_internacional = 'USD' and v_cod_moneda != 'USD' then
                                    param.f_convertir_moneda(mon.id_moneda,v_id_moneda,bfp.importe,now()::date,'O',2)
                                else
                                    bfp.importe
                                end)) into v_total_boletos
                    from obingresos.tboleto b
                    inner join obingresos.tboleto_forma_pago bfp on bfp.id_boleto = b.id_boleto
                    inner join vef.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                    inner join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                    where b.estado = 'pagado' and b.fecha_reg::date = v_registro.fecha_apertura_cierre and
                    b.id_punto_venta = v_parametros.id_punto_venta and b.id_usuario_cajero = p_id_usuario and
                    fp.codigo like 'CA';
                    --raise exception 'llega %,%,%',v_parametros.id_punto_venta,v_registro.fecha_apertura_cierre,p_id_usuario;
            	ELSE
                	v_total_boletos = 0;
                end if;

                --si hay un monto de arqueo en moneda extranjera lo convertimos a moneda de sucursal
                if (coalesce(v_parametros.arqueo_moneda_extranjera,0) > 0) then
                	v_parametros.arqueo_moneda_extranjera = param.f_convertir_moneda(v_id_moneda_usd,v_id_moneda,coalesce(v_parametros.arqueo_moneda_extranjera,0),now()::date,'O',2);
                end if;

            	--si el total de ventas y boletos es menor q los arqueos en moneda de sucursal falta dinero!!!
                if (v_total_boletos + v_total_ventas + v_registro.monto_inicial + param.f_convertir_moneda(v_id_moneda_usd,v_id_moneda,coalesce(v_registro.monto_inicial_moneda_extranjera,0),now()::date,'O',2) > v_parametros.arqueo_moneda_local + COALESCE(v_parametros.arqueo_moneda_extranjera,0) ) then
                	--raise exception 'Los montos del arqueo en moneda local y extranjera son inferiores al total vendido en efectivo mas el monto inicial: %',v_total_boletos + v_total_ventas + v_registro.monto_inicial;
                end if;

            end if;


			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Apertura de Caja modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_apertura_cierre_caja',v_parametros.id_apertura_cierre_caja::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'VF_ABRAPCIE_MOD'
 	#DESCRIPCION:	Apertura de caja cerrada
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		15-11-2017
	***********************************/

	elsif(p_transaccion='VF_ABRAPCIE_MOD')then

		begin
            select su.tipo_usuario into v_tipo_usuario
			from vef.tapertura_cierre_caja ap
          	inner join vef.tsucursal_usuario su on su.id_punto_venta=ap.id_punto_venta
			where su.id_usuario=p_id_usuario and ap.id_apertura_cierre_caja=v_parametros.id_apertura_cierre_caja;


            /*Aqui poner control para que el cajero auxiliar no pueda abrir caja si el cajero Admin ya cerro su caja*/
            select caja.id_apertura_cierre_admin
            	   into
                   v_id_apertura_cierre_caja_admin
            from vef.tapertura_cierre_caja caja
            where caja.id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja;

            if (v_id_apertura_cierre_caja_admin is not null) then
            v_mensaje_apertura = '';
            	select
                       aper.estado,
                       u.desc_persona
                       into
                       v_estado_cajero_admin,
                       v_cajero_admin
                from vef.tapertura_cierre_caja aper
                inner join segu.vusuario u on u.id_usuario = aper.id_usuario_cajero
                where aper.id_apertura_cierre_caja = v_id_apertura_cierre_caja_admin;

                /*Aqui verificamos el estado de la caja*/
                if (v_estado_cajero_admin = 'cerrado') then

                    v_mensaje_apertura = v_mensaje_apertura||'<p><b><font color="#005DFF">Cajero: </font>'||v_cajero_admin||'.</b></p>';
                    v_mensaje_apertura = v_mensaje_apertura||'<p><b><font color="#005DFF">Estado Caja: </font>'||upper(v_estado_cajero_admin)||'.</b></p>';

                	raise exception 'El cajero % tiene la caja cerrada. Favor coordinar con el mismo para que abra su caja y realizar el cambio correspondiente.',v_mensaje_apertura;

                end if;
                /***************************************/

            end if;



            /********************************************************************************************************/



            if (exists (select 1
            			from vef.tapertura_cierre_caja acc
                        where id_usuario_cajero = p_id_usuario and
        				date(acc.fecha_reg) != CURRENT_DATE and estado = 'cerrado' and
                        acc.id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja ) ) then

            	IF v_tipo_usuario !='administrador' THEN
            		raise exception 'La caja no puede ser re-abierta posterior a su fecha de apertura para el usuario. Por favor contactarse con personal de Finanzas';
                END IF;

            end if;


			--Sentencia de la modificacion
			update vef.tapertura_cierre_caja set
            estado = 'abierto',
            modificado = 'si'
			where id_apertura_cierre_caja=v_parametros.id_apertura_cierre_caja;
          IF((select cde.tipo_apertura
             from vef.tdetalle_apertura_cc cde
             where cde.id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja) = 'carga')then

            update vef.tdetalle_apertura_cc  set
                  monto_ca_facturacion_bs = 0,
                  monto_cc_facturacion_bs = 0,
                  monto_cte_facturacion_bs = 0,
                  monto_mco_facturacion_bs = 0,
                  monto_ca_facturacion_usd = 0,
                  monto_cc_facturacion_usd = 0,
                  monto_cte_facturacion_usd = 0,
                  monto_mco_facturacion_usd = 0
                  where id_apertura_cierre_caja =v_parametros.id_apertura_cierre_caja ;
            else
            delete from vef.tdetalle_apertura_cc d
            where d.id_apertura_cierre_caja =v_parametros.id_apertura_cierre_caja ;
		end if;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Apertura de Caja modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_apertura_cierre_caja',v_parametros.id_apertura_cierre_caja::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VF_APCIE_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		07-07-2016 14:16:20
	***********************************/

	elsif(p_transaccion='VF_APCIE_ELI')then

		begin
			--Sentencia de la eliminacion

            select * into v_registro
            from vef.tapertura_cierre_caja apc
            where id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja;



            if (exists (select 1 from vef.tventa v
            			where v.estado_reg = 'activo' and
                        	(v.id_punto_venta = v_registro.id_punto_venta or v.id_sucursal = v_registro.id_sucursal ) and
                            v.fecha = v_registro.fecha_apertura_cierre and v.id_usuario_cajero = p_id_usuario)) then
				raise exception 'Ya se registraron ventas con esta apertura de caja. Debe eliminar esas ventas para poder eliminar la apertura';
            end if;
            if (exists (select 1 from pg_catalog.pg_namespace where nspname = 'obingresos' ))then
                if (exists (select 1 from obingresos.tboleto_amadeus b
                            where b.estado_reg = 'activo' and
                                b.id_punto_venta = v_registro.id_punto_venta and
                                b.fecha_emision = v_registro.fecha_apertura_cierre and
                                b.estado='revisado' and
                                b.id_usuario_cajero = p_id_usuario)) then
                    raise exception 'Ya se emitieron boletos con esta apertura de caja. Debe eliminar esos boletos para poder eliminar la apertura';
                end if;
            end if;

            delete from vef.tapertura_cierre_caja
            where id_apertura_cierre_caja=v_parametros.id_apertura_cierre_caja;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Apertura de Caja eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_apertura_cierre_caja',v_parametros.id_apertura_cierre_caja::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    /*********************************
 	#TRANSACCION:  'VF_APCIE_FECH'
 	#DESCRIPCION:	Insertar  entrega brinks
 	#AUTOR:		MMM
 	#FECHA:		10-11-2017
	***********************************/

	elsif(p_transaccion='VF_APCIE_FECH')then

		begin

        update vef.tapertura_cierre_caja   set
        id_entrega_brinks = v_parametros.id_entrega_brinks
        where fecha_apertura_cierre::date = v_parametros.fecha
        and id_punto_venta= v_parametros.id_punto_venta
        and id_usuario_cajero = v_parametros.id_usuario_cajero;

         --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','entrega insertada (a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_entrega_brinks',v_parametros.id_entrega_brinks::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
    #TRANSACCION: 'VF_APCIE_BOR'
    #DESCRIPCION: Date-mining
    #AUTOR: MMM
    #FECHA: 10-11-2017
    ***********************************/

	elsif (p_transaccion = 'VF_APCIE_BOR') then

  	BEGIN

      update vef.tapertura_cierre_caja  set
      id_entrega_brinks = null
      where id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja;

      --Definition of the response
      	v_resp = pxp.f_agrega_clave(v_resp, 'message ', 'delivery inserted (a)');
        v_resp = pxp.f_agrega_clave(v_resp, 'id_apertura_cierre_caja ', v_parametros.id_apertura_cierre_caja:: varchar);

      --Returns the answer
        return v_resp;

  	END;

	 /*********************************
    #TRANSACCION: 'VF_TIPO_CAMBIO_IME'
    #DESCRIPCION: RECUPERA EL TIPO DE CAMBIO
    #AUTOR: FRANKLIN ESPINOZA ALVAREZ
    #FECHA: 5-12-2018
    ***********************************/

	elsif (p_transaccion = 'VF_TIPO_CAMBIO_IME') then

  	BEGIN
      select tc.oficial
      into v_tipo_cambio
      from param.ttipo_cambio tc
      inner join param.tmoneda tm on tm.id_moneda = tc.id_moneda
      where tm.codigo_internacional = 'USD' and tc.fecha = v_parametros.fecha_cambio::date;


	  select
      mon.codigo_internacional
      into v_codigo_moneda
      from param.tmoneda mon
      where tipo_moneda='base';

      select va.valor into v_tolerancia
      from pxp.variable_global va
      where va.variable = 'tolerancia_cierre_caja';

      --Definition of the response
      	v_resp = pxp.f_agrega_clave(v_resp, 'message ', 'Tipo Cambio Argentina');
        v_resp = pxp.f_agrega_clave(v_resp,'v_tipo_cambio',v_tipo_cambio::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'v_codigo_moneda',v_codigo_moneda::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'v_tolerancia',v_tolerancia::varchar);

      --Returns the answer
        return v_resp;

  	END;

    /*********************************
    #TRANSACCION: 'VF_MON_BASE_IME'
    #DESCRIPCION: RECUPERA EL TIPO DE CAMBIO
    #AUTOR: IRVA
    #FECHA: 18-7-2019
    ***********************************/

	elsif (p_transaccion = 'VF_MON_BASE_IME') then

  	BEGIN

	  select
      mon.codigo_internacional
      into v_codigo_moneda
      from param.tmoneda mon
      where tipo_moneda='base';

      --Definition of the response
      	v_resp = pxp.f_agrega_clave(v_resp, 'message ', 'Tipo Cambio Argentina');
        v_resp = pxp.f_agrega_clave(v_resp,'v_codigo_moneda',v_codigo_moneda::varchar);

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

ALTER FUNCTION vef.ft_apertura_cierre_caja_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
