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
    <title>My Apply List</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>My Apply List</h2>
        <div class="job-grid">
            <?php
            $sql = "SELECT applications.status, jobs.id, jobs.title, jobs.location, employer_profiles.company_name
                    FROM applications
                    INNER JOIN jobs ON jobs.id = applications.job_id
                    LEFT JOIN employer_profiles ON employer_profiles.user_id = jobs.employer_id
                    WHERE applications.seeker_id = $user_id
                    ORDER BY applications.created_at DESC";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="job-box">
                        <h4><?php echo $row['title']; ?></h4>
                        <p><strong>Company:</strong> <?php echo $row['company_name'] ? $row['company_name'] : 'N/A'; ?></p>
                        <p><strong>Location:</strong> <?php echo $row['location']; ?></p>
                        <p><strong>Status:</strong> <?php echo $row['status']; ?></p>
                        <a href="job_details.php?id=<?php echo $row['id']; ?>" class="see-more-btn" style="text-decoration:none; display:inline-block;">See More</a>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='grid-column: 1/-1; text-align:center;'>You have not applied to any job yet.</p>";
            }
            ?>
        </div>
        <br>
        <button type="button" class="nav-button" onclick="window.location.href='home.php'">Back to Dashboard</button>
    </div>
</body>
</html>
