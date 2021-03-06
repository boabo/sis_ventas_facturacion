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
    v_insertar_aticipos	varchar;
    v_codigo_auxiliar_venta	varchar;
    v_codigo_auxiliar_carga	varchar;
    v_insertar_ventas	varchar;
    v_insertar_boletos	varchar;
    v_insertar_depositos varchar;
    v_punto_venta_fac	varchar;
    v_punto_venta_bol 	varchar;
    v_punto_venta_carga	varchar;
    v_codigo_pv	varchar;
    v_punto_venta_bol_externa	varchar;
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
			--Condicional para ejecutar los diferentes reportes

            IF (v_parametros.formato_reporte != 'RESUMEN CTA/CTE TOTALIZADO') THEN

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
                                                                tipo_documento varchar,
                                                                ruta varchar,
                                                                pasajero varchar,
                                                                debe numeric,
                                                                haber numeric,
                                                                tipo_factura varchar,
                                                                punto_venta varchar,
                                                                cuenta_auxiliar varchar,
                                                                observaciones varchar,
                                                                nro_ro_pago		varchar
                                                              )on commit drop;
                CREATE INDEX tfacturas_recibos_temporal_fecha_factura ON facturas_recibos_temporal
                USING btree (fecha_factura);

                CREATE INDEX tfacturas_recibos_temporal_nro_factura ON facturas_recibos_temporal
                USING btree (nro_factura);

                CREATE INDEX tfacturas_recibos_temporal_tipo_factura ON facturas_recibos_temporal
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

                --v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();


                /*Si el punto de venta es todos no ponemos ningun filtro*/
                if(v_parametros.id_punto_venta = 0) then

