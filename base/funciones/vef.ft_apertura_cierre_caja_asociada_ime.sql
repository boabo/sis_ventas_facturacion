CREATE OR REPLACE FUNCTION vef.ft_apertura_cierre_caja_asociada_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_apertura_cierre_caja_asociada_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tapertura_cierre_caja_asociada'
 AUTOR: 		 (ivaldivia)
 FECHA:	        15-08-2019 13:15:22
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				15-08-2019 13:15:22								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tapertura_cierre_caja_asociada'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_apertura_asociada	integer;
    v_suma_total			numeric;

    v_id_apertura			INTEGER[];

    v_arreglo				INTEGER[];
    v_length				integer;
    v_monto_ventas			numeric;
    v_monto_deposito		numeric;
    v_diferencia			numeric;
    v_monto_seleccionado	numeric;

    v_datos_punto_venta			record;
    v_id_cajero				INTEGER[];
    v_cajeros				varchar;
    v_nombre_departamento	varchar;
    v_detalle				varchar;

BEGIN

    v_nombre_funcion = 'vef.ft_apertura_cierre_caja_asociada_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_acca_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		15-08-2019 13:15:22
	***********************************/

	if(p_transaccion='VF_acca_INS')then

        begin
        	 v_arreglo = string_to_array(v_parametros.id_apertura_cierre_caja,',');
             v_length = array_length(v_arreglo,1);


            select string_to_array (list(DISTINCT caja.id_usuario_cajero::varchar),',') into v_id_cajero
            from vef.tapertura_cierre_caja caja
            where caja.id_apertura_cierre_caja = ANY (v_arreglo);

        for i in 1..v_length

        loop


        	--Sentencia de la insercion
        	insert into vef.tapertura_cierre_caja_asociada(
			estado_reg,
			id_apertura_cierre_caja,
			id_deposito,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_arreglo[i]::integer,
			v_parametros.id_deposito,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null



			)RETURNING id_apertura_asociada into v_id_apertura_asociada;



         end loop;

         	/***********RECUPERAMOS EL NOMBRE DEL PUNTO DE VENTA******************/
            SELECT pv.nombre into v_nombre_departamento
            FROM vef.tpunto_venta pv
            WHERE pv.id_punto_venta = v_parametros.id_punto_venta;
         	/*********************************************************************/

         	/************RECUPERAMOS LOS CAJEROS**************/
         	SELECT list(per.nombre_completo2) into v_cajeros
            from segu.tusuario usu
            inner join segu.vpersona2 per on per.id_persona = usu.id_persona
            where usu.id_usuario = ANY (v_id_cajero);
            /********************************************************/

            v_detalle = 'PUNTO DE VENTA: '||v_nombre_departamento||' CAJERO(S): '||v_cajeros;

            update tes.tts_libro_bancos set
            detalle = v_detalle
            where id_deposito = v_parametros.id_deposito;


			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CajaAsociada almacenado(a) con exito (id_apertura_asociada'||v_id_apertura_asociada||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_apertura_asociada',v_id_apertura_asociada::varchar);

            --Devuelve la respuesta
            return v_resp;


		end;

	/*********************************
 	#TRANSACCION:  'VF_acca_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		15-08-2019 13:15:22
	***********************************/

	elsif(p_transaccion='VF_acca_MOD')then

		begin
			--Sentencia de la modificacion
			update vef.tapertura_cierre_caja_asociada set
			id_apertura_cierre_caja = v_parametros.id_apertura_cierre_caja,
			id_deposito = v_parametros.id_deposito,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_apertura_asociada=v_parametros.id_apertura_asociada;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CajaAsociada modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_apertura_asociada',v_parametros.id_apertura_asociada::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VF_acca_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		15-08-2019 13:15:22
	***********************************/

	elsif(p_transaccion='VF_acca_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from vef.tapertura_cierre_caja_asociada
            where id_apertura_asociada=v_parametros.id_apertura_asociada;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CajaAsociada eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_apertura_asociada',v_parametros.id_apertura_asociada::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
    #TRANSACCION: 'VF_SUMA_TOTAL_IME'
    #DESCRIPCION: RECUPERA EL TOTAL DE LAS APERTURAS SELECCIONADAS
    #AUTOR: IRVA
    #FECHA: 15-8-2019
    ***********************************/

	elsif (p_transaccion = 'VF_SUMA_TOTAL_IME') then

  	BEGIN


     select ('{'||v_parametros.id_apertura::varchar||'}') into v_id_apertura;

        if (v_parametros.id_moneda_deposito <> 2) then


             select   sum(cdo.arqueo_moneda_local) into v_monto_ventas
                      from vef.vdepositos cdo
                      inner join vef.tapertura_cierre_caja_asociada aso on aso.id_apertura_cierre_caja = cdo.id_apertura_cierre_caja
                      inner join vef.vdepositos_agrupados dep on dep.id_deposito = aso.id_deposito
                      where dep.id_deposito = v_parametros.id_deposito;

             if (v_monto_ventas is null) then
                  v_monto_ventas = 0;
              end if;

	  	      select sum(cdo.arqueo_moneda_local) into v_suma_total
              from vef.vdepositos cdo
              where cdo.id_apertura_cierre_caja = ANY (v_id_apertura);

        else

             select   sum(cdo.arqueo_moneda_extranjera) into v_monto_ventas
                      from vef.vdepositos cdo
                      inner join vef.tapertura_cierre_caja_asociada aso on aso.id_apertura_cierre_caja = cdo.id_apertura_cierre_caja
                      inner join vef.vdepositos_agrupados dep on dep.id_deposito = aso.id_deposito
                      where dep.id_deposito = v_parametros.id_deposito;

             if (v_monto_ventas is null) then
                  v_monto_ventas = 0;
              end if;

        	  select sum(cdo.arqueo_moneda_extranjera) into v_suma_total
              from vef.vdepositos cdo
              where cdo.id_apertura_cierre_caja = ANY (v_id_apertura);
      	end if;


      if (v_suma_total is null) then
	      v_suma_total = 0;
      end if;


 		v_diferencia = (v_parametros.monto_deposito - (v_suma_total + v_monto_ventas));

      --Definition of the response
      	v_resp = pxp.f_agrega_clave(v_resp, 'message ', 'Monto agrupados');
        v_resp = pxp.f_agrega_clave(v_resp,'v_diferencia',v_diferencia::varchar);
         v_resp = pxp.f_agrega_clave(v_resp,'v_suma_total',v_suma_total::varchar);

      --Returns the answer
        return v_resp;

  	END;

    /*********************************
    #TRANSACCION: 'VF_DATSUC_IME'
    #DESCRIPCION: RECUPERAR DATOS DE LA SUCURSAL
    #AUTOR: ISMAEL VALDIVIA ARANIBAR
    #FECHA: 23/08/2019
    ***********************************/

	elsif (p_transaccion = 'VF_DATSUC_IME') then

  	BEGIN
    		/*Recupramos el id_sucursal, id_lugar, codigo*/
              select  su.id_sucursal,
                      su.id_lugar,
                      pto.codigo,
                      l.codigo as codigo_padre,
                      lu.codigo as estacion,
                      (
                      SELECT c.oficial
                      FROM param.ttipo_cambio c
                      WHERE c.id_moneda = 2 AND
                      c.fecha = now()::date AND
                      c.fecha_mod IS NULL
                      ) AS tipo_cambio,
                     (SELECT to_char(CURRENT_DATE,'YYYY-MM-DD')) as fecha_venta into v_datos_punto_venta
              from vef.tsucursal su
              inner join vef.tpunto_venta pto on pto.id_sucursal = su.id_sucursal
              inner join param.tlugar l on l.id_lugar = param.f_obtener_padre_id_lugar(su.id_lugar, 'pais'::character varying)
              inner join param.tlugar lu on lu.id_lugar = su.id_lugar
              where pto.id_punto_venta = v_parametros.id_punto_venta;
              /*********************************************/



      --Definition of the response
      	v_resp = pxp.f_agrega_clave(v_resp, 'message ', 'Datos de la sucursal');
        v_resp = pxp.f_agrega_clave(v_resp,'v_codigo_padre',v_datos_punto_venta.codigo_padre::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'v_estacion',v_datos_punto_venta.estacion::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'v_codigo',v_datos_punto_venta.codigo::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'v_fecha_venta',v_datos_punto_venta.fecha_venta::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'v_tipo_cambio',v_datos_punto_venta.tipo_cambio::varchar);


      --Returns the answer
        return v_resp;

  	END;



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

ALTER FUNCTION vef.ft_apertura_cierre_caja_asociada_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
