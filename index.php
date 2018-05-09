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


// define a default route
$f3->route('GET|POST /', function($f3, $params) {

    if (!$_SESSION['loggedin'] || !isset($_SESSION['loggedin'])){
        $database = new Database();

           $template = new Template();

        echo $template->render('views/home.html');

        //if login button is clicked
        if (isset($_POST['login'])) {

            $email = $_POST['email'];
            $password = sha1($_POST['password']);
            $success = true;
            $student = $database->getStudent($email);
            $f3->set('studentEmail', $email);

            if($student['studentEmail'] == $email && ($student['password'] == $password)) {
                $success = true;
            }
            else $success = false;

            if($success) {
                $_SESSION['loggedin'] = true;
                $_SESSION['email'] = $f3->get('studentEmail');
                if(validSEmail($email)){
                    $f3->reroute("/profile");
                }
                if(validIEmail($email)){
                    $f3->reroute("/message");
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

});

// define a route for logout
$f3->route('GET|POST /logout', function($f3, $params) {
    $database = new Database();
    $_SESSION['loggedin'] = false;
    $template = new Template();
    $f3->reroute("/");
});

// define a route for registration
$f3->route('GET|POST /register', function($f3, $params) {
    $database = new Database();
    $errors = array();

    // if registering as a student
    if(isset($_POST['submitS'])){
        $email = $_POST['semail'];
        $password = $_POST['password'];
        $confirm = $_POST['confirm'];
        $first = $_POST['first'];
        $last = $_POST['last'];
        $phone = shortenPhone($_POST['phone']);
        $carrier = $_POST['carrier'];
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $password;
        $_SESSION['confirm'] = $confirm;
        $_SESSION['first'] = $first;
        $_SESSION['last'] = $last;
        $_SESSION['phone'] = $phone;
        $_SESSION['carrier'] = $carrier;
        if(!validSEmail($email)){
            $errors['email'] = "Please enter a student email";
        }
        if(!validPassword($password)){
            $errors['password'] = "Please enter a valid password";
        }
        if(!validPassword($confirm)){
            $errors['confirm'] = "Please confirm your password";
        }
        if(!validPhone($phone)){
            $errors['phone'] = "Invalid phone number" . strlen($phone);
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
    // if registering as an instructor
    if(isset($_POST['submitI'])){
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
                $database->addStudent($email, $password, $phone, $first, $last, $carrier);
                $f3->reroute("/");
                echo "Student registered!";
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
                $f3->reroute("/message");
                echo "Instructor registered!";
            }
        }
    }
});

// define a message route
$f3->route('GET|POST /message', function($f3, $params) {
    if(!$_SESSION['loggedin']){
        $f3->reroute("/");
    }
    $dbh = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);
    $f3->set("students", $dbh->getStudents());
    if(isset($_POST['submit'])){
        $textMessage = $_POST['textMessage'];
        $textMessage = trim($textMessage);
        $f3->set('textMessage', $textMessage);
        $f3->set('sent', false);
        if(validTextMessage ($textMessage)){
            $studentEmails = $_POST['studentEmails'];
            foreach ($studentEmails as $studentEmail) {
                $studentInfo = $dbh->getStudent($studentEmail);
                if ($studentInfo['getTexts'] == "y") {
                    $carrierInfo = $dbh->getCarrierInfo($studentInfo['carrier']);
                    $carrierEmail = $carrierInfo['carrierEmail'];
                    $to = $studentInfo['phone'] . "@" . $carrierEmail;
                    $headers = "From: LaterGators\n";
                    mail($to, '', $textMessage . "\n", $headers);

                    // confirmation and remove message
                    $f3->set('sent', true);
                    $f3->set('textMessage', "");
                }
                if ($studentInfo['getPersonalEmails'] == "y") {
                    $to = $studentInfo['personalEmail'];
                    $headers = "From: LaterGators\n";
                    mail($to, '', $textMessage . "\n", $headers);

                    // confirmation and remove message
                    $f3->set('sent', true);
                    $f3->set('textMessage', "");
                }
                if ($studentInfo['getStudentEmails'] == "y") {
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
        else{
            echo "must be between 1 and 250 characters";
        }

    }
    $template = new Template();
    echo $template->render('views/instructorMessage.html');
});

$f3->route('GET|POST /profile', function($f3, $params) {

    if(!$_SESSION['loggedin']){
        $f3->reroute("/");
    }

    $dbh = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);


    $studentEmail = $_SESSION['email'];
    $student = $dbh->getStudent($studentEmail);

    $f3->set('studentEmail', $studentEmail);
    $f3->set('password', $student['password']);
    $f3->set('fname', $student['fname']);
    $f3->set('lname', $student['lname']);
    $f3->set('phone', $student['phone']);
    $f3->set('carrier', $student['carrier']);
    $f3->set('personalEmail', $student['personalEmail']);
    $f3->set('getTexts', $student['getTexts']);
    $f3->set('getStudentEmails', $student['getStudentEmails']);
    $f3->set('getPersonalEmails', $student['getPersonalEmails']);

    //if changes were made
    if(isset($_POST['save'])) {
        if(isset ($_POST['getStudentEmails'])) {
            $getStudentEmails = 'y';
            "You will now receive announcements via your student email";
        }
        else {
            $getStudentEmails = 'n';
            "You will no longer receive announcements via your student email";
        }
        if(isset ($_POST['getTexts'])) {
            $getTexts = 'y';
            echo "You will now receive announcements via text message";
        }
        else {
            $getTexts = 'n';
            echo "You will no longer receive announcements via text message";
        }
        if(isset ($_POST['getPersonalEmails'])) {
            $getPersonalEmails = 'y';
            "You will now receive announcements via your personal email";
        }
        else {
            $getPersonalEmails = 'n';
            "You will no longer receive announcements via your personal email";
        }

        $dbh->updatePreferences($studentEmail, $getStudentEmails, $getTexts, $getPersonalEmails);
    }

    /*
    //change password if button was clicked
    if(isset($_POST['changePassword'])) {

        $currentPassword = sha1($_POST['currentPassword']);
        $newPassword = sha1($_POST['newPassword']);
        $confirmPassword = sha1($_POST['confirmPassword']);

        if($currentPassword == $f3->get('password')) {
            if($newPassword == $confirmPassword) {
                $dbh->changeStudentPassword($studentEmail, $newPassword);
                echo 'Password successfully changed!';
            }
            else {
                echo 'Passwords do not match.';
            }
        }
        else {
            echo 'Current password is incorrect.';
        }
    } */

    $template = new Template();
    echo $template->render('views/studentProfile.html');
});



// run fat free
$f3->run();