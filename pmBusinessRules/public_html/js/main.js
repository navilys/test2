$(document).ready(init);

function init()
{
    initComponents();
}

function initComponents()
{

    $('#rules_table').dataTable({
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        "bLengthChange": false,
        "bFilter": false
    });

    $('.x-action-new').on('click', newRule);
    $('.x-action-import').on('click', importRule);
    $('.x-action-globals').on('click', showGlobals);
    $('.x-action-dbcnn').on('click', showDbConnections);
}

function newRule()
{
    var panel = new HtmlPanel();
    var content = $('#new_rule_panel').tmpl({}).html();

    panel.setSource(content);

    var win = new Window({
        width: 730,
        height: 400,
        modal: true,
        title: '',
        closeButton: false
    });
    win.addPanel(panel);
    win.show();

    $('.x-action-cancel-new').on('click', function() {
        win.close();
        return false;
    });

    $('.x-action-save-new').on('click', saveNew);

    return false;
}

function importRule()
{
    var panel = new HtmlPanel();
    var content = $('#import_panel').tmpl({}).html();

    panel.setSource(content);

    var win = new Window({
        width: 640,
        height: 300,
        modal: true,
        title: '',
        closeButton: true
    });
    win.addPanel(panel);
    win.show();

    return false;
}

var nEditing = null;
var table1;

function showGlobals()
{
    var panel = new HtmlPanel();
    var content = $('#globals_panel').tmpl({name:'erik'}).html();

    panel.setSource(content);

    var win = new Window({
        width: 600,
        height: 455,
        modal: true,
        title: '',
        closeButton: false
    });
    win.addPanel(panel);
    win.show();

    table1 = $('#globals_table').dataTable({
        "bProcessing": true,
        "bServerSide": false,
        "sAjaxSource": "controllers/businessRulesProxy?functionExecute=getGlobals",
        "sScrollY": "250px",
        "bPaginate": false,
        "bScrollCollapse": true,
        "bSort": true,
        "aoColumns": [
            { "mData": "GF_NAME" },
            { "mData": "GF_VALUE" },
            { "mData": "GF_TYPE_LABEL" },
            { "mData": "_options" }
        ]
    });

    $('.dropdown-menu').dropdown('toggle');

    $('.x-action-cancel-globals').on('click', function() {
        win.close();
    });

    //////
    //

    $('.x-action-add-globals').live('click', addGlobals);

    $('#globals_table .x-g-edit').live('click', function (e) {
        e.preventDefault();

        /* Get the row as a parent of the link that was clicked on */
        var nRow = $(this).parents('tr')[0];

        if ( nEditing !== null && nEditing != nRow ) {
            /* A different row is being edited - the edit should be cancelled and this row edited */
            cancelEditRow(table1, nEditing);
            editRow( table1, nRow );
            nEditing = nRow;
        } else {
            /* No row currently being edited */
            editRow( table1, nRow );
            nEditing = nRow;
        }
    });

    $('#globals_table .x-g-cancel').live('click', function (e) {
        e.preventDefault();
        var nRow = $(this).parents('tr')[0];

        cancelEditRow(table1, nRow);
    });

    $('#globals_table .x-g-save').live('click', function (e) {
        e.preventDefault();
        var nRow = $(this).parents('tr')[0];

        saveRow(table1, nRow);
    });

    $('#globals_table .x-g-delete').live('click', deleteGlobals);
    $('#globals_table .x-g-dbquery').live('click', setDbQueryGlobals);



    return false;
}

// start globals vars functions
//
function addGlobals()
{
    var _options = '<td class=""><button title="Edit" class="btn btn-mini x-g-edit"><i class="icon-pencil"></i></button> <button disabled="" title="Set DB Query" class="btn btn-mini"><i class="icon-random"></i></button> <button title="Delete" data-id="ewe" class="btn btn-mini x-g-delete"><i class="icon-trash"></i></button></td>';
    $('#globals_table').dataTable().fnAddData({
        GF_NAME: '',
        GF_VALUE: '',
        GF_TYPE_LABEL: '',
        _options: _options
    });

    var nRow = $('#globals_table tr')[1];
    editRow(table1, nRow, true);
}


