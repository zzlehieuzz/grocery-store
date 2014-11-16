/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'data',
    id  : 'id',
    totalProperty: 'total'
};

var objectField = [{name: 'id',                type: 'int'},
                   {name: 'invoiceId',         type: 'int'},
                   {name: 'driverId',          type: 'int'},
                   {name: 'invoiceNumber',     type: 'string'},
                   {name: 'customerName',      type: 'string'},
                   {name:'createInvoiceDate',  type:'date', dateFormat:'Y-m-d H:i:s'},
                   {name: 'address',           type: 'string'},
                   {name: 'phoneNumber',       type: 'string'}];

var objFieldDriver = [{name: 'id',   type: 'int'},
                      {name: 'name', type: 'string'}];
var objFieldInvoice = [{name: 'id',   type: 'int'},
                       {name: 'invoiceNumber', type: 'string'}];

MyUtil.Object.defineModel('DriverInvoice', objectField);
MyUtil.Object.defineModel('Driver', objFieldDriver);
MyUtil.Object.defineModel('Invoice', objFieldInvoice);

var storeLoadDriverInvoice = new Ext.data.JsonStore({
    model: 'DriverInvoice',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Delivery_DriverInvoiceLoad"),
        reader: readerJson
    }),
    autoLoad: false
});

var storeLoadDriver = new Ext.data.JsonStore({
    model: 'Driver',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Driver_Load"),
        reader: readerJson
    }),
    pageSize: pageSizeDefault,
    autoLoad: ({params: {limit: limitDefault, page: pageDefault, start: startDefault}}, false)
});

var storeLoadInvoice = new Ext.data.JsonStore({
    model: 'Invoice',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Delivery_InvoiceLoad"),
        reader: readerJson
    }),
    autoLoad: false
});

