<?php
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["filter-year-sem-data"])) {

        $current_department_id = htmlspecialchars(trim($_POST["department-id"]));
        $current_academic_year = htmlspecialchars($_POST["academic-year"]);
        $current_semester = htmlspecialchars($_POST["semester"]);
    } else {

        $current_academic_year = $academic_year;
        $current_semester = $semester;

        $get_current_department_id = $conn->prepare("SELECT * FROM departments_tbl LIMIT 1");
        $get_current_department_id->execute();

        $current_department_id = $get_current_department_id->fetch()["department_id"] ?? null;
    }

    // student Action Averages
    $get_student_action_averages = $conn->prepare("SELECT
                                                                        tl.action_name,
                                                                        ROUND(AVG(tl.tally), 2) AS 'avg_tally',
                                                                        dt.department_code
                                                                        FROM student_action_log_tbl tl
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
    $get_student_action_averages->execute([
        ":academic_year" => $current_academic_year,
        ":semester" => $current_semester,
        ":department_id" => $current_department_id
    ]);

    $student_label = [];
    $student_action_avg_tally = [];

    $total_student_tally = 0;

    $student_actions = $get_student_action_averages->fetchAll();

    $department_code = $student_actions[0]["department_code"] ?? "";

    foreach ($student_actions as $student_action) {
        $student_label[] = $student_action["action_name"];
        $total_student_tally += (float)$student_action["avg_tally"];

        $student_dl_title = $department_code . "_Student_Actions_Averages_A_Y_" . $current_academic_year . "_" . $current_semester;
        $student_chart_title = $department_code . " Student Actions Averages: A.Y. " . $current_academic_year . ", " . $current_semester;
    }

    foreach ($student_actions as $student_action) {
        $student_avg_tally = (float)$student_action["avg_tally"];

        if ($student_avg_tally > 0) {
            $student_tally_percentage = ($student_avg_tally / $total_student_tally) * 100;
        } else {
            $student_tally_percentage = 0;
        }

        $student_action_avg_tally[] = round($student_tally_percentage, 2);
    }

    // Convert student Averages to JSON
    $student_label_json = json_encode($student_label);
    $student_avg_tally_json = json_encode($student_action_avg_tally);
?>

<div class="col-12">

    <div class="card shadow mb-3">

        <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
            <h5 class="card-title custom-card-title text-white">
                Student Actions Stats
            </h5>

            <div class="d-flex justify-content-between align-items-center gap-3">

                    <div>

                        <form action="home.php?page=student-stats" method="POST" class="d-flex justify-content-between align-items-center gap-2">

                            <?php
                                $fetch_departments = $conn->prepare("SELECT * FROM departments_tbl");
                                $fetch_departments->execute();

                                if($fetch_departments->rowCount() > 0):
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
                                $fetch_academic_years = $conn->prepare("SELECT * FROM academic_years_tbl ORDER BY academic_year ASC");
                                $fetch_academic_years->execute();
                            ?>
                                <select class="form-select" name="academic-year">
                            <?php  

                                while ($academic_years = $fetch_academic_years->fetch()) {
                                ?>
                                    <option value="<?php echo htmlspecialchars($academic_years["academic_year"]); ?>" <?php echo htmlspecialchars($academic_years["academic_year"] === $current_academic_year ? "selected" : ""); ?>>
                                        <?php echo htmlspecialchars($academic_years["academic_year"]); ?>
                                    </option>
                            <?php
                                }
                            ?>
                            </select>
                        
                                <select class="form-select" name="semester">
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
                if ($student_label && $student_action_avg_tally) { ?>

                    <button
                        class="btn btn-light"
                        onclick="exportToPDF(
                                        'averagestudentActionChart',
                                        '<?php echo htmlspecialchars($student_dl_title); ?>,',
                                        'l'
                                    )">
                        Download Chart
                    </button>

                <?php } ?>

            </div>

        </div>

        <?php
        if ($student_label && $student_action_avg_tally) { ?>

            <div class="card-body mt-3">
                <div class="row">

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    Student Actions Averages
                                </h5>

                                <!-- Bar Chart -->
                                <div id="averagestudentActionChart"></div>
                                <!-- End Bar Chart -->

                                <!-- Charts -->
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        convertDataToChart(
                                            "averagestudentActionChart",
                                            <?php echo $student_label_json; ?>,
                                            <?php echo $student_avg_tally_json; ?>,
                                            <?php echo json_encode($colors_pool); ?>,
                                            "<?php echo htmlspecialchars($student_chart_title); ?>",
                                            "bar"
                                        );
                                    });
                                </script>
                                <!-- End Charts -->

                            </div>
                        </div>
                    </div>

                </div>
            </div>

        <?php } else { ?>

            <div class="card-body d-flex justify-content-between align-items-center mt-3">
                <h4 class="mb-0 fw-bold"> No data. </h4>
            </div>

        <?php } ?>

    </div>

</div>