<?php
include "db.php";

$date = $_GET['date'] ?? "";
$location = $_GET['location'] ?? "";

$sql = "SELECT * FROM events WHERE 1";

if($date != ""){
    $sql .= " AND date='$date'";
}

if($location != ""){
    $sql .= " AND location='$location'";
}

$sql .= " ORDER BY date DESC";

$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Recent Events</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Poppins,sans-serif;
}

/* Background Blur */
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

/* Glass Container */
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

/* Scroll Area */
.events-wrapper{
    max-height:400px;
    overflow-y:auto;
    padding-right:10px;
    margin-bottom:20px;
}

/* Event Card */
.event-card{
    background:#f2f2f2;
    color:#333;
    padding:20px;
    border-radius:20px;
    margin-bottom:20px;
    text-align:left;
    box-shadow:0 5px 15px rgba(0,0,0,.3);
    transition:0.3s ease;
}

.event-card:hover{
    transform:translateY(-5px);
}

/* Titles */
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

/* Back Button */
.back-btn{
    display:inline-block;
    margin-top:20px;
    padding:12px 30px;
    border-radius:30px;
    text-decoration:none;
    color:white;
    background:linear-gradient(90deg,#ff5f6d,#ffc371);
    transition:0.3s;
}

.back-btn:hover{
  color:black;
  background: radial-gradient(circle, rgba(209,35,50,1), rgba(255,5,5,1));

  /* ✨ WHITE GLOW */
  box-shadow:
    0 0 10px rgba(255,255,255,0.8),
    0 0 20px rgba(255,255,255,0.6),
    0 0 35px rgba(255,255,255,0.4);

  transform:translateY(-2px);
}
main{
  width:75%;
  max-width:650px;
  padding:35px 30px;

  background: rgba(255,255,255,0.12); /* semi transparent */
  backdrop-filter: blur(20px);        /* 🔥 blur effect */
  -webkit-backdrop-filter: blur(20px); /* for Safari */

  border-radius:25px;
  box-shadow:0 25px 60px rgba(0,0,0,.5);

  border:1px solid rgba(255,255,255,0.25); /* glass border */

  margin-top:100px;
}
.choiceCard{
  border:1px solid rgba(255,255,255,.3);
  background: rgba(255,255,255,0.08);

  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
}
.go-btn{

    display:block;

    width:100%;

    margin-top:18px;

    padding:12px;

    text-align:center;

    border-radius:25px;

    text-decoration:none;

    color:white;

    background:linear-gradient(90deg,#ff5f6d,#ffc371);

    transition:.3s ease;

    font-weight:600;
}

.go-btn:hover{

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

<div class="results-container">
<h2>🎉 Recent Events</h2>

<div class="events-wrapper">

<?php
if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
?>

<div class="event-card">

<div class="event-title">
🎉 <?php echo $row['event_name']; ?>
</div>

<div class="event-info">
📍 <b>Location:</b>
<?php echo $row['location']; ?>
</div>

<div class="event-info">
📅 <b>Date:</b>
<?php echo $row['date']; ?>
</div>

<div class="event-info">
⏰ <b>Time:</b>
<?php echo $row['time']; ?>
</div>

<div class="event-info">
👷 <b>Workers Needed:</b>
<?php echo $row['workers_needed']; ?>
</div>

<div class="event-info">
👨‍🍳 <b>Caterer:</b>
<?php echo $row['caterer_name']; ?>
</div>

<div class="event-info">
📞 <b>Contact:</b>
<?php echo $row['caterer_contact']; ?>
</div>

<a href="bookingEvent.php?id=<?php echo $row['id']; ?>" class="go-btn">
    Go →
</a>

</div>

<?php
    }
}else{
    echo "<p>No events found</p>";
}
?>

</div>

<a href="searchEvents.php" class="back-btn">← Back</a>
</div>

</body>
</html>