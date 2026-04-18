<?php

    require_once "../../config/conn.config.php";
    require_once "../../config/validations.config.php";
    require_once "../../config/dropdowns.config.php";

    require_once "../../config/file-upload/vendor/autoload.php";

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    // Add/Modify Subjects
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-new-subject"])) {
        $id_number = htmlspecialchars(trim($_POST["dean-id"]));
        $subject_code = htmlspecialchars($_POST["subject-code"]);
        $subject_name = htmlspecialchars($_POST["subject-name"]);
        $units = htmlspecialchars(trim($_POST["subject-units"]));
        $semester = htmlspecialchars($_POST["semester"]);
        $department_id = htmlspecialchars(base64_decode($_POST["department-id"]));
        $dean_password = htmlspecialchars(trim($_POST["dean-password"]));

        if(empty($id_number) || empty($subject_code) || empty($subject_name) || empty($units) || empty($semester) || empty($department_id) || empty($dean_password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=subjects");
            exit();
        }

        else if(!in_array($semester, $allowed_semesters)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid semester! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=subjects");
            exit();
        }

        else if(!ctype_digit($units) || (int)$units > 10 || (int)$units < 1) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Subject units must be a number from 1 to 10 only! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=subjects");
            exit();
        }

        else {
            try {

                $check_subject_codes = $conn->prepare("SELECT * FROM subjects_tbl WHERE subject_code = :subject_code");
                $check_subject_codes->execute([":subject_code" => $subject_code]);

                $check_subject_names = $conn->prepare("SELECT * FROM subjects_tbl WHERE subject_name = :subject_name");
                $check_subject_names->execute([":subject_name" => $subject_name]);

                $check_department = $conn->prepare("SELECT * FROM departments_tbl WHERE department_id = :department_id AND department_status = :department_status");
                $check_department->execute([":department_id" => $department_id, ":department_status" => "Active"]);

                if($check_subject_codes->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "This subject is already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/deans/home.php?page=subjects");
                    exit();
                }

                else if($check_subject_names->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "This subject is already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/deans/home.php?page=subjects");
                    exit();
                }

                else if($check_department->rowCount() <= 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Department does not exist! Please try again."
                    ];
        
                    header("Location: ../../pages/deans/home.php?page=subjects");
                    exit();
                }

                else {
                    $check_dean_account = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                    $check_dean_account->execute([":id_number" => $id_number]);

                    if($check_dean_account->rowCount() === 1) {
                        $dean_details = $check_dean_account->fetch(PDO::FETCH_OBJ);

                        if(password_verify($dean_password, $dean_details->dean_password)) {
                            $insert_subject = $conn->prepare("INSERT INTO subjects_tbl(subject_code, subject_name, subject_units, semester, department_id)
                                                            VALUES(:subject_code, :subject_name, :subject_units, :semester, :department_id)");

                            $insert_subject->execute([
                                ":subject_code" => $subject_code,
                                ":subject_name" => $subject_name,
                                ":subject_units" => $units,
                                ":semester" => $semester,
                                ":department_id" => $department_id
                            ]);

                            $_SESSION["query-status"] = [
                                "status" => "success",
                                "message" => "Subject added successfully!"
                            ];
                
                            header("Location: ../../pages/deans/home.php?page=subjects");
                            exit();
                        }

                        else {
                            $_SESSION["query-status"] = [
                                "status" => "danger",
                                "message" => "Invalid password! Please try again."
                            ];
                
                            header("Location: ../../pages/deans/home.php?page=subjects");
                            exit();
                        }
                    }
                    
                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Dean user not found!"
                        ];
            
                        header("Location: ../../pages/deans/home.php?page=subjects");
                        exit();
                    }
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again." . $e->getMessage()
                ];
    
                header("Location: ../../pages/deans/home.php?page=subjects");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-multiple-subjects"])) {

        $id_number = htmlspecialchars(trim($_POST["dean-id"]));
        $department_id = htmlspecialchars(base64_decode($_POST["department-id"]));
        $dean_password = htmlspecialchars(trim($_POST["dean-password"]));

        $excel_file = $_FILES["excel-file"]["name"];
        $file_tmp_name = $_FILES["excel-file"]["tmp_name"];
        $file_error = $_FILES["excel-file"]["error"];
        $file_extension = strtolower(pathinfo($excel_file, PATHINFO_EXTENSION));

        if(empty($id_number) || empty($department_id) || empty($dean_password) || empty($excel_file)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=subjects");
            exit();
        }

        else if(!in_array($file_extension, ["xlsx", "xls", "csv"]) || $file_error !== 0) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid or corrupted file! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=subjects");
            exit();
        }

        else {
            
            try {
                $unique_file_name = "Subjects_" . time() . "_" . uniqid() . "." . $file_extension;

                $check_account = $conn->prepare("SELECT * FROM deans_credentials_tbl WHERE id_number = :id_number AND is_archived = 'No' LIMIT 1");
                $check_account->execute([":id_number" => $id_number]);

                if($check_account->rowCount() === 1) {
                    $dean_details = $check_account->fetch(PDO::FETCH_OBJ);

                    if(password_verify($dean_password, $dean_details->dean_password)) {

                        $header_aliases = [
                            'semester' => ['semester', 'sem'],
                            'subject code' => ['subject code', 'code', 'sub code', 'sub. code'],
                            'subject name' => ['subject name', 'name', 'sub name', 'sub. name'],
                            'units' => ['units', 'unit', 'subject units', 'sub. units']
                        ];

                        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_tmp_name);
                        $rows = $spreadsheet->getActiveSheet()->toArray();

                        $header_rows = array_map(function($h) {
                            return strtolower(trim($h));
                        }, $rows[0]);

                        $headers = [];

                        foreach($header_rows as $index => $header) {
                            foreach($header_aliases as $key => $possible_names) {
                                if(in_array($header, $possible_names)) {
                                    $headers[$key] = $index;
                                }
                            }
                        }

                        // Validate Headers
                        foreach($header_aliases as $key => $value) {
                            if(!isset($headers[$key])) {
                                $_SESSION["query-status"] = [
                                    "status" => "danger",
                                    "message" => "Missing required header! Please try again."
                                ];

                                header("Location: ../../pages/deans/home.php?page=subjects");
                                exit();
                            }
                        }

                        $insert_subject = $conn->prepare("INSERT IGNORE INTO subjects_tbl(subject_code, subject_name, subject_units, semester, department_id)
                                                          VALUES(:subject_code, :subject_name, :subject_units, :semester, :department_id)");
                        
                        foreach($rows as $index => $row) {

                            if($index === 0) continue;

                            $subject_code = trim($row[$headers["subject code"]] ?? "");
                            $subject_name = trim($row[$headers["subject name"]] ?? "");
                            $units = trim($row[$headers["units"]] ?? "");
                            $semester = trim($row[$headers["semester"]] ?? "");

                            if(!$subject_code || !$subject_name) {
                                continue;
                            }
                        
                            if(!in_array($semester, $allowed_semesters)) {
                                $_SESSION["query-status"] = [
                                    "status" => "danger",
                                    "message" => "Invalid semester! Please try again."
                                ];

                                header("Location: ../../pages/deans/home.php?page=subjects");
                                exit();
                            }

                            if(!ctype_digit($units) || (int)$units > 10 || (int)$units < 1) {
                                $_SESSION["query-status"] = [
                                    "status" => "danger",
                                    "message" => "Subject units must be a number from 1 to 10 only! Please try again."
                                ];

                                header("Location: ../../pages/deans/home.php?page=subjects");
                                exit();
                            }

                            $subject_code = strtoupper($subject_code);

                            $insert_subject->bindParam(":subject_code", $subject_code);
                            $insert_subject->bindParam(":subject_name", $subject_name);
                            $insert_subject->bindParam(":subject_units", $units);
                            $insert_subject->bindParam(":semester", $semester);
                            $insert_subject->bindParam(":department_id", $department_id);

                            $insert_subject->execute();

                        }

                        $upload_path = "../../uploads/files/" . $unique_file_name;
                        move_uploaded_file($file_tmp_name, $upload_path);

                        $_SESSION["query-status"] = [
                            "status" => "success",
                            "message" => "Subjects imported successfully!"
                        ];

                        header("Location: ../../pages/deans/home.php?page=subjects");
                        exit();
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/deans/home.php?page=subjects");
                        exit();
                    }
                }

                else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Invalid account! Please try again."
                    ];

                    header("Location: ../../pages/deans/home.php?page=subjects");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again." . $e->getMessage()
                ];
    
                header("Location: ../../pages/deans/home.php?page=subjects");
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

        if(empty($subject_id) || empty($subject_code) || empty($subject_name) || empty($units) || empty($semester)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=subjects");
            exit();
        }

        else if(!in_array($semester, $allowed_semesters)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid semester! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=subjects");
            exit();
        }

        else if(!ctype_digit($units) || (int)$units > 10 || (int)$units < 1) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Subject units must be a number from 1 to 10 only! Please try again."
            ];

            header("Location: ../../pages/deans/home.php?page=subjects");
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
        
                    header("Location: ../../pages/deans/home.php?page=subjects");
                    exit();
                }

                else if($check_subject_names->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "This subject is already exists! Please try again."
                    ];
        
                    header("Location: ../../pages/deans/home.php?page=subjects");
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
        
                    header("Location: ../../pages/deans/home.php?page=subjects");
                    exit();
                }
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
    
                header("Location: ../../pages/deans/home.php?page=subjects");
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

            header("Location: ../../pages/deans/home.php?page=subjects");
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

                header("Location: ../../pages/deans/home.php?page=subjects");
                exit();
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/deans/home.php?page=subjects");
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

            header("Location: ../../pages/deans/home.php?page=subjects");
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

                header("Location: ../../pages/deans/home.php?page=subjects");
                exit();
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/deans/home.php?page=subjects");
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