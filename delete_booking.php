<?php
session_start();
include 'db.php';

// Проверка на роль пользователя
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];

    // Удаляем бронирование
    $stmt = $db->prepare("DELETE FROM bookings WHERE id = :booking_id");
    $stmt->bindValue(':booking_id', $booking_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Ошибка при удалении бронирования.";
    }
} else {
    echo "Ошибка: ID бронирования не передан.";
}
?>
