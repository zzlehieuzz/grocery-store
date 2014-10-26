/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'data',
    id  : 'id',
    totalProperty: 'total'
};

var objectField = [{name: 'id',   type: 'int'},
                   {name: 'name', type: 'string'},
                   {name: 'code', type: 'string'}];

MyUtil.Object.defineModel('Unit', objectField);

var storeLoadUnit = new Ext.data.JsonStore({
    model: 'Unit',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Unit_Load"),
        reader: readerJson
    }),
    pageSize: 5,
    autoLoad: ({params:{limit: 5, page: 1, start: 1}}, false)
});

Ext.define('SrcPageUrl.Unit.List', {
    extend: 'Ext.ux.desktop.Module',
    requires: [
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.grid.Panel',
        'Ext.grid.RowNumberer'
    ],

    id:'unit-list',

    init : function(){
        this.launcher = {
            text: 'Unit List',
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
                        url: MyUtil.Path.getPathAction("Unit_Update"),
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

        var columnsUnit = [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'id',
                hidden : true
            }, {
                text: "Unit Name",
                width: 150,
                flex: 1,
                dataIndex: 'name',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: "Unit Code",
                flex: 2,
                dataIndex: 'code',
                editor: {
                    xtype: 'textfield'
                }
            }
        ]

        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('grid-win');
        if(!win){
            win = desktop.createWindow({
                id: 'unit-list',
                title:'Unit List',
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
                        id: 'grid-unit-list',
                        store: storeLoadUnit,
                        loadMask:true,
                        selModel: rowModel,
                        plugins: rowEditing,
                        columns: columnsUnit,
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
                      var r = Ext.create('Unit', {
                        id: '',
                        name: '',
                        code: '',
                        unit: ''
                      });

                      storeLoadUnit.insert(0, r);
                      rowEditing.startEdit(0, 0);
                    }
                }, '-',{
                    text:'Remove',
                    tooltip:'Remove the selected item',
                    iconCls:'remove',
                    listeners: {
                        click: function () {
                            var selection = Ext.getCmp('grid-unit-list').getView().getSelectionModel().getSelection();

                            if (selection.length > 0) {
                                Ext.MessageBox.confirm('Delete', 'Are you sure ?', function(btn){
                                    if(btn === 'yes') {
                                        var arrId = [];
                                        Ext.each(selection, function(v, k) {
                                            arrId[k] = v.data.id;
                                        });

                                        Ext.Ajax.request({
                                            url: MyUtil.Path.getPathAction("Unit_Delete"),
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json' },
                                            jsonData: {'params' : arrId},
                                            scope: this,
                                            success: function(msg) {
                                                if (msg.status) {
                                                    //storeLoadUnit.remove(selection);
                                                    storeLoadUnit.reload();
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
                    store: storeLoadUnit,
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

