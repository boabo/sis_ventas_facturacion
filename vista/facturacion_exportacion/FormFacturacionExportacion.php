<?php
/**
*@package pXP
*@file    FormFacturacionExportacion.php
*@author  Ismael Valdivia Aranibar
*@date    4-12-2020
*@description Registrar Varias Formas de Pago
*/
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.FormFacturacionExportacion=Ext.extend(Phx.frmInterfaz,{
    ActSave:'../../sis_ventas_facturacion/control/FacturacionExportacion/insertarVentaExportacion',
    tam_pag: 10,
    layout: 'fit',
    tabEnter: true,
    autoScroll: false,
    breset: false,
    labelSubmit: '<div><span style="vertical-align: middle; font-size:25px; font-weight:bold; color:#1479B8; text-shadow: 3px 0px 0px #000000;">GENERAR</span></div>',
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
        Phx.vista.FormFacturacionExportacion.superclass.constructor.call(this,config);
        this.init();
        this.onNew();
        this.iniciarEventos();

    },

    /*Los componentes que se editan*/
    buildComponentesDetalle: function(){
        var  me = this;
        this.detCmp = {
                    'tipo': new Ext.form.ComboBox({
                            name: 'tipo',
                            fieldLabel: 'Tipo detalle',
                            allowBlank:false,
                            emptyText:'Tipo...',
                            typeAhead: true,
                            triggerAction: 'all',
                            lazyRender:true,
                            mode: 'local',
                            gwidth: 150,
                            store:this.tipoDetalleArray
                    }),

                    'id_producto': new Ext.form.ComboBox({
                                            name: 'id_producto',
                                            fieldLabel: '<img src="../../../lib/imagenes/facturacion/BolsaCompraColores.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Producto/Servicio</span>',
                                            allowBlank: false,
                                            emptyText: 'Productos...',
                                            store: new Ext.data.JsonStore({
                                                url: '../../sis_ventas_facturacion/control/Servicios/listarServicios',
                                                id: 'id_producto',
                                                root: 'datos',
                                                sortInfo: {
                                                    field: 'desc_ingas',
                                                    direction: 'ASC'
                                                },
                                                totalProperty: 'total',
                                                fields: ['id_concepto_ingas', 'tipo','desc_ingas','requiere_descripcion','precio','excento','contabilizable','boleto_asociado','nombre_actividad','comision'],
                                                remoteSort: true,
                                                baseParams: {par_filtro: 'ingas.desc_ingas'}
                                            }),
                                            valueField: 'id_producto',
                                            displayField: 'desc_ingas',
                                            gdisplayField: 'desc_ingas',
                                            hiddenName: 'id_producto',
                                            forceSelection: true,
                                            tpl: new Ext.XTemplate([
                                        				'<tpl for=".">',
                                        				'<div class="x-combo-list-item">',
                                                '<p><b>Nombre:</b><span style="color: green; font-weight:bold;"> {desc_ingas}</span></p></p>',
                                        				'<p><b>Actividad Económica:</b><span style="color: green; font-weight:bold;"> {nombre_actividad}</span></p></p>',
                                        				'<p><b>Descripcion:</b> <span style="color: blue; font-weight:bold;">{desc_ingas}</span></p>',
                                        				'<p><b>Precio:</b> <span style="color: blue; font-weight:bold;">{precio}</span></p>',
                                        				'<p><b>Tiene Exento:</b> <span style="color: red; font-weight:bold;">{desc_ingas}</span></p>',
                                                '<p><b>Requiere Descripción:</b> <span style="color: red; font-weight:bold;">{desc_ingas}</span></p>',
                                                '<p><b>Contabilizable:</b> <span style="color: red; font-weight:bold;">{contabilizable}</span></p>',
                                        				'<p><b>Asociar:</b> <span style="color: red; font-weight:bold;">{boleto_asociado}</span></p>',
                                        				'</div></tpl>'
                                        			]),
                                            typeAhead: false,
                                            triggerAction: 'all',
                                            lazyRender: true,
                                            mode: 'remote',
                                            resizable:true,
                                            pageSize: 15,
                                            queryDelay: 1000,
                                            anchor: '100%',
                                            width : 250,
                                            listWidth:'600',
                                            minChars: 2 ,
                                            disabled:true,

                                         }),
                    'descripcion': new Ext.form.TextField({
                            name: 'descripcion',
                            fieldLabel: 'Descripcion',
                            allowBlank:true,
                            gwidth: 150,
                            //disabled : true
                    }),

                    'cantidad': new Ext.form.NumberField({
                                        name: 'cantidad',
                                        msgTarget: 'title',
                                        fieldLabel: 'Cantidad',
                                        allowBlank: false,
                                        allowDecimals: me.cantidadAllowDecimals,
                                        decimalPrecision : 2,
                                        enableKeyEvents : false,


                                }),
                    'precio_unitario': new Ext.form.NumberField({
                                        name: 'precio_unitario',
                                        msgTarget: 'title',
                                        fieldLabel: 'P/U',
                                        allowBlank: false,
                                        allowDecimals: true,
                                        decimalPrecision : 2,
                                        enableKeyEvents : true
                                }),
                    'precio_total': new Ext.form.NumberField({
                                        name: 'precio_total',
                                        msgTarget: 'title',
                                        fieldLabel: 'Total',
                                        allowBlank: false,
                                        allowDecimals: false,
                                        maxLength:10,
                                        readOnly :true,
                                })

              }


    },
    /*******************************/


        buildDetailGrid:function () {
              var Items = Ext.data.Record.create([{
                              name: 'cantidad',
                              type: 'int'
                          }, {
                              name: 'id_producto',
                              type: 'int'
                          },{
                              name: 'tipo',
                              type: 'string'
                          }
                          ]);
                this.mestore = new Ext.data.JsonStore({
                            url: '../../sis_ventas_facturacion/control/Cajero/listarVentaDetalle',
                            id: 'id_venta_detalle',
                            root: 'datos',
                            totalProperty: 'total',
                            fields: [
                                {name:'id_venta_detalle', type: 'numeric'},
                                {name:'id_venta', type: 'numeric'},
                                {name:'id_producto', type: 'numeric'},
                                {name:'id_sucursal_producto', type: 'numeric'},
                                {name:'nombre_producto', type: 'string'},
                                {name:'precio_unitario', type: 'numeric'},
                                {name:'cantidad', type: 'numeric'},
                                {name:'precio_total', type: 'numeric'},
                                {name:'descripcion', type: 'string'},
                                {name:'tipo', type: 'string'},
                                {name:'estado_reg', type: 'string'},
                                {name:'id_usuario_ai', type: 'numeric'},
                                {name:'usuario_ai', type: 'string'},
                                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                                {name:'id_usuario_reg', type: 'numeric'},
                                {name:'id_usuario_mod', type: 'numeric'},
                                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},

                            ],
                            remoteSort: true,
                            baseParams: {dir:'ASC',sort:'id_venta_detalle',limit:'50',start:'0'}
                        });

                this.summary = new Ext.ux.grid.GridSummary();

                this.editorDetail = new Ext.ux.grid.RowEditor({
                    });

                this.megrid = new Ext.grid.EditorGridPanel({
                    layout: 'fit',
                    store:  this.mestore,
                    region: 'center',
                    split: true,
                    border: false,
                    clicksToEdit: 1,
                    plain: true,
                    plugins: [ this.summary],
                    stripeRows: true,
                    tbar: [
                      {
                  	    text: '<div style="font-weight:bold; font-size:15px;"><img src="../../../lib/imagenes/facturacion/guardar.png" style="width:30px; vertical-align: middle;"> Guardar</div>',
                        scope: this,
                        id:'botonGuardar',
              			    handler: function(btn) {
                          this.guardarDetalles();
              			    }
                      },
                      {
                      text: '<div style="font-weight:bold; font-size:15px;"><img src="../../../lib/imagenes/facturacion/anadir.png" style="width:30px; vertical-align: middle;"> Agregar Detalle</div>',
                      scope: this,
                      id:'botonAgregar',
                        handler : function(){
                          this.formularioAgregar();
                          }
                      },
                      {
                      text: '<div style="font-weight:bold; font-size:15px;"><img src="../../../lib/imagenes/facturacion/eliminar.png" style="width:30px; vertical-align: middle;"> Eliminar</div>',
                      scope: this,
                      id:'botonEliminar',
                        handler : function(){
                          var index = this.megrid.getSelectionModel().getSelectedCell();
                          if (!index) {
                              return false;
                          }
                          var rec = this.mestore.getAt(index[0]);
                          this.mestore.remove(rec);
                          this.obtenersuma(true);
                        }
                      },
                    ],

                    columns: [
                    new Ext.grid.RowNumberer(),
                    // {
                    //     header: 'Tipo',
                    //     dataIndex: 'tipo',
                    //     width: 90,
                    //     sortable: false,
                    //     //editor: this.detCmp.tipo
                    // },
                    {
                        header: 'Producto/Servicio',
                        dataIndex: 'id_producto',
                        width: 350,
                        editable: true,
                        sortable: false,
                        renderer:function(value, p, record){
                          return String.format('{0}', record.data['nombre_producto']);
                        },
                        //editor: this.detCmp.id_producto
                    },
                    {
                        header: 'Descripción',
                        dataIndex: 'descripcion',
                        width: 300,
                        //sortable: false,
                        editor:this.detCmp.descripcion
                    },
                    {

                        header: 'Cantidad',
                        dataIndex: 'cantidad',
                        align: 'right',
                        width: 150,
                        summaryType: 'sum',
                        editor: this.detCmp.cantidad
                    },
                    {
                        xtype: 'numbercolumn',
                        header: 'P / Unit',
                        dataIndex: 'precio_unitario',
                        align: 'right',
                        selectOnFocus: true,
                        width: 200,
                        decimalPrecision : 2,
                        summaryType: 'sum',
                        format: '0,0.00',
                        editor: this.detCmp.precio_unitario
                    },
                    {
                        xtype: 'numbercolumn',
                        header: '<b style="color:red; font-size:18px;">Total</b>',
                        dataIndex: 'precio_total',
                        align: 'right',
                        width: 200,/*irva222*/
                        format: '0,0.00',
                        summaryType: 'sum',

                        //editor: this.detCmp.precio_total
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
                                          //collapseFirst : false,
                                          //collapsible: true,
                                          width: '100%',
                                          autoScroll:true,
                                          //autoHeight: true,
                                          style: {
                                                 height:'30%',
                                                 background: 'radial-gradient(ellipse at center, #cfe7fa 0%,#6393c1 100%)',
                                                 //border:'2px solid green'
                                              },
                                          padding: '0 0 0 10',
                                          items:[
                                                  {
                                                      xtype: 'fieldset',
                                                      //title: '<b style="color:#000000;">Datos FOB<b>',
                                                      autoHeight: true,
                                                      border: true,
                                                      style:{
                                                        background:'#89DDFF',
                                                        height : '170px',
                                                         marginLeft:'7px',
                                                         padding:'10'
                                                      },
                                                      defaults: {
                                                          anchor: '23' // leave room for error icon
                                                      },
                                                      items: [],
                                                      id_grupo:0
                                                  },
                                                  {
                                                      xtype: 'fieldset',
                                                      //title: '<b style="color:#000000;">Datos CIF<b>',
                                                      autoHeight: true,
                                                      border: true,
                                                      style:{
                                                    	 				  background:'#F4FF4D',
                                                    	          height : '170px',
                                                                marginLeft:'7px',
                                                                padding:'10'
                                                      //
                                                    	     },
                                                      defaults: {
                                                          anchor: '23' // leave room for error icon
                                                      },
                                                      items: [],
                                                      id_grupo:1
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
                                            width: '100%',
                                            autoScroll:true,
                                            style: {
                                                     height:'30%',
                                                     background:'radial-gradient(ellipse at center, #cfe7fa 0%,#6393c1 100%)',
                                                    // border:'2px solid blue'
                                                   },
                                            padding: '0 0 0 10',
                                            items:[
                                                    {
                                                        xtype: 'fieldset',
                                                        title: '<b style="color:blue;">Datos FOB<b>',
                                                        autoHeight: true,
                                                        border: true,
                                                        style:{
                                                           background:'#9BFF6D',
                                                           height : '170px',
                                                           marginLeft:'7px',
                                                           padding:'10'
                                                         },
                                                        defaults: {
                                                            anchor: '23' // leave room for error icon
                                                        },
                                                        items: [],
                                                        id_grupo:2
                                                    },
                                                    {
                                                        xtype: 'fieldset',
                                                        title: '<b style="color:blue;">Datos CIF<b>',
                                                        autoHeight: true,
                                                        border: true,
                                                        style:{
                                                      	 				  background:'#FFDA43',
                                                      	          height : '170px',
                                                                  marginLeft:'7px',
                                                                  padding:'10'
                                                        //
                                                      	     },
                                                        defaults: {
                                                            anchor: '23' // leave room for error icon
                                                        },
                                                        items: [],
                                                        id_grupo:3
                                                    },
                                                    {
                                                        xtype: 'fieldset',
                                                        title: '<b style="color:blue;">Datos Formas de Pago<b>',
                                                        autoHeight: true,
                                                        border: true,
                                                        style:{
                                                      	 				  background:'#78E100',
                                                      	          height : '170px',
                                                                  marginLeft:'7px',
                                                                  padding:'10'
                                                        //
                                                      	     },
                                                        defaults: {
                                                            anchor: '23' // leave room for error icon
                                                        },
                                                        items: [],
                                                        id_grupo:4
                                                    },

                                                    {
                                                        xtype: 'fieldset',
                                                        id:'segunda_forma_pago',
                                                        title: '<b style="color:blue;">Datos Formas de Pago 2<b>',
                                                        autoHeight: true,
                                                        border: true,
                                                        style:{
                                                                  background:'#78E100',
                                                                  height : '170px',
                                                                  marginLeft:'7px',
                                                                  padding:'10'
                                                        //
                                                             },
                                                        defaults: {
                                                            anchor: '23' // leave room for error icon
                                                        },
                                                        items: [],
                                                        id_grupo:5
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
                 bodyStyle:'margin-left:-7px; margin-top:-7px; padding:10px 10px 0; background:radial-gradient(ellipse at center, #cfe7fa 0%,#6393c1 100%);',
                 width: 650,
                 height:300,
                 defaultType: 'textfield',
                 items: [new Ext.form.ComboBox({
                                     name: 'tipo',
                                     fieldLabel: 'Tipo detalle',
                                     allowBlank:false,
                                     emptyText:'Tipo...',
                                     typeAhead: true,
                                     hidden:true,
                                     triggerAction: 'all',
                                     lazyRender:true,
                                     mode: 'local',
                                     gwidth: 150,
                                     style:{
                                       width: '200px'
                                     },
                                     store:this.tipoDetalleArray
                             }),
                            new Ext.form.ComboBox({
                                                                name: 'id_producto',
                                                                fieldLabel: 'Producto/<br>Servicio',
                                                                allowBlank: false,
                                                                emptyText: 'Productos...',
                                                                store: new Ext.data.JsonStore({
                                                                    url: '../../sis_ventas_facturacion/control/Servicios/listarServicios',
                                                                    id: 'id_producto',
                                                                    root: 'datos',
                                                                    sortInfo: {
                                                                        field: 'desc_ingas',
                                                                        direction: 'ASC'
                                                                    },
                                                                    totalProperty: 'total',
                                                                    fields: ['id_concepto_ingas', 'tipo','nandina','desc_unidad_medida','desc_moneda','id_moneda','desc_ingas','requiere_descripcion','precio','excento','contabilizable','boleto_asociado','nombre_actividad','comision'],
                                                                    remoteSort: true,
                                                                    baseParams: {par_filtro: 'ingas.desc_ingas',listaConceptoEmision:'FACT_EXPO'}
                                                                }),
                                                                valueField: 'id_concepto_ingas',
                                                                displayField: 'desc_ingas',
                                                                gdisplayField: 'desc_ingas',
                                                                hiddenName: 'id_producto',
                                                                forceSelection: true,
                                                                tpl: new Ext.XTemplate([
                                                                   '<tpl for=".">',
                                                                   '<div class="x-combo-list-item">',
                                                                   '<p><b>Concepto: </b><span style="color: blue; font-weight:bold;"> {desc_ingas}</span></p>',
                                                                    '<p><b>Unidad de Medida: </b><span style="color: red; font-weight:bold;"> {desc_unidad_medida}</span></p>',
                                                                     '<p><b>Nandina: </b><span style="color: green; font-weight:bold;"> {nandina}</span></p>',
                                                                   '</div></tpl>'
                                                                 ]),
                                                                // tpl: new Ext.XTemplate([
                                                                //    '<tpl for=".">',
                                                                //    '<div class="x-combo-list-item">',
                                                                //    '<p><b>Nombre:</b><span style="color: green; font-weight:bold;"> {desc_ingas}</span></p></p>',
                                                                //    '<p><b>Actividad Económica:</b><span style="color: green; font-weight:bold;"> {nombre_actividad}</span></p></p>',
                                                                //    '<p><b>Moneda:</b> <span style="color: blue; font-weight:bold;">{desc_moneda}</span></p>',
                                                                //    '<p><b>Precio:</b> <span style="color: blue; font-weight:bold;">{precio}</span></p>',
                                                                //    '<p><b>Tiene Exento:</b> <span style="color: red; font-weight:bold;">{excento}</span></p>',
                                                                //    '<p><b>Requiere Descripción:</b> <span style="color: red; font-weight:bold;">{requiere_descripcion}</span></p>',
                                                                //    '<p><b>Contabilizable:</b> <span style="color: red; font-weight:bold;">{contabilizable}</span></p>',
                                                                //    '<p><b>Asociar:</b> <span style="color: red; font-weight:bold;">{boleto_asociado}</span></p>',
                                                                //    '</div></tpl>'
                                                                //  ]),
                                                                typeAhead: false,
                                                                triggerAction: 'all',
                                                                lazyRender: true,
                                                                mode: 'remote',
                                                                resizable:true,
                                                                pageSize: 20,
                                                                queryDelay: 1000,
                                                                //anchor: '100%',
                                                                width : 450,
                                                                listWidth:'600',
                                                                minChars: 2 ,
                                                                disabled:false,
                                                                 style:{
                                                                   width: '200px'
                                                                 },

                                                              }),
                                                               new Ext.form.TextArea({
                                                                      name: 'descripcion',
                                                                      fieldLabel: 'Descripción',
                                                                      allowBlank:true,
                                                                      style:{
                                                                        width: '190px'
                                                                      },
                                                                      disabled : false,
                                                                      width : 450,
                                                                      hidden : false
                                                              }),

                                                              new Ext.form.NumberField({
                                                                                  name: 'cantidad',
                                                                                  msgTarget: 'title',
                                                                                  fieldLabel: 'Cantidad',
                                                                                  allowBlank: false,
                                                                                  width : 450,
                                                                                  style:{
                                                                                    width: '190px'
                                                                                  },
                                                                                  //allowDecimals: me.cantidadAllowDecimals,
                                                                                  decimalPrecision : 2,
                                                                                  enableKeyEvents : true,


                                                                          }),
                                                              new Ext.form.NumberField({
                                                                                  name: 'precio_unitario',
                                                                                  msgTarget: 'title',
                                                                                  fieldLabel: 'P/U',
                                                                                  allowBlank: false,
                                                                                  allowDecimals: true,
                                                                                  width : 450,
                                                                                  decimalPrecision : 2,
                                                                                  style:{
                                                                                    width: '190px'
                                                                                  },
                                                                                  enableKeyEvents : true
                                                                          }),
                                                               new Ext.form.NumberField({
                                                                                  name: 'precio_total',
                                                                                  msgTarget: 'title',
                                                                                  fieldLabel: 'Total',
                                                                                  style:{
                                                                                    width: '190px'
                                                                                  },
                                                                                  allowBlank: false,
                                                                                  width : 450,
                                                                                  allowDecimals: false,
                                                                                  maxLength:10,
                                                                                  readOnly :true
                                                                          })
                                                            ]

             });
             this.variables = simple;

             /*Aumentando para Filtrar los servicios por id_punto_venta y el tipo del PV (ATO CTO)*/
             /*Aqui el valor por defecto de la cantidad 1*/
             this.variables.items.items[3].setValue(1);
             /********************************************/
             this.variables.items.items[1].store.baseParams.tipo_pv = 'ato';
             this.variables.items.items[1].store.baseParams.regionales = 'BOL';
             // /************************************************************************************************/
                var win = new Ext.Window({
                  title: '<center>AGREGAR DETALLE</center>', //the title of the window
                  width:600,
                  height:300,
                  closeAction:'hide',
                  modal:true,
                  plain: true,
                  items:simple,
                  buttons: [{
                              text:'Guardar',
                              id:'botonGuardarFormulario',
                              scope:this,
                              handler: function(){
                                if (this.megrid.store.data.items.length == 0) {
                                  this.insertarNuevo(win);
                                } else {
                                  var array = new Array();

                                  for (var i = 0; i < this.megrid.store.data.items.length; i++) {
                                    if(this.megrid.store.data.items[i].data.contabilizable!=undefined){
                                      console.log("llega unicos",this.megrid.store.data.items[i].data);
                                      if (!array.includes(this.megrid.store.data.items[i].data.contabilizable)) {
                                        array.push(this.megrid.store.data.items[i].data.contabilizable);
                                        }
                                      }
                                  }
                                  array.push(this.contabilizable);
                                  var unicos = Array.from(new Set(array));
                                  console.log("llega unicos",unicos);
                                  // if (!array.includes(this.contabilizable)) {
                                  if(unicos.length == 0 || unicos.length == 1){
                                    this.insertarNuevo(win);
                                  } else {
                                    Ext.Msg.show({
                                			   title:'Información',
                                         maxWidth : 550,
                                         width: 550,
                                			   msg: 'No puede utilizar conceptos contabilizables y no contabilizables en la misma venta!',
                                			   buttons: Ext.Msg.OK,
                                         icon: Ext.MessageBox.QUESTION,
                                			   scope:this
                                			});
                                  }

                                }
                              }
                          },{
                              text: 'Cancelar',
                              id:'botonCancelarFormulario',
                              handler: function(){
                                  win.hide();
                              }
                          }]

                });
                this.ventana_detalle = win;
                win.show();

                this.registroDetalleFormulario();



              },

              registroDetalleFormulario : function (tipo) {

                this.variables.items.items[1].on('select',function(c,r,i) {

                  /*Aqui condicional para recuperar el tipo de cambio de la  moneda selccionada*/
                  //Si la moneda de la venta es bs y el concepto en dolar
                  if (this.Cmp.id_moneda_venta.getValue() != 2 && r.data.id_moneda == 2) {
                    Ext.Ajax.request({
                        url:'../../sis_ventas_facturacion/control/FacturacionExportacion/getTipoCambioConcepto',
                        params:{fecha_cambio:this.fecha_actual,
                                id_moneda: r.data.id_moneda},
                        success: function(resp){
                            var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                            if (reg.ROOT.datos.oficial_concepto != '') {

                              var tipo_cambio_concepto = parseFloat(reg.ROOT.datos.oficial_concepto);
                              var precio_concepto = parseFloat(r.data.precio??"0");
                              var precio_convertido = parseFloat(precio_concepto*tipo_cambio_concepto);
                              var cantidad_concepto =  this.variables.items.items[3].getValue();

                               this.variables.items.items[4].setValue(parseFloat(precio_convertido));
                               this.variables.items.items[5].setValue(parseFloat(cantidad_concepto*precio_convertido));

                            } else {
                              Ext.Msg.show({
                               title:'<h1 style="color:red"><center>Tipo de Cambio no registrado</center></h1>',
                               maxWidth : 500,
                               width: 500,
                               msg: 'No se encontró el tipo de cambio para la Moneda: '+r.data.desc_moneda+' en Fecha: '+this.fecha_actual+' Favor contactarse con Contabilidad para el registro correspondiente.',
                               buttons: Ext.Msg.OK,
                               icon: Ext.MessageBox.QUESTION,
                               scope:this
                            });
                            }
                        },
                        failure: this.conexionFailure,
                        timeout:this.timeout,
                        scope:this
                    });
                  } //Si la moneda es en dolar y el concepto en bs
                  else if (this.Cmp.id_moneda_venta.getValue() == 2 && r.data.id_moneda != 2) {
                    var tipo_cambio = parseFloat(this.Cmp.tipo_cambio.getValue());
                    var precio_concepto = parseFloat(r.data.precio??"0");
                    var precio_convertido = parseFloat(precio_concepto/tipo_cambio);
                    var cantidad_concepto =  this.variables.items.items[3].getValue();

                     this.variables.items.items[4].setValue(parseFloat(precio_convertido));
                     this.variables.items.items[5].setValue(parseFloat(cantidad_concepto*precio_convertido));
                  } else if (this.Cmp.id_moneda_venta.getValue() == r.data.id_moneda) {
                    var precio_concepto = parseFloat(r.data.precio??"0");
                    var cantidad_concepto =  this.variables.items.items[3].getValue();
                     this.variables.items.items[4].setValue(parseFloat(precio_concepto));
                     this.variables.items.items[5].setValue(parseFloat(cantidad_concepto*precio_concepto));
                  }
                  /*****************************************************************************/
                  this.contabilizable = r.data.contabilizable??"no";

                },this);

                this.variables.items.items[3].on('keyup',function(c,r,i) {
                  this.variables.items.items[5].setValue(this.variables.items.items[3].getValue()*this.variables.items.items[4].getValue());
                },this);

                this.variables.items.items[4].on('keyup',function(c,r,i) {
                  this.variables.items.items[5].setValue(this.variables.items.items[3].getValue()*this.variables.items.items[4].getValue());
                },this);


              },


              insertarNuevo : function (win) {
                // if (this.variables.items.items[0].getValue() == '' || this.variables.items.items[1].getValue() == '' || this.variables.items.items[1].lastSelectionText == ''
                //   || this.variables.items.items[3].getValue() == '' || this.variables.items.items[4].getValue() == '' || this.variables.items.items[5].getValue() == '') {
                //     Ext.Msg.show({
            		// 	   title:'Información',
                //      maxWidth : 550,
                //      width: 550,
            		// 	   msg: 'Complete los campos para guardar el detalle!',
            		// 	   buttons: Ext.Msg.OK,
                //      icon: Ext.MessageBox.QUESTION,
            		// 	   scope:this
            		// 	});
                // } else {
                var grillaRecord =  Ext.data.Record.create([
                  {name:'id_venta_detalle', type: 'numeric'},
                    {name:'id_venta', type: 'numeric'},
                    {name:'nombre_producto', type: 'string'},
                    {name:'id_producto', type: 'numeric'},
                    {name:'tipo', type: 'string'},
                    {name:'descripcion', type: 'string'},
                    {name:'requiere_descripcion', type: 'string'},
                    {name:'estado_reg', type: 'string'},
                    {name:'cantidad', type: 'numeric'},
                    {name:'precio_unitario', type: 'numeric'},
                    {name:'precio_total', type: 'numeric'},
                    {name:'id_usuario_ai', type: 'numeric'},
                    {name:'usuario_ai', type: 'string'},
                    {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                    {name:'id_usuario_reg', type: 'numeric'},
                    {name:'id_usuario_mod', type: 'numeric'},
                    {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                    {name:'usr_reg', type: 'string'},
                    {name:'usr_mod', type: 'string'}
              ]);
              var myNewRecord = new grillaRecord({
                tipo: this.variables.items.items[0].getValue(),
                id_producto: this.variables.items.items[1].getValue(),
                nombre_producto: this.variables.items.items[1].lastSelectionText,
                descripcion: this.variables.items.items[2].getValue(),
                cantidad: this.variables.items.items[3].getValue(),
                precio_unitario: this.variables.items.items[4].getValue(),
                precio_total:this.variables.items.items[5].getValue() ,
                requiere_excento:this.requiere_excento,        //
                requiere_comision:this.requiere_comision,        //
                id_venta:this.Cmp.id_venta.getValue()  ,      //
                contabilizable:this.contabilizable??"no",     //
                asociar_boletos:this.asociar_boleto??"no"        //
                });
                this.mestore.add(myNewRecord);
                //this.obtenersuma();
                this.guardarDetalles();
                win.hide();
              //}

              },


              guardarDetalles : function(){

                for (var i = 0; i < this.megrid.store.data.items.length; i++) {
                  this.megrid.store.data.items[i].data.precio_total=(this.megrid.store.data.items[i].data.precio_unitario * this.megrid.store.data.items[i].data.cantidad);
                }

                this.mestore.commitChanges();
                this.megrid.getView().refresh();
                // if(!flag){
                  this.obtenersuma();
                // }
              },

              obtenersuma: function () {

                var total_datos = this.megrid.store.data.items.length;
                var suma_local = 0;
                var suma_extranjera = 0;
                for (var i = 0; i < total_datos; i++) {
                    suma_local = suma_local + parseFloat(this.megrid.store.data.items[i].data.precio_total);
                    //suma_extranjera = suma_extranjera + parseFloat(this.megrid.store.data.items[i].data.monto_total_extranjero);
                }
                this.suma_total_local = suma_local;

                this.suma_total_local = this.suma_total_local.toFixed(2);

                /*Aqui mandamos el monto al campo monto transaccion de la forma de pago*/
                this.Cmp.monto_forma_pago.setValue(this.suma_total_local);
                /***********************************************************************/

                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[3].childNodes[0].style.color="#000000";
                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[3].childNodes[0].style.fontWeight="bold";
                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[3].childNodes[0].style.fontSize="22px";

                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[4].childNodes[0].style.color="#000000";
                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[4].childNodes[0].style.fontWeight="bold";
                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[4].childNodes[0].style.fontSize="22px";

                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[5].childNodes[0].style.color="green";
                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[5].childNodes[0].style.background="#F0FF00";
                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[5].childNodes[0].style.fontWeight="bold";
                this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[5].childNodes[0].style.fontSize="22px";


              },

    onNew: function(){
        this.accionFormulario = 'NEW';
        },


    funcionesNuevo: function() {

      this.arrayBotones[0].scope.form.buttons[0].setDisabled(true);
      /*Aqui recuperar el tipo de cambio para el dolar en la forma de pago*/
      Ext.Ajax.request({
          url:'../../sis_ventas_facturacion/control/FacturacionExportacion/getTipoCambioConcepto',
          params:{fecha_cambio:this.fecha_actual,
                  id_moneda: 2
                 },
          success: function(resp){
              var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
              if (reg.ROOT.datos.oficial_concepto != '') {
                this.tipo_cambio_general = parseFloat(reg.ROOT.datos.oficial_concepto);
              } else {
                Ext.Msg.show({
                 title:'<h1 style="color:red"><center>Tipo de Cambio no registrado</center></h1>',
                 maxWidth : 500,
                 width: 500,
                 msg: 'No se encontró el tipo de cambio para la Moneda: '+this.desc_moneda_recu[i]+' en Fecha: '+this.fecha_actual+' Favor contactarse con Contabilidad para el registro correspondiente.',
                 buttons: Ext.Msg.OK,
                 icon: Ext.MessageBox.QUESTION,
                 scope:this
              });
              }
          },
          failure: this.conexionFailure,
          timeout:this.timeout,
          scope:this
      });
      /*********************************************************************************************/

      /*Aqui Cuando se seleccione el Nit recuperar los datos del cliente*/
      this.Cmp.nit.on('blur',function(c) {
        if (this.Cmp.nit.getValue() != '') {
          this.Cmp.nombre_factura.reset();
          this.Cmp.id_cliente.reset();
          Ext.Ajax.request({
              url : '../../sis_ventas_facturacion/control/FacturacionExportacion/RecuperarCliente',
              params : {
                'nit' : this.Cmp.nit.getValue(),
                'razon_social' : this.Cmp.nombre_factura.getValue(),
              },
              success: function(resp){
                  var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                  this.Cmp.nombre_factura.setValue(reg.ROOT.datos.razon);
                  this.Cmp.id_cliente.setValue(reg.ROOT.datos.id_cliente);
                  this.Cmp.direccion_cliente.setValue(reg.ROOT.datos.direccion);
              },
              failure : this.conexionFailure,
              timeout : this.timeout,
              scope : this
            });
        }

      },this);
      /*******************************************************************************/

      /*Para insertar la formula*/
      this.Cmp.id_formula.on('select',function(c,r,i) {

              var formu = r.data.id_formula;
              if (formu != 0) {
                this.eliminarAnteriores();
              }

      },this);
      /*************************/

      /*El combo de la moneda de la venta para recueprar el tipo de cambio selccionado*/
      this.Cmp.id_moneda_venta.on('select',function(c,r,i) {
        Ext.Ajax.request({
            url:'../../sis_ventas_facturacion/control/FacturacionExportacion/getTipoCambio',
            params:{fecha_cambio:this.fecha_actual,
                    id_moneda_pais: r.data.id_moneda_pais},
            success: function(resp){
                var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                if (reg.ROOT.datos.oficial != '') {
                  this.Cmp.tipo_cambio.setValue(parseFloat(reg.ROOT.datos.oficial));
                } else {
                  Ext.Msg.show({
                   title:'<h1 style="color:red"><center>Tipo de Cambio no registrado</center></h1>',
                   maxWidth : 500,
                   width: 500,
                   msg: 'No se encontró el tipo de cambio para la Moneda: '+r.data.desc_moneda+' en Fecha: '+this.fecha_actual+' Favor contactarse con Contabilidad para el registro correspondiente.',
                   buttons: Ext.Msg.OK,
                   icon: Ext.MessageBox.QUESTION,
                   // fn: function () {
                   //    this.panel.close();
                   // },
                   scope:this
                });
                }
            },
            failure: this.conexionFailure,
            timeout:this.timeout,
            scope:this
        });

        /*Aqui seleccionaremos la moneda de la forma de pago en base a la moneda que se seleccione la cebezera*/
        this.Cmp.id_moneda.store.baseParams.id_moneda = this.Cmp.id_moneda_venta.getValue();
        this.Cmp.id_moneda.store.load({params:{start:0,limit:50},
               callback : function (r) {
                    if (r.length == 1 ) {
                          this.Cmp.id_moneda.setValue(r[0].data.id_moneda);
                          this.Cmp.id_moneda.fireEvent('select', this.Cmp.id_moneda,r[0],0);
                          this.Cmp.id_moneda.store.baseParams.id_moneda = '';
                      }
                }, scope : this
            });




      },this);
      /*******************************************************************************/
      /*Aqui seleccionaremos la moneda de la forma de pago en base a la moneda que se seleccione la cebezera*/
      this.Cmp.id_moneda.on('select',function(c,r,i) {
        if (this.Cmp.id_moneda_venta.getValue() != 2 && this.Cmp.id_moneda.getValue() == 2) {
              this.Cmp.monto_forma_pago.setValue(this.suma_total_local/this.tipo_cambio_general);
        }else if (this.Cmp.id_moneda_venta.getValue() == 2 && this.Cmp.id_moneda.getValue() != 2) {
              this.Cmp.monto_forma_pago.setValue(this.suma_total_local*this.tipo_cambio_general);
        } else if (this.Cmp.id_moneda_venta.getValue() == this.Cmp.id_moneda.getValue()) {
              this.Cmp.monto_forma_pago.setValue(this.suma_total_local);
        }

        /*Control para mostrar o esconder la segunda forma de pago*/
        var total_venta = parseFloat(this.suma_total_local);
        /*Aqui control para el monto*/
        if (this.Cmp.id_moneda_venta.getValue() != 2 && this.Cmp.id_moneda.getValue() == 2) {
              var total_control = parseFloat(this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio_general);
        }else if (this.Cmp.id_moneda_venta.getValue() == 2 && this.Cmp.id_moneda.getValue() != 2) {
              var total_control = parseFloat(this.Cmp.monto_forma_pago.getValue()/this.tipo_cambio_general);
        } else if (this.Cmp.id_moneda_venta.getValue() == this.Cmp.id_moneda.getValue()) {
              var total_control = parseFloat(this.Cmp.monto_forma_pago.getValue());
        }

        if (this.Cmp.monto_forma_pago.getValue() != '') {
          if (total_control >= total_venta) {
            Ext.getCmp('segunda_forma_pago').hide();
            this.Cmp.id_moneda_2.reset();
            this.Cmp.id_medio_pago_2.reset();
            this.Cmp.id_auxiliar_2.reset();
            this.Cmp.numero_tarjeta_2.reset();
            this.Cmp.mco_2.reset();
            this.Cmp.codigo_tarjeta_2.reset();
            this.Cmp.monto_forma_pago_2.reset();

            this.Cmp.id_moneda_2.allowBlank = true;
            this.Cmp.id_medio_pago_2.allowBlank = true;

              /***************************/
          } else {

            Ext.getCmp('segunda_forma_pago').show();
            this.Cmp.id_moneda_2.allowBlank = false;
            this.Cmp.id_medio_pago_2.allowBlank = false;
          }
          /**********************************************************/
        }







      },this);

    /**********************************************************************/

      /*Aqui para Calcular los totales del FOB y del CIF*/
          this.Cmp.valor_bruto.on('keyup', function(field,newValue,oldValue){
            var valorBruto = this.Cmp.valor_bruto.getValue()??"0";
            var transporteFob = this.Cmp.transporte_fob.getValue()??"0";
            var segurosFob = this.Cmp.seguros_fob.getValue()??"0";
            var otrosFob = this.Cmp.otros_fob.getValue()??"0";

            var transporteCIF = this.Cmp.transporte_cif.getValue()??"0";
            var segurosCIF = this.Cmp.seguros_cif.getValue()??"0";
            var otrosCIF = this.Cmp.otros_cif.getValue()??"0";

            this.Cmp.total_fob.setValue(valorBruto+transporteFob+segurosFob+otrosFob);
            this.Cmp.total_cif.setValue((this.Cmp.total_fob.getValue()??"0")+transporteCIF+segurosCIF+otrosCIF);

            var total_venta = parseFloat(this.Cmp.total_cif.getValue()??"0");
            var suma_venta = parseFloat(this.suma_total_local??"0");

            if (total_venta != suma_venta) {
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(true);
            } else {
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(false);
            }

          },this)

          this.Cmp.transporte_fob.on('keyup', function(field,newValue,oldValue){
            var valorBruto = this.Cmp.valor_bruto.getValue()??"0";
            var transporteFob = this.Cmp.transporte_fob.getValue()??"0";
            var segurosFob = this.Cmp.seguros_fob.getValue()??"0";
            var otrosFob = this.Cmp.otros_fob.getValue()??"0";

            var transporteCIF = this.Cmp.transporte_cif.getValue()??"0";
            var segurosCIF = this.Cmp.seguros_cif.getValue()??"0";
            var otrosCIF = this.Cmp.otros_cif.getValue()??"0";

            this.Cmp.total_fob.setValue(valorBruto+transporteFob+segurosFob+otrosFob);
            this.Cmp.total_cif.setValue((this.Cmp.total_fob.getValue()??"0")+transporteCIF+segurosCIF+otrosCIF);

            var total_venta = parseFloat(this.Cmp.total_cif.getValue()??"0");
            var suma_venta = parseFloat(this.suma_total_local??"0");

            if (total_venta != suma_venta) {
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(true);
            } else {
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(false);
            }

          },this)

          this.Cmp.seguros_fob.on('keyup', function(field,newValue,oldValue){
            var valorBruto = this.Cmp.valor_bruto.getValue()??"0";
            var transporteFob = this.Cmp.transporte_fob.getValue()??"0";
            var segurosFob = this.Cmp.seguros_fob.getValue()??"0";
            var otrosFob = this.Cmp.otros_fob.getValue()??"0";

            var transporteCIF = this.Cmp.transporte_cif.getValue()??"0";
            var segurosCIF = this.Cmp.seguros_cif.getValue()??"0";
            var otrosCIF = this.Cmp.otros_cif.getValue()??"0";

            this.Cmp.total_fob.setValue(valorBruto+transporteFob+segurosFob+otrosFob);
            this.Cmp.total_cif.setValue((this.Cmp.total_fob.getValue()??"0")+transporteCIF+segurosCIF+otrosCIF);

            var total_venta = parseFloat(this.Cmp.total_cif.getValue()??"0");
            var suma_venta = parseFloat(this.suma_total_local??"0");

            if (total_venta != suma_venta) {
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(true);
            } else {
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(false);
            }
          },this)

          this.Cmp.otros_fob.on('keyup', function(field,newValue,oldValue){
            var valorBruto = this.Cmp.valor_bruto.getValue()??"0";
            var transporteFob = this.Cmp.transporte_fob.getValue()??"0";
            var segurosFob = this.Cmp.seguros_fob.getValue()??"0";
            var otrosFob = this.Cmp.otros_fob.getValue()??"0";

            var transporteCIF = this.Cmp.transporte_cif.getValue()??"0";
            var segurosCIF = this.Cmp.seguros_cif.getValue()??"0";
            var otrosCIF = this.Cmp.otros_cif.getValue()??"0";

            this.Cmp.total_fob.setValue(valorBruto+transporteFob+segurosFob+otrosFob);
            this.Cmp.total_cif.setValue((this.Cmp.total_fob.getValue()??"0")+transporteCIF+segurosCIF+otrosCIF);

            var total_venta = parseFloat(this.Cmp.total_cif.getValue()??"0");
            var suma_venta = parseFloat(this.suma_total_local??"0");

            if (total_venta != suma_venta) {
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(true);
            } else {
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(false);
            }
          },this)

          /*Calculamos los montos CIF*/
          this.Cmp.transporte_cif.on('keyup', function(field,newValue,oldValue){
            var transporteCIF = this.Cmp.transporte_cif.getValue()??"0";
            var segurosCIF = this.Cmp.seguros_cif.getValue()??"0";
            var otrosCIF = this.Cmp.otros_cif.getValue()??"0";
            this.Cmp.total_cif.setValue((this.Cmp.total_fob.getValue()??"0")+transporteCIF+segurosCIF+otrosCIF);

            var total_venta = parseFloat(this.Cmp.total_cif.getValue()??"0");
            var suma_venta = parseFloat(this.suma_total_local??"0");

            if (total_venta != suma_venta) {
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(true);
            } else {
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(false);
            }
          },this)

          this.Cmp.seguros_cif.on('keyup', function(field,newValue,oldValue){
            var transporteCIF = this.Cmp.transporte_cif.getValue()??"0";
            var segurosCIF = this.Cmp.seguros_cif.getValue()??"0";
            var otrosCIF = this.Cmp.otros_cif.getValue()??"0";
            this.Cmp.total_cif.setValue((this.Cmp.total_fob.getValue()??"0")+transporteCIF+segurosCIF+otrosCIF);

            var total_venta = parseFloat(this.Cmp.total_cif.getValue()??"0");
            var suma_venta = parseFloat(this.suma_total_local??"0");

            if (total_venta != suma_venta) {
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(true);
            } else {
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(false);
            }
          },this)

          this.Cmp.otros_cif.on('keyup', function(field,newValue,oldValue){
            var transporteCIF = this.Cmp.transporte_cif.getValue()??"0";
            var segurosCIF = this.Cmp.seguros_cif.getValue()??"0";
            var otrosCIF = this.Cmp.otros_cif.getValue()??"0";
            this.Cmp.total_cif.setValue((this.Cmp.total_fob.getValue()??"0")+transporteCIF+segurosCIF+otrosCIF);

            var total_venta = parseFloat(this.Cmp.total_cif.getValue()??"0");
            var suma_venta = parseFloat(this.suma_total_local??"0");

            if (total_venta != suma_venta) {
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(true);
            } else {
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(false);
            }
          },this)
      /***************************************************/


      /***********************/
      /*Medio de pago por defecto*/
      this.Cmp.id_medio_pago.store.load({params:{start:0,limit:50},
          callback : function (r) {
                     if (r.length == 1 ) {
                         this.Cmp.id_medio_pago.setValue(r[0].data.id_medio_pago_pw);
                         this.Cmp.id_medio_pago.fireEvent('select', this.Cmp.id_medio_pago_pw,r[0],0);
                     } else {
                       for (var i = 0; i < r.length; i++) {
                         if (r[i].data.fop_code.startsWith("CA")) {
                           this.Cmp.id_medio_pago.setValue(r[i].data.id_medio_pago_pw);
                           this.Cmp.id_medio_pago.fireEvent('select', this.Cmp.id_medio_pago_pw,r[i]);
                         }
                       }
                     }
           }, scope : this
          });
      /***************************/

      /***********************************************************/
      /*Aqui para los campos de las formas de pago*/
      this.Cmp.id_medio_pago.on('select',function(c,r,i) {

        if(r){
          if (r.data) {
            var codigo_forma_pago = r.data.fop_code;
            //this.Cmp.tipo_tarjeta.setValue(r.data.name);
          }
        }

        if (codigo_forma_pago != undefined && codigo_forma_pago != '' && codigo_forma_pago != null) {

        if (codigo_forma_pago.startsWith("CC")) {
          this.mostrarComponente(this.Cmp.codigo_tarjeta);
          this.mostrarComponente(this.Cmp.numero_tarjeta);
          this.ocultarComponente(this.Cmp.id_auxiliar);
          this.ocultarComponente(this.Cmp.mco);
        //this.Cmp.tipo_tarjeta.setValue(r.data.nombre);
          this.Cmp.codigo_tarjeta.allowBlank = false;
        //  this.Cmp.tipo_tarjeta.allowBlank = false;
          this.Cmp.mco.allowBlank = true;
        } else if (codigo_forma_pago.startsWith("MCO")) {
          this.mostrarComponente(this.Cmp.mco);
          this.Cmp.numero_tarjeta.allowBlank = true;
          this.Cmp.codigo_tarjeta.allowBlank = true;
          //this.Cmp.tipo_tarjeta.allowBlank = true;
          this.Cmp.mco.allowBlank = false;
          this.ocultarComponente(this.Cmp.codigo_tarjeta);
          //this.ocultarComponente(this.Cmp.tipo_tarjeta);
          this.ocultarComponente(this.Cmp.id_auxiliar);
          this.ocultarComponente(this.Cmp.numero_tarjeta);
          this.Cmp.codigo_tarjeta.reset();
          //this.Cmp.tipo_tarjeta.reset();
          this.Cmp.id_auxiliar.reset();
          this.Cmp.numero_tarjeta.reset();
        } else if (codigo_forma_pago.startsWith("CU") || codigo_forma_pago.startsWith("CT")) {
          this.mostrarComponente(this.Cmp.id_auxiliar);
          this.Cmp.numero_tarjeta.allowBlank = true;
          this.Cmp.codigo_tarjeta.allowBlank = true;
          //this.Cmp.tipo_tarjeta.allowBlank = true;
          this.Cmp.mco.allowBlank = true;
          this.Cmp.id_auxiliar.allowBlank = false;
          this.ocultarComponente(this.Cmp.codigo_tarjeta);
          //this.ocultarComponente(this.Cmp.tipo_tarjeta);
          this.ocultarComponente(this.Cmp.numero_tarjeta);
          this.ocultarComponente(this.Cmp.mco);
          this.Cmp.codigo_tarjeta.reset();
          //this.Cmp.tipo_tarjeta.reset();
          this.Cmp.id_auxiliar.reset();
          this.Cmp.mco.reset();
          this.Cmp.numero_tarjeta.reset();
        }else if (codigo_forma_pago.startsWith("CA")) {
          this.mostrarComponente(this.Cmp.id_auxiliar);
          this.Cmp.numero_tarjeta.allowBlank = true;
          this.Cmp.codigo_tarjeta.allowBlank = true;
          //this.Cmp.tipo_tarjeta.allowBlank = true;
          this.Cmp.id_auxiliar.allowBlank = true;
          this.Cmp.mco.allowBlank = true;
          this.ocultarComponente(this.Cmp.codigo_tarjeta);
          //this.ocultarComponente(this.Cmp.tipo_tarjeta);
          this.ocultarComponente(this.Cmp.numero_tarjeta);
          this.ocultarComponente(this.Cmp.id_auxiliar);
          this.ocultarComponente(this.Cmp.mco);
          this.Cmp.codigo_tarjeta.reset();
          //this.Cmp.tipo_tarjeta.reset();
          this.Cmp.id_auxiliar.reset();
          this.Cmp.mco.reset();
          this.Cmp.numero_tarjeta.reset();
        } else {
          this.Cmp.codigo_tarjeta.reset();
          //this.Cmp.tipo_tarjeta.reset();
          this.Cmp.id_auxiliar.reset();
          this.Cmp.mco.reset();
          this.Cmp.numero_tarjeta.reset();
        }
      }

      },this);





      Ext.getCmp('segunda_forma_pago').hide();
      this.Cmp.id_moneda_2.reset();
      this.Cmp.id_medio_pago_2.reset();
      this.Cmp.id_auxiliar_2.reset();
      this.Cmp.numero_tarjeta_2.reset();
      this.Cmp.mco_2.reset();
      this.Cmp.codigo_tarjeta_2.reset();
      this.Cmp.monto_forma_pago_2.reset();

      this.Cmp.id_moneda_2.allowBlank = true;
      this.Cmp.id_medio_pago_2.allowBlank = true;


      /*Aqui para el control del monto ingresado con el total de la venta*/
      this.Cmp.monto_forma_pago.on('change', function(){
        var total_venta = parseFloat(this.Cmp.total_cif.getValue()??"0");
        var suma_venta = parseFloat(this.suma_total_local??"0");

        /*Aqui control para verificar que los montos sean iguales*/
        if (suma_venta != total_venta) {

          Ext.Msg.show({
           title:'Información',
           maxWidth : 550,
           width: 550,
           msg: 'Los totales no Igualan, Favor verifique los totales del detalle es igual a: '+suma_venta+' y el total general es igual a: '+total_venta,
           buttons: Ext.Msg.OK,
           icon: Ext.MessageBox.QUESTION,
           scope:this
        });



      } else {


        var total_venta = parseFloat(this.suma_total_local);
        /*Aqui control para el monto*/
        if (this.Cmp.id_moneda_venta.getValue() != 2 && this.Cmp.id_moneda.getValue() == 2) {
              var total_control = parseFloat(this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio_general);
        }else if (this.Cmp.id_moneda_venta.getValue() == 2 && this.Cmp.id_moneda.getValue() != 2) {
              var total_control = parseFloat(this.Cmp.monto_forma_pago.getValue()/this.tipo_cambio_general);
        } else if (this.Cmp.id_moneda_venta.getValue() == this.Cmp.id_moneda.getValue()) {
              var total_control = parseFloat(this.Cmp.monto_forma_pago.getValue());
        }

        if (total_control >= total_venta) {
          Ext.getCmp('segunda_forma_pago').hide();
          this.Cmp.id_moneda_2.reset();
          this.Cmp.id_medio_pago_2.reset();
          this.Cmp.id_auxiliar_2.reset();
          this.Cmp.numero_tarjeta_2.reset();
          this.Cmp.mco_2.reset();
          this.Cmp.codigo_tarjeta_2.reset();
          this.Cmp.monto_forma_pago_2.reset();

          this.Cmp.id_moneda_2.allowBlank = true;
          this.Cmp.id_medio_pago_2.allowBlank = true;

            /***************************/
        } else {

          Ext.getCmp('segunda_forma_pago').show();
          /*Aqui seleccionamos la moneda por defecto de la segunda forma de pago*/
          /*Moneda Base por defecto*/
          this.Cmp.id_moneda_2.store.baseParams.id_moneda = this.Cmp.id_moneda_venta.getValue();
          this.Cmp.id_moneda_2.store.load({params:{start:0,limit:50},
                 callback : function (r) {
                      if (r.length == 1 ) {
                            this.Cmp.id_moneda_2.setValue(r[0].data.id_moneda);
                            this.Cmp.id_moneda_2.fireEvent('select', this.Cmp.id_moneda,r[0],0);
                            this.Cmp.id_moneda_2.store.baseParams.id_moneda = '';
                        }
                  }, scope : this
              });
          /***********************/

          this.Cmp.id_moneda_2.on('select',function(c,r,i) {

            if (this.Cmp.id_moneda_venta.getValue() != 2) {
              if (this.Cmp.id_moneda.getValue() != 2 && this.Cmp.id_moneda_2.getValue() == 2) {
                    this.Cmp.monto_forma_pago_2.setValue((this.suma_total_local-this.Cmp.monto_forma_pago.getValue())/this.tipo_cambio_general);
              }else if (this.Cmp.id_moneda.getValue() == 2 && this.Cmp.id_moneda_2.getValue() != 2) {
                    this.Cmp.monto_forma_pago_2.setValue(this.suma_total_local-(this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio_general));
              } else if (this.Cmp.id_moneda.getValue() != 2 && this.Cmp.id_moneda_2.getValue() != 2) {
                    this.Cmp.monto_forma_pago_2.setValue(this.suma_total_local-this.Cmp.monto_forma_pago.getValue());
              } else if (this.Cmp.id_moneda.getValue() == 2 && this.Cmp.id_moneda_2.getValue() == 2) {
                    this.Cmp.monto_forma_pago_2.setValue((this.suma_total_local/this.tipo_cambio_general)-this.Cmp.monto_forma_pago.getValue());
              }

            } else {
              if (this.Cmp.id_moneda.getValue() != 2 && this.Cmp.id_moneda_2.getValue() == 2) {
                    this.Cmp.monto_forma_pago_2.setValue(this.suma_total_local-(this.Cmp.monto_forma_pago.getValue()/this.tipo_cambio_general));
              }else if (this.Cmp.id_moneda.getValue() == 2 && this.Cmp.id_moneda_2.getValue() != 2) {
                    this.Cmp.monto_forma_pago_2.setValue((this.suma_total_local-this.Cmp.monto_forma_pago.getValue())*this.tipo_cambio_general);
              } else if (this.Cmp.id_moneda.getValue() != 2 && this.Cmp.id_moneda_2.getValue() != 2) {
                    this.Cmp.monto_forma_pago_2.setValue((this.suma_total_local*this.tipo_cambio_general)-this.Cmp.monto_forma_pago.getValue());
              } else if (this.Cmp.id_moneda.getValue() == 2 && this.Cmp.id_moneda_2.getValue() == 2) {
                    this.Cmp.monto_forma_pago_2.setValue(this.suma_total_local-this.Cmp.monto_forma_pago.getValue());
              }
            }




          },this);



          /*Medio de pago por defecto*/
          this.Cmp.id_medio_pago_2.store.load({params:{start:0,limit:50},
              callback : function (r) {
                         if (r.length == 1 ) {
                             this.Cmp.id_medio_pago_2.setValue(r[0].data.id_medio_pago_pw);
                             this.Cmp.id_medio_pago_2.fireEvent('select', this.Cmp.id_medio_pago_pw,r[0],0);
                         } else {
                           for (var i = 0; i < r.length; i++) {
                             if (r[i].data.fop_code.startsWith("CA")) {
                               this.Cmp.id_medio_pago_2.setValue(r[i].data.id_medio_pago_pw);
                               this.Cmp.id_medio_pago_2.fireEvent('select', this.Cmp.id_medio_pago_pw,r[i]);
                             }
                           }
                         }
               }, scope : this
              });



        }

      }
        /*********************************************************/



      },this)

      /*Aqui control para la segunda forma de pago*/
      this.Cmp.id_medio_pago_2.on('select',function(c,r,i) {

        if(r){
          if (r.data) {
            var codigo_forma_pago = r.data.fop_code;
            //this.Cmp.tipo_tarjeta_2.setValue(r.data.name);
          }
        }

        if (codigo_forma_pago != undefined && codigo_forma_pago != '' && codigo_forma_pago != null){
          if (codigo_forma_pago.startsWith("CC")) {
            this.mostrarComponente(this.Cmp.codigo_tarjeta_2);
            //this.mostrarComponente(this.Cmp.tipo_tarjeta_2);
            this.mostrarComponente(this.Cmp.numero_tarjeta_2);
            this.ocultarComponente(this.Cmp.id_auxiliar_2);
            this.ocultarComponente(this.Cmp.mco_2);
            //this.Cmp.tipo_tarjeta_2.setValue(r.data.nombre);
            this.Cmp.codigo_tarjeta_2.allowBlank = false;
            //this.Cmp.tipo_tarjeta_2.allowBlank = false;
            this.Cmp.mco_2.allowBlank = true;
          } else if (codigo_forma_pago.startsWith("MCO")) {
            this.mostrarComponente(this.Cmp.mco_2);
            this.Cmp.numero_tarjeta_2.allowBlank = true;
            this.Cmp.codigo_tarjeta_2.allowBlank = true;
            //this.Cmp.tipo_tarjeta_2.allowBlank = true;
            this.Cmp.mco_2.allowBlank = false;
            this.ocultarComponente(this.Cmp.codigo_tarjeta_2);
            //this.ocultarComponente(this.Cmp.tipo_tarjeta_2);
            this.ocultarComponente(this.Cmp.id_auxiliar_2);
            this.ocultarComponente(this.Cmp.numero_tarjeta_2);
            this.Cmp.codigo_tarjeta_2.reset();
            //this.Cmp.tipo_tarjeta_2.reset();
            this.Cmp.id_auxiliar_2.reset();
            this.Cmp.numero_tarjeta_2.reset();
          } else if (codigo_forma_pago.startsWith("CU") || codigo_forma_pago.startsWith("CT")) {
            this.mostrarComponente(this.Cmp.id_auxiliar_2);
            this.Cmp.numero_tarjeta_2.allowBlank = true;
            this.Cmp.codigo_tarjeta_2.allowBlank = true;
          //  this.Cmp.tipo_tarjeta_2.allowBlank = true;
            this.Cmp.mco_2.allowBlank = true;
            this.Cmp.id_auxiliar_2.allowBlank = false;
            this.ocultarComponente(this.Cmp.codigo_tarjeta_2);
            //this.ocultarComponente(this.Cmp.tipo_tarjeta_2);
            this.ocultarComponente(this.Cmp.numero_tarjeta_2);
            this.ocultarComponente(this.Cmp.mco_2);
            this.Cmp.codigo_tarjeta_2.reset();
            //this.Cmp.tipo_tarjeta_2.reset();
            this.Cmp.id_auxiliar_2.reset();
            this.Cmp.mco_2.reset();
            this.Cmp.numero_tarjeta_2.reset();
          }else if (codigo_forma_pago.startsWith("CA")) {
            this.mostrarComponente(this.Cmp.id_auxiliar_2);
            this.Cmp.numero_tarjeta_2.allowBlank = true;
            this.Cmp.codigo_tarjeta_2.allowBlank = true;
            //this.Cmp.tipo_tarjeta_2.allowBlank = true;
            this.Cmp.id_auxiliar_2.allowBlank = true;
            this.Cmp.mco_2.allowBlank = true;
            this.ocultarComponente(this.Cmp.codigo_tarjeta_2);
            //this.ocultarComponente(this.Cmp.tipo_tarjeta_2);
            this.ocultarComponente(this.Cmp.numero_tarjeta_2);
            this.ocultarComponente(this.Cmp.id_auxiliar_2);
            this.ocultarComponente(this.Cmp.mco_2);
            this.Cmp.codigo_tarjeta_2.reset();
            //this.Cmp.tipo_tarjeta_2.reset();
            this.Cmp.id_auxiliar_2.reset();
            this.Cmp.mco_2.reset();
            this.Cmp.numero_tarjeta_2.reset();
          } else {
            this.Cmp.codigo_tarjeta_2.reset();
            //this.Cmp.tipo_tarjeta_2.reset();
            this.Cmp.id_auxiliar_2.reset();
            this.Cmp.mco_2.reset();
            this.Cmp.numero_tarjeta_2.reset();
          }
        }




      },this);
      /********************************************/


      /********************************************************************/

      /********************************************/
    },

    eliminarAnteriores : function () {
      for (var i = this.mestore.data.length; i >= 0; i--) {
              var suma_eli = 0;
              suma_eli = suma_eli + i;
              var dato = 0;
              dato = suma_eli - 1;
              if(dato == (-1) ){
                dato = 0;
              }
              if (suma_eli == 0 ) {
                  this.successRecuperarDatos();
              } else if (suma_eli >= 0 )  {
                 this.mestore.remove(this.mestore.getAt(dato));
               }
        }
      },

      successRecuperarDatos : function () {
          Ext.Ajax.request({
              url:'../../sis_ventas_facturacion/control/FacturacionExportacion/insertarFormula',
              params:{id_formula:this.Cmp.id_formula.getValue(),
                      id_moneda_venta:this.Cmp.id_moneda_venta.getValue()},
              success: function(resp){
                  var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                  this.nombre_producto = reg.ROOT.datos.v_nombre_producto;
                  this.id_producto_recu = reg.ROOT.datos.v_id_producto;
                  this.id_formula_recu = reg.ROOT.datos.v_id_formula;
                  this.precio_inde = reg.ROOT.datos.v_precio;
                  this.id_moneda_paque = reg.ROOT.datos.v_id_moneda_paquetes;

                  this.producto_nombre = this.nombre_producto.split(",");
                  this.producto_id = this.id_producto_recu.split(",");
                  this.precio_form = this.precio_inde.split(",");
                  this.desc_moneda_recu = reg.ROOT.datos.v_desc_moneda;
                  this.nombre_moneda = this.desc_moneda_recu.split(",");
                  this.moneda_paque = this.id_moneda_paque.split(",");

                  var grillaRecord =  Ext.data.Record.create([
                      {name:'nombre_producto', type: 'string'},
                      {name:'id_producto', type: 'numeric'},
                      {name:'cantidad', type: 'numeric'},
                      {name:'precio_unitario', type: 'numeric'},
                      {name:'precio_total', type: 'numeric'},
                      {name:'descripcion', type: 'string'},
                ]);

                for (var i = 0; i < this.producto_nombre.length; i++) {
                  var precio_concepto = this.precio_form[i];
                  /*Aqui control para realizar la conversion de la moneda*/
                  if (this.Cmp.id_moneda_venta.getValue() != 2 && this.moneda_paque[i] == 2) {
                    var nombre_producto = this.producto_nombre[i];
                    var id_producto = this.producto_id[i];
                    Ext.Ajax.request({
                        url:'../../sis_ventas_facturacion/control/FacturacionExportacion/getTipoCambioConcepto',
                        params:{fecha_cambio:this.fecha_actual,
                                id_moneda: this.moneda_paque[i]},
                        success: function(resp){
                            var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                            if (reg.ROOT.datos.oficial_concepto != '') {
                              var tipo_cambio = parseFloat(reg.ROOT.datos.oficial_concepto);
                              precio_base = precio_concepto*tipo_cambio;

                              var myNewRecord = new grillaRecord({
                                  nombre_producto : nombre_producto,
                                  id_producto: id_producto,
                                  cantidad : '1',
                                  precio_unitario : (precio_base==undefined || precio_base==null || precio_base=='')?0:precio_base,
                                  precio_total: precio_base*1

                                });

                                this.mestore.add(myNewRecord);
                                this.guardarDetalles(true);

                            } else {
                              Ext.Msg.show({
                               title:'<h1 style="color:red"><center>Tipo de Cambio no registrado</center></h1>',
                               maxWidth : 500,
                               width: 500,
                               msg: 'No se encontró el tipo de cambio para la Moneda: '+this.desc_moneda_recu[i]+' en Fecha: '+this.fecha_actual+' Favor contactarse con Contabilidad para el registro correspondiente.',
                               buttons: Ext.Msg.OK,
                               icon: Ext.MessageBox.QUESTION,
                               scope:this
                            });
                            }
                        },
                        failure: this.conexionFailure,
                        timeout:this.timeout,
                        scope:this
                    });
                  } //Si la moneda es en dolar y el concepto en bs
                  else if (this.Cmp.id_moneda_venta.getValue() == 2 && this.moneda_paque[i] != 2) {

                    var tipo_cambio = parseFloat(this.Cmp.tipo_cambio.getValue());
                    precio_base = precio_concepto/tipo_cambio;

                    var myNewRecord = new grillaRecord({
                        nombre_producto : this.producto_nombre[i],
                        id_producto: this.producto_id[i],
                        cantidad : '1',
                        precio_unitario : (precio_base==undefined || precio_base==null || precio_base=='')?0:precio_base,
                        precio_total: precio_base*1

                      });

                      this.mestore.add(myNewRecord);
                      this.guardarDetalles(true);

                  } else if (this.Cmp.id_moneda_venta.getValue() == this.moneda_paque[i]) {
                    precio_base = precio_concepto;

                    var myNewRecord = new grillaRecord({
                        nombre_producto : this.producto_nombre[i],
                        id_producto: this.producto_id[i],
                        cantidad : '1',
                        precio_unitario : (precio_base==undefined || precio_base==null || precio_base=='')?0:precio_base,
                        precio_total: precio_base*1

                      });

                      this.mestore.add(myNewRecord);
                      this.guardarDetalles(true);
                  }
                  /*******************************************************/



              }

              },
              failure: this.conexionFailure,
              timeout:this.timeout,
              scope:this
          });

        },


    iniciarEventos : function () {

      this.Cmp.id_sucursal.store.load({params:{start:0,limit:50},
         callback : function (r) {

            this.Cmp.id_sucursal.setValue(this.data.objPadre.variables_globales.id_sucursal);
            if (this.data.objPadre.variables_globales.vef_tiene_punto_venta != 'true') {
              this.detCmp.id_producto.store.baseParams.id_sucursal = this.Cmp.id_sucursal.getValue();
              }
              this.Cmp.id_sucursal.fireEvent('select',this.Cmp.id_sucursal, this.Cmp.id_sucursal.store.getById(this.data.objPadre.variables_globales.id_sucursal));

          }, scope : this
      });

      this.Cmp.id_punto_venta.store.baseParams.id_punto_venta = this.data.objPadre.variables_globales.id_punto_venta;
	        this.Cmp.id_punto_venta.store.load({params:{start:0,limit:this.tam_pag},
	           callback : function (r) {
	                this.Cmp.id_punto_venta.setValue(this.data.objPadre.variables_globales.id_punto_venta);
	           		   this.detCmp.id_producto.store.baseParams.id_punto_venta = this.Cmp.id_punto_venta.getValue();
	                this.Cmp.id_punto_venta.fireEvent('select',this.Cmp.id_punto_venta, this.Cmp.id_punto_venta.store.getById(this.data.objPadre.variables_globales.id_punto_venta));

	            }, scope : this
	        });


      /*Recuperamos la Fecha Actual*/
      this.tipo_cambio = 0;
      var fecha = new Date();
      var dd = fecha.getDate();
      var mm = fecha.getMonth() + 1; //January is 0!
      var yyyy = fecha.getFullYear();
      this.fecha_actual = dd + '/' + mm + '/' + yyyy;

      this.Cmp.id_formula.store.baseParams.tipo_pv = 'ato';

      /*Control del tamaño de los botones del Grid*/
      this.megrid.topToolbar.items.items[0].container.dom.style.width="80px";
      this.megrid.topToolbar.items.items[0].container.dom.style.height="35px";
      this.megrid.topToolbar.items.items[0].btnEl.dom.style.height="35px";

      /*Aumentando para el hover Ismael Valdivia (13/11/2020)*/

      Ext.getCmp('botonGuardar').el.dom.onmouseover = function () {
        Ext.getCmp('botonGuardar').btnEl.dom.style.background = '#00B2FA';
      };

      Ext.getCmp('botonGuardar').el.dom.onmouseout = function () {
        Ext.getCmp('botonGuardar').btnEl.dom.style.background = '';
      };

      /*******************************************************/

      /******************Cambiaremos el estilo del boton agregar detalle************************/
      this.megrid.topToolbar.items.items[1].container.dom.style.width="80px";
      this.megrid.topToolbar.items.items[1].container.dom.style.height="35px";
      this.megrid.topToolbar.items.items[1].btnEl.dom.style.height="35px";

      /*Aumentando para el hover Ismael Valdivia (13/11/2020)*/

      Ext.getCmp('botonAgregar').el.dom.onmouseover = function () {
        Ext.getCmp('botonAgregar').btnEl.dom.style.background = '#5CE100';
      };

      Ext.getCmp('botonAgregar').el.dom.onmouseout = function () {
        Ext.getCmp('botonAgregar').btnEl.dom.style.background = '';
      };

      /*******************************************************/



      this.megrid.topToolbar.items.items[2].container.dom.style.width="75px";
      this.megrid.topToolbar.items.items[2].container.dom.style.height="35px";
      this.megrid.topToolbar.items.items[2].btnEl.dom.style.height="35px";

      /*Aumentando para el hover Ismael Valdivia (13/11/2020)*/

      Ext.getCmp('botonEliminar').el.dom.onmouseover = function () {
        Ext.getCmp('botonEliminar').btnEl.dom.style.background = 'rgba(255, 0, 0, 0.5)';
      };

      Ext.getCmp('botonEliminar').el.dom.onmouseout = function () {
        Ext.getCmp('botonEliminar').btnEl.dom.style.background = '';
      };


      this.megrid.topToolbar.el.dom.style.background="#3AC2B6";
      this.megrid.topToolbar.el.dom.style.height="45px";
      this.megrid.topToolbar.el.dom.style.borderRadius="2px";
      this.megrid.body.dom.childNodes[0].firstChild.children[0].firstChild.style.background='#FFF4EB';

      /*******************************************************/
      /*Aqui el boton de generar*/
      this.arrayBotones[0].scope.form.buttons[0].container.dom.style.width="20px";
      this.arrayBotones[0].scope.form.buttons[0].btnEl.dom.style.width="190px";
      this.arrayBotones[0].scope.form.buttons[0].btnEl.dom.style.height="50px";
      /*************************/


      if (this.accionFormulario == 'NEW') {
        this.funcionesNuevo();
      }
    },


    loadValoresIniciales:function()
    {
       Phx.vista.FormFacturacionExportacion.superclass.loadValoresIniciales.call(this);
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
				name: 'nit',
				fieldLabel: 'NIT',
				allowBlank: false,
				width:200,
				maxLength:20,
        //disabled:true,
			},
			type:'NumberField',
			id_grupo:0,
			form:true,
			//valorInicial:'0'
		},
    {
 		 config:{
 			 name: 'nombre_factura',
 			 fieldLabel: 'Razón Social',
 			 allowBlank: false,
 			 width:200,
 			 gwidth: 150,
 			 maxLength:100,
			 style:'text-transform:uppercase'
 		 },
 			 type:'TextField',
 			 filters:{pfiltro:'fact.nombre_factura',type:'string'},
 			 id_grupo:0,
 			 grid:true,
 			 bottom_filter:true,
 			 form:true
 	 },

    {
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_cliente'
			},
			type:'Field',
			form:true
		},
    {
			config:{
				name: 'direccion_cliente',
				fieldLabel: 'Dirección del Importador',
				allowBlank: true,
				width:200,
				style:'text-transform:uppercase;'
			},
				type:'TextArea',
				id_grupo:0,
				form:true
		},
    {
   		 config: {
   				 name: 'id_moneda_venta',
   				 fieldLabel: 'Moneda de la Transacción',
   				 allowBlank: true,
   				 width:200,
   				 listWidth:250,
   				 resizable:true,
   				 gwidth: 150,
   				 emptyText: 'Moneda a pagar...',
   				 store: new Ext.data.JsonStore({
   						 url: '../../sis_contabilidad/control/MonedaPais/listarMonedaPais',
   						 id: 'id_moneda',
   						 root: 'datos',
   						 sortInfo: {
   								 field: 'moneda',
   								 direction: 'ASC'
   						 },
   						 totalProperty: 'total',
   						 fields: ['id_moneda', 'desc_moneda', 'id_moneda_pais'],
   						 remoteSort: true,
   						 baseParams: {combo: 'si',par_filtro: 'mon.codigo#mon.moneda'}
   				 }),
   				 valueField: 'id_moneda',
   				 gdisplayField : 'desc_moneda',
   				 displayField: 'desc_moneda',
   				 hiddenName: 'id_moneda',
   				 tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;"><b style="color:black;">Moneda:</b> <b>{desc_moneda}</b></p></div></tpl>',
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
   		 id_grupo: 0,
   		 form: true,
   		 grid: true
    },
    {
			config : {
				name : 'id_formula',
				fieldLabel : 'Paquetes',
				allowBlank : true,
        width:200,
        listWidth:'450',
        maxHeight : 450,
        resizable: true,
				emptyText : 'Paquetes...',
				store : new Ext.data.JsonStore({
          //url: '../../sis_ventas_facturacion/control/Formula/listarFormula',
          url: '../../sis_ventas_facturacion/control/Formula_v2/listarFormula',
					id : 'id_formula',
					root : 'datos',
					sortInfo : {
						field : 'nombre',
						direction : 'ASC'
					},
					totalProperty : 'total',
					fields : ['id_formula', 'nombre', 'descripcion'],
					remoteSort : true,
					baseParams : {
						par_filtro : 'form.nombre',
            emision:'FACT_EXPO',
            regional:'BOL'
					}
				}),
				valueField : 'id_formula',
				displayField : 'nombre',
				gdisplayField : 'nombre',
				hiddenName : 'id_formula',
				forceSelection : false,
				typeAhead : false,
				// tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Nombre:</b> {nombre}</p><p><b>Descripcion:</b> {descripcion}</p></div></tpl>',
        tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color: green; font-weight:bold;"> {nombre}</p></div></tpl>',
				triggerAction : 'all',
				lazyRender : true,
				mode : 'remote',
				pageSize : 20,
				queryDelay : 1000,
				cls:'uppercase',
				gwidth : 500,
				minChars : 2,
				style:'text-transform:uppercase;'
			},
			type : 'ComboBox',
			id_grupo :1,
			form : true
		},
    {
  	 config:{
  		 name: 'observaciones',
  		 fieldLabel: 'Incoterm y Puerta Destino',
  		 allowBlank: false,
  		 width:200,
  		 gwidth: 150,
  		 maxLength:100,
  		 style:'text-transform:uppercase'
  	 },
  		 type:'TextField',
  		 filters:{pfiltro:'fact.nombre_factura',type:'string'},
  		 id_grupo:1,
  		 grid:true,
  		 bottom_filter:true,
  		 form:true
   },


   {
    config:{
   	 name: 'tipo_cambio',
   	 fieldLabel: 'Tipo de Cambio',
   	 allowBlank: false,
   	 width:200,
   	 gwidth: 150,
   	 maxLength:100,
     disabled:true,
   	 style:'text-transform:uppercase'
    },
   	 type:'TextField',
   	 filters:{pfiltro:'fact.nombre_factura',type:'string'},
   	 id_grupo:1,
   	 grid:true,
   	 bottom_filter:true,
   	 form:true
   },

   {
       config:{
           name: 'valor_bruto',
           fieldLabel: 'Valor Bruto',
           allowBlank: false,
           width:150,
           maxLength:20,
           enableKeyEvents: true,
           allowNegative:false,
       },
           type:'NumberField',
           id_grupo:2,
           form:true,
           valorInicial:'0'
   },
   {
       config:{
           name: 'transporte_fob',
           fieldLabel: 'Transporte FOB',
           allowBlank: false,
           width:150,
           maxLength:20,
           enableKeyEvents: true,
           allowNegative:false,
       },
           type:'NumberField',
           id_grupo:2,
           form:true,
           valorInicial:'0'
   },
   {
       config:{
           name: 'seguros_fob',
           fieldLabel: 'Seguros FOB',
           allowBlank: false,
           width:150,
           maxLength:20,
           enableKeyEvents: true,
           allowNegative:false,
       },
           type:'NumberField',
           id_grupo:2,
           form:true,
           valorInicial:'0'
   },
   {
       config:{
           name: 'otros_fob',
           fieldLabel: 'Otros FOB',
           allowBlank: false,
           width:150,
           maxLength:20,
           enableKeyEvents: true,
           allowNegative:false,
       },
           type:'NumberField',
           id_grupo:2,
           form:true,
           valorInicial:'0'
   },
   {
       config:{
           name: 'total_fob',
           fieldLabel: 'Total FOB',
           allowBlank: false,
           width:150,
           maxLength:20,
           disabled:true,
           enableKeyEvents: true,
           allowNegative:false,
       },
           type:'NumberField',
           id_grupo:2,
           form:true,
           valorInicial:'0'
   },
   /*******************************************/

   /*Campos para los montos CIF*/
   {
       config:{
           name: 'transporte_cif',
           fieldLabel: 'Transporte Internacional',
           allowBlank: false,
           width:150,
           maxLength:20,
           enableKeyEvents: true,
           allowNegative:false,
       },
           type:'NumberField',
           id_grupo:3,
           form:true,
           valorInicial:'0'
   },
   {
       config:{
           name: 'seguros_cif',
           fieldLabel: 'Seguro Internacional',
           allowBlank: false,
           width:150,
           maxLength:20,
           enableKeyEvents: true,
           allowNegative:false,
       },
           type:'NumberField',
           id_grupo:3,
           form:true,
           valorInicial:'0'
   },
   {
       config:{
           name: 'otros_cif',
           fieldLabel: 'Otros',
           allowBlank: false,
           width:150,
           maxLength:20,
           enableKeyEvents: true,
           allowNegative:false,
       },
           type:'NumberField',
           id_grupo:3,
           form:true,
           valorInicial:'0'
   },
   {
       config:{
           name: 'total_cif',
           fieldLabel: 'Total General',
           allowBlank: false,
           width:150,
           maxLength:20,
           //disabled:true,
           enableKeyEvents: true,
           allowNegative:false,
           readOnly:true,
           style: 'background-color: #F0FF00; background-image: none; color: green;'
       },
           type:'NumberField',
           id_grupo:3,
           form:true,
           valorInicial:'0'
   },

   /*Aqui para las formas de pago*/
   {
       config: {
           name: 'id_moneda',
           fieldLabel: 'Moneda',
           allowBlank: false,
           width:150,
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
               baseParams: {par_filtro: 'moneda.codigo#moneda.codigo_internacional', filtrar: 'si'}
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
       id_grupo: 4,
       form: true
   },
   {
       config: {
           name: 'id_medio_pago',
           fieldLabel: 'Medio de pago',
           allowBlank: false,
           width:150,
           id: 'testeoColor',
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
               baseParams: {par_filtro: 'mppw.name#fp.fop_code',
               emision:'FACTCOMP',
               regional:'BOL'
             }
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
           // gwidth: 150,
           listWidth:250,
           resizable:true,
           minChars: 2,
           disabled:false
       },
       type: 'ComboBox',
       id_grupo: 4,
       grid: true,
       form: true
   },
   /*********************************************************************************************/
   {
       config:{
           name: 'numero_tarjeta',
           fieldLabel: 'N° Tarjeta',
           allowBlank: true,
           width:150,
           maxLength:20,
           minLength:15

       },
           type:'TextField',
           id_grupo:4,
           form:true
   },
   {
       config:{
           name: 'mco',
           fieldLabel: 'MCO',
           allowBlank: false,
           width:150,
           gwidth: 150,
           minLength:15,
           maxLength:20
       },
       type:'TextField',
       id_grupo:4,
       grid:true,
       form:true
   },
   {
       config:{
           name: 'codigo_tarjeta',
           fieldLabel: 'Codigo de Autorización',
           allowBlank: false,
           width:150,
           minLength:6,
           maxLength:6,
           style:'text-transform:uppercase;',
           maskRe: /[a-zA-Z0-9]+/i,
           regex: /[a-zA-Z0-9]+/i

       },
           type:'TextField',
           id_grupo:4,
           form:true
   },
   {
     config: {
       name: 'id_auxiliar',
       fieldLabel: 'Cuenta Corriente',
       allowBlank: true,
       width:150,
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
       minChars: 2,
       renderer : function(value, p, record) {
         return String.format('{0}', record.data['nombre_auxiliar']);
       }
     },
     type: 'ComboBox',
     id_grupo: 4,
     grid: true,
     form: true
   },
   {
       config:{
           name: 'monto_forma_pago',
           fieldLabel: 'Importe Recibido',
           allowBlank: false,
           width:150,
           maxLength:20,
           allowNegative:false,
           enableKeyEvents : true
           //value:0
       },
           type:'NumberField',
           id_grupo:4,
           form:true,
           //valorInicial:'0'
   },
   // {
   //     config:{
   //         name: 'tipo_tarjeta',
   //         fieldLabel: 'Tipo Tarjeta',
   //         allowBlank: false,
   //         width:150,
   //         hidden:true,
   //         //maxLength:20,
   //         //allowNegative:false,
   //         //value:0
   //     },
   //         type:'TextField',
   //         id_grupo:4,
   //         form:true,
   //         //valorInicial:'0'
   // },
       {
           config: {
               name: 'id_moneda_2',
               fieldLabel: 'Moneda',
               allowBlank: false,
               width:150,
               listWidth:250,
               resizable:true,
               disabled:false,
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
                   baseParams: {par_filtro: 'moneda.codigo#moneda.codigo_internacional', filtrar: 'si'}
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
           id_grupo: 5,
           form: true
       },

       {
           config: {
               name: 'id_medio_pago_2',
               fieldLabel: 'Medio de pago',
               allowBlank: false,
               width:150,
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
                   baseParams: {par_filtro: 'mppw.name#fp.fop_code',
                   emision:'FACTCOMP',
                   regional:'BOL'
                 }
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
               minChars: 2,
               disabled:false,
               renderer : function(value, p, record) {
                   return String.format('{0}', record.data['codigo_fp']);
               }
           },
           type: 'ComboBox',
           id_grupo: 5,
           grid: true,
           form: true
       },
       {
         config: {
           name: 'id_auxiliar_2',
           fieldLabel: 'Cuenta Corriente',
           allowBlank: true,
           width:150,
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
           minChars: 2,
           renderer : function(value, p, record) {
             return String.format('{0}', record.data['nombre_auxiliar']);
           }
         },
         type: 'ComboBox',
         id_grupo: 5,
         grid: true,
         form: true
       },
       {
           config:{
               name: 'numero_tarjeta_2',
               fieldLabel: 'N° Tarjeta',
               allowBlank: true,
               //disabled:true,
               width:150,
               gwidth: 150,
               maxLength:20,
               minLength:15
           },
           type:'TextField',
           id_grupo:5,
           grid:false,
           form:true
       },
       // ///nuevo
       {
           config:{
               name: 'mco_2',
               fieldLabel: 'MCO 2',
               allowBlank: true,
               width:150,
               gwidth: 150,
               //disabled:true,
               minLength:15,
               maxLength:20
           },
           type:'TextField',
           id_grupo:5,
           grid:true,
           form:true
       },
       {
           config:{
               name: 'codigo_tarjeta_2',
               fieldLabel: 'Codigo de Autorización',
               allowBlank: true,
               width:150,
               //disabled:true,
               minLength:6,
               maxLength:6,
               style:'text-transform:uppercase;',
               maskRe: /[a-zA-Z0-9]+/i,
               regex: /[a-zA-Z0-9]+/i

           },
           type:'TextField',
           id_grupo:5,
           grid:false,
           form:true
       },
       {
           config:{
               name: 'monto_forma_pago_2',
               fieldLabel: 'Importe Recibido',
               allowBlank:true,
               width:150,
               allowDecimals:true,
               decimalPrecision:2,
               allowNegative : false,
               //disabled:true,
               gwidth: 125,
               style: 'background-color: #f2f23c;  background-image: none;'
           },
           type:'NumberField',
           id_grupo:5,
           grid:true,
           form:true
       },


       {
           config: {
               name: 'id_sucursal',
               fieldLabel: 'Sucursal',
               allowBlank: false,
               width:200,
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
                   baseParams: {filtro_usuario: 'si',par_filtro: 'suc.nombre#suc.id_sucursal'}
               }),
               valueField: 'id_sucursal',
               gdisplayField : 'nombre_sucursal',
               displayField: 'nombre',
               hiddenName: 'id_sucursal',
               tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>id:</b> {id_sucursal}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
               forceSelection: true,
               typeAhead: false,
               triggerAction: 'all',
               lazyRender: true,
               mode: 'remote',
               pageSize: 15,
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
                  baseParams: {filtro_usuario: 'si',par_filtro: 'puve.nombre#puve.codigo'}
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
              minChars: 2,
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
   /******************************/


    ],
    title: 'Formulario Venta',

    onAfterEdit:function(re, o, rec, num){
            //set descriptins values ...  in combos boxs

            //var cmb_rec = this.detCmp['id_medio_pago'].store.getById(rec.get('id_medio_pago'));
            if(cmb_rec) {

               console.log("aqui llega datos del detGrid",cmb_rec);

                rec.set('codigo', cmb_rec.get('codigo'));
            }

        },

        evaluaRequistos: function(){
            //valida que todos los requistosprevios esten completos y habilita la adicion en el grid
            var i = 0;
            sw = true;
            while( i < this.Componentes.length) {
                if(!this.Componentes[i].isValid()){
                    sw = false;
                }
                i++;
            }
            return sw
        },

        onSubmit: function(o) {

          /*Control Para Bloquear el boton cuando se emita la factura*/
          if(this.evaluaRequistos() === true) {
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(true);
            }
          else{
              alert('Verifique los campos de registro');
              this.arrayBotones[0].scope.form.buttons[0].setDisabled(false);
          }
          /************************************************************/


          var arra = [], i, me = this;
          var formapa = [];
          for (i = 0; i < me.megrid.store.getCount(); i++) {
              var record = me.megrid.store.getAt(i);
              arra[i] = record.data;
          }
          if (me.storeFormaPago) {
            for (i = 0; i < me.storeFormaPago.getCount(); i++) {
                var record = me.storeFormaPago.getAt(i);
                formapa[i] = record.data;
            }
        }

        me.argumentExtraSubmit = { 'json_new_records': JSON.stringify(arra,
        				function replacer(key, value) {
                       		if (typeof value === 'string') {
                            	return String(value).replace(/&/g, "%26")
                            }
                            return value;
                        }),
                        'formas_pago' :  JSON.stringify(formapa,
        				function replacer(key, value) {
                       		if (typeof value === 'string') {
                            	return String(value).replace(/&/g, "%26")
                            }
                            return value;
                        }),
                        'tipo_factura':this.data.objPadre.tipo_factura};

            if( i > 0 &&  !this.editorDetail.isVisible()){
                 Phx.vista.FormFacturacionExportacion.superclass.onSubmit.call(this,o);
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
              Ext.Ajax.request({
                url:'../../sis_ventas_facturacion/control/FacturacionExportacion/EmitirFacturaExportacion',
                params:{id_estado_wf_act:d.id_estado_wf,
                  id_proceso_wf_act:d.id_proceso_wf,
                  tipo_pv:this.data.tipo_punto_venta,
                  tipo:'recibo'},
                  success:this.successWizard,
                  failure: this.conexionFailure,
                  timeout:this.timeout,
                  scope:this
                });

        this.panel.close();
        Phx.CP.getPagina(this.idContenedorPadre).reload();
        },

        // conexionFailure:function(resp)
        // {
        // 	/*Control Para Bloquear el boton cuando se emita la factura*/
        //   this.arrayBotones[0].scope.form.buttons[0].setDisabled(false);
        //   /*************************************************************/
        //   Phx.CP.conexionFailure(resp);
        //
        // },

        successWizard:function(resp){

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
              Ext.Ajax.request({
      	          url : '../../sis_ventas_facturacion/control/FacturacionExportacion/imprimirFactura',
      	          params : {
      	            //imprimir : 'si'
      	            'id_venta' : this.id_venta
                    /*'id_punto_venta' : rec.data.id_punto_venta,
      	            'formato_comprobante' : this.variables_globales.formato_comprobante,
      	            'tipo_factura': this.store.baseParams.tipo_factura,
      	            'enviar_correo': this.store.baseParams.enviar_correo*/
      	          },
      	          success : this.successExport,
      	          failure : this.conexionFailure,
      	          timeout : this.timeout,
      	          scope : this
      	        });
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
