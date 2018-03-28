<?php
/**
 *@package pXP
 *@file gen-MODAperturaCierreCaja.php
 *@author  (jrivera)
 *@date 07-07-2016 14:16:20
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODAperturaCierreCaja extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarAperturaCierreCaja(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='vef.ft_apertura_cierre_caja_sel';
        $this->transaccion='VF_APCIE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_apertura_cierre_caja','int4');
        $this->captura('id_sucursal','int4');
        $this->captura('id_punto_venta','int4');
        $this->captura('id_usuario_cajero','int4');
        $this->captura('id_moneda','int4');
        $this->captura('obs_cierre','text');
        $this->captura('monto_inicial','numeric');
        $this->captura('obs_apertura','text');
        $this->captura('monto_inicial_moneda_extranjera','numeric');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');

        $this->captura('estado','varchar');
        $this->captura('fecha_apertura_cierre','date');
        $this->captura('fecha_hora_cierre','timestamp');
        $this->captura('nombre_punto_venta','varchar');
        $this->captura('nombre_sucursal','varchar');
        $this->captura('arqueo_moneda_local','numeric');
        $this->captura('arqueo_moneda_extranjera','numeric');
        $this->captura('id_entrega_brinks','int4');
        $this->captura('tipo','varchar');
        $this->captura('desc_persona','text');
        $this->captura('modificado','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarCierreCaja(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='vef.ft_apertura_cierre_caja_sel';
        $this->transaccion='VF_CIE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //$this->setCount(false);

        $this->setParametro('fecha','fecha','date');
        //Definicion de la lista del resultado del query
        $this->captura('id_apertura_cierre_caja','int4');
        $this->captura('id_sucursal','int4');
        $this->captura('id_punto_venta','int4');
        $this->captura('nombre_punto_venta','varchar');
        $this->captura('obs_cierre','text');
        $this->captura('monto_inicial','numeric');
        $this->captura('obs_apertura','text');
        $this->captura('monto_inicial_moneda_extranjera','numeric');
        $this->captura('estado','varchar');
        $this->captura('fecha_apertura_cierre','date');
        $this->captura('arqueo_moneda_local','numeric');
        $this->captura('arqueo_moneda_extranjera','numeric');
        //$this->captura('monto_boleto_moneda_base','numeric');
        //$this->captura('monto_boleto_moneda_ref','numeric');
        $this->captura('monto_base_fp_boleto','numeric');
        $this->captura('monto_ref_fp_boleto','numeric');
        $this->captura('monto_base_fp_ventas','numeric');
        $this->captura('monto_ref_fp_ventas','numeric');
        //$this->captura('monto_cc_boleto_bs','numeric');
        //$this->captura('monto_cc_boleto_usd','numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function reporteAperturaCierreCaja(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='vef.ft_apertura_cierre_caja_sel';
        $this->transaccion='VF_REPAPCIE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);//tipo de transaccion

        $this->setParametro('id_apertura_cierre_caja','id_apertura_cierre_caja','int4');

        //Definicion de la lista del resultado del query
        $this->captura('cajero','varchar');
        $this->captura('fecha','varchar');
        $this->captura('pais','varchar');
        $this->captura('estacion','varchar');
        $this->captura('punto_venta','varchar');
        $this->captura('obs_cierre','varchar');
        $this->captura('arqueo_moneda_local','numeric');
        $this->captura('arqueo_moneda_extranjera','numeric');
        $this->captura('monto_inicial','numeric');
        $this->captura('monto_inicial_moneda_extranjera','numeric');
        $this->captura('tipo_cambio','numeric');
        $this->captura('tiene_dos_monedas','varchar');
        $this->captura('moneda_local','varchar');
        $this->captura('moneda_extranjera','varchar');
        $this->captura('cod_moneda_local','varchar');
        $this->captura('cod_moneda_extranjera','varchar');

        $this->captura('efectivo_boletos_ml','numeric');
        $this->captura('efectivo_boletos_me','numeric');
        $this->captura('tarjeta_boletos_ml','numeric');
        $this->captura('tarjeta_boletos_me','numeric');
        $this->captura('cuenta_corriente_boletos_ml','numeric');
        $this->captura('cuenta_corriente_boletos_me','numeric');
        $this->captura('mco_boletos_ml','numeric');
        $this->captura('mco_boletos_me','numeric');
        $this->captura('otros_boletos_ml','numeric');
        $this->captura('otros_boletos_me','numeric');

        $this->captura('efectivo_ventas_ml','numeric');
        $this->captura('efectivo_ventas_me','numeric');
        $this->captura('tarjeta_ventas_ml','numeric');
        $this->captura('tarjeta_ventas_me','numeric');
        $this->captura('cuenta_corriente_ventas_ml','numeric');
        $this->captura('cuenta_corriente_ventas_me','numeric');
        $this->captura('mco_ventas_ml','numeric');
        $this->captura('mco_ventas_me','numeric');
        $this->captura('otros_ventas_ml','numeric');
        $this->captura('otros_ventas_me','numeric');


        $this->captura('comisiones_ml','numeric');
        $this->captura('comisiones_me','numeric');
        $this->captura('monto_ca_recibo_ml','numeric');
        $this->captura('monto_ca_recibo_me','numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();


        //var_dump($this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarAperturaCierreCaja(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_apertura_cierre_caja_ime';
        $this->transaccion='VF_APCIE_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_sucursal','id_sucursal','int4');
        $this->setParametro('id_punto_venta','id_punto_venta','int4');
        $this->setParametro('id_usuario_cajero','id_usuario_cajero','int4');
        $this->setParametro('id_moneda','id_moneda','int4');
        $this->setParametro('obs_cierre','obs_cierre','text');
        $this->setParametro('monto_inicial','monto_inicial','int4');
        $this->setParametro('obs_apertura','obs_apertura','text');
        $this->setParametro('monto_inicial_moneda_extranjera','monto_inicial_moneda_extranjera','int4');
        $this->setParametro('fecha_apertura_cierre','fecha_apertura_cierre','date');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarAperturaCierreCaja(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_apertura_cierre_caja_ime';
        $this->transaccion='VF_APCIE_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_apertura_cierre_caja','id_apertura_cierre_caja','int4');
        $this->setParametro('id_sucursal','id_sucursal','int4');
        $this->setParametro('id_punto_venta','id_punto_venta','int4');
        $this->setParametro('id_usuario_cajero','id_usuario_cajero','int4');
        $this->setParametro('id_moneda','id_moneda','int4');
        $this->setParametro('obs_cierre','obs_cierre','text');
        $this->setParametro('monto_inicial','monto_inicial','numeric');
        $this->setParametro('obs_apertura','obs_apertura','text');
        $this->setParametro('monto_inicial_moneda_extranjera','monto_inicial_moneda_extranjera','numeric');
        // $this->setParametro('monto_ca_recibo_ml','monto_ca_recibo_ml','numeric');
        //$this->setParametro('monto_cc_recibo_ml','monto_cc_recibo_ml','numeric');
        $this->setParametro('accion','accion','varchar');
        $this->setParametro('arqueo_moneda_local','arqueo_moneda_local','numeric');
        $this->setParametro('arqueo_moneda_extranjera','arqueo_moneda_extranjera','numeric');
        $this->setParametro('fecha_apertura_cierre','fecha_apertura_cierre','date');
        //$this->setParametro('monto_billete_100_ml','monto_billete_100_ml','numeric');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('monto_ca_boleto_bs','monto_ca_boleto_bs','numeric');
        $this->setParametro('monto_cc_boleto_bs','monto_cc_boleto_bs','numeric');
        $this->setParametro('monto_cte_boleto_bs','monto_cte_boleto_bs','numeric');
        $this->setParametro('monto_mco_boleto_bs','monto_mco_boleto_bs','numeric');

        $this->setParametro('monto_ca_boleto_usd','monto_ca_boleto_usd','numeric');
        $this->setParametro('monto_cc_boleto_usd','monto_cc_boleto_usd','numeric');
        $this->setParametro('monto_cte_boleto_usd','monto_cte_boleto_usd','numeric');
        $this->setParametro('monto_mco_boleto_usd','monto_mco_boleto_usd','numeric');

        $this->setParametro('monto_ca_recibo_ml','monto_ca_recibo_ml','numeric');
        $this->setParametro('monto_ca_recibo_me','monto_ca_recibo_me','numeric');
        $this->setParametro('monto_cc_recibo_ml','monto_cc_recibo_ml','numeric');
        $this->setParametro('monto_cc_recibo_me','monto_cc_recibo_me','numeric');

        $this->setParametro('monto_ca_facturacion_bs', 'monto_ca_facturacion_bs','numeric');
        $this->setParametro('monto_cc_facturacion_bs', 'monto_cc_facturacion_bs','numeric');
        $this->setParametro('monto_cte_facturacion_bs','monto_cte_facturacion_bs','numeric');
        $this->setParametro('monto_mco_facturacion_bs','monto_mco_facturacion_bs','numeric');

        $this->setParametro('monto_ca_facturacion_usd','monto_ca_facturacion_usd','numeric');
        $this->setParametro('monto_cc_facturacion_usd','monto_cc_facturacion_usd','numeric');
        $this->setParametro('monto_cte_facturacion_usd','monto_cte_facturacion_usd','numeric');
        $this->setParametro('monto_mco_facturacion_usd','monto_mco_facturacion_usd','numeric');

        $this->setParametro('comisiones_ml','comisiones_ml','numeric');
        $this->setParametro('comisiones_me','comisiones_me','numeric');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function abrirAperturaCierreCaja(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_apertura_cierre_caja_ime';
        $this->transaccion='VF_ABRAPCIE_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_apertura_cierre_caja','id_apertura_cierre_caja','int4');
        $this->setParametro('estado','estado','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarAperturaCierreCaja(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_apertura_cierre_caja_ime';
        $this->transaccion='VF_APCIE_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_apertura_cierre_caja','id_apertura_cierre_caja','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function insertarFecha(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_apertura_cierre_caja_ime';
        $this->transaccion='VF_APCIE_FECH';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('fecha','fecha','date');
        $this->setParametro('id_entrega_brinks','id_entrega_brinks','int4');
        $this->setParametro('id_usuario_cajero','id_usuario_cajero','int4');
        $this->setParametro('id_punto_venta','id_punto_venta','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function listarAperturaCierreCajaEntrega(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='vef.ft_apertura_cierre_caja_sel';
        $this->transaccion='VF_APENTRE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->capturaCount('arqueo_moneda_local_total','numeric');
        $this->capturaCount('arqueo_moneda_extranjera_total','numeric');
        $this->captura('id_apertura_cierre_caja','int4');
        $this->captura('id_punto_venta','int4');
        $this->captura('id_usuario_cajero','int4');
        $this->captura('id_entrega_brinks','int4');
        $this->captura('id_usuario_reg','int4');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('fecha_apertura_cierre','date');
        $this->captura('nombre_punto_venta','varchar');
        $this->captura('arqueo_moneda_local','numeric');
        $this->captura('arqueo_moneda_extranjera','numeric');
        $this->captura('obs_cierre','text');
        $this->captura('cajero','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function eleminarFecha(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='vef.ft_apertura_cierre_caja_ime';
        $this->transaccion='VF_APCIE_BOR';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion

        $this->setParametro('id_apertura_cierre_caja','id_apertura_cierre_caja','int4');



        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function EstadoApertura(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='vef.ft_apertura_cierre_caja_sel';
        $this->transaccion='VF_CONTR_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);//tipo de transaccion
        $this->captura('fecha_apertura_cierre','date');
        $this->captura('abierto_co','int4');
        $this->captura('cerrado_co','int4');
        $this->captura('estado','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function DetalleEstadoApertura(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='vef.ft_apertura_cierre_caja_sel';
        $this->transaccion='VF_DETA_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->captura('id_apertura_cierre_caja','int4');
        $this->captura('fecha_apertura_cierre','date');
        $this->captura('estado','varchar');
        $this->captura('nombre','varchar');
        $this->captura('codigo','varchar');
        $this->captura('desc_persona','text');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function listarAperturaCierreCajaVentas(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='vef.ft_apertura_cierre_caja_sel';
        $this->transaccion='VF_LISCAR_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);//tipo de transaccion

        $this->setParametro('id_apertura_cierre_caja','id_apertura_cierre_caja','int4');

        //Definicion de la lista del resultado del query
        $this->captura('cajero','varchar');
        $this->captura('fecha','varchar');
        $this->captura('pais','varchar');
        $this->captura('estacion','varchar');
        $this->captura('punto_venta','varchar');
        $this->captura('obs_cierre','varchar');
        //  $this->captura('arqueo_moneda_local','numeric');
        //  $this->captura('arqueo_moneda_extranjera','numeric');
        $this->captura('monto_inicial','numeric');
        $this->captura('monto_inicial_moneda_extranjera','numeric');
        $this->captura('tipo_cambio','numeric');
        $this->captura('tiene_dos_monedas','varchar');
        $this->captura('moneda_local','varchar');
        $this->captura('moneda_extranjera','varchar');
        $this->captura('cod_moneda_local','varchar');
        $this->captura('cod_moneda_extranjera','varchar');
        $this->captura('efectivo_ventas_ml','numeric');
        $this->captura('efectivo_ventas_me','numeric');
        $this->captura('tarjeta_ventas_ml','numeric');
        $this->captura('tarjeta_ventas_me','numeric');
        $this->captura('cuenta_corriente_ventas_ml','numeric');
        $this->captura('cuenta_corriente_ventas_me','numeric');
        $this->captura('mco_ventas_ml','numeric');
        $this->captura('mco_ventas_me','numeric');
        $this->captura('otros_ventas_ml','numeric');
        $this->captura('otros_ventas_me','numeric');
        $this->captura('monto_ca_boleto_bs','numeric');
        $this->captura('monto_cc_boleto_bs','numeric');
        $this->captura('monto_cte_boleto_bs','numeric');
        $this->captura('monto_mco_boleto_bs','numeric');
        $this->captura('monto_ca_boleto_usd','numeric');
        $this->captura('monto_cc_boleto_usd','numeric');
        $this->captura('monto_cte_boleto_usd','numeric');
        $this->captura('monto_mco_boleto_usd','numeric');
        $this->captura('monto_ca_recibo_ml','numeric');
        $this->captura('monto_cc_recibo_ml','numeric');
        $this->captura('monto_ca_recibo_me','numeric');
        $this->captura('monto_cc_recibo_me','numeric');
        $this->captura('arqueo_moneda_local','numeric');
        $this->captura('arqueo_moneda_extranjera','numeric');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //var_dump($this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function reporteApertura(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='vef.ft_apertura_cierre_caja_sel';
        $this->transaccion='VF_REPORAP_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);//tipo de transaccion

        $this->setParametro('id_apertura_cierre_caja','id_apertura_cierre_caja','int4');

        //Definicion de la lista del resultado del query
        $this->captura('cajero','varchar');
        $this->captura('fecha','varchar');
        $this->captura('pais','varchar');
        $this->captura('estacion','varchar');
        $this->captura('punto_venta','varchar');
        $this->captura('obs_cierre','varchar');
        $this->captura('arqueo_moneda_local','numeric');
        $this->captura('arqueo_moneda_extranjera','numeric');
        $this->captura('monto_inicial','numeric');
        $this->captura('monto_inicial_moneda_extranjera','numeric');
        $this->captura('tipo_cambio','numeric');
        $this->captura('tiene_dos_monedas','varchar');
        $this->captura('moneda_local','varchar');
        $this->captura('moneda_extranjera','varchar');
        $this->captura('cod_moneda_local','varchar');
        $this->captura('cod_moneda_extranjera','varchar');

        $this->captura('efectivo_boletos_ml','numeric');
        $this->captura('efectivo_boletos_me','numeric');
        $this->captura('tarjeta_boletos_ml','numeric');
        $this->captura('tarjeta_boletos_me','numeric');
        $this->captura('cuenta_corriente_boletos_ml','numeric');
        $this->captura('cuenta_corriente_boletos_me','numeric');
        $this->captura('mco_boletos_ml','numeric');
        $this->captura('mco_boletos_me','numeric');
        $this->captura('otros_boletos_ml','numeric');
        $this->captura('otros_boletos_me','numeric');

        $this->captura('efectivo_ventas_ml','numeric');
        $this->captura('efectivo_ventas_me','numeric');
        $this->captura('tarjeta_ventas_ml','numeric');
        $this->captura('tarjeta_ventas_me','numeric');
        $this->captura('cuenta_corriente_ventas_ml','numeric');
        $this->captura('cuenta_corriente_ventas_me','numeric');
        $this->captura('mco_ventas_ml','numeric');
        $this->captura('mco_ventas_me','numeric');
        $this->captura('otros_ventas_ml','numeric');
        $this->captura('otros_ventas_me','numeric');

        $this->captura('comisiones_ml','numeric');
        $this->captura('comisiones_me','numeric');
        $this->captura('monto_ca_recibo_ml','numeric');
        $this->captura('monto_ca_recibo_me','numeric');
        $this->captura('monto_cc_recibo_ml','numeric');
        $this->captura('monto_cc_recibo_me','numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();


        //var_dump($this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }




}
?>
