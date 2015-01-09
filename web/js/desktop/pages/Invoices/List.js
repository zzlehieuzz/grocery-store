/*
 * @author HieuNLD 2014/06/27
 */
var date_format = 'd/m/Y';

var readerJson = {
    type: 'json',
    root: 'data',
    id: 'id',
    totalProperty: 'total'
};

var readerGridJson = {
    type: 'json',
    root: 'grid_data',
    id: 'id',
    totalProperty: 'total'
};

var readerJsonForm = {
    type: 'json',
    root: 'form_data',
    id: 'id',
    totalProperty: 'total'
};

var readerJsonCommon = {
    type: 'json',
    root: 'data',
    id: 'id',
    totalProperty: 'total'
};

var readerJsonInvoiceNumber = {
    type: 'json',
    root: 'invoice_number',
    id: 'id',
    totalProperty: 'total'
};

var objectField = [{name: 'id', type: 'int'},
                   {name: 'subjectName', type: 'string'},
                   {name: 'invoiceType', type: 'int'},
                   {name: 'invoiceTypeText', type: 'string'},
                   {name: 'invoiceNumber', type: 'string'},
                   {name: 'paymentStatus', type: 'string'},
                   {name: 'deliveryStatus', type: 'string'},
                   {name: 'amount', type: 'int'},
                   {name: 'description', type: 'string'},
                   {name: 'createInvoiceDate', type: 'string'}];

MyUtil.Object.defineModel('Invoice', objectField);

var storeLoadInvoice = new Ext.data.JsonStore({
    model: 'Invoice',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Invoice_Load"),
        reader: readerJson
    }),
    pageSize: pageSizeDefault,
    autoLoad: ({params:{limit: limitDefault, page: pageDefault, start: startDefault}}, false)
});

//GridField
var objectGridField = [{name: 'id', type: 'int'},
    {name: 'invoiceId', type: 'int'},
    {name: 'productId', type: 'int'},
    {name: 'price', type: 'string'},
    {name: 'unit', type: 'int'},
    {name: 'quantity', type: 'int'},
    {name: 'amount', type: 'int'}];

var objectProductUnitField = [
    {name: 'id', type: 'int'},
    {name: 'salePrice', type: 'string'},
    {name: 'originalPrice', type: 'string'},
    {name: 'unitId1', type: 'int'},
    {name: 'unitId2', type: 'int'},
    {name: 'inputQuantity', type: 'int'},
    {name: 'outputQuantity', type: 'int'},
    {name: 'convertAmount', type: 'int'}];

//FormField
var objectFormField = [{name: 'id', type: 'int'},
    {name: 'invoiceNumber', type: 'string'},
    {name: 'createInvoiceDate', type: 'string'},
    {name: 'subject', type: 'int'},
    {name: 'address', type: 'string'},
    {name: 'deliveryReceiverMan', type: 'string'},
    {name: 'createInvoiceMan', type: 'string'},
    {name: 'phoneNumber', type: 'string'},
    {name: 'invoiceType', type: 'string'},
    {name: 'description', type: 'string'},
    {name: 'liab_arr', type: 'string'},
    {name: 'paymentStatus', type: 'int'}
];

//FormField
var objectListOutput = [{name: 'id', type: 'int'},
                        {name: 'invoiceNumber', type: 'string'},
                        {name: 'createInvoiceDate', type: 'string'},
                        {name: 'address', type: 'string'},
                        {name: 'phoneNumber', type: 'string'},
                        {name: 'invoiceType', type: 'int'},
                        {name: 'totalAmount', type: 'string'},
                        {name: 'description', type: 'string'},
                        {name: 'customerCode', type: 'string'},
                        {name: 'customerName', type: 'string'},
                        {name: 'liab_arr', type: 'string'},
                        {name: 'invoiceId'}];

MyUtil.Object.defineModel('Input2', objectFormField);
MyUtil.Object.defineModel('List_Output', objectListOutput);

//Distributor
var objectDistributorField = [{name: 'id', type: 'int'},
    {name: 'name', type: 'string'},
    {name: 'code', type: 'string'},
    {name: 'address', type: 'string'},
    {name: 'phoneNumber', type: 'string'}];

//Product
var objectProductField = [{name: 'id', type: 'int'},
    {name: 'name', type: 'string'},
    {name: 'code', type: 'string'},
    {name: 'salePrice', type: 'string'},
    {name: 'productUnitId', type: 'int'}
];

var objectUnitInvoiceDetailField = [{name: 'id', type: 'int'},  {name: 'name', type: 'string'}];

var objectInvoiceNumber = [{name: 'input', type: 'string'}, {name: 'output', type: 'string'}];

