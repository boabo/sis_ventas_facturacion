<?php
/**
*@package pXP
*@file gen-MODVentaFacturacion.php
*@author  (ivaldivia)
*@date 10-05-2019 19:08:47
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODVentaFacturacion extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarVentaFacturacion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_facturacion_sel';
		$this->transaccion='VF_fact_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->setParametro('pes_estado','pes_estado','varchar');

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
		$this->captura('requiere_excento','varchar');
		$this->captura('excento_verificado','varchar');
		$this->captura('nombre','varchar');
		$this->captura('id_formula','int4');
		$this->captura('nro_pnr','varchar');
		$this->captura('id_auxiliar_anticipo','int4');
		$this->captura('nombre_auxiliar','varchar');
		$this->captura('codigo_auxiliar','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		// echo ($this->consulta);exit;
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarVentaFacturacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_facturacion_ime';
		$this->transaccion='VF_fact_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_cliente','id_cliente','varchar');
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
		$this->setParametro('id_formula','id_formula','integer');

		if ($this->arreglo['id_producto'] != null && $this->arreglo['id_producto'] != ''){
			$this->setParametro('nro_pnr','nro_pnr','varchar');
			$this->setParametro('descripcion','descripcion','text');
			$this->setParametro('cantidad_det','cantidad','numeric');
			$this->setParametro('tipo','tipo','varchar');
			$this->setParametro('id_producto','id_producto','int4');
			$this->setParametro('precio','precio','numeric');
			$this->setParametro('total','total','numeric');
			$this->setParametro('id_moneda_venta_recibo','id_moneda_venta_recibo','int4');
			$this->setParametro('monto_exacto','monto_exacto','numeric');
			$this->setParametro('id_auxiliar_anticipo','id_auxiliar_anticipo','int4');
			$this->setParametro('data_pnr','data_pnr','varchar');			
		}

		//$this->setParametro('nombre_factura','nombre_factura','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarVentaFacturacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_facturacion_ime';
		$this->transaccion='VF_fact_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_venta','id_venta','int4');
		$this->setParametro('id_cliente','id_cliente','varchar');
		$this->setParametro('observaciones','observaciones','text');
		$this->setParametro('nombre_factura','nombre_factura','varchar');
		$this->setParametro('nit','nit','varchar');
		$this->setParametro('id_formula','id_formula','int4');
		$this->setParametro('id_auxiliar_anticipo','id_auxiliar_anticipo','int4');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarVentaFacturacion(){
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

	function obtenerApertura(){
			//Definicion de variables para ejecucion del procedimientp
			$this->procedimiento='vef.ft_venta_facturacion_ime';
			$this->transaccion='VF_APERTURA_IME';
			$this->tipo_procedimiento='IME';//tipo de transaccion

			$this->setParametro('id_punto_venta','id_punto_venta','varchar');
			$this->setParametro('id_sucursal','id_sucursal','varchar');
			$this->setParametro('fecha_apertura','fecha_apertura','varchar');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}
	function obtenerAperturaCounter(){
			//Definicion de variables para ejecucion del procedimientp
			$this->procedimiento='vef.ft_venta_facturacion_ime';
			$this->transaccion='VF_APERCOUN_IME';
			$this->tipo_procedimiento='IME';//tipo de transaccion

			$this->setParametro('id_punto_venta','id_punto_venta','varchar');
			$this->setParametro('id_sucursal','id_sucursal','varchar');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}

	function siguienteEstadoRecibo(){
			//Definicion de variables para ejecucion del procedimiento
			$this->procedimiento='vef.ft_venta_facturacion_ime';
			$this->transaccion='VEF_ENVIARCAJA_IME';
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

	function insertarVentaCompleta(){
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
            $this->procedimiento = 'vef.ft_venta_facturacion_ime';

            $this->tipo_procedimiento = 'IME';
			//var_dump("el ID ES",$this->aParam->getParametro('id_venta'));
			if ($this->aParam->getParametro('id_venta') != '') {
				//Eliminar formas de pago
				$this->transaccion = 'VF_FACFORPA_ELI';
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
			$this->transaccion = 'VF_VENDET_ELI';

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
				$this->transaccion = 'VF_FACTU_MOD';
			} else {
				$this->transaccion = 'VF_FACVEN_INS';
			}
            //Define los parametros para la funcion
            $this->setParametro('id_cliente','id_cliente','varchar');
						$this->setParametro('nit','nit','varchar');
            $this->setParametro('id_sucursal','id_sucursal','int4');
            $this->setParametro('nro_tramite','nro_tramite','varchar');
            $this->setParametro('a_cuenta','a_cuenta','numeric');
            $this->setParametro('total_venta','total_venta','numeric');
            $this->setParametro('fecha_estimada_entrega','fecha_estimada_entrega','date');
			$this->setParametro('id_punto_venta','id_punto_venta','int4');
			$this->setParametro('id_forma_pago','id_forma_pago','int4');
			$this->setParametro('id_forma_pago_2','id_forma_pago_2','int4');
			/*Aumentando la instancia de pago*/
			$this->setParametro('id_medio_pago','id_medio_pago','int4');
			$this->setParametro('id_medio_pago_2','id_medio_pago_2','int4');
			$this->setParametro('id_moneda_2','id_moneda_2','int4');
			/********************************/

			$this->setParametro('monto_forma_pago','monto_forma_pago','numeric');
			$this->setParametro('monto_forma_pago_2','monto_forma_pago_2','numeric');
			$this->setParametro('mco','mco','varchar');
			$this->setParametro('mco_2','mco_2','varchar');
			$this->setParametro('id_auxiliar','id_auxiliar','integer');
			$this->setParametro('id_auxiliar_2','id_auxiliar_2','integer');

			$this->setParametro('numero_tarjeta_2','numero_tarjeta_2','varchar');
			$this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
			$this->setParametro('codigo_tarjeta','codigo_tarjeta','varchar');
			$this->setParametro('codigo_tarjeta_2','codigo_tarjeta_2','varchar');
			$this->setParametro('tipo_tarjeta','tipo_tarjeta','varchar');
			$this->setParametro('tipo_tarjeta_2','tipo_tarjeta_2','varchar');
            $this->setParametro('porcentaje_descuento','porcentaje_descuento','integer');
            $this->setParametro('id_vendedor_medico','id_vendedor_medico','varchar');
			$this->setParametro('comision','comision','numeric');
			$this->setParametro('observaciones','observaciones','text');

			$this->setParametro('tipo_factura','tipo_factura','varchar');
			$this->setParametro('fecha','fecha','date');
            $this->setParametro('nro_factura','nro_factura','varchar');
			$this->setParametro('id_dosificacion','id_dosificacion','integer');
			$this->setParametro('excento','excento','numeric');

			$this->setParametro('id_moneda','id_moneda','int4');
			$this->setParametro('tipo_cambio_venta','tipo_cambio_venta','numeric');
			$this->setParametro('total_venta_msuc','total_venta_msuc','numeric');
			$this->setParametro('transporte_fob','transporte_fob','numeric');
			$this->setParametro('seguros_fob','seguros_fob','numeric');
			$this->setParametro('otros_fob','otros_fob','numeric');
			$this->setParametro('transporte_cif','transporte_cif','numeric');
			$this->setParametro('seguros_cif','seguros_cif','numeric');
			$this->setParametro('otros_cif','otros_cif','numeric');
			$this->setParametro('valor_bruto','valor_bruto','numeric');
			$this->setParametro('descripcion_bulto','descripcion_bulto','varchar');
			$this->setParametro('id_cliente_destino','id_cliente_destino','varchar');
			$this->setParametro('hora_estimada_entrega','hora_estimada_entrega','varchar');
			$this->setParametro('forma_pedido','forma_pedido','varchar');

			/*Aumentando mejora para facturacion enviar correo y registrar el tipo de formato emitido*/
			//$this->setParametro('formato_factura','formato_factura','varchar');
			$this->setParametro('enviar_correo','enviar_correo','varchar');
			$this->setParametro('correo_electronico','correo_electronico','varchar');
			$this->setParametro('nombre_factura','nombre_factura','varchar');
			$this->setParametro('boleto_asociado','boleto_asociado','varchar');
			$this->setParametro('liquidacion','liquidacion','varchar');
			$this->setParametro('total_suma','total_suma','numeric');
			$this->setParametro('tipo_interfaz','tipo_interfaz','varchar');
			$this->setParametro('id_venta_recibo','id_venta_recibo','int4');
			$this->setParametro('id_venta_recibo_2','id_venta_recibo_2','int4');
			$this->setParametro('saldo_recibo','saldo_recibo','numeric');
			$this->setParametro('saldo_recibo_2','saldo_recibo_2','numeric');
			/****************************************************************************************/

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
                $this->procedimiento='vef.ft_venta_detalle_facturacion_ime';
                $this->transaccion='VF_FACTDET_INS';
                $this->tipo_procedimiento='IME';
                //modifica los valores de las variables que mandaremos
                $this->arreglo['id_item'] = $f['id_item'];
								$this->arreglo['id_producto'] = $f['id_producto'];
                $this->arreglo['id_sucursal_producto'] = $f['id_sucursal_producto'];
              //  $this->arreglo['id_formula'] = $f['id_formula'];
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

			if ($this->aParam->getParametro('id_forma_pago') == '0') {
				//decodifica JSON  de forma de pago
	            $json_detalle = $this->aParam->_json_decode($this->aParam->getParametro('formas_pago'));

	            //var_dump("DATOS IRVA JSON",$json_detalle);
	            foreach($json_detalle as $f){

	                $this->resetParametros();
	                //Definicion de variables para ejecucion del procedimiento
	                $this->procedimiento='vef.ft_venta_forma_pago_ime';
	                $this->transaccion='VF_VENFP_INS';
	                $this->tipo_procedimiento='IME';
	                //modifica los valores de las variables que mandaremos
	                $this->arreglo['id_forma_pago'] = $f['id_forma_pago'];
	                $this->arreglo['valor'] = $f['valor'];
									$this->arreglo['numero_tarjeta'] = $f['numero_tarjeta'];
									$this->arreglo['codigo_tarjeta'] = $f['codigo_tarjeta'];
									$this->arreglo['tipo_tarjeta'] = $f['tipo_tarjeta'];
									$this->arreglo['id_auxiliar'] = $f['id_auxiliar'];
	                $this->arreglo['id_venta'] = $id_venta;

	                //Define los parametros para la funcion
	                $this->setParametro('id_venta','id_venta','int4');
	                $this->setParametro('id_forma_pago','id_forma_pago','int4');
									$this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
									$this->setParametro('codigo_tarjeta','codigo_tarjeta','varchar');
									$this->setParametro('tipo_tarjeta','tipo_tarjeta','varchar');
	                $this->setParametro('valor','valor','numeric');
	                $this->setParametro('id_auxiliar','id_auxiliar','int4');
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
	        }

			if($this->aParam->getParametro('nombre_vista')!='FormVentaCounter') {
				$this->resetParametros();
				//Validar que todo este ok
				$this->procedimiento = 'vef.ft_venta_facturacion_ime';
				$this->transaccion = 'VF_FACVALI_MOD';
				$this->setParametro('id_venta', 'id_venta', 'int4');
				$this->setParametro('tipo_factura', 'tipo_factura', 'varchar');
				$this->setParametro('tipo', 'tipo', 'varchar');
				$this->setParametro('excento', 'excento', 'varchar');
				$this->setParametro('boleto_asociado','boleto_asociado','varchar');
				//Ejecuta la instruccion
				$this->armarConsulta();
				$stmt = $link->prepare($this->consulta);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);

				//recupera parametros devuelto depues de insertar ... (id_formula)
				$resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
				$respuesta = $resp_procedimiento['datos'];


				if ($resp_procedimiento['tipo_respuesta'] == 'ERROR') {
					throw new Exception("Error al ejecutar en la bd", 3);
				}
			}

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

	function insertarFacturacionManual(){
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
            $this->procedimiento = 'vef.ft_venta_facturacion_ime';

            $this->tipo_procedimiento = 'IME';
			//var_dump("el ID ES",$this->aParam->getParametro('id_venta'));
			if ($this->aParam->getParametro('id_venta') != '') {
				//Eliminar formas de pago
				$this->transaccion = 'VF_FACFORPA_ELI';
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
			$this->transaccion = 'VF_VENDET_ELI';

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
				$this->transaccion = 'VF_FACTU_MOD';
			} else {
				$this->transaccion = 'VF_FACVEN_INS';
			}

            //Define los parametros para la funcion
            $this->setParametro('id_cliente','id_cliente','varchar');
			$this->setParametro('nit','nit','varchar');
            $this->setParametro('id_sucursal','id_sucursal','int4');
            $this->setParametro('nro_tramite','nro_tramite','varchar');
            $this->setParametro('a_cuenta','a_cuenta','numeric');
            $this->setParametro('total_venta','total_venta','numeric');
            $this->setParametro('fecha_estimada_entrega','fecha_estimada_entrega','date');
			$this->setParametro('id_punto_venta','id_punto_venta','int4');
			$this->setParametro('id_forma_pago','id_forma_pago','int4');
			$this->setParametro('id_forma_pago_2','id_forma_pago_2','int4');
			$this->setParametro('monto_forma_pago','monto_forma_pago','numeric');
			$this->setParametro('monto_forma_pago_2','monto_forma_pago_2','numeric');
			/*Aumentando la instancia de pago*/
			$this->setParametro('id_medio_pago','id_medio_pago','int4');
			$this->setParametro('id_medio_pago_2','id_medio_pago_2','int4');
			$this->setParametro('id_moneda_2','id_moneda_2','int4');
			/********************************/
			$this->setParametro('mco','mco','varchar');
			$this->setParametro('mco_2','mco_2','varchar');
			$this->setParametro('id_auxiliar','id_auxiliar','integer');
			$this->setParametro('id_auxiliar_2','id_auxiliar_2','integer');

			$this->setParametro('numero_tarjeta_2','numero_tarjeta_2','varchar');
			$this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
			$this->setParametro('codigo_tarjeta','codigo_tarjeta','varchar');
			$this->setParametro('codigo_tarjeta_2','codigo_tarjeta_2','varchar');
			$this->setParametro('tipo_tarjeta','tipo_tarjeta','varchar');
			$this->setParametro('tipo_tarjeta_2','tipo_tarjeta_2','varchar');
            $this->setParametro('porcentaje_descuento','porcentaje_descuento','integer');
            $this->setParametro('id_vendedor_medico','id_vendedor_medico','varchar');
			$this->setParametro('comision','comision','numeric');
			$this->setParametro('observaciones','observaciones','text');

			$this->setParametro('tipo_factura','tipo_factura','varchar');
			$this->setParametro('fecha','fecha','date');
            $this->setParametro('nro_factura','nro_factura','varchar');
			$this->setParametro('id_dosificacion','id_dosificacion','integer');
			$this->setParametro('excento','excento','numeric');

			$this->setParametro('id_moneda','id_moneda','int4');
			$this->setParametro('tipo_cambio_venta','tipo_cambio_venta','numeric');
			$this->setParametro('total_venta_msuc','total_venta_msuc','numeric');
			$this->setParametro('transporte_fob','transporte_fob','numeric');
			$this->setParametro('seguros_fob','seguros_fob','numeric');
			$this->setParametro('otros_fob','otros_fob','numeric');
			$this->setParametro('transporte_cif','transporte_cif','numeric');
			$this->setParametro('seguros_cif','seguros_cif','numeric');
			$this->setParametro('otros_cif','otros_cif','numeric');
			$this->setParametro('valor_bruto','valor_bruto','numeric');
			$this->setParametro('descripcion_bulto','descripcion_bulto','varchar');
			$this->setParametro('id_cliente_destino','id_cliente_destino','varchar');
			$this->setParametro('hora_estimada_entrega','hora_estimada_entrega','varchar');
			$this->setParametro('forma_pedido','forma_pedido','varchar');
			$this->setParametro('informe','informe','text');
			$this->setParametro('anulado','anulado','varchar');
			$this->setParametro('nombre_factura','nombre_factura','varchar');
			$this->setParametro('boleto_asociado','boleto_asociado','varchar');


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
                $this->procedimiento='vef.ft_venta_detalle_facturacion_ime';
                $this->transaccion='VF_FACTDET_INS';
                $this->tipo_procedimiento='IME';
                //modifica los valores de las variables que mandaremos
                $this->arreglo['id_item'] = $f['id_item'];
                $this->arreglo['id_producto'] = $f['id_producto'];
              //  $this->arreglo['id_formula'] = $f['id_formula'];
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

			if ($this->aParam->getParametro('id_forma_pago') == '0') {
				//decodifica JSON  de forma de pago
	            $json_detalle = $this->aParam->_json_decode($this->aParam->getParametro('formas_pago'));

	            //var_dump($json_detalle)   ;
	            foreach($json_detalle as $f){

	                $this->resetParametros();
	                //Definicion de variables para ejecucion del procedimiento
	                $this->procedimiento='vef.ft_venta_forma_pago_ime';
	                $this->transaccion='VF_VENFP_INS';
	                $this->tipo_procedimiento='IME';
	                //modifica los valores de las variables que mandaremos
	                $this->arreglo['id_forma_pago'] = $f['id_forma_pago'];
	                $this->arreglo['valor'] = $f['valor'];
									$this->arreglo['numero_tarjeta'] = $f['numero_tarjeta'];
									$this->arreglo['codigo_tarjeta'] = $f['codigo_tarjeta'];
									$this->arreglo['tipo_tarjeta'] = $f['tipo_tarjeta'];
									$this->arreglo['id_auxiliar'] = $f['id_auxiliar'];
	                $this->arreglo['id_venta'] = $id_venta;

	                //Define los parametros para la funcion
	                $this->setParametro('id_venta','id_venta','int4');
	                $this->setParametro('id_forma_pago','id_forma_pago','int4');
									$this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
									$this->setParametro('codigo_tarjeta','codigo_tarjeta','varchar');
									$this->setParametro('tipo_tarjeta','tipo_tarjeta','varchar');
	                $this->setParametro('valor','valor','numeric');
	                $this->setParametro('id_auxiliar','id_auxiliar','int4');
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
	        }

			if($this->aParam->getParametro('nombre_vista')!='FormVentaCounter') {
				$this->resetParametros();
				//Validar que todo este ok
				$this->procedimiento = 'vef.ft_venta_facturacion_ime';
				$this->transaccion = 'VF_FACVALI_MOD';

				$this->arreglo['id_venta'] = $id_venta;

				$this->setParametro('id_venta', 'id_venta', 'int4');
				$this->setParametro('tipo_factura', 'tipo_factura', 'varchar');
				$this->setParametro('tipo', 'tipo', 'varchar');
				$this->setParametro('anulado', 'anulado', 'varchar');
				$this->setParametro('excento', 'excento', 'varchar');
				$this->setParametro('boleto_asociado','boleto_asociado','varchar');
				//Ejecuta la instruccion
				$this->armarConsulta();
				$stmt = $link->prepare($this->consulta);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);

				//recupera parametros devuelto depues de insertar ... (id_formula)
				$resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
				$respuesta = $resp_procedimiento['datos'];


				if ($resp_procedimiento['tipo_respuesta'] == 'ERROR') {
					throw new Exception("Error al ejecutar en la bd", 3);
				}
			}

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

		/*Funcion para Insertar los recibos manuales (Ismael Valdivia 17/09/2020)*/
		function insertarReciboManual(){
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
		        $this->procedimiento = 'vef.ft_venta_ime';
		        $this->tipo_procedimiento = 'IME';
						//var_dump("el ID ES",$this->aParam->getParametro('id_venta'));
						if ($this->aParam->getParametro('id_venta') != '') {
							//Eliminar formas de pago
							$this->transaccion = 'VF_VEALLFORPA_ELI';
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
							$this->transaccion = 'VF_VEALLDET_ELI';
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
							$this->transaccion = 'VF_VEN_MOD';
						} else {
							$this->transaccion = 'VF_VEN_INS';
						}
	            //Define los parametros para la funcion
	            $this->setParametro('id_cliente','id_cliente','varchar');
							$this->setParametro('nit','nit','varchar');
	            $this->setParametro('id_sucursal','id_sucursal','int4');
	            $this->setParametro('nro_tramite','nro_tramite','varchar');
	            $this->setParametro('a_cuenta','a_cuenta','numeric');
	            $this->setParametro('total_venta','total_venta','numeric');
	            $this->setParametro('fecha_estimada_entrega','fecha_estimada_entrega','date');
							$this->setParametro('id_punto_venta','id_punto_venta','int4');
							$this->setParametro('id_forma_pago','id_forma_pago','int4');
							$this->setParametro('id_forma_pago_2','id_forma_pago_2','int4');
							$this->setParametro('monto_forma_pago','monto_forma_pago','numeric');
							$this->setParametro('monto_forma_pago_2','monto_forma_pago_2','numeric');
							/*Aumentando la instancia de pago*/
							$this->setParametro('id_medio_pago','id_medio_pago','int4');
							$this->setParametro('id_medio_pago_2','id_medio_pago_2','int4');
							$this->setParametro('id_moneda_2','id_moneda_2','int4');
							/********************************/
							$this->setParametro('mco','mco','varchar');
							$this->setParametro('mco_2','mco_2','varchar');
							$this->setParametro('id_auxiliar','id_auxiliar','integer');
							$this->setParametro('id_auxiliar_2','id_auxiliar_2','integer');

							$this->setParametro('numero_tarjeta_2','numero_tarjeta_2','varchar');
							$this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
							$this->setParametro('codigo_tarjeta','codigo_tarjeta','varchar');
							$this->setParametro('codigo_tarjeta_2','codigo_tarjeta_2','varchar');
							$this->setParametro('tipo_tarjeta','tipo_tarjeta','varchar');
							$this->setParametro('tipo_tarjeta_2','tipo_tarjeta_2','varchar');
				      $this->setParametro('porcentaje_descuento','porcentaje_descuento','integer');
				      $this->setParametro('id_vendedor_medico','id_vendedor_medico','varchar');
							$this->setParametro('comision','comision','numeric');
							$this->setParametro('observaciones','observaciones','text');

							$this->setParametro('tipo_factura','tipo_factura','varchar');
							$this->setParametro('fecha','fecha','date');
				      $this->setParametro('nro_factura','nro_factura','varchar');
							$this->setParametro('id_dosificacion','id_dosificacion','integer');
							$this->setParametro('excento','excento','numeric');

							$this->setParametro('id_moneda','id_moneda','int4');
							$this->setParametro('tipo_cambio_venta','tipo_cambio_venta','numeric');
							$this->setParametro('total_venta_msuc','total_venta_msuc','numeric');
							$this->setParametro('transporte_fob','transporte_fob','numeric');
							$this->setParametro('seguros_fob','seguros_fob','numeric');
							$this->setParametro('otros_fob','otros_fob','numeric');
							$this->setParametro('transporte_cif','transporte_cif','numeric');
							$this->setParametro('seguros_cif','seguros_cif','numeric');
							$this->setParametro('otros_cif','otros_cif','numeric');
							$this->setParametro('valor_bruto','valor_bruto','numeric');
							$this->setParametro('descripcion_bulto','descripcion_bulto','varchar');
							$this->setParametro('id_cliente_destino','id_cliente_destino','varchar');
							$this->setParametro('hora_estimada_entrega','hora_estimada_entrega','varchar');
							$this->setParametro('forma_pedido','forma_pedido','varchar');
							$this->setParametro('anulado','anulado','varchar');

							/*Aumentando para registrar el deposito*/
							$this->setParametro('id_deposito', 'id_deposito', 'integer');
							$this->setParametro('nro_deposito', 'nro_deposito', 'varchar');
							$this->setParametro('monto_deposito', 'monto_deposito', 'numeric');
							$this->setParametro('fecha_deposito', 'fecha_deposito', 'varchar');
							/***************************************/
							/*********Mandamos la moneda del recibo (18/08/2020 Ismael Valdivia)********/
							$this->setParametro('id_moneda_venta_recibo', 'id_moneda_venta_recibo', 'integer');
							$this->setParametro('id_auxiliar_anticipo', 'id_auxiliar_anticipo', 'integer');
							/***********************************************************************************/

							/*Aumentando para registrar correo electronico en MIAMI (Ismael Valdivia 14/10/2020)*/
							$this->setParametro('correo_electronico', 'correo_electronico', 'varchar');
							$this->setParametro('nombre_factura','nombre_factura', 'varchar');
							$this->setParametro('id_venta_recibo', 'id_venta_recibo', 'int4');
							$this->setParametro('id_venta_recibo_2', 'id_venta_recibo_2', 'int4');
							$this->setParametro('saldo_recibo', 'saldo_recibo', 'numeric');
							$this->setParametro('saldo_recibo_2', 'saldo_recibo_2', 'numeric');
							/***********************************************************************************/


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
			                $this->procedimiento='vef.ft_venta_detalle_ime';
			                $this->transaccion='VF_VEDET_INS';
			                $this->tipo_procedimiento='IME';
			                //modifica los valores de las variables que mandaremos
			                $this->arreglo['id_item'] = $f['id_item'];
			                $this->arreglo['id_producto'] = $f['id_producto'];
			              //  $this->arreglo['id_formula'] = $f['id_formula'];
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

						if ($this->aParam->getParametro('id_forma_pago') == '0') {
							//decodifica JSON  de forma de pago
				            $json_detalle = $this->aParam->_json_decode($this->aParam->getParametro('formas_pago'));

				            //var_dump($json_detalle)   ;
				            foreach($json_detalle as $f){

				                $this->resetParametros();
				                //Definicion de variables para ejecucion del procedimiento
				                $this->procedimiento='vef.ft_venta_forma_pago_ime';
				                $this->transaccion='VF_VENFP_INS';
				                $this->tipo_procedimiento='IME';
				                //modifica los valores de las variables que mandaremos
				                $this->arreglo['id_forma_pago'] = $f['id_forma_pago'];
				                $this->arreglo['valor'] = $f['valor'];
												$this->arreglo['numero_tarjeta'] = $f['numero_tarjeta'];
												$this->arreglo['codigo_tarjeta'] = $f['codigo_tarjeta'];
												$this->arreglo['tipo_tarjeta'] = $f['tipo_tarjeta'];
												$this->arreglo['id_auxiliar'] = $f['id_auxiliar'];
				                $this->arreglo['id_venta'] = $id_venta;

				                //Define los parametros para la funcion
				                $this->setParametro('id_venta','id_venta','int4');
				                $this->setParametro('id_forma_pago','id_forma_pago','int4');
												$this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
												$this->setParametro('codigo_tarjeta','codigo_tarjeta','varchar');
												$this->setParametro('tipo_tarjeta','tipo_tarjeta','varchar');
				                $this->setParametro('valor','valor','numeric');
				                $this->setParametro('id_auxiliar','id_auxiliar','int4');
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
				        }

						if($this->aParam->getParametro('nombre_vista')!='FormVentaCounter') {
							$this->resetParametros();
							//Validar que todo este ok
							$this->procedimiento = 'vef.ft_venta_ime';
							$this->transaccion = 'VF_VENVALI_MOD';

							$this->arreglo['id_venta'] = $id_venta;

							$this->setParametro('id_venta', 'id_venta', 'int4');
							$this->setParametro('tipo_factura', 'tipo_factura', 'varchar');
							$this->setParametro('tipo', 'tipo', 'varchar');
							$this->setParametro('anulado', 'anulado', 'varchar');
							//Ejecuta la instruccion
							$this->armarConsulta();
							$stmt = $link->prepare($this->consulta);
							$stmt->execute();
							$result = $stmt->fetch(PDO::FETCH_ASSOC);

							//recupera parametros devuelto depues de insertar ... (id_formula)
							$resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
							$respuesta = $resp_procedimiento['datos'];


							if ($resp_procedimiento['tipo_respuesta'] == 'ERROR') {
								throw new Exception("Error al ejecutar en la bd", 3);
							}
						}

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
		/*************************************************************************/

		/*Aumentando para corregir las formas de pago*/
		function corregirFactura(){
			//Definicion de variables para ejecucion del procedimiento
			$this->procedimiento='vef.ft_venta_facturacion_ime';
			$this->transaccion='VF_FACT_CORRE';
			$this->tipo_procedimiento='IME';

			//Define los parametros para la funcion
			$this->setParametro('id_venta','id_venta','int4');
			$this->setParametro('id_moneda','id_moneda','int4');
			$this->setParametro('id_moneda_2','id_moneda_2','int4');
			$this->setParametro('id_venta_forma_pago_1','id_venta_forma_pago_1','int4');
			$this->setParametro('id_venta_forma_pago_2','id_venta_forma_pago_2','int4');
			$this->setParametro('id_medio_pago','id_medio_pago','int4');
			$this->setParametro('id_medio_pago_2','id_medio_pago_2','int4');
			$this->setParametro('codigo_tarjeta','codigo_tarjeta','varchar');
			$this->setParametro('codigo_tarjeta_2','codigo_tarjeta_2','varchar');
			$this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
			$this->setParametro('numero_tarjeta_2','numero_tarjeta_2','varchar');
			$this->setParametro('id_auxiliar','id_auxiliar','numeric');
			$this->setParametro('id_auxiliar_2','id_auxiliar_2','numeric');
			$this->setParametro('monto_forma_pago','monto_forma_pago','numeric');
			$this->setParametro('monto_forma_pago_2','monto_forma_pago_2','numeric');
			$this->setParametro('mco','mco','varchar');
			$this->setParametro('mco_2','mco_2','varchar');
			$this->setParametro('tipo_tarjeta','tipo_tarjeta','varchar');
			$this->setParametro('tipo_tarjeta_2','tipo_tarjeta_2','varchar');
			$this->setParametro('id_venta_recibo','id_venta_recibo','int4');
			$this->setParametro('id_venta_recibo_2','id_venta_recibo_2','int4');
			$this->setParametro('saldo_recibo','saldo_recibo','numeric');
			$this->setParametro('saldo_recibo_2','saldo_recibo_2','numeric');
			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
		}

		function corregirReciboOficial(){
			//Definicion de variables para ejecucion del procedimiento
			$this->procedimiento='vef.ft_venta_facturacion_ime';
			$this->transaccion='VF_ROS_CORRE';
			$this->tipo_procedimiento='IME';

			//Define los parametros para la funcion
			$this->setParametro('id_venta','id_venta','int4');
			$this->setParametro('id_moneda','id_moneda','int4');
			$this->setParametro('id_moneda_2','id_moneda_2','int4');
			$this->setParametro('id_venta_forma_pago_1','id_venta_forma_pago_1','int4');
			$this->setParametro('id_venta_forma_pago_2','id_venta_forma_pago_2','int4');
			$this->setParametro('id_medio_pago','id_medio_pago','int4');
			$this->setParametro('id_medio_pago_2','id_medio_pago_2','int4');
			$this->setParametro('codigo_tarjeta','codigo_tarjeta','varchar');
			$this->setParametro('codigo_tarjeta_2','codigo_tarjeta_2','varchar');
			$this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
			$this->setParametro('numero_tarjeta_2','numero_tarjeta_2','varchar');
			$this->setParametro('id_auxiliar','id_auxiliar','numeric');
			$this->setParametro('id_auxiliar_2','id_auxiliar_2','numeric');
			$this->setParametro('monto_forma_pago','monto_forma_pago','numeric');
			$this->setParametro('monto_forma_pago_2','monto_forma_pago_2','numeric');
			$this->setParametro('mco','mco','varchar');
			$this->setParametro('mco_2','mco_2','varchar');
			$this->setParametro('tipo_tarjeta','tipo_tarjeta','varchar');
			$this->setParametro('tipo_tarjeta_2','tipo_tarjeta_2','varchar');

			/*Los Nuevos campos*/
			$this->setParametro('nro_deposito', 'nro_deposito', 'varchar');
			$this->setParametro('monto_deposito', 'monto_deposito', 'numeric');
			$this->setParametro('fecha_deposito', 'fecha_deposito', 'varchar');
			$this->setParametro('id_moneda_venta_recibo', 'id_moneda_venta_recibo', 'integer');
			$this->setParametro('id_auxiliar_anticipo', 'id_auxiliar_anticipo', 'integer');
			$this->setParametro('id_venta_recibo','id_venta_recibo','int4');
			$this->setParametro('id_venta_recibo_2','id_venta_recibo_2','int4');
			$this->setParametro('saldo_recibo','saldo_recibo','numeric');
			$this->setParametro('saldo_recibo_2','saldo_recibo_2','numeric');
			/***********************************************************************************/




			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
		}




		/****************************************************************************/
		/******Aumentando para listar boletos*******/
		function listarAsociarBoletos(){
			//Definicion de variables para ejecucion del procedimiento
			$this->procedimiento='vef.ft_asociar_boletos_facturas_sel';
			$this->transaccion='VF_LISBOLETOS_SEL';
			$this->tipo_procedimiento='SEL';

			//Define los parametros para la funcion
			$this->captura('nro_boleto','varchar');
			$this->captura('id_boleto','int4');
			$this->captura('estado_reg','varchar');
			$this->captura('fecha_emision','date');
			$this->captura('pasajero','varchar');
			$this->captura('nit','varchar');
			$this->captura('ruta','varchar');
			$this->captura('razon','varchar');
			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
		}
		/***************************************************************************/

		function ObtenerCuentaBancaria(){
				//Definicion de variables para ejecucion del procedimientp
				$this->procedimiento='vef.ft_venta_facturacion_ime';
				$this->transaccion='VF_CUENBANDEP_IME';
				$this->tipo_procedimiento='IME';//tipo de transaccion

				$this->setParametro('id_punto_venta','id_punto_venta','integer');
				$this->setParametro('id_sucursal','id_sucursal','integer');
				$this->setParametro('id_moneda','id_moneda','integer');

				//Ejecuta la instruccion
				$this->armarConsulta();
				$this->ejecutarConsulta();

				//Devuelve la respuesta
				return $this->respuesta;
		}

		function verificarDeposito(){
				//Definicion de variables para ejecucion del procedimientp
				$this->procedimiento='vef.ft_venta_facturacion_ime';
				$this->transaccion='VF_DEPVERI_IME';
				$this->tipo_procedimiento='IME';//tipo de transaccion

				$this->setParametro('nro_deposito','nro_deposito','varchar');
				$this->setParametro('id_moneda','id_moneda','integer');
				$this->setParametro('fecha','fecha','varchar');

				//Ejecuta la instruccion
				$this->armarConsulta();
				$this->ejecutarConsulta();

				//Devuelve la respuesta
				return $this->respuesta;
		}

		function verificarDosificacionRoManual(){
				//Definicion de variables para ejecucion del procedimientp
				$this->procedimiento='vef.ft_venta_facturacion_ime';
				$this->transaccion='VF_VERIDOSMAN_IME';
				$this->tipo_procedimiento='IME';//tipo de transaccion

				$this->setParametro('fecha_apertura','fecha_apertura','varchar');
				$this->setParametro('id_punto_venta','id_punto_venta','integer');

				//Ejecuta la instruccion
				$this->armarConsulta();
				$this->ejecutarConsulta();

				//Devuelve la respuesta
				return $this->respuesta;
		}

		function insertarDosificacionRoManual(){
				//Definicion de variables para ejecucion del procedimientp
				$this->procedimiento='vef.ft_venta_facturacion_ime';
				$this->transaccion='VF_INSDOSMAN_IME';
				$this->tipo_procedimiento='IME';//tipo de transaccion

				$this->setParametro('numero_inicial','numero_inicial','varchar');
				$this->setParametro('numero_final','numero_final','varchar');
				$this->setParametro('id_punto_venta','id_punto_venta','integer');
				$this->setParametro('fecha_apertura','fecha_apertura','varchar');

				//Ejecuta la instruccion
				$this->armarConsulta();
				$this->ejecutarConsulta();

				//Devuelve la respuesta
				return $this->respuesta;
		}

		function insertarFormasPago(){
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
							if ($this->objParam->getParametro('id_venta') == '' || $this->objParam->getParametro('id_venta') == null) {
								//Definicion de variables para ejecucion del procedimiento
		            $this->procedimiento = 'vef.ft_venta_facturacion_ime';
		            $this->tipo_procedimiento = 'IME';
								$this->transaccion = 'VF_FPINS_INS';

		            //Define los parametros para la funcion
								$this->setParametro('nit','nit','varchar');
		            $this->setParametro('id_cliente','id_cliente','varchar');
		            $this->setParametro('observaciones','observaciones','text');
		            $this->setParametro('excento','excento','numeric');
		            $this->setParametro('id_sucursal','id_sucursal','int4');
								$this->setParametro('id_punto_venta','id_punto_venta','int4');
								$this->setParametro('total_venta','total_venta','numeric');
								$this->setParametro('tipo_factura','tipo_factura','varchar');
								$this->setParametro('moneda_recibo','moneda_recibo','int4');
								$this->setParametro('id_auxiliar_anticipo','id_auxiliar_anticipo','int4');
								$this->setParametro('nro_factura','nro_factura','int4');
								$this->setParametro('id_dosificacion','id_dosificacion','int4');
								$this->setParametro('informe','informe','varchar');
								$this->setParametro('fecha_factura','fecha_factura','varchar');
								$this->setParametro('id_formula','id_formula','integer');
								$this->setParametro('nombre_factura','nombre_factura', 'varchar');
								//$this->setParametro('asociar_boletos','asociar_boletos','varchar');
								//$this->setParametro('id_venta','id_venta','integer');

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
								$tipo_factura = $this->objParam->getParametro('tipo_factura');
								$id_formula = $this->objParam->getParametro('id_formula');

								$json_detalle = $this->aParam->_json_decode($this->aParam->getParametro('json_detalle_venta'));

		            foreach($json_detalle as $d){
		                $this->resetParametros();
		                //Definicion de variables para ejecucion del procedimiento
		                $this->procedimiento='vef.ft_venta_detalle_facturacion_ime';
		                $this->transaccion='VF_FPDET_INS';
		                $this->tipo_procedimiento='IME';
		                //modifica los valores de las variables que mandaremos
		                $this->arreglo['id_producto'] = $d['id_producto'];
										$this->arreglo['id_formula'] = $id_formula;
		                $this->arreglo['tipo'] = $d['tipo'];
		                $this->arreglo['precio_unitario'] = $d['precio_unitario'];
		                $this->arreglo['cantidad'] = $d['cantidad'];
		                $this->arreglo['precio'] = $d['precio_unitario'];
		                $this->arreglo['precio_total'] = $d['precio_total'];
										$this->arreglo['nombre_producto'] = $d['nombre_producto'];
		                $this->arreglo['descripcion'] = $d['descripcion'];
										$this->arreglo['id_venta'] = $id_venta;
										$this->arreglo['tipo_factura'] = $tipo_factura;



		                //Define los parametros para la funcion
										$this->setParametro('id_producto','id_producto','int4');
		                $this->setParametro('id_formula','id_formula','int4');
		                $this->setParametro('tipo','tipo','varchar');
										$this->setParametro('precio','precio','numeric');
										$this->setParametro('cantidad_det','cantidad','numeric');
		                $this->setParametro('precio_total','precio_total','numeric');
		                $this->setParametro('nombre_producto','nombre_producto','varchar');
										$this->setParametro('descripcion','descripcion','text');
										$this->setParametro('id_venta','id_venta','int4');
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

								//decodifica JSON  de detalles
							 $json_pago = $this->aParam->_json_decode($this->aParam->getParametro('json_formas_pago'));

						 //  var_dump('detalle',$json_detalle)   ;
							 foreach($json_pago as $p){
									 //var_dump("aqui llega las n formas de pago",$p);
									 $this->resetParametros();

									 $this->procedimiento='vef.ft_venta_forma_pago_ime';
									 $this->transaccion='VF_NFP_INS';
									 $this->tipo_procedimiento='IME';
									 //modifica los valores de las variables que mandaremos
									 $this->arreglo['id_moneda'] = $p['id_moneda'];
									 $this->arreglo['id_medio_pago'] = $p['id_medio_pago'];
									 $this->arreglo['id_auxiliar'] = $p['id_auxiliar'];
									 $this->arreglo['num_tarjeta'] = $p['num_tarjeta'];
									 $this->arreglo['codigo_autorizacion'] = $p['codigo_autorizacion'];
									 $this->arreglo['mco'] = $p['mco'];
									 $this->arreglo['monto_total_local'] = $p['monto_total_local'];
									 $this->arreglo['monto_total_extranjero'] = $p['monto_total_extranjero'];
									 $this->arreglo['id_venta'] = $id_venta;
									 $this->arreglo['tipo_factura'] = $tipo_factura;
									 $this->arreglo['id_venta_recibo'] = $p['id_venta_recibo'];


									 //Define los parametros para la funcion
									 $this->setParametro('id_venta','id_venta','int4');
									 $this->setParametro('id_moneda','id_moneda','int4');
									 $this->setParametro('id_medio_pago','id_medio_pago','int4');
									 $this->setParametro('id_auxiliar','id_auxiliar','int4');
									 $this->setParametro('num_tarjeta','num_tarjeta','varchar');
									 $this->setParametro('codigo_autorizacion','codigo_autorizacion','varchar');
									 $this->setParametro('mco','mco','varchar');
									 $this->setParametro('monto_total_local','monto_total_local','numeric');
									 $this->setParametro('monto_total_extranjero','monto_total_extranjero','numeric');
									 $this->setParametro('tipo_factura','tipo_factura','varchar');
									 $this->setParametro('id_venta_recibo','id_venta_recibo','int4');
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


							} else {

								$this->resetParametros();
								$id_venta = $this->objParam->getParametro('id_venta');

								$this->procedimiento = 'vef.ft_venta_forma_pago_ime';
								$this->transaccion = 'VF_FACFPELI_DEL';
								$this->tipo_procedimiento='IME';

								 $this->arreglo['id_venta'] = $id_venta;


								$this->setParametro('id_venta', 'id_venta', 'int4');

								$this->armarConsulta();
								$stmt = $link->prepare($this->consulta);
								$stmt->execute();
								$result = $stmt->fetch(PDO::FETCH_ASSOC);

								$resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);

								if ($resp_procedimiento['tipo_respuesta'] == 'ERROR') {
									throw new Exception("Error al ejecutar en la bd", 3);
								}

								$id_venta = $this->objParam->getParametro('id_venta');
								$tipo_factura = $this->objParam->getParametro('tipo_factura');
								$id_formula = $this->objParam->getParametro('id_formula');

								//decodifica JSON  de detalles
							 $json_pago = $this->aParam->_json_decode($this->aParam->getParametro('json_formas_pago'));

						 //  var_dump('detalle',$json_detalle)   ;
							 foreach($json_pago as $p){
									 //var_dump("aqui llega las n formas de pago",$p);
									 $this->resetParametros();

									 $this->procedimiento='vef.ft_venta_forma_pago_ime';
									 $this->transaccion='VF_NFP_INS';
									 $this->tipo_procedimiento='IME';
									 //modifica los valores de las variables que mandaremos
									 $this->arreglo['id_moneda'] = $p['id_moneda'];
									 $this->arreglo['id_medio_pago'] = $p['id_medio_pago'];
									 $this->arreglo['id_auxiliar'] = $p['id_auxiliar'];
									 $this->arreglo['num_tarjeta'] = $p['num_tarjeta'];
									 $this->arreglo['codigo_autorizacion'] = $p['codigo_autorizacion'];
									 $this->arreglo['mco'] = $p['mco'];
									 $this->arreglo['monto_total_local'] = $p['monto_total_local'];
									 $this->arreglo['monto_total_extranjero'] = $p['monto_total_extranjero'];
									 $this->arreglo['id_venta'] = $id_venta;
									 $this->arreglo['tipo_factura'] = $tipo_factura;
									 $this->arreglo['id_venta_recibo'] = $p['id_venta_recibo'];


									 //Define los parametros para la funcion
									 $this->setParametro('id_venta','id_venta','int4');
									 $this->setParametro('id_moneda','id_moneda','int4');
									 $this->setParametro('id_medio_pago','id_medio_pago','int4');
									 $this->setParametro('id_auxiliar','id_auxiliar','int4');
									 $this->setParametro('num_tarjeta','num_tarjeta','varchar');
									 $this->setParametro('codigo_autorizacion','codigo_autorizacion','varchar');
									 $this->setParametro('mco','mco','varchar');
									 $this->setParametro('monto_total_local','monto_total_local','numeric');
									 $this->setParametro('monto_total_extranjero','monto_total_extranjero','numeric');
									 $this->setParametro('tipo_factura','tipo_factura','varchar');
									 $this->setParametro('id_venta_recibo','id_venta_recibo','int4');
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

							}

							$this->resetParametros();
							//Validar que todo este ok
							$this->procedimiento = 'vef.ft_venta_forma_pago_ime';
							$this->transaccion = 'VF_FACFPVALI_MOD';
							$this->setParametro('id_venta', 'id_venta', 'int4');
							$this->setParametro('tipo_factura','tipo_factura','varchar');
							$this->setParametro('asociar_boletos','asociar_boletos','varchar');
							//Ejecuta la instruccion
							$this->armarConsulta();
							$stmt = $link->prepare($this->consulta);
							$stmt->execute();
							$result = $stmt->fetch(PDO::FETCH_ASSOC);

							//recupera parametros devuelto depues de insertar ... (id_formula)
							$resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
							$respuesta = $resp_procedimiento['datos'];


							if ($resp_procedimiento['tipo_respuesta'] == 'ERROR') {
								throw new Exception("Error al ejecutar en la bd", 3);
							}




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

			function insertarPermisosSucursal(){
				//Definicion de variables para ejecucion del procedimiento
				$this->procedimiento='vef.ft_permisos_sucursales_ime';
				$this->transaccion='VEF_SUCPERMISOS_INS';
				$this->tipo_procedimiento='IME';

				//Define los parametros para la funcion
				$this->setParametro('id_funcionario','id_funcionario','int4');

				//Ejecuta la instruccion
				$this->armarConsulta();
				$this->ejecutarConsulta();

				//Devuelve la respuesta
				return $this->respuesta;
			}

			function modificarPermisosSucursal(){
				//Definicion de variables para ejecucion del procedimiento
				$this->procedimiento='vef.ft_permisos_sucursales_ime';
				$this->transaccion='VEF_SUCPERMISOS_MOD';
				$this->tipo_procedimiento='IME';


				//Define los parametros para la funcion
				$this->setParametro('id_funcionario','id_funcionario','int4');
				$this->setParametro('id_autorizacion','id_autorizacion','int4');

				//Ejecuta la instruccion
				$this->armarConsulta();

				$this->ejecutarConsulta();

				//Devuelve la respuesta
				return $this->respuesta;
			}

			function eliminarPermisosSucursal(){
				//Definicion de variables para ejecucion del procedimiento
				$this->procedimiento='vef.ft_permisos_sucursales_ime';
				$this->transaccion='VEF_SUCPERMISOS_ELI';
				$this->tipo_procedimiento='IME';

				//Define los parametros para la funcion
				$this->setParametro('id_autorizacion','id_autorizacion','int4');

				//Ejecuta la instruccion
				$this->armarConsulta();
				$this->ejecutarConsulta();

				//Devuelve la respuesta
				return $this->respuesta;
			}

			function listarPermisosSucursal(){
				//Definicion de variables para ejecucion del procedimientp
				$this->procedimiento='vef.ft_permisos_sucursales_sel';
				$this->transaccion='VEF_SUCPERMISOS_SEL';
				$this->tipo_procedimiento='SEL';//tipo de transaccion

				//Definicion de la lista del resultado del query
				$this->captura('id_autorizacion','int4');
				$this->captura('id_funcionario','int4');
				$this->captura('estado_reg','varchar');
				$this->captura('id_usuario_ai','int4');
				$this->captura('fecha_reg','timestamp');
				$this->captura('usuario_ai','varchar');
				$this->captura('id_usuario_reg','int4');
				$this->captura('fecha_mod','timestamp');
				$this->captura('id_usuario_mod','int4');
				$this->captura('usr_reg','varchar');
				$this->captura('usr_mod','varchar');
				$this->captura('desc_funcionario1','varchar');
				$this->captura('nombre_cargo','varchar');

				//Ejecuta la instruccion
				$this->armarConsulta();
				$this->ejecutarConsulta();

				//Devuelve la respuesta
				return $this->respuesta;
			}


			function RecuperarCliente(){
				//Definicion de variables para ejecucion del procedimiento
				$this->procedimiento='vef.ft_venta_facturacion_ime';
				$this->transaccion='VEF_RECUCLI_MOD';
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



}
?>
