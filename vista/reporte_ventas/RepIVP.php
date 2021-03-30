<script>
  Phx.vista.RepIVP=Ext.extend(Phx.baseInterfaz,{
        constructor:function(config) {
          this.maestro = config.maestro;
          Phx.vista.RepIVP.superclass.constructor.call(this, config);
          this.panel.destroy();
          window.open('http://172.17.110.5:8082/BoAReports/browse/BoaDwRepIngresos', '_blank')
          }
    });
</script>
