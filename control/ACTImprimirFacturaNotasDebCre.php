<?php
/**
*@package pXP
*@file gen-ACTFacturasCarga.php
*@author  (ismael.valdivia)
*@date 08-03-2019 15:50:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/
include(dirname(__FILE__).'/../reportes/RFactura.php');
class ACTImprimirFacturaNotasDebCre extends ACTbase{
	function imprimirFacturaNotasDebCre(){
    $this->objFunc = $this->create('MODImprimirFacturaNotasDebCre');
		$datos = array();
		$this->res = $this->objFunc->listarFactura($this->objParam);

		$datos = $this->res->getDatos();
		$datos = $datos[0];

		$this->objFunc = $this->create('MODImprimirFacturaNotasDebCre');
		$this->res = $this->objFunc->listarFacturaDetalle($this->objParam);

		$datos['detalle'] = $this->res->getDatos();

		 $fecha_actual = date("d/m/Y");
		 $fecha_venta = $datos["fecha_venta"];
		 $tipo_usuario = $datos["tipo_usuario"];


  		$reporte = new RFactura();
  		$temp = array();
  		$temp['html'] = $reporte->generarHtml($this->objParam->getParametro('formato_comprobante'),$datos);

    //  var_dump("aqui llega data",$temp['html']);
      //
  		// $this->res->setDatos($temp);
  		// $json = $this->res->generarJson();
  		$this->res->imprimirRespuesta($temp['html']);
	}


}

?>
