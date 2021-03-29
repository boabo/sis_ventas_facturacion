<?php
/**
 * @package pXP
 * @file gen-MODReporteEmisionBoletos.php
 * @author  (Ismael Valdivia)
 * @date 11-02-2021 10:10:12
 * @description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODReporteEmisionBoletos extends MODbase
{

    function __construct(CTParametro $pParam)
    {
        parent::__construct($pParam);
    }

    function listarDetalleEmisionBoletos()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'vef.ft_rep_emision_boletos';
        $this->transaccion = 'VEF_REP_EMI_BOL_SEL';
        $this->tipo_procedimiento = 'SEL';
        //$this->setCount(false);

        $this->setParametro('id_auxiliar', 'id_auxiliar', 'int4');
        $this->setParametro('codigo_auxiliar', 'codigo_auxiliar', 'varchar');
        $this->setParametro('desde', 'desde', 'varchar');
        $this->setParametro('hasta', 'hasta', 'varchar');
        $this->setParametro('id_punto_venta', 'id_punto_venta', 'int4');
        $this->setParametro('formato_reporte', 'formato_reporte', 'varchar');

        //var_dump("aqui llega data",$this->objParam->getParametro('codigo_auxiliar'));
        //captura parametros adicionales para el count
        $this->capturaCount('total_debe', 'numeric');
        $this->capturaCount('total_haber', 'numeric');
        //$this->capturaCount('total_saldo', 'numeric');


        //Definicion de la lista del resultado del query
        $this->captura('fecha_factura', 'date');
        $this->captura('nro_factura', 'numeric');
        $this->captura('nro_documento', 'varchar');
        $this->captura('ruta', 'varchar');
        $this->captura('pasajero', 'varchar');
        $this->captura('debe', 'numeric');
        $this->captura('haber', 'numeric');
        $this->captura('tipo_factura', 'varchar');
        $this->captura('punto_venta', 'varchar');
        $this->captura('cuenta_auxiliar', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        //var_dump("llega el reporte inde",$this->respuesta);
        return $this->respuesta;
    }
    /********************************************************************************************************/
    function listarPuntosVentas()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'vef.ft_rep_emision_boletos';
        $this->transaccion = 'VEF_REP_PUN_VEN';
        $this->tipo_procedimiento = 'SEL';
        $this->setCount(false);


        $this->setParametro('id_auxiliar', 'id_auxiliar', 'int4');
        $this->setParametro('codigo_auxiliar', 'codigo_auxiliar', 'varchar');
        $this->setParametro('desde', 'desde', 'varchar');
        $this->setParametro('hasta', 'hasta', 'varchar');
        $this->setParametro('id_punto_venta', 'id_punto_venta', 'int4');
        //Definicion de la lista del resultado del query
        $this->captura('punto_venta', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        //var_dump("llega el reporte inde",$this->respuesta);
        return $this->respuesta;
    }

    function reporteResumenDetalle()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'vef.ft_rep_resumen_detalle_cta_cte';
        $this->transaccion = 'VEF_REP_RESU_DET_SEL';
        $this->tipo_procedimiento = 'SEL';
        $this->setCount(false);

        $this->setParametro('id_auxiliar', 'id_auxiliar', 'int4');
        $this->setParametro('codigo_auxiliar', 'codigo_auxiliar', 'varchar');
        $this->setParametro('desde', 'desde', 'varchar');
        $this->setParametro('hasta', 'hasta', 'varchar');
        $this->setParametro('id_punto_venta', 'id_punto_venta', 'int4');
        $this->setParametro('formato_reporte', 'formato_reporte', 'varchar');

        //var_dump("aqui llega data",$this->objParam->getParametro('codigo_auxiliar'));
        //captura parametros adicionales para el count
        $this->capturaCount('total_debe', 'numeric');
        $this->capturaCount('total_haber', 'numeric');
        //$this->capturaCount('total_saldo', 'numeric');


        //Definicion de la lista del resultado del query
        $this->captura('fecha_factura', 'date');
        $this->captura('nro_factura', 'numeric');
        $this->captura('nro_documento', 'varchar');
        $this->captura('ruta', 'varchar');
        $this->captura('pasajero', 'varchar');
        $this->captura('debe', 'numeric');
        $this->captura('haber', 'numeric');
        $this->captura('tipo_factura', 'varchar');
        $this->captura('punto_venta', 'varchar');
        $this->captura('cuenta_auxiliar', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        //var_dump("llega el reporte inde",$this->respuesta);
        return $this->respuesta;
    }


    function reporteResumenCtaCte()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'vef.ft_rep_resumen_detalle_cta_cte';
        $this->transaccion = 'VEF_REP_RESU_CTA_SEL';
        $this->tipo_procedimiento = 'SEL';
        $this->setCount(false);

        $this->setParametro('id_auxiliar', 'id_auxiliar', 'int4');
        $this->setParametro('codigo_auxiliar', 'codigo_auxiliar', 'varchar');
        $this->setParametro('desde', 'desde', 'varchar');
        $this->setParametro('hasta', 'hasta', 'varchar');
        $this->setParametro('id_punto_venta', 'id_punto_venta', 'int4');
        $this->setParametro('formato_reporte', 'formato_reporte', 'varchar');


        //Definicion de la lista del resultado del query
        $this->captura('mes', 'varchar');
        $this->captura('total_debe', 'numeric');
        $this->captura('total_haber', 'numeric');
        $this->captura('fecha_factura', 'date');
        $this->captura('nro_factura', 'varchar');
        $this->captura('tipo', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        //var_dump("llega el reporte inde",$this->respuesta);
        return $this->respuesta;
    }




}

?>
