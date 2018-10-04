CREATE OR REPLACE FUNCTION vef.f_mod_forma_pago_amadeus_prueba (
)
RETURNS void AS
$body$
DECLARE
 v_nombre_funcion   	 text;
 v_resp    				 varchar;
 v_mensaje 			 	 varchar;
 v_registros 			 record;
 v_datos				     record;
BEGIN
 v_nombre_funcion = 'vef.f_mod_forma_pago_amadeus';

 FOR v_registros in (WITH total_ventas AS(        ( WITH forma_pago AS (
  SELECT fp_1.id_forma_pago,
         fp_1.id_moneda,
         CASE
           WHEN fp_1.codigo::text ~~ 'CA%'::text THEN 'CASH'::text
           WHEN fp_1.codigo::text ~~ 'CC%'::text THEN 'CC'::text
           WHEN fp_1.codigo::text ~~ 'CT%'::text THEN 'CT'::text
           WHEN fp_1.codigo::text ~~ 'MCO%'::text THEN 'MCO'::text
           ELSE 'OTRO'::text
         END::character varying AS codigo
  FROM obingresos.tforma_pago fp_1)
         SELECT u.desc_persona::character varying AS desc_persona,
                to_char(acc.fecha_apertura_cierre::timestamp with time zone,
                  'DD/MM/YYYY'::text)::character varying AS to_char,
                COALESCE(ppv.codigo, ps.codigo) AS pais,
                COALESCE(lpv.codigo, ls.codigo) AS estacion,
                COALESCE(pv.codigo, s.codigo) AS punto_venta,
                acc.obs_cierre::character varying AS obs_cierre,
                acc.arqueo_moneda_local,
                acc.arqueo_moneda_extranjera,
                acc.monto_inicial,
                acc.monto_inicial_moneda_extranjera,
                6.960000 AS tipo_cambio,
                'si'::character varying AS tiene_dos_monedas,
                'Bolivianos (BOB)'::character varying AS moneda_local,
                'Dolares Americanos (USD)'::character varying AS
                  moneda_extranjera,
                'BOB'::character varying AS cod_moneda_local,
                'USD'::character varying AS cod_moneda_extranjera,
                sum(CASE
                      WHEN fp.codigo::text = 'CASH'::text AND fp.id_moneda = 1
                        THEN bfp.importe
                      ELSE 0::numeric
                    END) AS efectivo_boletos_ml,
                sum(CASE
                      WHEN fp.codigo::text = 'CASH'::text AND fp.id_moneda = 2
                        THEN bfp.importe
                      ELSE 0::numeric
                    END) AS efectivo_boletos_me,
                sum(CASE
                      WHEN fp.codigo::text = 'CC'::text AND fp.id_moneda = 1
                        THEN bfp.importe
                      ELSE 0::numeric
                    END) AS tarjeta_boletos_ml,
                sum(CASE
                      WHEN fp.codigo::text = 'CC'::text AND fp.id_moneda = 2
                        THEN bfp.importe
                      ELSE 0::numeric
                    END) AS tarjeta_boletos_me,
                sum(CASE
                      WHEN fp.codigo::text = 'CT'::text AND fp.id_moneda = 1
                        THEN bfp.importe
                      ELSE 0::numeric
                    END) AS cuenta_corriente_boletos_ml,
                sum(CASE
                      WHEN fp.codigo::text = 'CT'::text AND fp.id_moneda = 2
                        THEN bfp.importe
                      ELSE 0::numeric
                    END) AS cuenta_corriente_boletos_me,
                sum(CASE
                      WHEN fp.codigo::text = 'MCO'::text AND fp.id_moneda = 1
                        THEN bfp.importe
                      ELSE 0::numeric
                    END) AS mco_boletos_ml,
                sum(CASE
                      WHEN fp.codigo::text = 'MCO'::text AND fp.id_moneda = 2
                        THEN bfp.importe
                      ELSE 0::numeric
                    END) AS mco_boletos_me,
                sum(CASE
                      WHEN fp.codigo::text = 'OTRO'::text AND fp.id_moneda = 1
                        THEN bfp.importe
                      ELSE 0::numeric
                    END) AS otro_boletos_ml,
                sum(CASE
                      WHEN fp.codigo::text ~~ 'OTRO'::text AND fp.id_moneda = 2
                        THEN bfp.importe
                      ELSE 0::numeric
                    END) AS otro_boletos_me,
                sum(CASE
                      WHEN fp2.codigo::text = 'CASH'::text AND fp2.id_moneda = 1
                        THEN vfp.monto_mb_efectivo
                      ELSE 0::numeric
                    END) AS efectivo_ventas_ml,
                sum(CASE
                      WHEN fp2.codigo::text = 'CASH'::text AND fp2.id_moneda = 2
                        THEN vfp.monto_mb_efectivo / 6.960000
                      ELSE 0::numeric
                    END) AS efectivo_ventas_me,
                sum(CASE
                      WHEN fp2.codigo::text = 'CC'::text AND fp2.id_moneda = 1
                        THEN vfp.monto_mb_efectivo
                      ELSE 0::numeric
                    END) AS tarjeta_ventas_ml,
                sum(CASE
                      WHEN fp2.codigo::text = 'CC'::text AND fp2.id_moneda = 2
                        THEN vfp.monto_mb_efectivo / 6.960000
                      ELSE 0::numeric
                    END) AS tarjeta_vetas_me,
                sum(CASE
                      WHEN fp2.codigo::text = 'CT'::text AND fp2.id_moneda = 1
                        THEN vfp.monto_mb_efectivo
                      ELSE 0::numeric
                    END) AS cuenta_corriente_ventas_ml,
                sum(CASE
                      WHEN fp2.codigo::text = 'CT'::text AND fp2.id_moneda = 2
                        THEN vfp.monto_mb_efectivo / 6.960000
                      ELSE 0::numeric
                    END) AS cuenta_corriente_ventas_me,
                sum(CASE
                      WHEN fp2.codigo::text = 'MCO'::text AND fp2.id_moneda = 1
                        THEN vfp.monto_mb_efectivo
                      ELSE 0::numeric
                    END) AS mco_ventas_ml,
                sum(CASE
                      WHEN fp2.codigo::text = 'MCO'::text AND fp2.id_moneda = 2
                        THEN vfp.monto_mb_efectivo / 6.960000
                      ELSE 0::numeric
                    END) AS mco_ventas_me,
                sum(CASE
                      WHEN fp2.codigo::text = 'OTRO'::text AND fp2.id_moneda = 1
                        THEN vfp.monto_mb_efectivo
                      ELSE 0::numeric
                    END) AS otro_ventas_ml,
                sum(CASE
                      WHEN fp2.codigo::text ~~ 'OTRO'::text AND fp2.id_moneda =
                        2 THEN vfp.monto_mb_efectivo / 6.960000
                      ELSE 0::numeric
                    END) AS otro_ventas_me,
                COALESCE((
                           SELECT sum(ven.comision) AS sum
                           FROM vef.tventa ven
                           WHERE COALESCE(ven.comision, 0::numeric) > 0::numeric
  AND
                                 ven.id_moneda = 1 AND
                                 ven.fecha = acc.fecha_apertura_cierre AND
                                 ven.id_punto_venta = acc.id_punto_venta AND
                                 ven.id_usuario_cajero = acc.id_usuario_cajero
  AND
                                 ven.estado::text = 'finalizado'::text
                ), 0::numeric) + COALESCE((
                                            SELECT sum(bol.comision) AS sum
                                            FROM obingresos.tboleto_amadeus bol
                                            WHERE COALESCE(bol.comision, 0::
                                              numeric) > 0::numeric AND
                                                  bol.id_moneda_boleto = 1 AND
                                                  bol.fecha_emision =
                                                    acc.fecha_apertura_cierre
  AND
                                                  bol.id_punto_venta =
                                                    acc.id_punto_venta AND
                                                  bol.id_usuario_cajero =
                                                    acc.id_usuario_cajero AND
                                                  bol.estado::text = 'revisado'
                                                    ::text
                ), 0::numeric) AS comisiones_ml,
                COALESCE((
                           SELECT sum(ven.comision) AS sum
                           FROM vef.tventa ven
                           WHERE COALESCE(ven.comision, 0::numeric) > 0::numeric
  AND
                                 ven.id_moneda = 2 AND
                                 ven.fecha = acc.fecha_apertura_cierre AND
                                 ven.id_punto_venta = acc.id_punto_venta AND
                                 ven.id_usuario_cajero = acc.id_usuario_cajero
  AND
                                 ven.estado::text = 'finalizado'::text
                ), 0::numeric) + COALESCE((
                                            SELECT sum(bol.comision) AS sum
                                            FROM obingresos.tboleto_amadeus bol
                                            WHERE COALESCE(bol.comision, 0::
                                              numeric) > 0::numeric AND
                                                  bol.id_moneda_boleto = 2 AND
                                                  bol.fecha_emision =
                                                    acc.fecha_apertura_cierre
  AND
                                                  bol.id_punto_venta =
                                                    acc.id_punto_venta AND
                                                  bol.id_usuario_cajero =
                                                    acc.id_usuario_cajero AND
                                                  bol.estado::text = 'revisado'
                                                    ::text
                ), 0::numeric) AS comisiones_me,
                acc.monto_ca_recibo_ml,
                acc.monto_cc_recibo_ml,
                acc.id_apertura_cierre_caja
         FROM vef.tapertura_cierre_caja acc
              JOIN segu.vusuario u ON u.id_usuario = acc.id_usuario_cajero
              LEFT JOIN vef.tsucursal s ON acc.id_sucursal = s.id_sucursal
              LEFT JOIN vef.tpunto_venta pv ON pv.id_punto_venta =
                acc.id_punto_venta
              LEFT JOIN vef.tsucursal spv ON spv.id_sucursal = pv.id_sucursal
              LEFT JOIN param.tlugar lpv ON lpv.id_lugar = spv.id_lugar
              LEFT JOIN param.tlugar ls ON ls.id_lugar = s.id_lugar
              LEFT JOIN param.tlugar ppv ON ppv.id_lugar =
                param.f_get_id_lugar_pais(lpv.id_lugar)
              LEFT JOIN param.tlugar ps ON ps.id_lugar =
                param.f_get_id_lugar_pais(ls.id_lugar)
              LEFT JOIN obingresos.tboleto_amadeus b ON b.id_usuario_cajero =
                u.id_usuario AND b.fecha_emision = acc.fecha_apertura_cierre AND
                b.id_punto_venta = acc.id_punto_venta AND b.estado::text =
                'revisado'::text AND b.voided::text = 'no'::text
              LEFT JOIN obingresos.tboleto_amadeus_forma_pago bfp ON
                bfp.id_boleto_amadeus = b.id_boleto_amadeus
              LEFT JOIN forma_pago fp ON fp.id_forma_pago = bfp.id_forma_pago
              LEFT JOIN vef.tventa v ON v.id_usuario_cajero = u.id_usuario AND
                v.fecha = acc.fecha_apertura_cierre AND v.id_punto_venta =
                acc.id_punto_venta AND v.estado::text = 'finalizado'::text
              LEFT JOIN vef.tventa_forma_pago vfp ON vfp.id_venta = v.id_venta
              LEFT JOIN forma_pago fp2 ON fp2.id_forma_pago = vfp.id_forma_pago
         WHERE acc.id_apertura_cierre_caja  = ANY (ARRAY [ 2510, 2511, 2498,
           2500, 2512, 2501, 2513, 2509, 2516, 2504, 2515, 2499, 2508, 2506,
           2497, 2507, 2526, 2527, 2524, 2521, 2528, 2530, 2518, 2519, 2525,
           2529, 2517, 2503, 2523, 2531, 2522, 2505, 2514, 2520, 2719 ])
         GROUP BY u.desc_persona,
                  acc.fecha_apertura_cierre,
                  ppv.codigo,
                  ps.codigo,
                  lpv.codigo,
                  ls.codigo,
                  pv.codigo,
                  pv.nombre,
                  s.codigo,
                  s.nombre,
                  acc.id_punto_venta,
                  acc.id_usuario_cajero,
                  acc.obs_cierre,
                  acc.arqueo_moneda_local,
                  acc.arqueo_moneda_extranjera,
                  acc.monto_inicial,
                  acc.monto_inicial_moneda_extranjera,
                  acc.monto_ca_recibo_ml,
                  acc.monto_cc_recibo_ml,
                  acc.id_apertura_cierre_caja)
        UNION ALL
        ( WITH forma_pago AS (
                 SELECT fp.id_forma_pago,
                        fp.id_moneda,
                        CASE
                          WHEN fp.codigo::text ~~ 'CA%'::text THEN 'CASH'::text
                          WHEN fp.codigo::text ~~ 'CC%'::text THEN 'CC'::text
                          WHEN fp.codigo::text ~~ 'CT%'::text THEN 'CT'::text
                          WHEN fp.codigo::text ~~ 'MCO%'::text THEN 'MCO'::text
                          ELSE 'OTRO'::text
                        END::character varying AS codigo
                 FROM obingresos.tforma_pago fp)
         SELECT u.desc_persona::character varying AS desc_persona,
                to_char(acc.fecha_apertura_cierre::timestamp with time zone,
                  'DD/MM/YYYY'::text)::character varying AS
                  fecha_apertura_cierre,
                v.pais,
                v.estacion,
                v.agt::character varying AS punto_venta,
                acc.obs_cierre::character varying AS obs_cierre,
                acc.arqueo_moneda_local,
                acc.arqueo_moneda_extranjera,
                acc.monto_inicial,
                acc.monto_inicial_moneda_extranjera,
                6.960000 AS tipo_cambio,
                'si'::character varying AS tiene_dos_monedas,
                'Bolivianos (BOB)'::character varying AS moneda_local,
                'Dolares Americanos (USD)'::character varying AS
                  moneda_extranjera,
                'BOB'::character varying AS cod_moneda_local,
                'USD'::character varying AS cod_moneda_extranjera,
                0 AS efectivo_boletos_ml,
                0 AS efectivo_boletos_me,
                0 AS tarjeta_boletos_ml,
                0 AS tarjeta_boletos_me,
                0 AS cuenta_corriente_boletos_ml,
                0 AS cuenta_corriente_boletos_me,
                0 AS mco_boletos_ml,
                0 AS mco_boletos_me,
                0 AS otro_boletos_ml,
                0 AS otro_boletos_me,
                sum(CASE
                      WHEN fp2.codigo::text = 'CASH'::text AND fp2.id_moneda = 1
                        THEN vfp.importe_pago
                      ELSE 0::numeric
                    END) AS efectivo_ventas_ml,
                sum(CASE
                      WHEN fp2.codigo::text = 'CASH'::text AND fp2.id_moneda = 2
                        THEN vfp.importe_pago
                      ELSE 0::numeric
                    END) AS efectivo_ventas_me,
                sum(CASE
                      WHEN fp2.codigo::text = 'CC'::text AND fp2.id_moneda = 1
                        THEN vfp.importe_pago
                      ELSE 0::numeric
                    END) AS tarjeta_ventas_ml,
                sum(CASE
                      WHEN fp2.codigo::text = 'CC'::text AND fp2.id_moneda = 2
                        THEN vfp.importe_pago
                      ELSE 0::numeric
                    END) AS tarjeta_vetas_me,
                sum(CASE
                      WHEN fp2.codigo::text = 'CT'::text AND fp2.id_moneda = 1
                        THEN vfp.importe_pago
                      ELSE 0::numeric
                    END) AS cuenta_corriente_ventas_ml,
                sum(CASE
                      WHEN fp2.codigo::text = 'CT'::text AND fp2.id_moneda = 2
                        THEN vfp.importe_pago
                      ELSE 0::numeric
                    END) AS cuenta_corriente_ventas_me,
                sum(CASE
                      WHEN fp2.codigo::text = 'MCO'::text AND fp2.id_moneda = 1
                        THEN vfp.importe_pago
                      ELSE 0::numeric
                    END) AS mco_ventas_ml,
                sum(CASE
                      WHEN fp2.codigo::text = 'MCO'::text AND fp2.id_moneda = 2
                        THEN vfp.importe_pago
                      ELSE 0::numeric
                    END) AS mco_ventas_me,
                sum(CASE
                      WHEN fp2.codigo::text = 'OTRO'::text AND fp2.id_moneda = 1
                        THEN vfp.importe_pago
                      ELSE 0::numeric
                    END) AS otro_ventas_ml,
                sum(CASE
                      WHEN fp2.codigo::text ~~ 'OTRO'::text AND fp2.id_moneda =
                        2 THEN vfp.importe_pago
                      ELSE 0::numeric
                    END) AS otro_ventas_me,
                0 AS comisiones_ml,
                0 AS comisiones_me,
                0 AS monto_ca_recibo_ml,
                0 AS monto_cc_recibo_ml,
                0
         FROM vef.tapertura_cierre_caja acc
              JOIN vef.tpunto_venta pv ON pv.id_punto_venta = acc.id_punto_venta
              JOIN segu.vusuario u ON u.id_usuario = acc.id_usuario_cajero
              JOIN vef.tfactucom_endesis v ON v.fecha =
                acc.fecha_apertura_cierre AND v.estado_reg::text = 'emitida'::
                text AND v.usuario::text = u.cuenta::text AND v.agt::character
                varying::text = pv.codigo::text
              JOIN vef.tfactucompag_endesis vfp ON vfp.id_factucom =
                v.id_factucom
              JOIN forma_pago fp2 ON fp2.id_forma_pago =((
                                                           SELECT
                                                             fp.id_forma_pago
                                                           FROM
                                                             obingresos.tforma_pago
                                                             fp
                                                                JOIN
                                                                  param.tmoneda
                                                                  mon ON
                                                                  mon.id_moneda
                                                                  = fp.id_moneda
                                                                JOIN
                                                                  param.tlugar
                                                                  lug ON
                                                                  lug.id_lugar =
                                                                  fp.id_lugar
                                                           WHERE fp.codigo::text
                                                             = vfp.forma::text
  AND
                                                                 mon.codigo_internacional
                                                                   ::text =
                                                                   vfp.moneda::
                                                                   text AND
                                                                 lug.codigo::
                                                                   text =
                                                                   vfp.pais::
                                                                   text
              ))
         WHERE acc.id_apertura_cierre_caja  = ANY (ARRAY [ 2510, 2511, 2498,
           2500, 2512, 2501, 2513, 2509, 2516, 2504, 2515, 2499, 2508, 2506,
           2497, 2507, 2526, 2527, 2524, 2521, 2528, 2530, 2518, 2519, 2525,
           2529, 2517, 2503, 2523, 2531, 2522, 2505, 2514, 2520, 2719 ])AND
               v.sw_excluir::text = 'no'::text
         GROUP BY u.desc_persona,
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
        )
 SELECT total_ventas.desc_persona,
        total_ventas.to_char,
        total_ventas.pais,
        total_ventas.estacion,
        total_ventas.punto_venta,
        total_ventas.obs_cierre,
        total_ventas.arqueo_moneda_local,
        total_ventas.arqueo_moneda_extranjera,
        total_ventas.monto_inicial,
        total_ventas.monto_inicial_moneda_extranjera,
        total_ventas.tipo_cambio,
        total_ventas.tiene_dos_monedas,
        total_ventas.moneda_local,
        total_ventas.moneda_extranjera,
        total_ventas.cod_moneda_local,
        total_ventas.cod_moneda_extranjera,
        sum(total_ventas.efectivo_boletos_ml) AS efectivo_boletos_ml,
        sum(total_ventas.efectivo_boletos_me) AS efectivo_boletos_me,
        sum(total_ventas.tarjeta_boletos_ml) AS tarjeta_boletos_ml,
        sum(total_ventas.tarjeta_boletos_me) AS tarjeta_boletos_me,
        sum(total_ventas.cuenta_corriente_boletos_ml) AS cuenta_corriente_boletos_ml,
        sum(total_ventas.cuenta_corriente_boletos_me) AS cuenta_corriente_boletos_me,
        sum(total_ventas.mco_boletos_ml) AS mco_boletos_ml,
        sum(total_ventas.mco_boletos_me) AS mco_boletos_me,
        sum(total_ventas.otro_boletos_ml) AS otro_boletos_ml,
        sum(total_ventas.otro_boletos_me) AS otro_boletos_me,
        sum(total_ventas.efectivo_ventas_ml) AS efectivo_ventas_ml,
        sum(total_ventas.efectivo_ventas_me) AS efectivo_ventas_me,
        sum(total_ventas.tarjeta_ventas_ml) AS tarjeta_ventas_ml,
        sum(total_ventas.tarjeta_vetas_me) AS tarjeta_vetas_me,
        sum(total_ventas.cuenta_corriente_ventas_ml) AS cuenta_corriente_ventas_ml,
        sum(total_ventas.cuenta_corriente_ventas_me) AS cuenta_corriente_ventas_me,
        sum(total_ventas.mco_ventas_ml) AS mco_ventas_ml,
        sum(total_ventas.mco_ventas_me) AS mco_ventas_me,
        sum(total_ventas.otro_ventas_ml) AS otro_ventas_ml,
        sum(total_ventas.otro_ventas_me) AS otro_ventas_me,
        sum(total_ventas.comisiones_ml) AS comisiones_ml,
        sum(total_ventas.comisiones_me) AS comisiones_me,
        sum(total_ventas.monto_ca_recibo_ml) AS monto_ca_recibo_ml,
        sum(total_ventas.monto_cc_recibo_ml) AS monto_cc_recibo_ml,
        id_apertura_cierre_caja
 FROM total_ventas
 GROUP BY total_ventas.desc_persona,
          total_ventas.to_char,
          total_ventas.pais,
          total_ventas.estacion,
          total_ventas.obs_cierre,
          total_ventas.arqueo_moneda_local,
          total_ventas.arqueo_moneda_extranjera,
          total_ventas.monto_inicial,
          total_ventas.monto_inicial_moneda_extranjera,
          total_ventas.punto_venta,
          total_ventas.tipo_cambio,
          total_ventas.tiene_dos_monedas,
          total_ventas.moneda_local,
          total_ventas.moneda_extranjera,
          total_ventas.cod_moneda_local,
          total_ventas.cod_moneda_extranjera,
          id_apertura_cierre_caja)LOOP
     INSERT INTO vef.tdetalle_apertura_cc ( id_usuario_reg,
                                                id_usuario_mod,
                                                fecha_reg,
                                                fecha_mod,
                                                estado_reg,
                                                id_apertura_cierre_caja,
                                                tipo_apertura,
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
            									arqueo_moneda_extranjera,
                                                comisiones_ml,
                                                comisiones_me
                                              )
                                              VALUES (
                                                1,
                                                null,
                                                now(),
                                                null,
                                                'activo',
                                                v_registros.id_apertura_cierre_caja,
                                                'normal',
                                                --local
                                                v_registros.efectivo_boletos_ml,
                                                v_registros.tarjeta_boletos_ml,
                                                v_registros.cuenta_corriente_boletos_ml,
                                                v_registros.mco_boletos_ml,
                                                --Internacional
                                                v_registros.efectivo_boletos_me,
                                                v_registros.tarjeta_boletos_me,
                                                v_registros.cuenta_corriente_boletos_me,
                                                v_registros.mco_boletos_me,

                                                0, --v_registros.monto_ca_recibo_ml,
                                                0, --v_registros.monto_ca_recibo_me,
                                                0, --v_registros.monto_cc_recibo_ml,
                                                0, --v_registros.monto_cc_recibo_me,

                                                v_registros.efectivo_ventas_ml,
                                                v_registros.tarjeta_ventas_ml,
                                                v_registros.cuenta_corriente_ventas_ml,
                                                v_registros.mco_ventas_ml,

                                                v_registros.efectivo_ventas_me,
                                                v_registros.tarjeta_vetas_me,
                                                v_registros.cuenta_corriente_ventas_me,
                                                v_registros.mco_ventas_me,

                                                v_registros.arqueo_moneda_local,
            									v_registros.arqueo_moneda_extranjera,
                                                v_registros.comisiones_ml,
                                                v_registros.comisiones_me);


 END LOOP;

 /* FOR v_registros in (WITH punto_venta AS (	select 	p.id_punto_venta,
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
                    where b.nro_boleto in ( '9302400686728',
 '9302400686729',
'9302400686790',
'9302400686791',
'9302400686801',
 '9302400686850',
 '9302400686851',
 '9302400686852',
 '9302400686853',
 '9302400686936',
 '9302400686938',
 '9302400726059',
 '9302400726060',
 '9302400726061',
 '9302400726256',
 '9302400726257',
 '9302400726322',
 '9302400726323',
 '9302400726324',
 '9302400726325',
 '9302400726326',
 '9302400726327',
 '9302400726328',
 '9302400726329',
 '9302400726330',
 '9302400726331',
 '9302400726332',
 '9302400726333',
 '9302400726334',
 '9302400726335',
 '9302400726336',
 '9302400726337',
 '9302400726338',
 '9302400726339',
 '9302400726340',
 '9302400726341',
 '9302400726342',
 '9302400726343',
 '9302400726344',
 '9302400726345',
 '9302400726346',
 '9302400726347',
 '9302400726359',
 '9302400726395',
 '9302400726396',
 '9302400726401',
 '9302400726402',
 '9302400726449',
 '9302400726450',
 '9302400726451',
 '9302400726525',
 '9302400726526',
 '9302400726527',
 '9302400726528',
 '9302400726633',
 '9302400726634',
'9302400749441',
'9302400749442',
 '9302400749443',
 '9302400749444',
 '9302400749445',
 '9302400749446',
 '9302400749468',
 '9302400749469',
 '9302400749470',
 '9302400749471',
 '9302400749472',
 '9302400749473',
 '9302400785474',
 '9302400785475',
 '9302400785476',
 '9302400785477',
 '9302400785478',
 '9302400785479',
 '9302400785480',
 '9302400785481',
 '9302400785482',
 '9302400785483',
 '9302400785484',
 '9302400785485',
 '9302400785486',
 '9302400785487',
 '9302400785488',
 '9302400785489',
 '9302400785490',
 '9302400785491',
 '9302400785492',
 '9302400785493',
 '9302400785494',
 '9302400785495',
 '9302400785496',
 '9302400785497',
 '9302400785498',
 '9302400801879',
 '9302400801880'
)
)
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

	END LOOP;*/
   /*
    FOR v_registros in  (select  b.id_boleto_amadeus,
		p.id_boleto_amadeus_forma_pago,
        p.importe - (b.neto *0.06) as cal ,


 		(b.neto *0.06) as comision_6,
        (b.neto *0.1) as comision_10

from obingresos.tboleto_amadeus b

inner join obingresos.tboleto_amadeus_forma_pago p on p.id_boleto_amadeus = b.id_boleto_amadeus

where b.nro_boleto in ( '9302400686728',
 '9302400686729',
'9302400686790',
'9302400686791',
'9302400686801',
 '9302400686850',
 '9302400686851',
 '9302400686852',
 '9302400686853',
 '9302400686936',
 '9302400686938',
 '9302400726059',
 '9302400726060',
 '9302400726061',
 '9302400726256',
 '9302400726257',
 '9302400726322',
 '9302400726323',
 '9302400726324',
 '9302400726325',
 '9302400726326',
 '9302400726327',
 '9302400726328',
 '9302400726329',
 '9302400726330',
 '9302400726331',
 '9302400726332',
 '9302400726333',
 '9302400726334',
 '9302400726335',
 '9302400726336',
 '9302400726337',
 '9302400726338',
 '9302400726339',
 '9302400726340',
 '9302400726341',
 '9302400726342',
 '9302400726343',
 '9302400726344',
 '9302400726345',
 '9302400726346',
 '9302400726347',
 '9302400726359',
 '9302400726395',
 '9302400726396',
 '9302400726401',
 '9302400726402',
 '9302400726449',
 '9302400726450',
 '9302400726451',
 '9302400726525',
 '9302400726526',
 '9302400726527',
 '9302400726528',
 '9302400726633',
 '9302400726634',
'9302400749441',
'9302400749442',
 '9302400749443',
 '9302400749444',
 '9302400749445',
 '9302400749446',
 '9302400749468',
 '9302400749469',
 '9302400749470',
 '9302400749471',
 '9302400749472',
 '9302400749473',
 '9302400785474',
 '9302400785475',
 '9302400785476',
 '9302400785477',
 '9302400785478',
 '9302400785479',
 '9302400785480',
 '9302400785481',
 '9302400785482',
 '9302400785483',
 '9302400785484',
 '9302400785485',
 '9302400785486',
 '9302400785487',
 '9302400785488',
 '9302400785489',
 '9302400785490',
 '9302400785491',
 '9302400785492',
 '9302400785493',
 '9302400785494',
 '9302400785495',
 '9302400785496',
 '9302400785497',
 '9302400785498',
 '9302400801879',
 '9302400801880'
)
order by id_boleto_amadeus )loop

	update obingresos.tboleto_amadeus_forma_pago  set
	importe = v_registros.cal
    where id_boleto_amadeus_forma_pago = v_registros.id_boleto_amadeus_forma_pago;

   end loop;*/



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