<?php
/**
*@package pXP
*@file gen-ModFormaPago.php
*@author  (miguel.mamani)
*@date 13-12-2017 21:37:47
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ModFormaPago=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.ModFormaPago.superclass.constructor.call(this,config);
		this.init();
        this.campo_fecha = new Ext.form.DateField({
            name: 'fecha_reg',
            grupo: this.grupoDateFin,
            fieldLabel: 'Fecha',
            allowBlank: false,
            anchor: '60%',
            gwidth: 100,
            format: 'd/m/Y',
            hidden : false
        });

        this.tbar.addField(this.campo_fecha);
        var  b = new  Date();
          var fecha =   b.dateFormat('d/m/Y');
             console.log(fecha);
        var fecha_array = fecha.split('/');
        this.campo_fecha.setValue(new Date(fecha_array[2],parseInt(fecha_array[1]) - 1,fecha_array[0]));
        this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('Ymd');

        this.campo_fecha.on('select',function(value){
            this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('Ymd');
            this.load();
        },this);
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_mod_forma_pago'
			},
			type:'Field',
			form:true 
		},
        {
            config:{
                name: 'billete',
                fieldLabel: 'Billete',
                allowBlank: false,
                anchor: '80%',
                gwidth: 120,
                renderer : function(value, p, record) {
                        return String.format('<div title="Nro. Boleto"><b><font color="red">{0}</font></b></div>', value);
                }
            },
            type:'NumberField',
            filters:{pfiltro:'cfm.billete',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:true,
            bottom_filter:true
        },
        {
            config:{
                name: 'forma',
                fieldLabel: 'Forma de Pago',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                renderer : function(value, p, record) {
                    return String.format('<div title="Nro. Boleto"><b><font color="#006400">{0}</font></b></div>', value);
                }
            },
            type:'TextField',
            filters:{pfiltro:'cfm.forma',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'importe',
                fieldLabel: 'Importe',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                renderer: function(value, p, record) {

                    return '<tpl for="."><div class="x-combo-list-item"><p><b>Importe: </b> <font color="black">' + record.data['importe'] + '</font><p><b>Comision:</b> <font color="#20b2aa"><b>' + record.data['comision'] +'</b></font></p></div></tpl>';

                }
            },
            type:'NumberField',
            filters:{pfiltro:'cfm.importe',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'comision',
                fieldLabel: 'comision',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:1179650
            },
            type:'NumberField',
            filters:{pfiltro:'cfm.comision',type:'numeric'},
            id_grupo:1,
            grid:false
        },
		{
			config:{
				name: 'fecha',
				fieldLabel: 'Fecha Emision',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'cfm.fecha',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
        {
            config:{
                name: 'agt',
                fieldLabel: 'Punto de Venta / Codigo',
                allowBlank: true,
                anchor: '80%',
                gwidth: 220,
                renderer: function(value, p, record) {

                    return '<tpl for="."><div class="x-combo-list-item"><p><b>Punto Venta: </b> <font color="#191970">' + record.data['punto_venta'] + '</font><p><b>Codigo:</b> <font color="#191970"><b>' + record.data['agt'] +'</b></font></p></div></tpl>';

                }
            },
            type:'NumberField',
            filters:{pfiltro:'cfm.agt',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:true,
            bottom_filter:true
        },
        {
            config:{
                name: 'pais',
                fieldLabel: 'Pais / estacion',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                renderer: function(value, p, record) {

                    return '<tpl for="."><div class="x-combo-list-item"><p><b>Pais: </b> <font color="#8b0000">' + record.data['pais'] + '</font><p><b>Estacion:</b> <font color="#b8860b"><b>' + record.data['estacion'] +'</b></font></p></div></tpl>';

                }
            },
            type:'TextField',
            filters:{pfiltro:'cfm.pais',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'moneda',
                fieldLabel: 'Moneda',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:3
            },
            type:'TextField',
            filters:{pfiltro:'cfm.moneda',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
		{
			config:{
				name: 'estacion',
				fieldLabel: 'estacion',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:3
			},
				type:'TextField',
				filters:{pfiltro:'cfm.estacion',type:'string'},
				id_grupo:1,
				grid:false,
                bottom_filter:true
		},
        {
            config:{
                name: 'tarjeta',
                fieldLabel: 'Tarjeta',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:6
            },
            type:'TextField',
            filters:{pfiltro:'cfm.tarjeta',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },

        {
            config:{
                name: 'numero',
                fieldLabel: 'Numero',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:20
            },
            type:'TextField',
            filters:{pfiltro:'cfm.numero',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'autoriza',
                fieldLabel: 'Autoriza',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:6
            },
            type:'TextField',
            filters:{pfiltro:'cfm.autoriza',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'ctacte',
                fieldLabel: 'Cuenta Corriente',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:15
            },
            type:'TextField',
            filters:{pfiltro:'cfm.ctacte',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'pagomco',
                fieldLabel: 'pagomco',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:1179650
            },
            type:'NumberField',
            filters:{pfiltro:'cfm.pagomco',type:'numeric'},
            id_grupo:1,
            grid:true,
            bottom_filter:true
        },
		{
			config:{
				name: 'observa',
				fieldLabel: 'observa',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:80
			},
				type:'TextField',
				filters:{pfiltro:'cfm.observa',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
        {
            config:{
                name: 'usuario',
                fieldLabel: 'Usuario',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:15
            },
            type:'TextField',
            filters:{pfiltro:'cfm.usuario',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'fecha_mod',
                fieldLabel: 'Fecha Modif.',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
            type:'DateField',
            filters:{pfiltro:'cfm.fecha_mod',type:'date'},
            id_grupo:1,
            grid:true,
            form:false
        },
		{
			config:{
				name: 'hora_mod',
				fieldLabel: 'Hora Modif',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:8
			},
				type:'TextField',
				filters:{pfiltro:'cfm.hora_mod',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		}

	],
	tam_pag:50,	
	title:'Consulta Forma de Pago Modificado',
	ActSave:'../../sis_ventas_facturacion/control/ModFormaPago/insertarModFormaPago',
	ActDel:'../../sis_ventas_facturacion/control/ModFormaPago/eliminarModFormaPago',
	ActList:'../../sis_ventas_facturacion/control/ModFormaPago/listarModFormaPago',
	id_store:'id_mod_forma_pago',
	fields: [
		{name:'id_mod_forma_pago', type: 'numeric'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'ctacte', type: 'string'},
		{name:'forma', type: 'string'},
		{name:'agt', type: 'numeric'},
		{name:'estacion', type: 'string'},
		{name:'pais', type: 'string'},
		{name:'comision', type: 'numeric'},
		{name:'usuario', type: 'string'},
		{name:'importe', type: 'numeric'},
		{name:'autoriza', type: 'string'},
		{name:'observa', type: 'string'},
		{name:'hora_mod', type: 'string'},
		{name:'tarjeta', type: 'string'},
		{name:'billete', type: 'numeric'},
		{name:'numero', type: 'string'},
		{name:'moneda', type: 'string'},
		{name:'pagomco', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d'},
        {name:'punto_venta', type: 'string'}
		
	],
	sortInfo:{
		field: 'id_mod_forma_pago',
		direction: 'ASC'
	},
	bdel:false,
	bsave:false,
    bnew:false,
    bedit:false

	}
)
</script>
		
		