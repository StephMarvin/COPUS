<?php

    require_once "../../config/conn.config.php";
    require_once "../../config/validations.config.php";
    require_once "../../config/dropdowns.config.php";

    $get_teacher_actions = $conn->prepare("SELECT * FROM teacher_actions_tbl WHERE action_status = :action_status");
    $get_teacher_actions->execute([":action_status" => "Active"]);

    if ($get_teacher_actions->rowCount() > 0) {
        $teacher_actions = $get_teacher_actions->fetchAll();
    }

    $get_student_actions = $conn->prepare("SELECT * FROM student_actions_tbl WHERE action_status = :action_status");
    $get_student_actions->execute([":action_status" => "Active"]);

    if ($get_student_actions->rowCount() > 0) {
        $student_actions = $get_student_actions->fetchAll();
    }

    $levels_of_engagements_summative = ["high", "medium", "low", "no"];

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["observe-now"]) && isset($_SESSION["observer-id"])) {
        $observer_id = htmlspecialchars(trim(base64_decode($_POST["observer-id"])));
        $department_id = htmlspecialchars(trim(base64_decode($_POST["department-id"])));
        $semester_id = htmlspecialchars(trim(base64_decode($_POST["semester-id"])));
        $teacher_id = htmlspecialchars(trim($_POST["teacher-id"]));
        $copus_type = htmlspecialchars($_POST["copus-type"]);
        $modality = htmlspecialchars($_POST["modality"]);
        $year_level = htmlspecialchars($_POST["year-level"]);
        $subject_id = htmlspecialchars(trim($_POST["subject-id"]));
        $password = htmlspecialchars(trim($_POST["observer-password"]));

        if (empty($observer_id) || empty($department_id) || empty($teacher_id) || empty($copus_type) || empty($modality) || empty($year_level) || empty($subject_id) || empty($password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/observer/home.php?page=observe-now");
            exit();
        } 
        
        else if (!in_array($copus_type, $allowed_copus_types)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid COPUS type! Please try again."
            ];

            header("Location: ../../pages/observer/home.php?page=observe-now");
            exit();
        } 
        
        else if (!in_array($modality, $allowed_modality)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid modality! Please try again."
            ];

            header("Location: ../../pages/observer/home.php?page=observe-now");
            exit();
        } 
        
        else if (!in_array($year_level, $allowed_year_levels)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid year level! Please try again."
            ];

            header("Location: ../../pages/observer/home.php?page=observe-now");
            exit();
        } 
        
        else {
            try {
                $check_account = $conn->prepare("SELECT * FROM observers_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                $check_account->execute([":id_number" => $observer_id]);

                $check_copus_type = $conn->prepare("SELECT * FROM observations_tbl WHERE copus_type = :copus_type AND semester_id = :semester_id AND teacher_id = :teacher_id");
                $check_copus_type->execute([
                    ":copus_type" => $copus_type,
                    ":semester_id" => $semester_id,
                    ":teacher_id" => $teacher_id
                ]);

                $check_semester = $conn->prepare("SELECT * FROM semesters_tbl WHERE semester_id = :semester_id");
                $check_semester->execute([":semester_id" => $semester_id]);

                $check_teacher = $conn->prepare("SELECT * FROM teacher_credentials_tbl WHERE id_number = :teacher_id");
                $check_teacher->execute([":teacher_id" => $teacher_id]);

                $check_subject = $conn->prepare("SELECT * FROM subjects_tbl WHERE subject_id = :subject_id");
                $check_subject->execute([":subject_id" => $subject_id]);

                $check_department = $conn->prepare("SELECT * FROM departments_tbl WHERE department_id = :department_id");
                $check_department->execute([":department_id" => $department_id]);

                if ($check_account->rowCount() <= 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Invalid observer account! Please try again."
                    ];

                    header("Location: ../../pages/observer/home.php?page=observe-now");
                    exit();
                }


                if ($check_copus_type->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => $copus_type . " has already been done to this teacher! Please try again."
                    ];

                    header("Location: ../../pages/observer/home.php?page=observe-now");
                    exit();
                } 
                
                else if ($check_semester->rowCount() <= 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Invalid semester! Please try again."
                    ];

                    header("Location: ../../pages/observer/home.php?page=observe-now");
                    exit();
                } 
                
                else if ($check_teacher->rowCount() <= 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Invalid teacher! Please try again."
                    ];

                    header("Location: ../../pages/observer/home.php?page=observe-now");
                    exit();
                } 
                
                else if ($check_subject->rowCount() <= 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Invalid subject! Please try again."
                    ];

                    header("Location: ../../pages/observer/home.php?page=observe-now");
                    exit();
                } 

                else if ($check_department->rowCount() <= 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Invalid department! Please try again."
                    ];

                    header("Location: ../../pages/observer/home.php?page=observe-now");
                    exit();
                } 
                
                else {
                    $observer_info = $check_account->fetch(PDO::FETCH_OBJ);

                    if (password_verify($password, $observer_info->observer_password)) {
                        $insert_observe = $conn->prepare("INSERT INTO observations_tbl(copus_type, semester_id, teacher_id, subject_id, observer_id, department_id, year_level, modality)
                                                            VALUES(:copus_type, :semester_id, :teacher_id, :subject_id, :observer_id, :department_id, :year_level, :modality)
                                                            ");
                        $insert_observe->execute([
                            ":copus_type" => $copus_type,
                            ":semester_id" => $semester_id,
                            ":teacher_id" => $teacher_id,
                            ":subject_id" => $subject_id,
                            ":observer_id" => $observer_id,
                            ":department_id" => $department_id,
                            ":year_level" => $year_level,
                            ":modality" => $modality
                        ]);

                        $observe_id = $conn->lastInsertId();

                        $_SESSION["observe-id"] = $observe_id;

                        if ($copus_type === "Summative") {
                            header("Location: ../../pages/observer/summative-observation.php");
                            exit();
                        } 
                        
                        else {
                            header("Location: ../../pages/observer/observe.php");
                            exit();
                        }
                    } 
                    
                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Invalid password! Please try again."
                        ];

                        header("Location: ../../pages/observer/home.php?page=observe-now");
                        exit();
                    }
                }
            } 
            
            catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/observer/home.php?page=observe-now");
                exit();
            }
        }
    } 
    
    else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["continue-observing"]) && isset($_SESSION["observer-id"])) {
        $observation_id = htmlspecialchars(trim(base64_decode($_POST["observe-id"])));

        if (empty($observation_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/observer/home.php?page=observe-now");
            exit();
        } else {
            try {
                $check_observation = $conn->prepare("SELECT * FROM observations_tbl WHERE observation_id = :observation_id LIMIT 1");
                $check_observation->execute([":observation_id" => $observation_id]);

                if ($check_observation->rowCount() === 1) {

                    $_SESSION["observe-id"] = $observation_id;

                    $observation_data = $check_observation->fetch(PDO::FETCH_OBJ);

                    if ($observation_data->copus_type === "Summative") {
                        header("Location: ../../pages/observer/summative-observation.php");
                        exit();
                    } else {
                        header("Location: ../../pages/observer/observe.php");
                        exit();
                    }
                } else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Observation not found! Please try again."
                    ];

                    header("Location: ../../pages/observer/home.php?page=observe-now");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/observer/home.php?page=observe-now");
                exit();
            }
        }
    } 
    
    else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit-observation"]) && isset($_SESSION["observe-id"]) && isset($_SESSION["observer-id"])) {
        $observation_id = htmlspecialchars(trim(base64_decode($_POST["observe-id"])));

        if (empty($observation_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/observer/observe-now.php");
            exit();
        } else {
            try {
                $conn->beginTransaction();

                $check_observation = $conn->prepare("SELECT * FROM observations_tbl WHERE observation_id = :observation_id");
                $check_observation->execute([":observation_id" => $observation_id]);

                if ($check_observation->rowCount() <= 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Invalid observation! Please try again."
                    ];

                    header("Location: ../../pages/observer/observe-now.php");
                    exit();
                } else {
                    $update_observation_data = $conn->prepare("UPDATE observations_tbl
                                                                SET observe_status = :observe_status, observed_at = CURRENT_TIMESTAMP()
                                                                WHERE observation_id = :observation_id");
                    $update_observation_data->execute([
                        ":observe_status" => "Complete",
                        ":observation_id" => $observation_id
                    ]);

                    $student_action_data = [];
                    $teacher_action_data = [];

                    foreach ($student_actions as $student_action) {
                        $student_fieldname = "student-" . $student_action["action_code"];

                        $minutes = isset($_POST[$student_fieldname]) && is_array($_POST[$student_fieldname])
                            ? count($_POST[$student_fieldname]) * 2
                            : 0;

                        $insert_student_action = $conn->prepare("INSERT INTO student_action_log_tbl(observation_id, action_name, tally, minutes)
                                                                    VALUES(:observation_id, :action_name, :tally, :minutes)");

                        $insert_student_action->execute([
                            ":observation_id" => $observation_id,
                            ":action_name" => $student_action["action_name"],
                            ":tally" => $minutes / 2,
                            ":minutes" => $minutes
                        ]);
                    }

                    foreach ($teacher_actions as $teacher_action) {
                        $teacher_fieldname = "teacher-" . $teacher_action["action_code"];

                        $minutes = isset($_POST[$teacher_fieldname]) && is_array($_POST[$teacher_fieldname])
                            ? count($_POST[$teacher_fieldname]) * 2
                            : 0;

                        $insert_teacher_actions = $conn->prepare("INSERT INTO teacher_action_log_tbl(observation_id, action_name, tally, minutes)
                                                                    VALUES(:observation_id, :action_name, :tally, :minutes)");

                        $insert_teacher_actions->execute([
                            ":observation_id" => $observation_id,
                            ":action_name" => $teacher_action["action_name"],
                            ":tally" => $minutes / 2,
                            ":minutes" => $minutes
                        ]);
                    }

                    $levels_of_engagements = ["high", "medium", "low"];
                    $counts = ["high" => 0, "medium" => 0, "low" => 0];

                    foreach ($_POST as $key => $value) {
                        if (str_starts_with($key, 'engagement_') && in_array($value, $levels_of_engagements)) {
                            $counts[$value]++;
                        }
                    }

                    foreach ($levels_of_engagements as $level) {
                        $minutes = $counts[$level] * 2;

                        $insert_engagements = $conn->prepare("
                                INSERT INTO engagement_logs_tbl (observation_id, engagement, tally, minutes)
                                VALUES (:observation_id, :engagement, :tally, :minutes)
                            ");
                        $insert_engagements->execute([
                            ':observation_id' => $observation_id,
                            ':engagement' => ucfirst($level),
                            ':tally' => $counts[$level],
                            ':minutes' => $minutes
                        ]);
                    }

                    $comments = $_POST["comments"] ? $_POST["comments"] : [];
                    $student_data = json_encode($student_action_data);
                    $teacher_data = json_encode($teacher_action_data);

                    $allowed_comments = array_filter($comments, function ($comment) {
                        return trim($comment) !== "";
                    });

                    $sanitized_comments = array_map(function ($comment) {
                        return htmlspecialchars(trim($comment), ENT_QUOTES, 'UTF-8');
                    }, $allowed_comments);

                    foreach ($sanitized_comments as $comments) {
                        $insert_comment = $conn->prepare("INSERT INTO observation_comments_tbl(observation_id, observation_comment)
                                                            VALUES(:observation_id, :comment)");

                        $insert_comment->execute([
                            ":observation_id" => $observation_id,
                            ":comment" => $comments
                        ]);
                    }

                    $encoded_observation_id = base64_encode($observation_id);

                    $conn->commit();

                    $_SESSION["query-status"] = [
                        "status" => "success",
                        "message" => "Observation data submitted!"
                    ];

                    header("Location: ../../pages/observer/home.php?page=observation-summary&observation-id=$encoded_observation_id");
                    exit();
                }
            } catch (PDOException $e) {
                $conn->rollBack();
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/observer/observe-now.php");
                exit();
            }
        }
    } 
    
    else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit-summative-observation"]) && isset($_SESSION["observe-id"]) && isset($_SESSION["observer-id"])) {
        $observation_id = htmlspecialchars(trim(base64_decode($_POST["observe-id"])));
        $ratings = $_POST["rating"] ?? [];

        $valid_ratings = array_filter($ratings, function ($value) use ($levels_of_engagements_summative) {
            return in_array(strtolower($value), $levels_of_engagements_summative, true);
        });

        if (empty($observation_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/observer/home.php?page=observe-now");
            exit();
        } else if (count($valid_ratings) !== count($ratings)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid rating type! Please try again."
            ];

            header("Location: ../../pages/observer/home.php?page=observe-now");
            exit();
        } else {

            try {
                $conn->beginTransaction();

                $check_observation = $conn->prepare("SELECT * FROM observations_tbl WHERE observation_id = :observation_id");
                $check_observation->execute([":observation_id" => $observation_id]);

                if ($check_observation->rowCount() <= 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Invalid observation! Please try again."
                    ];

                    header("Location: ../../pages/observer/observe.php");
                    exit();
                } else {
                    $update_observation_data = $conn->prepare("UPDATE observations_tbl
                                                                SET observe_status = :observe_status, observed_at = CURRENT_TIMESTAMP()
                                                                WHERE observation_id = :observation_id");
                    $update_observation_data->execute([
                        ":observe_status" => "Complete",
                        ":observation_id" => $observation_id
                    ]);

                    $total_items  = count($ratings);
                    $high_count   = count(array_filter($valid_ratings, fn($r) => $r === 'high'));
                    $medium_count = count(array_filter($valid_ratings, fn($r) => $r === 'medium'));
                    $low_count    = count(array_filter($valid_ratings, fn($r) => $r === 'low'));
                    $no_count     = count(array_filter($valid_ratings, fn($r) => $r === 'no'));

                    $high_percentage = $high_count > 0 ? ($high_count / $total_items) * 100 : 0;

                    $submit_summative_observation = $conn->prepare("INSERT INTO summative_observations_tbl(observation_id, high_count, medium_count, low_count, no_count, high_percentage)
                                                                    VALUES(:observation_id, :high_count, :medium_count, :low_count, :no_count, :high_percentage)");
                    $submit_summative_observation->execute([
                        "observation_id" => $observation_id,
                        ":high_count" => $high_count,
                        ":medium_count" => $medium_count,
                        ":low_count" => $low_count,
                        ":no_count" => $no_count,
                        ":high_percentage" => $high_percentage
                    ]);

                    // Comments
                    $comments = $_POST["comments"] ? $_POST["comments"] : [];
                    $student_data = json_encode($student_action_data);
                    $teacher_data = json_encode($teacher_action_data);

                    $allowed_comments = array_filter($comments, function ($comment) {
                        return trim($comment) !== "";
                    });

                    $sanitized_comments = array_map(function ($comment) {
                        return htmlspecialchars(trim($comment), ENT_QUOTES, 'UTF-8');
                    }, $allowed_comments);

                    foreach ($sanitized_comments as $comments) {
                        $insert_comment = $conn->prepare("INSERT INTO observation_comments_tbl(observation_id, observation_comment)
                                                            VALUES(:observation_id, :comment)");

                        $insert_comment->execute([
                            ":observation_id" => $observation_id,
                            ":comment" => $comments
                        ]);
                    }

                    $encoded_observation_id = base64_encode($observation_id);

                    $conn->commit();

                    $_SESSION["query-status"] = [
                        "status" => "success",
                        "message" => "Observation data submitted!"
                    ];

                    header("Location: ../../pages/observer/home.php?page=observation-summary&observation-id=$encoded_observation_id");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/observer/home.php?page=observe-now");
                exit();
            }
        }
    } 
    
    else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update-summary"])) {
        $observation_id = htmlspecialchars(trim(base64_decode($_POST["observation-id"])));
        $encoded_observation_id = base64_encode($observation_id);
        $observer_password = htmlspecialchars(trim($_POST["observer-password"]));

        $feedback_rating = htmlspecialchars($_POST["feedback-rating"]);
        $nps_score = htmlspecialchars($_POST["nps-score"]);

        $uploaded_file = $_FILES["feedback-data"]["name"];
        $tmp_name = $_FILES["feedback-data"]["tmp_name"];

        $file_extension = strtolower(pathinfo($uploaded_file, PATHINFO_EXTENSION));

        if (empty($observation_id) || empty($uploaded_file) || empty($feedback_rating) || empty($nps_score)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit();
        } else if (!in_array($file_extension, $allowed_file_types)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid file type! Please try again."
            ];

            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit();
        } else if (!is_numeric($feedback_rating) || (float) $feedback_rating < 0 || (float) $feedback_rating > 100.00) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Feedback rating must be numeric and not less than 0 and does not exceed to 100! Please try again."
            ];

            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit();
        } else if (!is_numeric($nps_score) || (float) $nps_score < 0 || (float) $nps_score > 100.00) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Net promoter score rating must be numeric and not less than 0 and does not exceed to 100! Please try again."
            ];

            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit();
        } else {
            try {

                $check_feedback_data = $conn->prepare("SELECT * FROM student_feedback_tbl WHERE observation_id = :observation_id");
                $check_feedback_data->execute([":observation_id" => $observation_id]);

                if ($check_feedback_data->rowCount() > 0) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Student feedback already rated! Please try again."
                    ];

                    header("Location: ../../pages/observer/home.php?page=observation-details&observation-id=$encoded_observation_id");
                    exit();
                } else {


                    $unique_file_name = "Student_Feedback_" . uniqid() . "." . $file_extension;

                    $file_location = "../../uploads/copus/";
                    $file_path = $file_location . $unique_file_name;

                    $add_student_feedback = $conn->prepare("INSERT INTO student_feedback_tbl(observation_id, feedback_rating, net_promoter_score, feedback_form)
                                                    VALUES(:observation_id, :feedback_rating, :nps, :file_name)");
                    $add_student_feedback->execute([
                        ":observation_id" => $observation_id,
                        ":feedback_rating" => $feedback_rating,
                        ":nps" => $nps_score,
                        ":file_name" => $unique_file_name
                    ]);

                    move_uploaded_file($tmp_name, $file_path);

                    $_SESSION["query-status"] = [
                        "status" => "success",
                        "message" => "Summary observation updated successfully!"
                    ];

                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit();
            }
        }
    } 
    
    else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete-observation"]) && isset($_SESSION["observer-id"])) {

        $observation_id = htmlspecialchars(trim(base64_decode($_POST["observe-id"])));

        if (empty($observation_id)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/observer/home.php?page=observe-now");
            exit();
        } else {
            try {
                $check_observation = $conn->prepare("SELECT * FROM observations_tbl WHERE observation_id = :observation_id LIMIT 1");
                $check_observation->execute([":observation_id" => $observation_id]);

                if ($check_observation->rowCount() === 1) {
                    $delete_observation = $conn->prepare("DELETE FROM observations_tbl WHERE observation_id = :observation_id");
                    $delete_observation->execute([":observation_id" => $observation_id]);

                    $_SESSION["query-status"] = [
                        "status" => "success",
                        "message" => "Observation deleted successfully!"
                    ];

                    header("Location: ../../pages/observer/home.php?page=observe-now");
                    exit();
                } else {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "Observation not found! Please try again."
                    ];

                    header("Location: ../../pages/observer/home.php?page=observe-now");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];

                header("Location: ../../pages/observer/home.php?page=observe-now");
                exit();
            }
        }
    }

    // Default
    else {
        header("Location: ../../index.php");
    }
?>
