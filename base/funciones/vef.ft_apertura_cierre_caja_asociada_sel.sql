CREATE OR REPLACE FUNCTION vef.ft_apertura_cierre_caja_asociada_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_apertura_cierre_caja_asociada_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tapertura_cierre_caja_asociada'
 AUTOR: 		 (ivaldivia)
 FECHA:	        15-08-2019 13:15:22
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				15-08-2019 13:15:22								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tapertura_cierre_caja_asociada'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'vef.ft_apertura_cierre_caja_asociada_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_acca_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		15-08-2019 13:15:22
	***********************************/

	if(p_transaccion='VF_acca_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						acca.id_apertura_asociada,
						acca.estado_reg,
						acca.id_apertura_cierre_caja,
						acca.id_deposito,
						acca.id_usuario_reg,
						acca.fecha_reg,
						acca.id_usuario_ai,
						acca.usuario_ai,
						acca.id_usuario_mod,
						acca.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,

                        cdo.id_punto_venta,
                        cdo.id_entrega_brinks,
                        cdo.id_usuario_cajero,
                        cdo.codigo_padre,
                        cdo.estacion,
                        initcap (cdo.nombre)::varchar as nombre_punto_venta,
                        cdo.codigo,
                        initcap( cdo.cajero) as cajero,
                        cdo.fecha_recojo,
                        cdo.fecha_venta,
                        cdo.arqueo_moneda_local,
                        cdo.arqueo_moneda_extranjera,
                        cdo.deposito_bs::numeric(18,2),
                        cdo.deposito_usd::numeric(18,2),
                        cdo.tipo_cambio,
                        cdo.diferencia_bs,
                        cdo.diferencia_usd

						from vef.tapertura_cierre_caja_asociada acca
						inner join segu.tusuario usu1 on usu1.id_usuario = acca.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = acca.id_usuario_mod

                        INNER join vef.vdepositos cdo on cdo.id_apertura_cierre_caja = acca.id_apertura_cierre_caja

				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_acca_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		15-08-2019 13:15:22
	***********************************/

	elsif(p_transaccion='VF_acca_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_apertura_asociada),
                        sum(caja.arqueo_moneda_local),
                        sum(caja.arqueo_moneda_extranjera)
                        from vef.tapertura_cierre_caja_asociada acca
                        inner join vef.tapertura_cierre_caja caja on caja.id_apertura_cierre_caja = acca.id_apertura_cierre_caja
                        inner join segu.tusuario usu1 on usu1.id_usuario = acca.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = acca.id_usuario_mod
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

ALTER FUNCTION vef.ft_apertura_cierre_caja_asociada_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
