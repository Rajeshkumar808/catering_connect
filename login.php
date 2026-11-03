<?php
session_start();

require "db.php";
$role = $_GET['role'] ?? $_POST['role'] ?? 'user';

if (!in_array($role, ['user', 'organizer'])) {
    $role = 'user';
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    /* ================= ADMIN LOGIN ================= */



/* ADMIN LOGIN CHECK */

if(($login === "adminwe3@gmail.com" && $password === "admin1234")

){

    $_SESSION['admin'] = true;

    $_SESSION['user'] = "Admin";

    header("Location: admin.php");

    exit();
}



    /* ================= NORMAL USER LOGIN ================= */

 /* ================= USER / ORGANIZER LOGIN ================= */


/* USER / ORGANIZER LOGIN FROM SAME TABLE */

/* LOGIN - SAME ACCOUNT FOR USER AND ORGANIZER */

$stmt = $conn->prepare(
    "SELECT * FROM users WHERE email=? OR phone=?"
);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("ss", $login, $login);
$stmt->execute();

$result = $stmt->get_result();
$account = $result->fetch_assoc();

if ($account && password_verify($password, $account['password'])) {

    session_regenerate_id(true);

    // Same account ID for both roles
    $_SESSION['user_id'] = $account['id'];
    $_SESSION['organizer_id'] = $account['id'];

    $_SESSION['user'] = $account['username'];
    $_SESSION['organizer_name'] = $account['username'];
    $_SESSION['email'] = $account['email'];

    // Select the role requested during login
    $_SESSION['role'] = $role;

    if ($role === 'organizer') {
        header("Location: addEvent.php");
    } else {
        header("Location: searchEvents.php");
    }

    exit();

} else {

    $error = "Invalid Login";

}

$stmt->close();

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
box-sizing:border-box;
font-family:"Poppins",sans-serif;
margin:0;
padding:0;
}

/* SAME BACKGROUND AS index.php */
body{
min-height:100vh;
display:flex;
justify-content:center;
align-items:center;
background:
linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.85)),
url('https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg')
no-repeat center center fixed;
background-size:cover;
color:white;
}

/* GLASS LOGIN BOX */
.login-box{
width:350px;
padding:40px;
background:rgba(255,255,255,.12);
backdrop-filter:blur(18px);
border-radius:25px;
box-shadow:0 25px 60px rgba(0,0,0,.5);
text-align:center;
}

/* INPUT STYLE SAME AS INDEX */
input{
width:100%;
padding:12px;
margin:10px 0;
border-radius:10px;
border:none;
outline:none;
transition:all .3s ease;
color:black;
}

input::placeholder{
color:#444;
}

/* INPUT GLOW EFFECT */
input:hover{
box-shadow:0 0 10px rgba(255,255,255,.6);
}

input:focus{
box-shadow:
0 0 8px rgba(255,255,255,.7),
0 0 16px rgba(255,255,255,.5);
transform:scale(1.01);
}

/* BUTTON SAME AS INDEX */
button{
width:100%;
padding:15px;
border:none;
border-radius:25px;
font-size:1.1rem;
cursor:pointer;
color:white;
gap: 10px;
background: linear-gradient(90deg,
rgba(255,95,109,1),
rgba(255,195,113,1));
transition:all .3s ease;
}

/* BUTTON HOVER GLOW */
button:hover{
color:black;
background: radial-gradient(circle,
rgba(209,35,50,1),
rgba(255,5,5,1));

box-shadow:
0 0 10px rgb(202,202,201),
0 0 20px rgb(204,201,201),
0 0 35px rgb(201,198,198);

transform:translateY(-2px);
}

a{
color:#ffc371;
text-decoration:none;
}

.error{
color:#ff4d4d;
margin-bottom:10px;
} /* SAME BUTTON STYLE AS index.php */

/* LOGIN BUTTON */

button{

    width:100%;
    padding:14px;

    border:none;
    border-radius:30px;

    font-size:16px;
    font-weight:600;

    color:white;
    cursor:pointer;

    background:linear-gradient(135deg,#ff6a00,#ee0979);

    box-shadow:
        0 5px 15px rgba(238,9,121,.25);

    transition:
        transform .25s ease,
        box-shadow .25s ease,
        background .25s ease;
}

/* HOVER EFFECT */

button:hover{

    background:linear-gradient(135deg,#ff512f,#dd2476);

    transform:translateY(-3px);

    box-shadow:
        0 10px 22px rgba(221,36,118,.35);

    color:white;
}

/* CLICK EFFECT */

button:active{

    transform:scale(.98);
}

/* INPUT BOX STYLE — SAME AS INDEX */

input, select {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border-radius: 12px;
  border: none;
  outline: none;
  font-size: 16px;
  color: #000;
  transition: box-shadow 0.3s ease, transform 0.2s ease, border 0.3s ease;
}

/* Placeholder color */
input::placeholder {
  color: #3b3838;
  opacity: 1;
}

/* Hover glow */
input:hover, select:hover {
  border: 1.5px solid black;
  box-shadow: 0 0 10px rgba(255,255,255,0.6);
}

/* Focus glow when typing */
input:focus, select:focus {
  border: 1.5px solid black;
  box-shadow:
    0 0 8px rgba(255,255,255,0.7),
    0 0 16px rgba(255,255,255,0.5);
  transform: scale(1.01);
}

/* Date & time text dark */
input[type="date"],
input[type="time"] {
  color: #000;
}
body {
  background:
    linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.85)),
    url('https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg')
    no-repeat center center fixed;
  background-size: cover;
  font-family: "Poppins", sans-serif;
}

.btn-group{
display:flex;
flex-direction:column; /* vertical buttons */
gap:15px; /* ⭐ THIS CREATES SPACE */
margin-top:10px;
}
</style>

</head>

<body>

<div class="login-box">

<h2><?= ucfirst($role) ?> Login</h2>

<?php
if(isset($error)){
echo "<p class='error'>$error</p>";
}
?>

<form method="POST" autocomplete="off">

<input type="text" name="login" placeholder="Email or Phone Number" required autocomplete="off" >

<input type="password" name="password" placeholder="Password" required autocomplete="new-password">
<input type="hidden" name="role"
       value="<?= htmlspecialchars($role) ?>">
<div class="btn-group">
    <button type="submit">Login</button>

    
</div>


</form>

<p>Don't have account?</p>

<a href="register.php?role=<?= urlencode($role) ?>"
   style="text-decoration: underline;">
   Register
</a>

</div>

</body>
</html>