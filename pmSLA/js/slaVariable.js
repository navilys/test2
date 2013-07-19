var dataVariable = [
    ['@@', 'Replace the value in quotes'],
    ['@#', 'Replace the value converted to float'],
    ['@%', 'Replace the value converted to integer'],
    ['@?', 'Replace the value with URL encoding'],
    ['@$', 'Replace the value for use in SQL sentences'],
    ['@=', 'Replace the value without changes']
];

var storeVarible = new Ext.data.ArrayStore({
    fields: [{
        name: _TRANS("ID_VARIABLE")
    }, {
        name: _TRANS("ID_DESCRIPTION")
    }]
});
storeVarible.loadData(dataVariable);

var gridVariable = new Ext.grid.GridPanel({
    store: storeVarible,
    columns: [{
        id: 'variable',
        header: _TRANS("ID_VARIABLE"),
        width: 60,
        sortable: true
    }, {
        id: 'description',
        header: _TRANS("ID_DESCRIPTION"),
        width: 240,
        sortable: true
    }],
    stripeRows: true,

    height: 180,
    width: 310,
    title: _TRANS("ID_VARIABLES_PREFIX"),
    stateful: true,
    stateId: 'grid'
});

var storeAllVariables = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        method: 'POST',
        url: 'controllers/variablesProxy.php'
    }),
    root: 'data',
    autoDestroy: true,

    baseParams: {
        option: 'allVariables'
    },
    fields: [{
        name: 'sName',
        type: 'string'
    }, {
        name: 'sLabel',
        type: 'string'
    }]
});

var gridAllVariables = new Ext.grid.GridPanel({
    id: 'gridAllVariables',
    store: storeAllVariables,
    margins: '0 0 0 0',
    border: true,
    title: _TRANS("ID_ALL_VARIABLES"),
    loadMask: true,
    autoShow: true,
    autoFill: true,
    nocache: true,
    autoWidth: true,
    stripeRows: true,
    stateful: true,
    height: 180,
    width: 310,
    cm: new Ext.grid.ColumnModel({
        defaults: {
            width: 20,
            sortable: true
        },
        columns: [{
            header: _TRANS("ID_VARIABLE"),
            width: 8,
            sortable: true,
            dataIndex: 'sName',
            renderer: function(v) {
                return "@@" + v;
            }
        }, {
            header: _TRANS("ID_LABEL"),
            width: 8,
            sortable: true,
            dataIndex: 'sLabel'
        } ]
    }),
    viewConfig: {
        forceFit: true,
        scrollOffset: 2,
        emptyText: '<div align="center"><b> ' + _TRANS("ID_NO_LIST_VAR") + ' </b></div>'
    },
    listeners: {
        rowdblclick: function() {
            rowSelected = gridAllVariables.getSelectionModel().getSelected();
            if (rowSelected) {
                txtareaConditionSla.setValue(txtareaConditionSla.getValue() + '@@' + rowSelected.data.sName);
                windowsFormVariable.hide();
            }
        }
    }
});

var tabAllVariables = new Ext.Panel({
    title: _TRANS("ID_ALL_VARIABLES"),
    autoScroll: true,
    layout: 'fit',
    items: [gridAllVariables],
    viewConfig: {
        forceFit: true
    },
    hidden: true,
    hideLabel: true
});

var storeSystemList = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        method: 'POST',
        url: 'controllers/variablesProxy.php'
    }),
    root: 'data',
    autoDestroy: true,
    baseParams: {
        option: 'system'
    },
    fields: [{
        name: 'sName',
        type: 'string'
    }, {
        name: 'sLabel',
        type: 'string'
    }]
});

