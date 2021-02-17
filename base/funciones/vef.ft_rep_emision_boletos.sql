CREATE OR REPLACE FUNCTION vef.ft_rep_emision_boletos (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_rep_emision_boletos
 DESCRIPCION:   Funcion que hace conexion para recuperar datos de facturas, boletos
 AUTOR: 		 (Ismael Valdivia)
 FECHA:	        11-02-2020 10:30:09
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

	v_id_entidad		integer;
    v_id_deptos			varchar;
    v_registros 		record;
    v_reg_entidad		record;
    v_tabla_origen    	varchar;
    v_filtro     		varchar;
    v_tipo   			varchar;
    v_sincronizar		varchar;
    v_gestion			integer;
    v_periodo			integer;

    v_periodo_ini		integer;
    v_periodo_fin		integer;


    v_datos				record;
    v_cantidad_nit		integer;
    v_natural_sumpli	varchar;
    v_venta_total		numeric;
    v_desc_sistema		varchar;
    v_filtro_temp		varchar;
    v_conexion			varchar;

    v_literal_mes		varchar;
    v_filtro_per_ini	varchar;
    v_filtro_ges		varchar;
	v_literal_mes_inicio	varchar;
    v_literal_mes_final		varchar;
    v_gestion_ini		integer;
    v_gestion_fin		integer;
    v_datos_mayores		record;
    v_fecha_ini			date;
    v_fecha_fin			date;
    v_filtro_totales	varchar;
    v_datos_acumulado	record;
    v_monto_impuestos	numeric;
    v_cadena_cnx		varchar;
	v_total_general		varchar;
    v_id_moneda_base	integer;

BEGIN

	v_nombre_funcion = 'vef.ft_rep_emision_boletos';
    v_parametros = pxp.f_get_record(p_tabla);


        /*********************************
        #TRANSACCION:  'VEF_REP_EMI_BOL_SEL'
        #DESCRIPCION:	Reporte de Comisionistas
        #AUTOR:		Ismael Valdivia
        #FECHA:		25-01-2021 11:30:09
        ***********************************/
        if(p_transaccion = 'VEF_REP_EMI_BOL_SEL')then
            begin


            	/*Recuperamos la moneda base para sacar la conversion*/
                select mon.id_moneda
                	   into
                       v_id_moneda_base
                from param.tmoneda mon
                where mon.tipo_moneda = 'base';
                /*****************************************************/

                create temp table facturas_recibos_temporal (
                                                                fecha_factura date,
                                                                nro_factura varchar,
                                                                nro_documento varchar,
                                                                ruta varchar,
                                                                pasajero varchar,
                                                                debe numeric,
                                                                haber numeric,
                                                                tipo_factura varchar,
                                                                punto_venta varchar
                                                              )on commit drop;
                CREATE INDEX tfacturas_recibos_temporal_fecha_factura ON facturas_recibos_temporal
                USING btree (fecha_factura);

                CREATE INDEX tfacturas_recibos_temporal_nro_factura ON facturas_recibos_temporal
                USING btree (nro_factura);

                CREATE INDEX tfacturas_recibos_temporal_tipo_factura ON facturas_recibos_temporal
                USING btree (tipo_factura);
                /*Si el punto de venta es todos no ponemos ningun filtro*/
                if(v_parametros.id_punto_venta = 0) then
                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        nro_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta)


                select  ven.fecha,
                		ven.nro_factura::varchar as nro_factura,
                        ven.nro_factura::varchar as nro_documento,
                		(CASE
                              WHEN ven.tipo_factura = 'computarizada'

                              THEN
                              	'FACTURACION COMPUTARIZADA'
                              WHEN ven.tipo_factura = 'manual'

                              THEN
                              	'FACTURACION MANUAL'
                              WHEN ven.tipo_factura = 'recibo'

                              THEN
                              	'RECIBO OFICIAL'
                                WHEN ven.tipo_factura = 'recibo_manual'

                              THEN
                              	'RECIBO OFICIAL MANUAL'
                              ELSE
                              	''

                        END) as ruta,
                        ven.nombre_factura,

                        /*(CASE
                              WHEN fp.id_moneda = 2

                              THEN
                              	param.f_convertir_moneda(fp.id_moneda,v_id_moneda_base,fp.monto_mb_efectivo,ven.fecha,'O',2,NULL,'si')
                              ELSE
                              	fp.monto_mb_efectivo

                        END) as monto_debe,*/
                        fp.monto_mb_efectivo as monto_debe,

                        0::numeric as monto_haber,
                        ven.tipo_factura,
                        pv.nombre
                from vef.tventa ven
                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                where ven.estado = 'finalizado' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date;

                /*Aqui recuperamos la data de los boletos para recuperar la ruta de los boletos*/

                create temp table temporal_rutas_boletos (    desc_ruta varchar,
                                                              nro_factura numeric
                                                          )on commit drop;

                insert into temporal_rutas_boletos (
                                                        desc_ruta,
                                                        nro_factura
                									)
                SELECT *
                FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                'select
                        desc_ruta,
                        nro_factura
                  from sfe.tfactura where estado_reg = ''activo''
                  and nro_factura::numeric in ('||(select
                                            list(bol.nro_boleto::varchar)
                                            from obingresos.tboleto_amadeus bol
                                            inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                            inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                            inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                            inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                            where bol.estado = 'revisado' and bol.voided = 'no'
                                            and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)||')

                  order by fecha_factura ASC, nro_factura ASC
                ')
                AS tdatos(
                desc_ruta varchar,
                nro_factura numeric)      ;
                /*******************************************************************************/


                /*Aqui insertamos los datos en la tabla temporal*/

                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        nro_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta)
                select
                        bol.fecha_emision,
                        bol.nro_boleto as nro_factura,
                        bol.nro_boleto as nro_documento,
                        ru.desc_ruta,
                        bol.pasajero,
                        (CASE
                          WHEN bolfp.id_moneda = 2

                          THEN
                            param.f_convertir_moneda(bolfp.id_moneda,v_id_moneda_base,bolfp.importe,bol.fecha_emision,'O',2,NULL,'si')
                          ELSE
                            bolfp.importe
                        END) as debe,
                        0::numeric as haber,
                        'boletos'::varchar as tipo_factura,
                        pv.nombre
                from obingresos.tboleto_amadeus bol
                inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                where bol.estado = 'revisado' and bol.voided = 'no'
                and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;

                /*Aqui para ir agrupando los puntos de ventas*/

                insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        nro_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura
                                                        )
                (select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        NULL::varchar as nro_factura,
                        NULL::varchar as nro_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        NULL::varchar as nro_factura,
                        NULL::varchar as nro_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date);


                /************************************************/

                else
                      insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              nro_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta)


                      select  ven.fecha,
                              ven.nro_factura::varchar as nro_factura,
                              ven.nro_factura::varchar as nro_documento,
                              (CASE
                                    WHEN ven.tipo_factura = 'computarizada'

                                    THEN
                                      'FACTURACION COMPUTARIZADA'
                                    WHEN ven.tipo_factura = 'manual'

                                    THEN
                                      'FACTURACION MANUAL'
                                    WHEN ven.tipo_factura = 'recibo'

                                    THEN
                                      'RECIBO OFICIAL'
                                      WHEN ven.tipo_factura = 'recibo_manual'

                                    THEN
                                      'RECIBO OFICIAL MANUAL'
                                    ELSE
                                      ''

                              END) as ruta,
                              ven.nombre_factura,

                              /*(CASE
                                    WHEN fp.id_moneda = 2

                                    THEN
                                      param.f_convertir_moneda(fp.id_moneda,v_id_moneda_base,fp.monto_mb_efectivo,ven.fecha,'O',2,NULL,'si')
                                    ELSE
                                      fp.monto_mb_efectivo

                              END) as monto_debe,*/

                              fp.monto_mb_efectivo as monto_debe,

                              0::numeric as monto_haber,
                              ven.tipo_factura,
                              pv.nombre
                      from vef.tventa ven
                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                      where ven.estado = 'finalizado' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                      and ven.id_punto_venta = v_parametros.id_punto_venta;

                      /*Aqui recuperamos la data de los boletos para recuperar la ruta de los boletos*/

                      create temp table temporal_rutas_boletos (    desc_ruta varchar,
                                                                    nro_factura numeric
                                                                )on commit drop;

                      insert into temporal_rutas_boletos (
                                                              desc_ruta,
                                                              nro_factura
                                                          )
                      SELECT *
                      FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                      'select
                              desc_ruta,
                              nro_factura
                        from sfe.tfactura where estado_reg = ''activo''
                        and nro_factura::numeric in ('||(select
                                                  list(bol.nro_boleto::varchar)
                                                  from obingresos.tboleto_amadeus bol
                                                  inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                                  inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                                  inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                                  inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                                  where bol.estado = 'revisado' and bol.voided = 'no'
                                                  and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                                                  and bol.id_punto_venta = v_parametros.id_punto_venta)||')

                        order by fecha_factura ASC, nro_factura ASC
                      ')
                      AS tdatos(
                      desc_ruta varchar,
                      nro_factura numeric)      ;
                      /*******************************************************************************/


                      /*Aqui insertamos los datos en la tabla temporal*/

                      insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              nro_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta)
                      select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              bol.nro_boleto as nro_documento,
                              ru.desc_ruta,
                              bol.pasajero,
                              (CASE
                                WHEN bolfp.id_moneda = 2

                                THEN
                                  param.f_convertir_moneda(bolfp.id_moneda,v_id_moneda_base,bolfp.importe,bol.fecha_emision,'O',2,NULL,'si')
                                ELSE
                                  bolfp.importe
                              END) as debe,
                              0::numeric as haber,
                              'boletos'::varchar as tipo_factura,
                      		  pv.nombre
                      from obingresos.tboleto_amadeus bol
                      inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                      left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                      where bol.estado = 'revisado' and bol.voided = 'no'
                      and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                      and bol.id_punto_venta = v_parametros.id_punto_venta;
                      /************************************************/


                      insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        nro_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura
                                                        )
                (select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        NULL::varchar as nro_factura,
                        NULL::varchar as nro_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        NULL::varchar as nro_factura,
                        NULL::varchar as nro_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta);


                end if;



                v_consulta := '(select
                						to_char(fecha_factura, ''DD/MM/YYYY'')::varchar as fecha_factura,
                                        nro_factura,
                                        nro_documento,
                                        ruta,
                                        pasajero,
                                        debe,
                                        haber,
                                        tipo_factura,
                                        punto_venta
            			   from facturas_recibos_temporal
                           ORDER BY punto_venta DESC,tipo_factura ASC NULLS FIRST, fecha_factura DESC )


                           union all

                          (select
                          null::varchar as fecha_factura,
                          null::varchar as nro_factura,
                          NULL::varchar as nro_documento,
                          NULL::varchar as ruta,
                          ''Total:''::varchar as pasajero,
                          SUM(debe),
                          0::numeric as haber,
                          ''total_pv''::varchar as tipo_factura,
                          punto_venta
                          from facturas_recibos_temporal
                          group by punto_venta)

                          ORDER BY punto_venta DESC,tipo_factura ASC NULLS FIRST, fecha_factura DESC, pasajero asc nulls LAST


                           ';

                return v_consulta;

            end;

            /*********************************
            #TRANSACCION:  'VEF_REP_EMI_BOL_CONT'
            #DESCRIPCION:	Conteo de registros
            #AUTOR:		admin
            #FECHA:		12-02-2021 12:04:26
            ***********************************/

            elsif(p_transaccion='VEF_REP_EMI_BOL_CONT')then

                begin
                	/*Recuperamos la moneda base para sacar la conversion*/
                select mon.id_moneda
                	   into
                       v_id_moneda_base
                from param.tmoneda mon
                where mon.tipo_moneda = 'base';
                /*****************************************************/

                create temp table facturas_recibos_temporal (
                                                                fecha_factura date,
                                                                nro_factura varchar,
                                                                nro_documento varchar,
                                                                ruta varchar,
                                                                pasajero varchar,
                                                                debe numeric,
                                                                haber numeric,
                                                                tipo_factura varchar,
                                                                punto_venta varchar
                                                              )on commit drop;
                CREATE INDEX tfacturas_recibos_temporal_fecha_factura ON facturas_recibos_temporal
                USING btree (fecha_factura);

                CREATE INDEX tfacturas_recibos_temporal_nro_factura ON facturas_recibos_temporal
                USING btree (nro_factura);

                CREATE INDEX tfacturas_recibos_temporal_tipo_factura ON facturas_recibos_temporal
                USING btree (tipo_factura);
                /*Si el punto de venta es todos no ponemos ningun filtro*/
                if(v_parametros.id_punto_venta = 0) then
                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        nro_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta)


                select  ven.fecha,
                		ven.nro_factura::varchar as nro_factura,
                        ven.nro_factura::varchar as nro_documento,
                		(CASE
                              WHEN ven.tipo_factura = 'computarizada'

                              THEN
                              	'FACTURACION COMPUTARIZADA'
                              WHEN ven.tipo_factura = 'manual'

                              THEN
                              	'FACTURACION MANUAL'
                              WHEN ven.tipo_factura = 'recibo'

                              THEN
                              	'RECIBO OFICIAL'
                                WHEN ven.tipo_factura = 'recibo_manual'

                              THEN
                              	'RECIBO OFICIAL MANUAL'
                              ELSE
                              	''

                        END) as ruta,
                        ven.nombre_factura,

                       /* (CASE
                              WHEN fp.id_moneda = 2

                              THEN
                              	param.f_convertir_moneda(fp.id_moneda,v_id_moneda_base,fp.monto_mb_efectivo,ven.fecha,'O',2,NULL,'si')
                              ELSE
                              	fp.monto_mb_efectivo

                        END) as monto_debe,*/

                        fp.monto_mb_efectivo as monto_debe,

                        0::numeric as monto_haber,
                        ven.tipo_factura,
                        pv.nombre
                from vef.tventa ven
                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                where ven.estado = 'finalizado' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date;

                /*Aqui recuperamos la data de los boletos para recuperar la ruta de los boletos*/

                create temp table temporal_rutas_boletos (    desc_ruta varchar,
                                                              nro_factura numeric
                                                          )on commit drop;

                insert into temporal_rutas_boletos (
                                                        desc_ruta,
                                                        nro_factura
                									)
                SELECT *
                FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                'select
                        desc_ruta,
                        nro_factura
                  from sfe.tfactura where estado_reg = ''activo''
                  and nro_factura::numeric in ('||(select
                                            list(bol.nro_boleto::varchar)
                                            from obingresos.tboleto_amadeus bol
                                            inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                            inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                            inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                            inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                            where bol.estado = 'revisado' and bol.voided = 'no'
                                            and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)||')

                  order by fecha_factura ASC, nro_factura ASC
                ')
                AS tdatos(
                desc_ruta varchar,
                nro_factura numeric)      ;
                /*******************************************************************************/


                /*Aqui insertamos los datos en la tabla temporal*/

                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        nro_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta)
                select
                        bol.fecha_emision,
                        bol.nro_boleto as nro_factura,
                        bol.nro_boleto as nro_documento,
                        ru.desc_ruta,
                        bol.pasajero,
                        (CASE
                          WHEN bolfp.id_moneda = 2

                          THEN
                            param.f_convertir_moneda(bolfp.id_moneda,v_id_moneda_base,bolfp.importe,bol.fecha_emision,'O',2,NULL,'si')
                          ELSE
                            bolfp.importe
                        END) as debe,
                        0::numeric as haber,
                        'boletos'::varchar as tipo_factura,
                        pv.nombre
                from obingresos.tboleto_amadeus bol
                inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                where bol.estado = 'revisado' and bol.voided = 'no'
                and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;
                /************************************************/

                else
                      insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              nro_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta)


                      select  ven.fecha,
                              ven.nro_factura::varchar as nro_factura,
                              ven.nro_factura::varchar as nro_documento,
                              (CASE
                                    WHEN ven.tipo_factura = 'computarizada'

                                    THEN
                                      'FACTURACION COMPUTARIZADA'
                                    WHEN ven.tipo_factura = 'manual'

                                    THEN
                                      'FACTURACION MANUAL'
                                    WHEN ven.tipo_factura = 'recibo'

                                    THEN
                                      'RECIBO OFICIAL'
                                      WHEN ven.tipo_factura = 'recibo_manual'

                                    THEN
                                      'RECIBO OFICIAL MANUAL'
                                    ELSE
                                      ''

                              END) as ruta,
                              ven.nombre_factura,

                              /*(CASE
                                    WHEN fp.id_moneda = 2

                                    THEN
                                      param.f_convertir_moneda(fp.id_moneda,v_id_moneda_base,fp.monto_mb_efectivo,ven.fecha,'O',2,NULL,'si')
                                    ELSE
                                      fp.monto_mb_efectivo

                              END) as monto_debe,*/

                              fp.monto_mb_efectivo as monto_debe,

                              0::numeric as monto_haber,
                              ven.tipo_factura,
                              pv.nombre
                      from vef.tventa ven
                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                      where ven.estado = 'finalizado' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                      and ven.id_punto_venta = v_parametros.id_punto_venta;

                      /*Aqui recuperamos la data de los boletos para recuperar la ruta de los boletos*/

                      create temp table temporal_rutas_boletos (    desc_ruta varchar,
                                                                    nro_factura numeric
                                                                )on commit drop;

                      insert into temporal_rutas_boletos (
                                                              desc_ruta,
                                                              nro_factura
                                                          )
                      SELECT *
                      FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                      'select
                              desc_ruta,
                              nro_factura
                        from sfe.tfactura where estado_reg = ''activo''
                        and nro_factura::numeric in ('||(select
                                                  list(bol.nro_boleto::varchar)
                                                  from obingresos.tboleto_amadeus bol
                                                  inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                                  inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                                  inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                                  inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                                  where bol.estado = 'revisado' and bol.voided = 'no'
                                                  and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                                                  and bol.id_punto_venta = v_parametros.id_punto_venta)||')

                        order by fecha_factura ASC, nro_factura ASC
                      ')
                      AS tdatos(
                      desc_ruta varchar,
                      nro_factura numeric)      ;
                      /*******************************************************************************/


                      /*Aqui insertamos los datos en la tabla temporal*/

                      insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              nro_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta)
                      select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              bol.nro_boleto as nro_documento,
                              ru.desc_ruta,
                              bol.pasajero,
                              (CASE
                                WHEN bolfp.id_moneda = 2

                                THEN
                                  param.f_convertir_moneda(bolfp.id_moneda,v_id_moneda_base,bolfp.importe,bol.fecha_emision,'O',2,NULL,'si')
                                ELSE
                                  bolfp.importe
                              END) as debe,
                              0::numeric as haber,
                              'boletos'::varchar as tipo_factura ,
                              pv.nombre
                      from obingresos.tboleto_amadeus bol
                      inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                      left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                      where bol.estado = 'revisado' and bol.voided = 'no'
                      and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                      and bol.id_punto_venta = v_parametros.id_punto_venta;
                      /************************************************/
                end if;

                    --Sentencia de la consulta de conteo de registros
                    v_consulta:='select
                                          count(tipo_factura),
                                          sum(debe) as total_debe,
                                          sum(haber) as total_haber
                                  from facturas_recibos_temporal';

                    --Definicion de la respuesta
                    --v_consulta:=v_consulta||v_parametros.filtro;

                    --Devuelve la respuesta
                    return v_consulta;

                end;

            /*********************************
            #TRANSACCION:  'VEF_REP_PUN_VEN'
            #DESCRIPCION:	Listar puntos de venta
            #AUTOR:		Ismael Valdivia
            #FECHA:		17-02-2021 12:04:26
            ***********************************/

            elsif(p_transaccion='VEF_REP_PUN_VEN')then

                begin
                      create temp table puntos_de_venta		   (  id_punto_venta integer,
                      											  nombre varchar
                                                                )on commit drop;



                    if(v_parametros.id_punto_venta = 0) then
                    insert into puntos_de_venta (
                                                              id_punto_venta,
                                                              nombre
                                                          )
                    	(select DISTINCT(ven.id_punto_venta),
                                pv.nombre
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        (select
                        distinct (bol.id_punto_venta),
                        pv.nombre
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date);

                    else
                    	insert into puntos_de_venta (
                                                              id_punto_venta,
                                                              nombre
                                                          )
                    	(select DISTINCT(ven.id_punto_venta),
                                pv.nombre
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (bol.id_punto_venta),
                        pv.nombre
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta);


                    end if;




                    --Sentencia de la consulta de conteo de registros
                    v_consulta:='select
                                          nombre
                                  from puntos_de_venta';

                    --Devuelve la respuesta
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

ALTER FUNCTION vef.ft_rep_emision_boletos (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
