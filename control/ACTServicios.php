<?php
/**
*@package pXP
*@file gen-ACTServicios.php
*@author  (ivaldivia)
*@date 10-09-2019 16:17:39
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTServicios extends ACTbase{

	function listarServicios(){
		$this->objParam->defecto('ordenacion','id_concepto_ingas');

		$this->objParam->defecto('dir_ordenacion','asc');

		/*Listamos los conceptos de acuerdo al tipo seleccionado para los paquetes (Ismael Valdivia)*/
		if($this->objParam->getParametro('tipo_serv') != '') {
			$this->objParam->addFiltro("(ingas.movimiento = ''ingreso'' or ingas.movimiento = ''recurso'' ) and
																	ingas.tipo = ''".$this->objParam->getParametro('tipo_serv')."''");
		}
		/********************************************************************************************/


		/*Filtro para que solo liste los servicios de movimiento ingreso o recurso*/
		if($this->objParam->getParametro('movimiento') != '') {
			$this->objParam->addFiltro("(ingas.movimiento = ''ingreso'' or ingas.movimiento = ''recurso'' )
																	and (ingas.tipo = ''Servicio'' or ingas.tipo = ''Producto'')
																	and ingas.estado_reg = ''activo''");
		}
		/**************************************************************************/
		/**************************************Filtramos por PV y Tipo PV(ATO CTO)**************************************/
		if($this->objParam->getParametro('tipo_pv') != '') {
			$this->objParam->addFiltro("''".$this->objParam->getParametro('tipo_pv')."''::varchar in (SELECT UNNEST(REGEXP_SPLIT_TO_ARRAY(array_to_string(ingas.tipo_punto_venta,'','')::varchar, '','')))");
		}

		if($this->objParam->getParametro('id_punto_venta_producto') != '') {
			$this->objParam->addFiltro("''".$this->objParam->getParametro('id_punto_venta_producto')."''::varchar in (SELECT UNNEST(REGEXP_SPLIT_TO_ARRAY(array_to_string(ingas.punto_venta_asociado,'','')::varchar, '','')))");
		}
		/***************************************************************************************************************/


		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODServicios','listarServicios');
		} else{
			$this->objFunc=$this->create('MODServicios');

			$this->res=$this->objFunc->listarServicios($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function listarServiciosPaquetes(){
		$this->objParam->defecto('ordenacion','id_concepto_ingas');
		$this->objParam->defecto('dir_ordenacion','asc');

		/*Listamos los conceptos de acuerdo al tipo seleccionado para los paquetes (Ismael Valdivia)*/
		if($this->objParam->getParametro('servicios_productos') == 'SI') {
			if ($this->objParam->getParametro('tipo_serv') != '') {
					$this->objParam->addFiltro("ser.tipo = ''".$this->objParam->getParametro('tipo_serv')."''");
			}		
		}
		/***************************************************************************************************************/


		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODServicios','listarServiciosPaquetes');
		} else{
			$this->objFunc=$this->create('MODServicios');

			$this->res=$this->objFunc->listarServiciosPaquetes($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarServicios(){
		$this->objFunc=$this->create('MODServicios');
		if($this->objParam->insertar('id_concepto_ingas')){
			$this->res=$this->objFunc->insertarServicios($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarServicios($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarServicios(){
			$this->objFunc=$this->create('MODServicios');
		$this->res=$this->objFunc->eliminarServicios($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

}

?>