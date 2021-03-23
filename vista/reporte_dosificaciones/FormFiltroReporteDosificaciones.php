<?php
/**
*@package pXP
*@file    FormFiltroReporteDosificaciones.php
*@author  Ismael Valdivia
*@date    13-03-2021
*@description permite filtrar datos para sacar el reporte de Facturas
*/
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.FormFiltroReporteDosificaciones=Ext.extend(Phx.frmInterfaz,{
    constructor:function(config)
    {

        Phx.vista.FormFiltroReporteDosificaciones.superclass.constructor.call(this,config);
        this.init();
        this.iniciarEventos();

        if(config.detalle){

  			//cargar los valores para el filtro
  			this.loadForm({data: config.detalle});
  			var me = this;
  			setTimeout(function(){
  				me.onSubmit()
  			}, 1500);

  		}
      /*Fondo de los filtros*/
      this.regiones[0].body.dom.style.background='#86CFFF';
      this.regiones[0].footer.dom.style.background='#7FB9DF';
      //this.regiones[0].footer.dom.style.border='1px solid #ffffff';
    },


    Grupos: [
  			{
  					layout: 'column',
            xtype: 'fieldset',
            region: 'north',
            collapseFirst : false,
            width: '100%',
            autoScroll:true,
            border: false,
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
                          height:'300px',
                          width:'590px',
                          backgroundColor:'#86CFFF'
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
                        height:'150px',
                        width:'300px',
                        backgroundColor:'#86CFFF'
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
                          height:'150px',
                          width:'350px',
                          backgroundColor:'#86CFFF',
                         },
                   border: false,
                   padding: '0 0 0 0',
                   bodyStyle: 'padding-left:0px;',
                   id_grupo: 2,
                   items: [],
                }]
            },
  					]
  			}
  	],


    Atributos:[
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
                      field: 'suc.codigo::integer',
                      direction: 'ASC'
                  },
                  totalProperty: 'total',
                  fields: ['id_sucursal', 'nombre', 'codigo'],
                  remoteSort: true,
                  baseParams: {par_filtro: 'suc.nombre#suc.codigo', '_adicionar':'si'}
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
             config: {
                 name: 'nombre_sucursal',
                 fieldLabel: 'Desc Pv',
                 allowBlank: true,
                 width: '100%',
                 gwidth: 110,
                 /* galign: 'right ', */
                 maxLength: 100,
                 hidden : true,
             },
             type: 'TextField',
             id_grupo: 0,
             grid: true,
             form: true
       },
      {
          config : {
              name : 'nombre_sistema',
              fieldLabel : 'Sistema de Facturación',
              width:250,
              tinit : false,
              emptyText: 'Sistema de Facturación...',
              allowBlank : false,
              origen : 'CATALOGO',
              gdisplayField : 'nombre_sistema',


              gwidth : 200,
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

      {
          config:{
              name:'tipo_generacion',
              fieldLabel:'Tipo de Generacion',
              allowBlank:false,
              emptyText:'Tipo Generación...',
              triggerAction: 'all',
              lazyRender:true,
              width:250,
              mode: 'local',
              store:['manual','computarizada']

          },
          type:'ComboBox',
          id_grupo:1,
          filters:{
              type: 'list',
              options: ['manual','computarizada'],
          },
          grid:true,
          form:true
      },

      {
          config:{
              name:'estado_dosificacion',
              fieldLabel:'Vencidas Vigentes',
              allowBlank:false,
              emptyText:'Estado Dosificación...',
              triggerAction: 'all',
              lazyRender:true,
              width:250,
              mode: 'local',
              store:['Todas','Vencidas','Vigentes']

          },
          type:'ComboBox',
          id_grupo:1,
          filters:{
              type: 'list',
              options: ['Todas','Vencidas','Vigentes'],
          },
          grid:true,
          form:true
      },




	],
	labelSubmit: '<i class="fa fa-check"></i> Aplicar Filtro',
	south: {
		url: '../../../sis_ventas_facturacion/vista/reporte_dosificaciones/DetalleReporteDosificaciones.php',
		title: 'Detalle Dosificaciones',
		height: '70%',
		cls: 'DetalleReporteDosificaciones'
	},
	title: 'Filtro de mayores',
	// Funcion guardar del formulario
	onSubmit: function(o) {
		var me = this;
		if (me.form.getForm().isValid()) {
			var parametros = me.getValForm();

      var tipo_generacion = parametros.tipo_generacion;
      var nombre_sistema = parametros.nombre_sistema;
      var nombre_sucursal = parametros.nombre_sucursal;
			var id_sucursal = parametros.id_sucursal;
      var estado_dosificacion = parametros.estado_dosificacion;
      //console.log("aqui llega los paramteros",parametros);
			 this.onEnablePanel(this.idContenedor + '-south',
				Ext.apply(parametros,{
                    'tipo_generacion': tipo_generacion,
										'nombre_sistema': nombre_sistema,
                    'nombre_sucursal':nombre_sucursal,
                    'id_sucursal':id_sucursal,
                    'estado_dosificacion':estado_dosificacion
									 }));
        }
    },

    iniciarEventos: function(){


      // this.Cmp.id_auxiliar.on('select',function(a,b,c) {
      //   this.Cmp.codigo_auxiliar.setValue(b.data.codigo_auxiliar);
      // },this);
      //
      this.Cmp.id_sucursal.on('select',function(a,b,c) {
        this.Cmp.nombre_sucursal.setValue(b.data.nombre);
      },this);


    },

    loadValoresIniciales: function(){
    	Phx.vista.FormFiltroReporteDosificaciones.superclass.loadValoresIniciales.call(this);

    }

})
</script>
