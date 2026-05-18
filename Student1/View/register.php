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
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            color: #1f2937;
        }
        .register-card{
            width: 100%;
            max-width: 620px;
            background-color: #ffffff;
            padding: 28px;
            border: 1px solid #dbe4f0;
            border-radius: 14px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
        }
        .error{
            color: #dc2626;
            font-size: 14px;
        }
        input[type="text"], input[type="email"], input[type="password"]{
            padding: 10px;
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-sizing: border-box;
            margin-top: 6px;
        }
        input[type="file"]{
            margin-top: 6px;
        }
        button[type="submit"]{
            padding: 10px 16px;
            border: 1px solid #0f766e;
            background-color: #0f766e;
            color: #ffffff;
            cursor: pointer;
            border-radius: 8px;
        }
        button[type="submit"]:hover{
            background-color: #0d5f59;
        }
        .nav-button{
            padding: 8px 14px;
            border: 1px solid #0f766e;
            background-color: #0f766e;
            color: #ffffff;
            cursor: pointer;
            border-radius: 8px;
        }
        .nav-button:hover{
            background-color: #0d5f59;
        }
        h2{
            margin-top: 0;
            color: #0f172a;
        }
        .radio-row{
            margin-top: 6px;
        }
    </style>
</head>
<body>
    <div class="register-card">
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
