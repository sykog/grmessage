<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Messaging</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--Style sheets-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
    <link type="text/css" rel="stylesheet" href="/355/grmessage/styles/main.css">
    <link type="text/css" rel="stylesheet" href="/355/grmessage/styles/message.css">

    <!-- Javascript-->
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js" type="text/javascript"></script>
</head>
<body>
<header class="header clearfix" id="mainHeader">
    <a href="message"><h1>Green River Messaging</h1></a>
    <a href="logout"><h1 id="logout">Logout</h1></a>
</header>

<div class="container" id="mainContainer">
    <h2 id="regHeader">Messaging</h2>
    <hr>

    <div class="col-6">
        <!--<input title="it-355 10am" id="it-355am" type="checkbox">
        <label>IT-355 10am</label>
        <br>
        <input title="it-355 1pm" id="it-355pm" type="checkbox">
        <label>IT-355 1pm</label>
        <br>
        <input title="it-334 11am" id="it-334am" type="checkbox">
        <label>IT-334 11am</label>
        <br>
        <input title="it-334 2pm" id="it-334pm" type="checkbox">
        <label>IT-334 2pm</label>
        <br>-->
        <form class="form" action="" method="post">
            <?php foreach (($programs?:[]) as $programOption): ?>
                <label>
                    <input type="checkbox" name="chosenPrograms[]" value="<?= ($programOption) ?>">
                    <?= ($programOption)."
" ?>
                </label>
                <br>
            <?php endforeach; ?>
            <p id="counter"><span id="chars">250</span> characters left</p>
            <textarea id="textMessage" name="textMessage" rows="6" cols="60" maxlength="250"><?= ($textMessage) ?></textarea>

            <button id="sendMessage" type="submit" name="submit" >Send Message</button>
            <?php if ($sent): ?>
                <h3 id="green" class="alert alert-success" role="alert">Message Sent!</h3>
            <?php endif; ?>
        </form>
    </div>
</div> <!-- container -->

<script>
    var maxLength = 250;

    // show number of characters left
    $("#textMessage").keydown(function() {
        var length = $(this).val().length;
        length = maxLength - length;
        $("#chars").text(length);
    });
</script>
</body>
</html>