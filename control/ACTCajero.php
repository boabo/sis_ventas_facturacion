<?php
/**
*@package pXP
*@file gen-ACTVenta.php
*@author  (ivaldivia)
*@date 29-05-2019 19:33:10
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
include(dirname(__FILE__).'/../reportes/RFactura.php');
include(dirname(__FILE__).'/../reportes/RReporteFacturaA4.php');
include(dirname(__FILE__).'/../reportes/RReporteReciboMiamiA4.php');

class ACTCajero extends ACTbase{

	function listarVenta(){
		$this->objParam->defecto('ordenacion','id_venta');
		$this->objParam->defecto('dir_ordenacion','asc');

		//var_dump("LLEGA AQUI",$this->objParam->getParametro('tipo_factura'));exit;
		if ($this->objParam->getParametro('id_punto_venta') != '') {
				if ($this->objParam->getParametro('tipo_factura') == 'todos') {
					$this->objParam->addFiltro(" fact.id_punto_venta = ". $this->objParam->getParametro('id_punto_venta')." and (fact.tipo_factura =''computarizada'' or fact.tipo_factura =''manual'' or fact.tipo_factura =''carga'')");
				} else if ($this->objParam->getParametro('tipo_factura') == 'manual') {
				 			$this->objParam->addFiltro(" fact.id_punto_venta = ". $this->objParam->getParametro('id_punto_venta')." and (fact.tipo_factura =''".$this->objParam->getParametro('tipo_factura')."'')");
				 	}

				else {
				 			$this->objParam->addFiltro(" fact.id_punto_venta = ". $this->objParam->getParametro('id_punto_venta')." and (fact.tipo_factura =''carga'' or fact.tipo_factura =''".$this->objParam->getParametro('tipo_factura')."'')");
				 	}
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

		$this->objFunc = $this->create('MODCajero');
		$this->res = $this->objFunc->listarFacturaDetalle($this->objParam);

		$datos['detalle'] = $this->res->getDatos();

		 $fecha_actual = date("d/m/Y");
		 $fecha_venta = $datos["fecha_venta"];
		 $tipo_usuario = $datos["tipo_usuario"];

		/*Aqui aumentaremos condicionales para que solo se haga la reimpresion el mismo dia (Ismael Valdivia)*/
		if ($tipo_usuario == 'administrador_facturacion') {
				$reporte = new RFactura();
				$temp = array();
				$temp['html'] = $reporte->generarHtml($this->objParam->getParametro('formato_comprobante'),$datos);
				$this->res->setDatos($temp);
				$this->res->imprimirRespuesta($this->res->generarJson());
		} else {
			if ($fecha_venta == $fecha_actual) {
					$reporte = new RFactura();
					$temp = array();
					$temp['html'] = $reporte->generarHtml($this->objParam->getParametro('formato_comprobante'),$datos);
					$this->res->setDatos($temp);
					$this->res->imprimirRespuesta($this->res->generarJson());
			} else {
				throw new Exception('Solo se puede realizar la reimpresión el mismo día de la emisión, Favor consulte con el Administrador.');
			}
		}



		/*****************************************************************************************************/


		// var_dump("llega aqui dato",$this->res->generarJson());
	}

	function reporteFacturaCarta()
	{
			$this->objFunc = $this->create('MODCajero');
			$this->res = $this->objFunc->listarFactura($this->objParam);

			$datosVenta = $this->res->getDatos();
			$datos = $datosVenta[0];

			$this->objFunc = $this->create('MODCajero');
			$this->detalle = $this->objFunc->listarFacturaDetalle($this->objParam);

			/*Aqui recueperar la casa matriz*/
			$this->objFunc = $this->create('MODCajero');
			$this->casaMatriz = $this->objFunc->listarCasaMatriz($this->objParam);

		  if ($datosVenta[0]['tipo_factura'] == 'recibo') {
		  	$titulo = 'Recibo';
		  } else {
				$titulo = 'Factura';
			}
			//obtener titulo del reporte

			//Genera el nombre del archivo (aleatorio + titulo)
			$nombreArchivo = uniqid(md5(session_id()) . $titulo);
			$nombreArchivo .= '.pdf';

			$this->objParam->addParametro('orientacion', 'P');
			$this->objParam->addParametro('tamano', 'LETTER');
			$this->objParam->addParametro('nombre_archivo', $nombreArchivo);

			$fecha_actual = date("d/m/Y");
 		  $fecha_venta = $datos["fecha_venta"];
			$tipo_usuario = $datos["tipo_usuario"];

			if ($tipo_usuario == 'administrador_facturacion') {
				if ($datosVenta[0]['tipo_factura'] == 'recibo' && $datosVenta[0]['moneda_base'] == 'USD') {
					$this->objReporteFormato = new RReporteReciboMiamiA4($this->objParam);
				} else {
					$this->objReporteFormato = new RReporteFacturaA4($this->objParam);
				}
				$this->objReporteFormato->setDatos($this->res->datos,$this->detalle->datos,$this->casaMatriz->datos);
				$this->objReporteFormato->generarReporte();
				$this->objReporteFormato->output($this->objReporteFormato->url_archivo, 'F');

				$this->mensajeExito = new Mensaje();
				$this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado',
						'Se generó con éxito el reporte: ' .$nombreArchivo, 'control');
				$this->mensajeExito->setArchivoGenerado($nombreArchivo);
				$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
			} else {
				if ($fecha_venta == $fecha_actual) {
					if ($datosVenta[0]['tipo_factura'] == 'recibo' && $datosVenta[0]['moneda_base'] == 'USD') {
						$this->objReporteFormato = new RReporteReciboMiamiA4($this->objParam);
					} else {
						$this->objReporteFormato = new RReporteFacturaA4($this->objParam);
					}
					$this->objReporteFormato->setDatos($this->res->datos,$this->detalle->datos,$this->casaMatriz->datos);
					$this->objReporteFormato->generarReporte();
					$this->objReporteFormato->output($this->objReporteFormato->url_archivo, 'F');

					$this->mensajeExito = new Mensaje();
					$this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado',
							'Se generó con éxito el reporte: ' .$nombreArchivo, 'control');
					$this->mensajeExito->setArchivoGenerado($nombreArchivo);
					$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
				} else {
					throw new Exception('Solo se puede realizar la reimpresión el mismo día de la emisión, Favor consulte con el Administrador.');
				}
			}

	}

	function getTipoUsuario(){
		 $this->objFunc=$this->create('MODCajero');
		 $this->res=$this->objFunc->getTipoUsuario($this->objParam);
		 $this->res->imprimirRespuesta($this->res->generarJson());
 }
 function listarInstanciaPago(){
		$this->objFunc=$this->create('MODCajero');
		$this->res=$this->objFunc->listarInstanciaPago($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
}
function getConceptoAsociar(){
	 $this->objFunc=$this->create('MODCajero');
	 $this->res=$this->objFunc->getConceptoAsociar($this->objParam);
	 $this->res->imprimirRespuesta($this->res->generarJson());
}


}

?>
