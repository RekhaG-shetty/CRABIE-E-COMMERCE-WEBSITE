<?php
include("dataBase.php");
include("functions.php");
include("header.php");

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: register"); // Redirect to login/register
    exit;
}

$userId = $_SESSION['id'];

// Fetch order history
$query = "
    SELECT o.*, p.name AS product_name, v.image_url 
    FROM placed_order o
    JOIN products p ON o.product_id = p.id
    LEFT JOIN variant_images v ON o.product_id = v.product_id
    WHERE o.user_id = :userId
    GROUP BY o.id
    ORDER BY o.order_date DESC
";

$stmt = $connection->prepare($query);
$stmt->execute([':userId' => $userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<style>
    .container {
        padding: 20px;
    }
    h5 {
        font-weight: 600;
        color: #333;
    }
    p {
        font-size: 14px;
        margin-bottom: 5px;
        color: #555;
    }
    .alert-info {
        border-radius: 12px;
        background-color: #f1f5ff;
        color: #234;
        font-weight: 500;
        padding: 15px;
    }
    .order-card {
        display: flex;
        gap: 20px;
        margin-bottom: 25px;
        padding: 20px;
        border-radius: 16px;
        background-color: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        align-items: center;
        border: 1px solid #eee;
    }
    .order-image img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #ccc;
    }
    .order-details {
        flex: 1;
    }
    .order-details h5 {
        margin-bottom: 10px;
        font-size: 20px;
        color: #222;
    }
    .order-details p {
        margin: 5px 0;
        color: #444;
        font-size: 15px;
    }
    .row{
    }
    @media (max-width: 768px) {
        .order-card {
            flex-direction: column;
            text-align: left;
        }
        
        .order-image img {
            width: 100%;
            height: auto;
        }
    }
    .alert-success{
        margin-top: 10px;
        padding: 0px 50px 10px 50px;
        margin-bottom: 10px;
        padding-top: 10px;
        border-radius: 8px;
        background-color:rgba(95, 252, 92, 1);
        font-weight: bold;
    }
    .center{
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
<div class="container">
    <h2 style="margin-bottom: 20px; display: flex; justify-content: center;">🧾 Your Order History</h2>
    <?php 
        if(isset($_GET['msg'])){
            echo '
                <div class="center">
                    <div class="alert fade show alert-success" role="alert">
                    <ul class="list-unstyled">Order placed successfully</ul>
                    </div>
                </div>
            ';
        }
    ?>
    <?php 
    if (count($orders) === 0){ 
    ?>
        <div class="alert alert-info text-center">You haven't placed any orders yet.</div>
    <?php 
    }
    else{ 
    ?>
        <div class="row">
            <?php 
            foreach ($orders as $order){ 
            ?>
                <div class="order-card">
                    <div class="order-image">
                        <img src="<?= $order['image_url'] ?>" alt="<?= $order['product_name'] ?>">
                    </div>
                    <div class="order-details">
                        <h5><?= $order['product_name'] ?></h5>
                        <p><strong>Size:</strong> <?= $order['size'] ?></p>
                        <p><strong>Quantity:</strong> <?= $order['quantity'] ?></p>
                        <p><strong>Price:</strong> ₹<?= $order['price'] ?></p>
                        <p><strong>Payment:</strong> <?= strtoupper($order['payment_method']) ?></p>
                        <p><strong>Date:</strong> <?= $order['order_date'] ?></p>
                    </div>
                </div>
            <?php 
            } 
            ?>
        </div>
    <?php 
    } 
    ?>
</div>

<?php include("footer.php"); ?>
