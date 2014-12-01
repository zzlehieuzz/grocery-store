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
        },

        initialize: function() {
            this.store1();
        }
    },
    constructor: function (config) {
        this.store1 = Ext.create('Ext.data.JsonStore', {
            fields: ['name', 'input', 'data2', 'data3'],
            data: this.self.getDummyData()
        });
console.log(this.self.getDummyData());
        var readerJson = {
            type: 'json',
            root: 'data'
        };

        var objectField = [{name: 'name',  type: 'string'},
                           {name: 'input', type: 'int'},
                           {name: 'output', type: 'int'},
                           {name: 'remain', type: 'int'}];

        MyUtil.Object.defineModel('Revenue', objectField);

        this.storeReportRevenueLoad = new Ext.data.JsonStore({
            model: 'Revenue',
            proxy: new Ext.data.HttpProxy({
                url: MyUtil.Path.getPathAction("Report_RevenueLoad"),
                reader: readerJson
            }),
            autoLoad: true
        });
    },
    create : function (){
        //this.storeReportRevenueLoad.on('beforeload', function() {
        //    this.proxy.extraParams = {fromDate : Ext.ComponentQuery.query('[name=reportRevenueFromDate]', this.tBarRevenue)[0].getSubmitValue(),
        //                              toDate   : Ext.ComponentQuery.query('[name=reportRevenueToDate]', this.tBarRevenue)[0].getSubmitValue()};
        //});

        var chart = Ext.create('Ext.chart.Chart', {
            //style: 'background:#fff',
            animate: true,
            shadow: true,
            store: this.storeReportRevenueLoad,
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
    }
});

