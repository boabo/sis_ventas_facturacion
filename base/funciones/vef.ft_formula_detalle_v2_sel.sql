CREATE OR REPLACE FUNCTION vef.ft_formula_detalle_v2_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_formula_detalle_v2_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tformula_detalle'
 AUTOR: 		 (ivaldivia)
 FECHA:	        18-09-2019 21:05:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				18-09-2019 21:05:00								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tformula_detalle'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'vef.ft_formula_detalle_v2_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_DETFORV2_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		18-09-2019 21:05:00
	***********************************/

	if(p_transaccion='VF_DETFORV2_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						detforv2.id_formula_detalle,
						detforv2.estado_reg,
						detforv2.cantidad,
						detforv2.id_item,
						detforv2.id_formula,
						detforv2.id_concepto_ingas,
						detforv2.id_usuario_reg,
						detforv2.fecha_reg,
						detforv2.id_usuario_ai,
						detforv2.usuario_ai,
						detforv2.id_usuario_mod,
						detforv2.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        ingas.desc_ingas,
                        (case
                        	when (ingas.precio is NULL) then
                            	0
                            ELSE
                            	ingas.precio
                        end )::numeric as precio
						from vef.tformula_detalle detforv2
						inner join segu.tusuario usu1 on usu1.id_usuario = detforv2.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = detforv2.id_usuario_mod
						left join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = detforv2.id_concepto_ingas

                        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_DETFORV2_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		18-09-2019 21:05:00
	***********************************/

	elsif(p_transaccion='VF_DETFORV2_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_formula_detalle)
					    from vef.tformula_detalle detforv2
					    inner join segu.tusuario usu1 on usu1.id_usuario = detforv2.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = detforv2.id_usuario_mod
                        left join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = detforv2.id_concepto_ingas
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

ALTER FUNCTION vef.ft_formula_detalle_v2_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
