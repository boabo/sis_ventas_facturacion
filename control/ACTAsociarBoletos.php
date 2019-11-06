<?php
/**
*@package pXP
*@file gen-ACTAsociarBoletos.php
*@author  (ivaldivia)
*@date 18-10-2019 11:30:00
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTAsociarBoletos extends ACTbase{

	function listarAsociarBoletos(){
		$this->objParam->defecto('ordenacion','id_boleto_asociado');
		$this->objParam->defecto('dir_ordenacion','asc');
    // var_dump("llega aqui la venta",$this->objParam->getParametro('id_venta_factura'));
    if ($this->objParam->getParametro('id_venta_factura') != '') {
				$this->objParam->addFiltro(" bol.id_venta = " .$this->objParam->getParametro('id_venta_factura'));
		}

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODAsociarBoletos','listarAsociarBoletos');
		} else{
			$this->objFunc=$this->create('MODAsociarBoletos');
			$this->res=$this->objFunc->listarAsociarBoletos($this->objParam);

		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarAsociarBoletos(){
		$this->objFunc=$this->create('MODAsociarBoletos');
		if($this->objParam->insertar('id_boleto_asociado')){
			$this->res=$this->objFunc->insertarAsociarBoletos($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarAsociarBoletos($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarAsociarBoletos(){
			$this->objFunc=$this->create('MODAsociarBoletos');
		$this->res=$this->objFunc->eliminarAsociarBoletos($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}


}

?>
