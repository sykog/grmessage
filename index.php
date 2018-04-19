<?php
    ini_set('display-errors', 1);
    error_reporting(E_ALL);

    // require autoload file
    require_once('vendor/autoload.php');

    // create instance of base class
    $f3 = Base::instance();
    $f3->set('DEBUG', 3);

    // define a default route
    $f3->route('GET /', function() {
        $template = new Template();
        echo $template->render('views/home.html');
    });

    // define a default route
    $f3->route('GET /register', function() {
        $template = new Template();
        echo $template->render('views/registration.html');
    });

    // define a default route
    $f3->route('GET /testConnection', function() {
        $template = new Template();
        echo $template->render('tempTestCnxn.html');
    });

    // run fat free
    $f3->run();