<?php

// Observations this Sem
$get_observations_this_sem = $conn->prepare("SELECT 
                                                          COUNT(*) AS 'observation_count'
                                                        FROM observations_tbl 
                                                        WHERE department_id = :department_id 
                                                        AND observe_status = :observe_status
                                                        AND semester_id = :semester_id
                                                        ");
$get_observations_this_sem->execute([
  ":department_id" => $department_id,
  ":observe_status" => "Complete",
  ":semester_id" => $semester_id
]);

$observations_this_sem_count = $get_observations_this_sem->fetch()["observation_count"];

// All Observations
$get_total_observations = $conn->prepare("SELECT 
                                                          COUNT(*) AS 'observation_count'
                                                        FROM observations_tbl 
                                                        WHERE department_id = :department_id 
                                                        AND observe_status = :observe_status      
                                                        ");
$get_total_observations->execute([
  ":department_id" => $department_id,
  ":observe_status" => "Complete",
]);

$total_observations_count = $get_total_observations->fetch()["observation_count"];

// Summative Observations
$get_summative_count = $conn->prepare("SELECT 
                                                          COUNT(*) AS 'observation_count'
                                                        FROM observations_tbl 
                                                        WHERE department_id = :department_id AND copus_type = :copus_type
                                                        AND observe_status = :observe_status      
                                                        ");
$get_summative_count->execute([
  ":department_id" => $department_id,
  ":copus_type" => "Summative",
  ":observe_status" => "Complete",
]);

$total_summative_count = $get_summative_count->fetch()["observation_count"];

// Teachers Accounts Count
$get_teachers_count = $conn->prepare("SELECT COUNT(*) AS 'teachers_count' FROM teacher_credentials_tbl WHERE department_id = :department_id AND is_archived = 'No'");
$get_teachers_count->execute([":department_id" => $department_id]);
$teachers_count = $get_teachers_count->fetch()["teachers_count"];

// Observers Accouts Count
$get_observers_count = $conn->prepare("SELECT COUNT(*) AS 'observers_count' FROM observers_credentials_tbl WHERE department_id = :department_id AND is_archived = 'No'");
$get_observers_count->execute([":department_id" => $department_id]);
$observers_count = $get_observers_count->fetch()["observers_count"];

// Get Subjects Count
$get_subjects_count = $conn->prepare("SELECT COUNT(*) AS 'subject_count' FROM subjects_tbl WHERE department_id = :department_id AND subject_status = :subject_status");
$get_subjects_count->execute([":department_id" => $department_id, ":subject_status" => "Active"]);
$subjects_count = $get_subjects_count->fetch()["subject_count"];

// Student Action Averages
$get_student_action_averages = $conn->prepare("SELECT
                                                            sa.action_name,
                                                            ROUND(AVG(sa.tally), 2) AS 'avg_tally'
                                                          FROM student_action_log_tbl sa
                                                          LEFT JOIN observations_tbl ot
                                                          ON sa.observation_id = ot.observation_id
                                                          WHERE ot.department_id = :department_id
                                                          GROUP BY sa.action_name
                                                          ORDER BY avg_tally DESC
                                                          ");
$get_student_action_averages->execute([":department_id" => $department_id]);

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
                                                            ROUND(AVG(ta.tally), 2) AS 'avg_tally'
                                                          FROM teacher_action_log_tbl ta
                                                          LEFT JOIN observations_tbl ot
                                                          ON ta.observation_id = ot.observation_id
                                                          WHERE ot.department_id = :department_id
                                                          GROUP BY ta.action_name
                                                          ORDER BY avg_tally DESC
                                                          ");
$get_teacher_action_averages->execute([":department_id" => $department_id]);

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

      <div class="col-lg-12">
          
          <div class="row">
              <div class="col-xxl-3 col-md-6">
        
                <div class="card info-card other-card">
        
                  <div class="card-body">
        
                    <h5 class="card-title"> Department </span></h5>
        
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
        
              <div class="col-xxl-3 col-md-6">
                <a href="home.php?page=teachers-list">
                  <div class="card info-card teacher-card">
        
                    <div class="card-body">
                      <h5 class="card-title"> Total Teachers </span></h5>
        
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-person-video3"></i>
                        </div>
                        <div class="ps-3">
                          <h6> <?php echo htmlspecialchars($teachers_count); ?> </h6>
                          <span class="text-muted small pt-2 ps-1"> Active Teachers </span>
                        </div>
                      </div>
                    </div>
        
                  </div>
                </a>
              </div>
        
              <div class="col-xxl-3 col-md-6">
                <a href="home.php?page=observers-list">
                  <div class="card info-card teacher-card">
        
                    <div class="card-body">
                      <h5 class="card-title"> Total Observers </span></h5>
        
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-person-workspace"></i>
                        </div>
                        <div class="ps-3">
                          <h6> <?php echo htmlspecialchars($observers_count); ?> </h6>
                          <span class="text-muted small pt-2 ps-1"> Active Observers </span>
                        </div>
                      </div>
                    </div>
        
                  </div>
                </a>
              </div>
        
              <div class="col-xxl-3 col-md-6">
                <a href="home.php?page=subjects">
                  <div class="card info-card other-card">
        
                    <div class="card-body">
                      <h5 class="card-title"> Total Subjects </span></h5>
        
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-journal-bookmark-fill"></i>
                        </div>
                        <div class="ps-3">
                          <h6> <?php echo htmlspecialchars($subjects_count); ?> </h6>
                          <span class="text-muted small pt-2 ps-1"> Active Subjects </span>
                        </div>
                      </div>
                    </div>
        
                  </div>
                </a>
              </div>
        
              <div class="col-xxl-4 col-md-6">
                <a href="home.php?page=observation-records">
                  <div class="card info-card teacher-card">
        
                    <div class="card-body">
                      <h5 class="card-title"> Obsservations this Semester </span></h5>
        
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
                <a href="home.php?page=teacher-records">
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
                <a href="home.php?page=summative-reports">
                  <div class="card info-card other-card">
        
                    <div class="card-body">
                      <h5 class="card-title"> Summative Observations </span></h5>
        
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-clipboard-data"></i>
                        </div>
                        <div class="ps-3">
                          <h6> <?php echo htmlspecialchars($total_summative_count); ?> </h6>
                          <span class="text-muted small pt-2 ps-1"> Total Observations </span>
                        </div>
                      </div>
                    </div>
        
                  </div>
                </a>
              </div>
          </div>
          
          
      </div>   

      <!-- Left side columns -->
      <div class="col-lg-12">
        <div class="row">

          <!-- Current Year and Semester -->
          <?php
          include_once "../global-includes/components/academic-year-and-sem.php";
          ?>
          <!-- End Current Year and Semester -->

          <!-- Student Actions -->
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
                              '<?php echo htmlspecialchars($department_code . '_Student_Actions_Average_Per_Observation'); ?>',
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
          <!-- End Student Actions -->

          <!-- Teacher Actions -->
          <div class="col-12">

            <div class="card shadow mb-3">

              <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                <h5 class="card-title custom-card-title text-white"> Teacher Action Averages </h5>

                <?php
                if ($teacher_label && $teacher_action_avg_tally) { ?>

                  <button
                    class="btn btn-light"
                    onclick="exportToPDF(
                            'averageTeacherActionChart',
                            '<?php echo htmlspecialchars($department_code . '_Teacher_Actions_Average_Per_Observation'); ?>',
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
          <!-- End Teacher Actions -->

          <!-- Scripts -->
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

            document.addEventListener("DOMContentLoaded", function() {
              convertDataToChart(
                "averageTeacherActionChart",
                <?php echo $teacher_label_json; ?>,
                <?php echo $teacher_avg_tally_json; ?>,
                <?php echo json_encode($colors_pool); ?>,
                "Teacher Actions Averages Per Observation",
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