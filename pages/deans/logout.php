<?php
    session_start();
    unset($_SESSION["dean-id"]);

    header("Location: login.php");
    exit();
?>