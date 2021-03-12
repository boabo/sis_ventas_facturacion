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

    Phx.vista.DepositoDetalle=Ext.extend(Phx.gridInterfaz, {
        constructor: function (config) {
            //llama al constructor de la clase padre
            Phx.vista.DepositoDetalle.superclass.constructor.call(this, config);
            this.init();
            this.addButton('archivo', {
                grupo: [0,1],
                argument: {imprimir: 'archivo'},
                text: 'Archivos Digitales',
                iconCls:'blist' ,
                disabled: false,
                handler: this.archivo
            });

            // this.addButton('sincronizar', {
            //     grupo: [0,1],
            //     text: 'Sincronizar Ingresos',
            //     iconCls:'bsendmail' ,
            //     disabled: false,
            //     handler: this.sincronizar
            // });


            this.finCons = true;
            //this.store.baseParams.tipo = 'venta_propia';

            /*Recuperar moneda base*/
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
                    this.moneda_base = reg.ROOT.datos.v_codigo_moneda;
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
            /***********************************************************************************/
            /************************/
        },

        archivo : function (){
            var rec = this.getSelectedData();
            //enviamos el id seleccionado para cual el archivo se deba subir
            rec.datos_extras_id = rec.id_deposito;
            //enviamos el nombre de la tabla
            rec.datos_extras_tabla = 'obingresos.tdeposito';
            //enviamos el codigo ya que una tabla puede tener varios archivos diferentes como ci,pasaporte,contrato,slider,fotos,etc
            rec.datos_extras_codigo = 'ESCANDEP';
            Phx.CP.loadWindows('../../../sis_parametros/vista/archivo/Archivo.php',
                'Archivo',
                {
                    width: 900,
                    height: 400
                }, rec, this.idContenedor, 'Archivo');
        },


        // sincronizar:function(){
        //   var rec = this.getSelectedData();
        //   Ext.Ajax.request({
        //           url:'../../sis_obingresos/control/Deposito/sincronizarDeposito',
        //           params:{
        //                   codigo_padre:this.maestro.codigo_padre,
        //                   estacion:this.maestro.estacion,
        //                   codigo:this.maestro.codigo,
        //                   fecha_venta:this.maestro.fecha_venta.dateFormat('d-m-Y'),
        //                   tipo_cambio:this.maestro.tipo_cambio,
        //                   fecha:rec.fecha.dateFormat('d-m-Y'),
        //                   nro_deposito:rec.nro_deposito,
        //                   monto_deposito:rec.monto_deposito,
        //                   id_punto_venta:this.maestro.id_punto_venta,
        //                   id_moneda_deposito:rec.id_moneda_deposito
        //
        //                 },
        //           success: this.successSincronizar,
        //           failure: this.successSincronizar,
        //           timeout:this.timeout,
        //           scope:this
        //   });
        //
        // },

        // successSincronizar:function(resp){
        //   var rec=this.sm.getSelected();
        //   var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
        //   Phx.CP.getPagina(this.idContenedorPadre).reload();
        // },


        bactGroups: [0, 1],
        bexcelGroups: [0, 1],
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

            }, {
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
                    name: 'id_usuario_cajero'
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
                config: {
                    name: 'nro_deposito',
                    fieldLabel: 'No Deposito',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength: 70
                },
                type: 'TextField',
                filters: {pfiltro: 'dep.nro_deposito', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: true
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
                        return value ? value.dateFormat('d/m/Y ') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'dep.fecha', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: true
            },

            {
                config: {
                    name: 'id_moneda_deposito',
                    origen: 'MONEDA',
                    allowBlank: false,
                    fieldLabel: 'Moneda Deposito',
                    gdisplayField: 'desc_moneda',//mapea al store del grid
                    gwidth: 100,
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['desc_moneda']);
                    }
                },
                type: 'ComboRec',
                id_grupo: 1,
                filters: {
                    pfiltro: 'mon.codigo',
                    type: 'string'
                },
                grid: true,
                form: true
            },

            {
                config: {
                    name: 'monto_deposito',
                    fieldLabel: 'Monto',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength: 1179650,
                    renderer:function (value,p,record) {
                        var dato =  value.replace('.', ",")
                            .replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");
                        return '<div ext:qtip="Optimo"><p> <font color="black"><b>'+dato+'</b></font></p></div>';

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
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
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
                        return value ? value.dateFormat('d/m/Y H:i:s') : ''
                    }
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
        ActSave: '../../sis_obingresos/control/Deposito/insertarDeposito',
        ActDel:'../../sis_obingresos/control/Deposito/eliminarDepositoPortal',
        ActList: '../../sis_obingresos/control/Deposito/listarDeposito',
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
            {name: 'id_agencia', type: 'numeric'},
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
            {name: 'id_apertura_cierre_caja', type: 'numeric'}
        ],
        sortInfo: {
            field: 'id_deposito',
            direction: 'DESC'
        },

        bdel: true,
        bsave: false,
        preparaMenu: function () {
            Phx.vista.DepositoDetalle.superclass.preparaMenu.call(this);
            this.getBoton('archivo').enable();
            //this.getBoton('sincronizar').enable();
        },
        liberaMenu: function () {
            this.getBoton('archivo').disable();
            //this.getBoton('sincronizar').disable();
            Phx.vista.DepositoDetalle.superclass.liberaMenu.call(this);
        },
        onReloadPage: function (m) {
            this.maestro = m;
            if (this.moneda_base != 'BOB') {
              //this.getBoton('sincronizar').setVisible(false);
            }
            //this.store.baseParams.tipo = 'venta_propia';
            this.store.baseParams = {id_apertura_cierre_caja: this.maestro.id_apertura_cierre_caja,
                                    mone_base: this.moneda_base,
                                    tipo:'venta_propia'};
            this.load({params: {start: 0, limit: 50}});
        },
        onButtonSave:function(){
            Phx.CP.getPagina(this.idContenedorPadre).reload();
            Phx.vista.DepositoDetalle.superclass.onButtonSave.call(this);
        },
        onButtonEdit: function() {
            Phx.vista.DepositoDetalle.superclass.onButtonEdit.call(this);
            this.getComponente('tipo').setValue('venta_propia');
            this.getComponente('codigo_padre').setValue(this.maestro.codigo_padre);
            this.getComponente('estacion').setValue(this.maestro.estacion);
            this.getComponente('codigo').setValue(this.maestro.codigo);
            this.getComponente('fecha_venta').setValue(this.maestro.fecha_venta.dateFormat('d-m-Y'));
            this.getComponente('id_punto_venta').setValue(this.maestro.id_punto_venta);

            /*AUMENTANDO PARA RECUPERAR EL PUNTO DE VENTA AL EDITAR*/
            console.log("llega aqui boton EDITAR",this);
            /******************************************************************/

        },
        /*********************AUMENTANDO PARA QUE SE REGISTRE LOS DEPOSITOS EN EL LIBRO DE VENTAS***/
        onButtonNew: function() {
            Phx.vista.DepositoDetalle.superclass.onButtonNew.call(this);
            this.store.baseParams.id_punto_venta = this.maestro.id_punto_venta;
            this.store.baseParams.id_usuario_cajero = this.maestro.id_usuario_cajero;

        },
        /************************************************************************************************/
        onButtonDel: function() {
            Phx.vista.DepositoDetalle.superclass.onButtonDel.call(this);
            this.eliminar();

        },
        loadValoresIniciales:function() {
            Phx.vista.DepositoDetalle.superclass.loadValoresIniciales.call(this);
            this.getComponente('id_apertura_cierre_caja').setValue(this.maestro.id_apertura_cierre_caja);
            this.getComponente('codigo_padre').setValue(this.maestro.codigo_padre);
            this.getComponente('estacion').setValue(this.maestro.estacion);
            this.getComponente('codigo').setValue(this.maestro.codigo);
            this.getComponente('fecha_venta').setValue(this.maestro.fecha_venta.dateFormat('d-m-Y'));
            this.getComponente('tipo_cambio').setValue(this.maestro.tipo_cambio);
            this.getComponente('id_punto_venta').setValue(this.maestro.id_punto_venta);
            this.getComponente('id_usuario_cajero').setValue(this.maestro.id_usuario_cajero);
        },
        successSave:function(resp){
            Phx.vista.DepositoDetalle.superclass.successSave.call(this,resp);
            Phx.CP.getPagina(this.idContenedorPadre).reload();
        },
        successEdit:function(resp){
            Phx.vista.DepositoDetalle.superclass.successEdit.call(this,resp);
            Phx.CP.getPagina(this.idContenedorPadre).reload();
        },
        successDel:function(resp){
            Phx.vista.DepositoDetalle.superclass.successDel.call(this,resp);
            Phx.CP.getPagina(this.idContenedorPadre).reload();
        },
        eliminar : function () {
            var data = this.getSelectedData();
            console.log(data);
            Ext.Ajax.request({
                url:'../../sis_obingresos/control/Deposito/eliminar',
                params:{'nro_deposito':data.nro_deposito,'codigo':this.maestro.codigo,
                        'fecha_venta':this.maestro.fecha_venta.dateFormat('d-m-Y'),'monto_deposito':data.monto_deposito,
                        'desc_moneda':data.desc_moneda},
                success: this.successWizard,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        }

    })


</script>
