<script>
  Phx.vista.RepGeneralVentas=Ext.extend(Phx.baseInterfaz,{
        constructor:function(config) {
          this.maestro = config.maestro;
          Phx.vista.RepGeneralVentas.superclass.constructor.call(this, config);
          this.panel.destroy();
          window.open('http://xicbb-bi-dw:8082/BoAReports/browse/Control%20Ingresos', '_blank')
          }
    });
</script>
