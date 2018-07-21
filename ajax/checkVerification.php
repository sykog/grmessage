<?php
    // connect to database
    require_once $_SERVER['DOCUMENT_ROOT']."/../config.php";
    require_once "../model/Database.php";

    $database = new Database(DB_DSN,DB_USERNAME, DB_PASSWORD);