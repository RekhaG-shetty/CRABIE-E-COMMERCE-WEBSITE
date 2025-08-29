<?php

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
        include("dataBase.php");
        include("functions.php");
        $productId = $_POST['productId'];
        $size = $_POST['size'];

        foreach ($_SESSION['cart'] as $index => $item) {
            if ($item['productId'] == $productId && $item['size'] == $size) {
                unset($_SESSION['cart'][$index]);
                if (isUser()) {
                    $userId = $_SESSION['id'];

                    $stmt = $connection->prepare("DELETE FROM user_cart WHERE user_id = ? AND product_id = ? AND size = ?");
                    $stmt->execute([$userId, $productId, $size]);
                }
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                break;
            }
        }
        header("Location: cart.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        include("dataBase.php");
        include("functions.php");
        
        $productId = $_POST['productId'] ?? null;
        $size = $_POST['size'] ?? null;

        // Update quantity
        if ($productId && $size) {
            if (isUser()) {
                $userId = $_SESSION['id'];

                if (isset($_POST['increase_qty'])) {
                    $stmt = $connection->prepare("UPDATE user_cart SET qty = qty + 1 WHERE user_id = ? AND product_id = ? AND size = ?");
                    $stmt->execute([$userId, $productId, $size]);
                }

                if (isset($_POST['decrease_qty'])) {
                    $stmt = $connection->prepare("UPDATE user_cart SET qty = GREATEST(qty - 1, 1) WHERE user_id = ? AND product_id = ? AND size = ?");
                    $stmt->execute([$userId, $productId, $size]);
                }
            }

            foreach ($_SESSION['cart'] as $index => $item) {
                if ($item['productId'] == $productId && $item['size'] == $size) {
                    if (isset($_POST['increase_qty'])) {
                        $_SESSION['cart'][$index]['qty'] += 1;
                    }
                    if (isset($_POST['decrease_qty']) && $_SESSION['cart'][$index]['qty'] > 1) {
                        $_SESSION['cart'][$index]['qty'] -= 1;
                    }
                    break;
                }
            }
            unset($item); // break reference
            header("Location: cart.php"); // refresh to see updated quantity and total
            exit;
        }
    }
    
    include("header.php");

    if (isset($_SESSION['welcome_back'])) {
        echo '<div class="alert alert-success" style="padding:10px; color: green;">' . $_SESSION['welcome_back'] . '</div>';
        unset($_SESSION['welcome_back']);
    }

    $cartItems = $_SESSION['cart'] ?? [];
    $validCartItems = [];
    $subtotal = 0;

    foreach ($cartItems as $item) {
    $productId = $item['productId'];
    $size = $item['size'];

    // Check if product exists
    $stmt = $connection->prepare("SELECT * FROM products WHERE id = :id AND is_deleted = 0");
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        continue; // Skip deleted product
    }

    // Check if variant exists
    $variantStmt = $connection->prepare("
        SELECT v.color, v.size, v.stocks, v.original_price, v.discounted_price, vi.image_url
        FROM product_variants v
        LEFT JOIN variant_images vi ON v.id = vi.variant_id
        WHERE v.product_id = :pid AND v.size = :size
        ORDER BY vi.id ASC LIMIT 1
    ");
    $variantStmt->execute([':pid' => $productId, ':size' => $size]);
    $variant = $variantStmt->fetch(PDO::FETCH_ASSOC);

    if (!$variant) {
        continue; // Skip if variant missing
    }

    // Add to valid cart
    $validCartItems[] = $item;

    // Calculate subtotal
    $subtotal += $variant['discounted_price'] * $item['qty'];

    // Render product card below (inside loop)
}

