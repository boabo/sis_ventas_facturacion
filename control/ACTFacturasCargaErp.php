<?php
/**
*@package pXP
*@file gen-ACTFacturasCargaErp.php
*@author  (ismael.valdivia)
*@date 08-03-2019 15:50:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class ACTFacturasCargaErp extends ACTbase{
	function insertarFacturasErp(){
		$this->objFunc=$this->create('MODFacturasCargaErp');

			$this->res=$this->objFunc->insertarFacturasErp($this->objParam);

		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function anularFacturasErp(){
		$this->objFunc=$this->create('MODFacturasCargaErp');

			$this->res=$this->objFunc->anularFacturasErp($this->objParam);

		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	// {dev:breydi.vasquez, date: 23/04/2021, desc: 'servicio carga modificacion forma pago'}
	function modFormaPagoFacturasErp(){
		$this->objFunc=$this->create('MODFacturasCargaErp');		
		$this->res=$this->objFunc->modFormaPagoFacturasErp($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

}

?>
