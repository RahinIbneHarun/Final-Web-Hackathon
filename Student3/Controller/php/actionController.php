<?php
session_start();
include '../../db.php';
require_once '../../Model/JobModel.php';
/** @var mysqli $conn */

$jobModel = new JobModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $job_id = intval($_POST['job_id']);
    $user_id = intval($_POST['user_id']);

    if ($action == 'save') {
        if ($jobModel->saveJob($user_id, $job_id)) {
            echo "<script>alert('Job Saved Successfully!'); window.location.href='../../View/html/saved_jobs.php';</script>";
        } else {
            echo "Error saving job: " . $jobModel->conn->error;
        }
        exit();
    }

    if ($action == 'unsave') {
        if ($jobModel->removeSavedJob($user_id, $job_id)) {
            echo "<script>alert('Job Unsaved!'); window.location.href='../../View/html/job_details.php?id=$job_id';</script>";
        } else {
            echo "Error unsaving job: " . $jobModel->conn->error;
        }
        exit();
    }

    if ($action == 'submit_application') {
        $cover_letter = mysqli_real_escape_string($conn, $_POST['cover_letter']);
        $resume_path = "";

        if (!empty($_FILES['resume_file']['name'])) {
            $resume_path = time() . "_" . basename($_FILES['resume_file']['name']);
            $target_dir = "../../View/asset/";
            move_uploaded_file($_FILES['resume_file']['tmp_name'], $target_dir . $resume_path);
        }

        if ($jobModel->applyJob($job_id, $user_id, $cover_letter, $resume_path)) {
            echo "<script>alert('Application Submitted Successfully!'); window.location.href='../../View/html/applied_jobs.php';</script>";
        } else {
            echo "Error submitting application: " . $jobModel->conn->error;
        }
        exit();
    }

    header("Location: ../../View/html/home.php");
    exit();
}

header("Location: ../../View/html/home.php");
exit();
?>
