<?php
session_start();
require "db.php";

/* LOGIN CHECK */

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

/* GET EVENT ID */

if(!isset($_GET['id'])){
    die("Event not found");
}

$event_id = (int)$_GET['id'];

/*
|--------------------------------------------------------------------------
| FETCH EVENT
|--------------------------------------------------------------------------
*/

$event_stmt = $conn->prepare("
    SELECT *
    FROM events
    WHERE id = ?
");

$event_stmt->bind_param("i", $event_id);
$event_stmt->execute();

$event_result = $event_stmt->get_result();

if($event_result->num_rows == 0){
    die("Invalid Event");
}

$event = $event_result->fetch_assoc();

/*
|--------------------------------------------------------------------------
| FETCH WORKERS WHO JOINED
|--------------------------------------------------------------------------
|
| TABLES USED:
|
| bookings
| - id
| - event_id
| - user_id
|
| users
| - id
| - name
| - phone
| - email
| - profile
|
*/

$stmt = $conn->prepare("
    SELECT users.*
    FROM bookings
    INNER JOIN users
    ON bookings.user_id = users.id
    WHERE bookings.event_id = ?
");

if(!$stmt){
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $event_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Remaining Workers</title>

<style>

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:"Poppins",sans-serif;
}

body{
    background:
    linear-gradient(rgba(0,0,0,.75), rgba(0,0,0,.85)),
    url("https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg")
    no-repeat center center fixed;

    background-size:cover;
    min-height:100vh;
    padding:30px;
    color:white;
}

.container{
    width:90%;
    max-width:1000px;
    margin:auto;
}

h1{
    text-align:center;
    margin-bottom:35px;
    font-size:34px;
}

.worker-card{
    background:rgba(255,255,255,.1);
    backdrop-filter:blur(10px);

    border-radius:20px;
    padding:20px;

    margin-bottom:25px;

    display:flex;
    align-items:center;
    gap:20px;

    box-shadow:0 10px 25px rgba(0,0,0,.4);

    transition:.3s ease;
}

.worker-card:hover{
    transform:translateY(-5px);
}

.worker-card img{
    width:110px;
    height:110px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid white;
}

.worker-details{
    flex:1;
}

.worker-details h2{
    color:#ffc371;
    margin-bottom:12px;
    font-size:24px;
}

.info{
    margin:8px 0;
    font-size:16px;
}

.no-workers{
    text-align:center;
    font-size:22px;
    color:#ddd;
    margin-top:60px;
}

/* BACK BUTTON */

.back-btn{

    display:inline-flex;
    align-items:center;
    justify-content:center;

    width:220px;
    margin-top:15px;
    margin-left:400px ;

    padding:12px 20px;

    border:none;
    border-radius:30px;

    text-decoration:none;

    font-size:16px;
    font-weight:600;

    color:white;

    background:linear-gradient(135deg,#ff6a00,#ee0979);

    box-shadow:
        0 5px 15px rgba(238,9,121,.25);

    cursor:pointer;

    transition:
        transform .25s ease,
        box-shadow .25s ease,
        background .25s ease;
}

/* HOVER */

.back-btn:hover{

    background:linear-gradient(135deg,#ff512f,#dd2476);

    transform:translateY(-3px);

    box-shadow:
        0 10px 22px rgba(221,36,118,.35);

    color:white;
}

/* CLICK EFFECT */

.back-btn:active{

    transform:scale(.98);
}

.event-title{
    text-align:center;
    margin-bottom:40px;
    color:#fff;
    font-size:20px;
}

</style>
</head>
<body>

<div class="container">

    <h1>👷 Joined Workers</h1>

    <div class="event-title">
        Event:
        <?= htmlspecialchars($event['event_name']) ?>
    </div>

    <?php if($result->num_rows > 0): ?>

        <?php while($worker = $result->fetch_assoc()): ?>

            <div class="worker-card">

                <img
    src="uploads/<?= htmlspecialchars($worker['profile_pic']) ?>"
    alt="Profile"
>
    <br>

                <div class="worker-details">

                    <h2>
                       <?= htmlspecialchars($worker['username']) ?>
                    </h2>

                    <div class="info">
                        📞 Phone:
                        <?= htmlspecialchars($worker['phone']) ?>
                    </div>

                    <div class="info">
                        📧 Gmail:
                        <?= htmlspecialchars($worker['email']) ?>
                    </div>

                </div>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div class="no-workers">
            No workers joined this event yet.
        </div>

    <?php endif; ?>

    <a href="myBookings.php" class="back-btn">
        ⬅ Back
    </a>

</div>

</body>
</html>