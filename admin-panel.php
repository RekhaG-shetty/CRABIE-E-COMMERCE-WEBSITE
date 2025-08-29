<?php

    include("dataBase.php");
    include("functions.php");

    if(!isAdmin()){
        header("Location: admin-login.php");
    }

    $error = '';
    
    if (isset($_POST["add-product"])){
        $formData = array();

        // name and desc
        $formData['name'] = $_POST['name'];
        $formData['description'] = $_POST['description'];

        $data = array(
            ':name'		=>	$formData['name']
        );

        $query = "
            SELECT name FROM products 
            WHERE name = :name
            AND is_deleted = 0
        ";
        $statement = $connection->prepare($query);    
        $statement->execute($data);

        if($statement->rowCount() > 0){
            $error = '<li>Product Already Exists Please Edit That From View Products tab for any changes</li>';
        }
        else{
            $data = array(
                ':name' => $formData['name'],
                ':description' => $formData['description']
            );

            $query = "
                INSERT into products 
                (name, description)
                VALUES (:name, :description)
            ";

            $statement = $connection->prepare($query);
            $statement->execute($data);

            $product_id = $connection->lastInsertId();

            // variants upload for this product
            $variantSizes = $_POST['variant_size'];
            $variantCategory = $_POST['variant_category'];
            $variantColors = $_POST['variant_color'];
            $variantOriginalPrices = $_POST['variant_original_price'];
            $variantDiscountedPrices = $_POST['variant_discounted_price'];
            $variantStocks = $_POST['variant_stocks'];

            for ($i = 0; $i < count($variantSizes); $i++){
                $size = $variantSizes[$i];
                $category = $variantCategory[$i];
                $color = $variantColors[$i];
                $originalPrice = $variantOriginalPrices[$i];
                $discountedPrice = $variantDiscountedPrices[$i];
                $stocks = $variantStocks[$i];

                $varData = array(
                    ':product_id'       => $product_id,
                    ':size'             => $size,
                    ':category'         => $category,
                    ':color'            => $color,
                    ':originalPrice'    => $originalPrice,
                    ':discountedPrice'  => $discountedPrice,
                    ':stocks'           => $stocks
                );

                $variantQuery = "
                    INSERT into product_variants
                    (product_id, size, category, color, original_price, discounted_price, stocks)
                    VALUES (:product_id, :size, :category, :color, :originalPrice, :discountedPrice, :stocks)
                ";

                $variantstmt = $connection->prepare($variantQuery);
                $variantstmt->execute($varData);

                $variant_id = $connection->lastInsertId();

                // image uploads for this variant
                $inputName = "variant_images_{$i}";
                if (isset($_FILES[$inputName])) {
                    $totalFiles = count($_FILES[$inputName]['name']);

                    for ($j = 0; $j < $totalFiles; $j++) {
                        $fileName = $_FILES[$inputName]['name'][$j];
                        $tmpName = $_FILES[$inputName]['tmp_name'][$j];

                        if ($fileName != "") {
                            $targetDir = "images/variants";
                            if (!is_dir($targetDir)) {
                                mkdir($targetDir, 0755, true);
                            }

                            $uniqueName = time() . "_" . rand(1000,9999) . "_" . basename($fileName);
                            $targetFilePath = $targetDir . '/' . $uniqueName;

                            if (move_uploaded_file($tmpName, $targetFilePath)) {
                                $imgData = array(
                                    ':variant_id' => $variant_id,
                                    ':product_id' => $product_id,
                                    ':image_url' => $targetFilePath
                                );

                                $imgQuery = "
                                    INSERT INTO variant_images
                                    (variant_id, product_id, image_url)
                                    VALUES (:variant_id, :product_id, :image_url)
                                ";

                                $imgStmt = $connection->prepare($imgQuery);
                                $imgStmt->execute($imgData);

                                header("Location: admin-panel?msg=add");
                            }
                        }
                    }
                }
            }
        }
    }

    if (isset($_POST['delete_product'])) {
        $productId = $_POST['product_id'];

        $deleteProduct = "
            UPDATE products SET is_deleted = 1 WHERE id = ?
        ";

        $stmt = $connection->prepare($deleteProduct);
        $stmt->execute([$productId]);

        $cartDeleteQuery = "DELETE FROM user_cart WHERE product_id = :pid";
        $stmt = $connection->prepare($cartDeleteQuery);
        $stmt->execute([':pid' => $productId]);

        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $index => $item) {
                if ($item['productId'] == $productId) {
                    unset($_SESSION['cart'][$index]);
                }
            }
            // Re-index the array after unsetting
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }

        echo "
            <script>
                alert('Product moved to Trash.');
                window.location.href='admin-deleted-products.php';
            </script>";
        exit;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Crabie</title>
    <link href="asset/css/simple-datatables-style.css" rel="stylesheet" />
    <link href="asset/css/styles.css" rel="stylesheet" />
    <script src="asset/js/font-awesome-5-all.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
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
        .admin-container {
            padding: 40px 5%;
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
        .admin-section {
            display: none;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .admin-section.active {
            display: block;
        }
        .admin-section h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #1a1e23;
        }
        .product-table {
            width: 100%;
            border-collapse: collapse;
        }
        .product-table th,
        .product-table td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }
        .product-table th {
            background-color: #eee;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .admin-btn {
            background-color: #000;
            color: #fff;
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .admin-btn:hover {
            background-color: #333;
        }
        .center{
            display: flex;
            justify-content: center;
            align-items: center;
        }
        ::-webkit-file-upload-button{
            color: white;
            background: #504f4fff;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            outline: none;
        }
        ::-webkit-file-upload-button:hover{
            background: #000000ff;
            cursor: pointer;
        }
        .variant-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 0 4px rgba(0,0,0,0.1);
        }
        .variant-row input[type="text"],
        .variant-row input[type="file"] {
            flex: 1 1 120px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .remove-btn {
            background: red;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        .variant-row button {
            background: red;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .add-var{
            padding: 8px 18px;
            border-radius: 4px;
            cursor: pointer;
        }
        .center{
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .alert-success{
            padding: 15px 45px 0px 20px;
            border-radius: 6px;
            background-color: greenyellow;
        }
        .alert-danger{
            padding: 15px 45px 0px 20px;
            border-radius: 6px;
        }
        .size{
            border-radius: 4px;
            cursor: pointer;
            border: 1px solid #CCCCCC;
        }
        .admin-btn:hover{
            color: white;
            background-color: #4a4a4aff;
        }
    </style>
</head>
<body>

    <div class="admin-header">
        <h1>Admin Panel - Crabie</h1>
    </div>

    <div class="admin-container">

        <?php

            if(isset($_GET['msg'])){

                // ADD MSG
    
                if($_GET['msg'] == 'add'){
                    echo '
                        <div class="center">
                            <div class="alert alert-dismissible fade show alert-success" role="alert">
                                <ul class="list-unstyled" style="color:black;">New Jacket Added</ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    ';
                }

                // Edit MSG
    
                if($_GET['msg'] == 'updated'){
                    echo '
                        <div class="center">
                            <div class="alert alert-dismissible fade show alert-success" role="alert">
                                <ul class="list-unstyled" style="color:black;">Jacket edited</ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    ';
                }
            }

        ?>

        <?php
            if($error != ''){
                echo '
                    <div class="center">
                        <div class="alert alert-dismissible fade show alert-danger" role="alert">
                            <ul class="list-unstyled">'.$error.'</ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                ';
            }
        ?>

        <!-- Toggle Buttons -->
        <div class="toggle-buttons">
            <button class="toggle-btn active" onclick="toggleSection('product-list')">View Products</button>
            <button class="toggle-btn" onclick="toggleSection('add-product')">Add Product</button>
            <a href="admin-deleted-products"><button class="toggle-btn">Deleted Products</button></a>
            <a href="admin-logout"><button class="toggle-btn">Logout</button></a>
        </div>

        <!-- Product List -->
        <div class="admin-section active" id="product-list">
            <h2>All Products</h2>
            <table class="product-table" id="datatablesSimple">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Original Price</th>
                    <th>Discounted Price</th>
                    <th>Stocks</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                        $query = "
                            SELECT 
                                p.id AS product_id,
                                p.name,
                                vi.image_url,
                                MIN(v.original_price) AS original_price,
                                MIN(v.discounted_price) AS discounted_price,
                                v.stocks
                            FROM products p
                            LEFT JOIN product_variants v ON p.id = v.product_id
                            LEFT JOIN (
                                SELECT variant_id, MIN(image_url) AS image_url
                                FROM variant_images
                                GROUP BY variant_id
                            ) vi ON v.id = vi.variant_id
                            WHERE p.is_deleted = 0
                            GROUP BY p.id
                            ORDER BY p.id ASC
                        ";
                        $stmt = $connection->prepare($query);
                        $stmt->execute();
                        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($products as $product) {
                            echo '
                            <tr>
                                <td>
                                    ' . htmlspecialchars($product['product_id']) . '
                                </td>
                                <td>
                                    <img src="' . htmlspecialchars($product['image_url'] ?? 'images/no-image.png') . '" width="60">
                                </td>
                                <td>
                                    ' . htmlspecialchars($product['name']) . '
                                </td>
                                <td>
                                    ₹' . htmlspecialchars($product['original_price']) . '
                                </td>
                                <td>
                                    ₹' . htmlspecialchars($product['discounted_price']) . '
                                </td>
                                <td>
                                    ' . htmlspecialchars($product['stocks']) . '
                                </td>
                                <td>
                                    <form method="GET" action="admin-actions?action=view" style="display:inline;">
                                        <input type="hidden" name="id" value="' . $product['product_id'] . '">
                                        <input type="hidden" name="action" value="view">
                                        <button type="submit" class="admin-btn">View</button>
                                    </form>
                                    <form method="GET" action="admin-actions?action=edit" style="display:inline;">
                                        <input type="hidden" name="id" value="' . $product['product_id'] . '">
                                        <input type="hidden" name="action" value="edit">
                                        <button type="submit" class="admin-btn">Edit</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="product_id" value="'. $product['product_id'] .'">
                                        <input type="hidden" name="action" value="edit">
                                        <button type="submit" name="delete_product" class="admin-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Add New Product -->
        <div class="admin-section" id="add-product" style="max-width: 600px; margin: 0 auto;">
            <h2 style="padding-left: 50px;">Add New Product</h2>
            <form method="POST" enctype="multipart/form-data" style="padding: 0 50px;" id="productForm">
                <!-- Basic Info -->
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>

                <!-- Variants -->
                <h3>Variants (Category, Size, Color, Price)</h3>
                <div id="variant-wrapper">
                    <div class="variant-row">
                        <select name="variant_category[]" class="size" style="padding: 8px 39px;" required>
                            <option value="" style="color:#CCCCCC;">Select Category</option>
                            <option value="Mens">Mens</option>
                            <option value="Womens">Womens</option>
                        </select>
                        <select name="variant_size[]" class="size" style="padding: 8px 57px;" required>
                            <option value="" style="color:#CCCCCC;">Select Size</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                            <option value="XXL">XXL</option>
                            <option value="XXXL">XXXL</option>
                        </select>
                        <input type="text" name="variant_color[]" placeholder="Color" required>
                        <input type="text" name="variant_original_price[]" placeholder="Original Price (₹)" required>
                        <input type="text" name="variant_discounted_price[]" placeholder="Discounted Price (₹)" required>
                        <input type="text" name="variant_stocks[]" placeholder="Stocks" required>

                        <!-- Four image upload boxes side by side -->
                        <label>Upload Images for this variant</label>
                        <input type="file" name="variant_images_0[]">
                        <input type="file" name="variant_images_0[]">
                        <input type="file" name="variant_images_0[]">
                        <input type="file" name="variant_images_0[]">
                        <input type="file" name="variant_images_0[]">
                        <input type="file" name="variant_images_0[]">
                        <input type="file" name="variant_images_0[]">
                        <input type="file" name="variant_images_0[]">
                        <input type="file" name="variant_images_0[]">
                    </div>
                </div>

                <button type="button" class="add-var" onclick="addVariant()">+ Add More Variants</button>

                <div class="center" style="margin-top: 20px;">
                    <input type="submit" name="add-product" class="admin-btn" value="Add Product">
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSection(sectionId) {
        const sections = document.querySelectorAll('.admin-section');
        const buttons = document.querySelectorAll('.toggle-btn');

        sections.forEach(sec => sec.classList.remove('active'));
        buttons.forEach(btn => btn.classList.remove('active'));

        document.getElementById(sectionId).classList.add('active');
        event.target.classList.add('active');
        }

        let variantCount = 1;

        function addVariant() {
            const wrapper = document.getElementById('variant-wrapper');
            const row = document.createElement('div');
            row.className = 'variant-row';
            row.innerHTML = `
                <select name="variant_category[]" class="size" style="padding: 8px 39px;" required>
                    <option value="" style="color:#CCCCCC;">Select Category</option>
                    <option value="Mens">Mens</option>
                    <option value="Womens">Womens</option>
                </select>
                <select name="variant_size[]" class="size" style="padding: 8px 57px;" required>
                    <option value="" style="color:#CCCCCC;">Select Size</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                    <option value="XXL">XXL</option>
                    <option value="XXXL">XXXL</option>
                </select>
                <input type="text" name="variant_color[]" placeholder="Color" required>
                <input type="text" name="variant_original_price[]" placeholder="Original Price (₹)" required>
                <input type="text" name="variant_discounted_price[]" placeholder="Discounted Price (₹)" required>
                <input type="text" name="variant_stocks[]" placeholder="Stocks" required>

                <label>Upload Images for this variant</label>
                <input type="file" name="variant_images_${variantCount}[]" accept="image/*">
                <input type="file" name="variant_images_${variantCount}[]" accept="image/*">
                <input type="file" name="variant_images_${variantCount}[]" accept="image/*">
                <input type="file" name="variant_images_${variantCount}[]" accept="image/*">
                <input type="file" name="variant_images_${variantCount}[]" accept="image/*">
                <input type="file" name="variant_images_${variantCount}[]" accept="image/*">
                <input type="file" name="variant_images_${variantCount}[]" accept="image/*">
                <input type="file" name="variant_images_${variantCount}[]" accept="image/*">
                <input type="file" name="variant_images_${variantCount}[]" accept="image/*">

                <button type="button" onclick="removeVariant(this)">✖</button>
            `;
            wrapper.appendChild(row);
            variantCount++;
        }

        function removeVariant(button) {
            button.parentElement.remove();
        }
    </script>

    <script src="asset/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="asset/js/scripts.js"></script>
    <script src="asset/js/simple-datatables@latest.js" crossorigin="anonymous"></script>
    <script src="asset/js/datatables-simple-demo.js"></script>

</body>
</html>
