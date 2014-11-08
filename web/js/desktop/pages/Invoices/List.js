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
                   {name: 'subject', type: 'int'},
                   {name: 'invoiceType', type: 'int'},
                   {name: 'invoiceNumber', type: 'string'}/*,
                   {name: 'createInvoiceDate', type: 'string'}*/];

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

var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
  clicksToMoveEditor: 1,
  autoCancel: false,
  listeners:{
    'canceledit': function(rowEditing, context) {
        //Do something
    },
    'edit': function(rowEditing, context) {
        //Do something
    }
  }
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
            text: 'invoices list',
//            text: 'invoice management'.Translator('Module'),
            iconCls:'icon-grid'
        };
    },

    createWindow : function(){
        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('grid-win');

        var columnsInvoice = [
            new Ext.grid.RowNumberer(),
            {
//                text: "subject".Translator('Common'),
                text: "Tên Khách Hàng",
                width: 100,
                dataIndex: 'subject',
                editor: {
                    allowBlank: true
                }
            }, /*{
//                text: "create_invoice_date".Translator('Invoice'),
                text: "Ngày",
                flex: 1,
                dataIndex: 'createInvoiceDate',
                editor: {
                    allowBlank: true
                }
            },*/ {
//                text: "create_invoice_date".Translator('Invoice'),
                text: "Loại Phiếu",
                flex: 1,
                dataIndex: 'invoiceType',
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
            },/*{
                xtype:'actioncolumn',
                width:100,
                items: [{
//                    icon: 'images/edit.png',  // Use a URL in the icon config
//                    tooltip: 'Edit',
                    launch: function(grid, rowIndex, colIndex) {
//                        var rec = grid.getStore().getAt(rowIndex);
//                        alert("Edit " + rec.get('firstname'));

                        var a = desktop.getWindow(new SrcPageUrl.Invoices.Input());
                        a.getActiveWindow();
//                        a.show();
//                        return a;
//                        return new SrcPageUrl.Invoices.Input().createWindow();
                    }
                }*//*, {
                    xtype: 'button',
                    text: 'Xem',
                    width: 50,
                    scale: 'small',
                    handler: function() {
                        alert("Hello World!");
                    }
                }*//*]
            },*/ {
//                text: "create_invoice_date".Translator('Invoice'),
                text: "Xem Chi Tiết",
                flex: 2,
//                dataIndex: 'invoiceNumber',
                editor: {
                    allowBlank: true
                }
            }, {
//                text: "create_invoice_date".Translator('Invoice'),
                text: "Trạng Thái",
                flex: 2,
//                dataIndex: 'invoiceNumber',
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
                title:'invoice management'.Translator('Module'),
                width:600,
                height:480,
                iconCls: 'icon-grid',
                animCollapse:false,
                constrainHeader:true,
                layout: 'fit',
                items: [
                  {
                    border: false,
                    id: 'grid-invoice-list',
                    xtype: 'grid',
                    store: storeLoadInvoice,
                    selModel: rowModel,
                    columns: columnsInvoice,
//                    plugins: [rowEditing],
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

