<?php
/**
*@package pXP
*@file gen-MODVenta.php
*@author  (admin)
*@date 01-06-2015 05:58:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODReportesVentas extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarReporteDetalle(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_repventa_sel';
		$this->transaccion='VF_REPDETBOA_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_sucursal','id_sucursal','integer');
		$this->setParametro('id_punto_venta','id_punto_venta','integer');
		$this->setParametro('id_usuario_cajero','id_usuario_cajero','integer');
		$this->setParametro('fecha_desde','fecha_desde','date');
		$this->setParametro('fecha_hasta','fecha_hasta','date');

		//Definicion de la lista del resultado del query
		$this->captura('moneda_emision','varchar');
		$this->captura('tipo','varchar');
		$this->captura('fecha','date');
		$this->captura('correlativo','varchar');
		$this->captura('tipo_factura','varchar');
		$this->captura('pasajero','varchar');
		$this->captura('boleto','varchar');
		$this->captura('pnr','varchar');
		$this->captura('codigo_auxiliar','varchar');
		$this->captura('ruta','varchar');
		$this->captura('conceptos','varchar');
		$this->captura('forma_pago','text');
		$this->captura('monto_cash_usd','numeric');
    $this->captura('monto_cc_usd','numeric');
    $this->captura('monto_cte_usd','numeric');
    $this->captura('monto_mco_usd','numeric');
    $this->captura('monto_otro_usd','numeric');
    $this->captura('monto_cash_mb','numeric');
    $this->captura('monto_cc_mb','numeric');
    $this->captura('monto_cte_mb','numeric');
    $this->captura('monto_mco_mb','numeric');
    $this->captura('monto_otro_mb','numeric');
		$this->captura('neto','numeric');
		$this->captura('precios_detalles','varchar');
    $this->captura('mensaje_error','varchar');
		$this->captura('comision','numeric');

		$this->captura('monto_deposito_mb','numeric');
    $this->captura('monto_deposito_usd','numeric');
		$this->captura('nombre_pv','varchar');
		$this->captura('estado_emision','varchar');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
      //  var_dump($this->respuesta);exit;

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarReporteXProducto () {
		$this->procedimiento='vef.ft_repventa_sel';
		$this->transaccion='VF_REPXPROD_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_sucursal','id_sucursal','integer');
		$this->setParametro('id_productos','id_productos','varchar');
		$this->setParametro('fecha_desde','fecha_desde','date');
		$this->setParametro('fecha_hasta','fecha_hasta','date');

		$this->captura('estado','varchar');
		$this->captura('tipo_documento','varchar');
		$this->captura('fecha','varchar');
		$this->captura('autorizacion','varchar');
		$this->captura('nit','varchar');
		$this->captura('razon_social','varchar');
		$this->captura('productos','varchar');
		$this->captura('nro_doc','varchar');
		$this->captura('monto','numeric');
		$this->captura('neto','numeric');
		$this->captura('iva','numeric');
		$this->captura('it','numeric');
		$this->captura('ingreso','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();

		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarReporteResumen(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_repventa_sel';
		$this->transaccion='VF_REPRESBOA_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_sucursal','id_sucursal','integer');
		$this->setParametro('id_punto_venta','id_punto_venta','integer');
		$this->setParametro('fecha_desde','fecha_desde','date');
		$this->setParametro('fecha_hasta','fecha_hasta','date');

		//Definicion de la lista del resultado del query
		$this->captura('fecha','date');
		$this->captura('concepto','varchar');
		$this->captura('monto_tarjeta','numeric');
		$this->captura('monto_cash','numeric');
		$this->captura('monto','numeric');


		//Ejecuta la instruccion
		$this->armarConsulta();

		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarConceptosSucursal(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_repventa_sel';
		$this->transaccion='VF_CONSUC_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->count =false;

		$this->setParametro('id_sucursal','id_sucursal','integer');
		$this->setParametro('id_punto_venta','id_punto_venta','integer');
		$this->setParametro('fecha_desde','fecha_desde','date');
		$this->setParametro('fecha_hasta','fecha_hasta','date');

		//Definicion de la lista del resultado del query
		$this->captura('nombre','varchar');
		$this->captura('tipo','varchar');


		//Ejecuta la instruccion
		$this->armarConsulta();


		$this->ejecutarConsulta();
		//var_dump("AQUI LLEGA",$this->respuesta);exit;
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarResumenFacturaComputarizada(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_repventa_sel';
		$this->transaccion='VF_REPRESUCOMP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('formato_reporte','formato_reporte','varchar');
		$this->setParametro('id_punto_venta','id_punto_venta','integer');
		$this->setParametro('id_concepto','id_concepto','integer');
		$this->setParametro('desde','desde','varchar');
		$this->setParametro('hasta','hasta','varchar');
		$this->setParametro('id_usuario_cajero','id_usuario_cajero','integer');
		$this->setParametro('tipo_documento','tipo_documento','varchar');

		$this->captura('conceptos','varchar');
		$this->captura('total_precio','numeric');
		$this->captura('nombre','varchar');
		$this->captura('codigo','varchar');
		$this->captura('desc_persona','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
    //var_dump($this->respuesta);

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarFacturaComputarizadaDetalle(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_repventa_sel';
		$this->transaccion='VF_REPFACTDET_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		//$this->setCount(false);

		$this->setParametro('id_punto_venta','id_punto_venta','integer');
		$this->setParametro('id_concepto','id_concepto','integer');
		$this->setParametro('desde','desde','varchar');
		$this->setParametro('hasta','hasta','varchar');
		$this->setParametro('id_usuario_cajero','id_usuario_cajero','integer');
		$this->setParametro('tipo_documento','tipo_documento','varchar');
		$this->setParametro('nit','nit','varchar');
		$this->setParametro('imprimir_reporte','imprimir_reporte','varchar');
		//$this->setCount(false);

		$this->capturaCount('totales_comision', 'numeric');
		$this->capturaCount('totales_exento', 'numeric');
		$this->capturaCount('totales_venta', 'numeric');
		$this->capturaCount('total_detalle', 'numeric');


		$this->captura('id_venta','integer');
		$this->captura('total_venta','varchar');
		$this->captura('fecha','varchar');
		$this->captura('conceptos','varchar');
		$this->captura('nombre','varchar');
		$this->captura('codigo','varchar');
		$this->captura('observaciones','varchar');
		$this->captura('nro_factura','integer');
		$this->captura('cantidad','varchar');
		$this->captura('precio','varchar');
		$this->captura('exento','varchar');
		$this->captura('comision','varchar');
		$this->captura('total_precio','varchar');
		$this->captura('moneda','varchar');
		$this->captura('num_tarjeta','varchar');
		$this->captura('total_monto','varchar');
		$this->captura('forma_pago','varchar');
		$this->captura('medio_pago','varchar');
		$this->captura('lugar','varchar');
		$this->captura('pais','varchar');
		$this->captura('estado','varchar');
		$this->captura('tipo_factura','varchar');
		$this->captura('cajero','varchar');
		$this->captura('cod_control','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
    //var_dump($this->respuesta);exit;

		//Devuelve la respuesta
		return $this->respuesta;
	}


	function listarFacturaComputarizadaCabecera(){

		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_repventa_sel';
		$this->transaccion='VF_REPFACTCABE_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		//$this->setCount(false);

		$this->setParametro('id_punto_venta','id_punto_venta','integer');
		$this->setParametro('id_concepto','id_concepto','integer');
		$this->setParametro('desde','desde','varchar');
		$this->setParametro('hasta','hasta','varchar');
		$this->setCount(false);

		$this->captura('nombre','varchar');
		$this->captura('codigo','varchar');
		$this->captura('lugar','varchar');
		$this->captura('pais','varchar');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
    //var_dump($this->respuesta);

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarFacturaConcepto(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_repventa_sel';
		$this->transaccion='VF_REPFACTCON_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		//$this->setCount(false);

		$this->setParametro('id_punto_venta','id_punto_venta','integer');
		$this->setParametro('id_concepto','id_concepto','integer');
		$this->setParametro('desde','desde','varchar');
		$this->setParametro('hasta','hasta','varchar');
		$this->setParametro('id_usuario_cajero','id_usuario_cajero','integer');
		$this->setParametro('tipo_documento','tipo_documento','varchar');
		$this->setCount(false);

		$this->captura('concepto','varchar');
		$this->captura('total_precio','numeric');
		$this->captura('nro_factura','varchar');
		$this->captura('nroaut','varchar');
		$this->captura('id_punto_venta','int4');
		$this->captura('fecha','varchar');
		$this->captura('nombre','varchar');
		$this->captura('id_venta','integer');
		$this->captura('desc_persona','varchar');
		$this->captura('codigo','varchar');
		$this->captura('tipo_factura','varchar');
		$this->captura('estado','varchar');
		$this->captura('total_venta','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
    //var_dump($this->respuesta);

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarMonedaBase(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_repventa_sel';
		$this->transaccion='VF_MONBA_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->count =false;

		//Definicion de la lista del resultado del query
		$this->setParametro('id_punto_venta','id_punto_venta','integer');

		$this->captura('moneda','varchar');
		$this->captura('nombre_pv','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();


		$this->ejecutarConsulta();
		//var_dump("AQUI LLEGA 22222",$this->respuesta);exit;
		//Devuelve la respuesta
		return $this->respuesta;
	}

}
?>
