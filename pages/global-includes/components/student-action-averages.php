<?php
  // Student Action Averages
  $get_student_action_averages = $conn->prepare("SELECT
                                                          action_name,
                                                          ROUND(AVG(tally), 2) AS 'avg_tally'
                                                        FROM student_action_log_tbl
                                                        GROUP BY action_name
                                                        ORDER BY avg_tally DESC
                                                        ");
  $get_student_action_averages->execute();

  $student_actions = $get_student_action_averages->fetchAll();

  $student_label = [];
  $student_action_avg_tally = [];

  $total_student_tally = 0;

  foreach($student_actions as $student_action) {
    $student_label[] = $student_action["action_name"];
    $total_student_tally += (float)$student_action["avg_tally"];
  }

  foreach($student_actions as $student_action) {
    $student_avg_tally = (float)$student_action["avg_tally"];

    if($student_avg_tally > 0) {
      $student_tally_percentage = ($student_avg_tally / $total_student_tally) * 100;
    }
    
    else {
      $student_tally_percentage = 0;
    }

    $student_action_avg_tally[] = round($student_tally_percentage, 2);
  }

  // Convert Student Averages to JSON
  $student_label_json = json_encode($student_label);
  $student_avg_tally_json = json_encode($student_action_avg_tally);
?>

<div class="col-12">

    <div class="card shadow mb-3">

        <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
            <h5 class="card-title custom-card-title text-white"> Student Action Averages </h5>

            <?php
            if ($student_label && $student_action_avg_tally) { ?>
                <button
                    class="btn btn-light"
                    onclick="exportToPDF(
                              'averageStudentActionChart',
                              'Student_Actions_Average_Per_Observation',
                              'l'
                          )">
                    Download Chart
                </button>
            <?php }
            ?>
        </div>

        <?php
        if ($student_label && $student_action_avg_tally) { ?>

            <div class="card-body mt-3">
                <div class="row">

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"> Student Actions Averages </h5>

                                <!-- Bar Chart -->
                                <div id="averageStudentActionChart"></div>
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
            "averageStudentActionChart",
            <?php echo $student_label_json; ?>,
            <?php echo $student_avg_tally_json; ?>,
            <?php echo json_encode($colors_pool); ?>,
            "Student Actions Averages Per Observation",
            "bar"
        )
    });
</script>