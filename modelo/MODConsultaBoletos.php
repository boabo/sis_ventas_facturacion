<?php
/**
*@package pXP
*@file gen-MODConsultaBoletos.php
*@author  (admin)
*@date 12-10-2017 21:15:26
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODConsultaBoletos extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarConsultaBoletos(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_consulta_boletos_sel';
		$this->transaccion='VF_CBS_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setParametro('criterio_filtro','criterio_filtro','varchar');
		//Definicion de la lista del resultado del query
		$this->captura('id_boleto','int4');
        $this->captura('punto_venta','varchar');
        $this->captura('localizador','varchar');
        $this->captura('total','numeric');
        $this->captura('liquido','numeric');
        $this->captura('id_moneda_boleto','int4');
        $this->captura('moneda','varchar');
        $this->captura('neto','numeric');
        $this->captura('fecha_emision','date');
        $this->captura('nro_boleto','varchar');
        $this->captura('pasajero','varchar');
        $this->captura('voided','varchar');
        $this->captura('estado','varchar');
        $this->captura('agente_venta','varchar');
        $this->captura('codigo_agente','varchar');
        $this->captura('forma_pago_amadeus','varchar');
        $this->captura('gestion','int4');

      /* $this->captura('id_forma_pago','int4');
        $this->captura('forma_pago','varchar');
        $this->captura('codigo_forma_pago','varchar');
        $this->captura('nombre_auxiliar','varchar');
        $this->captura('monto_forma_pago','numeric');
        $this->captura('id_forma_pago2','int4');
        $this->captura('forma_pago2','varchar');
        $this->captura('codigo_forma_pago2','varchar');
        $this->captura('monto_forma_pago2','numeric');*/
        //Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
}
?>