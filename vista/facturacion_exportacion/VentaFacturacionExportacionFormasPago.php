<?php
/**
*@package pXP
*@file gen-VentaFacturacionExportacionFormasPago.php
*@author  (ivaldivia)
*@date 10-05-2019 19:08:47
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.VentaFacturacionExportacionFormasPago=Ext.extend(Phx.gridInterfaz,{

    constructor:function(config){
        this.maestro=config.maestro;
        //llama al constructor de la clase padre
        Phx.vista.VentaFacturacionExportacionFormasPago.superclass.constructor.call(this,config);
        this.grid.getTopToolbar().disable();
        this.grid.getBottomToolbar().disable();
        this.instanciasPagoAnticipo = 'no';
        this.init();

        var dataPadre = Phx.CP.getPagina(this.idContenedorPadre).getSelectedData();
            if(dataPadre){
                this.onEnablePanel(this, dataPadre);
            }
            else
            {
               this.bloquearMenus();
            }
        this.iniciarEventos();

        this.bbar.el.dom.style.background='radial-gradient(ellipse at center, #cfe7fa 0%,#6393c1 100%)';
    		this.tbar.el.dom.style.background='radial-gradient(ellipse at center, #cfe7fa 0%,#6393c1 100%)';

    },
    Grupos: [
        {
            layout: 'column',
            border: false,
            xtype: 'fieldset',
            // defaults are applied to all child items unless otherwise specified by child item
            defaults: {
                border: false
            },

            items: [{
                xtype: 'fieldset',
                title: '<b style="color:#000000;">Datos FOB<b>',
                autoHeight: true,
                border: true,
                style:{
    							 background:'radial-gradient(ellipse at center, #d2ff52 7%,#91e842 64%)',
    							 height : '300px',
                   marginLeft:'7px',
                   padding:'10'
    						 },
                defaults: {
                    anchor: '23' // leave room for error icon
                },
                items: [],
                id_grupo:0
            },
            {
                xtype: 'fieldset',
                title: '<b style="color:#000000;">Datos CIF<b>',
                autoHeight: true,
                border: true,
                style:{
              	 				  background:'radial-gradient(ellipse at center, #a9e4f7 0%,#0fb4e7 100%)',
              	          height : '300px',
                          marginLeft:'7px',
                          padding:'10'
                //
              	     },
                defaults: {
                    anchor: '23' // leave room for error icon
                },
                items: [],
                id_grupo:1
            },
            {
                xtype: 'fieldset',
                title: '<b style="color:#000000;">Datos Formas de Pago<b>',
                autoHeight: true,
                border: true,
                style:{
              	 				  background:'radial-gradient(ellipse at center, #a9e4f7 0%,#0fb4e7 100%)',
              	          height : '300px',
                          marginLeft:'7px',
                          padding:'10'
                //
              	     },
                defaults: {
                    anchor: '23' // leave room for error icon
                },
                items: [],
                id_grupo:2
            }
          ]
        }
    ],
    // Grupos: [
  	// 		{
  	// 				layout: 'column',
  	// 				border: false,
  	// 				// defaults are applied to all child items unless otherwise specified by child item
  	// 				xtype: 'fieldset',
    //         frame:true,
    //         autoScroll: true,
    //
  	// 				items:/*ABRE*/[
  	// 						{//ABRE PARA PONER ITEMS
  	// 							xtype: 'fieldset',
  	// 							border: false,
  	// 							layout: 'column',
  	// 							//region: 'north',
  	// 							 style:{
  	// 								 width:'100%',
  	// 								},
  	// 							padding: '0 0 0 10',
  	// 							items:[
    //
  	// 										{
  	// 										 //bodyStyle: 'padding-left:20px;',
  	// 										 autoHeight: true,
  	// 										 autoScroll: true,
  	// 										 style:{
  	// 											 border:'1px solid #DEDEDE',
  	// 											 width:'47%',
  	// 											 height : '70%',
  	// 											 marginTop:'2px',
    //
  	// 										 },
  	// 										 border: false,
  	// 										 items:[
  	// 											 {
  	// 													 xtype: 'fieldset',
  	// 													 layout: 'form',
  	// 													 border: false,
  	// 													 frame: true,
  	// 													 style:{
  	// 														 background:'radial-gradient(ellipse at center, #d2ff52 7%,#91e842 64%)',
  	// 														 width:'100%',
  	// 														 height : '100%',
  	// 													 },
  	// 													 title: 'Datos FOB',
  	// 													 bodyStyle: 'padding:0 10px 0;',
  	// 													 columnWidth: 0.5,
  	// 													 items: [],
  	// 													 id_grupo: 0,
  	// 													 collapsible: false
  	// 											 }
  	// 										]
  	// 								},
  	// 								{
  	// 								 bodyStyle: 'padding-right:5px;',
  	// 								 autoHeight: true,
  	// 								 autoScroll: true,
  	// 								 style:{
  	// 									border:'1px solid #DEDEDE',
  	// 									width:'50%',
  	// 									height : '70%',
  	// 								 },
  	// 								 border: false,
  	// 								 items:[
  	// 									 {
  	// 											 xtype: 'fieldset',
  	// 											 layout: 'form',
  	// 											 border: false,
  	// 											 frame: true,
  	// 											 style:{
  	// 												 background:'radial-gradient(ellipse at center, #a9e4f7 0%,#0fb4e7 100%)',
  	// 												 width:'100%',
  	// 												 height : '100%',
    //
  	// 											 },
  	// 											 title: 'Datos Formas de Pago',
  	// 											 bodyStyle: 'padding:0 10px 0;',
  	// 											 columnWidth: 0.5,
  	// 											 items: [],
  	// 											 id_grupo: 1,
  	// 											 collapsible: false
  	// 									 }
  	// 								]
  	// 							}
  	// 						]
  	// 					}//CIERRA PONER ITEMS
  	// 				]/*CIERRA*/
  	// 		}
  	// ],


    Atributos:[
        {
            //configuracion del componente
            config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_venta_forma_pago'
            },
            type:'Field',
            form:true
        },
        {
          config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_venta'
            },
            type:'Field',
            form:true
        },
        {
          config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'fop_code'
            },
            type:'Field',
            form:true
        },

        /*Aumentando para los campos de exportacion*/
        {
            config:{
                name: 'valor_bruto',
                fieldLabel: 'Valor Bruto',
                allowBlank: false,
                width:150,
                maxLength:20,
                enableKeyEvents: true,
                allowNegative:false,
            },
                type:'NumberField',
                id_grupo:0,
                form:true,
        },
        {
            config:{
                name: 'transporte_fob',
                fieldLabel: 'Transporte FOB',
                allowBlank: false,
                width:150,
                maxLength:20,
                enableKeyEvents: true,
                allowNegative:false,
            },
                type:'NumberField',
                id_grupo:0,
                form:true,
        },
        {
            config:{
                name: 'seguros_fob',
                fieldLabel: 'Seguros FOB',
                allowBlank: false,
                width:150,
                maxLength:20,
                enableKeyEvents: true,
                allowNegative:false,
            },
                type:'NumberField',
                id_grupo:0,
                form:true,
        },
        {
            config:{
                name: 'otros_fob',
                fieldLabel: 'Otros FOB',
                allowBlank: false,
                width:150,
                maxLength:20,
                enableKeyEvents: true,
                allowNegative:false,
            },
                type:'NumberField',
                id_grupo:0,
                form:true,
        },
        {
            config:{
                name: 'total_fob',
                fieldLabel: 'Total FOB',
                allowBlank: false,
                width:150,
                maxLength:20,
                enableKeyEvents: true,
                allowNegative:false,
            },
                type:'NumberField',
                id_grupo:0,
                form:true,
        },
        /*******************************************/

        /*Campos para los montos CIF*/
        {
            config:{
                name: 'transporte_cif',
                fieldLabel: 'Transporte Internacional',
                allowBlank: false,
                width:150,
                maxLength:20,
                enableKeyEvents: true,
                allowNegative:false,
            },
                type:'NumberField',
                id_grupo:1,
                form:true,
        },
        {
            config:{
                name: 'seguros_cif',
                fieldLabel: 'Seguro Internacional',
                allowBlank: false,
                width:150,
                maxLength:20,
                enableKeyEvents: true,
                allowNegative:false,
            },
                type:'NumberField',
                id_grupo:1,
                form:true,
        },
        {
            config:{
                name: 'otros_cif',
                fieldLabel: 'Otros',
                allowBlank: false,
                width:150,
                maxLength:20,
                enableKeyEvents: true,
                allowNegative:false,
            },
                type:'NumberField',
                id_grupo:1,
                form:true,
        },
        {
            config:{
                name: 'total_cif',
                fieldLabel: 'Total CIF-VANCOUVER',
                allowBlank: false,
                width:150,
                maxLength:20,
                enableKeyEvents: true,
                allowNegative:false,
            },
                type:'NumberField',
                id_grupo:1,
                form:true,
        },
        /****************************/


        {
            config: {
                name: 'id_moneda',
                origen: 'MONEDA',
                allowBlank: false,
                fieldLabel: 'Moneda',
                gdisplayField: 'desc_moneda',
                gwidth: 50,
                renderer: function (value, p, record) {
                    return String.format('{0}', record.data['desc_moneda']);
                },
                width:150
            },
            type: 'ComboRec',
            id_grupo: 2,
            grid: true,
            form: true
        },
        {
            config: {
                name: 'id_medio_pago',
                fieldLabel: 'Medio de pago',
                allowBlank: false,
                width:150,
                emptyText: 'Medio de pago...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_obingresos/control/MedioPagoPw/listarMedioPagoPw',
                    id: 'id_medio_pago',
                    root: 'datos',
                    sortInfo: {
                        field: 'name',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_medio_pago_pw', 'name', 'fop_code'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'mppw.name#fp.fop_code', emision:'FACTCOMP'}
                }),
                valueField: 'id_medio_pago_pw',
                displayField: 'name',
                gdisplayField: 'name',
                hiddenName: 'id_medio_pago_pw',
                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Medio de Pago: <font color="Blue">{name}</font></b></p><b><p>Codigo: <font color="red">{fop_code}</font></b></p></div></tpl>',
                forceSelection: true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 15,
                queryDelay: 1000,
                gwidth: 150,
                listWidth:450,
                resizable:true,
                minChars: 2,
                renderer : function(value, p, record) {
                    return String.format('{0}', record.data['name']);
                }
            },
            type: 'ComboBox',
            id_grupo: 2,
            grid: true,
            form: true
        },
        {
            config:{
                name: 'importe_recibido',
                fieldLabel: 'Importe Recibido',
                allowBlank: true,
                width:150,
                maxLength:20,
                renderer : function(value, p, record) {
                    return String.format('{0}', record.data['monto_forma_pago']);
                }
            },
                type:'NumberField',
                id_grupo:2,
                grid:true,
                form:false
        },
        {
            config:{
                name: 'num_tarjeta',
                fieldLabel: 'N° Tarjeta',
                allowBlank: true,
                width:150,
                gwidth:100,
                maxLength:20,
                minLength:15
            },
            type:'TextField',
            id_grupo:2,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'codigo_tarjeta',
                fieldLabel: 'Codigo de Autorización',
                allowBlank: true,
                width:150,
                minLength:6,
                maxLength:6,
                style:'text-transform:uppercase;',
                maskRe: /[a-zA-Z0-9]+/i,
                regex: /[a-zA-Z0-9]+/i

            },
                type:'TextField',
                id_grupo:2,
                form:true
        },
        {
            config:{
                name: 'mco',
                fieldLabel: 'MCO',
                allowBlank: false,
                width:150,
                gwidth: 150,
                minLength:15,
                maxLength:20
            },
            type:'TextField',
            id_grupo:2,
            grid:true,
            form:true
        },
        {
    			config: {
    				name: 'id_auxiliar',
    				fieldLabel: 'Cuenta Corriente',
    				allowBlank: true,
            width:150,
    				emptyText: 'Cuenta Corriente...',
    				store: new Ext.data.JsonStore({
    					url: '../../sis_contabilidad/control/Auxiliar/listarAuxiliar',
    					id: 'id_auxiliar',
    					root: 'datos',
    					sortInfo: {
    						field: 'codigo_auxiliar',
    						direction: 'ASC'
    					},
    					totalProperty: 'total',
    					fields: ['id_auxiliar', 'codigo_auxiliar','nombre_auxiliar'],
    					remoteSort: true,
    					baseParams: {par_filtro: 'auxcta.codigo_auxiliar#auxcta.nombre_auxiliar',corriente:'si'}
    				}),
    				valueField: 'id_auxiliar',
    				displayField: 'nombre_auxiliar',
    				gdisplayField: 'codigo_auxiliar',
    				hiddenName: 'id_auxiliar',
    				tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre_auxiliar}</p><p>Codigo:{codigo_auxiliar}</p> </div></tpl>',
    				forceSelection: true,
    				typeAhead: false,
    				triggerAction: 'all',
    				lazyRender: true,
    				mode: 'remote',
    				pageSize: 15,
    				queryDelay: 1000,
    				gwidth: 150,
    				listWidth:350,
    				resizable:true,
    				minChars: 2,
    				renderer : function(value, p, record) {
    					return String.format('{0}', record.data['nombre_auxiliar']);
    				}
    			},
    			type: 'ComboBox',
    			id_grupo: 2,
    			grid: true,
    			form: true
    		},
        {
            config:{
                name: 'monto_forma_pago',
                fieldLabel: 'Importe Recibido',
                allowBlank: false,
                width:150,
                maxLength:20,
                enableKeyEvents: true,
                allowNegative:false,
            },
                type:'NumberField',
                id_grupo:2,
                form:true,
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
                filters:{pfiltro:'placal.estado_reg',type:'string'},
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
                type:'NumberField',
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
                filters:{pfiltro:'placal.fecha_reg',type:'date'},
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
                filters:{pfiltro:'placal.fecha_mod',type:'date'},
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
                type:'NumberField',
                filters:{pfiltro:'usu2.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
        }
    ],
    tam_pag:50,

    fheight:'70%',
  	fwidth:'90%',

    title:'Detalle Forma Pago',
    ActSave:'../../sis_ventas_facturacion/control/FacturacionExportacion/insertarFormasPago',
    //ActDel:'../../sis_contabilidad/control/PlantillaCalculo/eliminarPlantillaCalculo',
    ActList:'../../sis_ventas_facturacion/control/FacturacionExportacion/listarFormasPagoExportacion',
    id_store:'id_venta_forma_pago',
    fields: [
      {name: 'id_medio_pago',type: 'numeric'},
      {name: 'id_venta', type:'id_venta'},
      {name: 'id_moneda',     type: 'numeric'},
      {name: 'id_venta_forma_pago',type: 'numeric'},
      {name: 'nombre',      type: 'string'},
      {name: 'codigo_tarjeta',     type: 'string'},
      {name: 'num_tarjeta',     type: 'string'},
      {name: 'monto_forma_pago',     type: 'numeric'},
      {name:'desc_moneda',type:'string'},
      {name: 'name',      type: 'string'},
      {name: 'fop_code', type:'string'},
      {name: 'id_auxiliar', type:'int4'},
      {name: 'nombre_auxiliar', type:'string'},
      {name: 'codigo_auxiliar', type:'string'},
      {name:'mco',type:'string'},
      {name:'usr_reg',type:'string'},
      {name:'usr_mod',type:'string'},
      {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
      {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},



    ],
    sortInfo:{
        field: 'id_venta_forma_pago',
        direction: 'ASC'
    },
    bdel:false,
  	bsave:false,
  	bedit:false,
  	bexcel:false,
  	btest:false,
    bnew:false,

    loadValoresIniciales:function(){
        Phx.vista.VentaFacturacionExportacionFormasPago.superclass.loadValoresIniciales.call(this);
        this.getComponente('id_venta').setValue(this.maestro.id_venta);
        //this.Cmp.id_venta.setValue(this.maestro.id_venta);
    },
    onReloadPage:function(m){
        this.maestro=m;
        this.store.baseParams={id_venta:this.maestro.id_venta};
        this.load({params:{start:0, limit:this.tam_pag}});
    },
    iniciarEventos:function(){

      this.Cmp.codigo_tarjeta.setVisible(false);
      this.Cmp.num_tarjeta.setVisible(false);
      this.Cmp.id_auxiliar.setVisible(false);
      this.Cmp.mco.setVisible(false);
      this.Cmp.id_medio_pago.store.baseParams.regional = 'BOL';
      this.Cmp.id_medio_pago.store.baseParams.defecto = 'no';
      this.Cmp.id_medio_pago.store.baseParams.filtrar_base = 'si';
    },
    // east: {
  	// 		url:'../../../sis_ventas_facturacion/vista/venta/DetalleVentaFormaPagoRecibo.php',
  	// 		title:'Detalle Forma Pago',
  	// 		height:'50%',
    //      width: 500,
  	// 		cls:'DetalleVentaFormaPagoRecibo'
    //   		// url:'../../../sis_ventas_facturacion/vista/venta/DetalleVentaRecibo.php',
  	// 			// title:'Detalle Venta',
  	// 			// height:'50%',
    //       // width: 500,
  	// 			// cls:'DetalleVentaRecibo'
    // },

    onButtonNew: function(){

        Phx.vista.VentaFacturacionExportacionFormasPago.superclass.onButtonNew.call(this);
        this.window.items.items[0].body.dom.style.background = 'radial-gradient(ellipse at center, #cfe7fa 0%,#6393c1 100%)';


        this.Cmp.id_medio_pago.on('select',function(c,r,i) {
          this.Cmp.fop_code.setValue(r.data.fop_code);
          if (r.data.fop_code == 'CC' ){
            this.mostrarComponente(this.Cmp.num_tarjeta);
            this.mostrarComponente(this.Cmp.codigo_tarjeta);
            this.ocultarComponente(this.Cmp.id_auxiliar);
            this.ocultarComponente(this.Cmp.mco);
              this.Cmp.id_auxiliar.allowBlank = false;
            this.Cmp.codigo_tarjeta.allowBlank = false;
            this.Cmp.num_tarjeta.allowBlank = false;
            this.Cmp.mco.allowBlank = false;
            this.Cmp.id_auxiliar.reset();
            this.Cmp.mco.reset();

          }else if (r.data.fop_code == 'CA') {

            this.ocultarComponente(this.Cmp.num_tarjeta);
            this.ocultarComponente(this.Cmp.codigo_tarjeta);
            this.ocultarComponente(this.Cmp.id_auxiliar);
            this.ocultarComponente(this.Cmp.mco);
            this.Cmp.codigo_tarjeta.allowBlank = false;
            this.Cmp.num_tarjeta.allowBlank = false;
            this.Cmp.id_auxiliar.allowBlank = false;
            this.Cmp.mco.allowBlank = false;
            this.Cmp.codigo_tarjeta.reset();
            this.Cmp.mco.reset();
            this.Cmp.num_tarjeta.reset();
            this.Cmp.id_auxiliar.reset();

          }else if(r.data.fop_code == 'CU' || r.data.fop_code == 'CT'){

            this.mostrarComponente(this.Cmp.id_auxiliar);
            this.Cmp.num_tarjeta.allowBlank = true;
            this.Cmp.codigo_tarjeta.allowBlank = true;
            this.Cmp.mco.allowBlank = true;
            this.Cmp.id_auxiliar.allowBlank = false;
            this.ocultarComponente(this.Cmp.mco);
            this.ocultarComponente(this.Cmp.codigo_tarjeta);
            this.ocultarComponente(this.Cmp.num_tarjeta);
            this.Cmp.codigo_tarjeta.reset();
            this.Cmp.id_auxiliar.reset();
            this.Cmp.mco.reset();
            this.Cmp.num_tarjeta.reset();

          }else if(r.data.fop_code == 'MCO'){
            this.mostrarComponente(this.Cmp.mco);
            this.Cmp.num_tarjeta.allowBlank = true;
            this.Cmp.codigo_tarjeta.allowBlank = true;
            this.Cmp.mco.allowBlank = false;
            this.ocultarComponente(this.Cmp.num_tarjeta);
            this.ocultarComponente(this.Cmp.codigo_tarjeta);
            this.ocultarComponente(this.Cmp.id_auxiliar);
            this.Cmp.codigo_tarjeta.reset();
            this.Cmp.id_auxiliar.reset();
            this.Cmp.num_tarjeta.reset();

           }
        },this);
    },



    despliegueCampos:function() {
      var data=this.sm.getSelected().data;

      if (data.fop_code == 'CC' ){

        this.mostrarComponente(this.Cmp.num_tarjeta);
        this.mostrarComponente(this.Cmp.codigo_tarjeta);
        this.ocultarComponente(this.Cmp.id_auxiliar);
        this.ocultarComponente(this.Cmp.mco);

      }else if (data.fop_code == 'CA') {

        this.ocultarComponente(this.Cmp.num_tarjeta);
        this.ocultarComponente(this.Cmp.codigo_tarjeta);
        this.ocultarComponente(this.Cmp.id_auxiliar);
        this.ocultarComponente(this.Cmp.mco);

      }else if(data.fop_code == 'CU' || data.fop_code == 'CT'){

        this.mostrarComponente(this.Cmp.id_auxiliar);
        this.ocultarComponente(this.Cmp.mco);
        this.ocultarComponente(this.Cmp.codigo_tarjeta);
        this.ocultarComponente(this.Cmp.num_tarjeta);

      }else if(data.fop_code == 'MCO'){
        this.mostrarComponente(this.Cmp.mco);
        this.ocultarComponente(this.Cmp.num_tarjeta);
        this.ocultarComponente(this.Cmp.codigo_tarjeta);
        this.ocultarComponente(this.Cmp.id_auxiliar);


      }
    }

})
</script>
