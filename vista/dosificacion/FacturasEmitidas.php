<?php
/**
 *@package pXP
 *@file gen-SistemaDist.php
 *@author  (rarteaga)
 *@date 20-09-2011 10:22:05
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>


    Phx.vista.FacturasEmitidas=Ext.extend(Phx.gridInterfaz,{
        tipo_factura: 'computarizada',
        punto: 'sucursal',

            constructor:function(config) {
                //this.idContenedor = config.idContenedor;
                this.maestro=config.maestro;
                //this.id_dosificacion=this.maestro.id_dosificacion;
                Phx.vista.FacturasEmitidas.superclass.constructor.call(this,config);
                this.init();
                console.log('id:',this.maestro.id_dosificacion);
                this.store.baseParams = {id_dosificacion:this.maestro.id_dosificacion,tipo_factura: this.maestro.tipo_generacion,punto:this.punto};
                this.load({params:{start:0, limit:this.tam_pag}});
                this.addButton('btnImprimir',
                    {   grupo:[0,1,2],
                        text: 'Imprimir',
                        iconCls: 'bpdf32',
                        disabled: true,
                        handler: this.imprimirNota,
                        tooltip: '<b>Imprimir Formulario de Venta</b><br/>Imprime el formulario de la venta'
                    }
                );
                this.getBoton('btnImprimir').enable();

            },
            diagramGantt : function (){
                var data=this.sm.getSelected().data.id_proceso_wf;
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url:'../../sis_workflow/control/ProcesoWf/diagramaGanttTramite',
                    params:{'id_proceso_wf':data},
                    success:this.successExport,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
            },



            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_venta'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_cliente'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_dosificacion'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_punto_venta'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config:{
                        name: 'nro_factura',
                        fieldLabel: 'Nro Factura',
                        gwidth: 110
                    },
                    type:'TextField',
                    filters:{pfiltro:'ven.nro_factura',type:'string'},
                    grid:true,
                    form:false,
                    bottom_filter:true
                },
                {
                    config:{
                        name: 'nombre_completo',
                        fieldLabel: 'Cliente',
                        gwidth: 200
                    },
                    type:'TextField',
                    filters : {pfiltro : 'vcl.nombre_completo',type : 'string'},
                    grid:true,
                    form:false,
                    bottom_filter:true
                },
                {
                    config:{
                        name: 'nit',
                        fieldLabel: 'Nit',
                        gwidth: 110
                    },
                    type:'TextField',
                    filters : {pfiltro : 'vcl.nit',type : 'string'},
                    grid:true,
                    form:false,
                    bottom_filter:true
                },
                {
                    config:{
                        name: 'nroaut',
                        fieldLabel: 'Nro Autorizacion',
                        gwidth: 200
                    },
                    type:'TextField',
                    filters:{pfiltro:'ven.correlativo_venta',type:'string'},
                    grid:true,
                    form:false,
                    bottom_filter:true
                },
                {
                    config:{
                        name: 'cod_control',
                        fieldLabel: 'Codigo Control',
                        gwidth: 110
                    },
                    type:'TextField',
                    filters:{pfiltro:'ven.cod_control',type:'string'},
                    grid:true,
                    form:false,
                    bottom_filter:true
                },
                {
                    config:{
                        name: 'total_venta',
                        fieldLabel: 'Total Venta',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 120,
                        maxLength:5,
                        disabled:true
                    },
                    type:'NumberField',
                    filters:{pfiltro:'ven.total_venta',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'fecha_doc',
                        fieldLabel: 'Fecha Doc.',
                        gwidth: 110,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters: { pfiltro:'ven.fecha', type:'date'},
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'sucursal',
                        fieldLabel: 'Sucursal',
                        gwidth: 200
                    },
                    type:'TextField',
                    filters: { pfiltro: 's.nombre', type: 'string'},
                    grid: true,
                    form: false
                },

                {
                    config:{
                        name: 'forma_pago',
                        fieldLabel: 'Forma de Pago',
                        gwidth: 200
                    },
                    type:'TextField',
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'observaciones',
                        fieldLabel: 'Observaciones',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150
                    },
                    type:'TextArea',
                    filters:{pfiltro:'ven.observaciones',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'monto_forma_pago',
                        fieldLabel: 'Importe Recibido',
                        allowBlank: false,
                        gwidth: 120,
                        maxLength:5,
                        disabled:true
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'comision',
                        fieldLabel: 'Comisión',
                        gwidth: 120,
                        maxLength:5,
                        disabled:true
                    },
                    type:'NumberField',
                    grid:true

                },
                {
                    config:{
                        name: 'estado',
                        fieldLabel: 'Estado',
                        gwidth: 100
                    },
                    type:'TextField',
                    filters:{pfiltro:'ven.estado',type:'string'},
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'tipo_factura',
                        fieldLabel: 'Tipo Factura',
                        gwidth: 100
                    },
                    type:'TextField',
                    filters:{pfiltro:'ven.estado',type:'string'},
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'fecha_reg',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'ven.fecha_reg',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'usr_reg',
                        fieldLabel: 'Creado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'Field',
                    filters:{pfiltro:'usu1.cuenta',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'id_usuario_ai',
                        fieldLabel: 'Creado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'Field',
                    filters:{pfiltro:'ven.id_usuario_ai',type:'numeric'},
                    id_grupo:1,
                    grid:false,
                    form:false
                },
                {
                    config:{
                        name: 'usuario_ai',
                        fieldLabel: 'Funcionaro AI',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'ven.usuario_ai',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'usr_mod',
                        fieldLabel: 'Modificado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'Field',
                    filters:{pfiltro:'usu2.cuenta',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'fecha_mod',
                        fieldLabel: 'Fecha Modif.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'ven.fecha_mod',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'estado_reg',
                        fieldLabel: 'Estado Reg.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:10
                    },
                    type:'TextField',
                    filters:{pfiltro:'ven.estado_reg',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                }
            ],
            tam_pag:50,
            title:'Ventas',
            ActSave:'../../sis_ventas_facturacion/control/Venta/insertarVenta',
            ActDel:'../../sis_ventas_facturacion/control/Venta/eliminarVenta',
            ActList:'../../sis_ventas_facturacion/control/Venta/listarVentasDosificaciones',
            id_store:'id_venta',
            fields: [
                {name:'id_venta', type: 'numeric'},
                {name:'id_cliente', type: 'numeric'},
                {name:'id_dosificacion', type: 'numeric'},
                {name:'id_punto_venta', type: 'numeric'},
                {name:'nro_factura', type: 'string'},
                {name:'nombre_completo', type: 'string'},
                {name:'nit', type: 'string'},
                {name:'total_venta', type: 'numeric'},
                {name:'fecha_doc', type: 'date',dateFormat:'Y-m-d'},
                {name:'sucursal', type: 'string'},
                {name:'id_forma_pago', type: 'numeric'},
                {name:'forma_pago', type: 'numeric'},
                {name:'observaciones', type: 'string'},
                {name:'monto_forma_pago', type: 'numeric'},
                {name:'comision', type: 'numeric'},
                {name:'estado', type: 'string'},
                {name:'cod_control', type: 'string'},
                {name:'tipo_factura', type: 'string'},
                {name:'usuario_ai', type: 'string'},
                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'id_usuario_reg', type: 'numeric'},
                {name:'id_usuario_ai', type: 'numeric'},
                {name:'id_usuario_mod', type: 'numeric'},
                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'estado_reg', type: 'string'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mo',type:'string'},
                {name:'nroaut',type:'string'}

            ],
            sortInfo:{
                field: 'id_venta',
                direction: 'DESC'
            },
            bdel:false,
            bsave:false,
            bedit:false,
            bnew:false,
            rowExpander: new Ext.ux.grid.RowExpander({
                tpl : new Ext.Template(
                    '<br>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha de Registro:&nbsp;&nbsp;</b> {fecha_reg:date("d/m/Y")}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha Ult. Modificación:&nbsp;&nbsp;</b> {fecha_mod:date("d/m/Y")}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Creado por:&nbsp;&nbsp;</b> {usr_reg}</p>',
                    '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Modificado por:&nbsp;&nbsp;</b> {usr_mod}</p><br>'
                )
            }),
        loadValoresIniciales:function()
        {
            this.Cmp.id_dosificacion.setValue(this.id_dosificacion);
            Phx.vista.FacturasEmitidas.superclass.loadValoresIniciales.call(this);
        },

        imprimirNota: function(){
            var rec = this.sm.getSelected(),
                data = rec.data,
                me = this;
            if (data) {
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url : '../../sis_ventas_facturacion/control/Venta/reporteFacturaRecibo',
                    params : {
                        'id_venta' : data.id_venta,
                        'formato_comprobante' : "FACPAPELTERM",
                        'tipo_factura': me.tipo_factura
                    },
                    success : me.successExportHtml,
                    failure : me.conexionFailure,
                    timeout : me.timeout,
                    scope : me
                });
            }
        },
        successExportHtml: function (resp) {
            Phx.CP.loadingHide();
            var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log('hola',objRes);
            var objetoDatos = (objRes.ROOT == undefined)?objRes.datos:objRes.ROOT.datos;
            var wnd = window.open("about:blank", "", "_blank");
            wnd.document.write(objetoDatos.html);
        }


        })
</script>
