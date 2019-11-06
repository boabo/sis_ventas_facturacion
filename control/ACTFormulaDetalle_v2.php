<?php
/**
*@package pXP
*@file gen-ACTFormulaDetalle_v2.php
*@author  (ivaldivia)
*@date 18-09-2019 21:05:00
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTFormulaDetalle_v2 extends ACTbase{

	function listarFormulaDetalle(){
		$this->objParam->defecto('ordenacion','id_formula_detalle');

		$this->objParam->defecto('dir_ordenacion','asc');

		if($this->objParam->getParametro('id_formula')!=''){
			$this->objParam->addFiltro("detforv2.id_formula = ".$this->objParam->getParametro('id_formula'));
		}


		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODFormulaDetalle_v2','listarFormulaDetalle');
		} else{
			$this->objFunc=$this->create('MODFormulaDetalle_v2');

			$this->res=$this->objFunc->listarFormulaDetalle($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarFormulaDetalle(){
		$this->objFunc=$this->create('MODFormulaDetalle_v2');
		if($this->objParam->insertar('id_formula_detalle')){
			$this->res=$this->objFunc->insertarFormulaDetalle($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarFormulaDetalle($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarFormulaDetalle(){
			$this->objFunc=$this->create('MODFormulaDetalle_v2');
		$this->res=$this->objFunc->eliminarFormulaDetalle($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

}

?>
