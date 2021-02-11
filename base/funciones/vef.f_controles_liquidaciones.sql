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
    v_nroaut				varchar;

BEGIN
  	v_nombre_funcion = 'informix.f_liquidaciones';

    v_resp[0] =  false;
    v_resp[1] = 'exito';

    CREATE TEMP TABLE liquidevolucion_temp (
			            nroliqui varchar(20),
                  fecha date,
                  tcambio numeric,
                  moneda varchar(3),
                  estpago varchar(1),
                  estado varchar(1),
                  nroaut numeric,
                  nrofac numeric,
                  docmnt varchar(6)
                  ) ON COMMIT DROP;


    CREATE TEMP TABLE t_liquides_temp (
                  pais varchar(3),
                  estacion varchar(3),
                  docmnt varchar(6),
                  nroliqui varchar(20),
                  renglon smallint,
                  impcom varchar(6),
                  tipdoc varchar(6),
                  sobre varchar(1),
                  importe numeric,
                  tipreng varchar(1),
                  idimpcom varchar(1)
                ) ON COMMIT DROP;

    CREATE TEMP TABLE tipo_cambio_liq_temp (
                    pais varchar(3),
                    fecha date,
                    tcambio numeric(15,7)
                    ) ON COMMIT DROP;


  insert into liquidevolucion_temp
    SELECT nroliqui, fecha, tcambio, moneda, estpago, estado, nroaut, nrofac, docmnt FROM informix.liquidevolucion;

	insert into t_liquides_temp
    SELECT * FROM informix.t_liquides;

	insert into tipo_cambio_liq_temp
    SELECT * FROM informix.tipo_cambio_liq;

	--Sentencia de la consulta

      IF NOT EXISTS (SELECT 1 FROM liquidevolucion_temp WHERE trim(nroliqui) = trim(p_liquidaciones)) THEN

          v_resp[0] = true;
          v_resp[1] = 'No existe el numero de liquidacion '||p_liquidaciones||' en el Sistema de Ingresos';
          return v_resp;

      END IF;

      IF EXISTS(SELECT 1 FROM liquidevolucion_temp WHERE trim(nroliqui) = trim(p_liquidaciones) AND estpago = 'P' AND docmnt != 'DEVWEB') THEN

          v_resp[0] = true;
          v_resp[1] = 'No se puede generar la factura porque la liquidacion '||p_liquidaciones||' ya se encuentra pagada.';
          return v_resp;

      END IF;

      IF EXISTS(SELECT 1 FROM liquidevolucion_temp WHERE trim(nroliqui) = trim(p_liquidaciones) AND estado = '9') THEN

          v_resp[0] = true;
          v_resp[1] = 'No se puede generar la factura porque la liquidacion '||p_liquidaciones||' se encuentra anulada. ';
          return v_resp;

      END IF;

      IF EXISTS(SELECT 1 FROM liquidevolucion_temp WHERE trim(nroliqui) = trim(p_liquidaciones) AND estpago='P') THEN

          v_resp[0] = true;
          v_resp[1] = 'No se puede anular la factura porque la liquidacion '||p_liquidaciones||' se encuentra pagada.';
          return v_resp;

      END IF;

      IF EXISTS (SELECT 1 FROM liquidevolucion_temp WHERE trim(nroliqui) = trim(p_liquidaciones) and nrofac > 0 and nroaut > 0  ) THEN
         v_resp[0] = true;
         v_resp[1] = 'EL nro de liquidacion '||p_liquidaciones||' ya cuenta con un nro de factura y autorizacion.';
         return v_resp;
      END IF;


    SELECT sum(ldes.importe) as importe, ldev.moneda, ldev.tcambio as cambio_moneda, c.tcambio as cambio_dolar
        into v_res_liq
    FROM t_liquides_temp ldes
    INNER JOIN liquidevolucion_temp ldev on ldev.nroliqui=ldes.nroliqui
    INNER JOIN tipo_cambio_liq_temp c on c.fecha=ldev.fecha and trim(c.pais)='BO'
    WHERE trim(ldes.nroliqui) = trim(p_liquidaciones) and ldes.idimpcom='F'
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
