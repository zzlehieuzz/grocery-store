/*
 * @author HieuNLD 2014/06/27
 */
Ext.define('SrcPageUrl.Report.Revenue', {
    requires: [
        'Ext.chart.*',
        'Ext.data.*',
        'Ext.util.Format',
        'Ext.layout.container.Fit'
    ],
    statics: {
        getDummyData: function () {
            var data = [], i, n;

            for (i = 0; i < (n || 12); i++) {
                data.push({
                    name: Ext.Date.monthNames[i % 12],
                    'data1':100000000, 'data2':500050000, 'data3':(500050000-100000000), 'text': 'text' + i
                });
            }

            return data;
        }
    },
    create : function (){
        //this.storeReportRevenueLoad.on('beforeload', function() {
        //    this.proxy.extraParams = {fromDate : Ext.ComponentQuery.query('[name=reportRevenueFromDate]', this.tBarRevenue)[0].getSubmitValue(),
        //                              toDate   : Ext.ComponentQuery.query('[name=reportRevenueToDate]', this.tBarRevenue)[0].getSubmitValue()};
        //});

        var objectField = [{name: 'name',   type: 'string'},
                           {name: 'input',  type: 'int'},
                           {name: 'output', type: 'int'},
                           {name: 'remain', type: 'int'}];

        MyUtil.Object.defineModel('Revenue', objectField);
        var modelFields = [];

        var storeReportRevenueLoadJson = new Ext.data.JsonStore({
            model: 'Revenue',
            proxy: new Ext.data.HttpProxy({
                url: MyUtil.Path.getPathAction("Report_RevenueLoad"),
                reader: {
                    type: 'json',
                    root: 'data',
                    getData: function(data){
                        Ext.each(data, function(rec) {
                            modelFields.push(rec.data);
                        });

                        console.log(modelFields);
                        return modelFields;
                    }
                }
            }),
            autoLoad: true
        });

        //Ext.each(storeReportRevenueLoadJson.getStore(), function (rec) {
        //    modelFields.push(rec);
        //});
        console.log(modelFields);
        console.log(SrcPageUrl.Report.Revenue.getDummyData());
        var storeReportRevenueLoadArray = Ext.create('Ext.data.JsonStore', {
            fields: ['name', 'input', 'output', 'remain'],
            data: modelFields
        });



        var chart = Ext.create('Ext.chart.Chart', {
            //style: 'background:#fff',
            animate: true,
            shadow: true,
            store: storeReportRevenueLoadJson,
            legend: {
                position: 'right'
            },
            axes: [{
                type: 'Numeric',
                position: 'bottom',
                fields: ['input', 'output', 'remain'],
                minimum: 0,
                label : {
                    rotation:{degrees:270},
                    renderer: function(value){
                        return Ext.util.Format.currency(value, ' ', decimalPrecision)
                    }
                }
            }, {
                type: 'Category',
                position: 'left',
                fields: ['name'],
                grid: true,
                title: 'month of the year'.Translator('Report'),
                label : {
                    rotation:{degrees:320}
                }
            }],
            series: [{
                type: 'bar',
                axis: 'bottom',
                xField: 'name',
                yField: ['input', 'output', 'remain'],
                title: ['input'.Translator('Report'), 'output'.Translator('Report'), 'profit'.Translator('Report')],
                stacked: true,
                theme: 'Base:gradients',
                showInLegend: true,
                donut: true,
                label: {
                    display: 'insideEnd',
                    contrast: true,
                    font: '12px Times',
                    field: ['input', 'output', 'remain'],
                    renderer: function(value){
                        return Ext.util.Format.currency(value, ' ', decimalPrecision)
                    },
                    orientation: 'horizontal',
                    color: '#333'
                },
                tips: {
                    trackMouse: true,
                    width: 150,
                    height: 20,
                    renderer: function(storeItem, item) {
                        this.setTitle(String(Ext.util.Format.currency(item.value[1], ' ', decimalPrecision)) + ' VND');
                    }
                }
            }]
        });

        return chart;
    },
    createTbar : function (){
        return new Ext.Toolbar({
            items: [{
                name: 'reportRevenueFromDate',
                labelWidth: 50,
                fieldLabel: 'from date'.Translator('Invoice'),
                xtype: 'datefield',
                width: 165,
                padding: '0 0 0 10px;',
                format: dateFormat,
                submitFormat: dateSubmitFormat,
                value: Ext.Date.format(new Date(), firstDateFormat)
            }, {
                name: 'reportRevenueToDate',
                fieldLabel: '~',
                labelWidth: 5,
                labelSeparator: '',
                xtype: 'datefield',
                width: 120,
                format: dateFormat,
                submitFormat: dateSubmitFormat,
                value: Ext.Date.format(new Date(), lastDateFormat)
            }, '->', {
                text: 'find'.Translator('Common'),
                tooltip: 'find'.Translator('Common'),
                iconCls: 'find',
                listeners: {
                    click: function () {
                        this.storeReportRevenueLoad.reload();
                    }
                }
            }]
        });
    }
});

