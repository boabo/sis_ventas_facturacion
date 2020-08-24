<?php
/**
*@package pXP
*@file    FormRecibo.php
*@author  Ismael Valdivia Aranibar
*@date    11/04/2019
*@description permites subir archivos a la tabla de documento_sol
*/
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.FormRecibo=Ext.extend(Phx.frmInterfaz,{
    ActSave:'../../sis_ventas_facturacion/control/Venta/insertarVentaCompleta',
    tam_pag: 10,
    layout: 'fit',
    tabEnter: true,
    autoScroll: false,
    breset: true,
    bsubmit:false,
    storeFormaPago : false,
    fwidth : '9%',
    //labelSubmit: '<i class="fa fa-check"></i> Guardar',
    cantidadAllowDecimals: false,
    constructor:function(config)
    {
		Ext.apply(this,config);
        if (this.data.objPadre.variables_globales.vef_tiene_punto_venta === 'true') {

			this.Atributos.push({
	            config: {
	                name: 'id_punto_venta',
	                fieldLabel: 'Punto de Venta',
	                allowBlank: false,
                  width:200,
	                emptyText: 'Elija un Pun...',
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
	                    baseParams: {filtro_usuario: 'si',par_filtro: 'puve.nombre#puve.codigo'}
	                }),
	                valueField: 'id_punto_venta',
	                displayField: 'nombre',
	                gdisplayField: 'nombre_punto_venta',
	                hiddenName: 'id_punto_venta',
	                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
	                forceSelection: true,
	                typeAhead: false,
	                triggerAction: 'all',
	                lazyRender: true,
	                mode: 'remote',
	                pageSize: 15,
	                queryDelay: 1000,
	                gwidth: 150,
	                minChars: 2,
	                disabled:false,
	                renderer : function(value, p, record) {
	                    return String.format('{0}', record.data['nombre_punto_venta']);
	                }
	            },
	            type: 'ComboBox',
	            id_grupo: 1,
	            filters: {pfiltro: 'puve.nombre',type: 'string'},
	            grid: true,
	            form: true
	        },
          {
        config:{
          name: 'observaciones',
          fieldLabel: 'Observaciones',
          allowBlank: true,
          width:200,
          //maxLength:30,
          style:'text-transform:uppercase;'
        },
          type:'TextArea',
          id_grupo:22,
          form:true
      });
		}
		if (this.data.objPadre.variables_globales.habilitar_comisiones == 'si') {
			this.Atributos.push({
		            config:{
		                name: 'comision',
		                fieldLabel: 'Comisión',
		                allowBlank: true,
		                anchor: '80%',
		                maxLength:20,
		                allowNegative:false
		            },
		                type:'NumberField',
		                id_grupo:0,
		                form:true,
		                valorInicial:0
		      });
		}

		if (this.data.objPadre.tipo_factura == 'manual' || this.data.objPadre.tipo_factura == 'computarizadaexpo'|| this.data.objPadre.tipo_factura == 'computarizadamin'|| this.data.objPadre.tipo_factura == 'computarizadaexpomin'|| this.data.objPadre.tipo_factura == 'pedido') {
			this.Atributos.push({
				config:{
					name: 'fecha',
					fieldLabel: 'Fecha Factura',
					allowBlank: false,
					anchor: '80%',
					format: 'd/m/Y'

				},
					type:'DateField',
					id_grupo:0,
					form:true
			});
	  }
	 if (this.data.objPadre.tipo_factura == 'manual') {
			this.Atributos.push({
	            config: {
	                name: 'id_dosificacion',
	                fieldLabel: 'Dosificacion',
	                allowBlank: false,
	                emptyText: 'Elija un Dosi...',
	                store: new Ext.data.JsonStore({
	                    url: '../../sis_ventas_facturacion/control/Dosificacion/listarDosificacion',
	                    id: 'id_dosificacion',
	                    root: 'datos',
	                    sortInfo: {
	                        field: 'nroaut',
	                        direction: 'ASC'
	                    },
	                    totalProperty: 'total',
	                    fields: ['id_dosificacion', 'nroaut', 'desc_actividad_economica','inicial','final'],
	                    remoteSort: true,
	                    baseParams: {filtro_usuario: 'si',par_filtro: 'dos.nroaut'}
	                }),
	                valueField: 'id_dosificacion',
	                displayField: 'nroaut',
	                hiddenName: 'id_dosificacion',
	                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Autorizacion:</b> {nroaut}</p><p><b>Actividad:</b> {desc_actividad_economica}</p></p><p><b>No Inicio:</b> {inicio}</p><p><b>No Final:</b> {final}</p></div></tpl>',
	                forceSelection: true,
	                typeAhead: false,
	                triggerAction: 'all',
	                lazyRender: true,
	                mode: 'remote',
	                pageSize: 15,
	                queryDelay: 1000,
	                gwidth: 150,
	                minChars: 2,
	                disabled : true
	            },
	            type: 'ComboBox',
	            id_grupo: 0,
	            grid: false,
	            form: true
	        });
	        this.Atributos.push({
		            config:{
		                name: 'nro_factura',
		                fieldLabel: 'No Factura ',
		                allowBlank: false,
		                anchor: '80%',
		                maxLength:20
		            },
		                type:'NumberField',
		                id_grupo:0,
		                form:true
		      });

		}
		if (!this.tipoDetalleArray) {
		  this.tipoDetalleArray = this.data.objPadre.variables_globales.vef_tipo_venta_habilitado.split(",");
        }
        this.addEvents('beforesave');
        this.addEvents('successsave');

        this.buildComponentesDetalle();
        this.buildDetailGrid();
        this.buildGrupos();

        this.labelReset = '<div><img src="../../../lib/imagenes/facturacion/imprimir.png" style="width:45px; vertical-align: middle;"> <span style="vertical-align: middle; font-size:25px; font-weight:bold; color:#1479B8; text-shadow: 3px 0px 0px #000000;">GENERAR</span></div>';
        Phx.vista.FormRecibo.superclass.constructor.call(this,config);
        /*Obtenemos el tipo de cambio*/
        this.tipo_cambio = 0;
        this.instanciasPagoAnticipo = 'no';
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
        this.init();
        this.iniciarEventos();

        if(this.data.tipo_form == 'new'){
        	this.onNew();
        }
        else{
        	this.onEdit();
        }

        if(this.data.readOnly===true){
        	for(var index in this.Cmp) {
					if( this.Cmp[index].setReadOnly){
					    	 this.Cmp[index].setReadOnly(true);
					   }
			}

			if (this.data.objPadre.mycls == 'VentaCaja'){
				this.readOnlyGroup(2,false);
				this.blockGroup(0);
			}

			this.megrid.getTopToolbar().disable();

        }
        if (this.data.objPadre.variables_globales.vef_tiene_punto_venta === 'true') {
        	this.Cmp.id_sucursal.allowBlank = true;
        	this.Cmp.id_sucursal.setDisabled(true);
        }

    },

    buildComponentesDetalle: function(){
        var  me = this;
        this.detCmp = {
                    'tipo': new Ext.form.ComboBox({
                            name: 'tipo',
                            fieldLabel: 'Tipo detalle',
                            allowBlank:false,
                            emptyText:'Tipo...',
                            typeAhead: true,
                            triggerAction: 'all',
                            lazyRender:true,
                            mode: 'local',
                            gwidth: 150,
                            store:this.tipoDetalleArray
                    }),

                    'id_producto': new Ext.form.ComboBox({
                                            name: 'id_producto',
                                            fieldLabel: 'Producto/Servicio',
                                            allowBlank: false,
                                            emptyText: 'Productos...',
                                            store: new Ext.data.JsonStore({
                                                url: '../../sis_ventas_facturacion/control/SucursalProducto/listarProductoServicioItem',
                                                id: 'id_producto',
                                                root: 'datos',
                                                sortInfo: {
                                                    field: 'nombre',
                                                    direction: 'ASC'
                                                },
                                                totalProperty: 'total',
                                                fields: ['id_producto', 'tipo','nombre_producto','descripcion','medico','requiere_descripcion','precio','ruta_foto','codigo_unidad_medida'/*,'excento'*/],
                                                remoteSort: true,
                                                baseParams: {par_filtro: 'todo.nombre'}
                                            }),
                                            valueField: 'id_producto',
                                            displayField: 'nombre_producto',
                                            gdisplayField: 'nombre_producto',
                                            hiddenName: 'id_producto',
                                            forceSelection: true,
                                            tpl : new Ext.XTemplate('<tpl for="."><div class="x-combo-list-item">','<tpl if="tipo == \'formula\'">',
                                            '<p><b>Medico:</b> {medico}</p>','</tpl>',
                                            '<p><b>Nombre:</b> {nombre_producto}</p><p><b>Descripcion:</b> {descripcion} {codigo_unidad_medida}</p><p><b>Precio:</b> {precio}</p></div></tpl>'),
                                            typeAhead: false,
                                            triggerAction: 'all',
                                            lazyRender: true,
                                            mode: 'remote',
                                            resizable:true,
                                            pageSize: 15,
                                            queryDelay: 1000,
                                            anchor: '100%',
                                            width : 250,
                                            listWidth:'450',
                                            minChars: 2 ,
                                            disabled:true,
                                            // renderer : function(value, p, record) {
                                            //     return String.format('{0}', record.data['nombre_producto']);
                                            // }

                                         }),
                    'descripcion': new Ext.form.TextField({
                            name: 'descripcion',
                            fieldLabel: 'Descripcion',
                            allowBlank:true,
                            gwidth: 150,
                            disabled : true
                    }),

                    'cantidad': new Ext.form.NumberField({
                                        name: 'cantidad',
                                        msgTarget: 'title',
                                        fieldLabel: 'Cantidad',
                                        allowBlank: false,
                                        allowDecimals: me.cantidadAllowDecimals,
                                        decimalPrecision : 6,
                                        enableKeyEvents : false,


                                }),
                    'precio_unitario': new Ext.form.NumberField({
                                        name: 'precio_unitario',
                                        msgTarget: 'title',
                                        fieldLabel: 'P/U',
                                        allowBlank: false,
                                        allowDecimals: true,
                                        decimalPrecision : 6,
                                        enableKeyEvents : true
                                }),
                    'precio_total': new Ext.form.NumberField({
                                        name: 'precio_total',
                                        msgTarget: 'title',
                                        fieldLabel: 'Total',
                                        allowBlank: false,
                                        allowDecimals: false,
                                        maxLength:10,
                                        readOnly :true
                                }),
                    'id_moneda_recibo': new Ext.form.ComboBox({
                                    name: 'id_moneda_recibo',
                                    fieldLabel: 'Moneda',
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
                                    pageSize: 15,
                                    queryDelay: 1000,
                                    //anchor: '100%',
                                    width : 450,
                                    listWidth:'450',
                                    minChars: 2 ,

                                  }),

              }


    },

    iniciarEventosProducto:function(){
    	this.detCmp.id_producto.on('select',function(c,r,i) {
            this.mestore.data.items[0].data.nombre_producto = r.data.nombre_producto;
            // this.detCmp.precio_unitario.setValue(Number(r.data.precio));
            // var tmp = this.roundTwo(Number(r.data.precio) * Number(this.detCmp.cantidad.getValue()))
            //this.detCmp.precio_total.setValue(tmp);


        	if (r.data.requiere_descripcion == 'si') {
        		this.habilitarDescripcion(true);
        	} else {
        		this.habilitarDescripcion(false);
        	}

        },this);

    },

    iniciarEventos : function () {
        this.Cmp.cambio.setValue(0);
        this.Cmp.cambio_moneda_extranjera.setValue(0);
        this.Cmp.id_formula.store.baseParams.id_punto_venta = this.data.objPadre.variables_globales.id_punto_venta;
        this.Cmp.id_sucursal.store.load({params:{start:0,limit:50},
           callback : function (r) {

           		this.Cmp.id_sucursal.setValue(this.data.objPadre.variables_globales.id_sucursal);
           		if (this.data.objPadre.variables_globales.vef_tiene_punto_venta != 'true') {
                //console.log("lelga aqui comprobante",r)
           			this.detCmp.id_producto.store.baseParams.id_sucursal = this.Cmp.id_sucursal.getValue();
                }
                this.Cmp.id_sucursal.fireEvent('select',this.Cmp.id_sucursal, this.Cmp.id_sucursal.store.getById(this.data.objPadre.variables_globales.id_sucursal));

            }, scope : this
        });

        if (this.data.objPadre.variables_globales.vef_tiene_punto_venta === 'true') {

          /*******************cambiaremos el estilo del boton guardar *********************/
          this.megrid.topToolbar.items.items[0].container.dom.style.width="75px";
          this.megrid.topToolbar.items.items[0].container.dom.style.height="35px";
          this.megrid.topToolbar.items.items[0].btnEl.dom.style.height="35px";

          /******************Cambiaremos el estilo del boton agregar detalle************************/
          this.megrid.topToolbar.items.items[1].container.dom.style.width="75px";
          this.megrid.topToolbar.items.items[1].container.dom.style.height="35px";
          this.megrid.topToolbar.items.items[1].btnEl.dom.style.height="35px";

          this.megrid.topToolbar.items.items[2].container.dom.style.width="75px";
          this.megrid.topToolbar.items.items[2].container.dom.style.height="35px";
          this.megrid.topToolbar.items.items[2].btnEl.dom.style.height="35px";

          this.megrid.topToolbar.el.dom.style.background="#8ACDE7";
          this.megrid.topToolbar.el.dom.style.borderRadius="2px";
          this.megrid.body.dom.childNodes[0].firstChild.children[0].firstChild.style.background='#FFF4EB';

          //Aqui el boton generar
          //this.arrayBotones[0].scope.form.buttons[0].container.dom.style.border="2px solid red";
          this.arrayBotones[0].scope.form.buttons[0].container.dom.style.width="20px";
          //this.arrayBotones[0].scope.form.buttons[0].btnEl.dom.style.border="2px solid blue";
          this.arrayBotones[0].scope.form.buttons[0].btnEl.dom.style.width="190px";
          this.arrayBotones[0].scope.form.buttons[0].btnEl.dom.style.height="50px";

          this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[6].childNodes[0].style.color="#7400FF";
          this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[6].childNodes[0].style.fontWeight="bold";
          this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[6].childNodes[0].style.fontSize="15px";
      /********************************************************************************/
			this.Cmp.id_punto_venta.store.baseParams.id_punto_venta = this.data.objPadre.variables_globales.id_punto_venta;
	        this.Cmp.id_punto_venta.store.load({params:{start:0,limit:this.tam_pag},
	           callback : function (r) {
	                this.Cmp.id_punto_venta.setValue(this.data.objPadre.variables_globales.id_punto_venta);
	           		    this.detCmp.id_producto.store.baseParams.id_punto_venta = this.Cmp.id_punto_venta.getValue();
	                this.Cmp.id_punto_venta.fireEvent('select',this.Cmp.id_punto_venta, this.Cmp.id_punto_venta.store.getById(this.data.objPadre.variables_globales.id_punto_venta));

	            }, scope : this
	        });
	    }

	    if (this.data.objPadre.variables_globales.vef_tiene_punto_venta === 'true') {
    	    this.Cmp.id_punto_venta.on('select',function(c,r,i) {
    	    	if (this.accionFormulario != 'EDIT') {
                  /*Aumentando para que nos filtre siempre efectivo (CASH)*/
                  this.Cmp.id_instancia_pago.store.baseParams.defecto = 'si';
                	this.Cmp.id_moneda.store.baseParams.filtrar_base = 'si';
                  /*********************************************************/
               }
                this.cargarFormaPago();

            },this);
        }

        this.ocultarComponente(this.Cmp.mco_2);
        this.ocultarComponente(this.Cmp.numero_tarjeta_2);
        this.ocultarComponente(this.Cmp.codigo_tarjeta_2);
        this.ocultarComponente(this.Cmp.id_auxiliar_2);
        this.ocultarComponente(this.Cmp.tipo_tarjeta_2);
        this.cargarInstanciaPago();

       /********************************Aumemtando condicios para el id moneda****************************************/
       this.Cmp.id_moneda.on('select',function(c,r,i) {
         /*Aqui aumentamos para tener el cambio correcto en relacion a la moneda del detalle*/
         // if (this.id_moneda_recibo_cambio != undefined) {
         //   if (r.data.id_moneda == 2 && this.id_moneda_recibo_cambio != 2) {
         //
         //   }
         // }
         /***********************************************************************************/

         //if(r.data.id_moneda == 2){
            this.devolverCambio();
        //  }
       },this);

       this.Cmp.id_moneda_2.on('select',function(c,r,i) {
         /*Modificar el monto cambio Ismael Valdivia 2020*/
         if (this.Cmp.id_moneda.value != 2 && this.Cmp.id_moneda_2.value == 2) {
            if (this.id_moneda_recibo_cambio == 2) {
              this.Cmp.monto_forma_pago_2.setValue(((this.suma_total*this.tipo_cambio)-this.Cmp.monto_forma_pago.getValue())/this.tipo_cambio);
            } else {
              this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue())/this.tipo_cambio);
            }
         }else if (this.Cmp.id_moneda.value == 2 && this.Cmp.id_moneda_2.value == 2) {
           if (this.id_moneda_recibo_cambio == 2) {
              this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()));
           } else {
              this.Cmp.monto_forma_pago_2.setValue(((this.suma_total/this.tipo_cambio)-this.Cmp.monto_forma_pago.getValue()));
            }
         }else if (this.Cmp.id_moneda.value == 2 && this.Cmp.id_moneda_2.value != 2) {
           if (this.id_moneda_recibo_cambio == 2) {
              this.Cmp.monto_forma_pago_2.setValue(((this.suma_total-this.Cmp.monto_forma_pago.getValue())*this.tipo_cambio));
           } else {
              this.Cmp.monto_forma_pago_2.setValue((this.suma_total-(this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)));
            }
         }
         else{
             this.Cmp.monto_forma_pago_2.setValue(this.suma_total-this.Cmp.monto_forma_pago.getValue());
         }

       },this);

       this.Cmp.monto_forma_pago.on('keyup', function (cmp, e){         
       this.obtenersuma();
       this.devolverCambio();

     },this);
     /**************************************************************************************************************/

        this.Cmp.id_sucursal.on('select',function(c,r,i) {
        	if (this.accionFormulario != 'EDIT') {
            /*Comentando para agregar InstanciaPago*/
            //this.Cmp.id_forma_pago.store.baseParams.defecto = 'si';
            this.Cmp.id_instancia_pago.store.baseParams.defecto = 'si';
            this.Cmp.id_instancia_pago.store.baseParams.filtrar_base = 'si';
            }
            if (this.data.objPadre.tipo_factura == 'manual') {
            	this.Cmp.id_dosificacion.reset();
            	this.Cmp.id_dosificacion.store.baseParams.id_sucursal = this.Cmp.id_sucursal.getValue();
            	this.Cmp.id_dosificacion.modificado = true;
            }
            this.cargarFormaPago();

        },this);


        if (this.data.objPadre.tipo_factura == 'manual') {
	        this.Cmp.fecha.on('blur',function(c) {

	            if (this.data.objPadre.tipo_factura == 'manual') {
	            	this.Cmp.id_dosificacion.reset();
	            	this.Cmp.id_dosificacion.modificado = true;
	            	this.Cmp.id_dosificacion.setDisabled(false);
	            	this.Cmp.id_dosificacion.store.baseParams.fecha = this.Cmp.fecha.getValue().format('d/m/Y');
	            	this.Cmp.id_dosificacion.store.baseParams.tipo = 'manual';
	            }
	            this.cargarFormaPago();

	        },this);
	    }


        this.detCmp.tipo.on('select',function(c,r,i) {          ;
            this.cambiarCombo(r.data.field1);
        },this);



        this.Cmp.id_cliente.on('select',function(c,r,i) {
            if (r.data) {
                this.Cmp.nit.setValue(r.data.nit);
            } else {
                this.Cmp.nit.setValue(r.nit);
            }
        },this);


        this.Cmp.nit.on('blur',function(c) {

        	if (this.Cmp.nit.getValue() != '') {
        		this.Cmp.id_cliente.store.baseParams.nit = this.Cmp.nit.getValue();
            	this.Cmp.id_cliente.store.load({params:{start:0,limit:1},
		           callback : function (r) {
		           		this.Cmp.id_cliente.store.baseParams.nit = '';
		           		if (r.length == 1) {

		           			this.Cmp.id_cliente.setValue(r[0].data.id_cliente);
		           			}

		            }, scope : this
		        });
		    }
        },this);

        this.Cmp.id_formula.on('select',function(c,r,i) {
            if (r.data) {
                var formu = r.data.id_formula;
                if (formu != 0) {
                  this.eliminarAnteriores();
                  //this.successRecuperarDatos();
                }
            }
        },this);

        this.iniciarEventosProducto();

    },

    roundTwo: function(can){
    	 return  Math.round(can*Math.pow(10,2))/Math.pow(10,2);
    },

	habilitarDescripcion : function(opcion) {

    	if(this.detCmp.descripcion){
	    	if (opcion) {
	    		this.detCmp.descripcion.setDisabled(false);
	    		this.detCmp.descripcion.allowBlank = false;
	    	} else {
	    		this.detCmp.descripcion.setDisabled(true);
	    		this.detCmp.descripcion.allowBlank = true;
	    		this.detCmp.descripcion.reset();
	    	}
    	}

    },

    cambiarCombo : function (tipo) {
    	this.detCmp.id_producto.setDisabled(false);
    	this.detCmp.id_producto.store.baseParams.tipo = tipo;
    	if (this.data.objPadre.variables_globales.vef_tiene_punto_venta === 'true') {
    		this.detCmp.id_producto.store.baseParams.id_punto_venta = this.Cmp.id_punto_venta.getValue();
    	} else {
    		this.detCmp.id_producto.store.baseParams.id_sucursal = this.Cmp.id_sucursal.getValue();
    	}
    	this.detCmp.id_producto.modificado = true;
    	this.detCmp.id_producto.reset();
    },
    cargarFormaPago : function () {
        if (this.data.objPadre.variables_globales.vef_tiene_punto_venta === 'true') {
          /*comentando para incluir InstanciaPago*/
          //this.Cmp.id_forma_pago.store.baseParams.id_punto_venta = this.Cmp.id_punto_venta.getValue();
          //this.Cmp.id_forma_pago.store.baseParams.id_sucursal = this.Cmp.id_sucursal.getValue();
          //this.Cmp.id_forma_pago_2.store.baseParams.id_sucursal = this.Cmp.id_sucursal.getValue();
    	} else {
        /*comentando para incluir InstanciaPago*/
    		//this.Cmp.id_forma_pago.store.baseParams.id_sucursal = this.Cmp.id_sucursal.getValue();
      	}
        if (this.accionFormulario == 'EDIT' || this.accionFormulario == 'NEW' ) {

          /*Aumentando para instancia de pago*/
          this.Cmp.id_moneda.store.load({params:{start:0,limit:50},
                 callback : function (r) {
                      if (r.length == 1 ) {
                            this.Cmp.id_moneda.setValue(r[0].data.id_moneda);
                            this.Cmp.id_moneda.fireEvent('select', this.Cmp.id_moneda,r[0],0);
                        }
                        this.Cmp.id_moneda.fireEvent('select', this.Cmp.id_moneda,this.Cmp.id_moneda.store.getById(this.Cmp.id_moneda.getValue()),0);
                        this.Cmp.id_moneda.store.baseParams.filtrar_base = 'no';

                  }, scope : this
              });

              this.Cmp.id_instancia_pago.store.load({params:{start:0,limit:50},
      		           callback : function (r) {
                                if (r.length == 1 ) {
                                    this.Cmp.id_instancia_pago.setValue(r[0].data.id_instancia_pago);
                                    this.Cmp.id_instancia_pago.fireEvent('select', this.Cmp.id_instancia_pago,r[0],0);
                                }
                                    this.Cmp.id_instancia_pago.fireEvent('select', this.Cmp.id_instancia_pago,this.Cmp.id_instancia_pago.store.getById(this.Cmp.id_instancia_pago.getValue()),0);
                                    this.Cmp.id_instancia_pago.store.baseParams.defecto = 'no';

      		            }, scope : this
      		        });
        }
        if (this.accionFormulario == 'EDIT') {
          this.mostrarComponente(this.Cmp.habilitar_edicion);
          this.Cmp.cambio.setValue(0);
          this.Cmp.cambio_moneda_extranjera.setValue(0);
          /*******************Mostramos si se tiene excento***********************/
          if (this.Cmp.excento.getValue() == 0) {
            this.ocultarComponente(this.Cmp.excento);
          } else {
            this.mostrarComponente(this.Cmp.excento);
          }
          /**********************************************************************/

          /*****************Habilitamos los campos si se pone editar***************/
          this.Cmp.habilitar_edicion.setValue('NO');

          this.Cmp.nit.setDisabled(true);
          this.Cmp.id_cliente.setDisabled(true);
          this.Cmp.id_formula.setDisabled(true);
          this.Cmp.observaciones.setDisabled(true);
          this.Cmp.id_punto_venta.setDisabled(true);
          this.Cmp.excento.setDisabled(true);




                //  this.Cmp.id_formula.fireEvent('select',this.Cmp.id_formula, this.Cmp.id_formula.store.getById(this.data.datos_originales.data.id_formula));
          this.Cmp.habilitar_edicion.on('select',function(c,r,i) {

            if (r.data.value == 'NO') {
              this.Cmp.nit.setDisabled(true);
              this.Cmp.id_cliente.setDisabled(true);
              this.Cmp.id_formula.setDisabled(true);
              this.Cmp.observaciones.setDisabled(true);
              this.Cmp.id_punto_venta.setDisabled(true);
              this.Cmp.excento.setDisabled(true);
              this.megrid.topToolbar.items.items[0].setDisabled(true);
              this.megrid.topToolbar.items.items[1].setDisabled(true);
              this.megrid.topToolbar.items.items[2].setDisabled(true);
              this.megrid.topToolbar.items.items[3].setDisabled(true);
              this.megrid.colModel.config[3].editor='';
              this.megrid.colModel.config[4].editor='';
              this.megrid.colModel.config[5].editor='';
              //this.mestore.rollbChanges();

            } else {
              this.Cmp.nit.setDisabled(false);
              this.Cmp.id_cliente.setDisabled(false);
              this.Cmp.id_formula.setDisabled(false);
              this.Cmp.observaciones.setDisabled(false);
              this.Cmp.id_punto_venta.setDisabled(false);
              this.Cmp.excento.setDisabled(false);
              this.megrid.topToolbar.items.items[0].setDisabled(false);
              this.megrid.topToolbar.items.items[1].setDisabled(false);
              this.megrid.topToolbar.items.items[2].setDisabled(false);
              this.megrid.topToolbar.items.items[3].setDisabled(false);

              /*************************Habilitar la grilla para editar*************************/
              this.megrid.colModel.config[3].editor=this.editarDescripcion;
              this.megrid.colModel.config[4].editor=this.detCmp.cantidad;
              this.megrid.colModel.config[5].editor=this.detCmp.precio_unitario;

            }
          },this);
          /**************************************************************************/
          /*Aqui cargamos el combo q se selecciono*/
          this.Cmp.id_formula.store.load({params:{start:0,limit:50},
             callback : function (r) {
                this.Cmp.id_formula.setValue(this.data.datos_originales.data.id_formula);
                //  this.Cmp.id_formula.fireEvent('select',this.Cmp.id_formula, this.Cmp.id_formula.store.getById(this.data.datos_originales.data.id_formula));
              }, scope : this
          });
        /*************************************/

      }
    },

    onCancelAdd: function(re,save){
        if(this.sw_i_add){
            this.mestore.remove(this.mestore.getAt(0));
        }

        this.sw_init_add = false;
        this.evaluaGrilla();
    },


    onUpdateRegister: function() {
        this.sw_init_add = false;
        //this.mestore.commitChanges();

    },



    onAfterEdit:function(re, o, rec, num){
        //set descriptins values ...  in combos boxs
        var cmb_rec = this.detCmp['id_producto'].store.getById(rec.get('id_producto'));
        if(cmb_rec) {
            if (cmb_rec.get('codigo_unidad_medida')) {
                rec.set('nombre_producto', cmb_rec.get('nombre_producto') + ' (' + cmb_rec.get('codigo_unidad_medida') + ')');
            } else {
                rec.set('nombre_producto', cmb_rec.get('nombre_producto'));
            }
        }

        var cmb_rec = this.detCmp['id_moneda_recibo'].store.getById(rec.get('id_moneda_recibo'));
        console.log("aqui llega el dato para mostrar en la grilla",cmb_rec);
        if(cmb_rec) {

            rec.set('codigo_internacional', cmb_rec.get('codigo_internacional'));
        }


        this.obtenersuma();


    },

    obtenersuma: function () {
      var total_datos = this.megrid.store.data.items.length;
      var suma = 0;
      for (var i = 0; i < total_datos; i++) {
          suma = suma + parseFloat(this.megrid.store.data.items[i].data.precio_total);
      }
      this.suma_total = suma;
      this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[6].childNodes[0].style.color="#7400FF";
      this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[6].childNodes[0].style.fontWeight="bold";
      this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[6].childNodes[0].style.fontSize="20px";
      //console.log("La suma total es",this.suma_total);
    },


    evaluaRequistos: function(){
    	//valida que todos los requistosprevios esten completos y habilita la adicion en el grid
     	//solo validar que tenga sucursal o punto de venta
     	if (this.data.objPadre.variables_globales.vef_tiene_punto_venta === 'true') {
     	      if (this.Cmp.id_punto_venta.getValue() != '' && this.Cmp.id_punto_venta.getValue() != undefined) {
     	          return true;
     	      } else {
     	          return false;
     	      }
     	} else {
     	      if (this.Cmp.id_sucursal.getValue() != '' && this.Cmp.id_sucursal.getValue() != undefined) {

                  return true;
              } else {

                  return false;
              }
     	}

    },
    bloqueaRequisitos: function(sw){
    	this.Cmp.id_sucursal.setDisabled(sw);

    },

    evaluaGrilla: function(){
    	//al eliminar si no quedan registros en la grilla desbloquea los requisitos en el maestro
    	var  count = this.mestore.getCount();
    	if(count == 0){
    		this.bloqueaRequisitos(false);
    	}
    },
    buildDetailGrid: function(){

        //cantidad,detalle,peso,totalo
        var Items = Ext.data.Record.create([{
                        name: 'cantidad',
                        type: 'int'
                    }, {
                        name: 'id_producto',
                        type: 'int'
                    },{
                        name: 'tipo',
                        type: 'string'
                    }
                    ]);

        this.mestore = new Ext.data.JsonStore({
                    url: '../../sis_ventas_facturacion/control/VentaDetalle/listarVentaDetalle',
                    id: 'id_venta_detalle',
                    root: 'datos',
                    totalProperty: 'total',
                    fields: [
                        {name:'id_venta_detalle', type: 'numeric'},
                        {name:'id_venta', type: 'numeric'},
                        {name:'nombre_producto', type: 'string'},
                        {name:'id_producto', type: 'numeric'},
                        {name:'tipo', type: 'string'},
                        {name:'descripcion', type: 'string'},
                        {name:'requiere_descripcion', type: 'string'},
                        {name:'estado_reg', type: 'string'},
                        {name:'cantidad', type: 'numeric'},
                        {name:'precio_unitario', type: 'numeric'},
                        {name:'precio_total', type: 'numeric'},
                        {name:'id_usuario_ai', type: 'numeric'},
                        {name:'usuario_ai', type: 'string'},
                        {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                        {name:'id_usuario_reg', type: 'numeric'},
                        {name:'id_usuario_mod', type: 'numeric'},
                        {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                        {name:'usr_reg', type: 'string'},
                        {name:'usr_mod', type: 'string'},

                    ],
                    remoteSort: true,
                    baseParams: {dir:'ASC',sort:'id_venta_detalle',limit:'50',start:'0'}
                });

            this.editorDetail = new Ext.ux.grid.RowEditor({

                });


        this.summary = new Ext.ux.grid.GridSummary();

        //Ext.ux.grid.GridSummary.Calculations['total_precio_sum'];



        // this.editorDetail.on('beforeedit', this.onInitAdd , this);
        // //al cancelar la edicion
        // this.editorDetail.on('canceledit', this.onCancelAdd , this);
        //
        // //al cancelar la edicion
        // this.editorDetail.on('validateedit', this.onUpdateRegister, this);
        //
        //this.editorDetail.on('afteredit', this.onAfterEdit, this);
        //
        // //eventos de rror en los combos
        // this.detCmp.id_producto.store.on('exception', this.conexionFailure);
        //


    //    console.log("llega editar",this.editorDetail.on('beforeedit'));



        this.megrid = new Ext.grid.EditorGridPanel({
                    layout: 'fit',
                    store:  this.mestore,
                    region: 'center',
                    split: true,
                    border: false,
                    loadMask : true,
                    clicksToEdit: 2,
                    plain: true,
                    plugins: [this.summary],
                    stripeRows: true,
                    tbar: [
                      {
                  	    text: '<div style="font-weight:bold; font-size:15px;"><img src="../../../lib/imagenes/facturacion/guardar.png" style="width:30px; vertical-align: middle;"> Guardar</div>',
                        scope: this,
              			    handler: function(btn) {
                          this.guardarDetalles();
              			    }
                      },
                      {
                      text: '<div style="font-weight:bold; font-size:15px;"><img src="../../../lib/imagenes/facturacion/anadir.png" style="width:30px; vertical-align: middle;"> Agregar Detalle</div>',
                      scope: this,
                      id:'botonAgregar',
                        handler : function(){
                          this.formularioAgregar();
                          }
                      },
                      {
                        text: '<div style="font-weight:bold; font-size:15px;"><img src="../../../lib/imagenes/facturacion/eliminar.png" style="width:30px; vertical-align: middle;"> Eliminar</div>',
                        scope: this,
                          handler : function(){
                            var index = this.megrid.getSelectionModel().getSelectedCell();
                            if (!index) {
                                return false;
                            }
                            var rec = this.mestore.getAt(index[0]);
                            this.mestore.remove(rec);
                            Ext.getCmp('botonAgregar').show();
                            Ext.getCmp('segunda_forma_pago').show();
                            Ext.getCmp('datos_deposito').hide();
                            this.Cmp.nro_deposito.reset();
                            this.Cmp.fecha_deposito.reset();
                            this.Cmp.monto_deposito.reset();
                            this.Cmp.cuenta_bancaria.reset();
                            this.Cmp.nro_deposito.allowBlank=true;
                            this.Cmp.fecha_deposito.allowBlank=true;
                            this.Cmp.monto_deposito.allowBlank=true;
                            this.Cmp.cuenta_bancaria.allowBlank=true;
                            this.ocultarComponente(this.Cmp.nro_deposito);
                            this.ocultarComponente(this.Cmp.fecha_deposito);
                            this.ocultarComponente(this.Cmp.monto_deposito);
                            this.ocultarComponente(this.Cmp.cuenta_bancaria);
                            this.obtenersuma();
                        }


                        },
                    // {
                    //         text: '<div style="font-weight:bold; font-size:15px;"><i class="fa fa-plus-circle fa-lg"></i> Duplicar registro</div>',
                    //         scope:this,
                    //         handler: function(){
                    //             var index = this.megrid.getSelectionModel().getSelectedCell();
                    //             if (!index) {
                    //                 return false;
                    //             }
                    //             var rec = this.mestore.getAt(index[0]);
                    //             this.onDuplicateDetail(rec);
                    //             this.evaluaGrilla();
                    //             this.obtenersuma();
                    //
                    //         }
                    // }
                  ],

                    columns: [
                    new Ext.grid.RowNumberer(),
                    {
                        header: 'Tipo',
                        dataIndex: 'tipo',
                        width: 90,
                        sortable: false,
                        editor: this.detCmp.tipo
                    },
                    {
                        header: 'Producto/Servicio',
                        dataIndex: 'id_producto',
                        width: 350,
                        editable: true,
                        sortable: false,
                        renderer:function(value, p, record){
                          return String.format('{0}', record.data['nombre_producto']);
                        },
                        editor: this.detCmp.id_producto
                    },
                    {
                        header: 'Descripción',
                        dataIndex: 'descripcion',
                        width: 300,
                        sortable: false,
                        editor: this.detCmp.descripcion
                    },
                    {

                        header: 'Cantidad',
                        dataIndex: 'cantidad',
                        align: 'right',
                        width: 75,
                        summaryType: 'sum',
                        editor: this.detCmp.cantidad
                    },
                    {

                        header: 'P / Unit',
                        dataIndex: 'precio_unitario',
                        align: 'right',
                        width: 85,
                        summaryType: 'sum',
                        editor: this.detCmp.precio_unitario
                    },
                    {
                        xtype: 'numbercolumn',
                        header: 'Total',
                        dataIndex: 'precio_total',
                        align: 'right',
                        width: 150,/*irva222*/
                        format: '0,0.00',
                        summaryType: 'sum',
                        editor: this.detCmp.precio_total
                    },
                    {
                        header: 'Moneda',
                        dataIndex: 'id_moneda_recibo',
                        width: 350,
                        editable: true,
                        sortable: false,
                        renderer:function(value, p, record){
                          console.log("aqui llega moneda formato",record);
                          return String.format('{0}', record.data['desc_moneda_recibo']);
                        },
                        //editor: this.detCmp.id_moneda_recibo
                    },
                  ]
                });
    },

    guardarDetalles : function(){
      for (var i = 0; i < this.megrid.store.data.items.length; i++) {
        this.megrid.store.data.items[i].data.precio_total=(this.megrid.store.data.items[i].data.precio_unitario * this.megrid.store.data.items[i].data.cantidad);
      }
      this.mestore.commitChanges();
      this.megrid.getView().refresh();
      this.obtenersuma();

      /*Aqui obtenemos el monto que se pagara*/
      this.id_moneda_recibo_cambio = this.variables.items.items[6].getValue();
      /*Reseteamos el valor actual para poner el seleccionado*/
      this.Cmp.id_moneda_venta_recibo.reset();
      this.Cmp.id_moneda_venta_recibo.setValue(this.id_moneda_recibo_cambio);
      /*******************************************************/
      /*Si la moneda es dolar y la moneda a pagar es Bs el monto convertimos a bs*/
      if (this.id_moneda_recibo_cambio == 2 && this.Cmp.id_moneda.getValue() != 2) {
        this.Cmp.monto_forma_pago.setValue(this.suma_total*this.tipo_cambio);
      }
      /*Si la moneda es en Bs y la moneda a pagar es a Bs*/
      else if (this.id_moneda_recibo_cambio != 2 && this.Cmp.id_moneda.getValue() != 2) {
        this.Cmp.monto_forma_pago.setValue(this.suma_total);
      }
      else if (this.id_moneda_recibo_cambio != 2 && this.Cmp.id_moneda.getValue() == 2) {
        this.Cmp.monto_forma_pago.setValue(this.suma_total/this.tipo_cambio);
      }
      else if (this.id_moneda_recibo_cambio == 2 && this.Cmp.id_moneda.getValue() == 2) {
        this.Cmp.monto_forma_pago.setValue(this.suma_total);
      }
      /**************************************/
      Ext.getCmp('botonAgregar').hide();
    },


    formularioAgregar : function(){

      var simple = new Ext.FormPanel({
       labelWidth: 100, // label settings here cascade unless overridden
       frame:true,
       bodyStyle:'margin-left:-7px; margin-top:-7px; padding:10px 10px 0; background:#6EC8E3;',
       width: 650,
       height:250,
       defaultType: 'textfield',
       items: [new Ext.form.ComboBox({
                           name: 'tipo',
                           fieldLabel: 'Tipo detalle',
                           allowBlank:false,
                           emptyText:'Tipo...',
                           typeAhead: true,
                           hidden:true,
                           triggerAction: 'all',
                           lazyRender:true,
                           mode: 'local',
                           gwidth: 150,
                           style:{
                             width: '200px'
                           },
                           store:this.tipoDetalleArray
                   }),
                   new Ext.form.ComboBox({
                                                       name: 'id_producto',
                                                       fieldLabel: 'Producto/Servicio',
                                                       allowBlank: false,
                                                       emptyText: 'Productos...',
                                                       store: new Ext.data.JsonStore({
                                                           url: '../../sis_ventas_facturacion/control/Servicios/listarServicios',
                                                           id: 'id_producto',
                                                           root: 'datos',
                                                           sortInfo: {
                                                               field: 'desc_ingas',
                                                               direction: 'ASC'
                                                           },
                                                           totalProperty: 'total',
                                                           fields: ['id_concepto_ingas', 'tipo','desc_moneda','id_moneda','desc_ingas','requiere_descripcion','precio','excento','codigo'],
                                                           remoteSort: true,
                                                           baseParams: {par_filtro: 'ingas.desc_ingas',facturacion:'RO'}
                                                       }),
                                                       valueField: 'id_concepto_ingas',
                                                       displayField: 'desc_ingas',
                                                       gdisplayField: 'desc_ingas',
                                                       hiddenName: 'id_producto',
                                                       forceSelection: true,
                                                       tpl: new Ext.XTemplate([
                                                          '<tpl for=".">',
                                                          '<div class="x-combo-list-item">',
                                                          '<p><b>Nombre:</b><span style="color: green; font-weight:bold;"> {desc_ingas}</span></p></p>',
                                                          '<p><b>COD:</b> <span style="color: red; font-weight:bold;">{codigo}</span></p>',
                                                          '</div></tpl>'
                                                        ]),
                                                       typeAhead: false,
                                                       triggerAction: 'all',
                                                       lazyRender: true,
                                                       mode: 'remote',
                                                       resizable:true,
                                                       pageSize: 15,
                                                       queryDelay: 1000,
                                                       //anchor: '100%',
                                                       width : 450,
                                                       listWidth:'450',
                                                       minChars: 2 ,
                                                       disabled:true,

                                                     }),
                                                     new Ext.form.TextField({
                                                            name: 'descripcion',
                                                            fieldLabel: 'Descripcion',
                                                            allowBlank:true,
                                                            width : 450,
                                                            hidden:true,
                                                            //anchor:'10',
                                                            //gwidth: 150,
                                                            disabled : true
                                                    }),

                                                    new Ext.form.NumberField({
                                                                        name: 'cantidad',
                                                                        msgTarget: 'title',
                                                                        fieldLabel: 'Cantidad',
                                                                        allowBlank: false,
                                                                        width : 450,
                                                                        //allowDecimals: me.cantidadAllowDecimals,
                                                                        decimalPrecision : 6,
                                                                        enableKeyEvents : true,


                                                                }),
                                                    new Ext.form.NumberField({
                                                                        name: 'precio_unitario',
                                                                        msgTarget: 'title',
                                                                        fieldLabel: 'P/U',
                                                                        allowBlank: false,
                                                                        allowDecimals: true,
                                                                        decimalPrecision : 6,
                                                                        width : 450,
                                                                        enableKeyEvents : true
                                                                }),
                                                     new Ext.form.NumberField({
                                                                        name: 'precio_total',
                                                                        msgTarget: 'title',
                                                                        fieldLabel: 'Total',
                                                                        width : 450,
                                                                        allowBlank: false,
                                                                        allowDecimals: false,
                                                                        maxLength:10,
                                                                        readOnly :true
                                                                }),
                                                      new Ext.form.ComboBox({
                                                                      name: 'id_moneda_recibo',
                                                                      fieldLabel: 'Moneda',
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
                                                                      pageSize: 15,
                                                                      queryDelay: 1000,
                                                                      //anchor: '100%',
                                                                      width : 450,
                                                                      listWidth:'450',
                                                                      minChars: 2 ,

                                                                    }),
                                                  ]

   });
   this.variables = simple;
   /*Aumentando para Filtrar los servicios por id_punto_venta y el tipo del PV (ATO CTO)*/
   //this.variables.items.items[1].store.baseParams.id_punto_venta_producto = this.data.objPadre.variables_globales.id_punto_venta;
   //this.variables.items.items[1].store.baseParams.tipo_pv = this.data.objPadre.tipo_punto_venta;
   // /************************************************************************************************/


      var win = new Ext.Window({
        title: '<center><span style="vertical-align: middle; font-size:25px; font-weight:bold; color:#1479B8; text-shadow: 3px 0px 0px #000000;">AGREGAR CONCEPTO</span></center>', //the title of the window
        width:600,
        height:250,
        closeAction:'hide',
        modal:true,
        plain: true,
        items:simple,
        buttons: [{
                    text:'<div style="font-weight:bold; font-size:12px;"><img src="../../../lib/imagenes/facturacion/aceptar.png" style="width:15px; vertical-align: middle;"> <span style="vertical-align: middle; font-size:15px;">Guardar</span></div>',
                    scope:this,
                    handler: function(){
                        this.insertarNuevo(win);
                    }
                },{
                    text: '<div style="font-weight:bold; font-size:12px;"><img src="../../../lib/imagenes/facturacion/cancelar.png" style="width:15px; vertical-align: middle;"> <span style="vertical-align: middle; font-size:13px;">Cancelar</span></div>',
                    handler: function(){
                        win.hide();
                    }
                }]

      });
      win.show();

      /****************************Aqui recuperaremos por defecto*******************************/
      this.variables.items.items[0].setValue('servicio');//esta parte hay que ver mas adelante si se cambiara
      this.variables.items.items[0].fireEvent('select',this.variables.items.items[0],'servicio');
      this.ComboIdProducto(this.variables.items.items[0].getValue());
      /*****************************************************************************************/

      // this.variables.items.items[0].on('select',function(c,r,i) {
      //     this.ComboIdProducto(r.data.field1);
      // },this);

    },

    ComboIdProducto : function (tipo) {

      /*Aqui recuperamos el tipo del producto*/
    	this.variables.items.items[1].setDisabled(false);
    	this.variables.items.items[1].store.baseParams.tipo = tipo;
    	if (this.data.objPadre.variables_globales.vef_tiene_punto_venta === 'true') {
    		this.variables.items.items[1].store.baseParams.id_punto_venta = this.Cmp.id_punto_venta.getValue();
        this.variables.items.items[1].on('select',function(c,r,i) {
          /********************LA CONDICION AQUI DEL CODIGO DE RO*********************/
            if (r.data.codigo == 'ROAC' || r.data.codigo == 'ROPC') {
              //this.mostrarComponente(this.Cmp.boleta);
              this.instanciasPagoAnticipo = 'si';
              this.mostrarComponente(this.Cmp.id_auxiliar_anticipo);
              this.Cmp.id_auxiliar_anticipo.allowBlank=false;
              Ext.getCmp('datos_deposito').show();
              this.fecha_actual_RO =   moment().format("DD/MM/YYYY");
            } else {
              this.instanciasPagoAnticipo = 'no';
              this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
              this.Cmp.id_auxiliar_anticipo.reset();
              this.Cmp.id_auxiliar_anticipo.allowBlank=true;
              Ext.getCmp('datos_deposito').hide();
            }
          /***************************************************************************/
            if (r.data.requiere_descripcion == 'si') {
              this.mostrarComponente(this.variables.items.items[2]);
              this.variables.items.items[2].setDisabled(false);
            } else if (r.data.requiere_descripcion != 'si')  {
              this.ocultarComponente(this.variables.items.items[2]);
              this.variables.items.items[2].setDisabled(true);
    	    		this.variables.items.items[2].allowBlank = true;
      	    	this.variables.items.items[2].reset();
              }
              /*************************Recuperamos el precio unitario (irva recuperacion dato)************************************/
               this.variables.items.items[3].setValue(1);
               var precio = r.data.precio;
               //this.variables.items.items[4].setValue(0/*precio*/);
               //this.variables.items.items[5].setValue(this.variables.items.items[3].getValue()*precio);
             /*********************************************************************************************/


        },this);

          /*Aumentamos para cuando se seleccione la moneda igual se seleccione en el combo de la forma de pago*/
          this.variables.items.items[6].on('select',function(c,r,i) {
            this.Cmp.id_moneda.store.load({params:{start:0,limit:50},
               callback : function (r) {
               		this.Cmp.id_moneda.setValue(this.variables.items.items[6].getValue());
                  this.Cmp.id_moneda.fireEvent('select',this.Cmp.id_moneda, this.Cmp.id_moneda.store.getById(this.variables.items.items[6].getValue()));
                }, scope : this
            });
          },this);
          /****************************************************************************************************/



    	} else {
    		this.variables.items.items[1].store.baseParams.id_sucursal = this.Cmp.id_sucursal.getValue();
    	}
    	this.variables.items.items[1].modificado = true;
    	this.variables.items.items[1].reset();

      this.variables.items.items[3].on('change',function(field,newValue,oldValue){
        this.variables.items.items[5].setValue(this.variables.items.items[3].getValue()*this.variables.items.items[4].getValue());
      },this);//monto_forma_pago

      this.variables.items.items[4].on('change',function(field,newValue,oldValue){
        this.variables.items.items[5].setValue(this.variables.items.items[3].getValue()*this.variables.items.items[4].getValue());

      },this);//monto_forma_pago



    },


    insertarNuevo : function (win) {
      if (this.variables.items.items[0].getValue() == '' || this.variables.items.items[1].getValue() == '' || this.variables.items.items[1].lastSelectionText == ''
        || this.variables.items.items[3].getValue() == '' || this.variables.items.items[4].getValue() == '' || this.variables.items.items[5].getValue() == ''
        || this.variables.items.items[6].getValue() == '') {
          Ext.Msg.show({
  			   title:'Información',
  			   msg: 'Complete los campos para guardar el detalle!',
  			   buttons: Ext.Msg.OK,
           icon: Ext.MessageBox.QUESTION,
  			   scope:this
  			});
      } else {
      var grillaRecord =  Ext.data.Record.create([
        {name:'id_venta_detalle', type: 'numeric'},
          {name:'id_venta', type: 'numeric'},
          {name:'nombre_producto', type: 'string'},
          {name:'id_producto', type: 'numeric'},
          {name:'tipo', type: 'string'},
          {name:'descripcion', type: 'string'},
          {name:'requiere_descripcion', type: 'string'},
          {name:'estado_reg', type: 'string'},
          {name:'cantidad', type: 'numeric'},
          {name:'precio_unitario', type: 'numeric'},
          {name:'precio_total', type: 'numeric'},
          {name:'id_usuario_ai', type: 'numeric'},
          {name:'usuario_ai', type: 'string'},
          {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
          {name:'id_usuario_reg', type: 'numeric'},
          {name:'id_usuario_mod', type: 'numeric'},
          {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
          {name:'usr_reg', type: 'string'},
          {name:'usr_mod', type: 'string'}
    ]);
    var myNewRecord = new grillaRecord({
      tipo: this.variables.items.items[0].getValue(),
      id_producto: this.variables.items.items[1].getValue(),
      nombre_producto: this.variables.items.items[1].lastSelectionText,
      descripcion: this.variables.items.items[2].getValue(),
      cantidad: this.variables.items.items[3].getValue(),
      precio_unitario: this.variables.items.items[4].getValue(),
      precio_total:this.variables.items.items[5].getValue(),            //
      id_moneda_recibo:this.variables.items.items[6].getValue(),            //
      desc_moneda_recibo:this.variables.items.items[6].lastSelectionText,            //
      });

      this.mestore.add(myNewRecord);
    //  this.mestore.commitChanges();
      this.obtenersuma();
      this.guardarDetalles();
      win.hide();
    }

    },

    eliminarAnteriores : function () {
      for (var i = this.mestore.data.length; i >= 0; i--) {
              var suma_eli = 0;
              suma_eli = suma_eli + i;
              var dato = 0;
              dato = suma_eli - 1;
              if(dato == (-1) ){
                dato = 0;
              }
              if (suma_eli == 0 ) {
                  this.successRecuperarDatos();
              } else if (suma_eli >= 0 )  {
                 this.mestore.remove(this.mestore.getAt(dato));
               }
        }
      },

      successRecuperarDatos : function () {
          Ext.Ajax.request({
              url:'../../sis_ventas_facturacion/control/Venta/insertarFormula',
              params:{id_formula:this.Cmp.id_formula.getValue(),
                      id_sucursal:this.Cmp.id_sucursal.getValue()},
              success: function(resp){
                  var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                  this.nombre_producto = reg.ROOT.datos.v_nombre_producto;
                  this.id_producto_recu = reg.ROOT.datos.v_id_producto;
                  this.id_formula_recu = reg.ROOT.datos.v_id_formula;
                  this.producto_nombre = this.nombre_producto.split(",");
                  this.producto_id = this.id_producto_recu.split(",");
                  this.id_formula = this.id_formula_recu.split(",");
                  this.tiene_excento = reg.ROOT.datos.v_excento_req;
                  this.excento_formula = reg.ROOT.datos.v_requiere_excento;
                  this.precio_inde = reg.ROOT.datos.v_precio;
                  this.producto_nombre = this.nombre_producto.split(",");
                  this.producto_id = this.id_producto_recu.split(",");
                  this.id_formula = this.id_formula_recu.split(",");
                  this.req_excento = this.tiene_excento.split(",");
                  this.precio_form = this.precio_inde.split(",");

                  var grillaRecord =  Ext.data.Record.create([
                    {name:'id_venta_detalle', type: 'numeric'},
                      {name:'id_venta', type: 'numeric'},
                      {name:'nombre_producto', type: 'string'},
                      {name:'id_producto', type: 'numeric'},
                      {name:'tipo', type: 'string'},
                      {name:'descripcion', type: 'string'},
                      {name:'requiere_descripcion', type: 'string'},
                      {name:'estado_reg', type: 'string'},
                      {name:'cantidad', type: 'numeric'},
                      {name:'precio_unitario', type: 'numeric'},
                      {name:'precio_total', type: 'numeric'},
                      {name:'id_usuario_ai', type: 'numeric'},
                      {name:'usuario_ai', type: 'string'},
                      {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                      {name:'id_usuario_reg', type: 'numeric'},
                      {name:'id_usuario_mod', type: 'numeric'},
                      {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                      {name:'usr_reg', type: 'string'},
                      {name:'usr_mod', type: 'string'}
                ]);

                for (var i = 0; i < this.producto_nombre.length; i++) {
                var myNewRecord = new grillaRecord({
                  nombre_producto : this.producto_nombre[i],
              //     descripcion : request.arguments.nombre_formula,
                  id_producto: this.producto_id[i],
                  id_formula: this.id_formula[i],
                  tipo : 'formula',
                  cantidad : '1',
                  requiere_excento:this.req_excento[i],
                  precio_unitario : this.precio_form[i],
                  precio_total: this.precio_form[i]*1               //
                  });
                   this.mestore.add(myNewRecord);
              }

                  //this.mestore.commitChanges();
                  this.obtenersuma();
                  this.guardarDetalles();

              },
              failure: this.conexionFailure,
              timeout:this.timeout,
              scope:this
          });

        },


    onDuplicateDetail : function (rec) {
        var grillaRecord =  Ext.data.Record.create([
            {name:'id_venta_detalle', type: 'numeric'},
            {name:'id_venta', type: 'numeric'},
            {name:'nombre_producto', type: 'string'},
            {name:'id_producto', type: 'numeric'},
            {name:'tipo', type: 'string'},
            {name:'descripcion', type: 'string'},
            {name:'requiere_descripcion', type: 'string'},
            {name:'estado_reg', type: 'string'},
            {name:'cantidad', type: 'numeric'},
            {name:'precio_unitario', type: 'numeric'},
            {name:'precio_total', type: 'numeric'},
            {name:'id_usuario_ai', type: 'numeric'},
            {name:'usuario_ai', type: 'string'},
            {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'id_usuario_reg', type: 'numeric'},
            {name:'id_usuario_mod', type: 'numeric'},
            {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'usr_reg', type: 'string'},
            {name:'usr_mod', type: 'string'}
        ]);

        var myNewRecord = new grillaRecord({
            nombre_producto : rec.data.nombre_producto,
            descripcion : rec.data.descripcion,
            id_producto : rec.data.id_producto,
            tipo : rec.data.tipo,
            cantidad : rec.data.cantidad,
            precio_unitario : rec.data.precio_unitario,
            precio_total: rec.data.precio_total

        });
        this.mestore.add(myNewRecord);

        //this.mestore.commitChanges();
    },
    onInitAdd : function (r, i) {
    	if(this.data.readOnly===true){
    		return false
    	}
        this.detCmp.id_producto.setDisabled(true);
        var record = this.megrid.store.getAt(i);
        var recTem = new Array();
        recTem['id_producto'] = record.data['id_producto'];
        recTem['nombre_producto'] = record.data['nombre_producto'];

        this.detCmp.id_producto.store.add(new Ext.data.Record(this.arrayToObject(this.detCmp.id_producto.store.fields.keys,recTem), record.data['id_producto']));
        this.detCmp.id_producto.store.commitChanges();
        this.detCmp.id_producto.modificado = true;

        if (record.data.tipo != '' && record.data.tipo != undefined) {

            this.cambiarCombo(record.data.tipo);
        }

        if (record.data.requiere_descripcion == 'si') {

            this.habilitarDescripcion(true);
        } else {
        	this.habilitarDescripcion(false);
        }
    },
    buildGrupos: function(){
        this.Grupos = [{
                        layout: 'border',
                        border: false,
                         frame:true,
                        items:[
                          {
                            xtype: 'fieldset',
                            border: false,
                            split: true,
                            layout: 'column',
                            region: 'north',
                            collapseFirst : false,
                            collapsible: true,
                            width: '100%',
                            autoScroll:false,
                            style: {
                                   height:'100px',
                                   background: '#6ABCDF',
                                   //border:'2px solid green'
                                },
                            padding: '0 0 0 10',
                            items:[
                                   {
                                    bodyStyle: 'padding-right:5px;',
                                    autoHeight: true,
                                    border: false,
                                    items:[
                                       {
                                        xtype: 'fieldset',
                                        frame: true,
                                        border: false,
                                        layout: 'form',
                                        title: 'Datos Venta',
                                        width: '90%',
                                        style: {
                                               height:'90px',
                                               width:'590px',
                                            },
                                        padding: '0 0 0 10',
                                        bodyStyle: 'padding-left:5px;',
                                        id_grupo: 0,
                                        items: [],
                                     }]
                                 },
                                 {
                                  bodyStyle: 'padding-right:5px;',
                                  autoHeight: true,
                                  border: false,
                                  items:[
                                     {
                                      xtype: 'fieldset',
                                      frame: true,
                                      border: false,
                                      layout: 'form',
                                      style: {
                                             height:'90px',
                                             width:'300px',
                                          },
                                      padding: '0 0 0 10',
                                      bodyStyle: 'padding-left:5px;',
                                      id_grupo: 22,
                                      items: [],
                                   }]
                               },
                                 {
                                  bodyStyle: 'padding-right:5px;',
                                  border: false,
                                  autoHeight: true,
                                  items: [{
                                        xtype: 'fieldset',
                                        frame: true,
                                        layout: 'form',
                                        style: {
                                               height:'90px',
                                               width:'320px'
                                              },
                                        border: false,
                                        padding: '0 0 0 20',
                                        bodyStyle: 'padding-left:5px;',
                                        id_grupo: 1,
                                        items: [],
                                     }]
                                 },


                              ]
                          },
                            this.megrid,

                            {
                              xtype: 'fieldset',
                              border: false,
                              split: true,
                              layout: 'column',
                              region: 'south',
                              autoScroll: true,
                              collapseFirst : false,
                              collapsible: true,
                              style: {
                                       height:'220px',
                                       background:'#6ABCDF',
                                      // border:'2px solid blue'
                                     },
                              padding: '0 0 0 10',
                              items:[
                                {
                                 bodyStyle: 'padding-right:5px;',
                                 border: false,
                                 autoHeight: true,
                                 items: [{
                                       xtype: 'fieldset',
                                       frame: true,
                                       layout: 'form',
                                       title: ' Forma de Pago <br> <br>',
                                       border: false,
                                       width: 280,
                                       id_grupo: 2,
                                       items: [],
                        }]
                      },
                      {
                       bodyStyle: 'padding-right:5px;',
                       border: false,
                       autoHeight: true,
                       hidden:true,
                       id:'segunda_forma_pago',
                       items: [{
                             xtype: 'fieldset',
                             frame: true,
                             layout: 'form',
                             title: ' Forma de Pago <br> <br>',
                             border: false,
                             // style: {
                             //          border:'2px solid red'
                             //        },
                             width: 280,
                             id_grupo: 10,
                             items: [],
                           }]
                      },
                      {
                       bodyStyle: 'padding-right:5px;',
                       border: false,
                       autoHeight: true,
                       hidden:true,
                       id:'datos_deposito',
                       items: [{
                             xtype: 'fieldset',
                             frame: true,
                             layout: 'form',
                             title: ' Datos Anticipo <br> <br>',
                             border: false,
                             width: 280,
                             id_grupo: 13,
                             items: [],
                           }]
                      },
                      {
                       bodyStyle: 'padding-right:5px;',
                       border: false,
                       autoHeight: true,
                       items: [{
                             xtype: 'fieldset',
                             frame: true,
                             layout: 'form',
                             title: 'Cambio M/L <br><br>',
                             style: {
                                     width: '40%',
                                 },
                             border: false,
                             padding: '0 0 0 20',
                             bodyStyle: 'padding-left:5px;',
                             id_grupo: 11,
                             items: [],
                          }]
                      },
                      {
                       bodyStyle: 'padding-right:5px;',
                       border: false,
                       autoHeight: true,
                       items: [{
                             xtype: 'fieldset',
                             frame: true,
                             layout: 'form',
                             width: '60%',
                             title: 'Cambio M/E <br><br>',
                             border: false,
                             padding: '0 0 0 20',
                             bodyStyle: 'padding-left:5px;',
                             id_grupo: 12,
                             items: [],
                          }]
                      }
                      ]
                      }
                         ]
                 }];


    },

    loadValoresIniciales:function()
    {
       Phx.vista.FormRecibo.superclass.loadValoresIniciales.call(this);
    },
    onReset:function(o){
			this.generar = 'generar';
      if (this.mestore.modified.length == 0) {
          this.onSubmit(o);
      } else {
        Ext.Msg.show({
         title:'Información',
         msg: 'Guarde la información modificada para obtener el total correcto y poder generar el recibo!',
         buttons: Ext.Msg.OK,
         icon: Ext.MessageBox.QUESTION,
         scope:this
      });
      }
	   },

     successWizard:function(resp){
         // var rec=this.sm.getSelected();
         var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
         //console.log("llega aqui pasar estado",objRes);
         if (objRes.ROOT.datos.estado == 'finalizado' && this.tipo_factura != 'manual') {
             this.id_venta = objRes.ROOT.datos.id_venta;
             this.imprimirNota();
         }
         Phx.CP.loadingHide();
         resp.argument.wizard.panel.destroy();
         this.reload();
      },

      imprimirNota: function(){
   				Phx.CP.loadingShow();
   				Ext.Ajax.request({
   						url : '../../sis_ventas_facturacion/control/Venta/reporteRecibo',
   						params : {
                'id_venta' : this.id_venta,
   							'id_punto_venta' : this.data.objPadre.variables_globales.id_punto_venta,
   							'formato_comprobante' : this.data.objPadre.variables_globales.formato_comprobante,
   							'tipo_factura': this.data.objPadre.tipo_factura
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

    Atributos:[
        {
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
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_moneda_venta_recibo'
            },
            type:'Field',
            form:true
        },
		{
			//configuracion del componente
			config:{
				labelSeparator:'',
				inputType:'hidden',
				name: 'forma_pedido'
			},
			valorInicial:'cajero',
			type:'Field',
			form:true
		},
		{
			config:{
				name: 'nit',
				fieldLabel: 'NIT',
				allowBlank: true,
        hidden:true,
        // listeners: {
        //   afterrender: function(field) {
        //     field.focus(false);
        //   }
        // },
				width:200,
				maxLength:20
			},
			type:'NumberField',
			id_grupo:0,
			form:true,
		},
		{
			config : {
				name : 'id_cliente',
				fieldLabel : 'Razón Social Cliente',
        style:{
        //  width:'5000px',
          textTransform:'uppercase',
        },
        width:200,
				allowBlank : false,
        listeners: {
          afterrender: function(field) {
            field.focus(false);
          }
        },
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
			config : {
				name : 'id_formula',
				fieldLabel : 'Paquetes / Fórmulas',
				allowBlank : true,
        width:200,
        hidden:true,
        listWidth:'450',
        maxHeight : 450,
        resizable: true,
				emptyText : 'Paquetes...',
				store : new Ext.data.JsonStore({
          //url: '../../sis_ventas_facturacion/control/Formula/listarFormula',
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
            config : {
                name : 'id_cliente_destino',
                fieldLabel : 'Destino',
                allowBlank : false,
                emptyText : 'Destino...',
                qtip:'Cliente Destino',
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
                gdisplayField : 'cliente_destino',
                hiddenName : 'id_cliente_destino',
                forceSelection : false,
                typeAhead : false,
                tpl:'<tpl for="."><div class="x-combo-list-item"><b><p>Codigo:<font color="green">{nombre_factura}</font></b></p></div></tpl>',

                triggerAction : 'all',
                lazyRender : true,
                mode : 'remote',
                pageSize : 10,
                queryDelay : 1000,
                turl:'../../../sis_ventas_facturacion/vista/cliente/Cliente.php',
                ttitle:'Clientes',
                tasignacion : true,
                tname : 'id_cliente',
                tdata:{},
                tcls:'Cliente',
                gwidth : 170,
                minChars : 2
            },
            type : 'TrigguerCombo',
            id_grupo : 0,
            form : false
        },

		// {
	  //           config:{
	  //               name:'id_moneda',
	  //               origen:'MONEDA',
	  //               allowBlank:false,
	  //               fieldLabel:'Moneda',
	  //               gdisplayField:'desc_moneda',
	  //               gwidth:100,
		// 		    anchor: '80%'
	  //            },
	  //           type:'ComboRec',
	  //           id_grupo:0,
	  //           form:false
	  //   },
        {
            config:{
                name: 'tipo_cambio_venta',
                fieldLabel: 'Tipo Cambio',
                allowBlank: false,
                allowNegative: false,
                width:200,

            },
                type:'NumberField',
                id_grupo:0,
                form:false,
                valorInicial:'0'
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
                disabled:true,
                minChars: 2
            },
            type: 'ComboBox',
            id_grupo: 1,
            form: true
        },
        {
			config:{
				name: 'descripcion_bulto',
				fieldLabel: 'Bultos',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100
			},
				type:'TextArea',
				filters:{pfiltro:'ven.descripcion_bulto',type:'string'},
				id_grupo: 1,
				grid:true,
				form:false
		},
    // {
    //   config:{
    //     name: 'moneda_tarjeta',
    //     fieldLabel: 'Moneda',
    //     allowBlank: true,
    //     width:150,
    //     disabled:false,
    //     readOnly:true,
    //     style: {
    //       background: '#EFFFD6',
    //       color: 'red',
    //       fontWeight:'bold'
    //     },
    //     gwidth: 100
    //   },
    //     type:'TextField',
    //     id_grupo: 2,
    //     form:true
    // },
    {
        config: {
            name: 'id_moneda',
            fieldLabel: 'Moneda',
            allowBlank: false,
            width:150,
            listWidth:250,
            resizable:true,
            style: {
                 background: '#EFFFD6',
                 color: 'red',
                 fontWeight:'bold'
               },
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
            gdisplayField : 'codigo_internacional',
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
        id_grupo: 2,
        form: true
    },
    // {
    //     config: {
    //         name: 'id_forma_pago',
    //         fieldLabel: 'Forma de Pago',
    //         allowBlank: false,
    //         width:150,
    //         emptyText: 'Forma de Pago...',
    //         store: new Ext.data.JsonStore({
    //             url: '../../sis_ventas_facturacion/control/FormaPago/listarFormaPago',
    //             id: 'id_forma_pago',
    //             root: 'datos',
    //             sortInfo: {
    //                 field: 'nombre',
    //                 direction: 'ASC'
    //             },
    //             totalProperty: 'total',
    //             fields: ['id_forma_pago', 'nombre', 'desc_moneda','registrar_tarjeta','registrar_cc','codigo'],
    //             remoteSort: true,
    //             baseParams: {par_filtro: 'forpa.nombre#mon.codigo#forpa.codigo',sw_tipo_venta:'computarizada'}
    //         }),
    //         valueField: 'id_forma_pago',
    //         displayField: 'nombre',
    //         gdisplayField: 'forma_pago',
    //         hiddenName: 'id_forma_pago',
    //         tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p><b><p>Codigo:<font color="green">{codigo}</font></b></p><p><b>Moneda:<font color="red">{desc_moneda}</font></b></p> </div></tpl>',
    //         forceSelection: true,
    //         typeAhead: false,
    //         triggerAction: 'all',
    //         lazyRender: true,
    //         mode: 'remote',
    //         pageSize: 15,
    //         queryDelay: 1000,
    //         gwidth: 150,
    //         listWidth:250,
    //         resizable:true,
    //         minChars: 2,
    //         disabled:false,
    //         renderer : function(value, p, record) {
    //             return String.format('{0}', record.data['forma_pago']);
    //         }
    //     },
    //     type: 'ComboBox',
    //     id_grupo: 2,
    //     grid: true,
    //     form: true
    // },
    /************************Aumentando instancia de pago*****************************************/
    {
        config: {
            name: 'id_instancia_pago',
            fieldLabel: 'Instancia de pago',
            allowBlank: false,
            width:150,
            emptyText: 'Instancia de pago...',
            store: new Ext.data.JsonStore({
                url: '../../sis_obingresos/control/InstanciaPago/listarInstanciaPago',
                id: 'id_instancia_pago',
                root: 'datos',
                sortInfo: {
                    field: 'nombre',
                    direction: 'ASC'
                },
                totalProperty: 'total',
                fields: ['id_instancia_pago', 'nombre', 'codigo_forma_pago'],
                remoteSort: true,
                baseParams: {par_filtro: 'insp.nombre#insp.codigo_forma_pago'}
            }),
            valueField: 'id_instancia_pago',
            displayField: 'nombre',
            gdisplayField: 'codigo_forma_pago',
            hiddenName: 'id_instancia_pago',
            tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p><b><p>Codigo:<font color="green">{codigo_forma_pago}</font></b></p></div></tpl>',
            forceSelection: true,
            typeAhead: false,
            triggerAction: 'all',
            lazyRender: true,
            mode: 'remote',
            pageSize: 15,
            queryDelay: 1000,
            gwidth: 150,
            listWidth:250,
            resizable:true,
            minChars: 2,
            disabled:false,
            renderer : function(value, p, record) {
                return String.format('{0}', record.data['codigo_forma_pago']);
            }
        },
        type: 'ComboBox',
        id_grupo: 2,
        grid: true,
        form: true
    },
    /*********************************************************************************************/

    {
        config:{
            name: 'numero_tarjeta',
            fieldLabel: 'N° Tarjeta',
            allowBlank: true,
            width:150,
            maxLength:20,
            minLength:15

        },
            type:'TextField',
            id_grupo:2,
            form:true
    },
    {
        config:{
            name: 'mco',
            fieldLabel: 'MCO',
            allowBlank: false,
            width:150,
            gwidth: 150,
            minLength:15,
            maxLength:20
        },
        type:'TextField',
        id_grupo:2,
        grid:true,
        form:true
    },
    {
        config:{
            name: 'codigo_tarjeta',
            fieldLabel: 'Codigo de Autorización',
            allowBlank: false,
            width:150,
            minLength:6,
            maxLength:6,
            style:'text-transform:uppercase;',
            maskRe: /[a-zA-Z0-9]+/i,
            regex: /[a-zA-Z0-9]+/i

        },
            type:'TextField',
            id_grupo:2,
            form:true
    },
    {
			config: {
				name: 'id_auxiliar',
				fieldLabel: 'Cuenta Corriente',
				allowBlank: true,
        width:150,
				emptyText: 'Cuenta Corriente...',
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
					baseParams: {par_filtro: 'auxcta.codigo_auxiliar#auxcta.nombre_auxiliar',corriente:'si'}
				}),
				valueField: 'id_auxiliar',
				displayField: 'nombre_auxiliar',
				gdisplayField: 'codigo_auxiliar',
				hiddenName: 'id_auxiliar',
				tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre_auxiliar}</p><p>Codigo:{codigo_auxiliar}</p> </div></tpl>',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				gwidth: 150,
				listWidth:350,
				resizable:true,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['nombre_auxiliar']);
				}
			},
			type: 'ComboBox',
			id_grupo: 2,
			grid: true,
			form: true
		},
    {
			config: {
				name: 'id_auxiliar_anticipo',
				fieldLabel: 'Cuenta Corriente',
				allowBlank: true,
        width:150,
				emptyText: 'Cuenta Corriente...',
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
					baseParams: {par_filtro: 'auxcta.codigo_auxiliar#auxcta.nombre_auxiliar',corriente:'si'}
				}),
				valueField: 'id_auxiliar',
				displayField: 'nombre_auxiliar',
				gdisplayField: 'codigo_auxiliar',
				hiddenName: 'id_auxiliar',
				tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre_auxiliar}</p><p>Codigo:{codigo_auxiliar}</p> </div></tpl>',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				gwidth: 150,
				listWidth:350,
				resizable:true,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['nombre_auxiliar']);
				}
			},
			type: 'ComboBox',
			id_grupo: 13,
			grid: true,
			form: true
		},
    {
        config:{
            name: 'id_deposito',
            fieldLabel: 'id_deposito',
            allowBlank: true,
            hidden:true,
            width:150,
        },
            type:'TextField',
            id_grupo:13,
            form:true,
            //valorInicial:'0'
    },
    {
        config:{
            name: 'nro_deposito',
            fieldLabel: 'Nro Depósito',
            allowBlank: true,
            hidden:true,
            width:150,
            maskRe: /[a-zA-Z0-9]+/i,
            regex: /[a-zA-Z0-9]+/i
        },
            type:'TextField',
            id_grupo:13,
            form:true,
            //valorInicial:'0'
    },
    {
        config:{
            name: 'fecha_deposito',
            fieldLabel: 'Fecha Deposito',
            allowBlank: true,
            hidden:true,
            disabled:true,
            width:150,
            format: 'd/m/Y',
            renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
        },
            type:'DateField',
            id_grupo:13,
            form:true,
            //valorInicial:'0'
    },
    {
        config:{
            name: 'monto_deposito',
            fieldLabel: 'Monto Depósito',
            allowBlank: true,
            hidden:true,
            enableKeyEvents: true,
            width:150,
            maxLength:20,
            allowNegative:false,
            value:0
        },
            type:'NumberField',
            id_grupo:13,
            form:true,
            valorInicial:'0'
    },
    {
        config:{
            name: 'id_cuenta_bancaria',
            fieldLabel: 'id_cuenta_bancaria',
            allowBlank: true,
            hidden:true,
            width:150,
            maxLength:20,
            allowNegative:false
        },
            type:'NumberField',
            id_grupo:13,
            form:true
    },
    {
        config:{
            name: 'cuenta_bancaria',
            fieldLabel: 'Nro Cuenta',
            allowBlank: true,
            hidden:true,
            width:150,
            maxLength:20,
            allowNegative:false
        },
            type:'NumberField',
            id_grupo:13,
            form:true
    },
    {
        config:{
            name: 'monto_forma_pago',
            fieldLabel: 'Importe Recibido',
            allowBlank: false,
            width:150,
            maxLength:20,
            enableKeyEvents: true,
            allowNegative:false,
        },
            type:'NumberField',
            id_grupo:2,
            form:true,
    },
    {
        config:{
            name: 'tipo_tarjeta',
            fieldLabel: 'Tipo Tarjeta',
            allowBlank: false,
            width:150,
            //maxLength:20,
            //allowNegative:false,
            //value:0
        },
            type:'TextField',
            id_grupo:2,
            form:true,
            //valorInicial:'0'
    },
        // {
        //     config:{
        //         name: 'valor_bruto',
        //         fieldLabel: 'Valor Bruto',
        //         allowBlank: false,
        //         width:150,
        //         readOnly: true,
        //         maxLength: 20
        //
        //     },
        //         type:'NumberField',
        //         valorInicial: 0,
        //         id_grupo: 3,
        //         form: false
        // },
        // {
        //     config:{
        //         name: 'transporte_fob',
        //         fieldLabel: 'Trasporte FOB',
        //         allowBlank: false,
        //         width: 150,
        //         maxLength: 20
        //
        //     },
        //         type:'NumberField',
        //         id_grupo: 3,
        //         valorInicial: 0,
        //         form: false
        // },
        // {
        //     config:{
        //         name: 'seguros_fob',
        //         fieldLabel: 'Seguros FOB',
        //         allowBlank: false,
        //         width: 150,
        //         maxLength:20
        //
        //     },
        //         type:'NumberField',
        //         id_grupo: 3,
        //         valorInicial: 0,
        //         form: false
        // },
        // {
        //     config:{
        //         name: 'otros_fob',
        //         fieldLabel: 'Otros FOB',
        //         allowBlank: false,
        //         width: 150,
        //         maxLength:20
        //
        //     },
        //         type:'NumberField',
        //         id_grupo: 3,
        //         valorInicial: 0,
        //         form: false
        // },
        // {
        //     config:{
        //         name: 'total_fob',
        //         fieldLabel: 'TOTAL F.O.B',
        //         allowBlank: false,
        //         readOnly: true,
        //         width: 150,
        //         maxLength:20
        //
        //     },
        //         type:'NumberField',
        //         id_grupo: 3,
        //
        //         valorInicial: 0,
        //         form: false
        // },
        // {
        //     config:{
        //         name: 'transporte_cif',
        //         fieldLabel: 'Trasporte CIF',
        //         allowBlank: false,
        //         width: 150,
        //         maxLength: 20
        //
        //     },
        //         type:'NumberField',
        //         id_grupo: 4,
        //         valorInicial: 0,
        //         form: false
        // },
        // {
        //     config:{
        //         name: 'seguros_cif',
        //         fieldLabel: 'Seguros CIF',
        //         allowBlank: false,
        //         width: 150,
        //         maxLength:20
        //
        //     },
        //         type:'NumberField',
        //         id_grupo: 4,
        //         valorInicial: 0,
        //         form: false
        // },
        // {
        //     config:{
        //         name: 'otros_cif',
        //         fieldLabel: 'Otros CIF',
        //         allowBlank: false,
        //         width: 150,
        //         maxLength:20
        //
        //     },
        //         type:'NumberField',
        //         id_grupo: 4,
        //         valorInicial: 0,
        //         form: false
        // },
        // {
        //     config:{
        //         name: 'total_cif',
        //         fieldLabel: 'TOTAL CIF',
        //         allowBlank: false,
        //         readOnly: true,
        //         width: 150,
        //         maxLength:20
        //
        //     },
        //         type:'NumberField',
        //         id_grupo: 4,
        //         valorInicial: 0,
        //         form: false
        // },
        // {
        //   config:{
        //     name: 'moneda_tarjeta_2',
        //     fieldLabel: 'Moneda',
        //     allowBlank: true,
        //     width:150,
        //     disabled:false,
        //     readOnly:true,
        //     style: {
        //       background: '#EFFFD6',
        //       color: 'red',
        //       fontWeight:'bold',
        //     //  border:'2px solid blue'
        //     },
        //     gwidth: 100
        //   },
        //     type:'TextField',
        //     id_grupo: 10,
        //     form:true
        // },
        {
            config: {
                name: 'id_moneda_2',
                fieldLabel: 'Moneda',
                allowBlank: false,
                width:150,
                listWidth:250,
                resizable:true,
                disabled:false,
                style: {
                     background: '#EFFFD6',
                     color: 'red',
                     fontWeight:'bold'
                   },
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
                    baseParams: {filtrar: 'si'}
                }),
                valueField: 'id_moneda',
                gdisplayField : 'codigo_internacional',
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
            id_grupo: 10,
            form: true
        },
        {
            config: {
                name: 'id_instancia_pago_2',
                fieldLabel: 'Instancia de pago',
                allowBlank: false,
                width:150,
                emptyText: 'Instancia de pago...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_obingresos/control/InstanciaPago/listarInstanciaPago',
                    id: 'id_instancia_pago',
                    root: 'datos',
                    sortInfo: {
                        field: 'nombre',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_instancia_pago', 'nombre', 'codigo_forma_pago'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'insp.nombre#insp.codigo_forma_pago'}
                }),
                valueField: 'id_instancia_pago',
                displayField: 'nombre',
                gdisplayField: 'codigo_forma_pago',
                hiddenName: 'id_instancia_pago',
                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p><b><p>Codigo:<font color="green">{codigo_forma_pago}</font></b></p></div></tpl>',
                forceSelection: true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 15,
                queryDelay: 1000,
                gwidth: 150,
                listWidth:250,
                resizable:true,
                minChars: 2,
                disabled:false,
                renderer : function(value, p, record) {
                    return String.format('{0}', record.data['codigo_forma_pago']);
                }
            },
            type: 'ComboBox',
            id_grupo: 10,
            grid: true,
            form: true
        },
        // {
        //     config: {
        //         name: 'id_forma_pago_2',
        //         fieldLabel: 'Forma de Pago',
        //         allowBlank: true,
        //         disabled:true,
        //         emptyText: 'Forma de Pago...',
        //         store: new Ext.data.JsonStore({
        //             url: '../../sis_ventas_facturacion/control/FormaPago/listarFormaPago',
        //             id: 'id_forma_pago',
        //             root: 'datos',
        //             sortInfo: {
        //                 field: 'nombre',
        //                 direction: 'ASC'
        //             },
        //             totalProperty: 'total',
        //             fields: ['id_forma_pago', 'nombre', 'desc_moneda','registrar_tarjeta','registrar_cc','codigo'],
        //             remoteSort: true,
        //             baseParams: {par_filtro: 'forpa.nombre#forpa.codigo#mon.codigo_internacional',sw_tipo_venta:'boletos'}
        //         }),
        //         valueField: 'id_forma_pago',
        //         displayField: 'nombre',
        //         gdisplayField: 'forma_pago',
        //         hiddenName: 'id_forma_pago',
        //         width:150,
        //         tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p><b><p>Codigo:<font color="green">{codigo}</font></b></p><p><b>Moneda:<font color="red">{desc_moneda}</font></b></p> </div></tpl>',
        //         forceSelection: true,
        //         typeAhead: false,
        //         triggerAction: 'all',
        //         lazyRender: true,
        //         mode: 'remote',
        //         pageSize: 15,
        //         queryDelay: 1000,
        //         gwidth: 150,
        //         listWidth:350,
        //         resizable:true,
        //         minChars: 2,
        //         //disabled:false,
        //         renderer : function(value, p, record) {
        //             return String.format('{0}', record.data['forma_pago2']);
        //         }
        //     },
        //     type: 'ComboBox',
        //     id_grupo: 10,
        //     grid: true,
        //     form: true
        // },

        {
    			config: {
    				name: 'id_auxiliar_2',
    				fieldLabel: 'Cuenta Corriente',
    				allowBlank: true,
            width:150,
    				emptyText: 'Cuenta Corriente...',
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
    					baseParams: {par_filtro: 'auxcta.codigo_auxiliar#auxcta.nombre_auxiliar',corriente:'si'}
    				}),
    				valueField: 'id_auxiliar',
    				displayField: 'nombre_auxiliar',
    				gdisplayField: 'codigo_auxiliar',
    				hiddenName: 'id_auxiliar',
    				tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre_auxiliar}</p><p>Codigo:{codigo_auxiliar}</p> </div></tpl>',
    				forceSelection: true,
    				typeAhead: false,
    				triggerAction: 'all',
    				lazyRender: true,
    				mode: 'remote',
    				pageSize: 15,
    				queryDelay: 1000,
    				gwidth: 150,
    				listWidth:350,
    				resizable:true,
    				minChars: 2,
    				renderer : function(value, p, record) {
    					return String.format('{0}', record.data['nombre_auxiliar']);
    				}
    			},
    			type: 'ComboBox',
    			id_grupo: 10,
    			grid: true,
    			form: true
    		},

        {
            config:{
                name: 'numero_tarjeta_2',
                fieldLabel: 'N° Tarjeta',
                allowBlank: true,
                //disabled:true,
                width:150,
                gwidth: 150,
                maxLength:20,
                minLength:15
            },
            type:'TextField',
            id_grupo:10,
            grid:false,
            form:true
        },
        // ///nuevo
        {
            config:{
                name: 'mco_2',
                fieldLabel: 'MCO 2',
                allowBlank: true,
                width:150,
                gwidth: 150,
                //disabled:true,
                minLength:15,
                maxLength:20
            },
            type:'TextField',
            id_grupo:10,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'codigo_tarjeta_2',
                fieldLabel: 'Codigo de Autorización',
                allowBlank: true,
                width:150,
                //disabled:true,
                minLength:6,
                maxLength:6,
                style:'text-transform:uppercase;',
                maskRe: /[a-zA-Z0-9]+/i,
                regex: /[a-zA-Z0-9]+/i

            },
            type:'TextField',
            id_grupo:10,
            grid:false,
            form:true
        },
        {
            config:{
                name: 'monto_forma_pago_2',
                fieldLabel: 'Importe Recibido',
                allowBlank:true,
                width:150,
                allowDecimals:true,
                decimalPrecision:2,
                enableKeyEvents: true,
                allowNegative : false,
                disabled:true,
                gwidth: 125,
                style: 'background-color: #f2f23c;  background-image: none;'
            },
            type:'NumberField',
            id_grupo:10,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'tipo_tarjeta_2',
                fieldLabel: 'Tipo Tarjeta',
                allowBlank: false,
                width:150,
                //maxLength:20,
                //allowNegative:false,
                //value:0
            },
                type:'TextField',
                id_grupo:10,
                form:true,
                //valorInicial:'0'
        },
        // {
        //         config:{
        //             name: 'tipo_tarjeta_2',
        //             fieldLabel: 'Tipo Tarjeta',
        //             allowBlank: true,
        //             width:150,
        //             emptyText:'tipo...',
        //             triggerAction: 'all',
        //             lazyRender:true,
        //             mode: 'local',
        //             displayField: 'text',
        //             valueField: 'value',
        //             store:new Ext.data.SimpleStore({
    		// 			data : [['VI', 'VISA'], ['AX', 'AMERICAN EXPRESS'],
    		// 					['DC', 'DINERS CLUB'],['CA', 'MASTER CARD'],
    		// 					['RE', 'RED ENLACE']],
    		// 			id : 'value',
    		// 			fields : ['value', 'text']
    		// 		})
        //         },
        //         type:'ComboBox',
        //         id_grupo:10,
        //         form:true
        //     },
        // //modifcado
        {
            config:{
                name: 'cambio',
                fieldLabel: '',
                allowBlank:true,
                allowDecimals:true,
                decimalPrecision:2,
                allowNegative : false,
                readOnly:true,
                gwidth: 110,
                disabled:false,
                style: {
                  backgroundColor: '#EFFFD6',
                  backgroundImage:'none',
                  marginLeft:'-120px',
                  height:'100px',
                  width: '200px',
                  //color:'#2A00FE',
                  textAlign:'center',
                  fontSize:'40px',
                  position:'center',
                  fontWeight:'bold',
                }
            },
            type:'NumberField',
            id_grupo:11,
            grid:false,
            form:true
        },
        {
            config:{
                name: 'cambio_moneda_extranjera',
                fieldLabel: '',
                allowBlank:true,
                //anchor: '20%',
                allowDecimals:true,
                decimalPrecision:2,
                allowNegative : false,
                readOnly:true,
                disabled:false,
                gwidth: 110,
                style: {
                  backgroundColor: '#EFFFD6',
                  backgroundImage:'none',
                  //border:'2px solid red',
                  marginLeft:'-120px',
                  height:'100px',
                  color:'blue',
                  width: '200px',
                  fontSize:'40px',
                  textAlign:'center',
                  position:'center',
                  fontWeight:'bold'
                }
            },
            type:'NumberField',
            id_grupo:12,
            grid:false,
            form:true
        },



    ],
    title: 'Formulario Venta',
    onEdit:function(){
    	this.accionFormulario = 'EDIT';
    	this.loadForm(this.data.datos_originales);
        //load detalle de conceptos
        this.mestore.baseParams.id_venta = this.Cmp.id_venta.getValue();
        this.mestore.load();
        this.Cmp.id_forma_pago.reset();
        this.crearStoreFormaPago();

    },
    onNew: function(){
    	this.accionFormulario = 'NEW';
	},
    devolverCambio: function(){
      if(this.Cmp.id_moneda.getValue() == 2 && this.id_moneda_recibo_cambio != 2){
        this.Cmp.cambio.setValue(((this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)-this.suma_total));
        this.Cmp.cambio_moneda_extranjera.setValue(((this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)-this.suma_total)/this.tipo_cambio);
      }

      else if (this.Cmp.id_moneda.getValue() != 2 && this.id_moneda_recibo_cambio == 2){
        this.Cmp.cambio.setValue((this.Cmp.monto_forma_pago.getValue()-(this.suma_total*this.tipo_cambio)));
        this.Cmp.cambio_moneda_extranjera.setValue(((this.Cmp.monto_forma_pago.getValue()/this.tipo_cambio)-this.suma_total));
      }

      else if (this.Cmp.id_moneda.getValue() == 2 && this.id_moneda_recibo_cambio == 2) {
        this.Cmp.cambio.setValue((this.Cmp.monto_forma_pago.getValue()-this.suma_total)*this.tipo_cambio);
        this.Cmp.cambio_moneda_extranjera.setValue((this.Cmp.monto_forma_pago.getValue()-this.suma_total));
      }

      else if (this.Cmp.id_moneda.getValue() != 2 && this.id_moneda_recibo_cambio != 2) {
        this.Cmp.cambio.setValue(((this.Cmp.monto_forma_pago.getValue())-this.suma_total));
        this.Cmp.cambio_moneda_extranjera.setValue((this.Cmp.monto_forma_pago.getValue()-this.suma_total)/this.tipo_cambio);
      }

      if ( (this.Cmp.id_moneda.getValue() == 2 && this.id_moneda_recibo_cambio != 2) && (this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio) < this.suma_total) {

        /**********************************Cambiamos el Style *****************************************/
        this.Cmp.cambio_moneda_extranjera.label.dom.control.style.color = "red";
        this.Cmp.cambio_moneda_extranjera.label.dom.control.style.background = "#FFE4E4";
        this.Cmp.cambio.label.dom.control.style.color = "red";
        this.Cmp.cambio.label.dom.control.style.background = "#FFE4E4";
        /**********************************Cambiamos el Style *****************************************/
        //this.Cmp.id_forma_pago_2.enable();
        Ext.getCmp('segunda_forma_pago').show();
        this.Cmp.id_moneda_2.enable();
        this.Cmp.id_instancia_pago_2.enable();
        this.Cmp.monto_forma_pago_2.enable();
        this.Cmp.monto_forma_pago_2.setValue((this.suma_total-(this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)));


      }else if ((this.Cmp.id_moneda.getValue() != 2 && this.id_moneda_recibo_cambio == 2) && this.Cmp.monto_forma_pago.getValue() < (this.suma_total*this.tipo_cambio)) {


        /**********************************Cambiamos el Style *****************************************/
        this.Cmp.cambio_moneda_extranjera.label.dom.control.style.color = "red";
        this.Cmp.cambio_moneda_extranjera.label.dom.control.style.background = "#FFE4E4";
        this.Cmp.cambio.label.dom.control.style.color = "red";
        this.Cmp.cambio.label.dom.control.style.background = "#FFE4E4";
        /**********************************Cambiamos el Style *****************************************/
        Ext.getCmp('segunda_forma_pago').show();
        //this.Cmp.id_forma_pago_2.enable();
        this.Cmp.id_moneda_2.enable();
        this.Cmp.id_instancia_pago_2.enable();
        this.Cmp.monto_forma_pago_2.enable();
        this.Cmp.monto_forma_pago_2.setValue(((this.suma_total*this.tipo_cambio)-this.Cmp.monto_forma_pago.getValue()));

      } else if ((this.Cmp.id_moneda.getValue() == 2 && this.id_moneda_recibo_cambio == 2) && this.Cmp.monto_forma_pago.getValue() < this.suma_total) {


        /**********************************Cambiamos el Style *****************************************/
        this.Cmp.cambio_moneda_extranjera.label.dom.control.style.color = "red";
        this.Cmp.cambio_moneda_extranjera.label.dom.control.style.background = "#FFE4E4";
        this.Cmp.cambio.label.dom.control.style.color = "red";
        this.Cmp.cambio.label.dom.control.style.background = "#FFE4E4";
        /**********************************Cambiamos el Style *****************************************/
        Ext.getCmp('segunda_forma_pago').show();
        //this.Cmp.id_forma_pago_2.enable();
        this.Cmp.id_moneda_2.enable();
        this.Cmp.id_instancia_pago_2.enable();
        this.Cmp.monto_forma_pago_2.enable();
        this.Cmp.monto_forma_pago_2.setValue(this.suma_total-this.Cmp.monto_forma_pago.getValue());
      } else if ((this.Cmp.id_moneda.getValue() != 2 && this.id_moneda_recibo_cambio != 2) && this.Cmp.monto_forma_pago.getValue() < this.suma_total) {

        /**********************************Cambiamos el Style *****************************************/
        this.Cmp.cambio_moneda_extranjera.label.dom.control.style.color = "red";
        this.Cmp.cambio_moneda_extranjera.label.dom.control.style.background = "#FFE4E4";
        this.Cmp.cambio.label.dom.control.style.color = "red";
        this.Cmp.cambio.label.dom.control.style.background = "#FFE4E4";
        /**********************************Cambiamos el Style *****************************************/
        Ext.getCmp('segunda_forma_pago').show();
        //this.Cmp.id_forma_pago_2.enable();
        this.Cmp.id_moneda_2.enable();
        this.Cmp.id_instancia_pago_2.enable();
        this.Cmp.monto_forma_pago_2.enable();
        this.Cmp.monto_forma_pago_2.setValue(this.suma_total-this.Cmp.monto_forma_pago.getValue());
      }

      else{
        //this.Cmp.id_forma_pago_2.disable();
        Ext.getCmp('segunda_forma_pago').hide();
        this.Cmp.id_moneda_2.disable();
        this.Cmp.id_instancia_pago_2.disable();
        this.Cmp.monto_forma_pago_2.disable();
        this.Cmp.monto_forma_pago_2.reset();
        //this.Cmp.id_forma_pago_2.reset();
        this.Cmp.id_moneda_2.reset();
        this.Cmp.id_instancia_pago_2.reset();
        //this.Cmp.moneda_tarjeta_2.reset();
        this.Cmp.cambio_moneda_extranjera.label.dom.control.style.color = "blue";
        this.Cmp.cambio_moneda_extranjera.label.dom.control.style.background = "#EFFFD6";
        this.Cmp.cambio.label.dom.control.style.color = "";
        this.Cmp.cambio.label.dom.control.style.background = "#EFFFD6";
        this.ocultarComponente(this.Cmp.mco_2);
        this.ocultarComponente(this.Cmp.numero_tarjeta_2);
        this.ocultarComponente(this.Cmp.codigo_tarjeta_2);
        this.ocultarComponente(this.Cmp.id_auxiliar_2);
        this.ocultarComponente(this.Cmp.tipo_tarjeta_2);
      }
      /***************************************************************************+*/
    },

    cargarInstanciaPago: function() {
      /****************************Aumnetando la instancia de pago********************************/
      this.Cmp.id_instancia_pago.on('select',function(c,r,i) {
        var codigo_forma_pago = r.data.codigo_forma_pago.substr(0,2);
        this.arrayBotones[0].scope.form.buttons[0].setDisabled(false);
        this.Cmp.tipo_tarjeta.setValue(r.data.nombre);
        if (codigo_forma_pago == 'CC') {
          Ext.getCmp('segunda_forma_pago').show();
          this.mostrarComponente(this.Cmp.codigo_tarjeta);
          this.mostrarComponente(this.Cmp.tipo_tarjeta);
          this.mostrarComponente(this.Cmp.numero_tarjeta);
          this.ocultarComponente(this.Cmp.id_auxiliar);
          this.Cmp.id_auxiliar_anticipo.allowBlank=true;
          this.Cmp.id_auxiliar_anticipo.reset();
          this.ocultarComponente(this.Cmp.mco);
          this.Cmp.tipo_tarjeta.setValue(r.data.nombre);
          this.Cmp.codigo_tarjeta.allowBlank = false;
          this.Cmp.tipo_tarjeta.allowBlank = false;
          this.Cmp.mco.allowBlank = true;

          if (this.instanciasPagoAnticipo == 'si') {
            this.mostrarComponente(this.Cmp.id_auxiliar_anticipo);
            Ext.getCmp('datos_deposito').show();
          }else{
            Ext.getCmp('datos_deposito').hide();
            this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
            this.Cmp.id_auxiliar_anticipo.allowBlank=true;
            this.Cmp.id_auxiliar_anticipo.reset();
          }

          this.ocultarComponente(this.Cmp.nro_deposito);
          this.ocultarComponente(this.Cmp.fecha_deposito);
          this.ocultarComponente(this.Cmp.monto_deposito);
          this.ocultarComponente(this.Cmp.cuenta_bancaria);
          this.Cmp.nro_deposito.allowBlank=true;
          this.Cmp.fecha_deposito.allowBlank=true;
          this.Cmp.monto_deposito.allowBlank=true;
          this.Cmp.cuenta_bancaria.allowBlank=true;
          this.Cmp.nro_deposito.reset();
          this.Cmp.fecha_deposito.reset();
          this.Cmp.monto_deposito.reset();
          this.Cmp.cuenta_bancaria.reset();

        } else if (codigo_forma_pago == 'MC') {
          Ext.getCmp('segunda_forma_pago').show();
          this.mostrarComponente(this.Cmp.mco);
          this.Cmp.numero_tarjeta.allowBlank = true;
          this.Cmp.codigo_tarjeta.allowBlank = true;
          this.Cmp.tipo_tarjeta.allowBlank = true;
          this.Cmp.mco.allowBlank = false;
          this.ocultarComponente(this.Cmp.codigo_tarjeta);
          this.ocultarComponente(this.Cmp.tipo_tarjeta);
          this.ocultarComponente(this.Cmp.id_auxiliar);
          this.Cmp.id_auxiliar.allowBlank=true;
          this.Cmp.id_auxiliar.reset();
          this.Cmp.codigo_tarjeta.reset();
          this.Cmp.tipo_tarjeta.reset();
          //this.Cmp.id_auxiliar.reset();
          this.Cmp.numero_tarjeta.reset();

          if (this.instanciasPagoAnticipo == 'si') {
            this.mostrarComponente(this.Cmp.id_auxiliar_anticipo);
            Ext.getCmp('datos_deposito').show();
          }else{
            Ext.getCmp('datos_deposito').hide();
            this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
            this.Cmp.id_auxiliar_anticipo.allowBlank=true;
            this.Cmp.id_auxiliar_anticipo.reset();
          }

          this.ocultarComponente(this.Cmp.nro_deposito);
          this.ocultarComponente(this.Cmp.fecha_deposito);
          this.ocultarComponente(this.Cmp.monto_deposito);
          this.ocultarComponente(this.Cmp.cuenta_bancaria);
          this.Cmp.nro_deposito.allowBlank=true;
          this.Cmp.fecha_deposito.allowBlank=true;
          this.Cmp.monto_deposito.allowBlank=true;
          this.Cmp.cuenta_bancaria.allowBlank=true;
          this.Cmp.nro_deposito.reset();
          this.Cmp.fecha_deposito.reset();
          this.Cmp.monto_deposito.reset();
          this.Cmp.cuenta_bancaria.reset();


        } else if (codigo_forma_pago == 'CU') {
          Ext.getCmp('segunda_forma_pago').show();
          this.mostrarComponente(this.Cmp.id_auxiliar);
          this.Cmp.numero_tarjeta.allowBlank = true;
          this.Cmp.codigo_tarjeta.allowBlank = true;
          this.Cmp.tipo_tarjeta.allowBlank = true;
          this.Cmp.mco.allowBlank = true;
          this.Cmp.id_auxiliar.allowBlank = false;
          this.ocultarComponente(this.Cmp.codigo_tarjeta);
          this.ocultarComponente(this.Cmp.tipo_tarjeta);
          this.ocultarComponente(this.Cmp.numero_tarjeta);
          this.Cmp.codigo_tarjeta.reset();
          this.Cmp.tipo_tarjeta.reset();
          this.Cmp.id_auxiliar.reset();
          this.Cmp.mco.reset();
          this.Cmp.numero_tarjeta.reset();

          if (this.instanciasPagoAnticipo == 'si') {
            this.mostrarComponente(this.Cmp.id_auxiliar_anticipo);
            Ext.getCmp('datos_deposito').show();
          }else{
            Ext.getCmp('datos_deposito').hide();
            this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
            this.Cmp.id_auxiliar_anticipo.allowBlank=true;
            this.Cmp.id_auxiliar_anticipo.reset();
          }

          this.ocultarComponente(this.Cmp.nro_deposito);
          this.ocultarComponente(this.Cmp.fecha_deposito);
          this.ocultarComponente(this.Cmp.monto_deposito);
          this.ocultarComponente(this.Cmp.cuenta_bancaria);
          this.Cmp.nro_deposito.allowBlank=true;
          this.Cmp.fecha_deposito.allowBlank=true;
          this.Cmp.monto_deposito.allowBlank=true;
          this.Cmp.cuenta_bancaria.allowBlank=true;
          this.Cmp.nro_deposito.reset();
          this.Cmp.fecha_deposito.reset();
          this.Cmp.monto_deposito.reset();
          this.Cmp.cuenta_bancaria.reset();


        }else if (codigo_forma_pago == 'CA') {
          Ext.getCmp('segunda_forma_pago').show();
          this.Cmp.numero_tarjeta.allowBlank = true;
          this.Cmp.codigo_tarjeta.allowBlank = true;
          this.Cmp.tipo_tarjeta.allowBlank = true;
          //this.Cmp.id_auxiliar.allowBlank = true;
          this.Cmp.mco.allowBlank = true;
          this.ocultarComponente(this.Cmp.codigo_tarjeta);
          this.ocultarComponente(this.Cmp.tipo_tarjeta);
          this.ocultarComponente(this.Cmp.numero_tarjeta);
          this.ocultarComponente(this.Cmp.id_auxiliar);
          this.Cmp.id_auxiliar.allowBlank=true;
          this.Cmp.id_auxiliar.reset();
          this.ocultarComponente(this.Cmp.mco);
          this.Cmp.codigo_tarjeta.reset();
          this.Cmp.tipo_tarjeta.reset();
          //this.Cmp.id_auxiliar.reset();
          this.Cmp.mco.reset();
          this.Cmp.numero_tarjeta.reset();

          if (this.instanciasPagoAnticipo == 'si') {
            this.mostrarComponente(this.Cmp.id_auxiliar_anticipo);
            Ext.getCmp('datos_deposito').show();
          }else{
            Ext.getCmp('datos_deposito').hide();
            this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
            this.Cmp.id_auxiliar_anticipo.allowBlank=true;
            this.Cmp.id_auxiliar_anticipo.reset();
          }

          this.ocultarComponente(this.Cmp.nro_deposito);
          this.ocultarComponente(this.Cmp.fecha_deposito);
          this.ocultarComponente(this.Cmp.monto_deposito);
          this.ocultarComponente(this.Cmp.cuenta_bancaria);
          this.Cmp.nro_deposito.allowBlank=true;
          this.Cmp.fecha_deposito.allowBlank=true;
          this.Cmp.monto_deposito.allowBlank=true;
          this.Cmp.cuenta_bancaria.allowBlank=true;
          this.Cmp.nro_deposito.reset();
          this.Cmp.fecha_deposito.reset();
          this.Cmp.monto_deposito.reset();
          this.Cmp.cuenta_bancaria.reset();


        }
        /*Aumentando esta parte para la forma de pago*/
        else if (codigo_forma_pago == 'DE') {
          Ext.getCmp('datos_deposito').show();
          if (this.instanciasPagoAnticipo == 'si') {
            this.mostrarComponente(this.Cmp.id_auxiliar_anticipo);
            this.Cmp.id_auxiliar_anticipo.allowBlank = false;
          }else{
            this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
            this.Cmp.id_auxiliar_anticipo.allowBlank = false;
            this.Cmp.id_auxiliar_anticipo.reset();
          }
          this.ocultarComponente(this.Cmp.mco);
          this.ocultarComponente(this.Cmp.codigo_tarjeta);
          this.ocultarComponente(this.Cmp.tipo_tarjeta);
          this.ocultarComponente(this.Cmp.numero_tarjeta);
          this.Cmp.mco.reset();
          this.Cmp.codigo_tarjeta.reset();
          this.Cmp.tipo_tarjeta.reset();
          this.Cmp.numero_tarjeta.reset();

          /**Aqui ponemos las condiciones**/
          Ext.getCmp('segunda_forma_pago').hide();
          this.mostrarComponente(this.Cmp.nro_deposito);
          this.mostrarComponente(this.Cmp.fecha_deposito);
          this.mostrarComponente(this.Cmp.monto_deposito);
          this.mostrarComponente(this.Cmp.cuenta_bancaria);
          this.Cmp.nro_deposito.allowBlank = false;
          this.Cmp.fecha_deposito.allowBlank = false;
          this.Cmp.fecha_deposito.setValue(this.fecha_actual_RO);
          this.Cmp.monto_deposito.allowBlank = false;
          this.Cmp.cuenta_bancaria.allowBlank = false;
          this.Cmp.cuenta_bancaria.setDisabled(true);
          this.Cmp.monto_deposito.setValue(this.suma_total);
          this.Cmp.monto_forma_pago.setValue(this.suma_total);

          this.devolverCambio();

          this.Cmp.monto_deposito.on('keyup', function (cmp, e) {
             this.Cmp.monto_forma_pago.setValue(this.Cmp.monto_deposito.getValue());
             /*Aqui ponemos estas condiciones para que se actualice el cambio*/
             this.obtenersuma();
             this.devolverCambio();
           }, this);
          /*******************************/
         /***Aqui validamos para ver si existe la boleta***/
         Ext.Ajax.request({
    				url:'../../sis_ventas_facturacion/control/VentaFacturacion/ObtenerCuentaBancaria',
    				params:{
    					id_punto_venta:this.Cmp.id_punto_venta.getValue(),
    					id_sucursal:this.Cmp.id_sucursal.getValue(),
              id_moneda:this.Cmp.id_moneda.getValue()
    				},
    				success: function(resp){
    						var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
    						   this.Cmp.cuenta_bancaria.setValue(reg.ROOT.datos.nro_cuenta);
                   this.Cmp.id_cuenta_bancaria.setValue(reg.ROOT.datos.id_cuenta_bancaria);
    				},
    				failure: this.conexionFailure,
    				timeout:this.timeout,
    				scope:this
    		});
         /*************************************************/
         /*Aqui volvemos a llamar a la funcion de acuerdo a la moneda seleccionada para la cuenta*/
          this.Cmp.id_moneda.on('select',function(c,r,i) {
            if (this.Cmp.id_moneda.getValue() == 2) {
              this.Cmp.monto_deposito.setValue((this.suma_total/this.tipo_cambio));
              this.Cmp.monto_forma_pago.setValue((this.suma_total/this.tipo_cambio));
            } else {
              this.Cmp.monto_deposito.setValue((this.suma_total));
              this.Cmp.monto_forma_pago.setValue((this.suma_total));
            }
            Ext.Ajax.request({
     					url:'../../sis_ventas_facturacion/control/VentaFacturacion/ObtenerCuentaBancaria',
     					params:{
     						id_punto_venta:this.Cmp.id_punto_venta.getValue(),
     						id_sucursal:this.Cmp.id_sucursal.getValue(),
                 id_moneda:this.Cmp.id_moneda.getValue()
     					},
     					success: function(resp){
     							var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                      this.Cmp.cuenta_bancaria.setValue(reg.ROOT.datos.nro_cuenta);
     							   this.Cmp.id_cuenta_bancaria.setValue(reg.ROOT.datos.id_cuenta_bancaria);

     					},
     					failure: this.conexionFailure,
     					timeout:this.timeout,
     					scope:this
     			});

          if(this.Cmp.nro_deposito.getValue() != '' && this.Cmp.nro_deposito.getValue() != null) {
                  Ext.Ajax.request({
          						url:'../../sis_ventas_facturacion/control/VentaFacturacion/verificarDeposito',
          						params:{
                       nro_deposito:this.Cmp.nro_deposito.getValue(),
                       id_moneda:this.Cmp.id_moneda.getValue(),
          						 fecha:this.Cmp.fecha_deposito.getValue(),
          						},
          						success: function(resp){
          								var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                            if (reg.ROOT.datos.cantidad_deposito > 0) {
                              /*Bloqueamos el boton para que no genere si el deposito*/
                               this.arrayBotones[0].scope.form.buttons[0].setDisabled(true);
                               Ext.Msg.show({
                                   title: 'Alerta',
                                   msg: '<p>El número de depósito <b>'+reg.ROOT.datos.nro_deposito+'</b>. ya se encuentra registrado favor contactarse con personal de ventas para su validación e ingrese nuevamente el nro de depósito</p>',
                                   buttons: Ext.Msg.OK,
                                   width: 512,
                                   icon: Ext.Msg.INFO
                               });
                               this.Cmp.nro_deposito.reset();
                             /********************************************************/
                               // this.Cmp.fecha_deposito.setValue(reg.ROOT.datos.fecha_deposito);
                               // this.Cmp.nro_deposito.setValue(reg.ROOT.datos.nro_deposito);
                               // this.Cmp.id_deposito.setValue(reg.ROOT.datos.id_deposito);
                               // this.Cmp.monto_deposito.setValue(reg.ROOT.datos.monto_deposito);
                               // this.Cmp.monto_forma_pago.setValue(reg.ROOT.datos.monto_deposito);
                               //this.Cmp.id_deposito.setValue(reg.ROOT.datos.id_deposito);
                            }else {
                              this.arrayBotones[0].scope.form.buttons[0].setDisabled(false);
                              //this.Cmp.fecha_deposito.reset();
                              //this.Cmp.nro_deposito.reset();
                              //this.Cmp.monto_deposito.setValue(0);
                              //this.Cmp.monto_forma_pago.setValue(0);
                            }
                            /*Aqui ponemos estas condiciones para que se actualice el cambio*/
                            this.obtenersuma();



          						},
          						failure: this.conexionFailure,
          						timeout:this.timeout,
          						scope:this
          				});
                }
                this.devolverCambio();
               },this);
               /*Aqui verificaremos si el deposito existe o no existe ya registrado*/
              this.Cmp.nro_deposito.on('change',function(field,newValue,oldValue){
                if(this.Cmp.nro_deposito.getValue() != '' && this.Cmp.nro_deposito.getValue() != null) {
                Ext.Ajax.request({
         					url:'../../sis_ventas_facturacion/control/VentaFacturacion/verificarDeposito',
         					params:{
                     nro_deposito:this.Cmp.nro_deposito.getValue(),
                     id_moneda:this.Cmp.id_moneda.getValue(),
         						fecha:this.Cmp.fecha_deposito.getValue(),
         					},
         					success: function(resp){
         							var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                          if (reg.ROOT.datos.cantidad_deposito > 0) {
                            /*Bloqueamos el boton para que no genere si el deposito*/
                             this.arrayBotones[0].scope.form.buttons[0].setDisabled(true);
                             Ext.Msg.show({
           											title: 'Alerta',
           											msg: '<p>El número de depósito <b>'+reg.ROOT.datos.nro_deposito+'</b>. ya se encuentra registrado favor contactarse con personal de ventas e ingrese nuevamente el nro de depósito</p>',
           											buttons: Ext.Msg.OK,
           											width: 512,
           											icon: Ext.Msg.INFO
           									});
                             this.Cmp.nro_deposito.reset();
                           /********************************************************/
                             // this.Cmp.fecha_deposito.setValue(reg.ROOT.datos.fecha_deposito);
                             // this.Cmp.nro_deposito.setValue(reg.ROOT.datos.nro_deposito);
                             // this.Cmp.id_deposito.setValue(reg.ROOT.datos.id_deposito);
                             // this.Cmp.monto_deposito.setValue(reg.ROOT.datos.monto_deposito);
                             // this.Cmp.monto_forma_pago.setValue(reg.ROOT.datos.monto_deposito);
                             //this.Cmp.id_deposito.setValue(reg.ROOT.datos.id_deposito);
                          }else {
                            this.arrayBotones[0].scope.form.buttons[0].setDisabled(false);
                            //this.Cmp.fecha_deposito.reset();
                            //this.Cmp.nro_deposito.reset();
                            //this.Cmp.monto_deposito.setValue(0);
                            //this.Cmp.monto_forma_pago.setValue(0);
                          }

                          /*Aqui ponemos estas condiciones para que se actualice el cambio*/
                          this.obtenersuma();
                          this.devolverCambio();


         					},
         					failure: this.conexionFailure,
         					timeout:this.timeout,
         					scope:this
         			});
               }
              },this);
          // this.Cmp.tipo_tarjeta.setValue(r.data.nombre);
          // this.Cmp.codigo_tarjeta.allowBlank = false;
          // this.Cmp.tipo_tarjeta.allowBlank = false;
          // this.Cmp.mco.allowBlank = true;
        /*********************************************/

        } else {
          this.Cmp.codigo_tarjeta.reset();
          this.Cmp.tipo_tarjeta.reset();
          this.Cmp.id_auxiliar.reset();
          this.Cmp.mco.reset();
          this.Cmp.numero_tarjeta.reset();
        }

      },this);

      this.Cmp.id_instancia_pago_2.on('select',function(c,r,i) {
        var codigo_forma_pago = r.data.codigo_forma_pago.substr(0,2);
        this.Cmp.tipo_tarjeta_2.setValue(r.data.nombre);
        if (codigo_forma_pago == 'CC') {
          this.mostrarComponente(this.Cmp.codigo_tarjeta_2);
          this.mostrarComponente(this.Cmp.tipo_tarjeta_2);
          this.mostrarComponente(this.Cmp.numero_tarjeta_2);
          this.ocultarComponente(this.Cmp.id_auxiliar_2);
          this.ocultarComponente(this.Cmp.mco_2);
          this.Cmp.tipo_tarjeta_2.setValue(r.data.nombre);
          this.Cmp.codigo_tarjeta_2.allowBlank = false;
          this.Cmp.tipo_tarjeta_2.allowBlank = false;
          this.Cmp.mco_2.allowBlank = true;
        } else if (codigo_forma_pago == 'MC') {
          this.mostrarComponente(this.Cmp.mco_2);
          this.Cmp.numero_tarjeta_2.allowBlank = true;
          this.Cmp.codigo_tarjeta_2.allowBlank = true;
          this.Cmp.tipo_tarjeta_2.allowBlank = true;
          this.Cmp.mco_2.allowBlank = false;
          this.ocultarComponente(this.Cmp.codigo_tarjeta_2);
          this.ocultarComponente(this.Cmp.tipo_tarjeta_2);
          this.ocultarComponente(this.Cmp.id_auxiliar_2);
          this.Cmp.codigo_tarjeta_2.reset();
          this.Cmp.tipo_tarjeta_2.reset();
          this.Cmp.id_auxiliar_2.reset();
          this.Cmp.numero_tarjeta_2.reset();
        } else if (codigo_forma_pago == 'CU') {
          this.mostrarComponente(this.Cmp.id_auxiliar_2);
          this.Cmp.numero_tarjeta_2.allowBlank = true;
          this.Cmp.codigo_tarjeta_2.allowBlank = true;
          this.Cmp.tipo_tarjeta_2.allowBlank = true;
          this.Cmp.mco_2.allowBlank = true;
          this.Cmp.id_auxiliar_2.allowBlank = false;
          this.ocultarComponente(this.Cmp.codigo_tarjeta_2);
          this.ocultarComponente(this.Cmp.tipo_tarjeta_2);
          this.ocultarComponente(this.Cmp.numero_tarjeta_2);
          this.Cmp.codigo_tarjeta_2.reset();
          this.Cmp.tipo_tarjeta_2.reset();
          this.Cmp.id_auxiliar_2.reset();
          this.Cmp.mco_2.reset();
          this.Cmp.numero_tarjeta_2.reset();
        }else if (codigo_forma_pago == 'CA') {
          this.mostrarComponente(this.Cmp.id_auxiliar_2);
          this.Cmp.numero_tarjeta_2.allowBlank = true;
          this.Cmp.codigo_tarjeta_2.allowBlank = true;
          this.Cmp.tipo_tarjeta_2.allowBlank = true;
          this.Cmp.id_auxiliar_2.allowBlank = true;
          this.Cmp.mco_2.allowBlank = true;
          this.ocultarComponente(this.Cmp.codigo_tarjeta_2);
          this.ocultarComponente(this.Cmp.tipo_tarjeta_2);
          this.ocultarComponente(this.Cmp.numero_tarjeta_2);
          this.ocultarComponente(this.Cmp.id_auxiliar_2);
          this.ocultarComponente(this.Cmp.mco_2);
          this.Cmp.codigo_tarjeta_2.reset();
          this.Cmp.tipo_tarjeta_2.reset();
          this.Cmp.id_auxiliar_2.reset();
          this.Cmp.mco_2.reset();
          this.Cmp.numero_tarjeta_2.reset();
        } else {
          this.Cmp.codigo_tarjeta_2.reset();
          this.Cmp.tipo_tarjeta_2.reset();
          this.Cmp.id_auxiliar_2.reset();
          this.Cmp.mco_2.reset();
          this.Cmp.numero_tarjeta_2.reset();
        }

        if (this.Cmp.id_moneda.value != 2 && this.Cmp.id_moneda_2.value == 2) {
           if (this.id_moneda_recibo_cambio == 2) {
             this.Cmp.monto_forma_pago_2.setValue(((this.suma_total*this.tipo_cambio)-this.Cmp.monto_forma_pago.getValue())/this.tipo_cambio);
           } else {
             this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue())/this.tipo_cambio);
           }
        }else if (this.Cmp.id_moneda.value == 2 && this.Cmp.id_moneda_2.value == 2) {
          if (this.id_moneda_recibo_cambio == 2) {
             this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()));
          } else {
             this.Cmp.monto_forma_pago_2.setValue(((this.suma_total/this.tipo_cambio)-this.Cmp.monto_forma_pago.getValue()));
           }
        }else if (this.Cmp.id_moneda.value == 2 && this.Cmp.id_moneda_2.value != 2) {
          if (this.id_moneda_recibo_cambio == 2) {
             this.Cmp.monto_forma_pago_2.setValue(((this.suma_total-this.Cmp.monto_forma_pago.getValue())*this.tipo_cambio));
          } else {
             this.Cmp.monto_forma_pago_2.setValue((this.suma_total-(this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)));
           }
        }
        else{
            this.Cmp.monto_forma_pago_2.setValue(this.suma_total-this.Cmp.monto_forma_pago.getValue());
        }


      },this);
      /************************************Fin agregar instancia de pago***************************************/

    },


    onSubmit: function(o) {
        //  validar formularios
        console.log("que es esto",o);
        var arra = [], i, me = this;
        var formapa = [];
        for (i = 0; i < me.megrid.store.getCount(); i++) {
            var record = me.megrid.store.getAt(i);
            arra[i] = record.data;
        }
        if (me.storeFormaPago) {
	        for (i = 0; i < me.storeFormaPago.getCount(); i++) {
	            var record = me.storeFormaPago.getAt(i);
	            formapa[i] = record.data;
	        }
	    }
        me.argumentExtraSubmit = { 'json_new_records': JSON.stringify(arra,
        				function replacer(key, value) {
                       		if (typeof value === 'string') {
                            	return String(value).replace(/&/g, "%26")
                            }
                            return value;
                        }),
                        'formas_pago' :  JSON.stringify(formapa,
        				function replacer(key, value) {
                       		if (typeof value === 'string') {
                            	return String(value).replace(/&/g, "%26")
                            }
                            return value;
                        }),
                        'tipo_factura':this.data.objPadre.tipo_factura};

        if( i > 0 &&  !this.editorDetail.isVisible()){
             Phx.vista.FormRecibo.superclass.onSubmit.call(this,o);
        }
        else{
            alert('La venta no tiene registrado ningun detalle');
        }
    },

    successSave:function(resp)
    {
    	var datos_respuesta = JSON.parse(resp.responseText);

    	Phx.CP.loadingHide();
      //console.log(datos_respuesta.ROOT.datos);
    	//console.log("llega aqui para ver los datos",this);
      if (this.generar == 'generar') {
      //  console.log("llega aqui generar",datos_respuesta);
        Phx.CP.loadingShow();
  			var d = datos_respuesta.ROOT.datos;
        //console.log('DATOS:',d);
  			Ext.Ajax.request({
  					url:'../../sis_ventas_facturacion/control/Venta/siguienteEstadoRecibo',
  					params:{id_estado_wf_act:d.id_estado_wf,
  									id_proceso_wf_act:d.id_proceso_wf,
  								  tipo:'recibo'},
  					success:this.successWizard,
  					failure: this.conexionFailure,
  					timeout:this.timeout,
  					scope:this
  			});
      }else{
        //console.log("no llega generar");
      }


    	if ('cambio' in datos_respuesta.ROOT.datos) {
    		Ext.Msg.show({
			   title:'DEVOLUCION',
			   msg: 'Debe devolver ' + datos_respuesta.ROOT.datos.cambio + ' al cliente',
			   buttons: Ext.Msg.OK,
			   fn: function () {
		        Phx.CP.getPagina(this.idContenedorPadre).reload();
		        this.panel.close();
			   },
			   scope:this
			});
    	} else {
    		Phx.CP.getPagina(this.idContenedorPadre).reload();
		    this.panel.close();
    	}

    },


})
</script>
