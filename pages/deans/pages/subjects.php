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
                        <h5 class="card-title custom-card-title text-white"> 
                            <?php echo htmlspecialchars($department_code); ?> List of Subjects 
                        </h5>

                        <div>
                            <a href="#" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#add-new-subject"> Add Subject </a>
                            <a href="#" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#add-multiple-subjects"> Import Subjects </a>
                        </div>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                        <table class="table table-striped table-hover custom-table mb-1 datatable">
                            <thead>

                            <tr>
                                <th scope="col"> Subject Code  </th>
                                <th scope="col"> Subject Name </th>
                                <th scope="col"> Units </th>
                                <th scope="col"> Semester </th>
                                <th scope="col"> Status </th> 
                                <th scope="col"> Added/Update At </th>  
                                <th scope="col"> Actions </th>
                            </tr>

                            </thead>

                            <tbody>

                            <?php
                                $get_subjects = $conn->prepare("SELECT
                                                                *
                                                            FROM subjects_tbl
                                                            WHERE department_id = :department_id
                                                            ORDER BY 
                                                            FIELD(subject_status, 'Active', 'Inactive'),
                                                            FIELD(semester, '1st Semester', '2nd Semester'),
                                                            modified_at DESC
                                                            ");
                                $get_subjects->execute([":department_id" => $department_id]);
                                if($get_subjects->rowCount() > 0) {
                                while($subject_data = $get_subjects->fetch()) {
                                    ?>
                                    <tr class="fw-bold">
                                        <td> <?php echo htmlspecialchars($subject_data["subject_code"]); ?> </td>
                                        <td class="w-25"> <?php echo htmlspecialchars($subject_data["subject_name"]); ?> </td>
                                        <td> <?php echo htmlspecialchars($subject_data["subject_units"]); ?> </td>

                                        <td> <?php echo htmlspecialchars($subject_data["semester"]); ?> </td>

                                        <td> 
                                            <?php if($subject_data["subject_status"] === "Active") { ?>
                                                <span class="text-success fw-bold"> <?php echo htmlspecialchars($subject_data["subject_status"]); ?>  </span>
                                            <?php } else { ?>
                                                <span class="text-danger fw-bold"> <?php echo htmlspecialchars($subject_data["subject_status"]); ?>  </span>
                                            <?php } ?>
                                        </td>

                                        <td> <?php echo htmlspecialchars(format_timestamp($subject_data["modified_at"])); ?> </td>

                                        <td>
                                            <a href="#" class="btn btn-primary custom-add-btn" data-bs-toggle="modal" data-bs-target="#edit-subject-<?php echo htmlspecialchars($subject_data["subject_id"]); ?>"> Update </a>
                                        </td>

                                    </tr>

                                    <!-- Modal -->
                                    <div class="modal fade" id="edit-subject-<?php echo htmlspecialchars($subject_data["subject_id"]); ?>" tabindex="-1">

                                        <div class="modal-dialog modal-lg modal-dialog-centered border-0">
                                        
                                            <div class="modal-content">

                                                <div class="modal-header custom-bg">
                                                    <h5 class="modal-title text-white"> Edit Subject: <span class="fw-bold"><?php echo htmlspecialchars($subject_data["subject_name"]); ?></span> </h5>
                                                    <button type="button" class="btn-close h1" data-bs-dismiss="modal"></button>
                                                </div>

                                                <form action="../../process/dean/academic-management.php" method="POST" autocomplete="off">

                                                    <input type="hidden" name="subject-id" value="<?php echo htmlspecialchars(base64_encode($subject_data["subject_id"])); ?>">
                                                        
                                                    <div class="modal-body">
                                                        
                                                        <div class="container">

                                                            <div class="row mb-2">

                                                                <div class="col">
                                                                    <select class="form-select mb-1" name="semester">
                                                                        <option selected disabled> SELECT SEMESTER </option>
                                                                        <option value="1st Semester" <?php echo $subject_data["semester"] === "1st Semester" ? "selected" : ""; ?>> 
                                                                            1st Semester 
                                                                        </option>

                                                                        <option value="2nd Semester" <?php echo $subject_data["semester"] === "2nd Semester" ? "selected" : ""; ?>> 
                                                                            2nd Semester 
                                                                        </option>
                                                                    </select>
                                                                </div>

                                                            </div>

                                                            <div class="row mb-2">

                                                                <div class="col">
                                                                    <div class="form-floating mb-1 d-flex align-items-center">
                                                                    <input type="text" class="form-control" id="subjectCode" placeholder="Subject Code" name="subject-code" value="<?php echo htmlspecialchars($subject_data["subject_code"]); ?>" required>
                                                                    <label for="subjectCode"> Subject Code </label>       
                                                                    </div>
                                                                </div>

                                                                <div class="col">
                                                                    <div class="form-floating mb-1 d-flex align-items-center">
                                                                    <input type="number" class="form-control" id="units" placeholder="Subject Units" value="<?php echo htmlspecialchars($subject_data["subject_units"]); ?>" name="units" required>
                                                                    <label for="units"> Subject Units </label>       
                                                                    </div>
                                                                </div>

                                                            </div>

                                                            <div class="row mb-2">

                                                                <div class="col">
                                                                    <div class="form-floating mb-1 d-flex align-items-center">
                                                                    <input type="text" class="form-control" id="subjectName" placeholder="Subject Name" value="<?php echo htmlspecialchars($subject_data["subject_name"]); ?>" name="subject-name" required>
                                                                    <label for="subjectName"> Subject Name </label>       
                                                                    </div>
                                                                </div>

                                                            </div>

                                                        </div>
                                                        
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="submit" name="update-subject" class="btn btn-primary custom-add-btn"> Update Subject </button> 
                                    </form>  
                                                        <form action="../../process/dean/academic-management.php" method="POST" id="set-subject-status">
                                                            <input type="hidden" name="subject-id" value="<?php echo htmlspecialchars(base64_encode($subject_data["subject_id"])); ?>">

                                                        <?php if($subject_data["subject_status"] === "Active") { ?> 

                                                            <input type="hidden" name="set-subject-inactive" value="1"> 

                                                            <button 
                                                            type="submit" 
                                                            class="btn btn-danger"
                                                            title="Set Active Subject"
                                                            onclick="return confirmAction(
                                                                event,
                                                                this.form,
                                                                'set-subject-status',
                                                                'Set to Inactive?',
                                                                'question',
                                                                'Are you sure you want to set the status of <?php echo htmlspecialchars($subject_data['subject_code'] . ': ' . $subject_data['subject_name']); ?> subject to inactive?',
                                                                'Set Inactive',
                                                                '#dc3545'
                                                            )"
                                                            >
                                                                Set Inactive
                                                            </button>

                                                        <?php } else { ?>  

                                                            <input type="hidden" name="set-subject-active" value="1">   

                                                            <button 
                                                            type="submit" 
                                                            class="btn btn-success"
                                                            title="Set Active Subject"
                                                            onclick="return confirmAction(
                                                                event,
                                                                this.form,
                                                                'set-subject-status',
                                                                'Set to Active?',
                                                                'question',
                                                                'Are you sure you want to set the status of <?php echo htmlspecialchars($subject_data['subject_code'] . ': ' . $subject_data['subject_name']); ?> subject to active?',
                                                                'Set Active',
                                                                '#2eca6a'
                                                            )"
                                                            >
                                                                Set Active
                                                            </button>

                                                        <?php } ?>
                                                        </form>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                    <!-- End Modal -->
                                    <?php
                                    }
                                }

                                else {
                                ?>
                                    <tr>
                                        <td colspan="7"> No subjects in this course yet. </td>
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

<!-- Modal -->
<div class="modal fade" id="add-new-subject" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered border-0">
    
        <div class="modal-content">

            <div class="modal-header custom-bg">
                <h5 class="modal-title text-white"> Add New Subject </h5>
                <button type="button" class="btn-close h1" data-bs-dismiss="modal"></button>
            </div>

            <form action="../../process/dean/academic-management.php" method="POST" autocomplete="off">

                <input type="hidden" name="dean-id" value="<?php echo htmlspecialchars($id_number); ?>">
                <input type="hidden" name="department-id" value="<?php echo htmlspecialchars(base64_encode($department_id)); ?>">
                    
                <div class="modal-body">
                    
                    <div class="container">

                        <div class="row mb-2">

                            <div class="col">
                                <select class="form-select mb-1" name="semester" required>
                                    <option selected disabled value=""> SELECT SEMESTER </option>
                                    <option value="1st Semester"> 1st Semester </option>
                                    <option value="2nd Semester"> 2nd Semester </option>
                                </select>
                            </div>

                        </div>

                        <div class="row mb-2">

                            <div class="col">
                                <div class="form-floating mb-1 d-flex align-items-center">
                                    <input type="text" class="form-control" id="subjectCode" placeholder="Subject Name" name="subject-code" required>
                                    <label for="subjectCode"> Subject Code </label>       
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-floating mb-1 d-flex align-items-center">
                                    <input type="number" class="form-control" id="units" placeholder="Subject Units" name="subject-units" required>
                                    <label for="units"> Subject Units </label>       
                                </div>
                            </div>

                        </div>

                        <div class="row mb-2">

                            <div class="col">
                                <div class="form-floating mb-1 d-flex align-items-center">
                                    <input type="text" class="form-control" id="subjectName" placeholder="Subject Name" name="subject-name" required>
                                    <label for="subjectName"> Subject Name </label>       
                                </div>
                            </div>

                        </div>

                        <h5 class="card-title mt-0 mb-0"> Enter Your Password to Confirm: </h5>

                        <div class="row">
                        
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="floatingInput" placeholder="Enter your Password" name="dean-password" required>
                                    <label for="floatingInput"> Enter your Password </label>
                                </div>
                            </div>

                        </div>

                    </div>
                    
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> Close </button>
                    <button type="submit" name="add-new-subject" class="btn btn-primary custom-add-btn"> Add New Subject </button>
                </div>

            </form>

        </div>

    </div>

</div>
<!-- End Modal -->

<!-- Modal -->
<div class="modal fade" id="add-multiple-subjects" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered border-0">
    
        <div class="modal-content">

            <div class="modal-header custom-bg">
                <h5 class="modal-title text-white"> Add Multiple Subjects </h5>
                <button type="button" class="btn-close h1" data-bs-dismiss="modal"></button>
            </div>

            <form action="../../process/dean/academic-management.php" method="POST" enctype="multipart/form-data" autocomplete="off">

                <input type="hidden" name="dean-id" value="<?php echo htmlspecialchars($id_number); ?>">
                <input type="hidden" name="department-id" value="<?php echo htmlspecialchars(base64_encode($department_id)); ?>">
                    
                <div class="modal-body">
                    
                    <div class="container">

                        <!-- Excel Upload -->
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label fw-bold"> Upload Excel File </label>
                                <input type="file" 
                                       class="form-control" 
                                       name="excel-file" 
                                       accept=".xlsx, .xls, .csv"
                                       required>
                            </div>

                            <small class="fw-bold mx-1 mt-1"> Allowed Files: xlsx, xls, csv </small>
                        </div>

                        <h5 class="card-title mt-3 mb-2"> Enter Your Password to Confirm: </h5>

                        <div class="row">
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="floatingInput" placeholder="Enter your Password" name="dean-password" required>
                                    <label for="floatingInput"> Enter your Password </label>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 mb-0">

                            <p class="fw-bold fs-5"> Excel File Guidelines: </p>
                            
                            <p> The Excel file must contain the following column headers: </p>
                            
                            <ul class="mb-0 mt-0 fw-bold">
                                <li> Subject Code </li>
                                <li> Subject Name </li>
                                <li> Units </li>
                                <li> Semester (1st Semester, 2nd Semester) </li>
                            </ul>
                        </div>

                    </div>
                    
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> Close </button>
                    <button type="submit" name="add-multiple-subjects" class="btn btn-primary custom-add-btn">
                        Upload & Import Subjects
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>
<!-- End Modal -->