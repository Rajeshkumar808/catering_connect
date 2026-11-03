<?php

$conn = new mysqli(
    "sql104.infinityfree.com",
    "if0_43345082",
    "6oZ9W0jxFI63ktq",
    "if0_43345082_catering_connect"
);

if ($conn->connect_error) {
    die("Database connection failed");
}

$conn->set_charset("utf8mb4");
?>