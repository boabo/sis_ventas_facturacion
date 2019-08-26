CREATE OR REPLACE FUNCTION vef.ft_venta_detalle_facturacion_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_venta_detalle_facturacion_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tventa_detalle_facturacion'
 AUTOR: 		 (ivaldivia)
 FECHA:	        10-05-2019 19:33:22
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				10-05-2019 19:33:22								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tventa_detalle_facturacion'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'vef.ft_venta_detalle_facturacion_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_FACTDET_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:33:22
	***********************************/

	if(p_transaccion='VF_FACTDET_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						factdet.id_venta_detalle,
						factdet.id_formula,
						factdet.id_item,
						factdet.id_medico,
						factdet.id_sucursal_producto,
						factdet.id_vendedor,
						factdet.id_venta,
						factdet.porcentaje_descuento,
						factdet.descripcion,
						factdet.id_boleto,
						factdet.estado,
						factdet.obs,
						factdet.id_unidad_medida,
						factdet.cantidad,
						factdet.tipo,
						factdet.bruto,
						factdet.estado_reg,
						factdet.id_producto,
						factdet.serie,
						factdet.precio,
						factdet.precio_sin_descuento,
						factdet.kg_fino,
						factdet.ley,
						factdet.id_usuario_ai,
						factdet.fecha_reg,
						factdet.usuario_ai,
						factdet.id_usuario_reg,
						factdet.id_usuario_mod,
						factdet.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        ing.desc_ingas as nombre_producto,
                        (factdet.precio * factdet.cantidad) as total
						from vef.tventa_detalle factdet
						inner join segu.tusuario usu1 on usu1.id_usuario = factdet.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = factdet.id_usuario_mod
                        inner join param.tconcepto_ingas ing on ing.id_concepto_ingas = factdet.id_producto
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_FACTDET_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:33:22
	***********************************/

	elsif(p_transaccion='VF_FACTDET_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_venta_detalle),
            			sum(factdet.precio*factdet.cantidad)
					    from vef.tventa_detalle factdet
						inner join segu.tusuario usu1 on usu1.id_usuario = factdet.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = factdet.id_usuario_mod
                        inner join param.tconcepto_ingas ing on ing.id_concepto_ingas = factdet.id_producto
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'VF_VEDETFACT_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:33:22
	***********************************/

	elsif(p_transaccion='VF_VEDETFACT_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
                          ven.id_venta_detalle,
                          ven.id_venta,
                          ven.id_producto,
                          ven.id_sucursal_producto,
                          ing.desc_ingas as nombre_producto,
                          ven.precio as precio_unitario,
                          ven.cantidad,
                          (ven.precio * ven.cantidad) as precio_total,
                          ven.descripcion,
                          ven.tipo,
                          ven.estado_reg,
                          ven.id_usuario_ai,
                          ven.usuario_ai,
                          ven.fecha_reg,
                          ven.id_usuario_reg,
                          ven.id_usuario_mod,
                          ven.fecha_mod
                    from vef.tventa_detalle ven
                    inner join param.tconcepto_ingas ing on ing.id_concepto_ingas = ven.id_producto
                    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'VF_VEDETFACT_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:33:22
	***********************************/

	elsif(p_transaccion='VF_VEDETFACT_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_venta_detalle)
					    from vef.tventa_detalle ven
                    	inner join param.tconcepto_ingas ing on ing.id_concepto_ingas = ven.id_producto
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

ALTER FUNCTION vef.ft_venta_detalle_facturacion_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
