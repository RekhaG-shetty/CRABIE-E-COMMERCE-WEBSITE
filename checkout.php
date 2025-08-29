<?php
    include_once("functions.php");
    include_once("dataBase.php");

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        header("Location: cart.php");
        exit;
    }

    if (!isUser()) {
        header("Location: register.php");
        exit;
    }

    if(isset($_POST['place_order'])){
        $payment = $_POST['payment_method'];
        $amount = $_POST['total'];

        $encryptedPayment = convertData($payment);        
        $encryptedAmount = convertData($amount);        
        header("Location: place-order.php?payment_method=$encryptedPayment&amount=$encryptedAmount");
    }

    $query = "
        SELECT * FROM users 
        WHERE id = :userId
        LIMIT 1
    ";
    $statement = $connection->prepare($query);
    $statement->execute([':userId' => $_SESSION["id"]]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    include_once("header.php");
?>

<style>
    .checkout-container {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        padding: 2rem;
        background-color: white;
        color: black;
    }

    .checkout-left, .checkout-right {
        flex: 1;
        min-width: 300px;
    }

    input {
        background-color: white;
        border: 1px solid #444;
        padding: 10px;
        width: 100%;
        color: black;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: black;
    }

    .checkout-right {
        background-color: white;
        padding: 1.5rem;
        border-radius: 10px;
    }

    .product-summary {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .product-summary img {
        width: 70px;
        border-radius: 8px;
        margin-right: 1rem;
    }

    .total-section {
        border-top: 1px solid black;
        padding-top: 1rem;
        margin-top: 1rem;
    }

    .total-line {
        display: flex;
        justify-content: space-between;
        margin: 0.5rem 0;
    }

    .btn-place-order {
        width: 100%;
        background-color: black;
        padding: 12px;
        border: none;
        border-radius: 6px;
        color: white;
        font-weight: bold;
        cursor: pointer;
    }

    .discount-badge {
        background: #2ecc71;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        display: inline-block;
        font-size: 0.9rem;
        margin-top: 5px;
    }
    select{
        background-color: white;
        border: 1px solid black;
        padding: 10px;
        width: 100%;
        color: black;
        border-radius: 6px;
        margin-bottom: 15px;
    }
</style>

<div class="checkout-container">
    <form method="POST" class="checkout-left">
        <div class="section-title">Contact</div>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($user['email']) ?>" required>
        <input type="text" name="phone" placeholder="Phone number" value="<?= htmlspecialchars($user['phone']) ?>" required>

        <div class="section-title">Delivery</div>
        <input type="text" name="first_name" style="text-transform: capitalize;" placeholder="First name" value="<?= htmlspecialchars($user['name']) ?>" required>
        <input type="text" name="address" style="text-transform: capitalize;" placeholder="Address" value="<?= htmlspecialchars($user['address']) ?>" required>
        <input type="text" name="landmark" style="text-transform: capitalize;" placeholder="Landmark" value="<?= htmlspecialchars($user['landmark']) ?>">
        <input type="text" name="city" style="text-transform: capitalize;" placeholder="City" value="<?= htmlspecialchars($user['city']) ?>" required>
        <input type="text" name="state" style="text-transform: capitalize;" placeholder="State" value="<?= htmlspecialchars($user['state']) ?>" required>
        <input type="text" name="pin" placeholder="PIN code" value="<?= htmlspecialchars($user['pincode']) ?>" required>
    </form>

    <div style="border-left: 1px solid black;"></div>

    <div class="checkout-right">
        <?php
            $total = 0;
            foreach ($_SESSION['cart'] as $item):
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
                    SELECT v.discounted_price, vi.image_url
                    FROM product_variants v
                    LEFT JOIN variant_images vi ON v.id = vi.variant_id
                    WHERE v.product_id = :pid AND v.size = :size
                    ORDER BY vi.id ASC
                    LIMIT 1
                ";
                $stmt = $connection->prepare($variantQuery);
                $stmt->execute([':pid' => $productId, ':size' => $size]);
                $variant = $stmt->fetch(PDO::FETCH_ASSOC);

                $image = $variant['image_url'] ?? 'placeholder.jpg';
                $total += $variant['discounted_price'] * $item['qty'];
        ?>
                <div class="product-summary">
                    <img src="<?= htmlspecialchars($image) ?>" alt="Product Image">
                    <div>
                        <div><strong><?= htmlspecialchars($product['name']) ?></strong></div>
                        <div>Qty: <?= $item['qty'] ?> | Size: <?= $size ?></div>
                        <div>₹<?= $variant['discounted_price'] ?></div>
                    </div>
                </div>
        <?php endforeach; ?>
        <div class="discount-badge">ONLINE PAYMENT 5% OFF</div>

        <div class="total-section">
            <div class="total-line">
                <span>Subtotal</span>
                <span>₹<?= number_format($total) ?></span>
            </div>
            <div class="total-line">
                <span>Order Discount</span>
                <span>- ₹0</span>
            </div>
            <div class="total-line" style="font-weight: bold;">
                <span>Total</span>
                <span>₹<?= number_format($total) ?></span>
            </div>
            <div class="section-title">Payment</div>
            
            <form method="POST">
                <select name="payment_method" required>
                    <option value="">Select payment method</option>
                    <option value="upi">UPI</option>
                    <option value="cod">Cash on Delivery</option>
                </select>
                <input type="hidden" name="total" value="<?= $total ?>" >

                <button type="submit" name="place_order" class="btn-place-order">Place Order</button>
            </form>
        </div>
    </div>
</div>
<?php include("footer.php"); ?>

</body>
</html>