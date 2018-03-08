<?php
/**
 *@package pXP
 *@file    FormCierreCajaCarga.php
 *@author  Gonzalo Sarmiento Sejas
 *@date    28-09-2017
 *@description muestra un formulario que muestra el importe contable con el cual sera registrado el deposito
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.FormCierreCajaCarga=Ext.extend(Phx.frmInterfaz,{
        layout:'fit',
        maxCount:0,
        mod:'',
        efectivo_ventas_ml:0,
        tarjeta_ventas_ml:0,
        cuenta_corriente_ventas_ml:0,
        mco_ventas_ml:0,
        efectivo_ventas_me:0,
        tarjeta_ventas_me:0,
        cuenta_corriente_ventas_me:0,
        mco_ventas_me:0,
        total_ventas_facturadas_me: 0,

        constructor:function(config){
        this.maestro=config
          this.Grupos =[{
                  layout: 'column',
                  items:[
                      {
                          bodyStyle: 'padding-right:10px;',
                          items:[
                             {
                                  xtype:'fieldset',
                                  layout: 'form',
                                  border: false,
                                  title: 'Total Boletos M/L',
                                  bodyStyle: 'padding:0 10px 0;',
                                  columnWidth: 0.5,
                                  items:[],
                                  id_grupo:0,
                                  collapsible:true
                              }
                          ]
                      },
                      {
                          bodyStyle: 'padding-left:10px;',
                          items: [
                              {
                                  xtype: 'fieldset',
                                  layout: 'form',
                                  border: false,
                                  title: 'Total Boletos M/E',
                                  bodyStyle: 'padding:0 10px 0;',
                                  columnWidth: 0.5,
                                  items: [],
                                  id_grupo: 1,
                                  collapsible: true
                              }
                          ]
                      },
                      {
                          bodyStyle: 'padding-right:10px;',
                          items: [
                              {
                                  xtype: 'fieldset',
                                  layout: 'form',
                                  border: false,
                                  title: 'Total Recibos M/L',
                                  bodyStyle: 'padding:0 10px 0;',
                                  columnWidth: 0.5,
                                  items: [],
                                  id_grupo: 4,
                                  collapsible: true
                              }
                          ]
                      },
                      {
                          bodyStyle: 'padding-right:10px;',
                          items: [
                              {
                                  xtype: 'fieldset',
                                  layout: 'form',
                                  border: false,
                                  title: 'Total Facturacion M/L',
                                  bodyStyle: 'padding:0 10px 0;',
                                  columnWidth: 0.5,
                                  items: [],
                                  id_grupo: 2,
                                  collapsible: true
                              }
                          ]
                      },
                      {
                          bodyStyle: 'padding-right:10px;',
                          items: [
                              {
                                  xtype: 'fieldset',
                                  layout: 'form',
                                  border: false,
                                  title: 'Total Facturacion M/E',
                                  bodyStyle: 'padding:0 10px 0;',
                                  columnWidth: 0.5,
                                  items: [],
                                  id_grupo: 3,
                                  collapsible: true
                              }
                          ]
                      },
                      {
                          bodyStyle: 'padding-right:10px;',
                          items: [
                              {
                                  xtype: 'fieldset',
                                  layout: 'form',
                                  border: false,
                                  title: 'Cortes Monedas M/L',
                                  bodyStyle: 'padding:0 10px 0;',
                                  columnWidth: 0.5,
                                  items: [],
                                  id_grupo: 6,
                                  collapsible: true
                              }
                          ]
                      },
                      {
                          bodyStyle: 'padding-right:10px;',
                          items: [
                              {
                                  xtype: 'fieldset',
                                  layout: 'form',
                                  border: false,
                                  title: 'Cortes Billetes M/L',
                                  bodyStyle: 'padding:0 10px 0;',
                                  columnWidth: 0.5,
                                  items: [],
                                  id_grupo: 7,
                                  collapsible: true
                              }
                          ]
                      },
                      {
                          bodyStyle: 'padding-right:10px;',
                          items: [
                              {
                                  xtype: 'fieldset',
                                  layout: 'form',
                                  border: false,
                                  title: 'Cortes Billetes M/E',
                                  bodyStyle: 'padding:0 10px 0;',
                                  columnWidth: 0.5,
                                  items: [],
                                  id_grupo: 8,
                                  collapsible: true
                              }
                          ]
                      },
                      {
                          bodyStyle: 'padding-right:10px;',
                          items: [
                              {
                                  xtype: 'fieldset',
                                  layout: 'form',
                                  border: false,
                                  title: 'Apertura',
                                  bodyStyle: 'padding:0 10px 0;',
                                  columnWidth: 0.5,
                                  items: [],
                                  id_grupo: 9,
                                  collapsible: true
                              }
                          ]
                      },
                      {
                          bodyStyle: 'padding-right:10px;',
                          items: [
                              {
                                  xtype: 'fieldset',
                                  layout: 'form',
                                  border: false,
                                  title: 'Total Comisiones',
                                  bodyStyle: 'padding:0 10px 0;',
                                  columnWidth: 0.5,
                                  items: [],
                                  id_grupo: 10,
                                  collapsible: true
                              }
                          ]
                      },
                      {
                          bodyStyle: 'padding-right:10px;',
                          items: [
                              {
                                  xtype: 'fieldset',
                                  layout: 'form',
                                  border: false,
                                  title: 'Cierre',
                                  bodyStyle: 'padding:0 10px 0;',
                                  columnWidth: 0.5,
                                  items: [],
                                  id_grupo: 5,
                                  collapsible: true
                              }
                          ]
                      }

                  ]
              }];
            Phx.vista.FormCierreCajaCarga.superclass.constructor.call(this,config);
            this.init();
            this.mod = this.maestro.data.modificado;
            this.iniciarEventos();
            this.calcularBoleto();
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
                    disabled:false,
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
                    disabled:false,
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
                    disabled:false,
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
                form:false,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_boleto_moneda_base',
                    fieldLabel: 'Total Boletos M/L',
                    allowBlank: true,
                    readOnly: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #A6EFB3;  background-image: none;'
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
                    disabled:false,
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
                    disabled:false,
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
                    disabled:false,
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
                form:false,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_boleto_moneda_usd',
                    fieldLabel: 'Total Boletos Moneda M/E',
                    allowBlank: true,
                    readOnly: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    style: 'background-color: #A6EFB3;  background-image: none;'
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
                form:false,
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
                form:false,
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
            //
            {
                config:{
                    name: 'monto_ca_recibo_me',
                    fieldLabel: 'Importe Cash Recibos M/E',
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
                    name: 'monto_cc_recibo_me',
                    fieldLabel: 'Importe Tarjetas Recibos M/E',
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
                form:false,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'comisiones_ml',
                    fieldLabel: 'Comisiones M/L',
                    allowBlank: true,
                    disabled:false,
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
                    disabled:false,
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
                    maxLength:16,
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
                    maxLength:16,
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
                    maxLength:16,
                    readOnly:true,
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
                    maxLength:16,
                    readOnly:true,
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
                    maxLength:16,
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
                url:'../../sis_ventas_facturacion/control/AperturaCierreCaja/listarAperturaCierreCajaVentas',
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
                if (this.mod == 'no'){
                      //Datos de Apertura
                      this.Cmp.id_apertura_cierre_caja.setValue(reg.datos[0]['id_apertura_cierre_caja']);
                      this.Cmp.id_sucursal.setValue(reg.datos[0]['id_sucursal']);
                      //Totales Ventas
                      var total_ventas_ml = parseFloat(reg.datos[0]['efectivo_ventas_ml']) + parseFloat(reg.datos[0]['tarjeta_ventas_ml']) + parseFloat(reg.datos[0]['cuenta_corriente_ventas_ml']) + parseFloat(reg.datos[0]['mco_ventas_ml']);
                      var total_ventas_me = parseFloat(reg.datos[0]['efectivo_ventas_me']) + parseFloat(reg.datos[0]['tarjeta_ventas_me']) + parseFloat(reg.datos[0]['cuenta_corriente_ventas_me']) + parseFloat(reg.datos[0]['mco_ventas_me']);
                      //Ventas Nacional
                      this.Cmp.monto_ca_facturacion_bs.setValue(reg.datos[0]['efectivo_ventas_ml']);
                      this.Cmp.monto_cc_facturacion_bs.setValue(reg.datos[0]['tarjeta_ventas_ml']);
                      this.Cmp.monto_cte_facturacion_bs.setValue(reg.datos[0]['cuenta_corriente_ventas_ml']);
                      this.Cmp.monto_mco_facturacion_bs.setValue(reg.datos[0]['mco_ventas_ml']);
                      this.Cmp.monto_moneda_base_fp_facturacion.setValue(total_ventas_ml);
                      //Ventas Internacional
                      this.Cmp.monto_ca_facturacion_usd.setValue(reg.datos[0]['efectivo_ventas_me']);
                      this.Cmp.monto_cc_facturacion_usd.setValue(reg.datos[0]['tarjeta_ventas_me']);
                      this.Cmp.monto_cte_facturacion_usd.setValue(reg.datos[0]['cuenta_corriente_ventas_me']);
                      this.Cmp.monto_mco_facturacion_usd.setValue(reg.datos[0]['mco_ventas_me']);
                      this.Cmp.monto_moneda_ref_fp_facturacion.setValue(total_ventas_me);
                      //Total Arqueo
                      this.Cmp.total_efectivo_ml.setValue(parseFloat(reg.datos[0]['efectivo_ventas_ml']) +  parseFloat(reg.datos[0]['tarjeta_ventas_ml']) +  parseFloat(reg.datos[0]['cuenta_corriente_ventas_ml'] )+  parseFloat( reg.datos[0]['mco_ventas_ml']));
                      this.Cmp.total_efectivo_me.setValue(parseFloat(reg.datos[0]['efectivo_ventas_me']) +  parseFloat(reg.datos[0]['tarjeta_ventas_me']) +  parseFloat(reg.datos[0]['cuenta_corriente_ventas_me'] )+  parseFloat( reg.datos[0]['mco_ventas_me']));
                      //Recuperar Monto Nacional
                      this.efectivo_ventas_ml = parseFloat(reg.datos[0]['efectivo_ventas_ml']);
                      this.tarjeta_ventas_ml =  parseFloat(reg.datos[0]['tarjeta_ventas_ml']);
                      this.cuenta_corriente_ventas_ml =  parseFloat(reg.datos[0]['cuenta_corriente_ventas_ml']);
                      this.mco_ventas_ml =  parseFloat(reg.datos[0]['mco_ventas_ml']);
                      //Recuperar Monto Nacional
                      this.efectivo_ventas_me = parseFloat(reg.datos[0]['efectivo_ventas_me']);
                      this.tarjeta_ventas_me = parseFloat(reg.datos[0]['tarjeta_ventas_me']);
                      this.cuenta_corriente_ventas_me = parseFloat(reg.datos[0]['cuenta_corriente_ventas_me']);
                      this.mco_ventas_me = parseFloat(reg.datos[0]['mco_ventas_me']);
                      this.Cmp.diferencia.setValue(this.calcularDiferencia());

                  }else{
                      //Datos de Apertura
                      this.Cmp.id_apertura_cierre_caja.setValue(reg.datos[0]['id_apertura_cierre_caja']);
                      this.Cmp.id_sucursal.setValue(reg.datos[0]['id_sucursal']);
                      //Totales Boletos
                      var total_boletos_ml = parseFloat(reg.datos[0]['monto_ca_boleto_bs']) + parseFloat(reg.datos[0]['monto_cc_boleto_bs']) + parseFloat(reg.datos[0]['monto_cte_boleto_bs']);
                      var total_boletos_me = parseFloat(reg.datos[0]['monto_ca_boleto_usd']) + parseFloat(reg.datos[0]['monto_cc_boleto_usd']) + parseFloat(reg.datos[0]['monto_cte_boleto_usd']);
                      //Boletos Nacional
                      this.Cmp.monto_ca_boleto_bs.setValue(reg.datos[0]['monto_ca_boleto_bs']);
                      this.Cmp.monto_cc_boleto_bs.setValue(reg.datos[0]['monto_cc_boleto_bs']);
                      this.Cmp.monto_cte_boleto_bs.setValue(reg.datos[0]['monto_cte_boleto_bs']);
                      this.Cmp.monto_boleto_moneda_base.setValue(total_boletos_ml);
                      //Boletos Internacional
                      this.Cmp.monto_ca_boleto_usd.setValue(reg.datos[0]['monto_ca_boleto_usd']);
                      this.Cmp.monto_cc_boleto_usd.setValue(reg.datos[0]['monto_cc_boleto_usd']);
                      this.Cmp.monto_cte_boleto_usd.setValue(reg.datos[0]['monto_cte_boleto_usd']);
                      this.Cmp.monto_boleto_moneda_usd.setValue(total_boletos_me);
                      //recibo nacional
                      this.Cmp.monto_ca_recibo_ml.setValue(reg.datos[0]['monto_ca_recibo_ml']);
                      this.Cmp.monto_cc_recibo_ml.setValue(reg.datos[0]['monto_cc_recibo_ml']);
                      //recibo Internacional
                      this.Cmp.monto_ca_recibo_me.setValue(reg.datos[0]['monto_ca_recibo_me']);
                      this.Cmp.monto_cc_recibo_me.setValue(reg.datos[0]['monto_cc_recibo_me']);
                      //venta total
                      var total_ventas_ml = parseFloat(reg.datos[0]['efectivo_ventas_ml']) + parseFloat(reg.datos[0]['tarjeta_ventas_ml']) + parseFloat(reg.datos[0]['cuenta_corriente_ventas_ml']) + parseFloat(reg.datos[0]['mco_ventas_ml']);
                      var total_ventas_me = parseFloat(reg.datos[0]['efectivo_ventas_me']) + parseFloat(reg.datos[0]['tarjeta_ventas_me']) + parseFloat(reg.datos[0]['cuenta_corriente_ventas_me']) + parseFloat(reg.datos[0]['mco_ventas_me']);
                      //venta nacional
                      this.Cmp.monto_ca_facturacion_bs.setValue(reg.datos[0]['efectivo_ventas_ml']);
                      this.Cmp.monto_cc_facturacion_bs.setValue(reg.datos[0]['tarjeta_ventas_ml']);
                      this.Cmp.monto_cte_facturacion_bs.setValue(reg.datos[0]['cuenta_corriente_ventas_ml']);
                      this.Cmp.monto_mco_facturacion_bs.setValue(reg.datos[0]['mco_ventas_ml']);
                      this.Cmp.monto_moneda_base_fp_facturacion.setValue(total_ventas_ml);
                      //venta Internacional
                      this.Cmp.monto_ca_facturacion_usd.setValue(reg.datos[0]['efectivo_ventas_me']);
                      this.Cmp.monto_cc_facturacion_usd.setValue(reg.datos[0]['tarjeta_ventas_me']);
                      this.Cmp.monto_cte_facturacion_usd.setValue(reg.datos[0]['cuenta_corriente_ventas_me']);
                      this.Cmp.monto_mco_facturacion_usd.setValue(reg.datos[0]['mco_ventas_me']);
                      this.Cmp.monto_moneda_ref_fp_facturacion.setValue(total_ventas_me);

                      this.efectivo_ventas_ml = parseFloat(reg.datos[0]['efectivo_ventas_ml']);
                      this.tarjeta_ventas_ml =  parseFloat(reg.datos[0]['tarjeta_ventas_ml']);
                      this.cuenta_corriente_ventas_ml = parseFloat(reg.datos[0]['cuenta_corriente_ventas_ml']);
                      this.mco_ventas_ml =  parseFloat(reg.datos[0]['mco_ventas_ml']);

                      this.efectivo_ventas_me = parseFloat(reg.datos[0]['efectivo_ventas_me']);
                      this.tarjeta_ventas_me = parseFloat(reg.datos[0]['tarjeta_ventas_me']);
                      this.cuenta_corriente_ventas_me = parseFloat(reg.datos[0]['cuenta_corriente_ventas_me']);
                      this.mco_ventas_me =  parseFloat(reg.datos[0]['mco_ventas_me']);

                      this.Cmp.total_efectivo_ml.setValue(parseFloat(total_boletos_ml) + parseFloat(total_ventas_ml) + parseFloat(reg.datos[0]['monto_ca_recibo_ml']));
                      this.Cmp.total_efectivo_me.setValue(parseFloat(total_boletos_me) + parseFloat(total_ventas_me) + parseFloat(reg.datos[0]['monto_ca_recibo_me']));

                      this.Cmp.diferencia.setValue(this.calcularDiferencia());
              }

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
            console.log(this.Cmp.monto_ca_facturacion_bs.getValue());
            var resp = {
                id_apertura_cierre_caja:this.data.id_apertura_cierre_caja,
                id_sucursal:this.Cmp.id_sucursal.getValue(),
                id_punto_venta:this.data.id_punto_venta,
                obs_cierre:this.Cmp.obs_cierre.getValue(),
                arqueo_moneda_local:this.Cmp.arqueo_moneda_local.getValue(),
                arqueo_moneda_extranjera:this.Cmp.arqueo_moneda_extranjera.getValue(),
                monto_inicial:this.Cmp.monto_inicial.getValue(),
                monto_inicial_moneda_extranjera: this.Cmp.monto_inicial_moneda_extranjera.getValue(),
                //monto_ca_recibo_ml: this.Cmp.monto_ca_recibo_ml.getValue(),
                //monto_cc_recibo_ml: this.Cmp.monto_cc_recibo_ml.getValue(),
                fecha_apertura_cierre : this.data.fecha_apertura_cierre,
                tipo :'carga',
                //nacional
                monto_ca_boleto_bs : this.Cmp.monto_ca_boleto_bs.getValue(),
                monto_cc_boleto_bs : this.Cmp.monto_cc_boleto_bs.getValue(),
                monto_cte_boleto_bs : this.Cmp.monto_cte_boleto_bs.getValue(),
                //internacional
                monto_ca_boleto_usd : this.Cmp.monto_ca_boleto_usd.getValue(),
                monto_cc_boleto_usd : this.Cmp.monto_cc_boleto_usd.getValue(),
                monto_cte_boleto_usd : this.Cmp.monto_cte_boleto_usd.getValue(),

                monto_ca_recibo_ml: this.Cmp.monto_ca_recibo_ml.getValue(),
                monto_ca_recibo_me: this.Cmp.monto_ca_recibo_me.getValue(),
                monto_cc_recibo_ml: this.Cmp.monto_cc_recibo_ml.getValue(),
                monto_cc_recibo_me: this.Cmp.monto_cc_recibo_me.getValue(),

                ///ventas nacioneles

                monto_ca_facturacion_bs: this.Cmp.monto_ca_facturacion_bs.getValue(),
                monto_cc_facturacion_bs: this.Cmp.monto_cc_facturacion_bs.getValue(),
                monto_cte_facturacion_bs: this.Cmp.monto_cte_facturacion_bs.getValue(),
                monto_mco_facturacion_bs: this.Cmp.monto_mco_facturacion_bs.getValue(),

                ///ventas internaciones

                monto_ca_facturacion_usd: this.Cmp.monto_ca_facturacion_usd.getValue(),
                monto_cc_facturacion_usd: this.Cmp.monto_cc_facturacion_usd.getValue(),
                monto_cte_facturacion_usd: this.Cmp.monto_cte_facturacion_usd.getValue(),
                //Comision
                comisiones_ml: this.Cmp.comisiones_ml.getValue(),
                comisiones_me: this.Cmp.comisiones_me.getValue()



            }
            return resp;
        },
        calcularBoleto :function () {

            //Moneda Nacional
            this.Cmp.monto_ca_boleto_bs.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.monto_boleto_moneda_base.setValue(this.Cmp.monto_ca_boleto_bs.getValue() + this.Cmp.monto_cc_boleto_bs.getValue() + this.Cmp.monto_cte_boleto_bs.getValue());
                    this.Cmp.total_efectivo_ml.setValue(parseFloat(this.calcularTotalBoletos())  );
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);
            this.Cmp.monto_cc_boleto_bs.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.monto_boleto_moneda_base.setValue(this.Cmp.monto_ca_boleto_bs.getValue() + this.Cmp.monto_cc_boleto_bs.getValue() + this.Cmp.monto_cte_boleto_bs.getValue());
                    this.Cmp.total_efectivo_ml.setValue(parseFloat(this.calcularTotalBoletos()));
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_cte_boleto_bs.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.monto_boleto_moneda_base.setValue(this.Cmp.monto_ca_boleto_bs.getValue() + this.Cmp.monto_cc_boleto_bs.getValue() + this.Cmp.monto_cte_boleto_bs.getValue());
                    this.Cmp.total_efectivo_ml.setValue(parseFloat(this.calcularTotalBoletos()));
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);
            this.Cmp.comisiones_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.total_efectivo_ml.setValue(parseFloat(this.calcularTotalBoletos()));
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);
            /// Moneda Internacional

            this.Cmp.monto_ca_boleto_usd.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.monto_boleto_moneda_usd.setValue(this.Cmp.monto_ca_boleto_usd.getValue() + this.Cmp.monto_cc_boleto_usd.getValue() + this.Cmp.monto_cte_boleto_usd.getValue());
                    this.Cmp.total_efectivo_me.setValue(parseFloat(this.calcularTotalBoletos_uds()));
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);
            this.Cmp.monto_cc_boleto_usd.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.monto_boleto_moneda_usd.setValue(this.Cmp.monto_ca_boleto_usd.getValue() + this.Cmp.monto_cc_boleto_usd.getValue() + this.Cmp.monto_cte_boleto_usd.getValue());
                    this.Cmp.total_efectivo_me.setValue(parseFloat(this.calcularTotalBoletos_uds()));
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_cte_boleto_usd.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.monto_boleto_moneda_usd.setValue(this.Cmp.monto_ca_boleto_usd.getValue() + this.Cmp.monto_cc_boleto_usd.getValue() + this.Cmp.monto_cte_boleto_usd.getValue());
                    this.Cmp.total_efectivo_me.setValue(parseFloat(this.calcularTotalBoletos_uds()));
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);
            this.Cmp.comisiones_me.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.total_efectivo_me.setValue(parseFloat(this.calcularTotalBoletos_uds()));
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);



        },

        iniciarEventos : function () {

            this.Cmp.monto_ca_recibo_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.total_efectivo_ml.setValue(this.calcularTotalBoletos() + newValue);
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_ca_recibo_me.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.total_efectivo_me.setValue(this.calcularTotalBoletos_uds() + newValue);
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_cc_recibo_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    //this.Cmp.monto_recibo_moneda_base.setValue(this.Cmp.monto_ca_recibo_ml.getValue() + this.Cmp.monto_cc_recibo_ml.getValue());
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
        },
        calcularTotalBoletos : function () {
            var BoletoTotal = this.Cmp.monto_ca_boleto_bs.getValue() + this.Cmp.monto_cc_boleto_bs.getValue() + this.Cmp.monto_cte_boleto_bs.getValue()+ this.Cmp.comisiones_ml.getValue() + this.efectivo_ventas_ml + this.tarjeta_ventas_ml + this.cuenta_corriente_ventas_ml+ this.mco_ventas_ml;
            return BoletoTotal;
        },
        calcularTotalBoletos_uds : function () {
            var BoletoTotal = this.Cmp.monto_ca_boleto_usd.getValue() + this.Cmp.monto_cc_boleto_usd.getValue() + this.Cmp.monto_cte_boleto_usd.getValue()+ this.Cmp.comisiones_me.getValue()  + this.efectivo_ventas_me + this.tarjeta_ventas_me + this.cuenta_corriente_ventas_me + this.mco_ventas_me;
            return BoletoTotal;
        }


    })
</script>
