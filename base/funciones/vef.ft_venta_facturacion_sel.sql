CREATE OR REPLACE FUNCTION vef.ft_venta_facturacion_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_venta_facturacion_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tventa'
 AUTOR: 		 (ivaldivia)
 FECHA:	        10-05-2019 19:08:47
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				10-05-2019 19:08:47								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tventa'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
    v_columnas_destino	varchar;
	v_join_destino		varchar;
BEGIN

	v_nombre_funcion = 'vef.ft_venta_facturacion_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_fact_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:08:47
	***********************************/

	if(p_transaccion='VF_fact_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:='select
						fact.id_venta,
						fact.id_cliente,
						fact.id_dosificacion,
						fact.id_estado_wf,
						fact.id_proceso_wf,
						fact.id_punto_venta,
						fact.id_sucursal,
						fact.id_usuario_cajero,
						fact.id_cliente_destino,
						fact.transporte_fob,
						fact.tiene_formula,
						fact.cod_control,
						fact.estado,
						fact.total_venta_msuc,
						fact.otros_cif,
						fact.nro_factura,
						fact.observaciones,
						fact.seguros_cif,
						fact.comision,
						fact.id_moneda,
						fact.id_movimiento,
						fact.transporte_cif,
						fact.correlativo_venta,
						fact.estado_reg,
						fact.nro_tramite,
						fact.tipo_cambio_venta,
						fact.a_cuenta,
						fact.contabilizable,
						fact.nombre_factura,
						fact.excento,
						fact.valor_bruto,
						fact.descripcion_bulto,
						fact.id_grupo_factura,
						fact.fecha,
						fact.nit,
						fact.tipo_factura,
						fact.seguros_fob,
						fact.total_venta,
						fact.forma_pedido,
						fact.porcentaje_descuento,
						fact.hora_estimada_entrega,
						fact.id_vendedor_medico,
						fact.otros_fob,
						fact.fecha_estimada_entrega,
						fact.id_usuario_ai,
						fact.usuario_ai,
						fact.fecha_reg,
						fact.id_usuario_reg,
						fact.id_usuario_mod,
						fact.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod
                        --sucu.nombre
						from vef.tventa fact
						inner join segu.tusuario usu1 on usu1.id_usuario = fact.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = fact.id_usuario_mod
                        --inner join vef.tsucursal sucu on sucu.id_sucursal = fact.id_sucursal
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_fact_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:08:47
	***********************************/

	elsif(p_transaccion='VF_fact_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_venta)
					    from vef.tventa fact
						inner join segu.tusuario usu1 on usu1.id_usuario = fact.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = fact.id_usuario_mod
                        --inner join vef.tsucursal sucu on sucu.id_sucursal = fact.id_sucursal
				        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'VF_CAJA_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:08:47
	***********************************/

	elsif(p_transaccion='VF_CAJA_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:='select
						fact.id_venta,
						fact.id_cliente,
						fact.id_dosificacion,
						fact.id_estado_wf,
						fact.id_proceso_wf,
						fact.id_punto_venta,
						fact.id_sucursal,
						fact.id_usuario_cajero,
						fact.id_cliente_destino,
						fact.transporte_fob,
						fact.tiene_formula,
						fact.cod_control,
						fact.estado,
						fact.total_venta_msuc,
						fact.otros_cif,
						fact.nro_factura,
						fact.observaciones,
						fact.seguros_cif,
						fact.comision,
						fact.id_moneda,
						fact.id_movimiento,
						fact.transporte_cif,
						fact.correlativo_venta,
						fact.estado_reg,
						fact.nro_tramite,
						fact.tipo_cambio_venta,
						fact.a_cuenta,
						fact.contabilizable,
						fact.nombre_factura,
						fact.excento,
						fact.valor_bruto,
						fact.descripcion_bulto,
						fact.id_grupo_factura,
						fact.fecha,
						fact.nit,
						fact.tipo_factura,
						fact.seguros_fob,
						fact.total_venta,
						fact.forma_pedido,
						fact.porcentaje_descuento,
						fact.hora_estimada_entrega,
						fact.id_vendedor_medico,
						fact.otros_fob,
						fact.fecha_estimada_entrega,
						fact.id_usuario_ai,
						fact.usuario_ai,
						fact.fecha_reg,
						fact.id_usuario_reg,
						fact.id_usuario_mod,
						fact.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        fact.informe
                        --sucu.nombre
						from vef.tventa fact
						inner join segu.tusuario usu1 on usu1.id_usuario = fact.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = fact.id_usuario_mod
                        --inner join vef.tsucursal sucu on sucu.id_sucursal = fact.id_sucursal
				        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_CAJA_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		10-05-2019 19:08:47
	***********************************/

	elsif(p_transaccion='VF_CAJA_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_venta)
					    from vef.tventa fact
						inner join segu.tusuario usu1 on usu1.id_usuario = fact.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = fact.id_usuario_mod
                        --inner join vef.tsucursal sucu on sucu.id_sucursal = fact.id_sucursal
				        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'VF_FACDETALLE_SEL'
 	#DESCRIPCION:   Reporte Detalle de Recibo o Factura
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		27-06-2019 15:08:10
	***********************************/

	elsif(p_transaccion='VF_FACDETALLE_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='
                        select
						(case when vedet.id_item is not null then
							item.nombre
						when vedet.id_sucursal_producto is not null then
							cig.desc_ingas
						when vedet.id_producto is not null then
                                cig2.desc_ingas
						end) as concepto,
                        vedet.cantidad::numeric,
                        vedet.precio,
                        vedet.precio*vedet.cantidad,
                        um.codigo,
                        cig.nandina,
                        vedet.bruto,
                        vedet.ley,
                        vedet.kg_fino,
                        vedet.descripcion,
                        umcig.codigo as unidad_concepto,
                        sum(vedet.precio*vedet.cantidad) OVER (PARTITION BY vedet.descripcion) as precio_grupo
						from vef.tventa_detalle vedet
						left join vef.tsucursal_producto sprod on sprod.id_sucursal_producto = vedet.id_sucursal_producto
						left join vef.tformula form on form.id_formula = vedet.id_formula
						left join alm.titem item on item.id_item = vedet.id_item
                        left join param.tconcepto_ingas cig2 on cig2.id_concepto_ingas = vedet.id_producto
                        left join param.tconcepto_ingas cig on cig.id_concepto_ingas = sprod.id_concepto_ingas
                        left join param.tunidad_medida um on um.id_unidad_medida = vedet.id_unidad_medida
				        left join param.tunidad_medida umcig on umcig.id_unidad_medida = cig.id_unidad_medida
                       where  id_venta = '||v_parametros.id_venta::varchar || '
                       order by vedet.descripcion,vedet.id_venta_detalle asc';


			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'VF_LISFACT_SEL'
 	#DESCRIPCION:   Reporte de Recibo o Factura
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		27-06-2019 15:10:00
	***********************************/

	elsif(p_transaccion='VF_LISFACT_SEL')then

    	begin

             if v_parametros.tipo_factura = 'pedido' then
               v_join_destino = '	inner join vef.vcliente clides on clides.id_cliente = ven.id_cliente_destino';
               v_columnas_destino = ' clides.nombre_factura as cliente_destino, clides.lugar as lugar_destino ';
            else
               v_join_destino = '';
                v_columnas_destino = ' ''''::varchar as cliente_destino,''''::varchar as lugar_destino  ';
            end if;



    		--Sentencia de la consulta
			v_consulta:=' with medico_usuario as(
                                  select (med.id_medico || ''_medico'')::varchar as id_medico_usuario,med.nombre_completo::varchar as nombre
                                  from vef.vmedico med
                                union all
                                select (usu.id_usuario || ''_usuario'')::varchar as id_medico_usuario,usu.desc_persona::varchar as nombre
                                from segu.vusuario usu

                              )
                       select
						en.nombre,
                        en.nit,
                        suc.direccion,
                        suc.telefono,
                        suc.lugar,
                        lug.nombre as departamento_sucursal,
                        to_char(ven.fecha,''DD/MM/YYYY'')::varchar,
                        ven.correlativo_venta,
                        mon.codigo as moneda,
                        ven.total_venta,
                        ven.total_venta - coalesce(ven.excento,0),
                        pxp.f_convertir_num_a_letra(ven.total_venta) as total_venta_literal,
                        ven.observaciones,
                        ven.nombre_factura,
                        suc.nombre_comprobante,
                        ven.nro_factura,
                        dos.nroaut,
                        ven.nit,
                        ven.cod_control,
                        to_char(dos.fecha_limite,''DD/MM/YYYY''),
                        dos.glosa_impuestos,
                        dos.glosa_empresa,
                        en.pagina_entidad,
                        ven.id_venta,
                        to_char(now(),''HH24:MI:SS''),
                        en.nit,
                        (select pxp.list(nombre)
                        from vef.tactividad_economica
                        where id_actividad_economica =ANY(dos.id_activida_economica))::varchar,
                        to_char(ven.fecha,''MM/DD/YYYY'')::varchar as fecha_venta_recibo,

                        tc.direccion,
                        ven.tipo_cambio_venta,
                        ven.total_venta_msuc,
                        pxp.f_convertir_num_a_letra(ven.total_venta_msuc) as total_venta_msuc_literal,
                        mven.codigo,
                        mon.moneda,
                        mven.moneda,
                        ven.transporte_fob,
                        ven.seguros_fob,
                        ven.otros_fob,
                        ven.transporte_cif,
                        ven.seguros_cif,
                        ven.otros_cif,
                        (to_char(ven.fecha,''DD'')::integer || '' de '' ||param.f_literal_periodo(to_char(ven.fecha,''MM'')::integer) || '' de '' || to_char(ven.fecha,''YYYY''))::varchar as fecha_literal,
			(select count(*) from vef.ttipo_descripcion td where td.estado_reg = ''activo'' and td.id_sucursal = suc.id_sucursal)::integer as descripciones,
			ven.estado,
            ven.valor_bruto,
            ven.descripcion_bulto,
            (cli.telefono_celular || '' '' || cli.telefono_fijo)::varchar,
            (to_char(ven.fecha_estimada_entrega,''DD/MM/YYYY'') || '' '' || to_char(ven.hora_estimada_entrega,''HH24:MI''))::varchar,
            ven.a_cuenta,
            mu.nombre::varchar as vendedor_medico,
            ven.nro_tramite,
            tc.codigo as codigo_cliente,
            cli.lugar as lugar_cliente,

            '||v_columnas_destino||',
            suc.codigo as codigo_sucursal,
            dos.leyenda,
            suc.zona
            from vef.tventa ven
              inner join vef.vcliente cli on cli.id_cliente = ven.id_cliente
              '||v_join_destino||'
              inner join vef.tcliente tc on tc.id_cliente = cli.id_cliente
              inner join vef.tsucursal suc on suc.id_sucursal = ven.id_sucursal
              inner join param.tentidad en on en.id_entidad = suc.id_entidad
              inner join param.tlugar lug on lug.id_lugar = suc.id_lugar
              inner join vef.tsucursal_moneda sucmon on sucmon.id_sucursal = suc.id_sucursal
                  and sucmon.tipo_moneda = ''moneda_base''
              inner join param.tmoneda mon on mon.id_moneda = sucmon.id_moneda
              inner join param.tmoneda mven on mven.id_moneda = ven.id_moneda
              left join vef.tdosificacion dos on dos.id_dosificacion = ven.id_dosificacion
                        left join medico_usuario mu on mu.id_medico_usuario = ven.id_vendedor_medico
             where  id_venta = '||v_parametros.id_venta::varchar;


			--Devuelve la respuesta
            raise notice 'consulta....%',v_consulta;
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'VF_REDESCRIP_SEL'
 	#DESCRIPCION:   Reporte Descripciones de Recibo o Factura
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		27-06-2019 15:15:00
	***********************************/

	elsif(p_transaccion='VF_REDESCRIP_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='
                        select
						vd.valor_label,
						td.columna,
						td.fila,
						vd.valor
						from vef.tvalor_descripcion vd
						inner join vef.ttipo_descripcion td on td.id_tipo_descripcion = vd.id_tipo_descripcion

                       where  vd.id_venta = '||v_parametros.id_venta::varchar||'
                       order by td.columna,td.fila asc';


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

ALTER FUNCTION vef.ft_venta_facturacion_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;