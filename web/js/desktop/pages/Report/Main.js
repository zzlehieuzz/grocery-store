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
        'SrcPageUrl.Report.Inventory'
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
            width: 900,
            height: 600,
            plain:true,
            defaults :{
                autoScroll: true
            },
            items: [{
                title: 'invoice input'.Translator('Report'),
                html: "My content was added during construction."
            },{
                title: 'invoice output'.Translator('Report'),
                html: "My content was added during construction."
            },{
                title: 'revenue'.Translator('Report'),
                html: "I am tab 4's content. I also have an event listener attached."
            }, {
                title: 'inventory'.Translator('Report'),
                items:  new SrcPageUrl.Report.Inventory().create()

            }]
        });

        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('report');
        if(!win){
            win = desktop.createWindow({
                id: 'report-tab',
                title: 'report'.Translator('Module'),
                width: 910,
                height: 630,
                iconCls: 'icon-grid',
                animCollapse: false,
                constrainHeader: true,
                layout: 'border',
                border: false,
                items: tabPanel
            });
        }

        return win;
    },

    statics: {
        getDummyData: function () {
            return [];
        }
    }
});

