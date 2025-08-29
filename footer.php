<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Crabie - Premium Winter Jackets</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* CSS Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            background-color: #fff;
            color: #1d1d1f;
        }

        .cta {
            text-align: center;
            padding: 2rem 1rem;
            background: #1d1d1f;
            color: #fff;
        }

        .cta h2 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .cta p {
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .shop-btn {
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            background-color: white;
            color: #1d1d1f;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .shop-btn:hover {
            background-color: #f1f1f1;
        }

        .footer {
            background: #1d1d1f;
            color: #ccc;
            padding: 2rem 1rem;
            font-size: 0.9rem;
        }

        .footer-container {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .footer-brand h3 {
            color: white;
            margin-bottom: 0.5rem;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .footer-links h4 {
            color: white;
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: #ccc;
            text-decoration: none;
            margin-bottom: 0.25rem;
            display: block;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            text-decoration: underline;
            color: #fff;
        }

        .footer-subscribe {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .footer-subscribe form {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .footer-subscribe input {
            padding: 0.6rem;
            border-radius: 8px;
            border: none;
            width: 100%;
        }

        .footer-subscribe button {
            padding: 0.6rem 1.2rem;
            background-color: white;
            color: #1d1d1f;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }

        .footer-socials a {
            margin-top: 1rem;
            margin-right: 1rem;
            font-size: 1.2rem;
            color: #ccc;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #888;
        }

        @media (min-width: 768px) {
            .footer-container {
                flex-direction: row;
                justify-content: space-between;
                align-items: flex-start;
            }

            .footer-links {
                flex-direction: row;
                gap: 3rem;
            }

            .footer-subscribe form {
                flex-direction: row;
                align-items: center;
            }

            .footer-subscribe input {
                flex: 2;
            }

            .footer-subscribe button {
                flex: 1;
            }

            .shop-btn {
                width: auto;
            }
        }
    </style>
</head>
<body>

<!-- CTA -->
<section class="cta">
    <h2>Ready to Upgrade Your Wardrobe?</h2>
    <p>
        Join thousands of satisfied customers who trust Crabie for their winter wardrobe.
        Experience the perfect blend of style, comfort, and quality.
    </p>
    <a href="shop" class="shop-btn">Shop Collection →</a>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-brand">
            <h3>CRABIE</h3>
            <p>Premium winter jackets crafted for the modern lifestyle. Stay warm, stay stylish.</p>
            <p><a href="mailto:store@crabie.com">store@crabie.com</a></p>
            <p><a href="tel:+918123443560">+91 8123443560</a></p>
            <p>Bengaluru, Karnataka, India</p>
            <div class="footer-socials">
                <a href="#"><i class="fa-brands fa-facebook"></i></a>
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-linkedin"></i></a>
            </div>
        </div>

        <div class="footer-links">
            <div>
                <h4>Company</h4>
                <a href="contact">Contact</a>
                <a href="about">About Us</a>
                <a href="careers">Careers</a>
                <a href="cookie">Cookie Policy</a>
            </div>

            <div>
                <h4>Support</h4>
                <a href="press">Press</a>
                <a href="size">Size Guide</a>
                <a href="privacy">Privacy Policy</a>
                <a href="shipping">Shipping Info</a>
            </div>

            <div>
                <h4>Legal</h4>
                <a href="terms">Terms Of Service</a>
                <a href="disclaimer">Disclaimer</a>
                <a href="returns">Returns</a>
                <a href="faq">FAQ</a>
            </div>
        </div>

        <div class="footer-subscribe">
            <label for="email">Stay updated:</label>
            <form method="POST">
                <input type="email" name="subscriber_email" placeholder="Enter your email" required>
                <button type="submit" name="subscribe_btn">Subscribe</button>
            </form>
        </div>
    </div>

    <?php
        include_once('dataBase.php');
        include_once('functions.php');

        if (isset($_POST['subscribe_btn'])) {
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
                $adminEmail = 'crabieorder@gmail.com';
                $adminSubject = "New Subscriber on Crabie";
                $adminBody = "
                    <p>Hi Admin,</p>
                    <p>You have a new subscriber:</p>
                    <p><strong>Email:</strong> {$email}</p>
                    <p>Cheers,<br>Your Website</p>
                ";
                sendMail($adminEmail, $adminSubject, $adminBody);
                echo "<script>alert('Thanks for subscribing!'); window.location.href='index.php';</script>";
            } else {
                echo "<script>alert('Failed to send email. Try again later.'); window.location.href='index.php';</script>";
            }
        }
    ?>
    
    <div class="footer-bottom">
        <p>&copy; 2024 Crabie. All rights reserved. Made with <span style="color: red">❤</span> in India.</p>
    </div>
</footer>

</body>
</html>
