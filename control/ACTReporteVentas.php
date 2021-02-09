<?php
/**
*@package pXP
*@file ReporteVentas.php
*@author  (bvasquez)
*@date 28-01-2021
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTReporteVentas extends ACTbase{


  function listarCanalVenta () {

    $this->objParam->getParametro('cod_catalogo') != '' && $this->objParam->addFiltro("cat.descripcion = ''".$this->objParam->getParametro('cod_catalogo')."''");

    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->listarCanalVenta($this->objParam);
    $this->res->imprimirRespuesta($this->res->generarJson());

  }

  function listarPuntoVentaRbol () {

    $this->objParam->getParametro('offi_id') == 'no' && $this->objParam->addFiltro("puve.office_id is not null ");

    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->listarPuntoVentaRbol($this->objParam);

    if($this->objParam->getParametro('_adicionar')!=''){

			$respuesta = $this->res->getDatos();
	    array_unshift ( $respuesta, array(  'id_punto_venta'=>'0',
	                                        'nombre'=>'Todos',
								                          'codigo'=>'Todos',
									                        'codigo'=>'Todos',
									                        'tipo'=>'Todos',
                                          'office_id'=>'Todos'));
			$this->res->setDatos($respuesta);
		}

    $this->res->imprimirRespuesta($this->res->generarJson());
  }


}

?>
