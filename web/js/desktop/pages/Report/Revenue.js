/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'data',
    id  : 'id',
    totalProperty: 'total'
};

var objectField = [{name: 'id',             type: 'int'},
                   {name: 'name',           type: 'string'},
                   {name: 'remainQuantity', type: 'int'},
                   {name: 'inputQuantity',  type: 'int'},
                   {name: 'outputQuantity', type: 'int'}];

MyUtil.Object.defineModel('Product', objectField);



Ext.define('SrcPageUrl.Report.Revenue', {
    requires: [
        'Ext.chart.*',
        'Ext.data.*',
        'Ext.util.Format',
        'Ext.layout.container.Fit'
    ],

    create : function (){
        //storeLoadProduct.on('beforeload', function() {
        //    this.proxy.extraParams = {fromDate : Ext.ComponentQuery.query('[name=reportRevenueFromDate]', tBar)[0].getSubmitValue(),
        //                              toDate   : Ext.ComponentQuery.query('[name=reportRevenueToDate]', tBar)[0].getSubmitValue()};
        //});
        var store = Ext.create('Ext.data.JsonStore', {
            fields: ['month', 'comedy', 'action', 'drama', 'thriller'],
            data: [
                {month: 1, comedy: 34000000, action: 23890000, drama: 18450000, thriller: 20060000},
                {month: 2, comedy: 56703000, action: 38900000, drama: 12650000, thriller: 21000000},
                {month: 3, comedy: 42100000, action: 50410000, drama: 25780000, thriller: 23040000},
                {month: 4, comedy: 42100000, action: 50410000, drama: 25780000, thriller: 23040000},
                {month: 5, comedy: 42100000, action: 50410000, drama: 25780000, thriller: 23040000},
                {month: 6, comedy: 42100000, action: 50410000, drama: 25780000, thriller: 23040000},
                {month: 7, comedy: 42100000, action: 50410000, drama: 25780000, thriller: 23040000},
                {month: 8, comedy: 42100000, action: 50410000, drama: 25780000, thriller: 23040000},
                {month: 9, comedy: 42100000, action: 50410000, drama: 25780000, thriller: 23040000},
                {month: 10, comedy: 42100000, action: 50410000, drama: 25780000, thriller: 23040000},
                {month: 11, comedy: 42100000, action: 50410000, drama: 25780000, thriller: 23040000},
                {month: 12, comedy: 38910000, action: 56070000, drama: 24810000, thriller: 26940000}
            ]
        });

        var chart = Ext.create('Ext.chart.Chart',{
            animate: true,
            shadow: true,
            store: store,
            legend: {
                position: 'right'
            },
            axes: [{
                type: 'Numeric',
                position: 'bottom',
                fields: ['comedy', 'action', 'drama', 'thriller'],
                title: false,
                grid: true,
                label: {
                    renderer: function(v) {
                        return String(v).replace(/(.)00000$/, '.$1 VND');
                    }
                }
            }, {
                type: 'Category',
                position: 'left',
                fields: ['month'],
                title: false
            }],
            series: [{
                type: 'bar',
                axis: 'top',
                gutter: 80,
                xField: 'month',
                yField: ['comedy', 'action', 'drama', 'thriller'],
                stacked: true,
                tips: {
                    trackMouse: true,
                    width: 65,
                    height: 28,
                    renderer: function(storeItem, item) {
                        this.setTitle(String(item.value[1] / 1000000) + 'VND');
                    }
                }
            }]
        });

        return chart;
    }
});

