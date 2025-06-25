<?php
session_start();
include 'db.php';
include 'header.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$tour_id = $_GET['tour_id'] ?? null;

if (!$tour_id) {
    echo "Ошибка: экскурсия не найдена.";
    exit;
}

// Получаем данные экскурсии
$stmt = $db->prepare("SELECT name, price, date FROM tours WHERE id = :tour_id");
$stmt->bindValue(':tour_id', $tour_id, SQLITE3_INTEGER);
$tour = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$tour) {
    echo "Ошибка: экскурсия не найдена.";
    exit;
}

// Обрабатываем бронирование
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);  // Получаем email
    $phone = trim($_POST['phone']);

    // Вставляем данные бронирования в таблицу bookings
    $stmt = $db->prepare("INSERT INTO bookings (user_id, tour_id, full_name, email, phone, created_at) 
                          VALUES (:user_id, :tour_id, :full_name, :email, :phone, CURRENT_TIMESTAMP)");
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(':tour_id', $tour_id, SQLITE3_INTEGER);
    $stmt->bindValue(':full_name', $full_name, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
    
    if ($stmt->execute()) {
        header("Location: profile.php?booking_success=1");
        exit;
    } else {
        echo "Ошибка при бронировании.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование экскурсии</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="content">
    <h2>Бронирование экскурсии: <?= htmlspecialchars($tour['name']) ?></h2>
    <p><strong>Дата:</strong> <?= htmlspecialchars($tour['date']) ?></p>
    <p><strong>Цена за участника:</strong> <?= number_format($tour['price'], 2) ?> ₽</p>

    <!-- Форма для бронирования -->
    <form method="POST">
        <label>ФИО:</label>
        <input type="text" name="full_name" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Телефон:</label>
        <input type="text" name="phone" required>

        <button type="submit">Забронировать</button>
    </form>
</div>

</body>
</html>