var queryWin;
function setDbQueryGlobals()
{
    //neyek
    var panel = new HtmlPanel();
    var gfName = $(this).attr('data-id');
    var content = $('#set_query_panel').tmpl({GF_NAME: gfName}).html();

    panel.setSource(content);

    queryWin = new Window({
        width: 600,
        height: 455,
        modal: true,
        title: '',
        closeButton: false
    });
    queryWin.addPanel(panel);
    queryWin.show();

    $('.x-query-save').on('click', saveQuery);
    $('.x-query-cancel').on('click', function(){
        queryWin.close();
    });

    $.get('controllers/businessRulesProxy', {functionExecute: 'getDdCnn'}, function(response) {
        var data = jQuery.parseJSON(response).aaData;

        $.each(data, function(key, row) {
            $('select#DBS_UID')
            .append($('<option>', { value : row.DBS_UID })
            .text(row.DBS_DATABASE_NAME + ' on ' + row.DBS_SERVER));
        });

        $.get('controllers/businessRulesProxy', {functionExecute: 'loadGlobal', GF_NAME: gfName}, function(response) {
            var data = jQuery.parseJSON(response);
            $('select#DBS_UID').val(data.DBS_UID);
            $('#GF_QUERY').val(data.GF_QUERY);
        });
    });
}

function saveQuery()
{
    $.post('controllers/businessRulesProxy', {
        functionExecute: 'saveGlobals',
        GF_NAME: $('#GF_NAME').val(),
        GF_QUERY: $('#GF_QUERY').val(),
        DBS_UID: $('#DBS_UID').val()
    }).done(function(response) {
        response = jQuery.parseJSON(response);
        alert(response.message);

        if (response.success) {
            queryWin.close();
        }
    });
}

function deleteGlobals()
{
    var name = $(this).attr('data-id');
    var nRow = $(this).parents('tr')[0];

    if (confirm('Are you sure to delete the global variable named: "'+name+'"')) {
        $.post('controllers/businessRulesProxy', {
            functionExecute: 'deleteGlobals',
            GF_NAME: name
        }).done(function(response) {
            response = jQuery.parseJSON(response);

            if (response.success) {
                table1.fnDeleteRow(nRow);
            } else {
                alert(response.message);
            }
        });
    }
}

var aData;

function editRow ( oTable, nRow, addNew )
{
    var jqTds = $('>td', nRow);
    aData = oTable.fnGetData(nRow);
    var selectOptions = '', selected = '';
    //console.log(aData);
    for(var i=0; i<globalFieldTypes.length; i++) {
        selected = globalFieldTypes[i].label == aData.GF_TYPE_LABEL ? ' selected' : '';
        selectOptions += '<option value="'+globalFieldTypes[i].id+'"'+selected+'>'+globalFieldTypes[i].label+'</option>';
    }

    jqTds[0].innerHTML = addNew ? '<input type="text" value="'+aData.GF_NAME+'" class="alpha" style="width:110px" />' : aData.GF_NAME;
    jqTds[1].innerHTML = '<input type="text" value="'+aData.GF_VALUE+'" style="width:110px" />';
    jqTds[2].innerHTML = '<select style="width:120px" class="x-global-type">'+selectOptions+'</select>';
    jqTds[3].innerHTML = '<div style="float:left"><a href="#" class="btn btn-success btn-mini x-g-save" rel="tooltip" '
                       + 'title="Guardar"><i class="icon-ok"></i></a>&nbsp;'
                       + '<a href="#" class="btn btn-mini x-g-cancel" rel="tooltip" title="Cancelar">'
                       + '<i class="icon-remove"></i></a></div>';

    if (aData.GF_TYPE_LABEL == '') {
        //$('.x-global-type').val('string');
    }

    $('input.alpha').keyup(function() {
        if (this.value.match(/[^a-zA-Z0-9_]/g)) {
            this.value = this.value.replace(/[^a-zA-Z0-9_]/g, '');
        }
    });

}

function cancelEditRow(oTable, nRow)
{
    aData = oTable.fnGetData(nRow);
    var jqTds = $('>td', nRow);

    jqTds[0].innerHTML = aData.GF_NAME;
    jqTds[1].innerHTML = aData.GF_VALUE;
    jqTds[2].innerHTML = aData.GF_TYPE_LABEL;
    jqTds[3].innerHTML = aData._options;
}

function saveRow(oTable, nRow)
{
    var inputs = $('input', nRow);
    var selects = $('select', nRow);
    var typeId = selects[0].value;

    if (inputs.length == 1) {
        var name = aData.GF_NAME;
        var value = inputs[0].value;
    } else {
        var name = inputs[0].value;
        var value = inputs[1].value;
    }

    $.post('controllers/businessRulesProxy', {
        functionExecute: 'saveGlobals',
        GF_NAME: name,
        GF_VALUE: value,
        GF_TYPE: typeId,
    }).done(function (response) {
        response = jQuery.parseJSON(response);

        if (response.success) {
            aData.GF_NAME = name;
            aData.GF_VALUE = value;
            aData.GF_TYPE = $('option:selected', selects[0]).text();
        } else {
            alert('ERROR: ' + response.message);
        }

        oTable.fnUpdate(aData.GF_NAME, nRow, 0, false);
        oTable.fnUpdate(aData.GF_VALUE, nRow, 1, false);
        oTable.fnUpdate(aData.GF_TYPE, nRow, 2, false);
        oTable.fnUpdate(aData._options, nRow, 3, false);
        oTable.fnDraw();

        if (typeId == 'query') {
            $('.x-g-dbquery', nRow).removeAttr("disabled");
        } else {
            $('.x-g-dbquery', nRow).attr("disabled", "disabled");
        }
    });

    return false;
}

