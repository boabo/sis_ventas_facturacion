<?php
/**
 *@package pXP
 *@file    FormCierreCaja.php
 *@author  Gonzalo Sarmiento Sejas
 *@date    28-09-2017
 *@description muestra un formulario que muestra el importe contable con el cual sera registrado el deposito
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.FormCierreCaja=Ext.extend(Phx.frmInterfaz,{
        //ActSave:'../../sis_tesoreria/control/ACTProcesoCaja/importeContableDeposito',
        layout:'fit',
        maxCount:0,
        constructor:function(config){

            this.Grupos = [
                {
                    layout: 'column',
                    border: false,
                    defaults: {
                        border: false
                    },
                    items: [
                        {
                            bodyStyle: 'padding-right:10px;',
                            items: [

                                {
                                    xtype: 'fieldset',
                                    title: 'Total Boletos M/L',
                                    autoHeight: true,
                                    autoWidth: true,
                                    //layout:'hbox',
                                    items: [],
                                    id_grupo:0
                                }
                            ]
                        },
                        {
                            bodyStyle: 'padding-right:10px;',
                            items: [

                                {
                                    xtype: 'fieldset',
                                    title: 'Total Boletos M/E',
                                    autoHeight: true,
                                    autoWidth: true,
                                    //layout:'hbox',
                                    items: [],
                                    id_grupo:1
                                }
                            ]
                        },{
                            bodyStyle: 'padding-right:10px; border-style: none;',
                            items: [
                                {
                                    //xtype: 'fieldset',
                                    //title: 'Total Recibos M/L',
                                    autoHeight: false,
                                    autoWidth: true,
                                    //layout:'hbox',
                                    items: [
                                        {
                                            bodyStyle: 'padding-right:10px; padding-left:10px;',
                                            items: [

                                                {
                                                    xtype: 'fieldset',
                                                    title: 'Total Recibos M/L',
                                                    autoHeight: true,
                                                    autoWidth: true,
                                                    //layout:'hbox',
                                                    items: [],
                                                    id_grupo:4
                                                }
                                            ]
                                        },
                                        {
                                            bodyStyle: 'padding-right:10px; padding-left:10px;',
                                            items: [

                                                {
                                                    xtype: 'fieldset',
                                                    title: 'Total Comisiones',
                                                    autoHeight: true,
                                                    autoWidth: true,
                                                    //layout:'hbox',
                                                    items: [],
                                                    id_grupo:10
                                                }
                                            ]
                                        }],
                                    //id_grupo:4
                                }
                            ]
                        },
                        {
                            bodyStyle: 'padding-right:10px;',
                            items: [

                                {
                                    xtype: 'fieldset',
                                    title: 'Total Facturacion M/L',
                                    autoHeight: true,
                                    autoWidth: true,
                                    //layout:'hbox',
                                    items: [],
                                    id_grupo:2
                                }
                            ]
                        },{
                            bodyStyle: 'padding-right:10px;',
                            items: [

                                {
                                    xtype: 'fieldset',
                                    title: 'Total Facturacion M/E',
                                    autoHeight: true,
                                    autoWidth: true,
                                    //layout:'hbox',
                                    items: [],
                                    id_grupo:3
                                }
                            ]
                        },
                        {
                            bodyStyle: 'padding-right:10px;',
                            items:[

                                {
                                    xtype: 'fieldset',
                                    title: 'Cortes Monedas M/L',
                                    autoHeight: true,
                                    hiden: true,
                                    autoWidth: true,
                                    //layout:'hbox',
                                    items: [],
                                    id_grupo: 6
                                }
                            ]
                        },
                        {
                            bodyStyle: 'padding-right:10px;',
                            items:[

                                {
                                    xtype: 'fieldset',
                                    title: 'Cortes Billetes M/L',
                                    autoHeight: true,
                                    autoWidth: true,
                                    hiden: true,
                                    //layout:'hbox',
                                    items: [],
                                    id_grupo: 7
                                }
                            ]
                        },
                        {
                            bodyStyle: 'padding-right:10px;',
                            items:[

                                {
                                    xtype: 'fieldset',
                                    title: 'Cortes Billetes M/E',
                                    autoHeight: true,
                                    autoWidth: true,
                                    hiden: true,
                                    //layout:'hbox',
                                    items: [],
                                    id_grupo: 8
                                }
                            ]
                        },
                        {
                            bodyStyle: 'padding-right:10px;',
                            items:[

                                {
                                    xtype: 'fieldset',
                                    title: 'Apertura',
                                    autoHeight: true,
                                    autoWidth: true,
                                    hiden: true,
                                    //layout:'hbox',
                                    items: [],
                                    id_grupo: 9
                                }
                            ]
                        },
                        {
                            bodyStyle: 'padding-right:10px;',
                            items:[

                                {
                                    xtype: 'fieldset',
                                    title: 'Cierre',
                                    autoHeight: true,
                                    autoWidth: true,
                                    hiden: true,
                                    //layout:'hbox',
                                    items: [],
                                    id_grupo: 5
                                }
                            ]
                        }
                    ]

                }];

            Phx.vista.FormCierreCaja.superclass.constructor.call(this,config);
            this.init();
            this.iniciarEventos();
            this.obtenerCaja();
        },

        Atributos:[
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_apertura_cierre_caja'
                },
                type:'Field',
                form:true
            },
            {
                config:{
                    name: 'fecha_apertura_cierre',
                    fieldLabel: 'Fecha ',
                    gwidth: 110,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters: { pfiltro:'apcie.fecha', type:'date'},
                grid:true,
                form:false
            },
            /*{
             config: {
             name: 'id_sucursal',
             fieldLabel: 'Sucursal',
             allowBlank: true,
             emptyText: 'Elija una Suc...',
             store: new Ext.data.JsonStore({
             url: '../../sis_ventas_facturacion/control/Sucursal/listarSucursal',
             id: 'id_sucursal',
             root: 'datos',
             sortInfo: {
             field: 'nombre',
             direction: 'ASC'
             },
             totalProperty: 'total',
             fields: ['id_sucursal', 'nombre', 'codigo'],
             remoteSort: true,
             baseParams: {tipo_usuario: 'cajero',par_filtro: 'suc.nombre#suc.codigo'}
             }),
             valueField: 'id_sucursal',
             gdisplayField : 'nombre_sucursal',
             displayField: 'nombre',
             hiddenName: 'id_sucursal',
             tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
             forceSelection: true,
             typeAhead: false,
             triggerAction: 'all',
             lazyRender: true,
             mode: 'remote',
             pageSize: 15,
             queryDelay: 1000,
             minChars: 2,
             width:250,
             resizable:true
             },
             type: 'ComboBox',
             id_grupo: 1,
             form: true,
             grid:true
             },
             {
             config: {
             name: 'id_punto_venta',
             fieldLabel: 'Punto de Venta',
             allowBlank: true,
             emptyText: 'Elija un Pun...',
             store: new Ext.data.JsonStore({
             url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
             id: 'id_punto_venta',
             root: 'datos',
             sortInfo: {
             field: 'nombre',
             direction: 'ASC'
             },
             totalProperty: 'total',
             fields: ['id_punto_venta', 'nombre', 'codigo'],
             remoteSort: true,
             baseParams: {tipo_usuario: 'cajero',par_filtro: 'puve.nombre#puve.codigo'}
             }),
             valueField: 'id_punto_venta',
             displayField: 'nombre',
             gdisplayField: 'nombre_punto_venta',
             hiddenName: 'id_punto_venta',
             tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
             forceSelection: true,
             typeAhead: false,
             triggerAction: 'all',
             lazyRender: true,
             mode: 'remote',
             pageSize: 15,
             queryDelay: 1000,
             gwidth: 150,
             minChars: 2,
             renderer : function(value, p, record) {
             return String.format('{0}', record.data['nombre_punto_venta']);
             },
             width:250,
             resizable:true
             },
             type: 'ComboBox',
             id_grupo: 1,
             filters: {pfiltro: 'puve.nombre',type: 'string'},
             form: true,
             grid:true
             },*/
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_sucursal'
                },
                type:'Field',
                form:true
            },
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_punto_venta'
                },
                type:'Field',
                form:false
            },
            {
                config:{
                    name: 'nombre_sucursal',
                    fieldLabel: 'Sucursal',
                    disabled:true,
                    gwidth: 100
                },
                type: 'ComboBox',
                filters:{pfiltro:'puve.nombre_punto_venta',type:'string'},
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'nombre_punto_venta',
                    fieldLabel: 'Punto de Venta',
                    disabled:true,
                    gwidth: 100
                },
                type: 'ComboBox',
                filters:{pfiltro:'puve.nombre_punto_venta',type:'string'},
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'estado',
                    fieldLabel: 'Estado',
                    gwidth: 100
                },
                type:'TextField',
                filters:{pfiltro:'ven.estado',type:'string'},
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'monto_inicial',
                    fieldLabel: 'Importe Inicial M/L',
                    allowBlank: true,
                    disabled: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo: 9,
                filters:{pfiltro:'apcie.monto_inicial',type:'numeric'},
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_inicial_moneda_extranjera',
                    fieldLabel: 'Importe Inicial M/E',
                    allowBlank: true,
                    disabled: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo: 9,
                filters:{pfiltro:'apcie.monto_inicial_moneda_extranjera',type:'numeric'},
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'obs_apertura',
                    fieldLabel: 'Obs. Apertura',
                    disabled: true,
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 200
                },
                type:'TextArea',
                id_grupo: 9,
                filters:{pfiltro:'apcie.obs_apertura',type:'string'},
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'monto_ca_boleto_bs',
                    fieldLabel: 'Importe Cash Boletos M/L',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:0,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_cc_boleto_bs',
                    fieldLabel: 'Importe Tarjetas Boletos M/L',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:0,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_cte_boleto_bs',
                    fieldLabel: 'Importe Cta Cte Boletos M/L',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:0,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_mco_boleto_bs',
                    fieldLabel: 'Importe MCO Boletos M/L',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:0,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_otro_boleto_bs',
                    fieldLabel: 'Importe Otros Boletos M/L',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:0,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_boleto_moneda_base',
                    fieldLabel: 'Total Boletos M/L',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:0,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_ca_boleto_usd',
                    fieldLabel: 'Importe Cash Boletos M/E',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:1,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_cc_boleto_usd',
                    fieldLabel: 'Importe Tarjetas Boletos M/E',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:1,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_cte_boleto_usd',
                    fieldLabel: 'Importe Cta Cte Boletos M/E',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:1,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_mco_boleto_usd',
                    fieldLabel: 'Importe MCO Boletos M/E',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:1,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_otro_boleto_usd',
                    fieldLabel: 'Importe Otros Boletos M/E',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:1,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_boleto_moneda_usd',
                    fieldLabel: 'Total Boletos Moneda M/E',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:1,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_ca_facturacion_bs',
                    fieldLabel: 'Importe Cash Facturacion M/L',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:2,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_cc_facturacion_bs',
                    fieldLabel: 'Importe Tarjetas Facturacion M/L',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:2,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_cte_facturacion_bs',
                    fieldLabel: 'Importe Cta Cte Facturacion M/L',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:2,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_mco_facturacion_bs',
                    fieldLabel: 'Importe MCO Facturacion M/L',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:2,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_otro_facturacion_bs',
                    fieldLabel: 'Importe Otros Facturacion M/L',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:2,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_moneda_base_fp_facturacion',
                    fieldLabel: 'Total Facturacion M/L',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:2,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_ca_facturacion_usd',
                    fieldLabel: 'Importe Cash Facturacion M/E',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:3,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_cc_facturacion_usd',
                    fieldLabel: 'Importe Tarjetas Facturacion M/E',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:3,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_cte_facturacion_usd',
                    fieldLabel: 'Importe Cta Cte Facturacion M/E',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:3,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_mco_facturacion_usd',
                    fieldLabel: 'Importe MCO Facturacion M/E',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:3,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_otro_facturacion_usd',
                    fieldLabel: 'Importe Otros Facturacion M/E',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:3,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_moneda_ref_fp_facturacion',
                    fieldLabel: 'Total Facturacion Moneda M/E',
                    allowBlank: true,
                    disabled:true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:3,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_ca_recibo_ml',
                    fieldLabel: 'Importe Cash Recibos M/L',
                    allowBlank: true,
                    disabled:false,
                    anchor: '90%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:4,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_cc_recibo_ml',
                    fieldLabel: 'Importe Tarjetas Recibos M/L',
                    allowBlank: true,
                    disabled:false,
                    anchor: '90%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:4,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_recibo_moneda_base',
                    fieldLabel: 'Total Recibos M/L',
                    allowBlank: true,
                    disabled:true,
                    anchor: '90%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:4,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'comisiones_ml',
                    fieldLabel: 'Comisiones M/L',
                    allowBlank: true,
                    disabled:true,
                    anchor: '90%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:10,
                //grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'comisiones_me',
                    fieldLabel: 'Comisiones M/E',
                    allowBlank: true,
                    disabled:true,
                    anchor: '90%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:10,
                //grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'obs_cierre',
                    fieldLabel: 'Obs. Cierre',
                    allowBlank: true,
                    anchor: '90%',
                    gwidth: 150
                },
                type:'TextArea',
                filters:{pfiltro:'apcie.obs_cierre',type:'string'},
                id_grupo:5,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'total_efectivo_ml',
                    fieldLabel: 'Total Efectivo M/L',
                    allowBlank: false,
                    readOnly: true,
                    anchor: '90%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #3cf251;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:5,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'total_efectivo_me',
                    fieldLabel: 'Total Efectivo M/E',
                    allowBlank: false,
                    readOnly: true,
                    anchor: '90%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #3cf251;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:5,
                grid:false,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'arqueo_moneda_local',
                    fieldLabel: 'Arqueo M/L',
                    allowBlank: false,
                    anchor: '90%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                filters:{pfiltro:'apcie.arqueo_moneda_local',type:'numeric'},
                id_grupo:5,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'arqueo_moneda_extranjera',
                    fieldLabel: 'Arqueo M/E',
                    allowBlank: false,
                    anchor: '90%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'

                },
                type:'NumberField',
                filters:{pfiltro:'apcie.arqueo_moneda_extranjera',type:'numeric'},
                id_grupo:5,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'diferencia',
                    fieldLabel: 'Diferencia M/L',
                    allowBlank: true,
                    readOnly: true,
                    anchor: '90%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #3cf251;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:5,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'fecha_hora_cierre',
                    fieldLabel: 'Fecha Hora Cierre',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'monto_moneda_5_ml',
                    fieldLabel: '5',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:6,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_moneda_2_ml',
                    fieldLabel: '2',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:6,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_moneda_1_ml',
                    fieldLabel: '1',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:6,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_moneda_50_ctvs',
                    fieldLabel: '0.50',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:6,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_moneda_20_ctvs',
                    fieldLabel: '0.20',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:6,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_moneda_10_ctvs',
                    fieldLabel: '0.10',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:6,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_billete_200_ml',
                    fieldLabel: '200',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:7,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_billete_100_ml',
                    fieldLabel: '100',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:7,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_billete_50_ml',
                    fieldLabel: '50',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:7,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_billete_20_ml',
                    fieldLabel: '20',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:7,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_billete_10_ml',
                    fieldLabel: '10',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:7,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_billete_100_usd',
                    fieldLabel: '100',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:8,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_billete_50_usd',
                    fieldLabel: '50',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:8,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_billete_20_usd',
                    fieldLabel: '20',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:8,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_billete_10_usd',
                    fieldLabel: '10',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:8,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_billete_5_usd',
                    fieldLabel: '5',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:8,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_billete_2_usd',
                    fieldLabel: '2',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:8,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_billete_1_usd',
                    fieldLabel: '1',
                    allowBlank: true,
                    disabled:false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:8,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'usr_reg',
                    fieldLabel: 'Cajero',
                    gwidth: 100
                },
                type:'TextField',
                filters:{pfiltro:'ven.estado',type:'string'},
                grid:true,
                form:false
            }
        ],

        title:'Cierre Caja',

        obtenerCaja:function(x){
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                // form:this.form.getForm().getEl(),
                url:'../../sis_ventas_facturacion/control/AperturaCierreCaja/listarCierreCaja',
                params:{id_apertura_cierre_caja:this.data.id_apertura_cierre_caja},
                success:this.successCaja,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },

        successCaja:function(resp){
            Phx.CP.loadingHide();

            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if(reg.datos.length=1){
                var total_boletos_ml = parseFloat(reg.datos[0]['efectivo_boletos_ml']) + parseFloat(reg.datos[0]['tarjeta_boletos_ml']) + parseFloat(reg.datos[0]['cuenta_corriente_boletos_ml']) + parseFloat(reg.datos[0]['mco_boletos_ml']) + parseFloat(reg.datos[0]['otros_boletos_ml'] + parseFloat(reg.datos[0]['comisiones_ml']));
                var total_boletos_me = parseFloat(reg.datos[0]['efectivo_boletos_me']) + parseFloat(reg.datos[0]['tarjeta_boletos_me']) + parseFloat(reg.datos[0]['cuenta_corriente_boletos_me']) + parseFloat(reg.datos[0]['mco_boletos_me']) + parseFloat(reg.datos[0]['otros_boletos_me'] + parseFloat(reg.datos[0]['comisiones_me']));
                this.Cmp.id_apertura_cierre_caja.setValue(reg.datos[0]['id_apertura_cierre_caja']);
                //this.Cmp.id_punto_venta.setValue(reg.datos[0]['id_punto_venta']);
                this.Cmp.id_sucursal.setValue(reg.datos[0]['id_sucursal']);
                //this.Cmp.nombre_punto_venta.setValue(reg.datos[0]['nombre_punto_venta']);

                this.Cmp.monto_ca_boleto_bs.setValue(reg.datos[0]['efectivo_boletos_ml']);
                this.Cmp.monto_cc_boleto_bs.setValue(reg.datos[0]['tarjeta_boletos_ml']);
                this.Cmp.monto_cte_boleto_bs.setValue(reg.datos[0]['cuenta_corriente_boletos_ml']);
                this.Cmp.monto_mco_boleto_bs.setValue(reg.datos[0]['mco_boletos_ml']);
                this.Cmp.monto_otro_boleto_bs.setValue(reg.datos[0]['otros_boletos_ml']);
                this.Cmp.monto_boleto_moneda_base.setValue(total_boletos_ml);

                this.Cmp.monto_ca_boleto_usd.setValue(reg.datos[0]['efectivo_boletos_me']);
                this.Cmp.monto_cc_boleto_usd.setValue(reg.datos[0]['tarjeta_boletos_me']);
                this.Cmp.monto_cte_boleto_usd.setValue(reg.datos[0]['cuenta_corriente_boletos_me']);
                this.Cmp.monto_mco_boleto_usd.setValue(reg.datos[0]['mco_boletos_me']);
                this.Cmp.monto_otro_boleto_usd.setValue(reg.datos[0]['otros_boletos_me']);
                this.Cmp.monto_boleto_moneda_usd.setValue(total_boletos_me);

                var total_ventas_ml = parseFloat(reg.datos[0]['efectivo_ventas_ml']) + parseFloat(reg.datos[0]['tarjeta_ventas_ml']) + parseFloat(reg.datos[0]['cuenta_corriente_ventas_ml']) + parseFloat(reg.datos[0]['mco_ventas_ml'])+ parseFloat(reg.datos[0]['otros_ventas_ml']);
                var total_ventas_me = parseFloat(reg.datos[0]['efectivo_ventas_me']) + parseFloat(reg.datos[0]['tarjeta_ventas_me']) + parseFloat(reg.datos[0]['cuenta_corriente_ventas_me']) + parseFloat(reg.datos[0]['mco_ventas_me'])+ parseFloat(reg.datos[0]['otros_ventas_me']);

                this.Cmp.monto_ca_facturacion_bs.setValue(reg.datos[0]['efectivo_ventas_ml']);
                this.Cmp.monto_cc_facturacion_bs.setValue(reg.datos[0]['tarjeta_ventas_ml']);
                this.Cmp.monto_cte_facturacion_bs.setValue(reg.datos[0]['cuenta_corriente_ventas_ml']);
                this.Cmp.monto_mco_facturacion_bs.setValue(reg.datos[0]['mco_ventas_ml']);
                this.Cmp.monto_otro_facturacion_bs.setValue(reg.datos[0]['otros_ventas_ml']);

                this.Cmp.monto_moneda_base_fp_facturacion.setValue(total_ventas_ml);

                this.Cmp.monto_ca_facturacion_usd.setValue(reg.datos[0]['efectivo_ventas_me']);
                this.Cmp.monto_cc_facturacion_usd.setValue(reg.datos[0]['tarjeta_ventas_me']);
                this.Cmp.monto_cte_facturacion_usd.setValue(reg.datos[0]['cuenta_corriente_ventas_me']);
                this.Cmp.monto_mco_facturacion_usd.setValue(reg.datos[0]['mco_ventas_me']);
                this.Cmp.monto_otro_facturacion_usd.setValue(reg.datos[0]['otros_ventas_me']);
                this.Cmp.monto_moneda_ref_fp_facturacion.setValue(total_ventas_me);

                this.Cmp.total_efectivo_ml.setValue(parseFloat(reg.datos[0]['efectivo_boletos_ml']) + parseFloat(reg.datos[0]['efectivo_ventas_ml']) + parseFloat(reg.datos[0]['comisiones_ml'])+ parseFloat(reg.datos[0]['monto_inicial']));
                this.Cmp.total_efectivo_me.setValue(parseFloat(reg.datos[0]['efectivo_boletos_me']) + parseFloat(reg.datos[0]['efectivo_ventas_me']) + parseFloat(reg.datos[0]['comisiones_me'])+ parseFloat(reg.datos[0]['monto_inicial_moneda_extranjera']));

                this.Cmp.monto_inicial.setValue(reg.datos[0]['monto_inicial']);
                this.Cmp.monto_inicial_moneda_extranjera.setValue(reg.datos[0]['monto_inicial_moneda_extranjera']);
                this.Cmp.comisiones_ml.setValue(reg.datos[0]['comisiones_ml']);
                this.Cmp.comisiones_me.setValue(reg.datos[0]['comisiones_me']);

            }else{
                alert('ocurrio error al obtener datos de la caja')
            }
        },

        onSubmit:function(){
            //TODO passar los datos obtenidos del wizard y pasar  el evento save

            if (this.form.getForm().isValid()) {
                if(this.Cmp.diferencia.getValue() >= 1 || this.Cmp.diferencia.getValue() <= -1){
                    alert('Existe diferencia de : '+ this.Cmp.diferencia.getValue());
                }else{
                    this.fireEvent('beforesave',this,this.getValues());
                    this.getValues();
                }
            }
        },

        getValues:function(){
            var me = this;
            var resp = {
                id_apertura_cierre_caja:this.data.id_apertura_cierre_caja,
                id_sucursal:this.Cmp.id_sucursal.getValue(),
                id_punto_venta:this.data.id_punto_venta,
                obs_cierre:this.Cmp.obs_cierre.getValue(),
                arqueo_moneda_local:this.Cmp.arqueo_moneda_local.getValue(),
                arqueo_moneda_extranjera:this.Cmp.arqueo_moneda_extranjera.getValue(),
                monto_inicial:this.Cmp.monto_inicial.getValue(),
                //obs_apertura:this.data.obs_apertura,
                monto_inicial_moneda_extranjera: this.Cmp.monto_inicial_moneda_extranjera.getValue(),
                monto_ca_recibo_ml: this.Cmp.monto_ca_recibo_ml.getValue(),
                monto_cc_recibo_ml: this.Cmp.monto_cc_recibo_ml.getValue()
            }
            return resp;
        },

        iniciarEventos : function () {

            this.Cmp.monto_ca_recibo_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.total_efectivo_ml.setValue(this.Cmp.monto_ca_boleto_bs.getValue()+this.Cmp.monto_ca_facturacion_bs.getValue() + + this.Cmp.monto_inicial.getValue() +newValue);
                    this.Cmp.monto_recibo_moneda_base.setValue(this.Cmp.monto_ca_recibo_ml.getValue() + this.Cmp.monto_cc_recibo_ml.getValue());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_cc_recibo_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.monto_recibo_moneda_base.setValue(this.Cmp.monto_ca_recibo_ml.getValue() + this.Cmp.monto_cc_recibo_ml.getValue());
                }
            }, this);

            this.Cmp.monto_moneda_10_ctvs.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_moneda_20_ctvs.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_moneda_50_ctvs.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_moneda_1_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_moneda_2_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_moneda_5_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_billete_10_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_billete_20_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_billete_50_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_billete_100_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_billete_200_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_billete_1_usd.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_extranjera.setValue(this.calcularArqueoMonedaExtranjera());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_billete_2_usd.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_extranjera.setValue(this.calcularArqueoMonedaExtranjera());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_billete_5_usd.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_extranjera.setValue(this.calcularArqueoMonedaExtranjera());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_billete_10_usd.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_extranjera.setValue(this.calcularArqueoMonedaExtranjera());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_billete_20_usd.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_extranjera.setValue(this.calcularArqueoMonedaExtranjera());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_billete_50_usd.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_extranjera.setValue(this.calcularArqueoMonedaExtranjera());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_billete_100_usd.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_extranjera.setValue(this.calcularArqueoMonedaExtranjera());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.arqueo_moneda_local.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.arqueo_moneda_extranjera.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);
        },

        calcularArqueoMonedaLocal : function () {
            var total = this.Cmp.monto_moneda_10_ctvs.getValue()*0.10 + this.Cmp.monto_moneda_20_ctvs.getValue()*0.20 +
                this.Cmp.monto_moneda_50_ctvs.getValue()*0.50 + this.Cmp.monto_moneda_1_ml.getValue() + this.Cmp.monto_moneda_2_ml.getValue()*2 +
                this.Cmp.monto_moneda_5_ml.getValue()*5 + this.Cmp.monto_billete_10_ml.getValue()*10 + this.Cmp.monto_billete_20_ml.getValue()*20 +
                this.Cmp.monto_billete_50_ml.getValue()*50 + this.Cmp.monto_billete_100_ml.getValue()*100 + this.Cmp.monto_billete_200_ml.getValue()*200;
            return total;
        },

        calcularArqueoMonedaExtranjera : function () {
            var total = this.Cmp.monto_billete_1_usd.getValue() + this.Cmp.monto_billete_2_usd.getValue()*2 +
                this.Cmp.monto_billete_5_usd.getValue()*5 + this.Cmp.monto_billete_10_usd.getValue()*10 + this.Cmp.monto_billete_20_usd.getValue()*20 +
                this.Cmp.monto_billete_50_usd.getValue()*50 + this.Cmp.monto_billete_100_usd.getValue()*100;
            return total;
        },

        calcularDiferencia : function () {
            var total_efectivo = this.Cmp.total_efectivo_ml.getValue() + (this.Cmp.total_efectivo_me.getValue()*6.96);
            var total_arqueo = this.Cmp.arqueo_moneda_local.getValue() + (this.Cmp.arqueo_moneda_extranjera.getValue()*6.96);
            return total_arqueo - total_efectivo;
        }

    })
</script>
