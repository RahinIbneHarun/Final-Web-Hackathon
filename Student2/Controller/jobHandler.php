<?php
include "../Model/DatabaseConnection.php";
session_start();

if(!isset($_SESSION["isLoggedIn"]) || !$_SESSION["isLoggedIn"]){
    header("Location: ../../Student1/View/login.php");
    exit();
}

if(!isset($_SESSION["role"]) || $_SESSION["role"] != "employer"){
    header("Location: ../../Student1/View/login.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] != "POST"){
    header("Location: ../View/employer_dashboard.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$action = $_POST["action"] ?? "";
$job_id = $_POST["id"] ?? 0;
$employer_id = $_SESSION["user_id"] ?? 0;

if($action == "delete"){
    $result = $db->getJobByIdForEmployer($connection, $job_id, $employer_id);

    if(!$result || $result->num_rows != 1){
        $_SESSION["job_message"] = "You can delete only your own jobs.";
        header("Location: ../View/employer_dashboard.php");
        exit();
    }

    $delete_result = $db->deleteJob($connection, $job_id, $employer_id);

    if($delete_result){
        $_SESSION["job_message"] = "Job deleted successfully.";
    }else{
        $_SESSION["job_message"] = "Job delete failed.";
    }

    header("Location: ../View/employer_dashboard.php");
    exit();
}

$category_id = $_POST["category_id"] ?? "";
$title = trim($_POST["title"] ?? "");
$description = trim($_POST["description"] ?? "");
$requirements = trim($_POST["requirements"] ?? "");
$salary_range = trim($_POST["salary_range"] ?? "");
$location = trim($_POST["location"] ?? "");
$job_type = trim($_POST["job_type"] ?? "");
$deadline = trim($_POST["deadline"] ?? "");

$_SESSION["job_old"] = [
    "category_id" => $category_id,
    "title" => $title,
    "description" => $description,
    "requirements" => $requirements,
    "salary_range" => $salary_range,
    "location" => $location,
    "job_type" => $job_type,
    "deadline" => $deadline
];

$_SESSION["job_errors"] = [];
$hasError = false;

$category_result = $db->getCategoryById($connection, $category_id);
if(!$category_id || !$category_result || $category_result->num_rows != 1){
    $_SESSION["job_errors"]["category_id"] = "Category is required.";
    $hasError = true;
}

if($title == ""){
    $_SESSION["job_errors"]["title"] = "Title is required.";
    $hasError = true;
}

if($description == ""){
    $_SESSION["job_errors"]["description"] = "Description is required.";
    $hasError = true;
}

if($requirements == ""){
    $_SESSION["job_errors"]["requirements"] = "Requirements are required.";
    $hasError = true;
}

if($salary_range == ""){
    $_SESSION["job_errors"]["salary_range"] = "Salary range is required.";
    $hasError = true;
}

if($location == ""){
    $_SESSION["job_errors"]["location"] = "Location is required.";
    $hasError = true;
}

if($job_type != "Full-time" && $job_type != "Part-time" && $job_type != "Remote"){
    $_SESSION["job_errors"]["job_type"] = "Choose a valid job type.";
    $hasError = true;
}

if($deadline == ""){
    $_SESSION["job_errors"]["deadline"] = "Deadline is required.";
    $hasError = true;
}else{
    $date = date_create($deadline);
    if(!$date || date_format($date, "Y-m-d") != $deadline){
        $_SESSION["job_errors"]["deadline"] = "Enter a valid date.";
        $hasError = true;
    }
}

if($action != "create" && $action != "update"){
    $_SESSION["job_errors"]["general"] = "Invalid action.";
    $hasError = true;
}

if($action == "update"){
    $result = $db->getJobByIdForEmployer($connection, $job_id, $employer_id);

    if(!$result || $result->num_rows != 1){
        $_SESSION["job_errors"]["general"] = "You can edit only your own jobs.";
        $hasError = true;
    }
}

if($hasError){
    if($action == "update"){
        header("Location: ../View/job_form.php?id=".$job_id);
    }else{
        header("Location: ../View/job_form.php");
    }
    exit();
}

unset($_SESSION["job_errors"]);

if($action == "create"){
    $result = $db->createJob($connection, $employer_id, $category_id, $title, $description, $requirements, $salary_range, $location, $job_type, $deadline, "active");

    if($result){
        $_SESSION["job_message"] = "Job created successfully.";
        unset($_SESSION["job_old"]);
    }else{
        $_SESSION["job_message"] = "Job create failed.";
    }
}else if($action == "update"){
    $result = $db->updateJob($connection, $job_id, $employer_id, $category_id, $title, $description, $requirements, $salary_range, $location, $job_type, $deadline);

    if($result){
        $_SESSION["job_message"] = "Job updated successfully.";
        unset($_SESSION["job_old"]);
    }else{
        $_SESSION["job_message"] = "Job update failed.";
    }
}

header("Location: ../View/employer_dashboard.php");
exit();
?>
