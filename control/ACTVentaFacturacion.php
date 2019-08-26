<?php
/**
*@package pXP
*@file gen-ACTVentaFacturacion.php
*@author  (ivaldivia)
*@date 10-05-2019 19:08:47
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTVentaFacturacion extends ACTbase{

	function listarVentaFacturacion(){
		$this->objParam->defecto('ordenacion','id_venta');
		$this->objParam->defecto('dir_ordenacion','asc');

		//var_dump("LLEGA AQUI",$this->objParam->getParametro('fecha'));
		if ($this->objParam->getParametro('id_punto_venta') != '') {
				$this->objParam->addFiltro(" fact.id_punto_venta = ". $this->objParam->getParametro('id_punto_venta')." and fact.tipo_factura =''".$this->objParam->getParametro('tipo_factura')."''");
			}

		if ($this->objParam->getParametro('pes_estado') != '') {
			if ($this->objParam->getParametro('pes_estado') == 'caja') {
					$this->objParam->addFiltro(" fact.estado = ''caja'' and fact.fecha = ''".$this->objParam->getParametro('fecha')."''");
			}elseif ($this->objParam->getParametro('pes_estado') == 'borrador') {
					$this->objParam->addFiltro(" fact.estado = ''borrador'' and fact.fecha = ''".$this->objParam->getParametro('fecha')."''");
			}elseif ($this->objParam->getParametro('pes_estado') == 'finalizado') {
					$this->objParam->addFiltro(" fact.estado = ''finalizado'' and fact.fecha = ''".$this->objParam->getParametro('fecha')."''");
			}elseif ($this->objParam->getParametro('pes_estado') == 'anulado') {
					$this->objParam->addFiltro(" fact.estado = ''anulado'' and fact.fecha = ''".$this->objParam->getParametro('fecha')."''");
			}
		}

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODVentaFacturacion','listarVentaFacturacion');
		} else{
			$this->objFunc=$this->create('MODVentaFacturacion');

			$this->res=$this->objFunc->listarVentaFacturacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function listarVentaCaja(){
		$this->objParam->defecto('ordenacion','id_venta');
		$this->objParam->defecto('dir_ordenacion','asc');

		//var_dump("LLEGA AQUI",$this->objParam->getParametro('tipo_factura'));exit;
		if ($this->objParam->getParametro('id_punto_venta') != '') {
				$this->objParam->addFiltro(" fact.id_punto_venta = ". $this->objParam->getParametro('id_punto_venta')." and fact.tipo_factura =''".$this->objParam->getParametro('tipo_factura')."''");
			}

		if ($this->objParam->getParametro('pes_estado') != '') {
			if ($this->objParam->getParametro('pes_estado') == 'caja') {
					$this->objParam->addFiltro(" fact.estado in(''caja'') ");
			}elseif ($this->objParam->getParametro('pes_estado') == 'borrador') {
					$this->objParam->addFiltro(" fact.estado in( ''borrador'') ");
			}
		}

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODVentaFacturacion','listarVentaFacturacion');
		} else{
			$this->objFunc=$this->create('MODVentaFacturacion');

			$this->res=$this->objFunc->listarVentaFacturacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarVentaFacturacion(){
		$this->objFunc=$this->create('MODVentaFacturacion');
		if($this->objParam->insertar('id_venta')){
			$this->res=$this->objFunc->insertarVentaFacturacion($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarVentaFacturacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarVentaFacturacion(){
			$this->objFunc=$this->create('MODVentaFacturacion');
		$this->res=$this->objFunc->eliminarVentaFacturacion($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function obtenerApertura(){
		 $this->objFunc=$this->create('MODVentaFacturacion');
		 $this->res=$this->objFunc->obtenerApertura($this->objParam);
		 $this->res->imprimirRespuesta($this->res->generarJson());
 }

 function obtenerAperturaCounter(){
		$this->objFunc=$this->create('MODVentaFacturacion');
		$this->res=$this->objFunc->obtenerAperturaCounter($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
}

 function siguienteEstadoRecibo(){
	 $this->objFunc=$this->create('MODVentaFacturacion');
	 $this->res=$this->objFunc->siguienteEstadoRecibo($this->objParam);
	 $this->res->imprimirRespuesta($this->res->generarJson());
 }

 function insertarVentaCompleta(){
		 $this->objFunc=$this->create('MODVentaFacturacion');
		 $this->res=$this->objFunc->insertarVentaCompleta($this->objParam);
		 $this->res->imprimirRespuesta($this->res->generarJson());
 }

 function insertarFacturacionManual(){
		 $this->objFunc=$this->create('MODVentaFacturacion');
		 $this->res=$this->objFunc->insertarFacturacionManual($this->objParam);
		 $this->res->imprimirRespuesta($this->res->generarJson());
 }



}

?>
