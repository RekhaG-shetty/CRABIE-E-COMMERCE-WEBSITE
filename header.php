<?php
    include_once("dataBase.php");
    include_once("functions.php");
    
    $cartCount = 0;

    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cartCount += $item['qty'];
        }
    }

?>    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crabie | Stylish Jackets</title>
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <!--google ads-->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5531966840566553"
     crossorigin="anonymous"></script>
</head>
<style>
    .nav-link.active{
        font-weight: bolder;
        text-decoration: underline;
    }
    .cart-icon {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cart-link {
        display: inline-block;
        position: relative;
    }

    .cart-img {
        width: 40px;
        height: auto;
    }

    .cart-badge {
        position: absolute;
        bottom: 0;
        right: 0;
        transform: translate(50%, 50%);
        background-color: #ccc; /* grey background */
        color: black;
        border-radius: 50%;
        padding: 4px 7px;
        font-size: 12px;
        min-width: 20px;
        height: 20px;
        line-height: 12px;
        text-align: center;
        font-weight: bold;
        box-shadow: 0 0 2px rgba(0,0,0,0.3);
    }
    .right{
        display: flex;
        gap: 10px;
    }
    .login{
        background-color: black;
        padding: 10px 20px;
        color: white;
        border-radius: 8px;
        transition: transform 0.3s ease
    }
    .login:hover{
        transform: scale(1.05);
    }
    .account {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }
    .account img,
    .cart-img {
        /* width: 35px; */
        height: 35px;
        object-fit: contain;
        display: block;
    }
    .dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 45px;
        background-color: white;
        min-width: 160px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        z-index: 1000;
    }

    .dropdown a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        border-bottom: 1px solid #eee;
    }

    .dropdown a:last-child {
        border-bottom: none;
    }

    .dropdown a:hover {
        background-color: #f1f1f1;
        border-radius: 8px;
    }
    @media (max-width: 768px) {
        header.navbar {
            flex-direction: column;
            align-items: flex-start;
            padding: 10px;
        }

        .logo {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .right {
            flex-direction: row;
            width: 100%;
            justify-content: space-between;
            align-items: center;
        }

        .cart-img {
            width: 32px;
        }

        .login {
            font-size: 14px;
            padding: 8px 12px;
        }

        .dropdown {
            right: 0;
            top: 38px;
            min-width: 140px;
        }

        .account img {
            width: 32px;
        }

        .cart-badge {
            padding: 3px 6px;
            font-size: 10px;
            height: 18px;
            min-width: 18px;
        }
    }
    .hamburger {
        display: none;
        font-size: 26px;
        cursor: pointer;
        padding: 10px 0;
    }

    @media (max-width: 768px) {
        .nav-container {
            width: 100%;
        }

        .hamburger {
            display: block;
        }

        nav.nav {
            display: none;
            flex-direction: column;
            gap: 10px;
            width: 100%;
            background-color: white;
            padding: 10px 0;
            border-top: 1px solid #eee;
        }

        nav.nav.show {
            display: flex;
        }

        .nav-link {
            padding: 10px 20px;
            border-bottom: 1px solid #eee;
        }
    }
    .nav-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .right-icons {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .right-icons .icon {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
}

    /* Hamburger icon */
    .hamburger {
        font-size: 26px;
        cursor: pointer;
        display: none;
    }

    /* Slide menu hidden by default */
    .slide-menu {
        position: fixed;
        top: 0;
        right: -250px;
        width: 250px;
        height: 100vh;
        background: white;
        box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        padding: 60px 20px;
        transition: right 0.3s ease;
        z-index: 9999;
    }

    .slide-menu a {
        padding: 15px 0;
        border-bottom: 1px solid #eee;
        color: black;
        text-decoration: none;
        font-weight: 600;
    }

    .slide-menu a:hover {
        background-color: #f1f1f1;
        border-radius: 6px;
        padding-left: 10px;
    }

    .slide-menu.open {
        right: 0;
    }

    /* Mobile View */
    @media (max-width: 768px) {
        .hamburger {
            display: block;
        }

        nav.nav {
            display: none;
        }

        .right-icons {
            gap: 10px;
        }

        .login {
            padding: 5px 10px;
            font-size: 14px;
        }

        .cart-img {
            width: 30px;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
            margin-left: 25px;
        }
        .cart-icon{
            margin-right: 25px;
        }
    }
    .slide-menu a.login-btn {
        background-color: black;
        color: white;
        border-radius: 8px;
        text-align: center;
        padding: 10px 15px;
        font-weight: 600;
        margin-top: 20px;
        border: none;
    }

    .slide-menu a.login-btn:hover {
        background-color: #333;
    }
    .desktop-only {
        display: block;
    }

    @media (max-width: 768px) {
        .desktop-only {
            display: none !important;
        }

        .login-btn {
            background-color: black;
            color: white;
            border-radius: 8px;
            text-align: center;
            padding: 12px 15px;
            font-weight: 600;
            margin-top: 30px;
            display: block;
            width: 100%;
        }

        .login-btn:hover {
            background-color: #333;
        }
    }
    .cart-icon img{
        width: 40px;
    }
</style>
<body>

  <!-- Header -->
<header class="navbar">
    <div class="nav-top">
        <a href="index" class="logo">CRABIE</a>

        <nav class="nav desktop-only">
            <a href="index" class="nav-link">Home</a>
            <a href="shop" class="nav-link">Shop</a>
            <a href="about" class="nav-link">About</a>
            <a href="contact" class="nav-link">Contact</a>
        </nav>

        <div class="right-icons">
            <div class="hamburger" onclick="toggleSlideMenu()">☰</div>

        <?php 
            if (!isUser()) { 
        ?>
                <div class="login desktop-only"><a href="register">Login/SignUp</a></div>
        <?php 
            } else { 
        ?>
                <div class="account" onclick="toggleDropdown()">
                    <img src="account.png" alt="Account" class="cart-img" />
                    <div id="accountDropdown" class="dropdown">
                        <a href="account-details">Account Details</a>
                        <a href="order-history">Order History</a>
                        <a href="logout">Logout</a>
                    </div>
                </div>
            <?php } ?>

            <div class="cart-icon">
                <a href="cart" class="cart-link">
                    <img src="cart-icon.png" alt="Cart" class="cart-img" />
                <?php 
                    if ($cartCount > 0) { 
               ?>
                        <span class="cart-badge"><?= $cartCount ?></span>
                <?php 
                    } 
                ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Hidden Slide-in Menu -->
    <div id="slideMenu" class="slide-menu">
        <a href="index">Home</a>
        <a href="shop">Shop</a>
        <a href="about">About</a>
        <a href="contact">Contact</a>
    <?php 
        if (!isUser()) { 
    ?>
            <a href="register" style="margin-top: 20px; font-weight: bold; color: #fff; background-color: black; padding: 10px 15px; border-radius: 8px; text-align: center;">
                Login / Sign Up
            </a>
    <?php 
        } 
    ?>
    </div>
</header>


<script>
    function toggleDropdown() {
        var dropdown = document.getElementById("accountDropdown");
        dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
    }
    document.addEventListener("click", function (event) {
        var dropdown = document.getElementById("accountDropdown");
        var account = document.querySelector(".account");
        if (!account.contains(event.target)) {
            dropdown.style.display = "none";
        }
    });
    
    // Menu bar
    function toggleSlideMenu() {
        const menu = document.getElementById("slideMenu");
        menu.classList.toggle("open");
    }
    // Close menu if clicked outside
    document.addEventListener("click", function (event) {
        const menu = document.getElementById("slideMenu");
        const hamburger = document.querySelector(".hamburger");
        if (!menu.contains(event.target) && !hamburger.contains(event.target)) {
            menu.classList.remove("open");
        }
    });
</script>