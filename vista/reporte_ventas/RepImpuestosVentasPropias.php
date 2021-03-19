<script>
    Phx.vista.RepImpuestosVentasPropias = Ext.extend(Phx.frmInterfaz, {

        Atributos : [
          {
          config : {
              name : 'tipo_reporte',
              fieldLabel : 'REPORTE',
              allowBlank : false,
              triggerAction : 'all',
              lazyRender : true,
              gwidth : 100,
              anchor : '100%',
              mode : 'local',
              emptyText:'...',
              style:'margin-bottom: 10px;',
              store: new Ext.data.ArrayStore({
                  id: '',
                  fields: [
                      'key',
                      'value'
                  ],
                  data: [
                      ['repo_inp', 'Reporte Impuestos Venta Propia'],
                      ['repo_bsp', 'Reporte BSP']
                  ]
              }),
              valueField: 'key',
              displayField: 'value'
          },
          type : 'ComboBox',
          id_grupo : 0,
          grid : true
        },
        {
            config:{
                name: 'box_hidden',
                fieldLabel: '',
                disabled:true,
                style:'margin-bottom: 10px;',
                // hidden: 'hidden'
            },
            type:'TextField',
            id_grupo:1,
            form:true
        },
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
  	                queryDelay: 1000,
  	                gwidth: 250,
                    listWidth: 450,
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
             config: {
               name: 'id_moneda',
               fieldLabel: 'Moneda',
               allowBlank: false,
               emptyText: 'Moneda...',
               store: new Ext.data.JsonStore({
                   url: '../../sis_parametros/control/Moneda/listarMoneda',
                   id: 'id_moneda',
                   root: 'datos',
                   sortInfo: {
                       field: 'moneda',
                       direction: 'ASC'
                   },
                   totalProperty: 'total',
                   fields: ['id_moneda', 'moneda', 'codigo', 'tipo_moneda', 'codigo_internacional'],
                   remoteSort: true,
                   baseParams: {_adicionar : 'si', par_filtro: 'moneda#codigo', filtrar: 'si'}
               }),
               valueField: 'id_moneda',
               displayField: 'moneda',
               tpl: '<tpl for="."><div class="x-combo-list-item"><p><font color="green"><b>{moneda}</b></font></p><p>Codigo:<b>{codigo}</b></p> <p>Codigo Internacional:<b>{codigo_internacional}</b></p></div></tpl>',
               hiddenName: 'id_moneda',
               forceSelection: true,
               typeAhead: false,
               triggerAction: 'all',
               lazyRender: true,
               mode: 'remote',
               pageSize: 10,
               queryDelay: 1000,
               width: 300,
               listWidth: '280',
               resizable: true,
               minChars: 2,
               style:'margin-bottom: 10px;'
             },
             type: 'ComboBox',
             valorInicial: 'TODOS' ,
             id_grupo: 0,
             form: true
          },
           {
           config : {
               name : 'transaccion',
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
           valorInicial: 'TODOS' ,
           id_grupo : 1,
           grid : true
         },
       {
       config : {
           name : 'tipo_fecha',
           fieldLabel : 'TIPO',
           allowBlank : false,
           triggerAction : 'all',
           lazyRender : true,
           gwidth : 100,
           anchor : '100%',
           mode : 'local',
           emptyText:'...',
           style:'margin-bottom: 10px;',
           store: new Ext.data.ArrayStore({
               id: '',
               fields: [
                   'key',
                   'value'
               ],
               data: [
                   ['tipo_f_e', 'Emision'],
                   ['tipo_f_p', 'Proceso']
               ]
           }),
           valueField: 'key',
           displayField: 'value'
       },
       type : 'ComboBox',
       id_grupo : 0,
       grid : true
     },
     {
         config:{
             name: 'box_hidden_1',
             fieldLabel: '',
             disabled:true,
             style:'margin-bottom: 10px;'
         },
         type:'TextField',
         id_grupo:1,
         form:true
     },
        ],


        title : 'Reporte de Ventas',
        topBar : true,
        botones : false,
        labelSubmit : 'Generar',
        tooltipSubmit : '<b>Reporte Revision Boletos</b>',
        constructor : function(config) {
            Phx.vista.RepImpuestosVentasPropias.superclass.constructor.call(this, config);
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
          this.Cmp.box_hidden.el.dom.style.border='none';
          this.Cmp.box_hidden_1.el.dom.style.border='none';
          var me = this;
          Ext.Ajax.request({
              url:'../../sis_workflow/control/NumTramite/usuarioAdminTF',
              params:{data:''},
              success:function(resp){
                  var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                  me.usuario = reg.ROOT.datos.funcionario;
              },
              failure: this.conexionFailure,
              timeout:this.timeout,
              scope:this
          });


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

                if (this.Cmp.tipo_reporte.getValue() != 'repo_inp'){
                  var arg =  '/ReportServer?/BoaDwRepIngresos/RepImpuestosVentasPropiasBSP&rs:Command=Render&FechaIni=' + this.Cmp.fecha_ini.getValue().format('Y-m-d');
                      arg = arg + "&FechaFin=" + this.Cmp.fecha_fin.getValue().format('Y-m-d');
                      arg = arg + "&EstacionVenta=" + this.Cmp.id_lugar_pais.getValue()
                      arg = arg + "&Ciudad=" + this.Cmp.id_lugar_ciudad.getValue();
                      arg = arg + "&CanalVenta=" + this.Cmp.id_canal.getValue();
                      arg = arg + "&CodigoIata=" + this.Cmp.id_codigo_aita.getValue();
                      arg = arg + "&Moneda=" + this.Cmp.id_moneda.getValue();
                      arg = arg + "&Transaccion="+ this.Cmp.transaccion.getValue();
                }else{
                  var arg = '/ReportServer/Pages/ReportViewer.aspx?/BoaDwRepIngresos/RepImpuestosVentasPropias&rs:Command=Render&FechaIni=' + this.Cmp.fecha_ini.getValue().format('Y-m-d');
                    arg = arg + "&FechaFin=" + this.Cmp.fecha_fin.getValue().format('Y-m-d');
                    arg = arg + "&EstacionVenta=" + this.Cmp.id_lugar_pais.getValue()
                    arg = arg + "&Ciudad=" + this.Cmp.id_lugar_ciudad.getValue();
                    arg = arg + "&CanalVenta=" + this.Cmp.id_canal.getValue();
                    arg = arg + "&TipoAgencia=" + this.Cmp.tipo_venta.getValue();
                    arg = arg + "&CodigoIata=" + this.Cmp.id_codigo_aita.getValue();
                    arg = arg + "&OficinaVentas=" + this.Cmp.id_office.getValue();
                    arg = arg + "&Moneda=" + this.Cmp.id_moneda.getValue();
                    arg = arg + "&Transaccion="+  this.Cmp.transaccion.getValue();
                }

                if (this.Cmp.tipo_fecha.getValue() == 'tipo_f_p'){
                    arg = arg + '&Tipo=P';
                }else{
                    arg = arg + '&Tipo=E';
                }
                    arg = arg + "&usr= " +this.usuario+ "&rs:Format=EXCEL";
                  console.log("datatsss",arg);
                  // window.open('http://10.150.0.22:8082'+arg, '_blank');

    			}
    		}
    })
</script>
