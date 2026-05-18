<?php
include "../Model/DatabaseConnection.php";
session_start();

if(!isset($_SESSION["isLoggedIn"]) || !$_SESSION["isLoggedIn"]){
    echo "Please login first.";
    exit();
}

$isAdmin = false;
if((isset($_SESSION["email"]) && $_SESSION["email"] == "admin@jobportal.test") || (isset($_SESSION["role"]) && $_SESSION["role"] == "admin")){
    $isAdmin = true;
}

if(!$isAdmin){
    echo "Only admin can use this page.";
    exit();
}

if($_SERVER["REQUEST_METHOD"] != "POST"){
    header("Location: ../View/category_panel.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$action = $_POST["action"] ?? "";
$category_id = $_POST["id"] ?? 0;
$name = trim($_POST["name"] ?? "");

$_SESSION["category_old_name"] = $name;

if($action == "delete"){
    $result = $db->getCategoryById($connection, $category_id);

    if(!$result || $result->num_rows != 1){
        $_SESSION["category_message"] = "Category not found.";
        header("Location: ../View/category_panel.php");
        exit();
    }

    if($db->categoryHasJobs($connection, $category_id)){
        $_SESSION["category_message"] = "Cannot delete this category because jobs are using it.";
        header("Location: ../View/category_panel.php");
        exit();
    }

    $delete_result = $db->deleteCategory($connection, $category_id);

    if($delete_result){
        $_SESSION["category_message"] = "Category deleted successfully.";
    }else{
        $_SESSION["category_message"] = "Category delete failed.";
    }

    header("Location: ../View/category_panel.php");
    exit();
}

$hasError = false;

if($name == ""){
    $_SESSION["category_name_error"] = "Category name is required.";
    $hasError = true;
}else{
    unset($_SESSION["category_name_error"]);
}

if($action != "create" && $action != "update"){
    $_SESSION["category_message"] = "Invalid action.";
    $hasError = true;
}

if($action == "update"){
    $result = $db->getCategoryById($connection, $category_id);

    if(!$result || $result->num_rows != 1){
        $_SESSION["category_message"] = "Category not found.";
        $hasError = true;
    }
}

if($hasError){
    if($action == "update"){
        header("Location: ../View/category_panel.php?edit=".$category_id);
    }else{
        header("Location: ../View/category_panel.php");
    }
    exit();
}

if($action == "create"){
    $result = $db->createCategory($connection, $name);

    if($result){
        $_SESSION["category_message"] = "Category created successfully.";
        unset($_SESSION["category_old_name"]);
    }else{
        $_SESSION["category_message"] = "Category create failed.";
    }
}else if($action == "update"){
    $result = $db->updateCategory($connection, $category_id, $name);

    if($result){
        $_SESSION["category_message"] = "Category updated successfully.";
        unset($_SESSION["category_old_name"]);
    }else{
        $_SESSION["category_message"] = "Category update failed.";
    }
}

header("Location: ../View/category_panel.php");
exit();
?>
