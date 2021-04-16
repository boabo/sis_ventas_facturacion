CREATE OR REPLACE FUNCTION vef.ft_corregir_formas_pago_carga (
  p_fecha_inicio date,
  p_fecha_fin date
)
RETURNS varchar AS
$body$
DECLARE
	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_movimiento_entidad	integer;
    v_existe				numeric;
    v_nombre_agencia		varchar;
    v_id_moneda				integer;

    /******Variables para ERP******/
    v_existencia_cliente	integer;
    v_hora_estimada			time;
    v_tipo_cambio			numeric;
    v_exento				numeric;
    v_monto_total			numeric;
    v_moneda_base			integer;
    v_id_punto_venta		integer;
    v_id_cliente			integer;
    v_id_venta				integer;
    v_codigo_proceso		varchar;
    v_num_ven				varchar;
    v_id_usuario			integer;
    v_id_periodo			integer;
    v_id_sucursal			integer;
    v_num_tramite			varchar;
    v_id_proceso_wf			integer;
    v_id_estado_wf			integer;
    v_codigo_estado			varchar;
    v_id_gestion			integer;
    v_id_tipo_estado_sig	integer;
    v_id_estado_wf_sig		integer;
    v_id_funcionario_sig	integer;
    v_venta					record;
    v_estado_finalizado		integer;
    v_tabla					varchar;
    v_codigo_estado_siguiente varchar;
    v_es_fin				varchar;
    v_acceso_directo 		varchar;
    v_clase 				varchar;
    v_parametros_ad 		varchar;
    v_tipo_noti 			varchar;
    v_titulo 				varchar;
    v_id_depto				integer;
    v_obs					text;
    v_id_estado_actual		integer;
    v_cant_id_usuario		numeric;
    v_cant_id_usuario_rol	numeric;
    v_medio_pago			record;
    v_id_moneda_fp			integer;
    v_id_auxiliar_fp		integer;
    v_id_medio_pago			integer;
    v_monto_fp				numeric;
    v_acumulado_fp			numeric;
    v_id_venta_forma_pago	integer;
    v_respaldo				record;
    v_res					varchar;
    v_registros				record;
    v_nro_autorizacion		varchar;
	/******************************/

    /*Variables conexion*/
    v_conexion varchar;
    v_cadena_cnx	varchar;
    v_sinc	varchar;
    v_consulta	varchar;
    v_id_factura	integer;
    v_res_cone	varchar;
    v_datos_carga	record;
    v_cajero	varchar;
    v_consulta_inser varchar;
    v_observaciones	varchar;
    v_host varchar;
    v_puerto varchar;
    v_dbname varchar;
    p_user varchar;
    v_password varchar;
    v_semilla	varchar;

    v_cuenta_usu	varchar;
    v_pass_usu		varchar;
    v_json_data		varchar;
    v_id_tipo_estado		 integer;
    v_venta_anu			 record;
    v_id_funcionario_inicio	 integer;
    /********************/

BEGIN
	  v_nombre_funcion = 'vef.ft_corregir_formas_pago_carga';

     	for v_datos_carga in ( select fv.id_origen,
        			               fv.cod_auxiliar,
                                   fv.cod_medio_pago
        		           from vef.tfacturas_pendientes_carga_validas fv
                           where fv.fecha between p_fecha_inicio and p_fecha_fin
                           ) LOOP

         if (v_datos_carga.cod_auxiliar is null) then

         	select venta.id_venta
            	   into
                   v_id_venta
            from vef.tventa venta
            where venta.id_sistema_origen = v_datos_carga.id_origen
            and venta.tipo_factura = 'carga';



            select mp.id_medio_pago_pw
            	   into
                   v_id_medio_pago
            from obingresos.tmedio_pago_pw mp
            where mp.mop_code = v_datos_carga.cod_medio_pago;



         	update vef.tventa_forma_pago set
            id_auxiliar = Null,
            id_medio_pago = v_id_medio_pago
            where id_venta = v_id_venta;

         else

         	select aux.id_auxiliar
            into
            v_id_auxiliar_fp
            from conta.tauxiliar aux
            where (aux.cod_antiguo = v_datos_carga.cod_auxiliar or aux.codigo_auxiliar = v_datos_carga.cod_auxiliar);

            select mp.id_medio_pago_pw
            	   into
                   v_id_medio_pago
            from obingresos.tmedio_pago_pw mp
            where mp.mop_code = v_datos_carga.cod_medio_pago;

            select venta.id_venta
            	   into
                   v_id_venta
            from vef.tventa venta
            where venta.id_sistema_origen = v_datos_carga.id_origen
            and venta.tipo_factura = 'carga';

            update vef.tventa_forma_pago set
            id_auxiliar = v_id_auxiliar_fp,
            id_medio_pago = v_id_medio_pago
            where id_venta = v_id_venta;
         end if;


        END LOOP;




    return 'Registro exitoso';

EXCEPTION
	WHEN OTHERS THEN
			--update a la tabla informix.migracion

            return SQLERRM;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION vef.ft_corregir_formas_pago_carga (p_fecha_inicio date, p_fecha_fin date)
  OWNER TO postgres;
