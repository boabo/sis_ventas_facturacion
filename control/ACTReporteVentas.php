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

  //  stages datatsss
  function puntoVentaPaiStage(){
    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->puntoVentaPaiStage($this->objParam);
    if($this->objParam->getParametro('_adicionar')!=''){
			$respuesta = $this->res->getDatos();
	    array_unshift ( $respuesta, array('country_code'=>'TODOS', 'country_name'=>'TODOS'));
			$this->res->setDatos($respuesta);
		}
    $this->res->imprimirRespuesta($this->res->generarJson());
  }

  function puntoVentaCiudadStage(){
    $this->filtrosStage();
    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->puntoVentaCiudadStage($this->objParam);
    if($this->objParam->getParametro('_adicionar')!=''){
			$respuesta = $this->res->getDatos();
	    array_unshift ( $respuesta, array('city_name'=>'TODOS', 'city_code'=>'TODOS'));
			$this->res->setDatos($respuesta);
		}
    $this->res->imprimirRespuesta($this->res->generarJson());
  }

  function listarCanalVentaStage(){

    $this->filtrosStage();
    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->listarCanalVentaStage($this->objParam);
    if($this->objParam->getParametro('_adicionar')!=''){
      $respuesta = $this->res->getDatos();
      array_unshift ( $respuesta, array('sale_channel'=>'TODOS'));
      $this->res->setDatos($respuesta);
    }
    $this->res->imprimirRespuesta($this->res->generarJson());
  }

  function listarPuntoVentaTipoStage(){
    $this->filtrosStage();
    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->listarPuntoVentaTipoStage($this->objParam);
    if($this->objParam->getParametro('_adicionar')!=''){
      $respuesta = $this->res->getDatos();
      array_unshift ( $respuesta, array('tipo_pos'=>'TODOS'));
      $this->res->setDatos($respuesta);
    }
    $this->res->imprimirRespuesta($this->res->generarJson());
  }

  function listarCodigoIataStage(){
    $this->filtrosStage();
    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->listarCodigoIataStage($this->objParam);
    if($this->objParam->getParametro('_adicionar')!=''){
      $respuesta = $this->res->getDatos();
      array_unshift ( $respuesta, array('iata_code'=>'TODOS', 'name_pv'=>'TODOS'));
      $this->res->setDatos($respuesta);
    }
    $this->res->imprimirRespuesta($this->res->generarJson());
  }

  function listarPuntoVentaOfficeIdStage(){
    $this->filtrosStage();
    $this->objFunc=$this->create('MODReporteVentas');
    $this->res=$this->objFunc->listarPuntoVentaOfficeIdStage($this->objParam);
    if($this->objParam->getParametro('_adicionar')!=''){
      $respuesta = $this->res->getDatos();
      array_unshift ( $respuesta, array('office_id'=>'TODOS'));
      $this->res->setDatos($respuesta);
    }
    $this->res->imprimirRespuesta($this->res->generarJson());
  }

  function filtrosStage(){
    if($this->objParam->getParametro('id_lugar_pais') != ''  && $this->objParam->getParametro('id_lugar_pais') !='TODOS') {
      $this->objParam->addFiltro("country_code = ''".$this->objParam->getParametro('id_lugar_pais')."''");
    }
    if($this->objParam->getParametro('id_lugar_ciudad') != ''  && $this->objParam->getParametro('id_lugar_ciudad') !='TODOS') {
      $this->objParam->addFiltro("city_code = ''".$this->objParam->getParametro('id_lugar_ciudad')."''");
    }
    if($this->objParam->getParametro('id_canal') != ''  && $this->objParam->getParametro('id_canal') !='TODOS') {
      $this->objParam->addFiltro("sale_channel = ''".$this->objParam->getParametro('id_canal')."''");
    }
    if($this->objParam->getParametro('tipo_venta') != ''  && $this->objParam->getParametro('tipo_venta') !='TODOS') {
      $this->objParam->addFiltro("tipo_pos = ''".$this->objParam->getParametro('tipo_venta')."''");
    }
    if($this->objParam->getParametro('id_codigo_aita') != ''  && $this->objParam->getParametro('id_codigo_aita') !='TODOS') {
      $this->objParam->addFiltro("iata_code = ''".$this->objParam->getParametro('id_codigo_aita')."''");
    }
  }
}

?>
