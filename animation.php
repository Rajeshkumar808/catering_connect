<?php
session_start();

$_SESSION['visited_animation'] = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Loading...</title>

<style>
body {
  margin: 0;
  overflow: hidden;
  background: white;
}

/* Splash Screen */
#splash {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Center content */
.splashContent {
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* Logo animation */
.logo {
  width: 380px;
  animation: fadeZoom 4s ease-in-out;
}

@keyframes fadeZoom {
  0%   { opacity: 0; transform: scale(0.5); }
  50%  { opacity: 1; transform: scale(1); }
  100% { opacity: 0; transform: scale(1.2); }
}

/* Text */
.splashText {
  margin-top: 15px;
  font-size: 10px;
  font-weight: 500;
  color: #D4AF37;
  letter-spacing: 1px;

  opacity: 0;
  animation: textFade 3s ease-in-out;
}

@keyframes textFade {
  0%   { opacity: 0; transform: translateY(10px); }
  50%  { opacity: 1; transform: translateY(0); }
  100% { opacity: 0; transform: translateY(-10px); }
}
</style>

</head>

<body>

<div id="splash">
  <div class="splashContent">
    <img src="images/splash.jpeg" class="logo">
    <div class="splashText">Designed By V3</div>
  </div>
</div>

<script>
window.addEventListener("load", () => {
    setTimeout(() => {
        // 🔥 Redirect to index after animation
        window.location.href = "login.php";
    }, 3000);
});
</script>

</body>
</html>