CREATE OR REPLACE FUNCTION vef.f_trig_apertura_cierre_caja (
)
RETURNS trigger AS
$body$
/**************************************************************************
 SISTEMA DE VENTAS
***************************************************************************
 SCRIPT: 		trig_tapertura_cierre_caja
 DESCRIPCIÓN: 	Valida informacion modificacion en la apertura cierre caja

 AUTOR: 		Gonzalo Sarmiento
 FECHA:			22-11-2017
 COMENTARIOS:
***************************************************************************
 HISTORIA DE MODIFICACIONES:

***************************************************************************/
--------------------------
-- CUERPO DE LA FUNCIÓN --
--------------------------


DECLARE

BEGIN

   IF TG_OP = 'UPDATE' THEN

        BEGIN

            if (exists (select 1 from pg_catalog.pg_namespace where nspname = 'obingresos' ))then
            	IF(OLD.fecha_apertura_cierre != NEW.fecha_apertura_cierre)THEN
                    if (exists (select 1 from obingresos.tboleto_amadeus b
                                where b.estado_reg = 'activo' and
                                    b.id_punto_venta = OLD.id_punto_venta and
                                    b.fecha_emision = OLD.fecha_apertura_cierre and
                                    b.estado='revisado' and
                                    b.id_usuario_cajero = OLD.id_usuario_cajero)) then
                        raise exception 'Ya se emitieron boletos con esta apertura de caja. Debe eliminar esos boletos para poder cambiar la fecha de la apertura';
                    end if;
                END IF;
            end if;

        END;
   end if;


   RETURN NEW;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;