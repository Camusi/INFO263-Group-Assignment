<?php
// This script generates a new page based on the provided ID and type. 
$id = isset($_GET['q']) ? trim($_GET['q']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
if ($id === '') {
    echo  'Missing a Query. Bad ID?';
    exit;
};

if ($type === 'title') {
    $pagePath = '../title/' . $id . '.php';
} else if ($type === 'person') {
    $pagePath = '../person/' . $id . '.php';
} else {
    echo  'Invalid Type. Bad Request?';
    exit;
}
?>
<?php
// Database connection
try {
    // Connect to SQLite database
    $db = new PDO('sqlite:imdb-2.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare SQL for both tables, include table name and primary field
    $sql = "
        SELECT tconst AS id, primaryTitle AS primary_name, 'title_basics_trim' AS table_name, startYear AS year
        FROM title_basics_trim
        WHERE tconst LIKE :query
        UNION ALL
        SELECT nconst AS id, primaryName AS primary_name, 'name_basics_trim' AS table_name, birthYear AS year
        FROM name_basics_trim
        WHERE nconst LIKE :query
    ";

    // Use $id as the query parameter
    $stmt = $db->prepare($sql);
    $likeQuery = '%' . $id . '%';
    $stmt->bindValue(':query', $likeQuery, PDO::PARAM_STR);
    $stmt->execute();

    $sqlOutput = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
}
?>
<?php
// Scrape current IMDB page for relevant data
$imdbType = ($type === 'person') ? 'name' : $type;
$url = "https://www.imdb.com/{$imdbType}/{$id}";
$data = file_get_contents($url);
if ($data === false) {
    echo json_encode(['error' => 'Failed to fetch IMDb page.']);
    exit;
}

// Find the cover image URL by using cover-image.php?q=ID json cover_image:

$startImg = strpos($data, 'https://m.media-amazon.com/images/M/');
if ($startImg === false) {
    echo json_encode(['error' => 'Cover image not found.']);
    exit;
}
$endImg= strpos($data, '"', $startImg);
if ($endImg === false) {
    echo json_encode(['error' => 'Cover image not found.']);
    exit;
}
$image_url = substr($data, $startImg, $endImg - $startImg);
$image_url = htmlspecialchars($image_url); // Escape HTML entities

// Find the plot summary
$plot = 'Plot synopsis not available.';
$plotStart = strpos($data, 'data-testid="sub-section-synopsis"');
if ($plotStart !== false) {
    $plotSection = substr($data, $plotStart, 1000); // Get a chunk after the marker
    $pTagStart = strpos($plotSection, '<p');
    $pTagEnd = strpos($plotSection, '</p>', $pTagStart);
    if ($pTagStart !== false && $pTagEnd !== false) {
        $plotRaw = substr($plotSection, $pTagStart, $pTagEnd - $pTagStart + 4);
        $plot = strip_tags($plotRaw); // Remove HTML tags
        $plot = htmlspecialchars($plot); // Escape HTML entities
        $plot = trim($plot); // Trim whitespace
    }
}
if (empty($plot)) {
    $plot = 'Plot synopsis not available.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creating <?php echo isset($_GET['type']) ? htmlspecialchars($_GET['type']) : '' ?> <?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ' '; ?></title>
    <link rel="stylesheet" href="../resources/style.css">
</head>
<body>
    <?php
    if (copy(__DIR__ . '/template_title.php', $pagePath)) {
        echo "Copying template to {$pagePath}<br>";
        $isGenerated = true;
    } else {
        echo "Failed to copy template to {$pagePath}<br>";
        $error = error_get_last();
        if ($error) {
            echo "Error details: " . htmlspecialchars($error['message']) . "<br>";
        }
    }
    $isWritten = false;
        // Read the copied file
        $content = file_get_contents($pagePath);
        if ($content !== false && !empty($sqlOutput)) {
            // Use the first result row
            $row = $sqlOutput[0];
            // Replace placeholders
            $content = str_replace('{NAME}', $row['primary_name'], $content);
            $content = str_replace('{YEAR}', $row['year'], $content);
            $content = str_replace('{POSTER}', $image_url, $content);
            /*content = str_replace('{DIRECTOR}', $director, $content);
            $content = str_replace('{WRITERS}', $writers, $content);
            $content = str_replace('{STARS}', $stars, $content);*/
            $content = str_replace('{PLOT}', $plot, $content);
            // Write back to the file
            if (file_put_contents($pagePath, $content) !== false) {
                $isWritten = true;
            }
        }
    if ($isGenerated && $isWritten) {
        echo "Page {$pagePath} created successfully.";
        header("Refresh: 0; URL={$pagePath}");
    } else {
        echo "<h1>An Error Occured:</h1><strong>Failed to create page {$pagePath}. Please contact a site administrator.</strong>";
        echo "<br><br>Redirecting to the homepage in 15 seconds.";
        header("Refresh: 15; URL=../");
    }

    ?>
</body>
</html>
