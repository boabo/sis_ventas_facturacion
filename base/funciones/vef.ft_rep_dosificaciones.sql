CREATE OR REPLACE FUNCTION vef.ft_rep_dosificaciones (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_rep_dosificaciones
 DESCRIPCION:   Funcion Para Generar reporte de Dosificaciones
 AUTOR: 		 (Ismael Valdivia)
 FECHA:	        22-03-2020 15:00:00
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
    v_consulta_depositos_resumen varchar;

    v_filtro_estado	varchar;
    v_filtro_sucursal	varchar;


BEGIN

	v_nombre_funcion = 'vef.ft_rep_dosificaciones';
    v_parametros = pxp.f_get_record(p_tabla);


        /*********************************
        #TRANSACCION:  'VEF_REP_DOSIFI_SEL'
        #DESCRIPCION:	Reporte de Dosificaciones
        #AUTOR:		Ismael Valdivia
        #FECHA:		22-03-2021 15:20:00
        ***********************************/
        if(p_transaccion = 'VEF_REP_DOSIFI_SEL')then

            begin

            	/*Aqui condicionales para los filtros*/
                if(v_parametros.estado_dosificacion = 'Todas') then
                	v_filtro_estado = '0=0';
                elsif(v_parametros.estado_dosificacion = 'Vencidas') then
                	v_filtro_estado = 'dias_restantes = 0';
                elsif(v_parametros.estado_dosificacion = 'Vigentes') then
                	v_filtro_estado = 'dias_restantes > 0';
                end if;
                /*************************************/


                if (v_parametros.id_sucursal = 0) then
                    v_filtro_sucursal = '0=0';
                else
                	v_filtro_sucursal = 'dos.id_sucursal = '||v_parametros.id_sucursal;
                end if;

                create temp table reporte_dosificaciones (
                                                                estacion varchar,
                                                                desc_sucursal varchar,
                                                                desc_actividad_economica varchar,
                                                                nro_autorizacion varchar,
                                                                nro_tramite varchar,
                                                                nombre_sistema varchar,
                                                                inicial numeric,
                                                                final numeric,
                                                                fecha_dosificacion varchar,
                                                                fecha_inicio_emi varchar,
                                                                fecha_limite varchar,
                                                                dias_restantes integer,
                                                                codigo_sucursal numeric
                                                              )on commit drop;

                v_consulta_depositos = 'insert into reporte_dosificaciones (
                                                                       estacion,
                                                                       desc_sucursal,
                                                                       desc_actividad_economica,
                                                                       nro_autorizacion,
                                                                       nro_tramite,
                                                                       nombre_sistema,
                                                                       inicial,
                                                                       final,
                                                                       fecha_dosificacion,
                                                                       fecha_inicio_emi,
                                                                       fecha_limite,
                                                                       dias_restantes,
                                                                       codigo_sucursal
                                                                       )
                					     (select  lu.codigo::varchar as estacion,
                                         		  (''(''||su.codigo||'') ''|| su.nombre)::varchar as desc_sucursal,
                                                  (select pxp.list(''(''|| codigo||'')''||nombre)
                                                  from vef.tactividad_economica
                                                  where id_actividad_economica = ANY(dos.id_activida_economica))::varchar as desc_actividad_economica,
                                                  dos.nroaut::varchar as nro_autorizacion ,
                                                  dos.nro_tramite::varchar as nro_tramite,
                                                  dos.nombre_sistema::varchar as nombre_sistema,
                                                  dos.inicial::numeric,
                                                  dos.final::numeric,
                                                  dos.fecha_dosificacion::varchar,
                                                  dos.fecha_inicio_emi::varchar,
                                                  dos.fecha_limite::varchar,
                                                  (case
                                                  when extract(days from ( dos.fecha_limite::timestamp -  now()::date)) > 0 then
                                                  extract(days from ( dos.fecha_limite::timestamp -  now()::date))
                                                  when dos.fecha_limite::timestamp  <  now()::date then
                                                  0
                                                  else
                                                  0
                                                  end)::integer as dias_restante,
                                                  su.codigo::numeric
                                                  from vef.tdosificacion dos
                                                  inner join segu.tusuario usu1 on usu1.id_usuario = dos.id_usuario_reg
                                                  inner join vef.tsucursal su on su.id_sucursal = dos.id_sucursal
                                                  inner join param.tlugar lu on lu.id_lugar = su.id_lugar
                                                  inner join vef.tactividad_economica ac on ac.id_actividad_economica = ANY(dos.id_activida_economica)
                                                  left join segu.tusuario usu2 on usu2.id_usuario = dos.id_usuario_mod

                                                  where dos.nombre_sistema = '''||v_parametros.nombre_sistema||'''

                                                  and dos.tipo_generacion = '''||v_parametros.tipo_generacion||'''

                                                  and '||v_filtro_sucursal||'

                                                  group by dos.id_dosificacion,
                                                                                    usu1.cuenta,
                                                                                    usu2.cuenta,
                                                                                    su.codigo,
                                                                                    su.nombre,
                                                                                    lu.codigo
                                                  ORDER BY su.codigo::numeric asc)

                                      ';

                --Aqui ejecutamos la consulta Armada
                execute v_consulta_depositos;


                v_consulta := '((select 	 estacion,
                                             desc_sucursal,
                                             desc_actividad_economica,
                                             nro_autorizacion,
                                             nro_tramite,
                                             nombre_sistema,
                                             inicial,
                                             final,
                                             fecha_dosificacion,
                                             fecha_inicio_emi,
                                             fecha_limite,
                                             dias_restantes,
                                             codigo_sucursal
                                    from reporte_dosificaciones
                                    where '||v_filtro_estado||')

                                    UNION ALL

                                    (select 	 ''cabecera''::varchar as estacion,
                                             desc_sucursal,
                                             null::varchar as desc_actividad_economica,
                                             null::varchar as nro_autorizacion,
                                             null::varchar as nro_tramite,
                                             null::varchar as nombre_sistema,
                                             null::numeric as inicial,
                                             null::numeric as final,
                                             null::varchar as fecha_dosificacion,
                                             null::varchar as fecha_inicio_emi,
                                             null::varchar as fecha_limite,
                                             null::integer as dias_restantes,
                                             codigo_sucursal
                                    from reporte_dosificaciones
                                    where '||v_filtro_estado||'
                                    group by estacion, desc_sucursal, codigo_sucursal))

                                    order by codigo_sucursal, fecha_dosificacion ASC NULLS FIRST';



                return v_consulta;

            end;

    /*********************************
        #TRANSACCION:  'VEF_REP_DOSIFI_CONT'
        #DESCRIPCION:	Reporte de Depositos
        #AUTOR:		Ismael Valdivia
        #FECHA:		12-03-2021 09:20:00
        ***********************************/
        elsif(p_transaccion = 'VEF_REP_DOSIFI_CONT')then

            	/*Aqui condicionales para los filtros*/
                if(v_parametros.estado_dosificacion = 'Todas') then
                	v_filtro_estado = '0=0';
                elsif(v_parametros.estado_dosificacion = 'Vencidas') then
                	v_filtro_estado = 'dias_restantes = 0';
                elsif(v_parametros.estado_dosificacion = 'Vigentes') then
                	v_filtro_estado = 'dias_restantes > 0';
                end if;
                /*************************************/


                if (v_parametros.id_sucursal = 0) then
                    v_filtro_sucursal = '0=0';
                else
                	v_filtro_sucursal = 'dos.id_sucursal = '||v_parametros.id_sucursal;
                end if;

                create temp table reporte_dosificaciones (
                                                                estacion varchar,
                                                                desc_sucursal varchar,
                                                                desc_actividad_economica varchar,
                                                                nro_autorizacion varchar,
                                                                nro_tramite varchar,
                                                                nombre_sistema varchar,
                                                                inicial numeric,
                                                                final numeric,
                                                                fecha_dosificacion varchar,
                                                                fecha_inicio_emi varchar,
                                                                fecha_limite varchar,
                                                                dias_restantes integer,
                                                                codigo_sucursal numeric
                                                              )on commit drop;

                v_consulta_depositos = 'insert into reporte_dosificaciones (
                                                                       estacion,
                                                                       desc_sucursal,
                                                                       desc_actividad_economica,
                                                                       nro_autorizacion,
                                                                       nro_tramite,
                                                                       nombre_sistema,
                                                                       inicial,
                                                                       final,
                                                                       fecha_dosificacion,
                                                                       fecha_inicio_emi,
                                                                       fecha_limite,
                                                                       dias_restantes,
                                                                       codigo_sucursal
                                                                       )
                					     (select  lu.codigo::varchar as estacion,
                                         		  (''(''||su.codigo||'') ''|| su.nombre)::varchar as desc_sucursal,
                                                  (select pxp.list(''(''|| codigo||'')''||nombre)
                                                  from vef.tactividad_economica
                                                  where id_actividad_economica = ANY(dos.id_activida_economica))::varchar as desc_actividad_economica,
                                                  dos.nroaut::varchar as nro_autorizacion ,
                                                  dos.nro_tramite::varchar as nro_tramite,
                                                  dos.nombre_sistema::varchar as nombre_sistema,
                                                  dos.inicial::numeric,
                                                  dos.final::numeric,
                                                  dos.fecha_dosificacion::varchar,
                                                  dos.fecha_inicio_emi::varchar,
                                                  dos.fecha_limite::varchar,
                                                  (case
                                                  when extract(days from ( dos.fecha_limite::timestamp -  now()::date)) > 0 then
                                                  extract(days from ( dos.fecha_limite::timestamp -  now()::date))
                                                  when dos.fecha_limite::timestamp  <  now()::date then
                                                  0
                                                  else
                                                  0
                                                  end)::integer as dias_restante,
                                                  su.codigo::numeric
                                                  from vef.tdosificacion dos
                                                  inner join segu.tusuario usu1 on usu1.id_usuario = dos.id_usuario_reg
                                                  inner join vef.tsucursal su on su.id_sucursal = dos.id_sucursal
                                                  inner join param.tlugar lu on lu.id_lugar = su.id_lugar
                                                  inner join vef.tactividad_economica ac on ac.id_actividad_economica = ANY(dos.id_activida_economica)
                                                  left join segu.tusuario usu2 on usu2.id_usuario = dos.id_usuario_mod

                                                  where dos.nombre_sistema = '''||v_parametros.nombre_sistema||'''

                                                  and dos.tipo_generacion = '''||v_parametros.tipo_generacion||'''

                                                  and '||v_filtro_sucursal||'

                                                  group by dos.id_dosificacion,
                                                                                    usu1.cuenta,
                                                                                    usu2.cuenta,
                                                                                    su.codigo,
                                                                                    su.nombre,
                                                                                    lu.codigo
                                                  ORDER BY su.codigo::numeric asc)

                                      ';

                --Aqui ejecutamos la consulta Armada
                execute v_consulta_depositos;


                v_consulta := ' select 	 count (estacion)
                                from reporte_dosificaciones
                                where '||v_filtro_estado||'';


                return v_consulta;



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

ALTER FUNCTION vef.ft_rep_dosificaciones (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
