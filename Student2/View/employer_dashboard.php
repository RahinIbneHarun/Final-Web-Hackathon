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

$isAdmin = false;
if((isset($_SESSION["email"]) && $_SESSION["email"] == "admin@jobportal.test") || (isset($_SESSION["role"]) && $_SESSION["role"] == "admin")){
    $isAdmin = true;
}

$db = new DatabaseConnection();
$connection = $db->openConnection();
$jobs = $db->getEmployerJobs($connection, $_SESSION["user_id"]);

$message = $_SESSION["job_message"] ?? "";
unset($_SESSION["job_message"]);
?>
<html>
    <head>
        <title>Employer Job Dashboard</title>
        <style>
            body{
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 24px;
                background-color: #eef6f0;
                color: #1f3527;
            }
            h1{
                color: #14532d;
                margin-top: 0;
            }
            a{
                color: #166534;
                text-decoration: none;
                font-weight: bold;
            }
            a:hover{
                text-decoration: underline;
            }
            table{
                border-collapse: collapse;
                width: 100%;
                background-color: #ffffff;
                border: 1px solid #cfe3d3;
            }
            th, td{
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #dbe9de;
            }
            th{
                background-color: #dff0e3;
                color: #14532d;
            }
            button, input[type="submit"]{
                padding: 8px 14px;
                border: 1px solid #198754;
                background-color: #198754;
                color: #ffffff;
                cursor: pointer;
                border-radius: 4px;
            }
            button:hover, input[type="submit"]:hover{
                background-color: #146c43;
            }
            .nav-button{
                margin-right: 8px;
                margin-bottom: 8px;
            }
            .status-button{
                min-width: 90px;
                border-color: #198754;
            }
            .status-label{
                color: #ffffff;
                font-weight: bold;
            }
            form{
                margin: 0;
            }
            #toggle_message{
                color: #166534;
                font-weight: bold;
            }
            hr{
                border: none;
                border-top: 1px solid #cfe3d3;
                margin: 18px 0;
            }
        </style>
    </head>
    <body>
        <h1>Employer Job Dashboard</h1>
        <p>Hello, <?php echo $_SESSION["name"] ?? "Employer"; ?></p>
        <button type="button" class="nav-button" onclick="window.location.href='job_form.php'">Post New Job</button>
        <button type="button" class="nav-button" onclick="window.location.href='../../Student4/View/applications_dashboard.php'">Applications Dashboard</button>
        <?php
        if($isAdmin){
            echo "<button type='button' class='nav-button' onclick=\"window.location.href='category_panel.php'\">Manage Categories</button>";
        }
        ?>
        <button type="button" class="nav-button" onclick="window.location.href='../../Student1/Controller/logout.php'">Logout</button>

        <hr>

        <?php
        if($message){
            echo "<p><strong>".$message."</strong></p>";
        }
        ?>

        <p id="toggle_message"></p>

        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Deadline</th>
                <th>Application Count</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php
            if($jobs && $jobs->num_rows > 0){
                while($row = $jobs->fetch_assoc()){
                    $status_text = "Closed";
                    $status_background = "#dc3545";

                    if($row["status"] == "active"){
                        $status_text = "Active";
                        $status_background = "#198754";
                    }

                    echo "<tr>";
                    echo "<td>".$row["title"]."</td>";
                    echo "<td>".$row["category_name"]."</td>";
                    echo "<td>".$row["deadline"]."</td>";
                    echo "<td>".$row["application_count"]."</td>";
                    echo "<td>
                            <button type='button' class='status-button' style='background-color: ".$status_background."; border-color: ".$status_background.";' onclick='toggleStatus(".$row["id"].", this)'>
                                <span class='status-label'>".$status_text."</span>
                            </button>
                          </td>";
                    echo "<td>
                            <button type='button' class='nav-button' onclick=\"window.location.href='job_form.php?id=".$row["id"]."'\">Edit</button>
                            <form method='post' action='../Controller/jobHandler.php' style='display:inline;' onsubmit='return confirm(\"Delete this job?\")'>
                                <input type='hidden' name='action' value='delete'>
                                <input type='hidden' name='id' value='".$row["id"]."'>
                                <input type='submit' value='Delete'>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            }else{
                echo "<tr><td colspan='6'>No jobs posted yet.</td></tr>";
            }
            ?>
        </table>

        <script>
            function toggleStatus(jobId, button){
                var request = new XMLHttpRequest();
                var label = button.querySelector(".status-label");
                var message = document.getElementById("toggle_message");

                request.open("POST", "../Controller/jobStatusToggle.php?id=" + jobId, true);

                request.onreadystatechange = function(){
                    if(request.readyState === 4){
                        if(request.status === 200){
                            var response = JSON.parse(request.responseText);

                            if(response.success){
                                label.innerHTML = response.label;
                                label.style.color = "white";
                                if(response.color === "green"){
                                    button.style.backgroundColor = "#198754";
                                    button.style.borderColor = "#198754";
                                }else{
                                    button.style.backgroundColor = "#dc3545";
                                    button.style.borderColor = "#dc3545";
                                }
                                message.innerHTML = "Job status updated successfully.";
                            }else{
                                message.innerHTML = response.message;
                            }
                        }else{
                            message.innerHTML = "Job status update failed.";
                        }
                    }
                };

                request.send();
            }
        </script>
    </body>
</html>
