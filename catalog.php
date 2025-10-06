<?php
session_start();
include 'db_connect.php';

// Получение категории из URL
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Получение параметров сортировки
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';
$price_min = isset($_GET['price_min']) ? (float)$_GET['price_min'] : null;
$price_max = isset($_GET['price_max']) ? (float)$_GET['price_max'] : null;

// Формирование SQL запроса
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";

if ($category_id) {
    $sql .= " AND p.category_id = $category_id";
}

if ($price_min !== null) {
    $sql .= " AND p.price >= $price_min";
}

if ($price_max !== null) {
    $sql .= " AND p.price <= $price_max";
}

// Добавление сортировки
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'name_desc':
        $sql .= " ORDER BY p.name DESC";
        break;
    default:
        $sql .= " ORDER BY p.name ASC";
}

$result = mysqli_query($conn, $sql);

// Если товар добавлен в корзину
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = 1;  // Стандартное количество товара всегда 1
    // Если корзина уже существует в сессии, добавляем товар
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    // Добавляем товар в корзину
    $_SESSION['cart'][$product_id] = $quantity;
    echo "<script>alert('Товар добавлен в корзину!');</script>";
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог товаров - PC Parts Shop</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .catalog-container {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
        }
        .filters {
            width: 250px;
            background: var(--background-light);
            padding: 1.5rem;
            border-radius: 8px;
            position: sticky;
            top: 100px;
            height: fit-content;
        }
        .filter-section {
            margin-bottom: 1.5rem;
        }
        .filter-section h3 {
            margin-bottom: 1rem;
            color: var(--text-primary);
        }
        .price-inputs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .price-inputs input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--background-dark);
            border-radius: 4px;
            background: var(--background-dark);
            color: var(--text-primary);
        }
        .sort-options {
            margin-bottom: 2rem;
        }
        .sort-select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--background-dark);
            border-radius: 4px;
            background: var(--background-dark);
            color: var(--text-primary);
        }
        .products-section {
            flex: 1;
        }
        .no-products {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
        }
        .apply-filters {
            width: 100%;
            margin-top: 1rem;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }
        .product-card {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            border-color: #8b0000;
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: contain;    /* <-- ВАЖНО! */
            background: #222;       /* Чтобы было красиво, если фото меньше блока */
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .container {
            margin-top: 70px !important;
            padding-top: 50px;
        }
        .product-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #e0e0e0;
        }
        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #8b0000;
            margin-bottom: 1rem;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #8b0000;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: #700000;
        }
        .btn-primary {
            background: #8b0000;
        }
        .btn-primary:hover {
            background: #700000;
        }
        .loading {
            text-align: center;
            padding: 2rem;
        }
        .loading i {
            font-size: 2rem;
            color: #8b0000;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <main class="container" style="margin-top: 80px;">
        <div class="catalog-container">
            <!-- Фильтры -->
            <aside class="filters">
                <form id="filters-form" method="GET">
                    <?php if ($category_id): ?>
                        <input type="hidden" name="category" value="<?php echo $category_id; ?>">
                    <?php endif; ?>
                    <div class="filter-section">
                        <h3>Сортировка</h3>
                        <select name="sort" class="sort-select" onchange="this.form.submit()">
                            <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>По названию (А-Я)</option>
                            <option value="name_desc" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>По названию (Я-А)</option>
                            <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Сначала дешевле</option>
                            <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Сначала дороже</option>
                        </select>
                    </div>
                    <div class="filter-section">
                        <h3>Цена</h3>
                        <div class="price-inputs">
                            <input type="number" name="price_min" placeholder="От" value="<?php echo $price_min ?? ''; ?>">
                            <input type="number" name="price_max" placeholder="До" value="<?php echo $price_max ?? ''; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary apply-filters">Применить</button>
                    </div>
                </form>
            </aside>
            <!-- Товары -->
            <div class="products-section">
                <?php if ($category_id): ?>
                    <?php
                    $category_query = "SELECT name FROM categories WHERE id = $category_id";
                    $category_result = mysqli_query($conn, $category_query);
                    $category = mysqli_fetch_assoc($category_result);
                    ?>
                    <h1><?php echo htmlspecialchars($category['name']); ?></h1>
                <?php else: ?>
                    <h1>Все товары</h1>
                <?php endif; ?>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <div id="products-container" class="product-grid">
                        <?php while ($product = mysqli_fetch_assoc($result)): ?>
                            <div class="product-card">
                                <?php
                                $image_path = "images/products/" . $product['id'] . "_1.png";
                                if (!file_exists($image_path)) $image_path = "images/products/default.jpg";
                                ?>
                                <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                                
                                <div class="product-info">
                                    <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="product-price"><?php echo number_format($product['price'], 0, '', ' '); ?> ₽</p>
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">Подробнее</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="no-products">
                        <p>Товары не найдены</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <script>
        // Функция для загрузки товаров по категории
        function loadProducts(categoryId, sort = 'name_asc', priceMin = null, priceMax = null) {
            const productsContainer = document.getElementById('products-container');
            productsContainer.innerHTML = '<div class="loading"><i class="fas fa-spinner"></i></div>';
            
            const url = new URL('catalog.php', window.location.origin);
            
            if (categoryId) {
                url.searchParams.set('category', categoryId);
            }
            
            url.searchParams.set('sort', sort);
            
            if (priceMin !== null && priceMin !== '') {
                url.searchParams.set('price_min', priceMin);
            }
            
            if (priceMax !== null && priceMax !== '') {
                url.searchParams.set('price_max', priceMax);
            }
            
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    // Извлекаем только содержимое контейнера с товарами
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    const newContent = tempDiv.querySelector('#products-container').innerHTML;
                    
                    // Обновляем содержимое контейнера
                    productsContainer.innerHTML = newContent;
                })
                .catch(error => {
                    console.error('Error loading products:', error);
                    productsContainer.innerHTML = '<div class="no-products"><p>Произошла ошибка при загрузке товаров</p></div>';
                });
        }
        
        // Обработчик для кнопок сортировки
        document.querySelectorAll('.sort-select').forEach(select => {
            select.addEventListener('change', function() {
                const categoryId = new URLSearchParams(window.location.search).get('category');
                const sort = this.value;
                const priceMin = document.querySelector('input[name="price_min"]').value;
                const priceMax = document.querySelector('input[name="price_max"]').value;
                
                loadProducts(categoryId, sort, priceMin, priceMax);
            });
        });
        
        // Обработчик для кнопки применения фильтров
        document.querySelector('.apply-filters').addEventListener('click', function(e) {
            e.preventDefault();
            const categoryId = new URLSearchParams(window.location.search).get('category');
            const sort = document.querySelector('.sort-select').value;
            const priceMin = document.querySelector('input[name="price_min"]').value;
            const priceMax = document.querySelector('input[name="price_max"]').value;
            
            loadProducts(categoryId, sort, priceMin, priceMax);
        });
        
        // Загрузка товаров при загрузке страницы, если есть параметры в URL
        document.addEventListener('DOMContentLoaded', function() {
            const categoryId = new URLSearchParams(window.location.search).get('category');
            const sort = new URLSearchParams(window.location.search).get('sort') || 'name_asc';
            const priceMin = new URLSearchParams(window.location.search).get('price_min');
            const priceMax = new URLSearchParams(window.location.search).get('price_max');
            
            if (categoryId || sort !== 'name_asc' || priceMin || priceMax) {
                loadProducts(categoryId, sort, priceMin, priceMax);
            }
        });
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>