<?php
session_start();

/* 🚫 Not logged in */
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$name  = $_SESSION['user'];
$email = $_SESSION['email'];
$phone = $_SESSION['phone'];
?>
<!DOCTYPE html>
<html>
<head>
<title>My Account</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
  box-sizing:border-box;
  margin:0;
  padding:0;
  font-family:"Poppins",sans-serif;
}

body{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;

  background:
  linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.85)),
  url("https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg")
  no-repeat center center fixed;
  background-size:cover;
}

/* MAIN CARD */

.container{
  width:90%;
  max-width:500px;
  
  background:rgba(255,255,255,.12);
  backdrop-filter:blur(18px);
  
  padding:35px;
  border-radius:25px;
  box-shadow:0 25px 60px rgba(0,0,0,.5);
}

/* TITLE */

h2{
  color:white;
  text-align:center;
  margin-bottom:25px;
}

/* PROFILE INFO */

.info{
  background:rgba(0,0,0,.4);
  padding:15px;
  border-radius:15px;
  margin-bottom:15px;
  color:#fff;
}

.label{
  font-size:13px;
  color:#ccc;
}

.value{
  font-size:18px;
  font-weight:600;
  margin-top:4px;
}

/* BACK BUTTON */

.backBtn{
  display:block;
  margin-top:20px;
  text-align:center;
  
  background:linear-gradient(90deg,#ff5f6d,#ffc371);
  color:white;
  
  padding:12px;
  border-radius:25px;
  text-decoration:none;
  font-weight:500;
  
  transition:.3s ease;
}

.backBtn:hover{
  color:black;
  background:radial-gradient(circle,#d12332,#ff0505);
  box-shadow:
    0 0 10px rgb(202,202,201),
    0 0 20px rgb(204,201,201);
  transform:translateY(-2px);
}

</style>
</head>

<body>

<div class="container">

<h2>👤 My Account</h2>

<div class="info">
  <div class="label">Name</div>
  <div class="value"><?= htmlspecialchars($name) ?></div>
</div>

<div class="info">
  <div class="label">Email</div>
  <div class="value"><?= htmlspecialchars($email) ?></div>
</div>

<div class="info">
  <div class="label">Phone Number</div>
  <div class="value"><?= htmlspecialchars($phone) ?></div>
</div>

<a class="backBtn" href="index.php">⬅ Back</a>

</div>

</body>
</html>