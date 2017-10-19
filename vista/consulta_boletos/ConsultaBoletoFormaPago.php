<?php
/**
 *@package pXP
 *@file
 *@author  (MMV)
 *@date 24-07-2017 14:48:34
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ConsultaBoletoFormaPago = {
        require: '../../../sis_obingresos/vista/boleto_forma_pago/BoletoFormaPago.php',
        requireclase: 'Phx.vista.BoletoFormaPago',
        title: 'BoletoFormaPago',
        nombreVista: 'ConsultaBoletoFormaPago',
        constructor: function (config) {
            Phx.vista.ConsultaBoletoFormaPago.superclass.constructor.call(this, config);
            this.store.baseParams={tipo_interfaz:this.nombreVista};
        },
        bnew:false,
        bedit:false,
        bdel:false
    }
</script>