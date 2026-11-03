<?php
session_start();
require "db.php";

$action = $_POST['action'] ?? $_GET['action'] ?? '';

/* ⭐ ADD EVENT */
if($action === "add"){

    $eventName = $_POST['event_name'];
    $eventLocation = $_POST['location'];
    $eventDate = $_POST['date'];
    $eventTime = $_POST['time'];
    $eventWorkers = $_POST['workers_needed'];
    $catererName = $_POST['caterer_name'];
    $catererContact = $_POST['caterer_contact'];

    $sql = "INSERT INTO events
    (eventName,eventLocation,eventDate,eventTime,eventWorkers,catererName,catererContact)
    VALUES
    ('$eventName','$eventLocation','$eventDate','$eventTime','$eventWorkers','$catererName','$catererContact')";

    if($conn->query($sql)){

        /* ⭐ Store recent event ID */
        $_SESSION['recentEventId'] = $conn->insert_id;

        echo "success";

    }else{
        echo "error";
    }
}
?>