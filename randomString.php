<?php
/**
 * Generates random passwords and validation codes.
 * User: Pavel Radchuk
 * Date: 6/8/18
 * Time: 2:13 PM
 */
function randomString($length){

    $str = "";
    $characters = array_merge(range('A','Z'), range('a','z'), range('0', '9'));
    $max = count($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $max);
        $str .= $characters[$rand];
    }
    return $str;
}