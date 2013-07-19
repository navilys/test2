Ext.onReady(function() {
  onMessageMnuContext = function (grid, rowIndex, e) {
    e.stopEvent();
    var coords = e.getXY();
    mnuContext.showAt([coords[0], coords[1]]);
  };

  ///////
  var storePM;

  var msgCt;

  //Stores
  storePM = new Ext.data.Store({
    proxy: new Ext.data.HttpProxy({
      url: "processMakerAjax",
      method: "POST"
    }),

    baseParams: {"option": "list"},

    reader: new Ext.data.JsonReader({
      root: "results",
      fields: [{name: "OBJ_UID", type: "string"}, {name: "OBJ_VERSION", type: "string"}, {name: "OBJ_VERSION_NAME", type: "string"}]
    }),

    autoLoad: true, //First call

    listeners: {
      exception: function (proxy, type, action, options, response, args){
        var dataResponse;
        var sw = 1;

        if (sw == 1 && !response.responseText) {
          sw = 0;
        }
        if (sw == 1 && response.responseText && response.responseText != "") {
          dataResponse = eval("(" + response.responseText + ")"); //json

          if (dataResponse.status && dataResponse.status == "ERROR") {
            sw = 0;
          }
        }

        if (sw == 0) {
          Ext.ComponentMgr.get("cboPmVersion").setDisabled(true);
          Ext.ComponentMgr.get("btnPmUpgrade").setDisabled(true);
        }
      },
      load: function (store, record, option) {
        Ext.ComponentMgr.get("cboPmVersion").setDisabled(false);
        Ext.ComponentMgr.get("btnPmUpgrade").setDisabled(false);

        if (store.getAt(0)) {
          Ext.ComponentMgr.get("cboPmVersion").setValue(store.getAt(0).get(Ext.ComponentMgr.get("cboPmVersion").valueField));
        }
        else {
          Ext.ComponentMgr.get("cboPmVersion").setDisabled(true);
          Ext.ComponentMgr.get("btnPmUpgrade").setDisabled(true);
        }
      }
    }
  });

  function createBox(t, s){
    return ['<div class="msg">',
    '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
    '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', t, '</h3>', s, '</div></div></div>',
    '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
    '</div>'].join('');
  }

  function message(title, arguments){
    if(!msgCt){
      msgCt = Ext.DomHelper.insertFirst(document.body, {
        id:'msg-div'
      }, true);
    }
    msgCt.alignTo(document, 't-t');
    //var s = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
    var m = Ext.DomHelper.append(msgCt, {
      html:createBox(title, arguments)
      }, true);
    m.slideIn('t').pause(10).ghost("t", {
      remove:true
    });
  }

  Ext.QuickTips.init();
  Ext.form.Field.prototype.msgTarget = 'side';

  var upgradeAddonId;
  var upgradeStoreId;

  function newLocation() {
    var site = '';
    if (SYS_SKIN.substring(0,2) == 'ux') {
        site = PROCESSMAKER_URL + "/main?st=admin&s=PMENTERPRISE";
    } else {
        site = PROCESSMAKER_URL + "/setup/main?s=PMENTERPRISE";
    }
    return site;
  }
  function processMakerUpgrade() {
    swReloadTask = 0;
    var noticeWindow = new Ext.Window({
      closable: false,
      autoHeight: true,
      modal: true,
      width: 600,
      height: 350,
      id: "notice_window",
      title: "Before Upgrade",
      bodyStyle: "font: normal 13px sans;",
      bbar: new Ext.Toolbar({
        buttonAlign: "center",
        padding: 15,
        items: [
        {
          id: "upgrade-continue",
          //text: " Continue &raquo; ",
          text: " Continue &gt;&gt; ",
          handler: function () {
            noticeWindow.hide();

            processMakerInstall();
          }
        },
        {
          id: "upgrade-cancel",
          text: "<b> Cancel </b>",
          handler: function () {
            swReloadTask = 1;
            noticeWindow.hide();
          }
        }]
      }),
      items: [{
        id: "notice",
        preventBodyReset : true,
        padding: 15,
        html: "<h3>Caution: Before continuing, please read these instructions carefully:</h3>" +
              "<ul style=\"font: 16px;\">" +
              "<li><a href=\"http://wiki.processmaker.com/index.php/Upgrading_ProcessMaker\" onclick=\"window.open(this.href, '_blank'); return (false);\">Read the wiki</a> carefully regarding the automatic upgrade.</li>" +
              "<li>Make a backup of all ProcessMaker files, including the database before proceeding.</li>" +
              "<li>This automatic upgrade may not always work depending on your server configuration.</li>" +
              "<li>In case the upgrade fails, read the wiki regarding alternative methods of upgrading.</li>" +
              "</ul>"
      }]
    });

    noticeWindow.show();
  }

  function upgradeStatus(addonId, storeId, record) {
    upgradeAddonId = addonId;
    upgradeStoreId = storeId;
    //console.log(record);
    //progressWindow.hide();
    progressWindow.show();
    //if (progress)
    //  Ext.ComponentMgr.get('upgrade-progress')
    if (record) {
      progress = record.get('progress');
      status = record.get('status');
      if (status == 'install')
        msg = "Please wait while installing the plugin...";
      else if (status == 'install-finish')
        msg = "Upgrade finished.";
      else
        msg = "Please wait while upgrading the plugin...";
      if (status == "download" && progress) {
        msg = "Downloading upgrade: " + progress + "%";
        Ext.ComponentMgr.get('upgrade-progress').show();
        Ext.ComponentMgr.get('upgrade-progress').updateProgress(progress/100, '', true);
      } else {
        Ext.ComponentMgr.get('upgrade-progress').hide();
      }
      Ext.ComponentMgr.get('finish-upgrade-button').setDisabled((status != "install-finish"));
      msg = '<h3>' + msg + '</h3>';
      logMsg = record.get('log');
      while (logMsg && logMsg.indexOf("\n") > -1)
        logMsg = logMsg.replace("\n","<br/>");
      if (logMsg && status != "download-start")
        Ext.ComponentMgr.get('upgrade-log').update("<h4>Installation log:</h4><p>"+logMsg+"</p>");
    } else {
      msg = "<h3> Please wait while the upgrade is starting...</h3>";
    }
    Ext.ComponentMgr.get('upgrade-status').update(msg);
  }

  function installError(addonId, storeId, msg) {
    recordId = addonsStore.findBy(function(record) {
      return (record.get("id") == addonId && record.get("store") == storeId);
    });

    downloadLink = "";
    if (recordId != -1) {
      record = addonsStore.getAt(recordId);
      url = record.get("url");
      downloadLink = "<p>You can download it manually <a href=\"" + url + "\">from here</a></p>";
    }

    if (msg === undefined) {
      msg = "<p><b>Error:<b> unknown</p>";
    }
    else {
      msg = "<p><b>Error:</b> " + msg + "</p>";
    }

    errorWindow = new Ext.Window({
      //applyTo: document.body,
      layout: "fit",
      width: 400,
      height: 250,
      plain: true,
      modal: true,

      items: [{
        id: "error",
        preventBodyReset: true,
        padding: 15,
        html: "<h3>Install Error</h3>" +
        "<p>There was a problem installing this addon.</p>" +
        //downloadLink +
        msg
      }],

      buttons: [{
        text: "Close",
        handler: function(){
          errorWindow.hide();
        }
      }]
    });
    errorWindow.show(this);
  }

  function storeError(msg) {
    if (msg === undefined) {
      msg = "<p><b>Error:<b> unknown</p>";
    } else {
      msg = "<p><b>Error:</b> " + msg + "</p>";
    }

    errorWindow = new Ext.Window({
      //applyTo:document.body,
      layout:'fit',
      width:400,
      height:250,
      plain: true,
      modal: true,

      items: [{
        id: 'error',
        preventBodyReset : true,
        padding: 15,
        html: '<h3>Server error</h3>'+
        '<p>There was a problem contacting the market server.</p>'+
        msg
      }],

      buttons: [{
        text: 'Close',
        handler: function(){
          errorWindow.hide();
        }
      }]
    });
    errorWindow.show(this);
  }

  function installAddon(addonId, storeId)
  {  var sw = 1;
     var msg = "";

     if (sw == 1 && PATH_PLUGINS_WRITABLE == 0) {
       sw = 0;
       msg = PATH_PLUGINS_WRITABLE_MESSAGE;
     }

     if (sw == 1) {
       swReloadTask = 0;
       reloadTask.cancel();

       recordId = addonsStore.findBy(function(record) {
         return (record.get("id") == addonId && record.get("store") == storeId);
       });

       //var addonEnabled = "";

       if (recordId != -1) {
         record = addonsStore.getAt(recordId);
         record.set("status", "download-start");
         record.commit();

         //addonEnabled = record.get("enabled");
       }

       Ext.Ajax.request({
         url: "addonsStoreAction",
         method: "POST",
         params: {
           "action": "install",
           "addon": addonId,
           "store": storeId
         },

         success: function (response, opts) {
           var dataResponse = eval("(" + response.responseText + ")"); //json
           swReloadTask = 1;

           if (dataResponse.status && dataResponse.status == "OK") {
             //parent.Ext.getCmp(parent.tabItems[1].id).getRootNode().reload();
             parent.parent.window.location.href = newLocation();
           }
           else {
             installError(addonId, storeId, dataResponse.message);

             addonsStore.load();
           }
         },

         failure: function (response, opts) {
           swReloadTask = 1;

           //installError(addonId, storeId);
         }
       });
     }
     else {
       Ext.MessageBox.alert("Warning", msg);
     }
  }

  function addonAvailable(addonId)
  {
      if (INTERNET_CONNECTION == 1) {
          swReloadTask = 0;
          reloadTask.cancel();

          Ext.MessageBox.confirm(
              "Confirm",
              "It will send a request to Sales Department, do you want to continue?",
              function (btn, text) {
                  if (btn == "yes") {
                      var myMask = new Ext.LoadMask(Ext.getBody(), {msg: "Sending request to ProcessMaker Sales Department, please wait..."});
                      myMask.show();

                      Ext.Ajax.request({
                          url: "addonsStoreAction",
                          method: "POST",
                          params: {
                              "action": "available",
                              "addonId": addonId
                          },

                          success: function (response, opts) {
                              var dataResponse = eval("(" + response.responseText + ")"); //json

                              swReloadTask = 1;
                              myMask.hide();

                              if (dataResponse.status && dataResponse.status == "OK") {
                                  Ext.MessageBox.show({
                                      width: 400,
                                      icon: Ext.MessageBox.INFO,
                                      buttons: Ext.MessageBox.OK,

                                      title: "Information",
                                      msg: "Your request has been sent, thanks for contact us."
                                      //fn: saveAddress
                                  });
                              } else {
                                Ext.MessageBox.alert("Warning", dataResponse.message);
                              }

                              addonsStore.load();
                          },

                          failure: function (response, opts) {
                              swReloadTask = 1;
                              myMask.hide();
                          }
                      });
                  } else {
                      swReloadTask = 1;

                      addonsStore.load();
                  }
              }
          );
      } else {
          Ext.MessageBox.alert("Information", "Enterprise Plugins Manager no connected to internet.");
      }
  }

  function processMakerInstall()
  {  var myMask = new Ext.LoadMask(Ext.getBody(), {msg: "Please wait while upgrading ProcessMaker..."});
     myMask.show();

     var cboPm = Ext.ComponentMgr.get("cboPmVersion");

     var record = cboPm.findRecord(cboPm.valueField, cboPm.getValue());
     var index  = cboPm.store.indexOf(record);

     var uid         = cboPm.store.getAt(index).get("OBJ_UID");
     var version     = cboPm.getValue();
     var versionName = cboPm.store.getAt(index).get(cboPm.displayField);

     swReloadTask = 0;
     reloadTask.cancel();

     Ext.Ajax.timeout = 1800000; //milliseconds //1 millisecond = 0.001 second

     Ext.Ajax.request({
       url: "processMakerAjax",
       method: "POST",
       params: {
         "option": "install",
         "uid": uid,
         "version": version,
         "versionName": versionName,
         "processMakerVersion": PROCESSMAKER_VERSION
       },

       success: function (response, opts) {
         swReloadTask = 1;
         myMask.hide();

         var sw = 1;
         var msg = "";

         if (sw == 1 && response.responseText == "") {
           sw = 0;
           msg = "";
         }

         if (sw == 1 && !(/^.*\{.*\}.*$/.test(response.responseText))) {
           sw = 0;
           msg = "<br />" + response.responseText + "<br />";
         }

         if (sw == 1) {
           var dataResponse = eval("(" + response.responseText + ")"); //json

           if (dataResponse.status && dataResponse.status == "OK") {
             //window.location.href = "";
             //window.location.reload();
             Ext.MessageBox.alert("Information", dataResponse.message + "<br />Please login again to apply the changes.", function () { parent.parent.window.location.href = PROCESSMAKER_URL + (SYS_SKIN.substring(0,2) == 'ux')? "/main/login" :"/setup/login/login"; });
           }
           else {
             Ext.MessageBox.alert("Warning", "Error upgrading System.<br />" + dataResponse.message);

             addonsStore.load();
           }
         }
         else {
           Ext.MessageBox.alert("Warning", "An error has occurred, press \"OK\" to check whether the system has been upgrade.<br />" + msg, function () { parent.parent.window.location.href = PROCESSMAKER_URL + (SYS_SKIN.substring(0,2) == 'ux')? "/main/login" :"/setup/login/login"; });
         }
       },

       failure: function (response, opts) {
         swReloadTask = 1;
         myMask.hide();
       }
     });
  }

  function enterpriseProcessAjax(option)
  {
      switch (option) {
          case "SETUP":
              var myMask = new Ext.LoadMask(Ext.getBody(), {msg: "Processing..."});
              myMask.show();
              break;
      }

      var p = {
          "option": option
      };

      switch (option) {
          case "SETUP":
              eval("p.internetConnection = \"" + ((Ext.getCmp("chkEeInternetConnection").checked == true)? 1 : 0) + "\"");
              break;
      }

      Ext.Ajax.request({
          url: "enterpriseAjax",
          method: "POST",
          params: p,

          success: function (response, opts) {
              var dataResponse = eval("(" + response.responseText + ")"); //json

              switch (option) {
                  case "SETUP":
                      INTERNET_CONNECTION = (Ext.getCmp("chkEeInternetConnection").checked == true)? 1 : 0;

                      reloadTask.cancel();

                      Ext.ComponentMgr.get("cboPmVersion").reset();
                      Ext.ComponentMgr.get("cboPmVersion").store.load();

                      addonsStore.load({
                          params: {
                              "force": true
                          }
                      });

                      Ext.getCmp("refresh-btn").setDisabled(!Ext.getCmp("chkEeInternetConnection").checked);

                      myMask.hide();
                      break;
              }
          },

          failure: function (response, opts) {
              //
          }
      });
  }

  var addonsStore = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
      url: "addonsStoreAction",
      method: "POST"
    }),
    baseParams: {"action": "addonsList"
                },

    //url: "addonsStoreAction?action=addonsList",

    autoDestroy: true,
    messageProperty: 'error',
    storeId: 'addonsStore',
    root: 'addons',
    idProperty: 'id',
    sortInfo: {
      field: 'nick',
      direction: 'ASC' // or 'DESC' (case sensitive for local sorting)
    },
    fields: ['id', 'name', 'store', 'nick', 'latest_version', 'version', 'status',
    'type', 'release_type', 'url', 'enabled', 'publisher', 'description',
    'log', 'progress'],
    listeners: {
      'beforeload': function(store, options) {
        //if (Ext.ComponentMgr.get('latest_version').getValue() == "")
        Ext.ComponentMgr.get('loading-indicator').setValue('<img src="/images/documents/_indicator.gif" />');
        return true;
      },
      "exception": function(e, type, action, options, response, arg) {
        //if (type == "response") {
        //  message("Error", "ProcessMaker had a problem completing this action. Please try again later");
        //} else {
        //  message("Error", response);
        //}

        Ext.ComponentMgr.get('loading-indicator').setValue('<span class="button_menu_ext ss_sprite ss_status_offline">&nbsp;</span>');
      },
      "load": function(store, records, options) {
        //reloadTask.delay(15000); //1 millisecond = 0.001 seconds

        //Ext.ComponentMgr.get('btnPmUpgrade').disable();
        Ext.ComponentMgr.get('loading-indicator').setValue("");
        progressWindow.hide();
        store.filterBy(function (record, id) {
          if (record.get('type') == 'core') {
            coreRecord = record.copy();

            //Ext.ComponentMgr.get('btnPmUpgrade').enable();
            //console.log("Core state: " + record.get('status'));
            //console.log(record);
            status = record.get('status');
            if (status == "download-start" || status == "download" || status == "install" || status == "install-finish") {
              upgradeStatus(record.get('id'), record.get('store'), record);
            }
            return false;
          }

          if (record.get('status') == 'download-start' || record.get('status') == 'download' || record.get('status') == 'cancel' || record.get('status') == 'install') {
              //
          }
          return true;
        });

        if (addonsGrid.disabled) {
          addonsGrid.enable();
        }

        errors = store.reader.jsonData.errors;
        for (var i = 0, n = errors.length; i<n; i++) {
          //console.log(errors[i]);
          error = errors[i];
          installError(error.addonId, error.storeId); ///////
        }

        store_errors = store.reader.jsonData.store_errors;
        //console.log(store_errors);
        error_msg = "";
        for (var i = 0, n = store_errors.length; i<n; i++) {
          //console.log(store_errors[i]);
          error_msg += "<p>" + store_errors[i].msg + "</p>";
        }

        //console.log(error_msg);
        //console.log(store_errors.length);
        if (store_errors.length > 0) {
          Ext.ComponentMgr.get('loading-indicator').setValue('<span class="button_menu_ext ss_sprite ss_status_offline" >&nbsp;</span>');
          //storeError(error_msg);
          reloadTask.cancel();
        }
        else{
          Ext.ComponentMgr.get('loading-indicator').setValue('<span class="button_menu_ext ss_sprite ss_status_online">&nbsp;</span>');
        }
      }
    }
  });

  var upgradeStore = new Ext.data.Store({
    recordType: addonsStore.recordType
  });

  var swReloadTask = 1;

  var reloadTask = new Ext.util.DelayedTask(
      function ()
      {
          if (swReloadTask == 1) {
              //addonsStore.load();
          }
      }
  );


  /**********               UI Controls               **********/

  var progressWindow = new Ext.Window({
    closable:false,
    autoHeight: false,
    autoScroll: false,
    modal: true,
    width:600,
    height:350,
    id: 'upgrade_window',
    title: 'Upgrade',
    bodyStyle: "font: normal 13px sans;",
    layout: 'vbox',
    layoutConfig: {
      align: 'stretch'
    },
    bbar: new Ext.Toolbar({
      buttonAlign: 'center',
      padding: 15,
      disabled: true,
      items: [{
        id: 'finish-upgrade-button',
        text: '<b> Finish </b>',
        handler: function() {
          Ext.ComponentMgr.get('finish-upgrade-button').setDisabled(true);
          Ext.Ajax.request({
            url: 'addonsStoreAction',
            params: {
              'action':'finish',
              'addon': upgradeAddonId,
              'store': upgradeStoreId
            }
          });
        }
      }]
    }),
    items: [{
      flex: 0,
      id: 'upgrade-status',
      preventBodyReset : true,
      padding: 5,
      html: '<h3>Upgrade</h3>'
    },{
      flex: 0,
      xtype: 'progress',
      hidden: true,
      id: 'upgrade-progress'
    },{
      flex: 1,
      id: 'upgrade-log',
      preventBodyReset : true,
      padding: 15,
      html: '',
      autoScroll: true
    }
    ]
  });

  var addLicenseWindow = new Ext.Window({
    title: 'Upload License',
    closeAction: 'hide',
    id: 'upload-window',
    resizeable: false,
    modal: true,
    //frame: true,
    width: 500,
    //autoHeight: true,
    items: [{
      xtype: 'form',
      id: 'upload-form',
      fileUpload: true,
      frame: true,
      border: false,
      //bodyStyle: 'padding: 10px 10px 0 10px;',
      labelWidth: 100,
      defaults: {
        anchor: '90%'
      },
      items: [{
        xtype: "fileuploadfield",
        id: "upLicense",

        emptyText: "Select a license file",
        fieldLabel: "License file",
        width: 200,
        name: "upLicense"
      }],
      buttons: [{
        text: "Upload",
        handler: function (button, event) {
          var uploadForm = Ext.getCmp("upload-form");
          var sw = 1;

          var fileName = Ext.getCmp("upLicense").value;

          if (!uploadForm.getForm().isValid()) {
            sw = 0;
          }
          if (!fileName) {
            sw = 0;
            Ext.MessageBox.alert("Warning", "Please select a valid license file.");
          }

          if (fileName && !(/^.*\.dat$/.test(fileName))) {
            sw = 0;
            Ext.MessageBox.alert("Warning", "The file doesn't a .dat extension, please select another file.");
          }

          if (sw == 1) {
            uploadForm.getForm().submit({
              url: "addonsStoreAction",
              params: {
                action: "importLicense"
              },
              waitMsg: "Uploading the license file...",
              success: function (form, o) {
                Ext.MessageBox.alert("Information", "Successfully uploaded.");
              },
              failure: function (form, o) {
                var dataResponse = eval("(" + o.response.responseText + ")"); //json

                Ext.MessageBox.alert("Warning", (dataResponse.errors)? dataResponse.errors : "Error uploading the license file.");
              }
            });
          }
        }
      },
      {
        text: 'Cancel',
        handler: function() {
          Ext.getCmp("upload-window").hide();
        }
      }
      ]
    }]
  });

  var addPluginWindow = new Ext.Window({
    title: 'Upload Plugin',
    closeAction: 'hide',
    id: 'upload-plugin-window',
    resizeable: false,
    modal: true,
    frame: true,
    width: 400,
    autoHeight: true,
    items: [{
      xtype: 'form',
      id: 'upload-plugin-form',
      fileUpload: true,
      frame: true,
      border: false,
      bodyStyle: 'padding: 10px 10px 0 10px;',
      labelWidth: 60,
      defaults: {
        anchor: '100%'
      },
      items: [{
        xtype: "fileuploadfield",
        id: "PLUGIN_FILENAME",

        emptyText: "Select a plugin file",
        fieldLabel: "Plugin file",
        name: "form[PLUGIN_FILENAME]"
      }],
      buttons: [{
        text: "Upload",
        handler: function (button, event) {
          var uploadForm = Ext.getCmp("upload-plugin-form");
          var sw = 1;
          var msg = "";

          if (sw == 1 && !uploadForm.getForm().isValid()) {
            sw = 0;
            msg = "";
          }
          if (sw == 1 && !Ext.getCmp("PLUGIN_FILENAME").value) {
            sw = 0;
            msg = "Please select a plugin";
          }

          if (sw == 1) {
            swReloadTask = 0;
            reloadTask.cancel();

            uploadForm.getForm().submit({
              url: "pluginsImportFile",
              params: {
                action: "installPlugin"
              },
              waitMsg: "Installing plugin...",

              success: function (form, action) {
                var dataResponse = action.result; //json

                swReloadTask = 1;
                Ext.getCmp("upload-plugin-window").hide();

                parent.parent.window.location.href = newLocation();
              },

              failure: function (form, action) {
                var dataResponse = action.result; //json

                swReloadTask = 1;

                Ext.MessageBox.alert("Warning", (dataResponse.message)? dataResponse.message : "Error uploading the plugin");

                addonsStore.load();
              }
            });
          }
          else {
            Ext.MessageBox.alert("Warning", msg);
          }
        }
      },
      {
        text: "Cancel",
        handler: function() {
          Ext.getCmp("upload-plugin-window").hide();
        }
      }]
    }]
  });

  var pnlUpgrade = new Ext.FormPanel({
      frame: true,
      title: "Upgrade System",

      bodyStyle: "padding: 5px 5px 5px 5px;",
      disabled: !licensed,

      items: [
          {
              layout: "column",
              items: [
                  {
                      columnWidth: 0.20,
                      xtype: "label",
                      text: "Current version" + ":"
                  },
                  {
                      columnWidth: 0.60,
                      xtype: "label",
                      text: PROCESSMAKER_VERSION
                  },
                  {
                      columnWidth: 0.20,
                      xtype: "label",
                      text: ""
                  }
              ]
          },
          {
              layout: "column",
              style: "margin-top: 10px;",
              items: [
                  {
                      columnWidth: 0.20,
                      xtype: "label",
                      text: "Latest version" + ":"
                  },
                  {
                      columnWidth: 0.60,
                      xtype: "container",
                      items: [
                          {
                              xtype: "combo",
                              id: "cboPmVersion",
                              editable: false,
                              store: storePM,
                              triggerAction: "all",
                              autoSelect: true,
                              mode: "local",
                              valueField:   "OBJ_VERSION",
                              displayField: "OBJ_VERSION_NAME",
                              emptyText: "No new versions available"
                          }
                      ]
                  },
                  {
                      columnWidth: 0.20,
                      xtype: "button",
                      id: "btnPmUpgrade",
                      text: "Upgrade",
                      disabled: true,
                      handler: function () {
                          if (INTERNET_CONNECTION == 1) {
                              processMakerUpgrade();
                          } else {
                              Ext.MessageBox.alert("Information", "Enterprise Plugins Manager no connected to internet.");
                          }
                      }
                  }
              ]
          }
      ]
  });

  var pnlSetup = new Ext.FormPanel({
      frame: true,
      title: "Setup",

      height: 80,

      bodyStyle: "padding: 5px 5px 5px 5px;",
      disabled: !licensed,

      items: [
          {
              layout: "column",
              items: [
                  {
                      columnWidth: 0.80,
                      xtype: "container",
                      items: [
                          {
                              xtype: "checkbox",
                              id: "chkEeInternetConnection",
                              name: "chkEeInternetConnection",
                              checked: (INTERNET_CONNECTION == 1)? true : false,
                              boxLabel: "Check for updates (you need to be connected to Internet)"
                          }
                      ]
                  },
                  {
                      columnWidth: 0.20,
                      xtype: "button",
                      id: "btnEeSetup",
                      text: "Save",
                      handler: function () {
                          enterpriseProcessAjax("SETUP");
                      }
                  }
              ]
          }
      ]
  });

  var pnlSystem = new Ext.Container({
      autoEl: "div",
      width: 550,

      items: [pnlUpgrade, pnlSetup]
  });

  var licensePanel = new Ext.FormPanel( {
    frame: true,
    title: "Your license",
    labelWidth: 150,
    labelAlign: "right",
    bodyStyle: "padding: 5px 5px 5px 5px;",
    defaultType: "displayfield",

    items: [
    {
      id: "license_name",
      fieldLabel:'Current license',
      value: license_name
    },
    {
      id: "license_server",
      fieldLabel:'License server',
      value: license_server
    },
    {
      id: "license_message",
      fieldLabel:'Status',
      hidden: licensed,
      hideLabel: licensed,
      value: "<font color='red'>"+license_message+"</font>&nbsp;("+license_start_date+"/"+license_end_date+")<br />"+license_user
    },

    {
      id: "license_user",
      fieldLabel:'Issued to',
      value: license_user,
      hidden: !licensed,
      hideLabel: !licensed
      },

      {
      id: "license_expires",
      fieldLabel:'Expires (days)',
      value: license_expires+'/'+license_span+" ("+license_start_date+"/"+license_end_date+")",
      hidden: !licensed,
      hideLabel: !licensed
      }
    ],
    buttons : [
    {
      text: "Import license",
      disable: false,
      handler: function() {
        addLicenseWindow.show();
      }
    },
    {
      text : "Renew",
      hidden: true,
      disabled : true
    }
    ]
  });

  var expander = new Ext.grid.RowExpander({
    tpl : new Ext.Template(
      '<p><b>Description:</b> {description}</p>'
      )
  });

  var btnUninstall = new Ext.Action({
    //id: "uninstall-btn",
    text: "Uninstall",
    tooltip: "Uninstall this plugin",
    iconCls: "button_menu_ext ss_sprite  ss_delete",
    handler: function (b, e) {
      //The plugin is activated, please deactivate first to remove it.

      var sw = 1;
      var msg = "";

      if (sw == 1 && PATH_PLUGINS_WRITABLE == 0) {
       sw = 0;
       msg = PATH_PLUGINS_WRITABLE_MESSAGE;
      }

      if (sw == 1) {
        Ext.MessageBox.confirm(
          "Confirm",
          "Are you sure that you want to remove this plugin?<br /><br />Uninstalling the plugin, it can affect your others workspaces.",
          function (btn, text) {
            if (btn == "yes") {
              swReloadTask = 0;
              reloadTask.cancel();

              var record = addonsGrid.getSelectionModel().getSelected();
              addonsGrid.disable();

              Ext.Ajax.request({
                url: "addonsStoreAction",
                params: {
                  "action": "uninstall",
                  "addon": record.get("id"),
                  "store": record.get("store")
                },
                success: function (response, opts) {
                  var dataResponse = eval("(" + response.responseText + ")"); //json
                  swReloadTask = 1;

                  if (dataResponse.status && dataResponse.status == "OK") {
                    parent.parent.window.location.href = newLocation();
                  }
                  else {
                    Ext.MessageBox.alert("Error uninstalling " + record.get("name"), dataResponse.message);

                    addonsStore.load();
                  }
                }
              });
            }
          }
        );
      }
      else {
        Ext.MessageBox.alert("Warning", msg);
      }
    }
  });

  var btnEnable = new Ext.Action({
    //id: "enable-btn",
    text: "Enable",
    tooltip: "Enable the selected addon",
    iconCls: "button_menu_ext ss_sprite ss_tag_green",
    disabled: true,
    handler: function (b, e) {
      var record = addonsGrid.getSelectionModel().getSelected();
      addonsGrid.disable();

      Ext.Ajax.request({
        url: "addonsStoreAction",
        params: {
          "action":"enable",
          "addon": record.get("id"),
          "store": record.get("store")
        },
        callback: function () {
          parent.parent.window.location.href = newLocation();
        },
        success: function (response) {
          var obj = eval("(" + response.responseText + ")"); //json

          if (!obj.success) {
            Ext.MessageBox.alert("Error enabling " + record.get("name"), obj.error);
          }
        }
      });
    }
  });

  var btnDisable = new Ext.Action({
    //id: "disable-btn",
    text: "Disable",
    tooltip: "Disable the selected plugin",
    iconCls: "button_menu_ext ss_sprite ss_tag_red",
    disabled: true,
    handler: function (b, e) {
      var record = addonsGrid.getSelectionModel().getSelected();
      addonsGrid.disable();

      Ext.Ajax.request({
        url: "addonsStoreAction",
        params: {
          "action":"disable",
          "addon": record.get("id"),
          "store": record.get("store")
          },
        callback: function () {
          parent.parent.window.location.href = newLocation();
        },
        success: function (response) {
          var obj = eval("(" + response.responseText + ")"); //json

          if (!obj.success) {
            Ext.MessageBox.alert("Error disabling " + record.get("name"), obj.error);
          }
        }
      });
    }
  });

  var btnAdmin = new Ext.Action({
    text: "Admin",
    tooltip: "Admin the selected plugin",
    //iconCls: "button_menu_ext ss_sprite ss_cog_edit",
    iconCls: "button_menu_ext ss_sprite ss_cog",
    disabled: true,
    handler: function () {
      var record = addonsGrid.getSelectionModel().getSelected();
      addonsGrid.disable();

      window.location.href = "pluginsSetup?id=" + record.get("id") + ".php";
    }
  });

  var mnuContext = new Ext.menu.Menu({
    //items: [btnUninstall, "-", btnEnable, btnDisable]
    items: [btnEnable, btnDisable, btnAdmin]
  });

  var addonsGrid = new Ext.grid.GridPanel({
    store: addonsStore,
    colspan: 2,
    flex: 1,
    padding: 5,
    disabled: !licensed,
    columns: [
    expander,
    {
      id       : 'icon-column',
      header   : '',
      width    : 30,
      //sortable : true,
      menuDisabled: true,
      hideable : false,
      dataIndex: 'status',
      renderer : function (val, metadata, record, rowIndex, colIndex, store) {
        return "<img src=\"/plugin/enterprise/" + val + ".png\" />";
      }
    },
    {
      id       :'nick-column',
      header   : 'Name',
      //width    : 160,
      //sortable : true,
      menuDisabled: true,
      dataIndex: 'nick',
      renderer: function (val, metadata, record, rowIndex, colIndex, store) {
        if (record.get('release_type') == 'beta')
          return val + " <span style='color:red'> (Beta)</span>";
        else if (record.get('release_type') == 'localRegistry')
          return val + " <span style='color:gray'> (Local)</span>";
        else
          return val;
      }
    },
    {
      id       : 'publisher-column',
      header   : 'Publisher',
      //sortable : true,
      menuDisabled: true,
      dataIndex: 'publisher'
    },
    {
      id       : 'version-column',
      header   : 'Version',
      //width    : 160,
      //sortable : true,
      menuDisabled: true,
      dataIndex: 'version'
    },
    {
      id       : 'latest-version-column',
      header   : 'Latest Version',
      //width    : 160,
      //sortable : true,
      menuDisabled: true,
      dataIndex: 'latest_version'
    },
    {
      id       : 'enabled-column',
      header   : 'Enabled',
      width    : 60,
      //sortable : true,
      menuDisabled: true,
      dataIndex: 'enabled',
      renderer: function (val) {
        if (val === true)
          return "<img src=\"/plugin/enterprise/tick-white.png\" />";
        else if (val === false)
          return "<img src=\"/plugin/enterprise/cross-white.png\" />";
        return '';
      }
    },
    {
      id       : "status",
      header   : "",
      width    : 120,
      //sortable : true,
      menuDisabled: true,
      hideable : false,
      dataIndex: "status",
      renderer: function (val) {
        var str = "";
        var text = "";

        switch (val) {
          case "available": text = "Buy now"; break;
          case "installed": text = "Installed"; break;
          case "ready":     text = "Install now"; break;
          case "upgrade":   text = "Upgrade now"; break;
          case "download":  text = "Cancel"; break;
          case "install":   text = "Installing"; break;
          case "cancel":    text = "Cancelling"; break;
          case "disabled":  text = "Disabled"; break;
          case "download-start": text = "<img src=\"/plugin/enterprise/loader.gif\" />"; break;
          default: text = val; break;
        }

        switch (val) {
          case "available":
          case "ready":
          case "upgrade":
          case "download":
          case "install":
          case "cancel":
          case "download-start":
            str = "<div class=\"" + val + " roundedCorners\">" + text + "</div>";
            break;

          case "installed":
          case "disabled":
            str = "<div style=\"margin-right: 0.85em; font-weight: bold; text-align: center;\">" + text + "</div>";
            break;

          default:
            str = "<div class=\"" + val + " roundedCorners\">" + text + "</div>";
            break;
        }

        return (str);
      }
    }
    ],
    tbar:[/*{
        text:'Install',
        tooltip:'Install this addon',
        //iconCls:'add',
        handler: function(b, e) {
          record = addonsGrid.getSelectionModel().getSelected();
          console.log(record.get('name') + ' ' + record.get('store'));
          installAddon(record.get('name'), record.get('store'));
        }
    },
    btnUninstall,
    '-',*/
    btnEnable,
    btnDisable,
    btnAdmin,
    '-',
    {
      id: "import-btn",
      text:"Install from file",
      tooltip:"Upload an plugin file",
      iconCls:"button_menu_ext ss_sprite ss_application_add",

      //ref: "../removeButton",
      disabled: false,
      handler: function () {
        var sw = 1;
        var msg = "";

        if (sw == 1 && PATH_PLUGINS_WRITABLE == 0) {
          sw = 0;
          msg = PATH_PLUGINS_WRITABLE_MESSAGE;
        }

        if (sw == 1) {
          addPluginWindow.show();
        }
        else {
          Ext.MessageBox.alert("Warning", msg);
        }
      }
    },
    '-',
    {
      id: 'refresh-btn',
      text:'Refresh',
      iconCls:'button_menu_ext ss_sprite ss_database_refresh',
      tooltip:'Refresh the plugins list',
      disabled: (INTERNET_CONNECTION == 1)? false : true,
      handler: function (b, e) {
        reloadTask.cancel();
        addonsStore.load({
            params: {
                "force": true
            }
        });
      }
    },
    '->',
    {
      xtype:"displayfield",
      id:'loading-indicator'
    }
    ],
    plugins: expander,
    collapsible: false,
    animCollapse: false,
    stripeRows: true,
    autoExpandColumn: 'nick-column',
    title: 'Enterprise Plugins',
    sm: new Ext.grid.RowSelectionModel({
      singleSelect:true,
      listeners: {
        selectionchange: function (sel) {
          if (sel.getCount() == 0 || sel.getSelected().get("name") == "enterprise") {
            //btnUninstall.setDisabled(true);
            btnEnable.setDisabled(true);
            btnDisable.setDisabled(true);
            btnAdmin.setDisabled(true);
          }
          else {
            record = sel.getSelected();

            //btnUninstall.setDisabled(!(record.get("status") == "installed" || record.get("status") == "upgrade" || record.get("status") == "disabled"));
            btnEnable.setDisabled(!(record.get("enabled") === false));
            btnDisable.setDisabled(!(record.get("enabled") === true));
            btnAdmin.setDisabled(!(record.get("enabled") === true));
          }
        }
      }
    }),
    //config options for stateful behavior
    stateful: true,
    stateId: "grid",
    listeners: {
      "cellclick": function (grid, rowIndex, columnIndex, e) {
        var record = grid.getStore().getAt(rowIndex);
        var fieldName = grid.getColumnModel().getDataIndex(columnIndex);
        //var data = record.get(fieldName);

        if (fieldName != "status") {
          return;
        }

        switch (record.get("status")) {
          case "upgrade":
          case "ready":
              if (INTERNET_CONNECTION == 1) {
                  installAddon(record.get("id"), record.get("store"));
              } else {
                  Ext.MessageBox.alert("Information", "Enterprise Plugins Manager no connected to internet.");
              }
              break;
          case "download":
            Ext.Ajax.request({
              url: "addonsStoreAction",
              params: {
                "action": "cancel",
                "addon": record.get("id"),
                "store": record.get("store")
                }
            });
            break;
          case "available":
            addonAvailable(record.get("id"));
            break;
        }
      }
    }
  });

  var topBox = new Ext.Panel({
    id:'main-panel-hbox',
    baseCls:'x-plain',
    layout:'hbox',
    flex: 0,
    //defaultMargins: "5",
    //autoHeight: true,
    layoutConfig: {
      align : 'stretchmax',
      pack  : 'start'
    },

    defaults: {
      frame:true,
      flex: 1,
      height: 170
    },
    items:[licensePanel, pnlSystem]
  });

  var fullBox = new Ext.Panel({
    id:'main-panel-vbox',
    baseCls:'x-plain',
    anchor: "right 100%",
    layout:'vbox',
    //padding: 10,
    //defaultMargins: "5",
    layoutConfig: {
      align : 'stretch',
      pack  : 'start'
    },

    defaults: {
      frame:true
    },
    items:[topBox, addonsGrid]
  });

  ///////
  addonsGrid.on("rowcontextmenu",
    function (grid, rowIndex, evt) {
      var sm = grid.getSelectionModel();
      sm.selectRow(rowIndex, sm.isSelected(rowIndex));
    },
    this
  );

  addonsGrid.addListener("rowcontextmenu", onMessageMnuContext, this);

  ///////
  var viewport = new Ext.Viewport({
    layout: "anchor",
    anchorSize: {
      width:800,
      height:600
    },
    items:[fullBox]
  });

  if (licensed) {
    addonsStore.load();
  }
});

