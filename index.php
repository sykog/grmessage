<?php
ini_set('display-errors', 1);
error_reporting(E_ALL);

// require autoload file
require_once "vendor/autoload.php";
require_once $_SERVER['DOCUMENT_ROOT']."/../config.php";

//Start the session
session_start();
include "validate.php";
include "randomString.php";
include "gatorlock.php";

// create instance of base class
$f3 = Base::instance();
$f3->set('DEBUG', 3);

$f3->set('carriers', array("Verizon", "AT&T", "Sprint", "T-Mobile", "Boost Mobile",
    "Cricket Wireless", "Virgin Mobile", "Republic Wireless", "U.S. Cellular", "Alltel"));
$f3->set('programs', array("Bachelors - Software Development", "Associates - Software Development",
    "Bachelors - Networking", "Associates - Networking", "Bachelors - Aeronautical Science", "Associates - Aviation"));

// define a default route
$f3->route('GET|POST /', function($f3, $params) {

    unset($_SESSION['resendCode']);
    unset($_SESSION['oldCode']);
    unset($_SESSION['codeError']);
    unset($_SESSION['verificationCode']);
    unset($_SESSION['passwordError']);
    unset($_SESSION['passwordChanged']);

    if (isset($_SESSION['loginError'])) $f3->set('loginError', $_SESSION['loginError']);

    if (!$_SESSION['loggedIn']) {
        $database = new Database();

        //if login button is clicked
        if (isset($_POST['login'])) {

            $email = $_POST['email'];
            $_SESSION['login'] = true;
            $f3->set('username', $email);

            $password = $_POST['password'];
            $success = true;

            // if logging in as a student
            if (validSEmail($email)) {
                $student = $database->getStudent($email);
                $_SESSION['isInstructor'] = false;

                // if email is in the database, try to log in
                if($student['studentEmail'] == $email) {
                    $result = gatorlockLogin($email, $password);
                    $success = false;
                    // successful login returns 7
                    if ($result == 7) {
                        $success = true;
                    } elseif ($result == 8 || $result == 16 || $result == 0) {
                        $_SESSION['loginError'] = "Account is locked out";
                    } elseif ($result == 15) {
                        $_SESSION['loginError'] = "GatorLock account is not verified";
                    } else {
                        $_SESSION['loginError'] = "Incorrect email or password";
                    }
                } else {
                    $success = false;
                    $_SESSION['loginError'] = "Incorrect email or password";
                }
            }

            // if logging in as an instructor
            elseif (validIEmail($email)) {
                $instructor = $database->getInstructor($email);
                $_SESSION['isInstructor'] = true;

                // if email is in the database, try to log in
                if($instructor['email'] == $email) {
                    $result = gatorlockLogin($email, $password);
                    $success = true;
                    // successful login returns 7
                    if ($result == 7) {
                        $success = true;
                    } elseif ($result == 8 || $result == 16) {
                        $_SESSION['loginError'] = "Account is locked out after 5th failed login";
                    } elseif ($result == 15) {
                        $_SESSION['loginError'] = "GatorLock account is not verified";
                    } else {
                        $_SESSION['loginError'] = "Incorrect email or password";
                    }
                } else {
                    $success = false;
                    $_SESSION['loginError'] = "Incorrect email or password";
                }
            }
            // if the username is invalid, display login error
            else {
                $success = false;
                $_SESSION['loginError'] = "Incorrect email or password";
            }

            if($success) {
                $_SESSION['loggedIn'] = true;
                $_SESSION['email'] = $email;
                $f3->reroute("/profile");
            } else {
                $f3->reroute("/");
                return; // prevents POST on refresh
            }
        }

        /*if (isset($_POST['resend'])) {

            $email2 = $_POST['email2'];
            $newPassword = randomString(8);

            // if logging in as a student
            if (validSEmail($email2)) {
                $student = $database->getStudent($email2);
                $database->changeStudentPassword($email2, $newPassword);
                mail($email2, 'Password Reset',"Here is your new password: " .$newPassword . "\n");
            }

            // if logging in as an instructor
            elseif (validIEmail($email2)) {
                $instructor = $database->getInstructor($email2);
                $database->changeInstructorPassword($email2, $newPassword);
                mail($email2, 'Password Reset', "Here is your new password: " .$newPassword . "\n");
            }
        }*/
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
    $f3->set('instructor', false);

    // Create the transport
    $transport = (new Swift_SmtpTransport('mail.asuarez.greenriverdev.com', 465, 'ssl'))
        ->setUsername(EMAIL_USERNAME)
        ->setPassword(EMAIL_PASSWORD);
    // Create the Mailer using your created Transport
    $mailer = new Swift_Mailer($transport);

    // if registering as a student
    if(isset($_POST['submitS'])) {
        $email = $_POST['semail'];
        $gatorLock = $_POST['gatorLock'];
        $password = $_POST['password'];
        $confirm = $_POST['confirm'];
        $first = $_POST['first'];
        $last = $_POST['last'];
        $phone = shortenPhone($_POST['phone']);
        $carrier = $_POST['carrier'];
        $program = $_POST['program'];

        //if(!validSEmail($email)) $errors['email'] = "Please enter a student email";
        if($database->studentExists($email)) $errors['email'] = "Email already exists";
        // check for a successful login if user already has a gatorlock account
        if (isset($gatorLock)) {
            $loginCode = gatorlockLogin($email, $password);
            // if not a successful login
            if ($loginCode != 7) $errors['password'] = "Incorrect email or password";
        } else {
            if (!validPassword($password)) $errors['password'] = "Please enter a valid password";
            if(!validConfirm($password, $confirm)) $errors['confirm'] = "Please confirm your password";
        }
        if(!validPhone($phone)) $errors['phone'] = "Invalid phone number" . strlen($phone);
        if(!validCarrier($carrier)) $errors['carrier'] = "Invalid carrier" . strlen($phone);
        if(!validProgram($program)) $errors['program'] = "Invalid program" . strlen($phone);

        $success = sizeof($errors) == 0;
        $f3->set('instructor', false);
    }
    // if registering as an instructor
    if(isset($_POST['submitI'])) {
        $email = $_POST['iemail'];
        $gatorLock = $_POST['gatorLock'];
        $password = $_POST['password'];
        $confirm = $_POST['confirm'];
        $first = $_POST['first'];
        $last = $_POST['last'];
        $phone = $_POST['phone'];
        $carrier = $_POST['carrier'];

        if(!validIEmail($email)) $errors['email'] = "Please enter an instructor email";
        if($database->instructorExists($email)) $errors['email'] = "Email already exists";
        // check for a successful login if user already has a gatorlock account
        if (isset($gatorLock)) {
            $loginCode = gatorlockLogin($email, $password);
            // if not a successful login
            if ($loginCode != 7) $errors['password'] = "Incorrect email or password";
        } else {
            if (!validPassword($password)) $errors['password'] = "Please enter a valid password";
            if(!validConfirm($password, $confirm)) $errors['confirm'] = "Please confirm your password";
        }

        $success = sizeof($errors) == 0;
        $f3->set('instructor', true);
    }

    if($success) {
        $_SESSION['email'] = $email;
        $result = gatorLockRegister($email, $password, $first, $last);

        // 0 = register success
        if ($result == 0 || ($result == 3 && isset($gatorLock))) {
            // if student account
            if ($email == $_POST['semail']) {
                $database->addStudent($email, $password, $phone, $first, $last, $carrier, $program);
                $_SESSION['loggedIn'] = true;

                // generate code
                $studentCode = randomString(6);
                $database->setStudentCode("verifiedStudent", $studentCode, $email);

                $body = '<html lang="en">' .
                '<body>' .
                '<h3 style="color:#006225">Green River Messaging</h3>' .
                '<p>Here is your account verification code:</p>' .
                '<h3>' . $studentCode . '</h3>' .
                '<p>Verification codes last for one hour. If it has been over an hour, you can request a new code ' .
                'by logging into your account</p>' .
                '</body>' .
                '</html>';

                // create the message
                $message = (new Swift_Message())
                    ->setSubject('Account Verification Code')
                    ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
                    ->setTo($email)
                    ->setBody($body, 'text/html');

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

                $body = '<html lang="en">' .
                '<body>' .
                '<h3 style="color:#006225">Green River Messaging</h3>' .
                '<p>Here is your account verification code:</p>' .
                '<h3>' . $instructorCode . '</h3>' .
                '<p>Verification codes last for one hour. If it has been over an hour, you can request a new code' .
                'by logging into your account</p>' .
                '</body>' .
                '</html>';

                // create the message
                $message = (new Swift_Message())
                    ->setSubject('Account Verification Code')
                    ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
                    ->setTo($email)
                    ->setBody($body, 'text/html');

                // send the message
                $result = $mailer->send($message);

                $f3->reroute("/verify");
            }
        }
        // gatorlock email already exists
        else if ($result == 3) {
            $errors['gatorlock'] = "You already have an account with GatorLock. 
                    Please check the box at the top of the screen and register with those credentials";
        }
    }

    $f3->set('fields', $_POST);
    $f3->set('errors', $errors);
    $f3->set('success', $success);

    $template = new Template();
    echo $template->render('views/registration.html');
});

// define a route for verifying account
$f3->route('GET|POST /verify', function($f3) {

    $database = new Database();
    $email = $_SESSION['email'];
    $expired = "Codes are only valid for 1 hour. Please request a new code";
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

        if (validIEmail($email)) $database->setInstructorCode($code, $email);
        else $database->setStudentCode("verifiedStudent", $code, $email);

        $body = '<html lang="en">' .
                '<body>' .
                '<h3 style="color:#006225">Green River Messaging</h3>' .
                '<p>Here is your account verification code:</p>' .
                '<h3>' . $code . '</h3>' .
                '<p>Verification codes last for one hour. If it has been over an hour, you can request a new code' .
                'by logging into your account</p>' .
                '</body>' .
                '</html>';

        // create the message
        $message = (new Swift_Message())
            ->setSubject('Account Verification Code')
            ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
            ->setTo($email)
            ->setBody($body, 'text/html');

        // send the message
        $result = $mailer->send($message);
        $f3->reroute("/verify");
        return; // prevents another POST on refresh
    }

    elseif (isset($_POST['verify'])){
        // if code is correct, verify in database
        if ($_POST['verificationCode'] == $code && strlen($_POST['verificationCode']) > 0){
            // student email code is less than an hour old
            if(validSEmail($email) && $database->compareTimeStudent($email)) {

                $database->setStudentCode("verifiedStudent", "y", $email);
                $_SESSION['codeError'] = "false";
            }
            // instructor email code is less than an hour old
            elseif(validIEmail($email) && $database->compareTimeInstructor($email)) {

                $database->setInstructorCode("y", $email);
                $_SESSION['codeError'] = "false";
            } else {
                $_SESSION['codeError'] = $expired;
            }
        } elseif ($_SESSION['codeError'] != $expired) {
            $_SESSION['codeError'] = "Invalid code";
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
        $_SESSION['codeError'] = "false";
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
    unset($_SESSION['sent']);

    /* if (isset($_SESSION['passwordError'])) $f3->set('passwordError', $_SESSION['passwordError']);
    if (isset($_SESSION['passwordChanged'])) $f3->set('passwordChanged', $_SESSION['passwordChanged']);
    if (isset($_SESSION['nameError'])) $f3->set('nameError', $_SESSION['nameError']);*/

    // go to login page if not logged in
    if(!$_SESSION['loggedIn']) {
        $f3->reroute("/");
    }

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
        $f3->set('fname', $instructor['fname']);
        $f3->set('lname', $instructor['lname']);

        // changing instructor's name
        if (isset($_POST['updateName'])) {
            // can't be empty
            if (strlen($_POST['newFName']) > 0 && strlen($_POST['newLName'])) {
                $database->changeInstructorName($email, $_POST['newFName'], $_POST['newLName']);
            } else {
                $_SESSION['nameError'] = "First or last name cannot be blank";
            }
            $f3->reroute("/profile");
            return; // prevents POST on refresh
        }

        // change password if button was clicked
        /* else if (isset($_POST['updatePassword'])) {

            $currentPassword = sha1($_POST['currentPassword']);
            $newPassword = $_POST['newPassword'];
            $confirmPassword = $_POST['confirmPassword'];

            if ($currentPassword == $instructor['password']) {
                if ($newPassword == $confirmPassword) {
                    if (strlen($newPassword) > 7) {
                        $database->changeInstructorPassword($email, $newPassword);
                        $_SESSION['passwordChanged'] = "changed";
                    } else {
                        $_SESSION['passwordError'] = "Password must be at least 8 characters";
                    }
                } else {
                    $_SESSION['passwordError'] = "Passwords do not match";
                }
            } else {
                $_SESSION['passwordError'] = "Current password is incorrect";
            }
            $f3->reroute("/profile");
            return; // prevents POST on refresh
        }
        else {
            unset($_SESSION['passwordError']);
            unset($_SESSION['nameError']);
            unset($_SESSION['passwordChanged']);
        }*/

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
        $f3->set('fname', $student['fname']);
        $f3->set('lname', $student['lname']);
        $f3->set('phone', $student['phone']);
        $f3->set('carrier', $student['carrier']);
        $f3->set('program', $student['program']);
        $f3->set('personalEmail', $student['personalEmail']);
        $f3->set('getTexts', $student['getTexts']);
        $f3->set('getStudentEmails', $student['getStudentEmails']);
        $f3->set('getPersonalEmails', $student['getPersonalEmails']);

        // change password if button was clicked
        /*if (isset($_POST['updatePassword'])) {

            $currentPassword = sha1($_POST['currentPassword']);
            $newPassword = $_POST['newPassword'];
            $confirmPassword = $_POST['confirmPassword'];

            if ($currentPassword == $student['password']) {
                if ($newPassword == $confirmPassword) {
                    if (strlen($newPassword) > 7) {
                        $database->changeStudentPassword($email, $newPassword);
                        $_SESSION['passwordChanged'] = "changed";
                    } else {
                        $_SESSION['passwordError'] = "Password must be at least 8 characters";
                    }
                } else {
                    $_SESSION['passwordError'] = "Passwords do not match";
                }
            } else {
                $_SESSION['passwordError'] = "Current password is incorrect";
            }
            $f3->reroute("/profile");
            return; // prevents POST on refresh
        } else {
            unset($_SESSION['passwordError']);
            unset($_SESSION['passwordChanged']);
        }*/

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
    $studentUrl = "";
    $optOut = '<hr><p style="color:#7D7D7D">If you would like to stop receiving updates, uncheck the notifications on your <a href="http://asuarez.greenriverdev.com/355/grmessage/unsubscribe/'.
        $studentUrl.'">http://asuarez.greenriverdev.com/355/grmessage/unsubscribe/'.$studentUrl.'</a>.</p>';

    // go back to home page if not logged in
    if(!$_SESSION['loggedIn']) $f3->reroute("/");
    // go to profile if logged in, but as a student
    if($_SESSION['loggedIn'] && !$_SESSION['isInstructor']) $f3->reroute("/profile");

    if (isset($_SESSION['sent'])) $f3->set('sent', $_SESSION['sent']);
    $email = $_SESSION['email'];
    $database = new Database(DB_DSN, DB_USERNAME, DB_PASSWORD);

    $programs = $f3->get("programs");
    $studentCount = array();
    // get the number of current subscribers for each program
    foreach($programs as $program) {
        $subscribers = $database->studentCount($program);
        $studentCount[$program] = $subscribers;
    }

    $f3->set("studentCount", $studentCount);
    $instructor = $database->getInstructor($email);

    // go to verification page if not account hasn't been verified
    if(trim($instructor['verified']) != 'y') {
        $f3->reroute('/verify');
    }
    if(isset($_POST['submit'])) {
        if (!empty($_POST['subject'])) $subject = $_POST['subject'];
        else $subject = "Green River Messaging";
        $textMessage = trim($_POST['textMessage']);
        // keeps line breaks
        $emailMessage = nl2br($textMessage);

        $f3->set('subject', $subject);
        $f3->set('textMessage', $textMessage);

        if(validTextMessage ($textMessage)) {
            $chosenProgram = $_POST['chosenPrograms']; // gets the selected program(s)
            $students = $database->getStudents();

            // send message to every student by selecting the program, then the student
            foreach ((array)$chosenProgram as $current) {
                foreach ($students as $studentInfo) {
                    // encrypt student email to but used in opt out link
                    $studentUrl = sha1($studentInfo['studentEmail']);
                    $optOut = '<hr><p style="color:#7D7D7D">If you would like to stop receiving updates, opt out here: <a href="http://asuarez.greenriverdev.com/355/grmessage/unsubscribe/'.
                            $studentUrl.'">http://asuarez.greenriverdev.com/355/grmessage/unsubscribe/'.$studentUrl.'</a>.</p>';
                    // format email with html
                    $body = '<html lang="en">' .
                            '<body>' .
                            '<h3 style="color:#006225">Green River Messaging</h3>' .
                            '<p>' . $emailMessage . '</p>' .
                            $optOut .
                            '</body>' .
                            '</html>';


                    if ($studentInfo['program'] == $current && validProgram($current)) {
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
                                ->setSubject($subject)
                                ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
                                ->setTo($studentInfo['personalEmail'])
                                ->setBody($body, 'text/html');

                            // send the message
                            $result = $mailer->send($message);
                        }
                        // only send email if opted in and verified
                        if ($studentInfo['getStudentEmails'] == "y" && $studentInfo['verifiedStudent'] == 'y') {
                            // create the message
                            $message = (new Swift_Message())
                                ->setSubject($subject)
                                ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
                                ->setTo($studentInfo['studentEmail'])
                                ->setBody($body, 'text/html');

                            // send the message
                            $result = $mailer->send($message);
                        }
                    }
                }
            }

            // store message in the database, get chosen programs
            $recipient = implode(", ", (array)$chosenProgram);
            $database->storeMessage($email, $textMessage, $recipient);

            // confirm message was sent
            $_SESSION['sent'] = "true";
            $f3->reroute('/message');
            return; // prevents another POST on refresh
        }
    }
    unset($_SESSION['sent']);
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
    // replace the emails with the instructor's name
    foreach($messages as &$message) {
        $name = $database->getInstructor($message['instructorEmail'])['fname'] . " " .
                $database->getInstructor($message['instructorEmail'])['lname'];
        $message['instructorEmail'] = $name;
    }
    $f3->set('messages', $messages);

    $template = new Template();
    echo $template->render('views/viewMessages.html');
});

// define a message viewing route
$f3->route('GET|POST /unsubscribe/@student', function($f3, $params) {

    $database = new Database(DB_DSN, DB_USERNAME, DB_PASSWORD);
    // get the student email from the url
    $student = $database->getStudentByURL($params['student']);
    $email = $student['studentEmail'];
    $f3->set('email', $email);

    // go to home page if not an existing student url
    if (empty($email)) $f3->reroute("/");

    // set every contact to no
    if(isset($_POST['optOut'])) {
        $database->updatePreferences($email, "n", "n", "n");
        $f3->set('unsubscribe', "true");
    }

    $template = new Template();
    echo $template->render('views/unsubscribe.html');
});

// run fat free
$f3->run();