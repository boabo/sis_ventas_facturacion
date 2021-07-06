<?php
/**
*@package pXP
*@file gen-MODDosificacion.php
*@author  (Ismael Valdivia)
*@date 11-05-2021 13:00:56
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODInsertarNuevaBD extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

    function insertarDb(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.f_creacion_new_db_facturacion';
        $this->transaccion='VF_INS_NEW_DB_IME';
        $this->tipo_procedimiento='IME';
        //definicion de variables
        $this->tipo_conexion='seguridad';
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

		function obtenerDatosVariablesGlobales(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.f_creacion_new_db_facturacion';
        $this->transaccion='VF_GET_VG_DB_SEL';
        $this->tipo_procedimiento='SEL';
        //definicion de variables
				$this->tipo_conexion='seguridad';

				$this->setCount(false);
				//Definicion de la lista del resultado del query
				$this->captura('replicacion_data','varchar');
				$this->captura('gestion_actual','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>
