<?php
/**
*@package pXP
*@file gen-MODModFormaPago.php
*@author  (miguel.mamani)
*@date 13-12-2017 21:37:47
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODModFormaPago extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarModFormaPago(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_mod_forma_pago_sel';
		$this->transaccion='OBING_CFM_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_mod_forma_pago','int4');
		$this->captura('fecha','date');
		$this->captura('ctacte','varchar');
		$this->captura('forma','varchar');
		$this->captura('agt','numeric');
		$this->captura('estacion','varchar');
		$this->captura('pais','varchar');
		$this->captura('comision','numeric');
		$this->captura('usuario','varchar');
		$this->captura('importe','numeric');
		$this->captura('autoriza','varchar');
		$this->captura('observa','varchar');
		$this->captura('hora_mod','bpchar');
		$this->captura('tarjeta','varchar');
		$this->captura('billete','numeric');
		$this->captura('numero','varchar');
		$this->captura('moneda','varchar');
		$this->captura('pagomco','numeric');
		$this->captura('fecha_mod','date');
		$this->captura('punto_venta','varchar');

		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}

			
}
?>