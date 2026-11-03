
<?php
session_start();
require "db.php";

// ADMIN ACCESS
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// CHECK USER ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid User ID");
}

$userId = intval($_GET['id']);

// ESCAPE OUTPUT
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/* =========================
   USER DETAILS
========================= */

$userQuery = $conn->query("
    SELECT id, username, email, phone, profile_pic
    FROM users
    WHERE id = $userId
");

if (!$userQuery) {
    die("User Query Error: " . $conn->error);
}

$user = $userQuery->fetch_assoc();

if (!$user) {
    die("User not found");
}

/* =========================
   BOOKED EVENTS
========================= */

$bookedEvents = $conn->query("
    SELECT
        bookings.id AS booking_id,
        events.event_name,
        events.date AS event_date,
        events.location
    FROM bookings
    INNER JOIN events
        ON bookings.event_id = events.id
    WHERE bookings.user_id = $userId
    ORDER BY bookings.id DESC
");

if (!$bookedEvents) {
    die("Booked Events Query Error: " . $conn->error);
}

/* =========================
   ADDED EVENTS
========================= */

$addedEvents = $conn->query("
    SELECT
        id,
        event_name,
        date,
        time,
        location,
        workers_needed
    FROM events
    WHERE caterer_id = $userId
    ORDER BY id DESC
");

if (!$addedEvents) {
    die("Added Events Query Error: " . $conn->error);
}

// PROFILE IMAGE
$profileImage = !empty($user['profile_pic'])
    ? "uploads/" . $user['profile_pic']
    : "https://cdn-icons-png.flaticon.com/512/149/149071.png";
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>User History</title>

<style>

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}

body {
    min-height: 100vh;
    background:
        linear-gradient(rgba(0,0,0,.85), rgba(0,0,0,.92)),
        url("https://images.unsplash.com/photo-1414235077428-338989a2e8c0?q=80&w=1600")
        no-repeat center center fixed;
    background-size: cover;
    padding: 30px;
}

.container {
    max-width: 1200px;
    margin: auto;
}

/* CARD */

.card {
    background: rgba(255,255,255,.08);
    backdrop-filter: blur(18px);
    border-radius: 25px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 10px 35px rgba(0,0,0,.5);
}

.title {
    color: white;
    font-size: 28px;
    margin-bottom: 25px;
}

/* USER DETAILS */

.user-box {
    display: flex;
    align-items: center;
    gap: 25px;
    flex-wrap: wrap;
}

.user-box img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
}

.user-info {
    color: white;
    line-height: 2;
    overflow-wrap: anywhere;
}

/* TABLE */

.table-container {
    width: 100%;
    overflow-x: auto;
    border-radius: 15px;
}

table {
    width: 100%;
    min-width: 600px;
    border-collapse: collapse;
}

table th {
    background: rgba(255,255,255,.15);
    color: white;
    padding: 16px;
    text-align: center;
    white-space: nowrap;
}

table td {
    padding: 16px;
    color: #f3f4f6;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,.08);
    white-space: nowrap;
}

table tr:hover {
    background: rgba(255,255,255,.05);
}

.no-data {
    color: #ddd;
    text-align: center;
    padding: 20px;
}

/* BACK BUTTON */

.back-btn {
    display: inline-block;
    margin-top: 10px;
    padding: 13px 35px;
    border-radius: 35px;
    text-decoration: none;
    background: linear-gradient(90deg, #ff5f6d, #ffc371);
    color: white;
    font-weight: 600;
    transition: .3s ease;
}

.back-btn:hover {
    color: black;
    transform: translateY(-3px);
    box-shadow:
        0 0 12px rgba(255,255,255,.8),
        0 0 25px rgba(255,255,255,.5);
}

/* RESPONSIVE */

@media(max-width:600px) {
    body {
        padding: 15px;
    }

    .card {
        padding: 20px;
    }

    .title {
        font-size: 22px;
    }

    .user-box {
        flex-direction: column;
        align-items: flex-start;
    }
}

</style>

</head>

<body>

<div class="container">

    <!-- USER DETAILS -->

    <div class="card">

        <div class="title">
            👤 User Details
        </div>

        <div class="user-box">

            <img
                src="<?= e($profileImage) ?>"
                alt="Profile Picture"
                onerror="this.onerror=null;this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png';"
            >

            <div class="user-info">

                <h2><?= e($user['username']) ?></h2>

                <p>Email: <?= e($user['email']) ?></p>

                <p>Phone: <?= e($user['phone']) ?></p>

                <p>User ID: #<?= e($user['id']) ?></p>

            </div>

        </div>

    </div>

    <!-- BOOKED EVENTS -->

    <div class="card">

        <div class="title">
            🎟 Booked Events History
        </div>

        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Location</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($bookedEvents->num_rows > 0): ?>

                    <?php while ($book = $bookedEvents->fetch_assoc()): ?>

                        <tr>

                            <td>#<?= e($book['booking_id']) ?></td>

                            <td><?= e($book['event_name']) ?></td>

                            <td><?= e($book['event_date']) ?></td>

                            <td><?= e($book['location']) ?></td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="4" class="no-data">
                            No booked events found
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- ADDED EVENTS -->

    <div class="card">

        <div class="title">
            🎉 Added Events History
        </div>

        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>Event ID</th>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Workers</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($addedEvents->num_rows > 0): ?>

                    <?php while ($event = $addedEvents->fetch_assoc()): ?>

                        <tr>

                            <td>#<?= e($event['id']) ?></td>

                            <td><?= e($event['event_name']) ?></td>

                            <td><?= e($event['date']) ?></td>

                            <td><?= e($event['time']) ?></td>

                            <td><?= e($event['location']) ?></td>

                            <td><?= e($event['workers_needed']) ?></td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="no-data">
                            No added events found
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

    <center>

        <a href="admin.php" class="back-btn">
            ⬅ Back
        </a>

    </center>

</div>

</body>
</html>
