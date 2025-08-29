<?php
    include("functions.php");
    include("dataBase.php");

    $productId = $_GET['productId'] ?? '';
    
    $size = $_GET['size'] ?? '';
    $qty = $_GET['qty'] ?? 1;

    $decryptedProductId = convertData($productId, 'decrypt');
    $qty = (int)$qty;

    $found = false;
    foreach ($_SESSION['cart'] as $item) {
        if ($item['productId'] == $decryptedProductId && $item['size'] == $size) {
            $item['qty'] += $qty;
            $found = true;
            break;
        }
    }
    unset($item);

    // Add to cart session
    if (!$found) {
        $_SESSION['cart'][] = [
            'productId' => $decryptedProductId,
            'size' => $size,
            'qty' => $qty
        ];
    }

    if (isUser()) {
        $userId = $_SESSION['id'];

        $query = "
            SELECT qty FROM user_cart 
            WHERE user_id = :uid 
            AND product_id = :pid 
            AND size = :size
        ";

        $stmt = $connection->prepare($query);
        $stmt->execute([
            ':uid' => $userId,
            ':pid' => $decryptedProductId,
            ':size' => $size
        ]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $existQuery = "
                UPDATE user_cart 
                SET qty = qty + :qty 
                WHERE user_id = :uid 
                AND product_id = :pid 
                AND size = :size";
            $stmt = $connection->prepare($existQuery);
        } else {
            $elseQuery = "
                INSERT INTO user_cart 
                (user_id, product_id, size, qty) 
                VALUES (:uid, :pid, :size, :qty)
            ";
            $stmt = $connection->prepare($elseQuery);
        }

        $stmt->execute([
            ':uid' => $userId,
            ':pid' => $decryptedProductId,
            ':size' => $size,
            ':qty' => $qty
        ]);
    }

    // Redirect based on Buy Now flag
    if (isset($_GET['buyNow']) && $_GET['buyNow'] == 1) {
        header("Location: checkout.php");
        exit;
    } else {
        header("Location: cart.php");
        exit;
    }

?>