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
                   {name: 'invoiceId',     type: 'int'},
                   {name: 'invoiceNumber', type: 'string'},
                   {name: 'name',          type: 'string'},
                   {name: 'amount',        type: 'int'},
                   {name: 'price',         type: 'float'}];

var objectFieldCustomer = [{name: 'id',   type: 'int'},
                           {name: 'name', type: 'string'}];

MyUtil.Object.defineModel('Liabilities', objectField);
MyUtil.Object.defineModel('LiabilitiesCustomer', objectFieldCustomer);

var storeLoadLiabilities = new Ext.data.JsonStore({
    model: 'Liabilities',
    proxy: new Ext.data.HttpProxy({
        url: MyUtil.Path.getPathAction("Liabilities_Load"),
        reader: readerJson
    }),
    pageSize: pageSizeDefault,
    groupField: 'invoiceNumber',
    autoLoad: ({params:{limit: limitDefault, page: pageDefault, start: startDefault}}, false)
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

console.log(pageSizeDefault);
console.log(limitDefault);
console.log(pageDefault);
console.log(startDefault);

Ext.define('SrcPageUrl.Liabilities.List', {
    extend: 'Ext.ux.desktop.Module',
    requires: [
        'Ext.grid.*',
        'Ext.data.*',
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.form.field.Number',
        'Ext.form.field.Date'
    ],

    id:'liabilities-list',

    init : function(){
        this.launcher = {
            text: 'liabilities management'.Translator('Module'),
            iconCls:'icon-grid'
        };
    },

    createWindow : function (){
        var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToMoveEditor: 1,
            autoCancel: false,
            listeners: {
                'edit': function (editor,e) {
                    var record = e.record.data;

                    Ext.Ajax.request({
                        url: MyUtil.Path.getPathAction("Unit_Update"),
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        waitTitle: 'processing'.Translator('Common'),
                        waitMsg: 'sending data'.Translator('Common'),
                        jsonData: {'params': record},
                        scope: this,
                        success: function(msg) {
                            if (msg.status) {
                                storeLoadUnit.reload();
                                console.log('success');
                            }
                        },
                        failure: function(msg) {
                            console.log('failure');
                        }
                    });
                }
            }
        });

        var rowModel = Ext.create('Ext.selection.RowModel', {
            mode : "MULTI",
            onKeyPress: function(e, t) {
                console.log(e);
            }
        });

        var columnsLiabilities = [
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
                    width: 200,
                    collapsible: true,
                    id: 'west-region-container',
                    layout: 'fit',
                    items: [{
                        border: false,
                        xtype: 'grid',
                        id: 'grid-liabilities-customer-list',
                        store: storeLoadLiabilitiesCustomer,
                        loadMask: true,
                        columns: columnsCustomerLiabilities,
                        features: [{
                            ftype: 'groupingsummary',
                            groupHeaderTpl: '{name}',
                            hideGroupedHeader: true,
                            enableGroupingMenu: false
                        }],
                        listeners: {
                            beforerender: function () {
                                this.store.load();
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
                        features: [{
                            ftype: 'groupingsummary',
                            groupHeaderTpl: '{name}',
                            hideGroupedHeader: true,
                            enableGroupingMenu: false
                        }],
                        listeners: {
                            beforerender: function () {
                                //this.store.load();
                            }
                        }
                    }],
                    tbar:[{
                        text:'add'.Translator('Common'),
                        tooltip:'add'.Translator('Common'),
                        iconCls:'add',
                        handler : function() {
                            rowEditing.cancelEdit();

                            // Create a model instance
                            var r = Ext.create('Unit', {
                                id: '',
                                name: '',
                                code: '',
                                unit: ''
                            });

                            storeLoadUnit.insert(0, r);
                            rowEditing.startEdit(0, 0);
                        }
                    }, '-',{
                        text:'remove'.Translator('Common'),
                        tooltip:'remove'.Translator('Common'),
                        iconCls:'remove',
                        listeners: {
                            click: function () {
                                var selection = Ext.getCmp('grid-liabilities-list').getView().getSelectionModel().getSelection();

                                if (selection.length > 0) {
                                    Ext.MessageBox.confirm('delete'.Translator('Common'), 'Are you sure'.Translator('Common'), function(btn){
                                        if(btn === 'yes') {
                                            var arrId = [];
                                            Ext.each(selection, function(v, k) {
                                                arrId[k] = v.data.id;
                                            });

                                            Ext.Ajax.request({
                                                url: MyUtil.Path.getPathAction("Unit_Delete"),
                                                method: 'POST',
                                                headers: { 'Content-Type': 'application/json' },
                                                jsonData: {'params' : arrId},
                                                waitTitle: 'processing'.Translator('Common'),
                                                waitMsg: 'sending data'.Translator('Common'),
                                                scope: this,
                                                success: function(msg) {
                                                    if (msg.status) {
                                                        storeLoadUnit.reload();
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
                    }]
                }]
            });


            //win = desktop.createWindow({
            //    id: 'liabilities-list',
            //    title: 'liabilities management'.Translator('Module'),
            //    width: width_600,
            //    height: height_600,
            //    iconCls: 'icon-grid',
            //    animCollapse: false,
            //    constrainHeader: true,
            //    layout: 'fit',
            //    items:
            //        [
            //            {
            //            border: false,
            //            xtype: 'grid',
            //            id: 'grid-liabilities-list',
            //            store: storeLoadLiabilities,
            //            loadMask: true,
            //            selModel: rowModel,
            //            plugins: rowEditing,
            //            columns: columnsLiabilities,
            //            features: [{
            //                ftype: 'groupingsummary',
            //                groupHeaderTpl: '{name}',
            //                hideGroupedHeader: true,
            //                enableGroupingMenu: false
            //            }],
            //            listeners:{
            //                beforerender: function () {
            //                    this.store.load();
            //                }
            //            }
            //        }
            //    ],
            //    tbar:[{
            //        text:'add'.Translator('Common'),
            //        tooltip:'add'.Translator('Common'),
            //        iconCls:'add',
            //        handler : function() {
            //          rowEditing.cancelEdit();
            //
            //          // Create a model instance
            //          var r = Ext.create('Unit', {
            //            id: '',
            //            name: '',
            //            code: '',
            //            unit: ''
            //          });
            //
            //          storeLoadUnit.insert(0, r);
            //          rowEditing.startEdit(0, 0);
            //        }
            //    }, '-',{
            //        text:'remove'.Translator('Common'),
            //        tooltip:'remove'.Translator('Common'),
            //        iconCls:'remove',
            //        listeners: {
            //            click: function () {
            //                var selection = Ext.getCmp('grid-liabilities-list').getView().getSelectionModel().getSelection();
            //
            //                if (selection.length > 0) {
            //                    Ext.MessageBox.confirm('delete'.Translator('Common'), 'Are you sure'.Translator('Common'), function(btn){
            //                        if(btn === 'yes') {
            //                            var arrId = [];
            //                            Ext.each(selection, function(v, k) {
            //                                arrId[k] = v.data.id;
            //                            });
            //
            //                            Ext.Ajax.request({
            //                                url: MyUtil.Path.getPathAction("Unit_Delete"),
            //                                method: 'POST',
            //                                headers: { 'Content-Type': 'application/json' },
            //                                jsonData: {'params' : arrId},
            //                                waitTitle: 'processing'.Translator('Common'),
            //                                waitMsg: 'sending data'.Translator('Common'),
            //                                scope: this,
            //                                success: function(msg) {
            //                                    if (msg.status) {
            //                                        storeLoadUnit.reload();
            //                                        console.log('success');
            //                                    }
            //                                },
            //                                failure: function(msg) {
            //                                    console.log('failure');
            //                                }
            //                            });
            //                        }
            //                    });
            //                } else {
            //                    MyUtil.Message.MessageError();
            //                }
            //            }
            //        }
            //    }]
            //});
        }
        return win;
    },

    statics: {
        getDummyData: function () {
            return [];
        }
    }
});

