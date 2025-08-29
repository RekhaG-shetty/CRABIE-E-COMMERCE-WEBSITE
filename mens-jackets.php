<?php

    include("header.php");
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
        JOIN product_variants pv ON pv.product_id = p.id
        WHERE p.is_deleted = 0
        AND pv.category = 'mens'
        GROUP BY p.id
        ORDER BY RAND()
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
                <a href="shop"><li>All products</li></a>
                <a href="mens-jackets"><li class="active">Mens</li></a>
                <a href="womens-jackets"><li>Womens</li></a>
            </ul>
        </div>

        <div class="shop-content">
            <div class="shop-header">
                <h2>All products</h2>
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
            <input type="email" placeholder="Enter your email" />
            <button>Subscribe</button>
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