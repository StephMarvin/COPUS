<!-- Main -->
<main id="main" class="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Table -->
    <section class="section">

        <div class="row">

            <div class="col-lg-12">

                <div class="card shadow">

                    <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                        <h5 class="card-title custom-card-title text-white"> Archived Observers Accounts </h5>
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
                                        <th scope="col"> Department </th>
                                        <th scope="col"> Archived At </th>
                                        <th scope="col"> Restore </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $get_observers_list = $conn->prepare("SELECT
                                                                        oc.*, oi.profile_picture, dt.department_code
                                                                        FROM observers_credentials_tbl oc
                                                                        LEFT JOIN observers_info_tbl oi
                                                                        ON oc.id_number = oi.id_number
                                                                        LEFT JOIN departments_tbl dt
                                                                        ON oc.department_id = dt.department_id
                                                                        WHERE oc.is_archived = 'Yes'
                                                                        ORDER BY oc.last_name ASC
                                                                        ");

                                    $get_observers_list->execute();

                                    if ($get_observers_list->rowCount() > 0) {
                                        while ($observer_tbl_data = $get_observers_list->fetch()) {
                                            $profile_picture = $observer_tbl_data["profile_picture"];

                                            if (empty($profile_picture) || !file_exists($file_path . $profile_picture)) {
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
                                                <td> <?php echo htmlspecialchars($observer_tbl_data["department_code"]); ?> </td>
                                                <td> <?php echo format_timestamp(htmlspecialchars($observer_tbl_data["updated_at"])); ?> </td>

                                                <td>
                                                    <form action="../../process/admin/account-management.php" method="POST" id="set-archive-form">
                                                        <input type="hidden" name="observer-user-id" value="<?php echo htmlspecialchars(base64_encode($observer_tbl_data["id_number"])); ?>">
                                                        <input type="hidden" name="restore-observer-account" value="1">
                                                        <button type="submit"
                                                            class="btn btn-success btn-sm"
                                                            title="Restore Account"
                                                            onclick="confirmAction(
                                                                event,
                                                                this.form,
                                                                'set-archive-form',
                                                                'Restore Account?',
                                                                'question',
                                                                'Are you sure you want to restore <?php echo htmlspecialchars($observer_tbl_data['first_name'] . ' ' . $observer_tbl_data['last_name']); ?>\'s account?',
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