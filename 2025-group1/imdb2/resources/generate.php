<?php
session_start();
$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : '';
$id = isset($_GET['q']) ? trim($_GET['q']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$pageID = $id; // Define pageID here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finding {id}</title>
</head>
<body>
    <p>Welcome back <?php echo htmlspecialchars($userID); ?>!</p>
</body>
</html>

<?php
if ($id === '') {
    echo 'Missing a Query. Bad ID?';
    exit;
}
?>

<div class="like-dislike" data-userid="<?php echo htmlspecialchars($userID); ?>" data-pageid="<?php echo htmlspecialchars($pageID); ?>">


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
<p>You are actually the first person to search for <?php echo htmlspecialchars($id); ?>, so we are just loading the content for the first time. Sorry for the delay!</p>
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
    echo "<h1>An Error Occured:</h1><strong>Failed to create page {$pagePath}. Please contact a site administrator.</strong>";
    echo "<br><br>Redirecting to the homepage in 15 seconds.";
    header("Refresh: 15; URL=../index.php?error=Failed%20to%20create%20page%20" . urlencode($pageID) . ".");
    exit;
}
?>
<?php

/* 
  _______ _ _   _        ______                _   _                 
 |__   __(_) | | |      |  ____|              | | (_)                
    | |   _| |_| | ___  | |__ _   _ _ __   ___| |_ _  ___  _ __  ___ 
    | |  | | __| |/ _ \ |  __| | | | '_ \ / __| __| |/ _ \| '_ \/ __|
    | |  | | |_| |  __/ | |  | |_| | | | | (__| |_| | (_) | | | \__ \
    |_|  |_|\__|_|\___| |_|   \__,_|_| |_|\___|\__|_|\___/|_| |_|___/
                                                                     
    People are later on :D

 */

// Scrape current IMDB page for relevant data
$imdbType = ($type === 'person') ? 'name' : $type;
$url = "https://www.imdb.com/{$imdbType}/{$id}";
$data = file_get_contents($url);
if ($data === false) {
    echo "<h1>An Error Occured:</h1><strong>Failed to create page {$pagePath}. Please contact a site administrator.</strong>";
    echo "<br><br>Redirecting to the homepage in 15 seconds.";
    header("Refresh: 15; URL=../index.php?error=Failed%20to%20create%20page%20" . urlencode($pageID) . ".");}

// Find the cover image URL by using cover-image.php?q=ID json cover_image:

$image_url = '../resources/img/load.gif'; // Default fallback image



// Find the blurb
if (preg_match('/<span[^>]*data-testid="plot-xl"[^>]*>(.*?)<\/span>/is', $data, $blurbMatch)) {
    $blurb = trim(strip_tags($blurbMatch[1]));
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
            $warningsArr[] = 'The synopsis for this page is a stub. Help improve this page by adding more details!';
        }
        }
    } elseif (preg_match('/<span[^>]*class="sc-16ede01-2[^"]*"[^>]*>(.*?)<\/span>/is', $data, $plotMatch)) {
        $plot = trim(strip_tags($plotMatch[1]));
        // Add stub message if fallback is used
        if (!empty($sqlOutput)) {
            $warningsArr[] = 'The synopsis for this page is a stub. Help improve this page by adding more details!';
        }
    }

// Runtime
$runtime = 'We don\'t have a runtime for this title yet. Why not add it?';
if ($type === 'title') {
    $rstmt = $db->prepare("SELECT runtimeMinutes FROM title_basics_trim WHERE tconst = :tconst");
    $rstmt->execute([':tconst' => $id]);
    $row = $rstmt->fetch(PDO::FETCH_ASSOC);
    if ($row && isset($row['runtimeMinutes']) && !empty($row['runtimeMinutes'])) {
        $runtime = htmlspecialchars($row['runtimeMinutes']) . ' minutes';
    } else {
        $warningsArr[] = 'This title does not have a runtime yet. You can help by adding it!';
    }
} else {
    $runtime = 'N/A';
}


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
            $writersArr[] = "<a href=\"../resources/page.php?q={$row['writer']}\">" . htmlspecialchars($nameRow['primaryName']) . "</a>";
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
            $directorArr[] = "<a href=\"../resources/page.php?q={$row['director']}\">" . htmlspecialchars($dnameRow['primaryName']) . "</a>";
        }
    }
    $director = !empty($directorArr) ? implode(', ', $directorArr) : 'N/A';
} else {
    $director = 'N/A';
}

//Genres Logic
$genres = '';
if ($type === 'title') {
    $genresArr = [];
    $gstmt = $db->prepare("SELECT genres FROM title_basics_trim WHERE tconst = :tconst");
    $gstmt->execute([':tconst' => $id]);
    while ($row = $gstmt->fetch(PDO::FETCH_ASSOC)) {
        $genresArr[] = htmlspecialchars($row['genres']);
    }
    $genres = !empty($genresArr) ? implode(', ', $genresArr) : 'N/A';
} else {
    $genres = 'N/A';
}

// Find Stars
$stars = '';
if ($type === 'title') {
    $starsArr = [];
    $sstmt = $db->prepare("SELECT nconst FROM title_principals_trim WHERE tconst = :tconst AND category IN ('actor', 'actress')");
    $sstmt->execute([':tconst' => $id]);
    while ($row = $sstmt->fetch(PDO::FETCH_ASSOC)) {
        $nameStmt = $db->prepare("SELECT primaryName FROM name_basics_trim WHERE nconst = :nconst");
        $nameStmt->execute([':nconst' => $row['nconst']]);
        $nameRow = $nameStmt->fetch(PDO::FETCH_ASSOC);
        if ($nameRow) {
            $starsArr[] = "<a href=\"../resources/page.php?q={$row['nconst']}\">" . htmlspecialchars($nameRow['primaryName']) . "</a>";
        }
    }
    $stars = !empty($starsArr) ? implode(', ', $starsArr) : 'N/A';
} else {
    $stars = 'N/A';
}

