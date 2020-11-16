CREATE OR REPLACE FUNCTION vef.ft_formula_v2_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_formula_v2_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tformula'
 AUTOR: 		 (ivaldivia)
 FECHA:	        17-09-2019 15:28:13
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-09-2019 15:28:13								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tformula'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'vef.ft_formula_v2_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_FORMULAV2_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		17-09-2019 15:28:13
	***********************************/

	if(p_transaccion='VF_FORMULAV2_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						form.id_formula,
						form.estado_reg,
						form.nombre,
						form.descripcion,
						form.id_usuario_reg,
						form.fecha_reg,
						form.id_usuario_ai,
						form.usuario_ai,
						form.id_usuario_mod,
						form.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        array_to_string( form.sw_autorizacion, '','',''null'')::varchar,
                        array_to_string( form.regionales, '','',''null'')::varchar
                        from vef.tformula form
						inner join segu.tusuario usu1 on usu1.id_usuario = form.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = form.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_FORMULAV2_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		17-09-2019 15:28:13
	***********************************/

	elsif(p_transaccion='VF_FORMULAV2_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_formula)
					    from vef.tformula form
					    inner join segu.tusuario usu1 on usu1.id_usuario = form.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = form.id_usuario_mod
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

ALTER FUNCTION vef.ft_formula_v2_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
