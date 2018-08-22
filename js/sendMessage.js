// Counter for charactrer limit and validation for sending a message

// waits til page is loaded
$(document).ready(function() {

    // variables
    var maxLength = 250;
    var sendButton = $("#sendMessage");
    var containsMessage = false;
    var hasRecipient = false;

    sendButton.hide();

    // show number of characters left
    $("#textMessage").keyup(function() {
        var length = $(this).val().length;
        length = maxLength - length;
        $("#chars").text(length);

        // check if the textarea isn't empty
        if (length === maxLength) {
            containsMessage = false;
            sendButton.hide();
        } else {
            containsMessage = true;
            if(hasRecipient) sendButton.show();
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
        if (containsMessage && hasRecipient) sendButton.show();
        else sendButton.hide();
    });

    // confirm if user wants to refresh if there is a message
    $(window).on('beforeunload', function() {
        if ($("#textMessage").val().length > 0) {
            return "Are you sure you want to leave? Your message has not been sent";
        }
    });
});
