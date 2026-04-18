<!-- Main -->
<main id="main" class="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Table -->
    <section class="section">

        <div class="row">

            <div class="col-lg-12">

                <div class="card shadow">

                    <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                        <h5 class="card-title custom-card-title text-white"> Summative Reports </h5>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-striped table-hover custom-table datatable">
                                <thead>
                                    <tr>
                                        <th scope="col"> ID Number </th>
                                        <th scope="col"> Profile </th>
                                        <th scope="col"> Teacher Name </th>
                                        <th scope="col"> Department </th>
                                        <th scope="col"> Total Summative Observations </th>    
                                        <th scope="col"> View Records </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                        $get_teacher_list = $conn->prepare("SELECT
                                                                            tc.id_number,
                                                                            ti.profile_picture,
                                                                            CONCAT(tc.last_name, ', ', tc.first_name) AS 'teacher_name',
                                                                            COUNT(ot.teacher_id) AS 'total_observations',
                                                                            dt.department_code
                                                                        FROM teacher_credentials_tbl tc
                                                                        LEFT JOIN teacher_info_tbl ti
                                                                            ON tc.id_number = ti.id_number
                                                                        LEFT JOIN observations_tbl ot
                                                                            ON tc.id_number = ot.teacher_id
                                                                            AND ot.observe_status = :observe_status
                                                                            AND ot.copus_type = :copus_type
                                                                        LEFT JOIN departments_tbl dt
                                                                        ON tc.department_id = dt.department_id
                                                                        GROUP BY tc.id_number, tc.first_name, tc.last_name, ti.profile_picture
                                                                        ORDER BY tc.last_name ASC
                                                                        ");

                                        $get_teacher_list->execute([":observe_status" => "Complete", ":copus_type" => "Summative"]);

                                        if($get_teacher_list->rowCount() > 0) {
                                            while($teacher_record_data = $get_teacher_list->fetch()) {
                                                $profile_picture = $teacher_record_data["profile_picture"];

                                                if(empty($profile_picture) || !file_exists($file_path . $profile_picture)) {
                                                    $profile_picture = "default-img.png";
                                                }

                                                ?>
                                                    <tr class="fw-bold">
                                                        <td> <?php echo htmlspecialchars($teacher_record_data["id_number"]); ?> </td>
                                                        <td> <img src="<?php echo htmlspecialchars($file_path . $profile_picture); ?>" alt="Profile Picture"> </td>
                                                        <td> <?php echo htmlspecialchars($teacher_record_data["teacher_name"]); ?> </td>
                                                        <td> <?php echo htmlspecialchars($teacher_record_data["department_code"]); ?> </td>
                                                        <td> <?php echo htmlspecialchars($teacher_record_data["total_observations"]); ?> </td>
                                                        
                                                        <td> 
                                                            <a href="home.php?page=summative-report-details&id-number=<?php echo htmlspecialchars(base64_encode($teacher_record_data["id_number"])); ?>" class="btn btn-primary custom-add-btn" > 
                                                                View Records 
                                                            </a> 
                                                        </td>
                                                    </tr>
                                                <?php
                                            }
                                        }

                                        else {
                                            ?>
                                                <tr>
                                                    <td colspan="8"> No teacher data available. </td>
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