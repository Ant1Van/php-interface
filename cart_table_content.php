<?php
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    // Если в корзине есть товары
    echo "<table border='1' cellpadding='5'><tr>
            <th>Товар</th>
            <th>Количество</th>
            <th>Цена</th>
            <th>Удалить</th>
          </tr>";
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $query = "SELECT * FROM products WHERE id = $product_id";
        $result = mysqli_query($conn, $query);
        $product = mysqli_fetch_assoc($result);

        // Путь к картинке (основное фото товара)
        $image_path = "images/products/{$product_id}_1.png";
        if (!file_exists($image_path)) $image_path = "images/products/default.jpg";

        $total_price += $product['price'] * $quantity;
        echo "<tr>
            <td>
                <img src='$image_path' alt='' width='80' style='vertical-align:middle; border-radius:8px;'>
                <span style='margin-left: 10px;'>" . htmlspecialchars($product['name']) . "</span>
            </td>
            <td>
                <button class='cart-btn' data-id='$product_id' data-action='minus'>-</button>
                $quantity
                <button class='cart-btn' data-id='$product_id' data-action='plus'>+</button>
            </td>
            <td>{$product['price']} ₽</td>
            <td>
                <button class='remove-btn' data-id='$product_id'>
                    <img src='delete-icon.png' alt='Удалить' width='24'>
                </button>
            </td>
        </tr>";
    }
    echo "</table>";
    echo "<p><strong>Общая сумма: " . number_format($total_price, 2, '.', ' ') . " ₽</strong></p>";
    // Кнопка оформления заказа, если авторизован
    if (isset($_SESSION['user_id'])) {
        echo "<a href='checkout.php'>Оформить заказ</a>";
    } else {
        echo "<p><a href='login.php'>Пожалуйста, войдите в систему, чтобы оформить заказ.</a></p>";
    }
} else {
    // Если корзина пуста
    echo '
    <div style="text-align: center; padding: 50px;">
        <img src="empty-cart.png" alt="Корзина пуста" style="max-width:220px;display:block;margin:0 auto 30px;">
        <h2 style="font-size: 2em; margin-bottom: 16px;">Пока пусто</h2>
        <p style="font-size: 1.2em; color:#555;">Воспользуйтесь каталогом, чтобы добавить товары</p>
        <a href="index.php" style="
            display: inline-block;
            margin-top: 25px;
            padding: 14px 38px;
            background: #ff9100;
            color: #fff;
            border-radius: 12px;
            font-size: 1.1em;
            text-decoration: none;
            transition: background 0.2s;
        " onmouseover="this.style.background=\'#ffba4b\'" onmouseout="this.style.background=\'#ff9100\'">Перейти в каталог</a>
    </div>
    ';
}
?>
