<?php
include '../../db.php'; 
require_once '../../Model/JobModel.php';
/** @var mysqli $conn */

$jobModel = new JobModel();

$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$job_type = isset($_GET['job_type']) ? $_GET['job_type'] : '';
$salary = isset($_GET['salary']) ? $_GET['salary'] : '';

$result = $jobModel->getFilteredJobs($category_id, $location, $job_type, $salary);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $emp_id = $row['employer_id'];
        $company_name = $jobModel->getCompanyNameByUserId($emp_id);
        $company_name = $company_name ? $company_name : 'N/A';
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