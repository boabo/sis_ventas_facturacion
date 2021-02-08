CREATE OR REPLACE FUNCTION vef.ft_traer_acumulados_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_traer_acumulados_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tnits_no_considerados'
 AUTOR: 		 (ismael.valdivia)
 FECHA:	        03-02-2021 20:13:12
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				03-02-2021 20:13:12								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tnits_no_considerados'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;

	v_data	varchar;
    v_monto varchar;
BEGIN

    v_nombre_funcion = 'vef.ft_traer_acumulados_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_TRAER_ACUMUL_IME'
 	#DESCRIPCION:	Actualizacion de registros
 	#AUTOR:		ismael.valdivia
 	#FECHA:		03-02-2021 20:13:12
	***********************************/

	if(p_transaccion='VF_TRAER_ACUMUL_IME')then

		begin

        	v_data = vef.ft_insertar_acumulacion_comisionistas_ime();

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Actualizacion Correcta');

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'VF_MONNORMA_IME'
 	#DESCRIPCION:	Recuperar el monto de la variable Global
 	#AUTOR:		ismael.valdivia
 	#FECHA:		08-02-2021 09:32:12
	***********************************/

	elsif(p_transaccion='VF_MONNORMA_IME')then

		begin

        	v_monto = to_char(pxp.f_get_variable_global('vef_acumulativo_impuestos')::numeric,'999G999G999G999D99');

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Monto Acumulativo');
            v_resp = pxp.f_agrega_clave(v_resp,'monto_acumulado',v_monto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;


	else

    	raise exception 'Transaccion inexistente: %',p_transaccion;

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

ALTER FUNCTION vef.ft_traer_acumulados_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
