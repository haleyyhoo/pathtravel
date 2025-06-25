<?php
session_start();
include 'db.php';
include 'header.php'; // Подключаем header.php

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: attractions.php");
    exit;
}

$attraction_id = intval($_GET['id']);

// Получаем информацию о достопримечательности
$stmt = $db->prepare("SELECT name, description, image_path FROM attractions WHERE id = :attraction_id");
$stmt->bindValue(':attraction_id', $attraction_id, SQLITE3_INTEGER);
$attraction = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$attraction) {
    echo "Достопримечательность не найдена.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($attraction['name']) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="content">
    <h2><?= htmlspecialchars($attraction['name']) ?></h2>

    <?php if (!empty($attraction['image_path'])): ?>
        <img src="<?= htmlspecialchars($attraction['image_path']) ?>" alt="<?= htmlspecialchars($attraction['name']) ?>" class="city-image">
    <?php endif; ?>

    <p><?= htmlspecialchars($attraction['description']) ?></p>
</div>

</body>
</html>
