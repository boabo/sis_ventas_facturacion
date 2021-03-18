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

    v_tipo_usuario		varchar;
    v_condicion			varchar;
    v_tipo_punto_venta	varchar;
    v_existencia		integer;
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
						usu2.cuenta as usr_mod,
                        (select vef.ft_verificar_excento(fact.id_venta))::varchar requiere_excento,
                        fact.excento_verificado,
                    	fo.nombre,
					    fo.id_formula
                        --sucu.nombre
                        --det.id_formula
						from vef.tventa fact
                        --left join vef.tventa_detalle det on det.id_venta = fact.id_venta
						inner join segu.tusuario usu1 on usu1.id_usuario = fact.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = fact.id_usuario_mod
                        left  join vef.tformula fo on fo.id_formula = fact.id_formula
                        --inner join vef.tsucursal sucu on sucu.id_sucursal = fact.id_sucursal
				        where fact.estado_reg = ''activo'' and  ';

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
				        where fact.estado_reg = ''activo'' and';

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

        	/*Aumentando para listar las facturas emititas de un cajero especifico si es admin o no*/

            select  permiso.id_autorizacion into v_existencia
            from vef.tpermiso_sucursales permiso
            where permiso.id_funcionario = (select fun.id_funcionario
                                            from segu.tusuario usu
                                            inner join orga.vfuncionario funcio on funcio.id_persona = usu.id_persona
                                            inner join orga.vfuncionario_ultimo_cargo fun on fun.id_funcionario = funcio.id_funcionario
                                            where usu.id_usuario = p_id_usuario);

            IF (v_existencia > 0) then
            	v_condicion = '0=0';
            else
            	 if (p_administrador != 1) then
                    select pv.tipo
                           into
                           v_tipo_punto_venta
                    from vef.tpunto_venta pv
                    where pv.id_punto_venta = v_parametros.id_punto_venta;

                    if (v_tipo_punto_venta = 'cto') THEN
                        if (v_parametros.pes_estado = 'caja') then
                            v_condicion = '0=0';
                        else
                            v_condicion = 'fact.id_usuario_cajero = '||p_id_usuario;
                        end if;
                    else

                        v_condicion = 'fact.id_usuario_cajero = '||p_id_usuario;

                    end if;
                else
                    v_condicion = '0=0';
                end if;
            end  if;

            /***************************************************************************************/

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
                        fact.informe,
                        --sucu.nombre

                        det.id_formula,
                        fact.formato_factura_emitida,
                        fact.correo_electronico

						from vef.tventa fact
                        left join vef.tventa_detalle det on det.id_venta = fact.id_venta
						inner join segu.tusuario usu1 on usu1.id_usuario = fact.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = fact.id_usuario_mod
                        --inner join vef.tsucursal sucu on sucu.id_sucursal = fact.id_sucursal
				        where fact.estado_reg = ''activo'' and '||v_condicion||' and';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

            /*Agrupamos*/
            v_consulta:=v_consulta||'group by fact.id_venta,usu1.cuenta,usu2.cuenta,det.id_formula';

			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'Respuesta es %',v_consulta;
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
				        where fact.estado_reg = ''activo'' and';

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
            v_consulta:='select
						/*(case when vedet.id_item is not null then
							item.nombre
						when vedet.id_sucursal_producto is not null then
							cig2.desc_ingas
						when vedet.id_producto is not null then
                                cig2.desc_ingas
						end)*/ cig2.desc_ingas as concepto,
                        vedet.cantidad::numeric,
                        vedet.precio,
                        vedet.precio*vedet.cantidad,
						um.codigo,
						cig2.codigo as cod_producto,
                        cig2.nandina,
                        vedet.bruto,
                        vedet.ley,
                        vedet.kg_fino,
                        vedet.descripcion,
                        um.codigo as unidad_concepto,
                        sum(vedet.precio*vedet.cantidad) OVER (PARTITION BY vedet.descripcion) as precio_grupo
						from vef.tventa_detalle vedet
                        inner join vef.tventa ven on ven.id_venta = vedet.id_venta
						--left join vef.tsucursal_producto sprod on sprod.id_sucursal_producto = vedet.id_sucursal_producto
						left join vef.tformula form on form.id_formula = vedet.id_formula
						left join alm.titem item on item.id_item = vedet.id_item
                        left join param.tconcepto_ingas cig2 on cig2.id_concepto_ingas = vedet.id_producto
                        --left join param.tconcepto_ingas cig on cig.id_concepto_ingas = sprod.id_concepto_ingas
                        left join param.tunidad_medida um on um.id_unidad_medida = vedet.id_unidad_medida
				        --left join param.tunidad_medida umcig on umcig.id_unidad_medida = cig.id_unidad_medida
                       where  ven.id_proceso_wf =  '||v_parametros.id_proceso_wf::varchar || '
                       order by vedet.descripcion,vedet.id_venta_detalle asc';

			/*v_consulta:='
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
                       order by vedet.descripcion,vedet.id_venta_detalle asc';*/


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

        	  /*Aqui aumentamos para controlar la reimpresion que solo sea por un administrador asignado*/
              if (p_administrador = 1) then
              	v_tipo_usuario = 'administrador_facturacion'::varchar;
              else
                select  count(permiso.id_autorizacion) into v_existencia
                from vef.tpermiso_sucursales permiso
                where permiso.id_funcionario = (select fun.id_funcionario
                                                from segu.tusuario usu
                                                inner join orga.vfuncionario funcio on funcio.id_persona = usu.id_persona
                                                inner join orga.vfuncionario_ultimo_cargo fun on fun.id_funcionario = funcio.id_funcionario
                                                where usu.id_usuario = p_id_usuario);

                if (v_existencia > 0) then
                    v_tipo_usuario = 'administrador_facturacion'::varchar;
                else
                    v_tipo_usuario = 'operador_facturacion'::varchar;
                end if;
              end if;
              /******************************************************************************************/




             /*if v_parametros.tipo_factura = 'pedido' then
               v_join_destino = '	inner join vef.vcliente clides on clides.id_cliente = ven.id_cliente_destino';
               v_columnas_destino = ' clides.nombre_factura as cliente_destino, clides.lugar as lugar_destino ';
            else*/
               	v_join_destino = '';
                v_columnas_destino = ' ''''::varchar as cliente_destino,''''::varchar as lugar_destino  ';
            --end if;



    		--Sentencia de la consulta
			v_consulta:='
                       select
                        ven.id_venta,
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
                        suc.nombre,
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
                        (to_char(ven.fecha,''DD'')::integer || '' de '' ||param.f_literal_periodo((to_char(ven.fecha,''MM'')::integer+1)) || '' de '' || to_char(ven.fecha,''YYYY''))::varchar as fecha_literal,
			(select count(*) from vef.ttipo_descripcion td where td.estado_reg = ''activo'' and td.id_sucursal = suc.id_sucursal)::integer as descripciones,
			ven.estado,
            ven.valor_bruto,
            ven.descripcion_bulto,
            (cli.telefono_celular || '' '' || cli.telefono_fijo)::varchar,
            (to_char(ven.fecha_estimada_entrega,''DD/MM/YYYY'') || '' '' || to_char(ven.hora_estimada_entrega,''HH24:MI''))::varchar,
            ven.a_cuenta,
            ven.nro_tramite,
            tc.codigo as codigo_cliente,
            cli.lugar as lugar_cliente,

            '||v_columnas_destino||',
            suc.codigo as codigo_sucursal,
            dos.leyenda,
            suc.zona,

            /************DATO EXCENTO************/
            coalesce(ven.excento,0) as excento,

            suc.codigo as sucursal,
            suc.nombre::varchar as desc_sucursal,
            lug.nombre::varchar as desc_lugar,

            emp.logo,
            usu.cuenta as cuenta_cajero,
            usu.id_usuario,
            ven.tipo_factura,

            (select mon.codigo_internacional
            from param.tmoneda mon
            where mon.tipo_moneda = ''base'')::varchar as moneda_base,

            pv.codigo::varchar as codigo_iata,
            COALESCE(to_char((EXTRACT(DAY FROM ven.fecha::date)),''00'')||substring(Upper(to_char(ven.fecha::date,''month''))from 1 for 3)||RIGHT((EXTRACT(YEAR FROM ven.fecha::date))::varchar,2))::varchar as fecha_ingles,
            REPLACE(list(ip.mop_code),'','',''/'')::varchar as forma_pago,

            '''||v_tipo_usuario||'''::varchar as tipo_usuario

            /************************************/
			, comision,

            ven.hora_estimada_entrega

            from vef.tventa ven
              inner join vef.vcliente cli on cli.id_cliente = ven.id_cliente
              '||v_join_destino||'
              inner join vef.tcliente tc on tc.id_cliente = cli.id_cliente
              inner join vef.tsucursal suc on suc.id_sucursal = ven.id_sucursal

              inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta

              inner join param.tentidad en on en.id_entidad = suc.id_entidad

              /*Aumentando la empresa*/
              inner join param.tempresa emp on emp.nit = en.nit
              /**********************************************/

              inner join param.tlugar lug on lug.id_lugar = suc.id_lugar
              left join vef.tsucursal_moneda sucmon on sucmon.id_sucursal = suc.id_sucursal
                  and sucmon.tipo_moneda = ''moneda_base''
              left join param.tmoneda mon on mon.id_moneda = sucmon.id_moneda
              inner join param.tmoneda mven on mven.id_moneda = ven.id_moneda
              left join vef.tdosificacion dos on dos.id_dosificacion = ven.id_dosificacion
              --inner join segu.tusuario usu on usu.id_usuario = ven.id_usuario_reg

              left join segu.tusuario usu on usu.id_usuario = ven.id_usuario_cajero

              inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
			  inner join obingresos.tmedio_pago_pw ip on ip.id_medio_pago_pw = fp.id_medio_pago

             where  ven.id_proceso_wf = '||v_parametros.id_proceso_wf::varchar||' group by
             						ven.id_venta,
                                    en.nombre,
                                    en.nit,
                                    suc.direccion,
                                    suc.telefono,
                                    suc.lugar,
                                    lug.nombre,
                                    ven.fecha,
                                    ven.correlativo_venta,
                                    mon.codigo,
                                    ven.total_venta,
                                    ven.excento,
                                    ven.observaciones,
                                    ven.nombre_factura,
                                    suc.nombre_comprobante,
                                    ven.nro_factura,
                                    dos.nroaut,
                                    ven.nit,
                                    ven.cod_control,
                                    dos.fecha_limite,
                                    dos.glosa_impuestos,
                                    dos.glosa_empresa,
                                    en.pagina_entidad,
                                    ven.id_venta,
                                    en.nit,
                                    tc.direccion,
                                    ven.tipo_cambio_venta,
                                    ven.total_venta_msuc,
                                    mven.codigo,
                                    mon.moneda,
                                    mven.moneda,
                                    ven.transporte_fob,
                                    ven.seguros_fob,
                                    ven.otros_fob,
                                    ven.transporte_cif,
                                    ven.seguros_cif,
                                    ven.otros_cif,
                                    ven.estado,
                                    ven.valor_bruto,
                                    ven.descripcion_bulto,
                                    cli.telefono_celular,
                                    cli.telefono_fijo,
                                    ven.fecha_estimada_entrega,
                                    ven.a_cuenta,
                                    ven.nro_tramite,
                                    tc.codigo,
                                    cli.lugar,

                                    suc.codigo,
                                    dos.leyenda,
                                    suc.zona,
                                    ven.excento,
                                    suc.codigo,
                                    suc.nombre,
                                    lug.nombre,
                                    emp.logo,
                                    usu.cuenta,
                                    usu.id_usuario,
                                    ven.tipo_factura,
                                    pv.codigo,
                                    dos.id_activida_economica,
                                    suc.id_sucursal';


			--Devuelve la respuesta
            raise notice 'consulta....%',v_consulta;
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'VF_LISCASAMAT_SEL'
 	#DESCRIPCION:   Funcion para recueperar la casa matriz de la entidad
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		25-05-2020 21:00:00
	***********************************/

	elsif(p_transaccion='VF_LISCASAMAT_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='
                        select
                              suc.nombre::varchar as nombre_casa_matriz,
                              suc.codigo::varchar as codigo_casa_matriz,
                              suc.direccion::varchar as direccion_casa_matriz,
                              suc.telefono::varchar as telefono_casa_matriz,
                              suc.lugar::varchar as lugar_casa_matriz
                        from vef.tsucursal suc
                        where suc.codigo = ''0''';


			--Devuelve la respuesta
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
    /*********************************
 	#TRANSACCION:  'VF_LIST_INST_PA'
 	#DESCRIPCION:   Listar instancias de pago
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		8-10-2019 15:56:00
	***********************************/

	elsif(p_transaccion='VF_LIST_INST_PA')then

    	begin
        	--raise exception 'llega aqui el id_venta %',v_parametros.id_venta;
    		--Sentencia de la consulta
			v_consulta:='
                        select 	ip.id_medio_pago_pw,
                                ip.name,
                                fp.codigo_tarjeta,
                                fp.numero_tarjeta,
                                fp.monto_transaccion,
                                fp.id_moneda,
                                fp.id_venta_forma_pago
                        from obingresos.tmedio_pago_pw ip
                        inner join vef.tventa_forma_pago fp on fp.id_medio_pago = ip.id_medio_pago_pw
                        where fp.id_venta = '||v_parametros.id_venta::integer||'
                        order by fp.id_venta_forma_pago
						';


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
