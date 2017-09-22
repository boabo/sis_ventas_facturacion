CREATE OR REPLACE FUNCTION vef.ft_depositos_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_depositos_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tdepositos'
 AUTOR: 		 (miguel.mamani)
 FECHA:	        11-09-2017 15:32:32
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

	v_nombre_funcion = 'vef.ft_depositos_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_CDO_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		miguel.mamani
 	#FECHA:		11-09-2017 15:32:32
	***********************************/

	if(p_transaccion='VF_CDO_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select cdo.id_apertura_cierre_caja,
                                cdo.id_punto_venta,
                                cdo.id_sucursal,
                                cdo.id_entrega_brinks,
                                cdo.id_usuario_cajero,
                                cdo.id_deposito,
                                cdo.cajero,
                                cdo.codigo,
                                cdo.nro_deposito,
                                cdo.punto_venta as nombre_punto_venta,
                                cdo.sucursal as nombre_sucursal,
                                cdo.estacion as codigo_lugar,
                                cdo.fecha_venta,
                                cdo.fecha_recojo,
                                cdo.fecha_apertura_cierre,
                                cdo.arqueo_moneda_local,
                                cdo.arqueo_moneda_extranjera,
                                cdo.deposito_bs,
                                cdo.deposito_$us
						from vef.vdepositos cdo
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_CDO_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		miguel.mamani
 	#FECHA:		11-09-2017 15:32:32
	***********************************/

	elsif(p_transaccion='VF_CDO_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_apertura_cierre_caja)
					    from vef.vdepositos cdo
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