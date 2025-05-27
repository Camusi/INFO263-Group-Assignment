<?php 
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

$url = "https://www.imdb.com/title/$query";
$data = file_get_contents($url);

if ($query === '') {
    echo json_encode(['error' => 'No search query provided.']);
    exit;
}

try {
    $db = new PDO('sqlite:../resources/imdb-2.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

try {
    $stmt = $db->prepare("SELECT image_url FROM title_basics_trim WHERE tconst = :tconst");
    $stmt->bindParam(':tconst', $query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && !empty($result['image_url'])) {
        echo json_encode(['cover_image' => $result['image_url']]);
        exit;
    } else {
        // Use the fallback method to fetch the cover image from IMDb and then store the URL in the database
        if ($data === false) {
            echo json_encode(['error' => 'Failed to fetch IMDb page.']);
            exit;
        }
        $start = strpos($data, 'https://m.media-amazon.com/images/M/');
        if ($start === false) {
            echo json_encode(['error' => 'Cover image not found.']);
            exit;
        }
        $end = strpos($data, '"', $start);
        if ($end === false) {
            echo json_encode(['error' => 'Cover image not found.']);
            exit;
        }
        $image_url = substr($data, $start, $end - $start);

        // Try to update the database with the new image URL
        if (isset($db)) {
            try {
                $insert_stmt = $db->prepare("UPDATE title_basics_trim SET image_url = :image_url WHERE tconst = :tconst");
                $insert_stmt->bindParam(':image_url', $image_url);
                $insert_stmt->bindParam(':tconst', $query);
                $insert_stmt->execute();
            } catch (PDOException $e) {
                // Ignore DB update errors for fallback
            }
        }

        echo json_encode(['cover_image' => $image_url]);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
    exit;
}

?>