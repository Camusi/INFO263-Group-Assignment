<?php

include_once 'database.php';

/*
 * This API is indispensable for React implementation, but it is also
 * very useful for Ajax/JavaScript functionality and pagination.
 */

// Clear the default headers.
header_remove();

// CORS - NOTE: Change this link to the React page to avoid CORS warnings
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: 'Origin, X-Requested-With, Content-Type, Accept'");
header("Access-Control-Allow-Methods: 'GET, POST'");

// Set the header to make sure cache is forced.
header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
// Tell the receiving browser to treat this as JSON.
header('Content-Type: application/json');

// This is for pagination.
if (isset($_GET['offset']) and !empty($_GET['offset'])) {
    $offset = $_GET["offset"];
} else {
    $offset = 0;
}

if (isset($_GET['limit']) and !empty($_GET['limit'])) {
    $limit = $_GET["limit"];
} else {
    $limit = 12;
}

// Filtering by title.
if (isset($_GET['title']) and !empty($_GET['title'])) {
    $title_str = $_GET["title"];
} else {
    $title_str = "";
}

// Retrieving something by ID (title, name, etc.).
if (isset($_GET['id']) and !empty($_GET['id'])) {
    $id = $_GET["id"];
}

// Specify the type of data requested.
if (isset($_GET['q']) and !empty($_GET['q'])) {
    $q = $_GET['q'];

    if ($q == "titles") {
        $titles = getTitles($offset, $limit, $title_str);
        echo json_encode(new ArrayValue($titles), JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);
    }
    if ($q == "title_count") {
        $title_count = getTitleCount($title_str);
        echo json_encode(new ArrayValue(['title_count' => $title_count]), JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);
    }
    if ($q == "title") {
        $title = getTitle($id);
        echo json_encode($title, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);
    }
}

?>