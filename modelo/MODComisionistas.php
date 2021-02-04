<?php

/**
 * @package pXP
 * @file gen-MODComisionistas.php
 *@author  (Ismael Valdivia)
 *@date 25-01-2020 11:30:00
 * @description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */
class MODComisionistas extends MODbase
{

    function __construct(CTParametro $pParam)
    {
        parent::__construct($pParam);
    }

    function reporteComisionistas(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'vef.ft_rep_comisionistas_sel';
        $this->transaccion = 'VEF_REPCOMISI_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->setCount(false);

        $this->setParametro('filtro_sql', 'filtro_sql', 'varchar');
        $this->setParametro('id_periodo', 'id_periodo', 'integer');
        $this->setParametro('tipo_reporte', 'tipo_reporte', 'varchar');
        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        $this->setParametro('id_periodo_inicio', 'id_periodo_inicio', 'integer');
        $this->setParametro('id_periodo_final', 'id_periodo_final', 'integer');

        //Definicion de la lista del resultado del query
        $this->captura('fecha_factura', 'date');
        $this->captura('desc_ruta', 'varchar');
        $this->captura('sistema_origen', 'varchar');
        $this->captura('nit', 'numeric');
        $this->captura('razon_social', 'varchar');
        $this->captura('carnet_ide', 'varchar');
        $this->captura('cantidad', 'numeric');
        $this->captura('precio_unitario', 'numeric');
        $this->captura('precio_total', 'numeric');
        $this->captura('natural_simplificado', 'varchar');
        $this->captura('nro_factura', 'numeric');
        $this->captura('razon_empresa', 'varchar');
        $this->captura('nit_empresa', 'varchar');
        $this->captura('gestion', 'integer');
        $this->captura('periodo_num_ini', 'varchar');
        $this->captura('periodo_num_fin', 'varchar');
        $this->captura('periodo_literal_inicio', 'varchar');
        $this->captura('periodo_literal_fin', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta); exit;
        $this->ejecutarConsulta();
        //var_dump("aqui repuesta",$this->respuesta);
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function resumenComisionistas(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'vef.ft_rep_comisionistas_sel';
        $this->transaccion = 'VEF_RESUCOMISI_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->setCount(false);

        $this->setParametro('filtro_sql', 'filtro_sql', 'varchar');
        $this->setParametro('id_periodo', 'id_periodo', 'integer');
        $this->setParametro('tipo_reporte', 'tipo_reporte', 'varchar');
        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        $this->setParametro('id_gestion', 'id_gestion', 'integer');
        $this->setParametro('id_periodo_inicio', 'id_periodo_inicio', 'integer');
        $this->setParametro('id_periodo_final', 'id_periodo_final', 'integer');


        //Definicion de la lista del resultado del query
        $this->captura('nit', 'varchar');
        $this->captura('total_acumulado', 'numeric');
        $this->captura('mes_envio', 'varchar');
        $this->captura('gestion', 'integer');
        $this->captura('mes_inicio', 'varchar');
        $this->captura('mes_final', 'varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta); exit;
        $this->ejecutarConsulta();
        //var_dump("aqui repuesta",$this->respuesta);
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function TraerAcumulados(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'vef.ft_traer_acumulados_ime';
        $this->transaccion = 'VF_TRAER_ACUMUL_IME';
        $this->tipo_procedimiento = 'IME';//tipo de transaccion       

        $this->setParametro('comisionistas_traer', 'comisionistas_traer', 'varchar');      


        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta); exit;
        $this->ejecutarConsulta();
        //var_dump("aqui repuesta",$this->respuesta);
        //Devuelve la respuesta
        return $this->respuesta;
    }

}

?>
