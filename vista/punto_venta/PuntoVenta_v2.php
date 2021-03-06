<?php
/**
*@package pXP
*@file gen-PuntoVenta_v2.php
*@author  (IsmaelValdivia)
*@date 07-10-2015 21:02:00
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.PuntoVenta_v2=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.PuntoVenta_v2.superclass.constructor.call(this,config);
		this.init();
		this.store.baseParams.id_sucursal = this.maestro.id_sucursal;
		this.load({params:{start:0, limit:this.tam_pag}});
		// this.addButton('btnProductos',
    //         {
    //             text: 'Productos',
    //             iconCls: 'blist',
    //             disabled: true,
    //             handler: this.onButtonProductos,
    //             tooltip: 'Productos por Punto de Venta'
    //         }
    //     );
        /*Fondo color tbar (IRVA)*/
        this.bbar.el.dom.style.background='#77C5BB';
        this.tbar.el.dom.style.background='#77C5BB';
        this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#E4F9F6';
        this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#B3DCD7';
        /************************/
	},
	// onButtonProductos : function() {
  //       var rec = {maestro: this.sm.getSelected().data};
  //
  //           Phx.CP.loadWindows('../../../sis_ventas_facturacion/vista/punto_venta_producto/PuntoVentaProducto.php',
  //                   'Productos por punto de venta',
  //                   {
  //                       width:800,
  //                       height:'80%'
  //                   },
  //                   rec,
  //                   this.idContenedor,
  //                   'PuntoVentaProducto');
  //   },

	Atributos:[
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
            config:{
                name: 'codigo',
                fieldLabel: 'Código',
                allowBlank: true,
                anchor: '80%',
                gwidth: 150,
                maxLength:20
            },
                type:'TextField',
                filters:{pfiltro:'puve.codigo',type:'string'},
                id_grupo:1,
								bottom_filter:true,
                grid:true,
                form:true
        },

		{
			config:{
				name: 'nombre',
				fieldLabel: 'Nombre',
				allowBlank: false,
				anchor: '80%',
				gwidth: 150,
				maxLength:100
			},
				type:'TextField',
				filters:{pfiltro:'puve.nombre',type:'string'},
				id_grupo:1,
				bottom_filter:true,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'descripcion',
				fieldLabel: 'Descripción',
				allowBlank: true,
				anchor: '80%',
				gwidth: 200,
				maxLength:500
			},
				type:'TextField',
				filters:{pfiltro:'puve.descripcion',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config : {
				name : 'tipo',
				fieldLabel : 'Tipo',
				tinit : false,
				allowBlank : false,
				origen : 'CATALOGO',
				gdisplayField : 'tipo',
				gwidth : 200,
				anchor : '80%',
				valueField: 'codigo',
				baseParams : {
					cod_subsistema : 'VEF',
					catalogo_tipo : 'tipo_punto_venta'
				}
			},
			type : 'ComboRec',
			id_grupo : 0,
			filters : {
				pfiltro : 'puve.tipo',
				type : 'string'
			},
			grid : true,
			form : true
		},
		{
            config:{
                name: 'habilitar_comisiones',
                fieldLabel: 'Habilitar Comisiones',
                allowBlank: false,
                anchor: '40%',
                gwidth: 130,
                maxLength:2,
                emptyText:'si/no...',
                typeAhead: true,
                triggerAction: 'all',
                lazyRender:true,
                mode: 'local',
               // displayField: 'descestilo',
                store:['si','no']
            },
            type:'ComboBox',
            //filters:{pfiltro:'promac.inicio',type:'string'},
            id_grupo:1,
            filters:{
                         type: 'list',
                         pfiltro:'puve.habilitar_comisiones',
                         options: ['si','no'],
                    },
            grid:true,
            form:true
        },
				{
						config: {
								name: 'id_catalogo_canal',
								fieldLabel: 'Canal de Venta',
								allowBlank: true,
								emptyText: 'Canal venta...',
								store: new Ext.data.JsonStore(
										{
												url: '../../sis_ventas_facturacion/control/ReporteVentas/listarCanalVentaPuntoVenta',
												id: 'id_catalogo',
												root: 'datos',
												sortInfo: {
														field: 'codigo',
														direction: 'ASC'
												},
												totalProperty: 'total',
												fields: ['id_catalogo', 'codigo', 'descripcion'],
												remoteSort: true,
												baseParams: {cod_catalogo: 'canal_venta', par_filtro:'codigo#descripcion'}
										}),
								valueField: 'id_catalogo',
								displayField: 'codigo',
								gdisplayField: 'cod_canal',
								hiddenName: 'id_catalogo',
								tpl:'<tpl for="."><div class="x-combo-list-item"><p style="text-transform: uppercase;"><b>{codigo}</b></p></div></tpl>',
								triggerAction: 'all',
								lazyRender: true,
								mode: 'remote',
								pageSize: 50,
								queryDelay: 500,
								gwidth : 200,
								anchor : '80%',
								forceSelection: true,
								minChars: 2,
								style:'margin-bottom: 10px;'
						},
						type: 'ComboBox',
						filters: {pfiltro: 'lug.nombre', type: 'string'},
						id_grupo: 0,
						grid: true,
						form: true
				},
				{
					config:{
						name: 'office_id',
						fieldLabel: 'OfficeId',
						allowBlank: false,
						anchor: '80%',
						gwidth: 150,
						maxLength:100
					},
						type:'TextField',
						filters:{pfiltro:'puve.nombre',type:'string'},
						id_grupo:1,
						bottom_filter:true,
						grid:true,
						form:true
				},
				{
						config: {
								name: 'id_catalogo',
								fieldLabel: 'Osd',
								allowBlank: true,
								emptyText: 'OSD...',
								store: new Ext.data.JsonStore(
										{
												url: '../../sis_ventas_facturacion/control/ReporteVentas/listarCanalVentaPuntoVenta',
												id: 'id_catalogo',
												root: 'datos',
												sortInfo: {
														field: 'codigo',
														direction: 'ASC'
												},
												totalProperty: 'total',
												fields: ['id_catalogo', 'codigo', 'descripcion'],
												remoteSort: true,
												baseParams: {cod_catalogo: 'osd', par_filtro:'codigo#descripcion'}
										}),
								valueField: 'id_catalogo',
								displayField: 'codigo',
								gdisplayField: 'cod_osd',
								hiddenName: 'id_catalogo',
								tpl:'<tpl for="."><div class="x-combo-list-item"><p style="text-transform: uppercase;"><b>{codigo}</b></p></div></tpl>',
								triggerAction: 'all',
								lazyRender: true,
								mode: 'remote',
								pageSize: 50,
								queryDelay: 500,
								gwidth : 100,
								anchor : '80%',
								forceSelection: true,
								minChars: 2,
								style:'margin-bottom: 10px;'
						},
						type: 'ComboBox',
						id_grupo: 0,
						form: true,
						grid:true
				},
				{
        config : {
            name : 'iata_status',
            fieldLabel : 'IATA Status',
            allowBlank : true,
            triggerAction : 'all',
            lazyRender : true,
						gwidth : 100,
						anchor : '50%',
            mode : 'local',
            emptyText:'...',
            store: new Ext.data.ArrayStore({
                id: '',
                fields: [
                    'key',
                    'value'
                ],
                data: [
                    // ['todos', 'Todos'],
                    ['P', 'P'],
                    ['S', 'S']
                ]
            }),
            valueField: 'key',
            displayField: 'value'
        },
        type : 'ComboBox',
        id_grupo : 1,
        form : true,
        grid : true
    },
		{
			config:{
				name: 'nombre_amadeus',
				fieldLabel: 'Nombre Segun Amadeus',
				allowBlank: true,
				anchor: '80%',
				gwidth: 150
			},
				type:'TextField',
				id_grupo:0,
				bottom_filter:true,
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
				filters:{pfiltro:'puve.estado_reg',type:'string'},
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
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'puve.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'puve.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'puve.usuario_ai',type:'string'},
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
				filters:{pfiltro:'puve.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
  fheight:430,
  fwidth:470,
	title:'Punto de Venta',
	ActSave:'../../sis_ventas_facturacion/control/PuntoVenta/insertarPuntoVenta',
	ActDel:'../../sis_ventas_facturacion/control/PuntoVenta/eliminarPuntoVenta',
	ActList:'../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
	id_store:'id_punto_venta',
	fields: [
		{name:'id_punto_venta', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'tipo', type: 'string'},
		{name:'id_sucursal', type: 'numeric'},
		{name:'nombre', type: 'string'},
		{name:'codigo', type: 'string'},
		{name:'habilitar_comisiones', type: 'string'},
		{name:'descripcion', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'office_id', type: 'string'},
		{name:'id_catalogo', type: 'numeric'},
		{name:'cod_osd', type: 'string'},
		{name:'iata_status', type: 'string'},
		{name:'id_catalogo_canal', type: 'numeric'},
		{name:'cod_canal', type: 'string'},
		{name:'nombre_amadeus', type:'string'}

	],
	sortInfo:{
		field: 'id_punto_venta',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true,
	loadValoresIniciales:function()
    {
    	this.Cmp.id_sucursal.setValue(this.maestro.id_sucursal);
        Phx.vista.PuntoVenta_v2.superclass.loadValoresIniciales.call(this);
    },
    south:
    {
              url:'../../../sis_ventas_facturacion/vista/sucursal_usuario/SucursalUsuario_v2.php',
              title:'<center style="font-size:30px; color:#007a69;"><i style="font-size:35px;" class="fa fa-user-plus" aria-hidden="true"></i> Usuarios</center>',
              width:'40%',
              height : '50%',
              cls:'SucursalUsuario_v2'
    },

    onButtonEdit:function() {
            Phx.vista.PuntoVenta_v2.superclass.onButtonEdit.call(this);
            this.form.el.dom.firstChild.childNodes[0].style.background = '#77C5BB';
    },
    onButtonNew: function (){
      Phx.vista.PuntoVenta_v2.superclass.onButtonNew.call(this);
      this.form.el.dom.firstChild.childNodes[0].style.background = '#77C5BB';
    },

	preparaMenu:function()
    {
        //this.getBoton('btnProductos').enable();
        Phx.vista.PuntoVenta_v2.superclass.preparaMenu.call(this);
    },


    liberaMenu:function()
    {
        //this.getBoton('btnProductos').disable();
        Phx.vista.PuntoVenta_v2.superclass.liberaMenu.call(this);
    }
   }
)
</script>
