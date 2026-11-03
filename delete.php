<?php
session_start();
require "db.php";

/* Check login */
if(!isset($_SESSION['user_id'])){
    die("User not logged in ❌");
}

$id = $_SESSION['user_id'];

/* Delete user */
$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $id);

if($stmt->execute()){
    session_destroy(); // logout user
    header("Location: register.php");
    exit();
} else {
    echo "Delete failed ❌";
}
?>