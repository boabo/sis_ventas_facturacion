CREATE OR REPLACE FUNCTION vef.ft_impresion_notas_debito_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_impresion_notas_debito_sel
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

	v_nombre_funcion = 'vef.ft_impresion_notas_debito_sel';
    v_parametros = pxp.f_get_record(p_tabla);

     /*********************************
 	#TRANSACCION:  'VF_IMPREFACT_SEL'
 	#DESCRIPCION:   REIMPRESION FACTURA
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		28-01-2021 12:20:00
	***********************************/

	if(p_transaccion='VF_IMPREFACT_SEL')then

    	begin

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
                        --ven.total_venta - coalesce(ven.comision,0),
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
            comision,

            ven.hora_estimada_entrega

            from vef.tventa ven
              inner join vef.vcliente cli on cli.id_cliente = ven.id_cliente

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
 	#TRANSACCION:  'VF_IMPREFACTDET_SEL'
 	#DESCRIPCION:   Reporte Detalle de Recibo o Factura
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		28-01-2021 13:08:10
	***********************************/

	elsif(p_transaccion='VF_IMPREFACTDET_SEL')then

    	begin
    		--Sentencia de la consulta
            v_consulta:='select
						cig2.desc_ingas as concepto,
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
						left join vef.tformula form on form.id_formula = vedet.id_formula
						left join alm.titem item on item.id_item = vedet.id_item
                        left join param.tconcepto_ingas cig2 on cig2.id_concepto_ingas = vedet.id_producto
                        left join param.tunidad_medida um on um.id_unidad_medida = vedet.id_unidad_medida
                       where  ven.id_proceso_wf =  '||v_parametros.id_proceso_wf::varchar || '
                       order by vedet.descripcion,vedet.id_venta_detalle asc';




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

ALTER FUNCTION vef.ft_impresion_notas_debito_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
