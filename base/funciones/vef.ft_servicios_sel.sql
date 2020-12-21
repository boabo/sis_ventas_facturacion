CREATE OR REPLACE FUNCTION vef.ft_servicios_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ventas Facturación
 FUNCION: 		vef.ft_servicios_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'param.tconcepto_ingas'
 AUTOR: 		 (ivaldivia)
 FECHA:	        10-09-2019 16:17:39
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				10-09-2019 16:17:39								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'param.tconcepto_ingas'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'vef.ft_servicios_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VEF_INGAS_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		10-09-2019 16:17:39
	***********************************/

	if(p_transaccion='VEF_INGAS_SEL')then

    	begin
        	--raise exception 'Aqui llega datos';
    		--Sentencia de la consulta
			v_consulta:='select
						ingas.id_concepto_ingas,
						ingas.estado_reg,
						ingas.tipo,
						ingas.desc_ingas,
						ingas.movimiento,
						ingas.sw_tes,
						ingas.activo_fijo,
						ingas.almacenable,
						array_to_string( ingas.sw_autorizacion, '','',''null'')::varchar,
						ingas.codigo,
						ingas.id_unidad_medida,
						ingas.nandina,
						ingas.id_cat_concepto,
						ingas.id_usuario_reg,
						ingas.fecha_reg,
						ingas.id_usuario_ai,
						ingas.usuario_ai,
						ingas.id_usuario_mod,
						ingas.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,

                        /************************/
                       	array_to_string(ingas.punto_venta_asociado,'','')::varchar as punto_venta_asociado,
                        array_to_string(ingas.tipo_punto_venta,'','')::varchar as tipo_punto_venta,
                        ingas.id_moneda,
                        COALESCE(ingas.precio::varchar,'''')::varchar,
                        mon.codigo_internacional as desc_moneda,
                        ingas.requiere_descripcion,
                        ingas.excento,
                        act.nombre as nombre_actividad,
                        ingas.id_actividad_economica,
                        (select pxp.list(pv.nombre) from vef.tpunto_venta pv where pv.id_punto_venta =ANY(ingas.punto_venta_asociado))::varchar as nombres_punto_venta,


                        array_to_string( ingas.regionales, '','',''null'')::varchar,
                        array_to_string( ingas.nivel_permiso, '','',''null'')::varchar,
                        ingas.contabilizable,
                        ingas.boleto_asociado,
                        ingas.agrupador
                        /************************/

						from param.tconcepto_ingas ingas
						inner join segu.tusuario usu1 on usu1.id_usuario = ingas.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ingas.id_usuario_mod
                        left join param.tmoneda mon on mon.id_moneda = ingas.id_moneda
                        left join vef.tactividad_economica act on act.id_actividad_economica = ingas.id_actividad_economica
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VEF_INGAS_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		10-09-2019 16:17:39
	***********************************/

	elsif(p_transaccion='VEF_INGAS_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_concepto_ingas)
					    from param.tconcepto_ingas ingas
					    inner join segu.tusuario usu1 on usu1.id_usuario = ingas.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ingas.id_usuario_mod
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'VEF_INGASPAQ_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		10-09-2019 16:17:39
	***********************************/

	elsif(p_transaccion='VEF_INGASPAQ_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:='SELECT   ingas.id_concepto_ingas,
                                  ingas.desc_ingas,
                                  ingas.precio,
                                  ingas.id_moneda,
                                  excento,
                                  requiere_descripcion,
                                  mon.codigo_internacional as desc_moneda,
                                  ingas.tipo,
                                  ingas.boleto_asociado,
                                  ingas.contabilizable
                          from param.tconcepto_ingas ingas
                          left join param.tmoneda mon on mon.id_moneda = ingas.id_moneda
                          where (''RO''=ANY (ingas.sw_autorizacion) OR ''FACTCOMP''=ANY (ingas.sw_autorizacion) OR ''dev''=ANY (ingas.sw_autorizacion)) AND ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VEF_INGASPAQ_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		10-09-2019 16:17:39
	***********************************/

	elsif(p_transaccion='VEF_INGASPAQ_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:=' SELECT   count (ingas.id_concepto_ingas)
                          from param.tconcepto_ingas ingas
                          left join param.tmoneda mon on mon.id_moneda = ingas.id_moneda
                          where (''RO''=ANY (ingas.sw_autorizacion) OR ''FACTCOMP''=ANY (ingas.sw_autorizacion) OR ''dev''=ANY (ingas.sw_autorizacion)) AND  ';

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

ALTER FUNCTION vef.ft_servicios_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
