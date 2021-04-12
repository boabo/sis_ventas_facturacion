CREATE OR REPLACE FUNCTION vef.ft_consulta_boletos_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_consulta_boletos_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tconsulta_boletos'
 AUTOR: 		 (admin)
 FECHA:	        12-10-2017 21:15:26
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				12-10-2017 21:15:26								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tconsulta_boletos'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
  v_filto				varchar;
/*breydi vasquez */
v_boleto			record;
v_cadena_cnx		varchar;
v_rec 				record;
v_estado_cv			varchar;
v_sms				text;
v_periodo			varchar;
v_inhabilitacion	varchar='false';
v_estado_bol		varchar;
v_venta_f			record;
v_exito				varchar;
usu_mod				varchar;
v_count				integer;
v_ac_per			varchar='false';
v_res_cone			varchar;
BEGIN

	v_nombre_funcion = 'vef.ft_consulta_boletos_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_CBS_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		12-10-2017 21:15:26
	***********************************/

	if(p_transaccion='VF_CBS_SEL')then

    	begin
          IF  pxp.f_existe_parametro(p_tabla,'criterio_filtro') THEN
            v_filto = '(((cbs.pasajero::varchar ILIKE ''%'||v_parametros.criterio_filtro||'%'') OR to_tsvector(cbs.pasajero::varchar) @@ plainto_tsquery(''spanish'','''||v_parametros.criterio_filtro||''')) OR ((cbs.nro_boleto::varchar ILIKE ''%'||v_parametros.criterio_filtro||'%'') OR to_tsvector(cbs.nro_boleto::varchar) @@ plainto_tsquery(''spanish'','''||v_parametros.criterio_filtro||''')))  and ';
           ELSE
           	v_filto = '0=0 and';
            END IF;



    		--Sentencia de la consulta
			v_consulta:='select cbs.id_boleto,
                          cbs.punto_venta,
                          cbs.localizador,
                          cbs.total,
                          cbs.liquido,
                          cbs.id_moneda_boleto,
                          cbs.moneda,
                          cbs.neto,
                          cbs.fecha_emision,
                          cbs.nro_boleto,
                          cbs.pasajero,
                          cbs.voided,
                          cbs.estado,
                          cbs.agente_venta,
                          cbs.codigo_agente,
                          cbs.forma_pago_amadeus,
                          cbs.gestion
                          from vef.vboletos  cbs
                          where ' ;

			--Definicion de la respuesta

			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
            raise notice 'ONSULTA....%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VF_CBS_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		12-10-2017 21:15:26
	***********************************/

	elsif(p_transaccion='VF_CBS_CONT')then

		begin

        IF  pxp.f_existe_parametro(p_tabla,'criterio_filtro') THEN
                    v_filto = '(((cbs.pasajero::varchar ILIKE ''%'||v_parametros.criterio_filtro||'%'') OR to_tsvector(cbs.pasajero::varchar) @@ plainto_tsquery(''spanish'','''||v_parametros.criterio_filtro||''')) OR ((cbs.nro_boleto::varchar ILIKE ''%'||v_parametros.criterio_filtro||'%'') OR to_tsvector(cbs.nro_boleto::varchar) @@ plainto_tsquery(''spanish'','''||v_parametros.criterio_filtro||''')))  and ';

            ELSE
           	v_filto = '0=0 and ';
            END IF;

			--Sentencia de la consulta de conteo de registros
			v_consulta:='
                       select count(id_boleto)
					   from vef.vboletos  cbs
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
   	#TRANSACCION:  'VF_CNSBOINB_IME'
   	#DESCRIPCION:	Proceso de inhabilitacion de boleto.
   	#AUTOR:		breydi.vasquez
   	#FECHA:		12/03/2021
  	***********************************/

  	elsif(p_transaccion='VF_CNSBOINB_IME')then

      	begin

        IF ( pxp.f_existe_parametro(p_tabla, 'nro_tkt') and  pxp.f_existe_parametro(p_tabla,'fecha_emision')) THEN


           v_rec = param.f_get_periodo_gestion(v_parametros.fecha_emision::date);

           -- captura estado y id_departamento
           SELECT codep.estado into v_estado_cv
           FROM conta.tperiodo_compra_venta codep
           INNER JOIN param.tdepto dep ON dep.id_depto = codep.id_depto
           WHERE  dep.codigo = 'CON'
               AND codep.id_periodo = v_rec.po_id_periodo
               AND dep.id_depto = 4;

           -- captura de periodo
           SELECT pxp.f_obtener_literal_periodo(per.periodo,0) into v_periodo
           FROM param.tperiodo per
           WHERE per.id_periodo = v_rec.po_id_periodo;

           v_resp = pxp.f_agrega_clave(v_resp,'respuesta','Consulta Boleto con exito');

           IF v_estado_cv != 'cerrado' THEN

                 v_ac_per = 'true';
                 v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();

                 -- verificar si existen mas de un boleto con el mismo nro y fecha emision
                 SELECT count(nro_factura) into v_count
                 FROM dblink(v_cadena_cnx,
                       'SELECT nro_factura
                        FROM sfe.tfactura
                        WHERE sistema_origen = ''STAGE DB''
                        AND TRIM(nro_factura) = TRIM('''||v_parametros.nro_tkt||''')
                        AND fecha_factura::date = '''||v_parametros.fecha_emision||'''::date
                        ')
                 AS t1(nro_factura  varchar);

                 -- condiciones de respuesta

                 IF v_count = 1 THEN

                       --Sentencia de la consulta obtener boleto
                       SELECT * into v_boleto
                       FROM dblink(v_cadena_cnx,
                             'SELECT
                                     id_factura,
                                     estado,
                                     nro_factura
                              FROM sfe.tfactura
                              WHERE sistema_origen = ''STAGE DB''
                              AND TRIM(nro_factura) = TRIM('''||v_parametros.nro_tkt||''')
                              AND fecha_factura::date = '''||v_parametros.fecha_emision||'''::date
                              ')
                       AS t1(id_factura		integer,
                             estado			varchar,
                             nro_factura 		varchar);


                           IF v_boleto.estado = 'ANULADA' THEN
                               v_sms = 'EL nro de boleto '||v_parametros.nro_tkt||' con fecha de emision '||v_parametros.fecha_emision||' existe, ya se encuentra en estado '||v_boleto.estado||'. En el periodo '||v_periodo||' del libro de compras y ventas que se encuentra abierto.';
                           ELSE

                               --verificar si esta asociada a una factura

                               IF EXISTS (SELECT 1
                                           FROM vef.tboletos_asociados_fact
                                           WHERE TRIM(nro_boleto) = TRIM(v_parametros.nro_tkt)
                                           AND estado_reg = 'activo') THEN

                                   select ven.nro_factura, ven.nombre_factura, ven.nit, ven.nombre_factura,
                                        ven.total_venta, p.nombre into v_venta_f
                                   from vef.tboletos_asociados_fact f
                                   inner join vef.tventa ven on ven.id_venta = f.id_venta
                                   inner join vef.tpunto_venta p on p.id_punto_venta = ven.id_punto_venta
                                   where TRIM(nro_boleto) = TRIM(v_parametros.nro_tkt);

                                   v_sms = 'El nro de boleto '||v_parametros.nro_tkt||' con fecha de emision '||v_parametros.fecha_emision||' existe, se encuentra en estado '||v_boleto.estado||' y esta asociada a la factura nro: '||v_venta_f.nro_factura||'
                                   - nit: '||v_venta_f.nit||' - razon social: '||v_venta_f.nombre_factura||' - monto: '||v_venta_f.total_venta||' ,del punto de venta '||v_venta_f.nombre||'. No puede ser ANULADO';

                               ELSE
                                 SELECT per.nombre_completo2 into usu_mod
                                 from segu.tusuario usu
                                 inner join segu.vpersona2 per on per.id_persona = usu.id_persona
                                 where usu.id_usuario = p_id_usuario;

                                 SELECT  dblink_exec(v_cadena_cnx,

                                 'UPDATE sfe.tfactura SET
                                   estado=''ANULADA'',
                                   nit_ci_cli = ''0'',
                                   razon_social_cli = ''ANULADA'',
                                   importe_otros_no_suj_iva = 0,
                                   importe_debito_fiscal = 0,
                                   importe_total_venta  = 0,
                                   usuario_mod = '''||usu_mod||''',
                                   fecha_reg = now()
                                   WHERE sistema_origen = ''STAGE DB''
                                       AND TRIM(nro_factura) = TRIM('''||v_parametros.nro_tkt||''')
                                       AND fecha_factura::date = '''||v_parametros.fecha_emision||'''::date
                                   ')  into v_exito;

                                   IF v_exito = 'UPDATE 1' THEN
                                     v_sms = 'El nro de boleto '||v_parametros.nro_tkt||' con fecha de emision '||v_parametros.fecha_emision||' fue ANULADO exitosamente ';
                                     v_inhabilitacion = 'true';
                                   ELSE
                                     v_sms = '!Notificacion no se pudo ANULAR el boleto '||v_parametros.nro_tkt||' con fecha de emision '||v_parametros.fecha_emision;
                                   END IF;

                               END IF;

                           END IF;

                         v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_sms::varchar);
                         v_resp = pxp.f_agrega_clave(v_resp,'inhabilitar',v_inhabilitacion::varchar);
                         v_resp = pxp.f_agrega_clave(v_resp,'periodo',v_ac_per::varchar);

                     ELSIF v_count = 0 THEN
                         v_sms = 'El nro de boleto '||v_parametros.nro_tkt||' con fecha de emision '||v_parametros.fecha_emision||' no existe.';
                         v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_sms::varchar);
                         v_resp = pxp.f_agrega_clave(v_resp,'inhabilitar',v_inhabilitacion::varchar);
                         v_resp = pxp.f_agrega_clave(v_resp,'periodo',v_ac_per::varchar);
                     ELSE
                         v_sms = 'Existe mas de un registro con el nro de boleto '||v_parametros.nro_tkt||' y fecha de emision '||v_parametros.fecha_emision||'';
                         v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_sms::varchar);
                         v_resp = pxp.f_agrega_clave(v_resp,'inhabilitar',v_inhabilitacion::varchar);
                         v_resp = pxp.f_agrega_clave(v_resp,'periodo',v_ac_per::varchar);
                     END IF;

                     --v_res_cone=(select dblink_disconnect());

           ELSE
                   v_sms = 'EL nro de boleto '||v_parametros.nro_tkt|| ' con fecha de emision '||v_parametros.fecha_emision||', no puede ser ANULADO. Ya que el periodo '||v_periodo||' del libro de compras y ventas se encuentra cerrado.';
                   v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_sms::varchar);
                   v_resp = pxp.f_agrega_clave(v_resp,'inhabilitar',v_inhabilitacion::varchar);
                   v_resp = pxp.f_agrega_clave(v_resp,'periodo',v_ac_per::varchar);

           END IF;

        ELSE

             raise 'No se envio los parametros de nro boleto y fecha emision para su proceso.';

        END IF;

   --Devuelve la respuesta
   return v_resp;

   end;

	else

		raise exception 'Transaccion inexistente';

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
