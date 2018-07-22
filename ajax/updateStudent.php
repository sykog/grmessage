<?php
    // connect to database
    require_once $_SERVER['DOCUMENT_ROOT']."/../config.php";
    require_once "../model/Database.php";
    require_once "../model/randomString.php";
    require_once "../vendor/autoload.php";

    $database = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);

    // Create the transport
    $transport = (new Swift_SmtpTransport('mail.asuarez.greenriverdev.com', 465, 'ssl'))
        ->setUsername(EMAIL_USERNAME)
        ->setPassword(EMAIL_PASSWORD);
    $mailer = new Swift_Mailer($transport);

    $email = trim($_POST['email']);

    // update based on column name
    if ($_POST['column'] == "name") {
        $database->changeStudentName($email, $_POST['fname'], $_POST['lname']);
    }

    // update personal email
    elseif ($_POST['column'] == "pEmail") {
        $database->changePersonalEmail($email, $_POST['pemail']);

        // send a code when changing email
        $code = randomString(6);
        $database->setStudentCode("verifiedPersonal", $code, $email);

        // create the message
        /*$message = (new Swift_Message())
            ->setSubject('Verification Code')
            ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
            ->setTo($_POST['pemail'])
            ->setBody("Personal Email Verification Code: ". $code, 'text/html');

        // send the message
        $result = $mailer->send($message);*/
    }

    // verify personal email
    elseif ($_POST['column'] == "pEmailVerify") {
        $verified = $database->getStudent($email)['verifiedPersonal'];
        // if it matches code in the database
        if ($_POST['code'] == $verified || $verified == "y") {
            $database->setStudentCode("verifiedPersonal", "y", $email);
            echo "correct";
        } else echo "incorrect";
    }

    // resend personal email code
    elseif ($_POST['column'] == "pEmailResend") {
        $pEmail = trim($_POST['pemail']);

        // send a code when changing email
        $code = randomString(6);
        $database->setStudentCode("verifiedPersonal", $code, $email);

        // create the message
        /*$message = (new Swift_Message())
            ->setSubject('Verification Code')
            ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
            ->setTo($pEmail)
            ->setBody("Personal Email Verification Code: ". $code, 'text/html');

        // send the message
        $result = $mailer->send($message);*/
    }

    // update phone number
    elseif ($_POST['column'] == "phone") {
        $phone = $_POST['phone'];
        $carrier = $database->getCarrierInfo(trim($_POST['carrier']));
        $carrierEmail = $carrier['carrierEmail'];
        $database->changePhoneNumber($email, $phone);

        // send a code when changing phone number
        $code = randomString(6);
        $database->setStudentCode("verifiedPhone", $code, $email);
        $to = $phone . "@" . $carrierEmail;

        // create the message
        $message = (new Swift_Message())
            ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
            ->setTo($to)
            ->setBody("Phone Verification Code: " . $code, 'text/html');

        // send the message
        $result = $mailer->send($message);
    }