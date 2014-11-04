/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'data',
    id  : 'id',
    totalProperty: 'total'
};

var objectField = [{name: 'id',                 type: 'int'},
                   {name: 'name',               type: 'string'},
                   {name: 'code',               type: 'string'},
                   {name: 'productUnitId',      type: 'string'},
                   {name: 'unitId1',            type: 'string'},
                   {name: 'unitId2',            type: 'string'},
                   {name: 'convertAmount',      type: 'string'}];

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
    pageSize: 20,
    autoLoad: ({params:{limit: 20, page: 1, start: 1}}, false)
});

var storeLoadUnit1 = new Ext.data.JsonStore({
    model: 'Unit',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Unit_Load"),
        reader: readerJson
    }), autoLoad: true
});

var storeLoadUnit2 = storeLoadUnit1;

Ext.define('SrcPageUrl.Product.List', {
    extend: 'Ext.ux.desktop.Module',
    requires: [
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.grid.Panel',
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
                'edit': function (editor,e) {
                    var record = e.record.data;

                    Ext.Ajax.request({
                        url: MyUtil.Path.getPathAction("Product_Update"),
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        waitTitle: 'Connecting',
                        waitMsg: 'Sending data...',
                        jsonData: {'params' : record},
                        scope: this,
                        success: function(msg) {
                            if (msg.status) {
                                console.log('success');
                            }
                        },
                        failure: function(msg) {
                            console.log('failure');
                        }
                    });
                }
            }
        });

        var rowModel = Ext.create('Ext.selection.RowModel', {
            mode : "MULTI",
            onKeyPress: function(e, t) {
                console.log(e);
            }
        });

        var columnsProduct = [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'id',
                hidden : true
            }, {
                text: "name".Translator('Common'),
                width: 150,
                dataIndex: 'name',
                editor: { xtype: 'textfield' }
            }, {
                text: "product code".Translator('Product'),
                width: 120,
                dataIndex: 'code',
                editor: { xtype: 'textfield' }
            }, {
                header: 'unit 1'.Translator('Product'),
                dataIndex: 'unitId1',
                width: 110,
                editor: {
                    xtype: 'combobox',
                    store: storeLoadUnit1,
                    displayField: 'name',
                    valueField: 'id'
                },
                renderer: function(value){
                    if(value != 0 && value != "")
                    {
                        if(storeLoadUnit1.findRecord("id", value) != null)
                            return storeLoadUnit1.findRecord("id", value).get('name');
                        else
                            return value;
                    } else return "";  // display nothing if value is empty
                }
            }, {
                header: 'unit 2'.Translator('Product'),
                dataIndex: 'unitId2',
                width: 110,
                editor: {
                    xtype: 'combobox',
                    store: storeLoadUnit2,
                    displayField: 'name',
                    valueField: 'id'
                }, renderer: function(value){
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
                editor: {xtype: 'textfield'}
            }
        ];

        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('grid-win');
        if(!win){
            win = desktop.createWindow({
                id: 'product-list',
                title: 'product management'.Translator('Module'),
                width:740,
                height:480,
                iconCls: 'icon-grid',
                animCollapse: false,
                constrainHeader: true,
                layout: 'fit',
                items:
                    [{
                        border: false,
                        xtype: 'grid',
                        id: 'grid-product-list',
                        store: storeLoadProduct,
                        loadMask:true,
                        selModel: rowModel,
                        plugins: rowEditing,
                        columns: columnsProduct,
                        listeners:{
                            beforerender: function () {
                                this.store.load();
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
                                Ext.MessageBox.confirm('Delete', 'Are you sure ?', function(btn){
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
                    displayInfo:true
                })
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

