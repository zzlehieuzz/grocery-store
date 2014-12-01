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
    constructor: function () {
        this.tBarInventory = '', this.tBarRevenue = '';
        this.tBarInventory = new Ext.Toolbar({
            items: [{
                name: 'reportInventoryFromDate',
                labelWidth: 50,
                fieldLabel: 'from date'.Translator('Invoice'),
                xtype: 'datefield',
                width: 165,
                padding: '0 0 0 10px;',
                format: dateFormat,
                submitFormat: dateSubmitFormat,
                value: Ext.Date.format(new Date(), firstDateFormat)
            }, {
                name: 'reportInventoryToDate',
                fieldLabel: '~',
                labelWidth: 5,
                labelSeparator: '',
                xtype: 'datefield',
                width: 120,
                format: dateFormat,
                submitFormat: dateSubmitFormat,
                value: Ext.Date.format(new Date(), lastDateFormat)
            }, '-',{
                name: 'reportInventoryProductName',
                width: 200,
                labelWidth: 50,
                emptyText: 'product name'.Translator('Report'),
                xtype: 'textfield'
            }, '->',{
                text: 'find'.Translator('Common'),
                tooltip: 'find'.Translator('Common'),
                iconCls:'find',
                listeners: {
                    click: function () {
                        //storeReportInventoryLoad.reload();
                    }
                }
            }]
        });

        this.tBarRevenue = new Ext.Toolbar({
            items: [{
                name: 'reportRevenueFromDate',
                labelWidth: 50,
                fieldLabel: 'from date'.Translator('Invoice'),
                xtype: 'datefield',
                width: 165,
                padding: '0 0 0 10px;',
                format: dateFormat,
                submitFormat: dateSubmitFormat,
                value: Ext.Date.format(new Date(), firstDateFormat)
            }, {
                name: 'reportRevenueToDate',
                fieldLabel: '~',
                labelWidth: 5,
                labelSeparator: '',
                xtype: 'datefield',
                width: 120,
                format: dateFormat,
                submitFormat: dateSubmitFormat,
                value: Ext.Date.format(new Date(), lastDateFormat)
            }, '->',{
                text: 'find'.Translator('Common'),
                tooltip: 'find'.Translator('Common'),
                iconCls:'find',
                listeners: {
                    click: function () {
                        //storeReportInventoryLoad.reload();
                    }
                }
            }]
        });
    },
    createWindow : function (){
        var tabPanel = Ext.widget('tabpanel', {
            activeTab: 0,
            width: 950,
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
                tbar: this.tBarRevenue,
                items: new SrcPageUrl.Report.Revenue().create()
            }, {
                title: 'inventory'.Translator('Report'),
                layout: 'fit',
                tbar: this.tBarInventory,
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
                layout: 'border',
                border: false,
                items: tabPanel
            });
        }

        return win;
    }
});

