/*
 * @author HieuNLD 2014/06/27
 */
Ext.define('SrcPageUrl.Report.Inventory', {
    requires: [
        'Ext.data.*',
        'Ext.util.Format'
    ],
    constructor: function () {
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

        this.storeReportInventoryLoad = new Ext.data.JsonStore({
            model: 'Product',
            proxy: new Ext.data.HttpProxy({
                url: MyUtil.Path.getPathAction("Report_InventoryLoad"),
                reader: readerJson
            }),
            pageSize: pageSizeReport,
            autoLoad: ({params:{limit: limitReport, page: pageDefault, start: startDefault}}, false)
        });
    },
    create : function (){
        this.storeReportInventoryLoad.on('beforeload', function() {
            this.proxy.extraParams = {fromDate : Ext.ComponentQuery.query('[name=reportInventoryFromDate]', this.tBarInventory)[0].getSubmitValue(),
                                      toDate   : Ext.ComponentQuery.query('[name=reportInventoryToDate]', this.tBarInventory)[0].getSubmitValue(),
                                      productName   : Ext.ComponentQuery.query('[name=reportInventoryProductName]', this.tBarInventory)[0].getSubmitValue()};
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
                width: 180,
                dataIndex: 'remainQuantity',
                style: 'text-align:center;',
                align: 'right',
                renderer: Ext.util.Format.numberRenderer(moneyFormat)
            }, {
                text: "input quantity".Translator('Report'),
                width: 180,
                dataIndex: 'inputQuantity',
                style: 'text-align:center;',
                align: 'right',
                renderer: Ext.util.Format.numberRenderer(moneyFormat)
            }, {
                text: "output quantity".Translator('Report'),
                width: 180,
                dataIndex: 'outputQuantity',
                style: 'text-align:center;',
                align: 'right',
                renderer: Ext.util.Format.numberRenderer(moneyFormat)
            }
        ];

        return Ext.widget('gridpanel', {
            border: false,
            name: 'grid-inventory',
            store: this.storeReportInventoryLoad,
            loadMask: true,
            columns: columnsProduct,
            viewConfig: {
                emptyText: 'no records found'.Translator('Common')
            },
            listeners: {
                beforerender: function () {
                    this.store.load();
                }
            },
            bbar: new Ext.PagingToolbar({
                store: this.storeReportInventoryLoad,
                pageSize: limitDefault,
                emptyMsg : 'no records found'.Translator('Common'),
                beforePageText : 'page'.Translator('Common'),
                afterPageText : 'of'.Translator('Common') + ' {0}',
                refreshText : 'refresh'.Translator('Common'),
                displayMsg : 'displaying'.Translator('Common') + ' {0} - {1} ' + 'of'.Translator('Common') + ' {2}',
                displayInfo:true
            })
        });
    }
});