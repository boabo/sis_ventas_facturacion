CREATE OR REPLACE FUNCTION vef.ft_entrega_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_entrega_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tentrega'
 AUTOR: 		 (admin)
 FECHA:	        12-09-2017 15:04:26
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

BEGIN

	v_nombre_funcion = 'vef.ft_entrega_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_ENG_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		12-09-2017 15:04:26
	***********************************/

	if(p_transaccion='VF_ENG_SEL')then

    	begin

        IF 	p_administrador THEN
        v_filto = '0=0 AND';

        ELSIF (v_parametros.tipo_usuario = 'adminEntrega') THEN
          v_filto = '0=0 AND';
        ELSE
        v_filto = ' id_usuario_reg = '||p_id_usuario||' and ';
        END IF;
    		--Sentencia de la consulta
			v_consulta:=' WITH punto_venta AS (SELECT p.id_punto_venta,
                                            l.codigo AS estacion,
                                            p.nombre,
                                            p.codigo
                                           FROM param.tlugar l
                                             inner join vef.tsucursal s ON s.id_lugar = l.id_lugar
                                             inner join vef.tpunto_venta p ON p.id_sucursal = s.id_sucursal)
						select
						eng.id_entrega_brinks,
						eng.fecha_recojo,
						eng.estado_reg,
						eng.id_usuario_ai,
						eng.id_usuario_reg,
						eng.usuario_ai,
						eng.fecha_reg,
						eng.fecha_mod,
						eng.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        (select COALESCE(sum(a.arqueo_moneda_local),0)
                        from vef.tapertura_cierre_caja a
                        where a.id_entrega_brinks = eng.id_entrega_brinks)::numeric  as arqueo_moneda_local,
                        (select COALESCE(sum(a.arqueo_moneda_extranjera),0)
                        from vef.tapertura_cierre_caja a
                        where a.id_entrega_brinks = eng.id_entrega_brinks)::numeric  as arqueo_moneda_extranjera,
                        eng.id_punto_venta,
                        initcap (v.nombre)::varchar as nombre_punto_venta,
                        v.estacion,
                        v.codigo,
                        initcap(u.desc_persona) as cajero
						from vef.tentrega eng
						inner join segu.tusuario usu1 on usu1.id_usuario = eng.id_usuario_reg
                        inner join punto_venta v on v.id_punto_venta = eng.id_punto_venta
                        inner join segu.vusuario u on u.id_usuario = eng.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = eng.id_usuario_mod
                        where '||v_filto;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'consulta %',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_ENG_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		12-09-2017 15:04:26
	***********************************/

	elsif(p_transaccion='VF_ENG_CONT')then

		begin
        IF 	p_administrador THEN
        v_filto = '0=0 AND';
        ELSIF (v_parametros.tipo_usuario = 'adminEntrega') THEN
          v_filto = '0=0 AND';
        ELSE
        v_filto = ' id_usuario_reg = '||p_id_usuario||' and ';
        END IF;
			--Sentencia de la consulta de conteo de registros
			v_consulta:='WITH punto_venta AS (SELECT p.id_punto_venta,
                                            l.codigo AS estacion,
                                            p.nombre,
                                            p.codigo
                                           FROM param.tlugar l
                                             inner join vef.tsucursal s ON s.id_lugar = l.id_lugar
                                             inner join vef.tpunto_venta p ON p.id_sucursal = s.id_sucursal)
            select count(id_entrega_brinks)
					    from vef.tentrega eng
					    inner join segu.tusuario usu1 on usu1.id_usuario = eng.id_usuario_reg
                        inner join punto_venta v on v.id_punto_venta = eng.id_punto_venta
                        inner join segu.vusuario u on u.id_usuario = eng.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = eng.id_usuario_mod
					    where '||v_filto;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'VF_ENG_FECH'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		12-09-2017 15:04:26
	***********************************/
    elsif(p_transaccion='VF_ENG_FECH')then

		begin
         IF p_administrador THEN
        v_filto = 'ap.id_entrega_brinks is null and ap.estado = ''cerrado'' and ';
        ELSE
        v_filto = 'ap.id_entrega_brinks is null and ap.estado =''cerrado'' and ap.id_usuario_cajero='||p_id_usuario||' and ';
        END IF;

        v_consulta:='select  to_char( ap.fecha_apertura_cierre,''DD/MM/YYYY'')::varchar as fecha_cierre,
        			ap.id_punto_venta,
                    initcap(u.desc_persona) as nombre_cajero,
                    initcap(p.nombre) as nombre
                    from vef.tapertura_cierre_caja ap
                    inner join segu.vusuario u on u.id_usuario = ap.id_usuario_cajero
                    inner join vef.tpunto_venta p on p.id_punto_venta = ap.id_punto_venta
                    where '||v_filto;


        v_consulta:=v_consulta||v_parametros.filtro;
		v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
		RAISE NOTICE 'CONUSLA...... %',v_consulta;
		--Devuelve la respuesta
		return v_consulta;


	end ;
    /*********************************
 	#TRANSACCION:  'VF_ENG_REPRO'
 	#DESCRIPCION:	FORMULARIO DE ENTREGA EFECTIVO
 	#AUTOR:		admin
 	#FECHA:		12-09-2017 15:04:26
	***********************************/

    elsif(p_transaccion='VF_ENG_REPRO')then

		begin
        v_consulta:='select 	p.nombre_completo1 as nombre_cajero,
                                l.codigo as estacion,
                                pu.nombre as punto_venta,
                                 to_char(  e.fecha_recojo,''DD/MM/YYYY'')  as fecha_recojo,
                                to_char( a.fecha_apertura_cierre,''DD/MM/YYYY'') as fecha_apertura_cierre,
                               COALESCE( a.arqueo_moneda_local, 0) as arqueo_moneda_local,
                               cuen.nro_cuenta,
                               cuen.denominacion,
                               (select sum(r.arqueo_moneda_local)
                               from vef.tapertura_cierre_caja r
                               where r.id_entrega_brinks = e.id_entrega_brinks) as total,
                               pxp.f_convertir_num_a_letra((select sum(r.arqueo_moneda_local)
                               from vef.tapertura_cierre_caja r
                               where r.id_entrega_brinks = e.id_entrega_brinks) ) as literial,
                               cuen.id_moneda
                              from vef.tentrega e
                              inner join vef.tapertura_cierre_caja a on a.id_entrega_brinks = e.id_entrega_brinks
                              inner join segu.tusuario u on u.id_usuario = a.id_usuario_cajero
                              inner join segu.vpersona p on p.id_persona = u.id_persona
                              inner join vef.tpunto_venta pu on pu.id_punto_venta = a.id_punto_venta
                              inner join vef.tsucursal s on s.id_sucursal = pu.id_sucursal
                              left join tes.tdepto_cuenta_bancaria de on de.id_depto = s.id_depto
                              left join tes.tcuenta_bancaria cuen on cuen.id_cuenta_bancaria = de.id_cuenta_bancaria and cuen.id_moneda = 1
                              inner join param.tlugar l on l.id_lugar = s.id_lugar
                              where cuen.id_moneda = 1 and e.id_entrega_brinks = '|| v_parametros.id_entrega_brinks;


		--Devuelve la respuesta
		return v_consulta;
	end ;
    elsif(p_transaccion='VF_ENG_REDOL')then

		begin
        v_consulta:='select 	p.nombre_completo1 as nombre_cajero,
                                l.codigo as estacion,
                                pu.nombre as punto_venta,
                                 to_char(  e.fecha_recojo,''DD/MM/YYYY'')  as fecha_recojo,
                                to_char( a.fecha_apertura_cierre,''DD/MM/YYYY'') as fecha_apertura_cierre,
                               COALESCE( a.arqueo_moneda_extranjera, 0) as arqueo_moneda_extranjera,
                               cuen.nro_cuenta,
                               cuen.denominacion,
                               (select sum(r.arqueo_moneda_extranjera)
                               from vef.tapertura_cierre_caja r
                               where r.id_entrega_brinks = e.id_entrega_brinks) as total,
                               pxp.f_convertir_num_a_letra((select sum(r.arqueo_moneda_extranjera)
                               from vef.tapertura_cierre_caja r
                               where r.id_entrega_brinks = e.id_entrega_brinks) ) as literial,
                               cuen.id_moneda
                              from vef.tentrega e
                              inner join vef.tapertura_cierre_caja a on a.id_entrega_brinks = e.id_entrega_brinks
                              inner join segu.tusuario u on u.id_usuario = a.id_usuario_cajero
                              inner join segu.vpersona p on p.id_persona = u.id_persona
                              inner join vef.tpunto_venta pu on pu.id_punto_venta = a.id_punto_venta
                              inner join vef.tsucursal s on s.id_sucursal = pu.id_sucursal
                              left join tes.tdepto_cuenta_bancaria de on de.id_depto = s.id_depto
                              left join tes.tcuenta_bancaria cuen on cuen.id_cuenta_bancaria = de.id_cuenta_bancaria and cuen.id_moneda = 2
                              inner join param.tlugar l on l.id_lugar = s.id_lugar
                              where cuen.id_moneda = 2 and e.id_entrega_brinks = '|| v_parametros.id_entrega_brinks;


		--Devuelve la respuesta
		return v_consulta;


	end ;

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