MyUtil.Object.defineModel('Input', objectGridField);
MyUtil.Object.defineModel('DistributorCmb', objectDistributorField);
MyUtil.Object.defineModel('CustomerCmb', objectDistributorField);
MyUtil.Object.defineModel('ProductCmb', objectProductField);
MyUtil.Object.defineModel('InvoiceNumber', objectInvoiceNumber);
MyUtil.Object.defineModel('Unit', objectUnitInvoiceDetailField);
MyUtil.Object.defineModel('ProductUnit', objectProductUnitField);

var storeLoadInput = new Ext.data.JsonStore({
    model: 'Input',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Input_Load"),
        reader: readerGridJson
    }),
    pageSize: pageSizeDefault,
    autoLoad: ({params:{limit: limitDefault, page: pageDefault, start: startDefault}}, false)
});

//Load default to print list invoice output
var storeListOutput = new Ext.data.JsonStore({
    model: 'List_Output',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("List_Output_Load"),
        reader: readerJsonCommon
    }),
    autoLoad: true
});

var storeLoadInvoiceNumber2 = new Ext.data.JsonStore({
    model: 'InvoiceNumber',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Input_Load"),
        reader: readerJsonInvoiceNumber
    }),
    autoLoad: false
});

var storeLoadProductCmb = new Ext.data.JsonStore({
    model: 'ProductCmb',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Product_LoadAll"),
        reader: readerJsonCommon
    }),
    autoLoad: true
});

var storeLoadUnitInvoiceDetail = new Ext.data.JsonStore({
    model: 'Unit',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Unit_LoadAll"),
        reader: readerJson
    }), autoLoad: false
});

var storeLoadUnitByProduct = new Ext.data.JsonStore({
    model: 'ProductUnit',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Product_LoadUnitByProductId"),
        reader: readerJson
    }), autoLoad: false
});

var storeLoadDistributorCmb = new Ext.data.JsonStore({
    model: 'DistributorCmb',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Distributor_LoadAll"),
        reader: readerJsonCommon
    }),
    autoLoad: false
});

