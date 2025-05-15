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

    // Prepare SQL for both tables
    $sql = "
        SELECT nconst AS id FROM name_basics WHERE primaryName LIKE :query
        UNION
        SELECT tconst AS id FROM title_basics WHERE primaryTitle LIKE :query
    ";

    $stmt = $db->prepare($sql);
    $likeQuery = '%' . $query . '%';
    $stmt->bindValue(':query', $likeQuery, PDO::PARAM_STR);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode(['ids' => $results]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error.']);
    exit;
}
?>