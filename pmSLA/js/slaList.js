function openFormSla (dataSla)
{
    if (typeof(dataSla) == 'undefined') {
        valuesData.VAL_ID_SLA = 'new';
        valuesData.VAL_PROCESS_SLA = '';
        valuesData.VAL_NAME_SLA = '';
        valuesData.VAL_DESCRIPTION_SLA = '';
        valuesData.VAL_TYPE_SLA = 'PROCESS';
        valuesData.VAL_TASK_START_SLA = '';
        valuesData.VAL_NAME_SLA = '';
        valuesData.VAL_TASK_END_SLA = '';
        valuesData.VAL_DURATION_NUMBER_SLA = '1';
        valuesData.VAL_DURATION_TYPE_SLA = 'HOURS';
        valuesData.VAL_CONDITION_SLA = '';
        valuesData.VAL_SLA_PEN_ENABLED = '1';
        valuesData.VAL_PENALITY_TIME_NUMBER_SLA = '1';
        valuesData.VAL_PENALITY_UNIT_TYPE_SLA = '$us';
        valuesData.VAL_PENALTY_UNIT_SLA = '';
        valuesData.VAL_PENALITY_VALUE_NUMBER_SLA = '1';
        valuesData.VAL_PENALITY_VALUE_TYPE_SLA = 'HOURS';
        valuesData.VAL_STATUS_SLA = '1';
        valuesData.VAL_TYPE_FORM = 'NEW';
    }
    comboProcess.setValue(valuesData.VAL_PROCESS_SLA);
    textNameSla.setValue(valuesData.VAL_NAME_SLA);
    txtareaDescriptionSla.setValue(valuesData.VAL_DESCRIPTION_SLA);
    comboStatusSla.setValue(valuesData.VAL_STATUS_SLA);
    comboTypeSla.setValue(valuesData.VAL_TYPE_SLA);
    comboTaskStartSla.setValue(valuesData.VAL_TASK_START_SLA);
    comboTaskEndSla.setValue(valuesData.VAL_TASK_END_SLA);
    numberDurationSla.setValue(valuesData.VAL_DURATION_NUMBER_SLA);
    comboDurationSla.setValue(valuesData.VAL_DURATION_TYPE_SLA);


    fieldsPenaltys.collapse();
    txtareaConditionSla.setValue(valuesData.VAL_CONDITION_SLA);
    numberPenaltyTimeSla.setValue(valuesData.VAL_PENALITY_TIME_NUMBER_SLA);
    comboPenaltyUnitSla.setValue(valuesData.VAL_PENALITY_UNIT_TYPE_SLA);
    numberPenaltyValueSla.setValue(valuesData.VAL_PENALITY_VALUE_NUMBER_SLA);
    comboPenaltyValueSla.setValue(valuesData.VAL_PENALITY_VALUE_TYPE_SLA);

    fieldsTasks.hide();

    changeSizeForm(DISPLAY_NORMAL);
    comboTypeSla.setRawValue(_TRANS("ID_ENTIRE_PROCESS"));

    storeProcess.sort('PRO_TITLE','ASC');
    windowsFormSla.show();
    saveButton.disable();
    windowsFormSla.setTitle(_TRANS("ID_NEW_SLA"));
}

