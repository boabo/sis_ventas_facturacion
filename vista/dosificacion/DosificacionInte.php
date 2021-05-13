<?php
/**
*@package pXP
*@file gen-Dosificacion.php
*@author  (admin)
*@date 26-09-2017 14:16:52
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>

Phx.vista.DosificacionInte=Ext.extend(Phx.gridInterfaz,{
	tabsouth:[
		{
			url:'../../../sis_ventas_facturacion/vista/servicios/ServiciosDosificaciones.php',
			title:'Conceptos Relacionados a la Dosificación',
			width:'100%',
			height:'50%',
			cls:'ServiciosDosificaciones'
		}
	],
	constructor:function(config){
		this.maestro=config.maestro;
		//this.tipoInterfaz = 'InterfazExterna';
    	//llama al constructor de la clase padre
		Phx.vista.DosificacionInte.superclass.constructor.call(this,config);
		this.init();
        this.iniciarEventos();
		this.load({params:{start:0, limit:this.tam_pag}});
        this.addButton('facturas_emitida',{
            grupo: [0,1],
            text: 'Facturas Emitida',
            iconCls: 'bfolder',
            disabled: false,
            handler: this.onButtonFacturasEmitida,
            tooltip: '<b>Facturas Emitida</b>',
            scope:this
        });

				this.bbar.el.dom.style.background='#84BFE7';
				this.tbar.el.dom.style.background='#84BFE7';
	},
    Atributos:[
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
                gwidth: 100
            },
            type:'NumberField',
            filters:{pfiltro:'dos.nro_siguiente',type:'numeric'},
            grid:true,
            form:false
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
                    baseParams: {par_filtro: 'suc.nombre#suc.codigo'}
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
            config : {
                name : 'id_activida_economica',
                fieldLabel : 'Actividad Economica',
                allowBlank : false,
                emptyText : 'Actividad...',
                store : new Ext.data.JsonStore({
                    url : '../../sis_ventas_facturacion/control/ActividadEconomica/listarActividadEconomica',
                    id : 'id_actividad_economica',
                    root : 'datos',
                    sortInfo : {
                        field : 'codigo',
                        direction : 'ASC'
                    },
                    totalProperty : 'total',
                    fields : ['id_actividad_economica', 'nombre', 'codigo', 'descripcion'],
                    remoteSort : true,
                    baseParams : {
                        par_filtro : 'acteco.codigo#acteco.nombre'
                    }
                }),
                valueField : 'id_actividad_economica',
                displayField : 'nombre',
                //gdisplayField : 'desc_actividad_economica',
                hiddenName : 'id_actividad_economica',
                forceSelection : true,
                typeAhead : false,
              //  tpl:'<tpl for="."><div class="x-combo-list-item"><p>{codigo}</p><p>{nombre}</p><p>{descripcion}</p> </div></tpl>',
                tpl: new Ext.XTemplate([
                    '<tpl for=".">',
                    '<div class="x-combo-list-item">',
                    '<div class="awesomecombo-item {checked}">',
                    '<p>({codigo}) {nombre}</p>',
                    '</div>',
                    '</div></tpl>'
                ]),
                triggerAction : 'all',
                lazyRender : true,
                mode : 'remote',
                pageSize : 10,
                queryDelay : 1000,
                gwidth : 500,
								listWidth:'800',
                minChars : 2,
								resizable: true,
                enableMultiSelect:true,
                renderer:function(value, p, record){return String.format('{0}', record.data['desc_actividad_economica']);}
            },
            type : 'ComboBox',
            id_grupo : 0,
            form : true,
            grid:true
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
                anchor: '80%',
                gwidth: 120,
                triggerAction: 'all',
                lazyRender:true,
                mode: 'local',
                store: new Ext.data.ArrayStore({
                    id: 0,
                    fields: [
                        'id',
                        'display'
                    ],
                    data: [['F', 'Factura'], ['N', 'Nota de Credito/Debito']]
                }),
                valueField: 'id',
                displayField: 'display',
                renderer:function (value, p, record){
                    if (value == 'F') {
                        return 'Factura';
                    } else {
                        return 'Nota de Credito/Debito'
                    }
                }
            },
            type:'ComboBox',
            filters:{ type: 'list',
                options: ['F','N']
            },
            id_grupo:0,
            grid:true,
            form:true
        },

        {
            config:{
                name: 'nro_tramite',
                fieldLabel: 'Nro Tramite',
                allowBlank: false,
                anchor: '80%',
                gwidth: 150,
                maxLength:150,
                allowDecimals:false,
                allowNegative:false
            },
            type:'NumberField',
            filters:{pfiltro:'dos.nro_tramite',type:'string'},
            id_grupo:0,
            grid:true,
						bottom_filter:true,
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
                name: 'nroaut',
                fieldLabel: 'No Autorizacion',
                allowBlank: false,
                anchor: '100%',
                gwidth: 120,
                maxLength:150
            },
            type:'TextField',
            filters:{pfiltro:'dos.nroaut',type:'string'},
            id_grupo:0,
            grid:true,
            form:true,
            bottom_filter:true
        },

				/*Aumentando Campos para registrar La dosificacion para facturacion por exportacion*/
				{
						config: {
								name: 'caracteristica',
								fieldLabel: 'Característica',
								allowBlank: true,
								emptyText: 'Característica...',
								gwidth: 400,
								style: {
												background: '#AAEEFF',
												color:'blue',
												fontWeight: 'bold'
								},
								store : new Ext.data.JsonStore({
								 url : '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
								 id : 'id_catalogo',
								 root : 'datos',
								 sortInfo : {
									 field : 'codigo',
									 direction : 'ASC'
								 },
								 totalProperty : 'total',
								 fields: ['codigo','descripcion'],
								 remoteSort : true,
								 baseParams:{
									cod_subsistema:'VEF',
									catalogo_tipo:'carasteristicas_dosificaciones_factura'
								},
							 }),
								valueField: 'descripcion',
								gdisplayField : 'caracteristica',
								displayField: 'descripcion',
								forceSelection: true,
								typeAhead: false,
								triggerAction: 'all',
								lazyRender: true,
								mode: 'remote',
								pageSize: 15,
								width:250,
								queryDelay: 1000,
								minChars: 2,
								resizable:true
						},
						type: 'ComboBox',
						id_grupo: 0,
						form: true,
						grid: true
				},

				{
						config: {
								name: 'titulo',
								fieldLabel: 'Titulo',
								allowBlank: false,
								emptyText: 'Titulo...',
								gwidth: 350,
								style: {
												background: '#AAEEFF',
												color:'blue',
												fontWeight: 'bold'
								},
								store : new Ext.data.JsonStore({
								 url : '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
								 id : 'id_catalogo',
								 root : 'datos',
								 sortInfo : {
									 field : 'codigo',
									 direction : 'ASC'
								 },
								 totalProperty : 'total',
								 fields: ['codigo','descripcion'],
								 remoteSort : true,
								 baseParams:{
									cod_subsistema:'VEF',
									catalogo_tipo:'titulo_dosificacion'
								},
							 }),
								valueField: 'descripcion',
								gdisplayField : 'titulo',
								displayField: 'descripcion',
								forceSelection: true,
								typeAhead: false,
								triggerAction: 'all',
								lazyRender: true,
								mode: 'remote',
								pageSize: 15,
								width:250,
								queryDelay: 1000,
								minChars: 2,
								resizable:true
						},
						type: 'ComboBox',
						id_grupo: 0,
						form: true,
						grid: true
				},

				{
						config: {
								name: 'subtitulo',
								fieldLabel: 'Subtitulo',
								allowBlank: true,
								emptyText: 'Subtitulo...',
								gwidth: 200,
								style: {
												background: '#AAEEFF',
												color:'blue',
												fontWeight: 'bold'
								},
								store : new Ext.data.JsonStore({
								 url : '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
								 id : 'id_catalogo',
								 root : 'datos',
								 sortInfo : {
									 field : 'codigo',
									 direction : 'ASC'
								 },
								 totalProperty : 'total',
								 fields: ['codigo','descripcion'],
								 remoteSort : true,
								 baseParams:{
									cod_subsistema:'VEF',
									catalogo_tipo:'tsubtitulo_dosificacion'
								},
							 }),
								valueField: 'descripcion',
								gdisplayField : 'subtitulo',
								displayField: 'descripcion',
								forceSelection: true,
								typeAhead: false,
								triggerAction: 'all',
								lazyRender: true,
								mode: 'remote',
								pageSize: 15,
								width:250,
								queryDelay: 1000,
								minChars: 2,
								resizable:true
						},
						type: 'ComboBox',
						id_grupo: 0,
						form: true,
						grid: true
				},
				/**********************************************************************************/


        {
            config:{
                name: 'codigo',
                fieldLabel: 'Codigo Actividad Economica ',
                allowBlank: true,
                anchor: '100%',
                gwidth: 120,
                maxLength:150
            },
            type:'TextField',
            filters:{pfiltro:'ac.codigo',type:'string'},
            id_grupo:0,
            grid:true,
            form:false,
            bottom_filter:true
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
        {
            config : {
                name : 'rnd',
                fieldLabel : 'Nro de Resolución',
                anchor : '90%',
                tinit : false,
                allowBlank : false,
                origen : 'CATALOGO',
                gdisplayField : 'rnd',
                gwidth : 300,
                anchor : '100%',
                baseParams : {
                    cod_subsistema : 'VEF',
                    catalogo_tipo : 'nro_resolución'
                },
                renderer:function(value, p, record){return String.format('{0}', record.data['rnd']);}
            },
            type : 'ComboRec',
            id_grupo : 0,
            filters : {
                pfiltro : 'dos.rnd',
                type : 'string'
            },
            grid : true,
            form : true
        },

        {
            config:{
                name:'tipo_generacion',
                fieldLabel:'Tipo de Generacion',
                allowBlank:false,
                emptyText:'Tip...',
                triggerAction: 'all',
                lazyRender:true,
                mode: 'local',
                store:['manual','computarizada']

            },
            type:'ComboBox',
            id_grupo:0,
            filters:{
                type: 'list',
                options: ['manual','computarizada'],
            },
            grid:true,
            form:true
        },

				{
            config : {
                name : 'nombre_sistema',
                fieldLabel : 'Nombre Sistema de Facturación',
                anchor : '90%',
                tinit : false,
                allowBlank : false,
                origen : 'CATALOGO',
                gdisplayField : 'nombre_sistema',


                gwidth : 200,
                anchor : '100%',
                baseParams : {
                    cod_subsistema : 'VEF',
                    catalogo_tipo : 'sistema_facturacion'
                },
                renderer:function(value, p, record){return String.format('{0}', record.data['nombre_sistema']);}
            },
            type : 'ComboRec',
            id_grupo : 0,
            filters : {pfiltro : 'dos.nombre_sistema', type : 'string'},
            grid : true,
            form : true
        },

        //INI ES COMPUTARIZADA
        {
            config:{
                name: 'llave',
                fieldLabel: 'Llave',
                allowBlank: false,
                anchor: '100%',
                gwidth: 120,
                maxLength:150
            },
            type:'TextArea',
            filters:{pfiltro:'dos.llave',type:'string'},
            id_grupo:1,
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
        //FIN ES COMPUTARIZADA
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
                name: 'glosa_impuestos',
                fieldLabel: 'Leyenda Impuestos',
                allowBlank: true,
                anchor: '100%',
                gwidth: 300,
                maxLength:150
            },
            type:'TextArea',
            filters:{pfiltro:'dos.glosa_impuestos',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config : {
                name : 'glosa_empresa',
                fieldLabel : 'Leyenda Consumidor',
                anchor : '90%',
                tinit : false,
                allowBlank : false,
                origen : 'CATALOGO',
                gdisplayField : 'glosa_empresa',
               // valueField : 'descripcion',
                gwidth : 300,
                anchor : '100%',
                baseParams : {
                    cod_subsistema : 'VEF',
                    catalogo_tipo : 'leyenda_ley_453'
                },
                renderer:function(value, p, record){return String.format('{0}', record.data['glosa_empresa']);}
            },
            type : 'ComboRec',
            id_grupo : 1,
            filters : {
                pfiltro : 'dos.glosa_empresa',
                type : 'string'
            },
            grid : true,
            form : true
        },
        {
            config:{
                name: 'leyenda',
                fieldLabel: 'Leyenda Boa',
                allowBlank: true,
                anchor: '100%',
                gwidth: 180,
                maxLength:150
            },
            type:'TextArea',
            filters:{pfiltro:'dos.leyenda',type:'string'},
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

	/*Aqui para el tamaño de la ventana nuevo*/
	fheight:'80%',
	fwidth: '90%',
	/***/


	title:'Dosificación',
	ActSave:'../../sis_ventas_facturacion/control/Dosificacion/insertarDosificacionExter',
	ActDel:'../../sis_ventas_facturacion/control/Dosificacion/eliminarDosificacion',
	ActList:'../../sis_ventas_facturacion/control/Dosificacion/listarDosificacionInte',
	id_store:'id_dosificacion',
	fields: [
		{name:'id_dosificacion', type: 'numeric'},
		{name:'id_sucursal', type: 'numeric'},
		{name:'id_activida_economica', type: 'string'},
		{name:'inicial', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'glosa_empresa', type: 'string'},
		{name:'llave', type: 'string'},
		{name:'fecha_limite', type: 'date',dateFormat:'Y-m-d'},
		{name:'tipo_generacion', type: 'string'},
		{name:'nroaut', type: 'string'},
		{name:'fecha_inicio_emi', type: 'date',dateFormat:'Y-m-d'},
		{name:'glosa_impuestos', type: 'string'},
		{name:'tipo', type: 'string'},
		{name:'final', type: 'numeric'},
		{name:'nro_siguiente', type: 'numeric'},
		{name:'fecha_dosificacion', type: 'date',dateFormat:'Y-m-d'},
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
        {name:'desc_actividad_economica', type: 'string'},
        {name:'codigo', type: 'string'},
        {name:'estacion', type: 'string'},
        {name:'dias_restante', type: 'numeric'},
        {name:'nro_tramite', type: 'string'},
        {name:'nombre_sistema', type: 'string'},
        {name:'leyenda', type: 'string'},
        {name:'rnd', type: 'string'},

				/*Aumentando para dosificaciones de facturacion por exportacion
				Dev: Ismael Valdivia
				Fecha Mod: 19/04/2021*/
				{name:'caracteristica', type: 'string'},
				{name:'titulo', type: 'string'},
				{name:'subtitulo', type: 'string'}
				/***************************************************************/

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

    // fheight:'60%',
    // fwidth:'88%',
    onButtonNew:function() {
        Phx.vista.DosificacionInte.superclass.onButtonNew.call(this);
        this.Cmp.glosa_impuestos.setValue('ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS. EL USO ILÍCITO DE ÉSTA SERÁ SANCIONADO DE ACUERDO A LEY')
        this.Cmp.leyenda.setValue('Gracias por su preferencia!!!');
    },
    iniciarEventos :  function () {
			  this.cm.setHidden(18, true);

        this.Cmp.tipo_generacion.on('select',function (c,r,v) {
            if (this.Cmp.tipo_generacion.getValue() == 'manual') {
                this.mostrarComponente(this.Cmp.inicial);
                this.Cmp.inicial.allowBlank = false;

                this.mostrarComponente(this.Cmp.final);
                this.Cmp.final.allowBlank = false;

                this.ocultarComponente(this.Cmp.llave);
                this.Cmp.llave.allowBlank = true;
                this.Cmp.llave.reset();

                this.ocultarComponente(this.Cmp.fecha_inicio_emi);
                this.Cmp.fecha_inicio_emi.allowBlank = true;
                this.Cmp.fecha_inicio_emi.reset();

								this.ocultarComponente(this.Cmp.nombre_sistema);
                this.Cmp.nombre_sistema.allowBlank = true;


								this.Cmp.nombre_sistema.store.load({params:{start:0,limit:50},
		                   callback : function (r) {
												 this.Cmp.nombre_sistema.reset();
		                     for (var i = 0; i < r.length; i++) {
		                       if (r[i].data.descripcion == 'SISTEMAFACTURACIONBOA') {
		                         this.Cmp.nombre_sistema.setValue(r[i].data.descripcion);
		                         this.Cmp.nombre_sistema.fireEvent('select', this.Cmp.nombre_sistema,this.Cmp.nombre_sistema.store.getById(r[i].data.descripcion));

		                       }
		                     }
		                    }, scope : this
		                });


            } else {
                this.ocultarComponente(this.Cmp.inicial);
                this.Cmp.inicial.allowBlank = true;
                this.Cmp.inicial.reset();

                this.ocultarComponente(this.Cmp.final);
                this.Cmp.final.allowBlank = true;
                this.Cmp.final.reset();

                this.mostrarComponente(this.Cmp.llave);
                this.Cmp.llave.allowBlank = false;

                this.mostrarComponente(this.Cmp.fecha_inicio_emi);
                this.Cmp.fecha_inicio_emi.allowBlank = false;

								this.mostrarComponente(this.Cmp.nombre_sistema);
                this.Cmp.nombre_sistema.allowBlank = false;
								this.Cmp.nombre_sistema.reset();

            }
        },this);

    },
    onButtonEdit:function() {
        Phx.vista.DosificacionInte.superclass.onButtonEdit.call(this);
        this.Cmp.tipo_generacion.fireEvent('select');

				/*Para que la actividad economica se seleccion en el boton editar*/
				if (this.Cmp.id_activida_economica.getValue() != null && this.Cmp.id_activida_economica.getValue() != '') {
					this.Cmp.id_activida_economica.store.baseParams.id_actividad_economica = this.Cmp.id_activida_economica.getValue();
		      this.Cmp.id_activida_economica.store.load({params:{start:0,limit:50},
		        callback : function (r) {
		          if (r.length == 1 ) {
									  this.Cmp.id_activida_economica.setValue(r[0].data.id_actividad_economica);
		                this.Cmp.id_activida_economica.fireEvent('select', this.Cmp.id_activida_economica,r[0],0);
		                this.Cmp.id_activida_economica.store.baseParams.id_actividad_economica = '';
		            }
		         }, scope : this
		     });


				}
				/*****************************************************************/
    },
    onSubmit : function(o) {
        Phx.vista.DosificacionInte.superclass.onSubmit.call(this,o);
    },
    successSave:function(resp){
        var datos_respuesta = JSON.parse(resp.responseText);
        if (datos_respuesta.ROOT.datos.prueba) {
            Ext.Msg.alert('Atencion',datos_respuesta.ROOT.datos.prueba).getDialog().setSize(350,300);

        }
        Phx.vista.DosificacionInte.superclass.successSave.call(this,resp);
    },
    onButtonFacturasEmitida : function() {
        var rec={maestro:this.sm.getSelected().data};
        console.log('maestro',rec);
        Phx.CP.loadWindows('../../../sis_ventas_facturacion/vista/dosificacion/FacturasEmitidas.php',
            'Facturas Emitidas',
            {
                width:800,
                height:'80%'
            },
            rec,
            this.idContenedor,
            'FacturasEmitidas');
    }

	}
)
</script>
