<?php
/**
*@package pXP
*@file gen-MODEntrega.php
*@author  (admin)
*@date 12-09-2017 15:04:26
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODEntrega extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarEntrega(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_entrega_sel';
		$this->transaccion='VF_ENG_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setParametro('id_punto_venta','id_punto_venta','integer');
        $this->setParametro('tipo_usuario','tipo_usuario','varchar');
		//Definicion de la lista del resultado del query
		$this->captura('id_entrega_brinks','int4');
		$this->captura('fecha_recojo','date');
		$this->captura('estado_reg','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('arqueo_moneda_local','numeric');
        $this->captura('arqueo_moneda_extranjera','numeric');
        $this->captura('id_punto_venta','int4');
        $this->captura('nombre_punto_venta','varchar');
        $this->captura('estacion','varchar');
        $this->captura('codigo','varchar');
        $this->captura('cajero','text');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarEntrega(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_entrega_ime';
		$this->transaccion='VF_ENG_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('fecha_recojo','fecha_recojo','date');
        $this->setParametro('id_punto_venta','id_punto_venta','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarEntrega(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_entrega_ime';
		$this->transaccion='VF_ENG_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_entrega_brinks','id_entrega_brinks','int4');
		$this->setParametro('fecha_recojo','fecha_recojo','date');
		$this->setParametro('estado_reg','estado_reg','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarEntrega(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_entrega_ime';
		$this->transaccion='VF_ENG_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_entrega_brinks','id_entrega_brinks','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
    function fechaApertura(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='vef.ft_entrega_sel';
        $this->transaccion='VF_ENG_FECH_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //$this->setCount(false);

        //Definicion de la lista del resultado del query
        $this->captura('fecha_cierre','varchar');
        $this->captura('id_punto_venta','int4');
        $this->captura('nombre_cajero','text');
        $this->captura('nombre','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function listarEntregaBs(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='vef.ft_entrega_sel';
        $this->transaccion='VF_ENG_REPRO';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);

        $this->setParametro('id_entrega_brinks','id_entrega_brinks','int4');
        //Definicion de la lista del resultado del query
        $this->captura('nombre_cajero','text');
        $this->captura('estacion','varchar');
        $this->captura('punto_venta','varchar');
        $this->captura('fecha_recojo','text');
        $this->captura('fecha_apertura_cierre','text');
        $this->captura('arqueo_moneda_local','numeric');
        $this->captura('nro_cuenta','varchar');
        $this->captura('denominacion','varchar');
        $this->captura('total','numeric');
        $this->captura('literial','varchar');
				$this->captura('id_moneda','int4');
        $this->captura('moneda_local','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //var_dump($this->respuesta); exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function listarEntregaDolares(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='vef.ft_entrega_sel';
        $this->transaccion='VF_ENG_REDOL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);

        $this->setParametro('id_entrega_brinks','id_entrega_brinks','int4');
        //Definicion de la lista del resultado del query
        $this->captura('nombre_cajero','text');
        $this->captura('estacion','varchar');
        $this->captura('punto_venta','varchar');
        $this->captura('fecha_recojo','text');
        $this->captura('fecha_apertura_cierre','text');
        $this->captura('arqueo_moneda_extranjera','numeric');
        $this->captura('nro_cuenta','varchar');
        $this->captura('denominacion','varchar');
        $this->captura('total','numeric');
        $this->captura('literial','varchar');
        $this->captura('id_moneda','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //var_dump($this->respuesta); exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function getPuntoVenta(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_entrega_ime';
        $this->transaccion='VF_ENG_GET';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_punto_venta','id_punto_venta','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function getTipoUsuario(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_entrega_ime';
        $this->transaccion='VF_USU_GET';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('p_id_usuario','p_id_usuario','int4');
        $this->setParametro('id_punto_venta','id_punto_venta','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
}
?>
