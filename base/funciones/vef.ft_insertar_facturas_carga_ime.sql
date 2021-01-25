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
    v_datos_carga	record;
    v_cajero	varchar;
    v_consulta_inser varchar;
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
          v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();
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
                            importe_otros_no_suj_iva,
                            codigo_control ,
                            sistema_origen,
                            id_origen,
                            tipo_factura,
                            usuario_reg,
                            desc_ruta

                            )
          				values(
                            '||v_id_factura||',
                            '''||v_parametros.fecha::date||''',
                            '''||v_parametros.nro_factura::varchar||''',
                            '''||v_parametros.nro_autorizacion::varchar||''',
                            ''VÁLIDA'',
                            '''||v_parametros.nit::varchar||''',
                            '''||v_parametros.razon_social::varchar||''',
                            '||v_parametros.importe_total::numeric||',
                            0,
                            '''||v_parametros.codigo_control::varchar||''',--decomentar
                            ''CARGA'',
                            '||v_parametros.id_origen::integer||',
                            '''||v_parametros.tipo_factura::varchar||''',
                            '''||v_parametros.usuario_registro::varchar||''',
                            ''CARGA NACIONAL COMPUTARIZADA''
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

    /*********************************
 	#TRANSACCION:  'VEF_ANULAR_FCA'
 	#DESCRIPCION:	Actualizacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		01-10-2019 17:30:00
	***********************************/

	elsif(p_transaccion='VEF_ANULAR_FCA')then

		begin
			--Sentencia de la modificacion

            /*Recuperamos el nombre del cajero que esta finalizando la factura*/
            SELECT per.nombre_completo2 into v_cajero
            from segu.tusuario usu
            inner join segu.vpersona2 per on per.id_persona = usu.id_persona
            where usu.id_usuario = p_id_usuario;
            /******************************************************************/

        /*Replicacion a la base de datos DB_FACTURAS 2019*/
   		/*Para migrar los datos a la nueva base de datos db_facturas_2019*/

          /*Establecemos la conexion con la base de datos*/
            v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();
            v_conexion = (SELECT dblink_connect(v_cadena_cnx));

            /************************************************/
          		select * FROM dblink(v_cadena_cnx,'
                								  select f.id_factura,
                                                  		 f.fecha_factura,
                                                         f.nro_factura,
                                                         f.estado,
                                                         f.nit_ci_cli,
                                                         f.razon_social_cli,
                                                         f.importe_total_venta,
                                                         f.usuario_reg,
                                                         f.tipo_factura,
                                                         f.id_origen,
                                                         f.sistema_origen
                  								  from sfe.tfactura f
                                                  where f.id_origen = '''||v_parametros.id_origen||''' and f.sistema_origen = ''CARGA''
                                                  ',TRUE) AS datos_carga (
                                                  		id_factura INTEGER,
                                                        fecha_factura date,
                                                        nro_factura varchar,
                                                        estado varchar,
                                                        nit_ci_cli varchar,
                                                        razon_social_cli varchar,
                                                        importe_total_venta numeric,
                                                        usuario_reg varchar,
                                                        tipo_factura varchar,
                                                        id_origen INTEGER,
                                                        sistema_origen varchar )
                                                  into v_datos_carga;

              v_consulta = 'update sfe.tfactura set
                            estado_reg = ''inactivo''
                            where id_origen = '''||v_datos_carga.id_origen||''' and sistema_origen = ''CARGA'' and estado <> ''anulado'';';



               select * FROM dblink(v_cadena_cnx,'select nextval(''sfe.tfactura_id_factura_seq'')',TRUE)AS t1(resp integer)
            	into v_id_factura;

              v_consulta_inser = '
                                INSERT INTO sfe.tfactura(
                                id_factura,
                                fecha_factura,
                                nro_factura,
                                estado,
                                nit_ci_cli,
                                razon_social_cli,
                                importe_total_venta,
                                usuario_reg,
                                tipo_factura,
                                id_origen,
                                sistema_origen,
                                desc_ruta
                                )
                                values(
                                '||v_id_factura||',
                                '''||v_datos_carga.fecha_factura||''',
                                '''||v_datos_carga.nro_factura::varchar||''',
                                ''ANULADA'',
                                ''0'',
                                ''ANULADO'',
                                0,
                                '''||v_datos_carga.usuario_reg||''',
                                '''||v_datos_carga.tipo_factura||''',
                                '||v_datos_carga.id_origen||',
                                ''CARGA'',
                                ''CARGA NACIONAL COMPUTARIZADA''
                                );';



              IF(v_conexion!='OK') THEN
              		raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
              ELSE
                       perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);

                       perform dblink_exec(v_cadena_cnx,v_consulta_inser,TRUE);

              	v_res_cone=(select dblink_disconnect());

              END IF;

          /*Establecemos la conexion con la base de datos*/
            v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();
            v_conexion = (SELECT dblink_connect(v_cadena_cnx));
          /************************************************/



              IF(v_conexion!='OK') THEN
              		raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
              	ELSE
                   perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);

              	v_res_cone=(select dblink_disconnect());

              END IF;
              /************************************/
        /*****************************************************************/




			/*Replicacion a la base de datos DB_FACTURAS 2019*/
   		/*Para migrar los datos a la nueva base de datos db_facturas_2019*/

          /*Establecemos la conexion con la base de datos*/
           /* v_cadena_cnx = migra.f_obtener_cadena_conexion();
            v_conexion = (SELECT dblink_connect(v_cadena_cnx));
          /************************************************/

                v_consulta = '
                            update sfe.tfactura set
                            codigo_control = Null,
                            importe_total_venta = 0,
                            razon_social_cli = ''ANULADO'',
                            nit_ci_cli = ''0'',
                            estado = ''anulado'',
                            nro_autorizacion = Null
                            where id_origen = '''||v_parametros.id_origen||''' and sistema_origen = ''CARGA'';
                            ';


              IF(v_conexion!='OK') THEN
              		raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
              ELSE

              	perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);

              	v_res_cone=(select dblink_disconnect());

              END IF;
*/
              /************************************/
        /*****************************************************************/


        /*************************************************/

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Fáctura Anulada');

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
