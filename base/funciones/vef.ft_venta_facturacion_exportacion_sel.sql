CREATE OR REPLACE FUNCTION vef.ft_venta_facturacion_exportacion_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_venta_facturacion_exportacion_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tventa'
 AUTOR: 		 (ivaldivia)
 FECHA:	        21-04-2021 09:30:47
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				21-04-2021 09:30:47							Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tventa'
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
    v_id_deposito		integer;
    v_tipo_cambio		numeric;

    v_datos_moneda_extranjera record;
    v_datos_moneda_local	record;
BEGIN

	v_nombre_funcion = 'vef.ft_venta_facturacion_exportacion_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_FACT_EMPR_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		21-04-2021 09:30:47
	***********************************/

	if(p_transaccion='VF_FACT_EMPR_SEL')then

    	begin

            v_consulta = 'select 	emp.nit,
                                    emp.nombre,
                                    emp.logo
                            from param.tempresa emp';



			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'VF_FACT_CAB_EXPO_SEL'
 	#DESCRIPCION:	Recuperacion de la cabecera de la factura
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		21-04-2021 09:30:47
	***********************************/

	elsif(p_transaccion='VF_FACT_CAB_EXPO_SEL')then

    	begin
			--raise exception 'Aqui llega el id venta %',v_parametros.id_venta;
            v_consulta = 'select
                                initcap(lu.nombre)::varchar,
                                (to_char(ven.fecha,''DD'')::integer || '' de '' ||initcap (param.f_literal_periodo((to_char(ven.fecha,''MM'')::integer+1))) || '' de '' || to_char(ven.fecha,''YYYY''))::varchar as fecha_literal,
                                (ven.nombre_factura)::VARCHAR,
                                (ven.direccion_cliente)::VARCHAR,
                                ven.nit,
                                ven.observaciones::varchar,
                                mon.moneda::varchar,
                                ven.tipo_cambio_venta::numeric,
                                ven.nro_factura::numeric,
                                dos.nroaut::varchar as nro_autorizacion,
                                aec.nombre::varchar as actividad_economica,

                                suc.codigo,
                                suc.nombre,
                                suc.direccion,
                                suc.telefono,
                                suc.lugar,

                                ven.valor_bruto,
                                ven.transporte_fob,
                                ven.seguros_fob,
                                ven.otros_fob,
                                ven.transporte_cif,
                                ven.seguros_cif,
                                ven.otros_cif,
                                mon.codigo,

                                (ven.valor_bruto+ven.transporte_fob+ven.seguros_fob+ven.otros_fob)::numeric as totales_fob,
                                (ven.transporte_cif+ven.seguros_cif+ven.otros_cif)::numeric as totales_cif,

                                TO_CHAR(dos.fecha_limite, ''DD/MM/YYYY'')::varchar,
      							ven.cod_control::varchar,

                                dos.glosa_impuestos::varchar,
                                dos.glosa_empresa::varchar,
                                ven.hora_estimada_entrega::varchar,
                                usu.cuenta::varchar,
                                dos.leyenda::varchar,
                                ven.id_venta


                                /***********************************************************/
                          from vef.tventa ven
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                          inner join vef.tsucursal suc on suc.id_sucursal = pv.id_sucursal
                          inner join param.tlugar lu on lu.id_lugar = suc.id_lugar
                          inner join vef.tcliente cli on cli.id_cliente = ven.id_cliente
                          inner join param.tmoneda mon on mon.id_moneda = ven.id_moneda
                          inner join vef.tdosificacion dos on dos.id_dosificacion = ven.id_dosificacion
                          inner join vef.tactividad_economica aec on aec.id_actividad_economica = ANY (dos.id_activida_economica)
                          inner join segu.tusuario usu on usu.id_usuario = ven.id_usuario_cajero
                          where ven.id_venta = '||v_parametros.id_venta ;



			--Devuelve la respuesta
			return v_consulta;

		end;

     /*********************************
 	#TRANSACCION:  'VF_FACT_DET_EXPO_SEL'
 	#DESCRIPCION:	Recuperacion de la cabecera de la factura
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		05-05-2021 11:50:47
	***********************************/

	elsif(p_transaccion='VF_FACT_DET_EXPO_SEL')then

    	begin

            v_consulta = 'select	(CASE
                                          WHEN det.descripcion != ''''

                                          THEN

                                          ((ing.desc_ingas)||'' - ''|| (det.descripcion) )

                                          ELSE

                                          (ing.desc_ingas)


                                    END )::varchar as descripcion_reporte,
                                    det.cantidad::numeric,
                                    det.precio::numeric,
                                    um.descripcion::varchar,
                                    (det.cantidad * det.precio)::numeric as subtotal,
                                    ing.nandina::varchar
                            from vef.tventa_detalle det
                            inner join param.tconcepto_ingas ing on ing.id_concepto_ingas = det.id_producto
                            left join param.tunidad_medida um on um.id_unidad_medida = ing.id_unidad_medida
                            where det.id_venta = '||v_parametros.id_venta ;
			--Devuelve la respuesta
			return v_consulta;

		end;


    /*********************************
 	#TRANSACCION:  'VF_FACT_TOT_EXPO_SEL'
 	#DESCRIPCION:	Recuperacion de la cabecera de la factura
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		05-05-2021 12:25:47
	***********************************/

	elsif(p_transaccion='VF_FACT_TOT_EXPO_SEL')then

    	begin

        	select tc.oficial
            		into
                    v_tipo_cambio
            from param.ttipo_cambio tc
            where tc.fecha = (select ven.fecha
            				  from vef.tventa ven
                              where ven.id_venta = v_parametros.id_venta)
            and tc.id_moneda = 2;

            select  mon.codigo,
            	    mon.moneda
                    into
                    v_datos_moneda_local
            from param.tmoneda mon
            where mon.tipo_moneda = 'base';

            select  mon.codigo,
            	    mon.moneda
                    into
                    v_datos_moneda_extranjera
            from param.tmoneda mon
            where mon.id_moneda = 2;

            v_consulta = 'select
                                CASE
                                      WHEN ven.id_moneda != 2  THEN

                                        sum((det.cantidad * det.precio))

                                      WHEN ven.id_moneda = 2 THEN

                                        sum((det.cantidad * det.precio)) * '||v_tipo_cambio::numeric||'
                                END as total_local,

                                CASE
                                      WHEN ven.id_moneda != 2  THEN

                                        sum((det.cantidad * det.precio)) / '||v_tipo_cambio::numeric||'

                                      WHEN ven.id_moneda = 2 THEN

                                        sum((det.cantidad * det.precio))
                                END as total_extranjera,

                                sum((det.cantidad * det.precio))::numeric as total,

                                '''||v_datos_moneda_local.codigo||'''::varchar as codigo_moneda_local,
                                '''||v_datos_moneda_local.moneda||'''::varchar as moneda_local,

                                '''||v_datos_moneda_extranjera.codigo||'''::varchar as codigo_moneda_extranjera,
                                '''||v_datos_moneda_extranjera.moneda||'''::varchar as moneda_extranjera,


                                CASE
                                      WHEN ven.id_moneda != 2  THEN

                                     	INITCAP( pxp.f_convertir_num_a_letra(sum((det.cantidad * det.precio))))


                                      WHEN ven.id_moneda = 2 THEN

                                       INITCAP ( pxp.f_convertir_num_a_letra(sum((det.cantidad * det.precio)) * '||v_tipo_cambio::numeric||'))

                                END::varchar as total_literal_local,

                                CASE
                                      WHEN ven.id_moneda != 2  THEN

                                       INITCAP( pxp.f_convertir_num_a_letra(sum((det.cantidad * det.precio)) / '||v_tipo_cambio::numeric||'))

                                      WHEN ven.id_moneda = 2 THEN

                                      	INITCAP (pxp.f_convertir_num_a_letra(sum((det.cantidad * det.precio))))


                                END::varchar as total_literal_extranjera


                        from vef.tventa_detalle det
                        inner join vef.tventa ven on ven.id_venta = det.id_venta
                        where det.id_venta = '||v_parametros.id_venta ||'
                        group by ven.id_moneda';
			--Devuelve la respuesta
			return v_consulta;

		end;


    /*********************************
 	#TRANSACCION:  'VF_FACTEXPOR_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		22-04-2021 15:08:47
	***********************************/

	elsif(p_transaccion='VF_FACTEXPOR_SEL')then

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

                        (CASE
                             WHEN fact.estado = ''borrador''
                             THEN
                             	''BORRADOR''
                             WHEN fact.estado = ''finalizado'' THEN
                             	''EMITIDA''
                             WHEN fact.estado = ''anulado'' THEN
                             	''ANULADO''
                        END)::varchar as estado,--fact.estado,
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
						''Factura de Exportacion''::varchar as tipo_factura,--fact.tipo_factura,
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


                        fact.formato_factura_emitida,
                        fact.correo_electronico,
                        usuca.desc_persona::varchar as cajero,
                        dosifi.nroaut as nro_autorizacion,
                        fact.id_auxiliar_anticipo,

                        /*Aqui para los depositos*/
                        depo.nro_deposito,
                        depo.fecha as fecha_deposito,
                        /*************************/
						fact.id_moneda_venta_recibo,

                        fact.direccion_cliente::varchar as direccion,

                        mon.codigo_internacional::varchar as desc_moneda

						from vef.tventa fact

						inner join segu.tusuario usu1 on usu1.id_usuario = fact.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = fact.id_usuario_mod
                        left join segu.vusuario usuca on usuca.id_usuario = fact.id_usuario_cajero

                        left join vef.tdosificacion dosifi on dosifi.id_dosificacion = fact.id_dosificacion

                        left join obingresos.tdeposito depo on depo.id_deposito = fact.id_deposito

                        inner join vef.tcliente cli on cli.id_cliente = fact.id_cliente

                        inner join param.tmoneda mon on mon.id_moneda = fact.id_moneda


                        --inner join vef.tsucursal sucu on sucu.id_sucursal = fact.id_sucursal
				        where fact.estado_reg = ''activo'' and '||v_condicion||' and';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;



			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'Respuesta es %',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_FACTEXPOR_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		22-04-2021 15:08:47
	***********************************/

	elsif(p_transaccion='VF_FACTEXPOR_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_venta)
					    from vef.tventa fact

						inner join segu.tusuario usu1 on usu1.id_usuario = fact.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = fact.id_usuario_mod
                        left join segu.vusuario usuca on usuca.id_usuario = fact.id_usuario_cajero

                        left join vef.tdosificacion dosifi on dosifi.id_dosificacion = fact.id_dosificacion

                        left join obingresos.tdeposito depo on depo.id_deposito = fact.id_deposito

                        inner join vef.tcliente cli on cli.id_cliente = fact.id_cliente

                        inner join param.tmoneda mon on mon.id_moneda = fact.id_moneda
				        where fact.estado_reg = ''activo'' and';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;


    /*********************************
 	#TRANSACCION:  'VF_FACTDETEXP_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		22-04-2021 15:33:22
	***********************************/

	elsif(p_transaccion='VF_FACTDETEXP_SEL')then

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
            (factdet.precio * factdet.cantidad) as total,
            /*Aumentando para el excento*/
            ven.excento,
            (case when
            (ing.excento is not null) then
            	ing.excento
            else
            	''no''
            end)::varchar as tiene_excento,
            ven.id_moneda,
            mon.codigo_internacional,
            ing.id_concepto_ingas,
						ing.desc_ingas,
            (case when ing.requiere_descripcion is not null then
                ing.requiere_descripcion
            else
                ''no''
            end)::varchar as requiere_descripcion
            /****************************/
						from vef.tventa_detalle factdet
						inner join segu.tusuario usu1 on usu1.id_usuario = factdet.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = factdet.id_usuario_mod
            inner join param.tconcepto_ingas ing on ing.id_concepto_ingas = factdet.id_producto

            /*Aumentando para excento*/
            inner join vef.tventa ven on ven.id_venta = factdet.id_venta
            --left join vef.tsucursal_producto pro on pro.id_concepto_ingas = factdet.id_producto and pro.id_sucursal = ven.id_sucursal
	        left join param.tmoneda mon on mon.id_moneda = ven.id_moneda
 			      /****************************/
                        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'VF_FACTDETEXP_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		22-04-2021 15:33:22
	***********************************/

	elsif(p_transaccion='VF_FACTDETEXP_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_venta_detalle),
            			sum(factdet.precio*factdet.cantidad)
					    from vef.tventa_detalle factdet
						inner join segu.tusuario usu1 on usu1.id_usuario = factdet.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = factdet.id_usuario_mod
                        inner join param.tconcepto_ingas ing on ing.id_concepto_ingas = factdet.id_producto

                        /*Aumentando para excento*/
                        inner join vef.tventa ven on ven.id_venta = factdet.id_venta
                        --left join vef.tsucursal_producto pro on pro.id_concepto_ingas = factdet.id_producto and pro.id_sucursal = ven.id_sucursal
            	        left join param.tmoneda mon on mon.id_moneda = ing.id_moneda
             			/****************************/
                        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'VF_EXP_FP_LIST_SEL'
 	#DESCRIPCION:  Listar las instancias de pago de la venta
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		06-05-2021
	***********************************/

	elsif(p_transaccion='VF_EXP_FP_LIST_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:='
                        select 	ip.id_medio_pago_pw,
                        		ip.name,
                                fp.codigo_tarjeta,
                                fp.numero_tarjeta,
                                (CASE
                                     WHEN fp.id_moneda != 2 THEN

                                     fp.monto_mb_efectivo

                                     ELSE

                                     fp.monto_dolar_efectivo
                                END)::numeric as monto_transaccion,
                                fp.id_moneda,
                                fp.id_venta_forma_pago,
                                fp.id_venta,
                                mon.codigo_internacional as desc_moneda,
                                fo.fop_code,
                                fp.id_auxiliar,
                                aux.nombre_auxiliar,
                                aux.codigo_auxiliar,
                                fp.nro_mco as mco,
                                usu1.cuenta as usr_reg,
                                usu2.cuenta as usr_mod,
                                fp.fecha_reg,
                                fp.fecha_mod
                        from obingresos.tmedio_pago_pw ip
                        inner join vef.tventa_forma_pago fp on fp.id_medio_pago = ip.id_medio_pago_pw
                        inner join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                        inner join obingresos.tforma_pago_pw fo on fo.id_forma_pago_pw = ip.forma_pago_id
                        left join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tventa ven on ven.id_venta = fp.id_venta
                        inner join segu.tusuario usu1 on usu1.id_usuario = fp.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = fp.id_usuario_mod
                        where fp.id_venta = '||v_parametros.id_venta::integer||'
                        and
						';
            v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			--raise notice 'Respuesta es %',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'VF_EXP_FP_LIST_CONT'
 	#DESCRIPCION:  Contador del listado de las instancias de pago
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		06-05-2021
	***********************************/

	elsif(p_transaccion='VF_EXP_FP_LIST_CONT')then

    	begin
        	--raise exception 'llega aqui el id_venta %',v_parametros.id_venta;
    		--Sentencia de la consulta
			v_consulta:='
                        select 	count(ip.id_medio_pago_pw)
                        from obingresos.tmedio_pago_pw ip
                        inner join vef.tventa_forma_pago fp on fp.id_medio_pago = ip.id_medio_pago_pw
                        inner join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                        inner join obingresos.tforma_pago_pw fo on fo.id_forma_pago_pw = ip.forma_pago_id
                        left join conta.tauxiliar aux on aux.id_auxiliar = fp.id_auxiliar
                        inner join vef.tventa ven on ven.id_venta = fp.id_venta
                        inner join segu.tusuario usu1 on usu1.id_usuario = fp.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = fp.id_usuario_mod
                        where fp.id_venta = '||v_parametros.id_venta::integer||' and
						';
            v_consulta:=v_consulta||v_parametros.filtro;
			--raise notice 'Respuesta es %',v_consulta;

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

ALTER FUNCTION vef.ft_venta_facturacion_exportacion_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
