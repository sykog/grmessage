<?php
ini_set('display-errors', 1);
error_reporting(E_ALL);

// require autoload file
require_once('vendor/autoload.php');

//Start the session
session_start();
include ('model/validate.php');
include('model/randomString.php');

// create instance of base class
$f3 = Base::instance();
$f3->set('DEBUG', 3);

$f3->set('carriers', array("Verizon","AT&T","Sprint","T-Mobile","Boost Mobile",
    "Cricket Wireless","Virgin Mobile","Republic Wireless","U.S. Cellular","Alltel"));
$f3->set('programs', array("Bachelors - Software Development", "Associates - Software Development",
    "Bachelors - Networking", "Associates - Networking"));

// define a default route
$f3->route('GET|POST /', function($f3, $params) {

    if (!isset($_POST['login'])) {
        $template = new Template();
        echo $template->render('views/home.html');
    }

    if (!$_SESSION['loggedIn']) {
        $database = new Database();

        //if login button is clicked
        if (isset($_POST['login'])) {

            $email = $_POST['email'];
            $_SESSION['username'] = $email;
            $f3->set('username', $email);

            $template = new Template();
            echo $template->render('views/home.html');

            $password = sha1($_POST['password']);
            $success = true;

            // if logging in as a student
            if (validSEmail($email)) {
                $student = $database->getStudent($email);
                $_SESSION['isInstructor'] = false;

                if($student['studentEmail'] == $email && ($student['password'] == $password)) {
                    $success = true;
                } else $success = false;
            }

            // if logging in as an instructor
            elseif (validIEmail($email)) {
                $instructor = $database->getInstructor($email);
                $_SESSION['isInstructor'] = true;
                if($instructor['email'] == $email && ($instructor['password'] == $password)) {
                    $success = true;
                } else $success = false;
            }
            // if the username is invalid, display login error
            else {
                $success = false;
            }

            if($success) {
                $_SESSION['loggedIn'] = true;
                $_SESSION['email'] = $email;

                $f3->reroute("/profile");
            }
            else {
                echo "<div class=\"error alert alert-danger\" role=\"alert\">
                Incorrect email or password</div>";
            }
        }

        if (isset($_POST['resend'])) {

            $email2 = $_POST['email2'];
            $newPassword = randomString(8);

            // if logging in as a student
            if (validSEmail($email2)) {
                $student = $database->getStudent($email2);
                $database->changeStudentPassword($email2, $newPassword);
                $headers = "From: Green River Messaging\n";
                mail($email2, 'Password Reset',
                    "Here is your new password: " .$newPassword . "\n", $headers);
            }

            // if logging in as an instructor
            elseif (validIEmail($email2)) {
                $instructor = $database->getInstructor($email2);
                $database->changeInstructorPassword($email2, $newPassword);
                $headers = "From: LaterGators\n";
                mail($email2, 'Password Reset',
                    "Here is your new password: " .$newPassword . "\n", $headers);
            }
        }
    }
    else {
        $f3->reroute("/profile");
    }

    // go to registration page when clicking button
    if (isset($_POST["register"])) {
        $f3->reroute("/register");
    }
});

// define a route for logout
$f3->route('GET|POST /logout', function($f3, $params) {

    $_SESSION['loggedIn'] = false;
    $_SESSION['isInstructor'] = false;
    $template = new Template();
    $f3->reroute("/");
});

