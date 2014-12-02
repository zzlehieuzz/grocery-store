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
    create : function (){
        var objectField = [{name: 'name',   type: 'string'},
                           {name: 'input',  type: 'int'},
                           {name: 'output', type: 'int'},
                           {name: 'remain', type: 'int'}];

        MyUtil.Object.defineModel('Revenue', objectField);

        var storeReportRevenueLoadJson = new Ext.data.JsonStore({
            model: 'Revenue',
            proxy: new Ext.data.HttpProxy({
                url: MyUtil.Path.getPathAction("Report_RevenueLoad"),
                reader: {
                    type: 'json',
                    root: 'data',
                    getData: function(data){
                        return Object.keys(data.data).map(function (key) {return data.data[key]});
                    }
                }
            }),
            autoLoad: true
        });

        storeReportRevenueLoadJson.on('beforeload', function() {
            this.proxy.extraParams = {fromYear : Ext.ComponentQuery.query('[name=reportRevenueFromDate]', new SrcPageUrl.Report.Revenue().createTbar())[0].getSubmitValue()};
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
                    rotation:{degrees: 340},
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
                label : {rotation:{degrees:320}}
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
                labelWidth: 30,
                fieldLabel: 'year'.Translator('Invoice'),
                xtype: 'datefield',
                width: 90,
                padding: '0 0 0 10px;',
                format: dateFormatY,
                submitFormat: dateFormatY,
                value: Ext.Date.format(new Date(), dateFormatY)
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

