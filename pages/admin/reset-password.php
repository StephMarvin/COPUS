<?php
    session_start();
    require_once "../../config/datetime.config.php";

    if(empty($_SESSION["temp-reset"])) {
        header("Location: login.php");
        exit();
    }

    $token_expiry_date = strtotime($_SESSION["temp-reset"]["token-expiry"]);
    if(!$token_expiry_date || $token_expiry_date < time()) {
        $_SESSION["query-status"] = [
            "status" => "danger",
            "message" => "Reset token expired! Please try again."
        ];

        unset($_SESSION["temp-reset"]);
        header("Location: login.php");
        exit();
    }

    if(!isset($_SESSION["otp-attempts"])) {
        $_SESSION["otp-attempts"] = 0;
    }

    if($_SESSION["otp-attempts"] >= 5) {
        $_SESSION["query-status"] = [
            "status" => "danger",
            "message" => "Maximum OTP attempts exceeded! Please login again."
        ];

        unset($_SESSION["temp-reset"], $_SESSION["otp-attempts"]);
        header("Location: login.php");
        exit();
    }
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title> COPUS: Reset Password </title>

  <link rel="shortcut icon" type="image/x-icon" href="../../public/assets/website-logo-cite.png" />
  
  <link rel="stylesheet" href="../../public/login/assets/css/styles.min.css" />
  <link rel="stylesheet" href="../../public/login/assets/css/custom-styles.css" />
  
</head>

<body>

    <!-- Main -->

    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

        <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">

            <div class="d-flex align-items-center justify-content-center w-100">

                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">

                            <div class="card-body">

                                <?php if (isset($_SESSION["query-status"]) && $_SESSION["query-status"] !== ""): ?>
                                    <div class="alert alert-<?php echo $_SESSION["query-status"]["status"]; ?> text-center" id="notification" role="alert">
                                        <?php echo $_SESSION["query-status"]["message"]; ?>
                                    </div>
                                    <?php unset($_SESSION["query-status"]); ?>
                                <?php endif; ?>

                                <a href="#" class="text-nowrap logo-img text-center d-block py-3 w-100">
                                    <img src="../../public/assets/website-logo-cite.png" width="180" alt=""> 
                                </a>
                                <p class="text-center"> Reset Admin Password </p>

                                <form action="../../process/admin/admin-auth.php" method="POST" autocomplete="off" id="reset-password-form">
                                    
                                    <div class="mb-3">
                                        <label for="token" class="form-label"> Password Reset Token: </label>
                                        <input type="text" class="form-control" name="reset-token" id="token" placeholder="Password Reset Token" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="new-password" class="form-label"> New Password: </label>
                                        <input type="password" class="form-control" name="new-password" id="new-password" placeholder="New Password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$" title="Passwords must at least 8 characters long, at least 1 uppercase, lowecase letters, digits and symbols." required>
                                        
                                        <div id="passwordError" class="error text-danger"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="confirm-password" class="form-label"> Confirm New Password: </label>
                                        <input type="password" class="form-control" name="confirm-password" id="confirm-password" placeholder="Confirm New Password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$" title="Passwords must at least 8 characters long, at least 1 uppercase, lowecase letters, digits and symbols." required>
                                        
                                        <div id="confirmPasswordError" class="error text-danger"></div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between mb-4">

                                        <div class="form-check">
                                            <input class="form-check-input primary" type="checkbox" id="show-passwords">
                                            <label class="form-check-label text-dark" for="show-passwords">
                                                Show Password
                                            </label>
                                        </div>

                                    </div>
                                    
                                    <?php include_once("../global-includes/password-validator.php"); ?>

                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <p>
                                            <i>
                                                Note: <br> The password reset token will expire in: <b><?php echo htmlspecialchars(format_unix_time($_SESSION["temp-reset"]["token-expiry"])); ?></b>
                                            </i>
                                        </p>
                                    </div>

                                    <input type="submit" name="reset-password" value="Reset Password" class="btn btn-primary w-100 py-8 fs-4 mb-2 rounded-2 custom-btn">
                                    
                                </form>

                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- End Main -->

    <!-- Scripts File -->
    <script src="../../public/login/assets/js/login.js"></script>
    <script src="../../public/login/assets/js/password-reset.js"></script>

    <script>
        setTimeout(() => {
        const notification = document.getElementById("notification")

            if(notification) {
                notification.classList.add("fade-out");
                setTimeout(() => {
                    notification.style.display = "none";
                }, 1000)
            }
        }, 3000);
    </script>

</body>

</html>