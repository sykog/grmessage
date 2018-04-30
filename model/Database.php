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
    function addStudent($email, $password, $phone, $fname, $lname, $carrier)
    {
        // encrypt password
        $password = sha1($password);
        $dbh = $this->dbh;
        // define the query
        $sql = "INSERT INTO students(studentEmail, password, phone, fname, lname, carrier)
            VALUES (:email, :password, :phone, :fname, :lname, :carrier)";

        // prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->bindParam(':password', $password, PDO::PARAM_STR);
        $statement->bindParam(':phone', $phone, PDO::PARAM_STR);
        $statement->bindParam(':fname', $fname, PDO::PARAM_STR);
        $statement->bindParam(':lname', $lname, PDO::PARAM_STR);
        $statement->bindParam(':carrier', $carrier, PDO::PARAM_STR);

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

    function changeStudentPassword($id, $newPassword)
    {
        $dbh = $this->dbh;
        // define the query
        $sql = "UPDATE students
            SET (password = :newPassword)
            WHERE studentid = :id";
    }//end changeStudentPassword

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
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

}//end Database class