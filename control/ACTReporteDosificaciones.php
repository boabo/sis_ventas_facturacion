<?php
/**
*@package pXP
*@file gen-ACTReporteDosificaciones.php
*@author  (Ismael Valdivia)
*@date 12-03-2021 09:20:00
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
require_once(dirname(__FILE__).'/../reportes/RReporteDosificacionesXLS.php');

class ACTReporteDosificaciones extends ACTbase{

    function listarReporteDepositos()
    {
        $this->objParam->defecto('ordenacion', 'id_int_transaccion');
        $this->objParam->defecto('dir_ordenacion', 'asc');

        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODIntTransaccion', 'listarReporteDepositos');
        } else {
            $this->objFunc = $this->create('MODReporteDosificaciones');

            $this->res = $this->objFunc->listarReporteDosificaciones($this->objParam);
        }
        //adicionar una fila al resultado con el summario
        // $temp = Array();
        // $temp['total_importe_ml'] = $this->res->extraData['total_importe_ml'];
        // $temp['total_importe_me'] = $this->res->extraData['total_importe_me'];
        // $temp['total_deposito_ml'] = $this->res->extraData['total_deposito_ml'];
        // $temp['total_deposito_me'] = $this->res->extraData['total_deposito_me'];
        // $temp['totales_venta_ml'] = $this->res->extraData['totales_venta_ml'];
        // $temp['totales_venta_me'] = $this->res->extraData['totales_venta_me'];
        // $temp['total_diferencia'] = $this->res->extraData['total_diferencia'];
        // $temp['tipo_reg'] = 'summary';
        //
        // $this->res->total++;
        //
        // $this->res->addLastRecDatos($temp);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


		function reporteDosificacionesExcel()	{

		  $this->objFunc=$this->create('MODReporteDosificaciones');
			$this->res=$this->objFunc->listarReporteDosificaciones($this->objParam);

			//obtener titulo del reporte
			$titulo = 'Reporte Dosificaciones';
			//Genera el nombre del archivo (aleatorio + titulo)
			$nombreArchivo=uniqid(md5(session_id()).$titulo);

			$nombreArchivo.='.xls';
			$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
			$this->objParam->addParametro('datos',$this->res->datos);


			//Instancia la clase de excel
			$this->objReporteFormato=new RReporteDosificacionesXLS($this->objParam);
			$this->objReporteFormato->datosHeader($this->res);
			$this->objReporteFormato->generarReporte();

			$this->mensajeExito=new Mensaje();
			$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
											'Se generó con éxito el reporte: '.$nombreArchivo,'control');
			$this->mensajeExito->setArchivoGenerado($nombreArchivo);
			$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

		}

}

?>
