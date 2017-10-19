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

    Phx.vista.DepositoApertura=Ext.extend(Phx.gridInterfaz, {

        constructor: function (config) {

            Phx.vista.DepositoApertura.superclass.constructor.call(this, config);
            this.init();
            this.addButton('archivo', {
                grupo: [0,1],
                argument: {imprimir: 'archivo'},
                text: 'Archivos Digitales',
                iconCls:'blist' ,
                disabled: false,
                handler: this.archivo
            });
            this.addButton('btnValidar',
                {
                    grupo: [0],
                    text: 'Validar',
                    iconCls: 'bok',
                    disabled: true,
                    handler: this.onValidar,
                    tooltip: 'Valida el deposito registrado'
                }
            );
            this.finCons = true;
            this.store.baseParams.tipo = 'agencia';
            this.store.baseParams.estado = 'borrador';

        },
        gruposBarraTareas: [
            {name: 'borrador', title: '<H1 align="center"><i class="fa fa-eye"></i> Registrados</h1>', grupo: 0, height: 0},
            {name: 'validado', title: '<H1 align="center"><i class="fa fa-eye"></i> Validados</h1>', grupo: 1, height: 0}
        ],
        bactGroups: [0, 1],
        bexcelGroups: [0, 1],
        actualizarSegunTab: function (name, indice) {
            if (this.finCons) {
                this.store.baseParams.estado = name;
                this.load({params: {start: 0, limit: this.tam_pag}});
            }
        },
        onValidar: function () {
            var rec = this.sm.getSelected();
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_obingresos/control/Deposito/cambiaEstadoDeposito',
                params: {
                    'id_deposito': rec.data.id_deposito,
                    'accion': 'validado'
                },
                success: this.successSave,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });

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
                    name: 'saldo'
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
                valorInicial : 'agencia'
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
                        return value ? value.dateFormat('d/m/Y') : ''
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
                    maxLength: 1179650
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
        ActSave: '../../sis_obingresos/control/Deposito/completarDeposito',
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
            var rec = this.sm.getSelected();
            Phx.vista.DepositoApertura.superclass.preparaMenu.call(this);
            this.getBoton('archivo').enable();
            if (this.maestro.estado == 'abierto') {
                this.getBoton('btnValidar').enable();
                this.getBoton('del').enable();
                this.getBoton('new').enable();
                this.getBoton('edit').enable();
            }
            else {
                this.getBoton('btnValidar').disable();
                this.getBoton('del').disable();
                this.getBoton('new').disable();
                this.getBoton('edit').disable();
            }

        },

        liberaMenu: function () {
            var rec = this.sm.getSelected();
            Phx.vista.DepositoApertura.superclass.liberaMenu.call(this);
            this.getBoton('archivo').disable();
            this.getBoton('btnValidar').disable();
            if (this.maestro.estado == 'cerrado') {
                this.getBoton('del').disable();
                this.getBoton('new').disable();
                this.getBoton('edit').disable();
            }

        },
        onReloadPage: function (m) {
            this.maestro = m;
            this.store.baseParams = {id_apertura_cierre_caja: this.maestro.id_apertura_cierre_caja};
            this.load({params: {start: 0, limit: 50}});
        },
        loadValoresIniciales:function(){
            Phx.vista.DepositoApertura.superclass.loadValoresIniciales.call(this);
            this.getComponente('id_apertura_cierre_caja').setValue(this.maestro.id_apertura_cierre_caja);
        }


    })


</script>
