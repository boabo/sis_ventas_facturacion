CREATE OR REPLACE FUNCTION vef.ft_regularizacion_facturas_no_replicadas (
  p_id_usuario integer,
  p_tipo_factura varchar,
  p_tipo_punto_venta varchar,
  p_gestion_base_datos integer
)
RETURNS varchar AS
$body$
DECLARE
	v_consulta    		varchar;
	v_registros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
    v_datos					record;

    v_cadena_cnx		varchar;
    v_conexion			varchar;
    v_id_factura		integer;
    v_tipo_pv			varchar;
	v_cajero			varchar;
    v_res_cone			varchar;
    v_consulta_2		varchar;


BEGIN
	  v_nombre_funcion = 'vef.ft_regularizacion_facturas_no_replicadas ';



         /*Establecemos la conexion con la base de datos*/
            v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();
            v_conexion = (SELECT dblink_connect(v_cadena_cnx));
          /*************************************************/



            /*Recuperamos el nombre del cajero que esta finalizando la factura*/
            SELECT per.nombre_completo2 into v_cajero
            from segu.tusuario usu
            inner join segu.vpersona2 per on per.id_persona = usu.id_persona
            where usu.id_usuario = p_id_usuario;
            /******************************************************************/

            if (p_tipo_factura = 'manual' and p_tipo_punto_venta = 'ato') THEN
            	v_tipo_pv= 'FAC.BOL.MANUAL.CONTABLE ATO';
            ELSIF (p_tipo_factura = 'manual' and p_tipo_punto_venta = 'cto') then
            	v_tipo_pv= 'FAC.BOL.MANUAL.CONTABLE CTO';
            ELSIF (p_tipo_factura = 'manual' and p_tipo_punto_venta = 'carga') then
            	v_tipo_pv= 'FAC.BOL.MANUAL.CONTABLE CARGA';
            end if;

            for v_registros in (select 	ven.id_venta,
            							ven.fecha,
                                        ven.nro_factura,
                                        ven.nit,
                                        ven.nombre_factura,
                                        ven.total_venta,
                                        ven.excento,
                                        ven.tipo_factura
                                from vef.tventa ven
                                where (ven.estado = 'finalizado' or ven.estado = 'anulado')
                                and (ven.tipo_factura = 'computarizada' or ven.tipo_factura = 'manual')
                                and ven.id_venta != 190
                                and ven.id_venta not in (SELECT *
                                FROM dblink('dbname=db_facturas_'||p_gestion_base_datos||' options=-csearch_path=',
                                'select
                                        id_origen
                                from sfe.tfactura
                                where sistema_origen = ''ERP''
                                ')
                                AS tdatos(id_origen integer))) loop


                      	select * FROM dblink(v_cadena_cnx,'select nextval(''sfe.tfactura_id_factura_seq'')',TRUE)AS t1(resp integer)
          				into v_id_factura;

                         v_consulta = 'INSERT INTO sfe.tfactura(
                                          id_factura,
                                          fecha_factura,
                                          nro_factura,
                                          estado,
                                          nit_ci_cli,
                                          razon_social_cli,
                                          importe_total_venta,
                                          importe_otros_no_suj_iva,
                                          usuario_reg,
                                          tipo_factura,
                                          id_origen,
                                          sistema_origen,
                                          desc_ruta
                                          )
                                          values(
                                          '||v_id_factura||',
                                          '''||v_registros.fecha||''',
                                          '''||v_registros.nro_factura::varchar||''',
                                          ''CONTINGENCIA'',
                                          '''||v_registros.nit::varchar||''',
                                          '''||v_registros.nombre_factura::varchar||''',
                                          '||v_registros.total_venta::numeric||',
                                          '||v_registros.excento||',
                                          '''||v_cajero||''',
                                          '''||v_registros.tipo_factura||''',
                                          '||v_registros.id_venta||',
                                          ''ERP'',
                                          '''||v_tipo_pv::varchar||'''
                                          );';



                            IF(v_conexion!='OK') THEN
                            --raise notice 'Aqui 1';
                                  raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
                            ELSE

                              perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);

                              --v_res_cone=(select dblink_disconnect());

                            END IF;

            end loop;








    return 'exito';

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

ALTER FUNCTION vef.ft_regularizacion_facturas_no_replicadas (p_id_usuario integer, p_tipo_factura varchar, p_tipo_punto_venta varchar, p_gestion_base_datos integer)
  OWNER TO postgres;
