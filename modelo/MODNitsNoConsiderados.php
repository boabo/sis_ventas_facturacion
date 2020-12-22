<?php
/**
*@package pXP
*@file gen-MODNitsNoConsiderados.php
*@author  (maylee.perez)
*@date 21-12-2020 20:13:12
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODNitsNoConsiderados extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarNitsNoConsiderados(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_nits_no_considerados_sel';
		$this->transaccion='VF_NITNCONS_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_nits_no_considerados','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('nit_ci','varchar');
		$this->captura('razon_social','varchar');
		$this->captura('t_contr','varchar');
		$this->captura('incl_rep','varchar');
		$this->captura('observaciones','varchar');
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
			
	function insertarNitsNoConsiderados(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_nits_no_considerados_ime';
		$this->transaccion='VF_NITNCONS_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nit_ci','nit_ci','varchar');
		$this->setParametro('razon_social','razon_social','varchar');
		$this->setParametro('t_contr','t_contr','varchar');
		$this->setParametro('incl_rep','incl_rep','varchar');
		$this->setParametro('observaciones','observaciones','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarNitsNoConsiderados(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_nits_no_considerados_ime';
		$this->transaccion='VF_NITNCONS_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_nits_no_considerados','id_nits_no_considerados','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nit_ci','nit_ci','varchar');
		$this->setParametro('razon_social','razon_social','varchar');
		$this->setParametro('t_contr','t_contr','varchar');
		$this->setParametro('incl_rep','incl_rep','varchar');
		$this->setParametro('observaciones','observaciones','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarNitsNoConsiderados(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_nits_no_considerados_ime';
		$this->transaccion='VF_NITNCONS_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_nits_no_considerados','id_nits_no_considerados','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>