<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if ($password === $confirm_password) {
        // Хэшируем пароль для безопасности
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            header("location: login.php");
            exit;
        } else {
            echo "<div class='alert alert-danger'>Ошибка при регистрации!</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Пароли не совпадают!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Регистрация</title>
        <!-- подключаем бутстрап -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    </head>
    <body>
        <!-- вся страница будет в одном контейнере -->
        <div class="container mt-5">
            <div class="row">
                <!-- делаем самую простую вёрстку -->
                <div class="col-md-6 offset-md-3">
                    <h2>Регистрация</h2>
                    <p>Заполните все поля, чтобы создать новый аккаунт.</p>

                    <!-- форма регистрации -->
                    <form action="" method="post">
                        <div class="form-group">
                            <label>Имя</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>    

                        <div class="form-group">
                            <label>Электронная почта</label>
                            <input type="email" name="email" class="form-control" required />
                        </div>    

                        <div class="form-group">
                            <label>Пароль</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Повторите пароль</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>

                        <div class="form-group mt-3">
                            <input type="submit" name="submit" class="btn btn-primary" value="Зарегистрироваться">
                        </div>

                        <p class="mt-3">Уже зарегистрированы? <a href="login.php">Войдите в систему</a>.</p>
                    </form>
                </div>
            </div>
        </div>    
    </body>
</html>
