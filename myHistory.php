```php
<?php
session_start();
require_once __DIR__ . "/db.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

// Fetch booked event details
$sql = "
    SELECT
        b.id AS booking_id,
        b.user_id,
        b.event_id,
        b.amount,
        e.id AS actual_event_id,
        e.event_name,
        e.location,
        e.date AS event_date,
        e.time AS event_time
    FROM bookings AS b
    LEFT JOIN events AS e ON b.event_id = e.id
    WHERE b.user_id = ?
    ORDER BY b.id DESC
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Safely display values
function safeDisplay($value)
{
    if ($value === null || trim((string)$value) === '') {
        return "Not available";
    }

    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Bookings - Catering Connect</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            padding: 30px 15px;
            color: white;

            background:
                linear-gradient(rgba(0,0,0,.78), rgba(0,0,0,.88)),
                url("https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg")
                center / cover fixed no-repeat;
        }

        .container {
            max-width: 1000px;
            width: 100%;
            margin: auto;
        }

        h1 {
            text-align: center;
            color: #ffc371;
            margin-bottom: 8px;
            font-size: 30px;
        }

        .subtitle {
            text-align: center;
            color: #ddd;
            margin-bottom: 30px;
        }

        .event-card {
            padding: 24px;
            margin-bottom: 22px;
            border-radius: 18px;

            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.18);

            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);

            box-shadow: 0 10px 30px rgba(0,0,0,.3);
            transition: transform .3s ease;
        }

        .event-card:hover {
            transform: translateY(-4px);
        }

        .event-card h2 {
            color: #ffc371;
            margin-bottom: 18px;
            font-size: 23px;
            overflow-wrap: anywhere;
        }

        .details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .detail {
            background: rgba(255,255,255,.07);
            padding: 14px;
            border-radius: 10px;
            overflow-wrap: anywhere;
        }

        .label {
            display: block;
            font-size: 13px;
            color: #ccc;
            margin-bottom: 5px;
        }

        .value {
            font-size: 15px;
            font-weight: 500;
        }

        .payment {
            color: #90ee90;
            font-weight: 700;
        }

        .warning {
            margin-top: 15px;
            padding: 12px;
            border-radius: 10px;
            background: rgba(255,100,100,.15);
            color: #ffaaaa;
            font-size: 13px;
        }

        .empty {
            text-align: center;
            background: rgba(255,255,255,.1);
            padding: 30px;
            border-radius: 15px;
        }

        .empty h2 {
            margin-bottom: 10px;
        }

        .back {
            display: block;
            width: fit-content;
            margin: 30px auto 10px;
            padding: 12px 28px;

            border-radius: 30px;
            background: linear-gradient(135deg,#ff6a00,#ee0979);

            color: white;
            text-decoration: none;
            font-weight: 600;

            transition: .25s;
        }

        .back:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(238,9,121,.3);
        }

        @media (max-width: 600px) {
            body {
                padding: 20px 12px;
                background-attachment: scroll;
            }

            h1 {
                font-size: 25px;
            }

            .event-card {
                padding: 17px;
            }

            .event-card h2 {
                font-size: 20px;
            }

            .details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h1>📋 My Bookings</h1>
    <p class="subtitle">Your booked event details</p>

    <?php if ($result->num_rows > 0): ?>

        <?php while ($row = $result->fetch_assoc()): ?>

            <div class="event-card">

                <h2>
                    🎉 <?= safeDisplay($row['event_name']) ?>
                </h2>

                <?php if ($row['actual_event_id'] === null): ?>

                    <div class="warning">
                        The original event was not found.
                        Event ID: <?= (int)$row['event_id'] ?>
                    </div>

                <?php else: ?>

                    <div class="details">

                        <!-- Event Location -->
                        <div class="detail">
                            <span class="label">📍 Location</span>
                            <span class="value">
                                <?= safeDisplay($row['location']) ?>
                            </span>
                        </div>

                        <!-- Event Date -->
                        <div class="detail">
                            <span class="label">📅 Event Date</span>
                            <span class="value">
                                <?php
                                $date = $row['event_date'];

                                if ($date && $date !== '0000-00-00') {
                                    $timestamp = strtotime($date);

                                    echo $timestamp !== false
                                        ? htmlspecialchars(date('d M Y', $timestamp))
                                        : 'Not available';
                                } else {
                                    echo 'Not available';
                                }
                                ?>
                            </span>
                        </div>

                        <!-- Event Time -->
                        <div class="detail">
                            <span class="label">⏰ Event Time</span>
                            <span class="value">
                                <?php
                                $time = $row['event_time'];

                                if ($time) {
                                    $timestamp = strtotime($time);

                                    echo $timestamp !== false
                                        ? htmlspecialchars(date('h:i A', $timestamp))
                                        : safeDisplay($time);
                                } else {
                                    echo 'Not available';
                                }
                                ?>
                            </span>
                        </div>

                        <!-- Payment -->
                        <div class="detail">
                            <span class="label">💳 Payment</span>
                            <span class="value payment">
                                ₹<?= number_format((float)$row['amount'], 2) ?>
                            </span>
                        </div>

                    </div>

                <?php endif; ?>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div class="empty">
            <h2>No Bookings Found</h2>
            <p>You haven't booked any events yet.</p>
        </div>

    <?php endif; ?>

    <a href="viewEvents.php" class="back">
        ⬅ Back to Events
    </a>

</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
```
