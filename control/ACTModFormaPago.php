<?php
/**
*@package pXP
*@file gen-ACTModFormaPago.php
*@author  (miguel.mamani)
*@date 13-12-2017 21:37:47
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTModFormaPago extends ACTbase{    
			
	function listarModFormaPago(){
		$this->objParam->defecto('ordenacion','id_mod_forma_pago');
        if ($this->objParam->getParametro('fecha') != '') {
            $this->objParam->addFiltro("cfm.fecha = ''". date($this->objParam->getParametro('fecha') )."''");
        }

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODModFormaPago','listarModFormaPago');
		} else{
			$this->objFunc=$this->create('MODModFormaPago');
			
			$this->res=$this->objFunc->listarModFormaPago($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

}

?>