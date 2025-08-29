<?php
    include("dataBase.php");
    include("functions.php");

    if(!isAdmin()){
        header("Location: admin-login.php");
    }
    if (isset($_POST['update-product'])) {
        $product_id = $_POST['product_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];

        // Update product name & description
        $updateProduct = "
            UPDATE products SET name = :name, description = :desc WHERE id = :id
        ";
        $stmt = $connection->prepare($updateProduct);
        $stmt->execute([
            ':name' => $name,
            ':desc' => $description,
            ':id' => $product_id
        ]);

        // Update each variant
        foreach ($_POST['variant_ids'] as $index => $variant_id) {
            $category = $_POST['variant_category'][$index];
            $size = $_POST['variant_size'][$index];
            $color = $_POST['variant_color'][$index];
            $original = $_POST['variant_original_price'][$index];
            $discount = $_POST['variant_discounted_price'][$index];
            $stocks = $_POST['variant_stocks'][$index];

            $updateVariant = "
                UPDATE product_variants 
                SET size = :size, category = :category, color = :color,
                    original_price = :original, discounted_price = :discount, stocks = :stocks
                WHERE id = :variant_id AND product_id = :product_id
            ";
            $stmt = $connection->prepare($updateVariant);
            $stmt->execute([
                ':size' => $size,
                ':category' => $category,
                ':color' => $color,
                ':original' => $original,
                ':discount' => $discount,
                ':stocks' => $stocks,
                ':variant_id' => $variant_id,
                ':product_id' => $product_id
            ]);

            // Upload new images if any
            $inputName = "variant_images_{$index}";
            if (isset($_FILES[$inputName])) {
                foreach ($_FILES[$inputName]['tmp_name'] as $key => $tmp) {
                    if (!empty($tmp)) {
                        $fileName = $_FILES[$inputName]['name'][$key];
                        $targetDir = "images/variants";
                        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

                        $uniqueName = time() . "_" . rand(1000, 9999) . "_" . basename($fileName);
                        $targetPath = $targetDir . '/' . $uniqueName;

                        if (move_uploaded_file($tmp, $targetPath)) {
                            $stmt = $connection->prepare("
                                INSERT INTO variant_images (variant_id, product_id, image_url)
                                VALUES (:variant_id, :product_id, :image)
                            ");
                            $stmt->execute([
                                ':variant_id' => $variant_id,
                                ':product_id' => $product_id,
                                ':image' => $targetPath
                            ]);
                        }
                    }
                }
            }
        }

        header("Location: admin-panel.php?msg=updated");
        exit;
    }

    $productId = $_GET['id'];
    $action = $_GET['action'];

    // Fetch product info
    $query = "
        SELECT p.name, p.description
        FROM products p
        WHERE p.id = :id
        AND p.is_deleted = 0
    ";
    $stmt = $connection->prepare($query);
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch variants grouped by color
    $query = "
        SELECT v.id AS variant_id, v.color, v.size, v.stocks, v.original_price, v.discounted_price, vi.image_url
        FROM product_variants v
        LEFT JOIN variant_images vi ON v.id = vi.variant_id
        WHERE v.product_id = :id
        ORDER BY v.color, v.size
    ";
    $stmt = $connection->prepare($query);
    $stmt->execute([':id' => $productId]);

    $variants = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $color = $row['color'];
        $variants[$color]['images'][] = $row['image_url'];
        $variants[$color]['prices'][] = [
            'original' => $row['original_price'],
            'discounted' => $row['discounted_price']
        ];
        $variants[$color]['stocks'][] = $row['stocks'];
        $variants[$color]['sizes'][] = $row['size'];
    }
    $colors = array_keys($variants);
    $defaultColor = $colors[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Product - Crabie Admin</title>
    <link href="asset/css/styles.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f7f7f7; }
        .admin-header { 
            background: #111; 
            color: white; 
            padding: 20px; 
            text-align: center; 
        }
        .admin-container { 
            padding: 40px 5%; 
        }
        .product-container {
            display: flex; 
            flex-wrap: wrap; 
            gap: 40px; 
        }
        .carousel-wrapper {
            flex: 1;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #carousel {
            position: relative;
            width: 100%;
            height: 400px;
            overflow: hidden;
            border-radius: 8px;
        }
        .carousel-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: none;
            position: absolute;
            top: 0;
            left: 0;
        }
        .carousel-image.active {
            display: block;
        }
        .details-wrapper { 
            flex: 1; 
            max-width: 500px; 
            background: #fff; 
            padding: 20px; 
            border-radius: 8px; 
        }
        .color-button { 
            display: inline-block; 
            width: 32px; height: 32px; 
            border: 2px solid #ccc; 
            border-radius: 50%; 
            margin-right: 10px; 
            cursor: pointer; 
        }
        .color-button.active { 
            border-color: black; 
        }
        .size-box { 
            display: inline-block; 
            padding: 5px 10px; 
            border: 1px solid #999; 
            margin: 5px; 
            border-radius: 4px; 
        }
        .price-tag { 
            font-size: 20px; 
            margin: 10px 0; 
        }
        .original-price { 
            text-decoration: line-through; 
            color: red; 
            margin-right: 10px; 
        }
        .admin-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: #fefefe;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 12px;
            font-family: 'Segoe UI', sans-serif;
        }

        .admin-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #222;
        }

        .admin-container h3 {
            margin-top: 30px;
            font-size: 20px;
            color: #444;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
        }

        .form-group input[type="text"],
        .form-group textarea,
        .variant-row input,
        .variant-row select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.2s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .variant-row input:focus,
        .variant-row select:focus {
            border-color: #000000ff;
            outline: none;
        }

        .variant-row {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            background: #fafafa;
        }

        .variant-row > * {
            margin-bottom: 12px;
        }

        .variant-row input[type="file"] {
            padding: 6px;
        }

        .admin-btn {
            padding: 12px 25px;
            background: #000000ff;
            border: none;
            color: white;
            font-weight: bold;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .admin-btn:hover {
            background: #000000ff;
        }

        /* Responsive grid for variant fields */
        @media screen and (min-width: 768px) {
            .variant-row {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                gap: 12px;
                align-items: end;
            }
        }
    </style>
</head>

<?php

    if($action === 'view'){

?>

        <div class="admin-header">
            <h1>View Product - Crabie Admin</h1>
        </div>

        <div class="admin-container">
            <div class="product-container">

                <!-- LEFT: Image Carousel -->
                <div class="carousel-wrapper">
                    <div id="carousel">
                        <?php foreach ($variants[$defaultColor]['images'] as $index => $img): ?>
                            <img src="<?= $img ?>" class="carousel-image <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>" data-color="<?= $defaultColor ?>">
                        <?php endforeach; ?>
                    </div>
                    <div style="margin-top: 10px; text-align: center;">
                        <button id="prevBtn">Prev</button>
                        <button id="nextBtn">Next</button>
                    </div>
                </div>

                <!-- RIGHT: Product Info -->
                <div class="details-wrapper">
                    <h2><?= htmlspecialchars($product['name']) ?></h2>
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

                    <div class="price-tag">
                        <span class="original-price">₹<?= $variants[$defaultColor]['prices'][0]['original'] ?></span>
                        <strong>₹<?= $variants[$defaultColor]['prices'][0]['discounted'] ?></strong>
                    </div>

                    <p><strong>Stocks:</strong> <?= $variants[$defaultColor]['stocks'][0] ?></p>

                    <p><strong>Colors:</strong></p>
                    <?php foreach ($colors as $color): ?>
                        <div class="color-button" data-color="<?= $color ?>" style="background: <?= $color ?>;"></div>
                    <?php endforeach; ?>

                    <p><strong>Sizes:</strong></p>
                    <?php foreach (array_unique($variants[$defaultColor]['sizes']) as $size): ?>
                        <div class="size-box"><?= $size ?></div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>

<?php
    }
    else if($action === 'edit'){

        $query = "
            SELECT * FROM product_variants 
            WHERE product_id = :id
        ";
        $stmt = $connection->prepare($query);
        $stmt->execute([':id' => $productId]);
        $variantList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    
        <div class="admin-header">
            <h1>Edit Product - Crabie Admin</h1>
        </div>

        <div class="admin-container">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?= $productId ?>">
                
                <h2>Edit Product: <?= htmlspecialchars($product['name']) ?></h2>

                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>

                <h3>Variants</h3>

                <?php foreach ($variantList as $index => $variant): ?>
                    <input type="hidden" name="variant_ids[]" value="<?= $variant['id'] ?>">
                    <div class="variant-row">
                        <select name="variant_category[]" class="size" required>
                            <option value="Mens" <?= $variant['category'] === 'Mens' ? 'selected' : '' ?>>Mens</option>
                            <option value="Womens" <?= $variant['category'] === 'Womens' ? 'selected' : '' ?>>Womens</option>
                        </select>

                        <select name="variant_size[]" class="size" required>
                            <?php foreach (['S', 'M', 'L', 'XL', 'XXL', 'XXXL'] as $size): ?>
                                <option value="<?= $size ?>" <?= $variant['size'] === $size ? 'selected' : '' ?>><?= $size ?></option>
                            <?php endforeach; ?>
                        </select>

                        <input type="text" name="variant_color[]" placeholder="Color" value="<?= $variant['color'] ?>" required>
                        <input type="text" name="variant_original_price[]" placeholder="Original Price" value="<?= $variant['original_price'] ?>" required>
                        <input type="text" name="variant_discounted_price[]" placeholder="Discounted Price" value="<?= $variant['discounted_price'] ?>" required>
                        <input type="text" name="variant_stocks[]" placeholder="Stocks" value="<?= $variant['stocks'] ?>" required>
                    </div>
                <?php endforeach; ?>

                <div style="margin-top: 20px;">
                    <button type="submit" name="update-product" class="admin-btn">Update Product</button>
                </div>
            </form>
        </div>

<?php
    }
?>


<script>
    let currentSlide = 0;
    const colorButtons = document.querySelectorAll('.color-button');
    const carousel = document.getElementById('carousel');

    const variantImages = <?= json_encode($variants) ?>;

    colorButtons.forEach(button => {
        button.addEventListener('click', () => {
            const selectedColor = button.dataset.color;

            // Remove active class from all
            document.querySelectorAll('.color-button').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            // Update images
            carousel.innerHTML = "";
            variantImages[selectedColor].images.forEach((img, index) => {
                const imgElem = document.createElement('img');
                imgElem.src = img;
                imgElem.className = index === 0 ? 'active' : '';
                imgElem.classList.add('carousel-image');
                imgElem.dataset.index = index;
                carousel.appendChild(imgElem);

            });

            // Update prices
            const priceDiv = document.querySelector(".price-tag");
            priceDiv.innerHTML = `
                <span class="original-price">₹${variantImages[selectedColor].prices[0].original}</span>
                <strong>₹${variantImages[selectedColor].prices[0].discounted}</strong>
            `;

            // Update stocks
            document.querySelector("p strong + strong")?.remove();
            const stockElem = document.querySelector("p strong").parentElement;
            stockElem.innerHTML = `<strong>Stocks:</strong> ${variantImages[selectedColor].stocks[0]}`;

            // Update sizes
            const sizes = [...new Set(variantImages[selectedColor].sizes)];
            const sizeWrapper = document.querySelectorAll(".size-box");
            sizeWrapper.forEach(el => el.remove());
            const detailWrapper = document.querySelector(".details-wrapper");
            const sizeParagraph = [...detailWrapper.querySelectorAll("p")].find(p => p.textContent.includes("Sizes"));
            sizes.forEach(size => {
                const sizeBox = document.createElement("div");
                sizeBox.className = "size-box";
                sizeBox.textContent = size;
                sizeParagraph.insertAdjacentElement("afterend", sizeBox);
            });
            reinitializeCarousel();
        });
    });

    // carousellet currentSlide = 0;

    function showSlide(index) {
        const slides = document.querySelectorAll('.carousel-image');
        if (slides.length === 0) return;

        slides.forEach(slide => slide.classList.remove('active'));
        currentSlide = (index + slides.length) % slides.length;
        slides[currentSlide].classList.add('active');
    }

    document.getElementById('prevBtn').addEventListener('click', () => {
        showSlide(currentSlide - 1);
    });

    document.getElementById('nextBtn').addEventListener('click', () => {
        showSlide(currentSlide + 1);
    });

    // Reinitialize on color change
    function reinitializeCarousel() {
        currentSlide = 0;
        showSlide(currentSlide);
    }

</script>

</body>
</html>
