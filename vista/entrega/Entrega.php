<?php
/**
*@package pXP
*@file gen-Entrega.php
*@author  (admin)
*@date 12-09-2017 15:04:26
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>

    Phx.vista.Entrega=Ext.extend(Phx.gridInterfaz,{
        mosttar:'',

        solicitarPuntoVenta: true,

	constructor:function(config){
		this.maestro=config.maestro;
        this.tipo_usuario = 'cajero';
        this.tbarItems = ['-',
            this.cmbPuntoV,'-'
        ];
        Ext.Ajax.request({
            url:'../../sis_ventas_facturacion/control/Venta/getVariablesBasicas',
            params: {'prueba':'uno'},
            success:this.successGetVariables,
            failure: this.conexionFailure,
            arguments:config,
            timeout:this.timeout,
            scope:this
        });
        this.cmbPuntoV.on('select', function( combo, record, index){
            this.capturaFiltros();
        },this);
    },
    successGetVariables : function (response,request) {

        var respuesta = JSON.parse(response.responseText);
        if('datos' in respuesta){
            this.variables_globales = respuesta.datos;
        }
        if(this.solicitarPuntoVenta){
            this.seleccionarPuntoVentaSucursal();

        }
        Phx.vista.Entrega.superclass.constructor.call(this,request.arguments);
        this.addButton('Report',{
            grupo:[0,1],
            text :'Entrega de Remesas',
            iconCls : 'bpdf32',
            disabled: true,
            handler : this.onButtonReporte,
            tooltip : '<b>Reporte Formulario de Entrega de Remesas</b>'
        });
        this.init();
        this.getBoton('Report').enable();
    },
    seleccionarPuntoVentaSucursal : function () {
        var validado = false;
        var title;
        var value;
        title = 'Punto de Venta';
        value = 'id_punto_venta';
        var storeCombo = new Ext.data.JsonStore({
            url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
            id: 'id_punto_venta',
            root: 'datos',
            sortInfo: {
                field: 'nombre',
                direction: 'ASC'
            },
            totalProperty: 'total',
            fields: ['id_punto_venta', 'id_sucursal','nombre', 'codigo','habilitar_comisiones','formato_comprobante'],
            remoteSort: true,
            baseParams: {tipo_usuario: this.tipo_usuario,par_filtro: 'puve.nombre#puve.codigo', tipo_factura: this.tipo_factura}
        });
        storeCombo.load({params:{start: 0, limit: this.tam_pag},
            callback : function (r) {
                if (r.length == 1) {
                        this.variables_globales.id_punto_venta = r[0].data.id_punto_venta;
                        this.store.baseParams.id_punto_venta = this.variables_globales.id_punto_venta;
                        this.load({params:{start:0, limit:this.tam_pag}});
                } else {
                    var combo2 = new Ext.form.ComboBox(
                        {
                            typeAhead: false,
                            fieldLabel: title,
                            allowBlank : false,
                            store: storeCombo,
                            mode: 'remote',
                            pageSize: 15,
                            triggerAction: 'all',
                            valueField : value,
                            displayField : 'nombre',
                            forceSelection: true,
                            tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                            allowBlank : false,
                            anchor: '100%',
                            resizable : true
                        });
                    var formularioInicio = new Ext.form.FormPanel({
                        items: [combo2],
                        padding: true,
                        bodyStyle:'padding:5px 5px 0',
                        border: false,
                        frame: false
                    });
                    var VentanaInicio = new Ext.Window({
                        title: 'Punto de Venta ',
                        modal: true,
                        width: 500,
                        height: 160,
                        bodyStyle: 'padding:5px;',
                        layout: 'fit',
                        hidden: true,
                        buttons: [
                            {
                                text: '<i class="fa fa-check"></i> Aceptar',
                                handler: function () {
                                    if (formularioInicio.getForm().isValid()) {
                                        validado = true;
                                        this.variables_globales.habilitar_comisiones = combo2.getStore().getById(combo2.getValue()).data.habilitar_comisiones;
                                        this.variables_globales.formato_comprobante = combo2.getStore().getById(combo2.getValue()).data.formato_comprobante;
                                        VentanaInicio.close();
                                        this.variables_globales.id_punto_venta = combo2.getValue();

                                        Ext.Ajax.request({
                                            url:'../../sis_ventas_facturacion/control/Entrega/getPuntoVen',
                                            params:{id_punto_venta : this.variables_globales.id_punto_venta},
                                            success:function(resp){
                                                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                                                console.log('datos',reg);
                                                this.cmbPuntoV.setValue(reg.ROOT.datos.id_punto_venta);
                                                this.cmbPuntoV.setRawValue(reg.ROOT.datos.nombre);
                                            },
                                            failure: this.conexionFailure,
                                            timeout:this.timeout,
                                            scope:this
                                        });
                                        this.store.baseParams.id_punto_venta = this.variables_globales.id_punto_venta;
                                        this.load({params:{start:0, limit:this.tam_pag}});
                                    }
                                },
                                scope: this
                            }],
                        items: formularioInicio,
                        autoDestroy: true,
                        closeAction: 'close'
                    });
                    VentanaInicio.show();
                    VentanaInicio.on('beforeclose', function (){
                        if (!validado) {
                            alert('Debe seleccionar el punto de venta o sucursal de trabajo');
                            return false;
                        }
                    },this)
                }

            }, scope : this
        });



    },
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_entrega_brinks'
			},
			type:'Field',
			form:true 
		},
        {
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
				name: 'fecha_recojo',
				fieldLabel: 'Fecha Recojo',
				allowBlank: true,
				anchor: '80%',
				gwidth: 225,
                format: 'd/m/Y',
                renderer: function(value,p,record){
				    if (record.data['fecha_recojo'] == null){
                        return '<tpl for="."><div class="x-combo-list-item"><p><b>Fecha Recojo:</b> <font color="#a52a2a"><b>'+record.data['fecha_recojo']+'</b></font><p><b>Cajero:</b> <font color="#191970"><b>'+record.data['cajero']+'</b></font></p></div></tpl>';
                    }else{
                        return '<tpl for="."><div class="x-combo-list-item"><p><b>Fecha Recojo:</b> <font color="#a52a2a"><b>'+record.data['fecha_recojo'].dateFormat('d/m/Y')+'</b></font><p><b>Cajero:</b> <font color="#191970"><b>'+record.data['cajero']+'</b></font></p></div></tpl>';
                    }

                }
			},
				type:'DateField',
				filters:{pfiltro:'eng.fecha_recojo',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
        {
            config:{
                name: 'nombre_punto_venta',
                fieldLabel: 'Punto de Venta / Codigo / Estacion',
                allowBlank: true,
                anchor: '80%',
                gwidth: 270,
                maxLength:100,
                disabled: true,
                renderer: function(value,p,record){
                    return '<tpl for="."><div class="x-combo-list-item"><p><b>Punto de venta: </b> <font color="#006400"><b>'+record.data['nombre_punto_venta']+'</b></font></p><p><b>Codigo: </b><font color="#dc143c"><b>'+record.data['codigo']+'</b></font></p> <p><b>Estacion: </b><font color="#191970"><b>'+record.data['estacion']+'</b></font></p></div></tpl>';

                }
            },
            type:'TextField',
            grid:true,
            form:false
        },
        {
            config:{
                name: 'arqueo_moneda_local',
                fieldLabel: 'Importe Total (Bs)',
                allowBlank: true,
                anchor: '80%',
                gwidth: 150,
                maxLength:1179650,
                renderer:function (value,p,record) {
                    var dato =  value.replace('.', ",")
                        .replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");
                    return '<div ext:qtip="Optimo"><p> <font color="blue"><b>'+dato+'</b></font></p></div>';
                }
            },
            type:'NumberField',
            filters:{pfiltro:'cdo.arqueo_moneda_local',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:false
        },
        {
            config:{
                name: 'arqueo_moneda_extranjera',
                fieldLabel: 'Importe Total ($us)',
                allowBlank: true,
                anchor: '80%',
                gwidth: 150,
                maxLength:1179650,
                renderer:function (value,p,record) {
                    var dato =  value.replace('.', ",")
                        .replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");
                    return '<div ext:qtip="Optimo"><p> <font color="red"><b>'+dato+'</b></font></p></div>';
                }
            },
            type:'NumberField',
            filters:{pfiltro:'cdo.arqueo_moneda_extranjera',type:'numeric'},
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
				filters:{pfiltro:'eng.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: '',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'eng.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
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
				name: 'usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:300
			},
				type:'TextField',
				filters:{pfiltro:'eng.usuario_ai',type:'string'},
				id_grupo:1,
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
				filters:{pfiltro:'eng.fecha_reg',type:'date'},
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
				filters:{pfiltro:'eng.fecha_mod',type:'date'},
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
		}
	],
	tam_pag:50,	
	title:'Entrega',
	ActSave:'../../sis_ventas_facturacion/control/Entrega/insertarEntrega',
	ActDel:'../../sis_ventas_facturacion/control/Entrega/eliminarEntrega',
	ActList:'../../sis_ventas_facturacion/control/Entrega/listarEntrega',
	id_store:'id_entrega_brinks',
	fields: [
		{name:'id_entrega_brinks', type: 'numeric'},
		{name:'fecha_recojo', type: 'date',dateFormat:'Y-m-d'},
		{name:'estado_reg', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
        {name:'arqueo_moneda_local', type: 'numeric'},
        {name:'arqueo_moneda_extranjera', type: 'numeric'},
        {name:'id_punto_venta', type: 'numeric'},
        {name:'nombre_punto_venta', type: 'string'},
        {name:'estacion', type: 'string'},
        {name:'codigo', type: 'string'},
        {name:'cajero', type: 'string'}
		
	],
	sortInfo:{
		field: 'id_entrega_brinks',
		direction: 'DESC'
	},
    tabsouth :[
        {
            url:'../../../sis_ventas_facturacion/vista/entrega/DepositoEntrega.php',
            title:'Apertura de Caja',
            height:'40%',
            cls:'DepositoEntrega'
        }
    ],
	bdel:true,
	bsave:false,

    cmbPuntoV: new Ext.form.ComboBox({
        name: 'punto_venta',
        id: 'id_punto_venta',
        fieldLabel: 'Punto Venta',
        allowBlank: true,
        emptyText:'Punto de Venta...',
        blankText: 'Año',
        store: new Ext.data.JsonStore({
            url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
            id: 'id_punto_venta',
            root: 'datos',
            sortInfo: {
                field: 'nombre',
                direction: 'ASC'
            },
            totalProperty: 'total',
            fields: ['id_punto_venta', 'id_sucursal','nombre', 'codigo','habilitar_comisiones','formato_comprobante'],
            remoteSort: true,
            baseParams: {tipo_usuario: 'cajero',par_filtro: 'puve.nombre#puve.codigo', tipo_factura: this.tipo_factura}
        }),
        tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
        valueField: 'id_punto_venta',
        triggerAction: 'all',
        displayField: 'nombre',
        hiddenName: 'id_punto_venta',
        mode:'remote',
        pageSize:50,
        queryDelay:500,
        listWidth:'300',
        hidden:false,
        width:300
    }),
        capturaFiltros:function(combo, record, index){
            this.desbloquearOrdenamientoGrid();
            this.store.baseParams.id_punto_venta = this.cmbPuntoV.getValue();
            this.load();
        },
        onButtonNew : function () {
            Phx.vista.Entrega.superclass.onButtonNew.call(this);
            this.Cmp.id_punto_venta.setValue(this.variables_globales.id_punto_venta);
           // if(this.variables_globales.id_punto_venta != this.cmbPuntoV.getValue()) {
                console.log(this.variables_globales.id_punto_venta,this.cmbPuntoV.getValue() );
                this.Cmp.id_punto_venta.setValue(this.cmbPuntoV.getValue());
           // }
        },
        onButtonReporte:function(){
            var rec=this.sm.getSelected();
            Ext.Ajax.request({
                url:'../../sis_ventas_facturacion/control/Entrega/reporteEntregaBs',
                params:{'id_entrega_brinks':rec.data.id_entrega_brinks},
                success: this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        }

	}
)
</script>
		
		