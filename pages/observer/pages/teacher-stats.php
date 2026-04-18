<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["filter-year-sem-data"])) {

    $current_academic_year = htmlspecialchars($_POST["selected-academic-year"]);
    $current_semester = htmlspecialchars($_POST["selected-semester"]);
} else {

    $current_academic_year = $academic_year;
    $current_semester = $semester;
}

// Teacher Action Averages
$get_teacher_action_averages = $conn->prepare("SELECT
                                                                    tl.action_name,
                                                                    ROUND(AVG(tl.tally), 2) AS 'avg_tally'
                                                                FROM teacher_action_log_tbl tl
                                                                LEFT JOIN observations_tbl o
                                                                ON tl.observation_id = o.observation_id
                                                                LEFT JOIN semesters_tbl s
                                                                ON o.semester_id = s.semester_id
                                                                LEFT JOIN academic_years_tbl ay
                                                                ON s.academic_year_id = ay.academic_year_id
                                                                WHERE ay.academic_year = :academic_year AND 
                                                                s.semester = :semester AND
                                                                o.department_id = :department_id
                                                                GROUP BY tl.action_name
                                                                ORDER BY avg_tally DESC
                                                        ");
$get_teacher_action_averages->execute([
    ":academic_year" => $current_academic_year,
    ":semester" => $current_semester,
    ":department_id" => $department_id
]);

$teacher_label = [];
$teacher_action_avg_tally = [];

$total_teacher_tally = 0;

    $teacher_actions = $get_teacher_action_averages->fetchAll();

    foreach ($teacher_actions as $teacher_action) {
        $teacher_label[] = $teacher_action["action_name"];
        $total_teacher_tally += (float)$teacher_action["avg_tally"];

        $teacher_dl_title = $department_code . "_Teacher_Actions_Averages_A_Y_" . $current_academic_year . "_" . $current_semester;
        $teacher_chart_title = "Teacher Actions Averages: A.Y. " . $current_academic_year . ", " . $current_semester;
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

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["select-teacher"])) {

        $scroll_to_chart = isset($_POST["scroll-to-chart"]);

        $current_teacher = htmlspecialchars(trim($_POST["teacher-id"]));
        $current_academic_year_selected = htmlspecialchars($_POST["academic-year"]);
        $current_semester_selected = htmlspecialchars($_POST["semester"]);
    } else {
        $get_current_teacher = $conn->prepare("SELECT id_number FROM teacher_credentials_tbl WHERE department_id = :department_id ORDER BY last_name ASC LIMIT 1");
        $get_current_teacher->execute([":department_id" => $department_id]);

        $current_teacher = $get_current_teacher->fetch()["id_number"] ?? null;

        $current_academic_year_selected = $academic_year;
        $current_semester_selected  = $semester;
    }

    $get_teacher_name = $conn->prepare("SELECT last_name, first_name FROM teacher_credentials_tbl WHERE id_number = :id_number LIMIT 1");
    $get_teacher_name->execute([":id_number" => $current_teacher]);

    if ($get_teacher_name->rowCount() > 0):
        $teacher_name_data = $get_teacher_name->fetch(PDO::FETCH_OBJ);
        $teacher_name = $teacher_name_data->last_name . ", " . $teacher_name_data->first_name;
    endif;

    // Teacher Action Averages
    $get_current_teacher_action_averages = $conn->prepare("SELECT
                                                                            tl.action_name,
                                                                            ROUND(AVG(tl.tally), 2) AS 'avg_tally'                                                            FROM teacher_action_log_tbl tl
                                                                        LEFT JOIN observations_tbl o
                                                                        ON tl.observation_id = o.observation_id
                                                                        LEFT JOIN semesters_tbl s
                                                                        ON o.semester_id = s.semester_id
                                                                        LEFT JOIN academic_years_tbl ay
                                                                        ON s.academic_year_id = ay.academic_year_id      
                                                                        WHERE ay.academic_year = :academic_year AND s.semester = :semester AND o.teacher_id = :id_number
                                                                        GROUP BY tl.action_name
                                                                        ORDER BY avg_tally DESC
                                                                            ");
    $get_current_teacher_action_averages->execute([
        ":academic_year" => $current_academic_year_selected,
        ":semester" => $current_semester_selected,
        ":id_number" => $current_teacher
    ]);

    $current_teacher_label = [];
    $current_teacher_action_avg_tally = [];

    $current_total_teacher_tally = 0;

    $current_teacher_actions = $get_current_teacher_action_averages->fetchAll();

    foreach ($current_teacher_actions as $current_teacher_action) {
        $current_teacher_label[] = $current_teacher_action["action_name"];
        $current_total_teacher_tally += (float)$current_teacher_action["avg_tally"];

        $current_teacher_dl_title = $teacher_name_data->last_name . "_" . $teacher_name_data->first_name . "_" . "Teacher_Actions_Averages_A_Y_" . $current_academic_year_selected . "_" . $current_semester_selected;
        $current_teacher_chart_title = $teacher_name . ": Teacher Actions Averages: A.Y. " . $current_academic_year_selected . ", " . $current_semester_selected;
    }

    foreach ($current_teacher_actions as $current_teacher_action) {
        $current_teacher_avg_tally = (float)$current_teacher_action["avg_tally"];

        if ($current_teacher_avg_tally > 0) {
            $current_teacher_tally_percentage = ($current_teacher_avg_tally / $current_total_teacher_tally) * 100;
        } else {
            $current_teacher_tally_percentage = 0;
        }

        $current_teacher_action_avg_tally[] = round($current_teacher_tally_percentage, 2);
    }

    // Convert Teacher Averages to JSON
    $current_teacher_label_json = json_encode($current_teacher_label);
    $current_teacher_avg_tally_json = json_encode($current_teacher_action_avg_tally);
?>


<main class="main" id="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Dashboard Main -->
    <section class="section dashboard">

        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">

                <div class="row">

                    <!-- Teacher Actions -->
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
                                            $fetch_all_academic_years = $conn->prepare("SELECT * FROM academic_years_tbl ORDER BY academic_year ASC");
                                            $fetch_all_academic_years->execute();

                                            if ($fetch_all_academic_years->rowCount() > 0):
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
                    <!-- End Teacher Actions -->

                </div>

                <div class="row">

                    <!-- Current Teacher Actions -->
                    <div class="col-12" id="current-teacher-averages">

                        <div class="card shadow mb-3">

                            <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">

                                <h5 class="card-title custom-card-title text-white">
                                    Teacher Stats
                                </h5>

                                <div class="d-flex justify-content-between align-items-center gap-3">

                                    <div>

                                        <form action="home.php?page=teacher-stats" method="POST" class="d-flex justify-content-between align-items-center gap-2">

                                            <input type="hidden" name="scroll-to-chart" value="true">

                                            <?php
                                            $fetch_teachers = $conn->prepare("SELECT id_number, first_name, last_name FROM teacher_credentials_tbl WHERE department_id = :department_id ORDER BY last_name ASC");
                                            $fetch_teachers->execute([":department_id" => $department_id]);
                                            if ($fetch_teachers->rowCount() > 0):
                                            ?>
                                                <select class="form-select" name="teacher-id">
                                                    <?php
                                                    while ($teacher_data = $fetch_teachers->fetch()) {
                                                    ?>
                                                        <option value="<?php echo htmlspecialchars($teacher_data["id_number"]); ?>" <?php echo htmlspecialchars($teacher_data["id_number"] === $current_teacher ? "selected" : ""); ?>>
                                                            <?php echo htmlspecialchars($teacher_data["last_name"] . ", " . $teacher_data["first_name"]); ?>
                                                        </option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>

                                            <?php
                                            $fetch_academic_years = $conn->prepare("SELECT * FROM academic_years_tbl ORDER BY academic_year ASC");
                                            $fetch_academic_years->execute();
                                            ?>
                                                <select class="form-select" name="academic-year">
                                                    <?php

                                                    while ($academic_years = $fetch_academic_years->fetch()) {
                                                    ?>
                                                        <option value="<?php echo htmlspecialchars($academic_years["academic_year"]); ?>" <?php echo htmlspecialchars($academic_years["academic_year"] === $current_academic_year_selected ? "selected" : ""); ?>>
                                                            <?php echo htmlspecialchars($academic_years["academic_year"]); ?>
                                                        </option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>

                                                <select class="form-select" name="semester">
                                                    <option value="1st Semester" <?php echo htmlspecialchars($current_semester_selected === "1st Semester" ? "selected" : ""); ?>>
                                                        1st Semester
                                                    </option>

                                                    <option value="2nd Semester" <?php echo htmlspecialchars($current_semester_selected === "2nd Semester" ? "selected" : ""); ?>>
                                                        2nd Semester
                                                    </option>
                                                </select>

                                                <button type="submit" name="select-teacher" class="btn btn-light">
                                                    Filter
                                                </button>
                                            <?php
                                            endif;
                                            ?>

                                        </form>

                                    </div>

                                    <?php
                                    if ($current_teacher_label && $current_teacher_action_avg_tally) { ?>

                                        <button
                                            class="btn btn-light"
                                            onclick="exportToPDF(
                                            'averageCurrentTeacherActionChart',
                                            '<?php echo htmlspecialchars($current_teacher_dl_title); ?>,',
                                            'l'
                                        )">
                                            Download Chart
                                        </button>

                                    <?php } ?>

                                </div>
                            </div>

                            <?php
                            if ($current_teacher_label && $current_teacher_action_avg_tally) { ?>

                                <div class="card-body mt-3">
                                    <div class="row">

                                        <div class="col-lg-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        Teacher Actions Averages
                                                    </h5>

                                                    <!-- Bar Chart -->
                                                    <div id="averageCurrentTeacherActionChart"></div>
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
                                            "averageCurrentTeacherActionChart",
                                            <?php echo $current_teacher_label_json; ?>,
                                            <?php echo $current_teacher_avg_tally_json; ?>,
                                            <?php echo json_encode($colors_pool); ?>,
                                            "<?php echo htmlspecialchars($current_teacher_chart_title); ?>",
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
                    <!-- End Teacher Actions -->

                </div>

            </div>
            <!-- Left Side Columns -->

        </div>

    </section>
    <!-- End Dashboard Main -->

</main>

<?php if (!empty($scroll_to_chart)) : ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chartSection = document.getElementById("current-teacher-averages");
            if (chartSection) {
                chartSection.scrollIntoView({
                    behavior: "instant",
                    block: "start"
                });
            }
        });
    </script>
<?php endif; ?>