// shows the option to change password

// waits till page is loaded
$(document).ready(function(){

    // hide box initially
    $("#forgotPassword").hide();

    $("button#openDialog").click(function() {
        $("#forgotPassword").show();
    });

    $("#close").click(function() {
        $("#forgotPassword").hide();
    });
});