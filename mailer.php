
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';

function sendEventEmail($email, $event)
{
   $mail = new PHPMailer(true);

$mail->Timeout = 10;
$mail->SMTPTimeout = 10;
$mail->Timelimit = 15;
$mail->SMTPKeepAlive = false;

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'caterringconnect@gmail.com';
$mail->Password = 'YOUR_16_DIGIT_APP_PASSWORD';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
    try {

        // Gmail SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // YOUR GMAIL
       $mail->Username = 'caterringconnect@gmail.com';

$mail->Password = 'YOUR_16_DIGIT_APP_PASSWORD';

$mail->setFrom(
    'caterringconnect@gmail.com',
    'Catering Connection'
);
        // Receiver
        $mail->addAddress($email);

        // Email content
        $mail->isHTML(true);

        $mail->Subject = 'New Catering Event Available!';

        $eventName = htmlspecialchars($event['event_name']);
        $eventDate = htmlspecialchars($event['date']);
        $eventTime = htmlspecialchars($event['time']);
        $location = htmlspecialchars($event['location']);
        $workers = htmlspecialchars($event['workers_needed']);

        $mail->Body = "
            <h2>🎉 New Catering Event!</h2>

            <p>A new event has been added to Catering Connection.</p>

            <h3>$eventName</h3>

            <p><b>Date:</b> $eventDate</p>
            <p><b>Time:</b> $eventTime</p>
            <p><b>Location:</b> $location</p>
            <p><b>Workers Required:</b> $workers</p>
<p><b>Organizer:</b> " . htmlspecialchars($event['caterer_name']) . "</p>
<p><b>Contact:</b> " . htmlspecialchars($event['caterer_contact']) . "</p>
            <br>

            <p>Visit Catering Connection to book this event.</p>
        ";

        $mail->AltBody = "New event: $eventName | Date: $eventDate | Location: $location";

        $mail->send();

        return true;

    } catch (Exception $e) {

        error_log("Email Error: " . $mail->ErrorInfo);

        return false;
    }
}
