CREATE OR REPLACE FUNCTION vef.f_controles_liquidaciones (
  p_liquidaciones text,
  p_venta_total numeric
)
RETURNS varchar [] AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ventas
 FUNCION: 		vef.f_controles_liquidaciones
 DESCRIPCION:   Controles para el nro de liquidacion
 AUTOR: 		 breydi.vasquez
 FECHA:	        20-01-2021
 COMENTARIOS:
***************************************************************************/
DECLARE
	v_consulta    			varchar;
	v_nombre_funcion   		text;
    v_resp					varchar[];
    v_nro_liq				integer;
	v_count_liq				integer;
    v_res_count_liq			integer;
    v_res_liq				record;
    v_monto					numeric;
    v_liquidacion			varchar;
    v_union					varchar='';
    v_data					varchar[];
    v_in					varchar;
    v_nroaut				varchar;

BEGIN
  	v_nombre_funcion = 'informix.f_liquidaciones';

    v_resp[0] =  false;
    v_resp[1] = 'exito';
    v_data = string_to_array(p_liquidaciones, ',');
	--Sentencia de la consulta

   	FOREACH v_liquidacion IN ARRAY v_data

    LOOP

      v_union = v_union ||''||v_liquidacion||''||' ,';

      IF NOT EXISTS (SELECT 1 FROM informix.liquidevolucion WHERE trim(nroliqui) = trim(v_liquidacion)) THEN

          v_resp[0] = true;
          v_resp[1] = 'No existe el numero de liquidacion '||v_liquidacion||' en el Sistema de Ingresos';
          return v_resp;

      END IF;

      IF EXISTS(SELECT 1 FROM informix.liquidevolucion WHERE trim(nroliqui) = trim(v_liquidacion) AND estpago = 'P' AND docmnt != 'DEVWEB') THEN

          v_resp[0] = true;
          v_resp[1] = 'No se puede generar la factura porque la liquidacion '||v_liquidacion||' ya se encuentra pagada.';
          return v_resp;

      END IF;

      IF EXISTS(SELECT 1 FROM informix.liquidevolucion WHERE trim(nroliqui) = trim(v_liquidacion) AND estado = '9') THEN

          v_resp[0] = true;
          v_resp[1] = 'No se puede generar la factura porque la liquidacion '||v_liquidacion||' se encuentra anulada. ';
          return v_resp;

      END IF;

      IF EXISTS(SELECT 1 FROM informix.liquidevolucion WHERE trim(nroliqui) = trim(v_liquidacion) AND estpago='P') THEN

          v_resp[0] = true;
          v_resp[1] = 'No se puede anular la factura porque la liquidacion '||v_liquidacion||' se encuentra pagada.';
          return v_resp;

      END IF;

      IF EXISTS (SELECT 1 FROM informix.liquidevolucion WHERE trim(nroliqui) = trim(v_liquidacion) and nrofac > 0 and nroaut > 0  ) THEN
         v_resp[0] = true;
         v_resp[1] = 'EL nro de liquidacion '||v_liquidacion||' ya cuenta con un nro de factura y autorizacion.';
         return v_resp;
      END IF;

    END LOOP;

    v_in = substring(v_union, 1, (char_length(v_union) - 1));

    SELECT sum(ldes.importe) as importe, ldev.moneda, ldev.tcambio as cambio_moneda, c.tcambio as cambio_dolar
        into v_res_liq
    FROM informix.t_liquides ldes
    INNER JOIN informix.liquidevolucion ldev on ldev.nroliqui=ldes.nroliqui
    INNER JOIN informix.tipo_cambio_liq c on c.fecha=ldev.fecha and trim(c.pais)='BO'
    WHERE ldes.nroliqui = ANY (string_to_array(v_in,',')) and ldes.idimpcom='F'
    GROUP BY ldev.moneda, c.tcambio, ldev.tcambio;

     IF v_res_liq.importe is null AND v_res_liq.moneda IS NULL THEN

            v_resp[0] = true;
            v_resp[1] = 'El numero de liquidacion '||p_liquidaciones||' no cuenta con descuentos que se consideren factura';
            return v_resp;

     ELSE

            IF 	v_res_liq.moneda = 'EUR' THEN

                v_monto = v_res_liq.importe / v_res_liq.cambio_moneda * v_res_liq.cambio_dolar;

            ELSIF 	v_res_liq.moneda = 'USD' THEN

                v_monto = v_res_liq.importe * v_res_liq.cambio_moneda;

            ELSIF	v_res_liq.moneda = 'BOB' THEN

                v_monto = v_res_liq.importe;

            END IF;

     END IF;




	-- CONTROL DE MONTOS FACTURA Y COMISION
    IF p_venta_total != v_monto THEN
    	v_resp[0] = true;
        v_resp[1] = 'El monto total del detalle de pago es: '||p_venta_total||' , el cual difiere del monto recuperado del numero de liquidacion: '||p_liquidaciones||' el cual es: '||v_monto;
        return v_resp;
    END IF;

    return v_resp;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION vef.f_controles_liquidaciones (p_liquidaciones text, p_venta_total numeric)
  OWNER TO "postgres";
