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
        if(!validSEmail($email)){
            $errors['semail'] = "Please enter a student email.";
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
            echo "student account";
            $database->addStudent($email, $password, $phone, $first, $last, $carrier);
        }
        // if instructor account
        if ($email == $_POST['iemail']) {
            echo "instructor account";
            $database->addInstructor($email, $password, $first, $last);
        }
    }
});

// define a default route
$f3->route('GET /profile', function() {

    $dbh = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);
    $template = new Template();
    echo $template->render('views/profile.html');

});

// run fat free
$f3->run();