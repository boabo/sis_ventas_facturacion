<?php
/**
*@package pXP
*@file gen-MODFacturasCarga.php
*@author  (ismael.valdivia)
*@date 08-03-2019 15:50:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODImprimirFacturaNotasDebCre extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

  function listarFactura(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_impresion_notas_debito_sel';
		$this->transaccion='VF_IMPREFACT_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);


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
		$this->captura('comision','numeric');//nuevo mvm
		$this->captura('hora_estimada_entrega','time');//nuevo mvm

        //Ejecuta la instruccion
		$this->armarConsulta();


		$this->ejecutarConsulta();
		//var_dump("result 1",$this->respuesta);
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


}
?>
