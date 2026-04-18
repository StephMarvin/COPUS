<?php

    // $get_teacher_name = $conn->prepare("SELECT 
    //                                     CONCAT(first_name, ' ', last_name) AS 'teacher' 
    //                                     FROM teacher_credentials_tbl
    //                                     WHERE id_number = :id_number
    //                                 ");
    // $get_teacher_name->execute([":id_number" => $id_number]);

    // if($get_teacher_name->rowCount() <= 0) {
    //     $_SESSION["query-status"] = [
    //         "status" => "danger", 
    //         "message" => "Teacher records not found!"
    //     ];

    //     header("Location: home.php?page=dashboard");
    //     exit();
    // }

    $get_teacher_records = $conn->prepare("SELECT
                                                    ot.*,
                                                    ay.academic_year,
                                                    s.semester,
                                                    CONCAT(tc.last_name, ', ', tc.first_name) AS 'teacher'
                                                FROM observations_tbl ot
                                                LEFT JOIN semesters_tbl s
                                                ON ot.semester_id = s.semester_id
                                                LEFT JOIN academic_years_tbl ay
                                                ON s.academic_year_id = ay.academic_year_id
                                                LEFT JOIN teacher_credentials_tbl tc
                                                ON ot.teacher_id = tc.id_number
                                                WHERE 
                                                ot.teacher_id = :teacher_id AND 
                                                ot.observe_status = :observe_status AND
                                                ot.copus_type != :copus_type
                                                ORDER BY 
                                                ay.academic_year,
                                                FIELD(s.semester, '2nd Semester', '1st Semester'),
                                                FIELD(copus_type, 'COPUS 3', 'COPUS 2', 'COPUS 1')
                                                ");
    $get_teacher_records->execute([":teacher_id" => $id_number, ":observe_status" => "Complete", ":copus_type" => "Summative"]);

?>

<!-- Main -->
<main id="main" class="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Observation Data -->
    <section class="section">

        <div class="row">
            
            <?php
                if($get_teacher_records->rowCount() > 0) {
                    while($observation_data = $get_teacher_records->fetch()) {

                        $observation_id = $observation_data["observation_id"];
                        $academic_year = $observation_data["academic_year"];
                        $semester = $observation_data["semester"];

                        $get_observation_data = $conn->prepare("SELECT
                                                        ot.*,   
                                                        sub.subject_name,
                                                        CONCAT(tc.last_name, ', ', tc.first_name) AS 'teacher',
                                                        CONCAT(oc.last_name, ', ', oc.first_name) AS 'observer',
                                                        cft.*,
                                                        dt.department_code, dt.department_name
                                                    FROM observations_tbl ot
                                                    LEFT JOIN semesters_tbl s
                                                    ON ot.semester_id = s.semester_id
                                                    LEFT JOIN academic_years_tbl ay
                                                    ON s.academic_year_id = s.academic_year_id
                                                    LEFT JOIN subjects_tbl sub
                                                    ON ot.subject_id = sub.subject_id
                                                    LEFT JOIN teacher_credentials_tbl tc
                                                    ON ot.teacher_id = tc.id_number
                                                    LEFT JOIN observers_credentials_tbl oc
                                                    ON ot.observer_id = oc.id_number
                                                    LEFT JOIN copus_forms_tbl cft
                                                    ON ot.observation_id = cft.observation_id
                                                    LEFT JOIN departments_tbl dt
                                                    ON ot.department_id = dt.department_id
                                                    WHERE ot.observation_id = :observation_id
                                                    ");
                        $get_observation_data->execute([":observation_id" => $observation_id]);

                        if($get_observation_data->rowCount() > 0) {

                            $observation_info = $get_observation_data->fetch(PDO::FETCH_OBJ);

                            $student_action_labels = [];
                            $student_tally_series = [];

                            $teacher_action_labels = [];
                            $teacher_tally_series = [];

                            $total_student_tally = 0;
                            $total_teacher_tally = 0;

                            $total_student_active_learning_tally = 0;
                            $total_teacher_active_learning_tally = 0;

                            $get_student_action_data = $conn->prepare("SELECT 
                                                                                sal.*,
                                                                                sat.is_active_learning
                                                                            FROM student_action_log_tbl sal
                                                                            LEFT JOIN student_actions_tbl sat
                                                                            ON sal.action_name = sat.action_name
                                                                            WHERE observation_id = :observation_id");

                            $get_student_action_data->execute([":observation_id" => $observation_id]);

                            $student_actions = $get_student_action_data->fetchAll();

                            foreach ($student_actions as $student_action) {

                                if ($student_action["is_active_learning"] === "Yes") {
                                    $total_student_tally += (int)$student_action["tally"];
                                    $total_student_active_learning_tally += (int)$student_action["tally"];
                                } 
                                
                                else {
                                    $total_student_tally += (int)$student_action["tally"];
                                }
                            }

                            foreach ($student_actions as $student_action) {
                                $student_tally = (int)$student_action["tally"];

                                if ($student_tally > 0) {
                                    $student_action_labels[] = htmlspecialchars($student_action["action_name"]);

                                    if ($total_student_tally > 0) {
                                        $student_percentage = ($student_tally / $total_student_tally) * 100;
                                    } 
                                    
                                    else {
                                        $student_percentage = 0;
                                    }

                                    $student_tally_series[] = round($student_percentage, 2);
                                }
                            }

                            $get_teacher_action_data = $conn->prepare("SELECT 
                                                            tal.*,
                                                            tat.is_active_learning
                                                        FROM 
                                                        teacher_action_log_tbl tal
                                                        LEFT JOIN teacher_actions_tbl tat
                                                        ON tal.action_name = tat.action_name
                                                        WHERE tal.observation_id = :observation_id");

                            $get_teacher_action_data->execute([":observation_id" => $observation_id]);

                            $teacher_actions = $get_teacher_action_data->fetchAll();

                            foreach ($teacher_actions as $teacher_action) {

                                if ($teacher_action["is_active_learning"] === "Yes") {
                                    $total_teacher_tally += (int)$teacher_action["tally"];
                                    $total_teacher_active_learning_tally += (int)$teacher_action["tally"];
                                } 
                                
                                else {
                                    $total_teacher_tally += (int)$teacher_action["tally"];
                                }
                            }

                            foreach ($teacher_actions as $teacher_action) {
                                $teacher_tally = (int)$teacher_action["tally"];

                                if ($teacher_tally > 0) {
                                    $teacher_action_labels[] = htmlspecialchars($teacher_action["action_name"]);

                                    if ($total_teacher_tally > 0) {
                                        $teacher_percentage = ($teacher_tally / $total_teacher_tally) * 100;
                                    } 
                                    
                                    else {
                                        $teacher_percentage = 0;
                                    }

                                    $teacher_tally_series[] = round($teacher_percentage, 2);
                                }
                            }

                            $high_engagements = 0;
                            $medium_engagements = 0;
                            $low_engagements = 0;

                            $total_engagement_tally = 0;

                            $select_engagements = $conn->prepare("SELECT * FROM engagement_logs_tbl WHERE observation_id = :observation_id");
                            $select_engagements->execute([":observation_id" => $observation_id]);

                            $engagements = $select_engagements->fetchAll();

                            foreach ($engagements as $engagement) {
                                $engagement_tally = (int)$engagement["tally"];
                                $total_engagement_tally += $engagement_tally;

                                switch (strtolower($engagement["engagement"])) {
                                    case "high":
                                        $high_engagements += $engagement_tally;
                                        break;

                                    case "medium":
                                        $medium_engagements += $engagement_tally;
                                        break;

                                    case "low":
                                        $low_engagements += $engagement_tally;
                                        break;
                                }
                            }

                            if ($total_engagement_tally > 0) {
                                $high_engagement_percentage = number_format(($high_engagements / $total_engagement_tally) * 100, 2, '.');
                                $medium_engagement_percentage = number_format(($medium_engagements / $total_engagement_tally) * 100, 2, '.');
                                $low_engagement_percentage = number_format(($low_engagements / $total_engagement_tally) * 100, 2, '.');
                            } 
                            
                            else {
                                $high_engagement_percentage = $medium_engagement_percentage = $low_engagement_percentage = 0;
                            }

                            $student_action_label_json = json_encode($student_action_labels);
                            $student_action_series_json = json_encode($student_tally_series);

                            $teacher_action_label_json = json_encode($teacher_action_labels);
                            $teacher_action_series_json = json_encode($teacher_tally_series);

                            $student_active_learning_percentage = number_format(($total_student_active_learning_tally / $total_student_tally) * 100, 2, '.');
                            $teacher_active_learning_percentage = number_format(($total_teacher_active_learning_tally / $total_teacher_tally) * 100, 2, '.');
                                                 
                            $file_name = $observation_info->teacher . "_" . $observation_info->copus_type . "_" . "A.Y." . $academic_year . "_" . $semester . "_Observation_Summary";
                        }

                        else {
                            $_SESSION["query-status"] = [
                                "status" => "danger", 
                                "message" => "Observation data not found!"
                            ];
                    
                            header("Location: home.php?page=teacher-records");
                            exit();
                        }
                ?>
                    <div class="col-lg-12">

                        <div class="card shadow">

                            <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                                <h5 class="card-title custom-card-title text-white"> 
                                    Academic Year: <?php echo htmlspecialchars($academic_year . " | " . $semester); ?> 
                                </h5>

                                <div>

                                    <a class="btn btn-dark text-white" href="<?php echo htmlspecialchars($observation_info->file_path); ?>" download="<?php echo htmlspecialchars($observation_info->file_name); ?>"> 
                                        Download Form
                                    </a>

                                    <button 
                                        class="btn btn-light" 
                                        onclick="exportToPDF(
                                            'observation_summary_<?php echo htmlspecialchars($observation_id); ?>',
                                            '<?php echo htmlspecialchars($file_name); ?>'
                                        )"
                                        > 
                                        Download Data
                                    </button>

                                </div>

                            </div>

                            <div class="card-body" id="observation_summary_<?php echo htmlspecialchars($observation_id); ?>">

                                <div class="mt-3 mb-3 p-2">

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <h5> Academic Year and Semester: <span class="fw-bold"><?php echo htmlspecialchars($academic_year . " | " . $semester); ?></span> </h5>
                                        </div>

                                        <div class="col-lg-6">
                                            <h5> COPUS Type: <span class="fw-bold"><?php echo htmlspecialchars($observation_info->copus_type); ?></span> </h5>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <h5> Year Level: <span class="fw-bold"><?php echo htmlspecialchars($observation_info->year_level); ?></span> </h5>
                                        </div>

                                        <div class="col">
                                            <h5> Modality: <span class="fw-bold"><?php echo htmlspecialchars($observation_info->modality); ?></span> </h5>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <h5> Subject: <span class="fw-bold"><?php echo htmlspecialchars($observation_info->subject_name); ?></span> </h5>
                                        </div>

                                        <div class="col">
                                            <h5> Teacher: <span class="fw-bold"><?php echo htmlspecialchars($observation_info->teacher); ?></span> </h5>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <h5> Department: <span class="fw-bold"><?php echo htmlspecialchars($observation_info->department_name . " (" . $observation_info->department_code . ")"); ?></span> </h5>
                                        </div> 
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <h5> Observer: <span class="fw-bold"><?php echo htmlspecialchars($observation_info->observer); ?></span> </h5>
                                        </div>

                                        <div class="col">
                                            <h5> Observed at: <span class="fw-bold"><?php echo htmlspecialchars(format_timestamp($observation_info->observed_at)); ?></span> </h5>
                                        </div>
                                    </div>
                                </div>

                                <section class="section">

                                    <div class="row">

                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover text-center border">

                                                <thead>
                                                    <tr>
                                                        <th colspan="2" class="bg-dark text-white"> Active Learning Percentage </th>
                                                    </tr>

                                                    <tr>
                                                        <th class="bg-warning"> % of Student Actions Supportive of AL </th>
                                                        <th class="bg-warning"> % of Teacher Actions Supportive of AL </th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <tr class="fw-bold">
                                                        <td>
                                                            <?php echo htmlspecialchars($student_active_learning_percentage); ?>%
                                                        </td>

                                                        <td>
                                                            <?php echo htmlspecialchars($teacher_active_learning_percentage); ?>%
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover text-center">

                                                <thead>
                                                    <tr>
                                                        <th colspan="3" class="bg-dark text-white"> Student Engagement </th>
                                                    </tr>

                                                    <tr>
                                                        <th class="bg-success"> % of High-level Engagement </th>
                                                        <th class="bg-warning"> % of Medium-level Engagement </th>
                                                        <th class="bg-danger"> % of Low-level Engagement </th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <tr class="fw-bold">
                                                        <td>
                                                            <?php echo htmlspecialchars($high_engagement_percentage); ?>%
                                                        </td>

                                                        <td>
                                                            <?php echo htmlspecialchars($medium_engagement_percentage); ?>%
                                                        </td>

                                                        <td>
                                                            <?php echo htmlspecialchars($low_engagement_percentage); ?>%
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>

                                    <ul class="nav nav-tabs nav-tabs-bordered">

                                        <li class="nav-item w-50">
                                            <button class="w-100 nav-link active" data-bs-toggle="tab" data-bs-target="#chart-view-<?php echo htmlspecialchars($observation_id); ?>"> Chart View </button>
                                        </li>

                                        <li class="nav-item w-50">
                                            <button class="w-100 nav-link" data-bs-toggle="tab" data-bs-target="#table-view-<?php echo htmlspecialchars($observation_id); ?>"> Table View </button>
                                        </li>

                                    </ul>

                                    <div class="tab-content">

                                        <div class="tab-pane fade show active" id="chart-view-<?php echo htmlspecialchars($observation_id); ?>">

                                            <div class="row">

                                                <div class="col-lg-6 col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5 class="card-title"> Student Actions Data </h5>

                                                            <!-- Pie Chart -->
                                                            <div id="studentActionChart_<?php echo htmlspecialchars($observation_id); ?>"></div>
                                                            <!-- End Pie Chart -->

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5 class="card-title"> Teacher Actions Data </h5>

                                                            <!-- Pie Chart -->
                                                            <div id="teacherActionChart_<?php echo htmlspecialchars($observation_id); ?>"></div>  
                                                            <!-- End Pie Chart -->

                                                        </div>
                                                    </div>
                                                </div>

                                                <script>
                                                    document.addEventListener("DOMContentLoaded", function() {
                                                        convertDataToChart(
                                                            "studentActionChart_<?php echo htmlspecialchars($observation_id); ?>", 
                                                            <?php echo $student_action_label_json; ?>,
                                                            <?php echo $student_action_series_json; ?>,
                                                            <?php echo json_encode($colors_pool); ?>,
                                                            "Student Actions Data",
                                                            "pie"
                                                        );

                                                        convertDataToChart(
                                                            "teacherActionChart_<?php echo htmlspecialchars($observation_id); ?>",                  
                                                            <?php echo $teacher_action_label_json; ?>,
                                                            <?php echo $teacher_action_series_json; ?>,
                                                            <?php echo json_encode($colors_pool); ?>,
                                                            "Teacher Actions Data",
                                                            "pie"
                                                        )
                                                    });               
                                                </script>

                                            </div>

                                        </div>

                                        <div class="tab-pane fade" id="table-view-<?php echo htmlspecialchars($observation_id); ?>">

                                            <div class="table-responsive">

                                                <div class="row mt-2 p-0">

                                                    <div class="col-lg-6 col-md-12">
                                                        <table class="table table-striped table-hover border text-center">

                                                            <thead>
                                                                <tr>
                                                                    <th class="bg-warning"> Student Actions </th>
                                                                    <th class="bg-warning"> Tally </th>
                                                                    <th class="bg-warning"> Percentage </th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <?php
                                                                foreach ($student_actions as $student_action):
                                                                    $student_tally = $student_action["tally"];
                                                                    $student_tally_percentage = number_format(($student_tally / $total_student_tally) * 100, 2, '.');
                                                                ?>
                                                                    <tr>
                                                                        <td class="bg-warning fw-bold">
                                                                            <?php echo htmlspecialchars($student_action["action_name"]); ?>
                                                                        </td>

                                                                        <td class="fw-bold">
                                                                            <?php echo htmlspecialchars($student_tally); ?>
                                                                        </td>

                                                                        <td class="fw-bold">
                                                                            <?php echo htmlspecialchars($student_tally_percentage); ?>%
                                                                        </td>
                                                                    </tr>
                                                                <?php
                                                                endforeach
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div class="col-lg-6 col-md-12">
                                                        <table class="table table-striped table-hover border text-center">

                                                            <thead>
                                                                <tr>
                                                                    <th class="bg-primary text-white"> Teacher Actions </th>
                                                                    <th class="bg-primary text-white"> Tally </th>
                                                                    <th class="bg-primary text-white"> Percentage </th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <?php
                                                                foreach ($teacher_actions as $teacher_action):
                                                                    $teacher_tally = $teacher_action["tally"];
                                                                    $teacher_tally_percentage = number_format(($teacher_tally / $total_teacher_tally) * 100, 2, '.');
                                                                ?>
                                                                    <tr>
                                                                        <td class="bg-primary fw-bold text-white">
                                                                            <?php echo htmlspecialchars($teacher_action["action_name"]); ?>
                                                                        </td>

                                                                        <td class="fw-bold">
                                                                            <?php echo htmlspecialchars($teacher_tally); ?>
                                                                        </td>

                                                                        <td class="fw-bold">
                                                                            <?php echo htmlspecialchars($teacher_tally_percentage); ?>%
                                                                        </td>
                                                                    </tr>
                                                                <?php
                                                                endforeach
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <h5 class="fs-lg-4 fs-md-5 fs-sm-6"> Comments: </h5>

                                        <?php
                                            $get_comments = $conn->prepare("SELECT * FROM observation_comments_tbl WHERE observation_id = :observation_id");
                                            $get_comments->execute([":observation_id" => $observation_id]);

                                            if($get_comments->rowCount() > 0) {
                                                $comments = [];
                                                while($comment_data = $get_comments->fetch()) {
                                                    $comments[] = htmlspecialchars($comment_data["observation_comment"]);
                                                }

                                                ?>
                                                    <p> <?php echo implode(", ", $comments); ?>. </p>
                                                <?php
                                            }

                                            else {
                                                ?>
                                                    <p> No comments. </p>
                                                <?php
                                            }
                                        ?>
                                    </div>

                                </section>
                                
                            </div>

                        </div>    
                    </div>
                <?php
                    }
                }

                else {
                    ?>
                        <div class="col-lg-12">

                            <div class="card shadow">

                                <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                                    <h5 class="card-title custom-card-title text-white"> 
                                        No Observation Record
                                    </h5>
                                </div>

                                <div class="card-body py-2">
                                    <h5 class="text-center"> No observation record. </h5>
                                </div>
                            </div>

                        </div>
                    <?php
                }
            ?>
        </div>

    </section>
    <!-- End Observation Data -->

</main>
<!-- End #main -->