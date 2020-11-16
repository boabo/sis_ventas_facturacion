CREATE OR REPLACE FUNCTION vef.ft_facturacion_externa_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_facturacion_externa_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tentrega'
 AUTOR: 		 (ismael valdivia)
 FECHA:	        24-08-2020 15:04:26
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
    v_filto				varchar;
    v_var				varchar;

BEGIN

	v_nombre_funcion = 'vef.ft_facturacion_externa_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VEF_LIST_FACT_EX_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ismael.valdivia
 	#FECHA:		12-09-2017 15:04:26
	***********************************/

	if(p_transaccion='VEF_LIST_FACT_EX_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select  ven.id_proceso_wf,
                                 ven.fecha,
                                 ven.nro_factura,
                                 ven.nit,
                                 ven.nombre_factura,
                                 pv.nombre
                          from vef.tventa ven
                          inner join vef.tpunto_venta pv on pv.id_sucursal = ven.id_sucursal
                          where ven.observaciones like ''%'||v_parametros.ci||'%''
                          order by ven.nro_factura DESC';

			--Definicion de la respuesta
			raise notice '%',v_consulta;

			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VEF_LIST_FACT_EX_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		12-09-2017 15:04:26
	***********************************/

	elsif(p_transaccion='VEF_LIST_FACT_EX_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select  count (ven.id_venta)
                          from vef.tventa ven
                          inner join vef.tpunto_venta pv on pv.id_sucursal = ven.id_sucursal
                          where ven.observaciones like ''%'||v_parametros.ci||'%''
                          order by ven.nro_factura DESC';
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

ALTER FUNCTION vef.ft_facturacion_externa_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
