<?php
/**
 *@package pXP
 *@file    FormCierreCaja.php
 *@author  Gonzalo Sarmiento Sejas
 *@date    28-09-2017
 *@description muestra un formulario que muestra el importe contable con el cual sera registrado el deposito
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.FormCierreCaja=Ext.extend(Phx.frmInterfaz,{
        //ActSave:'../../sis_tesoreria/control/ACTProcesoCaja/importeContableDeposito',
        layout:'fit',
        maxCount:0,
        constructor:function(config){

            this.Grupos = [
                {
                    layout: 'column',
                    border: false,
                    defaults: {
                        border: false
                    },
                    items: [
                        {
                            bodyStyle: 'padding-right:10px;',
                            items: [

                                {
                                    xtype: 'fieldset',
                                    title: 'Apertura',
                                    autoHeight: true,
                                    //layout:'hbox',
                                    items: [],
                                    id_grupo:0
                                }
                            ]
                        }, {

                            bodyStyle: 'padding-right:10px;',
                            items: [

                                {
                                    xtype: 'fieldset',
                                    title: 'Total Boletos Bs',
                                    autoHeight: true,
                                    //layout:'hbox',
                                    items: [],
                                    id_grupo:1
                                },
                                {
                                    xtype: 'fieldset',
                                    title: 'Total Boletos USD',
                                    autoHeight: true,
                                    //layout:'hbox',
                                    items: [],
                                    id_grupo:2
                                }
                            ]
                        },{
                            bodyStyle: 'padding-right:10px;',
                            items:[

                                {
                                    xtype: 'fieldset',
                                    title: 'Cierre',
                                    autoHeight: true,
                                    hiden: true,
                                    //layout:'hbox',
                                    items: [],
                                    id_grupo: 3
                                }
                            ]
                        }

                    ]

                }];

            Phx.vista.FormCierreCaja.superclass.constructor.call(this,config);
            this.init();
            this.obtenerCaja();
        },

        Atributos:[
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_apertura_cierre_caja'
                },
                type:'Field',
                form:true
            },
            {
                config:{
                    name: 'fecha_apertura_cierre',
                    fieldLabel: 'Fecha ',
                    gwidth: 110,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters: { pfiltro:'apcie.fecha', type:'date'},
                grid:true,
                form:false
            },
            /*{
                config: {
                    name: 'id_sucursal',
                    fieldLabel: 'Sucursal',
                    allowBlank: true,
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
                        baseParams: {tipo_usuario: 'cajero',par_filtro: 'suc.nombre#suc.codigo'}
                    }),
                    valueField: 'id_sucursal',
                    gdisplayField : 'nombre_sucursal',
                    displayField: 'nombre',
                    hiddenName: 'id_sucursal',
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    minChars: 2,
                    width:250,
                    resizable:true
                },
                type: 'ComboBox',
                id_grupo: 1,
                form: true,
                grid:true
            },
            {
                config: {
                    name: 'id_punto_venta',
                    fieldLabel: 'Punto de Venta',
                    allowBlank: true,
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
                        baseParams: {tipo_usuario: 'cajero',par_filtro: 'puve.nombre#puve.codigo'}
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
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['nombre_punto_venta']);
                    },
                    width:250,
                    resizable:true
                },
                type: 'ComboBox',
                id_grupo: 1,
                filters: {pfiltro: 'puve.nombre',type: 'string'},
                form: true,
                grid:true
            },*/
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_sucursal'
                },
                type:'Field',
                form:true
            },
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_punto_venta'
                },
                type:'Field',
                form:true
            },
            {
                config:{
                    name: 'nombre_sucursal',
                    fieldLabel: 'Sucursal',
                    disabled:true,
                    gwidth: 100
                },
                type: 'ComboBox',
                id_grupo:0,
                filters:{pfiltro:'puve.nombre_punto_venta',type:'string'},
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'nombre_punto_venta',
                    fieldLabel: 'Punto de Venta',
                    disabled:true,
                    gwidth: 100
                },
                type: 'ComboBox',
                filters:{pfiltro:'puve.nombre_punto_venta',type:'string'},
                grid:true,
                id_grupo:0,
                form:true
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
                    name: 'monto_inicial',
                    fieldLabel: 'Monto Inicial',
                    allowBlank: false,
                    disabled: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                filters:{pfiltro:'apcie.monto_inicial',type:'numeric'},
                id_grupo:0,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_inicial_moneda_extranjera',
                    fieldLabel: 'Monto Inicial Moneda Extranjera',
                    allowBlank: false,
                    disabled: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                filters:{pfiltro:'apcie.monto_inicial_moneda_extranjera',type:'numeric'},
                id_grupo:0,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'obs_apertura',
                    fieldLabel: 'Obs. Apertura',
                    disabled: true,
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 200
                },
                type:'TextArea',
                filters:{pfiltro:'apcie.obs_apertura',type:'string'},
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'monto_ca_boleto_bs',
                    fieldLabel: 'CA Amadeus Bs',
                    allowBlank: true,
                    disabled:true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:1,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_ca_boleto_usd',
                    fieldLabel: 'CA Amadeus USD',
                    allowBlank: true,
                    disabled:true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:2,
                grid:true,
                form:true,
                valorInicial :0.00
            },{
                config:{
                    name: 'monto_cc_boleto_bs',
                    fieldLabel: 'CC Amadeus Bs',
                    allowBlank: true,
                    disabled:true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:1,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_cc_boleto_usd',
                    fieldLabel: 'CC Amadeus USD',
                    allowBlank: true,
                    disabled:true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:2,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_boleto_bs',
                    fieldLabel: 'Total Boletos Bs',
                    allowBlank: true,
                    disabled:true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:1,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'monto_boleto_usd',
                    fieldLabel: 'Total Boletos USD',
                    allowBlank: true,
                    disabled:true,
                    anchor: '100%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                id_grupo:2,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'obs_cierre',
                    fieldLabel: 'Obs. Cierre',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 150
                },
                type:'TextArea',
                filters:{pfiltro:'apcie.obs_cierre',type:'string'},
                id_grupo:3,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'arqueo_moneda_local',
                    fieldLabel: 'Arqueo Moneda Local',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2
                },
                type:'NumberField',
                filters:{pfiltro:'apcie.arqueo_moneda_local',type:'numeric'},
                id_grupo:3,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'arqueo_moneda_extranjera',
                    fieldLabel: 'Arqueo Moneda Extranjera',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:8,
                    allowDecimals: true,
                    decimalPrecision : 2

                },
                type:'NumberField',
                filters:{pfiltro:'apcie.arqueo_moneda_extranjera',type:'numeric'},
                id_grupo:3,
                grid:true,
                form:true,
                valorInicial :0.00
            },
            {
                config:{
                    name: 'fecha_hora_cierre',
                    fieldLabel: 'Fecha Hora Cierre',
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
            },
            {
                config:{
                    name: 'usr_reg',
                    fieldLabel: 'Cajero',
                    gwidth: 100
                },
                type:'TextField',
                filters:{pfiltro:'ven.estado',type:'string'},
                grid:true,
                form:false
            }
        ],

        title:'Cierre Caja',

        obtenerCaja:function(x){
            var dateToday = new Date();
            var dd = dateToday.getDate();
            var mm = dateToday.getMonth()+1;
            var yyyy = dateToday.getFullYear();
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                // form:this.form.getForm().getEl(),
                url:'../../sis_ventas_facturacion/control/AperturaCierreCaja/listarCierreCaja',
                params:{fecha:dd+'/'+mm+'/'+yyyy,id_punto_venta:this.data.id_punto_venta},
                success:this.successCaja,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },

        successCaja:function(resp){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if(reg.datos.length=1){
                this.Cmp.id_apertura_cierre_caja.setValue(reg.datos[0]['id_apertura_cierre_caja']);
                this.Cmp.id_punto_venta.setValue(reg.datos[0]['id_punto_venta']);
                this.Cmp.id_sucursal.setValue(reg.datos[0]['id_sucursal']);
                this.Cmp.nombre_punto_venta.setValue(reg.datos[0]['nombre_punto_venta']);
                this.Cmp.monto_boleto_bs.setValue(reg.datos[0]['monto_boleto_bs']);
                this.Cmp.monto_boleto_usd.setValue(reg.datos[0]['monto_boleto_usd']);
                this.Cmp.monto_ca_boleto_bs.setValue(reg.datos[0]['monto_ca_boleto_bs']);
                this.Cmp.monto_ca_boleto_usd.setValue(reg.datos[0]['monto_ca_boleto_usd']);
                this.Cmp.monto_cc_boleto_bs.setValue(reg.datos[0]['monto_cc_boleto_bs']);
                this.Cmp.monto_cc_boleto_usd.setValue(reg.datos[0]['monto_cc_boleto_usd']);
                this.Cmp.monto_inicial.setValue(reg.datos[0]['monto_inicial']);
                this.Cmp.monto_inicial_moneda_extranjera.setValue(reg.datos[0]['monto_inicial_moneda_extranjera']);
            }else{
                alert('ocurrio error al obtener datos de la caja')
            }
        },

        onSubmit:function(){
            //TODO passar los datos obtenidos del wizard y pasar  el evento save
            if (this.form.getForm().isValid()) {
                this.fireEvent('beforesave',this,this.getValues());
                this.getValues();
            }
        },

        getValues:function(){
            var me = this;
            var resp = {
                id_apertura_cierre_caja:this.Cmp.id_apertura_cierre_caja.getValue(),
                id_sucursal:this.Cmp.id_sucursal.getValue(),
                id_punto_venta:this.Cmp.id_punto_venta.getValue(),
                obs_cierre:this.Cmp.obs_cierre.getValue(),
                arqueo_moneda_local:this.Cmp.arqueo_moneda_local.getValue(),
                arqueo_moneda_extranjera:this.Cmp.arqueo_moneda_extranjera.getValue(),
                monto_inicial:this.Cmp.monto_inicial.getValue(),
                obs_apertura:this.Cmp.obs_apertura.getValue(),
                monto_inicial_moneda_extranjera: this.Cmp.monto_inicial_moneda_extranjera.getValue()
            }
            return resp;
        }

    })
</script>
