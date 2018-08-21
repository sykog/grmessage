<?php
// Connects to the database and uses functions to update and select from it

require_once $_SERVER['DOCUMENT_ROOT']."/../config.php";
class Database
{
    protected $dbh;
    /**
     * Database constructor.
     * Establishes connection with database
     * @return void
     */
    function __construct() {
        try {
            // instantiate a PDO object
            $this->dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    } // end constructor

    /**
     * Adds a student to the database
     * @param $email student's email
     * @param $password student's password
     * @param $phone student's phone number
     * @param $fname first name
     * @param $lname last name
     * @param $carrier cell phone carrier
     */
    function addStudent($email, $password, $phone, $fname, $lname, $carrier, $program) {
        // encrypt password
        $password = sha1($password);
        if (strlen($phone) == 0) {
            $carrier = "";
        }
        $dbh = $this->dbh;
        // define the query
        $sql = "INSERT INTO students(studentEmail, password, phone, fname, lname, carrier, program)
            VALUES (:email, :password, :phone, :fname, :lname, :carrier, :program)";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->bindParam(':password', $password, PDO::PARAM_STR);
        $statement->bindParam(':phone', $phone, PDO::PARAM_STR);
        $statement->bindParam(':fname', $fname, PDO::PARAM_STR);
        $statement->bindParam(':lname', $lname, PDO::PARAM_STR);
        $statement->bindParam(':carrier', $carrier, PDO::PARAM_STR);
        $statement->bindParam(':program', $program, PDO::PARAM_STR);

        // execute
        $statement->execute();
        $id = $dbh->lastInsertId();
    } // end addStudent

    /**
     * Adds an instructor to the database
     * @param $email instructor's email
     * @param $password instructor's password
     * @param $fname first name
     * @param $lname last name
     */
    function addInstructor($email, $password, $fname, $lname) {
        // encrypt password
        $password = sha1($password);
        $dbh = $this->dbh;

        // define the query
        $sql = "INSERT INTO instructors(email, password, fname, lname)
            VALUES (:email, :password, :fname, :lname)";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->bindParam(':password', $password, PDO::PARAM_STR);
        $statement->bindParam(':fname', $fname, PDO::PARAM_STR);
        $statement->bindParam(':lname', $lname, PDO::PARAM_STR);

        // execute
        $statement->execute();
        $id = $dbh->lastInsertId();
    } // end addInstructor

    /**
     * change password for students
     * @param $studentEmail identifier
     * @param $newPassword new password
     */
    function changeStudentPassword($studentEmail, $newPassword) {
        // encrypt password
        $newPassword = sha1($newPassword);
        $dbh = $this->dbh;

        // define the query
        $sql = "UPDATE students
            SET password = :password
            WHERE studentEmail = :studentEmail";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":studentEmail", $studentEmail, PDO::PARAM_STR);
        $statement->bindParam(":password", $newPassword, PDO::PARAM_STR);

        // execute
        $statement->execute();
    } // end changeStudentPassword

    /**
     * change password for instructors
     * @param $instructorEmail identifier
     * @param $newPassword new password
     */
    function changeInstructorPassword($instructorEmail, $newPassword) {
        // encrypt password
        $newPassword = sha1($newPassword);
        $dbh = $this->dbh;

        // define the query
        $sql = "UPDATE instructors
            SET password = :password
            WHERE email = :email";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":email", $instructorEmail, PDO::PARAM_STR);
        $statement->bindParam(":password", $newPassword, PDO::PARAM_STR);

        // execute
        $statement->execute();
    } // end changeStudentPassword

    /**
     * change student's first and last name
     * @param $studentEmail identifier
     * @param $first first name
     * @param $last last name
     */
    function changeStudentName($studentEmail, $first, $last) {
        $dbh = $this->dbh;

        // define the query
        $sql = "UPDATE students
            SET lname = :lname, fname = :fname
            WHERE studentEmail = :studentEmail";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":studentEmail", $studentEmail, PDO::PARAM_STR);
        $statement->bindParam(":fname", $first, PDO::PARAM_STR);
        $statement->bindParam(":lname", $last, PDO::PARAM_STR);

        // execute
        $statement->execute();
    } // end change name

    /**
     * change instructor's first and last name
     * @param $email identifier
     * @param $first first name
     * @param $last last name
     */
    function changeInstructorName($email, $first, $last) {
        $dbh = $this->dbh;

        // define the query
        $sql = "UPDATE instructors
            SET lname = :lname, fname = :fname
            WHERE email = :email";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":email", $email, PDO::PARAM_STR);
        $statement->bindParam(":fname", $first, PDO::PARAM_STR);
        $statement->bindParam(":lname", $last, PDO::PARAM_STR);

        // execute
        $statement->execute();
    } // end change name

    /**
     * change student's personal email
     * @param $studentEmail identifier
     * @param $personalEmail secondary email
     */
    function changePersonalEmail($studentEmail, $personalEmail) {
        $dbh = $this->dbh;

        // define the query
        $sql = "UPDATE students
            SET personalEmail = :personalEmail
            WHERE studentEmail = :studentEmail";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":studentEmail", $studentEmail, PDO::PARAM_STR);
        $statement->bindParam(":personalEmail", $personalEmail, PDO::PARAM_STR);

        // execute
        $statement->execute();
    } // end changePersonalEmail

    /**
     * change student's cell phone number
     * @param $studentEmail identifier
     * @param $phone cell phone number
     */
    function changePhoneNumber($studentEmail, $phone) {
        $dbh = $this->dbh;

        // define the query
        $sql = "UPDATE students
            SET phone = :phone
            WHERE studentEmail = :studentEmail";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":studentEmail", $studentEmail, PDO::PARAM_STR);
        $statement->bindParam(":phone", $phone, PDO::PARAM_STR);

        // execute
        $statement->execute();
    } // end changePhoneNumber

    /**
     * change student's cell phone carrier
     * @param $studentEmail identifier
     * @param $carrier cell phone carrier
     */
    function changeCarrier($studentEmail, $carrier) {
        $dbh = $this->dbh;

        // define the query
        $sql = "UPDATE students
            SET carrier = :carrier
            WHERE studentEmail = :studentEmail";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":studentEmail", $studentEmail, PDO::PARAM_STR);
        $statement->bindParam(":carrier", $carrier, PDO::PARAM_STR);

        // execute
        $statement->execute();
    } // end changeCarrier

    /**
     * retrieve the phone carrier from the database
     * @param $carrier cell phone carrier
     * @return carrier and each of the columns
     */
    function getCarrierInfo($carrier) {
        $dbh = $this->dbh;
        // Define the query
        $sql = "SELECT * FROM carriers WHERE carrier = :carrier";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":carrier", $carrier, PDO::PARAM_STR);

        // execute and process the result
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result;
    } // end getCarrierInfo

