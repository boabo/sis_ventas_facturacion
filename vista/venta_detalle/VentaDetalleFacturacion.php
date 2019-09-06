<?php
/**
*@package pXP
*@file gen-VentaDetalleFacturacion.php
*@author  (ivaldivia)
*@date 10-05-2019 19:33:22
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.VentaDetalleFacturacion=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;

		Ext.Ajax.request({
				url:'../../sis_ventas_facturacion/control/Venta/getVariablesBasicas',
				params: {'prueba':'uno'},
				success:this.successGetVariables,
				failure: this.conexionFailure,
				arguments:config,
				timeout:this.timeout,
				scope:this
		});



	},

	successGetVariables : function (response,request) {

			var respuesta = JSON.parse(response.responseText);
			if('datos' in respuesta){
					this.variables_globales = respuesta.datos;
			}

		Phx.vista.VentaDetalleFacturacion.superclass.constructor.call(this,request.arguments);
		this.init();
		//this.Cmp.id_producto.store.baseParams.tipo='producto_servicio';
		this.bbar.el.dom.style.background='linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%)';
		this.tbar.el.dom.style.background='linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%)'



	},

	onButtonNew:function () {
		Phx.vista.VentaDetalleFacturacion.superclass.onButtonNew.call(this);
		this.window.items.items[0].body.dom.style.background = 'linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%)';
		this.Cmp.descripcion.setVisible(false);
		this.Cmp.excento.setVisible(false);
		this.Cmp.tipo.setValue('servicio');
		this.Cmp.tipo.fireEvent('select', this.Cmp.tipo,'servicio',0);

		this.Cmp.id_venta.setValue(this.maestro.id_venta);

		this.Cmp.tipo.on('select',function(c,r,i) {
						this.cambiarCombo(r.data.field1);
		},this);



		this.comboCambio();

		this.Cmp.id_producto.on('select',function(c,r,i) {
			this.Cmp.precio.setValue(parseFloat(r.data.precio));
			this.Cmp.cantidad.setValue(1);
			this.Cmp.total.setValue(parseFloat(r.data.precio*1));
			if (r.data.requiere_descripcion == 'si') {
					this.Cmp.descripcion.setVisible(true);
					this.Cmp.descripcion.allowBlank = false;
			} else {
				this.Cmp.descripcion.allowBlank = true;
				this.Cmp.descripcion.setVisible(false);
			}

			if (r.data.excento == 'si') {
					this.Cmp.excento.setVisible(true);
					this.Cmp.excento.allowBlank = false;
			} else {
				this.Cmp.excento.allowBlank = true;
				this.Cmp.excento.setVisible(false);
			}

		},this);


		this.Cmp.precio.on('change',function(field,newValue,oldValue){
			this.Cmp.total.setValue(parseFloat(this.Cmp.precio.getValue()*this.Cmp.cantidad.getValue()));
		},this);//monto_forma_pago

		this.Cmp.cantidad.on('change',function(field,newValue,oldValue){
			this.Cmp.total.setValue(parseFloat(this.Cmp.cantidad.getValue()*this.Cmp.precio.getValue()));
		},this);



	},

	onButtonEdit:function () {
		Phx.vista.VentaDetalleFacturacion.superclass.onButtonEdit.call(this);
		this.window.items.items[0].body.dom.style.background = 'linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%)';
		this.Cmp.tipo.setValue('servicio');
		this.Cmp.tipo.fireEvent('select', this.Cmp.tipo,'servicio',0);


		this.Cmp.id_venta.setValue(this.store.baseParams.id_venta);
		this.Cmp.tipo.on('select',function(c,r,i) {
						this.cambiarCombo(r.data.field1);
		},this);


		this.comboCambio();

		this.Cmp.id_producto.on('select',function(c,r,i) {
			this.Cmp.funcion.setValue('editar');
			this.Cmp.precio.setValue(parseFloat(r.data.precio));
			this.Cmp.cantidad.setValue(1);
			this.Cmp.total.setValue(parseFloat(r.data.precio*1));
		},this);


		this.Cmp.precio.on('change',function(field,newValue,oldValue){
			this.Cmp.total.setValue(parseFloat(this.Cmp.precio.getValue()*this.Cmp.cantidad.getValue()));
		},this);//monto_forma_pago

		this.Cmp.cantidad.on('change',function(field,newValue,oldValue){
			this.Cmp.total.setValue(parseFloat(this.Cmp.cantidad.getValue()*this.Cmp.precio.getValue()));
		},this);


	},


	cambiarCombo : function (tipo) {
		this.Cmp.id_producto.store.baseParams.tipo=tipo;
		this.comboCambio(tipo);
	},

	comboCambio : function (tipo){
			if (tipo == undefined) {
				this.Cmp.id_producto.store.baseParams.tipo='servicio';
			} else {
				this.Cmp.id_producto.store.baseParams.tipo=tipo;
			}
	},

	successSave:function(resp){
			Phx.vista.VentaDetalleFacturacion.superclass.successSave.call(this,resp);
			Phx.CP.getPagina(this.idContenedorPadre).reload();
	},
	

	loadMask :false,
	Atributos:[

		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_venta_detalle'
			},
			type:'Field',
			form:true
		},
		{
			config:{
				name: 'id_venta',
				fieldLabel: 'id_venta',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10,
				hidden:true,
			},
				type:'TextField',
				filters:{pfiltro:'factdet.id_venta',type:'string'},
				id_grupo:1,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'funcion',
				fieldLabel: 'funcion',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10,
				hidden:true,
			},
				type:'TextField',
				filters:{pfiltro:'factdet.funcion',type:'string'},
				id_grupo:1,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'tipo',
				fieldLabel: 'Tipo detalle',
				allowBlank:false,
				anchor: '80%',
				emptyText:'Tipo...',
				typeAhead: true,
				triggerAction: 'all',
				lazyRender:true,
				mode: 'local',
				gwidth: 150,
				store:['producto','servicio']
			},
				type:'ComboBox',
				filters:{pfiltro:'factdet.tipo',type:'string'},
				id_grupo:1,
				grid:false,
				form:true
		},
		{
			config: {
				name: 'id_producto',
				fieldLabel: 'Producto/Servicio',
				allowBlank: true,
				anchor: '80%',
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_ventas_facturacion/control/SucursalProducto/listarProductoServicioItem',
					id: 'id_producto',
					root: 'datos',
					sortInfo: {
						field: 'nombre',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_producto', 'tipo','nombre_producto','descripcion','medico','requiere_descripcion','precio','ruta_foto','codigo_unidad_medida','excento'],
					remoteSort: true,
					baseParams: {par_filtro: 'todo.nombre'}
				}),
				valueField: 'id_producto',
				displayField: 'nombre_producto',
				gdisplayField: 'nombre_producto',
				hiddenName: 'id_producto',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				listWidth:'450',
				gwidth: 300,
				minChars: 2,
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div><b>{0}</b></div>', Ext.util.Format.number(value));
					}
					else{
							return '<b><p style="font-size:15px; color:blue; font-weight:bold; text-align:right;">Venta Total: &nbsp;&nbsp; </p></b>';
					}
				},
				listeners: {
					beforequery: function(qe){
						delete qe.combo.lastQuery;
					}
				},
			tpl: new Ext.XTemplate([
				'<tpl for=".">',
				'<div class="x-combo-list-item">',
				'<p><b>Nombre:</b><span style="color: green; font-weight:bold;"> {nombre_producto}</span></p></p>',
				'<p><b>Descripcion:</b> <span style="color: blue; font-weight:bold;">{descripcion}</span></p>',
				'<p><b>Precio:</b> <span style="color: blue; font-weight:bold;">{precio}</span></p>',
				'<p><b>Tiene Excento:</b> <span style="color: red; font-weight:bold;">{excento}</span></p>',
				'<p><b>Requiere Descripción:</b> <span style="color: red; font-weight:bold;">{requiere_descripcion}</span></p>',
				'</div></tpl>'
			]),
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'movtip.nombre',type: 'string'},
			grid: true,
			form: true

		},
		{
			config:{
				name: 'cantidad',
				fieldLabel: 'Cantidad',
				allowBlank: false,
				disabled: false,
				anchor: '80%',
				gwidth: 100,
				galign: 'right',
				selectOnFocus: true,
				decimalPrecision:0,
				renderer:function (value,p,record){
								return  String.format('<div style="font-size:12px; color:#58009D; font-weight:bold; text-align:right;">{0}</div>', record.data['cantidad']);

				},
				//maxLength:-5
			},
				type:'NumberField',
				filters:{pfiltro:'factdet.cantidad',type:'numeric'},
				id_grupo:1,
				egrid:true,
				grid:true,
				form:true

		},
		{
			config:{
				name: 'precio',
				fieldLabel: 'Precio Unitario',
				allowBlank: false,
				anchor: '80%',
				gwidth: 120,
				galign:'right',
				selectOnFocus: true,
				maxLength:1179654,
				style:{
					width: '200px'
				},
				renderer:function (value,p,record){
					return  String.format('<div style="font-size:12px; color:green; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
				},
				decimalPrecision:2
			},
				type:'NumberField',
				filters:{pfiltro:'factdet.precio',type:'numeric'},
				id_grupo:1,
				egrid:true,
				grid:true,
				form:true

		},
		{
			config:{
				name: 'total',
				fieldLabel: 'Total',
				allowBlank: true,
				anchor: '80%',
				gwidth: 150,
				decimalPrecision:2,
				maxLength:100,
				readOnly :true,
				galign:'right',
				style:{
					width: '200px'
				},
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div style="font-size:12px; color:blue; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
					}

					else{
						return  String.format('<div style="font-size:15px; text-align:right; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.venta_total,'0,000.00'));
					}
				}
			},
				type:'NumberField',
				filters:{pfiltro:'factdet.total',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'excento',
				fieldLabel: 'Valor Excento',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650,
				galign:'right',
				renderer:function (value,p,record){
				  if (record.data['tiene_excento'] == 'no') {
						return  String.format('<div style="font-size:12px; color:red; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(0,'0,000.00'));
					} else {
						return  String.format('<div style="font-size:12px; color:red; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
					}
				}
			},
				type:'NumberField',
				filters:{pfiltro:'ven.excento',type:'numeric'},
				id_grupo:1,
				egrid:true,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'tiene_excento',
				fieldLabel: 'Tiene Excento?',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650,
				galign:'center',
				renderer:function (value,p,record){
				return  String.format('<div style="font-size:12px; color:red; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value));
			}
			},
				type:'TextField',
				filters:{pfiltro:'pro.excento',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'descripcion',
				fieldLabel: 'Descripcion',
				allowBlank: true,
				gwidth: 200,
				anchor: '80%',
				//maxLength:-5
			},
				type:'TextArea',
				filters:{pfiltro:'factdet.descripcion',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
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
				filters:{pfiltro:'factdet.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},

		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: '',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'factdet.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'factdet.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
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
				filters:{pfiltro:'factdet.usuario_ai',type:'string'},
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
				filters:{pfiltro:'factdet.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	title:'<center style="font-size:25px; color:#0E00B7; text-shadow: -1px -1px 1px rgba(255,255,255,.1), 1px 1px 1px rgba(0,0,0,.5);"><i style="color:#FF7400;" class="fa fa-list-ol" aria-hidden="true"></i> Detalle Venta</center>',
	fheight:'45%',
	fwidth:'30%',
	ActSave:'../../sis_ventas_facturacion/control/VentaDetalleFacturacion/insertarVentaDetalleFacturacion',
	ActDel:'../../sis_ventas_facturacion/control/VentaDetalleFacturacion/eliminarVentaDetalleFacturacion',
	ActList:'../../sis_ventas_facturacion/control/VentaDetalleFacturacion/listarVentaDetalleFacturacion',
	id_store:'id_venta_detalle',
	fields: [
		{name:'id_venta_detalle', type: 'numeric'},
		{name:'id_formula', type: 'numeric'},
		{name:'id_item', type: 'numeric'},
		{name:'id_medico', type: 'numeric'},
		{name:'id_sucursal_producto', type: 'numeric'},
		{name:'id_vendedor', type: 'numeric'},
		{name:'id_venta', type: 'numeric'},
		{name:'porcentaje_descuento', type: 'numeric'},
		{name:'descripcion', type: 'string'},
		{name:'id_boleto', type: 'numeric'},
		{name:'estado', type: 'string'},
		{name:'obs', type: 'string'},
		{name:'id_unidad_medida', type: 'numeric'},
		{name:'cantidad', type: 'numeric'},
		{name:'tipo', type: 'string'},
		{name:'bruto', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'id_producto', type: 'numeric'},
		{name:'tipo_reg', type: 'string'},
		{name:'serie', type: 'string'},
		{name:'precio', type: 'numeric'},
		{name:'precio_sin_descuento', type: 'numeric'},
		{name:'kg_fino', type: 'string'},
		{name:'ley', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'nombre_producto', type: 'string'},
		{name:'total', type: 'numeric'},
		{name:'venta_total', type: 'numeric'},
		{name:'excento', type: 'numeric'},
		{name:'tiene_excento', type: 'string'},

	],
	sortInfo:{
		field: 'id_venta_detalle',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true,
	bedit:false,

	onReloadPage: function(m){
		this.maestro=m;
		this.store.baseParams={id_venta:this.maestro.id_venta};
		this.Cmp.id_producto.store.baseParams.id_sucursal=this.maestro.id_sucursal;
		this.Cmp.id_producto.store.baseParams.id_punto_venta=this.maestro.id_punto_venta;
		this.Cmp.cantidad.setDisabled(true);
		this.load({params: {start: 0, limit: 50}});
	},

	loadValoresIniciales: function(){
		this.Cmp.id_venta.setValue(this.maestro.id_venta);
		Phx.vista.VentaDetalleFacturacion.superclass.loadValoresIniciales.call(this);
	}

	}
)
</script>
