<?php
/**
*@package pXP
*@file    ConsultaFactura.php
*@author  Breydi vasquez
*@date    03-03-2021
*@description permite filtrar datos para sacar reporte boletos
*/
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.ConsultaFactura=Ext.extend(Phx.frmInterfaz,{
    constructor:function(config)
    {

        Phx.vista.ConsultaFactura.superclass.constructor.call(this,config);
        this.init();
        this.iniciarEventos();
        this.regiones[0].body.dom.style.background='#C8E3F5';
    },


    Grupos: [
  			{
  					layout: 'column',
            xtype: 'fieldset',
            region: 'north',
            collapseFirst : false,
            width: '100%',
            autoScroll:true,
            padding: '0 0 0 0',
  					items: [
              {
               bodyStyle: 'padding-right:0px;',
               autoHeight: true,
               border: false,
               items:[
                  {
                   xtype: 'fieldset',
                   frame: true,
                   border: false,
                   layout: 'form',
                   width: '90%',
                   style: {
                          height:'200px',
                          width:'590px',
                          backgroundColor:'#C8E3F5'
                       },
                   padding: '0 0 0 0',
                   bodyStyle: 'padding-left:0px;',
                   id_grupo: 0,
                   items: [],
                }]
            },
            {
             bodyStyle: 'padding-right:0px;',
             autoHeight: true,
             border: false,
             items:[
                {
                 xtype: 'fieldset',
                 frame: true,
                 border: false,
                 layout: 'form',
                 style: {
                        height:'200px',
                        width:'100%',
                        backgroundColor:'#C8E3F5'
                     },
                 padding: '0 0 0 0',
                 bodyStyle: 'padding-left:0px;',
                 id_grupo: 1,
                 items: [],
              }]
          },
            {
             bodyStyle: 'padding-right:0px;',
             border: false,
             autoHeight: true,
             items: [{
                   xtype: 'fieldset',
                   frame: true,
                   layout: 'form',
                   style: {
                          height:'200px',
                          width:'100%',
                          backgroundColor:'#C8E3F5',
                         },
                   border: false,
                   padding: '0 0 0 0',
                   bodyStyle: 'padding-left:0px;',
                   id_grupo: 2,
                   items: [],
                }]
            },
            {
             bodyStyle: 'padding-right:0px;',
             border: false,
             autoHeight: true,
             items: [{
                   xtype: 'fieldset',
                   frame: true,
                   layout: 'form',
                   style: {
                          height:'200px',
                          width:'100%',
                          backgroundColor:'#C8E3F5',
                         },
                   border: false,
                   padding: '0 0 0 0',
                   bodyStyle: 'padding-left:0px;',
                   id_grupo: 3,
                   items: [],
                }]
            },
  					]
  			}
  	],


    Atributos:[
      {
        config: {
            name: 'id_lugar',
            fieldLabel: 'Estación',
            allowBlank: false,
            emptyText: 'Elija un Punto...',
            store: new Ext.data.JsonStore(
                {
                    url: '../../sis_ventas_facturacion/control/ReporteVentas/puntoVentaCiudadStage',
                    id: 'city_name',
                    root: 'datos',
                    sortInfo: {
                        field: 'city_name',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['city_name', 'city_code'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'city_name#city_code',_adicionar:'si',pais_ini:'BO'}
                }),
            valueField: 'city_code',
            displayField: 'city_name',
            gdisplayField: 'city_code',
            hiddenName: 'city_code',
            tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{city_name} -- <span style="color:green;">{city_code}</span></b></p></div></tpl>',
            // store: new Ext.data.JsonStore({
            //     url: '../../sis_parametros/control/Lugar/listarLugar',
            //     id: 'id_lugar',
            //     root: 'datos',
            //     sortInfo: {
            //         field: 'nombre',
            //         direction: 'ASC'
            //     },
            //     totalProperty: 'total',
            //     fields: ['id_lugar', 'codigo','nombre'],
            //     remoteSort: true,
            //     baseParams: {par_filtro: 'codigo', lugar_estacion: 'Bol', _adicionar: true}
            // }),
            // valueField: 'id_lugar',
            // displayField: 'codigo',
            // gdisplayField: 'codigo',
            // hiddenName: 'id_lugar',
            // tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b> <span style="color:green;font-weight:bold;"> ({codigo})</span></p></div></tpl>',
            forceSelection: true,
            typeAhead: false,
            triggerAction: 'all',
            lazyRender: true,
            mode: 'remote',
            pageSize: 16,
            queryDelay: 1000,
            gwidth: 150,
            width: 250,
            listWidth: 250,
            resizable: true,
            minChars: 2,
            hidden: false,
            style:'margin-bottom: 10px;'
        },
        type: 'ComboBox',
        id_grupo: 0,
        grid: true,
        form: true
    },
    {
        config: {
            name: 'id_sucursal',
            fieldLabel: 'Sucursal',
            allowBlank: false,
            emptyText: 'Elija una Sucursal...',
            store: new Ext.data.JsonStore({
                url: '../../sis_ventas_facturacion/control/Sucursal/listarSucursalXestacion',
                id: 'id_sucursal',
                root: 'datos',
                sortInfo: {
                    field: 'nombre',
                    direction: 'ASC'
                },
                totalProperty: 'total',
                fields: ['id_sucursal', 'nombre', 'codigo', 'id_lugar'],
                remoteSort: true,
                baseParams: {par_filtro: 'nombre#codigo', x_estacion: 'x_estacion',_adicionar:'si'}
            }),
            valueField: 'id_sucursal',
            gdisplayField: 'nombre_sucursal',
            displayField: 'nombre',
            hiddenName: 'id_sucursal',
            tpl: new Ext.XTemplate([
                '<tpl for=".">',
                '<div class="x-combo-list-item">',
                '<p><b>{nombre}<span style="color: green;">( {codigo} )</span> </b></p></div>',
                '</div></tpl>'
            ]),
            forceSelection: true,
            typeAhead: false,
            triggerAction: 'all',
            lazyRender: true,
            listWidth: 320,
            mode: 'remote',
            pageSize: 15,
            width: 250,
            queryDelay: 1000,
            minChars: 2,
            resizable: true,
            hidden: false,
            style:'margin-bottom: 10px;'
        },
        type: 'ComboBox',
        id_grupo: 0,
        form: true
    },
    {
        config: {
            name: 'id_punto_venta',
            fieldLabel: 'Punto de Venta',
            allowBlank: false,
            emptyText: 'Elija un Punto...',
            store: new Ext.data.JsonStore({
                url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
                id: 'id_punto_venta',
                root: 'datos',
                sortInfo: {
                    field: 'nombre',
                    direction: 'ASC'
                },
                totalProperty: 'total',
                fields: ['id_punto_venta', 'nombre', 'codigo', 'id_sucursal'],
                remoteSort: true,
                baseParams: {tipo_usuario: 'todos', par_filtro: 'puve.nombre#puve.codigo', _adicionar: true}
            }),
            valueField: 'id_punto_venta',
            displayField: 'nombre',
            gdisplayField: 'nombre_punto_venta',
            hiddenName: 'id_punto_venta',
            tpl: new Ext.XTemplate([
                '<tpl for=".">',
                '<div class="x-combo-list-item">',
                '<p><b>Código: {codigo}</b></p>',
                '<p><b>Nombre: <span style="color: green;">{nombre}</span></b></p>',
                '</div></tpl>'
            ]),
            forceSelection: true,
            typeAhead: false,
            triggerAction: 'all',
            lazyRender: true,
            mode: 'remote',
            pageSize: 15,
            queryDelay: 1000,
            gwidth: 150,
            width: 250,
            listWidth: 350,
            resizable: true,
            minChars: 2,
            hidden: false
        },
        type: 'ComboBox',
        id_grupo: 0,
        form: true
    },

    {
        config: {
            name: 'tipo_documento',
            fieldLabel: 'Tipo Documento',
            typeAhead: true,
            allowBlank: false,
            triggerAction: 'all',
            emptyText: 'Tipo...',
            selectOnFocus: true,
            mode: 'local',
            store: new Ext.data.ArrayStore({
                fields: ['ID', 'valor'],
                data: [['factura', 'Factura'],
                    ['recibo', 'Recibo']
                ]
            }),
            valueField: 'ID',
            displayField: 'valor',
            width: 100,
            style:'margin-bottom: 10px;'
        },
        type: 'ComboBox',
        id_grupo: 1,
        form: true
    },
    {
        config:{
            name: 'fecha_ini',
            fieldLabel: 'DESDE',
            width: 100,
            format: 'd/m/Y',
            allowBlank: true,
            style:'margin-bottom: 10px;'
        },
        type:'DateField',
        id_grupo:1,
        form:true
    },
    {
        config:{
            name: 'fecha_fin',
            fieldLabel: 'HASTA',
            allowBlank: true,
            width: 100,
            format: 'd/m/Y',
            style:'margin-bottom: 10px;'
        },
        type:'DateField',
        id_grupo:1,
        form:true
    },
    {
			config:{
				name: 'nro_documento',
				fieldLabel: 'N° Documento',
				allowBlank: true,
				width: 150,
				gwidth: 170,
        style:'margin-bottom: 10px;'
			},
				type:'TextField',
				id_grupo:2,
				form:true
		},
    {
			config:{
				name: 'nro_autorizacion',
				fieldLabel: 'N° Autorizacion',
				allowBlank: true,
				width: 150,
				gwidth: 170
			},
				type:'TextField',
				id_grupo:2,
				form:true
		},
    {
        config: {
            name: 'estado_documento',
            fieldLabel: 'Estado Factura/recibo',
            typeAhead: true,
            allowBlank: true,
            triggerAction: 'all',
            emptyText: 'Tipo...',
            selectOnFocus: true,
            mode: 'local',
            store: new Ext.data.ArrayStore({
                fields: ['ID', 'valor'],
                data: [['anulado', 'ANULADO'],
                    ['finalizado', 'VALIDA']
                ]
            }),
            valueField: 'ID',
            displayField: 'valor',
            width: 150,
            style:'margin-bottom: 10px;'
        },
        type: 'ComboBox',
        id_grupo: 2,
        form: true
    },
    {
			config:{
				name: 'nit',
				fieldLabel: 'NIT',
				allowBlank: true,
				width: 150,
			},
				type:'TextField',
				id_grupo:3,
				form:true
		},
	],
	labelSubmit: '<i class="fa fa-check"></i> Aplicar Filtro',
	south: {
		url: '../../../sis_ventas_facturacion/vista/consulta_boletos/DetalleFacturaConsulta.php',
		title: 'Detalle',
		height: '75%',
		cls: 'DetalleFacturaConsulta'
	},
	title: 'Filtro de mayores',
	// Funcion guardar del formulario
	onSubmit: function(o) {
		var me = this;
		if (me.form.getForm().isValid()) {
			var parametros = me.getValForm();
      
      var id_entidad = parametros.id_lugar;
      var id_sucursal = parametros.id_sucursal;
      var id_punto_venta = parametros.id_punto_venta;
			var tipo_documento = parametros.tipo_documento;
      var nro_documento =  parametros.nro_documento;
      var nro_autorizacion = parametros.nro_autorizacion;
      var estado_documento = parametros.estado_documento;
      var fecha_ini = parametros.fecha_ini;
      var fecha_fin = parametros.fecha_fin;
      var nit = parametros.nit;

      //console.log("aqui llega los paramteros",parametros);
			 this.onEnablePanel(this.idContenedor + '-south',
				Ext.apply(parametros,{
                    'id_entidad': id_entidad,
                    'id_sucursal': id_sucursal,
                    'id_punto_venta': id_punto_venta,
                    'tipo_documento': tipo_documento,
                    'nro_documento':nro_documento,
                    'nro_autorizacion':nro_autorizacion,
                    'estado_documento':estado_documento,
                    'fecha_ini': fecha_ini,
                    'fecha_fin': fecha_fin,
                    'nit':nit
									 }));
        }
    },

    iniciarEventos: function(){

      this.Cmp.id_lugar.on('select', function (cmp, rec, indice) {
        // console.log("data",rec);
          this.Cmp.id_sucursal.reset();
          this.Cmp.id_punto_venta.reset();
          this.Cmp.id_sucursal.store.baseParams.cod_lugar = rec.data.city_code;
          this.Cmp.id_sucursal.modificado = true;
      }, this);

      this.getComponente('id_sucursal').on('select', function (cmp, rec, indice) {
          this.Cmp.id_punto_venta.reset();
          this.Cmp.id_punto_venta.store.baseParams.id_sucursal = rec.data.id_sucursal;
          this.Cmp.id_punto_venta.modificado = true;
      }, this);

      this.getComponente('tipo_documento').on('select', function(c,r,i){
          if (r.data.ID=='recibo'){
              this.ocultarComponente(this.Cmp.nro_autorizacion)
          }else{
              this.mostrarComponente(this.Cmp.nro_autorizacion)
          }
      }, this);
      this.Cmp.nit.el.dom.labels[0].style.width='40px';
      this.Cmp.nit.el.dom.offsetParent.style.paddingLeft='45px';
    },

    loadValoresIniciales: function(){
    	Phx.vista.ConsultaFactura.superclass.loadValoresIniciales.call(this);
    }

})
</script>
