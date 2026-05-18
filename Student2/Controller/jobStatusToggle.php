<?php
include "../Model/DatabaseConnection.php";
session_start();
header("Content-Type: application/json");

if($_SERVER["REQUEST_METHOD"] != "POST"){
    echo json_encode([
        "success" => false,
        "message" => "POST request required."
    ]);
    exit();
}

if(!isset($_SESSION["isLoggedIn"]) || !$_SESSION["isLoggedIn"]){
    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);
    exit();
}

if(!isset($_SESSION["role"]) || $_SESSION["role"] != "employer"){
    echo json_encode([
        "success" => false,
        "message" => "Only employer can change job status."
    ]);
    exit();
}

$job_id = $_GET["id"] ?? 0;
$employer_id = $_SESSION["user_id"] ?? 0;

if(!$job_id){
    echo json_encode([
        "success" => false,
        "message" => "Job id is required."
    ]);
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();
$status = $db->toggleJobStatus($connection, $job_id, $employer_id);

if(!$status){
    echo json_encode([
        "success" => false,
        "message" => "Job not found or update failed."
    ]);
    exit();
}

$label = "Closed";
$color = "red";

if($status == "active"){
    $label = "Active";
    $color = "green";
}

echo json_encode([
    "success" => true,
    "status" => $status,
    "label" => $label,
    "color" => $color
]);
exit();
?>
