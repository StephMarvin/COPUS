<!-- Main -->
<main id="main" class="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Form -->
    <section class="section">

        <div class="row">

            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">

                        <div class="px-3">
                            <h5 class="card-title custom-card-title mb-0"> Add New Observer </h5>
                            <p class="lh-1 mb-0 text-muted"> Please ensure that all information provided is accurate. </p>
                        </div>

                        <hr>

                        <form action="../../process/dean/account-management.php" method="POST" class="custom-form" autocomplete="off">

                            <input type="hidden" name="dean-id" value="<?php echo htmlspecialchars($id_number); ?>">
                            <input type="hidden" name="department-id" value="<?php echo htmlspecialchars(base64_encode($department_id)); ?>">

                            <div class="container mt-1">

                                <h5 class="card-title mb-0"> Personal Information: </h5>

                                <div class="row">

                                    <div class="col-lg-4 col-md-12">

                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="floatingInput" placeholder="First Name" name="first-name" required>
                                            <label for="floatingInput"> First Name </label>
                                        </div>

                                    </div>

                                    <div class="col-lg-4 col-md-12">  
                                        
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="floatingInput" placeholder="Middle Name" name="middle-name">
                                            <label for="floatingInput"> Middle Name(Optional) </label>
                                        </div>

                                    </div>

                                    <div class="col-lg-4 col-md-12">

                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="floatingInput" placeholder="Last Name" name="last-name" required>
                                            <label for="floatingInput"> Last Name </label>
                                        </div>

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-lg-4 col-md-12 mb-3">

                                        <select class="form-select" name="designation" required>
                                            <option selected disabled value=""> SELECT DESIGNATION </option>
                                            <option value="Dean"> Dean </option>  
                                            <option value="Asst. Dean"> Asst. Dean </option>   
                                            <option value="Program Head"> Program Head </option>   
                                            <option value="Faculty"> Faculty </option> 
                                            <option value="Active Learning Coach (ALC)"> Active Learning Coach (ALC) </option> 
                                        </select>

                                    </div>

                                    <div class="col-lg-4 col-md-12 mb-3">

                                        <select class="form-select" name="gender" required>
                                            <option selected disabled value=""> SELECT GENDER </option>
                                            <option value="Male"> Male </option>
                                            <option value="Female"> Female </option>
                                            <option value="Others"> Others </option>   
                                        </select>

                                    </div>

                                    <div class="col-lg-4 col-md-12">

                                        <select class="form-select" name="marital-status" required>
                                            <option selected disabled value=""> SELECT MARITAL STATUS </option>
                                            <option value="Single"> Single </option>  
                                            <option value="Married"> Married </option>
                                            <option value="Divorced"> Divorced </option>
                                            <option value="Widowed"> Widowed </option>
                                        </select>

                                    </div>

                                </div>

                                <h5 class="card-title mt-3 mb-0"> Login Credentials and Contact Information: </h5>

                                <div class="row">

                                    <div class="col-lg-4 col-md-12">

                                        <div class="col">

                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="floatingInput" placeholder="ID Number" name="id-number" required>
                                                <label for="floatingInput"> ID Number </label>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-4 col-md-12">

                                        <div class="col">

                                            <div class="form-floating mb-3">
                                                <input type="email" class="form-control" id="floatingInput" placeholder="Email Address" name="email-address" required>
                                                <label for="floatingInput"> Email Address </label>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-4 col-md-12">

                                        <div class="col">

                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="floatingInput" placeholder="Phone Number" name="phone-number">
                                                <label for="floatingInput"> Phone Number (Optional) </label>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-lg-6 col-md-12">

                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="floatingInput" placeholder="Temporary Address" name="temporary-address">
                                            <label for="floatingInput"> Temporary Address(Optional) </label>
                                        </div>
                                        
                                    </div>

                                    <div class="col-lg-6 col-md-12">

                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="floatingInput" placeholder="Permanent Address" name="permanent-address">
                                            <label for="floatingInput"> Permanent Address (Optional) </label>
                                        </div>

                                    </div>

                                </div>

                                <h5 class="card-title mt-3 mb-0"> Enter Your Password to Confirm: </h5>

                                <div class="row">

                                    <div class="col">

                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" id="floatingInput" placeholder="Enter your Password" name="dean-password" required>
                                            <label for="floatingInput"> Enter your Password </label>
                                        </div>

                                    </div>

                                    <div class="col">

                                        <div class="form-check">
                                            <input class="form-check-input border border-secondary" type="checkbox" value="true" id="confirm-add" name="confirmation" required>

                                            <label class="form-check-label" for="confirm-add">
                                                I hereby confirm the addition of this new observer account to the system.
                                            </label>

                                        </div>

                                    </div>   

                                </div>

                                <button type="submit" name="add-new-observer" class="btn btn-primary custom-add-btn" > Add Observer </button>

                            </div>

                        </form>

                    </div>
                </div>
            </div>    

        </div>

    </section>
    <!-- Form End -->
 
</main>
<!-- End Main -->