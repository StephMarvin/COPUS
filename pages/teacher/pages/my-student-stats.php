<?php
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["select-teacher"])) {

        $current_academic_year_selected = htmlspecialchars($_POST["academic-year"]);
        $current_semester_selected = htmlspecialchars($_POST["semester"]);
    } 
    
    else {
        
        $current_academic_year_selected = $academic_year;
        $current_semester_selected  = $semester;
    }

    // Teacher Student Action Averages
    $get_current_student_action_averages = $conn->prepare("SELECT
                                                                                tl.action_name,
                                                                                ROUND(AVG(tl.tally), 2) AS 'avg_tally'                                                            
                                                                            FROM student_action_log_tbl tl
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
    $get_current_student_action_averages->execute([
        ":academic_year" => $current_academic_year_selected,
        ":semester" => $current_semester_selected,
        ":id_number" => $id_number
    ]);

    $current_student_label = [];
    $current_student_action_avg_tally = [];

    $current_total_student_tally = 0;

    $current_student_actions = $get_current_student_action_averages->fetchAll();

    foreach ($current_student_actions as $current_student_action) {
        $current_student_label[] = $current_student_action["action_name"];
        $current_total_student_tally += (float)$current_student_action["avg_tally"];

        $current_student_dl_title = $last_name . "_" . $first_name . "_" . "Student_Actions_Averages_A_Y_" . $current_academic_year_selected . "_" . $current_semester_selected;
        $current_student_chart_title = $last_name . ", " . $first_name . ": Student Actions Averages: A.Y. " . $current_academic_year_selected . ", " . $current_semester_selected;
    }

    foreach ($current_student_actions as $current_student_action) {
        $current_student_avg_tally = (float)$current_student_action["avg_tally"];

        if ($current_student_avg_tally > 0) {
            $current_student_tally_percentage = ($current_student_avg_tally / $current_total_student_tally) * 100;
        } else {
            $current_student_tally_percentage = 0;
        }

        $current_student_action_avg_tally[] = round($current_student_tally_percentage, 2);
    }

    // Convert Student Averages to JSON
    $current_student_label_json = json_encode($current_student_label);
    $current_student_avg_tally_json = json_encode($current_student_action_avg_tally);
?>

<div class="main" id="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <div class="section dashboard">

        <div class="row">

            <!-- Student Action Stats -->
            <div class="col-12">

                <div class="card shadow mb-3">

                    <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                        <h5 class="card-title custom-card-title text-white">
                            My Student Actions Stats
                        </h5>

                        <div class="d-flex justify-content-between align-items-center gap-3">

                            <div>

                                <form action="home.php?page=my-student-stats" method="POST" class="d-flex justify-content-between align-items-center gap-2">

                                <?php
                                    $fetch_academic_years = $conn->prepare("SELECT * FROM academic_years_tbl ORDER BY academic_year ASC");
                                    $fetch_academic_years->execute();
                                    if($fetch_academic_years->rowCount() > 0):
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
                            if ($current_student_label && $current_student_action_avg_tally) { ?>

                                <button
                                    class="btn btn-light"
                                    onclick="exportToPDF(
                                            'averageCurrentStudentActionChart',
                                            '<?php echo htmlspecialchars($current_student_dl_title); ?>,',
                                            'l'
                                        )">
                                    Download Chart
                                </button>

                            <?php } ?>

                        </div>
                    </div>

                    <?php
                    if ($current_student_label && $current_student_action_avg_tally) { ?>

                        <div class="card-body mt-3">
                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                Student Actions Averages
                                            </h5>

                                            <!-- Bar Chart -->
                                            <div id="averageCurrentStudentActionChart"></div>
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
                                    "averageCurrentStudentActionChart",
                                    <?php echo $current_student_label_json; ?>,
                                    <?php echo $current_student_avg_tally_json; ?>,
                                    <?php echo json_encode($colors_pool); ?>,
                                    "<?php echo htmlspecialchars($current_student_chart_title); ?>",
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
            <!-- End Student Action Stats -->

        </div>

    </div>

</div>
