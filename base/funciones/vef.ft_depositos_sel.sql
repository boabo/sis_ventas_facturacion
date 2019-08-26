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
    v_denominacion		varchar;
    v_filtro			varchar;
    v_fill				varchar;

    v_ventas_agrupadas 	varchar;
    v_subconsulta_ventas 	varchar;

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
        	if (pxp.f_existe_parametro(p_tabla,'relacion_deposito')) then

            	  	IF (v_parametros.id_moneda_deposito_agrupado = 2 ) then
                          select
                                  list(aso.id_apertura_cierre_caja::varchar) into v_ventas_agrupadas
                          from vef.vdepositos_agrupados dep
                          inner join vef.tapertura_cierre_caja_asociada aso on aso.id_deposito = dep.id_deposito
                          where dep.id_moneda_deposito = v_parametros.id_moneda_deposito_agrupado and dep.diferencia_me = 0
                          group by dep.diferencia_me, dep.diferencia_ml;

                          v_subconsulta_ventas = 'cdo.id_apertura_cierre_caja not in ('||v_ventas_agrupadas||') and cdo.arqueo_moneda_extranjera <> 0 and';

                    else
                    	select
                        		   list(aso.id_apertura_cierre_caja::varchar) into v_ventas_agrupadas
                          from vef.vdepositos_agrupados dep
                          inner join vef.tapertura_cierre_caja_asociada aso on aso.id_deposito = dep.id_deposito
                          where dep.id_moneda_deposito = v_parametros.id_moneda_deposito_agrupado and dep.diferencia_ml = 0
                          group by dep.diferencia_me, dep.diferencia_ml;

                          v_subconsulta_ventas = 'cdo.id_apertura_cierre_caja not in ('||v_ventas_agrupadas||') and cdo.arqueo_moneda_local <> 0 and';

                    end if;

                    if (v_ventas_agrupadas is null) then
                    	v_subconsulta_ventas = '';
                    end if;


            else
            	v_subconsulta_ventas = '';


            end if;


            v_fill = '(1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario ='||p_id_usuario||' ) or (
                                                    '||p_id_usuario||' in (select d.id_usuario
                                                                                            from vef.tsucursal s
                                                                                            inner join vef.tpunto_venta p on p.id_sucursal = s.id_sucursal
                                                                                            inner join param.tdepto_usuario d on d.id_depto = s.id_depto
                                                                                            where cdo.id_punto_venta = p.id_punto_venta
                                                                                            ))) and ';

    		--Sentencia de la consulta
			v_consulta:='select cdo.id_apertura_cierre_caja,
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
                                from vef.vdepositos cdo
                                where '||v_subconsulta_ventas||v_fill;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'consulta -> %',v_consulta;
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

            v_fill = '(1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario ='||p_id_usuario||' ) or (
                                                    '||p_id_usuario||' in (select d.id_usuario
                                                                                            from vef.tsucursal s
                                                                                            inner join vef.tpunto_venta p on p.id_sucursal = s.id_sucursal
                                                                                            inner join param.tdepto_usuario d on d.id_depto = s.id_depto
                                                                                            where cdo.id_punto_venta = p.id_punto_venta
                                                                                            ))) and ';

			v_consulta:='select count(id_apertura_cierre_caja)
					    from vef.vdepositos cdo
					    where ' ||v_fill;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
	/*********************************
 	#TRANSACCION:  'VF_CDO_GET'
 	#DESCRIPCION:	recuperar cuentas bancarias
 	#AUTOR:		miguel.mamani
 	#FECHA:		11-09-2017 15:32:32
	***********************************/
    elsif(p_transaccion='VF_CDO_GET')then
    begin

                select 	cuen.denominacion
                into
                v_denominacion
                        from vef.tsucursal s
                        inner join tes.tdepto_cuenta_bancaria b on  b.id_depto = s.id_depto
                        inner join vef.tpunto_venta pv on pv.id_sucursal = s.id_sucursal
                        inner  join tes.tcuenta_bancaria cuen on cuen.id_cuenta_bancaria = b.id_cuenta_bancaria
                        where  cuen.id_moneda = v_parametros.id_moneda and pv.id_punto_venta = v_parametros.id_punto_venta;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Transaccion Exitosa');
            v_resp = pxp.f_agrega_clave(v_resp,'denominacion',v_denominacion::varchar);

            --Devuelve la respuesta
            return v_resp;




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

ALTER FUNCTION vef.ft_depositos_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
