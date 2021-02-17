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
    background-color: #FFC318;
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
            autoFill: true,
            getRowClass: function (record) {
              console.log("aqui datos",record);
                if (record.data.tipo_factura == null) {
                  return 'punto_venta';
                } else if (record.data.tipo_factura == 'total_pv') {
                  return 'total_pv';
                } else if (record.data.tipo_factura == '') {
                  return 'totales';
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
                    config: {
                        name: 'fecha_factura',
                        fieldLabel: 'Fecha',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength: 1000,
                        //format: 'd/m/Y',
                        //  renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
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
                        fieldLabel: 'Nro. Factura',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 95,
                        maxLength: 1000
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
                        name: 'nro_documento',
                        fieldLabel: 'Nro. Documento',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 95,
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
                        gwidth: 300,
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
                        gwidth: 300,
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
                        name: 'debe',
                        fieldLabel: 'Debe',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 110,
                        galign: 'right ',
                        maxLength: 100,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('<div style="color:red; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                            }
                            else{
                                return  String.format('<div style="font-size:20px; text-align:right; color:red;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_debe,'0,000.00'));
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
                        gwidth: 110,
                        galign: 'right ',
                        maxLength: 100,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  String.format('<div style="color:blue; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
                            }
                            else{
                                return  String.format('<div style="font-size:20px; text-align:right; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_haber,'0,000.00'));
                            }
                        },
                    },
                    type: 'NumberField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },


            ];


            //llama al constructor de la clase padre
            Phx.vista.DetalleEmisionAuxiliares.superclass.constructor.call(this, config);

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

        		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#EFFBFF';
        		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#DBF0F5';
            this.iniciarEventos();


        },


        tam_pag: 50,

        ActList: '../../sis_ventas_facturacion/control/ReporteEmisionPasajes/listarReporteEmisionBoletos',
        id_store: 'id_venta',
        fields: [
          //  {name:'fecha_factura', type: 'date',dateFormat:'Y-m-d'},
            {name: 'fecha_factura', type: 'varchar'},
            {name: 'nro_factura', type: 'varchar'},
            {name: 'nro_documento', type: 'varchar'},
            {name: 'ruta', type: 'varchar'},
            {name: 'pasajero', type: 'varchar'},
            {name: 'debe', type: 'numeric'},
            {name: 'haber', type: 'numeric'},
            {name:'tipo_reg', type: 'string'},
            {name:'total_debe', type: 'numeric'},
            {name:'total_haber', type: 'numeric'},
            {name:'tipo_factura', type: 'varchar'},
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
        },

        initFiltro: function (param) {
            this.store.baseParams = param;
            this.load({params: {start: 0, limit: this.tam_pag}});
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
                  },
                  success: this.successExport,
                  failure: this.conexionFailure,
                  timeout: this.timeout,
                  scope: this
              });

        },
        /***************************************************************************************/
        postReloadPage: function (data) {
            console.log("Aqui data irva",data);
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
