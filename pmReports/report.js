new Ext.KeyMap(document, {
  key: Ext.EventObject.F5,
  fn: function (keycode, e) {
    if (!e.ctrlKey) {
      if (Ext.isIE) {
        // IE6 doesn't allow cancellation of the F5 key, so trick it into
        // thinking some other key was pressed (backspace in this case)
         e.browserEvent.keyCode = 8;
      }
      e.stopEvent();
      //document.location = document.location;
      //Ext.getCmp('storeConsolidatedGrid').reload();
      storeConsolidated.reload();
    }
    else
      Ext.Msg.alert(TRANSLATIONS.LABEL_REFRESH, TRANSLATIONS.MESSAGE_REFRESH);
  }
});

/*** global variables **/
var storeConsolidated;
var storeConsolidatedAllRecords = [];
var storeAllRecords = [];
var toolbarconsolidated;
var consolidatedGrid;
var grid;
var winFilter = "";
var totalCountAll;
var pmrUidDownloadAll;
var proUidDownloadAll;
var totalProperty;
var dataResponse
/** */

//Funcion que genera un grid con datos obtenidos de un request en Ajax
function generateGrid(panelId, pmrUid, proUid)
{  //Comienzo del request en ajax
  try{
    Ext.Ajax.timeout = 60000;

    Ext.Ajax.request({
      url: 'proxyGenerateGrid',
      params: {xaction: '', pmrUid: pmrUid, proUid : proUid},
      waitMsg : 'Loading, please wait...',
      success: function (response) {
        //Obtenemos el column model, el field reader y los filtros del grid generado
        dataResponse   = Ext.util.JSON.decode(response.responseText);
        var viewConfigObject;
        var filterSerialData = '';
        var textArea = dataResponse.hasTextArea;

        if (textArea == false) {
          viewConfigObject = { forceFit:true };
        }
        else {
          viewConfigObject = {
            forceFit:true,
            enableRowBody:true,
            showPreview:true,
            getRowClass : function (record, rowIndex, p, store) {
              if (this.showPreview) {
                p.body = '<p><br></p>';
                return 'x-grid3-row-expanded';
              }
              return 'x-grid3-row-collapsed';
            }
          };
        }

        //dataStore remoto
        storeConsolidated = new Ext.data.Store({
          storeId: 'storeConsolidatedData',
          remoteSort: true,
          //Definimos un proxy como un objeto de la clase HttpProxy
          proxy: new Ext.data.HttpProxy({
            url: '../pmReports/proxyReport',
            api: {read: '../pmReports/proxyReport'}
          }),
          //El data reader obtiene los reader fields de la consulta en ajax
          reader: new Ext.data.JsonReader({
            fields: dataResponse.readerFields,
            totalProperty: 'totalCount',
            //successProperty: 'success',
            idProperty: 'APP_UID',
            root: 'data',
            messageProperty: 'message'
          }),
          //El data writer es un objeto generico pero q permitira a futuro el escribir los datos al servidor mediante el proxy
          writer: new Ext.data.JsonWriter({
            encode: true,
            writeAllFields: false
          }),  // <-- plug a DataWriter into the store just as you would a Reader
          autoSave: true, // <-- false would delay executing create, update, destroy requests until specifically told to do so with some [save] buton.
                           // el ordenamiento para los campos posiblemente este tenga q ser el tercer dato obtenido del proxy dado q los listados son muy cambiantes de tarea en tarea
          sortInfo: {field: 'APP_CACHE_VIEW.APP_NUMBER', direction: "DESC"}
          //sortInfo:{field: 'APP_CACHE_VIEW.APP_NUMBER'}
        });

        //whenever the grid data is loaded the report excel data is created
        storeConsolidated.on('load', function (store, records, opts) {
          storeConsolidatedAllRecords['data'] = '[';
          var tempStore = storeConsolidated.getRange();
          var count = 0;

          for (var i in tempStore) {
            if (typeof(tempStore[i]) == 'object') {
              if (count==0) {
                storeConsolidatedAllRecords['data'] = storeConsolidatedAllRecords['data'] + Ext.encode(tempStore[i].data);
              }
              else {
                storeConsolidatedAllRecords['data'] = storeConsolidatedAllRecords['data'] + ',' + Ext.encode(tempStore[i].data);
              }
              count++;
            }
          }

         storeConsolidatedAllRecords['data'] = storeConsolidatedAllRecords['data'] + ']';
         var reader = this.reader;
         var jsonData = reader.jsonData;
         //console.dir(jsonData);
         totalCountAll  = jsonData['totalCount'];
        });

        //carga de datos del data store via un request en Ajax
        //storeConsolidated.load();
        //ejemplo de un load con parametros para un data store
        //storeConsolidated.load({params:{ start : 0 , limit : pageSize , action: 'todo'}});
        //definicion del column model basados en la respuesta del servidor

        //inicializamos los filtros
        var filterFields = dataResponse.filterFields;

        //si existe la ventana es destruida, para evitar conflictos de visualizacion entre ventanas de distintos tabs
        if (winFilter != "") {
          winFilter.destroy();
        }

        //Ventana de filtros
        winFilter = new Ext.Window({
          //layout  : 'fit',
          width: 550, //tamaño de la ventana de Filtro
          height: 250,
          closeAction: 'hide',
          plain: true,
          autoScroll: true,
          //Formulario embebido en el panel
          items: new Ext.FormPanel({
            labelWidth: 120,
            id : 'filterForm',
            frame:true,
            title: 'Filters',
            bodyStyle:'padding:5px 5px 0',
            width: 510,
            defaults: {width: 230},
            defaultType: 'textfield',
            //Se definen los elementos del form
            items: [filterFields]
          }),
          //botones
          buttons: [
            {text: 'Filter',
             handler: function () {
               var filterForm = Ext.getCmp('filterForm').getForm().getValues(false);

               //inicializa los filtros
               filterSerialData = Ext.util.JSON.encode(filterForm);
               winFilter.hide();

               //si el filtro ha sido incializado
               if (filterSerialData != '') {
                 storeConsolidated.setBaseParam('filterList', filterSerialData);
               }

               //recargar el data store
               storeConsolidated.load();
             }
            },
            {text: 'Close',
             handler: function () {
               winFilter.hide();
             }
            }
          ]
        });

        var cm = new Ext.grid.ColumnModel(dataResponse.columnModel);
        //generacion del grid basados en los atributos definidos con anterioridad

        storeConsolidated.setBaseParam("start", 0);
        storeConsolidated.setBaseParam("limit", 20);//limite de la paginacion
        storeConsolidated.setBaseParam("pmrUid", pmrUid);
        storeConsolidated.setBaseParam("proUid", proUid);
        //storeConsolidated.setBaseParam("filterList", filterSerialData);
        storeConsolidated.setBaseParam("comboBoxList", Ext.util.JSON.encode(dataResponse.comboBoxList));

        storeConsolidated.load();

        storeConsolidatedAllRecords['pmrUid'] = pmrUid;
        pmrUidDownloadAll = pmrUid;
        proUidDownloadAll = proUid;

        consolidatedGrid = new Ext.grid.GridPanel({
          region: 'center',
          id: 'casesGrid',
          store: storeConsolidated,
          cm: cm,
          //autoHeight: true,
          //height: 1000,
          //layout: 'fit',
          //viewConfig: viewConfigObject,
          tbar: new Ext.Toolbar({
            width : '100%',
            dock: 'top',
            height: 33,
            items: [toolbarconsolidated]
          }),
          bbar: new Ext.PagingToolbar({
            pageSize: 20,
            store: storeConsolidated,
            displayInfo: true,
            //displayMsg: 'Displaying items {0} - {1} of {2} ' + ' &nbsp; ' ,
            displayMsg: TRANSLATIONS.LABEL_DISPLAY_ITEMS + ' &nbsp; ',
            emptyMsg: TRANSLATIONS.LABEL_DISPLAY_EMPTY
          })
        });

        //remocion de todos los elementos del panel principal donde se carga el grid
        Ext.getCmp(panelId).removeAll();
        //adicion del grid definido con anterioridad
        Ext.getCmp(panelId).add(consolidatedGrid);
        //recarga de los elementos del grid, para su visualizacion.
        Ext.getCmp(panelId).doLayout();

        ///////
        var tbAux = consolidatedGrid.getTopToolbar();

        var tbVisible = (filterFields.length > 0)? true : false;
        //tbAux.getComponent(1).setVisible(tbVisible);
        //tbAux.getComponent(2).setVisible(tbVisible);
        //tbAux.getComponent(3).setVisible(tbVisible);
        /*tbAux.getComponent(4).setVisible(tbVisible);
        tbAux.getComponent(5).setVisible(tbVisible);*/
   //     var tbDownloadAll = (totalCountAll>20)? true : false;
   //     tbAux.getComponent(3).setVisible(tbDownloadAll);
   //     tbAux.getComponent(4).setVisible(tbDownloadAll);
        ///////
      },
      //En caso de fallo ejecutar la siguiente funcion.
      failure: function () {
        Ext.MessageBox.show({
          title: 'Error',
          msg: 'Time out, the server response is taking too long.',
          buttons: Ext.MessageBox.OK,
          icon: Ext.MessageBox.ERROR
        });
      }
    });
    //Fin del request en ajax
  }
  catch (error) {
    alert('->' + error + '<-');
  }
}

