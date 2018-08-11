<?php
/**
 * This ia a downloadable file for web master implementation
 * Created by: James McPherson | Derrick Selle | Edward Mendoza | Hanchen Liu
 */

/**
 * Returned by register() when a new user
 * enters an unregistered e-mail address with
 * a Green River College-based domain name and
 * a password that follows our password rules
 */
define("REGISTER_SUCCESS", 0);
/**
 * Returned by register() when a new user tries
 * to register for an account with a non-Green
 * River College based e-mail address
 */
define("NOT_A_GRC_EMAIL", 1);
/**
 * Returned by register() when a new user tries
 * to register for an account with a password
 * that does not abide by our password rules
 */
define("PASSWORD_RULE_VIOLATION", 2);
/**
 * Returned by register() when a new user tries
 * to register for an account with an e-mail
 * address that has already been used for
 * registering
 */
define("EMAIL_ALREADY_EXISTS", 3);
/**
 * Returned by any of our functions if an
 * error we did not account for occurs
 */
define("UNKNOWN_ERROR", 4);
/**
 * Returned by login() when a user enters
 * a password that does not match with
 * the e-mail address they entered
 */
define("WRONG_PASSWORD", 5);
/**
 * Returned by login() and sendResetLink()
 * when a user has entered an e-mail address
 * that is not in our database
 */
define("USERNAME_DNE", 6);
/**
 * Returned by login() when a user has
 * entered their username (e-mail address)
 * and password correctly
 */
define("SUCCESSFUL_LOGIN", 7);
/**
 * Returned by login() when a user has
 * exceeded the 5-log in limit on their
 * account
 */
define("TOO_MANY_LOGINS", 8);
/**
 * Returned by verifyAccount() when a
 * user's account has been successfully
 * verified
 */
define("VERIFY_SUCCESS", 10);
/**
 * Returned by sendResetLink() when an
 * e-mail with a link to our reset password
 * page has been sent to the user-provided
 * e-mail address
 */
define("RESET_LINK_SENT", 11);
/**
 * Returned by resetPassword() when a
 * user has not correctly entered their
 * new password twice in a row
 */
define("PASSWORDS_DO_NOT_MATCH", 12);
/**
 * Returned by resetPassword() when a user
 * wishes to change their password to an
 * older password they've previously used
 */
define("RESETTING_TO_OLD_PASSWORD", 13);
/**
 * Returned by resetPassword() when a user's
 * password has been successfully changed
 */
define("PASSWORD_RESET_SUCCEED", 14);
/**
 * Returned by login() when a user tries to
 * log in with an unverified account
 */
define("EMAIL_STATE_IS_INITIAL", 15);
/**
 * Returned by login() when a user tries to
 * log into an account that has been locked
 * due to the log in limit being exceeded
 */
define("EMAIL_STATE_IS_LOCKED_OUT", 16);
/**
 * Returned by verifyAccount() when a new
 * user tries to verify their account with
 * a verification link that is more than
 * a day old
 */
define("EXPIRED_VERIFICATION_CODE", 17);
/**
 * Returned by forgetMe() to show the cookie does not exist
 */
define("COOKIE_DNE", 18);
/**
 * Returned by setPermissions() to show a user already has a permission
 */
define("ALREADY_HAS_PERMISSION", 19);
/**
 * Returned by setPermissions() to show set permission successful
 */
define("PERMISSION_SET_SUCCESS", 20);

/**
 * returned by loginWithCookie if the cookie is older than the current password set by the password
 */
define("USER_RECENTLY_CHANGED_PASSWORD", 21);

/**
 * Stores the url to the server side receiver file as a constant
 */
define("RECEIVER_URL", 'http://www.gatorlock.greenriverdev.com/model/receiver.php');

/**
 * Stores the cookie name for uncookie
 */
define("GL_UNCOOKIE_NAME", "GRC_Gatorlock_usercookie");

//Cookie will not expire for 10 years after creatuin
define("COOKIE_EXPIRATION", 86400*3650);

//NOTE: ALL FUNCTIONS MUST SEND AN ARRAY WITH THE FIRST INDEX KEY NAMED AS 'functionName'

/**
 * Allows a user to login
 *
 * @param $username string user's email
 * @param $password string associated password of user's email
 * @return int result code of login status
 */
