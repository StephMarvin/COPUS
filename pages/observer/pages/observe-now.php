<!-- Main -->
<main id="main" class="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Form -->
    <section class="section">

        <div class="row">

            <?php
                if($semester_id) {

                
                    $get_incomplete_observation = $conn->prepare("SELECT
                                                                    ot.*,
                                                                    CONCAT(tc.last_name, ', ', tc.first_name) AS 'teacher_name',
                                                                    CONCAT(st.subject_code, ': ', st.subject_name) AS 'subject'
                                                                FROM observations_tbl ot
                                                                LEFT JOIN teacher_credentials_tbl tc
                                                                ON ot.teacher_id = tc.id_number
                                                                LEFT JOIN subjects_tbl st
                                                                ON ot.subject_id = st.subject_id
                                                                WHERE observer_id = :observer_id AND observe_status = :observe_status
                                                                LIMIT 1
                                                                ");
                    $get_incomplete_observation->execute([
                        ":observer_id" => $id_number,
                        ":observe_status" => "Incomplete"
                    ]);

                    if($get_incomplete_observation->rowCount() === 0) {

            ?>
                        <div class="col-lg-12">

                            <div class="card">
                                <div class="card-body">

                                    <div class="px-3">
                                        <h5 class="card-title custom-card-title mb-0"> Observe Now </h5>
                                        <p class="lh-1 mb-0 text-muted"> Please select a teacher, COPUS type, modality, year, and subject to proceed. </p>
                                    </div>

                                    <hr>

                                    <form action="../../process/observer/observation-management.php" method="POST" class="custom-form">

                                        <input type="hidden" name="observer-id" value="<?php echo htmlspecialchars(base64_encode($id_number)); ?>">
                                        <input type="hidden" name="semester-id" value="<?php echo htmlspecialchars(base64_encode($semester_id)); ?>">
                                        <input type="hidden" name="department-id" value="<?php echo htmlspecialchars(base64_encode($department_id)); ?>">
                                        
                                        <div class="container mt-1">

                                            <h5 class="card-title mb-0"> Select Teacher and COPUS Type: </h5>

                                            <div class="row mb-3">

                                                <div class="col-lg-6 col-md-12 mb-2">

                                                    <select class="form-select" name="teacher-id" required>
                                                        <option selected disabled value=""> SELECT TEACHER </option>
                                                        <?php
                                                            $get_teacher_list = $conn->prepare("SELECT * FROM teacher_credentials_tbl WHERE department_id = :department_id AND is_archived = 'No'");
                                                            $get_teacher_list->execute([":department_id" => $department_id]);

                                                            if($get_teacher_list->rowCount() > 0) {
                                                                while($teacher_data = $get_teacher_list->fetch()) {
                                                                    ?>
                                                                        <option value="<?php echo htmlspecialchars($teacher_data["id_number"]); ?>">
                                                                            <?php echo htmlspecialchars($teacher_data["last_name"] . ", " . $teacher_data["first_name"]); ?>
                                                                        </option>
                                                                    <?php
                                                                }
                                                            }
                                                        ?>   
                                                    </select>

                                                </div>

                                                <div class="col-lg-6 col-md-12 mb-2">

                                                    <select class="form-select" name="copus-type" required>
                                                        <option selected disabled value=""> SELECT COPUS TYPE </option>
                                                        <option value="COPUS 1"> COPUS 1 </option>
                                                        <option value="COPUS 2"> COPUS 2 </option>
                                                        <option value="COPUS 3"> COPUS 3 </option>
                                                        <?php if(in_array($designation, $allowed_summative_observer)): ?>
                                                            <option value="Summative"> Summative </option>
                                                        <?php endif; ?>
                                                    </select>

                                                </div>                                    

                                            </div>

                                            <h5 class="card-title mb-0"> Select Year, Modality and Subject: </h5>

                                            <div class="row mb-3">

                                                <div class="col-lg-4 col-md-12 mb-2">

                                                    <select class="form-select" name="year-level" required>
                                                        <option selected disabled value=""> SELECT YEAR LEVEL </option>
                                                        <option value="First Year"> First Year </option>
                                                        <option value="Second Year"> Second Year </option>
                                                        <option value="Third Year"> Third Year </option>
                                                        <option value="Fourth Year"> Fourth Year </option>
                                                        <option value="Fifth Year"> Fifth Year </option>
                                                    </select>

                                                </div>

                                                <div class="col-lg-4 col-md-12 mb-2">

                                                    <select class="form-select" name="modality" required>
                                                        <option selected disabled value=""> SELECT MODALITY </option>
                                                        <option value="FLEX (Face-to-Face)"> FLEX (Face-to-Face) </option>
                                                        <option value="RAD (Online Class)"> RAD (Online Class) </option>
                                                    </select>

                                                </div>

                                                <div class="col-lg-4 col-md-12 mb-0">

                                                    <select class="form-select" name="subject-id" required>
                                                        <option selected disabled value=""> SELECT SUBJECT </option>
                                                        <?php
                                                            $get_subjects = $conn->prepare("SELECT * FROM subjects_tbl WHERE semester = :semester AND subject_status = :subject_status AND department_id = :department_id");
                                                            $get_subjects->execute([":semester" => $semester, ":subject_status" => "Active", ":department_id" => $department_id]);

                                                            if($get_subjects->rowCount() > 0) {
                                                                while($subject_data = $get_subjects->fetch()) {
                                                                    ?>
                                                                        <option value="<?php echo htmlspecialchars($subject_data["subject_id"]); ?>"> 
                                                                            <?php echo htmlspecialchars($subject_data["subject_code"] . ": " . $subject_data["subject_name"]); ?>
                                                                        </option>
                                                                    <?php
                                                                }
                                                            }
                                                        ?>
                                                    </select>

                                                </div>                                    

                                            </div>

                                            <h5 class="card-title mt-3 mb-0"> Enter Your Password to Confirm: </h5>

                                            <div class="row">

                                                <div class="col-6">

                                                    <div class="form-floating mb-3">
                                                        <input type="password" class="form-control" id="floatingInput" placeholder="Enter your Password" name="observer-password" required>
                                                        <label for="floatingInput"> Enter your Password </label>
                                                    </div>

                                                </div>

                                            </div>

                                            <button type="submit" name="observe-now" class="btn btn-primary custom-add-btn" > Observe Now </button>

                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div> 
            <?php
                    }

                    else {
                        $observe_details = $get_incomplete_observation->fetch(PDO::FETCH_OBJ);
                        ?>
                            <div class="col-lg-12">

                                <div class="card">
                                    <div class="card-body">

                                        <div class="px-3">
                                            <h5 class="card-title custom-card-title mb-0"> Incomplete Observation </h5>
                                            <p class="lh-1 mb-0 text-muted">
                                                You have not completed the observation. Please complete the observation or delete to start a new one.
                                            </p>
                                        </div>

                                        <hr>
                                            
                                        <div class="container mt-1">

                                            <h5 class="card-title mb-0"> Teacher and COPUS Type: </h5>

                                            <div class="row mb-3">

                                                <div class="col-lg-6 col-md-12">

                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control" id="floatingInput" value="<?php echo htmlspecialchars($observe_details->teacher_name); ?>" readonly disabled>
                                                        <label for="floatingInput"> Teacher Name </label>
                                                    </div>

                                                </div>

                                                <div class="col-lg-6 col-md-12">

                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control" id="floatingInput" value="<?php echo htmlspecialchars($observe_details->copus_type); ?>" readonly disabled>
                                                        <label for="floatingInput"> COPUS Type </label>
                                                    </div>

                                                </div>                                     

                                            </div>

                                            <h5 class="card-title mb-0"> Year Level and Subject: </h5>

                                            <div class="row">

                                                <div class="col-lg-4 col-md-12">

                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control" id="floatingInput" value="<?php echo htmlspecialchars($observe_details->year_level); ?>" readonly disabled>
                                                        <label for="floatingInput"> Year Level </label>
                                                    </div>
                                                        
                                                </div>

                                                <div class="col-lg-4 col-md-12">

                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control" id="floatingInput" value="<?php echo htmlspecialchars($observe_details->modality); ?>" readonly disabled>
                                                        <label for="floatingInput"> Modality </label>
                                                    </div>

                                                </div>

                                                <div class="col-lg-4 col-md-12">

                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control" id="floatingInput" value="<?php echo htmlspecialchars($observe_details->subject); ?>" readonly disabled>
                                                        <label for="floatingInput"> Subject </label>
                                                    </div>

                                                </div>                                    

                                            </div>

                                            <h5 class="card-title"> Options: </h5>

                                            <div class="row">

                                                <div class="col-lg-6 col-md-12 d-flex gap-2">

                                                    <form action="../../process/observer/observation-management.php" method="POST">
                                                        <input type="hidden" name="observe-id" value="<?php echo htmlspecialchars(base64_encode($observe_details->observation_id)); ?>">
                                                        
                                                        <button class="btn btn-primary custom-add-btn" type="submit" name="continue-observing">
                                                            Continue Observing
                                                        </button>
                                                    </form>

                                                    <form action="../../process/observer/observation-management.php" method="POST">
                                                        <input type="hidden" name="observe-id" value="<?php echo htmlspecialchars(base64_encode($observe_details->observation_id)); ?>">
                                                        <input type="hidden" name="delete-observation" value="1">
                                                        <button
                                                        type="submit"
                                                        class="btn btn-danger"
                                                        onclick="confirmAction(
                                                        event, 
                                                        this.form, 
                                                        '',
                                                        'Start New Observation?', 
                                                        'warning',
                                                        'Are you sure you want to start a new observation and delete the current one?',
                                                        'Delete',
                                                        '#dc3545'
                                                        )"
                                                        >
                                                            Start New Observation
                                                        </button>
                                                    </form>
                                                        
                                                </div>  

                                            </div>


                                        </div>   

                                    </div>
                                </div>

                            </div>
                        <?php
                    }

                } 
                
                else {
            ?>
                    <div class="col-lg-12">

                        <div class="card shadow">

                            <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                                <h5 class="card-title custom-card-title text-white"> 
                                    Unable to Observe
                                </h5>
                            </div>

                            <div class="card-body py-2">
                                <h5 class="text-center"> No Currently Active Semester. </h5>
                            </div>
                        </div>

                    </div>
            <?php
                }
            ?>

        </div>

    </section>
    <!-- Form End -->
 
</main>
<!-- End Main -->