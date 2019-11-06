CREATE OR REPLACE FUNCTION vef.ft_formula_v2_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_formula_v2_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tformula'
 AUTOR: 		 (ivaldivia)
 FECHA:	        17-09-2019 15:28:13
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-09-2019 15:28:13								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tformula'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_formula	integer;

BEGIN

    v_nombre_funcion = 'vef.ft_formula_v2_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_FORMULAV2_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		17-09-2019 15:28:13
	***********************************/

	if(p_transaccion='VF_FORMULAV2_INS')then

        begin

        	--Sentencia de la insercion
        	insert into vef.tformula(
			estado_reg,
			nombre,
			descripcion,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod,
            tipo_punto_venta,
            punto_venta_asociado
          	) values(
			'activo',
			v_parametros.nombre,
			v_parametros.descripcion,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null,
			string_to_array (v_parametros.tipo_punto_venta,',')::VARCHAR[],
            string_to_array (v_parametros.punto_venta_asociado,',')::integer[]

			)RETURNING id_formula into v_id_formula;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Fórmula almacenado(a) con exito (id_formula'||v_id_formula||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_formula',v_id_formula::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VF_FORMULAV2_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		17-09-2019 15:28:13
	***********************************/

	elsif(p_transaccion='VF_FORMULAV2_MOD')then

		begin
			--Sentencia de la modificacion
			update vef.tformula set
			nombre = v_parametros.nombre,
			descripcion = v_parametros.descripcion,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            tipo_punto_venta = string_to_array (v_parametros.tipo_punto_venta,',')::VARCHAR[],
            punto_venta_asociado = string_to_array (v_parametros.punto_venta_asociado,',')::integer[]
			where id_formula=v_parametros.id_formula;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Fórmula modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_formula',v_parametros.id_formula::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VF_FORMULAV2_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		17-09-2019 15:28:13
	***********************************/

	elsif(p_transaccion='VF_FORMULAV2_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from vef.tformula
            where id_formula=v_parametros.id_formula;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Fórmula eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_formula',v_parametros.id_formula::varchar);

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

ALTER FUNCTION vef.ft_formula_v2_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