// define a route for registration
$f3->route('GET|POST /register', function($f3, $params) {
    $database = new Database();
    $errors = array();

    // if registering as a student
    if(isset($_POST['submitS'])) {
        $email = $_POST['semail'];
        $password = $_POST['password'];
        $confirm = $_POST['confirm'];
        $first = $_POST['first'];
        $last = $_POST['last'];
        $phone = shortenPhone($_POST['phone']);
        $carrier = $_POST['carrier'];
        $program = $_POST['program'];
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $password;
        $_SESSION['confirm'] = $confirm;
        $_SESSION['first'] = $first;
        $_SESSION['last'] = $last;
        $_SESSION['phone'] = $phone;
        $_SESSION['carrier'] = $carrier;
        $_SESSION['program'] = $program;

        if(!validSEmail($email)) {
            $errors['email'] = "Please enter a student email";
        }
        if(!validPassword($password)) {
            $errors['password'] = "Please enter a valid password";
        }
        if(!validPassword($confirm)) {
            $errors['confirm'] = "Please confirm your password";
        }
        if(!validPhone($phone)) {
            $errors['phone'] = "Invalid phone number" . strlen($phone);
        }
        if(!validCarrier($carrier)) {
            $errors['carrier'] = "Invalid carrier" . strlen($phone);
        }
        if(!validProgram($program)) {
            $errors['program'] = "Invalid program" . strlen($phone);
        }

        $success = sizeof($errors) == 0;
        $f3->set('email', $email);
        $f3->set('password', $password);
        $f3->set('confirm', $confirm);
        $f3->set('first', $first);
        $f3->set('last', $last);
        $f3->set('phone', $phone);
        $f3->set('carrier', $carrier);
        $f3->set('program', $carrier);
        $f3->set('errors', $errors);
        $f3->set('success', $success);
    }
    // if registering as an instructor
    if(isset($_POST['submitI'])) {
        $email = $_POST['iemail'];
        $password = $_POST['password'];
        $confirm = $_POST['confirm'];
        $first = $_POST['first'];
        $last = $_POST['last'];
        $phone = $_POST['phone'];
        $carrier = $_POST['carrier'];
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $password;
        $_SESSION['confirm'] = $confirm;
        $_SESSION['first'] = $first;
        $_SESSION['last'] = $last;

        if(!validIEmail($email)){
            $errors['iemail'] = "Please enter an instructor email";
        }
        if(!validPassword($password)){
            $errors['password'] = "Please enter a valid password";
        }
        if(!validConfirm($password, $confirm)){
            $errors['confirm'] = "Please confirm your password";
        }

        $success = sizeof($errors) == 0;
        $f3->set('email', $email);
        $f3->set('password', $password);
        $f3->set('confirm', $confirm);
        $f3->set('first', $first);
        $f3->set('last', $last);
        $f3->set('phone', $phone);
        $f3->set('carrier', $carrier);
        $f3->set('errors', $errors);
        $f3->set('success', $success);
    }

    $template = new Template();
    echo $template->render('views/registration.html');

    if($success) {
        // if student account
        if ($email == $_POST['semail']) {
            // only submit if the email doesn't exist
            if($database->studentExists($email)) {
                echo "<div class=\"error alert alert-danger\" role=\"alert\">
            Email already exists.</div>";
            } else {
                $database->addStudent($email, $password, $phone, $first, $last, $carrier, $program);
                $_SESSION['loggedIn'] = true;
                $studentCode = randomString(6);
                $database->setStudentCode("verifiedStudent", $studentCode, $email);
                $headers = "From: Green River Messaging\n";
                mail($email, 'Account Verification Code',
                    "Account Verification Code: " .$studentCode . "\n", $headers);

                $f3->reroute("/verify");
            }
        }

        // if instructor account
        if ($email == $_POST['iemail']) {
            // only submit if the email doesnt exist
            if($database->instructorExists($email)) {
                echo "<div class=\"error alert alert-danger\" role=\"alert\">
            Email already exists.</div>";
            } else {
                $database->addInstructor($email, $password, $first, $last);
                $_SESSION['loggedIn'] = true;

                // generate code
                $instructorCode = randomString(6);
                $database->setInstructorCode($instructorCode, $email);
                $headers = "From: LaterGators\n";
                mail($email, 'Verification Code', $instructorCode . "\n", $headers);
                $f3->reroute("/verify");
            }
        }
    }
});

