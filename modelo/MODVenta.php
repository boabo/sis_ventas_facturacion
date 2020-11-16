<?php
/**
*@package pXP
*@file gen-MODVenta.php
*@author  (admin)
*@date 01-06-2015 05:58:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODVenta extends MODbase{

	var $cone;
	var $link;
	var $informix;
	var $tabla_factucom_informix;
	var $tabla_factucomcon_informix;
	var $tabla_factucompag_informix;

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);

		$this->monedaBase();
		if ($this->monedaBase() == 'BOB') {
			$this->cone = new conexion();
			//$this->informix = $this->cone->conectarPDOInformix();
			// conexion a informix
			$this->link = $this->cone->conectarpdo();
			//conexion a pxp(postgres)

			$this->tabla_factucom_informix = $_SESSION['tabla_factucom_informix'];
			$this->tabla_factucomcon_informix = $_SESSION['tabla_factucomcon_informix'];
			$this->tabla_factucompag_informix = $_SESSION['tabla_factucompag_informix'];
		}

	}

	function monedaBase(){

		$cone = new conexion();
		$link = $cone->conectarpdo();
		$copiado = false;

		$consulta ="select m.codigo_internacional
																			from param.tmoneda m
																			where m.tipo_moneda =  'base'";

		$res = $link->prepare($consulta);
		$res->execute();
		$result = $res->fetchAll(PDO::FETCH_ASSOC);
		return $result[0]['codigo_internacional'];

	}


	function listarVenta(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_sel';
		$this->transaccion='VF_VEN_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->setParametro('historico','historico','varchar');
		$this->setParametro('tipo_factura','tipo_factura','varchar');
		$this->setParametro('id_sucursal','id_sucursal','integer');
		$this->setParametro('id_punto_venta','id_punto_venta','integer');
    $this->setParametro('tipo_usuario','tipo_usuario','varchar');


		//Definicion de la lista del resultado del query
		$this->captura('id_venta','int4');
		$this->captura('id_cliente','int4');
		$this->captura('id_sucursal','int4');
		$this->captura('id_proceso_wf','int4');
		$this->captura('id_estado_wf','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('correlativo_venta','varchar');
		$this->captura('a_cuenta','numeric');
		$this->captura('total_venta','numeric');
		$this->captura('fecha_estimada_entrega','date');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
        $this->captura('estado','varchar');
        $this->captura('nombre_factura','varchar');
        $this->captura('nombre_sucursal','varchar');
		$this->captura('nit','varchar');
		$this->captura('id_punto_venta','int4');
		$this->captura('nombre_punto_venta','varchar');

    $this->captura('id_forma_pago','varchar');
		$this->captura('forma_pago','varchar');
		$this->captura('monto_forma_pago','varchar');
		$this->captura('numero_tarjeta','varchar');
		$this->captura('codigo_tarjeta','varchar');
		$this->captura('tipo_tarjeta','varchar');
		
    $this->captura('porcentaje_descuento','numeric');
    $this->captura('id_vendedor_medico','varchar');
		$this->captura('comision','numeric');
		$this->captura('observaciones','text');
		$this->captura('fecha','date');
		$this->captura('nro_factura','integer');
		$this->captura('excento','numeric');
		$this->captura('cod_control','varchar');
		$this->captura('id_moneda','integer');
    $this->captura('total_venta_msuc','numeric');
    $this->captura('transporte_fob','numeric');
    $this->captura('seguros_fob','numeric');
    $this->captura('otros_fob','numeric');
    $this->captura('transporte_cif','numeric');
    $this->captura('seguros_cif','numeric');
    $this->captura('otros_cif','numeric');
		$this->captura('tipo_cambio_venta','numeric');
		$this->captura('desc_moneda','varchar');
		$this->captura('valor_bruto','numeric');
		$this->captura('descripcion_bulto','varchar');
		$this->captura('contabilizable','varchar');
		$this->captura('hora_estimada_entrega','varchar');
		$this->captura('forma_pedido','varchar');
		$this->captura('id_cliente_destino','integer');
		$this->captura('cliente_destino','varchar');





		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function getVariablesBasicas(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_sel';
		$this->transaccion='VF_VENCONFBAS_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);


		//Definicion de la lista del resultado del query
		$this->captura('variable','varchar');
		$this->captura('valor','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarVenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_ime';
		$this->transaccion='VF_VEN_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_cliente','id_cliente','int4');
		$this->setParametro('id_sucursal','id_sucursal','int4');
		$this->setParametro('nro_tramite','nro_tramite','varchar');
		$this->setParametro('a_cuenta','a_cuenta','numeric');
		$this->setParametro('total_venta','total_venta','numeric');
		$this->setParametro('fecha_estimada_entrega','fecha_estimada_entrega','date');
		$this->setParametro('observaciones','observaciones','text');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

    function insertarVentaCompleta() {
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

	            //recupera parametros devuelto depues de insertar ... (id_formula)
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

            //var_dump($json_detalle)   ;
            foreach($json_detalle as $f){
                $this->resetParametros();
                //Definicion de variables para ejecucion del procedimiento
                $this->procedimiento='vef.ft_venta_detalle_ime';
                $this->transaccion='VF_VEDET_INS';
                $this->tipo_procedimiento='IME';
                //modifica los valores de las variables que mandaremos
                $this->arreglo['id_item'] = $f['id_item'];
                $this->arreglo['id_producto'] = $f['id_producto'];
                $this->arreglo['id_formula'] = $f['id_formula'];
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
				$this->setParametro('id_venta', 'id_venta', 'int4');
				$this->setParametro('tipo_factura', 'tipo_factura', 'varchar');
				$this->setParametro('tipo', 'tipo', 'varchar');

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

	function insertarVentaInformix($id_venta)
	{
		$venta = $this->venta($id_venta);
		$detallesVenta = $this->detalleVenta($id_venta);
		$formasPago = $this->formaPagoVenta($id_venta);

		$pais = $venta[0]['pais'];
		$estacion = $venta[0]['estacion'];
		$nroaut = $venta[0]['nroaut'];
		$nroFactura = $venta[0]['nro_factura'];
		$estado = $venta[0]['estado'];
		$tipo_con = $venta[0]['tipo_con'];
		$agt = $venta[0]['agt'];
		$agtnoiata = $venta[0]['agtnoiata'];
		$sucursal = $venta[0]['sucursal'];
		$autoimpresor = $venta[0]['autoimpresor'];
		$fecha = $venta[0]['fecha'];
		$razon = $venta[0]['razon'];
		$nit = $venta[0]['nit'];
		$monto = $venta[0]['monto'];
		$exento = $venta[0]['excento'];
		$moneda = $venta[0]['moneda'];
		$tcambio = $venta[0]['tcambio'];
		$cod_control = $venta[0]['cod_control'];
		$usuario = $venta[0]['usuario'];
		$usuarioreg = $venta[0]['usuarioreg'];
		$horareg = $venta[0]['horareg'];
		$fechareg = $venta[0]['fechareg'];
		$contablz = $venta[0]['contablz'];
		$observacion = $venta[0]['observacion'];

		try {
			$this->informix->beginTransaction();

			$sql_in_fac = "INSERT INTO ingresos:$this->tabla_factucom_informix
						(pais, estacion, nroaut,
						 nrofac, estado, tipocon,
						  agt, agtnoiata, sucursal,
						  autoimpresor, fecha, razon,
						   nit, monto, exento,
						   moneda, tcambio, codcontrol,
						   usuario, usuarioreg, horareg,
						   fechareg, contablz, observacion)
					VALUES
						('" . $pais . "', '" . $estacion . "', '" . $nroaut . "',
						 '" . $nroFactura . "', '" .$estado. "', '" . $tipo_con . "',
						 '" . $agt . "', '" . $agtnoiata . "', '" . $sucursal . "',
						 '" . $autoimpresor . "', '" . $fecha . "', '" . $razon . "',
						 '" . $nit . "', '" . $monto . "', '" . $exento . "',
						 '" . $moneda . "', '" . $tcambio . "', '" .$cod_control. "',
						  '" . $usuario . "', '" . $usuarioreg . "', '" .$horareg. "',
						   '" . $fechareg . "', '" . $contablz . "', '" .$observacion. "');";

			$info_factura_fac = $this->informix->prepare($sql_in_fac);
			$info_factura_fac->execute();
			$detalleCont=1;
			foreach($detallesVenta as $detalle){
				$sql_in_det = "INSERT INTO ingresos:$this->tabla_factucomcon_informix
						(pais, estacion, nroaut,
						 nrofac, renglon, tipocon,
						  nroconce, cantidad, preciounit,
						  importe)
					VALUES
						('" . $pais . "', '" . $estacion . "', '" . $nroaut . "',
						 '" . $nroFactura . "', '" .$detalleCont. "', '" . $tipo_con . "',
						 '', '" . $detalle['cantidad'] . "', '" . $detalle['preciounit'] . "',
						 '" . $detalle['importe'] . "');";

				$info_factura_fac = $this->informix->prepare($sql_in_det);
				$info_factura_fac->execute();
				$detalleCont++;
			}

			$pagoCont=1;
			foreach($formasPago as $pago){
				$grupo='';
				$observa='';
				$nomaut='';
				$autoriza='';
				$cuotas=0;
				$recargo=0;
				$comprbnt=0;
				$pagomco=0;
				$sql_in_pag = "INSERT INTO ingresos:$this->tabla_factucompag_informix
						(pais, estacion, nroaut,
						 nrofac, renglon, forma,
						  tarjeta, numero, importe,
						  moneda, tcambio, agt,
						  agtnoiata, grupo, estado,
						  fecha, usuario, cuotas,
						  recargo, autoriza, comprbnt,
						  fecproc, ctacte, nomaut,
						  pagomco, contablz, observa)
					VALUES
						('" . $pais . "', '" . $estacion . "', '" . $nroaut . "',
						 '" . $nroFactura . "', '" .$pagoCont. "', '" . $pago['forma'] . "',
						 '" . $pago['tarjeta'] . "', '" . $pago['numero'] . "','" . $pago['importe'] . "',
						 '" . $moneda . "', '" . $tcambio . "','" . $agt . "',
						 '" . $agtnoiata . "', '" .$grupo. "','" . $estado . "',
						 '" . $fecha . "', '" . $usuario . "','".$cuotas."',
						 '".$recargo."', '" .$autoriza. "','".$comprbnt."',
						 '" . $fecha . "', '" . $pago['ctacte'] . "','" .$nomaut. "',
						 '".$pagomco."', '" . $contablz . "','" .$observa. "'
						 );";
				//var_dump($sql_in); exit;
				$info_factura_fac = $this->informix->prepare($sql_in_pag);
				$info_factura_fac->execute();
				$pagoCont++;
			}
			$respues  = $this->informix->commit();
			var_dump($respues);
			$this->respuesta = new Mensaje();
			$this->respuesta->setMensaje('EXITO', $this->nombre_archivo, 'La consulta se ejecuto con exito generacion de factura', 'La consulta se ejecuto con exito', 'base', 'no tiene', 'no tiene', 'SEL', '$this->consulta', 'no tiene');
			//$this->respuesta->setTotal(1);
			//$this->respuesta->setDatos($temp);
			return $this->respuesta;

		} catch (Exception $e) {
			$this->informix->rollBack();
			$this->respuesta = new Mensaje();
			throw new Exception($e->getMessage(), 2);
		}
		//var_dump($detallesVenta);
		//var_dump($formasPago); exit;

		//$nroliqui = $this->objParam->getParametro('liquidevolu');
		//$estacion = $this->objParam->getParametro('sucursal');

		/*$fecha_reg = $nota[0]['fecha_reg'];
		$date = new DateTime($fecha_reg);
		//var_dump($date->format('d-m-Y'));
		$fecha_fac = new DateTime($nota[0]['fecha_fac']);
		$fecha = new DateTime($nota[0]['fecha']);

		$nro_factura_anterior = '';
		$nro_autorizacion_anterior = '';

		$observaciones = '';
		$usuario = $_SESSION['_LOGIN'];
		if ($nota[0]['tipo'] == 'FACTURA' || $nota[0]['tipo'] == 'FACTURA MANUAL') {
			$nro_factura_anterior = $nota[0]['nrofac'];
			$nro_autorizacion_anterior = $nota[0]['nroaut_anterior'];
		} else {
			$observaciones = 'LIQUIDACION NRO: ' . $nota[0]['nro_liquidacion'];

		}*/

		//$results = $info_nota_ins->fetchAll(PDO::FETCH_ASSOC); //cuando llamada es un select
	}

	function venta($id_venta)
	{
		$stmt2 = $this->link->prepare("select ps.codigo as pais, est.codigo as estacion, dos.nroaut, vta.nro_factura,
										case when vta.estado='finalizado' then '1' else '9' end as estado,
										'' as tipo_con, pv.codigo as agt, '' as agtnoiata, suc.codigo as sucursal,
										'' as autoimpresor, vta.fecha, vta.nombre_factura as razon, vta.nit, vta.total_venta as monto, vta.excento,
										 mon.codigo_internacional as moneda, tc.oficial as tcambio, vta.cod_control,
										 coalesce(usu.cuenta, usreg.cuenta) as usuario, usreg.cuenta as usuarioreg,
										 vta.hora_estimada_entrega as horareg, to_char(vta.fecha_reg,'YYYY-MM-DD') as fechareg,
										 case when vta.contabilizable = 'no' then 'N' else 'S' end as contablz , vta.observaciones as observacion
										from vef.tventa vta
										inner join vef.tsucursal suc on suc.id_sucursal=vta.id_sucursal
										inner join param.tlugar est on est.id_lugar=suc.id_lugar
										inner join param.tlugar ps on ps.id_lugar=est.id_lugar_fk
										inner join vef.tdosificacion dos on dos.id_dosificacion=vta.id_dosificacion
										inner join param.tmoneda mon on mon.id_moneda=vta.id_moneda
										left join param.ttipo_cambio tc on tc.fecha=vta.fecha and tc.id_moneda in (select id_moneda from param.tmoneda where triangulacion='si')
										left join segu.tusuario usu on usu.id_usuario=vta.id_usuario_cajero
										inner join segu.tusuario usreg on usreg.id_usuario=vta.id_usuario_reg
										inner join vef.tpunto_venta pv on pv.id_punto_venta=vta.id_punto_venta
										where vta.id_venta= '$id_venta'");
		$stmt2->execute();
		$results = $stmt2->fetchAll(PDO::FETCH_ASSOC);
		return $results;
	}

	function detalleVenta($id_venta)
	{
		$stmt2 = $this->link->prepare("select vtadet.cantidad, vtadet.precio as preciounit, vtadet.cantidad*vtadet.precio as importe
										from vef.tventa_detalle vtadet
										where vtadet.id_venta= '$id_venta'");
		$stmt2->execute();
		$results = $stmt2->fetchAll(PDO::FETCH_ASSOC);
		return $results;
	}

	function formaPagoVenta($id_venta)
	{
		$stmt2 = $this->link->prepare("select fp.codigo as forma, vtafp.tipo_tarjeta as tarjeta,
										case when fp.nombre like '%CTA-CTE%' then '' else vtafp.numero_tarjeta end as numero,
										 vtafp.monto_mb_efectivo as importe,
										case when fp.nombre like '%CTA-CTE%' then vtafp.numero_tarjeta else '' end as ctacte
										from vef.tventa_forma_pago vtafp
										inner join vef.tforma_pago fp on fp.id_forma_pago=vtafp.id_forma_pago
										where vtafp.id_venta = '$id_venta'");
		$stmt2->execute();
		$results = $stmt2->fetchAll(PDO::FETCH_ASSOC);
		return $results;
	}

	function modificarVenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_ime';
		$this->transaccion='VF_VEN_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('tipo_factura','tipo_factura','varchar');
		$this->setParametro('id_venta','id_venta','int4');
		$this->setParametro('id_cliente','id_cliente','int4');
		$this->setParametro('id_sucursal','id_sucursal','int4');
		$this->setParametro('nro_tramite','nro_tramite','varchar');
		$this->setParametro('a_cuenta','a_cuenta','numeric');
		$this->setParametro('total_venta','total_venta','numeric');
		$this->setParametro('fecha_estimada_entrega','fecha_estimada_entrega','date');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarVenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_ime';
		$this->transaccion='VF_VEN_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_venta','id_venta','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function anularVenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_venta_ime';
		$this->transaccion='VF_VENANU_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_venta','id_venta','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

    function setContabilizable(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_venta_ime';
        $this->transaccion='VF_VENCONTA_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_venta','id_venta','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function verificarRelacion(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_venta_ime';
        $this->transaccion='VF_VENVERELA_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_punto_venta','id_punto_venta','int4');
        $this->setParametro('id_sucursal','id_sucursal','int4');
        $this->setParametro('tipo_factura','tipo_factura','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function siguienteEstadoVenta(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_venta_ime';
        $this->transaccion='VEF_SIGEVE_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_proceso_wf_act','id_proceso_wf_act','int4');
        $this->setParametro('id_estado_wf_act','id_estado_wf_act','int4');
        $this->setParametro('id_funcionario_usu','id_funcionario_usu','int4');
        $this->setParametro('id_tipo_estado','id_tipo_estado','int4');
        $this->setParametro('id_funcionario_wf','id_funcionario_wf','int4');
        $this->setParametro('id_depto_wf','id_depto_wf','int4');
        $this->setParametro('obs','obs','text');
        $this->setParametro('json_procesos','json_procesos','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

		function siguienteEstadoRecibo(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_venta_ime';
        $this->transaccion='VEF_SIGRECI_IME';
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

    function anteriorEstadoVenta(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_venta_ime';
        $this->transaccion='VEF_ANTEVE_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_proceso_wf','id_proceso_wf','int4');
        $this->setParametro('id_funcionario_usu','id_funcionario_usu','int4');
        $this->setParametro('operacion','operacion','varchar');

        $this->setParametro('id_funcionario','id_funcionario','int4');
        $this->setParametro('id_tipo_estado','id_tipo_estado','int4');
        $this->setParametro('id_estado_wf','id_estado_wf','int4');
        $this->setParametro('obs','obs','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

	function listarNotaVentaDet(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_sel';
		$this->transaccion='VF_NOTAVENDV_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion


		$this->setParametro('id_venta','id_venta','int4');
		//captura parametros adicionales para el count
		$this->capturaCount('suma_total','numeric');
		//Definicion de la lista del resultado del query
		$this->captura('id_venta','int4');

		$this->captura('id_venta_detalle','int4');
		$this->captura('precio','numeric');
		$this->captura('tipo','varchar');
		$this->captura('cantidad','int4');
		$this->captura('precio_total','numeric');
		$this->captura('codigo_nombre','varchar');
		$this->captura('item_nombre','varchar');
		$this->captura('nombre_producto','varchar');
		$this->captura('id_formula','int4');
		$this->captura('id_formula_detalle','int4');
		$this->captura('cantidad_df','NUMERIC');
		$this->captura('item_nombre_df','varchar');
		$this->captura('nombre_formula','varchar');





		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

    function listarNotaVenta(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_sel';
		$this->transaccion='VF_NOTVENV_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);
		$this->setParametro('id_venta','id_venta','int4');



		//Definicion de la lista del resultado del query
		$this->captura('id_venta','int4');
		$this->captura('id_cliente','int4');
		$this->captura('id_sucursal','int4');
		$this->captura('id_proceso_wf','int4');
		$this->captura('id_estado_wf','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('nro_tramite','varchar');
		$this->captura('a_cuenta','numeric');
		$this->captura('total_venta','numeric');
		$this->captura('fecha_estimada_entrega','date');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
        $this->captura('estado','varchar');
        $this->captura('nombre_completo','text');
        $this->captura('nombre_sucursal','varchar');

		$this->captura('direccion','varchar');
		$this->captura('correo','varchar');
		$this->captura('telefono','varchar');
		$this->captura('total_string','varchar');




		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarReciboFactura(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_sel';
		$this->transaccion='VF_VENREP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_venta','id_venta','integer');
		$this->setParametro('tipo_factura','tipo_factura','varchar');

		//Definicion de la lista del resultado del query
		$this->captura('nombre_entidad','varchar');
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
        $this->captura('medico_vendedor','varchar');


        $this->captura('nro_tramite','varchar');
		$this->captura('codigo_cliente','varchar');


		$this->captura('lugar_cliente','varchar');
		$this->captura('cliente_destino','varchar');
		$this->captura('lugar_destino','varchar');
        $this->captura('codigo_sucursal','varchar');//nuevo mvm
		$this->captura('leyenda','varchar');//nuevo mvm
        $this->captura('zona','varchar');//nuevo mvm









        //Ejecuta la instruccion
		$this->armarConsulta();


		$this->ejecutarConsulta();


		//var_dump($this->respuesta); exit;
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarRecibo(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_sel';
		$this->transaccion='VF_VENREC_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_venta','id_venta','integer');
		$this->setParametro('id_punto_venta','id_punto_venta','integer');
		$this->setParametro('tipo_factura','tipo_factura','varchar');


		//Definicion de la lista del resultado del query
		$this->captura('nombre_entidad','varchar');
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
        $this->captura('medico_vendedor','varchar');


        $this->captura('nro_tramite','varchar');
		$this->captura('codigo_cliente','varchar');


		$this->captura('lugar_cliente','varchar');
		$this->captura('cliente_destino','varchar');
		$this->captura('lugar_destino','varchar');
        $this->captura('codigo_sucursal','varchar');//nuevo mvm
		$this->captura('leyenda','varchar');//nuevo mvm
		$this->captura('zona','varchar');//nuevo mvm
		$this->captura('moneda_base','varchar');//nuevo mvm
		$this->captura('codigo_moneda','varchar');//nuevo mvm
		$this->captura('fecha_ingles','varchar');//nuevo mvm
		$this->captura('forma_pago','varchar');//nuevo mvm
		$this->captura('codigo_iata','varchar');//nuevo mvm
		$this->captura('codigo_moneda_recibo','varchar');//nuevo mvm
		$this->captura('moneda_literal','varchar');//nuevo mvm

        //Ejecuta la instruccion
		$this->armarConsulta();


		$this->ejecutarConsulta();


		//var_dump($this->respuesta);
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarReciboFacturaDetalle(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_sel';
		$this->transaccion='VF_VENDETREP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_venta','id_venta','integer');

		//Definicion de la lista del resultado del query
		$this->captura('concepto','varchar');
		$this->captura('cantidad','numeric');
		$this->captura('precio_unitario','numeric');
		$this->captura('precio_total','numeric');
		$this->captura('unidad_medida','varchar');
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

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarReciboDetalle(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_sel';
		$this->transaccion='VF_VENDETREP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_venta','id_venta','integer');

		//Definicion de la lista del resultado del query
		$this->captura('concepto','varchar');
		$this->captura('cantidad','numeric');
		$this->captura('precio_unitario','numeric');
		$this->captura('precio_total','numeric');
		$this->captura('unidad_medida','varchar');
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

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarReciboFacturaDescripcion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_sel';
		$this->transaccion='VF_VENDESREP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_venta','id_venta','integer');

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

	function listarReciboDescripcion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_venta_sel';
		$this->transaccion='VF_VENDESREP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_venta','id_venta','integer');

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
    function listarVentaDotificacion(){
        $this->procedimiento='vef.ft_venta_sel';
        $this->transaccion='VF_VEND_SEL';
        $this->tipo_procedimiento='SEL';
        $this->setParametro('pes_estado','pes_estado','varchar');
        $this->setParametro('punto','punto','varchar');
        $this->setParametro('id_punto_venta','id_punto_venta','integer');
        $this->captura('id_venta','int4');
        $this->captura('id_cliente','int4');
        $this->captura('id_dosificacion','int4');
        $this->captura('id_punto_venta','int4');
        $this->captura('nro_factura','int4');
        $this->captura('nombre_completo','text');
        $this->captura('nit','varchar');
        $this->captura('total_venta','numeric');
        $this->captura('fecha_doc','date');
        $this->captura('sucursal','varchar');
        $this->captura('id_forma_pago','int4');
        $this->captura('forma_pago','varchar');
        $this->captura('observaciones','text');
        $this->captura('monto_forma_pago','numeric');
        $this->captura('comision','numeric');
        $this->captura('estado','varchar');
        $this->captura('cod_control','varchar');
        $this->captura('tipo_factura','varchar');
        $this->captura('usuario_ai','varchar');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_reg','int4');
        $this->captura('id_usuario_ai','int4');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('estado_reg','varchar');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mo','varchar');
        $this->captura('nroaut','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarFacturaExterna(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.f_inserta_factura_manual';
        $this->transaccion='VEF_INSFACEXT_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('v_factura','v_factura','text');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

		function insertarFormula(){
        //Definicion de variables para ejecucion del procedimiento
				$this->procedimiento='vef.ft_venta_ime';
				$this->transaccion='VF_VENFOR_INS';
				$this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
				$this->setParametro('id_formula','id_formula','integer');
        $this->setParametro('id_sucursal','id_sucursal','integer');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }



}
?>
