<?php

require_once 'connection.php';
require_once './objects/ArrayValue.php';
require_once './objects/Title.php';

/**
 * Create connection to the database
 *
 * @return PDO (PHP Data Objects) provides access to the database
 */
function openConnection(): PDO
{
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

function getTitles($offset, $limit, $title /* Define more parameters for filtering, e.g. rating, date, etc. */ )
{
    // WARNING! This is a slow query because it contains subqueries.
    // It would be better implemented as separate queries specific to any given (filtering, pagination) purpose.
    $query = "SELECT t.tconst as id, titleType as title_type, primaryTitle as primary_title, 
                     originalTitle as original_title, isAdult as is_adult, startYear as start_year, 
                     endYear as end_year, runtimeMinutes as runtime_minutes, t.genres, 
                     r.averageRating as rating, r.numVotes as votes,
                     (
                         SELECT count(*)
                         FROM title_director_trim d
                         WHERE d.tconst = t.tconst
                     ) as directors_count,
                     (
                         SELECT count(*)
                         FROM title_principals_trim p
                         WHERE p.tconst = t.tconst
                     ) as principals_count,
                     (
                         SELECT count(*)
                         FROM title_writer_trim w
                         WHERE w.tconst = t.tconst
                     ) as writers_count
              FROM title_basics_trim t
              JOIN title_ratings_trim r on r.tconst = t.tconst
              WHERE 1 = 1 "; // This allows us to tack on filtering and sorting and limiting clauses later on.

    if (!empty($title)) {
        $query .= "AND (primaryTitle LIKE :title or originalTitle LIKE :title) ";
    }

    $query .= "LIMIT :limit OFFSET :offset";

    try {
        $imdb = openConnection();
        $stmt = $imdb->prepare($query);

        if (!empty($title)) {
            $title = "%" . $title . "%";
            $stmt->bindParam(':title', $title);
        }

        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $objects = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Title::class);
    } catch (PDOException $e) {
        die($e->getMessage());
    }
    return $objects;
}

function getTitleCount($title)
{
    $query = "SELECT count(*) AS title_count
              FROM title_basics_trim AS t
              JOIN title_ratings_trim r on r.tconst = t.tconst
              WHERE 1 = 1 ";

    if (!empty($title)) {
        $query = $query . "AND (primaryTitle LIKE :title or originalTitle LIKE :title) ";
    }

    try {
        $db = openConnection();
        $stmt = $db->prepare($query);

        if (!empty($title)) {
            $title = "%" . $title . "%";
            $stmt->bindParam(':title', $title);
        }

        $stmt->execute();
        $row = $stmt->fetch();

    } catch (PDOException $e) {
        die($e->getMessage());
    }

    return $row["title_count"];
}

function getTitle($id) {
    $query = "SELECT t.tconst as id, titleType as title_type, primaryTitle as primary_title,
                     originalTitle as original_title, isAdult as is_adult, startYear as start_year,
                     endYear as end_year, runtimeMinutes as runtime_minutes, r.averageRating as rating,
                     numVotes as votes,
                     (
                         SELECT count(*)
                         FROM title_director_trim d
                         WHERE d.tconst = t.tconst
                     ) as directors_count,
                     (
                         SELECT count(*)
                         FROM title_principals_trim p
                         WHERE p.tconst = t.tconst
                     ) as principals_count,
                     (
                         SELECT count(*)
                         FROM title_writer_trim w
                         WHERE w.tconst = t.tconst
                     ) as writers_count
              FROM title_basics_trim t
              JOIN title_ratings_trim r on r.tconst = t.tconst
              WHERE t.tconst = :id";

    try {
        $imdb = openConnection();
        $stmt = $imdb->prepare($query);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        $object = $stmt->fetchObject(Title::class);
    } catch (PDOException $e) {
        die($e->getMessage());
    }
    return $object;
}


/** Functions to create tables: genres, title_genre, professions, name_professions, title_known_for */
// TODO: Create tables: title_genre, professions, name_professions, title_known_for

/** Creates genres table */
function createGenres()
{
    $pdo = openConnection();

    // Drop the table if it exists
    $pdo->exec("DROP TABLE IF EXISTS genres;");

    // Create the 'genres' table
    $pdo->exec("
        CREATE TABLE genres (
            genre_id INTEGER PRIMARY KEY AUTOINCREMENT,
            genre_name TEXT UNIQUE
        );
    ");

    // Fetch all genre strings from the source table
    $stmt = $pdo->query("SELECT genres FROM title_basics_trim WHERE genres IS NOT NULL;");
    $allGenres = []; // Holds all the unique genres

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { // While there is a row to fetch from
        $genres = explode(',', $row['genres']);
        $genreCount = count($genres);

        for ($i = 0; $i < $genreCount; $i++) {
            $trimmed = trim($genres[$i]);
            if ($trimmed !== '' && !in_array($trimmed, $allGenres)) {
                $allGenres[] = $trimmed;
            }
        }
    }

    // Insert unique genres into the genres table
    $insert = $pdo->prepare("INSERT OR IGNORE INTO genres (genre_name) VALUES (:genre)");
    foreach ($allGenres as $genre) {
        $insert->execute([':genre' => $genre]);
    }
}

/** Creates title_genre table - Combination table between title_basics and genres */
function createTitleGenre()
{
    $pdo = openConnection();

    // Drop the table if it exists
    $pdo->exec("DROP TABLE IF EXISTS title_genre;");

    // NOTE: If you guys see this before I complete it, the issue is that there are comma separated values in the cells so JOIN doesn't really work
    $pdo->exec("
//        CREATE TABLE title_genre AS
//        SELECT t1.tconst, t2.genre_id
//        FROM title_basics_trim t1
//        JOIN genres t2 ON FIND_IN_SET(t2.genre_name, t1.genres) > 0;
    ");
}

/** Create the tables */
createGenres();
createTitleGenre();