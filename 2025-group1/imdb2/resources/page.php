<?php

echo '
<img src="../resources/img/load.gif" alt="Loading..." style="display: block; margin: 0 auto; width: 100px; height: 100px;">
<p style="text-align: center;">Redirecting to the page...</p>
';
// PAGE CHECK AND GENERATE
// Step 1: Check if the page already exists
$id = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($id === '') {
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
        WHERE tconst IS :query
        UNION ALL
        SELECT nconst AS id, primaryName AS primary_name, 'name_basics_trim' AS table_name
        FROM name_basics_trim
        WHERE nconst IS :query
    ";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':query', $id, PDO::PARAM_STR);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $result) {
        if ($result['table_name'] === 'title_basics_trim') {
            $pageType = 'title';
            $pagePath = '../title/' . $result['id'] . '.php';
            if (file_exists($pagePath)) {
                header("Location: $pagePath");
                exit;
            }
        } else if ($result['table_name'] === 'name_basics_trim') {
            $pageType = 'person';
            $pagePath = '../person/' . $result['id'] . '.php';
            if (file_exists($pagePath)) {
                header("Location: $pagePath");
                exit;
            }
        } else {
            echo json_encode(['error' => 'Invalid table name. Bad ID?']);
            exit;
    }
} 
        
        }catch (Exception $e) {
            echo json_encode(['error' => 'Database error: ' . $e->getMessage() . ' Bad ID?']);
            exit;
        }

// Step 2: If it doesn't exist, generate the page
    // Redirect to generate.php with query and type
    header("Location: generate.php?q=" . $result['id'] . "&type=" . $pageType);
    exit;
?>