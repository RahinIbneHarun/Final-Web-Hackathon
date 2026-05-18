<?php
session_start();
include '../../db.php'; 
require_once '../../Model/JobModel.php';
/** @var mysqli $conn */

$jobModel = new JobModel();

if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);
    $experience = mysqli_real_escape_string($conn, $_POST['experience']);
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : intval($_POST['id']);

    $resume_name = "";
    if (!empty($_FILES['resume_file']['name'])) {
        $resume_name = time() . '_' . basename($_FILES["resume_file"]["name"]);
        $target_dir = "../../View/asset/";
        $target_file = $target_dir . $resume_name;
        move_uploaded_file($_FILES["resume_file"]["tmp_name"], $target_file);
    }

    if ($jobModel->updateUserProfile($user_id, $name, $email, $skills, $experience)) {
        if($resume_name != "") {
            $stmt = $jobModel->conn->prepare("UPDATE users SET resume=? WHERE id=?");
            $stmt->bind_param('si', $resume_name, $user_id);
            $stmt->execute();
        }
        $_SESSION['user_name'] = $name; 
        echo "<script>alert('Profile Updated in Database!'); window.location.href='../../View/html/profile.php';</script>";
    } else {
        echo "Error updating profile.";
    }
}
?>
