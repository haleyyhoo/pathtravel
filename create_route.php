<?php
session_start();
include 'db.php';
include 'header.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $waypoints = $_POST['waypoints']; // JSON-строка с координатами
    $user_id = $_SESSION['user_id'];

    $stmt = $db->prepare("INSERT INTO routes (name, user_id, waypoints, description) VALUES (:name, :user_id, :waypoints, :description)");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(':waypoints', $waypoints, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->execute();

    header("Location: routes.php");
    exit;
}
?>

<div class="content">
    <h2>Создать маршрут</h2>

    <form action="create_route.php" method="post">
        <label for="name">Название маршрута:</label>
        <input type="text" id="name" name="name" required>

        <label for="description">Описание:</label>
        <textarea id="description" name="description" required></textarea>

        <input type="hidden" id="waypoints" name="waypoints">

        <div id="map" style="width: 100%; height: 400px;"></div>

        <button type="submit">Создать маршрут</button>
    </form>
</div>

<script src="https://api-maps.yandex.ru/2.1/?apikey=a9fa0c1b-11d6-4183-a15c-6e73d24d0216&lang=ru_RU"></script>
<script>
    let map, route, waypoints = [];

    function initMap() {
        map = new ymaps.Map("map", {
            center: [55.751574, 37.573856],
            zoom: 10
        });

        map.events.add('click', function (e) {
            let coords = e.get('coords');
            waypoints.push(coords);

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
            updateWaypoints();
        });
    }

    function updateWaypoints() {
        document.getElementById('waypoints').value = JSON.stringify(waypoints);
    }

    ymaps.ready(initMap);
</script>

</body>
</html>
