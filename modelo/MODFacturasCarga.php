<?php
/**
*@package pXP
*@file gen-MODFacturasCarga.php
*@author  (ismael.valdivia)
*@date 08-03-2019 15:50:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODFacturasCarga extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function insertarFacturas(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_insertar_facturas_carga_ime';
		$this->transaccion='VEF_INS_FACCARGA';
		$this->tipo_procedimiento='IME';

    $this->setParametro('fecha','fecha','varchar');
    $this->setParametro('nro_factura','nro_factura','varchar');
    $this->setParametro('nro_autorizacion','nro_autorizacion','varchar');
    $this->setParametro('estado','estado','varchar');
    $this->setParametro('nit','nit','varchar');
    $this->setParametro('razon_social','razon_social','varchar');
    $this->setParametro('importe_total','importe_total','varchar');
    $this->setParametro('codigo_control','codigo_control','varchar');
    $this->setParametro('id_origen','id_origen','varchar');
    $this->setParametro('tipo_factura','tipo_factura','varchar');
    $this->setParametro('usuario_registro','usuario_registro','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}



	function anularFacturas(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_insertar_facturas_carga_ime';
		$this->transaccion='VEF_ANULAR_FCA';
		$this->tipo_procedimiento='IME';

    $this->setParametro('id_origen','id_origen','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}


}
?>
