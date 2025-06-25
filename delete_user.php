<?php
session_start();
include 'db.php';

// Проверка на роль пользователя
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Удаляем пользователя
    $stmt = $db->prepare("DELETE FROM users WHERE id = :user_id");
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Ошибка при удалении пользователя.";
    }
} else {
    echo "Ошибка: ID пользователя не передан.";
}
?>
