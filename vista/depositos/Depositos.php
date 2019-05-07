<?php
/**
*@package pXP
*@file gen-Depositos.php
*@author  (miguel.mamani)
*@date 11-09-2017 15:32:32
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.Depositos=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
        this.tbarItems = ['-', this.cmbPuntoVenta,'-'];
    	//llama al constructor de la clase padre
		Phx.vista.Depositos.superclass.constructor.call(this,config);
        this.addButton('reporte',{grupo:[0,1],text:'Declaración de Ventas',iconCls: 'bpdf',disabled:true,handler:this.generarReporte,tooltip: '<b>Reporte Declaración de Ventas Diarias/b>'});
        this.addButton('rventas',{grupo:[0,1],text:'Detalle de Ventas',iconCls: 'bpdf',disabled:true,handler:this.generarReporteVentas,tooltip: '<b>Reporte Detalle de Ventas</b>'});

        this.getBoton('reporte').enable();
        this.getBoton('rventas').enable();
        this.cmbPuntoVenta.on('select', function( combo, record, index){
            this.capturaFiltros();
        },this);
        this.init();
        this.finCons = true;
        this.store.baseParams.pes_estado = 'pendiente';
        this.load({params:{start:0, limit:this.tam_pag}})
	},
    gruposBarraTareas:[{name:'pendiente',title:'<H1 align="center"><i class="fa fa-folder-open"></i> Pendientes</h1>',grupo:0,height:0},
                          {name:'registrado',title:'<H1 align="center"><i class="fa fa-folder"></i> Registrados</h1>',grupo:1,height:0}],
    actualizarSegunTab: function(name, indice){
        if(this.finCons) {
            this.store.baseParams.pes_estado = name;
            this.load({params:{start:0, limit:this.tam_pag}});
        }
    },
    bactGroups:  [0,1],
    bexcelGroups: [0,1],

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_apertura_cierre_caja'
			},
			type:'Field',
			form:true
		},
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
                name: 'cajero',
                fieldLabel: 'Cajero',
                allowBlank: true,
                anchor: '80%',
                gwidth: 190,
                maxLength:-5
            },
            type:'TextField',
            filters:{pfiltro:'cdo.cajero',type:'string'},
            id_grupo:1,
            grid:true,
            form:true,
            bottom_filter:true
        },
        {
            config:{
                name: 'codigo',
                fieldLabel: 'Codigo',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:20
            },
            type:'TextField',
            filters:{pfiltro:'cdo.codigo',type:'string'},
            id_grupo:1,
            grid:false,
            form:false
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
            filters:{pfiltro:'cdo.nombre',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'fecha_venta',
                fieldLabel: 'Fecha Venta',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
            type:'DateField',
            filters:{pfiltro:'cdo.fecha_venta',type:'date'},
            id_grupo:1,
            grid:true,
            form:true
        },
		{
			config:{
				name: 'arqueo_moneda_local',
				fieldLabel: 'Importe M/L.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
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
				form:true
		},
        {
			config:{
				name: 'arqueo_moneda_extranjera',
				fieldLabel: 'Importe M/E.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
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
				form:true
		},
        {
            config:{
                name: 'fecha_recojo',
                fieldLabel: 'Fecha Recojo',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
            type:'DateField',
            filters:{pfiltro:' cdo.fecha_recojo',type:'date'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config: {
                name: 'deposito_bs',
                fieldLabel: 'Total Deposito M/L.',
                currencyChar: ' ',
                allowBlank: true,
                width: 100,
                gwidth: 120,
                disabled: true,
                maxLength: 1245186,
               renderer:function (value,p,record) {
                   /*var datos2;
                   if (record.data['diferencia_bs'] == record.data['arqueo_moneda_local'] ){
                       datos2= '<p><b>Diferencia: </b><font color="black"><b>'+0+'</b></font></p>';
                   }else {
                       datos2= '<p><b>Diferencia: </b><font color="red"><b>'+record.data['diferencia_bs']+'</b></font></p>';
                   }*/
                   var dato =  value.replace('.', ",")
                                    .replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");
                   return '<div ext:qtip="Optimo"><p><font color="#0000cd"><b>'+dato+'</b></font></p></div>';

               }
            },
            type: 'MoneyField',
            filters:{pfiltro:'cdo.deposito_bs',type:'numeric'},
            id_grupo: 1,
            grid: true,
            form: false
        },
        {
            config: {
                name: 'deposito_usd',
                fieldLabel: 'Total Deposito M/E.',
                currencyChar: ' ',
                allowBlank: true,
                width: 100,
                gwidth: 120,
                disabled: true,
                maxLength: 1245186,
                renderer:function (value,p,record) {
                    var dato =     value.replace('.', ",")
                                        .replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");
                    return '<div ext:qtip="Optimo"><p> <font color="#dc143c"><b>'+dato+'</b></font></p></div>';
                }
            },
            type: 'MoneyField',
            filters:{pfiltro:'cdo.deposito_usd',type:'numeric'},
            id_grupo: 1,
            grid: true,
            form: false
        },

        {
            config: {
                name: 'diferencia_bs',
                fieldLabel: 'Diferencia M/L.',
                currencyChar: ' ',
                allowBlank: true,
                width: 100,
                gwidth: 120,
                disabled: true,
                maxLength: 1245186,
                renderer:function (value,p,record) {
                    var dato =     record.data['diferencia_bs'].replace('.', ",")
                        .replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");

                    if (record.data['diferencia_bs'] == 0 ){
                        return '<div ext:qtip="Optimo"><p> <font color="black"><b>'+dato+'</b></font></p></div>';
                    }else {
                        return '<div ext:qtip="Optimo"><p> <font color="red"><b>'+dato+'</b></font></p></div>';
                    }

                }
            },
            type: 'MoneyField',
            id_grupo: 1,
            grid: true,
            form: false
        },
        {
            config: {
                name: 'diferencia_usd',
                fieldLabel: 'Diferencia M/E.',
                currencyChar: ' ',
                allowBlank: true,
                width: 100,
                gwidth: 120,
                disabled: true,
                maxLength: 1245186,
                renderer:function (value,p,record) {
                    var dato =     record.data['diferencia_usd'].replace('.', ",")
                        .replace(/(\d)(?:(?=\d+(?=[^\d.]))(?=(?:[0-9]{3})+\b)|(?=\d+(?=\.))(?=(?:[0-9]{3})+(?=\.)))/g, "$1.");

                    if (  record.data['diferencia_usd'] == 0 ){
                        return '<div ext:qtip="Optimo"><p> <font color="black"><b>'+dato+'</b></font></p></div>';
                    }else {
                        return '<div ext:qtip="Optimo"><p> <font color="red"><b>'+dato+'</b></font></p></div>';
                    }
                }
            },
            type: 'MoneyField',
            id_grupo: 1,
            grid: true,
            form: false
        }
	],
	tam_pag:50,
	title:'Completar Deposito',
	ActSave:'../../sis_ventas_facturacion/control/Depositos/insertarDepositos',
	ActDel:'../../sis_ventas_facturacion/control/Depositos/eliminarDepositos',
	ActList:'../../sis_ventas_facturacion/control/Depositos/listarDepositos',
	id_store:'id_apertura_cierre_caja',
	fields: [
		{name:'id_apertura_cierre_caja', type: 'numeric'},
        {name:'id_punto_venta', type: 'numeric'},
        {name:'id_entrega_brinks', type: 'numeric'},
        {name:'id_usuario_cajero', type: 'numeric'},
        {name:'cajero', type: 'string'},
        {name:'codigo', type: 'string'},
        {name:'nombre_punto_venta', type: 'string'},
        {name:'codigo_padre', type: 'string'},
        {name:'estacion', type: 'string'},
        {name:'fecha_venta', type: 'date',dateFormat:'Y-m-d'},
        {name:'fecha_recojo', type: 'date',dateFormat:'Y-m-d'},
        {name:'arqueo_moneda_local', type: 'numeric'},
        {name:'arqueo_moneda_extranjera', type: 'numeric'},
        {name:'deposito_bs', type: 'numeric'},
        {name:'deposito_usd', type: 'numeric'},
        {name:'tipo_cambio', type: 'numeric'},
        {name:'diferencia_bs', type: 'numeric'},
        {name:'diferencia_usd', type: 'numeric'}

	],
	sortInfo:{
		field: 'id_apertura_cierre_caja',
		direction: 'ASC'
	},
    tabsouth :[
        {
            url:'../../../sis_ventas_facturacion/vista/depositos/DepositoDetalle.php',
            title:'Deposito',
            height:'40%',
            cls:'DepositoDetalle'
        }
    ],
	bdel:false,
	bsave:false,
    bedit:false,
    bnew:false,

    cmbPuntoVenta: new Ext.form.ComboBox({
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
            baseParams: {tipo_usuario: 'adminEntrega',par_filtro: 'puve.nombre#puve.codigo', tipo_factura: this.tipo_factura}
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
        this.store.baseParams.id_punto_venta = this.cmbPuntoVenta.getValue();
        this.load();
    },
    generarReporte : function () {
        var data=this.sm.getSelected().data;
        Phx.CP.loadingShow();
        Ext.Ajax.request({
            url:'../../sis_ventas_facturacion/control/AperturaCierreCaja/reporteAperturaCierreCaja',
            params:{'id_apertura_cierre_caja' : data.id_apertura_cierre_caja},
            success:this.successExport,
            failure: this.conexionFailure,
            timeout:this.timeout,
            scope:this
        });
    },
    generarReporteVentas : function () {
        var data=this.sm.getSelected().data;
        Phx.CP.loadingShow();
        var d = data.fecha_venta;
        var date = (d.getMonth() + 1) + '/' + d.getDate() + '/' +  d.getFullYear();
        Ext.Ajax.request({
            url:'../../sis_ventas_facturacion/control/ReportesVentas/reporteResumenVentasBoa',
            params:{'id_punto_venta' : data.id_punto_venta,fecha_hasta:date,fecha_desde:date},
            success:this.successExport,
            failure: this.conexionFailure,
            timeout:this.timeout,
            scope:this
        });
    }
	})
</script>
