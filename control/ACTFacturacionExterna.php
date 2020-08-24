<?php
/**
*@package pXP
*@file gen-ACTFacturacionExterna.php
* @author  (Ismael Valdivia)
* @date 23-05-2020 14:00:00
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo*/

include(dirname(__FILE__).'/../reportes/RReporteFacturaA4.php');

class ACTFacturacionExterna extends ACTbase{

	/********************************Insertamos las facturas********************************/
	function insertarVentaFactura(){
			$this->objFunc=$this->create('MODFacturacionExterna');
			$this->res=$this->objFunc->insertarVentaFactura($this->objParam);
			$this->res->imprimirRespuesta($this->res->generarJson());
	}
	/***************************************************************************************/
	function insertarVentaFacturaPrueba(){
			$this->objFunc=$this->create('MODFacturacionExterna');
			$this->res=$this->objFunc->insertarVentaFacturaPrueba($this->objParam);
			$this->res->imprimirRespuesta($this->res->generarJson());
	}

	/*Servicio para imprimir la factura*/
	function reporteFacturaCarta()
	{
		$this->objFunc = $this->create('MODCajero');
		$this->res = $this->objFunc->listarFactura($this->objParam);


		$this->objFunc = $this->create('MODCajero');
		$this->detalle = $this->objFunc->listarFacturaDetalle($this->objParam);

		/*Aqui recueperar la casa matriz*/
		$this->objFunc = $this->create('MODCajero');
		$this->casaMatriz = $this->objFunc->listarCasaMatriz($this->objParam);

		/*Aqui obtenemos la condicion para ver que formato de reporte se imprimi*/
		$this->formato_reporte = "sin_cantidad";
		/************************************************************************/


		//obtener titulo del reporte
		$titulo = 'Factura';
		//Genera el nombre del archivo (aleatorio + titulo)
		$nombreArchivo = uniqid(md5(session_id()) . $titulo);
		$nombreArchivo .= '.pdf';

		$this->objParam->addParametro('orientacion', 'P');
		$this->objParam->addParametro('tamano', 'LETTER');
		$this->objParam->addParametro('nombre_archivo', $nombreArchivo);


		$this->objReporteFormato = new RReporteFacturaA4($this->objParam);
		$this->objReporteFormato->setDatos($this->res->datos,$this->detalle->datos,$this->casaMatriz->datos,$this->formato_reporte);
		$this->objReporteFormato->generarReporte();
		$this->objReporteFormato->output($this->objReporteFormato->url_archivo, 'F');
		//var_dump("aqui llega",'13.84.180.63/kerp/lib/lib_control/Intermediario.php?r='.$nombreArchivo);
		$this->mensajeExito = new Mensaje();
		$this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado',
				'Se generó con éxito el reporte: ' .$nombreArchivo, 'control');
		$this->mensajeExito->setArchivoGenerado('http://23.96.44.28:8099/kerp/lib/lib_control/Intermediario.php?r='.$nombreArchivo);
		$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

	}
	/***********************************************************************************************/
	function anularFactura(){
		$this->objFunc=$this->create('MODFacturacionExterna');
		$this->res=$this->objFunc->anularFactura($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

}

?>
