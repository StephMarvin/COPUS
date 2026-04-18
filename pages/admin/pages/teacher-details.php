<?php
    $teacher_details_id = isset($_GET["id-number"]) ? base64_decode($_GET["id-number"]) : null;
        
    $get_teacher_details = $conn->prepare("SELECT
                                        tc.*, ti.*, dt.*
                                    FROM teacher_credentials_tbl tc
                                    LEFT JOIN teacher_info_tbl ti
                                    ON tc.id_number = ti.id_number
                                    LEFT JOIN departments_tbl dt
                                    ON tc.department_id = dt.department_id
                                    WHERE tc.id_number = :id_number AND tc.is_archived = 'No'
                                    LIMIT 1
                                    ");

    $get_teacher_details->execute([":id_number" => $teacher_details_id]);

    if($get_teacher_details->rowCount() === 1) {
        $fetched_teacher_details = $get_teacher_details->fetch(PDO::FETCH_OBJ);

        $teacher_profile_picture = $fetched_teacher_details->profile_picture;
        $locked_account = $fetched_teacher_details->locked_account;

        if(empty($teacher_profile_picture) || !file_exists($file_path . $teacher_profile_picture)) {
            $teacher_profile_picture = "default-img.png";
        }
    }

    else {
        $_SESSION["query-status"] = [
            "status" => "danger", 
            "message" => "This user does not exist in the system!"
        ];

        header("Location: home.php?page=teachers-accounts");
        exit();
    }
?>

<!-- Main -->
<main id="main" class="main">

    <!-- Page Title -->
    <div class="pagetitle">

        <?php if (isset($_SESSION["query-status"]) && $_SESSION["query-status"] !== ""): ?>
            <div class="alert alert-<?php echo $_SESSION["query-status"]["status"]; ?> text-center" id="notification" role="alert">
                <?php echo $_SESSION["query-status"]["message"]; ?>
            </div>
            <?php unset($_SESSION["query-status"]); ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between">

            <div>
                <h1> <?php echo htmlspecialchars($page_titles[$page_name]); ?> </h1>

                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="home.php?page=dashboard"> Home </a></li>
                        <li class="breadcrumb-item"> <a href="home.php?page=teachers-accounts"> Teacher Accounts </a></li>
                        <li class="breadcrumb-item active"> <?php echo htmlspecialchars($fetched_teacher_details->first_name . " " . $fetched_teacher_details->last_name); ?> </li>
                    </ol>
                </nav>

                <?php
                    if($generated_password === "Yes") { ?>

                    <div class="alert alert-danger">
                        <span class="text-danger">
                        You are currently using <span class="fw-bold">system generated password</span>.
                        <a href="home.php?page=user-profile&update-password=true" class="fw-bold text-decoration-underline text-danger">Click here to change now.</a>
                        </span>
                    </div>

                <?php 
                    } 
                ?>
            </div>

            <div>
                <h1 id="date-time" class="mt-2"> Date and Time: </h1>
            </div>

        </div>

    </div>
    <!-- End Page Title -->

    <!-- Details Main -->
    <section class="section profile">

        <div class="row">

            <div class="col-xl-4">

                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                    <img 
                    src="<?php echo htmlspecialchars($file_path . $teacher_profile_picture); ?>" 
                    alt="Profile Picture" 
                    class="rounded-circle"
                    width="110" height="110">

                    <h2> <?php echo htmlspecialchars($fetched_teacher_details->first_name . " " . $fetched_teacher_details->last_name); ?> </h2>
                    <h3> <?php echo htmlspecialchars($fetched_teacher_details->department_code . " " . $fetched_teacher_details->teacher_rank); ?> </h3>

                    <?php
                        if($locked_account === "Yes") {
                    ?>
                        <span class="badge bg-danger">
                            Locked Account
                        </span>
                    <?php
                        }
                    ?>
                    
                    </div>
                </div>

            </div>

            <div class="col-xl-8">

                <div class="card">
                    <div class="card-body pt-3">

                        <!-- Bordered Tabs -->
                        <ul class="nav nav-tabs nav-tabs-bordered">

                            <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview"> Profile Overview </button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#other-information"> Other Information </button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#update-status"> 
                                    Update Status
                                </button>
                            </li>
                            
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#account-settings"> 
                                    Account Settings
                                </button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reset-password"> Reset Password </button>
                            </li>
                           

                        </ul>

                        <div class="tab-content pt-2">

                            <!-- Overview -->
                            <div class="tab-pane fade show active profile-overview custom-overview" id="profile-overview">

                                <div class="row">

                                    <div class="col-lg-12">

                                        <div class="container">

                                            <h5 class="card-title mb-0" style="color:#2b3c49;"> Personal Information: </h5>

                                            <div class="row">

                                                <div class="col">

                                                    <div class="mb-1 mt-1">
                                                        <label for="first-name" class="form-label"> First Name: </label>
                                                        <input type="text" class="form-control" id="first-name" value="<?php echo htmlspecialchars($fetched_teacher_details->first_name); ?>" readonly disabled>
                                                    </div>

                                                </div>

                                                <div class="col">

                                                    <div class="mb-1 mt-1">
                                                        <label for="middle-name" class="form-label"> Middle Name: </label>
                                                        <input type="text" class="form-control" id="middle-name"value="<?php echo htmlspecialchars($fetched_teacher_details->middle_name); ?>" readonly disabled>
                                                    </div>

                                                </div>

                                                <div class="col">

                                                    <div class="mb-1 mt-1">
                                                        <label for="last-name" class="form-label"> Last Name: </label>
                                                        <input type="text" class="form-control" id="last-name" value="<?php echo htmlspecialchars($fetched_teacher_details->last_name); ?>" readonly disabled>
                                                    </div>

                                                </div>

                                            </div>

                                            <h5 class="card-title mb-0" style="color:#2b3c49;"> Account Credentials: </h5>

                                            <div class="row">

                                                <div class="col">

                                                    <div class="mb-1 mt-1">
                                                        <label for="id-number" class="form-label"> ID Number: </label>
                                                        <input type="text" class="form-control" id="id-number" value="<?php echo htmlspecialchars($fetched_teacher_details->id_number); ?>" readonly disabled>
                                                    </div>

                                                </div>

                                                <div class="col">

                                                    <div class="mb-1 mt-1">
                                                        <label for="email-address" class="form-label"> Email Address: </label>
                                                        <input type="email" class="form-control" id="email-address" value="<?php echo htmlspecialchars($fetched_teacher_details->email_address); ?>" readonly disabled>
                                                    </div>

                                                </div>

                                                <div class="col">

                                                    <div class="mb-1 mt-1">
                                                        <label for="role" class="form-label"> Role: </label>
                                                        <input type="text" class="form-control" id="role" value="<?php echo htmlspecialchars($fetched_teacher_details->role); ?>" readonly disabled>
                                                    </div>

                                                </div>

                                            </div>

                                            <h5 class="card-title mb-0" style="color:#2b3c49;"> Assigned Department: </h5>

                                            <div class="row">

                                                <div class="col">

                                                    <div class="mb-1 mt-1">
                                                        <label for="department" class="form-label"> Department: </label>
                                                        <input type="text" class="form-control" id="department" value="<?php echo htmlspecialchars($fetched_teacher_details->department_name . " (" . $fetched_teacher_details->department_code . ")"); ?>" readonly disabled>
                                                    </div>

                                                </div>

                                            </div>

                                            <h5 class="card-title mb-0" style="color:#2b3c49;"> Employment Details: </h5>

                                            <div class="row">

                                                <div class="col">

                                                    <div class="mb-1 mt-1">
                                                        <label for="id-number" class="form-label"> Employment Status: </label>
                                                        <input type="text" class="form-control" id="employment-status" value="<?php echo htmlspecialchars($fetched_teacher_details->employment_status); ?>" readonly disabled>
                                                    </div>

                                                </div>

                                                <div class="col">

                                                    <div class="mb-1 mt-1">
                                                        <label for="email-address" class="form-label"> Teacher Rank: </label>
                                                        <input type="email" class="form-control" id="teacher-rank" value="<?php echo htmlspecialchars($fetched_teacher_details->teacher_rank); ?>" readonly disabled>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>
                            <!-- Overview End -->

                            <!-- Information -->
                            <div class="tab-pane fade profile-overview" id="other-information">

                                <h5 class="card-title" style="color:#2b3c49;"> Primary Information </h5>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label "> Date of Birth </div>
                                    <div class="col-lg-9 col-md-8"> <?php echo htmlspecialchars(format_date($fetched_teacher_details->date_of_birth) ?? "N/A"); ?> </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label "> Gender </div>
                                    <div class="col-lg-9 col-md-8"> <?php echo htmlspecialchars($fetched_teacher_details->gender ?? "N/A"); ?> </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label"> Marital Status </div>
                                    <div class="col-lg-9 col-md-8"> <?php echo htmlspecialchars($fetched_teacher_details->marital_status ?? "N/A"); ?> </div>
                                </div>

                                <h5 class="card-title" style="color:#2b3c49;"> Contact Information </h5>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label "> Phone Number </div>
                                    <div class="col-lg-9 col-md-8"> <?php echo htmlspecialchars($fetched_teacher_details->phone_number ?? "N/A"); ?> </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label"> Telephone Number </div>
                                    <div class="col-lg-9 col-md-8"> <?php echo htmlspecialchars($fetched_teacher_details->telephone_number ?? "N/A"); ?> </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label"> Temporary Address </div>
                                    <div class="col-lg-9 col-md-8"> <?php echo htmlspecialchars($fetched_teacher_details->temporary_address ?? "N/A"); ?> </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label"> Permanent Address </div>
                                    <div class="col-lg-9 col-md-8"> <?php echo htmlspecialchars($fetched_teacher_details->permanent_address ?? "N/A"); ?> </div>
                                </div>

                                <h5 class="card-title" style="color:#2b3c49;"> Social Media Links  </h5>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label"> Facebook Link </div>
                                    <div class="col-lg-9 col-md-8"> 
                                        <?php
                                            if(!empty($fetched_teacher_details->facebook_link)) {
                                                ?>
                                                    <a href="<?php echo htmlspecialchars($fetched_teacher_details->facebook_link); ?>" target="_blank"> 
                                                        <?php echo htmlspecialchars($fetched_teacher_details->first_name . " " . $fetched_teacher_details->last_name); ?> 
                                                    </a>
                                                <?php
                                            }

                                            else {
                                                echo "N/A";
                                            }
                                        ?>
                                    </div>
                                </div>

                                <h5 class="card-title" style="color:#2b3c49;"> Account Editing/Modify </h5>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label"> Last Updated At </div>
                                    <div class="col-lg-9 col-md-8"> 
                                        <?php echo format_timestamp(htmlspecialchars($fetched_teacher_details->updated_at)); ?> 
                                    </div>
                                </div>

                            </div>
                            <!-- Information End -->
                
                            <!-- Update Status -->
                            <div class="tab-pane fade" id="update-status">

                                <div class="row">

                                    <!-- Update Status -->
                                    <div class="col-12">

                                        <form action="../../process/admin/account-management.php" method="POST" class="custom-form" id="set-employment-form">

                                            <input type="hidden" name="teacher-user-id" value="<?php echo htmlspecialchars(base64_encode($teacher_details_id)); ?>">
                                            <input type="hidden" name="admin-id" value="<?php echo htmlspecialchars($id_number); ?>">

                                            <div class="container mt-0">

                                                <h5 class="card-title mb-0"> Update Employment Status: </h5>

                                                <div class="row">

                                                    <div class="col-lg-6 col-md-12 mb-3">

                                                        <select class="form-select" name="employment-status" required>
                                                            <option value="Contractual" <?php echo htmlspecialchars($fetched_teacher_details->employment_status === "Contractual" ? "selected" : ""); ?>> Contractual </option>
                                                            <option value="Full-Time" <?php echo htmlspecialchars($fetched_teacher_details->employment_status === "Full-Time" ? "selected" : ""); ?>> Full-Time </option>
                                                            <option value="Part-Time" <?php echo htmlspecialchars($fetched_teacher_details->employment_status === "Part-Time" ? "selected" : ""); ?>> Part-Time </option>
                                                        </select>

                                                    </div>

                                                    <div class="col-lg-6 col-md-12">

                                                        <select class="form-select" name="teacher-rank" required>
                                                            <option value="Instructor" <?php echo htmlspecialchars($fetched_teacher_details->teacher_rank === "Instructor" ? "selected" : ""); ?>> Instructor </option>
                                                            <option value="Assistant Professor" <?php echo htmlspecialchars($fetched_teacher_details->teacher_rank === "Assistant Professor" ? "selected" : ""); ?>> Assistant Professor </option>
                                                            <option value="Associate Professor" <?php echo htmlspecialchars($fetched_teacher_details->teacher_rank === "Associate Professor" ? "selected" : ""); ?>> Associate Professor </option>
                                                            <option value="Professor" <?php echo htmlspecialchars($fetched_teacher_details->teacher_rank === "Professor" ? "selected" : ""); ?>> Professor </option>
                                                            <option value="Exemplary Teacher" <?php echo htmlspecialchars($fetched_teacher_details->teacher_rank === "Exemplary Teacher" ? "selected" : ""); ?>> Exemplary Teacher </option>
                                                            <option value="Master Teacher" <?php echo htmlspecialchars($fetched_teacher_details->teacher_rank === "Master Teacher" ? "selected" : ""); ?>> Master Teacher </option>
                                                            <option value="N/A" <?php echo htmlspecialchars($fetched_teacher_details->teacher_rank === "N/A" ? "selected" : ""); ?>> N/A </option>
                                                        </select>

                                                    </div>

                                                </div>

                                                <p> Please enter your password to update this account's employment status and rank. </p>

                                                <div class="row">

                                                    <div class="col-lg-12">

                                                        <div class="form-floating mb-3">
                                                            <input type="password" class="form-control" id="floatingInput" placeholder="Enter your password" name="admin-password" required>
                                                            <label for="floatingInput"> Enter your password </label>
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col">

                                                        <input type="hidden" name="update-employment-status" value="1">

                                                        <button
                                                            type="submit"
                                                            class="btn btn-success"
                                                            onclick="confirmAction(
                                                                    event, 
                                                                    this.form, 
                                                                    'set-employment-form',
                                                                    'Update Employment?', 
                                                                    'question',
                                                                    'Are you sure you want to update <?php echo htmlspecialchars($fetched_teacher_details->first_name . ' ' . $fetched_teacher_details->last_name); ?>\'s employment status?',
                                                                    'Update Status',
                                                                    '#2eca6a'
                                                                )"
                                                            title="Unlock Account">
                                                            Update Status
                                                        </button>

                                                    </div>
                                                </div>

                                            </div>

                                        </form>

                                    </div>
                                    <!-- Update End -->

                                </div>

                            </div>
                            <!-- Update Status End -->

                            <!-- Account Settings -->
                            <div class="tab-pane fade" id="account-settings">

                                <div class="row">

                                    <!-- Lock/Unlock Account -->
                                    <div class="col-12">

                                        <form action="../../process/admin/account-management.php" method="POST" class="custom-form" id="set-status-form">

                                            <input type="hidden" name="teacher-user-id" value="<?php echo htmlspecialchars(base64_encode($teacher_details_id)); ?>">
                                            <input type="hidden" name="admin-id" value="<?php echo htmlspecialchars($id_number); ?>">

                                            <div class="container mt-0">

                                                <h5 class="card-title mb-0"> <?php echo htmlspecialchars($locked_account === "Yes" ? "Unlock" : "Lock"); ?> Account: </h5>

                                                <p> Please enter your password to <?php echo htmlspecialchars($locked_account === "Yes" ? "unlock" : "lock"); ?> this account. </p>

                                                <div class="row">

                                                    <div class="col-lg-12">

                                                        <div class="form-floating mb-3">
                                                            <input type="password" class="form-control" id="floatingInput" placeholder="Enter your password" name="admin-password" required>
                                                            <label for="floatingInput"> Enter your password </label>
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col">
                                                        <?php
                                                        if ($locked_account === "Yes") {
                                                        ?>
                                                            <input type="hidden" name="unlock-teacher-account" value="1">
                                                            <button
                                                                type="submit"
                                                                class="btn btn-success"
                                                                onclick="confirmAction(
                                                                    event, 
                                                                    this.form, 
                                                                    'set-status-form',
                                                                    'Unlock Account?', 
                                                                    'question',
                                                                    'Are you sure you want to unlock <?php echo htmlspecialchars($fetched_teacher_details->first_name . ' ' . $fetched_teacher_details->last_name); ?>\'s account?',
                                                                    'Unlock Account',
                                                                    '#2eca6a'
                                                                )"
                                                                title="Unlock Account">
                                                                Unlock Account
                                                            </button>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <input type="hidden" name="lock-teacher-account" value="1">
                                                            <button
                                                                type="submit"
                                                                class="btn btn-danger"
                                                                onclick="confirmAction(
                                                                    event, 
                                                                    this.form, 
                                                                    'set-status-form',
                                                                    'Lock Account?', 
                                                                    'question',
                                                                    'Are you sure you want to lock <?php echo htmlspecialchars($fetched_teacher_details->first_name . ' ' . $fetched_teacher_details->last_name); ?>\'s account?',
                                                                    'Lock Account',
                                                                    '#dc3545'
                                                                )"
                                                                title="Lock Account">
                                                                Lock Account
                                                            </button>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>

                                            </div>

                                        </form>

                                    </div>
                                    <!-- Lock Account End -->

                                    <!-- Archive Account -->
                                    <div class="col-12 mt-2">

                                        <form action="../../process/admin/account-management.php" method="POST" class="custom-form" id="set-archive-form">

                                            <input type="hidden" name="teacher-user-id" value="<?php echo htmlspecialchars(base64_encode($teacher_details_id)); ?>">
                                            <input type="hidden" name="admin-id" value="<?php echo htmlspecialchars($id_number); ?>">

                                            <div class="container mt-0">

                                                <h5 class="card-title mb-0"> Archive Account: </h5>

                                                <p> Please enter your password to archive this account. </p>

                                                <div class="row">

                                                    <div class="col-lg-12">

                                                        <div class="form-floating mb-3">
                                                            <input type="password" class="form-control" id="floatingInput" placeholder="Enter your password" name="admin-password" required>
                                                            <label for="floatingInput"> Enter your password </label>
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col">

                                                        <input type="hidden" name="archive-teacher-account" value="1">
                                                        <button
                                                            type="submit"
                                                            class="btn btn-danger"
                                                            onclick="confirmAction(
                                                                    event, 
                                                                    this.form, 
                                                                    'set-archive-form',
                                                                    'Archive Account?', 
                                                                    'question',
                                                                    'Are you sure you want to archive <?php echo htmlspecialchars($fetched_teacher_details->first_name . ' ' . $fetched_teacher_details->last_name); ?>\'s account?',
                                                                    'Archive Account',
                                                                    '#dc3545'
                                                                )"
                                                            title="Archive Account">
                                                            Archive Account
                                                        </button>

                                                    </div>
                                                </div>

                                            </div>

                                        </form>

                                    </div>
                                    <!-- Archive Account End -->

                                </div>

                            </div>
                            <!-- Account Settings End -->

                            <!-- Reset Password -->
                            <div class="tab-pane fade" id="reset-password">

                                <div class="row">

                                    <!-- Reset Password -->
                                    <div class="col-12">

                                        <form action="../../process/admin/account-management.php" method="POST" class="custom-form" id="reset-password-form">

                                            <input type="hidden" name="teacher-user-id" value="<?php echo htmlspecialchars(base64_encode($teacher_details_id)); ?>">
                                            <input type="hidden" name="reset-teacher-password" value="1">

                                            <div class="container mt-0">

                                                <h5 class="card-title mb-0"> Reset Account Password: </h5>

                                                <p>
                                                    Please enter the user's
                                                    <strong>ID number</strong> and
                                                    <strong>email address</strong>
                                                    to reset password.
                                                </p>

                                                <div class="row">

                                                    <div class="col">
                                                        <div class="form-floating mb-3">
                                                            <input type="text" class="form-control" id="floatingInput" placeholder="ID Number" name="id-number" required>
                                                            <label for="floatingInput"> ID Number </label>
                                                        </div>
                                                    </div>

                                                    <div class="col">
                                                        <div class="form-floating mb-3">
                                                            <input type="email" class="form-control" id="floatingInput" placeholder="Email Address" name="email-address" required>
                                                            <label for="floatingInput"> Email Address </label>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row mt-2">
                                                    <div class="col">
                                                        <button
                                                            type="submit"
                                                            class="btn btn-primary custom-add-btn"
                                                            onclick="confirmAction(
                                                                event, 
                                                                this.form, 
                                                                'reset-password-form',
                                                                'Reset Password?', 
                                                                'question',
                                                                'Are you sure you want to reset <?php echo htmlspecialchars($fetched_teacher_details->first_name . ' ' . $fetched_teacher_details->last_name); ?>\'s password?',
                                                                'Reset Password',
                                                                '#2eca6a'
                                                            )"
                                                            title="Reset Password">
                                                            Reset Password
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>

                                        </form>

                                    </div>
                                    <!-- End Reset Password -->

                                </div>

                            </div>
                            <!-- End Reset Password -->

                        </div>
                        <!-- End Bordered Tabs -->

                    </div>
                </div>

            </div>
            
        </div>

    </section>
    <!-- End Details Main -->

</main>
<!-- End #main -->