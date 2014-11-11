/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'grid_data',
    id  : 'id',
    totalProperty: 'total'
};

var readerJsonForm = {
    type: 'json',
    root: 'form_data',
    id  : 'id',
    totalProperty: 'total'
};

var readerJsonCommon = {
    type: 'json',
    root: 'data',
    id  : 'id',
    totalProperty: 'total'
};

//GridField
var objectGridField = [{name: 'id',   type: 'int'},
                       {name: 'invoiceId', type: 'int'},
                       {name: 'productId', type: 'int'},
                       {name: 'price', type: 'string'},
                       {name: 'unit', type: 'int'},
                       {name: 'quantity', type: 'int'},
                       {name: 'amount', type: 'int'}];

//FormField
var objectFormField = [{name: 'id',   type: 'int'},
                        {name: 'invoiceNumber', type: 'string'},
                        {name: 'createInvoiceDate', type: 'string'},
                        {name: 'subject', type: 'int'},
                        {name: 'createInvoiceMan', type: 'string'},
                        {name: 'phoneNumber', type: 'string'},
                        {name: 'invoiceType', type: 'string'},
                        {name: 'paymentStatus', type: 'int'}
                    ];

//Distributor
var objectDistributorField = [{name: 'id',   type: 'int'},
                                {name: 'name', type: 'string'},
                                {name: 'address', type: 'string'},
                                {name: 'phoneNumber', type: 'string'}
                            ];

//Product
var objectProductField = [{name: 'id',   type: 'int'},
                        {name: 'name', type: 'string'},
                        {name: 'code', type: 'string'},
                        {name: 'productUnitId', type: 'int'}
                        ];

MyUtil.Object.defineModel('Input', objectGridField);
MyUtil.Object.defineModel('Input2', objectFormField);
MyUtil.Object.defineModel('DistributorCmb', objectDistributorField);
MyUtil.Object.defineModel('ProductCmb', objectProductField);

var storeLoadInput = new Ext.data.JsonStore({
    model: 'Input',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Input_Load"),
        reader: readerJson
    }),
    pageSize: 5,
    autoLoad: ({params:{limit: 5, page: 1, start: 1}}, false)
});

var storeLoadInputForm = new Ext.data.JsonStore({
    model: 'Input2',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Input_Load"),
        reader: readerJsonForm
    }),
    autoLoad: true
});

var storeLoadDistributorCmb = new Ext.data.JsonStore({
    model: 'DistributorCmb',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Distributor_Load"),
        reader: readerJsonCommon
    }),
    autoLoad: true
});

var storeLoadProductCmb = new Ext.data.JsonStore({
    model: 'ProductCmb',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Product_Load"),
        reader: readerJsonCommon
    }),
    autoLoad: true
});

//Default value
var formData = { 'id' : '',
                'invoiceNumber' : '',
//                'createInvoiceDate': new Date('d-m-Y'),
                'createInvoiceDate': '',
                'subject': 1,
                'createInvoiceMan': '',
                'phoneNumber': '',
                'invoiceType': '',
                'paymentStatus': ''};

storeLoadInputForm.on('load', function(){
    if (storeLoadInputForm.data.items[0]) {
        formData = storeLoadInputForm.data.items[0].data;
    }
});

