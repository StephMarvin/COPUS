<!-- Main -->
<main id="main" class="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Table -->
    <section class="section">

        <div class="row">

            <div class="col-lg-12">

                <div class="card shadow">

                    <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                        <h5 class="card-title custom-card-title text-white"> Admin Accounts </h5>
                        <a href="home.php?page=add-admin" class="btn btn-light"> Add New Admin </a>
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
                                        <th scope="col"> Created At </th>
                                        <th scope="col"> Last Login </th>
                                        <th scope="col"> Details </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                        $get_admin_list = $conn->prepare("SELECT
                                                                        ac.*, ai.profile_picture
                                                                        FROM admin_credentials_tbl ac
                                                                        LEFT JOIN admin_info_tbl ai
                                                                        ON ac.id_number = ai.id_number
                                                                        WHERE ac.is_archived = 'No'
                                                                        ORDER BY ac.last_name ASC
                                                                        ");

                                        $get_admin_list->execute();

                                        if($get_admin_list->rowCount() > 0) {
                                            while($admin_tbl_data = $get_admin_list->fetch()) {
                                                $profile_picture = $admin_tbl_data["profile_picture"];

                                                if(empty($profile_picture) || !file_exists($file_path . $profile_picture)) {
                                                    $profile_picture = "default-img.png";
                                                }

                                                ?>
                                                    <tr class="fw-bold">
                                                        <td> <?php echo htmlspecialchars($admin_tbl_data["id_number"]); ?> </td>

                                                        <td> 
                                                            <img src="<?php echo htmlspecialchars($file_path . $profile_picture); ?>" alt="Profile Picture" width="40" height="40"> 
                                                        </td>

                                                        <td> <?php echo htmlspecialchars($admin_tbl_data["last_name"] . ", " . $admin_tbl_data["first_name"]); ?> </td>
                                                        <td> <?php echo htmlspecialchars($admin_tbl_data["email_address"]); ?> </td>
                                                        <td> <?php echo htmlspecialchars($admin_tbl_data["role"]); ?> </td>
                                                        <td> <?php echo format_timestamp(htmlspecialchars($admin_tbl_data["created_at"])); ?> </td>
                                                        <td> 
                                                            <?php echo $admin_tbl_data["last_login"] ? format_timestamp(htmlspecialchars($admin_tbl_data["last_login"])) : "No login data."; ?> 
                                                        </td>
                                                        <td> 
                                                            <?php
                                                                if($admin_tbl_data["id_number"] === $id_number) {
                                                                    ?>
                                                                        <span> Logged in Account </span>
                                                                    <?php
                                                                }

                                                                else {
                                                                    ?>
                                                                        <a href="home.php?page=admin-details&id-number=<?php echo htmlspecialchars(base64_encode($admin_tbl_data["id_number"])); ?>" class="btn btn-primary custom-add-btn" > 
                                                                            Details 
                                                                        </a> 
                                                                    <?php
                                                                }
                                                            ?>       
                                                        </td>
                                                    </tr>
                                                <?php
                                            }
                                        }

                                        else {
                                            ?>
                                                <tr>
                                                    <td colspan="8"> No admin data available. </td>
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