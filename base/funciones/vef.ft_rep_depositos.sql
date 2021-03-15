CREATE OR REPLACE FUNCTION vef.ft_rep_depositos (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_rep_depositos
 DESCRIPCION:   Funcion Para Generar reporte de Depositos
 AUTOR: 		 (Ismael Valdivia)
 FECHA:	        12-03-2020 09:30:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
    v_consulta_depositos varchar;
    v_filtro_punto_venta varchar;


BEGIN

	v_nombre_funcion = 'vef.ft_rep_depositos';
    v_parametros = pxp.f_get_record(p_tabla);


        /*********************************
        #TRANSACCION:  'VEF_REP_DEP_VEN_SEL'
        #DESCRIPCION:	Reporte de Depositos
        #AUTOR:		Ismael Valdivia
        #FECHA:		12-03-2021 09:20:00
        ***********************************/
        if(p_transaccion = 'VEF_REP_DEP_VEN_SEL')then

            begin



            if (v_parametros.formato_reporte = 'REPORTE DETALLE DE DEPÓSITOS') then

            IF(v_parametros.id_punto_venta = 0) then
            	v_filtro_punto_venta = '0=0';
            else
            	v_filtro_punto_venta = 'pv.id_punto_venta = '||v_parametros.id_punto_venta||'';
            end if;

                create temp table reporte_depositos (
                                                                fecha_venta date,
                                                                nro_deposito varchar,
                                                                fecha_deposito date,
                                                                importe_ml numeric,
                                                                importe_usd numeric,
                                                                cuenta_bancaria varchar,
                                                                cajero varchar,
                                                                usuario_registro varchar,
                                                                observaciones varchar,
                                                                tipo_deposito	varchar,
                                                                punto_venta varchar
                                                              )on commit drop;
                CREATE INDEX treporte_depositos_fecha_venta ON reporte_depositos
                USING btree (fecha_venta);

                CREATE INDEX treporte_depositos_fecha_deposito ON reporte_depositos
                USING btree (fecha_deposito);

                v_consulta_depositos = 'insert into reporte_depositos (
                                                                       fecha_venta,
                                                                       nro_deposito,
                                                                       fecha_deposito,
                                                                       importe_ml,
                                                                       importe_usd,
                                                                       cuenta_bancaria,
                                                                       cajero,
                                                                       usuario_registro,
                                                                       observaciones,
                                                                       tipo_deposito,
                                                                       punto_venta
                                                                       )
                                       ((select acc.fecha_apertura_cierre,
                                               depo.nro_deposito,
                                               depo.fecha,
                                               (CASE
                                                     WHEN depo.id_moneda_deposito != 2 THEN
                                                     depo.monto_deposito
                                                     ELSE
                                                     0
                                                END) as importe_ml,

                                                (CASE
                                                     WHEN depo.id_moneda_deposito = 2 THEN
                                                     depo.monto_deposito
                                                     ELSE
                                                     0
                                                END) as importe_me,
                                                cuen.nro_cuenta,
                                                usucaja.desc_persona as cajero,
                                                usu.desc_persona as registrado,
                                               ''''::varchar as observaciones,
                                               ''venta_propia''::varchar as tipo_deposito,
                                               pv.nombre
                                        from obingresos.tdeposito depo
                                        inner join vef.tapertura_cierre_caja acc on acc.id_apertura_cierre_caja = depo.id_apertura_cierre_caja
                                        inner join vef.tpunto_venta pv on pv.id_punto_venta = acc.id_punto_venta
                                        inner join vef.tsucursal sucu on sucu.id_sucursal = pv.id_sucursal
                                        inner join tes.tdepto_cuenta_bancaria de on de.id_depto = sucu.id_depto
                                        inner join tes.tcuenta_bancaria cuen on cuen.id_cuenta_bancaria = de.id_cuenta_bancaria and cuen.id_moneda = depo.id_moneda_deposito
                                        inner join segu.vusuario usu on usu.id_usuario = depo.id_usuario_reg
                                        inner join segu.vusuario usucaja on usucaja.id_usuario = acc.id_usuario_cajero
                                        where depo.tipo = ''venta_propia''
                                        and acc.fecha_apertura_cierre between '''||v_parametros.desde||''' and '''||v_parametros.hasta||'''
                                        and '||v_filtro_punto_venta||'
                                        order by acc.fecha_apertura_cierre asc, depo.fecha ASC)

                                        UNION ALL


                                        (select
                                               acc.fecha_apertura_cierre,
                                               depo.nro_deposito,
                                               depo.fecha,
                                               (CASE
                                                     WHEN depo.id_moneda_deposito != 2 THEN
                                                     depo.monto_deposito
                                                     ELSE
                                                     0
                                                END) as importe_ml,

                                                (CASE
                                                     WHEN depo.id_moneda_deposito = 2 THEN
                                                     depo.monto_deposito
                                                     ELSE
                                                     0
                                                END) as importe_me,
                                                cuen.nro_cuenta,
                                                usucaja.desc_persona as cajero,
                                                usu.desc_persona as registrado,
                                                (''RO Concepto: ''||ingas.desc_ingas) as observaciones,
                                                ''cuenta_corriente''::varchar as tipo_deposito,

                                               pv.nombre
                                        from vef.tventa ven
                                        inner join vef.tventa_detalle vendet on vendet.id_venta = ven.id_venta
                                        inner join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = vendet.id_producto
                                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                        inner join vef.tsucursal sucu on sucu.id_sucursal = pv.id_sucursal
                                        inner join obingresos.tdeposito depo on depo.id_deposito = ven.id_deposito
                                        inner join tes.tdepto_cuenta_bancaria de on de.id_depto = sucu.id_depto
                                        inner join tes.tcuenta_bancaria cuen on cuen.id_cuenta_bancaria = de.id_cuenta_bancaria and cuen.id_moneda = depo.id_moneda_deposito
                                        inner join segu.vusuario usu on usu.id_usuario = depo.id_usuario_reg


                                        inner join vef.tapertura_cierre_caja acc on acc.fecha_apertura_cierre = ven.fecha and acc.id_punto_venta = ven.id_punto_venta
                                        and acc.id_usuario_cajero = ven.id_usuario_cajero
                                         inner join segu.vusuario usucaja on usucaja.id_usuario = acc.id_usuario_cajero

                                        where ven.id_deposito is not null
                                        and ven.id_auxiliar_anticipo is null
                                        and depo.tipo = ''cuenta_corriente''
                                        and acc.fecha_apertura_cierre between '''||v_parametros.desde||''' and '''||v_parametros.hasta||'''
                                        and ven.estado = ''finalizado''
                                        and '||v_filtro_punto_venta||'
                                        order by depo.fecha asc)


                                        UNION ALL

                                        (select
                                                acc.fecha_apertura_cierre,
                                               depo.nro_deposito,
                                               depo.fecha,
                                               (CASE
                                                     WHEN depo.id_moneda_deposito != 2 THEN
                                                     depo.monto_deposito
                                                     ELSE
                                                     0
                                                END) as importe_ml,

                                                (CASE
                                                     WHEN depo.id_moneda_deposito = 2 THEN
                                                     depo.monto_deposito
                                                     ELSE
                                                     0
                                                END) as importe_me,
                                                cuen.nro_cuenta,
                                                usucaja.desc_persona as cajero,
                                                usu.desc_persona as registrado,
                                                (''Anticipo: RO NroRecibo: ''|| ven.nro_factura||'' CTA/CTE: ''||''(''||aux.codigo_auxiliar||'') ''||aux.nombre_auxiliar) as observaciones,
                                                NULL::varchar as tipo_deposito,

                                               pv.nombre
                                        from vef.tventa ven
                                        inner join vef.tventa_detalle vendet on vendet.id_venta = ven.id_venta
                                        inner join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = vendet.id_producto
                                        inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                        inner join vef.tsucursal sucu on sucu.id_sucursal = pv.id_sucursal
                                        inner join obingresos.tdeposito depo on depo.id_deposito = ven.id_deposito
                                        inner join tes.tdepto_cuenta_bancaria de on de.id_depto = sucu.id_depto
                                        inner join tes.tcuenta_bancaria cuen on cuen.id_cuenta_bancaria = de.id_cuenta_bancaria and cuen.id_moneda = depo.id_moneda_deposito
                                        inner join segu.vusuario usu on usu.id_usuario = depo.id_usuario_reg

                                        inner join vef.tapertura_cierre_caja acc on acc.fecha_apertura_cierre = ven.fecha and acc.id_punto_venta = ven.id_punto_venta
                                        and acc.id_usuario_cajero = ven.id_usuario_cajero
                                        inner join segu.vusuario usucaja on usucaja.id_usuario = acc.id_usuario_cajero

                                        where ven.id_deposito is not null
                                        and ven.id_auxiliar_anticipo is not null
                                        and depo.tipo = ''cuenta_corriente''
                                        and acc.fecha_apertura_cierre between '''||v_parametros.desde||''' and '''||v_parametros.hasta||'''
                                        and ven.estado = ''finalizado''
                                        and '||v_filtro_punto_venta||'
                                        order by depo.fecha asc)

                                        )
                                      ';

                --Aqui ejecutamos la consulta Armada
                execute v_consulta_depositos;


                v_consulta := '
                                ((select
                                     fecha_venta,
                                     nro_deposito,
                                     fecha_deposito,
                                     importe_ml,
                                     importe_usd,
                                     cuenta_bancaria,
                                     cajero,
                                     usuario_registro,
                                     observaciones,
                                     tipo_deposito,
                                     punto_venta,
                                     NULL::numeric as deposito_ml,
                                     NULL::numeric as deposito_me,
                                     NULL::numeric as tipo_cambio,
                                     NULL::numeric as total_venta_ml,
                                     NULL::numeric as total_venta_me,
                                     NULL::numeric as diferencia
                                from reporte_depositos)

                                UNION ALL

                                (select
                                     fecha_venta,
                                     ''Total Fecha: ''||TO_CHAR(fecha_venta, ''DD/MM/YYYY'')::varchar as nro_deposito,
                                     NULL::date as fecha_deposito,
                                     SUM(COALESCE(importe_ml,0)),
                                     SUM(COALESCE(importe_usd,0)),
                                     NULL::varchar as cuenta_bancaria,
                                     NULL::varchar as cajero,
                                     NULL::varchar as usuario_registro,
                                     NULL::varchar as observaciones,
                                     ''total''::varchar as tipo_deposito,
                                     NULL::varchar as punto_venta,
                                     NULL::numeric as deposito_ml,
                                     NULL::numeric as deposito_me,
                                     NULL::numeric as tipo_cambio,
                                     NULL::numeric as total_venta_ml,
                                     NULL::numeric as total_venta_me,
                                     NULL::numeric as diferencia
                                from reporte_depositos
                                group by fecha_venta)

                                UNION ALL

                                (select
                                     fecha_venta,
                                     NULL::varchar as nro_deposito,
                                     now()::date as fecha_deposito,
                                     NULL::numeric as importe_ml,
                                     NULL::numeric importe_usd,
                                     NULL::varchar as cuenta_bancaria,
                                     NULL::varchar as cajero,
                                     NULL::varchar as usuario_registro,
                                     NULL::varchar as observaciones,
                                     ''cabecera''::varchar as tipo_deposito,
                                     NULL::varchar as punto_venta,
                                     NULL::numeric as deposito_ml,
                                     NULL::numeric as deposito_me,
                                     NULL::numeric as tipo_cambio,
                                     NULL::numeric as total_venta_ml,
                                     NULL::numeric as total_venta_me,
                                     NULL::numeric as diferencia
                                from reporte_depositos
                                group by fecha_venta)
                                )
                                order by fecha_venta asc,nro_deposito ASC NULLS FIRST ,punto_venta DESC';

            else

            IF(v_parametros.id_punto_venta = 0) then
            	v_filtro_punto_venta = '0=0';
            else
            	v_filtro_punto_venta = 'cdo.id_punto_venta = '||v_parametros.id_punto_venta||'';
            end if;

            create temp table reporte_depositos (
                                                                fecha_venta date,
                                                                nro_deposito varchar,
                                                                fecha_deposito date,
                                                                importe_ml numeric,
                                                                importe_usd numeric,
                                                                cuenta_bancaria varchar,
                                                                cajero varchar,
                                                                usuario_registro varchar,
                                                                observaciones varchar,
                                                                tipo_deposito	varchar,
                                                                punto_venta varchar,
                                                                deposito_ml numeric,
                                                                deposito_me numeric,
                                                                tipo_cambio numeric,
                                                                total_venta_ml numeric,
                                                                total_venta_me numeric,
                                                                diferencia numeric
                                                              )on commit drop;
                CREATE INDEX treporte_depositos_fecha_venta ON reporte_depositos
                USING btree (fecha_venta);

                CREATE INDEX treporte_depositos_fecha_deposito ON reporte_depositos
                USING btree (fecha_deposito);

            v_consulta_depositos = '
            						insert into reporte_depositos (
                                                                       fecha_venta,
                                                                       nro_deposito,
                                                                       fecha_deposito,
                                                                       importe_ml,
                                                                       importe_usd,
                                                                       cuenta_bancaria,
                                                                       cajero,
                                                                       usuario_registro,
                                                                       observaciones,
                                                                       tipo_deposito,
                                                                       punto_venta,
                                                                       deposito_ml,
                                                                       deposito_me,
                                                                       tipo_cambio,
                                                                       total_venta_ml,
                                                                       total_venta_me,
                                                                       diferencia
                                                                       )
                                    (select
                                    		cdo.fecha_venta,
                                            NULL::varchar as nro_deposito,
                                            NULL::date as fecha_deposito,
                                            sum(cdo.arqueo_moneda_local) as arqueo_moneda_local,
                                            sum(cdo.arqueo_moneda_extranjera) as arqueo_moneda_extranjera,
                                            NULL::varchar as cuenta_bancaria,
                                            NULL::varchar as cajero,
                                            NULL::varchar AS usuario_registro,
                                            NULL::varchar as observaciones,
                                            NULL::varchar as tipo_deposito,
                                            NULL::varchar as punto_venta,
                                            sum(cdo.deposito_bs) as deposito_bs,
                                            sum(cdo.deposito_usd) as deposito_usd,
                                            tc.oficial,
                                            ((sum(cdo.arqueo_moneda_local) + (sum(cdo.arqueo_moneda_extranjera)*tc.oficial))) as total_venta_ml,
                                            ((sum(cdo.deposito_bs) + (sum(cdo.deposito_usd)*tc.oficial))) as total_venta_me,
                                            ((sum(cdo.arqueo_moneda_local) + (sum(cdo.arqueo_moneda_extranjera)*tc.oficial))-((sum(cdo.deposito_bs) + (sum(cdo.deposito_usd)*tc.oficial)))) as diferencia

                                    from vef.vdepositos cdo
                                    inner join param.ttipo_cambio tc on tc.fecha = cdo.fecha_venta and tc.id_moneda = 2
                                    where '||v_filtro_punto_venta||' and cdo.fecha_venta between '''||v_parametros.desde||''' and '''||v_parametros.hasta||'''
                                    group by cdo.fecha_venta, tc.oficial)';

                                    execute v_consulta_depositos;


                  v_consulta := '
                                      select
                                           fecha_venta,
                                           nro_deposito,
                                           fecha_deposito,
                                           importe_ml,
                                           importe_usd,
                                           cuenta_bancaria,
                                           cajero,
                                           usuario_registro,
                                           observaciones,
                                           tipo_deposito,
                                           punto_venta,
                                           deposito_ml,
                                           deposito_me,
                                           tipo_cambio,
                                           total_venta_ml,
                                           total_venta_me,
                                           diferencia
                                      from reporte_depositos
                                      order by fecha_venta ASC';





            end if;

                return v_consulta;

            end;

    /*********************************
        #TRANSACCION:  'VEF_REP_DEP_VEN_CONT'
        #DESCRIPCION:	Reporte de Depositos
        #AUTOR:		Ismael Valdivia
        #FECHA:		12-03-2021 09:20:00
        ***********************************/
        elsif(p_transaccion = 'VEF_REP_DEP_VEN_CONT')then

            begin



            if (v_parametros.formato_reporte = 'REPORTE DETALLE DE DEPÓSITOS') then

            IF(v_parametros.id_punto_venta = 0) then
            	v_filtro_punto_venta = '0=0';
            else
            	v_filtro_punto_venta = 'pv.id_punto_venta = '||v_parametros.id_punto_venta||'';
            end if;

                create temp table reporte_depositos (
                                                                fecha_venta date,
                                                                nro_deposito varchar,
                                                                fecha_deposito date,
                                                                importe_ml numeric,
                                                                importe_usd numeric,
                                                                cuenta_bancaria varchar,
                                                                cajero varchar,
                                                                usuario_registro varchar,
                                                                observaciones varchar,
                                                                tipo_deposito	varchar,
                                                                punto_venta varchar
                                                              )on commit drop;
                CREATE INDEX treporte_depositos_fecha_venta ON reporte_depositos
                USING btree (fecha_venta);

                CREATE INDEX treporte_depositos_fecha_deposito ON reporte_depositos
                USING btree (fecha_deposito);

                v_consulta_depositos = 'insert into reporte_depositos (
                                                                       fecha_venta,
                                                                       nro_deposito,
                                                                       fecha_deposito,
                                                                       importe_ml,
                                                                       importe_usd,
                                                                       cuenta_bancaria,
                                                                       cajero,
                                                                       usuario_registro,
                                                                       observaciones,
                                                                       tipo_deposito,
                                                                       punto_venta
                                                                       )
                                       ((select acc.fecha_apertura_cierre,
                                               depo.nro_deposito,
                                               depo.fecha,
                                               (CASE
                                                     WHEN depo.id_moneda_deposito != 2 THEN
                                                     depo.monto_deposito
                                                     ELSE
                                                     0
                                                END) as importe_ml,

                                                (CASE
                                                     WHEN depo.id_moneda_deposito = 2 THEN
                                                     depo.monto_deposito
                                                     ELSE
                                                     0
                                                END) as importe_me,
                                                cuen.nro_cuenta,
                                                usucaja.desc_persona as cajero,
                                                usu.desc_persona as registrado,
                                               ''''::varchar as observaciones,
                                               ''venta_propia''::varchar as tipo_deposito,
                                               pv.nombre
                                        from obingresos.tdeposito depo
                                        inner join vef.tapertura_cierre_caja acc on acc.id_apertura_cierre_caja = depo.id_apertura_cierre_caja
                                        inner join vef.tpunto_venta pv on pv.id_punto_venta = acc.id_punto_venta
                                        inner join vef.tsucursal sucu on sucu.id_sucursal = pv.id_sucursal
                                        inner join tes.tdepto_cuenta_bancaria de on de.id_depto = sucu.id_depto
                                        inner join tes.tcuenta_bancaria cuen on cuen.id_cuenta_bancaria = de.id_cuenta_bancaria and cuen.id_moneda = depo.id_moneda_deposito
                                        inner join segu.vusuario usu on usu.id_usuario = depo.id_usuario_reg
                                        inner join segu.vusuario usucaja on usucaja.id_usuario = acc.id_usuario_cajero
                                        where depo.tipo = ''venta_propia''
                                        and acc.fecha_apertura_cierre between '''||v_parametros.desde||''' and '''||v_parametros.hasta||'''
                                        and '||v_filtro_punto_venta||'
                                        order by acc.fecha_apertura_cierre asc, depo.fecha ASC)

                                        UNION ALL


                                        (select
                                               acc.fecha_apertura_cierre,
                                               depo.nro_deposito,
                                               depo.fecha,
                                               (CASE
                                                     WHEN depo.id_moneda_deposito != 2 THEN
                                                     depo.monto_deposito
                                                     ELSE
                                                     0
                                                END) as importe_ml,

                                                (CASE
                                                     WHEN depo.id_moneda_deposito = 2 THEN
                                                     depo.monto_deposito
                                                     ELSE
                                                     0
                                                END) as importe_me,
                                                cuen.nro_cuenta,
                                                usu.desc_persona as registrado,
                                                usucaja.desc_persona as cajero,
                                                (''RO Concepto: ''||ingas.desc_ingas) as observaciones,
                                                ''cuenta_corriente''::varchar as tipo_deposito,

                                               pv.nombre
                                        from vef.tventa ven
                                        inner join vef.tventa_detalle vendet on vendet.id_venta = ven.id_venta
                                        inner join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = vendet.id_producto
                                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                        inner join vef.tsucursal sucu on sucu.id_sucursal = pv.id_sucursal
                                        inner join obingresos.tdeposito depo on depo.id_deposito = ven.id_deposito
                                        inner join tes.tdepto_cuenta_bancaria de on de.id_depto = sucu.id_depto
                                        inner join tes.tcuenta_bancaria cuen on cuen.id_cuenta_bancaria = de.id_cuenta_bancaria and cuen.id_moneda = depo.id_moneda_deposito
                                        inner join segu.vusuario usu on usu.id_usuario = depo.id_usuario_reg


                                        inner join vef.tapertura_cierre_caja acc on acc.fecha_apertura_cierre = ven.fecha and acc.id_punto_venta = ven.id_punto_venta
                                        and acc.id_usuario_cajero = ven.id_usuario_cajero
                                         inner join segu.vusuario usucaja on usucaja.id_usuario = acc.id_usuario_cajero

                                        where ven.id_deposito is not null
                                        and ven.id_auxiliar_anticipo is null
                                        and depo.tipo = ''cuenta_corriente''
                                        and acc.fecha_apertura_cierre between '''||v_parametros.desde||''' and '''||v_parametros.hasta||'''
                                        and ven.estado = ''finalizado''
                                        and '||v_filtro_punto_venta||'
                                        order by depo.fecha asc)


                                        UNION ALL

                                        (select
                                                acc.fecha_apertura_cierre,
                                               depo.nro_deposito,
                                               depo.fecha,
                                               (CASE
                                                     WHEN depo.id_moneda_deposito != 2 THEN
                                                     depo.monto_deposito
                                                     ELSE
                                                     0
                                                END) as importe_ml,

                                                (CASE
                                                     WHEN depo.id_moneda_deposito = 2 THEN
                                                     depo.monto_deposito
                                                     ELSE
                                                     0
                                                END) as importe_me,
                                                cuen.nro_cuenta,
                                                usu.desc_persona as registrado,
                                                usucaja.desc_persona as cajero,
                                                (''Anticipo: RO NroRecibo: ''|| ven.nro_factura||'' CTA/CTE: ''||''(''||aux.codigo_auxiliar||'') ''||aux.nombre_auxiliar) as observaciones,
                                                NULL::varchar as tipo_deposito,

                                               pv.nombre
                                        from vef.tventa ven
                                        inner join vef.tventa_detalle vendet on vendet.id_venta = ven.id_venta
                                        inner join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = vendet.id_producto
                                        inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                        inner join vef.tsucursal sucu on sucu.id_sucursal = pv.id_sucursal
                                        inner join obingresos.tdeposito depo on depo.id_deposito = ven.id_deposito
                                        inner join tes.tdepto_cuenta_bancaria de on de.id_depto = sucu.id_depto
                                        inner join tes.tcuenta_bancaria cuen on cuen.id_cuenta_bancaria = de.id_cuenta_bancaria and cuen.id_moneda = depo.id_moneda_deposito
                                        inner join segu.vusuario usu on usu.id_usuario = depo.id_usuario_reg

                                        inner join vef.tapertura_cierre_caja acc on acc.fecha_apertura_cierre = ven.fecha and acc.id_punto_venta = ven.id_punto_venta
                                        and acc.id_usuario_cajero = ven.id_usuario_cajero
                                        inner join segu.vusuario usucaja on usucaja.id_usuario = acc.id_usuario_cajero

                                        where ven.id_deposito is not null
                                        and ven.id_auxiliar_anticipo is not null
                                        and depo.tipo = ''cuenta_corriente''
                                        and acc.fecha_apertura_cierre between '''||v_parametros.desde||''' and '''||v_parametros.hasta||'''
                                        and ven.estado = ''finalizado''
                                        and '||v_filtro_punto_venta||'
                                        order by depo.fecha asc)

                                        )
                                      ';

                --Aqui ejecutamos la consulta Armada
                execute v_consulta_depositos;

                v_consulta := 'SELECT COUNT (nro_deposito),
                	                  SUM(Coalesce(importe_ml,0))::numeric as total_importe_ml,
                                      SUM(coalesce(importe_usd,0))::numeric as total_importe_me,
                                      SUM(0::numeric) as total_deposito_ml,
                                      SUM(0::numeric) as total_deposito_me,
                                      SUM(0::numeric) as totales_venta_ml,
                                      SUM(0::numeric) as totales_venta_me,
                                      SUM(0::numeric) as total_diferencia
                	           FROM reporte_depositos
                                ';

            else

            IF(v_parametros.id_punto_venta = 0) then
            	v_filtro_punto_venta = '0=0';
            else
            	v_filtro_punto_venta = 'cdo.id_punto_venta = '||v_parametros.id_punto_venta||'';
            end if;

            create temp table reporte_depositos (
                                                                fecha_venta date,
                                                                nro_deposito varchar,
                                                                fecha_deposito date,
                                                                importe_ml numeric,
                                                                importe_usd numeric,
                                                                cuenta_bancaria varchar,
                                                                cajero varchar,
                                                                usuario_registro varchar,
                                                                observaciones varchar,
                                                                tipo_deposito	varchar,
                                                                punto_venta varchar,
                                                                deposito_ml numeric,
                                                                deposito_me numeric,
                                                                tipo_cambio numeric,
                                                                total_venta_ml numeric,
                                                                total_venta_me numeric,
                                                                diferencia numeric
                                                              )on commit drop;
                CREATE INDEX treporte_depositos_fecha_venta ON reporte_depositos
                USING btree (fecha_venta);

                CREATE INDEX treporte_depositos_fecha_deposito ON reporte_depositos
                USING btree (fecha_deposito);

            v_consulta_depositos = '
            						insert into reporte_depositos (
                                                                       fecha_venta,
                                                                       nro_deposito,
                                                                       fecha_deposito,
                                                                       importe_ml,
                                                                       importe_usd,
                                                                       cuenta_bancaria,
                                                                       cajero,
                                                                       usuario_registro,
                                                                       observaciones,
                                                                       tipo_deposito,
                                                                       punto_venta,
                                                                       deposito_ml,
                                                                       deposito_me,
                                                                       tipo_cambio,
                                                                       total_venta_ml,
                                                                       total_venta_me,
                                                                       diferencia
                                                                       )
                                    (select
                                    		cdo.fecha_venta,
                                            NULL::varchar as nro_deposito,
                                            NULL::date as fecha_deposito,
                                            sum(cdo.arqueo_moneda_local) as arqueo_moneda_local,
                                            sum(cdo.arqueo_moneda_extranjera) as arqueo_moneda_extranjera,
                                            NULL::varchar as cuenta_bancaria,
                                            NULL::varchar as cajero,
                                            NULL::varchar AS usuario_registro,
                                            NULL::varchar as observaciones,
                                            NULL::varchar as tipo_deposito,
                                            NULL::varchar as punto_venta,
                                            sum(cdo.deposito_bs) as deposito_bs,
                                            sum(cdo.deposito_usd) as deposito_usd,
                                            tc.oficial,
                                            ((sum(cdo.arqueo_moneda_local) + (sum(cdo.arqueo_moneda_extranjera)*tc.oficial))) as total_venta_ml,
                                            ((sum(cdo.deposito_bs) + (sum(cdo.deposito_usd)*tc.oficial))) as total_venta_me,
                                            ((sum(cdo.arqueo_moneda_local) + (sum(cdo.arqueo_moneda_extranjera)*tc.oficial))-((sum(cdo.deposito_bs) + (sum(cdo.deposito_usd)*tc.oficial)))) as diferencia

                                    from vef.vdepositos cdo
                                    inner join param.ttipo_cambio tc on tc.fecha = cdo.fecha_venta and tc.id_moneda = 2
                                    where '||v_filtro_punto_venta||' and cdo.fecha_venta between '''||v_parametros.desde||''' and '''||v_parametros.hasta||'''
                                    group by cdo.fecha_venta, tc.oficial)';

                                    execute v_consulta_depositos;


                  v_consulta := 'SELECT COUNT (nro_deposito),
                	                  SUM(Coalesce(importe_ml,0))::numeric as total_importe_ml,
                                      SUM(coalesce(importe_usd,0))::numeric as total_importe_me,
                                      SUM(coalesce(deposito_ml,0))::numeric as total_deposito_ml,
                                      SUM(coalesce(deposito_me,0))::numeric as total_deposito_me,
                                      SUM(coalesce(total_venta_ml,0))::numeric as totales_venta_ml,
                                      SUM(coalesce(total_venta_me,0))::numeric as totales_venta_me,
                                      SUM(coalesce(total_venta_ml,0)-COALESCE(total_venta_me,0)) as total_diferencia
                	           FROM reporte_depositos';




            end if;

                return v_consulta;

            end;


    else

		raise exception 'Transaccion inexistente';

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

ALTER FUNCTION vef.ft_rep_depositos (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
