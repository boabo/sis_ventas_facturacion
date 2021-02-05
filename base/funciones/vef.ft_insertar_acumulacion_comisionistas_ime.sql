CREATE OR REPLACE FUNCTION vef.ft_insertar_acumulacion_comisionistas_ime (
)
RETURNS varchar AS
$body$
DECLARE
	v_consulta    		varchar;
	v_registros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
    v_datos					record;
	v_id_periodo			integer;
    v_insertar				record;

    v_fecha_ini				date;
	v_fecha_fin				date;
    v_anio				integer;
    v_cantidad_nit		integer;
    v_natural_sumpli	varchar;
    v_id_gestion		integer;
    v_fecha_ini_periodo	date;
   	v_maximo_periodo	integer;
    v_id_periodo_min	integer;
    v_fecha_inicial		date;
    v_periodos			record;
    v_estado_actualizar	varchar;
    v_periodos_abiertos	record;
    v_id_periodo_maximo	integer;
    v_insertar_acumulados	record;
    v_total				numeric;
    v_id_periodo_cerrado	integer;
    v_id_periodo_abierto	integer;
    v_existe_nit		integer;
    v_totales_sumar		numeric;
    v_venta_total		numeric;
    v_cadena_cnx		varchar;

BEGIN
	  v_nombre_funcion = 'vef.ft_insertar_acumulacion_comisionistas_ime';



      select EXTRACT(YEAR FROM now()::date) into v_anio;

      /*Aqui recuperamos la fecha del periodo*/

      select ges.id_gestion
      into v_id_gestion
      from param.tgestion ges
      where ges.gestion = v_anio;

      select MIN(per.fecha_ini)
      		 into
             v_fecha_ini_periodo
      from param.tgestion ges
      inner join param.tperiodo per on per.id_gestion = ges.id_gestion
      inner join conta.tperiodo_compra_venta cp on cp.id_periodo = per.id_periodo
      where ges.gestion = v_anio and (cp.estado = 'abierto' or cp.estado = 'cerrado_parcial')  and cp.id_depto = (select depo.id_depto
                                                                              from param.tdepto depo
                                                                              where depo.codigo = 'CON');
      /******************************************/
      for v_periodos in ( select ven.id_periodo,
                                            ven.estado
                                    from conta.tperiodo_compra_venta ven
                                    inner join param.tperiodo per on per.id_periodo = ven.id_periodo
                                    where ven.id_depto = (select depo.id_depto
                                                          from param.tdepto depo
                                                          where depo.codigo = 'CON')
                                    and per.id_gestion = v_id_gestion ) loop

                                            update vef.tacumulacion_comisionistas set
                                              estado = v_periodos.estado
                                            where id_periodo = v_periodos.id_periodo;

                                    end loop;

      /*Aqui Haremos el update de la tabla para periodos cerrados*/
      	delete from vef.tacumulacion_comisionistas
      	where estado = 'abierto' or estado = 'cerrado_parcial';
      /***********************************************************/


        create temp table temporal_acumulativo (
        										  id_periodo integer,
                                                  nit_ci_cli varchar ,
                                                  razon_social_cli varchar,
                                                  total_venta numeric,
                                                  natural_simplificado varchar,
                                                  id_gestion integer
                                                ) on commit drop;

        CREATE INDEX ttemporal_acumulativo_id_periodo ON temporal_acumulativo
        USING btree (id_periodo);

        CREATE INDEX ttemporal_acumulativo_nit_ci_cli ON temporal_acumulativo
        USING btree (nit_ci_cli);

        CREATE INDEX ttemporal_acumulativo_natural_simplificado ON temporal_acumulativo
        USING btree (natural_simplificado);

        CREATE INDEX ttemporal_acumulativo_id_gestion ON temporal_acumulativo
        USING btree (id_gestion);

            create temp table temporal_comisionistas_acumulado (
                                                        id_factura varchar,
                                                        fecha_factura date,
                                                        nro_factura varchar,
                                                        estado varchar,
                                                        desc_ruta varchar,
                                                        sistema_origen varchar,
                                                        nit_ci_cli numeric,
                                                        razon_social_cli varchar,
                                                        cantidad integer,
                                                        total_venta numeric,
                                                        importe_exento numeric
                                                      )on commit drop;
            CREATE INDEX ttemporal_comisionistas_acumulado_fecha_factura ON temporal_comisionistas_acumulado
            USING btree (fecha_factura);

            CREATE INDEX ttemporal_comisionistas_acumulado_nit ON temporal_comisionistas_acumulado
            USING btree (nit_ci_cli);

            CREATE INDEX ttemporal_comisionistas_acumulado_sistema_origen ON temporal_comisionistas_acumulado
            USING btree (sistema_origen);

            v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();

            insert into temporal_comisionistas_acumulado (
                                                        id_factura,
                                                        fecha_factura,
                                                        nro_factura,
                                                        estado,
                                                        desc_ruta,
                                                        sistema_origen,
                                                        nit_ci_cli,
                                                        razon_social_cli,
                                                        cantidad,
                                                        total_venta,
                                                        importe_exento)
            SELECT *
                FROM dblink(''||v_cadena_cnx||' options=-csearch_path=',
                            'select id_factura,
                                    fecha_factura,
                                    nro_factura,
                                    estado,
                                    desc_ruta,
                                    sistema_origen,
                                    nit_ci_cli,
                                    razon_social_cli,
                                    1,
                                    importe_total_venta,
                                    importe_otros_no_suj_iva
                             from sfe.tfactura where estado_reg = ''activo''
                             and
                             ((case when
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
                                          end)) != 0 and fecha_factura >= '''||v_fecha_ini_periodo||'''
                             order by fecha_factura ASC, nro_factura ASC
                             ')
                AS t1(id_factura varchar,
                      fecha_factura date,
                      nro_factura varchar,
                      estado varchar,
                      desc_ruta varchar,
                      sistema_origen varchar,
                      nit_ci_cli numeric,
                      razon_social_cli varchar,
                      cantidad integer,
                      total_venta numeric,
                      importe_exento numeric);




        for v_datos in (
                        SELECT *
                        from temporal_comisionistas_acumulado
                        ) LOOP


                select per.id_periodo,
                	   per.id_gestion
                into v_id_periodo,
                	 v_id_gestion
                from param.tperiodo per
                where v_datos.fecha_factura between per.fecha_ini and per.fecha_fin;


                SELECT length(v_datos.nit_ci_cli::varchar) into v_cantidad_nit;

                if (v_cantidad_nit > 8) then
                	v_natural_sumpli = 'S';
                else
                	v_natural_sumpli = 'N';
                end if;

                IF (v_datos.sistema_origen = 'ERP') then
                	v_venta_total = (v_datos.total_venta - COALESCE (v_datos.importe_exento,0));
                elsif (v_datos.sistema_origen = 'CARGA') then
                	v_venta_total = v_datos.total_venta;
                else
                	v_venta_total = v_datos.total_venta;
                end if;



                insert into temporal_acumulativo (
                								  id_periodo,
                								  nit_ci_cli,
                								  razon_social_cli,
                                                  total_venta,
                                                  natural_simplificado,
                                                  id_gestion) values
                								 ( v_id_periodo,
                                                   v_datos.nit_ci_cli,
                                                   v_datos.razon_social_cli,
                                                   v_venta_total,
                                                   v_natural_sumpli,
                                                   v_id_gestion
                                                 );






                end loop;

                for v_insertar in (

                				/*select 	tempo.id_periodo,
                                        tempo.nit_ci_cli,
                                        list(DISTINCT (tempo.razon_social_cli)) as razon_social_cli,
                                        sum(ac.total_acumulado) as total_venta,
                                        tempo.natural_simplificado,
                                        tempo.id_gestion
                                from temporal_acumulativo tempo
                                LEFT JOIN vef.tacumulacion_comisionistas ac on ac.nit = tempo.nit_ci_cli

                                /*Exluimos los nits no considerados*/
                                where tempo.nit_ci_cli::numeric not in (
                                select nc.nit_ci::numeric
                                from vef.tnits_no_considerados nc
                                )
                                /**********************************/
                                group by tempo.id_periodo, tempo.nit_ci_cli, tempo.natural_simplificado, tempo.id_gestion
								*/


                                select id_periodo,
                                       nit_ci_cli,
                                       list(DISTINCT (razon_social_cli)) as razon_social_cli,
                                       sum(total_venta) as total_venta,
                                       natural_simplificado,
                                       id_gestion
                                from temporal_acumulativo

                                /*Exluimos los nits no considerados*/
                                where nit_ci_cli::numeric not in (
                                	select nc.nit_ci::numeric
                                 	from vef.tnits_no_considerados nc
                                 )
                                 /**********************************/
                                group by id_periodo, nit_ci_cli, natural_simplificado, id_gestion

                                ) loop

                select per.fecha_ini,
                	   per.fecha_fin
                       into
                       v_fecha_ini,
                       v_fecha_fin
                from param.tperiodo per
                where per.id_periodo = v_insertar.id_periodo;


                 insert into vef.tacumulacion_comisionistas (nit,
                		     							    razon_social,
                                                            id_periodo,
                                                            total_acumulado,
                                                            fecha_ini,
                                                            fecha_fin,
                                                            natural_simplificado,
                                                            id_gestion,
                                                            estado
                                                            ) values
                                                            (v_insertar.nit_ci_cli,
                                                             v_insertar.razon_social_cli,
                                                             v_insertar.id_periodo,
                                                             v_insertar.total_venta,
                                                             v_fecha_ini,
                                                             v_fecha_fin,
                                                             v_insertar.natural_simplificado,
                                                             v_insertar.id_gestion,
                                                             'abierto');

                /************************************/

                end loop;


                /*Aqui recuperamos el periodo cerrado anterior*/

                select ges.id_gestion
                into v_id_gestion
                from param.tgestion ges
                where ges.gestion = v_anio;

                select
                        MAX (ven.id_periodo)
                        into
                        v_id_periodo_cerrado
                from conta.tperiodo_compra_venta ven
                inner join param.tperiodo per on per.id_periodo = ven.id_periodo
                where ven.id_depto = (select depo.id_depto
                from param.tdepto depo
                where depo.codigo = 'CON')
                and per.id_gestion = v_id_gestion and ven.estado = 'cerrado';

                /*Aqui recuperamos el periodo ABIERTO actual*/
                select
                        min (ven.id_periodo)
                        into
                        v_id_periodo_abierto
                from conta.tperiodo_compra_venta ven
                inner join param.tperiodo per on per.id_periodo = ven.id_periodo
                where ven.id_depto = (select depo.id_depto
                                      from param.tdepto depo
                                      where depo.codigo = 'CON')
                and per.id_gestion = v_id_gestion and (ven.estado = 'abierto' or ven.estado = 'cerrado_parcial');


                select

                        per.fecha_ini,
                        per.fecha_fin
                        into
                        v_fecha_ini,
                        v_fecha_fin
                from conta.tperiodo_compra_venta ven
                inner join param.tperiodo per on per.id_periodo = ven.id_periodo
                where ven.id_depto = (select depo.id_depto
                from param.tdepto depo
                where depo.codigo = 'CON')
                and per.id_gestion = v_id_gestion and per.id_periodo = v_id_periodo_abierto;
                /********************************************/

                if (v_id_periodo_cerrado is not null) then

                for v_insertar_acumulados in ( select acumul.*
                							   from vef.tacumulacion_comisionistas acumul
                                               where acumul.id_periodo = v_id_periodo_cerrado)
                loop
                 					           select acumul.nit::numeric as nit,
                                               	      acumul.total_acumulado::numeric
                                               		  into
                                                      v_existe_nit,
                                                      v_totales_sumar
                							   from vef.tacumulacion_comisionistas acumul
                                               where acumul.id_periodo = v_id_periodo_abierto and acumul.nit = v_insertar_acumulados.nit;


                                               if (v_existe_nit is not null) then

                                               update vef.tacumulacion_comisionistas set
                                                  total_acumulado = (total_acumulado+v_insertar_acumulados.total_acumulado)
                                               where nit = v_insertar_acumulados.nit and id_periodo = v_id_periodo_abierto;

                                               else

                                                insert into vef.tacumulacion_comisionistas (nit,
                		     							    razon_social,
                                                            id_periodo,
                                                            total_acumulado,
                                                            fecha_ini,
                                                            fecha_fin,
                                                            natural_simplificado,
                                                            id_gestion,
                                                            estado
                                                            ) values
                                                            (v_insertar_acumulados.nit,
                                                             v_insertar_acumulados.razon_social,
                                                             v_id_periodo_abierto,
                                                             v_insertar_acumulados.total_acumulado,
                                                             v_fecha_ini,
                                                             v_fecha_fin,
                                                             v_insertar_acumulados.natural_simplificado,
                                                             v_insertar_acumulados.id_gestion,
                                                             'abierto');

                                               end if;





                end loop;

                end if;





               /* for v_insertar_acumulados in (WITH periodo_anterior AS (
                                                    select  acumu.nit,
                                                    sum(acumu.total_acumulado) as total_arrastrado,
                                                    acumu.id_periodo
                                                    from vef.tacumulacion_comisionistas acumu
                                                    where acumu.estado = 'cerrado'
                                                    group by acumu.nit,acumu.id_periodo
                                                   )

                                                   select  acumu.nit,
                                                          sum(acumu.total_acumulado) + sum(per.total_arrastrado) as total_arrastrado
                                                    from vef.tacumulacion_comisionistas acumu
                                                    inner join periodo_anterior per on per.nit = acumu.nit
                                                    where acumu.estado = 'abierto'
                          group by acumu.nit
                loop
                  if (v_insertar_acumulados.nit is not NULL) then
                      update vef.tacumulacion_comisionistas set
                        total_acumulado = v_insertar_acumulados.total_arrastrado
                      where nit = v_insertar_acumulados.nit;
                  end if;

                end loop;*/
                /*********************************************************/







    return 'exito';

EXCEPTION
	WHEN OTHERS THEN
			--update a la tabla informix.migracion

            return SQLERRM;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION vef.ft_insertar_acumulacion_comisionistas_ime ()
  OWNER TO postgres;
