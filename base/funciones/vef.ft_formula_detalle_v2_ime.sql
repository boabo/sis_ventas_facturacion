CREATE OR REPLACE FUNCTION vef.ft_formula_detalle_v2_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_formula_detalle_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tformula_detalle'
 AUTOR: 		 (ivaldivia)
 FECHA:	        18-09-2019 21:05:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				18-09-2019 21:05:00								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tformula_detalle'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_formula_detalle	integer;
    v_cantidad				integer;

BEGIN

    v_nombre_funcion = 'vef.ft_formula_detalle_v2_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_DETFORV2_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		18-09-2019 21:05:00
	***********************************/

	if(p_transaccion='VF_DETFORV2_INS')then

        begin
        --raise exception 'llega aqui parametros:%.',v_parametros.id_concepto_ingas;




        	--Sentencia de la insercion
        	insert into vef.tformula_detalle(
			estado_reg,
			cantidad,
			id_formula,
			id_concepto_ingas,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			1,
			v_parametros.id_formula,
			v_parametros.id_concepto_ingas,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null
			)RETURNING id_formula_detalle into v_id_formula_detalle;



             /*Verificamos si el concepto es contabilizable para no mezclar*/
              select count(distinct inga.contabilizable) into v_cantidad
              from vef.tformula_detalle det
              inner join param.tconcepto_ingas inga on inga.id_concepto_ingas = det.id_concepto_ingas
              where det.id_formula = v_parametros.id_formula;

              if (v_cantidad > 1) then
                raise exception 'No puede utilizar conceptos contabilizables y no contabilizables en la misma venta';
              end if;
        /******************************************************************/


			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle de formula almacenado(a) con exito (id_formula_detalle'||v_id_formula_detalle||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_formula_detalle',v_id_formula_detalle::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VF_DETFORV2_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		18-09-2019 21:05:00
	***********************************/

	elsif(p_transaccion='VF_DETFORV2_MOD')then

		begin
			--Sentencia de la modificacion
			update vef.tformula_detalle set
			cantidad = v_parametros.cantidad,
			id_item = v_parametros.id_item,
			id_formula = v_parametros.id_formula,
			id_concepto_ingas = v_parametros.id_concepto_ingas,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_formula_detalle=v_parametros.id_formula_detalle;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle de formula modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_formula_detalle',v_parametros.id_formula_detalle::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VF_DETFORV2_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		18-09-2019 21:05:00
	***********************************/

	elsif(p_transaccion='VF_DETFORV2_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from vef.tformula_detalle
            where id_formula_detalle=v_parametros.id_formula_detalle;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle de formula eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_formula_detalle',v_parametros.id_formula_detalle::varchar);

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

ALTER FUNCTION vef.ft_formula_detalle_v2_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
