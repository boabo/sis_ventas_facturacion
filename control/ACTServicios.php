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

		/*Aumentando para filtrar solo los conceptos que seran para Recibos Oficiales (Ismael Valdivia 14/07/2020)*/
		if($this->objParam->getParametro('emision')!=''){
			if ($this->objParam->getParametro('emision') == 'facturacion') {
				$this->objParam->addFiltro("''".$this->objParam->getParametro('facturacion')."''=ANY (ingas.sw_autorizacion) AND ''".$this->objParam->getParametro('regionales')."''=ANY (ingas.regionales) AND ''".$this->objParam->getParametro('tipo_pv')."''=ANY (ingas.nivel_permiso)");
			} elseif ($this->objParam->getParametro('emision') == 'recibo') {
				$this->objParam->addFiltro("''".$this->objParam->getParametro('facturacion')."''=ANY (ingas.sw_autorizacion) AND ''".$this->objParam->getParametro('regionales')."''=ANY (ingas.regionales) AND ''".$this->objParam->getParametro('tipo_pv')."''=ANY (ingas.nivel_permiso)");
				if ($this->objParam->getParametro('anticipo_ro_grupo')!=''){
						$this->objParam->addFiltro(" ingas.codigo in (''ROPC'',''ROAC'')");
				}
			}
			elseif ($this->objParam->getParametro('emision') == 'DEVOLUCIONES') {
				$this->objParam->addFiltro("''".$this->objParam->getParametro('facturacion')."''=ANY (ingas.sw_autorizacion)");
			}elseif ($this->objParam->getParametro('emision') == 'notas_de_cobro'){
				$this->objParam->addFiltro("''".$this->objParam->getParametro('facturacion')."''=ANY (ingas.nivel_permiso)");
			}
		}

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

		if($this->objParam->getParametro('Facturacion') != '') {
			if ($this->objParam->getParametro('Facturacion') == 'conceptos_facturacion') {
				$this->objParam->addFiltro("(''RO''=ANY (ingas.sw_autorizacion) OR ''FACTCOMP''=ANY (ingas.sw_autorizacion) OR ''dev''=ANY (ingas.sw_autorizacion))");
			}
		}

		if($this->objParam->getParametro('ListaConceptos') != '') {
				$this->objParam->addFiltro("ingas.listado = ''".$this->objParam->getParametro('ListaConceptos')."''");
		}

		if($this->objParam->getParametro('listaConceptoEmision') != '') {
			$this->objParam->addFiltro("''".$this->objParam->getParametro('listaConceptoEmision')."''=ANY (ingas.sw_autorizacion) AND ''".$this->objParam->getParametro('regionales')."''=ANY (ingas.regionales) AND ''".$this->objParam->getParametro('tipo_pv')."''=ANY (ingas.nivel_permiso)");
		}



		if($this->objParam->getParametro('id_actividad_economica')!=''){
			$this->objParam->addFiltro("ingas.id_actividad_economica = ".$this->objParam->getParametro('id_actividad_economica'));
		}

		/*Aumentando Filtro para listar conceptos*/
		if($this->objParam->getParametro('tipo_documento')!=''){

			if ($this->objParam->getParametro('tipo_documento') == 'Factura') {
				$this->objParam->addFiltro("(''FACTCOMP''=ANY (ingas.sw_autorizacion))");
			} elseif ($this->objParam->getParametro('tipo_documento') == 'Recibo') {
				$this->objParam->addFiltro("(''RO''=ANY (ingas.sw_autorizacion))");
			}


		}


		/*****************************************/


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

		if($this->objParam->getParametro('listarConceptosVentas') != '') {

			$this->objParam->addFiltro("ingas.listado = ''".$this->objParam->getParametro('listarConceptosVentas')."''");

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
