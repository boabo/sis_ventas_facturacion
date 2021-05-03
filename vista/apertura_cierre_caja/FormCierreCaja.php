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

        layout:'fit',
        maxCount:0,
        constructor:function(config){
        this.Grupos =[{
          layout: 'column',
          border: false,
          xtype: 'fieldset',
          frame:true,
          autoScroll: true,

            style:{
                  background:'#FFBE59',
                  //border:'2px solid green',
                  width : '100%',
                 },
                items:/*ABRE*/[
                    {//ABRE PARA PONER ITEMS
                      xtype: 'fieldset',
                      border: false,
                      layout: 'column',
                      region: 'north',
                       style:{
                      //   //background:'#FFBE59',
                         width:'100%',
                        // border:'2px solid red'
                        },
                      //autoWidth: true,
                      //  width: '100%',
                      //height: '100%',
                      padding: '0 0 0 10',
                      items:[
                        {
                         border:false,
                         style:{
                           border:'1px solid #DEDEDE',
                           width : '200px',
                           height : '300px',
                           marginTop:'2px',
                          },
                         items:[
                           {
                               xtype:'fieldset',
                               layout: 'form',
                               title: 'Total Boletos M/L',
                               frame: true,
                               style:{
                                 background:'#FF9595',
                                 height:'300px'
                               },
                               columnWidth: 0.5,
                               items:[],
                               id_grupo:0,
                               border:false,
                               collapsible:false
                           }
                        ]
                      },
                      {
                       bodyStyle: 'padding-right:5px;',
                       autoHeight: true,
                       style:{
                         border:'1px solid #DEDEDE',
                         width : '200px',
                         height : '300px',
                         marginTop:'2px',
                         marginLeft:'2px'
                       },
                       border: false,
                       id:'grupo_boletos_me',
                       items:[
                         {
                             xtype: 'fieldset',
                             layout: 'form',
                             border: false,
                             frame: true,
                             style:{
                               background:'#FF9595',
                               height:'300px'
                             },
                             title: 'Total Boletos M/E',
                             bodyStyle: 'padding:0 10px 0;',
                             columnWidth: 0.5,
                             items: [],
                             id_grupo: 1,
                             collapsible: false
                         }
                      ]
                    },
                    {
                     bodyStyle: 'padding-right:5px;',
                     autoHeight: true,
                     style:{
                      border:'1px solid #DEDEDE',
                       width : '200px',
                       height : '300px',
                       marginTop:'2px',
                       marginLeft:'2px'
                     },
                     border: false,
                     items:[
                       {
                           xtype: 'fieldset',
                           layout: 'form',
                           border: false,
                           frame: true,
                           style:{
                             background:'#FFDC95',
                             height:'300px'
                           },
                           title: 'Total Recibos M/L',
                           bodyStyle: 'padding:0 10px 0;',
                           columnWidth: 0.5,
                           items: [],
                           id_grupo: 4,
                           collapsible: false
                       }
                    ]
                  },
                  {
                   bodyStyle: 'padding-right:5px;',
                   autoHeight: true,
                   style:{
                     border:'1px solid #DEDEDE',
                     width : '200px',
                     height : '300px',
                     marginTop:'2px',
                     marginLeft:'2px'
                   },
                   border: false,
                   id:'grupo_recibos_me',
                   items:[
                     {
                         xtype: 'fieldset',
                         layout: 'form',
                         border: false,
                         frame: true,
                         style:{
                           background:'#FFDC95',
                           height:'300px'
                         },
                         title: 'Total Recibos M/E',
                         bodyStyle: 'padding:0 10px 0;',
                         columnWidth: 0.5,
                         items: [],
                         id_grupo: 11,
                         collapsible: false
                     }
                  ]
                },
                {
                 bodyStyle: 'padding-right:5px;',
                 autoHeight: true,
                 style:{
                   border:'1px solid #DEDEDE',
                   width : '200px',
                   height : '300px',
                   marginTop:'2px',
                   marginLeft:'2px'
                 },
                 border: false,
                 items:[
                   {
                      xtype: 'fieldset',
                      layout: 'form',
                      border: false,
                      frame: true,
                      style:{
                        background:'#B1FF95',
                        height:'300px'
                      },
                      title: 'Total Facturacion M/L',
                      bodyStyle: 'padding:0 10px 0;',
                      columnWidth: 0.5,
                      items: [],
                      id_grupo: 2,
                      collapsible: false
                   }
                ]
              },{
               bodyStyle: 'padding-right:5px;',
               autoHeight: true,
               style:{
                border:'1px solid #DEDEDE',
                 width : '200px',
                 height : '300px',
                 marginTop:'2px',
                 marginLeft:'2px'
               },
               border: false,
               id:'grupo_facturacion_me',
               items:[
                 {
                     xtype: 'fieldset',
                     layout: 'form',
                     border: false,
                     frame: true,
                     style:{
                       background:'#B1FF95',
                       height:'300px'
                     },
                     title: 'Total Facturacion M/E',
                     bodyStyle: 'padding:0 10px 0;',
                     columnWidth: 0.5,
                     items: [],
                     id_grupo: 3,
                     collapsible: false
                 }
              ]
            },
            {
             bodyStyle: 'padding-right:5px;',
             autoHeight: true,
             style:{
               border:'1px solid #DEDEDE',
               width : '200px',
               height : '300px',
               marginTop:'2px',

             },
             border: false,
             items:[
               {
                   xtype: 'fieldset',
                   layout: 'form',
                   border: false,
                   frame: true,
                   style:{
                     background:'#9EC9EA',
                     height:'300px'
                   },
                   title: 'Cortes Monedas M/L',
                   bodyStyle: 'padding:0 10px 0;',
                   columnWidth: 0.5,
                   items: [],
                   id_grupo: 6,
                   collapsible: false
               }
            ]
          },
          {
           bodyStyle: 'padding-right:5px;',
           autoHeight: true,
           style:{
            border:'1px solid #DEDEDE',
             width : '200px',
             height : '300px',
             marginTop:'2px',
             marginLeft:'2px'
           },
           border: false,
           items:[
             {
                 xtype: 'fieldset',
                 layout: 'form',
                 border: false,
                 frame: true,
                 style:{
                   background:'#9EC9EA',
                   height:'300px'
                 },
                 title: 'Cortes Billetes M/L',
                 bodyStyle: 'padding:0 10px 0;',
                 columnWidth: 0.5,
                 items: [],
                 id_grupo: 7,
                 collapsible: false
             }
          ]
        },{
         bodyStyle: 'padding-right:5px;',
         autoHeight: true,
         style:{
          border:'1px solid #DEDEDE',
           width : '200px',
           height : '300px',
           marginTop:'2px',
           marginLeft:'2px'
         },
         id:'grupo_corte_billete',
         border: false,
         items:[
           {
               xtype: 'fieldset',
               layout: 'form',
               border: false,
               frame: true,
               style:{
                 background:'#9EC9EA',
                 height:'300px'
               },
               title: 'Cortes Billetes M/E',
               bodyStyle: 'padding:0 10px 0;',
               columnWidth: 0.5,
               items: [],
               hidden:false,
               id_grupo: 8,
               collapsible: false
           }
        ]
        },{
         bodyStyle: 'padding-right:5px;',
         autoHeight: true,
         style:{
           border:'1px solid #DEDEDE',
           width : '200px',
           height : '196px',
           marginTop:'2px',
           marginLeft:'2px'
         },
         border: false,
         id:'grupo_apertura',
         items:[
           {
               xtype: 'fieldset',
               layout: 'form',
               border: false,
               frame: true,
               id:'grupo_apertura_contenido',
               style:{
                 background:'#95E0FF',
                 height:'196px'
               },
               title: 'Apertura',
               bodyStyle: 'padding:0 10px 0;',
               columnWidth: 0.5,
               items: [],
               id_grupo: 9,
               collapsible: false
           }
        ]
      },{
       bodyStyle: 'padding-right:5px;',
       autoHeight: true,
       style:{
        border:'1px solid #DEDEDE',
         width : '200px',
         height : '100px',
         marginTop : '202px',
         marginLeft:'-203px'
       },
       border: false,
       id:'grupo_comisiones',
       items:[
         {
             xtype: 'fieldset',
             layout: 'form',
             border: false,
             frame: true,
             id:'grupo_comisiones_contenido',
             style:{
               background:'#95E0FF',
               height:'100px'
             },
             title: 'Total Comisiones',
             bodyStyle: 'padding:0 10px 0;',
             columnWidth: 0.5,
             items: [],
             id_grupo: 10,
             collapsible: false
         }
      ]
      },{
       bodyStyle: 'padding-right:5px;',
       autoHeight: true,
       style:{
         border:'1px solid #DEDEDE',
         width : '404px',
         height : '300px',
         marginTop:'2px',
         marginLeft:'2px'
       },
       border: false,
       items:[
         {
            xtype: 'fieldset',
            layout: 'form',
            border: false,
            frame: true,
            style:{
              background:'#91DAFF',
              width : '404px',
              height:'300px',
            },
            title: 'Cierre',
            bodyStyle: 'padding:0 10px 0;',
            columnWidth: 0.5,
            items: [],
            id_grupo: 5,
            collapsible: false
        }
      ]
    }
  ]
}//CIERRA PONER ITEMS
]/*CIERRA*/
}];/*CIERRE FINAL*/

            Phx.vista.FormCierreCaja.superclass.constructor.call(this,config);
            // obtenemos el timpo de cambio
            this.tipo_cambio = 0;
            Ext.Ajax.request({
                url:'../../sis_ventas_facturacion/control/AperturaCierreCaja/getTipoCambio',
                params:{fecha_cambio:this.getValues().fecha_apertura_cierre},
                success: function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    this.tipo_cambio = reg.ROOT.datos.v_tipo_cambio;
                    this.moneda_base = reg.ROOT.datos.v_codigo_moneda;
                    this.tolerancia = reg.ROOT.datos.v_tolerancia;
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
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
                    name: 'id_apertura_cierre_caja',
                    value: -1
                },
                type:'Field',
                form:true
            },

            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'cambio_usd'
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
                    style:{
                      width:'60px'
                    },
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
                    style:{
                      width:'60px'
                    },
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
                    style:{
                      width:'60px'
                    },
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    gwidth: 100,
                    style:{
                      width:'60px'
                    },
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'

                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '80%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '80%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '80%',
                    gwidth: 100,
                    style:{
                      width:'60px'
                    },
                    maxLength:15,
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
                    //anchor: '80%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '80%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '80%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px',
                      background: '#FFF1B8'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
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
                    //anchor: '100%',
                    style:{
                      width:'60px',
                      background: '#FFF1B8'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:4,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_cc_recibo_cta_cte_ml',
                    fieldLabel: 'Importe Cta Cte Recibos M/L',
                    allowBlank: true,
                    disabled:false,
                    //anchor: '100%',
                    style:{
                      width:'60px',
                      background: '#FFF1B8'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:4,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_deposito_recibo_ml',
                    fieldLabel: 'Importe Depósito M/L',
                    allowBlank: true,
                    disabled:false,
                    //anchor: '100%',
                    style:{
                      width:'60px',
                      background: '#FFF1B8'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:4,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_otro_recibo_ml',
                    fieldLabel: 'Importe Otros Recibos M/L',
                    allowBlank: true,
                    disabled:true,
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:4,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            /*Aqui la nueva forma de pago pago electronivo (Ismael Valdivia 12/11/2020)*/
            {
                config:{
                    name: 'monto_pago_externo_recibo_ml',
                    fieldLabel: 'Pago Externo M/L',
                    allowBlank: true,
                    disabled:false,
                    hidden:true,
                    //anchor: '100%',
                    style:{
                      width:'60px',
                      background: '#FFF1B8'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:4,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            /***************************************************************************/
            //nuevo MMV
            {
                config:{
                    name: 'monto_ca_recibo_me',
                    fieldLabel: 'Importe Cash Recibos M/E',
                    allowBlank: true,
                    disabled:false,
                    //anchor: '100%',
                    style:{
                      width:'60px',
                      background: '#FFF1B8'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:11,
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
                    //anchor: '100%',
                    style:{
                      width:'60px',
                      background: '#FFF1B8'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:11,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_cc_recibo_cta_cte_me',
                    fieldLabel: 'Importe Cta Cte Recibos M/E',
                    allowBlank: true,
                    disabled:false,
                    //anchor: '100%',
                    style:{
                      width:'60px',
                      background: '#FFF1B8'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:11,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_deposito_recibo_me',
                    fieldLabel: 'Importe Déposito Recibo M/E',
                    allowBlank: true,
                    disabled:false,
                    //anchor: '100%',
                    style:{
                      width:'60px',
                      background: '#FFF1B8'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:11,
                grid:true,
                form:true,
                valorInicial :0.00
            },

            {
                config:{
                    name: 'monto_otro_recibo_me',
                    fieldLabel: 'Importe Otros Recibos M/E',
                    allowBlank: true,
                    disabled:true,
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:11,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            /*Aqui la nueva forma de pago pago electronivo (Ismael Valdivia 12/11/2020)*/
            {
                config:{
                    name: 'monto_pago_externo_recibo_me',
                    fieldLabel: 'Pago Externo M/E',
                    allowBlank: true,
                    disabled:false,
                    hidden:true,
                    //anchor: '100%',
                    style:{
                      width:'60px',
                      background: '#FFF1B8'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:11,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            /***************************************************************************/

            //
            {
                config:{
                    name: 'monto_recibo_moneda_base',
                    fieldLabel: 'Total Recibos M/L',
                    allowBlank: true,
                    disabled:true,
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    name: 'monto_recibo_moneda_extranjera',
                    fieldLabel: 'Total Recibos M/E',
                    allowBlank: true,
                    disabled:true,
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:11,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    //anchor: '100%',
                    style:{
                      width:'60px'
                    },
                    gwidth: 100,
                    maxLength:15,
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
                    name: 'total_efectivo_ml',
                    fieldLabel: 'Total Efectivo M/L',
                    allowBlank: false,
                    readOnly: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:16,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #3cf251;  background-image: none;'
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
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:16,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #3cf251;  background-image: none;'
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
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:16,
                    readOnly:true,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
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
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:16,
                    readOnly:true,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'

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
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:16,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #3cf251;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:5,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'obs_cierre',
                    fieldLabel: 'Obs. Cierre',
                    allowBlank: true,
                    anchor: '80%',
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
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2
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
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
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
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
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
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:6,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            /***************************aumentando corte de moneda*************************************/
            {
                config:{
                    name: 'monto_moneda_25_ctvs',
                    fieldLabel: '0.25',
                    allowBlank: true,
                    disabled:false,
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:6,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            /*******************************************************************************************/
            {
                config:{
                    name: 'monto_moneda_20_ctvs',
                    fieldLabel: '0.20',
                    allowBlank: true,
                    disabled:false,
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                  //  style: 'background-color: #f2f23c;  background-image: none;'
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
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },          gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:6,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            /********************aumento corte de moneda*********************************/
            {
                config:{
                    name: 'monto_moneda_05_ctvs',
                    fieldLabel: '0.05',
                    allowBlank: true,
                    disabled:false,
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },  gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:6,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_moneda_01_ctvs',
                    fieldLabel: '0.01',
                    allowBlank: true,
                    disabled:false,
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:6,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            /**************************************************************************************/
            /************************************aumento de billetes*/
            {
                config:{
                    name: 'monto_billete_1000_ml',
                    fieldLabel: '1000',
                    allowBlank: true,
                    disabled:false,
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:7,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_billete_500_ml',
                    fieldLabel: '500',
                    allowBlank: true,
                    disabled:false,
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:7,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            /**************************************************************************************/
            {
                config:{
                    name: 'monto_billete_200_ml',
                    fieldLabel: '200',
                    allowBlank: true,
                    disabled:false,
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
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
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                  //  style: 'background-color: #f2f23c;  background-image: none;'
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
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
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
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
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
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:7,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            /************************************aumentando IRVA****************************************/
            {
                config:{
                    name: 'monto_billete_5_ml',
                    fieldLabel: '5',
                    allowBlank: true,
                    disabled:false,
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:7,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_billete_2_ml',
                    fieldLabel: '2',
                    allowBlank: true,
                    disabled:false,
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:7,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_billete_1_ml',
                    fieldLabel: '1',
                    allowBlank: true,
                    disabled:false,
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
                },
                type:'NumberField',
                id_grupo:7,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            /*************************************************************************/
            {
                config:{
                    name: 'monto_billete_100_usd',
                    fieldLabel: '100',
                    allowBlank: true,
                    disabled:false,
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
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
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
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
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
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
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
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
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
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
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
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
                    style:{
                      width:'60px',
                      background: '#C3DFF5'
                    },
                    gwidth: 100,
                    maxLength:15,
                    allowDecimals: true,
                    decimalPrecision : 2,
                    //style: 'background-color: #f2f23c;  background-image: none;'
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
                var total_boletos_ml = parseFloat(reg.datos[0]['efectivo_boletos_ml']) + parseFloat(reg.datos[0]['tarjeta_boletos_ml']) + parseFloat(reg.datos[0]['cuenta_corriente_boletos_ml']) + parseFloat(reg.datos[0]['mco_boletos_ml']) + parseFloat(reg.datos[0]['otros_boletos_ml']) + parseFloat(reg.datos[0]['comisiones_ml']);
                var total_boletos_me = parseFloat(reg.datos[0]['efectivo_boletos_me']) + parseFloat(reg.datos[0]['tarjeta_boletos_me']) + parseFloat(reg.datos[0]['cuenta_corriente_boletos_me']) + parseFloat(reg.datos[0]['mco_boletos_me']) + parseFloat(reg.datos[0]['otros_boletos_me']) + parseFloat(reg.datos[0]['comisiones_me']);
                this.Cmp.id_apertura_cierre_caja.setValue(reg.datos[0]['id_apertura_cierre_caja']);
                //this.Cmp.id_punto_venta.setValue(reg.datos[0]['id_punto_venta']);
                this.Cmp.id_sucursal.setValue(reg.datos[0]['id_sucursal']);
                //this.Cmp.nombre_punto_venta.setValue(reg.datos[0]['nombre_punto_venta']);
                this.Cmp.monto_ca_boleto_bs.setValue(reg.datos[0]['efectivo_boletos_ml']);
                this.Cmp.monto_cc_boleto_bs.setValue(reg.datos[0]['tarjeta_boletos_ml']);
                this.Cmp.monto_cte_boleto_bs.setValue(reg.datos[0]['cuenta_corriente_boletos_ml']);
                this.Cmp.monto_mco_boleto_bs.setValue(reg.datos[0]['mco_boletos_ml']);
                /******Aumentando para recuperar el monto de otras formas de pago*****/
                this.Cmp.monto_otro_boleto_bs.setValue(reg.datos[0]['otros_boletos_ml']);
                /*********************************************************************/
                this.Cmp.monto_boleto_moneda_base.setValue(total_boletos_ml);

                this.Cmp.monto_ca_boleto_usd.setValue(reg.datos[0]['efectivo_boletos_me']);
                this.Cmp.monto_cc_boleto_usd.setValue(reg.datos[0]['tarjeta_boletos_me']);
                this.Cmp.monto_cte_boleto_usd.setValue(reg.datos[0]['cuenta_corriente_boletos_me']);
                this.Cmp.monto_mco_boleto_usd.setValue(reg.datos[0]['mco_boletos_me']);
                /******Aumentando para recuperar el monto de otras formas de pago*****/
                this.Cmp.monto_otro_boleto_usd.setValue(reg.datos[0]['otros_boletos_me']);
                /*********************************************************************/
                this.Cmp.monto_boleto_moneda_usd.setValue(total_boletos_me);

                var total_ventas_ml = parseFloat(reg.datos[0]['efectivo_ventas_ml']) + parseFloat(reg.datos[0]['tarjeta_ventas_ml']) + parseFloat(reg.datos[0]['cuenta_corriente_ventas_ml']) + parseFloat(reg.datos[0]['mco_ventas_ml'] + parseFloat(reg.datos[0]['otros_ventas_ml']));
                var total_ventas_me = parseFloat(reg.datos[0]['efectivo_ventas_me']) + parseFloat(reg.datos[0]['tarjeta_ventas_me']) + parseFloat(reg.datos[0]['cuenta_corriente_ventas_me']) + parseFloat(reg.datos[0]['mco_ventas_me'] + parseFloat(reg.datos[0]['otros_ventas_me']));

                this.Cmp.monto_ca_facturacion_bs.setValue(reg.datos[0]['efectivo_ventas_ml']);
                this.Cmp.monto_cc_facturacion_bs.setValue(reg.datos[0]['tarjeta_ventas_ml']);
                this.Cmp.monto_cte_facturacion_bs.setValue(reg.datos[0]['cuenta_corriente_ventas_ml']);
                this.Cmp.monto_mco_facturacion_bs.setValue(reg.datos[0]['mco_ventas_ml']);

                /******Aumentando para recuperar el monto de otras formas de pago*****/
                this.Cmp.monto_otro_facturacion_bs.setValue(reg.datos[0]['otros_ventas_ml']);
                /*********************************************************************/


                this.Cmp.monto_moneda_base_fp_facturacion.setValue(total_ventas_ml);

                this.Cmp.monto_ca_facturacion_usd.setValue(reg.datos[0]['efectivo_ventas_me']);
                this.Cmp.monto_cc_facturacion_usd.setValue(reg.datos[0]['tarjeta_ventas_me']);
                this.Cmp.monto_cte_facturacion_usd.setValue(reg.datos[0]['cuenta_corriente_ventas_me']);
                this.Cmp.monto_mco_facturacion_usd.setValue(reg.datos[0]['mco_ventas_me']);

                /******Aumentando para recuperar el monto de otras formas de pago*****/
                this.Cmp.monto_otro_facturacion_usd.setValue(reg.datos[0]['otros_ventas_me']);
                /*********************************************************************/

                this.Cmp.monto_moneda_ref_fp_facturacion.setValue(total_ventas_me);

                this.Cmp.total_efectivo_ml.setValue(parseFloat(reg.datos[0]['efectivo_boletos_ml']) + parseFloat(reg.datos[0]['efectivo_ventas_ml']) + parseFloat(reg.datos[0]['efectivo_recibo_ml']));
                this.Cmp.total_efectivo_me.setValue(parseFloat(reg.datos[0]['efectivo_boletos_me']) + parseFloat(reg.datos[0]['efectivo_ventas_me']) + parseFloat(reg.datos[0]['efectivo_recibo_me']));

                this.Cmp.monto_inicial.setValue(reg.datos[0]['monto_inicial']);
                this.Cmp.monto_inicial_moneda_extranjera.setValue(reg.datos[0]['monto_inicial_moneda_extranjera']);
                this.Cmp.comisiones_ml.setValue(reg.datos[0]['comisiones_ml']);
                this.Cmp.comisiones_me.setValue(reg.datos[0]['comisiones_me']);

                /**********************Recuperamos el Recibo************************************/
                var total_recibos_ml = parseFloat(reg.datos[0]['efectivo_recibo_ml']) + parseFloat(reg.datos[0]['tarjeta_recibo_ml']) + parseFloat(reg.datos[0]['cuenta_corriente_recibo_ml']) + parseFloat(reg.datos[0]['deposito_recibo_ml']) + parseFloat(reg.datos[0]['otros_recibo_ml']) + parseFloat(reg.datos[0]['pago_externo_ml']);
                var total_recibos_me = parseFloat(reg.datos[0]['efectivo_recibo_me']) + parseFloat(reg.datos[0]['tarjeta_recibo_me']) + parseFloat(reg.datos[0]['cuenta_corriente_recibo_me']) + parseFloat(reg.datos[0]['deposito_recibo_me']) + parseFloat(reg.datos[0]['otros_recibo_me']) + parseFloat(reg.datos[0]['pago_externo_me']);

                this.Cmp.monto_ca_recibo_ml.setValue(reg.datos[0]['efectivo_recibo_ml']);
                this.Cmp.monto_cc_recibo_ml.setValue(reg.datos[0]['tarjeta_recibo_ml']);
                this.Cmp.monto_deposito_recibo_ml.setValue(reg.datos[0]['deposito_recibo_ml']);
                this.Cmp.monto_pago_externo_recibo_ml.setValue(reg.datos[0]['pago_externo_ml']);
                this.Cmp.monto_cc_recibo_cta_cte_ml.setValue(reg.datos[0]['cuenta_corriente_recibo_ml']);
                /******Aumentando para recuperar el monto de otras formas de pago*****/
                this.Cmp.monto_otro_recibo_ml.setValue(reg.datos[0]['otros_recibo_ml']);
                /*********************************************************************/

                this.Cmp.monto_recibo_moneda_base.setValue(total_recibos_ml);

                this.Cmp.monto_ca_recibo_me.setValue(reg.datos[0]['efectivo_recibo_me']);
                this.Cmp.monto_cc_recibo_me.setValue(reg.datos[0]['tarjeta_recibo_me']);
                this.Cmp.monto_deposito_recibo_me.setValue(reg.datos[0]['deposito_recibo_me']);
                this.Cmp.monto_pago_externo_recibo_me.setValue(reg.datos[0]['pago_externo_me']);
                this.Cmp.monto_cc_recibo_cta_cte_me.setValue(reg.datos[0]['cuenta_corriente_recibo_me']);
                /******Aumentando para recuperar el monto de otras formas de pago*****/
                this.Cmp.monto_otro_recibo_me.setValue(reg.datos[0]['otros_recibo_me']);
                /*********************************************************************/
                this.Cmp.monto_recibo_moneda_extranjera.setValue(total_recibos_me);
                /****************************************************************************/
                this.Cmp.diferencia.setValue(this.calcularDiferencia());

            }else{
                alert('ocurrio error al obtener datos de la caja')
            }
        },

        onSubmit:function(){
            //TODO passar los datos obtenidos del wizard y pasar  el evento save

            if (this.form.getForm().isValid()) {
                if(this.Cmp.diferencia.getValue() >= (this.tolerancia*(1)) || this.Cmp.diferencia.getValue() <= (this.tolerancia*(-1))){
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
                monto_inicial_moneda_extranjera: this.Cmp.monto_inicial_moneda_extranjera.getValue(),

                //monto_ca_recibo_ml: this.Cmp.monto_ca_recibo_ml.getValue(),
                //monto_cc_recibo_ml: this.Cmp.monto_cc_recibo_ml.getValue(),
                fecha_apertura_cierre : this.data.fecha_apertura_cierre,
                tipo :'normal',
                //nacional
                monto_ca_boleto_bs : this.Cmp.monto_ca_boleto_bs.getValue(),
                monto_cc_boleto_bs : this.Cmp.monto_cc_boleto_bs.getValue(),
                monto_cte_boleto_bs : this.Cmp.monto_cte_boleto_bs.getValue(),
                monto_mco_boleto_bs : this.Cmp.monto_mco_boleto_bs.getValue(),
                monto_otro_boleto_bs : this.Cmp.monto_otro_boleto_bs.getValue(),
                //internacional
                monto_ca_boleto_usd : this.Cmp.monto_ca_boleto_usd.getValue(),
                monto_cc_boleto_usd : this.Cmp.monto_cc_boleto_usd.getValue(),
                monto_cte_boleto_usd : this.Cmp.monto_cte_boleto_usd.getValue(),
                monto_mco_boleto_usd : this.Cmp.monto_mco_boleto_usd.getValue(),
                monto_otro_boleto_usd : this.Cmp.monto_otro_boleto_usd.getValue(),

                monto_ca_recibo_ml: this.Cmp.monto_ca_recibo_ml.getValue(),
                monto_ca_recibo_me: this.Cmp.monto_ca_recibo_me.getValue(),
                monto_cc_recibo_ml: this.Cmp.monto_cc_recibo_ml.getValue(),
                monto_cc_recibo_me: this.Cmp.monto_cc_recibo_me.getValue(),
                monto_otro_recibo_ml: this.Cmp.monto_otro_recibo_ml.getValue(),
                monto_otro_recibo_me: this.Cmp.monto_otro_recibo_me.getValue(),

                ///ventas nacioneles

                monto_ca_facturacion_bs: this.Cmp.monto_ca_facturacion_bs.getValue(),
                monto_cc_facturacion_bs: this.Cmp.monto_cc_facturacion_bs.getValue(),
                monto_cte_facturacion_bs: this.Cmp.monto_cte_facturacion_bs.getValue(),
                monto_mco_facturacion_bs: this.Cmp.monto_mco_facturacion_bs.getValue(),
                monto_otro_facturacion_bs: this.Cmp.monto_otro_facturacion_bs.getValue(),
                ///ventas internaciones

                monto_ca_facturacion_usd: this.Cmp.monto_ca_facturacion_usd.getValue(),
                monto_cc_facturacion_usd: this.Cmp.monto_cc_facturacion_usd.getValue(),
                monto_cte_facturacion_usd: this.Cmp.monto_cte_facturacion_usd.getValue(),
                monto_mco_facturacion_usd: this.Cmp.monto_mco_facturacion_usd.getValue(),
                monto_otro_facturacion_usd: this.Cmp.monto_otro_facturacion_usd.getValue(),
                //Comision
                comisiones_ml: this.Cmp.comisiones_ml.getValue(),
                comisiones_me: this.Cmp.comisiones_me.getValue()
            }
            return resp;
        },

        iniciarEventos : function () {

            this.Cmp.monto_ca_recibo_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.total_efectivo_ml.setValue(this.Cmp.monto_ca_boleto_bs.getValue()+this.Cmp.monto_ca_facturacion_bs.getValue() +newValue);
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                    var cal_total=this.Cmp.monto_ca_recibo_ml.getValue()+this.Cmp.monto_cc_recibo_ml.getValue()+this.Cmp.monto_deposito_recibo_ml.getValue()+this.Cmp.monto_cc_recibo_cta_cte_ml.getValue()+this.Cmp.monto_pago_externo_recibo_ml.getValue();
                    this.Cmp.monto_recibo_moneda_base.setValue(cal_total);

                }
            }, this);

            this.Cmp.monto_cc_recibo_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                  var cal_total=this.Cmp.monto_ca_recibo_ml.getValue()+this.Cmp.monto_cc_recibo_ml.getValue()+this.Cmp.monto_deposito_recibo_ml.getValue()+this.Cmp.monto_cc_recibo_cta_cte_ml.getValue()+this.Cmp.monto_pago_externo_recibo_ml.getValue();
                  this.Cmp.monto_recibo_moneda_base.setValue(cal_total);
                }
            }, this);

            this.Cmp.monto_cc_recibo_cta_cte_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                  var cal_total=this.Cmp.monto_ca_recibo_ml.getValue()+this.Cmp.monto_cc_recibo_ml.getValue()+this.Cmp.monto_deposito_recibo_ml.getValue()+this.Cmp.monto_cc_recibo_cta_cte_ml.getValue()+this.Cmp.monto_pago_externo_recibo_ml.getValue();
                  this.Cmp.monto_recibo_moneda_base.setValue(cal_total);
                }
            }, this);

            this.Cmp.monto_deposito_recibo_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                  var cal_total=this.Cmp.monto_ca_recibo_ml.getValue()+this.Cmp.monto_cc_recibo_ml.getValue()+this.Cmp.monto_deposito_recibo_ml.getValue()+this.Cmp.monto_cc_recibo_cta_cte_ml.getValue()+this.Cmp.monto_pago_externo_recibo_ml.getValue();
                  this.Cmp.monto_recibo_moneda_base.setValue(cal_total);
                }
            }, this);

            this.Cmp.monto_pago_externo_recibo_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                  var cal_total=this.Cmp.monto_ca_recibo_ml.getValue()+this.Cmp.monto_cc_recibo_ml.getValue()+this.Cmp.monto_deposito_recibo_ml.getValue()+this.Cmp.monto_cc_recibo_cta_cte_ml.getValue()+this.Cmp.monto_pago_externo_recibo_ml.getValue();
                  this.Cmp.monto_recibo_moneda_base.setValue(cal_total);
                }
            }, this);

            this.Cmp.monto_ca_recibo_me.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.total_efectivo_me.setValue(this.Cmp.monto_ca_boleto_usd.getValue()+this.Cmp.monto_ca_facturacion_usd.getValue() +newValue);
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                    var cal_total=this.Cmp.monto_ca_recibo_me.getValue()+this.Cmp.monto_cc_recibo_me.getValue()+this.Cmp.monto_deposito_recibo_me.getValue()+this.Cmp.monto_cc_recibo_cta_cte_me.getValue()+this.Cmp.monto_pago_externo_recibo_me.getValue();
                    this.Cmp.monto_recibo_moneda_extranjera.setValue(cal_total);
                }
            }, this);

            this.Cmp.monto_cc_recibo_me.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                  var cal_total=this.Cmp.monto_ca_recibo_me.getValue()+this.Cmp.monto_cc_recibo_me.getValue()+this.Cmp.monto_deposito_recibo_me.getValue()+this.Cmp.monto_cc_recibo_cta_cte_me.getValue()+this.Cmp.monto_pago_externo_recibo_me.getValue();
                  this.Cmp.monto_recibo_moneda_extranjera.setValue(cal_total);
                }
            }, this);

            this.Cmp.monto_cc_recibo_cta_cte_me.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                  var cal_total=this.Cmp.monto_ca_recibo_me.getValue()+this.Cmp.monto_cc_recibo_me.getValue()+this.Cmp.monto_deposito_recibo_me.getValue()+this.Cmp.monto_cc_recibo_cta_cte_me.getValue()+this.Cmp.monto_pago_externo_recibo_me.getValue();
                  this.Cmp.monto_recibo_moneda_extranjera.setValue(cal_total);
                }
              }, this);


            this.Cmp.monto_deposito_recibo_me.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                  var cal_total=this.Cmp.monto_ca_recibo_me.getValue()+this.Cmp.monto_cc_recibo_me.getValue()+this.Cmp.monto_deposito_recibo_me.getValue()+this.Cmp.monto_cc_recibo_cta_cte_me.getValue()+this.Cmp.monto_pago_externo_recibo_me.getValue();
                  this.Cmp.monto_recibo_moneda_extranjera.setValue(cal_total);
                }
            }, this);

            this.Cmp.monto_pago_externo_recibo_me.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                  var cal_total=this.Cmp.monto_ca_recibo_me.getValue()+this.Cmp.monto_cc_recibo_me.getValue()+this.Cmp.monto_deposito_recibo_me.getValue()+this.Cmp.monto_cc_recibo_cta_cte_me.getValue()+this.Cmp.monto_pago_externo_recibo_me.getValue();
                  this.Cmp.monto_recibo_moneda_extranjera.setValue(cal_total);
                }
            }, this);

            this.Cmp.monto_moneda_01_ctvs.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_moneda_05_ctvs.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
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

            this.Cmp.monto_moneda_25_ctvs.on('change', function (field, newValue, oldValue) {
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

            this.Cmp.monto_billete_1_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_billete_2_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_billete_5_ml.on('change', function (field, newValue, oldValue) {
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

            this.Cmp.monto_billete_500_ml.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.arqueo_moneda_local.setValue(this.calcularArqueoMonedaLocal());
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                }
            }, this);

            this.Cmp.monto_billete_1000_ml.on('change', function (field, newValue, oldValue) {
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
            var total = this.Cmp.monto_moneda_01_ctvs.getValue()*0.01 + this.Cmp.monto_moneda_05_ctvs.getValue()*0.05 +  this.Cmp.monto_moneda_10_ctvs.getValue()*0.10 + this.Cmp.monto_moneda_20_ctvs.getValue()*0.20 +
                this.Cmp.monto_moneda_25_ctvs.getValue()*0.25 + this.Cmp.monto_moneda_50_ctvs.getValue()*0.50 + this.Cmp.monto_moneda_1_ml.getValue() + this.Cmp.monto_moneda_2_ml.getValue()*2 +
                this.Cmp.monto_moneda_5_ml.getValue()*5 + this.Cmp.monto_billete_1_ml.getValue()*1 + this.Cmp.monto_billete_2_ml.getValue()*2 + this.Cmp.monto_billete_5_ml.getValue()*5 +
                this.Cmp.monto_billete_10_ml.getValue()*10 + this.Cmp.monto_billete_20_ml.getValue()*20 + this.Cmp.monto_billete_50_ml.getValue()*50 + this.Cmp.monto_billete_100_ml.getValue()*100 + this.Cmp.monto_billete_200_ml.getValue()*200 + this.Cmp.monto_billete_500_ml.getValue()*500 +
                this.Cmp.monto_billete_1000_ml.getValue()*1000 ;
            return total;
        },

        calcularArqueoMonedaExtranjera : function () {
            var total = this.Cmp.monto_billete_1_usd.getValue() + this.Cmp.monto_billete_2_usd.getValue()*2 +
                this.Cmp.monto_billete_5_usd.getValue()*5 + this.Cmp.monto_billete_10_usd.getValue()*10 + this.Cmp.monto_billete_20_usd.getValue()*20 +
                this.Cmp.monto_billete_50_usd.getValue()*50 + this.Cmp.monto_billete_100_usd.getValue()*100;
            return total;
        },

        calcularDiferencia : function () {
          // var mon_ext = this.Grupos[0].items[7].items[0];
          // console.log('La moneda base es: ',this);
          if(this.moneda_base == 'BOB'){
            this.Cmp.monto_billete_1000_ml.setVisible(false);
            this.Cmp.monto_billete_500_ml.setVisible(false);
            this.Cmp.monto_billete_1_ml.setVisible(false);
            this.Cmp.monto_billete_2_ml.setVisible(false);
            this.Cmp.monto_billete_5_ml.setVisible(false);
            this.Cmp.monto_moneda_01_ctvs.setVisible(false);
            this.Cmp.monto_moneda_25_ctvs.setVisible(false);
            this.Cmp.monto_moneda_05_ctvs.setVisible(false);

            this.Cmp.monto_ca_boleto_bs.setDisabled(true);
            this.Cmp.monto_boleto_moneda_base.setDisabled(true);
            this.Cmp.monto_cc_boleto_bs.setDisabled(true);
            this.Cmp.monto_cte_boleto_bs.setDisabled(true);
            this.Cmp.monto_mco_boleto_bs.setDisabled(true);
            this.Cmp.monto_boleto_moneda_base.setDisabled(true);

            /*Aqui ocultamos el pago electronico para bolivia no mostrar en el cierre (Ismael Valdivia 16/11/2020)*/
            this.ocultarComponente(this.Cmp.monto_pago_externo_recibo_ml);
            this.ocultarComponente(this.Cmp.monto_pago_externo_recibo_me);
            /******************************************************************************************************/

          }
          else if(this.moneda_base == 'ARS'){
            this.Cmp.monto_moneda_20_ctvs.setVisible(false);
            this.Cmp.monto_moneda_01_ctvs.setVisible(false);
            this.Cmp.monto_billete_2_ml.setVisible(false);
            this.Cmp.monto_billete_1_ml.setVisible(false);

            this.Cmp.monto_ca_boleto_bs.setDisabled(true);
            this.Cmp.monto_boleto_moneda_base.setDisabled(true);
            this.Cmp.monto_cc_boleto_bs.setDisabled(true);
            this.Cmp.monto_cte_boleto_bs.setDisabled(true);
            this.Cmp.monto_mco_boleto_bs.setDisabled(true);
            this.Cmp.monto_boleto_moneda_base.setDisabled(true);

            /*Aqui ocultamos el pago electronico para bolivia no mostrar en el cierre (Ismael Valdivia 16/11/2020)*/
            this.ocultarComponente(this.Cmp.monto_pago_externo_recibo_ml);
            this.ocultarComponente(this.Cmp.monto_pago_externo_recibo_me);
            /******************************************************************************************************/
          }
          else if(this.moneda_base == 'USD'){
            this.Cmp.monto_billete_200_ml.setVisible(false);
            this.Cmp.monto_moneda_20_ctvs.setVisible(false);
            this.Cmp.monto_moneda_50_ctvs.setVisible(false);
            this.Cmp.monto_moneda_1_ml.setVisible(false);
            this.Cmp.monto_moneda_2_ml.setVisible(false);
            this.Cmp.monto_moneda_5_ml.setVisible(false);
            this.Cmp.monto_billete_100_usd.setVisible(false);
            this.Cmp.monto_billete_50_usd.setVisible(false);
            this.Cmp.monto_billete_20_usd.setVisible(false);
            this.Cmp.monto_billete_10_usd.setVisible(false);
            this.Cmp.monto_billete_5_usd.setVisible(false);
            this.Cmp.monto_billete_2_usd.setVisible(false);
            this.Cmp.monto_billete_1_usd.setVisible(false);
            Ext.getCmp('grupo_corte_billete').hide();
            Ext.getCmp('grupo_boletos_me').hide();
            Ext.getCmp('grupo_facturacion_me').hide();
            Ext.getCmp('grupo_recibos_me').hide();

            Ext.getCmp('grupo_apertura').el.dom.style.width='200px';
            Ext.getCmp('grupo_apertura').el.dom.style.height='300px';

            Ext.getCmp('grupo_apertura_contenido').el.dom.style.width='200px';
            Ext.getCmp('grupo_apertura_contenido').el.dom.style.height='300px';

            Ext.getCmp('grupo_comisiones').el.dom.style.width='200px';
            Ext.getCmp('grupo_comisiones').el.dom.style.height='300px';

            Ext.getCmp('grupo_comisiones_contenido').el.dom.style.width='200px';
            Ext.getCmp('grupo_comisiones_contenido').el.dom.style.height='300px';

            Ext.getCmp('grupo_comisiones').el.dom.style.marginLeft='2px';
            Ext.getCmp('grupo_comisiones').el.dom.style.marginTop='2px';


            this.Cmp.monto_ca_boleto_bs.setDisabled(false);
            //this.Cmp.monto_boleto_moneda_base.setDisabled(false);
            this.Cmp.monto_cc_boleto_bs.setDisabled(false);
            this.Cmp.monto_cte_boleto_bs.setDisabled(false);
            this.Cmp.monto_mco_boleto_bs.setDisabled(false);

            this.ocultarComponente(this.Cmp.monto_inicial_moneda_extranjera);
            this.ocultarComponente(this.Cmp.comisiones_me);
            this.ocultarComponente(this.Cmp.total_efectivo_me);
            this.ocultarComponente(this.Cmp.arqueo_moneda_extranjera);


            /*Aqui ocultamos el pago electronico para bolivia no mostrar en el cierre (Ismael Valdivia 16/11/2020)*/
            this.mostrarComponente(this.Cmp.monto_pago_externo_recibo_ml);
            this.mostrarComponente(this.Cmp.monto_pago_externo_recibo_me);
            /******************************************************************************************************/

            //this.Cmp.monto_boleto_moneda_base.setDisabled(false);

            this.Cmp.monto_ca_boleto_bs.on('change', function (field, newValue, oldValue) {
                if (oldValue != newValue) {
                    this.Cmp.total_efectivo_ml.setValue(this.Cmp.monto_ca_recibo_ml.getValue()+this.Cmp.monto_ca_facturacion_bs.getValue() +newValue);
                    this.Cmp.diferencia.setValue(this.calcularDiferencia());
                    var cal_total=this.Cmp.monto_ca_boleto_bs.getValue()+this.Cmp.monto_cc_boleto_bs.getValue()+this.Cmp.monto_cte_boleto_bs.getValue()+this.Cmp.monto_mco_boleto_bs.getValue();
                    this.Cmp.monto_boleto_moneda_base.setValue(cal_total);

                }
            }, this);

            // this.ocultarComponente(mon_ext);
          }

          var total_efectivo = this.Cmp.total_efectivo_ml.getValue() + (this.Cmp.total_efectivo_me.getValue()*this.tipo_cambio);
          var total_arqueo = this.Cmp.arqueo_moneda_local.getValue() + (this.Cmp.arqueo_moneda_extranjera.getValue()*this.tipo_cambio);
          return total_arqueo - total_efectivo;
      }

    })
</script>
