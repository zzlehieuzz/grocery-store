/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'data',
    id  : 'id',
    totalProperty: 'total'
};

MyUtil.Object.defineModel('Product', [{name: 'id',           type: 'int'},
                                     {name: 'name',          type: 'string'},
                                     {name: 'code',          type: 'string'},
                                     {name: 'productUnitId', type: 'string'},
                                     {name: 'originalPrice', type: 'string'},
                                     {name: 'salePrice',     type: 'string'},
                                     {name: 'unitId1',       type: 'string'},
                                     {name: 'unitId2',       type: 'string'},
                                     {name: 'convertAmount', type: 'string'}]);

MyUtil.Object.defineModel('Unit', [{name: 'id',       type: 'int'},
                                   {name: 'name',     type: 'string'},
                                   {name: 'code',     type: 'string'}]);

var storeLoadProduct = new Ext.data.JsonStore({
    model: 'Product',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Product_Load"),
        reader: readerJson
    }), pageSize: pageSizeDefault,
    autoLoad: ({params:{limit: limitDefault, page: pageDefault, start: startDefault}}, false)
}),

storeLoadUnit1 = new Ext.data.JsonStore({
    model: 'Unit',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Unit_Load"),
        reader: readerJson
    }), autoLoad: false
}),

storeLoadUnit2 = storeLoadUnit1;

