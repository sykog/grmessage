// shows the option to change password

// waits till page is loaded
$(document).ready(function() {

    $("#newPhone").mask("(999) 999-9999");

    $(".error, .sameLine, .update, .cancelInput, #save, .alert-success").hide();
    $(".verify *, #passChanged").show();

    // hide verification tabs if verified
    hideVerifications();

    // hide checkboxes if value are empty
    if ($("#phoneSpan").text().length <= 2) {
        $("#getTexts, #getTexts + p, p + br").remove();
        $("#carrierDiv").hide();
    }
    if ($("#pEmailSpan").text().length <= 2) $("#getPersonalEmails, #getPersonalEmails + p").remove();

    // Scroll down to see update button for notifications
    $("#getPersonalEmails, #getTexts, #getStudentEmails").click( function() {
        $("#save").show();
        $("html, body").animate({ scrollTop: $(document).height() }, 1000);
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

    /********************** AJAX POSTS ************************/

    // update student name
    $("#updateName").click(function() {

        if ($("#newFName").val().length > 0 && $("#newLName").val().length > 0) {
            var updateName = $.post('ajax/updateStudent.php', {
                email: $("#emailSpan").text(),
                column: "name",
                fname: $("#newFName").val(),
                lname: $("#newLName").val()});

            // update the page
            updateName.done(function() {
                $("#nameSpan").text($("#newFName").val() + " " + $("#newLName").val());
                $("#regHeader").text("Welcome, " + $("#newFName").val());
                $("#nameSpan, #editName").show();
                $("#newFName, #newLName, #updateName, #cancelName, #nameError").hide();
            });
        } else $("#nameError").show();
    });

    // update personal email
    $("#updatePersonalEmail").click(function() {

        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if (emailReg.test($("#newPersonalEmail").val()) && $("#newPersonalEmail").val().length > 0) {
            var updatePEmail = $.post('ajax/updateStudent.php', {
                email: $("#emailSpan").text(),
                column: "pEmail",
                pemail: $("#newPersonalEmail").val()});

            // update the page
            updatePEmail.done(function() {
                $("#pEmailSpan").text($("#newPersonalEmail").val());
                $("#pEmailSpan, #editPersonalEmail, #personalVerification, #verifyPEmailDiv, #resendPEmail, #pEmailSent").show();
                $("#newPersonalEmail, #updatePersonalEmail, #cancelPersonalEmail, #pEmailError").hide();
                $('#pEmailSent').delay(5000).fadeOut('slow');
            });
        } else $("#pEmailError").show();
    });

    // verify personal email
    $("#verifyPersonal").click(function() {

        var verifyPhone = $.post('ajax/updateStudent.php', {
            email: $("#emailSpan").text(),
            column: "pEmailVerify",
            code: $("#personalVerification").val()});

        // update the page
        verifyPhone.done(function(data) {
            // hide tab if code is correct, display error if not
            if (data == "correct") $("#verifyPEmailDiv, #pEmailVerifyError, #pEmailError").hide();
            else $("#pEmailVerifyError").show();
        });
    });

    // resend personal email code
    $("#resendPEmail").click(function() {

        var resendPEmail = $.post('ajax/updateStudent.php', {
            email: $("#emailSpan").text(),
            column: "pEmailResend",
            pemail: $("#pEmailSpan").text()});

        // update the page
        resendPEmail.done(function() {
            $("#pEmailSent").show();
            $("#pEmailVerifyError, #pEmailError").hide();
            $('#pEmailSent').delay(5000).fadeOut('slow');
        });
    });

    // update phone number
    $("#updatePhone").click(function() {

        // remove non numeric characters
        var newPhone = $("#newPhone").val().replace(/\D/g,'');

        // if not empty, mask makes sure it's valid
        if (newPhone.length > 0) {
            var updatePhone = $.post('ajax/updateStudent.php', {
                email: $("#emailSpan").text(),
                column: "phone",
                phone: newPhone,
                carrier: $("#carrierSpan").text()});

            // update the page
            updatePhone.done(function() {
                $("#phoneSpan").text(newPhone);
                $("#phoneSpan, #editPhone, #phoneVerification, #verifyPhoneDiv, #resendPhone, #phoneSent").show();
                $("#newPhone, #updatePhone, #cancelPhone, #phoneError").hide();
                $('#phoneSent').delay(5000).fadeOut('slow');
            });
        } else $("#phoneError").show();
    });

    // update phone carrier
    $("#updateCarrier").click(function() {

        var carriers = ["Verizon","AT&T","Sprint","T-Mobile","Boost Mobile",
                "Cricket Wireless","Virgin Mobile","Republic Wireless","U.S. Cellular","Alltel"];
        var carrier = $.trim($("#newCarrier").val());

        // -1 if false
        if ($.inArray(carrier, carriers) >= 0) {
            var updateCarrier = $.post('ajax/updateStudent.php', {
            email: $("#emailSpan").text(),
            column: "carrier",
            phone: $("#phoneSpan").text(),
            carrier: carrier});

            // update the page
            updateCarrier.done(function() {
                $("#carrierSpan").text(carrier);
                $("#carrierSpan, #editCarrier, #carrierVerification, #verifyPhoneDiv, #resendPhone, #phoneSent").show();
                $("#newCarrier, #updateCarrier, #cancelCarrier, #carrierError").hide();
                $('#phoneSent').delay(5000).fadeOut('slow');
            });
        } else $("#carrierError").show();
    });

    // verify phone number
    $("#verifyPhone").click(function() {

        var verifyPhone = $.post('ajax/updateStudent.php', {
            email: $("#emailSpan").text(),
            column: "phoneVerify",
            code: $("#phoneVerification").val()});

        // update the page
        verifyPhone.done(function(data) {
            // hide tab if code is correct, display error if not
            if (data == "correct") $("#verifyPhoneDiv, #phoneVerifyError, #phoneError").hide();
            else $("#phoneVerifyError").show();
        });
    });

    // resend phone code
    $("#resendPhone").click(function() {

        var resendPhone = $.post('ajax/updateStudent.php', {
            email: $("#emailSpan").text(),
            column: "phoneResend",
            phone: $("#phoneSpan").text(),
            carrier: $("#carrierSpan").text()});

        // update the page
        resendPhone.done(function() {
            $("#phoneSent").show();
            $("#phoneVerifyError, #phoneError").hide();
            $('#phoneSent').delay(5000).fadeOut('slow');
        });
    });

    // update program
    $("#updateProgram").click(function() {

        var programs = ["Bachelors - Software Development", "Associates - Software Development",
                "Bachelors - Networking", "Associates - Networking"];
        var program = $.trim($("#newProgram").val());

        // -1 if false
        if ($.inArray(program, programs) >= 0) {
            var updateProgram = $.post('ajax/updateStudent.php', {
            email: $("#emailSpan").text(),
            column: "program",
            program: program});

            // update the page
            updateProgram.done(function() {
                $("#programSpan").text(program);
                $("#programSpan, #editProgram").show();
                $("#newProgram, #updateProgram, #cancelProgram, #programError").hide();
            });
        } else $("#programError").show();
    });

    // update notification preferences
    $("#save").click(function() {
        var getStudentEmail = $("#getStudentEmails[type=checkbox]").prop('checked');
        var getPersonalEmail = $("#getPersonalEmails[type=checkbox]").prop('checked');
        var getTexts = $("#getTexts[type=checkbox]").prop('checked');

        var updatePreferences = $.post('ajax/updateStudent.php', {
        email: $("#emailSpan").text(),
        column: "preferences",
        studentEmail: getStudentEmail,
        personalEmail: getPersonalEmail,
        text: getTexts});

        // update the page
        updatePreferences.done(function() {
            $("#save").hide();

            if (getStudentEmail) {
                $("#prefEmail").show();
                $("#prefEmail").delay(5000).fadeOut('slow');
            }
            if (getPersonalEmail) {
                $("#prefPEmail").show();
                $("#prefPEmail").delay(5000).fadeOut('slow');
            }
            if (getTexts) {
                $("#prefText").show();
                $("#prefText").delay(5000).fadeOut('slow');
            }
        });
    });

    function hideVerifications() {
        var verificatons = $.post('ajax/checkVerification.php', {email: $("#emailSpan").text()});

        // check to see which are verified, do nothing if neither are
        verificatons.done(function(data) {
            if (data == "both") $("#verifyPhoneDiv, #phoneVerifyError, #phoneError, #verifyPEmailDiv, #pEmailVerifyError, #pEmailError").hide();
            else if (data == "phone") $("#verifyPhoneDiv, #phoneVerifyError, #phoneError").hide();
            else if (data == "email") $("#verifyPEmailDiv, #pEmailVerifyError, #pEmailError").hide();
        });
    }
});