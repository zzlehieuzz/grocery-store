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
                   {name: 'invoiceId', type: 'int'},
                   {name: 'productId', type: 'int'},
                   {name: 'price', type: 'string'},
                   {name: 'unit', type: 'int'},
                   {name: 'quantity', type: 'int'},
                   {name: 'amount', type: 'int'}];

MyUtil.Object.defineModel('Invoice', objectField);

var storeLoadInvoice = new Ext.data.JsonStore({
    model: 'Invoice',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Input_Load"),
        reader: readerJson
    }),
    pageSize: 5,
    autoLoad: ({params:{limit: 5, page: 1, start: 1}}, false)
});

var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
  clicksToMoveEditor: 1,
  autoCancel: false,
  listeners:{
//    'canceledit': function(rowEditing, context) {
//
//    },
    'edit': function(rowEditing, context) {
//      console.log(context.record.data);

      Ext.Ajax.request({
        url: MyUtil.Path.getPathAction("Input_Update")
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
//            text: 'Phiếu Nhập',
            text: 'invoices input',
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
                name: 'invoice_number',
                id: 'invoice_number'
            }, {
                fieldLabel: 'Ngày Lập Phiếu',
                name: 'create_invoice_date',
                id: 'create_invoice_date',
                xtype: 'datefield'
            }, {
                fieldLabel: 'Nhà Phân Phối',
                name: 'subject',
                id: 'subject'
            }, {
                fieldLabel: 'Người Giao Hàng',
                name: 'delivery_receiver_man',
                id: 'delivery_receiver_man'
            }, {
                fieldLabel: 'Người Lập',
                name: 'create_invoice_man',
                id: 'create_invoice_man'
            }, {
//                labelWidth: 200,
                fieldLabel: 'Địa Chỉ Nhà PP',
                name: 'address',
                id: 'address'
            },{
                fieldLabel: 'Điện Thoại Nhà PP',
                name: 'phone_number',
                id: 'phone_number'
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

                    //Get value form
                    var invoice_number = Ext.getCmp('invoice_number').getValue();
                    var create_invoice_date = Ext.util.Format.date(Ext.getCmp('create_invoice_date').getValue(), 'Y-m-d');
                    var subject = Ext.getCmp('subject').getValue();
                    var delivery_receiver_man = Ext.getCmp('delivery_receiver_man').getValue();
                    var create_invoice_man = Ext.getCmp('create_invoice_man').getValue();
                    var address = Ext.getCmp('address').getValue();
                    var phone_number = Ext.getCmp('phone_number').getValue();

                    var form_fields_value = [{'invoice_number': invoice_number,
                        'create_invoice_date': create_invoice_date,
                        'subject': subject,
                        'delivery_receiver_man': delivery_receiver_man,
                        'create_invoice_man': create_invoice_man,
                        'address': address,
                        'phone_number': phone_number
                    }];

                    //Get value grid product
                    var selection = Ext.getCmp('grid-input').getView().getStore().getRange();
//                    var selection = Ext.getCmp('grid-input').getView().getSelectionModel().selectAll();
//                    var selection = Ext.getCmp('grid-input').record;

                    var gridData = [];
                    var tourData = selection;
                    Ext.each(tourData, function (record) {
                        gridData.push(record.data);
                    });

                    console.log(Ext.getCmp('grid-input'));

                    var params = {'form_fields_value': form_fields_value, 'grid_value': gridData};

                    Ext.Ajax.request({
                        url: MyUtil.Path.getPathAction("Input_Update")
                        , params: JSON.stringify(params)
                        , method: 'POST'
                        , headers: {
                            'content-type': 'application/json'
                        }
                        , success: function (data) {
                            // do any thing
                        }
                    });

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

            var columnsInvoice = [
//            new Ext.grid.RowNumberer({text: 'STT'}),
            { xtype : 'rownumberer', text : 'STT', width : 30 },
            {
                text: "Tên Sản Phẩm",
                width: 150,
                flex: 1,
                dataIndex: 'productId',
                editor: {
                    allowBlank: true
                }
            }, {
                    text: "Mã Sản Phẩm",
                    flex: 2,
                    dataIndex: 'productId',
                    editor: {
                        allowBlank: true
                    }
            }, {
                    text: "Đơn Vị Tính",
                    flex: 2,
                    dataIndex: 'unit',
                    editor: {
                        allowBlank: true
                    }
            }, {
                text: "Số lượng",
                flex: 2,
                dataIndex: 'quantity',
                editor: {
                    allowBlank: true
                }
            }, {
                text: "Đơn giá",
                flex: 2,
                dataIndex: 'price',
                editor: {
                    allowBlank: true
                }
            }, {
                text: "Thành Tiền",
                width: 150,
                flex: 1,
                dataIndex: 'amount',
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
                    store: storeLoadInvoice,
                    selModel: rowModel,
                    columns: columnsInvoice,
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
                  var r = Ext.create('Invoice', {
                      id: '',
                      invoiceId: '',
                      productId: '',
                      price: '',
                      unit: '',
                      quantity: '',
                      amount: ''
                  });

                  storeLoadInvoice.insert(0, r);
                  rowEditing.startEdit(0, 0);
                }
              },'-',{
                text:'Remove',
                tooltip:'Remove the selected item',
                iconCls:'remove',
                listeners:  {
                  click: function () {
                    var selection = Ext.getCmp('grid-input').getView().getSelectionModel().getSelection();

                    if (selection.length > 0) {
                      Ext.MessageBox.confirm('Delete', 'Are you sure ?', function(btn){
                        if (btn === 'yes') {
                          var arrId = [];
                          Ext.each(selection, function(v, k) {
                            arrId[k] = v.data.id;
                          });

                          Ext.Ajax.request({
                            url: MyUtil.Path.getPathAction("Input_Delete"),
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            jsonData: {'params' : arrId},
                            scope: this,
                            success: function(msg) {
                              if (msg.status) {
                                storeLoadInvoice.reload();
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
                store: storeLoadInvoice,
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

