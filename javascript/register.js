// adds a listener to window.onload
$(document).ready(function() {

    // variables
    var student = $("button#student");
	var instructor = $("button#instructor");
	var field = "";
	var semailExp = new RegExp('^[\\w]+@mail.greenriver.edu$');
	var iemailExp = new RegExp('^[\\w]+@greenriver.edu$');

	// input masks
	$("#phone").mask("(999) 999-9999");

	// shows student fields (phone, carrier)
	student.click( function() {
        $("#studentFields").show();
        $("#submit").attr("name", "submitS");
        $("#email").attr("name", "semail");
	});

	// hides fields (phone, carrier)
	instructor.click( function() {
        $("#studentFields").hide();
        student.removeClass("showed");
        $("#submit").attr("name", "submitI");
        $("#email").attr("name", "iemail");
	});

	// toggle show password
    $("#showPassword").click( function() {

        if ($("#password").attr("type") == "password") {
            $("#password").attr("type", "text");
            $("#confirm").attr("type", "text");
        } else {
            $("#password").attr("type", "password");
            $("#confirm").attr("type", "password");
        }
    });

	// when clicking on inpult field
    $("input").focus( function() {
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
        $("input#email").keyup(function () {
            if(semailExp.test($(this).val())) {
                $(".red").eq(0).text("");
            } else {
                $(".red").eq(0).text("Not a green river student email");
            }
        });
    }

    // check for valid instructor email.
    function validIEmail() {
        $("input#email").keyup(function () {
            if(iemailExp.test($(this).val())) {
                $(".red").eq(0).text("");
            } else {
                $(".red").eq(0).text("Not a green river instructor email");
            }
        });
    }

    // must be at least 6 characters
    function validPassword() {
        $("input#password").keyup(function () {
            if (this.value.length < 6) {
                // eq() gets the nth element
                $(".red").eq(1).text("Password must be at least 6 characters");
            } else {
                $(".red").eq(1).text("");
            }
        });
    }

    // must match password
    function validConfirm() {
        $("input#confirm").keyup(function () {
            if ($("input#password").val() === this.value) {
                $(".red").eq(2).text("");
            } else {
                $(".red").eq(2).text("Does not match password");
            }
        });
    }
});