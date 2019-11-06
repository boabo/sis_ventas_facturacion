<?php
/**
*@package pXP
*@file gen-AsociarBoletos.php
*@author  (ivaldivia)
*@date 15-08-2019 13:15:22
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/
 
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.AsociarBoletos=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		Ext.apply(this,config);
		Phx.vista.AsociarBoletos.superclass.constructor.call(this,config);
		this.init();
		this.bbar.el.dom.style.background='#7FB3D5';
		this.tbar.el.dom.style.background='#7FB3D5';
		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#EBF5FB';
		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#AED6F1';

    this.store.baseParams.id_venta_factura = this.maestro.id_venta;

		this.load({params:{start:0, limit:this.tam_pag}})
	},


  onButtonNew : function () {
      Phx.vista.AsociarBoletos.superclass.onButtonNew.call(this);
      this.form.el.dom.firstChild.childNodes[0].style.background = '#7FB3D5';
      this.Cmp.id_venta.setValue(this.maestro.id_venta);

  },

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_venta'
			},
			type:'Field',
			form:true
		},
    {
        //configuracion del componente
        config: {
            labelSeparator: '',
            inputType: 'hidden',
            name: 'id_boleto_asociado'
        },
        type: 'Field',
        form: true
    },
		// {
		// 	config: {
		// 		name: 'id_boleto',
		// 		fieldLabel: '<b style="font-size:12px;"><i style="vertical-align: middle; font-size:20px; color:red;" class="fa fa-th-list" aria-hidden="true"></i> Nro. Boleto</b>',
		// 		allowBlank: true,
    //     enableMultiSelect: true,
		// 		emptyText: 'Elija los boletos para Asociar...',
		// 		store: new Ext.data.JsonStore({
		// 			url: '../../sis_ventas_facturacion/control/VentaFacturacion/listarAsociarBoletos',
		// 			id: 'id_boleto',
		// 			root: 'datos',
		// 			sortInfo: {
		// 				field: 'id_boleto',
		// 				direction: 'ASC'
		// 			},
		// 			totalProperty: 'total',
		// 			fields: ['id_boleto', 'nro_boleto', 'estado_reg','pasajero', 'fecha_emision', 'nit','ruta','razon'],
		// 			remoteSort: true,
		// 			baseParams: {par_filtro: 'bol.nro_boleto#bol.fecha_emision#bol.nit#bol.razon'}
		// 		}),
		// 		valueField: 'id_boleto',
		// 		displayField: 'nro_boleto',
		// 		gdisplayField: 'nro_boleto',
		// 		hiddenName: 'id_boleto',
		// 		forceSelection: false,
		// 		typeAhead: false,
		// 		triggerAction: 'all',
		// 		lazyRender: true,
		// 		mode: 'remote',
		// 		pageSize: 15,
		// 		queryDelay: 1000,
		// 		//anchor: '80%',
    //     width:300,
    //     listWidth:'450',
		// 		gwidth: 150,
		// 		minChars: 2,
    //     style:{
    //       background:'#EBFFF9',
    //     },
    //     itemSelector: 'div.awesomecombo-5item',
    //     tpl: new Ext.XTemplate([
    //       '<tpl for=".">',
    //       '<div class="awesomecombo-5item {checked}">',
		// 			'<p><b>Nro.Boleto: {nro_boleto}</b></p>',
		// 			'<p><b>Fecha Emisión:</b> <span style="color: green;">{fecha_emision}</span></p>',
    //       '<p><b>Cliente:</b> <span style="color: red;">{pasajero}</span></p>',
    //       '<p><b>Razón Social:</b> <span style="color: red;">{razon}</span></p>',
    //       '<p><b>NIT:</b> <span style="color: blue;">{nit}</span></p>',
    //       '<p><b>RUTA:</b> <span style="color: blue;">{ruta}</span></p>',
    //       '<p><b>Estado Boleto:</b> <span style="color: green;">{estado_reg}</span></p>',
    //       '</div></tpl>'
    //     ]),
		// 		listeners: {
		// 				beforequery: function(qe){
		// 				delete qe.combo.lastQuery;
		// 			}
		// 		},
		// 		renderer : function(value, p, record) {
		// 			return String.format('{0}', record.data['nro_boleto']);
		// 		}
		// 	},
		// 	type: 'AwesomeCombo',
		// 	id_grupo: 0,
		// 	filters: {pfiltro: 'bol.nro_boleto#bol.fecha_emision#bol.nit#bol.razon',type: 'string'},
		// 	grid: true,
		// 	form: true
		// },
		{
			config:{
				name: 'nro_boleto',
				fieldLabel: '<b style="font-size:12px;"><i style="vertical-align: middle; font-size:20px; color:red;" class="fa fa-th-list" aria-hidden="true"></i> Nro. Boleto</b>',
				allowBlank: true,
				anchor: '80%',
				gwidth: 300,
				maxLength:13
			},
				type:'TextField',
				filters:{pfiltro:'bol.pasajero',type:'string'},
				id_grupo:1,
				valorInicial:'930',
				grid:true,
				form:true
		},
    {
			config:{
				name: 'pasajero',
				fieldLabel: '<b style="font-size:12px;"><i style="vertical-align: middle; font-size:20px; color:green;" class="fa fa-user" aria-hidden="true"></i> Pasajero</b>',
				allowBlank: true,
				anchor: '80%',
				gwidth: 300,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'bol.pasajero',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
    {
			config:{
				name: 'nit',
				fieldLabel: '<b style="font-size:12px;"><i style="vertical-align: middle; font-size:20px; color:blue;" class="fa fa-tag" aria-hidden="true"></i> NIT</b>',
				allowBlank: true,
				anchor: '80%',
				gwidth: 150,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'bol.nit',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
    {
			config:{
				name: 'razon',
				fieldLabel: '<b style="font-size:12px;"><i style="vertical-align: middle; font-size:20px; color:#FF651D;" class="fa fa-pencil-square-o" aria-hidden="true"></i> Razón</b>',
				allowBlank: true,
				anchor: '80%',
				gwidth: 300,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'bol.pasajero',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
    {
			config:{
				name: 'ruta',
				fieldLabel: '<b style="font-size:12px;"><i style="vertical-align: middle; font-size:20px; color:#0E27FF;" class="fa fa-plane" aria-hidden="true"></i> Ruta</b>',
				allowBlank: true,
				anchor: '80%',
				gwidth: 90,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'bol.pasajero',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
    {
			config:{
				name: 'estado_reg',
				fieldLabel: '<b style="font-size:12px;"><i style="vertical-align: middle; font-size:20px; color:#369B2C;" class="fa fa-thumbs-up" aria-hidden="true"></i> Estado Reg.</b>',
				allowBlank: true,
				anchor: '80%',
				gwidth: 200,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'acca.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usr_reg',
				fieldLabel: '<b style="font-size:12px;"><i style="vertical-align: middle; font-size:20px; color:#8475B4;" class="fa fa-users" aria-hidden="true"></i> Creado por</b>',
				allowBlank: true,
				anchor: '80%',
				gwidth: 200,
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
				fieldLabel: '<b style="font-size:12px;"><i style="vertical-align: middle; font-size:20px; color:#BD7000;" class="fa fa-calendar" aria-hidden="true"></i> Fecha creación</b>',
				allowBlank: true,
				anchor: '80%',
				gwidth: 200,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'acca.fecha_reg',type:'date'},
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
				filters:{pfiltro:'acca.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'acca.usuario_ai',type:'string'},
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
				filters:{pfiltro:'acca.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	title:'<center><h1 style="font-size:15px; color:#0E00B7; text-shadow: -1px -1px 1px rgba(255,255,255,.1), 1px 1px 1px rgba(0,0,0,.5);"> <img src="../../../lib/imagenes/icono_dibu/dibu_zoom.png" height="20px" style="float:center; vertical-align: middle;"> Asociar Boletos</h1></center>',
  fheight: 150,
  fwidth: 500,
	ActSave:'../../sis_ventas_facturacion/control/AsociarBoletos/insertarAsociarBoletos',
	ActDel:'../../sis_ventas_facturacion/control/AsociarBoletos/eliminarAsociarBoletos',
	ActList:'../../sis_ventas_facturacion/control/AsociarBoletos/listarAsociarBoletos',
	id_store:'id_boleto_asociado',
	fields: [
    {name:'id_boleto_asociado', type: 'numeric'},
    {name:'id_boleto', type: 'numeric'},
    {name:'nro_boleto', type: 'numeric'},
    {name:'pasajero', type: 'varchar'},
    {name:'nit', type: 'varchar'},
    {name:'razon', type: 'varchar'},
    {name:'ruta', type: 'varchar'},
    {name:'estado_reg', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},

	],
	sortInfo:{
		field: 'id_boleto_asociado',
		direction: 'ASC'
	},
	bdel:true,
	bsave:false,
	bedit:false,
  bexcel:false,
	btest:false,

	}
)
</script>
