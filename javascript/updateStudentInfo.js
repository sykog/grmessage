// shows the option to change password

// waits till page is loaded
$(document).ready(function() {

    $("#newPhone").mask("(999) 999-9999");

    $(".sameLine").hide();
    $(".update").hide();
    $("#cancelInput").hide();

    //change password button is clicked
    $("#changePassword").click(function() {

        $("#changePassword").hide();
        $("#password").html("<h3>Change Password</h3>" +
            "<p><strong>Current Password: </strong> <input class=\"form-control\" type='password' name='currentPassword'></p>" +
            "<p><strong>New Password: </strong><input class=\"form-control\" type='password' name='newPassword'></p>" +
            "<p><strong>Confirm New Password: </strong><input class=\"form-control\" type='password' name='confirmPassword'></p>" +
            "<button type=\"submit\" id='updatePassword' name=\"updatePassword\">Change Password</button>" +
            "<button type=\"button\" id='cancelPassword' name=\"cancelPassword\">Cancel</button>");

        //cancel password is clicked
        $("#cancelPassword").click(function() {
            //clear password div, show change password button
            $("#cancelPassword").hide();
            $("#updatePassword").hide();
            $("#password").html("");
            $("#changePassword").show();

        });
    });

    //edit personal email
    $("#editName").click(function() {
        $(this).hide();
        $("#nameSpan").hide();

        $('#newFName').show();
        $('#newLName').show();
        $("#updateName").show();
    });

    //edit personal email
    $("#editPersonalEmail").click(function() {
        $(this).hide();
        $("#pEmailSpan").hide();

        $('#newPersonalEmail').show();
        $("#updatePersonalEmail").show();
    });

    //edit phone number
    $("#editPhone").click(function() {
        $(this).hide();
        $("#phoneSpan").hide();

        $('#newPhone').show();
        $("#updatePhone").show();
    });

    $("#editCarrier").click(function () {
        $(this).hide();
        $("#carrierSpan").hide();

        $("#newCarrier").show();
        $("#updateCarrier").show();
    })

    $("#editProgram").click(function () {
        $(this).hide();
        $("#programSpan").hide();

        $("#newProgram").show();
        $("#updateProgram").show();
    })

});

function showUpdate() {
    $("#update").show();
};