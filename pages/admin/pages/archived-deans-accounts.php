<!-- Main -->
<main id="main" class="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Table -->
    <section class="section">

        <div class="row">

            <div class="col-lg-12">

                <div class="card shadow">

                    <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                        <h5 class="card-title custom-card-title text-white"> Archived Deans Accounts </h5>
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
                                        <th scope="col"> Archived At </th>
                                        <th scope="col"> Restore </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $get_deans_list = $conn->prepare("SELECT
                                                                        dc.*, di.profile_picture, dt.department_code
                                                                        FROM deans_credentials_tbl dc
                                                                        LEFT JOIN deans_info_tbl di
                                                                        ON dc.id_number = di.id_number
                                                                        LEFT JOIN departments_tbl dt
                                                                        ON dc.department_id = dt.department_id
                                                                        WHERE dc.is_archived = 'Yes'
                                                                        ORDER BY dc.last_name ASC
                                                                        ");

                                    $get_deans_list->execute();

                                    if ($get_deans_list->rowCount() > 0) {
                                        while ($deans_tbl_data = $get_deans_list->fetch()) {
                                            $profile_picture = $deans_tbl_data["profile_picture"];

                                            if (empty($profile_picture) || !file_exists($file_path . $profile_picture)) {
                                                $profile_picture = "default-img.png";
                                            }

                                    ?>
                                            <tr class="fw-bold">
                                                <td> <?php echo htmlspecialchars($deans_tbl_data["id_number"]); ?> </td>

                                                <td>
                                                    <img src="<?php echo htmlspecialchars($file_path . $profile_picture); ?>" alt="Profile Picture" width="40" height="40">
                                                </td>

                                                <td> <?php echo htmlspecialchars($deans_tbl_data["last_name"] . ", " . $deans_tbl_data["first_name"]); ?> </td>
                                                <td> <?php echo htmlspecialchars($deans_tbl_data["email_address"]); ?> </td>
                                                <td> <?php echo htmlspecialchars($deans_tbl_data["role"] . " of " . $deans_tbl_data["department_code"]); ?> </td>
                                                <td> <?php echo format_timestamp(htmlspecialchars($deans_tbl_data["updated_at"])); ?> </td>

                                                <td>
                                                    <form action="../../process/admin/account-management.php" method="POST" id="set-archive-form">
                                                        <input type="hidden" name="dean-user-id" value="<?php echo htmlspecialchars(base64_encode($deans_tbl_data["id_number"])); ?>">
                                                        <input type="hidden" name="department-id" value="<?php echo htmlspecialchars(base64_encode($deans_tbl_data["department_id"])); ?>">
                                                        <input type="hidden" name="restore-deans-account" value="1">
                                                        <button type="submit"
                                                            class="btn btn-success btn-sm"
                                                            title="Restore Account"
                                                            onclick="confirmAction(
                                                                event,
                                                                this.form,
                                                                'set-archive-form',
                                                                'Restore Account?',
                                                                'question',
                                                                'Are you sure you want to restore <?php echo htmlspecialchars($deans_tbl_data['first_name'] . ' ' . $deans_tbl_data['last_name']); ?>\'s account?',
                                                                'Restore Account',
                                                                '#2eca6a'
                                                            )">
                                                            <i class="bi bi-arrow-clockwise"></i>
                                                        </button>
                                                    </form>

                                                </td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="9"> No archived accounts. </td>
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