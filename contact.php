<?php

    include("header.php")

?>

    <!-- Blurred Background Image -->
    <div class="background"></div>

    <!-- Contact Form Section -->
    <div class="form-wrapper">
        <div class="form-container">
        <h1>Get in Touch</h1>
        <p>We'd love to hear from you about our jackets.</p>

        <form class="contact-form" action="https://formsubmit.co/crabie.store@gmail.com" method="POST">
            <!-- Disable CAPTCHA & redirect options -->
            <input type="hidden" name="_captcha" value="false">
            <input type="hidden" name="_template" value="box">

            <input type="hidden" name="_next" value="http://crabie/contact">

            <input type="text" name="name" placeholder="Your first name here" required />
            <input type="email" name="email" placeholder="Your email address here" required />
            <textarea name="message" placeholder="Type your message here" required></textarea>

            <button type="submit" class="submit">Submit your inquiry now</button>
        </form>
        </div>
    </div>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        <div class="contact-wrapper">
        
            <!-- Left: Contact Info -->
            <div class="contact-info">
                <h1>Contact Us</h1>
                <p>
                    We are located in India, providing premium winter jackets and apparel.
                    Reach out for inquiries or support regarding our products.
                </p>
        
                <h3>Location</h3>
                <ul>
                    <li>
                        <a href="https://maps.app.goo.gl/MS3gV17As7Xo8Y9A6" target="_blank">
                            H street, New Guddadahalli, Mysore Road, Bengaluru - 560026
                        </a>
                    </li>
                    <li>
                        <a href="https://maps.app.goo.gl/z21vRQTyLZPRyaAr8" target="_blank">
                            37, 5th Main, Hegganahalli Cross, Bengaluru - 560091
                        </a>
                    </li>
                </ul>
        
                <h3>Hours</h3>
                <p>9 AM - 10 PM</p>
            </div>
        
            <!-- Right: Embedded Map -->
            <div class="map-container">
                <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3888.20573573617!2d77.54230287442762!3d12.958683115162636!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bae3e0ea9940987%3A0x865c8660cc079526!2sCrabie!5e0!3m2!1sen!2sin!4v1752335790570!5m2!1sen!2sin"
                width="100%"
                height="100%"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>

    <?php
        include("footer.php");
    ?>

    <script>
        // JavaScript to highlight active link
        const links = document.querySelectorAll('.nav-link');
        const currentPage = location.pathname.split('/').pop();

        links.forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>
