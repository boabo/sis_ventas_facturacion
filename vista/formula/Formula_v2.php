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
		this.iniciarEventos(that);
		/*Fondo color tbar (IRVA)*/
		this.bbar.el.dom.style.background='linear-gradient(135deg, #8ec6ff 10%,#5da4ea 31%,#5da4ea 70%,#8ec6ff 88%)';
		this.tbar.el.dom.style.background='linear-gradient(135deg, #8ec6ff 10%,#5da4ea 31%,#5da4ea 70%,#8ec6ff 88%)';
		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#E7FAFF';
		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#A7DBFF';
		/************************/
		this.load({params:{start:0, limit:this.tam_pag}})
	},


	onButtonNew:function () {
		Phx.vista.Formula_v2.superclass.onButtonNew.call(this);
		this.ocultarComponente(this.Cmp.punto_venta_asociado);
		this.form.el.dom.firstChild.childNodes[0].style.background = 'linear-gradient(135deg, #8ec6ff 10%,#5da4ea 31%,#5da4ea 70%,#8ec6ff 88%)';
	},

	onButtonEdit:function () {
		Phx.vista.Formula_v2.superclass.onButtonEdit.call(this);
		this.form.el.dom.firstChild.childNodes[0].style.background = '#84BFE7';
		var rec = this.sm.getSelected();
		if (rec.data.punto_venta_asociado == '') {
			this.Cmp.punto_venta_asociado.reset();
		}

		if (rec.data.tipo_punto_venta == '') {
			 this.Cmp.tipo_punto_venta.reset();
		} else{
			 this.Cmp.punto_venta_asociado.store.baseParams.tipo_pv = rec.data.tipo_punto_venta;
		}

	},

	iniciarEventos: function(that){
		this.Cmp.tipo_punto_venta.on('select',function(c,r,i){
			that.capturaFiltros();
		});
		this.Cmp.tipo_punto_venta.reset();
	},

	capturaFiltros: function (combo, record, index) {
		this.Cmp.punto_venta_asociado.store.baseParams.tipo_pv = this.Cmp.tipo_punto_venta.getValue();
		this.mostrarComponente(this.Cmp.punto_venta_asociado);
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
 				 name: 'tipo_punto_venta',
 				 fieldLabel: 'Tipo Punto de Venta',
 				 allowBlank: true,
 				 //anchor: '100%',
 				 width:350,
				 gwidth: 150,
 				 emptyText:'Tipo punto de venta...',
 				 triggerAction: 'all',
 				 lazyRender:true,
 				 mode: 'local',
 				 displayField: 'text',
 				 valueField: 'value',
 				 enableMultiSelect:true,
 				 store:new Ext.data.SimpleStore({
 				 data : [['ato', 'ATO'], ['cto', 'CTO']],
 				 id : 'value',
 				 fields : ['value', 'text']
 	 })
 			 },
 			 type:'AwesomeCombo',
 			 id_grupo:0,
 			 form:true,
 			 grid:true
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
 			 width:350,
 			 gwidth: 150,
 			 minChars: 2,
 			 enableMultiSelect:true,
			 listeners: {
					  beforequery: function(qe){
						delete qe.combo.lastQuery;
					}
				},
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
		{name:'punto_venta_asociado', type: 'string'},
		{name:'tipo_punto_venta', type: 'string'},
		{name:'nombres_punto_venta', type: 'string'},

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
