/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'data',
    id  : 'id',
    totalProperty: 'total'
};

var objectField = [{name: 'id',             type: 'int'},
                   {name: 'name',           type: 'string'},
                   {name: 'remainQuantity', type: 'int'},
                   {name: 'inputQuantity',  type: 'int'},
                   {name: 'outputQuantity', type: 'int'}];

MyUtil.Object.defineModel('Product', objectField);

var storeLoadProduct = new Ext.data.JsonStore({
    model: 'Product',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Report_InventoryLoad"),
        reader: readerJson
    }),
    pageSize: pageSizeReport,
    autoLoad: ({params:{limit: limitReport, page: pageDefault, start: startDefault}}, false)
});

Ext.define('SrcPageUrl.Report.Revenue', {
    requires: [
        'Ext.data.*',
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.tab.*'
    ],

    create : function (){
        var tBar = new Ext.Toolbar({
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
                        storeLoadProduct.reload();
                    }
                }
            }]
        });

        storeLoadProduct.on('beforeload', function() {
            this.proxy.extraParams = {fromDate : Ext.ComponentQuery.query('[name=reportRevenueFromDate]', tBar),
                                      toDate   : Ext.ComponentQuery.query('[name=reportRevenueToDate]', tBar)};
        });

        var columnsProduct = [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'id',
                hidden : true
            }, {
                text: "name".Translator('Common'),
                flex: 1,
                dataIndex: 'name',
                style: 'text-align:center;'
            }, {
                text: "remain quantity".Translator('Report'),
                width: 150,
                dataIndex: 'remainQuantity',
                style: 'text-align:center;',
                align: 'right',
                renderer: Ext.util.Format.numberRenderer(moneyFormat)
            }, {
                text: "input quantity".Translator('Report'),
                width: 150,
                dataIndex: 'inputQuantity',
                style: 'text-align:center;',
                align: 'right',
                renderer: Ext.util.Format.numberRenderer(moneyFormat)
            }, {
                text: "output quantity".Translator('Report'),
                width: 150,
                dataIndex: 'outputQuantity',
                style: 'text-align:center;',
                align: 'right',
                renderer: Ext.util.Format.numberRenderer(moneyFormat)
            }
        ];

        return Ext.widget('gridpanel', {
            border: false,
            name: 'grid-Revenue',
            store: storeLoadProduct,
            loadMask: true,
            columns: columnsProduct,
            tbar: tBar,
            viewConfig: {
                emptyText: 'no records found'.Translator('Common')
            },
            listeners: {
                beforerender: function () {
                    this.store.load();
                }
            }
        });
    }
});

