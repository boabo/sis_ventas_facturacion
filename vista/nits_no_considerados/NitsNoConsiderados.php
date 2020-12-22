<?php
/**
*@package pXP
*@file gen-NitsNoConsiderados.php
*@author  (maylee.perez)
*@date 21-12-2020 20:13:12
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.NitsNoConsiderados=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.NitsNoConsiderados.superclass.constructor.call(this,config);
		this.init();
		this.load({params:{start:0, limit:this.tam_pag}})
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_nits_no_considerados'
			},
			type:'Field',
			form:true 
		},

		{
			config:{
				name: 'nit_ci',
				fieldLabel: 'NIT/CI/CUIT',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:800
			},
				type:'TextField',
				filters:{pfiltro:'nitncons.nit_ci',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'razon_social',
				fieldLabel: 'Nombre o Razon Social',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:800
			},
				type:'TextField',
				filters:{pfiltro:'nitncons.razon_social',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},

        {
            config: {
                name: 't_contr',
                fieldLabel: 'T-Contr. N/S/G',
                allowBlank: true,
                emptyText: '...',
                store: new Ext.data.ArrayStore({
                        fields: ['valor', 'desc'],
                        data: [
                            ['N', 'Natural'],
                            ['S', 'Simplificado'],
                            ['G', 'General']
                        ]
                    }
                ),
                tpl: new Ext.XTemplate([
                    '<tpl for=".">',
                    '<div class="x-combo-list-item">',
                    '<div class="awesomecombo-item {checked}">',
                    '<p><b style="color: green;"> {valor} - {desc}</b></p>',
                    '</div>',
                    '</div></tpl>'
                ]),
                valueField: 'valor',
                displayField: 'desc',
                typeAhead: true,
                triggerAction: 'all',
                listWidth: '220',
                resizable: true,
                mode: 'local',
                selectOnFocus: true,
                anchor: '80%',
                msgTarget: 'side',
                editable: false
            },

            type: 'AwesomeCombo',
            id_grupo: 1,
            grid: true,
            form: true
        },


        {
            config: {
                name: 'incl_rep',
                fieldLabel: 'Incl-Rep. S/N',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:500,
                typeAhead: true,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'local',
                store: ['no', 'si']
            },
            type: 'ComboBox',
            filters: {
                pfiltro: 'nitncons.incl_rep',
                type: 'string'
            },
            //valorInicial: 'no',
            id_grupo: 1,
            grid: true,
            form: true
        },

		{
			config:{
				name: 'observaciones',
				fieldLabel: 'observaciones',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:800
			},
				type:'TextField',
				filters:{pfiltro:'nitncons.observaciones',type:'string'},
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
            filters:{pfiltro:'nitncons.estado_reg',type:'string'},
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
				filters:{pfiltro:'nitncons.fecha_reg',type:'date'},
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
				filters:{pfiltro:'nitncons.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'nitncons.usuario_ai',type:'string'},
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
				filters:{pfiltro:'nitncons.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'Nits No considerados',
	ActSave:'../../sis_ventas_facturacion/control/NitsNoConsiderados/insertarNitsNoConsiderados',
	ActDel:'../../sis_ventas_facturacion/control/NitsNoConsiderados/eliminarNitsNoConsiderados',
	ActList:'../../sis_ventas_facturacion/control/NitsNoConsiderados/listarNitsNoConsiderados',
	id_store:'id_nits_no_considerados',
	fields: [
		{name:'id_nits_no_considerados', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'nit_ci', type: 'string'},
		{name:'razon_social', type: 'string'},
		{name:'t_contr', type: 'string'},
		{name:'incl_rep', type: 'string'},
		{name:'observaciones', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_nits_no_considerados',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true
	}
)
</script>
		
		