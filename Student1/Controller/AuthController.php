<?php
session_start();
include "../Model/DatabaseConnection.php";

$db = new DatabaseConnection();
$connection = $db->openConnection();

if (isset($_POST["register"])) {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = $_POST["role"] ?? "";
    $file = $_FILES["fileupload"] ?? null;

    $errors = [];
    $_SESSION["reg_old"] = [
        "name" => $name,
        "email" => $email,
        "role" => $role
    ];

    if (!$name) $errors["name"] = "Name is required.";
    if (!$email) $errors["email"] = "Email is required.";
    if (strlen($password) < 8) $errors["password"] = "Password must be at least 8 characters.";
    if ($role != "employer" && $role != "seeker") $errors["role"] = "Role selection is required.";

    $result = $db->GetUserByEmailWithPrepareStmt($connection, "users", $email);
    if ($result->num_rows > 0) {
        $errors["email"] = "Email already exists.";
    }

    $file_path = "";
    if ($file && $file["error"] == 0) {
        $allowed_mimes = [];

        if  ($role == "employer") {
            $allowed_mimes = ["image/jpeg", "image/png"];
        } else if ($role == "seeker") {
            $allowed_mimes = ["application/pdf"];
        }

        $file_mime = mime_content_type($file["tmp_name"]);

        if ($file["size"] > 2097152) {
            $errors["file"] = "File size must be 2MB or less.";
        } else if (!in_array($file_mime, $allowed_mimes)) {
            if ($role == "employer") {
                $errors["file"] = "Only JPG or PNG is allowed for employer.";
            } else {
                $errors["file"] = "Only PDF is allowed for seeker.";
            }
        } else {
            $uploadDirectory = "../uploads/";
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }

            $file_name = time() . "_" . basename($file["name"]);
            $target_path = $uploadDirectory . $file_name;

            if (move_uploaded_file($file["tmp_name"], $target_path)) {
                $file_path = "../uploads/" . $file_name;
            } else {
                $errors["file"] = "Failed to upload file.";
            }
        }
    }

    if (count($errors) > 0) {
        $_SESSION["reg_errors"] = $errors;
        header("Location: ../View/register.php");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $save_result = $db->CreateUserWithPrepareStmt($connection, "users", $name, $email, $hashed_password, $role, $file_path);

    if ($save_result) {
        unset($_SESSION["reg_old"]);
        header("Location: ../View/login.php?success=Registered successfully");
        exit();
    } else {
        $_SESSION["reg_errors"]["db"] = "Registration failed.";
        header("Location: ../View/register.php");
        exit();
    }
}

if (isset($_POST["login"])) {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $_SESSION["login_old_email"] = $email;

    if (!$email || !$password) {
        $_SESSION["loginErr"] = "Email and password are required.";
        header("Location: ../View/login.php");
        exit();
    }

    $result = $db->GetUserByEmailWithPrepareStmt($connection, "users", $email);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password_hash"]) || $password == $user["password_hash"]) {
            $_SESSION["isLoggedIn"] = true;
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["file_path"] = $user["file_path"];
            unset($_SESSION["login_old_email"]);

            if ($user["role"] == "employer") {
                header("Location: ../View/employer_dashboard.php");
            } else {
                header("Location: ../View/job_board.php");
            }
            exit();
        }
    }

    $_SESSION["loginErr"] = "Invalid email or password.";
    header("Location: ../View/login.php");
    exit();
}

