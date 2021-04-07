<?php
/**
*@package pXP
*@file gen-MODCajero.php
*@author  (ivaldivia)
*@date 29-05-2019 19:33:10
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODCajero extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarVenta(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_facturacion_sel';
		$this->transaccion='VF_CAJA_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->setParametro('pes_estado','pes_estado','varchar');
		$this->setParametro('id_punto_venta','id_punto_venta','integer');


		//Definicion de la lista del resultado del query
		$this->captura('id_venta','int4');
		$this->captura('id_cliente','int4');
		$this->captura('id_dosificacion','int4');
		$this->captura('id_estado_wf','int4');
		$this->captura('id_proceso_wf','int4');
		$this->captura('id_punto_venta','int4');
		$this->captura('id_sucursal','int4');
		$this->captura('id_usuario_cajero','int4');
		$this->captura('id_cliente_destino','int4');
		$this->captura('transporte_fob','numeric');
		$this->captura('tiene_formula','varchar');
		$this->captura('cod_control','varchar');
		$this->captura('estado','varchar');
		$this->captura('total_venta_msuc','numeric');
		$this->captura('otros_cif','numeric');
		$this->captura('nro_factura','int4');
		$this->captura('observaciones','text');
		$this->captura('seguros_cif','numeric');
		$this->captura('comision','numeric');
		$this->captura('id_moneda','int4');
		$this->captura('id_movimiento','int4');
		$this->captura('transporte_cif','numeric');
		$this->captura('correlativo_venta','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('nro_tramite','varchar');
		$this->captura('tipo_cambio_venta','numeric');
		$this->captura('a_cuenta','numeric');
		$this->captura('contabilizable','varchar');
		$this->captura('nombre_factura','varchar');
		$this->captura('excento','numeric');
		$this->captura('valor_bruto','numeric');
		$this->captura('descripcion_bulto','varchar');
		$this->captura('id_grupo_factura','int4');
		$this->captura('fecha','date');
		$this->captura('nit','varchar');
		$this->captura('tipo_factura','varchar');
		$this->captura('seguros_fob','numeric');
		$this->captura('total_venta','numeric');
		$this->captura('forma_pedido','varchar');
		$this->captura('porcentaje_descuento','numeric');
		$this->captura('hora_estimada_entrega','time');
		$this->captura('id_vendedor_medico','varchar');
		$this->captura('otros_fob','numeric');
		$this->captura('fecha_estimada_entrega','date');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('informe','text');
		$this->captura('id_formula','int4');
		$this->captura('formato_factura_emitida','varchar');
		$this->captura('correo_electronico','varchar');
		$this->captura('cajero','varchar');
		$this->captura('nro_autorizacion','varchar');
		$this->captura('id_auxiliar_anticipo','int4');
		$this->captura('nro_deposito','varchar');
		$this->captura('fecha_deposito','date');
		$this->captura('id_moneda_venta_recibo','int4');
		//$this->captura('nombre_sucursal','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//var_dump("aqui llega data",$this->respuesta);
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarVenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_facturacion_ime';
		$this->transaccion='VF_fact_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_cliente','id_cliente','int4');
		$this->setParametro('id_dosificacion','id_dosificacion','int4');
		$this->setParametro('id_estado_wf','id_estado_wf','int4');
		$this->setParametro('id_proceso_wf','id_proceso_wf','int4');
		$this->setParametro('id_punto_venta','id_punto_venta','int4');
		$this->setParametro('id_sucursal','id_sucursal','int4');
		$this->setParametro('id_usuario_cajero','id_usuario_cajero','int4');
		$this->setParametro('id_cliente_destino','id_cliente_destino','int4');
		$this->setParametro('transporte_fob','transporte_fob','numeric');
		$this->setParametro('tiene_formula','tiene_formula','varchar');
		$this->setParametro('cod_control','cod_control','varchar');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('total_venta_msuc','total_venta_msuc','numeric');
		$this->setParametro('otros_cif','otros_cif','numeric');
		$this->setParametro('nro_factura','nro_factura','int4');
		$this->setParametro('observaciones','observaciones','text');
		$this->setParametro('seguros_cif','seguros_cif','numeric');
		$this->setParametro('comision','comision','numeric');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('id_movimiento','id_movimiento','int4');
		$this->setParametro('transporte_cif','transporte_cif','numeric');
		$this->setParametro('correlativo_venta','correlativo_venta','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nro_tramite','nro_tramite','varchar');
		$this->setParametro('tipo_cambio_venta','tipo_cambio_venta','numeric');
		$this->setParametro('a_cuenta','a_cuenta','numeric');
		$this->setParametro('contabilizable','contabilizable','varchar');
		$this->setParametro('nombre_factura','nombre_factura','varchar');
		$this->setParametro('excento','excento','numeric');
		$this->setParametro('valor_bruto','valor_bruto','numeric');
		$this->setParametro('descripcion_bulto','descripcion_bulto','varchar');
		$this->setParametro('id_grupo_factura','id_grupo_factura','int4');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('nit','nit','varchar');
		$this->setParametro('tipo_factura','tipo_factura','varchar');
		$this->setParametro('seguros_fob','seguros_fob','numeric');
		$this->setParametro('total_venta','total_venta','numeric');
		$this->setParametro('forma_pedido','forma_pedido','varchar');
		$this->setParametro('porcentaje_descuento','porcentaje_descuento','numeric');
		$this->setParametro('hora_estimada_entrega','hora_estimada_entrega','time');
		$this->setParametro('id_vendedor_medico','id_vendedor_medico','varchar');
		$this->setParametro('otros_fob','otros_fob','numeric');
		$this->setParametro('fecha_estimada_entrega','fecha_estimada_entrega','date');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarVenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_facturacion_ime';
		$this->transaccion='VF_fact_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_venta','id_venta','int4');
		$this->setParametro('id_cliente','id_cliente','int4');
		$this->setParametro('id_dosificacion','id_dosificacion','int4');
		$this->setParametro('id_estado_wf','id_estado_wf','int4');
		$this->setParametro('id_proceso_wf','id_proceso_wf','int4');
		$this->setParametro('id_punto_venta','id_punto_venta','int4');
		$this->setParametro('id_sucursal','id_sucursal','int4');
		$this->setParametro('id_usuario_cajero','id_usuario_cajero','int4');
		$this->setParametro('id_cliente_destino','id_cliente_destino','int4');
		$this->setParametro('transporte_fob','transporte_fob','numeric');
		$this->setParametro('tiene_formula','tiene_formula','varchar');
		$this->setParametro('cod_control','cod_control','varchar');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('total_venta_msuc','total_venta_msuc','numeric');
		$this->setParametro('otros_cif','otros_cif','numeric');
		$this->setParametro('nro_factura','nro_factura','int4');
		$this->setParametro('observaciones','observaciones','text');
		$this->setParametro('seguros_cif','seguros_cif','numeric');
		$this->setParametro('comision','comision','numeric');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('id_movimiento','id_movimiento','int4');
		$this->setParametro('transporte_cif','transporte_cif','numeric');
		$this->setParametro('correlativo_venta','correlativo_venta','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nro_tramite','nro_tramite','varchar');
		$this->setParametro('tipo_cambio_venta','tipo_cambio_venta','numeric');
		$this->setParametro('a_cuenta','a_cuenta','numeric');
		$this->setParametro('contabilizable','contabilizable','varchar');
		$this->setParametro('nombre_factura','nombre_factura','varchar');
		$this->setParametro('excento','excento','numeric');
		$this->setParametro('valor_bruto','valor_bruto','numeric');
		$this->setParametro('descripcion_bulto','descripcion_bulto','varchar');
		$this->setParametro('id_grupo_factura','id_grupo_factura','int4');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('nit','nit','varchar');
		$this->setParametro('tipo_factura','tipo_factura','varchar');
		$this->setParametro('seguros_fob','seguros_fob','numeric');
		$this->setParametro('total_venta','total_venta','numeric');
		$this->setParametro('forma_pedido','forma_pedido','varchar');
		$this->setParametro('porcentaje_descuento','porcentaje_descuento','numeric');
		$this->setParametro('hora_estimada_entrega','hora_estimada_entrega','time');
		$this->setParametro('id_vendedor_medico','id_vendedor_medico','varchar');
		$this->setParametro('otros_fob','otros_fob','numeric');
		$this->setParametro('fecha_estimada_entrega','fecha_estimada_entrega','date');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarVenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_facturacion_ime';
		$this->transaccion='VF_fact_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_venta','id_venta','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarVentaDetalle(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_detalle_facturacion_sel';
		$this->transaccion='VF_VEDETFACT_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		//Definicion de la lista del resultado del query
		$this->captura('id_venta_detalle','int4');
		$this->captura('id_venta','int4');
		$this->captura('id_producto','int4');
		$this->captura('id_sucursal_producto','int4');
		$this->captura('nombre_producto','varchar');
		$this->captura('precio_unitario','numeric');
		$this->captura('cantidad','numeric');
		$this->captura('precio_total','numeric');
		$this->captura('descripcion','text');
		$this->captura('tipo','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('asociar_boletos','varchar');
		$this->captura('requiere_excento','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	function siguienteEstadoFactura(){
			//Definicion de variables para ejecucion del procedimiento
			$this->procedimiento='vef.ft_venta_facturacion_ime';
			$this->transaccion='VEF_FINALIZAR_IME';
			$this->tipo_procedimiento='IME';

			//Define los parametros para la funcion
			$this->setParametro('id_estado_wf_act','id_estado_wf_act','int4');
			$this->setParametro('id_proceso_wf_act','id_proceso_wf_act','int4');
			$this->setParametro('tipo_pv','tipo_pv','varchar');
			$this->setParametro('tipo','tipo','varchar');


			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();
			//var_dump($this->respuesta);
			//Devuelve la respuesta
			return $this->respuesta;
	}
	function finalizarFacturaManual(){
			//Definicion de variables para ejecucion del procedimiento
			$this->procedimiento='vef.ft_venta_facturacion_ime';
			$this->transaccion='VEF_FINMAN_IME';
			$this->tipo_procedimiento='IME';

			//Define los parametros para la funcion
			$this->setParametro('id_estado_wf_act','id_estado_wf_act','int4');
			$this->setParametro('id_proceso_wf_act','id_proceso_wf_act','int4');
			$this->setParametro('tipo_pv','tipo_pv','varchar');
			$this->setParametro('tipo','tipo','varchar');


			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}
	function FinalizarFactura(){
			//Definicion de variables para ejecucion del procedimiento
			$this->procedimiento='vef.ft_venta_facturacion_ime';
			$this->transaccion='VEF_FINATO_IME';
			$this->tipo_procedimiento='IME';

			//Define los parametros para la funcion
			$this->setParametro('id_estado_wf_act','id_estado_wf_act','int4');
			$this->setParametro('id_proceso_wf_act','id_proceso_wf_act','int4');
			$this->setParametro('tipo_pv','tipo_pv','varchar');
			$this->setParametro('tipo','tipo','varchar');
			$this->setParametro('liquidaciones','liquidaciones','varchar');
			$this->setParametro('tipo_interfaz','tipo_interfaz','varchar');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}

	function anularFactura(){
			//Definicion de variables para ejecucion del procedimiento
			$this->procedimiento='vef.ft_venta_facturacion_ime';
			$this->transaccion='VEF_ANULAR_IME';
			$this->tipo_procedimiento='IME';

			//Define los parametros para la funcion
			$this->setParametro('id_venta','id_venta','int4');
			$this->setParametro('tipo_factura_vista','tipo_factura_vista','varchar');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}

	function regresarCounter(){
			//Definicion de variables para ejecucion del procedimiento
			$this->procedimiento='vef.ft_venta_facturacion_ime';
			$this->transaccion='VEF_REGRECOUNTER_IME';
			$this->tipo_procedimiento='IME';

			//Define los parametros para la funcion
			$this->setParametro('id_estado_wf_act','id_estado_wf_act','int4');
			$this->setParametro('id_proceso_wf_act','id_proceso_wf_act','int4');
			$this->setParametro('tipo','tipo','varchar');


			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}

	function listarFactura(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_facturacion_sel';
		$this->transaccion='VF_LISFACT_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		// $this->setParametro('id_venta','id_venta','integer');
		$this->setParametro('id_punto_venta','id_punto_venta','integer');
		$this->setParametro('tipo_factura','tipo_factura','varchar');
		$this->setParametro('id_proceso_wf','id_proceso_wf','integer');

		//Definicion de la lista del resultado del query
		$this->captura('id_venta','int4');
		$this->captura('nombre_entidad','varchar');
		$this->captura('nit','varchar');
		$this->captura('direccion_sucursal','varchar');
		$this->captura('telefono_sucursal','varchar');
		$this->captura('lugar_sucursal','varchar');
		$this->captura('departamento_sucursal','varchar');
		$this->captura('fecha_venta','varchar');
		$this->captura('nro_venta','varchar');

		$this->captura('moneda_sucursal','varchar');
		$this->captura('total_venta','numeric');
		$this->captura('sujeto_credito','numeric');
		$this->captura('total_venta_literal','varchar');
		$this->captura('observaciones','text');
		$this->captura('cliente','varchar');

		$this->captura('nombre_sucursal','varchar');//nuevo
		$this->captura('numero_factura','integer');//nuevo
		$this->captura('autorizacion','varchar');//nuevo
		$this->captura('nit_cliente','varchar');//nuevo
		$this->captura('codigo_control','varchar');//nuevo
		$this->captura('fecha_limite_emision','text');//nuevo
		$this->captura('glosa_impuestos','varchar');//nuevo
		$this->captura('glosa_empresa','varchar');//nuevo
		$this->captura('pagina_entidad','varchar');//nuevo
		$this->captura('id','integer');//nuevo
		$this->captura('hora','text');//nuevo
		$this->captura('nit_entidad','varchar');//nuevo
		$this->captura('actividades','varchar');
		$this->captura('fecha_venta_recibo','varchar');

		$this->captura('direccion_cliente','varchar');
		$this->captura('tipo_cambio_venta','numeric');
		$this->captura('total_venta_msuc','numeric');
		$this->captura('total_venta_msuc_literal','varchar');
		$this->captura('moneda_venta','varchar');//codigo
		$this->captura('desc_moneda_sucursal','varchar');//nombre
		$this->captura('desc_moneda_venta','varchar');//nombre

		$this->captura('transporte_fob','numeric');
		$this->captura('seguros_fob','numeric');
		$this->captura('otros_fob','numeric');

		$this->captura('transporte_cif','numeric');
		$this->captura('seguros_cif','numeric');
		$this->captura('otros_cif','numeric');

		$this->captura('fecha_literal','varchar');

		$this->captura('cantidad_descripciones','integer');
		$this->captura('estado','varchar');

		$this->captura('valor_bruto','numeric');
		$this->captura('descripcion_bulto','varchar');
    $this->captura('telefono_cliente','varchar');
    $this->captura('fecha_hora_entrega','varchar');
    $this->captura('a_cuenta','numeric');



    $this->captura('nro_tramite','varchar');
		$this->captura('codigo_cliente','varchar');


		$this->captura('lugar_cliente','varchar');
		$this->captura('cliente_destino','varchar');
		$this->captura('lugar_destino','varchar');
    $this->captura('codigo_sucursal','varchar');//nuevo mvm
		$this->captura('leyenda','varchar');//nuevo mvm
		$this->captura('zona','varchar');//nuevo mvm
		$this->captura('excento','numeric');//nuevo excento

		$this->captura('sucursal','varchar');//nuevo excento
		$this->captura('desc_sucursal','varchar');
			$this->captura('desc_lugar','varchar');
			$this->captura('logo','varchar');
		$this->captura('cuenta_cajero','varchar');
		$this->captura('id_cuenta','integer');
		$this->captura('tipo_factura','varchar');
		$this->captura('moneda_base','varchar');
		$this->captura('codigo_iata','varchar');
		$this->captura('fecha_ingles','varchar');
		$this->captura('forma_pago','varchar');
		$this->captura('tipo_usuario','varchar');
		$this->captura('comision','numeric');//nuevo mvm
		$this->captura('hora_estimada_entrega','time');//nuevo mvm
		//$this->captura('codigo_moneda','varchar');//nuevo mvm
		//$this->captura('fecha_ingles','varchar');//nuevo mvm
		//$this->captura('forma_pago','varchar');//nuevo mvm
		//$this->captura('codigo_iata','varchar');//nuevo mvm

        //Ejecuta la instruccion
		$this->armarConsulta();


		$this->ejecutarConsulta();
		//var_dump($this->respuesta);
		return $this->respuesta;
	}

	function listarFacturaDescripcion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_facturacion_sel';
		$this->transaccion='VF_REDESCRIP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		//$this->setParametro('id_venta','id_venta','integer');
		$this->setParametro('id_proceso_wf','id_proceso_wf','integer');
		//Definicion de la lista del resultado del query
		$this->captura('nombre','varchar');
		$this->captura('columna','numeric');
		$this->captura('fila','numeric');
		$this->captura('valor','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();

		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarFacturaDetalle(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_facturacion_sel';
		$this->transaccion='VF_FACDETALLE_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		//$this->setParametro('id_venta','id_venta','integer');
		$this->setParametro('id_proceso_wf','id_proceso_wf','integer');
		//Definicion de la lista del resultado del query
		$this->captura('concepto','varchar');
		$this->captura('cantidad','numeric');
		$this->captura('precio_unitario','numeric');
		$this->captura('precio_total','numeric');
		$this->captura('unidad_medida','varchar');
		$this->captura('cod_producto','varchar');
		$this->captura('nandina','varchar');
		$this->captura('bruto','varchar');
		$this->captura('ley','varchar');
		$this->captura('kg_fino','varchar');
		$this->captura('descripcion','text');
		$this->captura('unidad_concepto','varchar');
		$this->captura('precio_grupo','numeric');



		//Ejecuta la instruccion
		$this->armarConsulta();

		$this->ejecutarConsulta();
		//var_dump($this->respuesta);
		//Devuelve la respuesta
		return $this->respuesta;
	}
	function getTipoUsuario(){
			//Definicion de variables para ejecucion del procedimientp
			$this->procedimiento='vef.ft_venta_facturacion_ime';
			$this->transaccion='VF_TIPO_USUARIO_IME';
			$this->tipo_procedimiento='IME';//tipo de transaccion

			$this->setParametro('vista','vista','varchar');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}
	function listarInstanciaPago(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_facturacion_sel';
		$this->transaccion='VF_LIST_INST_PA';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_venta','id_venta','integer');

		//Definicion de la lista del resultado del query
		$this->captura('id_medio_pago','int4');
		$this->captura('nombre','varchar');
		$this->captura('codigo_tarjeta','varchar');
		$this->captura('numero_tarjeta','varchar');
		$this->captura('monto_transaccion','numeric');
		$this->captura('id_moneda','int4');
		$this->captura('id_venta_forma_pago','int4');
		$this->captura('id_auxiliar','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();

		$this->ejecutarConsulta();
		//var_dump("aqui llega data",$this->respuesta);
		//Devuelve la respuesta
		return $this->respuesta;
	}


	function listarInstanciaPagoRecibos(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_facturacion_sel';
		$this->transaccion='VF_LIST_INST_PA_RO';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_venta','id_venta','integer');

		//Definicion de la lista del resultado del query
		$this->captura('id_medio_pago','int4');
		$this->captura('nombre','varchar');
		$this->captura('codigo_tarjeta','varchar');
		$this->captura('numero_tarjeta','varchar');
		$this->captura('monto_transaccion','numeric');
		$this->captura('id_moneda','int4');
		$this->captura('id_venta_forma_pago','int4');
		$this->captura('deposito','varchar');
		$this->captura('id_auxiliar','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();

		$this->ejecutarConsulta();
		//var_dump("aqui llega data",$this->respuesta);
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarCasaMatriz(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_facturacion_sel';
		$this->transaccion='VF_LISCASAMAT_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		//Definicion de la lista del resultado del query
		$this->captura('nombre_casa_matriz','varchar');
		$this->captura('codigo_casa_matriz','varchar');
		$this->captura('direccion_casa_matriz','varchar');
		$this->captura('telefono_casa_matriz','varchar');
		$this->captura('lugar_casa_matriz','varchar');

		$this->armarConsulta();


		$this->ejecutarConsulta();
		//var_dump($this->respuesta);
		return $this->respuesta;
	}

	function getConceptoAsociar(){
			//Definicion de variables para ejecucion del procedimientp
			$this->procedimiento='vef.ft_venta_facturacion_ime';
			$this->transaccion='VF_CONBOLE_IME';
			$this->tipo_procedimiento='IME';//tipo de transaccion

			$this->setParametro('id_venta','id_venta','int4');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}

		function listarInstanciaPagoCorreccion(){
			//Definicion de variables para ejecucion del procedimientp
			$this->procedimiento='vef.ft_venta_facturacion_sel';
			$this->transaccion='VF_LFPCOR_SEL';
			$this->tipo_procedimiento='SEL';//tipo de transaccio

			$this->setParametro('id_venta','id_venta','integer');

			//Definicion de la lista del resultado del query
			$this->captura('id_medio_pago_pw','int4');
			$this->captura('name','varchar');
			$this->captura('codigo_tarjeta','varchar');
			$this->captura('numero_tarjeta','varchar');
			$this->captura('monto_forma_pago','numeric');
			$this->captura('id_moneda','int4');
			$this->captura('id_venta_forma_pago','int4');
			$this->captura('id_venta','int4');
			$this->captura('desc_moneda','varchar');
			$this->captura('fop_code','varchar');
			$this->captura('id_auxiliar','int4');
			$this->captura('nombre_auxiliar','varchar');
			$this->captura('codigo_auxiliar','varchar');
			$this->captura('nro_deposito','varchar');
			$this->captura('fecha_deposito','date');
			$this->captura('monto_deposito','numeric');
			$this->captura('mco','varchar');
			//Ejecuta la instruccion
			$this->armarConsulta();
			// echo $this->consulta;exit;
			$this->ejecutarConsulta();
			//Devuelve la respuesta
			return $this->respuesta;
		}

}
?>