Ext.onReady(function () {
  parent._action = action;

  switch (action) {
    case "consolidated":
      menuItems = [];
    break;
    default:
      menuItems = []
  }

  var tabs = new Ext.TabPanel({
    autoWidth: true,
    enableTabScroll: true,
    activeTab: 0,
    //resizeTabs:true,
    defaults: {autoScroll: true},
    items: eval(Items),
    plugins: new Ext.ux.TabCloseMenu()
  });

  //Create the Download button and add it to the top toolbar
  var exportButton = new Ext.Button({
    text: "Export Page to Excel File",
    handler: function () {
      var con = 0;
      var con2 = 0;
      var Get = "?BSR=777";
      var IdHeaders = "";

      while (consolidatedGrid.colModel.config[con]) {
        if (consolidatedGrid.colModel.config[con].hidden) {
          Get = Get + "&VAR_" + con2 + "=" + consolidatedGrid.colModel.config[con].header;
          con2++;
        }

        IdHeaders = IdHeaders +  "&VARS___" + consolidatedGrid.colModel.config[con].dataIndex + "=" + encodeURIComponent(consolidatedGrid.colModel.config[con].header);
        con++;
      }

      Ext.Ajax.request({
        url: "proxyExcelGenerate" + Get + IdHeaders,
        params: {data: storeConsolidatedAllRecords["data"], pmrUid: storeConsolidatedAllRecords["pmrUid"]},
        success: function (response) {
          window.location.href = "rptDownload?sPmrUid=" + storeConsolidatedAllRecords["pmrUid"] + "&r=" + Math.floor(Math.random() * 100000);
        }
      });
    }
  });

  //Create the Download button All
  var exportButtonAll = new Ext.Button({
    text: "Export All to Excel File",
    id:"exportButtonAll",
    handler: function () {
      var con = 0;
      var con2 = 0;
      var Get = "?BSR=777";
      var IdHeaders = "";

      while (consolidatedGrid.colModel.config[con]) {
        if (consolidatedGrid.colModel.config[con].hidden) {
          Get = Get + "&VAR_" + con2 + "=" + consolidatedGrid.colModel.config[con].header;
          con2++;
        }
        IdHeaders = IdHeaders +  "&VARS___" + consolidatedGrid.colModel.config[con].dataIndex + "=" + encodeURIComponent(consolidatedGrid.colModel.config[con].header);
        con++;
      }
      var storeAll = new Ext.data.Store({
        storeId: 'storeAll',
        remoteSort: true,
        proxy: new Ext.data.HttpProxy({
          url: '../pmReports/proxyReport',
          api: {read: '../pmReports/proxyReport'}
        }),
        reader: new Ext.data.JsonReader({
          fields: dataResponse.readerFields,
          totalProperty: 'totalCount',
          idProperty: 'APP_UID',
          root: 'data',
          messageProperty: 'message'
        }),
        writer: new Ext.data.JsonWriter({
          encode: true,
          writeAllFields: false
        }),
        autoSave: true,
        sortInfo: {field: 'APP_CACHE_VIEW.APP_NUMBER', direction: "DESC"}
      });

      storeAll.setBaseParam("start", 0);
      storeAll.setBaseParam("limit",totalCountAll);
      storeAll.setBaseParam("pmrUid", pmrUidDownloadAll);
      storeAll.setBaseParam("proUid", proUidDownloadAll);
      //storeAll.setBaseParam("comboBoxList", Ext.util.JSON.encode(dataResponse.comboBoxList));

      storeAll.load();
      storeAll.on('load', function (store, records, opts) {
        storeAllRecords['data'] = '[';
        var tempStoreAll = storeAll.getRange();
        var count = 0;
        for (var i in tempStoreAll) {
          if (typeof(tempStoreAll[i]) == 'object') {
            if (count==0) {
              storeAllRecords['data'] = storeAllRecords['data'] + Ext.encode(tempStoreAll[i].data);
            }
            else {
              storeAllRecords['data'] = storeAllRecords['data'] + ',' + Ext.encode(tempStoreAll[i].data);
            }
            count++;
          }
        }
        storeAllRecords['data'] = storeAllRecords['data'] + ']';
        Ext.Ajax.request({
          url: "proxyExcelGenerate" + Get + IdHeaders,
          params: {data: storeAllRecords["data"], pmrUid: pmrUidDownloadAll},
          success: function (response) {
            window.location.href = "rptDownload?sPmrUid=" + pmrUidDownloadAll + "&r=" + Math.floor(Math.random() * 100000);
          }
        });
      });
    }
  });

  //Setting the filters button
  var filterButton = new Ext.Button({
    text: "Filter by:",
    handler: function () {
      winFilter.show();
    }
  });

  var filterResetButton = new Ext.Button({
    text: "Filter Reset",
    handler: function () {
      storeConsolidated.setBaseParam("filterList", "");
      storeConsolidated.load();
    }
  });

  toolbarconsolidated = [
    exportButton,
    "-",
    exportButtonAll/*,
    "-",
    filterButton,
    "-",
    filterResetButton,
    "->" //Begin using the right-justified button container*/
  ];

  var viewport = new Ext.Viewport({
    //layout: "border",
    layout: "fit",

    autoScroll: true,
    id: "viewportcases",
    items: [tabs]
  });

  //routine to hide the debug panel if it is open
  if (parent.PANEL_EAST_OPEN) {
    parent.PANEL_EAST_OPEN = false;
    var debugPanel = parent.Ext.getCmp("debugPanel");
    debugPanel.hide();
    debugPanel.ownerCt.doLayout();
  }

  _nodeId = "";
  switch (action) {
    case "consolidated":
      _nodeId = "ID_REPORTS";
      break;
  }

  if( _nodeId != "" ){
    treePanel1 = parent.Ext.getCmp("tree-panel")
    if (treePanel1)
      node = treePanel1.getNodeById(_nodeId);
    if (node)
      node.select();
  }

  //parent.updateCasesView();
  parent.updateCasesTree();

  //Add the additional "advanced" VTypes -- [Begin]
  Ext.apply(Ext.form.VTypes, {
    daterange: function (val, field) {
    var date = field.parseDate(val);

    if (!date) {
      return;
    }

    if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
      var start = Ext.getCmp(field.startDateField);
      start.setMaxValue(date);
      start.validate();
      this.dateRangeMax = date;
    }
    else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
      var end = Ext.getCmp(field.endDateField);
      end.setMinValue(date);
      end.validate();
      this.dateRangeMin = date;
    }

    /*
    * Always return true since we're only using this vtype to set the
    * min/max allowed values (these are tested for after the vtype test)
    */

    return true;
  }
});

//Add the additional "advanced" VTypes -- [End]
});