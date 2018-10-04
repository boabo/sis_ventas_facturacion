CREATE OR REPLACE FUNCTION vef.ft_replicacion_mod_boletelos (
)
RETURNS void AS
$body$
DECLARE
	v_nombre_funcion   	text;
	v_resp				varchar;
BEGIN
v_nombre_funcion = 'vef.ft_replicacion_mod_boletelos';












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