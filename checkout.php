<?php
session_start();

// Если пользователь не авторизован, перенаправляем на страницу входа
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db_connect.php';

// Получаем данные пользователя для автозаполнения формы
$user_id = intval($_SESSION['user_id']);
$user_query = mysqli_query($conn, "SELECT * FROM customers WHERE id = $user_id LIMIT 1");
$user_data = mysqli_fetch_assoc($user_query);

// Получаем методы доставки из delivery_types
$delivery_types = [];
$res = mysqli_query($conn, "SELECT id, name FROM delivery_types");
while ($row = mysqli_fetch_assoc($res)) {
    $delivery_types[] = $row;
}

// Если корзина пуста, редирект на каталог
if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    header("Location: catalog.php");
    exit;
}

// ---- КОРРЕКТНЫЙ СБОР ТОВАРОВ КОРЗИНЫ ----
$cart_items = [];
$total_price = 0;

if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $product_id = intval($product_id);
        $quantity = intval($quantity);

        $query = "SELECT * FROM products WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($product = mysqli_fetch_assoc($result)) {
            $image_path = "images/products/{$product_id}_1.png";
            if (!file_exists($image_path)) $image_path = "images/products/default.jpg";

            $subtotal = $product['price'] * $quantity;
            $total_price += $subtotal;
            $cart_items[] = [
                'id' => $product_id,
                'name' => $product['name'],
                'price' => $product['price'],
                'image_url' => $image_path,
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
        mysqli_stmt_close($stmt);
    }
}

