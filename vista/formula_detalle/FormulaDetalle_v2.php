<?php
/**
*@package pXP
*@file gen-.php
*@author  (ivaldivia)
*@date 18-09-2019 21:05:00
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.FormulaDetalle_v2=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		that=this;
    	//llama al constructor de la clase padre
		Phx.vista.FormulaDetalle_v2.superclass.constructor.call(this,config);
		this.init();
		this.iniciarEventos(that);
		/*Fondo color tbar (IRVA)*/
		this.bbar.el.dom.style.background='linear-gradient(135deg, #8ec6ff 10%,#5da4ea 31%,#5da4ea 70%,#8ec6ff 88%)';
		this.tbar.el.dom.style.background='linear-gradient(135deg, #8ec6ff 10%,#5da4ea 31%,#5da4ea 70%,#8ec6ff 88%)';
		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#E7FAFF';
		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#A7DBFF';
		/************************/
		//this.load({params:{start:0, limit:this.tam_pag}})
	},

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_formula_detalle'
			},
			type:'Field',
			form:true
		},
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
		// {
		// 	config:{
		// 		name: 'cantidad',
		// 		fieldLabel: 'cantidad',
		// 		allowBlank: false,
		// 		anchor: '80%',
		// 		gwidth: 100,
		// 		maxLength:1179654
		// 	},
		// 		type:'NumberField',
		// 		filters:{pfiltro:'detforv2.cantidad',type:'numeric'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
		// {
		// 	config:{
		// 		name: 'tipo',
		// 		fieldLabel: 'Tipo detalle',
		// 		allowBlank:false,
		// 		width:200,
		// 		emptyText:'Tipo...',
		// 		typeAhead: true,
		// 		triggerAction: 'all',
		// 		lazyRender:true,
		// 		mode: 'local',
		// 		gwidth: 150,
		// 		store:['Producto','Servicio']
		// 	},
		// 		type:'ComboBox',
		// 		filters:{pfiltro:'factdet.tipo',type:'string'},
		// 		id_grupo:1,
		// 		grid:false,
		// 		form:true
		// },
		{
			config: {
				name: 'id_concepto_ingas',
				fieldLabel: 'Producto/Servicio',
				allowBlank: false,
				emptyText: 'Productos...',
				store: new Ext.data.JsonStore({
						url: '../../sis_ventas_facturacion/control/Servicios/listarServiciosPaquetes',
					//	url: '../../sis_ventas_facturacion/control/Servicios/listarServicios',
						id: 'id_producto',
						root: 'datos',
						sortInfo: {
								field: 'id_concepto_ingas',
								direction: 'DESC'
						},
						totalProperty: 'total',
						fields: ['id_concepto_ingas', 'tipo','desc_ingas','desc_moneda','requiere_descripcion','precio','excento','contabilizable'],
						remoteSort: true,
						baseParams: {par_filtro: 'ingas.desc_ingas', servicios_productos:'SI'}
				}),
				valueField: 'id_concepto_ingas',
				displayField: 'desc_ingas',
				gdisplayField: 'desc_ingas',
				hiddenName: 'id_concepto_ingas',
				forceSelection: true,
				tpl: new Ext.XTemplate([
					 '<tpl for=".">',
					 '<div class="x-combo-list-item">',
					 '<p><b>Nombre:</b><span style="color: green; font-weight:bold;"> {desc_ingas}</span></p></p>',
					 '<p><b>Moneda:</b> <span style="color: blue; font-weight:bold;">{desc_moneda}</span></p>',
					 '<p><b>Precio:</b> <span style="color: blue; font-weight:bold;">{precio}</span></p>',
					 '<p><b>Tiene Excento:</b> <span style="color: red; font-weight:bold;">{excento}</span></p>',
					 '<p><b>Requiere Descripción:</b> <span style="color: red; font-weight:bold;">{requiere_descripcion}</span></p>',
					 '<p><b>Contabilizable:</b> <span style="color: red; font-weight:bold;">{contabilizable}</span></p>',
					 '</div></tpl>'
				 ]),
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				resizable:true,
				pageSize: 15,
				queryDelay: 1000,
				width : 200,
				listWidth:'450',
				minChars: 2 ,
				gwidth:300,
				listeners: {
					  beforequery: function(qe){
						delete qe.combo.lastQuery;
					}
				}
				//disabled:true,
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'movtip.nombre',type: 'string'},
			grid: true,
			form: true
		},
		{
			config:{
				name: 'precio',
				fieldLabel: 'Precio',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179654
			},
				type:'NumberField',
				filters:{pfiltro:'detforv2.cantidad',type:'numeric'},
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
				filters:{pfiltro:'detforv2.estado_reg',type:'string'},
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
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'detforv2.fecha_reg',type:'date'},
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
				filters:{pfiltro:'detforv2.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'detforv2.usuario_ai',type:'string'},
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
				filters:{pfiltro:'detforv2.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	fheight:200,
  fwidth:400,
	title:'Detalle de formula',
	ActSave:'../../sis_ventas_facturacion/control/FormulaDetalle_v2/insertarFormulaDetalle',
	ActDel:'../../sis_ventas_facturacion/control/FormulaDetalle_v2/eliminarFormulaDetalle',
	ActList:'../../sis_ventas_facturacion/control/FormulaDetalle_v2/listarFormulaDetalle',
	id_store:'id_formula_detalle',
	fields: [
		{name:'id_formula_detalle', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
	//	{name:'cantidad', type: 'numeric'},
		{name:'id_item', type: 'numeric'},
		{name:'id_formula', type: 'numeric'},
		{name:'id_concepto_ingas', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'desc_ingas', type: 'string'},
		{name:'precio', type: 'numeric'},

	],
	sortInfo:{
		field: 'id_formula_detalle',
		direction: 'ASC'
	},
	bdel:true,
	bsave:false,

	onReloadPage: function(m){
		this.maestro=m;
		this.store.baseParams={id_formula:this.maestro.id_formula};
		this.load({params: {start: 0, limit: 50}});
	},

	loadValoresIniciales: function(){
		Phx.vista.FormulaDetalle_v2.superclass.loadValoresIniciales.call(this);
	},

	iniciarEventos: function(that){
		// this.Cmp.tipo.on('select',function(c,r,i){
		// 	that.capturaFiltros();
		// });
		// this.Cmp.tipo.reset();
	},

	capturaFiltros: function (combo, record, index) {
		//this.Cmp.id_concepto_ingas.store.baseParams.tipo_serv = this.Cmp.tipo.getValue();
	},

	onButtonNew: function(){
		Phx.vista.FormulaDetalle_v2.superclass.onButtonNew.call(this);
		this.Cmp.id_formula.setValue(this.maestro.id_formula);
		this.Cmp.id_concepto_ingas.store.baseParams.id_formula = this.maestro.id_formula;
		this.form.el.dom.firstChild.childNodes[0].style.background = 'linear-gradient(135deg, #8ec6ff 10%,#5da4ea 31%,#5da4ea 70%,#8ec6ff 88%)';
	}


	}
)
</script>
