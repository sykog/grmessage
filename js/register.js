// switches between student and registration validation and validating both

// waits till page is loaded
$(document).ready(function() {

    // variables
    var student = $("button#student");
    var instructor = $("button#instructor");
    var field = "";
    var semailExp = new RegExp('^[\\w]+@mail.greenriver.edu$');
    var iemailExp = new RegExp('^[\\w]+@greenriver.edu$');
    var letterExp = new RegExp('[a-zA-Z]');
    var numberExp = new RegExp('[\\d]');
    var specCharExp = new RegExp('[!@#$%^&*_=+-]');

    // input masks
    $("#phone").mask("(999) 999-9999");
    if($("#email").attr("name") == "iemail") instructorClick();
    if ($("#gatorLock[type=checkbox]").prop('checked')) {
        $("#showPassword, .gatorLockShow").hide();
        $("#showPassword[type=checkbox]").prop('checked', false);
        $("#password").attr("type", "password");
        $("#confirm").attr("type", "password");
    }

    // carrier field starts hidden
    $("#selectCarrier").hide();

    // changes details if the gatorlock checkbox is clicked {
    $("#gatorLock").click(function() {
        if ($("#gatorLock[type=checkbox]").prop('checked')) {
            $("#showPassword, .gatorLockShow").hide();
            $("#showPassword[type=checkbox]").prop('checked', false);
            $("#password").attr("type", "password");
            $("#confirm").attr("type", "password");
        } else {
            $("#showPassword, .gatorLockShow").show();
        }
    });

    // shows student fields (phone, carrier)
    student.click(function () {
        $("#studentFields").show();
        student.addClass("clicked");
        instructor.removeClass("clicked");

        $("#submit").attr("name", "submitS");
        $("#email").attr("name", "semail");
        $("#email").attr("placeholder", "example@mail.greenriver.edu");
        $('#regHeader').text("Student");
    });

    // hides fields (phone, carrier)
    instructor.click(function() {
        instructorClick();
    });

    // shows carrier is phone number is typed in
    $("#phone").focus(function() {
        $("#selectCarrier").show();
        validPhone();
    });

    // toggle show password
    $("#showPassword").click(function () {

        if ($("#password").attr("type") == "password") {
            $("#password").attr("type", "text");
            $("#confirm").attr("type", "text");
        } else {
            $("#password").attr("type", "password");
            $("#confirm").attr("type", "password");
        }
    });

    // when clicking on input field
    $("input").focus(function () {
        // get name of field
        field = $(this).attr("name");

        if (field == "semail") {
            validSEmail();
        }
        else if (field == "iemail") {
            validIEmail();
        }
        else if (field == "password") {
            validPassword();
        }
        else if (field == "confirm") {
            validConfirm();
        }
    });

    // check for valid student email
    function validSEmail() {
        $("input#email").on('keyup blur change', function () {
            if (semailExp.test($(this).val())) {
                // if valid check if it already exists
                $(".red").eq(0).load('ajax/studentExists.php', {studentEmail: $(this).val()});
            } else {
                $(".red").eq(0).text("Not a green river student email");
            }
        });
    }

    // check for valid instructor email.
    function validIEmail() {
        $("input#email").on('keyup blur change', function () {
            if (iemailExp.test($(this).val())) {
                // if valid check if it already exists
                $(".red").eq(0).load('ajax/instructorExists.php', {email: $(this).val()});
            } else {
                $(".red").eq(0).text("Not a green river instructor email");
            }
        });
    }

    // must be at least 8 characters, and have a letter number, and special character
    function validPassword() {
        $("input#password").on('keyup blur change', function () {
            if (this.value.length < 8) {
                // eq() gets the nth element
                $(".red").eq(1).text("Not 8 characters");
            } else if (!letterExp.test(this.value)) {
                $(".red").eq(1).text("Must contain a letter");
            } else if (!numberExp.test(this.value)) {
                $(".red").eq(1).text("Must contain a number");
            } else if (!specCharExp.test(this.value)) {
                $(".red").eq(1).text("Must contain a special character");
            }
            else {
                $(".red").eq(1).text("");
            }
        });
    }

    // must match password
    function validConfirm() {
        $("input#confirm").on('keyup blur change', function () {
            if ($("input#password").val() === this.value) {
                $(".red").eq(2).text("");
            } else {
                $(".red").eq(2).text("Does not match password");
            }
        });
    }

    // hide carrier box if phone is empty
    function validPhone() {
        $("input#phone").on('keyup blur change', function () {
            if (this.value.length < 1) {
                $("#selectCarrier").hide();
            }
        });
    }

    function instructorClick() {
        $("#studentFields").hide();
        instructor.addClass("clicked");
        student.removeClass("clicked");

        $("#submit").attr("name", "submitI");
        $("#email").attr("name", "iemail");
        $("#email").attr("placeholder", "example@greenriver.edu");
        $('#regHeader').text("Instructor");
    }
});