var storeLoadCustomerCmb = new Ext.data.JsonStore({
    model: 'CustomerCmb',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Customer_LoadAll"),
        reader: readerJsonCommon
    }),
    autoLoad: false
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

    id: 'invoices-list',

    init: function () {
        this.launcher = {
            text: 'invoices management'.Translator('Module'),
            iconCls: 'icon-grid'
        };
    },

    createWindow: function () {
        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('grid-win');

        storeLoadUnitInvoiceDetail.load();
        storeLoadDistributorCmb.load();
        storeLoadCustomerCmb.load();

        var formFieldsList = {
            xtype: 'form',
            labelWidth: 150,
            style: 'margin: 5px 5px 0 5px;',
            defaultType: 'textfield',
            collapsible: true,
            defaults: {
                anchor: '100%'
            },
            layout: 'anchor',
            items: [{
                xtype: 'radiogroup',
                style: 'margin-left: 5px;',
                anchor: '60%',
                fieldLabel: 'invoice type'.Translator('Invoice'),
                columns: 3,
                name: 'invoiceTypeRadio',
                id: 'invoiceTypeRadio',
                vertical: true,
                items: [{
                            boxLabel: 'invoice input'.Translator('Invoice'),
                            name: 'rb', inputValue: '1'
                        }, {
                            boxLabel: 'invoice output'.Translator('Invoice'),
                            name: 'rb',
                            checked: true,
                            inputValue: '2'
                        }],
                listeners: {
                    change: function (field, newValue, oldValue) {
                        var printBtn = Ext.getCmp('print_btn');
                        if (newValue['rb'] == 2) {
                            printBtn.setVisible(true);
                        } else {
                            printBtn.setVisible(false);
                        }
                        storeLoadInvoice.reload();
                    }
                }
            }, {
                xtype: 'container',
                style: 'margin: 0 0 5px 5px;',
                anchor: '100%',
                layout: 'hbox',
                vertical: true,
                items: [{
                        fieldLabel: 'from date'.Translator('Invoice'),
                        xtype: 'datefield',
                        padding: '0 5px 0 0;',
                        format: date_format,
                        altFormats: date_format,
                        name: 'fromDate',
                        id: 'fromDate',
                        value: new Date(),
                        listeners: {
                            specialkey: function (s, e) {
                                if (e.getKey() == Ext.EventObject.ENTER) {
                                    storeLoadInvoice.reload();
                                }
                            }
                        }
                    }, {
                        labelWidth: 0,
                        fieldLabel: '~',
                        padding: '0 10px 0 0;',
                        labelSeparator: '',
                        name: 'toDate',
                        id: 'toDate',
                        xtype: 'datefield',
                        format: date_format,
                        altFormats: date_format,
                        value: new Date(),
                        listeners: {
                            specialkey: function (s, e) {
                                if (e.getKey() == Ext.EventObject.ENTER) {
                                    storeLoadInvoice.reload();
                                }
                            }
                        }
                    }, {
                        emptyText: 'customer name'.Translator('Invoice'),
                        padding: '0 5px 0 10px;',
                        xtype: 'textfield',
                        name: 'customerNameForm',
                        id: 'customerNameForm',
                        anchor: '50%',
                        listeners: {
                            specialkey: function (s, e) {
                                if (e.getKey() == Ext.EventObject.ENTER) {
                                    storeLoadInvoice.reload();
                                }
                            }
                        }
                    },{
                        emptyText: 'invoice number'.Translator('Invoice'),
                        padding: '0 5px 0 10px;',
                        xtype: 'textfield',
                        name: 'invoiceNumberForm',
                        id: 'invoiceNumberForm',
                        anchor: '50%',
                        listeners: {
                            specialkey: function (s, e) {
                                if (e.getKey() == Ext.EventObject.ENTER) {
                                    storeLoadInvoice.reload();
                                }
                            }
                        }
                    }]
                }],
            buttons: [{
                xtype: 'button',
                text: 'find'.Translator('Invoice'),
                width: 50,
                handler: function () {
                    storeLoadInvoice.reload();
                }
            }, {
                xtype: 'button',
                text: 'create new invoice'.Translator('Invoice'),
                width: 100,
                handler: function () {
                    var invoiceType = Ext.getCmp('invoiceTypeRadio').getValue().rb;
                    if (invoiceType == 0) {
                        MyUtil.Message.MessageWarning("please choose invoice type".Translator('Invoice'));
                    } else {
                        createPopupInvoiceForm(null, invoiceType);
                    }
                }
            }, {
                xtype: 'button',
                id: 'print_btn',
                text: 'print'.Translator('Invoice'),
                width: 50,
                handler: function () {
                    var grid2 = Ext.getCmp('grid-invoice-list').getSelectionModel().getSelection();

                    var arrId = [];
                    Ext.each(grid2, function (record) {
                        arrId.push(record.data.id);
                    });

                    MyUx.grid.Printer.printAutomatically = false;
                    MyUx.grid.Printer.printExtList(storeListOutput.data.items, arrId);
                }
            }]
        };

        storeLoadInvoice.on('beforeload', function() {
            var invoiceType       = Ext.getCmp('invoiceTypeRadio').getValue().rb;
            var fromDate          = Ext.util.Format.date(Ext.getCmp('fromDate').getValue(), 'Y-m-d');
            var toDate            = Ext.util.Format.date(Ext.getCmp('toDate').getValue(), 'Y-m-d');
            var customerNameForm  = Ext.getCmp('customerNameForm').getValue();
            var invoiceNumberForm = Ext.getCmp('invoiceNumberForm').getValue();

            this.proxy.extraParams = {
                invoiceType: invoiceType,
                fromDate: fromDate,
                toDate: toDate,
                customerName: customerNameForm,
                invoiceNumber: invoiceNumberForm
            };
        });

        var columnsInvoice = [
            new Ext.grid.RowNumberer(),
            {
                text: "customer name".Translator('Invoice'),
                flex: 1,
                style: 'text-align:center;',
                dataIndex: 'subjectName'
            }, {
                text: "date".Translator('Invoice'),
                dataIndex: 'createInvoiceDate',
                style: 'text-align:center;',
                align: 'center',
                width: 70
            }, {
                text: "invoice number".Translator('Invoice'),
                width: 100,
                style: 'text-align:center;',
                dataIndex: 'invoiceNumber'
            }, {
                text: "amount".Translator('Invoice'),
                style: 'text-align:center;',
                align: 'right',
                dataIndex: 'amount',
                width: 120,
                renderer:  Ext.util.Format.numberRenderer(moneyFormat)
            }, {
                text: "payment status".Translator('Invoice'),
                width: 100,
                style: 'text-align:center;',
                align: 'center',
                dataIndex: 'paymentStatus',
                renderer:function(value){
                    var paymentStatus;
                    switch (value) {
                        case '1':
                            paymentStatus = 'Chưa thanh toán';
                            break;
                        case '2':
                            paymentStatus = 'Đang thanh Toán';
                            break;
                        case '3':
                            paymentStatus = 'Đã thanh toán';
                            break;
                        default:
                            paymentStatus = '';
                    }

                    return paymentStatus;
                }
            }, {
                text: "delivery status".Translator('Invoice'),
                width: 100,
                style: 'text-align:center;',
                align: 'center',
                dataIndex: 'deliveryStatus',
                renderer:function(value){
                    var deliveryStatus;
                    switch (value) {
                        case '1':
                            deliveryStatus = 'Chưa giao hàng';
                            break;
                        case '2':
                            deliveryStatus = 'Đã giao hàng';
                            break;
                        default:
                            deliveryStatus = '';
                    }

                    return deliveryStatus;
                }
            }, {
                text: '',
                width: 80,
                renderer: function (val, meta, rec) {
                    var id = Ext.id();
                    Ext.defer(function () {
                        Ext.widget('button', {
                            renderTo: id,
                            text: 'view detail'.Translator('Invoice'),
                            scale: 'small',
                            handler: function () {
                                var invoiceId = rec.data.id;
                                var invoiceType2 = rec.data.invoiceType;
                                createPopupInvoiceForm(invoiceId, invoiceType2);
                            }
                        });
                    }, 50);

                    return Ext.String.format('<div id="{0}"></div>', id);
                }
            }
        ];

        var rowModel = Ext.create('Ext.selection.RowModel', {mode: "MULTI"});

        if (!win) {
            win = desktop.createWindow({
                id: 'invoice-list',
                title: 'invoices management'.Translator('Module'),
                width: 800,
                autoHeight: true,
                iconCls: 'icon-grid',
                animCollapse: false,
                constrainHeader: true,
                items: [formFieldsList,
                    {
                        border: true,
                        id: 'grid-invoice-list',
                        xtype: 'grid',
                        style: 'padding: 5px;',
                        columnLines: true,
                        height: 400,
                        store: storeLoadInvoice,
                        selModel: rowModel,
                        columns: columnsInvoice,
                        bbar: new Ext.PagingToolbar({
                            store: storeLoadInvoice,
                            displayInfo: true
                        }),
                        listeners: {
                            beforerender: function () {
                                this.store.load();
                            }
                        }
                    }
                ]
            });
        }

        return win;
    }
});

