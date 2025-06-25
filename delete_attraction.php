<?php
session_start();
include 'db.php';

// Проверка прав администратора
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Проверка ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Неверный ID достопримечательности.";
    exit;
}

$attraction_id = intval($_GET['id']);

// Удаление достопримечательности
$stmt = $db->prepare("DELETE FROM attractions WHERE id = :id");
$stmt->bindValue(':id', $attraction_id, SQLITE3_INTEGER);

if ($stmt->execute()) {
    header("Location: admin_dashboard.php");
    exit;
} else {
    echo "Ошибка при удалении достопримечательности.";
}
?>
