<?php
/**
*@package pXP
*@file    FormFiltroEmisionFacturas.php
*@author  Ismael Valdivia
*@date    11-02-2021
*@description permite filtrar datos para sacar el reporte de Facturas
*/
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.FormFiltroEmisionFacturas=Ext.extend(Phx.frmInterfaz,{
    constructor:function(config)
    {

        Phx.vista.FormFiltroEmisionFacturas.superclass.constructor.call(this,config);
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
      this.regiones[0].body.dom.style.background='#C8E3F5';


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
                          backgroundColor:'#C8E3F5'
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
                        backgroundColor:'#C8E3F5'
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
                          backgroundColor:'#C8E3F5',
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
             name: 'reporte',
                      fieldLabel: 'Reporte',
                      allowBlank:true,
                      emptyText:'Formato Reporte...',
                      typeAhead: true,
                      triggerAction: 'all',
                      lazyRender:true,
                      mode: 'local',
                      width:250,
                      gwidth: 150,
                      disabled : false,
                      hidden : true,
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
 	                    baseParams: {tipo_usuario : 'todos',par_filtro: 'puve.nombre#puve.codigo', '_adicionar':'si'}
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
                config: {
                    name: 'codigo_auxiliar',
                    fieldLabel: 'Codigo Aux',
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
                config: {
                    name: 'id_auxiliar',
                    fieldLabel: 'Cuenta Corriente',
                    allowBlank: false,
                    width:250,
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
                        baseParams: {par_filtro: 'auxcta.codigo_auxiliar#auxcta.nombre_auxiliar',corriente:'si', '_adicionar':'si'}
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
                id_grupo: 0,
                grid: true,
                form: true
            },

        {
          config : {
                       name: 'formato_reporte',
                       fieldLabel: 'Reporte',
                       allowBlank: false,
                       emptyText:'Seleccione el reporte...',
                       typeAhead: true,
                       triggerAction: 'all',
                       lazyRender:true,
                       mode: 'local',
                       width:250,
                       gwidth: 150,
                       disabled : false,
                       listWidth: 300,
                       store:['REPORTE FORMAS DE PAGO CTA/CTE (DEBE)','REPORTE ANTICIPO (HABER)','REPORTE CONSOLIDADO (DEBE-HABER)']
                    },
            type: 'ComboBox',
            id_grupo: 0,
            form: true
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
		url: '../../../sis_ventas_facturacion/vista/reporte_emision_facturas/DetalleEmisionAuxiliares.php',
		title: 'Detalle Auxiliares',
		height: '70%',
		cls: 'DetalleEmisionAuxiliares'
	},
	title: 'Filtro de mayores',
	// Funcion guardar del formulario
	onSubmit: function(o) {
		var me = this;
		if (me.form.getForm().isValid()) {
			var parametros = me.getValForm();

      var id_auxiliar = parametros.id_auxiliar;
      var codigo_auxiliar = parametros.codigo_auxiliar;
      var desde = parametros.desde;
      var hasta = parametros.hasta;
      var id_punto_venta = parametros.id_punto_venta;
      var nombre_pv = parametros.nombre_punto_venta;
			var formato_reporte = parametros.formato_reporte;

      //console.log("aqui llega los paramteros",parametros);
			 this.onEnablePanel(this.idContenedor + '-south',
				Ext.apply(parametros,{
                    'id_auxiliar': id_auxiliar,
                    'codigo_auxiliar': codigo_auxiliar,
                    'desde': desde,
										'hasta': hasta,
                    'id_punto_venta':id_punto_venta,
                    'nombre_pv':nombre_pv,
                    'formato_reporte':formato_reporte
									 }));
        }
    },

    iniciarEventos: function(){


      this.Cmp.id_auxiliar.on('select',function(a,b,c) {
        this.Cmp.codigo_auxiliar.setValue(b.data.codigo_auxiliar);
      },this);

      this.Cmp.id_punto_venta.on('select',function(a,b,c) {
        this.Cmp.nombre_punto_venta.setValue(b.data.nombre);
      },this);


    },

    loadValoresIniciales: function(){
    	Phx.vista.FormFiltroEmisionFacturas.superclass.loadValoresIniciales.call(this);

    }

})
</script>