// define a message route
$f3->route('GET|POST /message', function($f3, $params) {

    $optOut = "\n\nIf you would like to stop receiving updates, 
        visit your profile page to opt-out: latergators.greenriverdev.com/355/grmessage/";
    $headers = "From: Green River Messaging\n";

    // go back to home page if not logged in
    if(!$_SESSION['loggedIn']) $f3->reroute("/");
    // go to profile if logged in, but as a student
    if($_SESSION['loggedIn'] && !$_SESSION['isInstructor']) {
        $f3->reroute("/profile");
    }

    $database = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);
    $f3->set("students", $database->getStudents());
    $instructor = $database->getInstructor($_SESSION['username']);

    // go to verification page if not account hasn't been verified
    if(trim($instructor['verified']) != 'y') {
        $f3->reroute('/verify');
    }
    if(isset($_POST['submit'])) {
        $textMessage = $_POST['textMessage'];
        $textMessage = trim($textMessage);
        $f3->set('textMessage', $textMessage);
        $f3->set('sent', false);

        if(validTextMessage ($textMessage)) {
            $chosen = $_POST['chosenPrograms']; // gets the selected program(s)
            $students = $database->getStudents();

            // send message to every student by selecting the program, then the student
            foreach ((array)$chosen as $current) {
                foreach ($students as $studentInfo) {
                    if ($studentInfo['program'] == $current) {
                        // only send text if opted in and verified
                        if ($studentInfo['getTexts'] == "y" && $studentInfo['verifiedPhone'] == 'y') {
                            $carrierInfo = $database->getCarrierInfo($studentInfo['carrier']);
                            $carrierEmail = $carrierInfo['carrierEmail'];
                            $to = $studentInfo['phone'] . "@" . $carrierEmail;
                            mail($to, '', $textMessage . $optOut, $headers);

                            // confirmation and remove message
                            $f3->set('sent', true);
                            $f3->set('textMessage', "");
                        }
                        // only send secondary email if opted in and verified
                        if ($studentInfo['getPersonalEmails'] == "y" && $studentInfo['verifiedPersonal'] == 'y') {
                            $to = $studentInfo['personalEmail'];
                            mail($to, '', $textMessage . $optOut, $headers);

                            // confirmation and remove message
                            $f3->set('sent', true);
                            $f3->set('textMessage', "");
                        }
                        // only send email if opted in and verified
                        if ($studentInfo['getStudentEmails'] == "y" && $studentInfo['verifiedStudent'] == 'y') {
                            $to = $studentInfo['studentEmail'];
                            mail($to, '', $textMessage . $optOut, $headers);

                            // confirmation and remove message
                            $f3->set('sent', true);
                            $f3->set('textMessage', "");
                        }

                        // confirmation and remove message
                        $f3->set('sent', true);
                        $f3->set('textMessage', "");
                    }
                }
            }

            // store message in the database, get chosen programs
            $recipient = implode(", ", (array)$chosen);
            $database->storeMessage($_SESSION['username'], $textMessage, $recipient);
        } else {
            echo "<div class=\"error alert alert-danger\" role=\"alert\">
                Message must be between 1 and 250 characters</div>";
        }

    }
    $template = new Template();
    echo $template->render('views/instructorMessage.html');
});

