<?php
/**
*@package pXP
*@file gen-AperturaCierreCajaAsociada.php
*@author  (ivaldivia)
*@date 15-08-2019 13:15:22
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.AperturaCierreCajaAsociada=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		Ext.apply(this,config);
    	//llama al constructor de la clase padre
		Phx.vista.AperturaCierreCajaAsociada.superclass.constructor.call(this,config);
		this.init();
    this.iniciarEventos();
		this.bbar.el.dom.style.background='#a3c9f7';
		this.tbar.el.dom.style.background='#a3c9f7';
		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#DEFAF4';
		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#C7EAE3';
		//this.load({params:{start:0, limit:this.tam_pag}})
	},

  iniciarEventos : function () {

		this.addButton('asociar_pv',
				{   grupo:[2],
						text: 'Asociar P/V Diferentes',
						iconCls: 'bincremento_salarial',
						disabled: true,
						handler: this.asociar_diferentes,
						tooltip: '<b>Asociar Puntos de venta</b><br/>Diferentes'
				}
		);

    // var total = 0;
        this.Cmp.id_apertura_cierre_caja.on('change',function(field,newValue,oldValue){
          Ext.Ajax.request({
              url:'../../sis_ventas_facturacion/control/AperturaCierreCajaAsociada/getSumaTotal',
              params:{id_apertura:newValue, id_moneda_deposito:this.maestro.id_moneda_deposito, id_deposito:this.maestro.id_deposito, monto_deposito:this.maestro.monto_deposito},
              success: function(resp){
                  var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
									this.store.baseParams.suma_total = reg.ROOT.datos.v_suma_total;
                  this.store.baseParams.restante = reg.ROOT.datos.v_diferencia;
                  this.Cmp.total.setValue(parseFloat(this.store.baseParams.suma_total));
									console.log("el monto es",this.maestro.monto_deposito);
                  this.Cmp.diferencia.setValue(parseFloat(this.store.baseParams.restante));

									if (this.Cmp.diferencia.getValue() != 0) {
										this.Cmp.diferencia.el.dom.style.color = 'red';
										this.Cmp.diferencia.el.dom.style.background = '#FFAF9E';
										this.Cmp.diferencia.el.dom.style.fontWeight='bold';
									} else {
										this.Cmp.diferencia.el.dom.style.color = '#007350';
	                  this.Cmp.diferencia.el.dom.style.background = '#ACFF90';
	                  this.Cmp.diferencia.el.dom.style.fontWeight='bold';
									}
              },
              failure: this.conexionFailure,
              timeout:this.timeout,
              scope:this
          });
          /***********************************************************************************/

        },this);

  },



	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_apertura_asociada'
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
            // maxLength:-5
        },
        type:'TextField',
        filters:{pfiltro:'cdo.cajero',type:'string'},
        id_grupo:1,
        grid:true,
        form:false,
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
								if(record.data['nombre_punto_venta'] != ''){
                	return '<tpl for="."><div class="x-combo-list-item"><p><b>Punto de venta: </b> <font color="#006400"><b>'+record.data['nombre_punto_venta']+'</b></font></p><p><b>Codigo: </b><font color="#dc143c"><b>'+record.data['codigo']+'</b></font></p> <p><b>Estacion: </b><font color="#191970"><b>'+record.data['estacion']+'</b></font></p></div></tpl>';
								}
								else {
									return String.format('<b style="font-size:14px; font-weight:bold; color:blue; ">{0}</b>', ' ');
								}
            }
        },
        type:'TextField',
        filters:{pfiltro:'cdo.nombre',type:'string'},
        id_grupo:1,
        grid:true,
        form:false,
				bottom_filter:true
    },
    {
        config:{
            name: 'fecha_venta',
            fieldLabel: 'Fecha Venta',
            allowBlank: true,
            anchor: '80%',
            gwidth: 100,
            format: 'd/m/Y',
						renderer:function (value,p,record){
								if(record.data.tipo_reg != 'summary'){
										return value?value.dateFormat('d/m/Y'):''
								}
								else{
										return '<b><p style="font-size:15px; color:red; font-weight:bold; text-align:right;">Venta Total: &nbsp;&nbsp; </p></b>';
								}
						},
          //  renderer:function (value,p,record){}
        },
        type:'DateField',
        filters:{pfiltro:'cdo.fecha_venta',type:'date'},
        id_grupo:1,
        grid:true,
        form:false
    },
    {
			config:{
				name: 'arqueo_moneda_local',
				fieldLabel: 'Importe M/L.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650,
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div style="color:blue; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
					}

					else{
						return  String.format('<div style="font-size:15px; text-align:right; color:blue;"><b>{0}<b></div>', Ext.util.Format.number(record.data.venta_total_ml,'0,000.00'));
					}
				}

			},
				type:'NumberField',
				filters:{pfiltro:'cdo.arqueo_moneda_local',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:false
		},
        {
			config:{
				name: 'arqueo_moneda_extranjera',
				fieldLabel: 'Importe M/E.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650,
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div style="color:green; text-align:right; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0,000.00'));
					}

					else{
						return  String.format('<div style="font-size:15px; text-align:right; color:green;"><b>{0}<b></div>', Ext.util.Format.number(record.data.venta_total_me,'0,000.00'));
					}
				}
			},
				type:'NumberField',
				filters:{pfiltro:'cdo.arqueo_moneda_extranjera',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:false
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
            form:false
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
        },
		{
			config: {
				name: 'id_apertura_cierre_caja',
				fieldLabel: 'Asociar Ventas',
				allowBlank: true,
        enableMultiSelect: true,
				emptyText: 'Elija la apertura...',
				store: new Ext.data.JsonStore({
					url: '../../sis_ventas_facturacion/control/Depositos/listarDepositos',
					id: 'id_apertura_cierre_caja',
					root: 'datos',
					sortInfo: {
						field: 'nombre_punto_venta',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_apertura_cierre_caja', 'nombre_punto_venta', 'cajero', 'fecha_venta', 'arqueo_moneda_local','deposito_bs','arqueo_moneda_extranjera','deposito_usd'],
					remoteSort: true,
					baseParams: {par_filtro: 'cdo.cajero#cdo.nombre',pes_estado:'pendiente', relacion_deposito:'venta_propia_agrupada'}
				}),
				valueField: 'id_apertura_cierre_caja',
				displayField: 'id_apertura_cierre_caja',
				gdisplayField: 'id_apertura_cierre_caja',
				hiddenName: 'id_apertura_cierre_caja',
				forceSelection: false,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '80%',
        listWidth:'450',
				gwidth: 150,
				minChars: 2,
        itemSelector: 'div.awesomecombo-5item',
        tpl: new Ext.XTemplate([
          '<tpl for=".">',
          '<div class="awesomecombo-5item {checked}">',
					'<p><b>Punto de Venta: {nombre_punto_venta}</b></p>',
					'<p><b>Cajero:</b> <span style="color: green;">{cajero}</span></p>',
          '<p><b>Fecha de venta:</b> <span style="color: red;">{fecha_venta}</span></p>',
          '<p><b>Importe M/L:</b> <span style="color: blue;">{arqueo_moneda_local}</span></p>',
          '<p><b>Deposito M/L:</b> <span style="color: blue;">{deposito_bs}</span></p>',
          '<p><b>Importe M/E:</b> <span style="color: green;">{arqueo_moneda_extranjera}</span></p>',
          '<p><b>Deposito M/E:</b> <span style="color: green;">{deposito_usd}</span></p>',
          '</div></tpl>'
        ]),
				listeners: {
						beforequery: function(qe){
						delete qe.combo.lastQuery;
					}
				},
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['nombre_punto_venta']);
				}
			},
			type: 'AwesomeCombo',
			id_grupo: 0,
			filters: {pfiltro: 'cdo.cajero#cdo.nombre',type: 'string'},
			grid: false,
			form: true
		},
    {
        config: {
            name: 'id_deposito',
            inputType:'hidden',
            fieldLabel: 'No Deposito',
            allowBlank: false,
            anchor: '80%',
            gwidth: 150,
            maxLength: 70
        },
        type: 'TextField',
        filters: {pfiltro: 'dep.nro_deposito', type: 'string'},
        id_grupo: 1,
        grid: false,
        form: true
    },
    {
        config: {
            name: 'total',
            fieldLabel: 'Total',
            allowBlank: false,
            anchor: '80%',
            gwidth: 150,
						readOnly:true,
            maxLength: 70
        },
        type: 'TextField',
        filters: {pfiltro: 'dep.nro_deposito', type: 'string'},
        id_grupo: 1,
        grid: false,
        form: true
    },
    {
        config: {
            name: 'diferencia',
            fieldLabel: 'Diferencia',
            allowBlank: false,
						anchor: '80%',
            gwidth: 150,
						readOnly:true,
						decimalPrecision:0,
            maxLength: 70
        },
        type: 'NumberField',
        filters: {pfiltro: 'dep.nro_deposito', type: 'string'},
        id_grupo: 1,
        grid: false,
        form: true
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
				filters:{pfiltro:'acca.estado_reg',type:'string'},
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
				filters:{pfiltro:'acca.fecha_reg',type:'date'},
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
				filters:{pfiltro:'acca.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'acca.usuario_ai',type:'string'},
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
				filters:{pfiltro:'acca.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	title:'CajaAsociada',
  fheight:'30%',
  fwidth:'30%',
	ActSave:'../../sis_ventas_facturacion/control/AperturaCierreCajaAsociada/insertarAperturaCierreCajaAsociada',
	ActDel:'../../sis_ventas_facturacion/control/AperturaCierreCajaAsociada/eliminarAperturaCierreCajaAsociada',
	ActList:'../../sis_ventas_facturacion/control/AperturaCierreCajaAsociada/listarAperturaCierreCajaAsociada',
	id_store:'id_apertura_asociada',
	fields: [
		{name:'id_apertura_asociada', type: 'numeric'},
    {name:'cajero', type: 'string'},
    {name:'codigo', type: 'string'},
    {name:'nombre_punto_venta', type: 'string'},
    {name:'estacion', type: 'string'},
    {name:'fecha_venta', type: 'date',dateFormat:'Y-m-d'},
    {name:'fecha_recojo', type: 'date',dateFormat:'Y-m-d'},
    {name:'arqueo_moneda_local', type: 'numeric'},
    {name:'arqueo_moneda_extranjera', type: 'numeric'},
    {name:'deposito_bs', type: 'numeric'},
    {name:'deposito_usd', type: 'numeric'},
    {name:'diferencia_bs', type: 'numeric'},
    {name:'diferencia_usd', type: 'numeric'},
		{name:'id_apertura_cierre_caja', type: 'numeric'},
		{name:'id_deposito', type: 'numeric'},
    {name:'estado_reg', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'venta_total_ml', type: 'numeric'},
		{name:'venta_total_me', type: 'numeric'},
		{name:'tipo_reg', type: 'string'},

	],
	sortInfo:{
		field: 'id_apertura_asociada',
		direction: 'ASC'
	},
	bdel:true,
	bsave:false,
	bedit:false,

  onButtonNew: function (m) {
    Phx.vista.AperturaCierreCajaAsociada.superclass.onButtonNew.call(this);
    this.Cmp.id_deposito.setValue(this.maestro.id_deposito);
		this.form.el.dom.firstChild.childNodes[0].style.background = '#a3c9f7';
		this.Cmp.id_punto_venta.setValue(Phx.CP.getPagina(this.idContenedorPadre).id_punto_venta);
		/*CONDICION PARA QUE APLIQUE EL FILTRO*/
				if (m == 'SI') {
					this.Cmp.id_apertura_cierre_caja.store.baseParams.id_punto_venta = '';
				} else {
					this.Cmp.id_apertura_cierre_caja.store.baseParams.id_punto_venta = Phx.CP.getPagina(this.idContenedorPadre).id_punto_venta;
				}
		/******************************************/
  },

	asociar_diferentes : function() {
		var aso = 'SI';
		this.onButtonNew(aso);
	},

  onReloadPage: function (m) {
      this.maestro = m;
      if (this.maestro.id_moneda_deposito != 2) {
				console.log('this.cm',this.cm);
        this.cm.setHidden(5, true);
        this.cm.setHidden(8, true);
        this.cm.setHidden(4, false);
				this.cm.setHidden(7, false);
				this.cm.setHidden(9, true);
        this.cm.setHidden(10, true);
      } else {
        this.cm.setHidden(5, false);
        this.cm.setHidden(8, false);
        this.cm.setHidden(7, true);
        this.cm.setHidden(4, true);
				this.cm.setHidden(9, true);
        this.cm.setHidden(10, true);
      }

			this.Cmp.id_apertura_cierre_caja.store.baseParams.id_moneda_deposito_agrupado = this.maestro.id_moneda_deposito;
      //this.Cmp.id_apertura_cierre_caja.store.baseParams.id_punto_venta = Phx.CP.getPagina(this.idContenedorPadre).id_punto_venta;
      this.store.baseParams = {id_deposito: this.maestro.id_deposito};
      this.load({params: {start: 0, limit: 50}});
  },
	successSave:function(resp){
			Phx.vista.AperturaCierreCajaAsociada.superclass.successSave.call(this,resp);
			Phx.CP.getPagina(this.idContenedorPadre).reload();
	},
	successEdit:function(resp){
			Phx.vista.AperturaCierreCajaAsociada.superclass.successEdit.call(this,resp);
			Phx.CP.getPagina(this.idContenedorPadre).reload();
	},
	successDel:function(resp){
			Phx.vista.AperturaCierreCajaAsociada.superclass.successDel.call(this,resp);
			Phx.CP.getPagina(this.idContenedorPadre).reload();
	},

	}
)
</script>
