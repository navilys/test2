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

Ext.onReady(function() {
  nodeChangeCheck = function (node, check)
  {
      if (node) {
          if (node.hasChildNodes()) {
              node.eachChild(function (n) { nodeChangeCheck(n, check); });
          }

          //node.expand();
          node.getUI().toggleCheck(check);
      }
  }

  nodeChangeCheckStart = function (node, check)
  {
      treeDepartments.removeListener("checkchange", nodeChangeCheckStart, this);

      nodeChangeCheck(node, check);

      treeDepartments.addListener("checkchange", nodeChangeCheckStart, this);
  }

  try {
    Ext.Ajax.timeout = 300000;

    backButton = new Ext.Action({
      text : _('ID_BACK'),
      iconCls: 'button_menu_ext ss_sprite ss_arrow_redo',
      handler: function() {
        location.href = '../authSources/authSources_List';
      }
    });

    northPanel = new Ext.Panel({
      region: 'north',
      xtype: 'panel',
      tbar: ['<b>'+ 'Authentication Sources' + '</b>', {xtype: 'tbfill'}, backButton]
    });

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
      }],

      listeners: {
          checkchange: nodeChangeCheckStart
      }
    });

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

    departmentsPanel = new Ext.Panel({
      title: 'Synchronize Departments',
      autoWidth: true,
      layout: 'hbox',
      defaults: {flex: 1},
      layoutConfig: {align: 'stretch'},
      items: [treeDepartments],
      viewConfig: {forceFit: true}
    });

    groupsPanel = new Ext.Panel({
      title: 'Synchronize Groups',
      autoWidth: true,
      layout: 'hbox',
      defaults: {flex: 1},
      layoutConfig: {align: 'stretch'},
      items: [treeGroups],
      viewConfig: {forceFit: true}
    });

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

    viewport = new Ext.Viewport({
      layout: 'border',
      items: [northPanel, tabsPanel]
    });
  }
  catch (error) {
    alert('->' + error + '<-');
  }
});