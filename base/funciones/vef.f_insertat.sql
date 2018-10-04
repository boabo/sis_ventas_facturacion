CREATE OR REPLACE FUNCTION vef.f_insertat (
)
RETURNS void AS
$body$
DECLARE
 v_nombre_funcion   	 text;
 v_resp    				 varchar;
 v_mensaje 			 	 varchar;
 v_registros 			 record;
BEGIN
 v_nombre_funcion = 'vef.f_mod_forma_pago_amadeus';
 FOR v_registros in (select *
from obingresos.tmod_forma_pago_nov_es)
    LOOP

              INSERT INTO obingresos.tmod_forma_pago ( 	billete,
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
                                                      v_registros.billete,--
                                                      v_registros.forma,--
                                                      v_registros.fecha,--
                                                      v_registros.importe,--
                                                      v_registros.comision,--
                                                       v_registros.agt ,--
                                                      v_registros.pais,--
                                                      v_registros.estacion,--
                                                      v_registros.numero,
                                                      v_registros.tarjeta,
                                                      v_registros.moneda,
                                                      v_registros.autoriza,
                                                      v_registros.ctacte,
                                                      v_registros.usuario,
                                                      v_registros.fecha_mod,
                                                      v_registros.hora_mod,
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