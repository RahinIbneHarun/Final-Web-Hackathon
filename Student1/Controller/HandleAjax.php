<?php
include "../Model/DatabaseConnection.php";

$db = new DatabaseConnection();
$connection = $db->openConnection();

$type = $_POST["type"] ?? "";
$value = trim($_POST["value"] ?? "");

if($type == "register_name"){
    if($value == ""){
        echo "Name is required.";
        exit();
    }

    $result = $db->GetUserByNameWithPrepareStmt($connection, "users", $value);

    if($result && $result->num_rows > 0){
        echo "Same name already exists.";
    }else{
        echo "";
    }
    exit();
}

if($type == "register_email"){
    if($value == ""){
        echo "Email is required.";
        exit();
    }

    if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
        echo "Enter a valid email address.";
        exit();
    }

    $result = $db->GetUserByEmailWithPrepareStmt($connection, "users", $value);

    if($result && $result->num_rows > 0){
        echo "Email already exists.";
    }else{
        echo "";
    }
    exit();
}

echo "";
?>
