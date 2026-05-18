<?php
session_start();
include '../../db.php';
require_once '../../Model/JobModel.php';
/** @var mysqli $conn */

$jobModel = new JobModel();

<<<<<<< HEAD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
=======
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
>>>>>>> student3
    
    $action = $_POST['action'];
    $job_id = intval($_POST['job_id']);
    $user_id = intval($_POST['user_id']); 

<<<<<<< HEAD
    switch ($action) {
        
        case 'save':
            $sql = "INSERT INTO saved_jobs (user_id, job_id) VALUES ($user_id, $job_id)";
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Job Saved Successfully!'); window.location.href='../../View/html/saved_jobs.php';</script>";
            } else {
                echo "Error saving job: " . mysqli_error($conn);
            }
            break;

        case 'unsave':
            $sql = "DELETE FROM saved_jobs WHERE job_id = $job_id AND user_id = $user_id";
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Job Unsaved!'); window.location.href='../../View/html/job_details.php?id=$job_id';</script>";
            } else {
                echo "Error unsaving job: " . mysqli_error($conn);
            }
            break;

        case 'submit_application':
            $cover_letter = mysqli_real_escape_string($conn, $_POST['cover_letter']);
            $resume_path = "";

            if (!empty($_FILES['resume_file']['name'])) {
                $resume_path = time() . "_" . basename($_FILES['resume_file']['name']);
                $target_dir = "../../View/asset/";
                move_uploaded_file($_FILES['resume_file']['tmp_name'], $target_dir . $resume_path);
            }
            
            $sql = "INSERT INTO applications (job_id, seeker_id, cover_letter, resume_path, status) 
                    VALUES ($job_id, $user_id, '$cover_letter', '$resume_path', 'Submitted')";
            
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Application Submitted Successfully!'); window.location.href='../../View/html/applied_jobs.php';</script>";
            } else {
                echo "Error submitting application: " . mysqli_error($conn);
            }
            break;
            
        default:
            header("Location: ../../View/html/home.php");
            exit();
    }
    
} else {
    header("Location: ../../View/html/home.php");
    exit();
=======
    if ($action == 'save') {
        if ($jobModel->saveJob($user_id, $job_id)) {
            echo "<script>alert('Job Saved Successfully!'); window.location.href='../../View/html/saved_jobs.php';</script>";
        } else {
            echo "Error saving job.";
        }
    }
    
    if ($action == 'unsave') {
        if ($jobModel->removeSavedJob($user_id, $job_id)) {
            echo "<script>alert('Job Unsaved!'); window.location.href='../../View/html/job_details.php?id=$job_id';</script>";
        } else {
            echo "Error unsaving job.";
        }
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
            echo "Error submitting application.";
        }
    }
} else {
    header("Location: ../../View/html/home.php");
>>>>>>> student3
}
?>