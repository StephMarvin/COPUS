<?php
    $get_active_academic_year = $conn->prepare("SELECT * FROM academic_years_tbl WHERE status = :status LIMIT 1");
    $get_active_academic_year->execute([":status" => "Active"]);

    if($get_active_academic_year->rowCount() === 1) {
      $get_academic_year = $get_active_academic_year->fetch(PDO::FETCH_OBJ);
      $academic_year = $get_academic_year -> academic_year;
    }

    else {
      $academic_year = "Not set.";
    }
?>

<!-- Main -->
<main id="main" class="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Table -->
    <section class="section dashboard">

        <div class="row">

            <div class="col-lg-12">

                <!-- Current Year -->
                <div class="card shadow mb-3">

                    <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                        <h5 class="card-title custom-card-title text-white"> Current Academic Year </h5>

                        <?php if($role === "Super Admin") { ?>
                            <a href="#" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#add-academic-year"> Add Academic Year </a>
                        <?php } ?>
                    </div>

                    <div class="row px-4 py-3">

                        <div class="col-xxl-6 col-md-6">
                            <a href="home.php?page=academic-year">
                            <div class="card info-card teacher-card">

                                <div class="card-body">
                                <h5 class="card-title"> Current Academic Year </span></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-calendar3"></i>
                                    </div>
                                    <div class="ps-3">
                                    <h6> <?php echo htmlspecialchars($academic_year); ?> </h6>
                                    <span class="text-muted small pt-2 ps-1"> Academic Year </span>
                                    </div>
                                </div>
                                </div>

                            </div>
                            </a>
                        </div>

                        <div class="col-xxl-6 col-md-6">
                            <a href="home.php?page=academic-settings">
                            <div class="card info-card teacher-card">

                                <div class="card-body">
                                <h5 class="card-title"> Current Semester </span></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-journal-check"></i>
                                    </div>
                                    <div class="ps-3">
                                    <h6> <?php echo htmlspecialchars($semester); ?> </h6>
                                    <span class="text-muted small pt-2 ps-1"> Active Semester </span>
                                    </div>
                                </div>
                                </div>

                            </div>
                            </a>
                        </div>

                    </div>

                </div>
                <!-- End Current Year -->
                
                <!-- List of Academic Years -->
                <div class="card shadow mb-3">

                    <div class="card-header custom-bg text-white mb-2 py-0 px-4">
                        <h5 class="card-title custom-card-title text-white"> All Academic Years </h5>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-striped table-hover custom-table mt-2 mb-1 datatable">

                                <thead>

                                    <tr>
                                        <th scope="col"> Academic Year </th>
                                        <th scope="col"> Status </th>
                                        <th scope="col"> Created At </th>
                                        <th scope="col"> Modified At </th>
                                        <th scope="col"> Action </th>  
                                    </tr>

                                </thead>


                                <tbody>  
                                    <?php
                                    $get_academic_year_data = $conn->prepare("SELECT 
                                                                                        * 
                                                                                    FROM 
                                                                                    academic_years_tbl 
                                                                                    ORDER BY status ASC, academic_year_id DESC");
                                    $get_academic_year_data->execute();

                                    if($get_academic_year_data->rowCount() > 0) {
                                        while($academic_data = $get_academic_year_data->fetch()) {
                                        ?>
                                            <tr>
                                                <td class="fw-bold"> <?php echo htmlspecialchars($academic_data["academic_year"]); ?> </td>
                                                <td> 
                                                    <?php if($academic_data["status"] === "Active") { ?>
                                                    <span class="badge bg-success px-2 py-2 w-50"> <?php echo htmlspecialchars($academic_data["status"]); ?> </span>
                                                    <?php } ?>

                                                    <?php if($academic_data["status"] === "Inactive") { ?>
                                                    <span class="badge bg-danger px-2 py-2 w-50"> <?php echo htmlspecialchars($academic_data["status"]); ?> </span>
                                                    <?php } ?>
                                                </td>

                                                <td> <?php echo htmlspecialchars(format_timestamp($academic_data["created_at"])); ?> </td>
                                                <td> <?php echo htmlspecialchars(format_timestamp($academic_data["modified_at"])); ?> </td>

                                                <td>

                                                    <?php if ($academic_data["status"] === "Active") { ?>

                                                        <span class="text-success fw-bold"> Active Year </span>

                                                    <?php } else { ?>

                                                        <form action="../../process/admin/academic-management.php" method="POST" id="set-year-active">
                                                            <input type="hidden" name="academic-year-id" value="<?php echo htmlspecialchars(base64_encode($academic_data["academic_year_id"])); ?>">
                                                            <input type="hidden" name="set-active-year" value="1">

                                                            <button type="submit" 
                                                            class="btn btn-success btn-sm" 
                                                            style="font-size: 12px;"
                                                            title="Set Active Academic Year"
                                                            onclick="confirmAction(
                                                                event,
                                                                this.form,
                                                                'set-year-active',
                                                                'Set Active Year',
                                                                'question',
                                                                'Do you want to set <?php echo htmlspecialchars($academic_data['academic_year']); ?> as active year?',
                                                                'Set Year',
                                                                '#2eca6a'
                                                            )"
                                                            >
                                                                Set Active Year
                                                            </button>
                                                        </form>

                                                    <?php } ?>

                                                </td>

                                            </tr>
                                        <?php
                                        }
                                    }

                                    else {
                                        ?>
                                        <tr>
                                            <td colspan="6"> No academic year data. </td>
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
<div class="modal fade" id="add-academic-year" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered border-0">
    
    <div class="modal-content">

        <div class="modal-header custom-bg">
        <h5 class="modal-title text-white"> Add Academic Year </h5>
        <button type="button" class="btn-close h1" data-bs-dismiss="modal"></button>
        </div>

        <form action="../../process/admin/academic-management.php" method="POST">

        <input type="hidden" name="admin-id" value="<?php echo htmlspecialchars($id_number); ?>">
            
        <div class="modal-body">
            
            <div class="container">

                <div class="row">

                <div class="col">
                    <div class="form-floating mb-1 d-flex align-items-center">
                    <input type="text" class="form-control" id="academicYear" placeholder="Academic Year" name="academic-year" readonly required>
                    <label for="academicYear"> Academic Year </label>
                    
                    <div class="mx-2 d-flex justify-content-between align-items-center gap-2">

                        <button type="button" class="btn btn-secondary p-3 px-4 text-white" onclick="decrementSchoolYear()">
                            <i class="bi bi-dash-lg"></i>
                        </button>

                        <button type="button" class="btn btn-primary custom-add-btn p-3 px-4 text-white" onclick="incrementSchoolYear()">
                        <i class="bi bi-plus-lg"></i>
                        </button>

                    </div>
                    
                    </div>

                    
                </div>

                </div>

                <h5 class="card-title mt-0 mb-0"> Enter Your Password to Confirm: </h5>

                <div class="row">
                
                    <div class="col">
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="floatingInput" placeholder="Enter your Password" name="admin-password" required>
                        <label for="floatingInput"> Enter your Password </label>
                    </div>
                    </div>

                </div>

            </div>
            
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> Close </button>
            <button type="submit" name="add-academic-year" class="btn btn-primary custom-add-btn"> Add Academic Year </button>
        </div>

        </form>

    </div>

    </div>

</div>
<!-- End Modal -->