<?php
$db = new SQLite3('travel.db');

// Включение поддержки foreign key
$db->exec('PRAGMA foreign_keys = ON;');

$query = "SELECT name FROM sqlite_master WHERE type='table';";
$result = $db->query($query);

echo "<h2>Список таблиц:</h2>";

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $tableName = $row['name'];
    echo "<h3>Таблица: " . htmlspecialchars($tableName) . "</h3>";

    $dataQuery = "SELECT * FROM " . $tableName;
    $dataResult = $db->query($dataQuery);

    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>";
    $columnsQuery = "PRAGMA table_info(" . $tableName . ")";
    $columnsResult = $db->query($columnsQuery);
    $columns = [];
    
    while ($column = $columnsResult->fetchArray(SQLITE3_ASSOC)) {
        $columns[] = $column['name'];
        echo "<th>" . htmlspecialchars($column['name']) . "</th>";
    }
    echo "</tr>";

    // Вывод данных таблицы
    while ($dataRow = $dataResult->fetchArray(SQLITE3_ASSOC)) {
        echo "<tr>";
        foreach ($columns as $column) {
            echo "<td>" . htmlspecialchars($dataRow[$column]) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table><br>";
}

$db->close();
?>
