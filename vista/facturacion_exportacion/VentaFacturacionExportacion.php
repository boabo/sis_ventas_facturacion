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
<style>
.button-anular-red{
    background-image: url('../../../lib/imagenes/icono_dibu/anulared.png');
    background-repeat: no-repeat;
    filter: saturate(250%);
    background-size: 80%;
}
.button-impcarta{
	background-image: url('../../../lib/imagenes/icono_inc/inc_printer.png');
	background-repeat: no-repeat;
	filter: saturate(250%);
	background-size: 50%;
}

</style>
<script>
Phx.vista.VentaFacturacionExportacion=Ext.extend(Phx.gridInterfaz,{
	mosttar:'',
	solicitarPuntoVenta: true,
	mgs_user: '<p>Estimado Usuario no se puede anular facturas anteriores al día actual Favor consulte con el administrador</p>',
	mgs_user_impresion: '<p>Estimado Usuario no se puede reimprimir facturas anteriores al día actual Favor consulte con el administrador</p>',
	/***Aqui para el formulario de datos***/
	formUrl: '../../../sis_ventas_facturacion/vista/facturacion_exportacion/FormFacturacionExportacion.php',
	formClass : 'FormFacturacionExportacion',
  //tipo_factura: 'recibo',
  //nombreVista: 'ReciboLista',
	solicitarSucursal: true, //para indicar si es forzoso o no indicar la sucrsal al iniciar
	tipo_usuario : 'cajero',
  /**************************************/




	tabsouth:[
		{
			url:'../../../sis_ventas_facturacion/vista/facturacion_exportacion/VentaFacturacionExportacionDetalle.php',
			title:'Detalle Facturacion',
			height:'40%',
			cls:'VentaFacturacionExportacionDetalle'
		},
		{
			url:'../../../sis_ventas_facturacion/vista/facturacion_exportacion/VentaFacturacionExportacionFormasPago.php',
			title:'Formas de Pago',
			height:'40%',
			cls:'VentaFacturacionExportacionFormasPago'
		},
	],



	// tabeast:[
	// 	{
	// 		url:'../../../sis_ventas_facturacion/vista/facturacion_exportacion/VentaFacturacionExportacionDetalle.php',
	// 		title:'Detalle Facturacion',
	// 		width:'50%',
	// 		cls:'VentaFacturacionExportacionDetalle'
	// 	}
	// ],
	//
	//
	// south:
	// 	{
	// 		url:'../../../sis_ventas_facturacion/vista/facturacion_exportacion/VentaFacturacionExportacionFormasPago.php',
	// 		title:'Formas de Pago',
	// 		height:'50%',
	// 		cls:'VentaFacturacionExportacionFormasPago'
	// 	},


	bexcel:false,
	btest:false,

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
			if(this.solicitarPuntoVenta){
					this.seleccionarPuntoVentaSucursal();

			}
			Phx.vista.VentaFacturacionExportacion.superclass.constructor.call(this,request.arguments);
			this.store.baseParams.tipo_usuario = 'cajero';
			this.store.baseParams.pes_estado = 'finalizado';


			this.addButton('btn_imprimir_factura',{
         grupo: [2],
         text: 'Imprimir Factura',
         iconCls: 'button-impcarta',
         disabled: true,
         handler: this.onReporteEmision,
         tooltip: '<b>Imprimir Factura</b>',
         scope:this
     });

		 this.addButton('anular_fact',
 				{   grupo:[2],
 						text: '<b>Anular</b>',
 						iconCls: 'button-anular-red',
 						disabled: true,
 						handler: this.anular,
 						tooltip: '<b>Anular Factura</b>'
 				}
 		);

			this.init();

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
			this.bbar.el.dom.style.background='radial-gradient(ellipse at center, #cfe7fa 0%,#6393c1 100%)';
			this.tbar.el.dom.style.background='radial-gradient(ellipse at center, #cfe7fa 0%,#6393c1 100%)';
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

	gruposBarraTareas:[
											{name:'finalizado',title:'<H1 style="font-size:12px;" align="center"><i style="color:#B61BFF; font-size:15px;" class="fa fa-check-circle"></i> Emitidos</h1>',grupo:2,height:0},
											//{name:'borrador',title:'<H1 style="font-size:12px;" align="center"><i style="color:#FFAE00; font-size:15px;" class="fa fa-eraser"></i> En Registro</h1>',grupo:0,height:0},
											 //{name:'caja',title:'<H1 style="font-size:12px;" align="center"><i style="color:green; font-size:15px;" class="fa fa-usd"></i> En Caja</h1>',grupo:1,height:0},
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
			Phx.vista.VentaFacturacionExportacion.superclass.preparaMenu.call(this);

			this.getBoton('btn_imprimir_factura').setVisible(true);
			this.getBoton('btn_imprimir_factura').enable();

			this.getBoton('anular_fact').setVisible(true);
			this.getBoton('anular_fact').enable();

		},

		liberaMenu : function(){
				var rec = this.sm.getSelected();
				Phx.vista.VentaFacturacionExportacion.superclass.liberaMenu.call(this);
		},


	 bactGroups:  [0,1,2,3],
	 btestGroups: [0,1,2,3],
	 bexcelGroups: [1],
	 bnewGroups:[2],

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
								VentanaInicio.body.dom.childNodes[0].firstChild.firstChild.style.background='radial-gradient(ellipse at center, #cfe7fa 0%,#6393c1 100%)';
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

	},


		onButtonNew:function () {
			this.openForm('new');
		},
			onButtonEdit:function () {
				Phx.vista.VentaFacturacionExportacion.superclass.onButtonEdit.call(this);
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




			onReporteEmision : function() {
				var rec = this.sm.getSelected();
				Ext.Ajax.request({
						url:'../../sis_ventas_facturacion/control/Cajero/getTipoUsuario',
						params: {'vista':'cajero'},
						success: function(resp){
								var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
								this.tipo_usuario = reg.ROOT.datos.v_tipo_usuario;

								var date = new Date()

								var dia = date.getDate();
								var mes = date.getMonth() + 1;
								var ano = date.getFullYear();

								if(mes < 10){
									if(dia < 10){
										var fecha_hoy = "0"+dia + "/0" + mes + "/" + ano
									}else{
										var fecha_hoy = dia + "/0" + mes + "/" + ano
									}
								}else{
									var fecha_hoy = dia + "/" + mes + "/" + ano
								}

								if((rec.data.fecha.dateFormat('d/m/Y') != fecha_hoy ) && (this.tipo_usuario != 'administrador_facturacion')){
									Ext.Msg.show({
											title: 'Alerta',
											msg: this.mgs_user_impresion,
											buttons: Ext.Msg.OK,
											width: 512,
											icon: Ext.Msg.INFO
									});
								}else{
									Phx.CP.loadingShow();
									Ext.Ajax.request({
						          url : '../../sis_ventas_facturacion/control/FacturacionExportacion/imprimirFactura',
						          params : {
												'id_venta': rec.data.id_venta
						          },
						          success : this.successExport,
						          failure : this.conexionFailure,
						          timeout : this.timeout,
						          scope : this
						        });
								}






						},
						failure: this.conexionFailure,
						timeout:this.timeout,
						scope:this
				});


	    },

			anular : function () {

				Ext.Ajax.request({
						url:'../../sis_ventas_facturacion/control/Cajero/getTipoUsuario',
						params: {'vista':'cajero'},
						success: function(resp){
								var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
								this.tipo_usuario = reg.ROOT.datos.v_tipo_usuario;

								var rec=this.sm.getSelected();
								var me= this;
				        var date = new Date()

				        var dia = date.getDate();
				        var mes = date.getMonth() + 1;
				        var ano = date.getFullYear();

				        if(mes < 10){
				          if(dia < 10){
				            var fecha_hoy = "0"+dia + "/0" + mes + "/" + ano
				          }else{
				            var fecha_hoy = dia + "/0" + mes + "/" + ano
				          }
				        }else{
				          var fecha_hoy = dia + "/" + mes + "/" + ano
				        }
								if((rec.data.fecha.dateFormat('d/m/Y') != fecha_hoy) && (this.tipo_usuario != 'administrador_facturacion')){
									Ext.Msg.show({
											title: 'Alerta',
											msg: this.mgs_user,
											buttons: Ext.Msg.OK,
											width: 512,
											icon: Ext.Msg.INFO
									});
								}else{
								Ext.Msg.confirm(
										'Mensaje de Confirmación',
										'Esta Seguro de Anular la Factura',
										function(btn) {
												if (btn == 'yes'){
														Phx.CP.loadingShow();
												        Ext.Ajax.request({
												            url:'../../sis_ventas_facturacion/control/Cajero/anularFactura',
												            params:{
												                id_venta:  rec.data.id_venta
												                },
												            success:me.successSave,
												            failure: me.conexionFailure,
												            timeout:me.timeout,
												            scope:me
												        });
										}}
								);
							}




						},
						failure: this.conexionFailure,
						timeout:this.timeout,
						scope:this
				});
			},


			openForm : function (tipo, record) {
	    	var me = this;
	           me.objSolForm = Phx.CP.loadWindows(this.formUrl,'',
	                                    // '<center> <span > FACTURACIÓN COMPUTARIZADA</span></center>',
	                                    {
	                                        modal:true,
	                                        width:'100%',
	                                        height:'100%',
																					onEsc: function() {
																			    var me = this;
																			    Ext.Msg.confirm(
																			        'Mensaje de Confirmación',
																			        'Quiere cerrar el Formulario?, se perderán los datos que no han sido Guardados',
																			        function(btn) {
																			            if (btn == 'yes')
																			                me.hide();
																			        }
																			        );
																			},
	                                    }, {data:{objPadre : me,
	                                    		tipo_form : tipo,
	                                    		datos_originales: record,
					   							readOnly : this.readOnly}
	                                    },
	                                    this.idContenedor,
	                                    this.formClass,
	                                    {
	                                        config:[{
	                                                  event:'successsave',
	                                                  delegate: this.onSaveForm,

	                                                }],

	                                        scope:this
	                                     });
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
				fieldLabel: 'Fecha Factura',
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
	 		 config: {
	 				 name: 'id_moneda_venta',
	 				 fieldLabel: 'Moneda de la Transacción',
	 				 allowBlank: true,
	 				 anchor:'80%',
	 				 //listWidth:250,
	 				 resizable:true,
	 				 gwidth: 150,
	 				 emptyText: 'Moneda a pagar...',
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
	 				 gdisplayField : 'desc_moneda',
	 				 displayField: 'codigo_internacional',
	 				 hiddenName: 'id_moneda',
	 				 tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;"><b style="color:black;">Moneda:</b> <b>{moneda}</b></p><p style="color:red;"><b style="color:black;">Código:</b> <b>{codigo_internacional}</b></p></div></tpl>',
	 				 forceSelection: true,
	 				 typeAhead: false,
	 				 triggerAction: 'all',
	 				 lazyRender: true,
	 				 mode: 'remote',
	 				 pageSize: 15,
	 				 queryDelay: 1000,
	 				 //disabled:true,
	 				 minChars: 2
	 		 },
	 		 type: 'ComboBox',
	 		 id_grupo: 0,
	 		 form: true,
	 		 grid: true
	  },
		{
 		 config:{
 			 name: 'nro_factura',
 			 fieldLabel: 'Nro Factura',
 			 allowBlank: true,
 			 anchor:'100%',
 			 gwidth: 120,
 			 maxLength:4
 		 },
 			 type:'NumberField',
 			 filters:{pfiltro:'fact.nro_factura',type:'numeric'},
 			 id_grupo:1,
			 bottom_filter: true,
 			 grid:true,
 			 form:false
 	 },
	 {
		 config:{
			 name: 'cod_control',
			 fieldLabel: 'Codigo Control',
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
				fieldLabel: 'NIT',
				allowBlank: false,
				anchor:'80%',
				gwidth: 100,
				maxLength:25
			},
				type:'NumberField',
				filters:{pfiltro:'fact.nit',type:'string'},
				id_grupo:0,
				grid:true,
				bottom_filter: true,
				form:true
		},
		{
 		 config:{
 			 name: 'nombre_factura',
 			 fieldLabel: 'Razón Social',
 			 allowBlank: false,
 			 anchor:'80%',
 			 gwidth: 150,
 			 maxLength:100,
			 style:'text-transform:uppercase'
 		 },
 			 type:'TextField',
 			 filters:{pfiltro:'fact.nombre_factura',type:'string'},
 			 id_grupo:0,
 			 grid:true,
 			 bottom_filter:true,
 			 form:true
 	 },

	 {
		 config:{
			 name: 'total_venta',
			 fieldLabel: 'Total Venta',
			 allowBlank: false,
			 anchor:'100%',
			 gwidth: 100,
			 renderer:function (value,p,record) {
					var dato =  value.replace('.', ",")
									.replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");

					if (value>0) {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="green"><b>'+dato+'</b></font></p></div>';
					} else {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="red"><b>'+dato+'</b></font></p></div>';
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

	 /*Aqui para mostrar los datos de exportacion*/
	 {
		 config:{
			 name: 'valor_bruto',
			 fieldLabel: 'Valor Bruto',
			 allowBlank: false,
			 anchor:'100%',
			 gwidth: 100,
			 renderer:function (value,p,record) {
					var dato =  value.replace('.', ",")
									.replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");

					if (value>0) {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="blue"><b>'+dato+'</b></font></p></div>';
					} else {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="red"><b>'+dato+'</b></font></p></div>';
					}



				},
			 decimalPrecision:2,
			 maxLength:1179650
		 },
			 type:'NumberField',
			 filters:{pfiltro:'fact.valor_bruto',type:'numeric'},
			 id_grupo:1,
			 grid:true,
			 form:false
	 },

	 {
		 config:{
			 name: 'transporte_fob',
			 fieldLabel: 'Tranporte FOB',
			 allowBlank: false,
			 anchor:'100%',
			 gwidth: 100,
			 renderer:function (value,p,record) {
					var dato =  value.replace('.', ",")
									.replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");

					if (value>0) {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="blue"><b>'+dato+'</b></font></p></div>';
					} else {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="red"><b>'+dato+'</b></font></p></div>';
					}



				},
			 decimalPrecision:2,
			 maxLength:1179650
		 },
			 type:'NumberField',
			 filters:{pfiltro:'fact.transporte_fob',type:'numeric'},
			 id_grupo:1,
			 grid:true,
			 form:false
	 },

	 {
		 config:{
			 name: 'seguros_fob',
			 fieldLabel: 'Seguros FOB',
			 allowBlank: false,
			 anchor:'100%',
			 gwidth: 100,
			 renderer:function (value,p,record) {
					var dato =  value.replace('.', ",")
									.replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");

					if (value>0) {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="blue"><b>'+dato+'</b></font></p></div>';
					} else {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="red"><b>'+dato+'</b></font></p></div>';
					}



				},
			 decimalPrecision:2,
			 maxLength:1179650
		 },
			 type:'NumberField',
			 filters:{pfiltro:'fact.seguros_fob',type:'numeric'},
			 id_grupo:1,
			 grid:true,
			 form:false
	 },

	 {
		 config:{
			 name: 'otros_fob',
			 fieldLabel: 'Otros FOB',
			 allowBlank: false,
			 anchor:'100%',
			 gwidth: 100,
			 renderer:function (value,p,record) {
					var dato =  value.replace('.', ",")
									.replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");

					if (value>0) {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="blue"><b>'+dato+'</b></font></p></div>';
					} else {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="red"><b>'+dato+'</b></font></p></div>';
					}



				},
			 decimalPrecision:2,
			 maxLength:1179650
		 },
			 type:'NumberField',
			 filters:{pfiltro:'fact.otros_fob',type:'numeric'},
			 id_grupo:1,
			 grid:true,
			 form:false
	 },

	 {
		 config:{
			 name: 'transporte_cif',
			 fieldLabel: 'Transporte CIF',
			 allowBlank: false,
			 anchor:'100%',
			 gwidth: 100,
			 renderer:function (value,p,record) {
					var dato =  value.replace('.', ",")
									.replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");

					if (value>0) {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="#CA6500"><b>'+dato+'</b></font></p></div>';
					} else {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="red"><b>'+dato+'</b></font></p></div>';
					}



				},
			 decimalPrecision:2,
			 maxLength:1179650
		 },
			 type:'NumberField',
			 filters:{pfiltro:'fact.transporte_cif',type:'numeric'},
			 id_grupo:1,
			 grid:true,
			 form:false
	 },

	 {
		 config:{
			 name: 'seguros_cif',
			 fieldLabel: 'Seguros CIF',
			 allowBlank: false,
			 anchor:'100%',
			 gwidth: 100,
			 renderer:function (value,p,record) {
					var dato =  value.replace('.', ",")
									.replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");

					if (value>0) {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="#CA6500"><b>'+dato+'</b></font></p></div>';
					} else {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="red"><b>'+dato+'</b></font></p></div>';
					}



				},
			 decimalPrecision:2,
			 maxLength:1179650
		 },
			 type:'NumberField',
			 filters:{pfiltro:'fact.seguros_cif',type:'numeric'},
			 id_grupo:1,
			 grid:true,
			 form:false
	 },

	 {
		 config:{
			 name: 'otros_cif',
			 fieldLabel: 'Otros CIF',
			 allowBlank: false,
			 anchor:'100%',
			 gwidth: 100,
			 renderer:function (value,p,record) {
					var dato =  value.replace('.', ",")
									.replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");

					if (value>0) {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="#CA6500"><b>'+dato+'</b></font></p></div>';
					} else {
						return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="red"><b>'+dato+'</b></font></p></div>';
					}



				},
			 decimalPrecision:2,
			 maxLength:1179650
		 },
			 type:'NumberField',
			 filters:{pfiltro:'fact.otros_cif',type:'numeric'},
			 id_grupo:1,
			 grid:true,
			 form:false
	 },
	 /********************************************/

	 {
		config:{
			name: 'direccion_cliente',
			fieldLabel: 'Dirección del Importador',
			allowBlank: false,
			anchor:'80%',
			gwidth: 150,
			maxLength:100,
			style:'text-transform:uppercase'
		},
			type:'TextField',
			filters:{pfiltro:'fact.nombre_factura',type:'string'},
			id_grupo:0,
			grid:true,
			//bottom_filter:true,
			form:true
	},

	{
	 config:{
		 name: 'observaciones',
		 fieldLabel: 'Incoterm',
		 allowBlank: false,
		 anchor:'80%',
		 gwidth: 150,
		 maxLength:100,
		 style:'text-transform:uppercase'
	 },
		 type:'TextField',
		 filters:{pfiltro:'fact.observaciones',type:'string'},
		 id_grupo:0,
		 grid:true,
		 bottom_filter:true,
		 form:true
 },
{
 config:{
	 name: 'tipo_cambio',
	 fieldLabel: 'TC',
	 allowBlank: false,
	 anchor:'80%',
	 gwidth: 150,
	 maxLength:100,
	 style:'text-transform:uppercase'
 },
	 type:'TextField',
	 filters:{pfiltro:'fact.nombre_factura',type:'string'},
	 id_grupo:0,
	 grid:true,
	 bottom_filter:true,
	 form:true
},
		{
			config:{
				name: 'correlativo_venta',
				fieldLabel: 'Nro',
				allowBlank: false,
				anchor:'80%',
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
				id_grupo: 0,
				form: true
		},

	 {
		 config:{
			 name: 'estado',
			 fieldLabel: 'Estado',
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
			 fieldLabel : 'Paquetes / Fórmulas',
			 allowBlank : true,
			 anchor:'80%',
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
			 style:'text-transform:uppercase;'
		 },
		 type : 'ComboBox',
		 id_grupo : 0,
		 form : true
	 },
		{
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
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
				fieldLabel: 'Tipo Factura',
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
				fieldLabel: 'Fecha creación',
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
				fieldLabel: 'Cajero',
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
				filters:{pfiltro:'fact.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	fheight:'70%',
	fwidth:'50%',


	title:'CABECERA FACTURA',
	ActSave:'../../sis_ventas_facturacion/control/FacturacionExportacion/insertarCabeceraExportacion',
	ActDel:'../../sis_ventas_facturacion/control/VentaFacturacion/eliminarVentaFacturacion',
	ActList:'../../sis_ventas_facturacion/control/FacturacionExportacion/listarFacturaExportacion',
	id_store:'id_venta',
	fields: [
		{name:'id_venta', type: 'numeric'},
		{name:'id_cliente', type: 'numeric'},
		{name:'id_punto_venta', type: 'numeric'},
		{name:'id_sucursal', type: 'numeric'},
		{name:'id_usuario_cajero', type: 'numeric'},
		{name:'cod_control', type: 'string'},
		{name:'estado', type: 'string'},
		{name:'nro_factura', type: 'numeric'},
		{name:'observaciones', type: 'string'},
		{name:'id_moneda_venta', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'tipo_cambio', type: 'numeric'},
		{name:'nombre_factura', type: 'string'},
		{name:'nit', type: 'string'},
		{name:'direccion_cliente', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'id_formula', type: 'int4'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'nro_factura', type: 'numeric'},
		{name:'cod_control', type: 'string'},
		{name:'tipo_factura', type: 'string'},
		{name:'desc_moneda', type: 'string'},
		{name:'total_venta', type: 'numeric'},
		{name:'valor_bruto', type: 'numeric'},
		{name:'transporte_fob', type: 'numeric'},
		{name:'seguros_fob', type: 'numeric'},
		{name:'otros_fob', type: 'numeric'},
		{name:'transporte_cif', type: 'numeric'},
		{name:'seguros_cif', type: 'numeric'},
		{name:'otros_cif', type: 'numeric'},
	],
	sortInfo:{
		field: 'id_venta',
		direction: 'DESC'
	},

	bdel:true,
	bsave:false,
	bedit:false,


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
