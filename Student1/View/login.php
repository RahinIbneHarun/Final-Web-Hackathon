<?php
session_start();

if(isset($_SESSION["user_id"])){
    if (($_SESSION["role"] ?? "") == "employer") {
        header("Location: employer_dashboard.php");
    } else {
        header("Location: job_board.php");
    }
    exit();
}

$loginErr = $_SESSION["loginErr"] ?? "";
$successMsg = $_GET["success"] ?? "";
$oldEmail = $_SESSION["login_old_email"] ?? "";

unset($_SESSION["loginErr"]);
unset($_SESSION["login_old_email"]);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f8f8f8;
        }
        table{
            background-color: #fff;
            border-collapse: collapse;
        }
        td{
            padding: 8px;
        }
        input{
            padding: 8px;
            width: 280px;
        }
        .message{
            color: red;
        }
        .success{
            color: green;
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
    <h2>Login</h2>

    <?php if($successMsg) echo "<p class='success'>$successMsg</p>"; ?>

    <form method="post" action="../Controller/AuthController.php">
        <table border="1">
            <tr>
                <td>Email</td>
                <td><input type="email" name="email" value="<?php echo $oldEmail; ?>" placeholder="Enter your email" required /></td>
            </tr>

            <tr>
                <td>Password</td>
                <td><input type="password" name="password" placeholder="Enter your password" required /></td>
            </tr>

            <tr>
                <td></td>
                <td><p class="message"><?php echo $loginErr; ?></p></td>
            </tr>

            <tr>
                <td></td>
                <td>
                    Don't have an account?
                    <button type="button" class="nav-button" onclick="window.location.href='register.php'">Register Here</button>
                </td>
            </tr>

            <tr>
                <td></td>
                <td><input type="submit" name="login" value="Login" /></td>
            </tr>
        </table>
    </form>
</body>
</html>
