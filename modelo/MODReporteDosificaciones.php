<?php
/**
 * @package pXP
 * @file gen-MODReporteDosificaciones.php
 * @author  (Ismael Valdivia)
 * @date 12-03-2021 9:20:12
 * @description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODReporteDosificaciones extends MODbase
{

    function __construct(CTParametro $pParam)
    {
        parent::__construct($pParam);
    }

    function listarReporteDosificaciones()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'vef.ft_rep_dosificaciones';
        $this->transaccion = 'VEF_REP_DOSIFI_SEL';
        $this->tipo_procedimiento = 'SEL';
        //$this->setCount(false);

        $this->setParametro('tipo_generacion', 'tipo_generacion', 'varchar');
        $this->setParametro('nombre_sistema', 'nombre_sistema', 'varchar');
        $this->setParametro('id_sucursal', 'id_sucursal', 'integer');
        $this->setParametro('estado_dosificacion', 'estado_dosificacion', 'varchar');


        //var_dump("aqui llega data",$this->objParam->getParametro('codigo_auxiliar'));
        //captura parametros adicionales para el count
      //  $this->capturaCount('total_debe', 'numeric');
      //  $this->capturaCount('total_haber', 'numeric');
        //$this->capturaCount('total_saldo', 'numeric');


        //Definicion de la lista del resultado del query
        $this->captura('estacion', 'varchar');
        $this->captura('desc_sucursal', 'varchar');
        $this->captura('desc_actividad_economica', 'varchar');
        $this->captura('nro_autorizacion', 'varchar');
        $this->captura('nro_tramite', 'varchar');
        $this->captura('nombre_sistema', 'varchar');
        $this->captura('nro_inicial', 'numeric');
        $this->captura('nro_final', 'numeric');
        $this->captura('fecha_dosificacion', 'varchar');
        $this->captura('fecha_inicio_emi', 'varchar');
        $this->captura('fecha_limite', 'varchar');
        $this->captura('dias_restante', 'integer');
        $this->captura('codigo_sucursal', 'numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        //
        //var_dump("llega el reporte inde",$this->respuesta);
        return $this->respuesta;
    }
}

?>
