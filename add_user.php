<?php
session_start();
include 'db.php';

// Проверка, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':password', $password, SQLITE3_TEXT);
    $stmt->bindValue(':role', $role, SQLITE3_TEXT);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Ошибка при добавлении пользователя.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить пользователя</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="header">
<h1>Добавить пользователя</h1>
</div>
<div class="content">
<form method="POST">
    <label>Имя пользователя:</label>
    <input type="text" name="username" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Пароль:</label>
    <input type="password" name="password" required>

    <label>Роль:</label>
    <select name="role">
        <option value="user">Пользователь</option>
        <option value="admin">Администратор</option>
    </select>

    <button type="submit">Добавить</button>
</form>
</div>
</body>
</html>
