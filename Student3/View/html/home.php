<?php
session_start();
include '../../db.php'; 
require_once '../../Model/JobModel.php';
/** @var mysqli $conn */

$jobModel = new JobModel();
$logged_in_user = "Guest";
if (isset($_SESSION['user_id'])) {
    $current_user = $jobModel->getUserById(intval($_SESSION['user_id']));
    if ($current_user && !empty($current_user['name'])) {
        $logged_in_user = $current_user['name'];
    }
} elseif (isset($_SESSION['user_name'])) {
    $logged_in_user = $_SESSION['user_name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Seeker Home</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <header class="header">
        <div class="header-left">
            <button id="menuToggle" class="menu-btn">&#9776;</button>
            <h2>Job Dashboard</h2>
        </div>
        <div class="header-right">
            <span>Welcome, <strong><?php echo htmlspecialchars($logged_in_user, ENT_QUOTES, 'UTF-8'); ?></strong></span>
            <input type="text" id="searchBox" placeholder="Search jobs..." onkeyup="liveSearch()">
        </div>
    </header>

    <nav id="sidebarMenu" class="sidebar">
        <a href="profile.php">Profile Page</a>
        <a href="saved_jobs.php">Saved Job List</a>
        <a href="applied_jobs.php">My Apply List</a>
        <a href="../../../Student1/Controller/logout.php">Log Out</a>
    </nav>

    <main class="container" style="margin-bottom: 80px;">
        
        <div class="filter-container">
            <select id="filterCategory" onchange="applyFilter()">
                <option value="">Select Category</option>
                <?php
                $cat_result = $jobModel->getCategories();
                while($cat = $cat_result->fetch_assoc()) {
                    echo "<option value='".$cat['id']."'>".$cat['name']."</option>";
                }
                ?>
            </select>

            <select id="filterLocation" onchange="applyFilter()">
                <option value="">Select Location</option>
                <option value="Dhaka">Dhaka</option>
                <option value="Remote">Remote</option>
                <option value="Chittagong">Chittagong</option>
            </select>

            <select id="filterJobType" onchange="applyFilter()">
                <option value="">Select Job Type</option>
                <option value="Full-time">Full-time</option>
                <option value="Part-time">Part-time</option>
                <option value="Remote">Remote</option>
            </select>

            <select id="filterSalary" onchange="applyFilter()">
                <option value="">Select Salary Range</option>
                <option value="20000">20,000+ BDT</option>
                <option value="40000">40,000+ BDT</option>
                <option value="60000">60,000+ BDT</option>
            </select>
        </div>

        <h3>Available Jobs</h3>
        
        <div id="jobContainer" class="job-grid">
            <?php
            $result = $jobModel->getActiveJobs();

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $emp_id = $row['employer_id'];
                    $company_name = $jobModel->getCompanyNameByUserId($emp_id);
                    $company_name = $company_name ? $company_name : 'N/A';
                    ?>
                    <div class="job-box">
                        <h4><?php echo $row['title']; ?></h4>
                        <p><strong>Company:</strong> <?php echo $company_name; ?></p>
                        <p><strong>Location:</strong> <?php echo $row['location']; ?></p>
                        <p><strong>Deadline:</strong> <?php echo $row['deadline']; ?></p>
                        <a href="job_details.php?id=<?php echo $row['id']; ?>" class="see-more-btn" style="text-decoration:none; display:inline-block;">See More</a>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='grid-column: 1/-1; text-align:center;'>No jobs available.</p>";
            }
            ?>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; This portal is developed by Rahin</p>
    </footer>

    <script src="../../Controller/js/main.js"></script>
</body>
</html>
