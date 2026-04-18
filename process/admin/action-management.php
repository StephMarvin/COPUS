<?php
    require_once "../../config/conn.config.php";

    // Add/Modify Teacher Actions
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-new-teacher-action"])) {
        $id_number = htmlspecialchars(trim($_POST["admin-id"]));
        $action_code = htmlspecialchars($_POST["action-code"]);
        $action_name = htmlspecialchars($_POST["action-name"]);
        $is_active_learning = htmlspecialchars(trim($_POST["is-active-learning"]));
        $admin_password = htmlspecialchars($_POST["admin-password"]);

        if(empty($id_number) || empty($action_code) || empty($action_name) || empty($is_active_learning) || empty($admin_password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=teacher-actions");
            exit();
        }

        else if(!in_array($is_active_learning, ["Yes", "No"])) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid active learning type! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=teacher-actions");
            exit();
        }

        else {
            try {
                $check_action_codes = $conn->prepare("SELECT * FROM teacher_actions_tbl WHERE action_code = :action_code");
                $check_action_codes->execute([":action_code" => $action_code]);

                $check_action_names = $conn->prepare("SELECT * FROM teacher_actions_tbl WHERE action_name = :action_name");
                $check_action_names->execute([":action_name" => $action_name]);

                if($check_action_codes->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Teacher action already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=teacher-actions");
                    exit();
                }

                else if($check_action_names->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Teacher action already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=teacher-actions");
                    exit();
                }

                else {
                    $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                    $check_admin_account->execute([":id_number" => $id_number]);

                    if($check_admin_account->rowCount() === 1) {
                        $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                        if(password_verify($admin_password, $admin_credentials->admin_password)) {
                            $add_teacher_action = $conn->prepare("INSERT INTO teacher_actions_tbl(action_code, action_name, is_active_learning)
                                                                VALUES(:action_code, :action_name, :is_active_learning)
                                                                ");

                            $add_teacher_action->execute([
                                ":action_code" => $action_code,
                                ":action_name" => $action_name,
                                ":is_active_learning" => $is_active_learning
                            ]);

                            $_SESSION["query-status"] = [
                                "status" => "success",
                                "message" => "Teacher action added successfully!"
                            ];
                
                            header("Location: ../../pages/admin/home.php?page=teacher-actions");
                            exit();
                        }

                        else {
                            $_SESSION["query-status"] = [
                                "status" => "danger",
                                "message" => "Invalid password! Please try again."
                            ];
                
                            header("Location: ../../pages/admin/home.php?page=teacher-actions");
                            exit();
                        }

                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Admin user not found!"
                        ];
            
                        header("Location: ../../pages/admin/home.php?page=teacher-actions");
                        exit();
                    }
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
    
                header("Location: ../../pages/admin/home.php?page=teacher-actions");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update-teacher-action"])) {
        $action_id = htmlspecialchars(trim(base64_decode($_POST["action-id"])));
        $action_code = htmlspecialchars($_POST["action-code"]);
        $action_name = htmlspecialchars($_POST["action-name"]);
        $is_active_learning = htmlspecialchars(trim($_POST["is-active-learning"]));

        if(empty($action_id) || empty($action_code) || empty($action_name) || empty($is_active_learning)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=teacher-actions");
            exit();
        }

        else if(!in_array($is_active_learning, ["Yes", "No"])) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid active learning type! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=teacher-actions");
            exit();
        }

        else {
            try {
                $check_action_codes = $conn->prepare("SELECT * FROM teacher_actions_tbl WHERE action_code = :action_code AND action_id != :action_id");
                $check_action_codes->execute([":action_code" => $action_code, ":action_id" => $action_id]);

                $check_action_names = $conn->prepare("SELECT * FROM teacher_actions_tbl WHERE action_name = :action_name AND action_id != :action_id");
                $check_action_names->execute([":action_name" => $action_name, ":action_id" => $action_id]);

                if($check_action_codes->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Teacher action already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=teacher-actions");
                    exit();
                }

                else if($check_action_names->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Teacher action already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=teacher-actions");
                    exit();
                }

                else {
                    $update_teacher_action = $conn->prepare("UPDATE teacher_actions_tbl
                                                            SET action_code = :action_code,
                                                            action_name = :action_name,
                                                            is_active_learning = :is_active_learning
                                                            WHERE action_id = :action_id
                                                            ");

                    $update_teacher_action->execute([
                        ":action_code" => $action_code,
                        ":action_name" => $action_name,
                        ":is_active_learning" => $is_active_learning,
                        ":action_id" => $action_id
                    ]);

                    $_SESSION["query-status"] = [
                        "status" => "success",
                        "message" => "Teacher action updated successfully!"
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=teacher-actions");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
    
                header("Location: ../../pages/admin/home.php?page=teacher-actions");
                exit();
            }
        }
    }

    // Add/Modify Student Actions
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-new-student-action"])) {
        $id_number = htmlspecialchars(trim($_POST["admin-id"]));
        $action_code = htmlspecialchars($_POST["action-code"]);
        $action_name = htmlspecialchars($_POST["action-name"]);
        $is_active_learning = htmlspecialchars(trim($_POST["is-active-learning"]));
        $admin_password = htmlspecialchars($_POST["admin-password"]);

        if(empty($id_number) || empty($action_code) || empty($action_name) || empty($is_active_learning) || empty($admin_password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=student-actions");
            exit();
        }

        else if(!in_array($is_active_learning, ["Yes", "No"])) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid active learning type! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=student-actions");
            exit();
        }

        else {
            try {
                $check_action_codes = $conn->prepare("SELECT * FROM student_actions_tbl WHERE action_code = :action_code");
                $check_action_codes->execute([":action_code" => $action_code]);

                $check_action_names = $conn->prepare("SELECT * FROM student_actions_tbl WHERE action_name = :action_name");
                $check_action_names->execute([":action_name" => $action_name]);

                if($check_action_codes->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Student action already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=student-actions");
                    exit();
                }

                else if($check_action_names->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Student action already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=student-actions");
                    exit();
                }

                else {
                    $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                    $check_admin_account->execute([":id_number" => $id_number]);

                    if($check_admin_account->rowCount() === 1) {
                        $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                        if(password_verify($admin_password, $admin_credentials->admin_password)) {
                            $add_student_action = $conn->prepare("INSERT INTO student_actions_tbl(action_code, action_name, is_active_learning)
                                                                VALUES(:action_code, :action_name, :is_active_learning)
                                                                ");

                            $add_student_action->execute([
                                ":action_code" => $action_code,
                                ":action_name" => $action_name,
                                ":is_active_learning" => $is_active_learning
                            ]);

                            $_SESSION["query-status"] = [
                                "status" => "success",
                                "message" => "Teacher action added successfully!"
                            ];
                
                            header("Location: ../../pages/admin/home.php?page=student-actions");
                            exit();
                        }

                        else {
                            $_SESSION["query-status"] = [
                                "status" => "danger",
                                "message" => "Invalid password! Please try again."
                            ];
                
                            header("Location: ../../pages/admin/home.php?page=student-actions");
                            exit();
                        }

                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Admin user not found!"
                        ];
            
                        header("Location: ../../pages/admin/home.php?page=student-actions");
                        exit();
                    }
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
    
                header("Location: ../../pages/admin/home.php?page=student-actions");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update-student-action"])) {
        $action_id = htmlspecialchars(trim(base64_decode($_POST["action-id"])));
        $action_code = htmlspecialchars($_POST["action-code"]);
        $action_name = htmlspecialchars($_POST["action-name"]);
        $is_active_learning = htmlspecialchars(trim($_POST["is-active-learning"]));

        if(empty($action_id) || empty($action_code) || empty($action_name) || empty($is_active_learning)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=student-actions");
            exit();
        }

        else if(!in_array($is_active_learning, ["Yes", "No"])) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid active learning type! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=student-actions");
            exit();
        }

        else {
            try {
                $check_action_codes = $conn->prepare("SELECT * FROM student_actions_tbl WHERE action_code = :action_code AND action_id != :action_id");
                $check_action_codes->execute([":action_code" => $action_code, ":action_id" => $action_id]);

                $check_action_names = $conn->prepare("SELECT * FROM student_actions_tbl WHERE action_name = :action_name AND action_id != :action_id");
                $check_action_names->execute([":action_name" => $action_name, ":action_id" => $action_id]);

                if($check_action_codes->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Student action already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=student-actions");
                    exit();
                }

                else if($check_action_names->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Student action already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=student-actions");
                    exit();
                }

                else {
                    $update_student_action = $conn->prepare("UPDATE student_actions_tbl
                                                            SET action_code = :action_code,
                                                            action_name = :action_name,
                                                            is_active_learning = :is_active_learning
                                                            WHERE action_id = :action_id
                                                            ");

                    $update_student_action->execute([
                        ":action_code" => $action_code,
                        ":action_name" => $action_name,
                        ":is_active_learning" => $is_active_learning,
                        ":action_id" => $action_id
                    ]);

                    $_SESSION["query-status"] = [
                        "status" => "success",
                        "message" => "Student action updated successfully!"
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=student-actions");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
    
                header("Location: ../../pages/admin/home.php?page=student-actions");
                exit();
            }
        }
    }

    // Set Active/Inactive Teacher Actions
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["set-teacher-action-inactive"])) {
        $action_id = htmlspecialchars(trim(base64_decode($_POST["action-id"])));

        if(empty($action_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=teacher-actions");
            exit();
        }

        else {
            try {
                $update_action_status = $conn->prepare("UPDATE teacher_actions_tbl
                                                        SET action_status = :action_status
                                                        WHERE action_id = :action_id
                                                        ");
                $update_action_status->execute([
                    ":action_status" => "Inactive",
                    ":action_id" => $action_id
                ]);

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "Teacher action updated successfully!"
                ];

                header("Location: ../../pages/admin/home.php?page=teacher-actions");
                exit();
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=teacher-actions");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["set-teacher-action-active"])) {
        $action_id = htmlspecialchars(trim(base64_decode($_POST["action-id"])));

        if(empty($action_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=teacher-actions");
            exit();
        }

        else {
            try {
                $update_action_status = $conn->prepare("UPDATE teacher_actions_tbl
                                                        SET action_status = :action_status
                                                        WHERE action_id = :action_id
                                                        ");
                $update_action_status->execute([
                    ":action_status" => "Active",
                    ":action_id" => $action_id
                ]);

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "Teacher action updated successfully!"
                ];

                header("Location: ../../pages/admin/home.php?page=teacher-actions");
                exit();
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=teacher-actions");
                exit();
            }
        }
    }

    // Set Active/Inactive Student Actions
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["set-student-action-inactive"])) {
        $action_id = htmlspecialchars(trim(base64_decode($_POST["action-id"])));

        if(empty($action_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=student-actions");
            exit();
        }

        else {
            try {
                $update_action_status = $conn->prepare("UPDATE student_actions_tbl
                                                        SET action_status = :action_status
                                                        WHERE action_id = :action_id
                                                        ");
                $update_action_status->execute([
                    ":action_status" => "Inactive",
                    ":action_id" => $action_id
                ]);

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "Student action updated successfully!"
                ];

                header("Location: ../../pages/admin/home.php?page=student-actions");
                exit();
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=student-actions");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["set-student-action-active"])) {
        $action_id = htmlspecialchars(trim(base64_decode($_POST["action-id"])));

        if(empty($action_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=student-actions");
            exit();
        }

        else {
            try {
                $update_action_status = $conn->prepare("UPDATE student_actions_tbl
                                                        SET action_status = :action_status
                                                        WHERE action_id = :action_id
                                                        ");
                $update_action_status->execute([
                    ":action_status" => "Active",
                    ":action_id" => $action_id
                ]);

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "Student action updated successfully!"
                ];

                header("Location: ../../pages/admin/home.php?page=student-actions");
                exit();
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=student-actions");
                exit();
            }
        }
    }

    // Default
    else {
        header("Location: ../../index.php");
    }
?>