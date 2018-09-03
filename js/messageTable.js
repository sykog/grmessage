// creates and manages the data table

// data table is sorted by newest messages first
$(document).ready(function() {

    // adds more search pfeatures to filter by program
    $.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = parseInt( $('#min').val(), 10 );
        var max = parseInt( $('#max').val(), 10 );
        var age = parseFloat( data[3] ) || 0; // use data for the age column

        if ( ( isNaN( min ) && isNaN( max ) ) ||
             ( isNaN( min ) && age <= max ) ||
             ( min <= age   && isNaN( max ) ) ||
             ( min <= age   && age <= max ) )
        {
            return true;
        }
        return false;
    });

    // initialize data table
    var table = $('#messagesTable').DataTable({
        "order": [[ 3, "desc" ]],
        "columnDefs": [
            { "width": "10%", "targets": 0 },
            { "width": "10%", "targets": 3 },
            { "width": "18%", "targets": 2 }
        ]
    });
});