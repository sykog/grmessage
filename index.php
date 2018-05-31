<?php
ini_set('display-errors', 1);
error_reporting(E_ALL);

// require autoload file
require_once('vendor/autoload.php');

//Start the session
session_start();
include ('model/validate.php');

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

                if ($_SESSION['isInstructor']) {
                    $_SESSION['email'] = $email;
                    $f3->reroute("/profile");
                } else { // is a student
                    $_SESSION['email'] = $email;
                    $f3->reroute("/profile");
                }
            }
            else {
                echo "<div class=\"error alert alert-danger\" role=\"alert\">
                Incorrect email or password</div>";
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
    $database = new Database();
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
        //$f3->reroute('/profile');
        // if student account
        if ($email == $_POST['semail']) {
            // only submit if the email doesnt exist
            if($database->studentExists($email)) {
                echo "<div class=\"error alert alert-danger\" role=\"alert\">
            Email already exists.</div>";
            } else {
                $database->addStudent($email, $password, $phone, $first, $last, $carrier, $program);
                $_SESSION['loggedIn'] = true;
                $studentCode = rand(1111, 9999) . "";
                $database->setStudentCode("verifiedStudent", $studentCode, $email);
                $headers = "From: LaterGators\n";
                mail($email, 'Verification Code', $studentCode . "\n", $headers);

                // generate code
                $phoneCode = rand(1111, 9999) . "";
                $database->setStudentCode("verifiedPhone", $phoneCode, $email);
                $headers = "From: LaterGators\n";
                $carrierInfo = $database->getCarrierInfo($carrier);
                $carrierEmail = $carrierInfo['carrierEmail'];
                $to = $phone . "@" . $carrierEmail;
                mail($to, '', "Verification Code: " . $phoneCode . "\n", $headers);
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
                $instructorCode = rand(1111, 9999) . "";
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

    // go back to home page if not logged in
    if(!$_SESSION['loggedIn']){
        $f3->reroute("/");
    } // go to profile if logged in, but as a student
    if($_SESSION['loggedIn'] && !$_SESSION['isInstructor']) {
        $f3->reroute("/profile");
    }

    $dbh = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);
    $f3->set("students", $dbh->getStudents());
    $instructor = $dbh->getInstructor($_SESSION['username']);

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
            $students = $dbh->getStudents();
            // select the program, then the student
            foreach ($chosen as $current) {
                foreach ($students as $studentInfo) {
                    if ($studentInfo['program'] == $current) {
                        // only send text if opted in
                        if ($studentInfo['getTexts'] == "y" && $studentInfo['verifiedPhone'] == 'y') {
                            $carrierInfo = $dbh->getCarrierInfo($studentInfo['carrier']);
                            $carrierEmail = $carrierInfo['carrierEmail'];
                            $to = $studentInfo['phone'] . "@" . $carrierEmail;
                            $headers = "From: LaterGators\n";
                            mail($to, '', $textMessage . "\n", $headers);

                            // confirmation and remove message
                            $f3->set('sent', true);
                            $f3->set('textMessage', "");
                        }
                        // only send secondary email if opted in
                        if ($studentInfo['getPersonalEmails'] == "y" && $studentInfo['verifiedPersonal'] == 'y') {
                            $to = $studentInfo['personalEmail'];
                            $headers = "From: LaterGators\n";
                            mail($to, '', $textMessage . "\n", $headers);

                            // confirmation and remove message
                            $f3->set('sent', true);
                            $f3->set('textMessage', "");
                        }
                        // only send email if opted in
                        if ($studentInfo['getStudentEmails'] == "y" && $studentInfo['verifiedStudent'] == 'y') {
                            $to = $studentInfo['studentEmail'];
                            $headers = "From: LaterGators\n";
                            mail($to, '', $textMessage . "\n", $headers);

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

            //store message in the database
            $dbh->storeMessage($_SESSION['username'], $textMessage);
        } else {
            echo "must be between 1 and 250 characters";
        }

    }
    $template = new Template();
    echo $template->render('views/instructorMessage.html');
});

$f3->route('GET|POST /profile', function($f3, $params) {
    // go to login page if not logged in
    if(!$_SESSION['loggedIn']) {
        $f3->reroute("/");
    }
    // instructor profile page
    if($_SESSION['loggedIn'] && $_SESSION['isInstructor']) {

        $dbh = new Database(DB_DSN, DB_USERNAME, DB_PASSWORD);
        //set f3 variables
        $email = $_SESSION['email'];
        $instructor = $dbh->getInstructor($email);

        $f3->set('email', $email);
        $f3->set('password', $instructor['password']);
        $f3->set('fname', $instructor['fname']);
        $f3->set('lname', $instructor['lname']);


        $template = new Template();
        echo $template->render('views/instructorHome.html');
    }

    // student profile page
    else {
        $dbh = new Database(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $studentEmail = $_SESSION['email'];
        $student = $dbh->getStudent($studentEmail);

        if (trim($student['verifiedStudent']) != 'y') {
            $f3->reroute('/verify');
        }

        if (!$_SESSION['loggedIn']) {
            $f3->reroute("/");
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

            if (isset ($_POST['getStudentEmails'])) {
                $getStudentEmails = 'y';
                echo 'You will now receive updates via your student email.';
            } else {
                $getStudentEmails = 'n';
                echo "You will no longer receive announcements via your student email";
            }
            if (isset ($_POST['getTexts'])) {
                // if checked
                if ($f3->get('phone')) {
                    // if there is a phone number
                    if (!empty($f3->get('phone'))) {
                        $getTexts = 'y';
                        if ($student['verifiedPhone'] == 'y') {
                            echo "You will now receive announcements via text message";
                        } else {
                            echo "Verify your phone number to receive announcements via text message";
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">
                       Please provide a phone number before attempting to select this option.</div>';
                    }

                } else {
                    echo "You will no longer receive announcements via your personal email.";
                }

            } else {
                $getTexts = 'n';
                echo "You will no longer receive announcements via text message";
            }

            if (isset ($_POST['getPersonalEmails'])) {
                // if an email exists
                if (!empty($f3->get('personalEmail'))) {
                    $getPersonalEmails = 'y';
                    if ($student['verifiedPersonal'] == 'y') {
                        echo "You will now receive announcements via your personal email";
                    } else {
                        echo "Verify your personal email to receive announcements via your personal email";
                    }

                } else {
                    echo '<div class="alert alert-danger" role="alert">
                Please provide a phone number before selecting this option.</div>';
                }

            } else {
                $getPersonalEmails = 'n';
                echo "You will no longer receive announcements via your personal email";
            }

            $dbh->updatePreferences($studentEmail, $getStudentEmails, $getTexts, $getPersonalEmails);
            header("location: profile");
        }

        // change password if button was clicked
        if (isset($_POST['updatePassword'])) {

            $currentPassword = sha1($_POST['currentPassword']);
            $newPassword = $_POST['newPassword'];
            $confirmPassword = $_POST['confirmPassword'];

            if ($currentPassword == $f3->get('password')) {
                if ($newPassword == $confirmPassword) {
                    $dbh->changeStudentPassword($studentEmail, $newPassword);
                    header("location: profile");
                } else {
                    echo '<div class="alert alert-danger" role="alert">
                Passwords do not match.</div>';
                }
            } else {
                echo '<div class="alert alert-danger" role="alert">
                Current password is incorrect.</div>';
            }
        }//end update password

        // if update personal email button was clicked
        if (isset($_POST['updateName'])) {
            if (strlen($_POST['newFName']) > 0 && strlen($_POST['newLName']) > 0) {
                $dbh->changeStudentName($studentEmail, $_POST['newFName'], $_POST['newLName']);
                header("location: profile");
            } else {
                $errors['name'] = "Please enter a valid name.";
            }
        }

        // if update personal email button was clicked
        if (isset($_POST['updatePersonalEmail'])) {
            if (validPEmail($_POST['newPersonalEmail'])) {
                $dbh->changePersonalEmail($studentEmail, $_POST['newPersonalEmail']);
                header("location: profile");

                // send a code when changing email
                $personalCode = rand(1111, 9999) . "";
                $dbh->setStudentCode("verifiedPersonal", $personalCode, $studentEmail);
                $headers = "From: LaterGators\n";
                mail($_POST['newPersonalEmail'], 'Verification Code',
                    $personalCode . "\n", $headers);
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
                $dbh->setStudentCode($column, $value, $studentEmail);
            } else {
                $errors['personalVerificaton'] = "Incorrect verification code.";
            }
        }

        // if update phone number button was clicked
        if (isset($_POST['updatePhone'])) {
            $newPhone = $_POST['newPhone'];
            $newPhone = shortenPhone($newPhone);
            if (validPhone($newPhone) && strlen($newPhone) != 0) {
                $dbh->changePhoneNumber($studentEmail, $newPhone);
                header("location: profile");

                // send a code when changing phone number
                $phoneCode = rand(1111, 9999) . "";
                $column = "verifiedPhone";
                $dbh->setStudentCode($column, $phoneCode, $studentEmail);
                $headers = "From: LaterGators\n";
                if (isset($_POST['newCarrier'])) {
                    $carrierInfo = $dbh->getCarrierInfo($_POST['newCarrier']);
                } else {
                    $carrierInfo = $dbh->getCarrierInfo($student['carrier']);
                }
                $carrierEmail = $carrierInfo['carrierEmail'];
                $to = $student['phone'] . "@" . $carrierEmail;
                mail($to, '', "Verification Code: " . $phoneCode . "\n", $headers);
            } else {
                $errors['phone'] = "Please enter a valid phone number.";
            }

        }

        // if update phone carrier button was clicked
        if (isset($_POST['updateCarrier'])) {
            if (validCarrier($_POST['newCarrier'])) {
                $dbh->changeCarrier($studentEmail, $_POST['newCarrier']);
                header("location: profile");
                if (!isset($_POST['updatePhone'])) {
                    $phoneCode = rand(1111, 9999) . "";
                    $column = "verifiedPhone";
                    $dbh->setStudentCode($column, $phoneCode, $studentEmail);
                    $headers = "From: LaterGators\n";
                    $carrierInfo = $dbh->getCarrierInfo($_POST['newCarrier']);
                    $carrierEmail = $carrierInfo['carrierEmail'];
                    $to = $student['phone'] . "@" . $carrierEmail;
                    mail($to, '', "Verification Code: " . $phoneCode . "\n", $headers);
                }
            }
        }

        //if the phone verification button was clicked
        if (isset($_POST['verifyPhone'])) {
            if ($_POST['phoneVerification'] == $student['verifiedPhone']) {
                $column = 'verifiedPhone';
                $value = 'y';
                $dbh->setStudentCode($column, $value, $studentEmail);
                $f3->set('verifiedPhone', true);
            } else {
                $errors['phoneVerificaton'] = "Incorrect verification code.";
            }
        }

        // if update program button was clicked
        if (isset($_POST['updateProgram'])) {
            if (validProgram($_POST['newProgram'])) {
                $dbh->changeProgram($studentEmail, $_POST['newProgram']);
                header("location: profile");
            }
        }

        $f3->set("errors", $errors);

        $template = new Template();
        echo $template->render('views/studentProfile.html');
    }
});

//route for the view messages page
$f3->route('GET|POST /view-messages', function($f3) {

    // go to login page if not logged in
    if(!$_SESSION['loggedIn']) {
        $f3->reroute("/");
    }
    // go to profile page if logged in as a student
    if($_SESSION['loggedIn'] && !$_SESSION['isInstructor']) {
        $f3->reroute("/profile");
    }

    $template = new Template();
    $dbh = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);
    $instructor = $dbh->getInstructor($_SESSION['username']);
    if(trim($instructor['verified']) != 'y'){
        $f3->reroute('/verify');
    }

    //get all messages
    $messages = $dbh->getMessages();
    $f3->set('messages', $messages);

    echo $template->render('views/viewMessages.html');
});

$f3->route('GET|POST /verify', function ($f3) {

    $dbh = new Database();
    $code = '';
    if(validIEmail($_POST['email'])){
        $instructor = $dbh->getInstructor($_POST['email']);
        $code = trim($instructor['verified']);
        $f3->set('email', $_POST['email']);

        if(isset($_POST['resend'])){
            $code = rand(1111, 9999) . "";
            $dbh->setInstructorCode("verified", $code, $_POST['email']);
            $to = $_POST['email'];
            $headers = "From: LaterGators\n";
            mail($to, 'Verification Code', $code . "\n", $headers);
        }
        elseif (isset($_POST['verify'])){
            if($_POST['verificationCode'] == $code){
                $value = 'y';
                $dbh->setInstructorCode("verified", $value, $_POST['email']);
                $f3->reroute('/profile');
            }
            else{
                $f3->set('codeError', true);
            }
        }
    }

    elseif(validSEmail($_POST['email'])) {
        $student = $dbh->getStudent($_POST['email']);
        $code = trim($student['verifiedStudent']);
        $f3->set('email', $_POST['email']);

        if(isset($_POST['resend'])){
            $code = rand(1111, 9999) . "";
            $dbh->setStudentCode("verifiedStudent", $code, $_POST['email']);
            $to = $_POST['email'];
            $headers = "From: LaterGators\n";
            mail($to, 'Verification Code', $code . "\n", $headers);
        }
        elseif (isset($_POST['verify'])){
            if($_POST['verificationCode'] == $code){
                $column = 'verifiedStudent';
                $value = 'y';
                $dbh->setStudentCode($column, $value, $_POST['email']);
                $f3->reroute('/profile');
            }
            else{
                $f3->set('codeError', true);
            }
        }
    }
    $template = new Template();
    echo $template->render('views/verifyAccount.html');
});

// run fat free
$f3->run();