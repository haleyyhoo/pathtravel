<?php
session_start();
include 'db.php';

// Проверка, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $waypoints = trim($_POST['waypoints']);

    $stmt = $db->prepare("INSERT INTO routes (name, description, waypoints) VALUES (:name, :description, :waypoints)");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':waypoints', $waypoints, SQLITE3_TEXT);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Ошибка при добавлении маршрута.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить маршрут</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://api-maps.yandex.ru/2.1/?apikey=ВАШ_API_КЛЮЧ&lang=ru_RU"></script>
</head>
<body>
<div class="header">
<h1>Добавить маршрут</h1>
</div>
<div class="content">
<form method="POST">
    <label>Название маршрута:</label>
    <input type="text" name="name" required>

    <label>Описание маршрута:</label>
    <textarea name="description" required></textarea>

    <input type="hidden" name="waypoints" id="waypoints">

    <div id="map" style="width: 100%; height: 400px;"></div>

    <button type="submit">Добавить маршрут</button>
</form>
</div>
<script>
    let map, route, waypoints = [];

    function initMap() {
        map = new ymaps.Map("map", { center: [55.751574, 37.573856], zoom: 10 });

        map.events.add('click', function (e) {
            let coords = e.get('coords');
            waypoints.push(coords);

            if (route) map.geoObjects.remove(route);

            route = new ymaps.multiRouter.MultiRoute({ referencePoints: waypoints, params: { routingMode: "auto" } }, { boundsAutoApply: true });
            map.geoObjects.add(route);
            document.getElementById('waypoints').value = JSON.stringify(waypoints);
        });
    }

    ymaps.ready(initMap);
</script>

</body>
</html>