    /**
     * change program student is in
     * @param $studentEmail identifier
     * @param $program bachelors, associates
     */
    function changeProgram($studentEmail, $program) {
        $dbh = $this->dbh;

        // define the query
        $sql = "UPDATE students
            SET program = :program
            WHERE studentEmail = :studentEmail";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":studentEmail", $studentEmail, PDO::PARAM_STR);
        $statement->bindParam(":program", $program, PDO::PARAM_STR);

        // execute
        $statement->execute();
    } // end changeProgram

    /**
     * sets each of the preferences for recieving messages
     * @param $email identifier
     * @param $getStudentEmails y for yes, n for no
     * @param $getTexts y for yes, n for no
     * @param $getPersonalEmails y for yes, n for no
     */
    function updatePreferences ($email, $getStudentEmails, $getTexts, $getPersonalEmails) {
        $dbh = $this->dbh;

        // define the query
        $sql = "UPDATE students
                SET getTexts = :getTexts, getStudentEmails = :getStudentEmails, getPersonalEmails = :getPersonalEmails
                WHERE studentEmail = :email";

        // prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->bindParam(":email", $email, PDO::PARAM_STR);
        $statement->bindParam(":getTexts", $getTexts, PDO::PARAM_STR);
        $statement->bindParam(":getStudentEmails", $getStudentEmails, PDO::PARAM_STR);
        $statement->bindParam(":getPersonalEmails", $getPersonalEmails, PDO::PARAM_STR);

        // execute
        $statement->execute();
    } // end updatePreferences

    /**
     * Returns true if email is in database
     * @param $email identifier
     * @return bool true if email is found
     */
    function studentExists($email) {
        $dbh = $this->dbh;

        // define the query
        $sql = "SELECT * FROM students WHERE studentEmail= :email";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":email", $email, PDO::PARAM_STR);

