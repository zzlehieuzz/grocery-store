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
            activeTab: 1,
            width: 900,
            height: 550,
            plain: true,
            animCollapse: false,
            defaults :{
                layout: 'fit',
                autoScroll: true,
                autoWidth: true,
                autoHeight: true
            },
            layoutConfig: {
                deferredRender: true
            },
            items: [{
                title: 'revenue'.Translator('Report'),
                layout: 'fit',
                items: new SrcPageUrl.Report.Revenue().create()
            }, {
                title: 'inventory'.Translator('Report'),
                layout: 'fit',
                items: new SrcPageUrl.Report.Inventory().create()
            }]
        });

        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('report');
        if(!win){
            win = desktop.createWindow({
                name: 'report-tab',
                title: 'report'.Translator('Module'),
                width: 910,
                height: 530,
                iconCls: 'icon-grid',
                animCollapse: false,
                constrainHeader: true,
                layout: 'border',
                border: false,
                items: tabPanel
            });
        }

        return win;
    }
});

