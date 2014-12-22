/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'data',
    id  : 'id',
    totalProperty: 'total'
};

var objectField = [{name: 'id',            type: 'int'},
                   {name: 'customerId',    type: 'int'},
                   {name: 'invoiceId',     type: 'int'},
                   {name: 'invoiceNumber', type: 'string'},
                   {name: 'name',          type: 'string'},
                   {name: 'amount',        type: 'int'},
                   {name: 'price',         type: 'float'}];

var objectFieldCustomer = [{name: 'id',   type: 'int'},
                           {name: 'name', type: 'string'}];

MyUtil.Object.defineModel('Liabilities', objectField);
MyUtil.Object.defineModel('LiabilitiesCustomer', objectFieldCustomer);
MyUtil.Object.defineModel('LiabilitiesName', [{name: 'name', type: 'string'}]);

var storeLoadLiabilities = new Ext.data.JsonStore({
    model: 'Liabilities',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Liabilities_Load"),
        reader: readerJson
    }),
    groupField: 'invoiceNumber',
    autoLoad: false
});

var storeLoadLiabilitiesCustomer = new Ext.data.JsonStore({
    model: 'LiabilitiesCustomer',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Liabilities_Customer_Load"),
        reader: readerJson
    }),
    pageSize: pageSizeDefault,
    autoLoad: ({params:{limit: limitDefault, page: pageDefault, start: startDefault}}, false)
});

var storeLiabilitiesName = new Ext.data.JsonStore({
    model: 'LiabilitiesName',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Liabilities_Name_Load"),
        reader: {
            type: 'json',
            root: 'data',
            name  : 'name'
        }
    }),
    autoLoad: false
});

