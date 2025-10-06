<?php
session_start();
include 'db_connect.php';
include 'header.php';

// Получение информации о товарах в корзине
$cart_items = [];
$total = 0;

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
            // Новый путь к фото товара
            $image_path = "images/products/{$product_id}_1.png";
            if (!file_exists($image_path)) $image_path = "images/products/default.jpg";

            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;
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
?>

<style>
    .cart-container {
        max-width: 1200px;
        margin: 120px auto 40px;
        padding: 0 20px;
        min-height: 60vh;
    }

    .cart-items {
        background: #121212;
        border: 1px solid #333;
        border-radius: 15px;
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .cart-item {
        display: grid;
        grid-template-columns: auto 1fr auto auto;
        gap: 2rem;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid #333;
        transition: all 0.3s ease;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideOut {
        to {
            opacity: 0;
            transform: translateX(-100%);
        }
    }

    .cart-item.removing {
        animation: slideOut 0.5s ease forwards;
    }

    .cart-item:hover {
        background: #1a1a1a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .item-image {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #333;
        transition: transform 0.3s ease;
    }

    .cart-item:hover .item-image {
        transform: scale(1.05);
    }

    .item-details {
        color: #e0e0e0;
    }

    .item-name {
        font-size: 1.2rem;
        margin-bottom: 0.8rem;
        color: #e0e0e0;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .cart-item:hover .item-name {
        color: #9370DB;
    }

    .item-price {
        color: #8b0000;
        font-size: 1.1rem;
        font-weight: bold;
    }

    .item-quantity {
        display: flex;
        align-items: center;
        gap: 1rem;
        background: #1a1a1a;
        padding: 0.7rem;
        border-radius: 8px;
        border: 1px solid #333;
        transition: all 0.3s ease;
    }

    .cart-item:hover .item-quantity {
        border-color: #8b0000;
    }

    .quantity-btn {
        background: none;
        border: 1px solid #8b0000;
        color: #e0e0e0;
        width: 35px;
        height: 35px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .quantity-btn:hover {
        background: #8b0000;
        color: #fff;
        transform: translateY(-2px);
    }

    .quantity-btn:active {
        transform: translateY(0);
    }

    .quantity-value {
        color: #e0e0e0;
        font-size: 1.2rem;
        min-width: 30px;
        text-align: center;
        font-weight: bold;
    }

    .remove-btn {
        background: none;
        border: 1px solid #8b0000;
        color: #8b0000;
        cursor: pointer;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .remove-btn:hover {
        background: #8b0000;
        color: #fff;
        transform: rotate(90deg);
    }

    .empty-cart {
        text-align: center;
        padding: 4rem;
        background: #121212;
        border: 1px solid #333;
        border-radius: 15px;
        margin: 2rem auto;
        max-width: 600px;
        animation: fadeIn 0.5s ease;
    }

    .empty-cart p {
        margin: 1rem 0 2rem;
        font-size: 1.2rem;
        color: #b0b0b0;
    }

    .return-button {
        display: inline-block;
        padding: 1rem 2rem;
        background: #8b0000;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        border: 1px solid #8b0000;
        position: relative;
        overflow: hidden;
    }

    .return-button:hover {
        background: #6b0000;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 0, 0, 0.2);
    }

    .return-button::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 5px;
        height: 5px;
        background: rgba(255, 255, 255, 0.5);
        opacity: 0;
        border-radius: 100%;
        transform: scale(1, 1) translate(-50%);
        transform-origin: 50% 50%;
    }

    .return-button:hover::after {
        animation: ripple 1s ease-out;
    }

    @keyframes ripple {
        0% {
            transform: scale(0, 0);
            opacity: 0.5;
        }
        100% {
            transform: scale(20, 20);
            opacity: 0;
        }
    }

    .cart-summary {
        background: #121212;
        border: 1px solid #333;
        border-radius: 15px;
        padding: 2rem;
        margin-top: 2rem;
        animation: fadeIn 0.5s ease;
    }

    .cart-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #e0e0e0;
        font-size: 1.3rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #333;
    }

    .checkout-btn {
        width: 100%;
        padding: 1.2rem;
        background: #8b0000;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        position: relative;
        overflow: hidden;
    }

    .checkout-btn:hover {
        background: #6b0000;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 0, 0, 0.2);
    }

    .checkout-btn::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 5px;
        height: 5px;
        background: rgba(255, 255, 255, 0.5);
        opacity: 0;
        border-radius: 100%;
        transform: scale(1, 1) translate(-50%);
        transform-origin: 50% 50%;
    }

    .checkout-btn:hover::after {
        animation: ripple 1s ease-out;
    }

    .loading {
        position: relative;
        pointer-events: none;
    }

    .loading::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 8px;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #fff;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 768px) {
        .cart-item {
            grid-template-columns: 1fr;
            text-align: center;
            gap: 1.5rem;
            padding: 2rem 1.5rem;
        }

        .item-image {
            margin: 0 auto;
            width: 180px;
            height: 180px;
        }

        .item-quantity {
            justify-content: center;
            margin: 0 auto;
            padding: 1rem;
        }

        .remove-btn {
            margin: 0 auto;
            width: 50px;
            height: 50px;
            font-size: 1.4rem;
        }

        .quantity-btn {
            width: 45px;
            height: 45px;
        }

        .quantity-value {
            font-size: 1.4rem;
            min-width: 40px;
        }
    }

    /* Добавляем новые стили для анимаций */
    .quantity-value.changed {
        animation: pulse 0.3s ease-out;
    }

    .total-amount.changed {
        animation: pulse 0.3s ease-out;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.2);
            color: #8b0000;
        }
        100% {
            transform: scale(1);
        }
    }
