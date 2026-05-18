<?php
session_start();
include '../../db.php'; 
/** @var mysqli $conn */

if(!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true || ($_SESSION['role'] ?? '') != 'seeker'){
    echo '<p style="grid-column: 1/-1; text-align:center; color:red;">Please login first.</p>';
    exit();
}

$query = isset($_GET['query']) ? $_GET['query'] : '';

if($query != "") {
    $safe_query = mysqli_real_escape_string($conn, $query);
    $sql = "SELECT * FROM jobs WHERE (title LIKE '%$safe_query%' OR location LIKE '%$safe_query%') AND status = 'active' ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM jobs WHERE status = 'active' ORDER BY id DESC";
}
$result = mysqli_query($conn, $sql);
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $emp_id = $row['employer_id'];
        $emp_sql = "SELECT company_name FROM employer_profiles WHERE user_id = $emp_id";
        $emp_result = mysqli_query($conn, $emp_sql);
        $emp_row = mysqli_fetch_assoc($emp_result);
        
        $company_name = isset($emp_row['company_name']) ? $emp_row['company_name'] : 'N/A';
        echo '<div class="job-box">
                <h4>' . $row['title'] . '</h4>
                <p><strong>Company:</strong> ' . $company_name . '</p>
                <p><strong>Location:</strong> ' . $row['location'] . '</p>
                <p><strong>Deadline:</strong> ' . $row['deadline'] . '</p>
                <a href="job_details.php?id=' . $row['id'] . '" class="see-more-btn" style="text-decoration:none; display:inline-block;">See More</a>
              </div>';
    }
} else {
    echo '<p style="grid-column: 1/-1; text-align:center;">No jobs found.</p>';
}
?>