function deleteRow(oTable, nRow)
{
    if ( nRow.length !== 0 ) {
        oTable.fnDeleteRow(nRow[0]);
    }
}
// end globals vars functions

function saveNew()
{
    if ($('#RST_NAME').val() == '') {
        alert('The Rule Set Name is required');
        return false;
    }

    $.post('controllers/businessRulesProxy.php', {
        RST_UID: '',
        RST_NAME: $('#RST_NAME').val(),
        RST_DESCRIPTION: $('#RST_DESCRIPTION').val(),
        RST_TYPE: $('#RST_TYPE').val(),
        PRO_UID: $('#PRO_UID').val(),
        functionExecute: 'saveRule'
    }).done(function(response) {
        response = jQuery.parseJSON(response);

        if (response.success) {
            location.href = "editor.php?id=" + response.RST_UID;
        } else {
            alert('ERROR: ' + response.message);
        }
    });

    return false;
}

function editRule(id)
{
    location.href = "editor.php?id=" + id;

    return false;
}

var w;
var currentId;
var currentRuleSetIsEditable;

function exportPmrl (id)
{
    currentId = id;
    window.location = 'controllers/businessRulesProxy.php?functionExecute=exportPmrl&id='+id

    return false;
}

function showSource(id, option)
{
    currentId = id;
    currentRuleSetIsEditable = option;
    var rc = new RestClient();

    var panel = new HtmlPanel();
    panel.setSource($('#form1').html());

    w = new Window({
        width: 730,
        height: 400,
        modal: true,
        title: '',
        closeButton: false
    });
    w.addPanel(panel);
    w.show();

    $('.x-action-cancel-popup').on('click', function() {
        w.close();
        return false;
    });

    $('.x-action-edit-popup').on('click', editSource);
    $('.x-action-save-popup').on('click', saveSource);
    $('.x-action-cancel-edit-popup').on('click', cancelEditSource);

    cancelEditSource();

    $.get('controllers/businessRulesProxy.php', {
        id: currentId,
        functionExecute: 'getRuleSetSource'
    }).done(function(data) {
        $('.pre_rule_source').text(data);
        $('.textarea_rule_source').text(data);
    });

    return false;
}

function editSource()
{
    $('.x-options-show-source').css('display', 'none');
    $('.x-options-edit-source').css('display', 'block');
    $('.pre_rule_source').css('display', 'none');
    $('.textarea_rule_source').css('display', 'block');

    return false;
}

function cancelEditSource()
{
    $('.x-options-show-source').css('display', 'block');
    $('.x-options-edit-source').css('display', 'none');
    $('.pre_rule_source').css('display', 'block');
    $('.textarea_rule_source').css('display', 'none');

    return false;
}

function saveSource()
{
    if (currentRuleSetIsEditable) {
        var ans = confirm("If you save these changes, it will won't open in editor any more. Are you sure?");

        if (ans) {
            doSaveSource({redirect: true});
        }
    } else {
        doSaveSource({redirect: false});
    }

    return false;
}

function doSaveSource(options)
{
    $.post('controllers/businessRulesProxy.php', {
        id: currentId,
        data: $('.textarea_rule_source:last').val(),
        functionExecute: 'saveSource'
    }).done(function(response){
        response = jQuery.parseJSON(response);

        if (response.success) {
            $('.pre_rule_source').text($('.textarea_rule_source:last').val());

            if (options.redirect) {
                location.href = "main.php";
            } else {
                cancelEditSource();
                alert(response.message);
            }
        } else {
            alert("ERROR: " + response.message);
        }
    });
}

function deleteRule(id)
{
    var ans = confirm("Are you sure delete the Business Rule Set?");

    if (ans) {
        $.post('controllers/businessRulesProxy.php', {
            id: id,
            functionExecute: 'deleteRule'
        }).done(function(response){
            response = jQuery.parseJSON(response);

            if (response.success) {
                location.href = "main.php";
            } else {
                alert("ERROR: " + response.message);
            }
        });
    }

    return false;
}

