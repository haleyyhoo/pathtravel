<?php
session_start();
include 'db.php';

// Проверка, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    // Получаем данные пользователя для редактирования
    $stmt = $db->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $user = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if (!$user) {
        echo "Пользователь не найден.";
        exit;
    }

    // Обработка изменения данных пользователя
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $role = trim($_POST['role']); // Роль может быть 'admin' или 'user'

        // Обновляем данные пользователя в базе данных
        $stmt = $db->prepare("UPDATE users SET username = :username, email = :email, role = :role WHERE id = :user_id");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':role', $role, SQLITE3_TEXT);
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);

        if ($stmt->execute()) {
            header("Location: admin_dashboard.php");
            exit;
        } else {
            echo "Ошибка при обновлении пользователя.";
        }
    }
} else {
    echo "Ошибка: ID пользователя не передан.";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать пользователя</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="header">
    <h1>Редактирование пользователя</h1>
</div>

<div class="content">
    <form method="POST">
        <label>Имя пользователя:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Роль:</label>
        <select name="role">
            <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>Пользователь</option>
            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Администратор</option>
        </select>

        <button type="submit">Сохранить изменения</button>
    </form>
</div>

</body>
</html>
