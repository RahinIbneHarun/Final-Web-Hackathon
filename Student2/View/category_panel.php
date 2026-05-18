<?php
include "../Model/DatabaseConnection.php";
session_start();

if(!isset($_SESSION["isLoggedIn"]) || !$_SESSION["isLoggedIn"]){
    header("Location: ../../Student1/View/login.php");
    exit();
}

$isAdmin = false;
if((isset($_SESSION["email"]) && $_SESSION["email"] == "admin@jobportal.test") || (isset($_SESSION["role"]) && $_SESSION["role"] == "admin")){
    $isAdmin = true;
}

if(!$isAdmin){
    header("Location: employer_dashboard.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();
$categories = $db->getCategories($connection);

$edit_id = $_GET["edit"] ?? 0;
$isEdit = false;
$editCategory = null;

if($edit_id){
    $result = $db->getCategoryById($connection, $edit_id);

    if($result && $result->num_rows == 1){
        $editCategory = $result->fetch_assoc();
        $isEdit = true;
    }
}

$message = $_SESSION["category_message"] ?? "";
$name_error = $_SESSION["category_name_error"] ?? "";
$old_name = $_SESSION["category_old_name"] ?? "";

unset($_SESSION["category_message"]);
unset($_SESSION["category_name_error"]);
unset($_SESSION["category_old_name"]);

$name_value = $old_name;
if($name_value == "" && $isEdit){
    $name_value = $editCategory["name"];
}
?>
<html>
    <head>
        <title>Admin Category Panel</title>
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
            input[type="text"]{
                width: 100%;
                max-width: 420px;
                box-sizing: border-box;
                padding: 8px;
            }
            .nav-button, input[type="submit"]{
                padding: 8px 14px;
                margin-right: 8px;
                margin-bottom: 8px;
            }
            hr{
                margin: 18px 0;
            }
        </style>
    </head>
    <body>
        <h1>Admin Category Panel</h1>
        <button type="button" class="nav-button" onclick="window.location.href='employer_dashboard.php'">Back to Dashboard</button>
        <button type="button" class="nav-button" onclick="window.location.href='../../Student1/Controller/logout.php'">Logout</button>

        <hr>

        <?php
        if($message){
            echo "<p><strong>".$message."</strong></p>";
        }
        ?>

        <h2><?php if($isEdit){ echo "Edit Category"; }else{ echo "Create Category"; } ?></h2>

        <form method="post" action="../Controller/categoryHandler.php">
            <input type="hidden" name="action" value="<?php if($isEdit){ echo "update"; }else{ echo "create"; } ?>">

            <?php
            if($isEdit){
                echo '<input type="hidden" name="id" value="'.$editCategory["id"].'">';
            }
            ?>

            <label>Category Name</label><br>
            <input type="text" name="name" value="<?php echo $name_value; ?>">
            <br>
            <?php echo $name_error; ?>
            <br><br>
            <input type="submit" value="<?php if($isEdit){ echo "Update Category"; }else{ echo "Create Category"; } ?>">
            <button type="button" class="nav-button" onclick="window.location.href='category_panel.php'">Reset</button>
        </form>

        <h2>All Categories</h2>

        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Name</th>
                <th>Jobs Using Category</th>
                <th>Action</th>
            </tr>

            <?php
            if($categories && $categories->num_rows > 0){
                while($row = $categories->fetch_assoc()){
                    echo "<tr>";
                    echo "<td>".$row["name"]."</td>";
                    echo "<td>".$row["job_count"]."</td>";
                    echo "<td>
                            <button type='button' class='nav-button' onclick=\"window.location.href='category_panel.php?edit=".$row["id"]."'\">Edit</button>
                            <form method='post' action='../Controller/categoryHandler.php' onsubmit='return confirm(\"Delete this category?\")'>
                                <input type='hidden' name='action' value='delete'>
                                <input type='hidden' name='id' value='".$row["id"]."'>
                                <input type='submit' value='Delete'>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            }else{
                echo "<tr><td colspan='3'>No categories found.</td></tr>";
            }
            ?>
        </table>
    </body>
</html>
