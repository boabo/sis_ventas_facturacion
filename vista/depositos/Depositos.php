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
    	//llama al constructor de la clase padre
		Phx.vista.Depositos.superclass.constructor.call(this,config);
        this.addButton('reporte',{grupo:[0,1],text:'Reporte',iconCls: 'bpdf',disabled:true,handler:this.generarReporte,tooltip: '<b>Genera reporte de la caja</b>'});
        this.addButton('rventas',{grupo:[0,1],text:'Reporte Detalle ventas',iconCls: 'bpdf',disabled:true,handler:this.generarReporteVentas,tooltip: '<b>Genera reporte de la caja</b>'});

        this.getBoton('reporte').enable();
        this.getBoton('rventas').enable();
        this.init();
		this.load({params:{start:0, limit:this.tam_pag}})
	},
			
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
            //configuracion del componente
            config:{
                labelSeparator:'',
                inputType:'hidden',
                name: 'id_sucursal'
            },
            type:'Field',
            form:true
        },
        {
            //configuracion del componente
            config:{
                labelSeparator:'',
                inputType:'hidden',
                name: 'id_deposito'
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
                gwidth: 200,
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
            grid:true,
            form:true,
            bottom_filter:true
        },
        {
            config:{
                name: 'nombre_punto_venta',
                fieldLabel: 'Nombre Punto Venta',
                allowBlank: true,
                anchor: '80%',
                gwidth: 250,
                maxLength:100
            },
            type:'TextField',
            filters:{pfiltro:'cdo.nombre_punto_venta',type:'string'},
            id_grupo:1,
            grid:true,
            form:true,
            bottom_filter:true
        },
        {
            config:{
                name: 'codigo_lugar',
                fieldLabel: 'Estación',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:25
            },
            type:'TextField',
            filters:{pfiltro:'cdo.codigo_lugar',type:'string'},
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
				fieldLabel: 'Importe (Bs)',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650,
                renderer:function (value,p,record) {
                    return String.format('<div ext:qtip="Optimo"><b><font color="blue">{0}</font></b><br></div>', value);
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
				fieldLabel: 'Importe ($us)',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650,
                renderer:function (value,p,record) {
                    return String.format('<div ext:qtip="Optimo"><b><font color="red">{0}</font></b><br></div>', value);
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
            config:{
                name: 'deposito_bs',
                fieldLabel: 'Total Deposito (Bs)',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:1179650,
                renderer:function (value,p,record) {
                    return String.format('<div ext:qtip="Optimo"><b><font color="blue">{0}</font></b><br></div>', value);
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
                name: 'deposito_$us',
                fieldLabel: 'Total Deposito ($us)',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:1179650,
                renderer:function (value,p,record) {
                    return String.format('<div ext:qtip="Optimo"><b><font color="red">{0}</font></b><br></div>', value);
                }
            },
            type:'NumberField',
            filters:{pfiltro:'cdo.arqueo_moneda_extranjera',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:true
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
        {name:'id_sucursal', type: 'numeric'},
        {name:'id_entrega_brinks', type: 'numeric'},
        {name:'id_usuario_cajero', type: 'numeric'},
        {name:'id_deposito', type: 'numeric'},
        {name:'cajero', type: 'string'},
        {name:'codigo', type: 'string'},
        {name:'nro_deposito', type: 'string'},
        {name:'nombre_punto_venta', type: 'string'},
        {name:'nombre_sucursal', type: 'string'},
        {name:'codigo_lugar', type: 'string'},
        {name:'fecha_venta', type: 'date',dateFormat:'Y-m-d'},
        {name:'fecha_recojo', type: 'date',dateFormat:'Y-m-d'},
		{name:'fecha_hora_cierre', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
        {name:'arqueo_moneda_local', type: 'numeric'},
        {name:'arqueo_moneda_extranjera', type: 'numeric'},
        {name:'deposito_bs', type: 'numeric'},
        {name:'deposito_$us', type: 'numeric'}

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
	}
)
</script>

