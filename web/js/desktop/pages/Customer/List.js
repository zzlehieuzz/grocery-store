/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'data',
    id  : 'id',
    totalProperty: 'total'
};

var objectField = [{name: 'id',           type: 'int'},
                   {name: 'name',         type: 'string'},
                   {name: 'code',         type: 'string'},
                   {name: 'phoneNumber',  type: 'string'},
                   {name: 'address',      type: 'string'}];

MyUtil.Object.defineModel('Customer', objectField);

var storeLoadCustomer = new Ext.data.JsonStore({
    model: 'Customer',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Customer_Load"),
        reader: readerJson
    }),
    pageSize: 5,
    autoLoad: ({params:{limit: 5, page: 1, start: 1}}, false)
});

Ext.define('SrcPageUrl.Customer.List', {
    extend: 'Ext.ux.desktop.Module',
    requires: [
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.grid.Panel',
        'Ext.grid.RowNumberer'
    ],

    id:'customer-list',

    init : function(){
        this.launcher = {
            text: 'Customer List',
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
                        url: MyUtil.Path.getPathAction("Customer_Update"),
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

        var columnsCustomer = [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'id',
                hidden : true
            }, {
                text: "Customer Code",
                width: 150,
                flex: 1,
                dataIndex: 'code',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: "Customer Name",
                flex: 2,
                dataIndex: 'name',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: "Phone Number",
                flex: 3,
                dataIndex: 'phoneNumber',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: "Address",
                flex: 4,
                dataIndex: 'address',
                editor: {
                    xtype: 'textfield'
                }
            }
        ];

        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('grid-win');
        if(!win){
            win = desktop.createWindow({
                id: 'customer-list',
                title:'Customer List',
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
                        id: 'grid-customer-list',
                        store: storeLoadCustomer,
                        loadMask:true,
                        selModel: rowModel,
                        plugins: rowEditing,
                        columns: columnsCustomer,
                        listeners:{
                            beforerender: function () {
                                this.store.load();
                            }
                        }
                    }
                ],
                tbar:[{
                    text:'Add',
                    tooltip:'Add a new row',
                    iconCls:'add',
                    handler : function() {
                      rowEditing.cancelEdit();

                      // Create a model instance
                      var r = Ext.create('Customer', {
                        id: '',
                        name: '',
                        code: '',
                        phoneNumber: '',
                        address: ''
                      });

                      storeLoadCustomer.insert(0, r);
                      rowEditing.startEdit(0, 0);
                    }
                }, '-',{
                    text:'Remove',
                    tooltip:'Remove the selected item',
                    iconCls:'remove',
                    listeners: {
                        click: function () {
                            var selection = Ext.getCmp('grid-customer-list').getView().getSelectionModel().getSelection();

                            if (selection.length > 0) {
                                Ext.MessageBox.confirm('Delete', 'Are you sure ?', function(btn){
                                    if(btn === 'yes') {
                                        var arrId = [];
                                        Ext.each(selection, function(v, k) {
                                            arrId[k] = v.data.id;
                                        });

                                        Ext.Ajax.request({
                                            url: MyUtil.Path.getPathAction("Customer_Delete"),
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json' },
                                            jsonData: {'params' : arrId},
                                            scope: this,
                                            success: function(msg) {
                                                if (msg.status) {
                                                    //storeLoadCustomer.remove(selection);
                                                    storeLoadCustomer.reload();
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
                    store: storeLoadCustomer,
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
