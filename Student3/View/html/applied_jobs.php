<?php
session_start();
include '../../db.php';
require_once '../../Model/JobModel.php';

/** @var JobModel $jobModel */
$jobModel = new JobModel();
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 1;
$appliedJobs = $jobModel->getAppliedJobsByUser($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Apply List</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>My Apply List</h2>
        <div class="job-grid">
            <?php if ($appliedJobs && $appliedJobs->num_rows > 0): ?>
                <?php while($row = $appliedJobs->fetch_assoc()): ?>
                    <?php
                        $company_name = $jobModel->getCompanyNameByUserId($row['employer_id']);
                        $company_name = $company_name ? $company_name : 'N/A';
                        $status = !empty($row['application_status']) ? $row['application_status'] : 'Applied';
                    ?>
                    <div class="job-box">
                        <h4><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p><strong>Company:</strong> <?php echo htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></p>
                        <a href="job_details.php?id=<?php echo intval($row['id']); ?>" class="see-more-btn">See More</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align:center;">You have not applied for any jobs yet.</p>
            <?php endif; ?>
        </div>
        <br>
        <a href="home.php">Back to Dashboard</a>
    </div>
</body>
</html>