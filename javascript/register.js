// adds a listener to window.onload
$(document).ready(function() {

    // button variables
    var student = $("button#student");
	var instructor = $("button#instructor");
	var field = "";

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

	// when clicking on inpult field
    $("input").focus( function() {
        // get name of field
        field = $(this).attr("name");

        // eq() gets the nth element
        if (field == "semail") {
            validSEmail();
            $(".red").eq(0).text(field);
        }
        else if (field == "iemail") {
            validIEmail();
            $(".red").eq(0).text(field);
        }
        else if (field == "password") {
            validPassword();
            $(".red").eq(1).text(field);
        }
        else if (field == "confirm") {
            validConfirm();
            $(".red").eq(2).text(field);
        }
        else if (field == "first") {
            validFirst();
            $(".red").eq(3).text(field);
        }
        else if (field == "last") {
            validLast();
            $(".red").eq(4).text(field);
        }
        else if (field == "phone") {
            validPhone();
            $(".red").eq(5).text(field);
        }
    });

    function validSEmail() {

    }

    function validIEmail() {

    }

    function validPassword() {

    }

    function validConfirm() {

    }

    function validFirst() {

    }

    function validLast() {

    }

    function validPhone() {

    }
});