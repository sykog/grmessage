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

    function addStudent($email, $password, $phone, $fname, $lname, $carrier)
    {
        $dbh = $this->dbh;
// define the query
        $sql = "INSERT INTO students(studentEmail, password, phone, fname, lname)
VALUES (:email, sha1(:password), :phone, :fname, :lname, :carrier)";

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
    }//end addStudent

    function addInstructor($email, $password,$fname, $lname)
    {
        $dbh = $this->dbh;
// define the query
        $sql = "INSERT INTO instructors(email, password, fname, lname)
        VALUES (:email, sha1(:password), :fname, :lname)";

// prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->bindParam(':password', $password, PDO::PARAM_STR);
        $statement->bindParam(':fname', $fname, PDO::PARAM_STR);
        $statement->bindParam(':lname', $lname, PDO::PARAM_STR);

// execute
        $statement->execute();
    }//end addInstructor

    function changeStudentPassword($id, $newPassword)
    {
        $dbh = $this->dbh;
// define the query
        $sql = "UPDATE students
                SET (password = sha1(:newPassword))
                WHERE studentid = :id";

    }//end changeStudentPassword

   /* function login($email, $password){
        $dbh = $this->dbh;
        $sql = "SELECT password FROM student WHERE studentEmail = :email";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->execute();
        $comp = $statement->fetch();
        return $comp == $password;
    } */

    //retrieve student from database
    function login($email) {
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