<?php
    $allowed_roles = ["Super Admin"];
    $allowed_gender = ["Male", "Female", "Others"];
    $allowed_status = ["Single", "Married", "Divorced", "Widowed"];
    $allowed_designations = ["Dean", "Asst. Dean","Program Head", "Faculty", "Active Learning Coach (ALC)"];
    $allowed_img_format = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff', 'ico', 'jfif'];
    $allowed_file_types = ['xls', 'xlsx', 'pdf', 'doc', 'docx'];

    $allowed_employment_status = ["Contractual", "Full-Time", "Part-Time"];
    $allowed_rank = ["Instructor", "Assistant Professor", "Associate Professor", "Professor", "Exemplary Teacher", "Master Teacher", "N/A"];
    
    $two_factor_authentication = ["Enabled", "Disabled"];

    $allowed_modality = ["FLEX (Face-to-Face)", "RAD (Online Class)"];
    $allowed_semesters = ["1st Semester", "2nd Semester"];
    $allowed_year_levels = ["First Year", "Second Year", "Third Year", "Fourth Year", "Fifth Year"];
    $allowed_copus_types = ["COPUS 1", "COPUS 2", "COPUS 3", "Summative"];

    $colors_pool = [
        "#8b0000", "#006400", "#b8860b", "#00008b", "#a0522d",
        "#4b0082", "#008b8b", "#8b008b", "#556b2f", "#a52a2a",
        "#2f4f4f", "#5d478b", "#483d8b", "#696969", "#800000",
        "#228b22", "#6b8e23", "#8b4513", "#191970", "#2c2c2c",
    ];

    $year_pattern = "/^(20\d{2})-(20\d{2})$/";

?>