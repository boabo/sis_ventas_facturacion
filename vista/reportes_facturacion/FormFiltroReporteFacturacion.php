<?php
/**
*@package pXP
*@file    FormFiltroReporteFacturacion.php
*@author  Ismael Valdivia
*@date    01-12-2020
*@description permite filtrar datos para sacar el reporte de Facturas
*/
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.FormFiltroReporteFacturacion=Ext.extend(Phx.frmInterfaz,{
    constructor:function(config)
    {

        Phx.vista.FormFiltroReporteFacturacion.superclass.constructor.call(this,config);

        Ext.Ajax.request({
    				url:'../../sis_ventas_facturacion/control/Cajero/getTipoUsuario',
    				params: {'vista':'cajero'},
    				success: function(resp){
    						var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
    						this.tipo_usuario = reg.ROOT.datos.v_tipo_usuario;

                this.Cmp.id_punto_venta.store.baseParams.tipo_usuario = this.tipo_usuario;

    				},
    				failure: this.conexionFailure,
    				timeout:this.timeout,
    				scope:this
    		});


        this.init();
        this.iniciarEventos();

        if(config.detalle){

  			//cargar los valores para el filtro
  			this.loadForm({data: config.detalle});
  			var me = this;
  			setTimeout(function(){
  				me.onSubmit()
  			}, 1500);

  		}
      /*Fondo de los filtros*/
      this.regiones[0].body.dom.style.background='#dfe8f6';


    },


    Grupos: [
  			{
  					layout: 'column',
            xtype: 'fieldset',
            region: 'north',
            collapseFirst : false,
            width: '100%',
            autoScroll:true,
            padding: '0 0 0 0',
  					items: [
              {
               bodyStyle: 'padding-right:0px;',
               autoHeight: true,
               border: false,
               items:[
                  {
                   xtype: 'fieldset',
                   frame: true,
                   border: false,
                   layout: 'form',
                   width: '90%',
                   style: {
                          height:'150px',
                          width:'590px',
                          backgroundColor:'#dfe8f6'
                       },
                   padding: '0 0 0 0',
                   bodyStyle: 'padding-left:0px;',
                   id_grupo: 0,
                   items: [],
                }]
            },
            {
             bodyStyle: 'padding-right:0px;',
             autoHeight: true,
             border: false,
             items:[
                {
                 xtype: 'fieldset',
                 frame: true,
                 border: false,
                 layout: 'form',
                 style: {
                        height:'150px',
                        width:'300px',
                        backgroundColor:'#dfe8f6'
                     },
                 padding: '0 0 0 0',
                 bodyStyle: 'padding-left:0px;',
                 id_grupo: 1,
                 items: [],
              }]
          },
            {
             bodyStyle: 'padding-right:0px;',
             border: false,
             autoHeight: true,
             items: [{
                   xtype: 'fieldset',
                   frame: true,
                   layout: 'form',
                   style: {
                          height:'150px',
                          width:'350px',
                          backgroundColor:'#dfe8f6',
                         },
                   border: false,
                   padding: '0 0 0 0',
                   bodyStyle: 'padding-left:0px;',
                   id_grupo: 2,
                   items: [],
                }]
            },
  					]
  			}
  	],


    Atributos:[
      {
           config : {
             name: 'formato_reporte',
                      fieldLabel: 'Reporte',
                      allowBlank:false,
                      emptyText:'Formato Reporte...',
                      typeAhead: true,
                      triggerAction: 'all',
                      lazyRender:true,
                      mode: 'local',
                      width:250,
                      gwidth: 150,
                     disabled : false,
                     hidden : false,
                     store: new Ext.data.ArrayStore({
                           id: 0,
                           fields: [
                               'cod',
                               'desc'
                           ],
                           data: [['REPORTE DE FACTURAS', 'REPORTE DE EMISIONES'], ['REPORTE DE FACTURAS / CONCEPTO', 'REPORTE DE EMISIONES / CONCEPTO'], ['RESUMEN DE FACTURAS / CONCEPTO','RESUMEN DE EMISIONES / CONCEPTO']]
                       }),
                       valueField: 'cod',
                       displayField: 'desc'
           },
           type: 'ComboBox',
           id_grupo: 0,
           form: true
         },

         {
			        config: {
	                name: 'id_punto_venta',
	                fieldLabel: 'Punto de Venta',
	                allowBlank: false,
	                emptyText: 'Elija un Punto de Venta...',
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
	                    baseParams: {par_filtro: 'puve.nombre#puve.codigo'}
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
	                width:250,
	                resizable:true,
	                minChars: 2,
	                renderer : function(value, p, record) {
	                    return String.format('{0}', record.data['nombre_punto_venta']);
	                },
                	hidden : false

	            },
	            type: 'ComboBox',
	            id_grupo: 0,
	            filters: {pfiltro: 'puve.nombre',type: 'string'},
	            grid: true,
	            form: true
	       },
         {
                config: {
                    name: 'nombre_punto_venta',
                    fieldLabel: 'Desc Pv',
                    allowBlank: true,
                    width: '100%',
                    gwidth: 110,
                    /* galign: 'right ', */
                    maxLength: 100,
                    hidden : true,
                },
                type: 'TextField',
                id_grupo: 0,
                grid: true,
                form: true
          },
         {
   				config:{
   					name:'id_usuario_cajero',
   					fieldLabel:'Cajero',
   					allowBlank:false,
   					emptyText:'Cajero...',
   					store: new Ext.data.JsonStore({

   						url: '../../sis_seguridad/control/Usuario/listarUsuario',
   						id: 'id_usuario',
   						root: 'datos',
   						sortInfo:{
   							field: 'desc_person',
   							direction: 'ASC'
   						},
   						totalProperty: 'total',
   						fields: ['id_usuario','desc_person','cuenta'],
   						// turn on remote sorting
   						remoteSort: true,
   						baseParams:{par_filtro:'PERSON.nombre_completo2', '_adicionar':'si'}
   					}),
   					valueField: 'id_usuario',
   					displayField: 'desc_person',
   					gdisplayField:'desc_persona',//dibuja el campo extra de la consulta al hacer un inner join con orra tabla
   					tpl:'<tpl for="."><div class="x-combo-list-item"><br><p>{desc_person}</p><p>Cuenta Usuario:{cuenta}</p> </div></tpl>',
   					hiddenName: 'id_usuario',
   					forceSelection:true,
   					typeAhead: true,
   					triggerAction: 'all',
   					lazyRender:true,
   					mode:'remote',
   					pageSize:10,
   					queryDelay:1000,
   					width:250,
   					gwidth:280,
   					minChars:2,
   					turl:'../../../sis_seguridad/vista/usuario/Usuario.php',
   					ttitle:'Usuarios',
   					// tconfig:{width:1800,height:500},
   					tdata:{},
   					tcls:'usuario',
   					pid:this.idContenedor,

   					renderer:function (value, p, record){return String.format('{0}', record.data['desc_persona']);}
   				},
   				//type:'TrigguerCombo',
   				type:'ComboBox',
   				bottom_filter: true,
   				id_grupo:0,
   				filters:{
   					pfiltro:'nombre_completo1',
   					type:'string'
   				},

   				grid:true,
   				form:true
   			},

        {
            config: {
                name: 'tipo_documento',
                fieldLabel: 'Tipo Documento',
                typeAhead: true,
                allowBlank: false,
                triggerAction: 'all',
                emptyText: 'Tipo...',
                selectOnFocus: true,
                mode: 'local',
                store: new Ext.data.ArrayStore({
                    fields: ['ID', 'valor'],
                    data: [['factura', 'Factura'],
                        ['recibo', 'Recibo']
                    ]
                }),
                valueField: 'ID',
                displayField: 'valor',
                width: 250,
                style:'margin-bottom: 10px;'
            },
            type: 'ComboBox',
            id_grupo: 0,
            form: true
        },

         {
             config:{
                 name: 'id_concepto',
                 fieldLabel: 'Concepto',
                 allowBlank: true,
                 emptyText : 'Concepto...',
                 store: new Ext.data.JsonStore({
                     url: '../../sis_ventas_facturacion/control/Servicios/listarServicios',
                     id: 'id_producto',
                     root: 'datos',
                     sortInfo: {
                         field: 'desc_ingas',
                         direction: 'ASC'
                     },
                     totalProperty: 'total',
                     fields: ['id_concepto_ingas', 'tipo','desc_moneda','id_moneda','desc_ingas','requiere_descripcion','precio','excento','contabilizable','boleto_asociado','nombre_actividad','comision'],
                     remoteSort: true,
                     baseParams: {par_filtro: 'ingas.desc_ingas'}
                 }),
                 valueField: 'id_concepto_ingas',
                 displayField: 'desc_ingas',
                 gdisplayField: 'desc_ingas',
                 hiddenName: 'id_producto',
                 forceSelection: true,
                 typeAhead: false,
                 triggerAction: 'all',
                 lazyRender: true,
                 mode: 'remote',
                 resizable:true,
                 pageSize: 20,
                 queryDelay: 1000,
                 //anchor: '100%',
                 width : 250,
                 listWidth:'600',
                 hidden:true,
                 minChars: 2 ,
                 listeners: {
                   beforequery: function(qe){
                     delete qe.combo.lastQuery;
                   }
                 }
             },
             type:'ComboBox',
             id_grupo:1,
             form:true,
             grid:true
         },

	   	  {
				config:{
					name: 'desde',
					fieldLabel: 'Desde',
					allowBlank: false,
					format: 'd/m/Y',
					width:250,
				},
				type: 'DateField',
				id_grupo: 1,
				form: true
		  },
		  {
				config:{
					name: 'hasta',
					fieldLabel: 'Hasta',
					allowBlank: false,
					format: 'd/m/Y',
					width:250,
				},
				type: 'DateField',
				id_grupo: 1,
				form: true
		  },
      {
  			config:{
  				name: 'nit',
  				fieldLabel: 'NIT',
  				allowBlank: true,
  				width:250,
  				maxLength:20
  			},
  			type:'NumberField',
  			id_grupo:1,
  			form:true,
  		},

	],
	labelSubmit: '<i class="fa fa-check"></i> Aplicar Filtro',
	south: {
		url: '../../../sis_ventas_facturacion/vista/reportes_facturacion/DetalleFacturacion.php',
		title: 'Detalle Facturación',
		height: '70%',
		cls: 'DetalleFacturacion'
	},
	title: 'Filtro de mayores',
	// Funcion guardar del formulario
	onSubmit: function(o) {
		var me = this;
		if (me.form.getForm().isValid()) {
			var parametros = me.getValForm();

      var desde=parametros.desde;
			var hasta=parametros.hasta;
			var formato_reporte=parametros.formato_reporte;
			var id_concepto=parametros.id_concepto;
      var id_punto_venta=parametros.id_punto_venta;
      var id_usuario_cajero=parametros.id_usuario_cajero;
			var tipo_documento=parametros.tipo_documento;
      var nombre_pv = parametros.nombre_punto_venta;
      var nit = parametros.nit;

			this.onEnablePanel(this.idContenedor + '-south',
				Ext.apply(parametros,{
                    'desde': desde,
										'hasta': hasta,
										'formato_reporte': formato_reporte,
										'id_concepto': id_concepto,
                    'id_punto_venta' : id_punto_venta,
                    'id_usuario_cajero' : id_usuario_cajero,
                    'tipo_documento' : tipo_documento,
                    'nombre_pv' : nombre_pv,
                    'nit' : nit,
										'imprimir_reporte' : 'no'
									 }));
        }
    },

    iniciarEventos: function(){

      this.Cmp.formato_reporte.on('select',function(a,b,c) {

				if (b.data.cod == 'REPORTE DE FACTURAS') {
				      this.mostrarComponente(this.Cmp.id_punto_venta);
              this.ocultarComponente(this.Cmp.id_concepto);
              this.Cmp.id_concepto.reset();
				} else if (b.data.cod == 'REPORTE DE FACTURAS / CONCEPTO') {
              this.mostrarComponente(this.Cmp.id_concepto);
              this.mostrarComponente(this.Cmp.id_punto_venta);
              //this.Cmp.id_punto_venta.reset();
        } else if (b.data.cod == 'RESUMEN DE FACTURAS / CONCEPTO') {
              this.mostrarComponente(this.Cmp.id_concepto);
              this.mostrarComponente(this.Cmp.id_punto_venta);
              //this.Cmp.id_punto_venta.reset();
         }
			},this);

      /*Aumentando para controlar los conceptos dependiendo del tipo de documento (Ismael Valdivia 24/03/2021)*/
      this.Cmp.tipo_documento.on('select',function(a,b,c) {
        this.Cmp.id_concepto.store.baseParams.tipo_documento = b.data.valor;
      },this);
      /********************************************************************************************************/

      this.Cmp.id_punto_venta.on('select',function(a,b,c) {
        this.Cmp.nombre_punto_venta.setValue(b.data.nombre);
      },this);


    },

    loadValoresIniciales: function(){
    	Phx.vista.FormFiltroReporteFacturacion.superclass.loadValoresIniciales.call(this);

    }

})
</script>
