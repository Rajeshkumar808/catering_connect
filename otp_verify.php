<?php
session_start();

if(!isset($_SESSION['pending_user'])){

    header("Location: register.php");
    exit();
}

$error = "";

if(isset($_POST['verify'])){

    $emailOtp = trim($_POST['email_otp']);
    $phoneOtp = trim($_POST['phone_otp']);

    if(

        isset($_SESSION['email_otp']) &&
        isset($_SESSION['phone_otp']) &&

        $emailOtp == $_SESSION['email_otp'] &&

        $phoneOtp == $_SESSION['phone_otp']

    ){

        /* CLEAR OTP */

        unset($_SESSION['email_otp']);
        unset($_SESSION['phone_otp']);

        /* VERIFIED */

        $_SESSION['verified'] = true;

        echo "
        <script>
            alert('OTP Verified Successfully');
            window.location='otp_action.php';
        </script>
        ";

        exit();

    } else {

        $error = "Invalid OTP!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>OTP Verification</title>

<style>

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Poppins,sans-serif;
}

body{

    height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;

    background:
    linear-gradient(rgba(0,0,0,.7),rgba(0,0,0,.8)),
    url('https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg');

    background-size:cover;

    color:white;
}

.box{

    width:380px;

    padding:30px;

    border-radius:20px;

    background:rgba(255,255,255,.12);

    backdrop-filter:blur(15px);

    box-shadow:0 15px 40px rgba(0,0,0,.5);

    text-align:center;
}

h2{

    margin-bottom:20px;
}

input{

    width:100%;

    padding:12px;

    margin:10px 0;

    border:none;

    outline:none;

    border-radius:12px;

    font-size:14px;
}

button{

    width:100%;

    padding:12px;

    border:none;

    border-radius:25px;

    font-size:15px;

    cursor:pointer;

    color:white;

    background:
    linear-gradient(
        90deg,
        rgba(255,95,109,1),
        rgba(255,195,113,1)
    );

    margin-top:10px;
}

button:hover{

    opacity:.9;
}

.error{

    color:#ff4d4d;

    margin-bottom:10px;
}

</style>

</head>

<body>

<div class="box">

    <h2>OTP Verification</h2>

    <?php if($error){ ?>

        <div class="error">

            <?= $error ?>

        </div>

    <?php } ?>

    <form method="POST">

        <input
        type="text"
        name="email_otp"
        placeholder="Enter Email OTP"
        maxlength="6"
        required>

        <input
        type="text"
        name="phone_otp"
        placeholder="Enter Phone OTP"
        maxlength="6"
        required>

        <button
        type="submit"
        name="verify">

        Verify OTP

        </button>

    </form>

</div>

</body>
</html>