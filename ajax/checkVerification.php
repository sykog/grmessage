<?php
    // connect to database
    require_once $_SERVER['DOCUMENT_ROOT']."/../config.php";
    require_once "../model/Database.php";

    $database = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);

    $email = trim($_POST['email']);
    $verifiedPhone = $database->getStudent($email)['verifiedPhone'];
    $phone = $database->getStudent($email)['phone'];
    $verifiedEmail = $database->getStudent($email)['verifiedPersonal'];
    $pemail = $database->getStudent($email)['personalEmail'];

    if (($verifiedEmail == y && $verifiedPhone == "y") || ($phone == "" && $pemail == "")) echo "both";
    else if ($verifiedPhone == "y" || $phone == "") echo "phone";
    else if ($verifiedEmail == y || $pemail == "") echo "email";
    else echo $verifiedPhone . ":" . $verifiedEmail;