<?php
session_start();
require "db.php";

if(!isset($_GET['id'])){
    die("Event ID missing");
}

$id = intval($_GET['id']);

/* GET EVENT */

$query = $conn->query("
    SELECT *
    FROM events
    WHERE id='$id'
");

if($query->num_rows == 0){
    die("Event not found");
}

$event = $query->fetch_assoc();

/* CONFIRM BOOKING */

if(isset($_POST['confirm'])){

    /*
    CREATE bookings TABLE FIRST

    CREATE TABLE bookings(
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        event_id INT,
        event_name VARCHAR(100),
        location VARCHAR(100),
        event_date DATE,
        booked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    */

    $user_id = $_SESSION['user_id'];

    $event_name = $event['event_name'];
    $location   = $event['location'];
    $event_date = $event['date'];

    $insert = $conn->query("
        INSERT INTO bookings
        (user_id,event_id,event_name,location,event_date)
        VALUES
        (
            '$user_id',
            '$id',
            '$event_name',
            '$location',
            '$event_date'
        )
    ");

    if($insert){

        echo "
        <script>
        alert('Event booked successfully');
        window.location='searchEvents.php';
        </script>
        ";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Booking Event</title>

<style>

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:"Poppins",sans-serif;
}

body{

    min-height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;

    padding:25px;

    background:
    linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.85)),
    url("https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg")
    no-repeat center center fixed;

    background-size:cover;
}

.container{

    width:100%;
    max-width:600px;

    padding:30px;

    border-radius:25px;

    background:rgba(255,255,255,.12);

    backdrop-filter:blur(18px);

    box-shadow:0 15px 40px rgba(0,0,0,.5);

    color:white;
}

.title{

    text-align:center;

    font-size:30px;

    margin-bottom:25px;
}

.details{

    background:rgba(255,255,255,.08);

    border-radius:20px;

    padding:25px;

    margin-bottom:25px;
}

.info{

    margin:15px 0;

    font-size:17px;

    line-height:1.8;
}

.buttons{

    display:flex;

    gap:15px;
}

button,
.back-btn{

    flex:1;

    text-align:center;

    padding:14px;

    border:none;

    border-radius:30px;

    font-size:15px;

    font-weight:600;

    cursor:pointer;

    text-decoration:none;

    color:white;

    transition:.3s ease;

    background:
    linear-gradient(
    90deg,
    #ff5f6d,
    #ffc371
    );
}

button:hover,
.back-btn:hover{

    color:black;

    background:
    radial-gradient(
    circle,
    rgba(209,35,50,1),
    rgba(255,5,5,1)
    );

    box-shadow:
    0 0 10px rgba(255,255,255,.8),
    0 0 20px rgba(255,255,255,.5);

    transform:translateY(-2px);
}

</style>

</head>

<body>

<div class="container">

    <div class="title">
        🎉 Event Details
    </div>

    <div class="details">

        <div class="info">
            🎉 <b>Event:</b>
            <?= htmlspecialchars($event['event_name']) ?>
        </div>

        <div class="info">
            📍 <b>Location:</b>
            <?= htmlspecialchars($event['location']) ?>
        </div>

        <div class="info">
            📅 <b>Date:</b>
            <?= htmlspecialchars($event['date']) ?>
        </div>

        <div class="info">
            ⏰ <b>Time:</b>
            <?= htmlspecialchars($event['time']) ?>
        </div>

        <div class="info">
            👷 <b>Workers Needed:</b>
            <?= htmlspecialchars($event['workers_needed']) ?>
        </div>

        <div class="info">
            👨‍🍳 <b>Caterer:</b>
            <?= htmlspecialchars($event['caterer_name']) ?>
        </div>

        <div class="info">
            📞 <b>Contact:</b>
            <?= htmlspecialchars($event['caterer_contact']) ?>
        </div>

    </div>

    <form method="POST">

        <div class="buttons">

            <button type="submit" name="confirm">
                ✅ Confirm Booking
            </button>

            <a href="recentEvents.php" class="back-btn">
                ⬅ Back
            </a>

        </div>

    </form>

</div>

</body>
</html>