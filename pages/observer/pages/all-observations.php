<!-- Main -->
<main id="main" class="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Table -->
    <section class="section">

        <div class="row">

            <div class="col-lg-12">

                <div class="card shadow">

                    <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                        <h5 class="card-title custom-card-title text-white"> All Observed Classes </h5>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-striped table-hover custom-table datatable">
                                <thead>
                                    <tr>
                                        <th scope="col"> Academic Year </th>
                                        <th scope="col"> Semester </th>
                                        <th scope="col"> COPUS Type </th>
                                        <th scope="col"> Subject </th>
                                        <th scope="col"> Teacher </th>   
                                        <th scope="col"> Observed By </th>
                                        <th scope="col"> Observed At </th>
                                        <th scope="col"> View </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                        $get_observation_list = $conn->prepare("SELECT
                                                                        ot.*,
                                                                        ay.academic_year,
                                                                        s.semester,
                                                                        sub.subject_name,
                                                                        CONCAT(tc.last_name, ', ', tc.first_name) AS 'teacher',
                                                                        CONCAT(oc.last_name, ', ', oc.first_name) AS 'observer'
                                                                        FROM observations_tbl ot
                                                                        LEFT JOIN semesters_tbl s
                                                                        ON ot.semester_id = s.semester_id
                                                                        LEFT JOIN academic_years_tbl ay
                                                                        ON s.academic_year_id = ay.academic_year_id
                                                                        LEFT JOIN subjects_tbl sub
                                                                        ON ot.subject_id = sub.subject_id
                                                                        LEFT JOIN teacher_credentials_tbl tc
                                                                        ON ot.teacher_id = tc.id_number
                                                                        LEFT JOIN observers_credentials_tbl oc
                                                                        ON ot.observer_id = oc.id_number
                                                                        WHERE ot.observe_status = :observe_status AND
                                                                        ot.observer_id = :id_number
                                                                        ORDER BY 
                                                                        ay.academic_year DESC,
                                                                        FIELD(s.semester, '2nd Semester', '1st Semester'),
                                                                        ot.observed_at DESC
                                                                        ");

                                        $get_observation_list->execute([
                                            ":observe_status" => "Complete",
                                            ":id_number" => $id_number 
                                        ]);

                                        if($get_observation_list->rowCount() > 0) {
                                            while($observation_data = $get_observation_list->fetch()) {
                                                ?>
                                                    <tr class="fw-bold">
                                                        <td> <?php echo htmlspecialchars($observation_data["academic_year"]); ?> </td>
                                                        <td> <?php echo htmlspecialchars($observation_data["semester"]); ?> </td>
                                                        <td> <?php echo htmlspecialchars($observation_data["copus_type"]); ?> </td>
                                                        <td> <?php echo htmlspecialchars($observation_data["subject_name"]); ?> </td>
                                                        <td> <?php echo htmlspecialchars($observation_data["teacher"]); ?> </td>
                                                        <td> You </td>
                                                        <td> <?php echo format_timestamp(htmlspecialchars($observation_data["observed_at"])); ?> </td>
                                                        
                                                        <td>
                                                            <a href="home.php?page=observation-details&observation-id=<?php echo htmlspecialchars(base64_encode($observation_data["observation_id"])); ?>" class="btn btn-primary custom-add-btn" > 
                                                                Details 
                                                            </a> 
                                                        </td>
                                                    </tr>
                                                <?php
                                            }
                                        }

                                        else {
                                            ?>
                                                <tr>
                                                    <td colspan="8"> No observation data available. </td>
                                                </tr>
                                            <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>    
            </div>
        </div>

    </section>
    <!-- End Table -->

</main>
<!-- End #main -->