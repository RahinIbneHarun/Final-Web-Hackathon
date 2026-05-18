<?php
session_start();
include '../../db.php'; 
require_once '../../Model/JobModel.php';
/** @var mysqli $conn */

$jobModel = new JobModel();
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Saved Job List</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container" style="margin-bottom: 80px;">
        <h2>Saved Job List</h2>
        <div class="job-grid">
            
            <?php
            $result = $jobModel->getSavedJobsByUser($user_id);

            if ($result && $result->num_rows > 0) {
                while($saved_row = $result->fetch_assoc()) {
                    $job_id = $saved_row['job_id'];
                    $job_row = $jobModel->getJobById($job_id);
                    if ($job_row) {
                        $emp_id = $job_row['employer_id'];
                        $company_name = $jobModel->getCompanyNameByUserId($emp_id);
                        $company_name = $company_name ? $company_name : 'N/A';
                        ?>
                        <div class="job-box">
                            <h4><?php echo $job_row['title']; ?></h4>
                            <p><strong>Company:</strong> <?php echo $company_name; ?></p>
                            <p><strong>Location:</strong> <?php echo $job_row['location']; ?></p>
                            <p><strong>Deadline:</strong> <?php echo $job_row['deadline']; ?></p>
                            
                            <a href="job_details.php?id=<?php echo $job_row['id']; ?>" class="see-more-btn" style="text-decoration:none; display:inline-block;">See More</a>
                        </div>
                        <?php
                    }
                }
            } else {
                echo "<p style='grid-column: 1/-1; text-align:center;'>You haven't saved any jobs yet.</p>";
            }
            ?>
            
        </div>
        <br><br>
        <div style="text-align: center;">
            <a href="home.php">Back to Dashboard</a>
        </div>
    </div>
    
    <footer class="footer">
        <p>&copy; 2026 Job Portal. All Rights Reserved.</p>
    </footer>
</body>
</html>