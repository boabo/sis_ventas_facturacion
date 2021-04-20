CREATE OR REPLACE FUNCTION vef.ft_replicar_facturas_pendientes (
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
	v_consulta_ins		varchar;
    v_id_cliente		integer;

BEGIN
	  v_nombre_funcion = 'vef.ft_replicar_facturas_pendientes ';



         /*Establecemos la conexion con la base de datos*/
            v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();
            v_conexion = (SELECT dblink_connect(v_cadena_cnx));
          /*************************************************/

          /*for v_registros in (SELECT
                                    t1.fecha_reg,
                                    t1.estado_reg,
                                    t1.id_venta as id_venta,
                                    sucu.id_sucursal,
                                    pf.id_proceso_wf,
                                    es.id_estado_wf,
                                    pf.nro_tramite,
                                    t1.importe_total_venta,
                                    t1.fecha_factura,
                                   (CASE
                                         WHEN t1.estado = 'VÁLIDA'
                                              THEN 'finalizado'
                                         WHEN t1.estado = 'ANULADA'
                                              THEN 'anulado'
                                     END) estado,
                                     dosi.id_dosificacion,
                                     t1.nro_factura,
                                     t1.fecha_factura as fecha_factura_2,
                                     t1.excento,
                                     t1.tipo_factura,
                                     t1.codigo_control,
                                     usu.id_usuario as id_usuario_cajero,
                                     t1.razon_social_cli,
                                     t1.nit_ci_cli,
                                     per.nombre_completo2,
                                     pv.id_punto_venta

                                  FROM dblink('hostaddr=172.17.110.7 port=5432 dbname=db_facturas_2021 user=user_facturacion_erp password=user_facturacion_erp@2019',
                                              'select
                                                usuario_reg,
                                                fecha_reg,
                                                estado_reg,
                                                id_origen,
                                                ''VEN-''||id_origen::varchar codigo_proceso,
                                                nro_autorizacion,
                                                importe_total_venta,
                                                fecha_factura,
                                                estado,
                                                nro_factura,
                                                importe_otros_no_suj_iva,
                                                tipo_factura,
                                                codigo_control,
                                                 razon_social_cli,
                                                nit_ci_cli
                                            from sfe.tfactura
                                            where sistema_origen = ''ERP''
                                            and estado_reg = ''activo''

                                               ')
                                  AS t1(
                                        usuario_reg varchar,
                                        fecha_reg date,
                                        estado_reg varchar,
                                        id_venta integer,
                                        codigo_proceso varchar,
                                        nro_autorizacion varchar,
                                        importe_total_venta numeric,
                                        fecha_factura date,
                                        estado varchar,
                                        nro_factura varchar,
                                        excento numeric,
                                        tipo_factura varchar,
                                        codigo_control varchar,
                                        razon_social_cli varchar,
                                        nit_ci_cli varchar
                                        )
                                inner join wf.tproceso_wf pf on pf.codigo_proceso = t1.codigo_proceso
                                inner join wf.testado_wf es on es.id_proceso_wf = pf.id_proceso_wf and es.estado_reg = 'activo'
                                inner join vef.tdosificacion dosi on dosi.nroaut = t1.nro_autorizacion
                                inner join vef.tsucursal sucu on sucu.id_sucursal = dosi.id_sucursal
                                inner join segu.vpersona2 per on per.nombre_completo2 = t1.usuario_reg
                                inner join segu.vusuario usu on usu.id_persona = per.id_persona and usu.estado_reg = 'activo'
                                inner join vef.tapertura_cierre_caja aper on aper.fecha_apertura_cierre = '20/02/2021' and aper.id_usuario_cajero = usu.id_usuario
                                inner join vef.tpunto_venta pv on pv.id_punto_venta = aper.id_punto_venta and pv.id_sucursal = sucu.id_sucursal
                                where t1.id_venta not in (
                                    select id_venta
                                    from vef.tventa
                                    where (estado = 'finalizado' or estado = 'anulado')
                                    )
                                and t1.fecha_factura = '20/02/2021'
                                --and t1.id_venta not in (17159)
						        and t1.id_venta = 17159
								and aper.id_apertura_cierre_caja = 40418
                                order by t1.id_venta

                               ) loop


                                /*Aqui recuperamos el id del Cliente*/
                                select cli.id_cliente
                                into
                                v_id_cliente
                                from vef.tcliente cli
                                where upper(trim(cli.nombre_factura)) = upper(trim(v_registros.razon_social_cli)) and cli.nit = v_registros.nit_ci_cli
                                limit 1;
                                /*************************************/

                                if (v_id_cliente is null) then
                                	RAISE exception 'Datos Cliente %, %',v_registros.razon_social_cli, v_registros.nit_ci_cli;
                                end if;



                                INSERT INTO vef.tventa(
                                                        id_usuario_reg,--1
                                                        estado_reg,--2
                                                        id_venta,--3
                                                        id_cliente,--4
                                                        id_sucursal,--5
                                                        id_proceso_wf,--6
                                                        id_estado_wf,--7
                                                        nro_tramite,--8
                                                        total_venta,--9
                                                        a_cuenta,--10
                                                        fecha_estimada_entrega,--11
                                                        estado,--12
                                                        tiene_formula,--13
                                                        id_punto_venta,--14
                                                        correlativo_venta,--15
                                                        porcentaje_descuento,--16
                                                        comision,--17
                                                        observaciones,--18
                                                        id_dosificacion,--19
                                                        nro_factura,--20
                                                        fecha,--21
                                                        excento,--22
                                                        tipo_factura,--23
                                                        cod_control,--24
                                                        id_moneda,--25
                                                        total_venta_msuc,--26
                                                        transporte_fob,--27
                                                        seguros_fob,--27
                                                        otros_fob,--28
                                                        transporte_cif,--29
                                                        seguros_cif,--30
                                                        otros_cif,--31
                                                        tipo_cambio_venta,--32
                                                        valor_bruto,--33
                                                        descripcion_bulto,--34
                                                        id_usuario_cajero,--35
                                                        hora_estimada_entrega,--36
                                                        forma_pedido,--37
                                                        contabilizable,--38
                                                        nombre_factura,--39
                                                        nit,--40
                                                        informe,--41
                                                        anulado,--42
                                                        excento_verificado--43
                                                        )
                                                  values(
                                                        v_registros.id_usuario_cajero,--1
                                                        v_registros.estado_reg,--2
                                                        v_registros.id_venta,--3
                                                        v_id_cliente,--4
                                                        v_registros.id_sucursal,--5
                                                        v_registros.id_proceso_wf,--6
                                                        v_registros.id_estado_wf,--7
                                                        v_registros.nro_tramite,--8
                                                        v_registros.importe_total_venta,--9
                                                        0,--10
                                                        v_registros.fecha_factura,--11
                                                        v_registros.estado,--12
                                                        'no',--13
                                                        v_registros.id_punto_venta,--14
                                                        '',--15
                                                        0,--16
                                                        0,--17
                                                        '',--18
                                                        v_registros.id_dosificacion,--19
                                                        v_registros.nro_factura::integer,--20
                                                        v_registros.fecha_factura,--21
                                                        v_registros.excento,--22
                                                        v_registros.tipo_factura,--23
                                                        v_registros.codigo_control,--24
                                                        1,--25
                                                        v_registros.importe_total_venta,--26
                                                        0,--27
                                                        0,--28
                                                        0,--29
                                                        0,--30
                                                        0,--31
                                                        0,--32
                                                        0,--33
                                                        0,--34
                                                        '',--35
                                                        v_registros.id_usuario_cajero,--36
                                                        now()::time,--37
                                                        'cajero',--38
                                                        'si',--39
                                                        v_registros.razon_social_cli,--40
                                                        v_registros.nit_ci_cli,--41
                                                        'NINGUNO',--42
                                                        'NO',--43
                                                        'no'--44
                                                  );

          				end loop;*/







         /*for v_registros in (SELECT tdatos.*,
                                     dosi.nroaut
                              FROM dblink('hostaddr=172.17.110.7 port=5432 dbname=db_facturas_2021 user=user_facturacion_erp password=user_facturacion_erp@2019 options=-csearch_path=',
                                          'select fac.nro_autorizacion,
                                                  fac.id_origen
                                           from sfe.tfactura fac
                                           where (fac.nro_autorizacion = '''' or fac.nro_autorizacion = ''0'' or fac.nro_autorizacion is null)
                                              and fac.fecha_factura between ''01/01/2021'' and ''31/01/2021''
                                              and fac.sistema_origen = ''ERP''
                                           ')
                              AS tdatos(nro_autorizacion varchar,
                                        id_origen integer)
                              inner join vef.tventa ven on ven.id_venta = tdatos.id_origen
                              --inner join vef.trespaldo_facturas_anuladas ven on ven.id_venta = tdatos.id_origen
                              inner join vef.tdosificacion dosi on dosi.id_dosificacion = ven.id_dosificacion) loop

         	v_consulta = 'update sfe.tfactura set
                          nro_autorizacion = '''||v_registros.nroaut||'''
                          where id_origen = '''||v_registros.id_origen||''' and sistema_origen = ''ERP'';';


            IF(v_conexion!='OK') THEN
                  raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
            ELSE
              perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);
            END IF;

          end loop;*/


           for v_registros in (select pen.*
            				    from vef.tcuenta_corriente_actua pen
                                ) loop


                      /*	select * FROM dblink(v_cadena_cnx,'select nextval(''sfe.tfactura_id_factura_seq'')',TRUE)AS t1(resp integer)
          				into v_id_factura;*/

                        v_consulta = 'update sfe.tfactura set
                                      codigo_auxiliar = '''||v_registros.codigo_auxiliar||''',
                                      codigo_punto_venta = '''||v_registros.codigo_punto_venta||'''
                                      where id_origen = '||v_registros.id_origen||' and sistema_origen = ''CARGA'' and estado_reg = ''activo''
                                      and estado = ''VÁLIDA'';';



                            IF(v_conexion!='OK') THEN
                            --raise notice 'Aqui 1';
                                  raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
                            ELSE

                              perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);

                              --v_res_cone=(select dblink_disconnect());

                            END IF;

            end loop;

          	v_res_cone=(select dblink_disconnect());

            /*for v_registros in (select pen.*
            				    from vef.tfacturas_pendientes pen
                                where pen.fecha between '01/02/2021' and '28/02/2021'
                                ) loop


            		   v_consulta = 'update sfe.tfactura set
                                      estado_reg = ''inactivo''
                                      where id_origen = '''||v_registros.id_origen||''' and sistema_origen = ''CARGA'' and estado <> ''ANULADA'';';




                      	select * FROM dblink(v_cadena_cnx,'select nextval(''sfe.tfactura_id_factura_seq'')',TRUE)AS t1(resp integer)
          				into v_id_factura;

                         v_consulta_ins = 'INSERT INTO sfe.tfactura(
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
                                          desc_ruta,
                                          estado_reg,
                                          codigo_control,
                                          nro_autorizacion
                                          )
                                          values(
                                          '||v_id_factura||',
                                          '''||v_registros.fecha||''',
                                          '''||v_registros.nro_factura::varchar||''',
                                          '''||v_registros.estado::varchar||''',
                                          '''||v_registros.nit::varchar||''',
                                          '''||v_registros.razon_social::varchar||''',
                                          '||v_registros.importe_total::numeric||',
                                          0,
                                          '''||v_registros.usuario_registro||''',
                                          ''computarizada'',
                                          '||v_registros.id_origen||',
                                          ''CARGA'',
                                          ''CARGA NACIONAL COMPUTARIZADA'',
                                          ''activo'',
                                          '''||v_registros.codigo_control||''',
                                          '''||v_registros.nro_autorizacion||'''
                                          );';



                            IF(v_conexion!='OK') THEN
                                  raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
                            ELSE

                              perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);

                              perform dblink_exec(v_cadena_cnx,v_consulta_ins,TRUE);

                              --v_res_cone=(select dblink_disconnect());

                            END IF;

            end loop;*/







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

ALTER FUNCTION vef.ft_replicar_facturas_pendientes ()
  OWNER TO postgres;
