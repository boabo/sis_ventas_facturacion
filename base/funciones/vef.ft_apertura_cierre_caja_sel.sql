CREATE OR REPLACE FUNCTION vef.ft_apertura_cierre_caja_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_apertura_cierre_caja_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tapertura_cierre_caja'
 AUTOR: 		 (jrivera)
 FECHA:	        07-07-2016 14:16:20
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_consulta    				varchar;
	v_parametros  				record;
	v_nombre_funcion   			text;
	v_resp						varchar;
    v_fecha						date;
    v_id_pv						integer;
    v_id_sucursal				integer;
    v_id_moneda_base			integer;
    v_id_moneda_tri				integer;
    v_tiene_dos_monedas			varchar;
    v_tipo_cambio				numeric;
    v_moneda_extranjera			varchar;
    v_moneda_local				varchar;
    v_cod_moneda_extranjera		varchar;
    v_cod_moneda_local			varchar;
    v_moneda_base				varchar;
    v_moneda_ref				varchar;
    v_filtro					varchar;

BEGIN

	v_nombre_funcion = 'vef.ft_apertura_cierre_caja_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_APCIE_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera
 	#FECHA:		07-07-2016 14:16:20
	***********************************/

	if(p_transaccion='VF_APCIE_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						apcie.id_apertura_cierre_caja,
						apcie.id_sucursal,
						apcie.id_punto_venta,
						apcie.id_usuario_cajero,
						apcie.id_moneda,
						apcie.obs_cierre,
						apcie.monto_inicial,
						apcie.obs_apertura,
						apcie.monto_inicial_moneda_extranjera,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        apcie.estado,
                        apcie.fecha_apertura_cierre,
                        apcie.fecha_hora_cierre,
                        pv.nombre as nombre_punto_venta,
                        suc.nombre as nombre_sucursal,
                        apcie.arqueo_moneda_local,
                        apcie.arqueo_moneda_extranjera,
                        apcie.id_entrega_brinks
						from vef.tapertura_cierre_caja apcie
						inner join segu.tusuario usu1 on usu1.id_usuario = apcie.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = apcie.id_usuario_mod
				        left join vef.tpunto_venta pv on pv.id_punto_venta = apcie.id_punto_venta
                        left join vef.tsucursal suc on suc.id_sucursal = apcie.id_sucursal

                        where  ';


            IF p_administrador !=1  THEN

            	IF EXISTS (select 1
				    	   from vef.tsucursal_usuario su
						   where su.id_usuario =p_id_usuario
						   and su.tipo_usuario = 'administrador') THEN

                	v_filtro = ' apcie.id_punto_venta in (select pv.id_punto_venta
								 from vef.tsucursal s
                                 inner join vef.tpunto_venta pv on pv.id_sucursal = s.id_sucursal
                                 inner join vef.tsucursal_usuario su on su.id_punto_venta = pv.id_punto_venta
                                 and su.tipo_usuario = ''administrador''
								 where su.id_usuario ='||p_id_usuario||') and ';

                ELSE
              		v_filtro = ' apcie.id_usuario_cajero='||p_id_usuario||' and ';

              	END IF;
            ELSE
              v_filtro = ' ';
            END IF;
            v_consulta :=v_consulta||v_filtro;
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_APCIE_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera
 	#FECHA:		07-07-2016 14:16:20
	***********************************/

	elsif(p_transaccion='VF_APCIE_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_apertura_cierre_caja)
					    from vef.tapertura_cierre_caja apcie
					    inner join segu.tusuario usu1 on usu1.id_usuario = apcie.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = apcie.id_usuario_mod
					    left join vef.tpunto_venta pv on pv.id_punto_venta = apcie.id_punto_venta
                        left join vef.tsucursal suc on suc.id_sucursal = apcie.id_sucursal

                        where ';

			IF p_administrador !=1  THEN

            	IF EXISTS (select 1
				    	   from vef.tsucursal_usuario su
						   where su.id_usuario =p_id_usuario
						   and su.tipo_usuario = 'administrador') THEN

                	v_filtro = ' apcie.id_punto_venta in (select pv.id_punto_venta
								 from vef.tsucursal s
                                 inner join vef.tpunto_venta pv on pv.id_sucursal = s.id_sucursal
                                 inner join vef.tsucursal_usuario su on su.id_punto_venta = pv.id_punto_venta
                                 and su.tipo_usuario = ''administrador''
								 where su.id_usuario ='||p_id_usuario||') and ';

                ELSE
              		v_filtro = ' apcie.id_usuario_cajero='||p_id_usuario||' and ';

              	END IF;
            ELSE
              v_filtro = ' ';
            END IF;

            v_consulta :=v_consulta||v_filtro;
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'VF_CIE_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		28-09-2017
	***********************************/

	elsif(p_transaccion='VF_CIE_SEL')then

    	begin
            select codigo_internacional into v_moneda_base
            from param.tmoneda
        	where tipo_moneda='base';

            select codigo_internacional into v_moneda_ref
            from param.tmoneda
        	where tipo_moneda='ref';

    		--Sentencia de la consulta
			v_consulta:='select apcie.id_apertura_cierre_caja,
                         apcie.id_sucursal,
                         apcie.id_punto_venta,
                         pv.nombre as nombre_punto_venta,
                         apcie.obs_cierre,
                         apcie.monto_inicial,
                         apcie.obs_apertura,
                         apcie.monto_inicial_moneda_extranjera,
                         apcie.estado,
                         apcie.fecha_apertura_cierre,
                         apcie.arqueo_moneda_local,
                         apcie.arqueo_moneda_extranjera,
                         obingresos.f_monto_forma_pago_boletos('''||v_moneda_base::varchar||''','||v_parametros.id_usuario_cajero||','''||v_parametros.fecha::date||''') as monto_base_fp_boleto,
                         obingresos.f_monto_forma_pago_boletos('''||v_moneda_ref::varchar||''','||v_parametros.id_usuario_cajero||','''||v_parametros.fecha::date||''') as monto_ref_fp_boleto,
                         vef.f_monto_forma_pago_endesis('''||v_moneda_base::varchar||''','||v_parametros.id_usuario_cajero||','''||v_parametros.fecha::date||''') as monto_base_fp_ventas,
                         vef.f_monto_forma_pago_endesis('''||v_moneda_ref::varchar||''','||v_parametros.id_usuario_cajero||','''||v_parametros.fecha::date||''') as monto_ref_fp_ventas
                  from vef.tapertura_cierre_caja apcie
                  inner join obingresos.tboleto_amadeus bol on bol.id_punto_venta=apcie.id_punto_venta and bol.id_usuario_cajero= '||v_parametros.id_usuario_cajero||'
                  inner join vef.tpunto_venta pv on pv.id_punto_venta=apcie.id_punto_venta
                  where apcie.id_usuario_cajero = '||v_parametros.id_usuario_cajero||' and
                  		apcie.fecha_apertura_cierre='''||v_parametros.fecha||''' and
                      bol.estado=''revisado'' and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' group by apcie.id_apertura_cierre_caja,
                                               apcie.id_sucursal,
                                               apcie.id_punto_venta,
                                               apcie.id_usuario_cajero,
                                               apcie.id_moneda,
                                               apcie.obs_cierre,
                                               apcie.monto_inicial,
                                               apcie.obs_apertura,
                                               apcie.monto_inicial_moneda_extranjera,
                                               apcie.estado,
                                               apcie.fecha_apertura_cierre,
                                               apcie.arqueo_moneda_local,
                                               apcie.arqueo_moneda_extranjera,
                                               pv.nombre
                  						order by apcie.id_apertura_cierre_caja asc ';
            v_consulta:=v_consulta||' limit 1 ';
			raise notice 'v_consulta %', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'VF_REPAPCIE_SEL'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera
 	#FECHA:		07-07-2016 14:16:20
	***********************************/

	elsif(p_transaccion='VF_REPAPCIE_SEL')then

		begin

        	select acc.id_punto_venta,acc.id_sucursal,acc.id_moneda,acc.fecha_apertura_cierre into v_id_pv,v_id_sucursal,v_id_moneda_base, v_fecha
            from vef.tapertura_cierre_caja acc
            where acc.id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja;

            select m.id_moneda,m.codigo_internacional,m.moneda || ' (' || m.codigo_internacional || ')' into v_id_moneda_tri,v_cod_moneda_extranjera,v_moneda_extranjera
            from param.tmoneda m
            where m.estado_reg = 'activo' and m.triangulacion = 'si';

            select m.codigo_internacional,m.moneda || ' (' || m.codigo_internacional || ')' into v_cod_moneda_local,v_moneda_local
            from param.tmoneda m
            where m.id_moneda = v_id_moneda_base ;

            v_tiene_dos_monedas = 'no';
            v_tipo_cambio = 1;
            if (v_id_moneda_tri != v_id_moneda_base) then
            	v_tiene_dos_monedas = 'si';
                v_tipo_cambio = param.f_get_tipo_cambio_v2(v_id_moneda_base, v_id_moneda_tri,v_fecha,'O');
            end if;

			--Sentencia de la consulta de conteo de registros
			v_consulta:='with forma_pago as (
                          select fp.id_forma_pago,fp.id_moneda,
                          (case when fp.codigo like ''CA%'' then
                              ''CASH''
                          when fp.codigo like ''CC%'' then
                              ''CC''
                          when fp.codigo like ''CT%'' then
                              ''CT''
                          when fp.codigo like ''MCO%'' then
                              ''MCO''
                          else
                              ''OTRO''
                          end)::varchar as codigo

                          from obingresos.tforma_pago fp


                      )
                      select u.desc_persona::varchar, to_char(acc.fecha_apertura_cierre,''DD/MM/YYYY'')::varchar,
                      coalesce(ppv.codigo,ps.codigo)::varchar as pais, COALESCE(lpv.codigo,ls.codigo)::varchar as estacion,
                      coalesce(pv.codigo || ''-'' || pv.nombre, s.codigo || ''-'' || s.nombre)::varchar as punto_venta,
                      acc.obs_cierre::varchar, acc.arqueo_moneda_local,acc.arqueo_moneda_extranjera,acc.monto_inicial,acc.monto_inicial_moneda_extranjera,
                      ' || v_tipo_cambio || '::numeric as tipo_cambio, ''' || v_tiene_dos_monedas || '''::varchar as tiene_dos_monedas,
                      ''' || v_moneda_local || '''::varchar as moneda_local,''' || v_moneda_extranjera || '''::varchar as moneda_extranjera,
                       ''' || v_cod_moneda_local || '''::varchar as cod_moneda_local,''' || v_cod_moneda_extranjera || '''::varchar as cod_moneda_extranjera,
                      sum(case  when fp.codigo = ''CASH'' and fp.id_moneda = ' || v_id_moneda_base  || ' then
                              bfp.importe
                          else
                              0
                          end)as efectivo_boletos_ml,
                      sum(case  when fp.codigo = ''CASH'' and fp.id_moneda = ' || v_id_moneda_tri  || ' then
                              bfp.importe
                          else
                              0
                          end)as efectivo_boletos_me,
                      sum(case  when fp.codigo = ''CC'' and fp.id_moneda = ' || v_id_moneda_base  || ' then
                              bfp.importe
                          else
                              0
                          end)as tarjeta_boletos_ml,
                      sum(case  when fp.codigo = ''CC'' and fp.id_moneda = ' || v_id_moneda_tri  || ' then
                              bfp.importe
                          else
                              0
                          end)as tarjeta_boletos_me,
                      sum(case  when fp.codigo = ''CT'' and fp.id_moneda = ' || v_id_moneda_base  || ' then
                              bfp.importe
                          else
                              0
                          end)as cuenta_corriente_boletos_ml,
                      sum(case  when fp.codigo = ''CT'' and fp.id_moneda = ' || v_id_moneda_tri  || ' then
                              bfp.importe
                          else
                              0
                          end)as cuenta_corriente_boletos_me,
                      sum(case  when fp.codigo = ''MCO'' and fp.id_moneda = ' || v_id_moneda_base  || ' then
                              bfp.importe
                          else
                              0
                          end)as mco_boletos_ml,
                      sum(case  when fp.codigo = ''MCO'' and fp.id_moneda = ' || v_id_moneda_tri  || ' then
                              bfp.importe
                          else
                              0
                          end)as mco_boletos_me,
                      sum(case  when fp.codigo = ''OTRO'' and fp.id_moneda = ' || v_id_moneda_base  || ' then
                              bfp.importe
                          else
                              0
                          end)as otro_boletos_ml,
                      sum(case  when fp.codigo like ''OTRO'' and fp.id_moneda = ' || v_id_moneda_tri  || ' then
                              bfp.importe
                          else
                              0
                          end)as otro_boletos_me,


                      sum(case  when fp2.codigo = ''CASH'' and fp2.id_moneda = ' || v_id_moneda_base  || ' then
                              vfp.monto_mb_efectivo
                          else
                              0
                          end)as efectivo_ventas_ml,
                      sum(case  when fp2.codigo = ''CASH'' and fp2.id_moneda = ' || v_id_moneda_tri  || ' then
                              vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                          else
                              0
                          end)as efectivo_ventas_me,
                      sum(case  when fp2.codigo = ''CC'' and fp2.id_moneda = ' || v_id_moneda_base  || ' then
                              vfp.monto_mb_efectivo
                          else
                              0
                          end)as tarjeta_ventas_ml,
                      sum(case  when fp2.codigo = ''CC'' and fp2.id_moneda = ' || v_id_moneda_tri  || ' then
                              vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                          else
                              0
                          end)as tarjeta_vetas_me,
                      sum(case  when fp2.codigo = ''CT'' and fp2.id_moneda = ' || v_id_moneda_base  || ' then
                              vfp.monto_mb_efectivo
                          else
                              0
                          end)as cuenta_corriente_ventas_ml,
                      sum(case  when fp2.codigo = ''CT'' and fp2.id_moneda = ' || v_id_moneda_tri  || ' then
                              vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                          else
                              0
                          end)as cuenta_corriente_ventas_me,
                      sum(case  when fp2.codigo = ''MCO'' and fp2.id_moneda = ' || v_id_moneda_base  || ' then
                              vfp.monto_mb_efectivo
                          else
                              0
                          end)as mco_ventas_ml,
                      sum(case  when fp2.codigo = ''MCO'' and fp2.id_moneda = ' || v_id_moneda_tri  || ' then
                              vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                          else
                              0
                          end)as mco_ventas_me,
                      sum(case  when fp2.codigo = ''OTRO'' and fp2.id_moneda = ' || v_id_moneda_base  || ' then
                              vfp.monto_mb_efectivo
                          else
                              0
                          end)as otro_ventas_ml,
                      sum(case  when fp2.codigo like ''OTRO'' and fp2.id_moneda = ' || v_id_moneda_tri  || ' then
                              vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                          else
                              0
                          end)as otro_ventas_me,
                      COALESCE((	select sum(ven.comision) from vef.tventa ven
                          where coalesce(ven.comision,0) > 0 and ven.id_moneda = ' || v_id_moneda_base  || ' and
                                  ven.fecha = acc.fecha_apertura_cierre and ven.id_punto_venta= acc.id_punto_venta
                                  and ven.id_usuario_cajero = acc.id_usuario_cajero and
                                  ven.estado = ''finalizado''),0) +

                      COALESCE((	select sum(bol.comision) from obingresos.tboleto_amadeus bol
                          where coalesce(bol.comision,0) > 0 and bol.id_moneda_boleto = ' || v_id_moneda_base  || ' and
                                  bol.fecha_emision = acc.fecha_apertura_cierre and bol.id_punto_venta=acc.id_punto_venta
                                  and bol.id_usuario_cajero = acc.id_usuario_cajero and
                                  bol.estado = ''revisado''),0) as comisiones_ml,

                      COALESCE((	select sum(ven.comision) from vef.tventa ven
                          where coalesce(ven.comision,0) > 0 and ven.id_moneda = ' || v_id_moneda_tri  || ' and
                                  ven.fecha = acc.fecha_apertura_cierre and ven.id_punto_venta=acc.id_punto_venta
                                  and ven.id_usuario_cajero = acc.id_usuario_cajero and
                                  ven.estado = ''finalizado''),0) +

                      COALESCE((	select sum(bol.comision) from obingresos.tboleto_amadeus bol
                          where coalesce(bol.comision,0) > 0 and bol.id_moneda_boleto = ' || v_id_moneda_tri  || ' and
                                  bol.fecha_emision = acc.fecha_apertura_cierre and bol.id_punto_venta= acc.id_punto_venta
                                   and bol.id_usuario_cajero = acc.id_usuario_cajero and
                                  bol.estado = ''revisado''),0)   as comisiones_me,
					  acc.monto_ca_recibo_ml,
                      acc.monto_cc_recibo_ml
                      from vef.tapertura_cierre_caja acc
                      inner join segu.vusuario u on u.id_usuario = acc.id_usuario_cajero
                      left join vef.tsucursal s on acc.id_sucursal = s.id_sucursal
                      left join vef.tpunto_venta pv on pv.id_punto_venta = acc.id_punto_venta
                      left join vef.tsucursal spv on spv.id_sucursal = pv.id_sucursal
                      left join param.tlugar lpv on lpv.id_lugar = spv.id_lugar
                      left join param.tlugar ls on ls.id_lugar = s.id_lugar
                      left join param.tlugar ppv on ppv.id_lugar = param.f_get_id_lugar_pais(lpv.id_lugar)
                      left join param.tlugar ps on ps.id_lugar = param.f_get_id_lugar_pais(ls.id_lugar)
                      left join obingresos.tboleto_amadeus b on b.id_usuario_cajero = u.id_usuario
                                                      and b.fecha_reg::date = acc.fecha_apertura_cierre and
                                                      b.id_punto_venta = acc.id_punto_venta and b.estado = ''revisado''
													  and b.voided=''no''
                      left join obingresos.tboleto_amadeus_forma_pago bfp on bfp.id_boleto_amadeus = b.id_boleto_amadeus
                      left join forma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                      left join vef.tventa v on v.id_usuario_cajero = u.id_usuario
                                                      and v.fecha = acc.fecha_apertura_cierre and
                                                      v.id_punto_venta = acc.id_punto_venta and v.estado = ''finalizado''

                      left join vef.tventa_forma_pago vfp on vfp.id_venta = v.id_venta
                      left join forma_pago fp2 on fp2.id_forma_pago = vfp.id_forma_pago
                      where acc.id_apertura_cierre_caja = ' || v_parametros.id_apertura_cierre_caja  || '
                      group by u.desc_persona, acc.fecha_apertura_cierre,
                      ppv.codigo,ps.codigo,lpv.codigo,ls.codigo,
                      pv.codigo , pv.nombre, s.codigo ,s.nombre,acc.id_punto_venta,
                      acc.id_usuario_cajero,acc.obs_cierre, acc.arqueo_moneda_local,
                      acc.arqueo_moneda_extranjera,acc.monto_inicial,acc.monto_inicial_moneda_extranjera,
                      acc.monto_ca_recibo_ml, acc.monto_cc_recibo_ml';



            IF(pxp.f_get_variable_global('vef_facturacion_endesis')='true')THEN

            	v_consulta:= 'with total_ventas as(('||v_consulta||')
                              UNION ALL
                              (with forma_pago as (
                                      select fp.id_forma_pago,
                                             fp.id_moneda,
                                             (case
                                                when fp.codigo like ''CA%'' then ''CASH''
                                                when fp.codigo like ''CC%'' then ''CC''
                                                when fp.codigo like ''CT%'' then ''CT''
                                                when fp.codigo like ''MCO%'' then ''MCO''
                                                else ''OTRO''
                                              end)::varchar as codigo
                                      from obingresos.tforma_pago fp)
                                  select u.desc_persona::varchar,
                                         to_char(acc.fecha_apertura_cierre, ''DD/MM/YYYY'')::varchar as fecha_apertura_cierre,
                                         v.pais,
                                         v.estacion,
                                         v.agt || '' - '' || v.razon_sucursal as punto_venta,
                                         acc.obs_cierre::varchar,
                                         acc.arqueo_moneda_local,
                                         acc.arqueo_moneda_extranjera,
                                         acc.monto_inicial,
                                         acc.monto_inicial_moneda_extranjera,
                                         '||v_tipo_cambio||'::numeric as tipo_cambio,
                                         '''||v_tiene_dos_monedas||'''::varchar as tiene_dos_monedas,
                                         '''||v_moneda_local||'''::varchar as moneda_local,
                                         '''||v_moneda_extranjera||'''::varchar as moneda_extranjera,
                                         '''||v_cod_moneda_local||'''::varchar as cod_moneda_local,
                                         '''||v_cod_moneda_extranjera||'''::varchar as cod_moneda_extranjera,
                                         0 as efectivo_boletos_ml,
                                         0 as efectivo_boletos_me,
                                         0 as tarjeta_boletos_ml,
                                         0 as tarjeta_boletos_me,
                                         0 as cuenta_corriente_boletos_ml,
                                         0 as cuenta_corriente_boletos_me,
                                         0 as mco_boletos_ml,
                                         0 as mco_boletos_me,
                                         0 as otro_boletos_ml,
                                         0 as otro_boletos_me,
                                         sum(case
                                               when fp2.codigo = ''CASH'' and fp2.id_moneda = '||v_id_moneda_base||' then vfp.importe_pago
                                               else 0
                                             end) as efectivo_ventas_ml,
                                         sum(case
                                               when fp2.codigo = ''CASH'' and fp2.id_moneda = '||v_id_moneda_tri||' then vfp.importe_pago
                                               else 0
                                             end) as efectivo_ventas_me,
                                         sum(case
                                               when fp2.codigo = ''CC'' and fp2.id_moneda = '||v_id_moneda_base||' then vfp.importe_pago
                                               else 0
                                             end) as tarjeta_ventas_ml,
                                         sum(case
                                               when fp2.codigo = ''CC'' and fp2.id_moneda = '||v_id_moneda_tri||' then vfp.importe_pago
                                               else 0
                                             end) as tarjeta_vetas_me,
                                         sum(case
                                               when fp2.codigo = ''CT'' and fp2.id_moneda = '||v_id_moneda_base||' then vfp.importe_pago
                                               else 0
                                             end) as cuenta_corriente_ventas_ml,
                                         sum(case
                                               when fp2.codigo = ''CT'' and fp2.id_moneda = '||v_id_moneda_tri||' then vfp.importe_pago
                                               else 0
                                             end) as cuenta_corriente_ventas_me,
                                         sum(case
                                               when fp2.codigo = ''MCO'' and fp2.id_moneda = '||v_id_moneda_base||' then vfp.importe_pago
                                               else 0
                                             end) as mco_ventas_ml,
                                         sum(case
                                               when fp2.codigo = ''MCO'' and fp2.id_moneda = '||v_id_moneda_tri||' then vfp.importe_pago
                                               else 0
                                             end) as mco_ventas_me,
                                         sum(case
                                               when fp2.codigo = ''OTRO'' and fp2.id_moneda = '||v_id_moneda_base||' then vfp.importe_pago
                                               else 0
                                             end) as otro_ventas_ml,
                                         sum(case
                                               when fp2.codigo like ''OTRO'' and fp2.id_moneda = '||v_id_moneda_tri||' then vfp.importe_pago
                                               else 0
                                             end) as otro_ventas_me,
                                         0 as comisiones_ml,
                                         0 as comisiones_me,
                                         0 as monto_ca_recibo_ml,
                            			 0 as monto_cc_recibo_ml
                                  from vef.tapertura_cierre_caja acc
                                  inner join vef.tpunto_venta pv on pv.id_punto_venta=acc.id_punto_venta
                                       inner join segu.vusuario u on u.id_usuario = acc.id_usuario_cajero
                                       inner join vef.tfactucom_endesis v on v.fecha=acc.fecha_apertura_cierre and v.estado_reg=''emitida''
                                       and v.usuario=u.cuenta and v.agt::varchar=pv.codigo
                                       inner join vef.tfactucompag_endesis vfp on vfp.id_factucom=v.id_factucom
                                       inner join forma_pago fp2 on fp2.id_forma_pago=(select fp.id_forma_pago
                                                                                          from obingresos.tforma_pago fp
                                                                                          inner join param.tmoneda mon on mon.id_moneda=fp.id_moneda
                                                                                          inner join param.tlugar lug on lug.id_lugar=fp.id_lugar
                                                                                          where fp.codigo=vfp.forma and mon.codigo_internacional=vfp.moneda and
                                                                                          lug.codigo=vfp.pais)
                                  where acc.id_apertura_cierre_caja = '||v_parametros.id_apertura_cierre_caja||'
                                  group by u.desc_persona,
                                           acc.fecha_apertura_cierre,
                                           acc.id_punto_venta,
                                           acc.id_usuario_cajero,
                                           acc.obs_cierre,
                                           acc.arqueo_moneda_local,
                                           acc.arqueo_moneda_extranjera,
                                           acc.monto_inicial,
                                           acc.monto_inicial_moneda_extranjera,
                                           v.pais,
                                           v.estacion,
                                           v.agt,
                                           v.razon_sucursal)
                                           )select desc_persona::varchar,
                                                   to_char::varchar,
                                                   pais::varchar,
                                                   estacion::varchar,
                                                   ''''::varchar as punto_venta,
                                                   obs_cierre::varchar,
                                                   arqueo_moneda_local::numeric,
                                                   arqueo_moneda_extranjera::numeric,
                                                   monto_inicial::numeric,
                                                   monto_inicial_moneda_extranjera::numeric,
                                                   tipo_cambio::numeric,
                                                   tiene_dos_monedas::varchar,
                                                   moneda_local::varchar,
                                                   moneda_extranjera::varchar,
                                                   cod_moneda_local::varchar,
                                                   cod_moneda_extranjera::varchar,
                                                   sum(efectivo_boletos_ml)::numeric as  efectivo_boletos_ml,
                                                   sum(efectivo_boletos_me)::numeric as  efectivo_boletos_me,
                                                   sum(tarjeta_boletos_ml)::numeric as tarjeta_boletos_ml,
                                                   sum(tarjeta_boletos_me)::numeric as tarjeta_boletos_me,
                                                   sum(cuenta_corriente_boletos_ml)::numeric as cuenta_corriente_boletos_ml,
                                                   sum(cuenta_corriente_boletos_me)::numeric as cuenta_corriente_boletos_me,
                                                   sum(mco_boletos_ml)::numeric as mco_boletos_ml,
                                                   sum(mco_boletos_me)::numeric as mco_boletos_me,
                                                   sum(otro_boletos_ml)::numeric as otro_boletos_ml,
                                                   sum(otro_boletos_me)::numeric as otro_boletos_me,
                                                   sum(efectivo_ventas_ml)::numeric as efectivo_ventas_ml,
                                                   sum(efectivo_ventas_me)::numeric as efectivo_ventas_me,
                                                   sum(tarjeta_ventas_ml)::numeric as tarjeta_ventas_ml,
                                                   sum(tarjeta_vetas_me)::numeric as tarjeta_vetas_me,
                                                   sum(cuenta_corriente_ventas_ml)::numeric as cuenta_corriente_ventas_ml,
                                                   sum(cuenta_corriente_ventas_me)::numeric as cuenta_corriente_ventas_me,
                                                   sum(mco_ventas_ml)::numeric as mco_ventas_ml,
                                                   sum(mco_ventas_me)::numeric as mco_ventas_me,
                                                   sum(otro_ventas_ml)::numeric as otro_ventas_ml,
                                                   sum(otro_ventas_me)::numeric as otro_ventas_me,
                                                   sum(comisiones_ml)::numeric as comisiones_ml,
                                                   sum(comisiones_me)::numeric as comisiones_me,
                                                   sum(monto_ca_recibo_ml)::numeric as monto_ca_recibo_ml,
                                                   sum(monto_cc_recibo_ml)::numeric as monto_cc_recibo_ml
                                            from
                                           total_ventas
                                           group by
                                           desc_persona,
                                           to_char,
                                           pais,
                                           estacion,
                                           obs_cierre,
                                           arqueo_moneda_local,
                                           arqueo_moneda_extranjera,
                                           monto_inicial,
                                           monto_inicial_moneda_extranjera,
                                           tipo_cambio,
                                           tiene_dos_monedas,
                                           moneda_local,
                                           moneda_extranjera,
                                           cod_moneda_local,
                                           cod_moneda_extranjera';


            END IF;

			--Definicion de la respuesta

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_APENTRE_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		MMV
 	#FECHA:
	***********************************/

	elsif(p_transaccion='VF_APENTRE_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						apcie.id_apertura_cierre_caja,
						apcie.id_punto_venta,
						apcie.id_usuario_cajero,
                        apcie.id_entrega_brinks,
                        apcie.id_usuario_reg,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        apcie.fecha_apertura_cierre,
                        pv.nombre as nombre_punto_venta,
                        COALESCE(apcie.arqueo_moneda_local,0) as arqueo_moneda_local ,
                        COALESCE(apcie.arqueo_moneda_extranjera,0) as arqueo_moneda_extranjera,
                        apcie.obs_cierre,
                        p.nombre as cajero
                        from vef.tapertura_cierre_caja apcie
						inner join segu.tusuario usu1 on usu1.id_usuario = apcie.id_usuario_reg
                        inner join segu.tusuario u on u.id_usuario = apcie.id_usuario_cajero
                        inner join segu.vpersona p on p.id_persona = u.id_persona
						left join segu.tusuario usu2 on usu2.id_usuario = apcie.id_usuario_mod
				        left join vef.tpunto_venta pv on pv.id_punto_venta = apcie.id_punto_venta
                        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'VF_APENTRE_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		MMV
 	#FECHA:
	***********************************/

	elsif(p_transaccion='VF_APENTRE_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_apertura_cierre_caja),
            					COALESCE(sum(apcie.arqueo_moneda_local),0)::numeric as arqueo_moneda_local_total,
								COALESCE(sum(apcie.arqueo_moneda_extranjera),0)::numeric as arqueo_moneda_extranjera_total
					    from vef.tapertura_cierre_caja apcie
						inner join segu.tusuario usu1 on usu1.id_usuario = apcie.id_usuario_reg
                        inner join segu.tusuario u on u.id_usuario = apcie.id_usuario_cajero
                        inner join segu.vpersona p on p.id_persona = u.id_persona
						left join segu.tusuario usu2 on usu2.id_usuario = apcie.id_usuario_mod
				        left join vef.tpunto_venta pv on pv.id_punto_venta = apcie.id_punto_venta
                        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'VF_CIE_CONT'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		28-09-2017
	***********************************/

	elsif(p_transaccion='VF_CIE_CONT')then

    	begin
        	v_consulta:='select count(apcie.id_apertura_cierre_caja)
                        from vef.tapertura_cierre_caja apcie
                        where apcie.id_usuario_cajero = '||p_id_usuario||' and
                              apcie.fecha_apertura_cierre = current_date and ';
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