CREATE OR REPLACE FUNCTION vef.ft_repventa_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
  /**************************************************************************
   SISTEMA:		Sistema de Ventas
   FUNCION: 		vef.ft_repventa_sel
   DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tventa'
   AUTOR: 		 (admin)
   FECHA:	        01-06-2015 05:58:00
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
    v_id_funcionario_usuario	integer;
    v_sucursales		varchar;
    v_filtro			varchar;
    v_join				varchar;
    v_select			varchar;
    v_historico			varchar;
    v_id_sucursal		integer;
    v_id_moneda			integer;
    v_id_moneda_usd		integer;
    v_cod_moneda		varchar;
    v_group_by			varchar;
    v_id_pais			integer;
    v_filtro_cajero_boleto		varchar;
    v_filtro_cajero_factura		varchar;
    v_usuario			varchar;

    v_filtro_fecha_desde	varchar;
    v_filtro_fecha_hasta	varchar;
    v_filtro_id_punto_venta	varchar;
    v_filtro_id_concepto	varchar;
    v_moneda_base			varchar;
	v_filtro_cajero_boleto_1 varchar;
	v_id_apertura_cajero_principal	integer;
    v_id_cajero_auxiliar	varchar;
    v_nombre_pv			varchar;
    v_consulta_insertar_reporte	varchar;

    v_filtro_id_cajero varchar;
  	v_filtro_tipo_factura varchar;
    v_filtro_nit varchar;


  BEGIN

    v_nombre_funcion = 'vef.ft_repventa_sel';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
     #TRANSACCION:  'VF_CONSUC_SEL'
     #DESCRIPCION:	Obtencion de conceptos de gasto por punto de venta o sucursal
     #AUTOR:		admin
     #FECHA:		01-06-2015 05:58:00
    ***********************************/

    if(p_transaccion='VF_CONSUC_SEL')then

      begin
        IF  pxp.f_existe_parametro(p_tabla,'id_punto_venta') THEN

          select param.f_get_id_lugar_pais(s.id_lugar),mon.codigo_internacional into v_id_pais, v_cod_moneda
          from vef.tpunto_venta pv
            inner join vef.tsucursal s on s.id_sucursal = pv.id_sucursal
            inner join vef.tsucursal_moneda sm on sm.id_sucursal = s.id_sucursal
            inner join param.tmoneda mon on mon.id_moneda = sm.id_moneda
          where pv.id_punto_venta = v_parametros.id_punto_venta;


          if ( v_cod_moneda = 'USD') then
            v_select = 'select ''CASH USD''::varchar,''4MONEDA1''::varchar as tipo UNION ALL
            		    select ''CC USD''::varchar, ''4MONEDA1''::varchar as tipo UNION ALL
                    	select ''CTE USD''::varchar, ''4MONEDA1''::varchar as tipo UNION ALL
                    	select ''MCO USD''::varchar, ''4MONEDA1''::varchar as tipo UNION ALL
                    			select ''OTRO USD''::varchar, ''4MONEDA1''::varchar as tipo';
          else
            v_select = 'select ''CASH USD''::varchar,''4MONEDA1''::varchar as tipo UNION ALL
            			select ''CC USD''::varchar, ''4MONEDA1''::varchar as tipo UNION ALL
                        select ''CTE USD''::varchar, ''4MONEDA1''::varchar as tipo UNION ALL
                        select ''MCO USD''::varchar, ''4MONEDA1''::varchar as tipo UNION ALL
                        select ''OTRO USD''::varchar, ''4MONEDA1''::varchar as tipo UNION ALL
                    			select ''CASH ' || v_cod_moneda || '''::varchar,''4MONEDA2''::varchar as tipo UNION ALL
                                select ''CC ' || v_cod_moneda || '''::varchar,''4MONEDA2''::varchar as tipo UNION ALL
                                select ''CTE ' || v_cod_moneda || '''::varchar,''4MONEDA2''::varchar as tipo UNION ALL
                                select ''MCO ' || v_cod_moneda || '''::varchar,''4MONEDA2''::varchar as tipo UNION ALL
                                select ''OTRO ' || v_cod_moneda || '''::varchar,''4MONEDA2''::varchar as tipo';
          end if;

          v_filtro = ' id_punto_venta = ' || v_parametros.id_punto_venta;

          v_consulta:='(' || v_select || ' UNION ALL select ''TOTAL'',''1TARIFA''::varchar as tipo
                			UNION ALL
                			select cig.desc_ingas,''3CONCEPTO''::varchar as tipo
                			 from vef.tventa v
                             inner join vef.tventa_detalle vd
                             	on vd.id_venta = v.id_venta
                             inner join vef.tsucursal_producto sp
                             	on vd.id_sucursal_producto = sp.id_sucursal_producto
                             inner join param.tconcepto_ingas cig
                             	on cig.id_concepto_ingas = sp.id_concepto_ingas
                             where v.id_punto_venta = ' || v_parametros.id_punto_venta || ' and
             			(v.fecha between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''')
                        group by cig.desc_ingas';
        ELSE
          select param.f_get_id_lugar_pais(s.id_lugar),mon.codigo_internacional into v_id_pais, v_cod_moneda
          from vef.tsucursal  s
            inner join vef.tsucursal_moneda sm on sm.id_sucursal = s.id_sucursal
            inner join param.tmoneda mon on mon.id_moneda = sm.id_moneda
          where s.id_sucursal = v_parametros.id_sucursal;

          if ( v_cod_moneda = 'USD') then
            v_select = 'select ''USD'',''4MONEDA''::varchar as tipo';
          else
            v_select = 'select ''USD'',''4MONEDA''::varchar as tipo UNION ALL
                    			select ''' || v_cod_moneda || ''',''4MONEDA''::varchar as tipo';
          end if;

          v_filtro = ' id_sucursal = ' || v_parametros.id_sucursal;

          v_consulta:='(' || v_select || ' UNION ALL select ''TOTAL'',''1TARIFA''::varchar as tipo
                			UNION ALL
                			select cig.desc_ingas,''3CONCEPTO''::varchar as tipo
                			 from vef.tventa v
                             inner join vef.tventa_detalle vd
                             	on vd.id_venta = v.id_venta
                             inner join vef.tsucursal_producto sp
                             	on vd.id_sucursal_producto = sp.id_sucursal_producto
                             inner join param.tconcepto_ingas cig
                             	on cig.id_concepto_ingas = sp.id_concepto_ingas
                             where v.id_sucursal = ' || v_parametros.id_sucursal || ' and
             			(v.fecha between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''')
                        group by cig.desc_ingas';
        END IF;

        v_consulta:= v_consulta || ')
                        UNION ALL
                        (select imp.codigo,''2IMPUESTO''::varchar as tipo
                 from  obingresos.tboleto_impuesto bimp
                 inner join obingresos.tboleto b on b.id_boleto = bimp.id_boleto
                 inner join obingresos.timpuesto imp on imp.id_impuesto = bimp.id_impuesto
                where ' || v_filtro || ' and
             			(b.fecha_emision between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''')
                group by imp.codigo
                order by imp.codigo)
                order by 2,1';

        --Devuelve la respuesta
        raise notice '%',v_consulta;
        return v_consulta;

      end;

    /*********************************
     #TRANSACCION:  'VF_REPDETBOA_SEL'
     #DESCRIPCION:	Reporte de Boa para detalle de ventas
     #AUTOR:		admin
     #FECHA:		01-06-2015 05:58:00
    ***********************************/

    elsif(p_transaccion='VF_REPDETBOA_SEL')then

      begin
        IF  pxp.f_existe_parametro(p_tabla,'id_punto_venta') THEN
          v_filtro = ' id_punto_venta = ' || v_parametros.id_punto_venta;
          select
          id_sucursal,
          nombre
          into
          v_id_sucursal,
          v_nombre_pv
          from vef.tpunto_venta
          where id_punto_venta = v_parametros.id_punto_venta;
        else
          v_filtro = ' id_sucursal = ' || v_parametros.id_sucursal;

          select suc.nombre
          into
          v_nombre_pv
          from vef.tsucursal suc
          where suc.id_sucursal = v_parametros.id_sucursal;

          v_id_sucursal = v_parametros.id_sucursal;

        end if;

        v_filtro_cajero_boleto='';
        v_filtro_cajero_boleto_1='';
        v_filtro_cajero_factura='';


        /*Aqui ponemos la condicion para recuperar datos del cajero Auxiliar (Ismael Valdivia 08/02/2021)*/
        IF  pxp.f_existe_parametro(p_tabla,'id_usuario_cajero') THEN
          select list(distinct(aper.id_apertura_cierre_caja)::varchar)
          		 into
                 v_id_apertura_cajero_principal
          from vef.tapertura_cierre_caja aper
          where aper.id_usuario_cajero = v_parametros.id_usuario_cajero and aper.id_punto_venta = v_parametros.id_punto_venta
          and aper.fecha_apertura_cierre between v_parametros.fecha_desde and v_parametros.fecha_hasta;

          /*Aqui recuperamos el id_usuario cajero auxiliar para listar su informacion en el cajero Principal*/
            select list (distinct (ap.id_usuario_cajero)::varchar)
                   into
                   v_id_cajero_auxiliar
            from vef.tapertura_cierre_caja ap
            where ap.id_apertura_cierre_admin in (v_id_apertura_cajero_principal);
          /**************************************************************************************************/
		end if;
        /*************************************************************************************************/





        IF  pxp.f_existe_parametro(p_tabla,'id_usuario_cajero') THEN
            IF(v_parametros.id_usuario_cajero!=0)THEN
            	/*Aumentando condicion para listar de cajeros auxiliares (Ismael.Valdivia 08/02/2020)*/
                IF (v_id_cajero_auxiliar is not null) then
                  --v_filtro_cajero_boleto = ' and b.id_usuario_cajero in ('||v_parametros.id_usuario_cajero||')';
                  v_filtro_cajero_boleto_1 = ' and v.id_usuario_cajero in ('||v_parametros.id_usuario_cajero||','||v_id_cajero_auxiliar||')';
                else
                  v_filtro_cajero_boleto = ' and b.id_usuario_cajero ='||v_parametros.id_usuario_cajero||' and b.id_punto_venta ='||v_parametros.id_punto_venta;
                  v_filtro_cajero_boleto_1 = ' and v.id_usuario_cajero ='||v_parametros.id_usuario_cajero||' and v.id_punto_venta ='||v_parametros.id_punto_venta;
                end if;
                /*************************************************************************************/
            ELSE
            	/*Si el Filtro de usuario es 0 entonces tomar solo el punto de venta*/
                  v_filtro_cajero_boleto = ' and b.id_punto_venta ='||v_parametros.id_punto_venta;
                  v_filtro_cajero_boleto_1 = ' and v.id_punto_venta ='||v_parametros.id_punto_venta;
                /********************************************************************/
            END IF;

            IF(pxp.f_get_variable_global('vef_facturacion_endesis')='true')THEN
            	select cuenta into v_usuario
                from segu.tusuario
                where id_usuario=v_parametros.id_usuario_cajero;

            	IF(v_parametros.id_usuario_cajero!=0)THEN
                    v_filtro_cajero_factura = ' and v.usuario='''||v_usuario||'''';
                END IF;
            END IF;
        END IF;

        select	mon.codigo_internacional,mon.id_moneda into v_cod_moneda,v_id_moneda
        from vef.tsucursal_moneda sm
          inner join param.tmoneda mon on mon.id_moneda = sm.id_moneda
        where sm.tipo_moneda = 'moneda_base' and id_sucursal = v_id_sucursal;

        select id_moneda into v_id_moneda_usd
        from param.tmoneda
        where codigo_internacional = 'USD';


        IF(EXTRACT(YEAR from v_parametros.fecha_desde) = 2020 and EXTRACT(YEAR from v_parametros.fecha_hasta) = 2021) then
        	raise Exception 'Estimado Usuario en fecha 01/01/2021 se migro con las nuevos medios de pago, Aclarar que en la gestion 2020 hacia atras se utilizo las formas de pago antiguas.';
        end if;

		/*Cambiando condicion por fecha*/
       -- if (pxp.f_get_variable_global('instancias_de_pago_nuevas')='no') then
       if (v_parametros.fecha_desde <= '31/12/2020') then


        v_consulta:='
            ( WITH ';
        if (v_cod_moneda != 'USD') then
        	IF(pxp.f_get_variable_global('vef_facturacion_endesis')!='true')THEN
                v_consulta = v_consulta || ' forma_pago_usd  AS(
                            select vfp.id_venta,
                            round(sum(	case when fp.codigo = ''CA'' then
                                                vfp.monto_transaccion -(vfp.cambio /
                                                 param.f_get_tipo_cambio(fp.id_moneda, v.fecha::date, ''O''))
                                              ELSE
                                                  0
                                              end), 2) as monto_cash_usd,
                                   round(sum(case
                                               when fp.codigo = ''CC%'' then
                                                 vfp.monto_transaccion -(vfp.cambio /
                                                 param.f_get_tipo_cambio(fp.id_moneda,
                                                 v.fecha::date, ''O''))
                                               ELSE 0
                                             end), 2) as monto_cc_usd,
                                   round(sum(case
                                               when fp.codigo = ''CT%'' then
                                                 vfp.monto_transaccion -(vfp.cambio /
                                                 param.f_get_tipo_cambio(fp.id_moneda,
                                                 v.fecha::date, ''O''))
                                               ELSE 0
                                             end), 2) as monto_cte_usd,
                                   round(sum(case
                                               when fp.codigo = ''MCO%'' then
                                                 vfp.monto_transaccion -(vfp.cambio /
                                                 param.f_get_tipo_cambio(fp.id_moneda,
                                                 v.fecha::date, ''O''))
                                               ELSE 0
                                             end), 2) as monto_mco_usd,
                                   round(sum(case
                                               when fp.codigo not similar to ''(CA|CC|CT|MCO)%'' then
                                                 vfp.monto_transaccion -(vfp.cambio /
                                                 param.f_get_tipo_cambio(fp.id_moneda,
                                                 v.fecha::date, ''O''))
                                               ELSE 0
                                             end), 2) as monto_otro_usd,
                            pxp.list(fp.nombre) as forma_pago
                            from  vef.tventa_forma_pago vfp
                            inner join vef.tforma_pago fp on vfp.id_forma_pago = fp.id_forma_pago
                            inner join vef.tventa v on v.id_venta = vfp.id_venta
                            where fp.id_moneda = ' || v_id_moneda_usd || ' and (v.fecha::date between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''')
                            group by vfp.id_venta
                        ),';
            ELSE
            	v_consulta = v_consulta || ' forma_pago_usd  AS(
							select vfp.id_factucom,
                             round(sum(case
                                         when vfp.forma like ''CA'' then
                                           vfp.importe_pago
                                         ELSE 0
                                       end), 2) as monto_cash_usd,
                             round(sum(case
                                         when vfp.forma like ''CC%'' then
                                           vfp.importe_pago
                                         ELSE 0
                                       end), 2) as monto_cc_usd,
                             round(sum(case
                                         when vfp.forma like ''CT%'' then
                                           vfp.importe_pago
                                         ELSE 0
                                       end), 2) as monto_cte_usd,
                             round(sum(case
                                         when vfp.forma like ''MCO%'' then
                                           vfp.importe_pago
                                         ELSE 0
                                       end), 2) as monto_mco_usd,
                             round(sum(case
                                         when vfp.forma not similar to
                                           ''(CA|CC|CT|MCO)%'' then
                                           vfp.importe_pago
                                         ELSE 0
                                       end), 2) as monto_otro_usd,
                             pxp.list(vfp.nombre_forma) as forma_pago
                      from vef.tfactucompag_endesis vfp
                      inner join param.tmoneda mon on mon.codigo_internacional=vfp.moneda
                      inner join vef.tfactucom_endesis v on v.id_factucom=vfp.id_factucom
                      where mon.id_moneda = '||v_id_moneda_usd||' and
                            (v.fecha::date between '''||v_parametros.fecha_desde||''' and '''||v_parametros.fecha_hasta||''' )
                      group by vfp.id_factucom),';
            END IF;
        end if;
		IF(pxp.f_get_variable_global('vef_facturacion_endesis')!='true')THEN
            v_consulta = v_consulta || ' forma_pago_mb AS(
                          select vfp.id_venta,
                          sum(CASE when fp.codigo = ''CA'' then
                                        vfp.monto_transaccion - vfp.cambio
                                     ELSE
                                        0
                                     END) as monto_cash_mb,
                                 sum(CASE
                                       when fp.codigo like ''CC%'' then
                                         vfp.monto_transaccion - vfp.cambio
                                       ELSE 0
                                     END) as monto_cc_mb,
                                 sum(CASE
                                       when fp.codigo like ''CT%'' then
                                         vfp.monto_transaccion - vfp.cambio
                                       ELSE 0
                                     END) as monto_cte_mb,
                                 sum(CASE
                                       when fp.codigo like ''MCO%'' then
                                         vfp.monto_transaccion - vfp.cambio
                                       ELSE 0
                                     END) as monto_mco_mb,
                                 sum(CASE
                                       when fp.codigo not similar to ''(CA|CC|CT|MCO)%'' then
                                         vfp.monto_transaccion - vfp.cambio
                                       ELSE 0
                                     END) as monto_otro_mb,
                          pxp.list(fp.nombre) as forma_pago
                          from  vef.tventa_forma_pago vfp
                          inner join vef.tforma_pago fp on vfp.id_forma_pago = fp.id_forma_pago
                          inner join vef.tventa v on v.id_venta = vfp.id_venta
                          where fp.id_moneda = ' || v_id_moneda || ' and (v.fecha::date between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''')
                          group by vfp.id_venta
                      )
                      select ''' || v_cod_moneda || '''::varchar as moneda_emision,
                      ''venta''::varchar as tipo,
                      v.fecha::date as fecha,
                      v.nro_factura::varchar as correlativo_venta,
                      v.tipo_factura::varchar,
                      cli.nombre_factura,
                      v.observaciones::varchar as boleto,
                      /*Aumentando el Localizador*/
                      ''''::varchar as localizador,
                      ''''::varchar as codigo_auxiliar,
                      /***************************/
                      ''''::varchar as ruta,
                      ''''::varchar as conceptos,
                      ';
        ELSE
        	v_consulta = v_consulta ||' forma_pago_mb AS(select vfp.id_factucom,
                                       round(sum(case
                                                   when vfp.forma like ''CA'' then
                                                     vfp.importe_pago
                                                   ELSE 0
                                                 end), 2) as monto_cash_mb,
                                       round(sum(case
                                                   when vfp.forma like ''CC%'' then
                                                     vfp.importe_pago
                                                   ELSE 0
                                                 end), 2) as monto_cc_mb,
                                       round(sum(case
                                                   when vfp.forma like ''CT%'' then
                                                     vfp.importe_pago
                                                   ELSE 0
                                                 end), 2) as monto_cte_mb,
                                       round(sum(case
                                                   when vfp.forma like ''MCO%'' then
                                                     vfp.importe_pago
                                                   ELSE 0
                                                 end), 2) as monto_mco_mb,
                                       round(sum(case
                                                   when vfp.forma not similar to
                                                     ''(CA|CC|CT|MCO)%'' then
                                                     vfp.importe_pago
                                                   ELSE 0
                                                 end), 2) as monto_otro_mb,
                                       pxp.list(vfp.nombre_forma) as forma_pago
                                from vef.tfactucompag_endesis vfp
                                inner join param.tmoneda mon on mon.codigo_internacional=vfp.moneda
                                inner join vef.tfactucom_endesis v on v.id_factucom=vfp.id_factucom
                                where mon.id_moneda = '||v_id_moneda||' and (v.fecha::date between '''||v_parametros.fecha_desde||''' and
                                      '''||v_parametros.fecha_hasta||''' )
                                group by vfp.id_factucom)
						        select ''BOB''::varchar as moneda_emision,
                                   ''venta''::varchar as tipo,
                                   v.fecha::date as fecha,
                                   v.nrofac::varchar as correlativo_venta,
                                   ''''::varchar as tipo_factura,
                                   v.razon_cliente,
                                   ''''::varchar as boleto,
                                   ''''::varchar as localizador,
                                   ''''::varchar as codigo_auxiliar,
                                   v.observacion::varchar as ruta,
                                   ''''::varchar as conceptos,';

        END IF;

        if (v_cod_moneda != 'USD') then
          v_consulta = v_consulta || ' coalesce(fpusd.forma_pago || '','','''')|| coalesce(fpmb.forma_pago,'''') as forma_pago,
                    			coalesce(fpusd.monto_cash_usd, 0) as monto_cash_usd,
                                coalesce(fpusd.monto_cc_usd, 0) as monto_cc_usd,
                                coalesce(fpusd.monto_cte_usd, 0) as monto_cte_usd,
                                coalesce(fpusd.monto_mco_usd, 0) as monto_mco_usd,
                         		coalesce(fpusd.monto_otro_usd, 0) as monto_otro_usd,';
          v_group_by = ' ,fpusd.forma_pago, fpusd.monto_cash_usd,fpusd.monto_cc_usd,fpusd.monto_cte_usd,fpusd.monto_mco_usd,fpusd.monto_otro_usd';
        else
          v_group_by = '';
          v_consulta = v_consulta || ' fpmb.forma_pago as forma_pago,
                    						coalesce(fpmb.monto_cash_mb, 0) as monto_cash_usd,
                                            coalesce(fpmb.monto_cc_mb, 0) as monto_cc_mb,
                         					coalesce(fpmb.monto_cte_mb, 0) as monto_cte_mb,
                         					coalesce(fpmb.monto_mco_mb, 0) as monto_mco_mb,
                                            coalesce(fpmb.monto_otro_mb, 0) as monto_otro_usd,';
        end if;

        IF(pxp.f_get_variable_global('vef_facturacion_endesis')!='true')THEN

            v_consulta = v_consulta || '
                      coalesce(fpmb.monto_cash_mb, 0) as monto_cash_mb,
                      coalesce(fpmb.monto_cc_mb, 0) as monto_cc_mb,
                      coalesce(fpmb.monto_cte_mb, 0) as monto_cte_mb,
                      coalesce(fpmb.monto_mco_mb, 0) as monto_mco_mb,
                      coalesce(fpmb.monto_otro_mb, 0) as monto_otro_mb,
                      0::numeric,
                      string_agg((vd.precio*vd.cantidad)::text,''|'')::varchar as precios_detalles,
                      NULL::varchar as mensaje_error,
                      0::numeric as comision,
                      0::numeric,
                      0::numeric,
                      '''||COALESCE(v_nombre_pv,'')||'''::varchar as punto_venta
                      from vef.tventa v
                      inner join vef.tventa_detalle vd
                          on v.id_venta = vd.id_venta and vd.estado_reg = ''activo''
                      inner join vef.tsucursal_producto sp
                          on sp.id_sucursal_producto = vd.id_sucursal_producto
                      inner join param.tconcepto_ingas cig
                          on cig.id_concepto_ingas = sp.id_concepto_ingas
                      inner join vef.tcliente cli
                          on cli.id_cliente = v.id_cliente';
        ELSE
        	v_consulta = v_consulta || ' coalesce(fpmb.monto_cash_mb, 0) as monto_cash_mb,
                         coalesce(fpmb.monto_cc_mb, 0) as monto_cc_mb,
                         coalesce(fpmb.monto_cte_mb, 0) as monto_cte_mb,
                         coalesce(fpmb.monto_mco_mb, 0) as monto_mco_mb,
                         coalesce(fpmb.monto_otro_mb, 0) as monto_otro_mb,
                         v.monto::numeric,
                         ''''::varchar as precios_detalles,
                         NULL::varchar as mensaje_error,
                          0::numeric as ccomision
                          0::numeric,
                          0::numeric,
                          '''||COALESCE(v_nombre_pv,'')||'''::varchar as punto_venta
                  from vef.tfactucom_endesis v
                  inner join vef.tpunto_venta pv on pv.codigo=v.agt::varchar
                       inner join vef.tfactucompag_endesis vd on vd.id_factucom=v.id_factucom ';
        END IF;

        if (v_cod_moneda != 'USD') then
        	IF(pxp.f_get_variable_global('vef_facturacion_endesis')!='true')THEN
                v_consulta = v_consulta || ' left join forma_pago_usd fpusd
                            on v.id_venta = fpusd.id_venta ';
			ELSE
            	v_consulta = v_consulta || ' left join forma_pago_usd fpusd on v.id_factucom = fpusd.id_factucom ';
            END IF;

        end if;

		IF(pxp.f_get_variable_global('vef_facturacion_endesis')!='true')THEN
            v_consulta = v_consulta || ' left join forma_pago_mb fpmb
                          on v.id_venta = fpmb.id_venta
                      where v.estado = ''finalizado'' and
                        (v.fecha::date between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''')
                      group by v.fecha,v.nro_factura,v.tipo_factura,cli.nombre_factura,v.observaciones,
                                fpmb.forma_pago, fpmb.monto_cash_mb,fpmb.monto_cc_mb,fpmb.monto_cte_mb,fpmb.monto_mco_mb,fpmb.monto_otro_mb,v.total_venta_msuc ' || v_group_by || '
                      )
            union ALL --1
                (WITH ';
        ELSE
        	v_consulta = v_consulta || ' left join forma_pago_mb fpmb on v.id_factucom = fpmb.id_factucom
                  where v.estado_reg = ''emitida'' ' || v_filtro_cajero_factura || ' and
                  		pv.id_punto_venta='||v_parametros.id_punto_venta||' and
                        (v.fecha::date between '''||v_parametros.fecha_desde||''' and '''||v_parametros.fecha_hasta||''')
                  group by v.fecha,
                           v.nrofac,
                           v.monto,
                           v.razon_cliente,
                           v.observacion,
                           fpmb.forma_pago,
                           fpmb.monto_cash_mb,
                           fpmb.monto_cc_mb,
                           fpmb.monto_cte_mb,
                           fpmb.monto_mco_mb,
                           fpmb.monto_otro_mb'|| v_group_by ||')
                  UNION ALL --2
                  (WITH ';
        END IF;

        if (v_cod_moneda != 'USD') then
          v_consulta = v_consulta || ' bol_forma_pago_usd  AS(
        			select bfp.id_boleto_amadeus,
                    sum(case when fp.codigo = ''CA'' then
                           			bfp.importe
                           		ELSE
                                	0
                                END) as monto_cash_usd,
                           sum(case
                                 when fp.codigo like ''CC%'' then bfp.importe
                                 ELSE 0
                               END) as monto_cc_usd,
                           sum(case
                                 when fp.codigo like ''CT%'' then bfp.importe
                                 ELSE 0
                               END) as monto_cte_usd,
                           sum(case
                                 when fp.codigo like ''MCO%'' then bfp.importe
                                 ELSE 0
                               END) as monto_mco_usd,
                           sum(case
                                 when fp.codigo not similar to ''(CA|CC|CT|MCO)%'' then bfp.importe
                                 ELSE 0
                               END) as monto_otro_usd,
                    pxp.list(fp.nombre) as forma_pago
                    from  obingresos.tboleto_amadeus_forma_pago bfp
                    inner join obingresos.tboleto_amadeus b on b.id_boleto_amadeus = bfp.id_boleto_amadeus
                    inner join obingresos.tforma_pago fp on bfp.id_forma_pago = fp.id_forma_pago
                    where ' || v_filtro || ' and
             			(b.fecha_emision between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''') and fp.id_moneda = ' || v_id_moneda_usd || '
                    group by bfp.id_boleto_amadeus
        			),';
        end if;

        v_consulta = v_consulta || 'bol_forma_pago_mb AS(
                select bfp.id_boleto_amadeus,
                sum(case when fp.codigo = ''CA'' then
                           			bfp.importe
                           		ELSE
                                	0
                                END) as monto_cash_mb,
                           sum(case
                             when fp.codigo like ''CC%'' then bfp.importe
                             ELSE 0
                           END) as monto_cc_mb,
                       sum(case
                             when fp.codigo like ''CT%'' then bfp.importe
                             ELSE 0
                           END) as monto_cte_mb,
                       sum(case
                             when fp.codigo like ''MCO%'' then bfp.importe
                             ELSE 0
                           END) as monto_mco_mb,
                       sum(case
                             when fp.codigo not similar to ''(CA|CC|CT|MCO)%'' then bfp.importe
                             ELSE 0
                           END) as monto_otro_mb,
                pxp.list(fp.nombre) as forma_pago
                 from  obingresos.tboleto_amadeus_forma_pago bfp
                 inner join obingresos.tboleto_amadeus b on b.id_boleto_amadeus = bfp.id_boleto_amadeus
                 inner join obingresos.tforma_pago fp on bfp.id_forma_pago = fp.id_forma_pago
                where ' || v_filtro || ' and
             			(b.fecha_emision between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''') and fp.id_moneda = ' || v_id_moneda || '
                group by bfp.id_boleto_amadeus
            ), bol_impuesto AS(
            	select bimp.id_boleto,string_agg(bimp.importe::text,''|'')::varchar as monto_impuesto,string_agg(imp.codigo,''|'')::varchar as impuesto
                 from  obingresos.tboleto_impuesto bimp
                 inner join obingresos.tboleto_amadeus b on b.id_boleto_amadeus = bimp.id_boleto
                 inner join obingresos.timpuesto imp on imp.id_impuesto = bimp.id_impuesto
                where ' || v_filtro || ' and
             			(b.fecha_emision between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''')
                group by bimp.id_boleto
            )
             SELECT b.moneda::varchar as moneda_emision,
             ''boleto''::varchar as tipo ,b.fecha_emision,
             ''''::varchar as correlativo_venta,
             ''''::varchar as tipo_factura,
             b.pasajero::varchar as nombre_factura,
             b.nro_boleto::varchar as boleto,
             /*Aumentando para el localizador*/
             b.localizador::varchar as localizador,
             list((aux.codigo_auxiliar || ''-'' || aux.nombre_auxiliar))::varchar as codigo_auxiliar,
             /********************************/
             b.ruta_completa as ruta ,
             ''''::varchar as conceptos,
             ';
        if (v_cod_moneda != 'USD') then
          v_consulta = v_consulta || ' coalesce(fpusd.forma_pago || '','','''')|| coalesce(fpmb.forma_pago,'''') as forma_pago,
                    		case
                      when b.voided != ''si'' then coalesce(fpusd.monto_cash_usd,
                        0)
                      else 0
                    end as monto_cash_usd,
                    case
                      when b.voided != ''si'' then coalesce(fpusd.monto_cc_usd, 0)
                      else 0
                    end as monto_cc_usd,
                    case
                      when b.voided != ''si'' then coalesce(fpusd.monto_cte_usd, 0
                        )
                      else 0
                    end as monto_cte_usd,
                    case
                      when b.voided != ''si'' then coalesce(fpusd.monto_mco_usd, 0
                        )
                      else 0
                    end as monto_mco_usd,
                    case
                      when b.voided != ''si'' then coalesce(fpusd.monto_otro_usd,
                        0)
                      else 0
                    end as monto_otro_usd,';
          v_group_by = ' ,fpusd.forma_pago, fpusd.monto_cash_usd,fpusd.monto_cc_usd,
                      fpusd.monto_cte_usd, fpusd.monto_mco_usd,fpusd.monto_otro_usd ';
        else
          v_consulta = v_consulta || ' fpmb.forma_pago as forma_pago,
                    					case when b.voided != ''si'' then coalesce(fpmb.monto_cash_mb,0) else 0 as monto_cash_usd,
                                        case when b.voided != ''si'' then coalesce(fpmb.monto_cc_mb,0) else 0 as monto_cc_usd,
                                        case when b.voided != ''si'' then coalesce(fpmb.monto_cte_mb,0) else 0 as monto_cte_usd,
                                        case when b.voided != ''si'' then coalesce(fpmb.monto_mco_mb,0) else 0 as monto_mco_usd,
                                        case when b.voided != ''si'' then coalesce(fpmb.monto_otro_mb,0) else 0 as monto_otro_usd,';
          v_group_by = '';
        end if;
        v_consulta = v_consulta ||  '
             case when b.voided != ''si'' then coalesce(fpmb.monto_cash_mb,0) else 0 end as monto_cash_mb,
             case when b.voided != ''si'' then coalesce(fpmb.monto_cc_mb,0) else 0 end as monto_cc_mb,
             case when b.voided != ''si'' then coalesce(fpmb.monto_cte_mb,0) else 0 end as monto_cte_mb,
             case when b.voided != ''si'' then coalesce(fpmb.monto_mco_mb,0) else 0 end as monto_mco_mb,
             case when b.voided != ''si'' then coalesce(fpmb.monto_otro_mb,0) else 0 end as monto_otro_mb,
             case when b.voided != ''si'' then coalesce(b.total,0) else 0 end as total,
             --b.total,
             imp.monto_impuesto as precios_conceptos,
             b.mensaje_error,
             b.comision,
             0::numeric,
             0::numeric,
             '''||COALESCE(v_nombre_pv,'')||'''::varchar as punto_venta
             from obingresos.tboleto_amadeus b
             ';
        if (v_cod_moneda != 'USD') then
          v_consulta = v_consulta || ' left join bol_forma_pago_usd fpusd
                      on b.id_boleto_amadeus = fpusd.id_boleto_amadeus ';
        end if;

        v_consulta = v_consulta || '
             left join bol_forma_pago_mb fpmb
                on fpmb.id_boleto_amadeus = b.id_boleto_amadeus
             left join bol_impuesto imp
                on imp.id_boleto = b.id_boleto_amadeus

        	 left join obingresos.tboleto_amadeus_forma_pago formpa on formpa.id_boleto_amadeus = b.id_boleto_amadeus
             left join conta.tauxiliar aux on aux.id_auxiliar = formpa.id_auxiliar

             where b.estado_reg = ''activo'' and b.estado=''revisado'' and ' || v_filtro || ' and
             (b.fecha_emision between ''' || v_parametros.fecha_desde || ''' and ''' ||v_parametros.fecha_hasta || ''')
             '||v_filtro_cajero_boleto||'
             group by b.fecha_emision,b.pasajero, b.voided, b.nro_boleto,b.mensaje_error,b.ruta_completa,b.moneda,b.total,imp.impuesto,
             		imp.monto_impuesto,fpmb.forma_pago,fpmb.monto_cash_mb,fpmb.monto_cc_mb,
                      fpmb.monto_cte_mb,fpmb.monto_mco_mb,fpmb.monto_otro_mb,b.comision, b.localizador/*,aux.codigo_auxiliar,aux.nombre_auxiliar*/ '|| v_group_by || ')
             order by fecha,boleto,correlativo_venta';

      else --------------------------------------------------------------------------Aqui las nuevas instancias de pago
      		v_consulta:='
            ( WITH ';
        if (v_cod_moneda != 'USD') then
        	IF(pxp.f_get_variable_global('vef_facturacion_endesis')='true')THEN
                v_consulta = v_consulta || ' forma_pago_usd  AS(
                            select vfp.id_venta,
                            round(sum(	case when fp.fop_code = ''CA'' and v.id_deposito is null then
                                                vfp.monto_transaccion -(vfp.cambio /
                                                 param.f_get_tipo_cambio(vfp.id_moneda, v.fecha::date, ''O''))
                                              ELSE
                                                  0
                                              end), 2) as monto_cash_usd,
                                   round(sum(case
                                               when fp.fop_code = ''CC'' then
                                                 vfp.monto_transaccion -(vfp.cambio /
                                                 param.f_get_tipo_cambio(vfp.id_moneda,
                                                 v.fecha::date, ''O''))
                                               ELSE 0
                                             end), 2) as monto_cc_usd,
                                   round(sum(case
                                               when (fp.fop_code = ''CT'' OR fp.fop_code = ''CU'') then
                                                 vfp.monto_transaccion -(vfp.cambio /
                                                 param.f_get_tipo_cambio(vfp.id_moneda,
                                                 v.fecha::date, ''O''))
                                               ELSE 0
                                             end), 2) as monto_cte_usd,
                                   round(sum(case
                                               when fp.fop_code = ''MCO'' then
                                                 vfp.monto_transaccion -(vfp.cambio /
                                                 param.f_get_tipo_cambio(vfp.id_moneda,
                                                 v.fecha::date, ''O''))
                                               ELSE 0
                                             end), 2) as monto_mco_usd,
                                   round(sum(case
                                               when fp.fop_code not similar to ''(CA|CC|CT|MCO|CU)%'' then
                                                 vfp.monto_transaccion -(vfp.cambio /
                                                 param.f_get_tipo_cambio(vfp.id_moneda,
                                                 v.fecha::date, ''O''))
                                               ELSE 0
                                             end), 2) as monto_otro_usd,

                                   /*Aumentando para incluid depositos en el reporte*/
                                 	round(sum(CASE when fp.fop_code = ''CA'' and v.id_deposito is not null then
                                        vfp.monto_transaccion - vfp.cambio
                                     ELSE
                                        0
                                     END),2) as monto_deposito_usd,
                                   /*************************************************/





                            pxp.list(fp.name) as forma_pago
                            from  vef.tventa_forma_pago vfp
                            inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = vfp.id_medio_pago
                            inner join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
                            inner join vef.tventa v on v.id_venta = vfp.id_venta
                            where vfp.id_moneda = ' || v_id_moneda_usd || ' and (v.fecha::date between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''')
                            group by vfp.id_venta
                        ),';
            ELSE
            	v_consulta = v_consulta || ' forma_pago_usd  AS(
							select vfp.id_factucom,
                             round(sum(case
                                         when vfp.forma like ''CA'' then
                                           vfp.importe_pago
                                         ELSE 0
                                       end), 2) as monto_cash_usd,
                             round(sum(case
                                         when vfp.forma like ''CC%'' then
                                           vfp.importe_pago
                                         ELSE 0
                                       end), 2) as monto_cc_usd,
                             round(sum(case
                                         when vfp.forma like ''CT%'' then
                                           vfp.importe_pago
                                         ELSE 0
                                       end), 2) as monto_cte_usd,
                             round(sum(case
                                         when vfp.forma like ''MCO%'' then
                                           vfp.importe_pago
                                         ELSE 0
                                       end), 2) as monto_mco_usd,
                             round(sum(case
                                         when vfp.forma not similar to
                                           ''(CA|CC|CT|MCO)%'' then
                                           vfp.importe_pago
                                         ELSE 0
                                       end), 2) as monto_otro_usd,
                             0::numeric,
                             pxp.list(vfp.nombre_forma) as forma_pago
                      from vef.tfactucompag_endesis vfp
                      inner join param.tmoneda mon on mon.codigo_internacional=vfp.moneda
                      inner join vef.tfactucom_endesis v on v.id_factucom=vfp.id_factucom
                      where mon.id_moneda = '||v_id_moneda_usd||' and
                            (v.fecha::date between '''||v_parametros.fecha_desde||''' and '''||v_parametros.fecha_hasta||''' )
                      group by vfp.id_factucom),';
            END IF;

        end if;
		IF(pxp.f_get_variable_global('vef_facturacion_endesis')='true')THEN
            v_consulta = v_consulta || ' forma_pago_mb AS(
                          select vfp.id_venta,
                          sum(CASE when fp.fop_code = ''CA'' and v.id_deposito is null then
                                        vfp.monto_transaccion - vfp.cambio
                                     ELSE
                                        0
                                     END) as monto_cash_mb,
                                 sum(CASE
                                       when fp.fop_code like ''CC%'' then
                                         vfp.monto_transaccion - vfp.cambio
                                       ELSE 0
                                     END) as monto_cc_mb,
                                 sum(CASE
                                       when (fp.fop_code like ''CT%'' OR fp.fop_code like ''CU%'') then
                                         vfp.monto_transaccion - vfp.cambio
                                       ELSE 0
                                     END) as monto_cte_mb,
                                 sum(CASE
                                       when fp.fop_code like ''MCO%'' then
                                         vfp.monto_transaccion - vfp.cambio
                                       ELSE 0
                                     END) as monto_mco_mb,
                                 sum(CASE
                                       when fp.fop_code not similar to ''(CA|CC|CT|MCO|CU)%'' then
                                         vfp.monto_transaccion - vfp.cambio
                                       ELSE 0
                                     END) as monto_otro_mb,

                                 /*Aqui aumentando para depositos*/
                                 sum(CASE when fp.fop_code = ''CA'' and v.id_deposito is not null then
                                        vfp.monto_transaccion - vfp.cambio
                                     ELSE
                                        0
                                     END) as monto_deposito_mb,
                                 /********************************/

                          pxp.list(fp.name) as forma_pago
                          from  vef.tventa_forma_pago vfp
                          inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = vfp.id_medio_pago
                          inner join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
                          inner join vef.tventa v on v.id_venta = vfp.id_venta
                          where vfp.id_moneda = ' || v_id_moneda || ' and (v.fecha::date between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''')
                          group by vfp.id_venta
                      )
                      select ''' || v_cod_moneda || '''::varchar as moneda_emision,
                      ''venta''::varchar as tipo,
                      v.fecha::date as fecha,
                      v.nro_factura::varchar as correlativo_venta,
                      v.tipo_factura::varchar,
                      cli.nombre_factura,
                      v.observaciones::varchar as boleto,
                      /*Aumentando el Localizador*/
                      ''''::varchar as localizador,
                      ''''::varchar as codigo_auxiliar,
                      /***************************/
                      ''''::varchar as ruta,
                      ''''::varchar as conceptos,
                      ';
        ELSE
        	v_consulta = v_consulta ||' forma_pago_mb AS(select vfp.id_factucom,
                                       round(sum(case
                                                   when vfp.forma like ''CA'' then
                                                     vfp.importe_pago
                                                   ELSE 0
                                                 end), 2) as monto_cash_mb,
                                       round(sum(case
                                                   when vfp.forma like ''CC%'' then
                                                     vfp.importe_pago
                                                   ELSE 0
                                                 end), 2) as monto_cc_mb,
                                       round(sum(case
                                                   when vfp.forma like ''CT%'' then
                                                     vfp.importe_pago
                                                   ELSE 0
                                                 end), 2) as monto_cte_mb,
                                       round(sum(case
                                                   when vfp.forma like ''MCO%'' then
                                                     vfp.importe_pago
                                                   ELSE 0
                                                 end), 2) as monto_mco_mb,
                                       round(sum(case
                                                   when vfp.forma not similar to
                                                     ''(CA|CC|CT|MCO)%'' then
                                                     vfp.importe_pago
                                                   ELSE 0
                                                 end), 2) as monto_otro_mb,
                                       0::numeric,
                                       pxp.list(vfp.nombre_forma) as forma_pago
                                from vef.tfactucompag_endesis vfp
                                inner join param.tmoneda mon on mon.codigo_internacional=vfp.moneda
                                inner join vef.tfactucom_endesis v on v.id_factucom=vfp.id_factucom
                                where mon.id_moneda = '||v_id_moneda||' and (v.fecha::date between '''||v_parametros.fecha_desde||''' and
                                      '''||v_parametros.fecha_hasta||''' )
                                group by vfp.id_factucom)
						        select ''BOB''::varchar as moneda_emision,
                                   ''venta''::varchar as tipo,
                                   v.fecha::date as fecha,
                                   v.nrofac::varchar as correlativo_venta,
                                   ''''::varchar as tipo_factura,
                                   v.razon_cliente,
                                   ''''::varchar as boleto,
                                   v.observacion::varchar as ruta,
                                   ''''::varchar as conceptos,';

        END IF;

        if (v_cod_moneda != 'USD') then
          v_consulta = v_consulta || ' (CASE when v.id_deposito is not null then
                                        ''DEPOSITO''
                                     ELSE
                                        coalesce(fpusd.forma_pago || '','','''') || coalesce(fpmb.forma_pago,'''')
                                     END) as forma_pago,

          						--coalesce(fpusd.forma_pago || '','','''')|| coalesce(fpmb.forma_pago,'''') as forma_pago,
                    			coalesce(fpusd.monto_cash_usd, 0) as monto_cash_usd,
                                coalesce(fpusd.monto_cc_usd, 0) as monto_cc_usd,
                                coalesce(fpusd.monto_cte_usd, 0) as monto_cte_usd,
                                coalesce(fpusd.monto_mco_usd, 0) as monto_mco_usd,
                         		coalesce(fpusd.monto_otro_usd, 0) as monto_otro_usd,';
         v_group_by = ' ,fpusd.forma_pago, fpusd.monto_cash_usd,fpusd.monto_cc_usd,fpusd.monto_cte_usd,fpusd.monto_mco_usd,fpusd.monto_otro_usd,fpusd.monto_deposito_usd,v.id_deposito,v.comision';
        else
          v_group_by = '';
          v_consulta = v_consulta || ' 		CASE when v.id_deposito is not null then
                                                ''DEPOSITO''
                                             ELSE
                                                fpmb.forma_pago
                                             END) as forma_pago
          									--fpmb.forma_pago as forma_pago,
                    						coalesce(fpmb.monto_cash_mb, 0) as monto_cash_usd,
                                            coalesce(fpmb.monto_cc_mb, 0) as monto_cc_mb,
                         					coalesce(fpmb.monto_cte_mb, 0) as monto_cte_mb,
                         					coalesce(fpmb.monto_mco_mb, 0) as monto_mco_mb,
                                            coalesce(fpmb.monto_otro_mb, 0) as monto_otro_usd,';
        end if;

        IF(pxp.f_get_variable_global('vef_facturacion_endesis')='true')THEN

            v_consulta = v_consulta || '
                      coalesce(fpmb.monto_cash_mb, 0) as monto_cash_mb,
                      coalesce(fpmb.monto_cc_mb, 0) as monto_cc_mb,
                      coalesce(fpmb.monto_cte_mb, 0) as monto_cte_mb,
                      coalesce(fpmb.monto_mco_mb, 0) as monto_mco_mb,
                      coalesce(fpmb.monto_otro_mb, 0) as monto_otro_mb,
                      v.total_venta::numeric,
                      string_agg((vd.precio*vd.cantidad)::text,''|'')::varchar as precios_detalles,
                      NULL::varchar as mensaje_error,
                      -- 0::numeric as comision
                      coalesce(v.comision, 0)::numeric,

                      /*Aumentnado para depositos*/
                      coalesce(fpmb.monto_deposito_mb,0)::numeric,
                      coalesce(fpusd.monto_deposito_usd,0)::numeric,
                      '''||COALESCE(v_nombre_pv,'')||'''::varchar as punto_venta
                      /***************************/

                      from vef.tventa v
                      left join vef.tventa_detalle vd
                          on v.id_venta = vd.id_venta and vd.estado_reg = ''activo''
                      --inner join vef.tsucursal_producto sp
                      --    on sp.id_sucursal_producto = vd.id_sucursal_producto
                      left join param.tconcepto_ingas cig
                          on cig.id_concepto_ingas = vd.id_producto --sp.id_concepto_ingas
                      inner join vef.tcliente cli
                          on cli.id_cliente = v.id_cliente';
        ELSE
        	v_consulta = v_consulta || ' coalesce(fpmb.monto_cash_mb, 0) as monto_cash_mb,
                         coalesce(fpmb.monto_cc_mb, 0) as monto_cc_mb,
                         coalesce(fpmb.monto_cte_mb, 0) as monto_cte_mb,
                         coalesce(fpmb.monto_mco_mb, 0) as monto_mco_mb,
                         coalesce(fpmb.monto_otro_mb, 0) as monto_otro_mb,
                         v.monto::numeric,
                         ''''::varchar as precios_detalles,
                         NULL::varchar as mensaje_error,
                          0::numeric as ccomision,
                          0::numeric depo_mb,
                          0::numeric depo_ml,
                          '''||COALESCE(v_nombre_pv,'')||'''::varchar as punto_venta
                  from vef.tfactucom_endesis v
                  inner join vef.tpunto_venta pv on pv.codigo=v.agt::varchar
                       inner join vef.tfactucompag_endesis vd on vd.id_factucom=v.id_factucom ';
        END IF;

        if (v_cod_moneda != 'USD') then
        	IF(pxp.f_get_variable_global('vef_facturacion_endesis')='true')THEN
                v_consulta = v_consulta || ' left join forma_pago_usd fpusd
                            on v.id_venta = fpusd.id_venta ';
			ELSE
            	v_consulta = v_consulta || ' left join forma_pago_usd fpusd on v.id_factucom = fpusd.id_factucom ';
            END IF;

        end if;

		IF(pxp.f_get_variable_global('vef_facturacion_endesis')='true')THEN
            v_consulta = v_consulta || ' left join forma_pago_mb fpmb
                          on v.id_venta = fpmb.id_venta
                      where v.estado = ''finalizado'' and
                        (v.fecha::date between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''')
                        '|| v_filtro_cajero_boleto_1 ||'
                      group by v.fecha,v.nro_factura,v.tipo_factura,cli.nombre_factura,v.observaciones,
                                fpmb.forma_pago, fpmb.monto_cash_mb,fpmb.monto_cc_mb,fpmb.monto_cte_mb,fpmb.monto_mco_mb,fpmb.monto_otro_mb,v.total_venta_msuc,fpmb.monto_deposito_mb,v.total_venta ' || v_group_by || '
                      )
            union ALL --3
                (WITH ';
        ELSE
        	v_consulta = v_consulta || ' left join forma_pago_mb fpmb on v.id_factucom = fpmb.id_factucom
                  where v.estado_reg = ''emitida'' ' || v_filtro_cajero_factura || ' and
                  		pv.id_punto_venta='||v_parametros.id_punto_venta||' and
                        (v.fecha::date between '''||v_parametros.fecha_desde||''' and '''||v_parametros.fecha_hasta||''')
                  group by v.fecha,
                           v.nrofac,
                           v.monto,
                           v.razon_cliente,
                           v.observacion,
                           fpmb.forma_pago,
                           fpmb.monto_cash_mb,
                           fpmb.monto_cc_mb,
                           fpmb.monto_cte_mb,
                           fpmb.monto_mco_mb,
                           fpmb.monto_otro_mb'|| v_group_by ||')
                  UNION ALL --4
                  (WITH ';
        END IF;

        if (v_cod_moneda != 'USD') then
          v_consulta = v_consulta || ' bol_forma_pago_usd  AS(
        			select bfp.id_boleto_amadeus,
                    sum(case when fp.fop_code = ''CA'' then
                           			bfp.importe
                           		ELSE
                                	0
                                END) as monto_cash_usd,
                           sum(case
                                 when fp.fop_code like ''CC%'' then bfp.importe
                                 ELSE 0
                               END) as monto_cc_usd,
                           sum(case
                                 when (fp.fop_code like ''CT%'' OR fp.fop_code like ''CU%'') then bfp.importe
                                 ELSE 0
                               END) as monto_cte_usd,
                           sum(case
                                 when fp.fop_code like ''MCO%'' then bfp.importe
                                 ELSE 0
                               END) as monto_mco_usd,
                           sum(case
                                 when fp.fop_code not similar to ''(CA|CC|CT|MCO|CU)%'' then bfp.importe
                                 ELSE 0
                               END) as monto_otro_usd,
                    pxp.list(fp.name) as forma_pago
                    ,bfp.id_moneda
                    from  obingresos.tboleto_amadeus_forma_pago bfp
                    inner join obingresos.tboleto_amadeus b on b.id_boleto_amadeus = bfp.id_boleto_amadeus

                    inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bfp.id_medio_pago
                    inner join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id

                    --inner join obingresos.tforma_pago fp on bfp.id_forma_pago = fp.id_forma_pago

                    where ' || v_filtro || ' and
             			(b.fecha_emision between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''') and bfp.id_moneda = ' || v_id_moneda_usd || '
                    group by bfp.id_boleto_amadeus, bfp.id_moneda
        			),';
        end if;

        v_consulta = v_consulta || 'bol_forma_pago_mb AS(
                select bfp.id_boleto_amadeus,
                sum(case when fp.fop_code = ''CA'' then
                           			bfp.importe
                           		ELSE
                                	0
                                END) as monto_cash_mb,
                           sum(case
                             when fp.fop_code like ''CC%'' then bfp.importe
                             ELSE 0
                           END) as monto_cc_mb,
                       sum(case
                             when (fp.fop_code like ''CT%'' OR fp.fop_code like ''CU%'') then bfp.importe
                             ELSE 0
                           END) as monto_cte_mb,
                       sum(case
                             when fp.fop_code like ''MCO%'' then bfp.importe
                             ELSE 0
                           END) as monto_mco_mb,
                       sum(case
                             when fp.fop_code not similar to ''(CA|CC|CT|MCO|CU)%'' then bfp.importe
                             ELSE 0
                           END) as monto_otro_mb,
                pxp.list(fp.name) as forma_pago
                 from  obingresos.tboleto_amadeus_forma_pago bfp
                 inner join obingresos.tboleto_amadeus b on b.id_boleto_amadeus = bfp.id_boleto_amadeus

                 inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = bfp.id_medio_pago
                 inner join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id


                where ' || v_filtro || ' and
             			(b.fecha_emision between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''') and bfp.id_moneda = ' || v_id_moneda || '
                group by bfp.id_boleto_amadeus
            ), bol_impuesto AS(
            	select bimp.id_boleto,string_agg(bimp.importe::text,''|'')::varchar as monto_impuesto,string_agg(imp.codigo,''|'')::varchar as impuesto
                 from  obingresos.tboleto_impuesto bimp
                 inner join obingresos.tboleto_amadeus b on b.id_boleto_amadeus = bimp.id_boleto
                 inner join obingresos.timpuesto imp on imp.id_impuesto = bimp.id_impuesto
                where ' || v_filtro || ' and
             			(b.fecha_emision between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''')
                group by bimp.id_boleto
            )
             SELECT b.moneda::varchar as moneda_emision,
             ''boleto''::varchar as tipo ,b.fecha_emision,
             ''''::varchar as correlativo_venta,
             ''''::varchar as tipo_factura,
             b.pasajero::varchar as nombre_factura,
             b.nro_boleto::varchar as boleto,
             /*Aumentando para el localizador*/
             b.localizador::varchar as localizador,
             list((aux.codigo_auxiliar || ''-'' || aux.nombre_auxiliar))::varchar as codigo_auxiliar,
             /********************************/
             b.ruta_completa as ruta ,
             ''''::varchar as conceptos,
             ';
        if (v_cod_moneda != 'USD') then
          v_consulta = v_consulta || '
                     /*************************Aumentando*********************/
                     CASE WHEN b.forma_pago = ''CC'' and fpusd.id_boleto_amadeus is null AND fpmb.id_boleto_amadeus is null then

                        (select fp_pw.name
                        from obingresos.tforma_pago_pw fp_pw
                        where fp_pw.fop_code = b.forma_pago)

                     else
                        coalesce(fpusd.forma_pago || '','','''')|| coalesce(fpmb.forma_pago,'''')
                     end  as forma_pago,
                     /********************************************************/
                    		case
                      when b.voided != ''si'' then coalesce(fpusd.monto_cash_usd,
                        0)
                      else 0
                    end as monto_cash_usd,

                     /*Aumentando*/
                     CASE WHEN (b.forma_pago = ''CC'' and b.id_moneda_boleto = 2 and b.voided != ''si'' and fpusd.id_boleto_amadeus is null and fpmb.id_boleto_amadeus is null) then
                        (b.total - COALESCE(b.comision,0))
                     else
                        case
                      when b.voided != ''si'' and fpusd.id_moneda = 2 then coalesce(fpusd.monto_cc_usd, 0)
                      else 0
                    end
                     end  as monto_cc_usd,
                    /******************************************************************/
                    case
                      when b.voided != ''si'' then coalesce(fpusd.monto_cte_usd, 0
                        )
                      else 0
                    end as monto_cte_usd,
                    case
                      when b.voided != ''si'' then coalesce(fpusd.monto_mco_usd, 0
                        )
                      else 0
                    end as monto_mco_usd,
                    case
                      when b.voided != ''si'' then coalesce(fpusd.monto_otro_usd,
                        0)
                      else 0
                    end as monto_otro_usd,';
          v_group_by = ' ,fpusd.forma_pago, fpusd.monto_cash_usd,fpusd.monto_cc_usd,
                      fpusd.monto_cte_usd, fpusd.monto_mco_usd,fpusd.monto_otro_usd, fpusd.id_moneda ';
        else
          v_consulta = v_consulta || ' fpmb.forma_pago as forma_pago,
                    					case when b.voided != ''si'' then coalesce(fpmb.monto_cash_mb,0) else 0 as monto_cash_usd,
                                        case when b.voided != ''si'' then coalesce(fpmb.monto_cc_mb,0) else 0 as monto_cc_usd,
                                        case when b.voided != ''si'' then coalesce(fpmb.monto_cte_mb,0) else 0 as monto_cte_usd,
                                        case when b.voided != ''si'' then coalesce(fpmb.monto_mco_mb,0) else 0 as monto_mco_usd,
                                        case when b.voided != ''si'' then coalesce(fpmb.monto_otro_mb,0) else 0 as monto_otro_usd,';
          v_group_by = '';
        end if;
        v_consulta = v_consulta ||  '
             case when b.voided != ''si'' then coalesce(fpmb.monto_cash_mb,0) else 0 end as monto_cash_mb,

             /*Aumentando*/
             CASE WHEN (b.forma_pago = ''CC'' and b.id_moneda_boleto != 2 and b.voided != ''si'' and fpmb.id_boleto_amadeus is null and fpusd.id_boleto_amadeus is null) then

             	(b.total - COALESCE(b.comision,0))

             else
             	case when b.voided != ''si'' then coalesce(fpmb.monto_cc_mb,0) else 0 end
             end  as monto_cc_mb,


             case when b.voided != ''si'' then coalesce(fpmb.monto_cte_mb,0) else 0 end as monto_cte_mb,
             case when b.voided != ''si'' then coalesce(fpmb.monto_mco_mb,0) else 0 end as monto_mco_mb,
             case when b.voided != ''si'' then coalesce(fpmb.monto_otro_mb,0) else 0 end as monto_otro_mb,


             case when b.voided != ''si'' then coalesce(b.total,0) else 0 end as total,


             --b.total,
             imp.monto_impuesto as precios_conceptos,
             b.mensaje_error,
             b.comision,
             0::numeric,
             0::numeric,
             '''||COALESCE(v_nombre_pv,'')||'''::varchar as punto_venta
             from obingresos.tboleto_amadeus b
             ';
        if (v_cod_moneda != 'USD') then
          v_consulta = v_consulta || ' left join bol_forma_pago_usd fpusd
                      on b.id_boleto_amadeus = fpusd.id_boleto_amadeus ';
        end if;

        v_consulta = v_consulta || '
             left join bol_forma_pago_mb fpmb
                on fpmb.id_boleto_amadeus = b.id_boleto_amadeus
             left join bol_impuesto imp
                on imp.id_boleto = b.id_boleto_amadeus

        	 left join obingresos.tboleto_amadeus_forma_pago formpa on formpa.id_boleto_amadeus = b.id_boleto_amadeus
             left join conta.tauxiliar aux on aux.id_auxiliar = formpa.id_auxiliar

             where b.estado_reg = ''activo'' and b.estado=''revisado'' and ' || v_filtro || ' and
             (b.fecha_emision between ''' || v_parametros.fecha_desde || ''' and ''' ||v_parametros.fecha_hasta || ''')
             '||v_filtro_cajero_boleto||'
             group by b.fecha_emision,b.pasajero, b.voided, b.nro_boleto,b.mensaje_error,b.ruta_completa,b.moneda,b.total,imp.impuesto,
             		imp.monto_impuesto,fpmb.forma_pago,fpmb.monto_cash_mb,fpmb.monto_cc_mb,fpusd.id_boleto_amadeus,
                      fpmb.monto_cte_mb,fpmb.monto_mco_mb,fpmb.monto_otro_mb,b.comision, b.localizador,/*aux.codigo_auxiliar,aux.nombre_auxiliar,*/b.forma_pago, b.id_moneda_boleto, fpmb.id_boleto_amadeus '|| v_group_by || ')
             order by fecha,tipo_factura DESC,correlativo_venta, boleto';
        end if;



        raise notice '%',v_consulta;
        --Devuelve la respuesta
        return v_consulta;

      end;
    /*********************************
 	#TRANSACCION:  'VF_REPRESBOA_SEL'
 	#DESCRIPCION:	Reporte de Boa para resumen de ventas
 	#AUTOR:		admin
 	#FECHA:		01-06-2015 05:58:00
	***********************************/

    elsif(p_transaccion='VF_REPRESBOA_SEL')then

      begin
        IF  pxp.f_existe_parametro(p_tabla,'id_punto_venta') THEN
          v_filtro = ' id_punto_venta = ' || v_parametros.id_punto_venta;
        else
          v_filtro = ' id_sucursal = ' || v_parametros.id_sucursal;
        end if;



        v_consulta:='
            ( WITH forma_pago_cc  AS(
                      select vfp.id_venta,vfp.monto_mb_efectivo as monto_tarjeta
                      from  vef.tventa_forma_pago vfp
                      inner join vef.tforma_pago fp on vfp.id_forma_pago = fp.id_forma_pago
                      where fp.codigo = ''CCSUS''
                  ),
                  forma_pago_cash AS(
                      select vfp.id_venta,vfp.monto_mb_efectivo as monto_efectivo
                      from  vef.tventa_forma_pago vfp
                      inner join vef.tforma_pago fp on vfp.id_forma_pago = fp.id_forma_pago
                      where fp.codigo = ''EFESUS''
                  )
                  select v.fecha_reg::date as fecha,cig.desc_ingas,
                  sum(round(((coalesce(fpcc.monto_tarjeta,0)/v.total_venta)*vd.cantidad*vd.precio),2)) as precio_cc,
                  sum(round(((coalesce(fpcash.monto_efectivo,0)/v.total_venta)*vd.cantidad*vd.precio),2)) as precio_cash,
                  sum(vd.cantidad*vd.precio) as monto
                  from vef.tventa v
                  inner join vef.tventa_detalle vd
                      on v.id_venta = vd.id_venta and vd.estado_reg = ''activo''
                  inner join vef.tsucursal_producto sp
                      on sp.id_sucursal_producto = vd.id_sucursal_producto
                  inner join param.tconcepto_ingas cig
                      on cig.id_concepto_ingas = sp.id_concepto_ingas
                  left join forma_pago_cc fpcc
                      on v.id_venta = fpcc.id_venta
                  left join forma_pago_cash fpcash
                      on v.id_venta = fpcash.id_venta
                  where v.estado = ''finalizado'' and ' || v_filtro || ' and
                  	(v.fecha_reg::date between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''')
                  group by v.fecha_reg,cig.desc_ingas)
		union ALL
	 		(WITH bol_forma_pago_cc  AS(
        			select vfp.id_boleto,vfp.monto as monto_tarjeta
                    from  vef.tboleto_fp vfp
                    inner join vef.tforma_pago fp on vfp.id_forma_pago = fp.id_forma_pago
                    where fp.codigo = ''CCSUS''
        	),
            bol_forma_pago_cash AS(
                select vfp.id_boleto,vfp.monto as monto_efectivo
                from  vef.tboleto_fp vfp
                inner join vef.tforma_pago fp on vfp.id_forma_pago = fp.id_forma_pago
                where fp.codigo = ''EFESUS''
            )
             SELECT b.fecha,
             ''TARIFA NETA''::varchar as concepto,
             sum(coalesce(fpcc.monto_tarjeta,0)) as precio_tarjeta,
             sum(coalesce(fpcash.monto_efectivo,0)) as precio_cash,
             sum(coalesce(fpcc.monto_tarjeta,0) + coalesce(fpcash.monto_efectivo,0)) as monto

             from vef.tboleto_amadeus b
             left join bol_forma_pago_cc fpcc
                on fpcc.id_boleto = b.id_boleto
             left join bol_forma_pago_cash fpcash
                on fpcash.id_boleto = b.id_boleto
             where ' || v_filtro || ' and
             (b.fecha between ''' || v_parametros.fecha_desde || ''' and ''' || v_parametros.fecha_hasta || ''')
             group by b.fecha)
             order by fecha';

        --Devuelve la respuesta
        return v_consulta;

      end;
    /*********************************
     #TRANSACCION:  'VF_VENCONF_SEL'
     #DESCRIPCION:	Obtener configuraciones basicas para sistema de ventas
     #AUTOR:		admin
     #FECHA:		01-06-2015 05:58:00
    ***********************************/

    elsif(p_transaccion='VF_VENCONF_SEL')then

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
						 ';

        --Definicion de la respuesta
        return v_consulta;


      end;

    /*********************************
 	#TRANSACCION:  'VF_REPXPROD_SEL'
 	#DESCRIPCION:	Detalle de ventas para una lista de productos
 	#AUTOR:		admin
 	#FECHA:		01-06-2015 05:58:00
	***********************************/

    elsif(p_transaccion='VF_REPXPROD_SEL')then

      begin
        v_filtro = ' v.estado in (''finalizado'',''anulado'') and v.id_sucursal = ' || v_parametros.id_sucursal || ' and v.fecha >='''
                   || v_parametros.fecha_desde || ''' and v.fecha <= ''' || v_parametros.fecha_hasta ||
                   ''' and vd.id_sucursal_producto in(' || v_parametros.id_productos || ')' ;

        --Sentencia de la consulta de conteo de registros
        v_consulta:='	select

                            (tdcv.codigo||'' - ''||tdcv.nombre)::varchar as desc_tipo_doc_compra_venta,
                            pla.desc_plantilla::varchar,
                            to_char(dcv.fecha,''DD/MM/YYYY'')::varchar as fecha,
                            dcv.nro_autorizacion::varchar,
                            dcv.nit::varchar,
                            dcv.razon_social::varchar,
                            pxp.list(cig.desc_ingas)::varchar,
                            dcv.nro_documento,
                            COALESCE(dcv.importe_doc,0)::numeric as importe_doc,
                            COALESCE(dcv.importe_neto,0)::numeric as importe_neto,

                            COALESCE(dcv.importe_iva,0)::numeric as importe_iva,
                            COALESCE(dcv.importe_it,0)::numeric as importe_it,
                            COALESCE(dcv.importe_neto,0)::numeric - COALESCE(dcv.importe_iva,0) as ingreso


						from vef.tventa v
                        inner join vef.tventa_detalle vd on vd.id_venta = v.id_venta
                        inner join vef.tsucursal_producto sp on sp.id_sucursal_producto = vd.id_sucursal_producto
                        inner join param.tconcepto_ingas cig on cig.id_concepto_ingas = sp.id_concepto_ingas
                        inner join conta.tdoc_compra_venta dcv on dcv.id_origen = v.id_venta and dcv.tabla_origen = ''vef.tventa''

                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          where ' || v_filtro || '
                          group by dcv.estado,
                            pla.desc_plantilla,
                            dcv.fecha,
                            dcv.nro_autorizacion,
                            dcv.nit,
                            dcv.razon_social,
                            dcv.nro_documento,
                            dcv.importe_doc,
                            dcv.importe_neto,

                            dcv.importe_iva,
                            dcv.importe_it,
                            tdcv.codigo,
                            tdcv.nombre
                          order by dcv.fecha, dcv.nro_documento::integer
						 ';

        --Definicion de la respuesta

        raise notice '%',v_consulta;
        --Devuelve la respuesta
        return v_consulta;

      end;



    /*********************************
     #TRANSACCION:  'VF_NOTAVEND_SEL'
     #DESCRIPCION:	lista el detalle de la nota de venta
     #AUTOR:		admin
     #FECHA:		01-06-2015 05:58:00
    ***********************************/

    ELSIF(p_transaccion='VF_NOTAVEND_SEL')then

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
 	#TRANSACCION:  'VF_NOTAVEND_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		01-06-2015 05:58:00
	***********************************/

    elsif(p_transaccion='VF_NOTAVEND_CONT')then

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
 	#TRANSACCION:  'VF_NOTVEN_SEL'
 	#DESCRIPCION:   Lista de la cabecera de la nota de venta
 	#AUTOR:		admin
 	#FECHA:		01-06-2015 05:58:00
	***********************************/

    elsif(p_transaccion='VF_NOTVEN_SEL')then

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
 	#TRANSACCION:  'VF_REPFACTDET_SEL'
 	#DESCRIPCION:	Reporte de Facturacion Computarizada
 	#AUTOR:		admin
 	#FECHA:		01-12-2020 14:47:00
	***********************************/

    elsif(p_transaccion='VF_REPFACTDET_SEL')then

      begin
       	--raise exception 'Aqui llega datos %',v_parametros.desde;
        if (v_parametros.desde != '' || v_parametros.desde is not null) then
        	v_filtro_fecha_desde = 'vent.fecha >= '''||v_parametros.desde||''' ';
        else
        	v_filtro_fecha_desde = '0=0';
        end if;

        if (v_parametros.hasta != '' || v_parametros.hasta is not null) then
        	v_filtro_fecha_hasta = 'vent.fecha <= '''||v_parametros.hasta||''' ';
        else
        	v_filtro_fecha_hasta = '0=0';
        end if;

        if (v_parametros.id_punto_venta is not null and v_parametros.id_punto_venta != 0) then
        	v_filtro_id_punto_venta = 'vent.id_punto_venta = '||v_parametros.id_punto_venta||'';
        else
        	v_filtro_id_punto_venta = '0=0';
        end if;

         if (v_parametros.id_concepto is not null) then
        	v_filtro_id_concepto = 'det.id_producto = '||v_parametros.id_concepto||'';
        else
        	v_filtro_id_concepto = '0=0';
        end if;

        if (v_parametros.id_usuario_cajero is not null) then
          if (v_parametros.id_usuario_cajero != 0) then
              v_filtro_id_cajero = 'vent.id_usuario_cajero = '||v_parametros.id_usuario_cajero||'';
          else
              v_filtro_id_cajero = '0=0';
          end if;
        else
        	v_filtro_id_cajero = '0=0';
        end if;


        if (v_parametros.tipo_documento is not null) then
        	if (v_parametros.tipo_documento = 'factura') then
            	v_filtro_tipo_factura = '(vent.tipo_factura = ''computarizada'' or vent.tipo_factura = ''manual'' OR vent.tipo_factura = ''carga'')';
            elsif (v_parametros.tipo_documento = 'recibo') then
            	v_filtro_tipo_factura = '(vent.tipo_factura = ''recibo'' or vent.tipo_factura = ''recibo_manual'')';
            end if;
        else
        	v_filtro_tipo_factura = '0=0';
        end if;

        if (v_parametros.nit = '' or v_parametros.nit is null) then

        	v_filtro_nit = '0=0';

        else

       		v_filtro_nit = 'vent.nit = '''||v_parametros.nit||'''';

	    end if;


        /*Aqui creamos la tabla temporal para insertar y separar por punto de venta*/
         create temp table reporte_facturacion_computarizada (
                                                                id_venta integer,
                                                                total_venta varchar,
                                                                fecha varchar,
                                                                conceptos varchar,
                                                                nombre varchar,
                                                                codigo varchar,
                                                                observaciones varchar,
                                                                nro_factura integer,
                                                                cantidad varchar,
                                                                precio	varchar,
                                                                exento varchar,
                                                                comision varchar,
                                                                total_precio varchar,
                                                                moneda varchar,
                                                                num_tarjeta varchar,
                                                                total_monto varchar,
                                                                forma_pago varchar,
                                                                medio_pago varchar,
                                                                lugar varchar,
                                                                pais varchar,
                                                                estado varchar,
                                                                tipo_factura varchar,
                                                                cajero varchar,
                                                                cod_control varchar
                                                              )on commit drop;

                CREATE INDEX treporte_facturacion_computarizada_id_venta ON reporte_facturacion_computarizada
                USING btree (id_venta);

                CREATE INDEX treporte_facturacion_computarizada_fecha ON reporte_facturacion_computarizada
                USING btree (fecha);

                CREATE INDEX treporte_facturacion_computarizada_nro_factura ON reporte_facturacion_computarizada
                USING btree (nro_factura);

        /*************************************************************************************************************/


        v_consulta_insertar_reporte = 'insert into reporte_facturacion_computarizada (
                                                    id_venta,
                                                    total_venta,
                                                    fecha,
                                                    conceptos,
                                                    nombre,
                                                    codigo,
                                                    observaciones,
                                                    nro_factura,
                                                    cantidad,
                                                    precio,
                                                    exento,
                                                    comision,
                                                    total_precio,
                                                    moneda,
                                                    num_tarjeta,
                                                    total_monto,
                                                    forma_pago,
                                                    medio_pago,
                                                    lugar,
                                                    pais,
                                                    estado,
                                                    tipo_factura,
                                                    cajero,
                                                    cod_control
        								)
                                        (WITH  cabecera AS (select vent.id_venta,
                                                 vent.total_venta,
                                                 vent.fecha,
                                                 list (ingas.desc_ingas) as conceptos ,
                                                 pv.nombre ,
                                                 pv.codigo,
                                                 vent.observaciones,
                                                 vent.nro_factura,
                                                 list (det.cantidad::Varchar) cantidad,
                                                 list (det.precio::varchar) as precio,
                                                 vent.excento::varchar as exento,
                                                 vent.comision::varchar as comision,
                                                 list ((det.cantidad*det.precio)::Varchar) total_precio,
                                                 lug.nombre as lugar,
                                                 lug.id_lugar_fk,
                                                 vent.estado,
                                                 vent.tipo_factura,
                                                 usu.desc_persona,
                                                 vent.cod_control
                                          from vef.tventa vent
                                          left join vef.tventa_detalle det on det.id_venta = vent.id_venta
                                          left join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = det.id_producto
                                          inner join vef.tpunto_venta pv on pv.id_punto_venta = vent.id_punto_venta
                                          inner join vef.tsucursal suc on suc.id_sucursal = vent.id_sucursal
                                          inner join param.tlugar lug on lug.id_lugar = suc.id_lugar
                                          inner join segu.vusuario usu on usu.id_usuario = vent.id_usuario_cajero
                                          where vent.estado_reg = ''activo'' and (vent.estado = ''finalizado'' OR vent.estado = ''anulado'') and '||v_filtro_tipo_factura||' and '||v_filtro_id_cajero||' and '||v_filtro_fecha_desde||' and '||v_filtro_fecha_hasta||' and '||v_filtro_id_punto_venta||' and '||v_filtro_id_concepto||'
                                          and '||v_filtro_nit||'
                                          group by vent.id_venta, pv.nombre, pv.codigo, lug.nombre,lug.id_lugar_fk, usu.desc_persona),

                                        detalle as (
                                        select vent.id_venta,
                                               list (mon.codigo_internacional) AS moneda,
                                               list (fp.numero_tarjeta) as num_tarjeta,
                                               list (fp.monto_mb_efectivo::varchar) as total_monto,
                                               CASE
                                                      WHEN vent.id_deposito is not null
                                                      THEN
                                                      ''DEPÓSITO''
                                                      else
                                                      list (fpw.fop_code)
                                                END  as forma_pago,

                                                 CASE
                                                      WHEN vent.id_deposito is not null

                                                      THEN
                                                      ''DEPO''
                                                      else
                                                      list (mp.mop_code)
                                                END  as medio_pago
                                        from vef.tventa vent
                                        inner join vef.tventa_forma_pago fp on fp.id_venta = vent.id_venta
                                        inner join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                                        inner join obingresos.tforma_pago_pw fpw on fpw.id_forma_pago_pw = mp.forma_pago_id
                                        where vent.estado_reg = ''activo'' and (vent.estado = ''finalizado'' OR vent.estado = ''anulado'') and '||v_filtro_tipo_factura||' and '||v_filtro_id_cajero||' and '||v_filtro_fecha_desde||' and '||v_filtro_fecha_hasta||' and '||v_filtro_id_punto_venta||'
										and '||v_filtro_nit||'
                                        group by vent.id_venta)

                                        select ca.id_venta::integer,
                                               ca.total_venta::varchar,
                                               to_char(ca.fecha,''DD/MM/YYYY'')::varchar as fecha,
                                               ca.conceptos::varchar,
                                               ca.nombre::varchar,
                                               ca.codigo::varchar,
                                               ca.observaciones::varchar,
                                               ca.nro_factura::integer,
                                               ca.cantidad::varchar,
                                               ca.precio::varchar,
                                               ca.exento,
                                               ca.comision,
                                               ca.total_precio::varchar,
                                               det.moneda::varchar,
                                               det.num_tarjeta::varchar,
                                               det.total_monto::varchar,
                                               det.forma_pago::varchar,
                                               det.medio_pago::varchar,
                                               ca.lugar,
                                               lug.nombre::varchar as pais,
                                               (CASE
                                                  WHEN ca.estado = ''finalizado'' THEN
                                                  ''EMITIDA''
                                                  WHEN ca.estado = ''anulado''  THEN
                                                  ''ANULADA''
                                               END)::varchar as estado,
                                               (CASE
                                                  WHEN ca.tipo_factura = ''computarizada'' THEN
                                                  ''Facturación Computarizada''
                                                  WHEN ca.tipo_factura = ''manual''  THEN
                                                  ''Facturación Manual''
                                                  WHEN ca.tipo_factura = ''recibo''  THEN
                                                  ''RO Computarizado''
                                                  WHEN ca.tipo_factura = ''recibo_manual''  THEN
                                                  ''RO Manual''
                                                  WHEN ca.tipo_factura = ''carga''  THEN
                                                  ''Facturación Carga Computarizada''
                                               END)::varchar as tipo_factura,
                                               ca.desc_persona,
                                               ca.cod_control
                                        from cabecera ca
                                        inner join detalle det on det.id_venta = ca.id_venta
                                        inner join param.tlugar lug on lug.id_lugar = ca.id_lugar_fk
                                        order by nombre, nro_factura ASC)';
        execute v_consulta_insertar_reporte;

        v_consulta:='((select 	id_venta,
                                total_venta,
                                fecha,
                                conceptos,
                                nombre,
                                codigo,
                                observaciones,
                                nro_factura,
                                cantidad,
                                precio,
                                exento,
                                comision,
                                total_precio,
                                moneda,
                                num_tarjeta,
                                total_monto,
                                forma_pago,
                                medio_pago,
                                lugar,
                                pais,
                                estado,
                                tipo_factura,
                                cajero,
                                cod_control
        			from reporte_facturacion_computarizada
                    where '||v_parametros.filtro||')

                    UNION ALL

                    (select 	NULL::integer as id_venta,
                                NULL::varchar as total_venta,
                                NULL::varchar as fecha,
                                NULL::varchar as conceptos,
                                nombre,
                                codigo,
                                NULL::varchar as observaciones,
                                NULL::integer as nro_factura,
                                NULL::varchar as cantidad,
                                NULL::varchar as precio,
                                NULL::varchar as exento,
                                NULL::varchar as comision,
                                NULL::varchar as total_precio,
                                NULL::varchar as moneda,
                                NULL::varchar as num_tarjeta,
                                NULL::varchar as total_monto,
                                NULL::varchar as forma_pago,
                                NULL::varchar as medio_pago,
                                NULL::varchar as lugar,
                                NULL::varchar as pais,
                                NULL::varchar as estado,
                                ''cabecera''::varchar as tipo_factura,
                                NULL::varchar as cajero,
                                NULL::varchar as cod_control
                        from reporte_facturacion_computarizada
                        where '||v_parametros.filtro||'
                        group by nombre, codigo))
                    	order by nombre ASC, id_venta ASC NULLS FIRST';

        if (v_parametros.imprimir_reporte != 'si') then

        	v_consulta:=v_consulta||' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

        end if;

        return v_consulta;

      end;


      /*********************************
 	#TRANSACCION:  'VF_REPFACTDET_CONT'
 	#DESCRIPCION:	Reporte de Facturacion Computarizada
 	#AUTOR:		admin
 	#FECHA:		01-12-2020 14:47:00
	***********************************/

    elsif(p_transaccion='VF_REPFACTDET_CONT')then

      begin
       	--raise exception 'Aqui llega datos %',v_parametros.desde;
        if (v_parametros.desde != '' || v_parametros.desde is not null) then
        	v_filtro_fecha_desde = 'vent.fecha >= '''||v_parametros.desde||''' ';
        else
        	v_filtro_fecha_desde = '0=0';
        end if;

        if (v_parametros.hasta != '' || v_parametros.hasta is not null) then
        	v_filtro_fecha_hasta = 'vent.fecha <= '''||v_parametros.hasta||''' ';
        else
        	v_filtro_fecha_hasta = '0=0';
        end if;

        if (v_parametros.id_punto_venta is not null and v_parametros.id_punto_venta != 0) then
        	v_filtro_id_punto_venta = 'vent.id_punto_venta = '||v_parametros.id_punto_venta||'';
        else
        	v_filtro_id_punto_venta = '0=0';
        end if;

         if (v_parametros.id_concepto is not null) then
        	v_filtro_id_concepto = 'det.id_producto = '||v_parametros.id_concepto||'';
        else
        	v_filtro_id_concepto = '0=0';
        end if;

        if (v_parametros.id_usuario_cajero is not null) then
          if (v_parametros.id_usuario_cajero != 0) then
              v_filtro_id_cajero = 'vent.id_usuario_cajero = '||v_parametros.id_usuario_cajero||'';
          else
              v_filtro_id_cajero = '0=0';
          end if;
        else
        	v_filtro_id_cajero = '0=0';
        end if;


        if (v_parametros.tipo_documento is not null) then
        	if (v_parametros.tipo_documento = 'factura') then
            	v_filtro_tipo_factura = '(vent.tipo_factura = ''computarizada'' or vent.tipo_factura = ''manual'' OR vent.tipo_factura = ''carga'')';
            elsif (v_parametros.tipo_documento = 'recibo') then
            	v_filtro_tipo_factura = '(vent.tipo_factura = ''recibo'' or vent.tipo_factura = ''recibo_manual'')';
            end if;
        else
        	v_filtro_tipo_factura = '0=0';
        end if;

        if (v_parametros.nit = '' or v_parametros.nit is null) then

        	v_filtro_nit = '0=0';

        else

       		v_filtro_nit = 'vent.nit = '''||v_parametros.nit||'''';

	    end if;



        /*Aqui creamos la tabla temporal para insertar y separar por punto de venta*/
         create temp table reporte_facturacion_computarizada (
                                                                id_venta integer,
                                                                total_venta varchar,
                                                                fecha varchar,
                                                                conceptos varchar,
                                                                nombre varchar,
                                                                codigo varchar,
                                                                observaciones varchar,
                                                                nro_factura integer,
                                                                cantidad varchar,
                                                                precio	varchar,
                                                                exento varchar,
                                                                comision varchar,
                                                                total_precio varchar,
                                                                moneda varchar,
                                                                num_tarjeta varchar,
                                                                total_monto varchar,
                                                                forma_pago varchar,
                                                                medio_pago varchar,
                                                                lugar varchar,
                                                                pais varchar,
                                                                estado varchar,
                                                                tipo_factura varchar,
                                                                cajero varchar,
                                                                cod_control varchar
                                                              )on commit drop;

                CREATE INDEX treporte_facturacion_computarizada_id_venta ON reporte_facturacion_computarizada
                USING btree (id_venta);

                CREATE INDEX treporte_facturacion_computarizada_fecha ON reporte_facturacion_computarizada
                USING btree (fecha);

                CREATE INDEX treporte_facturacion_computarizada_nro_factura ON reporte_facturacion_computarizada
                USING btree (nro_factura);

        /*************************************************************************************************************/


        v_consulta_insertar_reporte = 'insert into reporte_facturacion_computarizada (
                                                    id_venta,
                                                    total_venta,
                                                    fecha,
                                                    conceptos,
                                                    nombre,
                                                    codigo,
                                                    observaciones,
                                                    nro_factura,
                                                    cantidad,
                                                    precio,
                                                    exento,
                                                    comision,
                                                    total_precio,
                                                    moneda,
                                                    num_tarjeta,
                                                    total_monto,
                                                    forma_pago,
                                                    medio_pago,
                                                    lugar,
                                                    pais,
                                                    estado,
                                                    tipo_factura,
                                                    cajero,
                                                    cod_control
        								)
                                        (WITH  cabecera AS (select vent.id_venta,
                                                 vent.total_venta,
                                                 vent.fecha,
                                                 list (ingas.desc_ingas) as conceptos ,
                                                 pv.nombre ,
                                                 pv.codigo,
                                                 vent.observaciones,
                                                 vent.nro_factura,
                                                 list (det.cantidad::Varchar) cantidad,
                                                 list (det.precio::varchar) as precio,
                                                 vent.excento::varchar as exento,
                                                 vent.comision::varchar as comision,
                                                 list ((det.cantidad*det.precio)::Varchar) total_precio,
                                                 lug.nombre as lugar,
                                                 lug.id_lugar_fk,
                                                 vent.estado,
                                                 vent.tipo_factura,
                                                 usu.desc_persona,
                                                 vent.cod_control
                                          from vef.tventa vent
                                          left join vef.tventa_detalle det on det.id_venta = vent.id_venta
                                          left join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = det.id_producto
                                          inner join vef.tpunto_venta pv on pv.id_punto_venta = vent.id_punto_venta
                                          inner join vef.tsucursal suc on suc.id_sucursal = vent.id_sucursal
                                          inner join param.tlugar lug on lug.id_lugar = suc.id_lugar
                                          inner join segu.vusuario usu on usu.id_usuario = vent.id_usuario_cajero
                                          where vent.estado_reg = ''activo'' and (vent.estado = ''finalizado'' OR vent.estado = ''anulado'') and '||v_filtro_tipo_factura||' and '||v_filtro_id_cajero||' and '||v_filtro_fecha_desde||' and '||v_filtro_fecha_hasta||' and '||v_filtro_id_punto_venta||' and '||v_filtro_id_concepto||'
                                          and '||v_filtro_nit||'
                                          group by vent.id_venta, pv.nombre, pv.codigo, lug.nombre,lug.id_lugar_fk, usu.desc_persona),

                                        detalle as (
                                        select vent.id_venta,
                                               list (mon.codigo_internacional) AS moneda,
                                               list (fp.numero_tarjeta) as num_tarjeta,
                                               list (fp.monto_mb_efectivo::varchar) as total_monto,
                                               CASE
                                                      WHEN vent.id_deposito is not null
                                                      THEN
                                                      ''DEPÓSITO''
                                                      else
                                                      list (fpw.fop_code)
                                                END  as forma_pago,

                                                 CASE
                                                      WHEN vent.id_deposito is not null

                                                      THEN
                                                      ''DEPO''
                                                      else
                                                      list (mp.mop_code)
                                                END  as medio_pago
                                        from vef.tventa vent
                                        inner join vef.tventa_forma_pago fp on fp.id_venta = vent.id_venta
                                        inner join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = fp.id_medio_pago
                                        inner join obingresos.tforma_pago_pw fpw on fpw.id_forma_pago_pw = mp.forma_pago_id
                                        where vent.estado_reg = ''activo'' and (vent.estado = ''finalizado'' OR vent.estado = ''anulado'') and '||v_filtro_tipo_factura||' and '||v_filtro_id_cajero||' and '||v_filtro_fecha_desde||' and '||v_filtro_fecha_hasta||' and '||v_filtro_id_punto_venta||'
										and '||v_filtro_nit||'
                                        group by vent.id_venta)

                                        select ca.id_venta::integer,
                                               ca.total_venta::varchar,
                                               to_char(ca.fecha,''DD/MM/YYYY'')::varchar as fecha,
                                               ca.conceptos::varchar,
                                               ca.nombre::varchar,
                                               ca.codigo::varchar,
                                               ca.observaciones::varchar,
                                               ca.nro_factura::integer,
                                               ca.cantidad::varchar,
                                               ca.precio::varchar,
                                               ca.exento,
                                               ca.comision,
                                               ca.total_precio::varchar,
                                               det.moneda::varchar,
                                               det.num_tarjeta::varchar,
                                               det.total_monto::varchar,
                                               det.forma_pago::varchar,
                                               det.medio_pago::varchar,
                                               ca.lugar,
                                               lug.nombre::varchar as pais,
                                               (CASE
                                                  WHEN ca.estado = ''finalizado'' THEN
                                                  ''EMITIDA''
                                                  WHEN ca.estado = ''anulado''  THEN
                                                  ''ANULADA''
                                               END)::varchar as estado,
                                               (CASE
                                                  WHEN ca.tipo_factura = ''computarizada'' THEN
                                                  ''Facturación Computarizada''
                                                  WHEN ca.tipo_factura = ''manual''  THEN
                                                  ''Facturación Manual''
                                                  WHEN ca.tipo_factura = ''recibo''  THEN
                                                  ''RO Computarizado''
                                                  WHEN ca.tipo_factura = ''recibo_manual''  THEN
                                                  ''RO Manual''
                                                  WHEN ca.tipo_factura = ''carga''  THEN
                                                  ''Facturación Carga Computarizada''
                                               END)::varchar as tipo_factura,
                                               ca.desc_persona,
                                               ca.cod_control
                                        from cabecera ca
                                        inner join detalle det on det.id_venta = ca.id_venta
                                        inner join param.tlugar lug on lug.id_lugar = ca.id_lugar_fk
                                        order by nombre, nro_factura ASC)';
        execute v_consulta_insertar_reporte;


        v_consulta:='select 	count (id_venta),
        				       sum (COALESCE (comision::numeric,0)) as totales_comision,
                               sum (COALESCE(exento::numeric,0)) as totales_exento,
                               sum (COALESCE (total_venta::numeric,0)) as totales_venta
        			from reporte_facturacion_computarizada
                    where ';

        v_consulta:=v_consulta||v_parametros.filtro;

        --Devuelve la respuesta
        return v_consulta;

      end;





       /*********************************
 	#TRANSACCION:  'VF_REPFACTCABE_SEL'
 	#DESCRIPCION:	Reporte de Facturacion Computarizada
 	#AUTOR:		admin
 	#FECHA:		01-12-2020 14:47:00
	***********************************/

    elsif(p_transaccion='VF_REPFACTCABE_SEL')then

      begin
       	--raise exception 'Aqui llega datos %',v_parametros.desde;
        if (v_parametros.desde != '' || v_parametros.desde is not null) then
        	v_filtro_fecha_desde = 'vent.fecha >= '''||v_parametros.desde||''' ';
        else
        	v_filtro_fecha_desde = '0=0';
        end if;

        if (v_parametros.hasta != '' || v_parametros.hasta is not null) then
        	v_filtro_fecha_hasta = 'vent.fecha <= '''||v_parametros.hasta||''' ';
        else
        	v_filtro_fecha_hasta = '0=0';
        end if;

        if (v_parametros.id_punto_venta is not null) then
        	v_filtro_id_punto_venta = 'vent.id_punto_venta = '||v_parametros.id_punto_venta||'';
        else
        	v_filtro_id_punto_venta = '0=0';
        end if;


        v_consulta:='select
                           pv.nombre::varchar,
                           pv.codigo::varchar,
                           lug.nombre::varchar as lugar,
                           (select lg.nombre
                           from param.tlugar lg
                           where lg.id_lugar = lug.id_lugar_fk)::varchar as pais
                    from vef.tventa vent
                    inner join vef.tpunto_venta pv on pv.id_punto_venta = vent.id_punto_venta
                    inner join vef.tsucursal suc on suc.id_sucursal = vent.id_sucursal
                    inner join param.tlugar lug on lug.id_lugar = suc.id_lugar
                    where '||v_filtro_fecha_desde||' and '||v_filtro_fecha_hasta||' and '||v_filtro_id_punto_venta||'
                    group by pv.nombre, pv.codigo, lug.nombre, lug.id_lugar_fk';
		raise notice '%',v_consulta;
        --Devuelve la respuesta
        return v_consulta;

      end;

      /*********************************
 	#TRANSACCION:  'VF_REPFACTCON_SEL'
 	#DESCRIPCION:	Reporte de Facturacion Computarizada por Concepto
 	#AUTOR:		admin
 	#FECHA:		01-12-2020 14:47:00
	***********************************/

    elsif(p_transaccion='VF_REPFACTCON_SEL')then

      begin
       	--raise exception 'Aqui llega datos %',v_parametros.desde;
        if (v_parametros.desde != '' || v_parametros.desde is not null) then
        	v_filtro_fecha_desde = 'vent.fecha >= '''||v_parametros.desde||''' ';
        else
        	v_filtro_fecha_desde = '0=0';
        end if;

        if (v_parametros.hasta != '' || v_parametros.hasta is not null) then
        	v_filtro_fecha_hasta = 'vent.fecha <= '''||v_parametros.hasta||''' ';
        else
        	v_filtro_fecha_hasta = '0=0';
        end if;

        if (v_parametros.id_punto_venta is not null and v_parametros.id_punto_venta != 0) then
        	v_filtro_id_punto_venta = 'vent.id_punto_venta = '||v_parametros.id_punto_venta||'';
        else
        	v_filtro_id_punto_venta = '0=0';
        end if;

         if (v_parametros.id_concepto is not null) then
        	v_filtro_id_concepto = 'det.id_producto = '||v_parametros.id_concepto||'';
        else
        	v_filtro_id_concepto = '0=0';
        end if;

        if (v_parametros.id_usuario_cajero is not null) then
          if (v_parametros.id_usuario_cajero != 0) then
              v_filtro_id_cajero = 'vent.id_usuario_cajero = '||v_parametros.id_usuario_cajero||'';
          else
              v_filtro_id_cajero = '0=0';
          end if;
        else
        	v_filtro_id_cajero = '0=0';
        end if;


        if (v_parametros.tipo_documento is not null) then
        	if (v_parametros.tipo_documento = 'factura') then
            	v_filtro_tipo_factura = '(vent.tipo_factura = ''computarizada'' or vent.tipo_factura = ''manual'')';
            elsif (v_parametros.tipo_documento = 'recibo') then
            	v_filtro_tipo_factura = '(vent.tipo_factura = ''recibo'' or vent.tipo_factura = ''recibo_manual'')';
            end if;
        else
        	v_filtro_tipo_factura = '0=0';
        end if;

        v_consulta:='(
        			 (select
                         ingas.desc_ingas::varchar,
                         (det.cantidad * det.precio)::numeric as total_precio,
                         vent.nro_factura::varchar,
                         dos.nroaut::varchar,
                         vent.id_punto_venta::integer,
                         to_char(vent.fecha,''DD/MM/YYYY'')::varchar as fecha,
                         pv.nombre::varchar,
                         vent.id_venta::integer,
                         usu.desc_persona::varchar as desc_persona,
                         pv.codigo,
                         (CASE
                            WHEN vent.estado = ''finalizado'' THEN
                            ''EMITIDA''
                            WHEN vent.estado = ''anulado''  THEN
                            ''ANULADA''
                         END)::varchar as estado,
                         (CASE
                            WHEN vent.tipo_factura = ''computarizada'' THEN
                            ''Facturación Computarizada''
                            WHEN vent.tipo_factura = ''manual''  THEN
                            ''Facturación Manual''
                            WHEN vent.tipo_factura = ''recibo''  THEN
                            ''RO Computarizado''
                            WHEN vent.tipo_factura = ''recibo_manual''  THEN
                            ''RO Manual''
                            WHEN vent.tipo_factura = ''carga''  THEN
                            ''Facturación Carga Computarizada''
                         END)::varchar as tipo_factura
                      from vef.tventa vent
                      inner join vef.tventa_detalle det on det.id_venta = vent.id_venta
                      inner join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = det.id_producto
                      left join vef.tdosificacion dos on dos.id_dosificacion = vent.id_dosificacion
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = vent.id_punto_venta
                      inner join segu.vusuario usu on usu.id_usuario = vent.id_usuario_cajero
                      where (vent.estado = ''finalizado'') and '||v_filtro_id_cajero||' and '||v_filtro_tipo_factura||' and '||v_filtro_fecha_desde||' and '||v_filtro_fecha_hasta||' and '||v_filtro_id_punto_venta||' and '||v_filtro_id_concepto||'
                      order by  vent.id_venta, vent.nro_factura DESC)

                      UNION ALL

                      (select
                         ingas.desc_ingas::varchar,
                         null::numeric as total_precio,
                         NULL::varchar as nro_factura,
                         null::varchar as nroaut,
                         NULL::integer as id_punto_venta,
                         NULL::varchar as fecha,
                         pv.nombre::varchar,
                         NULL::integer as id_venta,
                         ''cabecera''::varchar as desc_persona,
                         pv.codigo,
                         NULL::varchar as tipo_factura,
     					 NULL::varchar as estado
                      from vef.tventa vent
                      inner join vef.tventa_detalle det on det.id_venta = vent.id_venta
                      inner join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = det.id_producto
                      left join vef.tdosificacion dos on dos.id_dosificacion = vent.id_dosificacion
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = vent.id_punto_venta
                      inner join segu.vusuario usu on usu.id_usuario = vent.id_usuario_cajero
                      where (vent.estado = ''finalizado'') and '||v_filtro_id_cajero||' and '||v_filtro_tipo_factura||' and '||v_filtro_fecha_desde||' and '||v_filtro_fecha_hasta||' and '||v_filtro_id_punto_venta||' and '||v_filtro_id_concepto||'
                      group by pv.nombre, pv.codigo, ingas.desc_ingas)
                      )
                      order by nombre ASC, id_venta ASC NULLS FIRST';

		raise notice '%',v_consulta;
        --Devuelve la respuesta
        return v_consulta;

      end;
    /*********************************
 	#TRANSACCION:  'VF_REPRESUCOMP_SEL'
 	#DESCRIPCION:	Reporte de Facturacion Computarizada
 	#AUTOR:		admin
 	#FECHA:		01-12-2020 14:47:00
	***********************************/

    elsif(p_transaccion='VF_REPRESUCOMP_SEL')then

      begin
       	--raise exception 'Aqui llega datos %',v_parametros.desde;
        if (v_parametros.desde != '' || v_parametros.desde is not null) then
        	v_filtro_fecha_desde = 'vent.fecha >= '''||v_parametros.desde||''' ';
        else
        	v_filtro_fecha_desde = '0=0';
        end if;

        if (v_parametros.hasta != '' || v_parametros.hasta is not null) then
        	v_filtro_fecha_hasta = 'vent.fecha <= '''||v_parametros.hasta||''' ';
        else
        	v_filtro_fecha_hasta = '0=0';
        end if;

        if (v_parametros.id_punto_venta is not null and v_parametros.id_punto_venta != 0) then
        	v_filtro_id_punto_venta = 'vent.id_punto_venta = '||v_parametros.id_punto_venta||'';
        else
        	v_filtro_id_punto_venta = '0=0';
        end if;

         if (v_parametros.id_concepto is not null) then
        	v_filtro_id_concepto = 'det.id_producto = '||v_parametros.id_concepto||'';
        else
        	v_filtro_id_concepto = '0=0';
        end if;

        if (v_parametros.id_usuario_cajero is not null) then
          if (v_parametros.id_usuario_cajero != 0) then
              v_filtro_id_cajero = 'vent.id_usuario_cajero = '||v_parametros.id_usuario_cajero||'';
          else
              v_filtro_id_cajero = '0=0';
          end if;
        else
        	v_filtro_id_cajero = '0=0';
        end if;


        if (v_parametros.tipo_documento is not null) then
        	if (v_parametros.tipo_documento = 'factura') then
            	v_filtro_tipo_factura = '(vent.tipo_factura = ''computarizada'' or vent.tipo_factura = ''manual'')';
            elsif (v_parametros.tipo_documento = 'recibo') then
            	v_filtro_tipo_factura = '(vent.tipo_factura = ''recibo'' or vent.tipo_factura = ''recibo_manual'')';
            end if;
        else
        	v_filtro_tipo_factura = '0=0';
        end if;




		--raise exception 'Aqui llega param %',v_filtro_fecha_desde;
        v_consulta:='((select
                         ingas.desc_ingas::varchar,
                         SUM (det.cantidad * det.precio)::numeric as total_precio,
                         pv.nombre::varchar,
                         pv.codigo,
                         usu.desc_persona::varchar
                      from vef.tventa vent
                      inner join vef.tventa_detalle det on det.id_venta = vent.id_venta
                      inner join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = det.id_producto
                      inner join vef.tpunto_venta pv on pv.id_punto_venta = vent.id_punto_venta
                      inner join segu.vusuario usu on usu.id_usuario = vent.id_usuario_cajero
                      where vent.estado_reg = ''activo'' and (vent.estado = ''finalizado'') and '||v_filtro_id_cajero||' and '||v_filtro_tipo_factura||' and '||v_filtro_fecha_desde||' and '||v_filtro_fecha_hasta||' and '||v_filtro_id_punto_venta||' and '||v_filtro_id_concepto||'
                      group by ingas.desc_ingas, pv.nombre, pv.codigo, usu.desc_persona)

                      UNION ALL

                      (select
                              NULL::varchar as desc_ingas,
                              NULL::numeric as total_precio,
                              pv.nombre::varchar,
                              pv.codigo,
                              ''cabecera''::varchar as desc_persona
                        from vef.tventa vent
                        inner join vef.tventa_detalle det on det.id_venta = vent.id_venta
                        inner join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = det.id_producto
                        inner join vef.tpunto_venta pv on pv.id_punto_venta = vent.id_punto_venta
                        inner join segu.vusuario usu on usu.id_usuario = vent.id_usuario_cajero
                        where vent.estado_reg = ''activo'' and (vent.estado = ''finalizado'') and '||v_filtro_id_cajero||' and '||v_filtro_tipo_factura||' and '||v_filtro_fecha_desde||' and '||v_filtro_fecha_hasta||' and '||v_filtro_id_punto_venta||' and '||v_filtro_id_concepto||'
                      	group by pv.nombre, pv.codigo))
                        order by nombre ASC, desc_ingas ASC NULLS FIRST';
		raise notice '%',v_consulta;
        --Devuelve la respuesta
        return v_consulta;

      end;

    /*********************************
 	#TRANSACCION:  'VF_MONBA_SEL'
 	#DESCRIPCION:	Moneda Base
 	#AUTOR:		admin
 	#FECHA:		01-12-2020 14:47:00
	***********************************/

    elsif(p_transaccion='VF_MONBA_SEL')then

      begin

      	select
          nombre
          into
          v_nombre_pv
        from vef.tpunto_venta
        where id_punto_venta = v_parametros.id_punto_venta;




        v_consulta:='select mon.codigo_internacional,
        			'''||COALESCE (v_nombre_pv, '')||'''::varchar as nombre_pv
                    from param.tmoneda mon
                    where mon.tipo_moneda = ''base''';

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

ALTER FUNCTION vef.ft_repventa_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
