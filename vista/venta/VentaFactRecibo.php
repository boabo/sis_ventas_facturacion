<?php
/**
*@package pXP
*@file VentaFactRecibo.php
*@author  (bvasquez)
*@date 07-05-2021
*@description Archivo con la interfaz de usuario que permite el registro de facturacion recibo
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.VentaFactRecibo=Ext.extend(Phx.gridInterfaz,{
	mosttar:'',
	solicitarPuntoVenta: true,

	tabsouth:[
		{
			url:'../../../sis_ventas_facturacion/vista/venta_detalle/VentaDetalleFactRecibo.php',
			title:'Detalle Recibo',
			width:'100%',
			height:'50%',
			cls:'VentaDetalleFactRecibo'
		}
	],

	bexcel:false,
	btest:false,
  tipo_factura: '',
	constructor:function(config){
		this.maestro=config.maestro;
		//this.tipo_usuario = 'cajero';
		Ext.Ajax.request({
				url:'../../sis_ventas_facturacion/control/Cajero/getTipoUsuario',
				params: {'vista':'counter'},
				success: function(resp){
						var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
						this.tipo_usuario = reg.ROOT.datos.v_tipo_usuario;
				},
				failure: this.conexionFailure,
				timeout:this.timeout,
				scope:this
		});

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
			if(this.solicitarPuntoVenta){
					this.seleccionarPuntoVentaSucursal();

			}
			Phx.vista.VentaFactRecibo.superclass.constructor.call(this,request.arguments);
			this.store.baseParams.tipo_usuario = this.tipo_usuario;
			this.store.baseParams.pes_estado = 'borrador';
			//	this.bbar.add(this.cmbPuntoV);
			this.addButton('sgt_estado',{
					grupo:[0],
					text :'Enviar a Caja',
					iconCls : 'badelante',
					disabled: true,
					handler : this.sigEstado,
					tooltip : '<b>Enviar venta al cajero para su respectivo cobro</b>'
			});

			// this.addButton('mod_excento',{
			// 		grupo:[0],
			// 		text :'Modificar Exento',
			// 		iconCls : 'bmoney',
			// 		disabled: true,
			// 		handler : this.modExcento,
			// 		tooltip : '<b>Modificar el valor exento de la venta</b>'
			// });

			this.addButton('anular_fact',
					{   grupo:[4],
							text: 'Anular',
							iconCls: 'bwrong',
							disabled: true,
							handler: this.anular,
							tooltip: '<b>Imprimir Recibo</b><br/>Imprime el Recibo de la venta'
					}
			);

			this.init();
			this.getBoton('sgt_estado').enable();

			this.campo_fecha = new Ext.form.DateField({
				name: 'fecha_reg',
				grupo: this.bactGroups,
			fieldLabel: 'Fecha',
			allowBlank: false,
			anchor:'100%',
			gwidth: 100,
			format: 'd/m/Y',
			hidden : false
		});

			this.punto_venta = new Ext.form.Label({
					name: 'punto_venta',
					grupo: this.bactGroups,
					fieldLabel: 'P.V.',
					readOnly:true,
					anchor: '150%',
					gwidth: 150,
					format: 'd/m/Y',
					hidden : false,
					//style: 'font-size: 170%; font-weight: bold; background-image: none;'
					style: {
						fontSize:'170%',
						fontWeight:'bold',
						color:'black',
						textShadow: '0.5px 0.5px 0px #FFFFFF, 1px 0px 0px rgba(0,0,0,0.15)',
						marginLeft:'20px'
					}
			});

			this.apertura = new Ext.form.Label({
					name: 'apertura',
					//grupo: this.bactGroups,
					fieldLabel: 'Apertura',
					readOnly:true,
					anchor: '150%',
					gwidth: 150,
					format: 'd/m/Y',
					hidden : false,
					//style: 'font-size: 170%; font-weight: bold; background-image: none;'
					style: {
						fontSize:'170%',
						fontWeight:'bold',
						marginLeft:'20px'
					}
			});

			this.tbar.addField(this.campo_fecha);
			this.tbar.addField(this.punto_venta);
			this.bbar.addField(this.apertura);

			var datos_respuesta = JSON.parse(response.responseText);
	    var fecha_array = datos_respuesta.datos.fecha.split('/');
	    this.campo_fecha.setValue(new Date(fecha_array[2],parseInt(fecha_array[1]) - 1,fecha_array[0]));


			this.campo_fecha.on('select',function(value){
			this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');

			this.load();
		},this);

			this.finCons = true;
			this.bbar.el.dom.style.background='linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%)';
			this.tbar.el.dom.style.background='linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%)';
			this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#f9f9f9';
			this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#EAEAEA';


			// this.Cmp.id_formula.on('select',function(c,r,i) {
			// 	Ext.Ajax.request({
			// 			url:'../../sis_ventas_facturacion/control/VentaDetalleFacturacion/verificarExcentoFormula',
			// 			params:{id_formula:this.Cmp.id_formula.getValue()},
			// 			success: function(resp){
			// 					var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
			// 					this.requiere_excento = reg.ROOT.datos.v_tiene_excento;
			// 					if (this.requiere_excento == 'si') {
			// 						this.mostrarComponente(this.Cmp.excento);
			// 						this.Cmp.excento.setValue(0);
			// 					}
			// 			},
			// 			failure: this.conexionFailure,
			// 			timeout:this.timeout,
			// 			scope:this
			// 	});
			// },this);


	},

	gruposBarraTareas:[{name:'borrador',title:'<H1 style="font-size:12px;" align="center"><i style="color:#FFAE00; font-size:15px;" class="fa fa-eraser"></i> En Registro</h1>',grupo:0,height:0},
										 {name:'caja',title:'<H1 style="font-size:12px;" align="center"><i style="color:green; font-size:15px;" class="fa fa-usd"></i> En Caja</h1>',grupo:1,height:0},
										 {name:'finalizado',title:'<H1 style="font-size:12px;" align="center"><i style="color:#B61BFF; font-size:15px;" class="fa fa-check-circle"></i> Emitidos</h1>',grupo:2,height:0},
										 {name:'anulado',title:'<H1 style="font-size:12px;" align="center"><i style="color:red; font-size:15px;" class="fa fa-ban"></i> Anulados</h1>',grupo:3,height:0}
										 ],

 actualizarSegunTab: function(name, indice){
			 if(this.finCons){
					 this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');
					 this.store.baseParams.pes_estado = name;
					 this.store.baseParams.interfaz = 'vendedor';
					 this.load({params:{start:0, limit:this.tam_pag}});
				 }
	},

	preparaMenu: function () {
			var rec = this.sm.getSelected();
			this.getBoton('anular_fact').enable();
			//this.getBoton('sgt_estado').enable();

			// if (rec.data.requiere_excento == 'si') {
			// 	this.getBoton('mod_excento').enable();
			// }

			if (rec.data.total_venta > 0) {
				this.getBoton('sgt_estado').enable();
			}

			// if (rec.data.excento > 0) {
			// 	this.getBoton('mod_excento').enable();
			// }
			Phx.vista.VentaFactRecibo.superclass.preparaMenu.call(this);
		},

		liberaMenu : function(){
				var rec = this.sm.getSelected();
				this.getBoton('anular_fact').disable();
				this.getBoton('sgt_estado').disable();
				// this.getBoton('mod_excento').disable();
				Phx.vista.VentaFactRecibo.superclass.liberaMenu.call(this);
		},


	 bactGroups:  [0,1,2,3],
	 btestGroups: [0,1,2,3],
	 bexcelGroups: [0,1,2],

	seleccionarPuntoVentaSucursal : function () {
		var validado = false;
		var title;
		var value;
		if (this.variables_globales.vef_tiene_punto_venta === 'true') {
			title = 'Seleccione el punto de venta con el que trabajara';
			value = 'id_punto_venta';
			var storeCombo = new Ext.data.JsonStore({
											url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
											id: 'id_punto_venta',
											root: 'datos',
											sortInfo: {
													field: 'nombre',
													direction: 'ASC'
											},
											totalProperty: 'total',
											fields: ['id_punto_venta','tipo', 'id_sucursal','nombre', 'codigo','habilitar_comisiones','formato_comprobante'],
											remoteSort: true,
											baseParams: {tipo_usuario: this.tipo_usuario,par_filtro: 'puve.nombre#puve.codigo', tipo_factura: this.tipo_factura, tipo : this.tipo}
			});
		} else {
			title = 'Seleccione la sucursal con la que trabajara';
			value = 'id_sucursal';
			var storeCombo = new Ext.data.JsonStore({
										url: '../../sis_ventas_facturacion/control/Sucursal/listarSucursal',
										id: 'id_sucursal',
										root: 'datos',
										sortInfo: {
												field: 'nombre',
												direction: 'ASC'
										},
										totalProperty: 'total',
										fields: ['id_sucursal', 'nombre', 'codigo','habilitar_comisiones','formato_comprobante','id_entidad'],
										remoteSort: true,
										baseParams: {tipo_usuario: this.tipo_usuario,par_filtro: 'suc.nombre#suc.codigo', tipo_factura: this.tipo_factura}
							 });
		}

			storeCombo.load({params:{start: 0, limit: this.tam_pag},

						 callback : function (r) {
									//if (r.length == 0 ) {
									if (this.variables_globales.vef_tiene_punto_venta === 'false' ) {
										if (this.variables_globales.vef_tiene_punto_venta === 'true') {
												this.variables_globales.id_punto_venta = r[0].data.id_punto_venta;
												this.variables_globales.habilitar_comisiones = r[0].data.habilitar_comisiones;
												this.variables_globales.formato_comprobante = r[0].data.formato_comprobante;
												this.store.baseParams.id_punto_venta = this.variables_globales.id_punto_venta;
														this.store.baseParams.tipo_usuario = this.tipo_usuario;
											} else {
												this.variables_globales.id_sucursal = r[0].data.id_sucursal;
												this.variables_globales.id_entidad = r[0].data.id_entidad;
												this.variables_globales.habilitar_comisiones = r[0].data.habilitar_comisiones;
												this.variables_globales.formato_comprobante = r[0].data.formato_comprobante;
												this.store.baseParams.id_sucursal = this.variables_globales.id_sucursal;
													this.store.baseParams.tipo_usuario = this.tipo_usuario;
												}
											this.store.baseParams.tipo_factura = this.tipo_factura;
											this.load({params:{start:0, limit:this.tam_pag}});
									} else {

										var combo2 = new Ext.form.ComboBox(
								{
										typeAhead: false,
										fieldLabel: title,
										allowBlank : false,
										store: storeCombo,
										mode: 'remote',
												pageSize: 15,
										triggerAction: 'all',
										valueField : value,
												displayField : 'nombre',
										forceSelection: true,
										tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
										allowBlank : false,
										anchor: '100%',
										resizable : true
								});

						 var formularioInicio = new Ext.form.FormPanel({
										items: [combo2],
										padding: true,
										bodyStyle:'padding:5px 5px 0',
										border: false,
										frame: false
								});

						 var VentanaInicio = new Ext.Window({
											title: 'Punto de Venta / Sucursal',
											modal: true,
											width: 400,
											height: 160,
											bodyStyle: 'padding:5px;',
											layout: 'fit',
											hidden: true,
											buttons: [
													{
														text: '<i class="fa fa-check"></i> Aceptar',
														handler: function () {
															if (formularioInicio.getForm().isValid()) {
																validado = true;
																this.variables_globales.habilitar_comisiones = combo2.getStore().getById(combo2.getValue()).data.habilitar_comisiones;
																this.variables_globales.formato_comprobante = combo2.getStore().getById(combo2.getValue()).data.formato_comprobante;
																VentanaInicio.close();

																if (this.variables_globales.vef_tiene_punto_venta === 'true') {
																		this.variables_globales.id_punto_venta = combo2.getValue();
																		this.variables_globales.id_sucursal = storeCombo.getById(combo2.getValue()).data.id_sucursal;
																		this.store.baseParams.id_punto_venta = this.variables_globales.id_punto_venta;
																		this.store.baseParams.tipo = this.variables_globales.tipo;
																	} else {
																		this.variables_globales.id_sucursal = combo2.getValue();
																		this.store.baseParams.id_sucursal = this.variables_globales.id_sucursal;
																	}
																	this.variables_globales.tipo_pv = storeCombo.getById(combo2.getValue()).data.tipo;
																	this.store.baseParams.tipo_usuario = this.tipo_usuario;
																	this.store.baseParams.tipo_factura = 'recibo';
																	this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');
																	this.punto_venta.setText(combo2.lastSelectionText)
																	this.load({params:{start:0, limit:this.tam_pag}});
																	this.iniciarEventos();

															}
														},
										scope: this
												 }],
											items: formularioInicio,
											autoDestroy: true,
											closeAction: 'close'
									});
								VentanaInicio.show();
								VentanaInicio.mask.dom.style.background='black';
								VentanaInicio.body.dom.childNodes[0].firstChild.firstChild.style.background='linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%)';
								//VentanaInicio.body.dom.childNodes.style.background='black';
								VentanaInicio.on('beforeclose', function (){
									if (!validado) {
										alert('Debe seleccionar el punto de venta o sucursal de trabajo');
										return false;
									}
								},this)
									}

							}, scope : this
					});



	},

	onDestroy: function() {
			this.variables_globales.id_punto_venta = '';
			this.fireEvent('closepanel',this);

			if (this.window) {
					this.window.destroy();
			}
			if (this.form) {
					this.form.destroy();
			}

			Phx.CP.destroyPage(this.idContenedor);
			delete this;

	},


	iniciarEventos:function(){
		//recuperamos si tiene apertura de Caja

		/*Obtenemos el tipo de cambio*/
		this.tipo_cambio = 0;
		var fecha = new Date();
		var dd = fecha.getDate();
		var mm = fecha.getMonth() + 1; //January is 0!
		var yyyy = fecha.getFullYear();
		this.fecha_actual = dd + '/' + mm + '/' + yyyy;        ;
		Ext.Ajax.request({
				url:'../../sis_ventas_facturacion/control/AperturaCierreCaja/getTipoCambio',
				params:{fecha_cambio:this.fecha_actual},
				success: function(resp){
						var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
						this.tipo_cambio = reg.ROOT.datos.v_tipo_cambio;
						this.moneda_base = reg.ROOT.datos.v_codigo_moneda;
				},
				failure: this.conexionFailure,
				timeout:this.timeout,
				scope:this
		});
		/******************************/

		/*Filtramos la lista de paquetes por la sucursal seleccionada*/
		// this.Cmp.id_formula.store.baseParams.tipo_punto_venta = this.variables_globales.tipo_pv;
		// this.Cmp.id_formula.store.baseParams.regional = this.variables_globales.ESTACION_inicio;
		// this.Cmp.id_formula.store.baseParams.id_punto_venta = this.variables_globales.id_punto_venta;
		/*************************************************************/


		/*********Actualizacion automatica*******/
		// this.Ftimer();
		// this.timer_id=Ext.TaskMgr.start({
		// 	 run: Ftimer,
		// 	 interval:3000,
		// 	 scope:this
	 // });

	 function Ftimer(){
	if (this.variables_globales.id_punto_venta != '') {
		 Ext.Ajax.request({
 				url:'../../sis_ventas_facturacion/control/VentaFacturacion/obtenerAperturaCounter',
 				params:{
 					id_punto_venta:this.variables_globales.id_punto_venta,
 					id_sucursal:this.variables_globales.id_sucursal
 				},
 				success: function(resp){
 						var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
 						this.aperturaText = reg.ROOT.datos.v_apertura;
						this.cantidad_cajas = reg.ROOT.datos.v_cantidad_apertura;
 						this.variables_globales.aperturaEstado = this.aperturaText;

 						//console.log("llega aqui avriable",this.aperturaText);

 						if (this.aperturaText == 'SIN APERTURA DE CAJA') {
 							this.bbar.items.items[14].el.dom.style.color='red';
 							this.bbar.items.items[14].el.dom.style.letterSpacing='.1em';
 							this.bbar.items.items[14].el.dom.style.textShadow='0.5px 0.5px 0px #FFFFFF, 1px 0px 0px rgba(0,0,0,0.15)';
 							// this.bbar.el.dom.style.background='linear-gradient(45deg, #ffe2e2 0%,#e09d9d 100%)';
 							//
 							// this.tbar.el.dom.style.background='linear-gradient(45deg, #ffe2e2 0%,#e09d9d 100%)';
 							this.apertura.setText(this.aperturaText)
 						} else if (this.aperturaText == 'abierto') {
 							this.bbar.items.items[14].el.dom.style.color='green';
 							this.bbar.items.items[14].el.dom.style.letterSpacing='.1em';
 							this.bbar.items.items[14].el.dom.style.textShadow='0.5px 0.5px 0px #FFFFFF, 1px 0px 0px rgba(0,0,0,0.15)';

 							//console.log("llega aqui",this.bbar.items.items[14].el.dom.style);
 							// this.bbar.el.dom.style.background='linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%)';
 							//
 							// this.tbar.el.dom.style.background='linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%)';
							if (this.cantidad_cajas == 1) {
								this.apertura.setText('CAJA ABIERTA');
							} else {
								this.apertura.setText('('+this.cantidad_cajas+') CAJAS ABIERTAS');
							}
 						}else if (this.aperturaText == 'cerrado') {
 							this.bbar.items.items[14].el.dom.style.color='blue';
 							this.bbar.items.items[14].el.dom.style.letterSpacing='.1em';
 							this.bbar.items.items[14].el.dom.style.textShadow='0.5px 0.5px 0px #FFFFFF, 1px 0px 0px rgba(0,0,0,0.15)';
 							// this.bbar.el.dom.style.background='linear-gradient(45deg, #ffe2e2 0%,#e09d9d 100%)';
 							//
 							// this.tbar.el.dom.style.background='linear-gradient(45deg, #ffe2e2 0%,#e09d9d 100%)';
 							this.apertura.setText('CAJA CERRADA')
 						}


 				},
 				failure: this.conexionFailure,
 				timeout:this.timeout,
 				scope:this
 		});
 		/***************/

		}
	}

	this.Cmp.id_producto.store.baseParams.id_sucursal=this.variables_globales.id_sucursal;
	this.Cmp.id_producto.store.baseParams.id_punto_venta_producto=this.variables_globales.id_punto_venta;
	this.Cmp.id_producto.store.baseParams.tipo_pv=this.variables_globales.tipo_pv;
	this.Cmp.id_producto.store.baseParams.regionales=this.variables_globales.ESTACION_inicio;
	/************************************************/
	this.Cmp.id_producto.on('select',function(c,r,i) {

		// this.moneda_servicio = r.data.id_moneda;
		// if (this.moneda_servicio == 2) {
		// 	var precio = r.data.precio * this.tipo_cambio;
		// } else {
		// 	var precio = r.data.precio;
		// }
		// this.Cmp.precio.setValue(parseFloat(precio));
		// this.Cmp.cantidad.setValue(1);
		// this.Cmp.total.setValue(parseFloat(precio*1));

		if (r.data.requiere_descripcion == 'si') {
				this.Cmp.descripcion.setVisible(true);
				this.Cmp.descripcion.allowBlank = false;
		} else {
			this.Cmp.descripcion.allowBlank = true;
			this.Cmp.descripcion.setVisible(false);
		}
	 },this);

	 this.Cmp.precio.on('change',function(field,newValue,oldValue){
			 var precio = (this.Cmp.precio.getValue());
			 var tota_precio = parseFloat(precio*this.Cmp.cantidad.getValue());
			 if ( tota_precio < this.Cmp.monto_exacto.getValue()){
           Ext.Msg.show({
               title: 'Alerta',
               msg: '<p>El precio unitario: '+tota_precio+' es menor al total '+this.Cmp.monto_exacto.getValue()+' de la venta recuperada segun el PNR '+this.Cmp.nro_pnr.getValue()+' ingresado</p>',
               buttons: Ext.Msg.OK,
               width: 500,
               icon: Ext.Msg.INFO
           });
			 }else{
		 		this.Cmp.total.setValue(parseFloat(precio*this.Cmp.cantidad.getValue()));
			 }
	 },this);

	 this.Cmp.cantidad.on('change',function(field,newValue,oldValue){
		 var precio = (this.Cmp.precio.getValue());
		 var tota_precio = parseFloat(precio*this.Cmp.cantidad.getValue());
		 if (tota_precio< this.Cmp.monto_exacto.getValue()){
				 Ext.Msg.show({
						 title: 'Alerta',
						 msg: '<p>El precio unitario: '+tota_precio+' es menor al total '+this.Cmp.monto_exacto.getValue()+' de la venta recuperada segun el PNR '+this.Cmp.nro_pnr.getValue()+' ingresado</p>',
						 buttons: Ext.Msg.OK,
						 width: 500,
						 icon: Ext.Msg.INFO
				 });
		 }else{
			 this.Cmp.total.setValue(parseFloat(this.Cmp.cantidad.getValue()*precio));
		 }

	 },this);

	 this.Cmp.anticipo_inicial.on('check',function(f,n){

		 if(n){
			 this.Cmp.nro_pnr.setVisible(true);
			 this.Cmp.porcentaje_pnr.setVisible(true);
			 this.Cmp.nro_pnr.allowBlank = false;
			 this.Cmp.porcentaje_pnr.allowBlank = false;
		 }else{
			 this.Cmp.nro_pnr.setValue(null);
			 this.Cmp.porcentaje_pnr.setValue(30);
			 this.Cmp.nro_pnr.setVisible(false);
			 this.Cmp.porcentaje_pnr.setVisible(false);
			 this.Cmp.nro_pnr.allowBlank = true;
			 this.Cmp.porcentaje_pnr.allowBlank = true;
		 }
	 },this);

	 this.Cmp.nro_pnr.on('change',function(field,newValue,oldValue){
		 this.consultPnrData(newValue, this.Cmp.porcentaje_pnr.getValue(),this.Cmp.anticipo_inicial.getValue())
	 },this);

	 this.Cmp.porcentaje_pnr.on('change',function(field,newValue,oldValue){
		 this.consultPnrData(this.Cmp.porcentaje_pnr.getValue(), newValue,this.Cmp.anticipo_inicial.getValue())
	 },this);

	},


		onButtonNew:function () {
      this.window.setSize(550, 550);
			this.Cmp.monto_exacto.setValue(0);
			Phx.vista.VentaFactRecibo.superclass.onButtonNew.call(this);
			this.Cmp.cantidad.setValue(1);
			this.window.items.items[0].body.dom.style.background = 'linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%)';
			this.Cmp.id_punto_venta.store.baseParams.id_punto_venta = this.store.baseParams.id_punto_venta;
					this.Cmp.id_punto_venta.store.load({params:{start:0,limit:this.tam_pag},
						 callback : function (r) {
									this.Cmp.id_punto_venta.setValue(this.store.baseParams.id_punto_venta);
									//	this.detCmp.id_producto.store.baseParams.id_punto_venta = this.Cmp.id_punto_venta.getValue();
									this.Cmp.id_punto_venta.fireEvent('select',this.Cmp.id_punto_venta, this.Cmp.id_punto_venta.store.getById(this.store.baseParams.id_punto_venta));

							}, scope : this
					});

			this.Cmp.id_sucursal.store.load({params:{start:0,limit:50},
				 callback : function (r) {
						this.Cmp.id_sucursal.setValue(this.variables_globales.id_sucursal);
						if (this.variables_globales.vef_tiene_punto_venta != 'true') {
							this.detCmp.id_producto.store.baseParams.id_sucursal = this.Cmp.id_sucursal.getValue();
							}
							this.Cmp.id_sucursal.fireEvent('select',this.Cmp.id_sucursal, this.Cmp.id_sucursal.store.getById(this.variables_globales.id_sucursal));

					}, scope : this
			});


			/******************************************************/
			// this.Cmp.nit.on('blur',function(c) {
			// 	if (this.Cmp.nit.getValue() != '') {
			// 		this.Cmp.nombre_factura.reset();
			// 		Ext.Ajax.request({
			// 				url : '../../sis_ventas_facturacion/control/VentaFacturacion/RecuperarCliente',
			// 				params : {
			// 					'nit' : this.Cmp.nit.getValue(),
			// 					'razon_social' : this.Cmp.nombre_factura.getValue(),
			// 				},
			// 				success: function(resp){
	    //             var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
			// 						this.Cmp.nombre_factura.setValue(reg.ROOT.datos.razon);
	    //             this.Cmp.id_cliente.setValue(reg.ROOT.datos.id_cliente);
	    //         },
			// 				failure : this.conexionFailure,
			// 				timeout : this.timeout,
			// 				scope : this
			// 			});
			// 	}
      //
			// },this);

			// this.ocultarComponente(this.Cmp.excento);
			this.Cmp.id_producto.setVisible(true);
			this.Cmp.cantidad.setVisible(true);
			this.Cmp.precio.setVisible(true);
			this.Cmp.total.setVisible(true);
			// this.Cmp.nro_pnr.setVisible(false);
			this.Cmp.anticipo_inicial.setVisible(true);
			this.Cmp.id_producto.allowBlank = true;
			this.Cmp.cantidad.allowBlank = true;
			this.Cmp.precio.allowBlank = true;
			this.Cmp.total.allowBlank = true;
		},
			onButtonEdit:function () {
				this.window.setSize(500, 350);
				Phx.vista.VentaFactRecibo.superclass.onButtonEdit.call(this);
				this.window.items.items[0].body.dom.style.background = 'linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%)';

				var rec = this.sm.getSelected();

				this.Cmp.id_punto_venta.store.baseParams.id_punto_venta = this.store.baseParams.id_punto_venta;
						this.Cmp.id_punto_venta.store.load({params:{start:0,limit:this.tam_pag},
							 callback : function (r) {
										this.Cmp.id_punto_venta.setValue(this.store.baseParams.id_punto_venta);
										//	this.detCmp.id_producto.store.baseParams.id_punto_venta = this.Cmp.id_punto_venta.getValue();
										this.Cmp.id_punto_venta.fireEvent('select',this.Cmp.id_punto_venta, this.Cmp.id_punto_venta.store.getById(this.store.baseParams.id_punto_venta));

								}, scope : this
						});

				this.Cmp.id_sucursal.store.load({params:{start:0,limit:50},
					 callback : function (r) {
							this.Cmp.id_sucursal.setValue(this.variables_globales.id_sucursal);
							if (this.variables_globales.vef_tiene_punto_venta != 'true') {
								this.detCmp.id_producto.store.baseParams.id_sucursal = this.Cmp.id_sucursal.getValue();
								}
								this.Cmp.id_sucursal.fireEvent('select',this.Cmp.id_sucursal, this.Cmp.id_sucursal.store.getById(this.variables_globales.id_sucursal));

						}, scope : this
				});
				// this.Cmp.id_formula.store.load({params:{start:0,limit:50},
				// 	 callback : function (r) {
				// 			this.Cmp.id_formula.setValue(rec.data.id_formula);
				// 				this.Cmp.id_formula.fireEvent('select',this.Cmp.id_formula, this.Cmp.id_formula.store.getById(rec.data.id_formula));
				// 		}, scope : this
				// });

				// this.Cmp.nit.on('blur',function(c) {
				// 	if (this.Cmp.nit.getValue() != '') {
				// 		Ext.Ajax.request({
				// 				url : '../../sis_ventas_facturacion/control/VentaFacturacion/RecuperarCliente',
				// 				params : {
				// 					'nit' : this.Cmp.nit.getValue(),
				// 					'razon_social' : this.Cmp.nombre_factura.getValue(),
				// 				},
				// 				success: function(resp){
		    //             var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
				// 						this.Cmp.nombre_factura.setValue(reg.ROOT.datos.razon);
		    //             this.Cmp.id_cliente.setValue(reg.ROOT.datos.id_cliente);
		    //         },
				// 				failure : this.conexionFailure,
				// 				timeout : this.timeout,
				// 				scope : this
				// 			});
				// 	}
				// },this);

				this.Cmp.id_producto.setVisible(false);
				this.Cmp.descripcion.setVisible(false);
				this.Cmp.cantidad.setVisible(false);
				this.Cmp.precio.setVisible(false);
				this.Cmp.total.setVisible(false);
				// this.Cmp.nro_pnr.setVisible(false);
				// this.Cmp.porcentaje_pnr.setVisible(false);
				this.Cmp.anticipo_inicial.setVisible(false);
				this.Cmp.id_moneda_venta_recibo.setVisible(false);
				this.Cmp.id_producto.allowBlank = true;
				this.Cmp.descripcion.allowBlank = true;
				this.Cmp.cantidad.allowBlank = true;
				this.Cmp.precio.allowBlank = true;
				this.Cmp.total.allowBlank = true;
				this.Cmp.nro_pnr.allowBlank = true;
				this.Cmp.porcentaje_pnr.allowBlank=true;
				this.Cmp.id_moneda_venta_recibo.allowBlank = true;
			},

		sigEstado:function(){
			//Phx.CP.loadingShow();
			var d = this.sm.getSelected().data;

			Ext.Ajax.request({
					url:'../../sis_ventas_facturacion/control/VentaFacturacion/siguienteEstadoRecibo',
					params:{id_estado_wf_act:d.id_estado_wf,
									id_proceso_wf_act:d.id_proceso_wf,
								  tipo:'recibo'},
					success:this.successWizard,
					failure: this.conexionFailure,
					timeout:this.timeout,
					scope:this
			});

     },

		 failureWizard:function(resp1,resp2,resp3,resp4,resp5){
         var resp = resp1;// error conexion
         var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
         if (reg.ROOT.detalle.mensaje.indexOf('insuficientes')!=-1) {
             var mensaje = reg.ROOT.detalle.mensaje;
             mensaje = mensaje.replace(/#/g, "");
             mensaje = mensaje.replace("*", "");
             mensaje = mensaje.replace("*", "");
             mensaje = mensaje.replace("{", "");
             mensaje = mensaje.replace("}", "");
             alert(mensaje);
             Phx.CP.loadingHide();

         } else {
             Phx.vista.ReciboLista.superclass.conexionFailure.call(this,resp1,resp2,resp3,resp4,resp5);
         }

     },
     successWizard:function(resp){
         var rec=this.sm.getSelected();
         var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
				 Phx.CP.getPagina(this.idContenedor).reload();
				 // resp.argument.wizard.panel.destroy();

      },
			anular : function () {
				Phx.CP.loadingShow();
		        var rec=this.sm.getSelected();

		        Ext.Ajax.request({
		            url:'../../sis_ventas_facturacion/control/Cajero/anularFactura',
		            params:{
		                id_venta:  rec.data.id_venta
		                },
		            success:this.successSave,
		            failure: this.conexionFailure,
		            timeout:this.timeout,
		            scope:this
		        });
			},

			modExcento : function () {
				var rec=this.sm.getSelected();
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

				var win = new Ext.Window({
					title: '<h1 style="height:20px; font-size:15px;"><img src="../../../lib/imagenes/iconos_generales/pagar.png" height="20px" style="float:left;"> <p style="margin-left:30px;">Valor Exento<p></h1>', //the title of the window
					width:320,
					height:150,
					//closeAction:'hide',
					modal:true,
					plain: true,
					items:simple,
					buttons: [{
											text:'<i class="fa fa-floppy-o fa-lg"></i> Guardar',
											scope:this,
											handler: function(){
													this.modificarNuevo(win);
											}
									},{
											text: '<i class="fa fa-times-circle fa-lg"></i> Cancelar',
											handler: function(){
													win.hide();
											}
									}]

				});
				win.show();
				this.excento_formulario.items.items[0].setValue(rec.data.excento);

			},
			modificarNuevo : function (win) {
				this.guardarDetalles();
				win.hide();
			},

			guardarDetalles : function(){
				var rec=this.sm.getSelected();
				/*Recuperamos de la venta detalle si existe algun concepto con excento*/
				Ext.Ajax.request({
						url : '../../sis_ventas_facturacion/control/VentaDetalleFacturacion/actualizarExcento',
						params : {
							'id_venta' : rec.data.id_venta,
							'valor_excento': this.excento_formulario.items.items[0].getValue()
						},
						success : this.successExportHtml,
						failure : this.conexionFailure,
						timeout : this.timeout,
						scope : this
					});
					this.reload();
				/**********************************************************************/
			},

	loadMask :false,
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
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_cliente'
			},
			type:'Field',
			form:true
		},
		{
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'monto_exacto'
			},
			type:'Field',
			form:true
		},
		{
			config:{
				name: 'fecha',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/calendario.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Fecha Recibo</span>',
				allowBlank: false,
				//anchor:'100%',
				anchor:'100%',
				gwidth: 120,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'fact.fecha',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
 		 config:{
 			 name: 'nro_factura',
 			 fieldLabel: '<img src="../../../lib/imagenes/facturacion/listaNumeros.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Nro Recibo</span>',
 			 allowBlank: true,
 			 anchor:'100%',
 			 gwidth: 120,
 			 maxLength:4
 		 },
 			 type:'NumberField',
 			 filters:{pfiltro:'ven.nro_factura',type:'numeric'},
 			 id_grupo:1,
 			 grid:true,
 			 form:false
 	 },
	 // {
		//  config:{
		// 	 name: 'cod_control',
		// 	 fieldLabel: '<img src="../../../lib/imagenes/facturacion/Codigo.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Codigo Control</span>',
		// 	 allowBlank: true,
		// 	 anchor:'100%',
		// 	 gwidth: 150,
		// 	 maxLength:15
		//  },
		// 	 type:'TextField',
		// 	 filters:{pfiltro:'ven.cod_control',type:'string'},
		// 	 id_grupo:1,
		// 	 grid:true,
		// 	 form:false
	 // },
		{
			config:{
				name: 'nit',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/CarnetIdentidad.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> NIT</span>',
				allowBlank: true,
				anchor:'100%',
				gwidth: 100,
				hidden:true
			},
				type:'NumberField',
				filters:{pfiltro:'fact.nit',type:'string'},
				id_grupo:1,
				grid:true,
				bottom_filter: true,
        grid:false,
				form:true,
        // valorInicial:-1
		},
		{
			config:{
				name: 'anticipo_inicial',
				fieldLabel: 'Anticipo Inicial',
				allowBlank: true,
				width: '80%',
			},
				type:'Checkbox',
				id_grupo: 1,
				form:true
		},
		{
 		config:{
 			name: 'nro_pnr',
 			fieldLabel: '<img src="../../../lib/imagenes/facturacion/listaNumeros.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> PNR</span>',
 			allowBlank: true,
 			anchor:'100%',
 			gwidth: 150,
			selectOnFocus: true,
 			style:'text-transform:uppercase',
			hidden:true
 		},
 			type:'TextField',
 			filters:{pfiltro:'fact.nro_pnr',type:'string'},
 			id_grupo:1,
 			grid:true,
 			bottom_filter:true,
 			form:true
 		},
		{
  		config:{
  			name: 'porcentaje_pnr',
  			fieldLabel: 'Porcentaje PNR',
  			allowBlank: true,
  			anchor:'100%',
  			gwidth: 120,
				hidden:true
  		},
  			type:'NumberField',
  			id_grupo:1,
  			grid:false,
  			form:true

  	},
		{
 		 config:{
 			 name: 'nombre_factura',
 			 fieldLabel: '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Razón Social</span>',
 			 allowBlank: false,
 			 anchor:'100%',
 			 gwidth: 150,
 			 maxLength:100,
			 style:'text-transform:uppercase'
 		 },
 			 type:'TextField',
 			 filters:{pfiltro:'fact.nombre_factura',type:'string'},
 			 id_grupo:1,
 			 grid:true,
 			 bottom_filter:true,
 			 form:true
 	 },
	 {
		 config:{
			 name: 'total_venta',
			 fieldLabel: '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Total Recibo</span>',
			 allowBlank: false,
			 anchor:'100%',
			 gwidth: 100,
			 renderer:function (value,p,record) {
					var dato =  value.replace('.', ",")
									.replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");

					if (value>0) {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="blue"><b>'+dato+'</b></font></p></div>';
					} else {
						return '<div style="text-align:right; font-weight:bold; font-size:20px; ext:qtip="Optimo"><p> <font color="red"><b>'+dato+'</b></font></p></div>';
					}
				},
			 decimalPrecision:2,
			 maxLength:1179650
		 },
			 type:'NumberField',
			 filters:{pfiltro:'fact.total_venta',type:'numeric'},
			 id_grupo:1,
			 grid:true,
			 form:false
	 },
	 {
		 config:{
			 name: 'credito_fiscal',
			 fieldLabel: '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Crédito Fiscal</span>',
			 allowBlank: true,
			 anchor:'100%',
			 gwidth: 150,
			 maxLength:1179650,
			 galign:'right',
			 renderer:function (value,p,record){
				 if (record.data['excento'] != 0) {
				 	var credito = (record.data['total_venta'] - record.data['excento']);
				} else {
					var credito = 0;
				}
			 return  String.format('<div style="font-size:12px; color:green; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(credito,'0,000.00'));
		 }
		 },
			 type:'NumberField',
			 filters:{pfiltro:'ven.excento',type:'numeric'},
			 id_grupo:1,
			 grid:true,
			 form:false
	 },
	 {
		 config:{
			 name: 'observaciones',
			 fieldLabel: '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Observaciones</span>',
			 allowBlank: false,
			 anchor:'100%',
			 gwidth: 200,
			 style:'text-transform:uppercase;',
			 // minLength:12
		 },
			 type:'TextArea',
			 filters:{pfiltro:'fact.observaciones',type:'string'},
			 id_grupo:1,
			 grid:true,
			 form:true
	 },
		{
			config:{
				name: 'correlativo_venta',
				fieldLabel: 'Nro',
				allowBlank: false,
				anchor:'100%',
				gwidth: 100,
				maxLength:20
			},
				type:'TextField',
				filters:{pfiltro:'fact.correlativo_venta',type:'string'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
				config: {
						name: 'id_sucursal',
						fieldLabel: 'Sucursal',
						allowBlank: false,
						width:200,
						emptyText: 'Elija una Suc...',
						store: new Ext.data.JsonStore({
								url: '../../sis_ventas_facturacion/control/Sucursal/listarSucursal',
								id: 'id_sucursal',
								root: 'datos',
								sortInfo: {
										field: 'nombre',
										direction: 'ASC'
								},
								totalProperty: 'total',
								fields: ['id_sucursal', 'nombre', 'codigo'],
								remoteSort: true,
								baseParams: {filtro_usuario: 'si',par_filtro: 'suc.nombre#suc.id_sucursal'}
						}),
						valueField: 'id_sucursal',
						gdisplayField : 'nombre_sucursal',
						displayField: 'nombre',
						hiddenName: 'id_sucursal',
						tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>id:</b> {id_sucursal}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
						forceSelection: true,
						typeAhead: false,
						triggerAction: 'all',
						lazyRender: true,
						mode: 'remote',
						pageSize: 15,
						queryDelay: 1000,
						disabled:false,
						minChars: 2,
						hidden:true
				},
				type: 'ComboBox',
				id_grupo: 1,
				form: true
		},

	 {
		 config:{
			 name: 'estado',
			 fieldLabel: '<img src="../../../lib/imagenes/facturacion/aceptarVerde.svg" style="width:20px; vertical-align: middle;">   <img src="../../../lib/imagenes/facturacion/cancelarRojo.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Estado</span>',
			 allowBlank: false,
			 anchor:'100%',
			 gwidth: 150,
			 maxLength:100
		 },
			 type:'TextField',
			 filters:{pfiltro:'ven.estado',type:'string'},
			 id_grupo:1,
			 grid:true,
			 form:false
	 },
	 {
		 config: {
			 name: 'id_punto_venta',
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
			 gdisplayField: 'nombre_punto_venta',
			 hiddenName: 'id_punto_venta',
			 forceSelection: true,
			 typeAhead: false,
			 triggerAction: 'all',
			 lazyRender: true,
			 mode: 'remote',
			 pageSize: 15,
			 queryDelay: 1000,
			 anchor: '100%',
			 gwidth: 150,
			 minChars: 2,
			 hidden:true
			 // renderer : function(value, p, record) {
			 // 	return String.format('{0}', record.data['nombre_punto_venta']);
			 // }
		 },
		 type: 'ComboBox',
		 id_grupo: 0,
		 filters: {pfiltro: 'puve.nombre',type: 'string'},
		 grid: true,
		 form: true
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
			tpl: new Ext.XTemplate([
				 '<tpl for=".">',
				 '<div class="x-combo-list-item">',
				 '<p><span style="color: green; font-weight:bold;"> {desc_ingas}</span></p></p>',
				 '</div></tpl>'
			 ]),
		},
		type: 'ComboBox',
		id_grupo: 1,
		form: true

	},
	{
		config:{
			name: 'descripcion',
			fieldLabel: '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Descripción Concepto</span>',
			allowBlank: true,
			gwidth: 200,
			anchor:'100%',
			hidden:true
		},
			type:'TextArea',
			id_grupo:1,
			grid:false,
			form:true
	},
	{
		config: {
			name: 'id_auxiliar_anticipo',
			fieldLabel: 'Grupo',
			allowBlank: false,
			anchor:'100%',
			emptyText: '',
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
				baseParams: {par_filtro: 'auxcta.codigo_auxiliar#auxcta.nombre_auxiliar',corriente:'si',ro_activo:'si'}
			}),
			valueField: 'id_auxiliar',
			displayField: 'nombre_auxiliar',
			gdisplayField: 'codigo_auxiliar',
			hiddenName: 'id_auxiliar',
			tpl:'<tpl for="."><div class="x-combo-list-item"><b><p style="color:red;">{nombre_auxiliar}</p><p>Codigo: <span style="color:green;">{codigo_auxiliar}</span></p></b></div></tpl>',
			// tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre_auxiliar}</p><p>Codigo:{codigo_auxiliar}</p> </div></tpl>',
			forceSelection: true,
			typeAhead: false,
			triggerAction: 'all',
			lazyRender: true,
			mode: 'remote',
			pageSize: 15,
			queryDelay: 1000,
			gwidth: 300,
			listWidth:350,
			resizable:true,
			minChars: 2,
			turl:'../../../sis_contabilidad/vista/auxiliar/AuxiliarGrupoRo.php',
			ttitle:'Grupos',
			tdata:{},
			tcls:'AuxiliarGrupoRo',
			pid:this.idContenedor,
		},
		type: 'TrigguerCombo',
		id_grupo: 1,
		grid: false,
		form: true
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
			// selectOnFocus: true,
			// decimalPrecision:0,
			valorInicial:1
		},
			type:'NumberField',
			id_grupo:1,
			grid:false,
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
			// selectOnFocus: false,
			maxLength:1179654,
			style:{
				width: '200px'
			},
			decimalPrecision:2
		},
			type:'NumberField',
			id_grupo:1,
			grid:false,
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
			readOnly :true,
			galign:'right',
			style:{
				width: '200px'
			}
		 },
			type:'NumberField',
			id_grupo:1,
			grid:false,
			form:true
	 },
	 {
		 config: {
			 name: 'id_moneda_venta_recibo',
			 fieldLabel: '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:20px; vertical-align: middle;"> Moneda',
			 allowBlank: false,
			 emptyText: 'Moneda Recibo...',
			 store: new Ext.data.JsonStore({
					 url: '../../sis_parametros/control/Moneda/listarMoneda',
					 id: 'id_moneda',
					 root: 'datos',
					 sortInfo: {
							 field: 'moneda',
							 direction: 'ASC'
					 },
					 totalProperty: 'total',
					 fields: ['id_moneda', 'codigo', 'moneda', 'codigo_internacional'],
					 remoteSort: true,
					 baseParams: {filtrar: 'si',par_filtro: 'moneda.moneda#moneda.codigo#moneda.codigo_internacional'}
			 }),
			 valueField: 'id_moneda',
			 displayField: 'codigo_internacional',
			 gdisplayField: 'codigo_internacional',
			 hiddenName: 'id_moneda',
			 forceSelection: true,
			 tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;"><b style="color:black;">Moneda:</b> <b>{moneda}</b></p><p style="color:red;"><b style="color:black;">Código:</b> <b>{codigo_internacional}</b></p></div></tpl>',
			 typeAhead: false,
			 triggerAction: 'all',
			 lazyRender: true,
			 mode: 'remote',
			 resizable:true,
			 pageSize: 5,
			 queryDelay: 1000,
			 gwidth: 300,
			 listWidth:'250',
			 minChars: 2,
		 },
		 type : 'ComboBox',
		 id_grupo : 1,
		 form : true
	 },
	 {
		 config : {
			 name : 'id_formula',
			 fieldLabel : '<img src="../../../lib/imagenes/facturacion/paquete.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Paquetes / Fórmulas</span>',
			 allowBlank : true,
			 anchor:'100%',
			 //listWidth:'100%',
			 maxHeight : 450,
			 resizable: true,
			 emptyText : 'Paquetes...',
			 store : new Ext.data.JsonStore({
				 url: '../../sis_ventas_facturacion/control/Formula_v2/listarFormula',
				 id : 'id_formula',
				 root : 'datos',
				 sortInfo : {
					 field : 'nombre',
					 direction : 'ASC'
				 },
				 totalProperty : 'total',
				 fields : ['id_formula', 'nombre', 'descripcion'],
				 remoteSort : true,
				 baseParams : {
					 par_filtro : 'form.nombre',
					 emision:'FACTCOMP',
					 tipo_pv:'cto'
				 }
			 }),
			 valueField : 'id_formula',
			 displayField : 'nombre',
			 gdisplayField : 'nombre',
			 hiddenName : 'id_formula',
			 forceSelection : false,
			 typeAhead : false,
			 // tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Nombre:</b> {nombre}</p><p><b>Descripcion:</b> {descripcion}</p></div></tpl>',
			 tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color: green; font-weight:bold;">{nombre}</p></div></tpl>',
			 triggerAction : 'all',
			 lazyRender : true,
			 mode : 'remote',
			 pageSize : 20,
			 queryDelay : 1000,
			 turl:'../../../sis_ventas_facturacion/vista/formula/Formula.php',
			 ttitle:'Formula',
			 tasignacion : true,
			 tname : 'id_formula',
			 tdata:{},
			 cls:'uppercase',
			 tcls:'Formula',
			 gwidth : 500,
			 minChars : 2,
			 style:'text-transform:uppercase;',
       hidden:true
		 },
		 type : 'ComboBox',
		 id_grupo : 0,
		 form : true
	 },
	 {
		 config:{
			 name: 'excento',
			 fieldLabel: '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Exento</span>',
			 allowBlank: true,
			 anchor:'100%',
			 gwidth: 100,
			 hidden:true,
			 maxLength:1179650,
			 galign:'right',
			 renderer:function (value,p,record){
			 return  String.format('<div style="font-size:12px; color:red; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
		 }
		 },
			 type:'NumberField',
			 filters:{pfiltro:'ven.excento',type:'numeric'},
			 id_grupo:1,
			 grid:true,
			 form:true
	 },

	 {
			config:{
				name: 'estado_reg',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/aceptarVerde.svg" style="width:20px; vertical-align: middle;">   <img src="../../../lib/imagenes/facturacion/cancelarRojo.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Estado Reg.</span>',
				allowBlank: true,
				anchor:'100%',
				gwidth: 200,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'fact.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'tipo_factura',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/PcEscritorio.svg" style="width:20px; vertical-align: middle;">   <img src="../../../lib/imagenes/facturacion/LapizPapel.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Tipo Factura</span>',
				allowBlank: true,
				anchor:'100%',
				gwidth: 200,
        hidden: true
			},
				type:'TextField',
				// filters:{pfiltro:'fact.tipo_factura',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor:'100%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'fact.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'fact.usuario_ai',type:'string'},
				id_grupo:1,
				grid:true,
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
				filters:{pfiltro:'fact.fecha_reg',type:'date'},
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
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/AvatarUsuario.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Cajero</span>',
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
				filters:{pfiltro:'fact.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	fheight:'50%',
	fwidth:'47%',
	title:'<center><img src="../../../lib/imagenes/facturacion/BolsaCompraColores.svg" style="width:20px; vertical-align: middle;"> <span style="vertical-align: middle; color:#008CB2; font-size:20px; text-shadow: 2px 0px 0px #000000;"> CABECERA RECIBO</span></center>',
	ActSave:'../../sis_ventas_facturacion/control/VentaFacturacion/insertarVentaFacturacion',
	ActDel:'../../sis_ventas_facturacion/control/VentaFacturacion/eliminarVentaFacturacion',
	ActList:'../../sis_ventas_facturacion/control/VentaFacturacion/listarVentaFacturacion',
	id_store:'id_venta',
	fields: [
		{name:'id_venta', type: 'numeric'},
		{name:'id_cliente', type: 'numeric'},
		{name:'id_dosificacion', type: 'numeric'},
		{name:'id_estado_wf', type: 'numeric'},
		{name:'id_proceso_wf', type: 'numeric'},
		{name:'id_punto_venta', type: 'numeric'},
		{name:'id_sucursal', type: 'numeric'},
		{name:'id_usuario_cajero', type: 'numeric'},
		{name:'id_cliente_destino', type: 'numeric'},
		{name:'transporte_fob', type: 'numeric'},
		{name:'tiene_formula', type: 'string'},
		{name:'cod_control', type: 'string'},
		{name:'estado', type: 'string'},
		{name:'total_venta_msuc', type: 'numeric'},
		{name:'otros_cif', type: 'numeric'},
		{name:'nro_factura', type: 'numeric'},
		{name:'observaciones', type: 'string'},
		{name:'seguros_cif', type: 'numeric'},
		{name:'comision', type: 'numeric'},
		{name:'id_moneda', type: 'numeric'},
		{name:'id_movimiento', type: 'numeric'},
		{name:'transporte_cif', type: 'numeric'},
		{name:'correlativo_venta', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'nro_tramite', type: 'string'},
		{name:'tipo_cambio_venta', type: 'numeric'},
		{name:'a_cuenta', type: 'numeric'},
		{name:'contabilizable', type: 'string'},
		{name:'nombre_factura', type: 'string'},
		{name:'excento', type: 'numeric'},
		{name:'valor_bruto', type: 'numeric'},
		{name:'descripcion_bulto', type: 'string'},
		{name:'id_grupo_factura', type: 'numeric'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'nit', type: 'string'},
		{name:'tipo_factura', type: 'string'},
		{name:'seguros_fob', type: 'numeric'},
		{name:'total_venta', type: 'numeric'},
		{name:'forma_pedido', type: 'string'},
		{name:'porcentaje_descuento', type: 'numeric'},
		{name:'hora_estimada_entrega', type: 'string'},
		{name:'id_vendedor_medico', type: 'string'},
		{name:'otros_fob', type: 'numeric'},
		{name:'fecha_estimada_entrega', type: 'date',dateFormat:'Y-m-d'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'nombre_sucursal', type: 'string'},
		{name:'requiere_excento', type: 'string'},
		{name:'excento_verificado', type: 'string'},
		{name:'nombre', type: 'string'},
		{name:'id_formula', type: 'int4'},
		{name:'nro_pnr', type:'strig'},
		{name:'id_auxiliar_anticipo', type:'numeric'},
		{name:'nombre_auxiliar', type:'strig'},
		{name:'codigo_auxiliar', type:'strig'},

	],
	sortInfo:{
		field: 'id_venta',
		direction: 'ASC'
	},

	bdel:true,
	bsave:false,

	cmbPuntoV: new Ext.form.ComboBox({
			name: 'punto_venta',
			id: 'id_punto_venta',
			fieldLabel: 'Punto Venta',
			allowBlank: true,
			emptyText:'Punto de Venta...',
			blankText: 'Año',
			store: new Ext.data.JsonStore({
					url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
					id: 'id_punto_venta',
					root: 'datos',
					sortInfo: {
							field: 'nombre',
							direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_punto_venta', 'id_sucursal','nombre', 'codigo','habilitar_comisiones','formato_comprobante'],
					remoteSort: true,
					baseParams: {par_filtro: 'puve.nombre#puve.codigo', tipo_factura: this.tipo_factura}
			}),
			tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
			valueField: 'id_punto_venta',
			triggerAction: 'all',
			displayField: 'nombre',
			hiddenName: 'id_punto_venta',
			mode:'remote',
			pageSize:50,
			queryDelay:500,
			listWidth:'300',
			hidden:false,
			width:300
	}),

	consultPnrData: function(localizador,porcen, anticipo){
			if (localizador!='' && anticipo==true) {
					Ext.Ajax.request({
							url : '../../sis_obingresos/control/Boleto/consultaReservaBoletoExch',
							params : {
								  'porcentaje_pnr':porcen,
									'pnr' : localizador,
									'anticipo': anticipo
							},
							success: this.successPnr,
							failure : this.conexionFailure,
							timeout : this.timeout,
							scope : this
					});
			}
	},
	successPnr: function(res){
		data = JSON.parse(res.responseText);
		if (data.exito){
			this.Cmp.precio.setValue(data.importe_total);
			this.Cmp.total.setValue(data.importe_total);
			this.Cmp.id_moneda_venta_recibo.setValue(data.id_moneda);
			this.Cmp.id_moneda_venta_recibo.setRawValue(data.moneda);
			this.Cmp.monto_exacto.setValue(data.importe_total);
			this.Cmp.id_moneda_venta_recibo.setDisabled(true);
			this.Cmp.observaciones.setValue(this.Cmp.observaciones.getValue()+' ANTICIPO INICIAL ASOCIADO AL PNR '+this.Cmp.nro_pnr.getValue()+' ');
		}else{
			this.Cmp.precio.setValue(0);
			this.Cmp.total.setValue(null);
			this.Cmp.id_moneda_venta_recibo.setValue(null);
			this.Cmp.id_moneda_venta_recibo.setRawValue(null);
			this.Cmp.monto_exacto.setValue(0);
			this.Cmp.id_moneda_venta_recibo.setDisabled(false);
			Ext.Msg.show({
					title: 'Alerta',
					msg: '<p>No se tiene informacion relacionada con el pnr: '+this.Cmp.nro_pnr.getValue().toUpperCase()+', su registro no puede continuar.</p>',
					buttons: Ext.Msg.OK,
					width: 460,
					icon: Ext.Msg.INFO
			});
		}
	}
	})
</script>
