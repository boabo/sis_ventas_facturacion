<?php
/**
*@package pXP
*@file gen-VentaFacturacion.php
*@author  (ivaldivia)
*@date 10-05-2019 19:08:47
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.VentaFacturacion=Ext.extend(Phx.gridInterfaz,{
	mosttar:'',
	solicitarPuntoVenta: true,

	tabsouth:[
		{
			url:'../../../sis_ventas_facturacion/vista/venta_detalle/VentaDetalleFacturacion.php',
			title:'Detalle Facturacion',
			width:'100%',
			height:'50%',
			cls:'VentaDetalleFacturacion'
		}
	],

	bexcel:false,
	btest:false,

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


			/*Recuperamos de la venta detalle si existe algun concepto con excento*/




			//
			// /**********************************************************************/



	},

	successGetVariables : function (response,request) {

			var respuesta = JSON.parse(response.responseText);
			if('datos' in respuesta){
					this.variables_globales = respuesta.datos;
			}
			if(this.solicitarPuntoVenta){
					this.seleccionarPuntoVentaSucursal();

			}
			Phx.vista.VentaFacturacion.superclass.constructor.call(this,request.arguments);
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

			this.addButton('mod_excento',{
					grupo:[0],
					text :'Modificar Exento',
					iconCls : 'bmoney',
					disabled: true,
					handler : this.modExcento,
					tooltip : '<b>Modificar el valor exento de la venta</b>'
			});

			this.addButton('anular_fact',
					{   grupo:[2],
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


			this.Cmp.id_formula.on('select',function(c,r,i) {
				Ext.Ajax.request({
						url:'../../sis_ventas_facturacion/control/VentaDetalleFacturacion/verificarExcentoFormula',
						params:{id_formula:this.Cmp.id_formula.getValue()},
						success: function(resp){
								var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
								this.requiere_excento = reg.ROOT.datos.v_tiene_excento;
								if (this.requiere_excento == 'si') {
									this.mostrarComponente(this.Cmp.excento);
									this.Cmp.excento.setValue(0);
								}
						},
						failure: this.conexionFailure,
						timeout:this.timeout,
						scope:this
				});
			},this);


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
			this.getBoton('mod_excento').enable();

			if (rec.data.total_venta > 0) {
				this.getBoton('sgt_estado').enable();
			}

			// if (rec.data.excento > 0) {
			// 	this.getBoton('mod_excento').enable();
			// }
			Phx.vista.VentaFacturacion.superclass.preparaMenu.call(this);
		},

		liberaMenu : function(){
				var rec = this.sm.getSelected();
				this.getBoton('anular_fact').disable();
				this.getBoton('sgt_estado').disable();
				this.getBoton('mod_excento').disable();
				Phx.vista.VentaFacturacion.superclass.liberaMenu.call(this);
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
																	this.store.baseParams.tipo_factura = 'computarizada';
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
		this.Cmp.id_formula.store.baseParams.tipo_punto_venta = this.variables_globales.tipo_pv;
		this.Cmp.id_formula.store.baseParams.regional = this.variables_globales.ESTACION_inicio;
		//this.Cmp.id_formula.store.baseParams.id_punto_venta = this.variables_globales.id_punto_venta;
		/*************************************************************/


		/*********Actualizacion automatica*******/
		this.timer_id=Ext.TaskMgr.start({
			 run: Ftimer,
			 interval:3000,
			 scope:this
	 });

	 this.timer_id=Ext.TaskMgr.start({
			run: Ftimer2,
			interval:300000,
			scope:this
	});

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

				//this.reload();
				// Phx.CP.getPagina(this.idContenedor).reload();
			//	this.load({params:{start:0, limit:this.tam_pag}});
		}
	}

	function Ftimer2(){
			 if (this.variables_globales.id_punto_venta != '') {
						 this.reload();
				 }
			 }
/************************************************/

	},

		// onButtonAct:function () {
		// 	this.iniciarEventos();
		// 	this.reload();
		// },

		onButtonNew:function () {
			Phx.vista.VentaFacturacion.superclass.onButtonNew.call(this);
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

			/*Comentando Para que solo muestre el cliente del nit*/
			// this.Cmp.id_cliente.on('select',function(c,r,i) {
			// 		if (r.data) {
			// 				this.Cmp.nit.setValue(r.data.nit);
			// 		} else {
			// 				this.Cmp.nit.setValue(r.nit);
			// 		}
			// },this);
			/******************************************************/
			this.Cmp.nit.on('blur',function(c) {
				if (this.Cmp.nit.getValue() != '') {
					this.Cmp.nombre_factura.reset();
					Ext.Ajax.request({
							url : '../../sis_ventas_facturacion/control/VentaFacturacion/RecuperarCliente',
							params : {
								'nit' : this.Cmp.nit.getValue(),
								'razon_social' : this.Cmp.nombre_factura.getValue(),
							},
							success: function(resp){
	                var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
									this.Cmp.nombre_factura.setValue(reg.ROOT.datos.razon);
	                this.Cmp.id_cliente.setValue(reg.ROOT.datos.id_cliente);
	            },
							failure : this.conexionFailure,
							timeout : this.timeout,
							scope : this
						});
				}

				// if (this.Cmp.nit.getValue() != '') {
				// 	this.Cmp.id_cliente.store.baseParams.nit = this.Cmp.nit.getValue();
				// 		this.Cmp.id_cliente.store.load({params:{start:0,limit:1},
				// 		 callback : function (r) {
				// 				this.Cmp.id_cliente.store.baseParams.nit = '';
				// 				if (r.length == 1) {
				// 					this.Cmp.id_cliente.setValue(r[0].data.id_cliente);
				// 					}
				//
				// 			}, scope : this
				// 	});
				// }


			},this);


			// this.Cmp.nombre_factura.on('blur',function(c) {
			// 	if (this.Cmp.nombre_factura.getValue() != '') {
			// 		Ext.Ajax.request({
			// 				url : '../../sis_ventas_facturacion/control/VentaFacturacion/RecuperarCliente',
			// 				params : {
			// 					'nit' : this.Cmp.nit.getValue(),
			// 					'razon_social' : this.Cmp.nombre_factura.getValue(),
			// 				},
			// 				success: function(resp){
	    //             var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
			// 						this.Cmp.nit.setValue(reg.ROOT.datos.nit);
	    //             this.Cmp.id_cliente.setValue(reg.ROOT.datos.id_cliente);
	    //         },
			// 				failure : this.conexionFailure,
			// 				timeout : this.timeout,
			// 				scope : this
			// 			});
			// 	}
			// },this);

			this.ocultarComponente(this.Cmp.excento);

		},
			onButtonEdit:function () {
				Phx.vista.VentaFacturacion.superclass.onButtonEdit.call(this);
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
				this.Cmp.id_formula.store.load({params:{start:0,limit:50},
					 callback : function (r) {
							this.Cmp.id_formula.setValue(rec.data.id_formula);
								this.Cmp.id_formula.fireEvent('select',this.Cmp.id_formula, this.Cmp.id_formula.store.getById(rec.data.id_formula));
						}, scope : this
				});

				this.Cmp.nit.on('blur',function(c) {
					if (this.Cmp.nit.getValue() != '') {
						Ext.Ajax.request({
								url : '../../sis_ventas_facturacion/control/VentaFacturacion/RecuperarCliente',
								params : {
									'nit' : this.Cmp.nit.getValue(),
									'razon_social' : this.Cmp.nombre_factura.getValue(),
								},
								success: function(resp){
		                var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
										this.Cmp.nombre_factura.setValue(reg.ROOT.datos.razon);
		                this.Cmp.id_cliente.setValue(reg.ROOT.datos.id_cliente);
		            },
								failure : this.conexionFailure,
								timeout : this.timeout,
								scope : this
							});
					}
				},this);

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
				 resp.argument.wizard.panel.destroy();

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
				//console.log("llega auqi datos de la ventana",this);
				// if (this.excento_formulario.items.items[0].getValue() == '' || this.excento_formulario.items.items[0].getValue() == 0) {
				// 		Ext.Msg.show({
				// 		 title:'<h1 style="font-size:15px;">Aviso!</h1>',
				// 		 msg: '<p style="font-weight:bold; font-size:12px;">Tiene un concepto que requiere excento y el valor excento no debe ser vacio o 0!</p>',
				// 		 buttons: Ext.Msg.OK,
				// 		 width:320,
		 		// 		 height:150,
				// 		 icon: Ext.MessageBox.WARNING,
				// 		 scope:this
				// 	});
				// } else {
				this.guardarDetalles();
				win.hide();
			//	}

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
				name: 'fecha',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/calendario.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Fecha Factura</span>',
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
 			 fieldLabel: '<img src="../../../lib/imagenes/facturacion/listaNumeros.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Nro Factura</span>',
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
	 {
		 config:{
			 name: 'cod_control',
			 fieldLabel: '<img src="../../../lib/imagenes/facturacion/Codigo.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Codigo Control</span>',
			 allowBlank: true,
			 anchor:'100%',
			 gwidth: 150,
			 maxLength:15
		 },
			 type:'TextField',
			 filters:{pfiltro:'ven.cod_control',type:'string'},
			 id_grupo:1,
			 grid:true,
			 form:false
	 },
		{
			config:{
				name: 'nit',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/CarnetIdentidad.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> NIT</span>',
				allowBlank: false,
				anchor:'100%',
				gwidth: 100,
				maxLength:25
			},
				type:'TextField',
				filters:{pfiltro:'fact.nit',type:'string'},
				id_grupo:1,
				grid:true,
				bottom_filter: true,
				form:true
		},
		{
 		 config:{
 			 name: 'nombre_factura',
 			 fieldLabel: '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Razón Social</span>',
 			 allowBlank: true,
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
			 fieldLabel: '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Total Venta</span>',
			 allowBlank: false,
			 anchor:'100%',
			 gwidth: 100,
			 renderer:function (value,p,record) {
					var dato =  value.replace('.', ",")
									.replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");

					if (value>0) {
						console.log("entra aqui 1");
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="blue"><b>'+dato+'</b></font></p></div>';
					} else {
						console.log("entra aqui 222222");
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
		// {
 		//  config : {
 		// 	 name : 'id_cliente',
 		// 	 fieldLabel : '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Razón Social</span>',
 		// 	 style:'text-transform:uppercase;',
 		// 	 anchor:'100%',
 		// 	 allowBlank : false,
 		// 	 emptyText : 'Cliente...',
 		// 	 store : new Ext.data.JsonStore({
 		// 		 url : '../../sis_ventas_facturacion/control/Cliente/listarCliente',
 		// 		 id : 'id_cliente',
 		// 		 root : 'datos',
 		// 		 sortInfo : {
 		// 			 field : 'nombres',
 		// 			 direction : 'ASC'
 		// 		 },
 		// 		 totalProperty : 'total',
 		// 		 fields : ['id_cliente', 'nombres', 'primer_apellido', 'segundo_apellido','nombre_factura','nit'],
 		// 		 remoteSort : true,
 		// 		 baseParams : {
 		// 			 par_filtro : 'cli.nombres#cli.primer_apellido#cli.segundo_apellido#nombre_factura#nit'
 		// 		 }
 		// 	 }),
 		// 	 valueField : 'id_cliente',
 		// 	 displayField : 'nombre_factura',
 		// 	 gdisplayField : 'nombre_factura',
 		// 	 hiddenName : 'id_cliente',
 		// 	 forceSelection : false,
 		// 	 typeAhead : false,
		// 	 tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:red;"><b style="color:black;">NIT:</b> <b>{nit}</b></p><b><p>Cliente:<font color="#000CFF" weight="bold"> {nombre_factura}</font></b></p></div></tpl>',
		// 	 triggerAction : 'all',
 		// 	 lazyRender : true,
 		// 	 mode : 'remote',
 		// 	 pageSize : 10,
 		// 	 listWidth:'450',
 		// 	 maxHeight : 450,
 		// 	 queryDelay : 1000,
 		// 	 turl:'../../../sis_ventas_facturacion/vista/cliente/Cliente.php',
 		// 	 ttitle:'Clientes',
 		// 	 tasignacion : true,
 		// 	 tname : 'id_cliente',
 		// 	 tdata:{},
 		// 	 cls:'uppercase',
 		// 	 tcls:'Cliente',
 		// 	 gwidth : 170,
 		// 	 minChars : 2,
 		// 	 //style:';'
 		//  },
 		//  type : 'TrigguerCombo',
 		//  id_grupo : 0,
 		//  form : true
 	 // },
	 {
		 config:{
			 name: 'observaciones',
			 fieldLabel: '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Observaciones</span>',
			 allowBlank: false,
			 anchor:'100%',
			 gwidth: 200,
			 style:'text-transform:uppercase;',
			 minLength:12
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
			 tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Nombre:</b> {nombre}</p><p><b>Descripcion:</b> {descripcion}</p></div></tpl>',
			 triggerAction : 'all',
			 lazyRender : true,
			 mode : 'remote',
			 pageSize : 10,
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
			 style:'text-transform:uppercase;'
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

		// {
		// 	config: {
		// 		name: 'id_usuario_cajero',
		// 		fieldLabel: 'id_usuario_cajero',
		// 		allowBlank: true,
		// 		emptyText: 'Elija una opción...',
		// 		store: new Ext.data.JsonStore({
		// 			url: '../../sis_/control/Clase/Metodo',
		// 			id: 'id_',
		// 			root: 'datos',
		// 			sortInfo: {
		// 				field: 'nombre',
		// 				direction: 'ASC'
		// 			},
		// 			totalProperty: 'total',
		// 			fields: ['id_', 'nombre', 'codigo'],
		// 			remoteSort: true,
		// 			baseParams: {par_filtro: 'movtip.nombre#movtip.codigo'}
		// 		}),
		// 		valueField: 'id_',
		// 		displayField: 'nombre',
		// 		gdisplayField: 'desc_',
		// 		hiddenName: 'id_usuario_cajero',
		// 		forceSelection: true,
		// 		typeAhead: false,
		// 		triggerAction: 'all',
		// 		lazyRender: true,
		// 		mode: 'remote',
		// 		pageSize: 15,
		// 		queryDelay: 1000,
		// 		anchor: '100%',
		// 		gwidth: 150,
		// 		minChars: 2,
		// 		renderer : function(value, p, record) {
		// 			return String.format('{0}', record.data['desc_']);
		// 		}
		// 	},
		// 	type: 'ComboBox',
		// 	id_grupo: 0,
		// 	filters: {pfiltro: 'movtip.nombre',type: 'string'},
		// 	grid: true,
		// 	form: true
		// },


		// {
		// 	config:{
		// 		name: 'total_venta_msuc',
		// 		fieldLabel: 'total_venta_msuc',
		// 		allowBlank: true,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:1179650
		// 	},
		// 		type:'NumberField',
		// 		filters:{pfiltro:'fact.total_venta_msuc',type:'numeric'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
		// {
		// 	config:{
		// 		name: 'otros_cif',
		// 		fieldLabel: 'otros_cif',
		// 		allowBlank: false,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:1179650
		// 	},
		// 		type:'NumberField',
		// 		filters:{pfiltro:'fact.otros_cif',type:'numeric'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },


		// {
		// 	config:{
		// 		name: 'seguros_cif',
		// 		fieldLabel: 'seguros_cif',
		// 		allowBlank: false,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:1179650
		// 	},
		// 		type:'NumberField',
		// 		filters:{pfiltro:'fact.seguros_cif',type:'numeric'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
		// {
		// 	config:{
		// 		name: 'comision',
		// 		fieldLabel: 'Comisión',
		// 		allowBlank: true,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:1179650
		// 	},
		// 		type:'NumberField',
		// 		filters:{pfiltro:'fact.comision',type:'numeric'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:false
		// },
		// {
		// 	config: {
		// 		name: 'id_moneda',
		// 		fieldLabel: 'id_moneda',
		// 		allowBlank: true,
		// 		emptyText: 'Elija una opción...',
		// 		store: new Ext.data.JsonStore({
		// 			url: '../../sis_/control/Clase/Metodo',
		// 			id: 'id_',
		// 			root: 'datos',
		// 			sortInfo: {
		// 				field: 'nombre',
		// 				direction: 'ASC'
		// 			},
		// 			totalProperty: 'total',
		// 			fields: ['id_', 'nombre', 'codigo'],
		// 			remoteSort: true,
		// 			baseParams: {par_filtro: 'movtip.nombre#movtip.codigo'}
		// 		}),
		// 		valueField: 'id_',
		// 		displayField: 'nombre',
		// 		gdisplayField: 'desc_',
		// 		hiddenName: 'id_moneda',
		// 		forceSelection: true,
		// 		typeAhead: false,
		// 		triggerAction: 'all',
		// 		lazyRender: true,
		// 		mode: 'remote',
		// 		pageSize: 15,
		// 		queryDelay: 1000,
		// 		anchor: '100%',
		// 		gwidth: 150,
		// 		minChars: 2,
		// 		renderer : function(value, p, record) {
		// 			return String.format('{0}', record.data['desc_']);
		// 		}
		// 	},
		// 	type: 'ComboBox',
		// 	id_grupo: 0,
		// 	filters: {pfiltro: 'movtip.nombre',type: 'string'},
		// 	grid: true,
		// 	form: true
		// },
		// {
		// 	config: {
		// 		name: 'id_movimiento',
		// 		fieldLabel: 'id_movimiento',
		// 		allowBlank: true,
		// 		emptyText: 'Elija una opción...',
		// 		store: new Ext.data.JsonStore({
		// 			url: '../../sis_/control/Clase/Metodo',
		// 			id: 'id_',
		// 			root: 'datos',
		// 			sortInfo: {
		// 				field: 'nombre',
		// 				direction: 'ASC'
		// 			},
		// 			totalProperty: 'total',
		// 			fields: ['id_', 'nombre', 'codigo'],
		// 			remoteSort: true,
		// 			baseParams: {par_filtro: 'movtip.nombre#movtip.codigo'}
		// 		}),
		// 		valueField: 'id_',
		// 		displayField: 'nombre',
		// 		gdisplayField: 'desc_',
		// 		hiddenName: 'id_movimiento',
		// 		forceSelection: true,
		// 		typeAhead: false,
		// 		triggerAction: 'all',
		// 		lazyRender: true,
		// 		mode: 'remote',
		// 		pageSize: 15,
		// 		queryDelay: 1000,
		// 		anchor: '100%',
		// 		gwidth: 150,
		// 		minChars: 2,
		// 		renderer : function(value, p, record) {
		// 			return String.format('{0}', record.data['desc_']);
		// 		}
		// 	},
		// 	type: 'ComboBox',
		// 	id_grupo: 0,
		// 	filters: {pfiltro: 'movtip.nombre',type: 'string'},
		// 	grid: true,
		// 	form: true
		// },
		// {
		// 	config:{
		// 		name: 'transporte_cif',
		// 		fieldLabel: 'transporte_cif',
		// 		allowBlank: false,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:1179650
		// 	},
		// 		type:'NumberField',
		// 		filters:{pfiltro:'fact.transporte_cif',type:'numeric'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
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
		// {
		// 	config:{
		// 		name: 'nro_tramite',
		// 		fieldLabel: 'nro_tramite',
		// 		allowBlank: false,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:-5
		// 	},
		// 		type:'TextField',
		// 		filters:{pfiltro:'fact.nro_tramite',type:'string'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
		// {
		// 	config:{
		// 		name: 'tipo_cambio_venta',
		// 		fieldLabel: 'tipo_cambio_venta',
		// 		allowBlank: false,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:-5
		// 	},
		// 		type:'NumberField',
		// 		filters:{pfiltro:'fact.tipo_cambio_venta',type:'numeric'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
		// {
		// 	config:{
		// 		name: 'a_cuenta',
		// 		fieldLabel: 'a_cuenta',
		// 		allowBlank: false,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:1179650
		// 	},
		// 		type:'NumberField',
		// 		filters:{pfiltro:'fact.a_cuenta',type:'numeric'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
		// {
		// 	config:{
		// 		name: 'contabilizable',
		// 		fieldLabel: 'contabilizable',
		// 		allowBlank: false,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:2
		// 	},
		// 		type:'TextField',
		// 		filters:{pfiltro:'fact.contabilizable',type:'string'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },

		// {
		// 	config:{
		// 		name: 'valor_bruto',
		// 		fieldLabel: 'valor_bruto',
		// 		allowBlank: false,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:1179650
		// 	},
		// 		type:'NumberField',
		// 		filters:{pfiltro:'fact.valor_bruto',type:'numeric'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
		// {
		// 	config:{
		// 		name: 'descripcion_bulto',
		// 		fieldLabel: 'descripcion_bulto',
		// 		allowBlank: false,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:1000
		// 	},
		// 		type:'TextField',
		// 		filters:{pfiltro:'fact.descripcion_bulto',type:'string'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
		// {
		// 	config: {
		// 		name: 'id_grupo_factura',
		// 		fieldLabel: 'id_grupo_factura',
		// 		allowBlank: true,
		// 		emptyText: 'Elija una opción...',
		// 		store: new Ext.data.JsonStore({
		// 			url: '../../sis_/control/Clase/Metodo',
		// 			id: 'id_',
		// 			root: 'datos',
		// 			sortInfo: {
		// 				field: 'nombre',
		// 				direction: 'ASC'
		// 			},
		// 			totalProperty: 'total',
		// 			fields: ['id_', 'nombre', 'codigo'],
		// 			remoteSort: true,
		// 			baseParams: {par_filtro: 'movtip.nombre#movtip.codigo'}
		// 		}),
		// 		valueField: 'id_',
		// 		displayField: 'nombre',
		// 		gdisplayField: 'desc_',
		// 		hiddenName: 'id_grupo_factura',
		// 		forceSelection: true,
		// 		typeAhead: false,
		// 		triggerAction: 'all',
		// 		lazyRender: true,
		// 		mode: 'remote',
		// 		pageSize: 15,
		// 		queryDelay: 1000,
		// 		anchor: '100%',
		// 		gwidth: 150,
		// 		minChars: 2,
		// 		renderer : function(value, p, record) {
		// 			return String.format('{0}', record.data['desc_']);
		// 		}
		// 	},
		// 	type: 'ComboBox',
		// 	id_grupo: 0,
		// 	filters: {pfiltro: 'movtip.nombre',type: 'string'},
		// 	grid: true,
		// 	form: true
		// },

		{
			config:{
				name: 'tipo_factura',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/PcEscritorio.svg" style="width:20px; vertical-align: middle;">   <img src="../../../lib/imagenes/facturacion/LapizPapel.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Tipo Factura</span>',
				allowBlank: false,
				anchor:'100%',
				gwidth: 200,
				maxLength:20
			},
				type:'TextField',
				filters:{pfiltro:'fact.tipo_factura',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		// {
		// 	config:{
		// 		name: 'seguros_fob',
		// 		fieldLabel: 'seguros_fob',
		// 		allowBlank: false,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:1179650
		// 	},
		// 		type:'NumberField',
		// 		filters:{pfiltro:'fact.seguros_fob',type:'numeric'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
		// {
		// 	//configuracion del componente
		// 	config:{
		// 		labelSeparator:'',
		// 		inputType:'hidden',
		// 		name: 'forma_pedido'
		// 	},
		// 	valorInicial:'vendedor',
		// 	type:'Field',
		// 	form:true
		// },
		// {
		// 	config:{
		// 		name: 'forma_pedido',
		// 		fieldLabel: 'forma_pedido',
		// 		allowBlank: true,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:200
		// 	},
		// 		type:'TextField',
		// 		filters:{pfiltro:'fact.forma_pedido',type:'string'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
		// {
		// 	config:{
		// 		name: 'porcentaje_descuento',
		// 		fieldLabel: 'porcentaje_descuento',
		// 		allowBlank: true,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:327680
		// 	},
		// 		type:'NumberField',
		// 		filters:{pfiltro:'fact.porcentaje_descuento',type:'numeric'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
		// {
		// 	config:{
		// 		name: 'hora_estimada_entrega',
		// 		fieldLabel: 'hora_estimada_entrega',
		// 		allowBlank: true,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:8
		// 	},
		// 		type:'TextField',
		// 		filters:{pfiltro:'fact.hora_estimada_entrega',type:'string'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
		// {
		// 	config: {
		// 		name: 'id_vendedor_medico',
		// 		fieldLabel: 'id_vendedor_medico',
		// 		allowBlank: true,
		// 		emptyText: 'Elija una opción...',
		// 		store: new Ext.data.JsonStore({
		// 			url: '../../sis_/control/Clase/Metodo',
		// 			id: 'id_',
		// 			root: 'datos',
		// 			sortInfo: {
		// 				field: 'nombre',
		// 				direction: 'ASC'
		// 			},
		// 			totalProperty: 'total',
		// 			fields: ['id_', 'nombre', 'codigo'],
		// 			remoteSort: true,
		// 			baseParams: {par_filtro: 'movtip.nombre#movtip.codigo'}
		// 		}),
		// 		valueField: 'id_',
		// 		displayField: 'nombre',
		// 		gdisplayField: 'desc_',
		// 		hiddenName: 'id_vendedor_medico',
		// 		forceSelection: true,
		// 		typeAhead: false,
		// 		triggerAction: 'all',
		// 		lazyRender: true,
		// 		mode: 'remote',
		// 		pageSize: 15,
		// 		queryDelay: 1000,
		// 		anchor: '100%',
		// 		gwidth: 150,
		// 		minChars: 2,
		// 		renderer : function(value, p, record) {
		// 			return String.format('{0}', record.data['desc_']);
		// 		}
		// 	},
		// 	type: 'ComboBox',
		// 	id_grupo: 0,
		// 	filters: {pfiltro: 'movtip.nombre',type: 'string'},
		// 	grid: true,
		// 	form: true
		// },
		// {
		// 	config:{
		// 		name: 'otros_fob',
		// 		fieldLabel: 'otros_fob',
		// 		allowBlank: false,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 		maxLength:1179650
		// 	},
		// 		type:'NumberField',
		// 		filters:{pfiltro:'fact.otros_fob',type:'numeric'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
		// {
		// 	config:{
		// 		name: 'fecha_estimada_entrega',
		// 		fieldLabel: 'fecha_estimada_entrega',
		// 		allowBlank: false,
		// 		anchor:'100%',
		// 		gwidth: 100,
		// 					format: 'd/m/Y',
		// 					renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
		// 	},
		// 		type:'DateField',
		// 		filters:{pfiltro:'fact.fecha_estimada_entrega',type:'date'},
		// 		id_grupo:1,
		// 		grid:true,
		// 		form:true
		// },
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
	title:'<center><img src="../../../lib/imagenes/facturacion/BolsaCompraColores.svg" style="width:20px; vertical-align: middle;"> <span style="vertical-align: middle; color:#008CB2; font-size:20px; text-shadow: 2px 0px 0px #000000;"> CABECERA FACTURA</span></center>',
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
		{name:'id_formula', type: 'numeric'},

	],
	sortInfo:{
		field: 'id_venta',
		direction: 'ASC'
	},

	bdel:true,
	bsave:true,

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

	}
)
</script>
