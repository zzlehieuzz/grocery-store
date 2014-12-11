/**
 * @class Ext.ux.grid.Printer
 * @author Ed Spencer (edward@domine.co.uk)
 * Helper class to easily print the contents of a grid. Will open a new window with a table where the first row
 * contains the headings from your column model, and with a row for each item in your grid's store. When formatted
 * with appropriate CSS it should look very similar to a default grid. If renderers are specified in your column
 * model, they will be used in creating the table. Override headerTpl and bodyTpl to change how the markup is generated
 *
 * Usage:
 *
 * 1 - Add Ext.Require Before the Grid code
 * Ext.require([
 *   'Ext.ux.grid.GridPrinter',
 * ]);
 *
 * 2 - Declare the Grid
 * var grid = Ext.create('Ext.grid.Panel', {
 *   columns: //some column model,
 *   store   : //some store
 * });
 *
 * 3 - Print!
 * Ext.ux.grid.Printer.mainTitle = 'Your Title here'; //optional
 * Ext.ux.grid.Printer.print(grid);
 *
 * Original url: http://edspencer.net/2009/07/printing-grids-with-ext-js.html
 *
 * Modified by Loiane Groner (me@loiane.com) - September 2011 - Ported to Ext JS 4
 * http://loianegroner.com (English)
 * http://loiane.com (Portuguese)
 *
 * Modified by Bruno Sales - August 2012
 *
 * Modified by Paulo Goncalves - March 2012
 *
 * Modified by Beto Lima - March 2012
 *
 * Modified by Beto Lima - April 2012
 *
 * Modified by Paulo Goncalves - May 2012
 *
 * Modified by Nielsen Teixeira - 2012-05-02
 *
 * Modified by Joshua Bradley - 2012-06-01
 *
 * Modified by Loiane Groner - 2012-09-08
 *
 * Modified by Loiane Groner - 2012-09-24
 *
 */
