<?php
include "../Model/DatabaseConnection.php";
session_start();

if(!isset($_SESSION["isLoggedIn"]) || !$_SESSION["isLoggedIn"]){
    header("Location: ../../Student1/View/login.php");
    exit();
}

if(!isset($_SESSION["role"]) || $_SESSION["role"] != "employer"){
    header("Location: ../../Student1/View/login.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();
$employer_id = $_SESSION["user_id"];

$selected_job_id = "";
$selected_status = "";

$jobs = $db->getEmployerJobs($connection, $employer_id);
$summary = $db->getApplicationSummary($connection, $employer_id);
$applications = $db->getEmployerApplications($connection, $employer_id, "", "");

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
                margin: 24px;
            }
            .summary-box{
                display: inline-block;
                min-width: 140px;
                padding: 12px;
                border: 1px solid;
                margin: 0 10px 10px 0;
                vertical-align: top;
            }
            .filter-box, .funnel-box{
                margin: 18px 0;
                padding: 14px;
                border: 1px solid;
            }
            table{
                width: 100%;
                border-collapse: collapse;
            }
            th, td{
                padding: 10px;
                text-align: left;
                vertical-align: top;
            }
            .funnel-step{
                padding: 10px;
                margin: 8px 0;
                border: 1px solid;
            }
            .nav-button, input[type="submit"], select{
                padding: 8px 14px;
                margin-right: 8px;
                margin-bottom: 8px;
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
                    echo "<div class='funnel-step'>";
                    echo $step["label"]." - ".$step["count"];
                    echo "</div>";
                }
            }else{
                echo "<p class='funnel-empty'>No application data available for the funnel chart.</p>";
            }
            ?>
        </div>

        <div class="filter-box">
            <form onsubmit="return false;">
                <label>Filter by Job</label>
                <select name="job_id" id="jobFilter" onchange="filterApplications()" onkeyup="filterApplications()">
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
                <select name="status" id="statusFilter" onchange="filterApplications()" onkeyup="filterApplications()">
                    <option value="">All Status</option>
                    <option value="Submitted" <?php if($selected_status == "Submitted"){ echo "selected"; } ?>>Submitted</option>
                    <option value="Reviewed" <?php if($selected_status == "Reviewed"){ echo "selected"; } ?>>Reviewed</option>
                    <option value="Shortlisted" <?php if($selected_status == "Shortlisted"){ echo "selected"; } ?>>Shortlisted</option>
                    <option value="Rejected" <?php if($selected_status == "Rejected"){ echo "selected"; } ?>>Rejected</option>
                </select>

                <button type="button" class="nav-button" onclick="filterApplications()">Filter</button>
                <button type="button" class="nav-button" onclick="resetApplicationFilters()">Reset</button>
            </form>
        </div>

        <table border="1" id="applicationTable">
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
        <script>
            function filterApplications(){
                var jobFilter = document.getElementById("jobFilter").options[document.getElementById("jobFilter").selectedIndex].text.toLowerCase();
                var statusFilter = document.getElementById("statusFilter").value.toLowerCase();
                var table = document.getElementById("applicationTable");
                var rows = table.getElementsByTagName("tr");

                for(var i = 1; i < rows.length; i++){
                    var cells = rows[i].getElementsByTagName("td");

                    if(cells.length < 6){
                        continue;
                    }

                    var jobTitle = cells[0].innerText.toLowerCase();
                    var statusText = cells[3].innerText.toLowerCase();

                    var jobMatch = jobFilter == "" || jobFilter == "all jobs" || jobTitle == jobFilter;
                    var statusMatch = statusFilter == "" || statusText == statusFilter;

                    if(jobMatch && statusMatch){
                        rows[i].style.display = "";
                    }else{
                        rows[i].style.display = "none";
                    }
                }
            }

            function resetApplicationFilters(){
                document.getElementById("jobFilter").selectedIndex = 0;
                document.getElementById("statusFilter").selectedIndex = 0;
                filterApplications();
            }
        </script>
    </body>
</html>
