<?php
/**
*@package pXP
*@file gen-MODDepositos.php
*@author  (miguel.mamani)
*@date 11-09-2017 15:32:32
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODDepositos extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarDepositos(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_depositos_sel';
		$this->transaccion='VF_CDO_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

				//Definicion de la lista del resultado del query
				$this->setParametro('relacion_deposito','relacion_deposito','varchar');
				$this->setParametro('id_moneda_deposito_agrupado','id_moneda_deposito_agrupado','int4');

				$this->captura('id_apertura_cierre_caja','int4');
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

	function insertarDepositos(){
		//Definicion de variables para ejecucion del procedimiento


        $this->procedimiento='vef.ft_depositos_ime';
		$this->transaccion='VF_CDO_INS';
		$this->tipo_procedimiento='IME';



		//Define los parametros para la funcion
		$this->setParametro('nro_deposito','nro_deposito','varchar');
		$this->setParametro('id_punto_venta','id_punto_venta','int4');
		$this->setParametro('codigo','codigo','varchar');
		$this->setParametro('nombre_sucursal','nombre_sucursal','varchar');
		$this->setParametro('id_usuario_cajero','id_usuario_cajero','int4');
		$this->setParametro('fecha_hora_cierre','fecha_hora_cierre','timestamp');
		$this->setParametro('cajero','cajero','text');
		$this->setParametro('nombre_punto_venta','nombre_punto_venta','varchar');
		$this->setParametro('id_entrega_brinks','id_entrega_brinks','int4');
		$this->setParametro('fecha_apertura_cierre','fecha_apertura_cierre','date');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('monto_inicial_moneda_extranjera','monto_inicial_moneda_extranjera','numeric');
		$this->setParametro('arqueo_moneda_local','arqueo_moneda_local','numeric');
		$this->setParametro('fecha_venta','fecha_venta','date');
		$this->setParametro('id_sucursal','id_sucursal','int4');
		$this->setParametro('codigo_lugar','codigo_lugar','varchar');
		$this->setParametro('monto_inicial','monto_inicial','numeric');
		$this->setParametro('arqueo_moneda_extranjera','arqueo_moneda_extranjera','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarDepositos(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_depositos_ime';
		$this->transaccion='VF_CDO_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_apertura_cierre_caja','id_apertura_cierre_caja','int4');
		$this->setParametro('nro_deposito','nro_deposito','varchar');
		$this->setParametro('id_punto_venta','id_punto_venta','int4');
		$this->setParametro('codigo','codigo','varchar');
		$this->setParametro('nombre_sucursal','nombre_sucursal','varchar');
		$this->setParametro('id_usuario_cajero','id_usuario_cajero','int4');
		$this->setParametro('fecha_hora_cierre','fecha_hora_cierre','timestamp');
		$this->setParametro('cajero','cajero','text');
		$this->setParametro('nombre_punto_venta','nombre_punto_venta','varchar');
		$this->setParametro('id_entrega_brinks','id_entrega_brinks','int4');
		$this->setParametro('fecha_apertura_cierre','fecha_apertura_cierre','date');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('monto_inicial_moneda_extranjera','monto_inicial_moneda_extranjera','numeric');
		$this->setParametro('arqueo_moneda_local','arqueo_moneda_local','numeric');
		$this->setParametro('fecha_venta','fecha_venta','date');
		$this->setParametro('id_sucursal','id_sucursal','int4');
		$this->setParametro('codigo_lugar','codigo_lugar','varchar');
		$this->setParametro('monto_inicial','monto_inicial','numeric');
		$this->setParametro('arqueo_moneda_extranjera','arqueo_moneda_extranjera','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarDepositos(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_depositos_ime';
		$this->transaccion='VF_CDO_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_apertura_cierre_caja','id_apertura_cierre_caja','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}


}
?>
