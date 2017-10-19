<?php
/**
*@package pXP
*@file gen-ConsultaBoletos.php
*@author  (admin)
*@date 12-10-2017 21:15:26
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ConsultaBoletos=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
        this.initButtons=[this.cmbGestion];
       /* this.tbarItems = ['-',
            ' Filtrar:',
            this.datoFiltro,'-'
        ];*/
    	//llama al constructor de la clase padre
		Phx.vista.ConsultaBoletos.superclass.constructor.call(this,config);
		this.init();
        this.load({params:{start:0, limit:this.tam_pag}});
        Ext.Ajax.request({
            url:'../../sis_parametros/control/Gestion/obtenerGestionByFecha',
            params:{fecha:new Date()},
            success:function (resp) {
                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                console.log('datos',reg);
                if(!reg.ROOT.error){
                    this.cmbGestion.setValue(reg.ROOT.datos.id_gestion);
                    this.cmbGestion.setRawValue(reg.ROOT.datos.anho);
                    this.store.baseParams.gestion = reg.ROOT.datos.anho;
                    this.load({params:{start:0, limit:this.tam_pag}});
                }else{

                    alert('Ocurrio un error al obtener la Gestión')
                }
            },
            failure: this.conexionFailure,
            timeout:this.timeout,
            scope:this
        });
        this.cmbGestion.on('select',this.capturarEventos, this);

	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_boleto'
			},
			type:'Field',
			form:true 
		},
        {
            config:{
                name: 'fecha_emision',
                fieldLabel: 'Fecha Emision',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
            type:'DateField',
            filters:{pfiltro:'cbs.fecha_emision',type:'date'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'voided',
                fieldLabel: 'Anulado',
                anchor: '60%',
                gwidth: 60,
                readOnly:true,
                renderer : function(value, p, record) {
                    if (record.data['voided'] != 'si') {
                        return String.format('<div title="Anulado"><b><font color="green">{0}</font></b></div>', value);

                    } else {
                        return String.format('<div title="Anulado"><b><font color="red">{0}</font></b></div>', value);
                    }
                }
            },
            type:'TextField',
            filters:{pfiltro:'cbs.voided',type:'string'},
            id_grupo:0,
            grid:true,
            form:false
        },
        {
            config:{
                name: 'localizador',
                fieldLabel: 'Pnr',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:13
            },
            type:'TextField',
            filters:{pfiltro:'cbs.localizador',type:'string'},
            id_grupo:1,
            grid:true,
            form:false
            //bottom_filter: true
        },
        {
            config:{
                name: 'nro_boleto',
                fieldLabel: 'Billete: 930-',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                maxLength:10,
                minLength:10,
                enableKeyEvents:true,
                renderer : function(value, p, record) {
                    if (record.data['mensaje_error'] != '') {
                        return String.format('<div title="Error"><b><font color="red">{0}</font></b></div>', value);

                    } else {
                        return String.format('{0}', value);
                    }

                }
            },
            type:'TextField',
            filters:{pfiltro:'cbs.nro_boleto',type:'string'},
            id_grupo:1,
            grid:true,
            form:false,
            bottom_filter: true
        },
        {
            config:{
                name: 'pasajero',
                fieldLabel: 'Pasajero',
                allowBlank: true,
                anchor: '80%',
                gwidth: 200,
                maxLength:100
            },
            type:'TextField',
            filters:{pfiltro:'cbs.pasajero',type:'string'},
            id_grupo:1,
            grid:true,
            form:false,
            bottom_filter: true
        },
        {
            config:{
                name: 'punto_venta',
                fieldLabel: 'Punto Venta',
                allowBlank: true,
                anchor: '80%',
                gwidth: 200,
                maxLength:100
            },
            type:'TextField',
            filters:{pfiltro:'cbs.punto_venta',type:'string'},
            id_grupo:1,
            grid:true,
            form:false,
            bottom_filter: true
        },
        {
            config:{
                name: 'moneda',
                fieldLabel: 'Moneda',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:5
            },
            type:'TextField',
            filters:{pfiltro:'cbs.moneda',type:'string'},
            id_grupo:1,
            grid:true,
            form:false
        },
        {
            config:{
                name: 'total',
                fieldLabel: 'Total',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:1179650
            },
            type:'NumberField',
            filters:{pfiltro:'cbs.total',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:false
        },
        {
            config:{
                name: 'neto',
                fieldLabel: 'Neto',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:1179650
            },
            type:'NumberField',
            filters:{pfiltro:'cbs.neto',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'agente_venta',
                fieldLabel: 'Agente Venta',
                allowBlank: true,
                anchor: '80%',
                gwidth: 200,
                maxLength:-5
            },
            type:'TextField',
            filters:{pfiltro:'cbs.agente_venta',type:'string'},
            id_grupo:1,
            grid:true,
            form:false
        },
        {
            config:{
                name: 'codigo_agente',
                fieldLabel: 'Codigo Agente',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:7
            },
            type:'TextField',
            filters:{pfiltro:'cbs.codigo_agente',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'forma_pago_amadeus',
                fieldLabel: 'Pago Amadeus',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:3
            },
            type:'TextField',
            filters:{pfiltro:'cbs.forma_pago_amadeus',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        }

	],
	tam_pag:50,	
	title:'Consulta Boletos',
	ActSave:'../../sis_ventas_facturacion/control/ConsultaBoletos/insertarConsultaBoletos',
	ActDel:'../../sis_ventas_facturacion/control/ConsultaBoletos/eliminarConsultaBoletos',
	ActList:'../../sis_ventas_facturacion/control/ConsultaBoletos/listarConsultaBoletos',
	id_store:'id_boleto',
	fields: [
		{name:'id_boleto', type: 'numeric'},
		{name:'liquido', type: 'numeric'},
        {name:'codigo_agente', type: 'string'},
		{name:'punto_venta', type: 'string'},
		{name:'nro_boleto', type: 'string'},
		{name:'voided', type: 'string'},
		{name:'pasajero', type: 'string'},
		{name:'moneda', type: 'string'},
		{name:'fecha_emision', type: 'date',dateFormat:'Y-m-d'},
		{name:'agente_venta', type: 'string'},
		{name:'localizador', type: 'string'},
		{name:'total', type: 'numeric'},
		{name:'forma_pago_amadeus', type: 'string'},
		{name:'id_moneda_boleto', type: 'numeric'},
		{name:'neto', type: 'numeric'}
		
	],
	sortInfo:{
		field: 'id_boleto',
		direction: 'ASC'
	},
	bdel:false,
	bsave:false,
    bnew:false,
    bedit:false,
    tabsouth:[{
        url:'../../../sis_ventas_facturacion/vista/consulta_boletos/ConsultaBoletoFormaPago.php',
        title:'Formas de Pago',
        height:'40%',
        cls:'ConsultaBoletoFormaPago'
    }],
    /*datoFiltro:new Ext.form.Field({
        allowBlank:true,
        enableKeyEvents : true,
        width: 150}),*/

    cmbGestion: new Ext.form.ComboBox({
        fieldLabel: 'Gestion',
        allowBlank: false,
        emptyText:'Gestion...',
        blankText: 'Año',
        store:new Ext.data.JsonStore(
            {
                url: '../../sis_parametros/control/Gestion/listarGestion',
                id: 'id_gestion',
                root: 'datos',
                sortInfo:{
                    field: 'gestion',
                    direction: 'DESC'
                },
                totalProperty: 'total',
                fields: ['id_gestion','gestion'],
                // turn on remote sorting
                remoteSort: true,
                baseParams:{par_filtro:'gestion'}
            }),
        valueField: 'gestion',
        triggerAction: 'all',
        displayField: 'gestion',
        hiddenName: 'id_gestion',
        mode:'remote',
        pageSize:50,
        queryDelay:500,
        listWidth:'280',
        width:80
    }),
    capturarEventos: function () {
        if(this.validarFiltros()){
            this.capturaFiltros();
        }
    },

    capturaFiltros:function(combo, record, index){
        this.desbloquearOrdenamientoGrid();
        this.store.baseParams.gestion=this.cmbGestion.getValue();
        this.load({params:{start:0, limit:this.tam_pag}});
    },

    validarFiltros:function(){
        if(this.cmbGestion.isValid()){
            return true;
        }
        else{
            return false;
        }

    },

    onButtonAct:function(){
        if(!this.validarFiltros()){
            Ext.Msg.alert('ATENCION!!!','Especifique los filtros antes')
        }
        else{
            this.store.baseParams.gestion=this.cmbGestion.getValue();
            Phx.vista.ConsultaBoletos.superclass.onButtonAct.call(this);
        }
    }
	}
)
</script>
		
		