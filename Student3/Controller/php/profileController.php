<?php
session_start();
include '../../db.php'; 
/** @var mysqli $conn */

if(!isset($_SESSION['isLoggedIn']) || !$_SESSION['isLoggedIn'] || ($_SESSION['role'] ?? '') != 'seeker'){
    header("Location: ../../../Student1/View/login.php");
    exit();
}

if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);
    $experience = intval($_POST['experience'] ?? 0);
    $user_id = intval($_SESSION['user_id']); 

    $resume_name = "";
    if (!empty($_FILES['resume_file']['name'])) {
        $resume_name = time() . '_' . basename($_FILES["resume_file"]["name"]);
        $target_dir = "../../View/asset/";
        $target_file = $target_dir . $resume_name;
        move_uploaded_file($_FILES["resume_file"]["tmp_name"], $target_file);
    }

    $sql = "UPDATE users SET name='$name', email='$email'";
    if($resume_name != "") {
        $resume_path = "/Web%20Tech%20Project/Student3/View/asset/" . $resume_name;
        $sql .= ", file_path='$resume_path'";
    }
    $sql .= " WHERE id='$user_id'";

    if(mysqli_query($conn, $sql)) {
        $check_sql = "SELECT * FROM seeker_profiles WHERE user_id = $user_id";
        $check_result = mysqli_query($conn, $check_sql);
        $headline = $name;

        if(mysqli_num_rows($check_result) > 0){
            $profile_sql = "UPDATE seeker_profiles SET headline='$headline', skills='$skills', years_experience='$experience' WHERE user_id='$user_id'";
        } else {
            $profile_sql = "INSERT INTO seeker_profiles (user_id, headline, skills, years_experience) VALUES ('$user_id', '$headline', '$skills', '$experience')";
        }

        if(mysqli_query($conn, $profile_sql)) {
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            if($resume_name != "") {
                $_SESSION['file_path'] = $resume_path;
            }
<<<<<<< HEAD
<<<<<<< HEAD
            echo "<script>alert('Your Profile is Updated in Database!'); window.location.href='../../View/html/profile.php';</script>";
=======
            echo "<script>alert('Profile Updated in Database!'); window.location.href='../../View/html/profile.php';</script>";
>>>>>>> student3
=======
            echo "<script>alert('Profile Updated in Database!'); window.location.href='../../View/html/profile.php';</script>";
>>>>>>> e9c6cbe51d1eaedfd49f31acee1d609ab41a6423
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
