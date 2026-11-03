
<?php
session_start();
require "db.php";

/* ================= ADMIN CHECK ================= */

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

/* ================= FILTER ================= */

$filter = $_GET['filter'] ?? 'all';

$allowedFilters = ['all', 'users', 'organizers', 'events'];

if (!in_array($filter, $allowedFilters, true)) {
    $filter = 'all';
}

/* ================= USERS QUERY ================= */

if ($filter === "users") {

    $userResult = $conn->query("
        SELECT *
        FROM users
        WHERE id NOT IN (
            SELECT DISTINCT caterer_id
            FROM events
            WHERE caterer_id IS NOT NULL
        )
        ORDER BY id DESC
    ");

}
elseif ($filter === "organizers") {

    $userResult = $conn->query("
        SELECT *
        FROM users
        WHERE id IN (
            SELECT DISTINCT caterer_id
            FROM events
            WHERE caterer_id IS NOT NULL
        )
        ORDER BY id DESC
    ");

}
else {

    $userResult = $conn->query("
        SELECT *
        FROM users
        ORDER BY id DESC
    ");
}

/* ================= CHECK USERS QUERY ================= */

if (!$userResult) {
    die("Users Query Error: " . $conn->error);
}

/* ================= EVENTS QUERY ================= */

$eventResult = $conn->query("
    SELECT
        events.*,
        users.username,
        users.profile_pic,
        users.phone

    FROM events

    INNER JOIN users
    ON events.caterer_id = users.id

    ORDER BY events.id DESC
");

/* ================= CHECK EVENTS QUERY ================= */

if (!$eventResult) {
    die("Events Query Error: " . $conn->error);
}

/* ================= ORGANIZER IDS ================= */

$organizerIds = [];

$tempQuery = $conn->query("
    SELECT DISTINCT caterer_id
    FROM events
    WHERE caterer_id IS NOT NULL
");

if (!$tempQuery) {
    die("Organizer Query Error: " . $conn->error);
}

while ($row = $tempQuery->fetch_assoc()) {
    $organizerIds[] = (int)$row['caterer_id'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard</title>

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
    background:
    linear-gradient(rgba(0,0,0,.75), rgba(0,0,0,.88)),
    url("https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg")
    no-repeat center center fixed;
    background-size:cover;
    padding:25px;
    overflow-x:hidden;
}

/* SIDEBAR */

.sidebar{
    position:fixed;
    top:0;
    left:0;
    width:260px;
    height:100vh;
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(18px);
    border-right:1px solid rgba(255,255,255,.08);
    padding:30px 20px;
    z-index:1000;
}

.logo{
    color:white;
    font-size:28px;
    font-weight:700;
    text-align:center;
    margin-bottom:45px;
}

.menu{
    display:flex;
    flex-direction:column;
    gap:18px;
}

.menu a{
    text-decoration:none;
    color:#d1d5db;
    padding:15px 18px;
    border-radius:14px;
    transition:.3s ease;
    font-size:15px;
}

.menu a:hover,
.menu a.active{
    background:linear-gradient(90deg,#ff5f6d,#ffc371);
    color:white;
    transform:translateX(6px);
    box-shadow:0 0 10px rgba(255,255,255,.5),
               0 0 20px rgba(255,255,255,.3);
}

/* MAIN */

.main{
    margin-left:285px;
}

/* TOPBAR */

.topbar{
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(18px);
    border-radius:22px;
    padding:22px 28px;
    margin-bottom:30px;
    box-shadow:0 10px 35px rgba(0,0,0,.45);
}

.topbar h1{
    color:white;
    font-size:30px;
}

/* TABLE CARD */

.table-card{
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(18px);
    border-radius:25px;
    padding:28px;
    box-shadow:0 15px 35px rgba(0,0,0,.45);
    overflow:auto;
}

.table-title{
    color:white;
    font-size:25px;
    margin-bottom:22px;
    font-weight:600;
}

/* TABLE */

table{
    width:100%;
    border-collapse:collapse;
    min-width:1100px;
}

table th{
    background:rgba(255,255,255,.15);
    color:white;
    padding:18px;
    text-align:center;
    font-size:14px;
}

table td{
    padding:18px;
    text-align:center;
    color:#f3f4f6;
    border-bottom:none;
    font-size:14px;
}

table tr:hover{
    background:rgba(255,255,255,.06);
}

/* PROFILE */

.profile{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:10px;
}

.profile img{
    width:60px;
    height:60px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid white;
}

/* BUTTON */

.view-btn{
    text-decoration:none;
    background:linear-gradient(90deg,#ff5f6d,#ffc371);
    color:white;
    padding:11px 18px;
    border-radius:14px;
    font-size:13px;
    font-weight:600;
    transition:.3s ease;
    display:inline-block;
}

.view-btn:hover{
    color:black;
    transform:translateY(-3px);
    box-shadow:0 0 10px rgba(255,255,255,.7),
               0 0 20px rgba(255,255,255,.4);
}

/* STATUS */

.full{
    color:#ff6b6b;
    font-weight:bold;
}

.available{
    color:#90ee90;
    font-weight:bold;
}

/* BACK BUTTON */

.logout{
    display:inline-block;
    margin-top:35px;
    padding:14px 40px;
    border-radius:40px;
    text-decoration:none;
    color:white;
    background:linear-gradient(90deg,#ff5f6d,#ffc371);
    font-weight:600;
    transition:.35s ease;
}

.logout:hover{
    color:black;
    transform:translateY(-4px);
    box-shadow:0 0 12px rgba(255,255,255,.8),
               0 0 25px rgba(255,255,255,.5);
}

/* MOBILE */

@media(max-width:950px){

    .sidebar{
        width:100%;
        height:auto;
        position:relative;
        margin-bottom:20px;
    }

    .main{
        margin-left:0;
    }

    table{
        min-width:950px;
    }
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
    All Details
    </div>

    <div class="menu">

        <a href="admin.php?filter=all"
        class="<?= $filter=='all' ? 'active' : '' ?>">
            📊 Dashboard
        </a>

        <a href="admin.php?filter=users"
        class="<?= $filter=='users' ? 'active' : '' ?>">
            👥 Users
        </a>

        <a href="admin.php?filter=organizers"
        class="<?= $filter=='organizers' ? 'active' : '' ?>">
            🧑‍🍳 Organizers
        </a>

        <a href="admin.php?filter=events"
        class="<?= $filter=='events' ? 'active' : '' ?>">
            🎉 Events
        </a>

    </div>

</div>

<!-- MAIN -->

<div class="main">

    <div class="topbar">
        <h1>Admin World🔥</h1>
    </div>

    <div class="table-card">

<?php if($filter === "events"): ?>

    <div class="table-title">
        🎉 All Added Events
    </div>

    <table>

        <tr>
            <th>Organizer</th>
            <th>Event</th>
            <th>Location</th>
            <th>Date</th>
            <th>Time</th>
            <th>Total Workers</th>
            <th>Added Workers</th>
            <th>Status</th>
            <th>Contact</th>
        </tr>

        <?php while($event = $eventResult->fetch_assoc()): ?>

        <tr>

            <td>
                <div class="profile">

                    <?php if(!empty($event['profile_pic'])): ?>

                        <img src="uploads/<?= htmlspecialchars($event['profile_pic']) ?>">

                    <?php else: ?>

                        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png">

                    <?php endif; ?>

                    <?= htmlspecialchars($event['username']) ?>

                </div>
            </td>

            <td><?= htmlspecialchars($event['event_name']) ?></td>

            <td><?= htmlspecialchars($event['location']) ?></td>

            <td><?= htmlspecialchars($event['date']) ?></td>

            <td><?= htmlspecialchars($event['time']) ?></td>

            <td><?= htmlspecialchars($event['workers_needed']) ?></td>

            <td><?= htmlspecialchars($event['rem_workers']) ?></td>

            <td>

                <?php if($event['rem_workers'] <= 0): ?>

                    <span class="full">❌ Full</span>

                <?php else: ?>

                    <span class="available">✅ Available</span>

                <?php endif; ?>

            </td>

            <td><?= htmlspecialchars($event['caterer_contact']) ?></td>

        </tr>

        <?php endwhile; ?>

    </table>

<?php else: ?>

    <div class="table-title">

        <?php
        if($filter === "users"){
            echo "👥 All Users";
        }
        elseif($filter === "organizers"){
            echo "🧑‍🍳 All Organizers";
        }
        else{
            echo "📊 Dashboard - All Accounts";
        }
        ?>

    </div>

    <table>

        <tr>
            <th>Profile</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Type</th>
            <th>Action</th>
        </tr>

        <?php while($user = $userResult->fetch_assoc()): ?>

        <tr>

            <td>
                <div class="profile">

                    <?php if(!empty($user['profile_pic'])): ?>

                        <img src="uploads/<?= htmlspecialchars($user['profile_pic']) ?>">

                    <?php else: ?>

                        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png">

                    <?php endif; ?>

                </div>
            </td>

            <td><?= htmlspecialchars($user['username']) ?></td>

            <td><?= htmlspecialchars($user['email']) ?></td>

            <td><?= htmlspecialchars($user['phone']) ?></td>

            <td>
                <?= in_array((int)$user['id'], $organizerIds, true)
                    ? 'Organizer'
                    : 'User' ?>
            </td>

            <td>

                <a href="userHistory.php?id=<?= (int)$user['id'] ?>"
                   class="view-btn">
                    View Details
                </a>

            </td>

        </tr>

        <?php endwhile; ?>

    </table>

<?php endif; ?>

    </div>

    <a href="login.php" class="logout">
        ⬅ Back
    </a>

</div>

</body>
</html>
