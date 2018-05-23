// switches between student and registration validation and validating both

// waits till page is loaded
$(document).ready(function() {

    // variables
    var student = $("button#student");
    var instructor = $("button#instructor");
    var field = "";
    var semailExp = new RegExp('^[\\w]+@mail.greenriver.edu$');
    var iemailExp = new RegExp('^[\\w]+@greenriver.edu$');

    // input masks
    $("#phone").mask("(999) 999-9999");

    // carrier field starts hidden
    $("#selectCarrier").hide();

    // shows student fields (phone, carrier)
    student.click(function () {
        $("#studentFields").show();
        student.addClass("clicked");
        instructor.removeClass("clicked");

        $("#submit").attr("name", "submitS");
        $("#email").attr("name", "semail");
        $('#regHeader').text("Student");
    });

    // hides fields (phone, carrier)
    instructor.click(function () {
        $("#studentFields").hide();
        instructor.addClass("clicked");
        student.removeClass("clicked");

        $("#submit").attr("name", "submitI");
        $("#email").attr("name", "iemail");
        $('#regHeader').text("Instructor");
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
                $(".red").eq(0).load('studentExists.php', {studentEmail: $(this).val()});
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
                $(".red").eq(0).load('instructorExists.php', {email: $(this).val()});
            } else {
                $(".red").eq(0).text("Not a green river instructor email");
            }
        });
    }

    // must be at least 8 characters
    function validPassword() {
        $("input#password").on('keyup blur change', function () {
            if (this.value.length < 8) {
                // eq() gets the nth element
                $(".red").eq(1).text("Password must be at least 8 characters");
            } else {
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
});