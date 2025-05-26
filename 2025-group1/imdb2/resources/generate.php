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
global $warningsArr;
$warningsArr = array();
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
        WHERE tconst IS :query
        UNION ALL
        SELECT nconst AS id, primaryName AS primary_name, 'name_basics_trim' AS table_name, birthYear AS year
        FROM name_basics_trim
        WHERE nconst IS :query
    ";

    // Use $id as the query parameter
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':query', $id, PDO::PARAM_STR);
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

// Find the blurb
if (preg_match('/<span[^>]*data-testid="plot-xl"[^>]*>(.*?)<\/span>/is', $data, $blurbMatch)) {
    $blurb = trim(strip_tags($blurbMatch[1]));
} else {
    $blurb = '';
}


// Find the plot summary
$plot = 'Plot synopsis not available. (ERROR 1)';
$purl = "$url/plotsummary";
$pdata = file_get_contents($purl);
if ($pdata !== false) {
    // Try to extract the "Synopsis" section
    $synopsis = '';
    // IMDb's synopsis is usually inside <ul id="plot-synopsis-content"> or <li class="ipc-html-content-inner">
    if (preg_match('/<ul[^>]*id="plot-synopsis-content"[^>]*>(.*?)<\/ul>/is', $pdata, $ulMatch)) {
        // Extract the first <li> inside the ul
        if (preg_match('/<li[^>]*>(.*?)<\/li>/is', $ulMatch[1], $liMatch)) {
            $synopsis = strip_tags($liMatch[1]);
        }
    }
    // Fallback: try to find <li class="ipc-html-content-inner"> (new IMDb markup)
    if (empty($synopsis) && preg_match('/<li[^>]*class="ipc-html-content-inner"[^>]*>(.*?)<\/li>/is', $pdata, $liMatch)) {
        $synopsis = strip_tags($liMatch[1]);
    }
    // Fallback: try to find <li class="ipl-zebra-list__item"> (older IMDb markup)
    if (empty($synopsis) && preg_match('/<li[^>]*class="ipl-zebra-list__item"[^>]*>(.*?)<\/li>/is', $pdata, $liMatch)) {
        $synopsis = strip_tags($liMatch[1]);
    }
    if (!empty($synopsis)) {
        $plot = trim($synopsis);
    }
}

// Fallback: Try to extract plot summary from the main IMDb page if still not found
if ($plot === 'Plot synopsis not available. (ERROR 1)') {
    if (preg_match('/<span[^>]*data-testid="plot-xl"[^>]*>(.*?)<\/span>/is', $data, $plotMatch)) {
        $plot = trim(strip_tags($plotMatch[1]));
        // Add stub message if fallback is used
        if (!empty($sqlOutput) && isset($sqlOutput[0]['primary_name'], $sqlOutput[0]['year'])) {
            $warningsArr[] = 'This article is a stub. Help improve this page by adding more details!';
        }
        }
    } elseif (preg_match('/<span[^>]*class="sc-16ede01-2[^"]*"[^>]*>(.*?)<\/span>/is', $data, $plotMatch)) {
        $plot = trim(strip_tags($plotMatch[1]));
        // Add stub message if fallback is used
        if (!empty($sqlOutput)) {
            $warningsArr[] = 'This article is a stub. Help improve this page by adding more details!';
        }
    }

// Notable People Start
$notable_people = 'Either all the people who worked on this title are categorized, or we\'re missing someone. Feel free to correct this by editing the page.';
$notable_peopleArr = [];

// Find Writers
$writers = 'Sorry, we don\'t know who wrote this film yet. Why not add it?';
if ($type === 'title') {
    $writersArr = [];
    $wstmt = $db->prepare("SELECT writer FROM title_writer_trim WHERE tconst = :tconst");
    $wstmt->execute([':tconst' => $id]);
    while ($row = $wstmt->fetch(PDO::FETCH_ASSOC)) {
        $nameStmt = $db->prepare("SELECT primaryName FROM name_basics_trim WHERE nconst = :nconst");
        $nameStmt->execute([':nconst' => $row['writer']]);
        $nameRow = $nameStmt->fetch(PDO::FETCH_ASSOC);
        if ($nameRow) {
            $writersArr[] = htmlspecialchars($nameRow['primaryName']);
        }
    }
    $writers = !empty($writersArr) ? implode(', ', $writersArr) : 'N/A';
} else {
    $writers = 'N/A';
}


