<?php
// Set header for JSON response
header('Content-Type: application/json');

// Get the search query from GET parameter 'q'
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($query === '') {
    echo json_encode(['error' => 'No search query provided.']);
    exit;
}

try {
    // Connect to SQLite database
    $db = new PDO('sqlite:./imdb-2.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare SQL for both tables, include table name and primary field
    $sql = "
        SELECT tconst AS id, primaryTitle AS primary_name, 'title_basics_trim' AS table_name
        FROM title_basics_trim
        WHERE primaryTitle LIKE :query
        UNION ALL
        SELECT nconst AS id, primaryName AS primary_name, 'name_basics_trim' AS table_name
        FROM name_basics_trim
        WHERE primaryName LIKE :query
    ";

    $stmt = $db->prepare($sql);
    $likeQuery = '%' . $query . '%';
    $stmt->bindValue(':query', $likeQuery, PDO::PARAM_STR);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output as JSON: id, primary, table
    echo json_encode(['results' => $results]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
}
?>