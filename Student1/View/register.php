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
            margin: 24px;
        }
        .register-card{
            max-width: 620px;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="file"]{
            width: 100%;
            box-sizing: border-box;
            padding: 8px;
        }
        .radio-row{
            margin-top: 8px;
        }
        .nav-button, button[type="submit"]{
            padding: 8px 14px;
        }
    </style>
    <script src="../Controller/JS/formValidation.js"></script>
</head>
<body>
    <div class="register-card">
    <h2>Registration</h2>

    <?php if(isset($errors["db"])) echo "<p class='error'>".$errors["db"]."</p>"; ?>

    <form action="../Controller/AuthController.php" method="POST" enctype="multipart/form-data">
        <label>Name:</label><br>
        <input type="text" id="register_name" name="name" value="<?php echo $old["name"] ?? ""; ?>" onkeyup="checkRegisterName()">
        <span class="error" id="registerNameResponse"><?php echo $errors["name"] ?? ""; ?></span><br><br>

        <label>Email:</label><br>
        <input type="email" id="register_email" name="email" value="<?php echo $old["email"] ?? ""; ?>" onkeyup="checkRegisterEmail()">
        <span class="error" id="registerEmailResponse"><?php echo $errors["email"] ?? ""; ?></span><br><br>

        <label>Password (Min 8 chars):</label><br>
        <input type="password" name="password">
        <span class="error"><?php echo $errors["password"] ?? ""; ?></span><br><br>

        <label>Role:</label><br>
        <div class="radio-row">
        <input type="radio" name="role" value="employer" <?php if(($old["role"] ?? "") == "employer"){ echo "checked"; } ?>> Employer
        <input type="radio" name="role" value="seeker" <?php if(($old["role"] ?? "") == "seeker"){ echo "checked"; } ?>> Job Seeker
        </div>
        <br><span class="error"><?php echo $errors["role"] ?? ""; ?></span><br><br>

        <label>Upload Logo (Employer) or Resume PDF (Seeker) - Max 2MB:</label><br>
        <input type="file" name="fileupload">
        <span class="error"><?php echo $errors["file"] ?? ""; ?></span><br><br>

        <button type="submit" name="register">Register</button>
    </form>

    <p><button type="button" class="nav-button" onclick="window.location.href='login.php'">Back to Login</button></p>
    </div>
</body>
</html>
