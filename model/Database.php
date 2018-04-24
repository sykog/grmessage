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
            echo 'Connected to database!';
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }//end constructor
}