if (isset($_POST["save_profile"])) {
    if (!isset($_SESSION["isLoggedIn"]) || !$_SESSION["isLoggedIn"]) {
        header("Location: ../View/login.php");
        exit();
    }

    $user_id = $_SESSION["user_id"];
    $role = $_SESSION["role"];
    $file = $_FILES["fileupload"] ?? null;
    $current_password = $_POST["current_password"] ?? "";
    $new_password = $_POST["new_password"] ?? "";

    $errors = [];

    if ($role == "employer") {
        $company_name = trim($_POST["company_name"] ?? "");
        $industry = trim($_POST["industry"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $website = trim($_POST["website"] ?? "");

        $_SESSION["profile_old"] = [
            "company_name" => $company_name,
            "industry" => $industry,
            "description" => $description,
            "website" => $website
        ];

        if (!$company_name) $errors["company_name"] = "Company name is required.";
    } else {
        $headline = trim($_POST["headline"] ?? "");
        $skills = trim($_POST["skills"] ?? "");
        $years_experience = trim($_POST["years_experience"] ?? "");

        $_SESSION["profile_old"] = [
            "headline" => $headline,
            "skills" => $skills,
            "years_experience" => $years_experience
        ];

        if (!$headline) $errors["headline"] = "Headline is required.";
        if (!$skills) $errors["skills"] = "Skills are required.";
        if ($years_experience !== "" && !is_numeric($years_experience)) {
            $errors["years_experience"] = "Years of experience must be a number.";
        }
    }

    $user_result = $db->GetUserByIdWithPrepareStmt($connection, "users", $user_id);
    $user = $user_result->fetch_assoc();

    if ($new_password != "") {
        if ($current_password == "") {
            $errors["password"] = "Current password is required to set a new password.";
        } else if (!(password_verify($current_password, $user["password_hash"]) || $current_password == $user["password_hash"])) {
            $errors["password"] = "Current password does not match.";
        } else if (strlen($new_password) < 8) {
            $errors["password"] = "New password must be at least 8 characters.";
        }
    }

    $new_file_path = "";
    if ($file && $file["error"] == 0) {
        $allowed_mimes = [];

        if ($role == "employer") {
            $allowed_mimes = ["image/jpeg", "image/png"];
        } else if ($role == "seeker") {
            $allowed_mimes = ["application/pdf"];
        }

        $file_mime = mime_content_type($file["tmp_name"]);

        if ($file["size"] > 2097152) {
            $errors["file"] = "File size must be 2MB or less.";
        } else if (!in_array($file_mime, $allowed_mimes)) {
            if ($role == "employer") {
                $errors["file"] = "Only JPG or PNG is allowed for employer.";
            } else {
                $errors["file"] = "Only PDF is allowed for seeker.";
            }
        } else {
            $uploadDirectory = "../uploads/";
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }

            $file_name = time() . "_" . basename($file["name"]);
            $target_path = $uploadDirectory . $file_name;

            if (move_uploaded_file($file["tmp_name"], $target_path)) {
                $new_file_path = "../uploads/" . $file_name;
            } else {
                $errors["file"] = "Failed to upload file.";
            }
        }
    }

    if (count($errors) > 0) {
        $_SESSION["profile_errors"] = $errors;
        header("Location: ../View/profile.php");
        exit();
    }

    if ($role == "employer") {
        $profile_result = $db->GetProfileByUserIdWithPrepareStmt($connection, "employer_profiles", $user_id);

        if ($profile_result->num_rows > 0) {
            $db->UpdateEmployerProfileWithPrepareStmt($connection, "employer_profiles", $user_id, $company_name, $industry, $description, $website);
        } else {
            $db->CreateEmployerProfileWithPrepareStmt($connection, "employer_profiles", $user_id, $company_name, $industry, $description, $website);
        }
    } else {
        $years_value = 0;
        if ($years_experience !== "") {
            $years_value = (int)$years_experience;
        }

        $profile_result = $db->GetProfileByUserIdWithPrepareStmt($connection, "seeker_profiles", $user_id);

        if ($profile_result->num_rows > 0) {
            $db->UpdateSeekerProfileWithPrepareStmt($connection, "seeker_profiles", $user_id, $headline, $skills, $years_value);
        } else {
            $db->CreateSeekerProfileWithPrepareStmt($connection, "seeker_profiles", $user_id, $headline, $skills, $years_value);
        }
    }

    if ($new_password != "") {
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $db->UpdateUserPasswordWithPrepareStmt($connection, "users", $user_id, $new_hash);
    }

    if ($new_file_path != "") {
        $db->UpdateUserFileWithPrepareStmt($connection, "users", $user_id, $new_file_path);
        $_SESSION["file_path"] = $new_file_path;
    }

    unset($_SESSION["profile_old"]);
    $_SESSION["profile_message"] = "Profile saved successfully.";

    if ($role == "employer") {
        header("Location: ../View/employer_dashboard.php");
    } else {
        header("Location: ../View/job_board.php");
    }
    exit();
}
?>
