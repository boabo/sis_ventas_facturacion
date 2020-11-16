<?php
/**
*@package pXP
*@file gen-Formula_v2.php
*@author  (ivaldivia)
*@date 17-09-2019 15:28:13
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.Formula_v2=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		that=this;
    	//llama al constructor de la clase padre
		Phx.vista.Formula_v2.superclass.constructor.call(this,config);
		this.init();

		/*Fondo color tbar (IRVA)*/
		this.bbar.el.dom.style.background='linear-gradient(135deg, #8ec6ff 10%,#5da4ea 31%,#5da4ea 70%,#8ec6ff 88%)';
		this.tbar.el.dom.style.background='linear-gradient(135deg, #8ec6ff 10%,#5da4ea 31%,#5da4ea 70%,#8ec6ff 88%)';
		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#E7FAFF';
		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#A7DBFF';
		/************************/
		this.load({params:{start:0, limit:this.tam_pag}});

		this.crearFormAuto();
		this.addButton('inserAuto',{ text: 'Configurar Autorizaciones', iconCls: 'blist', disabled: false, handler: this.mostarFormAuto, tooltip: '<b>Configurar autorizaciones</b><br/>Permite seleccionar desde que modulos  puede selecionarse el concepto'});


	},

	mostarFormAuto:function(){
		var data = this.getSelectedData();
		if(data){
			this.cmpAuto.setValue(data.sw_autorizacion);
			this.cmpRegionales.setValue(data.regionales);
			this.wAuto.show();
		}

	},
	saveAuto: function(){
		    var d = this.getSelectedData();
		    Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_ventas_facturacion/control/Formula_v2/editAuto',
                params: {
												sw_autorizacion: this.cmpAuto.getValue(),
												regionales: this.cmpRegionales.getValue(),
                	      id_formula: d.id_formula
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
								}
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


		},

	onButtonNew:function () {
		Phx.vista.Formula_v2.superclass.onButtonNew.call(this);
		this.form.el.dom.firstChild.childNodes[0].style.background = 'linear-gradient(135deg, #8ec6ff 10%,#5da4ea 31%,#5da4ea 70%,#8ec6ff 88%)';
	},

	onButtonEdit:function () {
		Phx.vista.Formula_v2.superclass.onButtonEdit.call(this);
		this.form.el.dom.firstChild.childNodes[0].style.background = '#84BFE7';
		var rec = this.sm.getSelected();
	},

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_formula'
			},
			type:'Field',
			form:true
		},
		{
			config:{
				name: 'nombre',
				fieldLabel: 'Nombre',
				allowBlank: false,
				width:350,
				gwidth: 400,
				maxLength:150
			},
				type:'TextField',
				filters:{pfiltro:'form.nombre',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'descripcion',
				fieldLabel: 'Descripción',
				allowBlank: true,
				width:350,
				gwidth: 300,
				maxLength:-5
			},
				type:'TextArea',
				filters:{pfiltro:'form.descripcion',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
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
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'form.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		// {
		// 	config:{
		// 		name: 'cantidad',
		// 		fieldLabel: 'cantidad',
		// 		allowBlank: true,
		// 		anchor: '80%',
		// 		gwidth: 100,
		// 		maxLength:4
		// 	},
		// 		type:'NumberField',
		// 		filters:{pfiltro:'form.cantidad',type:'numeric'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
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
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'form.fecha_reg',type:'date'},
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
				filters:{pfiltro:'form.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'form.usuario_ai',type:'string'},
				id_grupo:1,
				grid:true,
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
				filters:{pfiltro:'form.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	fheight:300,
  fwidth:550,
	title:'Fórmula',
	ActSave:'../../sis_ventas_facturacion/control/Formula_v2/insertarFormula',
	ActDel:'../../sis_ventas_facturacion/control/Formula_v2/eliminarFormula',
	ActList:'../../sis_ventas_facturacion/control/Formula_v2/listarFormula',
	id_store:'id_formula',
	fields: [
		{name:'id_formula', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'nombre', type: 'string'},
		{name:'descripcion', type: 'string'},
		//{name:'cantidad', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'nombres_punto_venta', type: 'string'},
		'sw_autorizacion','regionales'

	],
	sortInfo:{
		field: 'id_formula',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true,

	south : {
					url : '../../../sis_ventas_facturacion/vista/formula_detalle/FormulaDetalle_v2.php',
					title:'<center style="font-size:20px; color:blue;"><i style="font-size:25px;" class="fa fa-info" aria-hidden="true"></i> Detalle Fórmula</center>',
					height : '50%',
					cls : 'FormulaDetalle_v2'
	},

	}
)
</script>
