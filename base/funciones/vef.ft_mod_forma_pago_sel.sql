CREATE OR REPLACE FUNCTION vef.ft_mod_forma_pago_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		ventas facturadas
 FUNCION: 		vef.ft_mod_forma_pago_sel
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

	v_nombre_funcion = 'vef.ft_mod_forma_pago_sel';
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
                        (select initcap (pu.nombre)
                        from vef.tpunto_venta pu
                        where pu.codigo::numeric = cfm.agt)::varchar as  punto_venta
						from obingresos.tmod_forma_pago cfm
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