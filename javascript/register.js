// adds a listener to window.onload
$(document).ready(function() {

    // button variables
    var student = $("button#student");
	var instructor = $("button#instructor");

	// shows student fields (phone, carrier)
	student.click( function() {
        $("#studentFields").show();
	});

	// hides fields (phone, carrier)
	instructor.click( function() {
        $("#studentFields").hide();
        student.removeClass("showed");
	});
});