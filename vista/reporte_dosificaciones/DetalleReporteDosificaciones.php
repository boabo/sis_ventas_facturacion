<?php
/**
 * @package pXP
 * @file gen-DetalleReporteDosificaciones.php
 * @author  (Ismael Valdivia)
 * @date 01-12-2020 08:30:00
 * @description Archivo con la interfaz de usuario que permite generar el reporte de las facturas
 */

header("content-type: text/javascript; charset=UTF-8");
?>


<style>
.punto_venta {
    background-color: #F7FF54;
    font-size: 20px;
}

.vencidas {
    background-color: #FF8080;
}

.total_pv {
    background-color: #F4FD31;
}
.totales {
    background-color: #92E176;
}
</style>

<script>
    var ini = null;
    var fin = null;

    var formato = null;
    var punto_venta = null;
    var concepto = null;



    Phx.vista.DetalleReporteDosificaciones = Ext.extend(Phx.gridInterfaz, {
        title: 'Mayor',

        viewConfig: {
            //stripeRows: false,
            autoFill: true,
            getRowClass: function (record) {
              console.log("aqui datos",record);
                if (record.data.estacion == 'cabecera') {
                  return 'punto_venta';
                }

                if (record.data.dias_restante == 0) {
                  return 'vencidas';
                }


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
                    config:{
                        name: 'estacion',
                        fieldLabel: 'Estacion',
                        allowBlank: false,
                        anchor: '100%',
                        gwidth: 3000,
                        maxLength:150,
                        renderer:function (value,p,record){

                          if (record.data.estacion == 'cabecera') {
                            return '<b><p style="text-align:left; text-decoration: border-top:2px;">Sucursal: '+record.data.desc_sucursal+'</p></b>';
                          } else {
                            return value;
                          }
                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'lu.codigo',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:false
                },
                {
                    config: {
                        name: 'desc_sucursal',
                        fieldLabel: 'Sucursal',
                        allowBlank: true,
                        gwidth: 800,
                        maxLength: 1000,
                    },
                    type: 'TextField',
                    bottom_filter: true,
                    filters: {pfiltro: 'vent.nro_factura', type: 'string'},
                    id_grupo: 1,
                    grid: false,
                    form: true
                },

                {
                    config: {
                        name: 'desc_actividad_economica',
                        fieldLabel: 'Actividad Económica',
                        allowBlank: true,
                        gwidth: 1500,
                        maxLength: 1000,
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
                        name: 'nro_autorizacion',
                        fieldLabel: 'Nro. Autorización',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 1300,
                        maxLength: 1000,
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
                        name: 'nro_tramite',
                        fieldLabel: 'Nro. Tramite',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 1000,
                        maxLength: 1000,
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
                        name: 'nombre_sistema',
                        fieldLabel: 'Sistema Facturación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 1500,
                        maxLength: 1000,
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
                        name: 'nro_inicial',
                        fieldLabel: 'Nro. Inicial',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 1300,
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
                        name: 'nro_final',
                        fieldLabel: 'Nro. Final',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 1300,
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
                        name: 'fecha_dosificacion',
                        fieldLabel: 'Fecha Dosificacion',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 1500,
                        maxLength: 1000,
                        renderer:function (value,p,record){
                          if (record.data.estacion == 'cabecera'){
                            return '';
                          } else {
                            var fecha = value.replaceAll('-', ',');
                            value = new Date(fecha);
                            return value?value.dateFormat('d/m/Y'):'';
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
                        name: 'fecha_limite',
                        fieldLabel: 'Fecha Lim. Emisión',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 1500,
                        maxLength: 1000,
                        renderer:function (value,p,record){
                          if (record.data.estacion == 'cabecera'){
                            return '';
                          } else {
                            var fecha = value.replaceAll('-', ',');
                            value = new Date(fecha);
                            return value?value.dateFormat('d/m/Y'):'';
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
                        name: 'dias_restante',
                        fieldLabel: 'Días Restantes',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 1500,
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
            Phx.vista.DetalleReporteDosificaciones.superclass.constructor.call(this, config);

            /********************Aumentando boton para sacar reporte libro mayor*******************************/
            this.addButton('btnImprimirReporteExcel', {
              text: '<center>Generar Reporte Excel</center>',
              iconCls: 'bexcel',
              disabled: false,
              handler: this.ReporteEXCEL,
              tooltip: '<b>Generar Reporte Excel'
            });
            /***********************************************************************************************/


            this.grid.getTopToolbar().disable();
            this.grid.getBottomToolbar().disable();
            this.init();

            this.tbar.el.dom.style.background='#86CFFF';
            this.bbar.el.dom.style.background='#86CFFF';
        		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#EFFBFF';
        		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#86CFFF';



            this.iniciarEventos();


        },


        tam_pag: 50,

        ActList: '../../sis_ventas_facturacion/control/ReporteDosificaciones/listarReporteDepositos',
        id_store: 'id_venta',
        fields: [
            //{name:'fecha_factura', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name: 'estacion', type: 'varchar'},
            {name: 'desc_sucursal', type: 'varchar'},
            {name: 'desc_actividad_economica', type: 'varchar'},
            {name: 'nro_autorizacion', type: 'varchar'},
            {name: 'nro_tramite', type: 'varchar'},
            {name: 'nombre_sistema', type: 'varchar'},
            {name: 'nro_inicial', type: 'numeric'},
            {name: 'nro_final', type: 'numeric'},
            {name: 'fecha_dosificacion', type: 'varchar'},
            {name: 'fecha_limite', type: 'varchar'},
            {name: 'dias_restante', type: 'numeric'},
        ],

        sortInfo: {
            field: 'fecha',
            direction: 'ASC'
        },
        bdel: true,
        bsave: false,
        loadValoresIniciales: function () {
            Phx.vista.DetalleReporteDosificaciones.superclass.loadValoresIniciales.call(this);

        },

        onReloadPage: function (param) {
            //Se obtiene la gestión en función de la fecha del comprobante para filtrar partidas, cuentas, etc.
            var me = this;
            this.initFiltro(param);
        },

        initFiltro: function (param) {
            this.store.baseParams = param;
            this.load({params: {start: 0, limit: this.tam_pag}});
        },

        ReporteEXCEL: function () {
              Phx.CP.loadingShow();
              Ext.Ajax.request({
                  url: '../../sis_ventas_facturacion/control/ReporteDosificaciones/reporteDosificacionesExcel',
                  params: {
                      tipo_generacion: this.store.baseParams.tipo_generacion,
                      nombre_sistema: this.store.baseParams.nombre_sistema,
                      nombre_sucursal: this.store.baseParams.nombre_sucursal,
                      id_sucursal: this.store.baseParams.id_sucursal,
                      estado_dosificacion: this.store.baseParams.estado_dosificacion,
                  },
                  success: this.successExport,
                  failure: this.conexionFailure,
                  timeout: this.timeout,
                  scope: this
              });

        },
        /***************************************************************************************/
        postReloadPage: function (data) {


        },

        bnew: false,
        bedit: false,
        bdel: false,
        bexcel:false,
      	btest:false
    })
</script>
