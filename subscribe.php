<?php

    include_once('dataBase.php');
    include_once('functions.php');

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['subscriber_email'])) {
        $rawEmail = trim($_POST['subscriber_email']);
        
        if (!filter_var($rawEmail, FILTER_VALIDATE_EMAIL)) {
            die("Invalid email address.");
        }

        $email = $rawEmail;

        $subject = "Thanks for Subscribing to Crabie";
        $body = "
            <p>Hey there,</p>
            <p>Thanks for joining <strong>Crabie</strong>! You're now on the list for the coziest stylish updates and exclusive jacket offers.</p>
            <p>Stay warm,<br>Team Crabie</p>
            <p><a href='https://crabie.in'>Visit our store</a></p>
        ";

        if (sendMail($email, $subject, $body)) {
            echo "<script>alert('Thanks for subscribing!'); window.location.href='index.php';</script>";
        } 
        else {
            echo "<script>alert('Failed to send email. Try again later.'); window.location.href='index.php';</script>";
        }
    } 
    else {
        header("Location: index.php");
        exit;
    }

?>
