<?php
/**
*@package pXP
*@file gen-MODServicios.php
*@author  (ivaldivia)
*@date 10-09-2019 16:17:39
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODServicios extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarServicios(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_servicios_sel';
		$this->transaccion='VEF_INGAS_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->captura('id_concepto_ingas','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('tipo','varchar');
		$this->captura('desc_ingas','varchar');
		$this->captura('movimiento','varchar');
		$this->captura('sw_tes','varchar');
		$this->captura('activo_fijo','varchar');
		$this->captura('almacenable','varchar');
		$this->captura('sw_autorizacion','_varchar');
		$this->captura('codigo','varchar');
		$this->captura('id_unidad_medida','int4');
		$this->captura('nandina','varchar');
		//$this->captura('ruta_foto','varchar');
		$this->captura('id_cat_concepto','int4');
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
		$this->captura('id_moneda','int4');
		$this->captura('precio','numeric');
		$this->captura('desc_moneda','varchar');
		$this->captura('requiere_descripcion','varchar');
		$this->captura('excento','varchar');
		$this->captura('nombre_actividad','varchar');
		$this->captura('id_actividad_economica','int4');
		$this->captura('nombres_punto_venta','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarServiciosPaquetes(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_servicios_sel';
		$this->transaccion='VEF_INGASPAQ_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setParametro('id_formula','id_formula','int4');
		//Definicion de la lista del resultado del query
		$this->captura('id_concepto_ingas','int4');
		$this->captura('desc_ingas','varchar');
		$this->captura('precio','numeric');
		$this->captura('id_moneda','int4');
		$this->captura('excento','varchar');
		$this->captura('requiere_descripcion','varchar');
		$this->captura('desc_moneda','varchar');
		$this->captura('tipo','varchar');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}


	function insertarServicios(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_servicios_ime';
		$this->transaccion='VEF_INGAS_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('desc_ingas','desc_ingas','varchar');
		$this->setParametro('movimiento','movimiento','varchar');
		$this->setParametro('sw_tes','sw_tes','varchar');
		$this->setParametro('activo_fijo','activo_fijo','varchar');
		$this->setParametro('almacenable','almacenable','varchar');
		$this->setParametro('sw_autorizacion','sw_autorizacion','_varchar');
		$this->setParametro('codigo','codigo','varchar');
		$this->setParametro('tipo_punto_venta','tipo_punto_venta','varchar');
		$this->setParametro('punto_venta_asociado','punto_venta_asociado','varchar');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('precio','precio','numeric');
		$this->setParametro('requiere_descripcion','requiere_descripcion','varchar');
		$this->setParametro('excento','excento','varchar');
		$this->setParametro('id_actividad_economica','id_actividad_economica','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarServicios(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_servicios_ime';
		$this->transaccion='VEF_INGAS_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_concepto_ingas','id_concepto_ingas','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('desc_ingas','desc_ingas','varchar');
		$this->setParametro('movimiento','movimiento','varchar');
		$this->setParametro('sw_tes','sw_tes','varchar');
		$this->setParametro('activo_fijo','activo_fijo','varchar');
		$this->setParametro('almacenable','almacenable','varchar');
		$this->setParametro('sw_autorizacion','sw_autorizacion','_varchar');
		$this->setParametro('codigo','codigo','varchar');
		$this->setParametro('tipo_punto_venta','tipo_punto_venta','varchar');
		$this->setParametro('punto_venta_asociado','punto_venta_asociado','varchar');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('precio','precio','numeric');
		$this->setParametro('requiere_descripcion','requiere_descripcion','varchar');
		$this->setParametro('excento','excento','varchar');
		$this->setParametro('id_actividad_economica','id_actividad_economica','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarServicios(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_servicios_ime';
		$this->transaccion='VEF_INGAS_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_concepto_ingas','id_concepto_ingas','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

}
?>
