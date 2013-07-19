function formatNumber(nStr, million)
{
    if (million) {
       nStr = nStr.toFixed(2);
    }
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

function convertLabelTime(timeMinutes) {
    timeSec = parseFloat(timeMinutes * 60);

    timeHrs = Math.floor(timeSec / 3600);
    timeMin = Math.floor((timeSec - (timeHrs * 3600)) / 60);

    return formatNumber(timeHrs, false) + ' H, ' + timeMin + ' min';
}

Ext.onReady(function() {
    storeSlaDash = new Ext.data.JsonStore({
        proxy: new Ext.data.HttpProxy({
            method: 'POST',
            url: 'controllers/slaProxy.php'
        }),
        root: 'data',
        autoDestroy: true,
        totalProperty: 'total',
        baseParams: {
            functionExecute: 'dashletSla'
        },
        //fields: ['SLA_NAME','NUM_CASES','TOTAL_EXCEEDED','AVG_EXCEEDED','PENALTY']
        fields: [{
            name: 'SLA_UID',
            type: 'string'
        }, ,
        {
            name: 'SLA_NAME',
            type: 'string'
        }, {
            name: 'SUM_DURATION',
            type: 'float'
        }, {
            name: 'SUM_EXCEEDED',
            type: 'float'
        }, {
            name: 'AVG_SLA',
            type: 'float'
        }, {
            name: 'SUM_PEN_VALUE',
            type: 'float'
        }, {
            name: 'SLA_PEN_VALUE_UNIT',
            type: 'string'
        }]
    });
    storeSlaDash.load();

    gridDashletSLA = new Ext.grid.GridPanel({
        id: 'gridLisSLA',
        store: storeSlaDash,
        margins: '0 0 0 0',
        border: true,
        title: '<span style="font-size: 10px;">' + _TRANS("ID_SLA_SUMMARY") + '</span>',
        loadMask: true,
        cm: new Ext.grid.ColumnModel({
            defaults: {
                width: 20,
                sortable: true
            },
            columns: [{
                header: '<span style="font-size: 9px;">' + _TRANS("ID_SLA") + ' </span>',
                width: 8,
                sortable: true,
                dataIndex: 'SLA_NAME'
            },

            {
                header: '<span style="font-size: 9px;">' + _TRANS("ID_TIMES_EXECUTED") + ' </span>',
                width: 8,
                sortable: true,
                dataIndex: 'SUM_DURATION',
                align: 'right',
                renderer: function(v) {
                    return (v > 1) ? formatNumber(v, false) + ' Cases' : '1 Case';
                }
            }, {
                header: '<span style="font-size: 9px;">' + _TRANS("ID_TIME_EXCEEDED") + ' </span>',
                width: 8,
                sortable: true,
                dataIndex: 'SUM_EXCEEDED',
                align: 'right',
                renderer: function(v) {
                    return convertLabelTime(v);
                }
            }, {
                header: '<span style="font-size: 9px;">' + _TRANS("ID_AVERAGE_EXCEED") + '</span>',
                width: 8,
                sortable: true,
                dataIndex: 'AVG_SLA',
                align: 'right',
                renderer: function(v) {
                    return convertLabelTime(v.toFixed(2));
                }
            },

            {
                header: '<span style="font-size: 9px;">' + _TRANS("ID_PENALTY") + ' </span>',
                width: 6,
                sortable: true,
                dataIndex: 'SUM_PEN_VALUE',
                align: 'right',
                renderer: function(v, param, data) {
                    return formatNumber(v, true) + ' ' + data.data.SLA_PEN_VALUE_UNIT;
                }
            }]
        }),
        autoShow: true,
        autoFill: true,
        nocache: true,
        autoWidth: true,
        stripeRows: true,
        stateful: true,
        animCollapse: true,
        viewConfig: {
            forceFit: true,
            scrollOffset: 2,
            emptyText: '<div align="center"><b> ' + _TRANS("ID_NO_SLA_SHOW") + ' </b></div>'
        }
    });


    new Ext.Viewport({
        layout: 'fit',
        autoScroll: true,
        border: false,
        items: [gridDashletSLA]
    });
});