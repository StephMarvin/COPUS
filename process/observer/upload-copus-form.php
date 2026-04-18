<?php
    @ini_set('upload_max_filesize', '1000M');
    @ini_set('post_max_size', '1000M');
    @ini_set('memory_limit', '256M');
    @ini_set('max_input_time', '300');
    @ini_set('max_execution_time', '300');

    require_once "../../config/conn.config.php";

    $upload_directory = "../../uploads/copus/";

    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK && isset($_SESSION["observer-id"])) {
        $file_tmp_path = $_FILES["pdf_file"]["tmp_name"];
        $observation_id = htmlspecialchars(trim($_POST["observation_id"]));
        $file_name = htmlspecialchars($_POST["file_name"]);

        $file_path = $upload_directory . $file_name;

        if(move_uploaded_file($file_tmp_path, $file_path)) {
            $upload_copus_form = $conn->prepare("INSERT INTO copus_forms_tbl(observation_id, file_name, file_path)
                                                VALUES(:observation_id, :file_name, :file_path)");

            $upload_copus_form->execute([
                ":observation_id" => $observation_id,
                ":file_name" => $file_name,
                ":file_path" => $file_path
            ]);

            echo "Success";
        }

        else {
            echo "Failed";
        }
    }

    else {
        echo "Failed";
    }
?>