var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
  clicksToMoveEditor: 1,
  autoCancel: false,
  listeners:{
//    'canceledit': function(rowEditing, context) {
//
//    },
    'edit': function(rowEditing, context) {

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
            items: [ {
                xtype:'hidden',
                name:'invoiceId',
                id:'invoiceId',
                value: formData.id
            },{
                fieldLabel: 'Số Phiếu',
                name: 'invoice_number',
                id: 'invoice_number',
                value: formData.invoiceNumber
            }, {
                fieldLabel: 'Ngày Lập Phiếu',
                name: 'create_invoice_date',
                id: 'create_invoice_date',
                xtype: 'datefield',
                value: formData.createInvoiceDate
            },{
                xtype:'combobox',
                listConfig: {minWidth: 180},
                fieldLabel: 'Nhà Phân Phối',
                value: formData.subject,
                name: 'subject',
                id: 'subject',
                valueField: 'id',
                typeAhead: true,
                triggerAction: 'all',
                selectOnTab: true,
                store: storeLoadDistributorCmb,
                displayField: 'name',
                lazyRender: true,
                queryMode: 'local',
                listeners: {
                    select : function(combo, record, index){
                        Ext.getCmp('address').setValue(record[0].data.address);
                        Ext.getCmp('phone_number').setValue(record[0].data.phoneNumber);
                    }}
            }, {
                fieldLabel: 'Người Giao Hàng',
                name: 'delivery_receiver_man',
                id: 'delivery_receiver_man',
                value: formData.deliveryReceiverMan
            }, {
                fieldLabel: 'Người Lập',
                name: 'create_invoice_man',
                id: 'create_invoice_man',
                value: formData.createInvoiceMan
            }, {
//                labelWidth: 200,
                fieldLabel: 'Địa Chỉ Nhà PP',
                name: 'address',
                id: 'address',
                value: formData.address
            },{
                fieldLabel: 'Điện Thoại Nhà PP',
                name: 'phone_number',
                id: 'phone_number',
                value: formData.phoneNumber
            },{
                xtype: 'button',
                text: 'Thêm',
                width: 30,
                handler : function() {
                    Ext.getCmp('invoice_number').setValue('');
                    Ext.getCmp('create_invoice_date').setValue('');
                    Ext.getCmp('subject').setValue('');
                    Ext.getCmp('delivery_receiver_man').setValue('');
                    Ext.getCmp('create_invoice_man').setValue('');
                    Ext.getCmp('address').setValue('');
                    Ext.getCmp('phone_number').setValue('');
                }
            },{
                xtype: 'button',
                text: 'Lưu',
                width: 30,
                handler : function() {

                    //Get value form
                    var id = Ext.getCmp('invoiceId').getValue();
                    var invoice_number = Ext.getCmp('invoice_number').getValue();
                    var create_invoice_date = Ext.util.Format.date(Ext.getCmp('create_invoice_date').getValue(), 'Y-m-d');
                    var subject = Ext.getCmp('subject').getValue();
                    var delivery_receiver_man = Ext.getCmp('delivery_receiver_man').getValue();
                    var create_invoice_man = Ext.getCmp('create_invoice_man').getValue();
                    var address = Ext.getCmp('address').getValue();
                    var phone_number = Ext.getCmp('phone_number').getValue();

                    var form_fields_value = [{'id': id,
                                            'invoice_number': invoice_number,
                                            'create_invoice_date': create_invoice_date,
                                            'subject': subject,
                                            'delivery_receiver_man': delivery_receiver_man,
                                            'create_invoice_man': create_invoice_man,
                                            'address': address,
                                            'phone_number': phone_number
                    }];

                    //Get value grid product
                    var selection = Ext.getCmp('grid-input').getView().getStore().getRange();

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
                    Ext.MessageBox.confirm('Delete', 'Are you sure ?', function(btn){
                        if (btn === 'yes') {

                            Ext.Ajax.request({
                                url: MyUtil.Path.getPathAction("Input_Delete"),
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                jsonData: {'params' : 0},
                                scope: this,
                                success: function(msg) {
                                    if (msg.status) {
                                        storeLoadInput.reload();
                                        console.log('success');
                                    }
                                },
                                failure: function(msg) {
                                    console.log('failure');
                                }
                            });
                        }
                    });
                }
            }]

        };

            var columnsInvoice = [
            { xtype : 'rownumberer', text : 'STT', width : 30 },
            {
                header: 'Tên Sản Phẩm',
                dataIndex: 'productId',
                editor:
                {
                    xtype: 'combobox',
                    store: storeLoadProductCmb,
                    displayField: 'name',
                    valueField: 'id'
                }
            }, {
                header: 'Mã Sản Phẩm',
                dataIndex: 'productId',
                editor:
                {
                    xtype: 'combobox',
                    store: storeLoadProductCmb,
                    displayField: 'code',
                    valueField: 'id'
                }
            }
            ,{
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
                    store: storeLoadInput,
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
                  var r = Ext.create('Input', {
                      id: '',
                      invoiceId: '',
                      productId: '',
                      price: '',
                      unit: '',
                      quantity: '',
                      amount: ''
                  });

                  storeLoadInput.insert(0, r);
                  rowEditing.startEdit(0, 0);
                }
              },'-',{
                text:'Remove',
                tooltip:'Remove the selected item',
                iconCls:'remove',
                listeners:  {
                  click: function () {
                      var selection = Ext.getCmp('grid-input').getView().getSelectionModel().getSelection()[0];
                      if (selection) {
                          storeLoadInput.remove(selection);
                      }
                  }
                }
              }],
              bbar: new Ext.PagingToolbar({
                store: storeLoadInput,
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

