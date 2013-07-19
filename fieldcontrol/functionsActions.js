
function openOutputsDocuments(appUid, delIndex, action)
{	//alert(appUid);
		if (summaryWindowOpened) {
		      return;
		}
		summaryWindowOpened = true;
		    Ext.Ajax.request({
		      url : '../appProxy/requestOpenSummary',
		      params : {
		        appUid  : appUid,
		        delIndex: delIndex,
		        action: action
		      },
		      success: function (result, request) {
		        var response = Ext.util.JSON.decode(result.responseText);
		        if (response.success) {
		          var sumaryInfPanel = PMExt.createInfoPanel('../appProxy/getSummary', {appUid: appUid, delIndex: delIndex, action: action});
		          sumaryInfPanel.setTitle(_('ID_GENERATE_INFO'));

		          var summaryWindow = new Ext.Window({
		            title:'Output Document',
		            layout: 'fit',
		            width: 600,
		            height: 450,
		            resizable: true,
		            closable: true,
		            modal: true,
		            autoScroll:true,
		            constrain: true,
		            keys: {
		              key: 27,
		              fn: function() {
		                summaryWindow.close();
		              }
		            }
		          });

		          var tabs = new Array();
		         
		          tabs.push({title: Ext.util.Format.capitalize(_('ID_GENERATED_DOCUMENTS')), bodyCfg: {
		            tag: 'iframe',
		            id: 'summaryIFrame',
		            src: '../cases/ajaxListener?action=generatedDocumentsSummary',
		            style: {border: '0px none',height: '450px'},
		            onload: ''
		          }});
		          var summaryTabs = new Ext.TabPanel({
		            activeTab: 0,
		            items: tabs
		          });
		          summaryWindow.add(summaryTabs);
		          summaryWindow.doLayout();
		          summaryWindow.show();
		        }
		        else {
		          PMExt.warning(_('ID_WARNING'), response.message);
		        }
		        summaryWindowOpened = false;
		      },
		      failure: function (result, request) {
		        summaryWindowOpened = false;
		      }
		    });
	}

function test(appUid, delIndex, action)
{}