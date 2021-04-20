CREATE OR REPLACE FUNCTION vef.ft_regularizacion_facturas_y_recibos_anualados (
)
RETURNS varchar AS
$body$
DECLARE
	v_consulta    		varchar;
	v_registros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
    v_datos					record;

    v_respaldo			record;


BEGIN
	  v_nombre_funcion = 'vef.ft_regularizacion_facturas_y_recibos_anualados ';

	   /*Aqui recuperamos los datos*/
       for v_registros in	 (select ven.*
                              from vef.tventa ven
                              where ven.estado = 'anulado'
                              and ven.total_venta != 0) LOOP


          if (v_registros.tipo_factura = 'computarizada') then
            update vef.tventa_forma_pago set
            monto_transaccion = 0,
            monto = 0,
            cambio = 0,
            monto_mb_efectivo = 0
            where id_venta = v_registros.id_venta;

            update vef.tventa_detalle set
            precio = 0,
            cantidad = 0
            where id_venta = v_registros.id_venta;

            update vef.tventa set
            cod_control = Null,
            total_venta_msuc = 0,
            nombre_factura = 'ANULADO',
            nit = '0',
            total_venta = 0
            where id_venta = v_registros.id_venta;

            /*AQUI ACTUALIZAR EL ESTADO DE LOS BOLETOS ASOCIADOS*/
            update vef.tboletos_asociados_fact set
            estado_reg = 'inactivo'
            where id_venta = v_registros.id_venta;
            /****************************************************/

          else

                for v_respaldo in  (select *
                              from vef.tventa ven
                              inner join vef.tventa_detalle vendet on vendet.id_venta = ven.id_venta
                              inner join vef.tventa_forma_pago fp on fp.id_venta = ven.id_venta
                              left join vef.tdosificacion dos on dos.id_dosificacion = ven.id_dosificacion
                              where ven.id_venta = v_registros.id_venta) loop

                    insert into vef.trespaldo_facturas_anuladas (
                    id_venta,
                    nombre_factura,
                    nit,
                    cod_control,
                    num_factura,
                    total_venta,
                    total_venta_msuc,
                    id_sucursal,
                    id_cliente,
                    id_punto_venta,
                    observaciones,
                    id_moneda,
                    excento,
                    fecha,
                    id_sucursal_producto,
                    id_formula,
                    id_producto,
                    cantidad,
                    precio,
                    tipo,
                    descripcion,
                    id_medio_pago,
                    monto,
                    monto_transaccion,
                    monto_mb_efectivo,
                    numero_tarjeta,
                    codigo_tarjeta,
                    tipo_tarjeta,
                    id_auxiliar,
                    fecha_reg,
                    id_usuario_reg,
                    id_dosificacion,
                    nro_autorizacion,
                    nro_mco
                    )
                    VALUES (
                    v_respaldo.id_venta,
                    v_respaldo.nombre_factura,
                    v_respaldo.nit,
                    v_respaldo.cod_control,
                    v_respaldo.nro_factura,
                    v_respaldo.total_venta,
                    v_respaldo.total_venta_msuc,
                    v_respaldo.id_sucursal,
                    v_respaldo.id_cliente,
                    v_respaldo.id_punto_venta,
                    v_respaldo.observaciones,
                    v_respaldo.id_moneda,
                    v_respaldo.excento,
                    v_respaldo.fecha,
                    v_respaldo.id_sucursal_producto,
                    v_respaldo.id_formula,
                    v_respaldo.id_producto,
                    v_respaldo.cantidad,
                    v_respaldo.precio,
                    v_respaldo.tipo,
                    v_respaldo.descripcion,
                    v_respaldo.id_medio_pago,
                    v_respaldo.monto,
                    v_respaldo.monto_transaccion,
                    v_respaldo.monto_mb_efectivo,
                    v_respaldo.numero_tarjeta,
                    v_respaldo.codigo_tarjeta,
                    v_respaldo.tipo_tarjeta,
                    v_respaldo.id_auxiliar,
                    now(),
                    v_respaldo.id_usuario_cajero,
                    v_respaldo.id_dosificacion,
                    v_respaldo.nroaut,
                    v_respaldo.nro_mco
                    );

           END LOOP;

           update vef.tventa_forma_pago set
            monto_transaccion = 0,
            monto = 0,
            cambio = 0,
            monto_mb_efectivo = 0
            where id_venta = v_registros.id_venta;

            update vef.tventa_detalle set
            precio = 0,
            cantidad = 0
            where id_venta = v_registros.id_venta;

            update vef.tventa set
            cod_control = Null,
            total_venta_msuc = 0,
            nombre_factura = 'ANULADO',
            total_venta = 0
            where id_venta = v_registros.id_venta;

            /*AQUI ACTUALIZAR EL ESTADO DE LOS BOLETOS ASOCIADOS*/
            update vef.tboletos_asociados_fact set
            estado_reg = 'inactivo'
            where id_venta = v_registros.id_venta;
            /****************************************************/


          end if;



       END LOOP;
       /*************************/

    return 'exito';

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

ALTER FUNCTION vef.ft_regularizacion_facturas_y_recibos_anualados ()
  OWNER TO postgres;
