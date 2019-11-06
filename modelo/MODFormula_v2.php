<?php
/**
*@package pXP
*@file gen-MODFormula.php
*@author  (ivaldivia)
*@date 17-09-2019 15:28:13
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODFormula_v2 extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarFormula(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_formula_v2_sel';
		$this->transaccion='VF_FORMULAV2_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->captura('id_formula','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('nombre','varchar');
		$this->captura('descripcion','text');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('punto_venta_asociado','varchar');
		$this->captura('tipo_punto_venta','varchar');
		$this->captura('nombres_punto_venta','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarFormula(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_formula_v2_ime';
		$this->transaccion='VF_FORMULAV2_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('descripcion','descripcion','text');
		$this->setParametro('tipo_punto_venta','tipo_punto_venta','varchar');
		$this->setParametro('punto_venta_asociado','punto_venta_asociado','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarFormula(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_formula_v2_ime';
		$this->transaccion='VF_FORMULAV2_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_formula','id_formula','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('descripcion','descripcion','text');
		$this->setParametro('tipo_punto_venta','tipo_punto_venta','varchar');
		$this->setParametro('punto_venta_asociado','punto_venta_asociado','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarFormula(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_formula_v2_ime';
		$this->transaccion='VF_FORMULAV2_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_formula','id_formula','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

}
?>
