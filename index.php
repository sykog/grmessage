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
    $database = new Database();

    $template = new Template();
    echo $template->render('views/home.html');

    //if login button is clicked
    if (isset($_POST['login'])) {

        $email = $_POST['email'];
        $password = sha1($_POST['password']);
        $success = true;
        $student = $database->getStudent($email);
        $f3->set('student', $student);

        // have to use [0] since the array is in an array
        if($student[0]['studentEmail'] == $email && ($student[0]['password'] == $password)) {
            $success = true;
        }
        else $success = false;

        if($success) {
            $_SESSION['studentEmail'] = $student[0]['studentEmail'];
            $f3->set('studentEmail', $_SESSION['studentEmail']);

            $_SESSION["message"] = "";
            $f3->reroute("/profile");
        }

        else {
            echo "Incorrect email or password <br>";
            echo $student[0]['studentEmail'] . "<br>";
            echo $student[0]['password']. "<br>";
            echo $email . "<br>";
            echo $password;
        }
    }
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
            $errors['email'] = "Please enter a student email.";
        }
        if(!validPassword($password)){
            $errors['password'] = "Please enter a valid password.";
        }
        if(!validPassword($confirm)){
            $errors['confirm'] = "Please Confirm your password.";
        }
        if(!validPhone($phone)){
            $errors['phone'] = "Invalid. Phone Number.";
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
            $errors['iemail'] = "Please enter an instructor email.";
        }
        if(!validPassword($password)){
            $errors['password'] = "Password is less than 6 characters.";
        }
        if(!validConfirm($password, $confirm)){
            $errors['confirm'] = "Password and confirmation do not match.";
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
                echo "email already exists";
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
                echo "email already exists";
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
                $to = "2536531125@vtext.com";
                $headers = "From: LaterGators\n";
                mail($to, '', $textMessage . "\n", $headers);

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
    $dbh = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);
    $template = new Template();
    echo $template->render('views/studentProfile.html');
});

// run fat free
$f3->run();