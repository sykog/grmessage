<?php
    // connect to database
    require_once $_SERVER['DOCUMENT_ROOT']."/../config.php";
    require_once "../model/Database.php";

    $database = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);

    $email = trim($_POST['email']);
    $verifiedPhone = $database->getStudent($email)['verifiedPhone'];
    $verifiedEmail = $database->getStudent($email)['verifiedPersonal'];

    if ($verifiedEmail == y && $verifiedPhone == "y") echo "both";
    else if ($verifiedPhone == "y") echo "phone";
    else if ($verifiedEmail == y) echo "email";
    else echo $verifiedPhone . ":" . $verifiedEmail;