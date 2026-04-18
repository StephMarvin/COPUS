<?php
    require_once "../../config/conn.config.php";
    require_once "../../config/dropdowns.config.php";
    require_once "../../config/functions.config.php";
    require_once "../../config/mailer.config.php";

    // Add Teacher Account
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-new-teacher"])) {
        $dean_id = htmlspecialchars(trim($_POST["dean-id"]));
        $first_name = htmlspecialchars($_POST["first-name"]);
        $middle_name = htmlspecialchars($_POST["middle-name"]);
        $last_name = htmlspecialchars($_POST["last-name"]);
        $gender = htmlspecialchars($_POST["gender"]);
        $status = htmlspecialchars($_POST["marital-status"]);
        $employment_status = htmlspecialchars($_POST["employment-status"]);
        $teacher_rank = htmlspecialchars($_POST["teacher-rank"]);
        $department_id = htmlspecialchars(base64_decode($_POST["department-id"]));
        $id_number = htmlspecialchars(trim($_POST["id-number"]));
        $email_address = filter_var($_POST["email-address"], FILTER_SANITIZE_EMAIL);
        $phone_number = htmlspecialchars(trim($_POST["phone-number"]));
        $temporary_address = htmlspecialchars($_POST["temporary-address"]);
        $permanent_address = htmlspecialchars($_POST["permanent-address"]);
        $dean_password = htmlspecialchars(trim($_POST["dean-password"]));
        $confirmation = htmlspecialchars(trim($_POST["confirmation"]));
        
        // Backup Code
        // if(empty($dean_id) || empty($first_name) || empty($last_name) || empty($gender) || empty($status) || empty($employment_status) || empty($teacher_rank) || empty($department_id) || empty($id_number) || empty($email_address) || empty($phone_number) || empty($permanent_address) || empty($dean_password) || empty($confirmation)) { 
        //   $_SESSION["query-status"] = [
        //       "status" => "danger",
        //       "message" => "An unknown error occured! Please try again."
        //   ];

        //   header("Location: ../../pages/deans/home.php?page=add-teacher");
        //   exit();
        // }

        if(empty($dean_id) || empty($first_name) || empty($last_name) || empty($gender) || empty($status) || empty($employment_status) || empty($teacher_rank) || empty($department_id) || empty($id_number) || empty($email_address) || empty($dean_password) || empty($confirmation)) { 
           $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/deans/home.php?page=add-teacher");
           exit();
        }

        if(!in_array($gender, $allowed_gender)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid gender! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=add-teacher");
            exit();
        }

        if(!in_array($status, $allowed_status)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid status! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=add-teacher");
            exit();
        }

        if(!in_array($employment_status, $allowed_employment_status)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid employment status! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=add-teacher");
            exit();
        }

        if(!in_array($teacher_rank, $allowed_rank)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid teacher rank! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=add-teacher");
            exit();
        }

        if($confirmation !== "true") {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];
 
            header("Location: ../../pages/deans/home.php?page=add-teacher");
            exit();
        }

        if(filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            try {
                $check_id_number = $conn->prepare("SELECT * FROM teacher_credentials_tbl WHERE id_number = :id_number");
                $check_id_number->execute([":id_number" => $id_number]);

                $check_email_address = $conn->prepare("SELECT * FROM teacher_credentials_tbl WHERE email_address = :email_address");
                $check_email_address->execute([":email_address" => $email_address]);

                $check_department = $conn->prepare("SELECT * FROM departments_tbl WHERE department_id = :department_id AND department_status = :department_status");
                $check_department->execute([":department_id" => $department_id, ":department_status" => "Active"]);

                if($check_id_number->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "ID number already exists! Please try again."
                    ];
         
                    header("Location: ../../pages/deans/home.php?page=add-teacher");
                    exit();
                }

                else if($check_email_address->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Email address already exists! Please try again."
                    ];
         
                    header("Location: ../../pages/deans/home.php?page=add-teacher");
                    exit();
                }

                else if($check_department->rowCount() <= 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Department does not exist! Please try again."
                    ];
         
                    header("Location: ../../pages/deans/home.php?page=add-teacher");
                    exit();
                }

                else {
                    $check_dean_user = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                    $check_dean_user->execute([":id_number" => $dean_id]);

                    if($check_dean_user->rowCount() === 1) {
                        $get_dean_password = $check_dean_user->fetch(PDO::FETCH_OBJ);

                        if(password_verify($dean_password, $get_dean_password->dean_password)) {
                            $full_name = $first_name . " " . $last_name;
                            $generated_password = $id_number;

                            $get_department = $check_department->fetch(PDO::FETCH_OBJ);
                            $department = $get_department->department_name . " (" . $get_department->department_code . ") Department";

                            $new_teacher_credentials = $conn->prepare("INSERT INTO teacher_credentials_tbl(id_number, teacher_password, first_name, middle_name, last_name, email_address, department_id, employment_status, teacher_rank)
                                                                        VALUES(:id_number, :teacher_password, :first_name, :middle_name, :last_name, :email_address, :department_id, :employment_status, :teacher_rank)");

                            $new_teacher_credentials->execute([
                                ":id_number" => $id_number,
                                ":teacher_password" => password_hash($generated_password, PASSWORD_DEFAULT),
                                ":first_name" => $first_name,
                                ":middle_name" => $middle_name,
                                ":last_name" => $last_name,
                                ":email_address" => $email_address,
                                ":department_id" => $department_id,
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

                            send_user_creation_email($email_address, $full_name, "Teacher of $department", $id_number, $generated_password);

                            $_SESSION["query-status"] = [
                                "status" => "success",
                                "message" => "Teacher account created successfully!"
                            ];
                 
                            header("Location: ../../pages/deans/home.php?page=teachers-list");
                            exit();
                        }

                        else {
                            $_SESSION["query-status"] = [
                                "status" => "danger",
                                "message" => "Invalid password! Please try again."
                            ];
                 
                            header("Location: ../../pages/deans/home.php?page=add-teacher");
                            exit();
                        }
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Deans user not found! Please try again."
                        ];
             
                        header("Location: ../../pages/deans/home.php?page=add-teacher");
                        exit();
                    }
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
     
                header("Location: ../../pages/deans/home.php?page=add-teacher");
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
        $dean_id = htmlspecialchars(trim($_POST["dean-id"]));

        $employment_status = htmlspecialchars($_POST["employment-status"]);
        $teacher_rank = htmlspecialchars($_POST["teacher-rank"]);
        $dean_password = htmlspecialchars(trim($_POST["dean-password"]));

        $encoded_id = base64_encode($teacher_user_id);

        if (empty($teacher_user_id) || empty($dean_id) || empty($dean_password) || empty($employment_status) || empty($teacher_rank)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
            exit();
        } else if (!in_array($employment_status, $allowed_employment_status)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid employment status! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
            exit();
        } else if (!in_array($teacher_rank, $allowed_rank)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid teacher rank! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
            exit();
        } else {
            try {
                $check_deans_account = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_deans_account->execute([":id_number" => $dean_id]);

                if ($check_deans_account->rowCount() === 1) {
                    $deans_credentials = $check_deans_account->fetch(PDO::FETCH_OBJ);

                    if (password_verify($dean_password, $deans_credentials->dean_password)) {
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

                        header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
                        exit();
                    } else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
                        exit();
                    }
                } else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Dean user not found! Please try again."
                    ];

                    header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "Unknown error occured! Please try again."
                ];

                header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Lock Teacher Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["lock-teacher-account"])) {
        $teacher_user_id = htmlspecialchars(trim(base64_decode($_POST["teacher-user-id"])));
        $dean_id = htmlspecialchars(trim($_POST["dean-id"]));
        $dean_password = htmlspecialchars(trim($_POST["dean-password"]));

        $encoded_id = base64_encode($teacher_user_id);

        if(empty($teacher_user_id) || empty($dean_id) || empty($dean_password)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_deans_account = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_deans_account->execute([":id_number" => $dean_id]);

                if($check_deans_account->rowCount() === 1) {
                    $deans_credentials = $check_deans_account->fetch(PDO::FETCH_OBJ);

                    if(password_verify($dean_password, $deans_credentials->dean_password)) {
                        $lock_account = $conn->prepare("UPDATE teacher_credentials_tbl SET locked_account = :locked_account WHERE id_number = :teacher_user_id");
                        $lock_account->execute([
                            ":locked_account" => "Yes",
                            ":teacher_user_id" => $teacher_user_id
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "This account has been locked!"
                        ];

                        header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
                        exit();
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
                        exit();
                    }
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Unlock Teacher Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["unlock-teacher-account"])) {
        $teacher_user_id = htmlspecialchars(trim(base64_decode($_POST["teacher-user-id"])));
        $dean_id = htmlspecialchars(trim($_POST["dean-id"]));
        $dean_password = htmlspecialchars(trim($_POST["dean-password"]));

        $encoded_id = base64_encode($teacher_user_id);

        if(empty($teacher_user_id) || empty($dean_id) || empty($dean_password)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_deans_account = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_deans_account->execute([":id_number" => $dean_id]);

                if($check_deans_account->rowCount() === 1) {
                    $deans_credentials = $check_deans_account->fetch(PDO::FETCH_OBJ);

                    if(password_verify($dean_password, $deans_credentials->dean_password)) {
                        $lock_account = $conn->prepare("UPDATE teacher_credentials_tbl SET locked_account = :locked_account WHERE id_number = :teacher_user_id");
                        $lock_account->execute([
                            ":locked_account" => "No",
                            ":teacher_user_id" => $teacher_user_id
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "This account has been unlocked!"
                        ];

                        header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
                        exit();
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
                        exit();
                    }
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
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

           header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
           exit();
        }
        
        else if(!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "Invalid email address format! Please try again."
           ];

           header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
           exit();
        }

        else if($teacher_user_id !== $id_number) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "Invalid account! Please try again."
           ];

           header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
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

                    header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
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

                header("Location: ../../pages/deans/home.php?page=teacher-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Add Observers Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-new-observer"])) {
        $dean_id = htmlspecialchars(trim($_POST["dean-id"]));
        $first_name = htmlspecialchars($_POST["first-name"]);
        $middle_name = htmlspecialchars($_POST["middle-name"]);
        $last_name = htmlspecialchars($_POST["last-name"]);
        $designation = htmlspecialchars($_POST["designation"]);
        $department_id = htmlspecialchars(base64_decode($_POST["department-id"]));
        $gender = htmlspecialchars($_POST["gender"]);
        $status = htmlspecialchars($_POST["marital-status"]);
        $id_number = htmlspecialchars(trim($_POST["id-number"]));
        $email_address = filter_var($_POST["email-address"], FILTER_SANITIZE_EMAIL);
        $phone_number = htmlspecialchars(trim($_POST["phone-number"]));
        $temporary_address = htmlspecialchars($_POST["temporary-address"]);
        $permanent_address = htmlspecialchars($_POST["permanent-address"]);
        $dean_password = htmlspecialchars(trim($_POST["dean-password"]));
        $confirmation = htmlspecialchars(trim($_POST["confirmation"]));
        
        // Backup Code
        // if(empty($dean_id) || empty($first_name) || empty($last_name) || empty($gender) || empty($status) || empty($id_number) || empty($designation) || empty($email_address) || empty($department_id) || empty($phone_number) || empty($permanent_address) || empty($dean_password) || empty($confirmation)) { 
        //   $_SESSION["query-status"] = [
        //       "status" => "danger",
        //       "message" => "An unknown error occured! Please try again."
        //   ];

        //   header("Location: ../../pages/deans/home.php?page=add-observer");
        //   exit();
        // }

        if(empty($dean_id) || empty($first_name) || empty($last_name) || empty($gender) || empty($status) || empty($id_number) || empty($designation) || empty($email_address) || empty($department_id) || empty($dean_password) || empty($confirmation)) { 
           $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/deans/home.php?page=add-observer");
           exit();
        }

        else if(!in_array($designation, $allowed_designations)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid designation! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=add-observer");
           exit();
        }

        else if(!in_array($gender, $allowed_gender)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid gender! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=add-observer");
           exit();
        }

        if(!in_array($status, $allowed_status)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid status! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=add-observer");
           exit();
        }

        if($confirmation !== "true") {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];
 
            header("Location: ../../pages/deans/home.php?page=add-observer");
            exit();
        }

        if(filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            try {
                $check_id_number = $conn->prepare("SELECT * FROM observers_credentials_tbl WHERE id_number = :id_number");
                $check_id_number->execute([":id_number" => $id_number]);

                $check_email_address = $conn->prepare("SELECT * FROM observers_credentials_tbl WHERE email_address = :email_address");
                $check_email_address->execute([":email_address" => $email_address]);

                $check_department = $conn->prepare("SELECT * FROM departments_tbl WHERE department_id = :department_id AND department_status = :department_status");
                $check_department->execute([":department_id" => $department_id, ":department_status" => "Active"]);

                if($check_id_number->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "ID number already exists! Please try again."
                    ];
         
                    header("Location: ../../pages/deans/home.php?page=add-observer");
                    exit();
                }

                else if($check_email_address->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Email address already exists! Please try again."
                    ];
         
                    header("Location: ../../pages/deans/home.php?page=add-observer");
                    exit();
                }

                else if($check_department->rowCount() <= 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Department does not exist! Please try again."
                    ];
         
                    header("Location: ../../pages/deans/home.php?page=add-observer");
                    exit();
                }


                else {
                    $check_dean_user = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                    $check_dean_user->execute([":id_number" => $dean_id]);

                    if($check_dean_user->rowCount() === 1) {
                        $get_dean_password = $check_dean_user->fetch(PDO::FETCH_OBJ);

                        if(password_verify($dean_password, $get_dean_password->dean_password)) {
                            $full_name = $first_name . " " . $last_name;
                            $generated_password = $id_number;

                            $get_department = $check_department->fetch(PDO::FETCH_OBJ);
                            $department = $get_department->department_name . " (" . $get_department->department_code . ") Department";

                            $new_observer_credentials = $conn->prepare("INSERT INTO observers_credentials_tbl(id_number, observer_password, first_name, middle_name, last_name, designation, department_id, email_address)
                                                                        VALUES(:id_number, :observer_password, :first_name, :middle_name, :last_name, :designation, :department_id, :email_address)");

                            $new_observer_credentials->execute([
                                ":id_number" => $id_number,
                                ":observer_password" => password_hash($generated_password, PASSWORD_DEFAULT),
                                ":first_name" => $first_name,
                                ":middle_name" => $middle_name,
                                ":last_name" => $last_name,
                                ":designation" => $designation,
                                ":department_id" => $department_id,
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

                            send_user_creation_email($email_address, $full_name, "Observer - $designation of $department", $id_number, $generated_password);

                            $_SESSION["query-status"] = [
                                "status" => "success",
                                "message" => "Obsever account created successfully!"
                            ];
                 
                            header("Location: ../../pages/deans/home.php?page=observers-list");
                            exit();
                        }

                        else {
                            $_SESSION["query-status"] = [
                                "status" => "danger",
                                "message" => "Invalid password! Please try again."
                            ];
                 
                            header("Location: ../../pages/deans/home.php?page=add-observer");
                            exit();
                        }
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Admin user not found! Please try again."
                        ];
             
                        header("Location: ../../pages/deans/home.php?page=add-observer");
                        exit();
                    }
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
     
                header("Location: ../../pages/deans/home.php?page=add-observer");
                exit();
            }
        }

        else {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid email address format! Please try again."
            ];
 
            header("Location: ../../pages/deans/home.php?page=add-observer");
            exit();
        }
    }

    // Update Observer's Designation
    else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update-designation"])) {
        $observer_user_id = htmlspecialchars(trim(base64_decode($_POST["observer-user-id"])));
        $dean_id = htmlspecialchars(trim($_POST["dean-id"]));

        $designation = htmlspecialchars($_POST["designation"]);
        $dean_password = htmlspecialchars(trim($_POST["dean-password"]));

        $encoded_id = base64_encode($observer_user_id);

        if (empty($observer_user_id) || empty($dean_id) || empty($designation) || empty($dean_password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
            exit();
        } else if (!in_array($designation, $allowed_designations)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid designation! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
            exit();
        } else {
            try {
                $check_deans_account = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_deans_account->execute([":id_number" => $dean_id]);

                if ($check_deans_account->rowCount() === 1) {
                    $deans_credentials = $check_deans_account->fetch(PDO::FETCH_OBJ);

                    if (password_verify($dean_password, $deans_credentials->dean_password)) {
                        $update_designation = $conn->prepare("UPDATE observers_credentials_tbl SET designation = :designation WHERE id_number = :observer_user_id");
                        $update_designation->execute([
                            ":designation" => $designation,
                            ":observer_user_id" => $observer_user_id
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "Designation updated successfully!!"
                        ];

                        header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
                        exit();
                    } else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
                        exit();
                    }
                } else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "Unknown error occured! Please try again."
                ];

                header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Lock Observer Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["lock-observer-account"])) {
        $observer_user_id = htmlspecialchars(trim(base64_decode($_POST["observer-user-id"])));
        $dean_id = htmlspecialchars(trim($_POST["dean-id"]));
        $dean_password = htmlspecialchars(trim($_POST["dean-password"]));

        $encoded_id = base64_encode($observer_user_id);

        if(empty($observer_user_id) || empty($dean_id) || empty($dean_password)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_deans_account = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_deans_account->execute([":id_number" => $dean_id]);

                if($check_deans_account->rowCount() === 1) {
                    $deans_credentials = $check_deans_account->fetch(PDO::FETCH_OBJ);

                    if(password_verify($dean_password, $deans_credentials->dean_password)) {
                        $lock_account = $conn->prepare("UPDATE observers_credentials_tbl SET locked_account = :locked_account WHERE id_number = :observer_user_id");
                        $lock_account->execute([
                            ":locked_account" => "Yes",
                            ":observer_user_id" => $observer_user_id
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "This account has been locked!"
                        ];

                        header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
                        exit();
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
                        exit();
                    }
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Unlock Observer Account
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["unlock-observer-account"])) {
        $observer_user_id = htmlspecialchars(trim(base64_decode($_POST["observer-user-id"])));
        $dean_id = htmlspecialchars(trim($_POST["dean-id"]));
        $dean_password = htmlspecialchars(trim($_POST["dean-password"]));

        $encoded_id = base64_encode($observer_user_id);

        if(empty($observer_user_id) || empty($dean_id) || empty($dean_password)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "An unknown error occured! Please try again."
           ];

           header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
           exit();
        }

        else {
            try {
                $check_deans_account = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_deans_account->execute([":id_number" => $dean_id]);

                if($check_deans_account->rowCount() === 1) {
                    $deans_credentials = $check_deans_account->fetch(PDO::FETCH_OBJ);

                    if(password_verify($dean_password, $deans_credentials->dean_password)) {
                        $lock_account = $conn->prepare("UPDATE observers_credentials_tbl SET locked_account = :locked_account WHERE id_number = :observer_user_id");
                        $lock_account->execute([
                            ":locked_account" => "No",
                            ":observer_user_id" => $observer_user_id
                        ]);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "This account has been unlocked!"
                        ];

                        header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
                        exit();
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
                        exit();
                    }
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Admin user not found! Please try again."
                    ];

                    header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
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

           header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
           exit();
        }
        
        else if(!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "Invalid email address format! Please try again."
           ];

           header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
           exit();
        }

        else if($observer_user_id !== $id_number) {
            $_SESSION["query-status"] = [
               "status" => "danger",
               "message" => "Invalid account! Please try again."
           ];

           header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
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

                    header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
                    exit();
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Account not found! Please try again."
                    ];

                    header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Unknown error occured! Please try again."
                    ];

                header("Location: ../../pages/deans/home.php?page=observer-details&id-number=$encoded_id");
                exit();
            }
        }
    }

    // Default
    else {
        header("Location: ../../index.php");
    }
    
?>