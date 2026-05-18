<?php
session_start();
include '../../db.php'; 
/** @var mysqli $conn */

if(!isset($_SESSION['isLoggedIn']) || !$_SESSION['isLoggedIn'] || ($_SESSION['role'] ?? '') != 'seeker'){
    header("Location: ../../../Student1/View/login.php");
    exit();
}

$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($job_id === 0) {
    header("Location: home.php");
    exit();
}
$user_id = intval($_SESSION['user_id']);

$job_sql = "SELECT title FROM jobs WHERE id = $job_id";
$job_result = mysqli_query($conn, $job_sql);
$job_data = mysqli_fetch_assoc($job_result);
$job_title = isset($job_data['title']) ? $job_data['title'] : "Selected Job";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Form - <?php echo $job_title; ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container" style="margin-bottom: 80px;">
        <div class="form-card" style="max-width: 600px; margin: 30px auto;">
            <h2>Application Form</h2>
            <p style="color: #555; margin-bottom: 20px;">Applying for: <strong><?php echo $job_title; ?></strong></p>
            
            <form action="../../Controller/php/actionController.php" method="POST" enctype="multipart/form-data">
                
                <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

                <div class="form-group">
                    <label><strong>Cover Letter</strong></label>
                    <textarea name="cover_letter" rows="8" placeholder="Write your cover letter here..." required></textarea>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label><strong>Resume Upload</strong></label>
                    <input type="file" name="resume_file" accept=".pdf,.doc,.docx" required>
                    <small style="color: #777; display: block; margin-top: 5px;">Accepted formats: PDF, DOC, DOCX</small>
                </div>

                <br>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <button type="submit" name="action" value="submit_application" class="apply-btn" style="border: none; cursor: pointer; padding: 10px 25px; font-weight: bold;">Submit Application</button>
                    <button type="button" class="nav-button" onclick="window.location.href='job_details.php?id=<?php echo $job_id; ?>'">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 Job Portal. All Rights Reserved.</p>
    </footer>
</body>
</html>