Ext.define('SrcPageUrl.Product.List', {
    extend: 'Ext.ux.desktop.Module',
    requires: [
        'Ext.util.Format',
        'Ext.grid.RowNumberer'
    ],

    id:'product-list',

    init : function(){
        this.launcher = {
            text: 'product management'.Translator('Module'),
            iconCls:'icon-grid'
        };
    },

    createWindow : function(){
        var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToMoveEditor: 1,
            autoCancel: false,
            listeners: {
                'edit': function (editor, e) {
                    var record = e.record.data;
                    Ext.Ajax.request({
                        url: MyUtil.Path.getPathAction("Product_Update"),
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        waitTitle: 'processing'.Translator('Common'),
                        waitMsg: 'sending data'.Translator('Common'),
                        scope: this,
                        jsonData: {'params' : record},
                        success: function(msg) {
                            if (msg.status) {
                                storeLoadProduct.reload();
                            }
                        },
                        failure: function(msg) {
                            console.log('failure');
                        }
                    });
                }
            }
        });

        var rowRendererLoadUnit1 = function(val) {
            var rec = storeLoadUnit1.findRecord('id', val);
            return rec !== null ? rec.get("name") : ''
        };

        var rowRendererLoadUnit2 = function(val) {
            var rec = storeLoadUnit2.findRecord('id', val);
            return rec !== null ? rec.get("name") : ''
        };

        var columnsProduct = [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'id',
                hidden : true
            }, {
                text: "name".Translator('Common'),
                width: 200,
                dataIndex: 'name',
                style: 'text-align:center;',
                editor: { xtype: 'textfield' }
            }, {
                text: "product code".Translator('Product'),
                width: 100,
                dataIndex: 'code',
                style: 'text-align:center;',
                editor: { xtype: 'textfield' }
            }, {
                text: "original price".Translator('Product'),
                width: 120,
                renderer: Ext.util.Format.numberRenderer(moneyFormat),
                dataIndex: 'originalPrice',
                style: 'text-align:center;',
                align: 'right',
                editor: {
                    xtype: 'numberfield',
                    decimalPrecision: decimalPrecision
                }
            }, {
                text: "sale price".Translator('Product'),
                width: 120,
                dataIndex: 'salePrice',
                style: 'text-align:center;',
                align: 'right',
                renderer:  Ext.util.Format.numberRenderer(moneyFormat),
                editor: {
                    xtype: 'numberfield',
                    decimalPrecision: decimalPrecision
                }
            }, {
                header: 'unit 1'.Translator('Product'),
                dataIndex: 'unitId1',
                style: 'text-align:center;',
                width: 100,
                editor: new Ext.form.ComboBox({
                    store: storeLoadUnit1,
                    queryMode: 'local',
                    displayField: 'name',
                    valueField: 'id',
                    minListWidth: 300,
                    listWidth: 150,
                    triggerAction: 'all',
                    typeAhead: true,
                    lazyRender: true,
                    selectOnFocus: true,
                    forceSelection: true,
                    listConfig: {
                        width: 240 ,resizable: true , resizeHandles: "se"
                    }
                }),
                renderer: rowRendererLoadUnit1
            }, {
                header: 'unit 2'.Translator('Product'),
                dataIndex: 'unitId2',
                width: 100,
                style: 'text-align:center;',
                editor: new Ext.form.ComboBox({
                    store: storeLoadUnit2,
                    queryMode: 'local',
                    displayField: 'name',
                    valueField: 'id',
                    triggerAction: 'all',
                    typeAhead: true,
                    lazyRender: true,
                    selectOnFocus: true,
                    forceSelection: true,
                    listConfig: {
                       width: 250 ,resizable: true , resizeHandles: "se"
                    }
                }),
                renderer: rowRendererLoadUnit2
            }, {
                text: "convert amount".Translator('Product'),
                flex: 1,
                dataIndex: 'convertAmount',
                style: 'text-align:center;',
                align: 'right',
                renderer: 0,
                editor: {
                    xtype: 'numberfield',
                    decimalPrecision: decimalPrecision
                }
            }
        ];

        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('grid-win');
        if(!win){
            win = desktop.createWindow({
                id: 'product-list',
                title: 'product management'.Translator('Module'),
                width: 900,
                height: 540,
                iconCls: 'icon-grid',
                animCollapse: false,
                constrainHeader: true,
                layout: 'fit',
                items:
                    [{
                        xtype: 'grid',
                        border: false,
                        id: 'grid-product-list',
                        store: storeLoadProduct,
                        loadMask: true,
                        columnLines: true,
                        selModel: Ext.create('Ext.selection.RowModel', {mode : "MULTI"}),
                        plugins: rowEditing,
                        columns: columnsProduct,
                        listeners:{
                            beforerender: function () {
                                this.store.load();
                                storeLoadUnit1.load();
                                storeLoadUnit2.load();
                            }
                        }
                    }
                ],
                tbar:[{
                    text: 'add'.Translator('Common'),
                    tooltip: 'add'.Translator('Common'),
                    iconCls: 'add',
                    handler : function() {
                        rowEditing.cancelEdit();

                        // Create a model instance
                        var r = Ext.create('Product', {
                        id: '',
                        name: '',
                        code: '',
                        unit: ''
                        });

                        storeLoadProduct.insert(0, r);
                        rowEditing.startEdit(0, 0);
                    }
                }, '-',{
                    text: 'remove'.Translator('Common'),
                    tooltip: 'remove'.Translator('Common'),
                    iconCls: 'remove',
                    listeners: {
                        click: function () {
                            var selection = Ext.getCmp('grid-product-list').getView().getSelectionModel().getSelection();

                            if (selection.length > 0) {
                                Ext.MessageBox.confirm('delete'.Translator('Common'), 'are you sure'.Translator('Common'), function(btn){
                                    if(btn === 'yes') {
                                        var arrId = [];
                                        Ext.each(selection, function(v, k) {
                                            arrId[k] = v.data.id;
                                        });

                                        Ext.Ajax.request({
                                            url: MyUtil.Path.getPathAction("Product_Delete"),
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json' },
                                            jsonData: {'params' : arrId},
                                            waitTitle: 'processing'.Translator('Common'),
                                            waitMsg: 'sending data'.Translator('Common'),
                                            scope: this,
                                            success: function(msg) {
                                                if (msg.status) {
                                                    //storeLoadProduct.remove(selection);
                                                    storeLoadProduct.reload();
                                                    console.log('success');
                                                }
                                            },
                                            failure: function(msg) {
                                                console.log('failure');
                                            }
                                        });
                                    }
                                });
                            } else {
                                MyUtil.Message.MessageError();
                            }
                        }
                    }
                }],
                bbar: new Ext.PagingToolbar({
                    store: storeLoadProduct,
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

        return win;
    }
});

