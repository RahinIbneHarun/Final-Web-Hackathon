<?php
session_start();
include '../../db.php'; 
/** @var mysqli $conn */

if(!isset($_SESSION['isLoggedIn']) || !$_SESSION['isLoggedIn'] || ($_SESSION['role'] ?? '') != 'seeker'){
    echo '<p style="grid-column: 1/-1; text-align:center; color: red; font-weight: bold;">Please login as job seeker first.</p>';
    exit();
}

$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$job_type = isset($_GET['job_type']) ? $_GET['job_type'] : '';
$salary = isset($_GET['salary']) ? $_GET['salary'] : '';

$sql = "SELECT * FROM jobs WHERE status = 'active'";

if ($category_id != '') {
    $sql .= " AND category_id = " . intval($category_id);
}

if ($location != '') {
    $safe_location = mysqli_real_escape_string($conn, $location);
    $sql .= " AND location LIKE '%$safe_location%'";
}

if ($job_type != '') {
    $safe_job_type = mysqli_real_escape_string($conn, $job_type);
    $sql .= " AND job_type = '$safe_job_type'";
}

if ($salary != '') {
    $safe_salary = mysqli_real_escape_string($conn, $salary);
    $sql .= " AND salary_range LIKE '%$safe_salary%'";
}

$sql .= " ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
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
    echo '<p style="grid-column: 1/-1; text-align:center; color: red; font-weight: bold;">No jobs match your selected filters.</p>';
}
?>
