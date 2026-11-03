
<?php
session_start();
require "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["phone"])) {
    exit("Invalid request");
}

$phone = trim($_POST["phone"]);

if (!preg_match("/^[0-9]{10}$/", $phone)) {
    exit("Invalid phone number");
}

// Generate OTP
$otp = random_int(100000, 999999);

// Store OTP in session
$_SESSION["phone_otp"] = (string)$otp;
$_SESSION["phone_otp_time"] = time();

// Recipient number
$userNumber = "91" . $phone;

// SMS message
$message = "Your Catering Connect OTP is: " . $otp;

// Insert SMS into queue
$stmt = $conn->prepare(
    "INSERT INTO sms_queue (phone, message, status)
     VALUES (?, ?, 'pending')"
);

$stmt->bind_param("ss", $userNumber, $message);

if ($stmt->execute()) {
    echo "OTP request created";
} else {
    echo "Failed to create SMS request";
}

$stmt->close();
?>
