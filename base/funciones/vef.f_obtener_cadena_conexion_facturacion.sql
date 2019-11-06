CREATE OR REPLACE FUNCTION vef.f_obtener_cadena_conexion_facturacion (
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Facturacion
 FUNCION: 		vef.f_obtener_cadena_conexion_facturacion
 DESCRIPCION:   Funcion que recupera los datos de conexion al servidor remoto
 AUTOR: 		Ismael Valdivia
 FECHA:	        06-11-2019
 COMENTARIOS:
***************************************************************************/
DECLARE

v_host varchar;
v_puerto varchar;
v_dbname varchar;
p_user varchar;
v_password varchar;
v_sincronizar varchar;
v_resp varchar;
v_nombre_funcion varchar;

BEGIN


 v_nombre_funcion =  'vef.f_obtener_cadena_conexion_facturacion';

  v_host=pxp.f_get_variable_global('sincroniza_ip_facturacion');
  v_puerto=pxp.f_get_variable_global('sincroniza_puerto_facturacion');
  v_dbname=pxp.f_get_variable_global('sincronizar_base_facturacion');
  p_user=pxp.f_get_variable_global('sincronizar_user_facturacion');
  v_password=pxp.f_get_variable_global('sincronizar_password_facturacion');


   IF v_sincronizar = 'false'  THEN

     raise exception 'La sincronizacion esta deshabilitada. Verifique la configuración en la tabla de variables globales';

   END IF;


  RETURN 'hostaddr='||v_host||' port='||v_puerto||' dbname='||v_dbname||' user='||p_user||' password='||v_password;


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

ALTER FUNCTION vef.f_obtener_cadena_conexion_facturacion ()
  OWNER TO postgres;
