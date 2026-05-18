<?php
session_start();
include "../Model/DatabaseConnection.php";

if(!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") != "employer"){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$name = $_SESSION["name"];
$email = $_SESSION["email"] ?? "";

$db = new DatabaseConnection();
$connection = $db->openConnection();

$result = $db->GetProfileByUserIdWithPrepareStmt($connection, "employer_profiles", $user_id);
$isProfileComplete = ($result->num_rows > 0);
$message = $_SESSION["profile_message"] ?? "";
unset($_SESSION["profile_message"]);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employer Dashboard</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f8f8f8;
        }
        .notice{
            border: 1px solid red;
            background-color: #ffdddd;
            color: red;
            padding: 10px;
            margin-bottom: 15px;
        }
        .success{
            color: green;
            font-weight: bold;
        }
        .nav-button{
            padding: 8px 14px;
            border: 1px solid #999;
            background-color: #f4f4f4;
            cursor: pointer;
            margin: 0 8px 8px 0;
        }
        .button-row{
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <h2>Employer Dashboard</h2>
    <p>Welcome, <?php echo $name; ?>!</p>
    <p>Email: <?php echo $email; ?></p>

    <?php if($message) echo "<p class='success'>".$message."</p>"; ?>

    <?php if(!$isProfileComplete): ?>
        <div class="notice">
            <strong>Notice:</strong> Your profile is incomplete. Please <a href="profile.php">complete your profile</a>.
        </div>
    <?php endif; ?>

    <div class="button-row">
        <button type="button" class="nav-button" onclick="window.location.href='profile.php'">Complete / Edit Profile</button>
        <button type="button" class="nav-button" onclick="window.location.href='../../Student2/View/employer_dashboard.php'">Open Employer Dashboard</button>
        <button type="button" class="nav-button" onclick="window.location.href='../../Student4/View/applications_dashboard.php'">Open Applications Dashboard</button>
    </div>

    <?php
    if($email == "admin@jobportal.test"){
        echo "<div class='button-row'><button type='button' class='nav-button' onclick=\"window.location.href='../../Student2/View/category_panel.php'\">Open Category Panel</button></div>";
    }
    ?>

    <div class="button-row">
        <button type="button" class="nav-button" onclick="window.location.href='../Controller/logout.php'">Logout</button>
    </div>
</body>
</html>
