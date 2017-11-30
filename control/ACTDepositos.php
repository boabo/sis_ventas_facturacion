<?php
/**
*@package pXP
*@file gen-ACTDepositos.php
*@author  (miguel.mamani)
*@date 11-09-2017 15:32:32
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTDepositos extends ACTbase{    
			
	function listarDepositos(){
		$this->objParam->defecto('ordenacion','id_apertura_cierre_caja');

        if ($this->objParam->getParametro('id_punto_venta') != '') {

            $this->objParam->addFiltro(" cdo.id_punto_venta= ". $this->objParam->getParametro('id_punto_venta'));
        }

        if ($this->objParam->getParametro('pes_estado') == 'pendiente') {

            $this->objParam->addFiltro("cdo.diferencia_bs <>  0 and  cdo.diferencia_usd <> 0
                                        OR
                                        cdo.diferencia_bs <> 0   and  cdo.diferencia_usd  = 0
                                        OR
                                        cdo.diferencia_bs =  0  and  cdo.diferencia_usd <> 0 ");
        }else{
            $this->objParam->addFiltro("cdo.diferencia_bs =  0  and  cdo.diferencia_usd = 0");
        }
		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODDepositos','listarDepositos');
		} else{
			$this->objFunc=$this->create('MODDepositos');
			
			$this->res=$this->objFunc->listarDepositos($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarDepositos(){
		$this->objFunc=$this->create('MODDepositos');	
		if($this->objParam->insertar('id_apertura_cierre_caja')){
			$this->res=$this->objFunc->insertarDepositos($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarDepositos($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarDepositos(){
			$this->objFunc=$this->create('MODDepositos');	
		$this->res=$this->objFunc->eliminarDepositos($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}



}

?>