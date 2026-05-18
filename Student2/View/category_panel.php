<?php
include "../Model/DatabaseConnection.php";
session_start();

if(!isset($_SESSION["isLoggedIn"]) || !$_SESSION["isLoggedIn"]){
    echo "Please login first.";
    exit();
}

$isAdmin = false;
if((isset($_SESSION["email"]) && $_SESSION["email"] == "admin@jobportal.test") || (isset($_SESSION["role"]) && $_SESSION["role"] == "admin")){
    $isAdmin = true;
}

if(!$isAdmin){
    echo "Only admin can use this page.";
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
                margin: 20px;
                background-color: #f8f8f8;
                color: #222;
            }
            a{
                color: #1a5fb4;
                text-decoration: none;
            }
            a:hover{
                text-decoration: underline;
            }
            input[type="text"], input[type="submit"]{
                padding: 8px;
                border: 1px solid #bbb;
            }
            table{
                border-collapse: collapse;
                width: 100%;
                background-color: #fff;
            }
            th, td{
                padding: 10px;
                text-align: left;
            }
            th{
                background-color: #efefef;
            }
            form{
                margin: 0;
            }
        </style>
    </head>
    <body>
        <h1>Admin Category Panel</h1>
        <a href="employer_dashboard.php">Back to Dashboard</a>

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
            <a href="category_panel.php">Reset</a>
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
                            <a href='category_panel.php?edit=".$row["id"]."'>Edit</a>
                            <form method='post' action='../Controller/categoryHandler.php' style='display:inline;' onsubmit='return confirm(\"Delete this category?\")'>
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
