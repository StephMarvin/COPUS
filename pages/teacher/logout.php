<?php
    session_start();
    unset($_SESSION["teacher-id"]);

    header("Location: login.php");
    exit();
?>