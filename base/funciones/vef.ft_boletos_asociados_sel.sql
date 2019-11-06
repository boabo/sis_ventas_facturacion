CREATE OR REPLACE FUNCTION vef.ft_boletos_asociados_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar 
)
RETURNS varchar AS'
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_boletos_asociados_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''vef.tboletos_asociados_fact''
 AUTOR: 		 (ivaldivia)
 FECHA:	        18-10-2019 11:37:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				19-10-2019 11:37:00		ivaldivia					Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''vef.tboletos_asociados_fact''
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = ''vef.ft_boletos_asociados_sel'';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  ''VF_LISTASOCIADOS_SEL''
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		18-10-2019 11:37:00
	***********************************/

	if(p_transaccion=''VF_LISTASOCIADOS_SEL'')then

    	begin
    		--Sentencia de la consulta
			v_consulta:=''select
                                bol.id_boleto,
                                bol.id_boleto_asociado,
                                bol.id_venta,
                                bol.nro_boleto,
                                bol.nit,
                                bol.pasajero,
                                bol.razon,
                                bol.ruta,
                                bol.estado_reg,
                                bol.id_usuario_reg,
                                bol.fecha_reg,
                                bol.id_usuario_ai,
                                bol.usuario_ai,
                                bol.id_usuario_mod,
                                bol.fecha_mod,
                                usu1.cuenta as usr_reg,
                                usu2.cuenta as usr_mod
                          from vef.tboletos_asociados_fact bol
                          inner join segu.tusuario usu1 on usu1.id_usuario = bol.id_usuario_reg
                          left join segu.tusuario usu2 on usu2.id_usuario = bol.id_usuario_mod
				         where  '';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||'' order by '' ||v_parametros.ordenacion|| '' '' || v_parametros.dir_ordenacion || '' limit '' || v_parametros.cantidad || '' offset '' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  ''VF_LISTASOCIADOS_CONT''
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		18-10-2019 11:37:00
	***********************************/

	elsif(p_transaccion=''VF_LISTASOCIADOS_CONT'')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:=''select count(bol.id_boleto_asociado)
                        from vef.tboletos_asociados_fact bol
                        inner join segu.tusuario usu1 on usu1.id_usuario = bol.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = bol.id_usuario_mod
                        where '';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

	else

		raise exception ''Transaccion inexistente'';

	end if;

EXCEPTION

	WHEN OTHERS THEN
			v_resp='''';
			v_resp = pxp.f_agrega_clave(v_resp,''mensaje'',SQLERRM);
			v_resp = pxp.f_agrega_clave(v_resp,''codigo_error'',SQLSTATE);
			v_resp = pxp.f_agrega_clave(v_resp,''procedimientos'',v_nombre_funcion);
			raise exception ''%'',v_resp;
END;
'LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION vef.ft_boletos_asociados_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
