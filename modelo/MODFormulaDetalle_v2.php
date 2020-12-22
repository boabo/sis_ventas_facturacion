<?php
/**
*@package pXP
*@file gen-MODFormulaDetalle_v2.php
*@author  (ivaldivia)
*@date 18-09-2019 21:05:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODFormulaDetalle_v2 extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarFormulaDetalle(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_formula_detalle_v2_sel';
		$this->transaccion='VF_DETFORV2_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->captura('id_formula_detalle','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('cantidad','numeric');
		$this->captura('id_item','int4');
		$this->captura('id_formula','int4');
		$this->captura('id_concepto_ingas','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_ingas','varchar');
		$this->captura('precio','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarFormulaDetalle(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_formula_detalle_v2_ime';
		$this->transaccion='VF_DETFORV2_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		//$this->setParametro('cantidad_det','cantidad','numeric');
		$this->setParametro('id_formula','id_formula','int4');
		$this->setParametro('id_concepto_ingas','id_concepto_ingas','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarFormulaDetalle(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_formula_detalle_v2_ime';
		$this->transaccion='VF_DETFORV2_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_formula_detalle','id_formula_detalle','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		// $this->setParametro('cantidad','cantidad','numeric');
		// $this->setParametro('id_item','id_item','int4');
		$this->setParametro('id_formula','id_formula','int4');
		$this->setParametro('id_concepto_ingas','id_concepto_ingas','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarFormulaDetalle(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_formula_detalle_v2_ime';
		$this->transaccion='VF_DETFORV2_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_formula_detalle','id_formula_detalle','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

}
?>
