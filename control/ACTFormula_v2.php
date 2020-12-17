<?php
/**
*@package pXP
*@file gen-ACTFormula.php
*@author  (ivaldivia)
*@date 17-09-2019 15:28:13
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTFormula_v2 extends ACTbase{

	function listarFormula(){
		$this->objParam->defecto('ordenacion','id_formula');

		$this->objParam->defecto('dir_ordenacion','asc');

		// /**************************************Filtramos por PV y Tipo PV(ATO CTO)**************************************/
		// if($this->objParam->getParametro('tipo_punto_venta') != '') {
		// 	$this->objParam->addFiltro("''".$this->objParam->getParametro('tipo_punto_venta')."''::varchar in (SELECT UNNEST(REGEXP_SPLIT_TO_ARRAY(array_to_string(form.tipo_punto_venta,'','')::varchar, '','')))");
		// }
		//
		// if($this->objParam->getParametro('id_punto_venta') != '') {
		// 	$this->objParam->addFiltro("''".$this->objParam->getParametro('id_punto_venta')."''::varchar in (SELECT UNNEST(REGEXP_SPLIT_TO_ARRAY(array_to_string(form.punto_venta_asociado,'','')::varchar, '','')))");
		// }
		// /***************************************************************************************************************/


		/*Aumentando para filtrar solo los conceptos que seran para Recibos Oficiales (Ismael Valdivia 14/07/2020)*/
		if($this->objParam->getParametro('emision')!=''){
				$this->objParam->addFiltro("''".$this->objParam->getParametro('emision')."''=ANY (form.sw_autorizacion) AND ''".$this->objParam->getParametro('regional')."''=ANY (form.regionales) AND''".$this->objParam->getParametro('tipo_pv')."''=ANY (form.nivel_permiso)");
		}

		// if($this->objParam->getParametro('regional')!=''){
		// 		$this->objParam->addFiltro("''".$this->objParam->getParametro('regional')."''=ANY (form.regionales)");
		// }




		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODFormula_v2','listarFormula');
		} else{
			$this->objFunc=$this->create('MODFormula_v2');

			$this->res=$this->objFunc->listarFormula($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarFormula(){
		$this->objFunc=$this->create('MODFormula_v2');
		if($this->objParam->insertar('id_formula')){
			$this->res=$this->objFunc->insertarFormula($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarFormula($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarFormula(){
			$this->objFunc=$this->create('MODFormula_v2');
		$this->res=$this->objFunc->eliminarFormula($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function editAuto(){
		$this->objFunc=$this->create('MODFormula_v2');
		$this->res=$this->objFunc->editAuto($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

}

?>
