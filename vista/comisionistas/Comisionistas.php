<?php
/**
 *@package pXP
 *@file    GenerarReportesComisionistas.php
 *@author  Ismael Valdivia
 *@date    21-01-2021
 *@description Archivo con la interfaz para generaci�n de reporte
 */
header("content-type: text/javascript; charset=UTF-8");
?>

<style>
.button-actualizar-dibu{
    background-image: url('../../../lib/imagenes/icono_dibu/dibu_act.png') !important;
    /* background-repeat: no-repeat;
    filter: saturate(250%);
    background-size: 80%; */
}
</style>
<script>
    Phx.vista.Comisionistas = Ext.extend(Phx.frmInterfaz, {

        Atributos : [
            {
                config:{
                    name: 'id_entidad',
                    fieldLabel: 'Entidad',
                    qtip: 'entidad a la que pertenese el depto, ',
                    allowBlank: false,
                    emptyText:'Entidad...',
                    msgTarget: 'side',
                    store:new Ext.data.JsonStore(
                        {
                            url: '../../sis_parametros/control/Entidad/listarEntidad',
                            id: 'id_entidad',
                            root: 'datos',
                            sortInfo:{
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_entidad','nit','nombre'],
                            // turn on remote sorting
                            remoteSort: true,
                            baseParams: { par_filtro:'nit#nombre' }
                        }),
                    valueField: 'id_entidad',
                    displayField: 'nombre',
                    gdisplayField:'desc_entidad',
                    hiddenName: 'id_entidad',
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:50,
                    queryDelay:500,
                    //anchor:"90%",
                    width: 280,
                    listWidth:280,
                    gwidth:150,
                    minChars:2,
                    renderer:function (value, p, record){return String.format('{0}', record.data['desc_entidad']);}

                },
                type:'ComboBox',
                filters:{pfiltro:'ENT.nombre',type:'string'},
                id_grupo:0,
                egrid: true,
                grid:true,
                form:true
            },



            {
                config:{
                    name:'tipo_reporte',
                    fieldLabel:'Tipo de Reporte',
                    typeAhead: true,
                    allowBlank:false,
                    triggerAction: 'all',
                    emptyText:'Tipo...',
                    selectOnFocus:true,
                    mode:'local',
                    msgTarget: 'side',
                    store:new Ext.data.ArrayStore({
                        fields: ['ID', 'valor'],
                        data :	[
                            //['endesis_erp','Libro de Compras Estandar'],
                            ['per_natu','Personas Naturales'],//concluido
                            ['regimen_simpli','Simplificado RTS'],//concluido
                            ['det_vent_natu','Detalle Ventas Naturales'],
                            ['res_vent_natu','Resumen Ventas Naturales'],
                            ['det_vent_rts','Detalle Ventas RTS'],
                            ['res_vent_rts','Resumen Ventas RTS'],

                        ]
                    }),
                    valueField:'ID',
                    displayField:'valor',
                    width:280,

                },
                type:'ComboBox',
                id_grupo:1,
                form:true
            },
            {
                config:{
                    name:'filtro_sql',
                    fieldLabel:'Filtrar Por',
                    typeAhead: true,
                    allowBlank:true,
                    triggerAction: 'all',
                    emptyText:'Filtro...',
                    selectOnFocus:true,
                    mode:'local',
                    store:new Ext.data.ArrayStore({
                        fields: ['ID', 'valor'],
                        data :	[['periodo','Gestión y Periodo']/*,
                            ['fechas','Rango de Fechas']*/]
                    }),
                    msgTarget: 'side',
                    valueField:'ID',
                    displayField:'valor',
                    width:280,
                    hidden:true

                },
                type:'ComboBox',
                id_grupo:1,
                form:true
            },

            {
                config:{
                    name:'id_gestion',
                    fieldLabel:'Gestión',
                    allowBlank:true,
                    emptyText:'Gestión...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_parametros/control/Gestion/listarGestion',
                        id: 'id_gestion',
                        root: 'datos',
                        sortInfo:{
                            field: 'gestion',
                            direction: 'DESC'
                        },
                        totalProperty: 'total',
                        fields: ['id_gestion','gestion','moneda','codigo_moneda'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams:{par_filtro:'gestion'}
                    }),
                    msgTarget: 'side',
                    valueField: 'id_gestion',
                    displayField: 'gestion',
                    //tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nro_cuenta}</b></p><p>{denominacion}</p></div></tpl>',
                    hiddenName: 'id_gestion',
                    forceSelection:true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:10,
                    queryDelay:1000,
                    listWidth:280,
                    resizable:true,
                    width: 280
                    //anchor:'100%'

                },
                type:'ComboBox',
                id_grupo:0,
                filters:{
                    pfiltro:'gestion',
                    type:'string'
                },
                grid:true,
                form:true
            },
            {
                config:{
                    name:'id_periodo',
                    fieldLabel:'Periodo',
                    allowBlank:true,
                    emptyText:'Periodo...',
                    msgTarget: 'side',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_parametros/control/Periodo/listarPeriodo',
                        id: 'id_periodo',
                        root: 'datos',
                        sortInfo:{
                            field: 'id_periodo',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_periodo','literal','periodo','fecha_ini','fecha_fin'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams:{par_filtro:'periodo#literal'}
                    }),
                    valueField: 'id_periodo',
                    displayField: 'literal',
                    //tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nro_cuenta}</b></p><p>{denominacion}</p></div></tpl>',
                    hiddenName: 'id_periodo',
                    forceSelection:true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:12,
                    queryDelay:1000,
                    listWidth:280,
                    resizable:true,
                    width: 280
                    //anchor:'100%'

                },
                type:'ComboBox',
                id_grupo:0,
                filters:{
                    pfiltro:'literal',
                    type:'string'
                },
                grid:true,
                form:true
            },

            {
                config:{
                    name:'id_periodo_inicio',
                    fieldLabel:'Periodo Inicial',
                    allowBlank:true,
                    emptyText:'Periodo Inicial...',
                    msgTarget: 'side',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_parametros/control/Periodo/listarPeriodo',
                        id: 'id_periodo',
                        root: 'datos',
                        sortInfo:{
                            field: 'id_periodo',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_periodo','literal','periodo','fecha_ini','fecha_fin'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams:{par_filtro:'periodo#literal'}
                    }),
                    valueField: 'id_periodo',
                    displayField: 'literal',
                    //tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nro_cuenta}</b></p><p>{denominacion}</p></div></tpl>',
                    hiddenName: 'id_periodo',
                    forceSelection:true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:12,
                    queryDelay:1000,
                    listWidth:280,
                    resizable:true,
                    width: 280,
                    hidden:true
                    //anchor:'100%'

                },
                type:'ComboBox',
                id_grupo:0,
                filters:{
                    pfiltro:'literal',
                    type:'string'
                },
                grid:true,
                form:true
            },

            {
                config:{
                    name:'id_periodo_final',
                    fieldLabel:'Periodo Final',
                    allowBlank:true,
                    emptyText:'Periodo...',
                    msgTarget: 'side',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_parametros/control/Periodo/listarPeriodo',
                        id: 'id_periodo',
                        root: 'datos',
                        sortInfo:{
                            field: 'id_periodo',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_periodo','literal','periodo','fecha_ini','fecha_fin'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams:{par_filtro:'periodo#literal'}
                    }),
                    valueField: 'id_periodo',
                    displayField: 'literal',
                    //tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nro_cuenta}</b></p><p>{denominacion}</p></div></tpl>',
                    hiddenName: 'id_periodo',
                    forceSelection:true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:12,
                    queryDelay:1000,
                    listWidth:280,
                    resizable:true,
                    width: 280,
                    hidden:true
                    //anchor:'100%'

                },
                type:'ComboBox',
                id_grupo:0,
                filters:{
                    pfiltro:'literal',
                    type:'string'
                },
                grid:true,
                form:true
            },


            {
                config:{
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    //anchor: '80%',
                    width: 177,
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'fecha_ini',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'fecha_fin',
                    fieldLabel: 'Fecha Fin',
                    allowBlank: true,
                    //anchor: '80%',
                    width: 177,
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'fecha_fin',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
            },
            // {
            //     config:{
            //         name:'id_usuario',
            //         fieldLabel:'Usuario',
            //         allowBlank:false,
            //         emptyText:'Usuario...',
            //         msgTarget: 'side',
            //         store: new Ext.data.JsonStore({
            //
            //             url: '../../sis_seguridad/control/Usuario/listarUsuario',
            //             id: 'id_persona',
            //             root: 'datos',
            //             sortInfo:{
            //                 field: 'desc_person',
            //                 direction: 'ASC'
            //             },
            //             totalProperty: 'total',
            //             fields: ['id_usuario','desc_person','cuenta'],
            //             // turn on remote sorting
            //             remoteSort: true,
            //             baseParams:{par_filtro:'PERSON.nombre_completo2#cuenta',_adicionar:'si'}
            //         }),
            //         valueField: 'id_usuario',
            //         displayField: 'desc_person',
            //         gdisplayField:'desc_usuario',//dibuja el campo extra de la consulta al hacer un inner join con orra tabla
            //         tpl:'<tpl for="."><div class="x-combo-list-item"><p>{desc_person}</p></div></tpl>',
            //         hiddenName: 'id_usuario',
            //         forceSelection:true,
            //         typeAhead: true,
            //         triggerAction: 'all',
            //         lazyRender:true,
            //         mode:'remote',
            //         pageSize:10,
            //         queryDelay:1000,
            //         width:280,
            //         gwidth:280,
            //         minChars:2
            //     },
            //     type:'ComboBox',
            //     id_grupo:0,
            //     form:true
            // },
            {
                config:{
                    name:'formato_reporte',
                    fieldLabel:'Formato del Reporte',
                    typeAhead: true,
                    allowBlank:false,
                    triggerAction: 'all',
                    emptyText:'Formato...',
                    selectOnFocus:true,
                    mode:'local',
                    msgTarget: 'side',
                    store:new Ext.data.ArrayStore({
                        fields: ['ID', 'valor'],
                        data :	[
                            ['pdf','PDF'],
                            ['xls','XLS'],
                            ['txt','TXT']]
                    }),
                    valueField:'ID',
                    displayField:'valor',
                    width:280,

                },
                type:'ComboBox',
                id_grupo:1,
                form:true
            }],


        title : 'Reporte Comisionistas',
        //ActSave : '../../sis_contabilidad/control/TsLibroBancos/reporteLibroBancos2',

        topBar : true,
        botones : false,
        labelSubmit : 'Generar',
        tooltipSubmit : '<b>Reporte Comisionistas</b>',


        constructor : function(config) {
            Phx.vista.Comisionistas.superclass.constructor.call(this, config);
            this.init();

            this.ocultarComponente(this.Cmp.fecha_fin);
            this.ocultarComponente(this.Cmp.fecha_ini);
            this.ocultarComponente(this.Cmp.id_gestion);
            this.ocultarComponente(this.Cmp.id_periodo);


            this.addButton('actualizar_data',{
    				text :'Actualizar Data',
    				iconCls : 'button-actualizar-dibu',
    				disabled: false,
    				handler : this.actualizar_comisionistas,
    				tooltip : '<b>Actualizar la información Manualmente</b>'
            });

            this.iniciarEventos();
        },

        iniciarEventos:function(){

          /*Aqui cambiaremos el Fondo del formualario*/
          this.form.body.dom.style.background = '#EFEFEF';
          // this.form.body.dom.style.backgroundImage = "url('../../../lib/imagenes/facturacion/guardar.png')";
          // this.form.body.dom.style.backgroundRepeat = 'no-repeat';
          // this.form.body.dom.style.backgroundPosition = 'center';
          //this.form.body.dom.style.backgroundSize = 'cover';



          //this.form.boy.dom.style
          /*******************************************/

            this.Cmp.id_entidad.store.load({params:{start:0, limit:10}, scope:this, callback: function (param,op,suc) {
                    this.Cmp.id_entidad.setValue(param[0].data.id_entidad);
                    this.Cmp.id_entidad.collapse();
                    this.Cmp.tipo_reporte.focus(false,  5);
                }});

            this.Cmp.tipo_reporte.on('select', function (combo,record,index){
              //if (record.data.ID == 'res_vent_natu' || record.data.ID == 'res_vent_rts') {
                this.mostrarComponente(this.Cmp.id_periodo_inicio);
                this.Cmp.id_periodo_inicio.allowBlank = false;
                this.mostrarComponente(this.Cmp.id_periodo_final);
                this.Cmp.id_periodo_final.allowBlank = false;

                this.mostrarComponente(this.Cmp.id_gestion);
                this.Cmp.id_gestion.allowBlank = false;



                /*Aqui Ocultaremos los demas campos*/
                this.ocultarComponente(this.Cmp.filtro_sql);
                this.ocultarComponente(this.Cmp.id_periodo);
                this.ocultarComponente(this.Cmp.fecha_ini);
                this.ocultarComponente(this.Cmp.fecha_fin);
                this.Cmp.filtro_sql.allowBlank = true;
                this.Cmp.id_periodo.allowBlank = true;
                this.Cmp.fecha_ini.allowBlank = true;
                this.Cmp.fecha_fin.allowBlank = true;
                this.Cmp.filtro_sql.reset();
                this.Cmp.id_periodo.reset();
                this.Cmp.fecha_ini.reset();
                this.Cmp.fecha_fin.reset();
                /***********************************/
              // } else {
              //   this.ocultarComponente(this.Cmp.id_periodo_inicio);
              //   this.Cmp.id_periodo_inicio.allowBlank = true;
              //   this.Cmp.id_periodo_inicio.reset();
              //   this.ocultarComponente(this.Cmp.id_periodo_final);
              //   this.Cmp.id_periodo_final.allowBlank = true;
              //   this.Cmp.id_periodo_final.reset();
              //
              //   this.ocultarComponente(this.Cmp.id_gestion);
              //   this.Cmp.id_gestion.reset();
              //   this.Cmp.id_gestion.allowBlank = true;
              //
              //   this.mostrarComponente(this.Cmp.filtro_sql);
              //   this.Cmp.filtro_sql.allowBlank = false;
              //   this.Cmp.id_periodo_inicio.reset();
              //   this.Cmp.id_periodo_final.reset();
              //
              //
              // }

            },this);


            this.Cmp.id_gestion.on('select',function(c,r,n){

                this.Cmp.id_periodo.reset();
                this.Cmp.id_periodo.store.baseParams={id_gestion:c.value, vista: 'reporte'};
                this.Cmp.id_periodo.modificado=true;


                this.Cmp.id_periodo_inicio.reset();
                this.Cmp.id_periodo_inicio.store.baseParams={id_gestion:c.value, vista: 'reporte'};
                this.Cmp.id_periodo_inicio.modificado=true;

                this.Cmp.id_periodo_final.reset();
                this.Cmp.id_periodo_final.store.baseParams={id_gestion:c.value, vista: 'reporte'};
                this.Cmp.id_periodo_final.modificado=true;

            },this);






            this.Cmp.filtro_sql.on('select',function(combo, record, index){

                if(index == 0){
                    this.ocultarComponente(this.Cmp.fecha_fin);
                    this.ocultarComponente(this.Cmp.fecha_ini);
                    this.mostrarComponente(this.Cmp.id_gestion);
                    this.ocultarComponente(this.Cmp.id_periodo);
                    this.mostrarComponente(this.Cmp.id_periodo_inicio);
                    this.mostrarComponente(this.Cmp.id_periodo_final);
                }
                else{
                    this.mostrarComponente(this.Cmp.fecha_fin);
                    this.mostrarComponente(this.Cmp.fecha_ini);
                    this.ocultarComponente(this.Cmp.id_gestion);
                    this.ocultarComponente(this.Cmp.id_periodo);
                }

            }, this);

            /****************Mostrar el monto de la resolucion******************************/
            this.monto_resolucion = new Ext.form.Label({
                name: 'monto_resolucion',
                fieldLabel: 'Monto Resolucion',
                readOnly:true,
                anchor: '150%',
                gwidth: 150,
                hidden : false,
                style: {
                  fontSize:'30px',
                  fontWeight:'bold',
                  color:'#FF6C00',
                  textShadow: '0.5px 0.5px 0px #FFFFFF, 1px 0px 0px rgba(0,0,0,0.15)'
                }
            });

            this.tbar.addField(this.monto_resolucion);

            Ext.Ajax.request({
								url:'../../sis_ventas_facturacion/control/Comisionistas/recuperarMontoNormativa',
								params:{
									monto_normativa:'recuperar'
								},
								success: function(resp){
										var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    console.log("la respuesta del dato es",reg);

                      this.monto_resolucion.setText('Monto Normativa: '+reg.ROOT.datos.monto_acumulado);
										// this.aperturaText = reg.ROOT.datos.monto_acumulado;
										// this.tipo_punto_venta = reg.ROOT.datos.v_tipo_punto_venta;
										// this.variables_globales.aperturaEstado = this.aperturaText;
										/*****************************************************************/

								},
								failure: this.conexionFailure,
								timeout:this.timeout,
								scope:this
						});
            /*******************************************************************************/


        },



        tipo : 'reporte',
        clsSubmit : 'bprint',

        Grupos : [{
            layout : 'column',
            labelAlign: 'top',
            border : false,
            autoScroll: true,
            autoHeight: true,
            xtype: 'fieldset',
            style:{
                  //background:'#FFBE59',
                  //border:'2px solid green',
                  width : '100%',
                 },
            items : [
                {
                    columnWidth: .31,
                    border: false,
                    //split: true,
                    xtype:'fieldset',
                    layout: 'anchor',
                    autoScroll: true,

                    collapseFirst : false,
                    collapsible: false,
                    //anchor: '100%',
                    padding: '0 0 0 0',
                    style: {
                           //height:'100px',
                            //background: '#5FB0A8',
                            width : '200px',
                           //border:'2px solid green'
                        },
                    items:[
                      {
                       border:false,
                       //frame: false,
                       xtype:'fieldset',
                       layout: 'anchor',
                       autoHeight: true,
                       style:{
                         border:'1px solid #DEDEDE',
                         width : '200px',
                         border: '2px solid black',
                         //height : '100%',
                         margin:'0px',
                         background:'#C3E1F7',
                         //paddingBottom:'-20px',
                        },
                       items:[
                         {
                             xtype:'fieldset',
                             layout: 'form',
                             title: 'Datos Generación Reporte',
                             frame: true,
                             style:{
                               background:'#C3E1F7',
                               //height:'100%'
                             },
                             columnWidth: 0.5,
                             items:[],
                             id_grupo:0,
                             border:false,
                             collapsible:false
                         }
                      ]
                    },
                    ]
                }
            ]
        }],

        //ActSave:'../../sis_ventas_facturacion/control/DocCompraVentaForm/reporteLCV',
        ActSave:'../../sis_ventas_facturacion/control/Comisionistas/reportesComisionistas',

        actualizar_comisionistas:function() {
            Phx.CP.loadingShow();
            Ext.Ajax.request({
				url:'../../sis_ventas_facturacion/control/Comisionistas/TraerAcumulados',
                params:{
                    comisionistas_traer:  'traer_data'
                    },
                success:this.successActualizarDatos,
				failure: this.conexionFailure,
				timeout:this.timeout,
				scope:this
		    });
        },

        successActualizarDatos :function(resp){
            Phx.CP.loadingHide();
            console.log('reg', reg);
        },

        successSave :function(resp){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log('reg', reg);
            if (reg.ROOT.error) {
                alert('error al procesar');
                return
            }

            var nomRep = reg.ROOT.detalle.archivo_generado;
            if(Phx.CP.config_ini.x==1){
                nomRep = Phx.CP.CRIPT.Encriptar(nomRep);
            }
            console.log("aqui llega formato para imprmir",this.Cmp.formato_reporte.getValue());
            if(this.Cmp.formato_reporte.getValue()=='pdf'){
                window.open('../../../lib/lib_control/Intermediario.php?r='+nomRep+'&t='+new Date().toLocaleTimeString())
            //}else if (this.Cmp.formato_reporte.getValue()=='xls'){
            //    window.open('../../../reportes_generados/'+nomRep+'?t='+new Date().toLocaleTimeString())
            }else if (this.Cmp.formato_reporte.getValue()=='txt'){
                window.open('../../../reportes_generados/'+nomRep+'?t='+new Date().toLocaleTimeString())
    						var data = "&extension=txt";
    							  data += "&name_file="+nomRep;
    								data += "&url=../../../reportes_generados/"+nomRep;
    								window.open('../../../lib/lib_control/CTOpenFile.php?' + data);
            } else if (this.Cmp.formato_reporte.getValue()=='xls'){
                  console.log("aqui entra para descargar");
                  var data = "&extension=xls";
      							  data += "&name_file="+nomRep;
      								data += "&url=../../../reportes_generados/"+nomRep;
      								window.open('../../../lib/lib_control/CTOpenFile.php?' + data);
                  //window.open('../../../reportes_generados/'+nomRep+'?t='+new Date().toLocaleTimeString())
            }

        }
    })
</script>
