<?php
/**
*@package pXP
*@file gen-SucursalMoneda_v2.php
*@author  (IsmaelValdivia)
*@date 10-09-2019 10:10:10
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.SucursalMoneda_v2=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){

		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.SucursalMoneda_v2.superclass.constructor.call(this,config);
		this.init();
		this.store.baseParams.id_sucursal = this.maestro.id_sucursal;
    /*Fondo color tbar (IRVA)*/
    this.bbar.el.dom.style.background='#77C5BB';
    this.tbar.el.dom.style.background='#77C5BB';
    this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#E4F9F6';
    this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#B3DCD7';
    /************************/
		this.load({params:{start:0, limit:this.tam_pag}})
	},

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_sucursal_moneda'
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
		// {
    //         config:{
    //             name:'id_moneda',
    //             origen:'MONEDA',
    //              allowBlank:false,
    //             fieldLabel:'Moneda',
    //             gdisplayField:'desc_moneda',//mapea al store del grid
    //             gwidth:50,
    //              renderer:function (value, p, record){return String.format('{0}', record.data['desc_moneda']);}
    //          },
    //         type:'ComboRec',
    //         id_grupo:1,
    //         filters:{
    //             pfiltro:'mon.codigo',
    //             type:'string'
    //         },
    //         grid:true,
    //         form:true
    //       },
    {
				config:{
					name: 'id_moneda',
					fieldLabel: 'Moneda',
					allowBlank: false,
					emptyText:'Moneda...',
					store:new Ext.data.JsonStore(
					{
						url: '../../sis_parametros/control/Moneda/listarMoneda',
						id: 'id_moneda',
						root: 'datos',
						sortInfo:{
							field: 'moneda',
							direction: 'ASC'
						},
						totalProperty: 'total',
						fields: ['id_moneda','moneda','codigo','codigo_internacional'],
						// turn on remote sorting
						remoteSort: true,
						baseParams:{par_filtro:'moneda.codigo_internacional#moneda.moneda',tipo:'listar_todo'}
					}),
					valueField: 'id_moneda',
					displayField: 'moneda',
					gdisplayField:'moneda',
					hiddenName: 'id_moneda',
					tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{moneda}</b></p><b><p>Codigo:<font color="green">{codigo_internacional}</font></b></p></div></tpl>',
						triggerAction: 'all',
						lazyRender:true,
					mode:'remote',
					pageSize:50,
					queryDelay:500,
					anchor:"100%",
					gwidth:150,
					minChars:2,
					renderer:function (value, p, record){return String.format('{0}', record.data['desc_moneda']);}
				},
				type:'ComboBox',
				filters:{pfiltro:'moneda.codigo_internacional',type:'string'},
				id_grupo:0,
				grid:true,
				form:true
			},
          {
            config:{
                name: 'tipo_moneda',
                fieldLabel: 'Tipo de Moneda',
                allowBlank: false,
                gwidth: 130,
                maxLength:15,
                anchor:"100%",
                emptyText:'tipo...',
                typeAhead: true,
                triggerAction: 'all',
                lazyRender:true,
                mode: 'local',
               // displayField: 'descestilo',
                store:['moneda_base','moneda_auxiliar']
            },
            type:'ComboBox',
            //filters:{pfiltro:'promac.inicio',type:'string'},
            id_grupo:0,
            filters:{
                         type: 'list',
                         pfiltro:'sucmon.tipo_moneda',
                         options: ['moneda_base','moneda_auxiliar']
                    },
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
				filters:{pfiltro:'sucmon.estado_reg',type:'string'},
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
				filters:{pfiltro:'sucmon.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'sucmon.fecha_reg',type:'date'},
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
				filters:{pfiltro:'sucmon.usuario_ai',type:'string'},
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
				filters:{pfiltro:'sucmon.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
  fheight:200,
  fwidth:350,
	title:'Monedas por sucursal',
	ActSave:'../../sis_ventas_facturacion/control/SucursalMoneda/insertarSucursalMoneda',
	ActDel:'../../sis_ventas_facturacion/control/SucursalMoneda/eliminarSucursalMoneda',
	ActList:'../../sis_ventas_facturacion/control/SucursalMoneda/listarSucursalMoneda',
	id_store:'id_sucursal_moneda',
	fields: [
		{name:'id_sucursal_moneda', type: 'numeric'},
		{name:'id_moneda', type: 'numeric'},
		{name:'id_sucursal', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'desc_moneda', type: 'string'},
		{name:'tipo_moneda', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},

	],
	sortInfo:{
		field: 'id_sucursal_moneda',
		direction: 'ASC'
	},
  onButtonNew: function (){
		Phx.vista.SucursalMoneda_v2.superclass.onButtonNew.call(this);
		this.form.el.dom.firstChild.childNodes[0].style.background = '#77C5BB';
	},

	onButtonEdit: function (){
		Phx.vista.SucursalMoneda_v2.superclass.onButtonEdit.call(this);
		this.form.el.dom.firstChild.childNodes[0].style.background = '#77C5BB';
	},
	loadValoresIniciales:function()
    {
        this.Cmp.id_sucursal.setValue(this.maestro.id_sucursal);
        Phx.vista.SucursalMoneda_v2.superclass.loadValoresIniciales.call(this);
    },
	bdel:true,
	bsave:true,

	}
)
</script>
