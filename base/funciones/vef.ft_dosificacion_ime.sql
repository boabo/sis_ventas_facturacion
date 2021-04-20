CREATE OR REPLACE FUNCTION vef.ft_dosificacion_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
  /**************************************************************************
   SISTEMA:		Sistema de Ventas
   FUNCION: 		vef.ft_dosificacion_ime
   DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tdosificacion'
   AUTOR: 		 (jrivera)
   FECHA:	        07-10-2015 13:00:56
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
    v_existe_dosificacion		numeric;

  BEGIN

    v_nombre_funcion = 'vef.ft_dosificacion_ime';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
     #TRANSACCION:  'VF_DOS_INS'
     #DESCRIPCION:	Insercion de registros
     #AUTOR:		jrivera
     #FECHA:		07-10-2015 13:00:56
    ***********************************/

    if(p_transaccion='VF_DOS_INS')then

      begin
        --Sentencia de la insercion
        insert into vef.tdosificacion(
          id_sucursal,
          final,
          tipo,
          fecha_dosificacion,
          nro_siguiente,
          nroaut,
          fecha_inicio_emi,
          fecha_limite,
          tipo_generacion,
          glosa_impuestos,
          id_activida_economica,
          llave,
          inicial,
          estado_reg,
          glosa_empresa,
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
          v_parametros.nroaut,
          v_parametros.fecha_inicio_emi,
          v_parametros.fecha_limite,
          v_parametros.tipo_generacion,
          v_parametros.glosa_impuestos,
          string_to_array(v_parametros.id_activida_economica, ',')::integer[],
          v_parametros.llave,
          v_parametros.inicial,
          'activo',
          v_parametros.glosa_empresa,
          v_parametros._id_usuario_ai,
          now(),
          v_parametros._nombre_usuario_ai,
          p_id_usuario,
          null,
          null

        )RETURNING id_dosificacion into v_id_dosificacion ;

        select e.nit,s.nombre,s.codigo into v_nit,v_nombre_sucursal,v_codigo_sucursal
        from vef.tsucursal s
          inner join param.tentidad e on e.id_entidad = s.id_entidad
        where s.id_sucursal = v_parametros.id_sucursal;

        v_cod_control = pxp.f_gen_cod_control(
           v_parametros.llave,
            v_parametros.nroaut,
            '1'::varchar,
            '196560027'::varchar,
            to_char(v_parametros.fecha_inicio_emi,'YYYYMMDD')::varchar,
            1::numeric
        );

        v_mensaje = '
            	Dosificacion registrada con exito para la sucursal ' || v_nombre_sucursal || '-' || v_codigo_sucursal || '.<br> Por favor valide la siguiente informacion en <b><a href="http://ov.impuestos.gob.bo/Paginas/Publico/VerificacionFactura.aspx">Impuestos</a></b>:<br><br>
            		NIT Emisor : ' || v_nit || '<br>
                    Numero Factura : 1 <br>
                    Numero autorizacion : ' || v_parametros.nroaut || ' <br>
                    Fecha de Emision : 	' || to_char(v_parametros.fecha_inicio_emi,'DD/MM/YYYY') || ' <br>
                    NIT Comprador : 196560027 <br>
                    Total : 1 <br>
                    Codigo Control : ' || v_cod_control || '<br><br>
                <b>Esto garantizara que la informacion de la dosificacion se ha registrado correctamente.</b>
            ';
        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'id_dosificacion',v_id_dosificacion::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'prueba', v_mensaje);
        --Devuelve la respuesta
        return v_resp;

      end;

    /*********************************
     #TRANSACCION:  'VF_DOS_MOD'
     #DESCRIPCION:	Modificacion de registros
     #AUTOR:		jrivera
     #FECHA:		07-10-2015 13:00:56
    ***********************************/

    elsif(p_transaccion='VF_DOS_MOD')then

      begin

        --Sentencia de la modificacion
        update vef.tdosificacion set
          id_sucursal = v_parametros.id_sucursal,
          final = v_parametros.final,
          tipo = v_parametros.tipo,
          fecha_dosificacion = v_parametros.fecha_dosificacion,
          nroaut = v_parametros.nroaut,
          fecha_inicio_emi = v_parametros.fecha_inicio_emi,
          fecha_limite = v_parametros.fecha_limite,
          tipo_generacion = v_parametros.tipo_generacion,
          glosa_impuestos = v_parametros.glosa_impuestos,
          id_activida_economica = string_to_array(v_parametros.id_activida_economica, ',')::integer[],
          llave =v_parametros.llave,
          inicial = v_parametros.inicial,
          glosa_empresa = v_parametros.glosa_empresa,
          fecha_mod = now(),
          id_usuario_mod = p_id_usuario,
          id_usuario_ai = v_parametros._id_usuario_ai,
          usuario_ai = v_parametros._nombre_usuario_ai
          where id_dosificacion=v_parametros.id_dosificacion;


        select e.nit,s.nombre,s.codigo into v_nit,v_nombre_sucursal,v_codigo_sucursal
        from vef.tsucursal s
          inner join param.tentidad e on e.id_entidad = s.id_entidad
        where s.id_sucursal = v_parametros.id_sucursal;

        v_cod_control = pxp.f_gen_cod_control(
            v_parametros.llave,
            v_parametros.nroaut,
            '1'::varchar,
            '196560027'::varchar,
            to_char(v_parametros.fecha_inicio_emi,'YYYYMMDD')::varchar,
            1::numeric
        );

        v_mensaje = '
            	Dosificacion modificada con exito para la sucursal ' || v_nombre_sucursal || '-' || v_codigo_sucursal || '.<br> Por favor valide la siguiente informacion en <b><a href="http://ov.impuestos.gob.bo/Paginas/Publico/VerificacionFactura.aspx">Impuestos</a></b>:<br><br>
            		NIT Emisor : ' || v_nit || '<br>
                    Numero Factura : 1 <br>
                    Numero autorizacion : ' || v_parametros.nroaut || ' <br>
                    Fecha de Emision : 	' || to_char(v_parametros.fecha_inicio_emi,'DD/MM/YYYY') || ' <br>
                    NIT Comprador : 196560027 <br>
                    Total : 1 <br>
                    Codigo Control : ' || v_cod_control || '<br><br>
                <b>Esto garantizara que la informacion de la dosificacion se ha registrado correctamente.</b>
            ';

        --Definicion de la respuesta

        v_resp = pxp.f_agrega_clave(v_resp,'id_dosificacion',v_parametros.id_dosificacion::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'prueba',v_mensaje);
        --Devuelve la respuesta
        return v_resp;

      end;

    /*********************************
     #TRANSACCION:  'VF_DOS_ELI'
     #DESCRIPCION:	Eliminacion de registros
     #AUTOR:		jrivera
     #FECHA:		07-10-2015 13:00:56
    ***********************************/

    elsif(p_transaccion='VF_DOS_ELI')then

      begin
        --Sentencia de la eliminacion
        delete from vef.tdosificacion
        where id_dosificacion=v_parametros.id_dosificacion;

        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Dosificación eliminado(a)');
        v_resp = pxp.f_agrega_clave(v_resp,'id_dosificacion',v_parametros.id_dosificacion::varchar);

        --Devuelve la respuesta
        return v_resp;

      end;
     /*********************************
     #TRANSACCION:  'VF_DOS_INSEX'
     #DESCRIPCION:	Insercion de registros interfaz externa
     #AUTOR:		MMV
     #FECHA:		2/10/2017
    ***********************************/
    elsif(p_transaccion='VF_DOS_INSEX')then

      begin

      /*Control para que la dosificacion sea unica*/
      select count (dosi.id_dosificacion) into
      v_existe_dosificacion
      from vef.tdosificacion dosi
      where dosi.nroaut = TRIM(regexp_replace(v_parametros.nroaut, E'[\\n\\r]+', ' ', 'g' ));

      if (v_existe_dosificacion = 0) then
      /********************************************/

      --validamos que la fecha de inicio de emision no sea anterior a la fecha de tramite
        IF(v_parametros.fecha_inicio_emi < v_parametros.fecha_dosificacion )Then
        	raise exception 'La Fecha de Inicio de Emisión, no puede ser anterior a la Fecha de Tramite. Por favor revise las fechas.';
        End If;

        --validamos que la fecha limite de emision no sea anterior a la fecha de inicio de emision
		If(v_parametros.fecha_limite < v_parametros.fecha_inicio_emi) Then
        	raise exception 'La Fecha Límite de Emisión, no puede ser anterior a la Fecha Inico de Emision. Por favor revise las fechas. ';
        End If;

        --validamos que las fechas inicio y fin no se sobrepongan a las de otra dosificacion
        IF EXISTS (Select DISTINCT(1)
                    From vef.tdosificacion dos
                    Where dos.id_sucursal = v_parametros.id_sucursal
                    --and dos.autoimpresor=if_autoimpresor
                    and dos.tipo = v_parametros.tipo

                    and dos.tipo_generacion = v_parametros.tipo_generacion --Manual Computarizada
                    and dos.caracteristica = v_parametros.caracteristica --Caracteristica por facturacion exportacion

                    and  (REPLACE(REPLACE(dos.id_activida_economica::varchar,'{',''),'}',''))::integer = v_parametros.id_activida_economica::integer
                    and v_parametros.fecha_inicio_emi BETWEEN dos.fecha_inicio_emi and dos.fecha_limite
                    and v_parametros.fecha_limite BETWEEN dos.fecha_inicio_emi and dos.fecha_limite) then

        	raise exception 'La fecha inicio de emision y fecha limite de emision que intenta registrar, se sobreponen a las fechas de una dosificacion anterior para la misma Sucursal, Actividad Economica y Tipo.';
        END IF;



       insert into vef.tdosificacion(
          id_sucursal,
          final,
          tipo,
          fecha_dosificacion,
          nro_siguiente,
          nroaut,
          fecha_inicio_emi,
          fecha_limite,
          tipo_generacion,
          glosa_impuestos,
          id_activida_economica,
          llave,
          inicial,
          estado_reg,
          glosa_empresa,
          id_usuario_ai,
          fecha_reg,
          usuario_ai,
          id_usuario_reg,
          fecha_mod,
          id_usuario_mod,
          nro_tramite,
          nombre_sistema,
          leyenda,
          rnd,
          /*Aumentando para registrar dosificacion de exportacion ismael valdivia (19/04/2021)*/
          caracteristica,
          titulo,
          subtitulo
          /************************************************************************************/
        ) values(
          v_parametros.id_sucursal,
          v_parametros.final,
          v_parametros.tipo,
          v_parametros.fecha_dosificacion,
          1,
          TRIM(regexp_replace(v_parametros.nroaut, E'[\\n\\r]+', ' ', 'g' )),
          --v_parametros.nroaut,
          v_parametros.fecha_inicio_emi,
          v_parametros.fecha_limite,
          v_parametros.tipo_generacion,
          v_parametros.glosa_impuestos,
          string_to_array(v_parametros.id_activida_economica, ',')::integer[],
          /*AUMENTANDO PARA PONER CONTROL DE QUITAR SALTO DE LINEA Y ESPACIOS*/
          --v_parametros.llave,
          TRIM(regexp_replace(v_parametros.llave, E'[\\n\\r]+', ' ', 'g' )),
          /********************************************************************/
          v_parametros.inicial,
          'activo',
          v_parametros.glosa_empresa,
          v_parametros._id_usuario_ai,
          now(),
          v_parametros._nombre_usuario_ai,
          p_id_usuario,
          null,
          null,
          v_parametros.nro_tramite,
          v_parametros.nombre_sistema,
          v_parametros.leyenda,
          v_parametros.rnd,
		  /*Aumentando para registrar dosificacion de exportacion ismael valdivia (19/04/2021)*/
          v_parametros.caracteristica,
          v_parametros.titulo,
          v_parametros.subtitulo
          /************************************************************************************/
        )RETURNING id_dosificacion into v_id_dosificacion ;

		if (v_parametros.tipo_generacion = 'computarizada') then
        select e.nit,s.nombre,s.codigo into v_nit,v_nombre_sucursal,v_codigo_sucursal
        from vef.tsucursal s
          inner join param.tentidad e on e.id_entidad = s.id_entidad
        where s.id_sucursal = v_parametros.id_sucursal;

        v_cod_control = pxp.f_gen_cod_control(
            --v_parametros.llave
            TRIM(regexp_replace(v_parametros.llave, E'[\\n\\r]+', ' ', 'g' )),
            --v_parametros.nroaut,
            TRIM(regexp_replace(v_parametros.nroaut, E'[\\n\\r]+', ' ', 'g' )),
            '1'::varchar,
            '196560027'::varchar,
            to_char(v_parametros.fecha_inicio_emi,'YYYYMMDD')::varchar,
            1::numeric
        );

        v_mensaje = '
            	Dosificacion registrada con exito para la sucursal ' || v_nombre_sucursal || '-' || v_codigo_sucursal || '.<br> Por favor valide la siguiente informacion en <b><a href="http://ov.impuestos.gob.bo/Paginas/Publico/VerificacionFactura.aspx">Impuestos</a></b>:<br><br>
            	<b>Estos datos son de prueba para la verificacion del Codigo de Control.</b><br>
                	NIT Emisor : ' || v_nit || '<br>
                    Numero Factura : 1 <br>
                    Característica : ' || v_parametros.caracteristica || ' <br>
                    Titulo : ' || v_parametros.titulo || ' <br>,
                    Subtitulo : ' || v_parametros.subtitulo || ' <br>
                    Numero autorizacion : ' || v_parametros.nroaut || ' <br>
                    Fecha de Emision : 	' || to_char(v_parametros.fecha_inicio_emi,'DD/MM/YYYY') || ' <br>
                    NIT Comprador : 196560027 <br>
                    Total : 1 <br>
                    Codigo Control : ' || v_cod_control || '<br><br>
                <b>Esto garantizara que la informacion de la dosificacion se ha registrado correctamente.</b>
            ';
		end if ;

        else
        	Raise exception 'Ya existe una dosificacion registrada con el Nro. Autorizacion: %, Titulo: %, Caracteristica: %.',v_parametros.nroaut,v_parametros.titulo,v_parametros.caracteristica;

        end if;
        --Definicion de la respuesta

      v_resp = pxp.f_agrega_clave(v_resp,'id_dosificacion',v_id_dosificacion::varchar);
      v_resp = pxp.f_agrega_clave(v_resp,'prueba', v_mensaje);
      --Devuelve la respuesta
      return v_resp;

      end;
       /*********************************
     #TRANSACCION:  'VF_DOS_MODEXT'
     #DESCRIPCION:	Modificacion de registros
     #AUTOR:		MMV
     #FECHA:		21/10/2017
    ***********************************/

    elsif(p_transaccion='VF_DOS_MODEXT')then

      begin

      	IF(v_parametros.fecha_inicio_emi < v_parametros.fecha_dosificacion )Then
        	raise exception 'La Fecha de Inicio de Emisión, no puede ser anterior a la Fecha de Tramite. Por favor revise las fechas.';
        End If;

        --validamos que la fecha limite de emision no sea anterior a la fecha de inicio de emision
		If(v_parametros.fecha_limite < v_parametros.fecha_inicio_emi) Then
        	raise exception 'La Fecha Límite de Emisión, no puede ser anterior a la Fecha Inico de Emision. Por favor revise las fechas. ';
        End If;

        --validamos que las fechas inicio y fin no se sobrepongan a las de otra dosificacion
        /*IF EXISTS (Select DISTINCT(1)
                    From vef.tdosificacion dos
                    Where dos.id_sucursal = v_parametros.id_sucursal
                    --and dos.autoimpresor=if_autoimpresor
                    and dos.tipo = v_parametros.tipo
                    and dos.id_actividad_economica = v_parametros.id_actividad_economica
                    and v_parametros.fecha_inicio_emi BETWEEN dos.fecha_inicio_emi and dos.fecha_limite
                    and v_parametros.fecha_limite BETWEEN dos.fecha_inicio_emi and dos.fecha_limite) then

        	raise exception 'La fecha inicio de emision y fecha limite de emision que intenta registrar, se sobreponen a las fechas de una dosificacion anterior para la misma Sucursal, Actividad Economica y Tipo.';
        END IF;*/




        --Sentencia de la modificacion
        update vef.tdosificacion set
          id_sucursal = v_parametros.id_sucursal,
          final = v_parametros.final,
          tipo = v_parametros.tipo,
          fecha_dosificacion = v_parametros.fecha_dosificacion,
          --nroaut = v_parametros.nroaut,
          nroaut = TRIM(regexp_replace(v_parametros.nroaut, E'[\\n\\r]+', ' ', 'g' )),
          fecha_inicio_emi = v_parametros.fecha_inicio_emi,
          fecha_limite = v_parametros.fecha_limite,
          tipo_generacion = v_parametros.tipo_generacion,
          glosa_impuestos = v_parametros.glosa_impuestos,
          id_activida_economica = string_to_array(v_parametros.id_activida_economica, ',')::integer[],
          /*AUMENTANDO PARA PONER CONTROL DE QUITAR SALTO DE LINEA Y ESPACIOS*/
          llave = TRIM(regexp_replace(v_parametros.llave, E'[\\n\\r]+', ' ', 'g' )),
          --v_parametros.llave,
          /********************************************************************/
          inicial = v_parametros.inicial,
          glosa_empresa = v_parametros.glosa_empresa,
          fecha_mod = now(),
          id_usuario_mod = p_id_usuario,
          id_usuario_ai = v_parametros._id_usuario_ai,
          usuario_ai = v_parametros._nombre_usuario_ai,
          nro_tramite = v_parametros.nro_tramite,
          nombre_sistema = v_parametros.nombre_sistema,
          leyenda = v_parametros.leyenda,
          rnd = v_parametros.rnd,

          /*Aumentando para registrar dosificacion de exportacion ismael valdivia (19/04/2021)*/
          caracteristica = v_parametros.caracteristica,
          titulo = v_parametros.titulo,
          subtitulo = v_parametros.subtitulo
          /************************************************************************************/
        where id_dosificacion=v_parametros.id_dosificacion;

    if (v_parametros.tipo_generacion = 'computarizada') then

        select e.nit,s.nombre,s.codigo into v_nit,v_nombre_sucursal,v_codigo_sucursal
        from vef.tsucursal s
          inner join param.tentidad e on e.id_entidad = s.id_entidad
        where s.id_sucursal = v_parametros.id_sucursal;

        v_cod_control = pxp.f_gen_cod_control(
            --v_parametros.llave,
            TRIM(regexp_replace(v_parametros.llave, E'[\\n\\r]+', ' ', 'g' )),
            --v_parametros.nroaut,
            TRIM(regexp_replace(v_parametros.nroaut, E'[\\n\\r]+', ' ', 'g' )),
            '1'::varchar,
            '196560027'::varchar,
            to_char(v_parametros.fecha_inicio_emi,'YYYYMMDD')::varchar,
            1::numeric
        );

        v_mensaje = '
            	Dosificacion modificada con exito para la sucursal ' || v_nombre_sucursal || '-' || v_codigo_sucursal || '.<br style = "color: red;"> Por favor valide la siguiente información en <b><a href="http://ov.impuestos.gob.bo/Paginas/Publico/VerificacionFactura.aspx">Impuestos </a></b>:<br><br>
            		<b>Estos datos son de prueba para la verificacion del Codigo de Control.</b><br>
                    NIT Emisor : ' || v_nit || '<br>
                    Numero Factura : 1 <br>
                    Característica : ' || v_parametros.caracteristica || ' <br>
                    Titulo : ' || v_parametros.titulo || ' <br>,
                    Subtitulo : ' || v_parametros.subtitulo || ' <br>
                    Numero autorizacion : ' || v_parametros.nroaut || ' <br>
                    Fecha de Emision : 	' || to_char(v_parametros.fecha_inicio_emi,'DD/MM/YYYY') || ' <br>
                    NIT Comprador : 196560027 <br>
                    Total : 1 <br>
                    Codigo Control : ' || v_cod_control || '<br><br>
                <b>Esto garantizara que la informacion de la dosificacion se ha registrado correctamente.</b>
            ';

        --Definicion de la respuesta
