/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'data',
    id  : 'id',
    totalProperty: 'total'
};

var objectField = [{name: 'id',            type: 'int'},
    {name: 'name',          type: 'string'},
    {name: 'code',          type: 'string'},
    {name: 'productUnitId', type: 'string'},
    {name: 'originalPrice', type: 'string'},
    {name: 'salePrice',     type: 'string'},
    {name: 'unitId1',       type: 'string'},
    {name: 'unitId2',       type: 'string'},
    {name: 'convertAmount', type: 'string'}];

var objectUnitField = [{name: 'id',       type: 'int'},
    {name: 'name',     type: 'string'},
    {name: 'code',     type: 'string'}];

MyUtil.Object.defineModel('Product', objectField);
MyUtil.Object.defineModel('Unit', objectUnitField);

var storeLoadProduct = new Ext.data.JsonStore({
    model: 'Product',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Product_Load"),
        reader: readerJson
    }),
    pageSize: pageSizeDefault,
    autoLoad: ({params:{limit: limitDefault, page: pageDefault, start: startDefault}}, false)
});

var storeLoadUnit1 = new Ext.data.JsonStore({
    model: 'Unit',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Unit_Load"),
        reader: readerJson
    }), autoLoad: false
});

var storeLoadUnit2 = storeLoadUnit1;

Ext.define('SrcPageUrl.Report.Inventory', {
    requires: [
        'Ext.data.*',
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.tab.*'
    ],
    init : function(){

    },

    create : function (){
        var tBar =[{
            text: 'add'.Translator('Common'),
            tooltip: 'add'.Translator('Common'),
            iconCls:'add',
            handler : function() {

            }
        }, '-', {
            text:'change-pass'.Translator('User'),
            tooltip:'change-pass'.Translator('User'),
            iconCls:'edit',
            handler : function() {

            }
        }, '-',{
            text: 'remove'.Translator('Common'),
            tooltip: 'remove'.Translator('Common'),
            iconCls:'remove',
            listeners: {
                click: function () {
                }
            }
        }];

        var columnsProduct = [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'id',
                hidden : true
            }, {
                text: "name".Translator('Common'),
                width: 160,
                dataIndex: 'name',
                style: 'text-align:center;'
            }, {
                text: "product code".Translator('Product'),
                width: 140,
                dataIndex: 'code',
                style: 'text-align:center;'
            }, {
                text: "original price".Translator('Product'),
                width: 120,
                dataIndex: 'originalPrice',
                style: 'text-align:center;',
                align: 'right',
                renderer: Ext.util.Format.numberRenderer(moneyFormat)
            }, {
                text: "sale price".Translator('Product'),
                width: 120,
                dataIndex: 'salePrice',
                style: 'text-align:center;',
                align: 'right',
                renderer: Ext.util.Format.numberRenderer(moneyFormat)
            }, {
                header: 'unit 1'.Translator('Product'),
                dataIndex: 'unitId1',
                style: 'text-align:center;',
                width: 100,
                renderer: function(value){
                    if(value != 0 && value != "") {
                        if(storeLoadUnit1.findRecord("id", value) != null) return storeLoadUnit1.findRecord("id", value).get('name');
                        else return value;
                    } else return "";  // display nothing if value is empty
                }
            }, {
                header: 'unit 2'.Translator('Product'),
                dataIndex: 'unitId2',
                width: 100,
                style: 'text-align:center;',
                renderer: function(value){
                    if(value != 0 && value != "") {
                        if(storeLoadUnit2.findRecord("id", value) != null)
                            return storeLoadUnit2.findRecord("id", value).get('name');
                        else
                            return value;
                    } else return "";  // display nothing if value is empty
                }
            }, {
                text: "convert amount".Translator('Product'),
                flex: 1,
                dataIndex: 'convertAmount',
                style: 'text-align:center;',
                align: 'right'
            }
        ];

        return Ext.widget('grid', {
            border: false,
            id: 'grid-product-list',
            store: storeLoadProduct,
            loadMask: true,
            columns: columnsProduct,
            tbar: tBar,
            listeners: {
                beforerender: function () {
                    this.store.load();
                }
            }
        });
    }
});

