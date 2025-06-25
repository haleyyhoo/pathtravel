<?php
session_start();
include 'db.php';

// Если пользователь не авторизован, перенаправляем на страницу входа
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем данные о пользователе
$stmt = $db->prepare("SELECT username FROM users WHERE id = :user_id");
$stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$user = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

// Получаем маршруты, созданные пользователем
$routes_stmt = $db->prepare("SELECT id, name FROM routes WHERE user_id = :user_id");
$routes_stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$routes_result = $routes_stmt->execute();

$routes = [];
while ($row = $routes_result->fetchArray(SQLITE3_ASSOC)) {
    $routes[] = $row;
}

// Получаем избранные маршруты пользователя
$favorites_stmt = $db->prepare("SELECT r.id, r.name
                                FROM routes r
                                JOIN favorites f ON r.id = f.route_id
                                WHERE f.user_id = :user_id");
$favorites_stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$favorites_result = $favorites_stmt->execute();

$favorites = [];
while ($row = $favorites_result->fetchArray(SQLITE3_ASSOC)) {
    $favorites[] = $row;
}

// Получаем уведомления для пользователя
$notifications_stmt = $db->prepare("SELECT id, route_id, message FROM notifications WHERE user_id = :user_id AND read = 0");
$notifications_stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$notifications_result = $notifications_stmt->execute();

$notifications = [];
while ($row = $notifications_result->fetchArray(SQLITE3_ASSOC)) {
    $notifications[] = $row;
}

// Помечаем уведомления как прочитанные
if (count($notifications) > 0) {
    $update_stmt = $db->prepare("UPDATE notifications SET read = 1 WHERE user_id = :user_id AND read = 0");
    $update_stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $update_stmt->execute();
}

// Получаем забронированные экскурсии пользователя
$bookings_stmt = $db->prepare("
    SELECT b.id, t.name, t.date, t.price, b.full_name, b.email, b.phone 
    FROM bookings b
    JOIN tours t ON b.tour_id = t.id
    WHERE b.user_id = :user_id
");
if ($bookings_stmt) {
    $bookings_stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $bookings_result = $bookings_stmt->execute();

    $bookings = [];
    while ($row = $bookings_result->fetchArray(SQLITE3_ASSOC)) {
        $bookings[] = $row;
    }
} else {
    $bookings = [];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="content">
        <h2>Привет, <?= htmlspecialchars($user['username']) ?>!</h2>

        <!-- Уведомления -->
        <h3>Уведомления</h3>
        <?php if (count($notifications) > 0): ?>
            <ul>
                <?php foreach ($notifications as $notification): ?>
                    <li>
                        <a href="route.php?id=<?= $notification['route_id'] ?>"><?= htmlspecialchars($notification['message']) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>У вас нет новых уведомлений.</p>
        <?php endif; ?>

	<h3>Мои бронирования экскурсий</h3>
	<?php if (count($bookings) > 0): ?>
	    <ul>
	        <?php foreach ($bookings as $booking): ?>
	            <li>
	                <strong><?= htmlspecialchars($booking['name']) ?></strong><br>
	                Дата: <?= htmlspecialchars($booking['date']) ?><br>
	                Цена: <?= htmlspecialchars($booking['price']) ?> руб.<br>
	                Контакт: <?= htmlspecialchars($booking['full_name']) ?>, <?= htmlspecialchars($booking['email']) ?>, <?= htmlspecialchars($booking['phone']) ?>
	            </li>
	        <?php endforeach; ?>
	    </ul>
	<?php else: ?>
	    <p>Вы еще не бронировали экскурсии.</p>
	<?php endif; ?>

        <!-- Мои маршруты -->
        <h3>Мои маршруты</h3>
        <?php if (count($routes) > 0): ?>
            <ul>
                <?php foreach ($routes as $route): ?>
                    <li><a href="route.php?id=<?= $route['id'] ?>"><?= htmlspecialchars($route['name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Вы еще не создали маршруты.</p>
        <?php endif; ?>

        <!-- Избранные маршруты -->
        <h3>Избранные маршруты</h3>
        <?php if (count($favorites) > 0): ?>
            <ul>
                <?php foreach ($favorites as $favorite): ?>
                    <li><a href="route.php?id=<?= $favorite['id'] ?>"><?= htmlspecialchars($favorite['name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>У вас нет избранных маршрутов.</p>
        <?php endif; ?>
    </div>

</body>
</html>
