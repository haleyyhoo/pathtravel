<?php
// Подключение к SQLite
$db = new SQLite3('travel.db');

// Включение поддержки foreign key
$db->exec('PRAGMA foreign_keys = ON;');
?>
