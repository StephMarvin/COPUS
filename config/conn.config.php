<?php
    session_start();
    date_default_timezone_set("Asia/Manila");
    require_once "variables.config.php";

    try {
        $data_source = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $conn = new PDO($data_source, DB_USERNAME, DB_PASSWORD);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        
        $conn->exec("SET time_zone = '+08:00'");
    }

    catch(PDOException $e) {
        error_log("Error Occured: " . $e->getMessage());
        die("Error Connecting to Database: " . $e->getMessage());
    }
?>