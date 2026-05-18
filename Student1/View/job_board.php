<?php
session_start();
include "../Model/DatabaseConnection.php";

if(!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") != "seeker"){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$name = $_SESSION["name"];

$db = new DatabaseConnection();
$connection = $db->openConnection();

$result = $db->GetProfileByUserIdWithPrepareStmt($connection, "seeker_profiles", $user_id);
$isProfileComplete = ($result->num_rows > 0);
$message = $_SESSION["profile_message"] ?? "";
unset($_SESSION["profile_message"]);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Job Board</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            margin: 24px;
        }
        .dashboard-card{
            max-width: 900px;
        }
        .button-row{
            margin-top: 14px;
        }
        .nav-button{
            padding: 8px 14px;
            margin-right: 8px;
            margin-bottom: 8px;
        }
        .notice{
            padding: 12px;
            margin-bottom: 14px;
            border: 1px solid;
        }
    </style>
</head>
<body>
    <div class="dashboard-card">
    <h2>Job Board</h2>
    <p>Welcome, <?php echo $name; ?>!</p>

    <?php if($message) echo "<p class='success'>".$message."</p>"; ?>

    <?php if(!$isProfileComplete): ?>
        <div class="notice">
            <strong>Notice:</strong> Your profile is incomplete. Please <a href="profile.php">complete your profile</a>.
        </div>
    <?php endif; ?>

    <div class="button-row">
        <button type="button" class="nav-button" onclick="window.location.href='profile.php'">Complete / Edit Profile</button>
        <button type="button" class="nav-button" onclick="window.location.href='../../Student3/View/html/home.php'">Open Job Board</button>
        <button type="button" class="nav-button" onclick="window.location.href='../Controller/logout.php'">Logout</button>
    </div>
    </div>
</body>
</html>
