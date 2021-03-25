<script>
  Phx.vista.RepIVP=Ext.extend(Phx.baseInterfaz,{
        constructor:function(config) {
          this.maestro = config.maestro;
          Phx.vista.RepIVP.superclass.constructor.call(this, config);
          this.panel.destroy();
          window.open('http://10.150.0.22:8082/Reports/Pages/Folder.aspx?ItemPath=%2fBoaDwRepIngresos', '_blank')
          }
    });
</script>
