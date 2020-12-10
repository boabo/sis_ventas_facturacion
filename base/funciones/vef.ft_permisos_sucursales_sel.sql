CREATE OR REPLACE FUNCTION vef.ft_permisos_sucursales_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_permisos_sucursales_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tdosificacion'
 AUTOR: 		 (Ismael Valdivia)
 FECHA:	        26-08-2020 10:30:00
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
    v_id_funcionario	integer;
    v_id_uo				integer;
    v_id_uo_gerencia	integer;
    v_existen_gerencias integer;
    v_existe_permiso	integer;

BEGIN

	v_nombre_funcion = 'vef.ft_permisos_sucursales_sel';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
 	#TRANSACCION:  'VEF_SUCPERMISOS_SEL'
 	#DESCRIPCION:	Listado de los permisos asignados a los funcionarios
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		26-08-2020 10:30:56
	***********************************/

	if(p_transaccion='VEF_SUCPERMISOS_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:='select
            					sucur.id_autorizacion,
            					sucur.id_funcionario,
                                sucur.estado_reg,
                                sucur.id_usuario_ai,
                                sucur.fecha_reg,
                                sucur.usuario_ai,
                                sucur.id_usuario_reg,
                                sucur.fecha_mod,
                                sucur.id_usuario_mod,
                                usu1.cuenta as usr_reg,
                                usu2.cuenta as usr_mod,
                                fun.desc_funcionario1::varchar,
                                fun.nombre_cargo

						from vef.tpermiso_sucursales sucur
						inner join segu.tusuario usu1 on usu1.id_usuario = sucur.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = sucur.id_usuario_mod
                        inner join orga.vfuncionario_ultimo_cargo fun on fun.id_funcionario = sucur.id_funcionario
                        where ';

			--Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'VEF_SUCPERMISOS_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:			Ismale Valdivia
 	#FECHA:
	***********************************/

	elsif(p_transaccion='VEF_SUCPERMISOS_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count (sucur.id_autorizacion)
						from vef.tpermiso_sucursales sucur
						inner join segu.tusuario usu1 on usu1.id_usuario = sucur.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = sucur.id_usuario_mod
                        inner join orga.vfuncionario_ultimo_cargo fun on fun.id_funcionario = sucur.id_funcionario
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

ALTER FUNCTION vef.ft_permisos_sucursales_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
