<?php
/**
 * @package pXP
 * @file gen-MODReporteDepositos.php
 * @author  (Ismael Valdivia)
 * @date 12-03-2021 9:20:12
 * @description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODReporteDepositos extends MODbase
{

    function __construct(CTParametro $pParam)
    {
        parent::__construct($pParam);
    }

    function listarReporteDepositos()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'vef.ft_rep_depositos';
        $this->transaccion = 'VEF_REP_DEP_VEN_SEL';
        $this->tipo_procedimiento = 'SEL';
        //$this->setCount(false);

        $this->setParametro('desde', 'desde', 'varchar');
        $this->setParametro('hasta', 'hasta', 'varchar');
        $this->setParametro('id_punto_venta', 'id_punto_venta', 'int4');
        $this->setParametro('formato_reporte', 'formato_reporte', 'varchar');

        $this->capturaCount('total_importe_ml', 'numeric');
        $this->capturaCount('total_importe_me', 'numeric');
        $this->capturaCount('total_deposito_ml', 'numeric');
        $this->capturaCount('total_deposito_me', 'numeric');
        $this->capturaCount('totales_venta_ml', 'numeric');
        $this->capturaCount('totales_venta_me', 'numeric');
        $this->capturaCount('total_diferencia', 'numeric');



        //var_dump("aqui llega data",$this->objParam->getParametro('codigo_auxiliar'));
        //captura parametros adicionales para el count
      //  $this->capturaCount('total_debe', 'numeric');
      //  $this->capturaCount('total_haber', 'numeric');
        //$this->capturaCount('total_saldo', 'numeric');


        //Definicion de la lista del resultado del query
        $this->captura('fecha_venta', 'date');
        $this->captura('nro_deposito', 'varchar');
        $this->captura('fecha_deposito', 'date');
        $this->captura('importe_ml', 'numeric');
        $this->captura('importe_usd', 'numeric');
        $this->captura('cuenta_bancaria', 'varchar');
        $this->captura('cajero', 'varchar');
        $this->captura('usuario_registro', 'varchar');
        $this->captura('observaciones', 'varchar');
        $this->captura('tipo_deposito', 'varchar');
        $this->captura('punto_venta', 'varchar');

        $this->captura('deposito_ml', 'numeric');
        $this->captura('deposito_me', 'numeric');
        $this->captura('tipo_cambio', 'numeric');
        $this->captura('total_venta_ml', 'numeric');
        $this->captura('total_venta_me', 'numeric');
        $this->captura('diferencia', 'numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        //var_dump("llega el reporte inde",$this->respuesta);
        return $this->respuesta;
    }
}

?>
