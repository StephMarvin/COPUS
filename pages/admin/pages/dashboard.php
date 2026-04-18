<?php

// // Admin Accounts Count
// $get_admin_count = $conn->prepare("SELECT COUNT(*) AS 'admin_count' FROM admin_credentials_tbl");
// $get_admin_count->execute();
// $admin_count = $get_admin_count->fetch()["admin_count"];

// Deans Accounts Count
$get_deans_count = $conn->prepare("SELECT COUNT(*) AS 'deans_count' FROM deans_credentials_tbl WHERE is_archived = 'No'");
$get_deans_count->execute();
$deans_count = $get_deans_count->fetch()["deans_count"];

$get_departments_count = $conn->prepare("SELECT COUNT(*) AS 'department_count' FROM departments_tbl WHERE department_status = :department_status");
$get_departments_count->execute([":department_status" => "Active"]);
$department_count = $get_departments_count->fetch()["department_count"];

// // Teachers Accounts Count
$get_teachers_count = $conn->prepare("SELECT COUNT(*) AS 'teachers_count' FROM teacher_credentials_tbl WHERE is_archived = 'No'");
$get_teachers_count->execute();
$teachers_count = $get_teachers_count->fetch()["teachers_count"];

// // Observers Accouts Count
$get_observers_count = $conn->prepare("SELECT COUNT(*) AS 'observers_count' FROM observers_credentials_tbl WHERE is_archived = 'No'");
$get_observers_count->execute();
$observers_count = $get_observers_count->fetch()["observers_count"];

// Observations this Sem
$get_observations_this_sem = $conn->prepare("SELECT 
                                                          COUNT(*) AS 'observation_count'
                                                        FROM observations_tbl 
                                                        WHERE observe_status = :observe_status
                                                        AND semester_id = :semester_id
                                                        ");
$get_observations_this_sem->execute([
  ":observe_status" => "Complete",
  ":semester_id" => $semester_id
]);

$observations_this_sem_count = $get_observations_this_sem->fetch()["observation_count"];

// All Observations
$get_total_observations = $conn->prepare("SELECT 
                                                          COUNT(*) AS 'observation_count'
                                                        FROM observations_tbl 
                                                        WHERE observe_status = :observe_status      
                                                        ");
$get_total_observations->execute([
  ":observe_status" => "Complete",
]);

$total_observations_count = $get_total_observations->fetch()["observation_count"];

// Summative Observations
$get_summative_count = $conn->prepare("SELECT 
                                                          COUNT(*) AS 'observation_count'
                                                        FROM observations_tbl 
                                                        WHERE copus_type = :copus_type
                                                        AND observe_status = :observe_status      
                                                        ");
$get_summative_count->execute([
  ":copus_type" => "Summative",
  ":observe_status" => "Complete",
]);

$total_summative_count = $get_summative_count->fetch()["observation_count"];
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

          <!-- <div class="col-xxl-4 col-md-12">
            <a href="home.php?page=admin-accounts">
              <div class="card info-card admin-card">

                <div class="card-body">
                  <h5 class="card-title"> Admin Accounts </span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-person-fill-gear"></i>
                    </div>
                    <div class="ps-3">
                      <h6> <?php echo htmlspecialchars($admin_count); ?> </h6>
                      <span class="text-muted small pt-2 ps-1"> Total Accounts </span>

                    </div>
                  </div>
                </div>

              </div>
            </a>
          </div> -->
          
          <div class=col-lg-12>
              
              <div class="row">
                  
                <!-- Departments -->
                <div class="col-xxl-3 col-md-6">
                    <a href="home.php?page=departments">
                      <div class="card info-card other-card">
        
                        <div class="card-body">
                          <h5 class="card-title"> Active Departments </span></h5>
        
                          <div class="d-flex align-items-center">
        
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                              <i class="bi bi-building"></i>
                            </div>
        
                            <div class="ps-3">
                              <h6> <?php echo htmlspecialchars($department_count); ?> </h6>
                              <span class="text-muted small pt-2 ps-1"> Departments </span>
                            </div>
        
                          </div>
        
                        </div>
                      </div>
                    </a>
                  </div>
                <!-- End Departments -->
    
                <!-- Deans Accounts -->
                <div class="col-xxl-3 col-md-6">
                <a href="home.php?page=deans-accounts">
                  <div class="card info-card teacher-card">
    
                    <div class="card-body">
                      <h5 class="card-title"> Deans Accounts </span></h5>
    
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-person-vcard-fill"></i>
                        </div>
                        <div class="ps-3">
                          <h6> <?php echo htmlspecialchars($deans_count); ?> </h6>
                          <span class="text-muted small pt-2 ps-1"> Active Accounts </span>
                        </div>
                      </div>
    
                    </div>
                  </div>
                </a>
              </div>
                <!-- End Deans Accounts -->
    
                <!-- Teachers Accounts -->
                <div class="col-xxl-3 col-md-6">
                <a href="home.php?page=teachers-accounts">
                  <div class="card info-card teacher-card">
    
                    <div class="card-body">
                      <h5 class="card-title"> Teacher Accounts </span></h5>
    
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-person-fill-check"></i>
                        </div>
                        <div class="ps-3">
                          <h6> <?php echo htmlspecialchars($teachers_count); ?> </h6>
                          <span class="text-muted small pt-2 ps-1"> Active Accounts </span>
                        </div>
                      </div>
                    </div>
    
                  </div>
                </a>
              </div>
                <!-- End Teachers Accounts -->
    
                <!-- Observers Accounts -->
                <div class="col-xxl-3 col-md-6">
                <a href="home.php?page=observers-accounts">
                  <div class="card info-card teacher-card">
    
                    <div class="card-body">
                      <h5 class="card-title"> Observers Accounts </span></h5>
    
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-person-workspace"></i>
                        </div>
                        <div class="ps-3">
                          <h6> <?php echo htmlspecialchars($observers_count); ?> </h6>
                          <span class="text-muted small pt-2 ps-1"> Active Accounts </span>
                        </div>
                      </div>
                    </div>
    
                  </div>
                </a>
              </div>
                <!-- End Observers Accounts --> 
                
                <div class="col-xxl-4 col-md-6">
                    <a href="home.php?page=observation-records">
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
                      <h5 class="card-title"> Total Summative Observations </span></h5>
    
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
              
        </div>
          
         
        
        <div class="row">

          <!-- Current Year and Semester -->
          <?php
          include_once "../global-includes/components/academic-year-and-sem.php";
          ?>
          <!-- End Current Year and Semester -->

          <!-- Student Actions -->
          <?php
          include_once "../global-includes/components/student-action-averages.php";
          ?>
          <!-- End Student Actions -->

          <!-- Teacher Actions -->
          <?php
          include_once "../global-includes/components/teacher-action-averages.php";
          ?>
          <!-- End Teacher Actions -->

        </div>
      </div>
      <!-- Left Side Columns -->

    </div>
  </section>
  <!-- End Dashboard Main -->

</main>
<!-- End #main -->
