/*
 * @author HieuNLD 2014/06/27
 */

Ext.define('SrcPageUrl.Report.Main', {
    extend: 'Ext.ux.desktop.Module',
    requires: [
        'Ext.data.*',
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.tab.*',
        'SrcPageUrl.Report.Inventory',
        'SrcPageUrl.Report.Revenue'
    ],

    id:'report',

    init : function(){
        this.launcher = {
            text: 'report'.Translator('Module'),
            iconCls:'icon-grid'
        };
    },
    createWindow : function (){
        var tabPanel = Ext.widget('tabpanel', {
            activeTab: 0,
            plain: true,
            animCollapse: false,
            layoutConfig: {
                deferredRender: true
            },
            items: [{
                title: 'revenue'.Translator('Report'),
                layout: 'fit',
                tbar: new SrcPageUrl.Report.Revenue().createTbar(),
                items: new SrcPageUrl.Report.Revenue().create()
            }, {
                title: 'inventory'.Translator('Report'),
                layout: 'fit',
                tbar: new SrcPageUrl.Report.Inventory().createTbar(),
                items: new SrcPageUrl.Report.Inventory().create()
            }]
        });

        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('report');
        if(!win){
            win = desktop.createWindow({
                name: 'report-tab',
                title: 'report'.Translator('Module'),
                width: 950,
                height: 600,
                iconCls: 'icon-grid',
                animCollapse: false,
                constrainHeader: true,
                layout: 'fit',
                border: false,
                items: [tabPanel]
            });
        }

        return win;
    }
});

