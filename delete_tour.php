<?php
session_start();
include 'db.php';

// Проверка на роль пользователя
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $tour_id = $_GET['id'];

    // Удаляем экскурсию
    $stmt = $db->prepare("DELETE FROM tours WHERE id = :tour_id");
    $stmt->bindValue(':tour_id', $tour_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Ошибка при удалении экскурсии.";
    }
} else {
    echo "Ошибка: ID экскурсии не передан.";
}
?>
