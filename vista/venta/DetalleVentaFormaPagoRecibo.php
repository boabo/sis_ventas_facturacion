<script>
Phx.vista.DetalleVentaFormaPagoRecibo=Ext.extend(Phx.gridInterfaz,{

    constructor:function(config){
        this.maestro=config.maestro;
        //llama al constructor de la clase padre
        Phx.vista.DetalleVentaFormaPagoRecibo.superclass.constructor.call(this,config);
        this.grid.getTopToolbar().disable();
        this.grid.getBottomToolbar().disable();
        this.instanciasPagoAnticipo = 'no';
        this.init();
        this.iniciarEventos();
    },

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
                widt:250
            },
            type: 'ComboRec',
            id_grupo: 1,
            grid: true,
            form: true
        },
        {
            config: {
                name: 'id_medio_pago_pw',
                fieldLabel: 'Medio de pago',
                allowBlank: false,
                width:250,
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
                    baseParams: {par_filtro: 'mppw.name#fp.fop_code', emision:'RO'}
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
                listWidth:250,
                resizable:true,
                minChars: 2,
                renderer : function(value, p, record) {
                    return String.format('{0}', record.data['name']);
                }
            },
            type: 'ComboBox',
            id_grupo: 1,
            grid: true,
            form: true
        },
        {
            config:{
                name: 'importe_recibido',
                fieldLabel: 'Importe Recibido',
                allowBlank: true,
                width:250,
                maxLength:20,
                renderer : function(value, p, record) {
                    return String.format('{0}', record.data['monto_forma_pago']);
                }
            },
                type:'NumberField',
                id_grupo:1,
                grid:true,
                form:false
        },
        {
            config:{
                name: 'numero_tarjeta',
                fieldLabel: 'N° Tarjeta',
                allowBlank: true,
                width:250,
                gwidth:100,
                maxLength:20,
                minLength:15
            },
            type:'TextField',
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'codigo_tarjeta',
                fieldLabel: 'Codigo de Autorización',
                allowBlank: true,
                width:250,
                minLength:6,
                maxLength:6,
                style:'text-transform:uppercase;',
                maskRe: /[a-zA-Z0-9]+/i,
                regex: /[a-zA-Z0-9]+/i

            },
                type:'TextField',
                id_grupo:1,
                form:true
        },
        {
    			config: {
    				name: 'id_auxiliar_anticipo',
    				fieldLabel: '<img src="../../../lib/imagenes/facturacion/CuentaCorriente.svg" style="width:15px; vertical-align: middle;"><span style="vertical-align: middle;"> Cuenta Corriente</span>',
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
    			id_grupo: 1,
    			grid: false,
    			form: true
    		},
        {
            config:{
                name: 'nro_deposito',
                fieldLabel: 'Nro Depósito',
                allowBlank: true,
                gwidth:150,
                width:150,
                maskRe: /[a-zA-Z0-9]+/i,
                regex: /[a-zA-Z0-9]+/i
            },
                type:'TextField',
                id_grupo:1,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'fecha_deposito',
                fieldLabel: 'Fecha Deposito',
                allowBlank: true,
                disabled:false,
                width:150,
                gwidth:150,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
                type:'DateField',
                id_grupo:1,
                form:true,
                grid:true
        },
        {
            config:{
                name: 'id_cuenta_bancaria',
                fieldLabel: 'id_cuenta_bancaria',
                allowBlank: true,
                hidden:true,
                width:150,
                maxLength:20,
                allowNegative:false
            },
                type:'NumberField',
                id_grupo:1,
                form:true
        },
        {
            config:{
                name: 'monto_deposito',
                fieldLabel: 'Monto Depósito',
                allowBlank: true,
                enableKeyEvents: true,
                width:150,
                gwidth:150,
                maxLength:20,
                allowNegative:false,
                value:0
            },
                type:'NumberField',
                id_grupo:1,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'cuenta_bancaria',
                fieldLabel: 'Nro Cuenta',
                allowBlank: true,
                width:150,
                gwidth:150,
                maxLength:20,
                allowNegative:false,
                disabled:true
            },
                type:'NumberField',
                id_grupo:11,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'mco',
                fieldLabel: 'MCO',
                allowBlank: false,
                width:250,
                gwidth: 150,
                minLength:15,
                maxLength:20
            },
            type:'TextField',
            id_grupo:1,
            grid:true,
            form:true
        },
        {
    			config: {
    				name: 'id_auxiliar',
    				fieldLabel: 'Cuenta Corriente',
    				allowBlank: true,
            width:250,
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
    			id_grupo: 1,
    			grid: true,
    			form: true
    		},
        {
            config:{
                name: 'monto_forma_pago',
                fieldLabel: 'Importe Recibido',
                allowBlank: false,
                width:250,
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
    title:'Detalle Forma Pago',
    // ActSave:'../../sis_ventas_facturacion/control/Cajero/correccionInstanciaPago',
    // ActDel:'../../sis_contabilidad/control/PlantillaCalculo/eliminarPlantillaCalculo',
    ActList:'../../sis_ventas_facturacion/control/Cajero/listarInstanciaPagoCorreccion',
    id_store:'id_venta_forma_pago',
    fields: [
      {name: 'id_medio_pago_pw',type: 'numeric'},
      {name: 'id_venta', type:'id_venta'},
      {name: 'id_moneda',     type: 'numeric'},
      {name: 'id_venta_forma_pago',type: 'numeric'},
      {name: 'nombre',      type: 'string'},
      {name: 'codigo_tarjeta',     type: 'string'},
      {name: 'numero_tarjeta',     type: 'string'},
      {name: 'monto_forma_pago',     type: 'numeric'},
      {name:'desc_moneda',type:'string'},
      {name: 'name',      type: 'string'},
      {name: 'fop_code', type:'string'},
      {name: 'id_auxiliar', type:'int4'},
      {name: 'nombre_auxiliar', type:'string'},
      {name: 'codigo_auxiliar', type:'string'},
      {name: 'fecha_deposito', type:'date'},
      {name: 'nro_deposito', type:'string'},
      {name: 'monto_deposito', type:'numeric'},
      {name:'mco',type:'string'}


    ],
    sortInfo:{
        field: 'id_venta_forma_pago',
        direction: 'ASC'
    },
    bdel:false,
    bsave:false,
    bnew:false,
    bedit:false,
    loadValoresIniciales:function(){
        Phx.vista.DetalleVentaFormaPagoRecibo.superclass.loadValoresIniciales.call(this);
        this.Cmp.id_venta.setValue(this.maestro.id_venta);
    },
    onReloadPage:function(m){
        this.maestro=m;
        this.store.baseParams={id_venta:this.maestro.id_venta};
        this.load({params:{start:0, limit:this.tam_pag}});
    },
    iniciarEventos:function(){

      this.Cmp.codigo_tarjeta.setVisible(false);
      this.Cmp.numero_tarjeta.setVisible(false);
      this.Cmp.id_auxiliar.setVisible(false);
      this.Cmp.mco.setVisible(false);
      this.Cmp.id_auxiliar_anticipo.setVisible(false);
      this.Cmp.nro_deposito.setVisible(false);
      this.Cmp.fecha_deposito.setVisible(false);
      this.Cmp.cuenta_bancaria.setVisible(false);
      this.Cmp.monto_deposito.setVisible(false);
      this.Cmp.id_medio_pago_pw.store.baseParams.regional = 'BOL';
      this.Cmp.id_medio_pago_pw.store.baseParams.defecto = 'no';
      this.Cmp.id_medio_pago_pw.store.baseParams.filtrar_base = 'si';
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
    onButtonEdit: function(){

        Phx.vista.DetalleVentaFormaPagoRecibo.superclass.onButtonEdit.call(this);
        this.despliegueCampos();
        this.Cmp.id_medio_pago_pw.on('select',function(c,r,i) {
          this.Cmp.fop_code.setValue(r.data.fop_code);
          if (r.data.fop_code == 'CC' ){
            this.mostrarComponente(this.Cmp.numero_tarjeta);
            this.mostrarComponente(this.Cmp.codigo_tarjeta);
            this.ocultarComponente(this.Cmp.id_auxiliar);
            this.ocultarComponente(this.Cmp.mco);
            this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
            this.ocultarComponente(this.Cmp.nro_deposito);
            this.ocultarComponente(this.Cmp.fecha_deposito);
            this.ocultarComponente(this.Cmp.monto_deposito);
            this.ocultarComponente(this.Cmp.cuenta_bancaria);
            this.Cmp.id_auxiliar_anticipo.allowBlank = false;
            this.Cmp.id_auxiliar.allowBlank = false;
            this.Cmp.codigo_tarjeta.allowBlank = false;
            this.Cmp.numero_tarjeta.allowBlank = false;
            this.Cmp.mco.allowBlank = false;
            this.Cmp.nro_deposito.allowBlank = false;
            this.Cmp.fecha_deposito.allowBlank = false;
            this.Cmp.monto_deposito.allowBlank = false;
            this.Cmp.cuenta_bancaria.allowBlank = false;
            this.Cmp.id_auxiliar.reset();
            this.Cmp.id_auxiliar_anticipo.reset();
            this.Cmp.mco.reset();
            this.Cmp.nro_deposito.reset();
            this.Cmp.fecha_deposito.reset();
            this.Cmp.monto_deposito.reset();
            this.Cmp.cuenta_bancaria.reset();

          }else if (r.data.fop_code == 'CA') {

            this.ocultarComponente(this.Cmp.numero_tarjeta);
            this.ocultarComponente(this.Cmp.codigo_tarjeta);
            this.ocultarComponente(this.Cmp.id_auxiliar);
            this.ocultarComponente(this.Cmp.mco);
            this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
            this.ocultarComponente(this.Cmp.nro_deposito);
            this.ocultarComponente(this.Cmp.fecha_deposito);
            this.ocultarComponente(this.Cmp.monto_deposito);
            this.ocultarComponente(this.Cmp.cuenta_bancaria);
            this.Cmp.id_auxiliar_anticipo.allowBlank = false;
            this.Cmp.codigo_tarjeta.allowBlank = false;
            this.Cmp.numero_tarjeta.allowBlank = false;
            this.Cmp.id_auxiliar.allowBlank = false;
            this.Cmp.mco.allowBlank = false;
            this.Cmp.nro_deposito.allowBlank = false;
            this.Cmp.fecha_deposito.allowBlank = false;
            this.Cmp.monto_deposito.allowBlank = false;
            this.Cmp.cuenta_bancaria.allowBlank = false;
            this.Cmp.codigo_tarjeta.reset();
            this.Cmp.id_auxiliar_anticipo.reset();
            this.Cmp.mco.reset();
            this.Cmp.numero_tarjeta.reset();
            this.Cmp.id_auxiliar.reset();

            this.Cmp.nro_deposito.reset();
            this.Cmp.fecha_deposito.reset();
            this.Cmp.monto_deposito.reset();
            this.Cmp.cuenta_bancaria.reset();

          }else if(r.data.fop_code == 'CU' || r.data.fop_code == 'CT'){

            this.mostrarComponente(this.Cmp.id_auxiliar);
            this.Cmp.numero_tarjeta.allowBlank = true;
            this.Cmp.codigo_tarjeta.allowBlank = true;
            this.Cmp.mco.allowBlank = true;
            this.Cmp.id_auxiliar.allowBlank = false;
            this.ocultarComponente(this.Cmp.mco);
            this.ocultarComponente(this.Cmp.codigo_tarjeta);
            this.ocultarComponente(this.Cmp.numero_tarjeta);
            this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
            this.ocultarComponente(this.Cmp.nro_deposito);
            this.ocultarComponente(this.Cmp.fecha_deposito);
            this.ocultarComponente(this.Cmp.monto_deposito);
            this.ocultarComponente(this.Cmp.cuenta_bancaria);
            this.Cmp.id_auxiliar_anticipo.allowBlank = false;
            this.Cmp.nro_deposito.allowBlank = false;
            this.Cmp.fecha_deposito.allowBlank = false;
            this.Cmp.monto_deposito.allowBlank = false;
            this.Cmp.cuenta_bancaria.allowBlank = false;
            this.Cmp.codigo_tarjeta.reset();
            this.Cmp.id_auxiliar.reset();
            this.Cmp.id_auxiliar_anticipo.reset();
            this.Cmp.mco.reset();
            this.Cmp.numero_tarjeta.reset();
            this.Cmp.nro_deposito.reset();
            this.Cmp.fecha_deposito.reset();
            this.Cmp.monto_deposito.reset();
            this.Cmp.cuenta_bancaria.reset();

          }else if(r.data.fop_code == 'MCO'){
            this.mostrarComponente(this.Cmp.mco);
            this.Cmp.numero_tarjeta.allowBlank = true;
            this.Cmp.codigo_tarjeta.allowBlank = true;
            this.Cmp.mco.allowBlank = false;
            this.ocultarComponente(this.Cmp.numero_tarjeta);
            this.ocultarComponente(this.Cmp.codigo_tarjeta);
            this.ocultarComponente(this.Cmp.id_auxiliar);
            this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
            this.ocultarComponente(this.Cmp.nro_deposito);
            this.ocultarComponente(this.Cmp.fecha_deposito);
            this.ocultarComponente(this.Cmp.monto_deposito);
            this.ocultarComponente(this.Cmp.cuenta_bancaria);
            this.Cmp.id_auxiliar_anticipo.allowBlank = false;
            this.Cmp.nro_deposito.allowBlank = false;
            this.Cmp.fecha_deposito.allowBlank = false;
            this.Cmp.monto_deposito.allowBlank = false;
            this.Cmp.cuenta_bancaria.allowBlank = false;
            this.Cmp.codigo_tarjeta.reset();
            this.Cmp.id_auxiliar_anticipo.reset();
            this.Cmp.id_auxiliar.reset();
            this.Cmp.numero_tarjeta.reset();
            this.Cmp.nro_deposito.reset();
            this.Cmp.fecha_deposito.reset();
            this.Cmp.monto_deposito.reset();
            this.Cmp.cuenta_bancaria.reset();

          }else if(r.data.fop_code == 'DEPO'){

            if (this.instanciasPagoAnticipo == 'si') {
              this.mostrarComponente(this.Cmp.id_auxiliar_anticipo);
              this.Cmp.id_auxiliar_anticipo.allowBlank = false;
            }else{
              this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
              this.Cmp.id_auxiliar_anticipo.allowBlank = false;
              this.Cmp.id_auxiliar_anticipo.reset();
            }
            this.ocultarComponente(this.Cmp.mco);
            this.ocultarComponente(this.Cmp.codigo_tarjeta);
            this.ocultarComponente(this.Cmp.numero_tarjeta);
            this.Cmp.numero_tarjeta.allowBlank = true;
            this.Cmp.mco.reset();
            this.Cmp.codigo_tarjeta.reset();
            this.Cmp.numero_tarjeta.reset();

            this.mostrarComponente(this.Cmp.nro_deposito);
            this.mostrarComponente(this.Cmp.fecha_deposito);
            this.mostrarComponente(this.Cmp.monto_deposito);
            this.mostrarComponente(this.Cmp.cuenta_bancaria);
            this.Cmp.nro_deposito.allowBlank = false;
            this.Cmp.fecha_deposito.allowBlank = false;
            var fehca_ro = new Date();
            this.Cmp.fecha_deposito.setValue(fehca_ro);
            this.Cmp.monto_deposito.allowBlank = false;
            this.Cmp.cuenta_bancaria.allowBlank = false;
            // this.Cmp.cuenta_bancaria.setDisabled(true);


            this.Cmp.monto_deposito.on('keyup', function (cmp, e) {
               this.Cmp.monto_forma_pago.setValue(this.Cmp.monto_deposito.getValue());
             }, this);

           /***Aqui validamos para ver si existe la boleta***/
           var v_idv = this.maestro.id_punto_venta;
           var v_ids = this.maestro.id_sucursal;
           Ext.Ajax.request({
      				url:'../../sis_ventas_facturacion/control/VentaFacturacion/ObtenerCuentaBancaria',
      				params:{
      					id_punto_venta: v_idv,
      					id_sucursal:v_ids,
                id_moneda:this.Cmp.id_moneda.getValue()
      				},
      				success: function(resp){
      						var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
      						   this.Cmp.cuenta_bancaria.setValue(reg.ROOT.datos.nro_cuenta);
                     this.Cmp.id_cuenta_bancaria.setValue(reg.ROOT.datos.id_cuenta_bancaria);
      				},
      				failure: this.conexionFailure,
      				timeout:this.timeout,
      				scope:this
      		});

             /*Aqui verificaremos si el deposito existe o no existe ya registrado*/
            this.Cmp.nro_deposito.on('change',function(field,newValue,oldValue){
              Ext.Ajax.request({
       					url:'../../sis_ventas_facturacion/control/VentaFacturacion/verificarDeposito',
       					params:{
                   nro_deposito: newValue,
                   id_moneda:this.Cmp.id_moneda.getValue(),
       						fecha:this.Cmp.fecha_deposito.getValue(),
       					},
       					success: function(resp){
       							var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                        if (reg.ROOT.datos.cantidad_deposito > 0) {
                           Ext.Msg.show({
         											title: 'Alerta',
         											msg: '<p>El número de depósito <b>'+reg.ROOT.datos.nro_deposito+'</b>. ya se encuentra registrado favor contactarse con personal de ventas e ingrese nuevamente el nro de depósito</p>',
         											buttons: Ext.Msg.OK,
         											width: 512,
         											icon: Ext.Msg.INFO
         									});
                           this.Cmp.nro_deposito.reset();
                        }
       					},
       					failure: this.conexionFailure,
       					timeout:this.timeout,
       					scope:this
       			});
            },this);
          }

        },this);
    },

    despliegueCampos:function() {
      var data=this.sm.getSelected().data;

      if (data.fop_code == 'CC' ){

        this.mostrarComponente(this.Cmp.numero_tarjeta);
        this.mostrarComponente(this.Cmp.codigo_tarjeta);
        this.ocultarComponente(this.Cmp.id_auxiliar);
        this.ocultarComponente(this.Cmp.mco);
        this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
        this.ocultarComponente(this.Cmp.nro_deposito);
        this.ocultarComponente(this.Cmp.fecha_deposito);
        this.ocultarComponente(this.Cmp.monto_deposito);
        this.ocultarComponente(this.Cmp.cuenta_bancaria);

      }else if (data.fop_code == 'CA') {

        this.ocultarComponente(this.Cmp.numero_tarjeta);
        this.ocultarComponente(this.Cmp.codigo_tarjeta);
        this.ocultarComponente(this.Cmp.id_auxiliar);
        this.ocultarComponente(this.Cmp.mco);
        this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
        this.ocultarComponente(this.Cmp.nro_deposito);
        this.ocultarComponente(this.Cmp.fecha_deposito);
        this.ocultarComponente(this.Cmp.monto_deposito);
        this.ocultarComponente(this.Cmp.cuenta_bancaria);

      }else if(data.fop_code == 'CU' || data.fop_code == 'CT'){

        this.mostrarComponente(this.Cmp.id_auxiliar);
        this.ocultarComponente(this.Cmp.mco);
        this.ocultarComponente(this.Cmp.codigo_tarjeta);
        this.ocultarComponente(this.Cmp.numero_tarjeta);
        this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
        this.ocultarComponente(this.Cmp.nro_deposito);
        this.ocultarComponente(this.Cmp.fecha_deposito);
        this.ocultarComponente(this.Cmp.monto_deposito);
        this.ocultarComponente(this.Cmp.cuenta_bancaria);

      }else if(data.fop_code == 'MCO'){
        this.mostrarComponente(this.Cmp.mco);
        this.ocultarComponente(this.Cmp.numero_tarjeta);
        this.ocultarComponente(this.Cmp.codigo_tarjeta);
        this.ocultarComponente(this.Cmp.id_auxiliar);
        this.ocultarComponente(this.Cmp.id_auxiliar_anticipo);
        this.ocultarComponente(this.Cmp.nro_deposito);
        this.ocultarComponente(this.Cmp.fecha_deposito);
        this.ocultarComponente(this.Cmp.monto_deposito);
        this.ocultarComponente(this.Cmp.cuenta_bancaria);

      }else if(data.fop_code == 'DEPO'){
        this.ocultarComponente(this.Cmp.mco);
        this.ocultarComponente(this.Cmp.codigo_tarjeta);
        this.ocultarComponente(this.Cmp.numero_tarjeta);
        this.mostrarComponente(this.Cmp.nro_deposito);
        this.mostrarComponente(this.Cmp.fecha_deposito);
        this.mostrarComponente(this.Cmp.monto_deposito);
        this.mostrarComponente(this.Cmp.cuenta_bancaria);
        var v_idv = this.maestro.id_punto_venta;
        var v_ids = this.maestro.id_sucursal;
        Ext.Ajax.request({
           url:'../../sis_ventas_facturacion/control/VentaFacturacion/ObtenerCuentaBancaria',
           params:{
             id_punto_venta: v_idv,
             id_sucursal:v_ids,
             id_moneda:this.Cmp.id_moneda.getValue()
           },
           success: function(resp){
               var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                  this.Cmp.cuenta_bancaria.setValue(reg.ROOT.datos.nro_cuenta);
                  this.Cmp.id_cuenta_bancaria.setValue(reg.ROOT.datos.id_cuenta_bancaria);
           },
           failure: this.conexionFailure,
           timeout:this.timeout,
           scope:this
       });
      }
    }

})
</script>
