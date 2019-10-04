<?php
/**
 *@package pXP
 *@file gen-AperturaCierreCaja.php
 *@author  (jrivera)
 *@date 07-07-2016 14:16:20
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.AperturaCierreCaja=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.AperturaCierreCaja.superclass.constructor.call(this,config);
                this.init();
                this.recuperarBase();
                this.addButton('cerrar',{grupo:[0],text:'Cerrar Caja',iconCls: 'block',disabled:true,handler:this.preparaCerrarCaja,tooltip: '<b>Cerrar la Caja seleccionada</b>'});
                this.addButton('abrir',{grupo:[1],text:'Abrir Caja',iconCls: 'bunlock',disabled:true,handler:this.abrirCaja,tooltip: '<b>Abrir la Caja seleccionada</b>'});
                this.addButton('boletos',{grupo:[0],text: 'Validar Boletos Amadeus - ERP',	iconCls: 'breload2',disabled: true,handler: this.onActualizarBoletos,tooltip: 'Actualizar boletos vendidos para cierre de caja'});
                this.addButton('reporte',{grupo:[1],text:'Declaracion de Ventas',iconCls: 'bpdf',disabled:true,handler:this.generarReporte,tooltip: '<b>Reporte Declaración Diarias de Ventas</b>'});
                this.finCons = true;
                this.store.baseParams.pes_estado = 'abierto';
                this.getBoton('reporte').setVisible(false);
                this.load({params:{start:0, limit:this.tam_pag}});
            },
            bactGroups:  [0,1],
            bexcelGroups: [0,1],
            bdel : true,
            gruposBarraTareas:[{name:'abierto',title:'<H1 align="center"><i class="fa fa-eye"></i> Abiertas</h1>',grupo:0,height:0},
                {name:'cerrado',title:'<H1 align="center"><i class="fa fa-eye"></i> Cerradas</h1>',grupo:1,height:0}

            ],
            actualizarSegunTab: function(name, indice){
                if(this.finCons) {
                    this.store.baseParams.pes_estado = name;
                    this.load({params:{start:0, limit:this.tam_pag}});
                }
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
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'modificado'
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
                    filters: { pfiltro:'apcie.fecha_apertura_cierre', type:'date'},
                    grid:true,
                    form:true
                },
                {
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
                    grid:false
                },
                {
                    config:{
                        name: 'desc_persona',
                        fieldLabel: 'Cajero',
                        gwidth: 200,
                        renderer: function(value, p, record) {
                            return '<tpl for="."><p <b><font color="black">'+record.data['desc_persona']+'</font></b></p></tpl>';
                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'u.desc_persona',type:'string'},
                    grid:true,
                    form:false,
                    bottom_filter:true
                },
                {
                    config:{
                        name: 'tipo',
                        fieldLabel: 'tipo',
                        gwidth: 200
                    },
                    type:'TextField',
                    grid:false,
                    form:false
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
                        gwidth: 200,
                        minChars: 2,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['nombre_punto_venta']);
                        },
                        width:250,
                        resizable:true
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    filters: {pfiltro: 'pv.nombre',type: 'string'},
                    grid: true,
                    form: true,
                    grid:true,
                    bottom_filter:true
                },
                {
                    config:{
                        name: 'tipo',
                        fieldLabel: 'Tipo',
                        gwidth: 100
                    },
                    type:'TextField',
                    filters:{pfiltro:'pv.tipo',type:'string'},
                    grid:true,
                    form:false
                },

                {
                    config:{
                        name: 'estado',
                        fieldLabel: 'Estado',
                        gwidth: 100,
                        renderer: function(value, p, record) {
                            if (record.data['estado'] == 'abierto') {
                                return '<tpl for="."><p><font color="red"><b>'+record.data['estado']+'</b></font></p></tpl>';
                            }else{
                                return '<tpl for="."><p <b><font color="black">'+record.data['estado']+'</font></b></p></tpl>';
                            }
                        }
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
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:10,
                        allowDecimals: true,
                        decimalPrecision : 2
                    },
                    type:'NumberField',
                    filters:{pfiltro:'apcie.monto_inicial',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:true,
                    valorInicial :0.00
                },
                {
                    config:{
                        name: 'monto_inicial_moneda_extranjera',
                        fieldLabel: 'Monto Inicial Moneda Extranjera',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:10,
                        allowDecimals: true,
                        decimalPrecision : 2
                    },
                    type:'NumberField',
                    filters:{pfiltro:'apcie.monto_inicial_moneda_extranjera',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:true,
                    valorInicial :0.00
                },
                {
                    config:{
                        name: 'obs_apertura',
                        fieldLabel: 'Obs. Apertura',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 200
                    },
                    type:'TextArea',
                    filters:{pfiltro:'apcie.obs_apertura',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
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
                    id_grupo:1,
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
                        maxLength:4,
                        allowDecimals: true,
                        decimalPrecision : 2
                    },
                    type:'NumberField',
                    filters:{pfiltro:'apcie.arqueo_moneda_local',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:true

                },
                {
                    config:{
                        name: 'arqueo_moneda_extranjera',
                        fieldLabel: 'Arqueo Moneda Extranjera',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4,
                        allowDecimals: true,
                        decimalPrecision : 2

                    },
                    type:'NumberField',
                    filters:{pfiltro:'apcie.arqueo_moneda_extranjera',type:'numeric'},
                    id_grupo:1,
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
                        renderer:function (value,p,record){
                            return value?value.dateFormat('d/m/Y H:i:s'):''
                        }
                    },
                    type:'DateField',
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'usr_reg',
                        fieldLabel: 'User Cajero',
                        gwidth: 100
                    },
                    type:'TextField',
                    filters:{pfiltro:'usu1.cuenta',type:'string'},
                    grid:true,
                    form:false
                }

            ],
            tam_pag:50,
            title:'Apertura de Caja',
            ActSave:'../../sis_ventas_facturacion/control/AperturaCierreCaja/insertarAperturaCierreCaja',
            ActDel:'../../sis_ventas_facturacion/control/AperturaCierreCaja/eliminarAperturaCierreCaja',
            ActList:'../../sis_ventas_facturacion/control/AperturaCierreCaja/listarAperturaCierreCaja',
            id_store:'id_apertura_cierre_caja',
            fields: [
                {name:'id_apertura_cierre_caja', type: 'numeric'},
                {name:'id_sucursal', type: 'numeric'},
                {name:'id_punto_venta', type: 'numeric'},
                {name:'id_usuario_cajero', type: 'numeric'},
                {name:'id_moneda', type: 'numeric'},
                {name:'obs_cierre', type: 'string'},
                {name:'estado', type: 'string'},
                {name:'fecha_apertura_cierre', type: 'date',dateFormat:'Y-m-d'},
                {name:'monto_inicial', type: 'numeric'},
                {name:'arqueo_moneda_local', type: 'numeric'},
                {name:'arqueo_moneda_extranjera', type: 'numeric'},
                {name:'monto_inicial', type: 'numeric'},
                {name:'obs_apertura', type: 'string'},
                {name:'usr_reg', type: 'string'},
                {name:'fecha_hora_cierre', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'monto_inicial_moneda_extranjera', type: 'numeric'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mod', type: 'string'},
                {name:'nombre_punto_venta', type: 'string'},
                {name:'id_entrega_brinks', type: 'numeric'},
                {name:'tipo', type: 'string'},
                {name:'desc_persona', type: 'string'},
                {name:'tipo', type: 'string'},
                {name:'modificado', type: 'string'}



            ],
            sortInfo:{
                field: 'fecha_apertura_cierre',
                direction: 'DESC'
            },

//Comentado temporalmente cuando se tenga facturacion computarizada y manual descomentar
          /*  tabsouth :[
                {
                    url:'../../../sis_ventas_facturacion/vista/apertura_cierre_caja/DepositoApertura.php',
                    title:'Deposito',
                    height:'40%',
                    cls:'DepositoApertura'
                },
                {
                    url:'../../../sis_ventas_facturacion/vista/apertura_cierre_caja/VendedorComputarizadoApertura.php',
                    title:'Venta Computarizada',
                    height:'40%',
                    cls:'VendedorComputarizadoApertura'
                },
                {
                    url:'../../../sis_ventas_facturacion/vista/apertura_cierre_caja/VendedorManualApertura.php',
                    title:'Venta Manual',
                    height:'40%',
                    cls:'VendedorManualApertura'
                },
                {
                    url:'../../../sis_ventas_facturacion/vista/apertura_cierre_caja/AperturaBoletosVenta.php',
                    title:'Venta Boletos',
                    height:'40%',
                    cls:'AperturaBoletosVenta'
                }
            ],*/
            onSubmit: function(o, x, force) {
                if (!this.Cmp.id_punto_venta.getValue() && !this.Cmp.id_punto_venta.getValue()) {
                    alert('Debe elegir un punto de venta o sucursal para abrir');
                } else {
                    Phx.vista.AperturaCierreCaja.superclass.onSubmit.call(this,o, x, force);
                }
            },
            onButtonNew: function() {
                this.ocultarComponente(this.Cmp.obs_cierre);
                this.ocultarComponente(this.Cmp.arqueo_moneda_local);
                this.ocultarComponente(this.Cmp.arqueo_moneda_extranjera);
                this.Cmp.arqueo_moneda_local.allowBlank = true;
                this.ocultarComponente(this.Cmp.id_sucursal);
                this.mostrarComponente(this.Cmp.id_punto_venta);
                this.mostrarComponente(this.Cmp.monto_inicial);
                this.mostrarComponente(this.Cmp.monto_inicial_moneda_extranjera);
                this.mostrarComponente(this.Cmp.obs_apertura);
                this.Cmp.id_punto_venta.setDisabled(false);
                this.Cmp.id_sucursal.setDisabled(false);
                Phx.vista.AperturaCierreCaja.superclass.onButtonNew.call(this);
                this.Cmp.fecha_apertura_cierre.setValue(new Date());
            },

            onButtonEdit: function() {
                this.ocultarComponente(this.Cmp.obs_cierre);
                this.ocultarComponente(this.Cmp.arqueo_moneda_local);
                this.ocultarComponente(this.Cmp.arqueo_moneda_extranjera);
                this.Cmp.arqueo_moneda_local.allowBlank = true;
                this.ocultarComponente(this.Cmp.id_sucursal);
                this.mostrarComponente(this.Cmp.id_punto_venta);
                this.mostrarComponente(this.Cmp.monto_inicial);
                this.mostrarComponente(this.Cmp.monto_inicial_moneda_extranjera);
                this.mostrarComponente(this.Cmp.obs_apertura);
                this.Cmp.id_punto_venta.setDisabled(true);
                this.Cmp.id_sucursal.setDisabled(true);
                this.argumentExtraSubmit = {'accion' :'nada'};
                Phx.vista.AperturaCierreCaja.superclass.onButtonEdit.call(this);

            },

            preparaCerrarCaja:function(){
                var data=this.sm.getSelected().data;
                console.log(data.tipo);
                if (data.tipo == 'carga'){
                    Phx.CP.loadWindows('../../../sis_ventas_facturacion/vista/apertura_cierre_caja/FormCierreCajaCarga.php',
                        'Cerrar Caja Carga',
                        {
                            modal: true,
                            width: '100%',
                            height: '100%',
                            autoScroll: true
                        }, {data: data}, this.idContenedor, 'FormCierreCajaCarga',
                        {
                            config: [{
                                event: 'beforesave',
                                delegate: this.cerrarCaja,
                            }
                            ],
                            scope: this
                        })

                }else {
                    Phx.CP.loadWindows('../../../sis_ventas_facturacion/vista/apertura_cierre_caja/FormCierreCaja.php',
                        'Cerrar Caja',
                        {
                          modal: true,
                          width: '100%',
                          height: '100%',
                          //autoScroll: true
                        }, {data: data}, this.idContenedor, 'FormCierreCaja',
                        {
                            config: [{
                                event: 'beforesave',
                                delegate: this.cerrarCaja,
                            }
                            ],
                            scope: this
                        })
                }
            },

            cerrarCaja:function(wizard,resp){
                var me=this;
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url:'../../sis_ventas_facturacion/control/AperturaCierreCaja/insertarAperturaCierreCaja',
                    params:{
                        id_apertura_cierre_caja: resp.id_apertura_cierre_caja,
                        id_sucursal: resp.id_sucursal,
                        id_punto_venta: resp.id_punto_venta,
                        obs_cierre: resp.obs_cierre,
                        arqueo_moneda_local: resp.arqueo_moneda_local,
                        arqueo_moneda_extranjera: resp.arqueo_moneda_extranjera,
                        accion :'cerrar',
                        monto_inicial: resp.monto_inicial,
                        obs_apertura: resp.obs_apertura,
                        monto_inicial_moneda_extranjera: resp.monto_inicial_moneda_extranjera,
                        //monto_ca_recibo_ml: resp.monto_ca_recibo_ml,
                        //	monto_cc_recibo_ml: resp.monto_cc_recibo_ml,
                        fecha_apertura_cierre: resp.fecha_apertura_cierre,

                        tipo:resp.tipo,
                        //nacional
                        monto_ca_boleto_bs : resp.monto_ca_boleto_bs,
                        monto_cc_boleto_bs : resp.monto_cc_boleto_bs,
                        monto_cte_boleto_bs : resp.monto_cte_boleto_bs,
                        monto_mco_boleto_bs : resp.monto_mco_boleto_bs,
                        //internaciona
                        monto_ca_boleto_usd: resp.monto_ca_boleto_usd,
                        monto_cc_boleto_usd: resp.monto_cc_boleto_usd,
                        monto_cte_boleto_usd: resp.monto_cte_boleto_usd,
                        monto_mco_boleto_usd: resp.monto_mco_boleto_usd,

                        monto_ca_recibo_ml : resp.monto_ca_recibo_ml,
                        monto_ca_recibo_me : resp.monto_ca_recibo_me,
                        monto_cc_recibo_ml : resp.monto_cc_recibo_ml,
                        monto_cc_recibo_me : resp.monto_cc_recibo_me,
                        //nacional_ventas

                        monto_ca_facturacion_bs : resp.monto_ca_facturacion_bs,
                        monto_cc_facturacion_bs : resp.monto_cc_facturacion_bs,
                        monto_cte_facturacion_bs : resp.monto_cte_facturacion_bs,
                        monto_mco_facturacion_bs : resp.monto_mco_facturacion_bs,
                        //interncionl_ventas
                        monto_ca_facturacion_usd : resp.monto_ca_facturacion_usd,
                        monto_cc_facturacion_usd : resp.monto_cc_facturacion_usd,
                        monto_cte_facturacion_usd : resp.monto_cte_facturacion_usd,
                        monto_mco_facturacion_usd : resp.monto_mco_facturacion_usd,
                        comisiones_ml : resp.comisiones_ml,
                        comisiones_me : resp.comisiones_me


                    },
                    argument:{wizard:wizard},
                    success:this.successWizard,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });

            },

            successWizard:function(resp){
                Phx.CP.loadingHide();
                resp.argument.wizard.panel.destroy()
                this.reload();
            },

            abrirCaja:function(){
                Phx.CP.loadingShow();
                var d = this.sm.getSelected().data;
                Ext.Ajax.request({
                    url:'../../sis_ventas_facturacion/control/AperturaCierreCaja/abrirAperturaCierreCaja',
                    params:{id_apertura_cierre_caja:d.id_apertura_cierre_caja, estado:d.estado},
                    success:this.successAbrirAperturaCierreCaja,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
            },

            successAbrirAperturaCierreCaja:function(resp){
                Phx.CP.loadingHide();
                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                if(!reg.ROOT.error){
                    this.reload();
                }
            },
            generarReporte : function () {
                var data=this.sm.getSelected().data;
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url:'../../sis_ventas_facturacion/control/AperturaCierreCaja/reporteAperturaCierreCaja',
                    params:{'id_apertura_cierre_caja' : data.id_apertura_cierre_caja},
                    success:this.successExport,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
            },

            recuperarBase : function () {
              /******************************OBTENEMOS LA MONEDA BASE*******************************************/
              var fecha = new Date();
              var dd = fecha.getDate();
              var mm = fecha.getMonth() + 1; //January is 0!
              var yyyy = fecha.getFullYear();
              this.fecha_actual = dd + '/' + mm + '/' + yyyy;
              Ext.Ajax.request({
                  url:'../../sis_ventas_facturacion/control/AperturaCierreCaja/getTipoCambio',
                  params:{fecha_cambio:this.fecha_actual},
                  success: function(resp){
                      var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                      //this.tipo_cambio = reg.ROOT.datos.v_tipo_cambio;
                      this.store.baseParams.moneda_base = reg.ROOT.datos.v_codigo_moneda;
                  },
                  failure: this.conexionFailure,
                  timeout:this.timeout,
                  scope:this
              });
              /***********************************************************************************/

            },
            onActualizarBoletos : function () {
                var data=this.sm.getSelected().data;
                Phx.CP.loadingShow();
                console.log("llega aqui la moneda base",this.store.baseParams.moneda_base);
                Ext.Ajax.request({
                    url:'../../sis_obingresos/control/Boleto/traerBoletosJsonAnulados',
                    params: {moneda_base:this.store.baseParams.moneda_base,id_punto_venta: data.id_punto_venta, fecha: data.fecha_apertura_cierre.dateFormat('Ymd'), id_usuario_cajero: data.id_usuario_cajero},
                    success:this.successSinc,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
            },

            successSinc: function(resp) {
                Phx.CP.loadingHide();
                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                if (reg.ROOT.error) {
                    Ext.Msg.alert('Error','Boletos no actualizados: se ha producido un error inesperado. Comuníquese con el Administrador del Sistema.')
                } else {
                    Ext.Msg.alert('Mensaje',reg.ROOT.datos.mensaje_respuesta);
                }
            },

            preparaMenu:function()
            {   var rec = this.sm.getSelected();

                if (rec.data.estado == 'abierto') {
                    this.getBoton('cerrar').enable();
                    this.getBoton('reporte').enable();
                    this.getBoton('edit').enable();
                    this.getBoton('del').enable();
                    this.getBoton('boletos').enable();
                    this.getBoton('abrir').disable();
                }

                if (rec.data.estado == 'cerrado') {
                    this.getBoton('cerrar').disable();
                    this.getBoton('reporte').enable();
                    this.getBoton('edit').disable();
                    this.getBoton('del').disable();
                    this.getBoton('boletos').disable();
                    this.getBoton('abrir').enable();
                }

                Phx.vista.AperturaCierreCaja.superclass.preparaMenu.call(this);
            },
            liberaMenu:function()
            {
                this.getBoton('cerrar').disable();
                this.getBoton('boletos').disable();
                Phx.vista.AperturaCierreCaja.superclass.liberaMenu.call(this);
            }


        }
    )
</script>
