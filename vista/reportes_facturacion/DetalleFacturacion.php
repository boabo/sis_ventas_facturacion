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
<style>
.punto_venta {
    background-color: #319DFD;
    font-size: 20px;
}
.anulado {
    background-color: #FA7967;
}

</style>

<script>
    var ini = null;
    var fin = null;

    var formato = null;
    var punto_venta = null;
    var concepto = null;



    Phx.vista.DetalleFacturacion = Ext.extend(Phx.gridInterfaz, {
        title: 'Mayor',

        viewConfig: {
            //stripeRows: false,
            autoFill: false,
            getRowClass: function (record) {
                if (record.data.tipo_factura == 'cabecera') {
                  return 'punto_venta';
                }

                if (record.data.estado == 'ANULADA') {
                  return 'anulado';
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
                        gwidth: 400,
                        maxLength: 1000,
                        renderer: function (value, p, record) {
                        if (record.data.tipo_factura == 'cabecera') {
                          return '<b>Detalle PV: '+record.data.nombre+' ('+record.data.codigo+')</b>';
                        } else {
                          return value;
                        }

                      },

                    },
                    type: 'TextField',
                    filters: {pfiltro: 'nombre', type: 'string'},
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
                        name: 'tipo_factura',
                        fieldLabel: 'Tipo Documento',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 200,
                        maxLength: 100,
                        renderer: function (value, p, record) {

                        if (value != null && value != 'cabecera') {
                          return value;
                        } else {
                          return '';
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
                        name: 'nro_factura',
                        fieldLabel: 'Nro. Documento',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength: 1000,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  value;
                            }
                            else{
                                return '<b><p style="font-size:15px; color:red; text-align:right; text-decoration: border-top:2px;">Totales:</p></b>';
                            }
                        },
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
                    filters: {pfiltro: 'nro_factura', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },

                {
                    config: {
                        name: 'cod_control',
                        fieldLabel: 'Cod. Control',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 200,
                        maxLength: 100,
                        renderer: function (value, p, record) {

                        if (value != null && value != 'cabecera') {
                          return value;
                        } else {
                          return '';
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
                        name: 'total_venta',
                        fieldLabel: 'Total Documento',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 110,
                        galign: 'right ',
                        maxLength: 100,

                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  value;
                            }
                            else{
                                return  String.format('<div style="font-size:15px; text-align:right; color:red;"><b>{0}<b></div>', Ext.util.Format.number(record.data.totales_venta,'0,000.00'));
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
                        name: 'exento',
                        fieldLabel: 'Exentos',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 110,
                        galign: 'right ',
                        maxLength: 100,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  value;
                            }
                            else{
                                return  String.format('<div style="font-size:15px; text-align:right; color:red;"><b>{0}<b></div>', Ext.util.Format.number(record.data.totales_exento,'0,000.00'));
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
                        name: 'comision',
                        fieldLabel: 'Comision',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 110,
                        galign: 'right ',
                        maxLength: 100,
                        renderer:function (value,p,record){
                            if(record.data.tipo_reg != 'summary'){
                                return  value;
                            }
                            else{
                                return  String.format('<div style="font-size:15px; text-align:right; color:red;"><b>{0}<b></div>', Ext.util.Format.number(record.data.totales_comision,'0,000.00'));
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
                        name: 'conceptos',
                        fieldLabel: 'Detalle Conceptos',
                        allowBlank: true,
                        gwidth: 600,
                        maxLength: 100,
                        renderer: function (value, p, record) {

                          if (value != null && record.data.tipo_reg != 'summary') {
                            var concepto = value.split(",");
                            var cantidad = record.data.cantidad.split(",");
                            var precio = record.data.precio.split(",");
                            var precio_total = record.data.total_precio.split(",");
                            var total_concepto = concepto.length;
                            var info = `<table border="2" >
                                            <tbody>
                                              <tr>
                                                <td style="font-size:13px; background-color: #54B6FF;"><b>Concepto</b></td>
                                                <td style="font-size:13px; background-color: #54B6FF;"><b>Cantidad</b></td>
                                                <td style="font-size:13px; background-color: #54B6FF;"><b>Precio/Unitario</b></td>
                                                <td style="font-size:13px; background-color: #54B6FF;"><b>Precio/Total</b></td>
                                              </tr>`;
                          for (var i = 0; i < total_concepto; i++) {
                            info += `
                                        <tr>
                                            <td style="font-size:13px; background-color: #AFDBFC;" align="center">${concepto[i]}</td>
                                            <td style="font-size:13px; background-color: #AFDBFC;" align="center">${parseFloat(cantidad[i])}</td>
                                            <td style="font-size:13px; background-color: #AFDBFC;" align="center">${parseFloat(precio[i])}</td>
                                            <td style="font-size:13px; background-color: #AFDBFC;" align="center">${parseFloat(precio_total[i])}</td>
                                        </tr>
                                    `;
                          }

                              info += `</table>`;
                              return info;
                          } else {
                            return  String.format('<div style="font-size:15px; text-align:right; color:green;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_detalle,'0,000.00'));

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
                        name: 'forma_pago',
                        fieldLabel: 'Detalle Formas de Pago',
                        allowBlank: true,
                        gwidth: 400,
                        maxLength: 100,
                        renderer: function (value, p, record) {

                        if (value != null && record.data.tipo_reg != 'summary') {
                          var forma_pago = value.split(",");
                          var total_monto = record.data.total_monto.split(",");
                          var medio_pago = record.data.medio_pago.split(",");
                          var total_forma_pago = forma_pago.length;
                          var info2 = `<table border="2">
                                          <tbody>
                                            <tr>
                                            <td style="font-size:13px; background-color: #88FD13;"><b>Forma de Pago</b></td>
                                            <td style="font-size:13px; background-color: #88FD13;"><b>Medio de Pago</b></td>
                                            <td style="font-size:13px; background-color: #88FD13;"><b>Monto</b></td>
                                            </tr>`;
                        for (var i = 0; i < total_forma_pago; i++) {
                          info2 += `
                                      <tr>
                                          <td style="font-size:13px; background-color: #C2FF85;" align="center">${forma_pago[i]}</td>
                                          <td style="font-size:13px; background-color: #C2FF85;" align="center">${medio_pago[i]}</td>
                                          <td style="font-size:13px; background-color: #C2FF85;" align="center">${parseFloat(total_monto[i])}</td>
                                      </tr>
                                  `;
                        }

                            info2 += `</table>`;
                            return info2;
                        } else {
                          return '';
                        }


                      },
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },

                // {
                //     config: {
                //         name: 'precio',
                //         fieldLabel: 'Precio/Unit',
                //         allowBlank: true,
                //         width: '100%',
                //         gwidth: 110,
                //         galign: 'right ',
                //         maxLength: 100,
                //
                //     },
                //     type: 'NumberField',
                //     id_grupo: 1,
                //     grid: false,
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

                {
                    config: {
                        name: 'estado',
                        fieldLabel: 'Estado Documento',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 110,
                        maxLength: 100,
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'cajero',
                        fieldLabel: 'Cajero',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 300,
                        maxLength: 100,
                    },
                    type: 'TextField',
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
            {name: 'exento', type: 'numeric'},
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
            {name: 'total_monto', type: 'numeric'},
            {name: 'total_excentos', type: 'numeric'},
            {name: 'total_comision', type: 'numeric'},
            {name: 'total_precio_unitario', type: 'numeric'},
            {name: 'total_forma_pago', type: 'numeric'},
            {name: 'tipo_factura', type: 'varchar'},
            {name: 'cajero', type: 'varchar'},
            {name: 'estado', type: 'varchar'},
            {name: 'codigo', type: 'varchar'},
            {name:'tipo_reg', type: 'string'},
            {name:'totales_comision', type: 'numeric'},
            {name:'totales_exento', type: 'numeric'},
            {name:'totales_venta', type: 'numeric'},
            {name:'total_detalle', type: 'numeric'},
            {name:'cod_control', type: 'numeric'},
        ],


        // rowExpander: new Ext.ux.grid.RowExpander({
        //     tpl: new Ext.Template(
        //         '<br>',
        //         '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Conceptos:&nbsp;&nbsp;</b> {conceptos}</p>',
        //         '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Cantidad:&nbsp;&nbsp;</b> {cantidad}</p>',
        //         '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Precio/Unitario:&nbsp;&nbsp;</b> {precio}</p>',
        //         '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Precio Total:&nbsp;&nbsp;</b> {total_precio}</p>',
        //         '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Formas de Pago:&nbsp;&nbsp;</b> {forma_pago}</p><br>',
        //         '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Medios de Pago:&nbsp;&nbsp;</b> {medio_pago}</p><br>'
        //     ),

            // renderer: function(v, p, record) {
            //   console.log("aqui llega detalle total",record.data);
            //   if (record.data['tipo_reg'] == 'summary') {
            //
            //   } else {
            //     return '<div class="x-grid3-row-expander"></div>';
            //   }
            //
            // },
            // renderer: function(v, p, record) {
            //   console.log("aqui llega detalle total",record.data);
            //   if (record.data['glosa1'] == 'SALDO ANTERIOR' || record.data['tipo_reg'] == 'summary') {
            //
            //   } else {
            //     return '<div class="x-grid3-row-expander"></div>';
            //   }
            //
            // },

        //}),

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

            if (this.store.baseParams.formato_reporte == 'REPORTE DE FACTURAS') {
              this.cm.setHidden(5, false);
            } else {
              this.cm.setHidden(5, true);
            }



        },

        initFiltro: function (param) {
            this.store.baseParams = param;
            this.load({params: {start: 0, limit: this.tam_pag}});
        },

        ReporteEXCEL: function () {
              Phx.CP.loadingShow();
              Ext.Ajax.request({
                  url: '../../sis_ventas_facturacion/control/ReportesVentas/ReporteFacturaComputarizada',
                  params: {
                      formato_reporte: this.store.baseParams.formato_reporte,
                      id_punto_venta: this.store.baseParams.id_punto_venta,
                      id_concepto: this.store.baseParams.id_concepto,
                      desde: this.store.baseParams.desde,
                      hasta: this.store.baseParams.hasta,
                      id_usuario_cajero: this.store.baseParams.id_usuario_cajero,
                      tipo_documento: this.store.baseParams.tipo_documento,
                      nombre_pv: this.store.baseParams.nombre_pv,
                      nit: this.store.baseParams.nit,
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
