CREATE OR REPLACE FUNCTION vef.ft_permisos_sucursales_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_permisos_sucursales_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'cola.tusuario_sucursal'
 AUTOR: 		 Ismael Valdivia
 FECHA:	        9-12-2020 11:30:0
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
   	v_consulta        		text;
    v_id_autorizacion		integer;


BEGIN

    v_nombre_funcion = 'vef.ft_permisos_sucursales_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VEF_SUCPERMISOS_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		26-08-2020 11:30:47
	***********************************/

	if(p_transaccion='VEF_SUCPERMISOS_INS')then

        begin

        	--Sentencia de la insercion
        	insert into vef.tpermiso_sucursales(
			id_funcionario,
			estado_reg,
			id_usuario_reg,
			fecha_reg,
			id_usuario_mod,
			fecha_mod
          	) values(
            v_parametros.id_funcionario,
			'activo',
			p_id_usuario,
			now(),
			null,
			null
			)RETURNING id_autorizacion into v_id_autorizacion;



			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Registro Exitoso');
            v_resp = pxp.f_agrega_clave(v_resp,'id_autorizacion',v_id_autorizacion::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VEF_SUCPERMISOS_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		26/08/2020
	***********************************/

      elsif(p_transaccion='VEF_SUCPERMISOS_MOD')then

		begin

			update vef.tpermiso_sucursales set
            id_funcionario = v_parametros.id_funcionario
            where id_autorizacion = v_parametros.id_autorizacion;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Permiso Modificado');
            v_resp = pxp.f_agrega_clave(v_resp,'id_autorizacion',v_parametros.id_autorizacion::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VEF_SUCPERMISOS_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		26/08/2020
	***********************************/

	elsif(p_transaccion='VEF_SUCPERMISOS_ELI')then

		begin

			delete from vef.tpermiso_sucursales
            where id_autorizacion=v_parametros.id_autorizacion;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Autorizacion Eliminada');
            v_resp = pxp.f_agrega_clave(v_resp,'id_autorizacion',v_parametros.id_autorizacion::varchar);

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

ALTER FUNCTION vef.ft_permisos_sucursales_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
