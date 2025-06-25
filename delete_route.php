<?php
session_start();
include 'db.php';

// Проверка на роль пользователя
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $route_id = $_GET['id'];

    // Удаляем маршрут
    $stmt = $db->prepare("DELETE FROM routes WHERE id = :route_id");
    $stmt->bindValue(':route_id', $route_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Ошибка при удалении маршрута.";
    }
} else {
    echo "Ошибка: ID маршрута не передан.";
}
?>
