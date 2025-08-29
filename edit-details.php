<?php

    include_once("functions.php");
    include_once("dataBase.php");
    if (!isUser()) {
        header("Location: register.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $mobile = trim($_POST['mobile']);
        $email = trim($_POST['email']);
        $address = trim($_POST['address']);
        $landmark = trim($_POST['landmark']);
        $city = trim($_POST['city']);
        $state = trim($_POST['state']);
        $pincode = trim($_POST['pincode']);

        $data = array(
            ':name'         => $name,
            ':mobile'       => $mobile,
            ':email'        => $email,
            ':address'      => $address,
            ':landmark'     => $landmark,
            ':city'         => $city,
            ':state'        => $state,
            ':pincode'      => $pincode,
            ':userId'       => $_SESSION['id']
        );

        $query = "
            UPDATE users 
            SET 
                name        = :name,
                phone       = :mobile,
                email       = :email,
                address     = :address,
                landmark    = :landmark,
                city        = :city,
                state       = :state,
                pincode     = :pincode
            WHERE id = :userId
        ";

        $stmt = $connection->prepare($query);
        $stmt->execute($data);

        header("Location: account-details.php");
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

    include("header.php");

?>

<style>
    .edit-container {
        max-width: 600px;
        margin: 40px auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        background: #dfdedeff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        font-family: 'Poppins', sans-serif;
    }
    .edit-title {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 25px;
        color: #333;
        text-align: center;
    }
    .edit-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .edit-form input {
        padding: 12px 18px;
        font-size: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        width: 100%;
    }
    .edit-form button {
        background-color: #000;
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-size: 15px;
        cursor: pointer;
        font-weight: 500;
        transition: background-color 0.2s ease;
    }
    .edit-form button:hover {
        background-color: #333;
    }
</style>

<div class="edit-container">
    <div class="edit-title">Edit Your Details</div>

    <form method="POST" class="edit-form">
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required placeholder="Name" />
        <input type="text" name="mobile" value="<?= htmlspecialchars($user['phone']) ?>" required placeholder="Mobile" />
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required placeholder="Email" />
        <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" required placeholder="Address" />
        <input type="text" name="landmark" value="<?= htmlspecialchars($user['landmark']) ?>" required placeholder="Landmark" />
        <input type="text" name="city" value="<?= htmlspecialchars($user['city']) ?>" required placeholder="City" />
        <input type="text" name="state" value="<?= htmlspecialchars($user['state']) ?>" required placeholder="State" />
        <input type="text" name="pincode" value="<?= htmlspecialchars($user['pincode']) ?>" required placeholder="Pincode" />
        <button type="submit">Save Changes</button>
    </form>
</div>
