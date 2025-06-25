<?php
session_start();
include 'db.php';
include 'header.php'; // Подключаем header.php

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // ID текущего пользователя

// Получаем доступные маршруты
$result = $db->query("SELECT r.id, r.name, r.description, 
                             COALESCE(AVG(re.rating), 0) AS avg_rating, COUNT(re.id) AS review_count
                      FROM routes r
                      LEFT JOIN reviews re ON r.id = re.route_id
                      GROUP BY r.id");

$routes = [];

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $routes[] = $row;
}

// Добавление маршрута в избранное
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['route_id'])) {
    $route_id = intval($_POST['route_id']);
    
    // Проверяем, есть ли уже этот маршрут в избранном
    $stmt = $db->prepare("SELECT id FROM favorites WHERE user_id = :user_id AND route_id = :route_id");
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(':route_id', $route_id, SQLITE3_INTEGER);
    $existing_fav = $stmt->execute()->fetchArray();

    if (!$existing_fav) {
        // Добавляем маршрут в избранное
        $stmt = $db->prepare("INSERT INTO favorites (user_id, route_id) VALUES (:user_id, :route_id)");
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $stmt->bindValue(':route_id', $route_id, SQLITE3_INTEGER);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Маршруты</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="content">
        <h2>Доступные маршруты</h2>

        <div class="create-route-button">
            <a href="create_route.php" class="btn">Создать маршрут</a>
        </div>

        <div class="routes-list">
            <?php foreach ($routes as $route): ?>
                <div class="route">
                    <h3><a href="route.php?id=<?= $route['id'] ?>">
                        <?= htmlspecialchars($route['name']) ?>
                    </a></h3>
                    <p><?= htmlspecialchars($route['description']) ?></p>
                    <p>⭐ <?= round($route['avg_rating'], 1) ?> (<?= $route['review_count'] ?> отзывов)</p>

                    <!-- Формируем форму для добавления в избранное -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="route_id" value="<?= $route['id'] ?>">
                        <button type="submit" class="favorite-button">
                            <span>&#10084;</span> <!-- Сердечко -->
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>
