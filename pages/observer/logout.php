<?php
    session_start();
    unset($_SESSION["observer-id"]);

    header("Location: login.php");
    exit();
?>