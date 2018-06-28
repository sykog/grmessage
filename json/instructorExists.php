<?php
    require_once $_SERVER['DOCUMENT_ROOT']."/../config.php";

    try {
        // instantiate a PDO object
        $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }

    // Define the query
    $sql = "SELECT * FROM instructors WHERE email = :email";

    // Prepare the statement
    $statement = $dbh->prepare($sql);

    // Bind the parameters
    $statement->bindParam(':email', $_POST['email'], PDO::PARAM_STR);

    //execute the query
    $statement->execute();

    // if a row already exists
    if ($statement->rowCount() > 0) {
        echo "Email already exists";
    } else {
        echo "";
    }