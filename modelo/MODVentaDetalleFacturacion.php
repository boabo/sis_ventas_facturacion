<?php
/**
*@package pXP
*@file gen-MODVentaDetalleFacturacion.php
*@author  (ivaldivia)
*@date 10-05-2019 19:33:22
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODVentaDetalleFacturacion extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarVentaDetalleFacturacion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_detalle_facturacion_sel';
		$this->transaccion='VF_FACTDET_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->capturaCount('venta_total','numeric');
		//Definicion de la lista del resultado del query
		$this->captura('id_venta_detalle','int4');
		$this->captura('id_formula','int4');
		$this->captura('id_item','int4');
		$this->captura('id_medico','int4');
		$this->captura('id_sucursal_producto','int4');
		$this->captura('id_vendedor','int4');
		$this->captura('id_venta','int4');
		$this->captura('porcentaje_descuento','numeric');
		$this->captura('descripcion','text');
		$this->captura('id_boleto','int4');
		$this->captura('estado','varchar');
		$this->captura('obs','varchar');
		$this->captura('id_unidad_medida','int4');
		$this->captura('cantidad','numeric');
		$this->captura('tipo','varchar');
		$this->captura('bruto','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('id_producto','int4');
		$this->captura('serie','varchar');
		$this->captura('precio','numeric');
		$this->captura('precio_sin_descuento','numeric');
		$this->captura('kg_fino','varchar');
		$this->captura('ley','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('nombre_producto','varchar');
		$this->captura('total','numeric');
		$this->captura('excento','numeric');
		$this->captura('tiene_excento','varchar');
		$this->captura('id_moneda','int4');
		$this->captura('codigo_internacional','varchar');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarVentaDetalleFacturacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_detalle_facturacion_ime';
		$this->transaccion='VF_FACTDETIND_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_venta','id_venta','int4');
		$this->setParametro('descripcion','descripcion','text');
		$this->setParametro('cantidad_det','cantidad','numeric');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_producto','id_producto','int4');
		//$this->setParametro('id_sucursal_producto','id_sucursal_producto','int4');
		$this->setParametro('precio','precio','numeric');
	//	$this->setParametro('excento','excento','numeric');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarVentaDetalleFacturacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_detalle_facturacion_ime';
		$this->transaccion='VF_FACTDET_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_venta_detalle','id_venta_detalle','int4');
		$this->setParametro('id_venta','id_venta','int4');
		$this->setParametro('descripcion','descripcion','text');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_producto','id_producto','int4');
		$this->setParametro('precio','precio','numeric');
		$this->setParametro('cantidad_det','cantidad','numeric');
		$this->setParametro('funcion','funcion','varchar');
		$this->setParametro('excento','excento','numeric');
		$this->setParametro('id_moneda','id_moneda','int4');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		//var_dump($this->respuesta);
		return $this->respuesta;
	}

	function eliminarVentaDetalleFacturacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_detalle_facturacion_ime';
		$this->transaccion='VF_FACTDET_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_venta_detalle','id_venta_detalle','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}


	function verificarExcento(){
			//Definicion de variables para ejecucion del procedimientp
			$this->procedimiento='vef.ft_venta_detalle_facturacion_ime';
			$this->transaccion='VF_FACTEXCEN_INS';
			$this->tipo_procedimiento='IME';//tipo de transaccion

			$this->setParametro('id_formula','id_formula','int4');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}

	function actualizarExcento(){
			//Definicion de variables para ejecucion del procedimientp
			$this->procedimiento='vef.ft_venta_detalle_facturacion_ime';
			$this->transaccion='VF_FACTUDT_MOD';
			$this->tipo_procedimiento='IME';//tipo de transaccion

			$this->setParametro('id_venta','id_venta','int4');
			$this->setParametro('valor_excento','valor_excento','numeric');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}

}
?>
