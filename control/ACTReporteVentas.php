<?php
/**
*@package pXP
*@file ReporteVentas.php
*@author  (bvasquez)
*@date 28-01-2021
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTReporteVentas extends ACTbase{


  function listarCanalVentaPuntoVenta () {

    $this->objParam->getParametro('cod_catalogo') != ''  && $this->objParam->addFiltro("descripcion = ''".$this->objParam->getParametro('cod_catalogo')."''");
    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->listarCanalVentaPuntoVenta($this->objParam);
    $this->res->imprimirRespuesta($this->res->generarJson());

  }

  function listarCanalVenta () {


    if ($this->objParam->getParametro('id_lugar_fk') != 'TODOS'  && $this->objParam->getParametro('id_lugar_fk') != '') {
        $this->objParam->addFiltro("l.codigo = ''".$this->objParam->getParametro('id_lugar_fk')."''");
    }


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

    if($this->objParam->getParametro('id_lugar_fk') != 'TODOS'  && $this->objParam->getParametro('id_lugar_fk') !='') {
      $this->objParam->addFiltro("l.codigo = ''".$this->objParam->getParametro('id_lugar_fk')."''");
    }
    if($this->objParam->getParametro('canal') != 'Todos'  && $this->objParam->getParametro('canal') !='') {
      $this->objParam->addFiltro("c.codigo  = ANY (string_to_array(''".$this->objParam->getParametro('canal')."'','',''))");
    }

    if($this->objParam->getParametro('tipoVenta') != 'Todos' && $this->objParam->getParametro('tipoVenta') != ''){
      $this->objParam->addFiltro("p.tipo = ANY (string_to_array(''".$this->objParam->getParametro('tipoVenta')."'','',''))");
    }

    if($this->objParam->getParametro('code_iata') != 'TODOS' && $this->objParam->getParametro('code_iata') != ''){
      $this->objParam->addFiltro("p.codigo = ''".$this->objParam->getParametro('code_iata')."''");
    }

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

    if($this->objParam->getParametro('id_lugar_fk') != 'TODOS'  && $this->objParam->getParametro('id_lugar_fk') !='') {
      $this->objParam->addFiltro("l.codigo = ''".$this->objParam->getParametro('id_lugar_fk')."''");
    }

    if($this->objParam->getParametro('canal') != 'Todos'  && $this->objParam->getParametro('canal') !='') {
      $this->objParam->addFiltro("c.codigo  = ANY (string_to_array(''".$this->objParam->getParametro('canal')."'','',''))");
    }

    if($this->objParam->getParametro('tipoVenta') != 'Todos' && $this->objParam->getParametro('tipoVenta') != ''){
      $this->objParam->addFiltro("p.tipo = ANY (string_to_array(''".$this->objParam->getParametro('tipoVenta')."'','',''))");
    }


    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->listarPuntoVentaRbol($this->objParam);

    if($this->objParam->getParametro('_adicionar')!=''){

			$respuesta = $this->res->getDatos();
      array_unshift ( $respuesta, array('codigo'=>'Todos'));
			$this->res->setDatos($respuesta);
		}

    $this->res->imprimirRespuesta($this->res->generarJson());
  }

  function listarPuntoVentaTipo () {

    if($this->objParam->getParametro('id_lugar_fk') != 'TODOS'  && $this->objParam->getParametro('id_lugar_fk') !='') {
      $this->objParam->addFiltro("l.codigo = ''".$this->objParam->getParametro('id_lugar_fk')."''");
    }

    if($this->objParam->getParametro('tipo') != 'Todos'  && $this->objParam->getParametro('tipo') !='') {
      $this->objParam->addFiltro("c.codigo  = ANY (string_to_array(''".$this->objParam->getParametro('tipo')."'','',''))");
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

  function subLugarPais() {
    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->subLugarPais($this->objParam);
    if($this->objParam->getParametro('_adicionar')!=''){

			$respuesta = $this->res->getDatos();

	    array_unshift ( $respuesta, array(  'id_lugar'=>'0',
								                          'id_lugar_fk'=>'0',
									                        'codigo'=>'Todos',
																					'nombre'=>'Todos',
																					'tipo'=>'Todos'
                                          ));
			$this->res->setDatos($respuesta);
		}
    $this->res->imprimirRespuesta($this->res->generarJson());
  }

  function consultaFacturaVenta() {

    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->consultaFacturaVenta($this->objParam);
    $this->res->imprimirRespuesta($this->res->generarJson());
  }
  function consultaDetalleFactura(){
    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->consultaDetalleFactura($this->objParam);
    $this->res->imprimirRespuesta($this->res->generarJson());
  }
}

?>
