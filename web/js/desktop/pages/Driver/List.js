/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'data',
    id  : 'id'
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

function getPathAction (id, action) {
    action = action || "action";

    return Ext.get(id).getAttribute(action);
}

var storeAllPlayer = new Ext.data.JsonStore({
    model: 'Driver',
    proxy: new Ext.data.HttpProxy({
        url: getPathAction("Common_LoadDriver"),
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
        url: getPathAction("updateData")
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
                    store: storeAllPlayer,
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
//                                width: 150,
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

                  storeAllPlayer.insert(0, r);
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

                  console.log(sm);
                  console.log(sm.getSelection());

                  rowEditing.cancelEdit();
                  storeAllPlayer.remove(sm.getSelection());

                  Ext.Ajax.request({
                    url: getPathAction("deleteData")
//                    , params: {id : sm.getSelection().data.id}
                    , params: {id : sm.getSelection().id}
                    , method: 'POST'
                    , headers: {
                      'content-type': 'application/json'
                    }
                    , success: function (data) {
                      if (storeAllPlayer.getCount() > 0) {
                        sm.select(0);
                      }
                    }
                  });
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

