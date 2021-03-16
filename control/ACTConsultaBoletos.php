<?php
/**
*@package pXP
*@file gen-ACTConsultaBoletos.php
*@author  (admin)
*@date 12-10-2017 21:15:26
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTConsultaBoletos extends ACTbase{

	function listarConsultaBoletos(){
		$this->objParam->defecto('ordenacion','id_boleto');

        if($this->objParam->getParametro('gestion') != ''){
            $this->objParam->addFiltro(" cbs.gestion = ".$this->objParam->getParametro('gestion'));
        }

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODConsultaBoletos','listarConsultaBoletos');
		} else{
			$this->objFunc=$this->create('MODConsultaBoletos');

			$this->res=$this->objFunc->listarConsultaBoletos($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function consultaBoletoInhabilitacion(){
		$this->objFunc=$this->create('MODConsultaBoletos');
		$this->res=$this->objFunc->consultaBoletoInhabilitacion($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
}

?>
