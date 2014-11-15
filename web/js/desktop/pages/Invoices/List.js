/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'data',
    id  : 'id',
    totalProperty: 'total'
};

var readerGridJson = {
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

var objectField = [{name: 'id',   type: 'int'},
                   {name: 'subjectName', type: 'string'},
                   {name: 'invoiceType', type: 'int'},
                   {name: 'invoiceTypeText', type: 'string'},
                   {name: 'invoiceNumber', type: 'string'},
                   {name: 'paymentStatus', type: 'string'},
                   {name: 'amount', type: 'INT'},
                   {name: 'createInvoiceDate', type: 'string'}];

MyUtil.Object.defineModel('Invoice', objectField);

var storeLoadInvoice = new Ext.data.JsonStore({
    model: 'Invoice',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Invoice_Load"),
        reader: readerJson
    }),
    pageSize: 5,
    autoLoad: ({params:{limit: 5, page: 1, start: 1}}, false)
});

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
    {name: 'address', type: 'string'},
    {name: 'deliveryReceiverMan', type: 'string'},
    {name: 'createInvoiceMan', type: 'string'},
    {name: 'phoneNumber', type: 'string'},
    {name: 'invoiceType', type: 'string'},
    {name: 'paymentStatus', type: 'int'}
];

MyUtil.Object.defineModel('Input2', objectFormField);

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
MyUtil.Object.defineModel('DistributorCmb', objectDistributorField);
MyUtil.Object.defineModel('CustomerCmb', objectDistributorField);
MyUtil.Object.defineModel('ProductCmb', objectProductField);

var storeLoadInput = new Ext.data.JsonStore({
    model: 'Input',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Input_Load"),
        reader: readerGridJson
    }),
    pageSize: 5,
    autoLoad: ({params:{limit: 5, page: 1, start: 1}}, false)
});

var storeLoadDistributorCmb = new Ext.data.JsonStore({
    model: 'DistributorCmb',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Distributor_Load"),
        reader: readerJsonCommon
    }),
    autoLoad: true
});

var storeLoadCustomerCmb = new Ext.data.JsonStore({
    model: 'CustomerCmb',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Customer_Load"),
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

var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
    clicksToEdit: 1
});