</style>

<div class="cart-container">
    <h1 class="page-title">КОРЗИНА</h1>

    <div id="cart-content">
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <p>Ваша корзина пуста</p>
                <a href="index.php" class="return-button">Перейти к покупкам</a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item" data-id="<?php echo $item['id']; ?>">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                             class="item-image">
                        <div class="item-details">
                            <h3 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="item-price" data-price="<?php echo $item['price']; ?>">
                                <?php echo number_format($item['price'], 0, '', ' '); ?> ₽
                            </p>
                        </div>
                        <div class="item-quantity">
                            <button class="quantity-btn minus-btn">-</button>
                            <span class="quantity-value"><?php echo $item['quantity']; ?></span>
                            <button class="quantity-btn plus-btn">+</button>
                        </div>
                        <button class="remove-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <div class="cart-total">
                    <span>Итого:</span>
                    <span class="total-amount"><?php echo number_format($total, 0, '', ' '); ?> ₽</span>
                </div>
                <button class="checkout-btn" onclick="proceedToCheckout()">Оформить заказ</button>
            </div>
        <?php endif; ?>
    </div>
</div>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация корзины
    const cart = {
        items: new Map(),
        init() {
            document.querySelectorAll('.cart-item').forEach(item => {
                const id = parseInt(item.dataset.id);
                const price = parseFloat(item.querySelector('.item-price').dataset.price);
                const quantity = parseInt(item.querySelector('.quantity-value').textContent);
                this.items.set(id, { price, quantity });
                item.querySelector('.minus-btn').addEventListener('click', () => this.updateQuantity(id, -1));
                item.querySelector('.plus-btn').addEventListener('click', () => this.updateQuantity(id, 1));
                item.querySelector('.remove-btn').addEventListener('click', () => this.removeItem(id));
            });
        },
        async updateQuantity(productId, change) {
            const item = document.querySelector(`.cart-item[data-id="${productId}"]`);
            const quantityContainer = item.querySelector('.item-quantity');
            const quantityElement = quantityContainer.querySelector('.quantity-value');
            const currentQuantity = parseInt(quantityElement.textContent);
            if (currentQuantity + change < 1) return;
            quantityContainer.classList.add('loading');
            try {
                const response = await fetch('cart_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=change_qty&id=${productId}&qty_action=${change > 0 ? 'plus' : 'minus'}`
                });
                if (response.ok) {
                    const newQuantity = currentQuantity + change;
                    quantityElement.textContent = newQuantity;
                    const itemData = this.items.get(productId);
                    itemData.quantity = newQuantity;
                    this.updateTotal();
                    quantityElement.classList.add('changed');
                    setTimeout(() => quantityElement.classList.remove('changed'), 300);
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                quantityContainer.classList.remove('loading');
            }
        },
        async removeItem(productId) {
            if (!confirm('Вы уверены, что хотите удалить этот товар из корзины?')) return;
            const item = document.querySelector(`.cart-item[data-id="${productId}"]`);
            item.classList.add('removing');
            try {
                const response = await fetch('cart_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=remove&id=${productId}`
                });
                if (response.ok) {
                    this.items.delete(productId);
                    setTimeout(() => {
                        item.remove();
                        this.updateTotal();
                        if (this.items.size === 0) {
                            this.showEmptyCart();
                        }
                    }, 500);
                }
            } catch (error) {
                console.error('Error:', error);
                item.classList.remove('removing');
            }
        },
        updateTotal() {
            let total = 0;
            this.items.forEach(item => {
                total += item.price * item.quantity;
            });
            const totalElement = document.querySelector('.total-amount');
            const formattedTotal = new Intl.NumberFormat('ru-RU').format(total);
            totalElement.textContent = `${formattedTotal} ₽`;
            totalElement.classList.add('changed');
            setTimeout(() => totalElement.classList.remove('changed'), 300);
        },
        showEmptyCart() {
            const cartContent = document.getElementById('cart-content');
            cartContent.innerHTML = `
                <div class="empty-cart">
                    <p>Ваша корзина пуста</p>
                    <a href="index.php" class="return-button">Перейти к покупкам</a>
                </div>
            `;
        }
    };
    cart.init();
    document.querySelectorAll('.quantity-btn, .remove-btn, .checkout-btn, .return-button').forEach(button => {
        button.addEventListener('mouseenter', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            this.style.setProperty('--x', x + 'px');
            this.style.setProperty('--y', y + 'px');
        });
    });
});

function proceedToCheckout() {
    const btn = event.target;
    btn.classList.add('loading');
    setTimeout(() => {
        window.location.href = 'checkout.php';
    }, 500);
}
</script>

<?php mysqli_close($conn); ?>