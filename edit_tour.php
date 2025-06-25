<?php
session_start();
include 'db.php';

// Проверка, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $tour_id = $_GET['id'];
    // Получаем данные экскурсии для редактирования
    $stmt = $db->prepare("SELECT * FROM tours WHERE id = :tour_id");
    $stmt->bindValue(':tour_id', $tour_id, SQLITE3_INTEGER);
    $tour = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if (!$tour) {
        echo "Экскурсия не найдена.";
        exit;
    }

    // Обработка изменения данных экскурсии
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $city_id = (int) $_POST['city_id'];
        $price = (float) $_POST['price'];
        $guide_name = trim($_POST['guide_name']);
        $date = trim($_POST['date']);

        // Обновляем данные экскурсии в базе данных
        $stmt = $db->prepare("UPDATE tours SET name = :name, description = :description, city_id = :city_id, 
                              price = :price, guide_name = :guide_name, date = :date WHERE id = :tour_id");
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':description', $description, SQLITE3_TEXT);
        $stmt->bindValue(':city_id', $city_id, SQLITE3_INTEGER);
        $stmt->bindValue(':price', $price, SQLITE3_FLOAT);
        $stmt->bindValue(':guide_name', $guide_name, SQLITE3_TEXT);
        $stmt->bindValue(':date', $date, SQLITE3_TEXT);
        $stmt->bindValue(':tour_id', $tour_id, SQLITE3_INTEGER);

        if ($stmt->execute()) {
            header("Location: admin_dashboard.php");
            exit;
        } else {
            echo "Ошибка при обновлении экскурсии.";
        }
    }
} else {
    echo "Ошибка: ID экскурсии не передан.";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать экскурсию</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="header">
    <h1>Редактирование экскурсии</h1>
</div>

<div class="content">
    <form method="POST">
        <label>Название экскурсии:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($tour['name']) ?>" required>

        <label>Описание экскурсии:</label>
        <textarea name="description" required><?= htmlspecialchars($tour['description']) ?></textarea>

        <label>Город:</label>
        <select name="city_id" required>
            <?php
            // Получаем все города для выбора
            $cities_stmt = $db->prepare("SELECT id, name FROM cities");
            $cities_result = $cities_stmt->execute();

            while ($city = $cities_result->fetchArray(SQLITE3_ASSOC)) {
                $selected = $tour['city_id'] == $city['id'] ? 'selected' : '';
                echo "<option value='" . $city['id'] . "' $selected>" . htmlspecialchars($city['name']) . "</option>";
            }
            ?>
        </select>

        <label>Цена:</label>
        <input type="number" name="price" value="<?= htmlspecialchars($tour['price']) ?>" step="0.01" required>

        <label>Имя гида:</label>
        <input type="text" name="guide_name" value="<?= htmlspecialchars($tour['guide_name']) ?>" required>

        <label>Дата экскурсии:</label>
        <input type="date" name="date" value="<?= htmlspecialchars($tour['date']) ?>" required>

        <button type="submit">Сохранить изменения</button>
    </form>
</div>

</body>
</html>
