/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'data',
    id: 'id',
    totalProperty: 'total'
};

var objectField = [{name: 'id', type: 'int'},
    {name: 'numberPlate', type: 'string'},
    {name: 'name', type: 'string'}];

MyUtil.Object.defineModel('Driver', objectField);

var storeLoadDriver = new Ext.data.JsonStore({
    model: 'Driver',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Driver_Load"),
        reader: readerJson
    }),
    pageSize: pageSizeDefault,
    autoLoad: ({params: {limit: limitDefault, page: pageDefault, start: startDefault}}, false)
});

Ext.define('SrcPageUrl.Driver.List', {
    extend: 'Ext.ux.desktop.Module',

    requires: [
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.grid.Panel',
        'Ext.grid.RowNumberer'
    ],

    id: 'driver-list',

    init: function () {
        this.launcher = {
            text: 'driver management'.Translator('Module'),
            iconCls: 'icon-grid'
        };
    },

    createWindow: function () {
        var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToMoveEditor: 1,
            autoCancel: false,
            listeners: {
                edit: function (editor, e) {
                    var record = e.record.data;
                    Ext.Ajax.request({
                        url: MyUtil.Path.getPathAction("Driver_Update"),
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        waitTitle: 'processing'.Translator('Common'),
                        waitMsg: 'sending data'.Translator('Common'),
                        scope: this,
                        jsonData: {'params' : record},
                        success: function (msg) {
                            if (msg.status) {
                                storeLoadDriver.reload();
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
        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('grid-win');

        var columnsDriver = [
            new Ext.grid.RowNumberer(),
            {
                text: "name".Translator('Common'),
                width: 300,
                dataIndex: 'name',
                editor: {
                    xtype: 'textfield',
                    allowBlank: true
                }
            }, {
                text: "number plate".Translator('Driver'),
                flex: 1,
                dataIndex: 'numberPlate',
                editor: {
                    xtype: 'textfield',
                    allowBlank: true
                }
            }
        ];

        var rowModel = Ext.create('Ext.selection.RowModel', {
            mode: "MULTI",
            onKeyPress: function (e, t) {
                console.log(e);
            }
        });

        if (!win) {
            win = desktop.createWindow({
                id: 'driver-list',
                title: 'driver management'.Translator('Module'),
                width: 600,
                height: 480,
                iconCls: 'icon-grid',
                animCollapse: false,
                constrainHeader: true,
                layout: 'fit',
                items: [
                    {
                        xtype: 'grid',
                        border: false,
                        id: 'grid-driver-list',
                        store: storeLoadDriver,
                        selModel: rowModel,
                        loadMask: true,
                        columns: columnsDriver,
                        plugins: rowEditing,
                        listeners: {
                            beforerender: function () {
                                this.store.load();
                            }
                        }
                    }
                ],
                tbar: [{
                    text: 'add'.Translator('Common'),
                    tooltip: 'add'.Translator('Common'),
                    iconCls: 'add',
                    handler: function () {
                        rowEditing.cancelEdit();

                        // Create a model instance
                        var r = Ext.create('Driver', {
                            id: '',
                            name: '',
                            numberPlate: ''
                        });

                        storeLoadDriver.insert(0, r);
                        rowEditing.startEdit(0, 0);
                    }
                }, '-', {
                    text: 'remove'.Translator('Common'),
                    tooltip: 'remove'.Translator('Common'),
                    iconCls: 'remove',
                    listeners: {
                        click: function () {
                            var selection = Ext.getCmp('grid-driver-list').getView().getSelectionModel().getSelection();

                            if (selection.length > 0) {
                                Ext.MessageBox.confirm('delete'.Translator('Common'), 'are you sure'.Translator('Common'), function(btn){
                                    if (btn === 'yes') {
                                        var arrId = [];
                                        Ext.each(selection, function (v, k) {
                                            arrId[k] = v.data.id;
                                        });

                                        Ext.Ajax.request({
                                            url: MyUtil.Path.getPathAction("Driver_Delete"),
                                            method: 'POST',
                                            headers: {'Content-Type': 'application/json'},
                                            jsonData: {'params': arrId},
                                            waitTitle: 'processing'.Translator('Common'),
                                            waitMsg: 'sending data'.Translator('Common'),
                                            scope: this,
                                            success: function (msg) {
                                                if (msg.status) {
                                                    storeLoadDriver.reload();
                                                    console.log('success');
                                                }
                                            },
                                            failure: function (msg) {
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
                    store: storeLoadDriver,
                    displayInfo: true
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

