<?php
/**
*@package pXP
*@file CorregirFormaPagoRos.php
*@author  (bvasquez)
*@date 22-03-2021
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
</style>
<script>

Phx.vista.CorregirFormaPagoRos=Ext.extend(Phx.gridInterfaz,{

	formUrl: '../../../sis_ventas_facturacion/vista/venta/FormCorregirRos.php',
	formClass : 'FormCorregirRos',
  nombreVista: 'ReciboManual',
	solicitarSucursal: true, //para indicar si es forzoso o no indicar la sucrsal al iniciar

    constructor:function(config) {
		this.maestro=config.maestro;

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

	},
	tabsouth :[
		// {
		// 		url:'../../../sis_ventas_facturacion/vista/venta/DetalleVentaRecibo.php',
		// 		title:'Detalle Venta',
		// 		height:'50%',
		// 		cls:'DetalleVentaRecibo'
		// 	},
    {
      		url:'../../../sis_ventas_facturacion/vista/venta/DetalleVentaRecibo.php',
  				title:'Detalle Venta',
  				height:'50%',
          // collapsed: true,
  				cls:'DetalleVentaRecibo'
    },
	],
	successGetVariables : function (response,request) {
    // this.initButtons=[this.cmbPv];
		var respuesta = JSON.parse(response.responseText);
   		if('datos' in respuesta){
		    this.variables_globales = respuesta.datos;
		}

		if(this.solicitarSucursal){
			this.seleccionarPuntoVentaSucursal();
		}
		//llama al constructor de la clase padre
		Phx.vista.CorregirFormaPagoRos.superclass.constructor.call(this,request.arguments);
		this.store.baseParams.tipo_usuario = this.tipo_usuario;
		this.store.baseParams.pes_estado = 'finalizado';
		this.init();
		this.addButton('corregir_formas_pago',{
				grupo:[1],
				text :'<center>Corregir Formas de Pago</center>',
				iconCls : 'bedit',
				disabled: true,
				handler : this.onButtonEdit,
				tooltip : '<b>Formulario para completar el pago</b>'
		});

		this.addButton('btnImprimir',
				{   grupo:[1,2],
						text: 'Imprimir',
						iconCls: 'bprint',
						disabled: true,
						handler: this.imprimirNota,
						tooltip: '<b>Imprimir Factura</b><br/>Imprime la Factura de la venta'
				}
		);

		this.addButton('anular_fact',
				{   grupo:[1],
						text: 'Anular',
						iconCls: 'button-anular-red',
						disabled: true,
						handler: this.anular,
						tooltip: '<b>Anular Factura</b><br/>Anula la Factura de la venta'
				}
		);


		this.campo_fecha = new Ext.form.DateField({
			name: 'fecha_reg',
			grupo: this.bactGroups,
			fieldLabel: 'Fecha',
			allowBlank: false,
			anchor: '80%',
			gwidth: 100,
			format: 'd/m/Y',
			hidden : false,
      style: {marginLeft:'25px'}
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
					marginLeft:'30px'
				}
		});

    this.cmbPv = new Ext.form.ComboBox({
                name: 'id_punto_venta',
                grupo: this.bactGroups,
                typeAhead: false,
                forceSelection: true,
                allowBlank: true,
                width:160,
                disableSearchButton: true,
                emptyText: 'Seleccione punto de venta ...',
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
                    baseParams: {tipo_usuario: this.tipo_usuario,par_filtro: 'puve.nombre#puve.codigo', tipo_factura: this.tipo_factura, tipo : this.tipo}

                }),
                valueField: 'id_punto_venta',
                displayField: 'nombre',
                hiddenName: 'id_punto_venta',
                enableMultiSelect: true,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 20,
                queryDelay: 200,
                // anchor: '180%',
                listWidth: '280',
                resizable: true,
                minChars: 2,
                style: {marginLeft:'5px'}
     });
		/*Fondo en el tbar*/
		this.finCons = true;
		this.bbar.el.dom.style.background='#6EC8E3';
		this.tbar.el.dom.style.background='#6EC8E3';
		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#FEFFF4';
		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#FFF4EB';

    this.tbar.addField(this.cmbPv);

		this.tbar.addField(this.campo_fecha);
		this.tbar.addField(this.punto_venta);

		var datos_respuesta = JSON.parse(response.responseText);
		var fecha_array = datos_respuesta.datos.fecha.split('/');
		this.campo_fecha.setValue(new Date(fecha_array[2],parseInt(fecha_array[1]) - 1,fecha_array[0]));


		this.campo_fecha.on('select',function(value){
  		this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');
  		this.load();
	   },this);

     this.cmbPv.on('select', function (obj, newValue, oldValue) {
       this.store.baseParams.id_punto_venta = newValue.data.id_punto_venta;
       this.store.baseParams.tipo_usuario = this.tipo_usuario;
       this.store.baseParams.tipo_factura = 'todos';
       this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');
       this.punto_venta.setText(newValue.data.nombre)
       this.load();
       this.iniciarEventos();
     }, this);

	},

	gruposBarraTareas:[{name:'finalizado',title:'<H1 style="font-size:12px;" align="center"><i style="color:#B61BFF; font-size:15px;" class="fa fa-check-circle"></i> Emitidos</h1>',grupo:1,height:0},
											{name:'anulado',title:'<H1 style="font-size:12px;" align="center"><i style="color:red; font-size:15px;" class="fa fa-ban"></i> Anulados</h1>',grupo:2,height:0}
										 ],

	 actualizarSegunTab: function(name, indice){
		 if(this.finCons){
				 this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');
				 this.store.baseParams.pes_estado = name;
				 this.store.baseParams.interfaz = 'vendedor';
				 this.load({params:{start:0, limit:this.tam_pag}});
			 }
	 	},

	 bactGroups:  [0,1,2],
 	 btestGroups: [0],
	 bexcelGroups: [0,1,2],
 	 bdelGroups: [0,1],

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
 																	this.store.baseParams.tipo_factura = 'todos';
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
	variables_globales : {
		vef_tiene_punto_venta : 'false',
		vef_tipo_venta_habilitado : 'producto_terminado,formula,servicio',
		habilitar_comisiones : 'no'
	},
	diagramGantt : function (){
            var data=this.sm.getSelected().data.id_proceso_wf;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_workflow/control/ProcesoWf/diagramaGanttTramite',
                params:{'id_proceso_wf':data},
                success:this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
	},
	anular : function () {
		var data=this.sm.getSelected();
    var me= this;
		if (data.data.tipo_factura=='recibo_manual'){
			Ext.Msg.show({
			 title:'<center> Aviso</h1></center>',
			 msg: '<center><b style="font-size:15px;">No se puede imprimir la factura de un recibo manual</b></center>',
			 width:290,
			 height:100,
			 buttons: Ext.Msg.OK,
			 fn: function () {
					Phx.CP.getPagina(this.idContenedorPadre).reload();
					this.panel.close();
			 },
			 scope:this
			});
		}else{

      Ext.Msg.confirm(
          'Mensaje de Confirmación',
          'Esta Seguro de Anular la Factura',
          function(btn) {
              if (btn == 'yes'){
                Phx.CP.loadingShow();
          	        Ext.Ajax.request({
          	            url:'../../sis_ventas_facturacion/control/Venta/anularVenta',
          	            params:{
          	                id_venta:  data.data.id_venta
          	                },
          	            success:me.successSave,
          	            failure: me.conexionFailure,
          	            timeout:me.timeout,
          	            scope:me
          	        });
          }
        }
      );

			}
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
                    name: 'id_proceso_wf'
            },
            type:'Field',
            form:true
        },

        {
            //configuracion del componente
            config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_estado_wf'
            },
            type:'Field',
            form:true
        },
				{
        config:{
            name: 'tipo_factura',
            fieldLabel: 'Tipo Recibo',
            gwidth: 90
        },
            type:'TextField',
            grid:true,
            form:true,
            bottom_filter: true
    		},
				{
					config:{
						labelSeparator:'',
						inputType:'hidden',
						name: 'ESTACION_inicio'
					},
						type:'Field',
						form:true
				},
				{
		            config:{
		                name: 'nro_factura',
		                fieldLabel: 'Nro Recibo',
		                gwidth: 90
		            },
		                type:'TextField',
		                filters:{pfiltro:'ven.nro_factura',type:'string'},
		                grid:true,
		                form:false,
		                bottom_filter: true
		        },
        {
            config:{
                name: 'correlativo_venta',
                fieldLabel: 'Nro',
                gwidth: 110,
                renderer: function(value,c,r){

                	if (r.data.estado == 'anulado') {
                		return String.format('{0}', '<p><font color="red">' + value + '</font></p>');
                	} else {
                		return value;
                	}

                }
            },
                type:'TextField',
                filters:{pfiltro:'ven.correlativo_venta',type:'string'},
                grid:false,
                form:false,
                bottom_filter: true
        },
        {
            config:{
                name: 'nombre_factura',
                fieldLabel: 'Cliente',
                gwidth: 110
            },
                type:'TextField',
                filters : {pfiltro : 'cli.nombre_factura',type : 'string'},
                grid:true,
                form:false,
                bottom_filter: true
        },
        // {
        //     config:{
        //         name: 'cliente_destino',
        //         fieldLabel: 'Destino',
        //         gwidth: 110
        //     },
        //         type:'TextField',
        //         filters : {pfiltro : 'clides.nombre_factura',type : 'string'},
        //         grid:false,
        //         form:false
        // },
        {
            config:{
                name: 'total_venta',
                fieldLabel: 'Total Venta',
                allowBlank: false,
                anchor: '80%',
                gwidth: 120,
                maxLength:5,
                disabled:true
            },
                type:'NumberField',
                filters:{pfiltro:'ven.total_venta',type:'numeric'},
                id_grupo:1,
                grid:true,
                form:false
        },
        {
            config:{
                name: 'fecha',
                fieldLabel: 'Fecha Doc.',
                gwidth: 110,
                format: 'd/m/Y',
				renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
                type:'DateField',
                filters: { pfiltro:'ven.fecha', type:'date'},
                grid:true,
                form:false
        },
		{
            config:{
                name: 'nombre_sucursal',
                fieldLabel: 'Sucursal',
                gwidth: 110
            },
                type:'TextField',
                filters: { pfiltro: 'suc.nombre', type: 'string'},
                grid: false,
                form: false,
                bottom_filter: true
        },

         {
            config:{
                name: 'forma_pago',
                fieldLabel: 'Forma de Pago',
                gwidth: 110
            },
                type:'TextField',
                grid:false,
                form:false
        },



        {
			config:{
				name: 'observaciones',
				fieldLabel: 'Observaciones',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100
			},
				type:'TextArea',
				filters:{pfiltro:'ven.observaciones',type:'string'},
				id_grupo:0,
				grid:true,
				form:false
		},
        {
            config:{
                name: 'monto_forma_pago',
                fieldLabel: 'Importe Recibido',
                allowBlank: false,
                gwidth: 120,
                maxLength:5,
                disabled:true
            },
                type:'NumberField',
                id_grupo:1,
                grid:false,
                form:false
        },
        {
            config:{
                name: 'comision',
                fieldLabel: 'Comisión',
                gwidth: 120,
                maxLength:5,
                disabled:true
            },
                type:'NumberField',
                grid:true

        },
				{
            config:{
                name: 'excento',
                fieldLabel: 'Imp Excento',
                gwidth: 110
            },
                type:'TextField',
                filters:{pfiltro:'ven.excento',type:'numeric'},
                grid:true,
                form:false
        },
        {
            config:{
                name: 'estado',
                fieldLabel: 'Estado',
                gwidth: 100
            },
                type:'TextField',
                filters:{pfiltro:'ven.estado',type:'string'},
                grid:true,
                form:false
        },
				{
            config:{
                name: 'cajero',
                fieldLabel: 'Cajero',
                gwidth: 300
            },
                type:'TextField',
                filters : {pfiltro : 'usua.desc_persona',type : 'string'},
                grid:true,
                form:false,
                bottom_filter: true
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
                filters:{pfiltro:'ven.estado_reg',type:'string'},
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
				filters:{pfiltro:'ven.usuario_ai',type:'string'},
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
				filters:{pfiltro:'ven.fecha_reg',type:'date'},
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
				name: 'id_usuario_ai',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'ven.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'ven.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
    {
        config:{
            name: 'cod_control',
            fieldLabel: 'Codigo Control',
            gwidth: 110
        },
            type:'TextField',
            filters:{pfiltro:'ven.cod_control',type:'string'},
            grid:false,
            form:false
    },
	],
	tam_pag:50,
	title:'Ventas',
	// ActSave:'../../sis_ventas_facturacion/control/Venta/insertarVenta',
	ActDel:'../../sis_ventas_facturacion/control/Venta/eliminarVenta',
	ActList:'../../sis_ventas_facturacion/control/Venta/listarRecibo',
	id_store:'id_venta',
	fields: [
		{name:'id_venta', type: 'numeric'},
		{name:'id_cliente', type: 'numeric'},
		{name:'id_sucursal', type: 'numeric'},
		{name:'id_punto_venta', type: 'numeric'},
		{name:'id_proceso_wf', type: 'numeric'},
		{name:'id_forma_pago', type: 'numeric'},
		{name:'porcentaje_descuento', type: 'numeric'},
		{name:'id_vendedor_medico', type: 'string'},
		{name:'forma_pago', type: 'string'},
		{name:'numero_tarjeta', type: 'string'},
		{name:'observaciones', type: 'string'},
		{name:'codigo_tarjeta', type: 'string'},
		{name:'tipo_tarjeta', type: 'string'},
		{name:'id_estado_wf', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'nombre_factura', type: 'string'},
		{name:'nombre_sucursal', type: 'string'},
		{name:'nombre_punto_venta', type: 'string'},
		{name:'forma_pedido', type: 'string'},
		{name:'estado', type: 'string'},
		{name:'correlativo_venta', type: 'string'},
        {name:'contabilizable', type: 'string'},
		{name:'a_cuenta', type: 'numeric'},
		{name:'total_venta', type: 'numeric'},
		{name:'comision', type: 'numeric'},
		{name:'fecha_estimada_entrega', type: 'date',dateFormat:'Y-m-d'},
        {name:'hora_estimada_entrega', type: 'string'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'nit', type: 'string'},
		{name:'monto_forma_pago', type: 'numeric'},
		{name:'nro_factura', type: 'string'},
		{name:'cod_control', type: 'string'},
        {name:'vendedor_medico', type: 'string'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'excento', type: 'numeric'},
		{name:'nroaut', type: 'numeric'},
		'id_moneda','total_venta_msuc','transporte_fob','seguros_fob',
		'otros_fob','transporte_cif','seguros_cif','otros_cif',
		'tipo_cambio_venta','desc_moneda','valor_bruto',
		'descripcion_bulto','cliente_destino','id_cliente_destino',
		'cajero','tipo_factura','ESTACION_inicio','id_auxiliar_anticipo'


	],
	sortInfo:{
		field: 'id_venta',
		direction: 'DESC'
	},
	bdel:true,
	bsave:false,

  // cmbPv: new Ext.form.ComboBox({
  //             name: 'id_punto_venta',
  //             grupo: [0, 1],
  //             typeAhead: false,
  //             forceSelection: true,
  //             allowBlank: true,
  //             width:250,
  //             disableSearchButton: true,
  //             emptyText: 'Seleccione punto de venta ...',
  //             store: new Ext.data.JsonStore({
  //                 url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
  //                 id: 'id_punto_venta',
  //                 root: 'datos',
  //                 sortInfo: {
  //                     field: 'nombre',
  //                     direction: 'ASC'
  //                 },
  //                 totalProperty: 'total',
  //                 fields: ['id_punto_venta', 'id_sucursal','nombre', 'codigo','habilitar_comisiones','formato_comprobante'],
  //                 remoteSort: true,
  //                 baseParams: {tipo_usuario: window.branch ,par_filtro: 'puve.nombre#puve.codigo', tipo_factura: '', tipo : ''}
  //
  //             }),
  //             valueField: 'id_punto_venta',
  //             displayField: 'nombre',
  //             hiddenName: 'id_punto_venta',
  //             enableMultiSelect: true,
  //             triggerAction: 'all',
  //             lazyRender: true,
  //             mode: 'remote',
  //             pageSize: 20,
  //             queryDelay: 200,
  //             anchor: '180%',
  //             listWidth: '280',
  //             resizable: true,
  //             minChars: 2
  //  }),

    loadValoresIniciales:function()
    {
        this.Cmp.total_venta.setValue(0);
        Phx.vista.CorregirFormaPagoRos.superclass.loadValoresIniciales.call(this);
    },
    openForm : function (tipo, record) {
    	var me = this;
           me.objSolForm = Phx.CP.loadWindows(this.formUrl,
                                    '<center><img src="../../../lib/imagenes/facturacion/editar.svg" style="width:35px; vertical-align: middle;"> <span style="vertical-align: middle; font-size:30px; text-shadow: 3px 0px 0px #000000; color:#D26300;"> CORRECCION DE ROs</span></center>',
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


        //this.openForm('new');
    },

    onButtonEdit : function () {
        //abrir formulario de solicitud
        this.openForm('edit', this.sm.getSelected());
    },

    arrayDefaultColumHidden:['estado_reg','usuario_ai',
    'fecha_reg','fecha_mod','usr_reg','usr_mod'],

    successWizard:function(resp){
        var rec=this.sm.getSelected();
        var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
        if (objRes.ROOT.datos.estado == 'finalizado' && this.tipo_factura != 'manual') {
            this.imprimirNota();
        }
        Phx.CP.loadingHide();
        resp.argument.wizard.panel.destroy();
        this.reload();
     },

   imprimirNota: function(){

			var rec = this.sm.getSelected(),
				data = rec.data,
				me = this;

			if (data) {
				Phx.CP.loadingShow();
				Ext.Ajax.request({
						url : '../../sis_ventas_facturacion/control/Venta/reporteRecibo',
						params : {
							'id_venta' : data.id_venta,
							'formato_comprobante' : me.variables_globales.formato_comprobante,
							'tipo_factura': me.tipo_factura,
							'id_punto_venta':data.id_punto_venta
						},
						success : me.successExportHtml,
						failure : me.conexionFailure,
						timeout : me.timeout,
						scope : me
					});
			}
	},
	successExportHtml: function (resp) {

        Phx.CP.loadingHide();
        var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
				var objetoDatos = (objRes.ROOT == undefined)?objRes.datos:objRes.ROOT.datos;
				console.log("prueba de que manda",objetoDatos);
        var wnd = window.open("about:blank", "", "_blank");
		wnd.document.write(objetoDatos.html);


    },

   successEstadoSinc:function(resp){
        Phx.CP.loadingHide();
        resp.argument.wizard.panel.destroy()
        this.reload();
     },
   iniciarEventos:function(){
     this.Cmp.ESTACION_inicio.setValue(this.variables_globales.ESTACION_inicio);
   },

   preparaMenu:function(n){
 		  var data = this.getSelectedData();

    this.getBoton('corregir_formas_pago').enable();
 		  Phx.vista.CorregirFormaPagoRos.superclass.preparaMenu.call(this,n);
      if(data.tipo_factura=='recibo_manual'){
          this.getBoton('anular_fact').disable();
          this.getBoton('btnImprimir').disable();
          this.getBoton('del').enable();
      }else{
          this.getBoton('anular_fact').enable();
          this.getBoton('btnImprimir').enable();
          this.getBoton('del').disable();
      }
 	 },

})
</script>
