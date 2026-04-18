<?php
    session_start();
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title> COPUS: Observer Login </title>

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

                                <a href="../../login.php" class="text-nowrap logo-img text-center d-block py-3 w-100">
                                    <img src="../../public/assets/website-logo-cite.png" width="180" alt=""> 
                                </a>
                                <p class="text-center"> Login as Observer </p>

                                <form action="../../process/observer/observer-auth.php" method="POST">
                                    <div class="mb-3">
                                        <label for="id-number" class="form-label"> ID Number: </label>
                                        <input type="text" class="form-control" id="id-number" name="id-number" placeholder="ID Number" autofocus required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="password" class="form-label"> Password: </label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between mb-4">

                                        <div class="form-check">
                                            <input class="form-check-input primary" type="checkbox" id="show-password">
                                            <label class="form-check-label text-dark" for="show-password">
                                                Show Password
                                            </label>
                                        </div>
                                        <a class="fw-bold custom-btn-color" href="forgot-password.php"> Forgot Password ?</a>

                                    </div>

                                    <input type="submit" name="teacher-login" value="Sign In" class="btn btn-primary w-100 py-8 fs-4 mb-2 rounded-2 custom-btn">

                                </form>

                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- End Main -->

    <!-- Script File -->
    <script src="../../public/login/assets/js/login.js"></script>

</body>

</html>