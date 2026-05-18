<?php
session_start();
include "../Model/DatabaseConnection.php";

if(!isset($_SESSION["isLoggedIn"]) || !$_SESSION["isLoggedIn"]){
    header("Location: login.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();
$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

$profile = [];
if($role == "employer"){
    $result = $db->GetProfileByUserIdWithPrepareStmt($connection, "employer_profiles", $user_id);
} else {
    $result = $db->GetProfileByUserIdWithPrepareStmt($connection, "seeker_profiles", $user_id);
}

if($result->num_rows > 0){
    $profile = $result->fetch_assoc();
}

$errors = $_SESSION["profile_errors"] ?? [];
$old = $_SESSION["profile_old"] ?? [];
$message = $_SESSION["profile_message"] ?? "";
unset($_SESSION["profile_errors"]);
unset($_SESSION["profile_old"]);
unset($_SESSION["profile_message"]);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f8f8f8;
        }
        fieldset{
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 15px;
        }
        input[type="text"], input[type="password"], input[type="number"], textarea{
            padding: 8px;
            width: 100%;
            max-width: 650px;
            box-sizing: border-box;
        }
        .error{
            color: red;
        }
        .success{
            color: green;
        }
        .nav-button{
            padding: 8px 14px;
            border: 1px solid #999;
            background-color: #f4f4f4;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Profile</h2>

    <?php if($message) echo "<p class='success'><strong>".$message."</strong></p>"; ?>

    <?php
    if($role == "employer"){
        echo "<p><button type='button' class='nav-button' onclick=\"window.location.href='employer_dashboard.php'\">Back to Dashboard</button></p>";
    } else {
        echo "<p><button type='button' class='nav-button' onclick=\"window.location.href='job_board.php'\">Back to Job Board</button></p>";
    }
    ?>

    <form action="../Controller/AuthController.php" method="post" enctype="multipart/form-data">
        <fieldset>
            <legend>Basic Information</legend>
            <p>Name: <?php echo $_SESSION["name"]; ?></p>
            <p>Email: <?php echo $_SESSION["email"]; ?></p>
            <p>Role: <?php echo $_SESSION["role"]; ?></p>
            <?php
            if(($_SESSION["file_path"] ?? "") != ""){
                if($role == "employer"){
                    echo "<p>Current File:<br><img src='".$_SESSION["file_path"]."' alt='No file' height='80'></p>";
                } else {
                    echo "<p>Current File: <button type='button' class='nav-button' onclick=\"window.open('".$_SESSION["file_path"]."', '_blank')\">Open Uploaded Resume</button></p>";
                }
            }
            ?>
        </fieldset>

        <?php if($role == "employer"): ?>
            <fieldset>
                <legend>Employer Profile</legend>

                <label>Company Name</label><br>
                <input type="text" name="company_name" value="<?php echo $old["company_name"] ?? ($profile["company_name"] ?? ""); ?>">
                <p class="error"><?php echo $errors["company_name"] ?? ""; ?></p>

                <label>Industry</label><br>
                <input type="text" name="industry" value="<?php echo $old["industry"] ?? ($profile["industry"] ?? ""); ?>"><br><br>

                <label>Description</label><br>
                <textarea name="description" rows="5"><?php echo $old["description"] ?? ($profile["description"] ?? ""); ?></textarea><br><br>

                <label>Website</label><br>
                <input type="text" name="website" value="<?php echo $old["website"] ?? ($profile["website"] ?? ""); ?>"><br><br>
            </fieldset>
        <?php else: ?>
            <fieldset>
                <legend>Job Seeker Profile</legend>

                <label>Headline</label><br>
                <input type="text" name="headline" value="<?php echo $old["headline"] ?? ($profile["headline"] ?? ""); ?>">
                <p class="error"><?php echo $errors["headline"] ?? ""; ?></p>

                <label>Skills</label><br>
                <textarea name="skills" rows="5"><?php echo $old["skills"] ?? ($profile["skills"] ?? ""); ?></textarea>
                <p class="error"><?php echo $errors["skills"] ?? ""; ?></p>

                <label>Years of Experience</label><br>
                <input type="number" name="years_experience" value="<?php echo $old["years_experience"] ?? ($profile["years_experience"] ?? ""); ?>">
                <p class="error"><?php echo $errors["years_experience"] ?? ""; ?></p>
            </fieldset>
        <?php endif; ?>

        <fieldset>
            <legend>Optional Update</legend>

            <label>Upload New Logo / Resume</label><br>
            <input type="file" name="fileupload">
            <p class="error"><?php echo $errors["file"] ?? ""; ?></p>

            <label>Current Password</label><br>
            <input type="password" name="current_password"><br><br>

            <label>New Password</label><br>
            <input type="password" name="new_password">
            <p class="error"><?php echo $errors["password"] ?? ""; ?></p>
        </fieldset>

        <input type="submit" name="save_profile" value="Save Profile">
    </form>
</body>
</html>
