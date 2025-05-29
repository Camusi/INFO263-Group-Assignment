<?php
session_start();
$pageID = $_POST['pageID'];
$type = htmlspecialchars($_POST['type'] ?? '');
$pageURL = '../' . $type . '/' . $pageID . '.php';
$username = $_SESSION['userID'];

// Initialize $updatedContent with the existing file content
$updatedContent = file_exists($pageURL) ? file_get_contents($pageURL) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? null;

    if ($type === 'title') {
        $updatedContent = preg_replace('/<span\s+id="movie-title"\s*>.*?<\/span>/is', '<span id="movie-title">' . htmlspecialchars($_POST['title']) . '</span>', $updatedContent);
        $updatedContent = preg_replace('/<span\s+id="movie-year"\s*>.*?<\/span>/is', '<span id="movie-year">' . htmlspecialchars($_POST['year']) . '</span>', $updatedContent);
        $updatedContent = preg_replace('/<span\s+id="movie-runtime"\s*>.*?<\/span>/is', '<span id="movie-runtime">' . htmlspecialchars($_POST['runtime']) . '</span>', $updatedContent);
        $updatedContent = preg_replace('/<p\s+id="blurb-text"\s*>.*?<\/p>/is', '<p id="blurb-text">' . htmlspecialchars($_POST['blurb']) . '</p>', $updatedContent);
        $updatedContent = preg_replace('/<p\s+id="plot-text"\s*>.*?<\/p>/is', '<p id="plot-text">' . htmlspecialchars($_POST['plot']) . '</p>', $updatedContent);
        $updatedContent = preg_replace('/<span\s+id="director"\s*>.*?<\/span>/is', '<span id="director">' . htmlspecialchars($_POST['director']) . '</span>', $updatedContent);
        $updatedContent = preg_replace('/<span\s+id="writers"\s*>.*?<\/span>/is', '<span id="writers">' . htmlspecialchars($_POST['writers']) . '</span>', $updatedContent);
        $updatedContent = preg_replace('/<span\s+id="stars"\s*>.*?<\/span>/is', '<span id="stars">' . htmlspecialchars($_POST['stars']) . '</span>', $updatedContent);
        $updatedContent = preg_replace('/<span\s+id="notable"\s*>.*?<\/span>/is', '<span id="notable">' . htmlspecialchars($_POST['notable']) . '</span>', $updatedContent);
    } elseif ($type === 'person') {
        $updatedContent = preg_replace('/<span\s+id="person-name"\s*>.*?<\/span>/is', '<span id="person-name">' . htmlspecialchars($_POST['title']) . '</span>', $updatedContent);
        $updatedContent = preg_replace('/<span\s+id="person-year"\s*>.*?<\/span>/is', '<span id="person-year">' . htmlspecialchars($_POST['year']) . '</span>', $updatedContent);
        $updatedContent = preg_replace('/<ul\s+class="roles-list"\s*>.*?<\/ul>/is', '<ul class="roles-list">' . htmlspecialchars($_POST['roles']) . '</ul>', $updatedContent);
    }
   
    $pageID = $_POST['id'];
    $pageURL = '../' . $type . '/' . $pageID . '.php';

    // Append sources/notes as an HTML comment
    $updatedContent .= "\n<!-- This page was recently edited by" . htmlspecialchars($username) . ": " . htmlspecialchars($_POST['sources']) . " -->";
    $updatedContent .= "id =\"" . htmlspecialchars($pageID) . "\"<br>";
    $updatedContent .= "<br>url: ". htmlspecialchars($pageURL);
    $updatedContent .= "<br>user: " . htmlspecialchars($username) . "<br>";

    // Write the updated content back to the file
    if (file_put_contents($pageURL, $updatedContent) === false) {
        // redirect to the edit page with an error message
        header("Location: ../edit.php?type=$type&id=$pageID&error=1");
        exit;
  } else {
        // Redirect to the updated page
        header("Location: /INFO263-Group-Assignment/2025-group1/imdb2/$type/$pageID.php");
        exit;
    }
}
?>