<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["filter-year-sem-data"])) {

    $current_department_id = htmlspecialchars($_POST["department-id"]);
    $current_academic_year = htmlspecialchars($_POST["selected-academic-year"]);
    $current_semester = htmlspecialchars($_POST["selected-semester"]);
} else {

    $current_academic_year = $academic_year;
    $current_semester = $semester;

    $get_current_department_id = $conn->prepare("SELECT * FROM departments_tbl LIMIT 1");
    $get_current_department_id->execute();

    $current_department_id = $get_current_department_id->fetch()["department_id"] ?? null;
}

// Teacher Action Averages
$get_teacher_action_averages = $conn->prepare("SELECT
                                                                    tl.action_name,
                                                                    ROUND(AVG(tl.tally), 2) AS 'avg_tally',
                                                                    dt.department_code
                                                                FROM teacher_action_log_tbl tl
                                                                LEFT JOIN observations_tbl o
                                                                ON tl.observation_id = o.observation_id
                                                                LEFT JOIN departments_tbl dt
                                                                ON o.department_id = dt.department_id
                                                                LEFT JOIN semesters_tbl s
                                                                ON o.semester_id = s.semester_id
                                                                LEFT JOIN academic_years_tbl ay
                                                                ON s.academic_year_id = ay.academic_year_id
                                                                WHERE ay.academic_year = :academic_year AND s.semester = :semester AND o.department_id = :department_id
                                                                GROUP BY tl.action_name
                                                                ORDER BY avg_tally DESC
                                                        ");
$get_teacher_action_averages->execute([
    ":academic_year" => $current_academic_year,
    ":semester" => $current_semester,
    ":department_id" => $current_department_id
]);

$teacher_label = [];
$teacher_action_avg_tally = [];

$total_teacher_tally = 0;

$teacher_actions = $get_teacher_action_averages->fetchAll();

$department_code = $teacher_actions[0]["department_code"] ?? "";

foreach ($teacher_actions as $teacher_action) {
    $teacher_label[] = $teacher_action["action_name"];
    $total_teacher_tally += (float)$teacher_action["avg_tally"];

    $teacher_dl_title = $department_code . "_Teacher_Actions_Averages_A_Y_" . $current_academic_year . "_" . $current_semester;
    $teacher_chart_title = $department_code . " Teacher Actions Averages: A.Y. " . $current_academic_year . ", " . $current_semester;
}

foreach ($teacher_actions as $teacher_action) {
    $teacher_avg_tally = (float)$teacher_action["avg_tally"];

    if ($teacher_avg_tally > 0) {
        $teacher_tally_percentage = ($teacher_avg_tally / $total_teacher_tally) * 100;
    } else {
        $teacher_tally_percentage = 0;
    }

    $teacher_action_avg_tally[] = round($teacher_tally_percentage, 2);
}

// Convert Teacher Averages to JSON
$teacher_label_json = json_encode($teacher_label);
$teacher_avg_tally_json = json_encode($teacher_action_avg_tally);
?>

<div class="col-12">

    <div class="card shadow mb-3">

        <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
            <h5 class="card-title custom-card-title text-white">
                Teacher Action Stats
            </h5>

            <div class="d-flex justify-content-between align-items-center gap-3">

                <div>

                    <form action="home.php?page=teacher-stats" method="POST" class="d-flex justify-content-between align-items-center gap-2">

                        <?php
                        $fetch_departments = $conn->prepare("SELECT * FROM departments_tbl");
                        $fetch_departments->execute();

                        if ($fetch_departments->rowCount() > 0):
                        ?>
                            <select class="form-select" name="department-id">
                                <?php

                                while ($department_data = $fetch_departments->fetch()) {
                                ?>
                                    <option value="<?php echo htmlspecialchars($department_data["department_id"]); ?>" <?php echo htmlspecialchars($department_data["department_id"] == $current_department_id ? "selected" : ""); ?>>
                                        <?php echo htmlspecialchars($department_data["department_code"]); ?>
                                    </option>
                            <?php
                                }
                            ?>
                            </select>

                            <?php
                            $fetch_all_academic_years = $conn->prepare("SELECT * FROM academic_years_tbl ORDER BY academic_year ASC");
                            $fetch_all_academic_years->execute();

                            ?>
                                <select class="form-select" name="selected-academic-year">
                                    <?php

                                    while ($all_academic_years = $fetch_all_academic_years->fetch()) {
                                    ?>
                                        <option value="<?php echo htmlspecialchars($all_academic_years["academic_year"]); ?>" <?php echo htmlspecialchars($all_academic_years["academic_year"] === $current_academic_year ? "selected" : ""); ?>>
                                            <?php echo htmlspecialchars($all_academic_years["academic_year"]); ?>
                                        </option>
                                    <?php
                                    }
                                    ?>
                                </select>

                                <select class="form-select" name="selected-semester">
                                    <option value="1st Semester" <?php echo htmlspecialchars($current_semester === "1st Semester" ? "selected" : ""); ?>>
                                        1st Semester
                                    </option>

                                    <option value="2nd Semester" <?php echo htmlspecialchars($current_semester === "2nd Semester" ? "selected" : ""); ?>>
                                        2nd Semester
                                    </option>
                                </select>

                                <button type="submit" name="filter-year-sem-data" class="btn btn-light">
                                    Filter
                                </button>
                            <?php
                            endif;
                            ?>

                    </form>

                </div>

                <?php
                if ($teacher_label && $teacher_action_avg_tally) { ?>

                    <button
                        class="btn btn-light"
                        onclick="exportToPDF(
                                        'averageTeacherActionChart',
                                        '<?php echo htmlspecialchars($teacher_dl_title); ?>,',
                                        'l'
                                    )">
                        Download Chart
                    </button>

                <?php } ?>

            </div>

        </div>

        <?php
        if ($teacher_label && $teacher_action_avg_tally) { ?>

            <div class="card-body mt-3">
                <div class="row">

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    Teacher Action Averages
                                </h5>

                                <!-- Bar Chart -->
                                <div id="averageTeacherActionChart"></div>
                                <!-- End Bar Chart -->

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Charts -->
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    convertDataToChart(
                        "averageTeacherActionChart",
                        <?php echo $teacher_label_json; ?>,
                        <?php echo $teacher_avg_tally_json; ?>,
                        <?php echo json_encode($colors_pool); ?>,
                        "<?php echo htmlspecialchars($teacher_chart_title); ?>",
                        "bar"
                    );
                });
            </script>
            <!-- End Charts -->

        <?php } else { ?>

            <div class="card-body d-flex justify-content-between align-items-center mt-3">
                <h4 class="mb-0 fw-bold"> No data. </h4>
            </div>

        <?php } ?>

    </div>

</div>