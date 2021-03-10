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
                      store:['REPORTE DE FACTURAS','REPORTE DE FACTURAS / CONCEPTO','RESUMEN DE FACTURAS / CONCEPTO']
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
	                    baseParams: {tipo_usuario : 'todos',par_filtro: 'puve.nombre#puve.codigo'}
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
             config:{
                 name: 'id_concepto',
                 fieldLabel: 'Concepto',
                 allowBlank: true,
                 emptyText : 'Concepto...',
                 store : new Ext.data.JsonStore({
                             url:'../../sis_parametros/control/ConceptoIngas/listarConceptoIngas',
                             id : 'id_concepto_ingas',
                             root: 'datos',
                             sortInfo:{
                                     field: 'desc_ingas',
                                     direction: 'ASC'
                             },
                             totalProperty: 'total',
                             fields: ['id_concepto_ingas','tipo','desc_ingas','movimiento','desc_partida','id_grupo_ots','filtro_ot','requiere_ot', 'codigo'],
                             remoteSort: true,
                             baseParams:{par_filtro:'desc_ingas',codigo:'Facturacion'}
                 }),
                valueField: 'id_concepto_ingas',
                displayField: 'desc_ingas',
                gdisplayField: 'desc_ingas',
                hiddenName: 'id_concepto_ingas',
                forceSelection:true,
                typeAhead: false,
                triggerAction: 'all',
                tpl:'<tpl for="."><div class="x-combo-list-item"><b>Codigo: </b><span style="color:red;font-weight:bold;">{codigo}</span><br><b>Descripcion: </b><span style="color:blue;font-weight:bold;">{desc_ingas}</span></div></tpl>',
                listWidth:450,
                resizable:true,
                lazyRender:true,
                mode:'remote',
                pageSize:10,
                width:250,
                hidden:true,
                queryDelay:1000,
                gwidth:100,
                minChars:1
             },
             type:'ComboBox',
             id_grupo:0,
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


			this.onEnablePanel(this.idContenedor + '-south',
				Ext.apply(parametros,{	'desde': desde,
										'hasta': hasta,
										'formato_reporte': formato_reporte,
										'id_concepto': id_concepto,
										'id_punto_venta' : id_punto_venta
									 }));
        }
    },

    iniciarEventos: function(){

      this.Cmp.formato_reporte.on('select',function(a,b,c) {
				if (b.data.field1 == 'REPORTE DE FACTURAS') {
				      this.mostrarComponente(this.Cmp.id_punto_venta);
              this.ocultarComponente(this.Cmp.id_concepto);
              this.Cmp.id_concepto.reset();
				} else if (b.data.field1 == 'REPORTE DE FACTURAS / CONCEPTO') {
              this.mostrarComponente(this.Cmp.id_concepto);
              this.mostrarComponente(this.Cmp.id_punto_venta);
              this.Cmp.id_punto_venta.reset();
        } else if (b.data.field1 == 'RESUMEN DE FACTURAS / CONCEPTO') {
              this.mostrarComponente(this.Cmp.id_concepto);
              this.mostrarComponente(this.Cmp.id_punto_venta);
              this.Cmp.id_punto_venta.reset();
         }
			},this);

    },

    loadValoresIniciales: function(){
    	Phx.vista.FormFiltroReporteFacturacion.superclass.loadValoresIniciales.call(this);

    }

})
</script>
