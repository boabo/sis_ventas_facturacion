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

 	function listarCanalVentaPuntoVenta() {
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_reporte_ventas';
		$this->transaccion='VF_CCANVE_SEL';
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
	function listarCanalVenta(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_reporte_ventas';
		$this->transaccion='VF_RREVBOL_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		// $this->setCount(false);
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

	function subLugarPais() {
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_reporte_ventas';
		$this->transaccion='VF_VLUTOT_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_lugar_fk','id_lugar_fk','int4');
		//Definicion de la lista del resultado del query
		$this->captura('id_lugar','int4');
		$this->captura('id_lugar_fk','int4');
		$this->captura('codigo','varchar');
		$this->captura('nombre','varchar');
		$this->captura('sw_impuesto','varchar');
		$this->captura('sw_municipio','varchar');
		$this->captura('tipo','varchar');
		$this->captura('es_regional','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		// echo $this->consulta;exit;
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function consultaFacturaVenta() {
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_consulta_factura';
		$this->transaccion='VF_CONSFACX_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		//Definicion de la lista del resultado del query

		$this->setParametro('id_entidad', 'id_entidad', 'int4');
		$this->setParametro('id_sucursal', 'id_sucursal', 'int4');
		$this->setParametro('id_punto_venta', 'id_punto_venta', 'int4');
		$this->setParametro('tipo_documento', 'tipo_documento', 'varchar');
		$this->setParametro('nro_documento', 'nro_documento', 'varchar');
		$this->setParametro('nro_autorizacion', 'nro_autorizacion', 'varchar');
		$this->setParametro('estado_documento', 'estado_documento', 'varchar');
		$this->setParametro('fecha_ini', 'fecha_ini', 'date');
		$this->setParametro('fecha_fin', 'fecha_fin', 'date');

		$this->captura('id_venta','integer');
		$this->captura('nro_factura','integer');
		$this->captura('nit','varchar');
		$this->captura('nombre_factura','varchar');
		$this->captura('cod_control','varchar');
		$this->captura('fecha_factura','date');
		$this->captura('observaciones','text');
		$this->captura('total_venta','numeric');
		$this->captura('excento','numeric');
		$this->captura('tipo_factura','varchar');
		$this->captura('nroaut','varchar');
		$this->captura('punto_venta','text');
		$this->captura('desc_persona','text');
		$this->captura('nro_deposito','varchar');
		$this->captura('monto_total','numeric');
		$this->captura('fecha_dep','date');
		$this->captura('nro_boleto','text');
		//Ejecuta la instruccion
		$this->armarConsulta();
		// echo $this->consulta;exit;
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	function consultaDetalleFactura(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_consulta_factura';
		$this->transaccion='VF_DJSVEN_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);

		$this->setParametro('id_venta', 'id_venta', 'int4');
		$this->captura('jsonData','text');
		//Ejecuta la instruccion
		$this->armarConsulta();
		// echo $this->consulta;exit;
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}

	// stage datatsss
	function puntoVentaPaiStage(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_reporte_ventas';
		$this->transaccion='VF_STPVCP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->captura('country_name','varchar');
		$this->captura('country_code','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		// echo $this->consulta;exit;
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}
	function puntoVentaCiudadStage(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_reporte_ventas';
		$this->transaccion='VF_STPVC_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->captura('city_name','varchar');
		$this->captura('city_code','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		// echo $this->consulta;exit;
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarCanalVentaStage(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_reporte_ventas';
		$this->transaccion='VF_STPVCH_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->captura('sale_channel','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		// echo $this->consulta;exit;
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarPuntoVentaTipoStage(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_reporte_ventas';
		$this->transaccion='VF_STPVT_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->captura('tipo_pos','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		// echo $this->consulta;exit;
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarCodigoIataStage(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_reporte_ventas';
		$this->transaccion='VF_STPCI_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->captura('iata_code','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		// echo $this->consulta;exit;
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarPuntoVentaOfficeIdStage(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_reporte_ventas';
		$this->transaccion='VF_STPOFI_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->captura('office_id','varchar');
		$this->captura('name_pv','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		// echo $this->consulta;exit;
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}
}
?>
