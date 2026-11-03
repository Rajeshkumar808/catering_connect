
<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "db.php";

/* ================= ROLE ================= */

$role = $_GET['role'] ?? $_POST['role'] ?? 'user';

if (!in_array($role, ['user', 'organizer'], true)) {
    $role = 'user';
}

$error = "";

/* ================= REGISTER ================= */

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {

    $username  = trim($_POST["name"] ?? "");
    $email     = strtolower(trim($_POST["email"] ?? ""));
    $phone     = trim($_POST["phone"] ?? "");
    $email_otp = trim($_POST["email_otp"] ?? "");
    $password  = $_POST["password"] ?? "";
    $confirm   = $_POST["confirm_password"] ?? "";

    /* ================= VALIDATION ================= */

    if (
        empty($username) ||
        empty($email) ||
        empty($phone) ||
        empty($email_otp) ||
        empty($password) ||
        empty($confirm)
    ) {
        $error = "All fields are required";
    }

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    }

    elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $error = "Phone number must be exactly 10 digits";
    }

    elseif (!isset($_SESSION["email_otp"])) {
        $error = "Generate Email OTP first";
    }

    elseif (
        !isset($_SESSION["email_otp_email"]) ||
        strtolower(trim($_SESSION["email_otp_email"])) !== $email
    ) {
        $error = "Email OTP was generated for a different email";
    }

    elseif ($email_otp !== (string)$_SESSION["email_otp"]) {
        $error = "Invalid Email OTP";
    }

    elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    }

    elseif ($password !== $confirm) {
        $error = "Passwords do not match";
    }

    else {

        /* ================= CHECK EXISTING ACCOUNT ================= */

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email=? OR phone=?"
        );

        if (!$check) {
            die("SQL Error: " . $conn->error);
        }

        $check->bind_param("ss", $email, $phone);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $error = "Email or Phone already exists";

        } else {

            /* ================= HASH PASSWORD ================= */

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            /* ================= INSERT ACCOUNT ================= */

            $stmt = $conn->prepare(
                "INSERT INTO users
                (username, email, phone, password, role)
                VALUES (?, ?, ?, ?, ?)"
            );

            if (!$stmt) {
                die("SQL Error: " . $conn->error);
            }

            $stmt->bind_param(
                "sssss",
                $username,
                $email,
                $phone,
                $hashed,
                $role
            );

            if ($stmt->execute()) {

                /* ================= CLEAR OTP ================= */

                unset($_SESSION["email_otp"]);
                unset($_SESSION["email_otp_email"]);

                /* ================= SUCCESS ================= */

                $_SESSION["success"] = "Registration Successful!";

                /* ================= REDIRECT ================= */

                header("Location: login.php?role=" . urlencode($role));
                exit();

            } else {

                $error = "Registration Failed: " . $stmt->error;

            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Register</title>

<style>



@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Poppins, sans-serif;
}

body {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;

    background:
        linear-gradient(rgba(0,0,0,.6), rgba(0,0,0,.8)),
        url('https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg');

    background-size: cover;
    background-position: center;
    background-attachment: fixed;
}

/* CONTAINER */

.container {
    width: 420px;
    max-width: 100%;
    padding: 28px;

    border-radius: 20px;
    background: rgba(255,255,255,.12);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);

    color: white;
    box-shadow: 0 8px 32px rgba(0,0,0,.25);
}

/* HEADING */

h2 {
    text-align: center;
    margin-bottom: 22px;
    font-size: 25px;
    font-weight: 600;
}

/* FORM */

