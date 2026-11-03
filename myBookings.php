<?php
session_start();
require "db.php";

/* LOGIN CHECK */

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* FETCH USER EVENTS */

$stmt = $conn->prepare("
    SELECT *
    FROM events
    WHERE caterer_id = ?
    ORDER BY id DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Events</title>

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
    linear-gradient(rgba(0,0,0,.7), rgba(0,0,0,.8)),
    url("https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg")
    no-repeat center center fixed;

    background-size:cover;

    min-height:100vh;
    padding:30px;
    color:white;
}

h1{
    text-align:center;
    margin-bottom:30px;
}

.container{
    width:90%;
    max-width:1000px;
    margin:auto;
}

.event-card{
    background:rgba(255,255,255,0.1);
    backdrop-filter:blur(10px);

    padding:20px;
    border-radius:18px;

    margin-bottom:20px;

    box-shadow:0 10px 25px rgba(0,0,0,.4);

    transition:.3s ease;
}

.event-card:hover{
    transform:translateY(-4px);
}

.event-card h2{
    margin-bottom:10px;
    color:#ffc371;
}

.event-info{
    margin:8px 0;
    font-size:15px;
}

.no-events{
    text-align:center;
    font-size:20px;
    color:#ddd;
    margin-top:50px;
}

/* BACK BUTTON */

.back-btn{

    display:inline-flex;
    align-items:center;
    justify-content:center;

    width:220px;
    margin:30px auto;

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

.workers-box{
    display:flex;
    gap:20px;
    margin-top:12px;
}

.worker-count{
    background:rgba(255,255,255,.12);
    padding:10px 16px;
    border-radius:12px;
    font-weight:600;
    color:#fff;
}

.full{
    color:#ff6b6b;
    font-weight:bold;
    margin-top:12px;
}

.available{
    color:#90ee90;
    font-weight:bold;
    margin-top:12px;
}

</style>
</head>
<body>

<div class="container">

    <h1>📋 My Added Events</h1>

    <?php if($result->num_rows > 0): ?>

        <?php while($row = $result->fetch_assoc()): ?>

            <div class="event-card">

                <h2>
                    <?= htmlspecialchars($row['event_name']) ?>
                </h2>

                <div class="event-info">
                    📍 Location:
                    <?= htmlspecialchars($row['location']) ?>
                </div>

                <div class="event-info">
                    📅 Date:
                    <?= htmlspecialchars($row['date']) ?>
                </div>

                <div class="event-info">
                    ⏰ Time:
                    <?= htmlspecialchars($row['time']) ?>
                </div>

                <!-- TOTAL + REMAINING -->

                <div class="workers-box">

                    <div class="worker-count">
                        👨‍🍳 Total Workers:
                        <?= htmlspecialchars($row['workers_needed']) ?>
                    </div>

                    <div class="worker-count">
                        👷 Remaining Workers:
                        <?= htmlspecialchars($row['rem_workers']) ?>
                    </div>

               </div>



<?php if($row['rem_workers'] <= 0): ?>

                    <div class="full">
                        ❌ Workers Full
                    </div>

                <?php else: ?>

                    

                <?php endif; ?>

                <div class="event-info">
                    🍽 Caterer:
                    <?= htmlspecialchars($row['caterer_name']) ?>
                </div>

                <div class="event-info">
                    📞 Contact:
                    <?= htmlspecialchars($row['caterer_contact']) ?>
                </div>
<div style="margin-top:15px;">

    <a href="rem_view.php?id=<?= $row['id'] ?>"
       style="
            text-decoration:none;
            padding:10px 18px;
            border-radius:10px;
            background:linear-gradient(90deg,#36d1dc,#5b86e5);
            color:white;
            font-weight:600;
            display:inline-block;
            transition:.3s;
       ">

         View Workers

    </a>

</div>
            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div class="no-events">
            No events added yet.
        </div>

    <?php endif; ?>

    <a href="addEvent.php" class="back-btn">
        ⬅ Back
    </a>

</div>

</body>
</html>