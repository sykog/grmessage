<?php
    // connect to database
    require_once $_SERVER['DOCUMENT_ROOT']."/../config.php";
    require_once "../model/Database.php";
    require_once "../randomString.php";
    require_once "../vendor/autoload.php";

    $database = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);

    // create the transport
    $transport = (new Swift_SmtpTransport(EMAIL_ACCOUNT, 465, 'ssl'))
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
        // only update if email is different than current one
        if ($database->getStudent($email)['personalEmail'] != $_POST['pemail']) {
            $database->changePersonalEmail($email, $_POST['pemail']);

            // send a code when changing email
            $code = randomString(6);
            $database->setStudentCode("verifiedPersonal", $code, $email);

            $body = '<html lang="en">' .
                '<body>' .
                '<h3 style="color:#006225">Green River Messaging</h3>' .
                '<p>Email Verification Code:</p>' .
                '<h3>' . $code . '</h3>' .
                '</body>' .
                '</html>';

            // create the message
            $message = (new Swift_Message())
                ->setSubject('Verification Code')
                ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
                ->setTo($_POST['pemail'])
                ->setBody($body, 'text/html');

            // send the message
            $result = $mailer->send($message);
        } else {
            echo "unchanged";
        }
    }

    // verify personal email
    elseif ($_POST['column'] == "pEmailVerify") {
        $verified = $database->getStudent($email)['verifiedPersonal'];
        // if it matches code in the database
        if (($_POST['code'] == $verified || $verified == "y") && !empty($_POST['code'])) {
            $database->setStudentCode("verifiedPersonal", "y", $email);
            echo "correct";
        } else echo "incorrect";
    }

    // resend personal email code
    elseif ($_POST['column'] == "pEmailResend") {
        $pEmail = trim($_POST['pemail']);

        $code = randomString(6);
        $database->setStudentCode("verifiedPersonal", $code, $email);

        $body = '<html lang="en">' .
            '<body>' .
            '<h3 style="color:#006225">Green River Messaging</h3>' .
            '<p>Email Verification Code:</p>' .
            '<h3>' . $code . '</h3>' .
            '</body>' .
            '</html>';

        // create the message
        $message = (new Swift_Message())
            ->setSubject('Verification Code')
            ->setFrom([EMAIL_USERNAME => 'Green River Messaging'])
            ->setTo($_POST['pemail'])
            ->setBody($body, 'text/html');

        // send the message
        $result = $mailer->send($message);

        // send the message
        $result = $mailer->send($message);
    }

    // update phone number
    elseif ($_POST['column'] == "phone") {
        $phone = $_POST['phone'];
        $carrier = $database->getCarrierInfo(trim($_POST['carrier']));
        $carrierEmail = $carrier['carrierEmail'];

        // only update if phone number is different than current one
        if ($database->getStudent($email)['phone'] != $phone) {
            $database->changePhoneNumber($email, $phone);

            // send a random code when changing phone number
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
        } else {
            echo "unchanged";
        }
    }

    // update carrier
    elseif ($_POST['column'] == "carrier") {
        $phone = trim($_POST['phone']);
        $carrier = $database->getCarrierInfo(trim($_POST['carrier']));
        $carrierEmail = $carrier['carrierEmail'];

        // only update if phone carrier is different than current one
        if ($database->getStudent($email)['carrier'] != $carrier['carrier']) {
            $database->changeCarrier($email, $carrier['carrier']);

            // send a random code when changing phone number
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
        } else {
            echo "unchanged";
        }
    }

    // verify phone number
    elseif ($_POST['column'] == "phoneVerify") {
        $verified = $database->getStudent($email)['verifiedPhone'];

        // if it matches code in the database
        if (($_POST['code'] == $verified || $verified == "y") && !empty($_POST['code'])) {
            $database->setStudentCode("verifiedPhone", "y", $email);
            echo "correct";
        } else echo "incorrect" . $_POST['code'];
    }

    // resend phone email code
    elseif ($_POST['column'] == "phoneResend") {
        $phone = trim($_POST['phone']);
        $carrier = $database->getCarrierInfo(trim($_POST['carrier']));
        $carrierEmail = $carrier['carrierEmail'];

        // create a code and add to the database
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

    // update program
    elseif ($_POST['column'] == "program") {
        $database->changeProgram($email, $_POST['program']);
    }

    // update notification preferences
    elseif ($_POST['column'] == "preferences") {
        if ($_POST['studentEmail'] == "true") $getStudentEmails = 'y';
        else $getStudentEmails = 'n';

        if ($_POST['personalEmail'] == "true") $getPersonalEmails = 'y';
        else $getPersonalEmails = 'n';

        if ($_POST['text'] == "true") $getTexts = 'y';
        else $getTexts = 'n';

        $database->updatePreferences($email, $getStudentEmails, $getTexts, $getPersonalEmails);
    }