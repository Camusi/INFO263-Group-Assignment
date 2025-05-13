<?php
// Database file path
$dbPath = realpath(__DIR__ . '/../imdb2/imdb-2.sqlite3');

if (!file_exists($dbPath)) {
    die("Database file not found.");
}

try {
    // Connect to the SQLite database
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if search query is provided
    if (isset($_GET['query']) && !empty($_GET['query'])) {
        $query = htmlspecialchars($_GET['query']);
        $query = "%$query%";

        // Prepare SQL statements for both tables
        $stmtNameBasics = $pdo->prepare("SELECT * FROM name_basics_trim WHERE primaryName LIKE :query");
        $stmtTitleBasics = $pdo->prepare("SELECT * FROM title_basics_trim WHERE primaryTitle LIKE :query");

        // Bind the query parameter
        $stmtNameBasics->bindParam(':query', $query, PDO::PARAM_STR);
        $stmtTitleBasics->bindParam(':query', $query, PDO::PARAM_STR);

        // Execute the queries
        $stmtNameBasics->execute();
        $stmtTitleBasics->execute();

        // Fetch results
        $nameResults = $stmtNameBasics->fetchAll(PDO::FETCH_ASSOC);
        $titleResults = $stmtTitleBasics->fetchAll(PDO::FETCH_ASSOC);

        // Display results

        if (!empty($nameResults)) {
            foreach ($nameResults as $row) {
                echo "<li>" . htmlspecialchars($row['primaryName']) . " (nconst: " . htmlspecialchars($row['nconst']) . ")</li>";
            }
            echo "</ul>";
        };

        if (!empty($titleResults)) {
            foreach ($titleResults as $row) {
                echo "<li>" . htmlspecialchars($row['primaryTitle']) . " (tconst: " . htmlspecialchars($row['tconst']) . ")</li>";
            }
            echo "</ul>";
        };
} 
    die("Database error: " . $e->getMessage());
}
?>