function createPopupInvoiceForm(invoiceId, invoiceType) {
    var invoiceTitle = "";
    var subjectT = "";
    var deliveryReceiver = "";
    var addressSubject = "";
    var phoneSubject = "";
    var hiddenPrintButtom = true;
    var storeObject = {};

    if (invoiceType == 1) {
        invoiceTitle = "invoice input".Translator('Invoice');
        subjectT = 'distributor'.Translator('Invoice');
        deliveryReceiver = 'delivery man'.Translator('Invoice');
        addressSubject = 'distributor address'.Translator('Invoice');
        phoneSubject = 'distributor phone'.Translator('Invoice');
        hiddenPrintButtom = true;
        storeObject = storeLoadDistributorCmb;
    } else {
        invoiceTitle = "invoice output".Translator('Invoice');
        subjectT = 'customer'.Translator('Invoice');
        deliveryReceiver = 'receiver man'.Translator('Invoice');
        addressSubject = 'customer address'.Translator('Invoice');
        phoneSubject = 'customer phone'.Translator('Invoice');
        storeObject = storeLoadCustomerCmb;
        hiddenPrintButtom = false;
    }

    var storeLoadInputForm = new Ext.data.JsonStore({
        model: 'Input2',
        proxy: new Ext.data.HttpProxy({
            url: MyUtil.Path.getPathAction("Input_Load"),
            reader: readerJsonForm
        }),
        autoLoad: false
    });

    storeLoadInvoiceNumber2.load({
        params: {limit: limitDefault, page: pageDefault, start: startDefault, id: invoiceId},
        callback: function (records, options, success) {
            if (storeLoadInvoiceNumber2.data.items[0]) {
                formData = storeLoadInvoiceNumber2.data.items[0].data;

                if (invoiceType == 1) {
                    Ext.getCmp('invoice_number').setValue(formData.input);
                } else {
                    Ext.getCmp('invoice_number').setValue(formData.output);
                }
            }
        }
    });

    //Default value
    var formData = {
        'id': '',
        //'invoiceNumber' : '',
        'createInvoiceDate': '',
        'subject': 1,
        'createInvoiceMan': '',
        'phoneNumber': '',
        'description': '',
        'invoiceType': '',
        'paymentStatus': ''
    };

    var liab_obj = {amount: 0, note: ''};

    storeLoadInputForm.load({
        params: {limit: limitDefault, page: pageDefault, start: startDefault, id: invoiceId},
        callback: function (records, options, success) {
            if (storeLoadInputForm.data.items[0]) {
                formData = storeLoadInputForm.data.items[0].data;
                liab_obj.amount = formData.liab_amount;
                liab_obj.note = formData.liab_note;

                Ext.getCmp('invoice_number').setValue(formData.invoiceNumber);
                Ext.getCmp('create_invoice_date').setValue(formData.createInvoiceDate);
                Ext.getCmp('subject').setValue(formData.subject);
                Ext.getCmp('delivery_receiver_man').setValue(formData.deliveryReceiverMan);
                Ext.getCmp('create_invoice_man').setValue(formData.createInvoiceMan);
                Ext.getCmp('address').setValue(formData.address);
                Ext.getCmp('description').setValue(formData.description);
                Ext.getCmp('phone_number').setValue(formData.phoneNumber);
            }
        }
    });

    var jsonUserLoginData = Ext.get('UserLoginJson').getAttribute('data'),
        userLoginData     = Ext.JSON.decode(jsonUserLoginData);

    var formFieldsAll = {
        xtype: 'form',
        border: false,
        id: 'formFieldsAll',
        name: 'formFieldsAll',
        style: 'margin: 5px 5px 0 5px;',
        items: [{
            xtype: 'container',
            anchor: '100%',
            layout: 'hbox',
            items: [{
                xtype: 'container',
                flex: 1,
                layout: 'anchor',
                items: [{
                    xtype: 'hidden',
                    name: 'invoiceId',
                    id: 'invoiceId',
                    value: invoiceId
                }, {
                    xtype: 'hidden',
                    name: 'invoiceTypeHidden',
                    id: 'invoiceTypeHidden',
                    value: invoiceType
                }, {
                    fieldLabel: 'invoice number'.Translator('Invoice'),
                    labelWidth: 150,
                    xtype: 'textfield',
                    name: 'invoice_number',
                    id: 'invoice_number'
                }, {
                    fieldLabel: 'create invoice date'.Translator('Invoice'),
                    labelWidth: 150,
                    name: 'create_invoice_date',
                    id: 'create_invoice_date',
                    xtype: 'datefield',
                    format: date_format,
                    altFormats: date_format,
                    value: new Date()
                }, {
                    xtype: 'combobox',
                    labelWidth: 150,
                    listConfig: {minWidth: 180},
                    fieldLabel: subjectT,
                    name: 'subject',
                    id: 'subject',
                    valueField: 'id',
                    typeAhead: true,
                    triggerAction: 'all',
                    selectOnTab: true,
                    store: storeObject,
                    displayField: 'name',
                    lazyRender: true,
                    queryMode: 'local',
                    listeners: {
                        select: function (combo, record, index) {
                            Ext.getCmp('address').setValue(record[0].data.address);
                            Ext.getCmp('phone_number').setValue(record[0].data.phoneNumber);
                        }
                    }
                }, {
                    fieldLabel: deliveryReceiver,
                    labelWidth: 150,
                    xtype: 'textfield',
                    name: 'delivery_receiver_man',
                    id: 'delivery_receiver_man'
                }]
            }, {
                xtype: 'container',
                flex: 1,
                layout: 'anchor',
                items: [{
                    fieldLabel: 'create invoice man'.Translator('Invoice'),
                    labelWidth: 150,
                    xtype: 'textfield',
                    name: 'create_invoice_man',
                    id: 'create_invoice_man',
                    value: userLoginData.name
                }, {
                    fieldLabel: addressSubject,
                    labelWidth: 150,
                    xtype: 'textfield',
                    name: 'address',
                    id: 'address'
                }, {
                    fieldLabel: phoneSubject,
                    labelWidth: 150,
                    xtype: 'textfield',
                    name: 'phone_number',
                    id: 'phone_number'
                }, {
                    fieldLabel: 'description'.Translator('Invoice'),
                    labelWidth: 150,
                    xtype: 'textfield',
                    name: 'description',
                    id: 'description'
                }]
            }]
        }],
        buttons: [{
            xtype: 'button',
            text: 'add product'.Translator('Invoice'),
            width: 100,
            handler: function () {
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
        }, {
            xtype: 'button',
            text: 'remove product'.Translator('Invoice'),
            width: 100,
            listeners: {
                click: function () {
                    var selection = Ext.getCmp('grid-input-output').getView().getSelectionModel().getSelection()[0];
                    if (selection) {
                        storeLoadInput.remove(selection);
                    }
                }
            }
        }]
    };

    var columnsInvoicePopup = [
        {
            xtype: 'rownumberer',
            text: 'order'.Translator('Invoice'),
            style: 'text-align:center;',
            width: 30
        }, {
            header: 'product name'.Translator('Product'),
            flex: 1,
            dataIndex: 'productId',
            style: 'text-align:center;',
            editor: {
                xtype: 'combobox',
                store: storeLoadProductCmb,
                listConfig: {minWidth: 220},
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    change: function (field, newValue, o, e) {
                        var grid = this.up().up();
                        var selModel = grid.getSelectionModel();

                        if (storeLoadInput.data.items[0].data.id == 0 && selModel.getSelection()[0].data.id == 0) {
                            var price = 0;

                            if (newValue != 0 && newValue != "") {
                                var index = Ext.StoreManager.lookup(storeLoadProductCmb).findExact('id', newValue);
                                var rec   = Ext.StoreManager.lookup(storeLoadProductCmb).getAt(index);

                                storeLoadUnitByProduct.load({
                                    params:{productId: rec.data.id},
                                    scope: this,
                                    callback: function(records, operation, success) {
                                        if (success) {
                                            if(records.length > 0) {
                                                var unitByProduct  = records[0].data,
                                                    inputQuantity  = unitByProduct.inputQuantity,
                                                    outputQuantity = unitByProduct.outputQuantity;

                                                var inOutRadio = Ext.ComponentQuery.query('[name=invoiceTypeRadio]')[0].getValue().rb;
                                                if (inOutRadio == 1) {
                                                    price = unitByProduct.originalPrice;
                                                } else if(inOutRadio == 2) {
                                                    var defQuantity = inputQuantity - outputQuantity;
                                                    if(defQuantity <= 0) {
                                                        MyUtil.Message.MessageWarning('end product'.Translator('Invoice'));
                                                        return false;
                                                    }
                                                    else if(5 >= defQuantity) {
                                                        MyUtil.Message.MessageWarning(
                                                          'still <= 5 product'.Translator('Invoice')
                                                          + '- [ ' + defQuantity + ' ]');
                                                    }

                                                    price = unitByProduct.salePrice;
                                                    selModel.getSelection()[0].set('unit', unitByProduct.unitId1);
                                                }
                                            }
                                        } else console.log('error');
                                        selModel.getSelection()[0].set('price', parseFloat(price));
                                    }
                                });
                            }
                        }
                    }
                }
            },
            renderer: function (value) {
                if (value != 0 && value != "") {
                    var index = Ext.StoreManager.lookup(storeLoadProductCmb).findExact('id', value);
                    var rec = Ext.StoreManager.lookup(storeLoadProductCmb).getAt(index);

                    if (rec)
                        return rec.data.name;
                    else
                        return value;
                } else return "";
            }
        }, {
            text: "unit".Translator('Invoice'),
            width: 80,
            dataIndex: 'unit',
            style: 'text-align:center;',
            editor: {
                allowBlank: true,
                xtype: 'combobox',
                store: storeLoadUnitInvoiceDetail,
                queryMode: 'local',
                displayField: 'name',
                valueField: 'id',
                listeners: {
                    change: function (field, newValue, o, e) {
                        var amount = 0, price = 0;

                        if (newValue != 0 && newValue != "") {
                            var grid         = this.up().up(),
                                selModel     = grid.getSelectionModel(),
                                selectedData = selModel.getSelection()[0].getData();

                            storeLoadUnitByProduct.load({
                                params:{productId: selectedData.productId},
                                scope: this,
                                callback: function(records, operation, success) {
                                    if (success) {
                                        if(records.length > 0) {
                                            var unitByProduct = records[0].data,
                                                unitId1       = unitByProduct.unitId1,
                                                unitId2       = unitByProduct.unitId2;
                                            var inOutRadio = Ext.ComponentQuery.query('[name=invoiceTypeRadio]')[0].getValue().rb;

                                            if (inOutRadio == 1) {

                                            } else if(inOutRadio == 2) {
                                                var quantity = selectedData.quantity;
                                                price  = parseFloat(unitByProduct.salePrice);

                                                if(unitId1 == newValue ) {
                                                    amount = price * quantity;
                                                } else if(unitId2 == newValue) {
                                                    price = Math.ceil(price / unitByProduct.convertAmount);
                                                    amount = price * quantity;
                                                    selModel.getSelection()[0].set('price', price);
                                                } else amount = 0; price = 0;
                                            }
                                            selModel.getSelection()[0].set('price', price);
                                        }
                                    } else console.log('error');

                                    selModel.getSelection()[0].set('amount', amount);
                                }
                            });
                        }
                    }
                }
            },
            renderer: function (value) {
                if (value != 0 && value != "") {
                    var index = Ext.StoreManager.lookup(storeLoadUnitInvoiceDetail).findExact('id', value);
                    var rec = Ext.StoreManager.lookup(storeLoadUnitInvoiceDetail).getAt(index);

                    if (rec)
                        return rec.data.name;
                    else
                        return value;
                } else return "";
            }
        }, {
            text: "quantity".Translator('Invoice'),
            width: 80,
            style: 'text-align:center;',
            align: 'right',
            dataIndex: 'quantity',
            renderer: function(value){
                return Ext.util.Format.currency(value, ' ', decimalPrecision)
            },
            editor: {
                allowBlank: true,
                listeners: {
                    change: function (field, newValue, o, e) {
                        var amount = 0;

                        if (newValue != 0 && newValue != "") {
                            var grid         = this.up().up(),
                                selModel     = grid.getSelectionModel(),
                                selectedData = selModel.getSelection()[0].getData();

                            storeLoadUnitByProduct.load({
                                params:{productId: selectedData.productId},
                                scope: this,
                                callback: function(records, operation, success) {
                                    if (success) {
                                        if(records.length > 0) {
                                            var unitByProduct = records[0].data,
                                                unitId1       = unitByProduct.unitId1,
                                                unitId2       = unitByProduct.unitId2;
                                            var inOutRadio = Ext.ComponentQuery.query('[name=invoiceTypeRadio]')[0].getValue().rb;

                                            if (inOutRadio == 1) {
                                                amount = (newValue * parseFloat(selectedData.price));
                                            } else if(inOutRadio == 2) {
                                                if(unitId1 == selectedData.unit || unitId2 == selectedData.unit) {
                                                    amount = (newValue * parseFloat(selectedData.price));
                                                } else amount = 0;
                                            }
                                        }
                                    } else console.log('error');
                                    selModel.getSelection()[0].set('amount', amount);
                                }
                            });
                        }
                    }
                }
            }
        }, {
            text: "price".Translator('Invoice'),
            width: 150,
            style: 'text-align:center;',
            align: 'right',
            dataIndex: 'price',
            summaryType: 'sum',
            summaryRenderer: function(records){
                return 'total'.Translator('Invoice');
            },
            renderer: function(value){
                return Ext.util.Format.currency(value, ' ', decimalPrecision)
            },
            editor: {
                allowBlank: true,
                listeners: {
                    change: function (field, newValue) {
                        var models = Ext.getCmp('grid-input-output').getStore().getRange();

                        var grid = this.up().up();
                        var selModel = grid.getSelectionModel();
                        var row = grid.store.indexOf(selModel.getSelection()[0]);

                        var amountComp = (parseInt(models[row].data.quantity) * parseFloat(newValue));
                        if (isNaN(amountComp)) {
                            amountComp = 0;
                        }
                        selModel.getSelection()[0].set('amount', parseFloat(amountComp));
                    }
                }
            }
        }, {
            text: "amount".Translator('Product'),
            width: 200,
            style: 'text-align:center;',
            align: 'right',
            dataIndex: 'amount',
            summaryType: function(records){
                var totals = 0;

                var length = records.length;
                for (var i = 0; i < length; i++) {
                    totals += records[i].data.amount;
                }

                return totals;
            },
            renderer: function(value){
                return Ext.util.Format.currency(value, ' ', decimalPrecision)
            },
            editor: {
                allowBlank: true
            }
        }
    ];

    var setting = new Ext.FormPanel({
        border: false,
        items: [{
            layout: 'form',
            border: false,
            items: [formFieldsAll
                , {
                    id: 'grid-input-output',
                    name: 'grid-input-output',
                    xtype: 'grid',
                    height: 280,
                    style: 'padding: 5px;',
                    store: storeLoadInput,
                    selModel: Ext.create('Ext.selection.RowModel', {mode: "MULTI"}),
                    columns: columnsInvoicePopup,
                    columnLines: true,
                    plugins: cellEditing,
                    features: [{ftype: 'summary'}],
                    listeners: {
                        beforerender: function () {
                            this.store.load({params: {limit: limitDefault, page: pageDefault, start: startDefault, id: invoiceId}});
                        }
                    }, bbar: new Ext.PagingToolbar({
                        store: storeLoadInput,
                        displayInfo: true
                    })
                }]
        }],
        buttons: [{
            xtype: 'button',
            text: 'add'.Translator('Invoice'),
            width: 30,
            handler: function () {
//                Ext.getCmp('invoice_number').setValue('');
                Ext.getCmp('create_invoice_date').setValue(new Date());
                Ext.getCmp('subject').setValue('');
                Ext.getCmp('delivery_receiver_man').setValue('');
                Ext.getCmp('create_invoice_man').setValue(userLoginData.name);
                Ext.getCmp('address').setValue('');
                Ext.getCmp('description').setValue('');
                Ext.getCmp('phone_number').setValue('');
            }
        }, {
            xtype: 'button',
            text: 'save'.Translator('Invoice'),
            width: 30,
            handler: function () {
                //Get value form
                var id = Ext.getCmp('invoiceId').getValue();
                var invoice_number = Ext.getCmp('invoice_number').getValue();
                var create_invoice_date = Ext.util.Format.date(Ext.getCmp('create_invoice_date').getValue(), 'Y-m-d');
                var subject = Ext.getCmp('subject').getValue();
                var delivery_receiver_man = Ext.getCmp('delivery_receiver_man').getValue();
                var create_invoice_man = Ext.getCmp('create_invoice_man').getValue();
                var address = Ext.getCmp('address').getValue();
                var description = Ext.getCmp('description').getValue();
                var phone_number = Ext.getCmp('phone_number').getValue();
                var invoice_type = Ext.getCmp('invoiceTypeHidden').getValue();

                var form_fields_value = [{
                    'id': id,
                    'invoiceType': invoice_type,
                    'invoiceNumber': invoice_number,
                    'createInvoiceDate': create_invoice_date,
                    'subject': subject,
                    'deliveryReceiverMan': delivery_receiver_man,
                    'createInvoiceMan': create_invoice_man,
                    'address': address,
                    'description': description,
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
                    MyUtil.Message.MessageInfo("please input invoice number".Translator('Invoice'));
                } else {
                    Ext.Ajax.request({
                        url: MyUtil.Path.getPathAction("Input_Update"),
                        params: JSON.stringify(params),
                        method: 'POST',
                        headers: {
                            'content-type': 'application/json'
                        },
                        success: function (data) {
                            editWindow.close();
                            storeLoadInvoice.reload();
                        }
                    });
                }
            }
        }, {
            xtype: 'button',
            text: 'delete'.Translator('Invoice'),
            width: 30,
            handler: function () {
                Ext.MessageBox.confirm('Delete', 'Are you sure ?', function (btn) {
                    if (btn === 'yes') {

                        Ext.Ajax.request({
                            url: MyUtil.Path.getPathAction("Input_Delete"),
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            jsonData: {'params': Ext.getCmp('invoiceId').getValue()},
                            scope: this,
                            success: function (msg) {
                                if (msg.status) {
                                    editWindow.close();
                                    storeLoadInvoice.reload();
                                }
                            },
                            failure: function (msg) {
                                console.log('failure');
                            }
                        });
                    }
                });
            }
        }, {
            xtype: 'button',
            hidden: hiddenPrintButtom,
            text: 'print'.Translator('Invoice'),
            width: 30,
            handler: function () {
                /*var grid = Ext.ComponentQuery.query('[name=grid-input-output]')[0];

                var invoiceNum   = Ext.getCmp('invoice_number').getValue();
                var subject      = Ext.getCmp('subject').getValue();
                var address      = Ext.getCmp('address').getValue();
                var phone_number = Ext.getCmp('phone_number').getValue();
                var description  = Ext.getCmp('description').getValue();
                var customerName = storeLoadCustomerCmb.findRecord("id", subject).get('name');
                var dataForms = '<div style="text-align: center; font-weight: bolder; font-size: x-large;">'
                + 'PHIẾU XUẤT' +'</div><br/>';

                dataForms += '<table class="no-border" border="0px" style="width: 100%">'+
                                '<tr>' +
                                    '<td class="font-bold" width="80">' + 'customer name'.Translator('Invoice') + ':' + '</td>'+
                                    '<td colspan="3">' + 'Anh/Chị ' + customerName + '</td>'+

                                '</tr>' +

                                '<tr>' +
                                    '<td class="font-bold" width="80">' + 'invoice number'.Translator('Invoice') + ':' + '</td>'+
                                    '<td>' + invoiceNum + '</td>'+
                                    '<td class="font-bold" width="80">' + 'phone number'.Translator('Invoice') + ':' + '</td>'+
                                    '<td>' + phone_number + '</td>' +
                                '</tr>' +

                                '<tr>' +
                                    '<td class="font-bold"  width="80">' + 'address'.Translator('Invoice') + ':' + '</td>' +
                                    '<td colspan="3">' + address + '</td>' +
                                '</tr>' +
                            '</table>';*/

                MyUx.grid.Printer.printAutomatically = false;
                MyUx.grid.Printer.printExtList(storeListOutput.data.items, Ext.getCmp('invoiceId').getValue());
            }
        }, {
            xtype: 'button',
            text: 'close'.Translator('Common'),
            width: 30,
            handler: function () {
                editWindow.close();
            }
        }]
    });

    var editWindow = new Ext.Window({
        title: invoiceTitle,
        width: 800,
        height: 500,
        closable: true,
        closeAction: 'destroy',
        resizable: false,
        modal: true,
        autoHeight: true,
        draggable: true,
        items: [setting],
        listeners: {
            close: function (p) {
                formClosed = true;
            }
        }
    });

    editWindow.show();
}