Ext.define('SrcPageUrl.Liabilities.List', {
    extend: 'Ext.ux.desktop.Module',
    requires: [
        'Ext.grid.*',
        'Ext.data.*',
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.form.field.Number',
        'Ext.form.field.Date',
        'MyUx.grid.Printer'
    ],

    id:'liabilities-list',

    init : function(){
        this.launcher = {
            text: 'liabilities management'.Translator('Module'),
            iconCls:'icon-grid'
        };
    },

    createWindow : function (){
        var invoiceSelectId  = '',
            customerSelectId = '',
            rowModel         = Ext.create('Ext.selection.RowModel', {mode : "MULTI"}),

            columnsLiabilities = [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'id',
                hidden : true
            }, {
                text: "name".Translator('Common'),
                width: 200,
                dataIndex: 'name',
                style: 'text-align:center;',
                summaryType: 'count',
                summaryRenderer: function(value) {
                    return ((value === 0 || value > 1) ? '(' + 'total'.Translator('Common') + ': ' + value + ')' : '(' + 'total'.Translator('Common') + ': 1)');
                },
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: "amount".Translator('Common'),
                width: 150,
                dataIndex: 'amount',
                style: 'text-align:center;',
                align: 'right',
                summaryType: 'sum',
                renderer: function(value){
                    return value;
                },
                summaryRenderer: function(value) {
                    return value;
                },
                editor: {
                    xtype: 'numberfield',
                    decimalPrecision: 0
                }
            }, {
                text: "price".Translator('Common'),
                flex: 1,
                dataIndex: 'price',
                style: 'text-align:center;',
                align: 'right',
                summaryType: 'sum',
                renderer: function(value){
                    return Ext.util.Format.currency(value, ' ', decimalPrecision)
                },
                summaryRenderer: function(value) {
                    return Ext.util.Format.currency(value, 'VND ', decimalPrecision);
                },
                editor: {
                    xtype: 'numberfield',
                    decimalPrecision: decimalPrecision
                }
            }
        ];

        var columnsCustomerLiabilities = [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'id',
                hidden : true
            }, {
                text: "name".Translator('Common'),
                flex: 1,
                dataIndex: 'name'
            }
        ];

        var addFormLiabilities = Ext.widget({
            xtype: 'form',
            layout: 'form',
            frame: true,
            border: false,
            style: 'border: 0;',
            width: 350,
            fieldDefaults: {
                msgTarget: 'side',
                labelWidth: 60
            },
            items: [{
                fieldLabel: 'name'.Translator('Common'),
                xtype: 'combobox',
                store: storeLiabilitiesName,
                listConfig: {minWidth: 300},
                displayField: 'name',
                valueField: 'name',
                queryMode: 'local',
                name: 'liabilitiesName',
                id: 'liabilitiesName',
                allowBlank: false
            }, {
                fieldLabel: 'amount'.Translator('Common'),
                name: 'liabilitiesAmount',
                id: 'liabilitiesAmount',
                xtype: 'numberfield',
                decimalPrecision: 0,
                allowBlank: false
            }, {
                fieldLabel: 'price'.Translator('Common'),
                name: 'liabilitiesPrice',
                id: 'liabilitiesPrice',
                allowBlank: false,
                xtype: 'numberfield',
                decimalPrecision: decimalPrecision
            }],
            buttons: [{
                text: 'save'.Translator('Common'),
                handler: function() {
                    var isValid = this.up('form').getForm().isValid();
                    if (isValid) {
                        var arrInsert = {
                            invoiceId  : invoiceSelectId,
                            customerId : customerSelectId,
                            name       : Ext.getCmp('liabilitiesName').getValue(),
                            amount     : Ext.getCmp('liabilitiesAmount').value,
                            price      : Ext.getCmp('liabilitiesPrice').value
                        };

                        if (arrInsert) {
                            Ext.Ajax.request({
                                url: MyUtil.Path.getPathAction("Liabilities_Save"),
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                jsonData: {'params' : arrInsert},
                                waitTitle: 'processing'.Translator('Common'),
                                waitMsg: 'sending data'.Translator('Common'),
                                scope: this,
                                success: function(msg) {
                                    if (msg.status) {
                                        this.up('form').getForm().reset();
                                        storeLoadLiabilities.reload();
                                        popupAddNewLiabilitiesForm.hide();
                                    }
                                },
                                failure: function(msg) {
                                    console.log('failure');
                                }
                            });
                        }
                    }
                }
            }, {
                text: 'cancel'.Translator('Common'),
                handler: function() {
                    this.up('form').getForm().reset();
                    popupAddNewLiabilitiesForm.hide();
                }
            }]
        });

        var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToMoveEditor: 1,
            autoCancel: false,
            listeners: {
                'edit': function (editor,e) {
                    var record    = e.record.data,
                        selection = Ext.getCmp('grid-liabilities-customer-list').getView().getSelectionModel().getSelection();

                    if (selection.length == 1) {
                        record.customerId = selection[0].data.id;
                    }

                    Ext.Ajax.request({
                        url: MyUtil.Path.getPathAction("Liabilities_Save"),
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        waitTitle: 'processing'.Translator('Common'),
                        waitMsg: 'sending data'.Translator('Common'),
                        jsonData: {'params': record},
                        scope: this,
                        success: function(msg) {
                            if (msg.status) {
                                storeLoadLiabilities.reload();
                            }
                        },
                        failure: function(msg) {
                            console.log('failure');
                        }
                    });
                }
            }
        });

        var popupAddNewLiabilitiesForm = new Ext.Window({
            title: 'add new liabilities'.Translator('Liabilities')
            , autoWidth: true
            , autoHeight: true
            , border: true
            , modal: true
            , items: addFormLiabilities
        });

        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('grid-win');
        if(!win){
            win = desktop.createWindow({
                id: 'liabilities-list',
                title: 'liabilities management'.Translator('Module'),
                width: width_800,
                height: height_600,
                iconCls: 'icon-grid',
                animCollapse: false,
                constrainHeader: true,
                layout: 'border',
                items: [{
                    title: 'liabilities customer'.Translator('Liabilities'),
                    region:'west',
                    xtype: 'panel',
                    margins: '5 0 5 5',
                    width: 235,
                    collapsible: true,
                    id: 'west-region-container',
                    layout: 'fit',
                    items: [{
                        border: false,
                        xtype: 'grid',
                        id: 'grid-liabilities-customer-list',
                        store: storeLoadLiabilitiesCustomer,
                        loadMask: true,
                        stripeRows : true,
                        selModel: Ext.create('Ext.selection.RowModel'),
                        columns: columnsCustomerLiabilities,
                        trackMouseOver: true,
                        viewConfig: {
                            emptyText: 'no records found'.Translator('Common')
                        },
                        listeners: {
                            beforerender: function () {
                                this.store.load();
                            },
                            itemclick: function (view, record) {
                                storeLoadLiabilities.load();
                            }
                        }
                    }],
                    bbar: new Ext.PagingToolbar({
                        store: storeLoadLiabilitiesCustomer,
                        displayInfo:true
                    })
                }, {
                    title: 'liabilities output invoice'.Translator('Liabilities'),
                    region: 'center',
                    xtype: 'panel',
                    layout: 'fit',
                    margins: '5 5 5 5',
                    items: [{
                        border: false,
                        xtype: 'grid',
                        id: 'grid-liabilities-list',
                        store: storeLoadLiabilities,
                        loadMask: true,
                        selModel: rowModel,
                        plugins: rowEditing,
                        columns: columnsLiabilities,
                        viewConfig: {
                            emptyText: 'no records found'.Translator('Common')
                        },
                        features: [{
                            ftype: 'groupingsummary',
                            groupHeaderTpl: Ext.create('Ext.XTemplate', '<input type="radio" name="rdoInvoiceId" class="rdoInvoiceId" customerId="' + '{[values.rows[0].data.customerId]}' + '" value="' + '{[values.rows[0].data.invoiceId]}' + '">' + '<label>' + 'invoice number'.Translator('Invoice') + ': {name}' + '</label>'),
                            hideGroupedHeader: true,
                            enableGroupingMenu: true,
                            collapsible: false
                        }],
                        listeners: {
                            beforerender: function () {
                                storeLoadLiabilities.loadData([],false);
                            }
                        }
                    }],
                    tbar:[{
                        text:'accept'.Translator('Delivery'),
                        tooltip:'accept'.Translator('Delivery'),
                        iconCls:'accept',
                        handler : function() {
                            var rdoInvoiceId = Ext.query(".rdoInvoiceId:checked");

                            if (rdoInvoiceId.length == 1) {
                                Ext.MessageBox.confirm('warning'.Translator('Common'), 'are you sure'.Translator('Common'), function(btn){
                                    if(btn === 'yes') {
                                        Ext.Ajax.request({
                                            url: MyUtil.Path.getPathAction("Liabilities_AcceptDelivery"),
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json' },
                                            jsonData: {'params' : rdoInvoiceId[0].value},
                                            waitTitle: 'processing'.Translator('Common'),
                                            waitMsg: 'sending data'.Translator('Common'),
                                            scope: this,
                                            success: function(msg) {
                                                if (msg.status) {
                                                    storeLoadLiabilities.reload();
                                                    storeLoadLiabilitiesCustomer.reload();
                                                }
                                            },
                                            failure: function(msg) {
                                                console.log('failure');
                                            }
                                        });
                                    }
                                });
                            } else {
                                MyUtil.Message.MessageWarning('please choice a invoice'.Translator('Liabilities'));
                            }
                        }
                    }, '-', {
                        text:'add'.Translator('Common'),
                        tooltip:'add'.Translator('Common'),
                        iconCls:'add',
                        handler : function() {
                            var rdoInvoiceId = Ext.query(".rdoInvoiceId:checked"),
                                selection    = Ext.getCmp('grid-liabilities-customer-list').getView().getSelectionModel().getSelection();

                            if (rdoInvoiceId.length == 1) {
                                storeLiabilitiesName.load();
                                invoiceSelectId  = rdoInvoiceId[0].value;
                                customerSelectId = selection[0].data.id;
                                popupAddNewLiabilitiesForm.show();
                            } else {
                                MyUtil.Message.MessageWarning('please choice a invoice'.Translator('Liabilities'));
                            }
                        }
                    }, '-',{
                        text:'remove'.Translator('Common'),
                        tooltip:'remove'.Translator('Common'),
                        iconCls:'remove',
                        listeners: {
                            click: function () {
                                var selection = Ext.getCmp('grid-liabilities-list').getView().getSelectionModel().getSelection();

                                if (selection.length > 0) {
                                    Ext.MessageBox.confirm('delete'.Translator('Common'), 'are you sure'.Translator('Common'), function(btn){
                                        if(btn === 'yes') {
                                            var arrId = [];
                                            Ext.each(selection, function(v, k) {
                                                arrId[k] = v.data.id;
                                            });

                                            Ext.Ajax.request({
                                                url: MyUtil.Path.getPathAction("Liabilities_Delete"),
                                                method: 'POST',
                                                headers: { 'Content-Type': 'application/json' },
                                                jsonData: {'params' : arrId},
                                                waitTitle: 'processing'.Translator('Common'),
                                                waitMsg: 'sending data'.Translator('Common'),
                                                scope: this,
                                                success: function(msg) {
                                                    if (msg.status) {
                                                        storeLoadLiabilities.reload();
                                                    }
                                                },
                                                failure: function(msg) {
                                                    console.log('failure');
                                                }
                                            });
                                        }
                                    });
                                } else {
                                    MyUtil.Message.MessageWarning('please choose 1 record to delete'.Translator('Common'));
                                }
                            }
                        }
                    }, '-',{
                        id: 'searchInvoiceName',
                        width: 200,
                        labelWidth: 50,
                        emptyText: 'invoice number'.Translator('Invoice'),
                        xtype: 'textfield'
                    }, {
                        text: 'find'.Translator('Common'),
                        tooltip: 'find'.Translator('Common'),
                        iconCls: 'find',
                        handler: function () {
                            var selection = Ext.getCmp('grid-liabilities-customer-list').getView().getSelectionModel().getSelection();

                            if (selection.length == 1) {
                                storeLoadLiabilities.reload();
                            } else {
                                MyUtil.Message.MessageWarning('please choice a customer'.Translator('Liabilities'));
                            }
                        }
                    }, '->',{
                        text: 'print'.Translator('Common'),
                        tooltip: 'print'.Translator('Common'),
                        iconCls: 'print',
                        listeners: {
                            click: function () {
                                var selection = Ext.getCmp('grid-liabilities-customer-list').getView().getSelectionModel().getSelection();
                                if (selection.length > 0) {
                                    var grid = Ext.getCmp('grid-liabilities-list');
                                    MyUx.grid.Printer.printAutomatically = false;
                                    MyUx.grid.Printer.print(grid);
                                } else {
                                    MyUtil.Message.MessageWarning('please choice a customer'.Translator('Liabilities'));
                                }
                            }
                        }
                    }]
                }]
            });
        }

        storeLoadLiabilities.on('beforeload', function() {
            var selection = Ext.getCmp('grid-liabilities-customer-list').getView().getSelectionModel().getSelection();
            if (selection.length == 1) {
                this.proxy.extraParams = {id: selection[0].data.id, searchInvoiceName:Ext.getCmp('searchInvoiceName').getValue()};
            }
        });

        return win;
    }
});

