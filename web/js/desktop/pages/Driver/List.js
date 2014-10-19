/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'data',
    id  : 'id',
    totalProperty: 'total'
};

var objectField = [{name: 'id',       type: 'int'},
                   {name: 'userName', type: 'string'},
                   {name: 'name',     type: 'string'},
                   {name: 'email',    type: 'string'}];

function defineModel (modelName, objectField) {
    Ext.define(modelName, {
        extend: 'Ext.data.Model',
        fields: objectField
    });
}

defineModel('Driver', objectField);

var storeLoadDriver = new Ext.data.JsonStore({
    model: 'Driver',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Driver_Load"),
        reader: readerJson
    }),
    autoLoad: true
});

var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
  clicksToMoveEditor: 1,
  autoCancel: false,
  listeners:{
    'canceledit': function(rowEditing, context) {
      // Canceling editing of a locally added, unsaved record: remove it
//      if (context.record.phantom) {
//        context.store.remove(context.record);
//      }
    },
    'edit': function(rowEditing, context) {
      console.log(context.record.data);

      Ext.Ajax.request({
        url: MyUtil.Path.getPathAction("Driver_Update")
        , params: context.record.data
        , method: 'POST'
        , headers: {
          'content-type': 'application/json'
        }
        , success: function (data) {
          // do any thing
        }
      });

    }
  }
});

Ext.define('SrcPageUrl.Driver.List', {
    extend: 'Ext.ux.desktop.Module',

    requires: [
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.grid.Panel',
        'Ext.grid.RowNumberer'
    ],

    id:'driver-list',

    init : function(){
        this.launcher = {
            text: 'Driver List',
            iconCls:'icon-grid'
        };
    },

    createWindow : function(){
        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('grid-win');

        var rowModel = Ext.create('Ext.selection.RowModel', {
            mode : "MULTI",
            onKeyPress: function(e, t) {
                console.log(e);
            }
        });

        if(!win){
            win = desktop.createWindow({
                id: 'driver-list',
                title:'Driver List',
                width:740,
                height:480,
                iconCls: 'icon-grid',
                animCollapse:false,
                constrainHeader:true,
                layout: 'fit',
                items: [
                  {
                    border: false,
                    id: 'MyGridPanel',
                    xtype: 'grid',
                    store: storeLoadDriver,
                    selModel: rowModel,
                    columns: [
                      new Ext.grid.RowNumberer(),
                      {
                        text: "User Name",
                        width: 150,
                        flex: 1,
                        //                                sortable: true,
                        dataIndex: 'userName',
                        editor: {
                          allowBlank: true
                        }
                      }, {
                        text: "Name",
                        flex: 2,
                        dataIndex: 'name',
                        editor: {
                          allowBlank: true
                        }
                      }, {
                        text: "Email",
                        flex: 3,
                        dataIndex: 'email',
                        editor: {
                          allowBlank: true
                        }
                      }
                    ],
                    plugins: [rowEditing],
                    listeners: {
                      'selectionchange': function(view, records) {
//                        grid.down('#removeEmployee').setDisabled(!records.length);
                      }
                    }
                  }
                ],
              tbar:[{
                text:'Add Something',
                tooltip:'Add a new row',
                iconCls:'add',
                handler : function() {
                  rowEditing.cancelEdit();

                  // Create a model instance
                  var r = Ext.create('Driver', {
                    id: '',
                    userName: 'New Guy',
                    name: 'New Guy',
                    email: 'new@sencha-test.com'
                  });

                  storeLoadDriver.insert(0, r);
                  rowEditing.startEdit(0, 0);
                }
              }, '-', {
                text:'Options',
                tooltip:'Modify options',
                iconCls:'option'
              },'-',{
                text:'Remove Something',
                tooltip:'Remove the selected item',
                iconCls:'remove',
                handler: function() {
                  var grid = Ext.getCmp("MyGridPanel");
                  var sm = grid.getSelectionModel();
                    var obj = (sm.getSelection());
                    var ob = obj[0];

                    Ext.MessageBox.confirm('Delete', 'Are you sure ?', function(btn){
                        if(btn === 'yes'){
                            //some code
                            rowEditing.cancelEdit();
                            storeLoadDriver.remove(sm.getSelection());

                            Ext.Ajax.request({
                                url: MyUtil.Path.getPathAction("Driver_Delete")
                                , params: {id : ob.data.id}
                                , method: 'POST'
                                , headers: {
                                    'content-type': 'application/json'
                                }
                                , success: function (data) {
                                    if (storeLoadDriver.getCount() > 0) {
                                        sm.select(0);
                                    }
                                }
                            });
                        }
                        else{
                            //some code
                        }
                    });
                }
              }, '->', {
                  text: 'Reload',
                  iconCls:'reload',
                  handler: function(){
                      storeLoadUser.load();
                  }
              }]
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

