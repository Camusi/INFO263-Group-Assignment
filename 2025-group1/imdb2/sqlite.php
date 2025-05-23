<?php
// SQLite database connection
try {
    $db = new PDO('sqlite:resources/imdb-2.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM name_basics_trim");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Row count: " . $row['count'];
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}

?>