<?php
/**
*@package pXP
*@file gen-ACTVentaDetalleFacturacion.php
*@author  (ivaldivia)
*@date 10-05-2019 19:33:22
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTVentaDetalleFacturacion extends ACTbase{

	function listarVentaDetalleFacturacion(){
		$this->objParam->defecto('ordenacion','id_venta_detalle');

		// var_dump($this->objParam->getParametro('id_venta'));exit;
		if($this->objParam->getParametro('id_venta')!=''){
			$this->objParam->addFiltro("factdet.id_venta = ".$this->objParam->getParametro('id_venta'));
		}

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODVentaDetalleFacturacion','listarVentaDetalleFacturacion');
		} else{
			$this->objFunc=$this->create('MODVentaDetalleFacturacion');

			            if ($this->objParam->getParametro('id_venta') != '') {
			                $this->res=$this->objFunc->listarVentaDetalleFacturacion($this->objParam);
			                $temp = Array();
			                $temp['venta_total'] = $this->res->extraData['venta_total'];
			                $temp['tipo_reg'] = 'summary';
			                //$temp['id_deposito'] = 0;

			                $this->res->total++;
			                $this->res->addLastRecDatos($temp);

			            }else{

										$this->res=$this->objFunc->listarVentaDetalleFacturacion($this->objParam);
				}
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarVentaDetalleFacturacion(){
		$this->objFunc=$this->create('MODVentaDetalleFacturacion');
		if($this->objParam->insertar('id_venta_detalle')){
			$this->res=$this->objFunc->insertarVentaDetalleFacturacion($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarVentaDetalleFacturacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarVentaDetalleFacturacion(){
			$this->objFunc=$this->create('MODVentaDetalleFacturacion');
		$this->res=$this->objFunc->eliminarVentaDetalleFacturacion($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function verificarExcento(){
 		 $this->objFunc=$this->create('MODVentaDetalleFacturacion');
 		 $this->res=$this->objFunc->verificarExcento($this->objParam);
 		 $this->res->imprimirRespuesta($this->res->generarJson());
  }

	function actualizarExcento(){
 		 $this->objFunc=$this->create('MODVentaDetalleFacturacion');
 		 $this->res=$this->objFunc->actualizarExcento($this->objParam);
 		 $this->res->imprimirRespuesta($this->res->generarJson());
  }


}

?>
