<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Неверный ID достопримечательности.";
    exit;
}

$attraction_id = intval($_GET['id']);

// Получаем данные
$stmt = $db->prepare("SELECT * FROM attractions WHERE id = :id");
$stmt->bindValue(':id', $attraction_id, SQLITE3_INTEGER);
$attraction = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$attraction) {
    echo "Достопримечательность не найдена.";
    exit;
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $image_path = trim($_POST['image_path']);

    $stmt = $db->prepare("UPDATE attractions SET name = :name, description = :description, image_path = :image_path WHERE id = :id");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':image_path', $image_path, SQLITE3_TEXT);
    $stmt->bindValue(':id', $attraction_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Ошибка при обновлении достопримечательности.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать достопримечательность</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="header">
    <h1>Редактирование достопримечательности</h1>
</div>

<div class="content">
    <form method="POST">
        <label>Название:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($attraction['name']) ?>" required>

        <label>Описание:</label>
        <textarea name="description" required><?= htmlspecialchars($attraction['description']) ?></textarea>

        <label>Путь к изображению:</label>
        <input type="text" name="image_path" value="<?= htmlspecialchars($attraction['image_path']) ?>">

        <button type="submit">Сохранить изменения</button>
    </form>
</div>
</body>
</html>
