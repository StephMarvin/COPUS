<?php
    require_once "../../config/conn.config.php";
    require_once "../../config/dropdowns.config.php";
    require_once "../../config/functions.config.php";
    require_once "../../config/mailer.config.php";

    // Add Admin Account
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-new-admin"])) {
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));
        $first_name = htmlspecialchars($_POST["first-name"]);
        $middle_name = htmlspecialchars($_POST["middle-name"]);
        $last_name = htmlspecialchars($_POST["last-name"]);
        $gender = htmlspecialchars($_POST["gender"]);
        $status = htmlspecialchars($_POST["marital-status"]);
        $id_number = htmlspecialchars(trim($_POST["id-number"]));
        $email_address = filter_var($_POST["email-address"], FILTER_SANITIZE_EMAIL);
        $phone_number = htmlspecialchars(trim($_POST["phone-number"]));
        $temporary_address = htmlspecialchars($_POST["temporary-address"]);
        $permanent_address = htmlspecialchars($_POST["permanent-address"]);
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));
        $confirmation = htmlspecialchars(trim($_POST["confirmation"]));
        
        // Backup code
        // if (empty($admin_id) || empty($first_name) || empty($last_name) || empty($gender) || empty($status) || empty($id_number) || empty($email_address) || empty($phone_number) || empty($permanent_address) || empty($admin_password) || empty($confirmation)) {
        //     $_SESSION["query-status"] = [
        //         "status" => "danger",
        //         "message" => "An unknown error occured! Please try again."
        //     ];

        //     header("Location: ../../pages/admin/home.php?page=add-admin");
        //     exit();
        // }

        if (empty($admin_id) || empty($first_name) || empty($last_name) || empty($gender) || empty($status) || empty($id_number) || empty($email_address) || empty($admin_password) || empty($confirmation)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=add-admin");
            exit();
        }

        if (!in_array($gender, $allowed_gender)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid gender! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=add-admin");
            exit();
        }

        if (!in_array($status, $allowed_status)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid status! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=add-admin");
            exit();
        }

        if ($confirmation !== "true") {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=add-admin");
            exit();
        }

        if (filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            try {
                $check_id_number = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number");
                $check_id_number->execute([":id_number" => $id_number]);

                $check_email_address = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE email_address = :email_address");
                $check_email_address->execute([":email_address" => $email_address]);

                if ($check_id_number->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "ID number already exists! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=add-admin");
                    exit();
                } else if ($check_email_address->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Email address already exists! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=add-admin");
                    exit();
                } else {
                    $check_admin_user = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                    $check_admin_user->execute([":id_number" => $admin_id]);

                    if ($check_admin_user->rowCount() === 1) {
                        $get_admin_password = $check_admin_user->fetch(PDO::FETCH_OBJ);

                        if (password_verify($admin_password, $get_admin_password->admin_password)) {
                            $full_name = $first_name . " " . $last_name;
                            $generated_password = $id_number;

                            $new_admin_credentials = $conn->prepare("INSERT INTO admin_credentials_tbl(id_number, admin_password, first_name, middle_name, last_name, email_address)
                                                                            VALUES(:id_number, :admin_password, :first_name, :middle_name, :last_name, :email_address)");

                            $new_admin_credentials->execute([
                                ":id_number" => $id_number,
                                ":admin_password" => password_hash($generated_password, PASSWORD_DEFAULT),
                                ":first_name" => $first_name,
                                ":middle_name" => $middle_name,
                                ":last_name" => $last_name,
                                ":email_address" => $email_address
                            ]);

                            $add_admin_info = $conn->prepare("INSERT INTO admin_info_tbl(id_number, phone_number, temporary_address, permanent_address, gender, marital_status)
                                                                    VALUES(:id_number, :phone_number, :temp_address, :perm_address, :gender, :marital_status)");

                            $add_admin_info->execute([
                                ":id_number" => $id_number,
                                ":phone_number" => $phone_number,
                                ":temp_address" => $temporary_address,
                                ":perm_address" => $permanent_address,
                                ":gender" => $gender,
                                ":marital_status" => $status
                            ]);

                            send_admin_creation_email($email_address, $full_name, "Super Admin", $id_number, $generated_password);

                            $_SESSION["query-status"] = [
                                "status" => "success",
                                "message" => "Admin account created successfully!"
                            ];

                            header("Location: ../../pages/admin/home.php?page=admin-accounts");
                            exit();
                        } else {
                            $_SESSION["query-status"] = [
                                "status" => "danger",
                                "message" => "Invalid password! Please try again."
                            ];

                            header("Location: ../../pages/admin/home.php?page=add-admin");
                            exit();
                        }
                    } else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Admin user not found! Please try again."
                        ];

                        header("Location: ../../pages/admin/home.php?page=add-admin");
                        exit();
                    }
                }
            } catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=add-admin");
                exit();
            }
        } else {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid email address format! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=add-admin");
            exit();
        }
    }

    // Archive Admin Account
    else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["archive-admin-account"])) {
        $admin_user_id = htmlspecialchars(trim(base64_decode($_POST["admin-user-id"])));
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        $encoded_id = base64_encode($admin_user_id);

        if (empty($admin_user_id) || empty($admin_id) || empty($admin_password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
            exit();
        } 

        else if($admin_user_id == $admin_id) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Can't archive current logged in account! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
            exit();
        }
        
        else {
            try {
                $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_admin_account->execute([":id_number" => $admin_id]);

                if ($check_admin_account->rowCount() === 1) {
                    $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                    if (password_verify($admin_password, $admin_credentials->admin_password)) {
                        $archive_account = $conn->prepare("UPDATE admin_credentials_tbl SET is_archived = :is_archived WHERE id_number = :admin_user_id");
                        $archive_account->execute([
                            ":is_archived" => "Yes",
                            ":admin_user_id" => $admin_user_id
                        ]);


                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "This account has been archived!"
                        ];

                        header("Location: ../../pages/admin/home.php?page=archived-admin-accounts");
                        exit();
                    } 
                    
                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
                        exit();
                    }
                } 
                
                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
                    exit();
                }
            } 
            
            catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "Unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
                exit();
            }
        }
    } 

    // Restore Admin's Account
    else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["restore-admin-account"])) {
        $admin_user_id = htmlspecialchars(trim(base64_decode($_POST["admin-user-id"])));

        if (empty($admin_user_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=archived-admin-accounts");
            exit();
        } 
        
        else {
            try {
                $restore_account = $conn->prepare("UPDATE admin_credentials_tbl SET is_archived = :is_archived WHERE id_number = :admin_user_id");
                $restore_account->execute([
                    ":is_archived" => "No",
                    ":admin_user_id" => $admin_user_id,
                ]);

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "This account has been restored!"
                ];

                header("Location: ../../pages/admin/home.php?page=admin-accounts");
                exit();
            } 
            
            catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "Unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=archived-admin-accounts");
                exit();
            }
        }
    }

    // Lock Admin Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["lock-admin-account"])) {
        $admin_user_id = htmlspecialchars(trim(base64_decode($_POST["admin-user-id"])));
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        $encoded_id = base64_encode($admin_user_id);

        if(empty($admin_user_id) || empty($admin_id) || empty($admin_password)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_admin_account->execute([":id_number" => $admin_id]);

                if($check_admin_account->rowCount() === 1) {
                    $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                    if(password_verify($admin_password, $admin_credentials->admin_password)) {
                        $lock_account = $conn->prepare("UPDATE admin_credentials_tbl SET locked_account = :locked_account WHERE id_number = :admin_user_id");
                        $lock_account->execute([
                            ":locked_account" => "Yes",
                            ":admin_user_id" => $admin_user_id
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "This account has been locked!"
                        ];

                        header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
                        exit();
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
                        exit();
                    }
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Unlock Admin Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["unlock-admin-account"])) {
        $admin_user_id = htmlspecialchars(trim(base64_decode($_POST["admin-user-id"])));
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        $encoded_id = base64_encode($admin_user_id);

        if(empty($admin_user_id) || empty($admin_id) || empty($admin_password)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_admin_account->execute([":id_number" => $admin_id]);

                if($check_admin_account->rowCount() === 1) {
                    $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                    if(password_verify($admin_password, $admin_credentials->admin_password)) {
                        $lock_account = $conn->prepare("UPDATE admin_credentials_tbl SET locked_account = :locked_account WHERE id_number = :admin_user_id");
                        $lock_account->execute([
                            ":locked_account" => "No",
                            ":admin_user_id" => $admin_user_id
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "This account has been unlocked!"
                        ];

                        header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
                        exit();
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
                        exit();
                    }
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Reset Admin Password
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reset-admin-password"])) {
        $admin_user_id = htmlspecialchars(trim(base64_decode($_POST["admin-user-id"])));
        $id_number = htmlspecialchars(trim($_POST["id-number"]));
        $email_address = filter_var($_POST["email-address"], FILTER_SANITIZE_EMAIL);

        $encoded_id = base64_encode($admin_user_id);

        if(empty($admin_user_id) || empty($id_number) || empty($email_address)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
           exit();
        }
        
        else if(!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "Invalid email address format! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
           exit();
        }

        else if($admin_user_id !== $id_number) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "Invalid account! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number AND email_address = :email_address LIMIT 1");
                $check_account->execute([
                    ":id_number" => $id_number,
                    ":email_address" => $email_address
                ]);

                if($check_account->rowCount() === 1) {
                    $reset_password = $conn->prepare("UPDATE admin_credentials_tbl SET admin_password = :default_password, generated_password = :generated_password, two_factor_authentication = :2fa WHERE id_number = :id_number");
                    $reset_password->execute([
                        ":default_password" => password_hash($admin_user_id, PASSWORD_DEFAULT),
                        ":generated_password" => "Yes",
                        ":2fa" => "Disabled",
                        ":id_number" => $admin_user_id
                    ]);

                    $_SESSION["query-status"] = [
                        "status" => "success",
                        "message" => "Password has been reset successfully!"
                    ];

                    header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
                    exit();
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Account not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/admin/home.php?page=admin-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Add Teacher Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-new-teacher"])) {
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));
        $first_name = htmlspecialchars($_POST["first-name"]);
        $middle_name = htmlspecialchars($_POST["middle-name"]);
        $last_name = htmlspecialchars($_POST["last-name"]);
        $gender = htmlspecialchars($_POST["gender"]);
        $status = htmlspecialchars($_POST["marital-status"]);
        $employment_status = htmlspecialchars($_POST["employment-status"]);
        $teacher_rank = htmlspecialchars($_POST["teacher-rank"]);
        $id_number = htmlspecialchars(trim($_POST["id-number"]));
        $email_address = filter_var($_POST["email-address"], FILTER_SANITIZE_EMAIL);
        $phone_number = htmlspecialchars(trim($_POST["phone-number"]));
        $temporary_address = htmlspecialchars($_POST["temporary-address"]);
        $permanent_address = htmlspecialchars($_POST["permanent-address"]);
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));
        $confirmation = htmlspecialchars(trim($_POST["confirmation"]));

        if(empty($admin_id) || empty($first_name) || empty($last_name) || empty($gender) || empty($status) || empty($employment_status) || empty($teacher_rank) || empty($id_number) || empty($email_address) || empty($phone_number) || empty($permanent_address) || empty($admin_password) || empty($confirmation)) { 
           $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=add-teacher");
           exit();
        }

        if(!in_array($gender, $allowed_gender)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid gender! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=add-teacher");
           exit();
        }

        if(!in_array($status, $allowed_status)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid status! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=add-teacher");
           exit();
        }

        if(!in_array($employment_status, $allowed_employment_status)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid employment status! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=add-teacher");
           exit();
        }

        if(!in_array($teacher_rank, $allowed_rank)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid teacher rank! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=add-teacher");
           exit();
        }

        if($confirmation !== "true") {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];
 
            header("Location: ../../pages/admin/home.php?page=add-teacher");
            exit();
        }

        if(filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            try {
                $check_id_number = $conn->prepare("SELECT * FROM teacher_credentials_tbl WHERE id_number = :id_number");
                $check_id_number->execute([":id_number" => $id_number]);

                $check_email_address = $conn->prepare("SELECT * FROM teacher_credentials_tbl WHERE email_address = :email_address");
                $check_email_address->execute([":email_address" => $email_address]);

                if($check_id_number->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "ID number already exists! Please try again."
                    ];
         
                    header("Location: ../../pages/admin/home.php?page=add-teacher");
                    exit();
                }

                else if($check_email_address->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Email address already exists! Please try again."
                    ];
         
                    header("Location: ../../pages/admin/home.php?page=add-teacher");
                    exit();
                }

                else {
                    $check_admin_user = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                    $check_admin_user->execute([":id_number" => $admin_id]);

                    if($check_admin_user->rowCount() === 1) {
                        $get_admin_password = $check_admin_user->fetch(PDO::FETCH_OBJ);

                        if(password_verify($admin_password, $get_admin_password->admin_password)) {
                            $full_name = $first_name . " " . $last_name;
                            $generated_password = $id_number;

                            $new_teacher_credentials = $conn->prepare("INSERT INTO teacher_credentials_tbl(id_number, teacher_password, first_name, middle_name, last_name, email_address, employment_status, teacher_rank)
                                                                        VALUES(:id_number, :teacher_password, :first_name, :middle_name, :last_name, :email_address, :employment_status, :teacher_rank)");

                            $new_teacher_credentials->execute([
                                ":id_number" => $id_number,
                                ":teacher_password" => password_hash($generated_password, PASSWORD_DEFAULT),
                                ":first_name" => $first_name,
                                ":middle_name" => $middle_name,
                                ":last_name" => $last_name,
                                ":email_address" => $email_address,
                                ":employment_status" => $employment_status,
                                ":teacher_rank" => $teacher_rank
                            ]);

                            $add_teacher_info = $conn->prepare("INSERT INTO teacher_info_tbl(id_number, phone_number, temporary_address, permanent_address, gender, marital_status)
                                                                VALUES(:id_number, :phone_number, :temp_address, :perm_address, :gender, :marital_status)");

                            $add_teacher_info->execute([
                                ":id_number" => $id_number,
                                ":phone_number" => $phone_number,
                                ":temp_address" => $temporary_address,
                                ":perm_address" => $permanent_address,
                                ":gender" => $gender,
                                ":marital_status" => $status
                            ]);

                            send_user_creation_email($email_address, $full_name, "Teacher", $id_number, $generated_password);

                            $_SESSION["query-status"] = [
                                "status" => "success",
                                "message" => "Teacher account created successfully!"
                            ];
                 
                            header("Location: ../../pages/admin/home.php?page=teachers-accounts");
                            exit();
                        }

                        else {
                            $_SESSION["query-status"] = [
                                "status" => "danger",
                                "message" => "Invalid password! Please try again."
                            ];
                 
                            header("Location: ../../pages/admin/home.php?page=add-teacher");
                            exit();
                        }
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Admin user not found! Please try again."
                        ];
             
                        header("Location: ../../pages/admin/home.php?page=add-teacher");
                        exit();
                    }
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
     
                header("Location: ../../pages/admin/home.php?page=add-teacher");
                exit();
            }
        }

        else {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid email address format! Please try again."
            ];
 
            header("Location: ../../pages/admin/home.php?page=add-teacher");
            exit();
        }
    }

    // Update Teacher's Employment Status
    else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update-employment-status"])) {
        $teacher_user_id = htmlspecialchars(trim(base64_decode($_POST["teacher-user-id"])));
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));

        $employment_status = htmlspecialchars($_POST["employment-status"]);
        $teacher_rank = htmlspecialchars($_POST["teacher-rank"]);
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        $encoded_id = base64_encode($teacher_user_id);

        if (empty($teacher_user_id) || empty($admin_id) || empty($admin_password) || empty($employment_status) || empty($teacher_rank)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
            exit();
        } else if (!in_array($employment_status, $allowed_employment_status)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid employment status! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
            exit();
        } else if (!in_array($teacher_rank, $allowed_rank)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid teacher rank! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
            exit();
        } else {
            try {
                $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_admin_account->execute([":id_number" => $admin_id]);

                if ($check_admin_account->rowCount() === 1) {
                    $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                    if (password_verify($admin_password, $admin_credentials->admin_password)) {
                        $update_status = $conn->prepare("UPDATE 
                                                                    teacher_credentials_tbl 
                                                                SET employment_status = :employment_status,
                                                                teacher_rank = :teacher_rank
                                                                WHERE id_number = :teacher_user_id");
                        $update_status->execute([
                            ":employment_status" => $employment_status,
                            ":teacher_rank" => $teacher_rank,
                            ":teacher_user_id" => $teacher_user_id
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "Employment status updated successfully!"
                        ];

                        header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                        exit();
                    } else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                        exit();
                    }
                } else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "Unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Archive Teacher Account
    else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["archive-teacher-account"])) {
        $teacher_user_id = htmlspecialchars(trim(base64_decode($_POST["teacher-user-id"])));
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        $encoded_id = base64_encode($teacher_user_id);

        if (empty($teacher_user_id) || empty($admin_id) || empty($admin_password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
            exit();
        } else {
            try {
                $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_admin_account->execute([":id_number" => $admin_id]);

                if ($check_admin_account->rowCount() === 1) {
                    $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                    if (password_verify($admin_password, $admin_credentials->admin_password)) {
                        $archive_account = $conn->prepare("UPDATE teacher_credentials_tbl SET is_archived = :is_archived WHERE id_number = :teacher_user_id");
                        $archive_account->execute([
                            ":is_archived" => "Yes",
                            ":teacher_user_id" => $teacher_user_id
                        ]);

                        $delete_current_observation = $conn->prepare("DELETE FROM observations_tbl WHERE teacher_id = :teacher_id AND observe_status = :observe_status");
                        $delete_current_observation->execute([
                            ":teacher_id" => $teacher_user_id,
                            ":observe_status" => "Incomplete"
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "This account has been archived!"
                        ];

                        header("Location: ../../pages/admin/home.php?page=archived-teachers-accounts");
                        exit();
                    } else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                        exit();
                    }
                } else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "Unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                exit();
            }
        }
    } 

    // Restore Teacher's Account
    else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["restore-teacher-account"])) {
        $teacher_user_id = htmlspecialchars(trim(base64_decode($_POST["teacher-user-id"])));

        if (empty($teacher_user_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=archived-teachers-accounts");
            exit();
        } 
        
        else {
            try {
                $restore_account = $conn->prepare("UPDATE teacher_credentials_tbl SET is_archived = :is_archived WHERE id_number = :teacher_user_id");
                $restore_account->execute([
                    ":is_archived" => "No",
                    ":teacher_user_id" => $teacher_user_id
                ]);

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "This account has been restored!"
                ];

                header("Location: ../../pages/admin/home.php?page=teachers-accounts");
                exit();
            } 
            
            catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "Unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=archived-teachers-accounts");
                exit();
            }
        }
    }

    // Lock Teacher Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["lock-teacher-account"])) {
        $teacher_user_id = htmlspecialchars(trim(base64_decode($_POST["teacher-user-id"])));
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        $encoded_id = base64_encode($teacher_user_id);

        if(empty($teacher_user_id) || empty($admin_id) || empty($admin_password)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_admin_account->execute([":id_number" => $admin_id]);

                if($check_admin_account->rowCount() === 1) {
                    $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                    if(password_verify($admin_password, $admin_credentials->admin_password)) {
                        $lock_account = $conn->prepare("UPDATE teacher_credentials_tbl SET locked_account = :locked_account WHERE id_number = :teacher_user_id");
                        $lock_account->execute([
                            ":locked_account" => "Yes",
                            ":teacher_user_id" => $teacher_user_id
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "This account has been locked!"
                        ];

                        header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                        exit();
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                        exit();
                    }
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Unlock Teacher Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["unlock-teacher-account"])) {
        $teacher_user_id = htmlspecialchars(trim(base64_decode($_POST["teacher-user-id"])));
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        $encoded_id = base64_encode($teacher_user_id);

        if(empty($teacher_user_id) || empty($admin_id) || empty($admin_password)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_admin_account->execute([":id_number" => $admin_id]);

                if($check_admin_account->rowCount() === 1) {
                    $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                    if(password_verify($admin_password, $admin_credentials->admin_password)) {
                        $lock_account = $conn->prepare("UPDATE teacher_credentials_tbl SET locked_account = :locked_account WHERE id_number = :teacher_user_id");
                        $lock_account->execute([
                            ":locked_account" => "No",
                            ":teacher_user_id" => $teacher_user_id
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "This account has been unlocked!"
                        ];

                        header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                        exit();
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                        exit();
                    }
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Reset Teacher Password
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reset-teacher-password"])) {
        $teacher_user_id = htmlspecialchars(trim(base64_decode($_POST["teacher-user-id"])));
        $id_number = htmlspecialchars(trim($_POST["id-number"]));
        $email_address = filter_var($_POST["email-address"], FILTER_SANITIZE_EMAIL);

        $encoded_id = base64_encode($teacher_user_id);

        if(empty($teacher_user_id) || empty($id_number) || empty($email_address)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
           exit();
        }
        
        else if(!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "Invalid email address format! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
           exit();
        }

        else if($teacher_user_id !== $id_number) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "Invalid account! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_account = $conn->prepare("SELECT * FROM teacher_credentials_tbl WHERE id_number = :id_number AND email_address = :email_address LIMIT 1");
                $check_account->execute([
                    ":id_number" => $id_number,
                    ":email_address" => $email_address
                ]);

                if($check_account->rowCount() === 1) {
                    $reset_password = $conn->prepare("UPDATE teacher_credentials_tbl SET teacher_password = :default_password, generated_password = :generated_password, two_factor_authentication = :2fa WHERE id_number = :id_number");
                    $reset_password->execute([
                        ":default_password" => password_hash($teacher_user_id, PASSWORD_DEFAULT),
                        ":generated_password" => "Yes",
                        ":2fa" => "Disabled",
                        ":id_number" => $teacher_user_id
                    ]);

                    $_SESSION["query-status"] = [
                        "status" => "success",
                        "message" => "Password has been reset successfully!"
                    ];

                    header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                    exit();
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Account not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/admin/home.php?page=teacher-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // ================================= Deans Account =========================================
    // Add Deans Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-new-dean"])) {
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));
        $first_name = htmlspecialchars($_POST["first-name"]);
        $middle_name = htmlspecialchars($_POST["middle-name"]);
        $last_name = htmlspecialchars($_POST["last-name"]);
        $department_id = htmlspecialchars($_POST["department-id"]);
        $gender = htmlspecialchars($_POST["gender"]);
        $status = htmlspecialchars($_POST["marital-status"]);
        $id_number = htmlspecialchars(trim($_POST["id-number"]));
        $email_address = filter_var($_POST["email-address"], FILTER_SANITIZE_EMAIL);
        $phone_number = htmlspecialchars(trim($_POST["phone-number"]));
        $temporary_address = htmlspecialchars($_POST["temporary-address"]);
        $permanent_address = htmlspecialchars($_POST["permanent-address"]);
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));
        $confirmation = htmlspecialchars(trim($_POST["confirmation"]));
        
        // Backup Code
        // if(empty($admin_id) || empty($first_name) || empty($last_name) || empty($department_id) || empty($gender) || empty($status) || empty($id_number) || empty($email_address) || empty($phone_number) || empty($permanent_address) || empty($admin_password) || empty($confirmation)) { 
        //   $_SESSION["query-status"] = [
        //       "status" => "danger",
        //       "message" => "An unknown error occured! Please try again."
        //   ];

        //   header("Location: ../../pages/admin/home.php?page=add-dean");
        //   exit();
        // }

        if(empty($admin_id) || empty($first_name) || empty($last_name) || empty($department_id) || empty($gender) || empty($status) || empty($id_number) || empty($email_address) ||  empty($admin_password) || empty($confirmation)) { 
           $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=add-dean");
           exit();
        }

        if(!in_array($gender, $allowed_gender)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid gender! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=add-dean");
           exit();
        }

        if(!in_array($status, $allowed_status)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid status! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=add-dean");
           exit();
        }

        if($confirmation !== "true") {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];
 
            header("Location: ../../pages/admin/home.php?page=add-dean");
            exit();
        }

        if(filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            try {
                $check_id_number = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE id_number = :id_number");
                $check_id_number->execute([":id_number" => $id_number]);

                $check_email_address = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE email_address = :email_address");
                $check_email_address->execute([":email_address" => $email_address]);

                $check_department = $conn->prepare("SELECT * FROM departments_tbl WHERE department_id = :department_id");
                $check_department->execute([":department_id" => $department_id]);

                if($check_id_number->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "ID number already exists! Please try again."
                    ];
         
                    header("Location: ../../pages/admin/home.php?page=add-dean");
                    exit();
                }

                else if($check_email_address->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Email address already exists! Please try again."
                    ];
         
                    header("Location: ../../pages/admin/home.php?page=add-dean");
                    exit();
                }

                else if($check_department->rowCount() <= 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "This department does not exist! Please try again."
                    ];
         
                    header("Location: ../../pages/admin/home.php?page=add-dean");
                    exit();
                }

                else {
                    $check_admin_user = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                    $check_admin_user->execute([":id_number" => $admin_id]);

                    if($check_admin_user->rowCount() === 1) {

                        $get_admin_password = $check_admin_user->fetch(PDO::FETCH_OBJ);

                        $get_department_name = $check_department->fetch(PDO::FETCH_OBJ);
                        $department = $get_department_name->department_name . " (" . $get_department_name->department_code . ")";

                        if(password_verify($admin_password, $get_admin_password->admin_password)) {
                            $full_name = $first_name . " " . $last_name;
                            $generated_password = $id_number;

                            $new_deans_credentials = $conn->prepare("INSERT INTO deans_credentials_tbl(id_number, dean_password, first_name, middle_name, last_name, email_address, department_id)
                                                                        VALUES(:id_number, :dean_password, :first_name, :middle_name, :last_name, :email_address, :department_id)");

                            $new_deans_credentials->execute([
                                ":id_number" => $id_number,
                                ":dean_password" => password_hash($generated_password, PASSWORD_DEFAULT),
                                ":first_name" => $first_name,
                                ":middle_name" => $middle_name,
                                ":last_name" => $last_name,
                                ":email_address" => $email_address,
                                ":department_id" => $department_id
                            ]);

                            $add_deans_info = $conn->prepare("INSERT INTO deans_info_tbl(id_number, phone_number, temporary_address, permanent_address, gender, marital_status)
                                                                VALUES(:id_number, :phone_number, :temp_address, :perm_address, :gender, :marital_status)");

                            $add_deans_info->execute([
                                ":id_number" => $id_number,
                                ":phone_number" => $phone_number,
                                ":temp_address" => $temporary_address,
                                ":perm_address" => $permanent_address,
                                ":gender" => $gender,
                                ":marital_status" => $status
                            ]);

                            $archive_other_deans_account = $conn->prepare("UPDATE deans_credentials_tbl SET is_archived = 'Yes' WHERE id_number != :id_number AND department_id = :department_id");
                            $archive_other_deans_account->execute([":id_number" => $id_number, ":department_id" => $department_id]);

                            send_user_creation_email($email_address, $full_name, "Dean of $department", $id_number, $generated_password);

                            $_SESSION["query-status"] = [
                                "status" => "success",
                                "message" => "Deans account created successfully!"
                            ];
                 
                            header("Location: ../../pages/admin/home.php?page=deans-accounts");
                            exit();
                        }

                        else {
                            $_SESSION["query-status"] = [
                                "status" => "danger",
                                "message" => "Invalid password! Please try again."
                            ];
                 
                            header("Location: ../../pages/admin/home.php?page=add-dean");
                            exit();
                        }
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Admin user not found! Please try again."
                        ];
             
                        header("Location: ../../pages/admin/home.php?page=add-dean");
                        exit();
                    }
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
     
                header("Location: ../../pages/admin/home.php?page=add-dean");
                exit();
            }
        }

        else {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid email address format! Please try again."
            ];
 
            header("Location: ../../pages/admin/home.php?page=add-dean");
            exit();
        }
    }

    // Restore Dean's Account
    else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["restore-deans-account"])) {
        $dean_user_id = htmlspecialchars(trim(base64_decode($_POST["dean-user-id"])));
        $department_id = htmlspecialchars(trim(base64_decode($_POST["department-id"])));

        if (empty($dean_user_id) || empty($department_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=archived-deans-accounts");
            exit();
        } 
        
        else {
            try {
                $restore_account = $conn->prepare("UPDATE deans_credentials_tbl SET is_archived = :is_archived WHERE id_number = :dean_user_id");
                $restore_account->execute([
                    ":is_archived" => "No",
                    ":dean_user_id" => $dean_user_id,
                ]);

                $archive_other_deans_account = $conn->prepare("UPDATE deans_credentials_tbl SET is_archived = 'Yes' WHERE id_number != :id_number AND department_id = :department_id");
                $archive_other_deans_account->execute([":id_number" => $dean_user_id, ":department_id" => $department_id]);

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "This account has been restored!"
                ];

                header("Location: ../../pages/admin/home.php?page=deans-accounts");
                exit();
            } 
            
            catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "Unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=archived-deans-accounts");
                exit();
            }
        }
    }

    // Lock Deans Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["lock-dean-account"])) {
        $dean_user_id = htmlspecialchars(trim(base64_decode($_POST["dean-user-id"])));
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        $encoded_id = base64_encode($dean_user_id);

        if(empty($dean_user_id) || empty($admin_id) || empty($admin_password)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_admin_account->execute([":id_number" => $admin_id]);

                if($check_admin_account->rowCount() === 1) {
                    $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                    if(password_verify($admin_password, $admin_credentials->admin_password)) {
                        $lock_account = $conn->prepare("UPDATE deans_credentials_tbl SET locked_account = :locked_account WHERE id_number = :dean_user_id");
                        $lock_account->execute([
                            ":locked_account" => "Yes",
                            ":dean_user_id" => $dean_user_id
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "This account has been locked!"
                        ];

                        header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
                        exit();
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
                        exit();
                    }
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Unlock Deans Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["unlock-dean-account"])) {
        $dean_user_id = htmlspecialchars(trim(base64_decode($_POST["dean-user-id"])));
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        $encoded_id = base64_encode($dean_user_id);

        if(empty($dean_user_id) || empty($admin_id) || empty($admin_password)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_admin_account->execute([":id_number" => $admin_id]);

                if($check_admin_account->rowCount() === 1) {
                    $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                    if(password_verify($admin_password, $admin_credentials->admin_password)) {
                        $lock_account = $conn->prepare("UPDATE deans_credentials_tbl SET locked_account = :locked_account WHERE id_number = :dean_user_id");
                        $lock_account->execute([
                            ":locked_account" => "No",
                            ":dean_user_id" => $dean_user_id
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "This account has been unlocked!"
                        ];

                        header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
                        exit();
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
                        exit();
                    }
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Reset Dean Password
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reset-dean-password"])) {
        $dean_user_id = htmlspecialchars(trim(base64_decode($_POST["dean-user-id"])));
        $id_number = htmlspecialchars(trim($_POST["id-number"]));
        $email_address = filter_var($_POST["email-address"], FILTER_SANITIZE_EMAIL);

        $encoded_id = base64_encode($dean_user_id);

        if(empty($dean_user_id) || empty($id_number) || empty($email_address)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
           exit();
        }
        
        else if(!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "Invalid email address format! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
           exit();
        }

        else if($dean_user_id !== $id_number) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "Invalid account! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_account = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE id_number = :id_number AND email_address = :email_address LIMIT 1");
                $check_account->execute([
                    ":id_number" => $id_number,
                    ":email_address" => $email_address
                ]);

                if($check_account->rowCount() === 1) {
                    $reset_password = $conn->prepare("UPDATE deans_credentials_tbl SET dean_password = :default_password, generated_password = :generated_password, two_factor_authentication = :2fa WHERE id_number = :id_number");
                    $reset_password->execute([
                        ":default_password" => password_hash($dean_user_id, PASSWORD_DEFAULT),
                        ":generated_password" => "Yes",
                        "2fa" => "Disabled",
                        ":id_number" => $dean_user_id
                    ]);

                    $_SESSION["query-status"] = [
                        "status" => "success",
                        "message" => "Password has been reset successfully!"
                    ];

                    header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
                    exit();
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Account not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/admin/home.php?page=deans-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Add Observers Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-new-observer"])) {
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));
        $first_name = htmlspecialchars($_POST["first-name"]);
        $middle_name = htmlspecialchars($_POST["middle-name"]);
        $last_name = htmlspecialchars($_POST["last-name"]);
        $designation = htmlspecialchars($_POST["designation"]);
        $gender = htmlspecialchars($_POST["gender"]);
        $status = htmlspecialchars($_POST["marital-status"]);
        $id_number = htmlspecialchars(trim($_POST["id-number"]));
        $email_address = filter_var($_POST["email-address"], FILTER_SANITIZE_EMAIL);
        $phone_number = htmlspecialchars(trim($_POST["phone-number"]));
        $temporary_address = htmlspecialchars($_POST["temporary-address"]);
        $permanent_address = htmlspecialchars($_POST["permanent-address"]);
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));
        $confirmation = htmlspecialchars(trim($_POST["confirmation"]));

        if(empty($admin_id) || empty($first_name) || empty($last_name) || empty($gender) || empty($status) || empty($id_number) || empty($email_address) || empty($phone_number) || empty($permanent_address) || empty($admin_password) || empty($confirmation)) { 
           $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=add-observer");
           exit();
        }

        else if(!in_array($designation, $allowed_designations)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid designation! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=add-observer");
           exit();
        }

        else if(!in_array($gender, $allowed_gender)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid gender! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=add-observer");
           exit();
        }

        if(!in_array($status, $allowed_status)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid status! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=add-observer");
           exit();
        }

        if($confirmation !== "true") {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];
 
            header("Location: ../../pages/admin/home.php?page=add-observer");
            exit();
        }

        if(filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            try {
                $check_id_number = $conn->prepare("SELECT * FROM observers_credentials_tbl WHERE id_number = :id_number");
                $check_id_number->execute([":id_number" => $id_number]);

                $check_email_address = $conn->prepare("SELECT * FROM observers_credentials_tbl WHERE email_address = :email_address");
                $check_email_address->execute([":email_address" => $email_address]);

                if($check_id_number->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "ID number already exists! Please try again."
                    ];
         
                    header("Location: ../../pages/admin/home.php?page=add-observer");
                    exit();
                }

                else if($check_email_address->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Email address already exists! Please try again."
                    ];
         
                    header("Location: ../../pages/admin/home.php?page=add-observer");
                    exit();
                }

                else {
                    $check_admin_user = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                    $check_admin_user->execute([":id_number" => $admin_id]);

                    if($check_admin_user->rowCount() === 1) {
                        $get_admin_password = $check_admin_user->fetch(PDO::FETCH_OBJ);

                        if(password_verify($admin_password, $get_admin_password->admin_password)) {
                            $full_name = $first_name . " " . $last_name;
                            $generated_password = $id_number;

                            $new_observer_credentials = $conn->prepare("INSERT INTO observers_credentials_tbl(id_number, observer_password, first_name, middle_name, last_name, designation, email_address)
                                                                        VALUES(:id_number, :observer_password, :first_name, :middle_name, :last_name, :designation, :email_address)");

                            $new_observer_credentials->execute([
                                ":id_number" => $id_number,
                                ":observer_password" => password_hash($generated_password, PASSWORD_DEFAULT),
                                ":first_name" => $first_name,
                                ":middle_name" => $middle_name,
                                ":last_name" => $last_name,
                                ":designation" => $designation,
                                ":email_address" => $email_address
                            ]);

                            $add_observer_info = $conn->prepare("INSERT INTO observers_info_tbl(id_number, phone_number, temporary_address, permanent_address, gender, marital_status)
                                                                VALUES(:id_number, :phone_number, :temp_address, :perm_address, :gender, :marital_status)");

                            $add_observer_info->execute([
                                ":id_number" => $id_number,
                                ":phone_number" => $phone_number,
                                ":temp_address" => $temporary_address,
                                ":perm_address" => $permanent_address,
                                ":gender" => $gender,
                                ":marital_status" => $status
                            ]);

                            send_user_creation_email($email_address, $full_name, "Observer", $id_number, $generated_password);

                            $_SESSION["query-status"] = [
                                "status" => "success",
                                "message" => "Obsever account created successfully!"
                            ];
                 
                            header("Location: ../../pages/admin/home.php?page=observers-accounts");
                            exit();
                        }

                        else {
                            $_SESSION["query-status"] = [
                                "status" => "danger",
                                "message" => "Invalid password! Please try again."
                            ];
                 
                            header("Location: ../../pages/admin/home.php?page=add-observer");
                            exit();
                        }
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Admin user not found! Please try again."
                        ];
             
                        header("Location: ../../pages/admin/home.php?page=add-observer");
                        exit();
                    }
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
     
                header("Location: ../../pages/admin/home.php?page=add-observer");
                exit();
            }
        }

        else {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid email address format! Please try again."
            ];
 
            header("Location: ../../pages/admin/home.php?page=add-observer");
            exit();
        }
    }

    // Update Observer's Designation
    else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update-designation"])) {
        $observer_user_id = htmlspecialchars(trim(base64_decode($_POST["observer-user-id"])));
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));

        $designation = htmlspecialchars($_POST["designation"]);
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        $encoded_id = base64_encode($observer_user_id);

        if (empty($observer_user_id) || empty($admin_id) || empty($designation) || empty($admin_password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
            exit();
        } else if (!in_array($designation, $allowed_designations)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid designation! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
            exit();
        } else {
            try {
                $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_admin_account->execute([":id_number" => $admin_id]);

                if ($check_admin_account->rowCount() === 1) {
                    $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                    if (password_verify($admin_password, $admin_credentials->admin_password)) {
                        $lock_account = $conn->prepare("UPDATE observers_credentials_tbl SET designation = :designation WHERE id_number = :observer_user_id");
                        $lock_account->execute([
                            ":designation" => $designation,
                            ":observer_user_id" => $observer_user_id
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "Designation updated successfully!!"
                        ];

                        header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                        exit();
                    } else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                        exit();
                    }
                } else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "Unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Archive Observer Account
    else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["archive-observer-account"])) {
        $observer_user_id = htmlspecialchars(trim(base64_decode($_POST["observer-user-id"])));
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        $encoded_id = base64_encode($observer_user_id);

        if (empty($observer_user_id) || empty($admin_id) || empty($admin_password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
            exit();
        } 
        
        else {
            try {
                $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_admin_account->execute([":id_number" => $admin_id]);

                if ($check_admin_account->rowCount() === 1) {
                    $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                    if (password_verify($admin_password, $admin_credentials->admin_password)) {
                        $archive_account = $conn->prepare("UPDATE observers_credentials_tbl SET is_archived = :is_archived WHERE id_number = :observer_user_id");
                        $archive_account->execute([
                            ":is_archived" => "Yes",
                            ":observer_user_id" => $observer_user_id
                        ]);

                        $delete_current_observation = $conn->prepare("DELETE FROM observations_tbl WHERE observer_id = :observer_id AND observe_status = :observe_status");
                        $delete_current_observation->execute([
                            ":observer_id" => $observer_user_id,
                            ":observe_status" => "Incomplete"
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "This account has been archived!"
                        ];

                        header("Location: ../../pages/admin/home.php?page=archived-observers-accounts");
                        exit();
                    } else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                        exit();
                    }
                } else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "Unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                exit();
            }
        }
    } 

    // Restore Observer's Account
    else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["restore-observer-account"])) {
        $observer_user_id = htmlspecialchars(trim(base64_decode($_POST["observer-user-id"])));

        if (empty($observer_user_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=archived-observers-accounts");
            exit();
        } 
        
        else {
            try {

                $restore_account = $conn->prepare("UPDATE observers_credentials_tbl SET is_archived = :is_archived WHERE id_number = :observer_user_id");
                $restore_account->execute([
                    ":is_archived" => "No",
                    ":observer_user_id" => $observer_user_id,
                ]);

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "This account has been restored!"
                ];

                header("Location: ../../pages/admin/home.php?page=observers-accounts");
                exit();
            } 
            
            catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "Unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=archived-observers-accounts");
                exit();
            }
        }
    }

    // Lock Observer Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["lock-observer-account"])) {
        $observer_user_id = htmlspecialchars(trim(base64_decode($_POST["observer-user-id"])));
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        $encoded_id = base64_encode($observer_user_id);

        if(empty($observer_user_id) || empty($admin_id) || empty($admin_password)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_admin_account->execute([":id_number" => $admin_id]);

                if($check_admin_account->rowCount() === 1) {
                    $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                    if(password_verify($admin_password, $admin_credentials->admin_password)) {
                        $lock_account = $conn->prepare("UPDATE observers_credentials_tbl SET locked_account = :locked_account WHERE id_number = :observer_user_id");
                        $lock_account->execute([
                            ":locked_account" => "Yes",
                            ":observer_user_id" => $observer_user_id
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "This account has been locked!"
                        ];

                        header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                        exit();
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                        exit();
                    }
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Unlock Observer Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["unlock-observer-account"])) {
        $observer_user_id = htmlspecialchars(trim(base64_decode($_POST["observer-user-id"])));
        $admin_id = htmlspecialchars(trim($_POST["admin-id"]));
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        $encoded_id = base64_encode($observer_user_id);

        if(empty($observer_user_id) || empty($admin_id) || empty($admin_password)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_admin_account->execute([":id_number" => $admin_id]);

                if($check_admin_account->rowCount() === 1) {
                    $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                    if(password_verify($admin_password, $admin_credentials->admin_password)) {
                        $lock_account = $conn->prepare("UPDATE observers_credentials_tbl SET locked_account = :locked_account WHERE id_number = :observer_user_id");
                        $lock_account->execute([
                            ":locked_account" => "No",
                            ":observer_user_id" => $observer_user_id
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "This account has been unlocked!"
                        ];

                        header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                        exit();
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                        exit();
                    }
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Reset Observer Password
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reset-observer-password"])) {
        $observer_user_id = htmlspecialchars(trim(base64_decode($_POST["observer-user-id"])));
        $id_number = htmlspecialchars(trim($_POST["id-number"]));
        $email_address = filter_var($_POST["email-address"], FILTER_SANITIZE_EMAIL);

        $encoded_id = base64_encode($observer_user_id);

        if(empty($observer_user_id) || empty($id_number) || empty($email_address)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
           exit();
        }
        
        else if(!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "Invalid email address format! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
           exit();
        }

        else if($observer_user_id !== $id_number) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "Invalid account! Please try again."
           ];

           header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_account = $conn->prepare("SELECT * FROM observers_credentials_tbl WHERE id_number = :id_number AND email_address = :email_address LIMIT 1");
                $check_account->execute([
                    ":id_number" => $id_number,
                    ":email_address" => $email_address
                ]);

                if($check_account->rowCount() === 1) {
                    $reset_password = $conn->prepare("UPDATE observers_credentials_tbl SET observer_password = :default_password, generated_password = :generated_password, two_factor_authentication = :2fa WHERE id_number = :id_number");
                    $reset_password->execute([
                        ":default_password" => password_hash($observer_user_id, PASSWORD_DEFAULT),
                        ":generated_password" => "Yes",
                        ":2fa" => "Disabled",
                        ":id_number" => $observer_user_id
                    ]);

                    $_SESSION["query-status"] = [
                        "status" => "success",
                        "message" => "Password has been reset successfully!"
                    ];

                    header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                    exit();
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Account not found! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/admin/home.php?page=observer-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Default
    else {
        header("Location: ../../index.php");
    }
    
?>