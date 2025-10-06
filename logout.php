<?php
session_start();

// Уничтожаем все данные сессии
session_unset();
session_destroy();

// Перенаправляем на главную страницу
header("Location: index.php");
exit;
?>
