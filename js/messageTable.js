// creates and manages the data table

// data table is sorted by newest messages first
$(document).ready(function() {
    var table = $('#messagesTable').DataTable({
        "order": [[ 3, "desc" ]],
        "columnDefs": [
            { "width": "10%", "targets": 3 },
            { "width": "18%", "targets": 2 }
        ]
    });
});