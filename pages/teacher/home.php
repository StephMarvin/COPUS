<?php

    ob_start();

    require_once "../../config/conn.config.php";
    require_once "../../config/dropdowns.config.php";
    require_once "../../config/datetime.config.php";

    include_once "includes/page-titles.php";
    $page_name = isset($_GET["page"]) ? $_GET["page"] : "dashboard";

    $file_path = "../../uploads/imgs/";

    if(empty($_SESSION["teacher-id"]) || $_SESSION["teacher-id"] === "") {
        header("Location: login.php");
        exit();
    }

    else {
        $id_number = htmlspecialchars(trim($_SESSION["teacher-id"]));

        $get_user_data = $conn->prepare("SELECT 
                                                    tc.*, ti.*, dt.*
                                                FROM teacher_credentials_tbl tc
                                                LEFT JOIN teacher_info_tbl ti
                                                ON tc.id_number = ti.id_number
                                                LEFT JOIN departments_tbl dt
                                                ON tc.department_id = dt.department_id
                                                WHERE tc.id_number = :id_number");
        $get_user_data->execute([":id_number" => $id_number]);
        
        while($user_data = $get_user_data->fetch(PDO::FETCH_OBJ)) {

            // Primary Data
            $first_name = $user_data->first_name;
            $middle_name = $user_data->middle_name;
            $last_name = $user_data->last_name;
            $role = $user_data->role;
            $email_address = $user_data->email_address;
            $employment_status = $user_data->employment_status;
            $teacher_rank = $user_data->teacher_rank;
            $generated_password = $user_data->generated_password;
            $is_archived = $user_data->is_archived;
            $locked_account = $user_data->locked_account;
            $two_factor_authentication = $user_data->two_factor_authentication;

            // Secondary Data
            $profile_picture = $user_data->profile_picture;
            $date_of_birth = $user_data->date_of_birth;
            $gender = $user_data->gender;
            $marital_status = $user_data->marital_status;
            $phone_number = $user_data->phone_number;
            $telephone_number = $user_data->telephone_number;
            $temporary_address = $user_data->temporary_address;
            $permanent_address = $user_data->permanent_address;
            $facebook_link = $user_data->facebook_link;
            $updated_at = $user_data->updated_at;

            // Department
            $department_name = $user_data->department_name ?? "Not Assigned";
            $department_code = $user_data->department_code ?? "Not Assigned";
            $department_id = $user_data->department_id ?? null;
        }

        if(empty($profile_picture) || !file_exists($file_path . $profile_picture)) {
            $profile_picture = "default-img.png";
        }

        if($locked_account === "Yes") {
            unset($_SESSION["teacher-id"]);

            $_SESSION["locked-account"] = true;
            
            header("Location: ../locked/locked-account.php");
            exit();
        }

        if($is_archived === "Yes") {
            unset($_SESSION["teacher-id"]);
            
            header("Location: login.php");
            exit();
        }

        $get_current_year_and_sem = $conn->prepare("SELECT 
                                                        ay.academic_year,
                                                        s.semester, s.semester_id
                                                      FROM 
                                                      academic_years_tbl ay
                                                      LEFT JOIN semesters_tbl s
                                                      ON ay.academic_year_id = s.academic_year_id
                                                      WHERE ay.status = :year_status AND s.semester_status = :semester_status
                                                      LIMIT 1
                                                      ");

        $get_current_year_and_sem->execute([
            ":year_status" => "Active",
            ":semester_status" => "Active"
        ]);

        if($get_current_year_and_sem->rowCount() === 1) {
            $academic_data = $get_current_year_and_sem->fetch(PDO::FETCH_OBJ);
            $academic_year = $academic_data -> academic_year;
            $semester_id = $academic_data -> semester_id;
            $semester = $academic_data -> semester;

            // $academic_year_and_sem = $academic_year . ": " . $semester;
        }

        else {
            $academic_year = "Not Set";
            $semester = "Not Set";
            // $academic_year_and_sem = "Not set.";
            $semester_id = null;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php
        include_once "../global-includes/web-title.php";
    ?>

    <?php
        include_once "../global-includes/css-files.php";
    ?>

</head>
<body>

    <?php include_once "../global-includes/header.php"; ?>
    <?php include_once "includes/sidebar-nav.php"; ?>

    <?php
        $page_path = "pages/$page_name.php";

        if(file_exists($page_path)) {
            include_once $page_path;
        }

        else {
            $page_name = "dashboard";
            include_once "pages/dashboard.php";
        }
       
    ?>

    <?php include_once "../global-includes/footer.php"; ?>

    <?php include_once "../global-includes/script-files.php"; ?>
    
</body>
</html>

<?php ob_end_flush(); ?>