<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PC Parts Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #1a1a1a;
            color: #e0e0e0;
            min-height: 100vh;
        }

        .header {
            background: #121212;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.5);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid #333;
        }

        .header-content {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 2rem;
            align-items: center;
            padding: 1rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-family: 'Times New Roman', serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .logo-link {
            color: #9370DB;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            color: #8b0000;
        }

        .search-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            border: 2px solid #333;
            border-radius: 25px;
            background: #2a2a2a;
            color: #e0e0e0;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #8b0000;
            background: #1a1a1a;
            box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.2);
        }

        .search-input::placeholder {
            color: #666;
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 10px;
            margin-top: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            display: none;
        }

        .nav {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            background: transparent;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #e0e0e0;
            background: transparent;
            border: 1px solid #8b0000;
        }

        .btn:hover {
            background: #8b0000;
            transform: translateY(-2px);
        }

        .btn-primary {
            background: #8b0000;
            color: #e0e0e0;
            border: none;
        }

        .btn-primary:hover {
            background: #6b0000;
        }

        .cart-icon {
            position: relative;
            color: #e0e0e0;
            text-decoration: none;
            font-size: 1.2rem;
            padding: 0.5rem;
            transition: all 0.3s ease;
            background: transparent;
        }

        .cart-icon:hover {
            color: #8b0000;
            transform: translateY(-2px);
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #8b0000;
            color: #e0e0e0;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            font-weight: bold;
        }

        .page-title {
            color: #9370DB;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-family: 'Times New Roman', serif;
            position: relative;
        }

        .page-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 2px;
            background: #8b0000;
            margin: 15px auto 0;
        }

        @media (max-width: 768px) {
            .header-content {
                grid-template-columns: 1fr;
                padding: 1rem;
                gap: 1rem;
            }

            .nav {
                justify-content: center;
            }

            .logo {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <a href="index.php" class="logo-link">
                    <i class="fas fa-desktop"></i>
                    PC PARTS SHOP
                </a>
            </div>
            
            <div class="search-container">
                <input type="text" id="search-input" class="search-input" placeholder="Поиск товаров...">
                <div id="search-results" class="search-results"></div>
            </div>
            
            <nav class="nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="btn">
                        <i class="fas fa-user"></i>
                        Профиль
                    </a>
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <?php
                        if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                            echo '<span class="cart-count">' . count($_SESSION['cart']) . '</span>';
                        }
                        ?>
                    </a>
                    <a href="logout.php" class="btn">Выйти</a>
                <?php else: ?>
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <?php
                        if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                            echo '<span class="cart-count">' . count($_SESSION['cart']) . '</span>';
                        }
                        ?>
                    </a>
                    <a href="login.php" class="btn btn-primary">Войти</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <script>
    $(document).ready(function() {
        let searchTimeout;
        
        $('#search-input').on('input', function() {
            clearTimeout(searchTimeout);
            const query = $(this).val();
            
            if (query.length >= 3) {
                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: 'search.php',
                        method: 'GET',
                        data: { query: query },
                        success: function(response) {
                            $('#search-results').html(response).show();
                        }
                    });
                }, 300);
            } else {
                $('#search-results').hide();
            }
        });

        $(document).click(function(event) {
            if (!$(event.target).closest('.search-container').length) {
                $('#search-results').hide();
            }
        });
    });
    </script>
</body>
</html>
