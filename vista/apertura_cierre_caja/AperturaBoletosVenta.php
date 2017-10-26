<?php
/**
 *@package pXP
 *@file gen-SistemaDist.php
 *@author  (rarteaga)
 *@date 20-09-2011 10:22:05
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    //Phx.vista.AperturaBoletosVenta
    Phx.vista.AperturaBoletosVenta=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                this.grupo = 'no';
                this.tipo_usuario = 'vendedor';
                Phx.vista.AperturaBoletosVenta.superclass.constructor.call(this,config);
                this.init();
                this.store.baseParams.estado = 'borrador';
                var dataPadre = Phx.CP.getPagina(this.idContenedorPadre).getSelectedData();
                if(dataPadre){
                    this.onEnablePanel(this, dataPadre);
                }
                else {
                    this.bloquearMenus();
                }
                /*
                this.addButton('btnImprimir',
                    {
                        text: 'Imprimir',
                        iconCls: 'bpdf32',
                        disabled: true,
                        handler: this.imprimirBoleto,
                        tooltip: '<b>Imprimir Boleto</b><br/>Imprime el boleto'
                    }
                );*/
            },
            /*imprimirBoleto: function(){
                //Ext.Msg.confirm('Confirmación','¿Está seguro de Imprimir el Comprobante?',function(btn){
                var rec = this.sm.getSelected();
                var data = rec.data;
                if (data) {
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url : '../../sis_obingresos/control/Boleto/reporteBoleto',
                        params : {
                            'id_boleto' : data.id_boleto
                        },
                        success : this.successExport,
                        failure : this.conexionFailure,
                        timeout : this.timeout,
                        scope : this
                    });
                }
            },*/
            /*successExport: function (resp) {

                Phx.CP.loadingHide();
                var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                console.log(objRes);
                var objetoDatos = (objRes.ROOT == undefined)?objRes.datos:objRes.ROOT.datos;
                var objetoDetalle = (objRes.ROOT == undefined)?objRes.detalle:objRes.ROOT.detalle;
                if ("archivo_generado" in objetoDetalle ) {
                    window.open('../../../lib/lib_control/Intermediario.php?r=' + objetoDetalle.archivo_generado + '&t='+new Date().toLocaleTimeString())
                } else {
                    var wnd = window.open("about:blank", "", "_blank");
                    wnd.document.write(objetoDatos.html);
                }

            },*/



            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_boleto'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'moneda_sucursal'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'ids_seleccionados'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'tc'
                    },
                    type:'NumberField',
                    form:true
                },



                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'moneda_fp1'
                    },
                    type:'TextField',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'moneda_fp2'
                    },
                    type:'TextField',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'codigo_forma_pago'
                    },
                    type:'TextField',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'codigo_forma_pago2'
                    },
                    type:'TextField',
                    form:true
                },
                {
                    config:{
                        name: 'voided',
                        fieldLabel: 'Anulado',
                        anchor: '60%',
                        gwidth: 60,
                        readOnly:true,
                        renderer : function(value, p, record) {
                            if (record.data['voided'] != 'si') {
                                return String.format('<div title="Anulado"><b><font color="green">{0}</font></b></div>', value);

                            } else {
                                return String.format('<div title="Anulado"><b><font color="red">{0}</font></b></div>', value);
                            }
                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.voided',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'boletos',
                        fieldLabel: 'Boletos a Pagar',
                        anchor: '80%',
                        gwidth: 80,
                        readOnly:true

                    },
                    type:'TextArea',
                    id_grupo:2,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'localizador',
                        fieldLabel: 'Pnr',
                        anchor: '40%',
                        gwidth: 70
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.localizador',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter: true
                },
                {
                    config:{
                        name: 'nro_boleto',
                        fieldLabel: 'Billete: 930-',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 90,
                        maxLength:10,
                        minLength:10,
                        enableKeyEvents:true,
                        renderer : function(value, p, record) {
                            if (record.data['mensaje_error'] != '') {
                                return String.format('<div title="Error"><b><font color="red">{0}</font></b></div>', value);

                            } else {
                                return String.format('{0}', value);
                            }


                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.nro_boleto',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter: true
                },

                {
                    config:{
                        name: 'tiene_conjuncion',
                        fieldLabel: 'Tiene Conjuncion',
                        anchor: '80%',
                        checked:false

                    },
                    type:'Checkbox',
                    id_grupo:0,
                    grid:false,
                    form:true
                },

                {
                    config:{
                        name: 'nro_boleto_conjuncion',
                        fieldLabel: 'Conjuncion : 930-',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 120,
                        maxLength:10,
                        minLength:10
                    },
                    type:'TextField',
                    id_grupo:0,
                    grid:false,
                    form:true
                },

                {
                    config:{
                        name: 'pasajero',
                        fieldLabel: 'Pasajero',
                        anchor: '100%',
                        gwidth: 130,
                        readOnly:true
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.pasajero',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true,
                    bottom_filter: true
                },
                {
                    config:{
                        name: 'total',
                        fieldLabel: 'Total Boleto',
                        anchor: '80%',
                        gwidth: 80,
                        readOnly:true
                    },
                    type:'NumberField',
                    filters:{pfiltro:'bol.total',type:'numeric'},
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'moneda',
                        fieldLabel: 'Moneda de Emision',
                        anchor: '80%',
                        gwidth: 120,
                        readOnly:true

                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.moneda',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'fecha_emision',
                        fieldLabel: 'Fecha Emision',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'bol.fecha_emision',type:'date'},
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'ruta_completa',
                        fieldLabel: 'Ruta',
                        anchor: '80%',
                        gwidth: 120,
                        readOnly:true

                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.ruta_completa',type:'string'},
                    id_grupo:0,
                    grid:false,
                    form:true
                },

                {
                    config: {
                        name: 'id_boleto_vuelo',
                        fieldLabel: 'Vuelo Ini Retorno',
                        allowBlank: true,
                        emptyText: 'Vuelo...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_obingresos/control/BoletoVuelo/listarBoletoVuelo',
                            id: 'id_boleto_vuelo',
                            root: 'datos',
                            sortInfo: {
                                field: 'cupon',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_boleto_vuelo', 'boleto_vuelo'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'bvu.aeropuerto_origen#bvu.aeropuerto_destino'}
                        }),
                        valueField: 'id_boleto_vuelo',
                        displayField: 'boleto_vuelo',
                        gdisplayField: 'vuelo_retorno',
                        hiddenName: 'id_boleto_vuelo',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        gwidth: 150,
                        listWidth:450,
                        resizable:true,
                        minChars: 2,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['vuelo_retorno']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    grid: false,
                    form: true
                },

                {
                    config:{
                        name: 'estado',
                        fieldLabel: 'Estado',
                        gwidth: 100,
                        readOnly:true
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.estado',type:'string'},
                    grid:true,
                    id_grupo:0,
                    form:true
                },
                {
                    config:{
                        name: 'comision',
                        fieldLabel: 'Comisión AGT',
                        allowBlank:true,
                        anchor: '80%',
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        disabled:true,
                        gwidth: 125
                    },
                    type:'NumberField',
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config: {
                        name: 'id_forma_pago',
                        fieldLabel: 'Forma de Pago1',
                        allowBlank: false,
                        emptyText: 'Forma de Pago...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_ventas_facturacion/control/FormaPago/listarFormaPago',
                            id: 'id_forma_pago',
                            root: 'datos',
                            sortInfo: {
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_forma_pago', 'nombre', 'desc_moneda','registrar_tarjeta','registrar_cc','codigo'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'forpa.nombre#mon.codigo_internacional',fp_ventas:'si'}
                        }),
                        valueField: 'id_forma_pago',
                        displayField: 'nombre',
                        gdisplayField: 'forma_pago',
                        hiddenName: 'id_forma_pago',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre}</p><p>Moneda:{desc_moneda}</p> </div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        gwidth: 150,
                        listWidth:450,
                        resizable:true,
                        minChars: 2,
                        disabled:true,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['forma_pago']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config:{
                        name: 'monto_forma_pago',
                        fieldLabel: 'Monto a Pagar 1',
                        allowBlank:false,
                        anchor: '80%',
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        disabled:true,
                        gwidth: 125
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'numero_tarjeta',
                        fieldLabel: 'No Tarjeta 1',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:50
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'codigo_tarjeta',
                        fieldLabel: 'Codigo de Autorización 1',
                        allowBlank: true,
                        anchor: '80%',
                        maxLength:20

                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'ctacte',
                        fieldLabel: 'Cta. Corriente 1',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:20
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },

                {
                    config: {
                        name: 'id_forma_pago2',
                        fieldLabel: 'Forma de Pago 2',
                        allowBlank: true,
                        emptyText: 'Forma de Pago...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_ventas_facturacion/control/FormaPago/listarFormaPago',
                            id: 'id_forma_pago',
                            root: 'datos',
                            sortInfo: {
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_forma_pago', 'nombre', 'desc_moneda','registrar_tarjeta','registrar_cc','codigo'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'forpa.nombre#mon.codigo_internacional',fp_ventas:'si'}
                        }),
                        valueField: 'id_forma_pago',
                        displayField: 'nombre',
                        gdisplayField: 'forma_pago2',
                        hiddenName: 'id_forma_pago',
                        anchor: '90%',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre}</p><p>Moneda:{desc_moneda}</p> </div></tpl>',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        gwidth: 150,
                        listWidth:450,
                        resizable:true,
                        minChars: 2,
                        disabled:true,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['forma_pago2']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config:{
                        name: 'monto_forma_pago2',
                        fieldLabel: 'Monto a Pagar 2',
                        allowBlank:true,
                        anchor: '80%',
                        allowDecimals:true,
                        decimalPrecision:2,
                        allowNegative : false,
                        disabled:true,
                        gwidth: 125
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'numero_tarjeta2',
                        fieldLabel: 'No Tarjeta 2',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:50
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'codigo_tarjeta2',
                        fieldLabel: 'Codigo de Autorización 2',
                        allowBlank: true,
                        anchor: '80%',
                        maxLength:20

                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'ctacte2',
                        fieldLabel: 'Cta. Corriente 2',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:20
                    },
                    type:'TextField',
                    id_grupo:1,
                    grid:false,
                    form:true
                },



                {
                    config:{
                        name: 'cupones',
                        fieldLabel: 'Cupones',
                        gwidth: 100

                    },
                    type:'NumberField',
                    filters:{pfiltro:'bol.cupones',type:'numeric'},
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'codigo_noiata',
                        fieldLabel: 'Cod. Noiata',
                        gwidth: 100
                    },
                    type:'TextField',

                    grid:true,
                    form:false
                },

                {
                    config:{
                        name: 'codigo_agencia',
                        fieldLabel: 'agt',
                        gwidth: 100
                    },
                    type:'TextField',
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'nombre_agencia',
                        fieldLabel: 'Agencia',
                        gwidth: 120
                    },
                    type:'TextField',
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'neto',
                        fieldLabel: 'Neto',
                        gwidth: 100
                    },
                    type:'NumberField',
                    filters:{pfiltro:'bol.neto',type:'numeric'},
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'tipopax',
                        fieldLabel: 'Tipo Pasajero',
                        gwidth: 110
                    },
                    type:'TextField',
                    filters:{pfiltro:'bol.tipopax',type:'string'},
                    grid:true,
                    form:false
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
                    filters:{pfiltro:'bol.estado_reg',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },

                {
                    config:{
                        name: 'id_usuario_ai',
                        fieldLabel: '',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'Field',
                    filters:{pfiltro:'bol.id_usuario_ai',type:'numeric'},
                    id_grupo:1,
                    grid:false,
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
                    filters:{pfiltro:'bol.fecha_reg',type:'date'},
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
                    filters:{pfiltro:'bol.usuario_ai',type:'string'},
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
                    id_grupo:1,
                    grid:true,
                    form:false
                }
            ],
            tam_pag:50,
            fwidth: '70%',
            title:'Boleto',
            ActSave:'../../sis_obingresos/control/Boleto/modificarBoletoVenta',
            ActDel:'../../sis_obingresos/control/Boleto/eliminarBoleto',
            ActList:'../../sis_obingresos/control/Boleto/listarBoletosEmitidosAmadeus',
            id_store:'id_boleto',
            fields: [
                {name:'id_boleto', type: 'numeric'},
                {name:'fecha_emision', type: 'date',dateFormat:'Y-m-d'},
                {name:'codigo_noiata', type: 'string'},
                {name:'cupones', type: 'numeric'},
                {name:'ruta_completa', type: 'string'},
                {name:'estado', type: 'string'},
                {name:'id_agencia', type: 'numeric'},
                {name:'moneda', type: 'string'},
                {name:'total', type: 'numeric'},
                {name:'pasajero', type: 'string'},
                {name:'id_moneda_boleto', type: 'numeric'},
                {name:'estado_reg', type: 'string'},
                {name:'gds', type: 'string'},
                {name:'comision', type: 'numeric'},
                {name:'codigo_agencia', type: 'string'},
                {name:'neto', type: 'numeric'},
                {name:'tipopax', type: 'string'},
                {name:'origen', type: 'string'},
                {name:'destino', type: 'string'},
                {name:'retbsp', type: 'string'},
                {name:'localizador', type: 'string'},
                {name:'monto_pagado_moneda_boleto', type: 'numeric'},
                {name:'monto_total_fp', type: 'numeric'},
                {name:'tipdoc', type: 'string'},
                {name:'liquido', type: 'numeric'},
                {name:'nro_boleto', type: 'string'},
                {name:'id_usuario_ai', type: 'numeric'},
                {name:'id_usuario_reg', type: 'numeric'},
                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usuario_ai', type: 'string'},
                {name:'id_usuario_mod', type: 'numeric'},
                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mod', type: 'string'},
                {name:'nombre_agencia', type: 'string'},
                {name:'id_forma_pago', type: 'numeric'},
                {name:'id_boleto_vuelo', type: 'numeric'},
                {name:'vuelo_retorno', type: 'string'},
                {name:'forma_pago', type: 'string'},
                {name:'numero_tarjeta', type: 'string'},
                {name:'ctacte', type: 'string'},
                {name:'codigo_forma_pago', type: 'string'},
                {name:'monto_forma_pago', type: 'numeric'},
                {name:'codigo_tarjeta', type: 'string'},
                {name:'mensaje_error', type: 'string'},
                {name:'id_forma_pago2', type: 'numeric'},
                {name:'forma_pago2', type: 'string'},
                {name:'numero_tarjeta2', type: 'string'},
                {name:'ctacte2', type: 'string'},
                {name:'codigo_forma_pago2', type: 'string'},
                {name:'codigo_tarjeta2', type: 'string'},
                {name:'monto_forma_pago2', type: 'numeric'},
                {name:'pais', type: 'string'},
                {name:'moneda_sucursal', type: 'string'},
                {name:'tiene_conjuncion', type: 'string'},
                {name:'tc', type: 'numeric'},
                {name:'moneda_fp1', type: 'string'},
                {name:'moneda_fp2', type: 'string'},
                {name:'voided', type: 'string'}
            ],
            sortInfo:{
                field: 'nro_boleto',
                direction: 'ASC'
            },
            arrayDefaultColumHidden:['estado_reg','usuario_ai',
                'fecha_reg','fecha_mod','usr_reg','usr_mod','estado','cupones','codigo_noiata','codigo_agencia','neto','tipopax','nombre_agencia','comision'],
            rowExpander: new Ext.ux.grid.RowExpander({
                tpl : new Ext.Template(
                    '<br>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha de Emision:&nbsp;&nbsp;</b> {fecha_emision:date("d/m/Y")}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b># Cupones:&nbsp;&nbsp;</b> {cupones}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Nombre Agencia:&nbsp;&nbsp;</b> {nombre_agencia}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Codigo NoIata:&nbsp;&nbsp;</b> {codigo_noiata}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Codigo Agencia:&nbsp;&nbsp;</b> {codigo_agencia}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Neto:&nbsp;&nbsp;</b> {neto}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Comision AGT:&nbsp;&nbsp;</b> {comision}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Tipo de Pasajero:&nbsp;&nbsp;</b> {tipopax}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha de Registro:&nbsp;&nbsp;</b> {fecha_reg:date("d/m/Y")}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha Ult. Modificación:&nbsp;&nbsp;</b> {fecha_mod:date("d/m/Y")}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Creado por:&nbsp;&nbsp;</b> {usr_reg}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Modificado por:&nbsp;&nbsp;</b> {usr_mod}</p><br>'
                )
            }),
            bdel:false,
            bsave:false,
            bedit:false,
            bnew:false,
            preparaMenu:function() {
                Phx.vista.AperturaBoletosVenta.superclass.preparaMenu.call(this);
                //this.getBoton('btnImprimir').enable();
            },
            liberaMenu:function() {
                Phx.vista.AperturaBoletosVenta.superclass.liberaMenu.call(this);
                //this.getBoton('btnImprimir').disable();
            },
        onReloadPage: function (m) {
            this.maestro = m;
            this.store.baseParams = {id_punto_venta: this.maestro.id_punto_venta, tipo_factura: this.tipo_factura, id_usuario_cajero:this.maestro.id_usuario_cajero, pes_estado:'revisados', fecha:this.maestro.fecha_apertura_cierre, id_usuario_cajero:this.maestro.id_usuario_cajero};
            this.store.baseParams.tipo_usuario = this.tipo_usuario;
            this.load({params: {start: 0, limit: 50}});
        }

        }
    )


</script>
