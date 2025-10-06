<?php
session_start();

// Подключение к базе данных
include 'db_connect.php';

if (isset($_GET['query'])) {
    $search = mysqli_real_escape_string($conn, $_GET['query']);
    
    // Поиск по названию и описанию товаров
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.name LIKE '%$search%' 
              OR p.description LIKE '%$search%' 
              LIMIT 10";
              
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<div class='search-results-container'>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='search-result-item'>";
            
            // Изображение товара
            // Корректный путь к картинке
            $image_path = "images/products/" . $row['id'] . "_1.png";
            if (!file_exists($image_path)) $image_path = "images/products/default.jpg";

            echo "<img src='" . $image_path . "' alt='" . htmlspecialchars($row['name']) . "' class='search-result-image'>";

            
            echo "<div class='search-result-info'>";
            echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
            
            // Категория
            if (!empty($row['category_name'])) {
                echo "<span class='category-tag'>" . htmlspecialchars($row['category_name']) . "</span>";
            }
            
            // Цена
            echo "<p class='price'>" . number_format($row['price'], 0, '', ' ') . " ₽</p>";
            
            // Краткое описание (если есть)
            if (!empty($row['description'])) {
                $short_desc = substr($row['description'], 0, 100);
                if (strlen($row['description']) > 100) {
                    $short_desc .= '...';
                }
                echo "<p class='description'>" . htmlspecialchars($short_desc) . "</p>";
            }
            
            echo "<a href='product.php?id=" . $row['id'] . "' class='view-product'>Подробнее</a>";
            echo "</div>"; // .search-result-info
            echo "</div>"; // .search-result-item
        }
        
        echo "</div>"; // .search-results-container
        
        // Добавляем стили для результатов поиска
        echo "<style>
            .search-results-container {
                background: var(--background-light);
                border-radius: 8px;
                overflow: hidden;
                box-shadow: var(--card-shadow);
            }
            
            .search-result-item {
                display: flex;
                padding: 1rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                transition: background-color 0.2s ease;
            }
            
            .search-result-item:last-child {
                border-bottom: none;
            }
            
            .search-result-item:hover {
                background-color: rgba(255, 255, 255, 0.05);
            }
            
            .search-result-image {
                width: 60px;
                height: 60px;
                object-fit: cover;
                border-radius: 4px;
                margin-right: 1rem;
            }
            
            .search-result-info {
                flex: 1;
            }
            
            .search-result-info h3 {
                margin: 0 0 0.5rem 0;
                font-size: 1rem;
                color: var(--text-primary);
            }
            
            .category-tag {
                display: inline-block;
                padding: 0.25rem 0.5rem;
                background-color: var(--primary-color);
                color: white;
                border-radius: 4px;
                font-size: 0.8rem;
                margin-bottom: 0.5rem;
            }
            
            .price {
                color: var(--primary-color);
                font-weight: 600;
                margin: 0.5rem 0;
            }
            
            .description {
                color: var(--text-secondary);
                font-size: 0.9rem;
                margin: 0.5rem 0;
            }
            
            .view-product {
                display: inline-block;
                color: var(--primary-color);
                text-decoration: none;
                font-size: 0.9rem;
                font-weight: 500;
            }
            
            .view-product:hover {
                text-decoration: underline;
            }
        </style>";
    } else {
        echo "<div class='no-results'>
                <p>По запросу «" . htmlspecialchars($search) . "» ничего не найдено</p>
                <style>
                    .no-results {
                        padding: 1rem;
                        text-align: center;
                        color: var(--text-secondary);
                    }
                </style>
              </div>";
    }
    
    mysqli_close($conn);
}
?>