EditSla = function()
{
    rowSelected = gridLisSLA.getSelectionModel().getSelected();
    if (rowSelected) {
        Ext.Ajax.request({
            url: 'controllers/slaProxy.php',
            params: {
                    functionExecute: 'loadSla',
                    SLA_UID: rowSelected.data.SLA_UID
                    },
            success: function (resp) {
                var data;
                data = Ext.decode(resp.responseText);
                if (data.success === true) {
                    valuesData.VAL_TYPE_SLA = data.data.SLA_TYPE;
                    valuesData.VAL_TASK_START_SLA = data.data.SLA_TAS_START;
                    valuesData.VAL_TASK_END_SLA = data.data.SLA_TAS_END;
                    valuesData.VAL_DURATION_NUMBER_SLA = data.data.SLA_TIME_DURATION;
                    valuesData.VAL_DURATION_TYPE_SLA = data.data.SLA_TIME_DURATION_MODE;
                    valuesData.VAL_CONDITION_SLA = data.data.SLA_CONDITIONS;
                    valuesData.VAL_PENALITY_TIME_NUMBER_SLA = data.data.SLA_PEN_TIME;
                    valuesData.VAL_PENALITY_UNIT_TYPE_SLA = data.data.SLA_PEN_VALUE_UNIT;
                    valuesData.VAL_PENALITY_VALUE_NUMBER_SLA = data.data.SLA_PEN_VALUE;
                    valuesData.VAL_PENALITY_VALUE_TYPE_SLA = data.data.SLA_PEN_TIME_MODE;
                    valuesData.VAL_SLA_PEN_ENABLED = data.data.SLA_PEN_ENABLED;
                    hdnUidSla.setValue(rowSelected.data.SLA_UID);
                    storeProcess.load();
                    storeProcess.sort('PRO_TITLE','ASC');
                    comboProcess.store.on('load',function(store) {
                        comboProcess.setValue(rowSelected.data.PRO_UID);
                    });

                    checkboxReload.setVisible(true);
                    checkboxReload.setValue(false);
                    textNameSla.setValue(rowSelected.data.SLA_NAME);
                    txtareaDescriptionSla.setValue(rowSelected.data.SLA_DESCRIPTION);

                    comboStatusSla.store.on('load',function(store) {
                        if (rowSelected.data.SLA_STATUS == 'ACTIVE') {
                            comboStatusSla.setValue(1);
                        } else {
                            comboStatusSla.setValue(0);
                        }
                    });

                    comboTypeSla.setValue(valuesData.VAL_TYPE_SLA);

                    var selVal;
                    var selVal2;
                    switch(valuesData.VAL_TYPE_SLA) {
                        case 'PROCESS':
                            fieldsTasks.hide();
                            comboTypeSla.setRawValue(_TRANS("ID_ENTIRE_PROCESS"));
                            break;
                        case 'RANGE':
                            Ext.getCmp('labelTo').show();
                            comboTaskEndSla.show();
                            fieldsTasks.show();
                            comboTypeSla.setRawValue(_TRANS("ID_MULTIPLE_TASKS"));
                            selVal = storeTaskStart.setBaseParam('PRO_UID', rowSelected.data.PRO_UID);
                            storeTaskStart.reload({params: {PRO_UID: selVal}});

                            selVal2 = storeTaskEnd.setBaseParam('PRO_UID', rowSelected.data.PRO_UID);
                            storeTaskEnd.reload({params: {PRO_UID: selVal2}});

                            comboTaskStartSla.store.on('load',function(store) {
                                comboTaskStartSla.setValue(valuesData.VAL_TASK_START_SLA);
                            });

                            comboTaskEndSla.store.on('load',function(store) {
                                comboTaskEndSla.setValue(valuesData.VAL_TASK_END_SLA);
                            });

                            break;
                        case 'TASK':
                            Ext.getCmp('labelTo').hide();
                            comboTaskEndSla.hide();
                            fieldsTasks.show();
                            comboTypeSla.setRawValue(_TRANS("ID_TASK"));
                            selVal = storeTaskStart.setBaseParam('PRO_UID', rowSelected.data.PRO_UID);
                            storeTaskStart.reload({params: {PRO_UID: selVal}});

                            comboTaskStartSla.store.on('load',function(store) {
                                comboTaskStartSla.setValue(valuesData.VAL_TASK_START_SLA);
                            });
                            break;

                    }

                    numberDurationSla.setValue(valuesData.VAL_DURATION_NUMBER_SLA);
                    comboDurationSla.setValue(valuesData.VAL_DURATION_TYPE_SLA);

                    txtareaConditionSla.setValue(valuesData.VAL_CONDITION_SLA);

                    comboPenaltyUnitSla.store.removeAll();
                    var aPenaltyUnitSla1 = new Array();
                    var aPenaltyUnitSla2 = new Array();
                    var aPenaltyUnitSla3 = new Array();
                    aPenaltyUnitSla1['ID'] = '$US';
                    aPenaltyUnitSla1['VAL'] = _TRANS("ID_SUS");
                    var recPenaltyUnitSla = new Ext.data.Record(aPenaltyUnitSla1);
                    comboPenaltyUnitSla.store.add(recPenaltyUnitSla);

                    aPenaltyUnitSla2['ID'] = 'POINTS';
                    aPenaltyUnitSla2['VAL'] = _TRANS("ID_POINTS");
                    recPenaltyUnitSla = new Ext.data.Record(aPenaltyUnitSla2);
                    comboPenaltyUnitSla.store.add(recPenaltyUnitSla);
                    if (valuesData.VAL_PENALITY_UNIT_TYPE_SLA != '$us' &&
                        valuesData.VAL_PENALITY_UNIT_TYPE_SLA != 'Points') {
                        aPenaltyUnitSla3['ID'] = valuesData.VAL_PENALITY_UNIT_TYPE_SLA;
                        aPenaltyUnitSla3['VAL'] = valuesData.VAL_PENALITY_UNIT_TYPE_SLA;
                        recPenaltyUnitSla = new Ext.data.Record(aPenaltyUnitSla3);
                        comboPenaltyUnitSla.store.add(recPenaltyUnitSla);
                    }
                    comboPenaltyUnitSla.setValue(valuesData.VAL_PENALITY_UNIT_TYPE_SLA);
                    
                    comboPenaltyValueSla.setValue(valuesData.VAL_PENALITY_VALUE_TYPE_SLA);

                    valuesData.VAL_TYPE_FORM = 'UPDATE';
                    windowsFormSla.show();
                    saveButton.disable();
                    windowsFormSla.setTitle( _TRANS("ID_EDIT_SLA"));

                    if (valuesData.VAL_SLA_PEN_ENABLED == '1') {

                        changeSizeForm(DISPLAY_EXPANDED);
                        fieldsPenaltys.expand();
                    } else {
                        changeSizeForm(DISPLAY_NORMAL);
                        fieldsPenaltys.collapse();
                    }
                    numberPenaltyValueSla.setValue(valuesData.VAL_PENALITY_VALUE_NUMBER_SLA);
                    numberPenaltyTimeSla.setValue(valuesData.VAL_PENALITY_TIME_NUMBER_SLA);

                } else {
                    Ext.MessageBox.alert(_TRANS("ID_ERROR"), _TRANS("ID_PROBLEM_OCCURRED"));
                }
            },
            failure: function () {
                Ext.MessageBox.alert(_TRANS("ID_ERROR"), _TRANS("ID_PROBLEM_OCCURRED"));
            }
        });

    }
}

