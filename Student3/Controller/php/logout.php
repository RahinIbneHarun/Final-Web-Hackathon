<?php
session_start();
session_unset();
session_destroy();
header("Location: ../../../Student1/View/login.php");
exit();
?>