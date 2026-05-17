<?php
session_start();
include '../../db.php'; 
/** @var mysqli $conn */

if(!isset($_SESSION['isLoggedIn']) || !$_SESSION['isLoggedIn'] || ($_SESSION['role'] ?? '') != 'seeker'){
    header("Location: ../../../Student1/View/login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$sql = "SELECT * FROM users WHERE id = $user_id"; 
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
$profile_sql = "SELECT * FROM seeker_profiles WHERE user_id = $user_id";
$profile_result = mysqli_query($conn, $profile_sql);
$profile = mysqli_fetch_assoc($profile_result);
$name = isset($user['name']) ? $user['name'] : "Billal";
$id = isset($user['id']) ? $user['id'] : "";
$skills = isset($profile['skills']) ? $profile['skills'] : "";
$experience = isset($profile['years_experience']) ? $profile['years_experience'] : "";
$email = isset($user['email']) ? $user['email'] : "";
$file_path = isset($user['file_path']) ? $user['file_path'] : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Page</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Your Profile</h2>
        <form action="../../Controller/php/profileController.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?php echo $name; ?>" required>
            </div>
            <div class="form-group">
                <label>ID:</label>
                <input type="text" name="id" value="<?php echo $id; ?>" readonly>
            </div>
            <div class="form-group">
                <label>Skills:</label>
                <input type="text" name="skills" value="<?php echo $skills; ?>">
            </div>
            <div class="form-group">
                <label>Year of Experience:</label>
                <input type="text" name="experience" value="<?php echo $experience; ?>">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo $email; ?>" required>
            </div>
            <div class="form-group">
                <label>Upload Resume:</label>
                <input type="file" name="resume_file">
            </div>
            <?php
            if($file_path != ""){
                echo "<p><button type='button' class='nav-button' onclick=\"window.open('".$file_path."', '_blank')\">Open Current Resume</button></p>";
            }
            ?>
            <button type="submit" name="update_profile" class="save-btn">Save / Edit</button>
        </form>
        <br>
        <button type="button" class="nav-button" onclick="window.location.href='home.php'">Back to Dashboard</button>
    </div>
</body>
</html>
