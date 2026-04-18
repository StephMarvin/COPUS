<?php
    $academic_year_id = isset($_GET["academic-year-id"]) ? htmlspecialchars(base64_decode($_GET["academic-year-id"])) : null;
    $get_academic_year_details = $conn->prepare("SELECT * FROM academic_years_tbl WHERE academic_year_id = :academic_id");
    $get_academic_year_details->execute([":academic_id" => $academic_year_id]);

    if($get_academic_year_details->rowCount() > 0) {
        $academic_year_details = $get_academic_year_details->fetch(PDO::FETCH_OBJ);
        $academic_year = $academic_year_details->academic_year;
    }

    else {
        $_SESSION["query-status"] = [
            "status" => "danger", 
            "message" => "Academic year not found!"
        ];

        header("Location: home.php?page=academic-settings");
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
                        <li class="breadcrumb-item"> <a href="home.php?page=academic-settings"> Academic Settings </a></li>
                        <li class="breadcrumb-item active"> <?php echo htmlspecialchars("Academic Year: " . $academic_year); ?> </li>
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

    <!-- Table -->
    <section class="section">

        <div class="row">

            <div class="col-lg-12">
                
                <!-- List of Academic Years -->

                <?php
                    $get_semesters = $conn->prepare("SELECT 
                                                    ay.*, s.* 
                                                    FROM academic_years_tbl ay 
                                                    LEFT JOIN semesters_tbl s 
                                                    ON ay.academic_year_id = s.academic_year_id
                                                    WHERE s.academic_year_id = :academic_id");
                    $get_semesters->execute([":academic_id" => $academic_year_id]);

                    while($semester_data = $get_semesters->fetch()) {
                        $semester_id = $semester_data["semester_id"];
                        ?>
                        <div class="card shadow mb-3">

                            <div class="card-header custom-bg text-white mb-2 py-0 px-4">
                                <h5 class="card-title custom-card-title text-white d-flex align-items-center gap-3"> <?php echo htmlspecialchars("Academic Year " . $academic_year . ": " . $semester_data["semester"]); ?> <?php echo $semester_data["semester_status"] === "Active" ? "<span class='badge bg-success text-white px-3 py-2'> Active </span>" : "<span class='badge bg-danger text-white px-3 py-2'> Inactive </span>"; ?> </h5>
                            </div>

                            <div class="card-body">

                            <div class="table-responsive">
                                <table class="table table-striped table-hover custom-table mt-2 mb-1">
                                
                                <thead>

                                    <tr>
                                        <th scope="col"> Academic Year </th>
                                        <th scope="col"> Semester </th>
                                        <th scope="col"> Semester Status </th>
                                        <th scope="col"> Set Semester Status </th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <tr>
                                        <td class="fw-bold"> <?php echo htmlspecialchars($academic_year); ?> </td>
                                        <td class="fw-bold"> <?php echo htmlspecialchars($semester_data["semester"]); ?> </td>

                                        <td>                                          
                                            <?php if($semester_data["semester_status"] === "Active") { ?>
                                                <span class="text-success fw-bold"> <?php echo htmlspecialchars($semester_data["semester_status"]); ?> </span>
                                            <?php } else { ?>
                                                <span class="text-danger fw-bold"> <?php echo htmlspecialchars($semester_data["semester_status"]); ?> </span>
                                            <?php } ?>
                                        </td>

                                        <td>
                                            <?php if($semester_data["status"] === "Inactive") { ?>
                                                <span class="text-danger fw-bold"> Not Active Academic Year </span>
                                            <?php } else { ?>

                                            <?php if($semester_data["semester_status"] === "Inactive") { ?>
                                                <form action="../../process/admin/academic-management.php" method="POST" id="set-semester-form">

                                                    <input type="hidden" name="semester-id" value="<?php echo htmlspecialchars(base64_encode($semester_data["semester_id"])); ?>">
                                                    <input type="hidden" name="academic-year-id" value="<?php echo htmlspecialchars(base64_encode($academic_year_id)); ?>">
                                                    <input type="hidden" name="set-semester-active" value="1">

                                                    <button 
                                                    type="submit" 
                                                    class="btn btn-success btn-sm" 
                                                    style="font-size: 12px;"
                                                    title="Set Semester"
                                                    onclick="return confirmAction(
                                                        event,
                                                        this.form,
                                                        'set-semester-form',
                                                        'Set Semester?',
                                                        'question',
                                                        'Do you want to set Academic Year: <?php echo htmlspecialchars($academic_year . ', Semester: ' . $semester_data['semester']); ?> as acive semester?',
                                                        'Set Semester',
                                                        '#2eca6a',
                                                    )"
                                                    >
                                                        Set Semester
                                                    </button>
                                                </form>
                                            <?php } else { ?>      
                                                <span class="text-success fw-bold"> Active Semester </span>
                                            <?php } } ?>
                                        </td>

                                        
    
                                    </tr>
                                </tbody>

                                </table>
                            </div>

                                
                                
                            </div>

                        </div>
                        <?php
                    }
                ?>
                
                <!-- End of List -->    

            </div>

        </div>

    </section>
    <!-- End Table -->

</main>
<!-- End #main -->