<?php
/**
 * @package pXP
 * @file gen-DetalleFacturacion.php
 * @author  (Ismael Valdivia)
 * @date 01-12-2020 08:30:00
 * @description Archivo con la interfaz de usuario que permite generar el reporte de las facturas
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    var ini = null;
    var fin = null;

    var formato = null;
    var punto_venta = null;
    var concepto = null;



    Phx.vista.DetalleFacturacion = Ext.extend(Phx.gridInterfaz, {
        title: 'Mayor',
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
                        name: 'id_int_transaccion'
                    },
                    type: 'Field',
                    form: true
                },
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
                        name: 'nombre',
                        fieldLabel: 'Punto de Venta',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength: 1000,

                    },
                    type: 'TextField',
                    filters: {pfiltro: 'pv.nombre', type: 'string'},
                    bottom_filter: true,
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'fecha',
                        fieldLabel: 'Fecha',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength: 1000
                    },
                    type: 'TextField',
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
                        gwidth: 200,
                        maxLength: 1000,
                      //   renderer:function (value,p,record){
                			// 		if(record.data.tipo_reg != 'summary'){
                			// 			return  String.format('{0}', record.data['nro_factura']);
                			// 		}
                			// 		else{
                			// 			return '<b><p style="font-size:20px; color:red; text-decoration: border-top:2px;">Totales: &nbsp;&nbsp; </p></b>';
                			// 		}
                			// },
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
                        name: 'total_venta',
                        fieldLabel: 'Total Factura',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 110,
                        galign: 'right ',
                        maxLength: 100,
                    //     renderer:function (value,p,record){
                		// 			if(record.data.tipo_reg != 'summary'){
                		// 				return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                		// 			}
                    //
                		// 			else{
                		// 				return  String.format('<div style="font-size:20px; text-align:rigth; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_monto_facturas,'0,000.00'));
                		// 			}
                    //
                		// },
                    },
                    type: 'NumberField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'excento',
                        fieldLabel: 'Exentos',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 110,
                        galign: 'right ',
                        maxLength: 100,
                    //     renderer:function (value,p,record){
                		// 			if(record.data.tipo_reg != 'summary'){
                		// 				return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                		// 			}
                    //
                		// 			else{
                		// 				return  String.format('<div style="font-size:20px; text-align:rigth; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_excentos,'0,000.00'));
                		// 			}
                    //
                		// },
                    },
                    type: 'NumberField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },

                {
                    config: {
                        name: 'comision',
                        fieldLabel: 'Comision',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 110,
                        galign: 'right ',
                        maxLength: 100,
                    //     renderer:function (value,p,record){
                		// 			if(record.data.tipo_reg != 'summary'){
                		// 				return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                		// 			}
                    //
                		// 			else{
                		// 				return  String.format('<div style="font-size:20px; text-align:rigth; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_comision,'0,000.00'));
                		// 			}
                    //
                		// },
                    },
                    type: 'NumberField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },

                // {
                //     config: {
                //         name: 'cantidad',
                //         fieldLabel: 'Cantidad',
                //         allowBlank: true,
                //         width: '100%',
                //         gwidth: 110,
                //         galign: 'right ',
                //         maxLength: 100
                //     },
                //     type: 'NumberField',
                //     id_grupo: 1,
                //     grid: true,
                //     form: true
                // },
                // {
                //     config: {
                //         name: 'conceptos',
                //         fieldLabel: 'Conceptos',
                //         allowBlank: true,
                //         anchor: '80%',
                //         gwidth: 200,
                //         maxLength: 1000,
                //         renderer:function (value,p,record){
                // 					if(record.data.tipo_reg != 'summary'){
                // 						return  String.format('{0}', record.data['conceptos']);
                // 					}
                // 					else{
                // 						return '<b><p style="font-size:20px; color:red; text-decoration: border-top:2px;">Totales FP: &nbsp;&nbsp; </p></b>';
                // 					}
                // 			},
                //     },
                //     type: 'TextField',
                //     bottom_filter: true,
                //     filters: {pfiltro: 'ingas.desc_ingas', type: 'string'},
                //     id_grupo: 1,
                //     grid: false,
                //     form: true
                // },

                {
                    config: {
                        name: 'precio',
                        fieldLabel: 'Precio/Unit',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 110,
                        galign: 'right ',
                        maxLength: 100,
                    //     renderer:function (value,p,record){
                		// 			if(record.data.tipo_reg != 'summary'){
                		// 				return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                		// 			}
                    //
                		// 			else{
                		// 				return  String.format('<div style="font-size:20px; text-align:rigth; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_precio_unitario,'0,000.00'));
                		// 			}
                    //
                		// },
                    },
                    type: 'NumberField',
                    id_grupo: 1,
                    grid: false,
                    form: true
                },

                // {
                //     config: {
                //         name: 'monto_mb_efectivo',
                //         fieldLabel: 'Total',
                //         allowBlank: true,
                //         width: '100%',
                //         gwidth: 110,
                //         galign: 'right ',
                //         maxLength: 100,
                //     //     renderer:function (value,p,record){
                // 		// 			if(record.data.tipo_reg != 'summary'){
                // 		// 				return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                // 		// 			}
                //     //
                // 		// 			else{
                // 		// 				return  String.format('<div style="font-size:20px; text-align:rigth; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_forma_pago,'0,000.00'));
                // 		// 			}
                //     //
                // 		// },
                //     },
                //     type: 'NumberField',
                //     id_grupo: 1,
                //     grid: false,
                //     form: true
                // },
                //
                // {
                //     config: {
                //         name: 'fop_code',
                //         fieldLabel: 'Forma de Pago',
                //         allowBlank: true,
                //         width: '100%',
                //         gwidth: 110,
                //         galign: 'right ',
                //         maxLength: 100
                //     },
                //     type: 'TextField',
                //     filters: {pfiltro: 'fpw.fop_code', type: 'string'},
                //     id_grupo: 1,
                //     grid: true,
                //     form: true
                // },

                // {
                //     config: {
                //         name: 'codigo_internacional',
                //         fieldLabel: 'Moneda',
                //         allowBlank: true,
                //         width: '100%',
                //         gwidth: 110,
                //         galign: 'right ',
                //         maxLength: 100
                //     },
                //     type: 'TextField',
                //     filters: {pfiltro: 'fpw.fop_code', type: 'string'},
                //     id_grupo: 1,
                //     grid: true,
                //     form: true
                // },

                // {
                //     config: {
                //         name: 'numero_tarjeta',
                //         fieldLabel: 'Nro Tarjeta',
                //         allowBlank: true,
                //         width: '100%',
                //         gwidth: 110,
                //         galign: 'right ',
                //         maxLength: 100
                //     },
                //     type: 'TextField',
                //     filters: {pfiltro: 'fp.numero_tarjeta', type: 'string'},
                //     id_grupo: 1,
                //     grid: true,
                //     form: true
                // },

                {
                    config: {
                        name: 'observaciones',
                        fieldLabel: 'Observaciones',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 300,
                        galign: 'right ',
                        maxLength: 100
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'vent.observaciones', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },

            ];


            //llama al constructor de la clase padre
            Phx.vista.DetalleFacturacion.superclass.constructor.call(this, config);

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

        		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#F1F7FF';
        		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#dfe8f6';
            this.iniciarEventos();


        },


        tam_pag: 50,

        ActList: '../../sis_ventas_facturacion/control/ReportesVentas/ReporteFacturaComputarizada',
        id_store: 'id_int_transaccion',
        fields: [
            {name: 'nombre', type: 'varchar'},
            {name: 'id_venta', type: 'numeric'},
            {name: 'fecha', type: 'varchar'},
            {name: 'nro_factura', type: 'numeric'},
            {name: 'total_venta', type: 'numeric'},
            {name: 'excento', type: 'numeric'},
            {name: 'comision', type: 'numeric'},
            {name: 'cantidad', type: 'numeric'},
            {name: 'conceptos', type: 'varchar'},
            {name: 'precio', type: 'numeric'},
            {name: 'total_precio', type: 'numeric'},
            {name: 'forma_pago', type: 'varchar'},
            {name: 'medio_pago', type: 'varchar'},
            {name: 'numero_tarjeta', type: 'varchar'},
            {name: 'numero_tarjeta', type: 'varchar'},
            {name: 'observaciones', type: 'varchar'},
            {name: 'tipo_reg', type: 'string'},
            {name: 'total_monto_facturas', type: 'numeric'},
            {name: 'total_excentos', type: 'numeric'},
            {name: 'total_comision', type: 'numeric'},
            {name: 'total_precio_unitario', type: 'numeric'},
            {name: 'total_forma_pago', type: 'numeric'},
        ],


        rowExpander: new Ext.ux.grid.RowExpander({
            tpl: new Ext.Template(
                '<br>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Conceptos:&nbsp;&nbsp;</b> {conceptos}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Cantidad:&nbsp;&nbsp;</b> {cantidad}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Precio/Unitario:&nbsp;&nbsp;</b> {precio}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Precio Total:&nbsp;&nbsp;</b> {total_precio}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Formas de Pago:&nbsp;&nbsp;</b> {forma_pago}</p><br>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Medios de Pago:&nbsp;&nbsp;</b> {medio_pago}</p><br>'
            ),

            renderer: function(v, p, record) {
              console.log("aqui llega detalle total",record.data);
              if (record.data['tipo_reg'] == 'summary') {

              } else {
                return '<div class="x-grid3-row-expander"></div>';
              }

            },
            // renderer: function(v, p, record) {
            //   console.log("aqui llega detalle total",record.data);
            //   if (record.data['glosa1'] == 'SALDO ANTERIOR' || record.data['tipo_reg'] == 'summary') {
            //
            //   } else {
            //     return '<div class="x-grid3-row-expander"></div>';
            //   }
            //
            // },

        }),

        //arrayDefaultColumHidden: ['fecha_mod', 'usr_reg', 'usr_mod', 'estado_reg', 'fecha_reg',],

        sortInfo: {
            field: 'nro_factura',
            direction: 'ASC'
        },
        bdel: true,
        bsave: false,
        loadValoresIniciales: function () {
            Phx.vista.DetalleFacturacion.superclass.loadValoresIniciales.call(this);
            //this.getComponente('id_int_comprobante').setValue(this.maestro.id_int_comprobante);
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
              console.log("aqui llega data",this.store);
              Ext.Ajax.request({
                  url: '../../sis_ventas_facturacion/control/ReportesVentas/ReporteFacturaComputarizada',
                  params: {
                      formato_reporte: this.store.baseParams.formato_reporte,
                      id_punto_venta: this.store.baseParams.id_punto_venta,
                      id_concepto: this.store.baseParams.id_concepto,
                      desde: this.store.baseParams.desde,
                      hasta: this.store.baseParams.hasta,
                      imprimir_reporte: 'si'
                  },
                  success: this.successExport,
                  failure: this.conexionFailure,
                  timeout: this.timeout,
                  scope: this
              });

        },
        /***************************************************************************************/
        ExtraColumExportDet: [{
            label: 'Partida',
            name: 'desc_partida',
            width: '200',
            type: 'string',
            gdisplayField: 'desc_partida',
            value: 'desc_partida'
        },
            {
                label: 'Cbte',
                name: 'nro_cbte',
                width: '100',
                type: 'string',
                gdisplayField: 'nro_cbte',
                value: 'nro_cbte'
            }],
        //mpmpmp
        postReloadPage: function (data) {
            console.log("Aqui data irva",data);
            ini = data.desde;
            fin = data.hasta;


        },

        bnew: false,
        bedit: false,
        bdel: false,
        bexcel:false,
      	btest:false
    })
</script>
