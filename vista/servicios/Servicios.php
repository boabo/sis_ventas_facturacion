<?php
/**
*@package pXP
*@file gen-Servicios.php
*@author  (ivaldivia)
*@date 10-09-2019 16:17:39
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.Servicios=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		that=this;
    	//llama al constructor de la clase padre
		Phx.vista.Servicios.superclass.constructor.call(this,config);
		this.init();
		this.iniciarEventos(that);
		this.store.baseParams.Facturacion = 'conceptos_facturacion';
		/*Fondo color tbar (IRVA)*/
		this.bbar.el.dom.style.background='#84BFE7';
		this.tbar.el.dom.style.background='#84BFE7';
		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#E8F0EF';
		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#BFD1DB';
		/************************/

		this.load({params:{start:0, limit:this.tam_pag}})

		/*Aumentando para ir agregando permisos*/
		this.crearFormAuto();
		this.addButton('inserAuto',{ text: 'Configurar Autorizaciones', iconCls: 'blist', disabled: false, handler: this.mostarFormAuto, tooltip: '<b>Configurar autorizaciones</b><br/>Permite seleccionar desde que modulos  puede selecionarse el concepto'});



	},

	crearFormAuto:function(){
		  this.formAuto = new Ext.form.FormPanel({
            baseCls: 'x-plain',
            autoDestroy: true,

            border: false,
            layout: 'form',
             autoHeight: true,


            items: [
							{
								 name:'sw_autorizacion',
								 xtype:"awesomecombo",
								 fieldLabel:'Autorizaciones',
								 allowBlank: true,
								 emptyText:'Autorizaciones...',
								 store : new Ext.data.JsonStore({
									 url : '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
									 id : 'id_catalogo',
									 root : 'datos',
									 sortInfo : {
										 field : 'codigo',
										 direction : 'ASC'
									 },
									 totalProperty : 'total',
									 fields: ['codigo','descripcion'],
									 remoteSort : true,
									 baseParams:{
										cod_subsistema:'PARAM',
										catalogo_tipo:'autorizaciones_concepto'
									},
								 }),
								 valueField: 'codigo',
								 displayField: 'descripcion',
								 mode: 'remote',
								 forceSelection:true,
								 typeAhead: true,
								 triggerAction: 'all',
								 lazyRender: true,
								 queryDelay: 1000,
								 width: 250,
								 minChars: 2 ,
							   enableMultiSelect: true,
								 pageSize: 200,
	 							 queryDelay: 100
							},

							{
								 name:'regionales',
								 xtype:"awesomecombo",
								 fieldLabel:'Regionales',
								 allowBlank: true,
								 emptyText:'Regionales...',
								 store : new Ext.data.JsonStore({
									 url : '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
									 id : 'id_catalogo',
									 root : 'datos',
									 sortInfo : {
										 field : 'codigo',
										 direction : 'ASC'
									 },
									 totalProperty : 'total',
									 fields: ['codigo','descripcion'],
									 remoteSort : true,
									 baseParams:{
										cod_subsistema:'PARAM',
										catalogo_tipo:'regionales_conceptos'
									},
								 }),
								 valueField: 'codigo',
								 displayField: 'descripcion',
								 mode: 'remote',
								 forceSelection:true,
								 typeAhead: true,
								 triggerAction: 'all',
								 lazyRender: true,
								 queryDelay: 1000,
								 width: 250,
								 minChars: 2 ,
							   enableMultiSelect: true,
								 pageSize: 200,
	 							 queryDelay: 100
							},

							/*Aumentando el nivel del Permiso*/
							{
								 name:'nivel_permiso',
								 xtype:"awesomecombo",
								 fieldLabel:'Nivel Permiso',
								 allowBlank: true,
								 emptyText:'Nivel Permiso...',
								 store : new Ext.data.JsonStore({
									 url : '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
									 id : 'id_catalogo',
									 root : 'datos',
									 sortInfo : {
										 field : 'codigo',
										 direction : 'ASC'
									 },
									 totalProperty : 'total',
									 fields: ['codigo','descripcion'],
									 remoteSort : true,
									 baseParams:{
										cod_subsistema:'PARAM',
										catalogo_tipo:'nivel_permiso'
									},
								 }),
								 valueField: 'codigo',
								 displayField: 'descripcion',
								 mode: 'remote',
								 forceSelection:true,
								 typeAhead: true,
								 triggerAction: 'all',
								 lazyRender: true,
								 queryDelay: 1000,
								 width: 250,
								 minChars: 2 ,
							   enableMultiSelect: true,
								 pageSize: 200,
	 							 queryDelay: 100
							},
							/*********************************/

						// 	{
       			// 	name:'sw_autorizacion',
       			// 	xtype:"awesomecombo",
       			// 	fieldLabel:'Autorizaciones',
       			// 	allowBlank: true,
       			// 	emptyText:'Autorizaciones...',
       			// 	store: new Ext.data.ArrayStore({
            //             fields: ['variable', 'valor'],
            //             data : [ ['adquisiciones', 'Adquisiciones'],
            //                      ['pago_directo', 'Pago Recurrente'],
            //                      ['caja_chica', 'Caja Chica'],
            //                      ['fondo_avance', 'Fondo en Avance'],
            //                      ['contrato', 'Contratos'],
            //                      ['pago_unico', 'Pago Único'],
            //                      ['especial', 'Especial'],
            //                      ['viatico', 'Viáticos'],
            //                      ['boarep', 'Boa Rep'],
            //                      ['RO', 'Recibos Oficiales'],
						// 										 ['gestion_materiales', 'Gestión de Materiales']
            //                    ]
            //             }),
       			// 	valueField: 'variable',
				    // displayField: 'valor',
				    // mode: 'local',
	       		//     forceSelection:true,
       			// 	typeAhead: true,
           	// 		triggerAction: 'all',
           	// 		lazyRender: true,
       			// 	queryDelay: 1000,
       			// 	width: 250,
       			// 	minChars: 2 ,
	       		// 	enableMultiSelect: true
       			// }
					]
        });



		this.wAuto = new Ext.Window({
            title: 'Configuracion',
            collapsible: true,
            maximizable: true,
            autoDestroy: true,
            width: 380,
            height: 170,
            layout: 'fit',
            plain: true,
            bodyStyle: 'padding:5px;',
            buttonAlign: 'center',
            items: this.formAuto,
            modal:true,
             closeAction: 'hide',
            buttons: [{
                text: 'Guardar',
                handler: this.saveAuto,
                scope: this

            },
             {
                text: 'Cancelar',
                handler: function(){ this.wAuto.hide() },
                scope: this
            }]
        });

					this.cmpAuto = this.formAuto.getForm().findField('sw_autorizacion');
					this.cmpRegionales = this.formAuto.getForm().findField('regionales');
				 	this.cmpNivelPermiso = this.formAuto.getForm().findField('nivel_permiso');


	},

	mostarFormAuto:function(){
		var data = this.getSelectedData();
		if(data){
			this.cmpAuto.setValue(data.sw_autorizacion);
			this.cmpRegionales.setValue(data.regionales);
			this.cmpNivelPermiso.setValue(data.nivel_permiso);
			this.wAuto.show();
		}

	},
	saveAuto: function(){
		    var d = this.getSelectedData();
		    Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_parametros/control/ConceptoIngas/editAuto',
                params: {
												sw_autorizacion: this.cmpAuto.getValue(),
												regionales: this.cmpRegionales.getValue(),
                	      nivel_permiso: this.cmpNivelPermiso.getValue(),
                	      id_concepto_ingas: d.id_concepto_ingas
                	    },
                success: this.successSinc,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });

	},
	successSinc:function(resp){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if(!reg.ROOT.error){
            	if(this.wOt){
            		this.wOt.hide();
            	}
            	if(this.wAuto){
            		this.wAuto.hide();
            	}

                this.reload();
             }else{
                alert('ocurrio un error durante el proceso')
            }
    },

	onButtonNew:function () {
		Phx.vista.Servicios.superclass.onButtonNew.call(this);
		this.Cmp.movimiento.setValue('recurso');
		this.Cmp.tipo.setValue('Servicio');
		this.ocultarComponente(this.Cmp.punto_venta_asociado);
		this.form.el.dom.firstChild.childNodes[0].style.background = '#84BFE7';
	},

	onButtonEdit:function () {
		Phx.vista.Servicios.superclass.onButtonEdit.call(this);
		this.mostrarComponente(this.Cmp.punto_venta_asociado);
		this.form.el.dom.firstChild.childNodes[0].style.background = '#84BFE7';
		var rec = this.sm.getSelected();
		if (rec.data.punto_venta_asociado == '') {
			this.Cmp.punto_venta_asociado.reset();
		}

	},

	iniciarEventos: function(that){

	},

	capturaFiltros: function (combo, record, index) {
		this.mostrarComponente(that.Cmp.punto_venta_asociado);
	},

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_concepto_ingas'
			},
			type:'Field',
			form:true
		},
		{
				config:{
					name:'tipo',
					fieldLabel:'Tipo',
					allowBlank:false,
					//anchor:'100%',
					width:200,
					emptyText:'Tipo...',
					typeAhead: true,
						triggerAction: 'all',
						lazyRender:true,
						mode: 'local',
						valueField: 'estilo',
						gwidth: 100,
						store:['Producto','Servicio']
				},
				type:'ComboBox',
				id_grupo:0,
				filters:{
								 type: 'list',
								 pfiltro:'conig.tipo',
						 options: ['Producto','Servicio'],
					},
				grid:true,
				form:true
			},
    {
      config:{
        name: 'desc_ingas',
        fieldLabel: 'Descripción',
        allowBlank: true,
				//anchor: '100%',
				width:200,
        gwidth: 450,
        maxLength:500
      },
      type:'TextArea',
      filters:{pfiltro:'ingas.desc_ingas',type:'string'},
      id_grupo:1,
      grid:true,
      form:true,
			bottom_filter:true
    },
		{
      config:{
        name: 'codigo',
        fieldLabel: 'Código del Servicio',
        allowBlank: true,
				//anchor: '100%',
				width:200,
        gwidth: 200,
        maxLength:500
      },
      type:'TextField',
      filters:{pfiltro:'ingas.codigo',type:'string'},
      id_grupo:1,
      grid:true,
      form:true
    },
		{
      config : {
          name : 'id_actividad_economica',
          fieldLabel : 'Actividad Economica',
          allowBlank : false,
          emptyText : 'Actividad...',
					listWidth:'500',
					//anchor: '100%',
					width:200,
          store : new Ext.data.JsonStore({
              url : '../../sis_ventas_facturacion/control/ActividadEconomica/listarActividadEconomica',
              id : 'id_actividad_economica',
              root : 'datos',
              sortInfo : {
                  field : 'codigo',
                  direction : 'ASC'
              },
              totalProperty : 'total',
              fields : ['id_actividad_economica', 'nombre', 'codigo', 'descripcion'],
              remoteSort : true,
              baseParams : {
                  par_filtro : 'acteco.codigo#acteco.nombre'
              }
          }),
          valueField : 'id_actividad_economica',
          displayField : 'nombre',
          gdisplayField : 'nombre_actividad',
          hiddenName : 'id_actividad_economica',
          forceSelection : true,
          typeAhead : false,
					tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:red;"><b style="color:black;">Codigo:</b> {codigo}</p><p style="color:blue;"><b style="color:black;">Nombre:</b> {nombre}</p><p style="color:green;"><b style="color:black;">Descripción:</b> {descripcion}</p> </div></tpl>',
          triggerAction : 'all',
          lazyRender : true,
          mode : 'remote',
          pageSize : 10,
          queryDelay : 1000,
          gwidth : 170,
          minChars : 2,
          renderer:function(value, p, record){
						return String.format('{0}', record.data['nombre_actividad']);}
      },
	      type : 'ComboBox',
	      id_grupo : 2,
	      filters:{pfiltro:'acteco.nombre',type:'string'},
	      form : true,
	      grid:true
	  },
    {
			config:{
				name: 'movimiento',
				fieldLabel: 'Movimiento',
				allowBlank: true,
				//anchor: '100%',
				width:200,
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'ingas.movimiento',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
				config: {
						name: 'id_moneda',
						origen: 'MONEDA',
						allowBlank: false,
						fieldLabel: 'Moneda Deposito',
						gdisplayField: 'desc_moneda',//mapea al store del grid
						//anchor:'100%',
						width:200,
						gwidth: 100,
						renderer: function (value, p, record) {
								return String.format('{0}', record.data['desc_moneda']);
						}
				},
				type: 'ComboRec',
				id_grupo: 1,
				filters: {
						pfiltro: 'mon.codigo',
						type: 'string'
				},
				grid: true,
				form: true
		},
		{
			config:{
				name: 'precio',
				fieldLabel: 'Precio',
				allowBlank: true,
				//anchor: '100%',
				width:200,
				allowDecimals:true,
				decimalPrecision:2,
				renderer:function (value,p,record){
						return  String.format('<div style="text-align:right;">{0}</div>', Ext.util.Format.number(value,'0,000.00'));
					},
				gwidth: 100,
				maxLength:20,
				allowNegative:false,
			},
				type:'NumberField',
				filters:{pfiltro:'ingas.precio',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
          config:{
            name:'sw_tes',
            fieldLabel:'Habilitar en Tesoreria',
            allowBlank:false,
						//anchor: '100%',
						width:200,
            emptyText:'Tipo...',
            typeAhead: true,
              triggerAction: 'all',
              lazyRender:true,
              mode: 'local',
              gwidth: 100,
              store:new Ext.data.ArrayStore({
            fields: ['ID', 'valor'],
            data :	[[1,'si'],
                [2,'no']]

          }),
        valueField:'ID',
        displayField:'valor',
        renderer:function (value, p, record){
						if (value == 1) {
							return 'si'
						} else if (value == 2) {
							return 'no'
						}
						else {
							return ''
						}
				}
          },
          type:'ComboBox',
          id_grupo:0,
          grid:true,
          form:true
       },
    {
     config: {
       name: 'activo_fijo',
       fieldLabel: 'Activo Fijo?',
      // anchor: '100%',
			 width:200,
       tinit: false,
       allowBlank: false,
       origen: 'CATALOGO',
       gdisplayField: 'activo_fijo',
       gwidth: 100,
       baseParams:{
           cod_subsistema:'PARAM',
           catalogo_tipo:'tgral__bandera_min'
       },
       renderer:function (value, p, record){return String.format('{0}', record.data['activo_fijo']);}
     },
     type: 'ComboRec',
     id_grupo: 0,
     filters:{pfiltro:'conig.activo_fijo',type:'string'},
     grid: true,
     form: true
   },
   {
     config: {
       name: 'almacenable',
       fieldLabel: 'Almacenable?',
       //anchor: '100%',
			 width:200,
       tinit: false,
       allowBlank: false,
       origen: 'CATALOGO',
       gdisplayField: 'almacenable',
       gwidth: 100,
       baseParams:{
           cod_subsistema:'PARAM',
           catalogo_tipo:'tgral__bandera_min'
       },
       renderer:function (value, p, record){return String.format('{0}', record.data['almacenable']);}
     },
     type: 'ComboRec',
     id_grupo: 0,
     filters:{pfiltro:'conig.almacenable',type:'string'},
     grid: true,
     form: true
   },
	 {
			 config:{
					 name: 'requiere_descripcion',
					 fieldLabel: 'Requiere Descripción',
					 allowBlank: false,
					 //anchor: '100%',
					 width:200,
					 gwidth: 130,
					 maxLength:2,
					 emptyText:'si/no...',
					 typeAhead: true,
					 triggerAction: 'all',
					 lazyRender:true,
					 mode: 'local',
					// displayField: 'descestilo',
					 store:['si','no']
			 },
			 type:'ComboBox',
			 //filters:{pfiltro:'promac.inicio',type:'string'},
			 id_grupo:2,
			 filters:{
										type: 'list',
										pfiltro:'sprod.requiere_descripcion',
										options: ['si','no'],
							 },
			 grid:true,
			 form:true
	 },
	 {
			 config:{
					 name: 'excento',
					 fieldLabel: 'Tiene Excento',
					 allowBlank: false,
					 //anchor: '100%',
					 width:200,
					 gwidth: 130,
					 maxLength:2,
					 emptyText:'si/no...',
					 typeAhead: true,
					 triggerAction: 'all',
					 lazyRender:true,
					 mode: 'local',
					// displayField: 'descestilo',
					 store:['si','no']
			 },
			 type:'ComboBox',
			 //filters:{pfiltro:'promac.inicio',type:'string'},
			 id_grupo:2,
			 filters:{
										type: 'list',
										pfiltro:'sprod.excento',
										options: ['si','no'],
							 },
			 grid:true,
			 form:true
	 },
	 {
		 config: {
			 name: 'punto_venta_asociado',
			 fieldLabel: 'Punto de Venta',
			 allowBlank: true,
			 emptyText: 'Elija su punto de venta...',
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
			 gdisplayField: 'nombres_punto_venta',
			 //hiddenName: 'id_punto_venta',
			 forceSelection: true,
			 typeAhead: true,
			 triggerAction: 'all',
			 lazyRender: true,
			 mode: 'remote',
			 pageSize: 15,
			 queryDelay: 1000,
			 //anchor: '100%',
			 width:200,
			 gwidth: 150,
			 minChars: 2,
			 enableMultiSelect:true,
			 itemSelector: 'div.awesomecombo-5item',
			 tpl:'<tpl for="."><div class="awesomecombo-5item {checked}"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
		 },
		 type: 'AwesomeCombo',
		 id_grupo: 0,
		 grid: true,
		 form: true
	 },
	 {
			 config:{
				 name: 'sw_autorizacion',
				 fieldLabel: 'Autorizaciones',
				 allowBlank: true,
				 anchor: '80%',
				 gwidth: 200,
				 maxLength:500
			 },
			 type:'TextArea',
			 filters: {pfiltro:'conig.sw_autorizacion', type:'string'},

			 id_grupo:1,
			 grid:true,
			 form:false
			},
			{
			config:{
				name: 'regionales',
				fieldLabel: 'Regionales',
				allowBlank: true,
				anchor: '80%',
				gwidth: 200,
				maxLength:500
			},
			type:'TextArea',
			filters: {pfiltro:'conig.regionales', type:'string'},

			id_grupo:1,
			grid:true,
			form:false
		 },
		 {
		 config:{
			 name: 'nivel_permiso',
			 fieldLabel: 'Nivel Permiso',
			 allowBlank: true,
			 anchor: '80%',
			 gwidth: 200,
			 maxLength:500
		 },
		 type:'TextArea',
		 filters: {pfiltro:'conig.nivel_permiso', type:'string'},

		 id_grupo:1,
		 grid:true,
		 form:false
		},
		{
			config:{
				name: 'usr_reg',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu1.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
    {
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'ingas.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},

		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'ingas.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'ingas.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:300
			},
				type:'TextField',
				filters:{pfiltro:'ingas.usuario_ai',type:'string'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'usr_mod',
				fieldLabel: 'Modificado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu2.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'ingas.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	fheight:600,
  fwidth:450,
	title:'Ingresos gastos',
	ActSave:'../../sis_ventas_facturacion/control/Servicios/insertarServicios',
	ActDel:'../../sis_ventas_facturacion/control/Servicios/eliminarServicios',
	ActList:'../../sis_ventas_facturacion/control/Servicios/listarServicios',
	id_store:'id_concepto_ingas',
	fields: [
		{name:'id_concepto_ingas', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'tipo', type: 'string'},
		{name:'desc_ingas', type: 'string'},
		{name:'movimiento', type: 'string'},
		{name:'sw_tes', type: 'string'},
		{name:'activo_fijo', type: 'string'},
		{name:'almacenable', type: 'string'},
		{name:'codigo', type: 'string'},
		{name:'id_cat_concepto', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'punto_venta_asociado', type: 'string'},
		{name:'id_moneda', type: 'numeric'},
		{name:'precio', type: 'numeric'},
		{name: 'desc_moneda', type: 'string'},
		{name: 'requiere_descripcion', type: 'string'},
		{name: 'excento', type: 'string'},
		{name: 'id_actividad_economica', type: 'numeric'},
		{name: 'nombre_actividad', type: 'string'},
		{name: 'nombres_punto_venta', type: 'string'},
		'sw_autorizacion','regionales','nivel_permiso'
	],
	sortInfo:{
		field: 'id_concepto_ingas',
		direction: 'DESC'
	},
	bdel:true,
	bsave:true
	}
)
</script>
