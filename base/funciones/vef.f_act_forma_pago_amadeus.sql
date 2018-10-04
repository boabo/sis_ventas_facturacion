CREATE OR REPLACE FUNCTION vef.f_act_forma_pago_amadeus (
  p_id_boleto integer,
  p_id_forma_pago integer
)
RETURNS void AS
$body$
DECLARE
 v_nombre_funcion   	 text;
 v_resp    				 varchar;
 v_mensaje 			 	 varchar;
 v_registros 			 record;
BEGIN
v_nombre_funcion = 'vef.f_act_forma_pago_amadeus';

      select  a.id_boleto_amadeus,
              a.id_forma_pago,
              a.importe,
              f.codigo
              into
              v_registros
      from obingresos.tboleto_amadeus_forma_pago a
      inner join vef.tforma_pago f on f.id_forma_pago = a.id_forma_pago
      where id_boleto_amadeus = p_id_boleto;

      if v_registros.id_forma_pago = p_id_forma_pago then
      	raise exception 'mismo forma de pago';
      else
      	raise exception 'cambio la forma de pago';
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