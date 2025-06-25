<?php
session_start();
include 'db.php';
include 'header.php'; // Подключаем header.php

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: cities.php");
    exit;
}

$city_id = intval($_GET['id']);

// Получаем информацию о выбранном городе
$stmt = $db->prepare("SELECT name, description, image_path FROM cities WHERE id = :city_id");
$stmt->bindValue(':city_id', $city_id, SQLITE3_INTEGER);
$city = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$city) {
    echo "Город не найден.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($city['name']) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="content">
    <h2><?= htmlspecialchars($city['name']) ?></h2>
    
    <?php if (!empty($city['image_path'])): ?>
        <img src="<?= htmlspecialchars($city['image_path']) ?>" alt="<?= htmlspecialchars($city['name']) ?>" class="city-image">
    <?php endif; ?>

    <p><?= htmlspecialchars($city['description']) ?></p>
</div>

</body>
</html>
