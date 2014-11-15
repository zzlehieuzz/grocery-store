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
                   {name: 'address',      type: 'string'},
                   {name: 'labels',       type: 'string'}];

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

Ext.define('SrcPageUrl.Distributor.List', {
    extend: 'Ext.ux.desktop.Module',
    requires: [
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.grid.Panel',
        'Ext.grid.RowNumberer'
    ],

    id:'distributor-list',

    init : function(){
        this.launcher = {
            text: 'distributor management'.Translator('Module'),
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

        var columnsDistributor = [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'id',
                hidden : true
            }, {
//                text: "name".Translator("Common"),
                text: "name",
                width: 200,
                dataIndex: 'name',
                editor: {
                    xtype: 'textfield'
                }
            }, {
//                text: "distributor code".Translator("Distributor"),
                text: "distributor code",
                width: 100,
                dataIndex: 'code',
                editor: {
                    xtype: 'textfield'
                }
            }, {
//                text: "phone".Translator("Distributor"),
                text: "phone",
                width: 200,
                dataIndex: 'phoneNumber',
                editor: {
                    xtype: 'textfield'
                }
            }, {
//                text: "address".Translator("Distributor"),
                text: "address",
                flex: 1,
                dataIndex: 'address',
                editor: {
                    xtype: 'textfield'
                }
            }, {
//                text: "labels".Translator("Distributor"),
                text: "Mặt Hàng",
                flex: 1,
                dataIndex: 'labels',
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
//                title:'distributor management'.Translator('Module'),
                title:'distributor management',
                width:800,
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
                        loadMask:true,
                        selModel: rowModel,
                        plugins: rowEditing,
                        columns: columnsDistributor,
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

                      // Create a model instance
                      var r = Ext.create('Distributor', {
                        id: '',
                        name: '',
                        code: '',
                        phoneNumber: '',
                        address: '',
                        labels: ''
                      });

                      storeLoadDistributor.insert(0, r);
                      rowEditing.startEdit(0, 0);
                    }
                }, '-',{
                    text:'remove'.Translator('Common'),
                    tooltip:'remove'.Translator('Common'),
                    iconCls:'remove',
                    listeners: {
                        click: function () {
                            var selection = Ext.getCmp('grid-distributor-list').getView().getSelectionModel().getSelection();

                            if (selection.length > 0) {
                                Ext.MessageBox.confirm('delete'.Translator('Common'), 'Are you sure'.Translator('Common'), function(btn){
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
                                                    //storeLoadDistributor.remove(selection);
                                                    storeLoadDistributor.reload();
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
                    store: storeLoadDistributor,
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

