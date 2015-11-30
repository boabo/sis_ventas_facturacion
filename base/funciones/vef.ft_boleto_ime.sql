CREATE OR REPLACE FUNCTION "vef"."ft_boleto_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_boleto_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tboleto'
 AUTOR: 		 (jrivera)
 FECHA:	        26-11-2015 22:03:32
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
	v_id_boleto	integer;
			    
BEGIN

    v_nombre_funcion = 'vef.ft_boleto_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'VF_BOL_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		26-11-2015 22:03:32
	***********************************/

	if(p_transaccion='VF_BOL_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into vef.tboleto(
			id_punto_venta,
			numero,
			ruta,
			estado_reg,
			monto,
			fecha,
			id_usuario_ai,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			v_parametros.id_punto_venta,
			v_parametros.numero,
			v_parametros.ruta,
			'activo',
			v_parametros.monto,
			v_parametros.fecha,
			v_parametros._id_usuario_ai,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_boleto into v_id_boleto;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boleto almacenado(a) con exito (id_boleto'||v_id_boleto||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_id_boleto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'VF_BOL_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		26-11-2015 22:03:32
	***********************************/

	elsif(p_transaccion='VF_BOL_MOD')then

		begin
			--Sentencia de la modificacion
			update vef.tboleto set
			id_punto_venta = v_parametros.id_punto_venta,
			numero = v_parametros.numero,
			ruta = v_parametros.ruta,
			monto = v_parametros.monto,
			fecha = v_parametros.fecha,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_boleto=v_parametros.id_boleto;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boleto modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_parametros.id_boleto::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'VF_BOL_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		26-11-2015 22:03:32
	***********************************/

	elsif(p_transaccion='VF_BOL_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from vef.tboleto
            where id_boleto=v_parametros.id_boleto;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boleto eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_parametros.id_boleto::varchar);
              
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
$BODY$
LANGUAGE 'plpgsql' VOLATILE
COST 100;
ALTER FUNCTION "vef"."ft_boleto_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
