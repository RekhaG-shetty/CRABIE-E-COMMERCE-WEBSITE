<?php
    include("header.php");
?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-text">
        <div class="hero-text-up">
            <p>Shop Now for Style</p>
            <h1>
                <span class="white-bold">WINTER</span>
                <br>
                <span class="black-bold">REDEFINED</span>
            </h1>
        </div>
        <div class="hero-text-mid">
            <p>
                Premium winter jackets crafted for the modern lifestyle. <br> Stay warm, stay stylish, stay confident.
            </p>
        </div>
      
        <a href="shop"><button class="shop-collection">Shop Collection →</button></a>
    </div>
  </section>

  <!-- Icons -->
    <div style="background-color: #F9FAFB; margin-top: 35px;">
        <section class="icons">
            <div>
                <i class="fas fa-shield-alt"></i>
                <p>Secure Payment</p>
            </div>
            <div>
                <i class="fas fa-truck"></i>
                <p>Free Shipping</p>
            </div>
            <div>
                <i class="fas fa-undo"></i>
                <p>Easy Returns</p>
            </div>
        </section>

        <!-- Explore Section -->
        <section class="explore">
            <div class="explore-box">
                <img src="./images/explore1.png" alt="Winter Jackets">
                <div class="overlay">
                    <p style="line-height: 7;">Stylish and Premium Options</p>
                    <h1>
                        <span class="white-bold" style="font-size: xxx-large; line-height: 1;">Explore Our Winter Jackets</span>
                    </h1>
                    <a href="shop"><button class="shop-collection">Shop →</button></a>
                </div>
            </div>
            <div class="explore-box">
                <img src="./images/explore2.png" alt="Men & Women Styles">
                <div class="overlay">
                    <p style="line-height: 7;">Stylish and Premium Options</p>
                    <h1>
                        <span class="white-bold" style="font-size: xxx-large; line-height: 1;">Men's and Women's Styles</span>
                    </h1>
                    <a href="shop"><button class="shop-collection">Browse →</button></a>
                </div>
            </div>
        </section>
    </div>

    <!-- Featured Collection -->

    <?php
        $featuredQuery = "
                SELECT 
                    p.id AS product_id,
                    p.name,
                    (
                        SELECT vi.image_url 
                        FROM product_variants pv2
                        JOIN variant_images vi ON pv2.id = vi.variant_id
                        WHERE pv2.product_id = p.id
                        LIMIT 1
                    ) AS image_url,
                    (
                        SELECT MIN(pv.discounted_price) 
                        FROM product_variants pv 
                        WHERE pv.product_id = p.id
                    ) AS min_price
                FROM products p
                WHERE p.is_deleted = 0
                ORDER BY RAND()
                LIMIT 4
            ";
                
            $feaStmt = $connection->prepare($featuredQuery);
            $feaStmt->execute();
            $featuredProduct = $feaStmt->fetchAll(PDO::FETCH_ASSOC);

    ?>
    <section class="collection">
        <h2>Featured Collection</h2>
        <p>Discover our most popular winter jackets, loved by thousands across India</p>
        <div class="grid">
            <?php
            foreach ($featuredProduct as $feaProduct) {
            ?>
                <div>
                    <div class="product-card-idx">
                        <span class="badge">Best Seller</span>
                        <?php
                        echo '
                            <a href="product-page?productId='.convertData($feaProduct['product_id']).'">
                                <img src="'.htmlspecialchars($feaProduct['image_url']).'" alt="'.htmlspecialchars($feaProduct['name']).'" style="" />
                            </a>
                        ';
                        ?>
                    </div>
                    <div class="product-desc">
                        <h4><?= htmlspecialchars($feaProduct['name']) ?></h4>
                        <p><?= htmlspecialchars($feaProduct['min_price']) ?></p>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </section>

    <!-- Story Section -->
    <section class="story" style="background-color: #ECE3DB;">
        <div class="story-text" style="line-height: 3;">
            <h2 style="font-size: xxx-large;line-height: 1;">Crafted for <br> Perfection</h2>
            <br>
            <span style="line-height: 1.5;">
                <p>Every Crabie jacket is meticulously designed and crafted using premium materials. From the bustling streets of Mumbai to the serene mountains of Himachal, our jackets are your perfect companion for every adventure.</p>
            </span>
            
            <a href="about"><button class="discover-btn">Discover Our Story →</button></a>
        </div>
        <div class="story-img">
            <img src="./images/crafted.png" alt="Jacket Back View">
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <h2>LOVED BY THOUSANDS</h2>
        <p>See what our customers are saying about their Crabie experience</p>

        <div class="testimonial-grid">
            <div class="testimonial-card">
            <div class="stars">★★★★★</div>
            <p>These jackets are exceptional! Impressive quality and unique designs make them a must-have for winter fashion. Truly a game-changer for my wardrobe this season!</p>
            <div class="user-info">
                <img src="./images/user1.png" alt="Anita Sharma">
                <div>
                <strong>Anita Sharma</strong><br>
                <span>Mumbai, India</span>
                </div>
            </div>
            </div>

            <div class="testimonial-card">
            <div class="stars">★★★★★</div>
            <p>The jacket exceeded my expectations. Stylish, warm, and perfect fit. Highly recommend!</p>
            <div class="user-info">
                <img src="./images/user2.png" alt="Ravi Kumar">
                <div>
                <strong>Ravi Kumar</strong><br>
                <span>Delhi, India</span>
                </div>
            </div>
            </div>
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
    </script>
</body>
</html>
