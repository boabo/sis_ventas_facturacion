<?php
/**
*@package pXP
*@file    FormCajero.php
*@author  Ismael Valdivia Aranibar
*@date    11/04/2019
*@description permites subir archivos a la tabla de documento_sol
*/
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.FormCajero=Ext.extend(Phx.frmInterfaz,{
    ActSave:'../../sis_ventas_facturacion/control/VentaFacturacion/insertarVentaCompleta',
    tam_pag: 10,
    layout: 'fit',
    tabEnter: true,
    autoScroll: false,
    breset: true,
    bsubmit:true,
    storeFormaPago : false,
    fwidth : '9%',
    cantidadAllowDecimals: false,
    formUrl: '../../../sis_ventas_facturacion/vista/venta/FormVariasFormasPago.php',
    formClass : 'FormVariasFormasPago',
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
                  //disable:true,
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
	                disabled:true,
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
                      name: 'habilitar_edicion',
                      fieldLabel: '<img src="../../../lib/imagenes/facturacion/LapizPapel.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Editar?</span>',
                      allowBlank: true,
                      width:200,
                      emptyText:'Editar...',
                      triggerAction: 'all',
                      lazyRender:true,
                      mode: 'local',
                      displayField: 'text',
                      valueField: 'value',
                      store:new Ext.data.SimpleStore({
      					data : [['SI', 'SI'], ['NO', 'NO']],
      					id : 'value',
      					fields : ['value', 'text']
      				})
                  },
                  type:'ComboBox',
                  id_grupo:1,
                  form:true
              },
          {
            config:{
              name: 'observaciones',
              fieldLabel: '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Observaciones</span>',
              allowBlank: false,
              width:200,
              // minLength:12,
              style:'text-transform:uppercase;'
            },
              type:'TextArea',
              id_grupo:22,
              form:true
          },
          {
            config:{
              name: 'boleto_asociado',
              fieldLabel: '<img src="../../../lib/imagenes/facturacion/ticket.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Boleto Asociado</span>',
              allowBlank: true,
              width:200,
              hidden:true,
              minLength:13,
              maxLength:13,
              //style:'text-transform:uppercase;'
            },
              type:'TextField',
              id_grupo:22,
              form:true
          },
      /*Aumentnado para formato de impresion*/
      // {
      //    config : {
      //      name : 'formato_factura',
      //      fieldLabel : 'Formato Factura',
      //      width:200,
      //      allowBlank : true,
      //      emptyText : 'Formato Factura...',
      //      store : new Ext.data.JsonStore({
      //        url : '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
      //        id : 'id_catalogo',
      //        root : 'datos',
      //        sortInfo : {
      //          field : 'codigo',
      //          direction : 'ASC'
      //        },
      //        totalProperty : 'total',
      //        fields: ['codigo','descripcion'],
      //        remoteSort : true,
      //        baseParams:{
      //          par_filtro: 'cat.descripcion',
      //         cod_subsistema:'VEF',
      //         catalogo_tipo:'tventa_formato_factura'
      //       },
      //      }),
      //      valueField : 'descripcion',
      //      displayField : 'descripcion',
      //      gdisplayField : 'formato_factura',
      //      hiddenName : 'formato_factura',
      //      forceSelection : true,
      //      typeAhead : false,
      //      tpl: new Ext.XTemplate([
      //          '<tpl for=".">',
      //          '<div class="x-combo-list-item">',
      //          '<div>',
      //          '<p><b>Codigo: <span style="color: red;">{codigo}</span></b></p>',
      //          '</div><p><b>Descripción: <span style="color: blue;">{descripcion}</span></b></p>',
      //          '</div></tpl>'
      //      ]),
      //      triggerAction : 'all',
      //      lazyRender : true,
      //      mode : 'remote',
      //      pageSize : 25,
      //      listWidth:'450',
      //      maxHeight : 450,
      //      queryDelay : 1000,
      //      gwidth : 170,
      //      minChars : 2,
      //      resizable:true,
      //      enableMultiSelect: false
      //    },
      //    type : 'ComboBox',
      //    id_grupo : 22,
      //    grid: true,
      //    form: true
      //  },
       /*Aumentando para enviar por correo o no*/
       // {
       //         config:{
       //             name: 'enviar_correo',
       //             fieldLabel: 'Enviar Correo',
       //             allowBlank: true,
       //             width:200,
       //             emptyText:'Enviar correo...',
       //             triggerAction: 'all',
       //             lazyRender:true,
       //             mode: 'local',
       //             displayField: 'text',
       //             valueField: 'value',
       //             store:new Ext.data.SimpleStore({
       //       data : [['SI', 'SI'], ['NO', 'NO']],
       //       id : 'value',
       //       fields : ['value', 'text']
       //     })
       //         },
       //         type:'ComboBox',
       //         id_grupo:22,
       //         form:true
       //     },
           {
             config:{
               name: 'correo_electronico',
               fieldLabel: '<img src="../../../lib/imagenes/facturacion/nuevoMail.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Email</span>',
               allowBlank: true,
               //hidden:true,
               width:200,
               vtype:'email',
               //maxLength:30,
             },
               type:'TextField',
               id_grupo:1,
               form:true
           },);

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
		                fieldLabel: '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Exento</span>',
		                allowBlank: false,
                    disabled:true,
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

          Ext.Ajax.request({
              url:'../../sis_ventas_facturacion/control/Cajero/getConceptoAsociar',
              params:{id_venta:this.data.datos_originales.data.id_venta},
              success: function(resp){
                  var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                  this.asociar_boleto = reg.ROOT.datos.v_asociado;
                  this.requiere_excento = reg.ROOT.datos.v_requiere_excento;

                  if (this.asociar_boleto == 'si') {
                    this.mostrarComponente(this.Cmp.boleto_asociado);
                  }

                  if (this.requiere_excento == 'si') {
                    this.mostrarComponente(this.Cmp.excento);
                  }


              },
              failure: this.conexionFailure,
              timeout:this.timeout,
              scope:this
          });

          this.buildDetailGridEdit();

        } else {
          this.buildDetailGridNew();
        }

        this.buildGrupos();

        this.labelReset = '<div><span style="vertical-align: middle; font-size:25px; font-weight:bold; color:#1479B8; text-shadow: 3px 0px 0px #000000;">GENERAR</span></div>';
        this.labelSubmit = '<div><span style="vertical-align: middle; font-size:25px; font-weight:bold; color:#1479B8; text-shadow: 3px 0px 0px #000000;">VARIAS FP</span></div>';
        Phx.vista.FormCajero.superclass.constructor.call(this,config);
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
                                            fieldLabel: '<img src="../../../lib/imagenes/facturacion/BolsaCompraColores.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Producto/Servicio</span>',
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
                                                fields: ['id_concepto_ingas', 'tipo','desc_ingas','requiere_descripcion','precio','excento','contabilizable','boleto_asociado','nombre_actividad'],
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
                                        				'<p><b>Actividad Económica:</b><span style="color: green; font-weight:bold;"> {nombre_actividad}</span></p></p>',
                                        				'<p><b>Descripcion:</b> <span style="color: blue; font-weight:bold;">{desc_ingas}</span></p>',
                                        				'<p><b>Precio:</b> <span style="color: blue; font-weight:bold;">{precio}</span></p>',
                                        				'<p><b>Tiene Exento:</b> <span style="color: red; font-weight:bold;">{desc_ingas}</span></p>',
                                                '<p><b>Requiere Descripción:</b> <span style="color: red; font-weight:bold;">{desc_ingas}</span></p>',
                                                '<p><b>Contabilizable:</b> <span style="color: red; font-weight:bold;">{contabilizable}</span></p>',
                                        				'<p><b>Asociar:</b> <span style="color: red; font-weight:bold;">{boleto_asociado}</span></p>',
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
                                            listWidth:'600',
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
                                        fieldLabel: '<img src="../../../lib/imagenes/facturacion/Cantidad.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Cantidad</span>',
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
    	this.detCmp.id_producto.on('select',function(c,r,i) {
            this.mestore.data.items[0].data.nombre_producto = r.data.nombre_producto;

        	if (r.data.requiere_descripcion == 'si') {
        		this.habilitarDescripcion(true);
        	} else {
        		this.habilitarDescripcion(false);
        	}

        },this);

    },

    /*Funcion para poner las condiciones en las regionales*/
    condicionesRegionales: function (){

      /*Aumentanod para filtrar las instancias de pago (Ismael Valdivia 16/10/2020)*/
      this.Cmp.id_formula.store.baseParams.regional = this.data.objPadre.variables_globales.ESTACION_inicio;
      this.Cmp.id_medio_pago.store.baseParams.regional = this.data.objPadre.variables_globales.ESTACION_inicio;
      this.Cmp.id_medio_pago_2.store.baseParams.regional = this.data.objPadre.variables_globales.ESTACION_inicio;
      /****************************************************************************/


      // if (this.data.objPadre.variables_globales.codigo_moneda_base == 'USD') {
      //   this.mostrarComponente(this.Cmp.correo_electronico);
      //   this.mostrarComponente(this.Cmp.id_formula);
      //   this.ocultarComponente(this.Cmp.cambio_moneda_extranjera);
      //   this.Cmp.cambio_moneda_extranjera.allowBlank = true;
      //
      //   Ext.getCmp('cambioM/E').hide();
      //   /*Aqui mostramos el boton para el pago electronico*/
      //   Ext.getCmp('botonPagoElectronico').hide();
      //   /**************************************************/
      //
      // } else {
      //   this.ocultarComponente(this.Cmp.correo_electronico);
      //   this.mostrarComponente(this.Cmp.cambio_moneda_extranjera);
      //   this.Cmp.cambio_moneda_extranjera.allowBlank = true;
      //   Ext.getCmp('cambioM/E').show();
      //   /*Aqui ocultamos el boton para el pago electronico*/
      //   Ext.getCmp('botonPagoElectronico').hide();
      //   /**************************************************/
      // }

    },
    /******************************************************/




    iniciarEventos : function () {
      this.ocultarComponente(this.Cmp.codigo_tarjeta);
      this.ocultarComponente(this.Cmp.tipo_tarjeta);
      this.ocultarComponente(this.Cmp.numero_tarjeta);
      this.ocultarComponente(this.Cmp.id_auxiliar);
      this.ocultarComponente(this.Cmp.mco);

      this.ocultarComponente(this.Cmp.mco_2);
      this.ocultarComponente(this.Cmp.numero_tarjeta_2);
      this.ocultarComponente(this.Cmp.codigo_tarjeta_2);
      this.ocultarComponente(this.Cmp.id_auxiliar_2);
      this.ocultarComponente(this.Cmp.tipo_tarjeta_2);

      this.form.body.dom[43].style.display='none'
      this.form.body.dom[31].style.display='none'
        /**********************************************************************************/
        /*Aumentando para poner Condiciones en Regionales (Ismael Valdivia 14/10/2020)*/
        this.condicionesRegionales();
        this.Cmp.id_formula.store.baseParams.tipo_pv = this.data.objPadre.tipo_punto_venta;
        /*****************************************************************************/

        /*Aqui aumentamos para que muestre el campo de correo electronico*/
        if (this.data.objPadre.variables_globales.enviar_correo == 'no' || this.data.objPadre.variables_globales.enviar_correo == '' || this.data.objPadre.variables_globales.enviar_correo == null) {
          this.ocultarComponente(this.Cmp.correo_electronico);
          this.Cmp.correo_electronico.allowBlank = true;
          this.Cmp.correo_electronico.reset();
        } else if (this.data.objPadre.variables_globales.enviar_correo == 'si') {
          this.mostrarComponente(this.Cmp.correo_electronico);
          this.Cmp.correo_electronico.allowBlank = false;
        }
        /***************************************************************/


        this.Cmp.cambio.setValue(0);
        this.Cmp.cambio_moneda_extranjera.setValue(0);
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
          if (this.data.tipo_form == 'new'){
          this.megrid.topToolbar.items.items[0].container.dom.style.width="80px";
          this.megrid.topToolbar.items.items[0].container.dom.style.height="35px";
          this.megrid.topToolbar.items.items[0].btnEl.dom.style.height="35px";

          /*Aumentando para el hover Ismael Valdivia (13/11/2020)*/

          Ext.getCmp('botonGuardar').el.dom.onmouseover = function () {
            Ext.getCmp('botonGuardar').btnEl.dom.style.background = '#00B2FA';
          };

          Ext.getCmp('botonGuardar').el.dom.onmouseout = function () {
            Ext.getCmp('botonGuardar').btnEl.dom.style.background = '';
          };

          /*******************************************************/

          /******************Cambiaremos el estilo del boton agregar detalle************************/
          this.megrid.topToolbar.items.items[1].container.dom.style.width="80px";
          this.megrid.topToolbar.items.items[1].container.dom.style.height="35px";
          this.megrid.topToolbar.items.items[1].btnEl.dom.style.height="35px";

          /*Aumentando para el hover Ismael Valdivia (13/11/2020)*/

          Ext.getCmp('botonAgregar').el.dom.onmouseover = function () {
            Ext.getCmp('botonAgregar').btnEl.dom.style.background = '#5CE100';
          };

          Ext.getCmp('botonAgregar').el.dom.onmouseout = function () {
            Ext.getCmp('botonAgregar').btnEl.dom.style.background = '';
          };

          /*******************************************************/



          this.megrid.topToolbar.items.items[2].container.dom.style.width="75px";
          this.megrid.topToolbar.items.items[2].container.dom.style.height="35px";
          this.megrid.topToolbar.items.items[2].btnEl.dom.style.height="35px";

          /*Aumentando para el hover Ismael Valdivia (13/11/2020)*/

          Ext.getCmp('botonEliminar').el.dom.onmouseover = function () {
            Ext.getCmp('botonEliminar').btnEl.dom.style.background = 'rgba(255, 0, 0, 0.5)';
          };

          Ext.getCmp('botonEliminar').el.dom.onmouseout = function () {
            Ext.getCmp('botonEliminar').btnEl.dom.style.background = '';
          };

          /*******************************************************/



          // this.megrid.topToolbar.items.items[1].setDisabled(false);

          this.megrid.topToolbar.el.dom.style.background="#3AC2B6";
          this.megrid.topToolbar.el.dom.style.height="45px";
          this.megrid.topToolbar.el.dom.style.borderRadius="2px";
          this.megrid.body.dom.childNodes[0].firstChild.children[0].firstChild.style.background='#FFF4EB';

        } else {

          this.megrid.topToolbar.items.items[0].container.dom.style.width="80px";
          this.megrid.topToolbar.items.items[0].container.dom.style.height="35px";
          this.megrid.topToolbar.items.items[0].btnEl.dom.style.height="35px";

          /******************Cambiaremos el estilo del boton agregar detalle************************/
          this.megrid.topToolbar.items.items[1].container.dom.style.width="80px";
          this.megrid.topToolbar.items.items[1].container.dom.style.height="35px";
          this.megrid.topToolbar.items.items[1].btnEl.dom.style.height="35px";

          this.megrid.topToolbar.items.items[2].container.dom.style.width="80px";
          this.megrid.topToolbar.items.items[2].container.dom.style.height="35px";
          this.megrid.topToolbar.items.items[2].btnEl.dom.style.height="35px";


          this.megrid.topToolbar.el.dom.style.background="#3AC2B6";
          this.megrid.topToolbar.el.dom.style.height="45px";
          this.megrid.topToolbar.el.dom.style.borderRadius="2px";
          this.megrid.body.dom.childNodes[0].firstChild.children[0].firstChild.style.background='#FFF4EB';

        }
          //this.arrayBotones[0].scope.form.buttons[0].container.dom.style.border="2px solid red";
          this.arrayBotones[0].scope.form.buttons[0].container.dom.style.width="20px";
          //this.arrayBotones[0].scope.form.buttons[0].btnEl.dom.style.border="2px solid blue";
          this.arrayBotones[0].scope.form.buttons[0].btnEl.dom.style.width="190px";
          this.arrayBotones[0].scope.form.buttons[0].btnEl.dom.style.height="50px";

          this.arrayBotones[1].scope.form.buttons[1].container.dom.style.width="20px";
          //this.arrayBotones[0].scope.form.buttons[0].btnEl.dom.style.border="2px solid blue";
          this.arrayBotones[1].scope.form.buttons[1].btnEl.dom.style.width="190px";
          this.arrayBotones[1].scope.form.buttons[1].btnEl.dom.style.height="50px";

          this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[6].childNodes[0].style.color="#7400FF";
          this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[6].childNodes[0].style.fontWeight="bold";
          this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[6].childNodes[0].style.fontSize="15px";
          /*irva*/
          //this.arrayBotones[0].scope.form.buttons[0].btnEl.dom.style.height="60px";
          //this.megrid.topToolbar.items.items[0].btnEl.dom.style.border="2px solid blue";
          //this.megrid.topToolbar.items.items[0].el.dom.style.border="2px solid green";
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
                  //Comentado para agregar InstanciaPago
                  //this.Cmp.id_forma_pago.store.baseParams.defecto = 'si';

                  /*Aumentando para que nos filtre siempre efectivo (CASH)*/
                  this.Cmp.id_medio_pago.store.baseParams.defecto = 'si';
                	this.Cmp.id_moneda.store.baseParams.filtrar_base = 'si';
                  /*********************************************************/
               }
                this.cargarFormaPago();

            },this);
        }


        /*Comentado para agregar instancia de pago*/
      /*  this.Cmp.id_forma_pago.on('select',function(c,r,i) {
          // console.log("la tarjeta llega aqui",r.data);
          // console.log("tbar cambiaremos",this.megrid);
          // console.log("tbar cambiaremos",this.megrid.tbar);
          //this.megrid.tbar.dom.style.border = "2px solid red";
            	if (r.data.registrar_tarjeta == 'si' || r.data.registrar_cc == 'si') {
              //this.Cmp.cambio_moneda_extranjera.setValue
            	this.mostrarComponente(this.Cmp.numero_tarjeta);
            	this.Cmp.numero_tarjeta.allowBlank = false;
            	if (r.data.registrar_tarjeta == 'si') {
					      this.mostrarComponente(this.Cmp.numero_tarjeta);
	            	this.mostrarComponente(this.Cmp.codigo_tarjeta);
	            	this.mostrarComponente(this.Cmp.tipo_tarjeta);
					      this.ocultarComponente(this.Cmp.id_auxiliar);
                this.ocultarComponente(this.Cmp.mco);
					      this.Cmp.codigo_tarjeta.allowBlank = false;
                this.Cmp.tipo_tarjeta.allowBlank = false;
	            	this.Cmp.mco.allowBlank = true;
	            } else {
	            	this.Cmp.codigo_tarjeta.allowBlank = true;
                this.Cmp.mco.allowBlank = true;
            		this.Cmp.tipo_tarjeta.allowBlank = true;
                this.ocultarComponente(this.Cmp.numero_tarjeta);
					      this.ocultarComponente(this.Cmp.numero_tarjeta_2);
            		this.ocultarComponente(this.Cmp.codigo_tarjeta);
            		this.ocultarComponente(this.Cmp.tipo_tarjeta);
					      this.mostrarComponente(this.Cmp.id_auxiliar);
            		this.Cmp.codigo_tarjeta.reset();
            		this.Cmp.tipo_tarjeta.reset();
	            }
            } else {
            	this.ocultarComponente(this.Cmp.numero_tarjeta);
            	this.ocultarComponente(this.Cmp.codigo_tarjeta);
            	this.ocultarComponente(this.Cmp.tipo_tarjeta);
				      this.ocultarComponente(this.Cmp.id_auxiliar);
            	this.Cmp.numero_tarjeta.allowBlank = true;
            	this.Cmp.codigo_tarjeta.allowBlank = true;
              this.Cmp.mco.allowBlank = true;
            	this.Cmp.tipo_tarjeta.allowBlank = true;
            	this.Cmp.numero_tarjeta.reset();
            	this.Cmp.codigo_tarjeta.reset();
            	this.Cmp.tipo_tarjeta.reset();
            }
            if (r.data.nombre == 'MISCELANEOUS CHARGER ORDER BOB' || r.data.nombre == 'MISCELANEOUS CHARGER ORDER USD') {
                this.mostrarComponente(this.Cmp.mco);
                this.Cmp.numero_tarjeta.allowBlank = true;
              	this.Cmp.codigo_tarjeta.allowBlank = true;
              	this.Cmp.tipo_tarjeta.allowBlank = true;
                this.Cmp.mco.allowBlank = false;
            } else {
                this.ocultarComponente(this.Cmp.mco);
            }

            if (r.data.codigo == 'CCVI') {
               //console.log("llega aqui el tipo de tarjeta",this);
               this.Cmp.tipo_tarjeta.setValue('VI');
               this.Cmp.tipo_tarjeta.fireEvent('select', this.Cmp.tipo_tarjeta,'VI',0);
            } else if (r.data.codigo == 'CCAX') {
                 //console.log("llega aqui el tipo de tarjeta",this);
                 this.Cmp.tipo_tarjeta.setValue('AX');
                 this.Cmp.tipo_tarjeta.fireEvent('select', this.Cmp.tipo_tarjeta,'AX',0);
              } else if (r.data.codigo == 'CCCA') {
                //   console.log("llega aqui el tipo de tarjeta",this);
                   this.Cmp.tipo_tarjeta.setValue('CA');
                   this.Cmp.tipo_tarjeta.fireEvent('select', this.Cmp.tipo_tarjeta,'CA',0);
                } else {
                this.Cmp.tipo_tarjeta.reset();
              }



            this.moneda = r.data.desc_moneda;
            this.Cmp.moneda_tarjeta.setValue(this.moneda);
            //console.log("aqui recuperar codigo moneda",this.moneda);
        },this);*/

        /****************************Aumnetando la instancia de pago********************************/
        this.Cmp.id_medio_pago.on('select',function(c,r,i) {

          // if(r){
            // if (r.data) {
              var codigo_forma_pago = r.data.fop_code;
              this.Cmp.tipo_tarjeta.setValue(r.data.name);
            // }
          // }

          //var codigo_forma_pago = r.data.codigo_forma_pago.substr(0,2);

          //this.Cmp.tipo_tarjeta.setValue(r.data.nombre);
          if (codigo_forma_pago != undefined && codigo_forma_pago != '' && codigo_forma_pago != null) {

          if (codigo_forma_pago.startsWith("CC")) {
            this.form.body.dom[31].style.display='block'
            this.mostrarComponente(this.Cmp.codigo_tarjeta);
            //this.mostrarComponente(this.Cmp.tipo_tarjeta);
          	this.mostrarComponente(this.Cmp.numero_tarjeta);
			      this.ocultarComponente(this.Cmp.id_auxiliar);
            this.ocultarComponente(this.Cmp.mco);
            this.Cmp.tipo_tarjeta.setValue(r.data.nombre);
			      this.Cmp.codigo_tarjeta.allowBlank = false;
            this.Cmp.tipo_tarjeta.allowBlank = false;
          	this.Cmp.mco.allowBlank = true;
          } else if (codigo_forma_pago.startsWith("MCO")) {
            this.form.body.dom[31].style.display='block'
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
            this.form.body.dom[31].style.display='block'
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
            this.form.body.dom[31].style.display='none'
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
            this.form.body.dom[43].style.display='block'
            this.mostrarComponente(this.Cmp.codigo_tarjeta_2);
            //this.mostrarComponente(this.Cmp.tipo_tarjeta_2);
          	this.mostrarComponente(this.Cmp.numero_tarjeta_2);
			      this.ocultarComponente(this.Cmp.id_auxiliar_2);
            this.ocultarComponente(this.Cmp.mco_2);
            this.Cmp.tipo_tarjeta_2.setValue(r.data.nombre);
			      this.Cmp.codigo_tarjeta_2.allowBlank = false;
            this.Cmp.tipo_tarjeta_2.allowBlank = false;
          	this.Cmp.mco_2.allowBlank = true;
          } else if (codigo_forma_pago.startsWith("MCO")) {
            this.form.body.dom[43].style.display='block'
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
            this.form.body.dom[43].style.display='block'
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
            this.form.body.dom[43].style.display='none'
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
             this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue())/this.tipo_cambio);

          }else if (this.Cmp.id_moneda.value == 2 && this.Cmp.id_moneda_2.value == 2) {
            this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)/this.tipo_cambio);

          }else if (this.Cmp.id_moneda.value == 2 && this.Cmp.id_moneda_2.value != 2) {
            this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio));
          }
          else{
              this.Cmp.monto_forma_pago_2.setValue(this.suma_total-this.Cmp.monto_forma_pago.getValue());
          }


        },this);


        /************************************Fin agregar instancia de pago***************************************/

          /*Comentando para aumentar el id_instancia_pago_2*/
        /*this.Cmp.id_forma_pago_2.on('select',function(c,r,i) {
          if (r.data.codigo == 'CCVI') {
             //console.log("llega aqui el tipo de tarjeta",this);
             this.Cmp.tipo_tarjeta_2.setValue('VI');
             this.Cmp.tipo_tarjeta_2.fireEvent('select', this.Cmp.tipo_tarjeta_2,'VI',0);
          } else if (r.data.codigo == 'CCAX') {
              // console.log("llega aqui el tipo de tarjeta",this);
               this.Cmp.tipo_tarjeta_2.setValue('AX');
               this.Cmp.tipo_tarjeta_2.fireEvent('select', this.Cmp.tipo_tarjeta_2,'AX',0);
            }else if (r.data.codigo == 'CCCA') {
                // console.log("llega aqui el tipo de tarjeta",this);
                 this.Cmp.tipo_tarjeta_2.setValue('CA');
                 this.Cmp.tipo_tarjeta_2.fireEvent('select', this.Cmp.tipo_tarjeta_2,'CA',0);
              } else {
              this.Cmp.tipo_tarjeta_2.reset();
            }

          if (r.data.registrar_tarjeta == 'si' || r.data.registrar_cc == 'si') {
          this.mostrarComponente(this.Cmp.numero_tarjeta_2);
          this.Cmp.numero_tarjeta_2.allowBlank = false;
          if (r.data.registrar_tarjeta == 'si') {
            this.mostrarComponente(this.Cmp.numero_tarjeta_2);
            this.mostrarComponente(this.Cmp.codigo_tarjeta_2);
            this.mostrarComponente(this.Cmp.tipo_tarjeta_2);
            this.ocultarComponente(this.Cmp.id_auxiliar_2);
            this.ocultarComponente(this.Cmp.mco_2);
            this.Cmp.codigo_tarjeta_2.allowBlank = false;
            this.Cmp.tipo_tarjeta_2.allowBlank = false;
            this.Cmp.mco_2.allowBlank = true;
          } else {
            this.Cmp.codigo_tarjeta_2.allowBlank = true;
            this.Cmp.mco_2.allowBlank = true;
            this.Cmp.tipo_tarjeta_2.allowBlank = true;
            this.ocultarComponente(this.Cmp.numero_tarjeta_2);
            this.ocultarComponente(this.Cmp.codigo_tarjeta_2);
            this.ocultarComponente(this.Cmp.tipo_tarjeta_2);
            this.mostrarComponente(this.Cmp.id_auxiliar_2);
            this.Cmp.codigo_tarjeta_2.reset();
            this.Cmp.tipo_tarjeta_2.reset();
          }
        } else {
          this.ocultarComponente(this.Cmp.numero_tarjeta_2);
          this.ocultarComponente(this.Cmp.codigo_tarjeta_2);
          this.ocultarComponente(this.Cmp.tipo_tarjeta_2);
          this.ocultarComponente(this.Cmp.id_auxiliar_2);
          this.Cmp.numero_tarjeta_2.allowBlank = true;
          this.Cmp.codigo_tarjeta_2.allowBlank = true;
          this.Cmp.mco_2.allowBlank = true;
          this.Cmp.tipo_tarjeta.allowBlank = true;
          this.Cmp.numero_tarjeta_2.reset();
          this.Cmp.codigo_tarjeta_2.reset();
          this.Cmp.tipo_tarjeta.reset();
        }
        if (r.data.nombre == 'MISCELANEOUS CHARGER ORDER BOB' || r.data.nombre == 'MISCELANEOUS CHARGER ORDER USD') {
            this.mostrarComponente(this.Cmp.mco_2);
            this.Cmp.numero_tarjeta_2.allowBlank = true;
            this.Cmp.codigo_tarjeta_2.allowBlank = true;
            this.Cmp.tipo_tarjeta.allowBlank = true;
            this.Cmp.mco_2.allowBlank = false;
        } else {
            this.ocultarComponente(this.Cmp.mco_2);
        }
        this.moneda_2 = r.data.desc_moneda;
        this.Cmp.moneda_tarjeta_2.setValue(this.moneda_2);

        // console.log("llega los parametros aqui",this.Cmp.moneda_tarjeta_2.value);
        // console.log("llega el tipo de cambio aqui",this.tipo_cambio);
         if (this.Cmp.moneda_tarjeta.value != 'USD' && this.Cmp.moneda_tarjeta_2.value == 'USD' ) {
            this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue())/this.tipo_cambio);

         }else if (this.Cmp.moneda_tarjeta.value == 'USD' && this.Cmp.moneda_tarjeta_2.value == 'USD') {
           this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)/this.tipo_cambio);

         }else if (this.Cmp.moneda_tarjeta.value == 'USD' && this.Cmp.moneda_tarjeta_2.value != 'USD') {
           this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio));
         }
         else{
             this.Cmp.monto_forma_pago_2.setValue(this.suma_total-this.Cmp.monto_forma_pago.getValue());
         }

       },this);*/
        /**************************************************************************************************************/
        /********************************Aumemtando condicios para el id moneda****************************************/
        this.Cmp.id_moneda.on('select',function(c,r,i) {
          if(r.data.id_moneda == 2){
              //console.log("llega el dolar");
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
            this.Cmp.monto_forma_pago_2.setValue((this.suma_total-(this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio))/this.tipo_cambio);

            /*Aqui para que moneda esta por defecto*/
            this.Cmp.id_moneda_2.store.load({params:{start:0,limit:50},
                   callback : function (r) {
                     for (var i = 0; i < r.length; i++) {
                       if (r[i].data.id_moneda == this.data.objPadre.variables_globales.id_moneda_base) {
                         this.Cmp.id_moneda_2.setValue(r[i].data.id_moneda);
                         this.Cmp.id_moneda_2.fireEvent('select', this.Cmp.id_moneda_2,this.Cmp.id_moneda_2.store.getById(r[i].data.id_moneda));

                       }
                     }
                    }, scope : this
                });
            /***************************************/


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
            this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()));

            /*Aqui para que moneda esta por defecto*/
            this.Cmp.id_moneda_2.store.load({params:{start:0,limit:50},
                   callback : function (r) {
                     for (var i = 0; i < r.length; i++) {
                       if (r[i].data.id_moneda == this.data.objPadre.variables_globales.id_moneda_base) {
                         this.Cmp.id_moneda_2.setValue(r[i].data.id_moneda);
                         this.Cmp.id_moneda_2.fireEvent('select', this.Cmp.id_moneda_2,this.Cmp.id_moneda_2.store.getById(r[i].data.id_moneda));

                       }
                     }
                    }, scope : this
                });
            /***************************************/

          } else{
            //this.Cmp.id_forma_pago_2.disable();
            this.Cmp.id_moneda_2.disable();
            this.Cmp.id_medio_pago_2.disable();
            this.Cmp.monto_forma_pago_2.disable();
            this.Cmp.monto_forma_pago_2.reset();
            //this.Cmp.id_forma_pago_2.reset();
            this.Cmp.id_medio_pago_2.reset();
            this.Cmp.id_moneda_2.reset();
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
        },this);


        this.Cmp.id_moneda_2.on('select',function(c,r,i) {
          if (this.Cmp.id_moneda.value != 2 && this.Cmp.id_moneda_2.value == 2 ) {
             this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue())/this.tipo_cambio);

          }else if (this.Cmp.id_moneda.value == 2 && this.Cmp.id_moneda_2.value == 2) {
            this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)/this.tipo_cambio);

          }else if (this.Cmp.id_moneda.value == 2 && this.Cmp.id_moneda_2.value != 2) {
            this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio));
          }
          else{
              this.Cmp.monto_forma_pago_2.setValue(this.suma_total-this.Cmp.monto_forma_pago.getValue());
          }

        },this);


          this.Cmp.monto_forma_pago.on('change',function(field,newValue,oldValue){
          this.obtenersuma();
          if(this.Cmp.id_moneda.getValue() == 2){
              //console.log("llega el dolar");
            this.Cmp.cambio.setValue(((this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)-this.suma_total));
            this.Cmp.cambio_moneda_extranjera.setValue(((this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)-this.suma_total)/this.tipo_cambio);
          } else {
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
            //this.Cmp.id_forma_pago_2.enable();
            this.Cmp.id_moneda_2.enable();
            this.Cmp.id_medio_pago_2.enable();
            this.Cmp.monto_forma_pago_2.enable();
            this.Cmp.monto_forma_pago_2.setValue((this.suma_total-(this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio))/this.tipo_cambio);

            /*Aqui para que moneda esta por defecto*/
            this.Cmp.id_moneda_2.store.load({params:{start:0,limit:50},
                   callback : function (r) {
                     for (var i = 0; i < r.length; i++) {
                       if (r[i].data.id_moneda == this.data.objPadre.variables_globales.id_moneda_base) {
                         this.Cmp.id_moneda_2.setValue(r[i].data.id_moneda);
                         this.Cmp.id_moneda_2.fireEvent('select', this.Cmp.id_moneda_2,this.Cmp.id_moneda_2.store.getById(r[i].data.id_moneda));

                       }
                     }
                    }, scope : this
                });
            /***************************************/



          }else if (this.Cmp.id_moneda.getValue() != 2 && this.Cmp.monto_forma_pago.getValue() < this.suma_total) {


            /**********************************Cambiamos el Style *****************************************/
            this.Cmp.cambio_moneda_extranjera.label.dom.control.style.color = "red";
            this.Cmp.cambio_moneda_extranjera.label.dom.control.style.background = "#FFE4E4";
            this.Cmp.cambio.label.dom.control.style.color = "red";
            this.Cmp.cambio.label.dom.control.style.background = "#FFE4E4";
            /**********************************Cambiamos el Style *****************************************/

            //this.Cmp.id_forma_pago_2.enable();
            this.Cmp.id_moneda_2.enable();
            this.Cmp.id_medio_pago_2.enable();
            this.Cmp.monto_forma_pago_2.enable();
            this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()));


            /*Aqui para que moneda esta por defecto*/
            this.Cmp.id_moneda_2.store.load({params:{start:0,limit:50},
                   callback : function (r) {
                     for (var i = 0; i < r.length; i++) {
                       if (r[i].data.id_moneda == this.data.objPadre.variables_globales.id_moneda_base) {
                         this.Cmp.id_moneda_2.setValue(r[i].data.id_moneda);
                         this.Cmp.id_moneda_2.fireEvent('select', this.Cmp.id_moneda_2,this.Cmp.id_moneda_2.store.getById(r[i].data.id_moneda));

                       }
                     }
                    }, scope : this
                });
            /***************************************/

          } else{
            //this.Cmp.id_forma_pago_2.disable();
            this.Cmp.id_moneda_2.disable();
            this.Cmp.id_medio_pago_2.disable();
            this.Cmp.monto_forma_pago_2.disable();
            this.Cmp.monto_forma_pago_2.reset();
            //this.Cmp.id_forma_pago_2.reset();
            this.Cmp.id_moneda_2.reset();
            this.Cmp.id_medio_pago_2.reset();
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

        },this);


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


        /**************************************************************************************************************/

        /*Comentando para aumentar la instancia de pago*/
        // this.Cmp.monto_forma_pago.on('change',function(field,newValue,oldValue){
        //   this.obtenersuma();
        //   if(this.moneda == 'USD'){
        //       //console.log("llega el dolar");
        //     this.Cmp.cambio.setValue(((this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)-this.suma_total));
        //     this.Cmp.cambio_moneda_extranjera.setValue(((this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio)-this.suma_total)/this.tipo_cambio);
        //   } else {
        //     this.Cmp.cambio.setValue((this.Cmp.monto_forma_pago.getValue()-this.suma_total));
        //     this.Cmp.cambio_moneda_extranjera.setValue((this.Cmp.monto_forma_pago.getValue()-this.suma_total)/this.tipo_cambio);
        //   }
        //
        //   if (this.moneda == 'USD' && (this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio) < this.suma_total) {
        //
        //
        //     /**********************************Cambiamos el Style *****************************************/
        //     this.Cmp.cambio_moneda_extranjera.label.dom.control.style.color = "red";
        //     this.Cmp.cambio_moneda_extranjera.label.dom.control.style.background = "#FFE4E4";
        //     this.Cmp.cambio.label.dom.control.style.color = "red";
        //     this.Cmp.cambio.label.dom.control.style.background = "#FFE4E4";
        //     /**********************************Cambiamos el Style *****************************************/
        //     this.Cmp.id_forma_pago_2.enable();
        //     this.Cmp.monto_forma_pago_2.enable();
        //     this.Cmp.monto_forma_pago_2.setValue((this.suma_total-(this.Cmp.monto_forma_pago.getValue()*this.tipo_cambio))/this.tipo_cambio);
        //   }else if (this.moneda != 'USD' && this.Cmp.monto_forma_pago.getValue() < this.suma_total) {
        //
        //
        //     /**********************************Cambiamos el Style *****************************************/
        //     this.Cmp.cambio_moneda_extranjera.label.dom.control.style.color = "red";
        //     this.Cmp.cambio_moneda_extranjera.label.dom.control.style.background = "#FFE4E4";
        //     this.Cmp.cambio.label.dom.control.style.color = "red";
        //     this.Cmp.cambio.label.dom.control.style.background = "#FFE4E4";
        //     /**********************************Cambiamos el Style *****************************************/
        //
        //
        //     this.Cmp.id_forma_pago_2.enable();
        //     this.Cmp.monto_forma_pago_2.enable();
        //     this.Cmp.monto_forma_pago_2.setValue((this.suma_total-this.Cmp.monto_forma_pago.getValue()));
        //   } else{
        //     this.Cmp.id_forma_pago_2.disable();
        //     this.Cmp.monto_forma_pago_2.disable();
        //     this.Cmp.monto_forma_pago_2.reset();
        //     this.Cmp.id_forma_pago_2.reset();
        //     this.Cmp.moneda_tarjeta_2.reset();
        //     this.Cmp.cambio_moneda_extranjera.label.dom.control.style.color = "blue";
        //     this.Cmp.cambio_moneda_extranjera.label.dom.control.style.background = "#EFFFD6";
        //     this.Cmp.cambio.label.dom.control.style.color = "";
        //     this.Cmp.cambio.label.dom.control.style.background = "#EFFFD6";
        //     this.ocultarComponente(this.Cmp.mco_2);
        //     this.ocultarComponente(this.Cmp.numero_tarjeta_2);
        //     this.ocultarComponente(this.Cmp.codigo_tarjeta_2);
        //     this.ocultarComponente(this.Cmp.id_auxiliar_2);
        //     this.ocultarComponente(this.Cmp.tipo_tarjeta_2);
        //   }
        //
        //
        //
        //
        // },this);

        this.Cmp.id_sucursal.on('select',function(c,r,i) {
        	if (this.accionFormulario != 'EDIT') {
              /*Comentando para agregar InstanciaPago*/
              //this.Cmp.id_forma_pago.store.baseParams.defecto = 'si';
              this.Cmp.id_medio_pago.store.baseParams.defecto = 'si';
            	this.Cmp.id_medio_pago.store.baseParams.filtrar_base = 'si';

            }

            this.cargarFormaPago();

        },this);


        this.detCmp.tipo.on('select',function(c,r,i) {
            this.cambiarCombo(r.data.field1);
        },this);

        // this.Cmp.id_cliente.on('select',function(c,r,i) {
        //
        //     if (r.data) {
        //         this.Cmp.nit.setValue(r.data.nit);
        //     } else {
        //         this.Cmp.nit.setValue(r.nit);
        //     }
        // },this);

        this.Cmp.nit.on('blur',function(c) {
        if (this.accionFormulario != 'EDIT') {
  				if (this.Cmp.nit.getValue() != '') {
            this.Cmp.nombre_factura.reset();
            this.Cmp.id_cliente.reset();
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
        } else {
          if (this.Cmp.nit.getValue() == '' || this.Cmp.habilitar_edicion.getValue() == 'SI') {
            this.Cmp.nombre_factura.reset();
            this.Cmp.id_cliente.reset();
  					Ext.Ajax.request({
  							url : '../../sis_ventas_facturacion/control/VentaFacturacion/RecuperarCliente',
  							params : {
  								'nit' : this.Cmp.nit.getValue(),
  								'razon_social' : this.Cmp.nombre_factura.getValue(),
  							},
  							success: function(resp){
  	                var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    //this.Cmp.nombre_factura.reset();
                    this.Cmp.nombre_factura.setValue(reg.ROOT.datos.razon);
  	                this.Cmp.id_cliente.setValue(reg.ROOT.datos.id_cliente);
  	            },
  							failure : this.conexionFailure,
  							timeout : this.timeout,
  							scope : this
  						});

  				}
        }

  			},this);



        // this.Cmp.nit.on('blur',function(c) {
        //   if (this.accionFormulario != 'EDIT') {
        //     	if (this.Cmp.nit.getValue() != '') {
        //     		this.Cmp.id_cliente.store.baseParams.nit = this.Cmp.nit.getValue();
        //         	this.Cmp.id_cliente.store.load({params:{start:0,limit:1},
    		//            callback : function (r) {
    		//            		this.Cmp.id_cliente.store.baseParams.nit = '';
    		//            		if (r.length == 1) {
        //
    		//            			this.Cmp.id_cliente.setValue(r[0].data.id_cliente);
    		//            			}
        //
    		//             }, scope : this
    		//         });
    		//     }
        //   }
        // },this);

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
        this.obtenersuma();


        this.ocultarComponente(this.Cmp.habilitar_edicion);
        /*Ocultar campo excento*/
        // if (this.Cmp.excento.getValue() == 0) {
        //   this.ocultarComponente(this.Cmp.excento);
        // } else {
        //   this.mostrarComponente(this.Cmp.excento);
        // }
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

            this.Cmp.id_medio_pago.store.load({params:{start:0,limit:50},
                   callback : function (r) {
                              if (r.length == 1 ) {
                                  this.Cmp.id_medio_pago.setValue(r[0].data.id_medio_pago_pw);
                                  this.Cmp.id_medio_pago.fireEvent('select', this.Cmp.id_medio_pago_pw,r[0],0);
                              } else {
                                for (var i = 0; i < r.length; i++) {
                                  if (r[i].data.fop_code.startsWith("CA")) {
                                    this.Cmp.id_medio_pago.setValue(r[i].data.id_medio_pago_pw);
                                    this.Cmp.id_medio_pago.fireEvent('select', this.Cmp.id_medio_pago_pw,r[i]);
                                  }
                                }
                              }
              this.Cmp.id_medio_pago.store.baseParams.defecto = 'no';
                    }, scope : this
                });
            /***************/

        /*Comentando para incluir InstanciaPago*/
        /*this.Cmp.id_forma_pago.store.load({params:{start:0,limit:50},
		           callback : function (r) {
		           		//if (this.accionFormulario != 'NEW') {
		           			if (r.length == 1 ) {
			                    this.Cmp.id_forma_pago.setValue(r[0].data.id_forma_pago);
			                    this.Cmp.id_forma_pago.fireEvent('select', this.Cmp.id_forma_pago,r[0],0);
			                }
		           		//} else {
		           			this.Cmp.id_forma_pago.fireEvent('select', this.Cmp.id_forma_pago,this.Cmp.id_forma_pago.store.getById(this.Cmp.id_forma_pago.getValue()),0);
		           		//}
		                this.Cmp.id_forma_pago.store.baseParams.defecto = 'no';
		                this.Cmp.id_forma_pago.modificado = true;
		            }, scope : this
		        });*/
      }
      if (this.accionFormulario == 'EDIT') {

        this.mostrarComponente(this.Cmp.habilitar_edicion);
        this.Cmp.cambio.setValue(0);
        this.Cmp.cambio_moneda_extranjera.setValue(0);
        /*******************Mostramos si se tiene excento***********************/
        // if (this.Cmp.excento.getValue() == 0) {
        //   this.ocultarComponente(this.Cmp.excento);
        // } else {
        //   this.mostrarComponente(this.Cmp.excento);
        // }

        // if (this.asociar_boleto == 'si') {
        //   this.mostrarComponente(this.Cmp.boleto_asociado);
        //   this.Cmp.boleto_asociado.allowBlank=false;
        // } else {
        //   this.ocultarComponente(this.Cmp.boleto_asociado);
        //   this.Cmp.boleto_asociado.allowBlank=true;
        // }
        /**********************************************************************/

        /*****************Habilitamos los campos si se pone editar***************/
        this.Cmp.habilitar_edicion.setValue('NO');

        this.Cmp.nit.setDisabled(true);
        //this.Cmp.id_cliente.setDisabled(true);
        this.Cmp.nombre_factura.setDisabled(true);
        this.Cmp.id_formula.setDisabled(true);
        this.Cmp.observaciones.setDisabled(true);
        //this.Cmp.id_punto_venta.setDisabled(true);
        this.Cmp.excento.setDisabled(true);

              //  this.Cmp.id_formula.fireEvent('select',this.Cmp.id_formula, this.Cmp.id_formula.store.getById(this.data.datos_originales.data.id_formula));
        this.Cmp.habilitar_edicion.on('select',function(c,r,i) {

          if (r.data.value == 'NO') {
            this.Cmp.nit.setDisabled(true);
            this.Cmp.id_cliente.setDisabled(true);
            this.Cmp.nombre_factura.setDisabled(true);
            //this.Cmp.id_formula.setDisabled(true);
            this.Cmp.observaciones.setDisabled(true);
            //this.Cmp.id_punto_venta.setDisabled(true);
            this.Cmp.excento.setDisabled(true);
            // this.megrid.topToolbar.items.items[0].setDisabled(true);
            // this.megrid.topToolbar.items.items[1].setDisabled(true);
            // this.megrid.topToolbar.items.items[2].setDisabled(true);
            // this.megrid.topToolbar.items.items[3].setDisabled(true);
            this.megrid.colModel.config[3].editor='';
            this.megrid.colModel.config[4].editor='';
            this.megrid.colModel.config[5].editor='';
            //this.mestore.rollbChanges();
          } else {
            this.Cmp.nit.setDisabled(false);
            this.Cmp.id_cliente.setDisabled(false);
            this.Cmp.nombre_factura.setDisabled(false);
            //this.Cmp.id_formula.setDisabled(false);
            this.Cmp.observaciones.setDisabled(false);
            //this.Cmp.id_punto_venta.setDisabled(false);
            this.Cmp.excento.setDisabled(false);
            // this.megrid.topToolbar.items.items[0].setDisabled(false);
            // this.megrid.topToolbar.items.items[1].setDisabled(false);
            // this.megrid.topToolbar.items.items[2].setDisabled(false);
            // this.megrid.topToolbar.items.items[3].setDisabled(false);

            /*************************Habilitar la grilla para editar*************************/
            this.megrid.colModel.config[3].editor='';
            this.megrid.colModel.config[4].editor='';
            this.megrid.colModel.config[5].editor='';

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


    obtenersuma: function (flag) {
      var total_datos = this.megrid.store.data.items.length;

      var verificar_montos = [];

      var suma = 0;
      for (var i = 0; i < total_datos; i++) {
        if (this.megrid.store.data.items[i].data.precio_total == 0 || isNaN(this.megrid.store.data.items[i].data.precio_total) || this.megrid.store.data.items[i].data.precio_total == '') {
          verificar_montos.push(this.megrid.store.data.items[i].data.precio_total);
        }
          suma = suma + parseFloat(this.megrid.store.data.items[i].data.precio_total);
      }
      if(!flag){
      if (verificar_montos.length > 0 ) {
          Ext.Msg.show({
           title:'Información',
           maxWidth : 550,
           width: 550,
           msg: 'Hay conceptos que no tienen precio unitario o el monto es 0, favor verifique y complete la información!',
           buttons: Ext.Msg.OK,
           icon: Ext.MessageBox.QUESTION,
           scope:this
        });

        verificar_montos = [];
      }
    }

      this.suma_total = suma;
      this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[6].childNodes[0].style.color="#7400FF";
      this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[6].childNodes[0].style.fontWeight="bold";
      this.summary.view.summary.dom.firstChild.lastElementChild.lastElementChild.cells[6].childNodes[0].style.fontSize="20px";

      /*Aqui pára mantener el campo*/
      var requiere_excento = [];
      var requiere_asociar = [];


      if (this.megrid.store.data.items.length>0) {
        for (var i = 0; i < this.megrid.store.data.items.length; i++) {
          if (!requiere_excento.includes(this.megrid.store.data.items[i].data.requiere_excento)) {
              requiere_excento.push(this.megrid.store.data.items[i].data.requiere_excento);
          }
        }
      } else {
        requiere_excento = [];
      }



      if (requiere_excento.includes('si')) {
        this.mostrarComponente(this.Cmp.excento);
        if (this.accionFormulario == 'EDIT') {
          this.Cmp.excento.setDisabled(true);
        } else {
          this.Cmp.excento.setDisabled(false);
        }

      } else {
        this.ocultarComponente(this.Cmp.excento);
      }


      if (this.megrid.store.data.items.length>0) {
        for (var i = 0; i < this.megrid.store.data.items.length; i++) {

          if (!requiere_asociar.includes(this.megrid.store.data.items[i].data.asociar_boletos)) {
              requiere_asociar.push(this.megrid.store.data.items[i].data.asociar_boletos);
          }
        }
      } else {
        requiere_asociar = [];
      }

      if (requiere_asociar.includes('si')) {
        this.mostrarComponente(this.Cmp.boleto_asociado);
        //this.Cmp.boleto_asociado.setDisabled(false);
        this.Cmp.boleto_asociado.allowBlank = false;
      } else {
        this.ocultarComponente(this.Cmp.boleto_asociado);
        this.Cmp.boleto_asociado.allowBlank = true;
      }
      //
      //
      // if (this.requiere_excento != undefined) {
      //   if (!requiere_excento.includes(this.requiere_excento)) {
      //     requiere_excento.push(this.requiere_excento);
      //     this.Cmp.excento.setDisabled(true);
      //   }
      // }
      //
      //   if (requiere_excento.includes('si')) {
      //     this.mostrarComponente(this.Cmp.excento);
      //     this.Cmp.excento.allowBlank = false;
      //
      //     if (this.accionFormulario != 'EDIT') {
      //       this.Cmp.excento.setDisabled(false);
      //     }
      //
      //
      //   } else {
      //     this.ocultarComponente(this.Cmp.excento);
      //     this.Cmp.boleto_asociado.allowBlank = true;
      //     this.Cmp.boleto_asociado.reset();
      //   }
      //
      //   if (this.asociar_boleto != undefined) {
      //     requiere_asociar.push(this.asociar_boleto);
      //   }
      //
      //   if (this.megrid.store.data.items.length>0) {
      //     for (var i = 0; i < this.megrid.store.data.items.length; i++) {
      //       if (!requiere_asociar.includes(this.megrid.store.data.items[i].data.asociar_boletos)) {
      //           requiere_asociar.push(this.megrid.store.data.items[i].data.asociar_boletos);
      //       }
      //
      //
      //     }
      //
      //   } else {
      //     requiere_asociar = [];
      //     //this.requiere_asociar_boleto = undefined;
      //   }
      //
      //   console.log("aqui llega datos",requiere_asociar);
      //   console.log("aqui llega datos22222",requiere_excento);
      //
      // if (requiere_asociar.includes('si')) {
      //   this.mostrarComponente(this.Cmp.boleto_asociado);
      //   this.Cmp.boleto_asociado.allowBlank = false;
      // } else {
      //   this.ocultarComponente(this.Cmp.boleto_asociado);
      //   this.Cmp.boleto_asociado.allowBlank = true;
      //   this.Cmp.boleto_asociado.reset();
      // }


        // if (this.requiere_asociar_boleto != undefined) {
        //   if (this.requiere_asociar_boleto == 'si') {
        //     this.mostrarComponente(this.Cmp.boleto_asociado);
        //     this.Cmp.boleto_asociado.allowBlank = false;
        //   }
        // }

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
    buildDetailGridNew: function(){

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

        this.editarDescripcion = new Ext.form.TextField({});

        /*megrid irva condicion*/
        this.megrid = new Ext.grid.EditorGridPanel({
                    layout: 'fit',
                    store:  this.mestore,
                    region: 'center',
                    split: true,
                    border: false,
                    loadMask : true,
                    clicksToEdit: 1,
                    plain: true,
                    id:'barraMenu',
                    plugins: [this.summary],
                    stripeRows: true,
                    tbar: [
                      {
                  	    text: '<div style="font-weight:bold; font-size:15px;"><img src="../../../lib/imagenes/facturacion/guardar.png" style="width:30px; vertical-align: middle;"> Guardar</div>',
                        scope: this,
                        id:'botonGuardar',
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
                        id:'botonEliminar',
                          handler : function(){
                            var index = this.megrid.getSelectionModel().getSelectedCell();
                            if (!index) {
                                return false;
                            }
                            var rec = this.mestore.getAt(index[0]);
                            this.mestore.remove(rec);
                            this.obtenersuma(true);
                            /*Cuando eliminamos un servicio que requiere excento reseteamos y ocultamos el campo*/
                            // if (rec.data.requiere_excento == 'si') {
                            //   this.ocultarComponente(this.Cmp.excento);
                            //   this.Cmp.excento.reset();
                            // }
                            //
                            // /*Cuando eliminamos un servicio que requiere boleto reseteamos y ocultamos el campo*/
                            // if (rec.data.asociar_boleto == 'si') {
                            //   this.ocultarComponente(this.Cmp.boleto_asociado);
                            //   this.Cmp.boleto_asociado.reset();
                            //   this.Cmp.boleto_asociado.allowBlank=true;
                            // }



                            /***********************************************************************************/
                        }


                        },
                    // {
                    //         text: '<i class="fa fa-plus-circle fa-lg"></i> Duplicar registro',
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
                        //editor: this.detCmp.tipo
                    },
                    {
                        header: '<img src="../../../lib/imagenes/facturacion/BolsaCompraColores.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle; font-size:17px;"> Producto/Servicio</span>',
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
                        header: '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle; font-size:17px;"> Descripción</span>',
                        dataIndex: 'descripcion',
                        width: 300,
                        //sortable: false,
                        editor:this.editarDescripcion
                    },
                    {

                        header: '<img src="../../../lib/imagenes/facturacion/Cantidad.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle; font-size:17px;"> Cantidad</span>',
                        dataIndex: 'cantidad',
                        align: 'right',
                        width: 150,
                        summaryType: 'sum',
                        editor: this.detCmp.cantidad
                    },
                    {
                        header: '<img src="../../../lib/imagenes/facturacion/Dolar.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle; font-size:17px;"> P / Unit</span>',
                        dataIndex: 'precio_unitario',
                        align: 'right',
                        selectOnFocus: true,
                        width: 100,
                        decimalPrecision : 2,
                        summaryType: 'sum',
                        renderer : function(value, p, record) {
                            return parseFloat(record.data['precio_unitario']);
                        },
                        editor: this.detCmp.precio_unitario
                    },
                    {
                        xtype: 'numbercolumn',
                        header: '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle; color:red; font-size:17px;"> <b>Total</b></span>',
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
                        {name:'asociar_boletos', type: 'string'},
                        {name:'requiere_excento', type: 'string'},

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
                    tbar: [
                      {
                  	    text: '<div style="font-weight:bold; font-size:15px;"><img src="../../../lib/imagenes/facturacion/guardar.png" style="width:30px; vertical-align: middle;"> Guardar</div>',
                        scope: this,
                        id:'botonGuardar',
                        disabled:true,
              			    handler: function(btn) {
                          this.guardarDetalles();
              			    }
                      },
                      {
                      text: '<div style="font-weight:bold; font-size:15px;"><img src="../../../lib/imagenes/facturacion/anadir.png" style="width:30px; vertical-align: middle;"> Agregar Detalle</div>',
                      scope: this,
                      id:'botonAgregar',
                      disabled:true,
                        handler : function(){
                          this.formularioAgregar();
                          }
                      },
                      {
                        text: '<div style="font-weight:bold; font-size:15px;"><img src="../../../lib/imagenes/facturacion/eliminar.png" style="width:30px; vertical-align: middle;"> Eliminar</div>',
                        scope: this,
                        disabled:true,
                        id:'botonEliminar',
                          handler : function(){
                            var index = this.megrid.getSelectionModel().getSelectedCell();
                            if (!index) {
                                return false;
                            }
                            var rec = this.mestore.getAt(index[0]);
                            this.mestore.remove(rec);
                            this.obtenersuma();
                            /*Cuando eliminamos un servicio que requiere excento reseteamos y ocultamos el campo*/
                            if (rec.data.requiere_excento == 'si') {
                              this.ocultarComponente(this.Cmp.excento);
                              this.Cmp.excento.reset();
                            }
                            if (rec.data.asociar_boleto == 'si') {
                              this.ocultarComponente(this.Cmp.boleto_asociado);
                              this.Cmp.boleto_asociado.reset();
                              this.Cmp.boleto_asociado.allowBlank=true;
                            }
                            /***********************************************************************************/
                        }


                        },
                    // {
                    //         text: '<i class="fa fa-plus-circle fa-lg"></i> Duplicar registro',
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
                        //editor: this.detCmp.tipo
                    },
                    {
                        header: '<img src="../../../lib/imagenes/facturacion/BolsaCompraColores.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle; font-size:17px;"> Producto/Servicio</span>',
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
                        header: '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle; font-size:17px;"> Descripción</span>',
                        dataIndex: 'descripcion',
                        width: 300,
                        //sortable: false,
                        editor: ''
                    },
                    {

                        header: '<img src="../../../lib/imagenes/facturacion/Cantidad.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle; font-size:17px;"> Cantidad</span>',
                        dataIndex: 'cantidad',
                        align: 'right',
                        width: 130,
                        summaryType: 'sum',
                        editor: ''
                    },
                    {
                        header: '<img src="../../../lib/imagenes/facturacion/Dolar.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle; font-size:17px;"> P / Unit</span>',
                        dataIndex: 'precio_unitario',
                        align: 'right',
                        selectOnFocus: true,
                        width: 100,
                        decimalPrecision : 2,
                        summaryType: 'sum',
                        renderer : function(value, p, record) {
                            return parseFloat(record.data['precio_unitario']);
                        },
                        editor: ''
                    },
                    {
                        xtype: 'numbercolumn',
                        header: '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle; font-size:17px; color:red"> <b>Total Bs</b></span>',
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

    guardarDetalles : function(flag){

      for (var i = 0; i < this.megrid.store.data.items.length; i++) {
        this.megrid.store.data.items[i].data.precio_total=(this.megrid.store.data.items[i].data.precio_unitario * this.megrid.store.data.items[i].data.cantidad);
      }

      this.mestore.commitChanges();
      this.megrid.getView().refresh();
      // if(!flag){
        this.obtenersuma(flag);
      // }
    },


    formularioAgregar : function(){
      var simple = new Ext.FormPanel({
       labelWidth: 75, // label settings here cascade unless overridden
       frame:true,
       bodyStyle:'margin-left:-7px; margin-top:-7px; padding:10px 10px 0; background:#6EC8E3;',
       width: 650,
       height:300,
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
                                                      fieldLabel: '<img src="../../../lib/imagenes/facturacion/BolsaCompraColores.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Producto/<br>Servicio</span>',
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
                                                          fields: ['id_concepto_ingas', 'tipo','desc_moneda','id_moneda','desc_ingas','requiere_descripcion','precio','excento','contabilizable','boleto_asociado','nombre_actividad'],
                                                          remoteSort: true,
                                                          baseParams: {par_filtro: 'ingas.desc_ingas',facturacion:'FACTCOMP', emision:'facturacion'}
                                                      }),
                                                      valueField: 'id_concepto_ingas',
                                                      displayField: 'desc_ingas',
                                                      gdisplayField: 'desc_ingas',
                                                      hiddenName: 'id_producto',
                                                      forceSelection: true,
                                                      tpl: new Ext.XTemplate([
                                                         '<tpl for=".">',
                                                         '<div class="x-combo-list-item">',
                                                         '<p><span style="color: green; font-weight:bold;"> {desc_ingas}</span></p></p>',
                                                         '</div></tpl>'
                                                       ]),
                                                      // tpl: new Ext.XTemplate([
                                                      //    '<tpl for=".">',
                                                      //    '<div class="x-combo-list-item">',
                                                      //    '<p><b>Nombre:</b><span style="color: green; font-weight:bold;"> {desc_ingas}</span></p></p>',
                                                      //    '<p><b>Actividad Económica:</b><span style="color: green; font-weight:bold;"> {nombre_actividad}</span></p></p>',
                                                      //    '<p><b>Moneda:</b> <span style="color: blue; font-weight:bold;">{desc_moneda}</span></p>',
                                                      //    '<p><b>Precio:</b> <span style="color: blue; font-weight:bold;">{precio}</span></p>',
                                                      //    '<p><b>Tiene Exento:</b> <span style="color: red; font-weight:bold;">{excento}</span></p>',
                                                      //    '<p><b>Requiere Descripción:</b> <span style="color: red; font-weight:bold;">{requiere_descripcion}</span></p>',
                                                      //    '<p><b>Contabilizable:</b> <span style="color: red; font-weight:bold;">{contabilizable}</span></p>',
                                                      //    '<p><b>Asociar:</b> <span style="color: red; font-weight:bold;">{boleto_asociado}</span></p>',
                                                      //    '</div></tpl>'
                                                      //  ]),
                                                      typeAhead: false,
                                                      triggerAction: 'all',
                                                      lazyRender: true,
                                                      mode: 'remote',
                                                      resizable:true,
                                                      pageSize: 20,
                                                      queryDelay: 1000,
                                                      //anchor: '100%',
                                                      width : 450,
                                                      listWidth:'600',
                                                      minChars: 2 ,
                                                      disabled:true,
                                                       style:{
                                                         width: '200px'
                                                       },

                                                    }),
                                                     new Ext.form.TextArea({
                                                            name: 'descripcion',
                                                            fieldLabel: '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Descripción</span>',
                                                            allowBlank:true,
                                                            style:{
                                                              width: '190px'
                                                            },
                                                            disabled : true,
                                                            width : 450,
                                                            hidden : true
                                                    }),

                                                    new Ext.form.NumberField({
                                                                        name: 'cantidad',
                                                                        msgTarget: 'title',
                                                                        fieldLabel: '<img src="../../../lib/imagenes/facturacion/Cantidad.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Cantidad</span>',
                                                                        allowBlank: false,
                                                                        width : 450,
                                                                        style:{
                                                                          width: '190px'
                                                                        },
                                                                        //allowDecimals: me.cantidadAllowDecimals,
                                                                        decimalPrecision : 2,
                                                                        enableKeyEvents : true,


                                                                }),
                                                    new Ext.form.NumberField({
                                                                        name: 'precio_unitario',
                                                                        msgTarget: 'title',
                                                                        fieldLabel: '<img src="../../../lib/imagenes/facturacion/Dolar.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> P/U</span>',
                                                                        allowBlank: false,
                                                                        allowDecimals: true,
                                                                        width : 450,
                                                                        decimalPrecision : 2,
                                                                        style:{
                                                                          width: '190px'
                                                                        },
                                                                        enableKeyEvents : true
                                                                }),
                                                     new Ext.form.NumberField({
                                                                        name: 'precio_total',
                                                                        msgTarget: 'title',
                                                                        fieldLabel: '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Total</span>',
                                                                        style:{
                                                                          width: '190px'
                                                                        },
                                                                        allowBlank: false,
                                                                        width : 450,
                                                                        allowDecimals: false,
                                                                        maxLength:10,
                                                                        readOnly :true
                                                                })
                                                  ]

   });
   this.variables = simple;
   /*Aumentando para Filtrar los servicios por id_punto_venta y el tipo del PV (ATO CTO)*/
   this.variables.items.items[1].store.baseParams.id_punto_venta_producto = this.data.objPadre.variables_globales.id_punto_venta;
   this.variables.items.items[1].store.baseParams.tipo_pv = this.data.objPadre.tipo_punto_venta;
   // /************************************************************************************************/
      var win = new Ext.Window({
        title: '<center><img src="../../../lib/imagenes/facturacion/anadir.png" style="width:30px; vertical-align: middle;"><span style="vertical-align: middle; font-size:25px; font-weight:bold; color:#1479B8; text-shadow: 3px 0px 0px #000000;"> AGREGAR DETALLE</span></center>', //the title of the window
        width:600,
        height:250,
        closeAction:'hide',
        modal:true,
        plain: true,
        items:simple,
        buttons: [{
                    text:'<div style="font-weight:bold; font-size:12px;"><img src="../../../lib/imagenes/facturacion/aceptar.png" style="width:15px; vertical-align: middle;"> <span style="vertical-align: middle; font-size:15px;">Guardar</span></div>',
                    id:'botonGuardarFormulario',
                    scope:this,
                    handler: function(){
                      if (this.megrid.store.data.items.length == 0) {
                        this.insertarNuevo(win);
                      } else {
                        var array = new Array();
                        for (var i = 0; i < this.megrid.store.data.items.length; i++) {
                          if (!array.includes(this.megrid.store.data.items[i].data.contabilizable)) {
                            array.push(this.megrid.store.data.items[i].data.contabilizable);
                          }
                        }

                        if (array.includes(this.contabilizable)) {
                          this.insertarNuevo(win);
                        } else {
                          Ext.Msg.show({
                      			   title:'Información',
                               maxWidth : 550,
                               width: 550,
                      			   msg: 'No puede utilizar conceptos contabilizables y no contabilizables en la misma venta!',
                      			   buttons: Ext.Msg.OK,
                               icon: Ext.MessageBox.QUESTION,
                      			   scope:this
                      			});
                        }

                      }
                    }
                },{
                    text: '<div style="font-weight:bold; font-size:12px;"><img src="../../../lib/imagenes/facturacion/cancelar.png" style="width:15px; vertical-align: middle;"> <span style="vertical-align: middle; font-size:13px;">Cancelar</span></div>',
                    id:'botonCancelarFormulario',
                    handler: function(){
                        win.hide();
                    }
                }]

      });
      this.ventana_detalle = win;
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

      /*Aqui aumentamos para poner el filtro de los conceptos*/
      this.variables.items.items[1].store.baseParams.regionales = this.data.objPadre.variables_globales.ESTACION_inicio;
      /*******************************************************/


    	this.variables.items.items[1].setDisabled(false);
    	//this.variables.items.items[1].store.baseParams.tipo = tipo;
      //this.variables.items.items[2].setVisible(false)
    	if (this.data.objPadre.variables_globales.vef_tiene_punto_venta === 'true') {
    		this.variables.items.items[1].store.baseParams.id_punto_venta = this.Cmp.id_punto_venta.getValue();

        this.variables.items.items[1].on('select',function(c,r,i) {
          if (r.data.requiere_descripcion == 'si') {
            this.variables.items.items[2].setDisabled(false);
            this.variables.items.items[2].setVisible(true);
            this.ventana_detalle.body.dom.style.height = "230px";
          } else if (r.data.requiere_descripcion != 'si')  {
              this.variables.items.items[2].setDisabled(true);
    	    		this.variables.items.items[2].allowBlank = true;
              this.variables.items.items[2].setVisible(false)
      	    	this.variables.items.items[2].reset();
              this.ventana_detalle.body.dom.style.height = "150px";
              }




      /***************Habilitamos el campo Excento****************/
      if (this.data.datos_originales == undefined) {
        // if (r.data.excento == 'si' /*|| this.data.datos_originales.data.excento > 0*/) {
        //   this.mostrarComponente(this.Cmp.excento);
        // }else{
        //   this.ocultarComponente(this.Cmp.excento);
        //   this.Cmp.excento.reset();
        // }

        // if (r.data.boleto_asociado == 'si' /*|| this.data.datos_originales.data.excento > 0*/) {
        //   this.mostrarComponente(this.Cmp.boleto_asociado);
        //   this.Cmp.boleto_asociado.allowBlank = false;
        // }else{
        //   this.ocultarComponente(this.Cmp.boleto_asociado);
        //   this.Cmp.boleto_asociado.reset();
        //   this.Cmp.boleto_asociado.allowBlank = true;
        // }



      } else {
        // if (r.data.excento == 'si' || this.data.datos_originales.data.excento > 0) {
        //   this.mostrarComponente(this.Cmp.excento);
        // }else{
        //   this.ocultarComponente(this.Cmp.excento);
        //   this.Cmp.excento.reset();
        // }

        // if (r.data.boleto_asociado == 'si') {
        //   this.mostrarComponente(this.Cmp.boleto_asociado);
        //   this.Cmp.boleto_asociado.allowBlank = false;
        // }else{
        //   this.ocultarComponente(this.Cmp.boleto_asociado);
        //   this.Cmp.boleto_asociado.reset();
        //   this.Cmp.boleto_asociado.allowBlank = true;
        // }


      }

      if (r.data.requiere_descripcion == 'si') {
        this.variables.items.items[2].setDisabled(false);
        this.variables.items.items[2].setVisible(true);
        this.ventana_detalle.body.dom.style.height = "230px";
      }

      this.requiere_excento = r.data.excento;
      this.contabilizable = r.data.contabilizable??"no";
      this.asociar_boleto = r.data.boleto_asociado??"no";


     /***********************************************************/

     /*************************Recuperamos el precio unitario (irva recuperacion dato)************************************/
          this.variables.items.items[3].setValue(1);
          if (r.data.id_moneda == 2) {
            var precio = (r.data.precio * this.tipo_cambio);
          } else {
            var precio = r.data.precio;
          }
          this.variables.items.items[4].setValue(precio);
          this.variables.items.items[5].setValue(this.variables.items.items[3].getValue()*precio);

    /*********************************************************************************************/

        },this);
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
        || this.variables.items.items[3].getValue() == '' || this.variables.items.items[4].getValue() == '' || this.variables.items.items[5].getValue() == '') {
          Ext.Msg.show({
  			   title:'Información',
           maxWidth : 550,
           width: 550,
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
      precio_total:this.variables.items.items[5].getValue() ,
      requiere_excento:this.requiere_excento,        //
      id_venta:this.Cmp.id_venta.getValue()  ,      //
      contabilizable:this.contabilizable??"no",     //
      asociar_boletos:this.asociar_boleto??"no"        //
      });
      this.mestore.add(myNewRecord);
      //this.obtenersuma();
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

          // for (var i = this.mestore.data.length; i >= 0; i--) {
          //         var suma_eli = 0;
          //         suma_eli = suma_eli + i;
          //         var dato = 0;
          //         dato = suma_eli - 1;
          //         if(dato == (-1) ){
          //           dato = 0;
          //         }
          //         if (suma_eli == 0 ) {
          //             this.successRecuperarDatos();
          //         } else if (suma_eli >= 0 && this.mestore.data.items[(dato)].data.tipo == 'formula')  {
          //            this.mestore.remove(this.mestore.getAt(dato));
          //          }
          //   }
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
                  this.tiene_excento = reg.ROOT.datos.v_excento_req;
                  this.boletos_asociados = reg.ROOT.datos.v_boletos_asociados;
                  this.excento_formula = reg.ROOT.datos.v_requiere_excento;
                  this.requiere_asociar_boleto = reg.ROOT.datos.v_boleto_asociado;
                  this.precio_inde = reg.ROOT.datos.v_precio;
                  this.producto_nombre = this.nombre_producto.split(",");
                  this.producto_id = this.id_producto_recu.split(",");
                  this.id_formula = this.id_formula_recu.split(",");
                  this.req_excento = this.tiene_excento.split(",");
                  this.precio_form = this.precio_inde.split(",");
                  this.boletos_asociados_recup = this.boletos_asociados.split(",");
                  this.desc_moneda_recu = reg.ROOT.datos.v_desc_moneda;
                  this.nombre_moneda = this.desc_moneda_recu.split(",");

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
                      {name:'requiere_excento', type: 'string'},
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
                  if (this.nombre_moneda[i] != this.data.objPadre.variables_globales.codigo_moneda_base) {
                    var precio_base = this.precio_form[i]*this.tipo_cambio;
                  } else {
                    var precio_base = this.precio_form[i];
                  }

                var myNewRecord = new grillaRecord({
                    nombre_producto : this.producto_nombre[i],
                //     descripcion : request.arguments.nombre_formula,
                    id_producto: this.producto_id[i],
                    id_formula: this.id_formula[i],
                    tipo : 'formula',
                    cantidad : '1',
                    requiere_excento:this.req_excento[i],
                    precio_unitario : (precio_base==undefined || precio_base==null || precio_base=='')?0:precio_base,
                    precio_total: precio_base*1,
                    asociar_boletos: this.boletos_asociados_recup[i],

                  });
                   this.mestore.add(myNewRecord);
              }

                  //this.mestore.commitChanges();
                  console.log("aqui llega data",this);
                  if (this.excento_formula == 'si') {
                    this.mostrarComponente(this.Cmp.excento);
                  }

                  if (this.requiere_asociar_boleto == 'si') {
                    this.mostrarComponente(this.Cmp.boleto_asociado);
                  }

                  //this.obtenersuma();
                  this.guardarDetalles(true);

              },
              failure: this.conexionFailure,
              timeout:this.timeout,
              scope:this
          });

        },


    onDuplicateDetail : function (rec) {
      console.log("llega auqi ", rec);
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
            console.log("llega descripcion 1",this);
        } else {
        	this.habilitarDescripcion(false);
          console.log("llega descripcion 2",this);
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
                            autoScroll:true,
                            style: {
                                   height:'160px',
                                   background: '#5FB0A8',
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
                                               height:'160px',
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
                                  //autoHeight: true,
                                  border: false,
                                  items:[
                                     {
                                      xtype: 'fieldset',
                                      frame: true,
                                      border: false,
                                      layout: 'form',
                                      style: {
                                             height:'160px',
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
                                               height:'160px',
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
                                       // height:'250px',
                                       height:'160px',
                                       background:'#5FB0A8',
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
                      },
                      //bvp
                      {
                       bodyStyle: 'padding-right:5px;',
                       border: false,
                       autoHeight: true,
                       items: [{
                             xtype: 'fieldset',
                             frame: true,
                             layout: 'form',
                             title: ' <br> <br>',
                             border: false,
                             width: 280,
                             id_grupo: 6,
                             items: [],
                      }]
                    },
                      //bvp
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
                             id_grupo: 10,
                             items: [],
                           }]
                      },
                      //bvp
                      {
                       bodyStyle: 'padding-right:5px;',
                       border: false,
                       autoHeight: true,
                       items: [{
                             xtype: 'fieldset',
                             frame: true,
                             layout: 'form',
                             title: ' <br> <br>',
                             border: false,
                             width: 280,
                             id_grupo: 8,
                             items: [],
                      }]
                    },
                      //bvp
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
                      },
                      // {
                      //  bodyStyle: 'padding-right:5px;',
                      //
                      //  border: false,
                      //  items: [{
                      //        xtype: 'fieldset',
                      //        frame: true,
                      //        layout: 'form',
                      //        width: '33%',
                      //        border: false,
                      //        id:'botonFormasPago',
                      //        title: '<br><br>',
                      //        style: {
                      //                 width: '40%',
                      //               },
                      //        padding: '0 0 0 10',
                      //        bodyStyle: 'padding-left:5px;',
                      //        id_grupo: 2,
                      //        items: [{
                      //          xtype:'button',
                      //          id:'dividirFormasPago',
                      //          text:'<img src="../../../lib/imagenes/facturacion/TarjetaCredito.svg" style="width:50px; vertical-align: middle;"><br><span style="font-weight:bold;"> Varias Formas <br>de Pago</span>',
                      //          handler: this.registrarVariasFormasPago,
                      //          scope:this,
                      //          scale: 'medium'
                      //        }],
                      //     }]
                      // },

                      ]
                            }



                         ]
                 }];


    },
    // crearStoreFormaPago : function () {
    // 	this.storeFormaPago = new Ext.data.JsonStore({
    // 	url: '../../sis_ventas_facturacion/control/FormaPago/listarFormaPago',
		// 	id: 'id_forma_pago',
		// 	root: 'datos',
		// 	sortInfo: {
		// 		field: 'id_forma_pago',
		// 		direction: 'ASC'
		// 	},
		// 	totalProperty: 'total',
		// 	fields: [
	  //          {name: 'id_forma_pago',type: 'numeric'},
	  //          {name: 'nombre',      type: 'string'},
	  //          {name: 'valor',     type: 'numeric'},
	  //          {name: 'numero_tarjeta',     type: 'string'},
	  //          {name: 'codigo_tarjeta',     type: 'string'},
	  //          {name: 'registrar_tarjeta',     type: 'string'},
    //          {name: 'registrar_tipo_tarjeta',     type: 'string'},
	  //          {name: 'registrar_cc',     type: 'string'},
	  //          {name: 'tipo_tarjeta',     type: 'string'}
	  //       ]
		// });
		// if (this.data.objPadre.variables_globales.vef_tiene_punto_venta === 'true') {
		//   this.storeFormaPago.baseParams.id_punto_venta = this.Cmp.id_punto_venta.getValue();
		// }
		// this.storeFormaPago.baseParams.id_sucursal = this.Cmp.id_sucursal.getValue();
		// this.storeFormaPago.baseParams.id_venta = this.Cmp.id_venta.getValue();
		// this.storeFormaPago.load({params:{start:0,limit:100}});
    // },

    loadValoresIniciales:function()
    {
       Phx.vista.FormCajero.superclass.loadValoresIniciales.call(this);
    },
    onReset:function(o){
			this.generar = 'generar';

      var verificar_montos = [];
      var total_datos = this.megrid.store.data.items.length;

      for (var i = 0; i < total_datos; i++) {
        if (this.megrid.store.data.items[i].data.precio_total == 0 || isNaN(this.megrid.store.data.items[i].data.precio_total) || this.megrid.store.data.items[i].data.precio_total == '') {
          verificar_montos.push(this.megrid.store.data.items[i].data.precio_total);
        }
      }

      if (this.mestore.modified.length == 0 && verificar_montos.length == 0) {
          this.onSubmit2(o);
      } else if (verificar_montos.length > 0) {
            Ext.Msg.show({
             title:'Información',
             maxWidth : 550,
             width: 550,
             msg: 'Hay conceptos que no tienen precio unitario o el monto es 0, favor verifique y complete la información!',
             buttons: Ext.Msg.OK,
             icon: Ext.MessageBox.QUESTION,
             scope:this
          });
          verificar_montos = [];

      } else if (this.mestore.modified.length > 0) {
        Ext.Msg.show({
         title:'Información',
         maxWidth : 550,
         width: 550,
         msg: 'Guarde la información modificada para obtener el total correcto y poder generar el recibo!',
         buttons: Ext.Msg.OK,
         icon: Ext.MessageBox.QUESTION,
         scope:this
      });
      }
	   },

     onSubmit:function(o){

       var requiere_excento = [];
       var requiere_asociar = [];

       for (var i = 0; i < this.mestore.data.items.length; i++) {
         requiere_excento.push(this.mestore.data.items[i].data.requiere_excento);
       }

       for (var i = 0; i < this.mestore.data.items.length; i++) {
         requiere_asociar.push(this.mestore.data.items[i].data.asociar_boletos);
       }


       if (requiere_excento.includes( 'si' )) {
         // if (this.Cmp.excento.getValue() == 0) {
         //   Ext.Msg.show({
         //       title:'Información',
         //       msg: 'Tiene un concepto que requiere un valor exento y el valor exento no puede ser 0',
         //       maxWidth : 550,
         //       width: 550,
         //       buttons: Ext.Msg.OK,
         //        icon: Ext.MessageBox.QUESTION,
         //       scope:this
         //    });
         // } else {
           this.registrarVariasFormasPago();
         //}
       } else if (requiere_asociar.includes( 'si' )) {
          if (this.Cmp.boleto_asociado.getValue() == '') {
           Ext.Msg.show({
               title:'Información',
               msg: 'Tiene un concepto que requiere Asociar un boleto Favor verifique',
               maxWidth : 550,
               width: 550,
               buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.QUESTION,
               scope:this
            });
         } else {
           this.registrarVariasFormasPago();
         }
       }
       // else if (this.Cmp.id_cliente.getValue() == '' || this.Cmp.id_cliente.getValue() == null) {
       //      Ext.Msg.show({
       //          title:'Información',
       //          msg: 'Favor Complete datos en la Cabecera.',
       //          maxWidth : 550,
       //          width: 550,
       //          buttons: Ext.Msg.OK,
       //           icon: Ext.MessageBox.QUESTION,
       //          scope:this
       //       });
       //    }
       else if (this.Cmp.nombre_factura.getValue() == '' || this.Cmp.nombre_factura.getValue() == null) {
            Ext.Msg.show({
                title:'Información',
                msg: 'Favor Complete datos en la Cabecera.',
                maxWidth : 550,
                width: 550,
                buttons: Ext.Msg.OK,
                 icon: Ext.MessageBox.QUESTION,
                scope:this
             });
          }

          else if (this.mestore.data.items.length == 0) {
            Ext.Msg.show({
                title:'Información',
                msg: 'Favor Complete datos en el detalle de Conceptos.',
                maxWidth : 550,
                width: 550,
                buttons: Ext.Msg.OK,
                 icon: Ext.MessageBox.QUESTION,
                scope:this
             });

          } else {
            this.registrarVariasFormasPago();
          }

 	   },

     successWizard:function(resp){
         // var rec=this.sm.getSelected();
         var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
         if (objRes.ROOT.datos.estado == 'finalizado' && this.tipo_factura != 'manual') {
             this.id_venta = objRes.ROOT.datos.id_venta;
             this.id_proceso_wf = objRes.ROOT.datos.id_proceso_wf;
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
         if (this.data.objPadre.tipo_punto_venta == 'ato') {
           if (this.data.objPadre.variables_globales.formato_comprobante == 'carta') {
           		Ext.Ajax.request({
           						url : '../../sis_ventas_facturacion/control/Cajero/reporteFacturaCarta',
           						params : {
                        'id_proceso_wf' : this.id_proceso_wf ,
           							'id_punto_venta' : this.data.objPadre.variables_globales.id_punto_venta,
           							'formato_comprobante' : this.data.objPadre.variables_globales.formato_comprobante,
           							'tipo_factura': this.data.objPadre.tipo_factura
           						},
           						success : this.successExport,
           						failure : this.conexionFailure,
           						timeout : this.timeout,
           						scope : this
           					});
               } else if (this.data.objPadre.variables_globales.formato_comprobante == 'rollo'){
                  Ext.Ajax.request({
               						url : '../../sis_ventas_facturacion/control/Cajero/reporteFactura',
               						params : {
                            'id_proceso_wf' : this.id_proceso_wf ,
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
        } else {
            if (this.data.objPadre.variables_globales.formato_comprobante == 'carta') {
              Ext.Ajax.request({
       						url : '../../sis_ventas_facturacion/control/Cajero/reporteFacturaCarta',
       						params : {
                    'id_proceso_wf' : this.id_proceso_wf  ,
       							'id_punto_venta' : this.data.objPadre.variables_globales.id_punto_venta,
       							'formato_comprobante' : this.data.objPadre.variables_globales.formato_comprobante,
       							'tipo_factura': this.data.objPadre.tipo_factura
       						},
       						success : this.successExport,
       						failure : this.conexionFailure,
       						timeout : this.timeout,
       						scope : this
       					});
            } else if (this.data.objPadre.variables_globales.formato_comprobante == 'rollo'){
              Ext.Ajax.request({
       						url : '../../sis_ventas_facturacion/control/Cajero/reporteFactura',
       						params : {
                    'id_proceso_wf' : this.data.datos_originales.data.id_proceso_wf ,
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
				name: 'nit',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/CarnetIdentidad.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> NIT</span>',
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
 		 config:{
 			 name: 'nombre_factura',
 			 fieldLabel: '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Razón Social</span>',
 			 allowBlank: false,
 			 width:200,
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
		// {
		// 	config : {
		// 		name : 'id_cliente',
		// 		fieldLabel : '<img src="../../../lib/imagenes/facturacion/conversacion.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Razón Social</span>',
    //     style:{
    //     //  width:'5000px',
    //       textTransform:'uppercase',
    //     },
    //     width:200,
		// 		allowBlank : false,
    //     // listeners: {
    //     //   afterrender: function(field) {
    //     //     field.focus(false);
    //     //   }
    //     // },
		// 		emptyText : 'Cliente...',
		// 		store : new Ext.data.JsonStore({
		// 			url : '../../sis_ventas_facturacion/control/Cliente/listarCliente',
		// 			id : 'id_cliente',
		// 			root : 'datos',
		// 			sortInfo : {
		// 				field : 'id_cliente',
		// 				direction : 'DESC'
		// 			},
		// 			totalProperty : 'total',
		// 			fields : ['id_cliente', 'nombres', 'primer_apellido', 'segundo_apellido','nombre_factura','nit'],
		// 			remoteSort : true,
		// 			baseParams : {
		// 				par_filtro : 'cli.nombres#cli.primer_apellido#cli.segundo_apellido#nombre_factura#nit'
		// 			}
		// 		}),
		// 		valueField : 'id_cliente',
		// 		displayField : 'nombre_factura',
		// 		gdisplayField : 'nombre_factura',
		// 		hiddenName : 'id_cliente',
		// 		forceSelection : false,
		// 		typeAhead : false,
		// 		tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:red;"><b style="color:black;">NIT:</b> <b>{nit}</b></p><b><p>Cliente:<font color="#000CFF" weight="bold"> {nombre_factura}</font></b></p></div></tpl>',
		// 		triggerAction : 'all',
		// 		lazyRender : true,
		// 		mode : 'remote',
		// 		pageSize : 10,
    //     listWidth:'450',
    //     maxHeight : 450,
		// 		queryDelay : 1000,
		// 		turl:'../../../sis_ventas_facturacion/vista/cliente/Cliente.php',
		// 		ttitle:'Clientes',
		// 		tasignacion : true,
		// 		tname : 'id_cliente',
		// 		tdata:{},
		// 		cls:'uppercase',
		// 		tcls:'Cliente',
		// 		gwidth : 170,
		// 		minChars : 2,
		// 		//style:';'
		// 	},
		// 	type : 'TrigguerCombo',
		// 	id_grupo : 0,
		// 	form : true
		// },
    {
			config : {
				name : 'id_formula',
				fieldLabel : '<img src="../../../lib/imagenes/facturacion/paquete.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Paquetes / Fórmulas</span>',
				allowBlank : true,
        width:200,
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
						par_filtro : 'form.nombre',
            emision:'FACTCOMP'
					}
				}),
				valueField : 'id_formula',
				displayField : 'nombre',
				gdisplayField : 'nombre',
				hiddenName : 'id_formula',
				forceSelection : false,
				typeAhead : false,
				// tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Nombre:</b> {nombre}</p><p><b>Descripcion:</b> {descripcion}</p></div></tpl>',
        tpl:'<tpl for="."><div class="x-combo-list-item"><p> {nombre}</p></div></tpl>',
				triggerAction : 'all',
				lazyRender : true,
				mode : 'remote',
				pageSize : 10,
				queryDelay : 1000,
				cls:'uppercase',
				gwidth : 500,
				minChars : 2,
				style:'text-transform:uppercase;'
			},
			type : 'ComboBox',
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
	  //           //form:false
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
            fieldLabel: '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Moneda</span>',
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
            fieldLabel: '<img src="../../../lib/imagenes/facturacion/TarjetaCreditos.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> N° Tarjeta</span>',
            allowBlank: true,
            width:150,
            maxLength:20,
            minLength:15

        },
            type:'TextField',
            id_grupo:6,
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
        id_grupo:6,
        grid:true,
        form:true
    },
    {
        config:{
            name: 'codigo_tarjeta',
            fieldLabel: '<img src="../../../lib/imagenes/facturacion/Codigo.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Codigo de Autorización</span>',
            allowBlank: false,
            width:150,
            minLength:6,
            maxLength:6,
            style:'text-transform:uppercase;',
            maskRe: /[a-zA-Z0-9]+/i,
            regex: /[a-zA-Z0-9]+/i

        },
            type:'TextField',
            id_grupo:6,
            form:true
    },
    {
			config: {
				name: 'id_auxiliar',
				fieldLabel: '<img src="../../../lib/imagenes/facturacion/CuentaCorriente.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Cuenta Corriente</span>',
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
			id_grupo: 6,
			grid: true,
			form: true
		},
    {
        config:{
            name: 'monto_forma_pago',
            fieldLabel: '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Importe Recibido</span>',
            allowBlank: false,
            width:150,
            maxLength:20,
            allowNegative:false,
            value:0
        },
            type:'NumberField',
            id_grupo:2,
            form:true,
            valorInicial:'0'
    },
    {
        config:{
            name: 'tipo_tarjeta',
            fieldLabel: 'Tipo Tarjeta',
            allowBlank: false,
            width:150,
            hidden:true,
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
    //         config:{
    //             name: 'tipo_tarjeta',
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
    //         id_grupo:2,
    //         form:true
    //     },
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
                fieldLabel: '<img src="../../../lib/imagenes/facturacion/MonedaDolar.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Moneda</span>',
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
        //             baseParams: {par_filtro: 'forpa.nombre#forpa.codigo#mon.codigo_internacional',sw_tipo_venta:'computarizada'}
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
    				fieldLabel: '<img src="../../../lib/imagenes/facturacion/CuentaCorriente.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Cuenta Corriente</span>',
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
    			id_grupo: 8,
    			grid: true,
    			form: true
    		},
        {
            config:{
                name: 'numero_tarjeta_2',
                fieldLabel: '<img src="../../../lib/imagenes/facturacion/TarjetaCreditos.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> N° Tarjeta</span>',
                allowBlank: true,
                //disabled:true,
                width:150,
                gwidth: 150,
                maxLength:20,
                minLength:15
            },
            type:'TextField',
            id_grupo:8,
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
            id_grupo:8,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'codigo_tarjeta_2',
                fieldLabel: '<img src="../../../lib/imagenes/facturacion/Codigo.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Codigo de Autorización</span>',
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
            id_grupo:8,
            grid:false,
            form:true
        },
        {
            config:{
                name: 'monto_forma_pago_2',
                fieldLabel: '<img src="../../../lib/imagenes/facturacion/BolsaDinero.svg" style="width:20px; vertical-align: middle;"><span style="vertical-align: middle;"> Importe Recibido</span>',
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
                hidden:true,
                //maxLength:20,
                //allowNegative:false,
                //value:0
            },
                type:'TextField',
                id_grupo:8,
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
        this.mestore.baseParams.id_venta = this.Cmp.id_venta.getValue();
        /*Comentando para incluir InstanciaPago*/
        //this.Cmp.id_forma_pago.store.baseParams.defecto = 'si';
        this.Cmp.id_medio_pago.store.baseParams.defecto = 'si';
        this.Cmp.id_moneda.store.baseParams.filtrar_base = 'si';
        this.mestore.load();

    },
    onNew: function(){
    	this.accionFormulario = 'NEW';
	},

    registrarVariasFormasPago: function(){

      /*Datos a enviar al siguiente Formulario*/
      var total_datos = this.megrid.store.data.items.length;
      var suma = 0;
      for (var i = 0; i < total_datos; i++) {
          suma = suma + parseFloat(this.megrid.store.data.items[i].data.precio_total);
      }
      this.suma_total = suma;

      var cliente = this.Cmp.id_cliente.getValue();
      var nit = this.Cmp.nit.getValue();
      var observaciones = this.Cmp.observaciones.getValue();
      var paquetes = this.Cmp.id_formula.getValue();
      var exento = this.Cmp.excento.getValue();
      var sucursal = this.Cmp.id_sucursal.getValue();
      var puntoVenta = this.Cmp.id_punto_venta.getValue();
      var detalleConceptos = this.mestore.data.items;
      var moneda_1 = this.Cmp.id_moneda.getValue();
      var desc_moneda1 = this.Cmp.id_moneda.lastSelectionText;
      var moneda_2 = this.Cmp.id_moneda_2.getValue();
      var desc_moneda2 = this.Cmp.id_moneda_2.lastSelectionText;
      var medio_pago_1 = this.Cmp.id_medio_pago.getValue();
      var desc_medio_pago_1 = this.Cmp.id_medio_pago.lastSelectionText;
      var medio_pago_2 = this.Cmp.id_medio_pago_2.getValue();
      var desc_medio_pago_2 = this.Cmp.id_medio_pago_2.lastSelectionText;
      var monto_mp_1 = this.Cmp.monto_forma_pago.getValue();
      var monto_mp_2 = this.Cmp.monto_forma_pago_2.getValue();
      var nro_tarjeta_1 = this.Cmp.numero_tarjeta.getValue();
      var nro_tarjeta_2 = this.Cmp.numero_tarjeta_2.getValue();
      var codigo_autorizacion_1 = this.Cmp.codigo_tarjeta.getValue();
      var mco1 = this.Cmp.mco.getValue();
      var codigo_autorizacion_2 = this.Cmp.codigo_tarjeta_2.getValue();
      var mco2 = this.Cmp.mco_2.getValue();
      var id_auxiliar = this.Cmp.id_auxiliar.getValue();
      var desc_id_auxiliar = this.Cmp.id_auxiliar.lastSelectionText;
      var id_auxiliar_2 = this.Cmp.id_auxiliar_2.getValue();
      var desc_id_auxiliar2 = this.Cmp.id_auxiliar_2.lastSelectionText;
      var variables_globales = this.data.objPadre.variables_globales;
      var total_pagar = this.suma_total;
      var tipo_cambio = this.tipo_cambio;

      var tipo_punto_venta = this.data.objPadre.tipo_punto_venta;

      if (this.data.datos_originales == undefined) {
        var  id_venta = '';
      } else {
        var  id_venta = this.data.datos_originales.data.id_venta;
      }
      /****************************************/
          Phx.CP.loadWindows(this.formUrl,
                                   '<center><img src="../../../lib/imagenes/facturacion/TarjetaCredito.svg" style="width:35px; vertical-align: middle;"> <span style="vertical-align: middle; font-size:30px; text-shadow: 3px 0px 0px #000000;"> REGISTRAR FORMAS DE PAGO</span></center>',
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
                                                   this.hide();
                                           }
                                           );
                                   },
                                 },
                                 {data:
                                   { cliente: cliente,
                                     nit: nit,
                                     observaciones: observaciones,
                                     paquetes: paquetes,
                                     exento: exento,
                                     sucursal: sucursal,
                                     puntoVenta: puntoVenta,
                                     detalleConceptos: detalleConceptos,
                                     moneda_1: moneda_1,
                                     moneda_2: moneda_2,
                                     medio_pago_1: medio_pago_1,
                                     medio_pago_2: medio_pago_2,
                                     monto_mp_1: monto_mp_1,
                                     monto_mp_2: monto_mp_2,
                                     nro_tarjeta_1: nro_tarjeta_1,
                                     nro_tarjeta_2: nro_tarjeta_2,
                                     codigo_autorizacion_1: codigo_autorizacion_1,
                                     codigo_autorizacion_2: codigo_autorizacion_2,
                                     variables_globales: variables_globales,
                                     total_pagar: total_pagar,
                                     tipo_cambio: tipo_cambio,
                                     panel_padre: this.panel,
                                     tipo_factura: 'computarizada',

                                     desc_moneda1: desc_moneda1,
                                     desc_moneda2:desc_moneda2,
                                     desc_medio_pago_1:desc_medio_pago_1,
                                     desc_medio_pago_2:desc_medio_pago_2,
                                     mco1:mco1,
                                     mco2:mco2,
                                     id_auxiliar:id_auxiliar,
                                     desc_id_auxiliar:desc_id_auxiliar,
                                     id_auxiliar_2:id_auxiliar_2,
                                     desc_id_auxiliar2:desc_id_auxiliar2,
                                     id_venta:id_venta,
                                     tipo_punto_venta:tipo_punto_venta,
                                     asociar_boletos:this.Cmp.boleto_asociado.getValue()

                                   }
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

    onSubmit2: function(o) {
        //  validar formularios
        console.log("que es esto",this);
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
             Phx.vista.FormCajero.superclass.onSubmit.call(this,o);
        }
        else{
            alert('La venta no tiene registrado ningun detalle');
        }
    },

    successSave:function(resp)
    {
    	var datos_respuesta = JSON.parse(resp.responseText);
    	Phx.CP.loadingHide();
      if (this.generar == 'generar') {
        //Phx.CP.loadingShow();
  			var d = datos_respuesta.ROOT.datos;
        if (this.data.objPadre.tipo_punto_venta == 'ato') {
  			Ext.Ajax.request({
  					url:'../../sis_ventas_facturacion/control/Cajero/FinalizarFactura',
  					params:{id_estado_wf_act:d.id_estado_wf,
  									id_proceso_wf_act:d.id_proceso_wf,
  								  tipo:'recibo'},
  					success:this.successWizard,
  					failure: this.conexionFailure,
  					timeout:this.timeout,
  					scope:this
  			});
      } else {
        Ext.Ajax.request({
  					url:'../../sis_ventas_facturacion/control/Cajero/siguienteEstadoFactura',
  					params:{id_estado_wf_act:d.id_estado_wf,
  									id_proceso_wf_act:d.id_proceso_wf,
  								  tipo:'recibo'},
  					success:this.successWizard,
  					failure: this.conexionFailure,
  					timeout:this.timeout,
  					scope:this
  			});

      }

      }

      if ('cambio' in datos_respuesta.ROOT.datos) {
        Phx.CP.getPagina(this.idContenedorPadre).reload();
        this.panel.close();
        //Comentando el mensaje temporalmente
          //   Ext.Msg.show({
          //    title:'DEVOLUCION',
          //    msg: 'Debe devolver ' + datos_respuesta.ROOT.datos.cambio + ' al cliente',
          //    buttons: Ext.Msg.OK,
          //    fn: function () {
          //       Phx.CP.getPagina(this.idContenedorPadre).reload();
          //       this.panel.close();
          //    },
          //    scope:this
          // });
      } else {
        Phx.CP.getPagina(this.idContenedorPadre).reload();
        this.panel.close();
      }

    },

    falla:function(resp){
      var datos_respuesta = JSON.parse(resp.responseText);
      this.panel.close();
      if (datos_respuesta.ROOT.error == true) {
          Ext.Msg.show({
           title:'<h1 style="color:red"><center>DOSIFICACION INACTIVA</center></h1>',
           msg: datos_respuesta.ROOT.detalle.mensaje + '.' + '<br><br>Consulte con el administrador de ventas.',
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
