<?php
/**
*@package pXP
*@file gen-MODAperturaCierreCajaAsociada.php
*@author  (ivaldivia)
*@date 15-08-2019 13:15:22
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODAperturaCierreCajaAsociada extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarAperturaCierreCajaAsociada(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_apertura_cierre_caja_asociada_sel';
		$this->transaccion='VF_acca_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->capturaCount('venta_total_ml','numeric');
		$this->capturaCount('venta_total_me','numeric');
		//Definicion de la lista del resultado del query
		$this->captura('id_apertura_asociada','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_apertura_cierre_caja','int4');
		$this->captura('id_deposito','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');

		$this->captura('id_punto_venta','int4');
		$this->captura('id_entrega_brinks','int4');
		$this->captura('id_usuario_cajero','int4');
		$this->captura('codigo_padre','varchar');
		$this->captura('estacion','varchar');
		$this->captura('nombre_punto_venta','varchar');
		$this->captura('codigo','varchar');
		$this->captura('cajero','text');
		$this->captura('fecha_recojo','date');
		$this->captura('fecha_venta','date');
		$this->captura('arqueo_moneda_local','numeric');
		$this->captura('arqueo_moneda_extranjera','numeric');
		$this->captura('deposito_bs','numeric');
		$this->captura('deposito_usd','numeric');
		$this->captura('tipo_cambio','numeric');
		$this->captura('diferencia_bs','numeric');
		$this->captura('diferencia_usd','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarAperturaCierreCajaAsociada(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_apertura_cierre_caja_asociada_ime';
		$this->transaccion='VF_acca_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_apertura_cierre_caja','id_apertura_cierre_caja','varchar');
		$this->setParametro('id_deposito','id_deposito','int4');
		$this->setParametro('id_punto_venta','id_punto_venta','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarAperturaCierreCajaAsociada(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_apertura_cierre_caja_asociada_ime';
		$this->transaccion='VF_acca_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_apertura_asociada','id_apertura_asociada','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_apertura_cierre_caja','id_apertura_cierre_caja','int4');
		$this->setParametro('id_deposito','id_deposito','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarAperturaCierreCajaAsociada(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_apertura_cierre_caja_asociada_ime';
		$this->transaccion='VF_acca_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_apertura_asociada','id_apertura_asociada','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function getSumaTotal(){
			//Definicion de variables para ejecucion del procedimientp
			$this->procedimiento='vef.ft_apertura_cierre_caja_asociada_ime';
			$this->transaccion='VF_SUMA_TOTAL_IME';
			$this->tipo_procedimiento='IME';//tipo de transaccion

			$this->setParametro('id_apertura','id_apertura','varchar');
			$this->setParametro('id_deposito','id_deposito','int4');
			$this->setParametro('id_moneda_deposito','id_moneda_deposito','int4');
			$this->setParametro('monto_deposito','monto_deposito','numeric');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}
	function getDatosSucursal(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_apertura_cierre_caja_asociada_ime';
		$this->transaccion='VF_DATSUC_IME';
		$this->tipo_procedimiento='IME';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->setParametro('id_punto_venta','id_punto_venta','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}


}
?>
