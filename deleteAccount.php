<?php
session_start();
require "db.php";

if(!isset($_SESSION['email'])){
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$conn->query("DELETE FROM users WHERE email='$email'");
session_destroy();
header("Location: register.php");
exit();
?>