<?php
/**
*@package pXP
*@file gen-ACTReporteEmisionPasajes.php
*@author  (Ismael Valdivia)
*@date 01-06-2015 05:58:00
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
require_once(dirname(__FILE__).'/../reportes/RReporteEmisionBoletosXLS.php');

class ACTReporteEmisionPasajes extends ACTbase{

    function listarReporteEmisionBoletos()
    {
        $this->objParam->defecto('ordenacion', 'id_int_transaccion');
        $this->objParam->defecto('dir_ordenacion', 'asc');


        /* if ($this->objParam->getParametro('id_auxiliar') != '') {
            $this->objParam->addFiltro("per.id_gestion = " . $this->objParam->getParametro('id_gestion'));
        }
 */

        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODIntTransaccion', 'listarDetalleEmisionBoletos');
        } else {
            $this->objFunc = $this->create('MODReporteEmisionBoletos');

            $this->res = $this->objFunc->listarDetalleEmisionBoletos($this->objParam);
        }
        //adicionar una fila al resultado con el summario
        $temp = Array();
        $temp['total_debe'] = $this->res->extraData['total_debe'];
        $temp['total_haber'] = $this->res->extraData['total_haber'];
        $temp['tipo_reg'] = 'summary';

        $this->res->total++;

        $this->res->addLastRecDatos($temp);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


		function reporteEmisionPasajes()	{

		  $this->objFunc=$this->create('MODReporteEmisionBoletos');
			$this->res=$this->objFunc->listarDetalleEmisionBoletos($this->objParam);

			//obtener titulo del reporte
			$titulo = 'Reporte Auxiliares';
			//Genera el nombre del archivo (aleatorio + titulo)
			$nombreArchivo=uniqid(md5(session_id()).$titulo);

			$nombreArchivo.='.xls';
			$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
			$this->objParam->addParametro('datos',$this->res->datos);


			//Instancia la clase de excel
			$this->objReporteFormato=new RReporteEmisionBoletosXLS($this->objParam);
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
