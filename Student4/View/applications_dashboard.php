<?php
include "../Model/DatabaseConnection.php";
session_start();

if(!isset($_SESSION["isLoggedIn"]) || !$_SESSION["isLoggedIn"]){
    echo "Please login first.";
    exit();
}

if(!isset($_SESSION["role"]) || $_SESSION["role"] != "employer"){
    echo "Only employer can use this page.";
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();
$employer_id = $_SESSION["user_id"];

$selected_job_id = $_GET["job_id"] ?? "";
$selected_status = $_GET["status"] ?? "";

$jobs = $db->getEmployerJobs($connection, $employer_id);
$summary = $db->getApplicationSummary($connection, $employer_id);
$applications = $db->getEmployerApplications($connection, $employer_id, $selected_job_id, $selected_status);

$message = $_SESSION["application_message"] ?? "";
unset($_SESSION["application_message"]);

$total_applications = (int)($summary["total_applications"] ?? 0);
$submitted_count = (int)($summary["submitted_count"] ?? 0);
$reviewed_count = (int)($summary["reviewed_count"] ?? 0);
$shortlisted_count = (int)($summary["shortlisted_count"] ?? 0);
$rejected_count = (int)($summary["rejected_count"] ?? 0);

function getFunnelWidth($count, $total){
    if($total <= 0){
        return 18;
    }

    if($count <= 0){
        return 18;
    }

    $width = round(($count / $total) * 100);

    if($width < 30){
        $width = 30;
    }

    return $width;
}

$funnel_steps = [
    ["label" => "Total Applications", "count" => $total_applications, "width" => 100, "color" => "#14532d"],
    ["label" => "Submitted", "count" => $submitted_count, "width" => getFunnelWidth($submitted_count, $total_applications), "color" => "#166534"],
    ["label" => "Reviewed", "count" => $reviewed_count, "width" => getFunnelWidth($reviewed_count, $total_applications), "color" => "#15803d"],
    ["label" => "Shortlisted", "count" => $shortlisted_count, "width" => getFunnelWidth($shortlisted_count, $total_applications), "color" => "#16a34a"],
    ["label" => "Rejected", "count" => $rejected_count, "width" => getFunnelWidth($rejected_count, $total_applications), "color" => "#6b7280"]
];
?>
<html>
    <head>
        <title>Applications Dashboard</title>
        <style>
            body{
                font-family: Arial, sans-serif;
                max-width: 1180px;
                margin: 0 auto;
                padding: 24px;
                background-color: #eef3ef;
                color: #1f2937;
            }
            a{
                color: #1a5fb4;
                text-decoration: none;
            }
            a:hover{
                text-decoration: underline;
            }
            h1{
                margin-bottom: 8px;
            }
            h2{
                margin: 0 0 14px 0;
                font-size: 22px;
            }
            .summary-box{
                display: inline-block;
                min-width: 150px;
                padding: 16px;
                margin: 0 12px 12px 0;
                background-color: #ffffff;
                border: 1px solid #d7dfd8;
                border-radius: 10px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                vertical-align: top;
            }
            .filter-box{
                background-color: #ffffff;
                border: 1px solid #d7dfd8;
                padding: 18px;
                margin: 18px 0;
                border-radius: 10px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }
            table{
                border-collapse: collapse;
                width: 100%;
                background-color: #ffffff;
                border: 1px solid #d7dfd8;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }
            th, td{
                padding: 12px;
                text-align: left;
                vertical-align: top;
                border-bottom: 1px solid #e7ece8;
            }
            th{
                background-color: #dff0e3;
                color: #164e2b;
            }
            select, input[type="submit"]{
                padding: 9px 10px;
                border: 1px solid #b8c9bc;
                border-radius: 6px;
                margin-right: 10px;
                margin-top: 8px;
            }
            input[type="submit"]{
                border-color: #198754;
                background-color: #198754;
                color: #ffffff;
                cursor: pointer;
            }
            input[type="submit"]:hover{
                background-color: #146c43;
            }
            .nav-button{
                padding: 9px 15px;
                border: 1px solid #198754;
                background-color: #198754;
                color: #ffffff;
                cursor: pointer;
                margin-right: 8px;
                border-radius: 6px;
            }
            .nav-button:hover{
                background-color: #146c43;
            }
            .message{
                color: #166534;
                font-weight: bold;
                padding: 10px 14px;
                background-color: #ecfdf3;
                border: 1px solid #bbf7d0;
                border-radius: 8px;
            }
            .funnel-box{
                background-color: #ffffff;
                border: 1px solid #d7dfd8;
                padding: 18px;
                margin: 18px 0;
                border-radius: 10px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }
            .funnel-title{
                margin-top: 0;
                margin-bottom: 15px;
            }
            .funnel-step{
                color: #ffffff;
                text-align: center;
                padding: 12px 10px;
                margin: 10px auto;
                font-weight: bold;
                border-radius: 4px;
            }
            .funnel-empty{
                margin: 0;
                color: #555;
            }
            p{
                line-height: 1.5;
            }
            table tr:hover td{
                background-color: #f7fbf8;
            }
        </style>
    </head>
    <body>
        <h1>Applications Dashboard</h1>
        <p>Hello, <?php echo $_SESSION["name"] ?? "Employer"; ?></p>
        <p>
            <button type="button" class="nav-button" onclick="window.location.href='../../Student1/View/employer_dashboard.php'">Back to Dashboard</button>
            <button type="button" class="nav-button" onclick="window.location.href='../../Student2/View/employer_dashboard.php'">Open Job Dashboard</button>
        </p>

        <?php
        if($message){
            echo "<p class='message'>".$message."</p>";
        }
        ?>

        <div class="summary-box">
            <strong>Total Applications</strong><br>
            <?php echo $summary["total_applications"] ? $summary["total_applications"] : 0; ?>
        </div>
        <div class="summary-box">
            <strong>Submitted</strong><br>
            <?php echo $summary["submitted_count"] ? $summary["submitted_count"] : 0; ?>
        </div>
        <div class="summary-box">
            <strong>Reviewed</strong><br>
            <?php echo $summary["reviewed_count"] ? $summary["reviewed_count"] : 0; ?>
        </div>
        <div class="summary-box">
            <strong>Shortlisted</strong><br>
            <?php echo $summary["shortlisted_count"] ? $summary["shortlisted_count"] : 0; ?>
        </div>
        <div class="summary-box">
            <strong>Rejected</strong><br>
            <?php echo $summary["rejected_count"] ? $summary["rejected_count"] : 0; ?>
        </div>

        <div class="funnel-box">
            <h2 class="funnel-title">Application Funnel Chart</h2>

            <?php
            if($total_applications > 0){
                foreach($funnel_steps as $step){
                    echo "<div class='funnel-step' style='width: ".$step["width"]."%; background-color: ".$step["color"].";'>";
                    echo $step["label"]." - ".$step["count"];
                    echo "</div>";
                }
            }else{
                echo "<p class='funnel-empty'>No application data available for the funnel chart.</p>";
            }
            ?>
        </div>

        <div class="filter-box">
            <form method="get" action="applications_dashboard.php">
                <label>Filter by Job</label>
                <select name="job_id">
                    <option value="">All Jobs</option>
                    <?php
                    if($jobs && $jobs->num_rows > 0){
                        while($job_row = $jobs->fetch_assoc()){
                            $selected = "";
                            if($selected_job_id == $job_row["id"]){
                                $selected = "selected";
                            }
                            echo "<option value='".$job_row["id"]."' ".$selected.">".$job_row["title"]."</option>";
                        }
                    }
                    ?>
                </select>

                <label>Status</label>
                <select name="status">
                    <option value="">All Status</option>
                    <option value="Submitted" <?php if($selected_status == "Submitted"){ echo "selected"; } ?>>Submitted</option>
                    <option value="Reviewed" <?php if($selected_status == "Reviewed"){ echo "selected"; } ?>>Reviewed</option>
                    <option value="Shortlisted" <?php if($selected_status == "Shortlisted"){ echo "selected"; } ?>>Shortlisted</option>
                    <option value="Rejected" <?php if($selected_status == "Rejected"){ echo "selected"; } ?>>Rejected</option>
                </select>

                <input type="submit" value="Filter">
                <button type="button" class="nav-button" onclick="window.location.href='applications_dashboard.php'">Reset</button>
            </form>
        </div>

        <table border="1">
            <tr>
                <th>Job Title</th>
                <th>Applicant Name</th>
                <th>Applicant Email</th>
                <th>Status</th>
                <th>Applied At</th>
                <th>Action</th>
            </tr>

            <?php
            if($applications && $applications->num_rows > 0){
                while($row = $applications->fetch_assoc()){
                    echo "<tr>";
                    echo "<td>".$row["title"]."</td>";
                    echo "<td>".$row["seeker_name"]."</td>";
                    echo "<td>".$row["seeker_email"]."</td>";
                    echo "<td>".$row["status"]."</td>";
                    echo "<td>".$row["created_at"]."</td>";
                    echo "<td><button type='button' class='nav-button' onclick=\"window.location.href='application_details.php?id=".$row["id"]."'\">View Details</button></td>";
                    echo "</tr>";
                }
            }else{
                echo "<tr><td colspan='6'>No applications found.</td></tr>";
            }
            ?>
        </table>
    </body>
</html>
