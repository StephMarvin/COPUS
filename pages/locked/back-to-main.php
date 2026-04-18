<?php
    session_start();

    unset($_SESSION["locked-account"]);

    header("Location: ../../index.php");
?>