var gridSystem = new Ext.grid.GridPanel({
    id: 'gridSystem',
    store: storeSystemList,
    margins: '0 0 0 0',
    border: true,
    title: _TRANS("ID_SYSTEM"),
    loadMask: true,
    autoShow: true,
    autoFill: true,
    nocache: true,
    autoWidth: true,
    stripeRows: true,
    stateful: true,
    height: 180,
    width: 310,
    cm: new Ext.grid.ColumnModel({
        defaults: {
            width: 20,
            sortable: true
        },
        columns: [{
            header: _TRANS("ID_VARIABLE"),
            width: 8,
            sortable: true,
            dataIndex: 'sName',
            renderer: function(v) {
                return "@@" + v;
            }
        }, {
            header: _TRANS("ID_LABEL"),
            width: 8,
            sortable: true,
            dataIndex: 'sLabel'
        } ]
    }),
    viewConfig: {
        forceFit: true,
        scrollOffset: 2,
        emptyText: '<div align="center"><b> ' + _TRANS("ID_NO_LIST_VAR") + ' </b></div>'
    },
    listeners: {
        rowdblclick: function() {
            rowSelected = gridSystem.getSelectionModel().getSelected();
            if (rowSelected) {
                txtareaConditionSla.setValue(txtareaConditionSla.getValue() + '@@' + rowSelected.data.sName);
                windowsFormVariable.hide();
            }
        }
    }
});

var tabSystem = new Ext.Panel({
    title: _TRANS("ID_SYSTEM"),
    autoWidth: true,
    layout: 'fit',
    defaults: {
        flex: 1
    },
    layoutConfig: {
        align: 'stretch'
    },
    items: [gridSystem],
    viewConfig: {
        forceFit: true
    }
});

var storeProcessList = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        method: 'POST',
        url: 'controllers/variablesProxy.php'
    }),
    root: 'data',
    autoDestroy: true,
    //totalProperty: 'total',
    baseParams: {
        option: 'process'
    },
    fields: [{
        name: 'sName',
        type: 'string'
    }, {
        name: 'sLabel',
        type: 'string'
    }]
});

var gridProcess = new Ext.grid.GridPanel({
    id: 'gridProcess',
    store: storeProcessList,
    margins: '0 0 0 0',
    border: true,
    title: _TRANS("ID_PROCESS"),
    loadMask: true,
    autoShow: true,
    autoFill: true,
    nocache: true,
    autoWidth: true,
    stripeRows: true,
    stateful: true,
    height: 180,
    width: 310,
    cm: new Ext.grid.ColumnModel({
        defaults: {
            width: 20,
            sortable: true
        },
        columns: [{
            header: _TRANS("ID_VARIABLE"),
            width: 8,
            sortable: true,
            dataIndex: 'sName',
            renderer: function(v) {
                return "@@" + v;
            }
        }, {
            header: _TRANS("ID_LABEL"),
            width: 8,
            sortable: true,
            dataIndex: 'sLabel'
        } ]
    }),
    viewConfig: {
        forceFit: true,
        scrollOffset: 2,
        emptyText: '<div align="center"><b> ' + _TRANS("ID_NO_LIST_VAR") + ' </b></div>'
    },
    listeners: {
        rowdblclick: function() {
            rowSelected = gridProcess.getSelectionModel().getSelected();
            if (rowSelected) {
                txtareaConditionSla.setValue(txtareaConditionSla.getValue() + '@@' + rowSelected.data.sName);
                windowsFormVariable.hide();
            }
        }
    }
});

var tabProcess = new Ext.Panel({
    title: _TRANS("ID_PROCESS"),
    autoWidth: true,
    layout: 'fit',
    defaults: {
        flex: 1
    },
    layoutConfig: {
        align: 'stretch'
    },
    items: [gridProcess],
    viewConfig: {
        forceFit: true
    }

});

var tabsPanelVariable = {
    region: 'center',
    activeTab: 0
};

tabsPanelVariable.items = []; // new Array();
tabsPanelVariable.items.push(tabAllVariables);
tabsPanelVariable.items.push(tabSystem);
tabsPanelVariable.items.push(tabProcess);
var tabsPanel = new Ext.TabPanel(tabsPanelVariable);


var formVariable = new Ext.FormPanel({
    labelWidth: 80,
    frame: true,
    autoWidth: true,
    autoScroll: true,
    bodyStyle: 'padding:5px 5px 0',
    items: [gridVariable, tabsPanel]
});


var windowsFormVariable = new Ext.Window({
    layout: 'fit',
    title: _TRANS("ID_VARIABLE"),
    width: 350,
    height: 450,
    plain: true,
    modal: true,
    closeAction: 'hide',
    hideMode: 'offsets',
    items: [formVariable]
});
