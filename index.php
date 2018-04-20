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
        $template = new Template();
        echo $template->render('views/home.html');
    });

    // define a route for registration
    $f3->route('GET|POST /register', function($f3, $params) {
        $errors = array();
        if(isset($_POST['submit'])){
            $email = $_POST['email'];
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
        $template = new Template();
        echo $template->render('views/registration.html');
        if($success) {
            //$f3->reroute('/profile');
            echo "success";
        }
    });

    // define a default route
    $f3->route('GET /testConnection', function() {
        $template = new Template();
        echo $template->render('tempTestCnxn.html');
    });

    // run fat free
    $f3->run();