<script>

    function resizeIframe(obj) {
        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
    }

    Ext.define('Phx.vista.RepIVP',{
        extend: 'Ext.util.Observable',

        constructor: function(config) {
            var me = this;
            Ext.apply(this, config);
            var me = this;
            this.callParent(arguments);


            this.panel = Ext.getCmp(this.idContenedor);
            var newIndex = 3;



            this.reportPanel = new Ext.Panel({
                id: 'reportPanel-boa',
                width: '100%',
                height: '100%',
                region:'center',
                margins: '5 0 5 5',
                layout: 'fit',
                autoScroll : true,
                items: [{
                    xtype: 'box',
                    width: '100%',
                    height: '100%',
                    autoEl: {
                        tag: 'iframe',
                        src: 'http://10.150.0.22:8082/Reports/Pages/Report.aspx?ItemPath=%2fBoaDwRepIngresos%2fRepImpuestosVentasPropias',
                    }}]
            });
            // console.log("datab",Ext.getClassByName('msrs-topBreadcrumb'));

            this.Border = new Ext.Container({
                layout:'border',
                id:'principal',
                items:[this.reportPanel]
            });

            this.panel.add(this.Border);
            this.panel.doLayout();
            this.addEvents('init');
            var iframe = document.getElementById('reportPanel-boa').firstChild.firstChild.firstChild;
            iframe.style.marginTop = '-30px';
            // Ext.get(iframe).on('load', function(r,d) {
            //   // console.log("data",d.contentWindow.document);
            //   });
        },

    });
</script>
