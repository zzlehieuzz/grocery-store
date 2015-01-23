/*
 * @author HieuNLD 2014/06/27
 */

Ext.define('SrcPageUrl.Distributor.List', {
    extend: 'Ext.ux.desktop.Module',
    requires: ['Ext.grid.RowNumberer'],

    id:'distributor-list',

    init : function(){
        this.launcher = {
            text: 'distributor management'.Translator('Module'),
            iconCls:'icon-grid'
        };
    },

    createWindow : function() {
        var readerJson = {
            type: 'json',
            root: 'data',
            id  : 'id',
            totalProperty: 'total'
        };

        var objectField = [{name: 'id',          type: 'int'},
                           {name: 'name',        type: 'string'},
                           {name: 'code',        type: 'string'},
                           {name: 'phoneNumber', type: 'string'},
                           {name: 'address',     type: 'string'},
                           {name: 'labels',      type: 'string'}];

        MyUtil.Object.defineModel('Distributor', objectField);
        var storeLoadDistributor = new Ext.data.JsonStore({
            model: 'Distributor',
            proxy: new Ext.data.HttpProxy({
                url: MyUtil.Path.getPathAction("Distributor_Load"),
                reader: readerJson
            }),
            pageSize: limitDefault,
            autoLoad: ({params:{limit: limitDefault, page: pageDefault, start: startDefault}}, false)
        });

        var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToMoveEditor: 1,
            autoCancel: false,
            listeners: {
                'edit': function (editor,e) {
                    var record = e.record.data;

                    Ext.Ajax.request({
                        url: MyUtil.Path.getPathAction("Distributor_Update"),
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        waitTitle: 'processing'.Translator('Common'),
                        waitMsg: 'sending data'.Translator('Common'),
                        jsonData: {'params' : record},
                        scope: this,
                        success: function(msg) {
                            if (msg.status) {
                                storeLoadDistributor.reload();
                            }
                        },
                        failure: function(msg) {
                            console.log('failure');
                        }
                    });
                }
            }
        });

        var columnsDistributor = [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'id',
                hidden : true
            }, {
                text: "name".Translator("Common"),
                width: 150,
                dataIndex: 'name',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: "distributor code".Translator("Distributor"),
                width: 100,
                dataIndex: 'code',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: "phone".Translator("Common"),
                width: 100,
                dataIndex: 'phoneNumber',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: "item".Translator("Distributor"),
                width: 120,
                dataIndex: 'labels',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: "distributor address".Translator("Distributor"),
                flex: 1,
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
                id: 'distributor-list',
                title:'distributor management'.Translator('Module'),
                width:850,
                height:480,
                iconCls: 'icon-grid',
                animCollapse: false,
                constrainHeader: true,
                layout: 'fit',
                items:
                    [{
                        border: false,
                        xtype: 'grid',
                        id: 'grid-distributor-list',
                        store: storeLoadDistributor,
                        loadMask: true,
                        selModel: Ext.create('Ext.selection.RowModel', {mode : "MULTI"}),
                        plugins: rowEditing,
                        columns: columnsDistributor,
                        columnLines: true,
                        listeners:{
                            beforerender: function () {
                                this.store.load();
                            }
                        }
                    }
                ],
                tbar:[{
                    text:'add'.Translator('Common'),
                    tooltip:'add'.Translator('Common'),
                    iconCls:'add',
                    handler : function() {
                        rowEditing.cancelEdit();

                        Ext.Ajax.request({
                            url: MyUtil.Path.getPathAction("Distributor_LoadLastCode"),
                            method: 'GET',
                            headers: { 'Content-Type': 'application/json' },
                            waitTitle: 'processing'.Translator('Common'),
                            waitMsg: 'sending data'.Translator('Common'),
                            scope: this,
                            success: function(msg) {
                                if (msg.status) {
                                    var lastCode = Ext.JSON.decode(msg.responseText).data;
                                    var r = Ext.create('Distributor', {
                                        id: '',
                                        name: '',
                                        code: lastCode,
                                        phoneNumber: '',
                                        address: '',
                                        labels: ''
                                    });

                                    storeLoadDistributor.insert(0, r);
                                    rowEditing.startEdit(0, 0);
                                }
                            },
                            failure: function(msg) {
                                console.log('failure');
                            }
                        });
                    }
                }, {
                    text:'remove'.Translator('Common'),
                    tooltip:'remove'.Translator('Common'),
                    iconCls:'remove',
                    listeners: {
                        click: function () {
                            var selection = Ext.getCmp('grid-distributor-list').getView().getSelectionModel().getSelection();

                            if (selection.length > 0) {
                                Ext.MessageBox.confirm('delete'.Translator('Common'), 'are you sure'.Translator('Common'), function(btn){
                                    if(btn === 'yes') {
                                        var arrId = [];
                                        Ext.each(selection, function(v, k) {
                                            arrId[k] = v.data.id;
                                        });

                                        Ext.Ajax.request({
                                            url: MyUtil.Path.getPathAction("Distributor_Delete"),
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json' },
                                            jsonData: {'params' : arrId},
                                            waitTitle: 'processing'.Translator('Common'),
                                            waitMsg: 'sending data'.Translator('Common'),
                                            scope: this,
                                            success: function(msg) {
                                                if (msg.status) {
                                                    storeLoadDistributor.load();
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
                    store: storeLoadDistributor,
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
    },

    statics: {
        getDummyData: function () {
            return [];
        }
    }
});

