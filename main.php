<?php
session_start();
require_once "config.php"; // подключаем БД

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $main_text = $_POST['text'];

    if (!$title || !$main_text) die("Заполните все поля");

    $sql = "INSERT INTO posts (title, main_text) VALUES ('$title','$main_text')";
    if (!mysqli_query($conn, $sql)) die("Ошибка добавления поста: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Добро пожаловать, <?= htmlspecialchars($_SESSION["name"]); ?>!</h2>
    <a href="logout.php" class="btn btn-primary mb-4">Выйти</a>

    <h4>Добавить пост</h4>
    <form method="POST" action="">
        <input type="text" class="form-control mb-2" name="title" placeholder="Заголовок поста">
        <textarea name="text" class="form-control mb-2" rows="5" placeholder="Введите текст поста..."></textarea>
        <button type="submit" class="btn btn-success" name="submit">Сохранить пост</button>
    </form>

    <hr>

    <h4>Все посты</h4>
    <?php
    $result = mysqli_query($conn, "SELECT * FROM posts ORDER BY id DESC");
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='card mt-3 p-3'>";
            echo "<h5>" . $row['title'] . "</h5>";
            echo "<p>" . nl2br($row['main_text']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>Постов пока нет.</p>";
    }
    ?>
</div>
</body>
</html>