DeleteSla = function()
{
    rowSelected = gridLisSLA.getSelectionModel().getSelected();
    if (rowSelected) {
        var swDelete = false;
        Ext.Ajax.request({
            url: 'controllers/slaProxy.php',
            params: {
                functionExecute: 'existSla',
                SLA_UID: rowSelected.data.SLA_UID
            },
            success: function(response, opts) {
                swDelete = (response.responseText == 'true') ? true : false;
                if (swDelete) {
                    Ext.Msg.confirm(_('ID_CONFIRM'), _TRANS("ID_MSG_DELETE_SLA") + ': ' +
                                                     rowSelected.data.SLA_NAME + '?',
                    function(btn, text) {
                        if (btn == "yes") {
                            Ext.Ajax.request({
                                url: 'controllers/slaProxy.php',
                                params: {
                                          functionExecute: 'deleteSla',
                                          SLA_UID: rowSelected.data.SLA_UID
                                        },
                                success: function(r,o) {
                                    gridLisSLA.store.load();
                                    editButton.disable();
                                    deleteButton.disable();
                                    PMExt.notify(_TRANS("ID_TITLE_PMSLA"), _TRANS("ID_REGISTER_DELETED"));
                                },
                                failure: function() {
                                }
                            });
                        }
                    });
                } else {
                    PMExt.error(_TRANS("ID_TITLE_PMSLA"), _TRANS("ID_MSG_NOT_DELETE_SLA_REGISTER"));
                }
            },
            failure: function() {
                DoNothing();
            }
        });
    }
};


var gridLisSLA;
var storeSlaList;
var editButton;
var deleteButton;

