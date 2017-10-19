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
    Phx.vista.VentaApertura=Ext.extend(Phx.gridInterfaz,{
        nombreVista: 'VentaApertura',
        constructor:function(config) {
            this.maestro=config.maestro;
            Phx.vista.VentaApertura.superclass.constructor.call(this,config);
            this.init();
            this.addButton('diagrama_gantt', {
                grupo:[0,1,2],
                text:'Gant',
                iconCls: 'bgantt',
                disabled:true,
                handler:this.diagramGantt,
                tooltip: '<b>Diagrama Gantt de la venta</b>'});
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
                form:false
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
                form:false
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
                form:false
            },
            {
                config:{
                    name: 'nroaut',
                    fieldLabel: 'Nro Autorizacion',
                    gwidth: 200
                },
                type:'TextField',
                filters:{pfiltro:'dos.nroaut',type:'string'},
                grid:true,
                form:false
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
                form:false
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
                    fieldLabel: 'Punto de Venta',
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
        arrayDefaultColumHidden:['estado_reg','usuario_ai', 'fecha_reg','fecha_mod','usr_reg','usr_mod','nro_factura','fecha','cod_control','nroaut'],
        rowExpander: new Ext.ux.grid.RowExpander({
            tpl : new Ext.Template(
                '<br>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha de Registro:&nbsp;&nbsp;</b> {fecha_reg:date("d/m/Y")}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha Ult. Modificación:&nbsp;&nbsp;</b> {fecha_mod:date("d/m/Y")}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Creado por:&nbsp;&nbsp;</b> {usr_reg}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Modificado por:&nbsp;&nbsp;</b> {usr_mod}</p><br>'
            )
        })


    })
</script>
