<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Проверяем пользователя
    $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE email = :email");
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Сохраняем данные пользователя в сессии
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];  
        $_SESSION['role'] = $user['role'];  // Сохраняем роль пользователя

        // Перенаправление в зависимости от роли
        if ($user['role'] == 'admin') {
            // Если администратор, перенаправляем на страницу администрирования
            header("Location: admin_dashboard.php");
        } else {
            // Если обычный пользователь, перенаправляем на главную страницу
            header("Location: index.php");
        }
        exit;
    } else {
        echo "Неверный email или пароль!";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="styles.css"> <!-- Подключаем стили -->
</head>
<body>

    <div class="header">
        <h1>Вход</h1>
    </div>

    <div class="content">
        <h2>Введите свои данные для входа</h2>

        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>
            
            <label>Пароль:</label>
            <input type="password" name="password" required>

            <button type="submit">Войти</button>
        </form>

        <p style="text-align: center;">Нет аккаунта? <a href="register.php">Регистрация</a></p>
    </div>

</body>
</html>
