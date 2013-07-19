/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function(){
    var options = {
        transitionEffect:"slideleft",
        enableFinishButton:true
    };
    $("#wizard").smartWizard(options);
    $("#workSpaceOrigin").html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="example"></table>');
    $("#example").dataTable({
       "aaData": loadData(),
       "aoColumns":loadColumn()
    });
    
});
function loadData(){
    return [
      ["ArkhamAsylumn","2011"],
      ["BibliotecaDeAlejandría","2011"],
      ["ChronSpace","2011"],
      ["DumasClub","2011"],
      ["MyscatonicUniversity","2011"],
      ["WorkToMan","2011"],
      ["dublin","2011"],
      ["formshare","2011"],
      ["formshareNew","2011"],
      ["importusps","2011"],
      ["mementovacui","2011"],
      ["newdumas","2011"],
      ["requisition","2011"],
      ["requisitionNew","2011"],
      ["springfieldls","2011"],
      ["template","2011"],
      ["template_test2","2011"],
      ["testWorkspace","2011"],
      ["testGround","2011"],
      ["workflow","2011"],
      ["zema","2011"],
      ["zuma","2011"]
    ];
}
function loadColumn(){
    return [
      {"sTitle":"Workspace Name","sClass":"center"},
      {"sTitle":"Process","sClass":"center"}
    ];
}