<?php

    include("header.php");

    if (!isUser()) {
        header("Location: register.php");
        exit;
    }

    $query = "
        SELECT * FROM users 
        WHERE id = :userId
        LIMIT 1
    ";
    $statement = $connection->prepare($query);
    $statement->execute([':userId' => $_SESSION["id"]]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);

?>
    <style>
        .account-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            background: #dfdedeff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            font-family: 'Poppins', sans-serif;
        }
        .account-title {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #000;
            text-align: center;
        }
        .account-info {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .account-item {
            font-size: 16px;
            padding: 14px 20px;
            background: #f5f5f5;
            border-radius: 8px;
            border-left: 5px solid #000;
        }
        .account-label {
            font-weight: 500;
            color: #777;
            margin-bottom: 5px;
        }
        .account-value {
            font-size: 16px;
            color: #222;
        }
        .edit-btn {
            margin-top: 30px;
            display: flex;
            justify-content: center;
        }
        .edit-btn a {
            background-color: #000;
            color: #fff;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: 0.2s ease;
        }
        .edit-btn a:hover {
            background-color: #333;
        }
    </style>

    <div class="account-container">
        <div class="account-title">Your Account</div>

        <div class="account-info">
            <div class="account-item">
                <div class="account-label">Name</div>
                <div class="account-value"><?= htmlspecialchars($user['name']) ?></div>
            </div>

            <div class="account-item">
                <div class="account-label">Mobile</div>
                <div class="account-value"><?= htmlspecialchars($user['phone']) ?></div>
            </div>

            <div class="account-item">
                <div class="account-label">Email</div>
                <div class="account-value"><?= htmlspecialchars($user['email']) ?></div>
            </div>
            
            <div class="account-item">
                <div class="account-label">Address</div>
                <div class="account-value"><?= htmlspecialchars($user['address']) ?></div>
            </div>

            <div class="account-item">
                <div class="account-label">Landmark</div>
                <div class="account-value"><?= htmlspecialchars($user['landmark']) ?></div>
            </div>

            <div class="account-item">
                <div class="account-label">City</div>
                <div class="account-value"><?= htmlspecialchars($user['city']) ?></div>
            </div>

            <div class="account-item">
                <div class="account-label">State</div>
                <div class="account-value"><?= htmlspecialchars($user['state']) ?></div>
            </div>

            <div class="account-item">
                <div class="account-label">Pincode</div>
                <div class="account-value"><?= htmlspecialchars($user['pincode']) ?></div>
            </div>

             <div class="edit-btn">
                <a href="edit-details.php">Edit Details</a>
            </div>
        </div>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
</html>