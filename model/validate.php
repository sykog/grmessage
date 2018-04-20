<?php
/**
 * Created by PhpStorm.
 * User: Pavel Radchuk
 * Date: 4/20/18
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

    function validEmail($str)
    {
        $regexp = '^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$^';
        return preg_match($regexp, $str);
    }
    function validPassword($str)
    {
        $regexp = '^[a-zA-Z]\w{3,14}$^';
        return preg_match($regexp, $str);
    }

    function validName($name)
    {
        return ctype_alpha($name);
    }

    function validPhone($phone)
    {
        $phone = str_replace('-', '', $phone);
        $phone = str_replace(' ', '', $phone);
        $phone = str_replace('(', '', $phone);
        $phone = str_replace(')', '', $phone);
        return (is_numeric($phone) && (strlen($phone) > 9 && (strlen($phone) < 16)));
    }