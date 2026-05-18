<?php
session_start();
include '../../db.php'; 
/** @var mysqli $conn */

if(!isset($_SESSION['isLoggedIn']) || !$_SESSION['isLoggedIn'] || ($_SESSION['role'] ?? '') != 'seeker'){
    header("Location: ../../../Student1/View/login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']); 
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
            $sql = "SELECT * FROM saved_jobs WHERE user_id = $user_id ORDER BY created_at DESC";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while($saved_row = mysqli_fetch_assoc($result)) {
                    
                    $job_id = $saved_row['job_id'];
                    
                    $job_sql = "SELECT * FROM jobs WHERE id = $job_id";
                    $job_result = mysqli_query($conn, $job_sql);
                    $job_row = mysqli_fetch_assoc($job_result);
                    
                    if ($job_row) {
                        $emp_id = $job_row['employer_id'];
                        $emp_sql = "SELECT company_name FROM employer_profiles WHERE user_id = $emp_id";
                        $emp_result = mysqli_query($conn, $emp_sql);
                        $emp_row = mysqli_fetch_assoc($emp_result);
                        $company_name = isset($emp_row['company_name']) ? $emp_row['company_name'] : 'N/A';
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
            <button type="button" class="nav-button" onclick="window.location.href='home.php'">Back to Dashboard</button>
        </div>
    </div>
    
    <footer class="footer">
        <p>&copy; 2026 Job Portal. All Rights Reserved.</p>
    </footer>
</body>
</html>
