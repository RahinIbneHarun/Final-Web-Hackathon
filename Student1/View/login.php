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
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #eef2ff;
            color: #1e293b;
        }
        .login-card{
            width: 100%;
            max-width: 560px;
            background-color: #ffffff;
            padding: 28px;
            border: 1px solid #d8def5;
            border-radius: 14px;
            box-shadow: 0 10px 28px rgba(37, 99, 235, 0.10);
        }
        table{
            width: 100%;
            background-color: #fff;
            border-collapse: collapse;
            border: 1px solid #d8def5;
        }
        td{
            padding: 12px;
            vertical-align: top;
        }
        input{
            padding: 10px;
            width: 100%;
            border: 1px solid #c7d2fe;
            border-radius: 8px;
            box-sizing: border-box;
        }
        input[type="submit"]{
            background-color: #2563eb;
            border: 1px solid #2563eb;
            color: #ffffff;
            cursor: pointer;
        }
        input[type="submit"]:hover{
            background-color: #1d4ed8;
        }
        .message{
            color: #dc2626;
            margin: 0;
        }
        .success{
            color: #1d4ed8;
            font-weight: bold;
        }
        .nav-button{
            padding: 8px 14px;
            border: 1px solid #2563eb;
            background-color: #2563eb;
            color: #ffffff;
            cursor: pointer;
            border-radius: 8px;
        }
        .nav-button:hover{
            background-color: #1d4ed8;
        }
        h2{
            margin-top: 0;
            margin-bottom: 18px;
            color: #1e3a8a;
        }
    </style>
</head>
<body>
    <div class="login-card">
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
    </div>
</body>
</html>
