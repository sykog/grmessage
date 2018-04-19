<?php
    ini_set('display-errors', 1);
    error_reporting(E_ALL);
    require_once "../config.php";

    try {
    //Instantiate a PDO object
    $dbh = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);

    echo "Connected to database!";
    }
    catch (PDOException $e) {
    echo $e->getMessage();
    }
