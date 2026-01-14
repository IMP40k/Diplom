<?php
$servername = "db"; // имя контейнера из docker-compose.yml
$username = "root";
$password = "root";
$dbname = "users_db";

// Создание подключения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

