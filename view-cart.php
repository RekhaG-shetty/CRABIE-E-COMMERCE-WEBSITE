<?php
    session_start();

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo "Cart is empty.";
    } else {
        echo "Items in cart: ";
        foreach ($_SESSION['cart'] as $productId) {
            echo "<p>Product ID: $productId</p>";
        }
    }
?>