<!-- Main -->
<main id="main" class="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Table -->
    <section class="section dashboard">

        <div class="row">

            <div class="col-lg-12">
                
                <div class="card shadow mb-3">

                    <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-4">
                        <h5 class="card-title custom-card-title text-white"> List of Student Actions </h5>

                        <?php if($role === "Super Admin") { ?>
                            <a href="#" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#add-new-action"> Add Action </a>
                        <?php } ?>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                        <table class="table table-striped table-hover custom-table mb-1 datatable">
                            <thead>

                            <tr>
                                <th scope="col"> Action Code  </th>
                                <th scope="col"> Action Name </th>
                                <th scope="col"> Is Active Learning </th>
                                <th scope="col"> Status </th>
                                <th scope="col"> Added/Update At </th>  
                                <th scope="col"> Actions </th>
                            </tr>

                            </thead>

                            <tbody>

                            <?php
                                $get_actions = $conn->prepare("SELECT
                                                                *
                                                            FROM student_actions_tbl
                                                            ORDER BY
                                                            FIELD(
                                                                action_code,
                                                                'L',
                                                                'Ind',
                                                                'Grp',
                                                                'AnQ',
                                                                'AsQ',
                                                                'WC',
                                                                'SP',
                                                                'T/Q',
                                                                'W',
                                                                'O'
                                                            ),
                                                            modified_at DESC
                                                            ");
                                $get_actions->execute();
                                if($get_actions->rowCount() > 0) {
                                while($action_data = $get_actions->fetch()) {
                                    ?>
                                    <tr>
                                        <td class="fw-bold"> <?php echo htmlspecialchars($action_data["action_code"]); ?> </td>
                                        <td class="fw-bold"> <?php echo htmlspecialchars($action_data["action_name"]); ?> </td>

                                        <td> 
                                            <?php if($action_data["is_active_learning"] === "Yes") { ?>
                                                <span class="text-success fw-bold"> <?php echo htmlspecialchars($action_data["is_active_learning"]); ?>  </span>
                                            <?php } else { ?>
                                                <span class="text-warning fw-bold"> <?php echo htmlspecialchars($action_data["is_active_learning"]); ?>  </span>
                                            <?php } ?>
                                        </td>

                                        <td> 
                                            <?php if($action_data["action_status"] === "Active") { ?>
                                                <span class="text-success fw-bold"> <?php echo htmlspecialchars($action_data["action_status"]); ?>  </span>
                                            <?php } else { ?>
                                                <span class="text-danger fw-bold"> <?php echo htmlspecialchars($action_data["action_status"]); ?>  </span>
                                            <?php } ?>
                                        </td>

                                        <td> <?php echo htmlspecialchars(format_timestamp($action_data["modified_at"])); ?> </td>

                                        <td>
                                            <a href="#" class="btn btn-primary custom-add-btn" data-bs-toggle="modal" data-bs-target="#edit-action-<?php echo htmlspecialchars($action_data["action_id"]); ?>"> Update </a>
                                        </td>
                                    </tr>

                                    <!-- Modal -->
                                    <div class="modal fade" id="edit-action-<?php echo htmlspecialchars($action_data["action_id"]); ?>" tabindex="-1">

                                        <div class="modal-dialog modal-lg modal-dialog-centered border-0">
                                        
                                            <div class="modal-content">

                                                <div class="modal-header custom-bg">
                                                    <h5 class="modal-title text-white"> Edit Action: <span class="fw-bold"><?php echo htmlspecialchars($action_data["action_name"]); ?></span> </h5>
                                                    <button type="button" class="btn-close h1" data-bs-dismiss="modal"></button>
                                                </div>

                                                <form action="../../process/admin/action-management.php" method="POST" autocomplete="off">

                                                    <input type="hidden" name="action-id" value="<?php echo htmlspecialchars(base64_encode($action_data["action_id"])); ?>">
                                                        
                                                    <div class="modal-body">
                                                        
                                                        <div class="container">

                                                            <div class="row mb-2">

                                                                <div class="col">
                                                                    <div class="form-floating mb-1 d-flex align-items-center">
                                                                    <input type="text" class="form-control" id="actionCode" placeholder="Action Code" name="action-code" value="<?php echo htmlspecialchars($action_data["action_code"]); ?>" name="action-code" required>
                                                                    <label for="actionCode"> Action Code </label>       
                                                                    </div>
                                                                </div>

                                                            </div>

                                                            <div class="row mb-2">

                                                                <div class="col">
                                                                    <div class="form-floating mb-1 d-flex align-items-center">
                                                                    <input type="text" class="form-control" id="actionName" placeholder="Action Name" value="<?php echo htmlspecialchars($action_data["action_name"]); ?>" name="action-name" required>
                                                                    <label for="actionName"> Action Name </label>       
                                                                    </div>
                                                                </div>

                                                            </div>

                                                            <div class="row mb-2">

                                                                <div class="col">
                                                                    <label for="active-learning" class="form-label"> Active Learning </label>
                                                                    <div class="input-group mb-3">
                                                                        <select class="form-select" name="is-active-learning" id="active-learning" required>
                                                                            <option value="Yes" <?php echo htmlspecialchars($action_data["is_active_learning"] === "Yes" ? "selected" : ""); ?>> Yes </option>
                                                                            <option value="No" <?php echo htmlspecialchars($action_data["is_active_learning"] === "No" ? "selected" : ""); ?>> No </option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                            </div>

                                                        </div>
                                                        
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="submit" name="update-student-action" class="btn btn-primary custom-add-btn"> Update Action </button> 
                                                    
                                  </form>    

                                                        <form action="../../process/admin/action-management.php" method="POST">
                                                                
                                                            <input type="hidden" name="action-id" value="<?php echo htmlspecialchars(base64_encode($action_data["action_id"])); ?>">
                                                    
                                                        <?php if($action_data["action_status"] === "Active") { ?>
                                                            
                                                            <input type="hidden" name="set-student-action-inactive" value="1">

                                                            <button 
                                                            type="submit" 
                                                            class="btn btn-danger"
                                                            title="Set Inactive Action"
                                                            onclick="return confirmAction(
                                                                event,
                                                                this.form,
                                                                'set-action-status',
                                                                'Set to Inactive?',
                                                                'question',
                                                                'Are you sure you want to set the status of <?php echo htmlspecialchars($action_data['action_name'] . '(' . $action_data['action_code']) . ')'; ?> action to inactive?',
                                                                'Set Inactive',
                                                                '#dc3545'
                                                            )"
                                                            >
                                                                Set Inactive
                                                            </button>
                                                            
                                                        <?php } else { ?>

                                                            <input type="hidden" name="set-student-action-active" value="1">

                                                            <button 
                                                            type="submit" 
                                                            class="btn btn-danger"
                                                            title="Set Active Action"
                                                            onclick="return confirmAction(
                                                                event,
                                                                this.form,
                                                                'set-action-status',
                                                                'Set to Active?',
                                                                'question',
                                                                'Are you sure you want to set the status of <?php echo htmlspecialchars($action_data['action_name'] . '(' . $action_data['action_code']) . ')'; ?> action to active?',
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
                                    <!-- End Modal -->
                                    <?php
                                    }
                                }

                                else {
                                ?>
                                    <tr>
                                        <td colspan="6"> No student actions yet. </td>
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
<div class="modal fade" id="add-new-action" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered border-0">
    
        <div class="modal-content">

            <div class="modal-header custom-bg">
                <h5 class="modal-title text-white"> Add New Action </h5>
                <button type="button" class="btn-close h1" data-bs-dismiss="modal"></button>
            </div>

            <form action="../../process/admin/action-management.php" method="POST" autocomplete="off">

                <input type="hidden" name="admin-id" value="<?php echo htmlspecialchars($id_number); ?>">
                    
                <div class="modal-body">
                    
                    <div class="container">

                        <div class="row mb-2">

                            <div class="col">
                                <div class="form-floating mb-1 d-flex align-items-center">
                                <input type="text" class="form-control" id="actionCode" placeholder="Subject Name" name="action-code" required>
                                <label for="actionCode"> Action Code </label>       
                                </div>
                            </div>

                        </div>

                        <div class="row mb-2">

                            <div class="col">
                                <div class="form-floating mb-1 d-flex align-items-center">
                                <input type="text" class="form-control" id="actionName" placeholder="Action Name" name="action-name" required>
                                <label for="actionName"> Action Name </label>       
                                </div>
                            </div>

                        </div>

                        <div class="row mb-2">

                            <div class="col">
                                <label for="active-learning" class="form-label"> Active Learning </label>
                                <div class="input-group mb-3">
                                    <select class="form-select" name="is-active-learning" id="active-learning" required>
                                        <option value="" selected disabled> SELECT IS ACTIVE LEARNING </option>
                                        <option value="Yes"> Yes </option>
                                        <option value="No"> No </option>
                                    </select>
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
                    <button type="submit" name="add-new-student-action" class="btn btn-primary custom-add-btn"> Add New Action </button>
                </div>

            </form>

        </div>

    </div>

</div>
<!-- End Modal -->