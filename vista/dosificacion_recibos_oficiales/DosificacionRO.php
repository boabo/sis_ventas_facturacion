<?php
/**
*@package pXP
*@file gen-DosificacionRO.php
*@author  (Ismael Valdivia)
*@date 25/08/2020 09:16:00
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>

Phx.vista.DosificacionRO=Ext.extend(Phx.gridInterfaz,{
	constructor:function(config){
		this.maestro=config.maestro;
		Phx.vista.DosificacionRO.superclass.constructor.call(this,config);
		this.init();
    this.iniciarEventos();
		this.load({params:{start:0, limit:this.tam_pag}});
        // this.addButton('facturas_emitida',{
        //     grupo: [0,1],
        //     text: 'Facturas Emitida',
        //     iconCls: 'bfolder',
        //     disabled: false,
        //     handler: this.onButtonFacturasEmitida,
        //     tooltip: '<b>Facturas Emitida</b>',
        //     scope:this
        // });
	},
    Atributos:[
        {
            //configuracion del componente
            config:{
                labelSeparator:'',
                inputType:'hidden',
                name: 'id_dosificacion_ro'
            },
            type:'Field',
            form:true
        },
        {
            config:{
                name: 'dias_restante',
                fieldLabel: 'Dias Restante',
                gwidth: 100,
                renderer:function (value,p,record){

                    var dias = record.data.dias_restante;
                    if (dias > 10 ) {
                        return String.format('<div ext:qtip="Optimo"><b><font color="green">{0}</font></b><br></div>', value);
                    }
                    else if( dias >= 1 &&  dias <= 9 ){
                        return String.format('<div ext:qtip="Critico"><b><font color="orange">{0}</font></b><br></div>', value);
                    }
                    else if(dias == 0 ){
                        return String.format('<div ext:qtip="malo"><b><font color="red">{0}</font></b><br></div>', value);
                    }

                }
            },
            type:'NumberField',
            filters:{pfiltro:'dias_restante',type:'numeric'},
            grid:true,
            form:false
        },
        //FIN ES MANUAL
        {
            config:{
                name: 'nro_siguiente',
                fieldLabel: 'Nro Siguiente',
                gwidth: 100,
								allowBlank: true,
            },
            type:'NumberField',
            filters:{pfiltro:'dos.nro_siguiente',type:'numeric'},
            grid:true,
            form:true
        },
        {
            config: {
                name: 'id_sucursal',
                fieldLabel: 'Sucursal',
                allowBlank: true,
                emptyText: 'Elija una Suc...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_ventas_facturacion/control/Sucursal/listarSucursal',
                    id: 'id_sucursal',
                    root: 'datos',
                    sortInfo: {
                        field: 'nombre',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_sucursal', 'nombre', 'codigo'],
                    remoteSort: true,
                    baseParams: {tipo_usuario: 'cajero',par_filtro: 'suc.nombre#suc.codigo'}
                }),
                valueField: 'id_sucursal',
                gdisplayField : 'nombre_sucursal',
                displayField: 'nombre',
                hiddenName: 'id_sucursal',
                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                forceSelection: true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 15,
                queryDelay: 1000,
                minChars: 2,
                width:250,
                gwidth: 230,
                resizable:true,
                renderer : function(value, p, record) {
                    return String.format('{0}', record.data['nom_sucursal']);
                }
            },
            type: 'ComboBox',
            id_grupo: 0,
            filters: {pfiltro:'su.nombre', type:'string'},
            form: true,
            grid:true,
            bottom_filter:true
        },
        {
            config:{
                name: 'estacion',
                fieldLabel: 'Estacion',
                allowBlank: false,
                anchor: '100%',
                gwidth: 90,
                maxLength:150
            },
            type:'TextField',
            filters:{pfiltro:'lu.codigo',type:'string'},
            id_grupo:0,
            grid:true,
            form:false
        },

        {
            config:{
                name: 'tipo',
                fieldLabel: 'Tipo Documento',
                allowBlank: false,
                disabled: true,
                anchor: '100%',
                gwidth: 90,
                maxLength:150,
                value:'Recibo'
            },
            type:'TextField',
            id_grupo:0,
            valorInicial:'Recibo',
            grid:true,
            form:true
        },
        {
            config:{
                name: 'fecha_dosificacion',
                fieldLabel: 'Fecha de Dosificacion',
                allowBlank: false,
                anchor: '80%',
                gwidth: 120,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
            type:'DateField',
            filters:{pfiltro:'dos.fecha_dosificacion',type:'date'},
            id_grupo:0,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'fecha_inicio_emi',
                fieldLabel: 'Fecha inicio de Emis.',
                allowBlank: false,
                anchor: '80%',
                gwidth: 125,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
            type:'DateField',
            filters:{pfiltro:'dos.fecha_inicio_emi',type:'date'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'fecha_limite',
                fieldLabel: 'Fecha Limite Emision',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
            type:'DateField',
            filters:{pfiltro:'dos.fecha_limite',type:'date'},
            id_grupo:0,
            grid:true,
            form:true
        },
        // {
        //     config:{
        //         name:'tipo_generacion',
        //         fieldLabel:'Tipo de Generacion',
        //         allowBlank:false,
        //         emptyText:'Tip...',
        //         triggerAction: 'all',
        //         lazyRender:true,
        //         mode: 'local',
        //         store:['manual','computarizada']
				//
        //     },
        //     type:'ComboBox',
        //     id_grupo:0,
        //     filters:{
        //         type: 'list',
        //         options: ['manual','computarizada'],
        //     },
        //     grid:true,
        //     form:true
        // },
        //INI ES MANUAL
        {
            config:{
                name: 'inicial',
                fieldLabel: 'No Inicial',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                maxLength:150,
                allowDecimals:false,
                allowNegative:false
            },
            type:'TextField',
            filters:{pfiltro:'dos.inicial',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:true
        },

        {
            config:{
                name: 'final',
                fieldLabel: 'No Final',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                maxLength:150,
                allowDecimals:false,
                allowNegative:false
            },
            type:'NumberField',
            filters:{pfiltro:'dos.final',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:true
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
            filters:{pfiltro:'dos.estado_reg',type:'string'},
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
            filters:{pfiltro:'dos.id_usuario_ai',type:'numeric'},
            id_grupo:1,
            grid:false,
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
            filters:{pfiltro:'dos.fecha_reg',type:'date'},
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
            filters:{pfiltro:'dos.usuario_ai',type:'string'},
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
                name: 'fecha_mod',
                fieldLabel: 'Fecha Modif.',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
            },
            type:'DateField',
            filters:{pfiltro:'dos.fecha_mod',type:'date'},
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
	title:'Dosificación',
	ActSave:'../../sis_ventas_facturacion/control/Dosificacion/insertarDosificacionRO',
	ActDel:'../../sis_ventas_facturacion/control/Dosificacion/eliminarDosificacionRO',
	ActList:'../../sis_ventas_facturacion/control/Dosificacion/listarDosificacionRO',
	id_store:'id_dosificacion_ro',
	fields: [
		{name:'id_dosificacion_ro', type: 'numeric'},
		{name:'id_sucursal', type: 'numeric'},
    {name:'final', type: 'numeric'},
    {name:'tipo', type: 'string'},
    {name:'fecha_dosificacion', type: 'date',dateFormat:'Y-m-d'},
    {name:'nro_siguiente', type: 'numeric'},
    {name:'fecha_inicio_emi', type: 'date',dateFormat:'Y-m-d'},
    {name:'fecha_limite', type: 'date',dateFormat:'Y-m-d'},
    {name:'tipo_generacion', type: 'string'},
		{name:'inicial', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
    {name:'nom_sucursal', type: 'string'},
    {name:'nombre_sucursal', type: 'string'},
    {name:'estacion', type: 'string'},
    {name:'dias_restante', type: 'numeric'},

	],
	sortInfo:{
		field: 'dias_restante',
		direction: 'DESC'
	},
	bdel:true,
	bsave:true,
    Grupos: [
        {
            layout: 'column',
            border: false,
            // defaults are applied to all child items unless otherwise specified by child item
            defaults: {
                // columnWidth: '.5',
                border: false
            },
            items: [{

                bodyStyle: 'padding-right:5px;',
                items: [{
                    xtype: 'fieldset',
                    title: 'Datos Básicos',
                    autoHeight: true,
                    defaults: {
                        anchor: '23' // leave room for error icon
                    },
                    items: [],
                    id_grupo:0
                }]
            }
                , {
                    bodyStyle: 'padding-left:30px;',
                    items: [{
                        xtype: 'fieldset',
                        title: 'Datos Adicionales',
                        autoHeight: true,
                        defaults: {
                            anchor: '23' // leave room for error icon
                        },
                        items: [],
                        id_grupo:1
                    }]
                }]
        }
    ],

    fheight:'60%',
    fwidth:'88%',
    onButtonNew:function() {
        Phx.vista.DosificacionRO.superclass.onButtonNew.call(this);
				this.ocultarComponente(this.Cmp.nro_siguiente);
    },
    iniciarEventos :  function () {
        // this.Cmp.tipo_generacion.on('select',function (c,r,v) {
        //     if (this.Cmp.tipo_generacion.getValue() == 'manual') {
        //         this.mostrarComponente(this.Cmp.inicial);
        //         this.Cmp.inicial.allowBlank = false;
				//
        //         this.mostrarComponente(this.Cmp.final);
        //         this.Cmp.final.allowBlank = false;
				//
				//
        //         this.ocultarComponente(this.Cmp.fecha_inicio_emi);
        //         this.Cmp.fecha_inicio_emi.allowBlank = true;
        //         this.Cmp.fecha_inicio_emi.reset();


          //  } else {
                this.ocultarComponente(this.Cmp.inicial);
                this.Cmp.inicial.allowBlank = true;
                this.Cmp.inicial.reset();

                this.ocultarComponente(this.Cmp.final);
                this.Cmp.final.allowBlank = true;
                this.Cmp.final.reset();

                this.mostrarComponente(this.Cmp.fecha_inicio_emi);
                this.Cmp.fecha_inicio_emi.allowBlank = false;

        //     }
        // },this);

    },
    onButtonEdit:function() {
        Phx.vista.DosificacionRO.superclass.onButtonEdit.call(this);
				this.mostrarComponente(this.Cmp.nro_siguiente);
        //this.Cmp.tipo_generacion.fireEvent('select');
    },
    onSubmit : function(o) {
        Phx.vista.DosificacionRO.superclass.onSubmit.call(this,o);
    },
    successSave:function(resp){
        var datos_respuesta = JSON.parse(resp.responseText);
        if (datos_respuesta.ROOT.datos.prueba) {
            Ext.Msg.alert('Atencion',datos_respuesta.ROOT.datos.prueba).getDialog().setSize(350,300);

        }
        Phx.vista.DosificacionRO.superclass.successSave.call(this,resp);
    },
    // onButtonFacturasEmitida : function() {
    //     var rec={maestro:this.sm.getSelected().data};
    //     console.log('maestro',rec);
    //     Phx.CP.loadWindows('../../../sis_ventas_facturacion/vista/dosificacion/FacturasEmitidas.php',
    //         'Facturas Emitidas',
    //         {
    //             width:800,
    //             height:'80%'
    //         },
    //         rec,
    //         this.idContenedor,
    //         'FacturasEmitidas');
    // }

	}
)
</script>
