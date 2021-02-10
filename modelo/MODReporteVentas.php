<?php
/**
*@package pXP
*@file MODReporteVentas.php
*@author  (breydi.vasquez)
*@date 29-01-2021
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODReporteVentas extends MODbase {

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarCanalVenta(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_reporte_ventas';
		$this->transaccion='VF_RREVBOL_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query

		$this->captura('id_catalogo', 'int4');
		$this->captura('codigo', 'varchar');
		$this->captura('descripcion', 'varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		// echo $this->consulta;exit;
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarPuntoVentaRbol(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_reporte_ventas';
		$this->transaccion='VF_PUVERB_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		// $this->captura('id_punto_venta','int4');
		// $this->captura('nombre','varchar');
		// $this->captura('descripcion','text');
		$this->captura('codigo','varchar');
		// $this->captura('tipo','varchar');
		// $this->captura('office_id', 'varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		// echo $this->consulta;exit;
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarPuntoVentaOfficeId() {
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_reporte_ventas';
		$this->transaccion='VF_OFFIDRV_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->captura('id_punto_venta','int4');
		$this->captura('office_id','varchar');

		$this->armarConsulta();
		// echo $this->consulta;exit;
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarPuntoVentaTipo() {
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_reporte_ventas';
		$this->transaccion='VF_FILTIPO_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->setParametro('tipo','tipo','varchar');
		//Definicion de la lista del resultado del query
		$this->captura('tipo','varchar');
		$this->captura('codigo','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		// echo $this->consulta;exit;
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function getCanal() {
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_reporte_ventas';
		$this->transaccion='VF_GETCAN_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);
		//Definicion de la lista del resultado del query
		$this->captura('codigo','text');
		//Ejecuta la instruccion
		$this->armarConsulta();
		// echo $this->consulta;exit;
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

}
?>
