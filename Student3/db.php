<?php
$conn = mysqli_connect("localhost", "root", "", "job_portal_db");
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>