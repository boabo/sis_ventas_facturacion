CREATE OR REPLACE FUNCTION vef.ft_verificar_excento (
  p_id_venta integer
)
RETURNS varchar AS
$body$
DECLARE
  v_requiere_excento	varchar;
BEGIN

	select distinct (con.excento) into v_requiere_excento
    from vef.tventa_detalle det
    inner join param.tconcepto_ingas con on con.id_concepto_ingas = det.id_producto
    where det.id_venta = p_id_venta and con.excento = 'si';

    if (v_requiere_excento = '' OR v_requiere_excento is null) then
    	v_requiere_excento = 'no';
    end if;

    return v_requiere_excento;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION vef.ft_verificar_excento (p_id_venta integer)
  OWNER TO postgres;
