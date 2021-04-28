CREATE OR REPLACE FUNCTION vef.ft_venta_forma_pago_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.ft_venta_forma_pago_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'vef.tventa_forma_pago'
 AUTOR: 		 (jrivera)
 FECHA:	        22-10-2015 14:49:46
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
	v_id_venta_forma_pago	integer;
    v_forma_pago			record;
    v_res					varchar;

    v_codigo_tarjeta		varchar;
    v_codigo_fp				varchar;
    v_monto					numeric;
    v_venta					record;
    v_id_moneda_venta		integer;
    v_id_moneda_suc			integer;
    v_monto_fp				numeric;
    v_acumulado_fp			numeric;
    v_registros				record;

    v_moneda_base			integer;
    v_monto_venta			numeric;
    v_inicial_boleto		varchar;
    v_existencia			integer;
    v_datos_boletos			record;
    v_id_boleto_asociado	integer;
BEGIN

    v_nombre_funcion = 'vef.ft_venta_forma_pago_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VF_VENFP_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera
 	#FECHA:		22-10-2015 14:49:46
	***********************************/

	if(p_transaccion='VF_VENFP_INS')then

        begin
        	select * into v_forma_pago
            from vef.tforma_pago fp
            where fp.id_forma_pago = v_parametros.id_forma_pago;
        	--Sentencia de la insercion
        	insert into vef.tventa_forma_pago(
			id_forma_pago,
			id_venta,
			monto_mb_efectivo,
			estado_reg,
			cambio,
			monto_transaccion,
			monto,
			fecha_reg,
			id_usuario_reg,
			fecha_mod,
			id_usuario_mod,
			numero_tarjeta,
			codigo_tarjeta,
            id_auxilliar,
			tipo_tarjeta
          	) values(
			v_parametros.id_forma_pago,
			v_parametros.id_venta,
			0,
			'activo',
			0,
			v_parametros.valor,
			0,
			now(),
			p_id_usuario,
			null,
			null,
			v_parametros.numero_tarjeta,
			v_parametros.codigo_tarjeta,
            v_parametros.id_auxiliar,
			v_parametros.tipo_tarjeta
			)RETURNING id_venta_forma_pago into v_id_venta_forma_pago;

            if (v_forma_pago.registrar_tarjeta = 'si' and v_forma_pago.registrar_tipo_tarjeta = 'no')then
            	v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta,substring(v_forma_pago.codigo from 3 for 2));
            elsif (v_forma_pago.registrar_tarjeta = 'si' and v_forma_pago.registrar_tipo_tarjeta = 'si')then
            	v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta,v_parametros.tipo_tarjeta);
            end if;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de Pago almacenado(a) con exito (id_venta_forma_pago'||v_id_venta_forma_pago||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta_forma_pago',v_id_venta_forma_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VF_VENFP_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		22-10-2015 14:49:46
	***********************************/

	elsif(p_transaccion='VF_VENFP_MOD')then

		begin
			--Sentencia de la modificacion
			update vef.tventa_forma_pago set
			id_forma_pago = v_parametros.id_forma_pago,
			id_venta = v_parametros.id_venta,
			monto_mb_efectivo = v_parametros.monto_mb_efectivo,
			cambio = v_parametros.cambio,
			monto_transaccion = v_parametros.monto_transaccion,
			monto = v_parametros.monto,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_venta_forma_pago=v_parametros.id_venta_forma_pago;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de Pago modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta_forma_pago',v_parametros.id_venta_forma_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VF_VENFP_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		22-10-2015 14:49:46
	***********************************/

	elsif(p_transaccion='VF_VENFP_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from vef.tventa_forma_pago
            where id_venta_forma_pago=v_parametros.id_venta_forma_pago;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de Pago eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta_forma_pago',v_parametros.id_venta_forma_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'VF_NFP_INS'
 	#DESCRIPCION:	Insercion de registros n formas de pago
 	#AUTOR:		Ismael Vadivia
 	#FECHA:		8-12-2020 9:00:46
	***********************************/

	elsif(p_transaccion='VF_NFP_INS')then

        begin
        	/*Aqui Hacemos las validaciones de las tarjetas y mco ingrseados*/

        	 if (v_parametros.id_medio_pago is not null and v_parametros.id_medio_pago != 0) then

                  select mp.mop_code, fp.fop_code into v_codigo_tarjeta, v_codigo_fp
                  from obingresos.tmedio_pago_pw mp
                  inner join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
                  where mp.id_medio_pago_pw = v_parametros.id_medio_pago;

                  v_codigo_tarjeta = (case when v_codigo_tarjeta is not null then
                                                  v_codigo_tarjeta
                                          else
                                                NULL
                                        end);

                  if (v_codigo_tarjeta is not null and v_codigo_fp = 'CC') then
                      if (substring(v_parametros.num_tarjeta::varchar from 1 for 1) != 'X') then
                          v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.num_tarjeta::varchar,v_codigo_tarjeta);
                      end if;
                  end if;
              end if;


              if (left (v_parametros.mco,3)  <> '930' and v_parametros.mco <> '')then
                  raise exception 'El numero del MCO tiene que empezar con 930';
              end if;

              if (char_length(v_parametros.mco::varchar) <> 15 and v_parametros.mco <> '' ) then
                  raise exception 'El numero del MCO debe tener 15 digitos obligatorios, 930000000012345';
              end if;
            /************************************************************************************************/

            v_monto = 0;
            v_acumulado_fp = 0;

            IF (v_parametros.id_moneda = 2) then
            	v_monto = v_parametros.monto_total_extranjero;
            else
            	v_monto = v_parametros.monto_total_local;
            end if;



            --Sentencia de la insercion
        	insert into vef.tventa_forma_pago(
            usuario_ai,
            fecha_reg,
            id_usuario_reg,
            id_usuario_ai,
            estado_reg,
            id_venta,
            monto_transaccion,
            monto,
            cambio,
            monto_mb_efectivo,
            numero_tarjeta,
            codigo_tarjeta,
            id_auxiliar,
            tipo_tarjeta,
            /*Aumentamos el id_instancia*/
            id_medio_pago,
            id_moneda,
            nro_mco,
            id_venta_recibo
            /****************************/
          )
          values(
            v_parametros._nombre_usuario_ai,
            now(),
            p_id_usuario,
            v_parametros._id_usuario_ai,
            'activo',
            v_parametros.id_venta,
            v_monto,
            0,
            0,
            0,
            v_parametros.num_tarjeta,
            replace(upper(v_parametros.codigo_autorizacion),' ',''),
            v_parametros.id_auxiliar,
            '',
            /*Aumentamos el id_instancia y el id_moneda*/
            v_parametros.id_medio_pago,
            v_parametros.id_moneda,
            v_parametros.mco,
            /*Aumentamos el id_venta_recibo nuevo medio de pago*/
            v_parametros.id_venta_recibo
            /****************************/
          )RETURNING id_venta_forma_pago into v_id_venta_forma_pago;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de Pago almacenado(a) con exito (id_venta_forma_pago'||v_id_venta_forma_pago||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta_forma_pago',v_id_venta_forma_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'VF_FACFPELI_DEL'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ismaelvaldivia
 	#FECHA:		14-12-2020 14:45:46
	***********************************/

	elsif(p_transaccion='VF_FACFPELI_DEL')then

		begin
			--Sentencia de la eliminacion
			delete from vef.tventa_forma_pago
            where id_venta=v_parametros.id_venta;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de Pago eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_parametros.id_venta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'VF_FACFPVALI_MOD'
 	#DESCRIPCION:	Validacion de las formas de pago
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		8-12-2020 10:10:00
	***********************************/

	elsif(p_transaccion='VF_FACFPVALI_MOD')then

		begin
			 /*Aqui validaremos los montos y la moneda para devolver el cambio*/
              select
                v.* ,
                sm.id_moneda as id_moneda_base,
                m.codigo  as moneda ,
                v.id_dosificacion as id_dosificacion_venta
              into
                v_venta
              from vef.tventa v
                inner join vef.tsucursal suc on suc.id_sucursal = v.id_sucursal
                inner join vef.tsucursal_moneda sm on suc.id_sucursal = sm.id_sucursal and sm.tipo_moneda = 'moneda_base'
                inner join param.tmoneda m on m.id_moneda = sm.id_moneda
              where id_venta = v_parametros.id_venta;
            /*****************************************************************/
            v_acumulado_fp = 0;

            v_moneda_base = param.f_get_moneda_base();

            IF  v_venta.tipo_factura in ('computarizadaexpo','computarizadaexpomin') THEN
              v_id_moneda_venta = v_venta.id_moneda;
            ELSE
              v_id_moneda_venta = v_venta.id_moneda_base;
            END IF;

            v_id_moneda_suc = v_venta.id_moneda_base;


            /*Aqui Ponemos la condicion para el monto venta*/
            if (v_parametros.tipo_factura = 'recibo') then
                if (v_venta.id_moneda_venta_recibo = 2 and v_moneda_base != 2) then
                    v_monto_venta = param.f_convertir_moneda(v_venta.id_moneda_venta_recibo,v_id_moneda_venta,v_venta.total_venta,v_venta.fecha::date,'CUS',2, NULL,'si');
                else
                    v_monto_venta = v_venta.total_venta;
                end if;
            else
            	v_monto_venta = v_venta.total_venta;
            end if;
            /***********************************************/



           for v_registros in (select vfp.id_venta_forma_pago, vfp.id_moneda,vfp.monto_transaccion
                              from vef.tventa_forma_pago vfp
                              where vfp.id_venta = v_parametros.id_venta)
        	loop



              if (v_registros.id_moneda != v_id_moneda_venta) then
                IF  v_venta.tipo_cambio_venta is not null and v_venta.tipo_cambio_venta != 0 THEN
                  v_monto_fp = param.f_convertir_moneda(v_registros.id_moneda,v_id_moneda_venta,v_registros.monto_transaccion,v_venta.fecha::date,'CUS',2, v_venta.tipo_cambio_venta,'si');
                ELSE
                  v_monto_fp = param.f_convertir_moneda(v_registros.id_moneda,v_id_moneda_venta,v_registros.monto_transaccion,v_venta.fecha::date,'O',2,NULL,'si');
                END IF;
              else
                v_monto_fp = v_registros.monto_transaccion;
              end if;


               update vef.tventa_forma_pago set
                monto = v_monto_fp,
                cambio = (case when (v_monto_fp + v_acumulado_fp - v_monto_venta) > 0 then
                  (v_monto_fp + v_acumulado_fp - v_monto_venta)
                          else
                            0
                          end),
                monto_mb_efectivo = (case when (v_monto_fp + v_acumulado_fp - v_monto_venta) > 0 then
                  v_monto_fp - (v_monto_fp + v_acumulado_fp - v_monto_venta)
                                     else
                                       v_monto_fp
                                     end)
              where id_venta_forma_pago = v_registros.id_venta_forma_pago;
           	  v_acumulado_fp = v_acumulado_fp + v_monto_fp;
        	end loop;

                /*Aumentamos para asociar los boletos registrados*/
          if (pxp.f_existe_parametro(p_tabla,'asociar_boletos')) then

          	IF (v_parametros.asociar_boletos != '' ) then

              select substring(v_parametros.asociar_boletos from 1 for 3) into v_inicial_boleto;

              if (v_inicial_boleto <> '930') then
                  raise exception 'Los digitos no corresponden a un boleto, verifique.';
              end if;

              /* select count (bole.id_boleto_amadeus)
                      into v_existencia
                  from obingresos.tboleto_amadeus bole
                  where bole.nro_boleto = v_parametros.asociar_boletos and bole.estado_reg = 'activo';

               if (v_existencia > 0) then*/

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
                  where bole.nro_boleto = v_parametros.asociar_boletos;


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
                  v_parametros.asociar_boletos,
                  v_datos_boletos.fecha_emision,
                  v_datos_boletos.pasajero,
                  --v_datos_boletos.nit,
                  --v_datos_boletos.ruta,
                  --v_datos_boletos.razon,
                  now(),
                  p_id_usuario
                  )RETURNING id_boleto_asociado into v_id_boleto_asociado;

               --else
                  --raise exception 'El número de boleto no se encuentra registrado, por favor verifique el número ingresado';
               --end if;
            end if;
          end if;
          /*************************************************/

          /*Mandamos los valores devueltos para finalizar el estado de la venta*/
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de Pago almacenado(a) con exito (id_venta_forma_pago'||v_id_venta_forma_pago||')');
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta_forma_pago',v_registros.id_venta_forma_pago::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'id_venta',v_venta.id_venta::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'id_proceso_wf',v_venta.id_proceso_wf::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'id_estado_wf',v_venta.id_estado_wf::varchar);

        /**********************************************************************/

        --Definicion de la respuesta

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

ALTER FUNCTION vef.ft_venta_forma_pago_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
