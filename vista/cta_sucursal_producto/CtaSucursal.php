<?php
/**
*@package pXP
*@file CtaSucursal.php
*@author  (rac)
*@date 20-09-2011 10:22:05
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.CtaSucursal = {
    require:'../../../sis_ventas_facturacion/vista/sucursal/Sucursal.php',
    requireclase:'Phx.vista.Sucursal',
    title:'Sucursales',
    constructor: function(config) {
        Phx.vista.CtaSucursal.superclass.constructor.call(this,config);     
    },    
   
   bedit:false,
   bnew:false,
   bdel:false,
   bsave:false,
   
   south : {
            url : '../../../sis_ventas_facturacion/vista/cta_sucursal_producto/CtaSucursalProducto.php',
            title : 'Productos',
            height : '50%',
            cls : 'CtaSucursalProducto'
        },
    tabeast:[
    { 
          url:'../../../sis_contabilidad/vista/relacion_contable/RelacionContableTabla.php',
          title:'Relacion Contable', 
          width:'50%',
          cls:'RelacionContableTabla',
          params:{nombre_tabla:'vef.tsucursal',tabla_id:'id_sucursal'}
     }],
   
   
   EnableSelect : function (n, extra) {
        var miExtra = {codigos_tipo_relacion:''};
        if (extra != null && typeof extra === 'object') {
            miExtra = Ext.apply(extra, miExtra) 
        }
      Phx.vista.CtaSucursal.superclass.EnableSelect.call(this,n,miExtra);  
   }
};
</script>