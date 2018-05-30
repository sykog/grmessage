// shows the option to change password

// waits till page is loaded
$(document).ready(function() {

    $("#newPhone").mask("(999) 999-9999");

    $(".sameLine").hide();
    $(".update").hide();
    $(".cancelInput").hide();
    $("#update").hide();
    $(".verify").show();

    // hide checkboxes if value are empty
    if ($("#phoneSpan").text().length <= 2) {
        $("#getTexts, #getTexts + p, p + br").remove();
        $("#carrierDiv").hide();
    }
    if ($("#pEmailSpan").text().length <= 2) $("#getPersonalEmails, #getPersonalEmails + p").remove();

    // change password button is clicked
    $("#changePassword").click(function() {

        $("#changePassword").hide();
        $("#password").html("<h3>Change Password</h3>" +
            "<p><strong>Current Password: </strong> <input class=\"form-control\" type='password' name='currentPassword'></p>" +
            "<p><strong>New Password: </strong><input class=\"form-control\" type='password' name='newPassword'></p>" +
            "<p><strong>Confirm New Password: </strong><input class=\"form-control\" type='password' name='confirmPassword'></p>" +
            "<button type=\"submit\" id='updatePassword' name=\"updatePassword\">Change Password</button>" +
            "<button type=\"button\" id='cancelPassword' name=\"cancelPassword\">Cancel</button>");

        // cancel password is clicked
        $("#cancelPassword").click(function() {
            // clear password div, show change password button
            $("#cancelPassword").hide();
            $("#updatePassword").hide();
            $("#password").html("");
            $("#changePassword").show();

        });
    });

    /****************** TOGGLING EDITING FIELDS ************************/

    // enable name editing
    $("#editName").click(function() {
        $(this).hide();
        $("#nameSpan").hide();
        $('#newFName, #newLName, #updateName, #cancelName').show();
    });
    // disable name editing
    $("#cancelName").click(function() {
        $(this).hide();
        $("#nameSpan, #editName").show();
        $('#newFName, #newLName, #updateName').hide();
    });

   // enable personal email editing
    $("#editPersonalEmail").click(function() {
        $(this).hide();
        $("#pEmailSpan").hide();
        $('#newPersonalEmail, #updatePersonalEmail, #cancelPersonalEmail').show();
    });
    // disable personalEmail editing
    $("#cancelPersonalEmail").click(function() {
        $(this).hide();
        $("#pEmailSpan, #editPersonalEmail").show();
        $('#newPersonalEmail, #newPersonalEmail, #updatePersonalEmail').hide();
    });

    // enable phone editing
    $("#editPhone").click(function() {
        $(this).hide();
        $("#phoneSpan").hide();
        $('#newPhone, #updatePhone, #cancelPhone').show();
    });
    // disable phone editing
    $("#cancelPhone").click(function() {
        $(this).hide();
        $("#phoneSpan, #editPhone").show();
        $('#newPhone, #newPhone, #updatePhone').hide();
    });

    // enable carrier editing
    $("#editCarrier").click(function () {
        $(this).hide();
        $("#carrierSpan").hide();
        $('#newCarrier, #updateCarrier, #cancelCarrier').show();
    });
    // disable carrier editing
    $("#cancelCarrier").click(function() {
        $(this).hide();
        $("#carrierSpan, #editCarrier").show();
        $('#newCarrier, #newCarrier, #updateCarrier').hide();
    });

    // enable carrier editing
    $("#editProgram").click(function () {
        $(this).hide();
        $("#programSpan").hide();
        $('#newProgram, #updateProgram, #cancelProgram').show();
    });
    // disable phone editing
    $("#cancelProgram").click(function() {
        $(this).hide();
        $("#programSpan, #editProgram").show();
        $('#newProgram, #newProgram, #updateProgram').hide();
    });
});

function showUpdate() {
    $("#update").show();
};