Ext.onReady(function() {
    storeSlaList = new Ext.data.JsonStore({
        proxy: new Ext.data.HttpProxy({
            method: 'POST',
            url: 'controllers/slaProxy.php'
        }),
        root: 'data',
        autoDestroy: true,
        totalProperty: 'total',
        remoteSort: true,
        baseParams: {functionExecute: 'listSla'},
        fields: ['SLA_UID','PRO_UID','SLA_NAME','SLA_DESCRIPTION','PRO_NAME','SLA_TASKS','SLA_DURATION','SLA_STATUS']
    });
    storeSlaList.load();


    var pageSize=parseInt(CONFIG.pageSize);
    var storePageSize = new Ext.data.SimpleStore({
      autoLoad: true,
      fields: ['size'],
      data:[['20'],['30'],['40'],['50'],['100']]
    });

    var comboPageSize = new Ext.form.ComboBox({
      typeAhead     : false,
      mode          : 'local',
      triggerAction : 'all',
      store: storePageSize,
      valueField: 'size',
      displayField: 'size',
      width: 50,
      editable: false,
      listeners:{
        select: function(c,d,i){
            pagingSlaList.pageSize = parseInt(d.data['size']);
            pagingSlaList.moveFirst();
        }
      }
    });

    comboPageSize.setValue(pageSize);

    var pagingSlaList = new Ext.PagingToolbar({
        pageSize : pageSize,
        store : storeSlaList,
        displayInfo : true,
        autoHeight : true,
        displayMsg : 'SLA {0} - {1} Of {2}',
        emptyMsg : _TRANS("ID_NO_SLA_SHOW"),
        items: [
          comboPageSize
        ]
    });


    var newButton = new Ext.Action({
        text:_('ID_NEW'),
        iconCls:'button_menu_ext ss_sprite ss_time_add',
        handler: function () {
             openFormSla();
        }
    });

    editButton = new Ext.Action({
        text: _('ID_EDIT'),
        icon: '/plugin/pmSLA/images/time_edit.png',
        handler: EditSla,
        disabled: true
    });

    deleteButton = new Ext.Action({
        text: _('ID_DELETE'),
        iconCls: 'button_menu_ext ss_sprite ss_time_delete',
        handler: DeleteSla,
        disabled: true
    });

    var searchText = new Ext.ux.form.SearchField({
         emptyText : _TRANS("ID_FIND_RELATION"),
         store: storeSlaList,
         width: 250
     })

    smodel = new Ext.grid.RowSelectionModel({
        singleSelect: true,
        listeners:{
            rowselect: function(sm) {
                editButton.enable();
                deleteButton.enable();
            },
            rowdeselect: function(sm) {
                editButton.disable();
                deleteButton.disable();
            }
        }
    });

    gridLisSLA = new Ext.grid.GridPanel({
        id: 'gridLisSLA',
        store: storeSlaList,
        margins: '0 0 0 0',
        border: true,
        title: _TRANS("ID_LIST_SLA"), // 'List SLA',
        loadMask : true,
        cm: new Ext.grid.ColumnModel({
            defaults: {
                width: 20,
                sortable: true
            },
            columns: [
                      new Ext.grid.RowNumberer(),
                      {header: _TRANS("ID_NAME"), width: 30, sortable: true, dataIndex: 'SLA_NAME'},
                      {header: _TRANS("ID_DESCRIPTION"), width: 20, sortable: true, dataIndex: 'SLA_DESCRIPTION'},
                      {header: _TRANS("ID_PROCESS"), width: 20, sortable: true, dataIndex: 'PRO_NAME'},

                      {header: _TRANS("ID_RANGE_TASKS"), width: 15, sortable: true, dataIndex: 'SLA_TASKS' },
                      {header: _TRANS("ID_STATUS"), width: 5, sortable: true, dataIndex: 'SLA_STATUS'}
            ]
        }),
        autoShow: true,
        autoFill:true,
        nocache: true,
        autoWidth: true,
        stripeRows: true,
        stateful: true,
        animCollapse: true,
        sm: smodel,
        tbar:[newButton,editButton,deleteButton,'->',searchText],
        bbar : pagingSlaList,
        viewConfig: {
                  forceFit:true,
                  scrollOffset: 2,
                  emptyText: '<div align="center"><b> ' + _TRANS("ID_NO_SLA_SHOW") + ' </b></div>'
        },
        listeners: {
            rowdblclick : EditSla
        }
    });


    new Ext.Viewport({
        layout:'fit',
        autoScroll: true,
        border: false,
        items:[gridLisSLA]
    });
});