form {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

/* ALL INPUT FIELDS */

input {
    width: 100%;
    padding: 13px 15px;

    border: 1px solid rgba(255,255,255,.15);
    outline: none;
    border-radius: 12px;

    background: rgba(255,255,255,.95);
    color: #222;

    font-size: 14px;

    box-shadow: none;
    transform: none;
    transition: none;
}

/* REMOVE INPUT HOVER EFFECT */

input:hover {
    border: 1px solid rgba(255,255,255,.15);
    box-shadow: none;
    outline: none;
    transform: none;
}

/* REMOVE INPUT FOCUS EFFECT */

input:focus,
input:active {
    border: 1px solid rgba(255,255,255,.15);
    box-shadow: none;
    outline: none;
    transform: none;
}

input::placeholder {
    color: #777;
}

/* MAIN BUTTON */

.main-btn {
    width: 100%;
    padding: 13px;

    border: none;
    border-radius: 25px;

    background: linear-gradient(
        90deg,
        rgba(255,95,109,1),
        rgba(255,195,113,1)
    );

    color: white;
    font-size: 15px;
    font-weight: 500;

    cursor: pointer;
    margin-top: 5px;
    transition: .3s ease;
}

.main-btn:hover {
    transform: translateY(-2px);
    box-shadow:
        0 0 10px rgba(255,255,255,.5),
        0 0 20px rgba(255,255,255,.3);
}

/* ERROR */

.error-box {
    background: #ff4d4d;
    padding: 11px;
    border-radius: 10px;
    margin-bottom: 18px;
    text-align: center;
    font-size: 14px;
}

/* PARAGRAPH */

p {
    text-align: center;
    margin-top: 18px;
    font-size: 14px;
}

/* LINKS */

a {
    color: #ffc371;
    text-decoration: none;
    font-weight: 500;
}

a:hover {
    text-decoration: underline;
}

/* EMAIL + SEND OTP */

.input-group {
    display: flex;
    gap: 10px;
    align-items: center;
    width: 100%;
}

.input-group input {
    flex: 1;
    min-width: 0;
}

.small-btn {
    flex-shrink: 0;
    padding: 13px 18px;
    border: none;
    border-radius: 25px;

    background: linear-gradient(
        90deg,
        rgba(255,95,109,1),
        rgba(255,195,113,1)
    );

    color: white;
    font-size: 14px;
    font-weight: 500;

    cursor: pointer;
    transition: .3s ease;
}

.small-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 0 15px rgba(255,195,113,.5);
}

/* OTP CONTAINER */

.otp-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin: 15px 0;
    width: 100%;
}

/* OTP BOXES */

.otp-input {
    width: 48px;
    height: 52px;
    flex: 1;
    max-width: 48px;
    min-width: 0;

    padding: 0;

    text-align: center;
    font-size: 22px;
    font-weight: 600;

    border: 1px solid rgba(255,255,255,.3);
    border-radius: 12px;

    background: rgba(255,255,255,.95);
    color: #222;

    outline: none;
    box-shadow: none;
    transform: none;
    transition: none;
}

/* OTP HOVER */

.otp-input:hover {
    border: 1px solid rgba(255,255,255,.3);
    box-shadow: none;
    outline: none;
    transform: none;
}

/* OTP FOCUS */

.otp-input:focus,
.otp-input:active {
    border: 1px solid rgba(255,255,255,.3);
    box-shadow: none;
    outline: none;
    transform: none;
}

/* MOBILE */

@media (max-width: 480px) {

    body {
        padding: 15px;
    }

    .container {
        padding: 22px;
    }

    h2 {
        font-size: 22px;
        margin-bottom: 18px;
    }

    input {
        padding: 12px;
    }

    .otp-container {
        gap: 8px;
    }

    .otp-input {
        height: 48px;
        max-width: 42px;
        font-size: 20px;
    }
}

@media (max-width: 400px) {

    .otp-container {
        gap: 7px;
    }

    .otp-input {
        width: 38px;
        height: 45px;
        flex-basis: 38px;
    }
}

</style>

</head>

<body>

<div class="container">

<h2>Create Account</h2>

