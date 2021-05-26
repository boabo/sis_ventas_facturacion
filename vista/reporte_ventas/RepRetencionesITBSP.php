<script>
  Phx.vista.RepRetencionesITBSP=Ext.extend(Phx.baseInterfaz,{
        constructor:function(config) {
          this.maestro = config.maestro;
          Phx.vista.RepRetencionesITBSP.superclass.constructor.call(this, config);
          this.panel.destroy();
          window.open('http://172.17.110.5:8082/boareports/report/Control%20Ingresos/Reporte%20Retenciones%20IT-BSP', '_blank')
          }
    });
</script>
