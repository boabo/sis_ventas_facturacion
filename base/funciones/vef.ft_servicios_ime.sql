CREATE OR REPLACE FUNCTION vef.ft_servicios_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ventas Facturacion
 FUNCION: 		vef.ft_concepto_ingas_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'param.tconcepto_ingas'
 AUTOR: 		 (ivaldivia)
 FECHA:	        10-09-2019 16:17:39
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				10-09-2019 16:17:39								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'param.tconcepto_ingas'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_concepto_ingas	integer;

BEGIN

    v_nombre_funcion = 'vef.ft_servicios_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VEF_INGAS_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		10-09-2019 16:17:39
	***********************************/

	if(p_transaccion='VEF_INGAS_INS')then

        begin
        	--Sentencia de la insercion
        	insert into param.tconcepto_ingas(
			estado_reg,
			tipo,
			desc_ingas,
			movimiento,
			sw_tes,
			activo_fijo,
			almacenable,
			codigo,
            tipo_punto_venta,
            punto_venta_asociado,
            id_moneda,
            precio,
            requiere_descripcion,
            excento,
            id_actividad_economica,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod,
            contabilizable,
            sw_autorizacion,
            regionales,
            nivel_permiso,
            boleto_asociado,
            agrupador,
            comision,
            id_concepto_ingas_fk,
            /*Nuevo campor para listar solo conceptos de factura, recibos, devoluciones, exportacion*/
            listado,
            id_unidad_medida,
            nandina

          	) values(
			'activo',
			v_parametros.tipo,
			v_parametros.desc_ingas,
			'recurso',
			NULL,
			NULL,
			NULL,
			NULL,
            NULL,
            NULL,
            v_parametros.id_moneda,
            v_parametros.precio,
            v_parametros.requiere_descripcion,
            v_parametros.excento,
            v_parametros.id_actividad_economica,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null,
			v_parametros.contabilizable,
            string_to_array(v_parametros.sw_autorizacion,',')::varchar[],
            string_to_array(v_parametros.regionales,',')::varchar[],
            string_to_array(v_parametros.nivel_permiso,',')::varchar[],
			v_parametros.boleto_asociado,
            v_parametros.agrupador,
            v_parametros.comision,
            v_parametros.id_concepto_ingas_fk,
            'ventas',
            v_parametros.id_unidad_medida,
            v_parametros.nandina

			)RETURNING id_concepto_ingas into v_id_concepto_ingas;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Ingresos gastos almacenado(a) con exito (id_concepto_ingas'||v_id_concepto_ingas||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_concepto_ingas',v_id_concepto_ingas::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VEF_INGAS_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		10-09-2019 16:17:39
	***********************************/

	elsif(p_transaccion='VEF_INGAS_MOD')then

		begin
			--Sentencia de la modificacion
			update param.tconcepto_ingas set
			tipo = v_parametros.tipo,
			desc_ingas = v_parametros.desc_ingas,
			--movimiento = v_parametros.movimiento,
			--activo_fijo = v_parametros.activo_fijo,
			--almacenable = v_parametros.almacenable,
            id_moneda = v_parametros.id_moneda,
            precio = v_parametros.precio,
            requiere_descripcion = v_parametros.requiere_descripcion,
            excento = v_parametros.excento,
            id_actividad_economica = v_parametros.id_actividad_economica,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            contabilizable = v_parametros.contabilizable,
            sw_autorizacion = string_to_array(v_parametros.sw_autorizacion,',')::varchar[],
            /*Aumentando para incluir regionales en los conceptos de gasto (Ismael Valdivia 16/10/2020)*/
            regionales = string_to_array(v_parametros.regionales,',')::varchar[],
            nivel_permiso = string_to_array(v_parametros.nivel_permiso,',')::varchar[],
            boleto_asociado = v_parametros.boleto_asociado,
            agrupador = v_parametros.agrupador,
            comision  = v_parametros.comision,
            id_concepto_ingas_fk = v_parametros.id_concepto_ingas_fk,
            /********************************************************************************************/
            id_unidad_medida = v_parametros.id_unidad_medida,
            nandina = v_parametros.nandina

			where id_concepto_ingas=v_parametros.id_concepto_ingas;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Ingresos gastos modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_concepto_ingas',v_parametros.id_concepto_ingas::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VEF_INGAS_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		10-09-2019 16:17:39
	***********************************/

	elsif(p_transaccion='VEF_INGAS_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from param.tconcepto_ingas
            where id_concepto_ingas=v_parametros.id_concepto_ingas;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Ingresos gastos eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_concepto_ingas',v_parametros.id_concepto_ingas::varchar);

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

ALTER FUNCTION vef.ft_servicios_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
