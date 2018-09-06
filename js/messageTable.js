// creates and manages the data table

// data table is sorted by newest messages first
$(document).ready(function() {

    // get all programs
    var filteredPrograms = getPrograms();

    // check all checkboxes
    $("#check").click(function() {
        $("input:checkbox").prop("checked", true);
    });

    // uncheck all checkboxes
    $("#uncheck").click(function() {
        $("input:checkbox").prop("checked", false);
    });

    // adds more search features to filter by program
    $.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        // split each recipient program in an array
        var recipients = data[2].split(",");
        var programFound = false;

        // check if the program is found in the array for each recipient
        for (var i = 0; i < recipients.length; i++)  {
            if ($.inArray(recipients[i], filteredPrograms) != -1) programFound = true;
        }

        return programFound;
    });

    // initialize data table
    var table = $('#messagesTable').DataTable({
        "order": [[ 3, "desc" ]],
        "columnDefs": [
            { "width": "12%", "targets": 0 },
            { "width": "18%", "targets": 2 },
            { "width": "10%", "targets": 3 }
        ]
    });

    // update the array of marked programs
    $("input:checkbox, #check, #uncheck").click(function() {
        filteredPrograms = getPrograms();

        // recreate the data table
        table.draw();
    });

    // create an array of all marked programs
    function getPrograms() {
        var programs = new Array();
        $("input:checkbox").each(function() {
            if ($(this).prop("checked")) programs.push($(this).val());
        });
        return programs;
    }
});