Ext.define('SrcPageUrl.Delivery.List', {
    extend: 'Ext.ux.desktop.Module',
    requires: [
        'Ext.grid.*',
        'Ext.data.*',
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.form.field.Number',
        'Ext.form.field.Date',
        'Ext.form.field.ComboBox'
    ],

    id:'delivery-list',

    init : function(){
        this.launcher = {
            text: 'delivery management'.Translator('Module'),
            iconCls:'icon-grid'
        };
    },

    createWindow : function (){
        var rowModel = Ext.create('Ext.selection.RowModel', {mode : "MULTI"});

        var columnsDriverInvoice = [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'id',
                hidden : true
            }, {
                dataIndex: 'invoiceId',
                hidden : true
            }, {
                dataIndex: 'driverId',
                hidden : true
            }, {
                text: "invoice number".Translator('Invoice'),
                width: 150,
                dataIndex: 'invoiceNumber',
                style: 'text-align:center;'
            }, {
                text: "create invoice date".Translator('Invoice'),
                width: 80,
                dataIndex: 'createInvoiceDate',
                style: 'text-align:center;',
                renderer: Ext.util.Format.dateRenderer('d/m/Y')
            }, {
                text: "phone number".Translator('Invoice'),
                width: 150,
                dataIndex: 'phoneNumber',
                style: 'text-align:center;',
                renderer: function(value) {return value.replace(/^(\d{3})(\d{3})(\d{4})$/, '$1-$2-$3');}
            }, {
                text: "address".Translator('Invoice'),
                flex: 1,
                dataIndex: 'address',
                style: 'text-align:center;'
            }
        ];

        var columnsDriver = [
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

        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('grid-win');
        if(!win){
            win = desktop.createWindow({
                id: 'delivery-list',
                title: 'delivery management'.Translator('Module'),
                width: 900,
                height: height_600,
                iconCls: 'icon-grid',
                animCollapse: false,
                constrainHeader: true,
                layout: 'border',
                items: [{
                    title: 'driver'.Translator('Driver'),
                    region:'west',
                    xtype: 'panel',
                    margins: '5 0 5 5',
                    width: 230,
                    collapsible: true,
                    layout: 'fit',
                    items: [{
                        border: false,
                        xtype: 'grid',
                        id: 'grid-driver-list',
                        store: storeLoadDriver,
                        loadMask: true,
                        stripeRows : true,
                        selModel: Ext.create('Ext.selection.RowModel'),
                        columns: columnsDriver,
                        trackMouseOver: true,
                        viewConfig: {
                            emptyText: 'no records found'.Translator('Common')
                        },
                        listeners: {
                            beforerender: function () {
                                this.store.load();
                            },
                            itemclick: function () {
                                storeLoadDriverInvoice.load();
                                storeLoadInvoice.load();
                            }
                        }
                    }],
                    bbar: new Ext.PagingToolbar({
                        store: storeLoadDriver,
                        displayInfo:true
                    })
                }, {
                    title: 'invoice list output'.Translator('Delivery'),
                    region: 'center',
                    xtype: 'panel',
                    layout: 'fit',
                    margins: '5 5 5 5',
                    items: [{
                        border: false,
                        xtype: 'grid',
                        id: 'grid-driver-invoice-list',
                        store: storeLoadDriverInvoice,
                        loadMask: true,
                        selModel: rowModel,
                        columns: columnsDriverInvoice,
                        viewConfig: {
                            emptyText: 'no records found'.Translator('Common')
                        },
                        listeners: {
                            beforerender: function () {
                                storeLoadDriverInvoice.loadData([],false);
                            }
                        }
                    }],
                    tbar:[{
                        text:'add'.Translator('Common'),
                        tooltip:'add'.Translator('Common'),
                        iconCls:'add',
                        handler : function() {
                            var selection            = Ext.getCmp('grid-driver-list').getView().getSelectionModel().getSelection();
                            var listAddInvoiceOutput = Ext.getCmp('listAddInvoiceOutput').getValue();

                            if (selection.length == 1 && listAddInvoiceOutput != null && listAddInvoiceOutput) {
                                if(listAddInvoiceOutput[0] == '' || listAddInvoiceOutput[0] == null) {
                                    MyUtil.Message.MessageWarning('please choose invoice'.Translator('Delivery'));
                                    return false;
                                }
                                if(typeof listAddInvoiceOutput != 'object') {
                                    MyUtil.Message.MessageWarning('please choose invoice'.Translator('Delivery'));
                                    return false;
                                }
                                Ext.Ajax.request({
                                    url: MyUtil.Path.getPathAction("Delivery_AddDriverInvoice"),
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    jsonData: {'params' : {'driverId': selection[0].data.id,
                                                           'data'    : listAddInvoiceOutput}},
                                    waitTitle: 'processing'.Translator('Common'),
                                    waitMsg: 'sending data'.Translator('Common'),
                                    scope: this,
                                    success: function(msg) {
                                        if (msg.status) {
                                            storeLoadDriverInvoice.reload();
                                            storeLoadInvoice.reload();
                                            Ext.getCmp('listAddInvoiceOutput').setValue('');
                                        }
                                    },
                                    failure: function(msg) {
                                        console.log('failure');
                                    }
                                });
                            }
                        }
                    }, '-',{
                        text:'remove'.Translator('Common'),
                        tooltip:'remove'.Translator('Common'),
                        iconCls:'remove',
                        listeners: {
                            click: function () {
                                var selection = Ext.getCmp('grid-driver-invoice-list').getView().getSelectionModel().getSelection();

                                if (selection.length > 0) {
                                    Ext.MessageBox.confirm('delete'.Translator('Common'), 'are you sure'.Translator('Common'), function(btn){
                                        if(btn === 'yes') {
                                            var arrId = [];
                                            Ext.each(selection, function(v, k) {
                                                arrId[k] = v.data.id;
                                            });

                                            Ext.Ajax.request({
                                                url: MyUtil.Path.getPathAction("Delivery_Delete"),
                                                method: 'POST',
                                                headers: { 'Content-Type': 'application/json' },
                                                jsonData: {'params' : arrId},
                                                waitTitle: 'processing'.Translator('Common'),
                                                waitMsg: 'sending data'.Translator('Common'),
                                                scope: this,
                                                success: function(msg) {
                                                    if (msg.status) {
                                                        storeLoadDriverInvoice.reload();
                                                        storeLoadInvoice.reload();
                                                        Ext.getCmp('listAddInvoiceOutput').setValue('');
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
                    }, '-', Ext.create('Ext.form.field.ComboBox', {
                        id: 'listAddInvoiceOutput',
                        fieldLabel: 'select invoice output'.Translator('Delivery'),
                        multiSelect: true,
                        displayField: 'invoiceNumber',
                        valueField: 'id',
                        width: 300,
                        labelWidth: 90,
                        store:storeLoadInvoice,
                        typeAhead: true,
                        queryMode: 'local'
                    })]
                }]
            });
        }

        storeLoadDriverInvoice.on('beforeload', function() {
            var selection = Ext.getCmp('grid-driver-list').getView().getSelectionModel().getSelection();
            if (selection.length == 1) {
                this.proxy.extraParams = {driverId: selection[0].data.id};
            }
        });

        return win;
    },

    statics: {
        getDummyData: function () {
            return [];
        }
    }
});

