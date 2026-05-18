<?php
session_start();

$errors = $_SESSION["reg_errors"] ?? [];
$old = $_SESSION["reg_old"] ?? [];
unset($_SESSION["reg_errors"]);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f8f8f8;
        }
        .error{
            color: red;
            font-size: 14px;
        }
        input[type="text"], input[type="email"], input[type="password"]{
            padding: 8px;
            width: 320px;
        }
        .nav-button{
            padding: 8px 14px;
            border: 1px solid #999;
            background-color: #f4f4f4;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Registration</h2>

    <?php if(isset($errors["db"])) echo "<p class='error'>".$errors["db"]."</p>"; ?>

    <form action="../Controller/AuthController.php" method="POST" enctype="multipart/form-data">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?php echo $old["name"] ?? ""; ?>">
        <span class="error"><?php echo $errors["name"] ?? ""; ?></span><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?php echo $old["email"] ?? ""; ?>">
        <span class="error"><?php echo $errors["email"] ?? ""; ?></span><br><br>

        <label>Password (Min 8 chars):</label><br>
        <input type="password" name="password">
        <span class="error"><?php echo $errors["password"] ?? ""; ?></span><br><br>

        <label>Role:</label><br>
        <input type="radio" name="role" value="employer" <?php if(($old["role"] ?? "") == "employer"){ echo "checked"; } ?>> Employer
        <input type="radio" name="role" value="seeker" <?php if(($old["role"] ?? "") == "seeker"){ echo "checked"; } ?>> Job Seeker
        <br><span class="error"><?php echo $errors["role"] ?? ""; ?></span><br><br>

        <label>Upload Logo (Employer) or Resume PDF (Seeker) - Max 2MB:</label><br>
        <input type="file" name="fileupload">
        <span class="error"><?php echo $errors["file"] ?? ""; ?></span><br><br>

        <button type="submit" name="register">Register</button>
    </form>

    <p><button type="button" class="nav-button" onclick="window.location.href='login.php'">Back to Login</button></p>
</body>
</html>
