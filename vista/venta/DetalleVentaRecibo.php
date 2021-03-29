<script>
Phx.vista.DetalleVentaRecibo=Ext.extend(Phx.gridInterfaz,{
    constructor:function(config){
        this.maestro=config.maestro;
        //llama al constructor de la clase padre
        Phx.vista.DetalleVentaRecibo.superclass.constructor.call(this,config);
        this.grid.getTopToolbar().disable();
        this.grid.getBottomToolbar().disable();
        this.init();
    },

    Atributos:[
        {
            //configuracion del componente
            config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_venta_detalle'
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
                    name: 'id_producto'
            },
            type:'Field',
            form:true
        },
        {
            config:{
                name: 'nombre_producto',
                fieldLabel: 'Nombre Producto',
                allowBlank: false,
                anchor: '80%',
                gwidth: 250
            },
                type:'TextField',
                id_grupo:1,
                grid:true
        },
        {
            config:{
                name: 'cantidad',
                fieldLabel: 'Cantidad',
                allowBlank: false,
                allowDecimals: false,
                width: 100,
                gwidth: 100,
                maxLength:10,
                msgTarget: 'side'
            },
            type:'NumberField',
            id_grupo:1,
            grid:true
        },
        {
            config:{
                name: 'precio_unitario',
                currencyChar: ' ',
                fieldLabel: 'Prec. Unit.',
                allowBlank: false,
                allowDecimals: true,
                allowNegative: false,
                decimalPrecision: 2,
                renderer: function(value, p, record){
                    return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                },
                width:100,
                gwidth: 110,
                msgTarget: 'side'
            },
            type:'NumberField',
            id_grupo:1,
            grid:true
        },
        {
            config:{
                name: 'precio_total',
                fieldLabel: 'Prec. Total',
                currencyChar:' ',
                allowBlank: true,
                width: 100,
                gwidth: 120,
            },
            type:'MoneyField',
            id_grupo:1,
            grid:true,
        },
        {
            config:{
                name: 'descripcion',
                fieldLabel: 'Descripcion',
                allowBlank: false,
                anchor: '80%',
                gwidth: 250
            },
                type:'TextArea',
                id_grupo:1,
                grid:true
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
    title:'Detalle Venta',
    // ActSave:'../../sis_contabilidad/control/PlantillaCalculo/insertarPlantillaCalculo',
    // ActDel:'../../sis_contabilidad/control/PlantillaCalculo/eliminarPlantillaCalculo',
    ActList:'../../sis_ventas_facturacion/control/VentaDetalle/listarVentaDetalle',
    id_store:'id_venta_detalle',
    fields: [
      {name:'id_venta_detalle', type: 'numeric'},
      {name:'id_venta', type: 'numeric'},
      {name:'nombre_producto', type: 'string'},
      {name:'id_producto', type: 'numeric'},
      {name:'tipo', type: 'string'},
      {name:'descripcion', type: 'string'},
      {name:'requiere_descripcion', type: 'string'},
      {name:'estado_reg', type: 'string'},
      {name:'cantidad', type: 'numeric'},
      {name:'precio_unitario', type: 'numeric'},
      {name:'precio_total', type: 'numeric'},
      {name:'id_usuario_ai', type: 'numeric'},
      {name:'usuario_ai', type: 'string'},
      {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
      {name:'id_usuario_reg', type: 'numeric'},
      {name:'id_usuario_mod', type: 'numeric'},
      {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
      {name:'usr_reg', type: 'string'},
      {name:'usr_mod', type: 'string'},
      {name:'id_moneda_recibo',type:'numeric'},
      {name:'desc_moneda_recibo',type:'string'}

    ],
    sortInfo:{
        field: 'id_venta_detalle',
        direction: 'ASC'
    },
    bdel:false,
    bsave:false,
    bnew:false,
    bedit:false,
    loadValoresIniciales:function(){
        Phx.vista.DetalleVentaRecibo.superclass.loadValoresIniciales.call(this);
        this.Cmp.id_venta.setValue(this.maestro.id_venta);
    },
    onReloadPage:function(m){
        this.maestro=m;
        this.store.baseParams={id_venta:this.maestro.id_venta};
        this.load({params:{start:0, limit:this.tam_pag}});
    },
    east: {
        url:'../../../sis_ventas_facturacion/vista/venta/DetalleVentaFormaPagoRecibo.php',
        title:'Detalle Forma Pago',
        height:'50%',
         width: 560,
        cls:'DetalleVentaFormaPagoRecibo'
    },
})
</script>