// Оформление заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $delivery_method_id = intval($_POST['delivery_method'] ?? 1);
    $payment_method = $_POST['payment_method'] ?? 'card';

    // Обновить профиль пользователя (ФИО — все в name)
    $full_name = $last_name . ' ' . $first_name;
    mysqli_query($conn, "UPDATE customers 
        SET name='" . mysqli_real_escape_string($conn, $full_name) . "',
            email='" . mysqli_real_escape_string($conn, $email) . "',
            phone='" . mysqli_real_escape_string($conn, $phone) . "',
            address='" . mysqli_real_escape_string($conn, $address) . "',
            city='" . mysqli_real_escape_string($conn, $city) . "',
            postal_code='" . mysqli_real_escape_string($conn, $postal_code) . "'
        WHERE id = $user_id
    ");

    // --- КОРРЕКТНО УСТАНАВЛИВАЕМ СТАТУС ---
    $status = ($payment_method === 'card') ? 'paid' : 'created';

    // Вставить заказ (delivery_method — это id delivery_types)
    $query = "INSERT INTO orders (customer_id, total_amount, delivery_method, status) VALUES (
        '$user_id', '$total_price', '$delivery_method_id', '$status')";
    mysqli_query($conn, $query);
    $order_id = mysqli_insert_id($conn);

    // Записать товары заказа
    foreach ($cart_items as $item) {
        $product_id = intval($item['id']);
        $quantity = intval($item['quantity']);
        $price = floatval($item['price']);
        mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (
            '$order_id', '$product_id', '$quantity', '$price')");
    }

    unset($_SESSION['cart']);

    // --- КУДА ПЕРЕНАПРАВЛЯТЬ ---
    if ($payment_method === 'card') {
        // На фейковую оплату с передачей суммы и id заказа
        header("Location: fake_payment.php?order_id=$order_id&amount=$total_price");
        exit;
    } else {
        // Сразу успех, если наличка
        header('Location: checkout_success.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа - PC Parts Shop</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: var(--background-dark, #18181a); }
        .checkout-main { min-height: 100vh; display: flex; flex-direction: column; align-items: center; }
        .checkout-card { max-width: 1040px; width: 100%; margin: 50px auto 0 auto; background: #181818; border-radius: 18px; box-shadow: 0 6px 36px #000c; padding: 2.8rem 2.1rem 2.2rem 2.1rem; display: flex; gap: 2.5rem; justify-content: center;}
        .checkout-form { flex: 1 1 450px; min-width: 300px; max-width: 470px; background: none; padding: 0; border-radius: 16px; }
        .order-summary { width: 340px; max-width: 99vw; background: #222124; border-radius: 14px; box-shadow: 0 2px 18px #0005; padding: 2rem 1.3rem 1.3rem 1.3rem; height: fit-content; position: sticky; top: 110px; }
        h1 { color: #fff; text-align: left; margin: 40px 0 30px 10px; font-size: 2.1rem; letter-spacing: 1.2px; }
        .form-section { margin-bottom: 2rem; background: none;}
        .form-section:last-child { margin-bottom: 0; }
        .form-section h2 { font-size: 1.11rem; margin-bottom: 1.1rem; color: #e0e0e0; font-weight: 600; letter-spacing: 1px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-field { margin-bottom: 1.1rem; }
        .form-field label { display: block; margin-bottom: 0.55rem; color: #b6b6b6; font-size: .97rem; font-weight: 500; letter-spacing: .6px; }
        .form-field input, .form-field select { width: 100%; padding: 0.85rem 1rem; border: 1.5px solid #222; border-radius: 7px; background: #202124; color: #fff; font-size: 1.02rem; transition: border .17s; }
        .form-field input:focus, .form-field select:focus { outline: none; border-color: #8b0000; }
        .payment-methods { display: flex; flex-direction: column; gap: 1.1rem; }
        .payment-method { display: flex; align-items: center; padding: 1rem 1.1rem; border: 1.5px solid #292929; border-radius: 7px; background: #222124; cursor: pointer; transition: border-color 0.18s, box-shadow .16s;}
        .payment-method input[type="radio"] { margin-right: 1.2rem; accent-color: #8b0000; }
        .payment-method:hover { border-color: #8b0000; box-shadow: 0 2px 10px #45000030;}
        .payment-method-icon { margin-right: 1rem; color: #c1c1c1; font-size: 1.3em; }
        .place-order { width: 100%; margin-top: 1.2rem; padding: 1.05rem 0; background: #8b0000; color: #fff; border: none; border-radius: 8px; font-size: 1.18rem; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; transition: background .16s, transform .16s, box-shadow .13s; cursor: pointer; box-shadow: 0 1px 8px #9d121230; }
        .place-order:hover { background: #b80000; transform: translateY(-2px) scale(1.01); box-shadow: 0 2px 18px #70000036; }
        .summary-title { font-size: 1.12rem; margin-bottom: 1.05rem; color: #fff; font-weight: 700; letter-spacing: 1px;}
        .summary-items { margin-bottom: 1.2rem; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #c3c3c3; font-size: 0.98rem;}
        .summary-total { display: flex; justify-content: space-between; margin-top: 1rem; padding-top: 1rem; border-top: 1.5px solid rgba(255, 255, 255, 0.08); font-weight: 700; color: #fff; font-size: 1.12rem; }
        .summary-item:last-child { margin-bottom: 0;}
        @media (max-width: 1100px) { .checkout-card { flex-direction: column; align-items: stretch; gap: 2rem; padding: 2rem .5rem; max-width: 97vw; } .order-summary { margin: 0 auto; }}
        @media (max-width: 700px) { .checkout-card { max-width: 99vw; padding: 0.6rem 0.1rem 1.6rem 0.1rem; } .checkout-form { padding: 0.6rem 0.4rem 0.6rem 0.4rem; } h1 { font-size: 1.23rem; margin: 25px 0 20px 6px; }}
        @media (max-width: 480px) { .order-summary { padding: 1.2rem .5rem 1rem .5rem; } .checkout-form { padding: 0; }}
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <main class="checkout-main">
        <h1>Оформление заказа</h1>
        <div class="checkout-card">
            <form method="POST" class="checkout-form" autocomplete="off">
                <div class="form-section">
                    <h2>Контактные данные</h2>
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="first_name">Имя</label>
                            <input type="text" id="first_name" name="first_name" required value="<?php echo htmlspecialchars(explode(' ', $user_data['name'])[1] ?? ''); ?>">
                        </div>
                        <div class="form-field">
                            <label for="last_name">Фамилия</label>
                            <input type="text" id="last_name" name="last_name" required value="<?php echo htmlspecialchars(explode(' ', $user_data['name'])[0] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>">
                    </div>
                    <div class="form-field">
                        <label for="phone">Телефон</label>
                        <input type="tel" id="phone" name="phone" required value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-section">
                    <h2>Адрес доставки</h2>
                    <div class="form-field">
                        <label for="address">Адрес</label>
                        <input type="text" id="address" name="address" required value="<?php echo htmlspecialchars($user_data['address'] ?? ''); ?>">
                    </div>
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="city">Город</label>
                            <input type="text" id="city" name="city" required value="<?php echo htmlspecialchars($user_data['city'] ?? ''); ?>">
                        </div>
                        <div class="form-field">
                            <label for="postal_code">Почтовый индекс</label>
                            <input type="text" id="postal_code" name="postal_code" required value="<?php echo htmlspecialchars($user_data['postal_code'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="delivery_method">Способ доставки</label>
                        <select id="delivery_method" name="delivery_method" required>
                            <?php foreach ($delivery_types as $type): ?>
                                <option value="<?= $type['id'] ?>">
                                    <?= htmlspecialchars($type['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-section">
                    <h2>Способ оплаты</h2>
                    <div class="payment-methods">
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="card" checked>
                            <i class="fas fa-credit-card payment-method-icon"></i>
                            <span>Банковская карта</span>
                        </label>
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="cash">
                            <i class="fas fa-money-bill-wave payment-method-icon"></i>
                            <span>Наличными при получении</span>
                        </label>
                    </div>
                </div>
                <button type="submit" class="place-order">Оформить заказ</button>
            </form>
            <div class="order-summary">
                <h2 class="summary-title">Ваш заказ</h2>
                <div class="summary-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="summary-item">
                            <span><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></span>
                            <span><?php echo number_format($item['subtotal'], 0, '', ' '); ?> ₽</span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="summary-item">
                    <span>Доставка</span>
                    <span>Бесплатно</span>
                </div>
                <div class="summary-total">
                    <span>Итого</span>
                    <span><?php echo number_format($total_price, 0, '', ' '); ?> ₽</span>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
<?php mysqli_close($conn); ?>
