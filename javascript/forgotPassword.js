// shows the option to change password

// waits till page is loaded
$(document).ready(function() {

    //change password button is clicked
    $("#reset").click(function() {

        console.log("its working");

        //$("#change").hide();
        $("#myModal").modal('show');

    });
});