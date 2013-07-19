Ext.onReady(function(){

  Ext.QuickTips.init();    
  var storeNotes;  
  var startRecord=0;
  var loadSize=10;
// ../convergenceList/actions/actionAjaxNotes.php
  storeNotes = new Ext.data.JsonStore({
      url : 'getNotesList.php?appUid='+APP_UID+'&tas=0',
      root: 'notes',
      totalProperty: 'totalCount',
      fields: ['USR_USERNAME','USR_FIRSTNAME','USR_LASTNAME','USR_FULL_NAME','NOTE_DATE','NOTE_CONTENT', 'USR_UID', 'user'],
      baseParams:{
        start:startRecord,
        limit:startRecord+loadSize
      },
      listeners:{
        load:function(){
          if(storeNotes.getCount()<storeNotes.getTotalCount()){          
            Ext.getCmp('CASES_MORE_BUTTON').show();
          }else{
            Ext.getCmp('CASES_MORE_BUTTON').hide();
          }
        }
      }
    });
    storeNotes.load();

    var textField1 = new Ext.form.TextArea({
            fieldLabel : 'Merci de saisir un nouveau commentaire',
            xtype  : 'textarea',
            id     : 'caseNoteText',
            name   : 'caseNoteText',
            width  : 300,
            grow   : true,
            height : 100,
            growMin: 40,
            growMax: 80,
            maxLengthText  : 500,
            allowBlank     :true,
            selectOnFocus  :true,
            enableKeyEvents: true          
  });

    var notesForm = new Ext.FormPanel({
      region: 'west',
          id: 'notesForm',
          frame: true,
          labelAlign: 'center',
          title: 'Formulaire',
          bodyStyle  : 'padding: 10px; background-color: #DFE8F6',        
          width: 500,
          autoHeight: true,
          layout: 'column',    // Specifies that the items will now be arranged in columns
          items: [{
              columnWidth: 1,
              xtype: 'fieldset',
              labelWidth: 120,            
              defaults: {border:false},    // Default config options for child items
              defaultType: 'textfield',
              autoHeight: true,            
              border: false,            
              items: [textField1]
          }],
          buttons : [
          {
             text    : 'Annuler',
             iconCls: 'x-btn-text button_menu_ext ss_sprite  ss_delete',
             handler : function(){
                notesForm.form.reset();            
             }
          },
          {
             text    : 'Enregistrer',
             iconCls: 'x-btn-text button_menu_ext ss_sprite ss_add',
             handler : function(){
              urlData = "actions/actionAjaxNotes.php";
              Ext.MessageBox.show({
                    msg : 'Storing Note, Please wait..',
                    progressText : 'Storing...',
                    width : 300,
                    wait : true,
                    waitConfig : {
                      interval : 200
                    }
              });    
              Ext.Ajax.request({
                 url : urlData,
                 params : {
                    options : 'save',
                    APP_UID  : APP_UID,
                    Note  :Ext.getCmp('caseNoteText').getValue()
                 },
                 success: function (result, request) {
                   var response = Ext.util.JSON.decode(result.responseText);
                   if (response.success) {
                      Ext.MessageBox.hide();
                      alert(response.messageinfo);
                      Ext.getCmp('caseNoteText').setValue('');
                      storeNotes.reload();
                   }
                   else {
                    Ext.MessageBox.hide();
                     PMExt.warning(_('ID_WARNING'), response.message);
                   }                   
                 },
                 failure: function (result, request) {
                  Ext.MessageBox.hide();                  
                 }
               });
             }
          }
          ]
      });

  var panelNotes = new Ext.Panel({
      region: 'center',
      id:'notesPanel',
      title: 'Les commentaires',
      frame:true,    
      Width:250,    
      collapsible:false,
      autoScroll: true,
      items:[
        new Ext.DataView({
          store: storeNotes,
          loadingtext:_('ID_CASE_NOTES_LOADING'),
          emptyText: _('ID_CASE_NOTES_EMPTY'),
          cls: 'x-cnotes-view',
          tpl: '<tpl for=".">' +
                  '<div class="x-cnotes-source"><table><tbody>' +
                      '<tr>' +
                        '<td class="x-cnotes-label"><img border="0" src="../users/users_ViewPhotoGrid?pUID={USR_UID}" width="40" height="40"/></td>' +
                        '<td class="x-cnotes-name">'+
                          '<p class="user-from">{user}</p>'+
                          '<p class="x-editable x-message">{NOTE_CONTENT}</p> '+
                          '<p class="x-editable"><small>'+_('ID_POSTED_AT')+'<i> {NOTE_DATE}</i></small></p>'+
                        '</td>' +
                      '</tr>' +
                  '</tbody></table></div>' +
               '</tpl>',
          itemSelector: 'div.x-cnotes-source',
          overClass: 'x-cnotes-over',
          selectedClass: 'x-cnotes-selected',
          singleSelect: true,

          prepareData: function(data){          
            data.user = _FNF(data.USR_USERNAME, data.USR_FIRSTNAME, data.USR_LASTNAME);
            data.NOTE_CONTENT = data.NOTE_CONTENT.replace(/\n/g,' <br/>');
            return data;
          },

          listeners: {
            selectionchange: {
              fn: function(dv,nodes){
                var l = nodes.length;
                var s = l != 1 ? 's' : '';
                panelNotes.setTitle('Process ('+l+' item'+s+' selected)');
              }
            },
            click: {
              fn: function(dv,nodes,a){
              }
            }
          }
        }),{
          xtype:'button',
          id:'CASES_MORE_BUTTON',
          iconCls: 'x-btn-text button_menu_ext ss_sprite ss_add',
          text:_('ID_CASE_NOTES_MORE'),
          align:'center',
          handler:function() {
            startRecord=startRecord+loadSize;
            limitRecord=startRecord+loadSize;
            storeNotes.load({
              params:{
                start:0,
                limit:startRecord+loadSize
              }
            });
          }
        }
      ]
    });

  var viewportNotes = new Ext.Viewport({
        layout: 'border',
        items: [panelNotes,notesForm]
  });

});
