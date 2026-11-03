<?php
session_start();
require "db.php";

/* LOGIN CHECK */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

/* GET EVENTS */
$events = [];

if(isset($_GET['search']) && $_GET['search'] != ""){

    $keyword = "%" . $_GET['search'] . "%";

    $stmt = $conn->prepare("
        SELECT * FROM events
        WHERE event_name LIKE ?
        OR location LIKE ?
        ORDER BY date ASC
    ");

    $stmt->bind_param("ss", $keyword, $keyword);

    $stmt->execute();

    $events = $stmt
        ->get_result()
        ->fetch_all(MYSQLI_ASSOC);

} else {

    $result = $conn->query("
        SELECT * FROM events
        ORDER BY date ASC
    ");

    if($result){
        $events = $result->fetch_all(MYSQLI_ASSOC);
    }
}

/* GET PROFILE PIC */
$userData = [];

$stmt = $conn->prepare("
    SELECT profile_pic
    FROM users
    WHERE id=?
");

/* CHECK QUERY */
if($stmt){

    $stmt->bind_param(
        "i",
        $_SESSION['user_id']
    );

    $stmt->execute();

    $result = $stmt->get_result();

    $userData = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Search Events</title>

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
    flex-direction:column;

    align-items:center;
    justify-content:center;

    overflow:hidden;

    background:
    linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.85)),
    url("https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg")
    no-repeat center center fixed;

    background-size:cover;

    padding-bottom:60px;
}

/* HEADER */

header{

    color:white;

    padding:15px;

    font-size:2rem;

    position:fixed;

    top:0;
    left:0;

    width:100%;

    text-align:center;

    z-index:1000;
}

/* MAIN */

main{

    width:85%;
    max-width:700px;

    margin-top:120px;

    padding:25px;

    border-radius:25px;

    background:rgba(255,255,255,.12);

    backdrop-filter:blur(18px);

    box-shadow:0 25px 60px rgba(0,0,0,.5);
}

h2{
    color:white;
    text-align:center;
    margin-bottom:10px;
}

.sub{
    color:#ccc;
    text-align:center;
    margin-bottom:20px;
}

/* SEARCH */

.search-box{

    display:flex;
    flex-direction:column;

    gap:12px;
}

input,
select{

    width:100%;

    padding:12px;

    border:none;
    outline:none;

    border-radius:12px;

    transition:.3s ease;
}

input:hover,
select:hover{

    border:1.5px solid black;

    box-shadow:
    0 0 10px rgba(255,255,255,.6);
}

input:focus,
select:focus{

    border:1.5px solid black;

    box-shadow:
    0 0 8px rgba(255,255,255,.7),
    0 0 16px rgba(255,255,255,.5);

    transform:scale(1.01);
}

/* BUTTONS */

button,
.viewBtn,
.backBtn{

    width:100%;

    padding:12px;

    border:none;

    border-radius:25px;

    text-align:center;

    font-size:15px;

    text-decoration:none;

    cursor:pointer;

    color:white;

    background:
    linear-gradient(
    90deg,
    #ff5f6d,
    #ffc371
    );

    transition:.3s ease;
}

button:hover,
.viewBtn:hover,
.backBtn:hover{

    color:black;

    background:
    radial-gradient(
    circle,
    #d12332,
    #ff0505
    );

    box-shadow:
    0 0 10px rgba(255,255,255,.8),
    0 0 20px rgba(255,255,255,.6),
    0 0 35px rgba(255,255,255,.4);

    transform:translateY(-2px);
}

/* NAV */

.nav-links{

    display:flex;
    flex-direction:column;

    gap:12px;

    margin-top:20px;
}

/* PROFILE */

.avatar{

    position:fixed;

    top:20px;
    right:20px;

    width:55px;
    height:55px;

    border-radius:50%;

    overflow:hidden;

    cursor:pointer;

    border:2px solid white;

    z-index:9999;
}

.avatar-img{

    width:100%;
    height:100%;

    object-fit:cover;
}

.avatar:hover{
    transform:scale(1.08);
}

/* PROFILE PANEL */

.profile-panel{

    position:fixed;

    top:0;
    right:-380px;

    width:320px;
    height:100%;

    background:#000;

    padding:25px;

    transition:.35s ease;

    z-index:9999;
}

.profile-panel.active{
    right:0;
}

.profile-header h2{
    font-size:28px;
}

.username{

    margin-top:20px;

    color:white;

    font-size:20px;

    font-weight:700;

    text-align:center;

    text-transform:uppercase;
}

/* PROFILE CARDS */

.profile-card{

    display:block;

    background:
    linear-gradient(
    145deg,
    #1f1f1f,
    #151515
    );

    padding:14px;

    margin:12px 0;

    border-radius:15px;

    color:#ddd;

    text-decoration:none;

    transition:.2s;
}

.profile-card:hover{
    transform:translateX(6px);
}

.profile-card.danger{
    color:#ff5252;
}

.profile-card.logout{
    color:#ffb74d;
}

/* CLOSE */

.close-btn{

    width:100%;

    padding:12px;

    margin-top:20px;

    border-radius:25px;

    background:transparent;

    border:2px solid #555;

    color:white;

    cursor:pointer;
}

.close-btn:hover{
    background:rgba(255,255,255,.1);
}

</style>

</head>

<body>

<header>
🍽 Catering Connect
</header>

<!-- PROFILE ICON -->

<div class="avatar"
onclick="toggleProfile()">

<?php if(!empty($userData['profile_pic'])): ?>

<img
src="uploads/<?= htmlspecialchars($userData['profile_pic']) ?>"
class="avatar-img">

<?php else: ?>

<img
src="https://cdn-icons-png.flaticon.com/512/149/149071.png"
class="avatar-img">

<?php endif; ?>

</div>

<!-- PROFILE PANEL -->

<div class="profile-panel"
id="profilePanel">

<div class="profile-header">

<h2>My Profile</h2>

<p class="username">
<?= htmlspecialchars($_SESSION['user']) ?>
</p>

</div>

<hr>

<a href="account.php"
class="profile-card">
👤 My Account
</a>

<a href="myHistory.php"
class="profile-card">
📜 My History
</a>

<a href="help.php"
class="profile-card">
❓ Help
</a>

<a href="delete.php"
onclick="return confirmDelete()"
class="profile-card danger">
🗑 Delete Account
</a>

<a href="logout.php"
class="profile-card logout">
🚪 Logout
</a>

<button
class="close-btn"
onclick="toggleProfile()">
❌ Close
</button>

</div>

<!-- MAIN -->

<main>

<h2>Available Events</h2>

<p class="sub">
Find catering opportunities near you
</p>

<form
action="viewEvents.php"
method="GET"
class="search-box">

<input
type="date"
name="date"
min="<?= date('Y-m-d') ?>">

<select name="location">

<option value="">
Select Location
</option>

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

<button
type="submit"
id="searchBtn"
disabled>
Search
</button>

</form>

<div class="nav-links">

<a href="viewEvents.php"
class="viewBtn">
👁 View All Events
</a>

<a href="index.php"
class="backBtn">
⬅ Back
</a>

</div>

</main>

<script>

function toggleProfile(){

    document
    .getElementById("profilePanel")
    .classList
    .toggle("active");
}

/* OUTSIDE CLICK */

document.addEventListener(
"click",
function(e){

    const panel =
    document.getElementById(
    "profilePanel"
    );

    const avatar =
    document.querySelector(
    ".avatar"
    );

    if(
        !panel.contains(e.target)
        &&
        !avatar.contains(e.target)
    ){

        panel.classList.remove(
        "active"
        );
    }
});

/* DELETE */

function confirmDelete(){

    return confirm(
    "⚠️ Are you sure?\nThis action cannot be undone!"
    );
}

/* BUTTON ENABLE */

const dateInput =
document.querySelector(
'input[name="date"]'
);

const locationInput =
document.querySelector(
'select[name="location"]'
);

const btn =
document.getElementById(
"searchBtn"
);

function checkInputs(){

    if(
        dateInput.value !== ""
        ||
        locationInput.value !== ""
    ){

        btn.disabled = false;
        btn.style.opacity = "1";

    } else {

        btn.disabled = true;
        btn.style.opacity = "0.5";
    }
}

dateInput.addEventListener(
"input",
checkInputs
);

locationInput.addEventListener(
"change",
checkInputs
);

</script>

</body>
</html>

