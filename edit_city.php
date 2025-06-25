<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Неверный ID города.";
    exit;
}

$city_id = intval($_GET['id']);

// Получаем данные
$stmt = $db->prepare("SELECT * FROM cities WHERE id = :id");
$stmt->bindValue(':id', $city_id, SQLITE3_INTEGER);
$city = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$city) {
    echo "Город не найден.";
    exit;
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $image_path = trim($_POST['image_path']);

    $stmt = $db->prepare("UPDATE cities SET name = :name, description = :description, image_path = :image_path WHERE id = :id");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':image_path', $image_path, SQLITE3_TEXT);
    $stmt->bindValue(':id', $city_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Ошибка при обновлении города.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать город</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="header">
    <h1>Редактирование города</h1>
</div>

<div class="content">
    <form method="POST">
        <label>Название:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($city['name']) ?>" required>

        <label>Описание:</label>
        <textarea name="description" required><?= htmlspecialchars($city['description']) ?></textarea>

        <label>Путь к изображению:</label>
        <input type="text" name="image_path" value="<?= htmlspecialchars($city['image_path']) ?>">

        <button type="submit">Сохранить изменения</button>
    </form>
</div>
</body>
</html>
