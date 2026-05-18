<?php
session_start();
include '../../db.php';
require_once '../../Model/JobModel.php';
/** @var mysqli $conn */

$jobModel = new JobModel();
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 1;
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 1;
$job = $jobModel->getJobById($job_id);

if (!$job) {
    die("Job not found!");
}
$emp_id = $job['employer_id'];
$company_name = $jobModel->getCompanyNameByUserId($emp_id);
$company_name = $company_name ? $company_name : 'N/A';

$is_saved = $jobModel->isJobSaved($user_id, $job_id);

$status = $jobModel->getApplicationStatus($job_id, $user_id);
$status = $status ? $status : 'Not Applied';
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
        <a href="home.php" style="display:block; text-align:center;">Back to Dashboard</a>
    </div>
</body>
</html>