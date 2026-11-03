<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
c:\xampp\htdocs\catering\ctr
<title>Welcome</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:"Poppins",sans-serif;
}

body{
height:100vh;
display:flex;
justify-content:center;
align-items:center;
overflow:hidden;

background:
linear-gradient(rgba(0,0,0,.7), rgba(0,0,0,.85)),
url('https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg')
no-repeat center center fixed;

background-size:cover;
}

.container{
text-align:center;
color:white;
animation:fadeIn 2s ease;
}

.logo{
font-size:4rem;
animation:bounce 2s infinite;
}

h1{
font-size:3rem;
margin-top:10px;
letter-spacing:2px;
}

p{
margin-top:10px;
font-size:1.2rem;
color:#ddd;
}

.loader{
margin:30px auto;
width:60px;
height:60px;
border:6px solid rgba(255,255,255,.3);
border-top:6px solid #fff;
border-radius:50%;
animation:spin 1s linear infinite;
}

@keyframes spin{
100%{
transform:rotate(360deg);
}
}

@keyframes bounce{
0%,100%{
transform:translateY(0);
}
50%{
transform:translateY(-15px);
}
}

@keyframes fadeIn{
from{
opacity:0;
transform:scale(.9);
}
to{
opacity:1;
transform:scale(1);
}
}
</style>

<script>
setTimeout(() => {
window.location.href = "login.php";
}, 4000);
</script>

</head>

<body>

<div class="container">

<div class="logo">🍽</div>

<h1>Catering Connect</h1>

<p>Connecting Caterers & Workers</p>

<div class="loader"></div>

</div>

</body>
</html>