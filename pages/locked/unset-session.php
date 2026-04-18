<?php
    session_start();

    unset($_SESSION["locked-account"]);

    http_response_code(200);
?>