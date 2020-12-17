CREATE OR REPLACE FUNCTION vef.ft_asociar_boletos_facturas_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_asociar_boletos_facturas_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tboleto'
 AUTOR: 		 (ivaldivia)
 FECHA:	        18-10-2019 10:14:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				18-10-2019 10:14:00								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tboleto'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'vef.ft_asociar_boletos_facturas_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_LISBOLETOS_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		18-10-2019 10:14:00
	***********************************/

	if(p_transaccion='VF_LISBOLETOS_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='
            			select bol.nro_boleto,
                               bol.id_boleto,
                               bol.estado_reg,
                               bol.fecha_emision,
                               bol.pasajero,
                               bol.nit::varchar,
                               (bol.origen ||''-''|| bol.destino)::varchar as ruta,
                               bol.razon
                        from obingresos.tboleto bol
                        where bol.estado_reg = ''activo'' and bol.id_boleto not in (select aso.id_boleto
                                                                                    from vef.tboletos_asociados_fact aso)
            			and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_LISBOLETOS_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		18-10-2019 10:14:00
	***********************************/

	elsif(p_transaccion='VF_LISBOLETOS_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count (bol.id_boleto)
                        from obingresos.tboleto bol
                        where bol.estado_reg = ''activo'' and bol.id_boleto not in (select aso.id_boleto
                                                                                  from vef.tboletos_asociados_fact aso)
                        and ';

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

ALTER FUNCTION vef.ft_asociar_boletos_facturas_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
