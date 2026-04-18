<!-- Main -->
<main id="main" class="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Table -->
    <section class="section">

        <div class="row">

            <div class="col-lg-12">

                <div class="card shadow">

                    <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                        <h5 class="card-title custom-card-title text-white"> Teachers Accounts </h5>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-striped table-hover custom-table datatable">
                                <thead>
                                    <tr>
                                        <th scope="col"> ID Number </th>
                                        <th scope="col"> Profile </th>
                                        <th scope="col"> Name </th>
                                        <th scope="col"> Email Address </th>
                                        <th scope="col"> Role </th>
                                        <th scope="col"> Department </th>
                                        <th scope="col"> Created At </th>
                                        <th scope="col"> Last Login </th>
                                        <th scope="col"> Details </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                        $get_teacher_list = $conn->prepare("SELECT
                                                                        tc.*, ti.profile_picture, dt.department_code
                                                                        FROM teacher_credentials_tbl tc
                                                                        LEFT JOIN teacher_info_tbl ti
                                                                        ON tc.id_number = ti.id_number
                                                                        LEFT JOIN departments_tbl dt
                                                                        ON tc.department_id = dt.department_id
                                                                        WHERE tc.is_archived = 'No'
                                                                        ORDER BY tc.last_name ASC
                                                                        ");

                                        $get_teacher_list->execute();

                                        if($get_teacher_list->rowCount() > 0) {
                                            while($teacher_tbl_data = $get_teacher_list->fetch()) {
                                                $profile_picture = $teacher_tbl_data["profile_picture"];

                                                if(empty($profile_picture) || !file_exists($file_path . $profile_picture)) {
                                                    $profile_picture = "default-img.png";
                                                }

                                                ?>
                                                    <tr class="fw-bold">
                                                        <td> <?php echo htmlspecialchars($teacher_tbl_data["id_number"]); ?> </td>

                                                        <td> 
                                                            <img src="<?php echo htmlspecialchars($file_path . $profile_picture); ?>" alt="Profile Picture" width="40" height="40"> 
                                                        </td>

                                                        <td> <?php echo htmlspecialchars($teacher_tbl_data["last_name"] . ", " . $teacher_tbl_data["first_name"]); ?> </td>
                                                        <td> <?php echo htmlspecialchars($teacher_tbl_data["email_address"]); ?> </td>
                                                        <td> <?php echo htmlspecialchars($teacher_tbl_data["role"]); ?> </td>
                                                        <td> <?php echo htmlspecialchars($teacher_tbl_data["department_code"]); ?> </td>
                                                        <td> <?php echo format_timestamp(htmlspecialchars($teacher_tbl_data["created_at"])); ?> </td>
                                                        <td> 
                                                            <?php echo $teacher_tbl_data["last_login"] ? format_timestamp(htmlspecialchars($teacher_tbl_data["last_login"])) : "No login data."; ?> 
                                                        </td>
                                                        <td> 
                                                            <a href="home.php?page=teacher-details&id-number=<?php echo htmlspecialchars(base64_encode($teacher_tbl_data["id_number"])); ?>" class="btn btn-primary custom-add-btn" > 
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
                                                    <td colspan="9"> No teacher data available. </td>
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