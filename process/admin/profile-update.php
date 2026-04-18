<?php
    require_once "../../config/conn.config.php";
    require_once "../../config/validations.config.php";
    require_once "../../config/dropdowns.config.php";

    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update-profile"])) {

        $id_number = htmlspecialchars(trim($_SESSION["admin-id"]));
        $first_name = htmlspecialchars($_POST["first-name"]);
        $middle_name = htmlspecialchars($_POST["middle-name"]);
        $last_name = htmlspecialchars($_POST["last-name"]);
        $date_of_birth = htmlspecialchars($_POST["date-of-birth"]);
        $gender = htmlspecialchars($_POST["gender"]);
        $status = htmlspecialchars($_POST["marital-status"]);
        $phone_number = htmlspecialchars(trim($_POST["phone-number"]));
        $telephone = htmlspecialchars(trim($_POST["telephone"]));
        $temporary_address = htmlspecialchars($_POST["temporary-address"]);
        $permanent_address = htmlspecialchars($_POST["permanent-address"]);
        $facebook_link = htmlspecialchars(trim($_POST["facebook-link"]));

        if(empty($id_number) || empty($first_name) || empty($last_name) || 
           empty($date_of_birth) || empty($gender) || empty($status) || 
           empty($phone_number) || empty($permanent_address)) 
        { 
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];
            header("Location: ../../pages/admin/home.php?page=user-profile&update-profile=true");
            exit();
        }

        else if(!in_array($gender, $allowed_gender)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid gender! Please try again."
            ];
            header("Location: ../../pages/admin/home.php?page=user-profile&update-profile=true");
            exit();
        }

        else if(!in_array($status, $allowed_status)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid marital status! Please try again."
            ];
            header("Location: ../../pages/admin/home.php?page=user-profile&update-profile=true");
            exit();
        }

        else if(!validate_date($date_of_birth)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "Invalid date! Please try again."
            ];
            header("Location: ../../pages/admin/home.php?page=user-profile&update-profile=true");
            exit();
        }

        else {
            try {
                $update_credentials = $conn->prepare("UPDATE admin_credentials_tbl
                                                    SET first_name = :first_name,
                                                    middle_name = :middle_name,
                                                    last_name = :last_name
                                                    WHERE id_number = :id_number
                                                    ");

                $update_credentials->execute([
                    ":first_name" => $first_name,
                    ":middle_name" => $middle_name,
                    ":last_name" => $last_name,
                    ":id_number" => $id_number
                ]);

                $update_info = $conn->prepare("UPDATE admin_info_tbl
                                            SET date_of_birth = :dob,
                                            phone_number = :phone_number,
                                            telephone_number = :telephone_number,
                                            temporary_address = :temporary_address,
                                            permanent_address = :permanent_address,
                                            gender = :gender,
                                            marital_status = :marital_status,
                                            facebook_link = :facebook_link
                                            WHERE id_number = :id_number
                                            ");
                $update_info->execute([
                    ":dob" => $date_of_birth,
                    ":phone_number" => $phone_number,
                    ":telephone_number" => $telephone_number,
                    ":temporary_address" => $temporary_address,
                    ":permanent_address" => $permanent_address,
                    ":gender" => $gender,
                    ":marital_status" => $status,
                    ":facebook_link" => $facebook_link,
                    ":id_number" => $id_number
                ]);

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "Profile updated successfully!"
                ];
                header("Location: ../../pages/admin/home.php?page=user-profile&update-profile=true");
                exit();
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
                header("Location: ../../pages/admin/home.php?page=user-profile&update-profile=true");
                exit();
            }
        }

    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update-profile-picture"])) {
        $file_location = "../../uploads/imgs/";

        $id_number = htmlspecialchars(trim($_SESSION["admin-id"]));
        $uploaded_photo = $_FILES["uploaded-photo"]["name"];
        $tmp_name = $_FILES["uploaded-photo"]["tmp_name"];

        $file_extension = strtolower(pathinfo($uploaded_photo, PATHINFO_EXTENSION));

        if(!in_array($file_extension, $allowed_img_format)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];
            header("Location: ../../pages/admin/home.php?page=user-profile&update-photo=true");
            exit();
        }

        else {
            try {

                $unique_filename = 'admin_' . $id_number . '_' . uniqid() . '.' . $file_extension;

                $update_profile_picture = $conn->prepare("UPDATE admin_info_tbl SET profile_picture = :profile_picture WHERE id_number = :id_number");
                $update_profile_picture->execute([
                    ":profile_picture" => $unique_filename,
                    ":id_number" => $id_number
                ]);

                $file_path = $file_location . $unique_filename;
                move_uploaded_file($tmp_name, $file_path);

                $_SESSION["query-status"] = [
                    "status" => "success",
                    "message" => "Profile picture updated successfully!"
                ];
                header("Location: ../../pages/admin/home.php?page=user-profile&update-photo=true");
                exit();
            }

            catch(PDOException $e) {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "An unknown error occured! Please try again."
                ];
                header("Location: ../../pages/admin/home.php?page=user-profile&update-photo=true");
                exit();
            }
        }
    }
    
    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update-password"])) {
        $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/";

        $id_number = htmlspecialchars(trim($_SESSION["admin-id"]));
        $current_password = htmlspecialchars(trim($_POST["current-password"]));
        $new_password = htmlspecialchars(trim($_POST["new-password"]));
        $confirm_password = htmlspecialchars(trim($_POST["confirm-new-password"]));

        if(empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];
            header("Location: ../../pages/admin/home.php?page=user-profile&update-password=true");
            exit();
        }

        else if(!preg_match($pattern, $new_password)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];
            header("Location: ../../pages/admin/home.php?page=user-profile&update-password=true");
            exit();
        }

        else {
            if($new_password === $confirm_password) {
                try {
                    $check_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
                    $check_account->execute([":id_number" => $id_number]);
                    
                    if($check_account->rowCount() === 1) {
                        $password_data = $check_account->fetch(PDO::FETCH_OBJ);

                        if(password_verify($current_password, $password_data->admin_password)) {
                            $set_new_password = $conn->prepare("UPDATE admin_credentials_tbl SET admin_password = :new_password, generated_password = :generated_password WHERE id_number = :id_number");
                            $set_new_password->execute([
                                ":new_password" => password_hash($new_password, PASSWORD_BCRYPT),
                                ":generated_password" => "No",
                                ":id_number" => $id_number
                            ]);

                            $change_update = $conn->prepare("UPDATE admin_info_tbl
                                                        SET updated_at = CURRENT_TIMESTAMP()
                                                        WHERE id_number = :id_number");
                            $change_update->execute([":id_number" => $id_number]);

                            $_SESSION["query-status"] = [
                                "status" => "success",
                                "message" => "Password updated successfully!"
                            ];
                            header("Location: ../../pages/admin/home.php?page=user-profile&update-password=true");
                            exit();

                        }

                        else {
                            $_SESSION["query-status"] = [
                                "status" => "danger",
                                "message" => "Invalid current password! Please try again."
                            ];
                            header("Location: ../../pages/admin/home.php?page=user-profile&update-password=true");
                            exit();
                        }
                    }

                    else {
                        $_SESSION["query-status"] = [
                            "status" => "danger",
                            "message" => "Account not found! Please try again."
                        ];
                        header("Location: ../../pages/admin/home.php?page=user-profile&update-password=true");
                        exit();
                    }
                }

                catch(PDOException $e) {
                    $_SESSION["query-status"] = [
                        "status" => "danger",
                        "message" => "An unknown error occured! Please try again."
                    ];
                    header("Location: ../../pages/admin/home.php?page=user-profile&update-password=true");
                    exit();
                }
            }

            else {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "Passwords don't match! Please try again."
                ];
                header("Location: ../../pages/admin/home.php?page=user-profile&update-password=true");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["toggle-two-factor"])) {

        $id_number = htmlspecialchars(trim($_SESSION["admin-id"]));
        $tfa_status = htmlspecialchars(trim($_POST["toggle-two-factor"]));

        if(!in_array($tfa_status, $two_factor_authentication)) {
            $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=user-profile&update-password=true");
            exit();
        }

        try {
            $check_account = $conn->prepare("SELECT * FROM admin_credentials_tbl WHERE id_number = :id_number LIMIT 1");
            $check_account->execute([":id_number" => $id_number]);

            if($check_account->rowCount() === 1) {
                $update_two_factor_authentication = $conn->prepare("UPDATE admin_credentials_tbl 
                                                                    SET two_factor_authentication = :two_factor_authentication
                                                                    WHERE id_number = :id_number
                                                                    ");

                $update_two_factor_authentication->execute([
                    ":two_factor_authentication" => $tfa_status,
                    ":id_number" => $id_number
                ]);

                $_SESSION["query-status"] = [
                    "status" => $tfa_status === "Enabled" ? "success" : "warning",
                    "message" => "Two-factor authentication has been " . strtoupper($tfa_status) . "!"
                ];

                header("Location: ../../pages/admin/home.php?page=user-profile&update-password=true");
                exit();
            }

            else {
                $_SESSION["query-status"] = [
                    "status" => "danger",
                    "message" => "Account not found! Please try again."
                ];

                header("Location: ../../pages/admin/home.php?page=user-profile&update-password=true");
                exit();
            }
        }

        catch(PDOException $e) {
$           $_SESSION["query-status"] = [
                "status" => "danger",
                "message" => "An unknown error occured! Please try again."
            ];

            header("Location: ../../pages/admin/home.php?page=user-profile&update-password=true");
            exit();
        }

        
    }
?>