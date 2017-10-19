CREATE OR REPLACE FUNCTION vef.ft_consulta_boletos_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_consulta_boletos_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tconsulta_boletos'
 AUTOR: 		 (admin)
 FECHA:	        12-10-2017 21:15:26
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				12-10-2017 21:15:26								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tconsulta_boletos'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
    v_filto				varchar;

BEGIN

	v_nombre_funcion = 'vef.ft_consulta_boletos_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_CBS_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		12-10-2017 21:15:26
	***********************************/

	if(p_transaccion='VF_CBS_SEL')then

    	begin
          IF  pxp.f_existe_parametro(p_tabla,'criterio_filtro') THEN
            v_filto = '(((cbs.pasajero::varchar ILIKE ''%'||v_parametros.criterio_filtro||'%'') OR to_tsvector(cbs.pasajero::varchar) @@ plainto_tsquery(''spanish'','''||v_parametros.criterio_filtro||''')) OR ((cbs.nro_boleto::varchar ILIKE ''%'||v_parametros.criterio_filtro||'%'') OR to_tsvector(cbs.nro_boleto::varchar) @@ plainto_tsquery(''spanish'','''||v_parametros.criterio_filtro||''')))  and ';
           ELSE
           	v_filto = '0=0 and';
            END IF;



    		--Sentencia de la consulta
			v_consulta:='select cbs.id_boleto,
                          cbs.punto_venta,
                          cbs.localizador,
                          cbs.total,
                          cbs.liquido,
                          cbs.id_moneda_boleto,
                          cbs.moneda,
                          cbs.neto,
                          cbs.fecha_emision,
                          cbs.nro_boleto,
                          cbs.pasajero,
                          cbs.voided,
                          cbs.estado,
                          cbs.agente_venta,
                          cbs.codigo_agente,
                          cbs.forma_pago_amadeus,
                          cbs.gestion
                          from vef.vboletos  cbs
                          where ' ;

			--Definicion de la respuesta

			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
            raise notice 'ONSULTA....%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_CBS_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		12-10-2017 21:15:26
	***********************************/

	elsif(p_transaccion='VF_CBS_CONT')then

		begin

        IF  pxp.f_existe_parametro(p_tabla,'criterio_filtro') THEN
                    v_filto = '(((cbs.pasajero::varchar ILIKE ''%'||v_parametros.criterio_filtro||'%'') OR to_tsvector(cbs.pasajero::varchar) @@ plainto_tsquery(''spanish'','''||v_parametros.criterio_filtro||''')) OR ((cbs.nro_boleto::varchar ILIKE ''%'||v_parametros.criterio_filtro||'%'') OR to_tsvector(cbs.nro_boleto::varchar) @@ plainto_tsquery(''spanish'','''||v_parametros.criterio_filtro||''')))  and ';

            ELSE
           	v_filto = '0=0 and ';
            END IF;

			--Sentencia de la consulta de conteo de registros
			v_consulta:='
                       select count(id_boleto)
					   from vef.vboletos  cbs
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