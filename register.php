<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Проверяем, есть ли уже такой пользователь
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt->execute();

    if ($result->fetchArray()) {
        echo "Пользователь с таким email уже существует!";
    } else {
        // Добавляем пользователя в БД
        $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':password', $password, SQLITE3_TEXT);
        $stmt->execute();

        $_SESSION['username'] = $username;
        $user_id = $_SESSION['user_id'];
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="styles.css"> <!-- Подключаем стили -->
</head>
<body>

    <div class="header">
        <h1>Регистрация</h1>
    </div>

    <div class="content">
        <h2>Создайте учетную запись</h2>

        <form method="POST">
            <label>Имя пользователя:</label>
            <input type="text" name="username" required>
            
            <label>Email:</label>
            <input type="email" name="email" required>
            
            <label>Пароль:</label>
            <input type="password" name="password" required>

            <button type="submit">Зарегистрироваться</button>
        </form>

        <p style="text-align: center;">Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>

</body>
</html>
