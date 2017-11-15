CREATE OR REPLACE FUNCTION vef.ft_entrega_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_entrega_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tentrega'
 AUTOR: 		 (admin)
 FECHA:	        12-09-2017 15:04:26
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
	v_id_entrega_brinks	integer;
    v_punto_venta		record;

BEGIN

    v_nombre_funcion = 'vef.ft_entrega_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_ENG_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		12-09-2017 15:04:26
	***********************************/

	if(p_transaccion='VF_ENG_INS')then

        begin
        	--Sentencia de la insercion

           -- raise exception 'id: %',v_parametros.id_punto_venta;
        	insert into vef.tentrega(
			fecha_recojo,
			estado_reg,
			id_usuario_ai,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			fecha_mod,
			id_usuario_mod,
            id_punto_venta
          	) values(
			v_parametros.fecha_recojo,
			'activo',
			v_parametros._id_usuario_ai,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			null,
			null,
            v_parametros.id_punto_venta
			)RETURNING id_entrega_brinks into v_id_entrega_brinks;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Entrega almacenado(a) con exito (id_entrega_brinks'||v_id_entrega_brinks||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_entrega_brinks',v_id_entrega_brinks::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VF_ENG_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		12-09-2017 15:04:26
	***********************************/

	elsif(p_transaccion='VF_ENG_MOD')then

		begin
			--Sentencia de la modificacion
			update vef.tentrega set
			fecha_recojo = v_parametros.fecha_recojo,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_entrega_brinks=v_parametros.id_entrega_brinks;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Entrega modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_entrega_brinks',v_parametros.id_entrega_brinks::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VF_ENG_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		12-09-2017 15:04:26
	***********************************/

	elsif(p_transaccion='VF_ENG_ELI')then

		begin
			--Sentencia de la eliminacion
            update vef.tapertura_cierre_caja  set
			id_entrega_brinks  = null
 			where id_entrega_brinks  = v_parametros.id_entrega_brinks;

			delete from vef.tentrega
            where id_entrega_brinks=v_parametros.id_entrega_brinks;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Entrega eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_entrega_brinks',v_parametros.id_entrega_brinks::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    /*********************************
 	#TRANSACCION:  'VF_ENG_GET'
 	#DESCRIPCION:	lista punto venta
 	#AUTOR:		admin
 	#FECHA:		12-09-2017 15:04:26
	***********************************/

    elsif(p_transaccion='VF_ENG_GET')then

		begin
			select  v.id_punto_venta,
            		v.nombre
            		into
                    v_punto_venta
                    from vef.tpunto_venta v
                    where v.id_punto_venta = v_parametros.id_punto_venta;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Transaccion Exitosa');
            v_resp = pxp.f_agrega_clave(v_resp,'id_punto_venta',v_punto_venta.id_punto_venta::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'nombre',v_punto_venta.nombre::varchar);

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