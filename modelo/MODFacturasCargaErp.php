<?php
/**
*@package pXP
*@file gen-MODFacturasCarga.php
*@author  (ismael.valdivia)
*@date 08-03-2019 15:50:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODFacturasCargaErp extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function insertarFacturasErp(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_insertar_facturas_carga_erp_ime';
		$this->transaccion='VEF_INS_FACCARGA_ERP';
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
		//$this->setParametro('usuario_registro','usuario_registro','varchar'); Comentando ya no sera necesraio este campo

		/*Nuevos parametros*/
		$this->setParametro('moneda','moneda','varchar');//Mandar el codigo de la moneda BOB
		$this->setParametro('codigo_punto_venta','codigo_punto_venta','varchar');//Mandar el codigo del punto de venta
		$this->setParametro('id_funcionario','id_funcionario','int4');//Mandar el codigo del punto de venta
    $this->setParametro('observaciones','observaciones','varchar');//Registraremos las observaciones de la factura carga
		$this->setParametro('json_venta_forma_pago','json_venta_forma_pago','text');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}



	function anularFacturasErp(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_insertar_facturas_carga_erp_ime';
		$this->transaccion='VEF_ANULAR_FCA_ERP';
		$this->tipo_procedimiento='IME';

    $this->setParametro('id_origen','id_origen','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modFormaPagoFacturasErp(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_insertar_facturas_carga_erp_ime';
		$this->transaccion='VEF_MODFOP_FCA_ERP';
		$this->tipo_procedimiento='IME';

		$this->setParametro('id_origen','id_origen','varchar');
		$this->setParametro('json_venta_forma_pago','json_venta_forma_pago','text');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}
}
?>
