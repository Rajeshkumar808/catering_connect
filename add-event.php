<?php
include "db.php";

/* ADD EVENT */
if (isset($_POST['action']) && $_POST['action'] === 'add') {

    $name     = $_POST['eventName'] ?? '';
    $location = $_POST['eventLocation'] ?? '';
    $date     = $_POST['eventDate'] ?? '';
    $time     = $_POST['eventTime'] ?? '';
    $workers  = max(1, intval($_POST['eventWorkers'] ?? 1));
    $caterer  = $_POST['catererName'] ?? '';
    $contact  = $_POST['catererContact'] ?? '';

    if (!preg_match('/^[0-9]{10}$/', $contact)) {
        echo "invalid_contact";
        exit;
    }

    $sql = "INSERT INTO events 
      (event_name, location, event_date, event_time, workers, caterer, contact)
      VALUES 
      ('$name','$location','$date','$time','$workers','$caterer','$contact')";

    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo "error";
    }
    exit;
}

/* SEARCH EVENTS */
if (isset($_GET['action']) && $_GET['action'] === 'search') {

    $location = $_GET['location'] ?? '';
    $date     = $_GET['date'] ?? '';

    $sql = "SELECT * FROM events WHERE 1";

    if ($location !== '') {
        $sql .= " AND location='$location'";
    }
    if ($date !== '') {
        $sql .= " AND event_date='$date'";
    }

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "
            <div class='event-card'>
              <strong>{$row['event_name']}</strong><br>
              📍 {$row['location']}<br>
              📅 {$row['event_date']} {$row['event_time']}<br>
              👥 Workers: {$row['workers']}<br>
              📞 {$row['contact']}
            </div>";
        }
    } else {
        echo "No events found";
    }
    exit;
}
?>