// Find Director(s)
$director = 'We don\'t yet have a director for this film. Why not add it?';
if ($type === 'title') {
    $directorArr = [];
    $dstmt = $db->prepare("SELECT director FROM title_director_trim WHERE tconst = :tconst");
    $dstmt->execute([':tconst' => $id]);
    while ($row = $dstmt->fetch(PDO::FETCH_ASSOC)) {
        $dnameStmt = $db->prepare("SELECT primaryName FROM name_basics_trim WHERE nconst = :nconst");
        $dnameStmt->execute([':nconst' => $row['director']]);
        $dnameRow = $dnameStmt->fetch(PDO::FETCH_ASSOC);
        if ($dnameRow) {
            $directorArr[] = htmlspecialchars($dnameRow['primaryName']);
        }
    }
    if (count($directorArr) > 1) {
        // Add all directors to notable people
        $notable_peopleArr = array_merge($notable_peopleArr, $directorArr);
        // Leave $director as the error message
        $director = 'We don\'t yet have a director for this film. Why not add it?';
    } elseif (count($directorArr) === 1) {
        $director = $directorArr[0];
    } else {
        $director = 'N/A';
    }
} else {
    $director = 'N/A';
}
/*
// Find Stars
$stars = '';
if ($type === 'title') {
    $starsArr = [];
    $sstmt = $db->prepare("SELECT star FROM title_principals_trim WHERE tconst = :tconst");
    $sstmt->execute([':tconst' => $id]);
    while ($row = $sstmt->fetch(PDO::FETCH_ASSOC)) {
        $nameStmt = $db->prepare("SELECT primaryName FROM name_basics_trim WHERE nconst = :nconst");
        $nameStmt->execute([':nconst' => $row['principal']]);
        $nameRow = $nameStmt->fetch(PDO::FETCH_ASSOC);
        if ($nameRow) {
            $starsArr[] = htmlspecialchars($nameRow['primaryName']);
        }
    }
    $stars = !empty($starsArr) ? implode(', ', $starsArr) : 'N/A';
} else {
    $stars = 'N/A';
}*/

// Warnings Array

$warningsArr[] = 'This page is automatically generated based on data from an IMDB database export. Information may be out of date or not accurately reflect reality.';
$warnings = !empty($warningsArr) ? implode(' <br>', $warningsArr) : '';

// Notable People Finish
$notable_people = !empty($notable_peopleArr) ? implode(', ', $notable_peopleArr) : $notable_people;

// Votes Logic
$votes = 0;

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
    if ($type == 'title') {
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
                $content = str_replace('{WRITERS}', $writers, $content);
                $content = str_replace('{DIRECTOR}', $director, $content);
                $content = str_replace('{STARS}', $stars, $content);
                $content = str_replace('{BLURB}', $blurb, $content);
                $content = str_replace('{PLOT}', $plot, $content);
                $content = str_replace('{NOTABLE}', $notable_people, $content);
                $content = str_replace('{ID}', $row['id'], $content);
                $content = str_replace('{WARNINGS}', $warnings, $content);
                $content = str_replace('{VOTES}', $votes, $content);
                // Write back to the file
                if (file_put_contents($pagePath, $content) !== false) {
                    $isWritten = true;
                }
            }
    } elseif ($type == 'person'){
        if (copy(__DIR__ . '/template_person.php', $pagePath)) {
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
                $content = str_replace('{ROLES}', $roles, $content);
                $content = str_replace('{BIO}', $bio, $content);
                $content = str_replace('{ID}', $row['id'], $content);
                $content = str_replace('{WARNINGS}', $warnings, $content);
                $content = str_replace('{VOTES}', $votes, $content);
                // Write back to the file
                if (file_put_contents($pagePath, $content) !== false) {
                    $isWritten = true;
                }
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