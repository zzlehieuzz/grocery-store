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
            }, {
                fieldLabel: 'Từ Ngày',
                xtype: 'datefield',
                name: 'fromDate',
                id: 'fromDate',
                anchor: '40%'
            }, {
                fieldLabel: 'Đến Ngày',
                name: 'toDate',
                id: 'toDate',
                xtype: 'datefield',
                anchor: '40%'
            },{
                xtype: 'button',
                text: 'Tìm',
                anchor: '10%',
                handler : function() {

                    var invoiceType = Ext.getCmp('invoiceTypeRadio').getValue().rb;
                    var fromDate = Ext.util.Format.date(Ext.getCmp('fromDate').getValue(), 'Y-m-d');
                    var toDate = Ext.util.Format.date(Ext.getCmp('toDate').getValue(), 'Y-m-d');

                    storeLoadInvoice.reload({params:{limit: 5, page: 1, start: 1, invoiceType: invoiceType, fromDate: fromDate, toDate: toDate}});
                }
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

                                if (invoiceType2 == 1) {
                                    Ext.Msg.alert("Xem chi tiết Phiếu Nhập ID: " + invoiceId)
                                } else {
                                    Ext.Msg.alert("Xem chi tiết Phiếu Xuất ID: " + invoiceId)
                                }

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

