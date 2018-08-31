// counter for character limit and validation for sending a message

// waits til page is loaded
$(document).ready(function() {

    // variables
    var maxLength = 250;
    var sendButton = $("#sendMessage");
    var containsMessage = false;
    var hasRecipient = false;
    var sent = true;

    // initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // disable send button
    disableButton();

    // hide sent message button after 5 seconds
    $("h3#green").delay(5000).fadeOut('slow');

    sendButton.click(function() {
        if (hasRecipient && $("#textMessage").val().length > 0) {
            sent = true;
        }
    });

    // show number of characters left
    $("#textMessage").keyup(function() {
        var length = $(this).val().length;
        length = maxLength - length;
        $("#chars").text(length);

        // check if the textarea isn't empty
        if (length === maxLength) {
            containsMessage = false;
            disableButton();
        } else {
            containsMessage = true;
            if(hasRecipient) enableButton();
        }
    });

    // check if a recipient is selected
    $("input").click(function() {
        var checked = $("input:checked");

        if (checked.length === 0) hasRecipient = false;
        else hasRecipient = true;
    });

    // show send button it there is both a message and a recipient
    $("form").change(function() {
        if (containsMessage && hasRecipient) enableButton();
        else disableButton();
    });

    // ask for confirmation when leaving or page with a valid unsent message
    window.onbeforeunload = function(event) {
        if (!sent) return confirm("Are you sure you want to leave? Your message has not been sent");
    };

    // enable button that sends a message
    function enableButton() {
        sendButton.attr("disabled", false);
        $('[data-toggle="tooltip"]').tooltip("disable");
        sendButton.removeClass("disabled");
    }

    // disable button that sends a message
    function disableButton() {
        sendButton.attr("disabled", true);
        $('[data-toggle="tooltip"]').tooltip("enable");
        sendButton.addClass("disabled");
    }
});