// Update session
$_SESSION['cart'] = $validCartItems;

    $suggestion = null;
    $cartProductIds = array_column($validCartItems, 'productId');

    if (empty($validCartItems)) {
?>
        <section class="hero-cart">
            <div class="hero-text-up-cart">
                <h1>
                    <span class="black-bold" style="font-size: 60px; font-weight: 800;">Your Cart is Empty</span>
                </h1>
                <a href="shop"><button class="shop-collection-cart">Shop Collection →</button></a>
            </div>
        </section>
<?php
    }
    else{

    $subtotal = 0;
?>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        background: #f2f2f2;
    }
    .cart-container {
        display: flex;
        padding: 20px;
    }
    .cart-items {
        flex: 2;
        background: #fff;
        padding: 20px;
        margin-right: 20px;
        border-radius: 8px;
    }
    .cart-summary {
        flex: 1;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
    }
    .cart-item {
        display: flex;
        border-bottom: 1px solid #ccc;
        padding: 15px 0;
    }   
    .cart-item img {
        width: 200px;
        height: 200px;
        margin-right: 20px;
    }
    .cart-item-details {
        flex: 1;
    }
    .cart-item-details h3 {
        margin: 0 0 10px;
    }
    .cart-item-details p {
        margin: 5px 0;
    }
    .cart-quantity-container {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #ccc;
        border-radius: 25px;
        overflow: hidden;
        margin-bottom: 10px;
    }
    .cart-qty-btn,
    .cart-qty-display {
    padding: 10px 16px;
        font-size: 16px;
        border: none;
        background: white;
        cursor: pointer;
        min-width: 40px;
        text-align: center;
    }
    .cart-qty-display {
        pointer-events: none;
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
    }
    .cart-subtotal {
        font-size: 18px;
        font-weight: bold;
        margin-top: 10px;
        margin-bottom: 20px;
    }
    .cart-proceed {
        margin-top: 30px;
        background-color: #000000ff;
        color: white;
        border: none;
        padding: 12px;
        width: 100%;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        border-radius: 6px;
    }
    .cart-suggestions {
        margin-top: 40px;
    }
    .cart-suggestions img {
        width: 80px;
    }
    .cart-suggestions .item {
        align-items: center;
    }
    .cart-actions {
        font-size: 12px;
        font-color: #747474;    
        margin-top: 10px;
    }
    .head{
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .add-jacket{
        background-color: black;
        color: white;
        padding: 10px 25px;
        border-radius: 8px;
        transition: transform 0.3s ease;
    }
    .add-jacket:hover{
        transform: scale(1.05);
    }
    .alert-success {
        display: flex;
        justify-content: center;
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 15px;
    }
</style>
<div class="cart-container">
    <!-- Left side: Cart items -->
    <div class="cart-items">
        <div class="head">
            <h2>Shopping Cart</h2>
            <a href="shop" class="add-jacket">Add more Jackets</a>
        </div>
        <hr> 
    <?php
        foreach ($validCartItems as $item){
            $productId = $item['productId'];
            $size = htmlspecialchars($item['size']);
            $qty = (int)$item['qty'];

            // Fetch product info
            $query = "SELECT name, description FROM products WHERE id = :id AND is_deleted = 0 LIMIT 1";
            $stmt = $connection->prepare($query);
            $stmt->execute([':id' => $productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            // Fetch variant info for the given product and size
            $variantQuery = "
                SELECT v.color, v.size, v.stocks, v.original_price, v.discounted_price, vi.image_url
                FROM product_variants v
                LEFT JOIN variant_images vi ON v.id = vi.variant_id
                WHERE v.product_id = :pid AND v.size = :size
                ORDER BY vi.id ASC
                LIMIT 1
            ";
            $stmt = $connection->prepare($variantQuery);
            $stmt->execute([':pid' => $productId, ':size' => $size]);
            $variant = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product || !$variant) continue;

            $image = $variant['image_url'] ?? 'placeholder.jpg';
            $discount = round((($variant['original_price'] - $variant['discounted_price']) / $variant['original_price']) * 100);
            $subtotal += $variant['discounted_price'] * $qty;
    ?>
            <div class="cart-item">
                <img src="<?= htmlspecialchars($image) ?>" alt="Product Image">
                <div class="cart-item-details">
                    <h2><?= htmlspecialchars($product['name']) ?></h2>
                    <p style="font-size:18px ; font-weight: 400; ">
                        <strong>₹<?= $variant['discounted_price'] ?> &nbsp <span style="color: green;"><?= $discount ?>% off</span></strong>
                    </p>
                    <p style="font-size:18px ;">Size: <?= $size ?> | Color: <?= $variant['color'] ?></p>
                    <!--  quantity and buy now -->
                    <div class="cart-quantity-container">
                        <form method="POST" style="display: contents;">
                            <input type="hidden" name="productId" value="<?= htmlspecialchars($productId) ?>">
                            <input type="hidden" name="size" value="<?= htmlspecialchars($size) ?>">

                            <button class="cart-qty-btn" type="submit" name="decrease_qty">−</button>
                            <span class="cart-qty-display"><?= $qty ?></span>
                            <button class="cart-qty-btn" type="submit" name="increase_qty">+</button>
                        </form>
                    </div>

                    <br>
                    <!-- actions -->
                    <div class="cart-actions">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_product" value="1">
                            <input type="hidden" name="productId" value="<?= htmlspecialchars($productId) ?>">
                            <input type="hidden" name="size" value="<?= htmlspecialchars($size) ?>">
                            <button type="submit" style="border: none; background: none; font-size: 15px; color: red; cursor: pointer;">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
    <?php
        }
    ?>
        
    </div>
    <hr>
    <!-- Right side: Summary  -->
    <div class="cart-summary">
        <!-- if order is more than 899- free delivery -->
        <p style="color: green;">Your order is eligible for FREE Delivery.</p>
        <?php
            $totalQty = 0;
            foreach ($validCartItems as $item) {
                $totalQty += $item['qty'];
            }
        ?>
        <div class="cart-subtotal">Subtotal (<?= $totalQty ?> item<?= $totalQty > 1 ? 's' : '' ?>): ₹<?= $subtotal ?></div>
        
        <p><input type="checkbox"> Have Coupon code</p>
        <!-- if there is coupon- open -->
        <input type="text" placeholder="Enter coupon code" style="width: 100%; padding: 8px; margin-bottom: 10px;">
        <button style="background-color: #000000ff; color: white; border: none; padding: 10px; width: 100%; font-size: 16px; font-weight: bold; cursor: pointer; border-radius: 6px;">Apply</button>
        
        <?php 
        if (isUser()){ 
        ?>
            <form method="POST" action="checkout.php">
                <button type="submit" name="proceed_to_buy" class="cart-proceed">Proceed to Buy</button>
            </form>
        <?php }
        else{ 
            $_SESSION['redirect_after_login'] = 'cart.php'
        ?>
            <a href="register.php" class="cart-proceed" style="display: inline-block; text-align: center;">Login to Checkout</a>
        <?php 
        } 
        ?>

        <?php 
        if (!empty($cartProductIds)) {
            $placeholders = rtrim(str_repeat('?,', count($cartProductIds)), ',');
            $query = "
                    SELECT p.id, p.name, p.description
                    FROM products p
                    WHERE p.id NOT IN ($placeholders)
                    AND p.is_deleted = 0
                ";
                
                $stmt = $connection->prepare($query);
                $stmt->execute($cartProductIds);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                // Fetch variants grouped by color
                $query = "
                    SELECT v.id AS variant_id, v.color, v.size, v.stocks, v.original_price, v.discounted_price, vi.image_url
                    FROM product_variants v
                    LEFT JOIN variant_images vi ON v.id = vi.variant_id
                    WHERE v.product_id NOT IN ($placeholders)
                    ORDER BY vi.id
                ";
                $stmt = $connection->prepare($query);
                $stmt->execute($cartProductIds);

                $variants = [];
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $color = $row['color'];
                    $variants[$color]['images'][] = $row['image_url'];
                    $variants[$color]['prices'][] = [
                        'original' => $row['original_price'],
                        'discounted' => $row['discounted_price']
                    ];
                    $variants[$color]['stocks'][] = $row['stocks'];
                    $variants[$color]['sizes'][] = $row['size'];
                }
                $colors = array_keys($variants);
                $defaultColor = $colors[0];
                $productImages = $variants[$defaultColor]['images'];

                if ($product){ 
        ?>          
                    <div style="margin-top: 20px;">
                        <h3>Customer's also buy:</h3>
                    </div>
                    <div style="display: flex; gap: 16px; border: 1px solid #ccc; padding: 16px; border-radius: 12px; max-width: 600px; align-items: flex-start;">
                        

                        <div style="flex: 1;">
                             <?php
                                echo '
                                    <a href="product-page?productId='.convertData($product['id']).'">
                                        <img src="'.htmlspecialchars($productImages[0]).'" alt="'.htmlspecialchars($product['name']).'" style="max-width: 100%; border-radius: 8px;" />
                                    </a>
                                ';
                            ?>
                        </div>

                        <div style="flex: 1;">
                            <h3 style="font-size: 20px; margin-bottom: 8px;"><?= htmlspecialchars($product['name']) ?></h3>
                            <p style="font-weight: bold; font-size: 18px; color: #111;">
                                <span style="color: green;">
                                    - <?= 
                                        round((($variants[$defaultColor]['prices'][0]['original'] - $variants[$defaultColor]['prices'][0]['discounted'])/$variants[$defaultColor]['prices'][0]['original'])*100 )
                                    ?>
                                     % off 
                                </span>
                                ₹
                                <?= $variants[$defaultColor]['prices'][0]['discounted'] ?>
                                
                            </p>

                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>" >

                            <!-- Size selector -->
                            <div class="size" style="margin-right: 50px;">
                                <div><strong>Size: </strong></div>
                                <div style="margin-bottom: 20px">
                                    <?php foreach (array_unique($variants[$defaultColor]['sizes']) as $size): ?>
                                        <button class="product-size" data-size="<?= $size ?>"><?= $size ?></button>
                                    <?php endforeach; ?>
                                    <input type="hidden" id="selectedSize" value="">
                                    <input type="hidden" id="selectedId" value="<?= convertData($product['id']) ?>">
                                </div>
                            </div>

                            <button id="addToCartBtn" style="padding: 10px 20px;background-color: black; color: white; border: none; border-radius: 6px;cursor: pointer;">Add to Bag</button>
                        </div>
                    </div>
        <?php 
                }
            }
        ?>
    </div>
</div>
<?php   
    }
?>
<?php 
    include("footer.php")
?>

    <script>
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
                const size = document.getElementById('selectedSize').value;
                const productId = document.getElementById('selectedId').value;

                if (!size) {
                    alert("Please select a size before adding to cart.");
                    return;
                }

                const cartUrl = `add-to-cart?productId=${productId}&size=${size}&qty=1`;
                window.location.href = cartUrl;
            });
    </script>
</body>
</html>