<?php
/**
 *@package pXP
 *@file gen-SistemaDist.php
 *@author  (rarteaga)
 *@date 20-09-2011 10:22:05
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.VentaApertura = {
        bsave:false,
        require:'../../../sis_ventas_facturacion/vista/venta/Venta.php',
        requireclase:'Phx.vista.Venta',
        title:'Boletos',
        nombreVista: 'VentaVendedor',
        tipo_usuario : 'vendedor',

        constructor: function(config) {
            this.maestro=config.maestro;
            Phx.vista.VentaApertura.superclass.constructor.call(this,config);
            this.tipo_factura = 'manual';
        },
        onReloadPage: function (m) {
            this.maestro = m;
            this.store.baseParams = {id_punto_venta: this.maestro.id_punto_venta, tipo_factura: this.tipo_factura, id_usuario_cajero:this.maestro.id_usuario_cajero,};
            this.store.baseParams.tipo_usuario = this.tipo_usuario;
            this.load({params: {start: 0, limit: 50}});
        }


    };
</script>
