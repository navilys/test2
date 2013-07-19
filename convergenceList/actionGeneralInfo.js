Ext.onReady(function(){      
      
      Ext.QuickTips.init();


function pp(sName,urlData){
    
    var tabPanelcaseGralInfo = parent.Ext.getCmp('tabPanelcaseGralInfo');
    
    tabPanelcaseGralInfo.add({
      id: 'iframe-' + sName,      
      title: sName,
      frameConfig:{name: sName + 'Frame', id: sName + 'Frame'},
      defaultSrc : urlData,      
      loadMask:{msg:'Loading...'},      
      autoHeigth: true,
      closable:true,
      autoScroll: true,       
      //bodyStyle:{height: (PMExt.getBrowser().screen.height-30) + 'px', overflow:'auto'},
      bodyStyle:{height: ADAPTIVEHEIGHT+'px'}
      //width:screenWidth
    }).show();
    
    tabPanelcaseGralInfo.doLayout();    

}

new Ext.Viewport({
    layout: 'border',
    items: [{
        region: 'center',
        collapsible: true,
        title: 'General Actions',
        xtype: 'treepanel',
        width: 200,
        autoScroll: true,
        split: true,
        loader: new Ext.tree.TreeLoader(),
        root: new Ext.tree.AsyncTreeNode({
            expanded: true,
            children: [{
                text: 'Case Notes',
                leaf: true,
                url: 'actions/viewCaseNotes.php?APP_UID='+APP_UID
            },{
                text: 'Historique du dossier',
                leaf: true,
                url : 'casesHistoryDynaformPage_Ajax.php?actionAjax=HistoryLog&APP_UID='+APP_UID+"&adaptiveHeight="+ADAPTIVEHEIGHT+"&num_dossier="+NUM_DOSSIER                
            }/*, {
                text: 'Dynaforms',
                leaf: true,                
                url: "DynaformsListener.php?actionType=view&appUid=" + APP_UID
            }*/]
        }),
        rootVisible: false,
        listeners: {
            click: function(n) {                
                //document.getElementById("setup-frame").src = n.attributes.url;
                pp(n.attributes.text,n.attributes.url);
            }
        }
    }]
}); 


});