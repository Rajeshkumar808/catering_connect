
<?php
session_start();

require "mailer.php";

if (isset($_POST['email'])) {

    $email = strtolower(trim($_POST['email']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email";
        exit;
    }

    $otp = rand(100000, 999999);

    if (sendEmailOTP($email, $otp)) {

        // Save OTP and associated email in session
        $_SESSION['email_otp'] = (string)$otp;
        $_SESSION['email_otp_email'] = $email;

        echo "success";

    } else {
        echo "failed";
    }

} else {
    echo "Email required";
}
?>
