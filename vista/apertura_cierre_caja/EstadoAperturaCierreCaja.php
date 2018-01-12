<?php
/**
 *@package pXP
 *@file gen-AperturaCierreCaja.php
 *@author  (jrivera)
 *@date 07-07-2016 14:16:20
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.EstadoAperturaCierreCaja=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                Phx.vista.EstadoAperturaCierreCaja.superclass.constructor.call(this,config);
                this.init();
                this.load({params:{start:0, limit:this.tam_pag}});
                this.addButton('replicar_aux',{
                    grupo: [0,1,2,3,4],
                    text: 'Replicar',
                    iconCls: 'bfolder',
                    disabled: false,
                    handler: this.replicarAux,
                    tooltip: '<b>Permite replicar un auxiliar recien registrado en la BD Ingresos</b>',
                    scope:this
                });
            },

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
                    config:{
                        name: 'fecha_apertura_cierre',
                        fieldLabel: 'Fecha ',
                        gwidth: 110,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters: { pfiltro:'b.fecha_apertura_cierre', type:'date'},
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'estado',
                        fieldLabel: 'Estado',
                        gwidth: 100,
                        renderer: function(value, p, record) {
                            if (record.data['estado'] == 'abierto') {
                                return '<tpl for="."><p><font color="green"><b>'+record.data['estado']+'</b></font></p></tpl>';
                            }else{
                                return '<tpl for="."><p <b><font color="black">'+record.data['estado']+'</font></b></p></tpl>';
                            }
                        }
                    },
                    type:'TextField',
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'abierto_co',
                        fieldLabel: 'Abiertos',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 50,
                        maxLength:4,
                        renderer: function(value, p, record) {
                            if(record.data.abierto_co > 0){
                                return String.format('<div ext:qtip="Optimo"><b><font color="red">{0}</font></b><br></div>', value);
                            }else{
                                return String.format('<div ext:qtip="Optimo"><b><font color="black">{0}</font></b><br></div>', value);

                            }

                        }
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'cerrado_co',
                        fieldLabel: 'Cerrado',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 50,
                        maxLength:4,
                        renderer: function(value, p, record) {
                            if(record.data.cerrado_co  < 1){
                                return String.format('<div ext:qtip="Optimo"><b><font color="red">{0}</font></b><br></div>', value);
                            }else{
                                return String.format('<div ext:qtip="Optimo"><b><font color="black">{0}</font></b><br></div>', value);

                            }

                        }
                    },
                    type:'NumberField',
                    id_grupo:1,
                    grid:true,
                    form:false
                }

            ],
            tam_pag:50,
            title:'Apertura de Caja Estado',
            ActList:'../../sis_ventas_facturacion/control/AperturaCierreCaja/EstadoApertura',
            id_store:'fecha_apertura_cierre',
            fields: [
                {name:'fecha_apertura_cierre', type: 'date',dateFormat:'Y-m-d'},
                {name:'abierto_co', type: 'numeric'},
                {name:'cerrado_co', type: 'numeric'},
                {name:'estado', type: 'string'}

            ],
            sortInfo:{
                field: 'fecha_apertura_cierre',
                direction: 'desc'
            },
        tabsouth :[
        {
            url:'../../../sis_ventas_facturacion/vista/apertura_cierre_caja/DetalleEstadoAperturaCierreCaja.php',
            title:'Detalle Apertura',
            height:'50%',
            cls:'DetalleEstadoAperturaCierreCaja'
        }
    ],
        bdel:false,
        bsave:false,
        bedit:false,
        bnew:false,
        replicarAux: function () {
            Ext.Ajax.request({
                url:'../../sis_contabilidad/control/Auxiliar/conectar',
                params:{id_usuario: 0},
                success:function(resp){
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                    //this.Cmp.id_oficina_registro_incidente.setValue(reg.ROOT.datos.id_oficina);
                    console.log('cambio exitoso');
                    Ext.Msg.alert('Aviso', 'Se esta replicando la informacion de Auxiliares, el procedimiento tarda de 30 a 60 segundos. Evite las replicaciones seguidas.');
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        }
        }
    )
</script>

