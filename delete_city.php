<?php
session_start();
include 'db.php';

// Проверка прав доступа
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Проверка наличия ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Неверный ID города.";
    exit;
}

$city_id = intval($_GET['id']);

// Удаление города
$stmt = $db->prepare("DELETE FROM cities WHERE id = :id");
$stmt->bindValue(':id', $city_id, SQLITE3_INTEGER);

if ($stmt->execute()) {
    header("Location: admin_dashboard.php");
    exit;
} else {
    echo "Ошибка при удалении города.";
}
?>
