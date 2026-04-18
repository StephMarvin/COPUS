<?php
// Teacher Action Averages
$get_teacher_action_averages = $conn->prepare("SELECT
                                                            ta.action_name,
                                                            ROUND(AVG(ta.tally), 2) AS 'avg_tally'
                                                            FROM teacher_action_log_tbl ta
                                                            LEFT JOIN observations_tbl ot
                                                            ON ta.observation_id = ot.observation_id
                                                            WHERE ot.semester_id = :semester_id
                                                            GROUP BY action_name
                                                            ORDER BY avg_tally DESC
                                                            ");
$get_teacher_action_averages->execute([":semester_id" => $semester_id]);

$teacher_actions = $get_teacher_action_averages->fetchAll();

$teacher_label = [];
$teacher_action_avg_tally = [];

$total_teacher_tally = 0;

foreach ($teacher_actions as $teacher_action) {
    $teacher_label[] = $teacher_action["action_name"];
    $total_teacher_tally += (float)$teacher_action["avg_tally"];
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
            <h5 class="card-title custom-card-title text-white"> Teacher Actions Averages This Semester </h5>

            <?php
            if ($teacher_label && $teacher_action_avg_tally) { ?>

                <button
                    class="btn btn-light"
                    onclick="exportToPDF(
                                        'averageTeacherActionChart',
                                        '<?php echo htmlspecialchars('Teacher_Actions_Averages_A_Y_' . $academic_year . '_' . $semester); ?>',
                                        'l'
                                    )">
                    Download Chart
                </button>

            <?php } ?>
        </div>

        <?php
        if ($teacher_label && $teacher_action_avg_tally) { ?>

            <div class="card-body mt-3">
                <div class="row">

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"> Teacher Actions Averages </h5>

                                <!-- Bar Chart -->
                                <div id="averageTeacherActionChart"></div>
                                <!-- End Bar Chart -->

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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        convertDataToChart(
            "averageTeacherActionChart",
            <?php echo $teacher_label_json; ?>,
            <?php echo $teacher_avg_tally_json; ?>,
            <?php echo json_encode($colors_pool); ?>,
            "Teacher Actions Averages: A.Y. <?php echo htmlspecialchars($academic_year . ", " . $semester); ?>",
            "bar"
        );
    });
</script>