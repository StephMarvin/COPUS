<!-- Main -->
<main id="main" class="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Table -->
    <section class="section dashboard">

        <div class="row">

            <div class="col-lg-12">
                
                <!-- List of Subjects -->
                <div class="card shadow mb-3">

                    <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-4">
                        <h5 class="card-title custom-card-title text-white"> List of Subjects </h5>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                        <table class="table table-striped table-hover custom-table mb-1 datatable">
                            <thead>

                            <tr>
                                <th scope="col"> Subject Code  </th>
                                <th scope="col"> Subject Name </th>
                                <th scope="col"> Units </th>
                                <th scope="col"> Department </th>
                                <th scope="col"> Semester </th>
                                <th scope="col"> Status </th> 
                                <th scope="col"> Added/Updated At </th>  
                            </tr>

                            </thead>

                            <tbody>

                            <?php
                                $get_subjects = $conn->prepare("SELECT
                                                                st.*, dt.department_code
                                                            FROM subjects_tbl st
                                                            LEFT JOIN departments_tbl dt
                                                            ON st.department_id = dt.department_id
                                                            ORDER BY 
                                                            FIELD(subject_status, 'Active', 'Inactive'),
                                                            FIELD(semester, '1st Semester', '2nd Semester'),
                                                            modified_at DESC
                                                            ");
                                $get_subjects->execute();
                                if($get_subjects->rowCount() > 0) {
                                while($subject_data = $get_subjects->fetch()) {
                                    ?>
                                    <tr class="fw-bold">
                                        <td> <?php echo htmlspecialchars($subject_data["subject_code"]); ?> </td>
                                        <td class="w-25"> <?php echo htmlspecialchars($subject_data["subject_name"]); ?> </td>
                                        <td> <?php echo htmlspecialchars($subject_data["subject_units"]); ?> </td>
                                        <td> <?php echo htmlspecialchars($subject_data["department_code"]); ?> </td>
                                        <td> <?php echo htmlspecialchars($subject_data["semester"]); ?> </td>

                                        <td> 
                                            <?php if($subject_data["subject_status"] === "Active") { ?>
                                                <span class="text-success fw-bold"> <?php echo htmlspecialchars($subject_data["subject_status"]); ?>  </span>
                                            <?php } else { ?>
                                                <span class="text-danger fw-bold"> <?php echo htmlspecialchars($subject_data["subject_status"]); ?>  </span>
                                            <?php } ?>
                                        </td>

                                        <td> <?php echo htmlspecialchars(format_timestamp($subject_data["modified_at"])); ?> </td>

                                    </tr>
                                    <?php
                                    }
                                }

                                else {
                                ?>
                                    <tr>
                                        <td colspan="7"> No subjects yet. </td>
                                    </tr>
                                <?php
                                }
                            ?>

                            </tbody>
                        </table>
                        </div>
                        
                    </div>

                </div>
                <!-- End of List -->    

            </div>

        </div>

    </section>
    <!-- End Table -->

</main>
<!-- End #main -->