        // execute and process the result
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row['studentEmail'] == $email;
    } // end studentExists

    /**
     * Returns true if email is in database
     * @param $email identifier
     * @return bool true if email is found
     */
    function instructorExists($email) {
        $dbh = $this->dbh;

        // define the query
        $sql = "SELECT * FROM instructors WHERE email= :email";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":email", $email, PDO::PARAM_STR);

        // execute and process the results
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row['email'] == $email;
    } // end instructorExists

    /**
     * retrieve student from the database
     * @param $email identifier
     * @return student and all of the columns
     */
    function getStudent($email) {
        $dbh = $this->dbh;

        // define the query
        $sql = "SELECT * FROM students WHERE studentEmail= :email";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":email", $email, PDO::PARAM_STR);

        // execute and process the result
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result;
    } // end getStudent

    /**
     * retrieve instructor from the database
     * @param $email identifier
     * @return instructor and all of the columns
     */
    function getInstructor($email) {
        $dbh = $this->dbh;

        // define the query
        $sql = "SELECT * FROM instructors WHERE email= :email";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":email", $email, PDO::PARAM_STR);

        // execute and process the result
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result;
    } // end getInstructor

    /**
     * retrieve every student from the database
     * @return each students in the array
     */
    function getStudents() {
        $dbh = $this->dbh;

        // define the query
        $sql = "SELECT * FROM students";

        // Prepare the statement
        $statement = $dbh->prepare($sql);

        // execute and process the result
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } // end getStudent

    /**
     * get number of students who are subscribed by program
     * @param $program program to search for
     * @return row count of students who have a y for a column
     */
    function studentCount($program) {
        $dbh = $this->dbh;

        // define the query
        $sql = "SELECT * FROM students WHERE program = :program AND
                (getTexts = 'y' OR getStudentEmails = 'y' OR getPersonalEmails = 'y')";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":program", $program, PDO::PARAM_STR);

        // execute and process the results
        $statement->execute();
        return $statement->rowCount();
    } // end studentCount

    /**
     * select the student if code is less than an hour old
     * @param $email identifier
     * @return true if at least 1 row matches the query
     */
    function compareTimeStudent($email) {
        $dbh = $this->dbh;

        // define the query
        $sql = "SELECT * FROM students WHERE verifiedStudentTime > DATE_SUB(NOW(), INTERVAL 1 HOUR) AND
                studentEmail = :email";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":email", $email, PDO::PARAM_STR);

        // execute and process the results
        $statement->execute();
        $count = $statement->rowCount();
        return $count > 0;
    } // end compareTimeStudent

    /**
     * select the instructor if code is less than an hour old
     * @param $email identifier
     * @return true if at least 1 row matches the query
     */
    function compareTimeInstructor($email) {
        $dbh = $this->dbh;

        // define the query
        $sql = "SELECT * FROM instructors WHERE verifiedTime > DATE_SUB(NOW(), INTERVAL 1 HOUR) AND
                email = :email";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":email", $email, PDO::PARAM_STR);

        // execute and process the results
        $statement->execute();
        $count = $statement->rowCount();
        return $count > 0;
    } // end compareTimeInstructor

    /**
     * Adds a verification code for a student to the database
     * @param $column texts, student email, or personal email
     * @param $code 'y' when verified
     * @param $email identifier
     */
    function setStudentCode($column, $code, $email) {
        $timestamp = $column . "Time";
        $dbh = $this->dbh;

        // define the query
        $sql = "UPDATE students
                SET $column = :verifiedStudent, $timestamp = CURRENT_TIMESTAMP
                WHERE studentEmail = :email";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":email", $email, PDO::PARAM_STR);
        $statement->bindParam(":verifiedStudent", $code, PDO::PARAM_STR);

        // execute
        $statement->execute();
    } // end setStudentCode

    /**
     * Adds a verification code for an instructor to the database
     * @param $code 'y' when verified
     * @param $email identifier
     */
    function setInstructorCode($code, $email) {
        $dbh = $this->dbh;

        // define the query
        $sql = "UPDATE instructors
                SET verified = :verified, verifiedTime = CURRENT_TIMESTAMP
                WHERE email = :email";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":email", $email, PDO::PARAM_STR);
        $statement->bindParam(":verified", $code, PDO::PARAM_STR);

        // execute
        $statement->execute();
    } // end setInstructorCode

    /**
     * Stores a sent message to the database
     * @param $instuctorEmail who sent the message
     * @param $content body of the message
     */
    function storeMessage($instuctorEmail, $content, $recipient) {
        $dbh = $this->dbh;

        // define the query
        $sql = "INSERT INTO messages (instructorEmail, content, datetime, recipient)
                VALUES (:instructorEmail, :content, NOW(), :recipient);";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":instructorEmail", $instuctorEmail, PDO::PARAM_STR);
        $statement->bindParam(":content", $content, PDO::PARAM_STR);
        $statement->bindParam(":recipient", $recipient, PDO::PARAM_STR);

        // execute
        $statement->execute();
    } // end storeMessage

    /**
     * retrieve each message from the database
     * @return each of the messages in an array
     */
    function getMessages() {
        $dbh = $this->dbh;
        // define the query
        $sql = "SELECT * FROM messages
                ORDER BY datetime DESC
                LIMIT 50 ";

        // prepare the statement
        $statement = $dbh->prepare($sql);

        // execute and process the results
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    } // end getMessages

}//end Database class