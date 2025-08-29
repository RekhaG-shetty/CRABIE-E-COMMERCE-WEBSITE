<?php
// admin-trash.php
include("dataBase.php");
include("functions.php");

if (!isAdmin()) {
    header("Location: admin-login.php");
    exit;
}

// Handle restore
if (isset($_POST['restore'])) {
    $id = $_POST['product_id'];
    $stmt = $connection->prepare("UPDATE products SET is_deleted = 0 WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

// Handle permanent delete
if (isset($_POST['delete_forever'])) {
    $id = $_POST['product_id'];

    // Delete images
    $stmt = $connection->prepare("DELETE FROM variant_images WHERE product_id = :id");
    $stmt->execute([':id' => $id]);

    // Delete variants
    $stmt = $connection->prepare("DELETE FROM product_variants WHERE product_id = :id");
    $stmt->execute([':id' => $id]);

    // Delete product
    $stmt = $connection->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

// Fetch trashed products
$stmt = $connection->query("SELECT * FROM products WHERE is_deleted = 1 ORDER BY id DESC");
$trashedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trashed Products - Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .admin-header {
            background-color: #111;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #333;
            color: white;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-restore {
            background-color: #28a745;
            color: white;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .toggle-buttons {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            gap: 20px;
        }
        .toggle-btn {
            background-color: #000;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .toggle-btn.active, .toggle-btn:hover {
            background-color: #333;
        }
        .admin-container {
            padding: 40px 5%;
        }
    </style>
</head>
<body>

    <div class="admin-header">
        <h1>Trashed Products</h1>
    </div>
    
    <div class="admin-container">
        <div class="toggle-buttons">
            <a href="admin-panel"><button class="toggle-btn">Admin Panel</button></a>
        </div>

        <?php if (count($trashedProducts) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trashedProducts as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['id']) ?></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['description']) ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <button type="submit" name="restore" class="btn btn-restore">Restore</button>
                                </form>
                                <form method="post" style="display:inline; margin-left: 10px;">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <button type="submit" name="delete_forever" class="btn btn-delete" onclick="return confirm('Are you sure? This will permanently delete the product.')">Delete Forever</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align:center; font-size: 18px; color: gray;">No trashed products found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
