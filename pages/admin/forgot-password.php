<?php
    session_start();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> COPUS: Forgot Password </title>

    <link rel="shortcut icon" type="image/png" href="../../public/assets/website-logo-cite.png" />

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

                                <a href="../index.php" class="text-nowrap logo-img text-center d-block py-3 w-100">
                                    <!-- Change this to Actual Website AcademiLink Logo -->
                                    <img src="../../public/assets/website-logo-cite.png" width="180" alt=""> 
                                </a>
                                <p class="text-center"> Forgot Administrator Password </p>

                                <form action="../../process/admin/admin-auth.php" method="POST">

                                    <div class="mb-3">
                                        <p> Enter your <b>email address</b> and <b>ID number</b> to continue: </p>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label"> Email Address: </label>
                                        <input type="email" class="form-control" id="email" name="email-address" placeholder="Email Address" autofocus >
                                    </div>

                                    <div class="mb-4">
                                        <label for="id" class="form-label"> ID Number: </label>
                                        <input type="text" class="form-control" id="id" name="id-number" placeholder="ID Number" >
                                    </div>

                                    <input type="submit" name="verify-account" value="Verify" class="btn btn-primary w-100 py-8 fs-4 mb-2 rounded-2 custom-btn">

                                    <div class="d-flex align-items-center justify-content-between mb-1 mt-2">

                                        <div></div>

                                        <a class="fw-bold custom-btn-color" href="login.php"> Click here to Login </a>

                                    </div>
                                    
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