$(document).ready(function () {

    // variable to track which row is selected
    var currentRow = null;
    var currentWksName = '';

    // create the datatable with the list of workspaces
    var oDataTable = $('#workspace-table').dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "bJQueryUI": true,
        "sAjaxSource": "workspaceListManagement.php",
        "aoColumns": [
                      { "bSortable": true },
                      { "bSortable": false },
                      { "bSortable": false },
                      { "bSortable": false },
                      { "bSortable": false },
                      { "bSortable": false }
                    ],
        "fnDrawCallback": function (oSettings) {

            // forget what row was selected when redrawing the screen
            currentRow = null;

            this.$("td").bind('click', function () {

                // remove the selected class from the current row
                if (currentRow != null) {
                    $(currentRow).removeClass("row_selected");
                }

                // find the new selected row.
                currentRow = $(this).parent("tr").first();
                $(currentRow).addClass("row_selected");
                currentWksName = $(currentRow).find("input[name=workspace-name]").val();
                // check the status of the workspace to display the appropriate button
                // between enable and disable
                var currentStatus = $(currentRow).find("input[name=status]").val();
                if (currentStatus === '1') {
                    $("#enable-workspace-button").parent().hide();
                    $("#disable-workspace-button").parent().show();
                } else {
                    $("#enable-workspace-button").parent().show();
                    $("#disable-workspace-button").parent().hide();
                }

            });

        }
    });

    // initialize the accordion used to make the operation log collapsible:
    $("#log-accordion").accordion({});

    // object used to handle the "in progress" dialog
    var inProgressDlg = function () {

        var oMgr = {};
        var readyToClose = false;

        // method used to display the dialog
        oMgr.show = function () {

            readyToClose = false;
            $("#loading-dialog").dialog({
                height: 175,
                modal: true,
                title: "Please wait",
                draggable: false,
                resizable: false,
                beforeClose: function (event, ui) {
                    return readyToClose;
                }
            })
        };

        // method used to hide the dialog
        oMgr.close = function () {
            readyToClose = true;
            $("#loading-dialog").dialog("close");
        }

        // returns the object created in this function
        return oMgr;

    } (); // force execution of this method to create the "singleton" 

    // function to display the warning when no workspace is selected
    var displayNeedToSelectWksDlg = function () {
        $("#needs-to-select-wks-dialog").dialog({
            height: 150,
            modal: true,
            title: "Select a workspace",
            draggable: false,
            resizable: false
        });
    };


    // object used to deal with operations that generate a "log", like a backup, restore, clone ...
    var operationWithLogDlg = function () {

        var oMgr = {};

        // function to display the log
        var convertLog = function (logLines) {

            $("#operation-log-input").html('');
            $.each(logLines, function (index, line) {
                var newLine = $("<p></p>");
                $(newLine).text(line);
                $("#operation-log-input").append(newLine);
            });

        };

        // expects a dialogInfo object with the properties:
        // id => id of the dialog <div>
        // width => width for the dialog
        // height => height for the dialog
        // title => title for the dialog
        // setup => function() that initializes the dialog
        // needsWks => true if a workspace needs to be selected.
        // url => name of the ajax method 
        // type => type of ajax call "GET"/"POST"
        // validate => function that verifies the data before making the call
        // data => an object or a function() that returns the data to send to the ajax function
        // redraw => if the datatable needs to redraw on success.
        oMgr.DisplayDialog = function (dialogInfo) {

            if (dialogInfo.needsWks === true && currentRow === null) {
                displayNeedToSelectWksDlg();
                return;
            }

            // Call the setup method.
            if (typeof dialogInfo.setup !== undefined) {
                var setupResult = dialogInfo.setup();
                if (setupResult === false) {
                    return;
                }
            }

            // Display the dialog
            $("#" + dialogInfo.id).dialog({
                width: dialogInfo.width,
                height: dialogInfo.height,
                title: dialogInfo.title,
                buttons: [
                    {
                        text: 'OK',
                        click: function () {

                            // execute validation, if any
                            if (dialogInfo.hasOwnProperty('validate')) {
                                var isValid = dialogInfo.validate();
                                if (isValid !== true) {
                                    return;
                                }
                            }

                            // close current dialog and show the inProgress dialog
                            $("#" + dialogInfo.id).dialog("close");
                            inProgressDlg.show();

                            // perform the ajax call
                            $.ajax({
                                url: dialogInfo.url,
                                type: dialogInfo.type,

                                // retrieve the data for the call using the function provided
                                data: dialogInfo.data(),

                                // if successful display the success message and the log
                                success: function (data) {
                                    // console.log(data);
                                    var info = $.parseJSON(data);
                                    if (info.result === true) {
                                        $("#operation-was-successful").show();
                                        $("#operation-was-not-successful").hide();
                                    } else {
                                        $("#operation-was-successful").hide();
                                        $("#operation-was-not-successful").show();
                                    }
                                    // $("#operation-log-input").val(info.log);
                                    convertLog(info.log);

                                    if (dialogInfo.redraw === true) {
                                        oDataTable.fnDraw();
                                    }
                                },
                                error: function () {
                                    console.log('error!');
                                },
                                complete: function () {

                                    // Display the dialog with the result of the operation and the log
                                    inProgressDlg.close();
                                    $("#operation-completed-dialog").dialog({
                                        height: 400,
                                        width: 600,
                                        title: "Operation Completed",
                                        modal: true,
                                        draggable: false,
                                        resizable: false
                                    });
                                }
                            });

                        }
                    },
                    {
                        text: 'Cancel',
                        click: function () {
                            $("#" + dialogInfo.id).dialog("close");
                        }
                    }
                ]
            });

        }

        return oMgr;

    } ();


    // function used to load drop downs with the list of workspace backups
    // expects a config object with the properties:
    // dropDownID : id of the drop down where the options will be appended
    // wksName: name of the workspace
    var loadBackupsInto = function (config) {

        var loadSucceeded = true;
        $.ajax({
            url: "workspaceAjaxManagement",
            type: "POST",
            data: {
                operation: 'list-backups',
                wksName: config.wksName
            },
            async: false,
            success: function (data) {
                var backupArray = $.parseJSON(data);

                // create an <option> for each element
                $.each(backupArray, function (key, object) {
                    var optionElement = $("<option></option>");
                    optionElement.attr('value', object.file);
                    optionElement.html(object.name);
                    $("#" + config.dropDownID).append(optionElement);
                });
            },
            error: function () {
                console.log('error!');
                loadSucceeded = false;
            }
        });

        return loadSucceeded;
    }


    // link the buttons click events
    $("#backup-workspace-button").click(function () {

        operationWithLogDlg.DisplayDialog({

            id: 'backup-workspace-dialog',
            width: 400,
            height: 275,
            title: "Backup Workspace",
            needsWks: true,
            setup: function () {
                // copy the name of the workspace to the dialog:
                var wksName = currentWksName;
                $("#backup-name").val('');
                $("#backup-workspace-name").text(wksName);
            },
            validate: function () {
                if ($.trim($("#backup-name").val()) === '') {
                    $("#backup-name").addClass("required-field");
                    return false;
                }
                return true;
            },
            url: "workspaceAjaxManagement",
            type: "POST",
            data: function () {
                return {
                    operation: 'backup',
                    wksName: currentWksName,
                    backupName: $("#backup-name").val()
                };
            }

        });
    });

    // restore workspaces
    $("#restore-workspace-button").click(function () {

        // retrieve the list of backups
        var wksName = currentWksName;
        // copy the name of the workspace to the dialog:
        $("#restore-workspace-name").text(wksName);
        // empty the restore list
        $("#restore-backup-list").html('');
        var setupSucceeded = loadBackupsInto({
            dropDownID: 'restore-backup-list',
            wksName: wksName
        });
        if (setupSucceeded === false) {
            return;
        }

        // verify there is at least one backup to restore from.
        if ($("#restore-backup-list option").length === 0) {
            $("#no-backups-found-dialog").dialog({
                width: 300,
                height: 150,
                draggable: false,
                modal: true,
                resizable: false
            });
            return;
        }

        operationWithLogDlg.DisplayDialog({

            id: 'restore-workspace-dialog',
            width: 600,
            height: 300,
            title: "Restore Workspace",
            needsWks: true,
            setup: function () {



                return setupSucceeded;
            },
            url: "workspaceAjaxManagement",
            type: "POST",
            data: function () {

                var wksName = currentWksName;
                var restoreFile = $("#restore-backup-list").val();
                return {
                    operation: 'restore',
                    wksName: wksName,
                    backupFile: restoreFile
                };
            }

        });

    });

    // enable or disable setting a new workspace when cloning, depending on the value of the target workspace
    $("#clone-target-list").change(function () {

        if ($(this).val() === '') {
            $("#clone-new-workspace").show();
        } else {
            $("#clone-new-workspace").hide();
            $("#clone-new-workspace").val('');
        }

    });

    // clone a workspace
    $("#clone-workspace-button").click(function () {

        operationWithLogDlg.DisplayDialog({

            id: 'clone-workspace-dialog',
            width: 600,
            height: 450,
            title: "Clone Workspace",
            needsWks: true,
            setup: function () {

                var wksName = $(currentRow).find("input").val();
                // copy the name of the workspace to the dialog:
                $("#clone-workspace-name").html(wksName);
                // empty the backup list
                $("#clone-backup-list").html('<option value="CURRENT_VERSION">- Current Version -</option>');
                var setupSucceeded = loadBackupsInto({
                    dropDownID: 'clone-backup-list',
                    wksName: wksName
                });

                if (setupSucceeded === false) {
                    return setupSucceeded;
                }

                // retrieve the list of workspaces
                $("#clone-target-list").html('<option value="">- New Workspace -</option>');
                $("#clone-target-list").change();
                $.ajax({
                    url: "workspaceAjaxManagement",
                    type: "POST",
                    data: { operation: 'list-workspaces' },
                    async: false,
                    success: function (data) {
                        var workspaceArray = $.parseJSON(data);

                        // create an <option> for each element
                        $.each(workspaceArray, function (key, object) {
                            var optionElement = $("<option></option>");
                            optionElement.attr('value', object.WSP_ID);
                            optionElement.html(object.WSP_ID);
                            $("#clone-target-list").append(optionElement);
                        });
                    },
                    error: function () {
                        console.log('error!');
                        setupSucceeded = false;
                    }
                });

                return setupSucceeded;
            },
            url: "workspaceAjaxManagement",
            type: "POST",
            data: function () {

                var wksName = currentWksName;
                var restoreFile = $("#clone-backup-list").val();
                var targetWks = $("#clone-target-list").val();
                var newWksName = $("#clone-new-workspace-name").val();
                return {
                    operation: 'clone',
                    wksName: wksName,
                    backupFile: restoreFile,
                    targetWks: targetWks,
                    newWksName: newWksName
                };
            },
            redraw: true,
            validate: function () {

                var validWksNameRegex = /^[a-zA-Z0-9_]+$/;
                if (!validWksNameRegex.test($("#clone-new-workspace-name").val())) {
                    $("#clone-new-wks-warning").show();
                    return false;
                }
                return true;

            }

        });

    });

    // delete a workspace
    $("#delete-workspace-button").click(function () {

        operationWithLogDlg.DisplayDialog({

            id: 'delete-workspace-dialog',
            width: 400,
            height: 200,
            title: "Delete workspace",
            needsWks: true,
            setup: function () {
                // copy the name of the workspace to the dialog:
                $("#delete-workspace-name").text(currentWksName);
            },
            url: "workspaceAjaxManagement",
            type: "POST",
            data: function () {
                return {
                    operation: 'delete',
                    wksName: $(currentRow).find("input").val()
                };
            },
            redraw: true

        });
    });

    // disable a workspace
    $("#disable-workspace-button, #enable-workspace-button").click(function () {

        // a workspace has to be selected
        if (currentRow === null) {
            displayNeedToSelectWksDlg();
            return;
        }

        $("#disable-workspace-name").text(currentWksName);

        // display the confirmation dialog
        $("#disable-workspace-dialog").dialog({
            height: 250,
            modal: true,
            title: "Change Status",
            draggable: false,
            resizeable: false,
            buttons: [
                {
                    text: "OK",
                    click: function () {
                        $("#disable-workspace-dialog").dialog("close");
                        inProgressDlg.show();
                        $.ajax({
                            url: "workspaceAjaxManagement",
                            type: "POST",
                            data: { operation: 'disable', wksName: $(currentRow).find("input").val() },
                            async: false,
                            success: function (data) {
                                inProgressDlg.close();
                                $("#disable-workspace-ok-dialog").dialog({
                                    height: 150,
                                    modal: true,
                                    draggable: false,
                                    resizeable: false
                                });
                                oDataTable.fnDraw(false);
                            },
                            error: function () {
                                inProgressDlg.close();
                                $("#disable-workspace-failed-dialog").dialog({
                                    height: 150,
                                    modal: true,
                                    draggable: false,
                                    resizeable: false
                                });
                            }
                        });

                    }
                },
                {
                    text: "Cancel",
                    click: function () {
                        $("#disable-workspace-dialog").dialog("close");
                    }
                }
            ]
        });

    });

    // refresh workspace information
    $("#refresh-workspace-stats").click(function () {

        $("#refresh-warning-dialog").dialog({
            width: 300,
            height: 220,
            resizable: false,
            draggable: false,
            buttons: [
                {
                    text: "No",
                    click: function () {
                        $("#refresh-warning-dialog").dialog("close");
                    }
                },
                {
                    text: "Yes",
                    click: function() {
                        $("#refresh-warning-dialog").dialog("close");
                        inProgressDlg.show();
                        $.ajax({
                            url: "workspaceAjaxManagement",
                            type: "POST",
                            data: { operation: 'refresh', wksName: $(currentRow).find("input").val() },
                            async: true,
                            success: function (data) {
                                oDataTable.fnDraw(false);
                            },
                            error: function () {
                            },
                            complete: function () {
                                inProgressDlg.close();
                            }
                        });
                    }
                }
            ]
        });

        return false;

    });

});