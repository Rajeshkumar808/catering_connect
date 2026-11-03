
<?php
session_start();
require "db.php";

$success = $error = null;

// Organizer login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle event submission
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $event_name = trim($_POST['eventName'] ?? '');
    $location   = trim($_POST['eventLocation'] ?? '');
    $date       = $_POST['eventDate'] ?? '';
    $time       = $_POST['eventTime'] ?? '';
    $workers    = (int)($_POST['eventWorkers'] ?? 0);
    $caterer    = trim($_POST['catererName'] ?? '');
    $contact    = trim($_POST['catererContact'] ?? '');
    $caterer_id = (int)$_SESSION['user_id'];

    // Server-side validation
    if ($workers < 1) {

        $error = "Workers must be at least 1";

    } elseif (!preg_match("/^[0-9]{10}$/", $contact)) {

        $error = "Contact must be exactly 10 digits";

    } else {

        // Insert event
        $stmt = $conn->prepare("
            INSERT INTO events
            (
                event_name,
                location,
                date,
                time,
                workers_needed,
                rem_workers,
                caterer_name,
                caterer_contact,
                caterer_id
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            die("Database error: " . $conn->error);
        }

        $stmt->bind_param(
            "ssssiissi",
            $event_name,
            $location,
            $date,
            $time,
            $workers,
            $workers,
            $caterer,
            $contact,
            $caterer_id
        );

        if ($stmt->execute()) {

            // Save recent event ID
            $_SESSION['recentEventId'] = $conn->insert_id;

            // Redirect after successful insertion
            $stmt->close();

            header("Location: addEvent.php?success=1");
            exit();

        } else {

            $error = "Failed to add event: " . $stmt->error;

        }

        $stmt->close();
    }
}

// Success message after redirect
if (isset($_GET['success'])) {
    $success = "Event successfully added!";
}

// Get user profile picture
$stmt = $conn->prepare("
    SELECT profile_pic
    FROM users
    WHERE id = ?
");

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();

$result = $stmt->get_result();
$userData = $result->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Event | Catering Connect</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
  box-sizing:border-box;
  font-family:"Poppins",sans-serif;
  margin:0;
  padding:0;
}

body{
  min-height:100vh;
  display:flex;
  overflow:hidden;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  background:transparent;
  padding-bottom:60px;
}

/* Background after load */
body.loaded{
  background:
linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.85)),
  url("https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg")
  no-repeat center center fixed;
  background-size:cover;
}

/* HEADER */
header{
  color:white;
  padding:15px;
  text-align:center;
  font-size:2rem;
  position:fixed;
  top:0;
  left:0;
  width:100%;
  z-index:1000;
}

header.show{
  opacity:1;
  transform:none;
}

/* MAIN CONTAINER */
main{
  width: 90%;
  max-width: 500px;
  margin-top: 100px;

  backdrop-filter: blur(14px);
  background: rgba(255,255,255,.10);

  border-radius: 18px;
  box-shadow: 0 15px 40px rgba(0,0,0,.5);

  height: 75vh;
  overflow: hidden; /* ❌ stop full scroll */
}

/* FIXED HEADER */
.form-header{
  padding: 15px;
  text-align: center;
  border-bottom: 1px solid rgba(255,255,255,0.2);
}

.form-header h2{
  color: white;
}

.form-header p{
  color: #ccc;
  font-size: 14px;
}

/* SCROLL ONLY THIS */
.form-body{
  height: calc(75vh - 80px);
  overflow-y: auto;
  padding: 15px;
}

.form-body::-webkit-scrollbar{
  display: none;
}

main::-webkit-scrollbar{ display:none; }

h2{
  color:white;
  text-align:center;
  margin-bottom:10px;
}

