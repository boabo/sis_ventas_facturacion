<?php
/**
*@package pXP
*@file gen-MODDosificacion.php
*@author  (jrivera)
*@date 07-10-2015 13:00:56
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODDosificacion extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarDosificacion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='vef.ft_dosificacion_sel';
		$this->transaccion='VF_DOS_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->captura('id_dosificacion','int4');
		$this->captura('id_sucursal','int4');
		$this->captura('final','integer');
		$this->captura('tipo','varchar');
		$this->captura('fecha_dosificacion','date');
		$this->captura('nro_siguiente','int4');
		$this->captura('nroaut','varchar');
		$this->captura('fecha_inicio_emi','date');
		$this->captura('fecha_limite','date');
		$this->captura('tipo_generacion','varchar');
		$this->captura('glosa_impuestos','varchar');
		$this->captura('id_activida_economica','varchar');
		$this->captura('llave','varchar');
        $this->captura('llave_aux','varchar');
		$this->captura('inicial','integer');
		$this->captura('estado_reg','varchar');
		$this->captura('glosa_empresa','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_actividad_economica','text');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarDosificacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_dosificacion_ime';
		$this->transaccion='VF_DOS_INS';
		$this->tipo_procedimiento='IME';



		//Define los parametros para la funcion
		$this->setParametro('id_sucursal','id_sucursal','int4');
		$this->setParametro('final','final','integer');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('fecha_dosificacion','fecha_dosificacion','date');
		$this->setParametro('nro_siguiente','nro_siguiente','int4');
		$this->setParametro('nroaut','nroaut','varchar');
		$this->setParametro('fecha_inicio_emi','fecha_inicio_emi','date');
		$this->setParametro('fecha_limite','fecha_limite','date');
		$this->setParametro('tipo_generacion','tipo_generacion','varchar');
		$this->setParametro('glosa_impuestos','glosa_impuestos','varchar');
		$this->setParametro('id_activida_economica','id_activida_economica','varchar');
		$this->setParametro('llave','llave','codigo_html');
		$this->setParametro('inicial','inicial','integer');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('glosa_empresa','glosa_empresa','varchar');



		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarDosificacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_dosificacion_ime';
		$this->transaccion='VF_DOS_MOD';
		$this->tipo_procedimiento='IME';


		//Define los parametros para la funcion
		$this->setParametro('id_dosificacion','id_dosificacion','int4');
		$this->setParametro('id_sucursal','id_sucursal','int4');
		$this->setParametro('final','final','integer');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('fecha_dosificacion','fecha_dosificacion','date');
		$this->setParametro('nro_siguiente','nro_siguiente','int4');
		$this->setParametro('nroaut','nroaut','varchar');
		$this->setParametro('fecha_inicio_emi','fecha_inicio_emi','date');
		$this->setParametro('fecha_limite','fecha_limite','date');
		$this->setParametro('tipo_generacion','tipo_generacion','varchar');
		$this->setParametro('glosa_impuestos','glosa_impuestos','varchar');
		$this->setParametro('id_activida_economica','id_activida_economica','varchar');
		$this->setParametro('llave','llave','codigo_html');
		$this->setParametro('inicial','inicial','integer');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('glosa_empresa','glosa_empresa','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();

		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarDosificacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='vef.ft_dosificacion_ime';
		$this->transaccion='VF_DOS_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_dosificacion','id_dosificacion','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

    function listarDosificacionInter(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='vef.ft_dosificacion_sel';
        $this->transaccion='VF_DOSI_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_dosificacion','int4');
        $this->captura('id_sucursal','int4');
        $this->captura('final','integer');
        $this->captura('tipo','varchar');
        $this->captura('fecha_dosificacion','date');
        $this->captura('nro_siguiente','int4');
        $this->captura('nroaut','varchar');
        $this->captura('fecha_inicio_emi','date');
        $this->captura('fecha_limite','date');
        $this->captura('tipo_generacion','varchar');
        $this->captura('glosa_impuestos','varchar');
        $this->captura('id_activida_economica','varchar');
        $this->captura('llave','varchar');
        $this->captura('inicial','integer');
        $this->captura('estado_reg','varchar');
        $this->captura('glosa_empresa','varchar');
        $this->captura('id_usuario_ai','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('id_usuario_mod','int4');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('desc_actividad_economica','text');
        $this->captura('codigo','varchar');
        $this->captura('nom_sucursal','text');
        $this->captura('nombre_sucursal','varchar');
        $this->captura('estacion','varchar');
        $this->captura('dias_restante','int4');
        $this->captura('nro_tramite','varchar');
        $this->captura('nombre_sistema','varchar');
        $this->captura('leyenda','varchar');
        $this->captura('rnd','varchar');
				/*Aumentando para dosificaciones de facturacion por exportacion
				Dev: Ismael Valdivia
				Fecha Mod: 19/04/2021*/
				$this->captura('caracteristica','varchar');
        $this->captura('titulo','varchar');
        $this->captura('subtitulo','varchar');
				/*************************************************************/

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function insertarDosificacionExter(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_dosificacion_ime';
        $this->transaccion='VF_DOS_INSEX';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_sucursal','id_sucursal','int4');
        $this->setParametro('final','final','integer');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('fecha_dosificacion','fecha_dosificacion','date');
        $this->setParametro('nro_siguiente','nro_siguiente','int4');
        $this->setParametro('nroaut','nroaut','varchar');
        $this->setParametro('fecha_inicio_emi','fecha_inicio_emi','date');
        $this->setParametro('fecha_limite','fecha_limite','date');
        $this->setParametro('tipo_generacion','tipo_generacion','varchar');
        $this->setParametro('glosa_impuestos','glosa_impuestos','varchar');
        $this->setParametro('id_activida_economica','id_activida_economica','varchar');
        $this->setParametro('llave','llave','codigo_html');
        $this->setParametro('inicial','inicial','integer');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('glosa_empresa','glosa_empresa','varchar');

        $this->setParametro('nro_tramite','nro_tramite','varchar');
        $this->setParametro('nombre_sistema','nombre_sistema','varchar');
        $this->setParametro('leyenda','leyenda','varchar');
        $this->setParametro('rnd','rnd','varchar');

				/*Aumentando para dosificaciones de facturacion por exportacion
				Dev: Ismael Valdivia
				Fecha Mod: 19/04/2021*/
				$this->setParametro('caracteristica','caracteristica','varchar');
				$this->setParametro('titulo','titulo','varchar');
				$this->setParametro('subtitulo','subtitulo','varchar');
				/*************************************************************/

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function modificarDosificacionExter(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_dosificacion_ime';
        $this->transaccion='VF_DOS_MODEXT';
        $this->tipo_procedimiento='IME';


        //Define los parametros para la funcion
        $this->setParametro('id_dosificacion','id_dosificacion','int4');
        $this->setParametro('id_sucursal','id_sucursal','int4');
        $this->setParametro('final','final','integer');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('fecha_dosificacion','fecha_dosificacion','date');
        $this->setParametro('nro_siguiente','nro_siguiente','int4');
        $this->setParametro('nroaut','nroaut','varchar');
        $this->setParametro('fecha_inicio_emi','fecha_inicio_emi','date');
        $this->setParametro('fecha_limite','fecha_limite','date');
        $this->setParametro('tipo_generacion','tipo_generacion','varchar');
        $this->setParametro('glosa_impuestos','glosa_impuestos','varchar');
        $this->setParametro('id_activida_economica','id_activida_economica','varchar');
        $this->setParametro('llave','llave','codigo_html');
        $this->setParametro('inicial','inicial','integer');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('glosa_empresa','glosa_empresa','varchar');

        $this->setParametro('nro_tramite','nro_tramite','varchar');
        $this->setParametro('nombre_sistema','nombre_sistema','varchar');
        $this->setParametro('leyenda','leyenda','varchar');
        $this->setParametro('rnd','rnd','varchar');


				/*Aumentando para dosificaciones de facturacion por exportacion
				Dev: Ismael Valdivia
				Fecha Mod: 19/04/2021*/
				$this->setParametro('caracteristica','caracteristica','varchar');
				$this->setParametro('titulo','titulo','varchar');
				$this->setParametro('subtitulo','subtitulo','varchar');
				/*************************************************************/

        //Ejecuta la instruccion
        $this->armarConsulta();

        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function alertarDosificacion(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_dosificacion_ime';
        $this->transaccion='VF_DOS_CORR';
        $this->tipo_procedimiento='IME';
        //definicion de variables
        $this->tipo_conexion='seguridad';
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
		/*Aumentando para la dosificacion de Recibos Oficiales (Ismael Valdivia 25/08/2020)*/
		function listarDosificacionRO(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='vef.ft_dosificacion_ro_sel';
        $this->transaccion='VF_DOSI_RO_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_dosificacion_ro','int4');
        $this->captura('id_sucursal','int4');
        $this->captura('final','integer');
        $this->captura('tipo','varchar');
        $this->captura('fecha_dosificacion','date');
        $this->captura('nro_siguiente','int4');
        $this->captura('fecha_inicio_emi','date');
        $this->captura('fecha_limite','date');
        $this->captura('tipo_generacion','varchar');
        $this->captura('inicial','integer');
        $this->captura('estado_reg','varchar');
        $this->captura('id_usuario_ai','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('id_usuario_mod','int4');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('nom_sucursal','text');
        $this->captura('nombre_sucursal','varchar');
        $this->captura('estacion','varchar');
        $this->captura('dias_restante','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


		function insertarDosificacionRO(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_dosificacion_ro_ime';
        $this->transaccion='VF_DOS_RO_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_sucursal','id_sucursal','int4');
        $this->setParametro('final','final','integer');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('fecha_dosificacion','fecha_dosificacion','date');
        $this->setParametro('nro_siguiente','nro_siguiente','int4');
        $this->setParametro('fecha_inicio_emi','fecha_inicio_emi','date');
        $this->setParametro('fecha_limite','fecha_limite','date');
        $this->setParametro('tipo_generacion','tipo_generacion','varchar');
        $this->setParametro('inicial','inicial','integer');
        $this->setParametro('estado_reg','estado_reg','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function modificarDosificacionRO(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_dosificacion_ro_ime';
        $this->transaccion='VF_DOS_RO_MOD';
        $this->tipo_procedimiento='IME';


        //Define los parametros para la funcion
				$this->setParametro('id_dosificacion_ro','id_dosificacion_ro','int4');
        $this->setParametro('id_sucursal','id_sucursal','int4');
				$this->setParametro('final','final','integer');
				$this->setParametro('tipo','tipo','varchar');
				$this->setParametro('fecha_dosificacion','fecha_dosificacion','date');
				$this->setParametro('nro_siguiente','nro_siguiente','int4');
				$this->setParametro('fecha_inicio_emi','fecha_inicio_emi','date');
				$this->setParametro('fecha_limite','fecha_limite','date');
				$this->setParametro('tipo_generacion','tipo_generacion','varchar');
				$this->setParametro('inicial','inicial','integer');
				$this->setParametro('estado_reg','estado_reg','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();

        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
		function eliminarDosificacionRO(){
			//Definicion de variables para ejecucion del procedimiento
			$this->procedimiento='vef.ft_dosificacion_ro_ime';
			$this->transaccion='VF_DOS_RO_ELI';
			$this->tipo_procedimiento='IME';

			//Define los parametros para la funcion
			$this->setParametro('id_dosificacion_ro','id_dosificacion_ro','int4');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
		}
		/***********************************************************************************/

}
?>
