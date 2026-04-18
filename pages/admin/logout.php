<?php
    session_start();
    unset($_SESSION["admin-id"]);

    header("Location: login.php");
    exit();
?>