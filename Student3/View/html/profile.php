<?php
session_start();
include '../../db.php'; 
require_once '../../Model/JobModel.php';
/** @var mysqli $conn */

$jobModel = new JobModel();

if (!isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

$user = $jobModel->getUserById(intval($_SESSION['user_id']));
if (!$user) {
    header("Location: home.php");
    exit();
}

$name = isset($user['name']) ? $user['name'] : "";
$id = isset($user['id']) ? $user['id'] : "";
$skills = isset($user['skills']) ? $user['skills'] : "";
$experience = isset($user['experience']) ? $user['experience'] : "";
$email = isset($user['email']) ? $user['email'] : "";
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
                <input type="text" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label>ID:</label>
                <input type="text" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Skills:</label>
                <input type="text" name="skills" value="<?php echo htmlspecialchars($skills, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label>Year of Experience:</label>
                <input type="text" name="experience" value="<?php echo htmlspecialchars($experience, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label>Upload Resume:</label>
                <input type="file" name="resume_file">
            </div>
            <button type="submit" name="update_profile" class="save-btn">Save / Edit</button>
        </form>
        <br>
        <a href="home.php">Back to Dashboard</a>
    </div>
</body>
</html>