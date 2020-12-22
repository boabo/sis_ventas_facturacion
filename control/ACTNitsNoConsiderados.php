<?php
/**
*@package pXP
*@file gen-ACTNitsNoConsiderados.php
*@author  (maylee.perez)
*@date 21-12-2020 20:13:12
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTNitsNoConsiderados extends ACTbase{    
			
	function listarNitsNoConsiderados(){
		$this->objParam->defecto('ordenacion','id_nits_no_considerados');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODNitsNoConsiderados','listarNitsNoConsiderados');
		} else{
			$this->objFunc=$this->create('MODNitsNoConsiderados');
			
			$this->res=$this->objFunc->listarNitsNoConsiderados($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarNitsNoConsiderados(){
		$this->objFunc=$this->create('MODNitsNoConsiderados');	
		if($this->objParam->insertar('id_nits_no_considerados')){
			$this->res=$this->objFunc->insertarNitsNoConsiderados($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarNitsNoConsiderados($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarNitsNoConsiderados(){
			$this->objFunc=$this->create('MODNitsNoConsiderados');	
		$this->res=$this->objFunc->eliminarNitsNoConsiderados($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>