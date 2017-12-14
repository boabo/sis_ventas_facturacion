CREATE OR REPLACE FUNCTION "vef"."ft_mod_forma_pago_sel"(
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_mod_forma_pago_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tmod_forma_pago'
 AUTOR: 		 (miguel.mamani)
 FECHA:	        13-12-2017 21:37:47
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				13-12-2017 21:37:47								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tmod_forma_pago'	
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'obingresos.ft_mod_forma_pago_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_CFM_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		miguel.mamani	
 	#FECHA:		13-12-2017 21:37:47
	***********************************/

	if(p_transaccion='OBING_CFM_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						cfm.id_mod_forma_pago,
						cfm.fecha,
						cfm.ctacte,
						cfm.forma,
						cfm.agt,
						cfm.estacion,
						cfm.pais,
						cfm.comision,
						cfm.usuario,
						cfm.importe,
						cfm.autoriza,
						cfm.observa,
						cfm.hora_mod,
						cfm.tarjeta,
						cfm.billete,
						cfm.numero,
						cfm.moneda,
						cfm.pagomco,
						cfm.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	
						from obingresos.tmod_forma_pago cfm
						inner join segu.tusuario usu1 on usu1.id_usuario = cfm.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = cfm.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_CFM_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		miguel.mamani	
 	#FECHA:		13-12-2017 21:37:47
	***********************************/

	elsif(p_transaccion='OBING_CFM_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_mod_forma_pago)
					    from obingresos.tmod_forma_pago cfm
					    inner join segu.tusuario usu1 on usu1.id_usuario = cfm.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = cfm.id_usuario_mod
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
$BODY$
LANGUAGE 'plpgsql' VOLATILE
COST 100;
ALTER FUNCTION "obingresos"."ft_mod_forma_pago_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
