CREATE OR REPLACE FUNCTION vef.f_monto_forma_pago_endesis (
  p_moneda varchar,
  p_id_usuario_cajero integer,
  p_fecha date
)
RETURNS numeric AS
$body$
DECLARE
  v_monto_total		numeric;
BEGIN
   	select coalesce(sum(pag.monto),0) into v_monto_total
 	from vef.tventa_forma_pago pag
 	inner join vef.tventa vta on vta.id_venta=pag.id_venta
 	inner join param.tmoneda mon on mon.id_moneda=vta.id_moneda
 	where vta.id_usuario_cajero = p_id_usuario_cajero
    and vta.estado='finalizado'
 	and vta.fecha=p_fecha
 	and mon.codigo_internacional=p_moneda;

    select coalesce(sum(fac.monto),0) into v_monto_total
 	from vef.tfactucom_endesis fac
    inner join segu.tusuario usu on usu.cuenta=fac.usuario
 	where usu.id_usuario = p_id_usuario_cajero
 	and fac.fecha=p_fecha and fac.moneda=p_moneda;

    return v_monto_total;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;