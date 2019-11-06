<?php
/**
*@package pXP
*@file gen-MODAsociarBoletos.php
*@author  (ivaldivia)
*@date 15-08-2019 13:15:22
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODAsociarBoletos extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarAsociarBoletos(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_boletos_asociados_sel';
		$this->transaccion='VF_LISTASOCIADOS_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->captura('id_boleto','int4');
		$this->captura('id_boleto_asociado','int4');
    $this->captura('id_venta','int4');
		$this->captura('nro_boleto','varchar');
		$this->captura('nit','varchar');
		$this->captura('pasajero','varchar');
		$this->captura('razon','varchar');
		$this->captura('ruta','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarAsociarBoletos(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_boletos_asociados_ime';
		$this->transaccion='VF_ASOBOLETOS_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		//$this->setParametro('id_boleto','id_boleto','varchar');
		$this->setParametro('nro_boleto','nro_boleto','varchar');
		$this->setParametro('id_venta','id_venta','int4');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	// function modificarAsociarBoletos(){
	// 	//Definicion de variables para ejecucion del procedimiento
	// 	$this->procedimiento='vef.ft_boletos_asociados_ime';
	// 	$this->transaccion='VF_acca_MOD';
	// 	$this->tipo_procedimiento='IME';
  //
	// 	//Define los parametros para la funcion
	// 	$this->setParametro('id_apertura_asociada','id_apertura_asociada','int4');
	// 	$this->setParametro('estado_reg','estado_reg','varchar');
	// 	$this->setParametro('id_apertura_cierre_caja','id_apertura_cierre_caja','int4');
	// 	$this->setParametro('id_deposito','id_deposito','int4');
  //
	// 	//Ejecuta la instruccion
	// 	$this->armarConsulta();
	// 	$this->ejecutarConsulta();
  //
	// 	//Devuelve la respuesta
	// 	return $this->respuesta;
	// }

	function eliminarAsociarBoletos(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_boletos_asociados_ime';
		$this->transaccion='VF_ASOBOLETOS_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_boleto_asociado','id_boleto_asociado','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

}
?>
