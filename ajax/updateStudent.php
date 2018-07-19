<?php
    // connect to database
    require_once $_SERVER['DOCUMENT_ROOT']."/../config.php";

    try {
        // instantiate a PDO object
        $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }

    $email = trim($_POST['email']);

    // update based on column name
    if ($_POST['column'] == "name") {
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];

        // Define the query
        $sql = "UPDATE students
                SET fname = :fname, lname = :lname
                WHERE studentEmail = :studentEmail";

        // Prepare the statement
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":studentEmail", $email, PDO::PARAM_STR);
        $statement->bindParam(":fname", $fname, PDO::PARAM_STR);
        $statement->bindParam(":lname", $lname, PDO::PARAM_STR);

        // Execute the statement
        $statement->execute();
    }