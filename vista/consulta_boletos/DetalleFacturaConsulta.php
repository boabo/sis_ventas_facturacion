<?php
/**
 * @package pXP
 * @file DetalleFacturaConsulta.php
 * @author  (breydi.vasquez)
 * @date 01-03-2021
 * @description Archivo con la interfaz de usuario que permite generar el reporte de las facturas
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<style>
#ventanaEmergente:hover {
  background-color: #91FF81;
  font-size: 20px;
}
</style>
<script>
    Phx.vista.DetalleFacturaConsulta = Ext.extend(Phx.gridInterfaz, {
        constructor: function (config) {
            var me = this;
            this.maestro = config.maestro;

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
                        fieldLabel: 'Nro. Documento',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 100,
                        renderer: function(value,p,record){
                          if(record.data.tipo_reg != 'summary'){
                                return String.format('{0}','<b id="ventanaEmergente"><i class="fa fa-share" aria-hidden="true"></i> &nbsp;&nbsp;&nbsp;'+record.data['nro_factura']+'</b>');
					                      // return String.format('{0}','<i class="fa fa-reply-all" aria-hidden="true"></i> &nbsp;&nbsp;&nbsp;'+record.data['nro_factura']);
                          }else{
                            return ''
                          }
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
                        name: 'tipo_factura',
                        fieldLabel: 'Tipo Documento',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 150,
                        renderer:function (value,p,record){
                          return  String.format('<div style="text-align:center;">{0}</div>',record.data['tipo_factura']);
                        }
                    },
                    type: 'TextField',
                    bottom_filter: true,
                    filters: {pfiltro: 'v.tipo_factura', type: 'string'},
                    id_grupo: 1,
                    grid: true
                },
                {
                    config: {
                        name: 'estado',
                        fieldLabel: 'Estado',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 100,
                        renderer:function (value,p,record){
                          if(record.data.tipo_reg != 'summary'){
                            return  String.format('<div style="text-align:center;">{0}</div>',(record.data['estado']=='finalizado')?'VALIDA':'ANULADO');
                          }else{
                            return '';
                          }
                        }
                    },
                    type: 'TextField',
                    bottom_filter: true,
                    filters: {pfiltro: 'v.estado', type: 'string'},
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
                          if(record.data.tipo_reg != 'summary'){
                            return  String.format('<div style="float:center;">{0}</div>',record.data['nroaut']);
                          }else{
                            return '<hr><b><p style="font-size:20px; float:right; color:green; border-top:2px;">Totales: &nbsp;&nbsp; </p></b>';
                          }
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
                        gwidth: 150,
                        renderer : function(value, p, record) {
            		          if(record.data.tipo_reg != 'summary'){
            		            return  String.format('<div style="float:right;"><b>{0}<b></div>', Ext.util.Format.number(record.data.total_venta,'0.000,00/i'));
            		          }else{
            		            return  String.format('<hr><div style="font-size:20px; float:right; color:#004DFF;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.mt_venta,'0.000,00/i'));
            		          }
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
                        gwidth: 150,
                        renderer:function (value,p,record){
                          if(record.data.tipo_reg != 'summary'){
                            return  String.format('<div style="float:right;"><b>{0}<b></div>', Ext.util.Format.number(record.data.excento,'0.000,00/i'));
                          }else{
                            return  String.format('<hr><div style="font-size:20px; float:right; color:#004DFF;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.mt_excento,'0.000,00/i'));
                          }
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
                        gwidth: 300
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true
                },
                {
                    config: {
                        name: 'nro_boleto',
                        fieldLabel: 'N° Boleto Asociado',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 120,
                        renderer:function (value,p,record){
                          if (value == null || value == ''){
                              return  String.format('<div style="float:center;"><b><b></div>');
                          }else{
                              return  String.format('<div style="float:center;"><b>{0}<b></div>',record.data['nro_boleto']);
                          }
                        }
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true
                },
                {
                    config: {
                        name: 'nro_deposito',
                        fieldLabel: 'Nro Deposito',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 120,
                        renderer:function (value,p,record){
                          if (value == null || value == ''){
                              return  String.format('<div style="text-align:center;"><b><b></div>');
                          }else{
                              return  String.format('<div style="text-align:center;"><b>{0}<b></div>',record.data['nro_deposito']);
                          }
                        }
                    },
                    type: 'TextField',
                    bottom_filter: true,
                    filters: {pfiltro: 'obd.nro_deposito', type: 'string'},
                    id_grupo: 1,
                    grid: true
                },
                {
                    config: {
                        name: 'monto_total',
                        fieldLabel: 'Monto Total Deposito',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 150,
                        renderer:function (value,p,record){
                          if(record.data.tipo_reg != 'summary'){
                            return  String.format('<div style="float:right;"><b>{0}<b></div>', Ext.util.Format.number(record.data.monto_total,'0.000,00/i'));
                          }else{

                            return  String.format('<hr><div style="font-size:20px; float:right; color:#004DFF;"><b><font>{0}</font><b></div>', Ext.util.Format.number((record.data.mt_total==null)?0.00:record.data.mt_total,'0.000,00/i'));
                          }
                        }
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    grid: true
                },
                {
                    config: {
                        name: 'fecha_dep',
                        fieldLabel: 'Fecha Deposito',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 150,
                        renderer:function (value,p,record){
                          return  String.format('<div style="float:center;">{0}</div>',value?value.dateFormat('d/m/Y'):'');
                        }
                    },
                    type: 'DateField',
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
            this.addButton('btnRepExcel', {
              text: '<center>Generar Reporte Excel</center>',
              iconCls: 'bexcel',
              disabled: false,
              handler: this.ReporteEXCEL,
              tooltip: '<b>Generar Reporte Excel'
            });
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
            {name: 'id_venta', type: 'numeric'},
            {name: 'nro_factura', type: 'string'},
            {name: 'nit', type: 'string'},
            {name: 'nombre_factura', type: 'string'},
            {name: 'cod_control', type: 'string'},
            {name: 'fecha_factura', type:'date',dateFormat: 'Y-m-d'},
            {name: 'observaciones', type: 'string'},
            {name: 'total_venta', type: 'numeric'},
            {name: 'excento', type: 'numeric'},
            {name: 'tipo_factura', type: 'string'},
            {name: 'nroaut', type: 'string'},
            {name: 'punto_venta', type: 'string'},
            {name: 'desc_persona', type: 'string'},
            {name: 'nro_deposito', type: 'string'},
            {name: 'monto_total', type: 'numeric'},
            {name: 'fecha_dep', type:'date',dateFormat: 'Y-m-d'},
            {name: 'nro_boleto', type: 'string'},
            {name: 'tipo_reg', type: 'string'},
            {name: 'mt_venta', type: 'numeric'},
            {name: 'mt_excento', type: 'numeric'},
            {name: 'mt_total', type: 'numeric'},
            {name: 'estado', type: 'string'},

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
        bexcel:false,
      	btest:false,
        abrirEnlace: function(cell,rowIndex,columnIndex,e){
          var data = this.sm.getSelected().data;
      		if(columnIndex==1 && data.id_venta != ''){
      			Phx.CP.loadWindows('../../../sis_ventas_facturacion/vista/consulta_boletos/VentanaDetalleFactura.php',
      			'<span style="font-size:14pt;padding-left: 35%;letter-spacing: 12px;">DETALLE DEL DOCUMENTO</span>', {
      				width:'91%',
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
        ReporteEXCEL: function () {
              Phx.CP.loadingShow();
              Ext.Ajax.request({
                  url: '../../sis_ventas_facturacion/control/ReporteVentas/RepConsultaDocumento',
                  params: {
                      id_entidad: this.store.baseParams.id_entidad,
                      id_sucursal: this.store.baseParams.id_sucursal,
                      id_punto_venta: this.store.baseParams.id_punto_venta,
                      tipo_documento: this.store.baseParams.tipo_documento,
                      nro_documento: this.store.baseParams.nro_documento,
                      nro_autorizacion: this.store.baseParams.nro_autorizacion,
                      estado_documento: this.store.baseParams.estado_documento,
                      fecha_ini: this.store.baseParams.fecha_ini,
                      fecha_fin: this.store.baseParams.fecha_fin,
                      nit: this.store.baseParams.nit,
                      re_count: 'no'
                  },
                  success: this.successExport,
                  failure: this.conexionFailure,
                  timeout: this.timeout,
                  scope: this
              });

        },
    })
</script>
