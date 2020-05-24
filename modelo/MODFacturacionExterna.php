<?php
/**
 * @package pXP
 * @file gen-MODFacturacionExterna.php
 * @author  (Ismael Valdivia)
 * @date 23-05-2020 14:00:00
 * @description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODFacturacionExterna extends MODbase
{
    function insertarVentaFactura()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'vef.ft_facturacion_externa_ime';
        $this->transaccion = 'VEF_INS_FAC_EXT_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('nit_cliente', 'nit_cliente', 'varchar');
        $this->setParametro('razon_social', 'razon_social', 'varchar');
        $this->setParametro('punto_venta', 'punto_venta', 'varchar');
        $this->setParametro('json_venta_detalle','json_venta_detalle','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


}

?>
