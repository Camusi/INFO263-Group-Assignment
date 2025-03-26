<?php
require_once 'connection.php';

/**
 * Create connection to the database
 *
 * @return PDO (PHP Data Objects) provides access to the database
 */
function openConnection()
{
    require 'connection.php';

    try {
        $pdo = new PDO(
            CONNECTION_STRING,
            CONNECTION_USER,
            CONNECTION_PASSWORD,
            CONNECTION_OPTIONS
        );
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }

    return $pdo;
}

function getTitleData() {
    $pdo = openConnection();
    $query = $pdo->prepare("SELECT * FROM title_basics_trim LIMIT 5");
    $query->execute();
    // Fetch all results as an associative array
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getGenreData() {
    $pdo = openConnection();
    $query = $pdo->prepare("SELECT * FROM genres LIMIT 5");
    $query->execute();
    // Fetch all results as an associative array
    return $query->fetchAll(PDO::FETCH_ASSOC);
}



// TODO: some records have genres in the form 'Drama,Family,Fantasy' but we just want singular genres
function createGenres() {
    $pdo = openConnection();

    // Create the 'genres' table to store genre names
    $query = $pdo->prepare("DROP TABLE IF EXISTS genres;");
    $query->execute();

    $query = $pdo->prepare("
        CREATE TABLE genres (
        genre_id INTEGER PRIMARY KEY AUTOINCREMENT,
        genre_name TEXT UNIQUE
        );
    ");
    $query->execute();

    // Create the 'title_genre' table to link genres with titles
    $query = $pdo->prepare("
        INSERT INTO genres (genre_name)
        SELECT DISTINCT genres FROM title_basics_trim;
    ");
    $query->execute();

    return "Genres table created successfully.";
}



// Example usage
$data = getTitleData();

// Now, echo the data
foreach ($data as $row) {
    echo "Tconst: " . $row['tconst'] . "<br>";
    echo "Primary Title: " . $row['primaryTitle'] . "<br>";
}

echo(createGenres());


$data = getGenreData();
// Now, echo the data
foreach ($data as $row) {
    echo "Id: " . $row['genre_id'] . "<br>";
    echo "Name: " . $row['genre_name'] . "<br>";
}
