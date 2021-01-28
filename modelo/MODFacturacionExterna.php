<?php
/**
 * @package pXP
 * @file gen-MODFacturacionExterna.php
 * @author  (Ismael Valdivia)
 * @date 23-05-2020 14:00:00
 * @description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODFacturacionExterna extends MODbase
{
    function insertarVentaFactura()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'vef.ft_facturacion_externa_ime';
        $this->transaccion = 'VEF_INS_FAC_EXT_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('nit_entidad', 'nit_entidad', 'varchar');
        $this->setParametro('punto_venta', 'punto_venta', 'varchar');
        $this->setParametro('nit_cliente', 'nit_cliente', 'varchar');
        $this->setParametro('razon_social', 'razon_social', 'varchar');
        $this->setParametro('moneda', 'moneda', 'varchar');
        $this->setParametro('tipo_cambio', 'tipo_cambio', 'numeric');
        $this->setParametro('json_venta_detalle','json_venta_detalle','text');
        $this->setParametro('exento','exento','numeric');
        $this->setParametro('observaciones','observaciones','varchar');
        $this->setParametro('enviar_correo','enviar_correo','varchar');
        $this->setParametro('correo_electronico','correo_electronico','varchar');
        $this->setParametro('id_liquidacion','id_liquidacion','integer');
        //$this->setParametro('ordeCode','ordeCode','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarVentaFacturaPrueba()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'vef.ft_facturacion_externa_prueba_ime';
        $this->transaccion = 'VEF_INS_FAC_PU_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('nit_entidad', 'nit_entidad', 'varchar');
        $this->setParametro('punto_venta', 'punto_venta', 'varchar');
        $this->setParametro('nit_cliente', 'nit_cliente', 'varchar');
        $this->setParametro('razon_social', 'razon_social', 'varchar');
        $this->setParametro('moneda', 'moneda', 'varchar');
        $this->setParametro('tipo_cambio', 'tipo_cambio', 'numeric');
        $this->setParametro('json_venta_detalle','json_venta_detalle','text');
        $this->setParametro('exento','exento','numeric');
        $this->setParametro('observaciones','observaciones','varchar');
        $this->setParametro('enviar_correo','enviar_correo','varchar');
        $this->setParametro('correo_electronico','correo_electronico','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function anularFactura(){
  			//Definicion de variables para ejecucion del procedimiento
  			$this->procedimiento='vef.ft_facturacion_externa_ime';
  			$this->transaccion='VEF_ANU_FAC_EXT';
  			$this->tipo_procedimiento='IME';

  			//Define los parametros para la funcion
  			$this->setParametro('id_venta','id_venta','int4');

  			//Ejecuta la instruccion
  			$this->armarConsulta();
  			$this->ejecutarConsulta();

  			//Devuelve la respuesta
  			return $this->respuesta;
  	}

	/*Aumentando para listar las facturas emitidas*/
    function listarFacturasEmitidas(){
  			//Definicion de variables para ejecucion del procedimiento
  			$this->procedimiento='vef.ft_facturacion_externa_sel';
  			$this->transaccion='VEF_LIST_FACT_EX_SEL';
  			$this->tipo_procedimiento='SEL';
        $this->setCount(false);
  			//Define los parametros para la funcion
  			$this->setParametro('ci','ci','varchar');

        $this->captura('id_proceso_wf','int4');
        $this->captura('fecha','date');
    		$this->captura('nro_factura','integer');
    		$this->captura('nit','varchar');
    		$this->captura('nombre_factura','varchar');
        $this->captura('nombre','varchar');
    		$this->captura('id_venta','int4');

  			//Ejecuta la instruccion
  			$this->armarConsulta();
  			$this->ejecutarConsulta();

  			//Devuelve la respuesta
  			return $this->respuesta;
  	}


}

?>
