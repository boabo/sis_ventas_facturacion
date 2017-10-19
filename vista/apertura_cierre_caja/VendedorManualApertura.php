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
    Phx.vista.VendedorManualApertura = {
        require:'../../../sis_ventas_facturacion/vista/apertura_cierre_caja/VentaApertura.php',
        requireclase:'Phx.vista.VentaApertura',
        title: 'Venta',
        nombreVista: 'VentaManueal',
        tipo_factura:'Manual',
        punto: 'puntoVenta',

        constructor: function(config) {
            this.maestro=config.maestro;
            Phx.vista.VendedorManualApertura.superclass.constructor.call(this,config);
            this.finCons = true;
            var dataPadre = Phx.CP.getPagina(this.idContenedorPadre).getSelectedData();
            if(dataPadre){
                this.onEnablePanel(this, dataPadre);
            }
            else
            {
                this.bloquearMenus();
            }
        } ,
        gruposBarraTareas:[
            {name:'finalizado',title:'<H1 align="center"><i class="fa fa-eye"></i> Finalizados</h1>',grupo:1,height:0},
            {name:'anulado',title:'<H1 align="center"><i class="fa fa-eye"></i> Anulados</h1>',grupo:2,height:0}
        ],
        actualizarSegunTab: function(name, indice){
            if(this.finCons){
                this.store.baseParams.pes_estado = name;
                this.load({params:{start:0, limit:this.tam_pag}});
            }
        },
        beditGroups: [0],
        bdelGroups:  [0],
        bactGroups:  [0,1,2],
        btestGroups: [0],
        bexcelGroups: [0,1,2],
        arrayDefaultColumHidden:['estado_reg','usuario_ai','fecha_reg','fecha_mod','usr_reg','usr_mod','cod_control','nroaut'],
        rowExpander: new Ext.ux.grid.RowExpander({
            tpl : new Ext.Template(
                '<br>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Nro Autorización:&nbsp;&nbsp;</b> {nroaut}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha de Registro:&nbsp;&nbsp;</b> {fecha_reg:date("d/m/Y")}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha Ult. Modificación:&nbsp;&nbsp;</b> {fecha_mod:date("d/m/Y")}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Creado por:&nbsp;&nbsp;</b> {usr_reg}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Modificado por:&nbsp;&nbsp;</b> {usr_mod}</p><br>'
            )
        }),
        onReloadPage: function (m) {
            this.maestro = m;
            console.log('id :',this.maestro.fecha_apertura_cierre);
            // this.store.baseParams = {id_usuario_cajero:this.maestro.id_usuario_cajero};
            this.store.baseParams={tipo_interfaz:this.nombreVista,punto:this.punto,id_punto_venta:this.maestro.id_punto_venta,fecha: "''"+this.maestro.fecha_apertura_cierre.dateFormat('d/m/Y')+"''",tipo_factura:"''"+this.tipo_factura+"''",id_usuario_cajero:this.maestro.id_usuario_cajero};
            this.store.baseParams.pes_estado = 'finalizado';
            this.load({params: {start: 0, limit: 50}});
        },
        loadValoresIniciales:function() {
            Phx.vista.VendedorManualApertura.superclass.loadValoresIniciales.call(this);
            this.Cmp.id_punto_venta.setValue(this.maestro.id_punto_venta);
        }


    };
</script>