-----------------/*Inicio del Reporte de Formas de pago*/
				if (v_parametros.formato_reporte = 'REPORTE FORMAS DE PAGO CTA/CTE (DEBE)') then
					if (v_parametros.codigo_auxiliar = 'Todos') then
                              insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones,
                                                                      nro_ro_pago)


                              select  ven.fecha,
                                      ven.nro_factura::varchar as nro_factura,
                                      --ven.nro_factura::varchar as nro_documento,
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
                                            WHEN ven.tipo_factura = 'carga'

                                            THEN
                                              'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                            ELSE
                                              ''

                                      END) as tipo_documento,
                                      /*(CASE
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
                                            WHEN ven.tipo_factura = 'carga'

                                            THEN
                                              'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                            ELSE
                                              ''

                                      END) as ruta*/''::varchar as ruta,
                                      ven.nombre_factura,
                                      fp.monto_mb_efectivo as monto_debe,
                                      0::numeric as monto_haber,
                                      ven.tipo_factura,
                                      pv.nombre,
                                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                      UPPER(ven.observaciones)::varchar as observaciones,
                                      (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                              from vef.tventa ven
                              inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                              inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                              inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                              inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                              inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                              where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
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
                                                          and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)||')

                                order by fecha_factura ASC, nro_factura ASC
                              ')
                              AS tdatos(
                              desc_ruta varchar,
                              nro_factura numeric);
                              /*******************************************************************************/


                              /*Aqui insertamos los datos en la tabla temporal*/

                              insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones,
                                                                      nro_ro_pago)
                              select
                                      bol.fecha_emision,
                                      bol.nro_boleto as nro_factura,
                                      --bol.nro_boleto as nro_documento,
                                      'BOLETO'::varchar as tipo_documento,
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
                                      pv.nombre,
                                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                      ''::varchar as observaciones,
                                      (select nro_factura from vef.tventa where id_venta = bolfp.id_venta) as nro_ro_pago
                              from obingresos.tboleto_amadeus bol
                              inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                              inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                              inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                              inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                              left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                              inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                              where bol.estado = 'revisado' and bol.voided = 'no'
                              and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;

                              /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                              insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones)


                              select
                                    bol.fecha_emision,
                                    bol.nro_boleto as nro_factura,
                                    'BOLETO EXTERNO'::varchar as tipo_documento,
                                    ''::varchar as ruta ,
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
                                    pv.nombre,
                                    ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                    ''::varchar as observaciones
                              from obingresos.tboleto_stage bol
                              inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                              inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                              inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                              where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                              and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;
                              /********************************************/


                              /*Aqui Recuperamos los datos de Carga*/

                              /*Aqui ponemos la condicion para recuperar carda antes del 19/03/2021*/
                              if(v_parametros.desde::date < '19/03/2021')then

                              if (v_parametros.hasta::date >= '19/03/2021') then
                              	v_fecha_final = '18/03/2021';
                              else
                              	v_fecha_final = v_parametros.hasta::date;
                              end if;

                              insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones)
                            (SELECT 	tdatos.fecha_factura,
                                      tdatos.nro_factura,
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'::varchar as tipo_documento,
                                      tdatos.desc_ruta,
                                      tdatos.razon_social_cli,
                                      tdatos.importe_total_venta,
                                      0::numeric as haber,
                                      'carga'::varchar as tipo_factura,
                                      pb.nombre,
                                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                      ''::varchar as observaciones
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


                              /*Aqui para ir agrupando los puntos de ventas*/
							if(v_parametros.desde::date < '19/03/2021')then

                            if (v_parametros.hasta::date >= '19/03/2021') then
                            v_fecha_final = '18/03/2021';
                            else
                            v_fecha_final = v_parametros.hasta::date;
                            end if;



                              insert into facturas_recibos_temporal (
                                                                      punto_venta,
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      cuenta_auxiliar,
                                                                      observaciones
                                                                      )
                              ((select
                                      DISTINCT(pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from vef.tventa ven
                                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                                      union

                                      (select
                                      distinct (pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from obingresos.tboleto_amadeus bol
                                      inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado = 'revisado' and bol.voided = 'no'
                                      and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                                      union
                                      /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                                      (select
                                      		distinct (pv.nombre),
                                            NULL::date as fecha_factura,
                                            '0'::varchar as nro_factura,
                                            NULL::varchar as tipo_documento,
                                            null::varchar as ruta,
                                            pv.nombre as pasajero,
                                            NULL::numeric as debe,
                                            NULL::numeric as haber,
                                            NULL::varchar as tipo_factura,
                                            NULL::varchar as cuenta_auxiliar,
                                            NULL::varchar as observaciones
                                      from obingresos.tboleto_stage bol
                                      inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                                      and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                                      /***************************************************************************/


                                      union

                                      (SELECT distinct (pb.nombre),
                                              NULL::date as fecha_factura,
                                              '0'::varchar nro_factura,
                                              null::varchar tipo_documento,
                                              null::varchar desc_ruta,
                                              pb.nombre,
                                              null::numeric importe_total_venta,
                                              null::numeric,
                                              NULL::varchar as tipo_factura,
                                              NULL::varchar as cuenta_auxiliar,
                                              NULL::varchar as observaciones

                                        FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                                        'select
                                                fecha_factura,
                                                nro_factura,
                                                nro_factura as nro_documento,
                                                desc_ruta,
                                                razon_social_cli,
                                                importe_total_venta,
                                                codigo_punto_venta
                                          from sfe.tfactura where estado_reg = ''activo''
                                          and codigo_auxiliar is not null
                                          and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                                          and sistema_origen = ''CARGA''
                                         order by fecha_factura ASC, nro_factura ASC
                                        ')
                                        AS tdatos(
                                        fecha_factura date,
                                        nro_factura varchar,
                                        nro_documento varchar,
                                        desc_ruta varchar,
                                        razon_social_cli varchar,
                                        importe_total_venta numeric,
                                        codigo_punto_venta varchar)
                                        inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                                        where pb.tipo = 'carga'));
                              else
                                      	insert into facturas_recibos_temporal (
                                                                      punto_venta,
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      cuenta_auxiliar,
                                                                      observaciones
                                                                      )
                              ((select
                                      DISTINCT(pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from vef.tventa ven
                                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                                      union

                                      (select
                                      distinct (pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from obingresos.tboleto_amadeus bol
                                      inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado = 'revisado' and bol.voided = 'no'
                                      and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                                      union

                                      /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                                      (select
                                      		distinct (pv.nombre),
                                            NULL::date as fecha_factura,
                                            '0'::varchar as nro_factura,
                                            NULL::varchar as tipo_documento,
                                            null::varchar as ruta,
                                            pv.nombre as pasajero,
                                            NULL::numeric as debe,
                                            NULL::numeric as haber,
                                            NULL::varchar as tipo_factura,
                                            NULL::varchar as cuenta_auxiliar,
                                            NULL::varchar as observaciones
                                      from obingresos.tboleto_stage bol
                                      inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                                      and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                                      /***************************************************************************/




                                      );
                                      end if;
              -----------------/*Fin del Reporte de Formas de pago*/
                else
                				insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones,
                                                                      nro_ro_pago)


                              select  ven.fecha,
                                      ven.nro_factura::varchar as nro_factura,
                                      --ven.nro_factura::varchar as nro_documento,
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
                                            WHEN ven.tipo_factura = 'carga'

                                            THEN
                                              'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                            ELSE
                                              ''

                                      END) as tipo_documento,
                                      /*(CASE
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
                                            WHEN ven.tipo_factura = 'carga'

                                            THEN
                                              'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                            ELSE
                                              ''

                                      END) as ruta*/ ''::varchar as ruta,
                                      ven.nombre_factura,
                                      fp.monto_mb_efectivo as monto_debe,
                                      0::numeric as monto_haber,
                                      ven.tipo_factura,
                                      pv.nombre,
                                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                      upper(ven.observaciones)::varchar as observaciones,
                                      (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                              from vef.tventa ven
                              inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                              inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                              inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                              inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                              inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                              where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
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
                              nro_factura numeric);
                              /*******************************************************************************/


                              /*Aqui insertamos los datos en la tabla temporal*/

                              insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones,
                                                                      nro_ro_pago)
                              select
                                      bol.fecha_emision,
                                      bol.nro_boleto as nro_factura,
                                      --bol.nro_boleto as nro_documento,
                                      'BOLETO'::varchar as tipo_documento,
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
                                      pv.nombre,
                                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                      ''::varchar as observaciones,
                                      (select nro_factura from vef.tventa where id_venta = bolfp.id_venta) as nro_ro_pago
                              from obingresos.tboleto_amadeus bol
                              inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                              inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                              inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                              inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                              left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                              inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                              where bol.estado = 'revisado' and bol.voided = 'no'
                              and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;

                		       /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                              insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones)


                              select
                                    bol.fecha_emision,
                                    bol.nro_boleto as nro_factura,
                                    'BOLETO EXTERNO'::varchar as tipo_documento,
                                    ''::varchar as ruta ,
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
                                    pv.nombre,
                                    ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                    ''::varchar as observaciones
                              from obingresos.tboleto_stage bol
                              inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                              inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                              inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                              where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                              and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;
                              /********************************************/



                              /*Aqui Recuperamos los datos de Carga*/

                              if(v_parametros.desde::date < '19/03/2021')then

                              if (v_parametros.hasta::date >= '19/03/2021') then
                              v_fecha_final = '18/03/2021';
                              else
                              v_fecha_final = v_parametros.hasta::date;
                              end if;


                              insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones)
                            (SELECT 	tdatos.fecha_factura,
                                      tdatos.nro_factura,
                                      --tdatos.nro_documento,
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'::varchar as tipo_documento,
                                      tdatos.desc_ruta,
                                      tdatos.razon_social_cli,
                                      tdatos.importe_total_venta,
                                      0::numeric as haber,
                                      'carga'::varchar as tipo_factura,
                                      pb.nombre,
                                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                      ''::varchar as observaciones
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
                                and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                               	and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                                and sistema_origen = ''CARGA''
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
                              codigo_auxiliar_carga varchar)
                              inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                              inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                              where pb.tipo = 'carga');

                              end if;
                              /*************************************/




                              /*Aqui para ir agrupando los puntos de ventas*/

                              if(v_parametros.desde::date < '19/03/2021')then

                              if (v_parametros.hasta::date >= '19/03/2021') then
                              v_fecha_final = '18/03/2021';
                              else
                              v_fecha_final = v_parametros.hasta::date;
                              end if;

                              insert into facturas_recibos_temporal (
                                                                      punto_venta,
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      cuenta_auxiliar,
                                                                      observaciones)
                              ((select
                                      DISTINCT(pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from vef.tventa ven
                                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                                      union

                                      (select
                                      distinct (pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from obingresos.tboleto_amadeus bol
                                      inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado = 'revisado' and bol.voided = 'no'
                                      and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                                      union

                                      /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                                      (select
                                      		distinct (pv.nombre),
                                            NULL::date as fecha_factura,
                                            '0'::varchar as nro_factura,
                                            NULL::varchar as tipo_documento,
                                            null::varchar as ruta,
                                            pv.nombre as pasajero,
                                            NULL::numeric as debe,
                                            NULL::numeric as haber,
                                            NULL::varchar as tipo_factura,
                                            NULL::varchar as cuenta_auxiliar,
                                            NULL::varchar as observaciones
                                      from obingresos.tboleto_stage bol
                                      inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                                      and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                                      /***************************************************************************/



                                      union

                                      (SELECT distinct (pb.nombre),
                                              NULL::date as fecha_factura,
                                              '0'::varchar nro_factura,
                                              null::varchar tipo_documento,
                                              null::varchar desc_ruta,
                                              pb.nombre,
                                              null::numeric importe_total_venta,
                                              null::numeric,
                                              NULL::varchar as tipo_factura,
                                              NULL::varchar as cuenta_auxiliar,
                                              NULL::varchar as observaciones

                                        FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                                        'select
                                                fecha_factura,
                                                nro_factura,
                                                nro_factura as nro_documento,
                                                desc_ruta,
                                                razon_social_cli,
                                                importe_total_venta,
                                                codigo_punto_venta
                                          from sfe.tfactura where estado_reg = ''activo''
                                          and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                                          and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                                          and sistema_origen = ''CARGA''
                                         order by fecha_factura ASC, nro_factura ASC
                                        ')
                                        AS tdatos(
                                        fecha_factura date,
                                        nro_factura varchar,
                                        nro_documento varchar,
                                        desc_ruta varchar,
                                        razon_social_cli varchar,
                                        importe_total_venta numeric,
                                        codigo_punto_venta varchar)
                                        inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                                        where pb.tipo = 'carga'));
                             else
                             		insert into facturas_recibos_temporal (
                                                                      punto_venta,
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      cuenta_auxiliar,
                                                                      observaciones)
                              ((select
                                      DISTINCT(pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from vef.tventa ven
                                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                                      union

                                      (select
                                      distinct (pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from obingresos.tboleto_amadeus bol
                                      inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado = 'revisado' and bol.voided = 'no'
                                      and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                                      union

                                       /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                                      (select
                                      		distinct (pv.nombre),
                                            NULL::date as fecha_factura,
                                            '0'::varchar as nro_factura,
                                            NULL::varchar as tipo_documento,
                                            null::varchar as ruta,
                                            pv.nombre as pasajero,
                                            NULL::numeric as debe,
                                            NULL::numeric as haber,
                                            NULL::varchar as tipo_factura,
                                            NULL::varchar as cuenta_auxiliar,
                                            NULL::varchar as observaciones
                                      from obingresos.tboleto_stage bol
                                      inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                                      and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                                      /***************************************************************************/




                                      );
                             end if;

              -----------------/*Fin del Reporte de Formas de pago*/
              	end if;
                ---fin todos cuenta corriente

----------------/*Condicion para Generar el reporte Anticipo*/

                elsif (v_parametros.formato_reporte = 'REPORTE ANTICIPO (HABER)') then

                if (v_parametros.codigo_auxiliar = 'Todos') then

                	/*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                    insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )

                    select  depo.fecha,
                            depo.nro_deposito::varchar as nro_factura,
                            --depo.nro_deposito::varchar as nro_documento,
                            'DEPOSITO CUENTA CORRIENTE' as tipo_documento,
                            '' as ruta,
                            aux.nombre_auxiliar::varchar as nombre_factura,
                            0::numeric as monto_debe,
                            param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                            'deposito'::varchar as tipo_factura,
                            'DEPOSITO'::varchar AS nombre,
                            ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                            ''::varchar as observaciones
                    from obingresos.tdeposito depo
                    inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                    and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date;
                    /************************************************************************************************/

                	insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones,
                                                        nro_ro_pago
                                                        )

                  select  ven.fecha,
                          ven.nro_factura::varchar as nro_factura,
                          --ven.nro_factura::varchar as nro_documento,
                          'ANTICIPO RECIBO' as nro_documento,
                          '' as ruta,
                          ven.nombre_factura,
                          0::numeric as monto_debe,
                          fp.monto_mb_efectivo as monto_haber,
                          ven.tipo_factura,
                          pv.nombre,
                          ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                          upper(ven.observaciones)::varchar as observaciones,
                          (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                  from vef.tventa ven
                  inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                  inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                  inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                  where ven.estado = 'finalizado'  and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                  and ven.id_auxiliar_anticipo is not null
                  and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date;

                  insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones)
                ((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                        and ven.id_auxiliar_anticipo is not null
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                /*Aumentando los depositos registrados de interfaz*/

                UNION

                (select
                      distinct ('DEPOSITO'),
                      NULL::date as fecha_factura,
                      '0'::varchar as nro_factura,
                      NULL::varchar as tipo_documento,
                      null::varchar as ruta,
                      'DEPOSITO'::VARCHAR as pasajero,
                      NULL::numeric as debe,
                      NULL::numeric as haber,
                      NULL::varchar as tipo_factura,
                      NULL::varchar as cuenta_auxiliar,
                      NULL::varchar as observaciones
                from obingresos.tdeposito depo
                inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date)
                );


                /**************************************************/

                else

                /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                insert into facturas_recibos_temporal (
                                                    fecha_factura,
                                                    nro_factura,
                                                    tipo_documento,
                                                    ruta,
                                                    pasajero,
                                                    debe,
                                                    haber,
                                                    tipo_factura,
                                                    punto_venta,
                                                    cuenta_auxiliar,
                                                    observaciones
                                                    )

                select  depo.fecha,
                        depo.nro_deposito::varchar as nro_factura,
                        'DEPOSITO CUENTA CORRIENTE' as tipo_documento,
                        '' as ruta,
                        aux.nombre_auxiliar::varchar as nombre_factura,
                        0::numeric as monto_debe,
                        param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                        'deposito'::varchar as tipo_factura,
                        'DEPOSITO'::varchar AS nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        ''::varchar as observaciones
                from obingresos.tdeposito depo
                inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                where aux.codigo_auxiliar = v_parametros.codigo_auxiliar;
                /************************************************************************************************/

                 insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones,
                                                        nro_ro_pago)

                  select  ven.fecha,
                          ven.nro_factura::varchar as nro_factura,
                          'ANTICIPO RECIBO' as tipo_documento,
                          '' as ruta,
                          ven.nombre_factura,
                          0::numeric as monto_debe,
                          fp.monto_mb_efectivo as monto_haber,
                          ven.tipo_factura,
                          pv.nombre,
                          ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                          upper(ven.observaciones)::varchar as observaciones,
                          (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                  from vef.tventa ven
                  inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                  inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                  inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                  where ven.estado = 'finalizado'  and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                  and ven.id_auxiliar_anticipo is not null
                  and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                  and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date;

                  insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones)
                ((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                        and ven.id_auxiliar_anticipo is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)


                 /*Aumentando los depositos registrados de interfaz*/

                  UNION

                  (select
                        distinct ('DEPOSITO'),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        'DEPOSITO'::VARCHAR as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                  from obingresos.tdeposito depo
                  inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                  and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                  where aux.codigo_auxiliar = v_parametros.codigo_auxiliar)
                  );


                  /**************************************************/





------------------/*FIN para Generar el reporte Anticipo*/
					end if; --Fin codigo Auxiliar todos

-----------------/*Condicion para Generar el reporte con todos los datos Anticipos y Formas de Pago*/
                elsif (v_parametros.formato_reporte = 'REPORTE CONSOLIDADO (DEBE-HABER)') then
				if (v_parametros.codigo_auxiliar = 'Todos') then

                  /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                insert into facturas_recibos_temporal (
                                                    fecha_factura,
                                                    nro_factura,
                                                    tipo_documento,
                                                    ruta,
                                                    pasajero,
                                                    debe,
                                                    haber,
                                                    tipo_factura,
                                                    punto_venta,
                                                    cuenta_auxiliar,
                                                    observaciones
                                                    )

                select  depo.fecha,
                        depo.nro_deposito::varchar as nro_factura,
                        'DEPOSITO CUENTA CORRIENTE' as tipo_documento,
                        '' as ruta,
                        aux.nombre_auxiliar::varchar as nombre_factura,
                        0::numeric as monto_debe,
                        param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                        'deposito'::varchar as tipo_factura,
                        'DEPOSITO'::varchar AS nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        ''::varchar as observaciones
                from obingresos.tdeposito depo
                inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date;
                /************************************************************************************************/



                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones,
                                                        nro_ro_pago)

                  select  ven.fecha,
                          ven.nro_factura::varchar as nro_factura,
                          'ANTICIPO RECIBO' as tipo_documento,
                          '' as ruta,
                          ven.nombre_factura,
                          0::numeric as monto_debe,
                          fp.monto_mb_efectivo as monto_haber,
                          ven.tipo_factura,
                          pv.nombre,
                          ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                          upper(ven.observaciones)::varchar as observaciones,
                          (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                  from vef.tventa ven
                  inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                  inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                  inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                  where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                  and ven.id_auxiliar_anticipo is not null
                  and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date;



               insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones,
                                                        nro_ro_pago)


                select  ven.fecha,
                		ven.nro_factura::varchar as nro_factura,
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
                              WHEN ven.tipo_factura = 'carga'
                              THEN
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                              ELSE
                              	''

                        END) as tipo_documento,
                		/*(CASE
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
                              WHEN ven.tipo_factura = 'carga'
                              THEN
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                              ELSE
                              	''

                        END) as ruta*/''::varchar as ruta,
                        ven.nombre_factura,
                        fp.monto_mb_efectivo as monto_debe,
                        0::numeric as monto_haber,
                        ven.tipo_factura,
                        pv.nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        upper(ven.observaciones)::varchar as observaciones,
                        (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                from vef.tventa ven
                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
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
                                            and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)||')

                  order by fecha_factura ASC, nro_factura ASC
                ')
                AS tdatos(
                desc_ruta varchar,
                nro_factura numeric);
                /*******************************************************************************/


                /*Aqui insertamos los datos en la tabla temporal*/

                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones,
                                                        nro_ro_pago)
                select
                        bol.fecha_emision,
                        bol.nro_boleto as nro_factura,
                        'BOLETO' as tipo_documento,
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
                        pv.nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        ''::varchar as observaciones,
                        (select nro_factura from vef.tventa where id_venta = bolfp.id_venta) as nro_ro_pago
                from obingresos.tboleto_amadeus bol
                inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                where bol.estado = 'revisado' and bol.voided = 'no'
                and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;

                /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones)


                select
                      bol.fecha_emision,
                      bol.nro_boleto as nro_factura,
                      'BOLETO EXTERNO'::varchar as tipo_documento,
                      ''::varchar as ruta ,
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
                      pv.nombre,
                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                      ''::varchar as observaciones
                from obingresos.tboleto_stage bol
                inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;
                /********************************************/




                /*Aqui Recuperamos los datos de Carga*/
                if(v_parametros.desde::date < '19/03/2021')then

                if (v_parametros.hasta::date >= '19/03/2021') then
                v_fecha_final = '18/03/2021';
                else
                v_fecha_final = v_parametros.hasta::date;
                end if;


                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones)
              (SELECT 	tdatos.fecha_factura,
                        tdatos.nro_factura,
                        'FACTURACION CARGA NACIONAL COMPUTARIZADA'::varchar as tipo_documento,
                        tdatos.desc_ruta,
                        tdatos.razon_social_cli,
                        tdatos.importe_total_venta,
                        0::numeric as haber,
                        'carga'::varchar as tipo_factura,
                        pb.nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        ''::varchar as observaciones
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
                codigo_auxiliar_carga varchar)
                inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                where pb.tipo = 'carga');
                end if;
                /*************************************/

                /*Aqui para ir agrupando los puntos de ventas*/
			if(v_parametros.desde::date < '19/03/2021')then

            if (v_parametros.hasta::date >= '19/03/2021') then
            v_fecha_final = '18/03/2021';
            else
            v_fecha_final = v_parametros.hasta::date;
            end if;

                insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                (
                	(select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                        /***************************************************************************/

                        union

                        (SELECT distinct (pb.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar nro_factura,
                                null::varchar tipo_documento,
                                null::varchar desc_ruta,
                                pb.nombre,
                                null::numeric importe_total_venta,
                                null::numeric,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
								NULL::varchar as observaciones
                          FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                          'select
                                  fecha_factura,
                                  nro_factura,
                                  nro_factura as nro_documento,
                                  desc_ruta,
                                  razon_social_cli,
                                  importe_total_venta,
                                  codigo_punto_venta
                            from sfe.tfactura where estado_reg = ''activo''
                            and codigo_auxiliar is not null
                            and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                            and sistema_origen = ''CARGA''
                           order by fecha_factura ASC, nro_factura ASC
                          ')
                          AS tdatos(
                          fecha_factura date,
                          nro_factura varchar,
                          nro_documento varchar,
                          desc_ruta varchar,
                          razon_social_cli varchar,
                          importe_total_venta numeric,
                          codigo_punto_venta varchar)
                          inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                          where pb.tipo = 'carga')

                          union

                        (select
                          DISTINCT(pv.nombre),
                          NULL::date as fecha_factura,
                          '0'::varchar as nro_factura,
                          NULL::varchar as tipo_documento,
                          null::varchar as ruta,
                          pv.nombre as pasajero,
                          NULL::numeric as debe,
                          NULL::numeric as haber,
                          NULL::varchar as tipo_factura,
                          NULL::varchar as cuenta_auxiliar,
                          NULL::varchar as observaciones
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo'  and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)


                          /*Aumentando los depositos registrados de interfaz*/

                          UNION

                          (select
                                distinct ('DEPOSITO'),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                'DEPOSITO'::VARCHAR as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date)


                          /**************************************************/



                          );

                else

                         insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                (
                	(select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                        /***************************************************************************/




                        union

                        (select
                          DISTINCT(pv.nombre),
                          NULL::date as fecha_factura,
                          '0'::varchar as nro_factura,
                          NULL::varchar as tipo_documento,
                          null::varchar as ruta,
                          pv.nombre as pasajero,
                          NULL::numeric as debe,
                          NULL::numeric as haber,
                          NULL::varchar as tipo_factura,
                          NULL::varchar as cuenta_auxiliar,
                          NULL::varchar as observaciones
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)


                          /*Aumentando los depositos registrados de interfaz*/

                          UNION

                          (select
                                distinct ('DEPOSITO'),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                'DEPOSITO'::VARCHAR as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                          /**************************************************/

                          );

                    end if;


                else

                 /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                insert into facturas_recibos_temporal (
                                                    fecha_factura,
                                                    nro_factura,
                                                    tipo_documento,
                                                    ruta,
                                                    pasajero,
                                                    debe,
                                                    haber,
                                                    tipo_factura,
                                                    punto_venta,
                                                    cuenta_auxiliar,
                                                    observaciones
                                                    )

                select  depo.fecha,
                        depo.nro_deposito::varchar as nro_factura,
                        'DEPOSITO CUENTA CORRIENTE'::varchar as tipo_documento,
                        '' as ruta,
                        aux.nombre_auxiliar::varchar as nombre_factura,
                        0::numeric as monto_debe,
                        param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                        'deposito'::varchar as tipo_factura,
                        'DEPOSITO'::varchar AS nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        NULL::varchar as observaciones
                from obingresos.tdeposito depo
                inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                where aux.codigo_auxiliar = v_parametros.codigo_auxiliar;
                /************************************************************************************************/





                 insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones,
                                                        nro_ro_pago)

                  select  ven.fecha,
                          ven.nro_factura::varchar as nro_factura,
                          'ANTICIPO RECIBO'::varchar as tipo_documento,
                          '' as ruta,
                          ven.nombre_factura,
                          0::numeric as monto_debe,
                          fp.monto_mb_efectivo as monto_haber,
                          ven.tipo_factura,
                          pv.nombre,
                          ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                          NULL::varchar as observaciones,
                          (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                  from vef.tventa ven
                  inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                  inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                  inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                  where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                  and ven.id_auxiliar_anticipo is not null
                  and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                  and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date;



               insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones,
                                                        nro_ro_pago)


                select  ven.fecha,
                		ven.nro_factura::varchar as nro_factura,
                        --ven.nro_factura::varchar as nro_documento,
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
                              WHEN ven.tipo_factura = 'carga'
                              THEN
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                              ELSE
                              	''

                        END) as tipo_documento,
                		/*(CASE
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
                              WHEN ven.tipo_factura = 'carga'
                              THEN
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                              ELSE
                              	''

                        END) as ruta*/''::varchar as ruta,
                        ven.nombre_factura,
                        fp.monto_mb_efectivo as monto_debe,
                        0::numeric as monto_haber,
                        ven.tipo_factura,
                        pv.nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        upper(ven.observaciones)::varchar as observaciones,
                        (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                from vef.tventa ven
                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
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
                nro_factura numeric);
                /*******************************************************************************/


                /*Aqui insertamos los datos en la tabla temporal*/

                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones,
                                                        nro_ro_pago)
                select
                        bol.fecha_emision,
                        bol.nro_boleto as nro_factura,
                        'BOLETO' as tipo_documento,
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
                        pv.nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        ''::varchar as observaciones,
                        (select nro_factura from vef.tventa where id_venta = bolfp.id_venta) as nro_ro_pago
                from obingresos.tboleto_amadeus bol
                inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                where bol.estado = 'revisado' and bol.voided = 'no'
                and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;



                /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones)


                select
                      bol.fecha_emision,
                      bol.nro_boleto as nro_factura,
                      'BOLETO EXTERNO'::varchar as tipo_documento,
                      ''::varchar as ruta ,
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
                      pv.nombre,
                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                      ''::varchar as observaciones
                from obingresos.tboleto_stage bol
                inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;
                /********************************************/



                /*Aqui Recuperamos los datos de Carga*/

                if(v_parametros.desde::date < '19/03/2021')then

                if (v_parametros.hasta::date >= '19/03/2021') then
                v_fecha_final = '18/03/2021';
                else
                v_fecha_final = v_parametros.hasta::date;
                end if;

                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones)
              (SELECT 	tdatos.fecha_factura,
                        tdatos.nro_factura,
                        'FACTURACION CARGA NACIONAL COMPUTARIZADA'::varchar as tipo_documento,
                        tdatos.desc_ruta,
                        tdatos.razon_social_cli,
                        tdatos.importe_total_venta,
                        0::numeric as haber,
                        'carga'::varchar as tipo_factura,
                        pb.nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        ''::varchar as observaciones
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
                  and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                  and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                  and sistema_origen = ''CARGA''
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
                codigo_auxiliar_carga varchar)
                inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                where pb.tipo = 'carga');
                end if;
                /*************************************/

                /*Aqui para ir agrupando los puntos de ventas*/
				if(v_parametros.desde::date < '19/03/2021')then

                if (v_parametros.hasta::date >= '19/03/2021') then
                v_fecha_final = '18/03/2021';
                else
                v_fecha_final = v_parametros.hasta::date;
                end if;

                insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones)
                (
                	(select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                        /***************************************************************************/

                        union

                        (SELECT distinct (pb.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar nro_factura,
                                null::varchar tipo_documento,
                                null::varchar desc_ruta,
                                pb.nombre,
                                null::numeric importe_total_venta,
                                null::numeric,
                                NULL::varchar as tipo_factura,
								NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                          FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                          'select
                                  fecha_factura,
                                  nro_factura,
                                  nro_factura as nro_documento,
                                  desc_ruta,
                                  razon_social_cli,
                                  importe_total_venta,
                                  codigo_punto_venta
                            from sfe.tfactura where estado_reg = ''activo''
                            and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                            and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                            and sistema_origen = ''CARGA''
                           order by fecha_factura ASC, nro_factura ASC
                          ')
                          AS tdatos(
                          fecha_factura date,
                          nro_factura varchar,
                          nro_documento varchar,
                          desc_ruta varchar,
                          razon_social_cli varchar,
                          importe_total_venta numeric,
                          codigo_punto_venta varchar)
                          inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                          where pb.tipo = 'carga')

                          union

                        (select
                          DISTINCT(pv.nombre),
                          NULL::date as fecha_factura,
                          '0'::varchar as nro_factura,
                          NULL::varchar as tipo_documento,
                          null::varchar as ruta,
                          pv.nombre as pasajero,
                          NULL::numeric as debe,
                          NULL::numeric as haber,
                          NULL::varchar as tipo_factura,
                          NULL::varchar as cuenta_auxiliar,
                          NULL::varchar as observaciones
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                          /*Aumentando los depositos registrados de interfaz*/

                          UNION

                          (select
                                distinct ('DEPOSITO'),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                'DEPOSITO'::VARCHAR as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          where aux.codigo_auxiliar = v_parametros.codigo_auxiliar)

                          /**************************************************/

                          );
                    else
                    	insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones)
                (
                	(select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                        /***************************************************************************/

                        union

                        (select
                          DISTINCT(pv.nombre),
                          NULL::date as fecha_factura,
                          '0'::varchar as nro_factura,
                          NULL::varchar as tipo_documento,
                          null::varchar as ruta,
                          pv.nombre as pasajero,
                          NULL::numeric as debe,
                          NULL::numeric as haber,
                          NULL::varchar as tipo_factura,
                          NULL::varchar as cuenta_auxiliar,
                          NULL::varchar as observaciones
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                          /*Aumentando los depositos registrados de interfaz*/

                          UNION

                          (select
                                distinct ('DEPOSITO'),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                'DEPOSITO'::VARCHAR as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          where aux.codigo_auxiliar = v_parametros.codigo_auxiliar)

                          /**************************************************/

                          );
                    end if;

------------------/*Fin del reporte Anticipos y Formas de Pago*/
					end if;


				end if;

                /************************************************/

                else

              	if (v_parametros.formato_reporte = 'REPORTE FORMAS DE PAGO CTA/CTE (DEBE)') then

                if (v_parametros.codigo_auxiliar = 'Todos') THEN
                	 insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones,
                                                              nro_ro_pago)


                      select  ven.fecha,
                              ven.nro_factura::varchar as nro_factura,
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END) as  tipo_documento,
                              /*(CASE
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END)*/''::varchar as ruta,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              upper(ven.observaciones)::varchar as observaciones,
                              (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                      from vef.tventa ven
                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                      and ven.id_auxiliar_anticipo is null
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
                                                  and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
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
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones,
                                                              nro_ro_pago)
                      select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO'::varchar as tipo_documento,
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
                      		  pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones,
                              (select nro_factura from vef.tventa where id_venta = bolfp.id_venta) as nro_ro_pago
                      from obingresos.tboleto_amadeus bol
                      inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                      left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                      where bol.estado = 'revisado' and bol.voided = 'no'
                      and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                      and bol.id_punto_venta = v_parametros.id_punto_venta;
                      /************************************************/


                      /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)


                        select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO EXTERNO'::varchar as tipo_documento,
                              ''::varchar as ruta ,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;
                        /********************************************/


                      /*Aqui recuperamos el id codigo punto de venta para filtrar carga*/
                      select pv.codigo
                      	     into
                             v_codigo
                      from vef.tpunto_venta pv
                      where pv.id_punto_venta = v_parametros.id_punto_venta and pv.tipo = 'carga';
                      /*****************************************************************/

                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then

                      if (v_parametros.hasta::date >= '19/03/2021') then
                      v_fecha_final = '18/03/2021';
                      else
                      v_fecha_final = v_parametros.hasta::date;
                      end if;

                       /*Aqui Recuperamos los datos de Carga*/
                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)
                      (SELECT 	tdatos.fecha_factura,
                                tdatos.nro_factura,
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA'::varchar as tipo_documento,
                                tdatos.desc_ruta,
                                tdatos.razon_social_cli,
                                tdatos.importe_total_venta,
                                0::numeric as haber,
                                'carga'::varchar as tipo_factura,
                                pb.nombre,
                                ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                ''::varchar as observaciones
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
                          and codigo_punto_venta = '''||v_codigo||'''
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
                        codigo_auxiliar_carga varchar)
                        inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                        inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                        where pb.tipo = 'carga');
                        /*************************************/
                       end if;


                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then

                      if (v_parametros.hasta::date >= '19/03/2021') then
                      v_fecha_final = '18/03/2021';
                      else
                      v_fecha_final = v_parametros.hasta::date;
                      end if;

                      insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones)
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)
                        /***************************************************************************/

                        union

                        (SELECT distinct (pb.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar nro_factura,
                                null::varchar tipo_documento,
                                null::varchar desc_ruta,
                                pb.nombre,
                                null::numeric importe_total_venta,
                                null::numeric,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
								NULL::varchar as observaciones
                          FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                          'select
                                  fecha_factura,
                                  nro_factura,
                                  nro_factura as nro_documento,
                                  desc_ruta,
                                  razon_social_cli,
                                  importe_total_venta,
                                  codigo_punto_venta
                            from sfe.tfactura where estado_reg = ''activo''
                            and codigo_auxiliar is not null
                            and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                            and codigo_punto_venta = '''||v_codigo||'''
                            and sistema_origen = ''CARGA''
                           order by fecha_factura ASC, nro_factura ASC
                          ')
                          AS tdatos(
                          fecha_factura date,
                          nro_factura varchar,
                          nro_documento varchar,
                          desc_ruta varchar,
                          razon_social_cli varchar,
                          importe_total_venta numeric,
                          codigo_punto_venta varchar)
                          inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                          where pb.tipo = 'carga'));

                        else
                        	insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones)
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_auxiliar_anticipo is null
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

						(select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)

                        );


                    end if;



                else

                      insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones,
                                                              nro_ro_pago)


                      select  ven.fecha,
                              ven.nro_factura::varchar as nro_factura,
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END)as tipo_documento,
                              /*(CASE
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END)*/''::varchar as ruta,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              upper(ven.observaciones)::varchar as observaciones,
                              (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                      from vef.tventa ven
                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                      and ven.id_auxiliar_anticipo is null
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
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones,
                                                              nro_ro_pago)
                      select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO' as tipo_documento,
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
                      		  pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones,
                              (select nro_factura from vef.tventa where id_venta = bolfp.id_venta) as nro_ro_pago
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
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)


                        select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO EXTERNO'::varchar as tipo_documento,
                              ''::varchar as ruta ,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta;
                        /********************************************/




                      /*Aqui recuperamos el id codigo punto de venta para filtrar carga*/

                      select pv.codigo
                      	     into
                             v_codigo
                      from vef.tpunto_venta pv
                      where pv.id_punto_venta = v_parametros.id_punto_venta and pv.tipo = 'carga';
                      /*****************************************************************/

                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then

                      if (v_parametros.hasta::date >= '19/03/2021') then
                      v_fecha_final = '18/03/2021';
                      else
                      v_fecha_final = v_parametros.hasta::date;
                      end if;

                       /*Aqui Recuperamos los datos de Carga*/
                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)
                      (SELECT 	tdatos.fecha_factura,
                                tdatos.nro_factura,
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA' as tipo_documento,
                                tdatos.desc_ruta,
                                tdatos.razon_social_cli,
                                tdatos.importe_total_venta,
                                0::numeric as haber,
                                'carga'::varchar as tipo_factura,
                                pb.nombre,
                                ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                ''::varchar as observaciones
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
                          and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                          and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                          and sistema_origen = ''CARGA''
                          and codigo_punto_venta = '''||v_codigo||'''
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
                        codigo_auxiliar_carga varchar)
                        inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                        inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                        where pb.tipo = 'carga');
                        /*************************************/
                      end if;

                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then
                      if (v_parametros.hasta::date >= '19/03/2021') then
                      v_fecha_final = '18/03/2021';
                      else
                      v_fecha_final = v_parametros.hasta::date;
                      end if;

                      insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as codigo_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)

                        union

                        (SELECT distinct (pb.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar nro_factura,
                                null::varchar tipo_documento,
                                null::varchar desc_ruta,
                                pb.nombre,
                                null::numeric importe_total_venta,
                                null::numeric,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
								NULL::varchar as observaciones
                          FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                          'select
                                  fecha_factura,
                                  nro_factura,
                                  nro_factura as nro_documento,
                                  desc_ruta,
                                  razon_social_cli,
                                  importe_total_venta,
                                  codigo_punto_venta
                            from sfe.tfactura where estado_reg = ''activo''
                            and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                            and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                            and codigo_punto_venta = '''||v_codigo||'''
                            and sistema_origen = ''CARGA''
                           order by fecha_factura ASC, nro_factura ASC
                          ')
                          AS tdatos(
                          fecha_factura date,
                          nro_factura varchar,
                          nro_documento varchar,
                          desc_ruta varchar,
                          razon_social_cli varchar,
                          importe_total_venta numeric,
                          codigo_punto_venta varchar)
                          inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                          where pb.tipo = 'carga'));
                        else
                        	insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_auxiliar_anticipo is null
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)



                        );
                	end if;
                end if;
