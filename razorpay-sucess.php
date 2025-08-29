<?php
    include("functions.php");
    include("dataBase.php");

    // Get user details
    $query = "SELECT * FROM users WHERE id = :userId LIMIT 1";
    $statement = $connection->prepare($query);
    $statement->execute([':userId' => $_SESSION["id"]]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    $name = $user['name'];
    $email = $user['email'];
    $phone = $user['phone'];

    $orderSummary = ""; // Prepare for email
    $total = 0;

    foreach ($_SESSION['cart'] as $item) {
        $productId = $item['productId'];
        $size = $item['size'];
        $qty = $item['qty'];

        // Get price
        $priceQuery = "
            SELECT discounted_price FROM product_variants 
            WHERE product_id = :productId AND size = :size
        ";
        $stmt = $connection->prepare($priceQuery);
        $stmt->execute([
            ':productId' => $productId,
            ':size' => $size
        ]);
        $variant = $stmt->fetch(PDO::FETCH_ASSOC);
        $price = $variant['discounted_price'];
        $total += $price * $qty;

        // Insert into orders
        $insert = $connection->prepare("
            INSERT INTO placed_order (user_id, product_id, size, quantity, price, payment_method)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $insert->execute([$_SESSION['id'], $productId, $size, $qty, $price, 'upi']);

        // Delete from cart
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

        // Add to email summary
        $orderSummary .= "Product ID: $productId, Size: $size, Quantity: $qty, Price: ₹$price<br>";
    }

    // Prepare email content
    $adminSubject = "New UPI Order Received - Crabie";
    $adminBody = "
        <h2>New Order Paid via UPI</h2>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Phone:</strong> $phone</p>
        <p><strong>Payment Method:</strong> UPI</p>
        <p><strong>Order Summary:</strong><br>$orderSummary</p>
        <p><strong>Total:</strong> ₹$total</p>
    ";

    $customerSubject = "Order Confirmation - Crabie";
    $customerBody = "
        <h2>Thank you for your payment!</h2>
        <p>Hi $name,</p>
        <p>We’ve received your payment and confirmed your order.</p>
        <p><strong>Order Details:</strong><br>$orderSummary</p>
        <p><strong>Total:</strong> ₹$total</p>
        <p>You’ll receive updates when your order is shipped.</p>
        <br><p>Regards,<br>Team Crabie</p>
    ";

    // Send emails
    sendMail("crabieorder@gmail.com", $adminSubject, $adminBody);
    sendMail($email, $customerSubject, $customerBody);

    // Clear cart
    unset($_SESSION['cart']);

    $msg = 'Order Placed Successfully';
    $encMsg = convertData($msg);
    header("Location: order-history.php?msg=$encMsg");
    exit;
?>
