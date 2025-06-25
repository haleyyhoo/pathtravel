<?php
session_start();
include 'db.php';
include 'header.php';

// Получаем список экскурсий
$tours_stmt = $db->query("SELECT t.id, t.name, t.description, t.price, t.guide_name, t.date, c.name AS city_name
                          FROM tours t
                          JOIN cities c ON t.city_id = c.id");

$tours = [];
while ($row = $tours_stmt->fetchArray(SQLITE3_ASSOC)) {
    $tours[] = $row;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Экскурсии</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="content">
    <h2>Доступные экскурсии</h2>

    <div class="tours-list">
        <?php foreach ($tours as $tour): ?>
            <div class="tour">
                <h3><?= htmlspecialchars($tour['name']) ?></h3>
                <p><strong>Город:</strong> <?= htmlspecialchars($tour['city_name']) ?></p>
                <p><?= htmlspecialchars($tour['description']) ?></p>
                <p><strong>Гид:</strong> <?= htmlspecialchars($tour['guide_name']) ?></p>
                <p><strong>Дата:</strong> <?= htmlspecialchars($tour['date']) ?></p>
                <p><strong>Цена:</strong> <?= number_format($tour['price'], 2) ?> ₽</p>
                <a href="book_tour.php?tour_id=<?= $tour['id'] ?>" class="book-button">Забронировать</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
