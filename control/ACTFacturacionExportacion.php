<?php
/**
*@package pXP
*@file gen-ACTVenta.php
*@author  (ivaldivia)
*@date 21-04-2021 09:33:10
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
include(dirname(__FILE__).'/../reportes/RFacturaExportacionA4.php');

class ACTFacturacionExportacion extends ACTbase{

	function listarFacturaExportacion(){
		$this->objParam->defecto('ordenacion','id_venta');
		$this->objParam->defecto('dir_ordenacion','asc');

		//var_dump("LLEGA AQUI",$this->objParam->getParametro('tipo_factura'));exit;
		if ($this->objParam->getParametro('id_punto_venta') != '') {
				 if ($this->objParam->getParametro('tipo_factura') == 'computarizada') {
						$this->objParam->addFiltro(" fact.id_punto_venta = ". $this->objParam->getParametro('id_punto_venta')." and (fact.tipo_factura =''".$this->objParam->getParametro('tipo_factura')."'') and fact.forma_pedido = ''factura_exportacion'' ");
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
			$this->res = $this->objReporte->generarReporteListado('MODFacturaExportacion','listarFacturaExportacion');
		} else{
			$this->objFunc=$this->create('MODFacturaExportacion');

			$this->res=$this->objFunc->listarFacturaExportacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}





	function listarDetalleFacturacionExportacion(){
		$this->objParam->defecto('ordenacion','id_venta_detalle');

		// var_dump($this->objParam->getParametro('id_venta'));exit;
		if($this->objParam->getParametro('id_venta')!=''){
			$this->objParam->addFiltro("factdet.id_venta = ".$this->objParam->getParametro('id_venta'));
		}

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODFacturaExportacion','listarDetalleFacturacionExportacion');
		} else{
			$this->objFunc=$this->create('MODFacturaExportacion');

			            if ($this->objParam->getParametro('id_venta') != '') {
			                $this->res=$this->objFunc->listarDetalleFacturacionExportacion($this->objParam);
			                $temp = Array();
			                $temp['venta_total'] = $this->res->extraData['venta_total'];
			                $temp['tipo_reg'] = 'summary';
			                //$temp['id_deposito'] = 0;

			                $this->res->total++;
			                $this->res->addLastRecDatos($temp);

			            }else{

										$this->res=$this->objFunc->listarDetalleFacturacionExportacion($this->objParam);
				}
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function imprimirFactura()
	{
			$this->objFunc = $this->create('MODFacturaExportacion');
			$this->res = $this->objFunc->datosEmpresa($this->objParam);

			$datosEmpresa = $this->res->getDatos();
			$datosEmpresa = $datosEmpresa[0];


			$this->objFunc = $this->create('MODFacturaExportacion');
			$this->cabecera = $this->objFunc->datosCabeceraFactura($this->objParam);

			$datosCabeceraFactura = $this->cabecera->getDatos();
			$datosCabeceraFactura = $datosCabeceraFactura[0];


			$this->objFunc = $this->create('MODFacturaExportacion');
			$this->detalle = $this->objFunc->datosDetalleFactura($this->objParam);
			$datosDetalleFactura = $this->detalle->getDatos();


			$this->objFunc = $this->create('MODFacturaExportacion');
			$this->totales = $this->objFunc->totalesFactura($this->objParam);
			$totalesFactura = $this->totales->getDatos();
			$totalesFactura = $totalesFactura[0];

			$titulo = 'FACTURA EXPORTACION';

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


				$this->objReporteFormato = new RFacturaExportacionA4($this->objParam);

				$this->objReporteFormato->setDatos($datosEmpresa,$datosCabeceraFactura,$datosDetalleFactura,$totalesFactura);
				$this->objReporteFormato->generarReporte();
				$this->objReporteFormato->output($this->objReporteFormato->url_archivo, 'F');

				$this->mensajeExito = new Mensaje();
				$this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado',
						'Se generó con éxito el reporte: ' .$nombreArchivo, 'control');
				$this->mensajeExito->setArchivoGenerado($nombreArchivo);
				$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

	}


	function insertarCabeceraExportacion(){
		$this->objFunc=$this->create('MODFacturaExportacion');
		if($this->objParam->insertar('id_venta')){
			$this->res=$this->objFunc->insertarCabeceraExportacion($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarCabeceraExportacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}


	function insertarVentaFacturacionExportacionDetalle(){
		$this->objFunc=$this->create('MODFacturaExportacion');
		if($this->objParam->insertar('id_venta_detalle')){
			$this->res=$this->objFunc->insertarVentaFacturacionExportacionDetalle($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarVentaFacturacionExportacionDetalle($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}


	function insertarFormasPago(){
		$this->objFunc=$this->create('MODFacturaExportacion');
		if($this->objParam->insertar('id_venta_forma_pago')){
			$this->res=$this->objFunc->insertarVentaFacturacionExportacionFormasPago($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarVentaFacturacionExportacionFormasPago($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function getTipoCambio(){
		 $this->objFunc=$this->create('MODFacturaExportacion');
		 $this->res=$this->objFunc->getTipoCambio($this->objParam);
		 $this->res->imprimirRespuesta($this->res->generarJson());
 }


 function getTipoCambioConcepto(){
		$this->objFunc=$this->create('MODFacturaExportacion');
		$this->res=$this->objFunc->getTipoCambioConcepto($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
}




 function insertarVentaExportacion(){
 		$this->objFunc=$this->create('MODFacturaExportacion');
 		$this->res=$this->objFunc->insertarVentaExportacion($this->objParam);
 		$this->res->imprimirRespuesta($this->res->generarJson());
 }

 function EmitirFacturaExportacion(){
	 $this->objFunc=$this->create('MODFacturaExportacion');
	 $this->res=$this->objFunc->EmitirFacturaExportacion($this->objParam);
	 $this->res->imprimirRespuesta($this->res->generarJson());
 }


 function insertarFormula(){
	 $this->objFunc=$this->create('MODFacturaExportacion');
	 $this->res=$this->objFunc->insertarFormula($this->objParam);
	 $this->res->imprimirRespuesta($this->res->generarJson());
 }

 function RecuperarCliente(){
		 $this->objFunc=$this->create('MODFacturaExportacion');
		 $this->res=$this->objFunc->RecuperarCliente($this->objParam);
	   $this->res->imprimirRespuesta($this->res->generarJson());
 }

 function listarFormasPagoExportacion(){
 	if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
 		$this->objReporte = new Reporte($this->objParam,$this);
 		$this->res = $this->objReporte->generarReporteListado('MODFacturaExportacion','listarFormasPagoExportacion');
 	} else{
 		$this->objFunc=$this->create('MODFacturaExportacion');

 		$this->res=$this->objFunc->listarFormasPagoExportacion($this->objParam);
 	}
 	$this->res->imprimirRespuesta($this->res->generarJson());
 }



}

?>
