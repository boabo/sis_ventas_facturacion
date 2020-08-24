<?php
/**
*@package pXP
*@file gen-ACTFacturasCarga.php
*@author  (ismael.valdivia)
*@date 08-03-2019 15:50:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class ACTFacturasCarga extends ACTbase{
	function insertarFacturas(){
		$this->objFunc=$this->create('MODFacturasCarga');

			$this->res=$this->objFunc->insertarFacturas($this->objParam);

		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function anularFacturas(){
		$this->objFunc=$this->create('MODFacturasCarga');

			$this->res=$this->objFunc->anularFacturas($this->objParam);

		$this->res->imprimirRespuesta($this->res->generarJson());
	}


}

?>
