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
                   {name: 'numberPlate', type: 'string'},
                   {name: 'name', type: 'string'}];

MyUtil.Object.defineModel('Driver', objectField);

var storeLoadDriver = new Ext.data.JsonStore({
    model: 'Driver',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Driver_Load"),
        reader: readerJson
    }),
    pageSize: 5,
    autoLoad: ({params:{limit: 5, page: 1, start: 1}}, false)
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

Ext.define('SrcPageUrl.Invoices.Input', {
    extend: 'Ext.ux.desktop.Module',

    requires: [
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.grid.Panel',
        'Ext.grid.RowNumberer'
    ],

    id:'invoices-input',

    init : function(){
        this.launcher = {
            text: 'Phiếu Nhập',
            iconCls:'icon-grid'
        };
    },

    createWindow : function(){
        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('grid-win');

        var formFields = {
            xtype: 'fieldset',
            columnWidth: 0.4,
            labelWidth: 200,
//            title: 'Fieldset 1',
//                        collapsible: true,
            defaultType: 'textfield',
            defaults: {
                anchor: '60%'
            },
            layout: 'anchor',
            items: [{
                fieldLabel: 'Số Phiếu',
                name: 'product_name'
            }, {
                fieldLabel: 'Ngày Lập Phiếu',
                name: 'product_code',
                xtype: 'datefield'
            }, {
                fieldLabel: 'Nhà Phân Phối',
                name: 'amount'
            }, {
                fieldLabel: 'Người Giao Hàng',
                name: 'price'
            }, {
                fieldLabel: 'Người Lập',
                name: 'amount'
            }, {
//                labelWidth: 200,
                fieldLabel: 'Địa Chỉ Nhà PP',
                name: 'dealer'
            },{
                fieldLabel: 'Điện Thoại Nhà PP',
                name: 'date'
            },{
                xtype: 'button',
                text: 'Thêm',
                width: 30,
                handler : function() {
                    alert('Thêm mới Phiếu');
                }
            },{
                xtype: 'button',
                text: 'Lưu',
                width: 30,
                handler : function() {
                    alert('Lưu Phiếu');
                }
            },{
                xtype: 'button',
                text: 'Xóa',
                width: 30,
                handler : function() {
                    alert('Xóa Phiếu');
                }
            }]

        };

            var columnsDriver = [
//            new Ext.grid.RowNumberer({text: 'STT'}),
            { xtype : 'rownumberer', text : 'STT', width : 30 },
            {
                text: "Tên Sản Phẩm",
                width: 150,
                flex: 1,
//                dataIndex: 'name',
                editor: {
                    allowBlank: true
                }
            }, {
                    text: "Mã Sản Phẩm",
                    flex: 2,
//                dataIndex: 'numberPlate',
                    editor: {
                        allowBlank: true
                    }
            }, {
                    text: "Đơn Vị Tính",
                    flex: 2,
//                dataIndex: 'numberPlate',
                    editor: {
                        allowBlank: true
                    }
            }, {
                text: "Số lượng",
                flex: 2,
//                dataIndex: 'numberPlate',
                editor: {
                    allowBlank: true
                }
            }, {
                text: "Đơn giá",
                flex: 2,
//                dataIndex: 'numberPlate',
                editor: {
                    allowBlank: true
                }
            }, {
                text: "Thành Tiền",
                width: 150,
                flex: 1,
//                dataIndex: 'name',
                editor: {
                    allowBlank: true
                }
            }
        ];

        var rowModel = Ext.create('Ext.selection.RowModel', {
            mode : "MULTI",
            onKeyPress: function(e, t) {
                console.log(e);
            }
        });

        if(!win){
            win = desktop.createWindow({
                id: 'input-form',
                title:'Phiếu Nhập',
                width:740,
                height:500,
                iconCls: 'icon-grid',
                animCollapse:false,
                constrainHeader:true,
//                layout: 'fit',
                items: [
                    formFields,
                  {
                    border: false,
                    id: 'grid-input',
                    xtype: 'grid',
                    store: storeLoadDriver,
                    selModel: rowModel,
                    columns: columnsDriver,
                    plugins: [rowEditing],
                    listeners: {
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
                  var r = Ext.create('Driver', {
                    id: '',
                    name: '',
                    numberPlate: ''
                  });

                  storeLoadDriver.insert(0, r);
                  rowEditing.startEdit(0, 0);
                }
              },'-',{
                text:'Remove',
                tooltip:'Remove the selected item',
                iconCls:'remove',
                listeners:  {
                  click: function () {
                    var selection = Ext.getCmp('grid-driver-list').getView().getSelectionModel().getSelection();

                    if (selection.length > 0) {
                      Ext.MessageBox.confirm('Delete', 'Are you sure ?', function(btn){
                        if (btn === 'yes') {
                          var arrId = [];
                          Ext.each(selection, function(v, k) {
                            arrId[k] = v.data.id;
                          });

                          Ext.Ajax.request({
                            url: MyUtil.Path.getPathAction("Driver_Delete"),
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            jsonData: {'params' : arrId},
                            scope: this,
                            success: function(msg) {
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
                      });
                    } else {
                      MyUtil.Message.MessageError();
                    }
                  }
                }
              }],
              bbar: new Ext.PagingToolbar({
                store: storeLoadDriver,
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