-------------------------------------

                    elsif (v_parametros.formato_reporte = 'REPORTE ANTICIPO (HABER)') then
						if (v_parametros.codigo_auxiliar = 'Todos') then

                         /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                          insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones
                                                              )

                          select  depo.fecha,
                                  depo.nro_deposito::varchar as nro_factura,
                                  'DEPOSITO CUENTA CORRIENTE'::varchar as tipo_documento,
                                  '' as ruta,
                                  aux.nombre_auxiliar::varchar as nombre_factura,
                                  0::numeric as monto_debe,
                                  param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                                  'deposito'::varchar as tipo_factura,
                                  'DEPOSITO'::varchar AS nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  ''::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date;
                          /************************************************************************************************/


                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones,
                                                                nro_ro_pago)

                          select  ven.fecha,
                                  ven.nro_factura::varchar as nro_factura,
                                  'ANTICIPO RECIBO'::varchar as tipo_documento,
                                  '' as ruta,
                                  ven.nombre_factura,
                                  0::numeric as monto_debe,
                                  fp.monto_mb_efectivo as monto_haber,
                                  ven.tipo_factura,
                                  pv.nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  NULL::varchar as observaciones,
                                  (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          and ven.id_punto_venta = v_parametros.id_punto_venta;

                          insert into facturas_recibos_temporal (
                                                                punto_venta,
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                cuenta_auxiliar,
                                                                observaciones
                                                                )
                       ( (select
                                DISTINCT(pv.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                pv.nombre as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                                from vef.tventa ven
                                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                                and ven.id_auxiliar_anticipo is not null
                                and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                                and ven.id_punto_venta = v_parametros.id_punto_venta)
                        /*Aumentando para los depositos*/
                        UNION

                            (select
                                  distinct ('DEPOSITO'),
                                  NULL::date as fecha_factura,
                                  '0'::varchar as nro_factura,
                                  NULL::varchar as tipo_documento,
                                  null::varchar as ruta,
                                  'DEPOSITO'::VARCHAR as pasajero,
                                  NULL::numeric as debe,
                                  NULL::numeric as haber,
                                  NULL::varchar as tipo_factura,
                                  NULL::varchar as cuenta_auxiliar,
                                  NULL::varchar as observaciones
                            from obingresos.tdeposito depo
                            inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                            and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date)
                        /*******************************/
                         );

                        else

                        /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                          insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones
                                                              )

                          select  depo.fecha,
                                  depo.nro_deposito::varchar as nro_factura,
                                  'DEPOSITO CUENTA CORRIENTE'::varchar as tipo_documento,
                                  '' as ruta,
                                  aux.nombre_auxiliar::varchar as nombre_factura,
                                  0::numeric as monto_debe,
                                  param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                                  'deposito'::varchar as tipo_factura,
                                  'DEPOSITO'::varchar AS nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  NULL::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          where aux.codigo_auxiliar = v_parametros.codigo_auxiliar;
                          /************************************************************************************************/




                         insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones,
                                                                nro_ro_pago)

                          select  ven.fecha,
                                  ven.nro_factura::varchar as nro_factura,
                                  'ANTICIPO RECIBO'::varchar as tipo_documento,
                                  '' as ruta,
                                  ven.nombre_factura,
                                  0::numeric as monto_debe,
                                  fp.monto_mb_efectivo as monto_haber,
                                  ven.tipo_factura,
                                  pv.nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  upper(ven.observaciones)::varchar as observaciones,
                                  (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          and ven.id_punto_venta = v_parametros.id_punto_venta;

                          insert into facturas_recibos_temporal (
                                                                punto_venta,
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                cuenta_auxiliar,
                                                                observaciones)
                        ((select
                                DISTINCT(pv.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                pv.nombre as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                                from vef.tventa ven
                                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                                and ven.id_auxiliar_anticipo is not null
                                and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                                and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                                and ven.id_punto_venta = v_parametros.id_punto_venta)
                                /*Aumentando para los depositos*/
                        UNION

                            (select
                                  distinct ('DEPOSITO'),
                                  NULL::date as fecha_factura,
                                  '0'::varchar as nro_factura,
                                  NULL::varchar as tipo_documento,
                                  null::varchar as ruta,
                                  'DEPOSITO'::VARCHAR as pasajero,
                                  NULL::numeric as debe,
                                  NULL::numeric as haber,
                                  NULL::varchar as tipo_factura,
                                  NULL::varchar as cuenta_auxiliar,
                                  NULL::varchar as observaciones
                            from obingresos.tdeposito depo
                            inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                            and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                            where aux.codigo_auxiliar = v_parametros.codigo_auxiliar)
                        /*******************************/    );
					end if;
/*****************************/ elsif (v_parametros.formato_reporte = 'REPORTE CONSOLIDADO (DEBE-HABER)') then
					if (v_parametros.codigo_auxiliar = 'Todos') then

                      /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                          insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones
                                                              )

                          select  depo.fecha,
                                  depo.nro_deposito::varchar as nro_factura,
                                  'DEPOSITO CUENTA CORRIENTE'::varchar as tipo_documento,
                                  '' as ruta,
                                  aux.nombre_auxiliar::varchar as nombre_factura,
                                  0::numeric as monto_debe,
                                  param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                                  'deposito'::varchar as tipo_factura,
                                  'DEPOSITO'::varchar AS nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  ''::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date;
                          /************************************************************************************************/


                    	insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones,
                                                              nro_ro_pago)


                      select  ven.fecha,
                              ven.nro_factura::varchar as nro_factura,
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END)::varchar as tipo_documento,
                              /*(CASE
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END)*/'' as ruta,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              upper(ven.observaciones)::varchar as observaciones,
                              (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                      from vef.tventa ven
                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                      and ven.id_auxiliar_anticipo is null
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
                                                  and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
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
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones,
                                                              nro_ro_pago)
                      select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO'::varchar as tipo_documento,
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
                      		  pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones,
                              (select nro_factura from vef.tventa where id_venta = bolfp.id_venta) as nro_ro_pago
                      from obingresos.tboleto_amadeus bol
                      inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                      left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                      where bol.estado = 'revisado' and bol.voided = 'no'
                      and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                      and bol.id_punto_venta = v_parametros.id_punto_venta;
                      /************************************************/


                      /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)


                        select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO EXTERNO'::varchar as tipo_documento,
                              ''::varchar as ruta ,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta;
                        /********************************************/

                      insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones,
                                                                nro_ro_pago)

                          select  ven.fecha,
                                  ven.nro_factura::varchar as nro_factura,
                                  'ANTICIPO RECIBO'::varchar as tipo_documento,
                                  '' as ruta,
                                  ven.nombre_factura,
                                  0::numeric as monto_debe,
                                  fp.monto_mb_efectivo as monto_haber,
                                  ven.tipo_factura,
                                  pv.nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  ''::varchar as observaciones,
                                  (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          and ven.id_punto_venta = v_parametros.id_punto_venta;

                      /*Aqui recuperamos el id codigo punto de venta para filtrar carga*/

                      select pv.codigo
                      	     into
                             v_codigo
                      from vef.tpunto_venta pv
                      where pv.id_punto_venta = v_parametros.id_punto_venta and pv.tipo = 'carga';
                      /*****************************************************************/

                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then

                      if (v_parametros.hasta::date >= '19/03/2021') then
                      v_fecha_final = '18/03/2021';
                      else
                      v_fecha_final = v_parametros.hasta::date;
                      end if;

                       /*Aqui Recuperamos los datos de Carga*/
                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)
                      (SELECT 	tdatos.fecha_factura,
                                tdatos.nro_factura,
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA'::varchar as tipo_documento,
                                tdatos.desc_ruta,
                                tdatos.razon_social_cli,
                                tdatos.importe_total_venta,
                                0::numeric as haber,
                                'carga'::varchar as tipo_factura,
                                pb.nombre,
                                ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                ''::varchar as observaciones
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
                          and codigo_punto_venta = '''||v_codigo||'''
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
                        codigo_auxiliar_carga varchar)
                        inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                        inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                        where pb.tipo = 'carga');
                        /*************************************/
                      end if;

                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then

                      if (v_parametros.hasta::date >= '19/03/2021') then
                      v_fecha_final = '18/03/2021';
                      else
                      v_fecha_final = v_parametros.hasta::date;
                      end if;


                      insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)

                        union

                        (SELECT distinct (pb.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar nro_factura,
                                null::varchar tipo_documento,
                                null::varchar desc_ruta,
                                pb.nombre,
                                null::numeric importe_total_venta,
                                null::numeric,
                                NULL::varchar as tipo_factura,
								NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                          FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                          'select
                                  fecha_factura,
                                  nro_factura,
                                  nro_factura as nro_documento,
                                  desc_ruta,
                                  razon_social_cli,
                                  importe_total_venta,
                                  codigo_punto_venta
                            from sfe.tfactura where estado_reg = ''activo''
                            and codigo_auxiliar is not null
                            and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                            and codigo_punto_venta = '''||v_codigo||'''
                            and sistema_origen = ''CARGA''
                           order by fecha_factura ASC, nro_factura ASC
                          ')
                          AS tdatos(
                          fecha_factura date,
                          nro_factura varchar,
                          nro_documento varchar,
                          desc_ruta varchar,
                          razon_social_cli varchar,
                          importe_total_venta numeric,
                          codigo_punto_venta varchar)
                          inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                          where pb.tipo = 'carga')

                          union

                        (select
                                DISTINCT(pv.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                pv.nombre as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                                from vef.tventa ven
                                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                                and ven.id_auxiliar_anticipo is not null
                                and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                                and ven.id_punto_venta = v_parametros.id_punto_venta)

                        /*Aumentando para los depositos*/
                        UNION

                            (select
                                  distinct ('DEPOSITO'),
                                  NULL::date as fecha_factura,
                                  '0'::varchar as nro_factura,
                                  NULL::varchar as tipo_documento,
                                  null::varchar as ruta,
                                  'DEPOSITO'::VARCHAR as pasajero,
                                  NULL::numeric as debe,
                                  NULL::numeric as haber,
                                  NULL::varchar as tipo_factura,
                                  NULL::varchar as cuenta_auxiliar,
                                  NULL::varchar as observaciones
                            from obingresos.tdeposito depo
                            inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                            and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date)
                        /*******************************/

                        );

                      else
                        	insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_auxiliar_anticipo is null
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)

                        union

                        (select
                                DISTINCT(pv.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                pv.nombre as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                                from vef.tventa ven
                                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                                and ven.id_auxiliar_anticipo is not null
                                and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                                and ven.id_punto_venta = v_parametros.id_punto_venta)

                        /*Aumentando para los depositos*/
                        UNION

                            (select
                                  distinct ('DEPOSITO'),
                                  NULL::date as fecha_factura,
                                  '0'::varchar as nro_factura,
                                  NULL::varchar as tipo_documento,
                                  null::varchar as ruta,
                                  'DEPOSITO'::VARCHAR as pasajero,
                                  NULL::numeric as debe,
                                  NULL::numeric as haber,
                                  NULL::varchar as tipo_factura,
                                  NULL::varchar as cuenta_auxiliar,
                                  NULL::varchar as observaciones
                            from obingresos.tdeposito depo
                            inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                            and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date)
                        /*******************************/

                        );


                 	end if;

                    else

                    /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                          insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones
                                                              )

                          select  depo.fecha,
                                  depo.nro_deposito::varchar as nro_factura,
                                  'DEPOSITO CUENTA CORRIENTE'::varchar as tipo_documento,
                                  '' as ruta,
                                  aux.nombre_auxiliar::varchar as nombre_factura,
                                  0::numeric as monto_debe,
                                  param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                                  'deposito'::varchar as tipo_factura,
                                  'DEPOSITO'::varchar AS nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  ''::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          where aux.codigo_auxiliar = v_parametros.codigo_auxiliar;
                          /************************************************************************************************/


                      insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones,
                                                              nro_ro_pago)


                      select  ven.fecha,
                              ven.nro_factura::varchar as nro_factura,
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END)::varchar as tipo_documento,
                              /*(CASE
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END)*/'' as ruta,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              upper(ven.observaciones)::varchar as observaciones,
                              (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                      from vef.tventa ven
                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                      and ven.id_auxiliar_anticipo is null
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
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones,
                                                              nro_ro_pago)
                      select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO'::varchar as tipo_documento,
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
                      		  pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones,
                              (select nro_factura from vef.tventa where id_venta = bolfp.id_venta) as nro_ro_pago
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


                      /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)


                        select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO EXTERNO'::varchar as tipo_documento,
                              ''::varchar as ruta ,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta;
                        /********************************************/

                      insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones,
                                                                nro_ro_pago)

                          select  ven.fecha,
                                  ven.nro_factura::varchar as nro_factura,
                                  'ANTICIPO RECIBO'::varchar as tipo_documento,
                                  '' as ruta,
                                  ven.nombre_factura,
                                  0::numeric as monto_debe,
                                  fp.monto_mb_efectivo as monto_haber,
                                  ven.tipo_factura,
                                  pv.nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  upper(ven.observaciones)::varchar as observaciones,
                                  (select nro_factura from vef.tventa where id_venta = fp.id_venta_recibo) as nro_ro_pago
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          and ven.id_punto_venta = v_parametros.id_punto_venta;

                      /*Aqui recuperamos el id codigo punto de venta para filtrar carga*/

                      select pv.codigo
                      	     into
                             v_codigo
                      from vef.tpunto_venta pv
                      where pv.id_punto_venta = v_parametros.id_punto_venta and pv.tipo = 'carga';
                      /*****************************************************************/

                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then
						if (v_parametros.hasta::date >= '19/03/2021') then
                        v_fecha_final = '18/03/2021';
                        else
                        v_fecha_final = v_parametros.hasta::date;
                        end if;

                       /*Aqui Recuperamos los datos de Carga*/
                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)
                      (SELECT 	tdatos.fecha_factura,
                                tdatos.nro_factura,
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA'::varchar as tipo_documento,
                                tdatos.desc_ruta,
                                tdatos.razon_social_cli,
                                tdatos.importe_total_venta,
                                0::numeric as haber,
                                'carga'::varchar as tipo_factura,
                                pb.nombre,
                                ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                ''::varchar as observaciones
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
                          and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                          and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                          and sistema_origen = ''CARGA''
                          and codigo_punto_venta = '''||v_codigo||'''
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
                        codigo_auxiliar_carga varchar)
                        inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                        inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                        where pb.tipo = 'carga');
                        /*************************************/
                      end if;

                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then

                      if (v_parametros.hasta::date >= '19/03/2021') then
                      v_fecha_final = '18/03/2021';
                      else
                      v_fecha_final = v_parametros.hasta::date;
                      end if;



                      insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)

                        union

                        (SELECT distinct (pb.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar nro_factura,
                                null::varchar tipo_documento,
                                null::varchar desc_ruta,
                                pb.nombre,
                                null::numeric importe_total_venta,
                                null::numeric,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
								NULL::varchar as observaciones
                          FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                          'select
                                  fecha_factura,
                                  nro_factura,
                                  nro_factura as nro_documento,
                                  desc_ruta,
                                  razon_social_cli,
                                  importe_total_venta,
                                  codigo_punto_venta
                            from sfe.tfactura where estado_reg = ''activo''
                            and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                            and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                            and codigo_punto_venta = '''||v_codigo||'''
                            and sistema_origen = ''CARGA''
                           order by fecha_factura ASC, nro_factura ASC
                          ')
                          AS tdatos(
                          fecha_factura date,
                          nro_factura varchar,
                          nro_documento varchar,
                          desc_ruta varchar,
                          razon_social_cli varchar,
                          importe_total_venta numeric,
                          codigo_punto_venta varchar)
                          inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                          where pb.tipo = 'carga')

                          union

                        (select
                                DISTINCT(pv.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                pv.nombre as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                                from vef.tventa ven
                                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                                and ven.id_auxiliar_anticipo is not null
                                and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                                and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                                and ven.id_punto_venta = v_parametros.id_punto_venta)

                                /*Aumentando para los depositos*/
                        UNION

                            (select
                                  distinct ('DEPOSITO'),
                                  NULL::date as fecha_factura,
                                  '0'::varchar as nro_factura,
                                  NULL::varchar as tipo_documento,
                                  null::varchar as ruta,
                                  'DEPOSITO'::VARCHAR as pasajero,
                                  NULL::numeric as debe,
                                  NULL::numeric as haber,
                                  NULL::varchar as tipo_factura,
                                  NULL::varchar as cuenta_auxiliar,
                                  NULL::varchar as observaciones
                            from obingresos.tdeposito depo
                            inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                            and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                            where aux.codigo_auxiliar = v_parametros.codigo_auxiliar)
                        /*******************************/
                        );

                      else
                        	insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_auxiliar_anticipo is null
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)

                        union

                        (select
                                DISTINCT(pv.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                pv.nombre as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                                from vef.tventa ven
                                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                                and ven.id_auxiliar_anticipo is not null
                                and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                                and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                                and ven.id_punto_venta = v_parametros.id_punto_venta)

                        /*Aumentando para los depositos*/
                        UNION

                            (select
                                  distinct ('DEPOSITO'),
                                  NULL::date as fecha_factura,
                                  '0'::varchar as nro_factura,
                                  NULL::varchar as tipo_documento,
                                  null::varchar as ruta,
                                  'DEPOSITOS'::VARCHAR as pasajero,
                                  NULL::numeric as debe,
                                  NULL::numeric as haber,
                                  NULL::varchar as tipo_factura,
                                  NULL::varchar as cuenta_auxiliar,
                                  NULL::varchar as observaciones
                            from obingresos.tdeposito depo
                            inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                            and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                            where aux.codigo_auxiliar = v_parametros.codigo_auxiliar)
                        /*******************************/

                        );


                 	end if;

				  END IF;

                  end if;

                end if;



                v_consulta := '(select
                						fecha_factura,
                                        nro_factura::numeric as nro_factura,
                                        tipo_documento,
                                        ruta,
                                        pasajero,
                                        debe,
                                        haber,
                                        tipo_factura,
                                        punto_venta,
                                        cuenta_auxiliar,
                                        observaciones,
                                        nro_ro_pago
            			   from facturas_recibos_temporal
                           ORDER BY punto_venta DESC,tipo_factura ASC NULLS FIRST, fecha_factura DESC )


                           union all

                          (select
                          now()::date as fecha_factura,
                          null::numeric as nro_factura,
                          NULL::varchar as tipo_documento,
                          NULL::varchar as ruta,
                          ''TOTALES (''||punto_venta||''):''::varchar as pasajero,
                          SUM(COALESCE(debe,0)),
                          SUM(COALESCE (haber,0)),
                          ''total_pv''::varchar as tipo_factura,
                          punto_venta,
                          NULL::varchar as cuenta_auxiliar,
                          NULL::varchar as observaciones,
                          NULL::varchar as nro_ro_pago
                          from facturas_recibos_temporal
                          group by punto_venta)

						  ORDER BY punto_venta ASC,fecha_factura ASC NULLS FIRST,nro_factura asc NULLS LAST



                           ';

				if (v_parametros.generar_reporte = 'no') then
                	v_consulta:=v_consulta||' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
				end if;

                return v_consulta;
