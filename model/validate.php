<?php
/**
 * Created by PhpStorm.
 * User: Pavel Radchuk
 * Date: 4/20/18
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

    // must end in @mail.greenriver.edu
    function validSEmail($email)
    {
        $regexp = '^[\w]+@mail.greenriver.edu$^';
        return preg_match($regexp, $email);
    }

    // must end in @greenriver.edu
    function validIEmail($email)
    {
        $regexp = '^[\w]+@greenriver.edu$^';
        return preg_match($regexp, $email);
    }

    // must be at least 6 character
    function validPassword($password)
    {
        return strlen($password) > 5;
    }

    // limit textBox characters
    function validTextMessage($length)
    {
        return strlen($length) > 0 && strlen($length) <= 250;
    }

    // confirmation and password must be the same
    function validConfirm($password, $confirm) {
        return $password == $confirm;
    }

    // 10 characters, numbers only
    function validPhone($phone)
    {
        $phone = str_replace('-', '', $phone);
        $phone = str_replace(' ', '', $phone);
        $phone = str_replace('(', '', $phone);
        $phone = str_replace(')', '', $phone);
        return is_numeric($phone) && strlen($phone) == 10;
    }

    // must be one of the valid carriers
    function validCarrier($carrier) {
        global $f3;
        return in_array($carrier, $f3->get('carriers'));
    }