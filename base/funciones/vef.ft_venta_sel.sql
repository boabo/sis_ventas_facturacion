CREATE OR REPLACE FUNCTION vef.ft_venta_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_venta_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tventa'
 AUTOR: 		 (admin)
 FECHA:	        01-06-2015 05:58:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:	Se aumenta el cliente destino para la interface del tipo pedido
 AUTOR:			Rensi Arteaga Copari
 FECHA:		    29/10/2016
***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
    v_id_funcionario_usuario	integer;
    v_sucursales		varchar;
    v_filtro			varchar;
    v_join				varchar;
    v_select			varchar;
    v_historico			varchar;
    v_join_destino		varchar;
    v_columnas_destino	varchar;
    v_join_punto		varchar;
    v_columnas_punto	varchar;
    v_moneda_base		varchar;

BEGIN

	v_nombre_funcion = 'vef.ft_venta_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_VEN_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		01-06-2015 05:58:00
	***********************************/

	if(p_transaccion='VF_VEN_SEL')then

    	begin
        	IF  pxp.f_existe_parametro(p_tabla,'historico') THEN
            	v_historico =  v_parametros.historico;
            ELSE
            	v_historico = 'no';
            END IF;

            --obtener funcionario del usuario
            select f.id_funcionario into v_id_funcionario_usuario
            from segu.tusuario u
            inner join segu.tpersona p on p.id_persona = u.id_persona
            inner join orga.tfuncionario f on f.id_persona = p.id_persona
            where u.id_usuario = p_id_usuario;

            if (v_id_funcionario_usuario is null) then
            	v_id_funcionario_usuario = -1;
            end if;

        select coalesce(pxp.list(su.id_sucursal::text),'-1') into v_sucursales
            from vef.tsucursal_usuario su
            where su.id_usuario = p_id_usuario and su.estado_reg = 'activo';

            v_select = 'ven.id_venta';
            v_join = 'inner join wf.testado_wf ewf on ewf.id_estado_wf = ven.id_estado_wf';

            if p_administrador !=1 then
            	if (v_historico = 'si') then
                	v_select = 'distinct(ven.id_venta)';
                	v_join = 'inner join wf.testado_wf ewf on ewf.id_proceso_wf = ven.id_proceso_wf';
                end if;

                if (v_parametros.tipo_usuario = 'vendedor') then
                  v_filtro = ' (ven.id_usuario_reg='||p_id_usuario::varchar||') and ';
                elsif (v_parametros.tipo_usuario = 'cajero') THEN
                  --v_filtro = ' (ewf.id_funcionario='||v_id_funcionario_usuario::varchar||') and ';
                  v_filtro = ' (ven.id_usuario_cajero='||p_id_usuario::varchar||') and ';
                ELSE
                  v_filtro = ' 0 = 0 and ';
                end if;
            else
            	v_filtro = ' 0 = 0 and ';
            end if;


            if v_parametros.tipo_factura = 'pedido' then
               v_join_destino = '	inner join vef.vcliente clides on clides.id_cliente = ven.id_cliente_destino';
               v_columnas_destino = ' clides.nombre_factura as cliente_destino';
            else
               v_join_destino = '';
                v_columnas_destino = ' ''''::varchar as cliente_destino';
            end if;


    		--Sentencia de la consulta
			v_consulta:='
						select
						' || v_select || ',
						ven.id_cliente,
						ven.id_sucursal,
						ven.id_proceso_wf,
						ven.id_estado_wf,
						ven.estado_reg,
						ven.correlativo_venta,
						ven.a_cuenta,
						ven.total_venta,
						ven.fecha_estimada_entrega,
						ven.usuario_ai,
						ven.fecha_reg,
						ven.id_usuario_reg,
						ven.id_usuario_ai,
						ven.id_usuario_mod,
						ven.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        ven.estado,
                        cli.nombre_factura,
                        suc.nombre as nombre_sucursal,
                        cli.nit,
                        puve.id_punto_venta,
                        puve.nombre as nombre_punto_venta,

                        mp.id_medio_pago_pw::varchar,
                        ( CASE
                        	WHEN ven.id_deposito is not null then
                        	''DEPO''
                          else
                           ( select string_agg(pago.mop_code,''/'')
                            FROM obingresos.tmedio_pago_pw pago
                            left join vef.tventa_forma_pago venta on venta.id_medio_pago = pago.id_medio_pago_pw
                            where venta.id_venta =  ven.id_venta)
                          END
                        )::varchar as forma_pago,
                        fp.monto::varchar,
                        fp.numero_tarjeta::varchar,
                        fp.codigo_tarjeta::varchar,
                        fp.tipo_tarjeta::varchar,

                        ven.porcentaje_descuento,
                        ven.id_vendedor_medico,
                        ven.comision,
                        ven.observaciones,
                        ven.fecha,
                        ven.nro_factura,
                        ven.excento,
                        ven.cod_control,


                        ven.id_moneda,
                        ven.total_venta_msuc,
                        ven.transporte_fob,
                        ven.seguros_fob,
                        ven.otros_fob,
                        ven.transporte_cif,
                        ven.seguros_cif,
                        ven.otros_cif,
                        ven.tipo_cambio_venta,
                        mon.moneda as desc_moneda,
                        ven.valor_bruto,
                        ven.descripcion_bulto,
                        ven.contabilizable,
                        to_char(ven.hora_estimada_entrega,''HH24:MI'')::varchar,

                        ven.forma_pedido,
                        ven.id_cliente_destino,
                        '||v_columnas_destino||',

                        usua.desc_persona::varchar as cajero,
                        ven.tipo_factura,ven.id_auxiliar_anticipo,
                        aux.nombre_auxiliar

						from vef.tventa ven
						inner join segu.tusuario usu1 on usu1.id_usuario = ven.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ven.id_usuario_mod

				        inner join vef.vcliente cli on cli.id_cliente = ven.id_cliente
                        '||v_join_destino||'
                        inner join vef.tsucursal suc on suc.id_sucursal = ven.id_sucursal
                        left join conta.tauxiliar aux on aux.id_auxiliar =  ven.id_auxiliar_anticipo

                        inner join vef.tpunto_venta puve on puve.id_punto_venta = ven.id_punto_venta
                        inner join param.tmoneda mon on mon.id_moneda = ven.id_moneda

                        left join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        left join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago

                        left join segu.vusuario usua on usua.id_usuario = ven.id_usuario_cajero

                        ' || v_join || '
                        where ven.estado_reg = ''activo'' and ' || v_filtro;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
            --raise exception '';
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_VEN_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		01-06-2015 05:58:00
	***********************************/

	elsif(p_transaccion='VF_VEN_CONT')then

		begin
        	IF  pxp.f_existe_parametro(p_tabla,'historico') THEN
            	v_historico =  v_parametros.historico;
            ELSE
            	v_historico = 'no';
            END IF;
        	--obtener funcionario del usuario
            select f.id_funcionario into v_id_funcionario_usuario
            from segu.tusuario u
            inner join segu.tpersona p on p.id_persona = u.id_persona
            inner join orga.tfuncionario f on f.id_persona = p.id_persona
            where u.id_usuario = p_id_usuario;

            if (v_id_funcionario_usuario is null) then
            	v_id_funcionario_usuario = -1;
            end if;

        select coalesce(pxp.list(su.id_sucursal::text),'-1') into v_sucursales
            from vef.tsucursal_usuario su
            where su.id_usuario = p_id_usuario and su.estado_reg = 'activo';

            v_select = 'ven.id_venta';
            v_join = 'inner join wf.testado_wf ewf on ewf.id_estado_wf = ven.id_estado_wf';

            if p_administrador !=1 then
            	if (v_historico = 'si') then
                	v_select = 'distinct(ven.id_venta)';
                	v_join = 'inner join wf.testado_wf ewf on ewf.id_proceso_wf = ven.id_proceso_wf';
                end if;

                if (v_parametros.tipo_usuario = 'vendedor') then
                  v_filtro = ' (ven.id_usuario_reg='||p_id_usuario::varchar||') and ';
                elsif (v_parametros.tipo_usuario = 'cajero') THEN
                  v_filtro = ' (ewf.id_funcionario='||v_id_funcionario_usuario::varchar||') and ';
                ELSE
                  v_filtro = ' 0 = 0 and ';
                end if;

            else
            	v_filtro = ' 0 = 0 and ';
            end if;

            if v_parametros.tipo_factura = 'pedido' then
               v_join_destino = '	inner join vef.vcliente clides on clides.id_cliente = ven.id_cliente_destino';
            else
               v_join_destino = '';
            end if;

			--Sentencia de la consulta de conteo de registros
			v_consulta:='
            		select count(' || v_select || ')
					    from vef.tventa ven
					    inner join segu.tusuario usu1 on usu1.id_usuario = ven.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ven.id_usuario_mod
					    inner join vef.vcliente cli on cli.id_cliente = ven.id_cliente
                        '||v_join_destino||'
                        inner join vef.tsucursal suc on suc.id_sucursal = ven.id_sucursal
                        left join conta.tauxiliar aux on aux.id_auxiliar =  ven.id_auxiliar_anticipo
                        inner join vef.tpunto_venta puve on puve.id_punto_venta = ven.id_punto_venta
                        inner join param.tmoneda mon on mon.id_moneda = ven.id_moneda

                        left join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                        left join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago

                        left join segu.vusuario usua on usua.id_usuario = ven.id_usuario_cajero

                        ' || v_join || '
                        where ven.estado_reg = ''activo'' and ' || v_filtro;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
	/*********************************
 	#TRANSACCION:  'VF_VENCONFBAS_SEL'
 	#DESCRIPCION:	Obtener configuraciones basicas para sistema de ventas
 	#AUTOR:		admin
 	#FECHA:		01-06-2015 05:58:00
	***********************************/

	elsif(p_transaccion='VF_VENCONFBAS_SEL')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='	select variable, valor
						 	from pxp.variable_global
						 	where variable like ''vef_%''
						 union all
						 	select ''sucursales''::varchar,pxp.list(id_sucursal::text)::varchar
						 	from vef.tsucursal_usuario
						 	where estado_reg = ''activo'' and id_usuario = ' || p_id_usuario || '
						 	and id_sucursal is not null and id_punto_venta is null
						 union all
						 	select ''puntos_venta''::Varchar,pxp.list(id_punto_venta::text)::varchar
						 	from vef.tsucursal_usuario
						 	where estado_reg = ''activo'' and id_usuario = ' || p_id_usuario || '
						 	and id_sucursal is null and id_punto_venta is not null
                         union all
						 	select ''fecha'',to_char(now(),''DD/MM/YYYY'')::varchar
                         /*Aumentando para recueperar la moneda base*/
                         union all
                           select ''id_moneda_base''::varchar,
                                 mon.id_moneda::varchar
                           from param.tmoneda mon
                           where mon.tipo_moneda = ''base''
                         union all
                           select ''codigo_moneda_base''::varchar,
                                 mon.codigo_internacional
                           from param.tmoneda mon
                         where mon.tipo_moneda = ''base''
                         union all
                           select variable, valor
                            from pxp.variable_global
                          where variable = ''ESTACION_inicio''
                        /*******************************************/
                        /*Aumentando para verificar si se usa las nuevas instancias de pago o no*/
                         union all
                         select variable, valor
                            from pxp.variable_global
                          where variable = ''instancias_de_pago_nuevas''
						 ';

			--Definicion de la respuesta


			--Devuelve la respuesta
			return v_consulta;

		end;
	/*********************************
 	#TRANSACCION:  'VF_NOTAVENDV_SEL'
 	#DESCRIPCION:	lista el detalle de la nota de venta
 	#AUTOR:		admin
 	#FECHA:		01-06-2015 05:58:00
	***********************************/

	ELSIF(p_transaccion='VF_NOTAVENDV_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select

                              vd.id_venta,
                              vd.id_venta_detalle,
                              COALESCE(vd.precio,0) as precio,
                              vd.tipo,
                              vd.cantidad,
                              (vd.cantidad * COALESCE(vd.precio,0)) as precio_total,
                              i.codigo as codigo_nombre,
                              i.nombre as item_nombre,
                              sp.nombre_producto,
                              fo.id_formula,
                              fd.id_formula_detalle,
                              fd.cantidad as cantidad_df,
                              ifo.nombre as item_nombre_df,
                              fo.nombre as nombre_formula



                            from vef.tventa_detalle vd
                            left join alm.titem i on i.id_item = vd.id_item
                            left join vef.tformula fo on fo.id_formula = vd.id_formula
                            left join vef.vmedico me on me.id_medico = fo.id_medico
                            left join vef.tformula_detalle fd on fd.id_formula = fo.id_formula
                            left join alm.titem ifo on ifo.id_item = fd.id_item
                            left join vef.tsucursal_producto sp on sp.id_sucursal_producto = vd.id_sucursal_producto
                        where
                               vd.estado_reg = ''activo'' and
                               vd.id_venta = '||v_parametros.id_venta::varchar;

			--Definicion de la respuesta
			v_consulta:=v_consulta||' order by vd.id_venta_detalle, fd.id_formula_detalle';

			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'VF_NOTAVENDV_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		01-06-2015 05:58:00
	***********************************/

	elsif(p_transaccion='VF_NOTAVENDV_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select
                            count(vd.id_venta_detalle) as total,
                            SUM(vd.cantidad*COALESCE(vd.precio,0)) as suma_total
                         from vef.tventa_detalle vd
                         where  id_venta = '||v_parametros.id_venta::varchar||'
                              and vd.estado_reg = ''activo''
                          group by vd.id_venta ';

			--Definicion de la respuesta


			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'VF_NOTVENV_SEL'
 	#DESCRIPCION:   Lista de la cabecera de la nota de venta
 	#AUTOR:		admin
 	#FECHA:		01-06-2015 05:58:00
	***********************************/

	elsif(p_transaccion='VF_NOTVENV_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						ven.id_venta,
						ven.id_cliente,
						ven.id_sucursal,
						ven.id_proceso_wf,
						ven.id_estado_wf,
						ven.estado_reg,
						ven.nro_tramite,
						ven.a_cuenta,
						ven.total_venta,
						ven.fecha_estimada_entrega,
						ven.usuario_ai,
						ven.fecha_reg,
						ven.id_usuario_reg,
						ven.id_usuario_ai,
						ven.id_usuario_mod,
						ven.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        ven.estado,
                        cli.nombre_completo,
                        suc.nombre,
                        suc.direccion,
                        suc.correo,
                        suc.telefono,
                        pxp.f_convertir_num_a_letra(ven.total_venta) as total_string

						from vef.tventa ven
						inner join segu.tusuario usu1 on usu1.id_usuario = ven.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ven.id_usuario_mod
				        inner join vef.vcliente cli on cli.id_cliente = ven.id_cliente
                        inner join vef.tsucursal suc on suc.id_sucursal = ven.id_sucursal
                       where  id_venta = '||v_parametros.id_venta::varchar;


			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'VF_VENREP_SEL'
 	#DESCRIPCION:   Reporte de Recibo o Factura
 	#AUTOR:		admin
 	#FECHA:		01-06-2015 05:58:00
	***********************************/

	elsif(p_transaccion='VF_VENREP_SEL')then

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
 	#TRANSACCION:  'VF_VENREC_SEL'
 	#DESCRIPCION:   Reporte de Recibo o Factura
 	#AUTOR:		admin
 	#FECHA:		01-06-2015 05:58:00
	***********************************/

	elsif(p_transaccion='VF_VENREC_SEL')then

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
                        upper(ven.nombre_factura)::varchar as nombre_factura,
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
            suc.zona,

            /*aumentando condicion moneda base*/

            (select mon.codigo_internacional
            from param.tmoneda mon
            where mon.tipo_moneda = ''base'') as moneda_base,

            (select mon.codigo
            from param.tmoneda mon
            where mon.tipo_moneda = ''base'') as codigo_moneda,

            COALESCE(to_char((EXTRACT(DAY FROM ven.fecha::date)),''00'')||substring(Upper(to_char(ven.fecha::date,''month''))from 1 for 3)||RIGHT((EXTRACT(YEAR FROM ven.fecha::date))::varchar,2))::varchar as fecha_ingles,

            (select string_agg(pago.mop_code,''/'')
            FROM vef.tventa ven
            inner join vef.tventa_forma_pago form on form.id_venta = ven.id_venta
            inner join obingresos.tmedio_pago_pw pago on pago.id_medio_pago_pw = form.id_medio_pago
            where ven.id_venta = '||v_parametros.id_venta::varchar||')::varchar as forma_pago,


            (select pto_ven.codigo
            from vef.tpunto_venta pto_ven
            where pto_ven.id_punto_venta = '||v_parametros.id_punto_venta::varchar||')::varchar as codigo_iata,

            moneda.codigo::varchar as codigo_moneda_recibo,
			moneda.moneda::varchar as moneda_literal
            /***********************************/

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
              inner join param.tmoneda moneda on moneda.id_moneda = ven.id_moneda_venta_recibo


             where  id_venta = '||v_parametros.id_venta::varchar;


			--Devuelve la respuesta
            raise notice 'consulta....%',v_consulta;
			return v_consulta;

		end;


   /*********************************
 	#TRANSACCION:  'VF_VENDETREP_SEL'
 	#DESCRIPCION:   Reporte Detalle de Recibo o Factura
 	#AUTOR:		admin
 	#FECHA:		01-06-2015 05:58:00
	***********************************/

	elsif(p_transaccion='VF_VENDETREP_SEL')then

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
 	#TRANSACCION:  'VF_VENDESREP_SEL'
 	#DESCRIPCION:   Reporte Descripciones de Recibo o Factura
 	#AUTOR:		admin
 	#FECHA:		01-06-2015 05:58:00
	***********************************/

	elsif(p_transaccion='VF_VENDESREP_SEL')then

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
 	#TRANSACCION:  'VF_VEND_SEL'
 	#DESCRIPCION:	ventas deposito
 	#AUTOR:		MMV
 	#FECHA:		3/10/2017
	***********************************/

	elsif(p_transaccion='VF_VEND_SEL')then

    	begin

        if (v_parametros.punto = 'puntoVenta') then
        v_columnas_punto = 'puve.nombre';
        v_join_punto = 'inner join vef.tpunto_venta puve on puve.id_punto_venta = ven.id_punto_venta';
        else
        v_columnas_punto = 's.nombre';
        v_join_punto ='left join vef.tpunto_venta puve on puve.id_punto_venta = ven.id_punto_venta';
        end if;


        	v_consulta:='with forma_pago_temporal as(
					    	select count(*)as cantidad_forma_pago,
                            vfp.id_venta,
					        pxp.list(fp.id_forma_pago::text) as id_forma_pago, pxp.list(fp.nombre) as forma_pago,
                            sum(monto_transaccion) as monto_transaccion,pxp.list(vfp.numero_tarjeta) as numero_tarjeta,
                            pxp.list(vfp.codigo_tarjeta) as codigo_tarjeta,pxp.list(vfp.tipo_tarjeta) as tipo_tarjeta
					        from vef.tventa_forma_pago vfp
					        inner join vef.tforma_pago fp on fp.id_forma_pago = vfp.id_forma_pago
					        group by vfp.id_venta)
                    select  ven.id_venta,
                            ven.id_cliente,
                            ven.id_dosificacion,
                            puve.id_punto_venta,
                            ven.nro_factura,
                            vcl.nombre_completo,
                            vcl.nit,
                            ven.total_venta,
                            ven.fecha as fecha_doc,
                            '||v_columnas_punto||' as sucursal,
                            (case when (forpa.cantidad_forma_pago > 1) then
                                0::integer
                            else
                                forpa.id_forma_pago::integer
                            end) as id_forma_pago,
                            (case when (forpa.cantidad_forma_pago > 1) then
                                ''DIVIDIDO''::varchar
                            else
                                forpa.forma_pago::varchar
                            end) as forma_pago,
                            ven.observaciones,

                            (case when (forpa.cantidad_forma_pago > 1) then
                                0::numeric
                            else
                                forpa.monto_transaccion::numeric
                            end) as monto_forma_pago,
                            ven.comision,
                            ven.estado,
                            ven.cod_control,
                            ven.tipo_factura,
                            ven.usuario_ai,
                            ven.fecha_reg,
                            ven.id_usuario_reg,
                            ven.id_usuario_ai,
                            ven.id_usuario_mod,
                            ven.fecha_mod,
                            ven.estado_reg,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            dos.nroaut
                            from vef.tventa ven
                            inner join segu.tusuario usu1 on usu1.id_usuario = ven.id_usuario_reg
                            inner join vef.vcliente vcl on vcl.id_cliente = ven.id_cliente
                            inner join vef.tsucursal s on s.id_sucursal = ven.id_sucursal
                            inner join forma_pago_temporal forpa on forpa.id_venta = ven.id_venta
                            left join segu.tusuario usu2 on usu2.id_usuario = ven.id_usuario_mod
                            '||v_join_punto||'
                            left join param.tmoneda mon on mon.id_moneda = ven.id_moneda
                            left join vef.tdosificacion dos on dos.id_dosificacion = ven.id_dosificacion
                            where ven.estado_reg = ''activo'' and';

   			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
            raise notice 'CONSULTA.... %',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;
	/*********************************
 	#TRANSACCION:  'VF_VEND_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		MMV
 	#FECHA:		3/10/2017
	***********************************/

    elsif(p_transaccion='VF_VEND_CONT')then

    	begin
        if (v_parametros.punto = 'puntoVenta') then
        v_join_punto = 'inner join vef.tpunto_venta puve on puve.id_punto_venta = ven.id_punto_venta';
        else
        v_join_punto ='left join vef.tpunto_venta puve on puve.id_punto_venta = ven.id_punto_venta';
        end if;
        v_consulta:='with forma_pago_temporal as(
					    	select count(*)as cantidad_forma_pago,
                            vfp.id_venta,
					        pxp.list(fp.id_forma_pago::text) as id_forma_pago, pxp.list(fp.nombre) as forma_pago,
                            sum(monto_transaccion) as monto_transaccion,pxp.list(vfp.numero_tarjeta) as numero_tarjeta,
                            pxp.list(vfp.codigo_tarjeta) as codigo_tarjeta,pxp.list(vfp.tipo_tarjeta) as tipo_tarjeta
					        from vef.tventa_forma_pago vfp
					        inner join vef.tforma_pago fp on fp.id_forma_pago = vfp.id_forma_pago
					        group by vfp.id_venta)
                    select  count (ven.id_venta)
                            from vef.tventa ven
                            inner join segu.tusuario usu1 on usu1.id_usuario = ven.id_usuario_reg
                            inner join vef.vcliente vcl on vcl.id_cliente = ven.id_cliente
                            inner join vef.tsucursal s on s.id_sucursal = ven.id_sucursal
                            inner join forma_pago_temporal forpa on forpa.id_venta = ven.id_venta
                            left join segu.tusuario usu2 on usu2.id_usuario = ven.id_usuario_mod
                            '||v_join_punto||'
                            left join param.tmoneda mon on mon.id_moneda = ven.id_moneda
                            left join vef.tdosificacion dos on dos.id_dosificacion = ven.id_dosificacion
                            where ven.estado_reg = ''activo'' and';

    		--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
   	#TRANSACCION:  'VF_LISRCB_SEL'
   	#DESCRIPCION:	Lista de recibos para uso en filtros
   	#AUTOR:		breydi.vasquez
   	#FECHA:		21/04/2021
  	***********************************/

      elsif(p_transaccion='VF_LISRCB_SEL')then
      	begin
            v_consulta:='
                      with trecibos_fp as (select
                                          v.id_venta,
                                          v.nro_factura,
                                          v.nombre_factura,
                                          v.total_venta,
                                          coalesce(sum(bafp.importe),0) as totales,
                                          vf.id_moneda,
                                          mon.codigo_internacional as moneda,
                                          v.id_auxiliar_anticipo
                                        from vef.tventa v
                                        inner join vef.tventa_forma_pago vf on vf.id_venta = v.id_venta
                                        left join obingresos.tboleto_amadeus_forma_pago  bafp on bafp.id_venta = v.id_venta and vf.id_moneda = bafp.id_moneda
                                        inner join param.tmoneda mon on mon.id_moneda = v.id_moneda
                                        where v.tipo_factura = ''recibo''
                                        and v.estado != ''anulado''
                                        group by v.id_venta, v.nro_factura, v.nombre_factura, v.total_venta, vf.id_moneda,mon.codigo_internacional,v.id_auxiliar_anticipo

                                        union all

                                  select
                                       v.id_venta,
                                       v.nro_factura,
                                       v.nombre_factura,
                                       v.total_venta,
                                       case when vf.id_moneda = 1 then
                                                coalesce(sum(vf.monto_mb_efectivo),0)
                                       when vf.id_moneda = 2 then
                                                coalesce(sum(param.f_convertir_moneda(1, vf.id_moneda, vf.monto_mb_efectivo, v.fecha, ''O'', 50)),0)
                                       end as totales,
                                       vf.id_moneda,
                                       mon.codigo_internacional as moneda,
                                       v.id_auxiliar_anticipo
                                  from vef.tventa v
                                  left join vef.tventa_forma_pago vf on vf.id_venta_recibo = v.id_venta
                                  inner join param.tmoneda mon on mon.id_moneda = vf.id_moneda
                                  where v.tipo_factura = ''recibo''
                                  and v.estado != ''anulado''
                                  group by v.id_venta, v.nro_factura, v.nombre_factura, v.total_venta, vf.id_moneda,mon.codigo_internacional,v.id_auxiliar_anticipo

                        )
                        select
                              v.id_venta,
                              v.nro_factura,
                              v.nombre_factura,
                              to_char(v.total_venta,''9 999 999D99'')||'' ''||v.moneda as total_venta,
                              ''&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:black;">Saldo:</span> ''||to_char((v.total_venta - sum(v.totales)),''9 999 999D99'')||'' ''||v.moneda as tex_saldo,
                              v.total_venta - round(sum(totales),2) as saldo,
                              v.id_moneda,
                              v.moneda
                        from trecibos_fp v
                        where ';

          v_consulta:=v_consulta||v_parametros.filtro;
          v_consulta:=v_consulta||'group by v.id_venta, v.nro_factura, v.nombre_factura, v.total_venta, v.id_moneda, v.moneda';
          v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
          --raise notice 'resp %',v_consulta;
          return v_consulta;
          end;

  	/*********************************
   	#TRANSACCION:  'VF_LISRCB_CONT'
   	#DESCRIPCION:	Contador de registros lista de recibos para uso en filtros
   	#AUTOR:		breydi.vasquez
   	#FECHA:		21/04/2021
  	***********************************/

      elsif(p_transaccion='VF_LISRCB_CONT')then
      	begin
        v_consulta:='
        with trecibos_fp as (select
                                  v.id_venta,
                                  v.nro_factura,
                                  v.nombre_factura,
                                  v.total_venta,
                                  vf.id_moneda,
                                  mon.codigo as moneda,
                                  v.id_auxiliar_anticipo
                                from vef.tventa v
                                inner join vef.tventa_forma_pago vf on vf.id_venta = v.id_venta
                                left join obingresos.tboleto_amadeus_forma_pago  bafp on bafp.id_venta = v.id_venta and vf.id_moneda = bafp.id_moneda
                                inner join param.tmoneda mon on mon.id_moneda = v.id_moneda
                                where v.tipo_factura = ''recibo''
                                and v.estado != ''anulado''
                                group by v.id_venta, v.nro_factura, v.nombre_factura, v.total_venta, vf.id_moneda,mon.codigo,v.id_auxiliar_anticipo


                                union all

                          select
                               v.id_venta,
                               v.nro_factura,
                               v.nombre_factura,
                               v.total_venta,
                               vf.id_moneda,
                               mon.codigo as moneda,
                               v.id_auxiliar_anticipo
                          from vef.tventa v
                          left join vef.tventa_forma_pago vf on vf.id_venta_recibo = v.id_venta
                          inner join param.tmoneda mon on mon.id_moneda = vf.id_moneda
                          where v.tipo_factura = ''recibo''
                          and v.estado != ''anulado''
                          group by v.id_venta, v.nro_factura, v.nombre_factura, v.total_venta, vf.id_moneda,mon.codigo,v.id_auxiliar_anticipo

                    )
                  select count(v.id_venta)
                  from trecibos_fp v
                  where ';

     			v_consulta:=v_consulta||v_parametros.filtro;
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

ALTER FUNCTION vef.ft_venta_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