// define a profile route (used for students and instructors)
$f3->route('GET|POST /profile', function($f3, $params) {
    // go to login page if not logged in
    if(!$_SESSION['loggedIn']) {
        $f3->reroute("/");
    }

    $headers = "From: Green River Messaging\n";

    // instructor profile page
    if($_SESSION['loggedIn'] && $_SESSION['isInstructor']) {

        $database = new Database(DB_DSN, DB_USERNAME, DB_PASSWORD);
        //set f3 variables
        $email = $_SESSION['email'];
        $instructor = $database->getInstructor($email);

        $f3->set('email', $email);
        $f3->set('password', $instructor['password']);
        $f3->set('fname', $instructor['fname']);
        $f3->set('lname', $instructor['lname']);
        $f3->set('passChanged', false);

        // change password if button was clicked
        if (isset($_POST['updatePassword'])) {

            $currentPassword = sha1($_POST['currentPassword']);
            $newPassword = $_POST['newPassword'];
            $confirmPassword = $_POST['confirmPassword'];

            if ($currentPassword == $f3->get('password')) {
                if ($newPassword == $confirmPassword) {
                    if (strlen($newPassword) > 7) {
                        $database->changeInstructorPassword($email, $newPassword);
                        $f3->set('passChanged', true);
                    } else {
                        $errors['password'] = "Password must be at least 8 characters";
                    }
                } else {
                    $f3->set("errors['password']", "Passwords do not match");
                }
            } else {
                $f3->set("errors['password']", "Current password is incorrect");
            }
        }//end update password

        $template = new Template();
        echo $template->render('views/instructorHome.html');
    }

    // student profile page
    else {
        $database = new Database(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $studentEmail = $_SESSION['email'];
        $student = $database->getStudent($studentEmail);

        // if account hasn't been verified
        if (trim($student['verifiedStudent']) != 'y') {
            $f3->reroute('/verify');
        }

        $errors = array();

        $f3->set('studentEmail', $studentEmail);
        $f3->set('password', $student['password']);
        $f3->set('fname', $student['fname']);
        $f3->set('lname', $student['lname']);
        $f3->set('phone', $student['phone']);
        $f3->set('carrier', $student['carrier']);
        $f3->set('program', $student['program']);
        $f3->set('personalEmail', $student['personalEmail']);
        $f3->set('getTexts', $student['getTexts']);
        $f3->set('getStudentEmails', $student['getStudentEmails']);
        $f3->set('getPersonalEmails', $student['getPersonalEmails']);
        $f3->set('verifiedPersonal', $student['verifiedPersonal'] == 'y'
            || empty($f3->get('personalEmail')));
        $f3->set('verifiedPhone', $student['verifiedPhone'] == 'y');
        $f3->set('carriers', array("Verizon", "AT&T", "Sprint", "T-Mobile", "Boost Mobile",
            "Cricket Wireless", "Virgin Mobile", "Republic Wireless", "U.S. Cellular", "Alltel"));

        //if changes were made
        if (isset($_POST['save'])) {

            if (isset ($_POST['getStudentEmails'])) $getStudentEmails = 'y';
            else $getStudentEmails = 'n';

            if (isset ($_POST['getTexts'])) $getTexts = 'y';
            else $getTexts = 'n';

            if (isset ($_POST['getPersonalEmails'])) $getPersonalEmails = 'y';
            else $getPersonalEmails = 'n';

            $database->updatePreferences($studentEmail, $getStudentEmails, $getTexts, $getPersonalEmails);
            $f3->reroute("/profile");
        }

        // change password if button was clicked
        if (isset($_POST['updatePassword'])) {

            $currentPassword = sha1($_POST['currentPassword']);
            $newPassword = $_POST['newPassword'];
            $confirmPassword = $_POST['confirmPassword'];

            if ($currentPassword == $f3->get('password')) {
                if ($newPassword == $confirmPassword) {
                    if (strlen($newPassword) > 7) {
                        $database->changeStudentPassword($studentEmail, $newPassword);
                        $f3->set('passChanged', true);
                    } else {
                        $errors['password'] = "Password must be at least 8 characters";
                    }
                } else {
                    $errors['password'] = "Passwords do not match";
                }
            } else {
                $errors['password'] = "Current password is incorrect";
            }
        }

        // if update personal email button was clicked
        if (isset($_POST['updateName'])) {
            if (strlen($_POST['newFName']) > 0 && strlen($_POST['newLName']) > 0) {
                $database->changeStudentName($studentEmail, $_POST['newFName'], $_POST['newLName']);
                $f3->reroute("/profile");
            } else {
                $errors['name'] = "Please enter a valid name.";
            }
        }

        // if update personal email button was clicked
        if (isset($_POST['updatePersonalEmail'])) {
            if (validPEmail($_POST['newPersonalEmail'])) {
                $database->changePersonalEmail($studentEmail, $_POST['newPersonalEmail']);
                $f3->reroute("/profile");

                // send a code when changing email
                $personalCode = randomString(6);
                $database->setStudentCode("verifiedPersonal", $personalCode, $studentEmail);
                mail($_POST['newPersonalEmail'], 'Verification Code',
                    "GREEN RIVER MESSAGING\n\n Personal Email Verification Code: ". $personalCode . "\n", $headers);
            } else {
                $errors['pEmail'] = "Please enter a valid email.";
            }
        }

        //if the personal email verification button was clicked
        if (isset($_POST['verifyPersonal'])) {
            if ($_POST['personalVerification'] == $student['verifiedPersonal']) {
                $column = "verifiedPersonal";
                $value = "y";
                $f3->set('verifiedPersonal', true);
                $database->setStudentCode($column, $value, $studentEmail);
            } else {
                $errors['personalVerificaton'] = "Incorrect verification code.";
            }
        }

        // resend phone verification
        if(isset($_POST['resendPEmail'])){
            $code = randomString(6);
            $column = "verifiedPersonal";
            $database->setStudentCode($column, $code, $studentEmail);
            $to = $student['personalEmail'];
            mail($to, "GREEN RIVER MESSAGING\n\n Personal Email Verification Code: ", $code . "\n", $headers);
        }

        // if update phone number button was clicked
        if (isset($_POST['updatePhone'])) {
            $newPhone = $_POST['newPhone'];
            $newPhone = shortenPhone($newPhone);
            if (validPhone($newPhone) && strlen($newPhone) != 0) {
                $database->changePhoneNumber($studentEmail, $newPhone);
                $f3->reroute("/profile");

                // send a code when changing phone number
                $phoneCode = randomString(6);
                $column = "verifiedPhone";
                $database->setStudentCode($column, $phoneCode, $studentEmail);

                if (isset($_POST['newCarrier'])) $carrierInfo = $database->getCarrierInfo($_POST['newCarrier']);
                else $carrierInfo = $database->getCarrierInfo($student['carrier']);

                $carrierEmail = $carrierInfo['carrierEmail'];
                $to = $newPhone . "@" . $carrierEmail;
                mail($to, '', "Phone Verification Code: " . $phoneCode . "\n", $headers);
            } else {
                $errors['phone'] = "Please enter a valid phone number.";
            }
        }

        // if update phone carrier button was clicked
        if (isset($_POST['updateCarrier'])) {
            if (validCarrier($_POST['newCarrier'])) {
                $database->changeCarrier($studentEmail, $_POST['newCarrier']);
                $f3->reroute("/profile");

                if (!isset($_POST['updatePhone'])) {
                    $phoneCode = randomString(6);
                    $column = "verifiedPhone";
                    $database->setStudentCode($column, $phoneCode, $studentEmail);
                    $carrierInfo = $database->getCarrierInfo($_POST['newCarrier']);
                    $carrierEmail = $carrierInfo['carrierEmail'];
                    $to = $student['phone'] . "@" . $carrierEmail;
                    mail($to, '', "Phone Verification Code: " . $phoneCode . "\n", $headers);
                }
            }
        }

        //if the phone verification button was clicked
        if (isset($_POST['verifyPhone'])) {
            if ($_POST['phoneVerification'] == $student['verifiedPhone']) {
                $column = 'verifiedPhone';
                $value = 'y';
                $database->setStudentCode($column, $value, $studentEmail);
                $f3->set('verifiedPhone', true);
            } else {
                $errors['phoneVerificaton'] = "Incorrect verification code.";
            }
        }

        // resend phone verification
        if(isset($_POST['resendPhone'])){
            $code = randomString(6);
            $column = 'verifiedPhone';
            $database->setStudentCode($column, $code, $studentEmail);
            $carrierInfo = $database->getCarrierInfo($student['carrier']);
            $carrierEmail = $carrierInfo['carrierEmail'];
            $to = $student['phone'] . "@" . $carrierEmail;
            mail($to, '', "Phone Verification Code: " . $code . "\n", $headers);
        }

        // if update program button was clicked
        if (isset($_POST['updateProgram'])) {
            if (validProgram($_POST['newProgram'])) {
                $database->changeProgram($studentEmail, $_POST['newProgram']);
                $f3->reroute("/profile");
            }
        }

        $f3->set("errors", $errors);

        $template = new Template();
        echo $template->render('views/studentProfile.html');
    }
});

// define a message viewing route
$f3->route('GET|POST /view-messages', function($f3) {

    // go to login page if not logged in
    if(!$_SESSION['loggedIn']) $f3->reroute("/");

    // go to profile page if logged in as a student
    if($_SESSION['loggedIn'] && !$_SESSION['isInstructor']) {
        $f3->reroute("/profile");
    }

    $database = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);
    $instructor = $database->getInstructor($_SESSION['username']);
    if(trim($instructor['verified']) != 'y'){
        $f3->reroute('/verify');
    }

    // get all messages
    $messages = $database->getMessages();
    $f3->set('messages', $messages);

    $template = new Template();
    echo $template->render('views/viewMessages.html');
});

