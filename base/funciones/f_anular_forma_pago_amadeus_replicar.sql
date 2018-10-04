CREATE OR REPLACE FUNCTION vef.f_anular_forma_pago_amadeus_replicar (
  p_id_boleto integer
)
RETURNS void AS
$body$
DECLARE
 v_nombre_funcion   	 text;
 v_resp    				 varchar;
 v_mensaje 			 	 varchar;
 v_registros 			 record;
 v_datos				 record;
BEGIN
 v_nombre_funcion = 'vef.f_mod_forma_pago_amadeus';

  FOR v_registros in (WITH punto_venta AS (	select 	p.id_punto_venta,
                                    l.codigo as codigo_pais,
                                    p.nombre,
                                    p.codigo ,
                                    lu.codigo as estacion
                                   from vef.tsucursal s
                                   inner join vef.tpunto_venta p on p.id_sucursal = s.id_sucursal
                                   inner join param.tlugar l on l.id_lugar = param.f_obtener_padre_id_lugar (s.id_lugar,'pais')
                                   inner join param.tlugar lu on lu.id_lugar = s.id_lugar
        )select 	CAST(b.nro_boleto as  DECIMAL) as billete,
                    p.codigo as mod_forma_pago,
                    b.fecha_emision as fecha,
                    a.importe,
                    b.comision,
                    CAST(n.codigo as DECIMAL) as agt,
                    n.codigo_pais as pais,
                    n.estacion,
                  (case
                    when a.numero_tarjeta = ' 'then
                    ''
                    else
                    a.numero_tarjeta
                    end
                    ) as numero_tarjeta,

                    COALESCE(a.tarjeta,' ') as tarjeta,
                    mo.codigo_internacional as moneda,
                    COALESCE(a.codigo_tarjeta,' ') as autoriza,
                    COALESCE(au.codigo_auxiliar,' ') as ctacte,
                    left (us.cuenta,3) as cuenta ,
                    a.fecha_reg::date as fecha_mod,
                    to_char(a.fecha_reg, 'HH12:MI:SS') as hr_mod,
                    (case
                    	when a.mco = '' or a.mco is null then
                        '0'
                        else
                        a.mco
                        end) as pagomco,
                    'ERP BOA'  as observa
                    from obingresos.tboleto_amadeus b
                    inner join punto_venta n on n.id_punto_venta = b.id_punto_venta
                    inner join obingresos.tboleto_amadeus_forma_pago a on a.id_boleto_amadeus = b.id_boleto_amadeus
                    inner join segu.vusuario us on us.id_usuario = a.id_usuario_reg
                    inner join vef.tforma_pago p on p.id_forma_pago = a.id_forma_pago
                    inner join param.tmoneda mo on mo.id_moneda = p.id_moneda
                    left join conta.tauxiliar au on au.id_auxiliar = a.id_auxiliar
                    where b.id_boleto_amadeus = p_id_boleto )
    LOOP
     INSERT INTO obingresos.tmod_forma_pago ( billete,
                                                                forma,
                                                                fecha,
                                                                importe,
                                                                comision,
                                                                agt,
                                                                pais,
                                                                estacion,
                                                                numero,
                                                                tarjeta,
                                                                moneda,
                                                                autoriza,
                                                                ctacte,
                                                                usuario,
                                                                fecha_mod,
                                                                hora_mod,
                                                                pagomco,
                                                                observa
                                                              )VALUES(
                                                              v_registros.billete,
                                                              v_registros.mod_forma_pago,
                                                              v_registros.fecha,
                                                              v_registros.importe,
                                                              v_registros.comision,
                                                               v_registros.agt ,
                                                              v_registros.pais,
                                                              v_registros.estacion,
                                                              v_registros.numero_tarjeta,
                                                              v_registros.tarjeta,
                                                              v_registros.moneda,
                                                              v_registros.autoriza,
                                                              v_registros.ctacte,
                                                              v_registros.cuenta,
                                                              v_registros.fecha_mod,
                                                              v_registros.hr_mod,
                                                              CAST(v_registros.pagomco as DECIMAL),
                                                              'ERP BOA'
                                                              );

	END LOOP;



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