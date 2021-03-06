<?php
/**
 * @package pXP
 * @file gen-DetalleEmisionAuxiliares.php
 * @author  (Ismael Valdivia)
 * @date 01-12-2020 08:30:00
 * @description Archivo con la interfaz de usuario que permite generar el reporte de las facturas
 */

header("content-type: text/javascript; charset=UTF-8");
?>


<style>
.punto_venta {
    background-color: #319DFD;
    font-size: 20px;
}
.total_pv {
    background-color: #F4FD31;
}
.totales {
    background-color: #92E176;
}

.depositos {
    background-color: #FF6060;
}

</style>

<script>
    var ini = null;
    var fin = null;

    var formato = null;
    var punto_venta = null;
    var concepto = null;



    Phx.vista.DetalleEmisionAuxiliares = Ext.extend(Phx.gridInterfaz, {
        title: 'Mayor',

        viewConfig: {
            //stripeRows: false,
            //autoFill: true,
            getRowClass: function (record) {
              //console.log("aqui datos",record);
                if (record.data.tipo_factura == null && record.data.pasajero != 'DEPOSITO') {
                  return 'punto_venta';
                } else if (record.data.tipo_factura == 'total_pv' ) {
                  return 'total_pv';
                } else if (record.data.tipo_factura == '' && record.data.pasajero != 'DEPOSITO') {
                  return 'totales';
                }
                else if (record.data.tipo_factura == null && record.data.pasajero == 'DEPOSITO') {
                  return 'depositos';
                }

                // else if (record.data.tipo_factura == 'total_pv' && record.data.pasajero == 'TOTALES (DEPOSITO):') {
                //   return 'depositos';
                // }


            },
    listener: {
        render: this.createTooltip
    },

        },




        constructor: function (config) {
            var me = this;
            this.maestro = config.maestro;

          //Agrega combo de moneda

            this.Atributos = [
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_venta'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        name: 'fecha_factura',
                        fieldLabel: 'Fecha',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength: 1000,
                        //format: 'd/m/Y',
                        renderer:function (value,p,record){
                          if (value != null && record.data.tipo_reg != 'summary' && record.data.tipo_factura != 'total_pv'){
                            var fecha = value.replaceAll('-', ',');
                            value = new Date(fecha);
                            return value?value.dateFormat('d/m/Y'):'';
                          } else if (value == ''){
                            return '';
                          }
                        }
                  },
                    type:'TextField',
                    //bottom_filter: true,
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'nro_factura',
                        fieldLabel: 'Nro. Documento',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 130,
                        maxLength: 1000,
                        renderer:function (value,p,record){
                          if (value == 0){
                            return '';
                          } else {
                            return value;
                          }
                        }
                    },
                    type: 'TextField',
                    bottom_filter: true,
                    filters: {pfiltro: 'vent.nro_factura', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'tipo_documento',
                        fieldLabel: 'Tipo de Documento',
                        allowBlank: true,
                        //width: '100%',
                        gwidth: 500,
                        //galign: 'right ',
                        maxLength: 100,
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'observaciones',
                        fieldLabel: 'Observaciones',
                        allowBlank: true,
                        //width: '100%',
                        gwidth: 500,
                        //galign: 'right ',
                        maxLength: 100,
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'ruta',
                        fieldLabel: 'Rutas',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 200,
                        //galign: 'right ',
                        maxLength: 100,
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'pasajero',
                        fieldLabel: 'Pasajero',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 200,
                        //galign: 'right ',
                        //maxLength: 100,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('{0}', record.data['pasajero']);
                            }
                            else{
                                return '<b><p style="font-size:15px; color:red; text-align:right; text-decoration: border-top:2px;">Total General:</p></b>';
                            }
                        },

                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },

                {
                    config: {
                        name: 'cuenta_auxiliar',
                        fieldLabel: 'Cuenta Auxiliar',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 350,
                        //galign: 'right ',
                        maxLength: 100,
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },

                {
                    config: {
                        name: 'debe',
                        fieldLabel: 'Debe',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 200,
                        galign: 'right ',
                        maxLength: 100,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('<div style="color:red; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                            }
                            else{
                                return  String.format('<div style="font-size:15px; text-align:right; color:red;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_debe,'0,000.00'));
                            }
                        },
                    },
                    type: 'NumberField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },

                {
                    config: {
                        name: 'haber',
                        fieldLabel: 'Haber',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 200,
                        galign: 'right ',
                        maxLength: 100,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('<div style="color:blue; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                            }
                            else{
                                return  String.format('<div style="font-size:15px; text-align:right; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_haber,'0,000.00'));
                            }
                        },
                    },
                    type: 'NumberField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'punto_venta',
                        fieldLabel: 'Punto de Venta',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 250,
                        //galign: 'right ',
                        maxLength: 100,
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },


            ];


            //llama al constructor de la clase padre
            Phx.vista.DetalleEmisionAuxiliares.superclass.constructor.call(this, config);

            /********************Aumentando boton para sacar reporte ***************************************/
            this.addButton('btnImprimirReporteExcel', {
              text: '<center>Generar Reporte Excel</center>',
              iconCls: 'bexcel',
              disabled: false,
              handler: this.ReporteEXCEL,
              tooltip: '<b>Generar Reporte Excel'
            });
            /***********************************************************************************************/


            /*Boton para Generar Resumen y Detalle de cta corriente*/
            this.addButton('btnReporteResuDet', {
              text: '<center>Reporte Resumen <br> y Detallado</center>',
              iconCls: 'bexcel',
              disabled: false,
              hidden: true,
              handler: this.ReporteResumenDetalle,
              tooltip: '<b>Generar Reporte Excel'
            });
            /*******************************************************/


            this.grid.getTopToolbar().disable();
            this.grid.getBottomToolbar().disable();
            this.init();

        		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#EFFBFF';
        		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#DBF0F5';
            this.iniciarEventos();


        },


        tam_pag: 50,

        ActList: '../../sis_ventas_facturacion/control/ReporteEmisionPasajes/listarReporteEmisionBoletos',
        id_store: 'id_venta',
        fields: [
            //{name:'fecha_factura', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name: 'fecha_factura', type: 'varchar'},
            {name: 'nro_factura', type: 'numeric'},
            {name: 'tipo_documento', type: 'varchar'},
            {name: 'ruta', type: 'varchar'},
            {name: 'pasajero', type: 'varchar'},
            {name: 'debe', type: 'numeric'},
            {name: 'haber', type: 'numeric'},
            {name:'tipo_reg', type: 'string'},
            {name:'total_debe', type: 'numeric'},
            {name:'total_haber', type: 'numeric'},
            {name:'tipo_factura', type: 'varchar'},
            {name:'cuenta_auxiliar', type: 'varchar'},
            {name:'punto_venta', type: 'varchar'},
            {name:'observaciones', type: 'varchar'},
        ],

        sortInfo: {
            field: 'fecha',
            direction: 'ASC'
        },
        bdel: true,
        bsave: false,
        loadValoresIniciales: function () {
            Phx.vista.DetalleEmisionAuxiliares.superclass.loadValoresIniciales.call(this);

        },

        onReloadPage: function (param) {
            //Se obtiene la gestión en función de la fecha del comprobante para filtrar partidas, cuentas, etc.
            var me = this;
            this.initFiltro(param);

            console.log("aqui llega el boton",this.getBoton('btnReporteResuDet'));

            if (this.store.baseParams.nombre_pv == 'Todos') {
              /*Mostrar el boton de reporte general*/
              this.getBoton('btnReporteResuDet').setVisible(true);

            } else {
              this.getBoton('btnReporteResuDet').setVisible(false);
            }

            if (this.store.baseParams.formato_reporte == 'RESUMEN CTA/CTE TOTALIZADO') {
              this.cm.setHidden(0, true);
              this.cm.setHidden(1, true);
              this.cm.setHidden(2, true);
              this.cm.setHidden(3, true);
              this.cm.setHidden(4, true);
              this.cm.setHidden(5, true);
              this.cm.setHidden(6, true);
              /*Esconder el punto de venta para diferenciar los auxiliares*/
              if (this.store.baseParams.nombre_pv == 'Todos') {
                this.cm.setHidden(10, true);
              } else {
                this.cm.setHidden(10, false);
              }
              /*************************************************************/

            } else {
              this.cm.setHidden(0, false);
              this.cm.setHidden(1, false);
              this.cm.setHidden(2, false);
              this.cm.setHidden(3, false);
              this.cm.setHidden(4, false);
              this.cm.setHidden(5, false);
              this.cm.setHidden(6, false);
              this.cm.setHidden(10, true);
            }




        },

        initFiltro: function (param) {
            this.store.baseParams = param;
            this.load({params: {start: 0, limit: 50}});
        },

        ReporteEXCEL: function () {
              Phx.CP.loadingShow();
              Ext.Ajax.request({
                  url: '../../sis_ventas_facturacion/control/ReporteEmisionPasajes/reporteEmisionPasajes',
                  params: {
                      id_auxiliar: this.store.baseParams.id_auxiliar,
                      codigo_auxiliar: this.store.baseParams.codigo_auxiliar,
                      desde: this.store.baseParams.desde,
                      hasta: this.store.baseParams.hasta,
                      id_punto_venta: this.store.baseParams.id_punto_venta,
                      nombre_pv: this.store.baseParams.nombre_pv,
                      formato_reporte: this.store.baseParams.formato_reporte,
                      generar_reporte: 'si',
                  },
                  success: this.successExport,
                  failure: this.conexionFailure,
                  timeout: this.timeout,
                  scope: this
              });

        },

        ReporteResumenDetalle: function () {
              Phx.CP.loadingShow();
              Ext.Ajax.request({
                  url: '../../sis_ventas_facturacion/control/ReporteEmisionPasajes/reporteResumenDetalle',
                  params: {
                      id_auxiliar: this.store.baseParams.id_auxiliar,
                      codigo_auxiliar: this.store.baseParams.codigo_auxiliar,
                      desde: this.store.baseParams.desde,
                      hasta: this.store.baseParams.hasta,
                      id_punto_venta: this.store.baseParams.id_punto_venta,
                      nombre_pv: this.store.baseParams.nombre_pv,
                      formato_reporte: this.store.baseParams.formato_reporte,
                  },
                  success: this.successExport,
                  failure: this.conexionFailure,
                  timeout: this.timeout,
                  scope: this
              });

        },





        /***************************************************************************************/
        postReloadPage: function (data) {
            ini = data.id_auxiliar;
            fin = data.codigo_auxiliar;


        },

        bnew: false,
        bedit: false,
        bdel: false,
        bexcel:false,
      	btest:false
    })
</script>
