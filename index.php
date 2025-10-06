<?php
session_start();
include 'db_connect.php';

// Получаем категории из базы данных
$query = "SELECT * FROM categories ORDER BY id";
$result = mysqli_query($conn, $query);
$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row; // Изменено для сохранения всей информации о категории
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PC Parts Shop - Магазин компьютерных комплектующих</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hero {
            text-align: center;
            padding: 3rem 0;
            background: linear-gradient(to bottom, #1a1a1a, #2a2a2a);
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #e0e0e0;
        }

        .hero p {
            color: #b0b0b0;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
        }

        .category-card {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #e0e0e0;
            position: relative;
            overflow: hidden;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            border-color: #8b0000;
        }

        .category-card:hover .category-icon {
            color: #8b0000;
            transform: scale(1.1);
        }

        .category-icon {
            font-size: 3rem;
            color: #666;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .category-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #e0e0e0;
        }

        .category-description {
            color: #b0b0b0;
            font-size: 0.9rem;
            line-height: 1.4;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .categories-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container" style="margin-top: 80px;">
        <section class="hero">
            <h1>Добро пожаловать в PC Parts Shop!</h1>
            <p>Выберите категорию и найдите лучшие комплектующие для вашего компьютера</p>
        </section>

        <section class="categories-grid">
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
                    'Охлаждение процессора' => 'fan'
                ];
                
                return $icons[$categoryName] ?? 'computer';
            }

            foreach ($categories as $category) {
                $icon = getCategoryIcon($category['name']);
                echo "<a href='catalog.php?category=" . intval($category['id']) . "' class='category-card'>";
                echo "<i class='fas fa-{$icon} category-icon'></i>";
                echo "<h3 class='category-name'>{$category['name']}</h3>";
                if (isset($category['description']) && !empty($category['description'])) {
                    echo "<p class='category-description'>{$category['description']}</p>";
                }
                echo "</a>";
            }
            ?>
        </section>
    </main>

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
