CREATE OR REPLACE FUNCTION vef.ft_insertar_facturas_carga_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ventas Facturación
 FUNCION: 		vef.ft_insertar_facturas_carga_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tforma_pago'
 AUTOR: 		 (ivaldivia)
 FECHA:	        28-08-2019 15:00:00
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
	v_id_movimiento_entidad	integer;
    v_existe				record;
    v_nombre_agencia		varchar;
    v_id_moneda				integer;

    /*Variables conexion*/
    v_conexion varchar;
    v_cadena_cnx	varchar;
    v_sinc	varchar;
    v_consulta	varchar;
    v_id_factura	integer;
    v_res_cone	varchar;
    /********************/

BEGIN

    v_nombre_funcion = 'vef.ft_insertar_facturas_carga_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VEF_INS_FACCARGA'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		28-08-2019 15:00:00
	***********************************/

	if(p_transaccion='VEF_INS_FACCARGA')then

        begin

        /*Migrar los datos a la nueva base de datos db_facturas_2019*/

        /*Establecemos la conexion con la base de datos*/
          v_cadena_cnx = migra.f_obtener_cadena_conexion();
          v_conexion = (SELECT dblink_connect(v_cadena_cnx));
        /*************************************************/

          select * FROM dblink(v_cadena_cnx,'select nextval(''sfe.tfactura_id_factura_seq'')',TRUE)AS t1(resp integer)
          into v_id_factura;

          v_consulta = 'INSERT INTO sfe.tfactura(
                            id_factura,
                            fecha_factura,
                            nro_factura,
                            nro_autorizacion,
                            estado,
                            nit_ci_cli,
                            razon_social_cli,
                            importe_total_venta,
                            codigo_control /*,
                            sistema_origen,
                            id_sistema_origen,
                            tipo_factura,
                            usuario_registro,
                            fecha_hora_registro*/
                            )
          				values(
                            '||v_id_factura||',
                            '''||v_parametros.fecha::date||''',
                            '''||v_parametros.nro_factura::varchar||''',
                            '''||v_parametros.nro_autorizacion::varchar||''',
                            '''||v_parametros.estado::varchar||''',
                            '''||v_parametros.nit::varchar||''',
                            '''||v_parametros.razon_social::varchar||''',
                            '||v_parametros.importe_total::numeric||',
                            '''||v_parametros.codigo_control::varchar||'''/*,
                            ''CGNC'',
                            '||v_parametros.id_origen::integer||',
                            '''||v_parametros.tipo_factura::varchar||''',
                            '''||v_parametros.usuario_registro::varchar||''',
                            '''||v_parametros.fecha_hora_registro::timestamp||'''*/
                            );';

              IF(v_conexion!='OK') THEN
              		raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
              ELSE

              	perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);

              	v_res_cone=(select dblink_disconnect());

              END IF;

              /************************************/
        /*****************************************************************/


              --Definicion de la respuesta
              v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Factura Registrada con con exito');

              --Devuelve la respuesta
              return v_resp;


          end;

    --Definicion de la respuesta
    v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Factura Registrada con con exito');
    v_resp = pxp.f_agrega_clave(v_resp,'tipo_mensaje','exito');

    --Devuelve la respuesta
    return v_resp;

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

ALTER FUNCTION vef.ft_insertar_facturas_carga_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
