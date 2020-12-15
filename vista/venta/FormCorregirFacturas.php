<?php
/**
*@package pXP
*@file    FormCorregirFacturas.php
*@author  Ismael Valdivia Aranibar
*@date    11/04/2019
*@description permites subir archivos a la tabla de documento_sol
*/
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.FormCorregirFacturas=Ext.extend(Phx.frmInterfaz,{
    ActSave:'../../sis_ventas_facturacion/control/VentaFacturacion/corregirFactura',
    tam_pag: 10,
    layout: 'fit',
    tabEnter: true,
    autoScroll: false,
    breset: true,
    bsubmit:false,
    storeFormaPago : false,
    fwidth : '9%',
    cantidadAllowDecimals: false,
    constructor:function(config)
    {
		Ext.apply(this,config);
    this.data.objPadre.tipo_factura = 'computarizada';

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
	                gwidth: 200,
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

    if (this.data.objPadre.tipo_factura == 'computarizada' || this.data.objPadre.tipo_factura == ''){
			this.Atributos.push({
		            config:{
		                name: 'excento',
		                fieldLabel: 'Excento',
		                allowBlank: false,
		                //anchor: '80%',
                    width:200,
                    style:{
                      background:'#FFD1A4'
                    },
		                maxLength:20,
		                value : 0
		            },
		                type:'NumberField',
		                id_grupo:22,
		                form:true,
		                valorInicial:'0'
		      });

		}
		if (!this.tipoDetalleArray) {
		  this.tipoDetalleArray = this.data.objPadre.variables_globales.vef_tipo_venta_habilitado.split(",");
        }
        this.addEvents('beforesave');
        this.addEvents('successsave');

        this.buildComponentesDetalle();
        //this.buildDetailGrid();
        if (this.data.tipo_form == 'edit') {
          this.buildDetailGridEdit();
        } else {
          this.buildDetailGridNew();
        }

        this.buildGrupos();

        this.labelReset = '<div style = "font-size:25px; font-weight:bold; color:#0435FF; text-shadow: -1px -1px 1px rgba(255,255,255,.1), 1px 1px 1px rgba(0,0,0,.5);"><img src="../../../lib/imagenes/icono_dibu/dibu_edit.png" style="float:left; vertical-align: middle;">CORREGIR</div>';
        Phx.vista.FormCorregirFacturas.superclass.constructor.call(this,config);
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
        this.init();
        this.iniciarEventos();

        if(this.data.tipo_form == 'new'){
        	this.onNew();
          //this.onEdit();
        }else{
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
                                                url: '../../sis_ventas_facturacion/control/Servicios/listarServicios',
                                                id: 'id_producto',
                                                root: 'datos',
                                                sortInfo: {
                                                    field: 'desc_ingas',
                                                    direction: 'ASC'
                                                },
                                                totalProperty: 'total',
                                                fields: ['id_concepto_ingas', 'tipo','desc_ingas','requiere_descripcion','precio','excento'],
                                                remoteSort: true,
                                                baseParams: {par_filtro: 'ingas.desc_ingas'}
                                            }),
                                            valueField: 'id_producto',
                                            displayField: 'desc_ingas',
                                            gdisplayField: 'desc_ingas',
                                            hiddenName: 'id_producto',
                                            forceSelection: true,
                                            tpl: new Ext.XTemplate([
                                        				'<tpl for=".">',
                                        				'<div class="x-combo-list-item">',
                                        				'<p><b>Nombre:</b><span style="color: green; font-weight:bold;"> {desc_ingas}</span></p></p>',
                                        				'<p><b>Descripcion:</b> <span style="color: blue; font-weight:bold;">{desc_ingas}</span></p>',
                                        				'<p><b>Precio:</b> <span style="color: blue; font-weight:bold;">{precio}</span></p>',
                                        				'<p><b>Tiene Excento:</b> <span style="color: red; font-weight:bold;">{desc_ingas}</span></p>',
                                        				'<p><b>Requiere Descripción:</b> <span style="color: red; font-weight:bold;">{desc_ingas}</span></p>',
                                        				'</div></tpl>'
                                        			]),
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
                                        decimalPrecision : 2,
                                        enableKeyEvents : false,


                                }),
                    'precio_unitario': new Ext.form.NumberField({
                                        name: 'precio_unitario',
                                        msgTarget: 'title',
                                        fieldLabel: 'P/U',
                                        allowBlank: false,
                                        allowDecimals: true,
                                        decimalPrecision : 2,
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
                                })

              }


    },

    iniciarEventosProducto:function(){
      console.log("llega eventos producto");
    	this.detCmp.id_producto.on('select',function(c,r,i) {
            this.mestore.data.items[0].data.nombre_producto = r.data.nombre_producto;

        	if (r.data.requiere_descripcion == 'si') {
        		this.habilitarDescripcion(true);
        	} else {
        		this.habilitarDescripcion(false);
        	}

        },this);
        //this.obtenersuma();
    },
    /*Funcion para poner las condiciones en las regionales*/
    condicionesRegionales: function (){

      /*Aumentanod para filtrar las instancias de pago (Ismael Valdivia 16/10/2020)*/
      this.Cmp.id_formula.store.baseParams.regional = this.data.objPadre.variables_globales.ESTACION_inicio;
      this.Cmp.id_medio_pago.store.baseParams.regional = this.data.objPadre.variables_globales.ESTACION_inicio;
      this.Cmp.id_medio_pago_2.store.baseParams.regional = this.data.objPadre.variables_globales.ESTACION_inicio;
      /****************************************************************************/
    },
    /******************************************************/
    iniciarEventos : function () {

      /*Aumentando para poner Condiciones en Regionales (Ismael Valdivia 14/10/2020)*/
      this.condicionesRegionales();
      /*****************************************************************************/

        this.Cmp.cambio.setValue(0);
        this.Cmp.cambio_moneda_extranjera.setValue(0);

        //this.obtenersuma();
        /*Filtramos la lista de paquetes por la sucursal seleccionada*/
    		//this.Cmp.id_formula.store.baseParams.tipo_punto_venta = this.variables_globales.tipo_pv;
    		this.Cmp.id_formula.store.baseParams.id_punto_venta = this.data.objPadre.variables_globales.id_punto_venta;
    		/*************************************************************/

        this.Cmp.id_sucursal.store.load({params:{start:0,limit:50},
           callback : function (r) {

           		this.Cmp.id_sucursal.setValue(this.data.objPadre.variables_globales.id_sucursal);
           		if (this.data.objPadre.variables_globales.vef_tiene_punto_venta != 'true') {
           			this.detCmp.id_producto.store.baseParams.id_sucursal = this.Cmp.id_sucursal.getValue();
                }
                this.Cmp.id_sucursal.fireEvent('select',this.Cmp.id_sucursal, this.Cmp.id_sucursal.store.getById(this.data.objPadre.variables_globales.id_sucursal));

            }, scope : this
        });

        if (this.data.objPadre.variables_globales.vef_tiene_punto_venta === 'true') {

          /*******************cambiaremos el estilo del boton guardar *********************/
          //this.arrayBotones[0].scope.form.buttons[0].container.dom.style.border="2px solid red";
          this.arrayBotones[0].scope.form.buttons[0].container.dom.style.width="12px";
          //this.arrayBotones[0].scope.form.buttons[0].btnEl.dom.style.border="2px solid blue";
          this.arrayBotones[0].scope.form.buttons[0].btnEl.dom.style.width="200px";
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
                this.cargarFormaPago();
            },this);
        }

        this.ocultarComponente(this.Cmp.mco_2);
        this.ocultarComponente(this.Cmp.numero_tarjeta_2);
        this.ocultarComponente(this.Cmp.codigo_tarjeta_2);
        this.ocultarComponente(this.Cmp.id_auxiliar_2);
        this.ocultarComponente(this.Cmp.tipo_tarjeta_2);

        /****************************Aumnetando la instancia de pago********************************/
        this.Cmp.id_medio_pago.on('select',function(c,r,i) {

          if(r){
            if (r.data) {
              var codigo_forma_pago = r.data.fop_code;
              this.Cmp.tipo_tarjeta.setValue(r.data.name);
            }
          }

          //this.Cmp.tipo_tarjeta.setValue(r.data.nombre);
          if (codigo_forma_pago != undefined && codigo_forma_pago != '' && codigo_forma_pago != null) {
          if (codigo_forma_pago.startsWith("CC")) {
            this.mostrarComponente(this.Cmp.codigo_tarjeta);
            this.ocultarComponente(this.Cmp.tipo_tarjeta);
          	this.mostrarComponente(this.Cmp.numero_tarjeta);
			      this.ocultarComponente(this.Cmp.id_auxiliar);
            this.ocultarComponente(this.Cmp.mco);
            this.Cmp.tipo_tarjeta.setValue(r.data.nombre);
			      this.Cmp.codigo_tarjeta.allowBlank = false;
            this.Cmp.tipo_tarjeta.allowBlank = false;
          	this.Cmp.mco.allowBlank = true;
          } else if (codigo_forma_pago.startsWith("MCO")) {
            this.mostrarComponente(this.Cmp.mco);
            this.Cmp.numero_tarjeta.allowBlank = true;
          	this.Cmp.codigo_tarjeta.allowBlank = true;
          	this.Cmp.tipo_tarjeta.allowBlank = true;
            this.Cmp.mco.allowBlank = false;
            this.ocultarComponente(this.Cmp.codigo_tarjeta);
          	this.ocultarComponente(this.Cmp.tipo_tarjeta);
			      this.ocultarComponente(this.Cmp.id_auxiliar);
            this.Cmp.codigo_tarjeta.reset();
            this.Cmp.tipo_tarjeta.reset();
            this.Cmp.id_auxiliar.reset();
            this.Cmp.numero_tarjeta.reset();
          } else if (codigo_forma_pago.startsWith("CU") || codigo_forma_pago.startsWith("CT")) {
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
          }else if (codigo_forma_pago.startsWith("CA")) {
            this.mostrarComponente(this.Cmp.id_auxiliar);
            this.Cmp.numero_tarjeta.allowBlank = true;
          	this.Cmp.codigo_tarjeta.allowBlank = true;
            this.Cmp.tipo_tarjeta.allowBlank = true;
            this.Cmp.id_auxiliar.allowBlank = true;
          	this.Cmp.mco.allowBlank = true;
            this.ocultarComponente(this.Cmp.codigo_tarjeta);
          	this.ocultarComponente(this.Cmp.tipo_tarjeta);
            this.ocultarComponente(this.Cmp.numero_tarjeta);
            this.ocultarComponente(this.Cmp.id_auxiliar);
			      this.ocultarComponente(this.Cmp.mco);
            this.Cmp.codigo_tarjeta.reset();
            this.Cmp.tipo_tarjeta.reset();
            this.Cmp.id_auxiliar.reset();
            this.Cmp.mco.reset();
            this.Cmp.numero_tarjeta.reset();
          } else {
            this.Cmp.codigo_tarjeta.reset();
            this.Cmp.tipo_tarjeta.reset();
            this.Cmp.id_auxiliar.reset();
            this.Cmp.mco.reset();
            this.Cmp.numero_tarjeta.reset();
          }
        }

        },this);


        this.Cmp.id_medio_pago_2.on('select',function(c,r,i) {
          var codigo_forma_pago = r.data.fop_code;
          this.Cmp.tipo_tarjeta_2.setValue(r.data.nombre);
          if (codigo_forma_pago.startsWith("CC")) {
            this.mostrarComponente(this.Cmp.codigo_tarjeta_2);
            this.ocultarComponente(this.Cmp.tipo_tarjeta_2);
          	this.mostrarComponente(this.Cmp.numero_tarjeta_2);
			      this.ocultarComponente(this.Cmp.id_auxiliar_2);
            this.ocultarComponente(this.Cmp.mco_2);
            this.Cmp.tipo_tarjeta_2.setValue(r.data.nombre);
			      this.Cmp.codigo_tarjeta_2.allowBlank = false;
            this.Cmp.tipo_tarjeta_2.allowBlank = false;
          	this.Cmp.mco_2.allowBlank = true;
          } else if (codigo_forma_pago.startsWith("MCO")) {
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
          } else if (codigo_forma_pago.startsWith("CU") || codigo_forma_pago.startsWith("CT")) {
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
          }else if (codigo_forma_pago.startsWith("CA")) {
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

            if (this.Cmp.id_moneda.value != 2 && this.Cmp.id_moneda_2.value == 2 ) {
               //this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue())/this.tipo_cambio);

            }else if (this.Cmp.id_moneda.value == 2 && this.Cmp.id_moneda_2.value == 2) {
              //this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)/this.tipo_cambio);

            }else if (this.Cmp.id_moneda.value == 2 && this.Cmp.id_moneda_2.value != 2) {
              //this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio));
            }
            else{
                //this.Cmp.monto_forma_pago_2.setValue(this.suma_total-this.Cmp.monto_forma_pago.getValue());
            }



        },this);

        /************************************Fin agregar instancia de pago***************************************/

        /********************************Aumemtando condicios para el id moneda****************************************/
        this.Cmp.id_moneda.on('select',function(c,r,i) {
          if(r.data.id_moneda == 2){
            this.Cmp.cambio.setValue(((this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)-this.suma_total));
            this.Cmp.cambio_moneda_extranjera.setValue(((this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)-this.suma_total)/this.tipo_cambio);
          } else {
            this.Cmp.cambio.setValue((this.Cmp.monto_forma_pago.getValue()-this.suma_total));
            this.Cmp.cambio_moneda_extranjera.setValue((this.Cmp.monto_forma_pago.getValue()-this.suma_total)/this.tipo_cambio);
          }

          if (r.data.id_moneda == 2 && (this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio) < this.suma_total) {
            /**********************************Cambiamos el Style *****************************************/
            this.Cmp.cambio_moneda_extranjera.label.dom.control.style.color = "red";
            this.Cmp.cambio_moneda_extranjera.label.dom.control.style.background = "#FFE4E4";
            this.Cmp.cambio.label.dom.control.style.color = "red";
            this.Cmp.cambio.label.dom.control.style.background = "#FFE4E4";
            /**********************************Cambiamos el Style *****************************************/

            this.Cmp.id_medio_pago_2.enable();
            this.Cmp.id_moneda_2.enable();
            this.Cmp.monto_forma_pago_2.enable();
              if (this.recuperar_monto_automatico == 'SI') {
                this.Cmp.monto_forma_pago_2.setValue((this.suma_total-(this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio))/this.tipo_cambio);
              }
            }else if (this.Cmp.id_moneda.getValue() != 2 && this.Cmp.monto_forma_pago.getValue() < this.suma_total) {

            /**********************************Cambiamos el Style *****************************************/
            this.Cmp.cambio_moneda_extranjera.label.dom.control.style.color = "red";
            this.Cmp.cambio_moneda_extranjera.label.dom.control.style.background = "#FFE4E4";
            this.Cmp.cambio.label.dom.control.style.color = "red";
            this.Cmp.cambio.label.dom.control.style.background = "#FFE4E4";
            /**********************************Cambiamos el Style *****************************************/

            this.Cmp.id_medio_pago_2.enable();
            this.Cmp.id_moneda_2.enable();
            this.Cmp.monto_forma_pago_2.enable();
            //this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()));

          } else{

            this.Cmp.id_moneda_2.disable();
            this.Cmp.id_medio_pago_2.disable();
            this.Cmp.monto_forma_pago_2.disable();
            this.Cmp.monto_forma_pago_2.reset();
            this.Cmp.id_medio_pago_2.reset();
            this.Cmp.id_moneda_2.reset();

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
        },this);


        this.Cmp.id_moneda_2.on('select',function(c,r,i) {

            if (this.Cmp.id_moneda.value != 2 && this.Cmp.id_moneda_2.value == 2 ) {
               //this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue())/this.tipo_cambio);

            }else if (this.Cmp.id_moneda.value == 2 && this.Cmp.id_moneda_2.value == 2) {
               //this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)/this.tipo_cambio);

            }else if (this.Cmp.id_moneda.value == 2 && this.Cmp.id_moneda_2.value != 2) {
               //this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio));
            }
            else{
               //this.Cmp.monto_forma_pago_2.setValue(this.suma_total-this.Cmp.monto_forma_pago.getValue());
            }


        },this);
        /*Formatear numero*/
        // this.Cmp.codigo_control.on('keyup', function (cmp, e) {
        //         /*Formatearemos la inserccion*/
        //         // var dato = cmp.getValue().replace( /[^0-9]/g, '' );
        //         // if (dato.length = 1 )
        //         //   dato = dato + ".00";
        //         // cmp.setValue(dato.toUpperCase());
        //
        //         var value = cmp.getValue(), tmp = '', tmp2 = '', sw = 0;
        //         tmp = value.replace(/,/g, '');
        //         var number = cmp.getValue().replace( /[^0-9]/g, '' );
        //
        //         if( number.length == 0 ) number = "0.00";
        //         else if( number.length == 1 ) number = "0.0" + number;
        //         else if( number.length == 2 ) number = "0." + number;
        //         else number = number.substring( 0, number.length - 2 ) + '.' + number.substring( number.length - 2, number.length );
        //
        //           console.log("llega aqui el value",number);
        //         number = new Number( number );
        //         number = number.toFixed( 2 );    // only works with the "."
        //
        //         //change the splitter to ","
        //         number = number.replace( /\./g, ',' );
        //
        //         // format the amount
        //         x = number.split( ',' );
        //         x1 = x[0];
        //         x2 = x.length > 1 ? ',' + x[1] : '';
        //
        //         var rgx = /(\d+)(\d{3})/;
        //           while( rgx.test( number ) ) {
        //               number = number.replace( rgx, '$1' + '.' + '$2' );
        //           }
        //
        //         cmp.setValue(number.toUpperCase());
        //         console.log("llega aqui el value",number);
        //
        //         /*var value = cmp.getValue(), tmp = '', tmp2 = '', sw = 0;
        //         tmp = value.replace(/,/g, '');
        //         console.log("llega aqui el value",tmp.length);
        //         for (var i = 0; i < tmp.length; i++) {
        //             tmp2 = tmp2 + tmp[i];
        //             if ((i + 1) % 3 == 0 && i != tmp.length - 1) {
        //                 tmp2 = tmp2 + ',';
        //             }
        //         }
        //         cmp.setValue(tmp2.toUpperCase());*/
        //     }, this);

          this.Cmp.monto_forma_pago.on('change',function(field,newValue,oldValue){
          //  this.Cmp.monto_forma_pago.setValue(Ext.util.Format.number(this.Cmp.monto_forma_pago.getValue(),'0,0.00'));

            //this.obtenersuma();
          if(this.Cmp.id_moneda.getValue() == 2){
              //console.log("llega el dolar");
            this.Cmp.cambio.setValue(((this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)-this.suma_total));
            this.Cmp.cambio_moneda_extranjera.setValue(((this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)-this.suma_total)/this.tipo_cambio);
          } else {
            //var cambio = parseFloat(this.Cmp.monto_forma_pago.getValue().toString()).toFixed(2);
            //var coversion_monto = parseFloat(this.Cmp.monto_forma_pago.getValue().replace(/,/g, ''));
            // console.log("llega aqui",parseFloat(coversion_monto));
            // this.Cmp.cambio.setValue((coversion_monto-this.suma_total));
            // this.Cmp.cambio_moneda_extranjera.setValue((coversion_monto-this.suma_total)/this.tipo_cambio);

            this.Cmp.cambio.setValue((this.Cmp.monto_forma_pago.getValue()-this.suma_total));
            this.Cmp.cambio_moneda_extranjera.setValue((this.Cmp.monto_forma_pago.getValue()-this.suma_total)/this.tipo_cambio);
          }

          if (this.Cmp.id_moneda.getValue() == 2 && (this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio) < this.suma_total) {


            /**********************************Cambiamos el Style *****************************************/
            this.Cmp.cambio_moneda_extranjera.label.dom.control.style.color = "red";
            this.Cmp.cambio_moneda_extranjera.label.dom.control.style.background = "#FFE4E4";
            this.Cmp.cambio.label.dom.control.style.color = "red";
            this.Cmp.cambio.label.dom.control.style.background = "#FFE4E4";
            /**********************************Cambiamos el Style *****************************************/

            this.Cmp.id_moneda_2.enable();
            this.Cmp.id_medio_pago_2.enable();
            this.Cmp.monto_forma_pago_2.enable();

            //  this.Cmp.monto_forma_pago_2.setValue((this.suma_total-(this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio))/this.tipo_cambio);

          }else if (this.Cmp.id_moneda.getValue() != 2 && this.Cmp.monto_forma_pago.getValue() < this.suma_total) {


            /**********************************Cambiamos el Style *****************************************/
            this.Cmp.cambio_moneda_extranjera.label.dom.control.style.color = "red";
            this.Cmp.cambio_moneda_extranjera.label.dom.control.style.background = "#FFE4E4";
            this.Cmp.cambio.label.dom.control.style.color = "red";
            this.Cmp.cambio.label.dom.control.style.background = "#FFE4E4";
            /**********************************Cambiamos el Style *****************************************/


            this.Cmp.id_moneda_2.enable();
            this.Cmp.id_medio_pago_2.enable();
            this.Cmp.monto_forma_pago_2.enable();

            //this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()));

          } else{

            this.Cmp.id_moneda_2.disable();
            this.Cmp.id_medio_pago_2.disable();
            this.Cmp.monto_forma_pago_2.disable();
            this.Cmp.monto_forma_pago_2.reset();

            this.Cmp.id_moneda_2.reset();
            this.Cmp.id_medio_pago_2.reset();

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


          this.Cmp.monto_forma_pago_2.on('change',function(field,newValue,oldValue){
            if (this.Cmp.id_moneda.getValue() == 2) {
              var cambio_calculado_2 = this.suma_total - (this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio);
            } else {
              var cambio_calculado_2 = this.suma_total - this.Cmp.monto_forma_pago.getValue();
            }

            if (this.Cmp.id_moneda_2.getValue() == 2) {
              this.Cmp.cambio.setValue((this.Cmp.monto_forma_pago_2.getValue()*this.tipo_cambio)-cambio_calculado_2);
              this.Cmp.cambio_moneda_extranjera.setValue(this.Cmp.monto_forma_pago_2.getValue()-(cambio_calculado_2/this.tipo_cambio));
            } else {
              this.Cmp.cambio.setValue(this.Cmp.monto_forma_pago_2.getValue()-cambio_calculado_2);
              this.Cmp.cambio_moneda_extranjera.setValue((this.Cmp.monto_forma_pago_2.getValue()-cambio_calculado_2)/this.tipo_cambio);
            }

            if (this.Cmp.cambio.getValue()>0) {
              this.Cmp.cambio_moneda_extranjera.label.dom.control.style.color = "blue";
              this.Cmp.cambio_moneda_extranjera.label.dom.control.style.background = "#EFFFD6";
              this.Cmp.cambio.label.dom.control.style.color = "";
              this.Cmp.cambio.label.dom.control.style.background = "#EFFFD6";
            } else {
              this.Cmp.cambio_moneda_extranjera.label.dom.control.style.color = "red";
              this.Cmp.cambio_moneda_extranjera.label.dom.control.style.background = "#FFE4E4";
              this.Cmp.cambio.label.dom.control.style.color = "red";
              this.Cmp.cambio.label.dom.control.style.background = "#FFE4E4";
            }


          },this);

        },this);
        /**************************************************************************************************************/

        this.Cmp.id_sucursal.on('select',function(c,r,i) {
            this.cargarFormaPago();
        },this);


        this.detCmp.tipo.on('select',function(c,r,i) {
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

                }
            }
        },this);

        //this.obtenersuma();
        /*Ocultar campo excento*/
        if (this.Cmp.excento.getValue() == 0) {
          this.ocultarComponente(this.Cmp.excento);
        } else {
          this.mostrarComponente(this.Cmp.excento);
        }
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
      if (this.accionFormulario == 'EDIT' || this.accionFormulario == 'NEW' ) {
        this.crearStoreFormaPago();
      }
      if (this.accionFormulario == 'EDIT') {
        //this.mostrarComponente(this.Cmp.habilitar_edicion);
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

        this.Cmp.nit.setDisabled(true);
        this.Cmp.id_cliente.setDisabled(true);
        this.Cmp.id_formula.setDisabled(true);
        this.Cmp.observaciones.setDisabled(true);
        this.Cmp.id_punto_venta.setDisabled(true);
        this.Cmp.excento.setDisabled(true);

        /*Todos los componentes estan deshabilitados*/
        this.Cmp.nit.setDisabled(true);
        this.Cmp.id_cliente.setDisabled(true);
        this.Cmp.id_formula.setDisabled(true);
        this.Cmp.observaciones.setDisabled(true);
        this.Cmp.id_punto_venta.setDisabled(true);
        this.Cmp.excento.setDisabled(true);

        this.megrid.colModel.config[3].editor='';
        this.megrid.colModel.config[4].editor='';
        this.megrid.colModel.config[5].editor='';


        /**************************************************************************/
        this.Cmp.id_formula.store.load({params:{start:0,limit:50},
           callback : function (r) {
              this.Cmp.id_formula.setValue(this.data.datos_originales.data.id_formula);
            }, scope : this
        });
      /*************************************/

    }



    },


    obtenersuma: function () {
      console.log("llega aqui la suma");
      var total_datos = this.megrid.store.data.items.length;
      var suma = 0;
      for (var i = 0; i < total_datos; i++) {
          suma = suma + parseFloat(this.megrid.store.data.items[i].data.precio_total);
      }
      this.suma_total = suma;
      this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[6].childNodes[0].style.color="#7400FF";
      this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[6].childNodes[0].style.fontWeight="bold";
      this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[6].childNodes[0].style.fontSize="20px";
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

    buildDetailGridEdit: function(){
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
                    url: '../../sis_ventas_facturacion/control/Cajero/listarVentaDetalle',
                    id: 'id_venta_detalle',
                    root: 'datos',
                    totalProperty: 'total',
                    fields: [
                        {name:'id_venta_detalle', type: 'numeric'},
                        {name:'id_venta', type: 'numeric'},
                        {name:'id_producto', type: 'numeric'},
                        {name:'id_sucursal_producto', type: 'numeric'},
                        {name:'nombre_producto', type: 'string'},
                        {name:'precio_unitario', type: 'numeric'},
                        {name:'cantidad', type: 'numeric'},
                        {name:'precio_total', type: 'numeric'},
                        {name:'descripcion', type: 'string'},
                        {name:'tipo', type: 'string'},
                        {name:'estado_reg', type: 'string'},
                        {name:'id_usuario_ai', type: 'numeric'},
                        {name:'usuario_ai', type: 'string'},
                        {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                        {name:'id_usuario_reg', type: 'numeric'},
                        {name:'id_usuario_mod', type: 'numeric'},
                        {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},

                    ],
                    remoteSort: true,
                    baseParams: {dir:'ASC',sort:'id_venta_detalle',limit:'50',start:'0'}
                });

            this.editorDetail = new Ext.ux.grid.RowEditor({

                });


        this.summary = new Ext.ux.grid.GridSummary();

        /*megrid irva condicion*/
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
                    columns: [
                    new Ext.grid.RowNumberer(),
                    {
                        header: 'Tipo',
                        dataIndex: 'tipo',
                        width: 90,
                        sortable: false,
                        //editor: this.detCmp.tipo
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
                        //editor: this.detCmp.id_producto
                    },
                    {
                        header: 'Descripción',
                        dataIndex: 'descripcion',
                        width: 300,
                        //sortable: false,
                        editor: ''
                    },
                    {

                        header: 'Cantidad',
                        dataIndex: 'cantidad',
                        align: 'right',
                        width: 75,
                        summaryType: 'sum',
                        editor: ''
                    },
                    {
                        header: 'P / Unit',
                        dataIndex: 'precio_unitario',
                        align: 'right',
                        selectOnFocus: true,
                        width: 85,
                        decimalPrecision : 2,
                        summaryType: 'sum',
                        renderer : function(value, p, record) {
                            return parseFloat(record.data['precio_unitario']);
                        },
                        editor: ''
                    },
                    {
                        xtype: 'numbercolumn',
                        header: 'Total',
                        dataIndex: 'precio_total',
                        align: 'right',
                        width: 150,/*irva222*/
                        format: '0,0.00',
                        summaryType: 'sum',
                        //editor: this.detCmp.precio_total
                    }
                  ]
                });
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
                            autoScroll:true,
                            style: {
                                   height:'130px',
                                   background: '#8AC5D2',
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
                                               height:'120px',
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
                                       height:'250px',
                                       background:'#8AC5D2',
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
                                       // style: {
                                       //          border:'2px solid red'
                                       //        },
                                       width: 280,
                                       id_grupo: 2,
                                       items: [],
                        }]
                      },{
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
                             id_grupo: 10,
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
                      },{
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
    crearStoreFormaPago : function () {

    	this.storeFormaPago = new Ext.data.JsonStore({
    	url: '../../sis_ventas_facturacion/control/Cajero/listarInstanciaPago',
			id: 'id_medio_pago',
			root: 'datos',
			sortInfo: {
				field: 'id_medio_pago',
				direction: 'ASC'
			},
			totalProperty: 'total',
			fields: [
             {name: 'id_moneda',     type: 'numeric'},
	           {name: 'id_medio_pago',type: 'numeric'},
	           {name: 'nombre',      type: 'string'},
             {name: 'codigo_tarjeta',     type: 'string'},
	           {name: 'numero_tarjeta',     type: 'string'},
	           {name: 'monto_transaccion',     type: 'numeric'},
             {name: 'id_venta_forma_pago',type: 'numeric'},

	        ]
		});

		this.storeFormaPago.baseParams.id_venta = this.Cmp.id_venta.getValue();
    this.storeFormaPago.load({params:{start:0,limit:50},
           callback : function (r) {
             var store = r;
                    if (r.length == 2) {
                      this.recuperarInstancias(store);
                    } else{
                      this.recuperarUnaInstancia(store);
                    }

            }, scope : this
        });
    },

    recuperarUnaInstancia:function(store){
      /*Aqui continuar Facturacion Ismael Valdivia*/
      this.Cmp.id_venta_forma_pago_1.setValue(store[0].data.id_venta_forma_pago);

      this.Cmp.id_moneda.store.load({params:{start:0,limit:50},
         callback : function (r) {
           this.Cmp.id_moneda.setValue(store[0].data.id_moneda);
           this.Cmp.id_moneda.fireEvent('select',this.Cmp.id_moneda, this.Cmp.id_moneda.store.getById(store[0].data.id_moneda));
           //this.obtenersuma();
          }, scope : this
      });

      this.Cmp.id_medio_pago.store.load({params:{start:0,limit:50},
         callback : function (r) {
           for (var i = 0; i < r.length; i++) {
             if (r[i].data.id_medio_pago_pw == store[0].data.id_medio_pago) {
               this.Cmp.id_medio_pago.setValue(r[i].data.id_medio_pago_pw);
               this.Cmp.id_medio_pago.fireEvent('select', this.Cmp.id_medio_pago_pw,r[i]);
             }
           }




           // this.Cmp.id_medio_pago.setValue(store[0].data.id_medio_pago);
           // this.Cmp.id_medio_pago.fireEvent('select',this.Cmp.id_medio_pago, this.Cmp.id_medio_pago.store.getById(store[0].data.id_medio_pago));
           //this.obtenersuma();
          }, scope : this
      });

        this.Cmp.monto_forma_pago.setValue(parseFloat(store[0].data.monto_transaccion));
        this.Cmp.codigo_tarjeta.setValue(parseFloat(store[0].data.codigo_tarjeta));
        this.Cmp.numero_tarjeta.setValue(parseFloat(store[0].data.numero_tarjeta));
        this.obtenersuma();
        //this.iniciarEventos();
        //this.recuperar_monto_automatico = 'NO';
        //this.ObtenerCondiciones();
    },

    recuperarInstancias:function(store){

      this.Cmp.id_venta_forma_pago_1.setValue(store[0].data.id_venta_forma_pago);

      this.Cmp.id_moneda.store.load({params:{start:0,limit:50},
         callback : function (r) {
           this.Cmp.id_moneda.setValue(store[0].data.id_moneda);
            this.Cmp.id_moneda.fireEvent('select',this.Cmp.id_moneda_2, this.Cmp.id_moneda_2.store.getById(store[0].data.id_moneda));
          }, scope : this
      });

      this.Cmp.id_medio_pago.store.load({params:{start:0,limit:50},
         callback : function (r) {
           for (var i = 0; i < r.length; i++) {
             if (r[i].data.id_medio_pago_pw == store[0].data.id_medio_pago) {
               this.Cmp.id_medio_pago.setValue(r[i].data.id_medio_pago_pw);
               this.Cmp.id_medio_pago.fireEvent('select', this.Cmp.id_medio_pago_pw,r[i]);
             }
           }
          }, scope : this
      });


      // this.Cmp.id_medio_pago.store.load({params:{start:0,limit:50},
      //    callback : function (r) {
      //      this.Cmp.id_medio_pago.setValue(store[0].data.id_medio_pago);
      //      this.Cmp.id_medio_pago.fireEvent('select',this.Cmp.id_medio_pago, this.Cmp.id_medio_pago.store.getById(store[0].data.id_medio_pago));
      //      //this.obtenersuma();
      //     }, scope : this
      // });

      this.Cmp.id_venta_forma_pago_2.setValue(store[1].data.id_venta_forma_pago);

      this.Cmp.id_moneda_2.store.load({params:{start:0,limit:50},
         callback : function (r) {
           this.Cmp.id_moneda_2.setValue(store[1].data.id_moneda);
            this.Cmp.id_moneda_2.fireEvent('select',this.Cmp.id_moneda_2, this.Cmp.id_moneda_2.store.getById(store[1].data.id_moneda));
          }, scope : this
      });

      this.Cmp.id_medio_pago_2.store.load({params:{start:0,limit:50},
         callback : function (r) {
           for (var i = 0; i < r.length; i++) {
             if (r[i].data.id_medio_pago_pw == store[1].data.id_medio_pago) {
               this.Cmp.id_medio_pago_2.setValue(r[i].data.id_medio_pago_pw);
               this.Cmp.id_medio_pago_2.fireEvent('select', this.Cmp.id_medio_pago_pw,r[i]);
             }
           }
          }, scope : this
      });

      // this.Cmp.id_medio_pago_2.store.load({params:{start:0,limit:50},
      //    callback : function (r) {
      //      this.Cmp.id_medio_pago_2.setValue(store[1].data.id_medio_pago);
      //       this.Cmp.id_medio_pago_2.fireEvent('select',this.Cmp.id_medio_pago_2, this.Cmp.id_medio_pago_2.store.getById(store[1].data.id_medio_pago));
      //     }, scope : this
      // });

        this.Cmp.monto_forma_pago.setValue(parseFloat(store[0].data.monto_transaccion));
        this.Cmp.monto_forma_pago_2.setValue(parseFloat(store[1].data.monto_transaccion));
        this.Cmp.codigo_tarjeta.setValue((store[0].data.codigo_tarjeta));
        this.Cmp.numero_tarjeta.setValue((store[0].data.numero_tarjeta));
        this.Cmp.codigo_tarjeta_2.setValue((store[1].data.codigo_tarjeta));
        this.Cmp.numero_tarjeta_2.setValue((store[1].data.numero_tarjeta));
        this.obtenersuma();
        //this.recuperar_monto_automatico = 'NO';
        //this.ObtenerCondiciones();
    },

    loadValoresIniciales:function()
    {
       Phx.vista.FormCorregirFacturas.superclass.loadValoresIniciales.call(this);
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
         console.log("llega aqui pasar estado",this.tipo_factura);
         if (objRes.ROOT.datos.estado == 'finalizado' && this.tipo_factura != 'manual') {
             this.id_venta = objRes.ROOT.datos.id_venta;
             this.imprimirNota();
         }
         Phx.CP.loadingHide();
         resp.argument.wizard.panel.destroy();
         //this.panel.destroy();
         this.reload();
      },

      imprimirNota: function(){
   		//Ext.Msg.confirm('Confirmación','¿Está seguro de Imprimir el Comprobante?',function(btn){
   				Phx.CP.loadingShow();
          console.log('condicionmes',this);
         if (this.data.objPadre.tipo_punto_venta == 'ato') {
   				Ext.Ajax.request({
   						url : '../../sis_ventas_facturacion/control/Cajero/reporteFactura',
   						params : {
                'id_venta' : this.id_venta ,
   							'id_punto_venta' : this.data.objPadre.variables_globales.id_punto_venta,
   							'formato_comprobante' : this.data.objPadre.variables_globales.formato_comprobante,
   							'tipo_factura': this.data.objPadre.tipo_factura
   						},
   						success : this.successExportHtml,
   						failure : this.conexionFailure,
   						timeout : this.timeout,
   						scope : this
   					});
        } else {

          Ext.Ajax.request({
   						url : '../../sis_ventas_facturacion/control/Cajero/reporteFactura',
   						params : {
                'id_venta' : this.data.datos_originales.data.id_venta ,
   							'id_punto_venta' : this.data.objPadre.variables_globales.id_punto_venta,
   							'formato_comprobante' : this.data.objPadre.variables_globales.formato_comprobante,
   							'tipo_factura': this.data.objPadre.tipo_factura
   						},
   						success : this.successExportHtml,
   						failure : this.conexionFailure,
   						timeout : this.timeout,
   						scope : this
   					});

        }


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
                    name: 'id_venta_forma_pago_1'
            },
            type:'Field',
            form:true
        },
        {
            config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_venta_forma_pago_2'
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
				allowBlank: false,
        listeners: {
          afterrender: function(field) {
            field.focus(false);
          }
        },
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
          textTransform:'uppercase',
        },
        width:200,
				allowBlank : false,
				emptyText : 'Cliente...',
				store : new Ext.data.JsonStore({
					url : '../../sis_ventas_facturacion/control/Cliente/listarCliente',
					id : 'id_cliente',
					root : 'datos',
					sortInfo : {
            field : 'id_cliente',
						direction : 'DESC'
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
        listWidth:'450',
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
                baseParams: {filtrar: 'si',par_filtro: 'moneda.id_moneda#moneda.moneda#moneda.codigo#moneda.codigo_internacional'}
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
            minChars: 2
        },
        type: 'ComboBox',
        id_grupo: 2,
        form: true
    },
    /************************Aumentando instancia de pago*****************************************/
    {
        config: {
            name: 'id_medio_pago',
            fieldLabel: '<img src="../../../lib/imagenes/facturacion/TarjetaCredito.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Medio de pago</span>',
            allowBlank: false,
            width:150,
            id: 'testeoColor',
            emptyText: 'Medio de pago...',
            store: new Ext.data.JsonStore({
                url: '../../sis_obingresos/control/MedioPagoPw/listarMedioPagoPw',
                id: 'id_medio_pago',
                root: 'datos',
                sortInfo: {
                    field: 'name',
                    direction: 'ASC'
                },
                totalProperty: 'total',
                fields: ['id_medio_pago_pw', 'name', 'fop_code'],
                remoteSort: true,
                baseParams: {par_filtro: 'mppw.name#fp.fop_code', emision:'FACTCOMP'}
            }),
            valueField: 'id_medio_pago_pw',
            displayField: 'name',
            gdisplayField: 'name',
            hiddenName: 'id_medio_pago_pw',
            tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Medio de Pago: <font color="Blue">{name}</font></b></p><b><p>Codigo: <font color="red">{fop_code}</font></b></p></div></tpl>',
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
                return String.format('{0}', record.data['codigo_fp']);
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
        config:{
            name: 'monto_forma_pago',
            fieldLabel: 'Importe Recibido',
            allowBlank: false,
            width:150,
            maxLength:20,
            allowNegative:false,
            value:0,

        },
            type:'NumberField',
            id_grupo:2,
            form:true,
            valorInicial:'0'
    },
    // {
    //                 config: {
    //                     name: 'codigo_control',
    //                     fieldLabel: 'Código de Control',
    //                     allowBlank: true,
    //                     anchor: '85%',
    //                     gwidth: 100,
    //                     enableKeyEvents: true,
    //                     //fieldStyle: 'text-transform: uppercase',
    //                     //maxLength: 200,
    //                 },
    //                 type: 'TextField',
    //                 id_grupo: 2,
    //                 form: true
    //             },
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
                    baseParams: {par_filtro: 'moneda.codigo#moneda.codigo_internacional', filtrar: 'si'}
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
                name: 'id_medio_pago_2',
                fieldLabel: '<img src="../../../lib/imagenes/facturacion/TarjetaCredito.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Medio de pago</span>',
                allowBlank: false,
                width:150,
                emptyText: 'Medio de pago...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_obingresos/control/MedioPagoPw/listarMedioPagoPw',
                    id: 'id_medio_pago',
                    root: 'datos',
                    sortInfo: {
                        field: 'name',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_medio_pago_pw', 'name', 'fop_code'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'mppw.name#fp.fop_code', emision:'FACTCOMP'}
                }),
                valueField: 'id_medio_pago_pw',
                displayField: 'name',
                gdisplayField: 'name',
                hiddenName: 'id_medio_pago_pw',
                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Medio de Pago: <font color="Blue">{name}</font></b></p><b><p>Codigo: <font color="red">{fop_code}</font></b></p></div></tpl>',
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
                    return String.format('{0}', record.data['codigo_fp']);
                }
            },
            type: 'ComboBox',
            id_grupo: 10,
            grid: true,
            form: true
        },
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
            },
                type:'TextField',
                id_grupo:10,
                form:true,
        },
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
        this.mestore.baseParams.id_venta = this.Cmp.id_venta.getValue();
        /*Comentando para incluir InstanciaPago*/
        //this.Cmp.id_forma_pago.store.baseParams.defecto = 'si';
        this.Cmp.id_medio_pago.store.baseParams.defecto = 'no';
        this.Cmp.id_moneda.store.baseParams.filtrar_base = 'no';
        this.mestore.load();
        //this.crearStoreFormaPago();
        //this.obtenersuma();
      //  this.Cmp.id_forma_pago.reset();


    },
    onNew: function(){
    	this.accionFormulario = 'NEW';
	},

    onSubmit: function(o) {
        //  validar formularios
        console.log("que es esto",this);
        var arra = [], i, me = this;
        var formapa = [];
        for (i = 0; i < me.megrid.store.getCount(); i++) {
            var record = me.megrid.store.getAt(i);
            arra[i] = record.data;
        }
      //   if (me.storeFormaPago) {
      //     console.log("llega aqui",me.storeFormaPago.getCount());
	    //     for (i = 0; i < me.storeFormaPago.getCount(); i++) {
	    //         var record = me.storeFormaPago.getAt(i);
	    //         formapa[i] = record.data;
	    //     }
	    // }
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
             Phx.vista.FormCorregirFacturas.superclass.onSubmit.call(this,o);
        }
        else{
            alert('La venta no tiene registrado ningun detalle');
        }
    },

    successSave:function(resp)
    {
    	var datos_respuesta = JSON.parse(resp.responseText);
    	Phx.CP.loadingHide();
      //this.panel.close();

      console.log("llega aqui respuesta cambio",datos_respuesta);
      if ('cambio' in datos_respuesta.ROOT.datos) {
        Ext.Msg.show({
         title:'<center><h1 style="font-size:20px; color:#0E00B7; text-shadow: -1px -1px 1px rgba(255,255,255,.1), 1px 1px 1px rgba(0,0,0,.5);"><img src="../../../lib/imagenes/obligacion_pago.png" height="30px" style="float:center; vertical-align: middle;"> Cambio</h1></center>',
         msg: '<center><b style="font-size:15px;">Debe devolver ' + datos_respuesta.ROOT.datos.cambio + ' al cliente</b></center>',
         width:290,
         height:100,
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
