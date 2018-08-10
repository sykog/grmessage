// Counter for charactrer limit and validation for sending a message

// waits til page is loaded
$(document).ready(function() {

    // variables
    var maxLength = 250;
    var sendButton = $("#sendMessage");

    sendButton.hide();

    // show number of characters left
    $("#textMessage").keydown(function() {
        var length = $(this).val().length;
        length = maxLength - length;
        $("#chars").text(length);

        if (length == maxLength) sendButton.hide()
        else sendButton.show();
    });

    // hide send button if no programs are selected
    $("input").click(function() {
        var checked = $("input:checked");

        if (checked.length === 0) sendButton.hide();
        else sendButton.show();
    });
});
