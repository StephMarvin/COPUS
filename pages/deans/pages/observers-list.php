<!-- Main -->
<main id="main" class="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Table -->
    <section class="section">

        <div class="row">

            <div class="col-lg-12">

                <div class="card shadow">

                    <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                        <h5 class="card-title custom-card-title text-white"> <?php echo htmlspecialchars($department_code); ?> Observers List </h5>
                        <a href="home.php?page=add-observer" class="btn btn-light"> Add New Observer </a>
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
                                        <th scope="col"> Designation </th>
                                        <th scope="col"> Created At </th>
                                        <th scope="col"> Last Login </th>
                                        <th scope="col"> Details </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                        $get_observer_list = $conn->prepare("SELECT
                                                                        oc.*, oi.profile_picture
                                                                        FROM observers_credentials_tbl oc
                                                                        LEFT JOIN observers_info_tbl oi
                                                                        ON oc.id_number = oi.id_number
                                                                        WHERE oc.department_id = :department_id AND oc.is_archived = 'No'
                                                                        ORDER BY oc.last_name ASC
                                                                        ");

                                        $get_observer_list->execute([":department_id" => $department_id]);

                                        if($get_observer_list->rowCount() > 0) {
                                            while($observer_tbl_data = $get_observer_list->fetch()) {
                                                $profile_picture = $observer_tbl_data["profile_picture"];

                                                if(empty($profile_picture) || !file_exists($file_path . $profile_picture)) {
                                                    $profile_picture = "default-img.png";
                                                }

                                                ?>
                                                    <tr class="fw-bold">
                                                        <td> <?php echo htmlspecialchars($observer_tbl_data["id_number"]); ?> </td>

                                                        <td> 
                                                            <img src="<?php echo htmlspecialchars($file_path . $profile_picture); ?>" alt="Profile Picture" width="40" height="40"> 
                                                        </td>

                                                        <td> <?php echo htmlspecialchars($observer_tbl_data["last_name"] . ", " . $observer_tbl_data["first_name"]); ?> </td>
                                                        <td> <?php echo htmlspecialchars($observer_tbl_data["email_address"]); ?> </td>
                                                        <td> <?php echo htmlspecialchars($observer_tbl_data["designation"]); ?> </td>
                                                        <td> <?php echo format_timestamp(htmlspecialchars($observer_tbl_data["created_at"])); ?> </td>
                                                        <td> 
                                                            <?php echo $observer_tbl_data["last_login"] ? format_timestamp(htmlspecialchars($observer_tbl_data["last_login"])) : "No login data."; ?> 
                                                        </td>
                                                        <td> 
                                                            <a href="home.php?page=observer-details&id-number=<?php echo htmlspecialchars(base64_encode($observer_tbl_data["id_number"])); ?>" class="btn btn-primary custom-add-btn" > 
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
                                                    <td colspan="8"> No observer data available. </td>
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