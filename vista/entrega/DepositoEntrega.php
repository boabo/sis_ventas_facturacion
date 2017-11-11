<?php
/**
 *@package pXP
 *@file gen-Entrega.php
 *@author  (admin)
 *@date 12-09-2017 15:04:26
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>

     Phx.vista.DepositoEntrega=Ext.extend(Phx.gridInterfaz,{

             constructor:function(config){
                 this.maestro=config.maestro;
                 Phx.vista.DepositoEntrega.superclass.constructor.call(this,config);
                 this.init();

             },
             bdel : true,
             Atributos:[
                 {
                     //configuracion del componente
                     config:{
                         labelSeparator:'',
                         inputType:'hidden',
                         name: 'id_apertura_cierre_caja'
                     },
                     type:'Field',
                     form:true
                 },
                 {
                     //configuracion del componente
                     config: {
                         labelSeparator: '',
                         inputType: 'hidden',
                         name: 'id_punto_venta'
                     },
                     type: 'Field',
                     form: true
                 },
                 {
                     //configuracion del componente
                     config: {
                         labelSeparator: '',
                         inputType: 'hidden',
                         name: 'id_entrega_brinks'
                     },
                     type: 'Field',
                     form: true
                 },
                 {
                     config: {
                         name: 'fecha',
                         fieldLabel: 'Fecha Apertura Cierre',
                         allowBlank: true,
                         emptyText: 'Fecha...',
                         store: new Ext.data.JsonStore({
                             url: '../../sis_ventas_facturacion/control/Entrega/listarfechaApertura',
                             id: 'fecha',
                             root: 'datos',
                             sortInfo: {
                                 field: 'fecha_cierre',
                                 direction: 'ASC'
                             },

                             totalProperty: 'total',
                             fields: ['fecha_cierre','id_punto_venta'],
                             remoteSort: true,
                             baseParams: {id_punto_venta:'si'}
                         }),

                         valueField: 'fecha_cierre',
                         displayField: 'fecha_cierre',
                         gdisplayField: 'fecha_apertura_cierre',
                         hiddenName: 'fecha',
                         forceSelection: true,
                         typeAhead: false,
                         triggerAction: 'all',
                         lazyRender: true,
                         mode: 'remote',
                         pageSize: 15,
                         queryDelay: 1000,
                         format: 'd/m/Y',
                         gwidth: 150,
                         listWidth: 450,
                         resizable: true,
                         minChars: 2,
                         renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                     },
                     type: 'ComboBox',
                     filters: {pfiltro:'ap.fecha_apertura_cierre', type:'string'},
                     id_grupo: 1,
                     grid: true,
                     form: true
                 },
                /* {
                     config:{
                         name: 'fecha_apertura_cierre',
                         fieldLabel: 'Fecha ',
                         gwidth: 110,
                         format: 'd/m/Y',
                         renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                     },
                     type:'DateField',
                     filters: { pfiltro:'apcie.fecha', type:'date'},
                     grid:true,
                     form:false
                 },*/
                 {
                     config:{
                         name: 'nombre_punto_venta',
                         fieldLabel: 'Punto de venta',
                         gwidth: 200,
                         renderer: function(value, p, record) {
                             if(record.data.nombre_punto_venta == 'TOTAL'){
                                 return String.format('<div ext:qtip="Optimo"><b><font color="#006400">{0}</font></b><br></div>', value);
                             }else{
                                 return String.format('<div ext:qtip="Optimo"><b><font color="black">{0}</font></b><br></div>', value);
                             }

                         }
                     },
                     type:'TextField',
                     filters:{pfiltro:'pv.nombre',type:'string'},
                     grid:true,
                     form:false
                 },
                 {
                     config:{
                         name: 'obs_cierre',
                         fieldLabel: 'Obs. Cierre',
                         allowBlank: true,
                         anchor: '100%',
                         gwidth: 150
                     },
                     type:'TextArea',
                     filters:{pfiltro:'apcie.obs_cierre',type:'string'},
                     id_grupo:1,
                     grid:true,
                     form:false
                 },
                 {
                     config:{
                         name: 'arqueo_moneda_local',
                         fieldLabel: 'Arqueo Moneda Local',
                         allowBlank: false,
                         anchor: '80%',
                         gwidth: 150,
                         maxLength:4,
                         allowDecimals: true,
                         decimalPrecision : 2,
                         renderer: function(value, p, record) {
                             if(record.data.nombre_punto_venta == 'TOTAL'){
                                 return String.format('<div ext:qtip="Optimo"><b><font color="blue">{0}</font></b><br></div>', value);
                             }else{
                                 return String.format('<div ext:qtip="Optimo"><b><font color="black">{0}</font></b><br></div>', value);
                             }

                         }
                     },
                     type:'NumberField',
                     filters:{pfiltro:'apcie.arqueo_moneda_local',type:'numeric'},
                     id_grupo:1,
                     grid:true,
                     form:false

                 },
                 {
                     config:{
                         name: 'arqueo_moneda_extranjera',
                         fieldLabel: 'Arqueo Moneda Extranjera',
                         allowBlank: true,
                         anchor: '80%',
                         gwidth: 150,
                         maxLength:4,
                         allowDecimals: true,
                         decimalPrecision : 2,
                         renderer: function(value, p, record) {
                             if(record.data.nombre_punto_venta == 'TOTAL'){
                                 return String.format('<div ext:qtip="Optimo"><b><font color="red">{0}</font></b><br></div>', value);
                             }else{
                                 return String.format('<div ext:qtip="Optimo"><b><font color="black">{0}</font></b><br></div>', value);
                             }

                         }

                     },
                     type:'NumberField',
                     filters:{pfiltro:'apcie.arqueo_moneda_extranjera',type:'numeric'},
                     id_grupo:1,
                     grid:true,
                     form:false,
                     valorInicial :0.00
                 },
                 {
                     config:{
                         name: 'cajero',
                         fieldLabel: 'Nombre Cajero',
                         gwidth: 200
                     },
                     type:'TextField',
                     filters:{pfiltro:'pv.nombre',type:'string'},
                     grid:true,
                     form:false
                 },
                 {
                     config:{
                         name: 'usr_reg',
                         fieldLabel: 'usr_cajero',
                         gwidth: 100
                     },
                     type:'TextField',
                     filters:{pfiltro:'ven.estado',type:'string'},
                     grid:true,
                     form:false
                 }

             ],
             tam_pag:50,
             title:'Apertura de Caja',
             ActSave:'../../sis_ventas_facturacion/control/AperturaCierreCaja/insertarFecha',
             ActDel:'../../sis_ventas_facturacion/control/AperturaCierreCaja/eleminarFecha',
             ActList:'../../sis_ventas_facturacion/control/AperturaCierreCaja/listarAperturaCierreCajaEntrega',
             id_store:'id_apertura_cierre_caja',
             fields: [
                 {name:'id_apertura_cierre_caja', type: 'numeric'},
                 {name:'id_punto_venta', type: 'numeric'},
                 {name:'id_usuario_cajero', type: 'numeric'},
                 {name:'id_entrega_brinks', type: 'numeric'},
                 {name:'id_usuario_reg', type: 'numeric'},
                 {name:'usr_reg', type: 'string'},
                 {name:'usr_mod', type: 'string'},
                 {name:'fecha_apertura_cierre', type: 'date',dateFormat:'Y-m-d'},
                 {name:'nombre_punto_venta', type: 'string'},
                 {name:'arqueo_moneda_local', type: 'numeric'},
                 {name:'arqueo_moneda_extranjera', type: 'numeric'},
                 {name:'obs_cierre', type: 'string'},
                 {name:'cajero', type: 'string'}



             ],
             sortInfo:{
                 field: 'id_apertura_cierre_caja',
                 direction: 'ASC'
             },
         bedit:false,
         onReloadPage: function (m) {
             this.maestro = m;
             this.store.baseParams = {id_entrega_brinks: this.maestro.id_entrega_brinks};
             this.load({params: {start: 0, limit: 50}});
         },
         loadValoresIniciales:function() {
             Phx.vista.DepositoEntrega.superclass.loadValoresIniciales.call(this);
             this.getComponente('id_entrega_brinks').setValue(this.maestro.id_entrega_brinks);
         },
         onButtonNew:function(){
             console.log('fecha',this.Cmp.fecha);
             Phx.vista.DepositoEntrega.superclass.onButtonNew.call(this);
             this.iniciarEvento();
             this.Cmp.fecha.store.baseParams ={id_punto_venta:this.maestro.id_punto_venta};
         },

         successSave:function(resp){
             Phx.vista.DepositoEntrega.superclass.successSave.call(this,resp);
             Phx.CP.getPagina(this.idContenedorPadre).reload();
         },

         iniciarEvento:function () {
             this.Cmp.fecha.lastQuery = null;
         },
         onButtonDel:function(){
             var rec=this.sm.getSelected();
             console.log ('Data',rec.data.id_apertura_cierre_caja);
             this.store.baseParams.id_apertura_cierre_caja =  rec.data.id_apertura_cierre_caja;
             Phx.vista.DepositoEntrega.superclass.onButtonDel.call(this);
         }


         })

</script>
