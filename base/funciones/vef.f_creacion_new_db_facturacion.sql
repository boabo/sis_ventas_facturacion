CREATE OR REPLACE FUNCTION vef.f_creacion_new_db_facturacion (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body1$
  /**************************************************************************
   SISTEMA:		Sistema de Ventas
   FUNCION: 		vef.f_creacion_new_db_facturacion
   DESCRIPCION:   Funcion que creara la nueva base de datos donde se replicaran las facturas de la gestion actual
   AUTOR: 		 (Ismael Valdivia)
   FECHA:	        11-05-2021 16:30:56
   COMENTARIOS:
  ***************************************************************************
   HISTORIAL DE MODIFICACIONES:

   DESCRIPCION:
   AUTOR:
   FECHA:
  ***************************************************************************/

  DECLARE

    v_parametros           	record;
    v_resp		            varchar;
    v_nombre_funcion        text;
    v_mensaje				varchar;


    v_ok				varchar;

    v_gestion_actual_db 	integer;
    v_gestion_actual_fecha	integer;


    v_new_db	varchar;
    v_name_db	varchar;
    v_cadena_conex	varchar;
    v_conexion	varchar;
    v_conexion_consultas	varchar;

    v_ip_produccion	varchar;
    v_db_produccion	varchar;



  BEGIN

    v_nombre_funcion = 'vef.f_creacion_new_db_facturacion';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
     #TRANSACCION:  'VF_INS_NEW_DB_IME'
     #DESCRIPCION:	Insercion de registros
     #AUTOR:		Ismael Valdivia
     #FECHA:		11-05-2021 16:30:56
    ***********************************/

    if(p_transaccion='VF_INS_NEW_DB_IME')then

      begin

      	v_gestion_actual_db = pxp.f_get_variable_global('gestion_base_datos_facturacion');

        select extract(year from now()::date) into v_gestion_actual_fecha;


        if (v_gestion_actual_fecha > v_gestion_actual_db) then

        v_name_db = 'db_facturas_'||v_gestion_actual_fecha::varchar;
       -- v_name_db = 'db_facturas_20212';

        /*Creamos la conexion para creacion de la nueva base de datos*/
        SELECT dblink_connect('conexion_actual', format('dbname=' || current_database())) into v_new_db;

        SELECT dblink_exec('conexion_actual', 'CREATE DATABASE '||v_name_db||'
                          WITH OWNER = postgres
                          ENCODING = ''UTF8''
                          TABLESPACE = pg_default;') into v_new_db;

        SELECT dblink_disconnect('conexion_actual') into v_new_db;
        /*************************************************************************/

        /*Aqui realizamos el update de la variable global*/
        update pxp.variable_global set
        valor = v_name_db
        where variable = 'sincronizar_base_facturacion';
        /*************************************************/


        /*Recuperamos los datos de coneccion para dblink de la nueva base de datos*/
        select vef.f_obtener_cadena_conexion_facturacion() into v_cadena_conex;
        /**************************************************************************/

        --raise exception 'Aqui llega conexion %',v_cadena_conex;
        /*Establecemos la conexion*/
        SELECT dblink_connect('conn', format(''||v_cadena_conex||'')) into v_ok;
        /**************************************************/


        /*Ejecutamos los scripts de las tablas funciones trigguers etc*/

        /****Crear el tbase que se heredara en el esquema public*****/
        SELECT dblink_exec('conn', 'CREATE TABLE public.tbase (
                                    usuario_reg VARCHAR(50),
                                    usuario_mod VARCHAR(50),
                                    fecha_reg TIMESTAMP WITHOUT TIME ZONE DEFAULT now(),
                                    fecha_mod TIMESTAMP WITHOUT TIME ZONE DEFAULT now(),
                                    estado_reg VARCHAR(10) DEFAULT ''activo''::character varying
                                  )
                                  WITH (oids = false);

                                  ALTER TABLE public.tbase
                                    OWNER TO postgres;') into v_ok;
        /**************************************************************/

        /*Creamos el schema sfe para la tabla tfactura donde se ira almacenando la data*/
        SELECT dblink_exec('conn', 'CREATE SCHEMA sfe AUTHORIZATION postgres;
                                    ALTER SCHEMA sfe
                                    OWNER TO postgres;') into v_ok;
        /*******************************************************************************/

        /************************************************Creacion de las funciones*********************************/

        /*Creamoos la funcion del user_mapping*/

        SELECT dblink_exec('conn', 'CREATE OR REPLACE FUNCTION sfe.f_user_mapping (
                                    )
                                    RETURNS void AS''
                                    DECLARE
                                      v_user varchar;
                                      v_pass varchar;
                                      v_te varchar;
                                      v_existe_user	numeric;
                                    BEGIN


                                      v_user = ''''user_facturacion_erp'''';
                                      v_pass = ''''user_facturacion_erp@2019'''';


                                      execute ''''CREATE USER MAPPING IF NOT EXISTS FOR "'''' || current_user || ''''"
                                               SERVER fdw_erp_produccion
                                               OPTIONS (user '''''''''''' || v_user || '''''''''''' , password '''''''''''' || v_pass || '''''''''''')'''';

                                    END;
                                    ''LANGUAGE ''plpgsql''
                                    VOLATILE
                                    CALLED ON NULL INPUT
                                    SECURITY INVOKER
                                    COST 100;

                                    ALTER FUNCTION sfe.f_user_mapping ()
                                      OWNER TO postgres;') into v_ok;

        /******************************************************************/

        /*Creamos la funcion sfe.ft_actualizar_cod_auxiliar*/
        SELECT dblink_exec('conn', 'CREATE OR REPLACE FUNCTION sfe.ft_actualizar_cod_auxiliar (
                                    )
                                    RETURNS varchar AS''
                                    DECLARE
                                        v_consulta    		varchar;
                                        v_registros  		record;
                                        v_nombre_funcion   	text;
                                        v_resp				varchar;
                                        v_datos				record;

                                        v_cadena_cnx		varchar;
                                        v_conexion			varchar;
                                        v_id_factura		integer;
                                        v_tipo_pv			varchar;
                                        v_cajero			varchar;
                                        v_res_cone			varchar;
                                        v_consulta_2		varchar;
                                        v_consulta_ins		varchar;
                                        v_id_cliente		integer;
                                        v_codigo_pv			varchar;

                                    BEGIN
                                          v_nombre_funcion = ''''sfe.ft_actualizar_cod_auxiliar '''';

                                          for v_registros in (select fv.codigo_punto_venta,
                                                                     fv.id_origen
                                                              from sfe.tfactura fac
                                                              inner join sfe.tfacturas_pendientes_validas fv on fv.id_origen = fac.id_origen
                                                              where fac.fecha_factura between ''''01/01/2021'''' and ''''31/01/2021''''
                                                              and fac.sistema_origen = ''''CARGA'''' and fac.estado_reg = ''''activo''''
                                                              and fac.estado = ''''VÁLIDA''''
                                                              and fac.codigo_punto_venta is null
                                                              order by fac.id_origen
                                                              ) loop

                                                              update sfe.tfactura set
                                                              codigo_punto_venta = v_registros.codigo_punto_venta
                                                              where id_origen = v_registros.id_origen and sistema_origen = ''''CARGA'''' and estado_reg = ''''activo''''
                                                              and estado = ''''VÁLIDA'''';



                                          end loop;
                                        return ''''Registro exitoso'''';

                                    EXCEPTION
                                        WHEN OTHERS THEN
                                                --update a la tabla informix.migracion

                                                return SQLERRM;

                                    END;
                                    ''LANGUAGE ''plpgsql''
                                    VOLATILE
                                    CALLED ON NULL INPUT
                                    SECURITY INVOKER
                                    COST 100;

                                    ALTER FUNCTION sfe.ft_actualizar_cod_auxiliar ()
                                      OWNER TO postgres;') into v_ok;


        /*Creamos la funcion sfe.ft_actualizar_nro_autorizacion*/
        SELECT dblink_exec('conn', 'CREATE OR REPLACE FUNCTION sfe.ft_actualizar_nro_autorizacion (
                                    )
                                    RETURNS varchar AS''
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
                                        v_consulta_ins		varchar;

                                    BEGIN
                                          v_nombre_funcion = ''''sfe.ft_actualizar_nro_autorizacion'''';

                                            for v_registros in ( select  fac.nro_autorizacion,
                                                                          fac.id_origen,
                                                                          fac.sistema_origen,
                                                                          fac.estado
                                                                from sfe.tfactura fac
                                                                where fac.nro_autorizacion != ''''0'''' and fac.nro_autorizacion != ''''''''
                                                                and fac.sistema_origen = ''''CARGA'''' and fac.estado != ''''NO UTILIZADA''''
                                                                and fac.estado_reg = ''''inactivo''''
                                                                ) loop

                                            update sfe.tfactura
                                            set nro_autorizacion = v_registros.nro_autorizacion
                                            where id_origen = v_registros.id_origen and sistema_origen = v_registros.sistema_origen
                                            and estado = ''''ANULADA'''' and estado_reg = ''''activo'''';

                                            end loop;


                                        return ''''exito'''';

                                    EXCEPTION
                                        WHEN OTHERS THEN
                                                --update a la tabla informix.migracion

                                                return SQLERRM;

                                    END;
                                    ''LANGUAGE ''plpgsql''
                                    VOLATILE
                                    CALLED ON NULL INPUT
                                    SECURITY INVOKER
                                    COST 100;

                                    ALTER FUNCTION sfe.ft_actualizar_nro_autorizacion ()
                                      OWNER TO postgres;') into v_ok;

        /*Creamos la funcion sfe.ft_replicar_facturas_pendientes*/
        SELECT dblink_exec('conn', 'CREATE OR REPLACE FUNCTION sfe.ft_replicar_facturas_pendientes (
                                      p_estado varchar,
                                      p_fecha_inicio date,
                                      p_fecha_fin date
                                    )
                                    RETURNS varchar AS''
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
                                        v_consulta_ins		varchar;
                                        v_id_cliente		integer;
                                        v_existe			numeric;
                                        v_insertar_anuladas	record;

                                    BEGIN
                                          v_nombre_funcion = ''''sfe.ft_replicar_facturas_pendientes '''';

                                          if (p_estado = ''''validas'''') then

                                            for v_registros in (select fv.fecha,
                                                                       fv.nro_factura,
                                                                       fv.nro_autorizacion,
                                                                       ''''VÁLIDA''''::varchar as estado,
                                                                       fv.nit,
                                                                       fv.razon_social,
                                                                       fv.importe_total,
                                                                       0::numeric as importe_otros_no_suj_iva,
                                                                       fv.codigo_control,
                                                                       ''''CARGA''''::varchar as sistema_origen,
                                                                       fv.id_origen,
                                                                       ''''computarizada''''::varchar as tipo_factura,
                                                                       ''''admin''''::varchar as usuario_reg,
                                                                       ''''CARGA NACIONAL COMPUTARIZADA''''::varchar as desc_ruta
                                                                from sfe.tfacturas_pendientes_validas fv
                                                                where fv.id_origen not in (select fac.id_origen
                                                                                            from sfe.tfactura fac
                                                                                            where fac.sistema_origen = ''''CARGA''''
                                                                                            and fac.estado_reg = ''''activo''''
                                                                                            and fac.fecha_factura between p_fecha_inicio and p_fecha_fin
                                                                                            and fac.estado = ''''VÁLIDA'''')
                                                                ) loop

                                                            select nextval(''''sfe.tfactura_id_factura_seq'''') into v_id_factura;

                                                            INSERT INTO sfe.tfactura(
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
                                                                v_id_factura,
                                                                v_registros.fecha::date,
                                                                v_registros.nro_factura::varchar,
                                                                v_registros.nro_autorizacion::varchar,
                                                                v_registros.estado::varchar,
                                                                v_registros.nit::varchar,
                                                                v_registros.razon_social::varchar,
                                                                v_registros.importe_total::numeric,
                                                                v_registros.importe_otros_no_suj_iva::numeric,
                                                                v_registros.codigo_control::varchar,--decomentar
                                                                v_registros.sistema_origen,
                                                                v_registros.id_origen::integer,
                                                                v_registros.tipo_factura,
                                                                v_registros.usuario_reg::varchar,
                                                                v_registros.desc_ruta
                                                                );

                                            end loop;

                                          elsif (p_estado = ''''anuladas'''') then

                                            for v_registros in (	select fv.fecha,
                                                                         fv.nro_factura,
                                                                         ''''ANULADA''''::varchar as estado,
                                                                         ''''0''''::varchar as nit,
                                                                         ''''ANULADA''''::varchar as razon_social_cli,
                                                                         fv.importe_total,
                                                                         ''''admin''''::varchar as usuario_reg,
                                                                         ''''computarizada''''::varchar as tipo_factura,
                                                                         fv.id_origen,
                                                                         ''''CARGA''''::varchar as sistema_origen,
                                                                         fv.nro_autorizacion
                                                                  from sfe.tfacturas_pendientes_anuladas fv
                                                                  where fv.id_origen not in (select fac.id_origen
                                                                                              from sfe.tfactura fac
                                                                                              where fac.sistema_origen = ''''CARGA''''
                                                                                              and fac.estado_reg = ''''activo''''
                                                                                              and fac.fecha_factura between p_fecha_inicio and p_fecha_fin
                                                                                              and fac.estado = ''''ANULADA'''')
                                                                  --ORDER BY fv.id_origen DESC
                                                                  --limit 1
                                                                  )
                                            LOOP

                                                    /*Insertamos la factura si no esta registrada para tener el historico*/

                                                    select count(fac.id_factura)
                                                    into v_existe
                                                    from sfe.tfactura fac
                                                    where fac.id_origen = v_registros.id_origen and fac.estado_reg = ''''activo'''' and fac.sistema_origen = ''''CARGA'''';

                                                    if (v_existe = 0) then

                                                    select nextval(''''sfe.tfactura_id_factura_seq'''') into v_id_factura;

                                                        select fv.fecha,
                                                               fv.nro_factura,
                                                               ''''VÁLIDA''''::varchar as estado,
                                                               fv.nit,
                                                               fv.razon_social,
                                                               fv.importe_total,
                                                               ''''admin''''::varchar as usuario_reg,
                                                               ''''computarizada''''::varchar as tipo_factura,
                                                               fv.id_origen,
                                                               ''''CARGA''''::varchar as sistema_origen,
                                                               fv.nro_autorizacion,
                                                               fv.codigo_control
                                                        into
                                                        v_insertar_anuladas
                                                        from sfe.tfacturas_pendientes_anuladas fv
                                                        where fv.id_origen = v_registros.id_origen;


                                                        INSERT INTO sfe.tfactura(
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
                                                                v_id_factura,
                                                                v_insertar_anuladas.fecha::date,
                                                                v_insertar_anuladas.nro_factura::varchar,
                                                                v_insertar_anuladas.nro_autorizacion::varchar,
                                                                v_insertar_anuladas.estado::varchar,
                                                                v_insertar_anuladas.nit::varchar,
                                                                v_insertar_anuladas.razon_social::varchar,
                                                                v_insertar_anuladas.importe_total::numeric,
                                                                0::numeric,
                                                                v_insertar_anuladas.codigo_control::varchar,--decomentar
                                                                ''''CARGA'''',
                                                                v_insertar_anuladas.id_origen::integer,
                                                                ''''computarizada'''',
                                                                ''''admin''''::varchar,
                                                                ''''CARGA NACIONAL COMPUTARIZADA''''
                                                                );
                                                    end if;
                                                    /*********************************************************************/


                                                    /*Cambiamos el estado de la factura*/
                                                    update sfe.tfactura set
                                                    estado_reg = ''''inactivo''''
                                                    where id_origen = v_registros.id_origen and sistema_origen = ''''CARGA'''' and estado <> ''''ANULADA'''';
                                                    /**********************************/

                                                    select nextval(''''sfe.tfactura_id_factura_seq'''') into v_id_factura;

                                                    /*Insertamos el dato de la factura*/
                                                    INSERT INTO sfe.tfactura(
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
                                                                    nro_autorizacion
                                                                    )
                                                                    values(
                                                                    v_id_factura,
                                                                    v_registros.fecha,
                                                                    v_registros.nro_factura::varchar,
                                                                    v_registros.estado,
                                                                    v_registros.nit,
                                                                    v_registros.razon_social_cli,
                                                                    0::numeric,
                                                                    0::numeric,
                                                                    v_registros.usuario_reg,
                                                                    v_registros.tipo_factura,
                                                                    v_registros.id_origen,
                                                                    v_registros.sistema_origen,
                                                                    v_registros.nro_autorizacion
                                                                    );
                                                    /**********************************/



                                            END LOOP;




                                          end if;


                                        return ''''Registro exitoso'''';

                                    EXCEPTION
                                        WHEN OTHERS THEN
                                                --update a la tabla informix.migracion

                                                return SQLERRM;

                                    END;
                                    ''LANGUAGE ''plpgsql''
                                    VOLATILE
                                    CALLED ON NULL INPUT
                                    SECURITY INVOKER
                                    COST 100;

                                    ALTER FUNCTION sfe.ft_replicar_facturas_pendientes (p_estado varchar, p_fecha_inicio date, p_fecha_fin date)
                                      OWNER TO postgres;') into v_ok;

        /*Creamos la funcion sfe.privilegios_esquema_set_objetos_dbkerp*/
        SELECT dblink_exec('conn', 'CREATE OR REPLACE FUNCTION sfe.privilegios_esquema_set_objetos_dbkerp (
                                        p_user text,
                                        p_schema text
                                      )
                                      RETURNS void AS
                                      $body$
                                      DECLARE
                                         objeto 		text;

                                      BEGIN

                                              FOR objeto IN
                                                  SELECT tablename FROM pg_tables WHERE schemaname = p_schema
                                                  UNION
                                                  SELECT relname FROM pg_statio_all_sequences WHERE schemaname = p_schema
                                                  UNION
                                                  SELECT viewname FROM pg_views WHERE schemaname = p_schema LOOP

                                                RAISE NOTICE ''Asignando todos los privilegios a % sobre %.%'', p_user, p_schema, objeto;
                                                EXECUTE ''GRANT ALL PRIVILEGES ON "'' || p_schema || ''"."'' || objeto || ''" TO '' || p_user ;

                                              END LOOP;

                                              EXECUTE ''GRANT EXECUTE ON ALL FUNCTIONS IN SCHEMA ''||p_schema||'' TO ''|| p_user ;
                                              EXECUTE ''GRANT USAGE ON SCHEMA ''||p_schema||'' TO ''|| p_user ;
                                      END;
                                      $body$
                                      LANGUAGE ''plpgsql''
                                      VOLATILE
                                      CALLED ON NULL INPUT
                                      SECURITY INVOKER
                                      COST 100;

                                      ALTER FUNCTION sfe.privilegios_esquema_set_objetos_dbkerp (p_user text, p_schema text)
                                        OWNER TO postgres;') into v_ok;

        /*******************************************************************************************************************/


        /*Creamos la funcion del Trigger*/


        SELECT dblink_exec('conn', 'CREATE OR REPLACE FUNCTION sfe.f_trigger_control_facturas (
        )
        RETURNS trigger AS''
        /**************************************************************************
         SISTEMA ERP
        ***************************************************************************
         SCRIPT: 		trigger control facturas
         DESCRIPCIÓN: 	Controlara el insertar, modificar y eliminar de las facturas en la tabla sfe.tfactura
         AUTOR: 		Ismael.Valdivia
         FECHA:			20-04-2021
         COMENTARIOS:
        ***************************************************************************
         HISTORIA DE MODIFICACIONES:

        ***************************************************************************/
        --------------------------
        -- CUERPO DE LA FUNCIÓN --
        --------------------------

        --**** DECLARACION DE VARIABLES DE LA FUNCIÓN (LOCALES) ****---


        DECLARE
            --PARÁMETROS FIJOS
            v_periodo record;
            v_periodo_anterior	record;
            v_mapping	varchar;
        BEGIN

              --*** EJECUCIÓN DEL PROCEDIMIENTO ESPECÍFICO


            IF TG_OP = ''''INSERT'''' THEN

              BEGIN

                    select sfe.f_user_mapping() into v_mapping;


                    select dep.nombre,
                           cv.estado,
                           per.fecha_ini,
                           per.fecha_fin
                    into
                           v_periodo
                    from sfe.tperiodo_compra_venta cv
                    inner join sfe.tperiodo per on per.id_periodo = cv.id_periodo
                    inner join sfe.tdepto dep on dep.id_depto = cv.id_depto
                    where dep.codigo = ''''CON''''
                    and new.fecha_factura between per.fecha_ini and per.fecha_fin;

                    if (v_periodo.estado = ''''cerrado'''') then
                        raise exception ''''No se puede insertar la factura Nro: % de Fecha: % con Nro Autorizacion: %. Debido a que el periodo se encuentra cerrado'''',new.nro_factura,new.fecha_factura,new.nro_autorizacion;
                    end if;

              END;

           ELSIF TG_OP = ''''UPDATE'''' THEN

                BEGIN
                    select sfe.f_user_mapping() into v_mapping;

                    /*Controlamos la fecha actual para verificar el periodo actual*/

                    select dep.nombre,
                           cv.estado,
                           per.fecha_ini,
                           per.fecha_fin
                    into
                           v_periodo
                    from sfe.tperiodo_compra_venta cv
                    inner join sfe.tperiodo per on per.id_periodo = cv.id_periodo
                    inner join sfe.tdepto dep on dep.id_depto = cv.id_depto
                    where dep.codigo = ''''CON''''
                    and OLD.fecha_factura between per.fecha_ini and per.fecha_fin;

                    if (v_periodo.estado = ''''cerrado'''') then
                        raise exception ''''No se puede modificar la factura Nro: % de Fecha: % con Nro Autorizacion: %. Debido a que el periodo se encuentra cerrado'''',old.nro_factura,old.fecha_factura,old.nro_autorizacion;
                    end if;


                    /*******************************************************************/

                    /*Controlamos la fecha nueva para verificar que no se modifique a un periodo anterior*/

                    select dep.nombre,
                           cv.estado,
                           per.fecha_ini,
                           per.fecha_fin
                    into
                           v_periodo_anterior
                    from sfe.tperiodo_compra_venta cv
                    inner join sfe.tperiodo per on per.id_periodo = cv.id_periodo
                    inner join sfe.tdepto dep on dep.id_depto = cv.id_depto
                    where dep.codigo = ''''CON''''
                    and new.fecha_factura between per.fecha_ini and per.fecha_fin;

                    if (v_periodo_anterior.estado = ''''cerrado'''') then
                        raise exception ''''Los datos de la nueva factura pertenecen a un periodo que se encuentra cerrado'''';
                    end if;

                    /*************************************************************************************/


                END;

          --procedimiento de eliminación de usuario

           ELSIF TG_OP = ''''DELETE'''' THEN

                BEGIN
                    select sfe.f_user_mapping() into v_mapping;

                    select dep.nombre,
                           cv.estado,
                           per.fecha_ini,
                           per.fecha_fin
                    into
                           v_periodo
                    from sfe.tperiodo_compra_venta cv
                    inner join sfe.tperiodo per on per.id_periodo = cv.id_periodo
                    inner join sfe.tdepto dep on dep.id_depto = cv.id_depto
                    where dep.codigo = ''''CON''''
                    and OLD.fecha_factura between per.fecha_ini and per.fecha_fin;

                    if (v_periodo.estado = ''''cerrado'''') then
                        raise exception ''''No se puede Eliminar la factura Nro: % de Fecha: % con Nro Autorizacion: %. Debido a que el periodo se encuentra cerrado'''',old.nro_factura,old.fecha_factura,old.nro_autorizacion;
                    end if;
                END;

           END IF;

           RETURN NULL;

        END;
        ''LANGUAGE ''plpgsql''
        VOLATILE
        CALLED ON NULL INPUT
        SECURITY INVOKER
        COST 100;

        ALTER FUNCTION sfe.f_trigger_control_facturas ()
          OWNER TO postgres;') into v_ok;

        /********************************/


        /*Creamos la tabla sfe.tcuenta_corriente_actua*/
        SELECT dblink_exec('conn', 'CREATE TABLE sfe.tcuenta_corriente_actua (
                                    nro_factura VARCHAR(200),
                                    id_origen INTEGER,
                                    codigo_auxiliar VARCHAR(200),
                                    codigo_punto_venta VARCHAR(200)
                                  )
                                  WITH (oids = false);

                                  ALTER TABLE sfe.tcuenta_corriente_actua
                                    OWNER TO postgres;') into v_ok;

        /*Creamos la tabla sfe.tfacturas_pendientes_anuladas*/
        SELECT dblink_exec('conn', 'CREATE TABLE sfe.tfacturas_pendientes_anuladas (
                                        fecha DATE,
                                        nro_factura VARCHAR(200),
                                        estado VARCHAR(100),
                                        nit VARCHAR(200),
                                        razon_social VARCHAR(200),
                                        importe_total NUMERIC(18,2),
                                        usuario_registro VARCHAR(200),
                                        id_origen INTEGER,
                                        codigo_control VARCHAR(200),
                                        nro_autorizacion VARCHAR(500)
                                      )
                                      WITH (oids = false);

                                      ALTER TABLE sfe.tfacturas_pendientes_anuladas
                                        OWNER TO postgres;') into v_ok;

        /*Creamos la tabla sfe.tfacturas_pendientes_validas*/
        SELECT dblink_exec('conn', 'CREATE TABLE sfe.tfacturas_pendientes_validas (
                                    fecha DATE,
                                    nro_factura VARCHAR(200),
                                    estado VARCHAR(100),
                                    nit VARCHAR(200),
                                    razon_social VARCHAR(200),
                                    importe_total NUMERIC(18,2),
                                    usuario_registro VARCHAR(200),
                                    id_origen INTEGER,
                                    codigo_control VARCHAR(200),
                                    nro_autorizacion VARCHAR(500),
                                    codigo_auxiliar VARCHAR(200),
                                    codigo_punto_venta VARCHAR(200)
                                  )
                                  WITH (oids = false);

                                  CREATE INDEX tfacturas_pendientes_validas_fecha ON sfe.tfacturas_pendientes_validas
                                    USING btree (codigo_auxiliar COLLATE pg_catalog."default", codigo_punto_venta COLLATE pg_catalog."default", fecha);

                                  ALTER TABLE sfe.tfacturas_pendientes_validas
                                    OWNER TO postgres;') into v_ok;

        /*Creamos la tabla sfe.tfactura*/


        SELECT dblink_exec('conn', 'CREATE TABLE sfe.tfactura (
                                    id_factura SERIAL,
                                    fecha_factura DATE,
                                    nro_factura VARCHAR(50),
                                    nro_autorizacion VARCHAR(50) DEFAULT ''0''::character varying,
                                    estado VARCHAR(20),
                                    nit_ci_cli VARCHAR(20),
                                    razon_social_cli VARCHAR(100),
                                    importe_total_venta NUMERIC(18,2),
                                    importe_otros_no_suj_iva NUMERIC(18,2),
                                    exportacion_excentas NUMERIC(18,2) DEFAULT 0,
                                    ventas_tasa_cero NUMERIC(18,2) DEFAULT 0,
                                    descuento_rebaja_suj_iva NUMERIC(18,2) DEFAULT 0,
                                    importe_debito_fiscal NUMERIC(18,2) DEFAULT 0,
                                    codigo_control VARCHAR(50) DEFAULT ''0''::character varying,
                                    tipo_factura VARCHAR(200),
                                    id_origen INTEGER NOT NULL,
                                    sistema_origen VARCHAR(200),
                                    desc_ruta VARCHAR(500),
                                    revision_nit VARCHAR(200),
                                    otr VARCHAR(1000),
                                    moneda VARCHAR(5),
                                    origen_servicio VARCHAR(3),
                                    nombre_pasajero VARCHAR(300),
                                    tipo_venta VARCHAR(200),
                                    codigo_auxiliar VARCHAR(200),
                                    codigo_punto_venta VARCHAR(200),
                                    pais_emision VARCHAR(5),
                                    CONSTRAINT tfactura_pkey PRIMARY KEY(id_factura)
                                  ) INHERITS (public.tbase)
                                  WITH (oids = false);

                                  COMMENT ON COLUMN sfe.tfactura.importe_otros_no_suj_iva
                                  IS ''Importe Exento de la venta'';

                                  COMMENT ON COLUMN sfe.tfactura.desc_ruta
                                  IS ''Campo para almacenar la ruta u otra descripcion de boletos y facturas'';

                                  COMMENT ON COLUMN sfe.tfactura.revision_nit
                                  IS ''Almacena el estado, el cual verificara los nits a corregir'';

                                  COMMENT ON COLUMN sfe.tfactura.otr
                                  IS ''Campo que se recupera para boleto de archivo ret.'';

                                  COMMENT ON COLUMN sfe.tfactura.moneda
                                  IS ''Moneda en la que se emitió el pasaje.'';

                                  COMMENT ON COLUMN sfe.tfactura.origen_servicio
                                  IS ''Ciudad de inicio del viaje (Ej.LBP, LIM, etc.)'';

                                  COMMENT ON COLUMN sfe.tfactura.nombre_pasajero
                                  IS ''Nombre de la Persona que viaja o remitente de la carga.'';

                                  COMMENT ON COLUMN sfe.tfactura.tipo_venta
                                  IS ''Tipo de venta si es propia o no.'';

                                  COMMENT ON COLUMN sfe.tfactura.codigo_auxiliar
                                  IS ''Campo donde se almacena el codigo de la cuenta auxiliar momentaneamente'';

                                  COMMENT ON COLUMN sfe.tfactura.codigo_punto_venta
                                  IS ''Campo donde se almacena el codigo del punto de venta para relacionar los datos'';

                                  CREATE INDEX tfactura_idx ON sfe.tfactura
                                    USING btree (nro_factura COLLATE pg_catalog."default");

                                  CREATE UNIQUE INDEX tfactura_idx1 ON sfe.tfactura
                                    USING btree (nro_factura COLLATE pg_catalog."default", nro_autorizacion COLLATE pg_catalog."default", estado COLLATE pg_catalog."default", estado_reg COLLATE pg_catalog."default", id_origen);

                                  CREATE TRIGGER trigger_control_facturas
                                    AFTER INSERT OR UPDATE OR DELETE
                                    ON sfe.tfactura

                                  FOR EACH ROW
                                    EXECUTE PROCEDURE sfe.f_trigger_control_facturas();

                                  ALTER TABLE sfe.tfactura
                                    OWNER TO postgres;') into v_ok;

        /*********************************************************************************************/

        /**************************************************************/

        /*Instalacion del fdw dblink*/
        --SELECT * FROM pg_extension

        /*Recuperamos los datos para hacer conexion a produccion*/

        v_ip_produccion = pxp.f_get_variable_global('ip_produccion');
        v_db_produccion = pxp.f_get_variable_global('db_name_produccion');


        SELECT dblink_exec('conn', 'CREATE EXTENSION dblink;
        							ALTER FOREIGN DATA WRAPPER dblink_fdw
                                    OWNER TO postgres;
                                    GRANT ALL PRIVILEGES ON FOREIGN DATA WRAPPER dblink_fdw TO postgres;
                                    GRANT ALL PRIVILEGES ON FOREIGN DATA WRAPPER dblink_fdw TO privilegios_objetos_dbkerp;') into v_ok;

        SELECT dblink_exec('conn', 'CREATE EXTENSION postgres_fdw;
        							ALTER FOREIGN DATA WRAPPER postgres_fdw
                                    OWNER TO postgres;
                                    GRANT ALL PRIVILEGES ON FOREIGN DATA WRAPPER postgres_fdw TO postgres;
                                    GRANT ALL PRIVILEGES ON FOREIGN DATA WRAPPER postgres_fdw TO privilegios_objetos_dbkerp;') into v_ok;

        SELECT dblink_exec('conn', 'CREATE SERVER fdw_erp_produccion
                                    FOREIGN DATA WRAPPER postgres_fdw
                                    OPTIONS (host '''||v_ip_produccion||''', port ''5432'', dbname '''||v_db_produccion||''');

                                    ALTER SERVER fdw_erp_produccion
                                    OWNER TO postgres;

                                    GRANT ALL PRIVILEGES ON FOREIGN SERVER fdw_erp_produccion TO postgres;
                                    GRANT ALL PRIVILEGES ON FOREIGN SERVER fdw_erp_produccion TO privilegios_objetos_dbkerp;
                                    ') into v_ok;

        /*****************************************************************/

        /*Mapeamos al usuario para la creacion de las tablas espejos*/

        SELECT dblink_exec('conn', 'CREATE USER MAPPING FOR CURRENT_USER
        SERVER fdw_erp_produccion
        OPTIONS (user ''user_facturacion_erp'', password ''user_facturacion_erp@2019'');') into v_ok;


        /************************************************************/

        /*Creamos las tablas espejos para el control del periodo cerrado*/
        SELECT dblink_exec('conn', 'IMPORT FOREIGN SCHEMA conta LIMIT TO (tperiodo_compra_venta)
        FROM SERVER fdw_erp_produccion INTO sfe;

        ALTER TABLE sfe.tperiodo_compra_venta
        OWNER TO postgres;

        GRANT ALL PRIVILEGES ON sfe.tperiodo_compra_venta to postgres;
		GRANT ALL PRIVILEGES ON sfe.tperiodo_compra_venta to privilegios_objetos_dbkerp;

        ') into v_ok;

        SELECT dblink_exec('conn', 'IMPORT FOREIGN SCHEMA param LIMIT TO (tdepto)
        FROM SERVER fdw_erp_produccion INTO sfe;

        ALTER TABLE sfe.tdepto
        OWNER TO postgres;

        GRANT ALL PRIVILEGES ON sfe.tdepto to postgres;
		GRANT ALL PRIVILEGES ON sfe.tdepto to privilegios_objetos_dbkerp;') into v_ok;

        SELECT dblink_exec('conn', 'IMPORT FOREIGN SCHEMA param LIMIT TO (tperiodo)
        FROM SERVER fdw_erp_produccion INTO sfe;

        ALTER TABLE sfe.tperiodo
        OWNER TO postgres;

        GRANT ALL PRIVILEGES ON sfe.tperiodo to postgres;
		GRANT ALL PRIVILEGES ON sfe.tperiodo to privilegios_objetos_dbkerp;') into v_ok;
        /****************************************************************/


        /*Ejecutamos los permisos de los esquemas*/
        SELECT *
        FROM dblink('conn',
        'select sfe.privilegios_esquema_set_objetos_dbkerp(''privilegios_objetos_dbkerp'', ''sfe'')')
        AS t1(dato varchar) into v_ok;


        SELECT *
        FROM dblink('conn',
        'select sfe.privilegios_esquema_set_objetos_dbkerp(''privilegios_objetos_dbkerp'', ''public'')')
        AS t1(dato varchar) into v_ok;

        SELECT dblink_disconnect('conn') into v_ok;



        update pxp.variable_global set
        valor = v_gestion_actual_fecha::varchar
        where variable = 'gestion_base_datos_facturacion';


        v_mensaje = 'Exito';

        else

        v_mensaje = 'No se creo la base de datos por que la gestion es la misma';

    	end if;



        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'Repuesta', v_mensaje);
        --Devuelve la respuesta
        return v_resp;

      end;

    else

      raise exception 'Transaccion inexistente: %',p_transaccion;

    end if;
 return v_resp;
    EXCEPTION

    WHEN OTHERS THEN
      v_resp='';
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
      v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
      v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
      raise exception '%',v_resp;

  END;
$body1$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION vef.f_creacion_new_db_facturacion (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
