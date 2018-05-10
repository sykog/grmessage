<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Profile</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--Style sheets-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
        <link type="text/css" rel="stylesheet" href="/355/grmessage/styles/main.css">

        <!-- Javascript-->
        <script src="http://code.jquery.com/jquery.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js" type="text/javascript"></script>
        <script src="/355/grmessage/javascript/updateStudentInfo.js"></script>
    </head>
    <body>
        <header class="header clearfix" id="mainHeader">
            <a href="profile"><h1>Green River Messaging</h1></a>
            <a href="logout"><h1 id="logout">Logout</h1></a>
        </header>

        <div class="container" id="mainContainer">
            <h2 id="regHeader">Welcome, <?= ($fname) ?></h2>
            <hr>

            <div class="col-6">
                <form class="form" action="" method="post">

                    <p><strong>Full Name: </strong> <?= ($fname) ?> <?= ($lname) ?> </p>

                    <div id="studentFields">

                        <p><strong>Student Email: </strong> <?= ($studentEmail) ?> </p>
                        <p><strong>Personal Email: </strong> <?= ($personalEmail) ?> </p>
                        <p><strong>Phone Number: </strong> <?= ($phone) ?> </p>
                        <p><strong>Mobile Carrier: </strong> <?= ($carrier) ?> </p>


                    </div>
                    <button type="button" id="change" name="change">Update Info</button><br>
                    <div id="updateInfo"></div>
                    <br>

                    <h3>Notification Preferences</h3>

                    <?php if ($getStudentEmails == 'y'): ?>
                        <input type="checkbox" name="getStudentEmails" value="getStudentEmails" checked> Student Email <br>
                        <?php else: ?><input type="checkbox" name="getStudentEmails" value="getStudentEmails"> Student Email<br>
                    <?php endif; ?>

                    <?php if ($getTexts == 'y'): ?>
                        <input type="checkbox" name="getTexts" value="getTexts" checked> Text Messages<br>
                        <?php else: ?><input type="checkbox" name="getTexts" value="getTexts"> Text Messages<br>
                    <?php endif; ?>

                    <?php if ($getPersonalEmails == 'y'): ?>
                        <input type="checkbox" name="getPersonalEmails" value="getPersonalEmails" checked> Personal Email <br>
                        <?php else: ?><input type="checkbox" name="getPersonalEmails" value="getPersonalEmails"> Personal Email<br>
                    <?php endif; ?>
                    <br>

                    <button type="submit" name="save">Save Changes</button>

                </form>
            </div>
            <div class="col-6">
                <label></label>
            </div>
        </div> <!-- /container -->
    </body>
</html>