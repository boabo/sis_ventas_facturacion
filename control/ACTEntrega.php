<?php
/**
*@package pXP
*@file gen-ACTEntrega.php
*@author  (admin)
*@date 12-09-2017 15:04:26
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
require_once(dirname(__FILE__).'/../reportes/REntregaEfectivo.php');
class ACTEntrega extends ACTbase{

	function listarEntrega(){
		$this->objParam->defecto('ordenacion','id_entrega_brinks');
		$this->objParam->defecto('dir_ordenacion','asc');
        if ($this->objParam->getParametro('id_punto_venta') != '') {

            $this->objParam->addFiltro(" eng.id_punto_venta= ". $this->objParam->getParametro('id_punto_venta'));
        }
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODEntrega','listarEntrega');
		} else{
			$this->objFunc=$this->create('MODEntrega');

			$this->res=$this->objFunc->listarEntrega($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarEntrega(){
		$this->objFunc=$this->create('MODEntrega');
		if($this->objParam->insertar('id_entrega_brinks')){
			$this->res=$this->objFunc->insertarEntrega($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarEntrega($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarEntrega(){
        $this->objFunc=$this->create('MODEntrega');
		$this->res=$this->objFunc->eliminarEntrega($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

    function listarfechaApertura(){
        $this->objParam->defecto('ordenacion','id_punto_venta');
        $this->objParam->defecto('dir_ordenacion','asc');				
        if($this->objParam->getParametro('id_punto_venta_2') != '') {
            $this->objParam->addFiltro(" ap.id_punto_venta = ".$this->objParam->getParametro('id_punto_venta'));
        }
	    $this->objFunc=$this->create('MODEntrega');
        $this->res=$this->objFunc->fechaApertura($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function reporteEntregaBs(){

        $this->objFunc=$this->create('MODEntrega');
        $dataSource=$this->objFunc->listarEntregaBs();
        $this->dataSource=$dataSource->getDatos();

        $this->objFunc=$this->create('MODEntrega');
        $dataSource2=$this->objFunc->listarEntregaDolares();
        $this->dataSource2=$dataSource2->getDatos();

        //var_dump($this->dataSource2);exit;
        $nombreArchivo = uniqid(md5(session_id()).'[Reporte-DocContratacionExt]').'.pdf';
        $this->objParam->addParametro('orientacion','P');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $this->objReporte = new REntregaEfectivo($this->objParam);
        $this->objReporte->setDatos($this->dataSource,$this->dataSource2);
        $this->objReporte->generarReporte();
        $this->objReporte->output($this->objReporte->url_archivo,'F');

        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());


    }
    function getPuntoVen(){
        $this->objFunc=$this->create('MODEntrega');
        $this->res=$this->objFunc->getPuntoVenta($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function getTipoUsuario(){
        $this->objFunc=$this->create('MODEntrega');
        $this->res=$this->objFunc->getTipoUsuario($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>
