var secondReportData = new Array();
var thirdReportData = new Array();

function errorExport (error) {
    Ext.MessageBox.show({
        title: _TRANS("ID_ERROR_EXPORT_TITLE"),
        msg: _TRANS("ID_ERROR_EXPORT"),
        buttons: Ext.MessageBox.OK,
        animEl: 'mb9',
        icon: Ext.MessageBox.ERROR
    });
}

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

function exportReport(typeExport, typeReport) {
    var con = 0;
    var col = new Array();

    switch ( typeReport )
    {
        case 'firstReport':
            var columns = gridFirstReport.colModel.columns;
        break;
        case 'secondReport':
            var columns = gridSecondReport.colModel.columns;
        break;
        case 'thirdReport':
            var columns = gridReportCase.colModel.columns;
        break;
    }

    for (var i = 0; columns.length > i; i++) {
        if (columns[i].hidden != true) {
            colAux = new Array();
            colAux = {
                'HEADER' : columns[i].header,
                'DATAINDEX' : columns[i].dataIndex
            };
            col.push(colAux);
        }
    }

    col = Ext.encode(col);
    col = col.replace(/#/, "(numeral)")

    var pageReportExport = 'slaExportReport.php?';

    switch ( typeReport )
    {
        case 'firstReport':
            pageReportExport += '&SLA_UID=' + comboSla.getValue();
            pageReportExport += '&DATE_START=' + dateStart.getValue();
            pageReportExport += '&DATE_END=' + dateEnd.getValue();
            pageReportExport += '&TYPE_EXCEEDED=' + comboExceeded.getValue();
            pageReportExport += '&EXC_NUMBER=' + numberDuration.getValue();
            pageReportExport += '&EXC_DURATION_TYPE=' + comboDuration.getValue();
            pageReportExport += '&EXC_STATUS=' + comboTypeCases.getValue();
        break;
        case 'secondReport':
            pageReportExport += '&SLA_UID=' + secondReportData['SLA_UID'];
            pageReportExport += '&SLA_NAME=' + secondReportData['SLA_NAME'];
            pageReportExport += '&DATE_START=' + secondReportData['DATE_START'];
            pageReportExport += '&DATE_END=' + secondReportData['DATE_END'];
            pageReportExport += '&TYPE_EXCEEDED=' + secondReportData['TYPE_EXCEEDED'];
            pageReportExport += '&EXC_NUMBER=' + secondReportData['EXC_NUMBER'];
            pageReportExport += '&EXC_DURATION_TYPE=' + secondReportData['EXC_DURATION_TYPE'];
            pageReportExport += '&EXC_STATUS=' + secondReportData['EXC_STATUS'];
        break;
        case 'thirdReport':
            pageReportExport += '&SLA=' + textSla.text;
            pageReportExport += '&PRO=' + textProcess.text;
            pageReportExport += '&TYP=' + textTypeSla.text;
            pageReportExport += '&TAS=' + textTasks.text;

            pageReportExport += '&CAS=' + textCasesNumber.text;
            pageReportExport += '&DUR=' + textDuration.text;
            pageReportExport += '&DUREXC=' + textDurationExceeded.text;
            pageReportExport += '&PEN=' + textPenalty.text;

            pageReportExport += '&APP_UID=' + thirdReportData['APP_UID'];
            pageReportExport += '&SLA_UID=' + thirdReportData['SLA_UID'];
            pageReportExport += '&APP_NUMBER=' + thirdReportData['APP_NUMBER'];
        break;
    }

    pageReportExport += '&DAT_REP=' + Ext.getCmp('executeCron1').text;
    pageReportExport += '&COLUMNS=' + Ext.encode(col);
    pageReportExport += '&TYPE_REPORT=' + typeReport;
    pageReportExport += '&TYPE_EXPORT=' + typeExport;

    document.getElementById('exportReport').src = pageReportExport;
}

function convertLabelTime(timeMinutes) {
    timeSec = parseFloat(timeMinutes * 60);

    timeHrs = Math.floor(timeSec / 3600);
    timeMin = Math.floor((timeSec - (timeHrs * 3600)) / 60);

    return formatNumber(timeHrs, false) + ' H, ' + timeMin + ' min';
}

var buttonFilters = new Ext.Button({
    id:'button1',
    text: _TRANS("ID_FILTERS"),
    scale: 'medium',
    enableToggle: true,
    toggleGroup: 'buttonsReport',
    toggleHandler: function() {
        if (typeof(formReport.getLayout().setActiveItem) == 'function') {
            formReport.getLayout().setActiveItem(0);
        }
    }
});

var buttonReportSlas = new Ext.Button({
    id:'button2',
    allowDepress: true,
    text: _TRANS("ID_SLA_SUMMARY"),
    scale: 'medium',
    anchor: '30%',
    enableToggle: true,
    toggleGroup: 'buttonsReport',
    toggleHandler: function() {
        if (typeof(formReport.getLayout().setActiveItem) == 'function') {
            formReport.getLayout().setActiveItem(1);
        }
    },
    hidden: true
});

var buttonReportCases = new Ext.Button({
    id:'button3',
    allowDepress: true,
    text: "",
    scale: 'medium',
    anchor: '30%',
    enableToggle: true,
    toggleGroup: 'buttonsReport',
    toggleHandler: function() {
        if (typeof(formReport.getLayout().setActiveItem) == 'function') {
            formReport.getLayout().setActiveItem(2);
        }
    },
    hidden: true
});

var buttonReportHistory = new Ext.Button({
    id:'button4',
    allowDepress: true,
    text: "",
    scale: 'medium',
    anchor: '30%',
    enableToggle: true,
    toggleGroup: 'buttonsReport',
    toggleHandler: function() {
        if (typeof(formReport.getLayout().setActiveItem) == 'function') {
            formReport.getLayout().setActiveItem(3);
        }
    },
    hidden: true
});

var formReport = new Ext.FormPanel({
    layout: "card",
    border: true,
    activeItem: panelReportFilter,
    tbar: [buttonFilters, buttonReportSlas, buttonReportCases, buttonReportHistory],
    items: [panelReportFilter, gridFirstReport, gridSecondReport, formReportCase],
    listeners: {
        afterrender: function(ob1, ob2, ob3) {
            numberDuration.hide();
            comboDuration.hide();

            dateStart.hide();
            Ext.getCmp('label_and').hide();
            dateEnd.hide();
        }
    }
});

Ext.onReady(function() {
    
    buttonFilters.toggle();

    new Ext.Viewport({
        layout: 'fit',
        border: false,
        items: [formReport]
    });

    Ext.getCmp('executeCron1').setText(_TRANS("ID_REPORT_GENERATED_ON") + ' : ' + timeCron);
    Ext.getCmp('executeCron2').setText(_TRANS("ID_REPORT_GENERATED_ON") + ' : ' + timeCron);
    Ext.getCmp('executeCron3').setText(_TRANS("ID_REPORT_GENERATED_ON") + ' : ' + timeCron);
    
    document.getElementById('button1').style.width = "150px";
    document.getElementById('button2').style.width = "150px";
    document.getElementById('button3').style.width = "150px";
    document.getElementById('button4').style.width = "150px";
});