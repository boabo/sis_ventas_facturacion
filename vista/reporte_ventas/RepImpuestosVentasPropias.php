<script>
    Phx.vista.RepImpuestosVentasPropias = Ext.extend(Phx.frmInterfaz, {

        Atributos : [
          {
              config:{
                  name: 'fecha_ini',
                  fieldLabel: 'DESDE',
                  width: 177,
                  gwidth: 100,
                  format: 'd/m/Y',
                  allowBlank: false,
                  style:'margin-bottom: 10px;'
              },
              type:'DateField',
              filters:{pfiltro:'fecha_ini',type:'date'},
              id_grupo:0,
              form:true
          },
          {
              config: {
                  name: 'id_lugar',
                  fieldLabel: 'PAÍS',
                  allowBlank: false,
                  emptyText: 'Lugar...',
                  store: new Ext.data.JsonStore(
                      {
                          url: '../../sis_parametros/control/Lugar/listarLugar',
                          id: 'id_lugar',
                          root: 'datos',
                          sortInfo: {
                              field: 'nombre',
                              direction: 'ASC'
                          },
                          totalProperty: 'total',
                          fields: ['id_lugar', 'id_lugar_fk', 'codigo', 'nombre', 'tipo', 'sw_municipio', 'sw_impuesto', 'codigo_largo'],
                          // turn on remote sorting
                          remoteSort: true,
                          //baseParams:{tipos:"''departamento'',''pais'',''localidad''",par_filtro:'nombre'}
                          baseParams: {tipos: "''pais''", par_filtro: 'nombre'}
                      }),
                  valueField: 'id_lugar',
                  displayField: 'nombre',
                  gdisplayField: 'codigo',
                  hiddenName: 'id_lugar',
                  tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p></div></tpl>',
                  triggerAction: 'all',
                  lazyRender: true,
                  mode: 'remote',
                  pageSize: 50,
                  queryDelay: 500,
                  gwidth: 250,
                  width:300,
                  forceSelection: true,
                  minChars: 2,
                  style:'margin-bottom: 10px;'
              },
              type: 'ComboBox',
              filters: {pfiltro: 'lug.nombre', type: 'string'},
              id_grupo: 0,
              form: true
          },
          {
              config: {
                  name: 'id_catalogo',
                  fieldLabel: 'CANAL DE VENTA',
                  allowBlank: true,
                  emptyText: 'Canal venta...',
                  store: new Ext.data.JsonStore(
                      {
                          url: '../../sis_ventas_facturacion/control/ReporteVentas/listarCanalVenta',
                          id: 'id_catalogo',
                          root: 'datos',
                          sortInfo: {
                              field: 'codigo',
                              direction: 'ASC'
                          },
                          totalProperty: 'total',
                          fields: ['id_catalogo', 'codigo', 'descripcion'],
                          remoteSort: true,
                          baseParams: {cod_catalogo: 'canal_venta', par_filtro:'codigo#descripcion'}
                      }),
                  valueField: 'id_catalogo',
                  displayField: 'codigo',
                  gdisplayField: 'codigo',
                  hiddenName: 'id_catalogo',
                  tpl:'<tpl for="."><div class="x-combo-list-item"><p style="text-transform: uppercase;"><b>{codigo}</b></p></div></tpl>',
                  triggerAction: 'all',
                  lazyRender: true,
                  mode: 'remote',
                  pageSize: 50,
                  queryDelay: 500,
                  gwidth: 250,
                  width:300,
                  forceSelection: true,
                  minChars: 2,
                  style:'margin-bottom: 10px;'
              },
              type: 'ComboBox',
              filters: {pfiltro: 'lug.nombre', type: 'string'},
              id_grupo: 0,
              form: true
          },
          {
              config:{
                  name: 'fecha_fin',
                  fieldLabel: 'HASTA',
                  allowBlank: false,
                  width: 177,
                  gwidth: 100,
                  format: 'd/m/Y',
                  style:'margin-bottom: 10px;'
              },
              type:'DateField',
              filters:{pfiltro:'fecha_fin',type:'date'},
              id_grupo:1,
              form:true
          },
          {
  			config: {
  	                name: 'id_punto_venta',
  	                fieldLabel: 'CODIGO IATA',
  	                allowBlank: true,
  	                emptyText: 'Elija la codigo iata...',
  	                store: new Ext.data.JsonStore({
  	                    url: '../../sis_ventas_facturacion/control/ReporteVentas/listarPuntoVentaRbol',
  	                    id: 'id_punto_venta',
  	                    root: 'datos',
  	                    sortInfo: {
  	                        field: 'nombre',
  	                        direction: 'ASC'
  	                    },
  	                    totalProperty: 'total',
  	                    fields: ['id_punto_venta', 'nombre', 'codigo', 'office_id'],
  	                    remoteSort: true,
  	                    baseParams: {_adicionar : 'si', offi_id:'si', par_filtro: 'codigo#nombre#office_id'}
  	                }),
  	                valueField: 'id_punto_venta',
  	                displayField: 'codigo',
  	                gdisplayField: 'codigo',
  	                hiddenName: 'id_punto_venta',
  	                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{codigo}</b></p></div></tpl>',
  	                forceSelection: true,
  	                typeAhead: false,
  	                triggerAction: 'all',
  	                lazyRender: true,
  	                mode: 'remote',
  	                pageSize: 15,
  	                queryDelay: 1000,
  	                gwidth: 250,
  	                width:300,
  	                resizable:true,
  	                minChars: 2,
                  	hidden : false,
                    style:'margin-bottom: 10px;'

  	            },
  	            type: 'ComboBox',
  	            id_grupo: 0,
  	            filters: {pfiltro: 'puve.nombre',type: 'string'},
  	            form: true
  	       },
          {
              config: {
                  name: 'id_lugar_fk',
                  fieldLabel: 'CIUDAD',
                  allowBlank: true,
                  emptyText: 'Lugar...',
                  qtip: 'Ciudad',
                  store: new Ext.data.JsonStore(
                      {
                          url: '../../sis_parametros/control/Lugar/listarLugar',
                          id: 'id_lugar',
                          root: 'datos',
                          sortInfo: {
                              field: 'nombre',
                              direction: 'ASC'
                          },
                          totalProperty: 'total',
                          fields: ['id_lugar', 'id_lugar_fk', 'codigo', 'nombre', 'tipo', 'sw_municipio', 'sw_impuesto', 'codigo_largo'],
                          // turn on remote sorting
                          //baseParams:{tipos:"''departamento'',''pais'',''localidad''",par_filtro:'nombre'}
                          remoteSort: true,
                          baseParams: {tipos: "''departamento'', ''provincia''", par_filtro: 'nombre'}
                      }),
                  valueField: 'id_lugar',
                  displayField: 'nombre',
                  gdisplayField: 'lugar_depto',
                  hiddenName: 'id_lugar',
                  tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p></div></tpl>',
                  triggerAction: 'all',
                  lazyRender: true,
                  mode: 'remote',
                  pageSize: 50,
                  queryDelay: 500,
                  gwidth: 250,
                  width:300,
                  forceSelection: true,
                  minChars: 2,
                  style:'margin-bottom: 10px;'
              },
              type: 'ComboBox',
              filters: {pfiltro: 'lug.nombre', type: 'string'},
              id_grupo: 1,
              form: true
          },
          {
      			config : {
      				name : 'tipo',
      				fieldLabel : 'TIPO VENTA',
      				tinit : false,
      				allowBlank : true,
      				origen : 'CATALOGO',
      				gdisplayField : 'tipo',
              gwidth: 250,
              width:300,
      				valueField: 'descripcion',
              style:'margin-bottom: 10px;',
              tpl:'<tpl for="."><div class="x-combo-list-item"><p style="text-transform: uppercase;"><b>{codigo}</b></p></div></tpl>',
      				baseParams : {
      					cod_subsistema : 'VEF',
      					catalogo_tipo : 'tipo_punto_venta',
                // par_filtro: 'codigo'
      				}
      			},
      			type : 'ComboRec',
      			id_grupo : 1,
      			form : true
      		},
          {
  			config: {
  	                name: 'id_punto_venta_1',
  	                fieldLabel: 'OFICINA DE VENTA',
  	                allowBlank: true,
  	                emptyText: 'Elija la Oficina de venta...',
  	                store: new Ext.data.JsonStore({
  	                    url: '../../sis_ventas_facturacion/control/ReporteVentas/listarPuntoVentaRbol',
                        // url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
  	                    id: 'id_punto_venta',
  	                    root: 'datos',
  	                    sortInfo: {
  	                        field: 'nombre',
  	                        direction: 'ASC'
  	                    },
  	                    totalProperty: 'total',
  	                    fields: ['id_punto_venta', 'nombre', 'codigo'],
  	                    remoteSort: true,
  	                    baseParams: {_adicionar : 'si', par_filtro:'codigo#nombre'}
  	                }),
  	                valueField: 'id_punto_venta',
  	                displayField: 'nombre',
  	                gdisplayField: 'nombre_punto_venta',
  	                hiddenName: 'id_punto_venta',
  	                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
  	                forceSelection: true,
  	                typeAhead: false,
  	                triggerAction: 'all',
  	                lazyRender: true,
  	                mode: 'remote',
  	                pageSize: 15,
  	                queryDelay: 1000,
  	                gwidth: 250,
  	                width:300,
  	                resizable:true,
  	                minChars: 2,
                  	hidden : false

  	            },
  	            type: 'ComboBox',
  	            id_grupo: 1,
  	            filters: {pfiltro: 'puve.nombre',type: 'string'},
  	            form: true
  	       },
           {
               config: {
                   name: 'id_moneda',
                   origen: 'MONEDA',
                   allowBlank: true,
                   fieldLabel: 'MONEDA',
                   gdisplayField: 'desc_moneda', //mapea al store del grid
                   gwidth: 80,
                   anchor:'100%'
               },
               type: 'ComboRec',
               id_grupo: 0,
               form: true
           },
          ],


        title : 'Reporte de Ventas',
        //ActSave : '../../sis_contabilidad/control//',

        topBar : true,
        botones : false,
        labelSubmit : 'Generar',
        tooltipSubmit : '<b>Reporte Revision Boletos</b>',
        constructor : function(config) {
            Phx.vista.RepImpuestosVentasPropias.superclass.constructor.call(this, config);
            this.init();
            this.country='',this.city='',this.channel='',this.typePOS='',this.iataCode='',this.officeID='';
            var fecha = new Date();
            Ext.Ajax.request({
                url:'../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                params:{fecha:fecha.getDate()+'/'+(fecha.getMonth()+1)+'/'+fecha.getFullYear()},
                success:function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    this.Cmp.fecha_ini.setValue('01/01/'+reg.ROOT.datos.anho);
                    this.Cmp.fecha_fin.setValue('31/12/'+reg.ROOT.datos.anho);
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
            this.iniciarEventos();
        },

        iniciarEventos:function(){

          this.Cmp.id_lugar.store.load({params:{start:0, limit:100}, scope:this, callback: function (param,op,suc) {

                  this.Cmp.id_lugar.setValue(param[5].data.id_lugar);
                  this.Cmp.id_lugar_fk.reset();
                  this.Cmp.id_lugar_fk.store.baseParams.id_lugar_fk = param[5].data.id_lugar;
                  this.Cmp.id_lugar_fk.modificado = true;
                  this.Cmp.id_lugar.collapse();
                  this.Cmp.id_lugar_fk.focus(false,  3);
          }});

          this.Cmp.id_lugar.on('select',function(cmp, rec, indice){

                        this.country = rec.data.codigo.toUpperCase();
                        this.Cmp.id_lugar_fk.reset();
                        this.Cmp.id_lugar_fk.store.baseParams.id_lugar_fk = rec.data.id_lugar;
                        this.Cmp.id_lugar_fk.modificado = true;
          },this);

          this.Cmp.id_lugar_fk.on('select',function(cmp, rec, indice){
                        this.city = rec.data.codigo.toUpperCase();
          },this);

          this.Cmp.id_catalogo.on('select',function(cmp, rec, indice){
                        this.channel = rec.data.codigo.toUpperCase();
          },this);

          this.Cmp.tipo.on('select',function(cmp, rec, indice){
                        this.typePOS = rec.data.codigo.toUpperCase();
          },this);

          this.Cmp.id_punto_venta.on('select',function(cmp, rec, indice){
                        this.iataCode = rec.data.codigo.toUpperCase();
          },this);

          this.Cmp.id_punto_venta_1.on('select',function(cmp, rec, indice){
                        this.officeID = rec.data.nombre.toUpperCase();
          },this);

        },



        tipo : 'reporte',
        clsSubmit : 'bprint',

        Grupos:
            [
                {
                    layout: 'column',
                    border: true,
                    defaults: {
                        border: false
                    },
                    items: [{
                        bodyStyle: 'padding-right:5px;margin-top:10px;',
                        items: [{
                            xtype: 'fieldset',
                            border: false,
                            // title: 'FILTROS DE CONSULTA',
                            autoHeight: true,
                            items: [],
                            id_grupo:0
                        }]
                    }, {
                        bodyStyle: 'padding-left:5px;margin-top:10px;',
                        items: [{
                            xtype: 'fieldset',
                            title: '',
                            border: false,
                            autoHeight: true,
                            items: [],
                            id_grupo:1
                        }]
                    }]
                }
            ],

        // ActSave:'../../sis_ventas_facturacion/control/ReporteVentas/onReporteVentas',

        onSubmit: function(){
        	    var me = this;
    			if (me.form.getForm().isValid()) {

            if(this.country == ''){
                this.country = me.Cmp.id_lugar.getStore().getById(me.Cmp.id_lugar.getValue()).data.codigo.toUpperCase();
            }
            var arg =  '/Control Ingresos/Reporte+de+Venta&rs:Command=Render&from=' + me.Cmp.fecha_ini.getValue().format('Y-m-d');
            		arg = arg + "&to=" + me.Cmp.fecha_fin.getValue().format('Y-m-d');
            		arg = arg + "&country=" + this.country;
            		arg = arg + "&city=" + this.city;
                arg = arg + "&channel=" + this.channel;
                arg = arg + "&typePOS=" + this.typePOS;
                arg = arg + "&iataCode=" + this.iataCode;
                arg = arg + "&officeID=" + this.officeID;
                arg = arg + "&rs:Format=EXCEL";
                console.log("resp", arg);
                // window.open('http://172.17.110.5:8082/BoAReportServer/Pages/ReportViewer.aspx?'+arg, '_blank');

    			}
    		}
    })
</script>
