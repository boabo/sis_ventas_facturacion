CREATE OR REPLACE FUNCTION vef.ft_boletos_asociados_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_boletos_asociados_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tboletos_asociados_fact'
 AUTOR: 		 (ivaldivia)
 FECHA:	        18-10-2019 12:15:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				18-10-2019 12:15:00								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tboletos_asociados_fact'
 #
 ***************************************************************************/

DECLARE

	v_parametros           	record;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;

    v_id_boleto_asociado	integer;


    v_arreglo				INTEGER[];
    v_length				integer;
	v_datos_boletos			record;
    v_existencia			integer;
    v_inicial_boleto		varchar;

BEGIN

    v_nombre_funcion = 'vef.ft_apertura_cierre_caja_asociada_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_ASOBOLETOS_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		18-10-2019 12:15:00
	***********************************/

	if(p_transaccion='VF_ASOBOLETOS_INS')then

        begin

        /*Insertar Solo un boleto*/

             --Verificamos si el numero de boleto ingresado existe


             select substring(v_parametros.nro_boleto from 1 for 3) into v_inicial_boleto;

			if (v_inicial_boleto <> '930') then
            	raise exception 'Los digitos no corresponden a un boleto, verifique.';
            end if;

             select count (bole.id_boleto_amadeus)
                    into v_existencia
                from obingresos.tboleto_amadeus bole
                where bole.nro_boleto = v_parametros.nro_boleto and bole.estado_reg = 'activo';

             if (v_existencia > 0) then

             	select
                	bole.nro_boleto,
                    --bole.nit,
                    bole.pasajero,
                    --bole.razon,
                    --(bole.origen || '-' || bole.destino) as ruta,
                    bole.fecha_emision,
                    bole.id_boleto_amadeus
                    into v_datos_boletos
                from obingresos.tboleto_amadeus bole
                where bole.nro_boleto = v_parametros.nro_boleto;


                --Sentencia de la insercion
                insert into vef.tboletos_asociados_fact(
                estado_reg,
                id_boleto,
                id_venta,
                nro_boleto,
                fecha_emision,
                pasajero,
                --nit,
                --ruta,
                --razon,
                fecha_reg,
                id_usuario_reg
                ) values(
                'activo',
                v_datos_boletos.id_boleto_amadeus,
                v_parametros.id_venta,
                v_datos_boletos.nro_boleto,
                v_datos_boletos.fecha_emision,
                v_datos_boletos.pasajero,
				--v_datos_boletos.nit,
                --v_datos_boletos.ruta,
                --v_datos_boletos.razon,
                now(),
                p_id_usuario
                )RETURNING id_boleto_asociado into v_id_boleto_asociado;


             else
             	raise exception 'El número de boleto no se encuentra registrado, por favor verifique el número ingresado';
             end if;


        /* --Insertar varios Boletos

          v_arreglo = string_to_array(v_parametros.id_boleto,',');
          v_length = array_length(v_arreglo,1);


        for i in 1..v_length
              loop

              	select
                	bole.nro_boleto,
                    bole.nit,
                    bole.pasajero,
                    bole.razon,
                    (bole.origen || '-' || bole.destino) as ruta,
                    bole.fecha_emision
                    into v_datos_boletos
                from obingresos.tboleto bole
                where bole.id_boleto = v_arreglo[i]::integer;



                --Sentencia de la insercion
                insert into vef.tboletos_asociados_fact(
                estado_reg,
                id_boleto,
                id_venta,
                nro_boleto,
                fecha_emision,
                pasajero,
                nit,
                ruta,
                razon,
                fecha_reg,
                id_usuario_reg
                ) values(
                'activo',
                v_arreglo[i]::integer,
                v_parametros.id_venta,
                v_datos_boletos.nro_boleto,
                v_datos_boletos.fecha_emision,
                v_datos_boletos.pasajero,
				v_datos_boletos.nit,
                v_datos_boletos.ruta,
                v_datos_boletos.razon,
                now(),
                p_id_usuario
                )RETURNING id_boleto_asociado into v_id_boleto_asociado;


             end loop;	 */


			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CajaAsociada almacenado(a) con exito (id_apertura_asociada'||v_id_boleto_asociado||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_apertura_asociada',v_id_boleto_asociado::varchar);

            --Devuelve la respuesta
            return v_resp;


		end;
/*
	/*********************************
 	#TRANSACCION:  'VF_ASOBOLETOS_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		15-08-2019 13:15:22
	***********************************/

	elsif(p_transaccion='VF_ASOBOLETOS_MOD')then

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
*/
	/*********************************
 	#TRANSACCION:  'VF_ASOBOLETOS_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		15-08-2019 13:15:22
	***********************************/

	elsif(p_transaccion='VF_ASOBOLETOS_ELI')then

		begin

			--Sentencia de la eliminacion
			delete from vef.tboletos_asociados_fact
            where id_boleto_asociado = v_parametros.id_boleto_asociado;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boleto Asociado eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_apertura_asociada',v_parametros.id_boleto_asociado::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;


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

ALTER FUNCTION vef.ft_boletos_asociados_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
