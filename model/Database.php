<?php
require_once $_SERVER['DOCUMENT_ROOT']."/../config.php";
class Database
{
    protected $dbh;
    /**
     * Database constructor.
     * Establishes connection with database
     * @return void
     */
    function __construct()
    {
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
    function addStudent($email, $password, $phone, $fname, $lname, $carrier, $program)
    {
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
    function addInstructor($email, $password, $fname, $lname)
    {
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

    function changeStudentPassword($studentEmail, $newPassword)
    {

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
     * change student's first and last name
     * @param $studentEmail identifier
     * @param $first first name
     * @param $last last name
     */
    function changeStudentName($studentEmail, $first, $last)
    {
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
     * change student's personal email
     * @param $studentEmail identifier
     * @param $personalEmail secondary email
     */
    function changePersonalEmail($studentEmail, $personalEmail)
    {

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
    function changePhoneNumber($studentEmail, $phone)
    {

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
    function changeCarrier($studentEmail, $carrier)
    {

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
     * change program student is in
     * @param $studentEmail identifier
     * @param $program bachelors, associates
     */
    function changeProgram($studentEmail, $program)
    {

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
     * Returns 1 if email is in database, 0 if not
     * @param email email being searched
     * @return bool 1 if email is found
     */
    function studentExists($email)
    {
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
     * Returns 1 if email is in database, 0 if not
     * @param email email being searched
     * @return bool 1 if email is found
     */
    function InstructorExists($email)
    {
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

    //retrieve student from database
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

    //retrieve student from database
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

    //retrieve all students from the database
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

    //retrieve carrier info from database
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

    //update changed student notification preferences
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

    function setStudentCode($column, $code, $email){
        $dbh = $this->dbh;
        // Define the query
        $sql = "UPDATE students
                SET verifiedStudent = :verifiedStudent
                WHERE studentEmail = :email";

        // Prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->bindParam(":email", $email, PDO::PARAM_STR);
        $statement->bindParam(":verifiedStudent", $code, PDO::PARAM_STR);

        $statement->execute();
    }

    function setInstructorCode($column, $code, $email){
        $dbh = $this->dbh;
        // Define the query
        $sql = "UPDATE instructors
                SET verified = :verified
                WHERE email = :email";

        // Prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->bindParam(":email", $email, PDO::PARAM_STR);
        $statement->bindParam(":verified", $code, PDO::PARAM_STR);

        $statement->execute();
    }

    function storeMessage($instuctorEmail, $content) {
        $dbh = $this->dbh;
        // Define the query
        $sql = "INSERT INTO messages (instructorEmail, content, datetime)
                VALUES (:instructorEmail, :content, NOW());";

        //prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->bindParam(":instructorEmail", $instuctorEmail, PDO::PARAM_STR);
        $statement->bindParam(":content", $content, PDO::PARAM_STR);

        $statement->execute();
    }

    function getMessages() {
        $dbh = $this->dbh;
        // Define the query
        $sql = "SELECT * FROM messages
                ORDER BY datetime DESC ";

        //prepare the statement
        $statement = $dbh->prepare($sql);

        $statement->execute();

        // Process the result
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }



}//end Database class