Ext.define("MyUx.grid.Printer", {

    requires: 'Ext.XTemplate',

    statics: {
        /**
         * Prints the passed grid. Reflects on the grid's column model to build a table, and fills it using the store
         * @param {Ext.grid.Panel} grid The grid to print
         */
        print: function(grid) {
            //We generate an XTemplate here by using 2 intermediary XTemplates - one to create the header,
            //the other to create the body (see the escaped {} below)
            var columns = [];
            //account for grouped columns
            Ext.each(grid.columns, function(c) {
                if(c.items.length > 0) {
                    columns = columns.concat(c.items.items);
                } else {
                    columns.push(c);
                }
            });

            //build a usable array of store data for the XTemplate
            var data = [];
            grid.store.data.each(function(item, row) {
                var convertedData = {};
                //apply renderers from column model
                for (var key in item.data) {
                    var value = item.data[key];
                    Ext.each(columns, function(column, col) {
                        if (column && column.dataIndex == key) {
                            /*
                             * TODO: add the meta to template
                             */
                            var meta = {item: '', tdAttr: '', style: ''};
                            value = column.renderer ? column.renderer.call(grid, value, meta, item, row, col, grid.store, grid.view) : value;
                            convertedData[Ext.String.createVarName(column.text)] = value;
                        } else if (column && column.xtype === 'rownumberer'){
                            convertedData['STT'] = row + 1;
                        }
                    }, this);
                }

                data.push(convertedData);
            });

            //remove columns that do not contains dataIndex or dataIndex is empty. for example: columns filter or columns button
            var clearColumns = [];
            Ext.each(columns, function (column, row) {
                if (column && column.xtype === 'rownumberer'){
                    column.text = 'STT';
                    clearColumns.push(column);
                }
                else if ((column) && (!Ext.isEmpty(column.dataIndex) && !column.hidden)) {
                    clearColumns.push(column);
                }
            });
            columns = clearColumns;

            //get Styles file relative location, if not supplied
            if (this.stylesheetPath === null) {
                var scriptPath = Ext.Loader.getPath('MyUx.grid.Printer');
                this.stylesheetPath = scriptPath.substring(0, scriptPath.indexOf('Printer.js')) + 'gridPrinterCss/print.css';
            }

            //use the headerTpl and bodyTpl markups to create the main XTemplate below
            var headings = Ext.create('Ext.XTemplate', this.headerTpl).apply(columns);
            var body     = Ext.create('Ext.XTemplate', this.bodyTpl).apply(columns);
            var pluginsBody = '',
                pluginsBodyMarkup = [];

            //add relevant plugins
            Ext.each(grid.plugins, function(p) {
                if (p.ptype == 'rowexpander') {
                    pluginsBody += p.rowBodyTpl.join('');
                }
            });

            if (pluginsBody != '') {
                pluginsBodyMarkup = [
                    '<tr class="{[xindex % 2 === 0 ? "even" : "odd"]}">',
                    '<td colspan="' + columns.length + '">',
                    pluginsBody,
                    '</td></tr>'
                ];
            }

            //Here because inline styles using CSS, the browser did not show the correct formatting of the data the first time that loaded
            var htmlMarkup = [
                '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
                '<html class="' + Ext.baseCSSPrefix + 'ux-grid-printer">',
                '<head>',
                '<title>Print</title>',
                '<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />',
                '<link href="' + this.stylesheetPath + '" rel="stylesheet" type="text/css" />',
                '<title>' + grid.title + '</title>',
                '</head>',

                '<body class="' + Ext.baseCSSPrefix + 'ux-grid-printer-body">',
                    '<div class="' + Ext.baseCSSPrefix + 'ux-grid-printer-noprint ' + Ext.baseCSSPrefix + 'ux-grid-printer-links">',
                        '<a class="' + Ext.baseCSSPrefix + 'ux-grid-printer-linkprint" href="javascript:void(0);" onclick="window.print();">' + this.printLinkText + '</a>',
                        '<a class="' + Ext.baseCSSPrefix + 'ux-grid-printer-linkclose" href="javascript:void(0);" onclick="window.close();">' + this.closeLinkText + '</a>',
                    '</div>',

                    '<h1>' + this.mainTitle + '</h1>',

                    '<table>',
                        '<tr>',
                        headings,
                        '</tr>',

                        '<tpl for=".">',
                        '<tr class="{[xindex % 2 === 0 ? "even" : "odd"]}">',
                        body,
                        '</tr>',
                        pluginsBodyMarkup.join(''),
                        '</tpl>',

                    '</table>',

                '</body>',
                '</html>'
            ];

            var html = Ext.create('Ext.XTemplate', htmlMarkup).apply(data),
                win  = window.open('', '_blank', "height='100%',width='100%',status=yes,toolbar=no,menubar=yes,location=no,scrollbars=yes");
            win.document.open();
            win.document.write(html);
            win.document.close();

            if (this.printAutomatically){
                win.print();
            }

            //Another way to set the closing of the main
            if (this.closeAutomaticallyAfterPrint){
                if(Ext.isIE){
                    window.close();
                } else {
                    win.close();
                }
            }
        },
        printExt: function(grid, extData) {
            //We generate an XTemplate here by using 2 intermediary XTemplates - one to create the header,
            //the other to create the body (see the escaped {} below)
            var columns = [];
            //account for grouped columns
            Ext.each(grid.columns, function(c) {
                if(c.items.length > 0) {
                    columns = columns.concat(c.items.items);
                } else {
                    columns.push(c);
                }
            });

            //build a usable array of store data for the XTemplate
            var data = [];
            var total = 0;
            grid.store.data.each(function(item, row) {
                var convertedData = {};
                //apply renderers from column model
                for (var key in item.data) {
                    var value = item.data[key];

                    if (key == 'amount') {
                        total += value;
                    }

                    Ext.each(columns, function(column, col) {
                        if (column && column.dataIndex == key) {

                            /*
                             * TODO: add the meta to template
                             */
                            var meta = {item: '', tdAttr: '', style: ''};
                            value = column.renderer ? column.renderer.call(grid, value, meta, item, row, col, grid.store, grid.view) : value;
                            convertedData[Ext.String.createVarName(column.text)] = value;
                        } else if (column && column.xtype === 'rownumberer'){
                            convertedData['STT'] = row + 1;
                        }
                    }, this);
                }

                data.push(convertedData);
            });

            //remove columns that do not contains dataIndex or dataIndex is empty. for example: columns filter or columns button
            var clearColumns = [];
            Ext.each(columns, function (column, row) {
                if (column && column.xtype === 'rownumberer'){
                    column.text = 'STT';
                    clearColumns.push(column);
                }
                else if ((column) && (!Ext.isEmpty(column.dataIndex) && !column.hidden)) {
                    clearColumns.push(column);
                }
            });
            columns = clearColumns;

            //get Styles file relative location, if not supplied
            if (this.stylesheetPath === null) {
                var scriptPath = Ext.Loader.getPath('MyUx.grid.Printer');
                this.stylesheetPath = scriptPath.substring(0, scriptPath.indexOf('Printer.js')) + 'gridPrinterCss/print.css';
            }

            //use the headerTpl and bodyTpl markups to create the main XTemplate below
            var headings = Ext.create('Ext.XTemplate', this.headerTpl).apply(columns);
            var body     = Ext.create('Ext.XTemplate', this.bodyTpl).apply(columns);
            var pluginsBody = '',
                pluginsBodyMarkup = [];

            //add relevant plugins
            Ext.each(grid.plugins, function(p) {
                if (p.ptype == 'rowexpander') {
                    pluginsBody += p.rowBodyTpl.join('');
                }
            });

            if (pluginsBody != '') {
                pluginsBodyMarkup = [
                    '<tr class="{[xindex % 2 === 0 ? "even" : "odd"]}">',
                    '<td colspan="' + columns.length + '">',
                    pluginsBody,
                    '</td></tr>'
                ];
            }

            //Here because inline styles using CSS, the browser did not show the correct formatting of the data the first time that loaded

            var contentNew = "";
            if (extData.length) {
                contentNew =   extData;
            }

            var htmlMarkupCustom = [
                '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
                '<html class="' + Ext.baseCSSPrefix + 'ux-grid-printer">',
                '<head>',
                '<title>Print</title>',
                '<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />',
                '<link href="' + this.stylesheetPath + '" rel="stylesheet" type="text/css" />',
                '<style type="text/css">'+
                    '.no-border tr td{ border-style:none !important; }'+
                    '.font-bold{ font-weight:bold !important; }'+

                    'h1{'+
                        'page-break-before: always;'+
                    '}'+

                    '@media print {'+
                    '.page-break	{ display: block; page-break-before: always; }'+
                    '}'+
                '</style>',
                '<title>' + grid.title + '</title>',
                '</head>',

                '<body class="' + Ext.baseCSSPrefix + 'ux-grid-printer-body">',
                '<div class="' + Ext.baseCSSPrefix + 'ux-grid-printer-noprint ' + Ext.baseCSSPrefix + 'ux-grid-printer-links">',
                '<a class="' + Ext.baseCSSPrefix + 'ux-grid-printer-linkprint" href="javascript:void(0);" onclick="window.print();">' + this.printLinkText + '</a>',
                '<a class="' + Ext.baseCSSPrefix + 'ux-grid-printer-linkclose" href="javascript:void(0);" onclick="window.close();">' + this.closeLinkText + '</a>',
                '</div>',

                '<h1>' + this.mainTitle + '</h1>',

                //Customize here
                    contentNew,
                '<br>' +
                '<br>' +
                //End Customize here

                '<table class="page-break" border="0" cellpadding="0" cellspacing="0">',
                    '<tr>',
                        headings,
                    '</tr>',

                    '<tpl for=".">',
                    '<tr class="{[xindex % 2 === 0 ? "even" : "odd"]}">',
                    body,
                    '</tr>',
                        pluginsBodyMarkup.join(''),
                    '</tpl>',

                    //Row total
                    '<tr>',
                        '<td style="text-align: right;" colspan="' + (columns.length -1) + '">',
                            '<span class="font-bold">'+ 'total'.Translator('Invoice') + '</span>',
                        '</td>',

                        '<td>',
                            total + 'currency'.Translator('Invoice'),
                        '</td>',
                    '</tr>',
                    //End Row total

                '</table>',

                '</body>',
                '</html>'
            ];

            var html = Ext.create('Ext.XTemplate', htmlMarkupCustom).apply(data),
                win  = window.open('', '_blank', "height='100%',width='100%',status=yes,toolbar=no,menubar=yes,location=no,scrollbars=yes");
            win.document.open();
            win.document.write(html);
            win.document.close();

            if (this.printAutomatically){
                win.print();
            }

            //Another way to set the closing of the main
            if (this.closeAutomaticallyAfterPrint){
                if(Ext.isIE){
                    window.close();
                } else {
                    win.close();
                }
            }
        },
        printExtList: function(extData) {
            var columns = [];

            //get Styles file relative location, if not supplied
            if (this.stylesheetPath === null) {
                var scriptPath = Ext.Loader.getPath('MyUx.grid.Printer');
                this.stylesheetPath = scriptPath.substring(0, scriptPath.indexOf('Printer.js')) + 'gridPrinterCss/print.css';
            }

            //use the headerTpl and bodyTpl markups to create the main XTemplate below
            var headings = Ext.create('Ext.XTemplate', this.headerTpl).apply(columns);
            var body     = Ext.create('Ext.XTemplate', this.bodyTpl).apply(columns);
            var pluginsBody = '',
                pluginsBodyMarkup = [];

            if (pluginsBody != '') {
                pluginsBodyMarkup = [
                    '<tr class="{[xindex % 2 === 0 ? "even" : "odd"]}">',
                    '<td colspan="' + columns.length + '">',
                    pluginsBody,
                    '</td></tr>'
                ];
            }

            var contentNew = "";
            var dataForms = "";
            var dataGrids = "";

            if (extData) {

                Ext.each(extData, function (record) {

                dataForms = '<table class="no-border" border="0px" style="width: 70%">'+
                    '<tr>'+
                    '<td class="font-bold">'+
                    'invoice number'.Translator('Invoice')+
                    '</td>'+

                    '<td>'+
                        record.data.invoiceNumber+
                    '</td>'+

                    '<td class="font-bold">'+
                    'customer name'.Translator('Invoice')+
                    '</td>'+

                    '<td>'+
                        record.data.customerName+
                    '</td>'+

                    '<td class="font-bold">'+
                    'phone number'.Translator('Invoice')+
                    '</td>'+

                    '<td>'+
                        record.data.phoneNumber+
                    '</td>'+
                    '</tr>'+

                    '<tr>'+
                    '<td class="font-bold">'+
                    'customer code'.Translator('Invoice')+
                    '</td>'+

                    '<td>'+
                        record.data.customerCode+
                    '</td>'+

                    '<td class="font-bold">'+
                    'address'.Translator('Invoice')+
                    '</td>'+

                    '<td>'+
                        record.data.address+
                    '</td>'+

                    '<td class="font-bold">'+
                    'description'.Translator('Invoice')+
                    '</td>'+

                    '<td>'+
                        record.data.description +
                    '</td>'+
                    '</tr>'+

                    '</table>';

                    var rowDetail = "";
                    var total = 0;
                    var order = 1;

                    Ext.each(record.data.invoiceId, function (recordDetail) {

                        var classColor = order % 2 === 0 ? "even" : "odd";

                        total = total + recordDetail.amount;
                        rowDetail = rowDetail + '<tr class="'+classColor+'">'+
                                                    '<td>'+
                                                         order+
                                                    '</td>'+

                                                    '<td>'+
                                                        recordDetail.productName+
                                                    '</td>'+

                                                    '<td>'+
                                                        recordDetail.productCode+
                                                    '</td>'+

                                                    '<td>'+
                                                        recordDetail.unitName+
                                                    '</td>'+

                                                    '<td>'+
                                                        recordDetail.quantity+
                                                    '</td>'+

                                                    '<td>'+
                                                        recordDetail.price+
                                                    '</td>'+

                                                    '<td>'+
                                                        recordDetail.amount+
                                                    '</td>'+

                                                '</tr>';

                                                order++;
                                            });

                    dataGrids = '<table>'+
                        //Header
                        '<tr>'+
                            '<th>'+
                                "order".Translator('Invoice')+
                            '</th>'+

                            '<th>'+
                                'product name'.Translator('Product')+
                            '</th>'+

                            '<th>'+
                                'product code'.Translator('Product')+
                            '</th>'+

                            '<th>'+
                                'unit'.Translator('Product')+
                            '</th>'+

                            '<th>'+
                                "quantity".Translator('Product')+
                            '</th>'+

                            '<th>'+
                                "price".Translator('Product')+
                            '</th>'+

                            '<th>'+
                                "amount".Translator('Product')+
                            '</th>'+
                        '</tr>'+

                        //Row Content
                        rowDetail +
                        //End Row Content

                        //Row total
                        '<tr>'+
                            '<td style="text-align: right;" colspan="6">'+
                            '<span class="font-bold">'+ 'total'.Translator('Invoice') + '</span>'+
                            '</td>'+

                            '<td>'+
                                total + 'currency'.Translator('Invoice')+
                            '</td>'+
                        '</tr>'+
                        //End Row total

                    '</table>'+

                    '<br>' +  '<br>' +  '<br>' +  '<br>'+  '<br>';

                     contentNew = contentNew + dataForms + '<br>' + '<br>' + dataGrids;
                });
            }

            var htmlMarkupCustom = [
                '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
                '<html class="' + Ext.baseCSSPrefix + 'ux-grid-printer">',
                '<head>',
                '<title>Print</title>',
                '<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />',
                '<link href="' + this.stylesheetPath + '" rel="stylesheet" type="text/css" />',
                '<style type="text/css">'+
                    '.no-border tr td{ border-style:none !important; }'+
                    '.font-bold{ font-weight:bold !important; }'+

                    'h1{'+
                    'page-break-before: always;'+
                    '}'+

                    '@media print {'+
                    '.page-break	{ display: block; page-break-before: always; }'+
                    '}'+
                    '</style>',
                '<title>' + 'Danh sách phiếu xuất' + '</title>',
                '</head>',

                '<body class="' + Ext.baseCSSPrefix + 'ux-grid-printer-body">',
                '<div class="' + Ext.baseCSSPrefix + 'ux-grid-printer-noprint ' + Ext.baseCSSPrefix + 'ux-grid-printer-links">',
                '<a class="' + Ext.baseCSSPrefix + 'ux-grid-printer-linkprint" href="javascript:void(0);" onclick="window.print();">' + this.printLinkText + '</a>',
                '<a class="' + Ext.baseCSSPrefix + 'ux-grid-printer-linkclose" href="javascript:void(0);" onclick="window.close();">' + this.closeLinkText + '</a>',
                '</div>',

                '<h1>' + this.mainTitle + '</h1>',

                //Customize here
                contentNew,
                '<br>' +
                 '<br>' +
                //End Customize here

                '</body>',
                '</html>'
            ];

            var html = Ext.create('Ext.XTemplate', htmlMarkupCustom).apply([]),
                win  = window.open('', '_blank', "height='100%',width='100%',status=yes,toolbar=no,menubar=yes,location=no,scrollbars=yes");
            win.document.open();
            win.document.write(html);
            win.document.close();

            if (this.printAutomatically){
                win.print();
            }

            //Another way to set the closing of the main
            if (this.closeAutomaticallyAfterPrint){
                if(Ext.isIE){
                    window.close();
                } else {
                    win.close();
                }
            }
        },

        /**
         * @property stylesheetPath
         * @type String
         * The path at which the print stylesheet can be found (defaults to 'ux/grid/gridPrinterCss/print.css')
         */
        stylesheetPath: null,

        /**
         * @property printAutomatically
         * @type Boolean
         * True to open the print dialog automatically and close the window after printing. False to simply open the print version
         * of the grid (defaults to false)
         */
        printAutomatically: false,

        /**
         * @property closeAutomaticallyAfterPrint
         * @type Boolean
         * True to close the window automatically after printing.
         * (defaults to false)
         */
        closeAutomaticallyAfterPrint: false,

        /**
         * @property mainTitle
         * @type String
         * Title to be used on top of the table
         * (defaults to empty)
         */
        mainTitle: '',

        /**
         * Text show on print link
         * @type String
         */
        printLinkText: 'Print',

        /**
         * Text show on close link
         * @type String
         */
        closeLinkText: 'Close',

        /**
         * @property headerTpl
         * @type {Object/Array} values
         * The markup used to create the headings row. By default this just uses <th> elements, override to provide your own
         */
        headerTpl: [
            '<tpl for=".">',
            '<th>{text}</th>',
            '</tpl>'
        ],

        /**
         * @property bodyTpl
         * @type {Object/Array} values
         * The XTemplate used to create each row. This is used inside the 'print' function to build another XTemplate, to which the data
         * are then applied (see the escaped dataIndex attribute here - this ends up as "{dataIndex}")
         */
        bodyTpl: [
            '<tpl for=".">',
            '<td>\{{[Ext.String.createVarName(values.text)]}\}</td>',
            '</tpl>'
        ]
    }
});
