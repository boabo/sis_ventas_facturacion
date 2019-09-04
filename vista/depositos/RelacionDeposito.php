<?php
/**
 *@package pXP
 *@file gen-AperturaCierreCaja.php
 *@author  (ivaldivia)
 *@date 07-07-2016 14:16:20
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>

    Phx.vista.RelacionDeposito=Ext.extend(Phx.gridInterfaz, {
        constructor: function (config) {
          	this.maestro=config.maestro;
            //llama al constructor de la clase padre

            /*Seleccionar el punto de venta*/
            Ext.Ajax.request({
                url:'../../sis_ventas_facturacion/control/Venta/getVariablesBasicas',
                params: {'prueba':'uno'},
                success:this.successGetVariables,
                failure: this.conexionFailure,
                arguments:config,
                timeout:this.timeout,
                scope:this
            });
            /*******************************/



        },

        successGetVariables : function (response,request) {
          Phx.vista.RelacionDeposito.superclass.constructor.call(this, request.arguments);
          this.init();

          this.finCons = true;
          this.store.baseParams.tipo = 'venta_propia';
          this.store.baseParams.pes_estado = 'pendiente';
          this.store.baseParams.relacion_deposito = 'venta_propia_agrupada';
          this.bbar.el.dom.style.background='#a3c9f7';
          this.tbar.el.dom.style.background='#a3c9f7';
          this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#DEFAF4';
          this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#C7EAE3';
          //this.load({params:{start:0, limit:this.tam_pag}})

          this.punto_venta = new Ext.form.Label({
              name: 'punto_venta',
              grupo: this.bactGroups,
              fieldLabel: 'P.V.',
              readOnly:true,
              anchor: '150%',
              gwidth: 150,
              format: 'd/m/Y',
              hidden : false,
              style: {
    						fontSize:'230%',
    						fontWeight:'bold',
    						color:'#4D00BD',
    						textShadow: '0.5px 0.5px 0px #FFFFFF, 1px 0px 0px rgba(0,0,0,0.15)',
    						marginLeft:'20px'
    					}
          });
          this.tbar.addField(this.punto_venta);
          //this.iniciarEventos();
          this.seleccionarPuntoVentaSucursal();

        },

         bactGroups:  [0,1,2,3],

        seleccionarPuntoVentaSucursal : function () {

            var storeCombo = new Ext.data.JsonStore({
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
            });


            storeCombo.load({params:{start:0,limit:this.tam_pag},
                callback : function (r) {
                  /*Cambiando la condicion para cuando el usuario solo tenga un punto de venta 1 por 0*/
                  //if (r.length == 1 ) {
                    if (r.length == 0 ) {
                        console.log("llega aqui el id_sucursal");
                        this.id_punto_venta = r[0].data.id_punto_venta;
                        this.store.baseParams.id_punto_venta = r[0].data.id_punto_venta;
                        this.punto_venta.setText(r[0].data.nombre);
                        this.argumentExtraSubmit.id_punto_venta = this.id_punto_venta;
                        this.load({params:{start:0, limit:this.tam_pag}});
                    } else {

                        var combo2 = new Ext.form.ComboBox(
                            {
                                typeAhead: false,
                                fieldLabel: 'Punto de Venta',
                                allowBlank : false,
                                store: storeCombo,
                                mode: 'remote',
                                pageSize: 15,
                                triggerAction: 'all',
                                valueField : 'id_punto_venta',
                                displayField : 'nombre',
                                forceSelection: true,
                                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                                allowBlank : false,
                                anchor: '100%'
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
                            width: 550,
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
                                            VentanaInicio.close();
                                            this.id_punto_venta  = combo2.getValue();
                                            this.store.baseParams.id_punto_venta = combo2.getValue();
                                            this.argumentExtraSubmit.id_punto_venta = this.id_punto_venta;
                                            this.iniciarEventos();
                                            this.punto_venta.setText(combo2.lastSelectionText)
                                            this.load({params:{start:0, limit:this.tam_pag}});
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
                        VentanaInicio.mask.dom.style.opacity='0.8';
                        VentanaInicio.body.dom.childNodes[0].firstChild.firstChild.style.background='#a3c9f7';
                        console.log("ventana",VentanaInicio.mask.dom.style);
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
        gruposBarraTareas:[{name:'pendiente',title:'<H1 align="center" style="color:red; font-size:15px;"><i style="color:red;" class="fa fa-folder-open"></i> Pendientes</h1>',grupo:0,height:0},
                           {name:'registrado',title:'<H1 align="center" style="color:green; font-size:15px;"><i style="color:green;" class="fa fa-folder"></i> Registrados</h1>',grupo:1,height:0}],
        actualizarSegunTab: function(name, indice){
            if(this.finCons) {
              console.log("thisact",this);
                this.store.baseParams.pes_estado = name;
                this.load({params:{start:0, limit:this.tam_pag}});
            }
        },
        bactGroups:  [0,1],
        bexcelGroups: [0,1],

        iniciarEventos : function () {
          console.log("llega aqui iniciar",this.id_punto_venta);
          /*Seleccionar el punto de venta*/
          //this.tipo_usuario = 'cajero';
      		Ext.Ajax.request({
      				url:'../../sis_ventas_facturacion/control/AperturaCierreCajaAsociada/getDatosSucursal',
              params: {'id_punto_venta':this.id_punto_venta},
      				success: function(resp){
      						var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                  this.codigo_padre = reg.ROOT.datos.v_codigo_padre;
                  this.estacion = reg.ROOT.datos.v_estacion;
                  this.codigo = reg.ROOT.datos.v_codigo;
                  this.fecha_venta = reg.ROOT.datos.v_fecha_venta;
      						this.tipo_cambio = reg.ROOT.datos.v_tipo_cambio;
      				},
      				failure: this.conexionFailure,
      				timeout:this.timeout,
      				scope:this
      		});
          /*******************************/

        },
        successGetDatosSucursal : function () {
          var respuesta = JSON.parse(response.responseText);
          console.log("llega respuesta",respuesta);
    			if('datos' in respuesta){
    					this.variables_globales = respuesta.datos;
    			}
        },

        Atributos: [
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_deposito'
                },
                type: 'Field',
                form: true
            },
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_apertura_cierre_caja'
                },
                type: 'Field',
                form: true
            },
            /******************DATOS DEL PUNTO DE VENTA*************************/
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'codigo_padre'
                },
                type: 'Field',
                form: true
            },
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'estacion'
                },
                type: 'Field',
                form: true
            },
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'codigo'
                },
                type: 'Field',
                form: true
            },
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'fecha_venta'
                },
                type: 'Field',
                form: true
            },
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'tipo_cambio'
                },
                type: 'Field',
                form: true
            },
            /**************************************/
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_punto_venta'
                },
                type: 'Field',
                form: true

            },
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'tipo'
                },
                type: 'Field',
                form: true,
                valorInicial : 'venta_propia'
            },
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'relacion_deposito'
                },
                type: 'Field',
                form: true,
                valorInicial : 'venta_propia_agrupada'
            },
            {
                config: {
                    name: 'nro_deposito',
                    fieldLabel: 'No Deposito',
                    allowBlank: false,
                    anchor: '80%',
                    renderer: function (value, p, record) {
                      if (record.data['id_moneda_deposito'] == 2) {
                        return String.format('<b style="font-size:14px; font-weight:bold; color:green; ">{0}</b>', record.data['nro_deposito']);
                      } else {
                        return String.format('<b style="font-size:14px; font-weight:bold; color:blue; ">{0}</b>', record.data['nro_deposito']);
                      }
                    },
                    gwidth: 150,
                    maxLength: 70
                },
                type: 'TextField',
                filters: {pfiltro: 'dep.nro_deposito', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter:true
            },
            {
                config: {
                    name: 'id_moneda_deposito',
                    origen: 'MONEDA',
                    allowBlank: false,
                    fieldLabel: 'Moneda Deposito',
                    gdisplayField: 'desc_moneda',//mapea al store del grid
                    gwidth: 100,
                  	anchor: '80%',
                    renderer: function (value, p, record) {
                      if (record.data['id_moneda_deposito'] == 2) {
                        return String.format('<b style="font-size:14px; font-weight:bold; color:green; ">{0}</b>', record.data['desc_moneda']);
                      } else {
                        return String.format('<b style="font-size:14px; font-weight:bold; color:blue; ">{0}</b>', record.data['desc_moneda']);
                      }
                    },
                },
                type: 'ComboRec',
                id_grupo: 1,
                filters: {
                    pfiltro: 'mon.codigo',
                    type: 'string'
                },
                grid: true,
                form: true,
            },
            {
                    config: {
                        name: 'monto_deposito',
                        fieldLabel: 'Monto Depósito',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 150,
                        gright: true,
                        maxLength: 1179650,
                        decimalPrecision:2,
                        renderer:function (value,p,record) {
                        var dato =  value.replace('.', ",")
                                .replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");
                        if (record.data['id_moneda_deposito'] == 2) {
                          return '<div style="font-size:14px; text-align:right; font-weight:bold; ext:qtip="Optimo"><p> <font color="green"><b>'+dato+'</b></font></p></div>';
                        } else {
                          return '<div style="font-size:14px; text-align:right; font-weight:bold; ext:qtip="Optimo"><p> <font color="blue"><b>'+dato+'</b></font></p></div>';
                        }
                        }
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'dep.monto_deposito', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'monto_total_ml',
                        fieldLabel: 'Monto Total Ventas M/L',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength: 70,
                        renderer:function (value,p,record) {
                        var dato =  value.replace('.', ",")
                                .replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");
                        if (record.data['id_moneda_deposito'] == 2) {
                          return '<div style="font-size:14px; text-align:right; font-weight:bold; ext:qtip="Optimo"><p> <font color="green"><b>'+dato+'</b></font></p></div>';
                        } else {
                          return '<div style="font-size:14px; text-align:right; font-weight:bold; ext:qtip="Optimo"><p> <font color="blue"><b>'+dato+'</b></font></p></div>';
                        }
                        }
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'dep.nro_deposito', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'monto_total_me',
                        fieldLabel: 'Monto Total Ventas M/E',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength: 70,
                        renderer:function (value,p,record) {
                        var dato =  value.replace('.', ",")
                                .replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");
                        if (record.data['id_moneda_deposito'] == 2) {
                          return '<div style="font-size:14px; text-align:right; font-weight:bold; ext:qtip="Optimo"><p> <font color="green"><b>'+dato+'</b></font></p></div>';
                        } else {
                          return '<div style="font-size:14px; text-align:right; font-weight:bold; ext:qtip="Optimo"><p> <font color="blue"><b>'+dato+'</b></font></p></div>';
                        }
                        }
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'dep.nro_deposito', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'diferencia_ml',
                        fieldLabel: 'Diferencia M/L',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength: 70,
                        renderer:function (value,p,record) {
                        var dato =  value.replace('.', ",")
                              .replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");
                        if (record.data['id_moneda_deposito'] == 2) {
                          return '<div style="font-size:14px; text-align:right; font-weight:bold; ext:qtip="Optimo"><p> <font color="green"><b>'+dato+'</b></font></p></div>';
                        } else if (record.data['diferencia_ml'] != '0.00') {
                          return '<div style="font-size:14px; text-align:right; font-weight:bold; ext:qtip="Optimo"><p> <font color="red"><b>'+dato+'</b></font></p></div>';
                        } else if (record.data['id_moneda_deposito'] != 2) {
                          return '<div style="font-size:14px; text-align:right; font-weight:bold; ext:qtip="Optimo"><p> <font color="blue"><b>'+dato+'</b></font></p></div>';
                        }
                        }
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'dep.nro_deposito', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'diferencia_me',
                        fieldLabel: 'Diferencia M/E',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength: 70,
                        renderer:function (value,p,record) {
                        var dato =  value.replace('.', ",")
                                .replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");
                                if (record.data['id_moneda_deposito'] == 2 && record.data['diferencia_me'] == '0.00') {
                                  return '<div style="font-size:14px; text-align:right; font-weight:bold; ext:qtip="Optimo"><p> <font color="green"><b>'+dato+'</b></font></p></div>';
                                } else if (record.data['id_moneda_deposito'] == 2 && record.data['diferencia_me'] != '0.00') {
                                  return '<div style="font-size:14px; text-align:right; font-weight:bold; ext:qtip="Optimo"><p> <font color="red"><b>'+dato+'</b></font></p></div>';
                                } else if (record.data['id_moneda_deposito'] != 2) {
                                  return '<div style="font-size:14px; text-align:right; font-weight:bold; ext:qtip="Optimo"><p> <font color="blue"><b>'+dato+'</b></font></p></div>';
                                }

                        }
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'dep.nro_deposito', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
            {
                config: {
                    name: 'fecha',
                    fieldLabel: 'Fecha Deposito',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 120,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                      if (record.data['id_moneda_deposito'] == 2) {
                        return String.format('<b style="font-size:14px; font-weight:bold; color:green; ">{0}</b>', value.dateFormat('d/m/Y'));
                      } else {
                        return String.format('<b style="font-size:14px; font-weight:bold; color:blue; ">{0}</b>', value.dateFormat('d/m/Y'));
                      }
                    },
                },
                type: 'DateField',
                filters: {pfiltro: 'dep.fecha', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    renderer: function (value, p, record) {
                      if (record.data['id_moneda_deposito'] == 2) {
                        return String.format('<b style="font-size:14px; font-weight:bold; color:green; ">{0}</b>', record.data['estado_reg']);
                      } else {
                        return String.format('<b style="font-size:14px; font-weight:bold; color:blue; ">{0}</b>', record.data['estado_reg']);
                      }
                    },
                    maxLength: 10
                },
                type: 'TextField',
                filters: {pfiltro: 'dep.estado_reg', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },


            {
                config: {
                    name: 'usr_reg',
                    fieldLabel: 'Creado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    renderer: function (value, p, record) {
                      if (record.data['id_moneda_deposito'] == 2) {
                        return String.format('<b style="font-size:14px; font-weight:bold; color:green; ">{0}</b>', record.data['usr_reg']);
                      } else {
                        return String.format('<b style="font-size:14px; font-weight:bold; color:blue; ">{0}</b>', record.data['usr_reg']);
                      }
                    },
                    maxLength: 4
                },
                type: 'Field',
                filters: {pfiltro: 'usu1.cuenta', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'fecha_reg',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                      if (record.data['id_moneda_deposito'] == 2) {
                        return String.format('<b style="font-size:14px; font-weight:bold; color:green; ">{0}</b>', value.dateFormat('d/m/Y H:i:s'));
                      } else {
                        return String.format('<b style="font-size:14px; font-weight:bold; color:blue; ">{0}</b>', value.dateFormat('d/m/Y H:i:s'));
                      }
                    },
                },
                type: 'DateField',
                filters: {pfiltro: 'dep.fecha_reg', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'id_usuario_ai',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'Field',
                filters: {pfiltro: 'dep.id_usuario_ai', type: 'numeric'},
                id_grupo: 1,
                grid: false,
                form: false
            },
            {
                config: {
                    name: 'usuario_ai',
                    fieldLabel: 'Funcionaro AI',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 300
                },
                type: 'TextField',
                filters: {pfiltro: 'dep.usuario_ai', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'usr_mod',
                    fieldLabel: 'Modificado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'Field',
                filters: {pfiltro: 'usu2.cuenta', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'fecha_mod',
                    fieldLabel: 'Fecha Modif.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y H:i:s') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'dep.fecha_mod', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: false
            }
        ],
        tam_pag: 50,
        title: 'Depositos',
        fheight:'40%',
        fwidth:'30%',
        ActSave: '../../sis_obingresos/control/Deposito/insertarDeposito',
        ActDel:'../../sis_obingresos/control/Deposito/eliminarDepositoPortal',
        ActList: '../../sis_obingresos/control/Deposito/listarDepositoAgrupado',
        id_store: 'id_deposito',
        fields: [
            {name: 'id_deposito', type: 'numeric'},
            {name: 'estado_reg', type: 'string'},
            {name: 'nro_deposito', type: 'string'},
            {name: 'desc_moneda', type: 'string'},
            {name: 'desc_periodo', type: 'string'},
            {name: 'medio_pago', type: 'string'},
            {name: 'monto_deposito', type: 'numeric'},
            {name: 'id_moneda_deposito', type: 'numeric'},
            {name: 'nombre_agencia', type: 'numeric'},
            {name: 'fecha', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'saldo', type: 'numeric'},
            {name: 'id_usuario_reg', type: 'numeric'},
            {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'id_usuario_ai', type: 'numeric'},
            {name: 'usuario_ai', type: 'string'},
            {name: 'id_usuario_mod', type: 'numeric'},
            {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'usr_reg', type: 'string'},
            {name: 'usr_mod', type: 'string'},
            {name: 'tipo', type: 'string'},
            {name: 'estado', type: 'string'},
            {name: 'id_apertura_cierre_caja', type: 'numeric'},
            {name: 'nro_cuenta', type: 'string'},
            {name: 'monto_total_ml', type: 'string'},
            {name: 'monto_total_me', type: 'string'},
            {name: 'diferencia_ml', type: 'string'},
            {name: 'diferencia_me', type: 'string'},

        ],
        sortInfo: {
            field: 'id_deposito',
            direction: 'DESC'
        },
        tabsouth :[
            {
                url:'../../../sis_ventas_facturacion/vista/apertura_cierre_caja_asociado/AperturaCierreCajaAsociada.php',
                title:'Ventas',
                height:'50%',
                cls:'AperturaCierreCajaAsociada'
            }
        ],

        bdel: true,
        bsave: false,
        bedit:false,

        onButtonNew : function () {
      	    Phx.vista.RelacionDeposito.superclass.onButtonNew.call(this);
      			this.form.el.dom.firstChild.childNodes[0].style.background = '#a3c9f7';
            this.Cmp.id_punto_venta.setValue(this.store.baseParams.id_punto_venta);
            this.Cmp.codigo_padre.setValue(this.codigo_padre);
            this.Cmp.estacion.setValue(this.estacion);
            this.Cmp.codigo.setValue(this.codigo);
            this.Cmp.fecha_venta.setValue(this.fecha_venta);
            this.Cmp.tipo_cambio.setValue(this.tipo_cambio);
            console.log("llega aqui",this);
          },

          onButtonDel: function() {
              Phx.vista.RelacionDeposito.superclass.onButtonDel.call(this);
              this.eliminar();

          },
          eliminar : function () {
              var data = this.getSelectedData();
              Ext.Ajax.request({
                  url:'../../sis_obingresos/control/Deposito/eliminar',
                  params:{'nro_deposito':data.nro_deposito,'codigo':this.codigo,
                          'fecha_venta':this.fecha_venta,'monto_deposito':data.monto_deposito,
                          'desc_moneda':data.desc_moneda},
                  success: this.successWizard,
                  failure: this.conexionFailure,
                  timeout:this.timeout,
                  scope:this
              });
          }

    })


</script>
