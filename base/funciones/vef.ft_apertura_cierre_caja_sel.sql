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
    v_regitro				 	record;

    v_moneda_desc				varchar;
    v_fecha_for					varchar;
    v_monto_deposito_ml			numeric;
    v_monto_deposito_me			numeric;


    v_cash_extranjera			varchar;
    v_tarjetas_extranjera		varchar;
    v_cuenta_corriente_extranjera	varchar;
    v_ventas_mco_extranjera		varchar;
    v_ventas_otros_extranjera	varchar;
    v_recibo_cash_extranjera				varchar;
    v_tarjeta_recibo_extranjera	varchar;
    v_cuenta_corriente_recibo_extranjera	varchar;
    v_deposito_recibo_extranjera	varchar;
    v_otro_recibo_extranjera	varchar;
    v_pago_externo_recibo_extranjera	varchar;

    v_boletos_cash_extranjera				varchar;
    v_boletos_tarjetas_extranjera			varchar;
    v_boletos_cuenta_corriente_extranjera	varchar;
    v_boletos_mco_extranjera				varchar;
    v_boletos_otros_extranjera				varchar;

    v_insertar_facturas_ro					varchar;
    v_insertar_boletos						varchar;
    v_insertar_facturas_ro_auxiliar			varchar;

    v_tsucursal_usuario						record;
    v_tipo_usuario							varchar;
    v_id_punto_venta						integer;
    v_id_apertura_recuperar					integer;
    v_desc_cajero_principal					varchar;
    v_fecha_apertura_principal				varchar;
    v_ids_recuperar							record;

    v_monto_inicial							numeric;
    v_monto_inicial_me						numeric;
    v_observaciones							varchar;
    v_arqueo_ml								numeric;
    v_arqueo_me								numeric;

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
                        apcie.id_entrega_brinks,
                        pv.tipo,
                        initcap(u.desc_persona) as desc_persona,
                        apcie.modificado,

                        apcie.id_apertura_cierre_admin,
                        initcap(uadm.desc_persona)::varchar as desc_persona_adm

						from vef.tapertura_cierre_caja apcie
						inner join segu.tusuario usu1 on usu1.id_usuario = apcie.id_usuario_reg
                        inner join segu.vusuario u on u.id_usuario = apcie.id_usuario_cajero
						left join segu.tusuario usu2 on usu2.id_usuario = apcie.id_usuario_mod
				        left join vef.tpunto_venta pv on pv.id_punto_venta = apcie.id_punto_venta
                        left join vef.tsucursal suc on suc.id_sucursal = apcie.id_sucursal

                        left join vef.tapertura_cierre_caja accadm on accadm.id_apertura_cierre_caja = apcie.id_apertura_cierre_admin
                        left join segu.vusuario uadm on uadm.id_usuario = accadm.id_usuario_cajero

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
			v_consulta:='select count(apcie.id_apertura_cierre_caja)
					    from vef.tapertura_cierre_caja apcie
					    inner join segu.tusuario usu1 on usu1.id_usuario = apcie.id_usuario_reg
						inner join segu.vusuario u on u.id_usuario = apcie.id_usuario_cajero
						left join segu.tusuario usu2 on usu2.id_usuario = apcie.id_usuario_mod
					    left join vef.tpunto_venta pv on pv.id_punto_venta = apcie.id_punto_venta
                        left join vef.tsucursal suc on suc.id_sucursal = apcie.id_sucursal

                        left join vef.tapertura_cierre_caja accadm on accadm.id_apertura_cierre_caja = apcie.id_apertura_cierre_admin
                        left join segu.vusuario uadm on uadm.id_usuario = accadm.id_usuario_cajero

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

           /*********************VERIFICAMOS SI EXISTE EL TIPO DE CAMBIO*************************/
           IF (v_tipo_cambio is null) then

            	select  mon.codigo into v_moneda_desc
                from param.tmoneda mon
                where mon.id_moneda = v_id_moneda_tri;

                SELECT to_char(v_fecha,'DD/MM/YYYY') into v_fecha_for;

            	raise exception 'No se pudo recuperar el tipo de cambio para la moneda: % en fecha: %, comuniquese con personal de Contabilidad Ingresos.',v_moneda_desc,v_fecha_for;
            end if;
            /***************************************************************************************/

            /*Aumentando para regional MIAMI si no tiene una triangulacion*/
            IF (v_cod_moneda_extranjera is null) then
            	v_cod_moneda_extranjera = '';
            end if;

            IF (v_moneda_extranjera is null) then
            	v_moneda_extranjera = '';
            end if;


            IF (v_id_moneda_tri is not null) then

            	v_cash_extranjera = 'sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as efectivo_ventas_me';

                v_tarjetas_extranjera = 'sum(case  when fp.fop_code = ''CC'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as tarjeta_vetas_me';

                 v_cuenta_corriente_extranjera = 'sum(case  when (fp.fop_code = ''CUEC'' or fp.fop_code = ''CU'') and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as cuenta_corriente_ventas_me';

                 v_ventas_mco_extranjera = 'sum(case  when fp.fop_code = ''MCO'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as mco_ventas_me';

                  v_ventas_otros_extranjera = 'sum(case  when fp.fop_code like ''OTRO'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as otro_ventas_me';

                  v_recibo_cash_extranjera = ' sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') AND v.id_deposito is null then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as efectivo_recibo_me';

                  v_tarjeta_recibo_extranjera = 'sum(case  when fp.fop_code = ''CC'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as tarjeta_recibo_me';

                  v_cuenta_corriente_recibo_extranjera = 'sum(case  when (fp.fop_code = ''CUEC'' or fp.fop_code = ''CU'') and vfp.id_moneda = ' || v_id_moneda_tri  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is null)) then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as cuenta_corriente_recibo_me';

                 v_deposito_recibo_extranjera = 'sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is not null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is not null)) then
                                    vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                else
                                    0
                                end)as deposito_recibo_me';

                 v_otro_recibo_extranjera = 'sum(case  when fp.fop_code like ''OTRO'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as otro_recibo_me';

                 v_pago_externo_recibo_extranjera = 'sum(case  when fp.fop_code = ''EXT'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as pago_externo_recibo_me';

                 v_boletos_cash_extranjera = 'sum(case  when fpb.fop_code = ''CA'' and bfp.id_moneda = ' || v_id_moneda_tri  || ' then
                                                bfp.importe
                                            else
                                                0
                                            end)as efectivo_boletos_me';

                 v_boletos_tarjetas_extranjera = 'sum(case  when fpb.fop_code is null and b.forma_pago = ''CC'' and b.moneda = ''USD'' then
                                                      b.total
                                                  when fpb.fop_code = ''CC'' and bfp.id_moneda = ' || v_id_moneda_tri  || ' THEN
                                                      bfp.importe
                                            else
                                                0
                                            end)as tarjeta_boletos_me';

                 v_boletos_cuenta_corriente_extranjera = 'sum(case  when (fpb.fop_code = ''CUEC'' or fpb.fop_code = ''CU'') and bfp.id_moneda = ' || v_id_moneda_tri  || ' then
                                                              bfp.importe
                                                          else
                                                              0
                                                          end)as cuenta_corriente_boletos_me';

                 v_boletos_mco_extranjera = 'sum(case  when fpb.fop_code = ''MCO'' and bfp.id_moneda = ' || v_id_moneda_tri || ' then
                                                bfp.importe
                                            else
                                                0
                                            end)as mco_boletos_me';

                 v_boletos_otros_extranjera = 'sum(case  when fpb.fop_code not similar to ''(CA|CC|CUEC|MCO)%'' and bfp.id_moneda = ' || v_id_moneda_tri || ' then
                                                  bfp.importe
                                              else
                                                  0
                                              end)as otro_boletos_ml';


            else
            	v_cash_extranjera = '0::numeric as efectivo_ventas_me';
                v_tarjetas_extranjera = '0::numeric  as tarjeta_vetas_me';
                v_cuenta_corriente_extranjera = '0::numeric  as cuenta_corriente_ventas_me';
                v_ventas_mco_extranjera = '0::numeric  as mco_ventas_me';
                v_ventas_otros_extranjera = '0::numeric  as otro_ventas_me';
                v_recibo_cash_extranjera = ' 0::numeric  as efectivo_recibo_me';
                v_tarjeta_recibo_extranjera = '0::numeric  as tarjeta_recibo_me';
                v_cuenta_corriente_recibo_extranjera = '0::numeric  as cuenta_corriente_recibo_me';
                v_deposito_recibo_extranjera = '0::numeric  as deposito_recibo_me';
                v_otro_recibo_extranjera = '0::numeric  as otro_recibo_me';
                v_pago_externo_recibo_extranjera = '0::numeric  as pago_externo_recibo_me';

                v_boletos_cash_extranjera = '0::numeric  as efectivo_boletos_me';
                v_boletos_tarjetas_extranjera = '0::numeric  as tarjeta_boletos_me';
                v_boletos_cuenta_corriente_extranjera = '0::numeric  as cuenta_corriente_boletos_me';
                v_boletos_mco_extranjera = '0::numeric  as mco_boletos_me';
                v_boletos_otros_extranjera = '0::numeric  as otro_boletos_ml';

            end if;

            /**************************************************************/

    	/*Inicio Condicion para usar las nuevas instancias de pago tanto boletos como ventas*/

        /*Cambiando por la fecha de apertura*/
    	--IF(pxp.f_get_variable_global('instancias_de_pago_nuevas') = 'no')THEN
        IF(v_fecha <= '31/12/2020')THEN
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
                      select u.desc_persona::varchar,
                      to_char(acc.fecha_apertura_cierre,''DD/MM/YYYY'')::varchar,
                      coalesce(ppv.codigo,ps.codigo)::varchar as pais,
                      COALESCE(lpv.codigo,ls.codigo)::varchar as estacion,
                      coalesce(pv.codigo, s.codigo)::varchar as punto_venta,
                      acc.obs_cierre::varchar,
                      acc.arqueo_moneda_local,
                      acc.arqueo_moneda_extranjera,
                      acc.monto_inicial,
                      acc.monto_inicial_moneda_extranjera,
                      ' || v_tipo_cambio || '::numeric as tipo_cambio,
                      ''' || v_tiene_dos_monedas || '''::varchar as tiene_dos_monedas,
                      ''' || v_moneda_local || '''::varchar as moneda_local,
                      ''' || v_moneda_extranjera || '''::varchar as moneda_extranjera,
                       ''' || v_cod_moneda_local || '''::varchar as cod_moneda_local,
                       ''' || v_cod_moneda_extranjera || '''::varchar as cod_moneda_extranjera,
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


                      sum(case  when fp2.codigo = ''CASH'' and fp2.id_moneda = ' || v_id_moneda_base  || ' and v.tipo_factura <> ''recibo'' then
                              vfp.monto_mb_efectivo
                          else
                              0
                          end)as efectivo_ventas_ml,
                      sum(case  when fp2.codigo = ''CASH'' and fp2.id_moneda = ' || v_id_moneda_tri  || ' and v.tipo_factura <> ''recibo'' then
                              vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                          else
                              0
                          end)as efectivo_ventas_me,
                      sum(case  when fp2.codigo = ''CC'' and fp2.id_moneda = ' || v_id_moneda_base  || ' and v.tipo_factura <> ''recibo'' then
                              vfp.monto_mb_efectivo
                          else
                              0
                          end)as tarjeta_ventas_ml,
                      sum(case  when fp2.codigo = ''CC'' and fp2.id_moneda = ' || v_id_moneda_tri  || ' and v.tipo_factura <> ''recibo'' then
                              vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                          else
                              0
                          end)as tarjeta_vetas_me,
                      sum(case  when fp2.codigo = ''CT'' and fp2.id_moneda = ' || v_id_moneda_base  || ' and v.tipo_factura <> ''recibo'' then
                              vfp.monto_mb_efectivo
                          else
                              0
                          end)as cuenta_corriente_ventas_ml,
                      sum(case  when fp2.codigo = ''CT'' and fp2.id_moneda = ' || v_id_moneda_tri  || ' and v.tipo_factura <> ''recibo'' then
                              vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                          else
                              0
                          end)as cuenta_corriente_ventas_me,
                      sum(case  when fp2.codigo = ''MCO'' and fp2.id_moneda = ' || v_id_moneda_base  || ' and v.tipo_factura <> ''recibo'' then
                              vfp.monto_mb_efectivo
                          else
                              0
                          end)as mco_ventas_ml,
                      sum(case  when fp2.codigo = ''MCO'' and fp2.id_moneda = ' || v_id_moneda_tri  || ' and v.tipo_factura <> ''recibo'' then
                              vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                          else
                              0
                          end)as mco_ventas_me,
                      sum(case  when fp2.codigo = ''OTRO'' and fp2.id_moneda = ' || v_id_moneda_base  || ' and v.tipo_factura <> ''recibo'' then
                              vfp.monto_mb_efectivo
                          else
                              0
                          end)as otro_ventas_ml,
                      sum(case  when fp2.codigo like ''OTRO'' and fp2.id_moneda = ' || v_id_moneda_tri  || ' and v.tipo_factura <> ''recibo'' then
                              vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                          else
                              0
                          end)as otro_ventas_me,

                      /**************************Aumentando la condicion**************/

                      sum(case  when fp_reci.codigo = ''CASH'' and fp_reci.id_moneda = ' || v_id_moneda_base  || ' and v.tipo_factura = ''recibo'' then
                              vfp.monto_mb_efectivo
                          else
                              0
                          end)as efectivo_recibo_ml,
                      sum(case  when fp_reci.codigo = ''CASH'' and fp_reci.id_moneda = ' || v_id_moneda_tri  || ' and v.tipo_factura = ''recibo'' then
                              vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                          else
                              0
                          end)as efectivo_recibo_me,
                      sum(case  when fp_reci.codigo = ''CC'' and fp_reci.id_moneda = ' || v_id_moneda_base  || ' and v.tipo_factura = ''recibo'' then
                              vfp.monto_mb_efectivo
                          else
                              0
                          end)as tarjeta_recibo_ml,
                      sum(case  when fp_reci.codigo = ''CC'' and fp_reci.id_moneda = ' || v_id_moneda_tri  || ' and v.tipo_factura = ''recibo'' then
                              vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                          else
                              0
                          end)as tarjeta_recibo_me,
                      sum(case  when fp_reci.codigo = ''CT'' and fp_reci.id_moneda =' || v_id_moneda_base  || ' and v.tipo_factura = ''recibo'' then
                              vfp.monto_mb_efectivo
                          else
                              0
                          end)as cuenta_corriente_recibo_ml,
                      sum(case  when fp_reci.codigo = ''CT'' and fp_reci.id_moneda = ' || v_id_moneda_tri  || ' and v.tipo_factura = ''recibo'' then
                              vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                          else
                              0
                          end)as cuenta_corriente_recibo_me,
                      /***********************Despositos***************************/
                      0::numeric as deposito_recibo_ml,
                      0::numeric as deposito_recibo_me,
                      /**************************************/
                      sum(case  when fp_reci.codigo = ''OTRO'' and fp_reci.id_moneda = ' || v_id_moneda_base  || ' and v.tipo_factura = ''recibo'' then
                              vfp.monto_mb_efectivo
                          else
                              0
                          end)as otro_recibo_ml,

                      sum(case  when fp_reci.codigo like ''OTRO'' and fp_reci.id_moneda = ' || v_id_moneda_tri  || ' and v.tipo_factura = ''recibo'' then
                              vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                          else
                              0
                          end)as otro_recibo_me,

                     /***********************Pago Externo***************************/
                      0::numeric as pago_externo_ml,
                      0::numeric as pago_externo_me,
                      /**************************************/

                      /************************************************************************************************/


                      COALESCE((	select sum(ven.comision) from vef.tventa ven
                          where coalesce(ven.comision,0) > 0 and ven.id_moneda = ' || v_id_moneda_base  || ' and
                                  ven.fecha = acc.fecha_apertura_cierre and ven.id_punto_venta= acc.id_punto_venta
                                  and ven.id_usuario_cajero = acc.id_usuario_cajero and
                                  ven.estado = ''finalizado''),0) +

                      COALESCE((	select sum(bol.comision) from obingresos.tboleto_amadeus bol
                          where coalesce(bol.comision,0) > 0 and bol.id_moneda_boleto = ' || v_id_moneda_base  || ' and
                                  bol.fecha_emision = acc.fecha_apertura_cierre and bol.id_punto_venta=acc.id_punto_venta
                                  and bol.id_usuario_cajero = acc.id_usuario_cajero and
                                  bol.estado = ''revisado'' and bol.voided != ''si''),0) as comisiones_ml,

                      COALESCE((	select sum(ven.comision) from vef.tventa ven
                          where coalesce(ven.comision,0) > 0 and ven.id_moneda = ' || v_id_moneda_tri  || ' and
                                  ven.fecha = acc.fecha_apertura_cierre and ven.id_punto_venta=acc.id_punto_venta
                                  and ven.id_usuario_cajero = acc.id_usuario_cajero and
                                  ven.estado = ''finalizado''),0) +

                      COALESCE((	select sum(bol.comision) from obingresos.tboleto_amadeus bol
                          where coalesce(bol.comision,0) > 0 and bol.id_moneda_boleto = ' || v_id_moneda_tri  || ' and
                                  bol.fecha_emision = acc.fecha_apertura_cierre and bol.id_punto_venta= acc.id_punto_venta
                                   and bol.id_usuario_cajero = acc.id_usuario_cajero and
                                  bol.estado = ''revisado'' and bol.voided != ''si''),0)   as comisiones_me,
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
                                                      and b.fecha_emision::date = acc.fecha_apertura_cierre and
                                                      b.id_punto_venta = acc.id_punto_venta and b.estado = ''revisado''
													  and b.voided=''no''
                      left join obingresos.tboleto_amadeus_forma_pago bfp on bfp.id_boleto_amadeus = b.id_boleto_amadeus
                      left join forma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                      left join vef.tventa v on v.id_usuario_cajero = u.id_usuario
                                                      and v.fecha = acc.fecha_apertura_cierre and
                                                      v.id_punto_venta = acc.id_punto_venta and v.estado = ''finalizado''

                      left join vef.tventa_forma_pago vfp on vfp.id_venta = v.id_venta
                      left join forma_pago fp2 on fp2.id_forma_pago = vfp.id_forma_pago

                      /*-----------------Aumentando condicion para recuperar los recibos-----------------*/
                      left join forma_pago fp_reci on fp_reci.id_forma_pago = vfp.id_forma_pago
                      /**********************************************************************************/

                      where acc.id_apertura_cierre_caja = ' || v_parametros.id_apertura_cierre_caja  || '
                      group by u.desc_persona, acc.fecha_apertura_cierre,
                      ppv.codigo,ps.codigo,lpv.codigo,ls.codigo,
                      pv.codigo , pv.nombre, s.codigo ,s.nombre,acc.id_punto_venta,
                      acc.id_usuario_cajero,acc.obs_cierre, acc.arqueo_moneda_local,
                      acc.arqueo_moneda_extranjera,acc.monto_inicial,acc.monto_inicial_moneda_extranjera,
                      acc.monto_ca_recibo_ml, acc.monto_cc_recibo_ml';



           IF(pxp.f_get_variable_global('vef_facturacion_endesis') ='true')THEN

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
                                         v.agt::varchar as punto_venta,
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


                                            0 as efectivo_recibo_ml,
                                            0 as efectivo_recibo_me,
                                            0 as tarjeta_recibo_ml,
                                            0 as tarjeta_recibo_me,

                                            0 as cuenta_corriente_recibo_ml,
                                            0 as cuenta_corriente_recibo_me,
                                            0 as deposito_recibo_ml,
                                            0 as deposito_recibo_me,
                                            0 as otro_recibo_ml,
                                            0 as otro_recibo_me,

                                            0 as pago_externo_ml,
                      						0 as pago_externo_me,


                      /************************************************************************************************/

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
                                   /*
                                   /*-----------------Aumentando condicion para recuperar los recibos-----------------*/
                                      left join vef.tventa v_rec on v_rec.id_usuario_cajero = u.id_usuario
                                                      and v_rec.fecha = acc.fecha_apertura_cierre and
                                                      v_rec.id_punto_venta = acc.id_punto_venta and v_rec.estado = ''finalizado''
                      				 left join vef.tventa_forma_pago vfp_rec on vfp_rec.id_venta = v_rec.id_venta
                                     left join forma_pago fp_reci on fp_reci.id_forma_pago = vfp_rec.id_forma_pago
                                      /**********************************************************************************/
                                      */

                                  where acc.id_apertura_cierre_caja = '||v_parametros.id_apertura_cierre_caja||'
                                  and v.sw_excluir=''no''
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
                                                   punto_venta::varchar as punto_venta,
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

                                                   sum(efectivo_recibo_ml)::numeric as efectivo_recibo_ml,
                                            sum(efectivo_recibo_me)::numeric as efectivo_recibo_me,
                                            sum(tarjeta_recibo_ml)::numeric as tarjeta_recibo_ml,
                                            sum(tarjeta_recibo_me)::numeric as tarjeta_recibo_me,

                                            sum(cuenta_corriente_recibo_ml)::numeric as cuenta_corriente_recibo_ml,
                                            sum(cuenta_corriente_recibo_me)::numeric as cuenta_corriente_recibo_me,
                                            sum(deposito_recibo_ml)::numeric as deposito_recibo_ml,
                                            sum(deposito_recibo_me)::numeric as deposito_recibo_me,
                                            sum(otro_recibo_ml)::numeric as otro_recibo_ml,
                                            sum(otro_recibo_me)::numeric as otro_recibo_me,

                                            sum(pago_externo_ml)::numeric as pago_externo_ml,
                                            sum(pago_externo_me)::numeric as pago_externo_me,

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
                                           punto_venta,
                                           tipo_cambio,
                                           tiene_dos_monedas,
                                           moneda_local,
                                           moneda_extranjera,
                                           cod_moneda_local,
                                           cod_moneda_extranjera';


                	END IF;
                ELSE

                /*SEPARANDO LAS CONSULTAS PARA OBTENER TOTALES EXACTOS (01/01/2021)*/


                /*CONSULTA PARA LOS BOLETOS*/
                v_insertar_boletos = '
                insert into temporal_cierre ( desc_persona,
                                          fecha_apertura_cierre,
                                          pais,
                                          estacion,
                                          punto_venta,
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
                                          cod_moneda_extranjera,
                                          efectivo_boletos_ml,
                                          boletos_cash_extranjera, --
                                          tarjeta_boletos_ml,
                                          boletos_tarjetas_extranjera, --
                                          cuenta_corriente_boletos_ml,
                                          boletos_cuenta_corriente_extranjera, --
                                          mco_boletos_ml,
                                          boletos_mco_extranjera, --
                                          otro_boletos_ml,
                                          boletos_otros_extranjera, --
                                          efectivo_ventas_ml,
                                          cash_extranjera, --
                                          tarjeta_ventas_ml,
                                          tarjetas_extranjera, --
                                          cuenta_corriente_ventas_ml,
                                          cuenta_corriente_extranjera, --
                                          mco_ventas_ml,
                                          ventas_mco_extranjera, --
                                          otro_ventas_ml,
                                          ventas_otros_extranjera, --
                                          efectivo_recibo_ml,
                                          recibo_cash_extranjera, --
                                          tarjeta_recibo_ml,
                                          tarjeta_recibo_extranjera, --
                                          cuenta_corriente_recibo_ml,
                                          cuenta_corriente_recibo_extranjera, --
                                          deposito_recibo_ml,
                                          deposito_recibo_extranjera, --
                                          otro_recibo_ml,
                                          otro_recibo_extranjera, --
                                          pago_externo_recibo_ml,
                                          pago_externo_recibo_extranjera, --
                                          comisiones_ml,
                                          comisiones_me,
                                          monto_ca_recibo_ml,
                                          monto_cc_recibo_ml)

                select  u.desc_persona::varchar,
                              to_char(acc.fecha_apertura_cierre,''DD/MM/YYYY'')::varchar,
                              coalesce(ppv.codigo,ps.codigo)::varchar as pais,
                              COALESCE(lpv.codigo,ls.codigo)::varchar as estacion,
                              coalesce(pv.codigo, s.codigo)::varchar as punto_venta,
                              COALESCE (acc.obs_cierre,'''')::varchar as obs_cierre,
                              COALESCE (acc.arqueo_moneda_local,0) as arqueo_moneda_local,
                              COALESCE (acc.arqueo_moneda_extranjera,0) as arqueo_moneda_extranjera,
                              acc.monto_inicial,
                              acc.monto_inicial_moneda_extranjera,
                              ' || v_tipo_cambio || '::numeric as tipo_cambio,
                              ''' || v_tiene_dos_monedas || '''::varchar as tiene_dos_monedas,
                              ''' || v_moneda_local || '''::varchar as moneda_local,
                              ''' || v_moneda_extranjera || '''::varchar as moneda_extranjera,
                               ''' || v_cod_moneda_local || '''::varchar as cod_moneda_local,
                               ''' || v_cod_moneda_extranjera || '''::varchar as cod_moneda_extranjera,

                              /*Aqui debemos recuperar los boletos queda pendiente*/



                              sum(case  when fpb.fop_code = ''CA'' and bfp.id_moneda = ' || v_id_moneda_base  || ' then
                                      bfp.importe
                                  else
                                      0
                                  end)as efectivo_boletos_ml,

                              '||v_boletos_cash_extranjera||',

                              sum(case  when fpb.fop_code is null and b.forma_pago = ''CC'' and b.moneda = (select mon.codigo_internacional from param.tmoneda mon where mon.tipo_moneda = ''base'') then
                                      		b.total
                                      	when fpb.fop_code = ''CC'' and bfp.id_moneda = ' || v_id_moneda_base  || ' THEN
                                      		bfp.importe
                                  else
                                      0
                                  end)as tarjeta_boletos_ml,

                              '||v_boletos_tarjetas_extranjera||',


                              sum(case  when (fpb.fop_code = ''CUEC'' or fpb.fop_code = ''CU'') and bfp.id_moneda = ' || v_id_moneda_base  || ' then
                                      bfp.importe
                                  else
                                      0
                                  end)as cuenta_corriente_boletos_ml,


                              '||v_boletos_cuenta_corriente_extranjera||',


                              sum(case  when fpb.fop_code = ''MCO'' and bfp.id_moneda = ' || v_id_moneda_base || ' then
                                      bfp.importe
                                  else
                                      0
                                  end)as mco_boletos_ml,

                              '||v_boletos_mco_extranjera||',


                              sum(case  when fpb.fop_code not similar to ''(CA|CC|CUEC|MCO)%'' and bfp.id_moneda = ' || v_id_moneda_base || ' then
                                      bfp.importe
                                  else
                                      0
                                  end)as otro_boletos_ml,

                              '||v_boletos_otros_extranjera||',

                              /****************************************************/

                              /******************************************************Recuperamos datos para facturas******************************************************/
                              /*Inicio recuperacion facturas*/
                              0::numeric as efectivo_ventas_ml,

                              0::numeric as efectivo_ventas_me,

                              0::numeric as tarjeta_ventas_ml,

                              0::numeric as tarjeta_ventas_me,

                              0::numeric as cuenta_corriente_ventas_ml,

                              0::numeric as cuenta_corriente_ventas_me,

                              0::numeric as mco_ml,

                              0::numeric as mco_me,

                              0::numeric as otro_ventas_ml,

                              0::numeric as otro_ventas_me,

                              /*Fin recuperacion facturas*/
                              /*****************************************************************************************************************************************/

                              /*Recuperamos datos para Recibos*/
                              /*Inicio recuperacion recibos*/

                              0::numeric as efectivo_recibo_ml,

                             	0::numeric as efectivo_recibo_ml,

                              0::numeric as tarjeta_recibo_ml,

                              0::numeric as tarjeta_recibo_me,

                              0::numeric as cuenta_corriente_recibo_ml,

                              0::numeric as cuenta_corriente_recibo_me,

                              /***********************************************Aqui Recuperamos los depositos asociados al Recibo****************************************************/
                               0::numeric as deposito_recibo_ml,

                             0::numeric as deposito_recibo_me,
                      		  /******************************************************************************************************************************************************/

                              0::numeric as otro_recibo_ml,

                              0::numeric as otro_recibo_me,

                              0::numeric as pago_externo_recibo_ml,

                              0::numeric as pago_externo_recibo_me,
                              /*************************************************************************/

                              /*Fin recuperacion recibos*/
                              /************************************************************************************************/


                              COALESCE((	select sum(bol.comision) from obingresos.tboleto_amadeus bol
                                  where coalesce(bol.comision,0) > 0 and bol.id_moneda_boleto = 1 and
                                          bol.fecha_emision = acc.fecha_apertura_cierre and bol.id_punto_venta=acc.id_punto_venta
                                          and bol.id_usuario_cajero = acc.id_usuario_cajero and
                                          bol.estado = ''revisado'' and bol.voided != ''si''),0) as comisiones_ml,



                              COALESCE((	select sum(bol.comision) from obingresos.tboleto_amadeus bol
                                  where coalesce(bol.comision,0) > 0 and bol.id_moneda_boleto = 2 and
                                          bol.fecha_emision = acc.fecha_apertura_cierre and bol.id_punto_venta= acc.id_punto_venta
                                           and bol.id_usuario_cajero = acc.id_usuario_cajero and
                                          bol.estado = ''revisado'' and bol.voided != ''si''),0)   as comisiones_me,

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


                      /*Recuperamos los Boletos de Amadeus con los nuevos medios de pago (Ismael Valdivia 26/11/2020)*/
                      left join obingresos.tboleto_amadeus b on b.id_usuario_cajero = u.id_usuario
                                                      and b.fecha_emision::date = acc.fecha_apertura_cierre and
                                                      b.id_punto_venta = acc.id_punto_venta and b.estado = ''revisado''
													  and b.voided=''no''

                      left join obingresos.tboleto_amadeus_forma_pago bfp on bfp.id_boleto_amadeus = b.id_boleto_amadeus

                      left join obingresos.tmedio_pago_pw mpb on mpb.id_medio_pago_pw = bfp.id_medio_pago
                      left join obingresos.tforma_pago_pw fpb on fpb.id_forma_pago_pw = mpb.forma_pago_id
                      /***********************************************************************************************/

                      where acc.id_apertura_cierre_caja = ' || v_parametros.id_apertura_cierre_caja  || '
                      group by u.desc_persona, acc.fecha_apertura_cierre,
                      ppv.codigo,ps.codigo,lpv.codigo,ls.codigo,
                      pv.codigo , pv.nombre, s.codigo ,s.nombre,acc.id_punto_venta,
                      acc.id_usuario_cajero,acc.obs_cierre, acc.arqueo_moneda_local,
                      acc.arqueo_moneda_extranjera,acc.monto_inicial,acc.monto_inicial_moneda_extranjera,
                      acc.monto_ca_recibo_ml, acc.monto_cc_recibo_ml';

                /*******************************************************************/

                 v_insertar_facturas_ro = '

                 insert into temporal_cierre ( desc_persona,
                                          fecha_apertura_cierre,
                                          pais,
                                          estacion,
                                          punto_venta,
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
                                          cod_moneda_extranjera,
                                          efectivo_boletos_ml,
                                          boletos_cash_extranjera, --
                                          tarjeta_boletos_ml,
                                          boletos_tarjetas_extranjera, --
                                          cuenta_corriente_boletos_ml,
                                          boletos_cuenta_corriente_extranjera, --
                                          mco_boletos_ml,
                                          boletos_mco_extranjera, --
                                          otro_boletos_ml,
                                          boletos_otros_extranjera, --
                                          efectivo_ventas_ml,
                                          cash_extranjera, --
                                          tarjeta_ventas_ml,
                                          tarjetas_extranjera, --
                                          cuenta_corriente_ventas_ml,
                                          cuenta_corriente_extranjera, --
                                          mco_ventas_ml,
                                          ventas_mco_extranjera, --
                                          otro_ventas_ml,
                                          ventas_otros_extranjera, --
                                          efectivo_recibo_ml,
                                          recibo_cash_extranjera, --
                                          tarjeta_recibo_ml,
                                          tarjeta_recibo_extranjera, --
                                          cuenta_corriente_recibo_ml,
                                          cuenta_corriente_recibo_extranjera, --
                                          deposito_recibo_ml,
                                          deposito_recibo_extranjera, --
                                          otro_recibo_ml,
                                          otro_recibo_extranjera, --
                                          pago_externo_recibo_ml,
                                          pago_externo_recibo_extranjera, --
                                          comisiones_ml,
                                          comisiones_me,
                                          monto_ca_recibo_ml,
                                          monto_cc_recibo_ml)

                 select  u.desc_persona::varchar,
                              to_char(acc.fecha_apertura_cierre,''DD/MM/YYYY'')::varchar,
                              coalesce(ppv.codigo,ps.codigo)::varchar as pais,
                              COALESCE(lpv.codigo,ls.codigo)::varchar as estacion,
                              coalesce(pv.codigo, s.codigo)::varchar as punto_venta,
                              COALESCE (acc.obs_cierre,'''')::varchar as obs_cierre,
                              COALESCE (acc.arqueo_moneda_local,0) as arqueo_moneda_local,
                              COALESCE (acc.arqueo_moneda_extranjera,0) as arqueo_moneda_extranjera,
                              acc.monto_inicial,
                              acc.monto_inicial_moneda_extranjera,
                              ' || v_tipo_cambio || '::numeric as tipo_cambio,
                              ''' || v_tiene_dos_monedas || '''::varchar as tiene_dos_monedas,
                              ''' || v_moneda_local || '''::varchar as moneda_local,
                              ''' || v_moneda_extranjera || '''::varchar as moneda_extranjera,
                               ''' || v_cod_moneda_local || '''::varchar as cod_moneda_local,
                               ''' || v_cod_moneda_extranjera || '''::varchar as cod_moneda_extranjera,

                              /*Aqui debemos recuperar los boletos queda pendiente*/

                              0::numeric ,
                              0::numeric,
                              0::numeric,
                              0::numeric,
                              0::numeric,
                              0::numeric,
                              0::numeric,
                              0::numeric,
                              0::numeric,
                              0::numeric,
                              /****************************************************/

                              /******************************************************Recuperamos datos para facturas******************************************************/
                              /*Inicio recuperacion facturas*/
                              sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as efectivo_ventas_ml,

                              '||v_cash_extranjera||',

                              sum(case  when fp.fop_code = ''CC'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as tarjeta_ventas_ml,

                              '||v_tarjetas_extranjera||',

                              sum(case  when (fp.fop_code = ''CUEC'' or fp.fop_code = ''CU'') and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as cuenta_corriente_ventas_ml,

                              '||v_cuenta_corriente_extranjera||',

                              sum(case  when fp.fop_code = ''MCO'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as mco_ventas_ml,

                              '||v_ventas_mco_extranjera||',

                              sum(case  when fp.fop_code not similar to ''(CA|CC|CUEC|MCO)%'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as otro_ventas_ml,

                              '||v_ventas_otros_extranjera||',

                              /*Fin recuperacion facturas*/
                              /*****************************************************************************************************************************************/

                              /*Recuperamos datos para Recibos*/
                              /*Inicio recuperacion recibos*/

                              sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') AND v.id_deposito is null  then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as efectivo_recibo_ml,

                             '||v_recibo_cash_extranjera||',

                              sum(case  when fp.fop_code = ''CC'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as tarjeta_recibo_ml,

                              '||v_tarjeta_recibo_extranjera||',

                              sum(case  when (fp.fop_code = ''CUEC'' or fp.fop_code = ''CU'') and vfp.id_moneda = ' || v_id_moneda_base  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is null)) then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as cuenta_corriente_recibo_ml,

                              '||v_cuenta_corriente_recibo_extranjera||',

                              /***********************************************Aqui Recuperamos los depositos asociados al Recibo****************************************************/
                               sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is not null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is not null)) then
                                    vfp.monto_mb_efectivo
                                else
                                    0
                                end) as deposito_recibo_ml,

                             '||v_deposito_recibo_extranjera||',
                      		  /******************************************************************************************************************************************************/

                              sum(case  when fp.fop_code not similar to ''(CA|CC|CUEC|MCO)%'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as otro_recibo_ml,

                              '||v_otro_recibo_extranjera||',

                              /*Aumentando para recuperar pago electronico (Ismael Valdivia 12/11/2020)*/
                              sum(case  when fp.fop_code = ''EXT'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is null)) then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as pago_externo_recibo_ml,

                              '||v_pago_externo_recibo_extranjera||',
                              /*************************************************************************/

                              /*Fin recuperacion recibos*/
                              /************************************************************************************************/
                              COALESCE((	select sum(ven.comision) from vef.tventa ven
                                  where coalesce(ven.comision,0) > 0 and ven.id_moneda = 1 and
                                          ven.fecha = acc.fecha_apertura_cierre and ven.id_punto_venta= acc.id_punto_venta
                                          and ven.id_usuario_cajero = acc.id_usuario_cajero and
                                          ven.estado = ''finalizado'' and ven.estado_reg = ''activo''),0) as comisiones_ml,

                              COALESCE((	select sum(ven.comision) from vef.tventa ven
                                  where coalesce(ven.comision,0) > 0 and ven.id_moneda = 2 and
                                          ven.fecha = acc.fecha_apertura_cierre and ven.id_punto_venta=acc.id_punto_venta
                                          and ven.id_usuario_cajero = acc.id_usuario_cajero and
                                          ven.estado = ''finalizado'' and ven.estado_reg = ''activo''),0) as comisiones_me,

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
                      left join vef.tventa v on v.id_usuario_cajero = u.id_usuario
                                                      and v.fecha = acc.fecha_apertura_cierre and
                                                      v.id_punto_venta = acc.id_punto_venta and v.estado = ''finalizado''
                                                      and v.estado_reg = ''activo''

                      left join vef.tventa_forma_pago vfp on vfp.id_venta = v.id_venta

                      /*********************************Aumentando para recuperar Instancias de pago**********************************/
                      left join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = vfp.id_medio_pago
                      left join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
                      /***************************************************************************************************************/

                      where acc.id_apertura_cierre_caja = ' || v_parametros.id_apertura_cierre_caja  || '
                      group by u.desc_persona, acc.fecha_apertura_cierre,
                      ppv.codigo,ps.codigo,lpv.codigo,ls.codigo,
                      pv.codigo , pv.nombre, s.codigo ,s.nombre,acc.id_punto_venta,
                      acc.id_usuario_cajero,acc.obs_cierre, acc.arqueo_moneda_local,
                      acc.arqueo_moneda_extranjera,acc.monto_inicial,acc.monto_inicial_moneda_extranjera,
                      acc.monto_ca_recibo_ml, acc.monto_cc_recibo_ml';


                 create  temp table temporal_cierre (
                                                  desc_persona varchar ,
                                                  fecha_apertura_cierre varchar,
                                                  pais varchar,
                                                  estacion varchar,
                                                  punto_venta varchar,
                                                  obs_cierre varchar,
                                                  arqueo_moneda_local numeric,
                                                  arqueo_moneda_extranjera numeric,
                                                  monto_inicial numeric,
                                                  monto_inicial_moneda_extranjera numeric,
                                                  tipo_cambio numeric,
                                                  tiene_dos_monedas varchar,
                                                  moneda_local varchar,
                                                  moneda_extranjera varchar,
                                                  cod_moneda_local varchar,
                                                  cod_moneda_extranjera varchar,
                                                  efectivo_boletos_ml numeric,
                                                  boletos_cash_extranjera numeric,
                                                  tarjeta_boletos_ml numeric,
                                                  boletos_tarjetas_extranjera numeric,
                                                  cuenta_corriente_boletos_ml numeric,
                                                  boletos_cuenta_corriente_extranjera numeric,
                                                  mco_boletos_ml numeric,
                                                  boletos_mco_extranjera numeric,
                                                  otro_boletos_ml numeric,
                                                  boletos_otros_extranjera numeric,
                                                  efectivo_ventas_ml numeric,
                                                  cash_extranjera numeric,
                                                  tarjeta_ventas_ml numeric,
                                                  tarjetas_extranjera numeric,
                                                  cuenta_corriente_ventas_ml numeric,
                                                  cuenta_corriente_extranjera numeric,
                                                  mco_ventas_ml numeric,
                                                  ventas_mco_extranjera numeric,
                                                  otro_ventas_ml numeric,
                                                  ventas_otros_extranjera numeric,
                                                  efectivo_recibo_ml numeric,
                                                  recibo_cash_extranjera numeric,
                                                  tarjeta_recibo_ml numeric,
                                                  tarjeta_recibo_extranjera numeric,
                                                  cuenta_corriente_recibo_ml numeric,
                                                  cuenta_corriente_recibo_extranjera numeric,
                                                  deposito_recibo_ml numeric,
                                                  deposito_recibo_extranjera numeric,
                                                  otro_recibo_ml numeric,
                                                  otro_recibo_extranjera numeric,
                                                  pago_externo_recibo_ml numeric,
                                                  pago_externo_recibo_extranjera numeric,
                                                  comisiones_ml numeric,
                                                  comisiones_me numeric,
                                                  monto_ca_recibo_ml numeric,
                                                  monto_cc_recibo_ml numeric
                                                      )  on commit drop;

                execute v_insertar_boletos;
                execute v_insertar_facturas_ro;


				/*Aqui Aumentaremos para que recupere datos del Cajero Auxiliar (Ismael Valdivia 08/02/2021)*/
				  select ap.id_apertura_cierre_caja
                  		into
                        v_id_apertura_recuperar
                  from vef.tapertura_cierre_caja ap
                  where ap.id_apertura_cierre_admin = v_parametros.id_apertura_cierre_caja;

                  /*Recuperamos los datos del Cajero Principal*/

                  if (v_id_apertura_recuperar is not null) then

                  	select  u.desc_persona::varchar,
                    	    to_char(acc.fecha_apertura_cierre,'DD/MM/YYYY')::varchar,
                            acc.obs_cierre::varchar,
                            acc.arqueo_moneda_local,
                            acc.arqueo_moneda_extranjera,
                            acc.monto_inicial,
                            acc.monto_inicial_moneda_extranjera
                    		into
                            v_desc_cajero_principal,
                            v_fecha_apertura_principal,
                            v_observaciones,
                            v_arqueo_ml,
                            v_arqueo_me,
                            v_monto_inicial,
                            v_monto_inicial_me
                      from vef.tapertura_cierre_caja acc
                      inner join segu.vusuario u on u.id_usuario = acc.id_usuario_cajero
                      left join vef.tsucursal s on acc.id_sucursal = s.id_sucursal
                      left join vef.tpunto_venta pv on pv.id_punto_venta = acc.id_punto_venta
                      left join vef.tsucursal spv on spv.id_sucursal = pv.id_sucursal
                      left join param.tlugar lpv on lpv.id_lugar = spv.id_lugar
                      left join param.tlugar ls on ls.id_lugar = s.id_lugar
                      left join param.tlugar ppv on ppv.id_lugar = param.f_get_id_lugar_pais(lpv.id_lugar)
                      left join param.tlugar ps on ps.id_lugar = param.f_get_id_lugar_pais(ls.id_lugar)
                      left join vef.tventa v on v.id_usuario_cajero = u.id_usuario
                                                      and v.fecha = acc.fecha_apertura_cierre and
                                                      v.id_punto_venta = acc.id_punto_venta and v.estado = 'finalizado'

                      left join vef.tventa_forma_pago vfp on vfp.id_venta = v.id_venta

                      /*********************************Aumentando para recuperar Instancias de pago**********************************/
                      left join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = vfp.id_medio_pago
                      left join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
                      /***************************************************************************************************************/

                      where acc.id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja
                      group by u.desc_persona, acc.fecha_apertura_cierre,
                      ppv.codigo,ps.codigo,lpv.codigo,ls.codigo,
                      pv.codigo , pv.nombre, s.codigo ,s.nombre,acc.id_punto_venta,
                      acc.id_usuario_cajero,acc.obs_cierre, acc.arqueo_moneda_local,
                      acc.arqueo_moneda_extranjera,acc.monto_inicial,acc.monto_inicial_moneda_extranjera,
                      acc.monto_ca_recibo_ml, acc.monto_cc_recibo_ml;

                      /*Aqui recuperamos los datos del cajero Auxiliar (Ismael Valdivia 08/02/2021)*/
                      for v_ids_recuperar in (select ap.id_apertura_cierre_caja
                                              from vef.tapertura_cierre_caja ap
                                              where ap.id_apertura_cierre_admin = v_parametros.id_apertura_cierre_caja) loop

                       v_insertar_facturas_ro_auxiliar = '

                           insert into temporal_cierre ( desc_persona,
                                                    fecha_apertura_cierre,
                                                    pais,
                                                    estacion,
                                                    punto_venta,
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
                                                    cod_moneda_extranjera,
                                                    efectivo_boletos_ml,
                                                    boletos_cash_extranjera, --
                                                    tarjeta_boletos_ml,
                                                    boletos_tarjetas_extranjera, --
                                                    cuenta_corriente_boletos_ml,
                                                    boletos_cuenta_corriente_extranjera, --
                                                    mco_boletos_ml,
                                                    boletos_mco_extranjera, --
                                                    otro_boletos_ml,
                                                    boletos_otros_extranjera, --
                                                    efectivo_ventas_ml,
                                                    cash_extranjera, --
                                                    tarjeta_ventas_ml,
                                                    tarjetas_extranjera, --
                                                    cuenta_corriente_ventas_ml,
                                                    cuenta_corriente_extranjera, --
                                                    mco_ventas_ml,
                                                    ventas_mco_extranjera, --
                                                    otro_ventas_ml,
                                                    ventas_otros_extranjera, --
                                                    efectivo_recibo_ml,
                                                    recibo_cash_extranjera, --
                                                    tarjeta_recibo_ml,
                                                    tarjeta_recibo_extranjera, --
                                                    cuenta_corriente_recibo_ml,
                                                    cuenta_corriente_recibo_extranjera, --
                                                    deposito_recibo_ml,
                                                    deposito_recibo_extranjera, --
                                                    otro_recibo_ml,
                                                    otro_recibo_extranjera, --
                                                    pago_externo_recibo_ml,
                                                    pago_externo_recibo_extranjera, --
                                                    comisiones_ml,
                                                    comisiones_me,
                                                    monto_ca_recibo_ml,
                                                    monto_cc_recibo_ml)

                                select  '''||v_desc_cajero_principal||''' as desc_persona,
                                        to_char(acc.fecha_apertura_cierre,''DD/MM/YYYY'')::varchar,
                                        coalesce(ppv.codigo,ps.codigo)::varchar as pais,
                                        COALESCE(lpv.codigo,ls.codigo)::varchar as estacion,
                                        coalesce(pv.codigo, s.codigo)::varchar as punto_venta,
                                        ''' ||COALESCE (v_observaciones,'')|| '''::varchar as obs_cierre,
                                        ' ||COALESCE (v_arqueo_ml,0)|| '::numeric as arqueo_moneda_local,
                                        ' ||COALESCE (v_arqueo_me,0)|| '::numeric as arqueo_moneda_extranjera,
                                        ' ||v_monto_inicial|| '::numeric as monto_inicial,
                            			' ||v_monto_inicial_me|| '::numeric as monto_inicial_moneda_extranjera,
                                        ' || v_tipo_cambio || '::numeric as tipo_cambio,
                                        ''' || v_tiene_dos_monedas || '''::varchar as tiene_dos_monedas,
                                        ''' || v_moneda_local || '''::varchar as moneda_local,
                                        ''' || v_moneda_extranjera || '''::varchar as moneda_extranjera,
                                         ''' || v_cod_moneda_local || '''::varchar as cod_moneda_local,
                                         ''' || v_cod_moneda_extranjera || '''::varchar as cod_moneda_extranjera,

                                        /*Aqui debemos recuperar los boletos queda pendiente*/

                                        0::numeric ,
                                        0::numeric,
                                        0::numeric,
                                        0::numeric,
                                        0::numeric,
                                        0::numeric,
                                        0::numeric,
                                        0::numeric,
                                        0::numeric,
                                        0::numeric,
                                        /****************************************************/

                                        /******************************************************Recuperamos datos para facturas******************************************************/
                                        /*Inicio recuperacion facturas*/
                                        sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as efectivo_ventas_ml,

                                        '||v_cash_extranjera||',

                                        sum(case  when fp.fop_code = ''CC'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as tarjeta_ventas_ml,

                                        '||v_tarjetas_extranjera||',

                                        sum(case  when (fp.fop_code = ''CUEC'' or fp.fop_code = ''CU'') and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as cuenta_corriente_ventas_ml,

                                        '||v_cuenta_corriente_extranjera||',

                                        sum(case  when fp.fop_code = ''MCO'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as mco_ventas_ml,

                                        '||v_ventas_mco_extranjera||',

                                        sum(case  when fp.fop_code = ''OTRO'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as otro_ventas_ml,

                                        '||v_ventas_otros_extranjera||',

                                        /*Fin recuperacion facturas*/
                                        /*****************************************************************************************************************************************/

                                        /*Recuperamos datos para Recibos*/
                                        /*Inicio recuperacion recibos*/

                                        sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') AND v.id_deposito is null  then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as efectivo_recibo_ml,

                                       '||v_recibo_cash_extranjera||',

                                        sum(case  when fp.fop_code = ''CC'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as tarjeta_recibo_ml,

                                        '||v_tarjeta_recibo_extranjera||',

                                        sum(case  when (fp.fop_code = ''CUEC'' or fp.fop_code = ''CU'') and vfp.id_moneda = ' || v_id_moneda_base  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is null)) then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as cuenta_corriente_recibo_ml,

                                        '||v_cuenta_corriente_recibo_extranjera||',

                                        /***********************************************Aqui Recuperamos los depositos asociados al Recibo****************************************************/
                                         sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is not null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is not null)) then
                                              vfp.monto_mb_efectivo
                                          else
                                              0
                                          end) as deposito_recibo_ml,

                                       '||v_deposito_recibo_extranjera||',
                                        /******************************************************************************************************************************************************/

                                        sum(case  when fp.fop_code not similar to ''(CA|CC|CUEC|MCO)%'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as otro_recibo_ml,

                                        '||v_otro_recibo_extranjera||',

                                        /*Aumentando para recuperar pago electronico (Ismael Valdivia 12/11/2020)*/
                                        sum(case  when fp.fop_code = ''EXT'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is null)) then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as pago_externo_recibo_ml,

                                        '||v_pago_externo_recibo_extranjera||',
                                        /*************************************************************************/

                                        /*Fin recuperacion recibos*/
                                        /************************************************************************************************/
                                        COALESCE((	select sum(ven.comision) from vef.tventa ven
                                            where coalesce(ven.comision,0) > 0 and ven.id_moneda = 1 and
                                                    ven.fecha = acc.fecha_apertura_cierre and ven.id_punto_venta= acc.id_punto_venta
                                                    and ven.id_usuario_cajero = acc.id_usuario_cajero and
                                                    ven.estado = ''finalizado'' and ven.estado_reg = ''activo''),0) as comisiones_ml,

                                        COALESCE((	select sum(ven.comision) from vef.tventa ven
                                            where coalesce(ven.comision,0) > 0 and ven.id_moneda = 2 and
                                                    ven.fecha = acc.fecha_apertura_cierre and ven.id_punto_venta=acc.id_punto_venta
                                                    and ven.id_usuario_cajero = acc.id_usuario_cajero and
                                                    ven.estado = ''finalizado'' and ven.estado_reg = ''activo''),0) as comisiones_me,

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
                                left join vef.tventa v on v.id_usuario_cajero = u.id_usuario
                                                                and v.fecha = acc.fecha_apertura_cierre and
                                                                v.id_punto_venta = acc.id_punto_venta and v.estado = ''finalizado''
																and v.estado_reg = ''activo''

                                left join vef.tventa_forma_pago vfp on vfp.id_venta = v.id_venta

                                /*********************************Aumentando para recuperar Instancias de pago**********************************/
                                left join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = vfp.id_medio_pago
                                left join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
                                /***************************************************************************************************************/

                                where acc.id_apertura_cierre_caja = ' || v_ids_recuperar.id_apertura_cierre_caja || '
                                group by u.desc_persona, acc.fecha_apertura_cierre,
                                ppv.codigo,ps.codigo,lpv.codigo,ls.codigo,
                                pv.codigo , pv.nombre, s.codigo ,s.nombre,acc.id_punto_venta,
                                acc.id_usuario_cajero,acc.obs_cierre, acc.arqueo_moneda_local,
                                acc.arqueo_moneda_extranjera,acc.monto_inicial,acc.monto_inicial_moneda_extranjera,
                                acc.monto_ca_recibo_ml, acc.monto_cc_recibo_ml';

                      /******************************************************************************/
                                execute v_insertar_facturas_ro_auxiliar;


                      end loop;






                  end if;

                 /********************************************************************************************/


                /*Consulta para recuperar las facturas boletos recibos con las nuevas instancias de pago*/
                v_consulta:=' select
                			  desc_persona,
                              fecha_apertura_cierre,
                              pais,
                              estacion,
                              punto_venta,
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
                              cod_moneda_extranjera,
                              sum (efectivo_boletos_ml),
                              sum (boletos_cash_extranjera),
                              sum (tarjeta_boletos_ml),
                              sum(boletos_tarjetas_extranjera),
                              sum(cuenta_corriente_boletos_ml),
                              sum(boletos_cuenta_corriente_extranjera),
                              sum(mco_boletos_ml),
                              sum(boletos_mco_extranjera),
                              sum(otro_boletos_ml),
                              sum(boletos_otros_extranjera),
                              sum(efectivo_ventas_ml),
                              sum(cash_extranjera),
                              sum(tarjeta_ventas_ml),
                              sum(tarjetas_extranjera),
                              sum(cuenta_corriente_ventas_ml),
                              sum(cuenta_corriente_extranjera),
                              sum(mco_ventas_ml),
                              sum(ventas_mco_extranjera),
                              sum(otro_ventas_ml),
                              sum(ventas_otros_extranjera),
                              sum(efectivo_recibo_ml),
                              sum(recibo_cash_extranjera),
                              sum(tarjeta_recibo_ml),
                              sum(tarjeta_recibo_extranjera),
                              sum(cuenta_corriente_recibo_ml),
                              sum(cuenta_corriente_recibo_extranjera),
                              sum(deposito_recibo_ml),
                              sum(deposito_recibo_extranjera),
                              sum(otro_recibo_ml),
                              sum(otro_recibo_extranjera),
                              sum(pago_externo_recibo_ml),
                              sum(pago_externo_recibo_extranjera),
                              sum(comisiones_ml),
                              sum(comisiones_me),
                              sum(monto_ca_recibo_ml),
                              sum(monto_cc_recibo_ml)
                              from temporal_cierre
                              group by desc_persona,
                              fecha_apertura_cierre,
                              pais,
                              estacion,
                              punto_venta,
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
                              cod_moneda_extranjera
           			  			';


              	END IF; /*Fin condicion para usar las nuevas instancias de pago tanto boletos como ventas*/
			--Definicion de la respuesta
			raise notice 'v_consulta %', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;
	/*********************************
 	#TRANSACCION:  'VF_REPORAP_SEL'
 	#DESCRIPCION:	Reporte Apertura
 	#AUTOR:		MMV
 	#FECHA: 20/2/2018
	***********************************/
    elsif(p_transaccion='VF_REPORAP_SEL')then

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

            IF (v_cod_moneda_extranjera is null) then
            	v_cod_moneda_extranjera = '';
            end if;

            IF (v_moneda_extranjera is null) then
            	v_moneda_extranjera = '';
            end if;

      /*  select ap.fecha_apertura_cierre
        from vef.tapertura_cierre_caja ap
        where ap.id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja;
     raise exception 'hola'*/

        v_consulta:='  select 	u.desc_persona::varchar,
   			to_char(a.fecha_apertura_cierre,''DD/MM/YYYY'')::varchar,
            coalesce(ppv.codigo,ps.codigo)::varchar as pais,
            COALESCE(lpv.codigo,ls.codigo)::varchar as estacion,
            coalesce(pv.codigo, s.codigo)::varchar as punto_venta,
            coalesce(pv.nombre,s.nombre)::varchar as desc_punto_venta,
            a.obs_cierre::varchar,
            a.arqueo_moneda_local,
            a.arqueo_moneda_extranjera,
            a.monto_inicial,
            a.monto_inicial_moneda_extranjera,
            ' || v_tipo_cambio || '::numeric as tipo_cambio,
            ''' || v_tiene_dos_monedas || '''::varchar as tiene_dos_monedas,
            ''' || v_moneda_local || '''::varchar as moneda_local,
            ''' || v_moneda_extranjera || '''::varchar as moneda_extranjera,
            ''' || v_cod_moneda_local || '''::varchar as cod_moneda_local,
            ''' || v_cod_moneda_extranjera || '''::varchar as cod_moneda_extranjera,
            d.monto_ca_boleto_bs as  efectivo_boletos_ml,
            d.monto_ca_boleto_usd as  efectivo_boletos_me,
            d.monto_cc_boleto_bs as tarjeta_boletos_ml,
            d.monto_cc_boleto_usd as tarjeta_boletos_me,
            d.monto_cte_boleto_bs as cuenta_corriente_boletos_ml,
            d.monto_cte_boleto_usd as cuenta_corriente_boletos_me,
            d.monto_mco_boleto_bs as mco_boletos_ml,
            d.monto_mco_boleto_usd as mco_boletos_me,

            d.monto_otro_boleto_bs::numeric as otro_boletos_ml,
            d.monto_otro_boleto_usd::numeric as otro_boletos_me,

            d.monto_ca_facturacion_bs as efectivo_ventas_ml,
            d.monto_ca_facturacion_usd as efectivo_ventas_mle,
            d.monto_cc_facturacion_bs as tarjeta_ventas_ml,
            d.monto_cc_facturacion_usd as tarjeta_ventas_me,
            d.monto_cte_facturacion_bs as cuenta_corriente_ventas_ml,
            d.monto_cte_facturacion_usd as cuenta_corriente_ventas_me,
            d.monto_mco_facturacion_bs as mco_ventas_ml,
            d.monto_mco_facturacion_usd as mco_ventas_me,

            d.monto_otro_facturacion_bs::numeric as otro_ventas_ml,
        	d.monto_otro_facturacion_usd::numeric as otro_ventas_me,

            d.comisiones_ml,
            d.comisiones_me,
            d.monto_ca_recibo_ml,
            d.monto_ca_recibo_me,
            d.monto_cc_recibo_ml,
            d.monto_cc_recibo_me,

            d.monto_otro_recibo_ml::numeric,
            d.monto_otro_recibo_me::numeric



            from vef.tapertura_cierre_caja a
            inner join segu.vusuario u on u.id_usuario = a.id_usuario_cajero
            inner join vef.tdetalle_apertura_cc d on d.id_apertura_cierre_caja = a.id_apertura_cierre_caja
            left join vef.tsucursal s on a.id_sucursal = s.id_sucursal
            left join vef.tpunto_venta pv on pv.id_punto_venta = a.id_punto_venta
            left join vef.tsucursal spv on spv.id_sucursal = pv.id_sucursal
            left join param.tlugar lpv on lpv.id_lugar = spv.id_lugar
            left join param.tlugar ls on ls.id_lugar = s.id_lugar
            left join param.tlugar ppv on ppv.id_lugar = param.f_get_id_lugar_pais(lpv.id_lugar)
            left join param.tlugar ps on ps.id_lugar = param.f_get_id_lugar_pais(ls.id_lugar)
            left join vef.tventa v on v.id_usuario_cajero = u.id_usuario and v.fecha = a.fecha_apertura_cierre and
            v.id_punto_venta = a.id_punto_venta and v.estado = ''finalizado''
            where a.id_apertura_cierre_caja = '||v_parametros.id_apertura_cierre_caja;


            --Definicion de la respuesta
            raise notice 'v_consulta %', v_consulta;
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
 	/*********************************
 	#TRANSACCION:  'VF_CONTR_SEL'
 	#DESCRIPCION:	Consulta estado cierre de apertucar
 	#AUTOR:		mmv
 	#FECHA:		11/1/2018
	***********************************/
    elsif(p_transaccion='VF_CONTR_SEL')then

    	begin
    --Sentencia de la consulta
			v_consulta:='WITH estado_cerrado as ( select  a.fecha_apertura_cierre,
								  count(a.estado) as cerrado
                                from vef.tapertura_cierre_caja a
                                where a.estado = ''cerrado''
                                group by a.fecha_apertura_cierre
                                order by fecha_apertura_cierre desc),
estado_abierto as ( select  a.fecha_apertura_cierre,
								 count(a.estado) as abierto
                                from vef.tapertura_cierre_caja a
                                where a.estado = ''abierto''
                                group by a.fecha_apertura_cierre
                                order by fecha_apertura_cierre desc
                                )select distinct  b.fecha_apertura_cierre,
                                COALESCE(a.abierto,0)::integer as abierto_co,
                                COALESCE(c.cerrado,0)::integer as cerrado_co,
                                case
                                when COALESCE( a.abierto,0) >= 1  then
                                ''abierto''
                                 when COALESCE( c.cerrado,0) >= COALESCE( a.abierto,0)  then
                                ''cerrado''
                                end::varchar as estado
                               from vef.tapertura_cierre_caja b
                               left join estado_cerrado c on c.fecha_apertura_cierre = b.fecha_apertura_cierre
                               left join estado_abierto a on a.fecha_apertura_cierre = b.fecha_apertura_cierre
                               order by fecha_apertura_cierre desc ';


			--Devuelve la respuesta
			return v_consulta;

		end;
        /*********************************
 	#TRANSACCION:  'VF_DETA_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		MMV
 	#FECHA:
	***********************************/

	elsif(p_transaccion='VF_DETA_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select 	a.id_apertura_cierre_caja,
                                    a.fecha_apertura_cierre,
                                    a.estado,
                                    p.nombre,
                                    p.codigo,
                                    initcap(u.desc_persona) as desc_persona
                            from vef.tapertura_cierre_caja a
                            inner join vef.tpunto_venta p on p.id_punto_venta = a.id_punto_venta
                            inner join segu.vusuario u on u.id_usuario = a.id_usuario_cajero
                            where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'cosulta %',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;
          /*********************************
 	#TRANSACCION:  'VF_DETA_CONT'
 	#DESCRIPCION:	Contar
 	#AUTOR:		MMV
 	#FECHA:		12-01-2018
	***********************************/

	elsif(p_transaccion='VF_DETA_CONT')then

    	begin
        	v_consulta:='select count(a.id_apertura_cierre_caja)
                         from vef.tapertura_cierre_caja a
                         inner join vef.tpunto_venta p on p.id_punto_venta = a.id_punto_venta
                         inner join segu.vusuario u on u.id_usuario = a.id_usuario_cajero
                         where ';
            v_consulta:=v_consulta||v_parametros.filtro;
            return v_consulta;
        end;
     /*********************************
 	#TRANSACCION:  'VF_LISCAR_SEL'
 	#DESCRIPCION:	Recuperar Datos ventas facturadas
 	#AUTOR:		MMV
 	#FECHA:		20-2-2018
	***********************************/
	elsif (p_transaccion='VF_LISCAR_SEL')then

     begin

     		select 	acc.id_punto_venta,
            		acc.id_sucursal,
                    acc.id_moneda,
                    acc.fecha_apertura_cierre
                    into
                    v_id_pv,
                    v_id_sucursal,
                    v_id_moneda_base,
                     v_fecha
            from vef.tapertura_cierre_caja acc
            where acc.id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja;

     --raise exception 'pues eh llegado %',v_parametros.id_apertura_cierre_caja;

            select 	m.id_moneda,
            		m.codigo_internacional,
                    m.moneda || ' (' || m.codigo_internacional || ')'
                    into
                    v_id_moneda_tri,
                    v_cod_moneda_extranjera,
                    v_moneda_extranjera
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

            /*********************VERIFICAMOS SI EXISTE EL TIPO DE CAMBIO*************************/
           IF (v_tipo_cambio is null) then

            	select  mon.codigo into v_moneda_desc
                from param.tmoneda mon
                where mon.id_moneda = v_id_moneda_tri;

                SELECT to_char(v_fecha,'DD/MM/YYYY') into v_fecha_for;

            	raise exception 'No se pudo recuperar el tipo de cambio para la moneda: % en fecha: %, comuniquese con personal de Contabilidad Ingresos.',v_moneda_desc,v_fecha_for;
            end if;
            /***************************************************************************************/

            /*Aumentando para regional MIAMI si no tiene una triangulacion*/
            IF (v_cod_moneda_extranjera is null) then
            	v_cod_moneda_extranjera = '';
            end if;

            IF (v_moneda_extranjera is null) then
            	v_moneda_extranjera = '';
            end if;


            IF (v_id_moneda_tri is not null) then

            	v_cash_extranjera = 'sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as efectivo_ventas_me';

                v_tarjetas_extranjera = 'sum(case  when fp.fop_code = ''CC'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as tarjeta_vetas_me';

                 v_cuenta_corriente_extranjera = 'sum(case  when (fp.fop_code = ''CUEC'' or fp.fop_code = ''CU'') and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as cuenta_corriente_ventas_me';

                 v_ventas_mco_extranjera = 'sum(case  when fp.fop_code = ''MCO'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as mco_ventas_me';

                  v_ventas_otros_extranjera = 'sum(case  when fp.fop_code not similar to ''(CA|CC|CUEC|MCO)%'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as otro_ventas_me';

                  v_recibo_cash_extranjera = ' sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') AND v.id_deposito is null then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as efectivo_recibo_me';

                  v_tarjeta_recibo_extranjera = 'sum(case  when fp.fop_code = ''CC'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as tarjeta_recibo_me';

                  v_cuenta_corriente_recibo_extranjera = 'sum(case  when (fp.fop_code = ''CUEC'' or fp.fop_code = ''CU'') and vfp.id_moneda = ' || v_id_moneda_tri  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is null)) then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as cuenta_corriente_recibo_me';

                 v_deposito_recibo_extranjera = 'sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is not null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is not null)) then
                                    vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                else
                                    0
                                end)as deposito_recibo_me';

                 v_otro_recibo_extranjera = 'sum(case  when fp.fop_code not similar to ''(CA|CC|CUEC|MCO)%'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as otro_recibo_me';

                 v_pago_externo_recibo_extranjera = 'sum(case  when fp.fop_code = ''EXT'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') then
                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                  else
                                      0
                                  end)as pago_externo_recibo_me';

                 v_boletos_cash_extranjera = 'sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and v.tipo_factura = ''carga'' then
                                                vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                            else
                                                0
                                            end)as efectivo_boletos_me';

                 v_boletos_tarjetas_extranjera = 'sum(case  when fp.fop_code = ''CC'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and v.tipo_factura = ''carga'' then
                                                      vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                                  else
                                                      0
                                                  end)as tarjeta_boletos_me';

                 v_boletos_cuenta_corriente_extranjera = ' sum(case  when (fp.fop_code = ''CUEC'' or fp.fop_code = ''CU'') and vfp.id_moneda = ' || v_id_moneda_tri  || ' and v.tipo_factura = ''carga'' then
                                                              vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                                          else
                                                              0
                                                          end)as cuenta_corriente_boletos_me';

                 v_boletos_mco_extranjera = ' sum(case  when fp.fop_code = ''MCO'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and  v.tipo_factura = ''carga'' then
                                                  vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                              else
                                                  0
                                              end)as mco_boletos_me';

                 v_boletos_otros_extranjera = 'sum(case  when fp.fop_code not similar to ''(CA|CC|CUEC|MCO)%'' and vfp.id_moneda = ' || v_id_moneda_tri  || ' and v.tipo_factura = ''carga''  then
                                                  vfp.monto_mb_efectivo/' || v_tipo_cambio || '
                                              else
                                                  0
                                              end)as otro_boletos_ml';


            else
            	v_cash_extranjera = '0::numeric as efectivo_ventas_me';
                v_tarjetas_extranjera = '0::numeric  as tarjeta_vetas_me';
                v_cuenta_corriente_extranjera = '0::numeric  as cuenta_corriente_ventas_me';
                v_ventas_mco_extranjera = '0::numeric  as mco_ventas_me';
                v_ventas_otros_extranjera = '0::numeric  as otro_ventas_me';
                v_recibo_cash_extranjera = ' 0::numeric  as efectivo_recibo_me';
                v_tarjeta_recibo_extranjera = '0::numeric  as tarjeta_recibo_me';
                v_cuenta_corriente_recibo_extranjera = '0::numeric  as cuenta_corriente_recibo_me';
                v_deposito_recibo_extranjera = '0::numeric  as deposito_recibo_me';
                v_otro_recibo_extranjera = '0::numeric  as otro_recibo_me';
                v_pago_externo_recibo_extranjera = '0::numeric  as pago_externo_recibo_me';

                v_boletos_cash_extranjera = '0::numeric  as efectivo_boletos_me';
                v_boletos_tarjetas_extranjera = '0::numeric  as tarjeta_boletos_me';
                v_boletos_cuenta_corriente_extranjera = '0::numeric  as cuenta_corriente_boletos_me';
                v_boletos_mco_extranjera = '0::numeric  as mco_boletos_me';
                v_boletos_otros_extranjera = '0::numeric  as otro_boletos_ml';

            end if;

            /**************************************************************/





      --IF(pxp.f_get_variable_global('instancias_de_pago_nuevas') ='no')THEN
      IF(v_fecha <= '31/12/2020')THEN

    	if( (select d.modificado
            from vef.tapertura_cierre_caja d
            where d.id_apertura_cierre_caja  = v_parametros.id_apertura_cierre_caja) = 'si')then

                    v_consulta:='with forma_pago as (
                                                  select fp.id_forma_pago,
                                                         fp.id_moneda,
                                                         (case
                                                            when fp.codigo like ''CA%'' then ''CASH''
                                                            when fp.codigo like ''CC%'' then ''CC''
                                                            when fp.codigo like ''CT%'' then ''CT''
                                                            when fp.codigo like ''MCO%'' then ''MCO''
                                                            else ''OTRO''
                                                          end)::varchar as codigo
                                                  from obingresos.tforma_pago fp),
                    botetos as (
            select id_apertura_cierre_caja,
                   monto_ca_boleto_bs,
                   monto_cc_boleto_bs,
                   monto_cte_boleto_bs,
                   monto_mco_boleto_bs,
                   monto_ca_boleto_usd,
                   monto_cc_boleto_usd,
                   monto_cte_boleto_usd,
                   monto_mco_boleto_usd,
                   monto_ca_recibo_ml,
                   monto_ca_recibo_me,
                   monto_cc_recibo_ml,
                   monto_cc_recibo_me,
                   monto_ca_facturacion_bs,
                   monto_cc_facturacion_bs,
                   monto_cte_facturacion_bs,
                   monto_mco_facturacion_bs,
                   monto_ca_facturacion_usd,
                   monto_cc_facturacion_usd,
                   monto_cte_facturacion_usd,
                   monto_mco_facturacion_usd,
                   arqueo_moneda_local,
                   arqueo_moneda_extranjera
            from vef.tdetalle_apertura_cc
            where id_apertura_cierre_caja = '||v_parametros.id_apertura_cierre_caja||'
            )
                                              select u.desc_persona::varchar,
                                                     to_char(acc.fecha_apertura_cierre, ''DD/MM/YYYY'')::varchar as fecha_apertura_cierre,
                                                     v.pais,
                                                     v.estacion,
                                                     v.agt::varchar as punto_venta,
                                                     acc.obs_cierre::varchar,
                                                     acc.monto_inicial,
                                                     acc.monto_inicial_moneda_extranjera,
                                                     '||v_tipo_cambio||'::numeric as tipo_cambio,
                                                     '''||v_tiene_dos_monedas||'''::varchar as tiene_dos_monedas,
                                                     '''||v_moneda_local||'''::varchar as moneda_local,
                                                     '''||v_moneda_extranjera||'''::varchar as moneda_extranjera,
                                                     '''||v_cod_moneda_local||'''::varchar as cod_moneda_local,
                                                     '''||v_cod_moneda_extranjera||'''::varchar as cod_moneda_extranjera,
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
                                                          /**************************Aumentando la condicion**************/

                                  sum(case  when fp_reci.codigo = ''CASH'' and fp_reci.id_moneda = ' || v_id_moneda_base  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo
                                      else
                                          0
                                      end)as efectivo_recibo_ml,
                                  sum(case  when fp_reci.codigo = ''CASH'' and fp_reci.id_moneda = ' || v_id_moneda_tri  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo/' || v_tipo_cambio || '
                                      else
                                          0
                                      end)as efectivo_recibo_me,
                                  sum(case  when fp_reci.codigo = ''CC'' and fp_reci.id_moneda = ' || v_id_moneda_base  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo
                                      else
                                          0
                                      end)as tarjeta_recibo_ml,
                                  sum(case  when fp_reci.codigo = ''CC'' and fp_reci.id_moneda = ' || v_id_moneda_tri  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo/' || v_tipo_cambio || '
                                      else
                                          0
                                      end)as tarjeta_recibo_me,
                                  sum(case  when fp_reci.codigo = ''CT'' and fp_reci.id_moneda =' || v_id_moneda_base  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo
                                      else
                                          0
                                      end)as cuenta_corriente_recibo_ml,
                                  sum(case  when fp_reci.codigo = ''CT'' and fp_reci.id_moneda = ' || v_id_moneda_tri  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo/' || v_tipo_cambio || '
                                      else
                                          0
                                      end)as cuenta_corriente_recibo_me,

                                    sum(case  when fp_reci.codigo = ''DEPO'' and fp_reci.id_moneda = ' || v_id_moneda_base  || ' and v_rec.tipo_factura = ''recibo'' and v_rec.id_deposito is not null then
                                                vfp_rec.monto_mb_efectivo
                                            else
                                                0
                                            end) as deposito_recibo_ml,

                                 sum(case  when fp_reci.codigo = ''DEPO'' and fp_reci.id_moneda = ' || v_id_moneda_tri  || ' and v_rec.tipo_factura = ''recibo'' and v_rec.id_deposito is not null then
                                        vfp_rec.monto_mb_efectivo/' || v_tipo_cambio || '
                                    else
                                        0
                                    end)as deposito_recibo_me,

                                  sum(case  when fp_reci.codigo = ''OTRO'' and fp_reci.id_moneda = ' || v_id_moneda_base  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo
                                      else
                                          0
                                      end)as otro_recibo_ml,
                                  sum(case  when fp_reci.codigo like ''OTRO'' and fp_reci.id_moneda = ' || v_id_moneda_tri  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo/' || v_tipo_cambio || '
                                      else
                                          0
                                      end)as otro_recibo_me,
                                  /************************************************************************************************/
                                                         b.monto_ca_boleto_bs,
                                                         b.monto_cc_boleto_bs,
                                                         b.monto_cte_boleto_bs,
                                                         b.monto_mco_boleto_bs,
                                                         b.monto_ca_boleto_usd,
                                                         b.monto_cc_boleto_usd,
                                                         b.monto_cte_boleto_usd,
                                                         b.monto_mco_boleto_usd,
                                                         b.monto_ca_recibo_ml,
                                                         b.monto_ca_recibo_me,
                                                         b.monto_cc_recibo_ml,
                                                         b.monto_cc_recibo_me,
                                                         b.arqueo_moneda_local,
                                                         b.arqueo_moneda_extranjera
                                              from vef.tapertura_cierre_caja acc
                                              inner join vef.tpunto_venta pv on pv.id_punto_venta=acc.id_punto_venta
                                              inner join segu.vusuario u on u.id_usuario = acc.id_usuario_cajero
                                              inner join vef.tfactucom_endesis v on v.fecha=acc.fecha_apertura_cierre and v.estado_reg=''emitida'' and v.usuario=u.cuenta and v.agt::varchar=pv.codigo
                                              inner join vef.tfactucompag_endesis vfp on vfp.id_factucom=v.id_factucom
                                              inner join forma_pago fp2 on fp2.id_forma_pago=(select fp.id_forma_pago
                                                                                                      from obingresos.tforma_pago fp
                                                                                                      inner join param.tmoneda mon on mon.id_moneda=fp.id_moneda
                                                                                                      inner join param.tlugar lug on lug.id_lugar=fp.id_lugar
                                                                                                      where fp.codigo=vfp.forma and mon.codigo_internacional=vfp.moneda and
                                                                                                      lug.codigo=vfp.pais)
                                               /*-----------------Aumentando condicion para recuperar los recibos-----------------*/
                                                  left join vef.tventa v_rec on v_rec.id_usuario_cajero = u.id_usuario
                                                                  and v_rec.fecha = acc.fecha_apertura_cierre and
                                                                  v_rec.id_punto_venta = acc.id_punto_venta and v_rec.estado = ''finalizado''
                                                 left join vef.tventa_forma_pago vfp_rec on vfp_rec.id_venta = v_rec.id_venta
                                                 left join forma_pago fp_reci on fp_reci.id_forma_pago = vfp_rec.id_forma_pago
                                                  /**********************************************************************************/

                                              inner join botetos b on b.id_apertura_cierre_caja = acc.id_apertura_cierre_caja
                                              inner join vef.tdetalle_apertura_cc  d on d.id_apertura_cierre_caja = acc.id_apertura_cierre_caja
                                              where acc.id_apertura_cierre_caja = '||v_parametros.id_apertura_cierre_caja||'
                                              and v.sw_excluir=''no''
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
                                                       v.razon_sucursal,
                                                       b.monto_ca_boleto_bs,
                                                       b.monto_cc_boleto_bs,
                                                       b.monto_cte_boleto_bs,
                                                       b.monto_mco_boleto_bs,
                                                       b.monto_ca_boleto_usd,
                                                       b.monto_cc_boleto_usd,
                                                       b.monto_cte_boleto_usd,
                                                       b.monto_mco_boleto_usd,
                                                       b.monto_ca_recibo_ml,
                                                       b.monto_ca_recibo_me,
                                                       b.monto_cc_recibo_ml,
                                                       b.monto_cc_recibo_me,
                                                       b.arqueo_moneda_local,
                                                       b.arqueo_moneda_extranjera';


                        else
                        v_consulta:='with forma_pago as (
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
                                                     v.agt::varchar as punto_venta,
                                                     acc.obs_cierre::varchar,
                                                     acc.monto_inicial,
                                                     acc.monto_inicial_moneda_extranjera,
                                                     '||v_tipo_cambio||'::numeric as tipo_cambio,
                                                     '''||v_tiene_dos_monedas||'''::varchar as tiene_dos_monedas,
                                                     '''||v_moneda_local||'''::varchar as moneda_local,
                                                     '''||v_moneda_extranjera||'''::varchar as moneda_extranjera,
                                                     '''||v_cod_moneda_local||'''::varchar as cod_moneda_local,
                                                     '''||v_cod_moneda_extranjera||'''::varchar as cod_moneda_extranjera,
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

                                                      /**************************Aumentando la condicion**************/

                                  sum(case  when fp_reci.codigo = ''CASH'' and fp_reci.id_moneda = ' || v_id_moneda_base  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo
                                      else
                                          0
                                      end)as efectivo_recibo_ml,
                                  sum(case  when fp_reci.codigo = ''CASH'' and fp_reci.id_moneda = ' || v_id_moneda_tri  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo/' || v_tipo_cambio || '
                                      else
                                          0
                                      end)as efectivo_recibo_me,
                                  sum(case  when fp_reci.codigo = ''CC'' and fp_reci.id_moneda = ' || v_id_moneda_base  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo
                                      else
                                          0
                                      end)as tarjeta_recibo_ml,
                                  sum(case  when fp_reci.codigo = ''CC'' and fp_reci.id_moneda = ' || v_id_moneda_tri  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo/' || v_tipo_cambio || '
                                      else
                                          0
                                      end)as tarjeta_recibo_me,
                                  sum(case  when fp_reci.codigo = ''CT'' and fp_reci.id_moneda =' || v_id_moneda_base  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo
                                      else
                                          0
                                      end)as cuenta_corriente_recibo_ml,
                                  sum(case  when fp_reci.codigo = ''CT'' and fp_reci.id_moneda = ' || v_id_moneda_tri  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo/' || v_tipo_cambio || '
                                      else
                                          0
                                      end)as cuenta_corriente_recibo_me,

                                   sum(case  when fp_reci.codigo = ''DEPO'' and fp_reci.id_moneda = ' || v_id_moneda_base  || ' and v_rec.tipo_factura = ''recibo'' and v_rec.id_deposito is not null then
                                                vfp_rec.monto_mb_efectivo
                                            else
                                                0
                                            end) as deposito_recibo_ml,

                                 sum(case  when fp_reci.codigo = ''DEPO'' and fp_reci.id_moneda = ' || v_id_moneda_tri  || ' and v_rec.tipo_factura = ''recibo'' and v_rec.id_deposito is not null then
                                        vfp_rec.monto_mb_efectivo/' || v_tipo_cambio || '
                                    else
                                        0
                                    end)as deposito_recibo_me,

                                  sum(case  when fp_reci.codigo = ''OTRO'' and fp_reci.id_moneda = ' || v_id_moneda_base  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo
                                      else
                                          0
                                      end)as otro_recibo_ml,
                                  sum(case  when fp_reci.codigo like ''OTRO'' and fp_reci.id_moneda = ' || v_id_moneda_tri  || ' and v_rec.tipo_factura = ''recibo'' then
                                          vfp_rec.monto_mb_efectivo/' || v_tipo_cambio || '
                                      else
                                          0
                                      end)as otro_recibo_me,
                                  /************************************************************************************************/

                                                     0::numeric as monto_ca_boleto_bs,
                                                     0::numeric as monto_cc_boleto_bs,
                                                     0::numeric as monto_cte_boleto_bs,
                                                     0::numeric as monto_mco_boleto_bs,
                                                     0::numeric as monto_ca_boleto_usd,
                                                     0::numeric as monto_cc_boleto_usd,
                                                     0::numeric as monto_cte_boleto_usd,
                                                     0::numeric as monto_mco_boleto_usd,
                                                     0::numeric as monto_ca_recibo_ml,
                                                     0::numeric as monto_ca_recibo_me,
                                                     0::numeric as monto_cc_recibo_ml,
                                                     0::numeric as monto_cc_recibo_me,
                                                     0::numeric as arqueo_moneda_local,
                                                     0::numeric as arqueo_moneda_extranjera
                                              from vef.tapertura_cierre_caja acc
                                              inner join vef.tpunto_venta pv on pv.id_punto_venta=acc.id_punto_venta
                                                   inner join segu.vusuario u on u.id_usuario = acc.id_usuario_cajero
                                                   /*-----------------Aumentando condicion para recuperar los recibos-----------------*/
                                                  left join vef.tventa v_rec on v_rec.id_usuario_cajero = u.id_usuario
                                                                  and v_rec.fecha = acc.fecha_apertura_cierre and
                                                                  v_rec.id_punto_venta = acc.id_punto_venta and v_rec.estado = ''finalizado''
                                                 left join vef.tventa_forma_pago vfp_rec on vfp_rec.id_venta = v_rec.id_venta
                                                 left join forma_pago fp_reci on fp_reci.id_forma_pago = vfp_rec.id_forma_pago
                                                  /**********************************************************************************/
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
                                              and v.sw_excluir=''no''
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
                                                       v.razon_sucursal';
                       end if;

        else
        		/*SEPARANDO LAS CONSULTAS PARA OBTENER TOTALES EXACTOS (01/01/2021)*/


                /*CONSULTA PARA LOS BOLETOS*/
                v_insertar_boletos = '
                insert into temporal_cierre_carga ( desc_persona,
                                          fecha_apertura_cierre,
                                          pais,
                                          estacion,
                                          punto_venta,
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
                                          cod_moneda_extranjera,
                                          efectivo_boletos_ml,
                                          boletos_cash_extranjera, --
                                          tarjeta_boletos_ml,
                                          boletos_tarjetas_extranjera, --
                                          cuenta_corriente_boletos_ml,
                                          boletos_cuenta_corriente_extranjera, --
                                          mco_boletos_ml,
                                          boletos_mco_extranjera, --
                                          otro_boletos_ml,
                                          boletos_otros_extranjera, --
                                          efectivo_ventas_ml,
                                          cash_extranjera, --
                                          tarjeta_ventas_ml,
                                          tarjetas_extranjera, --
                                          cuenta_corriente_ventas_ml,
                                          cuenta_corriente_extranjera, --
                                          mco_ventas_ml,
                                          ventas_mco_extranjera, --
                                          otro_ventas_ml,
                                          ventas_otros_extranjera, --
                                          efectivo_recibo_ml,
                                          recibo_cash_extranjera, --
                                          tarjeta_recibo_ml,
                                          tarjeta_recibo_extranjera, --
                                          cuenta_corriente_recibo_ml,
                                          cuenta_corriente_recibo_extranjera, --
                                          deposito_recibo_ml,
                                          deposito_recibo_extranjera, --
                                          otro_recibo_ml,
                                          otro_recibo_extranjera, --
                                          pago_externo_recibo_ml,
                                          pago_externo_recibo_extranjera, --
                                          comisiones_ml,
                                          comisiones_me,
                                          monto_ca_recibo_ml,
                                          monto_cc_recibo_ml)

                select  u.desc_persona::varchar,
                              to_char(acc.fecha_apertura_cierre,''DD/MM/YYYY'')::varchar,
                              coalesce(ppv.codigo,ps.codigo)::varchar as pais,
                              COALESCE(lpv.codigo,ls.codigo)::varchar as estacion,
                              coalesce(pv.codigo, s.codigo)::varchar as punto_venta,
                              acc.obs_cierre::varchar,
                              acc.arqueo_moneda_local,
                              acc.arqueo_moneda_extranjera,
                              acc.monto_inicial,
                              acc.monto_inicial_moneda_extranjera,
                              ' || v_tipo_cambio || '::numeric as tipo_cambio,
                              ''' || v_tiene_dos_monedas || '''::varchar as tiene_dos_monedas,
                              ''' || v_moneda_local || '''::varchar as moneda_local,
                              ''' || v_moneda_extranjera || '''::varchar as moneda_extranjera,
                               ''' || v_cod_moneda_local || '''::varchar as cod_moneda_local,
                               ''' || v_cod_moneda_extranjera || '''::varchar as cod_moneda_extranjera,

                              /*Aqui debemos recuperar los boletos queda pendiente*/



                              sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and v.tipo_factura = ''carga'' then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as efectivo_boletos_ml,

                              '||v_boletos_cash_extranjera||',

                              sum(case  when fp.fop_code = ''CC'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and v.tipo_factura = ''carga'' then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as tarjeta_boletos_ml,

                              '||v_boletos_tarjetas_extranjera||',


                              sum(case  when (fp.fop_code = ''CUEC'' or fp.fop_code = ''CU'') and vfp.id_moneda = ' || v_id_moneda_base  || ' and v.tipo_factura = ''carga'' then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as cuenta_corriente_boletos_ml,


                              '||v_boletos_cuenta_corriente_extranjera||',


                              sum(case  when fp.fop_code = ''MCO'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and  v.tipo_factura = ''carga'' then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as mco_boletos_ml,

                              '||v_boletos_mco_extranjera||',


                               sum(case  when fp.fop_code not similar to ''(CA|CC|CUEC|MCO)%'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and v.tipo_factura = ''carga''  then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as otro_boletos_ml,

                              '||v_boletos_otros_extranjera||',

                              /****************************************************/

                              /******************************************************Recuperamos datos para facturas******************************************************/
                              /*Inicio recuperacion facturas*/
                              0::numeric as efectivo_ventas_ml,

                              0::numeric as efectivo_ventas_me,

                              0::numeric as tarjeta_ventas_ml,

                              0::numeric as tarjeta_ventas_me,

                              0::numeric as cuenta_corriente_ventas_ml,

                              0::numeric as cuenta_corriente_ventas_me,

                              0::numeric as mco_ml,

                              0::numeric as mco_me,

                              0::numeric as otro_ventas_ml,

                              0::numeric as otro_ventas_me,

                              /*Fin recuperacion facturas*/
                              /*****************************************************************************************************************************************/

                              /*Recuperamos datos para Recibos*/
                              /*Inicio recuperacion recibos*/

                              0::numeric as efectivo_recibo_ml,

                             	0::numeric as efectivo_recibo_ml,

                              0::numeric as tarjeta_recibo_ml,

                              0::numeric as tarjeta_recibo_me,

                              0::numeric as cuenta_corriente_recibo_ml,

                              0::numeric as cuenta_corriente_recibo_me,

                              /***********************************************Aqui Recuperamos los depositos asociados al Recibo****************************************************/
                               0::numeric as deposito_recibo_ml,

                             0::numeric as deposito_recibo_me,
                      		  /******************************************************************************************************************************************************/

                              0::numeric as otro_recibo_ml,

                              0::numeric as otro_recibo_me,

                              0::numeric as pago_externo_recibo_ml,

                              0::numeric as pago_externo_recibo_me,
                              /*************************************************************************/

                              /*Fin recuperacion recibos*/
                              /************************************************************************************************/


                              0::NUMERIC as comisiones_ml,

                              0::NUMERIC as comisiones_me,

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
                      left join vef.tventa v on v.id_usuario_cajero = u.id_usuario
                                                      and v.fecha = acc.fecha_apertura_cierre and
                                                      v.id_punto_venta = acc.id_punto_venta and v.estado = ''finalizado''
														and v.estado_reg = ''activo''
                      left join vef.tventa_forma_pago vfp on vfp.id_venta = v.id_venta

                      /*********************************Aumentando para recuperar Instancias de pago**********************************/
                      left join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = vfp.id_medio_pago
                      left join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
                      /***************************************************************************************************************/

                      where acc.id_apertura_cierre_caja = ' || v_parametros.id_apertura_cierre_caja  || '
                      group by u.desc_persona, acc.fecha_apertura_cierre,
                      ppv.codigo,ps.codigo,lpv.codigo,ls.codigo,
                      pv.codigo , pv.nombre, s.codigo ,s.nombre,acc.id_punto_venta,
                      acc.id_usuario_cajero,acc.obs_cierre, acc.arqueo_moneda_local,
                      acc.arqueo_moneda_extranjera,acc.monto_inicial,acc.monto_inicial_moneda_extranjera,
                      acc.monto_ca_recibo_ml, acc.monto_cc_recibo_ml';

                /*******************************************************************/

                 v_insertar_facturas_ro = '

                 insert into temporal_cierre_carga ( desc_persona,
                                          fecha_apertura_cierre,
                                          pais,
                                          estacion,
                                          punto_venta,
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
                                          cod_moneda_extranjera,
                                          efectivo_boletos_ml,
                                          boletos_cash_extranjera, --
                                          tarjeta_boletos_ml,
                                          boletos_tarjetas_extranjera, --
                                          cuenta_corriente_boletos_ml,
                                          boletos_cuenta_corriente_extranjera, --
                                          mco_boletos_ml,
                                          boletos_mco_extranjera, --
                                          otro_boletos_ml,
                                          boletos_otros_extranjera, --
                                          efectivo_ventas_ml,
                                          cash_extranjera, --
                                          tarjeta_ventas_ml,
                                          tarjetas_extranjera, --
                                          cuenta_corriente_ventas_ml,
                                          cuenta_corriente_extranjera, --
                                          mco_ventas_ml,
                                          ventas_mco_extranjera, --
                                          otro_ventas_ml,
                                          ventas_otros_extranjera, --
                                          efectivo_recibo_ml,
                                          recibo_cash_extranjera, --
                                          tarjeta_recibo_ml,
                                          tarjeta_recibo_extranjera, --
                                          cuenta_corriente_recibo_ml,
                                          cuenta_corriente_recibo_extranjera, --
                                          deposito_recibo_ml,
                                          deposito_recibo_extranjera, --
                                          otro_recibo_ml,
                                          otro_recibo_extranjera, --
                                          pago_externo_recibo_ml,
                                          pago_externo_recibo_extranjera, --
                                          comisiones_ml,
                                          comisiones_me,
                                          monto_ca_recibo_ml,
                                          monto_cc_recibo_ml)

                 select  u.desc_persona::varchar,
                              to_char(acc.fecha_apertura_cierre,''DD/MM/YYYY'')::varchar,
                              coalesce(ppv.codigo,ps.codigo)::varchar as pais,
                              COALESCE(lpv.codigo,ls.codigo)::varchar as estacion,
                              coalesce(pv.codigo, s.codigo)::varchar as punto_venta,
                              acc.obs_cierre::varchar,
                              acc.arqueo_moneda_local,
                              acc.arqueo_moneda_extranjera,
                              acc.monto_inicial,
                              acc.monto_inicial_moneda_extranjera,
                              ' || v_tipo_cambio || '::numeric as tipo_cambio,
                              ''' || v_tiene_dos_monedas || '''::varchar as tiene_dos_monedas,
                              ''' || v_moneda_local || '''::varchar as moneda_local,
                              ''' || v_moneda_extranjera || '''::varchar as moneda_extranjera,
                               ''' || v_cod_moneda_local || '''::varchar as cod_moneda_local,
                               ''' || v_cod_moneda_extranjera || '''::varchar as cod_moneda_extranjera,

                              /*Aqui debemos recuperar los boletos queda pendiente*/

                              0::numeric ,
                              0::numeric,
                              0::numeric,
                              0::numeric,
                              0::numeric,
                              0::numeric,
                              0::numeric,
                              0::numeric,
                              0::numeric,
                              0::numeric,
                              /****************************************************/

                              /******************************************************Recuperamos datos para facturas******************************************************/
                              /*Inicio recuperacion facturas*/
                              sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as efectivo_ventas_ml,

                              '||v_cash_extranjera||',

                              sum(case  when fp.fop_code = ''CC'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as tarjeta_ventas_ml,

                              '||v_tarjetas_extranjera||',

                              sum(case  when (fp.fop_code = ''CUEC'' or fp.fop_code = ''CU'') and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as cuenta_corriente_ventas_ml,

                              '||v_cuenta_corriente_extranjera||',

                              sum(case  when fp.fop_code = ''MCO'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as mco_ventas_ml,

                              '||v_ventas_mco_extranjera||',

                              sum(case  when fp.fop_code not similar to ''(CA|CC|CUEC|MCO)%'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as otro_ventas_ml,

                              '||v_ventas_otros_extranjera||',

                              /*Fin recuperacion facturas*/
                              /*****************************************************************************************************************************************/

                              /*Recuperamos datos para Recibos*/
                              /*Inicio recuperacion recibos*/

                              sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') AND v.id_deposito is null  then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as efectivo_recibo_ml,

                             '||v_recibo_cash_extranjera||',

                              sum(case  when fp.fop_code = ''CC'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as tarjeta_recibo_ml,

                              '||v_tarjeta_recibo_extranjera||',

                              sum(case  when (fp.fop_code = ''CUEC'' or fp.fop_code = ''CU'') and vfp.id_moneda = ' || v_id_moneda_base  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is null)) then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as cuenta_corriente_recibo_ml,

                              '||v_cuenta_corriente_recibo_extranjera||',

                              /***********************************************Aqui Recuperamos los depositos asociados al Recibo****************************************************/
                               sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is not null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is not null)) then
                                    vfp.monto_mb_efectivo
                                else
                                    0
                                end) as deposito_recibo_ml,

                             '||v_deposito_recibo_extranjera||',
                      		  /******************************************************************************************************************************************************/

                              sum(case  when fp.fop_code not similar to ''(CA|CC|CUEC|MCO)%'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as otro_recibo_ml,

                              '||v_otro_recibo_extranjera||',

                              /*Aumentando para recuperar pago electronico (Ismael Valdivia 12/11/2020)*/
                              sum(case  when fp.fop_code = ''EXT'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is null)) then
                                      vfp.monto_mb_efectivo
                                  else
                                      0
                                  end)as pago_externo_recibo_ml,

                              '||v_pago_externo_recibo_extranjera||',
                              /*************************************************************************/

                              /*Fin recuperacion recibos*/
                              /************************************************************************************************/
                              COALESCE((	select sum(ven.comision) from vef.tventa ven
                                  where coalesce(ven.comision,0) > 0 and ven.id_moneda = 1 and
                                          ven.fecha = acc.fecha_apertura_cierre and ven.id_punto_venta= acc.id_punto_venta
                                          and ven.id_usuario_cajero = acc.id_usuario_cajero and
                                          ven.estado = ''finalizado'' and ven.estado_reg = ''activo''),0) as comisiones_ml,

                              COALESCE((	select sum(ven.comision) from vef.tventa ven
                                  where coalesce(ven.comision,0) > 0 and ven.id_moneda = 2 and
                                          ven.fecha = acc.fecha_apertura_cierre and ven.id_punto_venta=acc.id_punto_venta
                                          and ven.id_usuario_cajero = acc.id_usuario_cajero and
                                          ven.estado = ''finalizado'' and ven.estado_reg = ''activo''),0) as comisiones_me,

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
                      left join vef.tventa v on v.id_usuario_cajero = u.id_usuario
                                                      and v.fecha = acc.fecha_apertura_cierre and
                                                      v.id_punto_venta = acc.id_punto_venta and v.estado = ''finalizado''
                                                      and v.estado_reg = ''activo''

                      left join vef.tventa_forma_pago vfp on vfp.id_venta = v.id_venta

                      /*********************************Aumentando para recuperar Instancias de pago**********************************/
                      left join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = vfp.id_medio_pago
                      left join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
                      /***************************************************************************************************************/

                      where acc.id_apertura_cierre_caja = ' || v_parametros.id_apertura_cierre_caja  || '
                      group by u.desc_persona, acc.fecha_apertura_cierre,
                      ppv.codigo,ps.codigo,lpv.codigo,ls.codigo,
                      pv.codigo , pv.nombre, s.codigo ,s.nombre,acc.id_punto_venta,
                      acc.id_usuario_cajero,acc.obs_cierre, acc.arqueo_moneda_local,
                      acc.arqueo_moneda_extranjera,acc.monto_inicial,acc.monto_inicial_moneda_extranjera,
                      acc.monto_ca_recibo_ml, acc.monto_cc_recibo_ml';

                 create  temp table temporal_cierre_carga (
                                                  desc_persona varchar ,
                                                  fecha_apertura_cierre varchar,
                                                  pais varchar,
                                                  estacion varchar,
                                                  punto_venta varchar,
                                                  obs_cierre varchar,
                                                  arqueo_moneda_local numeric,
                                                  arqueo_moneda_extranjera numeric,
                                                  monto_inicial numeric,
                                                  monto_inicial_moneda_extranjera numeric,
                                                  tipo_cambio numeric,
                                                  tiene_dos_monedas varchar,
                                                  moneda_local varchar,
                                                  moneda_extranjera varchar,
                                                  cod_moneda_local varchar,
                                                  cod_moneda_extranjera varchar,
                                                  efectivo_boletos_ml numeric,
                                                  boletos_cash_extranjera numeric,
                                                  tarjeta_boletos_ml numeric,
                                                  boletos_tarjetas_extranjera numeric,
                                                  cuenta_corriente_boletos_ml numeric,
                                                  boletos_cuenta_corriente_extranjera numeric,
                                                  mco_boletos_ml numeric,
                                                  boletos_mco_extranjera numeric,
                                                  otro_boletos_ml numeric,
                                                  boletos_otros_extranjera numeric,
                                                  efectivo_ventas_ml numeric,
                                                  cash_extranjera numeric,
                                                  tarjeta_ventas_ml numeric,
                                                  tarjetas_extranjera numeric,
                                                  cuenta_corriente_ventas_ml numeric,
                                                  cuenta_corriente_extranjera numeric,
                                                  mco_ventas_ml numeric,
                                                  ventas_mco_extranjera numeric,
                                                  otro_ventas_ml numeric,
                                                  ventas_otros_extranjera numeric,
                                                  efectivo_recibo_ml numeric,
                                                  recibo_cash_extranjera numeric,
                                                  tarjeta_recibo_ml numeric,
                                                  tarjeta_recibo_extranjera numeric,
                                                  cuenta_corriente_recibo_ml numeric,
                                                  cuenta_corriente_recibo_extranjera numeric,
                                                  deposito_recibo_ml numeric,
                                                  deposito_recibo_extranjera numeric,
                                                  otro_recibo_ml numeric,
                                                  otro_recibo_extranjera numeric,
                                                  pago_externo_recibo_ml numeric,
                                                  pago_externo_recibo_extranjera numeric,
                                                  comisiones_ml numeric,
                                                  comisiones_me numeric,
                                                  monto_ca_recibo_ml numeric,
                                                  monto_cc_recibo_ml numeric
                                                      )  on commit drop;

                execute v_insertar_boletos;
                execute v_insertar_facturas_ro;

                /*Aqui Aumentaremos para que recupere datos del Cajero Auxiliar (Ismael Valdivia 08/02/2021)*/
				  select ap.id_apertura_cierre_caja
                  		into
                        v_id_apertura_recuperar
                  from vef.tapertura_cierre_caja ap
                  where ap.id_apertura_cierre_admin = v_parametros.id_apertura_cierre_caja;

                  /*Recuperamos los datos del Cajero Principal*/

                  if (v_id_apertura_recuperar is not null) then

                  	select  u.desc_persona::varchar,
                    	    to_char(acc.fecha_apertura_cierre,'DD/MM/YYYY')::varchar,
                            acc.obs_cierre::varchar,
                            acc.arqueo_moneda_local,
                            acc.arqueo_moneda_extranjera,
                            acc.monto_inicial,
                            acc.monto_inicial_moneda_extranjera
                    		into
                            v_desc_cajero_principal,
                            v_fecha_apertura_principal,
                            v_observaciones,
                            v_arqueo_ml,
                            v_arqueo_me,
                            v_monto_inicial,
                            v_monto_inicial_me
                      from vef.tapertura_cierre_caja acc
                      inner join segu.vusuario u on u.id_usuario = acc.id_usuario_cajero
                      left join vef.tsucursal s on acc.id_sucursal = s.id_sucursal
                      left join vef.tpunto_venta pv on pv.id_punto_venta = acc.id_punto_venta
                      left join vef.tsucursal spv on spv.id_sucursal = pv.id_sucursal
                      left join param.tlugar lpv on lpv.id_lugar = spv.id_lugar
                      left join param.tlugar ls on ls.id_lugar = s.id_lugar
                      left join param.tlugar ppv on ppv.id_lugar = param.f_get_id_lugar_pais(lpv.id_lugar)
                      left join param.tlugar ps on ps.id_lugar = param.f_get_id_lugar_pais(ls.id_lugar)
                      left join vef.tventa v on v.id_usuario_cajero = u.id_usuario
                                                      and v.fecha = acc.fecha_apertura_cierre and
                                                      v.id_punto_venta = acc.id_punto_venta and v.estado = 'finalizado'

                      left join vef.tventa_forma_pago vfp on vfp.id_venta = v.id_venta

                      /*********************************Aumentando para recuperar Instancias de pago**********************************/
                      left join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = vfp.id_medio_pago
                      left join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
                      /***************************************************************************************************************/

                      where acc.id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja
                      group by u.desc_persona, acc.fecha_apertura_cierre,
                      ppv.codigo,ps.codigo,lpv.codigo,ls.codigo,
                      pv.codigo , pv.nombre, s.codigo ,s.nombre,acc.id_punto_venta,
                      acc.id_usuario_cajero,acc.obs_cierre, acc.arqueo_moneda_local,
                      acc.arqueo_moneda_extranjera,acc.monto_inicial,acc.monto_inicial_moneda_extranjera,
                      acc.monto_ca_recibo_ml, acc.monto_cc_recibo_ml;

                      /*Aqui recuperamos los datos del cajero Auxiliar (Ismael Valdivia 08/02/2021)*/
                      for v_ids_recuperar in (select ap.id_apertura_cierre_caja
                                              from vef.tapertura_cierre_caja ap
                                              where ap.id_apertura_cierre_admin = v_parametros.id_apertura_cierre_caja) loop

                       v_insertar_facturas_ro_auxiliar = '

                           insert into temporal_cierre ( desc_persona,
                                                    fecha_apertura_cierre,
                                                    pais,
                                                    estacion,
                                                    punto_venta,
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
                                                    cod_moneda_extranjera,
                                                    efectivo_boletos_ml,
                                                    boletos_cash_extranjera, --
                                                    tarjeta_boletos_ml,
                                                    boletos_tarjetas_extranjera, --
                                                    cuenta_corriente_boletos_ml,
                                                    boletos_cuenta_corriente_extranjera, --
                                                    mco_boletos_ml,
                                                    boletos_mco_extranjera, --
                                                    otro_boletos_ml,
                                                    boletos_otros_extranjera, --
                                                    efectivo_ventas_ml,
                                                    cash_extranjera, --
                                                    tarjeta_ventas_ml,
                                                    tarjetas_extranjera, --
                                                    cuenta_corriente_ventas_ml,
                                                    cuenta_corriente_extranjera, --
                                                    mco_ventas_ml,
                                                    ventas_mco_extranjera, --
                                                    otro_ventas_ml,
                                                    ventas_otros_extranjera, --
                                                    efectivo_recibo_ml,
                                                    recibo_cash_extranjera, --
                                                    tarjeta_recibo_ml,
                                                    tarjeta_recibo_extranjera, --
                                                    cuenta_corriente_recibo_ml,
                                                    cuenta_corriente_recibo_extranjera, --
                                                    deposito_recibo_ml,
                                                    deposito_recibo_extranjera, --
                                                    otro_recibo_ml,
                                                    otro_recibo_extranjera, --
                                                    pago_externo_recibo_ml,
                                                    pago_externo_recibo_extranjera, --
                                                    comisiones_ml,
                                                    comisiones_me,
                                                    monto_ca_recibo_ml,
                                                    monto_cc_recibo_ml)

                                select  '''||v_desc_cajero_principal||''' as desc_persona,
                                        to_char(acc.fecha_apertura_cierre,''DD/MM/YYYY'')::varchar,
                                        coalesce(ppv.codigo,ps.codigo)::varchar as pais,
                                        COALESCE(lpv.codigo,ls.codigo)::varchar as estacion,
                                        coalesce(pv.codigo, s.codigo)::varchar as punto_venta,
                                        ''' ||COALESCE (v_observaciones,'')|| '''::varchar as obs_cierre,
                                        ' ||COALESCE (v_arqueo_ml,0)|| '::numeric as arqueo_moneda_local,
                                        ' ||COALESCE (v_arqueo_me,0)|| '::numeric as arqueo_moneda_extranjera,
                                        ' ||v_monto_inicial|| '::numeric as monto_inicial,
                            			' ||v_monto_inicial_me|| '::numeric as monto_inicial_moneda_extranjera,
                                        ' || v_tipo_cambio || '::numeric as tipo_cambio,
                                        ''' || v_tiene_dos_monedas || '''::varchar as tiene_dos_monedas,
                                        ''' || v_moneda_local || '''::varchar as moneda_local,
                                        ''' || v_moneda_extranjera || '''::varchar as moneda_extranjera,
                                         ''' || v_cod_moneda_local || '''::varchar as cod_moneda_local,
                                         ''' || v_cod_moneda_extranjera || '''::varchar as cod_moneda_extranjera,

                                        /*Aqui debemos recuperar los boletos queda pendiente*/

                                        0::numeric ,
                                        0::numeric,
                                        0::numeric,
                                        0::numeric,
                                        0::numeric,
                                        0::numeric,
                                        0::numeric,
                                        0::numeric,
                                        0::numeric,
                                        0::numeric,
                                        /****************************************************/

                                        /******************************************************Recuperamos datos para facturas******************************************************/
                                        /*Inicio recuperacion facturas*/
                                        sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as efectivo_ventas_ml,

                                        '||v_cash_extranjera||',

                                        sum(case  when fp.fop_code = ''CC'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as tarjeta_ventas_ml,

                                        '||v_tarjetas_extranjera||',

                                        sum(case  when (fp.fop_code = ''CUEC'' or fp.fop_code = ''CU'') and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as cuenta_corriente_ventas_ml,

                                        '||v_cuenta_corriente_extranjera||',

                                        sum(case  when fp.fop_code = ''MCO'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as mco_ventas_ml,

                                        '||v_ventas_mco_extranjera||',

                                        sum(case  when fp.fop_code not similar to ''(CA|CC|CUEC|MCO)%'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''computarizada'' or v.tipo_factura = ''manual'') then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as otro_ventas_ml,

                                        '||v_ventas_otros_extranjera||',

                                        /*Fin recuperacion facturas*/
                                        /*****************************************************************************************************************************************/

                                        /*Recuperamos datos para Recibos*/
                                        /*Inicio recuperacion recibos*/

                                        sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') AND v.id_deposito is null  then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as efectivo_recibo_ml,

                                       '||v_recibo_cash_extranjera||',

                                        sum(case  when fp.fop_code = ''CC'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as tarjeta_recibo_ml,

                                        '||v_tarjeta_recibo_extranjera||',

                                        sum(case  when (fp.fop_code = ''CUEC'' or fp.fop_code = ''CU'') and vfp.id_moneda = ' || v_id_moneda_base  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is null)) then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as cuenta_corriente_recibo_ml,

                                        '||v_cuenta_corriente_recibo_extranjera||',

                                        /***********************************************Aqui Recuperamos los depositos asociados al Recibo****************************************************/
                                         sum(case  when fp.fop_code = ''CA'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is not null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is not null)) then
                                              vfp.monto_mb_efectivo
                                          else
                                              0
                                          end) as deposito_recibo_ml,

                                       '||v_deposito_recibo_extranjera||',
                                        /******************************************************************************************************************************************************/

                                        sum(case  when fp.fop_code not similar to ''(CA|CC|CUEC|MCO)%'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and (v.tipo_factura = ''recibo'' or v.tipo_factura = ''recibo_manual'') then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as otro_recibo_ml,

                                        '||v_otro_recibo_extranjera||',

                                        /*Aumentando para recuperar pago electronico (Ismael Valdivia 12/11/2020)*/
                                        sum(case  when fp.fop_code = ''EXT'' and vfp.id_moneda = ' || v_id_moneda_base  || ' and ((v.tipo_factura = ''recibo'' and v.id_deposito is null) or (v.tipo_factura = ''recibo_manual'' and v.id_deposito is null)) then
                                                vfp.monto_mb_efectivo
                                            else
                                                0
                                            end)as pago_externo_recibo_ml,

                                        '||v_pago_externo_recibo_extranjera||',
                                        /*************************************************************************/

                                        /*Fin recuperacion recibos*/
                                        /************************************************************************************************/
                                        COALESCE((	select sum(ven.comision) from vef.tventa ven
                                            where coalesce(ven.comision,0) > 0 and ven.id_moneda = 1 and
                                                    ven.fecha = acc.fecha_apertura_cierre and ven.id_punto_venta= acc.id_punto_venta
                                                    and ven.id_usuario_cajero = acc.id_usuario_cajero and
                                                    ven.estado = ''finalizado'' and ven.estado_reg = ''activo''),0) as comisiones_ml,

                                        COALESCE((	select sum(ven.comision) from vef.tventa ven
                                            where coalesce(ven.comision,0) > 0 and ven.id_moneda = 2 and
                                                    ven.fecha = acc.fecha_apertura_cierre and ven.id_punto_venta=acc.id_punto_venta
                                                    and ven.id_usuario_cajero = acc.id_usuario_cajero and
                                                    ven.estado = ''finalizado'' and ven.estado_reg = ''activo''),0) as comisiones_me,

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
                                left join vef.tventa v on v.id_usuario_cajero = u.id_usuario
                                                                and v.fecha = acc.fecha_apertura_cierre and
                                                                v.id_punto_venta = acc.id_punto_venta and v.estado = ''finalizado''
                                                                and v.estado_reg = ''activo''

                                left join vef.tventa_forma_pago vfp on vfp.id_venta = v.id_venta

                                /*********************************Aumentando para recuperar Instancias de pago**********************************/
                                left join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = vfp.id_medio_pago
                                left join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
                                /***************************************************************************************************************/

                                where acc.id_apertura_cierre_caja = ' || v_ids_recuperar.id_apertura_cierre_caja || '
                                group by u.desc_persona, acc.fecha_apertura_cierre,
                                ppv.codigo,ps.codigo,lpv.codigo,ls.codigo,
                                pv.codigo , pv.nombre, s.codigo ,s.nombre,acc.id_punto_venta,
                                acc.id_usuario_cajero,acc.obs_cierre, acc.arqueo_moneda_local,
                                acc.arqueo_moneda_extranjera,acc.monto_inicial,acc.monto_inicial_moneda_extranjera,
                                acc.monto_ca_recibo_ml, acc.monto_cc_recibo_ml';

                      /******************************************************************************/
                                execute v_insertar_facturas_ro_auxiliar;


                      end loop;






                  end if;

                 /********************************************************************************************/



                /*Consulta para recuperar las facturas boletos recibos con las nuevas instancias de pago*/
                v_consulta:=' select
                			  desc_persona,
                              fecha_apertura_cierre,
                              pais,
                              estacion,
                              punto_venta,
                              obs_cierre,

                              monto_inicial,
                              monto_inicial_moneda_extranjera,
                              tipo_cambio,
                              tiene_dos_monedas,
                              moneda_local,
                              moneda_extranjera,
                              cod_moneda_local,
                              cod_moneda_extranjera,
                              sum(efectivo_ventas_ml),
                              sum(cash_extranjera),
                              sum(tarjeta_ventas_ml),
                              sum(tarjetas_extranjera),
                              sum(cuenta_corriente_ventas_ml),
                              sum(cuenta_corriente_extranjera),
                              sum(mco_ventas_ml),
                              sum(ventas_mco_extranjera),
                              sum(otro_ventas_ml),
                              sum(ventas_otros_extranjera),


                              sum(efectivo_recibo_ml),
                              sum(recibo_cash_extranjera),
                              sum(tarjeta_recibo_ml),
                              sum(tarjeta_recibo_extranjera),
                              sum(cuenta_corriente_recibo_ml),
                              sum(cuenta_corriente_recibo_extranjera),
                              sum(deposito_recibo_ml),
                              sum(deposito_recibo_extranjera),
                              sum(otro_recibo_ml),
                              sum(otro_recibo_extranjera),

                              sum (efectivo_boletos_ml),
                              sum (tarjeta_boletos_ml),
                              sum(cuenta_corriente_boletos_ml),
                              sum(mco_boletos_ml),

                              sum (boletos_cash_extranjera),
                              sum(boletos_tarjetas_extranjera),
                              sum(boletos_cuenta_corriente_extranjera),
                              sum(boletos_mco_extranjera),

                              sum(otro_boletos_ml),
                              sum(boletos_otros_extranjera),

                             sum(monto_ca_recibo_ml),
                             sum(monto_cc_recibo_ml),

                             0::numeric,
                             0::numeric,

                             arqueo_moneda_local,
                             arqueo_moneda_extranjera

                              /*

                              sum(pago_externo_recibo_ml),
                              sum(pago_externo_recibo_extranjera),
                              sum(comisiones_ml),
                              sum(comisiones_me),*/

                              from temporal_cierre_carga
                              group by desc_persona,
                              fecha_apertura_cierre,
                              pais,
                              estacion,
                              punto_venta,
                              obs_cierre,

                              monto_inicial,
                              monto_inicial_moneda_extranjera,
                              tipo_cambio,
                              tiene_dos_monedas,
                              moneda_local,
                              moneda_extranjera,
                              cod_moneda_local,
                              cod_moneda_extranjera  ,
                              arqueo_moneda_local,
                              arqueo_moneda_extranjera
           			  			';

        end if;


    		--Definicion de la respuesta
			raise notice 'v_consulta %', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

   	 end;

    /*********************************
 	#TRANSACCION:  'VF_COMAPCC_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		maylee.perez
 	#FECHA:		19-01-2021 14:16:20
	***********************************/

	elsif(p_transaccion='VF_COMAPCC_SEL')then

    	begin

            SELECT pv.id_punto_venta
            INTO v_id_punto_venta
            FROM vef.tsucursal s
            inner join vef.tpunto_venta pv on pv.id_sucursal = s.id_sucursal
            inner join vef.tsucursal_usuario su on su.id_punto_venta = pv.id_punto_venta
            and su.tipo_usuario = 'cajero_auxiliar'
            WHERE su.id_usuario = p_id_usuario;

            SELECT su.tipo_usuario
            INTO v_tipo_usuario
            FROM vef.tsucursal_usuario su
            WHERE su.id_usuario =p_id_usuario
            and su.id_punto_venta = v_id_punto_venta;

            -- raise exception 'llega %',v_tipo_usuario;

            IF (v_tipo_usuario = 'cajero_auxiliar') THEN

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
                        apcie.id_entrega_brinks,
                        pv.tipo,
                        initcap(u.desc_persona) as desc_persona,
                        apcie.modificado,

                        apcie.id_apertura_cierre_admin,
                        initcap(uadm.desc_persona)::varchar as desc_persona_adm


						from vef.tapertura_cierre_caja apcie
						inner join segu.tusuario usu1 on usu1.id_usuario = apcie.id_usuario_reg
                        inner join segu.vusuario u on u.id_usuario = apcie.id_usuario_cajero
						left join segu.tusuario usu2 on usu2.id_usuario = apcie.id_usuario_mod
				        left join vef.tpunto_venta pv on pv.id_punto_venta = apcie.id_punto_venta
                        left join vef.tsucursal suc on suc.id_sucursal = apcie.id_sucursal

                        left join vef.tapertura_cierre_caja accadm on accadm.id_apertura_cierre_caja = apcie.id_apertura_cierre_admin
                        left join segu.vusuario uadm on uadm.id_usuario = accadm.id_usuario_cajero

                        where  ';

            ELSE

            	SELECT su.tipo_usuario
                INTO v_tipo_usuario
                FROM vef.tsucursal s
                inner join vef.tpunto_venta pv on pv.id_sucursal = s.id_sucursal
                inner join vef.tsucursal_usuario su on su.id_punto_venta = pv.id_punto_venta
                WHERE su.id_usuario = p_id_usuario;

            	raise exception 'Usted es del Tipo de Usuario % y no corresponde para Apertura y Cierre de Caja (Administrativo).', upper(v_tipo_usuario);

            END IF;




            --v_filtro = ' ';

            --v_consulta :=v_consulta||v_filtro;
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_COMAPCC_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		19-01-2021 14:16:20
	***********************************/

	elsif(p_transaccion='VF_COMAPCC_CONT')then

		begin

         	SELECT pv.id_punto_venta
            INTO v_id_punto_venta
            FROM vef.tsucursal s
            inner join vef.tpunto_venta pv on pv.id_sucursal = s.id_sucursal
            inner join vef.tsucursal_usuario su on su.id_punto_venta = pv.id_punto_venta
            and su.tipo_usuario = 'cajero_auxiliar'
            WHERE su.id_usuario = p_id_usuario;

            SELECT su.tipo_usuario
            INTO v_tipo_usuario
            FROM vef.tsucursal_usuario su
            WHERE su.id_usuario =p_id_usuario
            and su.id_punto_venta = v_id_punto_venta;

            -- raise exception 'llega %',v_tipo_usuario;

            IF (v_tipo_usuario = 'cajero_auxiliar') THEN

			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(apcie.id_apertura_cierre_caja)
					    from vef.tapertura_cierre_caja apcie
					    inner join segu.tusuario usu1 on usu1.id_usuario = apcie.id_usuario_reg
						inner join segu.vusuario u on u.id_usuario = apcie.id_usuario_cajero
						left join segu.tusuario usu2 on usu2.id_usuario = apcie.id_usuario_mod
					    left join vef.tpunto_venta pv on pv.id_punto_venta = apcie.id_punto_venta
                        left join vef.tsucursal suc on suc.id_sucursal = apcie.id_sucursal

                        left join vef.tapertura_cierre_caja accadm on accadm.id_apertura_cierre_caja = apcie.id_apertura_cierre_admin
                        left join segu.vusuario uadm on uadm.id_usuario = accadm.id_usuario_cajero

                        where ';

            ELSE

            	SELECT su.tipo_usuario
                INTO v_tipo_usuario
                FROM vef.tsucursal s
                inner join vef.tpunto_venta pv on pv.id_sucursal = s.id_sucursal
                inner join vef.tsucursal_usuario su on su.id_punto_venta = pv.id_punto_venta
                WHERE su.id_usuario = p_id_usuario;

            	raise exception 'Usted es del Tipo de Usuario % no corresponde para el combo de Apertura y Cierre de Caja (Administrativo).', upper(v_tipo_usuario);

            END IF;


			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
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

ALTER FUNCTION vef.ft_apertura_cierre_caja_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
