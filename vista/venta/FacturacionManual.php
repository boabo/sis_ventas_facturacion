<?php
/**
*@package pXP
*@file gen-FacturacionManual.php
*@author  (ivaldivia)
*@date 10-05-2019 19:08:47
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.FacturacionManual=Ext.extend(Phx.gridInterfaz,{
	mosttar:'',
	solicitarPuntoVenta: true,

	formUrl: '../../../sis_ventas_facturacion/vista/venta/FormFacturaManual.php',
	formClass : 'FormFacturaManual',
  nombreVista: 'FacturacionManual',
  //tipo_factura: 'manual',
	solicitarSucursal: true, //para indicar si es forzoso o no indicar la sucrsal al iniciar
//	tipo_usuario : 'cajero',


	constructor:function(config){
		this.maestro=config.maestro;
		//this.tipo_usuario = 'cajero';
		//console.log("lelga aqui tipo",this);

		Ext.Ajax.request({
				url:'../../sis_ventas_facturacion/control/Cajero/getTipoUsuario',
				params: {'vista':'cajero'},
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
		this.cmbPuntoV.on('select', function( combo, record, index){
				this.capturaFiltros();
		},this);



	},

	successGetVariables : function (response,request) {

			var respuesta = JSON.parse(response.responseText);
			if('datos' in respuesta){
					this.variables_globales = respuesta.datos;
			}
			if(this.solicitarPuntoVenta){
					this.seleccionarPuntoVentaSucursal();

			}
			Phx.vista.FacturacionManual.superclass.constructor.call(this,request.arguments);
			this.store.baseParams.tipo_usuario = this.tipo_usuario;
			this.store.baseParams.pes_estado = 'borrador';
		//	this.bbar.add(this.cmbPuntoV);
		this.addButton('completar_pago_2',{
				grupo:[0],
				text :'Completar Pago',
				iconCls : 'bmoney',
				disabled: true,
				handler : this.completar_pago,
				tooltip : '<b>Formulario para completar el pago</b>'
		});

		this.addButton('asociar_boletos',
				{   grupo:[2],
						text: 'Asociar Boletos',
						iconCls: 'bchecklist',
						disabled: true,
						handler: this.AsociarBoletos,
						tooltip: '<b>Asociar Boletos</b><br/>Asocia Boletos a la factura emitida.'
				}
		);

			this.init();

			this.campo_fecha = new Ext.form.DateField({
				name: 'fecha_reg',
				grupo: this.bactGroups,
			fieldLabel: 'Fecha',
			allowBlank: false,
			anchor: '80%',
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
			//console.log("LLEGA FECHA SELEC",this.store);
			this.load();
		},this);



		this.finCons = true;
		this.bbar.el.dom.style.background='#A3C9F7';
		this.tbar.el.dom.style.background='#A3C9F7';
		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#FEFFF4';
		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#FFF4EB';


	},


	gruposBarraTareas:[{name:'borrador',title:'<H1 style="font-size:12px;" align="center"><i style="color:#FFAE00; font-size:15px;" class="fa fa-eraser"></i> Borrador</h1>',grupo:0,height:0},
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
			this.getBoton('completar_pago_2').enable();
			this.getBoton('asociar_boletos').enable();

			Phx.vista.FacturacionManual.superclass.preparaMenu.call(this);
		},

		liberaMenu : function(){
				var rec = this.sm.getSelected();
				this.getBoton('completar_pago_2').disable();
				this.getBoton('asociar_boletos').disable();
				Phx.vista.FacturacionManual.superclass.liberaMenu.call(this);
		},


	 bactGroups:  [0,1,2,3],
	 btestGroups: [0],
	 bexcelGroups: [0,1,2],
	 bnewGroups: [0],
	 bdelGroups:[0,1,2],

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
											fields: ['id_punto_venta', 'id_sucursal','nombre', 'codigo','habilitar_comisiones','formato_comprobante'],
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
							 /*if (r.length == 0 ) {*///comentando para que liste vacio
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
																	} else {
																		this.variables_globales.id_sucursal = combo2.getValue();
																		this.store.baseParams.id_sucursal = this.variables_globales.id_sucursal;
																	}

																	this.store.baseParams.tipo_usuario = this.tipo_usuario;
																	this.store.baseParams.tipo_factura = 'manual';
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
								VentanaInicio.body.dom.childNodes[0].firstChild.firstChild.style.background='#A3C9F7';
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
			//Phx.baseInterfaz.superclass.destroy.call(this,c);
			this.store.baseParams.id_punto_venta = '';
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
		this.timer_id=Ext.TaskMgr.start({
			 run: Ftimer,
			 interval:10000,
			 scope:this
	 });
	 function Ftimer(){
		//recuperamos si tiene apertura de Caja
		if (this.store.baseParams.id_punto_venta != '') {
		Ext.Ajax.request({
				url:'../../sis_ventas_facturacion/control/VentaFacturacion/obtenerApertura',
				params:{
					id_punto_venta:this.variables_globales.id_punto_venta,
					id_sucursal:this.variables_globales.id_sucursal,
					fecha_apertura: this.campo_fecha.getValue().dateFormat('d/m/Y')
				},
				success: function(resp){
						var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
						this.aperturaText = reg.ROOT.datos.v_apertura;
						this.tipo_punto_venta = reg.ROOT.datos.v_tipo_punto_venta;
						this.variables_globales.aperturaEstado = this.aperturaText;

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
							this.apertura.setText('CAJA ABIERTA')
						}else if (this.aperturaText == 'cerrado') {
							this.bbar.items.items[14].el.dom.style.color='blue';
							this.bbar.items.items[14].el.dom.style.letterSpacing='.1em';
							this.bbar.items.items[14].el.dom.style.textShadow='0.5px 0.5px 0px #FFFFFF, 1px 0px 0px rgba(0,0,0,0.15)';
							this.apertura.setText('CAJA CERRADA')
						}

				},
				failure: this.conexionFailure,
				timeout:this.timeout,
				scope:this
		});

			this.reload();
	 }
	}
		/***************/

	},

		onButtonAct:function () {
			this.iniciarEventos();
			this.reload();
		},

		openForm : function (tipo, record) {
    	var me = this;
           me.objSolForm = Phx.CP.loadWindows(this.formUrl,
                                    '<center><img src="../../../lib/imagenes/facturacion/factura.png" style="width:55px; vertical-align: middle;"><span style="vertical-align: middle; color:red; font-size:30px; text-shadow: 3px 0px 0px #000000;"> FACTURACIÓN MANUAL</span></center>',
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


		completar_pago : function () {
				//abrir formulario de solicitud
				this.openForm('edit', this.sm.getSelected());

				},

		sigEstado:function(){
			//Phx.CP.loadingShow();
			var d = this.sm.getSelected().data;
			//console.log("llega aqui el id y el proceso",d);
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
				 //console.log("ventana",panel);
				 //console.log("this",resp);

				 //

      },

			onButtonNew : function () {
	        //abrir formulario de solicitud
					Ext.Ajax.request({
							url:'../../sis_ventas_facturacion/control/VentaFacturacion/obtenerApertura',
							params:{
								id_punto_venta:this.variables_globales.id_punto_venta,
								id_sucursal:this.variables_globales.id_sucursal,
								fecha_apertura: this.campo_fecha.getValue().dateFormat('d/m/Y')
							},
							success: function(resp){
									var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
									// this.aperturaText = reg.ROOT.datos.v_apertura;
									if (reg.ROOT.datos.v_apertura == 'SIN APERTURA DE CAJA') {
										Ext.Msg.show({
												title: 'Alerta',
												msg: '<p>Estimado Usuario debe aperturar una caja para proceder con las ventas.</p>',
												buttons: Ext.Msg.OK,
												width: 512,
												icon: Ext.Msg.INFO
										});
									} else if (reg.ROOT.datos.v_apertura == 'cerrado') {
										Ext.Msg.show({
												title: 'Alerta',
												msg: '<p>Estimado Usuario la apertura de caja esta actualmente cerrada.</p>',
												buttons: Ext.Msg.OK,
												width: 512,
												icon: Ext.Msg.INFO
										});
									} else if (reg.ROOT.datos.v_apertura == 'abierto') {
										  this.openForm('new');
									}

							},
							failure: this.conexionFailure,
							timeout:this.timeout,
							scope:this
					});
	        // this.openForm('new');
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

			AsociarBoletos: function(){

		              var rec = {maestro: this.sm.getSelected().data}
		              console.log('VALOR',	rec);
		              Phx.CP.loadWindows('../../../sis_ventas_facturacion/vista/venta/AsociarBoletos.php',
		                  '<center><h1 style="font-size:25px; color:#0E00B7; text-shadow: -1px -1px 1px rgba(255,255,255,.1), 1px 1px 1px rgba(0,0,0,.5);"> <img src="../../../lib/imagenes/icono_dibu/dibu_zoom.png" style="float:center; vertical-align: middle;"> Asociar Boletos</h1></center>',
		                  {
		                      width:1200,
		                      height:600
		                  },
		                  rec,
		                  this.idContenedor,
		                  'AsociarBoletos');

		          },

			imprimirNota: function(){
   			var rec = this.sm.getSelected();
        //console.log("llega para imprimir",this);
   				Phx.CP.loadingShow();
   				Ext.Ajax.request({
   						url : '../../sis_ventas_facturacion/control/Cajero/reporteFactura',
   						params : {
                'id_venta' : rec.data.id_venta ,
   							'id_punto_venta' : rec.data.id_punto_venta,
   							'formato_comprobante' : this.variables_globales.formato_comprobante,
   							'tipo_factura': this.store.baseParams.tipo_factura
   						},
   						success : this.successExportHtml,
   						failure : this.conexionFailure,
   						timeout : this.timeout,
   						scope : this
   					});

   	},

    successExportHtml: function (resp) {
          Phx.CP.loadingHide();
          var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
          var objetoDatos = (objRes.ROOT == undefined)?objRes.datos:objRes.ROOT.datos;
          var wnd = window.open("about:blank", "", "_blank");
      wnd.document.write(objetoDatos.html);
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
 		 config:{
 			 name: 'cajero',
 			 fieldLabel: 'Cajero',
 			 allowBlank: true,
 			 anchor: '80%',
 			 gwidth: 300,
 			 maxLength:15
 		 },
 			 type:'TextField',
 			 filters:{pfiltro:'usuca.desc_persona',type:'string'},
 			 id_grupo:1,
 			 grid:true,
 			 form:false
 	 },
		{
			config:{
				name: 'fecha',
				fieldLabel: 'Fecha Factura.',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
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
 			 fieldLabel: 'Nro Factura',
 			 allowBlank: true,
 			 anchor: '80%',
 			 gwidth: 100,
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
				name: 'nit',
				fieldLabel: 'NIT',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:25
			},
				type:'TextField',
				filters:{pfiltro:'fact.nit',type:'string'},
				id_grupo:1,
				grid:true,
				bottom_filter:true,
				form:true
		},
		{
			config:{
				name: 'nombre_factura',
				fieldLabel: 'Razón Social',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:100
			},
				type:'TextField',
				filters:{pfiltro:'fact.nombre_factura',type:'string'},
				id_grupo:1,
				grid:true,
				bottom_filter:true,
				form:false
		},
		{
			config:{
				name: 'total_venta',
				fieldLabel: 'Total Venta',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				renderer:function (value,p,record) {
 					var dato =  value.replace('.', ",")
 									.replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");
 					return '<div style="text-align:right; font-weight:bold; font-size:12px; ext:qtip="Optimo"><p> <font color="red"><b>'+dato+'</b></font></p></div>';

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
 		 config : {
 			 name : 'id_cliente',
 			 fieldLabel : 'Razón Social Cliente',
 			 style:'text-transform:uppercase;',
 			 anchor: '80%',
 			 allowBlank : false,
 			 emptyText : 'Cliente...',
 			 store : new Ext.data.JsonStore({
 				 url : '../../sis_ventas_facturacion/control/Cliente/listarCliente',
 				 id : 'id_cliente',
 				 root : 'datos',
 				 sortInfo : {
 					 field : 'nombres',
 					 direction : 'ASC'
 				 },
 				 totalProperty : 'total',
 				 fields : ['id_cliente', 'nombres', 'primer_apellido', 'segundo_apellido','nombre_factura','nit'],
 				 remoteSort : true,
 				 baseParams : {
 					 par_filtro : 'cli.nombres#cli.primer_apellido#cli.segundo_apellido#nombre_factura#nit'
 				 }
 			 }),
 			 valueField : 'id_cliente',
 			 displayField : 'nombre_factura',
 			 gdisplayField : 'nombre_factura',
 			 hiddenName : 'id_cliente',
 			 forceSelection : false,
 			 typeAhead : false,
 			 tpl:'<tpl for="."><div class="x-combo-list-item"><b><p>Cliente:<font color="#000CFF" weight="bold"> {nombre_factura}</font></b></p></div></tpl>',
 			 triggerAction : 'all',
 			 lazyRender : true,
 			 mode : 'remote',
 			 pageSize : 10,
 			 listWidth:'450',
 			 maxHeight : 450,
 			 queryDelay : 1000,
 			 turl:'../../../sis_ventas_facturacion/vista/cliente/Cliente.php',
 			 ttitle:'Clientes',
 			 tasignacion : true,
 			 tname : 'id_cliente',
 			 tdata:{},
 			 cls:'uppercase',
 			 tcls:'Cliente',
 			 gwidth : 170,
 			 minChars : 2,
 			 //style:';'
 		 },
 		 type : 'TrigguerCombo',
 		 id_grupo : 0,
 		 form : true
 	 },
	 {
		 config:{
			 name: 'observaciones',
			 fieldLabel: 'Observaciones',
			 allowBlank: true,
			 anchor: '80%',
			 gwidth: 200,
			 style:'text-transform:uppercase;'
			 //maxLength:-5
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
				anchor: '80%',
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
			 fieldLabel: 'Estado',
			 allowBlank: false,
			 anchor: '80%',
			 gwidth: 100,
			 maxLength:100
		 },
			 type:'TextField',
			 filters:{pfiltro:'ven.estado',type:'string'},
			 id_grupo:1,
			 grid:true,
			 form:false
	 },
	 {
		 config:{
			 name: 'nro_autorizacion',
			 fieldLabel: 'Nro Autorización',
			 allowBlank: true,
			 anchor: '80%',
			 gwidth: 200,
			 maxLength:15
		 },
			 type:'TextField',
			 filters:{pfiltro:'dosifi.nroaut',type:'string'},
			 id_grupo:1,
			 grid:true,
			 form:false
	 },
	 {
		 config:{
			 name: 'informe',
			 fieldLabel: 'Motivo de Uso',
			 allowBlank: true,
			 anchor: '80%',
			 gwidth: 300,
			 //maxLength:15
		 },
			 type:'TextField',
			 filters:{pfiltro:'ven.cod_control',type:'string'},
			 id_grupo:1,
			 grid:true,
			 form:false,
			 egrid:true
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
			 anchor: '80%',
			 listWidth:'450',
			 maxHeight : 450,
			 resizable: true,
			 emptyText : 'Paquetes...',
			 store : new Ext.data.JsonStore({
				 url: '../../sis_ventas_facturacion/control/Formula/listarFormula',
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
					 par_filtro : 'form.nombre'
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
		 type : 'TrigguerCombo',
		 id_grupo : 0,
		 form : true
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
				anchor: '80%',
				gwidth: 100,
				maxLength:20
			},
				type:'TextField',
				filters:{pfiltro:'fact.tipo_factura',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			//configuracion del componente
			config:{
				labelSeparator:'',
				inputType:'hidden',
				name: 'forma_pedido'
			},
			valorInicial:'vendedor',
			type:'Field',
			form:true
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
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
				anchor: '80%',
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
				anchor: '80%',
				gwidth: 100,
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
				filters:{pfiltro:'fact.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	fheight:'40%',
	fwidth:'30%',
	title:'<center style="font-size:25px; color:#0E00B7; text-shadow: -1px -1px 1px rgba(255,255,255,.1), 1px 1px 1px rgba(0,0,0,.5);"><i style="color:green;" class="fa fa-qrcode" aria-hidden="true"></i> Cabecera Factura</center>',
	ActSave:'../../sis_ventas_facturacion/control/Cajero/insertarVenta',
	ActDel:'../../sis_ventas_facturacion/control/Cajero/eliminarVenta',
	ActList:'../../sis_ventas_facturacion/control/Cajero/listarVenta',
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
		{name:'informe', type: 'string'},
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
		{name:'cajero', type: 'string'},
		{name:'nro_autorizacion', type: 'string'},
	],
	sortInfo:{
		field: 'id_venta',
		direction: 'DESC'
	},

	bdel:true,
	bsave:false,
	bnew:true,
	bexcel:false,
	btest:false,
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
