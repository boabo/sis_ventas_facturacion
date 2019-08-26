<?php
/**
*@package pXP
*@file gen-ACTAperturaCierreCajaAsociada.php
*@author  (ivaldivia)
*@date 15-08-2019 13:15:22
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTAperturaCierreCajaAsociada extends ACTbase{

	function listarAperturaCierreCajaAsociada(){
		$this->objParam->defecto('ordenacion','id_apertura_asociada');
		$this->objParam->defecto('dir_ordenacion','asc');

		if ($this->objParam->getParametro('id_deposito') != '') {
				$this->objParam->addFiltro(" acca.id_deposito = " .$this->objParam->getParametro('id_deposito'));
		}


		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODAperturaCierreCajaAsociada','listarAperturaCierreCajaAsociada');
		} else{
			$this->objFunc=$this->create('MODAperturaCierreCajaAsociada');

			if ($this->objParam->getParametro('id_deposito') != '') {
					$this->res=$this->objFunc->listarAperturaCierreCajaAsociada($this->objParam);
					$temp = Array();
					$temp['venta_total_ml'] = $this->res->extraData['venta_total_ml'];
					$temp['venta_total_me'] = $this->res->extraData['venta_total_me'];
					$temp['tipo_reg'] = 'summary';
					//$temp['id_deposito'] = 0;

					$this->res->total++;
					$this->res->addLastRecDatos($temp);

			}else{

				$this->res=$this->objFunc->listarAperturaCierreCajaAsociada($this->objParam);

				}

		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarAperturaCierreCajaAsociada(){
		$this->objFunc=$this->create('MODAperturaCierreCajaAsociada');
		if($this->objParam->insertar('id_apertura_asociada')){
			$this->res=$this->objFunc->insertarAperturaCierreCajaAsociada($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarAperturaCierreCajaAsociada($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarAperturaCierreCajaAsociada(){
			$this->objFunc=$this->create('MODAperturaCierreCajaAsociada');
		$this->res=$this->objFunc->eliminarAperturaCierreCajaAsociada($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function getSumaTotal(){
		 $this->objFunc=$this->create('MODAperturaCierreCajaAsociada');
		 $this->res=$this->objFunc->getSumaTotal($this->objParam);
		 $this->res->imprimirRespuesta($this->res->generarJson());
 }
 function getDatosSucursal(){
	 $this->objFunc=$this->create('MODAperturaCierreCajaAsociada');
	 $this->res=$this->objFunc->getDatosSucursal($this->objParam);
	 $this->res->imprimirRespuesta($this->res->generarJson());
 }


}

?>
