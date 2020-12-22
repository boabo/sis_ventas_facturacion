CREATE OR REPLACE FUNCTION vef.ft_nits_no_considerados_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_nits_no_considerados_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tnits_no_considerados'
 AUTOR: 		 (maylee.perez)
 FECHA:	        21-12-2020 20:13:12
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				21-12-2020 20:13:12								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tnits_no_considerados'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'vef.ft_nits_no_considerados_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_NITNCONS_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		maylee.perez
 	#FECHA:		21-12-2020 20:13:12
	***********************************/

	if(p_transaccion='VF_NITNCONS_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						nitncons.id_nits_no_considerados,
						nitncons.estado_reg,
						nitncons.nit_ci,
						nitncons.razon_social,
						nitncons.t_contr,
						nitncons.incl_rep,
						nitncons.observaciones,
						nitncons.id_usuario_reg,
						nitncons.fecha_reg,
						nitncons.id_usuario_ai,
						nitncons.usuario_ai,
						nitncons.id_usuario_mod,
						nitncons.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod
						from vef.tnits_no_considerados nitncons
						inner join segu.tusuario usu1 on usu1.id_usuario = nitncons.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = nitncons.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_NITNCONS_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		21-12-2020 20:13:12
	***********************************/

	elsif(p_transaccion='VF_NITNCONS_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_nits_no_considerados)
					    from vef.tnits_no_considerados nitncons
					    inner join segu.tusuario usu1 on usu1.id_usuario = nitncons.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = nitncons.id_usuario_mod
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