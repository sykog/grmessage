// creates and manages the data table

$(document).ready(function() {
    var table = $('#messagesTable').DataTable({
        "order": [[ 3, "desc" ]],
        "columnDefs": [
            { "width": "10%", "targets": 3 },
            { "width": "18%", "targets": 2 }
        ]
    });
});