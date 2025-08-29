<?php
    
    include_once("functions.php");
    include_once("dataBase.php");

    require('razorpay-php/Razorpay.php');

    use Razorpay\Api\Api;

    $encryptedPayment = $_GET['payment_method'];
    $decryptedPayment = convertData($encryptedPayment, 'decrypt');
    $encryptedAmount = $_GET['amount'];
    $decryptedAmount = convertData($encryptedAmount, 'decrypt');

    $query = "
        SELECT * FROM users 
        WHERE id = :userId
        LIMIT 1
    ";
    $statement = $connection->prepare($query);
    $statement->execute([':userId' => $_SESSION["id"]]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    $name = $user['name'];
    $email = $user['email'];
    $phone = $user['phone'];

    if($decryptedPayment == 'cod'){
        $orderSummary = "";

        foreach ($_SESSION['cart'] as $item) {
            $productId = $item['productId'];
            $size = $item['size'];
            $qty = $item['qty'];

			$data = array(
				':productId' => $productId,
                ':size'     => $size
			);

            $proQuery = "
                SELECT discounted_price FROM product_variants 
                WHERE product_id = :productId
                AND size = :size
            ";

            $proStatement = $connection->prepare($proQuery);
            $proStatement->execute($data);
            $product = $proStatement->fetch(PDO::FETCH_ASSOC);

            $price = $product['discounted_price'];

            $stmt = $connection->prepare("INSERT INTO placed_order (user_id, product_id, size, quantity, price, payment_method) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['id'], $productId, $size, $qty, $price, 'cod']);

            // Delete from user_cart
            $deleteCart = $connection->prepare("
                DELETE FROM user_cart 
                WHERE user_id = :userId 
                AND product_id = :productId 
                AND size = :size
            ");
            $deleteCart->execute([
                ':userId'    => $_SESSION['id'],
                ':productId' => $productId,
                ':size'      => $size
            ]);

            $orderSummary .= "Product ID: $productId, Size: $size, Quantity: $qty, Price: ₹$price<br>";
            // unset($_SESSION['cart']);
        }
        $adminSubject = "New COD Order Received - Crabie";
        $adminBody = "
            <h2>New Order</h2>
            Name: $name<br>
            Email: $email<br>
            Phone: $phone<br>
            Payment Method: COD<br><br>
            <strong>Order Details:</strong><br>$orderSummary
            <br>Total: ₹$decryptedAmount
        ";
        $customerSubject = "Your Order with Crabie is Confirmed";
        $customerBody = "
            <h2>Thank you for your order!</h2>
            Hi $name,<br>
            We’ve received your order.<br><br>
            <strong>Order Details:</strong><br>$orderSummary
            <br>Total: ₹$decryptedAmount<br><br>
            We’ll notify you once it ships.
        ";

        sendMail("crabieorder@gmail.com", $adminSubject, $adminBody);
        sendMail($email, $customerSubject, $customerBody);

        // 👇 Now safe to unset
        unset($_SESSION['cart']);

        $msg='Order Placed Successfully';
        $encMsg = convertData($msg);
        header("Location: order-history.php?msg=$encMsg");
    }
    if ($decryptedPayment == 'upi') {
        $msg = 'Order Placed Successfully';
        $encMsg = convertData($msg);
        include("header.php");

        $api = new Api("rzp_live_26nE1LexqzYr1t", "fBn6asCqOXqSHn8FN3ykdzYE");

        $amount = $decryptedAmount * 100;

        // Create Razorpay order
        $order = $api->order->create([
            'receipt' => 'order_rcptid_' . time(),
            'amount' => $amount,
            'currency' => 'INR'
        ]);

        $orderId = $order['id']; // Save this to session for later verification
        $_SESSION['razorpay_order_id'] = $orderId;
?>
            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

            <script>
                const amount = "<?= $decryptedAmount ?>";
                const finalAmountPaisa = parseInt(amount) * 100;

                const options = {
                    key: "rzp_live_26nE1LexqzYr1t",
                    amount: "<?= $amount ?>",
                    currency: "INR",
                    name: "Crabie",
                    order_id: "<?= $orderId ?>",
                    handler: function (response) {
                        alert("Payment Successful!\nPayment ID: " + response.razorpay_payment_id);
                        window.location.href = "razorpay-sucess.php";
                    },
                    prefill: {
                        name: "<?= $name ?>",
                        email: "<?= $email ?>",
                        contact: "<?= $phone ?>"
                    },
                    theme: {
                        color: "#007bff"
                    }
                };

                const rzp = new Razorpay(options);
                rzp.open();
            </script>
        </body>
        </html>
<?php
    }
?>
