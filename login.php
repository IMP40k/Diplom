<?php
require_once "config.php";
session_start();

/* ------------------ ЛОГИН ------------------ */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_submit'])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT id, name, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $id;
            $_SESSION["name"] = $name;
            header("location: main.php");
            exit;
        } else {
            echo "<div class='alert alert-danger'>Неверный пароль!</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Пользователь не найден!</div>";
    }
}

/* ------------------ ПОИСК ПОСТОВ ------------------ */

$q = "";
if (isset($_GET['q'])) {
    // очищаем строку поиска
    $q = trim($_GET['q']);
}

// Если запрос не пустой — используется подготовленный запрос
if ($q !== "") {

    $sql_posts = $conn->prepare(
        "SELECT * FROM posts 
         WHERE title LIKE CONCAT('%', ?, '%') 
         OR main_text LIKE CONCAT('%', ?, '%') 
         ORDER BY id DESC"
    );

    $sql_posts->bind_param("ss", $q, $q);
    $sql_posts->execute();
    $result_posts = $sql_posts->get_result();

} else {
    // просто все посты
    $result_posts = $conn->query("SELECT * FROM posts ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход / Поиск постов</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

    <style>
        body { padding-top: 30px; }
        .post-card { margin-top: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 6px; }
    </style>
</head>

<body>
<div class="container">
    <div class="row">

        <!-- ЛОГИН -->
        <div class="col-md-6">
            <h2>Вход</h2>
            <p>Введите свою почту и пароль.</p>

            <form action="" method="post">
                <div class="form-group">
                    <label>Электронная почта</label>
                    <input type="email" name="email" class="form-control" required />
                </div>

                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="form-group mt-3">
                    <input type="submit" name="login_submit" class="btn btn-primary" value="Войти">
                </div>

                <p class="mt-3">Нет аккаунта? <a href="register.php">Создайте его за минуту</a>.</p>
            </form>
        </div>


        <!-- ПОИСК -->
        <div class="col-md-6">
            <h2>Поиск постов</h2>
            <p>Искать можно по заголовку и по тексту.</p>

            <form method="get" action="">
                <div class="input-group mb-3">
                    <input type="text"
                           name="q"
                           class="form-control"
                           placeholder="Поиск..."
                           value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">Найти</button>
                    </div>
                </div>
            </form>

            <h5>Результаты</h5>

            <?php
            // выводим строку поиска
            if ($q !== "") {
                echo "<div><b>Поиск:</b> " . htmlspecialchars($q, ENT_QUOTES, 'UTF-8') . "</div>";
            }

            if ($result_posts && mysqli_num_rows($result_posts) > 0) {

                while ($row = $result_posts->fetch_assoc()) {
                    echo "<div class='post-card'>";
                    echo "<h5>" . htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') . "</h5>";
                    echo "<div>" . nl2br(htmlspecialchars($row['main_text'], ENT_QUOTES, 'UTF-8')) . "</div>";

                    if (isset($row['user_id'])) {
                        echo "<small class='text-muted'>ID автора: " . intval($row['user_id']) . "</small>";
                    } else {
                        echo "<small class='text-muted'>ID поста: " . intval($row['id']) . "</small>";
                    }

                    echo "</div>";
                }

            } else {
                echo "<p>Постов не найдено.</p>";
            }
            ?>
        </div>

    </div>
</div>
</body>
</html>

