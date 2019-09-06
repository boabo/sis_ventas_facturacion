<?php
/**
*@package pXP
*@file gen-ACTVenta.php
*@author  (ivaldivia)
*@date 29-05-2019 19:33:10
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
include(dirname(__FILE__).'/../reportes/RFactura.php');

class ACTCajero extends ACTbase{

	function listarVenta(){
		$this->objParam->defecto('ordenacion','id_venta');
		$this->objParam->defecto('dir_ordenacion','asc');

		//var_dump("LLEGA AQUI",$this->objParam->getParametro('tipo_factura'));exit;
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
			$this->res = $this->objReporte->generarReporteListado('MODCajero','listarVenta');
		} else{
			$this->objFunc=$this->create('MODCajero');

			$this->res=$this->objFunc->listarVenta($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarVenta(){
		$this->objFunc=$this->create('MODCajero');
		if($this->objParam->insertar('id_venta')){
			$this->res=$this->objFunc->insertarVenta($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarVenta($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarVenta(){
			$this->objFunc=$this->create('MODCajero');
		$this->res=$this->objFunc->eliminarVenta($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function listarVentaDetalle(){
		$this->objParam->defecto('ordenacion','id_venta_detalle');
		//var_dump($this->objParam->getParametro('id_venta'));
		$this->objParam->defecto('dir_ordenacion','asc');
				if ($this->objParam->getParametro('id_venta') != '') {
						$this->objParam->addFiltro("ven.id_venta = ". $this->objParam->getParametro('id_venta'));
				}

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODCajero','listarVentaDetalle');
		} else{
			$this->objFunc=$this->create('MODCajero');

			$this->res=$this->objFunc->listarVentaDetalle($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function siguienteEstadoFactura(){
		$this->objFunc=$this->create('MODCajero');
		$this->res=$this->objFunc->siguienteEstadoFactura($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function finalizarFacturaManual(){
		$this->objFunc=$this->create('MODCajero');
		$this->res=$this->objFunc->finalizarFacturaManual($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function FinalizarFactura(){
		$this->objFunc=$this->create('MODCajero');
		$this->res=$this->objFunc->FinalizarFactura($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function anularFactura(){
		$this->objFunc=$this->create('MODCajero');
		$this->res=$this->objFunc->anularFactura($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function regresarCounter(){
 	 $this->objFunc=$this->create('MODCajero');
 	 $this->res=$this->objFunc->regresarCounter($this->objParam);
 	 $this->res->imprimirRespuesta($this->res->generarJson());
  }

	function reporteFactura(){
		$this->objFunc = $this->create('MODCajero');
		$datos = array();
		$this->res = $this->objFunc->listarFactura($this->objParam);

		$datos = $this->res->getDatos();
		$datos = $datos[0];
		if ($datos['cantidad_descripciones'] > 0){
			$this->objFunc = $this->create('MODCajero');
			$this->res = $this->objFunc->listarFacturaDescripcion($this->objParam);
			$datos['detalle_descripcion'] = $this->res->getDatos();
		}

		$this->objFunc = $this->create('MODCajero');
		$this->res = $this->objFunc->listarFacturaDetalle($this->objParam);

		$datos['detalle'] = $this->res->getDatos();

		$reporte = new RFactura();
		$temp = array();
		$temp['html'] = $reporte->generarHtml($this->objParam->getParametro('formato_comprobante'),$datos);
		$this->res->setDatos($temp);
		$this->res->imprimirRespuesta($this->res->generarJson());


	}
	function getTipoUsuario(){
		 $this->objFunc=$this->create('MODCajero');
		 $this->res=$this->objFunc->getTipoUsuario($this->objParam);
		 $this->res->imprimirRespuesta($this->res->generarJson());
 }


}

?>
