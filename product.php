<?php
session_start();
include 'db_connect.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$product_id = (int)$_GET['id'];
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.id = $product_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: index.php');
    exit();
}

$product = mysqli_fetch_assoc($result);
$category_id = $product['category_id'];

// Добавление в корзину
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $quantity = (int)$_POST['quantity'];
    if ($quantity > 0) {
        // Если товар уже в корзине — увеличиваем количество
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        header('Location: ' . $_SERVER['REQUEST_URI'] . '?added=1');
        exit();
    }
}

$can_review = false;
if (isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $check = "SELECT 1 FROM order_items oi
          JOIN orders o ON oi.order_id = o.id
          WHERE o.customer_id = $user_id
          AND oi.product_id = $product_id
          AND o.status IN ('paid', 'shipped')
          LIMIT 1";
    $res = mysqli_query($conn, $check);
    $can_review = mysqli_num_rows($res) > 0;
}



?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - PC Parts Shop</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }
        .product-gallery-modern {
            display: flex;
            align-items: flex-start;
            gap: 2rem;
            margin-top: 32px;
        }
        .gallery-thumbs {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .gallery-thumb {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border 0.2s;
            background: #1a1a1a;
        }
        .gallery-thumb.active,
        .gallery-thumb:hover {
            border: 2px solid #8b5cf6;
        }
        .gallery-main {
            width: 340px;
            height: 340px;
            border-radius: 14px;
            background: #191919;
            object-fit: contain;
            box-shadow: 0 2px 12px #111;
            display: block;
        }
        .product-details {
            background: var(--background-light);
            border-radius: 12px;
            padding: 2rem;
        }
        .product-category {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: var(--primary-color);
            color: white;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .product-title {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }
        .product-price {
            font-size: 1.5rem;
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .product-description {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .add-to-cart-form {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 2rem;
        }
        .quantity-input {
            width: 100px;
            padding: 0.75rem;
            border: 1px solid var(--background-dark);
            border-radius: 6px;
            background: var(--background-dark);
            color: var(--text-primary);
            font-size: 1rem;
        }
        .success-message {
            background-color: var(--success);
            color: white;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .specifications {
            margin-top: 2rem;
        }
        .specifications h2 {
            margin-bottom: 1rem;
        }
        .specs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }
        .spec-item {
            background: var(--background-dark);
            padding: 1rem;
            border-radius: 6px;
        }
        .spec-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        .spec-value {
            color: var(--text-primary);
            font-weight: 500;
        }
        .container {
            margin-top: 70px !important;
            padding-top: 50px;
        }
        @media (max-width: 900px) {
            .product-container {
                grid-template-columns: 1fr;
            }
            .gallery-main {
                width: 240px;
                height: 240px;
            }
        }
        @media (max-width: 600px) {
            .gallery-main { width: 120px; height: 120px; }
            .product-details { padding: 1rem; }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <?php if (isset($_GET['added'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                Товар успешно добавлен в корзину!
            </div>
        <?php endif; ?>

        <div class="product-container">
            <div class="product-gallery-modern">
                <?php
                $gallery_dir = "images/products/";
                $gallery = glob($gallery_dir . $product['id'] . "_*.png");
                if (!$gallery) $gallery = [$gallery_dir . "default.jpg"];
                ?>
                <div class="gallery-thumbs">
                    <?php foreach ($gallery as $i => $img): ?>
                        <img src="<?= $img ?>" class="gallery-thumb<?= $i === 0 ? ' active' : '' ?>" data-index="<?= $i ?>" alt="thumb">
                    <?php endforeach; ?>
                </div>
                <img src="<?= $gallery[0] ?>" class="gallery-main" id="mainProductImage">
            </div>

            <div class="product-details">
                <?php if (!empty($product['category_name'])): ?>
                    <div class="product-category">
                        <i class="fas fa-<?php echo getCategoryIcon($product['category_name']); ?>"></i>
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </div>
                <?php endif; ?>

                <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="product-price"><?php echo number_format($product['price'], 0, '', ' '); ?> ₽</div>

                <?php if (!empty($product['description'])): ?>
                    <div class="product-description">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="add-to-cart-form">
                    <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                    <button type="submit" name="add_to_cart" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i>
                        Добавить в корзину
                    </button>
                </form>

                <?php if (!empty($product['specifications'])): ?>
                    <div class="specifications">
                        <h2>Характеристики</h2>
                        <div class="specs-grid">
                            <?php
                            $specs = json_decode($product['specifications'], true);
                            if ($specs) {
                                foreach ($specs as $label => $value) {
                                    echo '<div class="spec-item">';
                                    echo '<div class="spec-label">' . htmlspecialchars($label) . '</div>';
                                    echo '<div class="spec-value">' . htmlspecialchars($value) . '</div>';
                                    echo '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
    const thumbs = document.querySelectorAll('.gallery-thumb');
    const mainImg = document.getElementById('mainProductImage');
    thumbs.forEach(thumb => {
        thumb.addEventListener('mouseenter', function() {
            mainImg.src = this.src;
            document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
    </script>
</body>
</html>

<?php
function getCategoryIcon($categoryName) {
    $icons = [
        'Процессоры' => 'microchip',
        'Видеокарты' => 'tv',
        'Материнские платы' => 'server',
        'Оперативная память' => 'memory',
        'SSD' => 'hdd',
        'HDD' => 'hdd',
        'Блоки питания' => 'plug',
        'Корпуса' => 'desktop',
        'Охлаждение' => 'fan'
    ];
    return $icons[$categoryName] ?? 'computer';
}
mysqli_close($conn);
?>
