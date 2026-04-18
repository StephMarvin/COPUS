<?php

    require_once "../../config/conn.config.php";
    require_once "../../config/validations.config.php";
    require_once "../../config/dropdowns.config.php";

    // Add/Set Academic Year
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-academic-year"])) {

        $current_year = date("Y");
        
        $id_number = htmlspecialchars(trim($_POST["admin-id"]));
        $academic_year = htmlspecialchars(trim($_POST["academic-year"]));
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        if(empty($id_number) || empty($academic_year) || empty($admin_password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];
 
            header("Location: ../../pages/admin/home.php?page=academic-year");
            exit();
        }

        else {
            if(preg_match($year_pattern, $academic_year, $years)) {
                $start_year = $years[1];
                $end_year = $years[2];

                if($start_year !== $current_year) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "The start year must be the current year! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=academic-year");
                    exit();
                }

                if($start_year >= $end_year) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Invalid year format! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=academic-year");
                    exit();
                }

                else if($end_year - $start_year !== 1) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Invalid year format! Please try again."
                    ];

                    header("Location: ../../pages/admin/home.php?page=academic-year");
                    exit();
                }

                else {
                    try {
                        $check_admin_user = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                        $check_admin_user->execute([":id_number" => $id_number]);

                        if($check_admin_user->rowCount() === 1) {
                            $get_admin_password = $check_admin_user->fetch(PDO::FETCH_OBJ);

                            if(password_verify($admin_password, $get_admin_password->admin_password)) {
                                $add_new_academic_year = $conn->prepare("INSERT INTO academic_years_tbl(academic_year) VALUES(:academic_year)");
                                $add_new_academic_year->execute([":academic_year" => $academic_year]);
                                $academic_year_id = $conn->lastInsertId();

                                foreach($allowed_semesters as $semester) {
                                    $add_semester = $conn->prepare("INSERT INTO semesters_tbl(academic_year_id, semester) VALUES(:academic_id, :semester)");
                                    $add_semester->execute([":academic_id" => $academic_year_id, ":semester" => $semester]);
                                }

                                $_SESSION["query-status"] = [
                                    "status" => "success",
                                    "message" => "Academic year created successfully!"
                                ];
                
                                header("Location: ../../pages/admin/home.php?page=academic-year");
                                exit();
                            }

                            else {
                                $_SESSION["query-status"] = [
                                    "status" => "danger",
                                    "message" => "Invalid password! Please try again."
                                ];
                
                                header("Location: ../../pages/admin/home.php?page=academic-year");
                                exit();
                            }

                        }

                        else {
                            $_SESSION["query-status"] = [
                                "status" => "danger",
                                "message" => "Admin user not found! Please try again."
                            ];
            
                            header("Location: ../../pages/admin/home.php?page=academic-year");
                            exit();
                        }
                    }

                    catch(PDOException $e) {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "An unknown error occured! Please try again." . $e->getMessage()
                        ];
        
                        header("Location: ../../pages/admin/home.php?page=academic-year");
                        exit();
                    }                    
                }     
            }

            else {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=academic-year");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["set-active-year"])) {
        $academic_year_id = htmlspecialchars(trim(base64_decode($_POST["academic-year-id"])));

        if(empty($academic_year_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=academic-year");
            exit();
        }

        else {
            try {
                $conn->beginTransaction();

                $set_active_year = $conn->prepare("UPDATE academic_years_tbl SET status = :status WHERE academic_year_id = :academic_year");
                $set_active_year->execute([
                    ":status" => "Active",
                    ":academic_year" => $academic_year_id
                ]);

                $set_inactive_year = $conn->prepare("UPDATE academic_years_tbl SET status = :status WHERE academic_year_id != :academic_year");
                $set_inactive_year->execute([
                    ":status" => "Inactive",
                    ":academic_year" => $academic_year_id
                ]);

                $set_inactive_semesters = $conn->prepare("UPDATE semesters_tbl SET semester_status = :semester_status");
                $set_inactive_semesters->execute([
                    ":semester_status" => "Inactive"
                ]);

                $set_active_semester = $conn->prepare("UPDATE semesters_tbl SET semester_status = :semester_status WHERE academic_year_id = :academic_year AND semester = :semester");
                $set_active_semester->execute([
                    ":semester_status" => "Active",
                    ":academic_year" => $academic_year_id,
                    ":semester" => "1st Semester"
                ]);

                $conn->commit();

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "Academic year set successfully!"
                ];

                header("Location: ../../pages/admin/home.php?page=academic-year");
                exit();
            }

            catch(PDOException $e) {
                $conn->rollBack();
                
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured!1 Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=academic-year");
                exit();
            }
        }
    }

    // Set Semester
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["set-semester-active"])) {
        $academic_year_id = htmlspecialchars(trim(base64_decode($_POST["academic-year-id"])));
        $encoded_academic_year_id = base64_encode($academic_year_id);

        $semester_id = htmlspecialchars(trim(base64_decode($_POST["semester-id"])));

        if(empty($academic_year_id) || empty($semester_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=academic-year-details&academic-year-id=$encoded_academic_year_id");
            exit();
        }

        else {
            try {
                $set_semester_status = $conn->prepare("UPDATE semesters_tbl SET semester_status = :semester_status WHERE semester_id = :semester_id");
                $set_semester_status->execute([
                    ":semester_status" => "Active",
                    ":semester_id" => $semester_id
                ]);

                $set_inactive_semester = $conn->prepare("UPDATE semesters_tbl SET semester_status = :semester_status WHERE semester_id != :semester_id");
                $set_inactive_semester->execute([
                    ":semester_status" => "Inactive",
                    ":semester_id" => $semester_id
                ]);

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "Semester set to active successfully!"
                ];
    
                header("Location: ../../pages/admin/home.php?page=academic-year-details&academic-year-id=$encoded_academic_year_id");
                exit();
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
    
                header("Location: ../../pages/admin/home.php?page=academic-year-details&academic-year-id=$encoded_academic_year_id");
                exit();
            }
        }
    }

    // Add/Modify Departments
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-new-department"])) {

        $id_number = htmlspecialchars(trim($_POST["admin-id"]));

        $department_code = htmlspecialchars($_POST["department-code"]);
        $department_name = htmlspecialchars($_POST["department-name"]);
        
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        if(empty($id_number) || empty($department_code) || empty($department_name) || empty($admin_password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=departments");
            exit();
        }

        else {
            try {

                $check_department_codes = $conn->prepare("SELECT * FROM departments_tbl WHERE department_code = :department_code");
                $check_department_codes->execute([":department_code" => $department_code]);

                $check_department_names = $conn->prepare("SELECT * FROM departments_tbl WHERE department_name = :department_name");
                $check_department_names->execute([":department_name" => $department_name]);

                if($check_department_codes->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "This department is already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=departments");
                    exit();
                }

                else if($check_department_names->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "This department is already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=departments");
                    exit();
                }

                else {
                    $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                    $check_admin_account->execute([":id_number" => $id_number]);

                    if($check_admin_account->rowCount() === 1) {
                        $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                        if(password_verify($admin_password, $admin_credentials->admin_password)) {
                            $insert_Department = $conn->prepare("INSERT INTO departments_tbl(department_code, department_name)
                                                            VALUES(:department_code, :department_name)");

                            $insert_Department->execute([
                                ":department_code" => strtoupper($department_code),
                                ":department_name" => $department_name,
                            ]);

                            $_SESSION["query-status"] = [
                                "status" => "success",
                                "message" => "Department added successfully!"
                            ];
                
                            header("Location: ../../pages/admin/home.php?page=departments");
                            exit();
                        }

                        else {
                            $_SESSION["query-status"] = [
                                "status" => "danger",
                                "message" => "Invalid password! Please try again."
                            ];
                
                            header("Location: ../../pages/admin/home.php?page=departments");
                            exit();
                        }
                    }
                    
                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Admin user not found!"
                        ];
            
                        header("Location: ../../pages/admin/home.php?page=departments");
                        exit();
                    }
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
    
                header("Location: ../../pages/admin/home.php?page=departments");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update-department"])) {

        $department_id = htmlspecialchars(trim(base64_decode($_POST["department-id"])));
        $department_code = htmlspecialchars($_POST["department-code"]);
        $department_name = htmlspecialchars($_POST["department-name"]);
    
        if(empty($department_id) || empty($department_code) || empty($department_name)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=departments");
            exit();
        }


        else {
            try {
                $check_department_codes = $conn->prepare("SELECT * FROM departments_tbl WHERE department_code = :department_code AND department_id != :department_id");
                $check_department_codes->execute([":department_code" => $department_code, ":department_id" => $department_id]);

                $check_department_names = $conn->prepare("SELECT * FROM departments_tbl WHERE department_name = :department_name AND department_id != :department_id");
                $check_department_names->execute([":department_name" => $department_name, ":department_id" => $department_id]);

                if($check_department_codes->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "This department is already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=departments");
                    exit();
                }

                else if($check_department_names->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "This department is already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=departments");
                    exit();
                }

                else {
                    $update_department = $conn->prepare("UPDATE departments_tbl SET
                                                        department_code = :department_code,
                                                        department_name = :department_name
                                                        WHERE department_id = :department_id
                                                        ");

                    $update_department->execute([
                        ":department_code" => $department_code,
                        ":department_name" => $department_name,
                        ":department_id" => $department_id
                    ]);

                    $_SESSION["query-status"] = [
                        "status" => "success",
                        "message" => "Department updated successfully!"
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=departments");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
    
                header("Location: ../../pages/admin/home.php?page=departments");
                exit();
            }
        }
    }

    // Set Active/Inactive Subject
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["set-department-inactive"])) {

        $department_id = htmlspecialchars(trim(base64_decode($_POST["department-id"])));

        if(empty($department_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=departments");
            exit();
        }

        else {
            try {
                $update_subject_status = $conn->prepare("UPDATE departments_tbl
                                                        SET department_status = :department_status
                                                        WHERE department_id = :department_id
                                                        ");
                $update_subject_status->execute([
                    ":department_status" => "Inactive",
                    ":department_id" => $department_id
                ]);

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "Department updated successfully!"
                ];

                header("Location: ../../pages/admin/home.php?page=departments");
                exit();
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=departments");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["set-department-active"])) {

        $department_id = htmlspecialchars(trim(base64_decode($_POST["department-id"])));

        if(empty($department_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=departments");
            exit();
        }

        else {
            try {
                $update_subject_status = $conn->prepare("UPDATE departments_tbl
                                                        SET department_status = :department_status
                                                        WHERE department_id = :department_id
                                                        ");
                $update_subject_status->execute([
                    ":department_status" => "Active",
                    ":department_id" => $department_id
                ]);

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "Department updated successfully!"
                ];

                header("Location: ../../pages/admin/home.php?page=departments");
                exit();
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=departments");
                exit();
            }
        }
    }

    // ============================= Subjects ==============================================
    // Add/Modify Subjects
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-new-subject"])) {
        $id_number = htmlspecialchars(trim($_POST["admin-id"]));
        $subject_code = htmlspecialchars($_POST["subject-code"]);
        $subject_name = htmlspecialchars($_POST["subject-name"]);
        $units = htmlspecialchars(trim($_POST["subject-units"]));
        $semester = htmlspecialchars($_POST["semester"]);
        $admin_password = htmlspecialchars(trim($_POST["admin-password"]));

        if(empty($id_number) || empty($subject_code) || empty($subject_name) || empty($units) || empty($admin_password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=subjects");
            exit();
        }

        else if(!in_array($semester, $allowed_semesters)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid semester! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=subjects");
            exit();
        }

        else if(!ctype_digit($units) || (int)$units > 10 || (int)$units < 1) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Subject units must be a number from 1 to 10 only! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=subjects");
            exit();
        }

        else {
            try {

                $check_subject_codes = $conn->prepare("SELECT * FROM subjects_tbl WHERE subject_code = :subject_code");
                $check_subject_codes->execute([":subject_code" => $subject_code]);

                $check_subject_names = $conn->prepare("SELECT * FROM subjects_tbl WHERE subject_name = :subject_name");
                $check_subject_names->execute([":subject_name" => $subject_name]);

                if($check_subject_codes->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "This subject is already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=subjects");
                    exit();
                }

                else if($check_subject_names->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "This subject is already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=subjects");
                    exit();
                }

                else {
                    $check_admin_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                    $check_admin_account->execute([":id_number" => $id_number]);

                    if($check_admin_account->rowCount() === 1) {
                        $admin_credentials = $check_admin_account->fetch(PDO::FETCH_OBJ);

                        if(password_verify($admin_password, $admin_credentials->admin_password)) {
                            $insert_subject = $conn->prepare("INSERT INTO subjects_tbl(subject_code, subject_name, subject_units, semester)
                                                            VALUES(:subject_code, :subject_name, :subject_units, :semester)");

                            $insert_subject->execute([
                                ":subject_code" => $subject_code,
                                ":subject_name" => $subject_name,
                                ":subject_units" => $units,
                                ":semester" => $semester
                            ]);

                            $_SESSION["query-status"] = [
                                "status" => "success",
                                "message" => "Subject added successfully!"
                            ];
                
                            header("Location: ../../pages/admin/home.php?page=subjects");
                            exit();
                        }

                        else {
                            $_SESSION["query-status"] = [
                                "status" => "danger",
                                "message" => "Invalid password! Please try again."
                            ];
                
                            header("Location: ../../pages/admin/home.php?page=subjects");
                            exit();
                        }
                    }
                    
                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Admin user not found!"
                        ];
            
                        header("Location: ../../pages/admin/home.php?page=subjects");
                        exit();
                    }
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
    
                header("Location: ../../pages/admin/home.php?page=subjects");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update-subject"])) {
        $subject_id = htmlspecialchars(trim(base64_decode($_POST["subject-id"])));
        $subject_code = htmlspecialchars($_POST["subject-code"]);
        $subject_name = htmlspecialchars($_POST["subject-name"]);
        $units = htmlspecialchars(trim($_POST["units"]));
        $semester = htmlspecialchars($_POST["semester"]);

        if(empty($subject_id) || empty($subject_code) || empty($subject_name) || empty($units)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=subjects");
            exit();
        }

        else if(!in_array($semester, $allowed_semesters)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid semester! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=subjects");
            exit();
        }

        else if(!ctype_digit($units) || (int)$units > 10 || (int)$units < 1) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Subject units must be a number from 1 to 10 only! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=subjects");
            exit();
        }

        else {
            try {
                $check_subject_codes = $conn->prepare("SELECT * FROM subjects_tbl WHERE subject_code = :subject_code AND subject_id != :subject_id");
                $check_subject_codes->execute([":subject_code" => $subject_code, ":subject_id" => $subject_id]);

                $check_subject_names = $conn->prepare("SELECT * FROM subjects_tbl WHERE subject_name = :subject_name AND subject_id != :subject_id");
                $check_subject_names->execute([":subject_name" => $subject_name, ":subject_id" => $subject_id]);

                if($check_subject_codes->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "This subject is already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=subjects");
                    exit();
                }

                else if($check_subject_names->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "This subject is already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=subjects");
                    exit();
                }

                else {
                    $update_subject = $conn->prepare("UPDATE subjects_tbl SET
                                                        subject_code = :subject_code,
                                                        subject_name = :subject_name,
                                                        subject_units = :subject_units,
                                                        semester = :semester
                                                        WHERE subject_id = :subject_id
                                                        ");

                    $update_subject->execute([
                        ":subject_code" => $subject_code,
                        ":subject_name" => $subject_name,
                        ":subject_units" => $units,
                        ":semester" => $semester,
                        ":subject_id" => $subject_id
                    ]);

                    $_SESSION["query-status"] = [
                        "status" => "success",
                        "message" => "Subject updated successfully!"
                    ];
        
                    header("Location: ../../pages/admin/home.php?page=subjects");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
    
                header("Location: ../../pages/admin/home.php?page=subjects");
                exit();
            }
        }
    }

    // Set Active/Inactive Subject
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["set-subject-inactive"])) {
        $subject_id = htmlspecialchars(trim(base64_decode($_POST["subject-id"])));

        if(empty($subject_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=subjects");
            exit();
        }

        else {
            try {
                $update_subject_status = $conn->prepare("UPDATE subjects_tbl
                                                        SET subject_status = :subject_status
                                                        WHERE subject_id = :subject_id
                                                        ");
                $update_subject_status->execute([
                    ":subject_status" => "Inactive",
                    ":subject_id" => $subject_id
                ]);

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "Subject updated successfully!"
                ];

                header("Location: ../../pages/admin/home.php?page=subjects");
                exit();
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=subjects");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["set-subject-active"])) {
        $subject_id = htmlspecialchars(trim(base64_decode($_POST["subject-id"])));

        if(empty($subject_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=subjects");
            exit();
        }

        else {
            try {
                $update_subject_status = $conn->prepare("UPDATE subjects_tbl
                                                        SET subject_status = :subject_status
                                                        WHERE subject_id = :subject_id
                                                        ");
                $update_subject_status->execute([
                    ":subject_status" => "Active",
                    ":subject_id" => $subject_id
                ]);

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "Subject updated successfully!"
                ];

                header("Location: ../../pages/admin/home.php?page=subjects");
                exit();
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=subjects");
                exit();
            }
        }
    }
    // ============================= Subjects ==============================================

    // Default
    else {
        header("Location: ../../index.php");
    }
    
?>