<?php
session_start();

require "db.php";

/* LOGIN CHECK */

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
    exit();
}

/* GET EVENT ID */

if(!isset($_GET['event_id'])){

    die("Event not found");
}

$event_id = (int)$_GET['event_id'];

/* FETCH EVENT */

$stmt = $conn->prepare("
    SELECT *
    FROM events
    WHERE id=?
");

$stmt->bind_param("i", $event_id);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){

    die("Invalid Event");
}

$event = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Fake Payment</title>

<style>

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

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

    padding:20px;

    background:
    linear-gradient(
    rgba(0,0,0,.7),
    rgba(0,0,0,.85)
    ),

    url('https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg');

    background-size:cover;
    background-position:center;
}

.container{

    width:430px;

    max-width:100%;

    padding:30px;

    border-radius:25px;

    background:rgba(255,255,255,.12);

    backdrop-filter:blur(18px);

    color:white;

    text-align:center;

    box-shadow:0 15px 40px rgba(0,0,0,.5);
}

h2{

    margin-bottom:20px;

    font-size:30px;
}

.payment-box{

    padding:20px;

    border-radius:18px;

    background:rgba(255,255,255,.10);

    margin-bottom:25px;

    text-align:left;
}

.payment-box p{

    margin:12px 0;

    font-size:16px;
}

button,
.back-btn{

    width:100%;

    padding:14px;

    border:none;

    border-radius:30px;

    font-size:16px;

    color:white;

    cursor:pointer;

    text-decoration:none;

    display:block;

    transition:.3s;

    background:
    linear-gradient(
    90deg,
    #ff5f6d,
    #ffc371
    );
}

button:hover,
.back-btn:hover{

    transform:translateY(-2px);

    box-shadow:
    0 0 12px rgba(255,255,255,.6);
}

.loader-box{

    display:none;

    margin-top:20px;
}

.loader{

    width:80px;
    height:80px;

    margin:auto;

    border-radius:50%;

    border:8px solid rgba(255,255,255,.2);

    border-top:8px solid white;

    animation:spin 1s linear infinite;
}

.loading-text{

    margin-top:20px;

    font-size:22px;

    animation:blink 1s infinite;
}

.success{

    display:none;

    margin-top:20px;

    padding:20px;

    border-radius:18px;

    background:transparent;

    font-size:20px;

    line-height:1.8;
}

.back-btn{

    margin-top:20px;
}

@keyframes spin{

    100%{
        transform:rotate(360deg);
    }
}

@keyframes blink{

    50%{
        opacity:.4;
    }
}

</style>

</head>

<body>

<div class="container">

    <h2>
        💳 Payment
    </h2>

    <!-- EVENT DETAILS -->

    <div class="payment-box">

        <p>
            🎉
            <b>
                <?= htmlspecialchars($event['event_name']) ?>
            </b>
        </p>

        <p>
            📍
            <?= htmlspecialchars($event['location']) ?>
        </p>

        <p>
            📅
            <?= htmlspecialchars($event['date']) ?>
        </p>

        <p>
            👷 Workers Needed :
            <b>
                <?= htmlspecialchars($event['workers_needed']) ?>
            </b>
        </p>

        <p>
            💰 Booking Amount :
            <b>₹100</b>
        </p>

    </div>

    <!-- PAYMENT BUTTON -->

    <div id="paymentSection">

        <button id="payBtn">

            Pay ₹100

        </button>

    </div>

    <!-- LOADER -->

    <div class="loader-box" id="loaderBox">

        <div class="loader"></div>

        <div class="loading-text">

            Paying ₹100...

        </div>

    </div>

    <!-- SUCCESS MESSAGE -->

    <div class="success" id="successBox">

        ✅ Payment Successful <br><br>

        🎉 Confirmed Your Booking

        <a
        href="bookingEvent.php?id=<?= $event['id'] ?>"
        class="back-btn">

            ⬅ Back to Booking

        </a>

    </div>

</div>

<script>

const payBtn = document.getElementById("payBtn");

payBtn.addEventListener("click", function(){

    document.getElementById("paymentSection").style.display = "none";

    document.getElementById("loaderBox").style.display = "block";

    setTimeout(() => {

        document.getElementById("loaderBox").style.display = "none";

        document.getElementById("successBox").style.display = "block";

    }, 3000);

});

</script>

</body>
</html>