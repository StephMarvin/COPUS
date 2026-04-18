<?php
require_once "../../config/conn.config.php";

if (empty($_SESSION["observe-id"]) || empty($_SESSION["observer-id"])) {
    unset($_SESSION["observe-id"], $_SESSION["observer-id"]);
    header("Location: login.php");
    exit();
} else {
    try {
        $observation_id = htmlspecialchars(trim($_SESSION["observe-id"]));

        $get_observation_data = $conn->prepare("SELECT
                                                    ot.*,
                                                    CONCAT(ta.last_name, ', ', ta.first_name) AS 'teacher_name',
                                                    CONCAT(oc.last_name, ', ', oc.first_name) AS 'observer_name',
                                                    oi.profile_picture,
                                                    CONCAT(st.subject_code, ': ', st.subject_name) AS 'subject',
                                                    dt.department_code, dt.department_name
                                                FROM observations_tbl ot
                                                LEFT JOIN teacher_credentials_tbl ta
                                                ON ot.teacher_id = ta.id_number
                                                LEFT JOIN observers_credentials_tbl oc
                                                ON ot.observer_id = oc.id_number
                                                LEFT JOIN observers_info_tbl oi
                                                ON oc.id_number = oi.id_number
                                                LEFT JOIN subjects_tbl st
                                                ON ot.subject_id = st.subject_id
                                                LEFT JOIN departments_tbl dt
                                                ON ot.department_id = dt.department_id
                                                WHERE ot.observation_id = :observation_id AND ot.observe_status = :observe_status
                                                LIMIT 1");
        $get_observation_data->execute([
            ":observation_id" => $observation_id,
            ":observe_status" => "Incomplete"
        ]);

        if ($get_observation_data->rowCount() === 1) {
            $observation_details = $get_observation_data->fetch(PDO::FETCH_OBJ);

            $get_active_year_and_sem = $conn->prepare("SELECT 
                                                                    ay.academic_year, s.semester_id, s.semester 
                                                                FROM academic_years_tbl ay
                                                                LEFT JOIN semesters_tbl s
                                                                ON ay.academic_year_id = s.academic_year_id
                                                                WHERE ay.status = :year_status AND s.semester_status = :semester_status
                                                                LIMIT 1
                                                                ");

            $get_active_year_and_sem->execute([
                ":year_status" => "Active",
                ":semester_status" => "Active"
            ]);

            if ($get_active_year_and_sem->rowCount() === 1) {
                $academic_data = $get_active_year_and_sem->fetch(PDO::FETCH_OBJ);
                $academic_year = $academic_data->academic_year;
                $semester_id = $academic_data->semester_id;
                $semester = $academic_data->semester;
            }

            $get_teacher_actions = $conn->prepare("SELECT * FROM teacher_actions_tbl WHERE action_status = :action_status");
            $get_teacher_actions->execute([":action_status" => "Active"]);

            if ($get_teacher_actions->rowCount() > 0) {
                $teacher_actions = $get_teacher_actions->fetchAll();
                $teacher_action_codes = [];
                $teacher_display_action = [];

                foreach ($teacher_actions as $teacher_action) {
                    $teacher_action_codes[] = htmlspecialchars($teacher_action["action_code"]);
                    $teacher_display_action[] =
                        htmlspecialchars($teacher_action["action_name"]) .
                        "<span class='fw-bold'>(" .
                        htmlspecialchars($teacher_action["action_code"]) .
                        ")</span>";
                }
            }

            $get_student_actions = $conn->prepare("SELECT * FROM student_actions_tbl WHERE action_status = :action_status");
            $get_student_actions->execute([":action_status" => "Active"]);

            if ($get_student_actions->rowCount() > 0) {
                $student_actions = $get_student_actions->fetchAll();
                $student_action_codes = [];
                $student_display_action = [];

                foreach ($student_actions as $student_action) {
                    $student_action_codes[] = htmlspecialchars($student_action["action_code"]);
                    $student_display_action[] =
                        htmlspecialchars($student_action["action_name"]) .
                        "<span class='fw-bold'>(" .
                        htmlspecialchars($student_action["action_code"]) .
                        ")</span>";
                }
            }
        } else {
            unset($_SESSION["observe-id"], $_SESSION["observer-id"]);
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        unset($_SESSION["observe-id"], $_SESSION["observer-id"]);
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title> COPUS: Observe Class </title>

    <link rel="shortcut icon" type="image/x-icon" href="../../public/assets/website-logo-cite.png">

    <?php
    include_once "../global-includes/css-files.php";
    ?>

    <style>
        body {
            background-color: #f8f9fa;
        }

        table {
            font-size: 13px;
            text-align: center;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: center;
            vertical-align: middle;
        }

        th.header {
            width: 40px;
        }

        textarea {
            width: 100%;
            border: 1px solid #dee2e6;
            resize: none;
            padding: 4px;
            font-size: 12px;
            border-radius: 0.25rem;
        }

        thead th {
            background-color: #0d6efd;
            color: white;
            vertical-align: middle;
        }

        tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        tbody tr:hover {
            background-color: #e9f5ff;
        }

        .form-label {
            font-weight: 600;
        }

        select.form-select-sm {
            border-radius: 0.375rem;
        }

        #observationTable {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .table-responsive {
            border-radius: 10px;
            overflow-x: auto;
        }

        th[rowspan],
        td[rowspan] {
            background-color: #e2e6ea;
            font-weight: bold;
        }

        .table thead th.sticky-top {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        @media (max-width: 768px) {
            table {
                font-size: 10px;
            }

            textarea {
                font-size: 10px;
            }

            .form-label {
                font-size: 12px;
            }
        }
    </style>

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="../../public/main/assets/css/style.css" rel="stylesheet">
    <link href="../../public/main/assets/css/custom-styles.css" rel="stylesheet">
    <link href="../../public/main/assets/css/custom-table.css" rel="stylesheet">

</head>

<body>

    <form action="../../process/observer/observation-management.php" method="POST" id="copus-form">

        <input type="hidden" name="submit-observation" value="1">
        <input type="hidden" name="observe-id" value="<?php echo htmlspecialchars(base64_encode($observation_id)); ?>">

        <div class="container-fluid">

            <div class="table-responsive">
                <table class="table table-bordered table-hover">

                    <thead>

                        <tr>
                            <th class="bg-success text-white fs-5" colspan="6">
                                Classroom Observation Protocol for Undergraduate STEM (COPUS)
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        <tr>

                            <td class="bg-success text-white">
                                Teacher:
                            </td>

                            <td class="fw-bold">
                                <?php echo htmlspecialchars($observation_details->teacher_name); ?>
                            </td>

                            <td class="bg-success text-white">
                                Academic Year and Semester:
                            </td>

                            <td class="fw-bold">
                                <?php echo htmlspecialchars($academic_year); ?> | <?php echo htmlspecialchars($semester); ?>
                            </td>

                            <td class="bg-success text-white">
                                Date:
                            </td>

                            <td class="fw-bold">
                                <span id="dateToday"></span>
                            </td>

                        </tr>

                        <tr>

                            <td class="bg-success text-white">
                                Modality:
                            </td>

                            <td class="fw-bold">
                                <?php echo htmlspecialchars($observation_details->modality); ?>
                            </td>

                            <td class="bg-success text-white">
                                Year Level:
                            </td>

                            <td class="fw-bold">
                                <?php echo htmlspecialchars($observation_details->year_level); ?>
                            </td>

                            <td class="bg-success text-white">
                                Subject:
                            </td>

                            <td class="fw-bold">
                                <?php echo htmlspecialchars($observation_details->subject); ?>
                            </td>

                        </tr>

                        <tr>

                            <td class="bg-success text-white">
                                Observer:
                            </td>

                            <td class="fw-bold">
                                <?php echo htmlspecialchars($observation_details->observer_name); ?>
                            </td>

                            <td class="bg-success text-white">
                                COPUS Type:
                            </td>

                            <td class="fw-bold">
                                <?php echo htmlspecialchars($observation_details->copus_type); ?>
                            </td>

                            <td class="bg-success text-white">
                                Department:
                            </td>

                            <td class="fw-bold">
                                <?php echo htmlspecialchars($observation_details->department_name . " (" . $observation_details->department_code . ")"); ?>
                            </td>

                        </tr>

                        <tr>
                            <td class="bg-success" colspan="6"></td>
                        </tr>

                        <tr>
                            <td class="bg-success text-white" colspan="2">
                                Instructions:
                            </td>

                            <td class="fw-bold" colspan="4">
                                For each 2-minute interval, check columns to show what's happening in each category.
                                Check multiple columns where appropriate.
                            </td>
                        </tr>

                        <tr>
                            <td class="bg-primary text-white">
                                Student Actions:
                            </td>

                            <td colspan="5">
                                <?php
                                echo implode("; ", $student_display_action) . ".";
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="bg-success text-white">
                                Teacher Actions:
                            </td>

                            <td colspan="5">
                                <?php
                                echo implode("; ", $teacher_display_action) . ".";
                                ?>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

            <!-- COPUS Form -->
            <div class="container-fluid mt-2">

                <div class="table-responsive">

                    <table class="table table-bordered table-sm" id="observationTable">

                        <thead class="table-light">
                            <tr>
                                <th rowspan="2" class="header bg-secondary text-white"> Minute </th>
                                <th colspan="<?php echo htmlspecialchars(count($student_actions)); ?>" class="bg-primary text-white">
                                    Students Doing
                                </th>

                                <th colspan="<?php echo htmlspecialchars(count($teacher_actions)); ?>" class="bg-success text-white">
                                    Instructor Doing
                                </th>

                                <th colspan="3" class="bg-info text-white">
                                    Level of Engagement
                                </th>

                                <th rowspan="2" class="bg-secondary text-white">
                                    Comments
                                </th>
                            </tr>

                            <tr>
                                <?php foreach ($student_actions as $student_action): ?>
                                    <th class="header bg-primary text-white" title="<?php echo htmlspecialchars($student_action["action_name"]); ?>">
                                        <?php echo htmlspecialchars($student_action["action_code"]); ?>
                                    </th>
                                <?php endforeach; ?>

                                <?php foreach ($teacher_actions as $teacher_action): ?>
                                    <th class="header bg-success text-white" title="<?php echo htmlspecialchars($teacher_action["action_name"]); ?>">
                                        <?php echo htmlspecialchars($teacher_action["action_code"]); ?>
                                    </th>
                                <?php endforeach; ?>

                                <th class="header bg-info text-white">
                                    High
                                </th>

                                <th class="header bg-warning text-white">
                                    Medium
                                </th>

                                <th class="header bg-danger text-white">
                                    Low
                                </th>

                            </tr>

                        </thead>

                        <tbody id="gridBody">
                            <?php
                            $total_minutes = 90;
                            $total_rows = 45;

                            for ($i = 0; $i < $total_rows; $i++) {
                                $minute_start = $i * 2;
                                $minute_end = $minute_start + 2;
                            ?>
                                <tr>
                                    <td> <?php echo htmlspecialchars($minute_start . " - " . $minute_end); ?> </td>

                                    <?php foreach ($student_action_codes as $student_action_code): ?>
                                        <td>
                                            <input
                                                type="checkbox"
                                                name="<?php echo htmlspecialchars("student-" . $student_action_code . "[]"); ?>"
                                                value="1"
                                                data-type="student"
                                                data-action="<?php echo htmlspecialchars($student_action_code); ?>"
                                                data-row="<?php echo $i; ?>">
                                        </td>
                                    <?php endforeach; ?>

                                    <?php foreach ($teacher_action_codes as $teacher_action_code): ?>
                                        <td>
                                            <input
                                                type="checkbox"
                                                name="<?php echo htmlspecialchars("teacher-" . $teacher_action_code . "[]"); ?>"
                                                value="1"
                                                data-type="teacher"
                                                data-action="<?php echo htmlspecialchars($teacher_action_code); ?>"
                                                data-row="<?php echo $i; ?>">
                                        </td>
                                    <?php endforeach; ?>

                                    <td>
                                        <input
                                            type="radio"
                                            name="engagement_<?php echo $i; ?>"
                                            value="high"
                                            data-type="teacher"
                                            data-action="high"
                                            data-row="<?php echo $i; ?>">
                                    </td>

                                    <td>
                                        <input
                                            type="radio"
                                            name="engagement_<?php echo $i; ?>"
                                            value="medium"
                                            data-type="teacher"
                                            data-action="medium"
                                            data-row="<?php echo $i; ?>">
                                    </td>

                                    <td>
                                        <input
                                            type="radio"
                                            name="engagement_<?php echo $i; ?>"
                                            value="low"
                                            data-type="teacher"
                                            data-action="low"
                                            data-row="<?php echo $i; ?>">
                                    </td>

                                    <td>
                                        <textarea rows="2" name="comments[]" data-row="<?php echo $i; ?>"></textarea>
                                    </td>
                                </tr>

                            <?php
                            }
                            ?>
                        </tbody>

                    </table>

                    <input type="submit" class="btn btn-primary" value="Submit Observation">

                </div>

            </div>

        </div>

    </form>

    <!-- CDN JS Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- Vendor JS Files -->
    <script src="../../public/main/assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="../../public/main/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/main/assets/vendor/chart.js/chart.umd.js"></script>
    <script src="../../public/main/assets/vendor/echarts/echarts.min.js"></script>
    <script src="../../public/main/assets/vendor/quill/quill.js"></script>
    <script src="../../public/main/assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../../public/main/assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="../../public/main/assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="../../public/main/assets/js/main.js"></script>
    <!-- <script src="../../public/main/assets/js/security.js"></script> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        function getCurrentDate() {
            const days = [
                "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
            ]
            const months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

            const currentDate = new Date();
            const month = currentDate.getMonth();
            const day = currentDate.getDay();
            const date = currentDate.getDate().toString().padStart(2, '0');
            const year = currentDate.getFullYear();

            const dateToday = `${days[day]} | ${months[month]} ${date}, ${year}`;
            document.getElementById('dateToday').textContent = dateToday;
        }

        getCurrentDate();
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][data-action]');

            // Load checkbox state from localStorage
            checkboxes.forEach(checkbox => {
                const key = getStorageKey(checkbox);
                checkbox.checked = localStorage.getItem(key) === "1";

                // Save state on change
                checkbox.addEventListener("change", () => {
                    localStorage.setItem(key, checkbox.checked ? "1" : "0");
                });
            });

            function getStorageKey(checkbox) {
                const type = checkbox.dataset.type;
                const action = checkbox.dataset.action;
                const row = checkbox.dataset.row;
                return `observation-${type}-${action}-row${row}`;
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const comments = document.querySelectorAll('textarea[name="comments[]"]');

            // Load saved comments
            comments.forEach(textarea => {
                const row = textarea.dataset.row;
                const key = `comment-row-${row}`;
                textarea.value = localStorage.getItem(key) || "";

                // Save on change
                textarea.addEventListener("input", () => {
                    localStorage.setItem(key, textarea.value);
                });
            });
        });
    </script>

    <script>
        document.getElementById('copus-form').addEventListener("submit", async function(e) {

            e.preventDefault();
            localStorage.clear();

            // Scroll to top before capture (prevents rendering issues)
            window.scrollTo(0, 0);

            const elementToCapture = document.body;

            // Wait for any lazy-loaded content
            await new Promise(resolve => setTimeout(resolve, 500));

            const canvas = await html2canvas(elementToCapture, {
                scale: 1, // Higher scale = sharper image
                useCORS: true,
                allowTaint: true,
                scrollX: 0,
                scrollY: 0,
                x: 0,
                y: 0,
                width: document.documentElement.scrollWidth,
                height: document.documentElement.scrollHeight,
                windowWidth: document.documentElement.scrollWidth,
                windowHeight: document.documentElement.scrollHeight
            });

            const imgData = canvas.toDataURL("image/png", 1.0);
            const {
                jsPDF
            } = window.jspdf;
            const pdf = new jsPDF("p", "mm", "a4");

            const imgWidth = 210;
            const pageHeight = 297;
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;
            let position = 0;

            pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft > 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            const fileName = '<?php echo htmlspecialchars($observation_details->teacher_name . "_" . $observation_details->copus_type . "_A_Y_" . $academic_year . "_" . $semester . "_" . $observation_details->subject . "_COPUS_Form.pdf"); ?>';
            const sanitizedFileName = fileName.replace(/[<>:"/\\|?*\s-]+/g, '_');

            // Convert to Blob for upload
            const pdfBlob = pdf.output('blob');
            const formData = new FormData();
            formData.append('pdf_file', pdfBlob, sanitizedFileName);
            formData.append('observation_id', '<?php echo htmlspecialchars($observation_id); ?>');
            formData.append(
                'file_name',
                sanitizedFileName
            );

            try {
                const response = await fetch('../../process/observer/upload-copus-form.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.text();

                if (result === "Success") {
                    this.submit();
                } else {
                    console.error("Upload error:", error);
                    alert("❌ Upload failed. Check console for details.");
                }
            } catch (error) {
                console.error("Upload error:", error);
                alert("❌ Upload failed. Check console for details.");
            }
        });
    </script>

</body>

</html>