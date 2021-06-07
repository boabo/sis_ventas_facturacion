<?php
/**
*@package pXP
*@file    FormVariasFormasPago.php
*@author  Ismael Valdivia Aranibar
*@date    4-12-2020
*@description Registrar Varias Formas de Pago
*/
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.FormVariasFormasPago=Ext.extend(Phx.frmInterfaz,{
    ActSave:'../../sis_ventas_facturacion/control/VentaFacturacion/insertarFormasPago',
    tam_pag: 10,
    layout: 'fit',
    tabEnter: true,
    autoScroll: false,
    breset: false,
    labelSubmit: '<div><img src="../../../lib/imagenes/facturacion/imprimir.png" style="width:45px; vertical-align: middle;"> <span style="vertical-align: middle; font-size:25px; font-weight:bold; color:#1479B8; text-shadow: 3px 0px 0px #000000;">GENERAR</span></div>',
    storeFormaPago : false,
    fwidth : '9%',
    cantidadAllowDecimals: false,
    constructor:function(config)
    {


        this.addEvents('beforesave');
        this.addEvents('successsave');
        this.buildComponentesDetalle();
        this.buildDetailGrid();
        this.buildGrupos();
        Phx.vista.FormVariasFormasPago.superclass.constructor.call(this,config);
        this.init();
        this.onNew();
        this.iniciarEventos();

    },


    buildComponentesDetalle: function () {

            this.detCmp =
                     {
                    'id_moneda': new Ext.form.TextField({
                      name: 'id_moneda',
                      fieldLabel: '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Moneda</span>',
                      allowBlank: false,
                      width : 450,
                      listWidth:250,
                      resizable:true,
                      style: {
                           background: '#EFFFD6',
                           color: 'red',
                           fontWeight:'bold'
                         },
                      emptyText: 'Moneda a pagar...',
                      store: new Ext.data.JsonStore({
                          url: '../../sis_parametros/control/Moneda/listarMoneda',
                          id: 'id_moneda',
                          root: 'datos',
                          sortInfo: {
                              field: 'moneda',
                              direction: 'ASC'
                          },
                          totalProperty: 'total',
                          fields: ['id_moneda', 'codigo', 'moneda', 'codigo_internacional'],
                          remoteSort: true,
                          baseParams: {filtrar: 'si',par_filtro: 'moneda.moneda#moneda.codigo#moneda.codigo_internacional'}
                      }),
                      valueField: 'id_moneda',
                      gdisplayField : 'codigo_internacional',
                      displayField: 'codigo_internacional',
                      hiddenName: 'id_moneda',
                      tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;"><b style="color:black;">Moneda:</b> <b>{moneda}</b></p><p style="color:red;"><b style="color:black;">Código:</b> <b>{codigo_internacional}</b></p></div></tpl>',
                      forceSelection: true,
                      typeAhead: false,
                      triggerAction: 'all',
                      lazyRender: true,
                      mode: 'remote',
                      pageSize: 15,
                      queryDelay: 1000,
                      //disabled:true,
                      minChars: 2
                    }),
                    'id_medio_pago': new Ext.form.ComboBox({
                                            name: 'id_medio_pago',
                                            fieldLabel: '<img src="../../../lib/imagenes/facturacion/BolsaCompraColores.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Producto/Servicio</span>',
                                            allowBlank: false,
                                            emptyText: 'Productos...',
                                            store: new Ext.data.JsonStore({
                                                url: '../../sis_obingresos/control/MedioPagoPw/listarMedioPagoPw',
                                                id: 'id_medio_pago',
                                                root: 'datos',
                                                sortInfo: {
                                                    field: 'name',
                                                    direction: 'ASC'
                                                },
                                                totalProperty: 'total',
                                                fields: ['id_medio_pago_pw', 'name', 'fop_code'],
                                                remoteSort: true,
                                                baseParams: {par_filtro: 'mppw.name#fp.fop_code', emision:'FACTCOMP'}
                                            }),
                                            valueField: 'id_medio_pago_pw',
                                            displayField: 'name',
                                            gdisplayField: 'name',
                                            hiddenName: 'id_medio_pago_pw',
                                            tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Medio de Pago: <font color="Blue">{name}</font></b></p><b><p>Codigo: <font color="red">{fop_code}</font></b></p></div></tpl>',
                                            forceSelection: true,
                                            typeAhead: false,
                                            triggerAction: 'all',
                                            lazyRender: true,
                                            mode: 'remote',
                                            pageSize: 15,
                                            queryDelay: 1000,
                                            gwidth: 150,
                                            listWidth:250,
                                            resizable:true,
                                            minChars: 2

                                         }),
                    'id_auxiliar': new Ext.form.TextField({
                        name: 'id_auxiliar',
                        fieldLabel: 'Cuenta Corriente',
                        allowBlank: false,
                        anchor: '90%',
                        maxLength:50
                    }),

                    'num_tarjeta': new Ext.form.TextField({
                        name: 'num_tarjeta',
                        fieldLabel: 'Nro. Tarjeta',
                        allowBlank: false,
                        anchor: '80%',
                        maxLength:5000
                    }),
                     'codigo_autorizacion': new Ext.form.TextArea({
                         name: 'codigo_autorizacion',
                         fieldLabel: 'Codigo Tarjeta',
                         allowBlank: false,
                         anchor: '100%',
                         maxLength:5000
                     }),

                     'mco': new Ext.form.TextArea({
                         name: 'mco',
                         fieldLabel: 'Nro MCO',
                         allowBlank: false,
                         anchor: '100%',
                         maxLength:5000
                     }),

                     'monto_total': new Ext.form.TextArea({
                         name: 'mco',
                         fieldLabel: 'Nro MCO',
                         allowBlank: false,
                         anchor: '100%',
                         maxLength:5000
                     })

                }
        },


        buildDetailGrid:function () {
                var Items = Ext.data.Record.create([{
                    name: 'cantidad_sol',
                    type: 'int'
                }
                ]);
                this.mestore = new Ext.data.JsonStore({
                    url: '../../sis_gestion_materiales/control/DetalleSol/listarDetalleSol',
                    id: 'id_detalle',
                    root: 'datos',
                    totalProperty: 'total',
                    fields: ['id_detalle','id_solicitud','precio', 'cantidad_sol',
                        'id_unidad_medida','descripcion','nro_parte_alterno','moneda','referencia',
                        'nro_parte','codigo','desc_descripcion','tipo','explicacion_detallada_part'
                    ],remoteSort: true,
                    baseParams: {dir:'ASC',sort:'id_detalle',limit:'100',start:'0'}
                });



                this.editorDetail = new Ext.ux.grid.RowEditor({

                    });

                this.summary = new Ext.ux.grid.GridSummary();

                this.megrid = new Ext.grid.EditorGridPanel({
                    layout: 'fit',
                    store:  this.mestore,
                    region: 'center',
                    split: true,
                    border: false,
                    plain: true,
                    plugins: [ this.summary],
                    stripeRows: true,
                    tbar: [
                    {
                    text: '<div style="font-weight:bold; font-size:15px;"><img src="../../../lib/imagenes/facturacion/Editar.svg" style="width:30px; vertical-align: middle;"> Editar FP</div>',
                    scope: this,
                    id:'botonEditar',
                      handler : function(){
                        var index = this.megrid.getSelectionModel().getSelectedCell();
                        var rec = this.mestore.getAt(index[0]);
                        this.formularioEditar(rec);
                        }
                    },
                    {
                    text: '<div style="font-weight:bold; font-size:15px;"><img src="../../../lib/imagenes/facturacion/anadir.png" style="width:30px; vertical-align: middle;"> Agregar FP</div>',
                    scope: this,
                    id:'botonAgregar2',
                      handler : function(){
                        this.formularioAgregar();
                        }
                    },
                    {
                        text: '<div style="font-weight:bold; font-size:15px;"><img src="../../../lib/imagenes/facturacion/eliminar.png" style="width:30px; vertical-align: middle;"> Eliminar</div>',
                        scope:this,
                        id:'botonEliminar2',
                        handler : function(){
                          var index = this.megrid.getSelectionModel().getSelectedCell();
                          if (!index) {
                              return false;
                          }
                          var rec = this.mestore.getAt(index[0]);
                          this.mestore.remove(rec);
                          this.obtenersuma();
                        }
                    }],

                    columns: [
                        new Ext.grid.RowNumberer(),
                        {
                            header: 'Moneda a Pagar',
                            dataIndex: 'id_moneda',
                            align: 'center',
                            width: 165,
                            renderer:function(value, p, record){
                              return String.format('{0}', record.data['desc_moneda']);
                            },
                            //editor: this.detCmp.id_moneda
                        },
                        {
                            header: 'Medio Pago',
                            dataIndex: 'id_medio_pago',
                            align: 'center',
                            width: 165,
                            renderer:function(value, p, record){
                              return String.format('{0}', record.data['desc_medio_pago']);
                            },
                            //editor: this.detCmp.id_medio_pago
                        },
                        {
                            header: 'Cuenta Corriente',
                            dataIndex: 'id_auxiliar',
                            align: 'center',
                            width: 165,
                            renderer:function(value, p, record){
                              return String.format('{0}', record.data['desc_auxiliar']);
                            },
                            //editor: this.detCmp.id_auxiliar
                        },
                        {
                            header: 'Nro. Tarjeta',
                            dataIndex: 'num_tarjeta',
                            align: 'center',
                            width: 180,
                            //editor: this.detCmp.num_tarjeta
                        },
                        {
                            header: 'Cod. Tarjeta',
                            dataIndex: 'codigo_autorizacion',
                            align: 'center',
                            width: 210,
                            //editor: this.detCmp.codigo_autorizacion
                        },
                        {
                            header: 'MCO',
                            dataIndex: 'mco',
                            align: 'center',
                            width: 125,
                            //editor: this.detCmp.mco
                        },
                        {
                            header: 'Monto M/L',
                            dataIndex: 'monto_total_local',
                            align: 'center',
                            width: 200,
                            summaryType: 'sum',
                            //editor: this.detCmp.monto_total
                        },
                        {
                            header: 'Monto M/E',
                            dataIndex: 'monto_total_extranjero',
                            align: 'center',
                            width: 200,
                            summaryType: 'sum',
                            //editor: this.detCmp.monto_total
                        }
                    ]
                });

            },


            buildGrupos: function(){
                  this.Grupos = [{
                      layout: 'border',
                      border: false,
                      frame:true,
                      items:[
                          {
                              xtype: 'fieldset',
                              border: false,
                              split: true,
                              layout: 'column',
                              region: 'north',
                              autoScroll: true,
                              autoHeight: true,
                              collapseFirst : false,
                              collapsible: true,
                              width: '100%',
                              style: {
                                     background: '#75A8CD',
                                     },
                              //autoHeight: true,
                              padding: '0 0 0 10',
                              items:[
                                      {
                                       bodyStyle: 'padding-right:5px;',
                                       autoHeight: true,
                                       border: false,
                                       items:[
                                          {
                                           xtype: 'fieldset',
                                           frame: true,
                                           border: false,
                                           layout: 'form',
                                           title: 'Datos Venta',
                                           width: '90%',
                                           style: {
                                                  height:'160px',
                                                  width:'590px',
                                               },
                                           padding: '0 0 0 10',
                                           bodyStyle: 'padding-left:5px;',
                                           id_grupo: 0,
                                           items: [],
                                        }]
                                    },
                                    {
                                     bodyStyle: 'padding-right:5px;',
                                     border: false,
                                     autoHeight: true,
                                     items: [{
                                           xtype: 'fieldset',
                                           frame: true,
                                           layout: 'form',
                                           style: {
                                                  height:'160px',
                                                  width:'320px'
                                                 },
                                           border: false,
                                           padding: '0 0 0 20',
                                           bodyStyle: 'padding-left:5px;',
                                           id_grupo: 1,
                                           items: [],
                                        }]
                                    },
                                    {
                                     bodyStyle: 'padding-right:5px;',
                                     autoHeight: true,
                                     border: false,
                                     items:[
                                        {
                                         xtype: 'fieldset',
                                         frame: true,
                                         border: false,
                                         layout: 'form',
                                         title: 'Datos Detalle',
                                         width: '90%',
                                         style: {
                                                height:'160px',
                                                width:'590px',
                                             },
                                         padding: '0 0 0 10',
                                         bodyStyle: 'padding-left:5px;',
                                         id_grupo: 2,
                                         items: [],
                                      }]
                                  },
                              ]
                          },
                          this.megrid,

                          {
                            xtype: 'fieldset',
                            border: false,
                            split: true,
                            layout: 'column',
                            region: 'south',
                            autoScroll: true,
                            collapseFirst : false,
                            collapsible: true,
                            style: {
                                     height:'135px',
                                     background:'#75A8CD',
                                    // border:'2px solid blue'
                                   },
                            padding: '0 0 0 10',
                            items:[
                    {
                     bodyStyle: 'padding-right:5px;',

                     border: false,
                     autoHeight: true,
                     items: [{
                           xtype: 'fieldset',
                           frame: true,
                           layout: 'form',
                           title: 'Cambio M/L <br><br>',
                           style: {
                                   width: '40%',
                               },
                           border: false,
                           padding: '0 0 0 20',
                           bodyStyle: 'padding-left:5px;',
                           id_grupo: 11,
                           items: [],
                        }]
                    },{
                     bodyStyle: 'padding-right:5px;',
                     border: false,
                     autoHeight: true,
                     items: [{
                           xtype: 'fieldset',
                           frame: true,
                           layout: 'form',
                           width: '60%',
                           title: 'Cambio M/E <br><br>',
                           border: false,
                           padding: '0 0 0 20',
                           bodyStyle: 'padding-left:5px;',
                           id_grupo: 12,
                           items: [],
                        }]
                    },

                    ]
                          }
                      ]
                  }];


              },
              formularioAgregar : function(){
                var simple = new Ext.FormPanel({
                 labelWidth: 75, // label settings here cascade unless overridden
                 frame:true,
                 bodyStyle:'margin-left:-7px; margin-top:-7px; padding:10px 10px 0; background:#6EC8E3;',
                 width: 900,
                 height:500,
                 defaultType: 'textfield',
                 items: [
                        new Ext.form.ComboBox({
                                                name: 'id_moneda',
                                                fieldLabel: '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Moneda</span>',
                                                allowBlank: false,
                                                width : 450,
                                                listWidth:250,
                                                resizable:true,
                                                style: {
                                                     background: '#EFFFD6',
                                                     color: 'red',
                                                     fontWeight:'bold'
                                                   },
                                                emptyText: 'Moneda a pagar...',
                                                store: new Ext.data.JsonStore({
                                                    url: '../../sis_parametros/control/Moneda/listarMoneda',
                                                    id: 'id_moneda',
                                                    root: 'datos',
                                                    sortInfo: {
                                                        field: 'moneda',
                                                        direction: 'ASC'
                                                    },
                                                    totalProperty: 'total',
                                                    fields: ['id_moneda', 'codigo', 'moneda', 'codigo_internacional'],
                                                    remoteSort: true,
                                                    baseParams: {filtrar: 'si',par_filtro: 'moneda.moneda#moneda.codigo#moneda.codigo_internacional'}
                                                }),
                                                valueField: 'id_moneda',
                                                gdisplayField : 'codigo_internacional',
                                                displayField: 'codigo_internacional',
                                                hiddenName: 'id_moneda',
                                                tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;"><b style="color:black;">Moneda:</b> <b>{moneda}</b></p><p style="color:red;"><b style="color:black;">Código:</b> <b>{codigo_internacional}</b></p></div></tpl>',
                                                forceSelection: true,
                                                typeAhead: false,
                                                triggerAction: 'all',
                                                lazyRender: true,
                                                mode: 'remote',
                                                pageSize: 15,
                                                queryDelay: 1000,
                                                //disabled:true,
                                                minChars: 2

                                              }),
                         new Ext.form.ComboBox({
                                                 name: 'id_medio_pago',
                                                 fieldLabel: '<img src="../../../lib/imagenes/facturacion/TarjetaCredito.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Medio de pago</span>',
                                                 allowBlank: false,
                                                 width : 450,
                                                 emptyText: 'Medio de pago...',
                                                 store: new Ext.data.JsonStore({
                                                     url: '../../sis_obingresos/control/MedioPagoPw/listarMedioPagoPw',
                                                     id: 'id_medio_pago',
                                                     root: 'datos',
                                                     sortInfo: {
                                                         field: 'name',
                                                         direction: 'ASC'
                                                     },
                                                     totalProperty: 'total',
                                                     fields: ['id_medio_pago_pw', 'name', 'fop_code'],
                                                     remoteSort: true,
                                                     baseParams: {par_filtro: 'mppw.name#fp.fop_code', emision:'FACTCOMP'}
                                                 }),
                                                 valueField: 'id_medio_pago_pw',
                                                 displayField: 'name',
                                                 gdisplayField: 'name',
                                                 hiddenName: 'id_medio_pago_pw',
                                                 tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Medio de Pago: <font color="Blue">{name}</font></b></p><b><p>Codigo: <font color="red">{fop_code}</font></b></p></div></tpl>',
                                                 forceSelection: true,
                                                 typeAhead: false,
                                                 triggerAction: 'all',
                                                 lazyRender: true,
                                                 mode: 'remote',
                                                 pageSize: 15,
                                                 queryDelay: 1000,
                                                 gwidth: 150,
                                                 listWidth:250,
                                                 resizable:true,
                                                 minChars: 2

                                               }),

                         new Ext.form.ComboBox({
                                                 name: 'id_auxiliar',
                                                 fieldLabel: '<img src="../../../lib/imagenes/facturacion/CuentaCorriente.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Cuenta Corriente</span>',
                                         				allowBlank: true,
                                                 width:450,
                                         				emptyText: 'Cuenta Corriente...',
                                         				store: new Ext.data.JsonStore({
                                         					url: '../../sis_contabilidad/control/Auxiliar/listarAuxiliar',
                                         					id: 'id_auxiliar',
                                         					root: 'datos',
                                         					sortInfo: {
                                         						field: 'codigo_auxiliar',
                                         						direction: 'ASC'
                                         					},
                                         					totalProperty: 'total',
                                         					fields: ['id_auxiliar', 'codigo_auxiliar','nombre_auxiliar'],
                                         					remoteSort: true,
                                         					baseParams: {par_filtro: 'auxcta.codigo_auxiliar#auxcta.nombre_auxiliar',corriente:'si'}
                                         				}),
                                         				valueField: 'id_auxiliar',
                                         				displayField: 'nombre_auxiliar',
                                         				gdisplayField: 'codigo_auxiliar',
                                         				hiddenName: 'id_auxiliar',
                                                tpl:'<tpl for="."><div class="x-combo-list-item"><b><p style="color:red;">{nombre_auxiliar}</p><p>Codigo: <span style="color:green;">{codigo_auxiliar}</span></p></b></div></tpl>',
                                         				// tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre_auxiliar}</p><p>Codigo:{codigo_auxiliar}</p> </div></tpl>',
                                         				forceSelection: true,
                                         				typeAhead: false,
                                         				triggerAction: 'all',
                                         				lazyRender: true,
                                         				mode: 'remote',
                                         				pageSize: 15,
                                         				queryDelay: 1000,
                                         				gwidth: 150,
                                         				listWidth:350,
                                         				resizable:true,
                                         				minChars: 2,
                                                hidden:true

                                               }),
                      new Ext.form.TextField({
                                          name: 'numero_tarjeta',
                                          msgTarget: 'title',
                                          fieldLabel: '<img src="../../../lib/imagenes/facturacion/TarjetaCreditos.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> N° Tarjeta</span>',
                                          allowBlank: true,
                                          hidden:true,
                                          width : 450,
                                          maxLength:20,
                                          minLength:15

                                  }),
                      new Ext.form.TextField({
                                          name: 'codigo_tarjeta',
                                          msgTarget: 'title',
                                          fieldLabel: '<img src="../../../lib/imagenes/facturacion/Codigo.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Codigo de Autorización</span>',
                                          allowBlank: true,
                                          width : 450,
                                          hidden:true,
                                          minLength:6,
                                          maxLength:6,
                                          style:'text-transform:uppercase;',
                                          maskRe: /[a-zA-Z0-9]+/i,
                                          regex: /[a-zA-Z0-9]+/i

                                  }),
                      new Ext.form.TextField({
                                          name: 'mco',
                                          msgTarget: 'title',
                                          fieldLabel: '<img src="../../../lib/imagenes/facturacion/Codigo.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> MCO</span>',
                                          allowBlank: true,
                                          width : 450,
                                          hidden:true,
                                          minLength:15,
                                          maxLength:20

                                  }),
                      new Ext.form.NumberField({
                                          name: 'monto_forma_pago',
                                          msgTarget: 'title',
                                          fieldLabel: '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Importe Recibido</span>',
                                          allowBlank: true,
                                          width : 450,
                                          maxLength:20,
                                          allowNegative:false,

                                  }),
                      new Ext.form.ComboBox({
                                          name: 'id_venta_recibo',
                                          fieldLabel: '<span style="vertical-align: middle;"> Nro. Recibo</span>',
                                  				allowBlank: true,
                                          width:450,
                                          store: new Ext.data.JsonStore({
                                              url: '../../sis_ventas_facturacion/control/Venta/listarReciboBoletosAmadeus',
                                              id: 'id_venta',
                                              root: 'datos',
                                              sortInfo: {
                                                  field: 'v.nro_factura, v.nombre_factura ',
                                                  direction: 'ASC'
                                              },
                                              totalProperty: 'total',
                                              fields: ['id_venta', 'nro_factura','nombre_factura','total_venta','saldo', 'tex_saldo','moneda'],
                                              remoteSort: true,
                                              baseParams: {par_filtro: 'v.nro_factura#v.nombre_factura'}
                                          }),
                                          valueField: 'id_venta',
                                          displayField: 'nro_factura',
                                          gdisplayField: 'nro_factura',
                                          hiddenName: 'id_venta',
                                          tpl:'<tpl for="."><div class="x-combo-list-item"><div style="font-weight:bold;">Numero <span style="color:blue;"">&nbsp;{nro_factura}</span><br> Nombre: <span style="color:green;"">{nombre_factura}</span> <br> Monto: <span style="color:red;">&nbsp;&nbsp;{total_venta}&nbsp;&nbsp;&nbsp{tex_saldo}</span><br></div></div></tpl>',
                                          forceSelection: true,
                                          typeAhead: false,
                                          triggerAction: 'all',
                                          lazyRender: true,
                                          mode: 'remote',
                                          pageSize: 15,
                                          queryDelay: 1000,
                                          gwidth: 150,
                                          listWidth:400,
                                          resizable:true,
                                          minChars: 1,
                                          hidden: true
                                })
                  ]

              });
              this.variables = simple;
              this.variables.items.items[1].store.baseParams.regional = this.data.variables_globales.ESTACION_inicio;
                var win = new Ext.Window({
                  title: '<center><img src="../../../lib/imagenes/facturacion/anadir.png" style="width:30px; vertical-align: middle;"><span style="vertical-align: middle; font-size:25px; font-weight:bold; color:#1479B8; text-shadow: 3px 0px 0px #000000;"> AGREGAR MEDIO DE PAGO</span></center>', //the title of the window
                  width:600,
                  height:220,
                  closeAction:'hide',
                  modal:true,
                  plain: true,
                  items:simple,
                  buttons: [{
                              text:'<div style="font-weight:bold; font-size:12px;"><img src="../../../lib/imagenes/facturacion/aceptar.png" style="width:15px; vertical-align: middle;"> <span style="vertical-align: middle; font-size:15px;">Guardar</span></div>',
                              id:'botonGuardarFormulario',
                              scope:this,
                              handler: function(){
                                  this.insertarNuevo(win);
                              }
                          },{
                              text: '<div style="font-weight:bold; font-size:12px;"><img src="../../../lib/imagenes/facturacion/cancelar.png" style="width:15px; vertical-align: middle;"> <span style="vertical-align: middle; font-size:13px;">Cancelar</span></div>',
                              id:'botonCancelarFormulario',
                              handler: function(){
                                  win.hide();
                              }
                          }]

                });
                this.ventana_detalle = win;
                win.show();
                this.formularioDet();
              },


              formularioEditar : function(datosEdit){
                var simple = new Ext.FormPanel({
                 labelWidth: 75, // label settings here cascade unless overridden
                 frame:true,
                 bodyStyle:'margin-left:-7px; margin-top:-7px; padding:10px 10px 0; background:#6EC8E3;',
                 width: 900,
                 height:500,
                 defaultType: 'textfield',
                 items: [
                        new Ext.form.ComboBox({
                                                name: 'id_moneda',
                                                fieldLabel: '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Moneda</span>',
                                                allowBlank: false,
                                                width : 450,
                                                listWidth:250,
                                                resizable:true,
                                                style: {
                                                     background: '#EFFFD6',
                                                     color: 'red',
                                                     fontWeight:'bold'
                                                   },
                                                emptyText: 'Moneda a pagar...',
                                                store: new Ext.data.JsonStore({
                                                    url: '../../sis_parametros/control/Moneda/listarMoneda',
                                                    id: 'id_moneda',
                                                    root: 'datos',
                                                    sortInfo: {
                                                        field: 'moneda',
                                                        direction: 'ASC'
                                                    },
                                                    totalProperty: 'total',
                                                    fields: ['id_moneda', 'codigo', 'moneda', 'codigo_internacional'],
                                                    remoteSort: true,
                                                    baseParams: {filtrar: 'si',par_filtro: 'moneda.moneda#moneda.codigo#moneda.codigo_internacional'}
                                                }),
                                                valueField: 'id_moneda',
                                                gdisplayField : 'codigo_internacional',
                                                displayField: 'codigo_internacional',
                                                hiddenName: 'id_moneda',
                                                tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;"><b style="color:black;">Moneda:</b> <b>{moneda}</b></p><p style="color:red;"><b style="color:black;">Código:</b> <b>{codigo_internacional}</b></p></div></tpl>',
                                                forceSelection: true,
                                                typeAhead: false,
                                                triggerAction: 'all',
                                                lazyRender: true,
                                                mode: 'remote',
                                                pageSize: 15,
                                                queryDelay: 1000,
                                                //disabled:true,
                                                minChars: 2

                                              }),
                         new Ext.form.ComboBox({
                                                 name: 'id_medio_pago',
                                                 fieldLabel: '<img src="../../../lib/imagenes/facturacion/TarjetaCredito.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Medio de pago</span>',
                                                 allowBlank: false,
                                                 width : 450,
                                                 emptyText: 'Medio de pago...',
                                                 store: new Ext.data.JsonStore({
                                                     url: '../../sis_obingresos/control/MedioPagoPw/listarMedioPagoPw',
                                                     id: 'id_medio_pago',
                                                     root: 'datos',
                                                     sortInfo: {
                                                         field: 'name',
                                                         direction: 'ASC'
                                                     },
                                                     totalProperty: 'total',
                                                     fields: ['id_medio_pago_pw', 'name', 'fop_code'],
                                                     remoteSort: true,
                                                     baseParams: {par_filtro: 'mppw.name#fp.fop_code', emision:'FACTCOMP'}
                                                 }),
                                                 valueField: 'id_medio_pago_pw',
                                                 displayField: 'name',
                                                 gdisplayField: 'name',
                                                 hiddenName: 'id_medio_pago_pw',
                                                 tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Medio de Pago: <font color="Blue">{name}</font></b></p><b><p>Codigo: <font color="red">{fop_code}</font></b></p></div></tpl>',
                                                 forceSelection: true,
                                                 typeAhead: false,
                                                 triggerAction: 'all',
                                                 lazyRender: true,
                                                 mode: 'remote',
                                                 pageSize: 15,
                                                 queryDelay: 1000,
                                                 gwidth: 150,
                                                 listWidth:250,
                                                 resizable:true,
                                                 minChars: 2

                                               }),

                         new Ext.form.ComboBox({
                                                 name: 'id_auxiliar',
                                                 fieldLabel: '<img src="../../../lib/imagenes/facturacion/CuentaCorriente.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Cuenta Corriente</span>',
                                         				allowBlank: true,
                                                 width:450,
                                         				emptyText: 'Cuenta Corriente...',
                                         				store: new Ext.data.JsonStore({
                                         					url: '../../sis_contabilidad/control/Auxiliar/listarAuxiliar',
                                         					id: 'id_auxiliar',
                                         					root: 'datos',
                                         					sortInfo: {
                                         						field: 'codigo_auxiliar',
                                         						direction: 'ASC'
                                         					},
                                         					totalProperty: 'total',
                                         					fields: ['id_auxiliar', 'codigo_auxiliar','nombre_auxiliar'],
                                         					remoteSort: true,
                                         					baseParams: {par_filtro: 'auxcta.codigo_auxiliar#auxcta.nombre_auxiliar',corriente:'si'}
                                         				}),
                                         				valueField: 'id_auxiliar',
                                         				displayField: 'nombre_auxiliar',
                                         				gdisplayField: 'codigo_auxiliar',
                                         				hiddenName: 'id_auxiliar',
                                                tpl:'<tpl for="."><div class="x-combo-list-item"><b><p style="color:red;">{nombre_auxiliar}</p><p>Codigo: <span style="color:green;">{codigo_auxiliar}</span></p></b></div></tpl>',
                                         				// tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre_auxiliar}</p><p>Codigo:{codigo_auxiliar}</p> </div></tpl>',
                                         				forceSelection: true,
                                         				typeAhead: false,
                                         				triggerAction: 'all',
                                         				lazyRender: true,
                                         				mode: 'remote',
                                         				pageSize: 15,
                                         				queryDelay: 1000,
                                         				gwidth: 150,
                                         				listWidth:350,
                                         				resizable:true,
                                         				minChars: 2,
                                                hidden:true

                                               }),
                      new Ext.form.TextField({
                                          name: 'numero_tarjeta',
                                          msgTarget: 'title',
                                          fieldLabel: '<img src="../../../lib/imagenes/facturacion/TarjetaCreditos.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> N° Tarjeta</span>',
                                          allowBlank: true,
                                          hidden:true,
                                          width : 450,
                                          maxLength:20,
                                          minLength:15

                                  }),
                      new Ext.form.TextField({
                                          name: 'codigo_tarjeta',
                                          msgTarget: 'title',
                                          fieldLabel: '<img src="../../../lib/imagenes/facturacion/Codigo.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Codigo de Autorización</span>',
                                          allowBlank: true,
                                          width : 450,
                                          hidden:true,
                                          minLength:6,
                                          maxLength:6,
                                          style:'text-transform:uppercase;',
                                          maskRe: /[a-zA-Z0-9]+/i,
                                          regex: /[a-zA-Z0-9]+/i

                                  }),
                      new Ext.form.TextField({
                                          name: 'mco',
                                          msgTarget: 'title',
                                          fieldLabel: '<img src="../../../lib/imagenes/facturacion/Codigo.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> MCO</span>',
                                          allowBlank: true,
                                          width : 450,
                                          hidden:true,
                                          minLength:15,
                                          maxLength:20

                                  }),
                      new Ext.form.NumberField({
                                          name: 'monto_forma_pago',
                                          msgTarget: 'title',
                                          fieldLabel: '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Importe Recibido</span>',
                                          allowBlank: true,
                                          width : 450,
                                          maxLength:20,
                                          allowNegative:false,

                                  }),
                      new Ext.form.ComboBox({
                                          name: 'id_venta_recibo',
                                          fieldLabel: '<span style="vertical-align: middle;"> Nro. Recibo</span>',
                                  				allowBlank: true,
                                          width:450,
                                          store: new Ext.data.JsonStore({
                                              url: '../../sis_ventas_facturacion/control/Venta/listarReciboBoletosAmadeus',
                                              id: 'id_venta',
                                              root: 'datos',
                                              sortInfo: {
                                                  field: 'v.nro_factura, v.nombre_factura ',
                                                  direction: 'ASC'
                                              },
                                              totalProperty: 'total',
                                              fields: ['id_venta', 'nro_factura','nombre_factura','total_venta','saldo', 'tex_saldo','moneda'],
                                              remoteSort: true,
                                              baseParams: {par_filtro: 'v.nro_factura#v.nombre_factura'}
                                          }),
                                          valueField: 'id_venta',
                                          displayField: 'nro_factura',
                                          gdisplayField: 'nro_factura',
                                          hiddenName: 'id_venta',
                                          tpl:'<tpl for="."><div class="x-combo-list-item"><div style="font-weight:bold;">Numero <span style="color:blue;"">&nbsp;{nro_factura}</span><br> Nombre: <span style="color:green;"">{nombre_factura}</span> <br> Monto: <span style="color:red;">&nbsp;&nbsp;{total_venta}&nbsp;&nbsp;&nbsp{tex_saldo}</span><br></div></div></tpl>',
                                          forceSelection: true,
                                          typeAhead: false,
                                          triggerAction: 'all',
                                          lazyRender: true,
                                          mode: 'remote',
                                          pageSize: 15,
                                          queryDelay: 1000,
                                          gwidth: 150,
                                          listWidth:400,
                                          resizable:true,
                                          minChars: 1,
                                          hidden: true
                                })
                  ]

              });
              this.variables = simple;
              this.variables.items.items[1].store.baseParams.regional = this.data.variables_globales.ESTACION_inicio;
                var win = new Ext.Window({
                  title: '<center><img src="../../../lib/imagenes/facturacion/anadir.png" style="width:30px; vertical-align: middle;"><span style="vertical-align: middle; font-size:25px; font-weight:bold; color:#1479B8; text-shadow: 3px 0px 0px #000000;"> EDITAR MEDIO DE PAGO</span></center>', //the title of the window
                  width:600,
                  height:220,
                  closeAction:'hide',
                  modal:true,
                  plain: true,
                  items:simple,
                  buttons: [{
                              text:'<div style="font-weight:bold; font-size:12px;"><img src="../../../lib/imagenes/facturacion/aceptar.png" style="width:15px; vertical-align: middle;"> <span style="vertical-align: middle; font-size:15px;">Guardar</span></div>',
                              id:'botonGuardarFormulario',
                              scope:this,
                              handler: function(){
                                  this.editarFp(win,datosEdit);
                              }
                          },{
                              text: '<div style="font-weight:bold; font-size:12px;"><img src="../../../lib/imagenes/facturacion/cancelar.png" style="width:15px; vertical-align: middle;"> <span style="vertical-align: middle; font-size:13px;">Cancelar</span></div>',
                              id:'botonCancelarFormulario',
                              handler: function(){
                                  win.hide();
                              }
                          }]

                });
                this.ventana_detalle = win;
                win.show();
                this.formularioDetFp(datosEdit);
              },

              obtenersuma: function () {

                var total_datos = this.megrid.store.data.items.length;
                var suma_local = 0;
                var suma_extranjera = 0;
                for (var i = 0; i < total_datos; i++) {
                    suma_local = suma_local + parseFloat(this.megrid.store.data.items[i].data.monto_total_local);
                    suma_extranjera = suma_extranjera + parseFloat(this.megrid.store.data.items[i].data.monto_total_extranjero);
                }

                this.suma_total_local = suma_local;
                this.suma_total_extranjera = suma_extranjera;
                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[7].childNodes[0].style.color="red";
                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[7].childNodes[0].style.fontWeight="bold";
                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[7].childNodes[0].style.fontSize="25px";

                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[8].childNodes[0].style.color="green";
                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[8].childNodes[0].style.fontWeight="bold";
                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[8].childNodes[0].style.fontSize="25px";



                /*Aqui mandamos el cambio que se debe devolver al cliente*/
                if (this.data.tipo_factura == 'recibo' || this.data.tipo_factura == 'recibo_manual') {
                  if(this.Cmp.moneda_recibo.getValue() == 2){
                    this.Cmp.cambio.setValue(((this.suma_total_local+(this.suma_total_extranjera*this.data.tipo_cambio))-(this.Cmp.total_venta.getValue()*this.data.tipo_cambio)));
                    this.Cmp.cambio_moneda_extranjera.setValue(((this.suma_total_local/this.data.tipo_cambio)+this.suma_total_extranjera)-this.Cmp.total_venta.getValue());

                    if ((this.suma_total_local+(this.suma_total_extranjera*this.data.tipo_cambio)) >= (this.Cmp.total_venta.getValue()*this.data.tipo_cambio)) {
                      Ext.getCmp('botonAgregar2').hide();
                    } else {
                      Ext.getCmp('botonAgregar2').show();
                    }

                  }else{
                    this.Cmp.cambio.setValue(((this.suma_total_local+(this.suma_total_extranjera*this.data.tipo_cambio))-this.Cmp.total_venta.getValue()));
                    this.Cmp.cambio_moneda_extranjera.setValue(((this.suma_total_local+(this.suma_total_extranjera*this.data.tipo_cambio))-this.Cmp.total_venta.getValue())/this.data.tipo_cambio);

                    if ((this.suma_total_local+(this.suma_total_extranjera*this.data.tipo_cambio)) >= this.Cmp.total_venta.getValue()) {
                      Ext.getCmp('botonAgregar2').hide();
                    } else {
                      Ext.getCmp('botonAgregar2').show();
                    }

                  }
                } else {
                  this.Cmp.cambio.setValue((this.suma_total_local + (this.suma_total_extranjera*this.data.tipo_cambio)) - this.Cmp.total_venta.getValue());
                  this.Cmp.cambio_moneda_extranjera.setValue(((this.suma_total_local + (this.suma_total_extranjera*this.data.tipo_cambio)) - this.Cmp.total_venta.getValue())/this.data.tipo_cambio);

                  /*Aqui restringiremos para no agregar mas formas de pago*/
                  if ((this.suma_total_local + (this.suma_total_extranjera*this.data.tipo_cambio))>= this.Cmp.total_venta.getValue()) {
                      Ext.getCmp('botonAgregar2').hide();
                  } else {
                      Ext.getCmp('botonAgregar2').show();
                  }
                  /********************************************************/
                }
                /*********************************************************/
              },

              onNew: function(){

                  },


    iniciarEventos : function () {

      this.Cmp.tipo_factura.setValue(this.data.tipo_factura);

      this.arrayBotones[0].scope.form.buttons[0].btnEl.dom.style.width="190px";
      this.arrayBotones[0].scope.form.buttons[0].btnEl.dom.style.height="50px";


      this.megrid.topToolbar.items.items[0].container.dom.style.width="80px";
      this.megrid.topToolbar.items.items[0].container.dom.style.height="35px";
      this.megrid.topToolbar.items.items[0].btnEl.dom.style.height="35px";

      this.megrid.topToolbar.el.dom.style.background="#469CE0";
      this.megrid.topToolbar.el.dom.style.height="45px";
      this.megrid.topToolbar.el.dom.style.borderRadius="2px";
      this.megrid.body.dom.childNodes[0].firstChild.children[0].firstChild.style.background='#FFF4EB';
      /*Aumentando para el hover Ismael Valdivia (13/11/2020)*/

      Ext.getCmp('botonAgregar2').el.dom.onmouseover = function () {
        Ext.getCmp('botonAgregar2').btnEl.dom.style.background = '#5CE100';
      };

      Ext.getCmp('botonAgregar2').el.dom.onmouseout = function () {
        Ext.getCmp('botonAgregar2').btnEl.dom.style.background = '';
      };

      this.megrid.topToolbar.items.items[1].container.dom.style.width="80px";
      this.megrid.topToolbar.items.items[1].container.dom.style.height="35px";
      this.megrid.topToolbar.items.items[1].btnEl.dom.style.height="35px";

      Ext.getCmp('botonEditar').el.dom.onmouseover = function () {
        Ext.getCmp('botonEditar').btnEl.dom.style.background = 'rgba(241, 241, 0, 0.5)';
      };

      Ext.getCmp('botonEditar').el.dom.onmouseout = function () {
        Ext.getCmp('botonEditar').btnEl.dom.style.background = '';
      };

      /*Aumentando para el hover Ismael Valdivia (13/11/2020)*/

      this.megrid.topToolbar.items.items[2].container.dom.style.width="80px";
      this.megrid.topToolbar.items.items[2].container.dom.style.height="35px";
      this.megrid.topToolbar.items.items[2].btnEl.dom.style.height="35px";

      Ext.getCmp('botonEliminar2').el.dom.onmouseover = function () {
        Ext.getCmp('botonEliminar2').btnEl.dom.style.background = 'rgba(255, 0, 0, 0.5)';
      };

      Ext.getCmp('botonEliminar2').el.dom.onmouseout = function () {
        Ext.getCmp('botonEliminar2').btnEl.dom.style.background = '';
      };
      this.Cmp.nit.setValue(this.data.nit);
      this.Cmp.observaciones.setValue(this.data.observaciones);

      if (this.data.tipo_factura == 'recibo') {
        this.Cmp.excento.setValue(0);
        this.ocultarComponente(this.Cmp.excento);

        this.mostrarComponente(this.Cmp.moneda_recibo);
        this.Cmp.moneda_recibo.store.load({params:{start:0,limit:50},
               callback : function (r) {
                        for (var i = 0; i < r.length; i++) {
                          if (r[i].data.id_moneda == this.data.detalleConceptos[0].data.id_moneda_recibo) {
                            this.Cmp.moneda_recibo.setValue(r[i].data.id_moneda);
                            this.Cmp.moneda_recibo.fireEvent('select', this.Cmp.moneda_recibo,r[i]);
                          }
                        }
                }, scope : this
            });

        if (this.data.id_auxiliar_anticipo != '') {
          this.mostrarComponente(this.Cmp.id_auxiliar_anticipo);
          this.Cmp.id_auxiliar_anticipo.store.baseParams.id_auxiliar=this.data.id_auxiliar_anticipo;
          this.Cmp.id_auxiliar_anticipo.store.load({params:{start:0,limit:50},
                 callback : function (r) {
                       if (r.length == 1 ) {
                          this.Cmp.id_auxiliar_anticipo.setValue(r[0].data.id_auxiliar);
                          this.Cmp.id_auxiliar_anticipo.fireEvent('select', this.Cmp.id_auxiliar,r[0]);
                        }
                  }, scope : this
              });
        } else {
          this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
          this.Cmp.moneda_recibo.reset();
        }

        this.ocultarComponente(this.Cmp.informe);
        this.Cmp.informe.reset();


      } else if (this.data.tipo_factura == 'recibo_manual') {

         this.Cmp.nro_factura.setValue(this.data.nro_factura);
         this.Cmp.fecha_factura.setValue(this.data.fecha_factura.dateFormat('d/m/Y'));

         this.Cmp.excento.setValue(0);
         this.mostrarComponente(this.Cmp.nro_factura);
         this.ocultarComponente(this.Cmp.excento);
         this.Cmp.nro_factura.setValue(this.data.nro_factura);

         this.mostrarComponente(this.Cmp.moneda_recibo);
         this.Cmp.moneda_recibo.store.load({params:{start:0,limit:50},
                callback : function (r) {
                         for (var i = 0; i < r.length; i++) {
                           if (r[i].data.id_moneda == this.data.detalleConceptos[0].data.id_moneda_recibo) {
                             this.Cmp.moneda_recibo.setValue(r[i].data.id_moneda);
                             this.Cmp.moneda_recibo.fireEvent('select', this.Cmp.moneda_recibo,r[i]);
                           }
                         }
                 }, scope : this
             });

         if (this.data.id_auxiliar_anticipo != '') {
           this.mostrarComponente(this.Cmp.id_auxiliar_anticipo);
           this.Cmp.id_auxiliar_anticipo.store.baseParams.id_auxiliar=this.data.id_auxiliar_anticipo;
           this.Cmp.id_auxiliar_anticipo.store.load({params:{start:0,limit:50},
                  callback : function (r) {
                        if (r.length == 1 ) {
                           this.Cmp.id_auxiliar_anticipo.setValue(r[0].data.id_auxiliar);
                           this.Cmp.id_auxiliar_anticipo.fireEvent('select', this.Cmp.id_auxiliar,r[0]);
                         }
                   }, scope : this
               });
         } else {
           this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
           this.Cmp.moneda_recibo.reset();
         }

         this.ocultarComponente(this.Cmp.informe);
         this.Cmp.informe.reset();
      } else if (this.data.tipo_factura == 'manual') {

         this.Cmp.excento.setValue(this.data.exento);
         this.Cmp.informe.setValue(this.data.informe);
         this.Cmp.nro_factura.setValue(this.data.nro_factura);
         this.Cmp.fecha_factura.setValue(this.data.fecha_factura.dateFormat('d/m/Y'));

         this.Cmp.id_dosificacion.store.baseParams.id_dosificacion = this.data.id_dosificacion;
         this.Cmp.id_dosificacion.store.load({params:{start:0,limit:50},
                callback : function (r) {
                      if (r.length == 1 ) {
                         this.Cmp.id_dosificacion.setValue(r[0].data.id_dosificacion);
                         this.Cmp.id_dosificacion.fireEvent('select', this.Cmp.id_dosificacion,r[0]);
                       }
                 }, scope : this
             });

         this.mostrarComponente(this.Cmp.informe);
         this.mostrarComponente(this.Cmp.nro_factura);
         this.mostrarComponente(this.Cmp.id_dosificacion);
         this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
         this.ocultarComponente(this.Cmp.moneda_recibo);

         this.Cmp.id_auxiliar_anticipo.reset();
         this.Cmp.moneda_recibo.reset();
      }

      else {
        this.Cmp.excento.setValue(this.data.exento);
        this.ocultarComponente(this.Cmp.moneda_recibo);
        this.Cmp.moneda_recibo.reset();
        this.ocultarComponente(this.Cmp.informe);
        this.ocultarComponente(this.Cmp.nro_factura);
        this.ocultarComponente(this.Cmp.id_dosificacion);

        this.Cmp.informe.reset();
        this.Cmp.nro_factura.reset();
        this.Cmp.id_dosificacion.reset();

      }
      //bvp
      var valoresAceptados = /^[0-9]+$/;
      var bolcontrol = false;
      if (this.data.cliente.match(valoresAceptados)){
          bolcontrol = true;
      }
      if(bolcontrol){
        this.Cmp.id_cliente.store.baseParams.id_cliente=this.data.cliente;
        this.Cmp.id_cliente.store.load({params:{start:0,limit:50},
               callback : function (r) {
                 if (r.length == 1 ) {
                    this.Cmp.id_cliente.setValue(r[0].data.id_cliente);
                    this.Cmp.id_cliente.fireEvent('select', this.Cmp.id_cliente,r[0]);
                  }

                }, scope : this
            });
        }else{
            this.Cmp.nombre_factura.setValue(this.data.nombre_factura);
            this.Cmp.id_cliente.setValue(this.data.nombre_factura);
        }
        this.Cmp.id_sucursal.store.baseParams.id_sucursal = this.data.sucursal;
        this.Cmp.id_sucursal.store.load({params:{start:0,limit:50},
               callback : function (r) {
                    this.Cmp.id_sucursal.setValue(r[0].data.id_sucursal);
                    this.Cmp.id_sucursal.fireEvent('select', this.Cmp.id_sucursal,r[0]);
                }, scope : this
            });

        this.Cmp.id_punto_venta.store.baseParams.id_punto_venta = this.data.puntoVenta;
        this.Cmp.id_punto_venta.store.load({params:{start:0,limit:50},
               callback : function (r) {
                        this.Cmp.id_punto_venta.setValue(this.data.puntoVenta);
                        this.Cmp.id_punto_venta.fireEvent('select', this.Cmp.id_punto_venta,r[0],0);
                }, scope : this
            });

        this.detCmp.id_medio_pago.store.baseParams.regional = this.data.variables_globales.ESTACION_inicio;

        /*aqui seteamos los datos de detalle*/
        var cantidad_detalle = this.data.detalleConceptos.length;
        this.Cmp.cantidad_conceptos.setValue(cantidad_detalle);
        this.Cmp.total_venta.setValue(this.data.total_pagar);
        /************************************/

        if (this.data.medio_pago_1 != '' && this.data.monto_mp_1 != '') {
          this.recuperarFormasdePago1();
        }

        if (this.data.medio_pago_2 != '') {
          this.recuperarFormasdePago2();
        }

        this.Cmp.id_formula.setValue(this.data.paquetes);
        this.Cmp.id_venta.setValue(this.data.id_venta);

        if (this.data.asociar_boletos != '') {
          this.mostrarComponente(this.Cmp.asociar_boletos);
          this.Cmp.asociar_boletos.setValue(this.data.asociar_boletos);
        }




    },

    formularioDetFp:function(datosEdit){

      /***************************Aqui para poner la moneda base por defecto**************************************/
      this.variables.items.items[0].store.load({params:{start:0,limit:50},
             callback : function (r) {
                      for (var i = 0; i < r.length; i++) {
                        if (r[i].data.id_moneda == datosEdit.data.id_moneda) {
                          this.variables.items.items[0].setValue(r[i].data.id_moneda);
                          this.variables.items.items[0].fireEvent('select', this.variables.items.items[0],r[i]);
                        }
                      }
              }, scope : this
          });

        this.variables.items.items[1].store.baseParams.id_medio_pago = datosEdit.data.id_medio_pago;

        this.variables.items.items[1].store.load({params:{start:0,limit:50},
               callback : function (r) {
                            this.variables.items.items[1].setValue(r[0].data.id_medio_pago_pw);
                            this.variables.items.items[1].fireEvent('select', this.variables.items.items[1],r[0]);
                }, scope : this
            });

        this.variables.items.items[1].store.baseParams.id_medio_pago = '';

        this.variables.items.items[2].store.baseParams.id_medio_pago = datosEdit.data.id_auxiliar;
        this.variables.items.items[2].store.load({params:{start:0,limit:50},
               callback : function (r) {
                            this.variables.items.items[2].setValue(r[0].data.id_auxiliar);
                            this.variables.items.items[2].fireEvent('select', this.variables.items.items[2],r[0]);
                }, scope : this
            });
        this.variables.items.items[2].store.baseParams.id_medio_pago = '';


        this.variables.items.items[3].setValue(datosEdit.data.num_tarjeta);
        this.variables.items.items[4].setValue(datosEdit.data.codigo_autorizacion);
        this.variables.items.items[5].setValue(datosEdit.data.mco);

        if (datosEdit.data.monto_total_extranjero == 0) {
          var monto_totla = datosEdit.data.monto_total_local;
        } else {
          var monto_totla = datosEdit.data.monto_total_extranjero;
        }

        this.variables.items.items[6].setValue(monto_totla);

      /************************************************************************************************************/

      /*Controlamos los medios de pago para ir mostrando campos*/
      this.variables.items.items[1].on('select',function(c,r,i) {

        if (r.data.fop_code.startsWith("CC")) {
          this.mostrarComponente(this.variables.items.items[3]);
          this.mostrarComponente(this.variables.items.items[4]);
          this.variables.items.items[3].allowBlank = false;
          this.variables.items.items[4].allowBlank = false;

          this.ocultarComponente(this.variables.items.items[2]);
          this.ocultarComponente(this.variables.items.items[5]);
          this.ocultarComponente(this.variables.items.items[7]);
          this.variables.items.items[2].allowBlank = true;
          this.variables.items.items[5].allowBlank = true;
          this.variables.items.items[2].reset();
          this.variables.items.items[5].reset();
          this.variables.items.items[7].reset();
          this.ventana_detalle.body.dom.style.height = "250px";


        } else if (r.data.fop_code.startsWith("MCO")) {

          this.mostrarComponente(this.variables.items.items[5]);
          this.variables.items.items[5].allowBlank = false;
          this.ocultarComponente(this.variables.items.items[2]);
          this.ocultarComponente(this.variables.items.items[3]);
          this.ocultarComponente(this.variables.items.items[4]);
          this.ocultarComponente(this.variables.items.items[7]);
          this.variables.items.items[2].allowBlank = true;
          this.variables.items.items[3].allowBlank = true;
          this.variables.items.items[4].allowBlank = true;
          this.variables.items.items[2].reset();
          this.variables.items.items[3].reset();
          this.variables.items.items[4].reset();
          this.variables.items.items[7].reset();
          this.ventana_detalle.body.dom.style.height = "240px";

        } else if (r.data.fop_code.startsWith("CU") || r.data.fop_code.startsWith("CT")) {

          this.mostrarComponente(this.variables.items.items[2]);
          this.variables.items.items[2].allowBlank = false;

          this.ocultarComponente(this.variables.items.items[3]);
          this.ocultarComponente(this.variables.items.items[4]);
          this.ocultarComponente(this.variables.items.items[5]);
          this.ocultarComponente(this.variables.items.items[7]);
          this.variables.items.items[3].allowBlank = true;
          this.variables.items.items[4].allowBlank = true;
          this.variables.items.items[5].allowBlank = true;
          this.variables.items.items[3].reset();
          this.variables.items.items[4].reset();
          this.variables.items.items[5].reset();
          this.variables.items.items[7].reset();
          this.ventana_detalle.body.dom.style.height = "240px";
          this.variables.items.items[2].label.dom.innerHTML='Cuenta Corriente';
          this.variables.items.items[2].store.baseParams.ro_activo='no';
          this.variables.items.items[2].modificado = true;
        } else if (r.data.fop_code.startsWith("CA")) {

          this.ocultarComponente(this.variables.items.items[2]);
          this.ocultarComponente(this.variables.items.items[3]);
          this.ocultarComponente(this.variables.items.items[4]);
          this.ocultarComponente(this.variables.items.items[5]);
          this.ocultarComponente(this.variables.items.items[7]);
          this.variables.items.items[2].allowBlank = true;
          this.variables.items.items[3].allowBlank = true;
          this.variables.items.items[4].allowBlank = true;
          this.variables.items.items[5].allowBlank = true;
          this.variables.items.items[2].reset();
          this.variables.items.items[3].reset();
          this.variables.items.items[4].reset();
          this.variables.items.items[5].reset();
          this.variables.items.items[7].reset();
          this.ventana_detalle.body.dom.style.height = "170px";

        } else if (r.data.fop_code.startsWith("RANT")) {
          this.mostrarComponente(this.variables.items.items[2])
          this.mostrarComponente(this.variables.items.items[7])
          this.variables.items.items[2].allowBlank = false;

          this.ocultarComponente(this.variables.items.items[3]);
          this.ocultarComponente(this.variables.items.items[4]);
          this.ocultarComponente(this.variables.items.items[5]);
          this.variables.items.items[3].allowBlank = true;
          this.variables.items.items[4].allowBlank = true;
          this.variables.items.items[5].allowBlank = true;
          this.variables.items.items[3].reset();
          this.variables.items.items[4].reset();
          this.variables.items.items[5].reset();
          this.variables.items.items[2].reset();
          this.ventana_detalle.body.dom.style.height = "240px";
          this.variables.items.items[2].label.dom.innerHTML='Grupo';
          this.variables.items.items[2].store.baseParams.ro_activo='si';
          this.variables.items.items[2].modificado = true;

        }

      },this);
      /**********************************************************/
      this.variables.items.items[0].on('select', function(c,r,i) {
        this.variables.items.items[7].reset()
        this.variables.items.items[7].store.baseParams.id_moneda=r.data.id_moneda;
        this.variables.items.items[7].store.baseParams.id_auxiliar_anticipo=this.variables.items.items[2].getValue();
        this.variables.items.items[7].modificado = true;
      },this);

      this.variables.items.items[2].on('select', function(c,r,i) {
        this.variables.items.items[7].reset()
        this.variables.items.items[7].store.baseParams.id_moneda=this.variables.items.items[0].getValue();
        this.variables.items.items[7].store.baseParams.id_auxiliar_anticipo=r.data.id_auxiliar;
        this.variables.items.items[7].modificado = true;
      },this);

      this.variables.items.items[7].on('select', function(c,r,i) {
        var saldo = r.data.saldo;
        var imp1 = this.variables.items.items[6].getValue();
        var mon_sel = r.data.moneda;
        var dif = imp1 - saldo;

        if (imp1 > saldo){
            Ext.Msg.show({
             title:'<h1 style="color:red"><center>AVISO</center></h1>',
             msg: '<b>El saldo del recibo es: <span style="color:red;"> '+mon_sel+ ' '+saldo+'</span> Falta un monto de <span style="color:red;">'+ mon_sel +' '+ dif +'</span> para la forma de pago recibo anticipo</b>',
             maxWidth : 400,
             width: 380,
             buttons: Ext.Msg.OK,
             scope:this
            });
        }
      },this);

    },



    formularioDet:function(){

      /***************************Aqui para poner la moneda base por defecto**************************************/
      this.variables.items.items[0].store.load({params:{start:0,limit:50},
             callback : function (r) {
                      for (var i = 0; i < r.length; i++) {
                        console.log("entra aqui data",r[i].data.codigo_internacional);
                        if (r[i].data.codigo_internacional == this.data.variables_globales.codigo_moneda_base) {
                          this.variables.items.items[0].setValue(r[i].data.id_moneda);
                          this.variables.items.items[0].fireEvent('select', this.variables.items.items[0],r[i]);
                        }
                      }
              }, scope : this
          });
      /************************************************************************************************************/

      /*Controlamos los medios de pago para ir mostrando campos*/
      this.variables.items.items[1].on('select',function(c,r,i) {

        if (r.data.fop_code.startsWith("CC")) {
          this.mostrarComponente(this.variables.items.items[3]);
          this.mostrarComponente(this.variables.items.items[4]);
          this.ocultarComponente(this.variables.items.items[7]);
          this.variables.items.items[3].allowBlank = false;
          this.variables.items.items[4].allowBlank = false;

          this.ocultarComponente(this.variables.items.items[2]);
          this.ocultarComponente(this.variables.items.items[5]);
          this.variables.items.items[2].allowBlank = true;
          this.variables.items.items[5].allowBlank = true;
          this.variables.items.items[2].reset();
          this.variables.items.items[5].reset();
          this.variables.items.items[7].reset();

          this.ventana_detalle.body.dom.style.height = "250px";


        } else if (r.data.fop_code.startsWith("MCO")) {

          this.mostrarComponente(this.variables.items.items[5]);
          this.variables.items.items[5].allowBlank = false;
          this.ocultarComponente(this.variables.items.items[2]);
          this.ocultarComponente(this.variables.items.items[3]);
          this.ocultarComponente(this.variables.items.items[4]);
          this.ocultarComponente(this.variables.items.items[7]);
          this.variables.items.items[2].allowBlank = true;
          this.variables.items.items[3].allowBlank = true;
          this.variables.items.items[4].allowBlank = true;
          this.variables.items.items[2].reset();
          this.variables.items.items[3].reset();
          this.variables.items.items[4].reset();
          this.variables.items.items[7].reset();

          this.ventana_detalle.body.dom.style.height = "240px";

        } else if (r.data.fop_code.startsWith("CU") || r.data.fop_code.startsWith("CT")) {

          this.mostrarComponente(this.variables.items.items[2]);
          this.variables.items.items[2].allowBlank = false;

          this.ocultarComponente(this.variables.items.items[3]);
          this.ocultarComponente(this.variables.items.items[4]);
          this.ocultarComponente(this.variables.items.items[5]);
          this.ocultarComponente(this.variables.items.items[7]);
          this.variables.items.items[3].allowBlank = true;
          this.variables.items.items[4].allowBlank = true;
          this.variables.items.items[5].allowBlank = true;
          this.variables.items.items[3].reset();
          this.variables.items.items[4].reset();
          this.variables.items.items[5].reset();
          this.variables.items.items[7].reset();

          this.ventana_detalle.body.dom.style.height = "240px";
          this.variables.items.items[2].label.dom.innerHTML='Cuenta Corriente';
          this.variables.items.items[2].store.baseParams.ro_activo='no';
          this.variables.items.items[2].modificado = true;
        } else if (r.data.fop_code.startsWith("CA")) {

          this.ocultarComponente(this.variables.items.items[2]);
          this.ocultarComponente(this.variables.items.items[3]);
          this.ocultarComponente(this.variables.items.items[4]);
          this.ocultarComponente(this.variables.items.items[5]);
          this.ocultarComponente(this.variables.items.items[7]);
          this.variables.items.items[2].allowBlank = true;
          this.variables.items.items[3].allowBlank = true;
          this.variables.items.items[4].allowBlank = true;
          this.variables.items.items[5].allowBlank = true;
          this.variables.items.items[2].reset();
          this.variables.items.items[3].reset();
          this.variables.items.items[4].reset();
          this.variables.items.items[5].reset();
          this.variables.items.items[7].reset();

          this.ventana_detalle.body.dom.style.height = "170px";

        } else if (r.data.fop_code.startsWith("RANT")) {  // {dev: breydi.vasquez, date: 27:04/2021, desc: nueva forma de pago}
          this.mostrarComponente(this.variables.items.items[2])
          this.mostrarComponente(this.variables.items.items[7])
          this.variables.items.items[2].allowBlank = false;

          this.ocultarComponente(this.variables.items.items[3]);
          this.ocultarComponente(this.variables.items.items[4]);
          this.ocultarComponente(this.variables.items.items[5]);
          this.variables.items.items[3].allowBlank = true;
          this.variables.items.items[4].allowBlank = true;
          this.variables.items.items[5].allowBlank = true;
          this.variables.items.items[3].reset();
          this.variables.items.items[4].reset();
          this.variables.items.items[5].reset();
          this.variables.items.items[2].reset();
          this.ventana_detalle.body.dom.style.height = "240px";
          this.variables.items.items[2].label.dom.innerHTML='Grupo';
          this.variables.items.items[2].store.baseParams.ro_activo='si';
          this.variables.items.items[2].modificado = true;
        }

      },this);
      /**********************************************************/
      this.variables.items.items[0].on('select', function(c,r,i) {
        this.variables.items.items[7].reset()
        this.variables.items.items[7].store.baseParams.id_moneda=r.data.id_moneda;
        this.variables.items.items[7].store.baseParams.id_auxiliar_anticipo=this.variables.items.items[2].getValue();
        this.variables.items.items[7].modificado = true;
      },this);

      this.variables.items.items[2].on('select', function(c,r,i) {
        this.variables.items.items[7].reset()
        this.variables.items.items[7].store.baseParams.id_moneda=this.variables.items.items[0].getValue();
        this.variables.items.items[7].store.baseParams.id_auxiliar_anticipo=r.data.id_auxiliar;
        this.variables.items.items[7].modificado = true;
      },this);

      this.variables.items.items[7].on('select', function(c,r,i) {
        var saldo = r.data.saldo;
        var imp1 = this.variables.items.items[6].getValue();
        var mon_sel = r.data.moneda;
        var dif = imp1 - saldo;

        if (imp1 > saldo){
            Ext.Msg.show({
             title:'<h1 style="color:red"><center>AVISO</center></h1>',
             msg: '<b>El saldo del recibo es: <span style="color:red;"> '+mon_sel+ ' '+saldo+'</span> Falta un monto de <span style="color:red;">'+ mon_sel +' '+ dif +'</span> para la forma de pago recibo anticipo</b>',
             maxWidth : 400,
             width: 380,
             buttons: Ext.Msg.OK,
             scope:this
            });
        }
      },this);

    },

    insertarNuevo : function (win) {
      if (this.variables.items.items[0].getValue() == '') {
          Ext.Msg.show({
  			   title:'Información',
  			   msg: 'Complete los campos para guardar el detalle!',
  			   buttons: Ext.Msg.OK,
           icon: Ext.MessageBox.QUESTION,
  			   scope:this
  			});
      } else {
      var grillaRecord =  Ext.data.Record.create([
          {name:'id_moneda', type: 'numeric'},
          {name:'desc_moneda', type: 'varchar'},
          {name:'id_medio_pago', type: 'numeric'},
          {name:'desc_medio_pago', type: 'varchar'},
          {name:'id_auxiliar', type: 'numeric'},
          {name:'desc_auxiliar', type: 'varchar'},
          {name:'num_tarjeta', type: 'varchar'},
          {name:'codigo_autorizacion', type: 'varchar'},
          {name:'mco', type: 'varchar'},
          {name:'monto_total_local', type: 'numeric'},
          {name:'monto_total_extranjero', type: 'numeric'},
          {name:'id_venta_recibo', type: 'numeric'},
          {name:'nro_recibo', type: 'numeric'},
    ]);

    if (this.variables.items.items[0].lastSelectionText == 'USD') {
      var monto_local = 0;
      var monto_extranjero = this.variables.items.items[6].getValue();
    } else {
      var monto_local = this.variables.items.items[6].getValue();
      var monto_extranjero = 0;
    }


    var myNewRecord = new grillaRecord({
          id_moneda: this.variables.items.items[0].getValue(),
          desc_moneda: this.variables.items.items[0].lastSelectionText,
          id_medio_pago: this.variables.items.items[1].getValue(),
          desc_medio_pago: this.variables.items.items[1].lastSelectionText,
          id_auxiliar: this.variables.items.items[2].getValue(),
          desc_auxiliar: this.variables.items.items[2].lastSelectionText,
          num_tarjeta: this.variables.items.items[3].getValue(),
          codigo_autorizacion: this.variables.items.items[4].getValue(),
          mco: this.variables.items.items[5].getValue(),
          monto_total_local: monto_local,
          monto_total_extranjero: monto_extranjero,
          id_venta_recibo:this.variables.items.items[7].getValue(),
          nro_recibo:this.variables.items.items[7].lastSelectionText
      });

      this.mestore.add(myNewRecord);
      this.guardarDetalles();
      win.hide();
    }

    },

    editarFp : function (win,datosEdit) {

      this.mestore.remove(datosEdit);

      var grillaRecord =  Ext.data.Record.create([
          {name:'id_moneda', type: 'numeric'},
          {name:'desc_moneda', type: 'varchar'},
          {name:'id_medio_pago', type: 'numeric'},
          {name:'desc_medio_pago', type: 'varchar'},
          {name:'id_auxiliar', type: 'numeric'},
          {name:'desc_auxiliar', type: 'varchar'},
          {name:'num_tarjeta', type: 'varchar'},
          {name:'codigo_autorizacion', type: 'varchar'},
          {name:'mco', type: 'varchar'},
          {name:'monto_total_local', type: 'numeric'},
          {name:'monto_total_extranjero', type: 'numeric'},
          {name:'id_venta_recibo', type: 'numeric'},
          {name:'nro_recibo', type: 'numeric'}
    ]);

    if (this.variables.items.items[0].lastSelectionText == 'USD') {
      var monto_local = 0;
      var monto_extranjero = this.variables.items.items[6].getValue();
    } else {
      var monto_local = this.variables.items.items[6].getValue();
      var monto_extranjero = 0;
    }


    var myNewRecord = new grillaRecord({
          id_moneda: this.variables.items.items[0].getValue(),
          desc_moneda: this.variables.items.items[0].lastSelectionText,
          id_medio_pago: this.variables.items.items[1].getValue(),
          desc_medio_pago: this.variables.items.items[1].lastSelectionText,
          id_auxiliar: this.variables.items.items[2].getValue(),
          desc_auxiliar: this.variables.items.items[2].lastSelectionText,
          num_tarjeta: this.variables.items.items[3].getValue(),
          codigo_autorizacion: this.variables.items.items[4].getValue(),
          mco: this.variables.items.items[5].getValue(),
          monto_total_local: monto_local,
          monto_total_extranjero: monto_extranjero,
          id_venta_recibo: this.variables.items.items[7].getValue(),
          nro_recibo: this.variables.items.items[7].lastSelectionText
      });
      this.mestore.add(myNewRecord);
      this.guardarDetalles();
      win.hide();


    },




    guardarDetalles : function(){
      this.mestore.commitChanges();
      this.megrid.getView().refresh();
      this.obtenersuma();
    },


    recuperarFormasdePago1 : function (win) {

      var grillaRecord =  Ext.data.Record.create([
          {name:'id_moneda', type: 'numeric'},
          {name:'desc_moneda', type: 'varchar'},
          {name:'id_medio_pago', type: 'numeric'},
          {name:'desc_medio_pago', type: 'varchar'},
          {name:'id_auxiliar', type: 'numeric'},
          {name:'desc_auxiliar', type: 'varchar'},
          {name:'num_tarjeta', type: 'varchar'},
          {name:'codigo_autorizacion', type: 'varchar'},
          {name:'mco', type: 'varchar'},
          {name:'monto_total_local', type: 'numeric'},
          {name:'monto_total_extranjero', type: 'numeric'},
          {name:'id_venta_recibo', type: 'numeric'}
    ]);

    if (this.data.desc_moneda1 == 'USD') {
      var monto_local = 0;
      var monto_extranjero = this.data.monto_mp_1;
    } else {
      var monto_local = this.data.monto_mp_1;
      var monto_extranjero = 0;
    }


    var myNewRecord = new grillaRecord({
          id_moneda: this.data.moneda_1??"",
          desc_moneda: this.data.desc_moneda1??"",
          id_medio_pago: this.data.medio_pago_1??"",
          desc_medio_pago: this.data.desc_medio_pago_1??"",
          id_auxiliar: this.data.id_auxiliar??"",
          desc_auxiliar: this.data.desc_id_auxiliar??"",
          num_tarjeta: this.data.nro_tarjeta_1??"",
          codigo_autorizacion: this.data.codigo_autorizacion_1??"",
          mco: this.data.mco1??"",
          monto_total_local: monto_local,
          monto_total_extranjero: monto_extranjero,
          id_venta_recibo: this.data.id_venta_recibo??""
      });
      this.mestore.add(myNewRecord);
      this.guardarDetalles();

    },

    recuperarFormasdePago2 : function (win) {
      console.log("aqui datos llega",this.data);
      var grillaRecord =  Ext.data.Record.create([
          {name:'id_moneda', type: 'numeric'},
          {name:'desc_moneda', type: 'varchar'},
          {name:'id_medio_pago', type: 'numeric'},
          {name:'desc_medio_pago', type: 'varchar'},
          {name:'id_auxiliar', type: 'numeric'},
          {name:'desc_auxiliar', type: 'varchar'},
          {name:'num_tarjeta', type: 'varchar'},
          {name:'codigo_autorizacion', type: 'varchar'},
          {name:'mco', type: 'varchar'},
          {name:'monto_total_local', type: 'numeric'},
          {name:'monto_total_extranjero', type: 'numeric'},
          {name:'id_venta_recibo', type: 'numeric'}
    ]);

    if (this.data.desc_moneda2 == 'USD') {
      var monto_local = 0;
      var monto_extranjero = this.data.monto_mp_2;
    } else {
      var monto_local = this.data.monto_mp_2;
      var monto_extranjero = 0;
    }


    var myNewRecord = new grillaRecord({
          id_moneda: this.data.moneda_2??"",
          desc_moneda: this.data.desc_moneda2??"",
          id_medio_pago: this.data.medio_pago_2??"",
          desc_medio_pago: this.data.desc_medio_pago_2??"",
          id_auxiliar: (this.data.id_auxiliar2??""),
          desc_auxiliar: (this.data.desc_id_auxiliar2??""),
          num_tarjeta: this.data.nro_tarjeta_2??"",
          codigo_autorizacion: this.data.codigo_autorizacion_2??"",
          mco: this.data.mco2??"",
          monto_total_local: monto_local,
          monto_total_extranjero: monto_extranjero,
          id_venta_recibo: this.data.id_venta_recibo_2??"",
      });
      this.mestore.add(myNewRecord);
      this.guardarDetalles();

    },



    loadValoresIniciales:function()
    {
       Phx.vista.FormVariasFormasPago.superclass.loadValoresIniciales.call(this);
    },

    Atributos:[
      {
  			config:{
  				name: 'tipo_factura',
  				fieldLabel: 'tipo factura',
  				allowBlank: false,
  				width:200,
  				maxLength:20,
          hidden:true,
          disabled:true,
  			},
  			type:'TextField',
  			id_grupo:0,
  			form:true,
  			//valorInicial:'0'
  		},
      {
  			config:{
  				name: 'id_formula',
  				fieldLabel: 'Formula',
  				allowBlank: false,
  				width:200,
          hidden:true,
          disabled:true,
  			},
  			type:'TextField',
  			id_grupo:0,
  			form:true,
  			//valorInicial:'0'
  		},
      {
        config:{
          name: 'id_venta',
          fieldLabel: 'id_venta',
          allowBlank: false,
          width:200,
          hidden:true,
          disabled:true,
        },
        type:'TextField',
        id_grupo:0,
        form:true,
        //valorInicial:'0'
      },
      {
  			config:{
  				name: 'fecha_factura',
  				fieldLabel: 'Fecha Factura',
  				allowBlank: false,
  				width:200,
  				maxLength:20,
          hidden:true,
          disabled:true,
  			},
  			type:'TextField',
  			id_grupo:0,
  			form:true,
  			//valorInicial:'0'
  		},
      {
  			config:{
  				name: 'moneda_recibo',
  				fieldLabel: ' Moneda Recibo',
  				allowBlank: true,
  				width:200,
          listWidth:250,
          disabled:true,
          resizable:true,
          style: {
               background: '#EFFFD6',
               color: 'red',
               fontWeight:'bold'
             },
          emptyText: 'Moneda a pagar...',
          store: new Ext.data.JsonStore({
              url: '../../sis_parametros/control/Moneda/listarMoneda',
              id: 'id_moneda',
              root: 'datos',
              sortInfo: {
                  field: 'moneda',
                  direction: 'ASC'
              },
              totalProperty: 'total',
              fields: ['id_moneda', 'codigo', 'moneda', 'codigo_internacional'],
              remoteSort: true,
              baseParams: {filtrar: 'si',par_filtro: 'moneda.moneda#moneda.codigo#moneda.codigo_internacional'}
          }),
          valueField: 'id_moneda',
          gdisplayField : 'codigo_internacional',
          displayField: 'codigo_internacional',
          hiddenName: 'id_moneda',
          tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;"><b style="color:black;">Moneda:</b> <b>{moneda}</b></p><p style="color:red;"><b style="color:black;">Código:</b> <b>{codigo_internacional}</b></p></div></tpl>',
          forceSelection: true,
          typeAhead: false,
          triggerAction: 'all',
          lazyRender: true,
          mode: 'remote',
          pageSize: 15,
          queryDelay: 1000,
          //disabled:true,
          minChars: 2
      },
      type: 'ComboBox',
      id_grupo: 1,
      form: true
  		},
      {
  			config: {
  				name: 'id_auxiliar_anticipo',
  				fieldLabel: 'Cuenta Corriente',
  				allowBlank: true,
          width:200,
  				emptyText: 'Cuenta Corriente...',
  				store: new Ext.data.JsonStore({
  					url: '../../sis_contabilidad/control/Auxiliar/listarAuxiliar',
  					id: 'id_auxiliar',
  					root: 'datos',
  					sortInfo: {
  						field: 'codigo_auxiliar',
  						direction: 'ASC'
  					},
  					totalProperty: 'total',
  					fields: ['id_auxiliar', 'codigo_auxiliar','nombre_auxiliar'],
  					remoteSort: true,
  					baseParams: {par_filtro: 'auxcta.codigo_auxiliar#auxcta.nombre_auxiliar',corriente:'si'}
  				}),
  				valueField: 'id_auxiliar',
  				displayField: 'nombre_auxiliar',
  				gdisplayField: 'codigo_auxiliar',
  				hiddenName: 'id_auxiliar',
  				tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre_auxiliar}</p><p>Codigo:{codigo_auxiliar}</p> </div></tpl>',
  				forceSelection: true,
  				typeAhead: false,
  				triggerAction: 'all',
  				lazyRender: true,
  				mode: 'remote',
  				pageSize: 15,
  				queryDelay: 1000,
  				gwidth: 150,
  				listWidth:350,
  				resizable:true,
          disabled:true,
          hidden:true,
  				minChars: 2,
  				renderer : function(value, p, record) {
  					return String.format('{0}', record.data['nombre_auxiliar']);
  				}
  			},
  			type: 'ComboBox',
  			id_grupo: 1,
  			grid: true,
  			form: true
  		},
		{
			config:{
				name: 'nit',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/CarnetIdentidad.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> NIT</span>',
				allowBlank: false,
				width:200,
				maxLength:20,
        disabled:true,
			},
			type:'NumberField',
			id_grupo:0,
			form:true,
			//valorInicial:'0'
		},
		{
			config : {
				name : 'id_cliente',
				fieldLabel : '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Razón Social</span>',
				allowBlank : false,
				emptyText : 'Cliente...',
				store : new Ext.data.JsonStore({
					url : '../../sis_ventas_facturacion/control/Cliente/listarCliente',
					id : 'id_cliente',
					root : 'datos',
					sortInfo : {
						field : 'nombres',
						direction : 'ASC'
					},
					totalProperty : 'total',
					fields : ['id_cliente', 'nombres', 'primer_apellido', 'segundo_apellido','nombre_factura','nit'],
					remoteSort : true,
					baseParams : {
						par_filtro : 'cli.nombres#cli.primer_apellido#cli.segundo_apellido#nombre_factura#nit'
					}
				}),
				valueField : 'id_cliente',
				displayField : 'nombre_factura',
				gdisplayField : 'nombre_factura',
				hiddenName : 'id_cliente',
				forceSelection : false,
				typeAhead : false,
				tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>NIT:</b> {nit}</p><p><b>Razon Social:</b> {nombre_factura}</p><p><b>Nombre:</b> {nombres} {primer_apellido} {segundo_apellido}</p> </div></tpl>',
				triggerAction : 'all',
				lazyRender : true,
				mode : 'remote',
				pageSize : 10,
				queryDelay : 1000,
				turl:'../../../sis_ventas_facturacion/vista/cliente/Cliente.php',
				ttitle:'Clientes',
				// tconfig:{width:1800,height:500},
				tasignacion : true,
				tname : 'id_cliente',
				tdata:{},
				cls:'uppercase',
				tcls:'Cliente',
				gwidth : 170,
        width:200,
				minChars : 2,
        disabled:true,
				style:'text-transform:uppercase;'
			},
			type : 'TrigguerCombo',
			id_grupo : 0,
			form : true
		},
    //bvp
    {
        config:{
                labelSeparator:'',
                inputType:'hidden',
                name: 'nombre_factura'
        },
        type:'Field',
        form:true
    },
    {
			config:{
				name: 'observaciones',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Observaciones</span>',
				allowBlank: true,
				width:200,
        disabled:true,
				style:'text-transform:uppercase;'
			},
				type:'TextArea',
				id_grupo:0,
				form:true
		},
    {
			config:{
				name: 'excento',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Exento</span>',
				allowBlank: true,
				width:200,
				maxLength:20,
        disabled:true,
			},
			type:'NumberField',
			id_grupo:1,
			form:true,
			//valorInicial:'0'
		},
      {
            config: {
                name: 'id_sucursal',
                fieldLabel: 'Sucursal',
                allowBlank: false,
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
                    baseParams: {filtro_usuario: 'si',par_filtro: 'suc.nombre#suc.codigo'}
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
                width:200,
                queryDelay: 1000,
                disabled:true,
                minChars: 2
            },
            type: 'ComboBox',
            id_grupo: 1,
            form: true
        },

        {
  	            config: {
  	                name: 'id_punto_venta',
  	                fieldLabel: 'Punto de Venta',
  	                allowBlank: false,
                    width:200,
                    //disable:true,
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
  	                    baseParams: {filtro_usuario: 'si',par_filtro: 'puve.nombre#puve.codigo#puve.id_punto_venta'}
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
  	                gwidth: 200,
  	                minChars: 1,
  	                disabled:true,
  	                renderer : function(value, p, record) {
  	                    return String.format('{0}', record.data['nombre_punto_venta']);
  	                }
  	            },
  	            type: 'ComboBox',
  	            id_grupo: 1,
  	            filters: {pfiltro: 'puve.nombre',type: 'string'},
  	            grid: true,
  	            form: true
  	        },
            {
        			config:{
        				name: 'informe',
        				fieldLabel: '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Informe</span>',
        				allowBlank: true,
        				width:200,
                disabled:true,
                hidden:true,
        				style:'text-transform:uppercase;'
        			},
        				type:'TextArea',
        				id_grupo:1,
        				form:true
        		},
            {
        			config:{
        				name: 'total_venta',
        				fieldLabel: '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Total Venta</span>',
        				allowBlank: false,
        				width:200,
        				maxLength:20,
                disabled:true,
        			},
        			type:'NumberField',
        			id_grupo:2,
        			form:true,
        			//valorInicial:'0'
        		},
            {
        			config:{
        				name: 'cantidad_conceptos',
        				fieldLabel: '<img src="../../../lib/imagenes/facturacion/Cantidad.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Cantidad Conceptos</span>',
        				allowBlank: false,
        				width:200,
        				maxLength:20,
                disabled:true,
        			},
        			type:'NumberField',
        			id_grupo:2,
        			form:true,
        			//valorInicial:'0'
        		},
            {
        			config:{
        				name: 'nro_factura',
        				fieldLabel: '<img src="../../../lib/imagenes/facturacion/CarnetIdentidad.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Nro. Factura</span>',
        				allowBlank: false,
        				width:200,
        				maxLength:20,
                disabled:true,
                hidden:true,
        			},
        			type:'NumberField',
        			id_grupo:2,
        			form:true,
        			//valorInicial:'0'
        		},
            {
        			config:{
        				name: 'asociar_boletos',
        				fieldLabel: '<img src="../../../lib/imagenes/facturacion/ticket.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Boleto Asociado</span>',
        				allowBlank: true,
        				width:200,
        				maxLength:20,
                disabled:true,
                hidden:true,
        			},
        			type:'NumberField',
        			id_grupo:2,
        			form:true,
        			//valorInicial:'0'
        		},
            {
              config: {
                  name: 'id_dosificacion',
                  fieldLabel: '<img src="../../../lib/imagenes/facturacion/dosifica.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Dosificación</span>',
                  allowBlank: false,
                  width:200,
                  emptyText: 'Elija un Dosi...',
                  store: new Ext.data.JsonStore({
                      url: '../../sis_ventas_facturacion/control/Dosificacion/listarDosificacion',
                      id: 'id_dosificacion',
                      root: 'datos',
                      sortInfo: {
                          field: 'nroaut',
                          direction: 'ASC'
                      },
                      totalProperty: 'total',
                      fields: ['id_dosificacion', 'nroaut', 'desc_actividad_economica','inicial','final'],
                      remoteSort: true,
                      baseParams: {filtro_usuario: 'si',par_filtro: 'dos.nroaut'}
                  }),
                  valueField: 'id_dosificacion',
                  displayField: 'nroaut',
                  hiddenName: 'id_dosificacion',
                  tpl: new Ext.XTemplate([
                      '<tpl for=".">',
                      '<div class="x-combo-list-item">',
                      '<p><b>N° Autorización:</b><span style="color: green; font-weight:bold;"> {nroaut}</span></p></p>',
                      '<p><b>Actividad Económica:</b> <span style="color: blue; font-weight:bold;">{desc_actividad_economica}</span></p>',
                      '<p><b>N° Inicial:</b> <span style="color: #D35000; font-weight:bold;">{inicial}</span></p>',
                      '<p><b>N° Final:</b> <span style="color: red; font-weight:bold;">{final}</span></p>',
                      '</div></tpl>'
                    ]),
                  //tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Autorizacion:</b> {nroaut}</p><p><b>Actividad:</b> {desc_actividad_economica}</p></p><p><b>No Inicio:</b> {inicial}</p><p><b>No Final:</b> {final}</p></div></tpl>',
                  forceSelection: true,
                  typeAhead: false,
                  triggerAction: 'all',
                  lazyRender: true,
                  mode: 'remote',
                  resizable:true,
                  pageSize: 15,
                  queryDelay: 1000,
                  gwidth: 150,
                  minChars: 2,
                  listWidth:'550',
                  disabled : true,
                  hidden:true,
              },
              type: 'ComboBox',
              id_grupo: 2,
              grid: false,
              form: true
            },
            {
                config:{
                    name: 'cambio',
                    fieldLabel: '',
                    allowBlank:true,
                    allowDecimals:true,
                    decimalPrecision:2,
                    allowNegative : false,
                    readOnly:true,
                    gwidth: 110,
                    disabled:false,
                    style: {
                      backgroundColor: '#EFFFD6',
                      backgroundImage:'none',
                      marginLeft:'-120px',
                      height:'100px',
                      width: '200px',
                      //color:'#2A00FE',
                      textAlign:'center',
                      fontSize:'40px',
                      position:'center',
                      fontWeight:'bold',
                    }
                },
                type:'NumberField',
                id_grupo:11,
                grid:false,
                form:true
            },
            {
                config:{
                    name: 'cambio_moneda_extranjera',
                    fieldLabel: '',
                    allowBlank:true,
                    //anchor: '20%',
                    allowDecimals:true,
                    decimalPrecision:2,
                    allowNegative : false,
                    readOnly:true,
                    disabled:false,
                    gwidth: 110,
                    style: {
                      backgroundColor: '#EFFFD6',
                      backgroundImage:'none',
                      //border:'2px solid red',
                      marginLeft:'-120px',
                      height:'100px',
                      color:'blue',
                      width: '200px',
                      fontSize:'40px',
                      textAlign:'center',
                      position:'center',
                      fontWeight:'bold'
                    }
                },
                type:'NumberField',
                id_grupo:12,
                grid:false,
                form:true
            },

    ],
    title: 'Formulario Venta',

    onAfterEdit:function(re, o, rec, num){
            //set descriptins values ...  in combos boxs

            var cmb_rec = this.detCmp['id_medio_pago'].store.getById(rec.get('id_medio_pago'));
            if(cmb_rec) {

               console.log("aqui llega datos del detGrid",cmb_rec);

                rec.set('codigo', cmb_rec.get('codigo'));
            }

        },

        onSubmit: function(o) {
            //  validar formularios

            var arra = [], i, j, me = this;
            var detalle = [];

            for (i = 0; i < me.megrid.store.getCount(); i++) {
                var record = me.megrid.store.getAt(i);
                arra[i] = record.data;
            }

            for (j = 0; j < this.data.detalleConceptos.length; j++) {
                var record = this.data.detalleConceptos[j];
                detalle[j] = record.data;
            }

            me.argumentExtraSubmit = { 'json_formas_pago': JSON.stringify(arra,
            				function replacer(key, value) {
                           		if (typeof value === 'string') {
                                	return String(value).replace(/&/g, "%26")
                                }
                                return value;
                            }),
                    'json_detalle_venta': JSON.stringify(detalle,
                    				function replacer(key, value) {
                                   		if (typeof value === 'string') {
                                        	return String(value).replace(/&/g, "%26")
                                        }
                                        return value;
                                    }),
            };

            if( i > 0 &&  !this.editorDetail.isVisible()){
                 Phx.vista.FormVariasFormasPago.superclass.onSubmit.call(this,o);
            }
            else{
                alert('La venta no tiene registrado ningun detalle');
            }
        },

        successSave:function(resp)
        {
        	var datos_respuesta = JSON.parse(resp.responseText);
        	Phx.CP.loadingHide();
      			var d = datos_respuesta.ROOT.datos;
          if (this.data.tipo_factura == 'recibo' || this.data.tipo_factura == 'recibo_manual') {
            Ext.Ajax.request({
      					url:'../../sis_ventas_facturacion/control/Venta/siguienteEstadoRecibo',
      					params:{id_estado_wf_act:d.id_estado_wf,
      									id_proceso_wf_act:d.id_proceso_wf,
      								  tipo:'recibo',
                        ins_edit: this.data.ins_edit},
      					success:this.successWizard,
      					failure: this.conexionFailure,
      					timeout:this.timeout,
      					scope:this
      			});
          }else if (this.data.tipo_factura == 'manual') {
            Ext.Ajax.request({
      					url:'../../sis_ventas_facturacion/control/Cajero/finalizarFacturaManual',
      					params:{id_estado_wf_act:d.id_estado_wf,
      									id_proceso_wf_act:d.id_proceso_wf,
                        tipo_pv:this.data.tipo_punto_venta,
      								  tipo:'manual'},
      					success:this.successWizard,
      					failure: this.conexionFailure,
      					timeout:this.timeout,
      					scope:this
      			});
          } else {
            if (this.data.tipo_punto_venta != 'ato' && this.data.tipo_punto_venta != 'carga') {
              Ext.Ajax.request({
        					url:'../../sis_ventas_facturacion/control/Cajero/siguienteEstadoFactura',
        					params:{id_estado_wf_act:d.id_estado_wf,
        									id_proceso_wf_act:d.id_proceso_wf,
                          tipo_pv:this.data.tipo_punto_venta,
        								  tipo:'recibo'},
        					success:this.successWizard,
        					failure: this.conexionFailure,
        					timeout:this.timeout,
        					scope:this
        			});

            } else {
              Ext.Ajax.request({
                url:'../../sis_ventas_facturacion/control/Cajero/FinalizarFactura',
                params:{id_estado_wf_act:d.id_estado_wf,
                  id_proceso_wf_act:d.id_proceso_wf,
                  tipo_pv:this.data.tipo_punto_venta,
                  tipo:'recibo'},
                  success:this.successWizard,
                  failure: this.conexionFailure,
                  timeout:this.timeout,
                  scope:this
                });
            }


          }
        this.data.panel_padre.close();
        this.panel.close();
        },

        successWizard:function(resp){
            // var rec=this.sm.getSelected();
            var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if (objRes.ROOT.datos.estado == 'finalizado' && this.tipo_factura != 'manual') {
                this.id_venta = objRes.ROOT.datos.id_venta;
                this.id_proceso_wf = objRes.ROOT.datos.id_proceso_wf;
                this.imprimirNota();
            }
            Phx.CP.loadingHide();
            //resp.argument.wizard.panel.destroy();
            this.panel.destroy();
            //this.reload();
         },

         imprimirNota: function(){
      				Phx.CP.loadingShow();
            if (this.data.tipo_factura == 'recibo') {
              Ext.Ajax.request({
      						url : '../../sis_ventas_facturacion/control/Venta/reporteRecibo',
      						params : {
      							'id_venta' : this.id_venta,
      							'formato_comprobante' : this.data.variables_globales.formato_comprobante,
      							'tipo_factura': this.data.tipo_factura,
      							'id_punto_venta':this.data.puntoVenta
      						},
      						success : this.successExportHtml,
      						failure : this.conexionFailure,
      						timeout : this.timeout,
      						scope : this
      					});
            }if (this.data.tipo_factura == 'computarizada') {
              if (this.data.variables_globales.formato_comprobante == 'carta') {
                    Ext.Ajax.request({
                 						url : '../../sis_ventas_facturacion/control/Cajero/reporteFacturaCarta',
                 						params : {
                              'id_proceso_wf' : this.id_proceso_wf ,
                 							'id_punto_venta' : this.data.variables_globales.id_punto_venta,
                 							'formato_comprobante' : this.data.variables_globales.formato_comprobante,
                 							'tipo_factura': this.data.tipo_factura
                 						},
                 						success : this.successExport,
                 						failure : this.conexionFailure,
                 						timeout : this.timeout,
                 						scope : this
                 					});
                } else if (this.data.variables_globales.formato_comprobante == 'rollo') {
                    Ext.Ajax.request({
                            url : '../../sis_ventas_facturacion/control/Cajero/reporteFactura',
                            params : {
                              'id_proceso_wf' : this.id_proceso_wf ,
                              'id_punto_venta' : this.data.variables_globales.id_punto_venta,
                              'formato_comprobante' : this.data.variables_globales.formato_comprobante,
                              'tipo_factura': this.data.tipo_factura
                            },
                            success : this.successExportHtml,
                            failure : this.conexionFailure,
                            timeout : this.timeout,
                            scope : this
                          });
                }
            }


      	},

    successExportHtml: function (resp) {
          Phx.CP.loadingHide();
          var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
          var objetoDatos = (objRes.ROOT == undefined)?objRes.datos:objRes.ROOT.datos;
          var wnd = window.open("about:blank", "", "_blank");
      wnd.document.write(objetoDatos.html);
      },

})
</script>