Ext.define('SrcPageUrl.Invoices.List', {
    extend: 'Ext.ux.desktop.Module',

    requires: [
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.grid.Panel',
        'Ext.grid.RowNumberer'
    ],

    id:'invoices-list',

    init : function(){
        this.launcher = {
            text: 'invoices management'.Translator('Module'),
//            text: 'Quản lý nhập xuất',
            iconCls:'icon-grid'
        };
    },

    createWindow : function(){
        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('grid-win');

        var formFieldsList = {
            xtype: 'fieldset',
            columnWidth: 0.4,
            labelWidth: 200,
            defaultType: 'textfield',
            defaults: {
                anchor: '100%'
            },
            layout: 'anchor',
            items: [{
                xtype: 'radiogroup',
                anchor: '80%',
                fieldLabel: 'Loại Phiếu',
                columns: 3,
                name: 'invoiceTypeRadio',
                id: 'invoiceTypeRadio',
                vertical: true,
                items: [
                    {boxLabel: 'All', name: 'rb', inputValue: '0', checked: true},
                    {boxLabel: 'Phiếu Nhập', name: 'rb', inputValue: '1'},
                    {boxLabel: 'Phiếu Xuất', name: 'rb', inputValue: '2'}
                ]
            },{
                xtype: 'container',
                anchor: '100%',
                layout: 'hbox',
                items:[{
                    xtype: 'container',
                    padding: '0 5 5 0',
                    layout: 'anchor',
                    items: [  {
                        fieldLabel: 'Từ Ngày',
                        xtype: 'datefield',
                        name: 'fromDate',
                        id: 'fromDate',
                        anchor: '50%'
                    }]
                },{
                    xtype: 'container',
                    layout: 'anchor',
                    items: [ {
                        fieldLabel: 'Đến Ngày',
                        name: 'toDate',
                        id: 'toDate',
                        xtype: 'datefield',
                        anchor: '50%'
                    }]
                }
                ]
            },,{
                xtype: 'container',
                anchor: '50%',
                layout: 'hbox',
                items:[{
                    xtype: 'container',
                    padding: '0 5 5 0',
                    layout: 'anchor',
                    items: [ {
                        xtype: 'button',
                        text: 'Tìm',
                        width: 50,
//                        anchor: '30%',
                        handler : function() {

                            var invoiceType = Ext.getCmp('invoiceTypeRadio').getValue().rb;
                            var fromDate = Ext.util.Format.date(Ext.getCmp('fromDate').getValue(), 'Y-m-d');
                            var toDate = Ext.util.Format.date(Ext.getCmp('toDate').getValue(), 'Y-m-d');

                            storeLoadInvoice.reload({params:{limit: 5, page: 1, start: 1, invoiceType: invoiceType, fromDate: fromDate, toDate: toDate}});
                        }
                    }]
                },{
                    xtype: 'container',
                    layout: 'anchor',
                    items: [ {
                        xtype: 'button',
                        text: 'Tạo Mới Phiếu',
                        width: 100,
                        handler : function() {

                            var invoiceType = Ext.getCmp('invoiceTypeRadio').getValue().rb;
                            if (invoiceType == 0) {
                                alert("Vui lòng chọn loại phiếu cần tạo");
                            } else {
                                createPopupInvoiceForm(null, invoiceType);
                            }
                        }
                    }]
                }
                ]
            }]

        };

        var columnsInvoice = [
            new Ext.grid.RowNumberer(),
            {
//                text: "subject".Translator('Common'),
                text: "Tên Khách Hàng",
                width: 100,
                dataIndex: 'subjectName',
                editor: {
                    allowBlank: true
                }
            }, {
//                text: "create_invoice_date".Translator('Invoice'),
                text: "Ngày",
                flex: 1,
                dataIndex: 'createInvoiceDate',
                editor: {
                    allowBlank: true
                }
            }, {
//                text: "create_invoice_date".Translator('Invoice'),
                text: "Thành Tiền",
                flex: 1,
                dataIndex: 'amount',
                editor: {
                    allowBlank: true
                }
            }, {
//                text: "create_invoice_date".Translator('Invoice'),
                text: "Loại Phiếu",
                flex: 1,
                dataIndex: 'invoiceTypeText',
                editor: {
                    allowBlank: true
                }
            }, {
//                text: "create_invoice_date".Translator('Invoice'),
                text: "Số Phiếu",
                flex: 2,
                dataIndex: 'invoiceNumber',
                editor: {
                    allowBlank: true
                }
            },
            {
                text: '',
                renderer: function(val,meta,rec) {
                    var id = Ext.id();
                    Ext.defer(function() {
                        Ext.widget('button', {
                            renderTo: id,
                            text: 'Xem Chi Tiết',
                            scale: 'small',
                            handler: function() {
                                var invoiceId = rec.data.id;
                                var invoiceType2 = rec.data.invoiceType;
                                createPopupInvoiceForm(invoiceId, invoiceType2);
                            }
                        });
                    }, 50);

                    return Ext.String.format('<div id="{0}"></div>', id);
                }
            }, {
//                text: "create_invoice_date".Translator('Invoice'),
                text: "Trạng Thái",
                flex: 2,
                dataIndex: 'paymentStatus',
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
                id: 'invoice-list',
//                title:'invoice management'.Translator('Module'),
                title:'Quản lý nhập xuất',
                width:600,
                height:500,
                iconCls: 'icon-grid',
                animCollapse:false,
                constrainHeader:true,
//                layout: 'fit',
                items: [
                    formFieldsList,
                  {
                    border: false,
                    id: 'grid-invoice-list',
                    xtype: 'grid',
                    height:300,
                    store: storeLoadInvoice,
                    selModel: rowModel,
                    columns: columnsInvoice,
                    listeners: {
                      beforerender: function () {
                        this.store.load();
                      }
                    }
                  }
                ],
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

function createPopupInvoiceForm(invoiceId, invoiceType){
    var invoiceTitle = "";
    var subjectT = "";
    var deliveryReceiver = "";
    var addressSubject = "";
    var phoneSubject = "";
    var date_format = 'd/m/Y';

    if (invoiceType == 1) {
        invoiceTitle = "Phiếu Nhập";
        subjectT = 'Nhà Phân Phối';
        deliveryReceiver = 'Người Giao Hàng';
        addressSubject = 'Địa Chỉ Nhà PP';
        phoneSubject = 'Điện Thoại Nhà PP';
    } else {
        invoiceTitle = "Phiếu Xuất";
        subjectT = 'Khách Hàng';
        deliveryReceiver = 'Người Nhận Hàng';
        addressSubject = 'Địa Chỉ Khách Hàng';
        phoneSubject = 'Điện Thoại Khách Hàng';
        storeLoadDistributorCmb = storeLoadCustomerCmb;
    }

    var storeLoadInputForm = new Ext.data.JsonStore({
        model: 'Input2',
        proxy: new Ext.data.HttpProxy({
            url: MyUtil.Path.getPathAction("Input_Load"),
            reader: readerJsonForm
        }),
        autoLoad: false
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

    storeLoadInputForm.load({
        params:{limit: 5, page: 1, start: 1, id: invoiceId},
        callback : function(records, options, success) {
            if (storeLoadInputForm.data.items[0]) {
                formData = storeLoadInputForm.data.items[0].data;

                Ext.getCmp('invoice_number').setValue(formData.invoiceNumber);
                Ext.getCmp('create_invoice_date').setValue(formData.createInvoiceDate);
                Ext.getCmp('subject').setValue(formData.subject);
                Ext.getCmp('delivery_receiver_man').setValue(formData.deliveryReceiverMan);
                Ext.getCmp('create_invoice_man').setValue(formData.createInvoiceMan);
                Ext.getCmp('address').setValue(formData.address);
                Ext.getCmp('phone_number').setValue(formData.phoneNumber);
            }
    }});

    var formFieldsAll = {
        xtype: 'fieldset',
        id: 'formFieldsAll',
        name: 'formFieldsAll',
        padding: '8 5 5 5',
        items:[{
            xtype: 'container',
            anchor: '100%',
            layout: 'hbox',
            items:[{
                xtype: 'container',
                style: 'padding-left: 10px;',
                flex: 1,
                layout: 'anchor',
                items: [  {
                    xtype:'hidden',
                    name:'invoiceId',
                    id:'invoiceId',
                    value: invoiceId
                }, {
                    xtype:'hidden',
                    name:'invoiceTypeHidden',
                    id:'invoiceTypeHidden',
                    value: invoiceType
                },{
                    fieldLabel: 'Số Phiếu',
                    labelWidth: 150,
                    xtype:'textfield',
                    name: 'invoice_number',
                    id: 'invoice_number'
                }, {
                    fieldLabel: 'Ngày Lập Phiếu',
                    labelWidth: 150,
                    name: 'create_invoice_date',
                    id: 'create_invoice_date',
                    xtype: 'datefield',
                    format: date_format,
                    altFormats: date_format
                },{
                    xtype:'combobox',
                    labelWidth: 150,
                    listConfig: {minWidth: 180},
                    fieldLabel: subjectT,
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
                    fieldLabel: deliveryReceiver,
                    labelWidth: 150,
                    xtype:'textfield',
                    name: 'delivery_receiver_man',
                    id: 'delivery_receiver_man'
                }]
            },{
                xtype: 'container',
                flex: 1,
                layout: 'anchor',
                items: [ {
                    fieldLabel: 'Người Lập',
                    labelWidth: 150,
                    xtype:'textfield',
                    name: 'create_invoice_man',
                    id: 'create_invoice_man'
                }, {
                    fieldLabel: addressSubject,
                    labelWidth: 150,
                    xtype:'textfield',
                    name: 'address',
                    id: 'address'
                },{
                    fieldLabel: phoneSubject,
                    labelWidth: 150,
                    xtype:'textfield',
                    name: 'phone_number',
                    id: 'phone_number'
                }]
            }
            ]
        }]
    };

    var columnsInvoicePopup = [
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
//            summaryType: 'sum',
//            summaryRenderer: function(value) {
//                return ((value === 0 || value > 1) ? '(' + 'total'.Translator('Common') + ': ' + value + ')' : '(' + 'total'.Translator('Common') + ': 1)');
//            },
            editor: {
                allowBlank: true
            }
        }, {
            text: "Số lượng",
            flex: 2,
            dataIndex: 'quantity',
//            summaryType: 'sum',
//            renderer: function(value){
//                return value;
//            },
//            summaryRenderer: function(value) {
//                return value;
//            },
            editor: {
                allowBlank: true,
                listeners : {
                    change : function(field, newValue, o ,e) {
                        var models = Ext.getCmp('grid-input-output').getStore().getRange();

                        var grid = this.up().up();
                        var selModel = grid.getSelectionModel();

                        var amountComp = (newValue * parseFloat(models[0].data.price));
                        if (isNaN(amountComp)) {
                            amountComp = 0;
                        }
                        selModel.getSelection()[0].set('amount', parseFloat(amountComp));
                    }
                }
            }
        }, {
            text: "Đơn giá",
            flex: 2,
            dataIndex: 'price',
//            summaryType: 'sum',
//            renderer: function(value){
//                return Ext.util.Format.currency(value, ' ', decimalPrecision)
//            },
//            summaryRenderer: function(value) {
//                return Ext.util.Format.currency(value, 'VND ', decimalPrecision);
//            },
            editor: {
                allowBlank: true,
                listeners : {
                    change : function(field, newValue) {
                        var models = Ext.getCmp('grid-input-output').getStore().getRange();

                        var grid = this.up().up();
                        var selModel = grid.getSelectionModel();
                        var amountComp = (parseInt(models[0].data.quantity) * parseFloat(newValue));
                        if (isNaN(amountComp)) {
                            amountComp = 0;
                        }
                        selModel.getSelection()[0].set('amount', parseFloat(amountComp));
                    }
                }
            }
        }, {
            text: "Thành Tiền",
            width: 150,
            flex: 1,
            dataIndex: 'amount',
            summaryType: 'count',
            renderer: function(value){
                return value;
            },
            summaryRenderer: function(value) {
                return value;
            },
            editor: {
                allowBlank: true
            }
        }
    ];

    var setting = new Ext.FormPanel({
        frame:true,
        items: [{
            layout: 'form',
            items: [formFieldsAll,
                    {
                        xtype: 'container',
                        anchor: '50%',
                        layout: 'hbox',
                        items:[{
                            xtype: 'container',
                            padding: '0 5 5 0',
                            layout: 'anchor',
                            items: [ {
                                xtype: 'button',
                                text: 'Thêm Sản Phẩm',
                                width: 100,
                                handler : function() {
                                    cellEditing.cancelEdit();

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
                                  cellEditing.startEdit(0, 0);
                                }
                            }]
                        },{
                            xtype: 'container',
                            layout: 'anchor',
                            items: [ {
                                xtype: 'button',
                                text: 'Xóa Sản Phẩm',
                                width: 100,
                                listeners:  {
                                    click: function () {
                                      var selection = Ext.getCmp('grid-input-output').getView().getSelectionModel().getSelection()[0];
                                      if (selection) {
                                          storeLoadInput.remove(selection);
                                      }
                                  }
                                }
                            }]
                        }
                        ]
                    }
                   ,
                {
                    border: false,
                    id: 'grid-input-output',
                    xtype: 'grid',
                    height:240,
                    store: storeLoadInput,
                    selModel: Ext.create('Ext.selection.RowModel', {
                        mode : "MULTI",
                        onKeyPress: function(e, t) {
                            console.log(e);
                        }
                    }),
                    columns: columnsInvoicePopup,
                    plugins: [cellEditing],
//                    features: [{
//                        ftype: 'groupingsummary',
//                        groupHeaderTpl: Ext.create('Ext.XTemplate',  '<label>' + ': {amount}' + '</label>'),
//                        hideGroupedHeader: true,
//                        enableGroupingMenu: true,
//                        collapsible: false
//                    }],
                    listeners: {
                        beforerender: function () {
                            this.store.load({params:{limit: 5, page: 1, start: 1, id: invoiceId}});
                        }
                    }, bbar: new Ext.PagingToolbar({
                        store: storeLoadInput,
                        displayInfo:true
                      })
                }]
        }],

        buttons: [{
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
                var invoice_type = Ext.getCmp('invoiceTypeHidden').getValue();

                var form_fields_value = [{'id': id,
                    'invoiceType': invoice_type,
                    'invoiceNumber': invoice_number,
                    'createInvoiceDate': create_invoice_date,
                    'subject': subject,
                    'deliveryReceiverMan': delivery_receiver_man,
                    'createInvoiceMan': create_invoice_man,
                    'address': address,
                    'phoneNumber': phone_number
                }];

                //Get value grid product
                var selection = Ext.getCmp('grid-input-output').getView().getStore().getRange();

                var gridData = [];
                var tourData = selection;
                Ext.each(tourData, function (record) {
                    gridData.push(record.data);
                });

                var params = {'form_fields_value': form_fields_value, 'grid_value': gridData};

                if (invoice_number == "") {
                   alert("Vui lòng nhập số phiếu.");
                } else {
                    Ext.Ajax.request({
                        url: MyUtil.Path.getPathAction("Input_Update")
                        , params: JSON.stringify(params)
                        , method: 'POST'
                        , headers: {
                            'content-type': 'application/json'
                        }
                        , success: function (data) {
                            editWindow.close();
                            storeLoadInput.reload();
                        }
                    });
                }
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
                            jsonData: {'params' : Ext.getCmp('invoiceId').getValue()},
                            scope: this,
                            success: function(msg) {
                                if (msg.status) {
                                    editWindow.close();
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
    });

    var editWindow = new Ext.Window({
        title: invoiceTitle,
        width: 700,
        height: 500,
        closable: true,
        closeAction : 'destroy',
        resizable: false,
        modal: true,
        autoHeight: true,
        draggable: true,
        items: [setting],
        listeners: {
            close: function(p){
                formClosed = true;
            }
        }
    });

    editWindow.show();
}

