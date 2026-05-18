<?php
session_start();
include '../../db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['isLoggedIn']) || !$_SESSION['isLoggedIn'] || ($_SESSION['role'] ?? '') != 'seeker'){
    header("Location: ../../../Student1/View/login.php");
    exit();
}

$job_id = isset($_GET['id']) ? intval($_GET['id']) : 1;
$user_id = intval($_SESSION['user_id']); 
$job_query = "SELECT * FROM jobs WHERE id = $job_id";
$job_result = mysqli_query($conn, $job_query);
$job = mysqli_fetch_assoc($job_result);

if (!$job) {
    die("Job not found!");
}
$emp_id = $job['employer_id'];
$emp_query = "SELECT company_name FROM employer_profiles WHERE user_id = $emp_id";
$emp_result = mysqli_query($conn, $emp_query);
$emp_data = mysqli_fetch_assoc($emp_result);
$company_name = isset($emp_data['company_name']) ? $emp_data['company_name'] : 'N/A';

$save_query = "SELECT * FROM saved_jobs WHERE job_id = $job_id AND user_id = $user_id";
$save_result = mysqli_query($conn, $save_query);
$is_saved = mysqli_num_rows($save_result) > 0;

$apply_query = "SELECT status FROM applications WHERE job_id = $job_id AND seeker_id = $user_id";
$apply_result = mysqli_query($conn, $apply_query);

if (mysqli_num_rows($apply_result) > 0) {
    $apply_data = mysqli_fetch_assoc($apply_result);
    $status = $apply_data['status']; 
} else {
    $status = "Not Applied";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Details</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container" style="margin-bottom: 80px;">
        <h2>Job Specification Details</h2>
        <div class="details-box">
            <h3><strong>Job Title:</strong> <?php echo $job['title']; ?></h3>
            <p><strong>Company Name:</strong> <?php echo $company_name; ?></p>
            <p><strong>Location:</strong> <?php echo $job['location']; ?></p>
            <p><strong>Job Type:</strong> <?php echo $job['job_type']; ?></p>
            <p><strong>Salary Range:</strong> <?php echo $job['salary_range']; ?></p>
            <p><strong>Apply Deadline:</strong> <?php echo $job['deadline']; ?></p>
            <hr>
            <p><strong>Job Description:</strong><br><?php echo $job['description']; ?></p>
            <p><strong>Job Requirements:</strong><br><?php echo $job['requirements']; ?></p>
            <hr>
            <p><strong>Status:</strong> <span class="status-badge"><?php echo $status; ?></span></p>
            <br>
            
            <form action="../../Controller/php/actionController.php" method="POST" style="display:inline;">
                <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                
                <?php if ($is_saved): ?>
                    <button type="submit" name="action" value="unsave" class="unsave-btn">Unsave Job</button>
                <?php else: ?>
                    <button type="submit" name="action" value="save" class="save-btn">Save Job</button>
                <?php endif; ?>
            </form>

            <a href="application_form.php?id=<?php echo $job_id; ?>" class="apply-btn">Apply Now</a>
        </div>
        <br>
        <div style="text-align:center;">
            <button type="button" class="nav-button" onclick="window.location.href='home.php'">Back to Dashboard</button>
        </div>
    </div>
</body>
</html>
