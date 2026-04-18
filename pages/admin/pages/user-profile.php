<?php
  $update_profile = false;
  $update_photo = false;
  $update_password = false;

  $active_tab = "overview";

  if(isset($_GET["update-profile"]) && isset($_GET["update-profile"]) === true) {
    $active_tab = "profile";
  }
  else if(isset($_GET["update-photo"]) && isset($_GET["update-photo"]) === true) {
    $active_tab = "photo";
  }
  else if(isset($_GET["update-password"]) && isset($_GET["update-password"]) === true) {
    $active_tab = "password";
  }

  $two_fa_enabled = $two_factor_authentication === "Enabled" ? "success" : "danger";
?>

<!-- Main -->
<main id="main" class="main">

  <?php include_once "includes/pagetitle.php"; ?>

  <!-- Details Main -->
  <section class="section profile">

    <div class="row">

      <div class="col-xl-4">

        <div class="card mb-2">
          <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

            <img 
            src="<?php echo htmlspecialchars($file_path . $profile_picture); ?>" 
            alt="Profile Picture" 
            class="rounded-circle"
            width="110" height="110">

            <h2> <?php echo htmlspecialchars($first_name . " " . $last_name); ?> </h2>
            <h3> <?php echo htmlspecialchars($role); ?> </h3>

          </div>
        </div>

        <div class="card">
          <div class="card-body profile-card">

            <h5 class="card-title mb-0" style="color:#2b3c49;"> Security Check: </h5>

            <?php
              if($generated_password === "No" && $two_factor_authentication === "Enabled") {
            ?>
              <div class="alert alert-success p-2">
                <span class="text-success" style="font-size: 12px;">
                  Your account is <span class="fw-bold">fully secured</span>.
                </span>
              </div>
            <?php
              } else {
                if($generated_password === "Yes") {
            ?>
              <div class="alert alert-danger p-2">
                <span class="text-danger" style="font-size: 13px;">
                  You are currently using <span class="fw-bold">system generated password</span>. <br>
                  <a href="home.php?page=user-profile&update-password=true" class="fw-bold text-decoration-underline text-danger">Click here to change now.</a>
                </span>
              </div>
            <?php
                }
                if($two_factor_authentication === "Disabled") {
            ?>
              <div class="alert alert-warning p-2">
                <span class="text-dark" style="font-size: 13px;">
                  Two-factor authentication is currently <span class="fw-bold">disabled</span>. <br>
                  <a href="home.php?page=user-profile&update-password=true" class="fw-bold text-decoration-underline text-dark">Click here to enable.</a>
                </span>
              </div>
            <?php
                }
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
                <button class="nav-link <?php echo htmlspecialchars($active_tab === "overview" ? "active" : ""); ?>" data-bs-toggle="tab" data-bs-target="#profile-overview"> Profile Overview </button>
              </li>

              <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#other-information"> Admin Details </button>
              </li>

              <li class="nav-item">
                <button class="nav-link <?php echo htmlspecialchars($active_tab === "profile" ? "active" : ""); ?>" data-bs-toggle="tab" data-bs-target="#edit-information"> Edit Profile </button>
              </li>

              <li class="nav-item">
                <button class="nav-link <?php echo htmlspecialchars($active_tab === "photo" ? "active" : ""); ?>" data-bs-toggle="tab" data-bs-target="#picture-edit"> Change Photo </button>
              </li>

              <li class="nav-item">
                <button class="nav-link <?php echo htmlspecialchars($active_tab === "password" ? "active" : ""); ?>" data-bs-toggle="tab" data-bs-target="#change-password"> Change Password </button>
              </li>

            </ul>

            <div class="tab-content pt-2">

              <!-- Overview -->
              <div class="tab-pane fade <?php echo htmlspecialchars($active_tab === "overview" ? "show active" : ""); ?> profile-overview custom-overview" id="profile-overview">

                <div class="row">
                  <div class="col-lg-12">

                    <div class="container">

                      <h5 class="card-title mb-0" style="color:#2b3c49;"> Personal Information: </h5>

                      <div class="row">
                        <div class="col">
                          <div class="mb-1 mt-1">
                            <label class="form-label"> First Name: </label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($first_name); ?>" readonly disabled>
                          </div>
                        </div>

                        <div class="col">
                          <div class="mb-1 mt-1">
                            <label class="form-label"> Middle Name: </label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($middle_name); ?>" readonly disabled>
                          </div>
                        </div>

                        <div class="col">
                          <div class="mb-1 mt-1">
                            <label class="form-label"> Last Name: </label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($last_name); ?>" readonly disabled>
                          </div>
                        </div>
                      </div>

                      <h5 class="card-title mb-0" style="color:#2b3c49;"> Account Credentials: </h5>

                      <div class="row">
                        <div class="col">
                          <div class="mb-1 mt-1">
                            <label class="form-label"> ID Number: </label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($id_number); ?>" readonly disabled>
                          </div>
                        </div>

                        <div class="col">
                          <div class="mb-1 mt-1">
                            <label class="form-label"> Email Address: </label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($email_address); ?>" readonly disabled>
                          </div>
                        </div>

                        <div class="col">
                          <div class="mb-1 mt-1">
                            <label class="form-label"> Role: </label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($role); ?>" readonly disabled>
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
                  <div class="col-lg-3 col-md-4 label"> Date of Birth </div>
                  <div class="col-lg-9 col-md-8"> <?php echo htmlspecialchars(format_date($date_of_birth) ?? "N/A"); ?> </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label "> Gender </div>
                  <div class="col-lg-9 col-md-8"> <?php echo htmlspecialchars($gender ?? "N/A"); ?> </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label"> Marital Status </div>
                  <div class="col-lg-9 col-md-8"> <?php echo htmlspecialchars($marital_status ?? "N/A"); ?> </div>
                </div>

                <h5 class="card-title" style="color:#2b3c49;"> Contact Information </h5>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label "> Phone Number </div>
                  <div class="col-lg-9 col-md-8"> <?php echo htmlspecialchars($phone_number ?? "N/A"); ?> </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label"> Telephone Number </div>
                  <div class="col-lg-9 col-md-8"> <?php echo htmlspecialchars($telephone_number ?? "N/A"); ?> </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label"> Temporary Address </div>
                  <div class="col-lg-9 col-md-8"> <?php echo htmlspecialchars($temporary_address ?? "N/A"); ?> </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label"> Permanent Address </div>
                  <div class="col-lg-9 col-md-8"> <?php echo htmlspecialchars($permanent_address ?? "N/A"); ?> </div>
                </div>

                <h5 class="card-title" style="color:#2b3c49;"> Social Media Links </h5>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label"> Facebook Link </div>
                  <div class="col-lg-9 col-md-8">
                    <?php
                      if(!empty($facebook_link)) {
                        ?>
                          <a href="<?php echo htmlspecialchars($facebook_link); ?>" target="_blank"> 
                            <?php echo htmlspecialchars($first_name . " " . $last_name); ?> 
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
                  <div class="col-lg-3 col-md-4 label"> Last Updated </div>
                  <div class="col-lg-9 col-md-8"> <?php echo htmlspecialchars(format_timestamp($updated_at)); ?> </div>
                </div>

              </div>
              <!-- Information End -->

              <!-- Edit Infromation -->
              <div class="tab-pane fade <?php echo htmlspecialchars($active_tab === "profile" ? "show active" : ""); ?> pt-3" id="edit-information">

                  <!-- Profile Edit Form -->
                <form action="../../process/admin/profile-update.php" method="POST" class="custom-form">

                  <input type="hidden" name="admin-id" value="<?php echo htmlspecialchars($id_number); ?>">

                  <div class="row mb-3">
                    <label for="f-name" class="col-md-4 col-lg-3 col-form-label"> First Name: </label>
                    <div class="col-md-8 col-lg-9">
                      <input type="text" class="form-control" id="f-name" name="first-name" placeholder="First Name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="m-name" class="col-md-4 col-lg-3 col-form-label"> Middle Name: </label>
                    <div class="col-md-8 col-lg-9">
                      <input type="text" class="form-control" id="m-name" name="middle-name" placeholder="Middle Name (Optional)" value="<?php echo htmlspecialchars($middle_name); ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="l-name" class="col-md-4 col-lg-3 col-form-label"> Last Name: </label>
                    <div class="col-md-8 col-lg-9">
                      <input type="text" class="form-control" id="l-name" name="last-name" placeholder="Last Name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="dob" class="col-md-4 col-lg-3 col-form-label"> Date of Birth: </label>
                    <div class="col-md-8 col-lg-9">
                      <input type="date" class="form-control" id="dob" name="date-of-birth" value="<?php echo htmlspecialchars($date_of_birth); ?>" required>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="gend" class="col-md-4 col-lg-3 col-form-label"> Gender: </label>
                    <div class="col-md-8 col-lg-9">
                      <select class="form-select" name="gender" id="gend" required style="height: 40px;">
                          <option value="Male" <?php echo htmlspecialchars($gender === "Male" ? "selected" : ""); ?>> Male </option>
                          <option value="Female" <?php echo htmlspecialchars($gender === "Female" ? "selected" : ""); ?>> Female </option>
                          <option value="Others" <?php echo htmlspecialchars($gender === "Others" ? "selected" : ""); ?>> Others </option>
                      </select>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="status" class="col-md-4 col-lg-3 col-form-label"> Marital Status: </label>
                    <div class="col-md-8 col-lg-9">
                      <select class="form-select" name="marital-status" id="status" required style="height: 40px;">
                          <option value="Single" <?php echo htmlspecialchars($marital_status === "Single" ? "selected" : ""); ?>> Single </option>
                          <option value="Married" <?php echo htmlspecialchars($marital_status === "Married" ? "selected" : ""); ?>> Married </option>
                          <option value="Divorced" <?php echo htmlspecialchars($marital_status === "Divorced" ? "selected" : ""); ?>> Divorced </option>
                          <option value="Widowed" <?php echo htmlspecialchars($marital_status === "Widowed" ? "selected" : ""); ?>> Widowed </option>
                      </select>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="contact" class="col-md-4 col-lg-3 col-form-label"> Phone Number: </label>
                    <div class="col-md-8 col-lg-9">
                      <input type="text" class="form-control" id="contact" name="phone-number" placeholder="Phone Number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="telephone" class="col-md-4 col-lg-3 col-form-label"> Telephone Number: </label>
                    <div class="col-md-8 col-lg-9">
                      <input type="text" class="form-control" id="telephone" name="telephone-number" placeholder="Telephone Number (Optional)" value="<?php echo htmlspecialchars($telephone_number); ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="temp-address" class="col-md-4 col-lg-3 col-form-label"> Temporary Address: </label>
                    <div class="col-md-8 col-lg-9">
                      <input type="text" class="form-control" id="temp-address" name="temporary-address" placeholder="Temporary Address (Optional)" value="<?php echo htmlspecialchars($temporary_address); ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="perm-address" class="col-md-4 col-lg-3 col-form-label"> Permanent Address: </label>
                    <div class="col-md-8 col-lg-9">
                      <input type="text" class="form-control" id="perm-address" name="permanent-address" placeholder="Permanent Address" value="<?php echo htmlspecialchars($permanent_address); ?>" required>
                    </div>
                  </div>

                  <div class="row mb-3">
                      <label for="fb-link" class="col-md-4 col-lg-3 col-form-label"> Facebook Link: </label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control" id="fb-link" name="facebook-link" placeholder="Facebook Link" value="<?php echo htmlspecialchars($facebook_link); ?>">
                      </div>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn btn-primary float-end custom-save-btn" name="update-profile"> Save Changes </button>
                  </div>
                </form>
                <!-- End Profile Edit Form -->

              </div>
              <!-- Edit Information End -->

              <!-- Picture Edit -->
              <div class="tab-pane fade <?php echo htmlspecialchars($active_tab === "photo" ? "show active" : ""); ?> pt-3" id="picture-edit">

                <!-- Profile Edit Form -->
                <form action="../../process/admin/profile-update.php" method="POST" enctype="multipart/form-data">

                  <div class="row mb-3">
                    <label for="profile-image" class="col-md-4 col-lg-3 col-form-label"> Profile Image </label>

                    <div class="col-md-8 col-lg-9">

                      <img 
                      id="profile-preview" 
                      src="<?php echo htmlspecialchars($file_path . $profile_picture); ?>" 
                      alt="Profile Picture" 
                      width="110" height="110">

                      <div class="pt-2">

                          <label for="upload-pic" style="color: white;" class="btn btn-primary btn-sm custom-save-btn" title="Upload new profile image">
                              <i class="bi bi-upload"></i>
                          </label>
                     
                          <input type="file" style="display: none;" id="upload-pic" accept="image/*" name="uploaded-photo" required>
                      </div>
                    </div>
                  </div>      

                  <div class="text-center">
                    <button type="submit" name="update-profile-picture" class="btn btn-primary float-end custom-save-btn"> Update Photo </button>
                  </div>
                </form><!-- End Profile Edit Form -->

                <form action="../../server/a/admin-profile-update.php" method="POST" class="float-end d-none">
                  <input type="hidden" name="admin-id" value="<?php echo $admin_id; ?>">
                  <input type="submit" name="delete-pic" id="delete-img">
                </form>

              </div>
              <!-- End Picture Edit -->
       
              <!-- Change Password -->
              <div class="tab-pane fade <?php echo htmlspecialchars($active_tab === "password" ? "show active" : ""); ?> p-0" id="change-password">

                  <div>

                    <h5 class="card-title mb-0" style="color:#2b3c49;">
                      Change Password
                      <span class="text-danger fs-6"><?php echo htmlspecialchars($generated_password === "Yes" ? " (Required)" : ""); ?></span>
                      :
                    </h5>

                    <p class="text-muted fst-italic" style="font-size: 14px;">
                      Password change is required if you are using a system-generated password.
                    </p>

                  </div>
                  
                  <!-- Change Password Form -->
                  <form action="../../process/admin/profile-update.php" method="POST" class="custom-form">

                    <div class="row mb-3">
                      <label for="curr-password" class="col-md-4 col-lg-3 col-form-label"> Current Password: </label>
                      <div class="col-md-8 col-lg-9">
                        <input type="password" class="form-control" id="curr-password" name="current-password" placeholder="Current Password" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="new-pword" class="col-md-4 col-lg-3 col-form-label"> New Password: </label>
                      <div class="col-md-8 col-lg-9">
                        <input type="password" class="form-control" id="new-password" name="new-password" placeholder="New Password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$" title="Passwords must at least 8 characters long, at least 1 uppercase, lowecase letters, digits and symbols." required>
                        <div id="passwordError" class="error text-danger"></div>
                      </div>
                    </div>

                    <div class="row mb-1">
                      <label for="conf-new-pword" class="col-md-4 col-lg-3 col-form-label"> Confirm New Password: </label>
                      <div class="col-md-8 col-lg-9">
                        <input type="password" class="form-control" id="confirm-password" name="confirm-new-password" placeholder="Confirm New Password" required>
                        <div id="confirmPasswordError" class="error text-danger"></div>
                      </div>
                    </div>
  
                    <div class="form-check mb-3">
                      <input class="form-check-input border border-secondary" type="checkbox" id="toggle-passwords">

                      <label class="form-check-label" for="toggle-passwords">
                          Show Passwords
                      </label>

                    </div>
                    
                    <?php include_once("../global-includes/password-validator.php"); ?>
  
                    <div class="text-center">
                      <button type="submit" class="btn btn-primary float-end custom-save-btn" name="update-password"> Change Password </button>
                    </div>
                  </form>
                  <!-- End Change Password Form -->

                  <div>

                    <h5 class="card-title mb-0 mt-5" style="color:#2b3c49;">
                      Two-Factor Authentication: 
                      <span class="badge bg-<?php echo htmlspecialchars($two_fa_enabled); ?> text-white p-2">
                        <?php echo htmlspecialchars($two_factor_authentication); ?>
                      </span>
                    </h5>

                    <p class="text-muted fst-italic" style="font-size: 14px;">
                      Enabling two-factor authentication strengthens the protection of your account.
                    </p>
                  </div>

                  <div class="container">
                  
                    <form action="../../process/admin/profile-update.php" method="POST" id="toggle-two-factor">

                      <input type="hidden" name="toggle-two-factor" value="Disabled">
                    
                      <div class="form-check form-switch px-4 py-1">
                  
                        <input 
                        class="form-check-input border border-secondary" 
                        type="checkbox" 
                        role="switch" 
                        id="two-factor" 
                        name="toggle-two-factor"
                        value="Enabled" 
                        <?php echo $two_factor_authentication === "Enabled" ? "checked" : "" ?>
                        >

                        <label class="form-check-label" for="two-factor">
                          <?php echo htmlspecialchars($two_factor_authentication === "Enabled" ? "Disable" : "Enable"); ?> 
                          Two-Factor Authentication
                        </label>
                      </div>

                    </form>

                  </div>

              </div>
              <!-- Change Password End -->

            </div>

          </div>
        </div>

      </div>

    </div>

  </section>

</main>