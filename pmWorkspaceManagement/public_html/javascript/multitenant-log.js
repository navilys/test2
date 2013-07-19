$(document).ready(function () {

    // object with the current filters:
    var oFilter = {};

    // function to display the log details
    var convertLog = function (logLines) {

        $("#log-detail").html('');
        $.each(logLines, function (index, line) {
            var newLine = $("<p></p>");
            $(newLine).text(line);
            $("#log-detail").append(newLine);
        });

    };

    // create the datatable with the list of workspaces
    var oDataTable = $('#log-table').dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "bJQueryUI": true,
        "sAjaxSource": "multitenantLogAjax.php",
        // change the dom layout so that there is no filter box
        "aoColumns": [
                      { "bSortable": true },
                      { "bSortable": true },
                      { "bSortable": true },
                      { "bSortable": true },
                      { "bSortable": true },
                      { "bSortable": true },
                      { "bSortable": false }
                    ],
        // add the extra filters.
        "fnServerParams": function (aoData) {
            $.each(oFilter, function (key, value) {
                aoData.push({ "name": key, "value": value });
            });
        },

        // on each draw link to the clicking of a row
        "fnDrawCallback": function (oSettings) {
            this.$("td").bind('click', function () {
                // when clicking on a row, display the log details
                $.ajax({
                    url: 'multitenantLogDetails.php',
                    type: 'POST',

                    // get the value for the ID from the same row but the first cell
                    data: { logID: $(this).parent("tr").first().children("td").first().html() },

                    // if successful display the success message and the log
                    success: function (data) {

                        var logLines = $.parseJSON(data);
                        // add the details to the log
                        convertLog(logLines);
                        // display the dialog box
                        $("#log-details-dialog").dialog({
                            height: 400,
                            width: 600,
                            modal: true,
                            draggable: false,
                            resizable: false
                        });

                    },
                    error: function () {
                        console.log('Error attemptying to display the log details');
                    }
                })
            });
        }
    });

    // hide the ID column
    // oDataTable.fnSetColumnVis( 0, false );

    // set the date pickers for the date filters
    // incializar los datepickers
    $.datepicker.setDefaults($.datepicker.regional["en"]);
    $(".datepicker").datepicker($.datepicker.regional["en"]);
    $(".datepicker").datepicker("option", "dateFormat", "dd/mm/yy");
    $(".datepicker").datepicker("option", "showAnim", "slideDown");

    // when the filter button is clicked
    $("#apply-filters-button").click(function () {
        oFilter.dateFrom = $("#DateFrom").val();
        oFilter.dateTo = $("#DateTo").val();
        oFilter.action = $("#Action").val();
        oFilter.type = $("#Type").val();
        oFilter.ipaddress = $("#ipaddress").val();
        oFilter.content = $("#content").val();

        // reload data and go back to the first page
        oDataTable.fnPageChange(0);
    });

});