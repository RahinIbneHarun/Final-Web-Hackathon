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
                margin: 24px;
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
            .nav-button, .status-button, input[type="submit"]{
                padding: 8px 14px;
                margin-right: 8px;
                margin-bottom: 8px;
            }
            input[type="text"]{
                padding: 8px;
                width: 320px;
                max-width: 100%;
                box-sizing: border-box;
            }
            .search-box{
                margin: 18px 0 12px 0;
            }
            hr{
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
        <div class="search-box">
            <label for="jobSearch">Search Job:</label><br>
            <input type="text" id="jobSearch" placeholder="Search by title, category or status" onkeyup="searchJobs()">
        </div>

        <table border="1" cellpadding="8" cellspacing="0" id="jobTable">
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
                            <button type='button' class='status-button' onclick='toggleStatus(".$row["id"].", this)'>
                                <span class='status-label'>".$status_text."</span>
                            </button>
                          </td>";
                    echo "<td>
                            <button type='button' class='nav-button' onclick=\"window.location.href='job_form.php?id=".$row["id"]."'\">Edit</button>
                            <form method='post' action='../Controller/jobHandler.php' onsubmit='return confirm(\"Delete this job?\")'>
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
            function searchJobs(){
                var input = document.getElementById("jobSearch");
                var filter = input.value.toLowerCase();
                var table = document.getElementById("jobTable");
                var rows = table.getElementsByTagName("tr");

                for(var i = 1; i < rows.length; i++){
                    var rowText = rows[i].innerText.toLowerCase();

                    if(rowText.indexOf(filter) > -1){
                        rows[i].style.display = "";
                    }else{
                        rows[i].style.display = "none";
                    }
                }
            }

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
