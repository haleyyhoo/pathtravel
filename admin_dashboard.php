<?php
session_start();
include 'db.php';

// Проверяем, если пользователь не админ, перенаправляем его
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

function getAllCities($db) {
    $stmt = $db->prepare("SELECT * FROM cities");
    $result = $stmt->execute();
    $cities = [];
    while ($city = $result->fetchArray(SQLITE3_ASSOC)) {
        $cities[] = $city;
    }
    return $cities;
}

function getAllAttractions($db) {
    $stmt = $db->prepare("SELECT * FROM attractions");
    $result = $stmt->execute();
    $attractions = [];
    while ($a = $result->fetchArray(SQLITE3_ASSOC)) {
        $attractions[] = $a;
    }
    return $attractions;
}

// Функция для получения всех пользователей
function getAllUsers($db) {
    $stmt = $db->prepare("SELECT * FROM users");
    $result = $stmt->execute();
    $users = [];
    while ($user = $result->fetchArray(SQLITE3_ASSOC)) {
        $users[] = $user;
    }
    return $users;
}

// Функция для получения всех маршрутов
function getAllRoutes($db) {
    $stmt = $db->prepare("SELECT * FROM routes");
    $result = $stmt->execute();
    $routes = [];
    while ($route = $result->fetchArray(SQLITE3_ASSOC)) {
        $routes[] = $route;
    }
    return $routes;
}

// Функция для получения всех экскурсии
function getAllTours($db) {
    $stmt = $db->prepare("SELECT * FROM tours");
    $result = $stmt->execute();
    $tours = [];
    while ($tour = $result->fetchArray(SQLITE3_ASSOC)) {
        $tours[] = $tour;
    }
    return $tours;
}

// Функция для получения всех бронирований
function getAllBookings($db) {
    $stmt = $db->prepare("SELECT b.*, t.name AS tour_name, u.username AS user_name 
                          FROM bookings b
                          JOIN tours t ON b.tour_id = t.id
                          JOIN users u ON b.user_id = u.id");
    $result = $stmt->execute();
    $bookings = [];
    while ($booking = $result->fetchArray(SQLITE3_ASSOC)) {
        $bookings[] = $booking;
    }
    return $bookings;
}

// Получаем данные
$users = getAllUsers($db);
$routes = getAllRoutes($db);
$tours = getAllTours($db);
$bookings = getAllBookings($db);
$cities = getAllCities($db);
$attractions = getAllAttractions($db);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ - Панель управления</title>
    <link rel="stylesheet" href="styles.css"> <!-- Подключаем стили -->
</head>
<body>

<div class="header">
    <h1>Панель управления</h1>
    <p>Добро пожаловать, администратор!</p>
    <a href="logout.php" class="btn">Выйти</a>
</div>

<div class="content">
<h2>Управление пользователями</h2>
<a href="add_user.php" class="btn">Добавить пользователя</a>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Имя пользователя</th>
            <th>Email</th>
            <th>Роль</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $user['id'] ?>">Редактировать</a> |
                    <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Вы уверены?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Управление маршрутами</h2>
<a href="add_route.php" class="btn">Добавить маршрут</a>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Описание</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($routes as $route): ?>
            <tr>
                <td><?= $route['id'] ?></td>
                <td><?= htmlspecialchars($route['name']) ?></td>
                <td><?= htmlspecialchars($route['description']) ?></td>
                <td>
                    <a href="edit_route.php?id=<?= $route['id'] ?>">Редактировать</a> |
                    <a href="delete_route.php?id=<?= $route['id'] ?>" onclick="return confirm('Вы уверены?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Управление городами</h2>
<a href="add_city.php" class="btn">Добавить город</a>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Описание</th>
            <th>Изображение</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cities as $city): ?>
            <tr>
                <td><?= $city['id'] ?></td>
                <td><?= htmlspecialchars($city['name']) ?></td>
                <td><?= htmlspecialchars($city['description']) ?></td>
                <td>
                    <?php if (!empty($city['image_path'])): ?>
                        <img src="<?= htmlspecialchars($city['image_path']) ?>" alt="" width="100">
                    <?php else: ?>
                        Нет изображения
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit_city.php?id=<?= $city['id'] ?>">Редактировать</a> |
                    <a href="delete_city.php?id=<?= $city['id'] ?>" onclick="return confirm('Удалить город?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Управление достопримечательностями</h2>
<a href="add_attraction.php" class="btn">Добавить достопримечательность</a>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Описание</th>
            <th>Изображение</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($attractions as $attraction): ?>
            <tr>
                <td><?= $attraction['id'] ?></td>
                <td><?= htmlspecialchars($attraction['name']) ?></td>
                <td><?= htmlspecialchars($attraction['description']) ?></td>
                <td>
                    <?php if (!empty($attraction['image_path'])): ?>
                        <img src="<?= htmlspecialchars($attraction['image_path']) ?>" alt="" width="100">
                    <?php else: ?>
                        Нет изображения
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit_attraction.php?id=<?= $attraction['id'] ?>">Редактировать</a> |
                    <a href="delete_attraction.php?id=<?= $attraction['id'] ?>" onclick="return confirm('Удалить достопримечательность?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<h2>Управление экскурсиями</h2>
<a href="add_tour.php" class="btn">Добавить экскурсию</a>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Город</th>
            <th>Цена</th>
            <th>Дата</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tours as $tour): ?>
            <tr>
                <td><?= $tour['id'] ?></td>
                <td><?= htmlspecialchars($tour['name']) ?></td>
                <td><?= htmlspecialchars($tour['city_id']) ?></td> 
                <td><?= number_format($tour['price'], 2) ?> ₽</td>
                <td><?= htmlspecialchars($tour['date']) ?></td>
                <td>
                    <a href="edit_tour.php?id=<?= $tour['id'] ?>">Редактировать</a> |
                    <a href="delete_tour.php?id=<?= $tour['id'] ?>" onclick="return confirm('Вы уверены?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    <h2>Управление бронированиями</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Пользователь</th>
                <th>Экскурсия</th>
                <th>ФИО</th>
                <th>Телефон</th>
                <th>Дата бронирования</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= $booking['id'] ?></td>
                    <td><?= htmlspecialchars($booking['user_name']) ?></td>
                    <td><?= htmlspecialchars($booking['tour_name']) ?></td>
                    <td><?= htmlspecialchars($booking['full_name']) ?></td>
                    <td><?= htmlspecialchars($booking['phone']) ?></td>
                    <td><?= $booking['created_at'] ?></td>
                    <td>
                        <a href="delete_booking.php?id=<?= $booking['id'] ?>" onclick="return confirm('Вы уверены?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
