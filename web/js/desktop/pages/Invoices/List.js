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
                        {name: 'invoiceId'}
                    ];

MyUtil.Object.defineModel('Input2', objectFormField);
MyUtil.Object.defineModel('List_Output', objectListOutput);

//Distributor
var objectDistributorField = [{name: 'id', type: 'int'},
    {name: 'name', type: 'string'},
    {name: 'code', type: 'string'},
    {name: 'address', type: 'string'},
    {name: 'phoneNumber', type: 'string'}
];

//Product
var objectProductField = [{name: 'id', type: 'int'},
    {name: 'name', type: 'string'},
    {name: 'code', type: 'string'},
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

var storeLoadInput = new Ext.data.JsonStore({
    model: 'Input',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Input_Load"),
        reader: readerGridJson
    }),
    pageSize: pageSizeDefault,
    autoLoad: ({params:{limit: limitDefault, page: pageDefault, start: startDefault}}, false)
});

var storeListOutput = new Ext.data.JsonStore({
    model: 'List_Output',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("List_Output_Load"),
        reader: readerJsonCommon
    }),
    autoLoad: false
});

var storeLoadUnitInvoiceDetail = new Ext.data.JsonStore({
    model: 'Unit',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Unit_Load"),
        reader: readerJson
    }), autoLoad: true
});

