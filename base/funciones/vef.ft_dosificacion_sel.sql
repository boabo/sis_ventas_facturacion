CREATE OR REPLACE FUNCTION vef.ft_dosificacion_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_dosificacion_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tdosificacion'
 AUTOR: 		 (jrivera)
 FECHA:	        07-10-2015 13:00:56
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

BEGIN

	v_nombre_funcion = 'vef.ft_dosificacion_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_DOS_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera
 	#FECHA:		07-10-2015 13:00:56
	***********************************/

	if(p_transaccion='VF_DOS_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						dos.id_dosificacion,
						dos.id_sucursal,
						dos.final,
						dos.tipo,
						dos.fecha_dosificacion,
						dos.nro_siguiente,
						dos.nroaut,
						dos.fecha_inicio_emi,
						dos.fecha_limite,
						dos.tipo_generacion,
						dos.glosa_impuestos,
						array_to_string(dos.id_activida_economica,'','')::varchar,
						dos.llave,
                        dos.llave as llave_aux,
						dos.inicial,
						dos.estado_reg,
						dos.glosa_empresa,
						dos.id_usuario_ai,
						dos.fecha_reg,
						dos.usuario_ai,
						dos.id_usuario_reg,
						dos.fecha_mod,
						dos.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						(select pxp.list(nombre)
						from vef.tactividad_economica
						where id_actividad_economica = ANY(dos.id_activida_economica)) as desc_actividad_economica
						from vef.tdosificacion dos
						inner join segu.tusuario usu1 on usu1.id_usuario = dos.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = dos.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_DOS_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera
 	#FECHA:		07-10-2015 13:00:56
	***********************************/

	elsif(p_transaccion='VF_DOS_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_dosificacion)
					    from vef.tdosificacion dos
					    inner join segu.tusuario usu1 on usu1.id_usuario = dos.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = dos.id_usuario_mod
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'VF_DOSI_SEL'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		MMV
 	#FECHA:		07-10-2015 13:00:56
	***********************************/

	elsif(p_transaccion='VF_DOSI_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:='select dos.id_dosificacion,
						dos.id_sucursal,
						dos.final,
						dos.tipo,
						dos.fecha_dosificacion,
						dos.nro_siguiente,
						dos.nroaut,
						dos.fecha_inicio_emi,
						dos.fecha_limite,
						dos.tipo_generacion,
						dos.glosa_impuestos,
						array_to_string(dos.id_activida_economica,'','')::varchar,
						dos.llave,
						dos.inicial,
						dos.estado_reg,
						dos.glosa_empresa,
						dos.id_usuario_ai,
						dos.fecha_reg,
						dos.usuario_ai,
						dos.id_usuario_reg,
						dos.fecha_mod,
						dos.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						(select pxp.list(''(''|| codigo||'')''||nombre)
						from vef.tactividad_economica
						where id_actividad_economica = ANY(dos.id_activida_economica)) as desc_actividad_economica,
                        pxp.list(ac.codigo)::varchar as codigo,
                        ''(''||su.codigo||'') ''|| su.nombre as nom_sucursal,
                        su.nombre as nombre_sucursal,
                        lu.codigo as estacion,
                         (case
                        when extract(days from ( dos.fecha_limite::timestamp -  now()::date)) > 0 then
                        extract(days from ( dos.fecha_limite::timestamp -  now()::date))
                        when dos.fecha_limite::timestamp  <  now()::date then
                        0
                        else
                        0
                        end)::integer as dias_restante,
                        dos.nro_tramite,
                        dos.nombre_sistema,
                        dos.leyenda,
                        dos.rnd,

                        /*Aumentando para recuperar datos de las dosificaciones de Exportacion
                        (Ismael Valdivia 19/04/2021)*/
                        dos.caracteristica,
                        dos.titulo,
                        dos.subtitulo
                        /**********************************************************************/

						from vef.tdosificacion dos
						inner join segu.tusuario usu1 on usu1.id_usuario = dos.id_usuario_reg
                        inner join vef.tsucursal su on su.id_sucursal = dos.id_sucursal
                        inner join param.tlugar lu on lu.id_lugar = su.id_lugar
                        inner join vef.tactividad_economica ac on ac.id_actividad_economica = ANY(dos.id_activida_economica)
						left join segu.tusuario usu2 on usu2.id_usuario = dos.id_usuario_mod
                        where ';

			--Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
             v_consulta:=v_consulta||'group by dos.id_dosificacion,
                                  usu1.cuenta,
                                  usu2.cuenta,
                                  su.codigo,
                                  su.nombre,
                                  lu.codigo';
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'con....%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'VF_DOSI_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:			MMV
 	#FECHA:
	***********************************/

	elsif(p_transaccion='VF_DOSI_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(distinct  id_dosificacion)
					    from vef.tdosificacion dos
						inner join segu.tusuario usu1 on usu1.id_usuario = dos.id_usuario_reg
                        inner join vef.tsucursal su on su.id_sucursal = dos.id_sucursal
                        inner join param.tlugar lu on lu.id_lugar = su.id_lugar
                        inner join vef.tactividad_economica ac on ac.id_actividad_economica = ANY(dos.id_activida_economica)
						left join segu.tusuario usu2 on usu2.id_usuario = dos.id_usuario_mod
                        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
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

ALTER FUNCTION vef.ft_dosificacion_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
