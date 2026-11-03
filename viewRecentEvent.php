<?php
session_start();
require "db.php";

$id = $_SESSION['recentEventId'] ?? '';

if(empty($id)){
    die("No recent event found");
}

/* Secure fetch */
$stmt = $conn->prepare("SELECT * FROM events WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    die("Event not found");
}
/* 🔴 DELETE EVENT */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {

    $delete_id = $_POST['delete_id'];

    $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        unset($_SESSION['recentEventId']); // remove from session
        echo "<script>alert('Event deleted successfully'); window.location='addEvent.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to delete event');</script>";
    }
}
$event = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Recent Event</title>

<style>
/* 🔥 PASTE YOUR SAME CSS HERE (no change needed) */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Poppins,sans-serif;
}

body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:30px;
    background:
       linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.85)),
        url("https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg")
        no-repeat center center/cover;
}

.results-container{
    width:700px;
    max-height:85vh;
    padding:30px;
    border-radius:30px;
    backdrop-filter: blur(18px);
    background:rgba(255,255,255,.12);
    color:white;
    text-align:center;
    box-shadow:0 10px 40px rgba(0,0,0,.6);
    display:flex;
    flex-direction:column;
}

h2{
    margin-bottom:25px;
    font-size:28px;
}

.events-wrapper{
    margin-bottom:20px;
}

.event-card{
    background:#f2f2f2;
    color:#333;
    padding:20px;
    border-radius:20px;
    margin-bottom:20px;
    text-align:left;
    box-shadow:0 5px 15px rgba(0,0,0,.3);
}

.event-title{
    font-size:22px;
    font-weight:bold;
    color:#ff5f6d;
    margin-bottom:15px;
}

.event-info{
    margin:8px 0;
    font-size:16px;
}

.back-btn{
    display:inline-block;
    margin-bottom:15px;
    padding:12px 28px;   /* ⬅ increased */
    border-radius:25px;  /* ⬅ slightly bigger curve */
    text-decoration:none;
    color:white;
    background:linear-gradient(90deg,#ff5f6d,#ffc371);
    font-size:16px;      /* ⬅ bigger text */
    transition:0.3s;
}

.back-btn:hover{
    color:black;
    background: radial-gradient(circle, rgba(209,35,50,1), rgba(255,5,5,1));
    box-shadow:
        0 0 10px rgba(255,255,255,0.8),
        0 0 20px rgba(255,255,255,0.6),
        0 0 35px rgba(255,255,255,0.4);
    transform:translateY(-2px);
}


.delete-btn{
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    margin-top: 1px;
    color: inherit; /* same as text */

}

.delete-btn:hover{
    transform: scale(1);
}
.tooltip{
    position: relative;
}

.tooltip-text{
    visibility: hidden;
    background: black;
    color: white;
    font-size: 12px;
    padding: 5px 10px;
    border-radius: 6px;
    position: absolute;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    opacity: 0;
    transition: 0.3s;
}

.tooltip:hover .tooltip-text{
    visibility: visible;
    opacity: 1;
}
</style>
</head>

<body>

<div class="results-container">

<h2>🎉 Recently Added Event</h2>

<div class="events-wrapper">

    <div class="event-card">

        <div class="event-title">
            🎉 <?= htmlspecialchars($event['event_name']) ?>
        </div>

        <div class="event-info">📍 <b>Location:</b> <?= htmlspecialchars($event['location']) ?></div>

        <div class="event-info">📅 <b>Date:</b> <?= $event['date'] ?></div>

        <div class="event-info">⏰ <b>Time:</b> <?= $event['time'] ?></div>

        <div class="event-info">👷 <b>Workers Needed:</b> <?= $event['workers_needed'] ?></div>

        <div class="event-info">👨‍🍳 <b>Catering Name:</b> <?= htmlspecialchars($event['caterer_name']) ?></div>

        <div class="event-info">📞 <b>Contact:</b> <?= $event['caterer_contact'] ?></div>
<form method="POST" onsubmit="return confirmDelete()">
    <input type="hidden" name="delete_id" value="<?= $event['id'] ?>">
<button type="submit" class="delete-btn tooltip">
    🗑
   
    <span class="tooltip-text">Delete your event</span>
</button>
</form>
    </div>

</div>

<a href="addEvent.php" class="back-btn">⬅ Back</a>

</div>
<script>
function confirmDelete() {
    return confirm("⚠ Are you sure you want to delete this event?");
}
</script>
</body>
</html>