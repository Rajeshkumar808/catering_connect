<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catering Connect</title>

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
    flex-direction:column;

    align-items:center;
    justify-content:center;

    background:
    linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.85)),
    url('https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg')
    no-repeat center center fixed;

    background-size:cover;

    padding-bottom:80px;

    overflow:hidden;
}

/* HEADER */

header{

    color:white;

    padding:15px;

    font-size:2rem;

    position:fixed;

    top:0;

    width:100%;

    text-align:center;
}

/* MAIN */

main{

    width:75%;

    max-width:650px;

    padding:35px 30px;

    background:rgba(35,33,33,0.12);

    backdrop-filter:blur(18px);

    border-radius:25px;

    box-shadow:0 25px 60px rgba(0,0,0,.5);

    margin-top:100px;
}

/* TITLE */

#welcomeTitle{

    color:white;

    font-size:1.7rem;

    text-align:center;
}

#welcomeSub{

    color:#ddd;

    text-align:center;

    margin-bottom:25px;
}

/* CARD CONTAINER */

.choiceContainer{

    display:flex;

    gap:25px;

    justify-content:center;

    flex-wrap:wrap;
}

/* CARD */

.choiceCard{

    border:2px solid rgba(255,255,255,.5);

    border-radius:20px;

    padding:40px 50px;

    text-align:center;

    cursor:pointer;

    width:280px;

    transition:.3s ease;
}

.choiceCard h3{

    color:white;

    font-size:1.6rem;
}

.choiceCard p{

    color:#ddd;

    margin-top:10px;

    font-size:1.05rem;
}

.choiceCard:hover{

    background:rgba(255,255,255,.15);

    transform:translateY(-8px) scale(1.03);

    box-shadow:0 0 25px rgba(255,255,255,0.3);
}

/* FOOTER */

.footer{

    position:fixed;

    bottom:15px;

    width:100%;

    display:flex;

    justify-content:center;

    align-items:center;

    gap:12px;

    color:#fff;

    font-size:1.2rem;

    font-weight:500;

    opacity:.9;
}

.footer img{

    width:50px;

    height:50px;

    object-fit:contain;

    filter:drop-shadow(0 0 6px rgba(255,255,255,0.5));
}

/* MOBILE */

@media(max-width:700px){

    main{

        width:90%;
    }

    .choiceContainer{

        flex-direction:column;

        align-items:center;
    }

    .choiceCard{

        width:100%;
    }
}

</style>

</head>

<body>

<header>
🍽 Catering Connect
</header>

<main>

<div id="welcomeTitle">
Connecting Caterers & Workers
</div>

<div id="welcomeSub">
Find work • Post events • Connect instantly
</div>

<div class="choiceContainer">

    <!-- USER -->
<!-- USER -->
<div class="choiceCard"
onclick="window.location.href='login.php?role=user'">

    <h3>👤 User</h3>
    <p>Find catering jobs easily</p>

</div>

<!-- ORGANIZER -->
<div class="choiceCard"
onclick="window.location.href='login.php?role=organizer'">

    <h3>🍽 Organizer</h3>
    <p>Post events & hire workers</p>

</div>

</div>

</main>



</body>
</html>