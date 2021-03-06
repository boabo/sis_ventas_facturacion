CREATE OR REPLACE FUNCTION vef.ft_dosificacion_ro_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
  /**************************************************************************
   SISTEMA:		Sistema de Ventas
   FUNCION: 		vef.ft_dosificacion_ro_ime
   DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tdosificacion'
   AUTOR: 		 (Ismael Valdivia)
   FECHA:	        25-08-2020 10:40:00
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
    v_id_dosificacion	integer;
    v_mensaje			text;
    v_nit				varchar;
    v_cod_control		varchar;
    v_nombre_sucursal	varchar;
    v_codigo_sucursal	varchar;
    v_registros			record;
    v_asunto					varchar;
	v_destinatorio				varchar;
	v_template					varchar;
    v_id_alarma					integer[];
	v_titulo					varchar;
	v_clase						varchar;
	v_parametros_ad				varchar;
	v_acceso_directo			varchar;

  BEGIN

    v_nombre_funcion = 'vef.ft_dosificacion_ro_ime';
    v_parametros = pxp.f_get_record(p_tabla);


     /*********************************
     #TRANSACCION:  'VF_DOS_RO_INS'
     #DESCRIPCION:	Insercion de registros interfaz externa
     #AUTOR:		Ismael Valdivia
     #FECHA:		25/08/2020
    ***********************************/
    if(p_transaccion='VF_DOS_RO_INS')then

      begin
       insert into vef.tdosificacion_ro(
          id_sucursal,
          final,
          tipo,
          fecha_dosificacion,
          nro_siguiente,
          fecha_inicio_emi,
          fecha_limite,
          tipo_generacion,
          inicial,
          estado_reg,
          id_usuario_ai,
          fecha_reg,
          usuario_ai,
          id_usuario_reg,
          fecha_mod,
          id_usuario_mod
        ) values(
          v_parametros.id_sucursal,
          v_parametros.final,
          v_parametros.tipo,
          v_parametros.fecha_dosificacion,
          1,
          v_parametros.fecha_inicio_emi,
          v_parametros.fecha_limite,
          'computarizada',
          v_parametros.inicial,
          'activo',
          v_parametros._id_usuario_ai,
          now(),
          v_parametros._nombre_usuario_ai,
          p_id_usuario,
          null,
          null

        )RETURNING id_dosificacion_ro into v_id_dosificacion ;

		--if (v_parametros.tipo_generacion = 'computarizada') then
        select e.nit,s.nombre,s.codigo into v_nit,v_nombre_sucursal,v_codigo_sucursal
        from vef.tsucursal s
          inner join param.tentidad e on e.id_entidad = s.id_entidad
        where s.id_sucursal = v_parametros.id_sucursal;

        v_mensaje = '
            	Dosificacion registrada con exito para la sucursal ' || v_nombre_sucursal || '-' || v_codigo_sucursal || '.<br> Por favor valide la siguiente informacion en <b><a href="http://ov.impuestos.gob.bo/Paginas/Publico/VerificacionFactura.aspx">Impuestos</a></b>:<br><br>
            		NIT Emisor : ' || v_nit || '<br>
                    Numero Factura : 1 <br>
                    Fecha de Emision : 	' || to_char(v_parametros.fecha_inicio_emi,'DD/MM/YYYY') || ' <br>
                    Total : 1 <br>
                <b>Esto garantizara que la informacion de la dosificacion se ha registrado correctamente.</b>
            ';
		--end if ;
        --Definicion de la respuesta

      v_resp = pxp.f_agrega_clave(v_resp,'id_dosificacion',v_id_dosificacion::varchar);
      v_resp = pxp.f_agrega_clave(v_resp,'prueba', v_mensaje);
      --Devuelve la respuesta
      return v_resp;

      end;
       /*********************************
     #TRANSACCION:  'VF_DOS_RO_MOD'
     #DESCRIPCION:	Modificacion de registros
     #AUTOR:		Ismael Valdivia
     #FECHA:		21/10/2017
    ***********************************/

    elsif(p_transaccion='VF_DOS_RO_MOD')then

      begin

        --Sentencia de la modificacion
        update vef.tdosificacion_ro set
          id_sucursal = v_parametros.id_sucursal,
          final = v_parametros.final,
          tipo = v_parametros.tipo,
          fecha_dosificacion = v_parametros.fecha_dosificacion,
          fecha_inicio_emi = v_parametros.fecha_inicio_emi,
          fecha_limite = v_parametros.fecha_limite,
          --tipo_generacion = computarizada,
          nro_siguiente = v_parametros.nro_siguiente,
          inicial = v_parametros.inicial,
          fecha_mod = now(),
          id_usuario_mod = p_id_usuario,
          id_usuario_ai = v_parametros._id_usuario_ai,
          usuario_ai = v_parametros._nombre_usuario_ai
        where id_dosificacion_ro=v_parametros.id_dosificacion_ro;

    	--if (v_parametros.tipo_generacion = 'computarizada') then

        select e.nit,s.nombre,s.codigo into v_nit,v_nombre_sucursal,v_codigo_sucursal
        from vef.tsucursal s
          inner join param.tentidad e on e.id_entidad = s.id_entidad
        where s.id_sucursal = v_parametros.id_sucursal;

        v_mensaje = '
            	Dosificacion modificada con exito para la sucursal ' || v_nombre_sucursal || '-' || v_codigo_sucursal || '.<br> Por favor valide la siguiente informacion en <b><a href="http://ov.impuestos.gob.bo/Paginas/Publico/VerificacionFactura.aspx">Impuestos</a></b>:<br><br>
            		NIT Emisor : ' || v_nit || '<br>
                    Numero Factura : 1 <br>
                    Fecha de Emision : 	' || to_char(v_parametros.fecha_inicio_emi,'DD/MM/YYYY') || ' <br>
                    Total : 1 <br>
                <b>Esto garantizara que la informacion de la dosificacion se ha registrado correctamente.</b>
            ';

        --Definicion de la respuesta
		--	end if;
        v_resp = pxp.f_agrega_clave(v_resp,'id_dosificacion',v_parametros.id_dosificacion_ro::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'prueba',v_mensaje);
        --Devuelve la respuesta
        return v_resp;

      end;

    /*********************************
     #TRANSACCION:  'VF_DOS_RO_ELI'
     #DESCRIPCION:	Eliminacion de registros
     #AUTOR:		Ismael Valdivia
     #FECHA:		25-08-2020 13:00:56
    ***********************************/

    elsif(p_transaccion='VF_DOS_RO_ELI')then

      begin
        --Sentencia de la eliminacion
        delete from vef.tdosificacion_ro
        where id_dosificacion_ro = v_parametros.id_dosificacion_ro;

        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Dosificación eliminado(a)');
        v_resp = pxp.f_agrega_clave(v_resp,'id_dosificacion',v_parametros.id_dosificacion_ro::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;

     /*********************************
     #TRANSACCION:  'VF_DOS_RO_CORR'
     #DESCRIPCION:	correo de vencimiento de dosificación
     #AUTOR:		Ismael Valdivia
     #FECHA:		25/08/2020
    ***********************************/

    elsif(p_transaccion='VF_DOS_RO_CORR')then

      begin

      for v_registros in ( select lu.codigo,
 		su.codigo||' - '||su.nombre as sucursal,
        (select pxp.list( codigo||' - '||nombre)
		from vef.tactividad_economica
		where id_actividad_economica = ANY(d.id_activida_economica)) as desc_actividad_economica,
        d.nroaut,
        d.nombre_sistema,
        CASE
        WHEN d.tipo::text = ANY (ARRAY['n'::character varying,'N'::character varying, 'n'::character varying]::text[]) THEN 'Nota de Credito/Debito'::text
        WHEN d.tipo::text = ANY (ARRAY['f'::character varying,'F'::character varying, 'f'::character varying]::text[]) THEN 'Factura'::text
        ELSE ''::text
        END::character varying AS tipo_dosifiacion,
        d.fecha_limite,
        d.id_usuario_reg,
        tipo_generacion
		from vef.tdosificacion d
		inner join vef.tsucursal su on su.id_sucursal = d.id_sucursal
		inner join param.tlugar lu on lu.id_lugar = su.id_lugar
        where extract(day from d.fecha_limite)-4 = extract(day from current_date)
        and extract(month from d.fecha_limite) = extract(month from current_date))loop

        v_asunto = 'Vencimiento de Dosificación';

        v_template = 	'<p>Estimad@s<br /><br />
        				En cumplimiento a politicas de la empresa, se les informa que la dosificaci&oacute;n con el siguiente detalle esta proxima a vencer.&nbsp;</p>
                      	<p><strong>Estación: </strong>'||v_registros.codigo::varchar||'<br />
                        <strong>Sucursal: </strong>'||v_registros.sucursal::varchar||'<br />
                        <strong>Actividad Económica: </strong>'||v_registros.desc_actividad_economica::varchar||'</strong><br />
                        <strong>Nro Autorización: </strong>'||v_registros.nroaut::varchar||'<br />
                        <strong>Nombre Sistema: </strong>'||v_registros.nombre_sistema::varchar||'<br />
                        <strong>Tipo de Generación: </strong>'||v_registros.tipo_generacion||'<br />
                        <strong>Tipo Dosificacion: </strong>'||v_registros.tipo_dosifiacion::varchar||'<br />
                        <strong>Fecha Límite de Emisión: </strong>'||v_registros.fecha_limite::varchar||'<br />
                        <br />Favor registrar con anticipación la nueva dosificación en el siguiente menu: Sistema de Ventas -> Catalogos -> Dosificación.<br />
                        <br />-------------------------------------<br />* Sistema ERP BOA</p>';

         v_titulo = 'Vencimiento de Dosificación '||v_registros.sucursal::varchar;
         v_acceso_directo = '';
         v_clase = '';
         v_parametros_ad = '{}';

       	v_id_alarma[1]:=param.f_inserta_alarma(		1062,
                                                    v_template ,    --descripcion alarmce
                                                    COALESCE(v_acceso_directo,''),--acceso directo
                                                    now()::date,
                                                    'felicitacion',
                                                    '',   -->
                                                    p_id_usuario,
                                                    v_clase,
                                                    v_titulo,--titulo
                                                    COALESCE(v_parametros_ad,''),
                                                    null,  --destino de la alarma
                                                    v_asunto,
                                                    'xzambrana@boa.bo,mcaballero@boa.bo,gvelasquezcolque@gmail.com,miguel.ale19934@gmail.com'
                                                    );

      end loop;
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
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION vef.ft_dosificacion_ro_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
