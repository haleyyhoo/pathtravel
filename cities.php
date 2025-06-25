<?php
session_start();
include 'db.php';
include 'header.php'; // Подключаем header.php

// Получаем список городов
$result = $db->query("SELECT id, name, description FROM cities");
$cities = [];

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $cities[] = $row;
}
?>

<div class="content">
    <h2>Города</h2>

    <div class="cities-list">
        <?php foreach ($cities as $city): ?>
            <div class="city">
                <div class="city-content">
                    <h3><a href="city.php?id=<?= $city['id'] ?>">
                        <?= htmlspecialchars($city['name']) ?>
                    </a></h3>
                    <p><?= htmlspecialchars($city['description']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
