<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Путь - сервис для путешествий</title>
    <link rel="stylesheet" href="styles.css"> <!-- Подключение CSS -->
</head>
<body>
    <div class="header">
        <h1>Путь - приключение начинается здесь</h1>
    </div>

<div class="nav">
    <div class="left-nav">
        <a href="index.php">Главная</a>
        <a href="routes.php">Маршруты</a>
        <a href="cities.php">Города</a>
        <a href="attractions.php">Достопримечательности</a>
        <a href="tours.php">Экскурсии</a>
    </div>

    <?php if (isset($_SESSION['username'])): ?>
        <div class="user-links">
            <a href="profile.php">Личный кабинет</a>
            <a href="logout.php" class="logout">Выйти</a>
        </div>
    <?php else: ?>
        <a href="login.php" class="logout">Войти</a>
    <?php endif; ?>
</div>