$f3->route('GET|POST /verify', function ($f3) {

    $database = new Database();

    $f3->set("email", $_SESSION['email']);
    $code = '';
    $headers = "From: Green River Messaging\n";

    // if an instructor account
    if (validIEmail($_SESSION['email'])){
        $instructor = $database->getInstructor($_SESSION['email']);
        $code = trim($instructor['verified']);

        // send new code when pressing resend
        if (isset($_POST['resend'])){
            $code = randomString(6);
            $database->setInstructorCode("verified", $code, $_SESSION['email']);
            $to = $_SESSION['email'];
            mail($to, "GREEN RIVER MESSAGING\n\n Account Verification Code: ", $code . "\n", $headers);
        }

        elseif (isset($_POST['verify'])){
            // if code is correct, verify in database
            if ($_POST['verificationCode'] == $code){
                $value = 'y';
                $database->setInstructorCode("verified", $value, $_SESSION['email']);
                $f3->reroute('/profile');
            }
            else {
                $f3->set('codeError', true);
            }
        }
    }

    // if a student account
    elseif (validSEmail($_SESSION['email'])) {
        $student = $database->getStudent($_SESSION['email']);
        $code = trim($student['verifiedStudent']);

        // send new code when pressing resend
        if (isset($_POST['resend'])){
            $code = randomString(6);
            $database->setStudentCode("verifiedStudent", $code, $_SESSION['email']);
            $to = $_SESSION['email'];
            mail($to, 'Account Verification Code',
                "GREEN RIVER MESSAGING\n\n Account Verification Code: " .$code . "\n", $headers);
        }

        elseif (isset($_POST['verify'])){
            // if code is correct, verify in database
            if($_POST['verificationCode'] == $code){
                $column = 'verifiedStudent';
                $value = 'y';
                $database->setStudentCode($column, $value, $_SESSION['email']);
                $f3->reroute('/profile');
            }
            else {
                $f3->set('codeError', true);
            }
        }
    }

    $template = new Template();
    echo $template->render('views/verifyAccount.html');
});

// run fat free
$f3->run();