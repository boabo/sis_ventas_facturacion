CREATE OR REPLACE FUNCTION vef.ft_nits_no_considerados_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_nits_no_considerados_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tnits_no_considerados'
 AUTOR: 		 (maylee.perez)
 FECHA:	        21-12-2020 20:13:12
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				21-12-2020 20:13:12								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tnits_no_considerados'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_nits_no_considerados	integer;

BEGIN

    v_nombre_funcion = 'vef.ft_nits_no_considerados_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_NITNCONS_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		21-12-2020 20:13:12
	***********************************/

	if(p_transaccion='VF_NITNCONS_INS')then

        begin
        	--Sentencia de la insercion
        	insert into vef.tnits_no_considerados(
			estado_reg,
			nit_ci,
			razon_social,
			t_contr,
			incl_rep,
			observaciones,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.nit_ci,
			v_parametros.razon_social,
			v_parametros.t_contr,
			v_parametros.incl_rep,
			v_parametros.observaciones,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null



			)RETURNING id_nits_no_considerados into v_id_nits_no_considerados;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Nits No considerados almacenado(a) con exito (id_nits_no_considerados'||v_id_nits_no_considerados||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_nits_no_considerados',v_id_nits_no_considerados::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VF_NITNCONS_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		21-12-2020 20:13:12
	***********************************/

	elsif(p_transaccion='VF_NITNCONS_MOD')then

		begin
			--Sentencia de la modificacion
			update vef.tnits_no_considerados set
			nit_ci = v_parametros.nit_ci,
			razon_social = v_parametros.razon_social,
			t_contr = v_parametros.t_contr,
			incl_rep = v_parametros.incl_rep,
			observaciones = v_parametros.observaciones,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_nits_no_considerados=v_parametros.id_nits_no_considerados;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Nits No considerados modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_nits_no_considerados',v_parametros.id_nits_no_considerados::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VF_NITNCONS_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		21-12-2020 20:13:12
	***********************************/

	elsif(p_transaccion='VF_NITNCONS_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from vef.tnits_no_considerados
            where id_nits_no_considerados=v_parametros.id_nits_no_considerados;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Nits No considerados eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_nits_no_considerados',v_parametros.id_nits_no_considerados::varchar);

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