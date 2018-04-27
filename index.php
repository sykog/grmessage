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
$f3->route('GET|POST /', function() {
    $database = new Database();

    $template = new Template();
    echo $template->render('views/home.html');
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
        $phone = $_POST['phone'];
        $carrier = $_POST['carrier'];
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $password;
        $_SESSION['confirm'] = $confirm;
        $_SESSION['first'] = $first;
        $_SESSION['last'] = $last;
        $_SESSION['phone'] = $phone;
        $_SESSION['carrier'] = $carrier;
        if(!validEmail($email)){
            $errors['email'] = "Please enter a student email.";
        }
        if(!validPassword($password)){
            $errors['password'] = "Please enter a valid password.";
        }
        if(!validPassword($confirm)){
            $errors['confirm'] = "Please Confirm your password.";
        }
        if(!validName($first)){
            $errors['first'] = "Please enter a valid name.";
        }
        if(!validName($last)){
            $errors['last'] = "Please enter a valid name.";
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
        $_SESSION['phone'] = $phone;
        $_SESSION['carrier'] = $carrier;
        if(!validIEmail($email)){
            $errors['iemail'] = "Please enter an instructor email.";
        }
        if(!validPassword($password)){
            $errors['password'] = "Password is less than 6 characters.";
        }
        if(!validConfirm($password, $confirm)){
            $errors['confirm'] = "Password and confirmation do not match.";
        }
        if(!validPhone($phone)){
            $errors['phone'] = "Invalid phone number.";
        }
        if(!validCarrier($carrier)) {
            $errors['phone'] = "Invalid carrier.";
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
                echo "Instructor registered!";
            }
        }
    }
});

// define a message route
$f3->route('GET|POST /message', function($f3, $params) {
    $dbh = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);
    if(isset($_POST['submit'])){
        $textMessage = $_POST['textMessage'];
        $textMessage = trim($textMessage);
        $f3->set('textMessage', $textMessage);
        if(validTextMessage ($textMessage)){
            $to = "2536531125@vtext.com";
            $headers = "From: LaterGators\n";
            mail($to, '', $textMessage ."\n", $headers);
            //echo $textMessage."length".strlen($textMessage);
        }
        else{
            echo "must be between 1 and 250 characters";
        }
    }
    $template = new Template();
    echo $template->render('views/instructorMessage.html');
});

$f3->route('POST /login', function($f3) {

    $dbh = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);
    $success = $dbh->login($_POST['email'], $_POST['password']);
    if($success){
        //$f3->reroute('/profile');
        echo "success";
    }
    else{
        //$f3->reroute('/');
        echo "failure";
    }

});


// run fat free
$f3->run();