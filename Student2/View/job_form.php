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
$categories = $db->getCategories($connection);

$job_id = $_GET["id"] ?? 0;
$isEdit = false;
$job = null;

if($job_id){
    $result = $db->getJobByIdForEmployer($connection, $job_id, $_SESSION["user_id"]);

    if($result && $result->num_rows == 1){
        $job = $result->fetch_assoc();
        $isEdit = true;
    }else{
        echo "You can edit only your own jobs.";
        exit();
    }
}

$errors = $_SESSION["job_errors"] ?? [];
$old = $_SESSION["job_old"] ?? [];
unset($_SESSION["job_errors"]);
unset($_SESSION["job_old"]);

$category_value = $old["category_id"] ?? ($job["category_id"] ?? "");
$title_value = $old["title"] ?? ($job["title"] ?? "");
$description_value = $old["description"] ?? ($job["description"] ?? "");
$requirements_value = $old["requirements"] ?? ($job["requirements"] ?? "");
$salary_range_value = $old["salary_range"] ?? ($job["salary_range"] ?? "");
$location_value = $old["location"] ?? ($job["location"] ?? "");
$job_type_value = $old["job_type"] ?? ($job["job_type"] ?? "");
$deadline_value = $old["deadline"] ?? ($job["deadline"] ?? "");
?>
<html>
    <head>
        <title><?php if($isEdit){ echo "Edit Job"; }else{ echo "Create Job"; } ?></title>
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
            fieldset{
                background-color: #ffffff;
                border: 1px solid #cfe3d3;
                padding: 16px;
                margin-bottom: 16px;
            }
            input[type="text"], input[type="date"], select, textarea{
                width: 100%;
                max-width: 650px;
                padding: 9px;
                border: 1px solid #b8d3bf;
                box-sizing: border-box;
                border-radius: 4px;
            }
            input[type="submit"]{
                padding: 8px 14px;
                border: 1px solid #198754;
                background-color: #198754;
                color: #ffffff;
                cursor: pointer;
                border-radius: 4px;
            }
            input[type="submit"]:hover{
                background-color: #146c43;
            }
        </style>
    </head>
    <body>
        <h1><?php if($isEdit){ echo "Edit Job"; }else{ echo "Create Job"; } ?></h1>
        <a href="employer_dashboard.php">Back to Dashboard</a>

        <?php
        if(isset($errors["general"])){
            echo "<p><strong>".$errors["general"]."</strong></p>";
        }
        ?>

        <form method="post" action="../Controller/jobHandler.php">
            <input type="hidden" name="action" value="<?php if($isEdit){ echo "update"; }else{ echo "create"; } ?>">

            <?php
            if($isEdit){
                echo '<input type="hidden" name="id" value="'.$job_id.'">';
            }
            ?>

            <fieldset>
                <legend>Job Information</legend>

                <p>
                    <label>Title</label><br>
                    <input type="text" name="title" value="<?php echo $title_value; ?>">
                    <br>
                    <?php echo $errors["title"] ?? ""; ?>
                </p>

                <p>
                    <label>Category</label><br>
                    <select name="category_id">
                        <option value="">Select Category</option>
                        <?php
                        if($categories && $categories->num_rows > 0){
                            while($row = $categories->fetch_assoc()){
                                $selected = "";
                                if($category_value == $row["id"]){
                                    $selected = "selected";
                                }
                                echo "<option value='".$row["id"]."' ".$selected.">".$row["name"]."</option>";
                            }
                        }
                        ?>
                    </select>
                    <br>
                    <?php echo $errors["category_id"] ?? ""; ?>
                </p>

                <p>
                    <label>Description</label><br>
                    <textarea name="description" rows="5" cols="60"><?php echo $description_value; ?></textarea>
                    <br>
                    <?php echo $errors["description"] ?? ""; ?>
                </p>

                <p>
                    <label>Requirements</label><br>
                    <textarea name="requirements" rows="5" cols="60"><?php echo $requirements_value; ?></textarea>
                    <br>
                    <?php echo $errors["requirements"] ?? ""; ?>
                </p>

                <p>
                    <label>Salary Range</label><br>
                    <input type="text" name="salary_range" value="<?php echo $salary_range_value; ?>">
                    <br>
                    <?php echo $errors["salary_range"] ?? ""; ?>
                </p>

                <p>
                    <label>Location</label><br>
                    <input type="text" name="location" value="<?php echo $location_value; ?>">
                    <br>
                    <?php echo $errors["location"] ?? ""; ?>
                </p>

                <fieldset>
                    <legend>Job Type</legend>

                    <input type="radio" name="job_type" value="Full-time" <?php if($job_type_value == "Full-time"){ echo "checked"; } ?>> Full-time<br>
                    <input type="radio" name="job_type" value="Part-time" <?php if($job_type_value == "Part-time"){ echo "checked"; } ?>> Part-time<br>
                    <input type="radio" name="job_type" value="Remote" <?php if($job_type_value == "Remote"){ echo "checked"; } ?>> Remote<br>
                    <?php echo $errors["job_type"] ?? ""; ?>
                </fieldset>

                <p>
                    <label>Application Deadline</label><br>
                    <input type="date" name="deadline" value="<?php echo $deadline_value; ?>">
                    <br>
                    <?php echo $errors["deadline"] ?? ""; ?>
                </p>
            </fieldset>

            <input type="submit" value="<?php if($isEdit){ echo "Update Job"; }else{ echo "Create Job"; } ?>">
        </form>
    </body>
</html>
