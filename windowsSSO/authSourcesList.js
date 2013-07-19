/**
 * This file is loaded automatically by ProcessMaker when the list of authentication
 * sources is displayed.
 *
 * @author Julio Cesar Laura <juliocesar at colosa dot com> <contact at julio-laura dot com>
 * @package plugins.windowsSSO
 * @copyright Copyright (C) 2004 - 2011 Colosa Inc.
 */

/**
 * This function loads the page with the tree for the departments
 * @return void
 */
var synchronizeDepartmentsWSSO = function() {
  iGrid = Ext.getCmp('infoGrid');
  rowSelected = iGrid.getSelectionModel().getSelected();
  if (rowSelected) {
    location.href = '../windowsSSO/authSourcesSynchronize?authUid=' + rowSelected.data.AUTH_SOURCE_UID + '&tab=synchronizeDepartments';
  }
};

/**
 * This function loads the page with the tree for the groups
 * @return void
 */
var synchronizeGroupsWSSO = function() {
  iGrid = Ext.getCmp('infoGrid');
  rowSelected = iGrid.getSelectionModel().getSelected();
  if (rowSelected) {
    location.href = '../windowsSSO/authSourcesSynchronize?authUid=' + rowSelected.data.AUTH_SOURCE_UID + '&tab=synchronizeGroups';
  }
};

// Creating an action button for the synchronize departments
var synchronizeDepartmentsButtonWSSO = new Ext.Action({
  text: 'Synchronize Departments',
  iconCls: 'ICON_DEPARTAMENTS',
  disabled: true,
  handler: synchronizeDepartmentsWSSO
});

// Creating an action button for the synchronize groups
var synchronizeGroupsButtonWSSO = new Ext.Action({
  text: 'Synchronize Groups',
  iconCls: 'ICON_GROUPS',
  disabled: true,
  handler: synchronizeGroupsWSSO
});

/**
 * Callback function to enable the action buttons according to the value
 * @param String sm The owner object
 * @param String index The index of the row
 * @param String record The record object for the row
 * @return void
 */
var _rowselectWSSO = function(sm, index, record) {
  if (record.get('AUTH_SOURCE_PROVIDER') == 'windowsSSO') {
    synchronizeDepartmentsButtonWSSO.enable();
    synchronizeGroupsButtonWSSO.enable();
  }
};

/**
 * Callback function to disable the action buttons according to the value
 * @param String sm The owner object
 * @param String index The index of the row
 * @param String record The record object for the row
 * @return void
 */
var _rowdeselectWSSO = function(sm, index, record) {
  synchronizeDepartmentsButtonWSSO.disable();
  synchronizeGroupsButtonWSSO.disable();
};

// Adding the new action buttons and callback functions to the main arrays
_rowselect.push(_rowselectWSSO);
_rowdeselect.push(_rowdeselectWSSO);
_pluginActionButtons.push(synchronizeDepartmentsButtonWSSO);
_pluginActionButtons.push(synchronizeGroupsButtonWSSO);