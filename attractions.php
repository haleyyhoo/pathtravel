<?php
session_start();
include 'db.php';
include 'header.php'; // Подключаем header.php

// Получаем список достопримечательностей
$result = $db->query("SELECT id, name, description FROM attractions");
$attractions = [];

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $attractions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Достопримечательности</title>
    <link rel="stylesheet" href="styles.css">
<div class="content">
    <h2>Достопримечательности</h2>

    <div class="attractions-list">
        <?php foreach ($attractions as $attraction): ?>
            <div class="attraction">
                <div class="attraction-content">
                    <?php if(!empty($attraction['category'])): ?>
                        <span class="attraction-category"><?= htmlspecialchars($attraction['category']) ?></span>
                    <?php endif; ?>
                    <h3><a href="attraction.php?id=<?= $attraction['id'] ?>">
                        <?= htmlspecialchars($attraction['name']) ?>
                    </a></h3>
                    <p><?= htmlspecialchars($attraction['description']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
