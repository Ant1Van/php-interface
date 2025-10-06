<?php
// Конфигурация подключения к базе данных
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = '300'; // Имя твоей базы данных

$conn = mysqli_connect($host, $username, $password, $dbname);

// Проверка подключения
if (!$conn) {
    die("Ошибка подключения: " . mysqli_connect_error());
}
?>
