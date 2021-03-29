CREATE OR REPLACE FUNCTION vef.ft_rep_resumen_detalle_cta_cte (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_rep_resumen_detalle_cta_cte
 DESCRIPCION:   Funcion para recuperar datos de una cuenta Corriente
 AUTOR: 		 (Ismael Valdivia)
 FECHA:	        29-03-2021 09:05:00
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

    v_cadena_cnx		varchar;
	v_total_general		varchar;
    v_id_moneda_base	integer;
    v_codigo			varchar;

    /*Variables de conexion*/
    v_host 				varchar;
    v_puerto 			varchar;
    v_dbname 			varchar;
    p_user 				varchar;
    v_password 			varchar;
    v_semilla			varchar;
    v_cuenta_usu		varchar;
    v_pass_usu			varchar;
    /***********************/

    /*Variables Filtros*/
    v_filtro_id_punto_venta	varchar;
    v_filtro_codigo_auxiliar varchar;

    v_insertar_facturas_recibos_temporal	varchar;
    v_recuperar_rutas_boletos	varchar;
    v_filtro_bol_id_punto_venta	varchar;
    v_nro_boletos	varchar;
    v_recuperar_boletos_amadeus	varchar;
    v_filtro_carga_codigo_auxiliar	varchar;
    v_venta_pv	varchar;
    v_fecha_final	date;

    v_codigo_auxiliar_venta	varchar;
    v_codigo_auxiliar_carga	varchar;

    v_insertar_ventas	varchar;
    v_insertar_boletos	varchar;
    v_insertar_depositos	varchar;
    v_insertar_aticipos	varchar;

BEGIN

	v_nombre_funcion = 'vef.ft_rep_resumen_detalle_cta_cte';
    v_parametros = pxp.f_get_record(p_tabla);


        /*********************************
        #TRANSACCION:  'VEF_REP_RESU_DET_SEL'
        #DESCRIPCION:	Reporte de Cta Cte Resumen y Detalle
        #AUTOR:		Ismael Valdivia
        #FECHA:		29-03-2021 09:05:00
        ***********************************/
        if(p_transaccion = 'VEF_REP_RESU_DET_SEL')then
            begin


            	/*Recuperamos la moneda base para sacar la conversion*/
                select mon.id_moneda
                	   into
                       v_id_moneda_base
                from param.tmoneda mon
                where mon.tipo_moneda = 'base';
                /*****************************************************/

                create temp table reporte_cta_cte_resu_det (
                                                                fecha_factura date,
                                                                nro_factura varchar,
                                                                nro_documento varchar,
                                                                ruta varchar,
                                                                pasajero varchar,
                                                                debe numeric,
                                                                haber numeric,
                                                                tipo_factura varchar,
                                                                punto_venta varchar,
                                                                cuenta_auxiliar varchar
                                                              )on commit drop;
                CREATE INDEX treporte_cta_cte_resu_det_fecha_factura ON reporte_cta_cte_resu_det
                USING btree (fecha_factura);

                CREATE INDEX treporte_cta_cte_resu_det_nro_factura ON reporte_cta_cte_resu_det
                USING btree (nro_factura);

                CREATE INDEX treporte_cta_cte_resu_det_tipo_factura ON reporte_cta_cte_resu_det
                USING btree (tipo_factura);

                /*Aqui recuperamos los datos de conexion*/
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
             	/************************************************************************************************************************/


                /*Aqui los filtros*/

                if (v_parametros.codigo_auxiliar != 'Todos') then
                	v_codigo_auxiliar_venta = 'aux.codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''';
                    v_codigo_auxiliar_carga = 'codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''';
                else
                	v_codigo_auxiliar_venta = '0=0';
                    v_codigo_auxiliar_carga = '0=0';
                end if;


                /*CUENTAS CORRIENTES DEBE RECIBOS FACTURAS ERP*/


                v_insertar_ventas = 'insert into reporte_cta_cte_resu_det (
                                                        fecha_factura,
                                                        nro_factura,
                                                        nro_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar)



                                      select  ven.fecha,
                                              ven.nro_factura::varchar as nro_factura,
                                              ven.nro_factura::varchar as nro_documento,
                                              (CASE
                                                    WHEN ven.tipo_factura = ''computarizada''

                                                    THEN
                                                      ''FACTURACION COMPUTARIZADA''
                                                    WHEN ven.tipo_factura = ''manual''

                                                    THEN
                                                      ''FACTURACION MANUAL''
                                                    WHEN ven.tipo_factura = ''recibo''

                                                    THEN
                                                      ''RECIBO OFICIAL''
                                                      WHEN ven.tipo_factura = ''recibo_manual''

                                                    THEN
                                                      ''RECIBO OFICIAL MANUAL''
                                                    WHEN ven.tipo_factura = ''carga''

                                                    THEN
                                                      ''FACTURACION CARGA NACIONAL COMPUTARIZADA''
                                                    ELSE
                                                      ''''

                                              END) as ruta,
                                              ven.nombre_factura,
                                              fp.monto_mb_efectivo as monto_debe,
                                              0::numeric as monto_haber,
                                              ''venta''::varchar as tipo_factura,
                                              pv.nombre,
                                              (aux.codigo_auxiliar||'' ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                      from vef.tventa ven
                                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                      where ven.estado_reg = ''activo'' and ven.estado = ''finalizado''
                                      and ven.fecha between '''||v_parametros.desde::date||''' and '''||v_parametros.hasta::date||'''
                                      and '||v_codigo_auxiliar_venta||'';

                execute v_insertar_ventas;

                /******************************************************************************************/

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
                                                  and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)||')

                        order by fecha_factura ASC, nro_factura ASC
                      ')
                      AS tdatos(
                      desc_ruta varchar,
                      nro_factura numeric);
                      /*******************************************************************************/






                v_insertar_boletos = 'insert into reporte_cta_cte_resu_det (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      nro_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar)
                                        /*CUENTA CORRIENTE BOLETOS*/
                                        select
                                                bol.fecha_emision,
                                                bol.nro_boleto as nro_factura,
                                                bol.nro_boleto as nro_documento,
                                                ru.desc_ruta,
                                                bol.pasajero,
                                                (CASE
                                                  WHEN bolfp.id_moneda = 2

                                                  THEN
                                                    param.f_convertir_moneda(bolfp.id_moneda,'||v_id_moneda_base||',bolfp.importe,bol.fecha_emision,''O'',2,NULL,''si'')
                                                  ELSE
                                                    bolfp.importe
                                                END) as debe,
                                                0::numeric as haber,
                                                ''boletos''::varchar as tipo_factura,
                                                pv.nombre,
                                                (aux.codigo_auxiliar||'' ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                        from obingresos.tboleto_amadeus bol
                                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                        left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                        where bol.estado_reg = ''activo'' and bol.estado = ''revisado'' and bol.voided = ''no''
                                        and bol.fecha_emision between '''||v_parametros.desde::date||''' and '''||v_parametros.hasta::date||'''
                                        and '||v_codigo_auxiliar_venta||'';

                execute v_insertar_boletos;

                /*********************************************************/



                /*Aqui Recuperamos los datos de Carga*/

                /*Aqui ponemos la condicion para recuperar carda antes del 19/03/2021*/
                if(v_parametros.desde::date < '19/03/2021')then

                if (v_parametros.hasta::date >= '19/03/2021') then
                  v_fecha_final = '18/03/2021';
                else
                  v_fecha_final = v_parametros.hasta::date;
                end if;

                insert into reporte_cta_cte_resu_det (
                                                        fecha_factura,
                                                        nro_factura,
                                                        nro_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar)
              (SELECT 	tdatos.fecha_factura,
                        tdatos.nro_factura,
                        tdatos.nro_documento,
                        tdatos.desc_ruta,
                        tdatos.razon_social_cli,
                        tdatos.importe_total_venta,
                        0::numeric as haber,
                        'carga'::varchar as tipo_factura,
                        pb.nombre,
                        (aux.codigo_auxiliar||' '||aux.nombre_auxiliar) as cuenta_auxiliar
                FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                'select
                        fecha_factura,
                        nro_factura,
                        nro_factura as nro_documento,
                        desc_ruta,
                        razon_social_cli,
                        importe_total_venta,
                        codigo_punto_venta,
                        codigo_auxiliar
                  from sfe.tfactura
                  where estado_reg = ''activo''
                  and codigo_auxiliar is not null
                  and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                  and sistema_origen = ''CARGA''
                  and '||v_codigo_auxiliar_carga||'
                 order by fecha_factura ASC, nro_factura ASC
                ')
                AS tdatos(
                fecha_factura date,
                nro_factura varchar,
                nro_documento varchar,
                desc_ruta varchar,
                razon_social_cli varchar,
                importe_total_venta numeric,
                codigo_punto_venta varchar,
                codigo_auxiliar_carga	varchar)
                inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                where pb.tipo = 'carga');
                /*************************************/

               end if;


               /*************************AQUI LOS ANTICIPOS HABER*********************/
               /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/

               v_insertar_depositos = 'insert into reporte_cta_cte_resu_det (
                                                    fecha_factura,
                                                    nro_factura,
                                                    nro_documento,
                                                    ruta,
                                                    pasajero,
                                                    debe,
                                                    haber,
                                                    tipo_factura,
                                                    punto_venta,
                                                    cuenta_auxiliar
                                                    )

                                        select  depo.fecha,
                                                depo.nro_deposito::varchar as nro_factura,
                                                depo.nro_deposito::varchar as nro_documento,
                                                ''DEPOSITO CUENTA CORRIENTE'' as ruta,
                                                aux.nombre_auxiliar::varchar as nombre_factura,
                                                0::numeric as monto_debe,
                                                param.f_convertir_moneda(depo.id_moneda_deposito,'||v_id_moneda_base||',depo.monto_deposito,depo.fecha,''O'',2,NULL,''si'') as monto_haber,
                                                ''deposito''::varchar as tipo_factura,
                                                NULL::varchar AS nombre,
                                                (aux.codigo_auxiliar||'' ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                        from obingresos.tdeposito depo
                                        inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                                        where depo.fecha between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                                        and '||v_codigo_auxiliar_venta||'
                                        and depo.estado_reg = ''activo''';

                execute v_insertar_depositos;
               /***************************************************************************************************/

               /*Aqui los recibos anticipo*/

               v_insertar_aticipos = 'insert into reporte_cta_cte_resu_det (
                                                        fecha_factura,
                                                        nro_factura,
                                                        nro_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar
                                                        )

                                        select  ven.fecha,
                                                ven.nro_factura::varchar as nro_factura,
                                                ven.nro_factura::varchar as nro_documento,
                                                ''ANTICIPO RECIBO'' as ruta,
                                                ven.nombre_factura,
                                                0::numeric as monto_debe,
                                                fp.monto_mb_efectivo as monto_haber,
                                                ''anticipo''::varchar as tipo_factura,
                                                pv.nombre,
                                                (aux.codigo_auxiliar||'' ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                        from vef.tventa ven
                                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                        inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                        where ven.estado_reg = ''activo'' and ven.estado = ''finalizado''
                                        and (ven.tipo_factura = ''recibo'' or ven.tipo_factura = ''recibo_manual'')
                                        and ven.id_auxiliar_anticipo is not null
                                        and ven.fecha between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                                        and '||v_codigo_auxiliar_venta||'';

               	/**********************************************************************************************************/

                execute v_insertar_aticipos;

                 v_consulta := 'select
                						fecha_factura,
                                        nro_factura::numeric as nro_factura,
                                        nro_documento,
                                        ruta,
                                        pasajero,
                                        debe,
                                        haber,
                                        tipo_factura,
                                        punto_venta,
                                        cuenta_auxiliar
            			   from reporte_cta_cte_resu_det
                           ORDER BY fecha_factura asc, punto_venta ASC NULLS FIRST';


                return v_consulta;

            end;

        /*********************************
        #TRANSACCION:  'VEF_REP_RESU_CTA_SEL'
        #DESCRIPCION:	Reporte de Cta Cte Resumen y Detalle
        #AUTOR:		Ismael Valdivia
        #FECHA:		29-03-2021 09:05:00
        ***********************************/
        elsif(p_transaccion = 'VEF_REP_RESU_CTA_SEL')then
            begin


            	/*Recuperamos la moneda base para sacar la conversion*/
                select mon.id_moneda
                	   into
                       v_id_moneda_base
                from param.tmoneda mon
                where mon.tipo_moneda = 'base';
                /*****************************************************/

                create temp table reporte_cta_cte_resu (
                                                                fecha_factura date,
                                                                nro_factura varchar,
                                                                nro_documento varchar,
                                                                ruta varchar,
                                                                pasajero varchar,
                                                                debe numeric,
                                                                haber numeric,
                                                                tipo_factura varchar,
                                                                punto_venta varchar,
                                                                cuenta_auxiliar varchar
                                                              )on commit drop;
                CREATE INDEX treporte_cta_cte_resu_fecha_factura ON reporte_cta_cte_resu
                USING btree (fecha_factura);

                CREATE INDEX treporte_cta_cte_resu_nro_factura ON reporte_cta_cte_resu
                USING btree (nro_factura);

                CREATE INDEX treporte_cta_cte_resu_tipo_factura ON reporte_cta_cte_resu
                USING btree (tipo_factura);

                /*Aqui recuperamos los datos de conexion*/
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
             	/************************************************************************************************************************/


                /*Aqui los filtros*/

                if (v_parametros.codigo_auxiliar != 'Todos') then
                	v_codigo_auxiliar_venta = 'aux.codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''';
                    v_codigo_auxiliar_carga = 'codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''';
                else
                	v_codigo_auxiliar_venta = '0=0';
                    v_codigo_auxiliar_carga = '0=0';
                end if;


                /*CUENTAS CORRIENTES DEBE RECIBOS FACTURAS ERP*/


                v_insertar_ventas = 'insert into reporte_cta_cte_resu (
                                                        fecha_factura,
                                                        nro_factura,
                                                        nro_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar)



                                      select  ven.fecha,
                                              ven.nro_factura::varchar as nro_factura,
                                              ven.nro_factura::varchar as nro_documento,
                                              (CASE
                                                    WHEN ven.tipo_factura = ''computarizada''

                                                    THEN
                                                      ''FACTURACION COMPUTARIZADA''
                                                    WHEN ven.tipo_factura = ''manual''

                                                    THEN
                                                      ''FACTURACION MANUAL''
                                                    WHEN ven.tipo_factura = ''recibo''

                                                    THEN
                                                      ''RECIBO OFICIAL''
                                                      WHEN ven.tipo_factura = ''recibo_manual''

                                                    THEN
                                                      ''RECIBO OFICIAL MANUAL''
                                                    WHEN ven.tipo_factura = ''carga''

                                                    THEN
                                                      ''FACTURACION CARGA NACIONAL COMPUTARIZADA''
                                                    ELSE
                                                      ''''

                                              END) as ruta,
                                              ven.nombre_factura,
                                              fp.monto_mb_efectivo as monto_debe,
                                              0::numeric as monto_haber,
                                              ''venta''::varchar as tipo_factura,
                                              pv.nombre,
                                              (aux.codigo_auxiliar||'' ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                      from vef.tventa ven
                                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                      where ven.estado_reg = ''activo'' and ven.estado = ''finalizado''
                                      and ven.fecha between '''||v_parametros.desde::date||''' and '''||v_parametros.hasta::date||'''
                                      and '||v_codigo_auxiliar_venta||'';

                execute v_insertar_ventas;

                /******************************************************************************************/

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
                                                  and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)||')

                        order by fecha_factura ASC, nro_factura ASC
                      ')
                      AS tdatos(
                      desc_ruta varchar,
                      nro_factura numeric);
                      /*******************************************************************************/






                v_insertar_boletos = 'insert into reporte_cta_cte_resu (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      nro_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar)
                                        /*CUENTA CORRIENTE BOLETOS*/
                                        select
                                                bol.fecha_emision,
                                                bol.nro_boleto as nro_factura,
                                                bol.nro_boleto as nro_documento,
                                                ru.desc_ruta,
                                                bol.pasajero,
                                                (CASE
                                                  WHEN bolfp.id_moneda = 2

                                                  THEN
                                                    param.f_convertir_moneda(bolfp.id_moneda,'||v_id_moneda_base||',bolfp.importe,bol.fecha_emision,''O'',2,NULL,''si'')
                                                  ELSE
                                                    bolfp.importe
                                                END) as debe,
                                                0::numeric as haber,
                                                ''boletos''::varchar as tipo_factura,
                                                pv.nombre,
                                                (aux.codigo_auxiliar||'' ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                        from obingresos.tboleto_amadeus bol
                                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                        left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                        where bol.estado_reg = ''activo'' and bol.estado = ''revisado'' and bol.voided = ''no''
                                        and bol.fecha_emision between '''||v_parametros.desde::date||''' and '''||v_parametros.hasta::date||'''
                                        and '||v_codigo_auxiliar_venta||'';

                execute v_insertar_boletos;

                /*********************************************************/



                /*Aqui Recuperamos los datos de Carga*/

                /*Aqui ponemos la condicion para recuperar carda antes del 19/03/2021*/
                if(v_parametros.desde::date < '19/03/2021')then

                if (v_parametros.hasta::date >= '19/03/2021') then
                  v_fecha_final = '18/03/2021';
                else
                  v_fecha_final = v_parametros.hasta::date;
                end if;

                insert into reporte_cta_cte_resu (
                                                        fecha_factura,
                                                        nro_factura,
                                                        nro_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar)
              (SELECT 	tdatos.fecha_factura,
                        tdatos.nro_factura,
                        tdatos.nro_documento,
                        tdatos.desc_ruta,
                        tdatos.razon_social_cli,
                        tdatos.importe_total_venta,
                        0::numeric as haber,
                        'carga'::varchar as tipo_factura,
                        pb.nombre,
                        (aux.codigo_auxiliar||' '||aux.nombre_auxiliar) as cuenta_auxiliar
                FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                'select
                        fecha_factura,
                        nro_factura,
                        nro_factura as nro_documento,
                        desc_ruta,
                        razon_social_cli,
                        importe_total_venta,
                        codigo_punto_venta,
                        codigo_auxiliar
                  from sfe.tfactura
                  where estado_reg = ''activo''
                  and codigo_auxiliar is not null
                  and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                  and sistema_origen = ''CARGA''
                  and '||v_codigo_auxiliar_carga||'
                 order by fecha_factura ASC, nro_factura ASC
                ')
                AS tdatos(
                fecha_factura date,
                nro_factura varchar,
                nro_documento varchar,
                desc_ruta varchar,
                razon_social_cli varchar,
                importe_total_venta numeric,
                codigo_punto_venta varchar,
                codigo_auxiliar_carga	varchar)
                inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                where pb.tipo = 'carga');
                /*************************************/

               end if;


               /*************************AQUI LOS ANTICIPOS HABER*********************/
               /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/

               v_insertar_depositos = 'insert into reporte_cta_cte_resu (
                                                    fecha_factura,
                                                    nro_factura,
                                                    nro_documento,
                                                    ruta,
                                                    pasajero,
                                                    debe,
                                                    haber,
                                                    tipo_factura,
                                                    punto_venta,
                                                    cuenta_auxiliar
                                                    )

                                        select  depo.fecha,
                                                depo.nro_deposito::varchar as nro_factura,
                                                depo.nro_deposito::varchar as nro_documento,
                                                ''DEPOSITO CUENTA CORRIENTE'' as ruta,
                                                aux.nombre_auxiliar::varchar as nombre_factura,
                                                0::numeric as monto_debe,
                                                param.f_convertir_moneda(depo.id_moneda_deposito,'||v_id_moneda_base||',depo.monto_deposito,depo.fecha,''O'',2,NULL,''si'') as monto_haber,
                                                ''deposito''::varchar as tipo_factura,
                                                NULL::varchar AS nombre,
                                                (aux.codigo_auxiliar||'' ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                        from obingresos.tdeposito depo
                                        inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                                        where depo.fecha between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                                        and '||v_codigo_auxiliar_venta||'
                                        and depo.estado_reg = ''activo''';

                execute v_insertar_depositos;
               /***************************************************************************************************/

               /*Aqui los recibos anticipo*/

               v_insertar_aticipos = 'insert into reporte_cta_cte_resu (
                                                        fecha_factura,
                                                        nro_factura,
                                                        nro_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar
                                                        )

                                        select  ven.fecha,
                                                ven.nro_factura::varchar as nro_factura,
                                                ven.nro_factura::varchar as nro_documento,
                                                ''ANTICIPO RECIBO'' as ruta,
                                                ven.nombre_factura,
                                                0::numeric as monto_debe,
                                                fp.monto_mb_efectivo as monto_haber,
                                                ''anticipo''::varchar as tipo_factura,
                                                pv.nombre,
                                                (aux.codigo_auxiliar||'' ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                        from vef.tventa ven
                                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                        inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                        where ven.estado_reg = ''activo'' and ven.estado = ''finalizado''
                                        and (ven.tipo_factura = ''recibo'' or ven.tipo_factura = ''recibo_manual'')
                                        and ven.id_auxiliar_anticipo is not null
                                        and ven.fecha between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                                        and '||v_codigo_auxiliar_venta||'';

               	/**********************************************************************************************************/

                execute v_insertar_aticipos;

                 v_consulta := ' (select param.f_literal_periodo((to_char(fecha_factura::date,''MM'')::integer+1))::varchar as mes,
                                         sum(debe)::numeric as debe,
                                         sum(haber)::numeric as haber,
                                         null::date as fecha_factura,
                                         NULL::varchar as nro_factura,
                                         ''gastos''::varchar as tipo
                                  from reporte_cta_cte_resu
                                  where (tipo_factura = ''venta'' or tipo_factura = ''boletos'' or tipo_factura = ''carga'')
                                  group by mes)

                                  UNION ALL

                                  (select param.f_literal_periodo((to_char(fecha_factura::date,''MM'')::integer+1))::varchar as mes,
                                         debe,
                                         haber,
                                         fecha_factura,
                                         nro_factura,
                                         ''ingresos''::varchar as tipo
                                  from reporte_cta_cte_resu
                                  where (tipo_factura = ''anticipo'' or tipo_factura = ''deposito''))
                                  order by mes ASC, fecha_factura ASC NULLS FIRST ';


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

ALTER FUNCTION vef.ft_rep_resumen_detalle_cta_cte (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
