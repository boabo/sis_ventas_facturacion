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
Phx.vista.VentaFacturacionExportacionDetalle=Ext.extend(Phx.gridInterfaz,{

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

		Phx.vista.VentaFacturacionExportacionDetalle.superclass.constructor.call(this,request.arguments);
		this.init();
		//this.Cmp.id_producto.store.baseParams.tipo='producto_servicio';
		this.bbar.el.dom.style.background='radial-gradient(ellipse at center, #cfe7fa 0%,#6393c1 100%)';
		this.tbar.el.dom.style.background='radial-gradient(ellipse at center, #cfe7fa 0%,#6393c1 100%)';

		this.Cmp.descripcion.setVisible(false);
		//this.Cmp.excento.setVisible(false);
	},

	onButtonNew:function () {
		Phx.vista.VentaFacturacionExportacionDetalle.superclass.onButtonNew.call(this);
		this.window.items.items[0].body.dom.style.background = 'radial-gradient(ellipse at center, #cfe7fa 0%,#6393c1 100%)';
		this.Cmp.descripcion.setVisible(false);
		this.Cmp.tipo.setValue('Servicio');
		//this.Cmp.tipo.fireEvent('select', this.Cmp.tipo,'Servicio',0);

		this.Cmp.moneda_transaccion.setValue(this.maestro.desc_moneda);

		this.Cmp.id_venta.setValue(this.maestro.id_venta);

		this.Cmp.tipo.on('select',function(c,r,i) {
						this.cambiarCombo(r.data.field1);
		},this);



		this.comboCambio();

		this.Cmp.id_producto.on('select',function(c,r,i) {

			this.moneda_venta = this.maestro.desc_moneda;
			this.moneda_servicio = r.data.desc_moneda;


			/*Condiciones para cobrar el precio*/
			if (this.moneda_venta == this.moneda_servicio) {
					var precio = r.data.precio;
			} else if (this.moneda_venta != 'USD' && this.moneda_servicio == 'USD') {
				  var precio = (r.data.precio * this.tipo_cambio);
			} else if (this.moneda_venta == 'USD' && this.moneda_servicio != 'USD') {
					var precio = (r.data.precio / this.tipo_cambio);
			}
			/***********************************/
			//
			// if (this.moneda_servicio == 2) {
			// 	var precio = r.data.precio * this.tipo_cambio;
			// } else {
			// 	var precio = r.data.precio;
			// }


			this.Cmp.precio.setValue(parseFloat(precio));
			this.Cmp.cantidad.setValue(1);
			this.Cmp.total.setValue(parseFloat(precio*1));

			if (r.data.requiere_descripcion == 'si') {
					this.Cmp.descripcion.setVisible(true);
					this.Cmp.descripcion.allowBlank = false;
			} else {
				this.Cmp.descripcion.allowBlank = true;
				this.Cmp.descripcion.setVisible(false);
			}

			// if (r.data.excento == 'si') {
			// 		this.Cmp.excento.setVisible(true);
			// 		this.Cmp.excento.allowBlank = false;
			// } else {
			// 	this.Cmp.excento.allowBlank = true;
			// 	this.Cmp.excento.setVisible(false);
			// }
		},this);



		this.Cmp.precio.on('change',function(field,newValue,oldValue){
				var precio = (this.Cmp.precio.getValue());
			this.Cmp.total.setValue(parseFloat(precio*this.Cmp.cantidad.getValue()));
		},this);//monto_forma_pago

		this.Cmp.cantidad.on('change',function(field,newValue,oldValue){
			var precio = (this.Cmp.precio.getValue());

			this.Cmp.total.setValue(parseFloat(this.Cmp.cantidad.getValue()*precio));
		},this);



	},



	cambiarCombo : function (tipo) {
		this.Cmp.id_producto.store.baseParams.tipo_serv=tipo;
		this.comboCambio(tipo);
	},

	comboCambio : function (tipo){
			if (tipo == undefined) {
				this.Cmp.id_producto.store.baseParams.tipo='servicio';
			} else {
				this.Cmp.id_producto.store.baseParams.tipo=tipo;
			}
	},

	onButtonSave:function(){
		var rec=this.sm.getSelected();
		this.Cmp.id_moneda.setValue(rec.data.id_moneda);
		Phx.vista.VentaFacturacionExportacionDetalle.superclass.onButtonSave.call(this);
	},

	successSave:function(resp){
			Phx.vista.VentaFacturacionExportacionDetalle.superclass.successSave.call(this,resp);
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
				anchor:'100%',
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
				name: 'id_moneda',
				fieldLabel: 'id_moneda',
				allowBlank: true,
				anchor:'100%',
				gwidth: 100,
				maxLength:10,
				hidden:true,
			},
				type:'TextField',
				filters:{pfiltro:'factdet.id_moneda',type:'string'},
				id_grupo:1,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'funcion',
				fieldLabel: 'funcion',
				allowBlank: true,
				anchor:'100%',
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
				anchor:'100%',
				emptyText:'Tipo...',
				typeAhead: true,
				triggerAction: 'all',
				lazyRender:true,
				mode: 'local',
				hidden:true,
				gwidth: 150,
				store:['Producto','Servicio']
			},
				type:'ComboBox',
				filters:{pfiltro:'factdet.tipo',type:'string'},
				id_grupo:1,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'moneda_transaccion',
				fieldLabel: 'Moneda Transacción',
				allowBlank: true,
				anchor:'100%',
				gwidth: 100,
				maxLength:10,
				disabled:true
				//hidden:true,
			},
				type:'TextField',
				filters:{pfiltro:'factdet.funcion',type:'string'},
				id_grupo:1,
				grid:false,
				form:true
		},
		{
			config: {
				name: 'id_producto',
				fieldLabel: 'Concepto',
				allowBlank: false,
				emptyText: 'Conceptos...',
				anchor:'100%',
				store: new Ext.data.JsonStore({
						url: '../../sis_ventas_facturacion/control/Servicios/listarServicios',
						id: 'id_producto',
						root: 'datos',
						sortInfo: {
								field: 'desc_ingas',
								direction: 'ASC'
						},
						totalProperty: 'total',
						fields: ['id_concepto_ingas', 'tipo','desc_moneda','id_moneda','desc_ingas','requiere_descripcion','precio','excento','contabilizable'],
						remoteSort: true,
						baseParams: {par_filtro: 'ingas.desc_ingas',facturacion:'FACTCOMP', emision:'facturacion'}
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
				pageSize: 20,
				queryDelay: 1000,
				listWidth:'450',
				gwidth: 300,
				minChars: 2,
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div><b>{0}</b></div>', Ext.util.Format.number(record.data.nombre_producto));
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
					 '<p><span style="color: green; font-weight:bold;"> {desc_ingas}</span></p></p>',
					 '</div></tpl>'
				 ]),

			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'movtip.nombre',type: 'string'},
			grid: true,
			form: true

		},
		// {
		// 	config:{
		// 		name: 'codigo_internacional',
		// 		fieldLabel: 'Moneda',
		// 		allowBlank: true,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:10,
		// 		hidden:false,
		// 		renderer:function (value,p,record){
		// 			return  String.format('<div style="font-size:12px; color:red; font-weight:bold;">{0}</div>', record.data['codigo_internacional']);
		// 		},
		// 	},
		// 		type:'TextField',
		// 		filters:{pfiltro:'factdet.codigo_internacional',type:'string'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:false
		// },
		{
			config:{
				name: 'cantidad',
				fieldLabel: 'Cantidad',
				allowBlank: false,
				disabled: false,
				anchor:'100%',
				gwidth: 120,
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
				//egrid:true,
				grid:true,
				form:true

		},
		{
			config:{
				name: 'precio',
				fieldLabel: 'P/U',
				allowBlank: false,
				anchor:'100%',
				gwidth: 130,
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
				//egrid:true,
				grid:true,
				form:true

		},
		{
			config:{
				name: 'total',
				fieldLabel: 'Total',
				allowBlank: true,
				anchor:'100%',
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
				name: 'descripcion',
				fieldLabel: 'Descripción',
				allowBlank: true,
				gwidth: 200,
				anchor:'100%',
				//hidden:true
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
				anchor:'100%',
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
				anchor:'100%',
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
				anchor:'100%',
				gwidth: 130,
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
				anchor:'100%',
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
				anchor:'100%',
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
				anchor:'100%',
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
				anchor:'100%',
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
	title:'DETALLE FACTURA DE EXPORTACIÓN',
	fheight:'50%',
	fwidth:'47%',
	ActSave:'../../sis_ventas_facturacion/control/FacturacionExportacion/insertarVentaFacturacionExportacionDetalle',
	ActDel:'../../sis_ventas_facturacion/control/VentaDetalleFacturacion/eliminarVentaDetalleFacturacion',
	ActList:'../../sis_ventas_facturacion/control/FacturacionExportacion/listarDetalleFacturacionExportacion',
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
		{name:'id_moneda', type: 'numeric'},
		{name:'codigo_internacional', type: 'numeric'},
		{name:'id_concepto_ingas', type: 'numeric'},
		{name:'desc_ingas', type: 'string'},


	],
	sortInfo:{
		field: 'id_venta_detalle',
		direction: 'ASC'
	},
	bdel:false,
	bsave:false,
	bedit:false,
	bexcel:false,
	btest:false,
	bnew:false,

	onReloadPage: function(m){
		this.maestro=m;
		this.store.baseParams={id_venta:this.maestro.id_venta};
		this.Cmp.id_producto.store.baseParams.id_sucursal=this.maestro.id_sucursal;
		this.Cmp.id_producto.store.baseParams.id_punto_venta_producto=this.maestro.id_punto_venta;
		this.Cmp.id_producto.store.baseParams.tipo_pv=Phx.CP.getPagina(this.idContenedorPadre).variables_globales.tipo_pv;
		this.Cmp.id_producto.store.baseParams.regionales=Phx.CP.getPagina(this.idContenedorPadre).variables_globales.ESTACION_inicio;
		this.tipo_cambio = Phx.CP.getPagina(this.idContenedorPadre).tipo_cambio;


		this.load({params: {start: 0, limit: 50}});
	},

	onButtonSave:function(o){
			var filas=this.store.getModifiedRecords();
			if(filas.length>0){
							//prepara una matriz para guardar los datos de la grilla
							var data={};
							for(var i=0;i<filas.length;i++){
									data[i]=filas[i].data;
									data[i]._fila=this.store.indexOf(filas[i])+1
									this.agregarArgsExtraSubmit(filas[i].data);
									Ext.apply(data[i],this.argumentExtraSubmit);
							}
							Phx.CP.loadingHide();
							Ext.Ajax.request({
									url:this.ActSave,
									params:{_tipo:'matriz','row':String(Ext.util.JSON.encode(data))},
									//isUpload:this.fileUpload,
									success:this.successSave,
									failure: this.conexionFailure,
									timeout:this.timeout,
									scope:this
							});
			}
	},

	loadValoresIniciales: function(){
		this.Cmp.id_venta.setValue(this.maestro.id_venta);
		Phx.vista.VentaFacturacionExportacionDetalle.superclass.loadValoresIniciales.call(this);
	}

	}
)
</script>
