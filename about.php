<?php

    include("header.php");

?>
<style>
    /* about story */
    .brand-story-section {
        padding: 80px 20px;
        background-color: #fff;
        text-align: center;
    }

    .brand-container {
        max-width: 900px;
        margin: 0 auto;
        color: #333;
    }

    .brand-container h1 {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #1a1a1a;
    }

    .brand-container h2 {
        font-size: 18px;
        font-weight: 500;
        color: #555;
        margin-bottom: 15px;
    }

    .divider {
        width: 40px;
        height: 2px;
        background-color: #e91e63;
        margin: 20px auto;
    }

    .brand-container p {
        font-size: 16px;
        line-height: 1.7;
        color: #444;
        margin-bottom: 20px;
    }
    /* bg image about */
    .about-background {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url('./images/about-2nd-jacket.avif') no-repeat center center/cover;
        filter: blur(1px);
        z-index: -1;
        height: 100% ;
    }

    /* slide show */
        
    .slideshow-container {
        position: relative;
        max-width: 800px;
        margin: auto;
        overflow: hidden;
        height: 10%;
        width: 40%;
    }

    img {
        width: 100%;
        height: auto;
        display: block;
    }
</style>
    <section class="brand-story-section">
        <div class="brand-container">
            <h1>The Crabie Story</h1>
            <h2>India’s premium winterwear destination</h2>
            <div class="divider"></div>
            <p>
                    Since our inception, Crabie has redefined the winterwear experience in India. From sourcing ethically made, 
                    fashion-forward jackets to offering sleek designs that cater to modern comfort and style, we’ve built more than just a brand — 
                    we’ve built a movement. With thousands of satisfied customers, an expanding presence across metros, and a fast-growing 
                    community of fashion-conscious individuals, we’re committed to bringing premium warmth to your doorstep.
            </p>
            <p>
                    The name “Crabie” reflects resilience, boldness, and a spirit of pushing through the cold with confidence. Our collections are 
                    crafted with care to suit diverse climates, personalities, and lifestyles. Whether you're commuting through city chill or exploring 
                    mountain trails, Crabie stands by your side — or rather, on your shoulders. Because warmth is more than a feeling; it's a style 
                    statement. Welcome to Crabie, where fashion meets function.
            </p>
        </div>
    </section>

    <!-- Blurred Background Image -->
    <div class="about-background"></div>
        
    <!-- to get space for the blurred background image -->
        
    <section class="section-break">
        <br/>
    </section>
    
    <!-- section 2 -->

    <section class="brand-story-section">

        <!-- <section 2- slide show> -->
        <div class="slideshow-container">
            <div class="mySlides">
                <img src="./images/about-ss1.avif" alt="Slide 1">
            </div>
            <div class="mySlides">
                <img src="./images/about-ss2.avif" alt="Slide 2">
            </div>
            <div class="mySlides">
                <img src="./images/about-ss3.avif" alt="Slide 3">
            </div>
        </div>

        <!-- section 2 theory -->
        <div class="brand-container">
            <!-- <img src="./images/about-2nd-jacket.avif" alt="Crabie Section 2"  /> -->
            <h1>Our Mission</h1>
            <h2>Bringing warmth and style to every winter</h2>
            <div class="divider"></div>
            <p>
                At Crabie, we believe that winterwear should be more than just functional; it should be a reflection of your personality. 
                Our mission is to provide you with jackets that not only keep you warm but also make you look and feel great. We are dedicated 
                to sourcing the finest materials, ensuring ethical production practices, and delivering exceptional quality in every piece we create.
            </p>
            <p>
                We understand that winter can be harsh, but with Crabie, you can embrace the season with confidence. Our jackets are designed 
                to withstand the elements while keeping you stylish and comfortable. Whether you're navigating the urban jungle or exploring the 
                great outdoors, Crabie is your trusted companion for every winter adventure.
            </p>
        </div>
    </section>

    <?php
        include("footer.php")
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


        let slideIndex = 0;
        function showSlides() {
            const slides = document.getElementsByClassName("mySlides");
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slideIndex++;
            if (slideIndex > slides.length) {
                slideIndex = 1; 
            }
            slides[slideIndex - 1].style.display = "block";
            setTimeout(showSlides, 2000); // Change image every 2 seconds
        }
        showSlides();
    </script>        
</body>
</html>