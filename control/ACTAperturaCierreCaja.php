<?php
/**
 *@package pXP
 *@file gen-ACTAperturaCierreCaja.php
 *@author  (jrivera)
 *@date 07-07-2016 14:16:20
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

require_once(dirname(__FILE__).'/../reportes/RAperturaCierrePDF.php');
class ACTAperturaCierreCaja extends ACTbase{

    function listarAperturaCierreCaja(){
        $this->objParam->defecto('ordenacion','id_apertura_cierre_caja');

        $this->objParam->defecto('dir_ordenacion','asc');

        if ($this->objParam->getParametro('pes_estado') != '') {
            $this->objParam->addFiltro(" apcie.estado = ''" .  $this->objParam->getParametro('pes_estado') . "''");
        }
        /* if($this->objParam->getParametro('id_entrega_brinks') != '') {
             $this->objParam->addFiltro(" apcie.id_entrega_brinks = " . $this->objParam->getParametro('id_entrega_brinks'));
         }*/
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODAperturaCierreCaja','listarAperturaCierreCaja');
        } else{
            $this->objFunc=$this->create('MODAperturaCierreCaja');

            $this->res=$this->objFunc->listarAperturaCierreCaja($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarCierreCaja(){
        /*$this->objParam->defecto('ordenacion','id_apertura_cierre_caja');
        $this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('id_punto_venta') != '') {
            $this->objParam->addFiltro(" apcie.id_punto_venta = " . $this->objParam->getParametro('id_punto_venta'));
        }*/

        $this->objFunc=$this->create('MODAperturaCierreCaja');
        $this->res=$this->objFunc->reporteAperturaCierreCaja($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarAperturaCierreCaja(){
        $this->objFunc=$this->create('MODAperturaCierreCaja');
        if($this->objParam->insertar('id_apertura_cierre_caja')){
            $this->res=$this->objFunc->insertarAperturaCierreCaja($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarAperturaCierreCaja($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function abrirAperturaCierreCaja(){
        $this->objFunc=$this->create('MODAperturaCierreCaja');
        $this->res=$this->objFunc->abrirAperturaCierreCaja($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarAperturaCierreCaja(){
        $this->objFunc=$this->create('MODAperturaCierreCaja');
        $this->res=$this->objFunc->eliminarAperturaCierreCaja($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function reporteAperturaCierreCaja()	{

        $this->objFunc=$this->create('MODAperturaCierreCaja');
        $this->res=$this->objFunc->reporteApertura($this->objParam);


        //obtener titulo del reporte
        $titulo = 'AperturaCierreCaja';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo=uniqid(md5(session_id()).$titulo);


        $nombreArchivo.='.pdf';
        $this->objParam->addParametro('orientacion','P');
        $this->objParam->addParametro('tamano','LETTER	');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        //Instancia la clase de pdf
        $this->objReporteFormato=new RAperturaCierrePDF($this->objParam);
        $this->objReporteFormato->setDatos($this->res->datos);
        $this->objReporteFormato->generarReporte();
        $this->objReporteFormato->output($this->objReporteFormato->url_archivo,'F');


        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }
    function insertarFecha(){
        $this->objFunc=$this->create('MODAperturaCierreCaja');
        $this->res=$this->objFunc->insertarFecha($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function eleminarFecha(){
        $this->objFunc=$this->create('MODAperturaCierreCaja');
        $this->res=$this->objFunc->eleminarFecha($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarAperturaCierreCajaEntrega(){
        $this->objParam->defecto('ordenacion','id_apertura_cierre_caja');

        $this->objParam->defecto('dir_ordenacion','asc');
        if($this->objParam->getParametro('id_entrega_brinks') != '') {
            $this->objParam->addFiltro(" apcie.id_entrega_brinks = " . $this->objParam->getParametro('id_entrega_brinks')." and apcie.id_punto_venta = ". $this->objParam->getParametro('id_punto_venta'));
        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODAperturaCierreCaja','listarAperturaCierreCajaEntrega');
        } else{
            $this->objFunc=$this->create('MODAperturaCierreCaja');
            $this->res=$this->objFunc->listarAperturaCierreCajaEntrega($this->objParam);
        }
        $temp = Array();
        $temp['nombre_punto_venta'] = 'TOTAL';
        $temp['arqueo_moneda_local'] = $this->res->extraData['arqueo_moneda_local_total'];
        $temp['arqueo_moneda_extranjera'] = $this->res->extraData['arqueo_moneda_extranjera_total'];
        $temp['tipo_reg'] = 'summary';
        $temp['id_apertura_cierre_caja'] = 0;
        $this->res->total++;
        $this->res->addLastRecDatos($temp);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function EstadoApertura(){
        // $this->objParam->defecto('ordenacion','id_apertura_cierre_caja');
        //$this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODAperturaCierreCaja','EstadoApertura');
        } else{
            $this->objFunc=$this->create('MODAperturaCierreCaja');

            $this->res=$this->objFunc->EstadoApertura($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function DetalleEstadoApertura(){
        $this->objParam->defecto('ordenacion','id_apertura_cierre_caja');
        $this->objParam->defecto('dir_ordenacion','asc');

        if ($this->objParam->getParametro('pes_estado') != ''  ) {
            $this->objParam->addFiltro(" a.fecha_apertura_cierre = " . $this->objParam->getParametro('fecha_apertura_cierre')."::date and a.estado = ''".  $this->objParam->getParametro('pes_estado') . "''");
        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODAperturaCierreCaja','DetalleEstadoApertura');
        } else{
            $this->objFunc=$this->create('MODAperturaCierreCaja');

            $this->res=$this->objFunc->DetalleEstadoApertura($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function listarAperturaCierreCajaVentas(){

        $this->objFunc=$this->create('MODAperturaCierreCaja');
        $this->res=$this->objFunc->listarAperturaCierreCajaVentas($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


}

?>
