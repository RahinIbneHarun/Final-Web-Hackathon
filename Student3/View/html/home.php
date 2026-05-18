<?php
session_start();
include '../../db.php'; 
/** @var mysqli $conn */

if(!isset($_SESSION['isLoggedIn']) || !$_SESSION['isLoggedIn'] || ($_SESSION['role'] ?? '') != 'seeker'){
    header("Location: ../../../Student1/View/login.php");
    exit();
}

$logged_in_user = isset($_SESSION['name']) ? $_SESSION['name'] : "Job Seeker"; 
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
            <button onclick="toggleMenu()" class="menu-btn">&#9776;</button>
            <h2>Job Dashboard</h2>
        </div>
        <div class="header-right">
            <span>Welcome, <strong><?php echo $logged_in_user; ?></strong></span>
            <input type="text" id="searchBox" placeholder="Search jobs..." onkeyup="liveSearch()">
        </div>
    </header>

    <nav id="sidebarMenu" class="sidebar">
        <a href="profile.php">Profile Page</a>
        <a href="saved_jobs.php">Saved Job List</a>
        <a href="applied_jobs.php">My Apply List</a>
        <a href="../../Controller/php/logout.php">Log Out</a>
    </nav>

    <main class="container" style="margin-bottom: 80px;">
        
        <div class="filter-container">
            <select id="filterCategory" onchange="applyFilter()">
                <option value="">Select Category</option>
                <?php
                $cat_sql = "SELECT * FROM categories";
                $cat_result = mysqli_query($conn, $cat_sql);
                while($cat = mysqli_fetch_assoc($cat_result)) {
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
            $sql = "SELECT * FROM jobs WHERE status = 'active' ORDER BY id DESC";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $emp_id = $row['employer_id'];
                    $emp_sql = "SELECT company_name FROM employer_profiles WHERE user_id = $emp_id";
                    $emp_result = mysqli_query($conn, $emp_sql);
                    $emp_row = mysqli_fetch_assoc($emp_result);
                    $company_name = isset($emp_row['company_name']) ? $emp_row['company_name'] : 'N/A';
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
        <p>&copy; 2026 Job Portal. All Rights Reserved.</p>
    </footer>

    <script src="../../Controller/js/main.js"></script>
</body>
</html>