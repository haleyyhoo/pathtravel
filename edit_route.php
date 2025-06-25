<?php
session_start();
include 'db.php';

// Проверка, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $route_id = $_GET['id'];

    // Получаем данные маршрута для редактирования
    $stmt = $db->prepare("SELECT * FROM routes WHERE id = :route_id");
    $stmt->bindValue(':route_id', $route_id, SQLITE3_INTEGER);
    $route = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if (!$route) {
        echo "Маршрут не найден.";
        exit;
    }

    // Обработка изменения данных маршрута
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $waypoints = trim($_POST['waypoints']); // Обновленные координаты в JSON

        // Обновляем маршрут в базе данных
        $stmt = $db->prepare("UPDATE routes SET name = :name, description = :description, waypoints = :waypoints WHERE id = :route_id");
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':description', $description, SQLITE3_TEXT);
        $stmt->bindValue(':waypoints', $waypoints, SQLITE3_TEXT);
        $stmt->bindValue(':route_id', $route_id, SQLITE3_INTEGER);

        if ($stmt->execute()) {
            header("Location: admin_dashboard.php");
            exit;
        } else {
            echo "Ошибка при обновлении маршрута.";
        }
    }
} else {
    echo "Ошибка: ID маршрута не передан.";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать маршрут</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://api-maps.yandex.ru/2.1/?apikey=a9fa0c1b-11d6-4183-a15c-6e73d24d0216&lang=ru_RU"></script>
</head>
<body>

<div class="header">
    <h1>Редактирование маршрута</h1>
</div>

<div class="content">
    <form method="POST">
        <label>Название маршрута:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($route['name']) ?>" required>

        <label>Описание маршрута:</label>
        <textarea name="description" required><?= htmlspecialchars($route['description']) ?></textarea>

        <input type="hidden" id="waypoints" name="waypoints" value="<?= htmlspecialchars($route['waypoints']) ?>">

        <div id="map" style="width: 100%; height: 400px;"></div>

        <button type="submit">Сохранить изменения</button>
    </form>
</div>

<script>
    let map, route, waypoints = [];

    function initMap() {
        map = new ymaps.Map("map", {
            center: [55.751574, 37.573856],
            zoom: 10
        });

        // Загружаем существующие точки маршрута
        let savedWaypoints = '<?= addslashes($route['waypoints']) ?>';
        try {
            waypoints = JSON.parse(savedWaypoints);
        } catch (e) {
            waypoints = [];
        }

        if (waypoints.length > 0) {
            addRoute();
        }

        map.events.add('click', function (e) {
            let coords = e.get('coords');
            waypoints.push(coords);
            addRoute();
            updateWaypoints();
        });
    }

    function addRoute() {
        if (route) {
            map.geoObjects.remove(route);
        }

        route = new ymaps.multiRouter.MultiRoute({
            referencePoints: waypoints,
            params: { routingMode: "auto" }
        }, {
            boundsAutoApply: true
        });

        map.geoObjects.add(route);
    }

    function updateWaypoints() {
        document.getElementById('waypoints').value = JSON.stringify(waypoints);
    }

    ymaps.ready(initMap);
</script>

</body>
</html>
