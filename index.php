<?php
ini_set('display-errors', 1);
error_reporting(E_ALL);

// require autoload file
require_once('vendor/autoload.php');
require_once $_SERVER['DOCUMENT_ROOT']."/../config.php";

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

    unset($_SESSION['resendCode']);
    unset($_SESSION['oldCode']);
    unset($_SESSION['codeError']);
    unset($_SESSION['verificationCode']);

    if (isset($_SESSION['loginError'])) $f3->set('loginError', $_SESSION['loginError']);

    if (!$_SESSION['loggedIn']) {
        $database = new Database();

        //if login button is clicked
        if (isset($_POST['login'])) {

            $email = $_POST['email'];
            $_SESSION['login'] = true;
            $f3->set('username', $email);

            $password = sha1($_POST['password']);
            $success = true;

            // if logging in as a student
            if (validSEmail($email)) {
                $student = $database->getStudent($email);
                $_SESSION['isInstructor'] = false;

                if($student['studentEmail'] == $email && ($student['password'] == $password)) {
                    $success = true;
                }
            }

            // if logging in as an instructor
            elseif (validIEmail($email)) {
                $instructor = $database->getInstructor($email);
                $_SESSION['isInstructor'] = true;
                if($instructor['email'] == $email && ($instructor['password'] == $password)) {
                    $success = true;
                }
            }
            // if the username is invalid, display login error
            else {
                $success = false;
                $_SESSION['loginError'] = true;
            }

            if($success) {
                $_SESSION['loggedIn'] = true;
                $_SESSION['email'] = $email;

                $f3->reroute("/profile");
            }
            else {
                $f3->reroute("/");
                return; // prevents POST on refresh
            }
        }

        if (isset($_POST['resend'])) {

            $email2 = $_POST['email2'];
            $newPassword = randomString(8);

            // if logging in as a student
            if (validSEmail($email2)) {
                $student = $database->getStudent($email2);
                $database->changeStudentPassword($email2, $newPassword);
                //mail($email2, 'Password Reset',"Here is your new password: " .$newPassword . "\n");
            }

            // if logging in as an instructor
            elseif (validIEmail($email2)) {
                $instructor = $database->getInstructor($email2);
                $database->changeInstructorPassword($email2, $newPassword);
                //mail($email2, 'Password Reset', "Here is your new password: " .$newPassword . "\n");
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

    if ($_SESSION['login']) {
        $_SESSION['login'] = false;
        $_SESSION['loginError'] = false;
    }

    $template = new Template();
    echo $template->render('views/home.html');
});

// define a route for logout
$f3->route('GET|POST /logout', function($f3, $params) {

    $_SESSION['loggedIn'] = false;
    $_SESSION['isInstructor'] = false;
    unset($_SESSION['email']);

    $template = new Template();
    $f3->reroute("/");
});

// define a route for registration
$f3->route('GET|POST /register', function($f3, $params) {

    $database = new Database();
    $errors = array();
    // Create the transport
    $transport = (new Swift_SmtpTransport('mail.asuarez.greenriverdev.com', 465, 'ssl'))
        ->setUsername(EMAIL_USERNAME)
        ->setPassword(EMAIL_PASSWORD);
    // Create the Mailer using your created Transport
    $mailer = new Swift_Mailer($transport);

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

        if(!validSEmail($email)) {
            $errors['email'] = "Please enter a student email";
        }
        if($database->studentExists($email)) {
            $errors['email'] = "Email already exists";
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

        if(!validIEmail($email)){
            $errors['iemail'] = "Please enter an instructor email";
        }
        if($database->instructorExists($email)) {
            $errors['email'] = "Email already exists";
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
        $_SESSION['email'] = $email;

        // if student account
        if ($email == $_POST['semail']) {
            $database->addStudent($email, $password, $phone, $first, $last, $carrier, $program);
            $_SESSION['loggedIn'] = true;
            $studentCode = randomString(6);
            $database->setStudentCode("verifiedStudent", $studentCode, $email);

            // create the message
            $message = (new Swift_Message())
                ->setSubject('Account Verification Code')
                ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
                ->setTo($email)
                ->setBody("Account Verification Code: " . $studentCode, 'text/html');

            // send the message
            $result = $mailer->send($message);

            $f3->reroute("/verify");
        }

        // if instructor account
        if ($email == $_POST['iemail']) {
            $database->addInstructor($email, $password, $first, $last);
            $_SESSION['loggedIn'] = true;

            // generate code
            $instructorCode = randomString(6);
            $database->setInstructorCode($instructorCode, $email);

            // create the message
            $message = (new Swift_Message())
                ->setSubject('Account Verification Code')
                ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
                ->setTo($email)
                ->setBody("Account Verification Code: " . $instructorCode, 'text/html');

            // send the message
            $result = $mailer->send($message);

            $f3->reroute("/verify");
        }
    }
});

// define a route for verifying account
$f3->route('GET|POST /verify', function($f3) {

    $database = new Database();
    $email = $_SESSION['email'];
    $f3->set("email", $email);
    if (isset($_SESSION['codeError'])) $f3->set('codeError', $_SESSION['codeError']);

    // Create the transport
    $transport = (new Swift_SmtpTransport('mail.asuarez.greenriverdev.com', 465, 'ssl'))
        ->setUsername(EMAIL_USERNAME)
        ->setPassword(EMAIL_PASSWORD);
    // Create the Mailer using your created Transport
    $mailer = new Swift_Mailer($transport);

    // if an instructor account
    if (validIEmail($email)) $code = trim($database->getInstructor($email)['verified']);
    // if a student account
    else $code = trim($database->getStudent($email)['verifiedStudent']);

    // send new code when pressing resend
    if (isset($_POST['resend'])){
        $_SESSION['oldCode'] = $code; // for checking if a new code was sent
        $code = randomString(6);
        $_SESSION['resendCode'] = $code; // store in session so is saves on redirect

        if (validIEmail($email)) $database->setInstructorCode("verified", $code, $email);
        else $database->setStudentCode("verifiedStudent", $code, $email);

        // create the message
        $message = (new Swift_Message())
            ->setSubject('Account Verification Code')
            ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
            ->setTo($email)
            ->setBody("Account Verification Code: " . $code, 'text/html');

        // send the message
        $result = $mailer->send($message);
        $f3->reroute("/verify");
        return; // prevents another POST on refresh
    }

    elseif (isset($_POST['verify'])){
        // if code is correct, verify in database
        if ($_POST['verificationCode'] == $code && strlen($_POST['verificationCode']) > 0){
            // store in session so is saves on redirect
            $_SESSION['verificationCode'] == $_POST['verificationCode'];
            $value = 'y';

            if (validIEmail($email))  $database->setInstructorCode("verified", $value, $email);
            else $database->setStudentCode("verifiedStudent", $value, $email);

            $_SESSION['codeError'] = false;
        } else {
            $_SESSION['codeError'] = true;
        }
        $_SESSION['verify'] = true;
        $f3->reroute('/profile');
        return; // prevents another POST on refresh
    }

    // only display the messages once
    if ($_SESSION['resendCode'] != $_SESSION['oldCode']) {
        $f3->set('sent', true);
        unset($_SESSION['resendCode']);
        unset($_SESSION['oldCode']);
    }
    if ($_SESSION['verify']) {
        $_SESSION['verify'] = false;
        $_SESSION['codeError'] = false;
    }

    $template = new Template();
    echo $template->render('views/verifyAccount.html');
});

// define a profile route (used for students and instructors)
$f3->route('GET|POST /profile', function($f3, $params) {

    unset($_SESSION['resendCode']);
    unset($_SESSION['oldCode']);
    unset($_SESSION['login']);
    unset($_SESSION['loginError']);

    // Create the transport
    $transport = (new Swift_SmtpTransport('mail.asuarez.greenriverdev.com', 465, 'ssl'))
        ->setUsername(EMAIL_USERNAME)
        ->setPassword(EMAIL_PASSWORD);
    // Create the Mailer using your created Transport
    $mailer = new Swift_Mailer($transport);

    // go to login page if not logged in
    if(!$_SESSION['loggedIn']) {
        $f3->reroute("/");
    }

    $headers = "From: Green River Messaging\n";
    $email = $_SESSION['email'];

    // instructor profile page
    if($_SESSION['loggedIn'] && $_SESSION['isInstructor']) {

        $database = new Database(DB_DSN, DB_USERNAME, DB_PASSWORD);
        //set f3 variables
        $instructor = $database->getInstructor($email);
        // if account hasn't been verified
        if (trim($instructor['verified']) != 'y') {
            $f3->reroute('/verify');
        }

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
        $student = $database->getStudent($email);
        // if account hasn't been verified
        if (trim($student['verifiedStudent']) != 'y') {
            $f3->reroute('/verify');
        }

        $errors = array();

        $f3->set('studentEmail', $email);
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

        //if changes were made
        if (isset($_POST['save'])) {

            if (isset ($_POST['getStudentEmails'])) $getStudentEmails = 'y';
            else $getStudentEmails = 'n';

            if (isset ($_POST['getTexts'])) $getTexts = 'y';
            else $getTexts = 'n';

            if (isset ($_POST['getPersonalEmails'])) $getPersonalEmails = 'y';
            else $getPersonalEmails = 'n';

            $database->updatePreferences($email, $getStudentEmails, $getTexts, $getPersonalEmails);
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
                        $database->changeStudentPassword($email, $newPassword);
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
                $database->changeStudentName($email, $_POST['newFName'], $_POST['newLName']);
                $f3->reroute("/profile");
            } else {
                $errors['name'] = "Please enter a valid name.";
            }
        }

        // if update personal email button was clicked
        if (isset($_POST['updatePersonalEmail'])) {
            if (validPEmail($_POST['newPersonalEmail'])) {
                $database->changePersonalEmail($email, $_POST['newPersonalEmail']);

                // send a code when changing email
                $code = randomString(6);
                $database->setStudentCode("verifiedPersonal", $code, $email);

                // create the message
                $message = (new Swift_Message())
                    ->setSubject('Verification Code')
                    ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
                    ->setTo($_POST['newPersonalEmail'])
                    ->setBody("Personal Email Verification Code: ". $code, 'text/html');

                // send the message
                $result = $mailer->send($message);
            } else {
                $errors['pEmail'] = "Please enter a valid email.";
            }
            $f3->reroute("/profile");
        }

        //if the personal email verification button was clicked
        if (isset($_POST['verifyPersonal'])) {
            if ($_POST['personalVerification'] == $student['verifiedPersonal']) {
                $column = "verifiedPersonal";
                $value = "y";
                $f3->set('verifiedPersonal', true);
                $database->setStudentCode($column, $value, $email);
            } else {
                $errors['personalVerificaton'] = "Incorrect verification code.";
            }
        }

        // resend phone verification
        if(isset($_POST['resendPEmail'])){
            $code = randomString(6);
            $column = "verifiedPersonal";
            $database->setStudentCode($column, $code, $email);

            // create the message
            $message = (new Swift_Message())
                ->setSubject('Verification Code')
                ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
                ->setTo($student['personalEmail'])
                ->setBody("Personal Email Verification Code: ". $code, 'text/html');

            // send the message
            $result = $mailer->send($message);
        }

        // if update phone number button was clicked
        if (isset($_POST['updatePhone'])) {
            $newPhone = $_POST['newPhone'];
            $newPhone = shortenPhone($newPhone);
            if (validPhone($newPhone) && strlen($newPhone) != 0) {
                $database->changePhoneNumber($email, $newPhone);

                // send a code when changing phone number
                $phoneCode = randomString(6);
                $column = "verifiedPhone";
                $database->setStudentCode($column, $phoneCode, $email);

                if (isset($_POST['newCarrier'])) $carrierInfo = $database->getCarrierInfo($_POST['newCarrier']);
                else $carrierInfo = $database->getCarrierInfo($student['carrier']);

                $carrierEmail = $carrierInfo['carrierEmail'];
                $to = $newPhone . "@" . $carrierEmail;

                // create the message
                $message = (new Swift_Message())
                    ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
                    ->setTo($to)
                    ->setBody("Phone Verification Code: ". $phoneCode, 'text/html');

                // send the message
                $result = $mailer->send($message);
            } else {
                $errors['phone'] = "Please enter a valid phone number.";
            }
            $f3->reroute("/profile");
        }

        // if update phone carrier button was clicked
        if (isset($_POST['updateCarrier'])) {
            if (validCarrier($_POST['newCarrier'])) {
                $database->changeCarrier($email, $_POST['newCarrier']);

                if (!isset($_POST['updatePhone'])) {
                    $phoneCode = randomString(6);
                    $column = "verifiedPhone";
                    $database->setStudentCode($column, $phoneCode, $email);
                    $carrierInfo = $database->getCarrierInfo($_POST['newCarrier']);
                    $carrierEmail = $carrierInfo['carrierEmail'];
                    $to = $student['phone'] . "@" . $carrierEmail;

                    // create the message
                    $message = (new Swift_Message())
                        ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
                        ->setTo($to)
                        ->setBody("Phone Verification Code: ". $phoneCode, 'text/html');

                    // send the message
                    $result = $mailer->send($message);
                }
            }
            $f3->reroute("/profile");
        }

        //if the phone verification button was clicked
        if (isset($_POST['verifyPhone'])) {
            if ($_POST['phoneVerification'] == $student['verifiedPhone']) {
                $column = 'verifiedPhone';
                $value = 'y';
                $database->setStudentCode($column, $value, $email);
                $f3->set('verifiedPhone', true);
            } else {
                $errors['phoneVerificaton'] = "Incorrect verification code.";
            }
        }

        // resend phone verification
        if(isset($_POST['resendPhone'])){
            $phoneCode = randomString(6);
            $column = 'verifiedPhone';
            $database->setStudentCode($column, $code, $email);
            $carrierInfo = $database->getCarrierInfo($student['carrier']);
            $carrierEmail = $carrierInfo['carrierEmail'];
            $to = $student['phone'] . "@" . $carrierEmail;

            // create the message
            $message = (new Swift_Message())
                ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
                ->setTo($to)
                ->setBody("Phone Verification Code: ". $phoneCode, 'text/html');

            // send the message
            $result = $mailer->send($message);
        }

        // if update program button was clicked
        if (isset($_POST['updateProgram'])) {
            if (validProgram($_POST['newProgram'])) {
                $database->changeProgram($email, $_POST['newProgram']);
                $f3->reroute("/profile");
            }
        }

        $f3->set("errors", $errors);
        $template = new Template();
        echo $template->render('views/studentProfile.html');
    }
});

// define a message route
$f3->route('GET|POST /message', function($f3, $params) {

    // Create the transport
    $transport = (new Swift_SmtpTransport('mail.asuarez.greenriverdev.com', 465, 'ssl'))
        ->setUsername(EMAIL_USERNAME)
        ->setPassword(EMAIL_PASSWORD);
    // Create the Mailer using your created Transport
    $mailer = new Swift_Mailer($transport);
    $optOut = "<hr>If you would like to stop receiving updates, visit your profile page to opt-out: <a href='asuarez.greenriverdev.com/355/grmessage/'>asuarez.greenriverdev.com/355/grmessage/</a>";

    // go back to home page if not logged in
    if(!$_SESSION['loggedIn']) $f3->reroute("/");
    // go to profile if logged in, but as a student
    if($_SESSION['loggedIn'] && !$_SESSION['isInstructor']) {
        $f3->reroute("/profile");
    }
    $email = $_SESSION['email'];
    $database = new Database(DB_DSN, DB_USERNAME, DB_PASSWORD);
    $f3->set("students", $database->getStudents());
    $instructor = $database->getInstructor($email);

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

                            // create the message
                            $message = (new Swift_Message())
                                ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
                                ->setTo($to)
                                ->setBody($textMessage, 'text/html');

                            // send the message
                            $result = $mailer->send($message);
                        }
                        // only send secondary email if opted in and verified
                        if ($studentInfo['getPersonalEmails'] == "y" && $studentInfo['verifiedPersonal'] == 'y') {
                            // create the message
                            $message = (new Swift_Message())
                                ->setSubject('Green River Messaging IT')
                                ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
                                ->setTo($studentInfo['personalEmail'])
                                ->setBody($textMessage . $optOut, 'text/html');

                            // send the message
                            $result = $mailer->send($message);
                        }
                        // only send email if opted in and verified
                        if ($studentInfo['getStudentEmails'] == "y" && $studentInfo['verifiedStudent'] == 'y') {
                            // create the message
                            $message = (new Swift_Message())
                                ->setSubject('Green River Messaging IT')
                                ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
                                ->setTo($studentInfo['studentEmail'])
                                ->setBody($textMessage . $optOut, 'text/html');

                            // send the message
                            $result = $mailer->send($message);
                        }
                    }
                }
            }

            // store message in the database, get chosen programs
            $recipient = implode(", ", (array)$chosen);
            $database->storeMessage($email, $textMessage, $recipient);

            // confirmation and remove message
            $f3->set('sent', true);
            $f3->set('textMessage', "");
        } else {
            echo "<div class=\"error alert alert-danger\" role=\"alert\">
                Message must be between 1 and 250 characters</div>";
        }

        // prevents posting again on page refresh
        //$f3->reroute("/message");
        //return;
    }
    $template = new Template();
    echo $template->render('views/instructorMessage.html');
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
    $instructor = $database->getInstructor($_SESSION['email']);
    if(trim($instructor['verified']) != 'y'){
        $f3->reroute('/verify');
    }

    // get all messages
    $messages = $database->getMessages();
    $f3->set('messages', $messages);

    $template = new Template();
    echo $template->render('views/viewMessages.html');
});

// run fat free
$f3->run();