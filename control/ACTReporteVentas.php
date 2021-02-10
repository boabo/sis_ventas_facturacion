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

    // $this->objParam->getParametro('cod_catalogo') != '' && $this->objParam->addFiltro("c.descripcion = ''".$this->objParam->getParametro('cod_catalogo')."''");

    $this->objParam->getParametro('id_catalogo') != 'TODOS' && $this->objParam->addFiltro("l.codigo = ''".$this->objParam->getParametro('id_catalogo')."''");

    // var_dump($this->objParam->getParametro('id_catalogo'));exit;

    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->listarCanalVenta($this->objParam);

    if($this->objParam->getParametro('_adicionar')!=''){

			$respuesta = $this->res->getDatos();

	    array_unshift ( $respuesta, array(  'id_catalogo'=>'0',
								                          'codigo'=>'Todos',
									                        'descripcion'=>'Todos'
                                          ));
			$this->res->setDatos($respuesta);
		}

    $this->res->imprimirRespuesta($this->res->generarJson());

  }

  function listarPuntoVentaOfficeId () {

    if($this->objParam->getParametro('id_lugar_fk') != 0  || $this->objParam->getParametro('id_lugar_fk') !='') {
      $this->objParam->addFiltro("l.codigo = ''".$this->objParam->getParametro('id_lugar_fk')."''");
    }
    if($this->objParam->getParametro('canal') != 0  || $this->objParam->getParametro('canal') !='') {
      $this->objParam->addFiltro("c.id_catalogo in (".$this->objParam->getParametro('canal').")");
    }

    $this->objParam->getParametro('tipoVenta') != 'Todos' && $this->objParam->addFiltro("p.tipo = ANY (string_to_array(''".$this->objParam->getParametro('tipoVenta')."'','',''))");
    $this->objParam->getParametro('code_iata') != 'TODOS' && $this->objParam->addFiltro("p.codigo = ''".$this->objParam->getParametro('code_iata')."''");

    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->listarPuntoVentaOfficeId($this->objParam);

    if($this->objParam->getParametro('_adicionar')!=''){

      $respuesta = $this->res->getDatos();
      array_unshift ( $respuesta, array(
                                        'id_punto_venta' => 0,
                                        'office_id'=>'Todos'));
      $this->res->setDatos($respuesta);
    }

    $this->res->imprimirRespuesta($this->res->generarJson());
  }
  function listarPuntoVentaRbol () {

    // $this->objParam->getParametro('offi_id') == 'no' && $this->objParam->addFiltro("puve.office_id is not null ");

    if($this->objParam->getParametro('id_lugar_fk') != 0  || $this->objParam->getParametro('id_lugar_fk') !='') {
      $this->objParam->addFiltro("l.codigo = ''".$this->objParam->getParametro('id_lugar_fk')."''");
    }

    if($this->objParam->getParametro('canal') != 0  || $this->objParam->getParametro('canal') !='') {
      $this->objParam->addFiltro("c.id_catalogo in (".$this->objParam->getParametro('canal').")");
    }
    // $this->objParam->getParametro('canal') != '' && $this->objParam->addFiltro("c.codigo = ANY (string_to_array(''".$this->objParam->getParametro('canal')."'','',''))");
    $this->objParam->getParametro('tipoVenta') != 'Todos' && $this->objParam->addFiltro("p.tipo = ANY (string_to_array(''".$this->objParam->getParametro('tipoVenta')."'','',''))");
    // $this->objParam->getParametro('canal') != 0 && $this->objParam->addFiltro("c.id_catalogo in (".$this->objParam->getParametro('canal').")");

    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->listarPuntoVentaRbol($this->objParam);

    if($this->objParam->getParametro('_adicionar')!=''){

			$respuesta = $this->res->getDatos();
      array_unshift ( $respuesta, array('codigo'=>'Todos'));
	    // array_unshift ( $respuesta, array(  'id_punto_venta'=>'0',
	    //                                     'nombre'=>'Todos',
			// 					                          'codigo'=>'Todos',
			// 						                        'codigo'=>'Todos',
			// 						                        'tipo'=>'Todos',
      //                                     'office_id'=>'Todos'));
			$this->res->setDatos($respuesta);
		}

    $this->res->imprimirRespuesta($this->res->generarJson());
  }

  function listarPuntoVentaTipo () {

    if($this->objParam->getParametro('id_lugar_fk') != 0  || $this->objParam->getParametro('id_lugar_fk') !='') {
      $this->objParam->addFiltro("l.codigo = ''".$this->objParam->getParametro('id_lugar_fk')."''");
    }
    // $this->objParam->getParametro('id_lugar_fk') != 0 && $this->objParam->addFiltro("l.codigo = ''".$this->objParam->getParametro('id_lugar_fk')."''");

    // $this->objParam->getParametro('tipo') != '' && $this->objParam->addFiltro("c.codigo = ANY (string_to_array(''".$this->objParam->getParametro('tipo')."'','',''))");

    if($this->objParam->getParametro('tipo') != 0  || $this->objParam->getParametro('tipo') !='') {
      $this->objParam->addFiltro("c.id_catalogo in (".$this->objParam->getParametro('tipo').")");
    }    

    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->listarPuntoVentaTipo($this->objParam);

    if($this->objParam->getParametro('_adicionar')!=''){

			$respuesta = $this->res->getDatos();
	    array_unshift ( $respuesta, array(  'id_catalogo_canal'=>'0',
								                          'tipo'=>'Todos'));
			$this->res->setDatos($respuesta);
		}

    $this->res->imprimirRespuesta($this->res->generarJson());
  }

  function getCanal() {

    $this->objParam->getParametro('id_catalogos') != 0 && $this->objParam->addFiltro("id_catalogo in (".$this->objParam->getParametro('id_catalogos').")");

    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->getCanal($this->objParam);
    $this->res->imprimirRespuesta($this->res->generarJson());
  }

}

?>
