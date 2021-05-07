<?php
/**
*@package pXP
*@file gen-MODFacturaExportacion.php
*@author  (ivaldivia)
*@date 21-04-2021 09:33:10
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODFacturaExportacion extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}


	function listarFacturaExportacion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_facturacion_exportacion_sel';
		$this->transaccion='VF_FACTEXPOR_SEL';
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
		$this->captura('id_moneda_venta','int4');
		$this->captura('id_movimiento','int4');
		$this->captura('transporte_cif','numeric');
		$this->captura('correlativo_venta','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('nro_tramite','varchar');
		$this->captura('tipo_cambio','numeric');
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
		//$this->captura('id_formula','int4');
		$this->captura('formato_factura_emitida','varchar');
		$this->captura('correo_electronico','varchar');
		$this->captura('cajero','varchar');
		$this->captura('nro_autorizacion','varchar');
		$this->captura('id_auxiliar_anticipo','int4');
		$this->captura('nro_deposito','varchar');
		$this->captura('fecha_deposito','date');
		$this->captura('id_moneda_venta_recibo','int4');
		$this->captura('direccion_cliente','varchar');
		$this->captura('desc_moneda','varchar');
		//$this->captura('nombre_sucursal','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//var_dump("aqui llega data",$this->respuesta);
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarDetalleFacturacionExportacion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_facturacion_exportacion_sel';
		$this->transaccion='VF_FACTDETEXP_SEL';
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
		$this->captura('id_concepto_ingas','int4');
		$this->captura('desc_ingas','varchar');
		$this->captura('requiere_descripcion','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function datosEmpresa(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_facturacion_exportacion_sel';
		$this->transaccion='VF_FACT_EMPR_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);
		//Definicion de la lista del resultado del query
		$this->captura('nit','varchar');
		$this->captura('nombre','varchar');
		$this->captura('logo','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//var_dump("aqui llega data",$this->respuesta);
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function datosCabeceraFactura(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_facturacion_exportacion_sel';
		$this->transaccion='VF_FACT_CAB_EXPO_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_venta','id_venta','integer');

		//Definicion de la lista del resultado del query
		$this->captura('lugar','varchar');
		$this->captura('fecha_literal','varchar');
		$this->captura('nombre_factura','varchar');
		$this->captura('direccion_cliente','varchar');
		$this->captura('nit','varchar');
		$this->captura('incoterm','varchar');
		$this->captura('moneda_venta','varchar');
		$this->captura('tipo_cambio_venta','numeric');
		$this->captura('nro_factura','numeric');
		$this->captura('nro_autorizacion','varchar');
		$this->captura('actividad_economica','varchar');
		$this->captura('codigo_sucursal','varchar');
		$this->captura('nombre_sucursal','varchar');
		$this->captura('direccion_sucursal','varchar');
		$this->captura('telefono_sucursal','varchar');
		$this->captura('lugar_sucursal','varchar');

		$this->captura('valor_bruto','numeric');
		$this->captura('transporte_fob','numeric');
		$this->captura('seguros_fob','numeric');
		$this->captura('otros_fob','numeric');
		$this->captura('transporte_cif','numeric');
		$this->captura('seguros_cif','numeric');
		$this->captura('otros_cif','numeric');
		$this->captura('codigo','varchar');
		$this->captura('totales_fob','numeric');
		$this->captura('totales_cif','numeric');

		$this->captura('fecha_limite_dosificacion','varchar');
		$this->captura('cod_control','varchar');

		$this->captura('glosa_impuestos','varchar');
		$this->captura('glosa_empresa','varchar');
		$this->captura('hora_estimada_entrega','varchar');
		$this->captura('cuenta_cajero','varchar');
		$this->captura('leyenda','varchar');
		$this->captura('id_venta','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//var_dump("aqui llega data",$this->respuesta);
		//Devuelve la respuesta
		return $this->respuesta;
	}


	function datosDetalleFactura(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_facturacion_exportacion_sel';
		$this->transaccion='VF_FACT_DET_EXPO_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_venta','id_venta','integer');

		//Definicion de la lista del resultado del query
		$this->captura('descripcion_reporte','varchar');
		$this->captura('cantidad','numeric');
		$this->captura('precio','numeric');
		$this->captura('unidad_medida','varchar');
		$this->captura('subtotal','numeric');
		$this->captura('nandina','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
	//	var_dump("aqui llega data",$this->respuesta);
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function totalesFactura(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_facturacion_exportacion_sel';
		$this->transaccion='VF_FACT_TOT_EXPO_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_venta','id_venta','integer');

		//Definicion de la lista del resultado del query
		$this->captura('total_local','numeric');
		$this->captura('total_extranjera','numeric');
		$this->captura('total','numeric');
		$this->captura('codigo_moneda_local','varchar');
		$this->captura('moneda_local','varchar');
		$this->captura('codigo_moneda_extranjera','varchar');
		$this->captura('moneda_extranjera','varchar');
		$this->captura('total_literal_local','varchar');
		$this->captura('total_literal_extranjera','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
	//	var_dump("aqui llega data",$this->respuesta);
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarCabeceraExportacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_facturacion_exportacion_ime';
		$this->transaccion='VF_FACTEXPOR_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_cliente','id_cliente','varchar');
		$this->setParametro('id_punto_venta','id_punto_venta','int4');
		$this->setParametro('id_sucursal','id_sucursal','int4');
		$this->setParametro('id_usuario_cajero','id_usuario_cajero','int4');
		$this->setParametro('cod_control','cod_control','varchar');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('nro_factura','nro_factura','int4');
		$this->setParametro('observaciones','observaciones','text');
		$this->setParametro('id_moneda_venta','id_moneda_venta','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('tipo_cambio','tipo_cambio','numeric');
		$this->setParametro('nombre_factura','nombre_factura','varchar');
		$this->setParametro('nit','nit','varchar');
		$this->setParametro('direccion_cliente','direccion_cliente','varchar');
		$this->setParametro('id_formula','id_formula','integer');
		$this->setParametro('tipo_factura','tipo_factura','varchar');
		//$this->setParametro('nombre_factura','nombre_factura','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarCabeceraExportacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_facturacion_exportacion_ime';
		$this->transaccion='VF_FACTEXPOR_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_venta','id_venta','int4');
		$this->setParametro('id_cliente','id_cliente','varchar');
		$this->setParametro('observaciones','observaciones','text');
		$this->setParametro('nombre_factura','nombre_factura','varchar');
		$this->setParametro('nit','nit','varchar');
		$this->setParametro('id_formula','id_formula','int4');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}


	function insertarVentaFacturacionExportacionDetalle(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_facturacion_exportacion_ime';
		$this->transaccion='VF_FACEXP_DET_INS';
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

	function modificarVentaFacturacionExportacionDetalle(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_facturacion_exportacion_ime';
		$this->transaccion='VF_FACEXP_DET_MOD';
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


	function insertarVentaFacturacionExportacionFormasPago(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_facturacion_exportacion_ime';
		$this->transaccion='VF_FACEXP_FP_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_venta_forma_pago','id_venta_forma_pago','int4');
		$this->setParametro('id_medio_pago','id_medio_pago','int4');
		$this->setParametro('id_venta','id_venta','int4');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('codigo_tarjeta','codigo_tarjeta','varchar');
		$this->setParametro('num_tarjeta','num_tarjeta','varchar');
		$this->setParametro('monto_forma_pago','monto_forma_pago','numeric');
		$this->setParametro('id_auxiliar','id_auxiliar','numeric');
		$this->setParametro('mco','mco','varchar');

		/*Aqui para el update en la cabecera*/
		$this->setParametro('valor_bruto','valor_bruto','numeric');
		$this->setParametro('transporte_fob','transporte_fob','numeric');
		$this->setParametro('seguros_fob','seguros_fob','numeric');
		$this->setParametro('otros_fob','otros_fob','numeric');
		$this->setParametro('total_fob','total_fob','numeric');
		$this->setParametro('transporte_cif','transporte_cif','numeric');
		$this->setParametro('seguros_cif','seguros_cif','numeric');
		$this->setParametro('otros_cif','otros_cif','numeric');
		/************************************/


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function getTipoCambio(){
			//Definicion de variables para ejecucion del procedimientp
			$this->procedimiento='vef.ft_venta_facturacion_exportacion_ime';
			$this->transaccion='VF_TC_EXPO_IME';
			$this->tipo_procedimiento='IME';//tipo de transaccion

			$this->setParametro('fecha_cambio','fecha_cambio','timestamp');
			$this->setParametro('id_moneda_pais','id_moneda_pais','integer');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}

	function getTipoCambioConcepto(){
			//Definicion de variables para ejecucion del procedimientp
			$this->procedimiento='vef.ft_venta_facturacion_exportacion_ime';
			$this->transaccion='VF_TC_CON_EXPO_IME';
			$this->tipo_procedimiento='IME';//tipo de transaccion

			$this->setParametro('fecha_cambio','fecha_cambio','timestamp');
			$this->setParametro('id_moneda','id_moneda','integer');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}



	function insertarVentaExportacion(){
        //Abre conexion con PDO
        $cone = new conexion();
        $link = $cone->conectarpdo();
        $copiado = false;

  try {
        $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $link->beginTransaction();

        /////////////////////////
        //  inserta cabecera de la solicitud de compra
        ///////////////////////

      //Definicion de variables para ejecucion del procedimiento
      $this->procedimiento = 'vef.ft_emision_facturacion_exportacion_ime';
      $this->tipo_procedimiento = 'IME';

			if ($this->aParam->getParametro('id_venta') != '') {
				//Eliminar formas de pago
				$this->transaccion = 'VF_EXP_FOP_ELI';
				$this->setParametro('id_venta','id_venta','int4');
				//Ejecuta la instruccion
        $this->armarConsulta();
        $stmt = $link->prepare($this->consulta);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        //recupera parametros devuelto depues de insertar ... (id_formula)
        $resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
        if ($resp_procedimiento['tipo_respuesta']=='ERROR') {
            throw new Exception("Error al ejecutar en la bd", 3);
        }

			//Eliminar detalles
			$this->transaccion = 'VF_EXP_DET_ELI';

			//Ejecuta la instruccion
      $this->armarConsulta();
      $stmt = $link->prepare($this->consulta);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

     // recupera parametros devuelto depues de insertar ... (id_formula)
      $resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
      if ($resp_procedimiento['tipo_respuesta']=='ERROR') {
          throw new Exception("Error al ejecutar en la bd", 3);
      }


				$this->transaccion = 'VF_EXP_MOD';
			} else {
				$this->transaccion = 'VF_EXP_VEN_INS';
			}
            //Define los parametros para la funcion
			$this->setParametro('tipo_factura','tipo_factura','varchar');
			$this->setParametro('nit','nit','varchar');
			$this->setParametro('nombre_factura','nombre_factura','varchar');
			$this->setParametro('id_cliente','id_cliente','varchar');
			$this->setParametro('direccion_cliente','direccion_cliente','varchar');
			$this->setParametro('observaciones','observaciones','text');
			$this->setParametro('id_moneda_venta','id_moneda_venta','int4');
			$this->setParametro('tipo_cambio','tipo_cambio','numeric');
			$this->setParametro('valor_bruto','valor_bruto','numeric');
			$this->setParametro('transporte_fob','transporte_fob','numeric');
			$this->setParametro('seguros_fob','seguros_fob','numeric');
			$this->setParametro('otros_fob','otros_fob','numeric');
			$this->setParametro('transporte_cif','transporte_cif','numeric');
			$this->setParametro('seguros_cif','seguros_cif','numeric');
			$this->setParametro('otros_cif','otros_cif','numeric');
			$this->setParametro('id_moneda','id_moneda','int4');
			$this->setParametro('id_medio_pago','id_medio_pago','int4');
			$this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
			$this->setParametro('mco','mco','varchar');
			$this->setParametro('codigo_tarjeta','codigo_tarjeta','varchar');
			$this->setParametro('id_auxiliar','id_auxiliar','integer');
			$this->setParametro('monto_forma_pago','monto_forma_pago','numeric');
			$this->setParametro('id_moneda_2','id_moneda_2','int4');
			$this->setParametro('id_medio_pago_2','id_medio_pago_2','int4');
			$this->setParametro('id_auxiliar_2','id_auxiliar_2','integer');
			$this->setParametro('numero_tarjeta_2','numero_tarjeta_2','varchar');
			$this->setParametro('mco_2','mco_2','varchar');
			$this->setParametro('codigo_tarjeta_2','codigo_tarjeta_2','varchar');
			$this->setParametro('monto_forma_pago_2','monto_forma_pago_2','numeric');

			$this->setParametro('id_sucursal','id_sucursal','int4');
			$this->setParametro('id_punto_venta','id_punto_venta','int4');


      //Ejecuta la instruccion
      $this->armarConsulta();
      $stmt = $link->prepare($this->consulta);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      //recupera parametros devuelto depues de insertar ... (id_formula)
      $resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
      if ($resp_procedimiento['tipo_respuesta']=='ERROR') {
          throw new Exception("Error al ejecutar en la bd", 3);
      }

      $respuesta = $resp_procedimiento['datos'];

      $id_venta = $respuesta['id_venta'];

      //decodifica JSON  de detalles
      $json_detalle = $this->aParam->_json_decode($this->aParam->getParametro('json_new_records'));

            foreach($json_detalle as $f){

                $this->resetParametros();
                //Definicion de variables para ejecucion del procedimiento
                $this->procedimiento='vef.ft_emision_facturacion_exportacion_ime';
                $this->transaccion='VF_EXP_DET_INS';
                $this->tipo_procedimiento='IME';
                //modifica los valores de las variables que mandaremos
								$this->arreglo['id_producto'] = $f['id_producto'];
                $this->arreglo['id_sucursal_producto'] = $f['id_sucursal_producto'];
                $this->arreglo['tipo'] = $f['tipo'];
                $this->arreglo['estado_reg'] = $f['estado_reg'];
                $this->arreglo['cantidad'] = $f['cantidad'];
                $this->arreglo['precio'] = $f['precio_unitario'];
                $this->arreglo['sw_porcentaje_formula'] = $f['sw_porcentaje_formula'];
                $this->arreglo['porcentaje_descuento'] = $f['porcentaje_descuento'];
                $this->arreglo['id_vendedor_medico'] = $f['id_vendedor_medico'];
								$this->arreglo['descripcion'] = $f['descripcion'];
                $this->arreglo['id_venta'] = $id_venta;

								$this->arreglo['bruto'] = $f['bruto'];
								$this->arreglo['ley'] = $f['ley'];
								$this->arreglo['kg_fino'] = $f['kg_fino'];
								$this->arreglo['id_unidad_medida'] = $f['id_unidad_medida'];

                //Define los parametros para la funcion
                $this->setParametro('id_venta','id_venta','int4');
                $this->setParametro('id_item','id_item','int4');
								$this->setParametro('id_producto','id_producto','int4');
                $this->setParametro('id_sucursal_producto','id_sucursal_producto','int4');
                $this->setParametro('id_formula','id_formula','int4');
                $this->setParametro('tipo','tipo','varchar');
                $this->setParametro('estado_reg','estado_reg','varchar');
                $this->setParametro('cantidad_det','cantidad','numeric');
                $this->setParametro('precio','precio','numeric');
                $this->setParametro('sw_porcentaje_formula','sw_porcentaje_formula','varchar');
                $this->setParametro('porcentaje_descuento','porcentaje_descuento','int4');
                $this->setParametro('id_vendedor_medico','id_vendedor_medico','varchar');
								$this->setParametro('descripcion','descripcion','text');
								$this->setParametro('id_unidad_medida','id_unidad_medida','int4');
								$this->setParametro('bruto','bruto','varchar');
								$this->setParametro('ley','ley','varchar');
								$this->setParametro('kg_fino','kg_fino','varchar');
								$this->setParametro('tipo_factura','tipo_factura','varchar');

                //Ejecuta la instruccion
                $this->armarConsulta();
                $stmt = $link->prepare($this->consulta);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                //recupera parametros devuelto depues de insertar ... (id_formula)
                $resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
                if ($resp_procedimiento['tipo_respuesta']=='ERROR') {
                    throw new Exception("Error al insertar detalle  en la bd", 3);
                }

            }

			// if ($this->aParam->getParametro('id_forma_pago') == '0') {
			// 	//decodifica JSON  de forma de pago
	    //         $json_detalle = $this->aParam->_json_decode($this->aParam->getParametro('formas_pago'));
			//
	    //         //var_dump("DATOS IRVA JSON",$json_detalle);
	    //         foreach($json_detalle as $f){
			//
	    //             $this->resetParametros();
	    //             //Definicion de variables para ejecucion del procedimiento
	    //             $this->procedimiento='vef.ft_venta_forma_pago_ime';
	    //             $this->transaccion='VF_VENFP_INS';
	    //             $this->tipo_procedimiento='IME';
	    //             //modifica los valores de las variables que mandaremos
	    //             $this->arreglo['id_forma_pago'] = $f['id_forma_pago'];
	    //             $this->arreglo['valor'] = $f['valor'];
			// 						$this->arreglo['numero_tarjeta'] = $f['numero_tarjeta'];
			// 						$this->arreglo['codigo_tarjeta'] = $f['codigo_tarjeta'];
			// 						$this->arreglo['tipo_tarjeta'] = $f['tipo_tarjeta'];
			// 						$this->arreglo['id_auxiliar'] = $f['id_auxiliar'];
	    //             $this->arreglo['id_venta'] = $id_venta;
			//
	    //             //Define los parametros para la funcion
	    //             $this->setParametro('id_venta','id_venta','int4');
	    //             $this->setParametro('id_forma_pago','id_forma_pago','int4');
			// 						$this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
			// 						$this->setParametro('codigo_tarjeta','codigo_tarjeta','varchar');
			// 						$this->setParametro('tipo_tarjeta','tipo_tarjeta','varchar');
	    //             $this->setParametro('valor','valor','numeric');
	    //             $this->setParametro('id_auxiliar','id_auxiliar','int4');
			// 						$this->setParametro('tipo_factura','tipo_factura','varchar');
			//
	    //             //Ejecuta la instruccion
	    //             $this->armarConsulta();
	    //             $stmt = $link->prepare($this->consulta);
	    //             $stmt->execute();
	    //             $result = $stmt->fetch(PDO::FETCH_ASSOC);
			//
	    //             //recupera parametros devuelto depues de insertar ... (id_formula)
	    //             $resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
	    //             if ($resp_procedimiento['tipo_respuesta']=='ERROR') {
	    //                 throw new Exception("Error al insertar detalle  en la bd", 3);
	    //             }
			//
	    //         }
	    //     }

			// if($this->aParam->getParametro('nombre_vista')!='FormVentaCounter') {
			// 	$this->resetParametros();
			// 	//Validar que todo este ok
			// 	$this->procedimiento = 'vef.ft_venta_facturacion_ime';
			// 	$this->transaccion = 'VF_FACVALI_MOD';
			// 	$this->setParametro('id_venta', 'id_venta', 'int4');
			// 	$this->setParametro('tipo_factura', 'tipo_factura', 'varchar');
			// 	$this->setParametro('tipo', 'tipo', 'varchar');
			// 	$this->setParametro('excento', 'excento', 'varchar');
			// 	$this->setParametro('boleto_asociado','boleto_asociado','varchar');
			// 	//Ejecuta la instruccion
			// 	$this->armarConsulta();
			// 	$stmt = $link->prepare($this->consulta);
			// 	$stmt->execute();
			// 	$result = $stmt->fetch(PDO::FETCH_ASSOC);
			//
			// 	//recupera parametros devuelto depues de insertar ... (id_formula)
			// 	$resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
			// 	$respuesta = $resp_procedimiento['datos'];
			//
			//
			// 	if ($resp_procedimiento['tipo_respuesta'] == 'ERROR') {
			// 		throw new Exception("Error al ejecutar en la bd", 3);
			// 	}
			// }

            //si todo va bien confirmamos y regresamos el resultado
            $link->commit();
            $this->respuesta=new Mensaje();
            $this->respuesta->setMensaje($resp_procedimiento['tipo_respuesta'],$this->nombre_archivo,$resp_procedimiento['mensaje'],$resp_procedimiento['mensaje_tec'],'base',$this->procedimiento,$this->transaccion,$this->tipo_procedimiento,$this->consulta);
            $this->respuesta->setDatos($respuesta);
        }
        catch (Exception $e) {
                $link->rollBack();
                $this->respuesta=new Mensaje();
                if ($e->getCode() == 3) {//es un error de un procedimiento almacenado de pxp
                    $this->respuesta->setMensaje($resp_procedimiento['tipo_respuesta'],$this->nombre_archivo,$resp_procedimiento['mensaje'],$resp_procedimiento['mensaje_tec'],'base',$this->procedimiento,$this->transaccion,$this->tipo_procedimiento,$this->consulta);
                } else if ($e->getCode() == 2) {//es un error en bd de una consulta
                    $this->respuesta->setMensaje('ERROR',$this->nombre_archivo,$e->getMessage(),$e->getMessage(),'modelo','','','','');
                } else {//es un error lanzado con throw exception
                    throw new Exception($e->getMessage(), 2);
                }

        }

        return $this->respuesta;
    }


		function EmitirFacturaExportacion(){
				//Definicion de variables para ejecucion del procedimiento
				$this->procedimiento='vef.ft_emision_facturacion_exportacion_ime';
				$this->transaccion='VEF_FIN_EXP_IME';
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


		function insertarFormula(){
        //Definicion de variables para ejecucion del procedimiento
				$this->procedimiento='vef.ft_venta_facturacion_exportacion_ime';
				$this->transaccion='VF_EXP_FORM_INS';
				$this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
				$this->setParametro('id_formula','id_formula','integer');
        $this->setParametro('id_moneda_venta','id_moneda_venta','integer');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

		function RecuperarCliente(){
			//Definicion de variables para ejecucion del procedimiento
			$this->procedimiento='vef.ft_venta_facturacion_exportacion_ime';
			$this->transaccion='VEF_CLIEXPO_MOD';
			$this->tipo_procedimiento='IME';

			//Define los parametros para la funcion
			$this->setParametro('nit','nit','varchar');
			$this->setParametro('razon_social','razon_social','varchar');
			//Ejecuta la instruccion
			$this->armarConsulta();

			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
		}

		function listarFormasPagoExportacion(){
			//Definicion de variables para ejecucion del procedimientp
			$this->procedimiento='vef.ft_venta_facturacion_exportacion_sel';
			$this->transaccion='VF_EXP_FP_LIST_SEL';
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
			$this->captura('mco','varchar');
			$this->captura('usr_reg','varchar');
			$this->captura('usr_mod','varchar');
			$this->captura('fecha_reg','timestamp');
			$this->captura('fecha_mod','timestamp');
			//Ejecuta la instruccion
			$this->armarConsulta();
			// echo $this->consulta;exit;
			$this->ejecutarConsulta();
			//Devuelve la respuesta
			return $this->respuesta;
		}


}
?>
