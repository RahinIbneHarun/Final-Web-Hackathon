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
            margin: 0;
            padding: 28px;
            background: linear-gradient(180deg, #fff7ed 0%, #fffaf4 100%);
            color: #3f3f46;
        }
        .dashboard-card{
            max-width: 900px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 28px;
            border: 1px solid #fde2c4;
            border-radius: 14px;
            box-shadow: 0 10px 24px rgba(234, 88, 12, 0.08);
        }
        .notice{
            border: 1px solid #fca5a5;
            background-color: #fef2f2;
            color: #b91c1c;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
        }
        .success{
            color: #c2410c;
            font-weight: bold;
        }
        .nav-button{
            padding: 8px 14px;
            border: 1px solid #ea580c;
            background-color: #ea580c;
            color: #ffffff;
            cursor: pointer;
            margin: 0 8px 8px 0;
            border-radius: 8px;
        }
        .nav-button:hover{
            background-color: #c2410c;
        }
        .button-row{
            margin-top: 15px;
        }
        h2{
            margin-top: 0;
            color: #c2410c;
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
