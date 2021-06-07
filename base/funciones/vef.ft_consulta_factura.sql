CREATE OR REPLACE FUNCTION vef.ft_consulta_factura (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_consulta_factura
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas
 AUTOR: 		 (breydi.vasquez)
 FECHA:	        03-03-2021
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
    v_filtro			varchar='0=0 ';
	v_iata				varchar='';
    v_fil_pv			varchar='';
    v_fil_su			varchar='';
    v_fil_td			varchar='';
    v_fil_nro_doc		varchar='';
    v_id_lug			text;
    v_inner				varchar;
    v_nroaut			varchar;
    v_tf				varchar;
    v_nro_autor			varchar='';
    v_fil_fif 			varchar= '';
    v_fil_estado_fr		varchar='';
    v_fil_nit			varchar='';
BEGIN

	v_nombre_funcion = 'vef.ft_consulta_factura';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_CONSFACX_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		breydi.vasquez
 	#FECHA:		29-01-2021
	***********************************/

	if(p_transaccion='VF_CONSFACX_SEL')then

    	begin
       	--raise 'resp %',v_parametros.nro_documento;


			IF v_parametros.id_entidad != 'TODOS' THEN

                IF v_parametros.id_sucursal != 0 THEN
                	v_fil_su = ' pv.id_sucursal = '||v_parametros.id_sucursal||' and ';
                ELSE
                  select pxp.list(suc.id_sucursal::text) into v_id_lug
                  from vef.tsucursal suc
                  left join param.tlugar lug on lug.id_lugar = suc.id_lugar
                  where lug.codigo = ''||v_parametros.id_entidad||'';
                	-- with recursive t1 (
                  --         id_l,
                  --         id_l_fk
                  --         )as (
                  --         select id_lugar, id_lugar_fk
                  --         from param.tlugar
                  --         where id_lugar = v_parametros.id_entidad
                  --         union
                  --         select l.id_lugar, l.id_lugar_fk
                  --         from param.tlugar l
                  --         inner join t1 t  on l.id_lugar_fk = t.id_l
                  --         )
                  --         select pxp.list(su.id_sucursal::text) into v_id_lug
                  --         from t1 t
                  --         inner join vef.tsucursal su on su.id_lugar =  t.id_l;

                	v_fil_su = ' pv.id_sucursal in ('||v_id_lug||') and ';
                END IF;
            END IF;

        	IF v_parametros.id_punto_venta != 0 THEN
            	v_fil_pv = ' v.id_punto_venta = '||v_parametros.id_punto_venta||' and ';
            END IF;

            IF v_parametros.tipo_documento = 'factura' THEN
            	v_fil_td = ' v.tipo_factura in(''computarizada'',''carga'',''manual'') and ';
                v_inner = 'left join vef.tdosificacion dos on dos.id_dosificacion = v.id_dosificacion';
                v_nroaut = 'dos.nroaut';

                IF v_parametros.nro_autorizacion != ''THEN
                	v_nro_autor = 'dos.nroaut = '''||v_parametros.nro_autorizacion||''' and ';
                END IF;

            ELSE
            	v_fil_td = ' v.tipo_factura in(''recibo'',''recibo_manual'') and ';
                v_inner = '';
                v_nroaut = '''''::varchar';
            END IF;

            IF v_parametros.nro_documento != '' THEN
            	v_fil_nro_doc = ' v.nro_factura = '''||v_parametros.nro_documento||''' and ';
            END IF;

            IF v_parametros.estado_documento != '' THEN
            	v_fil_estado_fr = ' v.estado = '''||v_parametros.estado_documento||''' and ';
            ELSE
            	v_fil_estado_fr = '(v.estado = ''finalizado'' or v.estado = ''anulado'') and ';
            END IF;

            IF v_parametros.fecha_ini is not null AND v_parametros.fecha_fin is not null THEN
            	v_fil_fif = ' v.fecha between '''||v_parametros.fecha_ini||'''::date and '''||v_parametros.fecha_fin||'''::date and ';
            ELSIF v_parametros.fecha_ini is not null AND v_parametros.fecha_fin is null THEN
            	v_fil_fif = ' v.fecha = '''||v_parametros.fecha_ini||'''::date and ';
            ELSIF v_parametros.fecha_ini is null AND v_parametros.fecha_fin is not null THEN
            	v_fil_fif = ' v.fecha = '''||v_parametros.fecha_fin||'''::date and ';
            END IF;

            IF v_parametros.nit != '' THEN
              v_fil_nit = 'v.nit = '''||v_parametros.nit||''' and ';
            END IF;

    		--Sentencia de la consulta
			v_consulta:='select
                                 v.id_venta,
                                 v.nro_factura,
                                 v.nit,
                                 v.nombre_factura,
                                 v.cod_control,
                                 v.fecha as fecha_factura,
                                 v.observaciones,
                                 v.total_venta,
                                 v.excento,
                                 v.tipo_factura,
                                 '||v_nroaut||',
                                 pv.nombre ||''--''|| pv.codigo as punto_venta,
                                 us.desc_persona,
                                 obd.nro_deposito,
                                 obd.monto_total,
                                 obd.fecha as fecha_dep,
                                 (select pxp.list(bas.nro_boleto::text)
                                 from vef.tboletos_asociados_fact bas
                                 where bas.id_venta = v.id_venta
                                 and bas.estado_reg = ''activo'') as nro_boleto,
                                 v.estado

                          from vef.tventa v
                          inner join segu.vusuario us on us.id_usuario = v.id_usuario_reg
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = v.id_punto_venta
                          left join obingresos.tdeposito obd on obd.id_deposito =v.id_deposito
                          '||v_inner||'
                          where v.estado_reg = ''activo'' and '||v_fil_estado_fr||'
                          '||v_fil_su||' '|| v_fil_pv ||' '|| v_fil_td||' '||v_fil_nro_doc||' '||v_nro_autor||'
                          '||v_fil_fif||' '||v_fil_nit||' ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
      if pxp.f_existe_parametro(p_tabla,'re_count')then
        v_consulta:=v_consulta||' order by v.nombre_factura asc ';
      else
			  v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
      end if;
			raise notice 'resp %',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_CONSFACX_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		breydi.vasquez
 	#FECHA:		28-01-2021
	***********************************/

	elsif(p_transaccion='VF_CONSFACX_CONT')then

		begin

			IF v_parametros.id_entidad != 'TODOS' THEN

                IF v_parametros.id_sucursal != 0 THEN
                	v_fil_su = ' pv.id_sucursal = '||v_parametros.id_sucursal||' and ';
                ELSE
                  select pxp.list(suc.id_sucursal::text) into v_id_lug
                  from vef.tsucursal suc
                  left join param.tlugar lug on lug.id_lugar = suc.id_lugar
                  where lug.codigo = ''||v_parametros.id_entidad||'';
                	-- with recursive t1 (
                  --         id_l,
                  --         id_l_fk
                  --         )as (
                  --         select id_lugar, id_lugar_fk
                  --         from param.tlugar
                  --         where id_lugar = v_parametros.id_entidad
                  --         union
                  --         select l.id_lugar, l.id_lugar_fk
                  --         from param.tlugar l
                  --         inner join t1 t  on l.id_lugar_fk = t.id_l
                  --         )
                  --         select pxp.list(su.id_sucursal::text) into v_id_lug
                  --         from t1 t
                  --         inner join vef.tsucursal su on su.id_lugar =  t.id_l;

                	v_fil_su = ' pv.id_sucursal in ('||v_id_lug||') and ';
                END IF;
            END IF;

        	IF v_parametros.id_punto_venta != 0 THEN
            	v_fil_pv = ' v.id_punto_venta = '||v_parametros.id_punto_venta||' and ';
            END IF;

            IF v_parametros.tipo_documento = 'factura' THEN
            	v_fil_td = ' v.tipo_factura in(''computarizada'',''carga'',''manual'') and ';
                v_inner = 'left join vef.tdosificacion dos on dos.id_dosificacion = v.id_dosificacion';

                IF v_parametros.nro_autorizacion != ''THEN
                	v_nro_autor = 'dos.nroaut = '''||v_parametros.nro_autorizacion||''' and ';
                END IF;
            ELSE
            	v_fil_td = ' v.tipo_factura in(''recibo'',''recibo_manual'') and ';
                v_inner = '';
            END IF;

            IF v_parametros.nro_documento != '' THEN
            	v_fil_nro_doc = ' v.nro_factura = '''||v_parametros.nro_documento||''' and ';
            END IF;

            IF v_parametros.estado_documento != '' THEN
            	v_fil_estado_fr = ' v.estado = '''||v_parametros.estado_documento||''' and ';
            ELSE
            	v_fil_estado_fr = '(v.estado = ''finalizado'' or v.estado = ''anulado'') and ';
            END IF;

            IF v_parametros.fecha_ini is not null AND v_parametros.fecha_fin is not null THEN
            	v_fil_fif = ' v.fecha between '''||v_parametros.fecha_ini||'''::date and '''||v_parametros.fecha_fin||'''::date and ';
            ELSIF v_parametros.fecha_ini is not null AND v_parametros.fecha_fin is null THEN
            	v_fil_fif = ' v.fecha = '''||v_parametros.fecha_ini||'''::date and ';
            ELSIF v_parametros.fecha_ini is null AND v_parametros.fecha_fin is not null THEN
            	v_fil_fif = ' v.fecha = '''||v_parametros.fecha_fin||'''::date and ';
            END IF;

            IF v_parametros.nit != '' THEN
            	v_fil_nit = 'v.nit = '''||v_parametros.nit||''' and ';
            END IF;

			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(v.id_venta),
                          sum(v.total_venta),
                          sum(v.excento),
                          sum(obd.monto_total)
                          from vef.tventa v
                          inner join segu.vusuario us on us.id_usuario = v.id_usuario_reg
                          inner join vef.tpunto_venta pv on pv.id_punto_venta = v.id_punto_venta
                          left join obingresos.tdeposito obd on obd.id_deposito =v.id_deposito
                          '||v_inner||'
                          where v.estado_reg = ''activo'' and '||v_fil_estado_fr||'
                          '||v_fil_su||' '|| v_fil_pv ||' '|| v_fil_td||' '||v_fil_nro_doc||' '||v_nro_autor||'
                          '||v_fil_fif||' '||v_fil_nit||' ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_DJSVEN_SEL'
 	#DESCRIPCION:	Consulta detalles de venta
 	#AUTOR:		breydi.vasquez
 	#FECHA:		28-04-2021
	***********************************/

	elsif(p_transaccion='VF_DJSVEN_SEL')then

		begin

			select tipo_factura into v_tf
            from vef.tventa
            where id_venta =  v_parametros.id_venta;

            IF v_tf = 'computarizada' THEN
                v_inner = 'inner join vef.tdosificacion dos on dos.id_dosificacion = v.id_dosificacion';
                v_nroaut = 'dos.nroaut';
            ELSE
            	v_inner = '';
                v_nroaut = '''''::varchar';
            END IF;

        	v_consulta:= 'SELECT	TO_JSON(ROW_TO_JSON(jsonD) :: TEXT) #>> ''{}'' as jsonData
                          FROM (
                          SELECT ROW_TO_JSON(tvalue_data) as data
                          FROM(
                              select
                              v.id_venta,
                              v.nro_factura,
                              v.nit,
                              v.nombre_factura,
                              v.cod_control,
                              v.fecha as fecha_factura,
                              v.observaciones,
                              v.total_venta,
                              v.excento,
                              v.comision,
                              v.estado,
                              v.tipo_factura,
                              aux.nombre_auxiliar ||'' ''||aux.codigo_auxiliar as nombre_auxiliar,
                              v.id_auxiliar_anticipo as anticipo,
                              case when v.tipo_factura = ''recibo'' then
	                              	''Recibo''
                              	   when v.tipo_factura = ''recibo_manual'' then
    	                            ''Recibo Manual''
                                   when v.tipo_factura = ''computarizada'' then
                                    ''Factura Computarizada''
                                   when v.tipo_factura = ''manual'' then
                                    ''Factura Manual''
                                   when v.tipo_factura = ''carga'' then
                                    ''Factura Carga''
                                    end tit_fac,
                              pv.nombre ||''-''|| pv.codigo as punto_venta,
                              us.desc_persona,
                              su.codigo ||''-''|| su.nombre as sucursal,
                              '||v_nroaut||',
                              case when v.id_deposito is not null then
                              (
                            	SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(dep))) as detalle_depo
                                	FROM (
                                    SELECT
                                      ob.nro_deposito,
                                      ob.monto_total,
                                      ob.fecha,
                                      mon.codigo_internacional as moneda_dep
                                    FROM obingresos.tdeposito ob
                                    inner join param.tmoneda mon on mon.id_moneda = ob.id_moneda_deposito
                                    WHERE ob.id_deposito = v.id_deposito
                                  ) dep
                              )
                              end as deposito,
                              (
                                SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(det))) as detalle_venta
                                    FROM (
                                      SELECT
                                        vd.tipo,
                                        vd.precio,
                                        vd.cantidad,
                                        vd.precio_sin_descuento,
                                        vd.porcentaje_descuento,
                                        vd.descripcion,
                                        vd.bruto,
                                        vd.ley,
                                        vd.kg_fino,
                                        vd.obs,
                                        vd.serie,
                                        ingas.tipo,
                                        ingas.desc_ingas,
                                        mon.codigo_internacional as moneda_det

                                      FROM vef.tventa_detalle vd
                                      inner join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = vd.id_producto
                                      left join param.tmoneda mon on mon.id_moneda = ingas.id_moneda
                                      WHERE vd.id_venta = v.id_venta
                                    ) det
                                ),
                              (
                                SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(fpago))) as formas_pago
                                    FROM (
                                      SELECT
                                        vf.monto,
                                        vf.monto_transaccion,
                                        vf.cambio,
                                        vf.monto_mb_efectivo,
                                        case when mon.codigo_internacional = ''USD'' then
                                        	param.f_convertir_moneda(1, vf.id_moneda, vf.monto_mb_efectivo, v.fecha, ''O'', 50)
                                        else
	                                        vf.monto_mb_efectivo
                                        end as monto_forma_pago,
                                        vf.numero_tarjeta,
                                        vf.codigo_tarjeta,
                                        vf.tipo_tarjeta,
                                        mon.codigo_internacional as moneda_fp,
                                        mp.name,
                                        aux.codigo_auxiliar||'' -> ''||aux.nombre_auxiliar as cod_cuenta,
                                        venr.nro_factura as nro_recibo,
                                        vf.id_venta_recibo
                                      FROM vef.tventa_forma_pago vf
                                      left join param.tmoneda mon on mon.id_moneda = vf.id_moneda
                                      inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = vf.id_medio_pago
                                      left join conta.tauxiliar aux on aux.id_auxiliar = vf.id_auxiliar
                                      left join vef.tventa venr on venr.id_venta =  vf.id_venta_recibo
                                      WHERE vf.id_venta = v.id_venta
                                    ) fpago
                                ),
                              (
                                SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(boas))) as bolasoc
                                    FROM (
                                      SELECT
                                        ba.fecha_emision,
                                        ba.nro_boleto,
                                        ba.pasajero,
                                        ba.nit,
                                        ba.ruta,
                                        ba.razon,
                                        ba.fecha_reg,
                                        ba.fecha_mod,
                                        ba.estado_reg
                                      FROM vef.tboletos_asociados_fact ba
                                      WHERE ba.id_venta = v.id_venta
                                    ) boas
                                ),
                                (
                                  SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(doc_pag_c_rec))) as pagos_con_recibo
                                      FROM (
                                        (SELECT
                                              rvfp.id_venta,
                                              vpr.nit,
                                              vpr.nombre_factura,
                                              vpr.nro_factura::varchar,
                                              vpr.fecha,
                                              vpr.total_venta,
                                              vpr.tipo_factura,
                                              mon.codigo_internacional as mon_fp_ro
                                        FROM vef.tventa_forma_pago rvfp
                                        INNER join vef.tventa vpr on vpr.id_venta = rvfp.id_venta
                                        INNER join param.tmoneda mon on mon.id_moneda = rvfp.id_moneda
                                        WHERE rvfp.id_venta_recibo = v.id_venta
                                        ORDER BY vpr.nombre_factura ASC)

                                        UNION ALL

                                        (SELECT
                                             bfp.id_venta,
                                             ''''::varchar,
                                             boam.pasajero,
                                             boam.nro_boleto,
                                             boam.fecha_emision,
                                             boam.total,
                                             ''Boleto'',
                                             mon.codigo_internacional as mon_fp_ro

                                        FROM obingresos.tboleto_amadeus_forma_pago bfp
                                        INNER join obingresos.tboleto_amadeus boam on boam.id_boleto_amadeus = bfp.id_boleto_amadeus
                                        INNER join param.tmoneda mon on mon.id_moneda = bfp.id_moneda
                                        WHERE bfp.id_venta = v.id_venta
                                        ORDER BY boam.pasajero ASC)
                                      ) doc_pag_c_rec
                                  )
                                from vef.tventa v
                                inner join segu.vusuario us on us.id_usuario = v.id_usuario_reg
                                inner join vef.tpunto_venta pv on pv.id_punto_venta = v.id_punto_venta
                                left join vef.tsucursal su on su.id_sucursal = pv.id_sucursal
                                left join conta.tauxiliar aux on aux.id_auxiliar = v.id_auxiliar_anticipo
                                '||v_inner||'
                                where v.id_venta = '||v_parametros.id_venta||'
                               )tvalue_data
                          ) jsonD';

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
