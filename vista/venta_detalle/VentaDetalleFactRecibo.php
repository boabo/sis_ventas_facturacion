<?php
/**
*@package pXP
*@file VentaDetalleFactRecibo.php
*@author  (bvasquez)
*@date 10-05-2019 19:33:22
*@description Archivo con la interfaz de usuario detalle de facturacion recibo
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.VentaDetalleFactRecibo=Ext.extend(Phx.gridInterfaz,{

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
		this.iniciarEventos()
	},

	successGetVariables : function (response,request) {

			var respuesta = JSON.parse(response.responseText);
			if('datos' in respuesta){
					this.variables_globales = respuesta.datos;
			}

		Phx.vista.VentaDetalleFactRecibo.superclass.constructor.call(this,request.arguments);
		this.init();
		//this.Cmp.id_producto.store.baseParams.tipo='producto_servicio';
		this.bbar.el.dom.style.background='linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%)';
		this.tbar.el.dom.style.background='linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%)';

		this.Cmp.descripcion.setVisible(false);
		//this.Cmp.excento.setVisible(false);
	},

	onButtonNew:function () {
		Phx.vista.VentaDetalleFactRecibo.superclass.onButtonNew.call(this);
		this.window.items.items[0].body.dom.style.background = 'linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%)';
		this.Cmp.descripcion.setVisible(false);
		this.Cmp.tipo.setValue('Servicio');
		//this.Cmp.tipo.fireEvent('select', this.Cmp.tipo,'Servicio',0);

		this.Cmp.id_venta.setValue(this.maestro.id_venta);

		this.Cmp.tipo.on('select',function(c,r,i) {
						this.cambiarCombo(r.data.field1);
		},this);



		this.comboCambio();

		this.Cmp.id_producto.on('select',function(c,r,i) {

			this.moneda_servicio = r.data.id_moneda;
			if (this.moneda_servicio == 2) {
				var precio = r.data.precio * this.tipo_cambio;
			} else {
				var precio = r.data.precio;
			}
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
		Phx.vista.VentaDetalleFactRecibo.superclass.onButtonSave.call(this);
	},

	successSave:function(resp){
			Phx.vista.VentaDetalleFactRecibo.superclass.successSave.call(this,resp);
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
			config: {
				name: 'id_producto',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/BolsaCompraColores.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Concepto</span>',
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
						baseParams: {par_filtro: 'ingas.desc_ingas',facturacion:'RO', emision:'recibo'}
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
				// tpl: new Ext.XTemplate([
				// 	 '<tpl for=".">',
				// 	 '<div class="x-combo-list-item">',
				// 	 '<p><b>Nombre:</b><span style="color: green; font-weight:bold;"> {desc_ingas}</span></p></p>',
				// 	 '<p><b>Moneda:</b> <span style="color: blue; font-weight:bold;">{desc_moneda}</span></p>',
				// 	 '<p><b>Precio:</b> <span style="color: blue; font-weight:bold;">{precio}</span></p>',
				// 	 '<p><b>Tiene Exento:</b> <span style="color: red; font-weight:bold;">{excento}</span></p>',
				// 	 '<p><b>Requiere Descripción:</b> <span style="color: red; font-weight:bold;">{requiere_descripcion}</span></p>',
				// 	 '<p><b>Contabilizable:</b> <span style="color: red; font-weight:bold;">{contabilizable}</span></p>',
				// 	 '</div></tpl>'
				//  ]),
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'movtip.nombre',type: 'string'},
			grid: true,
			form: true

		},
		{
			config:{
				name: 'codigo_internacional',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Moneda</span>',
				allowBlank: true,
				anchor:'100%',
				gwidth: 100,
				maxLength:10,
				hidden:false,
				renderer:function (value,p,record){
					return  String.format('<div style="font-size:12px; color:red; font-weight:bold;">{0}</div>', record.data['codigo_internacional']);
				},
			},
				type:'TextField',
				filters:{pfiltro:'factdet.codigo_internacional',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'cantidad',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/Cantidad.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Cantidad</span>',
				allowBlank: false,
				disabled: true,
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
				egrid:false,
				grid:true,
				form:true

		},
		{
			config:{
				name: 'precio',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/Dolar.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> P/U</span>',
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
				egrid:true,
				grid:true,
				form:true

		},
		{
			config:{
				name: 'total',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Total</span>',
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
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Descripción</span>',
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
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/calendario.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Fecha creación</span>',
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
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/AvatarUsuario.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Creado por</span>',
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
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/AvatarUsuario.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Modificado por</span>',
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
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/calendario.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Fecha Modif.</span>',
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
	title:'<center><img src="../../../lib/imagenes/facturacion/Perfil.svg" style="width:40px; vertical-align: middle;"> <span style="vertical-align: middle; color:#008CB2; font-size:35px; text-shadow: 2px 0px 0px #000000;"> DETALLE RECIBO</span></center>',
	fheight:'50%',
	fwidth:'47%',
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
	bsave:true,
	bedit:true,
	bnew:false,

	onButtonEdit: function(){
		Phx.vista.VentaDetalleFactRecibo.superclass.onButtonEdit.call(this);
		this.Cmp.precio.on('change',function(field,newValue,oldValue){
				var precio = (this.Cmp.precio.getValue());
			this.Cmp.total.setValue(parseFloat(precio*this.Cmp.cantidad.getValue()));
		},this);//monto_forma_pago

		// this.Cmp.cantidad.on('change',function(field,newValue,oldValue){
		// 	var precio = (this.Cmp.precio.getValue());
		//
		// 	this.Cmp.total.setValue(parseFloat(this.Cmp.cantidad.getValue()*precio));
		// },this);
	},
	onReloadPage: function(m){
		this.maestro=m;
		this.store.baseParams={id_venta:this.maestro.id_venta};
		this.Cmp.id_producto.store.baseParams.id_sucursal=this.maestro.id_sucursal;
		this.Cmp.id_producto.store.baseParams.id_punto_venta_producto=this.maestro.id_punto_venta;
		this.Cmp.id_producto.store.baseParams.tipo_pv=Phx.CP.getPagina(this.idContenedorPadre).variables_globales.tipo_pv;
		this.Cmp.id_producto.store.baseParams.regionales=Phx.CP.getPagina(this.idContenedorPadre).variables_globales.ESTACION_inicio;
		this.tipo_cambio = Phx.CP.getPagina(this.idContenedorPadre).tipo_cambio;
		if(this.maestro.estado!='borrador'){
			// this.getBoton('new').setVisible(false);
			this.getBoton('edit').setVisible(false);
			// this.getBoton('del').setVisible(false);
			this.getBoton('save').setVisible(false);
		}else{
			// this.getBoton('new').setVisible(true);
			this.getBoton('edit').setVisible(true);
			// this.getBoton('del').setVisible(true);
			this.getBoton('save').setVisible(true);
		}
		// if (m.excento_verificado == 'no' &&  m.requiere_excento == 'si') {
		// 	this.crearFormulatio();
		// }

		this.load({params: {start: 0, limit: 50}});
	},

	crearFormulatio: function (that) {

			var simple = new Ext.FormPanel({
			 labelWidth: 75, // label settings here cascade unless overridden
			 frame:true,
			 bodyStyle:'padding:5px 5px 0; background:linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%);',
			 width: 300,
			 height:70,
			 defaultType: 'textfield',
			 items: [
							  new Ext.form.NumberField({
																		name: 'excento',
																		msgTarget: 'title',
																		fieldLabel: 'Valor Exento',
																		allowBlank: false,
																		allowDecimals: true,
																		decimalPrecision : 2,
																		style:{
																			width: '190px'
																		},
																		enableKeyEvents : true,

														}),
								]

	 					});
				this.excento_formulario = simple;

			var formu_excento = new Ext.Window({
				title: '<h1 style="height:20px; font-size:15px;"><img src="../../../lib/imagenes/iconos_generales/pagar.png" height="20px" style="float:left;"> <p style="margin-left:30px;">Valor Exento<p></h1>', //the title of the window
				width:320,
				height:150,
				//closeAction:'hide',
				modal:true,
				plain: true,
				items:simple,
				buttons: [{
										text:'<p style="color:green; font-size:15px; font-weight:bold;"><i class="fa fa-floppy-o fa-lg"></i> Guardar</p>',
										scope:this,
										handler: function(){
												this.insertarNuevo(formu_excento);
										}
								},{
										text: '<p style="color:red; font-size:15px; font-weight:bold;"><i class="fa fa-times-circle fa-lg"></i> Cancelar</p>',
										handler: function(){
												formu_excento.hide();
										}
								}]

			});


			// formu_excento.buttons[0].btnEl.dom.style.height = '200px;'
			formu_excento.show();
			formu_excento.buttons[0].el.dom.style.width = '100px';
			formu_excento.buttons[0].el.dom.style.height = '30px';

			formu_excento.buttons[1].el.dom.style.width = '100px';
			formu_excento.buttons[1].el.dom.style.height = '30px';
			this.excento_formulario.items.items[0].setValue(this.valor_excento);


	},
	insertarNuevo : function (formu_excento) {
		this.guardarDetalles();
		formu_excento.hide();
	},

	guardarDetalles : function(){
		/*Recuperamos de la venta detalle si existe algun concepto con excento*/
		Ext.Ajax.request({
				url : '../../sis_ventas_facturacion/control/VentaDetalleFacturacion/actualizarExcento',
				params : {
					'id_venta' : this.maestro.id_venta,
					'valor_excento': this.excento_formulario.items.items[0].getValue()
				},
				success : this.successExportHtml,
				failure : this.conexionFailure,
				timeout : this.timeout,
				scope : this
			});
			Phx.CP.getPagina(this.idContenedorPadre).reload();
		/**********************************************************************/
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
		Phx.vista.VentaDetalleFactRecibo.superclass.loadValoresIniciales.call(this);
	}

	}
)
</script>
