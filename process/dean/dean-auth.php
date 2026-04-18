<?php

    require_once "../../config/conn.config.php";
    require_once "../../config/functions.config.php";
    require_once "../../config/mailer.config.php";

    // Login Dean
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["dean-login"])) {

        $id_number = htmlspecialchars(trim($_POST["id-number"]));
        $password = htmlspecialchars(trim($_POST["password"]));

        if(empty($id_number) || empty($password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/deans/login.php");
            exit();
        }

        else {
            try {
                $check_deans_id = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE id_number = :id_number AND is_archived = 'No' LIMIT 1");
                $check_deans_id->execute([":id_number" => $id_number]);

                if($check_deans_id->rowCount() === 1) {
                    $dean_credentials = $check_deans_id->fetch(PDO::FETCH_OBJ);

                    if(password_verify($password, $dean_credentials->dean_password)) {

                        if($dean_credentials->locked_account === "Yes") {
                            $_SESSION["locked-account"] = true;
                            header("Location: ../../pages/locked/locked-account.php");
                            exit();
                        }

                        else if($dean_credentials->two_factor_authentication === "Disabled") {
                            
                            $update_login = $conn->prepare("UPDATE deans_credentials_tbl SET last_login = CURRENT_TIMESTAMP() WHERE id_number = :id_number");
                            $update_login->execute([":id_number" => $id_number]);
                            
                            $_SESSION["dean-id"] = $id_number;

                            header("Location: ../../pages/deans/home.php");
                            exit();
                        }

                        else {

                            $full_name = $dean_credentials->first_name . " " . $dean_credentials->last_name;
                            $email_address = $dean_credentials->email_address;

                            $otp_code = generate_otp_code();
                            $otp_expiry = generate_expiry_time(5);

                            $update_otp = $conn->prepare("UPDATE deans_credentials_tbl SET otp_code = :otp_code, otp_code_expiry = :otp_expiry WHERE id_number = :id_number");
                            $update_otp->execute([
                                ":otp_code" => $otp_code,
                                ":otp_expiry" => $otp_expiry,
                                ":id_number" => $id_number
                            ]);

                            send_otp($email_address, $full_name, $otp_code);
                            $_SESSION["temp-login"] = ["dean-id" => $id_number, "otp-expiry" => $otp_expiry];

                            $_SESSION["query-status"] = [
                                "status" => "success",
                                "message" => "The OTP code has been sent to your email: " . $email_address
                            ];

                            header("Location: ../../pages/deans/auth.php");
                            exit();
                            
                        }
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid ID number or password! Please try again."
                        ];
            
                        header("Location: ../../pages/deans/login.php");
                        exit();
                    }
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Invalid ID number or password! Please try again."
                    ];
        
                    header("Location: ../../pages/deans/login.php");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
    
                header("Location: ../../pages/deans/login.php");
                exit();
            }
        }
    }

    // OTP Dean
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit-otp"]) && isset($_SESSION["temp-login"])) {
        $dean_id = htmlspecialchars(trim($_SESSION["temp-login"]["dean-id"]));
        $otp_code = htmlspecialchars(trim($_POST["otp-code"]));

        if(empty($otp_code)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            $_SESSION["otp-attempts"]++;

            header("Location: ../../pages/dean/auth.php");
            exit();
        }

        else {
            try {
                $check_otp = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE id_number = :id_number AND otp_code = :otp LIMIT 1");
                $check_otp->execute([
                    ":id_number" => $dean_id,
                    ":otp" => $otp_code
                ]);

                if($check_otp->rowCount() === 1) {
                    $dean_credentials = $check_otp->fetch(PDO::FETCH_OBJ);

                    if(strtotime($dean_credentials->otp_code_expiry) >= time()) {

                        $update_login = $conn->prepare("UPDATE deans_credentials_tbl SET last_login = CURRENT_TIMESTAMP() WHERE id_number = :id_number");
                        $update_login->execute([":id_number"  => $dean_id]);

                        unset($_SESSION["temp-login"], $_SESSION["otp-attempts"]);
                        $_SESSION["dean-id"] = $dean_id;

                        header("Location: ../../pages/deans/home.php");
                        exit();
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "OTP code expired! Please login again."
                        ];

                        unset($_SESSION["temp-login"], $_SESSION["otp-attempts"]);
                        header("Location: ../../pages/deans/login.php");
                        exit();
                    }
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Invalid OTP code! Please try again."
                    ];

                    $_SESSION["otp-attempts"]++;
        
                    header("Location: ../../pages/deans/auth.php");
                    exit();
                }
            }
            
            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
    
                header("Location: ../../pages/deans/auth.php");
                exit();
            }
        }
    }

    // Reset Dean Password
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["verify-account"])) {
        $email_address = filter_var($_POST["email-address"], FILTER_SANITIZE_EMAIL);
        $id_number = htmlspecialchars(trim($_POST["id-number"]));

        if(empty($email_address) || empty($id_number)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/deans/forgot-password.php");
            exit();
        }
        
        else {
            if(filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
                try {
                    $check_credentials = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE id_number = :id_number AND email_address = :email_address LIMIT 1");
                    $check_credentials->execute([":id_number" => $id_number, ":email_address" => $email_address]);

                    if($check_credentials->rowCount() === 1) {             
                        $dean_details = $check_credentials->fetch(PDO::FETCH_OBJ);
                        $full_name = $dean_details->first_name . " " . $dean_details->last_name;

                        $reset_token = generate_reset_token();
                        $token_expiry = generate_expiry_time(5);

                        $update_reset_token = $conn->prepare("UPDATE deans_credentials_tbl SET password_reset_token = :token, reset_token_expiry = :expiry WHERE id_number = :id_number");
                        $update_reset_token->execute([
                            ":token" => $reset_token,
                            ":expiry" => $token_expiry,
                            ":id_number" => $id_number
                        ]);

                        send_reset_token($email_address, $full_name, $reset_token);

                        $_SESSION["temp-reset"] = ["dean-id" => $id_number, "token-expiry" => $token_expiry];

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "The reset token has been sent to your email: " . $email_address
                        ];

                        header("Location: ../../pages/deans/reset-password.php");
                        exit();
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Deans account not found!"
                        ];
            
                        header("Location: ../../pages/deans/forgot-password.php");
                        exit();
                    }
                }

                catch(PDOException $e) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "An unknown error occured! Please try again."
                    ];
        
                    header("Location: ../../pages/deans/forgot-password.php");
                    exit();
                }
            }

            else {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "Invalid email address format! Please try again."
                ];
    
                header("Location: ../../pages/deans/forgot-password.php");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reset-password"]) && isset($_SESSION["temp-reset"])) {
        $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/";

        $id_number = $_SESSION["temp-reset"]["dean-id"];
        $reset_token = htmlspecialchars(trim($_POST["reset-token"]));
        $new_password = htmlspecialchars(trim($_POST["new-password"]));
        $confirm_password = htmlspecialchars(trim($_POST["confirm-password"]));

        if(empty($id_number) || empty($reset_token) || empty($new_password) || empty($confirm_password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            $_SESSION["otp-attempts"]++;

            header("Location: ../../pages/deans/reset-password.php");
            exit();
        }

        else if(!preg_match($pattern, $new_password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid password format! Please try again."
            ];

            $_SESSION["otp-attempts"]++;

            header("Location: ../../pages/deans/reset-password.php");
            exit();
        }

        else {
            if($new_password === $confirm_password) {
                try {
                    $check_token = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE password_reset_token = :token AND id_number = :id_number LIMIT 1");
                    $check_token->execute([
                        ":token" => $reset_token,
                        ":id_number" => $id_number
                    ]);

                    if($check_token->rowCount() === 1) {
                        $token_details = $check_token->fetch(PDO::FETCH_OBJ);

                        if(strtotime($token_details->reset_token_expiry) >= time()) {
                            $update_password = $conn->prepare("UPDATE deans_credentials_tbl SET dean_password = :dean_password, generated_password = :generated_password WHERE id_number = :id_number");
                            $update_password->execute([
                                ":dean_password" => password_hash($new_password, PASSWORD_BCRYPT),
                                ":generated_password" => "No",
                                ":id_number" => $id_number
                            ]);

                            $full_name = $token_details -> first_name . " " . $token_details -> last_name;
                            $email_address = $token_details -> email_address;
                            send_reset_password_notification($email_address, $full_name);

                            $_SESSION["query-status"] = [
                                "status" => "success",
                                "message" => "Password reset complete! Please login to continue."
                            ];
                
                            unset($_SESSION["temp-reset"], $_SESSION["otp-attempts"]);
                
                            header("Location: ../../pages/deans/login.php");
                            exit();
                        }

                        else {
                            $_SESSION["query-status"] = [
                                "status" => "danger",
                                "message" => "Reset token is already expired! Please try again."
                            ];
                
                            unset($_SESSION["temp-reset"], $_SESSION["otp-attempts"]);
                
                            header("Location: ../../pages/deans/login.php");
                            exit();
                        }
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid token! Please try again."
                        ];
            
                        $_SESSION["otp-attempts"]++;
            
                        header("Location: ../../pages/deans/reset-password.php");
                        exit();
                    }
                }

                catch(PDOException $e) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "An unknown error occured! Please try again."
                    ];
        
                    $_SESSION["otp-attempts"]++;
        
                    header("Location: ../../pages/deans/reset-password.php");
                    exit();
                }
            }

            else {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "Passwords don't match! Please try again."
                ];
    
                $_SESSION["otp-attempts"]++;
    
                header("Location: ../../pages/deans/reset-password.php");
                exit();
            }
        }
    }

    // Default
    else {
        header("Location: ../../index.php");
    }
?>