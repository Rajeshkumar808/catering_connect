<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Help</title>
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
  align-items:center;
  justify-content:center;

  background:
linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.85)),
  url("https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg")
  no-repeat center center fixed;
  background-size:cover;
}

/* MAIN CARD */

.container{
  width:90%;
  max-width:800px;
  padding:35px;
  border-radius:25px;
  backdrop-filter: blur(18px);
  background:rgba(255,255,255,.12);
  box-shadow:0 25px 60px rgba(0,0,0,.5);

  height:85vh;              /* fixed height */
  overflow-y:auto;          /* scroll enabled */

  /* 🔥 hide scrollbar */
  scrollbar-width: none;    /* Firefox */
}

.container::-webkit-scrollbar{
  display:none;             /* Chrome */
}

.container::-webkit-scrollbar-track {
  background: transparent;
}

.container::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.4);
  border-radius:10px;
}

.container::-webkit-scrollbar-thumb:hover {
  background: rgba(255,255,255,0.7);
}

.contactBtn{
  margin-top:15px;
}

.backBtn{
  margin-top:15px;
}
h2{
  color:white;
  text-align:center;
  margin-bottom:20px;
  font-size:2rem;
}

h3{
  color:#ffc371;
  margin-top:18px;
  margin-bottom:8px;
  font-weight:600;
}

/* TEXT */

p{
  color:#ddd;
  line-height:1.6;
  font-size:15px;
}

/* SUPPORT TEXT */

b{
  color:#fff;
}

/* BACK BUTTON */

.backBtn{
  display:block;
  margin-top:25px;
  text-align:center;
  
  background:linear-gradient(90deg,#ff5f6d,#ffc371);
  color:white;
  
  padding:12px;
  border-radius:25px;
  text-decoration:none;
  font-weight:500;
  
  transition:.3s ease;
}

.backBtn:hover{
  color:black;
  background:radial-gradient(circle,#d12332,#ff0505);
  box-shadow:
    0 0 10px rgb(202,202,201),
    0 0 20px rgb(204,201,201);
  transform:translateY(-2px);
}
.faq-box{
  border:1px solid rgba(255,255,255,0.3);
  border-radius:15px;
  padding:15px;
  margin-bottom:15px;
  background:rgba(255,255,255,0.05);
  transition:0.3s ease;
}

/* Hover effect 🔥 */
.faq-box:hover{
  border:1px solid #2c2a26;
  box-shadow:
    0 0 10px rgba(26, 25, 23, 0.6),
    0 0 20px rgba(33, 32, 30, 0.3);
  transform:translateY(-3px);
}
</style>
</head>

<body>
<div class="container">

<div class="content">

<h2>Help / FAQ</h2>


<div class="faq-box">
  <h3>What is Catering Connect?</h3>
  <p>
  Catering Connect is a platform where individuals can add event details. 
  We connect people who organize events with caterers or users who are 
  looking for collaboration opportunities. It helps event organizers and 
  service providers easily find and contact each other for event-related work.
  </p>
</div>

<div class="faq-box">
  <h3>How to use?</h3>
  <p>
  The process begins with selecting whether you are a <b>User</b> or a 
  <b>Caterer</b>. After that you can login if you already have an account 
  or register if you are new. Once logged in, you can search for events 
  or add new event details easily.
  </p>
</div>

<div class="faq-box">
  <h3>How do I find catering?</h3>
  <p>
  After logging in, users can search for events by location or specific 
  date and view detailed event information. This allows them to contact 
  event organizers or participants easily.
  </p>
</div>

<div class="faq-box">
  <h3>How do I complain about an event I attended?</h3>
  <p>
  All events are organized and managed by third-party event organizers. 
  Please contact the event organizer directly to make your complaint. 
  We are unable to mediate these situations.
  </p>
</div>

<div class="faq-box">
  <h3>Can I ask a question about an event?</h3>
  <p>
  Yes. Any queries regarding event details should be sent directly to 
  the event organizer through their website or social media.
  </p>
</div>

<div class="faq-box">
  <h3>How can I contact an event organiser?</h3>
  <p>
  You can contact the event organizer through the contact details provided 
  in the event information. Usually this includes their website or social media.
  </p>
</div>
<br>
</div>
<div class="btn-group">
    <a class="backBtn" href="mailto:support@cateringconnect.com">Contact Us</a>
    <a class="backBtn" href="javascript:history.back()">⬅ Back</a>
</div>
</div>
</body>
</html>