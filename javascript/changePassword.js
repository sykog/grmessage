// shows the option to change password

// waits till page is loaded
$(document).ready(function() {

    //change password button is clicked
    $("#change").click(function() {

        //$("#change").hide();
        $("#changePassword").html("<h3>Change Password</h3>" +
            "<p><strong>Current Password: </strong> <input type='password' name='currentPassword'></p>" +
            "<p><strong>New Password: </strong><input type='password' name='newPassword'></p>" +
            "<p><strong>Confirm New Password: </strong><input type='password' name='confirmPassword'></p>" +
            "<button type=\"submit\" name=\"changePassword\">Change Password</button>");

    });
});