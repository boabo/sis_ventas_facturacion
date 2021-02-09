CREATE OR REPLACE FUNCTION vef.ft_rep_comisionistas_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_rep_comisionistas_sel
 DESCRIPCION:   Funcion que hace conexion para recuperar datos de comisionistas
 AUTOR: 		 (Ismael Valdivia)
 FECHA:	        25-01-2020 11:30:09
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
    v_lista_nits		integer;

BEGIN

	v_nombre_funcion = 'vef.ft_rep_comisionistas_sel';
    v_parametros = pxp.f_get_record(p_tabla);


    /*********************************
    #TRANSACCION:  'VEF_REPCOMISI_SEL'
    #DESCRIPCION:	Reporte de Comisionistas
    #AUTOR:		Ismael Valdivia
    #FECHA:		25-01-2021 11:30:09
    ***********************************/
    if(p_transaccion = 'VEF_REPCOMISI_SEL')then
    	begin

        	v_filtro_temp = '';

            v_monto_impuestos = pxp.f_get_variable_global('vef_acumulativo_impuestos');

             if pxp.f_existe_parametro(p_tabla, 'tipo_reporte') then
             	if (v_parametros.tipo_reporte = 'per_natu' or v_parametros.tipo_reporte = 'det_vent_natu') then
                	v_filtro_temp = 'natural_simplificado = ''N''';
                    v_filtro_totales = 'N';
                elsif (v_parametros.tipo_reporte = 'regimen_simpli' or v_parametros.tipo_reporte = 'det_vent_rts') then
                	v_filtro_temp = 'natural_simplificado = ''S''';
                    v_filtro_totales = 'S';
                end if;
             end if;


            if pxp.f_existe_parametro(p_tabla, 'filtro_sql') then
              if 'periodo' = v_parametros.filtro_sql then
                  select tper.fecha_ini, tper.fecha_fin
                  into v_registros
                  from param.tperiodo tper
                  where tper.id_periodo = v_parametros.id_periodo;

                  select param.f_literal_periodo(v_parametros.id_periodo) into v_literal_mes;


                   /*Aqui recuperaremos las condiciones para filtrar mayores a 136000bs*/

                  select
                         MIN (comi.fecha_ini)
                         into
                         v_fecha_ini
                  from vef.tacumulacion_comisionistas comi
                  where comi.total_acumulado >= v_monto_impuestos and comi.fecha_ini >= v_registros.fecha_ini and comi.fecha_fin <= v_registros.fecha_fin
                  and comi.natural_simplificado = ''||v_filtro_totales||'';


                  select
                         MAX (comi.fecha_fin)
                         into
                         v_fecha_fin
                  from vef.tacumulacion_comisionistas comi
                  where comi.total_acumulado >= v_monto_impuestos and comi.fecha_ini >= v_registros.fecha_ini and comi.fecha_fin <= v_registros.fecha_fin
                  and comi.natural_simplificado = ''||v_filtro_totales||'';

                  --v_filtro = 'fecha_factura between '''||v_registros.fecha_ini||'''::date and '''||v_registros.fecha_fin||'''::date';

              	  v_filtro = 'fecha_factura between '''||v_fecha_ini||'''::date and '''||v_fecha_fin||'''::date';


                  v_gestion = date_part('year', v_registros.fecha_ini);
            	  v_periodo = date_part('month', v_registros.fecha_ini);


                  /*Aqui para obtener datos de la misma gestion*/
                  v_gestion_ini = date_part('year', v_registros.fecha_ini);
                  v_gestion_fin = date_part('year', v_registros.fecha_fin);
                  /*********************************************/

              elsif 'fechas' = v_parametros.filtro_sql then

                 -- v_filtro = 'fecha_factura between '''||v_parametros.fecha_ini||'''::date and '''||v_parametros.fecha_fin||'''::date';

                  v_gestion = date_part('year', v_parametros.fecha_ini);
            	  v_periodo = date_part('month', v_parametros.fecha_ini);


                  /*Aqui para obtener datos de la misma gestion*/
                  v_gestion_ini = date_part('year', v_parametros.fecha_ini);
                  v_gestion_fin = date_part('year', v_parametros.fecha_fin);
                  /*********************************************/

                  select tper.id_periodo
                  into v_parametros.id_periodo
                  from param.tperiodo tper
                  where tper.fecha_ini = v_parametros.fecha_ini;

                  select param.f_literal_periodo(v_parametros.id_periodo) into v_literal_mes;


                  /*Aqui recuperaremos las condiciones para filtrar mayores a 136000bs*/

                  select
                         MIN (comi.fecha_ini)
                         into
                         v_fecha_ini
                  from vef.tacumulacion_comisionistas comi
                  where comi.total_acumulado >= v_monto_impuestos and comi.fecha_ini >= v_parametros.fecha_ini and comi.fecha_fin <= v_parametros.fecha_fin
                  and comi.natural_simplificado = ''||v_filtro_totales||'';


                  select
                         MAX (comi.fecha_fin)
                         into
                         v_fecha_fin
                  from vef.tacumulacion_comisionistas comi
                  where comi.total_acumulado >= v_monto_impuestos and comi.fecha_ini >= v_parametros.fecha_ini and comi.fecha_fin <= v_parametros.fecha_fin
                  and comi.natural_simplificado = ''||v_filtro_totales||'';

                  v_filtro = 'fecha_factura between '''||v_fecha_ini||'''::date and '''||v_fecha_fin||'''::date';

                 /* select comi.nit,
                         comi.id_periodo,
                         comi.fecha_ini,
                         comi.fecha_fin
                  into
                  		 v_datos_mayores
                  from vef.tacumulacion_comisionistas comi
                  where comi.total_acumulado >= 136000 and comi.fecha_ini >= v_parametros.fecha_ini and comi.fecha_fin <= v_parametros.fecha_fin; */
                  /********************************************************************/
              end if;
            end if;


            /*Aqui aumentando para que solo filtre por periodos*/
            if pxp.f_existe_parametro(p_tabla, 'id_gestion') then

                	if (v_parametros.id_gestion is not null) then

                		v_filtro_ges = 'comi.id_gestion = '||v_parametros.id_gestion;

                        select ges.gestion into v_gestion
                        from param.tgestion ges
                        where ges.id_gestion = v_parametros.id_gestion;


                    end if;

                end if;

            v_filtro_per_ini = '0=0';

                if pxp.f_existe_parametro(p_tabla, 'id_periodo_inicio') then

                	if (v_parametros.id_periodo_inicio is not null and v_parametros.id_periodo_final is not null) then

                    	select
                               MIN (comi.fecha_ini)
                               into
                               v_fecha_ini
                        from vef.tacumulacion_comisionistas comi
                        where comi.total_acumulado >= v_monto_impuestos and comi.id_periodo between v_parametros.id_periodo_inicio and v_parametros.id_periodo_final
                        and comi.natural_simplificado = ''||v_filtro_totales||'';

                    	select
                             MAX (comi.fecha_fin)
                             into
                             v_fecha_fin
                      from vef.tacumulacion_comisionistas comi
                      where comi.total_acumulado >= v_monto_impuestos and comi.id_periodo between v_parametros.id_periodo_inicio and v_parametros.id_periodo_final
                      and comi.natural_simplificado = ''||v_filtro_totales||'';

                      select param.f_literal_periodo(v_parametros.id_periodo_inicio) into v_literal_mes_inicio;

                      select param.f_literal_periodo(v_parametros.id_periodo_final) into v_literal_mes_final;

                      v_gestion_ini = date_part('year', v_fecha_ini);
                      v_periodo_ini = date_part('month', v_fecha_ini);

                      v_gestion_fin = date_part('year', v_fecha_fin);
                      v_periodo_fin = date_part('month', v_fecha_fin);

                      --v_filtro = 'fecha_factura between '''||v_fecha_ini||'''::date and '''||v_fecha_fin||'''::date';



                    end if;

                end if;
            /***************************************************/



            if (v_gestion_ini != v_gestion_fin) then
            	raise exception 'Solo se puede recuperar información de la misma Gestión favor verifique los datos.';
            end if;

            if (v_gestion < 2021) then
            	raise exception 'No se puede generar el reporte debido a que la información en el Sistema ERP es desde la gestión 2021, Favor verifique los datos.';
            end if;

            /****************Crearemos la Tabla temporal almacenable**********************/
            create temp table temporal_comisionistas (
                                                        fecha_factura date,
                                                        desc_ruta varchar ,
                                                        sistema_origen varchar,
                                                        nit numeric,
                                                        razon_social varchar,
                                                        carnet_ide varchar,
                                                        cantidad numeric,
                                                        precio_unitario numeric,
                                                        precio_total NUMERIC,
                                                        natural_simplificado varchar,
                                                        nro_factura numeric
                                                      )on commit drop;
            CREATE INDEX ttemporal_comisionistas_fecha_factura ON temporal_comisionistas
            USING btree (fecha_factura);

            CREATE INDEX ttemporal_comisionistas_natural_simplificado ON temporal_comisionistas
            USING btree (natural_simplificado);

            CREATE INDEX ttemporal_comisionistas_nit ON temporal_comisionistas
            USING btree (nit);

            CREATE INDEX ttemporal_comisionistas_sistema_origen ON temporal_comisionistas
            USING btree (sistema_origen);



            create temp table temporal_data_comisionistas (
                                                        fecha_factura date,
                                                        desc_ruta varchar ,
                                                        sistema_origen varchar,
                                                        nit_ci_cli numeric,
                                                        razon_social_cli varchar,
                                                        cantidad numeric,
                                                        total_venta NUMERIC,
                                                        importe_exento NUMERIC,
                                                        nro_factura numeric
                                                      ) on commit drop;
            CREATE INDEX ttemporal_data_comisionistas_fecha_factura ON temporal_data_comisionistas
            USING btree (fecha_factura);

            CREATE INDEX ttemporal_data_comisionistas_natural_nit ON temporal_data_comisionistas
            USING btree (nit_ci_cli);

            CREATE INDEX ttemporal_data_comisionistas_nro_factura ON temporal_data_comisionistas
            USING btree (nro_factura);

            v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();


            select count(comi.nit)
            into
            v_lista_nits
            from vef.tacumulacion_comisionistas comi
            where comi.total_acumulado >= v_monto_impuestos and comi.id_periodo between v_parametros.id_periodo_inicio and v_parametros.id_periodo_final
            and comi.natural_simplificado = ''||v_filtro_totales||'';

            if (v_lista_nits = 0) then
            	raise exception 'No se encontraron NITS que superen el monto de: %',to_char(v_monto_impuestos::numeric,'999G999G999G999D99');
            end if;


          	--raise exception 'Aqui la cadena %',v_cadena_cnx;
            /*****************************************************************************/
             insert into temporal_data_comisionistas (
                                                        fecha_factura,
                                                        desc_ruta,
                                                        sistema_origen,
                                                        nit_ci_cli,
                                                        razon_social_cli,
                                                        cantidad,
                                                        total_venta,
                                                        importe_exento,
                                                        nro_factura)
                SELECT *
                FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                            'select
                            		fecha_factura,
                                    desc_ruta,
                                    sistema_origen,
                                    nit_ci_cli,
                                    razon_social_cli,
                                    1,
                                    importe_total_venta,
                                    importe_otros_no_suj_iva,
                                    nro_factura
                             from sfe.tfactura where estado_reg = ''activo''
                             and ((case when
                                          nit_ci_cli = '''' then
                                          0::numeric
                                          else
                                              trim (nit_ci_cli)::numeric
                                          end)) != 1 and

                                 ((case when
                                          nit_ci_cli = '''' then
                                          0::numeric
                                          else
                                              trim (nit_ci_cli)::numeric
                                          end)) != 0 and
                             ((case when
                                          nit_ci_cli = '''' then
                                          0::numeric
                                          else
                                              trim (nit_ci_cli)::numeric
                                          end)) IN ('||(select LIST (distinct(comi.nit))
                                                        from vef.tacumulacion_comisionistas comi
                                                        where comi.total_acumulado >= v_monto_impuestos and comi.id_periodo between v_parametros.id_periodo_inicio and v_parametros.id_periodo_final
                                                        and comi.natural_simplificado = ''||v_filtro_totales||'')||')

                             order by fecha_factura ASC, nro_factura ASC
                             ')
                AS tdatos(fecha_factura date,
                          desc_ruta varchar,
                          sistema_origen varchar,
                          nit_ci_cli numeric,
                          razon_social_cli varchar,
                          cantidad integer,
                          total_venta numeric,
                          importe_exento numeric,
                          nro_factura numeric);


            /*Recuperamos el nit y la razon social de la empresa*/
            select tem.nombre, tem.nit
            into v_registros
            from param.tempresa tem
            where tem.codigo = '578';
            /****************************************************/

            /*Aqui separamos para que saque los reportes sueltos*/

            if (v_parametros.tipo_reporte = 'per_natu' or v_parametros.tipo_reporte = 'regimen_simpli') then

            /*Recupera la información de todas las facturas y boletos emitidos*/
           	for v_datos in ( select *
            			     from temporal_data_comisionistas ) LOOP

              	/*Obtenemos la cantidad de caracteres del NIT para identificar natural o simplificado*/
                SELECT length(v_datos.nit_ci_cli::varchar) into v_cantidad_nit;
                          if (v_cantidad_nit > 8) then
                              v_natural_sumpli = 'S';
                          else
                              v_natural_sumpli = 'N';
                          end if;
                /*************************************************************************************/


                /*Recuperamos el Total de la venta si es boletos solo total, si es facturas restar exento*/
                IF (v_datos.sistema_origen = 'ERP') then
                	v_venta_total = (v_datos.total_venta - COALESCE (v_datos.importe_exento,0));
                    v_desc_sistema = 'SISTEMA ERP';
                elsif (v_datos.sistema_origen = 'CARGA') then
                	v_venta_total = v_datos.total_venta;
                    v_desc_sistema = 'SISTEMA DE CARGA';
                else
                	v_venta_total = v_datos.total_venta;
                    v_desc_sistema = 'SISTEMA AMADEUS';
                end if;
                /*****************************************************************************************/

                /*En base a esta data separamos los datos a mostrar*/
              /* for v_datos_acumulado in ( select comi.nit::numeric,
               							comi.fecha_ini::date,
                                        comi.fecha_fin::date
                                from vef.tacumulacion_comisionistas comi
                                where comi.total_acumulado >= 136000 and comi.id_periodo between v_parametros.id_periodo_inicio and v_parametros.id_periodo_final
                                and comi.natural_simplificado = ''||v_filtro_totales||''
                                order by comi.nit ASC) loop*/

                     --if (v_datos.nit_ci_cli::numeric = v_datos_acumulado.nit) then
                     --

                       insert into temporal_comisionistas (
                                                        fecha_factura,
                                                        desc_ruta ,
                                                        sistema_origen,
                                                        nit,
                                                        razon_social,
                                                        carnet_ide,
                                                        cantidad,
                                                        precio_unitario,
                                                        precio_total,
                                                        natural_simplificado,
                                                        nro_factura) values
                                                       ( v_datos.fecha_factura,
                                                         v_datos.desc_ruta,
                                                         v_desc_sistema,
                                                         v_datos.nit_ci_cli::numeric,
                                                         v_datos.razon_social_cli,
                                                         'CI',
                                                         v_datos.cantidad,
                                                         v_venta_total,
                                                         v_venta_total,
                                                         v_natural_sumpli,
                                                         v_datos.nro_factura::numeric
                                                       );
                      -- end if;
            	--end loop;
               /***************************************************/

            end loop;

            /*Aqui recuperamos el total general*/
            v_total_general = '0';
            /***********************************/

            /*Devolvemos la data recuperada*/

            v_consulta := 'select fecha_factura,
            	   				  desc_ruta,
                                  sistema_origen,
                                  nit,
                                  razon_social,
                                  carnet_ide,
                                  cantidad,
                                  to_char(precio_unitario::numeric,''999G999G999G999D99'')::varchar,
                                  to_char(precio_total::numeric,''999G999G999G999D99'')::varchar,
                                  natural_simplificado,
                                  nro_factura,
                                  '''||v_registros.nombre||'''::varchar as razon_empresa,
              					  '''||v_registros.nit||'''::varchar as nit_empresa,
                                  '||v_gestion_ini||'::integer as gestion,
                                  '''||v_periodo_ini||'''::varchar as periodo_num_ini,
                                  '''||v_periodo_fin||'''::varchar as periodo_num_fin,
                                  '''||v_literal_mes_inicio||'''::varchar as periodo_literal_inicio,
                                  '''||v_literal_mes_final||'''::varchar as periodo_literal_fin,
                                  '''||v_total_general||'''::varchar as total_general
            			   from temporal_comisionistas
                           where nit::numeric not in (
                                	select nc.nit_ci::numeric
                                 	from vef.tnits_no_considerados nc
                                 ) and '||v_filtro_temp||'
                           order by nit ASC, fecha_factura ASC, nro_factura ASC';


            /*******************************/
            else

            	/*Para comisionistas sirve totales y cabecera*/

                create temp table temporal_comisionistas_detalle (
                                                            fecha_factura date,
                                                            desc_ruta varchar ,
                                                            sistema_origen varchar,
                                                            nit numeric,
                                                            razon_social varchar,
                                                            carnet_ide varchar,
                                                            cantidad numeric,
                                                            precio_unitario numeric,
                                                            precio_total NUMERIC,
                                                            natural_simplificado varchar,
                                                            nro_factura numeric
                                                          )on commit drop;
                CREATE INDEX ttemporal_comisionistas_detalle_fecha_factura ON temporal_comisionistas_detalle
                USING btree (fecha_factura);

                CREATE INDEX ttemporal_comisionistas_detalle_natural_simplificado ON temporal_comisionistas_detalle
                USING btree (natural_simplificado);

                CREATE INDEX ttemporal_comisionistas_detalle_nit ON temporal_comisionistas_detalle
                USING btree (nit);

                CREATE INDEX ttemporal_comisionistas_detalle_sistema_origen ON temporal_comisionistas_detalle
                USING btree (sistema_origen);

                for v_datos in ((SELECT  NULL as fecha_factura,
                        'cabecera'::varchar as desc_ruta,
                        'cabecera'::varchar as sistema_origen,
                        tem.nit_ci_cli as nit_ci_cli,
                        'cabecera'::varchar as razon_social_cli,
                        1::numeric as cantidad,
                        null::numeric as total_venta,
                        0::numeric as importe_exento,
                        0::numeric as nro_factura
                        FROM temporal_data_comisionistas tem
                        group by tem.nit_ci_cli

                UNION ALL
                        SELECT *
                FROM temporal_data_comisionistas

                UNION ALL

                SELECT 	now()::date as fecha_factura,
                        'total'::varchar as desc_ruta,
                        'total'::varchar as sistema_origen,
                        tempo.nit_ci_cli as nit_ci_cli,
                        'total'::varchar as razon_social_cli,
                        null::numeric as cantidad,
                       sum (tempo.total_venta) as total_venta,
                       0::numeric as importe_exento,
                        0::numeric as nro_factura
                FROM temporal_data_comisionistas tempo
                group by tempo.nit_ci_cli

                )order by nit_ci_cli ASC, total_venta asc NULLS First, cantidad asc NULLS LAST) loop


                	/*Obtenemos la cantidad de caracteres del NIT para identificar natural o simplificado*/
                    SELECT length(v_datos.nit_ci_cli::varchar) into v_cantidad_nit;
                              if (v_cantidad_nit > 8) then
                                  v_natural_sumpli = 'S';
                              else
                                  v_natural_sumpli = 'N';
                              end if;
                    /*************************************************************************************/


                    /*Recuperamos el Total de la venta si es boletos solo total, si es facturas restar exento*/
                    IF (v_datos.sistema_origen = 'ERP') then
                        v_venta_total = (v_datos.total_venta - COALESCE (v_datos.importe_exento,0));
                        v_desc_sistema = 'SISTEMA ERP';
                    elsif (v_datos.sistema_origen = 'CARGA') then
                        v_venta_total = v_datos.total_venta;
                        v_desc_sistema = 'SISTEMA DE CARGA';
                    else
                        v_venta_total = v_datos.total_venta;
                        v_desc_sistema = 'SISTEMA AMADEUS';
                    end if;

                   insert into temporal_comisionistas (
                                                      fecha_factura,
                                                      desc_ruta ,
                                                      sistema_origen,
                                                      nit,
                                                      razon_social,
                                                      carnet_ide,
                                                      cantidad,
                                                      precio_unitario,
                                                      precio_total,
                                                      natural_simplificado,
                                                      nro_factura) values
                                                     ( v_datos.fecha_factura,
                                                       v_datos.desc_ruta,
                                                       v_desc_sistema,
                                                       v_datos.nit_ci_cli::numeric,
                                                       v_datos.razon_social_cli,
                                                       'CI',
                                                       v_datos.cantidad,
                                                       v_venta_total,
                                                       v_venta_total,
                                                       v_natural_sumpli,
                                                       v_datos.nro_factura::numeric
                                                     );



                end loop;

                /*Aqui recuperamos el total general de los datos*/
                select to_char(sum(comi.precio_total)::numeric,'999G999G999G999D99')
                		into
                       v_total_general
                from temporal_comisionistas comi
                where comi.razon_social = 'total';
				/*************************************************/

                /*Devolvemos la data recuperada*/

                v_consulta := 'select fecha_factura,
                                      desc_ruta,
                                      sistema_origen,
                                      nit,
                                      razon_social,
                                      carnet_ide,
                                      cantidad,
                                      to_char(precio_unitario::numeric,''999G999G999G999D99'')::varchar,
                                      to_char(precio_total::numeric,''999G999G999G999D99'')::varchar,
                                      natural_simplificado,
                                      nro_factura,
                                      '''||v_registros.nombre||'''::varchar as razon_empresa,
                                      '''||v_registros.nit||'''::varchar as nit_empresa,
                                      '||v_gestion_ini||'::integer as gestion,
                                      '''||v_periodo_ini||'''::varchar as periodo_num_ini,
                                      '''||v_periodo_fin||'''::varchar as periodo_num_fin,
                                      '''||v_literal_mes_inicio||'''::varchar as periodo_literal_inicio,
                                      '''||v_literal_mes_final||'''::varchar as periodo_literal_fin,
                                      '''||v_total_general||'''::varchar as total_general
                               from temporal_comisionistas
                               where nit::numeric not in (
                                        select nc.nit_ci::numeric
                                        from vef.tnits_no_considerados nc
                                     ) and '||v_filtro_temp||'
                               order by nit ASC,fecha_factura ASC NULLS First, cantidad NULLS LAST, nro_factura ASC';


                /*******************************/

            end if;
            /******************************************************************/

            --Devuelve la respuesta
            return v_consulta;

        end;

        /*********************************
        #TRANSACCION:  'VEF_RESUCOMISI_SEL'
        #DESCRIPCION:	Reporte de Comisionistas
        #AUTOR:		Ismael Valdivia
        #FECHA:		25-01-2021 11:30:09
        ***********************************/
        elsif(p_transaccion = 'VEF_RESUCOMISI_SEL')then
            begin

                v_filtro_temp = '';
                v_monto_impuestos = pxp.f_get_variable_global('vef_acumulativo_impuestos');
                 if pxp.f_existe_parametro(p_tabla, 'tipo_reporte') then
                    if (v_parametros.tipo_reporte = 'res_vent_natu') then
                        v_filtro_temp = 'comi.natural_simplificado = ''N''';
                    elsif (v_parametros.tipo_reporte = 'res_vent_rts') then
                        v_filtro_temp = 'comi.natural_simplificado = ''S''';
                    end if;
                 end if;

                v_filtro_ges = '0=0';

                if pxp.f_existe_parametro(p_tabla, 'id_gestion') then

                	if (v_parametros.id_gestion is not null) then

                		v_filtro_ges = 'comi.id_gestion = '||v_parametros.id_gestion;

                        select ges.gestion into v_gestion
                        from param.tgestion ges
                        where ges.id_gestion = v_parametros.id_gestion;


                    end if;

                end if;



                v_filtro_per_ini = '0=0';

                if pxp.f_existe_parametro(p_tabla, 'id_periodo_inicio') then

                	if (v_parametros.id_periodo_inicio is not null and v_parametros.id_periodo_final is not null) then

                		v_filtro_per_ini = 'comi.id_periodo between '||v_parametros.id_periodo_inicio||' and '||v_parametros.id_periodo_final;

                         select param.f_literal_periodo(v_parametros.id_periodo_inicio) into v_literal_mes_inicio;

                         select param.f_literal_periodo(v_parametros.id_periodo_final) into v_literal_mes_final;

                    end if;

                end if;

                /*Recuperamos el nit y la razon social de la empresa*/
                select tem.nombre, tem.nit
                into v_registros
                from param.tempresa tem
                where tem.codigo = '578';
                /****************************************************/

                /*Aqui para obtener datos de la misma gestion*/
                if (v_gestion < 2021) then
                	raise exception 'No se puede generar el reporte debido a que la información en el Sistema ERP es desde la gestión 2021, Favor verifique los datos.';
                end if;
                /*********************************************/


                if (v_parametros.tipo_reporte = 'res_vent_natu') then
                        select
                              count (comi.nit)
                              into
                              v_lista_nits
                        from vef.tacumulacion_comisionistas comi
                        inner join param.tgestion ge on ge.id_gestion = comi.id_gestion
                        where nit::numeric not in (
                        select nc.nit_ci::numeric
                        from vef.tnits_no_considerados nc
                        ) and comi.id_gestion = v_parametros.id_gestion and comi.id_periodo between v_parametros.id_periodo_inicio and v_parametros.id_periodo_final and comi.natural_simplificado = 'N'
                        and comi.total_acumulado >= v_monto_impuestos;
                    elsif (v_parametros.tipo_reporte = 'res_vent_rts') then
                        select
                                  count (comi.nit)
                                  into
                                  v_lista_nits
                            from vef.tacumulacion_comisionistas comi
                            inner join param.tgestion ge on ge.id_gestion = comi.id_gestion
                            where nit::numeric not in (
                            select nc.nit_ci::numeric
                            from vef.tnits_no_considerados nc
                            ) and comi.id_gestion = v_parametros.id_gestion and comi.id_periodo between v_parametros.id_periodo_inicio and v_parametros.id_periodo_final and comi.natural_simplificado = 'S'
                            and comi.total_acumulado >= v_monto_impuestos;
                    end if;





                if (v_lista_nits = 0) then
            	raise exception 'No se encontraron NITS que superen el monto de: %.',to_char(v_monto_impuestos::numeric,'999G999G999G999D99');
            	end if;


                /*Devolvemos la data recuperada*/

                v_consulta := 'select
                                        comi.nit,
                                        comi.total_acumulado,
                                        (param.f_literal_periodo(comi.id_periodo)||''-''||ge.gestion)::varchar as mes_envio,
                                        '||v_gestion||'::integer as gestion,
                                        '''||v_literal_mes_inicio||'''::varchar as mes_inicio,
                                        '''||v_literal_mes_final||'''::varchar as mes_final
                                from vef.tacumulacion_comisionistas comi
                                inner join param.tgestion ge on ge.id_gestion = comi.id_gestion
                               	where nit::numeric not in (
                                        select nc.nit_ci::numeric
                                        from vef.tnits_no_considerados nc
                                     ) and '||v_filtro_ges||' and '||v_filtro_per_ini||' and '||v_filtro_temp||'
                                and comi.total_acumulado >= '||v_monto_impuestos||'
                               order by comi.nit::numeric ASC, comi.id_periodo ASC
                               ';


                /*******************************/

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

ALTER FUNCTION vef.ft_rep_comisionistas_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
