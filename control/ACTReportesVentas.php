<?php
/**
*@package pXP
*@file gen-ACTVenta.php
*@author  (admin)
*@date 01-06-2015 05:58:00
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
require_once(dirname(__FILE__).'/../reportes/RResumenVentasBoaXLS.php');
require_once(dirname(__FILE__).'/../reportes/RReporteXProducto.php');
require_once(dirname(__FILE__).'/../reportes/RFacturacionComputarizada.php');
require_once(dirname(__FILE__).'/../reportes/RReporteFacturasConcepto.php');
require_once(dirname(__FILE__).'/../reportes/RReporteResumenFacturasConcepto.php');

class ACTReportesVentas extends ACTbase{

	function reporteResumenVentasBoa()	{

		$this->objFunc=$this->create('MODReportesVentas');

		$this->res=$this->objFunc->listarConceptosSucursal($this->objParam);

		$this->objFunc=$this->create('MODReportesVentas');
		$this->res2=$this->objFunc->listarReporteDetalle($this->objParam);


		//$this->objFunc=$this->create('MODReportesVentas');
		//$this->res3=$this->objFunc->listarReporteResumen($this->objParam);
		//obtener titulo del reporte
		$titulo = 'Resumen de Ventas';
		//Genera el nombre del archivo (aleatorio + titulo)
		$nombreArchivo=uniqid(md5(session_id()).$titulo);

		$nombreArchivo.='.xls';
		$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
		$this->objParam->addParametro('conceptos',$this->res->datos);
		$this->objParam->addParametro('datos',$this->res2->datos);
		//$this->objParam->addParametro('resumen',$this->res3->datos);

		//Instancia la clase de excel
		$this->objReporteFormato=new RResumenVentasBoaXLS($this->objParam);
		$this->objReporteFormato->imprimeDatos();
		//$this->objReporteFormato->imprimeDatosResumen();
		$this->objReporteFormato->generarReporte();

		$this->mensajeExito=new Mensaje();
		$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
										'Se generó con éxito el reporte: '.$nombreArchivo,'control');
		$this->mensajeExito->setArchivoGenerado($nombreArchivo);
		$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

	}

	/*Aumentando para Los reportes de Facturacion (Ismael Valdivia 01/12/2020)*/
	function ReporteFacturaComputarizada()	{

		if ($this->objParam->getParametro('imprimir_reporte') != NULL && $this->objParam->getParametro('imprimir_reporte') != '') {

			if ($this->objParam->getParametro('formato_reporte') == 'REPORTE DE FACTURAS') {
					$this->objFunc=$this->create('MODReportesVentas');
					$this->res=$this->objFunc->listarFacturaComputarizadaCabecera($this->objParam);

					$this->objFunc=$this->create('MODReportesVentas');
					$this->detalle=$this->objFunc->listarFacturaComputarizadaDetalle($this->objParam);

					$titulo = 'REPORTE_DE_FACTURAS_COMPUTARIZADAS_VENTAS PROPIAS';
					//Genera el nombre del archivo (aleatorio + titulo)
					$nombreArchivo=uniqid(md5(session_id()).$titulo);

					$nombreArchivo.='.xls';
					$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
					$this->objParam->addParametro('datos',$this->res->datos);
					//$this->objParam->addParametro('resumen',$this->res3->datos);
					//var_dump($this->res->getDatos());
					//Instancia la clase de excel
					$reporte=new RFacturacionComputarizada($this->objParam);
					$reporte->datosHeader($this->res->getDatos(),$this->detalle->getDatos());
	        $reporte->generarReporte();

					$this->mensajeExito=new Mensaje();
					$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
													'Se generó con éxito el reporte: '.$nombreArchivo,'control');
					$this->mensajeExito->setArchivoGenerado($nombreArchivo);
					$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
			} elseif ($this->objParam->getParametro('formato_reporte') == 'REPORTE DE FACTURAS / CONCEPTO') {

				$this->objFunc=$this->create('MODReportesVentas');
				$this->res=$this->objFunc->listarFacturaComputarizadaCabecera($this->objParam);

				$this->objFunc=$this->create('MODReportesVentas');
				$this->detalle=$this->objFunc->listarFacturaConcepto($this->objParam);

				$titulo = 'REPORTE DE FACTURAS COMPUTARIZADAS POR CONCEPTO';
				//Genera el nombre del archivo (aleatorio + titulo)
				$nombreArchivo=uniqid(md5(session_id()).$titulo);

				$nombreArchivo.='.xls';
				$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
				$this->objParam->addParametro('datos',$this->res->datos);
				//Instancia la clase de excel
				$reporte=new RReporteFacturasConcepto($this->objParam);
				$reporte->datosHeader($this->res->getDatos(),$this->detalle->getDatos());
				$reporte->generarReporte();

				$this->mensajeExito=new Mensaje();
				$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
												'Se generó con éxito el reporte: '.$nombreArchivo,'control');
				$this->mensajeExito->setArchivoGenerado($nombreArchivo);
				$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

			} elseif ($this->objParam->getParametro('formato_reporte') == 'RESUMEN DE FACTURAS / CONCEPTO') {

				$this->objFunc=$this->create('MODReportesVentas');
				$this->res=$this->objFunc->listarFacturaComputarizadaCabecera($this->objParam);

				$this->objFunc=$this->create('MODReportesVentas');
				$this->detalle=$this->objFunc->listarResumenFacturaComputarizada($this->objParam);

				$titulo = 'RESUMEN DE FACTURAS COMPUTARIZADAS POR CONCEPTO';
				//Genera el nombre del archivo (aleatorio + titulo)
				$nombreArchivo=uniqid(md5(session_id()).$titulo);

				$nombreArchivo.='.xls';
				$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
				$this->objParam->addParametro('datos',$this->res->datos);
				//Instancia la clase de excel
				$reporte=new RReporteResumenFacturasConcepto($this->objParam);
				$reporte->datosHeader($this->res->getDatos(),$this->detalle->getDatos());
				$reporte->generarReporte();

				$this->mensajeExito=new Mensaje();
				$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
												'Se generó con éxito el reporte: '.$nombreArchivo,'control');
				$this->mensajeExito->setArchivoGenerado($nombreArchivo);
				$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

			}



		} else {
			$this->objFunc = $this->create('MODReportesVentas');
			$this->res = $this->objFunc->listarFacturaComputarizadaDetalle($this->objParam);
			// $temp = Array();
			// $temp['total_monto_facturas'] = $this->res->extraData['total_monto_facturas'];
			// $temp['total_excentos'] = $this->res->extraData['total_excentos'];
			// $temp['total_comision'] = $this->res->extraData['total_comision'];
			// $temp['total_precio_unitario'] = $this->res->extraData['total_precio_unitario'];
			// $temp['total_forma_pago'] = $this->res->extraData['total_forma_pago'];
			// $temp['tipo_reg'] = 'summary';
			//
			// $this->res->total++;
			// $this->res->addLastRecDatos($temp);
			$this->res->imprimirRespuesta($this->res->generarJson());
		}

		// $this->objFunc=$this->create('MODReportesVentas');
		//
		// $this->res=$this->objFunc->listarConceptosSucursal($this->objParam);
		//
		// $this->objFunc=$this->create('MODReportesVentas');
		// $this->res2=$this->objFunc->listarReporteDetalle($this->objParam);
		//
		//
		// //$this->objFunc=$this->create('MODReportesVentas');
		// //$this->res3=$this->objFunc->listarReporteResumen($this->objParam);
		// //obtener titulo del reporte
		// $titulo = 'Resumen de Ventas';
		// //Genera el nombre del archivo (aleatorio + titulo)
		// $nombreArchivo=uniqid(md5(session_id()).$titulo);
		//
		// $nombreArchivo.='.xls';
		// $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
		// $this->objParam->addParametro('conceptos',$this->res->datos);
		// $this->objParam->addParametro('datos',$this->res2->datos);
		// //$this->objParam->addParametro('resumen',$this->res3->datos);
		//
		// //Instancia la clase de excel
		// $this->objReporteFormato=new RResumenVentasBoaXLS($this->objParam);
		// $this->objReporteFormato->imprimeDatos();
		// //$this->objReporteFormato->imprimeDatosResumen();
		// $this->objReporteFormato->generarReporte();
		//
		// $this->mensajeExito=new Mensaje();
		// $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
		// 								'Se generó con éxito el reporte: '.$nombreArchivo,'control');
		// $this->mensajeExito->setArchivoGenerado($nombreArchivo);
		// $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

	}
 /**********************************************/



	function reporteXProducto()	{

		$this->objFunc=$this->create('MODReportesVentas');

		$this->res=$this->objFunc->listarReporteXProducto($this->objParam);



		//obtener titulo del reporte
		$titulo = 'Ventas por Producto';
		//Genera el nombre del archivo (aleatorio + titulo)
		$nombreArchivo=uniqid(md5(session_id()).$titulo);

		$nombreArchivo.='.xls';
		$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
		$this->objParam->addParametro('datos',$this->res->datos);


		//Instancia la clase de excel
		$this->objReporteFormato=new RReporteXProducto($this->objParam);
		$this->objReporteFormato->imprimeDatos();
		$this->objReporteFormato->generarReporte();

		$this->mensajeExito=new Mensaje();
		$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
										'Se generó con éxito el reporte: '.$nombreArchivo,'control');
		$this->mensajeExito->setArchivoGenerado($nombreArchivo);
		$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

	}



}

?>
