<?php
include "../Model/DatabaseConnection.php";
session_start();

if(!isset($_SESSION["isLoggedIn"]) || !$_SESSION["isLoggedIn"]){
    echo "Please login first.";
    exit();
}

if(!isset($_SESSION["role"]) || $_SESSION["role"] != "employer"){
    echo "Only employer can use this page.";
    exit();
}

if($_SERVER["REQUEST_METHOD"] != "POST"){
    header("Location: ../View/applications_dashboard.php");
    exit();
}

$application_id = $_POST["application_id"] ?? 0;
$status = $_POST["status"] ?? "";
$employer_id = $_SESSION["user_id"] ?? 0;

if($status != "Submitted" && $status != "Reviewed" && $status != "Shortlisted" && $status != "Rejected"){
    $_SESSION["application_message"] = "Invalid status selected.";
    header("Location: ../View/application_details.php?id=".$application_id);
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();
$result = $db->updateApplicationStatus($connection, $application_id, $employer_id, $status);

if($result){
    $_SESSION["application_message"] = "Application status updated successfully.";
}else{
    $_SESSION["application_message"] = "Application status update failed.";
}

header("Location: ../View/application_details.php?id=".$application_id);
exit();
?>
