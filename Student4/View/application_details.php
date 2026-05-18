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

$application_id = $_GET["id"] ?? 0;
$db = new DatabaseConnection();
$connection = $db->openConnection();
$employer_id = $_SESSION["user_id"];
$result = $db->getEmployerApplicationById($connection, $application_id, $employer_id);

if(!$result || $result->num_rows != 1){
    echo "Application not found.";
    exit();
}

$application = $result->fetch_assoc();
$message = $_SESSION["application_message"] ?? "";
unset($_SESSION["application_message"]);
?>
<html>
    <head>
        <title>Application Details</title>
        <style>
            body{
                font-family: Arial, sans-serif;
                margin: 24px;
            }
            .details-box{
                max-width: 900px;
                padding: 14px;
                border: 1px solid;
            }
            .details-box p{
                margin-bottom: 12px;
            }
            .nav-button, input[type="submit"], select{
                padding: 8px 14px;
            }
            hr{
                margin: 18px 0;
            }
        </style>
    </head>
    <body>
        <h1>Application Details</h1>
        <p><button type="button" class="nav-button" onclick="window.location.href='applications_dashboard.php'">Back to Applications Dashboard</button></p>

        <?php
        if($message){
            echo "<p class='message'>".$message."</p>";
        }
        ?>

        <div class="details-box">
            <p><strong>Job Title:</strong> <?php echo $application["title"]; ?></p>
            <p><strong>Applicant Name:</strong> <?php echo $application["seeker_name"]; ?></p>
            <p><strong>Applicant Email:</strong> <?php echo $application["seeker_email"]; ?></p>
            <p><strong>Current Status:</strong> <?php echo $application["status"]; ?></p>
            <p><strong>Applied At:</strong> <?php echo $application["created_at"]; ?></p>
            <p><strong>Location:</strong> <?php echo $application["location"]; ?></p>
            <p><strong>Job Type:</strong> <?php echo $application["job_type"]; ?></p>
            <p><strong>Salary Range:</strong> <?php echo $application["salary_range"]; ?></p>
            <p><strong>Deadline:</strong> <?php echo $application["deadline"]; ?></p>
            <p><strong>Cover Letter:</strong><br><?php echo $application["cover_letter"]; ?></p>

            <?php
            if($application["resume_path"] != ""){
                echo "<p><strong>Resume:</strong> <button type='button' class='nav-button' onclick=\"window.open('../../Student3/View/asset/".$application["resume_path"]."', '_blank')\">Open Resume</button></p>";
            }else{
                echo "<p><strong>Resume:</strong> No resume uploaded.</p>";
            }
            ?>

            <hr>

            <form method="post" action="../Controller/applicationStatusHandler.php">
                <input type="hidden" name="application_id" value="<?php echo $application["id"]; ?>">

                <label><strong>Update Status</strong></label><br><br>
                <select name="status">
                    <option value="Submitted" <?php if($application["status"] == "Submitted"){ echo "selected"; } ?>>Submitted</option>
                    <option value="Reviewed" <?php if($application["status"] == "Reviewed"){ echo "selected"; } ?>>Reviewed</option>
                    <option value="Shortlisted" <?php if($application["status"] == "Shortlisted"){ echo "selected"; } ?>>Shortlisted</option>
                    <option value="Rejected" <?php if($application["status"] == "Rejected"){ echo "selected"; } ?>>Rejected</option>
                </select>
                <input type="submit" value="Update Status">
            </form>
        </div>
    </body>
</html>
