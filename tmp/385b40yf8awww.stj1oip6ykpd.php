<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--Style sheets-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
    <link type="text/css" rel="stylesheet" href="/355/grmessage/styles/main.css">
    <link type="text/css" rel="stylesheet" href="/355/grmessage/styles/form.css">

    <!-- Javascript-->
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js" type="text/javascript"></script>
    <script src="/355/grmessage/javascript/register.js"></script>
</head>
<body>
    <header class="header clearfix" id="mainHeader">
        <a href="/355/grmessage/"><h1>Green River Messaging</h1></a>
    </header>

    <div class="container" id="mainContainer">
        <h2>Register as: </h2>
        <button type="button" id="student" class="clicked">Student</button>
        <button type="button" id="instructor">Instructor</button>
        <h2 id="regHeader">Student</h2>
        <hr>

        <div class="col-6">
            <form class="form" action="" method="post">
                <p><i>* Required fields</i></p>

                <label class="form-control-label" for="email">*Green River Email<span class="red"></span></label>
                <?php if (isset($errors['email'])): ?>
                    <p class="error alert alert-danger"><?= ($errors['email']) ?></p>
                <?php endif; ?>
                <input class="form-control" type="text" id="email" name="semail"
                       value="<?= ($email) ?>" required>

                <label class="form-control-label" for="password">*Password<span class="red"></span>
                    <input type="checkbox" value="show" id="showPassword">Show</label>
                <?php if (isset($errors['password'])): ?>
                    <p class="error alert alert-danger"><?= ($errors['password']) ?></p>
                <?php endif; ?>
                <input class="form-control" type="password" id="password" name="password"
                        value="<?= ($password) ?>" required>

                <label class="form-control-label" for="confirm">*Confirm password<span class="red"></span></label>
                <?php if (isset($errors['confirm'])): ?>
                    <p class="error alert alert-danger"><?= ($errors['confirm']) ?></p>
                <?php endif; ?>
                <input class="form-control" type="password" id="confirm" name="confirm"
                        value="<?= ($confirm) ?>" required>

                <label class="form-control-label" for="first">*First Name<span class="red"></span></label>
                <input class="form-control" type="text" id="first" name="first"
                        value="<?= ($first) ?>" required>

                <label class="form-control-label" for="last">*Last Name<span class="red"></span></label>
                <input class="form-control" type="text" id="last" name="last"
                        value="<?= ($last) ?>" required>

                <div id="studentFields">
                    <label class="form-control-label" for="phone">Cell Phone Number<span class="red"></span></label>
                    <?php if (isset($errors['phone'])): ?>
                        <p class="error alert alert-danger"><?= ($errors['phone']) ?></p>
                    <?php endif; ?>
                    <input class="form-control" type="text" id="phone" name="phone"
                            value="<?= ($phone) ?>">

                    <div id="selectCarrier">
                        <label class="form-check-label">Mobile Carrier</label>
                        <label class="d-block"></label>
                        <select class="form-control dropdown" id="carrier" name="carrier">
                            <?php foreach (($carriers?:[]) as $carrierOption): ?>
                                <option <?php if ($carrierOption == $carrier): ?>selected<?php endif; ?>>
                                <?= ($carrierOption) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <label class="form-check-label">*Program</label>
                    <label class="d-block"></label>
                    <select class="form-control dropdown" id="program" name="program">
                        <?php foreach (($programs?:[]) as $programOption): ?>
                            <option <?php if ($programOption == $program): ?>selected<?php endif; ?>>
                            <?= ($programOption) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button id="submit" type="submit" name="submitS">Register</button>
            </form>
        </div>
    </div> <!-- /container -->
</body>
</html>