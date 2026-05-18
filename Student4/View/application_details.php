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
                max-width: 980px;
                margin: 0 auto;
                padding: 24px;
                background-color: #eef3ef;
                color: #1f2937;
            }
            h1{
                margin-bottom: 12px;
            }
            .details-box{
                background-color: #ffffff;
                border: 1px solid #d7dfd8;
                padding: 24px;
                max-width: 900px;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }
            .message{
                color: #166534;
                font-weight: bold;
                padding: 10px 14px;
                background-color: #ecfdf3;
                border: 1px solid #bbf7d0;
                border-radius: 8px;
            }
            select, input[type="submit"]{
                padding: 9px 10px;
                border: 1px solid #b8c9bc;
                border-radius: 6px;
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
                border-radius: 6px;
            }
            .nav-button:hover{
                background-color: #146c43;
            }
            a{
                color: #1a5fb4;
                text-decoration: none;
            }
            a:hover{
                text-decoration: underline;
            }
            .details-box p{
                margin: 0 0 14px 0;
                line-height: 1.6;
            }
            .details-box strong{
                color: #14532d;
            }
            hr{
                border: none;
                border-top: 1px solid #d7dfd8;
                margin: 22px 0;
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
