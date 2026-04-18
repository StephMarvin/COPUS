<?php
    if($role === "Super Admin" || $role === "Administrator") {
        $academic_settings_href = 'href=home.php?page=academic-settings';
        $academic_year_href = 'href=home.php?page=academic-year';
    }

    else {
        $academic_settings_href = "";
        $academic_year_href = "";
    }
?>

<div class="col-12 p-3">

    <a <?php echo htmlspecialchars($academic_settings_href); ?>>
        <div class="card shadow mb-3">

            <div class="d-flex justify-content-between align-items-center px-4 py-2 custom-bg mb-2">
                <h5 class="card-title custom-card-title text-white"> Current Academic Year and Semester </h5>
            </div>

            <div class="row px-4 py-3">

                <div class="col-xxl-6 col-md-6">
                    <a <?php echo htmlspecialchars($academic_year_href); ?>>
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
                    <a <?php echo htmlspecialchars($academic_settings_href); ?>>
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
    </a>

</div>