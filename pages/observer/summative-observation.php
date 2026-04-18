<?php
require_once "../../config/conn.config.php";

$student_behaviors = [
    'The students were either actively <u>engaged in the discussion or on-task</u>.',
    'The students are asking <u>questions or sharing their ideas either</u> in class, with a group, or in pairs.',
    'The students made their thought processes explicit by <u>explaining ideas in their own words and generating ideas</u>.',
    'The students were <u>rethinking or reconsidering</u> their answers, ideas or learning behavior following the teacher\'s timely and meaningful feedback or follow up.',
    'The students <u>related or applied what they learned</u> to practical real-life scenarios.',
    'The students <u>demonstrate progress towards achievement of lesson objectives</u> through the activities facilitated by the teacher.'
];


if (empty($_SESSION["observe-id"]) || empty($_SESSION["observer-id"])) {
    unset($_SESSION["observe-id"], $_SESSION["observer-id"]);
    header("Location: login.php");
    exit();
} 

else {
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

    <title> COPUS: Summative Observation </title>

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
            width: 10px;
        }

        th.engagement-header {
            width: 180px;
            height: 80;
        }

        th.engagement-header,
        th.engagement-info {
            background-color: #afe9adff;
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

    <form action="../../process/observer/observation-management.php" method="POST" id="summative-form">

        <input type="hidden" name="submit-summative-observation" value="1">
        <input type="hidden" name="observe-id" value="<?php echo htmlspecialchars(base64_encode($observation_id)); ?>">

        <div class="container-fluid">

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead>

                        <tr>
                            <th class="bg-success text-white fs-6" colspan="8">
                                Supervisor Rating Form
                            </th>

                            <th class="bg-success text-white">
                                Semester: <?php echo htmlspecialchars($semester); ?>
                            </th>

                            <th class="bg-success text-white">
                                School Year: <?php echo htmlspecialchars($academic_year); ?>
                            </th>
                        </tr>

                    </thead>

                    <tbody class="fw-bold">

                        <tr>
                            <td class="bg-success text-white" colspan="2">
                                Name of Teacher Observed (Last Name, First Name):
                            </td>

                            <td colspan="3">
                                <?php echo htmlspecialchars($observation_details->teacher_name); ?>
                            </td>

                            <td class="bg-success text-white" colspan="2">
                                Subject:
                            </td>

                            <td colspan="4">
                                <?php echo htmlspecialchars($observation_details->subject); ?>
                            </td>

                        </tr>

                        <tr>

                            <td class="bg-success text-white" colspan="2">
                                Name of Observer (Last Name, First Name):
                            </td>

                            <td colspan="3">
                                <?php echo htmlspecialchars($observation_details->observer_name); ?>
                            </td>

                            <td class="bg-success text-white" colspan="2">
                                Modality:
                            </td>

                            <td colspan="4">
                                <?php echo htmlspecialchars($observation_details->modality); ?>
                            </td>

                        </tr>

                        <tr>
                            <td colspan="2" class="bg-success text-white">
                                Department:
                            </td>

                            <td colspan="8">
                                <?php echo htmlspecialchars($observation_details->department_name . " (" . $observation_details->department_code . ")"); ?>
                            </td>
                        </tr>

                        <tr>

                            <td class="bg-success text-white" colspan="2">
                                Date of Observation:
                            </td>

                            <td>
                                <span id="dateToday"></span>
                            </td>

                            <td class="bg-success text-white">
                                Type of COPUS:
                            </td>

                            <td colspan="3">
                                <?php echo htmlspecialchars($observation_details->copus_type); ?>
                            </td>

                            <td class="bg-success text-white" colspan="2">
                                Year/Grade Level Observed:
                            </td>

                            <td>
                                <?php echo htmlspecialchars($observation_details->year_level); ?>
                            </td>

                        </tr>

                        <tr>
                            <td class="bg-success text-white text-start" colspan="10">
                                Instructions:
                            </td>
                        </tr>

                        <tr>
                            <td colspan="10" class="text-start fw-normal">
                                Below are learning behaviors that show evidence of an effective and meaningful Active Learning class. Please rate the level of student engagement that you
                                have observed. Tick the box corresponding to the level of engagement observed.
                            </td>
                        </tr>

                        <tr>
                            <td class="bg-success text-white text-start" colspan="10">
                                Reminders for the observer:
                            </td>
                        </tr>

                        <tr>
                            <td colspan="10" class="text-start fw-normal">
                                <p>
                                    1. The expected learning behaviors are result of a teachers' effective facilitation of an Active Learning class.
                                    An option for <strong>Not Observed</strong> is included for cases when the expected behaviors are not seen in class as a
                                    result of the teacher not employing Active Learning strategies or missing opportunities to make the class or student/s part
                                    of the learning process. A guide for the Observers is provided, which includes a column of 'What do observers need to look for
                                    when observing?'.
                                </p>

                                <p>
                                    2. In rating the level of engagement, the point of reference <span class="fw-bold text-decoration-underline">need not always be all</span> the students.
                                    For example, in item #4, feedback or follow up may only be given to a student or group of students. High level of engagement
                                    will be based on how many of the students who received feedback showed behavior towards rethinking or reconsidering their answers.
                                </p>

                                <p>
                                    3. In estimating the percentage, just count waht is observable for the duration of the observation. Thus, there might be times when
                                    observers might have to walk around the classroom to have a good estimate of the level of engagement.
                                </p>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

            <!-- COPUS Form -->

            <!-- End COPUS Form -->
        </div>

        <div class="container-fluid mt-2">

            <div class="table-responsive">

                <table class="table table-bordered table-lg" id="observationTable">

                    <thead class="table-light border border-dark">
                        <tr>
                            <th rowspan="2" colspan="2" class="header bg-success text-white w-25">
                                Learner Behaviors Observed in Class
                            </th>

                            <th class="engagement-header">
                                High-Level Engagement <br> (4)
                            </th>

                            <th class="engagement-header">
                                Medium-Level Engagement <br> (3)
                            </th>

                            <th class="engagement-header">
                                Low-Level Engagement <br> (2)
                            </th>

                            <th class="engagement-header">
                                Not Observed <br> (1)
                            </th>

                            <th rowspan="2" class="bg-success text-white">
                                Comments
                            </th>
                        </tr>

                        <tr>

                            <th class="engagement-info">
                                Large fraction of students (<span class="text-danger">75+%</span>)
                                clearly engaged in the class activity or demonstrated
                                exepected behavior.
                            </th>

                            <th class="engagement-info">
                                Substantial fractions both clearly engaged/showing
                                behavior and clearly not engaged/not showing behavior.
                            </th>

                            <th class="engagement-info">
                                Small fraction (<span class="text-danger">10-25%</span>) ovbiously
                                engaged or demonstrating expected behavior.
                            </th>

                            <th class="engagement-info">
                                There were missed opportunities that the students
                                could've been engaged more but were not given chances
                                or motivation to do so.
                            </th>

                        </tr>

                    </thead>

                    <tbody class="gridBody border border-dark">
                        <?php foreach ($student_behaviors as $i => $description): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><?php echo $description; ?></td>

                                <!-- High -->
                                <td class="header">
                                    <input
                                        type="radio"
                                        name="rating[<?php echo $i; ?>]" value="high"
                                        data-type="student"
                                        data-row="<?php echo $i; ?>">
                                </td>

                                <!-- Medium -->
                                <td class="header">
                                    <input
                                        type="radio"
                                        name="rating[<?php echo $i; ?>]" value="medium"
                                        data-type="student"
                                        data-row="<?php echo $i; ?>">
                                </td>

                                <!-- Low -->
                                <td class="header">
                                    <input
                                        type="radio"
                                        name="rating[<?php echo $i; ?>]" value="low"
                                        data-type="student"
                                        data-row="<?php echo $i; ?>">
                                </td>

                                <!-- Not Observed -->
                                <td class="header">
                                    <input
                                        type="radio"
                                        name="rating[<?php echo $i; ?>]" value="no"
                                        data-type="student"
                                        data-row="<?php echo $i; ?>">
                                </td>

                                <!-- Remarks -->
                                <td>
                                    <textarea class="border border-dark" rows="2" name="comments[]" data-row="<?php echo $i; ?>"></textarea>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                        <tr>
                            <td class="bg-warning" colspan="2">
                                Number of items that met the target student engagement
                            </td>

                            <td class="bg-warning">
                                <span id="highCount"></span>
                            </td>

                            <td>
                                <span id="mediumCount"></span>
                            </td>

                            <td>
                                <span id="lowCount"></span>
                            </td>

                            <td>
                                <span id="noCount"></span>
                            </td>

                            <td colspan="2"></td>
                        </tr>

                        <tr>
                            <td class="bg-secondary" colspan="2"></td>

                            <td class="bg-warning text-start fw-bold" colspan="3">
                                Final Rating
                            </td>

                            <td class="bg-warning fw-bold">
                                <span id="finalRating">
                                    0%
                                </span>
                            </td>

                            <td colspan="2">
                                <button type="submit" class="btn btn-primary"> Submit Observation </button>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="10" class="text-start">
                                <span>
                                    <strong>Rating Guide:</strong><br>
                                    Our goal is to engage at least 75% of students Almost Always (67% ot the time). To measure this,
                                    we calculate the percentage of items in which the teacher achieved a high level of engagement. The
                                    minimum target is to receive a rating of 4 in at least 4 out of six items. which equivalents to 66.67%
                                </span>
                            </td>
                        </tr>

                        <tr class="border-0">
                            <td colspan="3" class="text-start">
                                1 Item - 16.67%
                            </td>

                            <td colspan="7" class="text-start">
                                4 Items - 66.67%
                            </td>
                        </tr>

                        <tr class="border-0">
                            <td colspan="3" class="text-start">
                                2 Items - 33.33%
                            </td>

                            <td colspan="7" class="text-start">
                                5 Items - 83.33%
                            </td>
                        </tr>

                        <tr class="border-0">
                            <td colspan="3" class="text-start">
                                3 Item - 50.00%
                            </td>

                            <td colspan="7" class="text-start">
                                6 Items - 100%
                            </td>
                        </tr>

                    </tbody>

                </table>

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

    <!-- Date and Time -->
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

    <!-- Counting of Checklist -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Get all radio buttons
            const radios = document.querySelectorAll('input[type="radio"][data-type="student"]');

            function updateCounts() {
                let highPercent = 0;
                let high = 0,
                    medium = 0,
                    low = 0,
                    no = 0;

                // Loop through each radio group
                const rows = [...new Set(Array.from(radios).map(r => r.name))];
                rows.forEach(name => {
                    const checked = document.querySelector(`input[name="${name}"]:checked`);
                    if (checked) {
                        switch (checked.value) {
                            case "high":
                                high++;
                                break;
                            case "medium":
                                medium++;
                                break;
                            case "low":
                                low++;
                                break;
                            case "no":
                                no++;
                                break;
                        }
                    }
                });

                // Update counters
                document.getElementById("highCount").textContent = high;
                document.getElementById("mediumCount").textContent = medium;
                document.getElementById("lowCount").textContent = low;
                document.getElementById("noCount").textContent = no;


                if ((high + medium + low + no) > 0) {
                    highPercent = (high / (high + medium + low + no)) * 100;
                }

                // Display percentage
                const finalRating = document.getElementById("finalRating");
                if (finalRating) {
                    finalRating.textContent = highPercent.toFixed(2) + "%";
                }
            }

            // Run count whenever a radio is clicked
            radios.forEach(radio => {
                radio.addEventListener("change", updateCounts);
            });

            // Initialize on load
            updateCounts();
        });
    </script>

    <!-- Submitting Screenshot -->
    <script>
        document.getElementById('summative-form').addEventListener("submit", async function(e) {

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