p{ color:#ccc; }

/* INPUTS */
input,select{
  width:100%;
  padding:12px;
  margin:8px 0;
  border-radius:10px;
  border:none;
  outline:none;
  transition:.3s ease;
}

input:hover,
select:hover {
  border: none;
  box-shadow: none;
  transform: none;
}

input:focus,
select:focus {
  outline: none;
  border: none;
  box-shadow: none;
  transform: none;
  transition: none;
}
/* BUTTON */
/* Common button style */
button,
.viewBtn,
.backBtn,
.close-btn {
  width: 100%;
  height: 45px;           /* ✅ fixed height */
  display: flex;
  align-items: center;
  justify-content: center;

  border-radius: 25px;
  font-size: 15px;
  font-weight: 500;

  text-decoration: none;
  cursor: pointer;
}
button,
.viewBtn,
.backBtn{
  background: linear-gradient(90deg,#ff5f6d,#ffc371);
  color: white;
  border: none;
  transition: .3s ease;
}

button:hover,
.viewBtn:hover,
.backBtn:hover{
  color: black;
  background: radial-gradient(circle,#d12332,#ff0505);
  transform: translateY(-2px);
}

/* SUCCESS / ERROR */
.success-box{
  background:#4BB543;
  color:white;
  padding:12px;
  border-radius:10px;
  text-align:center;
  margin-bottom:15px;
  font-weight:500;
  animation:fadeIn .5s ease;
}

.error{
  color:#ff5f6d;
  text-align:center;
  margin-bottom:10px;
  font-weight:bold;
}

@keyframes fadeIn{
  from{opacity:0; transform:translateY(-10px);}
  to{opacity:1; transform:translateY(0);}
}

/* NAV LINKS */
.nav-links{
  display:flex;
  flex-direction:column;
  gap:10px;
  margin-top:20px;
}

/* BUTTON STYLE */
.viewBtn,
.backBtn,
button{

  display:block;
  width:100%;

  padding:13px;

  border:none;
  border-radius:30px;

  font-size:1rem;
  font-weight:600;

  color:white;
  text-align:center;
  text-decoration:none;

  cursor:pointer;

  background:linear-gradient(135deg,#ff6a00,#ee0979);

  box-shadow:
    0 5px 15px rgba(238,9,121,.25);

  transition:
    transform .25s ease,
    box-shadow .25s ease,
    background .25s ease;
}

/* HOVER EFFECT */
.viewBtn:hover,
.backBtn:hover,
button:hover{

  background:linear-gradient(135deg,#ff512f,#dd2476);

  transform:translateY(-3px) scale(1.01);

  box-shadow:
    0 10px 22px rgba(221,36,118,.35);
}

/* CLICK EFFECT */
.viewBtn:active,
.backBtn:active,
button:active{

  transform:scale(.98);
}

/* FORM */
form{
  display:flex;
  flex-direction:column;
  gap:6px;
}

/* ===== PROFILE PANEL ===== */

.avatar{
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 100000;   /* MUST be higher than panel */
    
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: #2a2a2a;
    
    display: flex;
    align-items: center;
    justify-content: center;
    
    font-size: 26px;
    color: #a855f7;
    cursor: pointer;
    
    box-shadow: 0 0 25px rgba(0,0,0,.8);
}

.avatar:hover{ transform: scale(1.08); }

.profile-panel{
    position: fixed;
    top: 0;
    right: -380px;
    width: 320px;
    height: 100%;
    background: #000;
    padding: 25px;
    transition: right .35s ease;
    z-index: 9000;
}

.profile-panel.show{ right: 0; }

.profile-header{ margin-bottom:25px; }

.profile-header h2{
    font-size:28px;
    margin-bottom:20px;
    margin-bottom: 8px;
}



.username{
    
    color:#aaa;
    margin-top: 30px;
}

/* Profile cards */

.profile-card{
    display:block;
    background:linear-gradient(145deg,#1f1f1f,#151515);
    
    padding:12px 14px;   /* ⭐ smaller height */
    margin:12px 0;       /* less spacing */
    
    border-radius:14px;
    color:#ddd;
    text-decoration:none;
    
    font-size:15px;      /* ⭐ smaller text */
    
    transition:transform .2s;
}

.profile-card:hover{ transform:translateX(6px); }

.profile-card.danger{ color:#ff5252; }
.profile-card.logout{ color:#ffb74d; }
.close-btn{
  display:block;
  margin:20px auto 0 auto;
  width:80%;
  padding:12px;
  border-radius:25px;

  background:transparent;        /* 🔥 transparent */
  color: #ffffff;;
  border:2px solid #565252;        /* 🔥 white border */

  font-size:15px;
  cursor:pointer;
  transition:all .3s ease;
  text-align:center;
}

/* ✨ Hover Glow Effect */
.close-btn:hover{
  background:rgba(255,255,255,0.1);
  color: #ffffff;;
  box-shadow:none;   /* ❌ removes glow */
  transform:none;    /* ❌ removes zoom */
}



.avatar{
  position:fixed;
  top:20px;
  right:20px;
  width:55px;
  height:55px;
  border-radius:50%;
  overflow:hidden;              /* 🔥 IMPORTANT */
  cursor:pointer;
  z-index:99999;
  border:2px solid white;       /* optional nice border */
}

/* Fix image inside avatar */
.avatar-img{
  width:100%;
  height:100%;
  object-fit:cover;             /* 🔥 keeps image perfect */
  display:block;
}
</style>
   
</head>
<body>

<header id="mainHeader">🍽 Catering Connect</header>



<!-- ⭐ SMALL CLICK ICON -->
<div class="avatar" onclick="toggleProfile()">
    <?php if(!empty($userData['profile_pic'])): ?>
        <img src="uploads/<?= $userData['profile_pic'] ?>" class="avatar-img">
    <?php else: ?>
        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" class="avatar-img">
    <?php endif; ?>
</div>
<!-- ⭐ SLIDE PANEL -->
<div class="profile-panel" id="profilePanel">

    <div class="profile-header">
        <h2>My Profile</h2>

        

      <p class="username"
   style="color:white; font-size:20px; font-weight:700; text-align:center; text-transform: uppercase;">
   <?= htmlspecialchars($_SESSION['user']) ?>
</p>
    </div>

    <hr>

    <a href="account.php" class="profile-card">👤 My Account</a>

<a href="myBookings.php" class="profile-card">My Events</a>

<a href="help.php" class="profile-card">❓ Help</a>
    <a href="delete.php"
   class="profile-card danger"
   onclick="return confirmDelete()">

   🗑 Delete Account

</a>
    <a href="logout.php" class="profile-card logout">🚪 Logout</a>
<button class="close-btn" onclick="toggleProfile()">❌ Close</button>
</div>
<main>

    <!-- 🔒 FIXED HEADER -->
    <div class="form-header">
        <h2>Organizer Dashboard</h2>
        <p>Fill in the details to host a new event</p>
    </div>

    <!-- 🔽 SCROLLABLE AREA -->
    <div class="form-body">

        <?php if($success): ?>
        <div class="success-box">
            ✅ <?= $success ?>
        </div>
        <?php endif; ?>

        <?php if($error) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">
            <select name="eventName" required>
                <option value="">Select Event Type</option>
                <option>Wedding</option>
                <option>Birthday</option>
                <option>Baby Shower</option>
                <option>Temple Event</option>
                <option>Corporate</option>
                <option>Festival</option>
                <option>House Warming</option>
                <option>Others</option>
            </select>

            <select name="eventLocation" required>
                <option value="">Select Location</option>
                <option>Udupi</option>
                <option>Kapu</option>
                <option>Bynduru</option>
                <option>Karkala</option>
                <option>Kundapura</option>
                <option>Hebri</option>
                <option>Brahmavara</option>
                <option>Mangaluru</option>
                <option>Ullal</option>
                <option>Mulki</option>
                <option>Moodbidri</option>
                <option>Bantwala</option>
                <option>Belathangadi</option>
                <option>Putturu</option>
                <option>Sulya</option>
                <option>Kadaba</option>
            </select>

            <input type="date" name="eventDate" id="eventDate" required>
          <input type="time" name="eventTime" id="eventTime" required>
            <input type="number" name="eventWorkers" placeholder="Workers Needed" min="1" required>
            <input type="text" name="catererName" placeholder="Caterer Name" required>
            <input type="tel" name="catererContact" placeholder="Contact (10 digits)" pattern="[0-9]{10}" maxlength="10" required>

            <button type="submit">Add Event</button>
        </form>

        <div class="nav-links">
            <?php if($success): ?>
                <a class="viewBtn" href="viewRecentEvent.php">👁 View Recent Event</a>
            <?php endif; ?>
            <a class="backBtn" href="index.php">⬅ Back to Home</a>
        </div>

    </div>

</main>
<script>

/* LOAD BACKGROUND */
window.onload = function(){

    document.body.classList.add("loaded");
};

const eventDate = document.getElementById("eventDate");
const eventTime = document.getElementById("eventTime");

/* CURRENT TIME */
let now = new Date();

/* ADD 5 HOURS */
let future = new Date(now.getTime() + 5 * 60 * 60 * 1000);

/* FORMAT DATE */
let year = future.getFullYear();

let month = String(future.getMonth() + 1).padStart(2,'0');

let day = String(future.getDate()).padStart(2,'0');

let minDate = year + "-" + month + "-" + day;

/* FORMAT TIME */
let hours = String(future.getHours()).padStart(2,'0');

let minutes = String(future.getMinutes()).padStart(2,'0');

let minTime = hours + ":" + minutes;

/* SET MIN DATE */
eventDate.min = minDate;

/* DATE CHANGE */
eventDate.addEventListener("change", function(){

    if(this.value === minDate){

        eventTime.min = minTime;

    } else {

        eventTime.min = "00:00";
    }

    eventTime.value = "";
});

/* PROFILE PANEL */
function toggleProfile(){

    document.getElementById("profilePanel")
            .classList.toggle("show");
}

/* CLOSE PANEL */
window.onclick = function(e){

    if(!e.target.closest('.profile-panel') &&
       !e.target.closest('.avatar')){

        document.getElementById("profilePanel")
                .classList.remove("show");
    }
}
/* DELETE ACCOUNT ALERT */

function confirmDelete(){

    return confirm(
        "Are you sure you want to delete your account?"
    );
}
</script>
</body>
</html>
