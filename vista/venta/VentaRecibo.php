<?php
/**
*@package pXP
*@file gen-SistemaDist.php
*@author  (rarteaga)
*@date 20-09-2011 10:22:05
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<style>
.button-impcarta{
	background-image: url('../../../lib/imagenes/icono_inc/inc_printer.png');
	background-repeat: no-repeat;
	filter: saturate(250%);
	background-size: 50%;
}
.button-anular-red{
    background-image: url('../../../lib/imagenes/icono_dibu/anulared.png');
    background-repeat: no-repeat;
    filter: saturate(250%);
    background-size: 80%;
}
</style>
<script>
Phx.vista.VentaRecibo = {
    bsave:false,
    bedit:false,
    bdel:false,
    require:'../../../sis_ventas_facturacion/vista/venta/ReciboLista.php',
    requireclase:'Phx.vista.ReciboLista',
    title:'ReciboLista',
    nombreVista: 'VentaRecibo',
    grupoDateFin: [1],
		mgs_user: '<p>Estimado Usuario Registros Anteriores a la Fecha Solo Pueden Ser Consultados </p>',

    constructor: function(config) {
        this.maestro=config.maestro;
        Phx.vista.VentaRecibo.superclass.constructor.call(this,config);
    } ,
    arrayDefaultColumHidden:['estado_reg','usuario_ai',
    'fecha_reg','fecha_mod','usr_reg','usr_mod','nro_factura','excento','fecha','cod_control','nroaut'],
    successGetVariables :function (response,request) {
    	Phx.vista.VentaRecibo.superclass.successGetVariables.call(this,response,request);
  		this.store.baseParams.pes_estado = 'registro';
			var date_f = new Date();
			this.store.baseParams.fecha = date_f.dateFormat('d/m/Y');


        this.addButton('anular',{grupo:[1],text:'Anular',iconCls: 'button-anular-red',disabled:true,handler:this.anular,tooltip: '<b>Anular la venta</b>',hidden:true});
				this.addButton('ant_estado',{grupo:[0],text :'Regresar a Emision',iconCls : 'batras',disabled: true,handler : this.regresarEmision,tooltip : '<b>Regresar al counter para la respectiva corrección</b>'});
				this.addButton('completar_pago',{grupo:[0],text :'Completar Pago',iconCls : 'bmoney',disabled: true,handler : this.completar_pago,tooltip : '<b>Formulario para completar el pago</b>'});
        this.addButton('btnImprimir',
            {   grupo:[1,2],
                text: 'Imprimir Rollo',
                iconCls: 'bprint',
                disabled: true,
                handler: this.imprimirNota,
                tooltip: '<b>Imprimir Recibo</b><br/>Imprime el Recibo de la venta'
            }
        );

        /*Aumentando el boton para imprimir la factura*/
    		this.addButton('btnChequeoDocumentosWf',{
    				text: 'Impresion Carta',
    				grupo: [1],
    				iconCls: 'button-impcarta',
    				disabled: true,
    				handler: this.loadCheckDocumentosRecWf,
    				tooltip: '<b>Documentos </b><br/>Subir los documetos requeridos.'
    		});
        /**********************************************/

        this.addButton('asociar_boletos',
    				{   grupo:[1],
    						text: 'Asociar Boletos',
    						iconCls: 'bchecklist',
    						disabled: true,
    						handler: this.AsociarBoletos,
    						tooltip: '<b>Asociar Boletos</b><br/>Asocia Boletos a la factura emitida.'
    				}
    		);
				this.addButton('diagrama_gantt',{grupo:[0,1,2],text:'Gant',iconCls: 'bgantt',disabled:true,handler:this.diagramGantt,tooltip: '<b>Diagrama Gantt de la venta</b>'});

        this.campo_fecha = new Ext.form.DateField({
	        name: 'fecha_reg',
	        grupo: this.bactGroups,
				fieldLabel: 'Fecha',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				format: 'd/m/Y',
				hidden : false
	    });
      this.punto_venta = new Ext.form.Label({
          name: 'punto_venta',
          grupo: this.bactGroups,
          fieldLabel: 'P.V.',
          readOnly:true,
          anchor: '150%',
          gwidth: 150,
          format: 'd/m/Y',
          hidden : false,
          //style: 'font-size: 170%; font-weight: bold; background-image: none;'
          style: {
            fontSize:'170%',
            fontWeight:'bold',
            color:'red',
            marginLeft:'20px'
          }
      });

      //this.load({params:{start:0, limit:this.tam_pag}});


		this.tbar.addField(this.campo_fecha);
    this.tbar.addField(this.punto_venta);
		var datos_respuesta = JSON.parse(response.responseText);
    	var fecha_array = datos_respuesta.datos.fecha.split('/');
    	this.campo_fecha.setValue(new Date(fecha_array[2],parseInt(fecha_array[1]) - 1,fecha_array[0]));
        //this.campo_fecha.hide();

        this.finCons = true;

        this.campo_fecha.on('select',function(value){
    		this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');
    		this.load();
    	},this);

  	},
  	gruposBarraTareas:[{name:'registro',title:'<H1 style="font-size:12px;" align="center"><i style="color:#FFAE00; font-size:15px;" class="fa fa-eraser"></i> Registro</h1>',grupo:0,height:0},
											 {name:'caja',title:'<H1 style="font-size:12px;" align="center"><i style="color:green; font-size:15px;" class="fa fa-usd"></i> En Caja</h1>',grupo:0,height:0},
                       {name:'finalizado',title:'<H1 style="font-size:12px;" align="center"><i style="color:#B61BFF; font-size:15px;" class="fa fa-check-circle"></i> Finalizados</h1>',grupo:1,height:0},
                       {name:'anulado',title:'<H1 style="font-size:12px;" align="center"><i style="color:red; font-size:15px;" class="fa fa-ban"></i> Anulados</h1>',grupo:2,height:0}
                       ],


    actualizarSegunTab: function(name, indice){
        if(this.finCons){
        	 	 this.store.baseParams.fecha = this.campo_fecha.getValue().dateFormat('d/m/Y');
             this.store.baseParams.pes_estado = name;
             this.store.baseParams.interfaz = 'vendedor';

						 if (name=='registro') {
									this.getBoton('new').setVisible(true);
									this.getBoton('completar_pago').setVisible(false);
									this.getBoton('ant_estado').setVisible(false);
									this.getBoton('diagrama_gantt').setVisible(false);
									this.getBoton('completar_pago').setVisible(false);
									this.getBoton('ant_estado').setVisible(false);
						 }else if(name=='caja'){
							 		this.getBoton('new').setVisible(false);
								  this.getBoton('completar_pago').setVisible(true);
								  this.getBoton('ant_estado').setVisible(true);
						 }else if(name=='finalizado'){
							 this.getBoton('completar_pago').setVisible(false);
							 this.getBoton('ant_estado').setVisible(false);
							 this.getBoton('new').setVisible(false);
						 }else if(name=='anulado'){
							 this.getBoton('completar_pago').setVisible(false);
							 this.getBoton('ant_estado').setVisible(false);
							 this.getBoton('new').setVisible(false);
						 }
             this.load({params:{start:0, limit:this.tam_pag}});
           }
    },
    // beditGroups: [0],
    // bdelGroups:  [0],
    bactGroups:  [0,1,2],
    btestGroups: [0],
    bexcelGroups: [0,1,2],
    preparaMenu:function()
    {   var rec = this.sm.getSelected();

        if (rec.data.estado == 'caja') {
              this.getBoton('ant_estado').enable();
							this.getBoton('completar_pago').enable();
							this.getBoton('btnImprimir').setVisible(false);
							this.getBoton('btnChequeoDocumentosWf').setVisible(false);
							this.getBoton('asociar_boletos').setVisible(false);
        }

        if (rec.data.estado == 'finalizado') {
              this.getBoton('anular').enable();
        }
        this.getBoton('btnImprimir').enable();
        this.getBoton('diagrama_gantt').enable();
        this.getBoton('asociar_boletos').enable();
        this.getBoton('btnChequeoDocumentosWf').enable();
        Phx.vista.VentaRecibo.superclass.preparaMenu.call(this);
    },
    liberaMenu:function()
    {
				this.getBoton('btnImprimir').disable();
        this.getBoton('diagrama_gantt').disable();
        this.getBoton('anular').disable();
        // this.getBoton('sig_estado').disable();
        this.getBoton('asociar_boletos').disable();
        this.getBoton('btnChequeoDocumentosWf').disable();
				this.getBoton('completar_pago').disable();
				this.getBoton('ant_estado').disable();
				this.getBoton('btnChequeoDocumentosWf').setVisible(false);

        Phx.vista.VentaRecibo.superclass.liberaMenu.call(this);
    },

    loadCheckDocumentosRecWf:function() {
					var rec=this.sm.getSelected();
					rec.data.nombreVista = this.nombreVista;
					Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
							'Chequear documento del WF',
							{
									width:'90%',
									height:500
							},
							rec.data,
							this.idContenedor,
							'DocumentoWf'
					)
			},

    AsociarBoletos: function(){

                var rec = {maestro: this.sm.getSelected().data}
                console.log('VALOR',	rec);
                Phx.CP.loadWindows('../../../sis_ventas_facturacion/vista/venta/AsociarBoletos.php',
                    '<center><h1 style="font-size:25px; color:#0E00B7; text-shadow: -1px -1px 1px rgba(255,255,255,.1), 1px 1px 1px rgba(0,0,0,.5);"> <img src="../../../lib/imagenes/icono_dibu/dibu_zoom.png" style="float:center; vertical-align: middle;"> Asociar Boletos</h1></center>',
                    {
                        width:1200,
                        height:600
                    },
                    rec,
                    this.idContenedor,
                    'AsociarBoletos');

            },
		regresarEmision:function(){
			var d = this.sm.getSelected().data;
				Ext.Ajax.request({
	 					url:'../../sis_ventas_facturacion/control/Cajero/regresarCounter',
	 					params:{id_estado_wf_act:d.id_estado_wf,
	 									id_proceso_wf_act:d.id_proceso_wf,
	 								  tipo:'facturacion'},
	 					success:this.successWizard,
	 					failure: this.conexionFailure,
	 					timeout:this.timeout,
	 					scope:this
	 			});

     },
		 successWizard:function(resp){
         var rec=this.sm.getSelected();
         var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
				 Phx.CP.getPagina(this.idContenedor).reload();
    },

		completar_pago : function () {
				var d = this.sm.getSelected().data;
        var date = new Date()

        var dia = date.getDate();
        var mes = date.getMonth() + 1;
        var ano = date.getFullYear();

        if(mes < 10){
          if(dia < 10){
            var fecha_hoy = "0"+dia + "/0" + mes + "/" + ano
          }else{
            var fecha_hoy = dia + "/0" + mes + "/" + ano
          }
        }else{
          var fecha_hoy = dia + "/" + mes + "/" + ano
        }

				if((d.fecha.dateFormat('d/m/Y') != fecha_hoy) && (this.tipo_usuario != 'administrador_facturacion')){
					Ext.Msg.show({
							title: 'Alerta',
							msg: this.mgs_user,
							buttons: Ext.Msg.OK,
							width: 512,
							icon: Ext.Msg.INFO
					});
				}else{
					this.openForm('edit', this.sm.getSelected());
				}
			},

			// openForm : function (tipo, record) {
	    // 	var me = this;
			// 	if (this.variables_globales.codigo_moneda_base=='USD') {
			// 		this.titulo_recibo_oficial = '<center><img src="../../../lib/imagenes/facturacion/ReciboIcon.png" style="width:35px; vertical-align: middle;"> <span style="vertical-align: middle; font-size:30px; text-shadow: 3px 0px 0px #000000;"> OFFICIAL RECEIPT</span></center>';
			// 	} else {
			// 		this.titulo_recibo_oficial = '';
			// 	}
	    //        me.objSolForm = Phx.CP.loadWindows(this.formUrl,this.titulo_recibo_oficial,
	    //                                 {
	    //                                     modal:true,
	    //                                     width:'100%',
	    //                                     height:'100%',
			// 																		onEsc: function() {
			// 																    var me = this;
			// 																    Ext.Msg.confirm(
			// 																        'Mensaje de Confirmación',
			// 																        'Quiere cerrar el Formulario?, se perderán los datos que no han sido Guardados',
			// 																        function(btn) {
			// 																            if (btn == 'yes')
			// 																                me.hide();
			// 																        }
			// 																        );
			// 																},
	    //                                 }, {data:{objPadre : me,
	    //                                 		tipo_form : tipo,
	    //                                 		datos_originales: record,
			// 		   							readOnly : this.readOnly}
	    //                                 },
	    //                                 this.idContenedor,
	    //                                 this.formClass,
	    //                                 {
	    //                                     config:[{
	    //                                               event:'successsave',
	    //                                               delegate: this.onSaveForm,
			//
	    //                                             }],
			//
	    //                                     scope:this
	    //                                  });
	    // },
};
</script>