end if;
        v_resp = pxp.f_agrega_clave(v_resp,'id_dosificacion',v_parametros.id_dosificacion::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'prueba',v_mensaje);
        --Devuelve la respuesta
        return v_resp;

      end;

     /*********************************
     #TRANSACCION:  'VF_DOS_CORR'
     #DESCRIPCION:	correo de vencimiento de dosificación
     #AUTOR:		MMV
     #FECHA:		21/10/2017
    ***********************************/

    elsif(p_transaccion='VF_DOS_CORR')then

      begin

      for v_registros in ( select lu.codigo,
 		su.codigo||' - '||su.nombre as sucursal,
        (select pxp.list( codigo||' - '||nombre)
		from vef.tactividad_economica
		where id_actividad_economica = ANY(d.id_activida_economica)) as desc_actividad_economica,
        d.nroaut,
        d.nombre_sistema,
        d.caracteristica,
        d.titulo,
        d.subtitulo,
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
        where extract(day from d.fecha_limite)-5 = extract(day from current_date)
        and extract(month from d.fecha_limite) = extract(month from current_date))loop

        v_asunto = 'Vencimiento de Dosificación';

        v_template = 	'<p>Estimad@s<br /><br />
        				En cumplimiento a politicas de la empresa, se les informa que la dosificaci&oacute;n con el siguiente detalle esta proxima a vencer.&nbsp;</p>
                      	<p><strong>Estación: </strong>'||v_registros.codigo::varchar||'<br />
                        <strong>Sucursal: </strong>'||v_registros.sucursal::varchar||'<br />
                        <strong>Actividad Económica: </strong>'||v_registros.desc_actividad_economica::varchar||'</strong><br />
                        <strong>Nro Autorización: </strong>'||v_registros.nroaut::varchar||'<br />
                        <strong>Nombre Sistema: </strong>'||v_registros.nombre_sistema::varchar||'<br />

                        <strong>Característica: </strong>'||v_registros.caracteristica||'<br />
                        <strong>Titulo: </strong>'||v_registros.titulo||'<br />
                        <strong>subtitulo: </strong>'||v_registros.subtitulo||'<br />



                        <strong>Tipo de Generación: </strong>'||v_registros.tipo_generacion||'<br />
                        <strong>Tipo Dosificacion: </strong>'||v_registros.tipo_dosifiacion::varchar||'<br />
                        <strong>Fecha Límite de Emisión: </strong>'||v_registros.fecha_limite::varchar||'<br />
                        <br />Favor registrar con anticipación la nueva dosificación en el siguiente menu: Sistema de Ventas -> Dosificaciones -> Dosificaciones.<br />
                        <br />-------------------------------------<br />* Sistema ERP BOA</p>';

         v_titulo = 'Vencimiento de Dosificación '||v_registros.sucursal::varchar;
         v_acceso_directo = '';
         v_clase = '';
         v_parametros_ad = '{}';

       	v_id_alarma[1]:=param.f_inserta_alarma(		NULL,
                                                    v_template ,    --descripcion alarmce
                                                    COALESCE(v_acceso_directo,''),--acceso directo
                                                    now()::date,
                                                    'notificacion',
                                                    '',   -->
                                                    p_id_usuario,
                                                    v_clase,
                                                    v_titulo,--titulo
                                                    COALESCE(v_parametros_ad,''),
                                                    null,  --destino de la alarma
                                                    v_asunto,
                                                    'shirley.mercado@boa.bo,[xzambrana@boa.bo;mcaballero@boa.bo;gvelasquez@boa.bo;ismael.valdivia@boa.bo]'
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

ALTER FUNCTION vef.ft_dosificacion_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
