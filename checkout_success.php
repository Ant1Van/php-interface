<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ оформлен - PC Parts Shop</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .success-container {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--background-light);
            border-radius: 12px;
            margin-top: 2rem;
        }

        .success-icon {
            font-size: 4rem;
            color: var(--success);
            margin-bottom: 2rem;
            animation: scale-in 0.5s ease;
        }

        .success-title {
            font-size: 2rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .success-message {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .success-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        @keyframes scale-in {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .success-actions {
                flex-direction: column;
            }

            .success-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container" style="margin-top: 80px;">
        <div class="success-container">
            <i class="fas fa-check-circle success-icon"></i>
            <h1 class="success-title">Заказ успешно оформлен!</h1>
            <p class="success-message">
                Спасибо за ваш заказ. Мы отправили подтверждение на ваш email.<br>
                Наш менеджер свяжется с вами в ближайшее время для уточнения деталей.
            </p>
            <div class="success-actions">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i>
                    Вернуться на главную
                </a>
                <a href="#" class="btn">
                    <i class="fas fa-file-alt"></i>
                    Отследить заказ
                </a>
            </div>
        </div>
    </main>
</body>
</html> 