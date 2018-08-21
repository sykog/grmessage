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
    }//end constructor

    /**
     * Adds a student to the database
     * @param $email students email
     * @param $password students password
     * @param $phone students phone number
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
    }//end addStudent

    /**
     * Adds an instructor to the database
     * @param $email
     * @param $password
     * @param $fname
     * @param $lname
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
    }//end addInstructor

    /**
     * change password for students
     * @param $studentEmail
     * @param $newPassword
     */
    function changeStudentPassword($studentEmail, $newPassword) {

        // encrypt password
        $newPassword = sha1($newPassword);

        $dbh = $this->dbh;
        // define the query
        $sql = "UPDATE students
            SET password = :password
            WHERE studentEmail = :studentEmail";

        // Prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":studentEmail", $studentEmail, PDO::PARAM_STR);
        $statement->bindParam(":password", $newPassword, PDO::PARAM_STR);

        // Execute the statement
        $statement->execute();
    }//end changeStudentPassword

    /**
     * change password for instructors
     * @param $instructorEmail
     * @param $newPassword
     */
    function changeInstructorPassword($instructorEmail, $newPassword) {

        // encrypt password
        $newPassword = sha1($newPassword);

        $dbh = $this->dbh;
        // define the query
        $sql = "UPDATE instructors
            SET password = :password
            WHERE email = :email";

        // Prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":email", $instructorEmail, PDO::PARAM_STR);
        $statement->bindParam(":password", $newPassword, PDO::PARAM_STR);

        // Execute the statement
        $statement->execute();
    }//end changeStudentPassword

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

        // Prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":studentEmail", $studentEmail, PDO::PARAM_STR);
        $statement->bindParam(":fname", $first, PDO::PARAM_STR);
        $statement->bindParam(":lname", $last, PDO::PARAM_STR);

        // Execute the statement
        $statement->execute();
    }//end change name

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

        // Prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":email", $email, PDO::PARAM_STR);
        $statement->bindParam(":fname", $first, PDO::PARAM_STR);
        $statement->bindParam(":lname", $last, PDO::PARAM_STR);

        // Execute the statement
        $statement->execute();
    }//end change name

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

        // Prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":studentEmail", $studentEmail, PDO::PARAM_STR);
        $statement->bindParam(":personalEmail", $personalEmail, PDO::PARAM_STR);

        // Execute the statement
        $statement->execute();
    }//end changePersonalEmail

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

        // Prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":studentEmail", $studentEmail, PDO::PARAM_STR);
        $statement->bindParam(":phone", $phone, PDO::PARAM_STR);

        // Execute the statement
        $statement->execute();
    }//end changePhoneNumber

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

        // Prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":studentEmail", $studentEmail, PDO::PARAM_STR);
        $statement->bindParam(":carrier", $carrier, PDO::PARAM_STR);

        // Execute the statement
        $statement->execute();
    }//end changeCarrier

    /**
     * retrieve the phone carrier from the database
     * @param $carrier
     * @return carrier and each of the columns
     */
    function getCarrierInfo($carrier) {
        $dbh = $this->dbh;
        // Define the query
        $sql = "SELECT * FROM carriers WHERE carrier = :carrier";

        // Prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->bindParam(":carrier", $carrier, PDO::PARAM_STR);

        // Execute the statement
        $statement->execute();

        // Process the result
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

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

        // Prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":studentEmail", $studentEmail, PDO::PARAM_STR);
        $statement->bindParam(":program", $program, PDO::PARAM_STR);

        // Execute the statement
        $statement->execute();
    }//end changeCarrier

    /**
     * sets each of the preferences for recieving messages
     * @param $email
     * @param $getStudentEmails y for yes, n for no
     * @param $getTexts y for yes, n for no
     * @param $getPersonalEmails y for yes, n for no
     */
    function updatePreferences ($email, $getStudentEmails, $getTexts, $getPersonalEmails) {
        $dbh = $this->dbh;
        // Define the query
        $sql = "UPDATE students
                SET getTexts = :getTexts, getStudentEmails = :getStudentEmails, getPersonalEmails = :getPersonalEmails
                WHERE studentEmail = :email";

        // Prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->bindParam(":email", $email, PDO::PARAM_STR);
        $statement->bindParam(":getTexts", $getTexts, PDO::PARAM_STR);
        $statement->bindParam(":getStudentEmails", $getStudentEmails, PDO::PARAM_STR);
        $statement->bindParam(":getPersonalEmails", $getPersonalEmails, PDO::PARAM_STR);

        // Execute the statement
        $statement->execute();
    }

    /**
     * Returns true if email is in database
     * @param $email email being searched
     * @return bool true if email is found
     */
    function studentExists($email) {
        $dbh = $this->dbh;

        // Define the query
        $sql = "SELECT * FROM students WHERE studentEmail= :email";

        // Prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":email", $email, PDO::PARAM_STR);

        // Execute the statement
        $statement->execute();
        // Process the result
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row['studentEmail'] == $email;
    }//end memberExists()

    /**
     * Returns true if email is in database
     * @param $email email being searched
     * @return bool true if email is found
     */
    function instructorExists($email) {
        $dbh = $this->dbh;
        // Define the query
        $sql = "SELECT * FROM instructors WHERE email= :email";

        // Prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":email", $email, PDO::PARAM_STR);

        // Execute the statement
        $statement->execute();
        // Process the result
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row['email'] == $email;
    }//end memberExists()

    /**
     * retrieve student from the database
     * @param $email
     * @return student and all of the columns
     */
    function getStudent($email) {
        $dbh = $this->dbh;
        // Define the query
        $sql = "SELECT * FROM students WHERE studentEmail= :email";

        // Prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->bindParam(":email", $email, PDO::PARAM_STR);

        // Execute the statement
        $statement->execute();

        // Process the result
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * retrieve instructor from the database
     * @param $email
     * @return instructor and all of the columns
     */
    function getInstructor($email) {
        $dbh = $this->dbh;
        // Define the query
        $sql = "SELECT * FROM instructors WHERE email= :email";

        // Prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->bindParam(":email", $email, PDO::PARAM_STR);

        // Execute the statement
        $statement->execute();

        // Process the result
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * retrieve every student from the database
     * @return each students in the array
     */
    function getStudents() {
        $dbh = $this->dbh;
        // Define the query
        $sql = "SELECT * FROM students";

        // Prepare the statement
        $statement = $dbh->prepare($sql);

        // Execute the statement
        $statement->execute();

        // Process the result
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    /**
     * get number of students who are subscribed by program
     * @param $program program to search for
     * @return row count of students who have a y for a column
     */
    function studentCount($program) {
        $dbh = $this->dbh;
        // Define the query
        $sql = "SELECT * FROM students WHERE program = :program AND
                (getTexts = 'y' OR getStudentEmails = 'y' OR getPersonalEmails = 'y')";

        // Prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->bindParam(":program", $program, PDO::PARAM_STR);

        $statement->execute();

        // Process the result
        return $statement->rowCount();
    }

    /**
     * select the student if code is less than an hour old
     * @param $email identifier
     * @return true if at least 1 row matches the query
     */
    function compareTimeStudent($email) {
        $dbh = $this->dbh;
        // Define the query
        $sql = "SELECT * FROM students WHERE verifiedStudentTime > DATE_SUB(NOW(), INTERVAL 1 HOUR) AND
                studentEmail = :email";

        // Prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->bindParam(":email", $email, PDO::PARAM_STR);

        $statement->execute();

        // Process the result
        $count = $statement->rowCount();
        return $count > 0;
    }

    /**
     * select the instructor if code is less than an hour old
     * @param $email identifier
     * @return true if at least 1 row matches the query
     */
    function compareTimeInstructor($email) {
        $dbh = $this->dbh;
        // Define the query
        $sql = "SELECT * FROM instructors WHERE verifiedTime > DATE_SUB(NOW(), INTERVAL 1 HOUR) AND
                email = :email";

        // Prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->bindParam(":email", $email, PDO::PARAM_STR);

        $statement->execute();

        // Process the result
        $count = $statement->rowCount();
        return $count > 0;
    }

    /**
     * Adds a verification code for a student to the database
     * @param $column texts, student email, or personal email
     * @param $code 'y' when verified
     * @param $email identifier
     */
    function setStudentCode($column, $code, $email) {
        $timestamp = $column . "Time";
        $dbh = $this->dbh;
        // Define the query
        $sql = "UPDATE students
                SET $column = :verifiedStudent, $timestamp = CURRENT_TIMESTAMP
                WHERE studentEmail = :email";

        // Prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->bindParam(":email", $email, PDO::PARAM_STR);
        $statement->bindParam(":verifiedStudent", $code, PDO::PARAM_STR);

        $statement->execute();
    }

    /**
     * Adds a verification code for an instructor to the database
     * @param $code 'y' when verified
     * @param $email identifier
     */
    function setInstructorCode($code, $email) {
        $dbh = $this->dbh;
        // Define the query
        $sql = "UPDATE instructors
                SET verified = :verified, verifiedTime = CURRENT_TIMESTAMP
                WHERE email = :email";

        // Prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->bindParam(":email", $email, PDO::PARAM_STR);
        $statement->bindParam(":verified", $code, PDO::PARAM_STR);

        $statement->execute();
    }

    /**
     * Stores a sent message to the database
     * @param $instuctorEmail
     * @param $content
     */
    function storeMessage($instuctorEmail, $content, $recipient) {
        $dbh = $this->dbh;
        // Define the query
        $sql = "INSERT INTO messages (instructorEmail, content, datetime, recipient)
                VALUES (:instructorEmail, :content, NOW(), :recipient);";

        //prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->bindParam(":instructorEmail", $instuctorEmail, PDO::PARAM_STR);
        $statement->bindParam(":content", $content, PDO::PARAM_STR);
        $statement->bindParam(":recipient", $recipient, PDO::PARAM_STR);

        $statement->execute();
    }

    /**
     * retrieve each message from the database
     * @return each of the messages in an array
     */
    function getMessages() {
        $dbh = $this->dbh;
        // Define the query
        $sql = "SELECT * FROM messages
                ORDER BY datetime DESC
                LIMIT 50 ";

        //prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->execute();

        // Process the result
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

}//end Database class