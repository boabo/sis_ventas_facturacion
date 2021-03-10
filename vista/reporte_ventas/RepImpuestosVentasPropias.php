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
                          baseParams: {tipos: "''pais''", par_filtro: 'nombre',_adicionar:'si'}
                      }),
                  valueField: 'id_lugar',
                  displayField: 'nombre',
                  gdisplayField: 'codigo',
                  hiddenName: 'id_lugar',
                  tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p></div></tpl>',
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
              type: 'ComboBox',
              id_grupo: 0,
              form: true
          },
          {
              config: {
                  name: 'id_catalogo',
                  fieldLabel: 'CANAL DE VENTA',
                  allowBlank: false,
                  emptyText: '',
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
                          baseParams: {cod_catalogo: 'canal_venta', par_filtro:'c.codigo', _adicionar:'si'}
                      }),
                  valueField: 'codigo',
                  displayField: 'codigo',
                  gdisplayField: 'codigo',
                  hiddenName: 'id_catalogo',
                  // tpl:'<tpl for="."><div class="x-combo-list-item"><p style="text-transform: uppercase;"><b>{codigo}</b></p></div></tpl>',
                  triggerAction: 'all',
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
              valorInicial: 'Todos' ,
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
  	                allowBlank: false,
                    disabled: false,
  	                emptyText: '',
  	                store: new Ext.data.JsonStore({
  	                    url: '../../sis_ventas_facturacion/control/ReporteVentas/listarPuntoVentaRbol',
  	                    id: 'codigo',
  	                    root: 'datos',
  	                    sortInfo: {
  	                        field: 'codigo',
  	                        direction: 'ASC'
  	                    },
  	                    totalProperty: 'total',
  	                    fields: ['codigo'],
  	                    remoteSort: true,
  	                    baseParams: {_adicionar : 'si', offi_id:'si', par_filtro: 'p.codigo'}
  	                }),
  	                valueField: 'codigo',
  	                displayField: 'codigo',
  	                gdisplayField: 'codigo',
  	                hiddenName: 'codigo',
  	                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{codigo}</b></p></div></tpl>',
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
                  	hidden : false,
                    style:'margin-bottom: 10px;'

  	            },
  	            type: 'ComboBox',
                valorInicial: 'Todos' ,
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
                          // url: '../../sis_parametros/control/Lugar/listarLugar',
                          url: '../../sis_ventas_facturacion/control/ReporteVentas/subLugarPais',
                          id: 'id_lugar',
                          root: 'datos',
                          sortInfo: {
                              field: 'nombre',
                              direction: 'ASC'
                          },
                          totalProperty: 'total',
                          fields: ['id_lugar', 'id_lugar_fk', 'codigo', 'nombre', 'tipo', 'sw_municipio', 'sw_impuesto'],
                          remoteSort: true,
                          baseParams: { par_filtro: 'nombre#codigo',_adicionar:'si'}
                      }),
                  valueField: 'id_lugar',
                  displayField: 'nombre',
                  gdisplayField: 'lugar_depto',
                  hiddenName: 'id_lugar',
                  tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b> <span style="color:green;">({codigo})</span></p></div></tpl>',
                  triggerAction: 'all',
                  lazyRender: true,
                  mode: 'remote',
                  pageSize: 5,
                  queryDelay: 500,
                  gwidth: 250,
                  width:300,
                  forceSelection: true,
                  minChars: 2,
                  style:'margin-bottom: 10px;'
              },
              type: 'ComboBox',
              valorInicial: 'Todos' ,
              id_grupo: 1,
              form: true
          },
          {
              config: {
                  name: 'tipo',
                  fieldLabel: 'TIPO VENTA',
                  allowBlank: false,
                  emptyText: '',
                  store: new Ext.data.JsonStore(
                      {
                          url: '../../sis_ventas_facturacion/control/ReporteVentas/listarPuntoVentaTipo',
                          id: 'tipo',
                          root: 'datos',
                          sortInfo: {
                              field: 'tipo',
                              direction: 'ASC'
                          },
                          totalProperty: 'total',
                          fields: ['tipo', 'codigo'],
                          remoteSort: true,
                          baseParams: {_adicionar:'si', par_filtro:'p.tipo'}
                      }),
                  valueField: 'tipo',
                  displayField: 'tipo',
                  gdisplayField: 'tipo',
                  hiddenName: 'tipo',
                  triggerAction: 'all',
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
              valorInicial: 'Todos' ,
              id_grupo: 1,
              form: true
          },
          {
  			config: {
  	                name: 'id_punto_venta_1',
  	                fieldLabel: 'OFICINA DE VENTA',
  	                allowBlank: false,
                    disabled: false,
  	                emptyText: '',
  	                store: new Ext.data.JsonStore({
  	                    url: '../../sis_ventas_facturacion/control/ReporteVentas/listarPuntoVentaOfficeId',
                        // url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
  	                    id: 'id_punto_venta',
  	                    root: 'datos',
  	                    sortInfo: {
  	                        field: 'office_id',
  	                        direction: 'ASC'
  	                    },
  	                    totalProperty: 'total',
  	                    fields: ['id_punto_venta','office_id'],
  	                    remoteSort: true,
  	                    baseParams: {_adicionar : 'si', par_filtro:'office_id'}
  	                }),
  	                valueField: 'id_punto_venta',
  	                displayField: 'office_id',
  	                gdisplayField: 'office_id',
  	                hiddenName: 'id_punto_venta',
  	                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{office_id}</b></p></div></tpl>',
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
                  	hidden : false

  	            },
  	            type: 'ComboBox',
                valorInicial: 'Todos' ,
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
             valorInicial: 'Todos' ,
             id_grupo: 0,
             form: true
          },
           // {
           //     config: {
           //         name: 'id_moneda',
           //         origen: 'MONEDA',
           //         allowBlank: true,
           //         fieldLabel: 'Monenda',
           //         gdisplayField: 'desc_moneda',
           //         gwidth: 250,
           //         anchor:'100%',
           //         style:'margin-bottom: 10px;'
           //     },
           //     type: 'ComboRec',
           //     id_grupo: 0,
           //     form: true
           // },
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
                       ['todos', 'Todos'],
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
           valorInicial: 'Todos' ,
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
        //ActSave : '../../sis_contabilidad/control//',

        topBar : true,
        botones : false,
        labelSubmit : 'Generar',
        tooltipSubmit : '<b>Reporte Revision Boletos</b>',
        constructor : function(config) {
            Phx.vista.RepImpuestosVentasPropias.superclass.constructor.call(this, config);
            this.init();
            this.country='',this.city='',this.channel='',this.typePOS='',this.iataCode='',this.officeID='', this.tipo_canal='', this.code_iata='',this.moneda='',this.transaction='',this.usuario='';
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

          // this.Cmp.tipo_reporte.on('select', function(cmp, rec, i) {
          //     if  (rec.data.key == 'repo_bsp'){
          //         this.Cmp.box_hidden.setValue(rec.data.key)
          //     }else{
          //         this.Cmp.box_hidden.setValue('')
          //     }
          //     this.Cmp.id_catalogo.store.baseParams.input_text = this.Cmp.box_hidden.getValue();
          //     this.Cmp.id_catalogo.reset();
          //     this.Cmp.id_catalogo.modificado=true;
          // },this);
          this.Cmp.id_lugar.store.load({params:{start:0, limit:100}, scope:this, callback: function (param,op,suc) {

                  this.Cmp.id_lugar.setValue(param[7].data.id_lugar);
                  this.Cmp.id_lugar_fk.reset();
                  this.Cmp.id_lugar_fk.store.baseParams.id_lugar_fk = param[7].data.id_lugar;
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

                        this.Cmp.id_catalogo.reset();
                        this.Cmp.id_catalogo.store.baseParams.id_lugar_fk = rec.data.codigo.toUpperCase();
                        this.Cmp.id_catalogo.modificado = true;
                        this.Cmp.id_catalogo.setValue('');
                        this.Cmp.id_punto_venta_1.reset();
                        this.Cmp.id_punto_venta_1.modificado = true;
                        this.Cmp.id_punto_venta_1.setValue('');
                        this.Cmp.tipo.reset();
                        this.Cmp.tipo.modificado = true;
                        this.Cmp.tipo.setValue('');
                        this.Cmp.id_punto_venta.reset();
                        this.Cmp.id_punto_venta.modificado = true;
                        this.Cmp.id_punto_venta.setValue('');

          },this);

          this.Cmp.id_catalogo.on('select',function(cmp, rec, indice){

                        this.Cmp.tipo.reset();
                        this.Cmp.tipo.store.baseParams.tipo = this.Cmp.id_catalogo.getValue();
                        this.Cmp.tipo.store.baseParams.id_lugar_fk = this.city;
                        this.Cmp.tipo.modificado = true;
          },this);

          this.Cmp.tipo.on('select',function(cmp, rec, indice){
                        this.typePOS = this.typePOS +','+ rec.data.tipo.toUpperCase();
                        this.Cmp.id_punto_venta.reset();
                        this.Cmp.id_punto_venta.store.baseParams.tipoVenta = this.Cmp.tipo.getValue();
                        this.Cmp.id_punto_venta.store.baseParams.id_lugar_fk = this.city;
                        this.Cmp.id_punto_venta.store.baseParams.canal = this.Cmp.id_catalogo.getValue();
                        this.Cmp.id_punto_venta.modificado = true;
                        this.Cmp.id_punto_venta.setDisabled(false);
          },this);

          this.Cmp.id_punto_venta.on('select',function(cmp, rec, indice){
                        this.iataCode = rec.data.codigo.toUpperCase();
                        // this.Cmp.id_punto_venta_1.setDisabled(false);

                        this.Cmp.id_punto_venta_1.reset();
                        this.Cmp.id_punto_venta_1.store.baseParams.tipoVenta = this.Cmp.tipo.getValue();
                        this.Cmp.id_punto_venta_1.store.baseParams.id_lugar_fk = this.city;
                        this.Cmp.id_punto_venta_1.store.baseParams.canal = this.Cmp.id_catalogo.getValue();
                        this.Cmp.id_punto_venta_1.store.baseParams.code_iata = this.iataCode;
                        this.Cmp.id_punto_venta_1.modificado = true;
          },this);

          this.Cmp.id_punto_venta_1.on('select',function(cmp, rec, indice){
                        this.officeID = rec.data.office_id.toUpperCase();
          },this);


          this.Cmp.id_moneda.on('select',function(cmp, rec, indice){
                        this.moneda = rec.data.codigo_internacional.toUpperCase();
          },this);

          this.Cmp.tipo_documento.on('select',function(cmp, rec, indice){
                        this.transaction = rec.data.key.toUpperCase();
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

                this.channel='TODOS';
                this.city = (this.city=='')?'TODOS':this.city;
                // unicos = (this.channel=='')?'TODOS':unicos.substring(1);
                this.typePOS = (this.typePOS=='')?'TODOS':this.Cmp.tipo.getValue().toUpperCase();
                this.iataCode = (this.iataCode=='')?'TODOS':this.iataCode;
                this.officeID = (this.officeID=='')?'TODOS':this.officeID;
                this.moneda = (this.moneda=='' || this.moneda=='TODOS')?0:this.moneda.toUpperCase();
                this.transaction = (this.transaction=='')?'TODOS':this.transaction.toUpperCase();

                if (this.Cmp.tipo_reporte.getValue() != 'repo_inp'){
                  var arg =  '/ReportServer?/BoaDwRepIngresos/RepImpuestosVentasPropiasBSP&rs:Command=Render&FechaIni=' + this.Cmp.fecha_ini.getValue().format('Y-m-d');
                      arg = arg + "&FechaFin=" + this.Cmp.fecha_fin.getValue().format('Y-m-d');
                      arg = arg + "&EstacionVenta=" + this.country;
                      arg = arg + "&Ciudad=" + this.city;
                      arg = arg + "&CanalVenta=" + this.channel;
                      arg = arg + "&CodigoIata=" + this.iataCode;
                      arg = arg + "&Moneda=" + this.moneda;
                      arg = arg + "&Transaccion="+this.transaction;
                }else{
                  var arg = '/ReportServer/Pages/ReportViewer.aspx?/BoaDwRepIngresos/RepImpuestosVentasPropias&rs:Command=Render&FechaIni=' + this.Cmp.fecha_ini.getValue().format('Y-m-d');
                    arg = arg + "&FechaFin=" + this.Cmp.fecha_fin.getValue().format('Y-m-d');
                    arg = arg + "&EstacionVenta=" + this.country;
                    arg = arg + "&Ciudad=" + this.city;
                    arg = arg + "&CanalVenta=" + this.channel;
                    arg = arg + "&TipoAgencia=" + this.typePOS;
                    arg = arg + "&CodigoIata=" + this.iataCode;
                    arg = arg + "&OficinaVentas=" + this.officeID;
                    arg = arg + "&Moneda=" + this.moneda;
                    arg = arg + "&Transaccion="+this.transaction;
                }

                if (this.Cmp.tipo_fecha.getValue() == 'tipo_f_p'){
                    arg = arg + '&Tipo=E';
                }else{
                    arg = arg + '&Tipo=P';
                }
                    arg = arg + "&usr= " +this.usuario+ "&rs:Format=EXCEL";
                  console.log("datatsss",arg);
                  window.open('http://10.150.0.22:8082'+arg, '_blank');

    			}
    		}
    })
</script>