//
//// dbcnn
//
var tableDbCnn;
var winDbCnn;
function showDbConnections()
{
    var panel = new HtmlPanel();
    var content = $('#dbcnn_panel').tmpl({}).html();

    panel.setSource(content);

    winDbCnn = new Window({
        width: 600,
        height: 500,
        modal: true,
        title: '',
        closeButton: false
    });
    winDbCnn.addPanel(panel);
    winDbCnn.show();

    tableDbCnn = $('#dbcnn_table').dataTable({
        "bProcessing": true,
        "bServerSide": false,
        "sAjaxSource": "controllers/businessRulesProxy?functionExecute=getDdCnn",
        "sScrollY": "250px",
        "bPaginate": false,
        "bScrollCollapse": true,
        "bSort": true,
        "aoColumns": [
            { "mData": "DBS_TYPE" },
            { "mData": "DBS_SERVER" },
            { "mData": "DBS_DATABASE_NAME" },
            { "mData": "_options" }
        ]
    });

    //$('.dropdown-menu').dropdown('toggle');

    $('.x-dbcnn-cancel').on('click', function() {
        winDbCnn.close();
    });

    $('.x-dbcnn-new').on('click', newDbConnection);
}

var winNewDbCnn;
function newDbConnection()
{
    dbConnectionForm('');
}

function editDbConnection(id)
{
    $.get('controllers/businessRulesProxy', {
        functionExecute: 'loadDbCnn',
        DBS_UID: id,
    }).done(function(response) {
        response = jQuery.parseJSON(response);
        dbConnectionForm(id);
        $('#DBS_UID').val(response.DBS_UID),
        $('#DBS_TYPE').val(response.DBS_TYPE),
        $('#DBS_SERVER').val(response.DBS_SERVER),
        $('#DBS_PORT').val(response.DBS_PORT),
        $('#DBS_DATABASE_NAME').val(response.DBS_DATABASE_NAME),
        $('#DBS_USERNAME').val(response.DBS_USERNAME),
        $('#DBS_PASSWORD').val(response.DBS_PASSWORD)
    });
}

function dbConnectionForm(id)
{
    var panel = new HtmlPanel();
    var content = $('#dbcnn_new_panel').tmpl({DBS_UID:''}).html();

    panel.setSource(content);

    winNewDbCnn = new Window({
        width: 600,
        height: 500,
        modal: true,
        title: '',
        closeButton: false
    });
    winNewDbCnn.addPanel(panel);
    winNewDbCnn.show();

    $('.x-dbcnn-save').on('click', testDbConnection);
    $('.x-dbcnn-cancel').on('click', function(){
        winNewDbCnn.close();
    });
}

function testDbConnection()
{
    var saveBtnHtml = $('.x-dbcnn-save').html();

    $('.x-dbcnn-save').attr('disabled', 'disabled');
    $('.x-dbcnn-cancel').attr('disabled', 'disabled');
    $('.x-dbcnn-save').html('<img src="/plugin/pmBusinessRules/images/loading.gif"></img>Testing connection...');

    $.post('controllers/businessRulesProxy', {
        functionExecute: 'testDbCnn',
        DBS_TYPE: $('#DBS_TYPE').val(),
        DBS_SERVER: $('#DBS_SERVER').val(),
        DBS_PORT: $('#DBS_PORT').val(),
        DBS_DATABASE_NAME: $('#DBS_DATABASE_NAME').val(),
        DBS_USERNAME: $('#DBS_USERNAME').val(),
        DBS_PASSWORD: $('#DBS_PASSWORD').val()
    }).done(function(response) {
        response = jQuery.parseJSON(response);

        if (response.success) {
            doSaveDbConnection();
        } else {
            alert(response.message);
            $('.x-dbcnn-save').html(saveBtnHtml);
            $('.x-dbcnn-save').removeAttr('disabled');
            $('.x-dbcnn-cancel').removeAttr('disabled');
        }
    });
}

function doSaveDbConnection()
{
    $.post('controllers/businessRulesProxy', {
        functionExecute: 'saveDbCnn',
        DBS_UID: $('#DBS_UID').val(),
        DBS_TYPE: $('#DBS_TYPE').val(),
        DBS_SERVER: $('#DBS_SERVER').val(),
        DBS_PORT: $('#DBS_PORT').val(),
        DBS_DATABASE_NAME: $('#DBS_DATABASE_NAME').val(),
        DBS_USERNAME: $('#DBS_USERNAME').val(),
        DBS_PASSWORD: $('#DBS_PASSWORD').val()
    }).done(function(response) {
        response = jQuery.parseJSON(response);
        alert(response.message)

        if (response.success) {
            winNewDbCnn.close();
            winDbCnn.close();
            showDbConnections();
        } else {
            alert(response.message);
        }
    });
}

function deleteDbConnection(id)
{
    if (confirm("Are you to delete this Data Base Connection?")) {
        $.post('controllers/businessRulesProxy', {
            functionExecute: 'deleteDbCnn',
            DBS_UID: id,
        }).done(function(response) {
            response = jQuery.parseJSON(response);

            alert(response.message);
            if (response.success) {
                winDbCnn.close();
                showDbConnections();
            }
        });
    }
}
//
