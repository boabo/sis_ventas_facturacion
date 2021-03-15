<?php
/**
 * @package pXP
 * @file gen-DetalleRepDepositos.php
 * @author  (Ismael Valdivia)
 * @date 01-12-2020 08:30:00
 * @description Archivo con la interfaz de usuario que permite generar el reporte de las facturas
 */

header("content-type: text/javascript; charset=UTF-8");
?>


<style>
.punto_venta {
    background-color: #319DFD;
    font-size: 20px;
}
.total_pv {
    background-color: #F4FD31;
}
.totales {
    background-color: #92E176;
}
</style>

<script>
    var ini = null;
    var fin = null;

    var formato = null;
    var punto_venta = null;
    var concepto = null;



    Phx.vista.DetalleRepDepositos = Ext.extend(Phx.gridInterfaz, {
        title: 'Mayor',

        viewConfig: {
            //stripeRows: false,
            autoFill: true,
            getRowClass: function (record) {
                if (record.data.tipo_deposito == 'cabecera') {
                  return 'punto_venta';
                } else if (record.data.tipo_deposito == 'total') {
                  return 'total_pv';
                } else if (record.data.tipo_deposito == '') {
                  return 'totales';
                }
            },
    listener: {
        render: this.createTooltip
    },

        },




        constructor: function (config) {
            var me = this;
            this.maestro = config.maestro;

          //Agrega combo de moneda

            this.Atributos = [
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_venta'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        name: 'fecha_venta',
                        fieldLabel: 'Fecha-Venta',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 700,
                        maxLength: 1000,
                        //format: 'd/m/Y',
                        renderer:function (value,p,record){
                          if (record.data.tipo_reg != 'summary' && record.data.tipo_deposito != 'total' && record.data.tipo_deposito != 'cabecera'){
                            var fecha = value.replaceAll('-', ',');
                            value = new Date(fecha);
                            return value?value.dateFormat('d/m/Y'):'';
                          } else if (record.data.tipo_deposito == 'cabecera'){
                            var fecha = value.replaceAll('-', ',');
                            value = new Date(fecha);
                            return '<b><font>FECHA DE VENTA '+value.dateFormat('d/m/Y')+' </font></b>';
                          }else if (record.data.tipo_deposito == 'total'){
                            return '';
                          } else if (record.data.tipo_reg == 'summary') {
                            return '<b><p style="font-size:15px; color:blue; text-align:right; text-decoration: border-top:2px;">Total General:</p></b>';

                          }
                        }
                  },
                    type:'TextField',
                    //bottom_filter: true,
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                /*************************************************/
                {
                    config: {
                        name: 'importe_ml',
                        fieldLabel: 'Venta M/L',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 700,
                        maxLength: 1000,
                        hidden:true,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('<div style="color:blue; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                            }
                            else{
                                return  String.format('<div style="font-size:15px; text-align:right; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_importe_ml,'0,000.00'));
                            }
                        },
                    },
                    type: 'NumberField',
                    bottom_filter: true,
                    filters: {pfiltro: 'vent.nro_factura', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'importe_usd',
                        fieldLabel: 'Venta M/E',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 700,
                        maxLength: 1000,
                        hidden:true,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('<div style="color:green; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                            }
                            else{
                                return  String.format('<div style="font-size:15px; text-align:right; color:green;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_importe_me,'0,000.00'));
                            }
                        },
                    },
                    type: 'NumberField',
                    bottom_filter: true,
                    filters: {pfiltro: 'vent.nro_factura', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                          name: 'deposito_ml',
                        fieldLabel: 'Importe Depósito M/L',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 700,
                        maxLength: 1000,
                        hidden:true,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('<div style="color:blue; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                            }
                            else{
                                return  String.format('<div style="font-size:15px; text-align:right; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_deposito_ml,'0,000.00'));
                            }
                        },
                    },
                    type: 'NumberField',
                    bottom_filter: true,
                    filters: {pfiltro: 'vent.nro_factura', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'deposito_me',
                        fieldLabel: 'Importe Depósito M/E',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 700,
                        maxLength: 1000,
                        hidden:true,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('<div style="color:green; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                            }
                            else{
                                return  String.format('<div style="font-size:15px; text-align:right; color:green;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_deposito_me,'0,000.00'));
                            }
                        },
                    },
                    type: 'NumberField',
                    bottom_filter: true,
                    filters: {pfiltro: 'vent.nro_factura', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'tipo_cambio',
                        fieldLabel: 'T/C',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 700,
                        maxLength: 1000,
                        hidden:true,
                        renderer:function (value,p,record){
                          return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));

                        },
                    },
                    type: 'NumberField',
                    bottom_filter: true,
                    filters: {pfiltro: 'vent.nro_factura', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'total_venta_ml',
                        fieldLabel: 'Total Venta M/L',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 700,
                        maxLength: 1000,
                        hidden:true,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('<div style="color:blue; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                            }
                            else{
                                return  String.format('<div style="font-size:15px; text-align:right; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.totales_venta_ml,'0,000.00'));
                            }
                        },
                    },
                    type: 'NumberField',
                    bottom_filter: true,
                    filters: {pfiltro: 'vent.nro_factura', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'total_venta_me',
                        fieldLabel: 'Total Depos M/L',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 700,
                        maxLength: 1000,
                        hidden:true,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('<div style="color:green; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                            }
                            else{
                                return  String.format('<div style="font-size:15px; text-align:right; color:green;"><b>{0}<b></div>', Ext.util.Format.number(record.data.totales_venta_me,'0,000.00'));
                            }
                        },
                    },
                    type: 'NumberField',
                    bottom_filter: true,
                    filters: {pfiltro: 'vent.nro_factura', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'diferencia',
                        fieldLabel: 'Saldo / Diferencia',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 700,
                        maxLength: 1000,
                        hidden:true,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('<div style="color:blue; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                            }
                            else{
                                return  String.format('<div style="font-size:15px; text-align:right; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_diferencia,'0,000.00'));
                            }
                        },
                    },
                    type: 'NumberField',
                    bottom_filter: true,
                    filters: {pfiltro: 'vent.nro_factura', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
/**********************************************************************/
                {
                    config: {
                        name: 'nro_deposito',
                        fieldLabel: 'Nro. Boleta',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 700,
                        maxLength: 1000,
                        renderer:function (value,p,record){
                          if (record.data.tipo_deposito == 'total'){
                            return '<b>'+value+'</b>';
                          } else{
                            return value;
                          }
                        }
                    },
                    type: 'TextField',
                    bottom_filter: true,
                    filters: {pfiltro: 'vent.nro_factura', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'fecha_deposito',
                        fieldLabel: 'Fecha Deposito',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 400,
                        //galign: 'right ',
                        maxLength: 200,
                        renderer:function (value,p,record){

                         if (value != null && value != '') {
                           if (record.data.tipo_deposito != 'total'){
                             var fecha = value.replaceAll('-', ',');
                             value = new Date(fecha);
                             return value?value.dateFormat('d/m/Y'):'';
                           } else if (record.data.tipo_deposito == 'total'){
                             return '';
                           }
                         } else {
                           return '';
                         }

                        }
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'importe_ml',
                        fieldLabel: 'Importe M/L',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 300,
                        galign: 'right ',
                        maxLength: 100,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('<div style="color:blue; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                            }
                            else{
                                return  String.format('<div style="font-size:15px; text-align:right; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_importe_ml,'0,000.00'));
                            }
                        },
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'importe_usd',
                        fieldLabel: 'Importe USD',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 300,
                        galign: 'right ',
                        maxLength: 100,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('<div style="color:green; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                            }
                            else{
                                return  String.format('<div style="font-size:15px; text-align:right; color:green;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_importe_me,'0,000.00'));
                            }
                        },
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'cuenta_bancaria',
                        fieldLabel: 'Cuenta Banco',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 400,
                        //galign: 'right ',
                        maxLength: 100,
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'cajero',
                        fieldLabel: 'Cajero',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 300,
                        //galign: 'right ',
                        maxLength: 100,
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'usuario_registro',
                        fieldLabel: 'Registrado por',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 300,
                        //galign: 'right ',
                        maxLength: 100,
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'observaciones',
                        fieldLabel: 'Observaciones',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 300,
                        //galign: 'right ',
                        maxLength: 100,
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'punto_venta',
                        fieldLabel: 'Punto de Venta',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 900,
                        //galign: 'right ',
                        maxLength: 100,
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
            ];


            //llama al constructor de la clase padre
            Phx.vista.DetalleRepDepositos.superclass.constructor.call(this, config);

            /********************Aumentando boton para sacar reporte libro mayor*******************************/
            this.addButton('btnImprimirReporteExcel', {
              text: '<center>Generar Reporte Excel</center>',
              iconCls: 'bexcel',
              disabled: false,
              handler: this.ReporteEXCEL,
              tooltip: '<b>Generar Reporte Excel'
            });
            /***********************************************************************************************/


            this.grid.getTopToolbar().disable();
            this.grid.getBottomToolbar().disable();
            this.init();
            this.tbar.el.dom.style.background='#86CFFF';
            this.bbar.el.dom.style.background='#86CFFF';
        		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#EFFBFF';
        		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#86CFFF';
            this.iniciarEventos();


        },


        tam_pag: 50,

        ActList: '../../sis_ventas_facturacion/control/ReporteDepositos/listarReporteDepositos',
        id_store: 'id_venta',
        fields: [
            //{name:'fecha_factura', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name: 'fecha_venta', type: 'varchar'},
            {name: 'nro_deposito', type: 'varchar'},
            {name: 'fecha_deposito', type: 'varchar'},
            {name: 'importe_ml', type: 'numeric'},
            {name: 'importe_usd', type: 'numeric'},
            {name: 'cuenta_bancaria', type: 'varchar'},
            {name: 'cajero', type: 'varchar'},
            {name: 'usuario_registro', type: 'varchar'},
            {name: 'observaciones', type: 'varchar'},
            {name: 'tipo_deposito', type: 'varchar'},
            {name: 'punto_venta', type: 'varchar'},
            {name: 'deposito_ml', type: 'numeric'},
            {name: 'deposito_me', type: 'numeric'},
            {name: 'tipo_cambio', type: 'numeric'},
            {name: 'total_venta_ml', type: 'numeric'},
            {name: 'total_venta_me', type: 'numeric'},
            {name: 'diferencia', type: 'numeric'},
            {name:'tipo_reg', type: 'string'},
            {name:'total_importe_ml', type: 'numeric'},
            {name:'total_importe_me', type: 'numeric'},
            {name:'total_deposito_ml', type: 'numeric'},
            {name:'total_deposito_me', type: 'numeric'},
            {name:'totales_venta_ml', type: 'numeric'},
            {name:'totales_venta_me', type: 'numeric'},
            {name:'total_diferencia', type: 'numeric'},
        ],

        sortInfo: {
            field: 'fecha',
            direction: 'ASC'
        },
        bdel: true,
        bsave: false,
        loadValoresIniciales: function () {
            Phx.vista.DetalleRepDepositos.superclass.loadValoresIniciales.call(this);

        },

        onReloadPage: function (param) {
            //Se obtiene la gestión en función de la fecha del comprobante para filtrar partidas, cuentas, etc.
            var me = this;
            this.initFiltro(param);

            if (this.store.baseParams.formato_reporte == 'RESUMEN DE DEPÓSITOS BANCARIOS') {
                this.cm.setHidden(2, false);
                this.cm.setHidden(3, false);
                this.cm.setHidden(4, false);
                this.cm.setHidden(5, false);
                this.cm.setHidden(6, false);
                this.cm.setHidden(7, false);
                this.cm.setHidden(8, false);
                this.cm.setHidden(9, false);

                this.cm.setHidden(10, true);
                this.cm.setHidden(11, true);
                this.cm.setHidden(12, true);
                this.cm.setHidden(13, true);
                this.cm.setHidden(14, true);
                this.cm.setHidden(15, true);
                this.cm.setHidden(16, true);
                this.cm.setHidden(17, true);
                this.cm.setHidden(18, true);





            } else if (this.store.baseParams.formato_reporte == 'REPORTE DETALLE DE DEPÓSITOS') {
                this.cm.setHidden(2, true);
                this.cm.setHidden(3, true);
                this.cm.setHidden(4, true);
                this.cm.setHidden(5, true);
                this.cm.setHidden(6, true);
                this.cm.setHidden(7, true);
                this.cm.setHidden(8, true);
                this.cm.setHidden(9, true);

                this.cm.setHidden(10, false);
                this.cm.setHidden(11, false);
                this.cm.setHidden(12, false);
                this.cm.setHidden(13, false);
                this.cm.setHidden(14, false);
                this.cm.setHidden(15, false);
                this.cm.setHidden(16, false);
                this.cm.setHidden(17, false);
                this.cm.setHidden(18, false);
            }
        },

        initFiltro: function (param) {
            this.store.baseParams = param;
            this.load({params: {start: 0, limit: this.tam_pag}});
        },

        ReporteEXCEL: function () {
              Phx.CP.loadingShow();
              Ext.Ajax.request({
                  url: '../../sis_ventas_facturacion/control/ReporteDepositos/reporteDepositosExcel',
                  params: {
                      desde: this.store.baseParams.desde,
                      hasta: this.store.baseParams.hasta,
                      id_punto_venta: this.store.baseParams.id_punto_venta,
                      nombre_pv: this.store.baseParams.nombre_pv,
                      formato_reporte: this.store.baseParams.formato_reporte,
                  },
                  success: this.successExport,
                  failure: this.conexionFailure,
                  timeout: this.timeout,
                  scope: this
              });

        },
        /***************************************************************************************/
        postReloadPage: function (data) {
            ini = data.id_auxiliar;
            fin = data.codigo_auxiliar;


        },

        bnew: false,
        bedit: false,
        bdel: false,
        bexcel:false,
      	btest:false
    })
</script>
