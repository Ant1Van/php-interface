# PHP Shop (simple)

Это простой PHP-проект магазина (статический по структуре файлов). В корне проекта находятся скрипты для каталога, корзины, оформления заказа и т.д.

Файлы в проекте (не исчерпывающий список):

- `index.php` — главная страница
- `catalog.php` — каталог товаров
- `product.php` — страница товара
- `cart.php`, `cart_ajax.php`, `cart_table_content.php` — корзина
- `checkout.php`, `checkout_success.php`, `fake_payment.php` — оформление заказа
- `login.php`, `logout.php`, `register.php`, `profile.php` — авторизация/профиль
- `db_connect.php` — подключение к БД
- `styles.css` — стили
- `images/products/` — изображения товаров

Требование
---------

Для корректной работы этот проект зависит от другого вашего репозитория: `csharp-db-app`.
Пожалуйста, установите или запустите `csharp-db-app` отдельно перед использованием этого проекта.

Репозиторий `csharp-db-app`: https://github.com/Ant1Van/csharp-db-app
