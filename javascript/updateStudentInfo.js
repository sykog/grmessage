// shows the option to change password

// waits till page is loaded
$(document).ready(function() {

    //change password button is clicked
    $("#changePassword").click(function() {

        $("#changePassword").hide();
        $("#password").html("<h3>Change Password</h3>" +
            "<p><strong>Current Password: </strong> <input type='password' name='currentPassword'></p>" +
            "<p><strong>New Password: </strong><input type='password' name='newPassword'></p>" +
            "<p><strong>Confirm New Password: </strong><input type='password' name='confirmPassword'></p>" +
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
    $("#editPersonalEmail").click(function() {
        $("#updateInfo").html("<hr><p><strong>Personal Email: </strong> <input type='text' name='newPersonalEmail' id='newPersonalEmail'>" +
            "<button type=\"submit\" id='updatePersonalEmail' name=\"updatePersonalEmail\">Update Info</button>")
    });

});