function gatorlockLogin($username, $password) {
    $fields = array("functionName" => urlencode("LOGIN"),
                    "userName" => urlencode($username),
                    "password" => urlencode($password));

    $fields_string = "";

    //url-ify the data for the POST
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');

    //execute post
    $result = curlCall($fields, $fields_string);

    return intval($result);
}

/**
 * Creates a cookie so the site remembers a user
 *
 * @param $email string user name
 */
function rememberMe($email)
{
    $cookie_date = time();
    $cookie_array = array($email ,$cookie_date);

    $result = setcookie(GL_UNCOOKIE_NAME, serialize($cookie_array), time() + COOKIE_EXPIRATION, "/");
    echo $result;
}

/**
 * Deletes the cookie.
 *
 * @return int COOKIE_DNE to show the cookie does not exist
 */
function forgetMe(){
    if(isset($_COOKIE[GL_UNCOOKIE_NAME])){
        unset($_COOKIE[GL_UNCOOKIE_NAME]);
        setcookie(GL_UNCOOKIE_NAME, "", time()-3600, "/");
    }
    return COOKIE_DNE;
}

/**
 * This allows user to log in with a cookie
 * @return mixed result code for login with cookie
 */
function glLoginWithCookie(){
    $data = getCookieData();

    $username = $data[0];
    $dateOfCreation = $data[1];

    $fields = array("functionName" => urlencode("LOGINWITHCOOKIE"),
        "userName" => urlencode($username),
        "cookieDate" => urlencode($dateOfCreation));

    $fields_string = "";

    //url-ify the data for the POST
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');

    //execute post
    $result = curlCall($fields, $fields_string);

    if($result != SUCCESSFUL_LOGIN){
        forgetMe();
    }

    return $result;

}

/**
 * Allows user to register for a new account
 *
 * @param $username string GRC email of the user
 * @param $password string a password that will be associated with the user's email
 * @param $firstName string user's first name
 * @param $lastName string user's last name
 *
 * @return int register result code
 */
function gatorlockRegister($username, $password,
                           $firstName, $lastName) {
    $fields = array("functionName" => urlencode("REGISTER"),
        "userName" => urlencode($username),
        "password" => urlencode($password),
        "firstName" => urlencode($firstName),
        "lastName" => urlencode($lastName));

    $fields_string = "";

    //url-ify the data for the POST
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');

    //execute post
    $result = curlCall($fields, $fields_string);

    return intval($result);
}

/**
 * This gets the cookie data if user decided to use cookie log in
 *
 * @return mixed the cookie data
 */
function getCookieData(){
    $data = unserialize( $_COOKIE[GL_UNCOOKIE_NAME]);
    return $data;
}

/**
 * This allows web masters to get the permission of a user
 *
 * @param $userName string user's email
 * @param $siteURL string the site that the web master is from
 * @return int user's permission
 */
function getPermissions($userName, $siteURL) {
    $fields = array("functionName" => urlencode("GETPERMISSIONS"),
        "userName" => urlencode($userName),
        "siteURL" => urlencode($siteURL));

    $fields_string = "";

    //url-ify the data for the POST
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');

    //execute post
    $result = curlCall($fields, $fields_string);

    return $result;
}

/**
 * This allows web masters to set the permission of a user
 * @param $userName string user's email
 * @param $siteURL string the site that the web master is from and want to set user permission to
 * @param $permission int permission level
 * @return int result code for set user permission
 */
function setPermissions($userName, $siteURL, $permission) {
    $fields = array("functionName" => urlencode("SETPERMISSIONS"),
        "userName" => urlencode($userName),
        "siteURL" => urlencode($siteURL),
        "permission" => urlencode($permission));

    $fields_string = "";

    //url-ify the data for the POST
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');

    //execute post
    $result = curlCall($fields, $fields_string);

    return intval($result);
}

/**
 * This allows this file to connect to GL server
 *
 * @param $fields array an array of fields that is going to be sent to the GL server
 * @param $fields_string string used to store individual field data
 * @return mixed curl status result
 */
function curlCall($fields, $fields_string) {
    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, RECEIVER_URL);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

    //execute post
    $result = curl_exec($ch);

    //close connection
    curl_close($ch);
    return $result;
}