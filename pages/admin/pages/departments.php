<!-- Main -->
<main id="main" class="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Table -->
    <section class="section dashboard">

        <div class="row">

            <div class="col-lg-12">
                
                <!-- List of Departments -->
                <div class="card shadow mb-3">

                    <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-4">
                        <h5 class="card-title custom-card-title text-white"> List of Departments </h5>

                        <!-- Add Subject Later -->
                        <?php if($role === "Super Admin") { ?>
                            <a href="#" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#add-new-department"> Add Department </a>
                        <?php } ?>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                        <table class="table table-striped table-hover custom-table mb-1 datatable">
                            <thead>

                            <tr>
                                <th scope="col"> Department Code  </th>
                                <th scope="col"> Department Name </th>
                                <th scope="col"> Num. of Teachers </th>
                                <th scope="col"> Status </th> 
                                <th scope="col"> Added/Update At </th>  
                                <th scope="col"> Actions </th>
                            </tr>

                            </thead>

                            <tbody>

                            <?php
                                $get_departments = $conn->prepare("SELECT
                                                                dt.*,
                                                                COUNT(tc.id_number) AS 'teacher_count'
                                                            FROM departments_tbl dt
                                                            LEFT JOIN teacher_credentials_tbl tc
                                                            ON dt.department_id = tc.department_id
                                                            GROUP BY dt.department_id
                                                            ORDER BY 
                                                            FIELD(dt.department_status, 'Active', 'Inactive'),
                                                            teacher_count DESC
                                                            ");
                                $get_departments->execute();
                                if($get_departments->rowCount() > 0) {
                                while($department_data = $get_departments->fetch()) {
                                    ?>
                                    <tr class="fw-bold">
                                        <td> <?php echo htmlspecialchars($department_data["department_code"]); ?> </td>
                                        <td class="w-25"> <?php echo htmlspecialchars($department_data["department_name"]); ?> </td>

                                        <td> <?php echo htmlspecialchars($department_data["teacher_count"]); ?> </td>

                                        <td> 
                                            <?php if($department_data["department_status"] === "Active") { ?>
                                                <span class="text-success fw-bold"> <?php echo htmlspecialchars($department_data["department_status"]); ?>  </span>
                                            <?php } else { ?>
                                                <span class="text-danger fw-bold"> <?php echo htmlspecialchars($department_data["department_status"]); ?>  </span>
                                            <?php } ?>
                                        </td>

                                        <td> <?php echo htmlspecialchars(format_timestamp($department_data["updated_at"])); ?> </td>

                                        <td>
                                            <a href="#" class="btn btn-primary custom-add-btn" data-bs-toggle="modal" data-bs-target="#edit-department-<?php echo htmlspecialchars($department_data["department_id"]); ?>"> Update </a>
                                        </td>

                                    </tr>

                                    <!-- Modal -->
                                    <div class="modal fade" id="edit-department-<?php echo htmlspecialchars($department_data["department_id"]); ?>" tabindex="-1">

                                        <div class="modal-dialog modal-lg modal-dialog-centered border-0">
                                        
                                            <div class="modal-content">

                                                <div class="modal-header custom-bg">
                                                    <h5 class="modal-title text-white"> Edit Department: <span class="fw-bold"><?php echo htmlspecialchars($department_data["department_name"]); ?></span> </h5>
                                                    <button type="button" class="btn-close h1" data-bs-dismiss="modal"></button>
                                                </div>

                                                <form action="../../process/admin/academic-management.php" method="POST" autocomplete="off">

                                                    <input type="hidden" name="department-id" value="<?php echo htmlspecialchars(base64_encode($department_data["department_id"])); ?>">
                                                        
                                                    <div class="modal-body">
                                                        
                                                        <div class="container">

                                                            <div class="row mb-2">

                                                                <div class="col">
                                                                    <div class="form-floating mb-1 d-flex align-items-center">
                                                                    <input type="text" class="form-control" id="deptCode" placeholder="Department Code" name="department-code" value="<?php echo htmlspecialchars($department_data["department_code"]); ?>" required>
                                                                    <label for="deptCode"> Department Code </label>       
                                                                    </div>
                                                                </div>

                                                            </div>

                                                            <div class="row mb-2">

                                                                <div class="col">
                                                                    <div class="form-floating mb-1 d-flex align-items-center">
                                                                    <input type="text" class="form-control" id="deptName" placeholder="Department Name" value="<?php echo htmlspecialchars($department_data["department_name"]); ?>" name="department-name" required>
                                                                    <label for="deptName"> Department Name </label>       
                                                                    </div>
                                                                </div>

                                                            </div>

                                                        </div>
                                                        
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="submit" name="update-department" class="btn btn-primary custom-add-btn"> Update Department </button> 
                                    </form>  
                                                        <form action="../../process/admin/academic-management.php" method="POST" id="set-department-status">
                                                            <input type="hidden" name="department-id" value="<?php echo htmlspecialchars(base64_encode($department_data["department_id"])); ?>">

                                                        <?php if($department_data["department_status"] === "Active") { ?> 

                                                            <input type="hidden" name="set-department-inactive" value="1"> 

                                                            <button 
                                                            type="submit" 
                                                            class="btn btn-danger"
                                                            title="Set Inactive Department"
                                                            onclick="return confirmAction(
                                                                event,
                                                                this.form,
                                                                'set-department-status',
                                                                'Set to Inactive?',
                                                                'question',
                                                                'Are you sure you want to set the status of <?php echo htmlspecialchars($department_data['department_code'] . ': ' . $department_data['department_name']); ?> department to inactive?',
                                                                'Set Inactive',
                                                                '#dc3545'
                                                            )"
                                                            >
                                                                Set Inactive
                                                            </button>

                                                        <?php } else { ?>  

                                                            <input type="hidden" name="set-department-active" value="1">   

                                                            <button 
                                                            type="submit" 
                                                            class="btn btn-success"
                                                            title="Set Active Department"
                                                            onclick="return confirmAction(
                                                                event,
                                                                this.form,
                                                                'set-department-status',
                                                                'Set to Active?',
                                                                'question',
                                                                'Are you sure you want to set the status of <?php echo htmlspecialchars($department_data['department_code'] . ': ' . $department_data['department_name']); ?> department to active?',
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
                                        <td colspan="6"> No departments yet. </td>
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
<div class="modal fade" id="add-new-department" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered border-0">
    
        <div class="modal-content">

            <div class="modal-header custom-bg">
                <h5 class="modal-title text-white"> Add New Department </h5>
                <button type="button" class="btn-close h1" data-bs-dismiss="modal"></button>
            </div>

            <form action="../../process/admin/academic-management.php" method="POST" autocomplete="off">

                <input type="hidden" name="admin-id" value="<?php echo htmlspecialchars($id_number); ?>">
                    
                <div class="modal-body">
                    
                    <div class="container">

                        <div class="row mb-2">

                            <div class="col">
                                <div class="form-floating mb-1 d-flex align-items-center">
                                    <input type="text" class="form-control" id="deptCode" placeholder="Department Code" name="department-code" required>
                                    <label for="deptCode"> Department Code </label>       
                                </div>
                            </div>
                        
                        </div>

                        <div class="row mb-2">

                            <div class="col">
                                <div class="form-floating mb-1 d-flex align-items-center">
                                    <input type="text" class="form-control" id="deptName" placeholder="Department Name" name="department-name" required>
                                    <label for="deptName"> Department Name </label>       
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
                    <button type="submit" name="add-new-department" class="btn btn-primary custom-add-btn"> Add New Department </button>
                </div>

            </form>

        </div>

    </div>

</div>
<!-- End Modal -->