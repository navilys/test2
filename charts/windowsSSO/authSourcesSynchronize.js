/**
 * This file render the tree objects
 *
 * @author Julio Cesar Laura <juliocesar at colosa dot com> <contact at julio-laura dot com>
 * @package plugins.windowsSSO
 * @copyright Copyright (C) 2004 - 2011 Colosa Inc.
 */

// Global variables
var viewPort;
var backButton;
var northPanel;
var tabsPanel;
var departmentsPanel;
var groupsPanel;
var treeDepartments;
var treeGroups;
var isSaved = true;
var isFirstTime = true;

// Main function
Ext.onReady(function() {
  try {
    Ext.Ajax.timeout = 300000;

    // Action button to back to the authentication sources list
    backButton = new Ext.Action({
      text : _('ID_BACK'),
      iconCls: 'button_menu_ext ss_sprite ss_arrow_redo',
      handler: function() {
        location.href = '../authSources/authSources_List';
      }
    });

    // North panel
    northPanel = new Ext.Panel({
      region: 'north',
      xtype: 'panel',
      tbar: ['<b>'+ 'Authentication Sources' + '</b>', {xtype: 'tbfill'}, backButton]
    });

    // Departments tree object
    treeDepartments = new Ext.tree.TreePanel({
      title: 'Departments List',
      defaults: {flex: 1},
      useArrows: true,
      autoScroll: true,
      animate: true,
      enableDD: true,
      containerScroll: true,
      rootVisible: false,
      frame: true,
      root: {
        nodeType: 'async'
      },
      maskDisabled: false,
      dataUrl: 'authSourcesSynchronizeAjax?m=loadDepartments&authUid=' + AUTHENTICATION_SOURCE.AUTH_SOURCE_UID,
      requestMethod: 'POST',
      buttons: [{
        text: 'Save Changes',
        handler: function() {
          isSaved = false;
          var msg = '', selNodes = treeDepartments.getChecked();
          treeDepartments.disabled = true;
          this.disabled = true;
          var departments = [];
          Ext.each(selNodes, function(node) {
            departments.push(node.id);
          });
          Ext.Ajax.request({
            url: 'authSourcesSynchronizeAjax',
            params: {m: 'saveDepartments', authUid: AUTHENTICATION_SOURCE.AUTH_SOURCE_UID, departmentsDN: departments.join('|')},
            success: function(r) {
              var response = Ext.util.JSON.decode(r.responseText);
              if (response.status == 'OK') {
                treeDepartments.getLoader().load(treeDepartments.root);
              }
              else {
                alert(response.message);
              }
            }
          });
        }
      }]
    });

    // Callback function for the departments tree object
    treeDepartments.loader.on('load', function() {
      treeDepartments.getRootNode().expand(true);
      if (!isSaved) {
        isSaved = true;
        treeDepartments.disabled = false;
        treeDepartments.buttons[0].disabled = false;
        Ext.Msg.show({
          title: 'Changes saved.',
          msg: 'All changes have been saved.',
          icon: Ext.Msg.INFO,
          minWidth: 200,
          buttons: Ext.Msg.OK
        });
      }
    });

    // Groups tree object
    treeGroups = new Ext.tree.TreePanel({
      title: 'Groups List',
      defaults: {flex: 1},
      useArrows: true,
      autoScroll: true,
      animate: true,
      enableDD: true,
      containerScroll: true,
      rootVisible: false,
      frame: true,
      root: {
        nodeType: 'async'
      },
      dataUrl: 'authSourcesSynchronizeAjax?m=loadGroups&authUid=' + AUTHENTICATION_SOURCE.AUTH_SOURCE_UID,
      requestMethod: 'POST',
      buttons: [{
        text: 'Save Changes',
        handler: function() {
          isSaved = false;
          var msg = '', selNodes = treeGroups.getChecked();
          treeGroups.disabled = true;
          this.disabled = true;
          var Groups = [];
          Ext.each(selNodes, function(node) {
            Groups.push(node.id);
          });
          Ext.Ajax.request({
            url: 'authSourcesSynchronizeAjax',
            params: {m: 'saveGroups', authUid: AUTHENTICATION_SOURCE.AUTH_SOURCE_UID, groupsDN: Groups.join('|')},
            success: function(r) {
              var response = Ext.util.JSON.decode(r.responseText);
              if (response.status == 'OK') {
                treeGroups.getLoader().load(treeGroups.root);
              }
              else {
                alert(response.message);
              }
            }
          });
        }
      }]
    });

    // Callback function for the groups tree object
    treeGroups.loader.on('load', function() {
      treeGroups.getRootNode().expand(true);
      if (!isSaved) {
        isSaved = true;
        treeGroups.disabled = false;
        treeGroups.buttons[0].disabled = false;
        Ext.Msg.show({
          title: 'Changes saved.',
          msg: 'All changes have been saved.',
          icon: Ext.Msg.INFO,
          minWidth: 200,
          buttons: Ext.Msg.OK
        });
      }
    });

    // Departments panel object
    departmentsPanel = new Ext.Panel({
      title: 'Synchronize Departments',
      autoWidth: true,
      layout: 'hbox',
      defaults: {flex: 1},
      layoutConfig: {align: 'stretch'},
      items: [treeDepartments],
      viewConfig: {forceFit: true}
    });

    // Groups panel object
    groupsPanel = new Ext.Panel({
      title: 'Synchronize Groups',
      autoWidth: true,
      layout: 'hbox',
      defaults: {flex: 1},
      layoutConfig: {align: 'stretch'},
      items: [treeGroups],
      viewConfig: {forceFit: true}
    });

    // Tabs panel object
    tabsPanel = new Ext.TabPanel({
      region: 'center',
      activeTab: AUTHENTICATION_SOURCE.CURRENT_TAB,
      items:[departmentsPanel, groupsPanel],
      listeners:{
        beforetabchange: function(p, t, c) {
          if (typeof(t.body) == 'undefined') {
            isFirstTime = true;
          }
        },
        tabchange: function(p, t) {
          if (!isFirstTime) {
            switch(t.title){
              case 'Synchronize Departments':
                treeDepartments.getLoader().load(treeDepartments.root);
              break;
              case 'Synchronize Groups':
                treeGroups.getLoader().load(treeGroups.root);
              break;
            }
          }
          else {
            isFirstTime = false;
          }
        }
      }
    });

    // Viewport object
    viewport = new Ext.Viewport({
      layout: 'border',
      items: [northPanel, tabsPanel]
    });
  }
  catch (error) {
    alert('->' + error + '<-');
  }
});