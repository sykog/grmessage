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
        <link type="text/css" rel="stylesheet" href="/355/grmessage/styles/profile.css">


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

            <div class="col-10">
                <form class="form" action="" method="post">

                    <div id="studentFields">

                        <?php if (isset($errors['name'])): ?>
                            <p class="error alert alert-danger"><?= ($errors['name']) ?></p>
                        <?php endif; ?>
                        <label><strong>Full Name: </strong><span id="nameSpan"><?= ($fname) ?> <?= ($lname) ?> </span>
                        <input class='form-control sameLine' type='text' name='newFName' id='newFName' value="<?= ($fname) ?>">
                        <input class='form-control sameLine' type='text' name='newLName' id='newLName' value="<?= ($lname) ?>">
                        <button class='update' type="submit" id='updateName' name="updateName">Update Info</button>
                        <button class="edit" type="button" id="editName"></button>
                        <button type="button" class="cancelInput" id="cancelName" name="cancelName">Cancel</button></label>

                        <label><strong>Student Email: </strong><span> <?= ($studentEmail) ?> </span></label><br>

                        <?php if (isset($errors['pEmail'])): ?>
                            <p class="error alert alert-danger"><?= ($errors['pEmail']) ?></p>
                        <?php endif; ?>
                        <label><strong>Personal Email: </strong><span id="pEmailSpan"><?= ($personalEmail) ?> </span>
                        <input class='form-control sameLine' type='text' name='newPersonalEmail' id='newPersonalEmail' value="<?= ($personalEmail) ?>">
                        <button class='update' type="submit" id='updatePersonalEmail' name="updatePersonalEmail">Update Info</button>
                        <button type="button" class="edit" id="editPersonalEmail"></button>
                        <button type="button" class="cancelInput" id="cancelPersonalEmail" name="cancelPersonalEmail">Cancel</button></label><br>

                        <?php if (!$verifiedPersonal): ?>
                            <?php if (isset($errors['personalVerificaton'])): ?>
                                <p class="error alert alert-danger"><?= ($errors['personalVerificaton']) ?></p>
                            <?php endif; ?>
                            <label><strong>Enter Verificaton Code: </strong><span id="PersonalVerificationSpan"> </span>
                                <input class='form-control sameLine verify' type='text' name='personalVerification' id='perssonalVerificatioin'>
                                <button class='update verify' type="submit" id='verifyPersonal' name="verifyPersonal">Verify Personal Email</button></label><br>
                        <?php endif; ?>

                        <?php if (isset($errors['phone'])): ?>
                            <p class="error alert alert-danger"><?= ($errors['phone']) ?></p>
                        <?php endif; ?>
                        <label><strong>Phone Number: </strong><span id="phoneSpan"> <?= ($phone) ?> </span>
                        <input class='form-control sameLine' type='text' name='newPhone' id='newPhone' value="<?= ($phone) ?>">
                        <button class='update' type="submit" id='updatePhone' name="updatePhone">Update Info</button>
                        <button type="button" class="edit" id="editPhone"></button>
                        <button type="button" class="cancelInput" id="cancelPhone" name="cancelPhone">Cancel</button></label><br>

                        <div id="carrierDiv">
                            <label><strong>Mobile Carrier: </strong><span id="carrierSpan"> <?= ($carrier) ?> </span>
                            <select class="form-control dropdown sameLine" id="newCarrier" name="newCarrier">
                                <?php foreach (($carriers?:[]) as $carrierOption): ?>
                                    <option <?php if ($carrierOption == $carrier): ?>selected<?php endif; ?>>
                                    <?= ($carrierOption) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class='update' type="submit" id='updateCarrier' name="updateCarrier">Update Info</button>
                            <button type="button" class="edit" id="editCarrier"></button>
                            <button type="button" class="cancelInput" id="cancelCarrier" name="cancelCarrier">Cancel</button></label>
                        </div>

                        <?php if (!$verifiedPhone): ?>
                            <?php if (isset($errors['phoneVerificaton'])): ?>
                                <p class="error alert alert-danger"><?= ($errors['phoneVerificaton']) ?></p>
                            <?php endif; ?>
                            <label><strong>Enter Verificaton Code: </strong><span id="PhoneVerificationSpan"> </span>
                                <input class='form-control sameLine verify' type='text' name='phoneVerification' id='phoneVerificatioin'>
                                <button class='update verify' type="submit" id='verifyPhone' name="verifyPhone">Verify Phone</button></label><br>
                        <?php endif; ?>

                        <label><strong>Program: </strong><span id="programSpan"> <?= ($program) ?> </span>
                        <select class="form-control dropdown sameLine" id="newProgram" name="newProgram">
                            <?php foreach (($programs?:[]) as $programOption): ?>
                                <option <?php if ($programOption == $program): ?>selected<?php endif; ?>>
                                <?= ($programOption) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class='update' type="submit" id='updateProgram' name="updateProgram">Update Info</button>
                        <button type="button" class="edit" id="editProgram"></button>
                        <button type="button" class="cancelInput" id="cancelProgram" name="cancelProgram">Cancel</button></label><br>
                    </div>

                    <button type="button" id="changePassword" name="changePassword">Change Password</button><br>

                    <div id="password"></div>
                    <br>

                    <h3>Notification Preferences</h3>

                    <?php if ($getStudentEmails == 'y'): ?>
                        <input onclick="showUpdate();" type="checkbox" name="getStudentEmails" id="getStudentEmails" value="getStudentEmails" checked><p>Student Email</p><br>
                        <?php else: ?><input onclick="showUpdate();" type="checkbox" name="getStudentEmails" id="getStudentEmails" value="getStudentEmails"><p>Student Email</p><br>
                    <?php endif; ?>

                    <?php if ($getTexts == 'y'): ?>
                        <input onclick="showUpdate();" type="checkbox" name="getTexts" id="getTexts" value="getTexts" checked><p>Text Messages</p><br>
                        <?php else: ?><input onclick="showUpdate();" type="checkbox" name="getTexts" id="getTexts" value="getTexts"><p>Text Messages</p><br>
                    <?php endif; ?>

                    <?php if ($getPersonalEmails == 'y'): ?>
                        <input onclick="showUpdate();" type="checkbox" name="getPersonalEmails" id="getPersonalEmails" value="getPersonalEmails" checked><p>Personal Email</p><br>
                    <?php else: ?><input onclick="showUpdate();" type="checkbox" name="getPersonalEmails" id="getPersonalEmails" value="getPersonalEmails"><p>Personal Email</p><br>
                    <?php endif; ?>

                    <button id="update" type="submit" name="save">Save Changes</button>
                </form>
            </div>
        </div> <!-- /container -->
    </body>
</html>