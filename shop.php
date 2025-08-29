<?php

    include("header.php");
    
    $sort = $_GET['sort'] ?? '';

    $orderBy = "ORDER BY RAND()"; // Default random

    switch ($sort) {
        case 'price_asc':
            $orderBy = "ORDER BY min_price ASC";
            break;
        case 'price_desc':
            $orderBy = "ORDER BY min_price DESC";
            break;
        case 'name_asc':
            $orderBy = "ORDER BY p.name ASC";
            break;
        case 'name_desc':
            $orderBy = "ORDER BY p.name DESC";
            break;
    }
    
    $query = "
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
        $orderBy
    ";

    $statement = $connection->prepare($query);
    $statement->execute();
    $products = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

    <!-- Banner -->
    <section class="shop-banner">
        <img src="./images/shop-banner.png" alt="Shop Banner"  class="banner-img">
    </section>

    <!-- Main Shop Section -->
    <section class="shop">
        <div class="shop-sidebar">
            <h4>Browse by</h4>
            <ul>
                <a href="shop"><li class="active">All products</li></a>
                <a href="mens-jackets"><li>Mens</li></a>
                <a href="womens-jackets"><li>Womens</li></a>
            </ul>
        </div>

        <div class="shop-content">
            <div class="shop-header">
                <h2>All products</h2>
                <div class="sort-dropdown" style="margin-top: 10px; text-align: right;">
                    <form method="GET" action="shop.php">
                        <label for="sort">Sort by:</label>
                        <select name="sort" id="sort" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc;">
                            <option value="">Default</option>
                            <option value="price_asc" <?= isset($_GET['sort']) && $_GET['sort'] === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_desc" <?= isset($_GET['sort']) && $_GET['sort'] === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="name_asc" <?= isset($_GET['sort']) && $_GET['sort'] === 'name_asc' ? 'selected' : '' ?>>Name: A to Z</option>
                            <option value="name_desc" <?= isset($_GET['sort']) && $_GET['sort'] === 'name_desc' ? 'selected' : '' ?>>Name: Z to A</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="product-grid">
                <?php

                    foreach ($products as $product) {
                        if (!$product['image_url']) continue;

                ?>
                        <div class="product-card">
                            <span class="badge black">Best Seller</span>
                            <?php
                                echo '
                                    <a href="product-page?productId='.convertData($product['product_id']).'">
                                        <img src="'.htmlspecialchars($product['image_url']).'" alt="'.htmlspecialchars($product['name']).'" />
                                    </a>
                                ';
                            ?>
                            <h4><?= htmlspecialchars($product['name']) ?></h4>
                            <p>₹<?= number_format($product['min_price'], 2) ?></p>
                            <?php
                                echo '
                                    <a href="product-page?productId='.convertData($product['product_id']).'"><button class="add-btn">View</button></a>
                                ';
                            ?>
                        </div>
                <?php

                    }

                ?>
            </div>
        </div>
    </section>

    <!-- Subscribe -->
    <section class="subscribe">
        <h3>STAY UPDATED</h3>
        <p>Be the first to know about new arrivals, exclusive offers, and style tips.</p>
        <div class="subscribe-form">
            <form method="POST">
                <input type="email" name="subscriber_email" placeholder="Enter your email" required>
                <button type="submit" name="subscribe_btn">Subscribe</button>
            </form>
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
                } 
                else {
                    echo "<script>alert('Failed to send email. Try again later.'); window.location.href='index.php';</script>";
                }
            }

        ?>
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