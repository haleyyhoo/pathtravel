<?php
session_start();
include 'db.php';
include 'header.php'; // Подключаем header.php

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: routes.php");
    exit;
}

$route_id = intval($_GET['id']);

$stmt = $db->prepare("SELECT r.name, r.waypoints, r.description, 
                             COALESCE(AVG(re.rating), 0) AS avg_rating
                      FROM routes r
                      LEFT JOIN reviews re ON r.id = re.route_id
                      WHERE r.id = :route_id");
$stmt->bindValue(':route_id', $route_id, SQLITE3_INTEGER);
$route = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$route) {
    echo "Маршрут не найден.";
    exit;
}

// Получаем отзывы
$reviews = [];
$stmt = $db->prepare("SELECT u.username, re.rating, re.comment, re.created_at
                      FROM reviews re
                      JOIN users u ON re.user_id = u.id
                      WHERE re.route_id = :route_id
                      ORDER BY re.created_at DESC");
$stmt->bindValue(':route_id', $route_id, SQLITE3_INTEGER);
$result = $stmt->execute();

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $reviews[] = $row;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($route['name']) ?></title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://api-maps.yandex.ru/2.1/?apikey=a9fa0c1b-11d6-4183-a15c-6e73d24d0216&lang=ru_RU"></script>
    <script>
        function initMap() {
            let map = new ymaps.Map("map", {
                center: [55.751574, 37.573856],
                zoom: 5
            });

            let waypoints = JSON.parse('<?= addslashes($route["waypoints"]) ?>');

            let multiRoute = new ymaps.multiRouter.MultiRoute({
                referencePoints: waypoints,
                params: { routingMode: "auto" }
            }, { boundsAutoApply: true });

            map.geoObjects.add(multiRoute);
        }

        ymaps.ready(initMap);
    </script>
</head>
<body>

    <div class="content">
        <h2><?= htmlspecialchars($route['name']) ?></h2>

        <p><?= htmlspecialchars($route['description']) ?></p>
        <p>⭐ Средний рейтинг: <?= round($route['avg_rating'], 1) ?></p>

        <div id="map" style="width: 100%; height: 500px;"></div>

        <h3>Отзывы</h3>
        <div class="reviews">
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <p><strong><?= htmlspecialchars($review['username']) ?></strong> (<?= $review['created_at'] ?>)</p>
                    <p>⭐ <?= $review['rating'] ?>/5</p>
                    <p><?= htmlspecialchars($review['comment']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <h3>Оставить отзыв</h3>
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="submit_review.php" method="POST">
                <input type="hidden" name="route_id" value="<?= $route_id ?>">
                <label>Оценка (1-5):</label>
                <input type="number" name="rating" min="1" max="5" required>
                <label>Комментарий:</label>
                <textarea name="comment" required></textarea>
                <button type="submit">Отправить</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">Войдите</a>, чтобы оставить отзыв.</p>
        <?php endif; ?>
    </div>

</body>
</html>
