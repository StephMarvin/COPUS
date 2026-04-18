<?php
$get_observations_this_sem = $conn->prepare("SELECT 
                                                        COUNT(*) AS 'observation_count'
                                                      FROM observations_tbl 
                                                      WHERE teacher_id = :teacher_id 
                                                      AND observe_status = :observe_status
                                                      AND semester_id = :semester_id
                                                      ");
$get_observations_this_sem->execute([
  ":teacher_id" => $id_number,
  ":observe_status" => "Complete",
  ":semester_id" => $semester_id
]);

$observations_this_sem_count = $get_observations_this_sem->fetch()["observation_count"];

$get_total_observations = $conn->prepare("SELECT 
                                                        COUNT(*) AS 'observation_count'
                                                      FROM observations_tbl 
                                                      WHERE teacher_id = :teacher_id 
                                                      AND observe_status = :observe_status      
                                                      ");
$get_total_observations->execute([
  ":teacher_id" => $id_number,
    ":observe_status" => "Complete",
  ]);

  $total_observations_count = $get_total_observations->fetch()["observation_count"];

  $get_summative_count = $conn->prepare("SELECT 
                                                          COUNT(*) AS 'observation_count'
                                                        FROM observations_tbl 
                                                        WHERE teacher_id = :teacher_id 
                                                        AND observe_status = :observe_status 
                                                        AND copus_type = :copus_type     
                                                        ");
  $get_summative_count->execute([
    ":teacher_id" => $id_number,
    ":observe_status" => "Complete",
    ":copus_type" => "Summative"
  ]);

  $summative_observations_count = $get_summative_count->fetch()["observation_count"];

  // Student Action Averages
  $get_student_action_averages = $conn->prepare("SELECT
                                                            ta.action_name,
                                                            ROUND(AVG(ta.tally), 2) AS 'avg_tally',
                                                            ot.teacher_id
                                                          FROM student_action_log_tbl ta
                                                          LEFT JOIN observations_tbl ot
                                                          ON ta.observation_id = ot.observation_id
                                                          WHERE ot.teacher_id = :teacher_id
                                                          GROUP BY ta.action_name
                                                          ORDER BY avg_tally DESC
                                                          ");
  $get_student_action_averages->execute([":teacher_id" => $id_number]);

  $student_actions = $get_student_action_averages->fetchAll();

  $student_label = [];
  $student_action_avg_tally = [];

  $total_student_tally = 0;

  foreach ($student_actions as $student_action) {
    $student_label[] = $student_action["action_name"];
    $total_student_tally += (float)$student_action["avg_tally"];
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

  // Convert Student Averages to JSON
  $student_label_json = json_encode($student_label);
  $student_avg_tally_json = json_encode($student_action_avg_tally);

  // Teacher Action Averages
  $get_teacher_action_averages = $conn->prepare("SELECT
                                                            ta.action_name,
                                                            ROUND(AVG(ta.tally), 2) AS 'avg_tally',
                                                            ot.teacher_id
                                                          FROM teacher_action_log_tbl ta
                                                          LEFT JOIN observations_tbl ot
                                                          ON ta.observation_id = ot.observation_id
                                                          WHERE ot.teacher_id = :teacher_id
                                                          GROUP BY ta.action_name
                                                          ORDER BY avg_tally DESC
                                                          ");
  $get_teacher_action_averages->execute([":teacher_id" => $id_number]);

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

<!-- Main -->
<main id="main" class="main">

  <?php include_once "includes/pagetitle.php"; ?>

  <!-- Dashboard Main -->
  <section class="section dashboard">
    <div class="row">

      <!-- Left side columns -->
      <div class="col-lg-12">
        <div class="row">

            <div class="col-lg-12">
                <div class="row">
                    <div class="col-xxl-12 col-md-12">
            
                        <div class="card info-card other-card">
            
                          <div class="card-body">
            
                            <h5 class="card-title"> Assigned Department </span></h5>
            
                            <div class="d-flex align-items-center">
                              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-building"></i>
                              </div>
            
                              <div class="ps-3">
                                <h6> <?php echo htmlspecialchars($department_name); ?> </h6>
                                <span class="text-muted small pt-2 ps-1"> <?php echo htmlspecialchars($department_code); ?> </span>
                              </div>
            
                            </div>
            
                          </div>
            
                        </div>
            
                      </div>
            
                    <div class="col-xxl-4 col-md-6">
                        <a href="home.php?page=observations-this-semester">
                          <div class="card info-card teacher-card">
            
                            <div class="card-body">
                              <h5 class="card-title"> Observations this Semester </span></h5>
            
                              <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                  <i class="bi bi-calendar-week"></i>
                                </div>
                                <div class="ps-3">
                                  <h6> <?php echo htmlspecialchars($observations_this_sem_count); ?> </h6>
                                  <span class="text-muted small pt-2 ps-1"> Total Observations </span>
                                </div>
                              </div>
                            </div>
            
                          </div>
                        </a>
                      </div>
            
                    <div class="col-xxl-4 col-md-6">
                        <a href="home.php?page=all-observations">
                          <div class="card info-card teacher-card">
            
                            <div class="card-body">
                              <h5 class="card-title"> Total Observations </span></h5>
            
                              <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                  <i class="bi bi-clock-history"></i>
                                </div>
                                <div class="ps-3">
                                  <h6> <?php echo htmlspecialchars($total_observations_count); ?> </h6>
                                  <span class="text-muted small pt-2 ps-1"> Total Observations </span>
                                </div>
                              </div>
                            </div>
            
                          </div>
                        </a>
                      </div>
            
                    <div class="col-xxl-4 col-md-12">
                        <a href="home.php?page=my-summative-reports">
                          <div class="card info-card teacher-card">
            
                            <div class="card-body">
                              <h5 class="card-title"> Summative Observations </span></h5>
            
                              <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                  <i class="bi bi-clipboard-data"></i>
                                </div>
                                <div class="ps-3">
                                  <h6> <?php echo htmlspecialchars($summative_observations_count); ?> </h6>
                                  <span class="text-muted small pt-2 ps-1"> Total Observations </span>
                                </div>
                              </div>
                            </div>
            
                          </div>
                        </a>
                      </div>
                </div>
          
            </div>
              
        </div>

          <!-- Current Year and Semester -->
          <?php
          include_once "../global-includes/components/academic-year-and-sem.php";
          ?>
          <!-- End Current Year and Semester -->

          <div class="col-12">

            <div class="card shadow mb-3">

              <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                <h5 class="card-title custom-card-title text-white"> My Student Actions Averages </h5>

                <?php
                if ($student_label && $student_action_avg_tally) { ?>
                  <button
                    class="btn btn-light"
                    onclick="exportToPDF(
                              'averageStudentActionChart',
                              '<?php echo htmlspecialchars($last_name . '_' . $first_name . '_Student_Actions_Averages'); ?>',
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

          <div class="col-12">

            <div class="card shadow mb-3">

              <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                <h5 class="card-title custom-card-title text-white"> My Teacher Actions Averages </h5>

                <?php
                if ($teacher_label && $teacher_action_avg_tally) { ?>

                  <button
                    class="btn btn-light"
                    onclick="exportToPDF(
                            'averageTeacherActionChart',
                            '<?php echo htmlspecialchars($last_name . '_' . $first_name . '_Teacher_Actions_Averages'); ?>',
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
                          <h5 class="card-title"> Teacher Action Averages </h5>

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
                "averageStudentActionChart",
                <?php echo $student_label_json; ?>,
                <?php echo $student_avg_tally_json; ?>,
                <?php echo json_encode($colors_pool); ?>,
                "<?php echo htmlspecialchars($last_name . ", " . $first_name); ?>: Student Actions Averages",
                "bar"
              );

              convertDataToChart(
                "averageTeacherActionChart",
                <?php echo $teacher_label_json; ?>,
                <?php echo $teacher_avg_tally_json; ?>,
                <?php echo json_encode($colors_pool); ?>,
                "<?php echo htmlspecialchars($first_name . " " . $last_name); ?>: Teacher Actions Averages",
                "bar"
              );
            });
          </script>

        </div>
      </div>
      <!-- Left Side Columns -->

    </div>
  </section>
  <!-- End Dashboard Main -->

</main>
<!-- End #main -->