/***************/ELSE

            	/*Recuperamos la moneda base para sacar la conversion*/
                select mon.id_moneda
                	   into
                       v_id_moneda_base
                from param.tmoneda mon
                where mon.tipo_moneda = 'base';
                /*****************************************************/

                create temp table reporte_resumen_totalizado_cta_cte (
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
                CREATE INDEX treporte_resumen_totalizado_cta_cte_fecha_factura ON reporte_resumen_totalizado_cta_cte
                USING btree (fecha_factura);

                CREATE INDEX treporte_resumen_totalizado_cta_cte_nro_factura ON reporte_resumen_totalizado_cta_cte
                USING btree (nro_factura);

                CREATE INDEX treporte_resumen_totalizado_cta_cte_tipo_factura ON reporte_resumen_totalizado_cta_cte
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

                /*Aqui filtro del punto de venta*/

                if (v_parametros.id_punto_venta != 0) then
                	v_punto_venta_fac = 'ven.id_punto_venta = '||v_parametros.id_punto_venta;
                    v_punto_venta_bol = 'bol.id_punto_venta = '||v_parametros.id_punto_venta;
                    v_punto_venta_bol_externa = 'bol.id_agencia = '||v_parametros.id_punto_venta;
                /*Para Recuperar Datos de tfactura para carga*/

                	select pv.codigo
                    	    into
                            v_codigo_pv
                    from vef.tpunto_venta pv
                    where pv.id_punto_venta = v_parametros.id_punto_venta and
                    pv.tipo = 'carga';

                    v_punto_venta_carga = 'codigo_punto_venta = '''||v_codigo_pv||'''';


                else
                	v_punto_venta_fac = '0=0';
                    v_punto_venta_bol = '0=0';
                    v_punto_venta_carga = '0=0';
                    v_punto_venta_bol_externa = '0=0';

                end if;


                /********************************/


                /*CUENTAS CORRIENTES DEBE RECIBOS FACTURAS ERP*/


                v_insertar_ventas = 'insert into reporte_resumen_totalizado_cta_cte (
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
                                              (''(''||aux.codigo_auxiliar||'') - ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                      from vef.tventa ven
                                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                      where ven.estado_reg = ''activo'' and ven.estado = ''finalizado''
                                      and ven.fecha between '''||v_parametros.desde::date||''' and '''||v_parametros.hasta::date||'''
                                      and '||v_codigo_auxiliar_venta||' and '||v_punto_venta_fac||'';

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


                v_insertar_boletos = 'insert into reporte_resumen_totalizado_cta_cte (
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
                                       ( (select
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
                                                (''(''||aux.codigo_auxiliar||'') - ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                        from obingresos.tboleto_amadeus bol
                                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                        left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                        where bol.estado_reg = ''activo'' and bol.estado = ''revisado'' and bol.voided = ''no''
                                        and bol.fecha_emision between '''||v_parametros.desde::date||''' and '''||v_parametros.hasta::date||'''
                                        and '||v_codigo_auxiliar_venta||' and '||v_punto_venta_bol||')

                                        UNION

                                        (select
                                                bol.fecha_emision,
                                                bol.nro_boleto as nro_factura,
                                                bol.nro_boleto as nro_documento,
                                                ''''::varchar as ruta,
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
                                                (''(''||aux.codigo_auxiliar||'') - ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                          from obingresos.tboleto_stage bol
                                          inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                                          inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                          inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                          where bol.estado_reg = ''activo'' and bol.voided = ''no'' and bolfp.id_auxiliar is not null
                                          and bol.fecha_emision between '''||v_parametros.desde::date||''' and '''||v_parametros.hasta::date||'''
                                          and '||v_codigo_auxiliar_venta||' and '||v_punto_venta_bol_externa||')

                                        )';


				RAISE NOTICE 'aQUI LLEGA %',v_insertar_boletos;

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

                insert into reporte_resumen_totalizado_cta_cte (
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
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar
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
                  and '||v_punto_venta_carga||'
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

               v_insertar_depositos = 'insert into reporte_resumen_totalizado_cta_cte (
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
                                                ''DEPÓSITO''::varchar AS nombre,
                                                (''(''||aux.codigo_auxiliar||'') - ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                        from obingresos.tdeposito depo
                                        inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                                        where depo.fecha between '''||v_parametros.desde::date||''' and '''||v_parametros.hasta||'''
                                        and '||v_codigo_auxiliar_venta||'
                                        and depo.estado_reg = ''activo''';

                execute v_insertar_depositos;
               /***************************************************************************************************/
               /*Aqui los recibos anticipo*/

               v_insertar_aticipos = 'insert into reporte_resumen_totalizado_cta_cte (
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
                                                (''(''||aux.codigo_auxiliar||'') - ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                        from vef.tventa ven
                                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                        inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                        where ven.estado_reg = ''activo'' and ven.estado = ''finalizado''
                                        and (ven.tipo_factura = ''recibo'' or ven.tipo_factura = ''recibo_manual'')
                                        and ven.id_auxiliar_anticipo is not null
                                        and ven.fecha between '''||v_parametros.desde::date||''' and '''||v_parametros.hasta||'''
                                        and '||v_codigo_auxiliar_venta||'
                                        and '||v_punto_venta_fac||'';

               	/**********************************************************************************************************/

                execute v_insertar_aticipos;

                if (v_parametros.id_punto_venta = 0) then

                    v_consulta := 'select
                                          null::DATE AS fecha_factura,
                                          null::numeric as nro_factura,
                                          null::VARCHAR  AS nro_documento,
                                          NULL::varchar as ruta,
                                          NULL::varchar as pasajero,
                                          sum (debe) as total_debe,
                                          sum (haber) as total_haber,
                                          ''totalizado''::VARCHAR as tipo_factura,
                                          NULL::varchar as punto_venta,
                                          cuenta_auxiliar,
                                          NULL::varchar as observaciones,
                                          NULL::varchar as nro_ro_pago
                                    from reporte_resumen_totalizado_cta_cte
                                    group by cuenta_auxiliar';

                else
                	v_consulta := 'select
                                          null::DATE AS fecha_factura,
                                          null::numeric as nro_factura,
                                          null::VARCHAR  AS nro_documento,
                                          NULL::varchar as ruta,
                                          NULL::varchar as pasajero,
                                          sum (debe) as total_debe,
                                          sum (haber) as total_haber,
                                          ''totalizado''::VARCHAR as tipo_factura,
                                          punto_venta,
                                          cuenta_auxiliar,
                                          NULL::varchar as observaciones,
                                          NULL::varchar as nro_ro_pago
                                    from reporte_resumen_totalizado_cta_cte
                                    group by cuenta_auxiliar,punto_venta';

                end if;




                 if (v_parametros.generar_reporte = 'no') then
                	v_consulta:=v_consulta||' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
				end if;

                 raise notice '%',v_consulta;
                return v_consulta;

            END IF;



            end;

            /*********************************
            #TRANSACCION:  'VEF_REP_EMI_BOL_CONT'
            #DESCRIPCION:	Conteo de registros
            #AUTOR:		admin
            #FECHA:		12-02-2021 12:04:26
            ***********************************/

            elsif(p_transaccion='VEF_REP_EMI_BOL_CONT')then

                begin

                IF (v_parametros.formato_reporte != 'RESUMEN CTA/CTE TOTALIZADO') THEN

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
                                                                tipo_documento varchar,
                                                                ruta varchar,
                                                                pasajero varchar,
                                                                debe numeric,
                                                                haber numeric,
                                                                tipo_factura varchar,
                                                                punto_venta varchar,
                                                                cuenta_auxiliar varchar,
                                                                observaciones varchar
                                                              )on commit drop;
                CREATE INDEX tfacturas_recibos_temporal_fecha_factura ON facturas_recibos_temporal
                USING btree (fecha_factura);

                CREATE INDEX tfacturas_recibos_temporal_nro_factura ON facturas_recibos_temporal
                USING btree (nro_factura);

                CREATE INDEX tfacturas_recibos_temporal_tipo_factura ON facturas_recibos_temporal
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

                --v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();


                /*Si el punto de venta es todos no ponemos ningun filtro*/
                if(v_parametros.id_punto_venta = 0) then

-----------------/*Inicio del Reporte de Formas de pago*/
				if (v_parametros.formato_reporte = 'REPORTE FORMAS DE PAGO CTA/CTE (DEBE)') then
					if (v_parametros.codigo_auxiliar = 'Todos') then
                              insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones)


                              select  ven.fecha,
                                      ven.nro_factura::varchar as nro_factura,
                                      --ven.nro_factura::varchar as nro_documento,
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
                                            WHEN ven.tipo_factura = 'carga'

                                            THEN
                                              'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                            ELSE
                                              ''

                                      END) as tipo_documento,
                                      /*(CASE
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
                                            WHEN ven.tipo_factura = 'carga'

                                            THEN
                                              'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                            ELSE
                                              ''

                                      END) as ruta*/''::varchar as ruta,
                                      ven.nombre_factura,
                                      fp.monto_mb_efectivo as monto_debe,
                                      0::numeric as monto_haber,
                                      ven.tipo_factura,
                                      pv.nombre,
                                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                      UPPER(ven.observaciones)::varchar as observaciones
                              from vef.tventa ven
                              inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                              inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                              inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                              inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                              inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                              where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
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
                                                          and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)||')

                                order by fecha_factura ASC, nro_factura ASC
                              ')
                              AS tdatos(
                              desc_ruta varchar,
                              nro_factura numeric);
                              /*******************************************************************************/


                              /*Aqui insertamos los datos en la tabla temporal*/

                              insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones)
                              select
                                      bol.fecha_emision,
                                      bol.nro_boleto as nro_factura,
                                      --bol.nro_boleto as nro_documento,
                                      'BOLETO'::varchar as tipo_documento,
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
                                      pv.nombre,
                                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                      ''::varchar as observaciones
                              from obingresos.tboleto_amadeus bol
                              inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                              inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                              inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                              inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                              left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                              inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                              where bol.estado = 'revisado' and bol.voided = 'no'
                              and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;

                              /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                              insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones)


                              select
                                    bol.fecha_emision,
                                    bol.nro_boleto as nro_factura,
                                    'BOLETO EXTERNO'::varchar as tipo_documento,
                                    ''::varchar as ruta ,
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
                                    pv.nombre,
                                    ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                    ''::varchar as observaciones
                              from obingresos.tboleto_stage bol
                              inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                              inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                              inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                              where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                              and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;
                              /********************************************/


                              /*Aqui Recuperamos los datos de Carga*/

                              /*Aqui ponemos la condicion para recuperar carda antes del 19/03/2021*/
                              if(v_parametros.desde::date < '19/03/2021')then

                              if (v_parametros.hasta::date >= '19/03/2021') then
                              	v_fecha_final = '18/03/2021';
                              else
                              	v_fecha_final = v_parametros.hasta::date;
                              end if;

                              insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones)
                            (SELECT 	tdatos.fecha_factura,
                                      tdatos.nro_factura,
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'::varchar as tipo_documento,
                                      tdatos.desc_ruta,
                                      tdatos.razon_social_cli,
                                      tdatos.importe_total_venta,
                                      0::numeric as haber,
                                      'carga'::varchar as tipo_factura,
                                      pb.nombre,
                                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                      ''::varchar as observaciones
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


                              /*Aqui para ir agrupando los puntos de ventas*/
							if(v_parametros.desde::date < '19/03/2021')then

                            if (v_parametros.hasta::date >= '19/03/2021') then
                            v_fecha_final = '18/03/2021';
                            else
                            v_fecha_final = v_parametros.hasta::date;
                            end if;



                              insert into facturas_recibos_temporal (
                                                                      punto_venta,
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      cuenta_auxiliar,
                                                                      observaciones
                                                                      )
                              ((select
                                      DISTINCT(pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from vef.tventa ven
                                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                                      union

                                      (select
                                      distinct (pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from obingresos.tboleto_amadeus bol
                                      inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado = 'revisado' and bol.voided = 'no'
                                      and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                                      union
                                      /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                                      (select
                                      		distinct (pv.nombre),
                                            NULL::date as fecha_factura,
                                            '0'::varchar as nro_factura,
                                            NULL::varchar as tipo_documento,
                                            null::varchar as ruta,
                                            pv.nombre as pasajero,
                                            NULL::numeric as debe,
                                            NULL::numeric as haber,
                                            NULL::varchar as tipo_factura,
                                            NULL::varchar as cuenta_auxiliar,
                                            NULL::varchar as observaciones
                                      from obingresos.tboleto_stage bol
                                      inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                                      and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                                      /***************************************************************************/


                                      union

                                      (SELECT distinct (pb.nombre),
                                              NULL::date as fecha_factura,
                                              '0'::varchar nro_factura,
                                              null::varchar tipo_documento,
                                              null::varchar desc_ruta,
                                              pb.nombre,
                                              null::numeric importe_total_venta,
                                              null::numeric,
                                              NULL::varchar as tipo_factura,
                                              NULL::varchar as cuenta_auxiliar,
                                              NULL::varchar as observaciones

                                        FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                                        'select
                                                fecha_factura,
                                                nro_factura,
                                                nro_factura as nro_documento,
                                                desc_ruta,
                                                razon_social_cli,
                                                importe_total_venta,
                                                codigo_punto_venta
                                          from sfe.tfactura where estado_reg = ''activo''
                                          and codigo_auxiliar is not null
                                          and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                                          and sistema_origen = ''CARGA''
                                         order by fecha_factura ASC, nro_factura ASC
                                        ')
                                        AS tdatos(
                                        fecha_factura date,
                                        nro_factura varchar,
                                        nro_documento varchar,
                                        desc_ruta varchar,
                                        razon_social_cli varchar,
                                        importe_total_venta numeric,
                                        codigo_punto_venta varchar)
                                        inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                                        where pb.tipo = 'carga'));
                              else
                                      	insert into facturas_recibos_temporal (
                                                                      punto_venta,
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      cuenta_auxiliar,
                                                                      observaciones
                                                                      )
                              ((select
                                      DISTINCT(pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from vef.tventa ven
                                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                                      union

                                      (select
                                      distinct (pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from obingresos.tboleto_amadeus bol
                                      inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado = 'revisado' and bol.voided = 'no'
                                      and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                                      union

                                      /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                                      (select
                                      		distinct (pv.nombre),
                                            NULL::date as fecha_factura,
                                            '0'::varchar as nro_factura,
                                            NULL::varchar as tipo_documento,
                                            null::varchar as ruta,
                                            pv.nombre as pasajero,
                                            NULL::numeric as debe,
                                            NULL::numeric as haber,
                                            NULL::varchar as tipo_factura,
                                            NULL::varchar as cuenta_auxiliar,
                                            NULL::varchar as observaciones
                                      from obingresos.tboleto_stage bol
                                      inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                                      and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                                      /***************************************************************************/




                                      );
                                      end if;
              -----------------/*Fin del Reporte de Formas de pago*/
                else
                				insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones)


                              select  ven.fecha,
                                      ven.nro_factura::varchar as nro_factura,
                                      --ven.nro_factura::varchar as nro_documento,
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
                                            WHEN ven.tipo_factura = 'carga'

                                            THEN
                                              'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                            ELSE
                                              ''

                                      END) as tipo_documento,
                                      /*(CASE
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
                                            WHEN ven.tipo_factura = 'carga'

                                            THEN
                                              'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                            ELSE
                                              ''

                                      END) as ruta*/ ''::varchar as ruta,
                                      ven.nombre_factura,
                                      fp.monto_mb_efectivo as monto_debe,
                                      0::numeric as monto_haber,
                                      ven.tipo_factura,
                                      pv.nombre,
                                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                      upper(ven.observaciones)::varchar as observaciones
                              from vef.tventa ven
                              inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                              inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                              inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                              inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                              inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                              where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
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
                              nro_factura numeric);
                              /*******************************************************************************/


                              /*Aqui insertamos los datos en la tabla temporal*/

                              insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones)
                              select
                                      bol.fecha_emision,
                                      bol.nro_boleto as nro_factura,
                                      --bol.nro_boleto as nro_documento,
                                      'BOLETO'::varchar as tipo_documento,
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
                                      pv.nombre,
                                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                      ''::varchar as observaciones
                              from obingresos.tboleto_amadeus bol
                              inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                              inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                              inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                              inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                              left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                              inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                              where bol.estado = 'revisado' and bol.voided = 'no'
                              and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;

                		       /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                              insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones)


                              select
                                    bol.fecha_emision,
                                    bol.nro_boleto as nro_factura,
                                    'BOLETO EXTERNO'::varchar as tipo_documento,
                                    ''::varchar as ruta ,
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
                                    pv.nombre,
                                    ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                    ''::varchar as observaciones
                              from obingresos.tboleto_stage bol
                              inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                              inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                              inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                              where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                              and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;
                              /********************************************/



                              /*Aqui Recuperamos los datos de Carga*/

                              if(v_parametros.desde::date < '19/03/2021')then

                              if (v_parametros.hasta::date >= '19/03/2021') then
                              v_fecha_final = '18/03/2021';
                              else
                              v_fecha_final = v_parametros.hasta::date;
                              end if;


                              insert into facturas_recibos_temporal (
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      punto_venta,
                                                                      cuenta_auxiliar,
                                                                      observaciones)
                            (SELECT 	tdatos.fecha_factura,
                                      tdatos.nro_factura,
                                      --tdatos.nro_documento,
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'::varchar as tipo_documento,
                                      tdatos.desc_ruta,
                                      tdatos.razon_social_cli,
                                      tdatos.importe_total_venta,
                                      0::numeric as haber,
                                      'carga'::varchar as tipo_factura,
                                      pb.nombre,
                                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                      ''::varchar as observaciones
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
                                and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                               	and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                                and sistema_origen = ''CARGA''
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
                              codigo_auxiliar_carga varchar)
                              inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                              inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                              where pb.tipo = 'carga');

                              end if;
                              /*************************************/




                              /*Aqui para ir agrupando los puntos de ventas*/

                              if(v_parametros.desde::date < '19/03/2021')then

                              if (v_parametros.hasta::date >= '19/03/2021') then
                              v_fecha_final = '18/03/2021';
                              else
                              v_fecha_final = v_parametros.hasta::date;
                              end if;

                              insert into facturas_recibos_temporal (
                                                                      punto_venta,
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      cuenta_auxiliar,
                                                                      observaciones)
                              ((select
                                      DISTINCT(pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from vef.tventa ven
                                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                                      union

                                      (select
                                      distinct (pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from obingresos.tboleto_amadeus bol
                                      inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado = 'revisado' and bol.voided = 'no'
                                      and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                                      union

                                      /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                                      (select
                                      		distinct (pv.nombre),
                                            NULL::date as fecha_factura,
                                            '0'::varchar as nro_factura,
                                            NULL::varchar as tipo_documento,
                                            null::varchar as ruta,
                                            pv.nombre as pasajero,
                                            NULL::numeric as debe,
                                            NULL::numeric as haber,
                                            NULL::varchar as tipo_factura,
                                            NULL::varchar as cuenta_auxiliar,
                                            NULL::varchar as observaciones
                                      from obingresos.tboleto_stage bol
                                      inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                                      and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                                      /***************************************************************************/



                                      union

                                      (SELECT distinct (pb.nombre),
                                              NULL::date as fecha_factura,
                                              '0'::varchar nro_factura,
                                              null::varchar tipo_documento,
                                              null::varchar desc_ruta,
                                              pb.nombre,
                                              null::numeric importe_total_venta,
                                              null::numeric,
                                              NULL::varchar as tipo_factura,
                                              NULL::varchar as cuenta_auxiliar,
                                              NULL::varchar as observaciones

                                        FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                                        'select
                                                fecha_factura,
                                                nro_factura,
                                                nro_factura as nro_documento,
                                                desc_ruta,
                                                razon_social_cli,
                                                importe_total_venta,
                                                codigo_punto_venta
                                          from sfe.tfactura where estado_reg = ''activo''
                                          and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                                          and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                                          and sistema_origen = ''CARGA''
                                         order by fecha_factura ASC, nro_factura ASC
                                        ')
                                        AS tdatos(
                                        fecha_factura date,
                                        nro_factura varchar,
                                        nro_documento varchar,
                                        desc_ruta varchar,
                                        razon_social_cli varchar,
                                        importe_total_venta numeric,
                                        codigo_punto_venta varchar)
                                        inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                                        where pb.tipo = 'carga'));
                             else
                             		insert into facturas_recibos_temporal (
                                                                      punto_venta,
                                                                      fecha_factura,
                                                                      nro_factura,
                                                                      tipo_documento,
                                                                      ruta,
                                                                      pasajero,
                                                                      debe,
                                                                      haber,
                                                                      tipo_factura,
                                                                      cuenta_auxiliar,
                                                                      observaciones)
                              ((select
                                      DISTINCT(pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from vef.tventa ven
                                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                                      union

                                      (select
                                      distinct (pv.nombre),
                                      NULL::date as fecha_factura,
                                      '0'::varchar as nro_factura,
                                      NULL::varchar as tipo_documento,
                                      null::varchar as ruta,
                                      pv.nombre as pasajero,
                                      NULL::numeric as debe,
                                      NULL::numeric as haber,
                                      NULL::varchar as tipo_factura,
                                      NULL::varchar as cuenta_auxiliar,
                                      NULL::varchar as observaciones
                                      from obingresos.tboleto_amadeus bol
                                      inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado = 'revisado' and bol.voided = 'no'
                                      and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                                      union

                                       /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                                      (select
                                      		distinct (pv.nombre),
                                            NULL::date as fecha_factura,
                                            '0'::varchar as nro_factura,
                                            NULL::varchar as tipo_documento,
                                            null::varchar as ruta,
                                            pv.nombre as pasajero,
                                            NULL::numeric as debe,
                                            NULL::numeric as haber,
                                            NULL::varchar as tipo_factura,
                                            NULL::varchar as cuenta_auxiliar,
                                            NULL::varchar as observaciones
                                      from obingresos.tboleto_stage bol
                                      inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                      where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                                      and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                                      /***************************************************************************/




                                      );
                             end if;

              -----------------/*Fin del Reporte de Formas de pago*/
              	end if;
                ---fin todos cuenta corriente

----------------/*Condicion para Generar el reporte Anticipo*/

                elsif (v_parametros.formato_reporte = 'REPORTE ANTICIPO (HABER)') then

                if (v_parametros.codigo_auxiliar = 'Todos') then

                	/*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                    insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )

                    select  depo.fecha,
                            depo.nro_deposito::varchar as nro_factura,
                            --depo.nro_deposito::varchar as nro_documento,
                            'DEPOSITO CUENTA CORRIENTE' as tipo_documento,
                            '' as ruta,
                            aux.nombre_auxiliar::varchar as nombre_factura,
                            0::numeric as monto_debe,
                            param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                            'deposito'::varchar as tipo_factura,
                            'DEPOSITO'::varchar AS nombre,
                            ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                            ''::varchar as observaciones
                    from obingresos.tdeposito depo
                    inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                    and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date;
                    /************************************************************************************************/

                	insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )

                  select  ven.fecha,
                          ven.nro_factura::varchar as nro_factura,
                          --ven.nro_factura::varchar as nro_documento,
                          'ANTICIPO RECIBO' as nro_documento,
                          '' as ruta,
                          ven.nombre_factura,
                          0::numeric as monto_debe,
                          fp.monto_mb_efectivo as monto_haber,
                          ven.tipo_factura,
                          pv.nombre,
                          ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                          upper(ven.observaciones)::varchar as observaciones
                  from vef.tventa ven
                  inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                  inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                  inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                  where ven.estado = 'finalizado'  and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                  and ven.id_auxiliar_anticipo is not null
                  and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date;

                  insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones)
                ((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                        and ven.id_auxiliar_anticipo is not null
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                /*Aumentando los depositos registrados de interfaz*/

                UNION

                (select
                      distinct ('DEPOSITO'),
                      NULL::date as fecha_factura,
                      '0'::varchar as nro_factura,
                      NULL::varchar as tipo_documento,
                      null::varchar as ruta,
                      'DEPOSITO'::VARCHAR as pasajero,
                      NULL::numeric as debe,
                      NULL::numeric as haber,
                      NULL::varchar as tipo_factura,
                      NULL::varchar as cuenta_auxiliar,
                      NULL::varchar as observaciones
                from obingresos.tdeposito depo
                inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date)
                );


                /**************************************************/

                else

                /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                insert into facturas_recibos_temporal (
                                                    fecha_factura,
                                                    nro_factura,
                                                    tipo_documento,
                                                    ruta,
                                                    pasajero,
                                                    debe,
                                                    haber,
                                                    tipo_factura,
                                                    punto_venta,
                                                    cuenta_auxiliar,
                                                    observaciones
                                                    )

                select  depo.fecha,
                        depo.nro_deposito::varchar as nro_factura,
                        'DEPOSITO CUENTA CORRIENTE' as tipo_documento,
                        '' as ruta,
                        aux.nombre_auxiliar::varchar as nombre_factura,
                        0::numeric as monto_debe,
                        param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                        'deposito'::varchar as tipo_factura,
                        'DEPOSITO'::varchar AS nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        ''::varchar as observaciones
                from obingresos.tdeposito depo
                inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                where aux.codigo_auxiliar = v_parametros.codigo_auxiliar;
                /************************************************************************************************/

                 insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones)

                  select  ven.fecha,
                          ven.nro_factura::varchar as nro_factura,
                          'ANTICIPO RECIBO' as tipo_documento,
                          '' as ruta,
                          ven.nombre_factura,
                          0::numeric as monto_debe,
                          fp.monto_mb_efectivo as monto_haber,
                          ven.tipo_factura,
                          pv.nombre,
                          ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                          upper(ven.observaciones)::varchar as observaciones
                  from vef.tventa ven
                  inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                  inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                  inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                  where ven.estado = 'finalizado'  and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                  and ven.id_auxiliar_anticipo is not null
                  and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                  and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date;

                  insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones)
                ((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                        and ven.id_auxiliar_anticipo is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)


                 /*Aumentando los depositos registrados de interfaz*/

                  UNION

                  (select
                        distinct ('DEPOSITO'),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        'DEPOSITO'::VARCHAR as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                  from obingresos.tdeposito depo
                  inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                  and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                  where aux.codigo_auxiliar = v_parametros.codigo_auxiliar)
                  );


                  /**************************************************/





------------------/*FIN para Generar el reporte Anticipo*/
					end if; --Fin codigo Auxiliar todos

-----------------/*Condicion para Generar el reporte con todos los datos Anticipos y Formas de Pago*/
                elsif (v_parametros.formato_reporte = 'REPORTE CONSOLIDADO (DEBE-HABER)') then
				if (v_parametros.codigo_auxiliar = 'Todos') then

                  /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                insert into facturas_recibos_temporal (
                                                    fecha_factura,
                                                    nro_factura,
                                                    tipo_documento,
                                                    ruta,
                                                    pasajero,
                                                    debe,
                                                    haber,
                                                    tipo_factura,
                                                    punto_venta,
                                                    cuenta_auxiliar,
                                                    observaciones
                                                    )

                select  depo.fecha,
                        depo.nro_deposito::varchar as nro_factura,
                        'DEPOSITO CUENTA CORRIENTE' as tipo_documento,
                        '' as ruta,
                        aux.nombre_auxiliar::varchar as nombre_factura,
                        0::numeric as monto_debe,
                        param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                        'deposito'::varchar as tipo_factura,
                        'DEPOSITO'::varchar AS nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        ''::varchar as observaciones
                from obingresos.tdeposito depo
                inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date;
                /************************************************************************************************/



                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones)

                  select  ven.fecha,
                          ven.nro_factura::varchar as nro_factura,
                          'ANTICIPO RECIBO' as tipo_documento,
                          '' as ruta,
                          ven.nombre_factura,
                          0::numeric as monto_debe,
                          fp.monto_mb_efectivo as monto_haber,
                          ven.tipo_factura,
                          pv.nombre,
                          ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                          upper(ven.observaciones)::varchar as observaciones
                  from vef.tventa ven
                  inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                  inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                  inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                  where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                  and ven.id_auxiliar_anticipo is not null
                  and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date;



               insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones)


                select  ven.fecha,
                		ven.nro_factura::varchar as nro_factura,
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
                              WHEN ven.tipo_factura = 'carga'
                              THEN
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                              ELSE
                              	''

                        END) as tipo_documento,
                		/*(CASE
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
                              WHEN ven.tipo_factura = 'carga'
                              THEN
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                              ELSE
                              	''

                        END) as ruta*/''::varchar as ruta,
                        ven.nombre_factura,
                        fp.monto_mb_efectivo as monto_debe,
                        0::numeric as monto_haber,
                        ven.tipo_factura,
                        pv.nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        upper(ven.observaciones)::varchar as observaciones
                from vef.tventa ven
                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
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
                                            and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)||')

                  order by fecha_factura ASC, nro_factura ASC
                ')
                AS tdatos(
                desc_ruta varchar,
                nro_factura numeric);
                /*******************************************************************************/


                /*Aqui insertamos los datos en la tabla temporal*/

                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones)
                select
                        bol.fecha_emision,
                        bol.nro_boleto as nro_factura,
                        'BOLETO' as tipo_documento,
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
                        pv.nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        ''::varchar as observaciones
                from obingresos.tboleto_amadeus bol
                inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                where bol.estado = 'revisado' and bol.voided = 'no'
                and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;

                /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones)


                select
                      bol.fecha_emision,
                      bol.nro_boleto as nro_factura,
                      'BOLETO EXTERNO'::varchar as tipo_documento,
                      ''::varchar as ruta ,
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
                      pv.nombre,
                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                      ''::varchar as observaciones
                from obingresos.tboleto_stage bol
                inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;
                /********************************************/




                /*Aqui Recuperamos los datos de Carga*/
                if(v_parametros.desde::date < '19/03/2021')then

                if (v_parametros.hasta::date >= '19/03/2021') then
                v_fecha_final = '18/03/2021';
                else
                v_fecha_final = v_parametros.hasta::date;
                end if;


                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones)
              (SELECT 	tdatos.fecha_factura,
                        tdatos.nro_factura,
                        'FACTURACION CARGA NACIONAL COMPUTARIZADA'::varchar as tipo_documento,
                        tdatos.desc_ruta,
                        tdatos.razon_social_cli,
                        tdatos.importe_total_venta,
                        0::numeric as haber,
                        'carga'::varchar as tipo_factura,
                        pb.nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        ''::varchar as observaciones
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
                codigo_auxiliar_carga varchar)
                inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                where pb.tipo = 'carga');
                end if;
                /*************************************/

                /*Aqui para ir agrupando los puntos de ventas*/
			if(v_parametros.desde::date < '19/03/2021')then

            if (v_parametros.hasta::date >= '19/03/2021') then
            v_fecha_final = '18/03/2021';
            else
            v_fecha_final = v_parametros.hasta::date;
            end if;

                insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                (
                	(select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                        /***************************************************************************/

                        union

                        (SELECT distinct (pb.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar nro_factura,
                                null::varchar tipo_documento,
                                null::varchar desc_ruta,
                                pb.nombre,
                                null::numeric importe_total_venta,
                                null::numeric,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
								NULL::varchar as observaciones
                          FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                          'select
                                  fecha_factura,
                                  nro_factura,
                                  nro_factura as nro_documento,
                                  desc_ruta,
                                  razon_social_cli,
                                  importe_total_venta,
                                  codigo_punto_venta
                            from sfe.tfactura where estado_reg = ''activo''
                            and codigo_auxiliar is not null
                            and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                            and sistema_origen = ''CARGA''
                           order by fecha_factura ASC, nro_factura ASC
                          ')
                          AS tdatos(
                          fecha_factura date,
                          nro_factura varchar,
                          nro_documento varchar,
                          desc_ruta varchar,
                          razon_social_cli varchar,
                          importe_total_venta numeric,
                          codigo_punto_venta varchar)
                          inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                          where pb.tipo = 'carga')

                          union

                        (select
                          DISTINCT(pv.nombre),
                          NULL::date as fecha_factura,
                          '0'::varchar as nro_factura,
                          NULL::varchar as tipo_documento,
                          null::varchar as ruta,
                          pv.nombre as pasajero,
                          NULL::numeric as debe,
                          NULL::numeric as haber,
                          NULL::varchar as tipo_factura,
                          NULL::varchar as cuenta_auxiliar,
                          NULL::varchar as observaciones
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo'  and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)


                          /*Aumentando los depositos registrados de interfaz*/

                          UNION

                          (select
                                distinct ('DEPOSITO'),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                'DEPOSITO'::VARCHAR as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date)


                          /**************************************************/



                          );

                else

                         insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                (
                	(select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                        /***************************************************************************/




                        union

                        (select
                          DISTINCT(pv.nombre),
                          NULL::date as fecha_factura,
                          '0'::varchar as nro_factura,
                          NULL::varchar as tipo_documento,
                          null::varchar as ruta,
                          pv.nombre as pasajero,
                          NULL::numeric as debe,
                          NULL::numeric as haber,
                          NULL::varchar as tipo_factura,
                          NULL::varchar as cuenta_auxiliar,
                          NULL::varchar as observaciones
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)


                          /*Aumentando los depositos registrados de interfaz*/

                          UNION

                          (select
                                distinct ('DEPOSITO'),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                'DEPOSITO'::VARCHAR as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                          /**************************************************/

                          );

                    end if;


                else

                 /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                insert into facturas_recibos_temporal (
                                                    fecha_factura,
                                                    nro_factura,
                                                    tipo_documento,
                                                    ruta,
                                                    pasajero,
                                                    debe,
                                                    haber,
                                                    tipo_factura,
                                                    punto_venta,
                                                    cuenta_auxiliar,
                                                    observaciones
                                                    )

                select  depo.fecha,
                        depo.nro_deposito::varchar as nro_factura,
                        'DEPOSITO CUENTA CORRIENTE'::varchar as tipo_documento,
                        '' as ruta,
                        aux.nombre_auxiliar::varchar as nombre_factura,
                        0::numeric as monto_debe,
                        param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                        'deposito'::varchar as tipo_factura,
                        'DEPOSITO'::varchar AS nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        NULL::varchar as observaciones
                from obingresos.tdeposito depo
                inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                where aux.codigo_auxiliar = v_parametros.codigo_auxiliar;
                /************************************************************************************************/





                 insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones)

                  select  ven.fecha,
                          ven.nro_factura::varchar as nro_factura,
                          'ANTICIPO RECIBO'::varchar as tipo_documento,
                          '' as ruta,
                          ven.nombre_factura,
                          0::numeric as monto_debe,
                          fp.monto_mb_efectivo as monto_haber,
                          ven.tipo_factura,
                          pv.nombre,
                          ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                          NULL::varchar as observaciones
                  from vef.tventa ven
                  inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                  inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                  inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                  where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                  and ven.id_auxiliar_anticipo is not null
                  and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                  and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date;



               insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones)


                select  ven.fecha,
                		ven.nro_factura::varchar as nro_factura,
                        --ven.nro_factura::varchar as nro_documento,
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
                              WHEN ven.tipo_factura = 'carga'
                              THEN
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                              ELSE
                              	''

                        END) as tipo_documento,
                		/*(CASE
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
                              WHEN ven.tipo_factura = 'carga'
                              THEN
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                              ELSE
                              	''

                        END) as ruta*/''::varchar as ruta,
                        ven.nombre_factura,
                        fp.monto_mb_efectivo as monto_debe,
                        0::numeric as monto_haber,
                        ven.tipo_factura,
                        pv.nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        upper(ven.observaciones)::varchar as observaciones
                from vef.tventa ven
                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
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
                nro_factura numeric);
                /*******************************************************************************/


                /*Aqui insertamos los datos en la tabla temporal*/

                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones)
                select
                        bol.fecha_emision,
                        bol.nro_boleto as nro_factura,
                        'BOLETO' as tipo_documento,
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
                        pv.nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        ''::varchar as observaciones
                from obingresos.tboleto_amadeus bol
                inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                where bol.estado = 'revisado' and bol.voided = 'no'
                and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;



                /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones)


                select
                      bol.fecha_emision,
                      bol.nro_boleto as nro_factura,
                      'BOLETO EXTERNO'::varchar as tipo_documento,
                      ''::varchar as ruta ,
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
                      pv.nombre,
                      ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                      ''::varchar as observaciones
                from obingresos.tboleto_stage bol
                inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;
                /********************************************/



                /*Aqui Recuperamos los datos de Carga*/

                if(v_parametros.desde::date < '19/03/2021')then

                if (v_parametros.hasta::date >= '19/03/2021') then
                v_fecha_final = '18/03/2021';
                else
                v_fecha_final = v_parametros.hasta::date;
                end if;

                insert into facturas_recibos_temporal (
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        punto_venta,
                                                        cuenta_auxiliar,
                                                        observaciones)
              (SELECT 	tdatos.fecha_factura,
                        tdatos.nro_factura,
                        'FACTURACION CARGA NACIONAL COMPUTARIZADA'::varchar as tipo_documento,
                        tdatos.desc_ruta,
                        tdatos.razon_social_cli,
                        tdatos.importe_total_venta,
                        0::numeric as haber,
                        'carga'::varchar as tipo_factura,
                        pb.nombre,
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                        ''::varchar as observaciones
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
                  and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                  and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                  and sistema_origen = ''CARGA''
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
                codigo_auxiliar_carga varchar)
                inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                where pb.tipo = 'carga');
                end if;
                /*************************************/

                /*Aqui para ir agrupando los puntos de ventas*/
				if(v_parametros.desde::date < '19/03/2021')then

                if (v_parametros.hasta::date >= '19/03/2021') then
                v_fecha_final = '18/03/2021';
                else
                v_fecha_final = v_parametros.hasta::date;
                end if;

                insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones)
                (
                	(select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                        /***************************************************************************/

                        union

                        (SELECT distinct (pb.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar nro_factura,
                                null::varchar tipo_documento,
                                null::varchar desc_ruta,
                                pb.nombre,
                                null::numeric importe_total_venta,
                                null::numeric,
                                NULL::varchar as tipo_factura,
								NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                          FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                          'select
                                  fecha_factura,
                                  nro_factura,
                                  nro_factura as nro_documento,
                                  desc_ruta,
                                  razon_social_cli,
                                  importe_total_venta,
                                  codigo_punto_venta
                            from sfe.tfactura where estado_reg = ''activo''
                            and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                            and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                            and sistema_origen = ''CARGA''
                           order by fecha_factura ASC, nro_factura ASC
                          ')
                          AS tdatos(
                          fecha_factura date,
                          nro_factura varchar,
                          nro_documento varchar,
                          desc_ruta varchar,
                          razon_social_cli varchar,
                          importe_total_venta numeric,
                          codigo_punto_venta varchar)
                          inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                          where pb.tipo = 'carga')

                          union

                        (select
                          DISTINCT(pv.nombre),
                          NULL::date as fecha_factura,
                          '0'::varchar as nro_factura,
                          NULL::varchar as tipo_documento,
                          null::varchar as ruta,
                          pv.nombre as pasajero,
                          NULL::numeric as debe,
                          NULL::numeric as haber,
                          NULL::varchar as tipo_factura,
                          NULL::varchar as cuenta_auxiliar,
                          NULL::varchar as observaciones
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                          /*Aumentando los depositos registrados de interfaz*/

                          UNION

                          (select
                                distinct ('DEPOSITO'),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                'DEPOSITO'::VARCHAR as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          where aux.codigo_auxiliar = v_parametros.codigo_auxiliar)

                          /**************************************************/

                          );
                    else
                    	insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones)
                (
                	(select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)

                        union

                        /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date)
                        /***************************************************************************/

                        union

                        (select
                          DISTINCT(pv.nombre),
                          NULL::date as fecha_factura,
                          '0'::varchar as nro_factura,
                          NULL::varchar as tipo_documento,
                          null::varchar as ruta,
                          pv.nombre as pasajero,
                          NULL::numeric as debe,
                          NULL::numeric as haber,
                          NULL::varchar as tipo_factura,
                          NULL::varchar as cuenta_auxiliar,
                          NULL::varchar as observaciones
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date)

                          /*Aumentando los depositos registrados de interfaz*/

                          UNION

                          (select
                                distinct ('DEPOSITO'),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                'DEPOSITO'::VARCHAR as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          where aux.codigo_auxiliar = v_parametros.codigo_auxiliar)

                          /**************************************************/

                          );
                    end if;

------------------/*Fin del reporte Anticipos y Formas de Pago*/
					end if;


				end if;

                /************************************************/

                else

              	if (v_parametros.formato_reporte = 'REPORTE FORMAS DE PAGO CTA/CTE (DEBE)') then

                if (v_parametros.codigo_auxiliar = 'Todos') THEN
                	 insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones)


                      select  ven.fecha,
                              ven.nro_factura::varchar as nro_factura,
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END) as  tipo_documento,
                              /*(CASE
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END)*/''::varchar as ruta,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              upper(ven.observaciones)::varchar as observaciones
                      from vef.tventa ven
                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                      and ven.id_auxiliar_anticipo is null
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
                                                  and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
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
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones)
                      select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO'::varchar as tipo_documento,
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
                      		  pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones
                      from obingresos.tboleto_amadeus bol
                      inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                      left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                      where bol.estado = 'revisado' and bol.voided = 'no'
                      and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                      and bol.id_punto_venta = v_parametros.id_punto_venta;
                      /************************************************/


                      /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)


                        select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO EXTERNO'::varchar as tipo_documento,
                              ''::varchar as ruta ,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date;
                        /********************************************/


                      /*Aqui recuperamos el id codigo punto de venta para filtrar carga*/
                      select pv.codigo
                      	     into
                             v_codigo
                      from vef.tpunto_venta pv
                      where pv.id_punto_venta = v_parametros.id_punto_venta and pv.tipo = 'carga';
                      /*****************************************************************/

                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then

                      if (v_parametros.hasta::date >= '19/03/2021') then
                      v_fecha_final = '18/03/2021';
                      else
                      v_fecha_final = v_parametros.hasta::date;
                      end if;

                       /*Aqui Recuperamos los datos de Carga*/
                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)
                      (SELECT 	tdatos.fecha_factura,
                                tdatos.nro_factura,
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA'::varchar as tipo_documento,
                                tdatos.desc_ruta,
                                tdatos.razon_social_cli,
                                tdatos.importe_total_venta,
                                0::numeric as haber,
                                'carga'::varchar as tipo_factura,
                                pb.nombre,
                                ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                ''::varchar as observaciones
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
                          and codigo_punto_venta = '''||v_codigo||'''
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
                        codigo_auxiliar_carga varchar)
                        inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                        inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                        where pb.tipo = 'carga');
                        /*************************************/
                       end if;


                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then

                      if (v_parametros.hasta::date >= '19/03/2021') then
                      v_fecha_final = '18/03/2021';
                      else
                      v_fecha_final = v_parametros.hasta::date;
                      end if;

                      insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones)
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)
                        /***************************************************************************/

                        union

                        (SELECT distinct (pb.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar nro_factura,
                                null::varchar tipo_documento,
                                null::varchar desc_ruta,
                                pb.nombre,
                                null::numeric importe_total_venta,
                                null::numeric,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
								NULL::varchar as observaciones
                          FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                          'select
                                  fecha_factura,
                                  nro_factura,
                                  nro_factura as nro_documento,
                                  desc_ruta,
                                  razon_social_cli,
                                  importe_total_venta,
                                  codigo_punto_venta
                            from sfe.tfactura where estado_reg = ''activo''
                            and codigo_auxiliar is not null
                            and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                            and codigo_punto_venta = '''||v_codigo||'''
                            and sistema_origen = ''CARGA''
                           order by fecha_factura ASC, nro_factura ASC
                          ')
                          AS tdatos(
                          fecha_factura date,
                          nro_factura varchar,
                          nro_documento varchar,
                          desc_ruta varchar,
                          razon_social_cli varchar,
                          importe_total_venta numeric,
                          codigo_punto_venta varchar)
                          inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                          where pb.tipo = 'carga'));

                        else
                        	insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones)
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_auxiliar_anticipo is null
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

						(select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)

                        );


                    end if;



                else

                      insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones)


                      select  ven.fecha,
                              ven.nro_factura::varchar as nro_factura,
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END)as tipo_documento,
                              /*(CASE
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END)*/''::varchar as ruta,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              upper(ven.observaciones)::varchar as observaciones
                      from vef.tventa ven
                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                      and ven.id_auxiliar_anticipo is null
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
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones)
                      select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO' as tipo_documento,
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
                      		  pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones
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
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)


                        select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO EXTERNO'::varchar as tipo_documento,
                              ''::varchar as ruta ,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta;
                        /********************************************/




                      /*Aqui recuperamos el id codigo punto de venta para filtrar carga*/

                      select pv.codigo
                      	     into
                             v_codigo
                      from vef.tpunto_venta pv
                      where pv.id_punto_venta = v_parametros.id_punto_venta and pv.tipo = 'carga';
                      /*****************************************************************/

                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then

                      if (v_parametros.hasta::date >= '19/03/2021') then
                      v_fecha_final = '18/03/2021';
                      else
                      v_fecha_final = v_parametros.hasta::date;
                      end if;

                       /*Aqui Recuperamos los datos de Carga*/
                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)
                      (SELECT 	tdatos.fecha_factura,
                                tdatos.nro_factura,
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA' as tipo_documento,
                                tdatos.desc_ruta,
                                tdatos.razon_social_cli,
                                tdatos.importe_total_venta,
                                0::numeric as haber,
                                'carga'::varchar as tipo_factura,
                                pb.nombre,
                                ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                ''::varchar as observaciones
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
                          and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                          and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                          and sistema_origen = ''CARGA''
                          and codigo_punto_venta = '''||v_codigo||'''
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
                        codigo_auxiliar_carga varchar)
                        inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                        inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                        where pb.tipo = 'carga');
                        /*************************************/
                      end if;

                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then
                      if (v_parametros.hasta::date >= '19/03/2021') then
                      v_fecha_final = '18/03/2021';
                      else
                      v_fecha_final = v_parametros.hasta::date;
                      end if;

                      insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as codigo_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)

                        union

                        (SELECT distinct (pb.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar nro_factura,
                                null::varchar tipo_documento,
                                null::varchar desc_ruta,
                                pb.nombre,
                                null::numeric importe_total_venta,
                                null::numeric,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
								NULL::varchar as observaciones
                          FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                          'select
                                  fecha_factura,
                                  nro_factura,
                                  nro_factura as nro_documento,
                                  desc_ruta,
                                  razon_social_cli,
                                  importe_total_venta,
                                  codigo_punto_venta
                            from sfe.tfactura where estado_reg = ''activo''
                            and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                            and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                            and codigo_punto_venta = '''||v_codigo||'''
                            and sistema_origen = ''CARGA''
                           order by fecha_factura ASC, nro_factura ASC
                          ')
                          AS tdatos(
                          fecha_factura date,
                          nro_factura varchar,
                          nro_documento varchar,
                          desc_ruta varchar,
                          razon_social_cli varchar,
                          importe_total_venta numeric,
                          codigo_punto_venta varchar)
                          inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                          where pb.tipo = 'carga'));
                        else
                        	insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_auxiliar_anticipo is null
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)



                        );
                	end if;
                end if;
-------------------------------------

                    elsif (v_parametros.formato_reporte = 'REPORTE ANTICIPO (HABER)') then
						if (v_parametros.codigo_auxiliar = 'Todos') then

                         /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                          insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones
                                                              )

                          select  depo.fecha,
                                  depo.nro_deposito::varchar as nro_factura,
                                  'DEPOSITO CUENTA CORRIENTE'::varchar as tipo_documento,
                                  '' as ruta,
                                  aux.nombre_auxiliar::varchar as nombre_factura,
                                  0::numeric as monto_debe,
                                  param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                                  'deposito'::varchar as tipo_factura,
                                  'DEPOSITO'::varchar AS nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  ''::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date;
                          /************************************************************************************************/


                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)

                          select  ven.fecha,
                                  ven.nro_factura::varchar as nro_factura,
                                  'ANTICIPO RECIBO'::varchar as tipo_documento,
                                  '' as ruta,
                                  ven.nombre_factura,
                                  0::numeric as monto_debe,
                                  fp.monto_mb_efectivo as monto_haber,
                                  ven.tipo_factura,
                                  pv.nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  NULL::varchar as observaciones
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          and ven.id_punto_venta = v_parametros.id_punto_venta;

                          insert into facturas_recibos_temporal (
                                                                punto_venta,
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                cuenta_auxiliar,
                                                                observaciones
                                                                )
                       ( (select
                                DISTINCT(pv.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                pv.nombre as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                                from vef.tventa ven
                                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                                and ven.id_auxiliar_anticipo is not null
                                and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                                and ven.id_punto_venta = v_parametros.id_punto_venta)
                        /*Aumentando para los depositos*/
                        UNION

                            (select
                                  distinct ('DEPOSITO'),
                                  NULL::date as fecha_factura,
                                  '0'::varchar as nro_factura,
                                  NULL::varchar as tipo_documento,
                                  null::varchar as ruta,
                                  'DEPOSITO'::VARCHAR as pasajero,
                                  NULL::numeric as debe,
                                  NULL::numeric as haber,
                                  NULL::varchar as tipo_factura,
                                  NULL::varchar as cuenta_auxiliar,
                                  NULL::varchar as observaciones
                            from obingresos.tdeposito depo
                            inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                            and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date)
                        /*******************************/
                         );

                        else

                        /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                          insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones
                                                              )

                          select  depo.fecha,
                                  depo.nro_deposito::varchar as nro_factura,
                                  'DEPOSITO CUENTA CORRIENTE'::varchar as tipo_documento,
                                  '' as ruta,
                                  aux.nombre_auxiliar::varchar as nombre_factura,
                                  0::numeric as monto_debe,
                                  param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                                  'deposito'::varchar as tipo_factura,
                                  'DEPOSITO'::varchar AS nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  NULL::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          where aux.codigo_auxiliar = v_parametros.codigo_auxiliar;
                          /************************************************************************************************/




                         insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)

                          select  ven.fecha,
                                  ven.nro_factura::varchar as nro_factura,
                                  'ANTICIPO RECIBO'::varchar as tipo_documento,
                                  '' as ruta,
                                  ven.nombre_factura,
                                  0::numeric as monto_debe,
                                  fp.monto_mb_efectivo as monto_haber,
                                  ven.tipo_factura,
                                  pv.nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  upper(ven.observaciones)::varchar as observaciones
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          and ven.id_punto_venta = v_parametros.id_punto_venta;

                          insert into facturas_recibos_temporal (
                                                                punto_venta,
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                cuenta_auxiliar,
                                                                observaciones)
                        ((select
                                DISTINCT(pv.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                pv.nombre as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                                from vef.tventa ven
                                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                                and ven.id_auxiliar_anticipo is not null
                                and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                                and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                                and ven.id_punto_venta = v_parametros.id_punto_venta)
                                /*Aumentando para los depositos*/
                        UNION

                            (select
                                  distinct ('DEPOSITO'),
                                  NULL::date as fecha_factura,
                                  '0'::varchar as nro_factura,
                                  NULL::varchar as tipo_documento,
                                  null::varchar as ruta,
                                  'DEPOSITO'::VARCHAR as pasajero,
                                  NULL::numeric as debe,
                                  NULL::numeric as haber,
                                  NULL::varchar as tipo_factura,
                                  NULL::varchar as cuenta_auxiliar,
                                  NULL::varchar as observaciones
                            from obingresos.tdeposito depo
                            inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                            and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                            where aux.codigo_auxiliar = v_parametros.codigo_auxiliar)
                        /*******************************/    );
					end if;
/*****************************/ elsif (v_parametros.formato_reporte = 'REPORTE CONSOLIDADO (DEBE-HABER)') then
					if (v_parametros.codigo_auxiliar = 'Todos') then

                      /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                          insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones
                                                              )

                          select  depo.fecha,
                                  depo.nro_deposito::varchar as nro_factura,
                                  'DEPOSITO CUENTA CORRIENTE'::varchar as tipo_documento,
                                  '' as ruta,
                                  aux.nombre_auxiliar::varchar as nombre_factura,
                                  0::numeric as monto_debe,
                                  param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                                  'deposito'::varchar as tipo_factura,
                                  'DEPOSITO'::varchar AS nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  ''::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date;
                          /************************************************************************************************/


                    	insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones)


                      select  ven.fecha,
                              ven.nro_factura::varchar as nro_factura,
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END)::varchar as tipo_documento,
                              /*(CASE
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END)*/'' as ruta,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              upper(ven.observaciones)::varchar as observaciones
                      from vef.tventa ven
                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                      and ven.id_auxiliar_anticipo is null
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
                                                  and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
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
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones)
                      select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO'::varchar as tipo_documento,
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
                      		  pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones
                      from obingresos.tboleto_amadeus bol
                      inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                      left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                      where bol.estado = 'revisado' and bol.voided = 'no'
                      and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                      and bol.id_punto_venta = v_parametros.id_punto_venta;
                      /************************************************/


                      /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)


                        select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO EXTERNO'::varchar as tipo_documento,
                              ''::varchar as ruta ,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta;
                        /********************************************/

                      insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)

                          select  ven.fecha,
                                  ven.nro_factura::varchar as nro_factura,
                                  'ANTICIPO RECIBO'::varchar as tipo_documento,
                                  '' as ruta,
                                  ven.nombre_factura,
                                  0::numeric as monto_debe,
                                  fp.monto_mb_efectivo as monto_haber,
                                  ven.tipo_factura,
                                  pv.nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  ''::varchar as observaciones
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          and ven.id_punto_venta = v_parametros.id_punto_venta;

                      /*Aqui recuperamos el id codigo punto de venta para filtrar carga*/

                      select pv.codigo
                      	     into
                             v_codigo
                      from vef.tpunto_venta pv
                      where pv.id_punto_venta = v_parametros.id_punto_venta and pv.tipo = 'carga';
                      /*****************************************************************/

                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then

                      if (v_parametros.hasta::date >= '19/03/2021') then
                      v_fecha_final = '18/03/2021';
                      else
                      v_fecha_final = v_parametros.hasta::date;
                      end if;

                       /*Aqui Recuperamos los datos de Carga*/
                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)
                      (SELECT 	tdatos.fecha_factura,
                                tdatos.nro_factura,
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA'::varchar as tipo_documento,
                                tdatos.desc_ruta,
                                tdatos.razon_social_cli,
                                tdatos.importe_total_venta,
                                0::numeric as haber,
                                'carga'::varchar as tipo_factura,
                                pb.nombre,
                                ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                ''::varchar as observaciones
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
                          and codigo_punto_venta = '''||v_codigo||'''
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
                        codigo_auxiliar_carga varchar)
                        inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                        inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                        where pb.tipo = 'carga');
                        /*************************************/
                      end if;

                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then

                      if (v_parametros.hasta::date >= '19/03/2021') then
                      v_fecha_final = '18/03/2021';
                      else
                      v_fecha_final = v_parametros.hasta::date;
                      end if;


                      insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)

                        union

                        (SELECT distinct (pb.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar nro_factura,
                                null::varchar tipo_documento,
                                null::varchar desc_ruta,
                                pb.nombre,
                                null::numeric importe_total_venta,
                                null::numeric,
                                NULL::varchar as tipo_factura,
								NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                          FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                          'select
                                  fecha_factura,
                                  nro_factura,
                                  nro_factura as nro_documento,
                                  desc_ruta,
                                  razon_social_cli,
                                  importe_total_venta,
                                  codigo_punto_venta
                            from sfe.tfactura where estado_reg = ''activo''
                            and codigo_auxiliar is not null
                            and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                            and codigo_punto_venta = '''||v_codigo||'''
                            and sistema_origen = ''CARGA''
                           order by fecha_factura ASC, nro_factura ASC
                          ')
                          AS tdatos(
                          fecha_factura date,
                          nro_factura varchar,
                          nro_documento varchar,
                          desc_ruta varchar,
                          razon_social_cli varchar,
                          importe_total_venta numeric,
                          codigo_punto_venta varchar)
                          inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                          where pb.tipo = 'carga')

                          union

                        (select
                                DISTINCT(pv.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                pv.nombre as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                                from vef.tventa ven
                                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                                and ven.id_auxiliar_anticipo is not null
                                and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                                and ven.id_punto_venta = v_parametros.id_punto_venta)

                        /*Aumentando para los depositos*/
                        UNION

                            (select
                                  distinct ('DEPOSITO'),
                                  NULL::date as fecha_factura,
                                  '0'::varchar as nro_factura,
                                  NULL::varchar as tipo_documento,
                                  null::varchar as ruta,
                                  'DEPOSITO'::VARCHAR as pasajero,
                                  NULL::numeric as debe,
                                  NULL::numeric as haber,
                                  NULL::varchar as tipo_factura,
                                  NULL::varchar as cuenta_auxiliar,
                                  NULL::varchar as observaciones
                            from obingresos.tdeposito depo
                            inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                            and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date)
                        /*******************************/

                        );

                      else
                        	insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo'
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_auxiliar_anticipo is null
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)

                        union

                        (select
                                DISTINCT(pv.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                pv.nombre as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                                from vef.tventa ven
                                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                                and ven.id_auxiliar_anticipo is not null
                                and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                                and ven.id_punto_venta = v_parametros.id_punto_venta)

                        /*Aumentando para los depositos*/
                        UNION

                            (select
                                  distinct ('DEPOSITO'),
                                  NULL::date as fecha_factura,
                                  '0'::varchar as nro_factura,
                                  NULL::varchar as tipo_documento,
                                  null::varchar as ruta,
                                  'DEPOSITO'::VARCHAR as pasajero,
                                  NULL::numeric as debe,
                                  NULL::numeric as haber,
                                  NULL::varchar as tipo_factura,
                                  NULL::varchar as cuenta_auxiliar,
                                  NULL::varchar as observaciones
                            from obingresos.tdeposito depo
                            inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                            and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date)
                        /*******************************/

                        );


                 	end if;

                    else

                    /*Aqui aumentamos para recuperar los depositos cuenta_corriente registrados de la nueva interfaz*/
                          insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones
                                                              )

                          select  depo.fecha,
                                  depo.nro_deposito::varchar as nro_factura,
                                  'DEPOSITO CUENTA CORRIENTE'::varchar as tipo_documento,
                                  '' as ruta,
                                  aux.nombre_auxiliar::varchar as nombre_factura,
                                  0::numeric as monto_debe,
                                  param.f_convertir_moneda(depo.id_moneda_deposito,v_id_moneda_base,depo.monto_deposito,depo.fecha,'O',2,NULL,'si') as monto_haber,
                                  'deposito'::varchar as tipo_factura,
                                  'DEPOSITO'::varchar AS nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  ''::varchar as observaciones
                          from obingresos.tdeposito depo
                          inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                          and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          where aux.codigo_auxiliar = v_parametros.codigo_auxiliar;
                          /************************************************************************************************/


                      insert into facturas_recibos_temporal (
                                                              fecha_factura,
                                                              nro_factura,
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones)


                      select  ven.fecha,
                              ven.nro_factura::varchar as nro_factura,
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END)::varchar as tipo_documento,
                              /*(CASE
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
                                    WHEN ven.tipo_factura = 'carga'
                                    THEN
                                      'FACTURACION CARGA NACIONAL COMPUTARIZADA'
                                    ELSE
                                      ''

                              END)*/'' as ruta,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              upper(ven.observaciones)::varchar as observaciones
                      from vef.tventa ven
                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                      where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                      and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                      and ven.id_auxiliar_anticipo is null
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
                                                              tipo_documento,
                                                              ruta,
                                                              pasajero,
                                                              debe,
                                                              haber,
                                                              tipo_factura,
                                                              punto_venta,
                                                              cuenta_auxiliar,
                                                              observaciones)
                      select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO'::varchar as tipo_documento,
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
                      		  pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones
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


                      /*Aqui Recuperamos para los boletos externos Ejemplo Caso YACIMIENTOS*/
                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)


                        select
                              bol.fecha_emision,
                              bol.nro_boleto as nro_factura,
                              'BOLETO EXTERNO'::varchar as tipo_documento,
                              ''::varchar as ruta ,
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
                              pv.nombre,
                              ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                              ''::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta;
                        /********************************************/

                      insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)

                          select  ven.fecha,
                                  ven.nro_factura::varchar as nro_factura,
                                  'ANTICIPO RECIBO'::varchar as tipo_documento,
                                  '' as ruta,
                                  ven.nombre_factura,
                                  0::numeric as monto_debe,
                                  fp.monto_mb_efectivo as monto_haber,
                                  ven.tipo_factura,
                                  pv.nombre,
                                  ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                  upper(ven.observaciones)::varchar as observaciones
                          from vef.tventa ven
                          inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                          inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                          and ven.id_auxiliar_anticipo is not null
                          and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                          and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                          and ven.id_punto_venta = v_parametros.id_punto_venta;

                      /*Aqui recuperamos el id codigo punto de venta para filtrar carga*/

                      select pv.codigo
                      	     into
                             v_codigo
                      from vef.tpunto_venta pv
                      where pv.id_punto_venta = v_parametros.id_punto_venta and pv.tipo = 'carga';
                      /*****************************************************************/

                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then
						if (v_parametros.hasta::date >= '19/03/2021') then
                        v_fecha_final = '18/03/2021';
                        else
                        v_fecha_final = v_parametros.hasta::date;
                        end if;

                       /*Aqui Recuperamos los datos de Carga*/
                        insert into facturas_recibos_temporal (
                                                                fecha_factura,
                                                                nro_factura,
                                                                tipo_documento,
                                                                ruta,
                                                                pasajero,
                                                                debe,
                                                                haber,
                                                                tipo_factura,
                                                                punto_venta,
                                                                cuenta_auxiliar,
                                                                observaciones)
                      (SELECT 	tdatos.fecha_factura,
                                tdatos.nro_factura,
                                'FACTURACION CARGA NACIONAL COMPUTARIZADA'::varchar as tipo_documento,
                                tdatos.desc_ruta,
                                tdatos.razon_social_cli,
                                tdatos.importe_total_venta,
                                0::numeric as haber,
                                'carga'::varchar as tipo_factura,
                                pb.nombre,
                                ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar,
                                ''::varchar as observaciones
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
                          and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                          and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                          and sistema_origen = ''CARGA''
                          and codigo_punto_venta = '''||v_codigo||'''
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
                        codigo_auxiliar_carga varchar)
                        inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                        inner join conta.tauxiliar aux on aux.codigo_auxiliar = codigo_auxiliar_carga
                        where pb.tipo = 'carga');
                        /*************************************/
                      end if;

                      if (v_codigo != '' and v_parametros.desde::date < '19/03/2021') then

                      if (v_parametros.hasta::date >= '19/03/2021') then
                      v_fecha_final = '18/03/2021';
                      else
                      v_fecha_final = v_parametros.hasta::date;
                      end if;



                      insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)

                        union

                        (SELECT distinct (pb.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar nro_factura,
                                null::varchar tipo_documento,
                                null::varchar desc_ruta,
                                pb.nombre,
                                null::numeric importe_total_venta,
                                null::numeric,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
								NULL::varchar as observaciones
                          FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                          'select
                                  fecha_factura,
                                  nro_factura,
                                  nro_factura as nro_documento,
                                  desc_ruta,
                                  razon_social_cli,
                                  importe_total_venta,
                                  codigo_punto_venta
                            from sfe.tfactura where estado_reg = ''activo''
                            and codigo_auxiliar = '''||v_parametros.codigo_auxiliar||'''
                            and fecha_factura between '''||v_parametros.desde::date||''' and '''||v_fecha_final||'''
                            and codigo_punto_venta = '''||v_codigo||'''
                            and sistema_origen = ''CARGA''
                           order by fecha_factura ASC, nro_factura ASC
                          ')
                          AS tdatos(
                          fecha_factura date,
                          nro_factura varchar,
                          nro_documento varchar,
                          desc_ruta varchar,
                          razon_social_cli varchar,
                          importe_total_venta numeric,
                          codigo_punto_venta varchar)
                          inner join vef.tpunto_venta pb on pb.codigo = codigo_punto_venta
                          where pb.tipo = 'carga')

                          union

                        (select
                                DISTINCT(pv.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                pv.nombre as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                                from vef.tventa ven
                                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                                and ven.id_auxiliar_anticipo is not null
                                and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                                and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                                and ven.id_punto_venta = v_parametros.id_punto_venta)

                                /*Aumentando para los depositos*/
                        UNION

                            (select
                                  distinct ('DEPOSITO'),
                                  NULL::date as fecha_factura,
                                  '0'::varchar as nro_factura,
                                  NULL::varchar as tipo_documento,
                                  null::varchar as ruta,
                                  'DEPOSITO'::VARCHAR as pasajero,
                                  NULL::numeric as debe,
                                  NULL::numeric as haber,
                                  NULL::varchar as tipo_factura,
                                  NULL::varchar as cuenta_auxiliar,
                                  NULL::varchar as observaciones
                            from obingresos.tdeposito depo
                            inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                            and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                            where aux.codigo_auxiliar = v_parametros.codigo_auxiliar)
                        /*******************************/
                        );

                      else
                        	insert into facturas_recibos_temporal (
                      									punto_venta,
                                                        fecha_factura,
                                                        nro_factura,
                                                        tipo_documento,
                                                        ruta,
                                                        pasajero,
                                                        debe,
                                                        haber,
                                                        tipo_factura,
                                                        cuenta_auxiliar,
                                                        observaciones
                                                        )
                		((select
                		DISTINCT(pv.nombre),
                		NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from vef.tventa ven
                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                        and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                        and ven.id_auxiliar_anticipo is null
                        and ven.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                        distinct (pv.nombre),
                        NULL::date as fecha_factura,
                        '0'::varchar as nro_factura,
                        NULL::varchar as tipo_documento,
                        null::varchar as ruta,
                        pv.nombre as pasajero,
                        NULL::numeric as debe,
                        NULL::numeric as haber,
                        NULL::varchar as tipo_factura,
                        NULL::varchar as cuenta_auxiliar,
                        NULL::varchar as observaciones
                        from obingresos.tboleto_amadeus bol
                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado = 'revisado' and bol.voided = 'no'
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_punto_venta = v_parametros.id_punto_venta)

                        union

                        (select
                              distinct (pv.nombre),
                              NULL::date as fecha_factura,
                              '0'::varchar as nro_factura,
                              NULL::varchar as tipo_documento,
                              null::varchar as ruta,
                              pv.nombre as pasajero,
                              NULL::numeric as debe,
                              NULL::numeric as haber,
                              NULL::varchar as tipo_factura,
                              NULL::varchar as cuenta_auxiliar,
                              NULL::varchar as observaciones
                        from obingresos.tboleto_stage bol
                        inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                        where bol.estado_reg = 'activo' and bol.voided = 'no' and bolfp.id_auxiliar is not null
                        and aux.codigo_auxiliar = v_parametros.codigo_auxiliar and bol.fecha_emision between v_parametros.desde::date and v_parametros.hasta::date
                        and bol.id_agencia = v_parametros.id_punto_venta)

                        union

                        (select
                                DISTINCT(pv.nombre),
                                NULL::date as fecha_factura,
                                '0'::varchar as nro_factura,
                                NULL::varchar as tipo_documento,
                                null::varchar as ruta,
                                pv.nombre as pasajero,
                                NULL::numeric as debe,
                                NULL::numeric as haber,
                                NULL::varchar as tipo_factura,
                                NULL::varchar as cuenta_auxiliar,
                                NULL::varchar as observaciones
                                from vef.tventa ven
                                inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and (ven.tipo_factura = 'recibo' or ven.tipo_factura = 'recibo_manual')
                                and ven.id_auxiliar_anticipo is not null
                                and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
                                and ven.fecha between v_parametros.desde::date and v_parametros.hasta::date
                                and ven.id_punto_venta = v_parametros.id_punto_venta)

                        /*Aumentando para los depositos*/
                        UNION

                            (select
                                  distinct ('DEPOSITO'),
                                  NULL::date as fecha_factura,
                                  '0'::varchar as nro_factura,
                                  NULL::varchar as tipo_documento,
                                  null::varchar as ruta,
                                  'DEPOSITOS'::VARCHAR as pasajero,
                                  NULL::numeric as debe,
                                  NULL::numeric as haber,
                                  NULL::varchar as tipo_factura,
                                  NULL::varchar as cuenta_auxiliar,
                                  NULL::varchar as observaciones
                            from obingresos.tdeposito depo
                            inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                            and depo.fecha between v_parametros.desde::date and v_parametros.hasta::date
                            where aux.codigo_auxiliar = v_parametros.codigo_auxiliar)
                        /*******************************/

                        );


                 	end if;

				  END IF;

                  end if;

                end if;



                v_consulta:='select
                                          count(tipo_factura),
                                          sum(COALESCE (debe,0)) as total_debe,
                                          sum(COALESCE (haber,0)) as total_haber
                                  from facturas_recibos_temporal';

				if (v_parametros.generar_reporte = 'no') then
                	v_consulta:=v_consulta||' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
				end if;

                return v_consulta;
/***************/ELSE

            	/*Recuperamos la moneda base para sacar la conversion*/
                select mon.id_moneda
                	   into
                       v_id_moneda_base
                from param.tmoneda mon
                where mon.tipo_moneda = 'base';
                /*****************************************************/

                create temp table reporte_resumen_totalizado_cta_cte (
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
                CREATE INDEX treporte_resumen_totalizado_cta_cte_fecha_factura ON reporte_resumen_totalizado_cta_cte
                USING btree (fecha_factura);

                CREATE INDEX treporte_resumen_totalizado_cta_cte_nro_factura ON reporte_resumen_totalizado_cta_cte
                USING btree (nro_factura);

                CREATE INDEX treporte_resumen_totalizado_cta_cte_tipo_factura ON reporte_resumen_totalizado_cta_cte
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

                /*Aqui filtro del punto de venta*/

                if (v_parametros.id_punto_venta != 0) then
                	v_punto_venta_fac = 'ven.id_punto_venta = '||v_parametros.id_punto_venta;
                    v_punto_venta_bol = 'bol.id_punto_venta = '||v_parametros.id_punto_venta;
                    v_punto_venta_bol_externa = 'bol.id_agencia = '||v_parametros.id_punto_venta;
                /*Para Recuperar Datos de tfactura para carga*/

                	select pv.codigo
                    	    into
                            v_codigo_pv
                    from vef.tpunto_venta pv
                    where pv.id_punto_venta = v_parametros.id_punto_venta and
                    pv.tipo = 'carga';

                    v_punto_venta_carga = 'codigo_punto_venta = '''||v_codigo_pv||'''';


                else
                	v_punto_venta_fac = '0=0';
                    v_punto_venta_bol = '0=0';
                    v_punto_venta_carga = '0=0';
                    v_punto_venta_bol_externa = '0=0';

                end if;


                /********************************/


                /*CUENTAS CORRIENTES DEBE RECIBOS FACTURAS ERP*/


                v_insertar_ventas = 'insert into reporte_resumen_totalizado_cta_cte (
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
                                              (''(''||aux.codigo_auxiliar||'') - ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                      from vef.tventa ven
                                      inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                                      inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                      inner join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                      where ven.estado_reg = ''activo'' and ven.estado = ''finalizado''
                                      and ven.fecha between '''||v_parametros.desde::date||''' and '''||v_parametros.hasta::date||'''
                                      and '||v_codigo_auxiliar_venta||' and '||v_punto_venta_fac||'';

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






                v_insertar_boletos = 'insert into reporte_resumen_totalizado_cta_cte (
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
                                        (select
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
                                                (''(''||aux.codigo_auxiliar||'') - ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                        from obingresos.tboleto_amadeus bol
                                        inner join obingresos.tboleto_amadeus_forma_pago bolfp on bolfp.id_boleto_amadeus = bol.id_boleto_amadeus
                                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bolfp.id_medio_pago
                                        inner join obingresos.tforma_pago_pw fpp on fpp.id_forma_pago_pw = mp.forma_pago_id
                                        inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                        left join temporal_rutas_boletos ru on ru.nro_factura = bol.nro_boleto::numeric
                                        inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                        where bol.estado_reg = ''activo'' and bol.estado = ''revisado'' and bol.voided = ''no''
                                        and bol.fecha_emision between '''||v_parametros.desde::date||''' and '''||v_parametros.hasta::date||'''
                                        and '||v_codigo_auxiliar_venta||' and '||v_punto_venta_bol||')

                                        union

                                        (select
                                                bol.fecha_emision,
                                                bol.nro_boleto as nro_factura,
                                                bol.nro_boleto as nro_documento,
                                                ''''::varchar as ruta,
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
                                                (''(''||aux.codigo_auxiliar||'') - ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                          from obingresos.tboleto_stage bol
                                          inner join obingresos.tboleto_forma_pago_stage bolfp on bolfp.id_boleto = bol.id_boleto
                                          inner join conta.tauxiliar aux on aux.id_auxiliar = bolfp.id_auxiliar
                                          inner join vef.tpunto_venta pv on pv.id_punto_venta = bol.id_punto_venta
                                          where bol.estado_reg = ''activo'' and bol.voided = ''no'' and bolfp.id_auxiliar is not null
                                          and bol.fecha_emision between '''||v_parametros.desde::date||''' and '''||v_parametros.hasta::date||'''
                                          and '||v_codigo_auxiliar_venta||' and '||v_punto_venta_bol_externa||')';

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

                insert into reporte_resumen_totalizado_cta_cte (
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
                        ('('||aux.codigo_auxiliar||') - '||aux.nombre_auxiliar) as cuenta_auxiliar
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
                  and '||v_punto_venta_carga||'
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

               v_insertar_depositos = 'insert into reporte_resumen_totalizado_cta_cte (
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
                                                ''DEPÓSITO''::varchar AS nombre,
                                                (''(''||aux.codigo_auxiliar||'') - ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                        from obingresos.tdeposito depo
                                        inner join conta.tauxiliar aux on aux.id_auxiliar = depo.id_auxiliar
                                        where depo.fecha between '''||v_parametros.desde::date||''' and '''||v_parametros.hasta||'''
                                        and '||v_codigo_auxiliar_venta||'
                                        and depo.estado_reg = ''activo''';

                execute v_insertar_depositos;
               /***************************************************************************************************/
               /*Aqui los recibos anticipo*/

               v_insertar_aticipos = 'insert into reporte_resumen_totalizado_cta_cte (
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
                                                (''(''||aux.codigo_auxiliar||'') - ''||aux.nombre_auxiliar) as cuenta_auxiliar
                                        from vef.tventa ven
                                        inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                                        inner join conta.tauxiliar aux on aux.id_auxiliar = ven.id_auxiliar_anticipo
                                        inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                        where ven.estado_reg = ''activo'' and ven.estado = ''finalizado''
                                        and (ven.tipo_factura = ''recibo'' or ven.tipo_factura = ''recibo_manual'')
                                        and ven.id_auxiliar_anticipo is not null
                                        and ven.fecha between '''||v_parametros.desde::date||''' and '''||v_parametros.hasta||'''
                                        and '||v_codigo_auxiliar_venta||'
                                        and '||v_punto_venta_fac||'';

               	/**********************************************************************************************************/

                execute v_insertar_aticipos;


                if (v_parametros.id_punto_venta = 0) then

                	v_consulta := 'select
                                      count (distinct (cuenta_auxiliar)),
                                      sum (debe) as total_debe,
                                      sum (haber) as total_haber
                                from reporte_resumen_totalizado_cta_cte';

                else

                	v_consulta := 'WITH datos as (select
                                                  null::DATE AS fecha_factura,
                                                  null::numeric as nro_factura,
                                                  null::VARCHAR  AS nro_documento,
                                                  NULL::varchar as ruta,
                                                  NULL::varchar as pasajero,
                                                  sum (debe) as total_debe,
                                                  sum (haber) as total_haber,
                                                  ''totalizado''::VARCHAR as tipo_factura,
                                                  punto_venta,
                                                  cuenta_auxiliar
                                                  from reporte_resumen_totalizado_cta_cte
                                                  group by cuenta_auxiliar,punto_venta)
                                  select
                                  count (cuenta_auxiliar),
                                  sum (total_debe) as total_debe,
                                  sum (total_haber) as total_haber
                                  from datos';


                end if;






                return v_consulta;

                END IF;






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
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
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
                        where ven.estado = 'finalizado' and ven.estado_reg = 'activo' and aux.codigo_auxiliar = v_parametros.codigo_auxiliar
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
