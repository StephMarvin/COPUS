<?php

$get_deans_name = $conn->prepare("SELECT 
                                            CONCAT(last_name, ', ', first_name) AS 'deans_name' 
                                            FROM deans_credentials_tbl
                                            WHERE department_id = :department_id AND is_archived = 'No'
                                            LIMIT 1
                                        ");
$get_deans_name->execute([":department_id" => $department_id]);

if ($get_deans_name->rowCount() > 0) {
    $deans_name = $get_deans_name->fetch(PDO::FETCH_OBJ)->deans_name;
}

else {
    $deans_name = "Not Set";
}

$observation_id = isset($_GET["observation-id"]) ? base64_decode($_GET["observation-id"]) : null;

$get_observation_data = $conn->prepare("SELECT
                                                    ot.*,
                                                    ay.academic_year,
                                                    s.semester,
                                                    sub.subject_name,
                                                    CONCAT(tc.last_name, ', ', tc.first_name) AS 'teacher',
                                                    CONCAT(oc.last_name, ', ', oc.first_name) AS 'observer',
                                                    cft.*, sft.*,
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
                                                LEFT JOIN student_feedback_tbl sft
                                                ON ot.observation_id = sft.observation_id
                                                LEFT JOIN departments_tbl dt
                                                ON ot.department_id = dt.department_id
                                                WHERE ot.observation_id = :observation_id AND ot.observe_status = :observe_status
                                                ");
$get_observation_data->execute([":observation_id" => $observation_id, ":observe_status" => "Complete"]);

if ($get_observation_data->rowCount() > 0) {
    $observation_data = $get_observation_data->fetch(PDO::FETCH_OBJ);
    $copus_type = $observation_data->copus_type;

    if ($copus_type === "Summative") {

        $get_summative_data = $conn->prepare("SELECT 
                                                            *
                                                        FROM 
                                                        summative_observations_tbl
                                                        WHERE observation_id = :observation_id");

        $get_summative_data->execute([":observation_id" => $observation_id]);

        $summative_data = $get_summative_data->fetch(PDO::FETCH_OBJ);

        $high_percentage = (float)$summative_data->high_percentage;
        $summative_observation_rating = "";
        $summative_observation_label = "";

        switch (true) {
            case $high_percentage >= 75.50 && $high_percentage <= 100.00:
                $summative_observation_rating = "#AFE1AF";
                $summative_observation_label = "Great";
                break;
            case $high_percentage >= 50.00 && $high_percentage <= 72.49:
                $summative_observation_rating = "#89CFF0";
                $summative_observation_label = "Good";
                break;
            case $high_percentage >= 25.00 && $high_percentage <= 49.99:
                $summative_observation_rating = "#FDDA0D";
                $summative_observation_label = "Needs Improvement";
                break;
            case $high_percentage >= 0.00 && $high_percentage <= 24.99:
                $summative_observation_rating = "#FAA0A0";
                $summative_observation_label = "Unsatisfactory";
                break;
            default:
                $summative_observation_rating = "#FAA0A0";
                $summative_observation_label = "Unsatisfactory";
                break;
        }

        $get_feedback_data = $conn->prepare("SELECT 
                                                        * 
                                                    FROM student_feedback_tbl 
                                                    WHERE observation_id = :observation_id");
        $get_feedback_data->execute([":observation_id" => $observation_id]);

        if ($get_feedback_data->rowCount() === 0) {
            $student_feedback_percentage = 0;
            $student_feedback_rating = "#FAA0A0";
            $student_feedback_label = "Unsatisfactory";
            $nps_percentage = 0;
            $nps_rating = "#FAA0A0";
            $nps_label = "Unsatisfactory";
        } else {
            $feedback_data = $get_feedback_data->fetch(PDO::FETCH_OBJ);
            $student_feedback_percentage = (float)$feedback_data->feedback_rating;
            $student_feedback_rating = "";
            $student_feedback_label = "";

            $nps_percentage = (float)$feedback_data->net_promoter_score;
            $nps_rating = "";
            $nps_label = "";

            switch (true) {
                case $student_feedback_percentage >= 72.50 && $student_feedback_percentage <= 100.00:
                    $student_feedback_rating = "#AFE1AF";
                    $student_feedback_label = "Great";
                    break;
                case $student_feedback_percentage >= 50.00 && $student_feedback_percentage <= 72.49:
                    $student_feedback_rating = "#89CFF0";
                    $student_feedback_label = "Good";
                    break;
                case $student_feedback_percentage >= 25.00 && $student_feedback_percentage <= 49.99:
                    $student_feedback_rating = "#FDDA0D";
                    $student_feedback_label = "Needs Improvement";
                    break;
                case $student_feedback_percentage >= 0.00 && $student_feedback_percentage <= 24.99:
                    $student_feedback_rating = "#FAA0A0";
                    $student_feedback_label = "Unsatisfactory";
                    break;
                default:
                    $student_feedback_rating = "#FAA0A0";
                    $student_feedback_label = "Unsatisfactory";
                    break;
            }

            switch (true) {
                case $nps_percentage >= 72.50 && $nps_percentage <= 100.00:
                    $nps_rating = "#AFE1AF";
                    $nps_label = "Great";
                    break;
                case $nps_percentage >= 50.00 && $nps_percentage <= 72.49:
                    $nps_rating = "#89CFF0";
                    $nps_label = "Good";
                    break;
                case $nps_percentage >= 25.00 && $nps_percentage <= 49.99:
                    $nps_rating = "#FDDA0D";
                    $nps_label = "Needs Improvement";
                    break;
                case $nps_percentage >= 0.00 && $nps_percentage <= 24.99:
                    $nps_rating = "#FAA0A0";
                    $nps_label = "Unsatisfactory";
                    break;
                default:
                    $nps_rating = "#FAA0A0";
                    $nps_label = "Unsatisfactory";
                    break;
            }
        }

        $final_rating = ($student_feedback_percentage * 0.70) + ($high_percentage * 0.30);
        $final_percentage_rating = "";
        $final_percentage_label = "";

        switch (true) {
            case $final_rating >= 72.50 && $final_rating <= 100.00:
                $final_percentage_rating = "#AFE1AF";
                $final_percentage_label = "Great";
                break;
            case $final_rating >= 50.00 && $final_rating <= 72.49:
                $final_percentage_rating = "#89CFF0";
                $final_percentage_label = "Good";
                break;
            case $final_rating >= 25.00 && $final_rating <= 49.99:
                $final_percentage_rating = "#FDDA0D";
                $final_percentage_label = "Needs Improvement";
                break;
            case $final_rating >= 0.00 && $final_rating <= 24.99:
                $final_percentage_rating = "#FAA0A0";
                $final_percentage_label = "Unsatisfactory";
                break;
            default:
                $final_percentage_rating = "#FAA0A0";
                $final_percentage_label = "Unsatisfactory";
                break;
        }

        $file_name = $observation_data->teacher . "_" . $observation_data->copus_type . "_A.Y." . $academic_year . "_" . $semester . "_Observation_Summary";
    } else {
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
                                                            FROM 
                                                            student_action_log_tbl sal
                                                            LEFT JOIN student_actions_tbl sat
                                                            ON sal.action_name = sat.action_name
                                                            WHERE sal.observation_id = :observation_id");
        $get_student_action_data->execute([":observation_id" => $observation_id]);

        $student_actions = $get_student_action_data->fetchAll();

        foreach ($student_actions as $student_action) {

            if ($student_action["is_active_learning"] === "Yes") {
                $total_student_tally += (int)$student_action["tally"];
                $total_student_active_learning_tally += (int)$student_action["tally"];
            } else {
                $total_student_tally += (int)$student_action["tally"];
            }
        }

        foreach ($student_actions as $student_action) {
            $student_tally = (int)$student_action["tally"];

            if ($student_tally > 0) {
                $student_action_labels[] = htmlspecialchars($student_action["action_name"]);

                if ($total_student_tally > 0) {
                    $student_percentage = ($student_tally / $total_student_tally) * 100;
                } else {
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
            } else {
                $total_teacher_tally += (int)$teacher_action["tally"];
            }
        }

        foreach ($teacher_actions as $teacher_action) {
            $teacher_tally = (int)$teacher_action["tally"];

            if ($teacher_tally > 0) {
                $teacher_action_labels[] = htmlspecialchars($teacher_action["action_name"]);

                if ($total_teacher_tally > 0) {
                    $teacher_percentage = ($teacher_tally / $total_teacher_tally) * 100;
                } else {
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
        } else {
            $high_engagement_percentage = $medium_engagement_percentage = $low_engagement_percentage = 0;
        }

        $student_action_label_json = json_encode($student_action_labels);
        $student_action_series_json = json_encode($student_tally_series);

        $teacher_action_label_json = json_encode($teacher_action_labels);
        $teacher_action_series_json = json_encode($teacher_tally_series);

        $student_active_learning_percentage = number_format(($total_student_active_learning_tally / $total_student_tally) * 100, 2, '.');
        $teacher_active_learning_percentage = number_format(($total_teacher_active_learning_tally / $total_teacher_tally) * 100, 2, '.');

        $file_name = $observation_data->teacher . "_" . $observation_data->copus_type . "_A.Y." . $academic_year . "_" . $semester . "_Observation_Summary";
    }
} else {
    $_SESSION["query-status"] = [
        "status" => "danger",
        "message" => "Observation data not found!"
    ];

    header("Location: home.php?page=observation-records");
    exit();
}

?>

<!-- Main -->
<main id="main" class="main">

    <!-- Page Title -->
    <div class="pagetitle">

        <?php if (isset($_SESSION["query-status"]) && $_SESSION["query-status"] !== ""): ?>
            <div class="alert alert-<?php echo $_SESSION["query-status"]["status"]; ?> text-center" id="notification" role="alert">
                <?php echo $_SESSION["query-status"]["message"]; ?>
            </div>
            <?php unset($_SESSION["query-status"]); ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between">

            <div>
                <h1> <?php echo htmlspecialchars($page_titles[$page_name]); ?> </h1>

                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="home.php?page=dashboard"> Home </a></li>
                        <li class="breadcrumb-item"> <a href="home.php?page=observation-records"> Observation Records </a></li>
                        <li class="breadcrumb-item active"> <?php echo htmlspecialchars($observation_data->subject_name); ?> </li>
                    </ol>
                </nav>

                <?php
                if ($generated_password === "Yes") { ?>

                    <div class="alert alert-danger">
                        <span class="text-danger">
                            You are currently using <span class="fw-bold">system generated password</span>.
                            <a href="home.php?page=user-profile&update-password=true" class="fw-bold text-decoration-underline text-danger">Click here to change now.</a>
                        </span>
                    </div>

                <?php
                }
                ?>
            </div>

            <div>
                <h1 id="date-time" class="mt-2"> Date and Time: </h1>
            </div>

        </div>

    </div>
    <!-- End Page Title -->

    <!-- Observation Data -->
    <section class="section">

        <div class="row">

            <div class="col-lg-12">

                <div class="card shadow">

                    <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                        <h5 class="card-title custom-card-title text-white"> Observation Summary </h5>

                        <div>
                            <a class="btn btn-dark text-white" href="<?php echo htmlspecialchars($observation_data->file_path); ?>" download="<?php echo htmlspecialchars($observation_data->file_name); ?>">
                                Download Form
                            </a>

                            <button
                                class="btn btn-light"
                                onclick="exportToPDF(
                                    'observation_summary_<?php echo htmlspecialchars($observation_id); ?>',
                                    '<?php echo htmlspecialchars($file_name); ?>'
                                )">
                                Download Data
                            </button>
                        </div>

                    </div>

                    <div class="card-body" id="observation_summary_<?php echo htmlspecialchars($observation_id); ?>">

                        <?php
                        if ($copus_type === "Summative") {
                        ?>
                            <div class="mt-3 mb-3 p-2">

                                <h5 class="text-center fw-bold mb-3"> Teacher’s End-of-Semester Developmental Report </h5>

                                <div class="table-responsive">

                                    <table class="table table-bordered header-table mb-3 border border-dark">

                                        <tbody>
                                            <tr>
                                                <th class="bg-success w-25 text-white"> Semester: </th>
                                                <td class="w-25"> <?php echo htmlspecialchars($semester); ?> </td>

                                                <th class="bg-success w-25 text-white"> School Year: </th>
                                                <td class="w-25"> <?php echo htmlspecialchars($academic_year); ?> </td>

                                            <tr>
                                                <th class="bg-success w-25 text-white"> Name of Teacher (Last Name, First Name): </th>
                                                <td class="w-25"> <?php echo htmlspecialchars($observation_data->teacher); ?> </td>

                                                <th class="bg-success w-25 text-white"> College: </th>
                                                <td class="w-25"> PHINMA University of Iloilo </td>
                                            </tr>

                                            <tr>
                                                <th class="bg-success w-25 text-white"> Name of Dean (Last Name, First Name): </th>
                                                <td class="w-25"> <?php echo htmlspecialchars($deans_name); ?> </td>

                                                <th class="bg-success w-25 text-white"> Department: </th>
                                                <td class="w-25">
                                                    <?php echo htmlspecialchars($observation_data->department_name . " (" . $observation_data->department_code . ")"); ?>
                                                </td>
                                            </tr>
                                        </tbody>

                                    </table>

                                </div>

                                <p>
                                    Hello, Teacher! This report is designed to support your reflection on the
                                    effectiveness of Active Learning (AL) in your class,
                                    with insights drawn from both students and supervisors.
                                </p>

                                <p>
                                    Our criteria for effective Active Learning is that <span class="text-success fw-bold">at least 75% of students
                                        experience AL 67% of the time, or “Almost Always”</span>. This is based on established
                                    benchmarks from research and the best practices identified by Team Learning,
                                    designed to effectively support our students. The report is also a
                                    guide for you to assess your journey toward becoming a “Great” teacher—one who makes a
                                    meaningful impact by empowering students to enrich their own lives and contribute
                                    positively to others.
                                </p>

                                <p>
                                    We encourage you to use this report to (1) reflect and set personal performance goals for
                                    the next semester, and (2) track your progress in implementing impactful
                                    Active Learning practices.
                                </p>

                                <div class="bg-primary text-white fw-bold p-2 d-flex justify-content-between align-items-center p-3">
                                    <span>
                                        End-of-Semester Performance Summary
                                    </span>

                                    <?php if ($get_feedback_data->rowCount() === 0) { ?>
                                        <button class="btn btn-light texd-dark" data-bs-toggle="modal" data-bs-target="#edit-summary">
                                            Edit Summary
                                        </button>
                                    <?php } else { ?>
                                        <a href="<?php echo htmlspecialchars("../../uploads/copus/" . $observation_data->feedback_form); ?>" class="btn btn-light text-dark" download="<?php echo htmlspecialchars($observation_data->feedback_form); ?>" title="Download: <?php echo htmlspecialchars($observation_data->feedback_form); ?>"> 
                                            Download Feedback 
                                        </a>
                                    <?php } ?>
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="edit-summary" tabindex="-1">

                                    <div class="modal-dialog modal-lg modal-dialog-centered border-0">

                                        <div class="modal-content">

                                            <div class="modal-header custom-bg">
                                                <h5 class="modal-title text-white"> Add Feedback Rating and NPS </h5>
                                                <button type="button" class="btn-close h1" data-bs-dismiss="modal"></button>
                                            </div>

                                            <form action="../../process/observer/observation-management.php" method="POST" autocomplete="off" enctype="multipart/form-data">

                                                <input type="hidden" name="observation-id" value="<?php echo htmlspecialchars(base64_encode($observation_id)); ?>">
                                                <div class="modal-body">

                                                    <div class="container">

                                                        <div class="row mb-2">

                                                            <div class="col mb-3">

                                                                <label for="basic-url" class="form-label">
                                                                    Upload Your Student Feedback Data/Form
                                                                </label>

                                                                <div class="input-group">
                                                                    <input 
                                                                        type="file" 
                                                                        class="form-control" 
                                                                        id="feedBackForm" 
                                                                        accept=".xls,.xlsx,.pdf,.doc,.docx"
                                                                        name="feedback-data"
                                                                        required
                                                                        >
                                                                    <label class="input-group-text" for="feedBackForm"> Upload </label>
                                                                </div>

                                                            </div>

                                                        </div>

                                                        <div class="row mb-2">

                                                            <div class="col">
                                                                <div class="form-floating mb-1 d-flex align-items-center">
                                                                    <input type="text" class="form-control" id="feedbackRating" placeholder="Student Feedback Rating (%)" name="feedback-rating" pattern="^\d{1,3}(\.\d{1,2})?$"  title="Enter a number with 1–3 digits before the decimal and exactly 2 digits after it, e.g. 1.23, 12.34, 100.00" required>
                                                                    <label for="feedbackRating"> Student Feedback Rating (%) </label>
                                                                </div>
                                                            </div>

                                                            <div class="col">
                                                                <div class="form-floating mb-1 d-flex align-items-center">
                                                                    <input type="text" class="form-control" id="nps" placeholder="Net Promoter Score (%)" name="nps-score" pattern="^\d{1,3}(\.\d{1,2})?$"  title="Enter a number with 1–3 digits before the decimal and exactly 2 digits after it, e.g. 1.23, 12.34, 100.00"required>
                                                                    <label for="nps"> Net Promoter Score (%) </label>
                                                                </div>
                                                            </div>

                                                        </div>
             
                                                    </div>

                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> Close </button>
                                                    <button type="submit" name="update-summary" class="btn btn-primary custom-add-btn"> Update Summary </button>
                                                </div>

                                            </form>

                                        </div>

                                    </div>

                                </div>
                                <!-- End Modal -->

                                <p class="mt-0">
                                    This section summarizes your ratings from the Student Feedback (SF) and Supervisor Rating (SR).
                                    The Net Promoter Score (NPS) is also included for easier reference as you do coaching with your supervisor.
                                </p>

                                <div class="row">

                                    <div class="col-lg-6 col-md-12">

                                        <div class="table-responsive">

                                            <table class="table table-bordered table-hover header-table mb-2 border border-dark text-center">

                                                <thead>
                                                    <tr>
                                                        <th style="background-color: #B2BEB5;">
                                                            Feedback Rating
                                                        </th>

                                                        <th style="background-color: #B2BEB5;">
                                                            Rating (%)
                                                        </th>

                                                        <th style="background-color: #B2BEB5;">
                                                            Interpretation
                                                        </th>
                                                    </tr>

                                                </thead>

                                                <tbody>

                                                    <tr>
                                                        <th style="background-color:<?php echo htmlspecialchars($student_feedback_rating); ?>;">
                                                            Student Feedback Rating (70%)
                                                        </th>

                                                        <td style="background-color:<?php echo htmlspecialchars($student_feedback_rating); ?>;">
                                                            <?php echo htmlspecialchars(number_format($student_feedback_percentage, 2, '.')); ?>%
                                                        </td>

                                                        <td style="background-color:<?php echo htmlspecialchars($student_feedback_rating); ?>;">
                                                            <?php echo htmlspecialchars($student_feedback_label); ?>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th style="background-color:<?php echo htmlspecialchars($summative_observation_rating); ?>;">
                                                            Summative Observation Rating (30%)
                                                        </th>

                                                        <td style="background-color:<?php echo htmlspecialchars($summative_observation_rating); ?>;">
                                                            <?php echo htmlspecialchars(number_format($high_percentage, 2, '.')); ?>%
                                                        </td>

                                                        <td style="background-color:<?php echo htmlspecialchars($summative_observation_rating); ?>;">
                                                            <?php echo htmlspecialchars($summative_observation_label); ?>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th style="background-color:<?php echo htmlspecialchars($final_percentage_rating); ?>;">
                                                            Final Rating
                                                        </th>

                                                        <td style="background-color:<?php echo htmlspecialchars($final_percentage_rating); ?>;">
                                                            <?php echo htmlspecialchars(number_format($final_rating, 2, '.')); ?>%
                                                        </td>

                                                        <td style="background-color:<?php echo htmlspecialchars($final_percentage_rating); ?>;">
                                                            <?php echo htmlspecialchars($final_percentage_label); ?>
                                                        </td>
                                                    </tr>

                                                </tbody>

                                            </table>

                                            <table class="table table-bordered table-hover header-table mb-2 border border-dark text-center">

                                                <thead>

                                                    <tr>
                                                        <th style="background-color: #B2BEB5;">
                                                            Net Promoter Score (NPS)
                                                        </th>

                                                        <th style="background-color: #B2BEB5;">
                                                            Interpretation
                                                        </th>

                                                    </tr>

                                                </thead>

                                                <tbody>

                                                    <tr>

                                                        <th style="background-color:<?php echo htmlspecialchars($nps_rating); ?>;">
                                                            <?php echo htmlspecialchars(number_format($nps_percentage, 2, '.')); ?>%
                                                        </th>

                                                        <td style="background-color:<?php echo htmlspecialchars($nps_rating); ?>;">
                                                            <?php echo htmlspecialchars($nps_label); ?>
                                                        </td>

                                                    </tr>

                                                    <tr>
                                                        <td colspan="2" class="text-start">
                                                            Your NPS reflects <strong>students' satisfaction</strong> with
                                                            their learning experience in your class.
                                                        </td>
                                                    </tr>

                                                </tbody>

                                            </table>

                                        </div>

                                        <div class="border border-dark p-2">
                                            <p class="fw-bold"> NPS Interpretation: </p>

                                            <p class="text-success lh-1 fw-bold">70 – 100% = Great</p>
                                            <p class="text-primary lh-1 fw-bold">30% – 70% = Good</p>
                                            <p class="text-warning lh-1 fw-bold">0% – 30% = Needs Improvement</p>
                                            <p class="text-danger lh-1 fw-bold">-100% – 0% = Unsatisfactory</p>

                                        </div>

                                    </div>

                                    <div class="col-lg-6 col-md-12">

                                        <div class="table-responsive">

                                            <table class="table table-bordered table-hover header-table mb-3 border border-dark text-center">

                                                <thead>

                                                    <tr>
                                                        <th style="background-color: #B2BEB5;">
                                                            What does my <span class="text-decoration-underline">final rating</span> say about my
                                                            Active Learning practice this semester?
                                                        </th>
                                                    </tr>

                                                </thead>

                                                <tbody>

                                                    <tr>
                                                        <td class="text-start fw-bold" style="background-color: #AFE1AF;"> 72.50% - 100%: Great </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-start" style="background-color: #AFE1AF;">
                                                            Congratulations! You engaged students in highly effective and meaningful learning experiences.
                                                            Keep reflecting on these strong practices and share them with your colleagues.
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-start fw-bold" style="background-color: #89CFF0;"> 50.00% - 72.49%: Good </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-start" style="background-color: #89CFF0;">
                                                            Well done! You’re on your way to becoming a Great teacher! Reflect on how to capitalize on
                                                            your strengths and work on additional ways to support student involvement in their learning process.
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-start fw-bold" style="background-color: #FDDA0D;"> 25.00% - 49.99%: Good </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-start" style="background-color: #FDDA0D;">
                                                            On the right track! You have some Active Learning strategies in place, but engagement remains limited.
                                                            Reflect on how to more effectively provide opportunities for students to actively engage in their own learning.
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-start fw-bold" style="background-color: #FAA0A0;"> 0.00% - 24.99%: Unsatisfactory </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-start" style="background-color: #FAA0A0;">
                                                            Let’s put in some extra effort! Currently, your ratings do not reflect our goal of actively
                                                            engaging students in their learning process. Work with your supervisor and AL Coach to
                                                            plan strategies that will enhance student engagement and create a more interactive
                                                            learning environment.
                                                        </td>
                                                    </tr>

                                                </tbody>

                                            </table>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <section class="section">

                                <div class="row mt-2">
                                    <h5 class="fs-lg-4 fs-md-5 fs-sm-6"> Comments: </h5>

                                    <?php
                                    $get_comments = $conn->prepare("SELECT * FROM observation_comments_tbl WHERE observation_id = :observation_id");
                                    $get_comments->execute([":observation_id" => $observation_id]);

                                    if ($get_comments->rowCount() > 0) {
                                        $comments = [];
                                        while ($comment_data = $get_comments->fetch()) {
                                            $comments[] = htmlspecialchars($comment_data["observation_comment"]);
                                        }

                                    ?>
                                        <p> <?php echo implode(", ", $comments); ?>. </p>
                                    <?php
                                    } else {
                                    ?>
                                        <p> No comments. </p>
                                    <?php
                                    }
                                    ?>
                                </div>

                            </section>
                        <?php
                        } else {
                        ?>
                            <div class="mt-3 mb-3 p-2">

                                <div class="row">

                                    <div class="col-lg-6 col-md-12">
                                        <h5> Academic Year and Semester: <span class="fw-bold"><?php echo htmlspecialchars($observation_data->academic_year . " | " . $observation_data->semester); ?></span> </h5>
                                    </div>

                                    <div class="col-lg-6 col-md-12">
                                        <h5> COPUS Type: <span class="fw-bold"><?php echo htmlspecialchars($observation_data->copus_type); ?></span> </h5>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col">
                                        <h5> Year Level: <span class="fw-bold"><?php echo htmlspecialchars($observation_data->year_level); ?></span> </h5>
                                    </div>

                                    <div class="col">
                                        <h5> Modality: <span class="fw-bold"><?php echo htmlspecialchars($observation_data->modality); ?></span> </h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <h5> Subject: <span class="fw-bold"><?php echo htmlspecialchars($observation_data->subject_name); ?></span> </h5>
                                    </div>

                                    <div class="col">
                                        <h5> Teacher: <span class="fw-bold"><?php echo htmlspecialchars($observation_data->teacher); ?></span> </h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <h5> Department: <span class="fw-bold"><?php echo htmlspecialchars($observation_data->department_name . " (" . $observation_data->department_code . ")"); ?></span> </h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <h5> Observer: <span class="fw-bold"><?php echo htmlspecialchars($observation_data->observer); ?></span> </h5>
                                    </div>

                                    <div class="col">
                                        <h5> Observed at: <span class="fw-bold"><?php echo htmlspecialchars(format_timestamp($observation_data->observed_at)); ?></span> </h5>
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
                                        <button class="w-100 nav-link active" data-bs-toggle="tab" data-bs-target="#chart-view"> Chart View </button>
                                    </li>

                                    <li class="nav-item w-50">
                                        <button class="w-100 nav-link" data-bs-toggle="tab" data-bs-target="#table-view"> Table View </button>
                                    </li>

                                </ul>

                                <div class="tab-content">

                                    <div class="tab-pane fade show active" id="chart-view">

                                        <div class="row">

                                            <div class="col-lg-6 col-md-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title"> Student Actions Data </h5>

                                                        <!-- Pie Chart -->
                                                        <div id="studentActionChart"></div>
                                                        <!-- End Pie Chart -->

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-md-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title"> Teacher Actions Data </h5>

                                                        <!-- Pie Chart -->
                                                        <div id="teacherActionChart"></div>
                                                        <!-- End Pie Chart -->

                                                    </div>
                                                </div>
                                            </div>

                                            <script>
                                                document.addEventListener("DOMContentLoaded", function() {
                                                    convertDataToChart(
                                                        "studentActionChart",
                                                        <?php echo $student_action_label_json; ?>,
                                                        <?php echo $student_action_series_json; ?>,
                                                        <?php echo json_encode($colors_pool); ?>,
                                                        "Student Actions Data",
                                                        "pie"
                                                    );

                                                    convertDataToChart(
                                                        "teacherActionChart",
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

                                    <div class="tab-pane fade" id="table-view">

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

                                <div class="row mt-2">
                                    <h5 class="fs-lg-4 fs-md-5 fs-sm-6"> Comments: </h5>

                                    <?php
                                    $get_comments = $conn->prepare("SELECT * FROM observation_comments_tbl WHERE observation_id = :observation_id");
                                    $get_comments->execute([":observation_id" => $observation_id]);

                                    if ($get_comments->rowCount() > 0) {
                                        $comments = [];
                                        while ($comment_data = $get_comments->fetch()) {
                                            $comments[] = htmlspecialchars($comment_data["observation_comment"]);
                                        }

                                    ?>
                                        <p> <?php echo implode(", ", $comments); ?>. </p>
                                    <?php
                                    } else {
                                    ?>
                                        <p> No comments. </p>
                                    <?php
                                    }
                                    ?>
                                </div>

                            </section>
                        <?php
                        }
                        ?>

                    </div>

                </div>

            </div>

        </div>

    </section>
    <!-- End Observation Data -->

</main>
<!-- End #main -->