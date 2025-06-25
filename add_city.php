<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $image_path = trim($_POST['image_path']);

    $stmt = $db->prepare("INSERT INTO cities (name, description, image_path) VALUES (:name, :description, :image_path)");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':image_path', $image_path, SQLITE3_TEXT);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Ошибка при добавлении города.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить город</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="header">
    <h1>Добавление города</h1>
</div>

<div class="content">
    <form method="POST">
        <label>Название:</label>
        <input type="text" name="name" required>

        <label>Описание:</label>
        <textarea name="description" required></textarea>

        <label>Путь к изображению:</label>
        <input type="text" name="image_path">

        <button type="submit">Добавить город</button>
    </form>
</div>
</body>
</html>
