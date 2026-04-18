<?php
    session_start();

    if (empty($_SESSION["locked-account"]) || !isset($_SESSION["locked-account"])) {
        header("Location: ../../index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title> Ops! Your account is locked! </title>

    <link rel="shortcut icon" type="image/x-icon" href="../../public/assets/website-logo-cite.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>

    <style>
        body {
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .locked-box {
            max-width: 700px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            padding: 25px 30px;
            background-color: white;
        }

        .btn-home {
            background-color: #ff4500;
            color: white;
            border: none;
            padding: 8px 20px;
            font-size: 14px;
            border-radius: 4px;
        }

        .btn-home:hover {
            background-color: #e03e00;
        }

        .description {
            font-size: 14px;
            color: #555;
        }

        hr {
            margin: 20px 0;
        }
    </style>

    

</head>

<body>

    <div class="locked-box">
        <div class="d-flex align-items-start gap-3">
            <img src="../../public/assets/locked-logo.png" alt="Lock Icon" width="60" height="60">

            <div>
                <h6 class="fw-bold mb-1">Your Account is Temporarily Locked</h6>
                <p class="description mb-0">
                    Your account has been locked for security reasons or due to multiple failed login attempts.<br>
                    Please contact the administrator or support team to unlock your account.
                </p>
            </div>
        </div>

        <hr />

        <div class="d-flex justify-content-end">
            <a href="../../index.php" class="btn btn-home"> Back to Home </a>
        </div>
    </div>

    <?php include_once "../global-includes/script-files.php"; ?>

    <script>
        window.addEventListener("unload", function() {
            navigator.sendBeacon("unset-session.php");
        });
    </script>

</body>

</html>