// Other Notable People
$notable_people = 'Either all the people who worked on this title are categorized, or we\'re missing someone. Feel free to correct this by editing the page.';
$notable_peopleArr = [];
if ($type === 'title') {
    $nstmt = $db->prepare("SELECT nconst, job FROM title_principals_trim WHERE tconst = :tconst AND category NOT IN ('actor', 'actress', 'writer', 'director')");
    $nstmt->execute([':tconst' => $id]);
    while ($row = $nstmt->fetch(PDO::FETCH_ASSOC)) {
        $nameStmt = $db->prepare("SELECT primaryName FROM name_basics_trim WHERE nconst = :nconst");
        $nameStmt->execute([':nconst' => $row['nconst']]);
        $nameRow = $nameStmt->fetch(PDO::FETCH_ASSOC);
        if ($nameRow) {
            $name = htmlspecialchars($nameRow['primaryName']);
            $job = isset($row['job']) && !empty($row['job']) ? htmlspecialchars($row['job']) : 'Unknown Job (You can add this!)';
            $notable_peopleArr[] = "<a href=\"../resources/page.php?q={$row['nconst']}\">{$name} ({$job})</a>";
        }
    }
    // If no notable people found, add a message
    if (empty($notable_peopleArr)) {
        $notable_peopleArr[] = 'Either all the people who worked on this title are categorized, or we\'re missing someone. Feel free to correct this by editing the page.';
    }
} else {
    $notable_peopleArr[] = 'N/A';
}
$notable_people = !empty($notable_peopleArr) ? implode(', ', $notable_peopleArr) : $notable_people;

// Warnings Array

$warningsArr[] = 'This page is automatically generated based on data from an IMDB database export. Information may be out of date or not accurately reflect reality.';
foreach ($warningsArr as $warning) {
    $warnings .= '<br><span id="page-warning">'. htmlspecialchars($warning) . '</span><br><br>';
}
// Votes Logic
$votes = 0;

/* 

  _____                 _        ______                _   _                 
 |  __ \               | |      |  ____|              | | (_)                
 | |__) |__  ___  _ __ | | ___  | |__ _   _ _ __   ___| |_ _  ___  _ __  ___ 
 |  ___/ _ \/ _ \| '_ \| |/ _ \ |  __| | | | '_ \ / __| __| |/ _ \| '_ \/ __|
 | |  |  __/ (_) | |_) | |  __/ | |  | |_| | | | | (__| |_| | (_) | | | \__ \
 |_|   \___|\___/| .__/|_|\___| |_|   \__,_|_| |_|\___|\__|_|\___/|_| |_|___/
                 | |                                                         
                 |_|                                                         

*/

// Bio logic
if ($type === 'person'){
    if (preg_match('/<span[^>]*data-testid="biography"[^>]*>(.*?)<\/span>/is', $data, $bioMatch)) {
        $bio = trim(strip_tags($bioMatch[1]));
    } else {
        $bio = 'No biography available yet, but you can add one!';
        $warningsArr[] = 'This person page is missing key information. Please help improve it by adding a biography!';
    }
}

//Roles Logic
$roles = '';
if ($type === 'person') {
    $rolesArr = [];
    $rstmt = $db->prepare("SELECT tconst, category FROM title_principals_trim WHERE nconst = :nconst");
    $rstmt->execute([':nconst' => $id]);
    while ($row = $rstmt->fetch(PDO::FETCH_ASSOC)) {
        $titleStmt = $db->prepare("SELECT primaryTitle FROM title_basics_trim WHERE tconst = :tconst");
        $titleStmt->execute([':tconst' => $row['tconst']]);
        $titleRow = $titleStmt->fetch(PDO::FETCH_ASSOC);
        if ($titleRow) {
            $roleName = htmlspecialchars($titleRow['primaryTitle']);
            $roleCategory = htmlspecialchars($row['category']);
            $rolesArr[] = "<a href=\"../resources/page.php?q={$row['tconst']}\">{$roleName} ({$roleCategory})</a>";
        }
    }
    // If no roles found, add a message
    if (empty($rolesArr)) {
        $rolesArr[] = 'No roles found for this person.';
    }
    foreach ($rolesArr as $role) {
        $roles .= '<li>' . $role . '</li>';
    }
} else {
    $roles = 'N/A';
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
                $content = str_replace('{GENRES}', $genres, $content);
                $content = str_replace('{STARS}', $stars, $content);
                $content = str_replace('{RUNTIME}', $runtime, $content);
                $content = str_replace('{BLURB}', $blurb, $content);
                $content = str_replace('{PLOT}', $plot, $content);
                $content = str_replace('{NOTABLE}', $notable_people, $content);
                $content = str_replace('{ID}', $row['id'], $content);
                $content = str_replace('{WARNINGS}', '', $content);
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
                $content = str_replace('{BLURB}', $bio, $content);
                $content = str_replace('{ID}', $row['id'], $content);
                $content = str_replace('{WARNINGS}', '', $content);
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
        header("Refresh: 15; URL=../index.php?error=Failed%20to%20create%20page%20{$row['primary_name']}.");
    }

    ?>
</body>
</html>