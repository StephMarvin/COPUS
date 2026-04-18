<!-- Main -->
<main id="main" class="main">

    <?php include_once "includes/pagetitle.php"; ?>

    <!-- Table -->
    <section class="section">

        <div class="row">

            <div class="col-lg-12">
                
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
                                        <th scope="col"> Details </th>  
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
                                                        <span class="badge bg-success px-3 py-2"> <?php echo htmlspecialchars($academic_data["status"]); ?> </span>
                                                    <?php } ?>

                                                    <?php if($academic_data["status"] === "Inactive") { ?>
                                                        <span class="badge bg-danger px-3 py-2"> <?php echo htmlspecialchars($academic_data["status"]); ?> </span>
                                                    <?php } ?>
                                                </td>

                                                <td> <?php echo htmlspecialchars(format_timestamp($academic_data["created_at"])); ?> </td>   
                                                <td> <?php echo htmlspecialchars(format_timestamp($academic_data["modified_at"])); ?> </td>   

                                                <td>
                                                    <?php if($academic_data["status"] === "Active") { ?>
                                                        <a href="home.php?page=academic-year-details&academic-year-id=<?php echo htmlspecialchars(base64_encode($academic_data["academic_year_id"])); ?>" class="btn btn-primary custom-add-btn btn-sm" > 
                                                            Details 
                                                        </a>
                                                    <?php } else { ?>
                                                        <span class="text-danger fw-bold"> Inactive Year </span>
                                                    <?php } ?>
                                                </td>

                                            </tr>
                                        <?php
                                        }
                                    }

                                    else {
                                        ?>
                                        <tr>
                                            <td colspan="5"> No academic year data. </td>
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