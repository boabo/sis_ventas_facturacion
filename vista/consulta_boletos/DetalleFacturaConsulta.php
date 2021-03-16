<?php
/**
 * @package pXP
 * @file DetalleFacturaConsulta.php
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
</style>

<script>
    Phx.vista.DetalleFacturaConsulta = Ext.extend(Phx.gridInterfaz, {
    //     viewConfig: {
    //         //stripeRows: false,
    //         autoFill: true,
    //         getRowClass: function (record) {
    //           console.log("aqui datos",record);
    //             if (record.data.tipo_factura == null) {
    //               return 'punto_venta';
    //             } else if (record.data.tipo_factura == 'total_pv') {
    //               return 'total_pv';
    //             } else if (record.data.tipo_factura == '') {
    //               return 'totales';
    //             }
    //         },
    // listener: {
    //     render: this.createTooltip
    // },
    //
    //     },
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
                        name: 'nro_factura',
                        fieldLabel: 'Nro. Factura',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 100,
                        renderer: function(value,p,record){
					                      return String.format('{0}','<i class="fa fa-reply-all" aria-hidden="true"></i> &nbsp;&nbsp;&nbsp;'+record.data['nro_factura']);
				                }
                    },
                    type: 'TextField',
                    bottom_filter: true,
                    filters: {pfiltro: 'v.nro_factura', type: 'string'},
                    id_grupo: 1,
                    grid: true
                },
                {
                    config: {
                        name: 'nit',
                        fieldLabel: 'NIT',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 110,
                        renderer:function (value,p,record){
                          return  String.format('<div style="float:center;"><b>{0}<b></div>',record.data['nit']);
                        }
                    },
                    type: 'TextField',
                    bottom_filter: true,
                    filters: {pfiltro: 'v.nit', type: 'string'},
                    id_grupo: 1,
                    grid: true
                },
                {
                    config: {
                        name: 'nombre_factura',
                        fieldLabel: 'Razon Social',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 180
                    },
                    type: 'TextField',
                    bottom_filter: true,
                    filters: {pfiltro: 'v.nombre_factura', type: 'string'},
                    id_grupo: 1,
                    grid: true
                },
                {
                    config: {
                        name: 'cod_control',
                        fieldLabel: 'Cod. Control',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 150
                    },
                    type: 'TextField',
                    bottom_filter: true,
                    filters: {pfiltro: 'v.cod_control', type: 'string'},
                    id_grupo: 1,
                    grid: true
                },
                {
                    config: {
                        name: 'fecha_factura',
                        fieldLabel: 'Fecha Emision',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 100,
                        // format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'v.fecha', type: 'string'},
                    id_grupo: 1,
                    grid: true
                },
                {
                    config: {
                        name: 'observaciones',
                        fieldLabel: 'observaciones',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 200
                    },
                    type: 'TextField',
                    bottom_filter: true,
                    filters: {pfiltro: 'v.observaciones', type: 'string'},
                    id_grupo: 1,
                    grid: true
                },
                {
                    config: {
                        name: 'nroaut',
                        fieldLabel: 'N° Autorizacion',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 120,
                        renderer:function (value,p,record){
                          return  String.format('<div style="float:center;">{0}</div>',record.data['nroaut']);
                        }
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true
                },
                {
                    config: {
                        name: 'total_venta',
                        fieldLabel: 'Total Venta',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 100,
                        renderer:function (value,p,record){
                          return  String.format('<div style="float:right;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_venta,'0.000,00/i'));
                        }
                    },
                    type: 'NumberField',
                    id_grupo: 1,
                    grid: true
                },
                {
                    config: {
                        name: 'excento',
                        fieldLabel: 'Excento',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 100,
                        renderer:function (value,p,record){
                          return  String.format('<div style="float:right;"><b>{0}<b></div>', Ext.util.Format.number(record.data.excento,'0.000,00/i'));
                        }
                    },
                    type: 'NumberField',
                    id_grupo: 1,
                    grid: true
                },
                {
                    config: {
                        name: 'punto_venta',
                        fieldLabel: 'Punto de Venta',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 250
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true
                },
                {
                    config: {
                        name: 'desc_persona',
                        fieldLabel: 'Usuario Emision',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 200
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true
                },

                // {
                //     config: {
                //         name: 'fecha_factura',
                //         fieldLabel: 'Fecha',
                //         allowBlank: true,
                //         anchor: '50%',
                //         gwidth: 30,
                //         format: 'd/m/Y'
                //   },
                //     type:'TextField',
                //     id_grupo: 1,
                //     grid: true
                // },

            ];
            //llama al constructor de la clase padre
            Phx.vista.DetalleFacturaConsulta.superclass.constructor.call(this, config);
            this.grid.getTopToolbar().disable();
            this.grid.getBottomToolbar().disable();
            this.init();
            this.grid.on('cellclick', this.abrirEnlace, this);
        		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#EFFBFF';
        		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#DBF0F5';
            this.iniciarEventos();
        },


        tam_pag: 50,

        ActList: '../../sis_ventas_facturacion/control/ReporteVentas/consultaFacturaVenta',
        id_store: 'id_venta',
        fields: [
            {name: 'id_venta', type: 'int4'},
            {name: 'nro_factura', type: 'varchar'},
            {name: 'nit', type: 'varchar'},
            {name: 'nombre_factura', type: 'varchar'},
            {name: 'cod_control', type: 'varchar'},
            {name: 'fecha_factura', type:'date',dateFormat: 'Y-m-d'},
            {name: 'observaciones', type: 'varchar'},
            {name: 'total_venta', type: 'numeric'},
            {name: 'excento', type: 'numeric'},
            {name: 'nroaut', type: 'varchar'},
            {name: 'punto_venta', type: 'varchar'},
            {name: 'desc_persona', type: 'varchar'}
        ],

        sortInfo: {
            field: 'fecha_factura',
            direction: 'DESC'
        },
        bdel: true,
        bsave: false,
        loadValoresIniciales: function () {
            Phx.vista.DetalleFacturaConsulta.superclass.loadValoresIniciales.call(this);
        },

        onReloadPage: function (param) {
            var me = this;
            this.initFiltro(param);
        },

        initFiltro: function (param) {
            this.store.baseParams = param;
            this.load({params: {start: 0, limit: this.tam_pag}});
        },
        bnew: false,
        bedit: false,
        bdel: false,
        bexcel:true,
      	btest:false,
        abrirEnlace: function(cell,rowIndex,columnIndex,e){
      		if(columnIndex==1){
      			var data = this.sm.getSelected().data;
      			Phx.CP.loadWindows('../../../sis_ventas_facturacion/vista/consulta_boletos/VentanaDetalleFactura.php',
      			'<span style="font-size:14pt;padding-left: 35%;letter-spacing: 12px;">DETALLE DEL DOCUMENTO</span>', {
      				width:'90%',
      				height:'90%'
      		    }, {
      		    	id_venta: data.id_venta,
      		    	link: true
      		    },
      		    this.idContenedor,
      		    'VentanaDetalleFactura'
      			);
      		}
      	},
    })
</script>
