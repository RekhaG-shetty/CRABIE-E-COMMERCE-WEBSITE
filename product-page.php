    <?php

        include("header.php");
        
        if (!isset($_GET['productId']) || empty($_GET['productId'])) {
            header("Location: shop");
            exit;
        }

        $encryptedId = $_GET['productId'];
        $decryptedId = convertData($encryptedId,'decrypt');

        if (!$decryptedId || !is_numeric($decryptedId)) {
            header("Location: shop");
            exit;
        }

        $query = "SELECT id FROM products WHERE id = :id AND is_deleted = 0 LIMIT 1";
        $stmt = $connection->prepare($query);
        $stmt->execute([':id' => $decryptedId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if($product){

            $query = "
                SELECT p.name, p.description
                FROM products p
                WHERE p.id = :id
            ";
            
            $stmt = $connection->prepare($query);
            $stmt->execute([':id' => $decryptedId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            // Fetch variants grouped by color
            $query = "
                SELECT v.id AS variant_id, v.color, v.category, v.size, v.stocks, v.original_price, v.discounted_price, vi.image_url
                FROM product_variants v
                LEFT JOIN variant_images vi ON v.id = vi.variant_id
                WHERE v.product_id = :id
                ORDER BY vi.id
            ";
            $stmt = $connection->prepare($query);
            $stmt->execute([':id' => $decryptedId]);

            $variants = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $color = $row['color'];
                $variants[$color]['images'][] = $row['image_url'];
                $variants[$color]['prices'][] = [
                    'original' => $row['original_price'],
                    'discounted' => $row['discounted_price'],
                    'category' => $row['category']
                ];
                $variants[$color]['stocks'][] = $row['stocks'];
                $variants[$color]['sizes'][] = $row['size'];
            }
            $colors = array_keys($variants);
            $defaultColor = $colors[0];
            $productImages = $variants[$defaultColor]['images'];
        }
        else{
            header("Location: shop.php");
        }

    ?>

    <div class="product-container">
        <div class="product-font">
            <!-- image1 section englarement -->
            <div class="product">
                <!-- Thumbnail image -->
                <div class="image-section">
                    <div class="carousel">
                        <?php 
                            foreach ($productImages as $index => $img):
                        ?>
                            <?php 
                                if ($index >= 0):
                            ?>
                                    <img src="<?= $img ?>" alt="Product Image <?= $index + 1 ?>" class="carousel-img" onmouseover="changeThumbnail(this)">
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <img id="thumbnail" src="<?= $productImages[0] ?>" alt="Product Image" class="product-image">
                </div>

                <!-- The Modal -->
                <div id="imageModal" class="modal">
                    <span class="close">&times;</span>
                    <img class="modal-content" id="modalImage">
                </div>

                <!-- product info section -->
                <div class="product-info">
                    <br>
                    <!-- product title -->
                    <div class="product-title"><?= htmlspecialchars($product['name']) ?></div>
                    <div class="product-sub-title">Warm and stylish outerwear for any season</div>
                    <div style="font-size: 20px; color: black;"><strong>Category: </strong><?= $variants[$defaultColor]['prices'][0]['category'] ?></div>

                    <!-- Product-Price -->
                    <div class="price-section">
                        <div class="special-price-label">Special price</div>
                        <div class="price-details">
                            <div class="discounted-price">
                                ₹<?= $variants[$defaultColor]['prices'][0]['discounted'] ?>
                            </div>
                            <div class="original-price">
                                ₹<?= $variants[$defaultColor]['prices'][0]['original'] ?>
                            </div>
                            <div class="discount-percent">
                                <?= 
                                    round((($variants[$defaultColor]['prices'][0]['original'] - $variants[$defaultColor]['prices'][0]['discounted'])/$variants[$defaultColor]['prices'][0]['original'])*100 )
                                ?>% off
                            </div>
                            <!-- <span class="info-icon" title="Info about discount"></span> -->
                        </div>
                    </div>
                    <br>


                    <!--  quantity -->
                    <div class="quantity-container">
                        <button class="qty-btn" id="decrease">-</button>
                        <span class="qty-display" id="quantity">1</span>
                        <button class="qty-btn" id="increase">+</button>
                    </div>
                    <br>

                    <div class="variant">
                        <!-- product-size -->
                        <div class="variant-options" style="display: flex">
                            <div class="size" style="margin-right: 50px;">
                                <div><strong>Size: </strong></div>
                                <div style="margin-bottom: 20px">
                                    <?php foreach (array_unique($variants[$defaultColor]['sizes']) as $size): ?>
                                        <button class="product-size" data-size="<?= $size ?>"><?= $size ?></button>
                                    <?php endforeach; ?>
                                    <input type="hidden" id="selectedSize" value="">
                                </div>
                            </div>

                            <!-- product-color-var -->
                            <div class="color">
                                <div class="color-var"><strong>Color: </strong></div>
                                <?php foreach ($colors as $color): ?>
                                    <button class="color-button" data-color="<?= $color ?>" style="background: <?= $color ?>;"></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    

                    <div class="buy-btns">
                        <!-- add-to-bag -->
                        <button class="add-to-bag" id="addToCartBtn">Add to Bag</button>
                        <?php
                        if(isUser()){
                        ?>
                            <!-- buy now -->
                            <button class="buy-now" id="BuyBtn">Buy Now</button>
                        <?php
                        }
                        else{
                        ?>
                            <button class="buy-now" onclick="window.location.href='./register'">Login To Buy</button>
                        <?php
                        }
                        ?>
                    </div>
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
                </div>
            </div>
            <br>
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
                    WHERE p.id != :currentId
                    AND p.is_deleted = 0
                    ORDER BY RAND()
                    LIMIT 3
                ";
                    
                $feaStmt = $connection->prepare($featuredQuery);
                $feaStmt->execute([':currentId' => $decryptedId]);
                $featuredProduct = $feaStmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <!-- You may also like -->
            <section class="related-section">
                <h2>You may also like</h2>
                <div class="product-grid">
                <?php
                    foreach ($featuredProduct as $feaProduct) {
                ?>
                        <div class="product-card">
                            <?php
                                    echo '
                                        <a href="product-page?productId='.convertData($feaProduct['product_id']).'">
                                            <img src="'.htmlspecialchars($feaProduct['image_url']).'" alt="'.htmlspecialchars($feaProduct['name']).'" style="height: 400px" />
                                        </a>
                                    ';
                                ?>
                            <h4><?= htmlspecialchars($feaProduct['name']) ?></h4>
                            <p><?= htmlspecialchars($feaProduct['min_price']) ?></p>
                            <?php
                                echo '
                                        <a href="product-page?productId='.convertData($feaProduct['product_id']).'"><button class="add-btn">View</button></a>
                                    ';
                            ?>
                        </div>
                <?php 
                    }
                ?>
                </div>
            </section>
            <!-- Desc -->
            <div style="background-color: #F9FAFB;">
                <div class="description">
                    <div class="section-title"><strong>Product Description</strong></div>
                    <p style="text-align: justify;">
                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                    </p>
                </div>
            </div>
        </div>
        <hr>
    </div>

        <?php
            include("footer.php")
        ?>

        <!-- quantity -->
        <script>
            const increaseBtn = document.getElementById('increase');
            const decreaseBtn = document.getElementById('decrease');
            const quantityDisplay = document.getElementById('quantity');
            
            let quantity = 1;
            
            increaseBtn.addEventListener('click', () => {
                quantity++;
                quantityDisplay.textContent = quantity;
            });
            
            decreaseBtn.addEventListener('click', () => {
                if (quantity > 1) {
                quantity--;
                quantityDisplay.textContent = quantity;
                }
            });

            const modal = document.getElementById("imageModal");
            const modalImg = document.getElementById("modalImage");
            const thumbnail = document.getElementById("thumbnail");
            const closeBtn = document.getElementsByClassName("close")[0];
            
            thumbnail.onclick = function () {
                modal.style.display = "block";
                modalImg.src = this.src;
            }
            
            closeBtn.onclick = function () {
                modal.style.display = "none";
            }
            
            window.onclick = function (event) {
                if (event.target == modal) {
                modal.style.display = "none";
                }
            }

            function changeThumbnail(imgElement) {
                document.getElementById('thumbnail').src = imgElement.src;
            }

            let selectedSize = "";

            document.querySelectorAll('.product-size').forEach(button => {
                button.addEventListener('click', function () {
                    // Remove active class from all
                    document.querySelectorAll('.product-size').forEach(btn => btn.classList.remove('active'));
                    // Add active to clicked one
                    this.classList.add('active');
                    selectedSize = this.getAttribute('data-size');
                    document.getElementById('selectedSize').value = selectedSize;
                });
            });

            document.getElementById('addToCartBtn').addEventListener('click', function () {
                const productId = "<?= $encryptedId ?>";
                const size = document.getElementById('selectedSize').value;
                const quantity = document.getElementById('quantity').textContent;

                if (!size) {
                    alert("Please select a size before adding to cart.");
                    return;
                }

                const cartUrl = `add-to-cart?productId=${productId}&size=${size}&qty=${quantity}`;
                window.location.href = cartUrl;
            });

            document.getElementById('BuyBtn').addEventListener('click', function () {
                const productId = "<?= $encryptedId ?>";
                const size = document.getElementById('selectedSize').value;
                const quantity = document.getElementById('quantity').textContent;

                if (!size) {
                    alert("Please select a size before adding to cart.");
                    return;
                }

                const url = `add-to-cart.php?productId=${productId}&size=${size}&qty=${quantity}&buyNow=1`;
                window.location.href = url;
                window.location.href = cartUrl;
            });

        </script>
    </body>
    </html>
