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
    Phx.vista.DetalleEstadoAperturaCierreCaja=Ext.extend(Phx.gridInterfaz,{
        punto:'',
            constructor:function(config){
                this.maestro=config.maestro;
                Phx.vista.DetalleEstadoAperturaCierreCaja.superclass.constructor.call(this,config);
                this.init();
                this.finCons = true;

                var estado;
                var dataPadre = Phx.CP.getPagina(this.idContenedorPadre).getSelectedData();
                if(dataPadre){
                    this.onEnablePanel(this, dataPadre);
                } else {
                    this.bloquearMenus();
                }
                this.store.baseParams.pes_estado = 'abierto';


            },
        bactGroups:  [0,1],
        bexcelGroups: [0,1],

        gruposBarraTareas:[{name:'abierto',title:'<H1 align="center"><i class="fa fa-eye"></i> Abiertas</h1>',grupo:0,height:0},
                           {name:'cerrado',title:'<H1 align="center"><i class="fa fa-eye"></i> Cerradas</h1>',grupo:1,height:0}

        ],
        actualizarSegunTab: function(name, indice){
            if(this.finCons) {
                //this.store.baseParams.pes_estado = name;
                this.store.baseParams = {fecha_apertura_cierre: "''"+this.maestro.fecha_apertura_cierre.dateFormat('d/m/Y')+"''",pes_estado:name};
                this.punto = name;
                this.load({params:{start:0, limit:this.tam_pag}});
            }
        },

            Atributos:[
                {
                    config:{
                        name: 'fecha_apertura_cierre',
                        fieldLabel: 'Fecha ',
                        gwidth: 110,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters: { pfiltro:'apcie.fecha_apertura_cierre', type:'date'},
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'estado',
                        fieldLabel: 'Estado',
                        gwidth: 100
                    },
                    type:'TextField',
                    filters:{pfiltro:'ven.estado',type:'string'},
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'nombre',
                        fieldLabel: 'Punto Venta',
                        gwidth: 250,
                        renderer: function(value, p, record) {
                                return String.format('<div ext:qtip="Optimo"><b><font color="black"><b>{0}</b></font></b><br></div>', value);
                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'p.nombre',type:'string'},
                    grid:true,
                    form:false,
                    bottom_filter:true
                },
                {
                    config:{
                        name: 'codigo',
                        fieldLabel: 'Codigo',
                        gwidth: 100
                    },
                    type:'TextField',
                    filters:{pfiltro:'p.codigo',type:'string'},
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'desc_persona',
                        fieldLabel: 'Cajero',
                        gwidth: 250,
                        renderer: function(value, p, record) {
                            return String.format('<div ext:qtip="Optimo"><b><font color="black"><b>{0}</b></font></b><br></div>', value);
                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'u.desc_persona',type:'string'},
                    grid:true,
                    form:false,
                    bottom_filter:true
                }

            ],
            tam_pag:50,
            title:'Apertura de Caja Estado',
            ActList:'../../sis_ventas_facturacion/control/AperturaCierreCaja/DetalleEstadoApertura',
            id_store:'id_apertura_cierre_caja',
            fields: [
                {name:'id_apertura_cierre_caja', type: 'numeric'},
                {name:'fecha_apertura_cierre', type: 'date',dateFormat:'Y-m-d'},
                {name:'estado', type: 'string'},
                {name:'nombre', type: 'string'},
                {name:'codigo', type: 'string'},
                {name:'desc_persona', type: 'string'}

            ],
            sortInfo:{
                field: 'fecha_apertura_cierre',
                direction: 'desc'
            },
            bdel:false,
            bsave:false,
            bedit:false,
            bnew:false,

        onReloadPage: function (m) {
            this.maestro = m;
            var  reg ='';
            if(reg == this.punto){
                reg = 'abierto'
            }else{
                reg =this.punto
            }
            this.store.baseParams = {fecha_apertura_cierre: "''"+this.maestro.fecha_apertura_cierre.dateFormat('d/m/Y')+"''"};
            this.store.baseParams.pes_estado = reg;
            this.load({params: {start: 0, limit: 50}});
        },
        loadValoresIniciales: function () {
            this.Cmp.fecha_apertura_cierre.setValue(this.maestro.fecha_apertura_cierre);
            Phx.vista.DetalleEstadoAperturaCierreCaja.superclass.loadValoresIniciales.call(this);
        }
        }
    )
</script>