var storeLoadDistributorCmb = new Ext.data.JsonStore({
    model: 'DistributorCmb',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Distributor_Load"),
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
                items: [{boxLabel: 'all'.Translator('Invoice'), name: 'rb', inputValue: '0', checked: true},
                        {boxLabel: 'invoice input'.Translator('Invoice'), name: 'rb', inputValue: '1'},
                        {boxLabel: 'invoice output'.Translator('Invoice'), name: 'rb', inputValue: '2'}],
                listeners: {
                    change: function (field, newValue, oldValue) {
                        var printBtn = Ext.getCmp('print_btn');
                        if (newValue['rb'] == 2) {
                            printBtn.setVisible(true);
                        } else {
                            printBtn.setVisible(false);
                        }
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
                        anchor: '50%'
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
                        anchor: '50%'
                    }, {
                        emptyText: 'customer name'.Translator('Invoice'),
                        padding: '0 5px 0 10px;',
                        xtype: 'textfield',
                        name: 'customerNameForm',
                        id: 'customerNameForm',
                        anchor: '50%'
                    },{
                        emptyText: 'invoice number'.Translator('Invoice'),
                        padding: '0 5px 0 10px;',
                        xtype: 'textfield',
                        name: 'invoiceNumberForm',
                        id: 'invoiceNumberForm',
                        anchor: '50%'
                    }]
                }],

            buttons: [{
                xtype: 'button',
                text: 'find'.Translator('Invoice'),
                width: 50,
                handler: function () {
                    var invoiceType = Ext.getCmp('invoiceTypeRadio').getValue().rb;
                    var fromDate = Ext.util.Format.date(Ext.getCmp('fromDate').getValue(), 'Y-m-d');
                    var toDate = Ext.util.Format.date(Ext.getCmp('toDate').getValue(), 'Y-m-d');
                    var customerNameForm = Ext.getCmp('customerNameForm').getValue();
                    var invoiceNumberForm = Ext.getCmp('invoiceNumberForm').getValue();

                    storeLoadInvoice.reload({
                        params: {
                            limit: limitDefault,
                            page: pageDefault,
                            start: startDefault,
                            invoiceType: invoiceType,
                            fromDate: fromDate,
                            toDate: toDate,
                            customerName: customerNameForm,
                            invoiceNumber: invoiceNumberForm,
                        }
                    });
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
                hidden: true,
                id: 'print_btn',
                text: 'print'.Translator('Invoice'),
                width: 50,
                handler: function () {
                    MyUx.grid.Printer.printAutomatically = false;
                    MyUx.grid.Printer.printExtList(storeListOutput.data.items);
                }
            }]
        };

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
                width: 80
            }, {
                text: "amount".Translator('Invoice'),
                flex: 1,
                style: 'text-align:center;',
                align: 'right',
                dataIndex: 'amount',
                renderer: function (value) {
                    if (value) {
                        return value;
                    } else return 0;
                }
            }, {
                text: "invoice type".Translator('Invoice'),
                width: 80,
                style: 'text-align:center;',
                dataIndex: 'invoiceTypeText'
            }, {
                text: "invoice number".Translator('Invoice'),
                width: 100,
                style: 'text-align:center;',
                dataIndex: 'invoiceNumber'
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
            }, {
                text: "state".Translator('Invoice'),
                width: 100,
                style: 'text-align:center;',
                dataIndex: 'paymentStatus'
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
//                    'invoiceNumber' : '',
        'createInvoiceDate': '',
        'subject': 1,
        'createInvoiceMan': '',
        'phoneNumber': '',
        'description': '',
        'invoiceType': '',
        'paymentStatus': ''
    };

    storeLoadInputForm.load({
        params: {limit: limitDefault, page: pageDefault, start: startDefault, id: invoiceId},
        callback: function (records, options, success) {
            if (storeLoadInputForm.data.items[0]) {
                formData = storeLoadInputForm.data.items[0].data;

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
                    altFormats: date_format
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
                    id: 'create_invoice_man'
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
            }
            ]
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
            width: 30
        }, {
            header: 'product name'.Translator('Product'),
            dataIndex: 'productId',
            editor: {
                xtype: 'combobox',
                store: storeLoadProductCmb,
                displayField: 'name',
                valueField: 'id'
            },
            renderer: function (value) {
                if (value != 0 && value != "") {
                    if (storeLoadProductCmb.findRecord("id", value) != null)
                        return storeLoadProductCmb.findRecord("id", value).get('name');
                    else
                        return value;
                } else return "";
            }
        }, {
            header: 'product code'.Translator('Product'),
            dataIndex: 'productId',
            editor: {
                xtype: 'combobox',
                store: storeLoadProductCmb,
                displayField: 'code',
                valueField: 'id'
            },
            renderer: function (value) {
                if (value != 0 && value != "") {
                    if (storeLoadProductCmb.findRecord("id", value) != null)
                        return storeLoadProductCmb.findRecord("id", value).get('code');
                    else
                        return value;
                } else return "";
            }
        }, {
            text: "unit".Translator('Product'),
            flex: 2,
            dataIndex: 'unit',
            editor: {
                allowBlank: true,
                xtype: 'combobox',
                store: storeLoadUnitInvoiceDetail,
                displayField: 'name',
                valueField: 'id'
            },
            renderer: function (value) {
                if (value != 0 && value != "") {
                    if (storeLoadUnitInvoiceDetail.findRecord("id", value) != null)
                        return storeLoadUnitInvoiceDetail.findRecord("id", value).get('name');
                    else
                        return value;
                } else return "";
            }
        }, {
            text: "quantity".Translator('Product'),
            flex: 1,
            dataIndex: 'quantity',
            editor: {
                allowBlank: true,
                listeners: {
                    change: function (field, newValue, o, e) {
                        var models = Ext.getCmp('grid-input-output').getStore().getRange();

                        var grid = this.up().up();
                        var selModel = grid.getSelectionModel();
                        var row = grid.store.indexOf(selModel.getSelection()[0]);

                        var amountComp = (newValue * parseFloat(models[row].data.price));
                        if (isNaN(amountComp)) {
                            amountComp = 0;
                        }
                        selModel.getSelection()[0].set('amount', parseFloat(amountComp));
                    }
                }
            }
        }, {
            text: "price".Translator('Product'),
            flex: 1,
            dataIndex: 'price',
            summaryType: function(records){
                return 'total'.Translator('Invoice');
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
            width: 150,
            dataIndex: 'amount',
            summaryType: function(records){
                var totals = 0;

                var length = records.length;
                for (var i = 0; i < length; i++) {
                    totals += records[i].data.amount;
                }

                return totals;
            },
            renderer: function (value) {
                return value;
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
                    xtype: 'grid',
                    height: 280,
                    style: 'padding: 5px;',
                    store: storeLoadInput,
                    selModel: Ext.create('Ext.selection.RowModel', {mode: "MULTI"}),
                    columns: columnsInvoicePopup,
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
                Ext.getCmp('create_invoice_date').setValue('');
                Ext.getCmp('subject').setValue('');
                Ext.getCmp('delivery_receiver_man').setValue('');
                Ext.getCmp('create_invoice_man').setValue('');
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
                            storeLoadInput.reload();
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
                                    storeLoadInput.reload();
                                    console.log('success');
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
                var grid = Ext.getCmp('grid-input-output');
                MyUx.grid.Printer.printAutomatically = false;

                var invoiceNum = Ext.getCmp('invoice_number').getValue();
                var subject = Ext.getCmp('subject').getValue();
                var address = Ext.getCmp('address').getValue();
                var phone_number = Ext.getCmp('phone_number').getValue();
                var description = Ext.getCmp('description').getValue();

                var customerName = storeLoadCustomerCmb.findRecord("id", subject).get('name');
                var customerCode = storeLoadCustomerCmb.findRecord("id", subject).get('code');

                var dataForm = '<table class="no-border" border="0px" style="width: 70%">'+
                                    '<tr>'+
                                        '<td class="font-bold">'+
                                            'invoice number'.Translator('Invoice')+
                                        '</td>'+

                                        '<td>'+
                                            invoiceNum+
                                        '</td>'+

                                        '<td class="font-bold">'+
                                            'customer name'.Translator('Invoice')+
                                        '</td>'+

                                        '<td>'+
                                            customerName+
                                        '</td>'+

                                        '<td class="font-bold">'+
                                            'phone number'.Translator('Invoice')+
                                        '</td>'+

                                        '<td>'+
                                            phone_number+
                                        '</td>'+
                                    '</tr>'+

                                    '<tr>'+
                                        '<td class="font-bold">'+
                                            'customer code'.Translator('Invoice')+
                                        '</td>'+

                                        '<td>'+
                                            customerCode+
                                        '</td>'+

                                        '<td class="font-bold">'+
                                            'address'.Translator('Invoice')+
                                        '</td>'+

                                        '<td>'+
                                            address+
                                        '</td>'+

                                        '<td class="font-bold">'+
                                            'description'.Translator('Invoice')+
                                        '</td>'+

                                        '<td>'+
                                            description+
                                        '</td>'+
                                    '</tr>'+

                                '</table>';

                MyUx.grid.Printer.printExt(grid, dataForm);
            }
        }]
    });

    var editWindow = new Ext.Window({
        title: invoiceTitle,
        width: 700,
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

