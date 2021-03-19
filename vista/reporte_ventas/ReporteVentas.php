<script>
    Phx.vista.ReporteVentas = Ext.extend(Phx.frmInterfaz, {

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
                  name: 'id_lugar_pais',
                  fieldLabel: 'PAÍS',
                  allowBlank: false,
                  emptyText: 'Lugar...',
                  store: new Ext.data.JsonStore(
                      {
                          url: '../../sis_ventas_facturacion/control/ReporteVentas/puntoVentaPaiStage',
                          id: 'country_code',
                          root: 'datos',
                          sortInfo: {
                              field: 'country_code',
                              direction: 'ASC'
                          },
                          totalProperty: 'total',
                          fields: ['country_code', 'country_name'],
                          remoteSort: true,
                          baseParams: {par_filtro: 'country_name#country_code',_adicionar:'si'}
                      }),
                  valueField: 'country_code',
                  displayField: 'country_name',
                  gdisplayField: 'country_code',
                  hiddenName: 'country_code',
                  tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{country_name}</b></p></div></tpl>',
                  triggerAction: 'all',
                  lazyRender: true,
                  mode: 'remote',
                  pageSize: 15,
                  queryDelay: 500,
                  gwidth: 250,
                  width:300,
                  forceSelection: true,
                  minChars: 2,
                  style:'margin-bottom: 10px;'
              },
              valorInicial: 'TODOS' ,
              type: 'ComboBox',
              id_grupo: 0,
              form: true
          },
          {
              config: {
                  name: 'id_canal',
                  fieldLabel: 'CANAL DE VENTA',
                  allowBlank: false,
                  emptyText: '',
                  store: new Ext.data.JsonStore(
                      {
                          url: '../../sis_ventas_facturacion/control/ReporteVentas/listarCanalVentaStage',
                          id: 'sale_channel',
                          root: 'datos',
                          sortInfo: {
                              field: 'sale_channel',
                              direction: 'ASC'
                          },
                          totalProperty: 'total',
                          fields: ['sale_channel'],
                          remoteSort: true,
                          baseParams: {par_filtro:'sale_channel', _adicionar:'si'}
                      }),
                  valueField: 'sale_channel',
                  displayField: 'sale_channel',
                  gdisplayField: 'sale_channel',
                  hiddenName: 'sale_channel',
                  tpl:'<tpl for="."><div class="x-combo-list-item"><p style="text-transform: uppercase;"><b>{sale_channel}</b></p></div></tpl>',
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
              valorInicial: 'TODOS' ,
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
                    name: 'id_codigo_aita',
                    fieldLabel: 'CODIGO IATA',
                    allowBlank: false,
                    disabled: false,
                    emptyText: '',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_ventas_facturacion/control/ReporteVentas/listarCodigoIataStage',
                        id: 'iata_code',
                        root: 'datos',
                        sortInfo: {
                            field: 'iata_code',
                            direction: 'DESC'
                        },
                        totalProperty: 'total',
                        fields: ['iata_code'],
                        remoteSort: true,
                        baseParams: {_adicionar : 'si', par_filtro: 'iata_code'}
                    }),
                    valueField: 'iata_code',
                    displayField: 'iata_code',
                    gdisplayField: 'iata_code',
                    hiddenName: 'iata_code',
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{iata_code}</b></p></div></tpl>',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 25,
                    queryDelay: 1000,
                    gwidth: 250,
                    width:300,
                    resizable:true,
                    minChars: 2,
                    listWidth: '300',
                    hidden : false,
                    style:'margin-bottom: 10px;'

                },
                type: 'ComboBox',
                valorInicial: 'TODOS' ,
                id_grupo: 0,
                filters: {pfiltro: 'puve.nombre',type: 'string'},
                form: true
           },
           {
               config: {
                   name: 'id_lugar_ciudad',
                   fieldLabel: 'CIUDAD',
                   allowBlank: false,
                   emptyText: 'Lugar...',
                   store: new Ext.data.JsonStore(
                       {
                           url: '../../sis_ventas_facturacion/control/ReporteVentas/puntoVentaCiudadStage',
                           id: 'city_name',
                           root: 'datos',
                           sortInfo: {
                               field: 'city_name',
                               direction: 'ASC'
                           },
                           totalProperty: 'total',
                           fields: ['city_name', 'city_code'],
                           remoteSort: true,
                           baseParams: {par_filtro: 'city_name#city_code',_adicionar:'si'}
                       }),
                   valueField: 'city_code',
                   displayField: 'city_name',
                   gdisplayField: 'city_code',
                   hiddenName: 'city_code',
                   tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{city_name} -- <span style="color:green;">{city_code}</span></b></p></div></tpl>',
                   triggerAction: 'all',
                   lazyRender: true,
                   mode: 'remote',
                   pageSize: 20,
                   queryDelay: 500,
                   gwidth: 250,
                   width:300,
                   forceSelection: true,
                   minChars: 2,
                   style:'margin-bottom: 10px;'
               },
               valorInicial: 'TODOS' ,
               type: 'ComboBox',
               id_grupo: 1,
               form: true
           },
           {
               config: {
                   name: 'tipo_venta',
                   fieldLabel: 'TIPO VENTA',
                   allowBlank: false,
                   emptyText: '',
                   store: new Ext.data.JsonStore(
                       {
                           url: '../../sis_ventas_facturacion/control/ReporteVentas/listarPuntoVentaTipoStage',
                           id: 'tipo_pos',
                           root: 'datos',
                           sortInfo: {
                               field: 'tipo_pos',
                               direction: 'ASC'
                           },
                           totalProperty: 'total',
                           fields: ['tipo_pos'],
                           remoteSort: true,
                           baseParams: {_adicionar:'si', par_filtro:'tipo_pos'}
                       }),
                   valueField: 'tipo_pos',
                   displayField: 'tipo_pos',
                   gdisplayField: 'tipo_pos',
                   hiddenName: 'tipo_pos',
                   triggerAction: 'all',
                   tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{tipo_pos}</span></b></p></div></tpl>',
                   lazyRender: true,
                   mode: 'remote',
                   pageSize: 50,
                   queryDelay: 500,
                   gwidth: 250,
                   width:300,
                   forceSelection: true,
                   minChars: 2,
                   // enableMultiSelect: true,
                   style:'margin-bottom: 10px;'
               },
               type: 'ComboBox',
               valorInicial: 'TODOS' ,
               id_grupo: 1,
               form: true
           },
           {
   			config: {
   	                name: 'id_office',
   	                fieldLabel: 'OFICINA DE VENTA',
   	                allowBlank: false,
                     disabled: false,
   	                emptyText: '',
   	                store: new Ext.data.JsonStore({
   	                    url: '../../sis_ventas_facturacion/control/ReporteVentas/listarPuntoVentaOfficeIdStage',
   	                    id: 'office_id',
   	                    root: 'datos',
   	                    sortInfo: {
   	                        field: 'office_id',
   	                        direction: 'ASC'
   	                    },
   	                    totalProperty: 'total',
   	                    fields: ['office_id','name_pv'],
   	                    remoteSort: true,
   	                    baseParams: {_adicionar : 'si', par_filtro:'office_id#name_pv'}
   	                }),
   	                valueField: 'office_id',
   	                displayField: 'office_id',
   	                gdisplayField: 'office_id',
   	                hiddenName: 'office_id',
   	                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{office_id} ---> {name_pv} </b></p></div></tpl>',
   	                forceSelection: true,
   	                typeAhead: false,
   	                triggerAction: 'all',
   	                lazyRender: true,
   	                mode: 'remote',
   	                pageSize: 25,
                    listWidth: 450,
   	                queryDelay: 1000,
   	                gwidth: 250,
   	                width:300,
   	                resizable:true,
   	                minChars: 2,
                   	hidden : false

   	            },
   	            type: 'ComboBox',
                 valorInicial: 'TODOS' ,
   	            id_grupo: 1,
   	            form: true
   	       },
           {
           config : {
               name : 'tipo_documento',
               fieldLabel : 'TIPO DOCUMENTO',
               allowBlank : true,
               triggerAction : 'all',
               lazyRender : true,
   						gwidth : 100,
   						anchor : '50%',
               mode : 'local',
               emptyText:'...',
               store: new Ext.data.ArrayStore({
                   id: '',
                   fields: [
                       'key',
                       'value'
                   ],
                   data: [
                       ['TODOS', 'TODOS'],
                       ['TKTT', 'TKTT'],
                       ['RFND', 'RFND'],
                       ['EMDA', 'EMDA'],
                       ['EMDS', 'EMDS'],
                       ['CANN', 'CANN'],
                       ['CANX', 'CANX'],
                       ['ADMA', 'ADMA'],
                       ['ACMA', 'ACMA'],
                       ['ACMD', 'ACMD'],
                       ['SPCR', 'SPCR'],
                       ['SPDR', 'SPDR'],
                   ]
               }),
               valueField: 'key',
               displayField: 'value'
           },
           type : 'ComboBox',
           valorInicial: 'TODOS',
           id_grupo : 0,
           form : true,
           grid : true
         }
        ],


        title : 'Reporte de Ventas',
        //ActSave : '../../sis_contabilidad/control//',

        topBar : true,
        botones : false,
        labelSubmit : 'Generar',
        tooltipSubmit : '<b>Reporte Revision Boletos</b>',
        constructor : function(config) {
            Phx.vista.ReporteVentas.superclass.constructor.call(this, config);
            this.init();
            var fecha = new Date();
            Ext.Ajax.request({
                url:'../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                params:{fecha:fecha.getDate()+'/'+(fecha.getMonth()+1)+'/'+fecha.getFullYear()},
                success:function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    this.Cmp.fecha_ini.setValue('01/01/'+reg.ROOT.datos.anho);
                    this.Cmp.fecha_fin.setValue('31/01/'+reg.ROOT.datos.anho);
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
            this.iniciarEventos();
        },

        iniciarEventos:function(){
          var me = this;
          this.Cmp.id_lugar_pais.on('select',function(cmp, rec, indice){ {
                  me.Cmp.id_lugar_ciudad.reset();
                  me.Cmp.id_canal.reset();
                  me.Cmp.tipo_venta.reset();
                  me.Cmp.id_codigo_aita.reset();
                  me.Cmp.id_office.reset();
                  me.Cmp.id_lugar_ciudad.store.baseParams.id_lugar_pais = rec.data.country_code;
                  me.Cmp.id_lugar_ciudad.modificado = true;
          }});

          this.Cmp.id_lugar_ciudad.on('select',function(cmp, rec, indice){
                  me.Cmp.id_canal.reset();
                  me.Cmp.tipo_venta.reset();
                  me.Cmp.id_codigo_aita.reset();
                  me.Cmp.id_office.reset();
                  me.Cmp.id_canal.store.baseParams.id_lugar_pais = me.Cmp.id_lugar_pais.getValue();
                  // me.Cmp.id_canal.store.baseParams.tipo_reporte = me.Cmp.tipo_reporte.getValue();
                  me.Cmp.id_canal.store.baseParams.id_lugar_ciudad = rec.data.city_code;
                  me.Cmp.id_canal.modificado = true;
          },this);

          this.Cmp.id_canal.on('select',function(cmp, rec, indice){
                  me.Cmp.tipo_venta.reset();
                  me.Cmp.id_codigo_aita.reset();
                  me.Cmp.id_office.reset();
                  me.Cmp.tipo_venta.store.baseParams.id_lugar_pais = me.Cmp.id_lugar_pais.getValue();
                  me.Cmp.tipo_venta.store.baseParams.id_lugar_ciudad = me.Cmp.id_lugar_ciudad.getValue();
                  me.Cmp.tipo_venta.store.baseParams.id_canal = rec.data.sale_channel;
                  me.Cmp.tipo_venta.modificado = true;
          },this);

          this.Cmp.tipo_venta.on('select',function(cmp, rec, indice){
                  me.Cmp.id_codigo_aita.reset();
                  me.Cmp.id_office.reset();
                  me.Cmp.id_codigo_aita.store.baseParams.id_lugar_pais = me.Cmp.id_lugar_pais.getValue();
                  me.Cmp.id_codigo_aita.store.baseParams.id_lugar_ciudad = me.Cmp.id_lugar_ciudad.getValue();
                  me.Cmp.id_codigo_aita.store.baseParams.id_canal = me.Cmp.id_canal.getValue();
                  me.Cmp.id_codigo_aita.store.baseParams.tipo_venta = rec.data.tipo_pos;
                  me.Cmp.id_codigo_aita.modificado = true;
          },this);

          this.Cmp.id_codigo_aita.on('select',function(cmp, rec, indice){
                  me.Cmp.id_office.reset();
                  me.Cmp.id_office.store.baseParams.id_lugar_pais = me.Cmp.id_lugar_pais.getValue();
                  me.Cmp.id_office.store.baseParams.id_lugar_ciudad = me.Cmp.id_lugar_ciudad.getValue();
                  me.Cmp.id_office.store.baseParams.id_canal = me.Cmp.id_canal.getValue();
                  me.Cmp.id_office.store.baseParams.tipo_venta = me.Cmp.tipo_venta.getValue();
                  me.Cmp.id_office.store.baseParams.id_codigo_aita = rec.data.iata_code;
                  me.Cmp.id_office.modificado = true;
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

                var arg =  '/Control Ingresos/Reporte+de+Venta&rs:Command=Render&from=' + this.Cmp.fecha_ini.getValue().format('Y-m-d');
                    arg = arg + "&to=" + this.Cmp.fecha_fin.getValue().format('Y-m-d');
                    arg = arg + "&country=" + this.Cmp.id_lugar_pais.getValue();
                    arg = arg + "&city=" + this.Cmp.id_lugar_ciudad.getValue();
                    arg = arg + "&channel=" + this.Cmp.id_canal.getValue();
                    arg = arg + "&typePOS=" + this.Cmp.tipo_venta.getValue();
                    arg = arg + "&iataCode=" + this.Cmp.id_codigo_aita.getValue();
                    arg = arg + "&officeID=" + this.Cmp.id_office.getValue();
                    arg = arg + "&transaction=" + this.Cmp.tipo_documento.getValue();
                    arg = arg + "&rs:Format=EXCEL";

                    console.log("resp", arg);
                    window.open('http://172.17.110.5:8082/BoAReportServer/Pages/ReportViewer.aspx?'+arg, '_blank');
    			}
    		}
    })
</script>