<?php if (!empty($error)): ?>
    <div class="error-box">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form method="POST" action="register.php">

    <!-- ROLE -->

    <input
        type="hidden"
        name="role"
        value="<?= htmlspecialchars($role) ?>">

    <!-- NAME -->

    <input
        type="text"
        name="name"
        placeholder="Enter Your Name"
        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
        required>

    <!-- PHONE -->

    <input
        type="tel"
        id="phone"
        name="phone"
        placeholder="Phone Number"
        maxlength="10"
        pattern="[0-9]{10}"
        value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
        required>

    <!-- EMAIL + SEND OTP -->

    <div class="input-group">

        <input
            type="email"
            id="email"
            name="email"
            placeholder="Email Address"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            required>

        <button
            type="button"
            class="small-btn"
            onclick="sendEmailOTP()">

            Send OTP

        </button>

    </div>

    <!-- EMAIL OTP SIX BOXES -->

    <div class="otp-container">

        <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
        <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
        <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
        <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
        <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
        <input type="text" class="otp-input" maxlength="1" inputmode="numeric">

        <input type="hidden" name="email_otp" id="email_otp">

    </div>

    <!-- PASSWORD -->

    <input
        type="password"
        name="password"
        placeholder="Password"
        required>

    <!-- CONFIRM PASSWORD -->

    <input
        type="password"
        name="confirm_password"
        placeholder="Confirm Password"
        required>

    <!-- REGISTER -->

    <button type="submit" name="register" class="main-btn">
        Register
    </button>

</form>

<p>
    Already have account?
    <a href="login.php?role=<?= urlencode($role) ?>">
        Login
    </a>
</p>

</div>

<script>

/* ================= OTP INPUTS ================= */

const otpInputs = document.querySelectorAll(".otp-input");

otpInputs.forEach((input, index) => {

    input.addEventListener("input", function () {

        this.value = this.value.replace(/\D/g, "").slice(-1);

        if (this.value !== "" && index < 5) {
            otpInputs[index + 1].focus();
        }

        updateOTP();
    });

    input.addEventListener("keydown", function (e) {

        if (e.key === "Backspace" && this.value === "" && index > 0) {
            otpInputs[index - 1].focus();
        }

        if (e.key === "ArrowLeft" && index > 0) {
            otpInputs[index - 1].focus();
        }

        if (e.key === "ArrowRight" && index < 5) {
            otpInputs[index + 1].focus();
        }

    });

    input.addEventListener("paste", function (e) {

        e.preventDefault();

        const pasted = e.clipboardData
            .getData("text")
            .replace(/\D/g, "")
            .slice(0, 6);

        otpInputs.forEach((box, i) => {
            box.value = pasted[i] || "";
        });

        otpInputs[Math.min(pasted.length, 5)].focus();

        updateOTP();
    });

});

/* ================= UPDATE OTP ================= */

function updateOTP() {

    document.getElementById("email_otp").value =
        Array.from(otpInputs)
            .map(input => input.value)
            .join("");

}

/* ================= SEND EMAIL OTP ================= */

function sendEmailOTP() {

    const emailInput = document.getElementById("email");

    const email = emailInput.value.trim().toLowerCase();

    if (email === "") {
        alert("Enter Email Address");
        return;
    }

    if (!emailInput.checkValidity()) {
        alert("Enter a valid email address");
        return;
    }

    emailInput.value = email;

    // Clear previous OTP boxes
    otpInputs.forEach(input => input.value = "");
    updateOTP();

    let xhr = new XMLHttpRequest();

    xhr.open("POST", "send_email_otp.php", true);

    xhr.setRequestHeader(
        "Content-type",
        "application/x-www-form-urlencoded"
    );

    xhr.onload = function() {

        let response = this.responseText.trim();

        if (response === "success") {

            alert("Email OTP Sent Successfully");

        } else {

            alert(response);

        }

    };

    xhr.onerror = function() {
        alert("Unable to send OTP. Check your connection.");
    };

    xhr.send("email=" + encodeURIComponent(email));

}

/* ================= PHONE OTP ================= */

function sendPhoneOTP() {

    let phone = document.getElementById("phone").value.trim();

    if (phone === "") {
        alert("Enter Phone Number");
        return;
    }

    if (!/^[0-9]{10}$/.test(phone)) {
        alert("Phone number must be 10 digits");
        return;
    }

    let xhr = new XMLHttpRequest();

    xhr.open("POST", "send_phone_otp.php", true);

    xhr.setRequestHeader(
        "Content-type",
        "application/x-www-form-urlencoded"
    );

    xhr.onload = function() {

        let response = this.responseText.trim();

        if (response.startsWith("https://wa.me")) {
            window.open(response, "_blank");
        } else {
            alert(response);
        }

    };

    xhr.send("phone=" + encodeURIComponent(phone));

}

</script>

</body>
</html>
