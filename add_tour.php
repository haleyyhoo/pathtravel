<?php
session_start();
include 'db.php';

// Проверка, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $city_id = trim($_POST['city_id']);
    $price = trim($_POST['price']);
    $date = trim($_POST['date']);

    $stmt = $db->prepare("INSERT INTO tours (name, city_id, price, date) VALUES (:name, :city_id, :price, :date)");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':city_id', $city_id, SQLITE3_INTEGER);
    $stmt->bindValue(':price', $price, SQLITE3_FLOAT);
    $stmt->bindValue(':date', $date, SQLITE3_TEXT);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Ошибка при добавлении экскурсии.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Добавить экскурсию</title>
</head>
<body>
<div class="header">
<h1>Добавить экскурсию</h1>
</div>
<div class="content">
<form method="POST">
    <label>Название экскурсии:</label>
    <input type="text" name="name" required>

    <label>Город (ID):</label>
    <input type="text" name="city_id" required>

    <label>Цена (₽):</label>
    <input type="number" step="0.01" name="price" required>

    <label>Дата:</label>
    <input type="date" name="date" required>

    <button type="submit">Добавить экскурсию</button>
</form>
</div>
</body>
</html>
